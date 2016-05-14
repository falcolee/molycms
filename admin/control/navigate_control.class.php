<?php
// +----------------------------------------------------------------------
// | MOLYCMS	导航管理
// +----------------------------------------------------------------------
//

class navigate_control extends admin_control {

    // 导航管理
	public function index() {
        $id = intval( R('id','G') );

		// 导航数组
        $nav_name = 'navigate_'.$id;
	$nav_arr = $this->kv->xget($nav_name);
        //if( empty($nav_arr) )	$this->message(0, '失败：导航菜单'.$nav_name.'不存在！');

		foreach($nav_arr as $k=>$v) {
			if($v['cid']) $nav_arr[$k]['url'] = $this->category->category_url($v['cid'], $v['alias']);
			if(isset($v['son'])) {
				foreach($v['son'] as $k2=>$v2) {
					if($v2['cid']) $nav_arr[$k]['son'][$k2]['url'] = $this->category->category_url($v2['cid'], $v2['alias']);
				}
			}
		}
        //模块分类传入
        $type_model = &$this->types;
        $type_data = $type_model->get($id);

        $this->assign('type_data', $type_data);
        $this->assign('nav_arr', $nav_arr);
        $this->assign('nav_id', $id);
		// 模型名称
		$mod_name = $this->models->get_name();
		$this->assign('mod_name', $mod_name);
		// 全部分类
		$category_list = $this->category->get_category();
		$this->assign('category_list', $category_list);

		// hook admin_navigate_control_index_after.php

		$this->display();
	}

    public function nav(){
        $model = &$this->types;
        $pagenum = 15;
        $urlstr = '';
        $where = array();
        $where['class'] = 'navigation';

        $total = $model->find_count($where);

        $maxpage = max(1, ceil($total/$pagenum));
        $page = min($maxpage, max(1, intval(R('page'))));
        $pages = pages($page, $maxpage, 'index.php?u=navigate-nav'.$urlstr.'-page-{page}');
        $this->assign('total', $total);
        $this->assign('pages', $pages);

        $list = $model->find_fetch($where,array('id'=>'-1'),($page-1)*$pagenum, $pagenum, $total);
        $this->assign('list', $list);

        $this->display();
    }

	// 导航管理
// 	public function get_navigate_content() {
// 		// 导航数组
// 		$nav_arr = $this->kv->xget('navigate');
// 		$this->assign('nav_arr', $nav_arr);

// 		$this->display('inc-navigate_content.htm');
// 	}
	
	// 保存修改
	public function nav_save() {
		$navi = R('navi', 'P');
        $nav_id = R('nav_id', 'P');
		if(!empty($navi) && is_array($navi) && !empty($nav_id)) {
            $nav_name = 'navigate_'.$nav_id;
			$nav_arr = array();
			$i = 0;
			foreach($navi as $v) {
				$cid = intval($v[0]);
				$name = htmlspecialchars(trim($v[1]));
				$url = $cid ? $cid : htmlspecialchars(trim($v[2]));
				$target = $v[3] ? '_blank' : '_self';
				$rank = intval($v[4]);

				$alias = '';
				if($cid) {
					$row = $this->category->get($cid);
					$alias = isset($row['alias']) ? $row['alias'] : '';
				}

				if($rank > 1) {
					$nav_arr[$i]['son'][] = array('cid'=>$cid, 'alias'=>$alias, 'name'=>$name, 'url'=>$url, 'target'=>$target);
				}else{
					$i++;
					$nav_arr[$i] = array('cid'=>$cid, 'alias'=>$alias, 'name'=>$name, 'url'=>$url, 'target'=>$target);
				}
			}
			$this->kv->set($nav_name, $nav_arr);
		}else{
			$this->message(0, '非法提交！');
		}
		
		$this->message(1, '保存修改完成！','index.php?u=navigate-index-'.$nav_id);
	}

	// 添加分类
	public function add_cate() {
		$cate = R('cate', 'P');
        $nav_id = R('nav_id', 'P');
		if(!empty($cate) && is_array($cate) && !empty($nav_id)) {
            $nav_name = 'navigate_'.$nav_id;
			$nav_arr = $this->kv->xget($nav_name);
			foreach($cate as $arr) {
				if(isset($arr[0]) && isset($arr[1])) {
					$name = htmlspecialchars(trim($arr[0]));
					$cid = intval($arr[1]);
					$row = $this->category->get($cid);
					$alias = $row['alias'];
					$nav_arr[] = array('cid'=>$cid, 'alias'=>$alias, 'name'=>$name, 'url'=>'', 'target'=>'_self');
				}
			}
			$this->kv->set($nav_name, $nav_arr);

			$this->message(1, '添加成功！','index.php?u=navigate-index-'.$nav_id);
		}else{
			$this->message(0, '添加分类不能为空！');
		}
	}

	// 添加链接
	public function add_link() {
		$name = htmlspecialchars(trim(R('name', 'P')));
		$url = htmlspecialchars(trim(R('url', 'P')));
		$target = (int) R('target', 'P');
        $nav_id = R('nav_id', 'P');

		!$name && $this->message(0, '名称不能为空！');
		!$url && $this->message(0, '链接不能为空！');
        !$nav_id && $this->message(0, '导航参数错误！');

        $nav_name = 'navigate_'.$nav_id;
		$nav_arr = $this->kv->xget($nav_name);
		$nav_arr[] = array('cid'=>0, 'alias'=>'', 'name'=>$name, 'url'=>$url, 'target'=>($target ? '_blank' : '_self'));
		$this->kv->set($nav_name, $nav_arr);
		
		$this->message(1, '添加成功！','index.php?u=navigate-index-'.$nav_id);
	}

	// 删除
	public function del() {
		$key = R('key', 'P');
        $nav_id = R('nav_id', 'P');

        !$nav_id && $this->message(0, '导航参数错误！');

        $nav_name = 'navigate_'.$nav_id;
		$nav_arr = $this->kv->xget($nav_name);
		if(is_numeric($key)) {
			unset($nav_arr[$key]);
		}else{
			$k = explode('-', $key);
			$k1 = intval($k[0]);
			$k2 = intval($k[1]);
			if(isset($nav_arr[$k1]['son'][$k2])) unset($nav_arr[$k1]['son'][$k2]);
		}
		$this->kv->set($nav_name, $nav_arr);

		$this->message(1, '删除成功！');
	}

	// hook admin_navigate_control_after.php
}
