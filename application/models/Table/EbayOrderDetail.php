<?php
class Table_EbayOrderDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayOrderDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayOrderDetail();
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
    public function update($row, $value, $field = "eod_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "eod_id")
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
    public function getByField($value, $field = 'eod_id', $colums = "*")
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
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["actual_handling_cost"]) && $condition["actual_handling_cost"] != ""){
            $select->where("actual_handling_cost = ?",$condition["actual_handling_cost"]);
        }
        if(isset($condition["actual_handling_cost_currency"]) && $condition["actual_handling_cost_currency"] != ""){
            $select->where("actual_handling_cost_currency = ?",$condition["actual_handling_cost_currency"]);
        }
        if(isset($condition["actual_shipping_cost"]) && $condition["actual_shipping_cost"] != ""){
            $select->where("actual_shipping_cost = ?",$condition["actual_shipping_cost"]);
        }
        if(isset($condition["actual_shipping_cost_currency"]) && $condition["actual_shipping_cost_currency"] != ""){
            $select->where("actual_shipping_cost_currency = ?",$condition["actual_shipping_cost_currency"]);
        }
        if(isset($condition["buyer_email"]) && $condition["buyer_email"] != ""){
            $select->where("buyer_email = ?",$condition["buyer_email"]);
        }
        if(isset($condition["created_date"]) && $condition["created_date"] != ""){
            $select->where("created_date = ?",$condition["created_date"]);
        }
        if(isset($condition["final_value_fee"]) && $condition["final_value_fee"] != ""){
            $select->where("final_value_fee = ?",$condition["final_value_fee"]);
        }
        if(isset($condition["final_value_fee_currency"]) && $condition["final_value_fee_currency"] != ""){
            $select->where("final_value_fee_currency = ?",$condition["final_value_fee_currency"]);
        }
        if(isset($condition["transaction_site_id"]) && $condition["transaction_site_id"] != ""){
            $select->where("transaction_site_id = ?",$condition["transaction_site_id"]);
        }
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["invoice_sent_time"]) && $condition["invoice_sent_time"] != ""){
            $select->where("invoice_sent_time = ?",$condition["invoice_sent_time"]);
        }
        if(isset($condition["order_line_item_id"]) && $condition["order_line_item_id"] != ""){
            $select->where("order_line_item_id = ?",$condition["order_line_item_id"]);
        }
        if(isset($condition["quantity_purchased"]) && $condition["quantity_purchased"] != ""){
            $select->where("quantity_purchased = ?",$condition["quantity_purchased"]);
        }
        if(isset($condition["shipped_time"]) && $condition["shipped_time"] != ""){
            $select->where("shipped_time = ?",$condition["shipped_time"]);
        }
        if(isset($condition["transaction_id"]) && $condition["transaction_id"] != ""){
            $select->where("transaction_id = ?",$condition["transaction_id"]);
        }
        if(isset($condition["transaction_price"]) && $condition["transaction_price"] != ""){
            $select->where("transaction_price = ?",$condition["transaction_price"]);
        }
        if(isset($condition["transaction_price_currency"]) && $condition["transaction_price_currency"] != ""){
            $select->where("transaction_price_currency = ?",$condition["transaction_price_currency"]);
        }
        if(isset($condition["integrated_merchant_credit_card_enabled"]) && $condition["integrated_merchant_credit_card_enabled"] != ""){
            $select->where("integrated_merchant_credit_card_enabled = ?",$condition["integrated_merchant_credit_card_enabled"]);
        }
        if(isset($condition["application_data"]) && $condition["application_data"] != ""){
            $select->where("application_data = ?",$condition["application_data"]);
        }
        if(isset($condition["seller_inventory_id"]) && $condition["seller_inventory_id"] != ""){
            $select->where("seller_inventory_id = ?",$condition["seller_inventory_id"]);
        }
        if(isset($condition["site"]) && $condition["site"] != ""){
            $select->where("site = ?",$condition["site"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["url"]) && $condition["url"] != ""){
            $select->where("url = ?",$condition["url"]);
        }
        if(isset($condition["condition_id"]) && $condition["condition_id"] != ""){
            $select->where("condition_id = ?",$condition["condition_id"]);
        }
        if(isset($condition["condition_display_name"]) && $condition["condition_display_name"] != ""){
            $select->where("condition_display_name = ?",$condition["condition_display_name"]);
        }
        if(isset($condition["selling_manager_sales_record_number"]) && $condition["selling_manager_sales_record_number"] != ""){
            $select->where("selling_manager_sales_record_number = ?",$condition["selling_manager_sales_record_number"]);
        }
        if(isset($condition["payment_hold_status"]) && $condition["payment_hold_status"] != ""){
            $select->where("payment_hold_status = ?",$condition["payment_hold_status"]);
        }
        if(isset($condition["payment_method_used"]) && $condition["payment_method_used"] != ""){
            $select->where("payment_method_used = ?",$condition["payment_method_used"]);
        }
        if(isset($condition["create_time_sys"]) && $condition["create_time_sys"] != ""){
            $select->where("create_time_sys = ?",$condition["create_time_sys"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
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