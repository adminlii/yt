<?php
class Table_ShopifyShop
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyShop();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyShop();
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
    public function update($row, $value, $field = "id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "id")
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
    public function getByField($value, $field = 'id', $colums = "*")
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
       
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["address1"]) && $condition["address1"] != ""){
            $select->where("address1 = ?",$condition["address1"]);
        }
        if(isset($condition["city"]) && $condition["city"] != ""){
            $select->where("city = ?",$condition["city"]);
        }
        if(isset($condition["country"]) && $condition["country"] != ""){
            $select->where("country = ?",$condition["country"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["customer_email"]) && $condition["customer_email"] != ""){
            $select->where("customer_email = ?",$condition["customer_email"]);
        }
        if(isset($condition["domain"]) && $condition["domain"] != ""){
            $select->where("domain = ?",$condition["domain"]);
        }
        if(isset($condition["email"]) && $condition["email"] != ""){
            $select->where("email = ?",$condition["email"]);
        }
        if(isset($condition["latitude"]) && $condition["latitude"] != ""){
            $select->where("latitude = ?",$condition["latitude"]);
        }
        if(isset($condition["longitude"]) && $condition["longitude"] != ""){
            $select->where("longitude = ?",$condition["longitude"]);
        }
        if(isset($condition["name"]) && $condition["name"] != ""){
            $select->where("name = ?",$condition["name"]);
        }
        if(isset($condition["phone"]) && $condition["phone"] != ""){
            $select->where("phone = ?",$condition["phone"]);
        }
        if(isset($condition["primary_location_id"]) && $condition["primary_location_id"] != ""){
            $select->where("primary_location_id = ?",$condition["primary_location_id"]);
        }
        if(isset($condition["province"]) && $condition["province"] != ""){
            $select->where("province = ?",$condition["province"]);
        }
        if(isset($condition["public"]) && $condition["public"] != ""){
            $select->where("public = ?",$condition["public"]);
        }
        if(isset($condition["source"]) && $condition["source"] != ""){
            $select->where("source = ?",$condition["source"]);
        }
        if(isset($condition["zip"]) && $condition["zip"] != ""){
            $select->where("zip = ?",$condition["zip"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?",$condition["country_code"]);
        }
        if(isset($condition["country_name"]) && $condition["country_name"] != ""){
            $select->where("country_name = ?",$condition["country_name"]);
        }
        if(isset($condition["currency"]) && $condition["currency"] != ""){
            $select->where("currency = ?",$condition["currency"]);
        }
        if(isset($condition["timezone"]) && $condition["timezone"] != ""){
            $select->where("timezone = ?",$condition["timezone"]);
        }
        if(isset($condition["shop_owner"]) && $condition["shop_owner"] != ""){
            $select->where("shop_owner = ?",$condition["shop_owner"]);
        }
        if(isset($condition["money_format"]) && $condition["money_format"] != ""){
            $select->where("money_format = ?",$condition["money_format"]);
        }
        if(isset($condition["money_with_currency_format"]) && $condition["money_with_currency_format"] != ""){
            $select->where("money_with_currency_format = ?",$condition["money_with_currency_format"]);
        }
        if(isset($condition["province_code"]) && $condition["province_code"] != ""){
            $select->where("province_code = ?",$condition["province_code"]);
        }
        if(isset($condition["taxes_included"]) && $condition["taxes_included"] != ""){
            $select->where("taxes_included = ?",$condition["taxes_included"]);
        }
        if(isset($condition["tax_shipping"]) && $condition["tax_shipping"] != ""){
            $select->where("tax_shipping = ?",$condition["tax_shipping"]);
        }
        if(isset($condition["county_taxes"]) && $condition["county_taxes"] != ""){
            $select->where("county_taxes = ?",$condition["county_taxes"]);
        }
        if(isset($condition["plan_display_name"]) && $condition["plan_display_name"] != ""){
            $select->where("plan_display_name = ?",$condition["plan_display_name"]);
        }
        if(isset($condition["plan_name"]) && $condition["plan_name"] != ""){
            $select->where("plan_name = ?",$condition["plan_name"]);
        }
        if(isset($condition["myshopify_domain"]) && $condition["myshopify_domain"] != ""){
            $select->where("myshopify_domain = ?",$condition["myshopify_domain"]);
        }
        if(isset($condition["google_apps_domain"]) && $condition["google_apps_domain"] != ""){
            $select->where("google_apps_domain = ?",$condition["google_apps_domain"]);
        }
        if(isset($condition["google_apps_login_enabled"]) && $condition["google_apps_login_enabled"] != ""){
            $select->where("google_apps_login_enabled = ?",$condition["google_apps_login_enabled"]);
        }
        if(isset($condition["money_in_emails_format"]) && $condition["money_in_emails_format"] != ""){
            $select->where("money_in_emails_format = ?",$condition["money_in_emails_format"]);
        }
        if(isset($condition["money_with_currency_in_emails_format"]) && $condition["money_with_currency_in_emails_format"] != ""){
            $select->where("money_with_currency_in_emails_format = ?",$condition["money_with_currency_in_emails_format"]);
        }
        if(isset($condition["eligible_for_payments"]) && $condition["eligible_for_payments"] != ""){
            $select->where("eligible_for_payments = ?",$condition["eligible_for_payments"]);
        }
        if(isset($condition["requires_extra_payments_agreement"]) && $condition["requires_extra_payments_agreement"] != ""){
            $select->where("requires_extra_payments_agreement = ?",$condition["requires_extra_payments_agreement"]);
        }
        if(isset($condition["password_enabled"]) && $condition["password_enabled"] != ""){
            $select->where("password_enabled = ?",$condition["password_enabled"]);
        }
        if(isset($condition["has_storefront"]) && $condition["has_storefront"] != ""){
            $select->where("has_storefront = ?",$condition["has_storefront"]);
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