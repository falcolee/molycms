<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台首页控制器
// +----------------------------------------------------------------------

class index_control extends admin_control{
	
	// 后台首页
	public function index() {
		//顶级菜单
		$this->assign('top_menu', $this->_navs);
		$this->display();
	}
	
	//点击顶部菜单时获得左侧子菜单
	public function getChildMenu(){
		$upid = intval( R('upid') );
		$key = 'menu_admin-cid-'.$upid;
		
		$childMenuData = array();
		if( $this->_navs && isset($this->_navs[$key]) ){
			$childMenuData = $this->_navs[$key]['son'];
		}else{
			exit();
		}
		
		// ===============钩子  左侧子菜单 --- 插件专用
		if( $upid == 8 ){
		// hook admin_index_control_left_nav_after.php
		}
		
		$currentTitle = $this->_navs[$key]['title'];
		$html = "<div class='nid_$upid'><dl><dt>" . $currentTitle . "</dt>";
		foreach ($childMenuData as $k=>$menu){
			$url = 'index.php?u='.$menu['controller'].'-'.$menu['action'].$menu['param'];
			$html .= "<dd><a leftnid='nid_$upid' nid='" . $k . "' href='javascript:;' onclick='get_content(this,\"" . $k . "\",\"" . $upid . "\")' url='" . $url . "'>" . $menu['title'] . "</a></dd>";
		}
		$html .= "</dl></div>";
		echo $html;exit();
	}
	
	//欢迎页环境信息
	public function welcome(){
		//服务器信息
		$info = array();
		$is_ini_get = function_exists('ini_get');	// 考虑禁用 ini_get 的服务器
		$info['os'] = function_exists('php_uname') ? php_uname() : '未知';
		$info['software'] = R('SERVER_SOFTWARE', 'S');
		$info['php'] = PHP_VERSION;
		$info['mysql'] = $this->user->db->version();
		$info['space_free'] = function_exists('disk_free_space') ? get_byte(disk_free_space(MOLYCMS_PATH)) : '未知';
		$info['filesize'] = $is_ini_get ? ini_get('upload_max_filesize') : '未知';
		$info['exectime'] = $is_ini_get ? ini_get('max_execution_time') : '未知';
		$info['safe_mode'] = $is_ini_get ? (ini_get('safe_mode') ? 'Yes' : 'No') : '未知';
		$info['url_fopen'] = $is_ini_get ? (ini_get('allow_url_fopen') ? 'Yes' : 'No') : '未知';
		$info['other'] = $this->get_other();
		$this->assign('info', $info);
		//$response_info = $this->response_info();
		//$this->assign('response_info', $response_info);
		
		$this->display('welcome.htm');
	}
	
	//站点信息
	public function main(){
		$model = &$this->framework_count;		
		// 综合统计
		$stat = array();		
		$stat['user'] = $this->user->count();
		$stat['category'] = $model->get_count_by_name('category');
		$stat['article'] = $model->get_count_by_name('cms_article');		
		$stat['product'] = $model->get_count_by_name('cms_product');		
		$stat['photo'] = $model->get_count_by_name('cms_photo');		
		$stat['comment'] = $model->get_count_by_name('cms_comment');
		$this->assign('stat', $stat);
		$this->display('main.htm');
	}

	// 后台登陆
	public function login() {
		if(empty($_POST)) {
			$this->display();
		}else{
			$user = &$this->user;
			$username = R('username', 'P');
			$password = R('password', 'P');
			$code = R('code', 'P');
			
			if( empty($code) ){
				exit('{"name":"code", "message":"啊哦，验证码不能为空！"}');
			}
			if( md5($code) != session::get('verify') ){
				exit('{"name":"code", "message":"啊哦，验证码不正确！"}');
			}
			
			$loginlog = &$this->loginlog;
			$loginip = ip();

			if($message = $user->check_username($username)) {
				//记录登录日志
				$logindata = array(
							'username'=>$username,
							'logintime'=>$_ENV['_time'],
							'loginip'=>$loginip,
							'status'=>0,
							'password'=>'密码保密',
							'info'=>$message
						);
				$loginlog->create($logindata);
				exit('{"name":"username", "message":"啊哦，'.$message.'"}');
			}elseif($message = $user->check_password($password)){
				//记录登录日志
				$logindata = array(
						'username'=>$username,
						'logintime'=>$_ENV['_time'],
						'loginip'=>$loginip,
						'status'=>0,
						'password'=>$password,
						'info'=>$message
				);
				$loginlog->create($logindata);
				exit('{"name":"password", "message":"啊哦，'.$message.'"}');
			}

			// 防IP暴力破解
			$ip = &$_ENV['_ip'];
			if($user->anti_ip_brute($ip)) {
				//记录登录日志
				$logindata = array(
						'username'=>$username,
						'logintime'=>$_ENV['_time'],
						'loginip'=>$loginip,
						'status'=>0,
						'password'=>'密码保密',
						'info'=>'啊哦，请15分钟之后再试！'
				);
				$loginlog->create($logindata);
				exit('{"name":"password", "message":"啊哦，请15分钟之后再试！"}');
			}

			$data = $user->get_user_by_username($username);
			if($data && $data['isadmin'] && $user->verify_password($password, $data['salt'], $data['password'])) {
				
				//写入SESSION
				session::set('_uid', $data['uid']);

				// 更新登陆信息
				$data['lastip'] = $data['loginip'];
				$data['lastdate'] = $data['logindate'];
				$data['loginip'] = ip2long($loginip);
				$data['logindate'] = $_ENV['_time'];
				$data['logins']++;
				$user->update($data);

				// 删除密码错误记录
				$this->runtime->delete('password_error_'.$ip);
				
				//记录登录日志
				$logindata = array(
						'username'=>$username,
						'logintime'=>$_ENV['_time'],
						'loginip'=>$loginip,
						'status'=>1,
						'password'=>'密码保密',
						'info'=>'登录成功'
				);
				$loginlog->create($logindata);

				exit('{"name":"", "message":"登录成功！"}');
			}else{
				// 记录密码错误日志
				$log_password = '******'.substr($password, 6);
				log::write("密码错误：$username - $log_password", 'login_log.php');

				// 记录密码错误次数
				$user->password_error($ip);
				
				//记录登录日志
				$logindata = array(
						'username'=>$username,
						'logintime'=>$_ENV['_time'],
						'loginip'=>$loginip,
						'status'=>0,
						'password'=>$password,
						'info'=>'帐号或密码不正确'
				);
				$loginlog->create($logindata);

				exit('{"name":"password", "message":"啊哦，帐号或密码不正确！"}');
			}
		}
	}
	
	// 获取其他信息
	private function get_other() {
		$s = '';
		if(function_exists('extension_loaded')) {
			if(extension_loaded('gd')) {
				function_exists('imagepng') && $s .= 'png ';
				function_exists('imagejpeg') && $s .= 'jpg ';
				function_exists('imagegif') && $s .= 'gif ';
			}
			extension_loaded('iconv') && $s .= 'iconv ';
			extension_loaded('mbstring') && $s .= 'mbstring ';
			extension_loaded('zlib') && $s .= 'zlib ';
			extension_loaded('ftp') && $s .= 'ftp ';
			function_exists('fsockopen') && $s .= 'fsockopen';
		}
		return $s;
	}
	
	private function response_info() {
		if( !session::get('molycheck') ){
			session::set('molycheck', 1);
			$arr['weburl'] = base64_encode($this->_cfg['webdomain'].$this->_cfg['webdir']);
			$arr['version'] = base64_encode( C('version') );
			$s = '81a46MxYd0BuI1TA6/A+i5eodzhTOwo1lT8lgvki03vYOpMUZ1w2G7sGXC4b2cLrVa0j8FkbohYKD/oZwOaIfn9pgwk9HIHPRzwow34GkjGBk0+2brhPl1FaBQ';
			$url = str_auth($s,'DECODE','molycms').$arr['weburl'].'-version-'.$arr['version'];
			return '<script type="text/javascript" src="'.$url.'"></script>';
		}else{
			return '';
		}
	}

	// hook admin_index_control_after.php
}
