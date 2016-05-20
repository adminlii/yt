<?php
class Service_CtsIssue extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_CtsIssue|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_CtsIssue();
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
    public static function update($row, $value, $field = "issue_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "issue_id")
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
    public static function getByField($value, $field = 'issue_id', $colums = "*")
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
    public static function getByJoinCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByJoinCondition($condition, $type, $pageSize, $page, $order);
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
        
              'E0'=>'issue_id',
              'E1'=>'tms_id',
              'E2'=>'issue_class_code',
              'E3'=>'issue_kind_code',
              'E4'=>'issue_status',
              'E5'=>'customer_id',
              'E6'=>'shipper_channel_id',
              'E7'=>'bs_id',
              'E8'=>'shipper_hawbcode',
              'E9'=>'server_hawbcode',
              'E10'=>'product_code',
              'E11'=>'isu_lastprocessdate',
              'E12'=>'st_id_process',
              'E13'=>'isu_lastfeedbackdate',
              'E14'=>'st_id_unholdassigned',
              'E15'=>'isu_unholddate',
              'E16'=>'st_id_unhold',
              'E17'=>'isu_releasedate',
              'E18'=>'st_id_release',
              'E19'=>'isu_webreplysign',
              'E20'=>'isu_interactionsign',
              'E21'=>'isu_holdsign',
              'E22'=>'st_id_create',
              'E23'=>'isu_createdate',
              'E24'=>'isu_closedate',
              'E25'=>'st_id_close',
              'E26'=>'checkin_og_id',
              'E27'=>'checkin_date',
        );
        return $row;
    }

}