<?php
class Service_CsdOrder extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_CsdOrder|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_CsdOrder();
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
    public static function update($row, $value, $field = "order_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "order_id")
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
    public static function getByField($value, $field = 'order_id', $colums = "*")
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
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "",$group = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order,$group);
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionJoinInvoice($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "",$group = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionJoinInvoice($condition, $type, $pageSize, $page, $order,$group);
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
        
              'E0'=>'order_id',
              'E1'=>'order_create_code',
              'E2'=>'customer_id',
              'E3'=>'customer_channelid',
              'E4'=>'product_code',
              'E5'=>'shipper_hawbcode',
              'E6'=>'server_hawbcode',
              'E7'=>'channel_hawbcode',
              'E8'=>'country_code',
              'E9'=>'order_pieces',
              'E10'=>'order_status',
              'E11'=>'mail_cargo_type',
              'E12'=>'document_change_sign',
              'E13'=>'oda_checksign',
              'E14'=>'oda_sign',
              'E15'=>'return_sign',
              'E16'=>'hold_sign',
              'E17'=>'buyer_id',
              'E18'=>'platform_id',
              'E19'=>'bs_id',
              'E20'=>'creater_id',
              'E21'=>'create_date',
              'E22'=>'modify_date',
              'E23'=>'print_date',
              'E24'=>'post_date',
              'E25'=>'checkin_date',
              'E26'=>'checkout_date',
              'E27'=>'tms_id',
        );
        return $row;
    }

}