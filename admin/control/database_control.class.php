<?php
// +----------------------------------------------------------------------
// | MOLYCMS	数据库备份还原
// +----------------------------------------------------------------------
//

class database_control extends admin_control {
	public $tablepre = 'moly_';	//表前缀
	public $tables;
	public $backupdir;
	
	public function __construct(){
		parent::__construct();
		
		$this->tablepre = $this->db->tablepre;
		
		$this->backupdir = MOLYCMS_PATH.'backup'.DIRECTORY_SEPARATOR;
	}
	
	//数据表列表
	public function index(){
		$sql = "SHOW TABLE STATUS LIKE '".$this->tablepre."%'";
		$dataList = $this->db->fetch_all($sql);
			
		$total = 0;
		foreach ($dataList as &$row){
			$total += $row['Data_length'];
			$row['Data_free'] = get_byte($row['Data_free']);
			$row['Data_length'] = get_byte($row['Data_length']);
		}
		$total = get_byte($total);
		
		$this->assign('totalSize', $total);
		$this->assign("list", $dataList);
		$this->display();
	}
	
	//表相关操作
	public function docommand(){
		$tables = R('tables','P');
		
		$do= strtoupper( trim(R('do','G')) );
		if(empty($tables)) $this->message(0,"请选择数据表");
		if($do=='SHOW'){	//表结构
			$data = array();
			foreach ((array)$tables as $t){
				$data[$t] = $this->db->fetch_all("SHOW COLUMNS FROM {$t}");
			}
			
			$this->assign('list',$data);
			$this->display('database_show.htm');
		}else{
			$tables = implode(',',$tables);
			$r = $this->db->fetch_all($do.' TABLE '.$tables);
			if(false != $r){
				$this->message(1,"数据表操作成功！执行命令：".$do);
			}else{
				$this->message(0,"数据表操作失败！执行命令：".$do);
			}
		}
	}
	
	//数据库备份
	public function backup(){
		$this->display();
	}
	
	public function dobackup(){
		$size =  R('size') ;
		$size = empty($size) ? 2 :intval($size);
			
		$volume = R('volume');
		$volume = empty( $volume ) ? 1 : intval( $volume );
		$startfrom = intval( R('startfrom') );
		$tableid = intval( R('tableid'));
			
		$filename = R('filename');
		$filename = empty( $filename ) ? date('YmdHis_').'molycms' : str_preg($filename);
			
		$sqldump = "# Identify: ".base64_encode(time().','.$volume)."\n".
				"# <?exit();?>\n".
				"# Molycms Multi-Volume Data Dump Vol.$volume\n".
				"# Version: Molycms ".C('version')."\n".
				"# Time: ".date('Y-m-d H:i')."\n".
				"# --------------------------------------------------------\n\n";
			
		$offset = 300;
		$sizelimit = $size * 1000000;
			
		$backupdir = $this->backupdir.$filename.DIRECTORY_SEPARATOR;
		$cachefile = $backupdir.'cachetableall.php';
		if($volume == 1 && file_exists($cachefile)) unlink($cachefile);
		if(file_exists($cachefile)) {
			$this->tables = include $cachefile;
		}else{
			$this->getTableAll();
		}
			
		$tablecount = count($this->tables);
		if($volume == 1) $sqldump .= $this->getTableStr();

		$this->db->query("SET NAMES utf8");
		while($tableid < $tablecount && strlen($sqldump) < $sizelimit) {
			$table = $this->tables[$tableid];
			$numrows = $offset;
			while(strlen($sqldump) < $sizelimit && $numrows == $offset) {
				$firstfield = $this->db->fetch_first('SHOW FULL COLUMNS FROM '.$table);
					
				if($firstfield['Extra'] == 'auto_increment') {
					$selectsql = 'SELECT * FROM '.$table.' WHERE '.$firstfield['Field'].'>'.$startfrom.' ORDER BY '.$firstfield['Field'].' LIMIT '.$offset;
				} else {
					$selectsql = 'SELECT * FROM '.$table.' LIMIT '.$startfrom.','.$offset;
				}
					
				$rows = $this->db->query($selectsql);
				$numrows = $this->db->num_rows($rows);
				while($row = $this->db->fetch_row($rows)) {
					$comma = '';
					$sqldump .= 'INSERT INTO '.$table.' VALUES(';
					foreach($row as $val) {
						$sqldump .= $comma.$this->db->S($val);
						$comma = ',';
					}
					$sqldump .= ");\n";
						
					if($firstfield['Extra'] == 'auto_increment') {
						$startfrom = $row[0];
					}else{
						$startfrom++;
					}
						
					if(strlen($sqldump) > $sizelimit) {
						break 3;
					}
				}
			}
			$sqldump .= "\n";
			if($numrows != $offset) {
				$startfrom = 0;
				$tableid++;
			}
		}
			
		$msg = '';
		$url = 'index.php?u=database-backup';
		$status = 0;
		if(!_mkdir($backupdir)) {
			$msg = '创建文件夹失败,请检查目录属性';
		}elseif(!file_exists($cachefile) && !FW($cachefile, '<?php return '.var_export($this->tables, true).';')) {
			$msg = '写入缓存文件失败,请检查目录属性';
		}elseif(!FW($backupdir.$volume.'.sql', $sqldump)) {
			$msg = '写入文件失败,请检查目录属性';
		}else{
			if($tableid < $tablecount) {
				$msg = '数据文件 '.$volume.'.sql 创建成功';
				$url =  'index.php?u=database-dobackup&size='.$size.'&volume='.++$volume.'&startfrom='.$startfrom.'&tableid='.$tableid.'&filename='.$filename;
				$status = 1;
			}else{
				unlink($cachefile);
				$msg = '备份完成！';
				$status = 1;
			}
		}
		
		$this->message($status, $msg,$url);
	}
	
	//还原
	public function recove(){
		$backupdir = $this->backupdir;
		$fileArr = array();
		if(is_dir($backupdir)) {
			if($dh = opendir($backupdir)) {
				while (($dir = readdir($dh)) !== false) {
					
					$sqldir = $backupdir.DIRECTORY_SEPARATOR.$dir;
					
					if(filetype($sqldir)=='dir' && $identify = $this->getIdentify($sqldir.DIRECTORY_SEPARATOR.'1.sql')) {
						$volumes = $sqlsize = 0;
						if($dh2 = opendir($sqldir)) {
							while (($file = readdir($dh2)) !== false) {
								if(filetype($sqldir.DIRECTORY_SEPARATOR.$file)=='file' && strrchr($file, '.')=='.sql') {
									$sqlsize += filesize($sqldir.DIRECTORY_SEPARATOR.$file);
									$volumes++;
								}
							}
							closedir($dh2);
						}
						$fileArr[] = array('name'=>$dir, 'createtime'=>date('Y-m-d H:i:s', $identify[0]), 'version'=>$identify[1], 'sqlsize'=>get_byte($sqlsize), 'volumes'=>$volumes);
					}
				}
				closedir($dh);
			}
		}
		
		$this->assign('list', $fileArr);
		$this->display();
	}
	
	/**
	//还原
	public function dorecove(){
		$sqlname = R('sqlname');
		$volume = R('volume');
		$volume = intval($volume) == 0 ? 1:intval($volume);
	
		$volumes = intval( R('volumes') );
	
		$sqlfile = $this->backupdir.$sqlname.DIRECTORY_SEPARATOR.$volume.'.sql';
	
		$status = 0;
		$msg = '';
		$url = 'index.php?u=database-recove';
		if(empty($sqlname) || preg_match('/\W/', $sqlname)) {
			$msg = '非法文件名';
		}elseif(!file_exists($sqlfile)) {
			$msg = '还原失败, '.$volume.'.sql 文件不存在';
		}elseif(!$identify = $this->getIdentify($sqlfile)) {
			$msg = '读取备份文件失败';
		}else{
			$sqlquery = splitsql(f_read($sqlfile));
	
			$querytrue = $queryfalse = 0;
			foreach($sqlquery as $sql) {
				if(trim($sql) != '') $this->db->query($sql) ? $querytrue++ : $queryfalse++;
			}
	
			if($volume < $volumes) {
				$msg = '还原 '.$volume.'.sql 完成 (ok:'.$querytrue.($queryfalse ? ' <font color=\'red\'>err:'.$queryfalse.'</font>' : '').')';
				$status = 1;
				$url = 'index.php?u=database-dorecove&sqlname='.$sqlname.'&volumes='.$volumes.'&volume='.++$volume;
			}else{
				$msg = '还原完成！';
				$status = 1;
			}
		}
		$this->message($status, $msg, $url);
	}
	*/
	//下载备份文件
	public function down(){
		$sqlname = R('sqlname','G');
		$filedir = $this->backupdir.$sqlname;
		$zipfile =  $filedir.'.zip';
		
		$cfg = $this->runtime->xget();
		if(empty($sqlname) || preg_match('/\W/', $sqlname)) {
			$this->message(0, '非法文件名');
		}elseif( file_exists($zipfile) ) {	
			$url = $cfg['webdir'].str_replace(MOLYCMS_PATH, '', $zipfile);
			$url = str_replace('\\','/',$url);
			
			@header('location:'.$url);
		}else{
			if(function_exists('gzcompress')) {
				if( kp_zip::zip($filedir, $zipfile) ) {
					$url = $cfg['webdir'].str_replace(MOLYCMS_PATH, '', $zipfile);
					$url = str_replace('\\','/',$url);
					
					@header('location:'.$url);
				}else{
					$this->message(0, '压缩为zip文件失败，请使用FTP下载吧');
				}
			}else{
				$this->message(0, '服务器不支持压缩功能，请使用FTP下载吧');
			}
		}
	}
	
	//删除备份文件
	function del() {
		$sqlname = R('sqlname','G');
		$filedir = $this->backupdir.$sqlname;
		
		if(empty($sqlname) || preg_match('/\W/', $sqlname)) {
			$msg = '非法文件名';
			$status = 0;
		}else{
			set_time_limit(0);
			if( _rmdir($filedir) ) {
				$msg = '删除完成';
				$status = 1;
			}else{
				$msg = '删除失败';
				$status = 0;
			}
		}
		$this->message($status, $msg,'index.php?u=database-recove');
	}
	
	//获取鉴定信息
	private function getIdentify($file) {
		if(!is_file($file)){
			return false;
		}
		if(@$fp = fopen($file, 'rb')) {
			$Identify = explode(',', base64_decode(preg_replace("/^# Identify: (\w+).*/s", "\\1", fgets($fp, 256))));
			fclose($fp);
			return $Identify;
		}
		return false;
	}
	
	//获取所有表名
	private function getTableAll() {
		$arr = array();
		$query = $this->db->query('SHOW TABLE STATUS LIKE "'.str_replace('_', '\_', addslashes($this->tablepre)).'%"');
		while($data = $this->db->fetch_array($query)) {
			$arr[] = $data['Name'];
		}
		$this->tables = $arr;
	}
	
	//获取所有表名创建SQL
	private function getTableStr() {
		$sqldump = '';
		$this->db->query('SET SQL_QUOTE_SHOW_CREATE=0');
		foreach($this->tables as $table) {
			$query = $this->db->query('SHOW CREATE TABLE '.$table);
			$data = $this->db->fetch_row($query);
			$data[1] = preg_replace("/ AUTO_INCREMENT=\d+/", '', $data[1]);
			if($this->db->version() >= '4.1') $data[1] = preg_replace("/CHARSET=.+/", 'CHARSET=utf8', $data[1]);
			else $data[1] = preg_replace("/TYPE\=(.+)/", 'ENGINE=\\1 DEFAULT CHARSET=utf8', $data[1]);
	
			$query = $this->db->query("SHOW TABLE STATUS LIKE '$table'");
			$tablestatus = $this->db->fetch_array($query);
			$sqldump .= 'DROP TABLE IF EXISTS '.$table.";\n".$data[1].($tablestatus['Auto_increment'] ? " AUTO_INCREMENT=$tablestatus[Auto_increment]" : '').";\n\n";
		}
		return $sqldump;
	}
}
