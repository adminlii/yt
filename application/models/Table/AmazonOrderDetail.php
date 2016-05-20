<?php
class Table_AmazonOrderDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonOrderDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonOrderDetail();
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
    public function update($row, $value, $field = "aod_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "aod_id")
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
    public function getByField($value, $field = 'aod_id', $colums = "*")
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
        
        if(isset($condition["aoo_id"]) && $condition["aoo_id"] != ""){
            $select->where("aoo_id = ?",$condition["aoo_id"]);
        }
        if(isset($condition["amazon_order_id"]) && $condition["amazon_order_id"] != ""){
            $select->where("amazon_order_id = ?",$condition["amazon_order_id"]);
        }
        if(isset($condition["asin"]) && $condition["asin"] != ""){
            $select->where("asin = ?",$condition["asin"]);
        }
        if(isset($condition["seller_sku"]) && $condition["seller_sku"] != ""){
            $select->where("seller_sku = ?",$condition["seller_sku"]);
        }
        if(isset($condition["order_item_id"]) && $condition["order_item_id"] != ""){
            $select->where("order_item_id = ?",$condition["order_item_id"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["quantity_ordered"]) && $condition["quantity_ordered"] != ""){
            $select->where("quantity_ordered = ?",$condition["quantity_ordered"]);
        }
        if(isset($condition["quantity_shipped"]) && $condition["quantity_shipped"] != ""){
            $select->where("quantity_shipped = ?",$condition["quantity_shipped"]);
        }
        if(isset($condition["gift_message_text"]) && $condition["gift_message_text"] != ""){
            $select->where("gift_message_text = ?",$condition["gift_message_text"]);
        }
        if(isset($condition["gift_wrap_level"]) && $condition["gift_wrap_level"] != ""){
            $select->where("gift_wrap_level = ?",$condition["gift_wrap_level"]);
        }
        if(isset($condition["item_price_currency_code"]) && $condition["item_price_currency_code"] != ""){
            $select->where("item_price_currency_code = ?",$condition["item_price_currency_code"]);
        }
        if(isset($condition["item_price_amount"]) && $condition["item_price_amount"] != ""){
            $select->where("item_price_amount = ?",$condition["item_price_amount"]);
        }
        if(isset($condition["shipping_price_currency_code"]) && $condition["shipping_price_currency_code"] != ""){
            $select->where("shipping_price_currency_code = ?",$condition["shipping_price_currency_code"]);
        }
        if(isset($condition["shipping_price_amount"]) && $condition["shipping_price_amount"] != ""){
            $select->where("shipping_price_amount = ?",$condition["shipping_price_amount"]);
        }
        if(isset($condition["gift_wrap_price_currency_code"]) && $condition["gift_wrap_price_currency_code"] != ""){
            $select->where("gift_wrap_price_currency_code = ?",$condition["gift_wrap_price_currency_code"]);
        }
        if(isset($condition["gift_wrap_price_amount"]) && $condition["gift_wrap_price_amount"] != ""){
            $select->where("gift_wrap_price_amount = ?",$condition["gift_wrap_price_amount"]);
        }
        if(isset($condition["item_tax_currency_code"]) && $condition["item_tax_currency_code"] != ""){
            $select->where("item_tax_currency_code = ?",$condition["item_tax_currency_code"]);
        }
        if(isset($condition["item_tax_amount"]) && $condition["item_tax_amount"] != ""){
            $select->where("item_tax_amount = ?",$condition["item_tax_amount"]);
        }
        if(isset($condition["shipping_tax_currency_code"]) && $condition["shipping_tax_currency_code"] != ""){
            $select->where("shipping_tax_currency_code = ?",$condition["shipping_tax_currency_code"]);
        }
        if(isset($condition["shipping_tax_amount"]) && $condition["shipping_tax_amount"] != ""){
            $select->where("shipping_tax_amount = ?",$condition["shipping_tax_amount"]);
        }
        if(isset($condition["gift_wrap_tax_currency_code"]) && $condition["gift_wrap_tax_currency_code"] != ""){
            $select->where("gift_wrap_tax_currency_code = ?",$condition["gift_wrap_tax_currency_code"]);
        }
        if(isset($condition["gift_wrap_tax_amount"]) && $condition["gift_wrap_tax_amount"] != ""){
            $select->where("gift_wrap_tax_amount = ?",$condition["gift_wrap_tax_amount"]);
        }
        if(isset($condition["shipping_discount_currency_code"]) && $condition["shipping_discount_currency_code"] != ""){
            $select->where("shipping_discount_currency_code = ?",$condition["shipping_discount_currency_code"]);
        }
        if(isset($condition["shipping_discount_amount"]) && $condition["shipping_discount_amount"] != ""){
            $select->where("shipping_discount_amount = ?",$condition["shipping_discount_amount"]);
        }
        if(isset($condition["promotion_discount_currency_code"]) && $condition["promotion_discount_currency_code"] != ""){
            $select->where("promotion_discount_currency_code = ?",$condition["promotion_discount_currency_code"]);
        }
        if(isset($condition["promotion_discount_amount"]) && $condition["promotion_discount_amount"] != ""){
            $select->where("promotion_discount_amount = ?",$condition["promotion_discount_amount"]);
        }
        if(isset($condition["cod_fee_currency_code"]) && $condition["cod_fee_currency_code"] != ""){
            $select->where("cod_fee_currency_code = ?",$condition["cod_fee_currency_code"]);
        }
        if(isset($condition["cod_fee_amount"]) && $condition["cod_fee_amount"] != ""){
            $select->where("cod_fee_amount = ?",$condition["cod_fee_amount"]);
        }
        if(isset($condition["cod_fee_discount_currency_code"]) && $condition["cod_fee_discount_currency_code"] != ""){
            $select->where("cod_fee_discount_currency_code = ?",$condition["cod_fee_discount_currency_code"]);
        }
        if(isset($condition["cod_fee_discount_amount"]) && $condition["cod_fee_discount_amount"] != ""){
            $select->where("cod_fee_discount_amount = ?",$condition["cod_fee_discount_amount"]);
        }
        if(isset($condition["invoice_requirement"]) && $condition["invoice_requirement"] != ""){
            $select->where("invoice_requirement = ?",$condition["invoice_requirement"]);
        }
        if(isset($condition["invoice_buyer_selected_category"]) && $condition["invoice_buyer_selected_category"] != ""){
            $select->where("invoice_buyer_selected_category = ?",$condition["invoice_buyer_selected_category"]);
        }
        if(isset($condition["invoice_title"]) && $condition["invoice_title"] != ""){
            $select->where("invoice_title = ?",$condition["invoice_title"]);
        }
        if(isset($condition["invoice_information"]) && $condition["invoice_information"] != ""){
            $select->where("invoice_information = ?",$condition["invoice_information"]);
        }
        if(isset($condition["condition_id"]) && $condition["condition_id"] != ""){
            $select->where("condition_id = ?",$condition["condition_id"]);
        }
        if(isset($condition["condition_subtype_id"]) && $condition["condition_subtype_id"] != ""){
            $select->where("condition_subtype_id = ?",$condition["condition_subtype_id"]);
        }
        if(isset($condition["condition_note"]) && $condition["condition_note"] != ""){
            $select->where("condition_note = ?",$condition["condition_note"]);
        }
        if(isset($condition["request_id"]) && $condition["request_id"] != ""){
            $select->where("request_id = ?",$condition["request_id"]);
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