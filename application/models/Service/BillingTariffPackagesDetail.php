<?php
class Service_BillingTariffPackagesDetail extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_BillingTariffPackagesDetail|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_BillingTariffPackagesDetail();
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
    public static function update($row, $value, $field = "btpd_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "btpd_id")
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
    public static function getByField($value, $field = 'btpd_id', $colums = "*")
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
        
              'E0'=>'btpd_id',
              'E1'=>'tp_id',
              'E2'=>'btp_id',
              'E3'=>'btpd_code',
              'E4'=>'btpd_status',
              'E5'=>'btpd_charge_time_start',
              'E6'=>'btpd_charge_time_end',
              'E7'=>'btpd_installation_fee',
              'E8'=>'btpd_maintenance_costs',
              'E9'=>'btpd_orders_max',
              'E10'=>'btpd_limit_orders',
              'E11'=>'btpd_limit_orders_val',
              'E12'=>'btpd_orders',
              'E13'=>'btpd_single_ticket_fees',
              'E14'=>'btpd_orders_exceeded',
              'E15'=>'btpd_exceed_orders_expenses',
              'E16'=>'btpd_total_amount',
              'E17'=>'btpd_actually_paid_amount',
              'E18'=>'tp_currency_code',
              'E19'=>'btpd_add_date',
              'E20'=>'btpd_toaccount_date',
              'E21'=>'btpd_verify_date',
        );
        return $row;
    }

}