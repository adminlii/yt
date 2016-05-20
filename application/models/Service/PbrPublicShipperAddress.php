<?php
class Service_PbrPublicShipperAddress extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PbrPublicShipperAddress|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PbrPublicShipperAddress();
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
    public static function update($row, $value, $field = "psa_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "psa_id")
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
    public static function getByField($value, $field = 'psa_id', $colums = "*")
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
        
              'E0'=>'psa_id',
              'E1'=>'tms_id',
              'E2'=>'product_code',
              'E3'=>'server_channelid',
              'E4'=>'country_code',
              'E5'=>'shipper_account',
              'E6'=>'shipper_name',
              'E7'=>'shipper_company',
              'E8'=>'shipper_countrycode',
              'E9'=>'shipper_province',
              'E10'=>'shipper_city',
              'E11'=>'shipper_street',
              'E12'=>'shipper_postcode',
              'E13'=>'shipper_areacode',
              'E14'=>'shipper_telephone',
              'E15'=>'shipper_mobile',
              'E16'=>'shipper_email',
              'E17'=>'shipper_certificatetype',
              'E18'=>'shipper_certificatecode',
              'E19'=>'shipper_fax',
              'E20'=>'shipper_mallaccount',
              'E21'=>'create_date_sys',
              'E22'=>'modify_date_sys',
              'E23'=>'modify_st_id',
        );
        return $row;
    }

}