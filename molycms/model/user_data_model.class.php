<?php
// +----------------------------------------------------------------------
// | MOLYCMS	用户扩展模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class user_data extends model {
	function __construct() {
		$this->table = 'user_data';		// 表名
		$this->pri = array('uid');	// 主键
	}
}
