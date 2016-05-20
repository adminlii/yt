<?php
class Table_ShippingAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShippingAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShippingAddress();
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
    public function update($row, $value, $field = "ShippingAddress_Id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ShippingAddress_Id")
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
    public function getByField($value, $field = 'ShippingAddress_Id', $colums = "*")
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
        
        if(isset($condition["Name"]) && $condition["Name"] != ""){
            $select->where("Name = ?",$condition["Name"]);
        }
        if(isset($condition["Street1"]) && $condition["Street1"] != ""){
            $select->where("Street1 = ?",$condition["Street1"]);
        }
        if(isset($condition["Street2"]) && $condition["Street2"] != ""){
            $select->where("Street2 = ?",$condition["Street2"]);
        }
        if(isset($condition["CityName"]) && $condition["CityName"] != ""){
            $select->where("CityName = ?",$condition["CityName"]);
        }
        if(isset($condition["StateOrProvince"]) && $condition["StateOrProvince"] != ""){
            $select->where("StateOrProvince = ?",$condition["StateOrProvince"]);
        }
        if(isset($condition["Country"]) && $condition["Country"] != ""){
            $select->where("Country = ?",$condition["Country"]);
        }
        if(isset($condition["CountryName"]) && $condition["CountryName"] != ""){
            $select->where("CountryName = ?",$condition["CountryName"]);
        }
        if(isset($condition["Phone"]) && $condition["Phone"] != ""){
            $select->where("Phone = ?",$condition["Phone"]);
        }
        if(isset($condition["PostalCode"]) && $condition["PostalCode"] != ""){
            $select->where("PostalCode = ?",$condition["PostalCode"]);
        }
        if(isset($condition["AddressID"]) && $condition["AddressID"] != ""){
            $select->where("AddressID = ?",$condition["AddressID"]);
        }
        if(isset($condition["AddressOwner"]) && $condition["AddressOwner"] != ""){
            $select->where("AddressOwner = ?",$condition["AddressOwner"]);
        }
        if(isset($condition["ExternalAddressID"]) && $condition["ExternalAddressID"] != ""){
            $select->where("ExternalAddressID = ?",$condition["ExternalAddressID"]);
        }
        if(isset($condition["OrderID"]) && $condition["OrderID"] != ""){
            $select->where("OrderID = ?",$condition["OrderID"]);
        }
        if(isset($condition["Plat_code"]) && $condition["Plat_code"] != ""){
            $select->where("Plat_code = ?",$condition["Plat_code"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
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