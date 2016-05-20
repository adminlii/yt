<?php
class Service_Supplier extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Supplier|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Supplier();
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
    public static function update($row, $value, $field = "supplier_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "supplier_id")
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
    public static function getByField($value, $field = 'supplier_id', $colums = "*")
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
        
              'E0'=>'supplier_id',
              'E1'=>'supplier_code',
              'E2'=>'supplier_name',
              'E3'=>'level',
              'E4'=>'supplier_type',
              'E5'=>'account_type',
              'E6'=>'pay_type',
              'E7'=>'pay_card',
              'E8'=>'pay_name',
              'E9'=>'pay_bank',
              'E10'=>'pc_id',
              'E11'=>'supplier_teamwork_type',
              'E12'=>'supplier_main_category_id',
              'E13'=>'supplier_status',
              'E14'=>'buyer_id',
              'E15'=>'account_cycleTime',
              'E16'=>'account_proportion',
              'E17'=>'supplier_qc_exception',
              'E18'=>'supplier_carrier',
              'E19'=>'supplier_ship_pay_type',
              'E20'=>'pay_platform',
        );
        return $row;
    }

}