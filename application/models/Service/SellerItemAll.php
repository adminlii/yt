<?php
class Service_SellerItemAll extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_SellerItemAll|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_SellerItemAll();
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
    public static function update($row, $value, $field = "sia_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "sia_id")
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
    public static function getByField($value, $field = 'sia_id', $colums = "*")
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
        
              'E0'=>'sia_id',
              'E1'=>'item_id',
              'E2'=>'item_status',
              'E3'=>'no_stock_online',
              'E4'=>'start_time',
              'E5'=>'end_time',
              'E6'=>'platform',
              'E7'=>'company_code',
              'E8'=>'sku',
              'E9'=>'currency',
              'E10'=>'price_sell',
              'E11'=>'price_purchase',
              'E12'=>'item_title',
              'E13'=>'item_url',
              'E14'=>'category_id',
              'E15'=>'category_name',
              'E16'=>'pic_path',
              'E17'=>'site',
              'E18'=>'user_account',
              'E19'=>'warehouse_sku',
              'E20'=>'is_binding_auction',
              'E21'=>'sell_qty',
              'E22'=>'sold_qty',
              'E23'=>'sell_type',
              'E24'=>'item_location',
              'E25'=>'auto_supply',
              'E26'=>'need_supply',
              'E27'=>'paypal_email_address',
              'E28'=>'list_type',
              'E29'=>'fee_insertion',
              'E30'=>'fee_insertion_currency',
              'E31'=>'var_sku',
              'E32'=>'var_sku_desc',
              'E33'=>'var_qty',
              'E34'=>'var_qty_sold',
              'E35'=>'var_start_pice',
              'E36'=>'var_currency',
              'E37'=>'last_sale_time',
              'E38'=>'last_modify_time',
        );
        return $row;
    }

}