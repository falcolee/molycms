<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台入口
// +----------------------------------------------------------------------

define('DEBUG', 0);	//调试模式，分三种：0 关闭调试; 1 开启调试; 2 开发调试   注意：开启调试会暴露绝对路径和表前缀
define('APP_NAME', 'molycms');	//APP名称
define('MOLYCMS_PATH', getcwd().'/');	//MOLYCMS目录
define('APP_PATH', MOLYCMS_PATH.APP_NAME.'/');	//APP目录
if(!is_file(APP_PATH.'config/config.inc.php')) exit('<html><body><script>location="'.APP_NAME.'/install/'.'"</script></body></html>');
define('KONG_PATH', APP_PATH.'kongphp/');	//框架目录
require KONG_PATH.'kongphp.php';
