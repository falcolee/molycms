<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台登录日志和操作日志
// +----------------------------------------------------------------------
//

class log_control extends admin_control {
	
	//后台登录日志
	public function login(){
		$loginlog = &$this->loginlog;
		
		$status = empty($_POST) ? R('status') : R('status', 'P');
		$username = empty($_POST) ? R('username') : R('username', 'P');
		
		// 初始分页
		$condition['status'] = '*';
		$condition['username'] = '';
		
		$where = array();
		$pagenum = 15;
		$urlstr = '';
		
		if( $status !== null && $status != '*' ){
			$condition['status'] = $where['status'] = $status;
			$urlstr .= '-status-'.$status;
		}
		if( $username ){
			$condition['username'] = $where['username'] = $username;
			$urlstr .= '-username-'.urlencode($username);
		}
		
		if( $where ){
			$total = $loginlog->find_count($where);
		}else{
			$total = $loginlog->count();
		}
		
		$this->assign('condition',$condition);
		
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=log-login'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
		
		$log_list = $loginlog->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
		$this->assign('log_list', $log_list);
		
		$this->display();
	}
	


	//后台操作日志
	public function opt(){
		$optlog = &$this->operationlog;
	
		$status = empty($_POST) ? R('status') : R('status', 'P');
		$uid = empty($_POST) ? R('uid') : R('uid', 'P');
	
		// 初始分页
		$condition['status'] = '*';
		$condition['uid'] = '';
	
		$where = array();
		$pagenum = 8;
		$urlstr = '';
		if( $status !== null && $status != '*' ){
			$condition['status'] = $where['status'] = $status;
			$urlstr .= '-status-'.$status;
		}
		if( $uid ){
			$condition['uid'] = $where['uid'] = $uid;
			$urlstr .= '-uid-'.$uid;
		}
	
		if( $where ){
			$total = $optlog->find_count($where);
		}else{
			$total = $optlog->count();
		}
	
		$this->assign('condition',$condition);
	
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=log-opt'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
	
		$log_list = $optlog->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
		$this->assign('log_list', $log_list);
	
		$this->display();
	}
	


	//删除一个月前的登录日志或者操作日志
	public function dellog(){
		$type = R('type','G');
		if( $type == 'opt' ){
			$message = '删除一个月前操作日志成功';
			$model = &$this->operationlog;
			$where['dateline'] = array('<='=>time() - (86400 * 30));
		}else{
			$message = '删除一个月前登录日志成功';
			$model = &$this->loginlog;
			$where['logintime'] = array('<='=>time() - (86400 * 30));
		}
		$model->find_delete($where);
		$this->message(1, $message);
	}
}
