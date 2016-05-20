<?php
class Service_EmailAccount extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EmailAccount|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EmailAccount();
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
    public static function update($row, $value, $field = "ea_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ea_id")
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
    public static function getByField($value, $field = 'ea_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('用户店铺'), "value" =>$val["user_account"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('邮箱用户名'), "value" =>$val["ea_user_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('邮箱密码'), "value" =>$val["ea_password"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('发件人名称'), "value" =>$val["send_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('邮箱地址'), "value" =>$val["email_address"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('服务类型'), "value" =>$val["server_type"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('邮箱服务器地址(收)'), "value" =>$val["imap_server"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('端口号（收）'), "value" =>$val["imap_port"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('邮箱服务器地址(发)'), "value" =>$val["smtp_server"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('端口号（发）'), "value" =>$val["smtp_port"], "regex" => array("require",));
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
        
              'E0'=>'ea_id',
              'E1'=>'user_account',
              'E2'=>'ea_user_name',
              'E3'=>'ea_password',
              'E4'=>'send_name',
              'E5'=>'email_address',
              'E6'=>'server_type',
              'E7'=>'imap_server',
              'E8'=>'imap_port',
              'E9'=>'smtp_server',
              'E10'=>'smtp_port',
              'E11'=>'status',
              'E12'=>'user_id',
              'E13'=>'create_date',
              'E14'=>'last_update',
        );
        return $row;
    }

}