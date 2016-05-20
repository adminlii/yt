<?php
class Service_ShopifyShop extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ShopifyShop|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ShopifyShop();
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
              'E1'=>'address1',
              'E2'=>'city',
              'E3'=>'country',
              'E4'=>'created_at',
              'E5'=>'customer_email',
              'E6'=>'domain',
              'E7'=>'email',
              'E8'=>'latitude',
              'E9'=>'longitude',
              'E10'=>'name',
              'E11'=>'phone',
              'E12'=>'primary_location_id',
              'E13'=>'province',
              'E14'=>'public',
              'E15'=>'source',
              'E16'=>'zip',
              'E17'=>'country_code',
              'E18'=>'country_name',
              'E19'=>'currency',
              'E20'=>'timezone',
              'E21'=>'shop_owner',
              'E22'=>'money_format',
              'E23'=>'money_with_currency_format',
              'E24'=>'province_code',
              'E25'=>'taxes_included',
              'E26'=>'tax_shipping',
              'E27'=>'county_taxes',
              'E28'=>'plan_display_name',
              'E29'=>'plan_name',
              'E30'=>'myshopify_domain',
              'E31'=>'google_apps_domain',
              'E32'=>'google_apps_login_enabled',
              'E33'=>'money_in_emails_format',
              'E34'=>'money_with_currency_in_emails_format',
              'E35'=>'eligible_for_payments',
              'E36'=>'requires_extra_payments_agreement',
              'E37'=>'password_enabled',
              'E38'=>'has_storefront',
        );
        return $row;
    }

}