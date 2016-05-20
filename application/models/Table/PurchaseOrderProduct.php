<?php
class Table_PurchaseOrderProduct
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PurchaseOrderProduct();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PurchaseOrderProduct();
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
    public function update($row, $value, $field = "pop_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pop_id")
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
    public function getByField($value, $field = 'pop_id', $colums = "*")
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
        
        if(isset($condition["po_id"]) && $condition["po_id"] != ""){
            $select->where("po_id = ?",$condition["po_id"]);
        }
        if(isset($condition["po_code"]) && $condition["po_code"] != ""){
            $select->where("po_code = ?",$condition["po_code"]);
        }
        if(isset($condition["po_status"]) && $condition["po_status"] != ""){
            $select->where("po_status = ?",$condition["po_status"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["qty_expected"]) && $condition["qty_expected"] != ""){
            $select->where("qty_expected = ?",$condition["qty_expected"]);
        }
        if(isset($condition["qty_eta"]) && $condition["qty_eta"] != ""){
            $select->where("qty_eta = ?",$condition["qty_eta"]);
        }
        if(isset($condition["qty_receving"]) && $condition["qty_receving"] != ""){
            $select->where("qty_receving = ?",$condition["qty_receving"]);
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
        if(isset($condition["unit_price"]) && $condition["unit_price"] != ""){
            $select->where("unit_price = ?",$condition["unit_price"]);
        }
        if(isset($condition["shipping_method_id"]) && $condition["shipping_method_id"] != ""){
            $select->where("shipping_method_id = ?",$condition["shipping_method_id"]);
        }
        if(isset($condition["plan_type"]) && $condition["plan_type"] != ""){
            $select->where("plan_type = ?",$condition["plan_type"]);
        }
        if(isset($condition["pop_update_time"]) && $condition["pop_update_time"] != ""){
            $select->where("pop_update_time = ?",$condition["pop_update_time"]);
        }
        if(isset($condition["note"]) && $condition["note"] != ""){
            $select->where("note = ?",$condition["note"]);
        }
        if(isset($condition["qty_pay"]) && $condition["qty_pay"] != ""){
            $select->where("qty_pay = ?",$condition["qty_pay"]);
        }
        if(isset($condition["receiving_exception"]) && $condition["receiving_exception"] != ""){
            $select->where("receiving_exception = ?",$condition["receiving_exception"]);
        }
        if(isset($condition["receiving_exception_handle"]) && $condition["receiving_exception_handle"] != ""){
            $select->where("receiving_exception_handle = ?",$condition["receiving_exception_handle"]);
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
    
    public function getByFieldJoinLeft($value, $field = 'po_id', $colums = "*")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $colums);
    	$select->joinLeft('product','purchase_order_product.product_id =  product.product_id',array('product_sku','product_barcode','product_title_en'));
    	$select->joinLeft('purchase_order_status','purchase_order_product.po_status =  purchase_order_status.po_staus',array('name_cn'));
    	$select->where("{$field} = ?", $value);
    	return $this->_table->getAdapter()->fetchAll($select);
    }
}