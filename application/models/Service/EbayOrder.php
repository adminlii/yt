<?php
class Service_EbayOrder extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayOrder|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayOrder();
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
    public static function update($row, $value, $field = "eo_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "eo_id")
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
    public static function getByField($value, $field = 'eo_id', $colums = "*")
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
        
              'E0'=>'eo_id',
              'E1'=>'order_sn',
              'E2'=>'subtotal',
              'E3'=>'subtotal_currency',
              'E4'=>'total',
              'E5'=>'total_currency',
              'E6'=>'adjustment_amount',
              'E7'=>'adjustment_amount_currency',
              'E8'=>'amoun_paid',
              'E9'=>'amoun_paid_currency',
              'E10'=>'amount_saved',
              'E11'=>'amount_saved_currency',
              'E12'=>'buyer_checkout_message',
              'E13'=>'buyer_user_id',
              'E14'=>'cancel_reason',
              'E15'=>'created_time',
              'E16'=>'paid_time',
              'E17'=>'shipped_time',
              'E18'=>'creating_user_role',
              'E19'=>'eias_token',
              'E20'=>'integrated_merchant_credit_card_enabled',
              'E21'=>'is_multi_leg_shipping',
              'E22'=>'order_status',
              'E23'=>'payment_hold_status',
              'E24'=>'seller_eias_token',
              'E25'=>'seller_email',
              'E26'=>'seller_user_id',
              'E27'=>'ebay_payment_status',
              'E28'=>'last_modified_time',
              'E29'=>'payment_method',
              'E30'=>'checkout_status',
              'E31'=>'address_id',
              'E32'=>'address_owner',
              'E33'=>'city_name',
              'E34'=>'country',
              'E35'=>'country_name',
              'E36'=>'external_address_id',
              'E37'=>'consignee_name',
              'E38'=>'consignee_phone',
              'E39'=>'consignee_zip',
              'E40'=>'consignee_state',
              'E41'=>'consignee_street1',
              'E42'=>'consignee_street2',
              'E43'=>'shipping_service',
              'E44'=>'shipping_service_cost',
              'E45'=>'shipping_service_cost_currency',
              'E46'=>'shipping_service_priority',
              'E47'=>'shipping_service_additional_cost',
              'E48'=>'shipping_service_additional_cost_currency',
              'E49'=>'shipping_insurance_cost',
              'E50'=>'shipping_insurance_cost_currency',
              'E51'=>'import_charge',
              'E52'=>'importCharge_currency',
              'E53'=>'expedited_service',
              'E54'=>'payment_methods',
              'E55'=>'company_code',
              'E56'=>'user_account',
              'E57'=>'create_time_sys',
              'E58'=>'update_time_sys',
        );
        return $row;
    }

}