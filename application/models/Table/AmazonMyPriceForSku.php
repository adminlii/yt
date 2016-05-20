<?php
class Table_AmazonMyPriceForSku
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonMyPriceForSku();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonMyPriceForSku();
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
        
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["seller_sku"]) && $condition["seller_sku"] != ""){
            $select->where("seller_sku = ?",$condition["seller_sku"]);
        }
        if(isset($condition["offer_seller_sku"]) && $condition["offer_seller_sku"] != ""){
            $select->where("offer_seller_sku = ?",$condition["offer_seller_sku"]);
        }        
        
        if(isset($condition["market_place_id"]) && $condition["market_place_id"] != ""){
            $select->where("market_place_id = ?",$condition["market_place_id"]);
        }
        if(isset($condition["asin"]) && $condition["asin"] != ""){
            $select->where("asin = ?",$condition["asin"]);
        }
        if(isset($condition["seller_id"]) && $condition["seller_id"] != ""){
            $select->where("seller_id = ?",$condition["seller_id"]);
        }
        if(isset($condition["landed_price_currency"]) && $condition["landed_price_currency"] != ""){
            $select->where("landed_price_currency = ?",$condition["landed_price_currency"]);
        }
        if(isset($condition["landed_price_amount"]) && $condition["landed_price_amount"] != ""){
            $select->where("landed_price_amount = ?",$condition["landed_price_amount"]);
        }
        if(isset($condition["listing_price_currency"]) && $condition["listing_price_currency"] != ""){
            $select->where("listing_price_currency = ?",$condition["listing_price_currency"]);
        }
        if(isset($condition["listing_price_amount"]) && $condition["listing_price_amount"] != ""){
            $select->where("listing_price_amount = ?",$condition["listing_price_amount"]);
        }
        if(isset($condition["shippint_currency"]) && $condition["shippint_currency"] != ""){
            $select->where("shippint_currency = ?",$condition["shippint_currency"]);
        }
        if(isset($condition["shippint_amount"]) && $condition["shippint_amount"] != ""){
            $select->where("shippint_amount = ?",$condition["shippint_amount"]);
        }
        if(isset($condition["regular_price_currency"]) && $condition["regular_price_currency"] != ""){
            $select->where("regular_price_currency = ?",$condition["regular_price_currency"]);
        }
        if(isset($condition["regular_price_amount"]) && $condition["regular_price_amount"] != ""){
            $select->where("regular_price_amount = ?",$condition["regular_price_amount"]);
        }
        if(isset($condition["fulfillment_channel"]) && $condition["fulfillment_channel"] != ""){
            $select->where("fulfillment_channel = ?",$condition["fulfillment_channel"]);
        }
        if(isset($condition["item_condition"]) && $condition["item_condition"] != ""){
            $select->where("item_condition = ?",$condition["item_condition"]);
        }
        if(isset($condition["item_sub_condition"]) && $condition["item_sub_condition"] != ""){
            $select->where("item_sub_condition = ?",$condition["item_sub_condition"]);
        }
        if(isset($condition["create_time"]) && $condition["create_time"] != ""){
            $select->where("create_time = ?",$condition["create_time"]);
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