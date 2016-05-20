<?php
class Service_Warehouse extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Warehouse|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Warehouse();
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
    public static function update($row, $value, $field = "warehouse_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "warehouse_id")
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
    public static function getByField($value, $field = 'warehouse_id', $colums = "*")
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

    public static function getJoinLeftByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getJoinLeftByCondition($condition, $type, $pageSize, $page, $order);
    }

    public static function getGroupCountryByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getGroupCountryByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * 获取非中转仓信息
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getStandardWarehouse($type = array("warehouse_id","warehouse_code","warehouse_desc"))
    {
    	$model = self::getModelInstance();
    	return $model->getStandardWarehouse($type = array("warehouse_id","warehouse_code","warehouse_desc"));
    }

    /**
     * @desc获取海外仓库
     * @param array $order
     * @return mixed
     */
    public static function getOtherWarehouse($order = array("country_id", "warehouse_code"))
    {
        $configRow = Service_Config::getByField('LOCALCOUNTRYID', 'config_attribute');
        $localCountryId = isset($configRow['config_value']) ? $configRow['config_value'] : '';
        return self::getByCondition(array('country_id_neq' => $localCountryId), '*', 0, 0, $order);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        $validateArr[] = array("name" =>EC::Lang('warehouseCode'), "value" =>$val["warehouse_code"], "regex" => array("require","english",));
        $validateArr[] = array("name" =>EC::Lang('country'), "value" =>$val["country_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('status'), "value" =>$val["warehouse_status"], "regex" => array("positive"));
        if($row['warehouse_virtual'] == 1){
        	$validateArr[] = array("name" =>EC::Lang('warehouse_service'), "value" =>$val["warehouse_service"], "regex" => array("require"));
        }
        
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'warehouse_id',
              'E1'=>'warehouse_code',
              'E2'=>'warehouse_status',
              'E3'=>'country_id',
              'E4'=>'state',
              'E5'=>'city',
              'E6'=>'contacter',
              'E7'=>'phone_no',
              'E8'=>'street_address1',
              'E9'=>'street_address2',
              'E10'=>'warehouse_desc',
              'E11'=>'warehouse_add_time',
              'E12'=>'warehouse_update_time',
              'E13'=>'warehouse_virtual',
              'E14'=>'warehouse_type',
              'E15'=>'postcode',
              'E16'=>'company',
        	  'E17'=>'warehouse_service',
        	  'E18'=>'is_systematic',
        );
        return $row;
    }

    /**
     * 获取仓库信息
     * @param string $companyCode
     * @return array $result
     */
    public static function getWarehouse($companyCode){
        $con = array(
            'company_code' => $companyCode
        );
        $field = '*'; 
        $warehouses = Service_Warehouse::getByCondition($con,$field);
        
        $result = array();
        foreach($warehouses as $k => $v){            
            $result[$v['warehouse_id']] = $v;
        }
        
        return $result;
    }
}