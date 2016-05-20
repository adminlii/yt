<?php
class Table_Customer
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Customer();
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public function getByField($value, $field = 'customer_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $dbname = $this->_table->info('schema');
        $select->from($dbname.'.'.$table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
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
        $dbname = $this->_table->info('schema');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["company_code_arr"]) && !empty($condition["company_code_arr"])){
            $select->where("company_code in (?)",$condition["company_code_arr"]);
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["customer_password"]) && $condition["customer_password"] != ""){
            $select->where("customer_password = ?",$condition["customer_password"]);
        }
        if(isset($condition["customer_firstname"]) && $condition["customer_firstname"] != ""){
            $select->where("customer_firstname = ?",$condition["customer_firstname"]);
        }
        if(isset($condition["customer_lastname"]) && $condition["customer_lastname"] != ""){
            $select->where("customer_lastname = ?",$condition["customer_lastname"]);
        }
        if(isset($condition["customer_email"]) && $condition["customer_email"] != ""){
            $select->where("customer_email = ?",$condition["customer_email"]);
        }
        if(isset($condition["customer_currency"]) && $condition["customer_currency"] != ""){
            $select->where("customer_currency = ?",$condition["customer_currency"]);
        }
        if(isset($condition["customer_telephone"]) && $condition["customer_telephone"] != ""){
            $select->where("customer_telephone = ?",$condition["customer_telephone"]);
        }
        if(isset($condition["customer_status"]) && $condition["customer_status"] != ""){
            $select->where("customer_status = ?",$condition["customer_status"]);
        }
        if(isset($condition["customer_saler_user_id"]) && $condition["customer_saler_user_id"] != ""){
            $select->where("customer_saler_user_id = ?",$condition["customer_saler_user_id"]);
        }
        if(isset($condition["customer_cser_user_id"]) && $condition["customer_cser_user_id"] != ""){
            $select->where("customer_cser_user_id = ?",$condition["customer_cser_user_id"]);
        }
        if(isset($condition["customer_verify_code"]) && $condition["customer_verify_code"] != ""){
            $select->where("customer_verify_code = ?",$condition["customer_verify_code"]);
        }
        if(isset($condition["customer_signature"]) && $condition["customer_signature"] != ""){
            $select->where("customer_signature = ?",$condition["customer_signature"]);
        }
        if(isset($condition["reg_step"]) && $condition["reg_step"] != ""){
            $select->where("reg_step = ?",$condition["reg_step"]);
        }
        if(isset($condition["password_update_time"]) && $condition["password_update_time"] != ""){
            $select->where("password_update_time = ?",$condition["password_update_time"]);
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