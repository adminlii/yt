<?php
class Table_EbayItem
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayItem();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayItem();
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
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["item_type"]) && $condition["item_type"] != ""){
            $select->where("item_type = ?",$condition["item_type"]);
        }
        
        if(isset($condition["listing_type"]) && $condition["listing_type"] != ""){
            $select->where("listing_type = ?",$condition["listing_type"]);
        }
        if(isset($condition["listing_duration"]) && $condition["listing_duration"] != ""){
            $select->where("listing_duration = ?",$condition["listing_duration"]);
        }
        if(isset($condition["auto_pay"]) && $condition["auto_pay"] != ""){
            $select->where("auto_pay = ?",$condition["auto_pay"]);
        }
        if(isset($condition["buyer_protection"]) && $condition["buyer_protection"] != ""){
            $select->where("buyer_protection = ?",$condition["buyer_protection"]);
        }
        if(isset($condition["buy_it_now_price"]) && $condition["buy_it_now_price"] != ""){
            $select->where("buy_it_now_price = ?",$condition["buy_it_now_price"]);
        }
        if(isset($condition["buy_it_now_price_currency"]) && $condition["buy_it_now_price_currency"] != ""){
            $select->where("buy_it_now_price_currency = ?",$condition["buy_it_now_price_currency"]);
        }
        if(isset($condition["country"]) && $condition["country"] != ""){
            $select->where("country = ?",$condition["country"]);
        }
        if(isset($condition["currency"]) && $condition["currency"] != ""){
            $select->where("currency = ?",$condition["currency"]);
        }
        if(isset($condition["gift_icon"]) && $condition["gift_icon"] != ""){
            $select->where("gift_icon = ?",$condition["gift_icon"]);
        }
        if(isset($condition["hit_counter"]) && $condition["hit_counter"] != ""){
            $select->where("hit_counter = ?",$condition["hit_counter"]);
        }
        if(isset($condition["location"]) && $condition["location"] != ""){
            $select->where("location = ?",$condition["location"]);
        }
        if(isset($condition["payment_methods"]) && $condition["payment_methods"] != ""){
            $select->where("payment_methods = ?",$condition["payment_methods"]);
        }
        if(isset($condition["paypal_email"]) && $condition["paypal_email"] != ""){
            $select->where("paypal_email = ?",$condition["paypal_email"]);
        }
        if(isset($condition["private_listing"]) && $condition["private_listing"] != ""){
            $select->where("private_listing = ?",$condition["private_listing"]);
        }
        if(isset($condition["quantity"]) && $condition["quantity"] != ""){
            $select->where("quantity = ?",$condition["quantity"]);
        }
        if(isset($condition["reserve_price_currency"]) && $condition["reserve_price_currency"] != ""){
            $select->where("reserve_price_currency = ?",$condition["reserve_price_currency"]);
        }
        if(isset($condition["reserve_price"]) && $condition["reserve_price"] != ""){
            $select->where("reserve_price = ?",$condition["reserve_price"]);
        }
        if(isset($condition["revise_status_item_revised"]) && $condition["revise_status_item_revised"] != ""){
            $select->where("revise_status_item_revised = ?",$condition["revise_status_item_revised"]);
        }
        if(isset($condition["site"]) && $condition["site"] != ""){
            $select->where("site = ?",$condition["site"]);
        }
        if(isset($condition["start_price_currency"]) && $condition["start_price_currency"] != ""){
            $select->where("start_price_currency = ?",$condition["start_price_currency"]);
        }
        if(isset($condition["start_price"]) && $condition["start_price"] != ""){
            $select->where("start_price = ?",$condition["start_price"]);
        }
        if(isset($condition["time_left"]) && $condition["time_left"] != ""){
            $select->where("time_left = ?",$condition["time_left"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["uuid"]) && $condition["uuid"] != ""){
            $select->where("uuid = ?",$condition["uuid"]);
        }
        if(isset($condition["hit_count"]) && $condition["hit_count"] != ""){
            $select->where("hit_count = ?",$condition["hit_count"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["postal_code"]) && $condition["postal_code"] != ""){
            $select->where("postal_code = ?",$condition["postal_code"]);
        }
        if(isset($condition["dispatch_time_max"]) && $condition["dispatch_time_max"] != ""){
            $select->where("dispatch_time_max = ?",$condition["dispatch_time_max"]);
        }
        if(isset($condition["proxy_item"]) && $condition["proxy_item"] != ""){
            $select->where("proxy_item = ?",$condition["proxy_item"]);
        }
        if(isset($condition["buyer_guarantee_price_currency"]) && $condition["buyer_guarantee_price_currency"] != ""){
            $select->where("buyer_guarantee_price_currency = ?",$condition["buyer_guarantee_price_currency"]);
        }
        if(isset($condition["buyer_guarantee_price"]) && $condition["buyer_guarantee_price"] != ""){
            $select->where("buyer_guarantee_price = ?",$condition["buyer_guarantee_price"]);
        }
        if(isset($condition["intangible_item"]) && $condition["intangible_item"] != ""){
            $select->where("intangible_item = ?",$condition["intangible_item"]);
        }
        if(isset($condition["condition_id"]) && $condition["condition_id"] != ""){
            $select->where("condition_id = ?",$condition["condition_id"]);
        }
        if(isset($condition["condition_display_name"]) && $condition["condition_display_name"] != ""){
            $select->where("condition_display_name = ?",$condition["condition_display_name"]);
        }
        if(isset($condition["post_checkout_experience_enabled"]) && $condition["post_checkout_experience_enabled"] != ""){
            $select->where("post_checkout_experience_enabled = ?",$condition["post_checkout_experience_enabled"]);
        }
        if(isset($condition["hide_from_search"]) && $condition["hide_from_search"] != ""){
            $select->where("hide_from_search = ?",$condition["hide_from_search"]);
        }
        if(isset($condition["primaryCategory_category_id"]) && $condition["primaryCategory_category_id"] != ""){
            $select->where("primaryCategory_category_id = ?",$condition["primaryCategory_category_id"]);
        }
        if(isset($condition["primaryCategory_Category_name"]) && $condition["primaryCategory_Category_name"] != ""){
            $select->where("primaryCategory_Category_name = ?",$condition["primaryCategory_Category_name"]);
        }
        if(isset($condition["picture_details_gallery_type"]) && $condition["picture_details_gallery_type"] != ""){
            $select->where("picture_details_gallery_type = ?",$condition["picture_details_gallery_type"]);
        }
        if(isset($condition["picture_details_gallery_url"]) && $condition["picture_details_gallery_url"] != ""){
            $select->where("picture_details_gallery_url = ?",$condition["picture_details_gallery_url"]);
        }
        if(isset($condition["picture_details_photo_display"]) && $condition["picture_details_photo_display"] != ""){
            $select->where("picture_details_photo_display = ?",$condition["picture_details_photo_display"]);
        }
        if(isset($condition["picture_details_picture_url"]) && $condition["picture_details_picture_url"] != ""){
            $select->where("picture_details_picture_url = ?",$condition["picture_details_picture_url"]);
        }
        if(isset($condition["picture_details_picture_source"]) && $condition["picture_details_picture_source"] != ""){
            $select->where("picture_details_picture_source = ?",$condition["picture_details_picture_source"]);
        }
        if(isset($condition["selling_status_bid_count"]) && $condition["selling_status_bid_count"] != ""){
            $select->where("selling_status_bid_count = ?",$condition["selling_status_bid_count"]);
        }
        if(isset($condition["selling_status_bid_increment"]) && $condition["selling_status_bid_increment"] != ""){
            $select->where("selling_status_bid_increment = ?",$condition["selling_status_bid_increment"]);
        }
        if(isset($condition["selling_status_converted_currentPrice"]) && $condition["selling_status_converted_currentPrice"] != ""){
            $select->where("selling_status_converted_currentPrice = ?",$condition["selling_status_converted_currentPrice"]);
        }
        if(isset($condition["selling_status_current_price"]) && $condition["selling_status_current_price"] != ""){
            $select->where("selling_status_current_price = ?",$condition["selling_status_current_price"]);
        }
        if(isset($condition["selling_status_minimum_to_bid"]) && $condition["selling_status_minimum_to_bid"] != ""){
            $select->where("selling_status_minimum_to_bid = ?",$condition["selling_status_minimum_to_bid"]);
        }
        if(isset($condition["selling_status_bid_increment_currency"]) && $condition["selling_status_bid_increment_currency"] != ""){
            $select->where("selling_status_bid_increment_currency = ?",$condition["selling_status_bid_increment_currency"]);
        }
        if(isset($condition["selling_status_converted_currentPrice_currency"]) && $condition["selling_status_converted_currentPrice_currency"] != ""){
            $select->where("selling_status_converted_currentPrice_currency = ?",$condition["selling_status_converted_currentPrice_currency"]);
        }
        if(isset($condition["selling_status_current_price_currency"]) && $condition["selling_status_current_price_currency"] != ""){
            $select->where("selling_status_current_price_currency = ?",$condition["selling_status_current_price_currency"]);
        }
        if(isset($condition["selling_status_minimum_to_bid_currency"]) && $condition["selling_status_minimum_to_bid_currency"] != ""){
            $select->where("selling_status_minimum_to_bid_currency = ?",$condition["selling_status_minimum_to_bid_currency"]);
        }
        if(isset($condition["selling_status_lead_count"]) && $condition["selling_status_lead_count"] != ""){
            $select->where("selling_status_lead_count = ?",$condition["selling_status_lead_count"]);
        }
        if(isset($condition["selling_status_quantitys_old"]) && $condition["selling_status_quantitys_old"] != ""){
            $select->where("selling_status_quantitys_old = ?",$condition["selling_status_quantitys_old"]);
        }
        if(isset($condition["selling_status_reserve_met"]) && $condition["selling_status_reserve_met"] != ""){
            $select->where("selling_status_reserve_met = ?",$condition["selling_status_reserve_met"]);
        }
        if(isset($condition["selling_status_second_chance_eligible"]) && $condition["selling_status_second_chance_eligible"] != ""){
            $select->where("selling_status_second_chance_eligible = ?",$condition["selling_status_second_chance_eligible"]);
        }
        if(isset($condition["selling_status_listing_status"]) && $condition["selling_status_listing_status"] != ""){
            $select->where("selling_status_listing_status = ?",$condition["selling_status_listing_status"]);
        }
        if(isset($condition["selling_status_quantity_sold_by_pickup_in_store"]) && $condition["selling_status_quantity_sold_by_pickup_in_store"] != ""){
            $select->where("selling_status_quantity_sold_by_pickup_in_store = ?",$condition["selling_status_quantity_sold_by_pickup_in_store"]);
        }
        if(isset($condition["business_seller_details_address_street1"]) && $condition["business_seller_details_address_street1"] != ""){
            $select->where("business_seller_details_address_street1 = ?",$condition["business_seller_details_address_street1"]);
        }
        if(isset($condition["business_seller_details_address_city_name"]) && $condition["business_seller_details_address_city_name"] != ""){
            $select->where("business_seller_details_address_city_name = ?",$condition["business_seller_details_address_city_name"]);
        }
        if(isset($condition["business_seller_details_address_state_or_province"]) && $condition["business_seller_details_address_state_or_province"] != ""){
            $select->where("business_seller_details_address_state_or_province = ?",$condition["business_seller_details_address_state_or_province"]);
        }
        if(isset($condition["business_seller_details_address_country_name"]) && $condition["business_seller_details_address_country_name"] != ""){
            $select->where("business_seller_details_address_country_name = ?",$condition["business_seller_details_address_country_name"]);
        }
        if(isset($condition["business_seller_details_address_phone"]) && $condition["business_seller_details_address_phone"] != ""){
            $select->where("business_seller_details_address_phone = ?",$condition["business_seller_details_address_phone"]);
        }
        if(isset($condition["business_seller_details_address_postal_code"]) && $condition["business_seller_details_address_postal_code"] != ""){
            $select->where("business_seller_details_address_postal_code = ?",$condition["business_seller_details_address_postal_code"]);
        }
        if(isset($condition["business_seller_details_address_company_name"]) && $condition["business_seller_details_address_company_name"] != ""){
            $select->where("business_seller_details_address_company_name = ?",$condition["business_seller_details_address_company_name"]);
        }
        if(isset($condition["business_seller_details_address_first_name"]) && $condition["business_seller_details_address_first_name"] != ""){
            $select->where("business_seller_details_address_first_name = ?",$condition["business_seller_details_address_first_name"]);
        }
        if(isset($condition["business_seller_details_address_last_name"]) && $condition["business_seller_details_address_last_name"] != ""){
            $select->where("business_seller_details_address_last_name = ?",$condition["business_seller_details_address_last_name"]);
        }
        if(isset($condition["business_seller_details_email"]) && $condition["business_seller_details_email"] != ""){
            $select->where("business_seller_details_email = ?",$condition["business_seller_details_email"]);
        }
        if(isset($condition["business_seller_details_legal_invoice"]) && $condition["business_seller_details_legal_invoice"] != ""){
            $select->where("business_seller_details_legal_invoice = ?",$condition["business_seller_details_legal_invoice"]);
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