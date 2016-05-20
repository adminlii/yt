<?php
class Table_MagentoOrderShippingAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MagentoOrderShippingAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MagentoOrderShippingAddress();
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
    public function update($row, $value, $field = "mosa_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "mosa_id")
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
    public function getByField($value, $field = 'mosa_id', $colums = "*")
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
        if(isset($condition["parent_id"]) && $condition["parent_id"] != ""){
            $select->where("parent_id = ?",$condition["parent_id"]);
        }
        if(isset($condition["address_type"]) && $condition["address_type"] != ""){
            $select->where("address_type = ?",$condition["address_type"]);
        }
        if(isset($condition["firstname"]) && $condition["firstname"] != ""){
            $select->where("firstname = ?",$condition["firstname"]);
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
        if(isset($condition["region"]) && $condition["region"] != ""){
            $select->where("region = ?",$condition["region"]);
        }
        if(isset($condition["postcode"]) && $condition["postcode"] != ""){
            $select->where("postcode = ?",$condition["postcode"]);
        }
        if(isset($condition["country_id"]) && $condition["country_id"] != ""){
            $select->where("country_id = ?",$condition["country_id"]);
        }
        if(isset($condition["telephone"]) && $condition["telephone"] != ""){
            $select->where("telephone = ?",$condition["telephone"]);
        }
        if(isset($condition["region_id"]) && $condition["region_id"] != ""){
            $select->where("region_id = ?",$condition["region_id"]);
        }
        if(isset($condition["address_id"]) && $condition["address_id"] != ""){
            $select->where("address_id = ?",$condition["address_id"]);
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