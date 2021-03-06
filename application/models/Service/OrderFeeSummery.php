<?php
class Service_OrderFeeSummery extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_OrderFeeSummery|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_OrderFeeSummery();
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
    public static function update($row, $value, $field = "of_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "of_id")
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
    public static function getByField($value, $field = 'of_id', $colums = "*")
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
        
              'E0'=>'of_id',
              'E1'=>'customer_code',
              'E2'=>'ref_id',
              'E3'=>'shipping_method',
              'E4'=>'country_code',
              'E5'=>'order_weight',
              'E6'=>'ship_cost',
              'E7'=>'op_cost',
              'E8'=>'fuel_cost',
              'E9'=>'register_cost',
              'E10'=>'warehouse_cost',
              'E11'=>'tariff_cost',
              'E12'=>'incidental_cost',
              'E13'=>'bi_billing_date',
        );
        return $row;
    }

}