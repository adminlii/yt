<?php
class Service_ShopifyOrderFulfillmentsLineItems extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ShopifyOrderFulfillmentsLineItems|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ShopifyOrderFulfillmentsLineItems();
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
              'E1'=>'order_id',
              'E2'=>'fulfillment_id',
              'E3'=>'fulfillment_service',
              'E4'=>'fulfillment_status',
              'E5'=>'gift_card',
              'E6'=>'grams',
              'E7'=>'price',
              'E8'=>'product_id',
              'E9'=>'quantity',
              'E10'=>'requires_shipping',
              'E11'=>'sku',
              'E12'=>'taxable',
              'E13'=>'title',
              'E14'=>'variant_id',
              'E15'=>'variant_title',
              'E16'=>'vendor',
              'E17'=>'name',
              'E18'=>'variant_inventory_management',
              'E19'=>'properties',
              'E20'=>'product_exists',
              'E21'=>'fulfillable_quantity',
              'E22'=>'tax_lines',
        );
        return $row;
    }

}