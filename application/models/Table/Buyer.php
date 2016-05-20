<?php
class Table_Buyer
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Buyer();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Buyer();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        $row['create_time'] = date('Y-m-d H:i:s');
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "bid")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "bid")
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
    public function getByField($value, $field = 'bid', $colums = "*")
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
        
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["buyer_account"]) && $condition["buyer_account"] != ""){
            $select->where("buyer_account = ?",$condition["buyer_account"]);
        }
        if(isset($condition["buyer_name"]) && $condition["buyer_name"] != ""){
            $select->where("buyer_name = ?",$condition["buyer_name"]);
        }
        if(isset($condition["buyer_email"]) && $condition["buyer_email"] != ""){
            $select->where("buyer_email = ?",$condition["buyer_email"]);
        }
        
        if(isset($condition["buyer_account_arr"]) && !empty($condition["buyer_account_arr"])){
            $select->where("buyer_account in (?)",$condition["buyer_account_arr"]);
        }
        if(isset($condition["buyer_name_arr"]) && !empty($condition["buyer_name_arr"])){
            $select->where("buyer_name in (?)",$condition["buyer_name_arr"]);
        }
        if(isset($condition["buyer_email_arr"]) && !empty($condition["buyer_email_arr"])){
            $select->where("buyer_email in (?)",$condition["buyer_email_arr"]);
        }

        if(isset($condition["user_account_arr"]) && !empty($condition["user_account_arr"])){
            $select->where("user_account in (?)",$condition["user_account_arr"]);
        }
        
        
        if(isset($condition["buyer_country"]) && $condition["buyer_country"] != ""){
            $select->where("buyer_country = ?",$condition["buyer_country"]);
        }
        if(isset($condition["buyer_tel"]) && $condition["buyer_tel"] != ""){
            $select->where("buyer_tel = ?",$condition["buyer_tel"]);
        }
        if(isset($condition["buyer_credit_count"]) && $condition["buyer_credit_count"] != ""){
            $select->where("buyer_credit_count = ?",$condition["buyer_credit_count"]);
        }
        if(isset($condition["buyer_level"]) && $condition["buyer_level"] != ""){
            $select->where("buyer_level = ?",$condition["buyer_level"]);
        }
        

        if(isset($condition["order_count_from"]) && $condition["order_count_from"] != ""){
            $select->where("order_count >= ?",$condition["order_count_from"]+0);
        }

        if(isset($condition["order_count_to"]) && $condition["order_count_to"] != ""){
            $select->where("order_count <= ?",$condition["order_count_to"]+0);
        }

        if(isset($condition["total_amountpaid_from"]) && $condition["total_amountpaid_from"] != ""){
            $select->where("total_amountpaid >= ?",$condition["total_amountpaid_from"]+0);
        }

        if(isset($condition["total_amountpaid_to"]) && $condition["total_amountpaid_to"] != ""){
            $select->where("total_amountpaid <= ?",$condition["total_amountpaid_to"]+0);
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
}