<?php
class Service_TakTrackingbusiness extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_TakTrackingbusiness|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_TakTrackingbusiness();
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
    public static function update($row, $value, $field = "tbs_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "tbs_id")
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
    public static function getByField($value, $field = 'tbs_id', $colums = "*")
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
        
              'E0'=>'tbs_id',
              'E1'=>'customer_id',
              'E2'=>'track_server_code',
              'E3'=>'shipper_hawbcode',
              'E4'=>'server_hawbcode',
              'E5'=>'country_code',
              'E6'=>'new_operation_status',
              'E7'=>'new_error_code',
              'E8'=>'new_operation_date',
              'E9'=>'new_track_code',
              'E10'=>'new_track_date',
              'E11'=>'new_track_location',
              'E12'=>'new_track_comment',
              'E13'=>'close_code',
              'E14'=>'hash_code',
              'E15'=>'close_date',
              'E16'=>'signatory_name',
              'E17'=>'start_track_date',
              'E18'=>'end_track_date',
              'E19'=>'reference_date',
              'E20'=>'create_date',
              'E21'=>'pass_back_date',
              'E22'=>'shipper_hawbcode_tracksign',
              'E23'=>'web_order_id',
              'E24'=>'sys_bs_id',
              'E25'=>'show_sign',
              'E26'=>'tms_id',
        );
        return $row;
    }

}