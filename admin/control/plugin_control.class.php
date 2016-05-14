<?php
// +----------------------------------------------------------------------
// | MOLYCMS	插件管理
// +----------------------------------------------------------------------
//

class plugin_control extends admin_control {
	// 插件管理
	public function index() {
		$plugins = core::get_plugins();

		// 检查是否有图标和设置功能
		foreach($plugins as &$arr) {
			if(isset($arr) && is_array($arr)) {
				foreach($arr as $dir => &$v) {
					is_file(PLUGIN_PATH.$dir.'/show.jpg') && $v['is_show'] = 1;
				}
			}
		}

		$this->assign('plugins', $plugins);
		$this->display();
	}

	// 插件启用
	public function enable() {
		$dir = R('dir', 'P');
		$this->check_plugin($dir);
		$plugins = $this->get_plugin_config();
		isset($plugins[$dir]) || $this->message(0, '启用出错，插件未安装！','index.php?u=plugin-index');

		// 如果是编辑器插件，卸载其他编辑器插件
		if(substr($dir, 0, 7) == 'editor_') {
			foreach($plugins as $k => $v) {
				substr($k, 0, 7) == 'editor_' && $plugins[$k]['enable'] = 0;
			}
		}

		$plugins[$dir]['enable'] = 1;
		if($this->set_plugin_config($plugins)) {
			$this->clear_cache();
			$this->message(1, '启用完成！','index.php?u=plugin-index');
		}else{
			$this->message(0, '写入 plugin.inc.php 文件失败！','index.php?u=plugin-index');
		}
	}

	// 插件停用
	public function disabled() {
		$dir = R('dir', 'P');
		$this->check_plugin($dir);
		$plugins = $this->get_plugin_config();
		isset($plugins[$dir]) || $this->message(0, $dir.'停用出错，插件未安装！','index.php?u=plugin-index');

		$plugins[$dir]['enable'] = 0;
		if($this->set_plugin_config($plugins)) {
			$this->clear_cache();
			$this->message(1, $dir.'停用完成！','index.php?u=plugin-index');
		}else{
			$this->message(0, $dir.'写入 plugin.inc.php 文件失败！','index.php?u=plugin-index');
		}
	}

	// 插件删除
	public function delete() {
		$dir = R('dir', 'P');
		$this->check_plugin($dir);

		$plugins = $this->get_plugin_config();

		// 只允许删除停用或未安装的插件
		if(empty($plugins[$dir]['enable'])) {
			// 检测有 uninstall.php 文件，则执行卸载
			$uninstall = PLUGIN_PATH.$dir.'/uninstall.php';
			if(is_file($uninstall)) {
				include $uninstall;
			}

			if(_rmdir(PLUGIN_PATH.$dir)) {
				if(isset($plugins[$dir])) {
					unset($plugins[$dir]);
					if(!$this->set_plugin_config($plugins)) {
						$this->message(0, $dir.'写入 plugin.inc.php 文件失败！','index.php?u=plugin-index');
					}
				}
				$this->message(1, $dir.'删除完成！','index.php?u=plugin-index');
			}else{
				$this->message(0, $dir.'删除出错！','index.php?u=plugin-index');
			}
		}else{
			$this->message(0, $dir.'启用的插件不允许删除！','index.php?u=plugin-index');
		}
	}

	// 本地插件安装
	public function install() {
		$dir = R('dir', 'P');
		$this->check_plugin($dir);

		$plugins = $this->get_plugin_config();
		isset($plugins[$dir]) && E(1, '插件已经安装过！');

		$cms_version = $this->get_version($dir);
		$cms_version && version_compare($cms_version, C('version'), '>') && $this->message(0, '无法安装，最低版本要求：MOLYCMS '.$cms_version,'index.php?u=plugin-index');

		// 检测有 install.php 文件，则执行安装
		$install = PLUGIN_PATH.$dir.'/install.php';
		if(is_file($install)) include $install;

		$plugins[$dir] = array('enable' => 0);
		if(!$this->set_plugin_config($plugins)){
			$this->message(0, $dir.'写入 plugin.inc.php 文件失败！','index.php?u=plugin-index');
		}
		
		$this->message(1, $dir.'安装完成！','index.php?u=plugin-index');
	}
	
	// 检查是否为合法的插件名
	private function check_plugin($dir) {
		if(empty($dir)) {
			E(1, '插件目录名不能为空！');
		}elseif(preg_match('/\W/', $dir)) {
			E(1, '插件目录名不正确！');
		}elseif(!is_dir(PLUGIN_PATH.$dir)) {
			E(1, '插件目录名不存在！');
		}
	}

	// 检查版本
	private function get_version($dir) {
		$cfg = is_file(PLUGIN_PATH.$dir.'/conf.php') ? (array)include(PLUGIN_PATH.$dir.'/conf.php') : array();
		return isset($cfg['cms_version']) ? $cfg['cms_version'] : 0;
	}

	// 获取插件配置信息
	private function get_plugin_config() {
		return is_file(CONFIG_PATH.'plugin.inc.php') ? (array)include(CONFIG_PATH.'plugin.inc.php') : array();
	}

	// 设置插件配置信息
	private function set_plugin_config($plugins) {
		$file = CONFIG_PATH.'plugin.inc.php';
		!is_file($file) && _is_writable(dirname($file)) && file_put_contents($file, '');
		if(!_is_writable($file)) return FALSE;
		return file_put_contents($file, "<?php\nreturn ".var_export($plugins, TRUE).";\n?>");
	}
}
