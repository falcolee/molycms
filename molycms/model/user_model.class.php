<?php
// +----------------------------------------------------------------------
// | MOLYCMS	用户模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class user extends model {
	function __construct() {
		$this->table = 'user';		// 表名
		$this->pri = array('uid');	// 主键
		$this->maxid = 'uid';		// 自增字段
	}

	// 根据用户名获取用户数据
	public function get_user_by_username($username) {
		$data = $this->find_fetch(array('username'=>$username), array(), 0, 1);
		return $data ? array_pop($data) : array();
	}
	
	//根据uid获取用户信息，包括扩展信息
	public function get_user_by_uid( $uid = 0 ){
		if( $uid == 0 ) $uid = session::get('_uid');
		$data = $this->get($uid);
		if( $data ){
			$data_info = $this->user_data->get($uid);
			if( is_array($data_info) ){
				$data = array_merge($data_info,$data);
			}
		}
		return $data ? $data : array();
	}
	
	//用户关联删除
	public function xdelete($uid = 0){
		if( $uid == 0 ) return 'UID参数错误！';
		
		$where['uid'] = $uid;
		
		//所有收藏数据
		$this->user_collect->find_delete($where);
			
		//所有投稿数据
		$this->cms_content->table = 'cms_audit_article';
		$this->cms_content->find_delete($where);
			
		//用户所有内容
		
		//用户详细数据
		$this->user_data->delete($uid);
		
		//用户基础数据
		$this->delete($uid);
		
		return '';
	}

	// 检查用户名是否合格
	public function check_username(&$username) {
		$username = trim($username);
		if(empty($username)) {
			return '用户名不能为空哦！';
		}elseif(utf8::strlen($username) > 16) {
			return '用户名不能大于16位哦！';
		}elseif(str_replace(array("\t","\r","\n",' ','　',',','，','-','"',"'",'\\','/','&','#','*'), '', $username) != $username) {
			return '用户名中含有非法字符！';
		}elseif(htmlspecialchars($username) != $username) {
			return '用户名中不能含有<>！';
		}elseif(utf8::strlen($username) < 2) {
			return '用户名不能小于2位哦！';
		}

		// hook usre_model_check_username_after.php
		return '';
	}

	// 返回安全的用户名
	public function safe_username(&$username) {
		$username = str_replace(array("\t","\r","\n",' ','　',',','，','-','"',"'",'\\','/','&','#','*'), '', $username);
		$username = htmlspecialchars($username);
	}

	// 检查密码是否合格
	public function check_password(&$password) {
		if(empty($password)) {
			return '密码不能为空哦！';
		}elseif(utf8::strlen($password) < 6) {
			return '密码不能小于6位哦！';
		}elseif(utf8::strlen($password) > 32) {
			return '密码不能大于32位哦！';
		}
		return '';
	}

	// 验证密码是否相等
	public function verify_password($password, $salt, $password_md5) {
		return get_password($password,$salt) == $password_md5;
	}

	// 防IP暴力破解
	public function anti_ip_brute($ip) {
		$password_error = $this->runtime->get('password_error_'.$ip);
		return ($password_error && $password_error >= 8) ? true : false;
	}

	// 根据IP记录密码错误次数
	public function password_error($ip) {
		$password_error = (int)$this->runtime->get('password_error_'.$ip);
		$password_error++;
		$this->runtime->set('password_error_'.$ip, $password_error, 450);
	}

	// 格式化后显示给用户
	public function format(&$user) {
		if(!$user) return;
		$user['regdate'] = empty($user['regdate']) ? '0000-00-00 00:00' : date('Y-m-d H:i', $user['regdate']);
		$user['regip'] = long2ip($user['regip']);
		$user['logindate'] = empty($user['logindate']) ? '0000-00-00 00:00' : date('Y-m-d H:i', $user['logindate']);
		$user['loginip'] = long2ip($user['loginip']);
		$user['lastdate'] = empty($user['lastdate']) ? '0000-00-00 00:00' : date('Y-m-d H:i', $user['lastdate']);
		$user['lastip'] = long2ip($user['lastip']);
		$user['avatar'] = ( isset($user['avatar']) && empty($user['avatar']) ) ? '../static/img/avatar.png' : '../'.$user['avatar'];
		// hook usre_model_format_after.php
	}
}
