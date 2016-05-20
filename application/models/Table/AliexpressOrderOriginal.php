<?php
class Table_AliexpressOrderOriginal
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AliexpressOrderOriginal();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AliexpressOrderOriginal();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "aoo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "aoo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->delete($where);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public function getByField($value, $field = 'aoo_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }

    public function getAll()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, "*");
        return $this->_table->getAdapter()->fetchAll($select);
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?",$condition["order_status"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["frozen_status"]) && $condition["frozen_status"] != ""){
            $select->where("frozen_status = ?",$condition["frozen_status"]);
        }
        if(isset($condition["issue_status"]) && $condition["issue_status"] != ""){
            $select->where("issue_status = ?",$condition["issue_status"]);
        }
        if(isset($condition["buyer_login_id"]) && $condition["buyer_login_id"] != ""){
            $select->where("buyer_login_id = ?",$condition["buyer_login_id"]);
        }
        if(isset($condition["buyer_signer_fullname"]) && $condition["buyer_signer_fullname"] != ""){
            $select->where("buyer_signer_fullname = ?",$condition["buyer_signer_fullname"]);
        }
        if(isset($condition["seller_login_id"]) && $condition["seller_login_id"] != ""){
            $select->where("seller_login_id = ?",$condition["seller_login_id"]);
        }
        if(isset($condition["seller_signer_fullname"]) && $condition["seller_signer_fullname"] != ""){
            $select->where("seller_signer_fullname = ?",$condition["seller_signer_fullname"]);
        }
        if(isset($condition["fund_status"]) && $condition["fund_status"] != ""){
            $select->where("fund_status = ?",$condition["fund_status"]);
        }
        if(isset($condition["payment_type"]) && $condition["payment_type"] != ""){
            $select->where("payment_type = ?",$condition["payment_type"]);
        }
        if(isset($condition["timeout_left_time"]) && $condition["timeout_left_time"] != ""){
            $select->where("timeout_left_time = ?",$condition["timeout_left_time"]);
        }
        if(isset($condition["biz_type"]) && $condition["biz_type"] != ""){
            $select->where("biz_type = ?",$condition["biz_type"]);
        }
        if(isset($condition["logistics_status"]) && $condition["logistics_status"] != ""){
            $select->where("logistics_status = ?",$condition["logistics_status"]);
        }
        if(isset($condition["order_detail_url"]) && $condition["order_detail_url"] != ""){
            $select->where("order_detail_url = ?",$condition["order_detail_url"]);
        }
        if(isset($condition["left_send_good_day"]) && $condition["left_send_good_day"] != ""){
            $select->where("left_send_good_day = ?",$condition["left_send_good_day"]);
        }
        if(isset($condition["left_send_good_hour"]) && $condition["left_send_good_hour"] != ""){
            $select->where("left_send_good_hour = ?",$condition["left_send_good_hour"]);
        }
        if(isset($condition["left_send_good_min"]) && $condition["left_send_good_min"] != ""){
            $select->where("left_send_good_min = ?",$condition["left_send_good_min"]);
        }
        if(isset($condition["has_request_loan"]) && $condition["has_request_loan"] != ""){
            $select->where("has_request_loan = ?",$condition["has_request_loan"]);
        }
        if(isset($condition["loan_amount"]) && $condition["loan_amount"] != ""){
            $select->where("loan_amount = ?",$condition["loan_amount"]);
        }
        if(isset($condition["loan_amount_cent"]) && $condition["loan_amount_cent"] != ""){
            $select->where("loan_amount_cent = ?",$condition["loan_amount_cent"]);
        }
        if(isset($condition["loan_amount_cent_factor"]) && $condition["loan_amount_cent_factor"] != ""){
            $select->where("loan_amount_cent_factor = ?",$condition["loan_amount_cent_factor"]);
        }
        if(isset($condition["loan_amount_currency_code"]) && $condition["loan_amount_currency_code"] != ""){
            $select->where("loan_amount_currency_code = ?",$condition["loan_amount_currency_code"]);
        }
        if(isset($condition["loan_amount_currency_default_fraction_digits"]) && $condition["loan_amount_currency_default_fraction_digits"] != ""){
            $select->where("loan_amount_currency_default_fraction_digits = ?",$condition["loan_amount_currency_default_fraction_digits"]);
        }
        if(isset($condition["loan_amount_currency_currency_code"]) && $condition["loan_amount_currency_currency_code"] != ""){
            $select->where("loan_amount_currency_currency_code = ?",$condition["loan_amount_currency_currency_code"]);
        }
        if(isset($condition["loan_amount_currency_symbol"]) && $condition["loan_amount_currency_symbol"] != ""){
            $select->where("loan_amount_currency_symbol = ?",$condition["loan_amount_currency_symbol"]);
        }
        if(isset($condition["pay_amount"]) && $condition["pay_amount"] != ""){
            $select->where("pay_amount = ?",$condition["pay_amount"]);
        }
        if(isset($condition["pay_amount_cent"]) && $condition["pay_amount_cent"] != ""){
            $select->where("pay_amount_cent = ?",$condition["pay_amount_cent"]);
        }
        if(isset($condition["pay_amount_cent_factor"]) && $condition["pay_amount_cent_factor"] != ""){
            $select->where("pay_amount_cent_factor = ?",$condition["pay_amount_cent_factor"]);
        }
        if(isset($condition["pay_amount_currency_code"]) && $condition["pay_amount_currency_code"] != ""){
            $select->where("pay_amount_currency_code = ?",$condition["pay_amount_currency_code"]);
        }
        if(isset($condition["pay_amount_currency_default_fraction_digits"]) && $condition["pay_amount_currency_default_fraction_digits"] != ""){
            $select->where("pay_amount_currency_default_fraction_digits = ?",$condition["pay_amount_currency_default_fraction_digits"]);
        }
        if(isset($condition["pay_amount_currency_currency_code"]) && $condition["pay_amount_currency_currency_code"] != ""){
            $select->where("pay_amount_currency_currency_code = ?",$condition["pay_amount_currency_currency_code"]);
        }
        if(isset($condition["pay_amount_currency_symbol"]) && $condition["pay_amount_currency_symbol"] != ""){
            $select->where("pay_amount_currency_symbol = ?",$condition["pay_amount_currency_symbol"]);
        }
        if(isset($condition["buyer_last_name"]) && $condition["buyer_last_name"] != ""){
            $select->where("buyer_last_name = ?",$condition["buyer_last_name"]);
        }
        if(isset($condition["buyer_first_name"]) && $condition["buyer_first_name"] != ""){
            $select->where("buyer_first_name = ?",$condition["buyer_first_name"]);
        }
        if(isset($condition["buyer_country_code"]) && $condition["buyer_country_code"] != ""){
            $select->where("buyer_country_code = ?",$condition["buyer_country_code"]);
        }
        if(isset($condition["buyer_email"]) && $condition["buyer_email"] != ""){
            $select->where("buyer_email = ?",$condition["buyer_email"]);
        }
        if(isset($condition["logistics_amount"]) && $condition["logistics_amount"] != ""){
            $select->where("logistics_amount = ?",$condition["logistics_amount"]);
        }
        if(isset($condition["logistics_cent"]) && $condition["logistics_cent"] != ""){
            $select->where("logistics_cent = ?",$condition["logistics_cent"]);
        }
        if(isset($condition["logistics_cent_factor"]) && $condition["logistics_cent_factor"] != ""){
            $select->where("logistics_cent_factor = ?",$condition["logistics_cent_factor"]);
        }
        if(isset($condition["logistics_currency_code"]) && $condition["logistics_currency_code"] != ""){
            $select->where("logistics_currency_code = ?",$condition["logistics_currency_code"]);
        }
        if(isset($condition["logistics_currency_default_fraction_digits"]) && $condition["logistics_currency_default_fraction_digits"] != ""){
            $select->where("logistics_currency_default_fraction_digits = ?",$condition["logistics_currency_default_fraction_digits"]);
        }
        if(isset($condition["logistics_currency_currency_code"]) && $condition["logistics_currency_currency_code"] != ""){
            $select->where("logistics_currency_currency_code = ?",$condition["logistics_currency_currency_code"]);
        }
        if(isset($condition["logistics_currency_symbol"]) && $condition["logistics_currency_symbol"] != ""){
            $select->where("logistics_currency_symbol = ?",$condition["logistics_currency_symbol"]);
        }
        if(isset($condition["logistics_type_code"]) && $condition["logistics_type_code"] != ""){
            $select->where("logistics_type_code = ?",$condition["logistics_type_code"]);
        }
        if(isset($condition["receive_status"]) && $condition["receive_status"] != ""){
            $select->where("receive_status = ?",$condition["receive_status"]);
        }
        if(isset($condition["logistics_no"]) && $condition["logistics_no"] != ""){
            $select->where("logistics_no = ?",$condition["logistics_no"]);
        }
        if(isset($condition["logistics_service_name"]) && $condition["logistics_service_name"] != ""){
            $select->where("logistics_service_name = ?",$condition["logistics_service_name"]);
        }
        if(isset($condition["order_amount"]) && $condition["order_amount"] != ""){
            $select->where("order_amount = ?",$condition["order_amount"]);
        }
        if(isset($condition["order_cent"]) && $condition["order_cent"] != ""){
            $select->where("order_cent = ?",$condition["order_cent"]);
        }
        if(isset($condition["order_cent_factor"]) && $condition["order_cent_factor"] != ""){
            $select->where("order_cent_factor = ?",$condition["order_cent_factor"]);
        }
        if(isset($condition["order_currency_code"]) && $condition["order_currency_code"] != ""){
            $select->where("order_currency_code = ?",$condition["order_currency_code"]);
        }
        if(isset($condition["order_currency_default_fraction_digits"]) && $condition["order_currency_default_fraction_digits"] != ""){
            $select->where("order_currency_default_fraction_digits = ?",$condition["order_currency_default_fraction_digits"]);
        }
        if(isset($condition["order_currency_currency_code"]) && $condition["order_currency_currency_code"] != ""){
            $select->where("order_currency_currency_code = ?",$condition["order_currency_currency_code"]);
        }
        if(isset($condition["order_currency_symbol"]) && $condition["order_currency_symbol"] != ""){
            $select->where("order_currency_symbol = ?",$condition["order_currency_symbol"]);
        }
        if(isset($condition["init_oder_amount"]) && $condition["init_oder_amount"] != ""){
            $select->where("init_oder_amount = ?",$condition["init_oder_amount"]);
        }
        if(isset($condition["init_oder_cent"]) && $condition["init_oder_cent"] != ""){
            $select->where("init_oder_cent = ?",$condition["init_oder_cent"]);
        }
        if(isset($condition["init_oder_cent_factor"]) && $condition["init_oder_cent_factor"] != ""){
            $select->where("init_oder_cent_factor = ?",$condition["init_oder_cent_factor"]);
        }
        if(isset($condition["init_oder_currency_code"]) && $condition["init_oder_currency_code"] != ""){
            $select->where("init_oder_currency_code = ?",$condition["init_oder_currency_code"]);
        }
        if(isset($condition["init_oder_currency_default_fraction_digits"]) && $condition["init_oder_currency_default_fraction_digits"] != ""){
            $select->where("init_oder_currency_default_fraction_digits = ?",$condition["init_oder_currency_default_fraction_digits"]);
        }
        if(isset($condition["init_oder_currency_currency_code"]) && $condition["init_oder_currency_currency_code"] != ""){
            $select->where("init_oder_currency_currency_code = ?",$condition["init_oder_currency_currency_code"]);
        }
        if(isset($condition["init_oder_currency_symbol"]) && $condition["init_oder_currency_symbol"] != ""){
            $select->where("init_oder_currency_symbol = ?",$condition["init_oder_currency_symbol"]);
        }
        if(isset($condition["refund_info"]) && $condition["refund_info"] != ""){
            $select->where("refund_info = ?",$condition["refund_info"]);
        }
        if(isset($condition["order_msg_list"]) && $condition["order_msg_list"] != ""){
            $select->where("order_msg_list = ?",$condition["order_msg_list"]);
        }
        if(isset($condition["opr_log_dto_list"]) && $condition["opr_log_dto_list"] != ""){
            $select->where("opr_log_dto_list = ?",$condition["opr_log_dto_list"]);
        }
        if(isset($condition["seller_operator_login_id"]) && $condition["seller_operator_login_id"] != ""){
            $select->where("seller_operator_login_id = ?",$condition["seller_operator_login_id"]);
        }
        if(isset($condition["loan_info_amount"]) && $condition["loan_info_amount"] != ""){
            $select->where("loan_info_amount = ?",$condition["loan_info_amount"]);
        }
        if(isset($condition["loan_status"]) && $condition["loan_status"] != ""){
            $select->where("loan_status = ?",$condition["loan_status"]);
        }
        if(isset($condition["seller_operator_aliidloginid"]) && $condition["seller_operator_aliidloginid"] != ""){
            $select->where("seller_operator_aliidloginid = ?",$condition["seller_operator_aliidloginid"]);
        }
        if(isset($condition["escrow_fee"]) && $condition["escrow_fee"] != ""){
            $select->where("escrow_fee = ?",$condition["escrow_fee"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?",$condition["country_code"]);
        }
        if(isset($condition["contact_person"]) && $condition["contact_person"] != ""){
            $select->where("contact_person = ?",$condition["contact_person"]);
        }
        if(isset($condition["address"]) && $condition["address"] != ""){
            $select->where("address = ?",$condition["address"]);
        }
        if(isset($condition["address2"]) && $condition["address2"] != ""){
            $select->where("address2 = ?",$condition["address2"]);
        }
        if(isset($condition["detail_address"]) && $condition["detail_address"] != ""){
            $select->where("detail_address = ?",$condition["detail_address"]);
        }
        if(isset($condition["province"]) && $condition["province"] != ""){
            $select->where("province = ?",$condition["province"]);
        }
        if(isset($condition["city"]) && $condition["city"] != ""){
            $select->where("city = ?",$condition["city"]);
        }
        if(isset($condition["zip"]) && $condition["zip"] != ""){
            $select->where("zip = ?",$condition["zip"]);
        }
        if(isset($condition["mobile_no"]) && $condition["mobile_no"] != ""){
            $select->where("mobile_no = ?",$condition["mobile_no"]);
        }
        if(isset($condition["phone_country"]) && $condition["phone_country"] != ""){
            $select->where("phone_country = ?",$condition["phone_country"]);
        }
        if(isset($condition["phone_area"]) && $condition["phone_area"] != ""){
            $select->where("phone_area = ?",$condition["phone_area"]);
        }
        if(isset($condition["phone_number"]) && $condition["phone_number"] != ""){
            $select->where("phone_number = ?",$condition["phone_number"]);
        }
        if(isset($condition["fax_country"]) && $condition["fax_country"] != ""){
            $select->where("fax_country = ?",$condition["fax_country"]);
        }
        if(isset($condition["fax_area"]) && $condition["fax_area"] != ""){
            $select->where("fax_area = ?",$condition["fax_area"]);
        }
        if(isset($condition["fax_number"]) && $condition["fax_number"] != ""){
            $select->where("fax_number = ?",$condition["fax_number"]);
        }
        if(isset($condition["is_loaded"]) && $condition["is_loaded"] != ""){
            $select->where("is_loaded = ?",$condition["is_loaded"]);
        }
        
//         echo $select->__toString();exit;
        
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            if (!empty($orderBy)) {
                $select->order($orderBy);
            }
            if ($pageSize > 0 and $page > 0) {
                $start = ($page - 1) * $pageSize;
                $select->limit($pageSize, $start);
            }
            $sql = $select->__toString();
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}