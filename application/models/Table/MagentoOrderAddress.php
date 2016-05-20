<?php
class Table_MagentoOrderAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MagentoOrderAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MagentoOrderAddress();
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
    public function update($row, $value, $field = "moa_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "moa_id")
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
    public function getByField($value, $field = 'moa_id', $colums = "*")
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
        
        if(isset($condition["mo_id"]) && $condition["mo_id"] != ""){
            $select->where("mo_id = ?",$condition["mo_id"]);
        }
        if(isset($condition["entity_id"]) && $condition["entity_id"] != ""){
            $select->where("entity_id = ?",$condition["entity_id"]);
        }
        if(isset($condition["parent_id"]) && $condition["parent_id"] != ""){
            $select->where("parent_id = ?",$condition["parent_id"]);
        }
        if(isset($condition["customer_address_id"]) && $condition["customer_address_id"] != ""){
            $select->where("customer_address_id = ?",$condition["customer_address_id"]);
        }
        if(isset($condition["quote_address_id"]) && $condition["quote_address_id"] != ""){
            $select->where("quote_address_id = ?",$condition["quote_address_id"]);
        }
        if(isset($condition["region_id"]) && $condition["region_id"] != ""){
            $select->where("region_id = ?",$condition["region_id"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["fax"]) && $condition["fax"] != ""){
            $select->where("fax = ?",$condition["fax"]);
        }
        if(isset($condition["region"]) && $condition["region"] != ""){
            $select->where("region = ?",$condition["region"]);
        }
        if(isset($condition["postcode"]) && $condition["postcode"] != ""){
            $select->where("postcode = ?",$condition["postcode"]);
        }
        if(isset($condition["lastname"]) && $condition["lastname"] != ""){
            $select->where("lastname = ?",$condition["lastname"]);
        }
        if(isset($condition["street"]) && $condition["street"] != ""){
            $select->where("street = ?",$condition["street"]);
        }
        if(isset($condition["city"]) && $condition["city"] != ""){
            $select->where("city = ?",$condition["city"]);
        }
        if(isset($condition["email"]) && $condition["email"] != ""){
            $select->where("email = ?",$condition["email"]);
        }
        if(isset($condition["telephone"]) && $condition["telephone"] != ""){
            $select->where("telephone = ?",$condition["telephone"]);
        }
        if(isset($condition["country_id"]) && $condition["country_id"] != ""){
            $select->where("country_id = ?",$condition["country_id"]);
        }
        if(isset($condition["firstname"]) && $condition["firstname"] != ""){
            $select->where("firstname = ?",$condition["firstname"]);
        }
        if(isset($condition["address_type"]) && $condition["address_type"] != ""){
            $select->where("address_type = ?",$condition["address_type"]);
        }
        if(isset($condition["prefix"]) && $condition["prefix"] != ""){
            $select->where("prefix = ?",$condition["prefix"]);
        }
        if(isset($condition["middlename"]) && $condition["middlename"] != ""){
            $select->where("middlename = ?",$condition["middlename"]);
        }
        if(isset($condition["suffix"]) && $condition["suffix"] != ""){
            $select->where("suffix = ?",$condition["suffix"]);
        }
        if(isset($condition["company"]) && $condition["company"] != ""){
            $select->where("company = ?",$condition["company"]);
        }
        if(isset($condition["vat_id"]) && $condition["vat_id"] != ""){
            $select->where("vat_id = ?",$condition["vat_id"]);
        }
        if(isset($condition["vat_is_valid"]) && $condition["vat_is_valid"] != ""){
            $select->where("vat_is_valid = ?",$condition["vat_is_valid"]);
        }
        if(isset($condition["vat_request_id"]) && $condition["vat_request_id"] != ""){
            $select->where("vat_request_id = ?",$condition["vat_request_id"]);
        }
        if(isset($condition["vat_request_date"]) && $condition["vat_request_date"] != ""){
            $select->where("vat_request_date = ?",$condition["vat_request_date"]);
        }
        if(isset($condition["vat_request_success"]) && $condition["vat_request_success"] != ""){
            $select->where("vat_request_success = ?",$condition["vat_request_success"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["create_time_sys"]) && $condition["create_time_sys"] != ""){
            $select->where("create_time_sys = ?",$condition["create_time_sys"]);
        }
        if(isset($condition["update_time_sys"]) && $condition["update_time_sys"] != ""){
            $select->where("update_time_sys = ?",$condition["update_time_sys"]);
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