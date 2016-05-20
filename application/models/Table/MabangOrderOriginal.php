<?php
class Table_MabangOrderOriginal
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MabangOrderOriginal();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MabangOrderOriginal();
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
    public function update($row, $value, $field = "moo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "moo_id")
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
    public function getByField($value, $field = 'moo_id', $colums = "*")
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
        
        if(isset($condition["code"]) && $condition["code"] != ""){
            $select->where("code = ?",$condition["code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["platformTradeCode"]) && $condition["platformTradeCode"] != ""){
            $select->where("platformTradeCode = ?",$condition["platformTradeCode"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
        if(isset($condition["hasException"]) && $condition["hasException"] != ""){
            $select->where("hasException = ?",$condition["hasException"]);
        }
        if(isset($condition["processMessage"]) && $condition["processMessage"] != ""){
            $select->where("processMessage = ?",$condition["processMessage"]);
        }
        if(isset($condition["packageId"]) && $condition["packageId"] != ""){
            $select->where("packageId = ?",$condition["packageId"]);
        }
        if(isset($condition["priceForcast"]) && $condition["priceForcast"] != ""){
            $select->where("priceForcast = ?",$condition["priceForcast"]);
        }
        if(isset($condition["priceReal"]) && $condition["priceReal"] != ""){
            $select->where("priceReal = ?",$condition["priceReal"]);
        }
        if(isset($condition["shippingCountryCode"]) && $condition["shippingCountryCode"] != ""){
            $select->where("shippingCountryCode = ?",$condition["shippingCountryCode"]);
        }
        if(isset($condition["timeCreated"]) && $condition["timeCreated"] != ""){
            $select->where("timeCreated = ?",$condition["timeCreated"]);
        }
        if(isset($condition["weightForcast"]) && $condition["weightForcast"] != ""){
            $select->where("weightForcast = ?",$condition["weightForcast"]);
        }
        if(isset($condition["weightReal"]) && $condition["weightReal"] != ""){
            $select->where("weightReal = ?",$condition["weightReal"]);
        }
        if(isset($condition["length"]) && $condition["length"] != ""){
            $select->where("length = ?",$condition["length"]);
        }
        if(isset($condition["width"]) && $condition["width"] != ""){
            $select->where("width = ?",$condition["width"]);
        }
        if(isset($condition["height"]) && $condition["height"] != ""){
            $select->where("height = ?",$condition["height"]);
        }
        if(isset($condition["productNameCn"]) && $condition["productNameCn"] != ""){
            $select->where("productNameCn = ?",$condition["productNameCn"]);
        }
        if(isset($condition["productNameEn"]) && $condition["productNameEn"] != ""){
            $select->where("productNameEn = ?",$condition["productNameEn"]);
        }
        if(isset($condition["productValue"]) && $condition["productValue"] != ""){
            $select->where("productValue = ?",$condition["productValue"]);
        }
        if(isset($condition["remark"]) && $condition["remark"] != ""){
            $select->where("remark = ?",$condition["remark"]);
        }
        if(isset($condition["itemListQuantity"]) && $condition["itemListQuantity"] != ""){
            $select->where("itemListQuantity = ?",$condition["itemListQuantity"]);
        }
        if(isset($condition["pickup_contact"]) && $condition["pickup_contact"] != ""){
            $select->where("pickup_contact = ?",$condition["pickup_contact"]);
        }
        if(isset($condition["pickup_province"]) && $condition["pickup_province"] != ""){
            $select->where("pickup_province = ?",$condition["pickup_province"]);
        }
        if(isset($condition["pickup_city"]) && $condition["pickup_city"] != ""){
            $select->where("pickup_city = ?",$condition["pickup_city"]);
        }
        if(isset($condition["pickup_area"]) && $condition["pickup_area"] != ""){
            $select->where("pickup_area = ?",$condition["pickup_area"]);
        }
        if(isset($condition["pickup_address"]) && $condition["pickup_address"] != ""){
            $select->where("pickup_address = ?",$condition["pickup_address"]);
        }
        if(isset($condition["pickup_telephone"]) && $condition["pickup_telephone"] != ""){
            $select->where("pickup_telephone = ?",$condition["pickup_telephone"]);
        }
        if(isset($condition["pickup_mobile"]) && $condition["pickup_mobile"] != ""){
            $select->where("pickup_mobile = ?",$condition["pickup_mobile"]);
        }
        if(isset($condition["pickup_zipcode"]) && $condition["pickup_zipcode"] != ""){
            $select->where("pickup_zipcode = ?",$condition["pickup_zipcode"]);
        }
        if(isset($condition["create_time_sys"]) && $condition["create_time_sys"] != ""){
            $select->where("create_time_sys = ?",$condition["create_time_sys"]);
        }
        if(isset($condition["update_time_sys"]) && $condition["update_time_sys"] != ""){
            $select->where("update_time_sys = ?",$condition["update_time_sys"]);
        }
        if(isset($condition["back_contact"]) && $condition["back_contact"] != ""){
            $select->where("back_contact = ?",$condition["back_contact"]);
        }
        if(isset($condition["back_province"]) && $condition["back_province"] != ""){
            $select->where("back_province = ?",$condition["back_province"]);
        }
        if(isset($condition["back_city"]) && $condition["back_city"] != ""){
            $select->where("back_city = ?",$condition["back_city"]);
        }
        if(isset($condition["back_area"]) && $condition["back_area"] != ""){
            $select->where("back_area = ?",$condition["back_area"]);
        }
        if(isset($condition["back_address"]) && $condition["back_address"] != ""){
            $select->where("back_address = ?",$condition["back_address"]);
        }
        if(isset($condition["back_telephone"]) && $condition["back_telephone"] != ""){
            $select->where("back_telephone = ?",$condition["back_telephone"]);
        }
        if(isset($condition["back_mobile"]) && $condition["back_mobile"] != ""){
            $select->where("back_mobile = ?",$condition["back_mobile"]);
        }
        if(isset($condition["back_zipcode"]) && $condition["back_zipcode"] != ""){
            $select->where("back_zipcode = ?",$condition["back_zipcode"]);
        }
        if(isset($condition["receive_countryCode"]) && $condition["receive_countryCode"] != ""){
            $select->where("receive_countryCode = ?",$condition["receive_countryCode"]);
        }
        if(isset($condition["receive_receiver"]) && $condition["receive_receiver"] != ""){
            $select->where("receive_receiver = ?",$condition["receive_receiver"]);
        }
        if(isset($condition["receive_province"]) && $condition["receive_province"] != ""){
            $select->where("receive_province = ?",$condition["receive_province"]);
        }
        if(isset($condition["receive_city"]) && $condition["receive_city"] != ""){
            $select->where("receive_city = ?",$condition["receive_city"]);
        }
        if(isset($condition["receive_street1"]) && $condition["receive_street1"] != ""){
            $select->where("receive_street1 = ?",$condition["receive_street1"]);
        }
        if(isset($condition["receive_telephone"]) && $condition["receive_telephone"] != ""){
            $select->where("receive_telephone = ?",$condition["receive_telephone"]);
        }
        if(isset($condition["receive_zipcode"]) && $condition["receive_zipcode"] != ""){
            $select->where("receive_zipcode = ?",$condition["receive_zipcode"]);
        }
        if(isset($condition["expresschannelcode"]) && $condition["expresschannelcode"] != ""){
            $select->where("expresschannelcode = ?",$condition["expresschannelcode"]);
        }
        if(isset($condition["expresschannelname"]) && $condition["expresschannelname"] != ""){
            $select->where("expresschannelname = ?",$condition["expresschannelname"]);
        }
        if(isset($condition["expresschanneltype"]) && $condition["expresschanneltype"] != ""){
            $select->where("expresschanneltype = ?",$condition["expresschanneltype"]);
        }
        if(isset($condition["myexpresschannelname"]) && $condition["myexpresschannelname"] != ""){
            $select->where("myexpresschannelname = ?",$condition["myexpresschannelname"]);
        }
        if(isset($condition["myexpresschannelcustomerCode"]) && $condition["myexpresschannelcustomerCode"] != ""){
            $select->where("myexpresschannelcustomerCode = ?",$condition["myexpresschannelcustomerCode"]);
        }
        if(isset($condition["htmlurl_b10_10_a"]) && $condition["htmlurl_b10_10_a"] != ""){
            $select->where("htmlurl_b10_10_a = ?",$condition["htmlurl_b10_10_a"]);
        }
        if(isset($condition["htmlurl_b10_10_c"]) && $condition["htmlurl_b10_10_c"] != ""){
            $select->where("htmlurl_b10_10_c = ?",$condition["htmlurl_b10_10_c"]);
        }
        if(isset($condition["htmlurl_b10_10_ac"]) && $condition["htmlurl_b10_10_ac"] != ""){
            $select->where("htmlurl_b10_10_ac = ?",$condition["htmlurl_b10_10_ac"]);
        }
        if(isset($condition["htmlurl_a4_a"]) && $condition["htmlurl_a4_a"] != ""){
            $select->where("htmlurl_a4_a = ?",$condition["htmlurl_a4_a"]);
        }
        if(isset($condition["htmlurl_a4_c"]) && $condition["htmlurl_a4_c"] != ""){
            $select->where("htmlurl_a4_c = ?",$condition["htmlurl_a4_c"]);
        }
        if(isset($condition["htmlurl_a4_ac"]) && $condition["htmlurl_a4_ac"] != ""){
            $select->where("htmlurl_a4_ac = ?",$condition["htmlurl_a4_ac"]);
        }
        if(isset($condition["pdfurl_b10_10_a"]) && $condition["pdfurl_b10_10_a"] != ""){
            $select->where("pdfurl_b10_10_a = ?",$condition["pdfurl_b10_10_a"]);
        }
        if(isset($condition["pdfurl_b10_10_c"]) && $condition["pdfurl_b10_10_c"] != ""){
            $select->where("pdfurl_b10_10_c = ?",$condition["pdfurl_b10_10_c"]);
        }
        if(isset($condition["pdfurl_b10_10_ac"]) && $condition["pdfurl_b10_10_ac"] != ""){
            $select->where("pdfurl_b10_10_ac = ?",$condition["pdfurl_b10_10_ac"]);
        }
        if(isset($condition["pdfurl_a4_a"]) && $condition["pdfurl_a4_a"] != ""){
            $select->where("pdfurl_a4_a = ?",$condition["pdfurl_a4_a"]);
        }
        if(isset($condition["pdfurl_a4_c"]) && $condition["pdfurl_a4_c"] != ""){
            $select->where("pdfurl_a4_c = ?",$condition["pdfurl_a4_c"]);
        }
        if(isset($condition["pdfurl_a4_ac"]) && $condition["pdfurl_a4_ac"] != ""){
            $select->where("pdfurl_a4_ac = ?",$condition["pdfurl_a4_ac"]);
        }
        if(isset($condition["imgurl_b10_10_a"]) && $condition["imgurl_b10_10_a"] != ""){
            $select->where("imgurl_b10_10_a = ?",$condition["imgurl_b10_10_a"]);
        }
        if(isset($condition["imgurl_b10_10_c"]) && $condition["imgurl_b10_10_c"] != ""){
            $select->where("imgurl_b10_10_c = ?",$condition["imgurl_b10_10_c"]);
        }
        if(isset($condition["imgurl_a4_a"]) && $condition["imgurl_a4_a"] != ""){
            $select->where("imgurl_a4_a = ?",$condition["imgurl_a4_a"]);
        }
        if(isset($condition["imgurl_a4_c"]) && $condition["imgurl_a4_c"] != ""){
            $select->where("imgurl_a4_c = ?",$condition["imgurl_a4_c"]);
        }
        if(isset($condition["customer_username"]) && $condition["customer_username"] != ""){
            $select->where("customer_username = ?",$condition["customer_username"]);
        }
        if(isset($condition["customer_name"]) && $condition["customer_name"] != ""){
            $select->where("customer_name = ?",$condition["customer_name"]);
        }
        if(isset($condition["is_loaded"]) && $condition["is_loaded"] != ""){
            $select->where("is_loaded = ?",$condition["is_loaded"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
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