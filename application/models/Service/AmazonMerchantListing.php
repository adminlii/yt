<?php
class Service_AmazonMerchantListing extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AmazonMerchantListing|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AmazonMerchantListing();
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
    public static function update($row, $value, $field = "id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "id")
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
    public static function getByField($value, $field = 'id', $colums = "*")
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
        
              'E0'=>'id',
              'E1'=>'company_code',
              'E2'=>'user_account',
              'E3'=>'listing_id',
              'E4'=>'seller_sku',
              'E5'=>'product_id',
              'E6'=>'item_name',
              'E7'=>'item_description',
              'E8'=>'price',
              'E9'=>'quantity',
              'E10'=>'open_date',
              'E11'=>'image_url',
              'E12'=>'item_is_marketplace',
              'E13'=>'product_id_type',
              'E14'=>'zshop_shipping_fee',
              'E15'=>'item_note',
              'E16'=>'item_condition',
              'E17'=>'zshop_category1',
              'E18'=>'zshop_browse_path',
              'E19'=>'zshop_storefront_feature',
              'E20'=>'asin1',
              'E21'=>'asin2',
              'E22'=>'asin3',
              'E23'=>'will_ship_internationally',
              'E24'=>'expedited_shipping',
              'E25'=>'zshop_boldface',
              'E26'=>'bid_for_featured_placement',
              'E27'=>'add_delete',
              'E28'=>'pending_quantity',
              'E29'=>'fulfillment_channel',
              'E30'=>'add_time',
              'E31'=>'update_time',
        );
        return $row;
    }

}