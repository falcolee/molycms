<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台入口
// +----------------------------------------------------------------------

define('DEBUG', 2);	//调试模式，分三种：0 关闭调试; 1 开启调试; 2 开发调试   注意：开启调试会暴露绝对路径和表前缀
define('APP_NAME', 'molycms_admin');	//APP名称
define('F_APP_NAME', 'molycms');	//前台APP名称
define('ADM_PATH', getcwd().'/');	//后台目录
define('MOLYCMS_PATH', dirname(ADM_PATH).'/');	//MOLYCMS目录
define('APP_PATH', MOLYCMS_PATH.F_APP_NAME.'/');	//APP目录
if(!is_file(APP_PATH.'config/config.inc.php')) exit('<html><body><script>location="../'.F_APP_NAME.'/install/'.'"</script></body></html>');
define('RUNTIME_MODEL', APP_PATH.'runtime/'.F_APP_NAME.'_model/');	//模型缓存目录
define('CONTROL_PATH', ADM_PATH.'control/');	//控制器目录
define('VIEW_PATH', ADM_PATH.'view/');	//视图目录
define('KONG_PATH', APP_PATH.'kongphp/');	//框架目录
define('IS_ADMIN', 1);	//是否管理后台
require KONG_PATH.'kongphp.php';
echo "\r\n<!--".number_format(microtime(1) - $_ENV['_start_time'], 4).'-->';
