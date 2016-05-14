<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台404控制器
// +----------------------------------------------------------------------
//

class error404_control extends control{
	public $_cfg = array();	// 全站参数
	public $_var = array();	// 404页参数

	public function index() {
		// hook error404_control_index_before.php

		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		
		$this->_cfg = $this->runtime->xget();
		$this->_cfg['titles'] = '404 Not Found';
		$this->_var['topcid'] = -1;

		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);

		$GLOBALS['run'] = &$this;

		// hook error404_control_index_after.php
		
		$_ENV['_theme'] = &$this->_cfg['theme'];
		$this->display('404.htm');
	}
}
