<?php
defined('KONG_PATH') || exit;

/**
 * 简单的友情链接插件
 * @return array
 */
function kp_block_links($conf) {
	global $run;

	// hook kp_block_links_before.php

	$arr = $run->kv->xget('maybe_links');

	// hook kp_block_links_after.php

	return $arr;
}
