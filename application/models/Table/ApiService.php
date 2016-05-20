<?php
class Table_ApiService
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ApiService();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ApiService();
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
    public function update($row, $value, $field = "as_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "as_id")
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
    public function getByField($value, $field = 'as_id', $colums = "*")
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
        
        if(isset($condition["as_code"]) && $condition["as_code"] != ""){
            $select->where("as_code = ?",$condition["as_code"]);
        }
        if(isset($condition["as_name"]) && $condition["as_name"] != ""){
            $select->where("as_name = ?",$condition["as_name"]);
        }
        if(isset($condition["as_type"]) && $condition["as_type"] != ""){
            $select->where("as_type = ?",$condition["as_type"]);
        }
        if(isset($condition["as_is_authorize"]) && $condition["as_is_authorize"] != ""){
            $select->where("as_is_authorize = ?",$condition["as_is_authorize"]);
        }
        if(isset($condition["as_status"]) && $condition["as_status"] != ""){
            $select->where("as_status = ?",$condition["as_status"]);
        }
        if(isset($condition["as_user"]) && $condition["as_user"] != ""){
            $select->where("as_user = ?",$condition["as_user"]);
        }
        if(isset($condition["as_pwd"]) && $condition["as_pwd"] != ""){
            $select->where("as_pwd = ?",$condition["as_pwd"]);
        }
        if(isset($condition["cig_user"]) && $condition["cig_user"] != ""){
            $select->where("cig_user = ?",$condition["cig_user"]);
        }
        if(isset($condition["cig_pwd"]) && $condition["cig_pwd"] != ""){
            $select->where("cig_pwd = ?",$condition["cig_pwd"]);
        }
        if(isset($condition["as_token"]) && $condition["as_token"] != ""){
            $select->where("as_token = ?",$condition["as_token"]);
        }
        if(isset($condition["as_address"]) && $condition["as_address"] != ""){
            $select->where("as_address = ?",$condition["as_address"]);
        }
        if(isset($condition["as_address1"]) && $condition["as_address1"] != ""){
            $select->where("as_address1 = ?",$condition["as_address1"]);
        }
        if(isset($condition["as_address2"]) && $condition["as_address2"] != ""){
            $select->where("as_address2 = ?",$condition["as_address2"]);
        }
        if(isset($condition["as_application"]) && $condition["as_application"] != ""){
            $select->where("as_application = ?",$condition["as_application"]);
        }
        if(isset($condition["as_environment"]) && $condition["as_environment"] != ""){
            $select->where("as_environment = ?",$condition["as_environment"]);
        }
        if(isset($condition["as_account"]) && $condition["as_account"] != ""){
            $select->where("as_account = ?",$condition["as_account"]);
        }
        if(isset($condition["as_ekp"]) && $condition["as_ekp"] != ""){
            $select->where("as_ekp = ?",$condition["as_ekp"]);
        }
        if(isset($condition["as_partner"]) && $condition["as_partner"] != ""){
            $select->where("as_partner = ?",$condition["as_partner"]);
        }
        if(isset($condition["as_ignore_exception"]) && $condition["as_ignore_exception"] != ""){
            $select->where("as_ignore_exception = ?",$condition["as_ignore_exception"]);
        }
        if(isset($condition["as_logo_image"]) && $condition["as_logo_image"] != ""){
            $select->where("as_logo_image = ?",$condition["as_logo_image"]);
        }
        if(isset($condition["as_add_date"]) && $condition["as_add_date"] != ""){
            $select->where("as_add_date = ?",$condition["as_add_date"]);
        }
        if(isset($condition["as_update_date"]) && $condition["as_update_date"] != ""){
            $select->where("as_update_date = ?",$condition["as_update_date"]);
        }
        if(isset($condition["as_creater"]) && $condition["as_creater"] != ""){
            $select->where("as_creater = ?",$condition["as_creater"]);
        }
        if(isset($condition["as_updater"]) && $condition["as_updater"] != ""){
            $select->where("as_updater = ?",$condition["as_updater"]);
        }
        if(isset($condition["as_path"]) && $condition["as_path"] != ""){
            $select->where("as_path = ?",$condition["as_path"]);
        }
        if(isset($condition["as_order_confirmship_status"]) && $condition["as_order_confirmship_status"] != ""){
            $select->where("as_order_confirmship_status = ?",$condition["as_order_confirmship_status"]);
        }
        if(isset($condition["as_order_waiting_status"]) && $condition["as_order_waiting_status"] != ""){
            $select->where("as_order_waiting_status = ?",$condition["as_order_waiting_status"]);
        }
        if(isset($condition["as_width"]) && $condition["as_width"] != ""){
            $select->where("as_width = ?",$condition["as_width"]);
        }
        if(isset($condition["as_height"]) && $condition["as_height"] != ""){
            $select->where("as_height = ?",$condition["as_height"]);
        }
        if(isset($condition["as_custom_tracking_number"]) && $condition["as_custom_tracking_number"] != ""){
            $select->where("as_custom_tracking_number = ?",$condition["as_custom_tracking_number"]);
        }
        if(isset($condition["as_func"]) && $condition["as_func"] != ""){
            $select->where("as_func = ?",$condition["as_func"]);
        }
        if(isset($condition["as_print_mode"]) && $condition["as_print_mode"] != ""){
            $select->where("as_print_mode = ?",$condition["as_print_mode"]);
        }
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
}