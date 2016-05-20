<?php
class Service_EbayOrderDetail extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayOrderDetail|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayOrderDetail();
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
    public static function update($row, $value, $field = "eod_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "eod_id")
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
    public static function getByField($value, $field = 'eod_id', $colums = "*")
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
        
              'E0'=>'eod_id',
              'E1'=>'order_sn',
              'E2'=>'item_id',
              'E3'=>'actual_handling_cost',
              'E4'=>'actual_handling_cost_currency',
              'E5'=>'actual_shipping_cost',
              'E6'=>'actual_shipping_cost_currency',
              'E7'=>'buyer_email',
              'E8'=>'created_date',
              'E9'=>'final_value_fee',
              'E10'=>'final_value_fee_currency',
              'E11'=>'transaction_site_id',
              'E12'=>'platform',
              'E13'=>'invoice_sent_time',
              'E14'=>'order_line_item_id',
              'E15'=>'quantity_purchased',
              'E16'=>'shipped_time',
              'E17'=>'transaction_id',
              'E18'=>'transaction_price',
              'E19'=>'transaction_price_currency',
              'E20'=>'integrated_merchant_credit_card_enabled',
              'E21'=>'application_data',
              'E22'=>'seller_inventory_id',
              'E23'=>'site',
              'E24'=>'sku',
              'E25'=>'title',
              'E26'=>'url',
              'E27'=>'condition_id',
              'E28'=>'condition_display_name',
              'E29'=>'selling_manager_sales_record_number',
              'E30'=>'payment_hold_status',
              'E31'=>'payment_method_used',
              'E32'=>'create_time_sys',
              'E33'=>'company_code',
              'E34'=>'user_account',
              'E35'=>'update_time_sys',
        );
        return $row;
    }

}