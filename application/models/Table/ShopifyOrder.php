<?php
class Table_ShopifyOrder
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyOrder();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyOrder();
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
    public function update($row, $value, $field = "id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "id")
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
    public function getByField($value, $field = 'id', $colums = "*")
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
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["buyer_accepts_marketing"]) && $condition["buyer_accepts_marketing"] != ""){
            $select->where("buyer_accepts_marketing = ?",$condition["buyer_accepts_marketing"]);
        }
        if(isset($condition["cancel_reason"]) && $condition["cancel_reason"] != ""){
            $select->where("cancel_reason = ?",$condition["cancel_reason"]);
        }
        if(isset($condition["cancelled_at"]) && $condition["cancelled_at"] != ""){
            $select->where("cancelled_at = ?",$condition["cancelled_at"]);
        }
        if(isset($condition["cart_token"]) && $condition["cart_token"] != ""){
            $select->where("cart_token = ?",$condition["cart_token"]);
        }
        if(isset($condition["checkout_token"]) && $condition["checkout_token"] != ""){
            $select->where("checkout_token = ?",$condition["checkout_token"]);
        }
        if(isset($condition["closed_at"]) && $condition["closed_at"] != ""){
            $select->where("closed_at = ?",$condition["closed_at"]);
        }
        if(isset($condition["confirmed"]) && $condition["confirmed"] != ""){
            $select->where("confirmed = ?",$condition["confirmed"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["currency"]) && $condition["currency"] != ""){
            $select->where("currency = ?",$condition["currency"]);
        }
        if(isset($condition["email"]) && $condition["email"] != ""){
            $select->where("email = ?",$condition["email"]);
        }
        if(isset($condition["financial_status"]) && $condition["financial_status"] != ""){
            $select->where("financial_status = ?",$condition["financial_status"]);
        }
        if(isset($condition["fulfillment_status"]) && $condition["fulfillment_status"] != ""){
            $select->where("fulfillment_status = ?",$condition["fulfillment_status"]);
        }
        if(isset($condition["gateway"]) && $condition["gateway"] != ""){
            $select->where("gateway = ?",$condition["gateway"]);
        }
        if(isset($condition["landing_site"]) && $condition["landing_site"] != ""){
            $select->where("landing_site = ?",$condition["landing_site"]);
        }
        if(isset($condition["location_id"]) && $condition["location_id"] != ""){
            $select->where("location_id = ?",$condition["location_id"]);
        }
        if(isset($condition["name"]) && $condition["name"] != ""){
            $select->where("name = ?",$condition["name"]);
        }
        if(isset($condition["note"]) && $condition["note"] != ""){
            $select->where("note = ?",$condition["note"]);
        }
        if(isset($condition["number"]) && $condition["number"] != ""){
            $select->where("number = ?",$condition["number"]);
        }
        if(isset($condition["reference"]) && $condition["reference"] != ""){
            $select->where("reference = ?",$condition["reference"]);
        }
        if(isset($condition["referring_site"]) && $condition["referring_site"] != ""){
            $select->where("referring_site = ?",$condition["referring_site"]);
        }
        if(isset($condition["source"]) && $condition["source"] != ""){
            $select->where("source = ?",$condition["source"]);
        }
        if(isset($condition["source_identifier"]) && $condition["source_identifier"] != ""){
            $select->where("source_identifier = ?",$condition["source_identifier"]);
        }
        if(isset($condition["source_name"]) && $condition["source_name"] != ""){
            $select->where("source_name = ?",$condition["source_name"]);
        }
        if(isset($condition["source_url"]) && $condition["source_url"] != ""){
            $select->where("source_url = ?",$condition["source_url"]);
        }
        if(isset($condition["subtotal_price"]) && $condition["subtotal_price"] != ""){
            $select->where("subtotal_price = ?",$condition["subtotal_price"]);
        }
        if(isset($condition["taxes_included"]) && $condition["taxes_included"] != ""){
            $select->where("taxes_included = ?",$condition["taxes_included"]);
        }
        if(isset($condition["test"]) && $condition["test"] != ""){
            $select->where("test = ?",$condition["test"]);
        }
        if(isset($condition["token"]) && $condition["token"] != ""){
            $select->where("token = ?",$condition["token"]);
        }
        if(isset($condition["total_discounts"]) && $condition["total_discounts"] != ""){
            $select->where("total_discounts = ?",$condition["total_discounts"]);
        }
        if(isset($condition["total_line_items_price"]) && $condition["total_line_items_price"] != ""){
            $select->where("total_line_items_price = ?",$condition["total_line_items_price"]);
        }
        if(isset($condition["total_price"]) && $condition["total_price"] != ""){
            $select->where("total_price = ?",$condition["total_price"]);
        }
        if(isset($condition["total_price_usd"]) && $condition["total_price_usd"] != ""){
            $select->where("total_price_usd = ?",$condition["total_price_usd"]);
        }
        if(isset($condition["total_tax"]) && $condition["total_tax"] != ""){
            $select->where("total_tax = ?",$condition["total_tax"]);
        }
        if(isset($condition["total_weight"]) && $condition["total_weight"] != ""){
            $select->where("total_weight = ?",$condition["total_weight"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
        if(isset($condition["browser_ip"]) && $condition["browser_ip"] != ""){
            $select->where("browser_ip = ?",$condition["browser_ip"]);
        }
        if(isset($condition["landing_site_ref"]) && $condition["landing_site_ref"] != ""){
            $select->where("landing_site_ref = ?",$condition["landing_site_ref"]);
        }
        if(isset($condition["order_number"]) && $condition["order_number"] != ""){
            $select->where("order_number = ?",$condition["order_number"]);
        }
        if(isset($condition["discount_codes"]) && $condition["discount_codes"] != ""){
            $select->where("discount_codes = ?",$condition["discount_codes"]);
        }
        if(isset($condition["note_attributes"]) && $condition["note_attributes"] != ""){
            $select->where("note_attributes = ?",$condition["note_attributes"]);
        }
        if(isset($condition["processing_method"]) && $condition["processing_method"] != ""){
            $select->where("processing_method = ?",$condition["processing_method"]);
        }
        if(isset($condition["checkout_id"]) && $condition["checkout_id"] != ""){
            $select->where("checkout_id = ?",$condition["checkout_id"]);
        }
        if(isset($condition["tax_lines"]) && $condition["tax_lines"] != ""){
            $select->where("tax_lines = ?",$condition["tax_lines"]);
        }
        if(isset($condition["tags"]) && $condition["tags"] != ""){
            $select->where("tags = ?",$condition["tags"]);
        }
        if(isset($condition["load_risk"]) && $condition["load_risk"] != ""){
            $select->where("load_risk = ?",$condition["load_risk"]);
        }
        if(isset($condition["load_transaction"]) && $condition["load_transaction"] != ""){
            $select->where("load_transaction = ?",$condition["load_transaction"]);
        }
        if(isset($condition["is_load"]) && $condition["is_load"] != ""){
            $select->where("is_load = ?",$condition["is_load"]);
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