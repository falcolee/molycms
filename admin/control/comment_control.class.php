<?php
// +----------------------------------------------------------------------
// | MOLYCMS	评论管理
// +----------------------------------------------------------------------
//

class comment_control extends admin_control {
	// 评论管理
	public function index() {
		// hook admin_comment_control_index_before.php

		$mid = max(2, (int)R('mid'));
		$table = $this->models->get_table($mid);

		// 模型名称
		$mod_name = $this->models->get_name();
		if(isset($mod_name[1])) unset($mod_name[1]);
		$this->assign('mid', $mid);
		$this->assign('mod_name', $mod_name);

		$where['mid'] = $mid;
		
		// 初始分页
		$pagenum = 20;
		$total = $this->cms_comment->find_count($where);
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=comment-index-mid-'.$mid.'-page-{page}');
		$this->assign('pages', $pages);
		$this->assign('total', $total);

		// 获取评论列表
		$id_arr = array();
		$comment_arr = $this->cms_comment->list_arr($where, -1, ($page-1)*$pagenum, $pagenum, $total);
		foreach($comment_arr as &$v) {
			$this->cms_comment->format($v, 'Y-m-d H:i:s', 0);
			$id_arr[] = $v['id'];
		}

		$id_arr = array_unique($id_arr);
		$this->cms_content->table = 'cms_'.$table;
		$key_pre = 'cms_'.$table.'-id-';
		$tmp = $this->cms_content->mget($id_arr);
		foreach($comment_arr as &$v) {
			$row = $tmp[$key_pre.$v['id']];
			$v['title'] = $row['title'];
			$v['url'] = $this->cms_content->content_url($row['cid'], $row['id'], $row['alias'], $row['dateline']);
		}
		$this->assign('comment_arr', $comment_arr);

		// hook admin_comment_control_index_after.php

		$this->display();
	}

	// 单条内容评论管理
	public function content() {
		// hook admin_comment_control_content_before.php

		$id = (int) R('id');
		$mid = max(2, (int)R('mid'));
		$table = $this->models->get_table($mid);

		// 模型名称
		$mod_name = $this->models->get_name();
		if(isset($mod_name[1])) unset($mod_name[1]);
		$this->assign('mid', $mid);
		$this->assign('mod_name', $mod_name);

		// 读取内容
		$this->cms_content->table = 'cms_'.$table;
		$row = $this->cms_content->read($id);

		// 初始化标题、位置
		$this->_pkey = 'content';

		// 初始分页
		$pagenum = 20;
		$total = $row['comments'];
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=comment-content-mid-'.$mid.'-id-'.$id.'-page-{page}');
		$this->assign('pages', $pages);
		$this->assign('total', $total);

		// 获取评论列表
		$this->cms_comment->table = 'cms_'.$table.'_comment';
		$comment_arr = $this->cms_content_comment->list_arr(array('id' => $id,'mid'=>$mid), -1, ($page-1)*$pagenum, $pagenum, $total);
		foreach($comment_arr as &$v) {
			$this->cms_content_comment->format($v, 'Y-m-d H:i:s', 0);
			$v['title'] = $row['title'];
			$v['url'] = $this->cms_content->content_url($row['cid'], $row['id'], $row['alias'], $row['dateline']);
		}
		$this->assign('comment_arr', $comment_arr);

		// hook admin_comment_control_content_after.php

		$this->display('comment_index.htm');
	}

	// 读取一条评论
	public function get_json() {
		// hook admin_comment_control_get_json_before.php
		
		$commentid = (int) R('commentid', 'P');
		
		$data = $this->cms_comment->read($commentid);

		// hook admin_comment_control_get_json_after.php

		echo json_encode($data);
		exit;
	}

	// 编辑评论
	public function edit() {
		// hook admin_comment_control_edit_before.php
		
		$commentid = (int) R('commentid', 'P');
		$author = htmlspecialchars(trim(R('author', 'P')));
		$content = htmlspecialchars(trim(R('content', 'P')));

		empty($commentid) && $this->message(0, '评论ID不能为空！');
		empty($author) && $this->message(0, '评论ID不能为空！');
		strlen($author)>20 && $this->message(0, '昵称太长了！');
		empty($content) && $this->message(0, '评论内容不能为空！');
		strlen($content)>3000 && $this->message(0, '评论内容太长了！');
		
		$data = $this->cms_comment->read($commentid);

		$data['author'] = $author;
		$data['content'] = $content;

		// hook admin_comment_control_edit_after.php

		if($this->cms_comment->update($data)) {
			$this->message(1, '编辑成功','index.php?u=comment-index');
		}else{
			$this->message(0, '编辑失败');
		}
		
	}

	// 删除评论
	public function del() {
		// hook admin_comment_control_del_before.php

		$mid = max(2, (int)R('mid', 'P'));
		$table = $this->models->get_table($mid);

		$id = (int) R('id', 'P');
		$commentid = (int) R('commentid', 'P');

		empty($id) && E(1, '内容ID不能为空！');
		empty($commentid) && E(1, '评论ID不能为空！');

		// hook admin_comment_control_del_after.php

		$err = $this->cms_comment->xdelete($table, $id, $commentid);
		if($err) {
			$this->message(0, $err);
		}else{
			$this->message(1, '删除成功！','index.php?u=comment-index');
		}
	}

	// 批量删除评论
	public function batch_del() {
		// hook admin_comment_control_batch_del_before.php

		$mid = max(2, (int)R('mid', 'P'));
		$table = $this->models->get_table($mid);

		$id_arr = R('id_arr', 'P');

		if(!empty($id_arr) && is_array($id_arr)) {
			$err_num = 0;
			foreach($id_arr as $arr) {
				$id = $arr[0];
				$commentid = $arr[1];
				$err = $this->cms_comment->xdelete($table, $id, $commentid);
				if($err){
					$err_num++;
				}
			}

			if($err_num) {
				E(1, $err_num.' 条评论删除失败！');
			}else{
				E(0, '删除成功！');
			}
		}else{
			E(1, '参数不能为空！');
		}
	}

	// hook admin_comment_control_after.php
}
