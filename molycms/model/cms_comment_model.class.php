<?php
// +----------------------------------------------------------------------
// | MOLYCMS	评论模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class cms_comment extends model {
	function __construct() {
		$this->table = 'cms_comment';	// 表名
		$this->pri = array('commentid');	// 主键
		$this->maxid = 'commentid';		// 自增字段
	}
	
	// 格式化评论数组
	public function format(&$v, $dateformat = 'Y-m-d', $humandate = TRUE) {
		// hook cms_comment_model_format_before.php
	
		if(empty($v)) return FALSE;
	
		$v['date'] = $humandate ? human_date($v['dateline'], $dateformat) : date($dateformat, $v['dateline']);
		$v['ip'] = long2ip($v['ip']);
		$v['ip'] = substr($v['ip'], 0, strrpos($v['ip'], '.')).'.*';
		
		$v['avatar'] = $_ENV['_config']['front_static'].'img/avatar.png';
		if( $v['uid'] ){
			$user = $this->user->read($v['uid']);
			$user['avatar'] && $v['avatar'] = $user['avatar'];
		}
	
		// hook cms_comment_model_format_after.php
	}
	
	// 获取评论列表
	public function list_arr($where, $orderway, $start, $limit, $total) {
		// 优化大数据量翻页
		if($start > 1000 && $total > 2000 && $start > $total/2) {
			$orderway = -$orderway;
			$newstart = $total-$start-$limit;
			if($newstart < 0) {
				$limit += $newstart;
				$newstart = 0;
			}
			$list_arr = $this->find_fetch($where, array('commentid' => $orderway), $newstart, $limit);
			return array_reverse($list_arr, TRUE);
		}else{
			return $this->find_fetch($where, array('commentid' => $orderway), $start, $limit);
		}
	}
	
	// 评论关联删除
	public function xdelete($table, $id, $commentid) {
		// hook cms_comment_model_xdelete_before.php
		
		$this->cms_content->table = 'cms_'.$table;
	
		// 更新评论数
		$data = $this->cms_content->read($id);
		if(empty($data)) return '读取内容表出错！';
		if($data['comments'] > 0) {
			$data['comments']--;
			if(!$this->cms_content->update($data)) return '写入内容表出错！';
		}
		
		//减少用户评论数
		if( $data['uid'] > 0 ){
			$userdata = $this->user->read($data['uid']);
			if( $userdata['comments'] ){
				$userdata['comments']--;
				if(!$this->user->update($userdata)) return '写入用户表出错！';
			}
		}
		return $this->delete($commentid) ? '' : '删除失败！';
	}
}
