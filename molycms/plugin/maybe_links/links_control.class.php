<?php
class links_control extends admin_control {
	// 管理链接
	public function index() {
		$links = $this->kv->xget('maybe_links');
		$this->assign('links', $links);

		$this->display();
	}

	// 增加/编辑链接
	public function set() {
		$name = htmlspecialchars(R('name', 'P'));
		$url = htmlspecialchars(R('url', 'P'));
		isset($_POST['key']) && $key = (int) R('key', 'P');

		!$name && $this->message(0, '网站名称不能为空！','index.php?u=links-index');
		!$url && $this->message(0, '网站URL不能为空！','index.php?u=links-index');

		$arr = $this->kv->xget('maybe_links');
		$row = array('name' => $name, 'url' => $url);

		// key 有值为编辑
		if(isset($key)) {
			$arr[$key] = $row;
			$this->kv->set('maybe_links', $arr);
			$this->message(1, '编辑成功！','index.php?u=links-index');
		}else{
			$arr[] = $row;
			$this->kv->set('maybe_links', $arr);
			$this->message(1, '添加成功！','index.php?u=links-index');
		}
	}

	// 删除链接
	public function del() {
		$key = (int) R('key', 'P');

		$arr = $this->kv->xget('maybe_links');
		unset($arr[$key]);
		$this->kv->set('maybe_links', $arr);
		
		$this->message(1, '删除完成！','index.php?u=links-index');
	}

	// 链接排序
	public function sort() {
		$keys = R('keys', 'P');

		$arr = $this->kv->xget('maybe_links');
		if(!empty($keys) && is_array($keys)) {
			$newarr = array();
			foreach($keys as $k) {
				strlen($k) && $newarr[] = $arr[$k];
			}
			$this->kv->set('maybe_links', $newarr);
		}
		
		$this->message(1, '修改排序完成！','index.php?u=links-index');
	}
}
