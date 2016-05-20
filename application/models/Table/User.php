<?php
class Table_User
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_User();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_User();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "user_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "user_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->delete($where);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public function getByField($value, $field = 'user_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }

    public function getAll()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, "*");
        return $this->_table->getAdapter()->fetchAll($select);
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }

        if(isset($condition["user_code"]) && $condition["user_code"] != ""){
        	$select->where("user_code = ?",$condition["user_code"]);
        }
        if(isset($condition["customer_channelid"]) && $condition["customer_channelid"] != ""){
            $select->where("customer_channelid = ?",$condition["customer_channelid"]);
        }
        if(isset($condition["user_password"]) && $condition["user_password"] != ""){
            $select->where("user_password = ?",$condition["user_password"]);
        }
        if(isset($condition["user_name"]) && $condition["user_name"] != ""){
            $select->where("user_name = ?",$condition["user_name"]);
        }
        if(isset($condition["user_name_en"]) && $condition["user_name_en"] != ""){
            $select->where("user_name_en = ?",$condition["user_name_en"]);
        }
        if(isset($condition["user_status"]) && $condition["user_status"] != ""){
            $select->where("user_status = ?",$condition["user_status"]);
        }
        if(isset($condition["user_email"]) && $condition["user_email"] != ""){
            $select->where("user_email = ?",$condition["user_email"]);
        }
        if(isset($condition["ud_id"]) && $condition["ud_id"] != ""){
            $select->where("ud_id = ?",$condition["ud_id"]);
        }
        if(isset($condition["up_id"]) && $condition["up_id"] != ""){
            $select->where("up_id = ?",$condition["up_id"]);
        }
        if(isset($condition["user_password_update_time"]) && $condition["user_password_update_time"] != ""){
            $select->where("user_password_update_time = ?",$condition["user_password_update_time"]);
        }
        if(isset($condition["user_phone"]) && $condition["user_phone"] != ""){
            $select->where("user_phone = ?",$condition["user_phone"]);
        }
        if(isset($condition["user_mobile_phone"]) && $condition["user_mobile_phone"] != ""){
            $select->where("user_mobile_phone = ?",$condition["user_mobile_phone"]);
        }
        if(isset($condition["user_note"]) && $condition["user_note"] != ""){
            $select->where("user_note = ?",$condition["user_note"]);
        }
        if(isset($condition["user_supervisor_id"]) && $condition["user_supervisor_id"] != ""){
            $select->where("user_supervisor_id = ?",$condition["user_supervisor_id"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["is_admin"]) && $condition["is_admin"] != ""){
            $select->where("is_admin = ?",$condition["is_admin"]);
        }
        
        if(isset($condition["platform_token"]) && $condition["platform_token"] != ""){
        	$select->where("platform_token = ?",$condition["platform_token"]);
        }
        
        if(isset($condition["user_sources"]) && $condition["user_sources"] != ""){
        	$select->where("user_sources = ?",$condition["user_sources"]);
        }

        if(isset($condition["user_activate_code"]) && $condition["user_activate_code"] != ""){
        	$select->where("user_activate_code = ?",$condition["user_activate_code"]);
        }

        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }

        if(isset($condition["parent_user_id"]) && $condition["parent_user_id"] != ""){
        	$select->where("parent_user_id = ?",$condition["parent_user_id"]);
        }

        if(isset($condition["auth_user_id"]) && $condition["auth_user_id"] != ""){
        	$select->where("parent_user_id = ? or user_id = ?",$condition["auth_user_id"]);
        }
        
        
//         echo $select;exit;
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            if (!empty($orderBy)) {
                $select->order($orderBy);
            }
            if ($pageSize > 0 and $page > 0) {
                $start = ($page - 1) * $pageSize;
                $select->limit($pageSize, $start);
            }
            $sql = $select->__toString();
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
    
    //用于执行in语句
    public function getConditionByIn($value, $field = 'user_id', $colums = "*"){
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $colums);
    	$select->where("{$field} in (?) ", $value);
    	$sql = $select->__toString();
    	return $this->_table->getAdapter()->fetchAll($sql);
    }
}