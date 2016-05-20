<?php
class Service_OrderProductSplit extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_OrderProductSplit|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_OrderProductSplit();
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
    public static function update($row, $value, $field = "op_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "op_id")
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
    public static function getByField($value, $field = 'op_id', $colums = "*")
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
        
              'E0'=>'op_id',
              'E1'=>'order_id',
              'E2'=>'product_id',
              'E3'=>'product_sku',
              'E4'=>'warehouse_sku',
              'E5'=>'product_title',
              'E6'=>'op_quantity',
              'E7'=>'op_ref_tnx',
              'E8'=>'op_recv_account',
              'E9'=>'op_ref_item_id',
              'E10'=>'op_site',
              'E11'=>'op_record_id',
              'E12'=>'op_ref_buyer_id',
              'E13'=>'op_ref_paydate',
              'E14'=>'op_add_time',
              'E15'=>'op_update_time',
              'E16'=>'OrderID',
              'E17'=>'OrderIDEbay',
              'E18'=>'is_modify',
              'E19'=>'pic',
              'E20'=>'url',
              'E21'=>'transaction_price',
              'E22'=>'unit_price',
              'E23'=>'unit_finalvaluefee',
              'E24'=>'unit_platformfee',
              'E25'=>'unit_shipfee',
              'E26'=>'currency_code',
              'E27'=>'sync_status',
              'E28'=>'give_up',
              'E29'=>'create_type',
        );
        return $row;
    }

}