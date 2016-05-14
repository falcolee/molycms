<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台分类控制器
// +----------------------------------------------------------------------
//

class cate_control extends commonbase_control{
	public $_var = array();	// 分类页参数
	
	public function index() {
		// hook cate_control_index_before.php

		$_GET['cid'] = (int)R('cid');
		$this->_var = $this->category->get_cache($_GET['cid']);
		empty($this->_var) && core::error404();
		
		// SEO 相关
		$this->_cfg['titles'] = empty($this->_var['seo_title']) ? $this->_var['name'].'_'.$this->_cfg['webname'] : $this->_var['seo_title'].'_'.$this->_cfg['webname'];
		
		if( intval(R('page','G')) > 1 ){
			$this->_cfg['titles'] .= '_第'.intval(R('page','G')).'页';
		}
		
		!empty($this->_var['seo_keywords']) && $this->_cfg['seo_keywords'] = $this->_var['seo_keywords'];
		!empty($this->_var['seo_description']) && $this->_cfg['seo_description'] =  $this->_var['seo_description'];

		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);

		$GLOBALS['run'] = &$this;

		// hook cate_control_index_after.php
		
		$this->display($this->_var['cate_tpl']);
	}

	// hook cate_control_after.php
}
