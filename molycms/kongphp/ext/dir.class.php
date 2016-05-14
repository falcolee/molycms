<?php
class dir{
	public static function dir_list($path, $exts = '', $list= array()) {
		$path = self::dir_path($path);
		$files = glob($path.'*');
		foreach($files as $v) {
			$fileext = self::fileext($v);
			if (!$exts || preg_match("/\.($exts)/i", $v)) {
				$list[] = $v;
				if (is_dir($v)) {
					$list = dir_list($v, $exts, $list);
				}
			}
		}
		return $list;
	}
	
	//文件夹相关函数
	public static function dir_path($path) {
		$path = str_replace('\\', '/', $path);
		if(substr($path, -1) != '/') $path = $path.'/';
		return $path;
	}
	
	//文件后缀
	public static function fileext($filename) {
		return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
	}
}