<?php
defined('KONG_PATH') || exit;

/**
 * 幻灯片模块
 * @param int id 幻灯片ID
 * @param string orderby 排序方式
 * @param int orderway 降序(-1),升序(1)
 * @param int start 开始位置
 * @param int limit 显示几条 
 * @return array
 */
function kp_block_slide($conf) {
	global $run;

	// hook kp_block_slide_before.php

	$id = _int($conf, 'id', 0);
	$orderby = isset($conf['orderby']) && in_array($conf['orderby'], array('id', 'listorder')) ? $conf['orderby'] : 'id';
	$orderway = isset($conf['orderway']) && $conf['orderway'] == 1 ? 1 : -1;
	$start = _int($conf, 'start');
	$limit = _int($conf, 'limit', 5);
	
	if( $id == 0 ) return array();

	$data = $run->slide->get($id);
	if( empty($data) || $data['status'] == 0 ) return array();
	
	$where['slide_id'] = $id;
	$where['status'] = 1;
	
	// 读取内容列表
	$list_arr = $run->slide_data->find_fetch($where, array($orderby => $orderway), $start, $limit);

	// hook kp_block_slide_after.php

	return $list_arr;
}
