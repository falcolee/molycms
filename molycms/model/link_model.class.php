<?php
// +----------------------------------------------------------------------
// | MOLYCMS	友情链接模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class link extends model {
	function __construct() {
		$this->table = 'link';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
}
