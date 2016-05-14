<?php
// +----------------------------------------------------------------------
// | MOLYCMS	自定义内容模型
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit;

class diy_models extends model {
    public $data = array();		// 防止重复查询
    var $fields			= array();
    var $keys			= array();
    var $primary_keys           = array();

	function __construct() {
		$this->table = 'models';	// 表名
		$this->pri = array('id');	// 主键
		$this->maxid = 'id';		// 自增字段            
	}

    // 获取所有模型
//    public function get_models() {
//        if(isset($this->data['models'])) {
//            return $this->data['models'];
//        }
//        return $this->data['models'] = $this->find_fetch();
//    }
    
    public function add_new_model($name = ''){
        if($name==''){
            throw new Exception('A table name is required for that operation.');
        }
            $table = 'u_m_' . $name;
            $this->drop_table($table);
            $this->add_field(array('id' => array('type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE)));
            $this->add_key('id', TRUE);
            $this->add_field(array('create_time' => array('type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'default' => 0)));
            $this->add_field(array('update_time' => array('type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'default' => 0)));
            $this->add_field(array('create_user' => array('type' => 'TINYINT', 'constraint' => 10, 'unsigned' => TRUE, 'default' => 0)));
            $this->add_field(array('update_user' => array('type' => 'TINYINT', 'constraint' => 10, 'unsigned' => TRUE, 'default' => 0)));
            $sql = $this->create_table($table);   
            return $this->db->query($sql);
    }

//    public function get_model_by_name($name){
//        $data = $this->find_fetch(array('name'=>$name), array(), 0, 1);
//        return $data ? array_pop($data) : array();
//    }
    
//    public function check_duplicate_model($name){
//        $where = array('name'=>$name);
//        if($this->find_count($where)>0){
//            return TRUE;
//        }  else {
//            return FALSE;
//        }
//    }

   public function set_table($table_name){
       $this->table = $table_name;
   }

    public function drop_table($name){
        return $this->db->drop_table($this->_escape_identifiers($this->db->tablepre.$name));
    }

    public function create_table($table,$if_not_exists=FALSE){
		if ($table == '')
		{
			throw new Exception('A table name is required for that operation.');
		}
		if (count($this->fields) == 0)
		{
			throw new Exception('Field information is required.');
		}        
		$sql = 'CREATE TABLE ';

		if ($if_not_exists === TRUE)
		{
			$sql .= 'IF NOT EXISTS ';
		}
                $sql .= $this->_escape_identifiers($this->db->tablepre.$table)." (";
                $sql .= $this->_process_fields($this->fields);
		if (count($this->primary_keys) > 0)
		{
			$key_name = $this->_protect_identifiers(implode('_', $this->primary_keys));
			$primary_keys = $this->_protect_identifiers($this->primary_keys);
			$sql .= ",\n\tPRIMARY KEY ".$key_name." (" . implode(', ', $primary_keys) . ")";
		}
		if (is_array($this->keys) && count($this->keys) > 0)
		{
			foreach ($this->keys as $key)
			{
				if (is_array($key))
				{
					$key_name = $this->db->_protect_identifiers(implode('_', $key));
					$key = $this->db->_protect_identifiers($key);
				}
				else
				{
					$key_name = $this->db->_protect_identifiers($key);
					$key = array($key_name);
				}

				$sql .= ",\n\tKEY {$key_name} (" . implode(', ', $key) . ")";
			}
		}
                $this->reset();
                $sql .= "\n) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
                return $sql;
    }
    
    public function add_field($field = ''){
        	if ($field == '')
		{
			throw new Exception('Field information is required.');
		}
		if (is_array($field))
		{
			$this->fields = array_merge($this->fields, $field);
		}
        if(is_string($field)){
            		if ($field == 'id')
			{
				$this->add_field(array(
										'id' => array(
													'type' => 'INT',
													'constraint' => 9,
													'auto_increment' => TRUE
													)
								));
				$this->add_key('id', TRUE);
			}
                        else
			{
				if (strpos($field, ' ') === FALSE)
				{
                                    throw new Exception('Field information is required for that operation.');
				}

				$this->fields[] = $field;
			}
        }     
    }
    
    public function add_key($key = '', $primary = FALSE){
        	if (is_array($key))
		{
			foreach ($key as $one)
			{
				$this->add_key($one, $primary);
			}

			return;
		}
                if ($key == '')
		{
			throw new Exception('无效键名');
		}
		if ($primary === TRUE)
		{
			$this->primary_keys[] = $key;
		}
		else
		{
			$this->keys[] = $key;
		}                
    }
    
    	public function rename_table($table_name, $new_table_name)
	{
            $table_name = $this->db->tablepre.'u_m_' . $table_name;
            $new_table_name = $this->db->tablepre.'u_m_' . $new_table_name;
            $sql = 'ALTER TABLE '.$this->_protect_identifiers($table_name)." RENAME TO ".$this->_protect_identifiers($new_table_name);
            return $this->db->query($sql);
	}

    public function check_table_exited($table_name){
        $table_name = $this->db->tablepre.'u_m_' . $table_name;
        $sql = 'SELECT table_name FROM information_schema.TABLES WHERE table_name ='.$this->_protect_identifiers($table_name);
        return $this->db->query($sql);
    }
        
        public function modify_column($model_id, $info = array(),$old_column_name){
            $model = $this->get($model_id);
            if(!$model||!$model['name'])return false;
            $table = $this->db->tablepre.'u_m_' . $model['name'];     
            $field_array = field_behavior::on_info($info,$old_column_name);
            if($this->_modify_column($table,$field_array)){
                return true;
            }  else {
                return false;
            }     
        }
        
        function _modify_column($table = '', $field = array()){
		if ($table == '')
		{
                    throw new Exception('A table name is required for that operation.');
		}

		// add field info into field array, but we can only do one at a time
		// so we cycle through

		foreach ($field as $k => $v)
		{
			// If no name provided, use the current name
			if ( ! isset($field[$k]['name']))
			{
				$field[$k]['name'] = $k;
			}

			$this->add_field(array($k => $field[$k]));

			if (count($this->fields) == 0)
			{
				throw new Exception('Field information is required.');
			}

			$sql = $this->_alter_table('CHANGE', $table, $this->fields);

			$this->reset();

			if ($this->db->query($sql) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;            
        }

	function _alter_table($alter_type, $table, $fields, $after_field = '')
	{
		$sql = 'ALTER TABLE '.$this->_protect_identifiers($table)." $alter_type ";

		// DROP has everything it needs now.
		if ($alter_type == 'DROP')
		{
			return $sql.$this->_protect_identifiers($fields);
		}

		$sql .= $this->_process_fields($fields);

		if ($after_field != '')
		{
			$sql .= ' AFTER ' . $this->_protect_identifiers($after_field);
		}

		return $sql;
	}        

        /**
	 * Process Fields
	 *
	 * @access	private
	 * @param	mixed	the fields
	 * @return	string
	 */
	function _process_fields($fields)
	{
		$current_field_count = 0;
		$sql = '';

		foreach ($fields as $field=>$attributes)
		{
                    
			// Numeric field names aren't allowed in databases, so if the key is
			// numeric, we know it was assigned by PHP and the developer manually
			// entered the field information, so we'll simply add it to the list
			if (is_numeric($field))
			{
				$sql .= "\n\t$attributes";
			}
			else
			{
				$attributes = array_change_key_case($attributes, CASE_UPPER);

				$sql .= "\n\t".$this->_protect_identifiers($field);

				if (array_key_exists('NAME', $attributes))
				{
					$sql .= ' '.$this->_protect_identifiers($attributes['NAME']).' ';
				}

				if (array_key_exists('TYPE', $attributes))
				{
					$sql .=  ' '.$attributes['TYPE'];

					if (array_key_exists('CONSTRAINT', $attributes))
					{
						switch ($attributes['TYPE'])
						{
							case 'decimal':
							case 'float':
							case 'numeric':
								$sql .= '('.implode(',', $attributes['CONSTRAINT']).')';
							break;

							case 'enum':
							case 'set':
								$sql .= '("'.implode('","', $attributes['CONSTRAINT']).'")';
							break;

							default:
								$sql .= '('.$attributes['CONSTRAINT'].')';
						}
					}
				}

				if (array_key_exists('UNSIGNED', $attributes) && $attributes['UNSIGNED'] === TRUE)
				{
					$sql .= ' UNSIGNED';
				}

				if (array_key_exists('DEFAULT', $attributes))
				{
					$sql .= ' DEFAULT \''.$attributes['DEFAULT'].'\'';
				}

				if (array_key_exists('NULL', $attributes) && $attributes['NULL'] === TRUE)
				{
					$sql .= ' NULL';
				}
				else
				{
					$sql .= ' NOT NULL';
				}

				if (array_key_exists('AUTO_INCREMENT', $attributes) && $attributes['AUTO_INCREMENT'] === TRUE)
				{
					$sql .= ' AUTO_INCREMENT';
				}
			}

			// don't add a comma on the end of the last field
			if (++$current_field_count < count($fields))
			{
				$sql .= ',';
			}
		}

		return $sql;
	}    
    
    	function reset()
	{
		$this->fields		= array();
		$this->keys		= array();
		$this->primary_keys	= array();
	}
}
