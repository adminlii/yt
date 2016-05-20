<?php
class Service_Country extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Country|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Country();
        }
        return self::$_modelClass;
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }
    
    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "country_id")
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
    public static function getByField($value, $field = 'country_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('countryName'), "value" =>$val["country_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('countryNameEn'), "value" =>$val["country_name_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('country_code'), "value" =>$val["country_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('sort'), "value" =>$val["country_sort"], "regex" => array("positive"));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'country_id',
              'E1'=>'country_name',
              'E2'=>'country_name_en',
              'E3'=>'country_local_name',
              'E4'=>'country_alias',
              'E5'=>'country_code',
              'E6'=>'country_sort',
              'E7'=>'country_short_name',
        );
        return $row;
    }

}