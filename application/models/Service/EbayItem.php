<?php
class Service_EbayItem extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayItem|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayItem();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "id")
    {
        $model = self::getModelInstance();
        return $model->delete($value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'id',
              'E1'=>'item_id',
              'E2'=>'listing_type',
              'E3'=>'listing_duration',
              'E4'=>'auto_pay',
              'E5'=>'buyer_protection',
              'E6'=>'buy_it_now_price',
              'E7'=>'buy_it_now_price_currency',
              'E8'=>'country',
              'E9'=>'currency',
              'E10'=>'gift_icon',
              'E11'=>'hit_counter',
              'E12'=>'location',
              'E13'=>'payment_methods',
              'E14'=>'paypal_email',
              'E15'=>'private_listing',
              'E16'=>'quantity',
              'E17'=>'reserve_price_currency',
              'E18'=>'reserve_price',
              'E19'=>'revise_status_item_revised',
              'E20'=>'site',
              'E21'=>'start_price_currency',
              'E22'=>'start_price',
              'E23'=>'time_left',
              'E24'=>'title',
              'E25'=>'uuid',
              'E26'=>'hit_count',
              'E27'=>'sku',
              'E28'=>'postal_code',
              'E29'=>'dispatch_time_max',
              'E30'=>'proxy_item',
              'E31'=>'buyer_guarantee_price_currency',
              'E32'=>'buyer_guarantee_price',
              'E33'=>'intangible_item',
              'E34'=>'condition_id',
              'E35'=>'condition_display_name',
              'E36'=>'post_checkout_experience_enabled',
              'E37'=>'hide_from_search',
              'E38'=>'primaryCategory_category_id',
              'E39'=>'primaryCategory_Category_name',
              'E40'=>'picture_details_gallery_type',
              'E41'=>'picture_details_gallery_url',
              'E42'=>'picture_details_photo_display',
              'E43'=>'picture_details_picture_url',
              'E44'=>'picture_details_picture_source',
              'E45'=>'selling_status_bid_count',
              'E46'=>'selling_status_bid_increment',
              'E47'=>'selling_status_converted_currentPrice',
              'E48'=>'selling_status_current_price',
              'E49'=>'selling_status_minimum_to_bid',
              'E50'=>'selling_status_bid_increment_currency',
              'E51'=>'selling_status_converted_currentPrice_currency',
              'E52'=>'selling_status_current_price_currency',
              'E53'=>'selling_status_minimum_to_bid_currency',
              'E54'=>'selling_status_lead_count',
              'E55'=>'selling_status_quantitys_old',
              'E56'=>'selling_status_reserve_met',
              'E57'=>'selling_status_second_chance_eligible',
              'E58'=>'selling_status_listing_status',
              'E59'=>'selling_status_quantity_sold_by_pickup_in_store',
              'E60'=>'business_seller_details_address_street1',
              'E61'=>'business_seller_details_address_city_name',
              'E62'=>'business_seller_details_address_state_or_province',
              'E63'=>'business_seller_details_address_country_name',
              'E64'=>'business_seller_details_address_phone',
              'E65'=>'business_seller_details_address_postal_code',
              'E66'=>'business_seller_details_address_company_name',
              'E67'=>'business_seller_details_address_first_name',
              'E68'=>'business_seller_details_address_last_name',
              'E69'=>'business_seller_details_email',
              'E70'=>'business_seller_details_legal_invoice',
        );
        return $row;
    }

}