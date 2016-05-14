<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台登陆日志模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class loginlog extends model {
	function __construct() {
		$this->table = 'loginlog';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
}
