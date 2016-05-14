<?php
defined('KONG_PATH') || exit;

/**
 * 碎片详情模块
 * @param int id 碎片ID 
 * @return array
 */
function kp_block_block_view($conf) {
	global $run;

	// hook kp_block_block_view_before.php

	$id = isset($conf['id']) ? intval($conf['id']) : 0;
	
	if( $id == 0 ) return array();

	$data = $run->block->get($id);

	// hook kp_block_block_view_after.php

	return $data;
}
