<?php
class Service_CsiShipperTrailerAddress extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_CsiShipperTrailerAddress|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_CsiShipperTrailerAddress();
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
    public static function update($row, $value, $field = "shipper_account")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "shipper_account")
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
    public static function getByField($value, $field = 'shipper_account', $colums = "*")
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
        $validateArr[] = array("name" =>EC::Lang('发件人名称'), "value" =>$val["shipper_name"], "regex" => array("require",));
        //$validateArr[] = array("name" =>EC::Lang('公司名'), "value" =>$val["shipper_company"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('国家'), "value" =>$val["shipper_countrycode"], "regex" => array("require",));
        //$validateArr[] = array("name" =>EC::Lang('省/州'), "value" =>$val["shipper_province"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('城市'), "value" =>$val["shipper_city"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('联系地址'), "value" =>$val["shipper_street"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('邮编'), "value" =>$val["shipper_postcode"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('联系电话'), "value" =>$val["shipper_telephone"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }
    
    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'shipper_account',
              'E1'=>'customer_id',
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
              'E13'=>'shipper_certificatetype',
              'E14'=>'shipper_certificatecode',
              'E15'=>'shipper_fax',
              'E16'=>'shipper_mallaccount',
              'E17'=>'is_default',
              'E18'=>'create_date_sys',
              'E19'=>'modify_date_sys',
              'E20'=>'is_modify',
        );
        return $row;
    }

}