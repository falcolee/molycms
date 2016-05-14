<?php
// +----------------------------------------------------------------------
// | MOLYCMS	后台菜单管理
// +----------------------------------------------------------------------
//

class menu_control extends admin_control {
	
	public function index(){
		$model = &$this->menu_admin;
		
		$menu_arr = $model->find_fetch();
		
		$array = array();
		
		foreach($menu_arr as $r) {
			$s = '';
			if( $r['upid'] == 0 ){
				$s = '<a href="index.php?u=menu-add-upid-'.$r['cid'].'">添加子菜单</a> | ';
			}
			$del = '';
			if( $r['system'] == 0 ){
				$del = ' | <a class="J_ajax_del" href="index.php?u=menu-del-cid-'.$r['cid'].'-ajax-1">删除</a>';
			}
			
			$r['str_manage'] = $s.'<a href="index.php?u=menu-edit-cid-'.$r['cid'].'">编辑</a>'.$del;	
			$array[] = $r;
		}
		
		$str  = "<tr>
		<td width='40' align='center'>
		<input name='listorder[\$cid]' type='text' size='3' value='\$listorder' class='input-text-c'>
		</td>
		<td align='center'>\$cid</td>
		<td >\$spacer\$title &nbsp;</td>
		<td align='center'>\$controller</td>
		<td align='center'>\$action</td>
		<td align='center'>\$param</td>
		<td align='center'>\$content</td>
		<td align='center'>\$str_manage</td>
		</tr>";
		
		$tree = new categorytree($array);
		
		unset($array);
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│  ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		
		$menus = $tree->get_tree(0, $str);
		$this->assign('menus', $menus);
		
		$this->display();
	}
	
	public function add(){
		$model = &$this->menu_admin;
		
		if( empty($_POST) ){
			$upid = intval( R('upid','G') );
			
			$menu_arr = $model->find_fetch(array('upid'=>0));
			$this->assign('menus',$menu_arr);
			
			$data = array('listorder'=>0,'status'=>1,'upid'=>$upid,'type'=>1);
			$this->assign('data',$data);
			
			$this->display('menu_set.htm');
		}else{
			$info = R('info','P');
			$info['listorder'] = intval( $info['listorder'] );
			if( empty($info['title']) ){
				$this->message(0, '菜单名称不能为空！');
			}elseif( $info['type'] == 1 && empty($info['controller']) ){
				$this->message(0, '控制器不能为空！');
			}
			empty($info['action']) && $info['action'] = 'index';
			
			$cid = $model->create($info);
			if( $cid ){
				$this->message(1, '添加菜单成功！','index.php?u=menu-index');
			}else{
				$this->message(0, '写入菜单表失败！');
			}
		}
	}
	
	public function edit(){
		$model = &$this->menu_admin;
		
		if( empty($_POST) ){
			$cid = intval( R('cid','G') );
				
			$menu_arr = $model->find_fetch(array('upid'=>0));
			$this->assign('menus',$menu_arr);
				
			$data = $model->get($cid);
			$this->assign('data',$data);
				
			$this->display('menu_set.htm');
		}else{
			$info = R('info','P');
			$info['listorder'] = intval( $info['listorder'] );
			if( empty($info['title']) ){
				$this->message(0, '菜单名称不能为空！');
			}elseif( $info['type'] == 1 && empty($info['controller']) ){
				$this->message(0, '控制器不能为空！');
			}
			
			$info['cid'] = intval( R('cid','P') );
			empty($info['action']) && $info['action'] = 'index';
			
			if(!$model->update($info)) {
				$this->message(0, '更新菜单表出错');
			}else{
				$this->message(1, '编辑菜单成功！','index.php?u=menu-index');
			}
		}
	}

    // 排序
    public function listorder() {
        $listorder = R('listorder','P');
        if( empty($listorder) ){
            $this->message(0, '排序参数错误！');
        }
        $model = &$this->menu_admin;
        foreach ($listorder as $k=>$v){
            $data['cid']=$k;
            $data['listorder'] = intval($v);
            $model->update($data);
        }
        $this->message(1, '排序成功！');
    }
	
	public function del(){
		$model = &$this->menu_admin;
		$cid = intval( R('cid','G') );
		
		//是否有子菜单
		$sonCount = $model->find_count(array('upid'=>$cid));
		if( intval($sonCount) > 0 ) $this->message(0, '删除菜单失败，请先删除子菜单！');
		
		$status = $model->delete($cid);
		if( $status ){
			$this->message(1, '删除菜单成功！','index.php?u=menu-index');
		}else{
			$this->message(0, '删除菜单失败！');
		}
	}
}
