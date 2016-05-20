<?php
class Service_PurchasePayment extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PurchasePayment|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PurchasePayment();
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
    public static function update($row, $value, $field = "pp_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pp_id")
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
    public static function getByField($value, $field = 'pp_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }
    
    public static function getFeeByOrder($value, $field = 'po_code', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getFeeByOrder($value, $field, $colums);
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
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionJoinFee($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionJoinFee($condition, $type, $pageSize, $page, $order);
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
        
              'E0'=>'pp_id',
              'E1'=>'pp_no',
              'E2'=>'po_code',
              'E3'=>'pp_source_type',
              'E4'=>'pp_effect_status',
              'E5'=>'po_id',
              'E6'=>'pp_applicant',
              'E7'=>'pp_payer',
              'E8'=>'pp_verifier',
              'E9'=>'pp_status',
              'E10'=>'pp_operation_mode',
              'E11'=>'pp_amount',
              'E12'=>'currency_code',
              'E13'=>'currency_rate',
              'E14'=>'po_remark',
              'E15'=>'pp_add_time',
              'E16'=>'pp_application_time',
              'E17'=>'pp_effective_time',
              'E18'=>'pp_verify_time',
              'E19'=>'pp_pay_time',
              'E20'=>'pp_update_time',
        );
        return $row;
    }

}