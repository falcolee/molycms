<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台评论控制器
// +----------------------------------------------------------------------
//

class comment_control extends commonbase_control{
	public $_var = array();	// 内容页参数

	public function index() {
		// hook comment_control_index_before.php

		$_GET['cid'] = (int)R('cid');
		$_GET['id'] = (int)R('id');
		$this->_var = $this->category->get_cache($_GET['cid']);
		empty($this->_var) && core::error404();
		
		$mid = (int)$this->_var['mid'];

		// 初始模型表名
		$this->cms_content->table = 'cms_'.$this->_var['table'];

		// 读取内容
		$_show = $this->cms_content->read($_GET['id']);
		if(empty($_show['cid']) || $_show['cid'] != $_GET['cid']) core::error404();

		// SEO 相关
		$this->_cfg['titles'] = $_show['title'];
		$this->_cfg['seo_keywords'] = empty($_show['seo_keywords']) ? $_show['title'] : $_show['seo_keywords'];
		$this->_cfg['seo_description'] = $_show['intro'];

		$this->assign('moly', $this->_cfg);
		$this->assign('moly_var', $this->_var);

		$GLOBALS['run'] = &$this;
		$GLOBALS['_show'] = &$_show;

		// hook comment_control_index_after.php
		
		$this->display('comment.htm');
	}

	// 发表评论
	public function post() {
		// hook comment_control_post_before.php
		
		// 关闭全站评论
		$_cfg = $this->runtime->xget();
		!empty($_cfg['dis_comment']) && $this->message(0, '已关闭全站评论，无法发表评论！');
		
		//验证码
		$code = R('code','P');
		if( $code == '' ){
			$this->message(0, '验证码不能为空！');
		}else{
			if( md5($code) != session::get('commentverify') ){
				$this->message(0, '验证码不正确！');
			}
		}
		
		//会员登录才能评论
		if( $_cfg['user_comment'] && !$this->_uid ){
			$this->message(0, '登录后才能评论！');
		}
		
		$cid = (int) R('cid', 'P');
		$id = (int) R('id', 'P');
		
		$cates = $this->category->get_cache($cid);
		empty($cates) && $this->message(0, '分类ID不正确！');
		
		$this->cms_content->table = 'cms_'.$cates['table'];
		$data = $this->cms_content->read($id);
		
		$data['iscomment'] && $this->message(0, '该内容不允许发表评论！');
		
		$content = htmlspecialchars(trim(R('content', 'P')));
		$author = htmlspecialchars(trim(R('author', 'P')));
		$ip = ip2long(ip());

		if(empty($cid) || empty($id)) $this->message(0, '参数不完整！');
		empty($author) && $this->message(0, '昵称不能为空！');
		strlen($author)>20 && $this->message(0, '昵称太长了！');
		empty($content) && $this->message(0, '评论内容不能为空！');
		strlen($content)>3000 && $this->message(0, '评论内容太长了！');
		
		//评论状态
		$status = 1;
		
		//敏感词过滤
		$comment_filter = isset($_cfg['comment_filter']) ? $_cfg['comment_filter']:'';
		if( $comment_filter ){
			$comment_filter = str_replace(PHP_EOL, '', $comment_filter);
			$comment_filter_arr = explode(':::', $comment_filter);
			foreach ($comment_filter_arr as $v){
				$content = str_replace($v, '**', $content);
			}
		}

		// hook comment_control_post_create_before.php
		
		$maxid = $this->cms_comment->create(array(
			'id' => $id,
			'mid' => $cates['mid'],
			'uid' => $this->_uid,
			'author' => $author,
			'content' => $content,
			'ip' => $ip,
			'dateline' => $_ENV['_time']
		));
		if(!$maxid) {
			$this->message(0, '写入评论表出错！');
		}

		//内容评论数+1
		$data['comments']++;
		if(!$this->cms_content->update($data)) {
			$this->message(0, '写入内容表出错！');
		}
		
		//登录用户评论，用户的评论数+1
		if( $this->_uid ){
			$user = &$this->user;
			$this->_user['comments'] += 1;
			$user->update($this->_user);
		}

		// hook comment_control_post_after.php

		$succMsg = '发表评论成功！';
		
		$this->message(1, $succMsg);
	}

	// 获取评论JSON
	public function json() {
		$cid = (int)R('cid');
		$id = (int)R('id');

		$commentid = (int)R('commentid');
		$orderway = isset($_GET['orderway']) && $_GET['orderway'] == 1 ? 1 : -1;
		$pagenum = empty($_GET['pagenum']) ? 20 : max(1, (int)$_GET['pagenum']);
		$dateformat = empty($_GET['dateformat']) ? 'Y-m-d H:i' : base64_decode($_GET['dateformat']);
		$humandate = isset($_GET['humandate']) ? ($_GET['humandate'] == 1 ? 1 : 0) : 1;

		if(empty($cid) || empty($id) || empty($commentid)) $this->message(0, '参数不完整！');

		$cates = $this->category->get_cache($cid);
		empty($cates) && $this->message(0, '分类ID不正确！');

		// 获取评论列表
		$key = $orderway == 1 ? '>' : '<';
		$where = array('id' => $id, 'mid'=>$cates['mid'], 'commentid' => array($key => $commentid));
		
		$ret = array();
		$ret['list_arr'] = $this->cms_comment->find_fetch($where, array('commentid' => $orderway), 0, $pagenum);
		foreach($ret['list_arr'] as &$v) {
			$this->cms_comment->format($v, $dateformat, $humandate);
		}

		$end_arr = end($ret['list_arr']);
		$commentid = $end_arr['commentid'];
		$orderway = max(0, $orderway);
		$dateformat = base64_encode($dateformat);
		$_cfg = $this->runtime->xget();
		$ret['next_url'] = $_cfg['webdir']."index.php?comment-json-cid-$cid-id-$id-commentid-$commentid-orderway-$orderway-pagenum-$pagenum-dateformat-$dateformat-humandate-$humandate-ajax-1";
		$ret['isnext'] = count($ret['list_arr']) < $pagenum ? 0 : 1;

		echo json_encode($ret);
		exit;
	}

	// hook comment_control_after.php
}
