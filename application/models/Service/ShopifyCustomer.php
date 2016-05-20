<?php
class Service_ShopifyCustomer extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ShopifyCustomer|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ShopifyCustomer();
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
              'E1'=>'accepts_marketing',
              'E2'=>'created_at',
              'E3'=>'email',
              'E4'=>'first_name',
              'E5'=>'last_name',
              'E6'=>'last_order_id',
              'E7'=>'multipass_identifier',
              'E8'=>'note',
              'E9'=>'orders_count',
              'E10'=>'state',
              'E11'=>'total_spent',
              'E12'=>'updated_at',
              'E13'=>'verified_email',
              'E14'=>'tags',
              'E15'=>'last_order_name',
              'E16'=>'default_address_id',
              'E17'=>'default_address_address1',
              'E18'=>'default_address_address2',
              'E19'=>'default_address_city',
              'E20'=>'default_address_company',
              'E21'=>'default_address_country',
              'E22'=>'default_address_first_name',
              'E23'=>'default_address_last_name',
              'E24'=>'default_address_phone',
              'E25'=>'default_address_province',
              'E26'=>'default_address_zip',
              'E27'=>'default_address_name',
              'E28'=>'default_address_province_code',
              'E29'=>'default_address_country_code',
              'E30'=>'default_address_country_name',
              'E31'=>'default_address_default',
        );
        return $row;
    }

}