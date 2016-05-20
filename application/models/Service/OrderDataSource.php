<?php
class Service_OrderDataSource extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_OrderDataSource|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_OrderDataSource();
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
    public static function update($row, $value, $field = "ods_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ods_id")
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
    public static function getByField($value, $field = 'ods_id', $colums = "*")
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
        $validateArr[] = array("name" =>'Code', "value" =>$val["ods_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('名称'), "value" =>$val["ods_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('名称(英文)'), "value" =>$val["ods_name_en"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('备注'), "value" =>$val["ods_note"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('排序'), "value" =>$val["ods_seq"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('状态'), "value" =>$val["ods_status"], "regex" => array("require","integer",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'ods_id',
              'E1'=>'ods_name',
              'E2'=>'ods_name_en',
              'E3'=>'ods_note',
              'E4'=>'ods_seq',
              'E5'=>'ods_status',
        	  'E6'=>'ods_code',
        	  'E7'=>'is_system'
        );
        return $row;
    }

}