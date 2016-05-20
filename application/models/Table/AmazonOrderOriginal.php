<?php
class Table_AmazonOrderOriginal
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonOrderOriginal();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonOrderOriginal();
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
    public function update($row, $value, $field = "aoo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "aoo_id")
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
    public function getByField($value, $field = 'aoo_id', $colums = "*")
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
        
        if(isset($condition["amazon_order_id"]) && $condition["amazon_order_id"] != ""){
            $select->where("amazon_order_id = ?",$condition["amazon_order_id"]);
        }
        if(isset($condition["seller_order_id"]) && $condition["seller_order_id"] != ""){
            $select->where("seller_order_id = ?",$condition["seller_order_id"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?",$condition["order_status"]);
        }
        if(isset($condition["fulfillment_channel"]) && $condition["fulfillment_channel"] != ""){
            $select->where("fulfillment_channel = ?",$condition["fulfillment_channel"]);
        }
        if(isset($condition["sales_channel"]) && $condition["sales_channel"] != ""){
            $select->where("sales_channel = ?",$condition["sales_channel"]);
        }
        if(isset($condition["order_channel"]) && $condition["order_channel"] != ""){
            $select->where("order_channel = ?",$condition["order_channel"]);
        }
        if(isset($condition["ship_service_level"]) && $condition["ship_service_level"] != ""){
            $select->where("ship_service_level = ?",$condition["ship_service_level"]);
        }
        if(isset($condition["order_type"]) && $condition["order_type"] != ""){
            $select->where("order_type = ?",$condition["order_type"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["amount"]) && $condition["amount"] != ""){
            $select->where("amount = ?",$condition["amount"]);
        }
        if(isset($condition["payment_method"]) && $condition["payment_method"] != ""){
            $select->where("payment_method = ?",$condition["payment_method"]);
        }
        if(isset($condition["marketplace_id"]) && $condition["marketplace_id"] != ""){
            $select->where("marketplace_id = ?",$condition["marketplace_id"]);
        }
        if(isset($condition["buyer_email"]) && $condition["buyer_email"] != ""){
            $select->where("buyer_email = ?",$condition["buyer_email"]);
        }
        if(isset($condition["buyer_name"]) && $condition["buyer_name"] != ""){
            $select->where("buyer_name = ?",$condition["buyer_name"]);
        }
        if(isset($condition["shipment_service_level_category"]) && $condition["shipment_service_level_category"] != ""){
            $select->where("shipment_service_level_category = ?",$condition["shipment_service_level_category"]);
        }
        if(isset($condition["shipped_amazon_tfm"]) && $condition["shipped_amazon_tfm"] != ""){
            $select->where("shipped_amazon_tfm = ?",$condition["shipped_amazon_tfm"]);
        }
        if(isset($condition["tfm_shipment_status"]) && $condition["tfm_shipment_status"] != ""){
            $select->where("tfm_shipment_status = ?",$condition["tfm_shipment_status"]);
        }
        if(isset($condition["cba_displayable_shipping_label"]) && $condition["cba_displayable_shipping_label"] != ""){
            $select->where("cba_displayable_shipping_label = ?",$condition["cba_displayable_shipping_label"]);
        }
        if(isset($condition["number_items_shipped"]) && $condition["number_items_shipped"] != ""){
            $select->where("number_items_shipped = ?",$condition["number_items_shipped"]);
        }
        if(isset($condition["number_items_unshipped"]) && $condition["number_items_unshipped"] != ""){
            $select->where("number_items_unshipped = ?",$condition["number_items_unshipped"]);
        }
        if(isset($condition["shipping_address_name"]) && $condition["shipping_address_name"] != ""){
            $select->where("shipping_address_name = ?",$condition["shipping_address_name"]);
        }
        if(isset($condition["shipping_address_phone"]) && $condition["shipping_address_phone"] != ""){
            $select->where("shipping_address_phone = ?",$condition["shipping_address_phone"]);
        }
        if(isset($condition["shipping_address_country_code"]) && $condition["shipping_address_country_code"] != ""){
            $select->where("shipping_address_country_code = ?",$condition["shipping_address_country_code"]);
        }
        if(isset($condition["shipping_address_state"]) && $condition["shipping_address_state"] != ""){
            $select->where("shipping_address_state = ?",$condition["shipping_address_state"]);
        }
        if(isset($condition["shipping_address_district"]) && $condition["shipping_address_district"] != ""){
            $select->where("shipping_address_district = ?",$condition["shipping_address_district"]);
        }
        if(isset($condition["shipping_address_county"]) && $condition["shipping_address_county"] != ""){
            $select->where("shipping_address_county = ?",$condition["shipping_address_county"]);
        }
        if(isset($condition["shipping_address_city"]) && $condition["shipping_address_city"] != ""){
            $select->where("shipping_address_city = ?",$condition["shipping_address_city"]);
        }
        if(isset($condition["shipping_address_postal_code"]) && $condition["shipping_address_postal_code"] != ""){
            $select->where("shipping_address_postal_code = ?",$condition["shipping_address_postal_code"]);
        }
        if(isset($condition["shipping_address_address1"]) && $condition["shipping_address_address1"] != ""){
            $select->where("shipping_address_address1 = ?",$condition["shipping_address_address1"]);
        }
        if(isset($condition["shipping_address_address2"]) && $condition["shipping_address_address2"] != ""){
            $select->where("shipping_address_address2 = ?",$condition["shipping_address_address2"]);
        }
        if(isset($condition["shipping_address_address3"]) && $condition["shipping_address_address3"] != ""){
            $select->where("shipping_address_address3 = ?",$condition["shipping_address_address3"]);
        }
        if(isset($condition["request_id"]) && $condition["request_id"] != ""){
            $select->where("request_id = ?",$condition["request_id"]);
        }
        if(isset($condition["is_loaded"]) && $condition["is_loaded"] != ""){
            $select->where("is_loaded = ?",$condition["is_loaded"]);
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