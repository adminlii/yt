<?php
class Service_EbayMessageAll extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayMessageAll|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayMessageAll();
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
              'E2'=>'ebay_ext_message_id',
              'E3'=>'sender_id',
              'E4'=>'receiving_id',
              'E5'=>'message_title',
              'E6'=>'send_time',
              'E7'=>'company_code',
              'E8'=>'message_type',
              'E9'=>'message_type_id',
              'E10'=>'receive_type',
              'E11'=>'status',
              'E12'=>'level',
              'E13'=>'customer_service_id',
              'E14'=>'send_mail',
              'E15'=>'item_id',
              'E16'=>'message_url',
              'E17'=>'refrence_id',
              'E18'=>'create_time',
              'E19'=>'response_time',
              'E20'=>'customer_service_response',
              'E21'=>'response_sync',
              'E22'=>'response_sync_time',
              'E23'=>'item_title',
              'E24'=>'question_type',
              'E25'=>'user_account',
              'E26'=>'response_status',
              'E27'=>'process_status',
              'E28'=>'currt_content',
              'E29'=>'response_content',
        );
        return $row;
    }

}