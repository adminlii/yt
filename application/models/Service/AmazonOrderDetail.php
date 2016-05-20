<?php
class Service_AmazonOrderDetail extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AmazonOrderDetail|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AmazonOrderDetail();
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
    public static function update($row, $value, $field = "aod_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "aod_id")
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
    public static function getByField($value, $field = 'aod_id', $colums = "*")
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
        
              'E0'=>'aod_id',
              'E1'=>'aoo_id',
              'E2'=>'amazon_order_id',
              'E3'=>'asin',
              'E4'=>'seller_sku',
              'E5'=>'order_item_id',
              'E6'=>'title',
              'E7'=>'quantity_ordered',
              'E8'=>'quantity_shipped',
              'E9'=>'gift_message_text',
              'E10'=>'gift_wrap_level',
              'E11'=>'item_price_currency_code',
              'E12'=>'item_price_amount',
              'E13'=>'shipping_price_currency_code',
              'E14'=>'shipping_price_amount',
              'E15'=>'gift_wrap_price_currency_code',
              'E16'=>'gift_wrap_price_amount',
              'E17'=>'item_tax_currency_code',
              'E18'=>'item_tax_amount',
              'E19'=>'shipping_tax_currency_code',
              'E20'=>'shipping_tax_amount',
              'E21'=>'gift_wrap_tax_currency_code',
              'E22'=>'gift_wrap_tax_amount',
              'E23'=>'shipping_discount_currency_code',
              'E24'=>'shipping_discount_amount',
              'E25'=>'promotion_discount_currency_code',
              'E26'=>'promotion_discount_amount',
              'E27'=>'cod_fee_currency_code',
              'E28'=>'cod_fee_amount',
              'E29'=>'cod_fee_discount_currency_code',
              'E30'=>'cod_fee_discount_amount',
              'E31'=>'invoice_requirement',
              'E32'=>'invoice_buyer_selected_category',
              'E33'=>'invoice_title',
              'E34'=>'invoice_information',
              'E35'=>'condition_id',
              'E36'=>'condition_subtype_id',
              'E37'=>'condition_note',
              'E38'=>'scheduled_delivery_start_date',
              'E39'=>'scheduled_delivery_end_date',
              'E40'=>'request_id',
        );
        return $row;
    }

}