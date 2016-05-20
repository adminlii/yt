<?php
class Service_MagentoOrderAddress extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_MagentoOrderAddress|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_MagentoOrderAddress();
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
    public static function update($row, $value, $field = "moa_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "moa_id")
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
    public static function getByField($value, $field = 'moa_id', $colums = "*")
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
        
              'E0'=>'moa_id',
              'E1'=>'mo_id',
              'E2'=>'entity_id',
              'E3'=>'parent_id',
              'E4'=>'customer_address_id',
              'E5'=>'quote_address_id',
              'E6'=>'region_id',
              'E7'=>'customer_id',
              'E8'=>'fax',
              'E9'=>'region',
              'E10'=>'postcode',
              'E11'=>'lastname',
              'E12'=>'street',
              'E13'=>'city',
              'E14'=>'email',
              'E15'=>'telephone',
              'E16'=>'country_id',
              'E17'=>'firstname',
              'E18'=>'address_type',
              'E19'=>'prefix',
              'E20'=>'middlename',
              'E21'=>'suffix',
              'E22'=>'company',
              'E23'=>'vat_id',
              'E24'=>'vat_is_valid',
              'E25'=>'vat_request_id',
              'E26'=>'vat_request_date',
              'E27'=>'vat_request_success',
              'E28'=>'company_code',
              'E29'=>'user_account',
              'E30'=>'create_time_sys',
              'E31'=>'update_time_sys',
        );
        return $row;
    }

}