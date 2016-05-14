<?php
// +----------------------------------------------------------------------
// | MOLYCMS	内容模型
// +----------------------------------------------------------------------
//
defined('MOLYCMS_PATH') or exit;

class cms_content extends model {
	function __construct() {
		$this->table = '';			// 表名 (可以是 cms_article、cms_product、cms_photo 等)
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}

	// 暂时用些方法解决获取 cfg 值
	function __get($var) {
		if($var == 'cfg') {
			return $this->cfg = $this->runtime->xget();
		}else{
			return parent::__get($var);
		}
	}

	// 格式化内容数组
	public function format(&$v, $mid, $dateformat = 'Y-m-d H:i:s', $titlenum = 0, $intronum = 0) {
		// hook cms_content_model_format_before.php

		if(empty($v)) return FALSE;

		$v['date'] = date($dateformat, $v['dateline']);
		$v['subject'] = $titlenum ? utf8::cutstr_cn($v['title'], $titlenum) : $v['title'];
		$v['url'] = $this->content_url($v['cid'], $v['id'], $v['alias'], $v['dateline']);
		$v['tags'] = _json_decode($v['tags']);
		if($v['tags']) {
			$v['tag_arr'] = array();
			
			foreach($v['tags'] as $tagid=>$name) {
				$v['tag_arr'][] = array('name'=>$name, 'url'=> $this->tag_url($mid, $tagid));
			}
		}

		$intronum && $v['intro'] = utf8::cutstr_cn($v['intro'], $intronum);
		$v['pic'] = $this->cfg['webdir'].(empty($v['pic']) ? 'static/img/nopic.gif' : $v['pic']);

		// hook cms_content_model_format_after.php
	}

	// 获取内容列表
	public function list_arr($where = array(), $orderby, $orderway=1, $start=0, $limit=0, $total=0) {
		// 优化大数据量翻页
		if($start > 1000 && $total > 2000 && $start > $total/2) {
			$orderway = -$orderway;
			$newstart = $total-$start-$limit;
			if($newstart < 0) {
				$limit += $newstart;
				$newstart = 0;
			}
			$list_arr = $this->find_fetch($where, array($orderby => $orderway), $newstart, $limit);
			return array_reverse($list_arr, TRUE);
		}else{
			return $this->find_fetch($where, array($orderby => $orderway), $start, $limit);
		}
	}

	// 内容关联删除
	public function xdelete($table, $id=0, $cid = 0) {
		// hook cms_content_model_xdelete_before.php

		$this->table = 'cms_'.$table;
		$this->cms_content_data->table = 'cms_'.$table.'_data';
		
		$this->cms_content_tag->table = 'cms_'.$table.'_tag';
		$this->cms_content_tag_data->table = 'cms_'.$table.'_tag_data';

		// 内容读取
		$data = $this->read($id);
		if(empty($data)) return '内容不存在！';
		
		$cid == 0 && $cid = $data['cid'];
		$catedata = $this->category->read($cid);
		if(empty($catedata)) return '读取分类表出错！';
		
		$mid = $catedata['mid'];

		// 删除评论
		$this->cms_comment->find_delete(array('id'=>$id,'mid'=>$mid));
		
// 		$this->cms_content_comment_sort->table = 'cms_comment_sort';
// 		$this->cms_content_comment_sort->delete($id);

		// 删除附件
		$attach_arr = $this->cms_attach->find_fetch(array('id'=>$id,'mid'=>$mid));
		$updir = MOLYCMS_PATH.'upload/'.$table.'/';
		foreach($attach_arr as $v) {
			$file = $updir.$v['filepath'];
			$thumb = image::thumb_name($file);
			try{
				is_file($file) && unlink($file);
				is_file($thumb) && unlink($thumb);
			}catch(Exception $e) {}
			$this->cms_attach->delete($v['aid']);
		}

		// 更新标签表
		if(!empty($data['tags'])) {
			$tags_arr = _json_decode($data['tags']);
			foreach($tags_arr as $tagid => $name) {
				$this->cms_content_tag_data->delete($tagid, $id);
				$tagdata = $this->cms_content_tag->read($tagid);
				$tagdata['count']--;
				if($tagdata['count'] > 0) $this->cms_content_tag->update($tagdata);
			}
		}

		// 更新分类表
		if($catedata['count'] > 0) {
			$catedata['count']--;
			if(!$this->category->update($catedata)) return '写入内容表出错！';
			$this->category->update_cache($cid);
		}

		// 删除内容
		$this->cms_content_data->delete($id);
		$this->only_alias->delete($data['alias']);
		
		//用户内容数 -1
		if( $data['uid'] ){
			$userdata = $this->user->read($data['uid']);
			if( $userdata['contents'] > 1 ) $userdata['contents']--;
			if(!$this->user->update($userdata)) return '写入用户表出错！';
		}
		
		$ret = $this->delete($id);
		return $ret ? '' : '删除失败！';
	}

	// 标签链接格式化
	public function tag_url(&$mid, &$tagid, $page = FALSE) {
		// hook cms_content_model_tag_url_before.php

		if(empty($_ENV['_config']['molycms_parseurl'])) {
			$s = $page ? '-page-{page}' : '';
			
			//为了去掉文章模型的mid参数
			if($mid > 2){
				$sTag = '-mid-'.$mid;
			}else{
				$sTag = '';
			}
			
			return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].'index.php?tag-index'.$sTag.'-tagid-'.$tagid.$s.$_ENV['_config']['url_suffix'];
		}else{
			//为了去掉文章模型的mid参数
			if($mid > 2){
				$sTag = $mid.'_';
			}else{
				$sTag = '';
			}
			
			return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].$this->cfg['link_tag_pre'].$sTag.$tagid.($page ? '_{page}' : '').$this->cfg['link_tag_end'];
		}
	}

	// 评论链接格式化
	public function comment_url(&$cid, &$id, $page = FALSE) {
		// hook cms_content_model_comment_url_before.php

		if(empty($_ENV['_config']['molycms_parseurl'])) {
			$s = $page ? '-page-{page}' : '';
			return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].'index.php?comment-index-cid-'.$cid.'-id-'.$id.$s.$_ENV['_config']['url_suffix'];
		}else{
			return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].$this->cfg['link_comment_pre'].$cid.'_'.$id.($page ? '_{page}' : '').$this->cfg['link_comment_end'];
		}
	}

	// 首页分页链接格式化
	public function index_url(&$mid) {
		// hook cms_content_model_index_url_before.php

		if(empty($_ENV['_config']['molycms_parseurl'])) {
			return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].'index.php?index-index-mid-'.$mid.'-page-{page}'.$_ENV['_config']['url_suffix'];
		}else{
			return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].'index_'.$mid.'_{page}'.$this->cfg['link_index_end'];
		}
	}

	// 内容链接格式化
	public function content_url(&$cid, &$id, &$alias, &$dateline, $page = FALSE) {
		// hook cms_content_model_content_url_before.php

		if(empty($_ENV['_config']['molycms_parseurl'])) {
			$s = $page ? '-page-{page}' : '';
			return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].'index.php?show-index-cid-'.$cid.'-id-'.$id.$s.$_ENV['_config']['url_suffix'];
		}else{
			// 性能方面，最后一个最差
			switch($this->cfg['link_show_type']) {
				case 1:
					return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].$cid.'/'.$id.($page ? '/{page}' : '').$this->cfg['link_show_end'];
				case 2:
					return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].$this->cfg['cate_arr'][$cid].'/'.$id.($page ? '/{page}' : '').$this->cfg['link_show_end'];
				case 3:
					return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].($alias ? $alias : $cid.'_'.$id.($page ? '_{page}' : '')).$this->cfg['link_show_end'];
				default:
					if( $page ){
						$arr = array(
								'{cid}' => $cid,
								'{id}' => $id,
								'page' => '{page}',
								'{alias}' => $alias ? $alias : $cid.'_'.$id,
								'{cate_alias}' => $this->cfg['cate_arr'][$cid],
								'{y}' => date('Y', $dateline),
								'{m}' => date('m', $dateline),
								'{d}' => date('d', $dateline)
						);
					}else{
						$arr = array(
								'{cid}' => $cid,
								'{id}' => $id,
								'{alias}' => $alias ? $alias : $cid.'_'.$id,
								'{cate_alias}' => $this->cfg['cate_arr'][$cid],
								'{y}' => date('Y', $dateline),
								'{m}' => date('m', $dateline),
								'{d}' => date('d', $dateline)
						);
					}
					return webdomain($this->cfg['webdomain']).$this->cfg['webdir'].strtr($this->cfg['link_show'], $arr);
			}
		}
	}
}
