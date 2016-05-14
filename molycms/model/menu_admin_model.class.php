<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台菜单模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit('Access Denied');

class menu_admin extends model {
	function __construct() {
		$this->table = 'menu_admin';	// 表名
		$this->pri = array('cid');	// 主键
		$this->maxid = 'cid';		// 自增字段
	}
	
}
