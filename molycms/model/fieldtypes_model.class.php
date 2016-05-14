<?php
// +----------------------------------------------------------------------
// | MOLYCMS	字段键值模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class fieldtypes extends model {
        public $data = array();		// 防止重复查询
	function __construct() {
		$this->table = 'fieldtypes';		// 表名
		$this->pri = array('k');	// 主键
	}

    public function get_field_types()
    {
        if(isset($this->data['fileTypes'])) {
                return $this->data['fileTypes'];
            }
            return $this->data['fileTypes'] = $this->find_fetch();            
        }

    public function get_types_name()
    {
        $names = array();
        foreach ($this->get_field_types() as $v) {
            $names[$v['k']] = $v['v'];
        }
        return $names;
    }
}
