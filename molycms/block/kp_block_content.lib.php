<?php
defined('KONG_PATH') || exit;

/**
 * 取得某一个模型下某条或者多条内容
 * @param int mid 模型ID 如果不填：自动识别
 * @param string ids 内容ID，可以多个用,隔开，比如1,2,3
 * @return array
 */
function kp_block_content($conf) {
	global $run;

	// hook kp_block_content_before.php

	$mid = _int($conf, 'mid', 2);
	$dateformat = empty($conf['dateformat']) ? 'Y-m-d' : $conf['dateformat'];
	$titlenum = _int($conf, 'titlenum');
	$intronum = _int($conf, 'intronum');
	$orderby = isset($conf['orderby']) && in_array($conf['orderby'], array('id', 'dateline','listorder')) ? $conf['orderby'] : 'id';
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	
	//内容ID
	if( isset($conf['ids']) ){
		$ids = $conf['ids'];
		if(strpos($ids,',')){
			$idsArr = explode(',', $ids);
			$where['id'] = array("IN" => $idsArr);
		}else{
			$where['id'] = $ids;
		}
	}else{
		return null;
	}
	
	$table_arr = &$run->_cfg['table_arr'];
	$table = isset($table_arr[$mid]) ? $table_arr[$mid] : 'article';
	
	// 初始模型表名
	$run->cms_content->table = 'cms_'.$table;
	
	// 读取内容列表
	$list_arr = $run->cms_content->find_fetch($where, array($orderby => $orderway));
	foreach($list_arr as &$v) {
		$run->cms_content->format($v, $mid, $dateformat, $titlenum, $intronum);
	}
	
	// hook kp_block_content_after.php

	return array('list'=> $list_arr);
}
