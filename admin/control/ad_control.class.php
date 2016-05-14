<?php
// +----------------------------------------------------------------------
// | MOLYCMS	广告管理
// +----------------------------------------------------------------------
//

class ad_control extends admin_control {
	
	public function index(){
		$model = &$this->ad;
		
		$pagenum = 15;
		$urlstr = '';
		$where = array();
		
		$total = $model->count();
		
		$maxpage = max(1, ceil($total/$pagenum));
		$page = min($maxpage, max(1, intval(R('page'))));
		$pages = pages($page, $maxpage, 'index.php?u=ad-index'.$urlstr.'-page-{page}');
		$this->assign('total', $total);
		$this->assign('pages', $pages);
		
		$list = $model->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
		$this->assign('list', $list);
		
		$this->display();
	}
	
	//添加广告
	public function add(){
		if( empty($_POST) ){
			$data = array('timeset'=>0);
			$this->assign('data', $data);
			
			$this->display('ad_set.htm');
		}else{
			$info = R('info','P');
			if($info['adname'] == '')	$this->message(0,"广告名称不能为空!");
			 
			if( $info['starttime'] ){
				$info['starttime'] = strtotime($info['starttime']);
			}else{
				$info['starttime'] = time();
			}
			if( $info['endtime'] ){
				$info['endtime'] = strtotime($info['endtime']);
			}else{
				$info['endtime'] = time();
			}
			 
			if( $info['starttime'] > $info['endtime'] )	$this->message(0,'开始时间不能大于结束时间!');
			$normbody = R('normbody','P');
			switch ($normbody['style']){
				case 'code':
					if( $normbody['htmlcode'] == '' )	$this->message(0,'广告代码不能为空!');
					 
					$info['normbody'] = $normbody['htmlcode'];
					break;
				case 'txt':
					if( $normbody['title'] == '' )	$this->message(0,'文字内容不能为空!');
					if( $normbody['link'] == '' )	$this->message(0,'文字链接不能为空!');
					 
					$font_size = $font_color = '';
					if( $normbody['size'] ){
						$font_size = 'font-size="'.$normbody['size'].'" ';
					}
					if( $normbody['color'] ){
						$font_color = 'color="'.$normbody['color'].'"';
					}
					 
					$info['normbody'] = '<a '.$font_size.$font_color.' target="_blank" title="'.$normbody['title'].'" href="'.$normbody['link'].'">'.$normbody['title'].'</a>';
					break;
				case 'img':
					if( $normbody['link'] == '' )	$this->message(0,'图片链接不能为空!');
					if( $normbody['url'] == '' )	$this->message(0,'图片地址不能为空!');
					 
					$width = $height = '';
					if( $normbody['width'] ){
						$width = 'width="'.$normbody['width'].'" ';
					}
					if( $normbody['height'] ){
						$height = 'height="'.$normbody['height'].'"';
					}
					$info['normbody'] = '<a target="_blank" href="'.$normbody['link'].'"><img '.$width.$height.' alt="'.$normbody['description'].'" src="'.$normbody['url'].'"></a>';
					break;
				case 'flash':
					if( $normbody['link'] == '' )	$this->message(0,'flash链接不能为空!');
			
					$width = $height = '';
					if( $normbody['width'] ){
						$width = 'width="'.$normbody['width'].'" ';
					}
					if( $normbody['height'] ){
						$height = 'height="'.$normbody['height'].'"';
					}
					 
					$info['normbody'] = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.Macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" '.$width.$height.'><param name="movie" value="'.$normbody['link'].'"/><param name="quality" value="high"/></object>';
					break;
					 
				default:
					$this->message(0,'广告类型错误!');
			}

			$model = &$this->ad;
			$id = $model->create($info);
			if( $id ){
				$this->message(1,'广告添加成功!','index.php?u=ad-index');
			}else{
				$this->message(0,'广告添加失败!');
			}
		}
	}
	
	//编辑广告
	public function edit(){
		$model = &$this->ad;
		if( empty($_POST) ){
			$id = intval( R('id','G') );
			$data = $model->read($id);
			empty($data) && $this->message(0,'获取广告失败!');
			
			if( $data['starttime'] ){
				$data['starttime'] = date('Y-m-d H:i:s',$data['starttime']);
			}
			if( $data['endtime'] ){
				$data['endtime'] = date('Y-m-d H:i:s',$data['endtime']);
			}
			$this->assign('data', $data);
			
			$this->display('ad_set.htm');
		}else{
			$info = R('info','P');
			if($info['adname'] == '')	$this->message(0,"广告名称不能为空!");
			
			if( $info['starttime'] ){
				$info['starttime'] = strtotime($info['starttime']);
			}else{
				$info['starttime'] = time();
			}
			if( $info['endtime'] ){
				$info['endtime'] = strtotime($info['endtime']);
			}else{
				$info['endtime'] = time();
			}
			
			if( $info['starttime'] > $info['endtime'] )	$this->message(0,'开始时间不能大于结束时间!');
			if(!$model->update($info)) {
				$this->message(0, '编辑广告失败：更新广告表失败！');
			}else{
				$this->message(1, '编辑广告成功！','index.php?u=ad-index');
			}
		}
	}
	
	public function del(){
		$model = &$this->ad;
		$id = intval( R('id','P') );
	
		$status = $model->delete($id);
		if( $status ){
			$this->message(1, '删除广告成功,广告ID:'.$id,'index.php?u=ad-index');
		}else{
			$this->message(0, '删除广告失败,广告ID:'.$id);
		}
	}
}
