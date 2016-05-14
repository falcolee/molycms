<?php
defined('KONG_PATH') || exit;

/**
 * 广告详情模块
 * @param int id 广告ID 
 * @return array
 */
function kp_block_ad($conf) {
	global $run;

	// hook kp_block_ad_before.php

	$id = _int($conf, 'id', 0);
	if( $id == 0 ) return array();

	$data = $run->ad->get($id);
	if( empty($data) ) return array();
	
	$array = array(
			'adname'=>$data['adname'],		//广告名称
			'content'=>$data['normbody']	//广告内容
			);
	
	if( $data['timeset'] ){	//开启时间限制
		$time = time();
		if( $data['starttime'] > $time || $data['endtime'] < $time ){	//未到开始时间或者已经结束，则显示过期广告内容
			$array['content'] = $data['expbody'];
		}
	}

	// hook kp_block_ad_after.php

	return $array;
}
