<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台控制器基类
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit('Access Denied');

class admin_control extends control {
	public $_user = array();	// 后台用户
	public $_uid = 0;	//管理员ID
	public $_group = array();	// 当前管理组
	public $_cfg = array();	// 全站参数
	public $_navs = array();	//后台菜单

	function __construct() {
		//$_ENV['_config']['FORM_HASH'] = form_hash();
		$this->assign('C', $_ENV['_config']);
		$this->assign_value('core', F_APP_NAME);
		
		$this->_cfg = $this->kv->xget('cfg');
		$this->assign('_cfg', $this->_cfg);

		$this->_uid = session::get('_uid');
		$_isadmin = session::get('_isadmin');
		
		$err = 1;
		if( $this->_uid && $_isadmin ){
			$err = 0;			
			$user = &$this->user;
			$user_group = &$this->user_group;
			$this->_user = $user->get($this->_uid);	//当前管理员信息
			
			$this->_group = $user_group->find_fetch( array('groupid'=>$this->_user['groupid'],'isadmin'=>1) );
			
			if( empty($this->_user) || $this->_user['isadmin'] == 0 ){
				$err = 1;
			}elseif ( empty($this->_group) ){
				$err = 1;
			}else{
				// 初始化导航数组
				$this->init_navigation();
				
				$gKey = 'user_group-groupid-'.$this->_user['groupid'];
				$this->_group = $this->_group[$gKey];
				
				$this->assign('_user', $this->_user);
				$this->assign('_group', $this->_group);
			}
		}
		
		if($err) {
			if(R('ajax')) {
				$this->message(0, '非法访问，请登陆后再试！', 'index.php?u=public-login');
			}
			exit('<html><body><script>top.location="index.php?u=public-login"</script></body></html>');
		}
		
		// hook admin_admin_control_construct_after.php
	}
	
	// 初始化导航数组
	protected function init_navigation() {		
		if( $navs = session::get('_navs'.$this->_user['groupid']) ){
			$this->_navs = $navs;
		}else{
			$menu = &$this->menu_admin;
			
			$where['upid'] = 0;
			$where['status'] = 1;
			$menus = $menu->find_fetch($where);
			
			foreach ($menus as $k=>$v){
				$where['upid'] = $v['cid'];
				$menus[$k]['son'] = $menu->find_fetch($where);
				
			}
			$this->_navs = $menus;
			session::set('_navs'.$this->_user['groupid'],$this->_navs);
		}
		
		// hook admin_admin_control_init_nav_after.php			
	}

	// 清除缓存
	public function clear_cache() {
		$this->runtime->truncate();

		try{ unlink(RUNTIME_PATH.'_runtime.php'); }catch(Exception $e) {}
		$tpmdir = array('_control', '_model', '_view');
		foreach($tpmdir as $dir) _rmdir(RUNTIME_PATH.APP_NAME.$dir);
		foreach($tpmdir as $dir) _rmdir(RUNTIME_PATH.F_APP_NAME.$dir);
		return TRUE;
	}
	
	//重写父类的信息提示方法
	public function message($status, $message, $jumpurl = '', $delay = 2) {
		//记录后台操作日志
		$optlog = &$this->operationlog;
		$fangs = 'AJAX';
		$request_method = $_SERVER['REQUEST_METHOD'];
		if ($request_method =='GET') {
			$fangs = 'GET';
		} elseif ($request_method =='POST') {
			$fangs = 'POST';
		}
		$optData = array(
				'uid'=>$this->_uid,
				'dateline'=>$_ENV['_time'],
				'ip'=>ip(),
				'status'=>$status,
				'info' => "提示语：{$message}<br/>控制器：" . R('control') . ",方法：" . R('action') . "<br/>请求方式：{$fangs}",
				'`get`'=>empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER']	//get为mysql关键词 
				);
		$optlog->create($optData);
		
		if(R('ajax')) {
			$status = $status == 1 ? 0:1;	//AJAX返回0表示成功，1表示失败
			echo json_encode(array('err'=>$status, 'msg'=>$message, 'jumpurl'=>$jumpurl, 'delay'=>$delay));
		}else{
			if(empty($jumpurl)) {
				$jumpurl = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
			}
			include VIEW_PATH.'sys_message.php';
		}
		exit;
	}

	// hook admin_admin_control_after.php
}
