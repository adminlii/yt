<?php
class Service_MagentoOrder extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_MagentoOrder|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_MagentoOrder();
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
    public static function update($row, $value, $field = "mo_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "mo_id")
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
    public static function getByField($value, $field = 'mo_id', $colums = "*")
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
        
              'E0'=>'mo_id',
              'E1'=>'increment_id',
              'E2'=>'store_id',
              'E3'=>'created_at',
              'E4'=>'updated_at',
              'E5'=>'customer_id',
              'E6'=>'tax_amount',
              'E7'=>'shipping_amount',
              'E8'=>'discount_amount',
              'E9'=>'subtotal',
              'E10'=>'grand_total',
              'E11'=>'total_qty_ordered',
              'E12'=>'total_canceled',
              'E13'=>'base_tax_amount',
              'E14'=>'base_shipping_amount',
              'E15'=>'base_discount_amount',
              'E16'=>'base_subtotal',
              'E17'=>'base_grand_total',
              'E18'=>'base_total_canceled',
              'E19'=>'billing_address_id',
              'E20'=>'billing_firstname',
              'E21'=>'billing_lastname',
              'E22'=>'shipping_address_id',
              'E23'=>'shipping_firstname',
              'E24'=>'shipping_lastname',
              'E25'=>'billing_name',
              'E26'=>'shipping_name',
              'E27'=>'store_to_base_rate',
              'E28'=>'store_to_order_rate',
              'E29'=>'base_to_global_rate',
              'E30'=>'base_to_order_rate',
              'E31'=>'weight',
              'E32'=>'store_name',
              'E33'=>'remote_ip',
              'E34'=>'status',
              'E35'=>'state',
              'E36'=>'applied_rule_ids',
              'E37'=>'global_currency_code',
              'E38'=>'base_currency_code',
              'E39'=>'store_currency_code',
              'E40'=>'order_currency_code',
              'E41'=>'shipping_method',
              'E42'=>'shipping_description',
              'E43'=>'customer_email',
              'E44'=>'customer_firstname',
              'E45'=>'customer_lastname',
              'E46'=>'quote_id',
              'E47'=>'is_virtual',
              'E48'=>'customer_group_id',
              'E49'=>'customer_note_notify',
              'E50'=>'customer_is_guest',
              'E51'=>'order_id',
              'E52'=>'coupon_code',
              'E53'=>'protect_code',
              'E54'=>'base_discount_canceled',
              'E55'=>'base_shipping_canceled',
              'E56'=>'base_shipping_tax_amount',
              'E57'=>'base_subtotal_canceled',
              'E58'=>'base_tax_canceled',
              'E59'=>'discount_canceled',
              'E60'=>'shipping_canceled',
              'E61'=>'shipping_tax_amount',
              'E62'=>'subtotal_canceled',
              'E63'=>'tax_canceled',
              'E64'=>'paypal_ipn_customer_notified',
              'E65'=>'base_shipping_discount_amount',
              'E66'=>'base_subtotal_incl_tax',
              'E67'=>'base_total_due',
              'E68'=>'shipping_discount_amount',
              'E69'=>'subtotal_incl_tax',
              'E70'=>'total_due',
              'E71'=>'discount_description',
              'E72'=>'total_item_count',
              'E73'=>'hidden_tax_amount',
              'E74'=>'base_hidden_tax_amount',
              'E75'=>'shipping_hidden_tax_amount',
              'E76'=>'base_shipping_hidden_tax_amount',
              'E77'=>'shipping_incl_tax',
              'E78'=>'base_shipping_incl_tax',
              'E79'=>'firstname',
              'E80'=>'lastname',
              'E81'=>'telephone',
              'E82'=>'postcode',
              'E83'=>'created',
              'E84'=>'company_code',
              'E85'=>'user_account',
              'E86'=>'create_time_sys',
              'E87'=>'update_time_sys',
        );
        return $row;
    }

}