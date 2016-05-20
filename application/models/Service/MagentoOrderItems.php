<?php
class Service_MagentoOrderItems extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_MagentoOrderItems|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_MagentoOrderItems();
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
    public static function update($row, $value, $field = "moi_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "moi_id")
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
    public static function getByField($value, $field = 'moi_id', $colums = "*")
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
        
              'E0'=>'moi_id',
              'E1'=>'mo_id',
              'E2'=>'item_id',
              'E3'=>'order_id',
              'E4'=>'quote_item_id',
              'E5'=>'created_at',
              'E6'=>'updated_at',
              'E7'=>'product_id',
              'E8'=>'product_type',
              'E9'=>'product_options',
              'E10'=>'weight',
              'E11'=>'is_virtual',
              'E12'=>'sku',
              'E13'=>'name',
              'E14'=>'free_shipping',
              'E15'=>'is_qty_decimal',
              'E16'=>'no_discount',
              'E17'=>'qty_canceled',
              'E18'=>'qty_invoiced',
              'E19'=>'qty_ordered',
              'E20'=>'qty_refunded',
              'E21'=>'qty_shipped',
              'E22'=>'price',
              'E23'=>'base_price',
              'E24'=>'original_price',
              'E25'=>'base_original_price',
              'E26'=>'tax_percent',
              'E27'=>'tax_amount',
              'E28'=>'base_tax_amount',
              'E29'=>'tax_invoiced',
              'E30'=>'base_tax_invoiced',
              'E31'=>'discount_percent',
              'E32'=>'discount_amount',
              'E33'=>'base_discount_amount',
              'E34'=>'discount_invoiced',
              'E35'=>'base_discount_invoiced',
              'E36'=>'amount_refunded',
              'E37'=>'base_amount_refunded',
              'E38'=>'row_total',
              'E39'=>'base_row_total',
              'E40'=>'row_invoiced',
              'E41'=>'base_row_invoiced',
              'E42'=>'row_weight',
              'E43'=>'weee_tax_applied',
              'E44'=>'weee_tax_applied_amount',
              'E45'=>'weee_tax_applied_row_amount',
              'E46'=>'base_weee_tax_applied_amount',
              'E47'=>'base_weee_tax_applied_row_amount',
              'E48'=>'weee_tax_disposition',
              'E49'=>'weee_tax_row_disposition',
              'E50'=>'base_weee_tax_disposition',
              'E51'=>'base_weee_tax_row_disposition',
        );
        return $row;
    }

}