<?php
// +----------------------------------------------------------------------
// | MOLYCMS	标签管理
// +----------------------------------------------------------------------
//

class tag_control extends admin_control {
	// 标签管理
	public function index() {
		// hook admin_tag_control_index_before.php

		$mid = max(2, (int)R('mid','R'));
		$table = $this->models->get_table($mid);
				
		// 模型名称
		$mod_name = $this->models->get_name();
		if(isset($mod_name[1])) unset($mod_name[1]);
		$this->assign('mid', $mid);
		$this->assign('mod_name', $mod_name);
		
		$keyword = empty($_POST) ? R('keyword') : R('keyword', 'P');
		if($keyword) {
			$keyword = urldecode($keyword);
			$keyword = safe_str($keyword);
		}
		$this->assign('keyword', $keyword);

		$this->cms_content_tag->table = 'cms_'.$table.'_tag';

		// 初始分页
		$urlstr = '';
		$pagenum = 10;
		if($keyword) {
			$where = array('name'=>array('LIKE'=>$keyword));
			$total = $this->cms_content_tag->find_count($where);
			$urlstr = '-keyword-'.urlencode($keyword);
		}else{
			$where = array();
			$total = $this->cms_content_tag->count();
		}
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=tag-index-mid-'.$mid.$urlstr.'-page-{page}');
		$this->assign('pages', $pages);
		$this->assign('total', $total);

		// 获取标签列表
		$list_arr = $this->cms_content_tag->list_arr($where,'tagid',-1, ($page-1)*$pagenum, $pagenum, $total);
		foreach($list_arr as &$v) {
			$v['url'] = $this->cms_content->tag_url($mid, $v['tagid']);
		}

		$this->assign('list_arr', $list_arr);

		// hook admin_tag_control_index_after.php

		$this->display();
	}

	// 读取一条标签
	public function get_json() {
		// hook admin_tag_control_get_json_before.php

		$mid = max(2, (int)R('mid', 'P'));
		$table = $this->models->get_table($mid);

		$tagid = (int) R('tagid', 'P');

		$this->cms_content_tag->table = 'cms_'.$table.'_tag';
		$data = $this->cms_content_tag->read($tagid);

		// hook admin_tag_control_get_json_after.php

		echo json_encode($data);
		exit;
	}

	// 添加标签
	public function add() {
		// hook admin_tag_control_add_before.php

		$mid = max(2, (int)R('mid', 'P'));
		$table = $this->models->get_table($mid);
		
		$name = trim(safe_str(R('name', 'P')));
		$content = htmlspecialchars(trim(R('content', 'P')));
		
		empty($name) && $this->message(0, '名称不能为空！！');
		strlen($name)>30 && $this->message(0, '名称太长了！');
		
		$this->cms_content_tag->table = 'cms_'.$table.'_tag';
		$tagdata = $this->cms_content_tag->get_tag_by_name($name);
		if( $tagdata ) {
			$this->message(0, '已有相同名称的标签！');
		}
		
		$data = array('name'=>$name, 'count'=>0, 'content'=>$content);
		
		// hook admin_tag_control_add_after.php
		
		if($this->cms_content_tag->create($data)) {
			$this->message(1, '添加成功！','index.php?u=tag-index-mid-'.$mid);
		}else{
			$this->message(0, '添加失败！');
		}
		
	}

	// 编辑标签
	public function edit() {
		// hook admin_tag_control_edit_before.php

		$mid = max(2, (int)R('mid', 'P'));
		$table = $this->models->get_table($mid);

		$tagid = (int) R('tagid', 'P');
		$name = trim(safe_str(R('name', 'P')));
		$content = htmlspecialchars(trim(R('content', 'P')));

		empty($tagid) && $this->message(0, '标签ID不能为空！');
		empty($name) && $this->message(0, '名称不能为空！！');
		strlen($name)>30 && $this->message(0, '名称太长了！');

		$this->cms_content->table = 'cms_'.$table;
		$this->cms_content_tag->table = 'cms_'.$table.'_tag';
		$this->cms_content_tag_data->table = 'cms_'.$table.'_tag_data';

		$data = $this->cms_content_tag->read($tagid);

		// 修改 cms_content 表的内容
		if($data['name'] != $name) {
			$tagdata = $this->cms_content_tag->get_tag_by_name($name);
			if( $tagdata ) {
				$this->message(0, '已有相同名称的标签！');
			}
			
			$list_arr = $this->cms_content_tag_data->find_fetch(array('tagid'=>$tagid));
			foreach($list_arr as $v) {
				$data2 = $this->cms_content->read($v['id']);
				if(empty($data2)) $this->message(0, '读取内容表出错！');

				$row = _json_decode($data2['tags']);
				$row[$tagid] = $name;
				$data2['tags'] = _json_encode($row);

				if(!$this->cms_content->update($data2)) $this->message(0, '写入内容表出错！');
			}
		}

		// hook admin_tag_control_edit_after.php

		$data['name'] = $name;
		$data['content'] = $content;
		if($this->cms_content_tag->update($data)) {
			$this->message(1, '编辑成功！','index.php?u=tag-index-mid-'.$mid);
		}else{
			$this->message(0, '编辑失败！');
		}
	}

	// 删除标签
	public function del() {
		// hook admin_tag_control_del_before.php

		$mid = max(2, (int)R('mid', 'P'));
		$table = $this->models->get_table($mid);

		$tagid = (int) R('tagid', 'P');

		empty($tagid) && $this->message(0, '标签ID不能为空！');

		// hook admin_tag_control_del_after.php

		$err = $this->cms_content_tag->xdelete($table, $tagid);
		if($err) {
			$this->message(0, $err);
		}else{
			$this->message(1, '删除成功！','index.php?u=tag-index-mid-'.$mid);
		}
	}
	
	// 批量删除标签
	public function batch_del() {
		// hook admin_tag_control_batch_del_before.php
	
		$mid = max(2, (int)R('mid', 'P'));
		$table = $this->models->get_table($mid);
	
		$id_arr = R('id_arr', 'P');
	
		if(!empty($id_arr) && is_array($id_arr)) {
			$err_num = 0;
			foreach($id_arr as $tagid) {
				$err = $this->cms_content_tag->xdelete($table, $tagid);
				if($err) $err_num++;
			}
	
			if($err_num) {
				$this->message(0, $err_num.' 条标签删除失败！');
			}else{
				$this->message(1, '删除成功！','index.php?u=tag-index-mid-'.$mid);
			}
		}else{
			$this->message(0, '请选择要删除的标签！');
		}
	}

	// hook admin_tag_control_after.php
}
