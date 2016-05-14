<?php
// +----------------------------------------------------------------------
// | MOLYCMS	模块分类管理
// +----------------------------------------------------------------------
//

class types_control extends admin_control {
	
	//列表
	public function index(){
		$model = &$this->types;
		
		$pagenum = 15;
		$urlstr = '';
		$where = array();
		
		$total = $model->count();
		
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=types-index'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
		
		$list = $model->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
		$this->assign('list', $list);
		
		$this->display();
	}
	
	//添加
	public function add(){
		
		if( empty($_POST) ){
			$data = array(
					'id'=>'',
					'title'=>'',
                            'class'=>'',
                            'remark'=>'',
					'thumb'=>'',
                            'listorder'=>'',
					'status'=>'1'
					);
			$this->assign('data', $data);
			$this->display('types_set.htm');
		}else{
			$info = R('info','P');
			
			//if( $info['url'] && !check::is_url($info['url']) )	$this->message(0, '添加碎片失败：URL格式不正确');
			
			$model = &$this->types;
			$id = $model->create($info);
			if(!$id) {
				$this->message(0, '添加失败：写入表失败！');
			}else{
				$this->message(1, '添加成功,ID:'.$id,'index.php?u=types-index');
			}
		}
	}
	
	//编辑
	public function edit(){
		$model = &$this->types;
		if( empty($_POST) ){
			$id = intval( R('id','G') );
			$data = $model->get($id);
			if( empty($data) )	$this->message(0, '编辑失败：'.$id.'不存在！');
			$this->assign('data', $data);
			$this->display('types_set.htm');
		}else{
			$id = intval( R('id','P') );
			if( empty($id) )	$this->message(0, '编辑失败：'.$id.'不存在！');
			
			$info = R('info','P');
			
			//if( $info['url'] && !check::is_url($info['url']) )	$this->message(0, '编辑碎片失败：URL格式不正确');
			
			$info['id'] = $id;
			
			if(!$model->update($info)) {
				$this->message(0, '编辑失败：更新表失败！');
			}else{
				$this->message(1, '编辑成功！','index.php?u=types-index');
			}
		}
	}
	
	//删除
	public function del(){
		$model = &$this->types;
		$id = intval( R('id','P') );
		
		$status = $model->delete($id);
		if( $status ){
			$this->message(1, '删除成功,ID:'.$id,'index.php?u=types-index');
		}else{
			$this->message(0, '删除失败,ID:'.$id);
		}
	}
}
