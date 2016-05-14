<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台操作日志模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class operationlog extends model {
	function __construct() {
		$this->table = 'operationlog';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
}
