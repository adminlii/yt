<?php
class Service_BuyerBlacklist extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_BuyerBlacklist|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_BuyerBlacklist();
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
    public static function update($row, $value, $field = "bb_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "bb_id")
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
    public static function getByField($value, $field = 'bb_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('平台'), "value" =>$val["bb_platform"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('类型'), "value" =>$val["bb_similitude_type"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('受限名称'), "value" =>$val["bb_similitude_val"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('状态'), "value" =>$val["bb_status"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'bb_id',
              'E1'=>'bb_platform',
              'E2'=>'bb_similitude_type',
              'E3'=>'bb_similitude_val',
              'E4'=>'bb_verify_str',
              'E5'=>'bb_processed_str',
              'E6'=>'bb_status',
              'E7'=>'create_id',
              'E8'=>'modify_id',
              'E9'=>'create_date',
              'E10'=>'last_update',
        );
        return $row;
    }

}