<?php
class Service_AmazonReportRequestInfo extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AmazonReportRequestInfo|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AmazonReportRequestInfo();
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
    public static function update($row, $value, $field = "id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "id")
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
    public static function getByField($value, $field = 'id', $colums = "*")
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
        
              'E0'=>'id',
              'E1'=>'company_code',
              'E2'=>'user_account',
              'E3'=>'ReportRequestId',
              'E4'=>'ReportType',
              'E5'=>'StartDate',
              'E6'=>'EndDate',
              'E7'=>'Scheduled',
              'E8'=>'SubmittedDate',
              'E9'=>'ReportProcessingStatus',
              'E10'=>'GeneratedReportId',
              'E11'=>'StartedProcessingDate',
              'E12'=>'CompletedDate',
        );
        return $row;
    }

}