<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台首页控制器
// +----------------------------------------------------------------------
//

class index_control extends commonbase_control{
	public $_var = array();	// 首页参数
		
	public function index() {
		// hook index_control_index_before.php
		
		$this->_cfg['titles'] = empty($this->_cfg['seo_title']) ? $this->_cfg['webname']:$this->_cfg['webname'].'_'.$this->_cfg['seo_title'];
		$this->_var['topcid'] = -1;	//不能为0,0会与外部链接的冲突
		
		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);

		$GLOBALS['run'] = &$this;

		// hook index_control_index_after.php
		
		$this->display('index.htm');
	}

	// hook index_control_after.php
}
