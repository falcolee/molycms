<?php
// +----------------------------------------------------------------------
// | MOLYCMS	留言板模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class guestbook extends model {
	function __construct() {
		$this->table = 'cms_guestbook';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
}
