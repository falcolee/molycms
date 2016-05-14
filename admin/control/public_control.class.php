<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台登陆管理
// +----------------------------------------------------------------------
//

class public_control extends control {
	public $_uid;
	public $_isadmin;
	
	public function __construct(){
		$this->_uid = session::get('_uid');
		$this->_isadmin = session::get('_isadmin');
		
		$this->assign('C', $_ENV['_config']);
		
		$cfg = $this->runtime->xget('cfg');
		$this->assign('moly', $cfg);
	}
	
	public function login(){
		if( $this->_uid && $this->_isadmin ){
			$this->message(0, '你已经登录！', 'index.php?u=index-index');
		}
		
		if( empty($_POST) ){
			$this->display('login.htm');
		}else{
			$user = &$this->user;
			$username = R('username', 'P');
			$password = R('password', 'P');
			$code = R('code', 'P');
			
			if( empty($username) ){
				exit('{"name":"username", "message":"啊哦，用户名不能为空！"}');
			}elseif( empty($password) ){
				exit('{"name":"password", "message":"啊哦，密码不能为空！"}');
			}elseif( empty($code) ){
				exit('{"name":"code", "message":"啊哦，验证码不能为空！"}');
			}elseif( md5($code) != session::get('verify') ){
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
				session::set('_isadmin', 1);

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
	
	// 后台登出
	public function logout(){
		session::delete('_uid');
		session::delete('_isadmin');
		session::destory();
		exit('<html><body><script>window.location="index.php?u=public-login"</script></body></html>');
	}
	
	/**
	 * 生成图像验证码
	 * @static
	 * @access public
	 * @param string $length  位数
	 * @param string $mode  类型
	 * @param string $type 图像格式
	 * @param string $width  宽度
	 * @param string $height  高度
	 * @return string
	 */
	public function verify($length=4,$type='png', $width=50, $height=25, $verifyName='verify') {
		$randval = random(4,1);
	
		$name = R('name','G');
		if($name == ''){
			$name = $verifyName;
		}
	
		R('width','G') && $width = R('width','G');
		R('height','G') && $height = R('height','G');
	
		session::set($name,md5($randval));
		$width = ($length * 10 + 10) > $width ? $length * 10 + 10 : $width;
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($width, $height);
		} else {
			$im = imagecreate($width, $height);
		}
		$r = Array(225, 255, 255, 223);
		$g = Array(225, 236, 237, 255);
		$b = Array(225, 236, 166, 125);
		$key = mt_rand(0, 3);
	
		$backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);    //背景色（随机）
		$borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
		imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		$stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
		// 干扰
		for ($i = 0; $i < 10; $i++) {
			imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $stringColor);
		}
		for ($i = 0; $i < 25; $i++) {
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $stringColor);
		}
		for ($i = 0; $i < $length; $i++) {
			imagestring($im, 5, $i * 10 + 5, mt_rand(1, 8), $randval{$i}, $stringColor);
		}
		header("Content-type: image/" . $type);
		$ImageFun = 'image' . $type;
		$ImageFun($im);
		imagedestroy($im);
	}
}
