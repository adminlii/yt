<?php
class Service_AutomaticLetterRule extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AutomaticLetterRule|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AutomaticLetterRule();
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
    public static function update($row, $value, $field = "alr_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "alr_id")
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
    public static function getByField($value, $field = 'alr_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('规则名称'), "value" =>$val["rule_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('规则平台'), "value" =>$val["rule_platform"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('模板内容ID'), "value" =>$val["altc_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('模板ID'), "value" =>$val["alt_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('模板语言'), "value" =>$val["language_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('优先级'), "value" =>$val["rule_level"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('修改人'), "value" =>$val["user_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('状态'), "value" =>$val["status"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
              'E0'=>'alr_id',
              'E1'=>'rule_name',
              'E2'=>'rule_platform',
              'E3'=>'altc_id',
              'E4'=>'language_code',
              'E5'=>'rule_level',
              'E6'=>'user_id',
              'E7'=>'status',
              'E8'=>'create_time',
              'E9'=>'lastupdate',
        	  'E10'=>'alt_id',
        	  'E11'=>'sync_message',
        );
        return $row;
    }

}