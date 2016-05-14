<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台更新内容浏览量控制器
// +----------------------------------------------------------------------
//

class views_control extends commonbase_control{
	
	public function index() {
		$_ENV['_config']['cache']['l2_cache'] = 0;
		
		$id = (int)R('id');
		$cid = (int)R('cid');
		
		$_var = $this->category->get_cache($cid);
		empty($_var) && core::error404();
		
		$mviews = &$this->models->cms_content_views;
		$mviews->table = 'cms_'.$_var['table'].'_views';
		
		$key = $mviews->arr2key($id);
		$data = $mviews->db->get($key);
		if(!$data) $data = array('id'=>$id, 'cid'=>$cid, 'views'=>0);
		$data['views']++;
		echo 'var views='.$data['views'].';';
		$mviews->db->set($key, $data);
		exit;
		
	}
}
