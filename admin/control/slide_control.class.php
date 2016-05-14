<?php
// +----------------------------------------------------------------------
// | MOLYCMS	幻灯片管理
// +----------------------------------------------------------------------
//

class slide_control extends admin_control {
    private $_class = 'slide';	//模型
    public $_group_arr = array();

    public function __construct(){
        parent::__construct();
        $types_model = &$this->types;
        $this->_group_arr = $types_model->get_names($this->_class);
    }
	public function index(){
		$model = &$this->slide;
		
		$pagenum = 15;
		$urlstr = '';
		$where = array();
		
		$total = $model->count();
		
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=slide-index'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
		
		$list = $model->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
        foreach ($list as &$v) {
            $v['typetitle'] = $this->_group_arr['types-id-'.$v['type']]['title'];
        }
		$this->assign('list', $list);
		
		$this->display();
	}

    //添加
    public function add(){

        if( empty($_POST) ){
            $data = array(
                'id'=>'',
                'type'=>'',
                'title'=>'',
                'url'=>'',
                'remark'=>'',
                'thumb'=>'',
                'listorder'=>'99',
                'status'=>'1'
            );

            $typehtml = $this->types->get_typehtml($this->_class);
            $this->assign('typehtml', $typehtml);
            $this->assign('data', $data);
            $this->display('slide_set.htm');
        }else{
            $info = R('info','P');

            if( $info['url'] && !check::is_url($info['url']) )	$this->message(0, '添加幻灯失败：URL格式不正确');
            if(!$info['type'])	$this->message(0, '添加幻灯失败：类型未定义');
            $model = &$this->slide;
            $id = $model->create($info);
            if(!$id) {
                $this->message(0, '添加失败：写入表失败！');
            }else{
                $this->message(1, '添加幻灯成功,ID:'.$id,'index.php?u=slide-index');
            }
        }
    }

    //编辑
    public function edit(){
        $model = &$this->slide;
        if( empty($_POST) ){
            $id = intval( R('id','G') );
            $data = $model->get($id);
            if( empty($data) )	$this->message(0, '编辑失败：'.$id.'不存在！');

            $typehtml = $this->types->get_typehtml($this->_class,$data['type']);
            $this->assign('typehtml', $typehtml);
            $this->assign('data', $data);
            $this->display('slide_set.htm');
        }else{
            $id = intval( R('id','P') );
            if( empty($id) )	$this->message(0, '编辑失败：'.$id.'不存在！');

            $info = R('info','P');

            if( $info['url'] && !check::is_url($info['url']) )	$this->message(0, '编辑幻灯失败：URL格式不正确');
            if(!$info['type'])	$this->message(0, '编辑幻灯失败：类型未定义');

            $info['id'] = $id;

            if(!$model->update($info)) {
                $this->message(0, '编辑失败：更新表失败！');
            }else{
                $this->message(1, '编辑成功！','index.php?u=slide-index');
            }
        }
    }

    // 删除幻灯
    public function del(){
        $model = &$this->slide;
        $id = intval( R('id','P') );

        $status = $model->delete($id);
        if( $status ){
            $this->message(1, '删除成功,幻灯ID:'.$id,'index.php?u=slide-index');
        }else{
            $this->message(0, '删除失败,幻灯ID:'.$id);
        }
    }

	// 排序
	public function listorder() {
		$listorder = R('listorder','P');
		if( empty($listorder) ){
			$this->message(0, '排序参数错误！');
		}
		$model = &$this->slide_data;
		foreach ($listorder as $k=>$v){
			$data['id']=$k;
			$data['listorder'] = intval($v);
			$model->update($data);
		}
		$this->message(1, '排序成功！');
	}
}
