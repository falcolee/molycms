<?php
defined('KONG_PATH') || exit;

/**
 * 分类展示模块	获取单一分类标签,包括单页
 * @param int cid 分类ID 如果不填：自动识别
 * @return array
 */
function kp_block_categoryone($conf) {
	global $run;

	// hook kp_block_categoryone_before.php

	$cid = isset($conf['cid']) ? intval($conf['cid']) : _int($_GET, 'cid',0);
	
	if( $cid == 0 ) return null;
	
	$cat = $run->category->get_cache($cid);
	
	if(empty($cat)) return null;
	
	$cat['content'] = '';
	if( $cat['mid'] == 1 ){	//单页
		$page = $run->cms_page->read($cid);
		$cat['content'] = $page['content'];
	}
	
	$cat['url'] = $run->category->category_url($cat['cid'], $cat['alias']);
	
	// hook kp_block_categoryone_after.php

	return $cat;
}
