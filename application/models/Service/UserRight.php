<?php
class Service_UserRight extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_UserRight|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_UserRight();
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
    public static function update($row, $value, $field = "ur_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ur_id")
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
    public static function getByField($value, $field = 'ur_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('menu'), "value" =>$val["um_id"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('rightName'), "value" =>$val["ur_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('rightNameEn'), "value" =>$val["ur_name_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('url'), "value" =>$val["ur_url"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('type'), "value" =>$val["ur_type"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('sort'), "value" =>$val["ur_sort"], "regex" => array("positive","number[0,999]"));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'ur_id',
              'E1'=>'um_id',
              'E2'=>'ur_name',
              'E3'=>'ur_name_en',
              'E4'=>'ur_description',
              'E5'=>'ur_url',
              'E6'=>'ur_type',
              'E7'=>'ur_module',
              'E8'=>'ur_icon',
              'E9'=>'ur_sort',
              'E101'=>'ur_common',
        );
        return $row;
    }

}