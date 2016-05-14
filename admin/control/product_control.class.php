<?php
// +----------------------------------------------------------------------
// | MOLYCMS	产品管理
// +----------------------------------------------------------------------
//

class product_control extends admin_control {
	private $_table = 'cms_product';	//文章模型表名
	private $_mid = 3;	//模型ID
	
	// 内容管理
	public function index() {
		// hook admin_product_control_index_before.php

		$cid = intval(R('cid','R'));
		$keyword = empty($_POST) ? R('keyword') : R('keyword', 'P');
		if($keyword) {
			$keyword = urldecode($keyword);
			$keyword = safe_str($keyword);
		}
		$this->assign('keyword', $keyword);

		// 获取分类下拉框
		$cidhtml = $this->category->get_cidhtml_by_mid($this->_mid, $cid, '所有产品');
		$this->assign('cidhtml', $cidhtml);

		// 初始模型表名
		$this->cms_content->table = $this->_table;

		// 初始分页
		$pagenum = 10;
		if($keyword) {
			$where = array('title'=>array('LIKE'=>$keyword));
			$total = $this->cms_content->find_count($where);
			$urlstr = '-keyword-'.urlencode($keyword);
		}elseif($cid) {
			$where = array('cid' => $cid);
			$categorys = $this->category->read($cid);
			$total = isset($categorys['count']) ? $categorys['count'] : 0;
			$urlstr = '-cid-'.$cid;
		}else{
			$where = array();
			$total = $this->cms_content->count();
			$urlstr = '';
		}
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=product-index'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);

		// 读取内容列表
		$flag_arr = array(1=>'推荐', 2=>'热点', 3=>'头条', 4=>'精选', 5=>'幻灯');
		$cms_product_arr = $this->cms_content->list_arr($where, 'id', -1, ($page-1)*$pagenum, $pagenum, $total);
		foreach($cms_product_arr as &$v) {
			$this->cms_content->format($v, $this->_mid);

			// 属性
			$v['flagstr'] = '';
			if(!empty($v['imagenum'])) {
				$v['flagstr'] .= ' [图片]';
			}
			if( $v['flags'] ) {
				$v['flagstr'] .= ' ['.$flag_arr[$v['flags']].']';
			}
			
			if($v['flagstr']) $v['flagstr'] = '<font color="BC0B0B">'.$v['flagstr'].'</font>';
		}
		$this->assign('list', $cms_product_arr);

		// hook admin_product_control_index_after.php

		$this->display();
	}

	// 发布产品
	public function add() {
		// hook admin_product_control_add_before.php
	
		$uid = $this->_user['uid'];
		if(empty($_POST)) {
			//上次发布的分类CID
			$habits = (array)$this->kv->get('user_habits_uid_'.$uid);
			$cid = isset($habits['last_add_cid']) ? (int)$habits['last_add_cid'] : 0;
				
			//自动保存的数据
			$data = $this->kv->get('auto_save_product_uid_'.$uid);
			
			if($data) {
				!empty($data['cid']) && $cid = $data['cid'];
				$data['pic_src'] = empty($data['pic']) ? '../static/img/nopic.gif' : '../'.$data['pic'];
				empty($data['author']) && $data['author'] = $this->_user['author'];
				$data['flags'] = isset($data['flags']) ? intval($data['flags']):0;
				$data['iscomment'] = isset($data['iscomment']) ? intval($data['iscomment']):0;
				$data['listorder'] = isset($data['listorder']) ? intval($data['listorder']):0;
				$data['views'] = isset($data['views']) ? intval($data['views']):rand(50,200);
			}else{
				//初始值
				$data['flags'] = 0;
				$data['pic_src'] = '../static/img/nopic.gif';
				$data['author'] = $this->_user['author'];
				$data['iscomment'] = 0;
				$data['listorder'] = 0;
				$data['views'] = rand(50,200);
			}
				
			$this->assign('data', $data);
	
			$cidhtml = $this->category->get_cidhtml_by_mid($this->_mid);
			$this->assign('cidhtml', $cidhtml);
			
			//用于编辑器定位到模型ID
			$edit_cid_id = '&mid='.$this->_mid;
			$this->assign('edit_cid_id', $edit_cid_id);
				
			$this->display('product_set.htm');
		}else{
				
			$cid = intval( R('cid','P') );
			empty($cid) && $this->message(0, '请选择分类！');
				
			//主表数据
			$info = R('info','P');
			$info['cid'] = $cid;
			$info['uid'] = $this->_uid;
			$info['title'] = trim(strip_tags($info['title']));
				
			//副表数据
			$data_info = R('data_info','P');
			R('images','P') && $data_info['images'] = json_encode(R('images','P'));
	
			empty($info['title']) && $this->message(0, '标题不能为空！');
				
			$categorys = $this->category->read($info['cid']);
			if(empty($categorys)) $this->message(0, '分类ID不存在！');
				
			$mid = $this->category->get_mid_by_cid($info['cid']);
			$table = $this->models->get_table($mid);
				
			// 防止提交到其他模型的分类
			if($table != 'product') $this->message(0, '分类ID非法！');
				
			// 检测别名是否能用
			if($info['alias'] && $err_msg = $this->only_alias->check_alias($info['alias'])) {
				$this->message(0, $err_msg);
			}
	
			// 标签预处理，最多支持5个标签
			$tags = trim($info['tags'], ", \t\n\r\0\x0B");
			$tags_arr = explode(',', $tags);
			$this->cms_content_tag->table = 'cms_'.$table.'_tag';
			$tagdatas = $tags = array();
			for($i=0; isset($tags_arr[$i]) && $i<5; $i++) {
				$name = trim($tags_arr[$i]);
				if($name) {
					$tagdata = $this->cms_content_tag->get_tag_by_name($name);
					if(!$tagdata) {
						$tagid = $this->cms_content_tag->create(array('name'=>$name, 'count'=>0, 'content'=>''));
						if(!$tagid) $this->message(0, '写入标签表出错');
						$tagdata = $this->cms_content_tag->get($tagid);
					}
						
					$tagdata['count']++;
					$tagdatas[] = $tagdata;
					$tags[$tagdata['tagid']] = $tagdata['name'];
				}
			}
				
			$contentstr = trim( $data_info['content'] );
				
			// 远程图片本地化
			$isremote = intval(R('isremote', 'P'));
			$endstr = '';
			if($isremote) {
				$endstr .= $this->get_remote_img($table, $contentstr, $uid);
			}
				
			// 计算图片数，和非图片文件数
			$imagenum = $this->cms_attach->find_count(array('id'=>0,'mid'=>$mid, 'uid'=>$uid, 'isimage'=>1));
			$filenum = $this->cms_attach->find_count(array('id'=>0,'mid'=>$mid, 'uid'=>$uid, 'isimage'=>0));
				
			// 如果缩略图为空，并且内容含有图片，则将第一张图片设置为缩略图
			if(empty($info['pic']) && $imagenum) {
				$info['pic'] = $this->auto_pic($table, $uid);
			}
				
			// 如果摘要为空，自动生成摘要
			$info['intro'] = $this->auto_intro($info['intro'], $contentstr);
				
			//推荐位
			$flags = $info['flags'];
			
			$info['tags'] = _json_encode($tags);
			$info['lasttime'] = $_ENV['_time'];
			$info['dateline'] = $_ENV['_time'];
			$info['imagenum'] = $imagenum;
			$info['filenum'] = $filenum;
			$info['listorder'] = intval( $info['listorder'] );
				
			$info['seo_title'] = trim(strip_tags($info['seo_title']));
			$info['seo_keywords'] = trim(strip_tags($info['seo_keywords']));
				
			//该钩子用来扩展产品主表
			// hook admin_product_control_add_content_after.php
	
			$this->cms_content->table = 'cms_'.$table;
			$id = $this->cms_content->create($info);
			if(!$id) {
				$this->message(0, '写入内容表出错');
			}
				
			// 写入内容数据表
			//该钩子用来扩展产品副表
			// hook admin_product_control_add_content_data_after.php
	
			$this->cms_content_data->table = 'cms_'.$table.'_data';
			$data_info['id'] = $id;
			if(!$this->cms_content_data->set($id, $data_info)) {
				$this->message(0, '写入内容数据表出错');
			}
			
			// 写入内容查看数表
			$views = intval(R('views'));
			$this->cms_content_views->table = 'cms_'.$table.'_views';
			if(!$this->cms_content_views->set($id, array('id' => $id, 'cid' => $cid, 'views' => $views))) {
				$this->message(0, '写入内容查看数表出错');
			}
	
			// 写入内容标签表
			$this->cms_content_tag_data->table = 'cms_'.$table.'_tag_data';
			foreach($tagdatas as $tagdata) {
				$this->cms_content_tag->update($tagdata);
				$this->cms_content_tag_data->set(array($tagdata['tagid'], $id), array('tagid' => $tagdata['tagid'], 'id'=>$id));
			}
				
			// 写入全站唯一别名表
			if($info['alias'] && !$this->only_alias->set($info['alias'], array('alias' => $info['alias'],'mid' => $mid, 'cid' => $info['cid'], 'id' => $id))) {
				$this->message(0, '写入全站唯一别名表出错');
			}
	
			// 更新附件归宿 cid 和 id
			if($imagenum || $filenum) {
				if(!$this->cms_attach->find_update(array('id'=>0, 'uid'=>$uid), array('cid'=>$info['cid'], 'id'=>$id))) {
					$this->message(0, '更新内容附件表出错');
				}
			}
				
			// 更新用户发布的内容条数
			$this->_user['contents']++;
			$this->user->update($this->_user);
	
			// 更新分类的内容条数
			$categorys['count']++;
			$this->category->update($categorys);
			$this->category->update_cache($info['cid']);
			
			//删除自动保存数据
			$this->kv->delete('auto_save_product_uid_'.$uid);
			
			// 记住最后一次发布的分类ID，感觉这样人性化一些吧。
			$habits = (array) $this->kv->get('user_habits_uid_'.$uid);
			$habits['last_add_cid'] = $cid;
			$habits = $this->kv->set('user_habits_uid_'.$uid, $habits);
	
			// hook admin_product_control_add_after.php
			$this->message(1, '发布完成'.$endstr,'index.php?u=product-index');
		}
	}
	
	// 编辑
	public function edit() {
		// hook admin_article_control_edit_before.php
	
		if(empty($_POST)) {
			$id = intval(R('id'));
			$cid = intval(R('cid'));
	
			$cidhtml = $this->category->get_cidhtml_by_mid($this->_mid, $cid);
			$this->assign('cidhtml', $cidhtml);
	
			$table = 'product';
	
			// 读取内容
			$this->cms_content->table = 'cms_'.$table;
			$this->cms_content_data->table = 'cms_'.$table.'_data';
			$this->cms_content_views->table = 'cms_'.$table.'_views';
			$data = $this->cms_content->get($id);
			if(empty($data)) $this->message(0, '内容不存在！');
	
			$data2 = $this->cms_content_data->get($id);
			$data3 = $this->cms_content_views->get($id);
			$data = array_merge($data, $data2, $data3);
			$data['images'] = (array)_json_decode($data['images']);
			$data['content'] = htmlspecialchars($data['content']);
			$data['tags'] = implode(',', (array)_json_decode($data['tags']));
			$data['intro'] = str_replace('<br />', "\n", strip_tags($data['intro'], '<br>'));
			$data['pic_src'] = empty($data['pic']) ? '../static/img/nopic.gif' : '../'.$data['pic'];
			//$data['dateline'] = date('Y-m-d H:i:s', $data['dateline']);
			$this->assign('data', $data);
			
			//用于编辑器定位到模型ID
			$edit_cid_id = '&mid='.$this->_mid.'&cid='.$data['cid'].'&id='.$data['id'];
			$this->assign('edit_cid_id', $edit_cid_id);
	
			$this->display('product_set.htm');
		}else{
				
			$cid = intval( R('cid','P') );
			empty($cid) && $this->message(0, '请选择分类！');
				
			$uid = $this->_user['uid'];
				
			//主表数据
			$info = R('info','P');
			$info['cid'] = $cid;
			$id = intval( $info['id'] );
			$info['title'] = trim(strip_tags($info['title']));
	
			//副表数据
			$data_info = R('data_info','P');
			R('images','P') && $data_info['images'] = json_encode(R('images','P'));
				
			empty($info['title']) && $this->message(0, '标题不能为空！');
	
			$categorys = $this->category->read($info['cid']);
			if(empty($categorys)) $this->message(0, '分类ID不存在！');
	
			$mid = $this->category->get_mid_by_cid($info['cid']);
			$table = $this->models->get_table($mid);
	
			// 防止提交到其他模型的分类
			if($table != 'product') $this->message(0, '分类ID非法！');
				
			$this->cms_content->table = 'cms_'.$table;
			$data = $this->cms_content->get($id);	//读取原来的内容
			if(empty($data)) $this->message(0, '内容不存在！');
	
			// 检测别名是否能用
			$alias_old = $data['alias'];
			if($info['alias'] && $info['alias'] != $alias_old && $err_msg = $this->only_alias->check_alias($info['alias'])) {
				$this->message(0, $err_msg);
			}
	
			// 比较标签变化
			$tags = trim($info['tags'], ", \t\n\r\0\x0B");
			$tags_new = explode(',', $tags);
			$tags_old = (array)_json_decode($data['tags']);
			$tags_arr = $tags = array();
			foreach($tags_new as $tagname) {
				$key = array_search($tagname, $tags_old);
				if($key === false) {
					$tags_arr[] = $tagname;
				}else{
					$tags[$key] = $tagname;
					unset($tags_old[$key]);
				}
			}
	
			// 标签预处理，最多支持5个标签
			$this->cms_content_tag->table = 'cms_'.$table.'_tag';
			$tagdatas = array();
			for($i=0; isset($tags_arr[$i]) && $i<5; $i++) {
				$name = trim($tags_arr[$i]);
				if($name) {
					$tagdata = $this->cms_content_tag->get_tag_by_name($name);
					if(!$tagdata) {
						$tagid = $this->cms_content_tag->create(array('name'=>$name, 'count'=>0, 'content'=>''));
						if(!$tagid) $this->message(0, '写入标签表出错');
						$tagdata = $this->cms_content_tag->get($tagid);
					}
	
					$tagdata['count']++;
					$tagdatas[] = $tagdata;
					$tags[$tagdata['tagid']] = $tagdata['name'];
				}
			}
				
			$contentstr = trim( $data_info['content'] );
	
			// 远程图片本地化
			$isremote = intval(R('isremote', 'P'));
			$endstr = '';
			if($isremote) {
				$endstr .= $this->get_remote_img($table, $contentstr, $uid, $cid, $id);
			}
	
			// 计算图片数，和非图片文件数
			$imagenum = $this->cms_attach->find_count(array('id'=>$id,'mid'=>$mid, 'uid'=>$uid, 'isimage'=>1));
			$filenum = $this->cms_attach->find_count(array('id'=>$id,'mid'=>$mid, 'uid'=>$uid, 'isimage'=>0));
	
			// 如果缩略图为空，并且内容含有图片，则将第一张图片设置为缩略图
			if(empty($info['pic']) && $imagenum) {
				$info['pic'] = $this->auto_pic($table, $uid, $id);
			}
	
			// 如果摘要为空，自动生成摘要
			$info['intro'] = $this->auto_intro($info['intro'], $contentstr);
	
			// 如果分类ID发生变化，更新分类内容数
			if($cid != $data['cid']) {
				// 旧的分类内容数减1
				$categorys_old = $this->category->read($data['cid']);
				$categorys_old['count'] = max(0, $categorys_old['count']-1);
				$this->category->update($categorys_old);
	
	
				// 新的分类内容数加1
				$categorys['count']++;
				$this->category->update($categorys);
	
				$this->category->delete_cache();
			}
	
			// 编辑时，别名有三种情况需要处理
			if($info['alias'] && $alias_old && $info['alias'] != $alias_old) {
				// 写入新别名
				if(!$this->only_alias->set($info['alias'], array('alias' => $info['alias'], 'mid' => $mid, 'cid' => $cid, 'id' => $id))) {
					$this->message(0, '写入全站唯一别名表出错');
				}
	
				// 删除旧别名
				if(!$this->only_alias->delete($alias_old)) {
					$this->message(0, '删除别名表旧数据时出错');
				}
			}elseif($info['alias'] && empty($alias_old)) {
				// 写入新别名
				if(!$this->only_alias->set($info['alias'], array('alias' => $info['alias'], 'mid' => $mid, 'cid' => $cid, 'id' => $id))) {
					$this->message(0, '写入全站唯一别名表出错');
				}
			}elseif(empty($info['alias']) && $alias_old) {
				// 删除旧别名
				if(!$this->only_alias->delete($alias_old)) {
					$this->message(0, '删除别名表旧数据时出错');
				}
			}
	
			// 写入内容表
			$info['tags'] = _json_encode($tags);
			$info['lasttime'] = $_ENV['_time'];
			$info['imagenum'] = $imagenum;
			$info['filenum'] = $filenum;
			$info['listorder'] = intval( $info['listorder'] );
			$info['seo_title'] = trim(strip_tags($info['seo_title']));
			$info['seo_keywords'] = trim(strip_tags($info['seo_keywords']));
				
			$data = array_merge($data,$info);
				
			//该钩子用来扩展产品主表
			// hook admin_product_control_edit_content_after.php
				
			if(!$this->cms_content->update($data)) {
				$this->message(0, '更新内容表出错');
			}
	
			// 写入内容数据表
			//该钩子用来扩展产品副表
			// hook admin_product_control_edit_content_data_after.php
				
			$this->cms_content_data->table = 'cms_'.$table.'_data';
			$data_info['id'] = $id;
			if(!$this->cms_content_data->set($id, $data_info)) {
				$this->message(0, '更新内容数据表出错');
			}
			
			// 写入内容查看数表
			$views = intval(R('views'));
			$this->cms_content_views->table = 'cms_'.$table.'_views';
			if(!$this->cms_content_views->set($id, array('id' => $id, 'cid' => $cid, 'views' => $views))) {
				$this->message(0, '写入内容查看数表出错');
			}
	
			// 写入内容标签表
			$this->cms_content_tag_data->table = 'cms_'.$table.'_tag_data';
			foreach($tagdatas as $tagdata) {
				$this->cms_content_tag->update($tagdata);
				$this->cms_content_tag_data->set(array($tagdata['tagid'], $id), array('tagid'=>$tagdata['tagid'],'id'=>$id));
			}
	
			// 删除不用的标签
			foreach($tags_old as $tagid => $tagname) {
				$tagdata = $this->cms_content_tag->get($tagid);
				$tagdata['count']--;
				$this->cms_content_tag->update($tagdata);
				$this->cms_content_tag_data->delete($tagid, $id);
			}
	
			// hook admin_product_control_edit_after.php
	
			$this->message(1, '编辑完成'.$endstr,'index.php?u=product-index');
		}
	}

	// 删除产品
	public function del() {
		// hook admin_product_control_del_before.php

		$id = (int) R('id', 'R');
		$cid = (int) R('cid', 'R');

		empty($id) && $this->message(0,'内容ID不能为空！');
		empty($cid) && $this->message(0,'分类ID不能为空！');

		// hook admin_product_control_del_after.php

		$err = $this->cms_content->xdelete('product', $id, $cid);
		if($err) {
			$this->message(0,$err);
		}else{
			$this->message(1,'删除产品成功，文章ID：'.$id);
		}
	}

	// 批量删除产品
	public function batch_del() {
		// hook admin_product_control_batch_del_before.php

		$id_arr = R('ids', 'P');
		if(!empty($id_arr) && is_array($id_arr)) {
			$err_num = 0;
			foreach($id_arr as $v) {
				$err = $this->cms_content->xdelete('product', $v);
				if($err) $err_num++;
			}
			
			if($err_num) {
				$this->message(0,$err_num.' 篇产品删除失败！');
			}else{
				$this->message(1,'删除成功！');
			}
		}else{
			$this->message(0,'参数不能为空！');
		}
	}

	// 删除单个附件
	public function del_attach() {
		// hook admin_product_control_del_attach_before.php

		$aid = (int) R('aid', 'P');

		empty($aid) && $this->message(0,'AID不能为空！');

		// hook admin_product_control_del_attach_after.php
		
		if($this->cms_attach->xdelete($aid)) {
			//减少内容表 附件数量
			
			$this->message(1,'删除成功！');
		}else{
			$this->message(0,'删除失败！');
		}
	}

	// 单独保存图集
	public function save_images() {
		$id = intval(R('id', 'P'));
		$images = (array)R('images', 'P');

		empty($id) && $this->message(0,'ID不能为空！');
		empty($images) && $this->message(0,'亲，您的产品忘上传图片了！');

		// 写入内容数据表
		$this->cms_content_data->table = 'cms_product_data';
		$data = $this->cms_content_data->read($id);
		if(empty($data)) {
			$this->message(0,'内容不存在！');
		}
		$data['images'] = json_encode($images);
		if($this->cms_content_data->set($id, $data)) {
			$this->message(1,'保存成功！');
		}else{
			$this->message(0,'写入内容数据表出错！');
		}
	}
	
	// 排序
	public function listorder() {
		$listorder = R('listorder','P');
		if( empty($listorder) ){
			$this->message(0, '排序参数错误！');
		}
		$this->cms_content->table = $this->_table;
		foreach ($listorder as $k=>$v){
			$data['id']=$k;
			$data['listorder'] = intval($v);
			$this->cms_content->update($data);
		}
		$this->message(1, '排序成功！');
	}
	
	// 自动保存
	public function auto_save() {
		$this->kv->set('auto_save_product_uid_'.$this->_user['uid'], $_POST) ? E(0, '自动保存成功！') : E(1, '自动保存失败！');
	}

	// 自动生成摘要
	private function auto_intro($intro, &$content) {
		if(empty($intro)) {
			$intro = preg_replace('/\s{2,}/', ' ', strip_tags($content));
			return trim(utf8::cutstr_cn($intro, 255, ''));
		}else{
			return str_replace(array("\r\n", "\r", "\n"), '<br />', strip_tags($intro));
		}
	}

	// 自动生成缩略图
	private function auto_pic($table, $uid, $id = 0) {
		$pic_arr = $this->cms_attach->find_fetch(array('id'=>$id, 'uid'=>$uid, 'isimage'=>1,'mid'=>$this->_mid), array(), 0, 1);
		$pic_arr = array_pop($pic_arr);
		$cfg = $this->runtime->xget();
		$path = 'upload/'.$table.'/'.$pic_arr['filepath'];
		$pic = image::thumb_name($path);
		$src_file = MOLYCMS_PATH.$path;
		$dst_file = MOLYCMS_PATH.$pic;
		if(is_file($src_file) && !is_file($dst_file)) {
			image::thumb($src_file, $dst_file, $cfg['thumb_'.$table.'_w'], $cfg['thumb_'.$table.'_h'], $cfg['thumb_type'], $cfg['thumb_quality']);
		}
		return $pic;
	}

	// 获取远程图片
	private function get_remote_img($table, &$content, $uid, $cid = 0, $id = 0) {
		function_exists('set_time_limit') && set_time_limit(0);
		$cfg = $this->runtime->xget();
		$updir = 'upload/'.$table.'/';
		$_ENV['_prc_err'] = 0;
		$_ENV['_prc_arg'] = array(
			'hosts'=>array('127.0.0.1', 'localhost', $_SERVER['HTTP_HOST'], $cfg['webdomain']),
			'uid'=>$uid,
			'cid'=>$cid,
			'id'=>$id,
			'mid'=>$this->_mid,
			'maxSize'=>10000,
			'upDir'=>MOLYCMS_PATH.$updir,
			'preUri'=>$cfg['weburl'].$updir,
			'cfg'=>$cfg,
		);
		$content = preg_replace_callback('#\<img [^\>]*src=["\']((?:http|ftp)\://[^"\']+)["\'][^\>]*\>#iU', array($this, 'img_replace'), $content);
		unset($_ENV['_prc_arg']);
		return $_ENV['_prc_err'] ? '，但远程抓取图片失败 '.$_ENV['_prc_err'].' 张！' : '';
	}

	// 远程图片处理 (如果抓取失败则不替换)
	// $conf 用到4个参数 hosts preUri cfg upDir
	private function img_replace($mat) {
		static $uris = array();
		$uri = $mat[1];
		$conf = &$_ENV['_prc_arg'];
		$conf['mid'] = $this->_mid;

		// 排除重复保存相同URL图片
		if(isset($uris[$uri])) return str_replace($uri, $uris[$uri], $mat[0]);

		// 根据域名排除本站图片
		$urls = parse_url($uri);
		if(in_array($urls['host'], $conf['hosts'])) return $mat[0];

		$file = $this->cms_attach->remote_down($uri, $conf);
		if($file) {
			$uris[$uri] = $conf['preUri'].$file;
			$cfg = $conf['cfg'];

			// 是否添加水印
			if(!empty($cfg['watermark_pos'])) {
				image::watermark($conf['upDir'].$file, MOLYCMS_PATH.'static/img/watermark.png', null, $cfg['watermark_pos'], $cfg['watermark_pct']);
			}

			return str_replace($uri, $uris[$uri], $mat[0]);
		}else{
			$_ENV['_prc_err']++;
			return $mat[0];
		}
	}

	// hook admin_product_control_after.php
}
