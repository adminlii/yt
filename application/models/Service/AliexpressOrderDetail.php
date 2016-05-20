<?php
class Service_AliexpressOrderDetail extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AliexpressOrderDetail|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AliexpressOrderDetail();
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
    public static function update($row, $value, $field = "aod_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "aod_id")
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
    public static function getByField($value, $field = 'aod_id', $colums = "*")
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
        
              'E0'=>'aod_id',
              'E1'=>'aoo_id',
              'E2'=>'child_id',
              'E3'=>'order_id',
              'E4'=>'son_order_status',
              'E5'=>'goods_prepare_time',
              'E6'=>'memo',
              'E7'=>'sku_code',
              'E8'=>'product_id',
              'E9'=>'product_count',
              'E10'=>'product_unit',
              'E11'=>'product_img_url',
              'E12'=>'product_name',
              'E13'=>'product_standard',
              'E14'=>'product_snap_url',
              'E15'=>'show_status',
              'E16'=>'product_unit_price_amount',
              'E17'=>'product_unit_price_cent',
              'E18'=>'product_unit_price_cent_factor',
              'E19'=>'product_unit_price_currency_code',
              'E20'=>'product_unit_price_currency_default_fraction_digits',
              'E21'=>'product_unit_price_currency_currency_code',
              'E22'=>'product_unit_price_currency_symbol',
              'E23'=>'total_product_amount',
              'E24'=>'total_product_cent',
              'E25'=>'total_product_cent_factor',
              'E26'=>'total_product_currency_code',
              'E27'=>'total_product_currency_default_fraction_digits',
              'E28'=>'total_product_currency_currency_code',
              'E29'=>'total_product_currency_symbol',
              'E30'=>'freight_commit_day',
              'E31'=>'can_submit_issue',
              'E32'=>'issue_status',
              'E33'=>'issue_mode',
              'E34'=>'logistics_type',
              'E35'=>'logistics_service_name',
              'E36'=>'money_back_three',
              'E37'=>'send_goods_time',
              'E38'=>'delivery_time',
              'E39'=>'fund_status',
        );
        return $row;
    }

}