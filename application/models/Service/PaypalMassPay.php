<?php
class Service_PaypalMassPay extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PaypalMassPay|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PaypalMassPay();
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
    public static function update($row, $value, $field = "pmp_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pmp_id")
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
    public static function getByField($value, $field = 'pmp_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('收款人类型'), "value" =>$val["receiver_type"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('收款人'), "value" =>$val["receiver_val"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('支付金额'), "value" =>$val["amt"], "regex" => array("require","positive3",));
        $validateArr[] = array("name" =>EC::Lang('币种'), "value" =>$val["currency_code"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'pmp_id',
              'E1'=>'receiver_type',
              'E2'=>'receiver_val',
              'E3'=>'amt',
              'E4'=>'currency_code',
              'E5'=>'receiver_note',
              'E6'=>'refrence_no',
              'E7'=>'sys_note',
              'E8'=>'create_id',
              'E9'=>'audit_id',
              'E10'=>'status',
              'E11'=>'create_date',
              'E12'=>'submit_date',
              'E13'=>'audit_date',
              'E14'=>'last_update',
              'E15'=>'sync_date',
              'E16'=>'paypal_tid',
        	  'E17'=>'pay_account',
        );
        return $row;
    }

}