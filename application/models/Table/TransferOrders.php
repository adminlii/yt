<?php
class Table_TransferOrders
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_TransferOrders();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_TransferOrders();
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
    public function update($row, $value, $field = "to_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "to_id")
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
    public function getByField($value, $field = 'to_id', $colums = "*")
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
    public function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "", $groupBy = '')
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["create_type"]) && $condition["create_type"] != ""){
            $select->where("create_type = ?",$condition["create_type"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?",$condition["order_status"]);
        }
        if(isset($condition["cancel_status"]) && $condition["cancel_status"] != ""){
            $select->where("cancel_status = ?",$condition["cancel_status"]);
        }
        if(isset($condition["shipping_method"]) && $condition["shipping_method"] != ""){
            $select->where("shipping_method = ?",$condition["shipping_method"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["warehouse_code"]) && $condition["warehouse_code"] != ""){
            $select->where("warehouse_code = ?",$condition["warehouse_code"]);
        }
        if(isset($condition["to_warehouse_id"]) && $condition["to_warehouse_id"] != ""){
            $select->where("to_warehouse_id = ?",$condition["to_warehouse_id"]);
        }
        if(isset($condition["to_warehouse_code"]) && $condition["to_warehouse_code"] != ""){
            $select->where("to_warehouse_code = ?",$condition["to_warehouse_code"]);
        }
        if(isset($condition["shipping_method_no"]) && $condition["shipping_method_no"] != ""){
            $select->where("shipping_method_no = ?",$condition["shipping_method_no"]);
        }
        if(isset($condition["order_weight"]) && $condition["order_weight"] != ""){
            $select->where("order_weight = ?",$condition["order_weight"]);
        }
        if(isset($condition["order_desc"]) && $condition["order_desc"] != ""){
            $select->where("order_desc = ?",$condition["order_desc"]);
        }
        
        if(isset($condition["createDateFrom"]) && $condition["createDateFrom"] != ""){
        	$select->where("date_create >= ?", $condition["createDateFrom"]);
        }
        
        if(isset($condition["createDateEnd"]) && $condition["createDateEnd"] != ""){
        	$select->where("date_create <= ?", $condition["createDateEnd"]);
        }
        if(isset($condition["order_id_arr"]) && ! empty($condition["order_id_arr"])){
        	$select->where("to_id in (?)", $condition["order_id_arr"]);
        }
        if(isset($condition["two_code_arr"]) && is_array($condition["two_code_arr"]) && count($condition["two_code_arr"]) > 0){
        	$select->where("two_code in (?)", $condition["two_code_arr"]);
        }
        
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            if (!empty($orderBy)) {
                $select->order($orderBy);
            }
            if(! empty($groupBy)){
            	$select->group($groupBy);
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