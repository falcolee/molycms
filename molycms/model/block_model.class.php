<?php
// +----------------------------------------------------------------------
// | MOLYCMS	碎片模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class block extends model {
	function __construct() {
		$this->table = 'cms_block';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
}
