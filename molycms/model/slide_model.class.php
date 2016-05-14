<?php
// +----------------------------------------------------------------------
// | MOLYCMS	幻灯片模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class slide extends model {
	function __construct() {
		$this->table = 'slide';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
//	//关联删除幻灯片信息
//	function xdelete($id = 0){
//		$this->slide_data->find_fetch(array('slide_id'=>$id));
//		$ret = $this->delete($id);
//		return $ret ? '' : '删除失败！';
//	}
}
