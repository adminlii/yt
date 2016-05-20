<?php
class Table_ShopifyCustomer
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyCustomer();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyCustomer();
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
        
        if(isset($condition["accepts_marketing"]) && $condition["accepts_marketing"] != ""){
            $select->where("accepts_marketing = ?",$condition["accepts_marketing"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["email"]) && $condition["email"] != ""){
            $select->where("email = ?",$condition["email"]);
        }
        if(isset($condition["first_name"]) && $condition["first_name"] != ""){
            $select->where("first_name = ?",$condition["first_name"]);
        }
        if(isset($condition["last_name"]) && $condition["last_name"] != ""){
            $select->where("last_name = ?",$condition["last_name"]);
        }
        if(isset($condition["last_order_id"]) && $condition["last_order_id"] != ""){
            $select->where("last_order_id = ?",$condition["last_order_id"]);
        }
        if(isset($condition["multipass_identifier"]) && $condition["multipass_identifier"] != ""){
            $select->where("multipass_identifier = ?",$condition["multipass_identifier"]);
        }
        if(isset($condition["note"]) && $condition["note"] != ""){
            $select->where("note = ?",$condition["note"]);
        }
        if(isset($condition["orders_count"]) && $condition["orders_count"] != ""){
            $select->where("orders_count = ?",$condition["orders_count"]);
        }
        if(isset($condition["state"]) && $condition["state"] != ""){
            $select->where("state = ?",$condition["state"]);
        }
        if(isset($condition["total_spent"]) && $condition["total_spent"] != ""){
            $select->where("total_spent = ?",$condition["total_spent"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
        }
        if(isset($condition["verified_email"]) && $condition["verified_email"] != ""){
            $select->where("verified_email = ?",$condition["verified_email"]);
        }
        if(isset($condition["tags"]) && $condition["tags"] != ""){
            $select->where("tags = ?",$condition["tags"]);
        }
        if(isset($condition["last_order_name"]) && $condition["last_order_name"] != ""){
            $select->where("last_order_name = ?",$condition["last_order_name"]);
        }
        if(isset($condition["default_address_id"]) && $condition["default_address_id"] != ""){
            $select->where("default_address_id = ?",$condition["default_address_id"]);
        }
        if(isset($condition["default_address_address1"]) && $condition["default_address_address1"] != ""){
            $select->where("default_address_address1 = ?",$condition["default_address_address1"]);
        }
        if(isset($condition["default_address_address2"]) && $condition["default_address_address2"] != ""){
            $select->where("default_address_address2 = ?",$condition["default_address_address2"]);
        }
        if(isset($condition["default_address_city"]) && $condition["default_address_city"] != ""){
            $select->where("default_address_city = ?",$condition["default_address_city"]);
        }
        if(isset($condition["default_address_company"]) && $condition["default_address_company"] != ""){
            $select->where("default_address_company = ?",$condition["default_address_company"]);
        }
        if(isset($condition["default_address_country"]) && $condition["default_address_country"] != ""){
            $select->where("default_address_country = ?",$condition["default_address_country"]);
        }
        if(isset($condition["default_address_first_name"]) && $condition["default_address_first_name"] != ""){
            $select->where("default_address_first_name = ?",$condition["default_address_first_name"]);
        }
        if(isset($condition["default_address_last_name"]) && $condition["default_address_last_name"] != ""){
            $select->where("default_address_last_name = ?",$condition["default_address_last_name"]);
        }
        if(isset($condition["default_address_phone"]) && $condition["default_address_phone"] != ""){
            $select->where("default_address_phone = ?",$condition["default_address_phone"]);
        }
        if(isset($condition["default_address_province"]) && $condition["default_address_province"] != ""){
            $select->where("default_address_province = ?",$condition["default_address_province"]);
        }
        if(isset($condition["default_address_zip"]) && $condition["default_address_zip"] != ""){
            $select->where("default_address_zip = ?",$condition["default_address_zip"]);
        }
        if(isset($condition["default_address_name"]) && $condition["default_address_name"] != ""){
            $select->where("default_address_name = ?",$condition["default_address_name"]);
        }
        if(isset($condition["default_address_province_code"]) && $condition["default_address_province_code"] != ""){
            $select->where("default_address_province_code = ?",$condition["default_address_province_code"]);
        }
        if(isset($condition["default_address_country_code"]) && $condition["default_address_country_code"] != ""){
            $select->where("default_address_country_code = ?",$condition["default_address_country_code"]);
        }
        if(isset($condition["default_address_country_name"]) && $condition["default_address_country_name"] != ""){
            $select->where("default_address_country_name = ?",$condition["default_address_country_name"]);
        }
        if(isset($condition["default_address_default"]) && $condition["default_address_default"] != ""){
            $select->where("default_address_default = ?",$condition["default_address_default"]);
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