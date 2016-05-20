<?php
class Service_ZendeskTickets extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ZendeskTickets|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ZendeskTickets();
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
              'E2'=>'external_id',
              'E3'=>'via_channel',
              'E4'=>'via_source_from_address',
              'E5'=>'via_source_from_name',
              'E6'=>'via_source_to_address',
              'E7'=>'via_source_to_name',
              'E8'=>'created_at',
              'E9'=>'updated_at',
              'E10'=>'type',
              'E11'=>'subject',
              'E12'=>'description',
              'E13'=>'priority',
              'E14'=>'status',
              'E15'=>'recipient',
              'E16'=>'requester_id',
              'E17'=>'submitter_id',
              'E18'=>'assignee_id',
              'E19'=>'organization_id',
              'E20'=>'group_id',
              'E21'=>'forum_topic_id',
              'E22'=>'problem_id',
              'E23'=>'has_incidents',
              'E24'=>'due_at',
              'E25'=>'result_type',
        );
        return $row;
    }

}