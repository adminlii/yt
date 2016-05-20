<?php
class Service_MagentoOrderDetail extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_MagentoOrderDetail|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_MagentoOrderDetail();
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
    public static function update($row, $value, $field = "mod_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "mod_id")
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
    public static function getByField($value, $field = 'mod_id', $colums = "*")
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
        
              'E0'=>'mod_id',
              'E1'=>'mo_id',
              'E2'=>'item_id',
              'E3'=>'order_id',
              'E4'=>'parent_item_id',
              'E5'=>'quote_item_id',
              'E6'=>'store_id',
              'E7'=>'created_at',
              'E8'=>'updated_at',
              'E9'=>'product_id',
              'E10'=>'product_type',
              'E11'=>'weight',
              'E12'=>'is_virtual',
              'E13'=>'sku',
              'E14'=>'name',
              'E15'=>'description',
              'E16'=>'applied_rule_ids',
              'E17'=>'additional_data',
              'E18'=>'free_shipping',
              'E19'=>'is_qty_decimal',
              'E20'=>'no_discount',
              'E21'=>'qty_backordered',
              'E22'=>'qty_canceled',
              'E23'=>'qty_invoiced',
              'E24'=>'qty_ordered',
              'E25'=>'qty_refunded',
              'E26'=>'qty_shipped',
              'E27'=>'base_cost',
              'E28'=>'price',
              'E29'=>'base_price',
              'E30'=>'original_price',
              'E31'=>'base_original_price',
              'E32'=>'tax_percent',
              'E33'=>'tax_amount',
              'E34'=>'base_tax_amount',
              'E35'=>'tax_invoiced',
              'E36'=>'base_tax_invoiced',
              'E37'=>'discount_percent',
              'E38'=>'discount_amount',
              'E39'=>'base_discount_amount',
              'E40'=>'discount_invoiced',
              'E41'=>'base_discount_invoiced',
              'E42'=>'amount_refunded',
              'E43'=>'base_amount_refunded',
              'E44'=>'row_total',
              'E45'=>'base_row_total',
              'E46'=>'row_invoiced',
              'E47'=>'base_row_invoiced',
              'E48'=>'row_weight',
              'E49'=>'base_tax_before_discount',
              'E50'=>'tax_before_discount',
              'E51'=>'ext_order_item_id',
              'E52'=>'locked_do_invoice',
              'E53'=>'locked_do_ship',
              'E54'=>'price_incl_tax',
              'E55'=>'base_price_incl_tax',
              'E56'=>'row_total_incl_tax',
              'E57'=>'base_row_total_incl_tax',
              'E58'=>'hidden_tax_amount',
              'E59'=>'base_hidden_tax_amount',
              'E60'=>'hidden_tax_invoiced',
              'E61'=>'base_hidden_tax_invoiced',
              'E62'=>'hidden_tax_refunded',
              'E63'=>'base_hidden_tax_refunded',
              'E64'=>'is_nominal',
              'E65'=>'tax_canceled',
              'E66'=>'hidden_tax_canceled',
              'E67'=>'tax_refunded',
              'E68'=>'base_tax_refunded',
              'E69'=>'discount_refunded',
              'E70'=>'base_discount_refunded',
              'E71'=>'gift_message_id',
              'E72'=>'gift_message_available',
              'E73'=>'base_weee_tax_applied_amount',
              'E74'=>'base_weee_tax_applied_row_amnt',
              'E75'=>'base_weee_tax_applied_row_amount',
              'E76'=>'weee_tax_applied_amount',
              'E77'=>'weee_tax_applied_row_amount',
              'E78'=>'weee_tax_applied',
              'E79'=>'weee_tax_disposition',
              'E80'=>'weee_tax_row_disposition',
              'E81'=>'base_weee_tax_disposition',
              'E82'=>'base_weee_tax_row_disposition',
              'E83'=>'has_children',
              'E84'=>'company_code',
              'E85'=>'user_account',
              'E86'=>'create_time_sys',
              'E87'=>'update_time_sys',
        );
        return $row;
    }

}