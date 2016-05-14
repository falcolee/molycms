<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台控制器基类
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit('Access Denied');

class commonbase_control extends control{
	public $_cfg = array();	// 全站参数
	public $_user = array();	// 用户
	public $_uid = 0;			//用户ID
	public $_group = array();	// 用户组
	public $_var = array();
	
	function __construct() {
		$this->_cfg = $this->runtime->xget();
		
		//用户相关
		$this->_uid = session::get('_uid');
		if($this->_uid){
			$user = &$this->user;
			$user_group = &$this->user_group;
			$this->_user = $user->get($this->_uid);
			$this->_group = $user_group->get($this->_user['groupid']);
		}
		
		$this->_var['topcid'] = 0;
		$this->assign('moly_var', $this->_var);
		
		$_ENV['_theme'] = &$this->_cfg['theme'];
		
		// hook commonbase_control_construct_after.php
	}
	
	
}
// hook commonbase_control_after.php