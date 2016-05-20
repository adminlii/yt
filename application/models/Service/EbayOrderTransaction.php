<?php
class Service_EbayOrderTransaction extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayOrderTransaction|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayOrderTransaction();
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
    public static function update($row, $value, $field = "EbayTransaction_Id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "EbayTransaction_Id")
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
    public static function getByField($value, $field = 'EbayTransaction_Id', $colums = "*")
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
        
              'E0'=>'OrderId',
              'E1'=>'EbayTransaction_Id',
              'E2'=>'Buyer_Mail',
              'E3'=>'SellingManagerSalesRecordNumber',
              'E4'=>'shippingcarrierused',
              'E5'=>'shipmenttrackingnumber',
              'E6'=>'CreatedDate',
              'E7'=>'ItemID',
              'E8'=>'Site',
              'E9'=>'Title',
              'E10'=>'ConditionID',
              'E11'=>'ConditionDisplayName',
              'E12'=>'QuantityPurchased',
              'E13'=>'PaymentHoldStatus',
              'E14'=>'TransactionID',
              'E15'=>'TransactionPrice',
              'E16'=>'finalvaluefee',
              'E17'=>'TransactionSiteID',
              'E18'=>'Platform',
              'E19'=>'actualshippingcost',
              'E20'=>'actualhandlingcost',
              'E21'=>'OrderLineItemID',
              'E22'=>'company_code',
              'E23'=>'create_date_sys',
              'E24'=>'modify_date_sys',
              'E25'=>'sku',
              'E26'=>'user_account',
        );
        return $row;
    }

}