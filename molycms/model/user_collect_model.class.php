<?php
// +----------------------------------------------------------------------
// | MOLYCMS	用户收藏模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class user_collect extends model {
	public $data = array();		// 防止重复查询
	
	function __construct() {
		$this->table = 'user_collect';	// 表名
		$this->pri = array('collect_id');	// 主键
		$this->maxid = 'collect_id';		// 自增字段
	}
	
	
}
