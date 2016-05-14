<?php
// +----------------------------------------------------------------------
// | MOLYCMS	自定义分类模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class cate_models extends model {
    public $data = array();		// 防止重复查询
	function __construct() {
		$this->table = 'cate_models';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}

    // 获取所有模型
    public function get_models() {
        if(isset($this->data['models'])) {
            return $this->data['models'];
        }
        return $this->data['models'] = $this->find_fetch();
    }

    public function get_model_by_name($name){
        $data = $this->find_fetch(array('name'=>$name), array(), 0, 1);
        return $data ? array_pop($data) : array();
    }

    public function check_duplicate_model($name){
        $where = array('name'=>$name);
        if($this->find_count($where)>0){
            return TRUE;
        }  else {
            return FALSE;
        }
    }
}
