<?php
// +----------------------------------------------------------------------
// | MOLYCMS	表总数模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit('Access Denied');

class framework_count extends model {
	function __construct() {
		$this->table = 'framework_count';	// 表名
		$this->pri = array('name');	// 主键
	}
	
	function get_count_by_name($tablename=''){
		$arr = $this->get($tablename);
		if( $arr ){
			return $arr['count'];
		}else{
			return 0;
		}
	}
}
