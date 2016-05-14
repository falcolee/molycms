<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台TAG控制器
// +----------------------------------------------------------------------
//

class tag_control extends commonbase_control{
	public $_var = array();	// 标签页参数

	public function index() {
		// hook tag_control_index_before.php

		$mid = max(2, (int)R('mid'));
		$table = isset($this->_cfg['table_arr'][$mid]) ? $this->_cfg['table_arr'][$mid] : 'article';

		$tagid = R('tagid');
		empty($tagid) && core::error404();
		
		$this->cms_content_tag->table = 'cms_'.$table.'_tag';
		$tags = $this->cms_content_tag->find_fetch(array('tagid'=>$tagid), array(), 0, 1);
		empty($tags) && core::error404();
		$tags = current($tags);

		$this->_cfg['titles'] = $tags['name'];
		$this->_var['topcid'] = -1;

		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);

		$GLOBALS['run'] = &$this;
		$GLOBALS['tags'] = &$tags;
		$GLOBALS['mid'] = &$mid;
		$GLOBALS['table'] = &$table;

		// hook tag_control_index_after.php
		
		$this->display('tag_list.htm');
	}

	// 热门标签
	public function top() {
		// hook tag_control_top_before.php
		
		$this->_cfg['titles'] = '热门标签';
		$this->_var['topcid'] = -1;

		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);

		$GLOBALS['run'] = &$this;

		// hook tag_control_top_after.php
		
		$this->display('tag_top.htm');
	}
}
