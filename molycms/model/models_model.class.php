<?php
// +----------------------------------------------------------------------
// | MOLYCMS	内容模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class models extends model {
	public $data = array();		// 防止重复查询

	function __construct() {
		$this->table = 'models';	// 表名
		$this->pri = array('mid');	// 主键
		$this->maxid = 'mid';		// 自增字段
	}

	// 获取所有系统模型
	public function get_models() {
		if(isset($this->data['models'])) {
			return $this->data['models'];
		}
		return $this->data['models'] = $this->find_fetch(array('system'=>'1'));
	}

    // 获取所有自定义模型
    public function get_diy_models() {
        if(isset($this->data['diy_models'])) {
            return $this->data['diy_models'];
        }
        return $this->data['models'] = $this->find_fetch(array('system'=>'0'));
    }

	// 获取所有模型的名称
	public function get_name() {
		if(isset($this->data['name'])) {
			return $this->data['name'];
		}

		$models_arr = $this->get_models();
		$arr = array();
		foreach ($models_arr as $v) {
			$arr[$v['mid']] = $v['name'];
		}
		return $this->data['name'] = $arr;
	}

	// 获取所有模型的表名
	public function get_table_arr() {
		if(isset($this->data['table_arr'])) {
			return $this->data['table_arr'];
		}

		$models_arr = $this->get_models();
		unset($models_arr[1]);
		$arr = array();
		foreach ($models_arr as $v) {
			$arr[$v['mid']] = $v['tablename'];
		}
		return $this->data['table_arr'] = $arr;
	}

	// 根据 mid 获取模型的表名
	public function get_table($mid) {
		$data = $this->get($mid);
		return isset($data['tablename']) ? $data['tablename'] : 'article';
	}
	
	// 获取模型下拉列表HTML
	public function get_midhtml($_mid=0) {
		$models_arr = $this->get_models();
		
		$s = '<select name="mid" id="mid">';
		if(empty($models_arr)) {
			$s .= '<option value="0">没有模型</option>';
		}else{
			foreach($models_arr as $mid => $v) {
				if( $v['mid'] == 1 ) continue;
				$s .= '<option value="'.$v['mid'].'"'.( $v['mid'] == $_mid ? ' selected="selected"' : '').'>'.$v['name'].'模型</option>';
			}
		}
		$s .= '</select>';
		return $s;
	}

    // 根据 表名字 获取模型
    public function get_model_by_table_name($table_name){
        $data = $this->find_fetch(array('tablename'=>$table_name), array(), 0, 1);
        return $data ? array_pop($data) : array();
    }

    //根据名字检查是否有重复的非系统模型
    public function check_duplicate_model($table_name){
        $where = array('tablename'=>$table_name,'system'=>'0');
        if($this->find_count($where)>0){
            return TRUE;
        }  else {
            return FALSE;
        }
    }
}
