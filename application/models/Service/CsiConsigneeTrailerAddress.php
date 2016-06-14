<?php
class Service_CsiConsigneeTrailerAddress extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_CsiconsigneeTrailerAddress|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_CsiConsigneeTrailerAddress();
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
    public static function update($row, $value, $field = "consignee_account")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "consignee_account")
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
    public static function getByField($value, $field = 'consignee_account', $colums = "*")
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
    public static function validator($val){
    	$validateArr=array();
    	$validateArr[] = array("name" =>EC::Lang('联系人'), "value" =>$val["consignee_name"], "regex" => array("require",));
    	//$validateArr[] = array("name" =>EC::Lang('公司名'), "value" =>$val["consignee_company"], "regex" => array("require",));
    	$validateArr[] = array("name" =>EC::Lang('国家'), "value" =>$val["consignee_countrycode"], "regex" => array("require",));
    	//$validateArr[] = array("name" =>EC::Lang('省/州'), "value" =>$val["consignee_province"], "regex" => array("require",));
    	$validateArr[] = array("name" =>EC::Lang('城市'), "value" =>$val["consignee_city"], "regex" => array("require",));
    	$validateArr[] = array("name" =>EC::Lang('联系地址'), "value" =>$val["consignee_street"], "regex" => array("require",));
    	$validateArr[] = array("name" =>EC::Lang('邮编'), "value" =>$val["consignee_postcode"], "regex" => array("require",));
    	$validateArr[] = array("name" =>EC::Lang('联系电话'), "value" =>$val["consignee_telephone"], "regex" => array("require",));
    	return  Common_Validator::formValidator($validateArr);
    }
   
    
    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
              'E0'=>'consignee_account',
              'E1'=>'customer_id',
              'E2'=>'consignee_name',
              'E3'=>'consignee_company',
              'E4'=>'consignee_countrycode',
              'E5'=>'consignee_province',
              'E6'=>'consignee_city',
              'E7'=>'consignee_street',
              'E8'=>'consignee_postcode',
              'E9'=>'consignee_areacode',
              'E10'=>'consignee_telephone',
              'E11'=>'consignee_mobile',
              'E12'=>'consignee_email',
              'E13'=>'consignee_certificatetype',
              'E14'=>'consignee_certificatecode',
              'E15'=>'consignee_fax',
              'E16'=>'consignee_mallaccount',
              'E17'=>'is_default',
              'E18'=>'create_date_sys',
              'E19'=>'modify_date_sys',
              'E20'=>'is_modify',
        	  'E21'=>'consignee_street1',
        	  'E22'=>'consignee_street2',
        );
        return $row;
    }

}