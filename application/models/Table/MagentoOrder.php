<?php
class Table_MagentoOrder
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MagentoOrder();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MagentoOrder();
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
    public function update($row, $value, $field = "mo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "mo_id")
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
    public function getByField($value, $field = 'mo_id', $colums = "*")
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
        
        if(isset($condition["increment_id"]) && $condition["increment_id"] != ""){
            $select->where("increment_id = ?",$condition["increment_id"]);
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
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["tax_amount"]) && $condition["tax_amount"] != ""){
            $select->where("tax_amount = ?",$condition["tax_amount"]);
        }
        if(isset($condition["shipping_amount"]) && $condition["shipping_amount"] != ""){
            $select->where("shipping_amount = ?",$condition["shipping_amount"]);
        }
        if(isset($condition["discount_amount"]) && $condition["discount_amount"] != ""){
            $select->where("discount_amount = ?",$condition["discount_amount"]);
        }
        if(isset($condition["subtotal"]) && $condition["subtotal"] != ""){
            $select->where("subtotal = ?",$condition["subtotal"]);
        }
        if(isset($condition["grand_total"]) && $condition["grand_total"] != ""){
            $select->where("grand_total = ?",$condition["grand_total"]);
        }
        if(isset($condition["total_qty_ordered"]) && $condition["total_qty_ordered"] != ""){
            $select->where("total_qty_ordered = ?",$condition["total_qty_ordered"]);
        }
        if(isset($condition["total_canceled"]) && $condition["total_canceled"] != ""){
            $select->where("total_canceled = ?",$condition["total_canceled"]);
        }
        if(isset($condition["base_tax_amount"]) && $condition["base_tax_amount"] != ""){
            $select->where("base_tax_amount = ?",$condition["base_tax_amount"]);
        }
        if(isset($condition["base_shipping_amount"]) && $condition["base_shipping_amount"] != ""){
            $select->where("base_shipping_amount = ?",$condition["base_shipping_amount"]);
        }
        if(isset($condition["base_discount_amount"]) && $condition["base_discount_amount"] != ""){
            $select->where("base_discount_amount = ?",$condition["base_discount_amount"]);
        }
        if(isset($condition["base_subtotal"]) && $condition["base_subtotal"] != ""){
            $select->where("base_subtotal = ?",$condition["base_subtotal"]);
        }
        if(isset($condition["base_grand_total"]) && $condition["base_grand_total"] != ""){
            $select->where("base_grand_total = ?",$condition["base_grand_total"]);
        }
        if(isset($condition["base_total_canceled"]) && $condition["base_total_canceled"] != ""){
            $select->where("base_total_canceled = ?",$condition["base_total_canceled"]);
        }
        if(isset($condition["billing_address_id"]) && $condition["billing_address_id"] != ""){
            $select->where("billing_address_id = ?",$condition["billing_address_id"]);
        }
        if(isset($condition["billing_firstname"]) && $condition["billing_firstname"] != ""){
            $select->where("billing_firstname = ?",$condition["billing_firstname"]);
        }
        if(isset($condition["billing_lastname"]) && $condition["billing_lastname"] != ""){
            $select->where("billing_lastname = ?",$condition["billing_lastname"]);
        }
        if(isset($condition["shipping_address_id"]) && $condition["shipping_address_id"] != ""){
            $select->where("shipping_address_id = ?",$condition["shipping_address_id"]);
        }
        if(isset($condition["shipping_firstname"]) && $condition["shipping_firstname"] != ""){
            $select->where("shipping_firstname = ?",$condition["shipping_firstname"]);
        }
        if(isset($condition["shipping_lastname"]) && $condition["shipping_lastname"] != ""){
            $select->where("shipping_lastname = ?",$condition["shipping_lastname"]);
        }
        if(isset($condition["billing_name"]) && $condition["billing_name"] != ""){
            $select->where("billing_name = ?",$condition["billing_name"]);
        }
        if(isset($condition["shipping_name"]) && $condition["shipping_name"] != ""){
            $select->where("shipping_name = ?",$condition["shipping_name"]);
        }
        if(isset($condition["store_to_base_rate"]) && $condition["store_to_base_rate"] != ""){
            $select->where("store_to_base_rate = ?",$condition["store_to_base_rate"]);
        }
        if(isset($condition["store_to_order_rate"]) && $condition["store_to_order_rate"] != ""){
            $select->where("store_to_order_rate = ?",$condition["store_to_order_rate"]);
        }
        if(isset($condition["base_to_global_rate"]) && $condition["base_to_global_rate"] != ""){
            $select->where("base_to_global_rate = ?",$condition["base_to_global_rate"]);
        }
        if(isset($condition["base_to_order_rate"]) && $condition["base_to_order_rate"] != ""){
            $select->where("base_to_order_rate = ?",$condition["base_to_order_rate"]);
        }
        if(isset($condition["weight"]) && $condition["weight"] != ""){
            $select->where("weight = ?",$condition["weight"]);
        }
        if(isset($condition["store_name"]) && $condition["store_name"] != ""){
            $select->where("store_name = ?",$condition["store_name"]);
        }
        if(isset($condition["remote_ip"]) && $condition["remote_ip"] != ""){
            $select->where("remote_ip = ?",$condition["remote_ip"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
        if(isset($condition["state"]) && $condition["state"] != ""){
            $select->where("state = ?",$condition["state"]);
        }
        if(isset($condition["applied_rule_ids"]) && $condition["applied_rule_ids"] != ""){
            $select->where("applied_rule_ids = ?",$condition["applied_rule_ids"]);
        }
        if(isset($condition["global_currency_code"]) && $condition["global_currency_code"] != ""){
            $select->where("global_currency_code = ?",$condition["global_currency_code"]);
        }
        if(isset($condition["base_currency_code"]) && $condition["base_currency_code"] != ""){
            $select->where("base_currency_code = ?",$condition["base_currency_code"]);
        }
        if(isset($condition["store_currency_code"]) && $condition["store_currency_code"] != ""){
            $select->where("store_currency_code = ?",$condition["store_currency_code"]);
        }
        if(isset($condition["order_currency_code"]) && $condition["order_currency_code"] != ""){
            $select->where("order_currency_code = ?",$condition["order_currency_code"]);
        }
        if(isset($condition["shipping_method"]) && $condition["shipping_method"] != ""){
            $select->where("shipping_method = ?",$condition["shipping_method"]);
        }
        if(isset($condition["shipping_description"]) && $condition["shipping_description"] != ""){
            $select->where("shipping_description = ?",$condition["shipping_description"]);
        }
        if(isset($condition["customer_email"]) && $condition["customer_email"] != ""){
            $select->where("customer_email = ?",$condition["customer_email"]);
        }
        if(isset($condition["customer_firstname"]) && $condition["customer_firstname"] != ""){
            $select->where("customer_firstname = ?",$condition["customer_firstname"]);
        }
        if(isset($condition["customer_lastname"]) && $condition["customer_lastname"] != ""){
            $select->where("customer_lastname = ?",$condition["customer_lastname"]);
        }
        if(isset($condition["quote_id"]) && $condition["quote_id"] != ""){
            $select->where("quote_id = ?",$condition["quote_id"]);
        }
        if(isset($condition["is_virtual"]) && $condition["is_virtual"] != ""){
            $select->where("is_virtual = ?",$condition["is_virtual"]);
        }
        if(isset($condition["customer_group_id"]) && $condition["customer_group_id"] != ""){
            $select->where("customer_group_id = ?",$condition["customer_group_id"]);
        }
        if(isset($condition["customer_note_notify"]) && $condition["customer_note_notify"] != ""){
            $select->where("customer_note_notify = ?",$condition["customer_note_notify"]);
        }
        if(isset($condition["customer_is_guest"]) && $condition["customer_is_guest"] != ""){
            $select->where("customer_is_guest = ?",$condition["customer_is_guest"]);
        }
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["coupon_code"]) && $condition["coupon_code"] != ""){
            $select->where("coupon_code = ?",$condition["coupon_code"]);
        }
        if(isset($condition["protect_code"]) && $condition["protect_code"] != ""){
            $select->where("protect_code = ?",$condition["protect_code"]);
        }
        if(isset($condition["base_discount_canceled"]) && $condition["base_discount_canceled"] != ""){
            $select->where("base_discount_canceled = ?",$condition["base_discount_canceled"]);
        }
        if(isset($condition["base_shipping_canceled"]) && $condition["base_shipping_canceled"] != ""){
            $select->where("base_shipping_canceled = ?",$condition["base_shipping_canceled"]);
        }
        if(isset($condition["base_shipping_tax_amount"]) && $condition["base_shipping_tax_amount"] != ""){
            $select->where("base_shipping_tax_amount = ?",$condition["base_shipping_tax_amount"]);
        }
        if(isset($condition["base_subtotal_canceled"]) && $condition["base_subtotal_canceled"] != ""){
            $select->where("base_subtotal_canceled = ?",$condition["base_subtotal_canceled"]);
        }
        if(isset($condition["base_tax_canceled"]) && $condition["base_tax_canceled"] != ""){
            $select->where("base_tax_canceled = ?",$condition["base_tax_canceled"]);
        }
        if(isset($condition["discount_canceled"]) && $condition["discount_canceled"] != ""){
            $select->where("discount_canceled = ?",$condition["discount_canceled"]);
        }
        if(isset($condition["shipping_canceled"]) && $condition["shipping_canceled"] != ""){
            $select->where("shipping_canceled = ?",$condition["shipping_canceled"]);
        }
        if(isset($condition["shipping_tax_amount"]) && $condition["shipping_tax_amount"] != ""){
            $select->where("shipping_tax_amount = ?",$condition["shipping_tax_amount"]);
        }
        if(isset($condition["subtotal_canceled"]) && $condition["subtotal_canceled"] != ""){
            $select->where("subtotal_canceled = ?",$condition["subtotal_canceled"]);
        }
        if(isset($condition["tax_canceled"]) && $condition["tax_canceled"] != ""){
            $select->where("tax_canceled = ?",$condition["tax_canceled"]);
        }
        if(isset($condition["paypal_ipn_customer_notified"]) && $condition["paypal_ipn_customer_notified"] != ""){
            $select->where("paypal_ipn_customer_notified = ?",$condition["paypal_ipn_customer_notified"]);
        }
        if(isset($condition["base_shipping_discount_amount"]) && $condition["base_shipping_discount_amount"] != ""){
            $select->where("base_shipping_discount_amount = ?",$condition["base_shipping_discount_amount"]);
        }
        if(isset($condition["base_subtotal_incl_tax"]) && $condition["base_subtotal_incl_tax"] != ""){
            $select->where("base_subtotal_incl_tax = ?",$condition["base_subtotal_incl_tax"]);
        }
        if(isset($condition["base_total_due"]) && $condition["base_total_due"] != ""){
            $select->where("base_total_due = ?",$condition["base_total_due"]);
        }
        if(isset($condition["shipping_discount_amount"]) && $condition["shipping_discount_amount"] != ""){
            $select->where("shipping_discount_amount = ?",$condition["shipping_discount_amount"]);
        }
        if(isset($condition["subtotal_incl_tax"]) && $condition["subtotal_incl_tax"] != ""){
            $select->where("subtotal_incl_tax = ?",$condition["subtotal_incl_tax"]);
        }
        if(isset($condition["total_due"]) && $condition["total_due"] != ""){
            $select->where("total_due = ?",$condition["total_due"]);
        }
        if(isset($condition["discount_description"]) && $condition["discount_description"] != ""){
            $select->where("discount_description = ?",$condition["discount_description"]);
        }
        if(isset($condition["total_item_count"]) && $condition["total_item_count"] != ""){
            $select->where("total_item_count = ?",$condition["total_item_count"]);
        }
        if(isset($condition["hidden_tax_amount"]) && $condition["hidden_tax_amount"] != ""){
            $select->where("hidden_tax_amount = ?",$condition["hidden_tax_amount"]);
        }
        if(isset($condition["base_hidden_tax_amount"]) && $condition["base_hidden_tax_amount"] != ""){
            $select->where("base_hidden_tax_amount = ?",$condition["base_hidden_tax_amount"]);
        }
        if(isset($condition["shipping_hidden_tax_amount"]) && $condition["shipping_hidden_tax_amount"] != ""){
            $select->where("shipping_hidden_tax_amount = ?",$condition["shipping_hidden_tax_amount"]);
        }
        if(isset($condition["base_shipping_hidden_tax_amount"]) && $condition["base_shipping_hidden_tax_amount"] != ""){
            $select->where("base_shipping_hidden_tax_amount = ?",$condition["base_shipping_hidden_tax_amount"]);
        }
        if(isset($condition["shipping_incl_tax"]) && $condition["shipping_incl_tax"] != ""){
            $select->where("shipping_incl_tax = ?",$condition["shipping_incl_tax"]);
        }
        if(isset($condition["base_shipping_incl_tax"]) && $condition["base_shipping_incl_tax"] != ""){
            $select->where("base_shipping_incl_tax = ?",$condition["base_shipping_incl_tax"]);
        }
        if(isset($condition["firstname"]) && $condition["firstname"] != ""){
            $select->where("firstname = ?",$condition["firstname"]);
        }
        if(isset($condition["lastname"]) && $condition["lastname"] != ""){
            $select->where("lastname = ?",$condition["lastname"]);
        }
        if(isset($condition["telephone"]) && $condition["telephone"] != ""){
            $select->where("telephone = ?",$condition["telephone"]);
        }
        if(isset($condition["postcode"]) && $condition["postcode"] != ""){
            $select->where("postcode = ?",$condition["postcode"]);
        }
        if(isset($condition["created"]) && $condition["created"] != ""){
            $select->where("created = ?",$condition["created"]);
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