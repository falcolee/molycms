<?php
// +----------------------------------------------------------------------
// | MOLYCMS	广告模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit('Access Denied');

class ad extends model {
	function __construct() {
		$this->table = 'cms_ad';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
}
