<?php
class Table_MagentoOrderItems
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MagentoOrderItems();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MagentoOrderItems();
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
    public function update($row, $value, $field = "moi_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "moi_id")
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
    public function getByField($value, $field = 'moi_id', $colums = "*")
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
        
        if(isset($condition["mo_id"]) && $condition["mo_id"] != ""){
            $select->where("mo_id = ?",$condition["mo_id"]);
        }
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["quote_item_id"]) && $condition["quote_item_id"] != ""){
            $select->where("quote_item_id = ?",$condition["quote_item_id"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_type"]) && $condition["product_type"] != ""){
            $select->where("product_type = ?",$condition["product_type"]);
        }
        if(isset($condition["product_options"]) && $condition["product_options"] != ""){
            $select->where("product_options = ?",$condition["product_options"]);
        }
        if(isset($condition["weight"]) && $condition["weight"] != ""){
            $select->where("weight = ?",$condition["weight"]);
        }
        if(isset($condition["is_virtual"]) && $condition["is_virtual"] != ""){
            $select->where("is_virtual = ?",$condition["is_virtual"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["name"]) && $condition["name"] != ""){
            $select->where("name = ?",$condition["name"]);
        }
        if(isset($condition["free_shipping"]) && $condition["free_shipping"] != ""){
            $select->where("free_shipping = ?",$condition["free_shipping"]);
        }
        if(isset($condition["is_qty_decimal"]) && $condition["is_qty_decimal"] != ""){
            $select->where("is_qty_decimal = ?",$condition["is_qty_decimal"]);
        }
        if(isset($condition["no_discount"]) && $condition["no_discount"] != ""){
            $select->where("no_discount = ?",$condition["no_discount"]);
        }
        if(isset($condition["qty_canceled"]) && $condition["qty_canceled"] != ""){
            $select->where("qty_canceled = ?",$condition["qty_canceled"]);
        }
        if(isset($condition["qty_invoiced"]) && $condition["qty_invoiced"] != ""){
            $select->where("qty_invoiced = ?",$condition["qty_invoiced"]);
        }
        if(isset($condition["qty_ordered"]) && $condition["qty_ordered"] != ""){
            $select->where("qty_ordered = ?",$condition["qty_ordered"]);
        }
        if(isset($condition["qty_refunded"]) && $condition["qty_refunded"] != ""){
            $select->where("qty_refunded = ?",$condition["qty_refunded"]);
        }
        if(isset($condition["qty_shipped"]) && $condition["qty_shipped"] != ""){
            $select->where("qty_shipped = ?",$condition["qty_shipped"]);
        }
        if(isset($condition["price"]) && $condition["price"] != ""){
            $select->where("price = ?",$condition["price"]);
        }
        if(isset($condition["base_price"]) && $condition["base_price"] != ""){
            $select->where("base_price = ?",$condition["base_price"]);
        }
        if(isset($condition["original_price"]) && $condition["original_price"] != ""){
            $select->where("original_price = ?",$condition["original_price"]);
        }
        if(isset($condition["base_original_price"]) && $condition["base_original_price"] != ""){
            $select->where("base_original_price = ?",$condition["base_original_price"]);
        }
        if(isset($condition["tax_percent"]) && $condition["tax_percent"] != ""){
            $select->where("tax_percent = ?",$condition["tax_percent"]);
        }
        if(isset($condition["tax_amount"]) && $condition["tax_amount"] != ""){
            $select->where("tax_amount = ?",$condition["tax_amount"]);
        }
        if(isset($condition["base_tax_amount"]) && $condition["base_tax_amount"] != ""){
            $select->where("base_tax_amount = ?",$condition["base_tax_amount"]);
        }
        if(isset($condition["tax_invoiced"]) && $condition["tax_invoiced"] != ""){
            $select->where("tax_invoiced = ?",$condition["tax_invoiced"]);
        }
        if(isset($condition["base_tax_invoiced"]) && $condition["base_tax_invoiced"] != ""){
            $select->where("base_tax_invoiced = ?",$condition["base_tax_invoiced"]);
        }
        if(isset($condition["discount_percent"]) && $condition["discount_percent"] != ""){
            $select->where("discount_percent = ?",$condition["discount_percent"]);
        }
        if(isset($condition["discount_amount"]) && $condition["discount_amount"] != ""){
            $select->where("discount_amount = ?",$condition["discount_amount"]);
        }
        if(isset($condition["base_discount_amount"]) && $condition["base_discount_amount"] != ""){
            $select->where("base_discount_amount = ?",$condition["base_discount_amount"]);
        }
        if(isset($condition["discount_invoiced"]) && $condition["discount_invoiced"] != ""){
            $select->where("discount_invoiced = ?",$condition["discount_invoiced"]);
        }
        if(isset($condition["base_discount_invoiced"]) && $condition["base_discount_invoiced"] != ""){
            $select->where("base_discount_invoiced = ?",$condition["base_discount_invoiced"]);
        }
        if(isset($condition["amount_refunded"]) && $condition["amount_refunded"] != ""){
            $select->where("amount_refunded = ?",$condition["amount_refunded"]);
        }
        if(isset($condition["base_amount_refunded"]) && $condition["base_amount_refunded"] != ""){
            $select->where("base_amount_refunded = ?",$condition["base_amount_refunded"]);
        }
        if(isset($condition["row_total"]) && $condition["row_total"] != ""){
            $select->where("row_total = ?",$condition["row_total"]);
        }
        if(isset($condition["base_row_total"]) && $condition["base_row_total"] != ""){
            $select->where("base_row_total = ?",$condition["base_row_total"]);
        }
        if(isset($condition["row_invoiced"]) && $condition["row_invoiced"] != ""){
            $select->where("row_invoiced = ?",$condition["row_invoiced"]);
        }
        if(isset($condition["base_row_invoiced"]) && $condition["base_row_invoiced"] != ""){
            $select->where("base_row_invoiced = ?",$condition["base_row_invoiced"]);
        }
        if(isset($condition["row_weight"]) && $condition["row_weight"] != ""){
            $select->where("row_weight = ?",$condition["row_weight"]);
        }
        if(isset($condition["weee_tax_applied"]) && $condition["weee_tax_applied"] != ""){
            $select->where("weee_tax_applied = ?",$condition["weee_tax_applied"]);
        }
        if(isset($condition["weee_tax_applied_amount"]) && $condition["weee_tax_applied_amount"] != ""){
            $select->where("weee_tax_applied_amount = ?",$condition["weee_tax_applied_amount"]);
        }
        if(isset($condition["weee_tax_applied_row_amount"]) && $condition["weee_tax_applied_row_amount"] != ""){
            $select->where("weee_tax_applied_row_amount = ?",$condition["weee_tax_applied_row_amount"]);
        }
        if(isset($condition["base_weee_tax_applied_amount"]) && $condition["base_weee_tax_applied_amount"] != ""){
            $select->where("base_weee_tax_applied_amount = ?",$condition["base_weee_tax_applied_amount"]);
        }
        if(isset($condition["base_weee_tax_applied_row_amount"]) && $condition["base_weee_tax_applied_row_amount"] != ""){
            $select->where("base_weee_tax_applied_row_amount = ?",$condition["base_weee_tax_applied_row_amount"]);
        }
        if(isset($condition["weee_tax_disposition"]) && $condition["weee_tax_disposition"] != ""){
            $select->where("weee_tax_disposition = ?",$condition["weee_tax_disposition"]);
        }
        if(isset($condition["weee_tax_row_disposition"]) && $condition["weee_tax_row_disposition"] != ""){
            $select->where("weee_tax_row_disposition = ?",$condition["weee_tax_row_disposition"]);
        }
        if(isset($condition["base_weee_tax_disposition"]) && $condition["base_weee_tax_disposition"] != ""){
            $select->where("base_weee_tax_disposition = ?",$condition["base_weee_tax_disposition"]);
        }
        if(isset($condition["base_weee_tax_row_disposition"]) && $condition["base_weee_tax_row_disposition"] != ""){
            $select->where("base_weee_tax_row_disposition = ?",$condition["base_weee_tax_row_disposition"]);
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