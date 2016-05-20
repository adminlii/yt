<?php
class Service_AmazonOrderOriginal extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AmazonOrderOriginal|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AmazonOrderOriginal();
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
    public static function update($row, $value, $field = "aoo_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "aoo_id")
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
    public static function getByField($value, $field = 'aoo_id', $colums = "*")
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
        
              'E0'=>'aoo_id',
              'E1'=>'amazon_order_id',
              'E2'=>'seller_order_id',
              'E3'=>'user_account',
              'E4'=>'purchase_date',
              'E5'=>'last_update_date',
              'E6'=>'order_status',
              'E7'=>'fulfillment_channel',
              'E8'=>'sales_channel',
              'E9'=>'order_channel',
              'E10'=>'ship_service_level',
              'E11'=>'order_type',
              'E12'=>'currency_code',
              'E13'=>'amount',
              'E14'=>'payment_method',
              'E15'=>'marketplace_id',
              'E16'=>'buyer_email',
              'E17'=>'buyer_name',
              'E18'=>'earliest_ship_date',
              'E19'=>'latest_ship_date',
              'E20'=>'shipment_service_level_category',
              'E21'=>'shipped_amazon_tfm',
              'E22'=>'tfm_shipment_status',
              'E23'=>'cba_displayable_shipping_label',
              'E24'=>'number_items_shipped',
              'E25'=>'number_items_unshipped',
              'E26'=>'shipping_address_name',
              'E27'=>'shipping_address_phone',
              'E28'=>'shipping_address_country_code',
              'E29'=>'shipping_address_state',
              'E30'=>'shipping_address_district',
              'E31'=>'shipping_address_county',
              'E32'=>'shipping_address_city',
              'E33'=>'shipping_address_postal_code',
              'E34'=>'shipping_address_address1',
              'E35'=>'shipping_address_address2',
              'E36'=>'shipping_address_address3',
              'E37'=>'request_id',
              'E38'=>'is_loaded',
        );
        return $row;
    }

}