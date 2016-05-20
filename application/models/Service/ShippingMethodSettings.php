<?php
class Service_ShippingMethodSettings extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ShippingMethodSettings|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ShippingMethodSettings();
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
    public static function update($row, $value, $field = "sms_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "sms_id")
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
    public static function getByField($value, $field = 'sms_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('费用类型'), "value" =>$val["smt_fee_type"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('仓库'), "value" =>$val["warehouse_id"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('运输方式'), "value" =>$val["sm_id"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('计费类型'), "value" =>$val["smt_type"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('使用标志'), "value" =>$val["sms_customer_type"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('处理费'), "value" =>$val["sms_mp_fee"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('挂号费'), "value" =>$val["sms_reg_fee"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('附加费'), "value" =>$val["sms_addons"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('燃油附加费%'), "value" =>$val["sms_baf"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('折扣%'), "value" =>$val["sms_discount"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('是否量体积'), "value" =>$val["sms_is_volume"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('重量限制'), "value" =>$val["sms_limit_weight"], "regex" => array("require",));
        $error=Common_Validator::formValidator($validateArr);
        $condition = array(
            'sm_id' => $val["sm_id"],
            'warehouse_id' => $val["warehouse_id"],
            'smt_fee_type' => $val["smt_fee_type"],
            'smt_type' => $val["smt_type"],
            'sms_supported_type' => $val["sms_supported_type"],
            'neq_sms_id' => $val["sms_id"],
        );
        if (self::getByCondition($condition, 'count(*)')) {
            $error[] = '此规则已存在';
        }
        return $error;
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'sms_id',
              'E1'=>'sm_id',
              'E2'=>'smt_fee_type',
              'E3'=>'smt_type',
              'E4'=>'sms_customer_type',
              'E5'=>'sms_mp_fee',
              'E6'=>'sms_reg_fee',
              'E7'=>'sms_addons',
              'E8'=>'sms_baf',
              'E9'=>'sms_discount',
              'E10'=>'sms_delivery_time_min',
              'E11'=>'sms_delivery_time_max',
              'E12'=>'sms_delivery_time_avg',
              'E13'=>'sms_is_volume',
              'E14'=>'sms_vol_rate',
              'E15'=>'sms_mp_fee_min',
              'E16'=>'sms_reg_fee_min',
              'E17'=>'sms_limit_volume',
              'E18'=>'sms_limit_weight',
              'E19'=>'sms_update_time',
              'E20'=>'sms_status',
              'E21'=>'warehouse_id',
              'E22'=>'sms_supported_type',
              'E23'=>'sms_limit_height',
              'E24'=>'sms_limit_width',
              'E25'=>'sms_limit_length',
        	  'E26'=>'company_code',
        	  'E27'=>'is_systematic'
        );
        return $row;
    }

}