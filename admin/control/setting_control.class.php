<?php
// +----------------------------------------------------------------------
// | MOLYCMS	站点设置
// +----------------------------------------------------------------------
//

class setting_control extends admin_control {
	// 基本设置
	public function index() {
		if(empty($_POST)) {
			$cfg = $this->kv->xget('cfg');
			$input = array();
			$input['webname'] = form::get_text('webname', $cfg['webname']);
			$input['webdomain'] = form::get_text('webdomain', $cfg['webdomain']);
			$input['webdir'] = form::get_text('webdir', $cfg['webdir']);
			$input['webmail'] = form::get_text('webmail', $cfg['webmail']);
			$input['tongji'] = form::get_textarea('tongji', $cfg['tongji']);
			$input['beian'] = form::get_text('beian', $cfg['beian']);
			$input['footer_info'] = form::get_textarea('footer_info', $cfg['footer_info']);

			// hook admin_setting_control_index_after.php

			$this->assign('input', $input);
			$this->display();
		}else{
			_trim($_POST);
			$this->kv->xset('webname', R('webname', 'P'), 'cfg');
			$this->kv->xset('webdomain', R('webdomain', 'P'), 'cfg');
			$this->kv->xset('webdir', R('webdir', 'P'), 'cfg');
			$this->kv->xset('webmail', R('webmail', 'P'), 'cfg');
			$this->kv->xset('tongji', R('tongji', 'P'), 'cfg');
			$this->kv->xset('beian', R('beian', 'P'), 'cfg');
			$this->kv->xset('footer_info', R('footer_info', 'P'), 'cfg');

			// hook admin_setting_control_index_post_after.php

			$this->kv->save_changed();
			$this->runtime->delete('cfg');

			$this->message(1,'修改基本设置成功！');
		}
	}
	
	//评论设置
	public function comment(){
		if(empty($_POST)) {
			$cfg = $this->kv->xget('cfg');
			$input = array();
			
			$input['dis_comment'] = empty($cfg['dis_comment']) ? 0 : 1;
			$input['user_comment'] = empty($cfg['user_comment']) ? 0 : 1;
			$input['comment_filter'] = form::get_textarea('comment_filter', $cfg['comment_filter']);
		
			// hook admin_setting_control_comment_after.php
		
			$this->assign('input', $input);
			$this->display();
		}else{
			_trim($_POST);
			$this->kv->xset('dis_comment', (int)R('dis_comment', 'P'), 'cfg');
			$this->kv->xset('user_comment', (int)R('user_comment', 'P'), 'cfg');
			$this->kv->xset('comment_filter', R('comment_filter', 'P'), 'cfg');
		
			// hook admin_setting_control_comment_post_after.php
		
			$this->kv->save_changed();
			$this->runtime->delete('cfg');
		
			$this->message(1,'修改评论设置成功！');
		}
	}

	// SEO设置
	public function seo() {
		if(empty($_POST)) {
			$cfg = $this->kv->xget('cfg');
			$input = array();
			$input['seo_title'] = form::get_text('seo_title', $cfg['seo_title'],'',80);
			$input['seo_keywords'] = form::get_text('seo_keywords', $cfg['seo_keywords'],'',80);
			$input['seo_description'] = form::get_textarea('seo_description', $cfg['seo_description']);

			// hook admin_setting_control_seo_after.php

			$this->assign('input', $input);
			$this->display();
		}else{
			_trim($_POST);
			$this->kv->xset('seo_title', R('seo_title', 'P'), 'cfg');
			$this->kv->xset('seo_keywords', R('seo_keywords', 'P'), 'cfg');
			$this->kv->xset('seo_description', R('seo_description', 'P'), 'cfg');

			// hook admin_setting_control_seo_post_after.php

			$this->kv->save_changed();
			$this->runtime->delete('cfg');

			$this->message(1,'修改SEO设置成功！');
		}
	}

	// 链接设置
	public function link() {
		if(empty($_POST)) {
			$software = R('SERVER_SOFTWARE', 'S');
			$this->assign('software', $software);

			$parseurl = $_ENV['_config']['molycms_parseurl'];
			$cfg = $this->kv->xget('cfg');
			$this->assign('cfg', $cfg);
			$mk = R('mk');
			$del = R('del');
			$do = (int) R('do');
			$this->assign('do', $do);

			// 伪静态规则
			$nginx = 'if (!-e $request_filename) {'."\n";
			$nginx .= "\t".'rewrite ^'.$cfg['webdir'].'(.+) '.$cfg['webdir'].'index.php?rewrite=$1 last;'."\n";
			$nginx .= '}';
			$this->assign('nginx', $nginx);

			$apache = '<IfModule mod_rewrite.c>'."\r\n";
			$apache .= 'RewriteEngine On'."\r\n";
			$apache .= 'RewriteBase '.$cfg['webdir']."\r\n";
			$apache .= 'RewriteCond %{REQUEST_FILENAME} !-f'."\r\n";
			$apache .= 'RewriteCond %{REQUEST_FILENAME} !-d'."\r\n";
			$apache .= 'RewriteRule (.+) index.php?rewrite=$1 [L]'."\r\n";
			$apache .= '</IfModule>';
			$this->assign('apache', $apache);

			// 创建.htaccess
			$file_apache = MOLYCMS_PATH.'.htaccess';
			$is_file_apache = is_file($file_apache);
			$this->assign('is_file_apache', $is_file_apache);
			if($mk == 'htaccess') {
				$f = @fopen($file_apache, 'w');
				if (!$f) {
					$this->message(0,'修改链接设置失败：无写入权限！');
				} else {
					$bytes = fwrite($f, $apache);
					fclose($f);
					if($bytes > 0) {
						$this->message(1,'修改链接设置成功：创建 .htaccess 成功！');
					}else{
						$this->message(0,'修改链接设置失败：创建 .htaccess 失败！');
					}
				}
			}

			// 删除.htaccess
			if($del == 'htaccess') {
				$ret = FALSE;
				try{ $is_file_apache && $ret = unlink($file_apache); }catch(Exception $e) {}
				if($ret) {
					$this->message(1,'修改链接设置成功：删除 .htaccess 成功！');
				}else{
					$this->message(0,'修改链接设置失败：删除.htaccess 失败！');
				}
			}

			$iis = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n";
			$iis .= '<configuration>'."\r\n";
			$iis .= "\t".'<system.webServer>'."\r\n";
			$iis .= "\t\t".'<rewrite>'."\r\n";
			$iis .= "\t\t\t".'<rules>'."\r\n";
			$iis .= "\t\t\t\t".'<rule name="MOLYCMS Rule '.$cfg['webdir'].'" stopProcessing="true">'."\r\n";
			$iis .= "\t\t\t\t\t".'<match url="(.+)" ignoreCase="false" />'."\r\n";
			$iis .= "\t\t\t\t\t".'<conditions logicalGrouping="MatchAll">'."\r\n";
			$iis .= "\t\t\t\t\t\t".'<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />'."\r\n";
			$iis .= "\t\t\t\t\t\t".'<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />'."\r\n";
			$iis .= "\t\t\t\t\t".'</conditions>'."\r\n";
			$iis .= "\t\t\t\t\t".'<action type="Rewrite" url="index.php?rewrite={R:1}" />'."\r\n";
			$iis .= "\t\t\t\t".'</rule>'."\r\n";
			$iis .= "\t\t\t".'</rules>'."\r\n";
			$iis .= "\t\t".'</rewrite>'."\r\n";
			$iis .= "\t".'</system.webServer>'."\r\n";
			$iis .= '</configuration>';
			$this->assign('iis', $iis);

			// 创建web.config
			$file_iis = MOLYCMS_PATH.'web.config';
			$is_file_iis = is_file($file_iis);
			$this->assign('is_file_iis', $is_file_iis);
			if($mk == 'web_config') {
				$f = @fopen($file_iis, 'w');
				if (!$f) {
					$this->message(0,'修改链接设置失败：无写入权限！');
				} else {
					$bytes = fwrite($f, $iis);
					fclose($f);
					if($bytes > 0) {
						$this->message(1,'修改链接设置成功：创建 web.config 成功！');
					}else{
						$this->message(0,'修改链接设置失败：创建 web.config 失败！');
					}
				}
			}

			// 删除web.config
			if($del == 'web_config') {
				$ret = FALSE;
				try{ $is_file_iis && $ret = unlink($file_iis); }catch(Exception $e) {}
				if($ret) {
					$this->message(1,'修改链接设置成功：删除 web.config 成功！');
				}else{
					$this->message(0,'修改链接设置失败：删除 web.config 失败！');
				}
			}

			// IIS6
			$path_file = $path_dir = '';
			$dh = opendir(MOLYCMS_PATH);
			while($file = readdir($dh)) {
				if(preg_match('#^[\w]+$#', $file) && is_dir(MOLYCMS_PATH.$file)) {
					$path_dir .= $file.'|';
				}elseif(preg_match('#^\w[\w\.]+$#', $file) && is_file(MOLYCMS_PATH.$file)) {
					$path_file .= preg_quote($file).'|';
				}
			}

			$webdir = preg_quote($cfg['webdir']);
			$iis6 = '[ISAPI_Rewrite]'."\r\n\r\n";
			$iis6 .= 'RewriteRule '.$webdir.'('.trim($path_file, '|').') '.$webdir.'$1 [L]'."\r\n";
			$iis6 .= 'RewriteRule '.$webdir.'('.trim($path_dir, '|').')/(.*) '.$webdir.'$1/$2 [L]'."\r\n";
			$iis6 .= 'RewriteRule '.$webdir.'(.+) '.$webdir.'index\.php\?rewrite=$1 [L]';
			$this->assign('iis6', $iis6);

			// 创建httpd.ini
			$file_iis6 = $_SERVER["DOCUMENT_ROOT"].'/httpd.ini';
			$is_file_iis6 = is_file($file_iis6);
			$this->assign('is_file_iis6', $is_file_iis6);
			if($mk == 'httpd_ini') {
				$f = @fopen($file_iis6, 'w');
				if (!$f) {
					$this->message(0,'修改链接设置失败：无写入权限！');
				} else {
					$bytes = fwrite($f, $iis6);
					fclose($f);
					if($bytes > 0) {
						$this->message(1,'修改链接设置成功：创建 httpd.ini 成功！');
					}else{
						$this->message(0,'修改链接设置失败：创建 httpd.ini 失败！');
					}
				}
			}

			// 删除httpd.ini
			if($del == 'httpd_ini') {
				$ret = FALSE;
				try{ $is_file_iis6 && $ret = unlink($file_iis6); }catch(Exception $e) {}
				if($ret) {
					$this->message(1,'修改链接设置成功：删除 httpd.ini 成功！');
				}else{
					$this->message(0,'修改链接设置失败：删除 httpd.ini 失败！');
				}
			}

			$input = array();
			$input['parseurl'] = form::loop('radio', 'parseurl', array('0'=>'动态', '1'=>'伪静态'), $parseurl, ' &nbsp; &nbsp;');
			$input['link_show'] = form::get_text('link_show', $cfg['link_show']);
			$input['link_cate_end'] = form::get_text('link_cate_end', $cfg['link_cate_end']);
			$input['link_cate_page_pre'] = form::get_text('link_cate_page_pre', $cfg['link_cate_page_pre']);
			$input['link_cate_page_end'] = form::get_text('link_cate_page_end', $cfg['link_cate_page_end']);
			$input['link_tag_pre'] = form::get_text('link_tag_pre', $cfg['link_tag_pre']);
			$input['link_tag_end'] = form::get_text('link_tag_end', $cfg['link_tag_end']);
			$input['link_comment_pre'] = form::get_text('link_comment_pre', $cfg['link_comment_pre']);
			$input['link_comment_end'] = form::get_text('link_comment_end', $cfg['link_comment_end']);
			$input['link_index_end'] = form::get_text('link_index_end', $cfg['link_index_end']);
			$this->assign('input', $input);

			// hook admin_setting_control_link_after.php

			$this->display();
		}else{
			_trim($_POST);
			// 伪静态开关
			$parseurl = (int)R('parseurl', 'P');
			$file = APP_PATH.'config/config.inc.php';
			if(!_is_writable($file)){
				$this->message(0,'修改链接设置失败：配置文件 molycms/config/config.inc.php 不可写！');
			}
			$s = file_get_contents($file);
			$s = preg_replace("#'molycms_parseurl'\s*=>\s*\d,#", "'molycms_parseurl' => {$parseurl},", $s);
			if(!file_put_contents($file, $s)){
				$this->message(0,'修改链接设置失败：写入 config.inc.php 失败！');
			}

			// 关闭伪静态时，不需要更改伪静态参数
			if($parseurl == 0) {
				$this->runtime->truncate();
				$this->message(1,'修改链接设置成功：修改成功！');
			}

			// 智能生成内容链接参数 (四种情况，性能方面依次排列)
			$link_show = R('link_show', 'P');
			if(substr($link_show, 0, 10) == '{cid}/{id}' && strpos($link_show, '{', 10) === FALSE) {
				$link_show_type = 1;
				$link_show_end = (string)substr($link_show, 10);
			}elseif(substr($link_show, 0, 17) == '{cate_alias}/{id}' && strpos($link_show, '{', 17) === FALSE) {
				$link_show_type = 2;
				$link_show_end = (string)substr($link_show, 17);
			}elseif(substr($link_show, 0, 7) == '{alias}' && strpos($link_show, '{', 7) === FALSE) {
				$link_show_type = 3;
				$link_show_end = (string)substr($link_show, 7);
			}else{
				$link_show_type = 4;
				$link_show_end = '';
			}
			$this->kv->xset('link_show', $link_show, 'cfg');
			$this->kv->xset('link_show_type', $link_show_type, 'cfg');
			$this->kv->xset('link_show_end', $link_show_end, 'cfg');

			$link_cate_page_pre = R('link_cate_page_pre', 'P');
			$link_cate_page_end = R('link_cate_page_end', 'P');
			$link_cate_end = R('link_cate_end', 'P');
			$link_tag_pre = R('link_tag_pre', 'P');
			$link_tag_end = R('link_tag_end', 'P');
			$link_comment_pre = R('link_comment_pre', 'P');
			$link_comment_end = R('link_comment_end', 'P');
			$link_index_end = R('link_index_end', 'P');

			// 暂时不考虑过滤 标签URL前缀 和 评论URL后缀 重复问题
			if(empty($link_cate_page_pre)) exit('{"err":1, "msg":"分类URL前缀不能为空"}');
			if(empty($link_cate_page_end)) exit('{"err":1, "msg":"分类URL后缀不能为空"}');
			if(empty($link_cate_end)) exit('{"err":1, "msg":"分类URL首页后缀不能为空"}');
			if(empty($link_tag_pre)) exit('{"err":1, "msg":"标签URL前缀不能为空"}');
			if(empty($link_tag_end)) exit('{"err":1, "msg":"标签URL后缀不能为空"}');
			if(empty($link_comment_pre)) exit('{"err":1, "msg":"评论URL前缀不能为空"}');
			if(empty($link_comment_end)) exit('{"err":1, "msg":"评论URL后缀不能为空"}');
			if(empty($link_index_end)) exit('{"err":1, "msg":"首页分页URL后缀不能为空"}');

			$this->kv->xset('link_index_end', $link_index_end, 'cfg');
			$this->kv->xset('link_cate_page_pre', $link_cate_page_pre, 'cfg');
			$this->kv->xset('link_cate_page_end', $link_cate_page_end, 'cfg');
			$this->kv->xset('link_cate_end', $link_cate_end, 'cfg');
			$this->kv->xset('link_tag_pre', $link_tag_pre, 'cfg');
			$this->kv->xset('link_tag_end', $link_tag_end, 'cfg');
			$this->kv->xset('link_comment_pre', $link_comment_pre, 'cfg');
			$this->kv->xset('link_comment_end', $link_comment_end, 'cfg');

			// hook admin_setting_control_link_post_after.php

			$this->kv->save_changed();
			$this->runtime->truncate();

			$this->message(1,'修改链接设置成功：修改成功！');
		}
	}

	// 上传设置
	public function attach() {
		if(empty($_POST)) {
			$cfg = $this->kv->xget('cfg');
			$input = array();
			$input['up_img_ext'] = form::get_text('up_img_ext', $cfg['up_img_ext'], 'input');
			$input['up_img_max_size'] = form::get_number('up_img_max_size', $cfg['up_img_max_size'], 'input');
			$input['up_file_ext'] = form::get_text('up_file_ext', $cfg['up_file_ext'], 'input');
			$input['up_file_max_size'] = form::get_number('up_file_max_size', $cfg['up_file_max_size'], 'input');

			// hook admin_setting_control_attach_after.php

			$this->assign('input', $input);
			$this->display();
		}else{
			_trim($_POST);
			$this->kv->xset('up_img_ext', R('up_img_ext', 'P'), 'cfg');
			$this->kv->xset('up_img_max_size', R('up_img_max_size', 'P'), 'cfg');
			$this->kv->xset('up_file_ext', R('up_file_ext', 'P'), 'cfg');
			$this->kv->xset('up_file_max_size', R('up_file_max_size', 'P'), 'cfg');

			// hook admin_setting_control_attach_post_after.php

			$this->kv->save_changed();
			$this->runtime->delete('cfg');
			
			$this->message(1,'修改上传设置成功：修改成功！');
		}
	}

	// 图片设置
	public function image() {
		if(empty($_POST)) {
			$cfg = $this->kv->xget('cfg');
			$input = array();
			$input['thumb_article_w'] = form::get_number('thumb_article_w', $cfg['thumb_article_w']);
			$input['thumb_article_h'] = form::get_number('thumb_article_h', $cfg['thumb_article_h']);
			$input['thumb_product_w'] = form::get_number('thumb_product_w', $cfg['thumb_product_w']);
			$input['thumb_product_h'] = form::get_number('thumb_product_h', $cfg['thumb_product_h']);
			$input['thumb_photo_w'] = form::get_number('thumb_photo_w', $cfg['thumb_photo_w']);
			$input['thumb_photo_h'] = form::get_number('thumb_photo_h', $cfg['thumb_photo_h']);

			$input['thumb_type'] = form::loop('radio', 'thumb_type', array('1'=>'补白', '2'=>'居中', '3'=>'上左'), $cfg['thumb_type'], ' &nbsp; &nbsp;');
			$input['thumb_quality'] = form::get_number('thumb_quality', $cfg['thumb_quality']);

			$cfg['watermark_pos'] = isset($cfg['watermark_pos']) ? (int)$cfg['watermark_pos'] : 0;
			$input['watermark_pct'] = form::get_number('watermark_pct', $cfg['watermark_pct']);

			// hook admin_setting_control_image_after.php

			$this->assign('input', $input);
			$this->assign('cfg', $cfg);
			$this->display();
		}else{
			$this->kv->xset('thumb_article_w', (int) R('thumb_article_w', 'P'), 'cfg');
			$this->kv->xset('thumb_article_h', (int) R('thumb_article_h', 'P'), 'cfg');
			$this->kv->xset('thumb_product_w', (int) R('thumb_product_w', 'P'), 'cfg');
			$this->kv->xset('thumb_product_h', (int) R('thumb_product_h', 'P'), 'cfg');
			$this->kv->xset('thumb_photo_w', (int) R('thumb_photo_w', 'P'), 'cfg');
			$this->kv->xset('thumb_photo_h', (int) R('thumb_photo_h', 'P'), 'cfg');
			$this->kv->xset('thumb_type', (int) R('thumb_type', 'P'), 'cfg');
			$this->kv->xset('thumb_quality', (int) R('thumb_quality', 'P'), 'cfg');
			$this->kv->xset('watermark_pos', (int) R('watermark_pos', 'P'), 'cfg');
			$this->kv->xset('watermark_pct', (int) R('watermark_pct', 'P'), 'cfg');

			// hook admin_setting_control_image_post_after.php

			$this->kv->save_changed();
			$this->runtime->delete('cfg');
			
			$this->message(1,'修改图片设置成功：修改成功！');
		}
	}
	
	//邮件设置
	public function email(){
		$mailconf = $this->kv->xget('mail_cfg');
		$sendtype = &$mailconf['sendtype'];
		$smtplist = &$mailconf['smtplist'];
		
		if(empty($_POST)) {		
			$data['sendtype'] = intval($sendtype);
			$data['smtplist'] = $smtplist;
			
			$this->assign('data', $data);
			$this->display();
		}else{
			$email = (array)R('email', 'P');
			$host = (array)R('host', 'P');
			$port = (array)R('port', 'P');
			$user = (array)R('user', 'P');
			$pass = (array)R('pass', 'P');
			$delete = (array)R('delete', 'P');
			$sendtype = intval(R('sendtype', 'P'));
			$smtplist = array();
			foreach($email as $k=>$v) {
				empty($port[$k]) && $port[$k] = 25;
				if(in_array($k, $delete)) continue;
				if(empty($email[$k]) || empty($host[$k]) || empty($user[$k])) continue;
				$smtplist[$k] = array('email'=>$email[$k], 'host'=>$host[$k], 'port'=>$port[$k], 'user'=>$user[$k], 'pass'=>$pass[$k]);
			}
			
			$this->kv->set('mail_cfg', $mailconf);

			$this->message(1,'修改邮箱设置成功！','index.php?u=setting-email');
		}
	}
	
	//邮件测试
	public function testemail(){
		$email = R('email','P');
		if( empty($email) || !check::is_email($email) )	$this->message(0,'邮箱格式不正确！');
		$mmiscModel = &$this->mmisc;
		$errMsg = $mmiscModel->sendmail('',$email,'邮件测试标题','邮件测试内容');
		if( empty($errMsg) ){
			$this->message(1,'邮件发送成功！');
		}else{
			$this->message(0,'邮件发送失败！'.$errMsg);
		}
	}

	// hook admin_setting_control_after.php
}
