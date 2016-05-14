<?php
// +----------------------------------------------------------------------
// | MOLYCMS	单页模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class cms_page extends model {
	function __construct() {
		$this->table = 'cms_page';	// 表名
		$this->pri = array('cid');	// 主键
	}
}
