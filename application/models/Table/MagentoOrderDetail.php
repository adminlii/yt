<?php
class Table_MagentoOrderDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MagentoOrderDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MagentoOrderDetail();
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
    public function update($row, $value, $field = "mod_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "mod_id")
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
    public function getByField($value, $field = 'mod_id', $colums = "*")
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
        if(isset($condition["parent_item_id"]) && $condition["parent_item_id"] != ""){
            $select->where("parent_item_id = ?",$condition["parent_item_id"]);
        }
        if(isset($condition["quote_item_id"]) && $condition["quote_item_id"] != ""){
            $select->where("quote_item_id = ?",$condition["quote_item_id"]);
        }
        if(isset($condition["store_id"]) && $condition["store_id"] != ""){
            $select->where("store_id = ?",$condition["store_id"]);
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
        if(isset($condition["description"]) && $condition["description"] != ""){
            $select->where("description = ?",$condition["description"]);
        }
        if(isset($condition["applied_rule_ids"]) && $condition["applied_rule_ids"] != ""){
            $select->where("applied_rule_ids = ?",$condition["applied_rule_ids"]);
        }
        if(isset($condition["additional_data"]) && $condition["additional_data"] != ""){
            $select->where("additional_data = ?",$condition["additional_data"]);
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
        if(isset($condition["qty_backordered"]) && $condition["qty_backordered"] != ""){
            $select->where("qty_backordered = ?",$condition["qty_backordered"]);
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
        if(isset($condition["base_cost"]) && $condition["base_cost"] != ""){
            $select->where("base_cost = ?",$condition["base_cost"]);
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
        if(isset($condition["base_tax_before_discount"]) && $condition["base_tax_before_discount"] != ""){
            $select->where("base_tax_before_discount = ?",$condition["base_tax_before_discount"]);
        }
        if(isset($condition["tax_before_discount"]) && $condition["tax_before_discount"] != ""){
            $select->where("tax_before_discount = ?",$condition["tax_before_discount"]);
        }
        if(isset($condition["ext_order_item_id"]) && $condition["ext_order_item_id"] != ""){
            $select->where("ext_order_item_id = ?",$condition["ext_order_item_id"]);
        }
        if(isset($condition["locked_do_invoice"]) && $condition["locked_do_invoice"] != ""){
            $select->where("locked_do_invoice = ?",$condition["locked_do_invoice"]);
        }
        if(isset($condition["locked_do_ship"]) && $condition["locked_do_ship"] != ""){
            $select->where("locked_do_ship = ?",$condition["locked_do_ship"]);
        }
        if(isset($condition["price_incl_tax"]) && $condition["price_incl_tax"] != ""){
            $select->where("price_incl_tax = ?",$condition["price_incl_tax"]);
        }
        if(isset($condition["base_price_incl_tax"]) && $condition["base_price_incl_tax"] != ""){
            $select->where("base_price_incl_tax = ?",$condition["base_price_incl_tax"]);
        }
        if(isset($condition["row_total_incl_tax"]) && $condition["row_total_incl_tax"] != ""){
            $select->where("row_total_incl_tax = ?",$condition["row_total_incl_tax"]);
        }
        if(isset($condition["base_row_total_incl_tax"]) && $condition["base_row_total_incl_tax"] != ""){
            $select->where("base_row_total_incl_tax = ?",$condition["base_row_total_incl_tax"]);
        }
        if(isset($condition["hidden_tax_amount"]) && $condition["hidden_tax_amount"] != ""){
            $select->where("hidden_tax_amount = ?",$condition["hidden_tax_amount"]);
        }
        if(isset($condition["base_hidden_tax_amount"]) && $condition["base_hidden_tax_amount"] != ""){
            $select->where("base_hidden_tax_amount = ?",$condition["base_hidden_tax_amount"]);
        }
        if(isset($condition["hidden_tax_invoiced"]) && $condition["hidden_tax_invoiced"] != ""){
            $select->where("hidden_tax_invoiced = ?",$condition["hidden_tax_invoiced"]);
        }
        if(isset($condition["base_hidden_tax_invoiced"]) && $condition["base_hidden_tax_invoiced"] != ""){
            $select->where("base_hidden_tax_invoiced = ?",$condition["base_hidden_tax_invoiced"]);
        }
        if(isset($condition["hidden_tax_refunded"]) && $condition["hidden_tax_refunded"] != ""){
            $select->where("hidden_tax_refunded = ?",$condition["hidden_tax_refunded"]);
        }
        if(isset($condition["base_hidden_tax_refunded"]) && $condition["base_hidden_tax_refunded"] != ""){
            $select->where("base_hidden_tax_refunded = ?",$condition["base_hidden_tax_refunded"]);
        }
        if(isset($condition["is_nominal"]) && $condition["is_nominal"] != ""){
            $select->where("is_nominal = ?",$condition["is_nominal"]);
        }
        if(isset($condition["tax_canceled"]) && $condition["tax_canceled"] != ""){
            $select->where("tax_canceled = ?",$condition["tax_canceled"]);
        }
        if(isset($condition["hidden_tax_canceled"]) && $condition["hidden_tax_canceled"] != ""){
            $select->where("hidden_tax_canceled = ?",$condition["hidden_tax_canceled"]);
        }
        if(isset($condition["tax_refunded"]) && $condition["tax_refunded"] != ""){
            $select->where("tax_refunded = ?",$condition["tax_refunded"]);
        }
        if(isset($condition["base_tax_refunded"]) && $condition["base_tax_refunded"] != ""){
            $select->where("base_tax_refunded = ?",$condition["base_tax_refunded"]);
        }
        if(isset($condition["discount_refunded"]) && $condition["discount_refunded"] != ""){
            $select->where("discount_refunded = ?",$condition["discount_refunded"]);
        }
        if(isset($condition["base_discount_refunded"]) && $condition["base_discount_refunded"] != ""){
            $select->where("base_discount_refunded = ?",$condition["base_discount_refunded"]);
        }
        if(isset($condition["gift_message_id"]) && $condition["gift_message_id"] != ""){
            $select->where("gift_message_id = ?",$condition["gift_message_id"]);
        }
        if(isset($condition["gift_message_available"]) && $condition["gift_message_available"] != ""){
            $select->where("gift_message_available = ?",$condition["gift_message_available"]);
        }
        if(isset($condition["base_weee_tax_applied_amount"]) && $condition["base_weee_tax_applied_amount"] != ""){
            $select->where("base_weee_tax_applied_amount = ?",$condition["base_weee_tax_applied_amount"]);
        }
        if(isset($condition["base_weee_tax_applied_row_amnt"]) && $condition["base_weee_tax_applied_row_amnt"] != ""){
            $select->where("base_weee_tax_applied_row_amnt = ?",$condition["base_weee_tax_applied_row_amnt"]);
        }
        if(isset($condition["base_weee_tax_applied_row_amount"]) && $condition["base_weee_tax_applied_row_amount"] != ""){
            $select->where("base_weee_tax_applied_row_amount = ?",$condition["base_weee_tax_applied_row_amount"]);
        }
        if(isset($condition["weee_tax_applied_amount"]) && $condition["weee_tax_applied_amount"] != ""){
            $select->where("weee_tax_applied_amount = ?",$condition["weee_tax_applied_amount"]);
        }
        if(isset($condition["weee_tax_applied_row_amount"]) && $condition["weee_tax_applied_row_amount"] != ""){
            $select->where("weee_tax_applied_row_amount = ?",$condition["weee_tax_applied_row_amount"]);
        }
        if(isset($condition["weee_tax_applied"]) && $condition["weee_tax_applied"] != ""){
            $select->where("weee_tax_applied = ?",$condition["weee_tax_applied"]);
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
        if(isset($condition["has_children"]) && $condition["has_children"] != ""){
            $select->where("has_children = ?",$condition["has_children"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["create_time_sys"]) && $condition["create_time_sys"] != ""){
            $select->where("create_time_sys = ?",$condition["create_time_sys"]);
        }
        if(isset($condition["update_time_sys"]) && $condition["update_time_sys"] != ""){
            $select->where("update_time_sys = ?",$condition["update_time_sys"]);
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