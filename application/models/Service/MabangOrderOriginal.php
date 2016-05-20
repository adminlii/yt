<?php
class Service_MabangOrderOriginal extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_MabangOrderOriginal|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_MabangOrderOriginal();
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
    public static function update($row, $value, $field = "moo_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "moo_id")
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
    public static function getByField($value, $field = 'moo_id', $colums = "*")
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
        
              'E0'=>'moo_id',
              'E1'=>'code',
              'E2'=>'user_account',
              'E3'=>'company_code',
              'E4'=>'platformTradeCode',
              'E5'=>'status',
              'E6'=>'hasException',
              'E7'=>'processMessage',
              'E8'=>'packageId',
              'E9'=>'priceForcast',
              'E10'=>'priceReal',
              'E11'=>'shippingCountryCode',
              'E12'=>'timeCreated',
              'E13'=>'weightForcast',
              'E14'=>'weightReal',
              'E15'=>'length',
              'E16'=>'width',
              'E17'=>'height',
              'E18'=>'productNameCn',
              'E19'=>'productNameEn',
              'E20'=>'productValue',
              'E21'=>'remark',
              'E22'=>'itemListQuantity',
              'E23'=>'pickup_contact',
              'E24'=>'pickup_province',
              'E25'=>'pickup_city',
              'E26'=>'pickup_area',
              'E27'=>'pickup_address',
              'E28'=>'pickup_telephone',
              'E29'=>'pickup_mobile',
              'E30'=>'pickup_zipcode',
              'E31'=>'create_time_sys',
              'E32'=>'update_time_sys',
              'E33'=>'back_contact',
              'E34'=>'back_province',
              'E35'=>'back_city',
              'E36'=>'back_area',
              'E37'=>'back_address',
              'E38'=>'back_telephone',
              'E39'=>'back_mobile',
              'E40'=>'back_zipcode',
              'E41'=>'receive_countryCode',
              'E42'=>'receive_receiver',
              'E43'=>'receive_province',
              'E44'=>'receive_city',
              'E45'=>'receive_street1',
              'E46'=>'receive_telephone',
              'E47'=>'receive_zipcode',
              'E48'=>'expresschannelcode',
              'E49'=>'expresschannelname',
              'E50'=>'expresschanneltype',
              'E51'=>'myexpresschannelname',
              'E52'=>'myexpresschannelcustomerCode',
              'E53'=>'htmlurl_b10_10_a',
              'E54'=>'htmlurl_b10_10_c',
              'E55'=>'htmlurl_b10_10_ac',
              'E56'=>'htmlurl_a4_a',
              'E57'=>'htmlurl_a4_c',
              'E58'=>'htmlurl_a4_ac',
              'E59'=>'pdfurl_b10_10_a',
              'E60'=>'pdfurl_b10_10_c',
              'E61'=>'pdfurl_b10_10_ac',
              'E62'=>'pdfurl_a4_a',
              'E63'=>'pdfurl_a4_c',
              'E64'=>'pdfurl_a4_ac',
              'E65'=>'imgurl_b10_10_a',
              'E66'=>'imgurl_b10_10_c',
              'E67'=>'imgurl_a4_a',
              'E68'=>'imgurl_a4_c',
              'E69'=>'customer_username',
              'E70'=>'customer_name',
              'E71'=>'is_loaded',
              'E72'=>'user_id',
        );
        return $row;
    }

}