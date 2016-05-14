<?php
defined('KONG_PATH') || exit;

/**
 * 内容列表排行模块 (排行功能比较消耗资源，故暂时不增加 一周内、一月内 评论/点击排行功能，有此需求的用户二次开发吧)
 * @param int cid 分类ID 如果不填，为自动识别；如果cid为0时，为整个模型
 * @param int mid 模型ID (当cid为0时，设置mid才能生效，否则程序自动识别)
 * @param string dateformat 时间格式
 * @param int titlenum 标题长度
 * @param int intronum 简介长度
 * @param string orderby 排序方式   评论数排列[comments] 点击数排列[views]
 * @param int start 开始位置
 * @param int limit 显示几条
 * @param int life 缓存时间 (开启二级缓存后，点击数排列才会有缓存时间)
 * @return array
 */
function kp_block_list_top($conf) {
	global $run;

	// hook kp_block_list_top_before.php

	$cid = isset($conf['cid']) ? intval($conf['cid']) : (isset($_GET['cid']) ? intval($_GET['cid']) : 0);
	$mid = _int($conf, 'mid', 2);
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d' : $conf['dateformat'];
	$titlenum = _int($conf, 'titlenum');
	$intronum = _int($conf, 'intronum');
	$orderby = isset($conf['orderby']) && in_array($conf['orderby'], array('comments', 'views')) ? $conf['orderby'] : 'views';
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	$start = _int($conf, 'start',0);
	$limit = _int($conf, 'limit', 10);
	$life = _int($conf, 'life', 60);
	
	if($cid == 0) {
		// 当cid为0时，根据mid确定table
		$table_arr = &$run->_cfg['table_arr'];
		$table = isset($table_arr[$mid]) ? $table_arr[$mid] : 'article';
		$where = array();
	}else{
		$cate_arr = $run->category->get_cache($cid);
		$table = &$cate_arr['table'];
		$where = array('cid' => $cid);
	}
	
	if( $orderby == 'comments' ){
		// 初始模型表名
		$run->cms_content->table = 'cms_'.$table;
		$list_arr = $run->cms_content->find_fetch($where, array('comments' => $orderway), $start, $limit);
		foreach($list_arr as $k=>&$v) {
			$run->cms_content->format($v, $mid, $dateformat, $titlenum, $intronum);
		}
	}else{
		$run->cms_content_views->table = $table_key = 'cms_'.$table.'_views';
		$key_arr = $run->cms_content_views->find_fetch($where, array($orderby => $orderway), $start, $limit, $life);

		$table_key .= '-id-';
		$keys = array();
		foreach($key_arr as $v) {
			$keys[] = $v['id'];
		}

		// 读取内容列表
		$run->cms_content->table = 'cms_'.$table;
		$list_arr = $run->cms_content->mget($keys);
		foreach($list_arr as $k=>&$v) {
			$run->cms_content->format($v, $mid, $dateformat, $titlenum, $intronum);
			isset($v['id']) && $v['views'] = $key_arr[$table_key.$v['id']]['views'];
		}
	}
	
	// hook kp_block_list_top_after.php

	return array('list'=> $list_arr);
}
