<?php
// +----------------------------------------------------------------------
// | MOLYCMS	用户分组模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class user_group extends model {
	public $data = array();		// 防止重复查询
	
	function __construct() {
		$this->table = 'user_group';	// 表名
		$this->pri = array('groupid');	// 主键
		$this->maxid = 'groupid';		// 自增字段
	}
	
	// 获取所有分组
	public function get_groups() {
		if(isset($this->data['groups'])) {
			return $this->data['groups'];
		}
	
		return $this->data['groups'] = $this->find_fetch();
	}
	
	// 根据 groupid 获取分组
	public function get_group_one($groupid) {
		return $this->get($groupid);
	}
}
