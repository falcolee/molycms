<?php
/**
 * Created by PhpStorm.
 * User: MolyCMS
 * Date: 14-12-28
 * Time: 下午8:38
 */
class formrender{

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
     * 输出控件HTML
     *
     * @access  public
     * @param   array
     * @param   string
     * @param   bool
     * @return  void
     */
    public function display( & $field, $default = '', $has_tip = TRUE, $allow_upload = FALSE)
    {
        //$this->_find_real_value($field['name'], $default);
        $type = '_'.$field['type'];
        if ($has_tip)
        {
            $html =  $this->$type($field, $default, $allow_upload);
            echo  $this->_add_tip($field['ruledescription'],$html);
        }
        else
        {
            echo  $this->$type($field, $default, $allow_upload);
        }
    }

    /**
     * 检测表单元素的真正的值
     *
     * @access  private
     * @param   string
     * @param   string
     * @return  void
     */
    private function _find_real_value($name, & $default)
    {
        if (isset($_POST[info][$name]))
        {
            $default = 	$_POST[info][$name];
        }
    }

    /**
     * 生成控件的TIPS
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _add_tip( & $rules, & $html)
    {
        if ($rules)
        {
            $html .= '<label>'.$rules.'</lable>';
        }
        return $html;
    }

    /**
     * 输出分类的HTML
     *
     * @access  public
     * @param   array
     * @param   string
     * @param   string
     * @return  void
     */
    public function show_class( & $category, $name, $default )
    {
        $this->_find_real_value($name, $default);
        $html = '<select name="info[' . $name . ']" id="' . $name .'">'.
            '<option value="">请选择</option>';
        foreach ($category as $v)
        {
            $html .= 	'<option value="' . $v['class_id'] . '" ' . ($default == $v['class_id'] ? 'selected="selected"' : '') . '>';
            for ($i = 0 ; $i < $v['deep'] ; $i++)
            {
                $html .= "&nbsp;&nbsp;";
            }
            $html .= $v['class_name'] . '</option>';
        }
        $html .= '</select>';
        echo $html;
    }

    // ------------------------------------------------------------------------

    /**
     * 输出隐藏控件的HTML
     *
     * @access  public
     * @param   string
     * @param   string
     * @return  void
     */
    public function show_hidden($name, $default = '', $lock = FALSE)
    {
        if ($lock == true)
        {
            $this->_find_real_value($name, $default);
        }
        echo '<input type="hidden" name="info[' . $name . ']" id="' . $name . '" value="' . $default . '" />';
    }

    /**
     * 根据给定的类型输出控件的HTML
     *
     * @access  public
     * @param   string
     * @param   string
     * @param   string
     * @param   string
     * @return  void
     */
    public function show($name, $type, $value = '', $default = '')
    {
        $this->_find_real_value($name, $default);
        $type = '_' . $type;
        $field = array('name' => $name, 'source' => $value, 'width' => 0, 'height' => 0);
        echo $this->$type($field, $default);
    }

    /**
     * 生成INT类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _int($field, $default)
    {
        return '<input class="normal" name="info[' . $field['name'] . ']" id="' . $field['name'] .
        '" type="text" style="width:50px" autocomplete="off" value="' . $default . '" />';
    }

    /**
     * 生成FLOAT类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _float($field, $default)
    {
        return '<input class="normal" name="info[' . $field['name'] . ']" id="' .$field['name'] .
        '" type="text" style="width:50px" autocomplete="off" value="' . $default . '" />';
    }

    /**
     * 生成PASSWORD类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _password($field, $default)
    {
        $field['width'] =  $field['width'] ? $field['width'] : 150;
        return '<input class="normal" name="info[' . $field['name'] . ']" id="' . $field['name'] .
        '" type="password" style="width:' . $field['width'] . 'px" autocomplete="off" />';
    }

    /**
     * 生成TEXTAREA类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _textarea($field, $default)
    {
        if ( ! $field['width'] )
        {
            $field['width'] = 300;
        }
        if ( ! $field['height'] )
        {
            $field['height'] = 100;
        }
        return '<textarea class="hack_xheditor" id="' . $field['name'] . '" name="info[' . $field['name'] .
        ']" style="width:' . $field['width'] . 'px;height:' . $field['height'] . 'px">' . $default . '</textarea>';
    }

    // ------------------------------------------------------------------------

    /**
     * 生成SELECT类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _select($field, $default)
    {
        $return = '<select name="info[' . $field['name'] . ']" id="' . $field['name'] . '">'.
            '<option value="" >请选择</option>';
        foreach ($field['source'] as $key=>$v)
        {
            $pre_fix = '';
            if (isset($field['levels'][$key]) AND $field['levels'][$key] > 0)
            {
                for ($i = 0 ; $i < $field['levels'][$key] ; $i ++)
                {
                    $pre_fix .= '&nbsp;&nbsp;';
                }
            }
            $return .= 	'<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . '>' . $pre_fix . $v . '</option>';
        }
        $return .= '</select>';
        return $return;
    }

    // ------------------------------------------------------------------------

    /**
     * 生成RADIO类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _radio($field, $default)
    {
        $return = '<ul class="attr_list">';
        $count = 1;
        foreach ($field['source'] as $key=>$v)
        {
            $return .= '<li><input id="rad_' . $field['name'] . '_' . $count . '" name="info[' . $field['name'] . ']" type="radio" value="' .
                $key . '" ' . ($default == $key ? 'checked="checked"' : '') . ' /><lable class="attr" for="rad_' . $field['name'] . '_' . $count . '">' . $v . '</lable></li>';
            $count ++;
        }
        $return .= '</ul>';
        return $return;
    }

    // ------------------------------------------------------------------------

    /**
     * 生成CHECKBOX类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _checkbox($field, $default)
    {
        $return = '<ul class="attr_list">';
        if (is_array($field['source']))
        {
            if ( ! is_array($default))
            {
                $default = ($default != '' ? explode(',', $default) : array());
            }
            $count = 1;
            foreach ($field['source'] as $key => $v)
            {
                $return .= 	'<li><input id="chk_' . $field['name'] . '_' . $count . '" name="info[' . $field['name'] . '][]" type="checkbox" value="' .
                    $key . '" ' . (in_array($key, $default) ? 'checked="checked"' : '') . ' /><lable class="attr" for="chk_' . $field['name'] . '_' . $count . '">' . $v . '</lable></li>';
                $count ++;
            }
        }
        else
        {
            $return .= 	'<li><input id="chk_' . $field['name'] . '" name="info[' . $field['name'] . ']" type="checkbox" value="1" ' .
                ($default == 1 ? 'checked="checked"' : '') . ' /><lable class="attr" for="chk_' . $field['name'] . '">' . $field['source'] . '</lable></li>';
        }
        $return .= '</ul>';
        return $return;
    }

    /**
     * 生成INPUT类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _input($field, $default)
    {
        $field['width'] =  $field['width'] ? $field['width'] : 150;
        return '<input class="normal" name="info[' . $field['name'] . ']" id="' . $field['name'] .
        '" type="text" style="width:' . $field['width'] . 'px" autocomplete="off" value="' . $default . '" />';
    }

    /**
     * 生成DATETIME类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _datetime($field, $default)
    {
        return '<input class="Wdate" style="width:150px;" type="text" name="info[' . $field['name'] . ']" id="' .
        $field['name'] . '" value="' . $default . '" onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:\'yyyy-MM-dd HH:mm:ss\'})"/>';
    }

    /**
     * 生成COLORPICKER类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _colorpicker($field, $default)
    {
        if ( ! $field['width'] )
        {
            $field['width'] = 100;
        }
        return '<input class="field_colorpicker normal" name="info[' . $field['name'] . ']" id="' . $field['name'] .
        '" type="text" style="width:' . $field['width'] . 'px" autocomplete="off" value="' . $default . '" />';
    }

    /**
     * 生成SELECT_FROM_MODEL类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _select_from_model($field, $default)
    {
        if ( ! $this->_get_data_from_model($field, TRUE))
        {
            return '获取数据源时出错了!';
        }
        return $this->_select($field, $default);
    }

    /**
     * 生成CHECKBOX_FROM_MODEL类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _checkbox_from_model($field, $default)
    {
        if ( ! $this->_get_data_from_model($field))
        {
            return '获取数据源时出错了!';
        }
        return $this->_checkbox($field, $default);
    }

    /**
     * 生成RADIO_FROM_MODEL类型控件HTML
     *
     * @access  private
     * @param   array
     * @param   string
     * @return  string
     */
    private function _radio_from_model($field, $default)
    {
        if ( ! $this->_get_data_from_model($field))
        {
            return '获取数据源时出错了!';
        }
        return $this->_radio($field, $default);
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