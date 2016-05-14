<?php
// +----------------------------------------------------------------------
// | 	模块分类模型
// +----------------------------------------------------------------------

defined('MOLYCMS_PATH') or exit;

class types extends model {
	public $data = array();		// 防止重复查询

	function __construct() {
		$this->table = 'types';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}

	// 获取所有模块分类
	public function get_types() {
		if(isset($this->data['types'])) {
			return $this->data['types'];
		}
		return $this->data['types'] = $this->find_fetch();
	}
        
        public function get_names($_type){
		$tmp_arr = $this->get_types();
                $models_arr = array();
                foreach ($tmp_arr as $key => $value) {
                   if($value['class'] == $_type){
                      $models_arr[$key] = $value ;
                   }
                }
                return $models_arr;
        }
	
	// 获取模型下拉列表HTML
	public function get_typehtml($_type='link',$_tid=0) {
        $models_arr = $this->get_names($_type);
		$s = '<select name="info[type]" id="type">';
		if(empty($models_arr)) {
			$s .= '<option value="0">没有模型分类</option>';
		}else{
			foreach($models_arr as $mid => $v) {
				$s .= '<option value="'.$v['id'].'"'.( $v['id'] == $_tid ? ' selected="selected"' : '').'>'.$v['title'].'</option>';
			}
		}
		$s .= '</select>';
		return $s;
	}
}
