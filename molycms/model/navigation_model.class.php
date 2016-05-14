<?php
// +----------------------------------------------------------------------
// | MOLYCMS	导航模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class navigation extends model {
	function __construct() {
		$this->table = 'navigation';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
}
