<?php
class Service_Receiving extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Receiving|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Receiving();
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
    public static function update($row, $value, $field = "receiving_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "receiving_id")
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
    public static function getByField($value, $field = 'receiving_id', $colums = "*")
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

    public static function getSearchByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getSearchByCondition($condition, $type, $pageSize, $page, $order);
    }
    
    public static function getByOrderNotReceiving($type = '*', $pageSize = 0, $page = 1){
    	$model = self::getModelInstance();
    	return $model->getByOrderNotReceiving($type, $pageSize, $page);
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
              'E0'=>'receiving_id',
              'E1'=>'receiving_code',
              'E2'=>'reference_no',
              'E3'=>'warehouse_id',
              'E4'=>'supplier_id',
              'E5'=>'receiving_update_user',
              'E6'=>'receiving_add_user',
              'E7'=>'customer_id',
              'E8'=>'customer_code',
              'E9'=>'receiving_type',
              'E10'=>'receiving_status',
              'E11'=>'contacter',
              'E12'=>'contact_phone',
              'E13'=>'receiving_description',
              'E14'=>'receiving_add_time',
              'E15'=>'receiving_update_time',
        	  'E29'=>'eda_date',
        	  'E16'=>'to_warehouse_id',
        	  'E17'=>'receiving_transfer_status',
        	  'E18'=>'po_code',
        	  'E19'=>'tracking_number',
        	  'E30'=>'expected_date',
        );
        return $row;
    }

}