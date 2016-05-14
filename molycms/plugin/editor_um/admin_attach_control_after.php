<?php

// 编辑器上传图片
function up_image() {
	$type = R('type');
	$mid = (int)R('mid');
	$cid = (int)R('cid');
	$id = (int)R('id');
	$cfg = $this->runtime->xget();

	if($mid == 0) {
		// 其它上传的图片量小，所以不保存到数据库。
		$updir = 'upload/other/';
		$config = array(
				'maxSize'=>$cfg['up_img_max_size'],
				'allowExt'=>$cfg['up_img_ext'],
				'upDir'=>MOLYCMS_PATH.$updir,
		);
		$up = new upload($config, 'upfile');
		$info = $up->getFileInfo();
	}elseif($mid == 1) {
		// 单页上传的图片量小，所以不保存到数据库。
		$updir = 'upload/page/';
		$config = array(
			'maxSize'=>$cfg['up_img_max_size'],
			'allowExt'=>$cfg['up_img_ext'],
			'upDir'=>MOLYCMS_PATH.$updir,
		);
		$up = new upload($config, 'upfile');
		$info = $up->getFileInfo();
	}else{
		// 非单页模型
		$table = $this->models->get_table($mid);
		
		if( empty($table) ) $table = 'article';
		
		$updir = 'upload/'.$table.'/';
		$config = array(
			'maxSize'=>$cfg['up_img_max_size'],
			'allowExt'=>$cfg['up_img_ext'],
			'upDir'=>MOLYCMS_PATH.$updir,
		);
		$this->cms_attach->table = 'cms_attach';
		$info = $this->cms_attach->uploads($config, $this->_user['uid'], $cid, $id,$mid);
	}
	$path = $updir.$info['path'];

	// 是否添加水印
	if($info['state'] == 'SUCCESS' && !empty($cfg['watermark_pos'])) {
		image::watermark(MOLYCMS_PATH.$path, MOLYCMS_PATH.'static/img/watermark.png', null, $cfg['watermark_pos'], $cfg['watermark_pct']);
	}

	if($type == 'ajax') {
		echo '{"path":"'.$path.'","state":"'.$info['state'].'"}';
	}else{
		$editorid = preg_replace('/\W/', '', R('editorid'));
		echo "<script>parent.UM.getEditor('".$editorid."').getWidgetCallback('image')('".$path."','".$info['state']."')</script>";
	}
	exit;
}
