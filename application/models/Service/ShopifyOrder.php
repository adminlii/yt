<?php
class Service_ShopifyOrder extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ShopifyOrder|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ShopifyOrder();
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
              'E1'=>'customer_id',
              'E2'=>'buyer_accepts_marketing',
              'E3'=>'cancel_reason',
              'E4'=>'cancelled_at',
              'E5'=>'cart_token',
              'E6'=>'checkout_token',
              'E7'=>'closed_at',
              'E8'=>'confirmed',
              'E9'=>'created_at',
              'E10'=>'currency',
              'E11'=>'email',
              'E12'=>'financial_status',
              'E13'=>'fulfillment_status',
              'E14'=>'gateway',
              'E15'=>'landing_site',
              'E16'=>'location_id',
              'E17'=>'name',
              'E18'=>'note',
              'E19'=>'number',
              'E20'=>'reference',
              'E21'=>'referring_site',
              'E22'=>'source',
              'E23'=>'source_identifier',
              'E24'=>'source_name',
              'E25'=>'source_url',
              'E26'=>'subtotal_price',
              'E27'=>'taxes_included',
              'E28'=>'test',
              'E29'=>'token',
              'E30'=>'total_discounts',
              'E31'=>'total_line_items_price',
              'E32'=>'total_price',
              'E33'=>'total_price_usd',
              'E34'=>'total_tax',
              'E35'=>'total_weight',
              'E36'=>'updated_at',
              'E37'=>'user_id',
              'E38'=>'browser_ip',
              'E39'=>'landing_site_ref',
              'E40'=>'order_number',
              'E41'=>'discount_codes',
              'E42'=>'note_attributes',
              'E43'=>'processing_method',
              'E44'=>'checkout_id',
              'E45'=>'tax_lines',
              'E46'=>'tags',
        );
        return $row;
    }

}