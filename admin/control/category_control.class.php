<?php
// +----------------------------------------------------------------------
// | MOLYCMS	分类管理
// +----------------------------------------------------------------------
//

class category_control extends admin_control {
	// 分类管理
	public function index() {
		$mod_name = $this->models->get_name();
		$this->assign('mod_name', $mod_name);

		$_ENV['_category_class'] = &$this->category;
		$_cfg = $this->runtime->xget();
		$this->assign('_cfg', $_cfg);
		
		$models = $this->models->get_models();
		$modelsJson = json_encode($models);
		$this->assign('models', $modelsJson);

		$category_arr = $this->category->get_category_db();
		$array = array();
		
		foreach($category_arr as $r) {
			$url = $_ENV['_category_class']->category_url($r['cid'], $r['alias']);
			
			$r['str_manage'] = '<a class="btn" href="javascript:edit('.$r['cid'].');">编辑</a> <a class="btn" href="javascript:del('.$r['cid'].')">删除</a> <a class="btn" target="_blank" href="'.$url.'">查看</a>';
			
			$r['modulename'] = $models['models-mid-'.$r['mid']]['name'];
			if( $r['mid'] >1 ){
				if( $r['type'] == 1 ){
					$r['cat_type'] = '<font class="red">频道</font>';
				}else{
					$r['cat_type'] = '<font class="green">列表</font>';
				}
			}else{
				$r['cat_type'] = ' - ';
			}
			
			
			$array[] = $r;
		}
		
		$str  = "<tr>
		<td width='40' align='center'>
		<input name='orderby[\$cid]' type='text' size='3' value='\$orderby' class='input-text-c'>
		</td>
		<td align='center'>\$cid</td>
		<td >\$spacer\$name &nbsp;</td>
		<td align='center'>\$alias</td>
		<td align='center'>\$modulename</td>
		<td align='center'>\$cat_type</td>
		<td align='center'>\$count</td>
		<td align='center'>\$str_manage</td>
		</tr>";
		
		
		$tree = new categorytree($array);
		unset($array);
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│  ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		
		$categorys = $tree->get_tree(0, $str);
		$this->assign('categorys', $categorys);

		// hook admin_category_control_index_after.php

		$this->display();
	}
	
	// 写入分类 (包括添加和编辑)
	public function set() {
		if(!empty($_POST)) {
			$post = array(
					'cid' => intval(R('cid', 'P')),
					'mid' =>  intval(R('mid', 'P')),
					'type' => intval(R('type', 'P')),
					'upid' => intval(R('upid', 'P')),
					'name' => trim(strip_tags(R('name', 'P'))),
					'alias' => trim(R('alias', 'P')),
					'intro' => trim(strip_tags(R('intro', 'P'))),
					'cate_tpl' => trim(strip_tags(R('cate_tpl', 'P'))),
					'show_tpl' => trim(strip_tags(R('show_tpl', 'P'))),
					'count' => 0,
					'orderby' => intval(R('orderby', 'P')),
					'seo_title' => trim(strip_tags(R('seo_title', 'P'))),
					'seo_keywords' => trim(strip_tags(R('seo_keywords', 'P'))),
					'seo_description' => trim(strip_tags(R('seo_description', 'P'))),
			);
	
			$category = &$this->category;
	
			// 检查基本参数是否填写
			if($err = $category->check_base($post)) {
				E(1, $err['msg'], $err['name']);
			}
	
			// cid 没有值时，为增加分类，否则为编辑分类
			if(empty($post['cid'])) {
				// 检查别名是否被使用
				if($err = $category->check_alias($post['alias'])) {
					E(1, $err['msg'], $err['name']);
				}
	
				$maxid = $category->create($post);
				if(!$maxid) {
					E(1, '写入分类数据表出错');
				}
	
				// 单页时
				if($post['mid'] == 1) {
					$pagedata = array('content' => R('page_content', 'P'));
					if(!$this->cms_page->set($maxid, $pagedata)) {
						E(1, '写入单页数据表出错');
					}
				}
			}else{
				$data = $category->read($post['cid']);
	
				// 检查分类是否符合编辑条件
				if($err = $category->check_is_edit($post, $data)) {
					E(1, $err['msg'], $err['name']);
				}
	
				// 别名被修改过才检查是否被使用
				if($post['alias'] != $data['alias']) {
					$err = $category->check_alias($post['alias']);
					if($err) {
						E(1, $err['msg'], $err['name']);
					}
	
					// 修改导航中的分类的别名
					$navigate = $this->kv->xget('navigate');
					foreach($navigate as $k=>$v) {
						if($v['cid'] == $post['cid']) $navigate[$k]['alias'] = $post['alias'];
						if(isset($v['son'])) {
							foreach($v['son'] as $k2=>$v2) {
								if($v2['cid'] == $post['cid']) $navigate[$k]['son'][$k2]['alias'] = $post['alias'];
							}
						}
					}
					$this->kv->set('navigate', $navigate);
				}
	
				// 这里赋值，是为了开启缓存后，编辑时更新缓存
				$post['count'] = $data['count'];
				if(!$category->update($post)) {
					E(1, '写入分类数据表出错');
				}
	
				// 删除以前的单页数据
				if($data['mid'] == 1 && $post['mid'] > 1) {
					$this->cms_page->delete($post['cid']);
				}
	
				// 单页时
				if($post['mid'] == 1) {
					$pagedata = array('content' => R('page_content', 'P'));
					if(!$this->cms_page->set($post['cid'], $pagedata)) {
						E(1, '写入单页数据表出错');
					}
				}
			}
	
			// 删除缓存
			$this->runtime->truncate();
	
			E(0, '保存成功');
			
		}
	}

	// 删除分类
	public function del() {
		$cid = intval(R('cid','P'));

		$data = $this->category->read($cid);

		// 检查是否符合删除条件
		if($err_msg = $this->category->check_is_del($data)) {
			$this->message(0, $err_msg);
		}

		if(!$this->category->delete($cid)) {
			$this->message(0, '操作分类表时出错');
		}

		if($data['mid'] == 1 && !$this->cms_page->delete($cid)) {
			$this->message(0, '操作单页表时出错');
		}

		// 删除导航中的分类
		$navigate = $this->kv->xget('navigate');
		foreach($navigate as $k=>$v) {
			if($v['cid'] == $cid) unset($navigate[$k]);
			if(isset($v['son'])) {
				foreach($v['son'] as $k2=>$v2) {
					if($v2['cid'] == $cid) unset($navigate[$k]['son'][$k2]);
				}
			}
		}
		$this->kv->set('navigate', $navigate);

		// 删除缓存
		$this->runtime->delete('cfg');
		$this->category->delete_cache();
		$this->runtime->truncate();

		$this->message(1, '删除分类成功！','index.php?u=category-index');
	}

	// 修改分类排序
	public function listorder() {
		$orderby = R('orderby','P');
		if( empty($orderby) ){
			$this->message(0, '排序参数错误！');
		}
		foreach ($orderby as $k=>$v){
			$data['cid']=$k;
			$data['orderby'] = intval($v);
			$this->category->update($data);
		}
		$this->message(1, '分类排序成功！');
	}

	// AJAX读取上级分类(添加或者编辑分类时用)
	public function get_category_upid() {
		$data['upid'] = $this->category->get_category_upid(intval(R('mid')), intval(R('upid')), intval(R('noid')));
		echo json_encode($data);
		exit;
	}
	
	// 读取分类 (JSON)
	public function get_category_json() {
		$cid = intval(R('cid', 'P'));
		$data = $this->category->get($cid);
	
		// 读取单页内容
		if($data['mid'] == 1) {
			$data2 = $this->cms_page->get($cid);
			if($data2) $data['page_content'] = $data2['content'];
		}
	
		// 为频道时，检测是否有下级分类
		if($data['type'] == 1 && $this->category->find_fetch_key(array('upid' => $data['cid']), array(), 0, 1)) {
			$data['son_cate'] = 1;
		}
	
		echo json_encode($data);
		exit;
	}
	
	// hook admin_category_control_after.php
}
