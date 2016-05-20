<?php
class Service_TariffPackages extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_TariffPackages|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_TariffPackages();
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
    public static function update($row, $value, $field = "tp_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "tp_id")
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
    public static function getByField($value, $field = 'tp_id', $colums = "*")
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
        
              'E0'=>'tp_id',
              'E1'=>'tp_level',
              'E2'=>'tp_code',
              'E3'=>'tp_name',
              'E4'=>'tp_desc',
              'E5'=>'tp_installation_fee',
              'E6'=>'tp_orders_start',
              'E7'=>'tp_orders_end',
              'E8'=>'tp_limit_orders',
              'E9'=>'tp_maintenance_costs',
              'E10'=>'tp_single_ticket_fees',
              'E11'=>'tp_currency_code',
              'E12'=>'tp_server',
              'E13'=>'tp_implement',
              'E14'=>'tp_aftermarket',
              'E15'=>'tp_two_domain_names',
              'E16'=>'tp_login_logo',
              'E17'=>'tp_upgrade',
              'E18'=>'tp_data_retention',
              'E19'=>'tp_customize',
              'E20'=>'tp_add_date',
        );
        return $row;
    }

}