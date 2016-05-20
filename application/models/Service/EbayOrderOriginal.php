<?php
class Service_EbayOrderOriginal extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayOrderOriginal|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayOrderOriginal();
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
    public static function update($row, $value, $field = "eoo_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "eoo_id")
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
    public static function getByField($value, $field = 'eoo_id', $colums = "*")
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
        
              'E0'=>'eoo_id',
              'E1'=>'OrderID',
              'E2'=>'OrderStatus',
              'E3'=>'adjustmentamount',
              'E4'=>'amountpaid',
              'E5'=>'amountsaved',
              'E6'=>'eBayPaymentStatus',
              'E7'=>'LastModifiedTime',
              'E8'=>'PaymentMethod',
              'E9'=>'CheckoutStatus',
              'E10'=>'ShippingService',
              'E11'=>'shippingservicecost',
              'E12'=>'sellingmanagersalesrecordnumber',
              'E13'=>'CreatedTime',
              'E14'=>'SellerEmail',
              'E15'=>'subtotal',
              'E16'=>'total',
              'E17'=>'externaltransactionid',
              'E18'=>'feeorcreditamount',
              'E19'=>'externaltransactiontime',
              'E20'=>'paymentorrefundamount',
              'E21'=>'buyeruserid',
              'E22'=>'paidtime',
              'E23'=>'shippedtime',
              'E24'=>'company_code',
              'E25'=>'create_date_sys',
              'E26'=>'modify_date_sys',
              'E27'=>'currency',
              'E28'=>'user_account',
              'E29'=>'buyer_note',
        );
        return $row;
    }

}