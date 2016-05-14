<?php
// +----------------------------------------------------------------------
// | MOLYCMS	留言管理
// +----------------------------------------------------------------------
//

class guestbook_control extends admin_control {
	
	//留言列表
	public function index(){
		$model = &$this->guestbook;
		
		$pagenum = 15;
		$urlstr = '';
		$where = array();
		
		$total = $model->count();
		
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=guestbook-index'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
		
		$list = $model->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
		$this->assign('list', $list);
		
		$this->display();
	}
	
	//留言详情
	public function view(){
		$model = &$this->guestbook;
		$id = intval( R('id','G') );
		$data = $model->get($id);
		if( empty($data) )	$this->message(0, '查看留言失败：'.$id.'不存在！');
		
		if( $data['status'] == 0 ){	//未读更新为已读
			$data['status'] = 1;
			$model->update($data);
		}
		
		$this->assign('data', $data);
		$this->display();
	}
	
	//删除
	public function del(){
		$model = &$this->guestbook;
		$id = intval( R('id','P') );
	
		$status = $model->delete($id);
		if( $status ){
			$this->message(1, '删除留言成功,留言ID:'.$id,'index.php?u=guestbook-index');
		}else{
			$this->message(0, '删除留言失败,留言ID:'.$id);
		}
	}
	
	//删除一个月前的留言
	public function delbatch(){
		$model = &$this->guestbook;
		$where['dateline'] = array('<='=>time() - (86400 * 30));
		$model->find_delete($where);
		$this->message(1, '删除一个月前的留言成功！');
	}
}
