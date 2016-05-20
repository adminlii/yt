<?php
class Service_AliexpressOrderOriginal extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AliexpressOrderOriginal|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AliexpressOrderOriginal();
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
    public static function update($row, $value, $field = "aoo_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "aoo_id")
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
    public static function getByField($value, $field = 'aoo_id', $colums = "*")
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
        
              'E0'=>'aoo_id',
              'E1'=>'order_id',
              'E2'=>'order_status',
              'E3'=>'user_account',
              'E4'=>'frozen_status',
              'E5'=>'issue_status',
              'E6'=>'buyer_login_id',
              'E7'=>'buyer_signer_fullname',
              'E8'=>'seller_login_id',
              'E9'=>'seller_signer_fullname',
              'E10'=>'fund_status',
              'E11'=>'payment_type',
              'E12'=>'gmt_pay_time',
              'E13'=>'gmt_create',
              'E14'=>'gmt_send_goods_time',
              'E15'=>'timeout_left_time',
              'E16'=>'biz_type',
              'E17'=>'logistics_status',
              'E18'=>'order_detail_url',
              'E19'=>'left_send_good_day',
              'E20'=>'left_send_good_hour',
              'E21'=>'left_send_good_min',
              'E22'=>'has_request_loan',
              'E23'=>'loan_amount',
              'E24'=>'loan_amount_cent',
              'E25'=>'loan_amount_cent_factor',
              'E26'=>'loan_amount_currency_code',
              'E27'=>'loan_amount_currency_default_fraction_digits',
              'E28'=>'loan_amount_currency_currency_code',
              'E29'=>'loan_amount_currency_symbol',
              'E30'=>'pay_amount',
              'E31'=>'pay_amount_cent',
              'E32'=>'pay_amount_cent_factor',
              'E33'=>'pay_amount_currency_code',
              'E34'=>'pay_amount_currency_default_fraction_digits',
              'E35'=>'pay_amount_currency_currency_code',
              'E36'=>'pay_amount_currency_symbol',
              'E37'=>'gmt_modified',
              'E38'=>'gmt_trade_end',
              'E39'=>'buyer_last_name',
              'E40'=>'buyer_first_name',
              'E41'=>'buyer_country_code',
              'E42'=>'buyer_email',
              'E43'=>'logistics_amount',
              'E44'=>'logistics_cent',
              'E45'=>'logistics_cent_factor',
              'E46'=>'logistics_currency_code',
              'E47'=>'logistics_currency_default_fraction_digits',
              'E48'=>'logistics_currency_currency_code',
              'E49'=>'logistics_currency_symbol',
              'E50'=>'logistics_type_code',
              'E51'=>'gmt_received',
              'E52'=>'receive_status',
              'E53'=>'logistics_no',
              'E54'=>'logistics_service_name',
              'E55'=>'gmt_send',
              'E56'=>'order_amount',
              'E57'=>'order_cent',
              'E58'=>'order_cent_factor',
              'E59'=>'order_currency_code',
              'E60'=>'order_currency_default_fraction_digits',
              'E61'=>'order_currency_currency_code',
              'E62'=>'order_currency_symbol',
              'E63'=>'init_oder_amount',
              'E64'=>'init_oder_cent',
              'E65'=>'init_oder_cent_factor',
              'E66'=>'init_oder_currency_code',
              'E67'=>'init_oder_currency_default_fraction_digits',
              'E68'=>'init_oder_currency_currency_code',
              'E69'=>'init_oder_currency_symbol',
              'E70'=>'refund_info',
              'E71'=>'order_msg_list',
              'E72'=>'opr_log_dto_list',
              'E73'=>'seller_operator_login_id',
              'E74'=>'loan_info_amount',
              'E75'=>'loan_info_time',
              'E76'=>'loan_status',
              'E77'=>'gmt_pay_success',
              'E78'=>'seller_operator_aliidloginid',
              'E79'=>'escrow_fee',
              'E80'=>'country_code',
              'E81'=>'contact_person',
              'E82'=>'address',
              'E83'=>'address2',
              'E84'=>'detail_address',
              'E85'=>'province',
              'E86'=>'city',
              'E87'=>'zip',
              'E88'=>'mobile_no',
              'E89'=>'phone_country',
              'E90'=>'phone_area',
              'E91'=>'phone_number',
              'E92'=>'fax_country',
              'E93'=>'fax_area',
              'E94'=>'fax_number',
              'E95'=>'is_loaded',
        	  'E96'=>'company_code',
        	  'E97'=>'sys_creation_date',
        	  'E98'=>'sys_last_update',
        );
        return $row;
    }

}