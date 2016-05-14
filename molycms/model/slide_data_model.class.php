<?php
// +----------------------------------------------------------------------
// | MOLYCMS	幻灯片模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class slide_data extends model {
	function __construct() {
		$this->table = 'cms_slide_data';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段
	}
	
}
