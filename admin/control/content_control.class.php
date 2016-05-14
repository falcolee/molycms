<?php
// +----------------------------------------------------------------------
// | MOLYCMS	内容模型内容管理控制器
// +----------------------------------------------------------------------
//

class content_control extends admin_control {

	public function index(){
        $model_id = R('id','G');
        if(!$model_id)$this->message(0, '参数错误:内容模型未定义');
		$model = &$this->models;
        $target_model = $model->get($model_id);
        $table_name = 'u_m_'.$target_model['name'];
		$model->set_table($table_name);
		$pagenum = 15;
		$urlstr = '';
		$where = array();
		$total = $model->count();
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=content-index'.$urlstr.'-page-{page}');

        $model_fields = $this->model_fields->get_fields_by_model($model_id);
        $fields = array();
        foreach ($model_fields as $v)
        {
            if ($v['listable'])
            {
                $fields[] = $v;
            }
        }

		$list = $model->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
        $this->assign('total', $total);
        $this->assign('pages', $pages);
		$this->assign('list', $list);
		$this->assign('model',$target_model);
        $this->assign('fields',$fields);
		$this->display();
	}

    public function add(){
        if( empty($_POST) ){
            //$model_name = R('model','G');
            //if(!$model_name)$this->message(0, '参数错误:内容模型未定义');
            $model_id = R('id','G');
            if(!$model_id)$this->message(0, '参数错误:内容模型未定义');
            $model = &$this->models;
            $data = array();
            $data['model'] = $model->get($model_id);
            //$data['model']  = $model->get_model_by_name($model_name);
            if(!$data['model'])$this->message(0, '参数错误:内容模型错误');
            $model_fields = $this->model_fields->get_fields_by_model($data['model']['id']);
            foreach ($model_fields as $v)
            {
                if ($v['editable'])
                {
                    $data['fields'][] = $v;
                }
            }
            $data['content'] = array();
            $this->assign('data', $data);
            $this->display('content_set.htm');
        }else{
            $model_id = R('mid','P');
            if(!$model_id)$this->message(0, '添加失败：内容模型错误！');
            $info = R('info','P');
            $model = &$this->models;
            $target_model = $model->get($model_id);
            if(!$target_model)$this->message(0, '添加失败：内容模型错误！');
            $model->set_table('u_m_'.$target_model['name']);
            $id = $model->create($info);
            if($id){
                $this->message(1, '添加成功,ID:'.$id,'index.php?u=models-dindex');
            }else{
                $this->message(0, '添加失败：写入表失败！');
            }
        }

    }

    public function edit(){

    }

    public function del(){

    }

}
