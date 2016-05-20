<?php
class Service_ApiService extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ApiService|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ApiService();
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
    public static function update($row, $value, $field = "as_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "as_id")
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
    public static function getByField($value, $field = 'as_id', $colums = "*")
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
        
              'E0'=>'as_id',
              'E1'=>'as_code',
              'E2'=>'as_name',
              'E3'=>'as_type',
              'E4'=>'as_is_authorize',
              'E5'=>'as_status',
              'E6'=>'as_user',
              'E7'=>'as_pwd',
              'E8'=>'cig_user',
              'E9'=>'cig_pwd',
              'E10'=>'as_token',
              'E11'=>'as_address',
              'E12'=>'as_address1',
              'E13'=>'as_address2',
              'E14'=>'as_application',
              'E15'=>'as_environment',
              'E16'=>'as_account',
              'E17'=>'as_ekp',
              'E18'=>'as_partner',
              'E19'=>'as_ignore_exception',
              'E20'=>'as_logo_image',
              'E21'=>'as_add_date',
              'E22'=>'as_update_date',
              'E23'=>'as_creater',
              'E24'=>'as_updater',
              'E25'=>'as_path',
              'E26'=>'as_order_confirmship_status',
              'E27'=>'as_order_waiting_status',
              'E28'=>'as_width',
              'E29'=>'as_height',
              'E30'=>'as_custom_tracking_number',
              'E31'=>'as_func',
              'E32'=>'as_print_mode',
        );
        return $row;
    }

}