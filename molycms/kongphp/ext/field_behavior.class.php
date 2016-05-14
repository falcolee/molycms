<?php

class field_behavior {

    private static $instance;

    /**
     * Description:静态方法，单例访问统一入口
     * @return Singleton：返回应用中的唯一对象实例
     */
    public static function GetInstance()
    {
        if (!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

     /**
     * 生成字段的创建信息
     *
     * @access  public
     * @param   string
     * @param   string
     * @return  array
     */
	public static function  on_info($data, $oldname = '')
	{
			switch ($data['type'])
			{
				case 'select_from_model' :
				case 'radio_from_model':
				case 'int'   	: $field = array(
												'type' => 'INT',
												 'constraint' => $data['length'] ? $data['length'] : 10 ,
												 'default' => 0
												) ;
								break;
				case 'float' : $field = array(
												'type' => 'FLOAT',
												 'constraint' => $data['length'] ? $data['length'] : 10,
												 'default' => 0
												) ;
								break;
				case 'input' : 
				case 'select':
				case 'radio' :
				case 'checkbox':
				case 'checkbox_from_model':
				case 'datetime':
				case 'colorpicker':
				case 'linked_menu':
				case 'textarea' : 
								$field = array(
												'type' => 'VARCHAR',
												 'constraint' => $data['length'] ? $data['length'] : 100 ,
												 'default' => ''
												) ;
								break;
				case 'wysiwyg' :
				case 'wysiwyg_basic':
								$field = array(
												'type' => 'TEXT'
												) ;
								break;
				case 'content' : $field = array(
												'type' => 'INT',
												 'constraint' => $data['length'] ? $data['length'] : 10 ,
												 'default' => 0
												) ;
								break;
			}
			if ($oldname != '')
			{
				$field['name'] = $data['name'];
				return array($oldname => $field);
			}
			else
			{
				return array($data['name'] => $field);
			}
	}

    /**
     * 生成字段的列表的控件
     *
     * @access  public
     * @param   array
     * @param   mixed
     * @return  void
     */
    public function on_list($field,$value){
        switch ($field['type'])
        {
            case 'radio' 	:
            case 'select'	:
                echo isset($field['source'][$value[$field['name']]]) ?  $field['source'][$value[$field['name']]] : 'undefined' ;
                break;
            case 'checkbox' :
                foreach (explode(',', $value[$field['name']]) as $t)
                {
                    echo isset($field['source'][$t]) ?  $field['source'][$t] . '<br />' : 'undefined' . '<br />';
                }
                break;
            case 'radio_from_model':
            case 'select_from_model':
                $this->_get_data_from_model($field,false);
                echo isset($field['source'][$value[$field['name']]]) ? $field['source'][$value[$field['name']]] : 'undefined' ;
                break;
            case 'checkbox_from_model':
                $this->_get_data_from_model($field,false);
                $checkbox_values = explode(',', $value[$field['name']]);
                foreach ($checkbox_values as $checkbox)
                {
                    //echo isset($setting[$options[0]][$checkbox][$options[1]]) ? $setting[$options[0]][$checkbox][$options[1]].'<br />' : 'undefined<br />' ;
                }
                break;
            case 'linked_menu':
                $options = explode('|', $field['source']);

                $temp_out = explode('|', $value[$field['name']]);
                foreach ($temp_out as & $t)
                {
                    $t = str_replace(',', '', $t);
                    $temp = explode('-', $t);
                    foreach ($temp as & $tt)
                    {
                        //$tt = (isset($setting[$options[0]][$tt][$options[1]]) ? $setting[$options[0]][$tt][$options[1]] : 'undefined');
                    }
                    $t = implode('-', $temp);
                }
                echo implode(',', $temp_out);
                break;
            case 'content':
                $options = explode('|', $field['source']);
                $content_model = new diy_models();
                $content_model->set_table('u_m_'.$options[0]);

                if($value[$field['name']] AND $row = $content_model->get($value[$field['name']])){
                    echo $row[$options[1]];
                }else{
                    echo '-';
                }
                break;
            default :
                echo $value[$field['name']];
        }
    }

    public static function on_form($field, $default = '', $has_tip = TRUE, $allow_upload = FALSE){
        $default_select_enabled = array('select', 'checkbox', 'radio');
        if (in_array($field['type'],$default_select_enabled))
        {
            if ($field['source'] == '')
            {
                $field['source'] = array();
            }
            else
            {
                $value = array();
                foreach (explode('|', $field['source']) as $vt)
                {
                    if (strpos($vt,'=') > -1)
                    {
                        $vt = explode('=', $vt);
                        $value[$vt[0]] = $vt[1];
                    }
                    else
                    {
                        $value[$vt] = $vt;
                    }
                }
                $field['source'] = $value;
            }
        }
        //查看是否有指定默认值,以下字段类型支持
        $default_value_enabled = array('int','float','input','textarea','colorpicker','datetime');
        if (in_array($field['type'], $default_value_enabled) AND $default == '' AND $field['source'] != '')
        {
            $default = $field['source'];
        }
        $formrender = formrender::GetInstance();
        $formrender->display($field, $default, $has_tip, $allow_upload);
    }

    /**
     * 获取缓存数据并处理，返回处理状态
     *
     * @access  private
     * @param   array
     * @param   bool
     * @return  bool
     */
    private function _get_data_from_model( & $field , $need_level = FALSE)
    {
        if ( ! $field['source'])
        {
            return FALSE;
        }
        if (count($options = explode('|', $field['source'])) != 2 )
        {
            return FALSE;
        }

        $model = new diy_models();
        $model->set_table($options[0]);
        $model_data = $model->find_fetch();
        $field['source'] = array();
        foreach ($model_data as $v)
        {
            $field['source'][$v['id']] = $v[$options[1]];
            $need_level AND $field['levels'][$v['id']] = $v['level'];
        }
        return TRUE;
    }
}
