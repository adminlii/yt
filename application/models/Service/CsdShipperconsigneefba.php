<?php
class Service_CsdShipperconsigneefba extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_CsdShipperconsignee|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_CsdShipperconsigneefba();
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
    public static function update($row, $value, $field = "order_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "order_id")
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
    public static function getByField($value, $field = 'order_id', $colums = "*")
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
        
              'E0'=>'order_id',
              'E1'=>'shipper_account',
              'E2'=>'shipper_name',
              'E3'=>'shipper_company',
              'E4'=>'shipper_countrycode',
              'E5'=>'shipper_province',
              'E6'=>'shipper_city',
              'E7'=>'shipper_street',
              'E8'=>'shipper_postcode',
              'E9'=>'shipper_areacode',
              'E10'=>'shipper_telephone',
              'E11'=>'shipper_mobile',
              'E12'=>'shipper_email',
              'E13'=>'shipper_certificatecode',
              'E14'=>'shipper_certificatetype',
              'E15'=>'shipper_fax',
              'E16'=>'shipper_mallaccount',
              'E17'=>'consignee_name',
              'E18'=>'consignee_company',
              'E19'=>'consignee_countrycode',
              'E20'=>'consignee_province',
              'E21'=>'consignee_city',
              'E22'=>'consignee_street',
              'E23'=>'consignee_postcode',
              'E24'=>'consignee_areacode',
              'E25'=>'consignee_telephone',
              'E26'=>'consignee_mobile',
              'E27'=>'consignee_fax',
              'E28'=>'consignee_email',
              'E29'=>'consignee_certificatecode',
              'E30'=>'consignee_mallaccount',
              'E31'=>'consignee_credentials_period',
              'E32'=>'consignee_certificatetype',
        );
        return $row;
    }

}