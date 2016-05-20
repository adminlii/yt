<?php
class Table_OrderProductSplit
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderProductSplit();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderProductSplit();
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
    public function update($row, $value, $field = "op_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "op_id")
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
    public function getByField($value, $field = 'op_id', $colums = "*")
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
        
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["warehouse_sku"]) && $condition["warehouse_sku"] != ""){
            $select->where("warehouse_sku = ?",$condition["warehouse_sku"]);
        }
        if(isset($condition["product_title"]) && $condition["product_title"] != ""){
            $select->where("product_title = ?",$condition["product_title"]);
        }
        if(isset($condition["op_quantity"]) && $condition["op_quantity"] != ""){
            $select->where("op_quantity = ?",$condition["op_quantity"]);
        }
        if(isset($condition["op_ref_tnx"]) && $condition["op_ref_tnx"] != ""){
            $select->where("op_ref_tnx = ?",$condition["op_ref_tnx"]);
        }
        if(isset($condition["op_recv_account"]) && $condition["op_recv_account"] != ""){
            $select->where("op_recv_account = ?",$condition["op_recv_account"]);
        }
        if(isset($condition["op_ref_item_id"]) && $condition["op_ref_item_id"] != ""){
            $select->where("op_ref_item_id = ?",$condition["op_ref_item_id"]);
        }
        if(isset($condition["op_site"]) && $condition["op_site"] != ""){
            $select->where("op_site = ?",$condition["op_site"]);
        }
        if(isset($condition["op_record_id"]) && $condition["op_record_id"] != ""){
            $select->where("op_record_id = ?",$condition["op_record_id"]);
        }
        if(isset($condition["op_ref_buyer_id"]) && $condition["op_ref_buyer_id"] != ""){
            $select->where("op_ref_buyer_id = ?",$condition["op_ref_buyer_id"]);
        }
        if(isset($condition["OrderID"]) && $condition["OrderID"] != ""){
            $select->where("OrderID = ?",$condition["OrderID"]);
        }
        if(isset($condition["OrderIDEbay"]) && $condition["OrderIDEbay"] != ""){
            $select->where("OrderIDEbay = ?",$condition["OrderIDEbay"]);
        }
        if(isset($condition["is_modify"]) && $condition["is_modify"] != ""){
            $select->where("is_modify = ?",$condition["is_modify"]);
        }
        if(isset($condition["pic"]) && $condition["pic"] != ""){
            $select->where("pic = ?",$condition["pic"]);
        }
        if(isset($condition["url"]) && $condition["url"] != ""){
            $select->where("url = ?",$condition["url"]);
        }
        if(isset($condition["transaction_price"]) && $condition["transaction_price"] != ""){
            $select->where("transaction_price = ?",$condition["transaction_price"]);
        }
        if(isset($condition["unit_price"]) && $condition["unit_price"] != ""){
            $select->where("unit_price = ?",$condition["unit_price"]);
        }
        if(isset($condition["unit_finalvaluefee"]) && $condition["unit_finalvaluefee"] != ""){
            $select->where("unit_finalvaluefee = ?",$condition["unit_finalvaluefee"]);
        }
        if(isset($condition["unit_platformfee"]) && $condition["unit_platformfee"] != ""){
            $select->where("unit_platformfee = ?",$condition["unit_platformfee"]);
        }
        if(isset($condition["unit_shipfee"]) && $condition["unit_shipfee"] != ""){
            $select->where("unit_shipfee = ?",$condition["unit_shipfee"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["sync_status"]) && $condition["sync_status"] != ""){
            $select->where("sync_status = ?",$condition["sync_status"]);
        }
        if(isset($condition["give_up"]) && $condition["give_up"] != ""){
            $select->where("give_up = ?",$condition["give_up"]);
        }
        if(isset($condition["create_type"]) && $condition["create_type"] != ""){
            $select->where("create_type = ?",$condition["create_type"]);
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