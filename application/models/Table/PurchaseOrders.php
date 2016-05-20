<?php
class Table_PurchaseOrders
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PurchaseOrders();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PurchaseOrders();
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
    public function update($row, $value, $field = "po_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "po_id")
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
    public function getByField($value, $field = 'po_id', $colums = "*")
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
        
        if(isset($condition["po_code"]) && $condition["po_code"] != ""){
            $select->where("po_code = ?",$condition["po_code"]);
        }
        if(isset($condition["create_type"]) && $condition["create_type"] != ""){
            $select->where("create_type = ?",$condition["create_type"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["shipping_method_id_head"]) && $condition["shipping_method_id_head"] != ""){
            $select->where("shipping_method_id_head = ?",$condition["shipping_method_id_head"]);
        }
        if(isset($condition["tracking_no"]) && $condition["tracking_no"] != ""){
            $select->where("tracking_no = ?",$condition["tracking_no"]);
        }
        if(isset($condition["ref_no"]) && $condition["ref_no"] != ""){
            $select->where("ref_no = ?",$condition["ref_no"]);
        }
        if(isset($condition["supplier_id"]) && $condition["supplier_id"] != ""){
            $select->where("supplier_id = ?",$condition["supplier_id"]);
        }
        if(isset($condition["payable_amount"]) && $condition["payable_amount"] != ""){
            $select->where("payable_amount = ?",$condition["payable_amount"]);
        }
        if(isset($condition["actually_amount"]) && $condition["actually_amount"] != ""){
            $select->where("actually_amount = ?",$condition["actually_amount"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["pay_status"]) && $condition["pay_status"] != ""){
            $select->where("pay_status = ?",$condition["pay_status"]);
        }
        if(isset($condition["po_status"]) && $condition["po_status"] != ""){
            $select->where("po_status = ?",$condition["po_status"]);
        }
        if(isset($condition["po_type"]) && $condition["po_type"] != ""){
            $select->where("po_type = ?",$condition["po_type"]);
        }
        if(isset($condition["date_release"]) && $condition["date_release"] != ""){
            $select->where("date_release = ?",$condition["date_release"]);
        }
        if(isset($condition["date_create"]) && $condition["date_create"] != ""){
            $select->where("date_create = ?",$condition["date_create"]);
        }
        if(isset($condition["operator_create"]) && $condition["operator_create"] != ""){
            $select->where("operator_create = ?",$condition["operator_create"]);
        }
        if(isset($condition["operator_release"]) && $condition["operator_release"] != ""){
            $select->where("operator_release = ?",$condition["operator_release"]);
        }
        if(isset($condition["operator_purchase"]) && $condition["operator_purchase"] != ""){
            $select->where("operator_purchase = ?",$condition["operator_purchase"]);
        }
        if(isset($condition["date_eta"]) && $condition["date_eta"] != ""){
            $select->where("date_eta = ?",$condition["date_eta"]);
        }
        if(isset($condition["po_update_time"]) && $condition["po_update_time"] != ""){
            $select->where("po_update_time = ?",$condition["po_update_time"]);
        }
        if(isset($condition["to_warehouse_id"]) && $condition["to_warehouse_id"] != ""){
            $select->where("to_warehouse_id = ?",$condition["to_warehouse_id"]);
        }
        if(isset($condition["pay_ship_amount"]) && $condition["pay_ship_amount"] != ""){
            $select->where("pay_ship_amount = ?",$condition["pay_ship_amount"]);
        }
        if(isset($condition["receiving_exception"]) && $condition["receiving_exception"] != ""){
            $select->where("receiving_exception = ?",$condition["receiving_exception"]);
        }
        if(isset($condition["receiving_exception_handle"]) && $condition["receiving_exception_handle"] != ""){
            $select->where("receiving_exception_handle = ?",$condition["receiving_exception_handle"]);
        }
        if(isset($condition["currency_rate"]) && $condition["currency_rate"] != ""){
            $select->where("currency_rate = ?",$condition["currency_rate"]);
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