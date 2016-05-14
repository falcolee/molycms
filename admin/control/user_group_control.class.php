<?php
// +----------------------------------------------------------------------
// | MOLYCMS	用户组管理
// +----------------------------------------------------------------------
//

class user_group_control extends admin_control {
	
	// 用户组管理
	public function index() {
		$user_group = &$this->user_group;
		$user_group_arr = $user_group->get_groups();
		$this->assign('list', $user_group_arr);

		// hook admin_user_group_control_index_after.php
		$this->display();
	}

	// hook admin_user_group_control_after.php
}
