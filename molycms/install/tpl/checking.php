<?php
class Checking{
	private $config=array(
			'version'=>'4.0.0',
			'writable'=>array(),
			'ext'=>array(),
			'ini'=>array()
		);
	private $passed = true;
	public function __construct($config=array()){
		$this->config = array_merge($this->config,$config);
	}

	//检查php版本
	public function isVersion()
	{
		$flag = version_compare(PHP_VERSION, $this->config['version'], '>=');
		if(!$flag) $this->passed = false;
		return $flag;
	}

	//检测目录与文件是否可写
	public function isWritable(){
		$writable = array();
		foreach ($this->config['writable'] as $file) {
			 if(_dir_write(TWCMS_ROOT.$file)){
			 	$writable[$file] = true;
			 }
			 else{
			 	$this->passed = false;
			 	$writable[$file] = false;
			 }
		}
		return $writable;
	}
	//检测扩展
	public function isExt(){
		$temp = array();
		$exts=(array_change_key_case(array_flip(get_loaded_extensions()), CASE_LOWER));
		foreach ($this->config['ext'] as $ext) {
			$ext = strtolower($ext);
			if(isset($exts[$ext])) $temp[$ext]=true;
			else{
				$temp[$ext] = false;
				$this->passed = false;
			}
		}
		return $temp;
	}
	//检测php环境配制 
	public function isIni(){
		$return = array();
		foreach($this->config['ini'] as $key => $val)
		{
			$localIni = @ini_get($key);
			if($localIni == $val)
			{
				$return[$key] = true;
			}
			else
			{
				$return[$key] = false;
				$this->passed = false;
			}
		}
		return $return;
	}

	public function check(){
		$return['version'] = $this->isVersion();
		$return['writable'] = $this->isWritable();
		$return['ext'] = $this->isExt();
		$return['ini'] = $this->isIni();
		$return['passed'] = $this->passed;
		return $return;
	}
		/**
	 * 文件或目录权限检查函数
	 *
	 * @access          public
	 * @param           string  $file_path   文件路径
	 * @param           bool    $rename_prv  是否在检查修改权限时检查执行rename()函数的权限
	 *
	 * @return          int     返回值的取值范围为{0 <= x <= 15}，每个值表示的含义可由四位二进制数组合推出。1111
	 *                  返回值在二进制计数法中，四位由高到低分别代表
	 *                  可执行rename()函数权限、可对文件追加内容权限、可写入文件权限、可读取文件权限。
	 */
	public function file_mode_info($file_path)
	{
	    /* 如果不存在，则不可读、不可写、不可改 */
	    if (!file_exists($file_path))
	    {
	        return false;
	    }
	    $mark = 0;
	    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
	    {
	        /* 测试文件 */
	        $test_file = $file_path . '/maybe_test.txt';

	        /* 如果是目录 */
	        if (is_dir($file_path))
	        {
	            /* 检查目录是否可读 */
	            $dir = @opendir($file_path);
	            if ($dir === false)
	            {
	                return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
	            }
	            if (@readdir($dir) !== false)
	            {
	                $mark ^= 1; //目录可读 001，目录不可读 000
	            }
	            @closedir($dir);

	            /* 检查目录是否可写 */
	            $fp = @fopen($test_file, 'wb');
	            if ($fp === false)
	            {
	                return $mark; //如果目录中的文件创建失败，返回不可写。
	            }
	            if (@fwrite($fp, 'directory access testing.') !== false)
	            {
	                $mark ^= 2; //目录可写可读011，目录可写不可读 010
	            }
	            @fclose($fp);

	            @unlink($test_file);

	            /* 检查目录是否可修改 */
	            $fp = @fopen($test_file, 'ab+');
	            if ($fp === false)
	            {
	                return $mark;
	            }
	            if (@fwrite($fp, "modify test.\r\n") !== false)
	            {
	                $mark ^= 4;
	            }
	            @fclose($fp);

	            /* 检查目录下是否有执行rename()函数的权限 */
	            if (@rename($test_file, $test_file) !== false)
	            {
	                $mark ^= 8;
	            }
	            @unlink($test_file);
	        }
	        /* 如果是文件 */
	        elseif (is_file($file_path))
	        {
	            /* 以读方式打开 */
	            $fp = @fopen($file_path, 'rb');
	            if ($fp)
	            {
	                $mark ^= 1; //可读 001
	            }
	            @fclose($fp);

	            /* 试着修改文件 */
	            $fp = @fopen($file_path, 'ab+');
	            if ($fp && @fwrite($fp, '') !== false)
	            {
	                $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
	            }
	            @fclose($fp);

	            /* 检查目录下是否有执行rename()函数的权限 */
	            if (@rename($test_file, $test_file) !== false)
	            {
	                $mark ^= 8;
	            }
	        }
	    }
	    else
	    {
	        if (@is_readable($file_path))
	        {
	            $mark ^= 1;
	        }

	        if (@is_writable($file_path))
	        {
	            $mark ^= 14;
	        }
	    }

	    return $mark;
	}
}