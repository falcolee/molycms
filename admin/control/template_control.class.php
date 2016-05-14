<?php
// +----------------------------------------------------------------------
// | MOLYCMS	模板管理
// +----------------------------------------------------------------------
//

class template_control extends admin_control {
	public $_current_theme_path = '';	//当前选择的主题所在位置
	
	public function __construct(){
		parent::__construct();
		
		$cfg = $this->runtime->xget('cfg');
		$this->_current_theme_path = APP_PATH.'/view/'.$cfg['theme'].'/';
	}
	
	public function index() {
		$files = dir::dir_list($this->_current_theme_path,'htm');
		
		foreach ($files as $key=>$file){
			$filename = basename($file);
			$templates[$key]['value'] =  substr($filename,0,strrpos($filename, '.'));
			$templates[$key]['filename'] = $filename;
			$templates[$key]['filepath'] = $file;
			$templates[$key]['filesize'] = get_byte(filesize($file));
			$templates[$key]['filemtime'] = filemtime($file);
			$templates[$key]['ext'] = strtolower(substr($filename,strrpos($filename, '.')-strlen($filename)));
		}
		
		$this->assign ( 'list',$templates );
		$this->display();
	}
	
	//添加模板
	public function add(){
		if( empty($_POST) ){
			$this->display();
		}else{
			$info = R('info','P');
			if( $info['filename'] == '' )	$this->message(0, '添加模板失败：文件名为空！');
			
			$content = $info['content'];
			if( $content && get_magic_quotes_gpc() ){
				$info['content'] = stripslashes($content);
			}
			
			//完整新增文件路径
			$filepath = $this->_current_theme_path . $info['filename'];
			if ( file_exists($filepath) ) {
				$this->message(0, '添加模板失败：'.$info['filename'].'已存在！');
			}
			
			//写入文件
			$status = file_put_contents($filepath, htmlspecialchars_decode(stripslashes($content)));
			if ($status) {
				$this->message(1, '添加模板成功：'.$info['filename'],'index.php?u=template-index');
			} else {
				$this->message(0, '添加模板失败：'.$info['filename'].'是否设置为可写！');
			}
		}
	}

	//编辑模板
	public function edit(){
		if( empty($_POST) ){
			$file = R('file','G');
			if( $file == '' || !file_exists($this->_current_theme_path.$file) ){
				$this->message(0, '编辑模板文件失败：'.$file.'不存在！');
			}
			$data['content'] = htmlspecialchars(file_get_contents($this->_current_theme_path.$file));
			$data['filename'] = $file;
			$this->assign('data', $data);
			$this->display();
		}else{
			$info = R('info','P');
			if( $info['filename'] == '' )	$this->message(0, '编辑模板失败：文件名为空！');
			
			$content = $info['content'];
			if( $content && get_magic_quotes_gpc() ){
				$info['content'] = stripslashes($content);
			}
				
			//完整新增文件路径
			$filepath = $this->_current_theme_path . $info['filename'];
			//写入文件
			$status = file_put_contents($filepath, htmlspecialchars_decode(stripslashes($content)));
			if ($status) {
				$this->message(1, '编辑模板成功：'.$info['filename'],'index.php?u=template-index');
			} else {
				$this->message(0, '编辑模板失败：'.$info['filename'].'是否设置为可写！');
			}
		}
	}
	
	//删除模板
	public function del(){
		$file = R('file','G');
		
		if( $file == '' || !file_exists($this->_current_theme_path.$file) ){
			$this->message(0, '删除模板文件失败：'.$file.'不存在！');
		}
		unlink($this->_current_theme_path.$file);
		$this->message(1, '删除模板文件成功：'.$file);
	}

	// hook admin_template_control_after.php
}
