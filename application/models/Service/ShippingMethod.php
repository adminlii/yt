<?php
class Service_ShippingMethod extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ShippingMethod|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ShippingMethod();
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
    public static function update($row, $value, $field = "sm_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "sm_id")
    {
        $model = self::getModelInstance();
        return $model->delete($value, $field);
    }
    
    public static function getHeadShippingMethod($warehouse = ""){
    	$model = self::getModelInstance();
    	return $model->getHeadShippingMethod($warehouse);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'sm_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }
    
    public static function getbyChangeProduct($value = ""){
    	$model = self::getModelInstance();
    	return $model->getbyChangeProduct($value);
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
     * @author solar
     * @return array
     */
    public static function getCodeNameKV() {
    	$aKeyVal = array();
    	$list = self::getAll();
    	foreach($list as $row)
    		$aKeyVal[$row['sm_code']] = $row['sm_name'];
    	return $aKeyVal;
    }
    
    /**
     * @author solar
     * @return array
     */
    public static function getCodeClassKV() {
    	$aKeyVal = array();
    	$list = self::getAll();
    	foreach($list as $row)
    		$aKeyVal[$row['sm_code']] = $row['sm_class_code'];
    	return $aKeyVal;
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
    
    public static function getByConditionPageLike($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionPageLike($condition, $type, $pageSize, $page, $order);
    }
    
    public static function getByConditionGetLike($searchMethod = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionGetLike($searchMethod);
    }


    public static function getByInnerJoinCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByInnerJoinCondition($condition, $type, $pageSize, $page, $order);
    }

    public static function getByLeftJoinCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByLeftJoinCondition($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $errorArr = array();
        $validateArr[] = array("name" =>EC::Lang('smCode'), "value" =>$val["sm_code"], "regex" => array("require","length[1,20]",));

       // $validateArr[] = array("name" =>EC::Lang('freightBestTime'), "value" =>$val["sm_delivery_time_min"], "regex" => array("require",));
       // $validateArr[] = array("name" =>EC::Lang('FreightLowestAging'), "value" =>$val["sm_delivery_time_max"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('status'), "value" =>$val["sm_status"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('smClass'), "value" =>$val["sm_class_code"], "regex" => array("require",));
        $errorArr= Common_Validator::formValidator($validateArr);

        $smId = isset($val['sm_id']) ? $val['sm_id'] : '';
        $rows = self::getByCondition(array('sm_id_ne' => $smId, 'sm_code' => $val["sm_code"]), array('sm_code'), 1);
        if (!empty($rows)) {
            $errorArr[] = '运输方式已存在';
        }
        return $errorArr;
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'sm_id',
              'E1'=>'sm_code',
              'E2'=>'sm_name_cn',
              'E3'=>'sm_name',
              'E4'=>'sm_mp_fee',
              'E5'=>'sm_reg_fee',
              'E6'=>'sm_addons',
              'E7'=>'sm_discount',
              'E8'=>'sm_delivery_time_min',
              'E9'=>'sm_delivery_time_max',
              'E10'=>'sm_delivery_time_avg',
              'E11'=>'sm_is_volume',
              'E12'=>'sm_vol_rate',
              'E13'=>'sm_status',
              'E14'=>'sm_class_code',
              'E15'=>'sm_logo',
              'E16'=>'sm_return_address',
              'E17'=>'sm_discount_min',
              'E18'=>'sm_mp_fee_min',
              'E19'=>'sm_reg_fee_min',
              'E20'=>'sm_limit_volume',
              'E21'=>'sm_limit_weight',
              'E22'=>'sm_sort',
              'E23'=>'sm_is_tracking',
              'E24'=>'sm_is_validate_remote',
              'E25'=>'warehouse_id',
              'E26'=>'sm_fee_type',
              'E27'=>'sm_calc_type',
              'E28'=>'sm_baf',
              'E29'=>'sm_return_recipient',
              'E30'=>'sm_short_name',
              'E31'=>'st_id',
              'E32'=>'sm_carrier_number',
              'E33'=>'sm_type',
        	  'E34'=>'is_systematic'
        );
        return $row;
    }

    /**
     * @desc 获取运输代码
     * @return array
     */
    public static function getShippingMethodSimple()
    {
        $smRows = Common_DataCache::getShippingMethodSimple();
        $smArr = array();
        foreach ($smRows as $val) {
            if ($val['sm_status'] == '1') {
                $smArr[] = $val['sm_code'];
            }
        }
        return $smArr;
    }
}