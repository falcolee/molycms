<?php
// +----------------------------------------------------------------------
// | MOLYCMS	安装文件
// +----------------------------------------------------------------------

version_compare(PHP_VERSION, '5.2.0', '>') || die('require PHP > 5.2.0 !');

define('MOLYCMS_INST', dirname(__FILE__));
define('MOLYCMS_CORE', dirname(MOLYCMS_INST));
define('MOLYCMS_ROOT', dirname(MOLYCMS_CORE));
define('APP_NAME', basename(MOLYCMS_CORE));

error_reporting(0);
date_default_timezone_set('Asia/Shanghai');
header('Content-Type: text/html; charset=UTF-8');

$do = isset($_GET['do']) && in_array($_GET['do'], array(1, 2, 3, 4,5)) ? intval($_GET['do']) : 1;
// 保护锁
if(is_file(MOLYCMS_CORE.'/config/config.inc.php') && $do != 5 ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	include MOLYCMS_INST.'/tpl/lock.php';
	exit;
}

include MOLYCMS_CORE.'/kongphp/base/base.func.php';
include MOLYCMS_INST.'/function.php';

if($do == 1) {
	include MOLYCMS_INST.'/tpl/header.php';
	include MOLYCMS_INST.'/tpl/license.php';
	include MOLYCMS_INST.'/tpl/footer.php';
}elseif($do == 2) {
	include MOLYCMS_INST.'/tpl/header.php';
	include MOLYCMS_INST.'/tpl/check_env.php';
	include MOLYCMS_INST.'/tpl/footer.php';
}elseif($do == 3) {
	include MOLYCMS_INST.'/tpl/header.php';
	include MOLYCMS_INST.'/tpl/check_db.php';
	include MOLYCMS_INST.'/tpl/footer.php';
}elseif($do == 4) {
	include MOLYCMS_INST.'/tpl/header.php';
	echo '<div class="box"><div class="gray_box"><div id="cont" class="content"></div></div></div>';
	include MOLYCMS_INST.'/tpl/footer.php';

	if(!isset($_POST['dbhost'])) {
		js_back('<u>非法访问！</u>');
	}

	$dbhost = isset($_POST['dbhost']) ? trim($_POST['dbhost']) : '';
	$dbuser = isset($_POST['dbuser']) ? trim($_POST['dbuser']) : '';
	$dbpw = isset($_POST['dbpw']) ? trim($_POST['dbpw']) : '';
	$dbname = isset($_POST['dbname']) ? trim($_POST['dbname']) : '';
	$charset = 'UTF8';
	$tablepre = isset($_POST['dbpre']) ? trim($_POST['dbpre']) : '';
	$adm_user = isset($_POST['adm_user']) ? trim($_POST['adm_user']) : '';
	$adm_pass = isset($_POST['adm_pass']) ? trim(str_replace(' ', '', $_POST['adm_pass'])) : '';

	if(empty($dbhost)) {
		js_back('<u>数据库主机不能为空！</u>');
	}elseif(empty($dbuser)) {
		js_back('<u>数据库用户名不能为空！</u>');
	}elseif(!preg_match('/^\w+$/', $dbname)) {
		js_back('<u>数据库名不正确！</u>');
	}elseif(empty($tablepre)) {
		js_back('<u>数据库表前辍不能为空！</u>');
	}elseif(!preg_match('/^\w+$/', $tablepre)) {
		js_back('<u>数据库表前辍不正确！</u>');
	}elseif(empty($adm_user)) {
		js_back('<u>创始人用户名不能为空！</u>');
	}elseif(strlen($adm_pass) < 8) {
		js_back('<u>密码不能小于8位数！</u>');
	}
	
	if(empty($adm_user)) {
		js_back('<u>用户名不能为空哦！</u>');
	}elseif(str_replace(array("\t","\r","\n",' ','　',',','，','-','"',"'",'\\','/','&','#','*'), '', $adm_user) != $adm_user) {
		js_back('<u>用户名中含有非法字符！</u>');
	}elseif(htmlspecialchars($adm_user) != $adm_user) {
		js_back('<u>用户名中不能含有<>！</u>');
	}

	// 连接数据库
	if(!function_exists('mysql_connect')) {
		js_back('函数 mysql_connect() 不存在，请检查 php.ini 是否加载了 mysql 模块！');
	}
	$link = mysql_connect($dbhost, $dbuser, $dbpw);
	if(!$link) {
		js_back('MySQL 主机、账号或密码不正确！<br><u>'.mysql_error().'</u>');
	}

	try{
		mysql_select_db($dbname, $link);
		if(mysql_errno() == 1049) {
			mysql_query("CREATE DATABASE $dbname DEFAULT CHARACTER SET UTF8");
			if(!mysql_select_db($dbname, $link)) {
				js_back('自动创建数据库失败鸟！您的MySQL账号是否有权限创建数据库？<br><u>'.mysql_error().'</u>');
			}
		}
		// 为防止意外，让用户自己做选择
		if(empty($_POST['cover'])) {
			$query = mysql_query("SHOW TABLES FROM $dbname");
			while($row = mysql_fetch_row($query)) {
				if(preg_match("#^{$tablepre}#", $row[0])) {
					js_back('<u>发现有相同表前缀，请返回选择“覆盖安装”或“修改表前缀”。</u>');
				}
			}
		}

		// 设置编码
		mysql_query("SET names utf8, sql_mode=''");
	}catch(Exception $e) {
		js_back('<u>未知错误！</u><br><u>'.mysql_error().'</u>');
	}

	// 创建数据表
	$file = MOLYCMS_INST.'/data/mysql.sql';
	if(!is_file($file)) {
		js_back('mysql.sql 文件 <u>丢失</u>');
	}
	$s = file_get_contents($file);
	$sqls = split_sql($s, $tablepre);
	foreach($sqls as $sql) {
		$sql = str_replace("\n", '', trim($sql));
		$ret = mysql_query($sql);
		if(substr($sql, 0, 6) == 'CREATE') {
			$name = preg_replace("/CREATE TABLE ([`a-z0-9_]+) .*/is", "\\1", $sql);

			if($ret) {
				js_show('创建数据表 '.$name.' ... <i>成功</i>');
			}else{
				js_back('创建数据表 '.$name.' ... <u>失败</u> (您的数据库没有写权限？)<br><u>'.mysql_error().'</u>');
			}
		}

		if(!$ret) {
			js_back('创建数据表失败</u> (您的数据库没有权限？)<br><u>'.mysql_error().'</u>');
		}
	}

	// 创建基本数据
	$file = MOLYCMS_INST.'/data/mysql_data.sql';
	if(!is_file($file)) {
		js_back('mysql_data.sql 文件 <u>丢失</u>');
	}
	$s = file_get_contents($file);
	$sqls = split_sql($s, $tablepre);
	$ret = true;
	foreach($sqls as $sql) {
		$sql = str_replace("\n", '', trim($sql));
		mysql_query($sql) || $ret = false;
	}
	js_show('创建基本数据 ... '.($ret ? '<i>成功</i>' : '<u>失败</u>'));
	if(!$ret) exit;

	// 创建创始人
	$salt = random(16, 3, '0123456789abcdefghijklmnopqrstuvwxyz~!@#$%^&*()_+<>,.'); // 增加破解难度
	$password = get_password($adm_pass,$salt);
	$ip = ip(1);
	$time = time();
	
	$ret = mysql_query("INSERT INTO `{$tablepre}user` (`uid`, `username`, `password`, `salt`, `groupid`, `email`, `regip`, `regdate`, `loginip`, `logindate`, `lastip`, `lastdate`, `contents`, `comments`, `logins`,`author`,`status`,`isadmin`) VALUES (1, '{$adm_user}', '{$password}', '{$salt}', 1, '', {$ip}, {$time}, 0, 0, 0, 0, 0, 0, 0,'{$adm_user}',1,1);");
	js_show('创建创始人 ... '.($ret ? '<i>成功</i>' : '<u>失败</u>'));
	if(!$ret) exit;
	
	$ret = mysql_query("INSERT INTO `{$tablepre}user_data` (`uid`,`homepage`) VALUES (1,'http://#');");
	js_show('创建创始人详细信息 ... '.($ret ? '<i>成功</i>' : '<u>失败</u>'));
	if(!$ret) exit;

	// 初始网站设置
	$webdomain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	$webdir = get_webdir();
	$weburl = 'http://'.$webdomain.$webdir;
	$cfg = array(
		'webname' => 'MOLYCMS',
		'webdomain' => $webdomain,
		'webdir' => $webdir,
		'webmail' => 'admin@molycms.com',
		'tongji' => '<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id=\'cnzz_stat_icon_1253619239\'%3E%3C/span%3E%3Cscript src=\'" + cnzz_protocol + "s95.cnzz.com/stat.php%3Fid%3D1253619239\' type=\'text/javascript\'%3E%3C/script%3E"));</script>',
		'beian' => '鄂ICP备14018391号-1',
		'dis_comment'=>0,//关闭全站评论
		'user_comment'=>0,//登录才能评论
		'comment_filter'=>'',//评论敏感词
		'footer_info'=>'Power by Molycms 版权所有',		
		'seo_title' => '网站建设利器！',
		'seo_keywords' => 'MOLYCMS',
		'seo_description' => 'MOLYCMS，网站建设利器！',

		'link_show' => '{cate_alias}/{id}.html',
		'link_show_type' => 2,
		'link_show_end' => '.html',
		'link_cate_page_pre' => '/page_',
		'link_cate_page_end' => '.html',
		'link_cate_end' => '/',
		'link_tag_pre' => 'tag/',
		'link_tag_end' => '.html',
		'link_comment_pre' => 'comment/',
		'link_comment_end' => '.html',
		'link_index_end' => '.html',

		'up_img_ext' => 'jpg,jpeg,gif,png',
		'up_img_max_size' => '3074',
		'up_file_ext' => 'zip,gz,rar,iso,xsl,doc,ppt,wps',
		'up_file_max_size' => '10240',
		'thumb_article_w' => 150,
		'thumb_article_h' => 150,
		'thumb_product_w' => 150,
		'thumb_product_h' => 150,
		'thumb_photo_w' => 150,
		'thumb_photo_h' => 150,
		'thumb_type' => 2,
		'thumb_quality' => 90,
		'watermark_pos' => 9,
		'watermark_pct' => 90,
	);
	$settings = addslashes(json_encode($cfg));
	$ret = mysql_query("INSERT INTO {$tablepre}kv SET k='cfg',v='{$settings}',expiry='0'");
	
	js_show('初始网站设置 ... '.($ret ? '<i>成功</i>' : '<u>失败</u>'));
	if(!$ret) exit;
	
	$user_cfg = array(
			'open_user_model' => 0,	//关闭用户模块
			'open_user_reg' => 0,	//关闭用户注册
			'user_active_method' => 0,	//直接激活用户
			'email_active_content' => '',	//邮箱激活内容模板
			'email_pwd_content' => '',	//邮箱找回密码模板
	);
	$usersettings = addslashes(json_encode($user_cfg));
	$ret = mysql_query("INSERT INTO {$tablepre}kv SET k='user_cfg',v='{$usersettings}',expiry='0'");
	
	js_show('初始网站用户模块设置 ... '.($ret ? '<i>成功</i>' : '<u>失败</u>'));
	if(!$ret) exit;

	// 清空缓存
	$runtime = MOLYCMS_CORE.'/runtime/';
	$file = $runtime.'_runtime.php';
	if(is_file($file)) {
		$ret = unlink($runtime.'_runtime.php');
		js_show('清除 runtime/_runtime.php ... <i>完成</i>');
	}
	$tpmdir = array('_control', '_model', '_view');
	foreach($tpmdir as $dir) {
		$ret = _rmdir($runtime.'molycms'.$dir);
		js_show('清除 runtime/molycms'.$dir.' ... <i>完成</i>');
	}
	foreach($tpmdir as $dir) {
		if($dir == '_model') continue;
		$ret = _rmdir($runtime.'molycms_admin'.$dir);
		js_show('清除 runtime/molycms_admin'.$dir.' ... <i>完成</i>');
	}

	// 初始插件配置
	$file = MOLYCMS_INST.'/plugin.sample.php';
	if(!is_file($file)) {
		js_back('plugin.sample.php 文件 <u>丢失</u>');
	}
	$ret = file_put_contents(MOLYCMS_CORE.'/config/plugin.inc.php', file_get_contents($file));
	js_show('设置 config/plugin.inc.php ... '.($ret ? '<i>成功</i>' : '<u>失败</u>'));
	if(!$ret) exit;

	// 生成配置文件
	$file = MOLYCMS_INST.'/config.sample.php';
	if(!is_file($file)) {
		js_back('config.sample.php 文件 <u>丢失</u>');
	}
	$auth_key = random(32, 3);
	$cookie_pre = 'molycms'.random(5, 3).'_';

	$s = file_get_contents($file);
	$s = preg_replace("#'auth_key' => '\w*',#", "'auth_key' => '".addslashes($auth_key)."',", $s);
	$s = preg_replace("#'cookie_pre' => '\w*',#", "'cookie_pre' => '".addslashes($cookie_pre)."',", $s);
	$s = preg_replace("#'host' => '\w*',#", "'host' => '".addslashes($dbhost)."',", $s);
	$s = preg_replace("#'user' => '\w*',#", "'user' => '".addslashes($dbuser)."',", $s);
	$s = preg_replace("#'password' => '\w*',#", "'password' => '".addslashes($dbpw)."',", $s);
	$s = preg_replace("#'name' => '\w*',#", "'name' => '".addslashes($dbname)."',", $s);
	$s = preg_replace("#'tablepre' => '\w*',#", "'tablepre' => '".addslashes($tablepre)."',", $s);
	$s = preg_replace("#'pre' => '\w*',#", "'pre' => '".addslashes($tablepre)."',", $s);

	$ret = file_put_contents(MOLYCMS_CORE.'/config/config.inc.php', $s);
	js_show('设置 config/config.inc.php ... '.($ret ? '<i>成功</i>' : '<u>失败</u>'));
	if(!$ret) exit;

	echo '<script type="text/javascript">window.location.href="index.php?do=5"</script>';
	exit;
}elseif($do == 5) {
	$webdomain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	$webdir = get_webdir();
	$weburl = 'http://'.$webdomain.$webdir;
	// 安装结束提示
	$s = '<div class="end"><h3>恭喜！您的网站已安装完成啦！</h3><p>';
	$s .= '首页地址：<a href="'.$weburl.'" >'.$weburl.'</a><br>';
	$s .= '后台地址：<a href="'.$weburl.'admin/" >'.$weburl.'admin/</a><br>';
	$s .= '亲，请牢记以上信息，您可以登陆后台修改密码及网站设置。^_^</p></div>';
	
	include MOLYCMS_INST.'/tpl/header.php';
	include MOLYCMS_INST.'/tpl/complete.php';
	include MOLYCMS_INST.'/tpl/footer.php';
	
	exit;
}