<?php
// +----------------------------------------------------------------------
// | MOLYCMS	用户管理
// +----------------------------------------------------------------------
//

class user_control extends admin_control {
	public $_group_arr = array();
	
	public function __construct(){
		parent::__construct();
		$user_group = &$this->user_group;
		$this->_group_arr = $user_group->get_groups();
	}
	
	// 用户管理
	public function index() {
		// hook admin_user_control_index_before.php
		
		$urlstr = '';
		
		$user = &$this->user;
		
		$where = array();
		$condition['username'] = '';
		$condition['groupid'] = '*';
		
		$username = empty($_POST) ? R('username') : R('username', 'P');
		$groupid = empty($_POST) ? R('groupid') : R('groupid', 'P');
		if( $username ){
			$condition['username'] = $where['username'] = $username;
			$urlstr .= '-username-'.urlencode($username);
		}
		if( $groupid && $groupid != '*' ){
			$condition['groupid'] = $where['groupid'] = $groupid;
			$urlstr .= '-groupid-'.$groupid;
		}
		
		//所有用户组
		$this->assign('user_group_list', $this->_group_arr);
		
		// 初始分页
		$pagenum = 15;
		
		$total = $user->find_count($where);
		
		$this->assign('condition',$condition);
		
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=user-index'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
		
		$list = $user->find_fetch($where,array('uid'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
		foreach ($list as &$v){
			$user->format($v);
			$v['groupname'] = $this->_group_arr['user_group-groupid-'.$v['groupid']]['groupname'];
			
			switch ($v['status']){
				case 1:
					$v['status'] = '正常';break;
				case 2:
					$v['status'] = '待审核';break;
				case 3:
					$v['status'] = '禁止登录';break;
				case 4:
					$v['status'] = '禁止评论';break;
				default:
					$v['status'] = '未知';
			}
		}
		$this->assign('list', $list);
		
		// hook admin_user_control_index_after.php
		$this->display();
	}
	
	//添加用户
	public function add(){
		if( empty($_POST) ){
			$this->assign('user_group', $this->_group_arr);
			
			$this->display();
		}else{
			$user = &$this->user;
			// hook admin_user_control_add_before.php
			
			$info = R('info','P');
			if( empty($info) ){
				$this->message(0, '添加用户失败，传递参数为空！');
			}elseif ( $info['email'] && !check::is_email($info['email']) ){
				$this->message(0, '添加用户失败，邮箱格式不正确！');
			}else{
				$usermsg = $user->check_username($info['username']);
				if( $usermsg != '' )	$this->message(0, '添加用户失败:'.$usermsg);
				
				$passwordmsg = $user->check_password($info['password']);
				if( $passwordmsg != '' )	$this->message(0, '添加用户失败:'.$passwordmsg);
				
				if( $info['password'] != $info['repassword'] )	$this->message(0, '添加用户失败:两次输入的密码不一致！');
				
				$has = $user->get_user_by_username($info['username']);
				if( $has ) $this->message(0, '添加用户失败，用户名已经存在！');
				
				unset($info['repassword']);
				
				//后台添加用户不需要增加审核机制
				
				$ip = ip(1);
				$time = time();
				$info['salt'] = $salt = random(16, 3, '0123456789abcdefghijklmnopqrstuvwxyz~!@#$%^&*()_+<>,.');
				$info['password'] = get_password($info['password'],$salt);
				$info['regip'] = $ip;
				$info['regdate'] = $time;
				$info['author'] = empty($info['author']) ? $info['username']:$info['author'];
				$info['status'] = 1;
				$info['groupid'] = max(1, (int)$info['groupid']);
				
				$info['isadmin'] = intval($this->_group_arr['user_group-groupid-'.$info['groupid']]['isadmin']);
				
				
				$info_data = R('data_info','P');
				if( $info_data['mobile'] && !check::is_mobile($info_data['mobile']) ){
					$this->message(0, '添加用户失败，手机号格式不正确！');
				}
				
				// hook admin_user_control_add_after.php
				
				$uid = $user->create($info);
				if( $uid ){
					
					$user_data = &$this->user_data;
					
					$info_data['uid'] = $uid;
					$user_data->create($info_data);
					
					$this->message(1, '添加用户成功：UID【'.$uid.'】','index.php?u=user-index');
				}else{
					$this->message(0, '添加用户失败，写入数据库失败！');
				}
			}
			
		}
	}
	
	
	//编辑用户
	public function edit(){
		if( empty($_POST) ){
			$uid = intval( R('uid','G') );
			$user = &$this->user;
			$data = $user->get_user_by_uid($uid);
			
			$this->assign('user_group',$this->_group_arr);
			
			$this->assign('data',$data);
			
			$this->display();
		}else{
			$user = &$this->user;
			// hook admin_user_control_edit_before.php
				
			$info = R('info','P');
			$uid = intval( R('uid','P') );
			if( empty($info) || empty($uid) ){
				$this->message(0, '编辑用户失败，传递参数为空！');
			}elseif ( $info['email'] && !check::is_email($info['email']) ){
				$this->message(0, '编辑用户失败，邮箱格式不正确！');
			}else{
				$usermsg = $user->check_username($info['username']);
				if( $usermsg != '' )	$this->message(0, '编辑用户失败:'.$usermsg);
			
				$has = $user->get_user_by_username($info['username']);
				if( $has && $has['uid'] != $uid ) $this->message(0, '编辑用户失败，用户名已经存在！');
								
				$info['groupid'] = max(1, (int)$info['groupid']);			
				$info['isadmin'] = intval($this->_group_arr['user_group-groupid-'.$info['groupid']]['isadmin']);
				$info['author'] = empty($info['author']) ? $info['username']:$info['author'];
				$info['uid'] = $uid;
			
				$info_data = R('data_info','P');
				if( $info_data['mobile'] && !check::is_mobile($info_data['mobile']) ){
					$this->message(0, '编辑用户失败，手机号格式不正确！');
				}
			
				// hook admin_user_control_edit_after.php
				
				if( $user->update($info) ){
						
					$user_data = &$this->user_data;
						
					$info_data['uid'] = $uid;
					$user_data->update($info_data);
						
					$this->message(1, '编辑用户成功！','index.php?u=user-index');
				}else{
					$this->message(0, '编辑用户失败，写入数据库失败！');
				}
			}
		}
	}
	
	//删除用户
	public function del(){
		$uid = intval( R('uid','P') );
		$user = &$this->user;
		$msg = $user->xdelete($uid);
		if( $msg ){
			$this->message(0, $msg);
		}else{
			$this->message(1, '删除成功！');
		}
	}
	
	//用户详情
	public function view(){
		$uid = intval( R('uid','G') );
		$user = &$this->user;
		$data = $user->get_user_by_uid($uid);
		
		//格式化
		$user->format($data);
		
		//所有用户组
		$group_arr = $this->_group_arr;
		foreach ($group_arr as $v){
			if( $v['groupid'] == $data['groupid'] ){
				$data['groupname'] = $v['groupname'];
				break;
			}
		}
		
		$this->assign('_user', $data);
		$this->display();
	}
	
	//用户配置
	public function setting(){
		if(empty($_POST)) {
			$cfg = $this->kv->xget('user_cfg');
			$input = array();
			$input['open_user_model'] = empty($cfg['open_user_model']) ? 0 : 1;
			$input['open_user_reg'] = empty($cfg['open_user_reg']) ? 0 : 1;
			$input['user_active_method'] = isset($cfg['user_active_method'])?intval($cfg['user_active_method']):0;

			if( empty($cfg['email_active_content']) ){
				$cfg['email_active_content'] = '欢迎您注册成为用户，您的账号需要邮箱认证，点击下面链接进行认证：{click}
或者将网址复制到浏览器：{url}';
			}
			if( empty($cfg['email_pwd_content']) ){
				$cfg['email_pwd_content'] = '密码找回，新的登录密码为：{password}';
			}
			
			$input['email_active_content'] = form::get_textarea('email_active_content', $cfg['email_active_content']);
			$input['email_pwd_content'] = form::get_textarea('email_pwd_content', $cfg['email_pwd_content']);
			
			// hook admin_user_control_setting_after.php
		
			$this->assign('input', $input);
			$this->display();
		}else{
			_trim($_POST);
			$this->kv->xset('open_user_model', R('open_user_model', 'P'), 'user_cfg');
			$this->kv->xset('open_user_reg', R('open_user_reg', 'P'), 'user_cfg');
			$this->kv->xset('user_active_method', R('user_active_method', 'P'), 'user_cfg');
			$this->kv->xset('email_active_content', R('email_active_content', 'P'), 'user_cfg');
			$this->kv->xset('email_pwd_content', R('email_pwd_content', 'P'), 'user_cfg');
		
			// hook admin_user_control_setting_post_after.php
		
			$this->kv->save_changed();
		
			$this->message(1,'修改成功！');
		}
	}
	
	// hook admin_user_control_after.php
}
