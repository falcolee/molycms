<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台留言控制器
// +----------------------------------------------------------------------
//

class guestbook_control extends commonbase_control{

	public function index() {
		// hook guestbook_control_index_before.php
		if( !empty($_POST) ){
			$title = trim( strip_tags(R('title','P')) );
			$author = strip_tags(R('author','P'));
			$email = strip_tags(R('email','P'));
			$telephone = strip_tags(R('telephone','P'));
			$content = trim( strip_tags(R('content','P')) );
			$code = strip_tags(R('code','P'));
			$uid = intval( $this->_uid );
			
			if( empty($content) ){
				$this->message(0, '内容不能为空！','history.back()');
			}elseif ( $email && !check::is_email($email) ){
				$this->message(0, '邮箱格式不正确！','history.back()');
			}elseif ( empty($email) && empty($telephone) ){
				$this->message(0, '邮箱或电话请填写一项！','history.back()');
			}elseif( md5($code) != session::get('guestbookcode') ){
				$this->message(0, '验证码不正确！','history.back()');
			}
			
			// 写入内容表
			$data = array(
					'uid' => $uid,
					'title' => $title,
					'author' => $author,
					'email' => $email,
					'telephone' => $telephone,
					'content' => $content,
					'dateline' => $_ENV['_time'],
					'ip' => ip2long($_ENV['_ip'])
			);
			// hook guestbook_control_index_after.php
			
			$model = &$this->guestbook;
			$id = $model->create($data);
			if( $id ){
				$this->message(1, '留言成功，等待管理员回复！');
			}else{
				$this->message(0, '留言失败，请重试！');
			}
		}else{
			$this->message(0, '留言失败，请重试！');
		}
	}
}
