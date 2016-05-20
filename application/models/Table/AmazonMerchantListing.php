<?php
class Table_AmazonMerchantListing
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonMerchantListing();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonMerchantListing();
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
        if(isset($condition["user_account_arr"]) && is_array($condition["user_account_arr"]) && ! empty($condition["user_account_arr"])){
            $select->where("user_account in (?)", $condition["user_account_arr"]);
        }
        if(isset($condition["listing_id"]) && $condition["listing_id"] != ""){
            $select->where("listing_id = ?",$condition["listing_id"]);
        }
        if(isset($condition["listing_id_arr"]) && is_array($condition["listing_id_arr"]) && ! empty($condition["listing_id_arr"])){
            $select->where("listing_id in (?)", $condition["listing_id_arr"]);
        }
        
        if(isset($condition["seller_sku"]) && $condition["seller_sku"] != ""){
            $select->where("seller_sku = ?",$condition["seller_sku"]);
        }

        if(isset($condition["seller_sku_like"]) && $condition["seller_sku_like"] != ""){
            $select->where("seller_sku like ?","%{$condition["seller_sku_like"]}%");
        }

        if(isset($condition["seller_sku_arr"]) && is_array($condition["seller_sku_arr"]) && ! empty($condition["seller_sku_arr"])){
            $select->where("seller_sku in (?)", $condition["seller_sku_arr"]);
        }
        
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_id_arr"]) && is_array($condition["product_id_arr"]) && ! empty($condition["product_id_arr"])){
            $select->where("product_id in (?)", $condition["product_id_arr"]);
        }
        
        if(isset($condition["item_name"]) && $condition["item_name"] != ""){
            $select->where("item_name = ?",$condition["item_name"]);
        }
        if(isset($condition["item_description"]) && $condition["item_description"] != ""){
            $select->where("item_description = ?",$condition["item_description"]);
        }
        if(isset($condition["price"]) && $condition["price"] != ""){
            $select->where("price = ?",$condition["price"]);
        }
        if(isset($condition["quantity"]) && $condition["quantity"] != ""){
            $select->where("quantity = ?",$condition["quantity"]);
        }
        if(isset($condition["sell_qty_from"]) && $condition["sell_qty_from"] != ""){
            $select->where("quantity >= ?",$condition["sell_qty_from"]);
        }

        if(isset($condition["sell_qty_to"]) && $condition["sell_qty_to"] != ""){
            $select->where("quantity <= ?",$condition["sell_qty_to"]);
        }
        
        
        if(isset($condition["open_date"]) && $condition["open_date"] != ""){
            $select->where("open_date = ?",$condition["open_date"]);
        }
        if(isset($condition["image_url"]) && $condition["image_url"] != ""){
            $select->where("image_url = ?",$condition["image_url"]);
        }
        if(isset($condition["item_is_marketplace"]) && $condition["item_is_marketplace"] != ""){
            $select->where("item_is_marketplace = ?",$condition["item_is_marketplace"]);
        }
        if(isset($condition["product_id_type"]) && $condition["product_id_type"] != ""){
            $select->where("product_id_type = ?",$condition["product_id_type"]);
        }
        if(isset($condition["zshop_shipping_fee"]) && $condition["zshop_shipping_fee"] != ""){
            $select->where("zshop_shipping_fee = ?",$condition["zshop_shipping_fee"]);
        }
        if(isset($condition["item_note"]) && $condition["item_note"] != ""){
            $select->where("item_note = ?",$condition["item_note"]);
        }
        if(isset($condition["item_condition"]) && $condition["item_condition"] != ""){
            $select->where("item_condition = ?",$condition["item_condition"]);
        }
        if(isset($condition["zshop_category1"]) && $condition["zshop_category1"] != ""){
            $select->where("zshop_category1 = ?",$condition["zshop_category1"]);
        }
        if(isset($condition["zshop_browse_path"]) && $condition["zshop_browse_path"] != ""){
            $select->where("zshop_browse_path = ?",$condition["zshop_browse_path"]);
        }
        if(isset($condition["zshop_storefront_feature"]) && $condition["zshop_storefront_feature"] != ""){
            $select->where("zshop_storefront_feature = ?",$condition["zshop_storefront_feature"]);
        }
        if(isset($condition["asin1"]) && $condition["asin1"] != ""){
            $select->where("asin1 = ?",$condition["asin1"]);
        }
        if(isset($condition["asin2"]) && $condition["asin2"] != ""){
            $select->where("asin2 = ?",$condition["asin2"]);
        }
        if(isset($condition["asin3"]) && $condition["asin3"] != ""){
            $select->where("asin3 = ?",$condition["asin3"]);
        }
        if(isset($condition["asin1_arr"]) && is_array($condition["asin1_arr"]) && ! empty($condition["asin1_arr"])){
            $select->where("asin1 in (?)", $condition["asin1_arr"]);
        }
        
        if(isset($condition["asin2_arr"]) && is_array($condition["asin2_arr"]) && ! empty($condition["asin2_arr"])){
            $select->where("asin2 in (?)", $condition["asin2_arr"]);
        }
        
        if(isset($condition["asin3_arr"]) && is_array($condition["asin3_arr"]) && ! empty($condition["asin3_arr"])){
            $select->where("asin3 in (?)", $condition["asin3_arr"]);
        }
        
        if(isset($condition["will_ship_internationally"]) && $condition["will_ship_internationally"] != ""){
            $select->where("will_ship_internationally = ?",$condition["will_ship_internationally"]);
        }
        if(isset($condition["expedited_shipping"]) && $condition["expedited_shipping"] != ""){
            $select->where("expedited_shipping = ?",$condition["expedited_shipping"]);
        }
        if(isset($condition["zshop_boldface"]) && $condition["zshop_boldface"] != ""){
            $select->where("zshop_boldface = ?",$condition["zshop_boldface"]);
        }
        if(isset($condition["bid_for_featured_placement"]) && $condition["bid_for_featured_placement"] != ""){
            $select->where("bid_for_featured_placement = ?",$condition["bid_for_featured_placement"]);
        }
        if(isset($condition["add_delete"]) && $condition["add_delete"] != ""){
            $select->where("add_delete = ?",$condition["add_delete"]);
        }
        if(isset($condition["pending_quantity"]) && $condition["pending_quantity"] != ""){
            $select->where("pending_quantity = ?",$condition["pending_quantity"]);
        }
        if(isset($condition["fulfillment_channel"]) && $condition["fulfillment_channel"] != ""){
            $select->where("fulfillment_channel = ?",$condition["fulfillment_channel"]);
        }
        if(isset($condition["add_time"]) && $condition["add_time"] != ""){
            $select->where("add_time = ?",$condition["add_time"]);
        }
        if(isset($condition["update_time"]) && $condition["update_time"] != ""){
            $select->where("update_time = ?",$condition["update_time"]);
        }
        if(isset($condition["item_status"]) && $condition["item_status"] != ""){
            $select->where("item_status = ?",$condition["item_status"]);
        }
        if(isset($condition["fulfillment_type"]) && $condition["fulfillment_type"] != ""){
            $select->where("fulfillment_type = ?",$condition["fulfillment_type"]);
        }
        
//         echo $select;exit;
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