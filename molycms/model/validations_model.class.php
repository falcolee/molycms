<?php
// +----------------------------------------------------------------------
// | MOLYCMS	字段验证类型模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class validations extends model
{
    public $data = array(); // 防止重复查询
    function __construct()
    {
        $this->table = 'validations'; // 表名
        $this->pri = array('k'); // 主键
    }

    public function get_validations()
    {
        if (isset($this->data['validations'])) {
            return $this->data['validations'];
        }
        return $this->data['validations'] = $this->find_fetch();
    }

    public function get_validations_name()
    {
        $names = array();
        foreach ($this->get_validations() as $v) {
            $names[$v['k']] = $v['v'];
        }
        return $names;
    }
}
