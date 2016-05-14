<?php
// +----------------------------------------------------------------------
// | MOLYCMS	分类模型管理和分类模型字段管理控制器
// +----------------------------------------------------------------------
//

class cates_control extends admin_control {
	
	//系统模型列表
	public function index(){
        $model = &$this->cate_models;
        $total = $model->count();
        $list = $model->get_models();
        $this->assign('list', $list);
        $this->assign('total', $total);
        $this->display();
	}
    
    //分类模型字段列表
    public function dlist(){
        $model = &$this->cate_fields;
        $id = intval( R('id','G') );
        $where = array('model'=>$id);
	    $total = $model->find_count($where);
        
        $cate_model = &$this->cate_models;
        $model_data = $cate_model->get($id);
        $this->assign('model_data', $model_data);

        $file_types = $this->fieldtypes->get_field_types();
        $list = $model->find_fetch($where);
        $this->assign('total', $total);
        $this->assign('list', $list);
        $this->assign('fieldtypes', $file_types);
        $this->display();
    }

    //添加
    public function add(){

        if( empty($_POST) ){
            $data = array(
                'id'=>'',
                'name'=>'',
                'description'=>'',
                'perpage'=>'20',
                'level'=>'',
                'hasattach'=>'0'
            );
            $this->assign('data', $data);
            $this->display('cates_set.htm');
        }else{
            $info = R('info','P');
            if(!$info['name'])	$this->message(0, '添加失败：模型标识未定义');
            $model = &$this->cate_models;
            if($model->check_duplicate_model($info['name']))$this->message(0, '添加失败：模型标识重复');
            $id = $model->create($info);
            if($id){
                //$model->add_new_model($info['name']);
                $this->message(1, '添加成功,ID:'.$id,'index.php?u=cates-index');
            }else{
               $this->message(0, '添加失败：写入表失败！'); 
            }     
            //TODO thumb_preferences未设置
        }
    }

    //编辑内容模型
    public function edit(){
        $model = &$this->cate_models;
        if( empty($_POST) ){
            $id = intval( R('id','G') );
            $data = $model->get($id);
            if( empty($data) )	$this->message(0, '编辑失败：'.$id.'不存在！');

            $this->assign('data', $data);
            $this->display('cates_set.htm');
        }else{
            $id = intval( R('id','P') );
            
            if( empty($id) )	$this->message(0, '编辑失败：'.$id.'不存在！');
            $target_model = $model->get($id);
            if(!$target_model)$this->message(0, '不存在的内容模型');
            
            $info = R('info','P');

            if(!$info['name'])	$this->message(0, '编辑失败：模型标识未定义');
            if ($target_model['name'] != $info['name']) {
                if ($model->check_duplicate_model($info['name'])) $this->message(0, '编辑失败：模型标识重复');
            }
            $info['id'] = $id;
            
            if(!$model->update($info)) {
                $this->message(0, '编辑失败：更新表失败！');
            }else{
                if($target_model['name']!=$info['name']){
                    $this->diy_models->rename_table($target_model['name'],$info['name']);
                }        
                $this->message(1, '编辑成功！','index.php?u=cates-index');
            }
        }
    }

    //编辑字段
    public function fedit(){
        $model = &$this->cate_fields;
        if( empty($_POST) ){
            $id = intval( R('id','G') );
            $data = $model->get($id);
            if( empty($data) )	$this->message(0, '编辑失败：'.$id.'不存在！');
            $model_data = $this->cate_models->get($data['model']);
            if (empty($model_data)) $this->message(0, '编辑失败：字段所属内容模型不存在！');
            $input = array();
            $field_types = $this->fieldtypes->get_types_name();
            $validation_names = $this->validations->get_validations_name();
            $input['type'] = form::loop('select', 'info[type]', $field_types, $data['type']);
            $input['rules'] = form::loop('checkbox', 'info[rules]', $validation_names, $data['rules']);
            $input['searchable'] = form::get_yesno('info[searchable]', $data['searchable']);
            $input['listable'] = form::get_yesno('info[listable]', $data['listable']);
            $input['editable'] = form::get_yesno('info[editable]', $data['editable']);
            $this->assign('data', $data);
            $this->assign('model_data', $model_data);
            $this->assign('input', $input);
            $this->display('cates_field_set.htm');
        }else{
            $info = R('info','P');
            if (!array_key_exists('id', $info))$this->message(0, '编辑失败：字段不存在！');
            if (!array_key_exists('model', $info))  $this->message(0, '编辑失败：内容模型不存在！');
            if (array_key_exists('rules', $info)) {
                $info['rules'] = implode(",", $info['rules']);
            }
            $target_field = $model->get($info['id']);

            if(!$target_field)  $this->message(0, '编辑失败: 未找到该字段模型');
            if(!$info['name'])	$this->message(0, '编辑失败：模型标识名称未定义');
            if ($target_field['name'] != $info['name']) {
                if ($model->check_duplicate_field($info['name'], $info['model'])) $this->message(0, '编辑失败：已存在重复的字段名');
            }

            if(!$model->update($info)) {
                $this->message(0, '编辑失败：更新表失败！');
            }else{
                if($target_field['name']!=$info['name']){
                    $this->diy_models->modify_column($info['model'],$info,$target_field['name']);
                }
                $this->message(1, '编辑成功！','index.php?u=cates-dlist-id-'.$info['model']);
            }
        }
    }

    //添加字段
    public function fadd()
    {
        if (empty($_POST)) {
            $mid = intval(R('mid', 'G'));
            $data = array(
                'id' => '',
                'name' => '',
                'description' => '',
                'model' => $mid,
                'type' => 'input',
                'length' => '20',
                'source' => '',
                'width' => '0',
                'height' => '0',
                'rules' => '',
                'ruledescription' => '',
                'searchable' => '1',
                'listable' => '1',
                'editable' => '1',
                'listorder' => '99'
            );
            $model_data = $this->cate_models->get($data['model']);
            if (empty($model_data)) $this->message(0, '添加失败：所属内容模型不存在！');
            $input = array();
            $field_types = $this->fieldtypes->get_types_name();
            $validation_names = $this->validations->get_validations_name();
            $input['type'] = form::loop('select', 'info[type]', $field_types, $data['type']);
            $input['rules'] = form::loop('checkbox', 'info[rules]', $validation_names, $data['rules']);
            $input['searchable'] = form::get_yesno('info[searchable]', $data['searchable']);
            $input['listable'] = form::get_yesno('info[listable]', $data['listable']);
            $input['editable'] = form::get_yesno('info[editable]', $data['editable']);
            $this->assign('model_data', $model_data);
            $this->assign('input', $input);
            $this->assign('data', $data);
            $this->display('cates_field_set.htm');
        } else {
            $info = R('info', 'P');
            if (!array_key_exists('model', $info))$this->message(0, '添加失败：内容模型未定义');
            if (array_key_exists('rules', $info)) {
                $info['rules'] = implode(",", $info['rules']);
            }

            if (!$info['name']) $this->message(0, '添加失败：字段标识未定义');
            $model = & $this->cate_fields;
            if ($model->check_duplicate_field($info['name'], $info['model'])) $this->message(0, '添加失败：模型标识重复');
            $id = $model->create($info);
            if ($id) {
                $this->message(1, '添加成功,ID:' . $id, 'index.php?u=cates-dlist-id-' . $info['model']);
            } else {
                $this->message(0, '添加失败：写入表失败！');
            }
        }
    }

    // 删除内容模型
    public function del(){
        $model = &$this->cate_models;
        $id = intval( R('id','P') );
        if (empty($id)) $this->message(0, '删除失败：内容模型ID不存在！');
        $status = $model->delete($id);
        if( $status ){
            //删除所有所属字段
            $this->cate_fields->del_fields_by_model($id);
            $this->message(1, '删除成功,模型ID:'.$id,'index.php?u=cates-index');
        }else{
            $this->message(0, '删除失败,模型ID:'.$id);
        }
    }

    //删除字段
    public function fdel(){
        $model = &$this->cate_fields;
        $id = intval( R('id','P') );
        if (empty($id)) $this->message(0, '删除失败：字段ID不存在！');
        $status = $model->delete($id);
        if( $status ){
            $this->message(1, '删除成功,字段ID:'.$id,'index.php?u=cates-dindex');
        }else{
            $this->message(0, '删除失败,字段ID:'.$id);
        }
    }
}
