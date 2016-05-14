<?php

// 统计程序运行时间
function runtime() {
	return number_format(microtime(1) - $_ENV['_start_time'], 4);
}

// 统计程序内存开销
function runmem() {
	return MEMORY_LIMIT_ON ? get_byte(memory_get_usage() - $_ENV['_start_memory']) : 'unknown';
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
 * @return mixed
 */
function ip($type = 0,$adv=false) {
	$type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

// 返回消息JSON (注意：不要含有 " \ 等之类破坏 JSON 结构的字符)
function E($err, $msg, $name = '') {
	exit('{"err":'.$err.', "msg":"'.$msg.'", "name":"'.$name.'"}');
}

/**
 * 无Notice快捷取变量 (Request 的缩写)
 * @param string $k 键值
 * @param string $var 类型 GET|POST|COOKIE|REQUEST|SERVER
 * @return mixed
 */
function R($k, $var = 'G') {
	switch($var) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		case 'R': $var = isset($_GET[$k]) ? $_GET : (isset($_POST[$k]) ? $_POST : $_COOKIE); break;
		case 'S': $var = &$_SERVER; break;
	}
	return isset($var[$k]) ? $var[$k] : null;
}

/**
 * 读取/设置 配置信息 (Config 的缩写)
 * @param string $key 键值
 * @param string $val 设置值
 * @return mixed
 */
function C($key, $val = null) {
	if(is_null($val)) return isset($_ENV['_config'][$key]) ? $_ENV['_config'][$key] : $val;
	return $_ENV['_config'][$key] = $val;
}

/**
 * 具有递归自动创建文件夹和写入文件数据的功能 (File Write 的缩写)
 * @param string filename 要被写入数据的文件名
 * @param string $data 要写入的数据
 * @return boot
 */
function FW($filename, $data) {
	$dir = dirname($filename);
	// 目录不存在则创建
	is_dir($dir) || mkdir($dir, 0755, true);

	return @file_put_contents($filename, $data);	// 不使用 LOCK_EX，多线程访问时会有同步问题
}

/**
 * 读取文件
 * @param string $file 文件路径
 */
function f_read($file) {
	if(function_exists('file_get_contents')) return file_get_contents($file);

	$ft=fopen($file,"rb");
	$str='';
	while (!feof($ft)) {
		$str.=fread($ft,4096);
	}
	fclose($ft);
	return $str;
}

/**
 * 写入文件
 * @param string $file 文件路径
 * @param string $str 写入字符串
 */
function f_write($file, $str) {
	if(function_exists('file_put_contents')) return file_put_contents($file,$str,LOCK_EX);

	$ft=fopen($file,'wb');
	flock($ft,LOCK_UN);
	flock($ft,LOCK_EX|LOCK_NB);
	$rn=fwrite($ft,$str);
	flock($ft,LOCK_UN);
	fclose($ft);
	return $rn;
}

#分割SQL语句
function splitsql($sql) {
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$ret[$num] = isset($ret[$num]) ? $ret[$num] : '';
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= isset($query[0]) && $query[0] == "#" ? NULL : $query;
		}
		$num++;
	}
	return $ret;
}

/**
 * 转换文件扩展名
 * @param string $ext “,”分割的扩展名
 */
function toExt($ext) {
	$Arr = explode(',', $ext);
	$ret = array();
	foreach($Arr as $val) {
		$val = trim($val);
		if($val) $ret[] = $val;
	}
	return '*.'.implode(';*.', $ret);
}

// 方便记忆 以 _ 开始的都是改造系统函数
// cookie 设置/删除
function _setcookie($name, $value='', $expire=0, $path='', $domain='', $secure=false, $httponly=false) {
	$name = $_ENV['_config']['cookie_pre'].$name;
	if(!$path) $path = $_ENV['_config']['cookie_path'];
	if(!$domain) $domain = $_ENV['_config']['cookie_domain'];
	$_COOKIE[$name] = $value;
	return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

#正则清理非数字字符串
function int_preg($str) {
	return preg_replace('/\D/','',$str);
}

#正则清理非字母,数字,_的字符串
function str_preg($str) {
	return preg_replace('/\W/','',$str);
}

// 递归加反斜线
function _addslashes(&$var) {
	if(is_array($var)) {
		foreach($var as $k=>&$v) _addslashes($v);
	}else{
		$var = addslashes($var);
	}
}

// 递归清理反斜线
function _stripslashes(&$var) {
	if(is_array($var)) {
		foreach($var as $k=>&$v) _stripslashes($v);
	}else{
		$var = stripslashes($var);
	}
}

// 递归转换为HTML实体代码
function _htmls(&$var) {
	if(is_array($var)) {
		foreach($var as $k=>&$v) _htmls($v);
	}else{
		$var = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $var);
	}
}

// 递归清理两端空白字符
function _trim(&$var) {
	if(is_array($var)) {
		foreach($var as $k=>&$v) _trim($v);
	}else{
		$var = trim($var);
	}
}

// 编码 URL 字符串
function _urlencode($s) {
	return str_replace('-', '%2D', urlencode($s));
}

// 对 JSON 格式的字符串进行解码
function _json_decode($s) {
	return $s === FALSE ? FALSE : json_decode($s, true);
}

// 简单的数组转JSON
function _json_encode($arr) {
	if(!is_array($arr) && empty($arr)) return '';
	$s = '{';
	foreach($arr as $k=>$v) {
		$s .= '"'.$k.'":"'.strtr($v, array('\\'=>'\\\\', '"'=>'\"')).'",';
	}
	return rtrim($s, ',').'}';
}

// 增强多维数组进行排序，最多支持两个字段排序
function _array_multisort(&$data, $c_1, $c_2 = true, $a_1 = 1, $a_2 = 1) {
	if(!is_array($data)) return $data;

	$col_1 = $col_2 = array();
	foreach($data as $key => $row) {
		$col_1[$key] = $row[$c_1];
		$col_2[$key] = $c_2===true ? $key : $row[$c_2];
	}

	$asc_1 = $a_1 ? SORT_ASC : SORT_DESC;
	$asc_2 = $a_2 ? SORT_ASC : SORT_DESC;
	array_multisort($col_1, $asc_1, $col_2, $asc_2, $data);

	return $data;
}

// 返回安全整数
function _int(&$c, $k, $v = 0) {
	if(isset($c[$k])) {
		$i = intval($c[$k]);
		return $i ? $i : $v;
	}else{
		return $v;
	}
}

// 列出文件和目录
function _scandir($dir) {
	if(function_exists('scandir')) return scandir($dir);	// 有些服务器禁用了scandir
	$dh = opendir($dir);
	$arr = array();
	while($file = readdir($dh)) {
		if($file == '.' || $file == '..') continue;
		$arr[] = $file;
	}
	closedir($dh);
	return $arr;
}

#递归创建目录
function _mkdir($dir, $isindex = true, $mode = 0777){
	if(is_dir($dir)) return true;
	if(_mkdir(dirname($dir), $mode)) {
		if(mkdir($dir, $mode)) {
			if($isindex) {
				touch($dir.'/index.htm');
				chmod($dir.'/index.htm', 0777);
			}
			return true;
		}
	}
	return false;
}

// 递归删除目录
function _rmdir($dir, $keepdir = 0) {
	if(!is_dir($dir) || $dir == '/' || $dir == '../') return FALSE;	// 避免意外删除整站数据
	$files = _scandir($dir);
	foreach($files as $file) {
		if($file == '.' || $file == '..') continue;
		$filepath = $dir.'/'.$file;
		if(!is_dir($filepath)) {
			try{unlink($filepath);}catch(Exception $e){}
		}else{
			_rmdir($filepath);
		}
	}
	if(!$keepdir) try{rmdir($dir);}catch(Exception $e){}
	return TRUE;
}

// 检测文件或目录是否可写 (兼容 windows)
function _is_writable($file) {
	try{
		if(is_dir($file)) {
			$tmpfile = $file.'/_test.tmp';
			$n = @file_put_contents($tmpfile, 'test');
			if($n > 0) {
				unlink($tmpfile);
				return TRUE;
			}else{
				return FALSE;
			}
		}elseif(is_file($file)) {
			if(strpos(strtoupper(PHP_OS), 'WIN') !== FALSE) {
				$fp = @fopen($file, 'a'); // 写入方式打开，将文件指针指向文件末尾。如果文件不存在则尝试创建之。
				@fclose($fp);
				return (bool)$fp;
			}else{
				return is_writable($file);
			}
		}
	}catch(Exception $e) {}
	return FALSE;
}

// 清理PHP代码中的空格和注释
function _strip_whitespace($content) {
	$tokens = token_get_all($content);
	$last = FALSE;
	$s = '';
	for($i = 0, $j = count($tokens); $i < $j; $i++) {
		if(is_string($tokens[$i])) {
			$last = FALSE;
			$s .= $tokens[$i];
		}else{
			switch($tokens[$i][0]) {
				case T_COMMENT: //清理PHP注释
				case T_DOC_COMMENT:
					break;
				case T_WHITESPACE: //清理多余空格
					if(!$last) {
						$s .= ' ';
						$last = TRUE;
					}
					break;
				case T_START_HEREDOC:
					$s .= "<<<KONG\n";
					break;
				case T_END_HEREDOC: // 修正 HEREDOC
					$s .= "KONG;\n";
					for($k = $i+1; $k < $j; $k++) {
						if(is_string($tokens[$k]) && $tokens[$k] == ';') {
							$i = $k;
							break;
						}elseif($tokens[$k][0] == T_CLOSE_TAG) {
							break;
						}
					}
					break;
				default:
					$last = FALSE;
					$s .= $tokens[$i][1];
			}
		}
	}
	return $s;
}

/**
 * 产生随机字符串
 * @param int	$length	输出长度
 * @param int	$type	输出类型 1为数字 2为a1 3为Aa1
 * @param string	$chars	随机字符 可自定义
 * @return string
 */
function random($length, $type = 1, $chars = '0123456789abcdefghijklmnopqrstuvwxyz') {
	if($type == 1) {
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		if($type == 3) $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) $hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

/**
 * 获取数据大小单位
 * @param int $byte 字节
 * @return string
 */
function get_byte($byte) {
	if($byte < 1024) {
		return $byte.' Byte';
	}elseif($byte < 1048576) {
		return round($byte/1024, 2).' KB';
	}elseif($byte < 1073741824) {
		return round($byte/1048576, 2).' MB';
	}elseif($byte < 1099511627776) {
		return round($byte/1073741824, 2).' GB';
	}else{
		return round($byte/1099511627776, 2).' TB';
	}
}

// 转换为人性化时间
function human_date($dateline, $dateformat = 'Y-m-d H:i:s') {
	$second = $_ENV['_time'] - $dateline;
	if($second > 31536000) {
		return date($dateformat, $dateline);
	}elseif($second > 2592000) {
		return floor($second / 2592000).'月前';
	}elseif($second > 86400) {
		return floor($second / 86400).'天前';
	}elseif($second > 3600) {
		return floor($second / 3600).'小时前';
	}elseif($second > 60) {
		return floor($second / 60).'分钟前';
	}else{
		return $second.'秒前';
	}
}

// 安全过滤 (过滤非空格、英文、数字、下划线、中文、日文、朝鲜文，其他语言通过 $ext 添加 Unicode 编码)
// 4E00-9FA5(中文)  30A0-30FF(日文片假名) 3040-309F(日文平假名) 1100-11FF(朝鲜文) 3130-318F(朝鲜文兼容字母) AC00-D7AF(朝鲜文音节)
function safe_str($s, $ext = '') {
	$ext = preg_quote($ext);
	$s = preg_replace('#[^\040\w\x{4E00}-\x{9FA5}\x{30A0}-\x{30FF}\x{3040}-\x{309F}\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}'.$ext.']+#u', '', $s);
	$s = trim($s);
	return $s;
}

// 获取下级所有目录名 （严格限制目录名只能是 数字 字母 _）
function get_dirs($path, $fullpath = false) {
	$arr = array();
	$dh = opendir($path);
	while($dir = readdir($dh)) {
		if(preg_match('#\W#', $dir) || !is_dir($path.$dir)) continue;
		$arr[] = $fullpath ? $path.$dir.'/' : $dir;
	}
	sort($arr); // 排序方式:目录名升序
	return $arr;
}

/**
 * 字符串只替换一次
 * @param string $search 查找的字符串
 * @param string $replace 替换的字符串
 * @param string $content 执行替换的字符串
 * @return string
 */
function str_replace_once($search, $replace, $content) {
	$pos = strpos($content, $search);
	if($pos === false) return $content;
	return substr_replace($content, $replace, $pos, strlen($search));
}

/**
 * 字符串加密、解密函数
 * @param string $string	字符串
 * @param string $operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE
 * @param string $key		密钥：数字、字母、下划线
 * @param string $expiry	过期时间
 * @return string
 */
function str_auth($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : C('auth_key'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		}else{
			return '';
		}
	}else{
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

// 生成 form hash
function form_hash() {
	return substr(md5(substr($_ENV['_time'], 0, -5).$_ENV['_config']['auth_key']), 16);
}

// 校验 form hash
function form_submit() {
	return R('FORM_HASH', 'P') == form_hash();
}

// 远程抓取数据
function fetch_url($url, $timeout = 30) {
	$opts = array ('http'=>array('method'=>'GET', 'timeout'=>$timeout));
	$context = stream_context_create($opts);
	$html = file_get_contents($url, false, $context);
	return $html;
}

/**
 * 分页函数
 * @param int $page 当前页
 * @param int $maxpage 最大页
 * @param string $url 完整路径
 * @param int $offset 偏移数
 * @param array $lang 上下页数组
 * @return string
 */
function pages($page, $maxpage, $url, $offset = 5, $lang = array('&#171;', '&#187;')) {
	if($maxpage < 2) return '';
	$pnum = $offset*2;
	$ismore = $maxpage > $pnum;
	$s = '';
	$ua = explode('{page}', $url);
	if($page > 1) $s .= '<a href="'.$ua[0].($page-1).$ua[1].'">'.$lang[0].'</a>';
	if($ismore) {
		$i_end = min($maxpage, max($pnum, $page+$offset)) - 1;
		$i = max(2, $i_end-$pnum+2);
	}else{
		$i_end = min($maxpage, $pnum)-1;
		$i = 2;
	}
	$s .= $page == 1 ? '<b>1</b>' : '<a href="'.$ua[0].'1'.$ua[1].'">1'.($ismore && $i > 2 ? ' ...' : '').'</a>';
	for($i; $i<=$i_end; $i++){
		$s .= $page == $i ? '<b>'.$i.'</b>' : '<a href="'.$ua[0].$i.$ua[1].'">'.$i.'</a>';
	}
	$s .= $page == $maxpage ? '<b>'.$maxpage.'</b>' : '<a href="'.$ua[0].$maxpage.$ua[1].'">'.($ismore && $i_end < $maxpage-1 ? '... ' : '').$maxpage.'</a>';
	if($page < $maxpage) $s .= '<a href="'.$ua[0].($page+1).$ua[1].'">'.$lang[1].'</a>';
	return $s;
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
	if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
		return true;
	}elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
		return true;
	}
	return false;
}

function webdomain($webdomain = ''){
	if( is_ssl() ){
		return 'https://'.$webdomain;
	}else{
		return 'http://'.$webdomain;
	}
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
	//多行URL地址支持
	$url        = str_replace(array("\n", "\r"), '', $url);
	if (empty($msg))
		$msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header('Location: ' . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	} else {
		$str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0)
			$str .= $msg;
		exit($str);
	}
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
	if (is_object($mix)) {
		return spl_object_hash($mix);
	} elseif (is_resource($mix)) {
		$mix = get_resource_type($mix) . strval($mix);
	} else {
		$mix = serialize($mix);
	}
	return md5($mix);
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
	static $_status = array(
			// Informational 1xx
			100 => 'Continue',
			101 => 'Switching Protocols',
			// Success 2xx
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			// Redirection 3xx
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily ',  // 1.1
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			// 306 is deprecated but reserved
			307 => 'Temporary Redirect',
			// Client Error 4xx
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			// Server Error 5xx
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			509 => 'Bandwidth Limit Exceeded'
	);
	if(isset($_status[$code])) {
		header('HTTP/1.1 '.$code.' '.$_status[$code]);
		// 确保FastCGI模式下正常
		header('Status:'.$code.' '.$_status[$code]);
	}
}

// 不区分大小写的in_array实现
function in_array_case($value,$array){
	return in_array(strtolower($value),array_map('strtolower',$array));
}

//汉字转拼音
function pinyin($_String) {
	$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
			"|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
			"cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
			"|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
			"|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
			"|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
			"|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
			"|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
			"|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
			"|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
			"|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
			"she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
			"tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
			"|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
			"|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
			"zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
	$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
			"|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
			"|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
			"|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
			"|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
			"|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
			"|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
			"|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
			"|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
			"|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
			"|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
			"|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
			"|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
			"|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
			"|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
			"|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
			"|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
			"|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
			"|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
			"|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
			"|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
			"|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
			"|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
			"|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
			"|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
			"|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
			"|-10270|-10262|-10260|-10256|-10254";
	$_TDataKey   = explode('|', $_DataKey);
	$_TDataValue = explode('|', $_DataValue);
	$_Data =  array_combine($_TDataKey, $_TDataValue);
	arsort($_Data);
	reset($_Data);
	$_String= auto_charset($_String,'utf-8','gbk');
	$_Res = '';
	for($i=0; $i<strlen($_String); $i++) {
		$_P = ord(substr($_String, $i, 1));
		if($_P>160) {
			$_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536;
		}
		$_Res .= _Pinyin($_P, $_Data);
	}
	return preg_replace("/[^a-z0-9]*/", '', $_Res);
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from='gbk', $to='utf-8') {
	$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
	$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
	if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
		//如果编码相同或者非字符串标量则不转换
		return $fContents;
	}
	if (is_string($fContents)) {
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($fContents, $to, $from);
		} elseif (function_exists('iconv')) {
			return iconv($from, $to, $fContents);
		} else {
			return $fContents;
		}
	} elseif (is_array($fContents)) {
		foreach ($fContents as $key => $val) {
			$_key = auto_charset($key, $from, $to);
			$fContents[$_key] = auto_charset($val, $from, $to);
			if ($key != $_key)
				unset($fContents[$key]);
		}
		return $fContents;
	}
	else {
		return $fContents;
	}
}

function _Pinyin($_Num, $_Data) {
	if    ($_Num>0      && $_Num<160   ) return chr($_Num);
	elseif($_Num<-20319 || $_Num>-10247) return '';
	else {
		foreach($_Data as $k=>$v){
			if($v<=$_Num) break;
		}
		return $k;
	}
}

// 密码生成规则
function get_password($password='',$salt=''){
	return md5(md5($password).$salt);
}

// 判断是否为手机访问
function is_mobile() {
	$is_mobile = R($_ENV['_config']['cookie_pre'].'is_mobile', 'R');
	if(isset($is_mobile)) return $is_mobile ? 1 : 0;

	$mobile_agents = array(
		'iphone','ipod','android','samsung','sony','meizu','ericsson','mot','htc','sgh','lg','sharp','sie-',
		'philips','panasonic','alcatel','lenovo','blackberry','netfront','symbian','ucweb','windowsce',
		'palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
	);

	$is_mobile = 0;
	$browser = $_SERVER['HTTP_USER_AGENT'];
	foreach($mobile_agents as $agent) {
		if(stripos($browser, $agent) !== 0) {
			$is_mobile = 1;
			break;
		}
	}
	_setcookie('is_mobile', $is_mobile);
	return $is_mobile;
}
