<?php
// +----------------------------------------------------------------------
// | MOLYCMS	数据表缓存模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class runtime extends model {
	public $data = array();		// 保证唯一性
	private $changed = array();		// 表示修改过的key

	function __construct() {
		$this->table = 'runtime';	// 表名
		$this->pri = array('k');	// 主键

		// hook runtime_model_construct_after.php
	}

	// 读取缓存
	public function get($k) {
		$arr = parent::get($k);
		return !empty($arr) && (empty($arr['expiry']) || $arr['expiry'] > $_ENV['_time']) ? _json_decode($arr['v']) : array();
	}

	// 写入缓存
	public function set($k, $s, $life = 0) {
		$s = json_encode($s);
		$arr = array();
		$arr['k'] = $k;
		$arr['v'] = $s;
		$arr['expiry'] = $life ? $_ENV['_time'] + $life : 0;
		return parent::set($k, $arr);
	}

// 读取
	public function xget($key = 'cfg') {
		if(!isset($this->data[$key])) {
			$cfg = $this->get($key);
			if($key == 'cfg') {
				if(empty($cfg)) {
					$cfg = (array)$this->kv->get('cfg');

					empty($cfg['theme']) && $cfg['theme'] = 'default';

					$cfg['view'] = $cfg['webdir'].(defined('F_APP_NAME') ? F_APP_NAME : APP_NAME).'/view/';
					$cfg['webroot'] = 'http://'.$cfg['webdomain'];
					$cfg['weburl'] = 'http://'.$cfg['webdomain'].$cfg['webdir'];

					$table_arr = $this->models->get_table_arr();
					$cfg['table_arr'] = $table_arr;

					$mod_name = $this->models->get_name();
					unset($mod_name[1]);
					$cfg['mod_name'] = $mod_name;

					$categorys = $this->category->get_category_db();
					$cate_arr = array();
					foreach($categorys as $row) {
						$cate_arr[$row['cid']] = $row['alias'];
					}
					$cfg['cate_arr'] = $cate_arr;

					$this->set('cfg', $cfg);
				}

				if(!empty($cfg['theme_mobile']) && is_mobile()) {
					$cfg['theme'] = $cfg['theme_mobile'];
				}
				
				if( !isset($cfg['view']) ){
					$cfg['view'] = $cfg['webdir'].(defined('F_APP_NAME') ? F_APP_NAME : APP_NAME).'/view/';
				}
				
				$cfg['tpl'] = $cfg['view'].$cfg['theme'].'/';
			}
			$this->data[$key] = $cfg;
		}
		return $this->data[$key];
	}

	// 修改
	public function xset($k, $v, $key = 'cfg') {
		if(!isset($this->data[$key])) {
			$this->data[$key] = $this->get($key);
		}
		if($v && is_string($v) && ($v[0] == '+' || $v[0] == '-')) {
			$v = intval($v);
			$this->data[$key][$k] += $v;
		}else{
			$this->data[$key][$k] = $v;
		}
		$this->changed[$key] = 1;
	}

	// 保存
	public function xsave($key = 'cfg') {
		$this->set($key, $this->data[$key]);
		$this->changed[$key] = 0;
	}

	// 保存所有修改过的key
	public function save_changed() {
		foreach($this->changed as $key=>$v) {
			$v && $this->xsave($key);
		}
	}
}
