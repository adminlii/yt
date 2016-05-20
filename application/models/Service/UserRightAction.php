<?php
class Service_UserRightAction extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_UserRightAction|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_UserRightAction();
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
    public static function update($row, $value, $field = "ura_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ura_id")
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
    public static function getByField($value, $field = 'ura_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('resourcName'), "value" =>$val["ura_title"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('resourcNameEn'), "value" =>$val["ura_title_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('status'), "value" =>$val["ura_status"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('display'), "value" =>$val["ura_display"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('module'), "value" =>$val["ura_module"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('controller'), "value" =>$val["ura_controller"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('action'), "value" =>$val["ura_action"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'ura_id',
              'E1'=>'ura_title',
              'E2'=>'ura_title_en',
              'E3'=>'ura_title_alias',
              'E4'=>'ura_status',
              'E5'=>'ura_display',
              'E6'=>'ura_module',
              'E7'=>'ura_controller',
              'E8'=>'ura_action',
        );
        return $row;
    }

    public static function getModule()
    {
        $model = self::getModelInstance();
        return $model->getModule();
    }

}