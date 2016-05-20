<?php
class Service_EbayMessage extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayMessage|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayMessage();
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
    public static function update($row, $value, $field = "message_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "message_id")
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
    public static function getByField($value, $field = 'message_id', $colums = "*")
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
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionInnerJoinContent($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionInnerJoinContent($condition, $type, $pageSize, $page, $order);
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
        
              'E0'=>'message_id',
              'E1'=>'ebay_message_id',
              'E2'=>'sender_id',
              'E3'=>'receiving_id',
              'E4'=>'message_title',
              'E5'=>'send_time',
              'E6'=>'company_code',
              'E7'=>'message_type',
              'E8'=>'receive_type',
              'E9'=>'status',
              'E10'=>'level',
              'E11'=>'customer_service_id',
              'E12'=>'send_mail',
              'E13'=>'item_id',
              'E14'=>'message_url',
              'E15'=>'refrence_id',
              'E16'=>'create_time',
              'E17'=>'response_time',
              'E18'=>'customer_service_response',
              'E19'=>'response_sync',
              'E20'=>'response_sync_time',
              'E21'=>'item_title',
        );
        return $row;
    }

}