<?php
class Service_ZendeskUser extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ZendeskUser|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ZendeskUser();
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
              'E1'=>'url',
              'E2'=>'name',
              'E3'=>'email',
              'E4'=>'created_at',
              'E5'=>'updated_at',
              'E6'=>'time_zone',
              'E7'=>'phone',
              'E8'=>'photo',
              'E9'=>'locale_id',
              'E10'=>'locale',
              'E11'=>'organization_id',
              'E12'=>'role',
              'E13'=>'verified',
              'E14'=>'external_id',
              'E15'=>'tags',
              'E16'=>'alias',
              'E17'=>'active',
              'E18'=>'shared',
              'E19'=>'shared_agent',
              'E20'=>'last_login_at',
              'E21'=>'signature',
              'E22'=>'details',
              'E23'=>'notes',
              'E24'=>'custom_role_id',
              'E25'=>'moderator',
              'E26'=>'ticket_restriction',
              'E27'=>'only_private_comments',
              'E28'=>'restricted_agent',
              'E29'=>'suspended',
              'E30'=>'user_fields',
        );
        return $row;
    }

}