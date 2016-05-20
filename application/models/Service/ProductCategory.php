<?php
class Service_ProductCategory extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductCategory|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductCategory();
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
    public static function update($row, $value, $field = "pc_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }
    
    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pc_id")
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
    public static function getByField($value, $field = 'pc_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('titleEn'), "value" =>$val["pc_name_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('title'), "value" =>$val["pc_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('shortName'), "value" =>$val["pc_shortname"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('hsCode'), "value" =>$val["pc_hs_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('sort'), "value" =>$val["pc_sort_id"], "regex" => array("positive"));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'pc_id',
              'E1'=>'warehouse_id',
              'E2'=>'pc_name_en',
              'E3'=>'pc_name',
              'E4'=>'pc_shortname',
              'E5'=>'pc_hs_code',
              'E6'=>'pc_sort_id',
              'E7'=>'pc_update_time',
        );
        return $row;
    }

}