<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台搜索控制器
// +----------------------------------------------------------------------
//

class search_control extends commonbase_control{
	public $_var = array();	// 搜索页参数

	public function index() {
		// hook search_control_index_before.php

		$keyword = urldecode(R('keyword'));
		$keyword = safe_str($keyword);
		
		$this->_cfg['titles'] = $keyword;
		$this->_var['topcid'] = -1;

		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);
		$this->assign('keyword', $keyword);

		$GLOBALS['run'] = &$this;
		$GLOBALS['keyword'] = &$keyword;

		// hook search_control_index_after.php
		
		$this->display('search.htm');
	}
}
