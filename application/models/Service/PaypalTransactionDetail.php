<?php
class Service_PaypalTransactionDetail extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PaypalTransactionDetail|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PaypalTransactionDetail();
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
    public static function update($row, $value, $field = "transactionid")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "transactionid")
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
    public static function getByField($value, $field = 'transactionid', $colums = "*")
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
        
              'E0'=>'receiverbusiness',
              'E1'=>'receiveremail',
              'E2'=>'receiverid',
              'E3'=>'email',
              'E4'=>'payerid',
              'E5'=>'payerstatus',
              'E6'=>'countrycode',
              'E7'=>'shiptoname',
              'E8'=>'shiptostreet',
              'E9'=>'shiptocity',
              'E10'=>'shiptostate',
              'E11'=>'shiptocountrycode',
              'E12'=>'shiptocountryname',
              'E13'=>'shiptozip',
              'E14'=>'addressowner',
              'E15'=>'addressstatus',
              'E16'=>'custom',
              'E17'=>'salestax',
              'E18'=>'shipdiscount',
              'E19'=>'insuranceamount',
              'E20'=>'buyerid',
              'E21'=>'closingdate',
              'E22'=>'timestamp',
              'E23'=>'correlationid',
              'E24'=>'ack',
              'E25'=>'version',
              'E26'=>'build',
              'E27'=>'firstname',
              'E28'=>'lastname',
              'E29'=>'transactionid',
              'E30'=>'transactiontype',
              'E31'=>'paymenttype',
              'E32'=>'ordertime',
              'E33'=>'amt',
              'E34'=>'feeamt',
              'E35'=>'taxamt',
              'E36'=>'shippingamt',
              'E37'=>'handlingamt',
              'E38'=>'currencycode',
              'E39'=>'paymentstatus',
              'E40'=>'pendingreason',
              'E41'=>'reasoncode',
              'E42'=>'shippingmethod',
              'E43'=>'protectioneligibility',
        );
        return $row;
    }

}