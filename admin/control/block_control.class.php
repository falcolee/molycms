<?php
// +----------------------------------------------------------------------
// | MOLYCMS	碎片管理
// +----------------------------------------------------------------------
//

class block_control extends admin_control {
	
	//碎片列表
	public function index(){
		$model = &$this->block;
		
		$pagenum = 15;
		$urlstr = '';
		$where = array();
		
		$total = $model->count();
		
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=block-index'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
		
		$list = $model->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
		$this->assign('list', $list);
		
		$this->display();
	}
	
	//添加碎片
	public function add(){
		// hook admin_block_control_add_before.php
		
		if( empty($_POST) ){
			$data = array(
					'id'=>'',
					'title'=>'',
					'url'=>'',
					'content'=>''
					);
			$this->assign('data', $data);
			$this->display('block_set.htm');
		}else{
			$info = R('info','P');
			
			if( $info['url'] && !check::is_url($info['url']) )	$this->message(0, '添加碎片失败：URL格式不正确');
			
			$model = &$this->block;
			$id = $model->create($info);
			if(!$id) {
				$this->message(0, '添加碎片失败：写入碎片表失败！');
			}else{
				$this->message(1, '添加碎片成功,碎片ID:'.$id,'index.php?u=block-index');
			}
		}
	}
	
	//编辑碎片
	public function edit(){
		$model = &$this->block;
		if( empty($_POST) ){
			$id = intval( R('id','G') );
			$data = $model->get($id);
			if( empty($data) )	$this->message(0, '编辑碎片失败：'.$id.'不存在！');
			$this->assign('data', $data);
			$this->display('block_set.htm');
		}else{
			$id = intval( R('id','P') );
			if( empty($id) )	$this->message(0, '编辑碎片失败：'.$id.'不存在！');
			
			$info = R('info','P');
			
			if( $info['url'] && !check::is_url($info['url']) )	$this->message(0, '编辑碎片失败：URL格式不正确');
			
			$info['id'] = $id;
			
			if(!$model->update($info)) {
				$this->message(0, '编辑碎片失败：更新碎片表失败！');
			}else{
				$this->message(1, '编辑碎片成功！','index.php?u=block-index');
			}
		}
	}
	
	//删除碎片
	public function del(){
		$model = &$this->block;
		$id = intval( R('id','P') );
		
		$status = $model->delete($id);
		if( $status ){
			$this->message(1, '删除碎片成功,碎片ID:'.$id,'index.php?u=block-index');
		}else{
			$this->message(0, '删除碎片失败,碎片ID:'.$id);
		}
	}
}
