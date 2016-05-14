<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台内容页控制器
// +----------------------------------------------------------------------
//

class show_control extends commonbase_control{
	public $_var = array();	// 内容页参数

	public function index() {
		// hook show_control_index_before.php

		$_GET['cid'] = (int)R('cid');
		$_GET['id'] = (int)R('id');
		$this->_var = $this->category->get_cache($_GET['cid']);
		empty($this->_var) && core::error404();

		// 初始模型表名
		$this->cms_content->table = 'cms_'.$this->_var['table'];
		

		// 读取内容
		$_show = $this->cms_content->read($_GET['id']);
		if( empty($_show['cid']) || $_show['cid'] != $_GET['cid'] ) core::error404();

		// SEO 相关
		$this->_cfg['titles'] = empty($_show['seo_title']) ? $_show['title'].'_'.$this->_var['name'].'_'.$this->_cfg['webname']:$_show['seo_title'];
		$this->_cfg['seo_keywords'] = empty($_show['seo_keywords']) ? $_show['title'].','.$this->_var['name'] : $_show['seo_keywords'];
		$this->_cfg['seo_description'] = $_show['intro'];

		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);

		$GLOBALS['run'] = &$this;
		$GLOBALS['_show'] = &$_show;

		// hook show_control_index_after.php
		
		$this->display($this->_var['show_tpl']);
	}

	// hook show_control_after.php
}
