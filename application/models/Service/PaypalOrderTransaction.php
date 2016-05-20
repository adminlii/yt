<?php
class Service_PaypalOrderTransaction extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PaypalOrderTransaction|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PaypalOrderTransaction();
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
    public static function update($row, $value, $field = "pot_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pot_id")
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
    public static function getByField($value, $field = 'pot_id', $colums = "*")
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
        
              'E0'=>'pot_id',
              'E1'=>'pt_id',
              'E2'=>'pot_paypal_id',
              'E3'=>'pot_country_code',
              'E4'=>'pot_ship_name',
              'E5'=>'pot_ship_street1',
              'E6'=>'pot_ship_street2',
              'E7'=>'pot_ship_city',
              'E8'=>'pot_ship_state',
              'E9'=>'pot_ship_county_code',
              'E10'=>'pot_ship_county_name',
              'E11'=>'pot_ship_zip',
              'E12'=>'pot_buyer_id',
              'E13'=>'pot_first_name',
              'E14'=>'pot_last_name',
              'E15'=>'pot_order_time',
              'E16'=>'pot_amt',
              'E17'=>'pot_currency_code',
        	  'E18'=>'pot_note',
        	  'E19'=>'pot_status',
        );
        return $row;
    }

}