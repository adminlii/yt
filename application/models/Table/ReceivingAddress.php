<?php
class Table_ReceivingAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingAddress();
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
    public function update($row, $value, $field = "rd_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rd_id")
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
    public function getByField($value, $field = 'rd_id', $colums = "*")
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
        if(isset($condition["region_0"]) && $condition["region_0"] != ""){
            $select->where("region_0 = ?",$condition["region_0"]);
        }
        if(isset($condition["region_1"]) && $condition["region_1"] != ""){
            $select->where("region_1 = ?",$condition["region_1"]);
        }
        if(isset($condition["region_2"]) && $condition["region_2"] != ""){
            $select->where("region_2 = ?",$condition["region_2"]);
        }
        if(isset($condition["street"]) && $condition["street"] != ""){
            $select->where("street = ?",$condition["street"]);
        }
        if(isset($condition["contacter"]) && $condition["contacter"] != ""){
            $select->where("contacter = ?",$condition["contacter"]);
        }
        if(isset($condition["contact_phone"]) && $condition["contact_phone"] != ""){
            $select->where("contact_phone = ?",$condition["contact_phone"]);
        }
        if(isset($condition["is_default"]) && $condition["is_default"] != ""){
            $select->where("is_default = ?",$condition["is_default"]);
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