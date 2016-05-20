<?php
class Table_EbayOrder
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayOrder();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayOrder();
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
    public function update($row, $value, $field = "eo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "eo_id")
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
    public function getByField($value, $field = 'eo_id', $colums = "*")
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
        
        if(isset($condition["order_sn"]) && $condition["order_sn"] != ""){
            $select->where("order_sn = ?",$condition["order_sn"]);
        }
        if(isset($condition["subtotal"]) && $condition["subtotal"] != ""){
            $select->where("subtotal = ?",$condition["subtotal"]);
        }
        if(isset($condition["subtotal_currency"]) && $condition["subtotal_currency"] != ""){
            $select->where("subtotal_currency = ?",$condition["subtotal_currency"]);
        }
        if(isset($condition["total"]) && $condition["total"] != ""){
            $select->where("total = ?",$condition["total"]);
        }
        if(isset($condition["total_currency"]) && $condition["total_currency"] != ""){
            $select->where("total_currency = ?",$condition["total_currency"]);
        }
        if(isset($condition["adjustment_amount"]) && $condition["adjustment_amount"] != ""){
            $select->where("adjustment_amount = ?",$condition["adjustment_amount"]);
        }
        if(isset($condition["adjustment_amount_currency"]) && $condition["adjustment_amount_currency"] != ""){
            $select->where("adjustment_amount_currency = ?",$condition["adjustment_amount_currency"]);
        }
        if(isset($condition["amoun_paid"]) && $condition["amoun_paid"] != ""){
            $select->where("amoun_paid = ?",$condition["amoun_paid"]);
        }
        if(isset($condition["amoun_paid_currency"]) && $condition["amoun_paid_currency"] != ""){
            $select->where("amoun_paid_currency = ?",$condition["amoun_paid_currency"]);
        }
        if(isset($condition["amount_saved"]) && $condition["amount_saved"] != ""){
            $select->where("amount_saved = ?",$condition["amount_saved"]);
        }
        if(isset($condition["amount_saved_currency"]) && $condition["amount_saved_currency"] != ""){
            $select->where("amount_saved_currency = ?",$condition["amount_saved_currency"]);
        }
        if(isset($condition["buyer_checkout_message"]) && $condition["buyer_checkout_message"] != ""){
            $select->where("buyer_checkout_message = ?",$condition["buyer_checkout_message"]);
        }
        if(isset($condition["buyer_user_id"]) && $condition["buyer_user_id"] != ""){
            $select->where("buyer_user_id = ?",$condition["buyer_user_id"]);
        }
        if(isset($condition["cancel_reason"]) && $condition["cancel_reason"] != ""){
            $select->where("cancel_reason = ?",$condition["cancel_reason"]);
        }
        if(isset($condition["created_time"]) && $condition["created_time"] != ""){
            $select->where("created_time = ?",$condition["created_time"]);
        }
        if(isset($condition["paid_time"]) && $condition["paid_time"] != ""){
            $select->where("paid_time = ?",$condition["paid_time"]);
        }
        if(isset($condition["shipped_time"]) && $condition["shipped_time"] != ""){
            $select->where("shipped_time = ?",$condition["shipped_time"]);
        }
        if(isset($condition["creating_user_role"]) && $condition["creating_user_role"] != ""){
            $select->where("creating_user_role = ?",$condition["creating_user_role"]);
        }
        if(isset($condition["eias_token"]) && $condition["eias_token"] != ""){
            $select->where("eias_token = ?",$condition["eias_token"]);
        }
        if(isset($condition["integrated_merchant_credit_card_enabled"]) && $condition["integrated_merchant_credit_card_enabled"] != ""){
            $select->where("integrated_merchant_credit_card_enabled = ?",$condition["integrated_merchant_credit_card_enabled"]);
        }
        if(isset($condition["is_multi_leg_shipping"]) && $condition["is_multi_leg_shipping"] != ""){
            $select->where("is_multi_leg_shipping = ?",$condition["is_multi_leg_shipping"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?",$condition["order_status"]);
        }
        if(isset($condition["payment_hold_status"]) && $condition["payment_hold_status"] != ""){
            $select->where("payment_hold_status = ?",$condition["payment_hold_status"]);
        }
        if(isset($condition["seller_eias_token"]) && $condition["seller_eias_token"] != ""){
            $select->where("seller_eias_token = ?",$condition["seller_eias_token"]);
        }
        if(isset($condition["seller_email"]) && $condition["seller_email"] != ""){
            $select->where("seller_email = ?",$condition["seller_email"]);
        }
        if(isset($condition["seller_user_id"]) && $condition["seller_user_id"] != ""){
            $select->where("seller_user_id = ?",$condition["seller_user_id"]);
        }
        if(isset($condition["ebay_payment_status"]) && $condition["ebay_payment_status"] != ""){
            $select->where("ebay_payment_status = ?",$condition["ebay_payment_status"]);
        }
        if(isset($condition["last_modified_time"]) && $condition["last_modified_time"] != ""){
            $select->where("last_modified_time = ?",$condition["last_modified_time"]);
        }
        if(isset($condition["payment_method"]) && $condition["payment_method"] != ""){
            $select->where("payment_method = ?",$condition["payment_method"]);
        }
        if(isset($condition["checkout_status"]) && $condition["checkout_status"] != ""){
            $select->where("checkout_status = ?",$condition["checkout_status"]);
        }
        if(isset($condition["address_id"]) && $condition["address_id"] != ""){
            $select->where("address_id = ?",$condition["address_id"]);
        }
        if(isset($condition["address_owner"]) && $condition["address_owner"] != ""){
            $select->where("address_owner = ?",$condition["address_owner"]);
        }
        if(isset($condition["city_name"]) && $condition["city_name"] != ""){
            $select->where("city_name = ?",$condition["city_name"]);
        }
        if(isset($condition["country"]) && $condition["country"] != ""){
            $select->where("country = ?",$condition["country"]);
        }
        if(isset($condition["country_name"]) && $condition["country_name"] != ""){
            $select->where("country_name = ?",$condition["country_name"]);
        }
        if(isset($condition["external_address_id"]) && $condition["external_address_id"] != ""){
            $select->where("external_address_id = ?",$condition["external_address_id"]);
        }
        if(isset($condition["consignee_name"]) && $condition["consignee_name"] != ""){
            $select->where("consignee_name = ?",$condition["consignee_name"]);
        }
        if(isset($condition["consignee_phone"]) && $condition["consignee_phone"] != ""){
            $select->where("consignee_phone = ?",$condition["consignee_phone"]);
        }
        if(isset($condition["consignee_zip"]) && $condition["consignee_zip"] != ""){
            $select->where("consignee_zip = ?",$condition["consignee_zip"]);
        }
        if(isset($condition["consignee_state"]) && $condition["consignee_state"] != ""){
            $select->where("consignee_state = ?",$condition["consignee_state"]);
        }
        if(isset($condition["consignee_street1"]) && $condition["consignee_street1"] != ""){
            $select->where("consignee_street1 = ?",$condition["consignee_street1"]);
        }
        if(isset($condition["consignee_street2"]) && $condition["consignee_street2"] != ""){
            $select->where("consignee_street2 = ?",$condition["consignee_street2"]);
        }
        if(isset($condition["shipping_service"]) && $condition["shipping_service"] != ""){
            $select->where("shipping_service = ?",$condition["shipping_service"]);
        }
        if(isset($condition["shipping_service_cost"]) && $condition["shipping_service_cost"] != ""){
            $select->where("shipping_service_cost = ?",$condition["shipping_service_cost"]);
        }
        if(isset($condition["shipping_service_cost_currency"]) && $condition["shipping_service_cost_currency"] != ""){
            $select->where("shipping_service_cost_currency = ?",$condition["shipping_service_cost_currency"]);
        }
        if(isset($condition["shipping_service_priority"]) && $condition["shipping_service_priority"] != ""){
            $select->where("shipping_service_priority = ?",$condition["shipping_service_priority"]);
        }
        if(isset($condition["shipping_service_additional_cost"]) && $condition["shipping_service_additional_cost"] != ""){
            $select->where("shipping_service_additional_cost = ?",$condition["shipping_service_additional_cost"]);
        }
        if(isset($condition["shipping_service_additional_cost_currency"]) && $condition["shipping_service_additional_cost_currency"] != ""){
            $select->where("shipping_service_additional_cost_currency = ?",$condition["shipping_service_additional_cost_currency"]);
        }
        if(isset($condition["shipping_insurance_cost"]) && $condition["shipping_insurance_cost"] != ""){
            $select->where("shipping_insurance_cost = ?",$condition["shipping_insurance_cost"]);
        }
        if(isset($condition["shipping_insurance_cost_currency"]) && $condition["shipping_insurance_cost_currency"] != ""){
            $select->where("shipping_insurance_cost_currency = ?",$condition["shipping_insurance_cost_currency"]);
        }
        if(isset($condition["import_charge"]) && $condition["import_charge"] != ""){
            $select->where("import_charge = ?",$condition["import_charge"]);
        }
        if(isset($condition["importCharge_currency"]) && $condition["importCharge_currency"] != ""){
            $select->where("importCharge_currency = ?",$condition["importCharge_currency"]);
        }
        if(isset($condition["expedited_service"]) && $condition["expedited_service"] != ""){
            $select->where("expedited_service = ?",$condition["expedited_service"]);
        }
        if(isset($condition["payment_methods"]) && $condition["payment_methods"] != ""){
            $select->where("payment_methods = ?",$condition["payment_methods"]);
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