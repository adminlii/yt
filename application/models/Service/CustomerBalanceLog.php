<?php
class Service_CustomerBalanceLog extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_CustomerBalanceLog|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_CustomerBalanceLog();
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
    public static function update($row, $value, $field = "cbl_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "cbl_id")
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
    public static function getByField($value, $field = 'cbl_id', $colums = "*")
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
        
              'E0'=>'cbl_id',
              'E1'=>'customer_code',
              'E2'=>'customer_id',
              'E3'=>'cbl_type',
              'E4'=>'cbl_transaction_value',
              'E5'=>'cbl_value',
              'E6'=>'currency_rate',
              'E7'=>'currency_code',
              'E8'=>'cbl_note',
              'E9'=>'user_id',
              'E10'=>'fee_id',
              'E11'=>'cbl_current_value',
              'E12'=>'cbl_current_hold_value',
              'E13'=>'application_code',
              'E14'=>'cbl_refer_code',
              'E15'=>'cbl_add_time',
              'E16'=>'transaction_number',
              'E17'=>'arrive_time',
              'E18'=>'ft_code',
        );
        return $row;
    }

}