<?php
class Service_Orders extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Orders|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Orders();
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
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "",$group='')
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order,$group);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionJoinCsdOrder($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "",$group='')
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionJoinCsdOrder($condition, $type, $pageSize, $page, $order,$group);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionPaypal($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionPaypal($condition, $type, $pageSize, $page, $order);
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
              'E1'=>'platform',
              'E2'=>'order_status',
              'E3'=>'create_method',
              'E4'=>'customer_id',
              'E5'=>'company_code',
              'E6'=>'shipping_method',
              'E7'=>'warehouse_id',
              'E8'=>'order_desc',
              'E9'=>'date_create',
              'E10'=>'date_release',
              'E11'=>'date_warehouse_shipping',
              'E12'=>'date_last_modify',
              'E13'=>'operator_id',
              'E14'=>'refrence_no',
              'E15'=>'refrence_no_platform',
              'E16'=>'shipping_address_id',
              'E17'=>'currency',
              'E18'=>'refrence_no_warehouse',
              'E19'=>'shipping_method_no',
              'E20'=>'sync_status',
              'E21'=>'sync_time',
              'E22'=>'order_type',
              'E23'=>'create_type',


        );
        return $row;
    }

}