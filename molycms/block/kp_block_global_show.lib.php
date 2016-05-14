<?php
defined('KONG_PATH') || exit;

/**
 * 内容页模块
 * @param string dateformat 时间格式
 * @param int show_prev_next 显示上下翻页
 * @return array
 */
function kp_block_global_show($conf) {
	global $run, $_show;

	// hook kp_block_global_show_before.php

	$dateformat = empty($conf['dateformat']) ? 'Y-m-d' : $conf['dateformat'];
	$show_prev_next = isset($conf['show_prev_next']) && (int)$conf['show_prev_next'] ? true : false;

	// 排除单页模型
	$mid = &$run->_var['mid'];
	if($mid == 1) return FALSE;

	// 初始模型表名
	$run->cms_content_data->table = 'cms_'.$run->_var['table'].'_data';

	// 格式化
	$run->cms_content->format($_show, $mid, $dateformat);

	// 合并大数据字段
	$id = &$_show['id'];
	$_show['comment_url'] = $run->cms_content->comment_url($run->_var['cid'], $id);
	$_show['views_url'] = $run->_cfg['webdir'].'index.php?u=views--cid-'.$run->_var['cid'].'-id-'.$id;
	$data = $run->cms_content_data->read($id);
	if($data) $_show += $data;

	// 提示：文章模型没有图集
	if(isset($_show['images'])) {
		$_show['images'] = (array)_json_decode($_show['images']);
		foreach($_show['images'] as &$v) {
			$v['big'] = $run->_cfg['webdir'].$v['big'];
			$v['thumb'] = $run->_cfg['webdir'].$v['thumb'];
		}
	}
	
	//内容分页 预留
	if( !isset($_show['content']) ) $_show['content'] = '';
	$CONTENT_POS = strpos($_show['content'], '_molycms_page_break_tag_');
	if ($CONTENT_POS !== false) {
		$contents = array_filter(explode('_molycms_page_break_tag_', $_show['content']));
		$_show['total'] = count($contents);	//总页数
				
		// 分页相关
		$currentpage = max(1, intval(R('page')));
		$page = min($_show['total'], $currentpage);
		
		$pages = pages($page, $_show['total'], $run->cms_content->content_url($_show['cid'],$_show['id'], $_show['alias'], $_show['dateline'],true));
		 
		//判断_molycms_page_break_tag_出现的位置是否在第一位
		if ($CONTENT_POS < 26) {
			$content = $contents[$currentpage];
		} else {
			$content = $contents[$currentpage - 1];
		}
		//分页
		$_show['pages'] = $pages;
		$_show['content'] = $content;
	}else{
		$_show['pages'] = '';
		$_show['total'] = 1;
	}

	// 显示上下翻页 (大数据站点建议关闭)
	if($show_prev_next) {
		// 上一页
		$_show['prev'] = $run->cms_content->find_fetch(array('cid' => $run->_var['cid'], 'id'=>array('<'=> $id)), array('id'=>-1), 0 , 1);
		$_show['prev'] = array_pop($_show['prev']);
		$run->cms_content->format($_show['prev'], $mid, $dateformat);

		// 下一页
		$_show['next'] = $run->cms_content->find_fetch(array('cid' => $run->_var['cid'], 'id'=>array('>'=> $id)), array('id'=>1), 0 , 1);
		$_show['next'] = array_pop($_show['next']);
		$run->cms_content->format($_show['next'], $mid, $dateformat);
	}

	// hook kp_block_global_show_after.php

	return $_show;
}
