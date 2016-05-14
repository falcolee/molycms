<?php
// +----------------------------------------------------------------------
// | MOLYCMS	投稿管理
// +----------------------------------------------------------------------
//

class audit_control extends admin_control {
	
	//投稿列表
	public function index(){
		$cid = intval(R('cid','R'));
	
		// 获取分类下拉框
		$cidhtml = $this->category->get_cidhtml_by_mid(2, $cid, '所有文章');
		$this->assign('cidhtml', $cidhtml);
	
		// 初始模型表名
		$this->cms_content->table = 'cms_audit_article';
	
		// 初始分页
		$pagenum = 10;
		$where = array();
		if($cid) {
			$where['cid'] = $cid;
			$total = $this->cms_content->find_count($where);
			$urlstr = '-cid-'.$cid;
		}else{
			$urlstr = '';
			$total = $this->cms_content->count();
		}
	
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=user-audit'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
	
		$cms_arr = $this->cms_content->list_arr($where, 'id', -1, ($page-1)*$pagenum, $pagenum, $total);
		foreach($cms_arr as &$v) {
			$this->cms_content->format($v, 2);
		}
		$this->assign('list', $cms_arr);
	
		$this->display();
	}
	
	public function edit(){
		
		if( empty($_POST) ){		
			$id = (int) R('id', 'R');
			$cid = (int) R('cid', 'R');
			
			empty($id) && $this->message(0,'内容ID不能为空！');
			empty($cid) && $this->message(0,'分类ID不能为空！');
			
			// 初始模型表名
			$this->cms_content->table = 'cms_audit_article';
			// 内容读取
			$data = $this->cms_content->read($id);
			if(empty($data)) $this->message(0,'内容不存在！');
			
			!empty($data['cid']) && $cid = $data['cid'];
			$data['pic_src'] = empty($data['pic']) ? '../static/img/nopic.gif' : '../'.$data['pic'];
			$data['listorder'] = isset($data['listorder']) ? intval($data['listorder']):0;
			$data['views'] = isset($data['views']) ? intval($data['views']):0;
			
			$this->assign('data', $data);
			
			$cidhtml = $this->category->get_cidhtml_by_mid(2,$cid);
			$this->assign('cidhtml', $cidhtml);
				
			//用于编辑器定位到模型ID
			$edit_cid_id = '&mid=2&cid='.$data['cid'];
			$this->assign('edit_cid_id', $edit_cid_id);
			
			$this->display();
		}else{
			//添加到正式的文章表
			$oldid = intval( R('oldid','P') );	//审核表的ID
			
			$cid = intval( R('cid','P') );
			empty($cid) && $this->message(0, '请选择分类！');
				
			//主表数据
			$info = R('info','P');
			$info['cid'] = $cid;
			$uid = $info['uid'];
			$info['title'] = trim(strip_tags($info['title']));
				
			//副表数据
			$data_info = R('data_info','P');
			
			empty($info['title']) && $this->message(0, '标题不能为空！');
				
			$categorys = $this->category->read($info['cid']);
			if(empty($categorys)) $this->message(0, '分类ID不存在！');
				
			$mid = $this->category->get_mid_by_cid($info['cid']);
			$table = $this->models->get_table($mid);
				
			// 防止提交到其他模型的分类
			if($table != 'article') $this->message(0, '分类ID非法！');
				
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
				$endstr .= $this->get_remote_img($table, $contentstr, $this->_uid);
			}
				
			// 计算图片数，和非图片文件数
			$imagenum = $this->cms_attach->find_count(array('id'=>0,'mid'=>$mid, 'uid'=>$this->_uid, 'isimage'=>1));
			$filenum = $this->cms_attach->find_count(array('id'=>0,'mid'=>$mid, 'uid'=>$this->_uid, 'isimage'=>0));
			
			//旧的图片数，和非图片文件数
			$imagenumold = $this->cms_attach->find_count(array('id'=>$oldid,'mid'=>255, 'uid'=>$uid, 'isimage'=>1));
			$filenumold = $this->cms_attach->find_count(array('id'=>$oldid,'mid'=>255, 'uid'=>$uid, 'isimage'=>0));

			$imagenum += $imagenumold;
			$filenum += $filenumold;
			
			// 如果缩略图为空，并且内容含有图片，则将第一张图片设置为缩略图
			if(empty($info['pic']) && $imagenum ) {
				$info['pic'] = $this->auto_pic($table, $uid,0,$oldid);
			}
				
			// 如果摘要为空，自动生成摘要
			$info['intro'] = $this->auto_intro($info['intro'], $contentstr);
				
			$info['tags'] = _json_encode($tags);
			$info['lasttime'] = $_ENV['_time'];
			$info['dateline'] = $_ENV['_time'];
			$info['imagenum'] = $imagenum;
			$info['filenum'] = $filenum;
			$info['listorder'] = intval( $info['listorder'] );
				
			$info['seo_title'] = trim(strip_tags($info['seo_title']));
			$info['seo_keywords'] = trim(strip_tags($info['seo_keywords']));
				
			//该钩子用来扩展文章主表
			// hook admin_audit_control_add_content_after.php
			
			$this->cms_content->table = 'cms_'.$table;
			$id = $this->cms_content->create($info);
			if(!$id) {
				$this->message(0, '写入内容表出错');
			}
				
			// 写入内容数据表
			//该钩子用来扩展文章副表
			// hook admin_audit_control_add_content_data_after.php
			
			$this->cms_content_data->table = 'cms_'.$table.'_data';
			if(!$this->cms_content_data->set($id, $data_info)) {
				$this->message(0, '写入内容数据表出错');
			}
			
			// 写入内容查看数表
			$views = intval(R('views'));
			$this->cms_content_views->table = 'cms_'.$table.'_views';
			if(!$this->cms_content_views->set($id, array('cid' => $cid, 'views' => $views))) {
				$this->message(0, '写入内容查看数表出错');
			}
			
			// 写入内容标签表
			$this->cms_content_tag_data->table = 'cms_'.$table.'_tag_data';
			foreach($tagdatas as $tagdata) {
				$this->cms_content_tag->update($tagdata);
				$this->cms_content_tag_data->set(array($tagdata['tagid'], $id), array('id'=>$id));
			}
				
			// 写入全站唯一别名表
			if($info['alias'] && !$this->only_alias->set($info['alias'], array('mid' => $mid, 'cid' => $info['cid'], 'id' => $id))) {
				$this->message(0, '写入全站唯一别名表出错');
			}
			
			// 更新附件归宿 cid 和 id
			if($imagenum || $filenum) {
				//更新管理员添加的附件
				$this->cms_attach->find_update(array('id'=>0, 'uid'=>$this->_uid), array('cid'=>$info['cid'], 'uid'=>$uid, 'id'=>$id));
							
				//更新旧的附件 从255 更新到2
				$this->cms_attach->find_update(array('id'=>$oldid, 'uid'=>$uid), array('cid'=>$info['cid'],'mid'=>$mid));
			}
				
			// 更新用户发布的内容条数
			$userModel = &$this->user;
			$userData = $userModel->get($uid);
			$userData['contents']++;
			$userModel->update($userData);
			
			// 更新分类的内容条数
			$categorys['count']++;
			$this->category->update($categorys);
			$this->category->update_cache($info['cid']);
				
			//删除自动保存的数据
			$this->kv->delete('auto_save_article_uid_'.$uid);
			
			//删除审核表数据
			$this->cms_content->table = 'cms_audit_article';
			$this->cms_content->delete($oldid);
			
			// hook admin_audit_control_add_after.php
			$this->message(1, '审核成功'.$endstr,'index.php?u=audit-index');
			
		}
	}
	
	//投稿拒绝
	public function dorefuse(){
		$id = (int) R('id', 'P');
		empty($id) && $this->message(0,'内容ID不能为空！');
		
		$refuse = R('refusecontent','P');
		
		$this->cms_content->table = 'cms_audit_article';
		
		$data = $this->cms_content->get($id);
		if(empty($data)) $this->message(0, '内容不存在！');
		
		$data['refuse'] = $refuse;
		$data['status'] = 1;	//已拒绝状态
		
		if(!$this->cms_content->update($data)) {
			$this->message(0, '更新投稿表出错');
		}else{
			$this->message(1, '拒绝成功！','index.php?u=audit-index');
		}
	}
	
	//删除
	public function del(){
		$id = (int) R('id', 'R');		
		empty($id) && $this->message(0,'内容ID不能为空！');
		
		// 初始模型表名
		$this->cms_content->table = 'cms_audit_article';
		$this->cms_attach->table = 'cms_attach';
		
		// 内容读取
		$data = $this->cms_content->read($id);
		if(empty($data)) $this->message(0,'内容不存在！');
		
		// 删除附件
		$attach_arr = $this->cms_attach->find_fetch(array('id'=>$id,'mid'=>255));
		$updir = MOLYCMS_PATH.'upload/article/';
		foreach($attach_arr as $v) {
			$file = $updir.$v['filepath'];
			$thumb = image::thumb_name($file);
			try{
				is_file($file) && unlink($file);
				is_file($thumb) && unlink($thumb);
			}catch(Exception $e) {
			}
			$this->cms_attach->delete($v['aid']);
		}
		
		$this->cms_content->delete($id);
		
		$this->message(1,'删除成功!','index.php?u=audit-index');
	}
	
	//批量删除
	public function batch_del(){
		$id_arr = R('ids', 'P');
		if(!empty($id_arr) && is_array($id_arr)) {
			foreach($id_arr as $v) {
				$id = $v;
				// 删除附件
				$attach_arr = $this->cms_attach->find_fetch(array('id'=>$id,'mid'=>255));
				$updir = MOLYCMS_PATH.'upload/article/';
				foreach($attach_arr as $v) {
					$file = $updir.$v['filepath'];
					$thumb = image::thumb_name($file);
					try{
						is_file($file) && unlink($file);
						is_file($thumb) && unlink($thumb);
					}catch(Exception $e) {
					}
					$this->cms_attach->delete($v['aid']);
				}
				
				$this->cms_content->delete($id);
			}
		
			$this->message(1,'删除成功!','index.php?u=audit-index');
		}else{
			$this->message(0,'参数不能为空！');
		}
	}
	
	// 自动保存 忽略
	public function auto_save() {
		
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
	private function auto_pic($table, $uid, $id = 0,$oldid = 0) {
		$pic_arr = $this->cms_attach->find_fetch(array('id'=>0, 'uid'=>$this->_uid, 'isimage'=>1), array(), 0, 1);
		$pic_arr = current($pic_arr);
		
		if( empty($pic_arr) ){
			$pic_arr = $this->cms_attach->find_fetch(array('id'=>$oldid, 'uid'=>$uid, 'isimage'=>1), array(), 0, 1);
			$pic_arr = current($pic_arr);
		}
		
		$cfg = $this->runtime->xget();
		$path = 'upload/'.$table.'/'.$pic_arr['filepath'];
		$pic = image::thumb_name($path);
		$src_file = MOLYCMS_PATH.$path;
		$dst_file = MOLYCMS_PATH.$pic;
		if(!is_file($dst_file)) {
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
				'mid'=>2,
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
}
