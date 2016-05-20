<?php
class Table_UserAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_UserAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_UserAddress();
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
    public function update($row, $value, $field = "address_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "address_id")
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
    public function getByField($value, $field = 'address_id', $colums = "*")
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
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["address_name"]) && $condition["address_name"] != ""){
            $select->where("address_name = ?",$condition["address_name"]);
        }
        if(isset($condition["state"]) && $condition["state"] != ""){
            $select->where("state = ?",$condition["state"]);
        }
        if(isset($condition["city"]) && $condition["city"] != ""){
            $select->where("city = ?",$condition["city"]);
        }
        if(isset($condition["district"]) && $condition["district"] != ""){
            $select->where("district = ?",$condition["district"]);
        }
        if(isset($condition["postal_code"]) && $condition["postal_code"] != ""){
            $select->where("postal_code = ?",$condition["postal_code"]);
        }
        if(isset($condition["street"]) && $condition["street"] != ""){
            $select->where("street = ?",$condition["street"]);
        }
        if(isset($condition["contact"]) && $condition["contact"] != ""){
            $select->where("contact = ?",$condition["contact"]);
        }
        if(isset($condition["phone"]) && $condition["phone"] != ""){
            $select->where("phone = ?",$condition["phone"]);
        }
        if(isset($condition["is_default"]) && $condition["is_default"] != ""){
            $select->where("is_default = ?",$condition["is_default"]);
        }
        if(isset($condition["pickup_og_id"]) && $condition["pickup_og_id"] != ""){
            $select->where("pickup_og_id = ?",$condition["pickup_og_id"]);
        }
        if(isset($condition["create_date_sys"]) && $condition["create_date_sys"] != ""){
            $select->where("create_date_sys = ?",$condition["create_date_sys"]);
        }
        if(isset($condition["modify_date_sys"]) && $condition["modify_date_sys"] != ""){
            $select->where("modify_date_sys = ?",$condition["modify_date_sys"]);
        }
        if(isset($condition["is_modify"]) && $condition["is_modify"] != ""){
            $select->where("is_modify = ?",$condition["is_modify"]);
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