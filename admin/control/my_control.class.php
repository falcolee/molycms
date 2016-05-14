<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台我的信息控制器
// +----------------------------------------------------------------------
//

class my_control extends admin_control {
	//我的信息
	public function index(){
		// 格式化后显示给用户
		$this->user->format($this->_user);
		// 常用功能
		$used_array = $this->get_used();
		$this->assign('used_array', $used_array);
		$this->display();
	}	

	// 修改密码
	public function password() {
		if(empty($_POST)) {
			// hook admin_my_control_password_after.php

			$this->display();
		}else{
			$pData = R('data','P');
			$oldpw = trim($pData['oldpw']);
			$newpw = trim($pData['newpw']);
			$confirm_newpw = trim($pData['confirm_newpw']);
			$data = $this->_user;

			if(empty($oldpw)) {
				$this->message(0, '修改密码失败：旧密码不能为空！');
			}elseif(strlen($newpw) < 8) {
				$this->message(0, '修改密码失败：新密码不能小于8位！');
			}elseif($confirm_newpw != $newpw) {
				$this->message(0, '修改密码失败：确认密码与新密码不一致！');
			}elseif($oldpw == $newpw) {
				$this->message(0, '修改密码失败：新密码不能和旧密码相同！');
			}elseif(!$this->user->verify_password($oldpw, $data['salt'], $data['password'])) {
				$this->message(0, '修改密码失败：旧密码不正确！');
			}

			// hook admin_my_control_password_post_after.php

			$data['salt'] = random(16, 3, '0123456789abcdefghijklmnopqrstuvwxyz~!@#$%^&*()_+<>,.'); // 增加破解难度
			$data['password'] = get_password($newpw,$data['salt']);
			if(!$this->user->update($data)) {
				$this->message(0, '修改密码失败！');
			}else{
				$this->message(1, '修改密码成功！');
			}
		}
	}
	
	// 修改资料
	public function profile() {
		if(empty($_POST)) {
			// hook admin_my_control_profile_after.php
			$data = $this->_user;
			$this->assign('adminInfo',$data);
			
			$this->display();
		}else{
			$pData = R('data','P');
			$email = trim($pData['email']);
			$author = trim($pData['author']);
			
			if( $email && !check::is_email($email) ){
				$this->message(0, '邮箱格式不正确！');
			}
			
			$data = $this->_user;
			
			// hook admin_my_control_profile_post_after.php
	
			$author && $data['author'] = $author;
			$data['email'] = $email;
			if(!$this->user->update($data)) {
				$this->message(0, '修改个人资料失败！');
			}else{
				$this->message(1, '修改个人资料成功！');
			}
		}
	}

	// 获取常用功能
	private function get_used() {
		$arr = array(
			array('name'=>'发布文章', 'url'=>'article-add', 'imgsrc'=>'admin/ico/article_add.jpg'),
			array('name'=>'文章管理', 'url'=>'article-index', 'imgsrc'=>'admin/ico/article_index.jpg'),
			array('name'=>'发布产品', 'url'=>'product-add', 'imgsrc'=>'admin/ico/product_add.jpg'),
			array('name'=>'产品管理', 'url'=>'product-index', 'imgsrc'=>'admin/ico/product_index.jpg'),
			array('name'=>'发布图集', 'url'=>'photo-add', 'imgsrc'=>'admin/ico/photo_add.jpg'),
			array('name'=>'图集管理', 'url'=>'photo-index', 'imgsrc'=>'admin/ico/photo_index.jpg'),
			array('name'=>'评论管理', 'url'=>'comment-index', 'imgsrc'=>'admin/ico/comment_index.jpg'),
			array('name'=>'分类管理', 'url'=>'category-index', 'imgsrc'=>'admin/ico/category_index.jpg'),
		);

		// hook admin_my_control_get_used_after.php

		return $arr;
	}

	// 获取其他信息
	private function get_other() {
		$s = '';
		if(function_exists('extension_loaded')) {
			if(extension_loaded('gd')) {
				function_exists('imagepng') && $s .= 'png ';
				function_exists('imagejpeg') && $s .= 'jpg ';
				function_exists('imagegif') && $s .= 'gif ';
			}
			extension_loaded('iconv') && $s .= 'iconv ';
			extension_loaded('mbstring') && $s .= 'mbstring ';
			extension_loaded('zlib') && $s .= 'zlib ';
			extension_loaded('ftp') && $s .= 'ftp ';
			function_exists('fsockopen') && $s .= 'fsockopen';
		}
		return $s;
	}

	// hook admin_my_control_after.php
}
