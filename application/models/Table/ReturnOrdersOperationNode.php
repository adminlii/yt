<?php
class Table_ReturnOrdersOperationNode
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReturnOrdersOperationNode();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReturnOrdersOperationNode();
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
    public function update($row, $value, $field = "roon_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "roon_id")
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
    public function getByField($value, $field = 'roon_id', $colums = "*")
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
        
        if(isset($condition["ro_code"]) && $condition["ro_code"] != ""){
            $select->where("ro_code = ?",$condition["ro_code"]);
        }
        if(isset($condition["ot_code"]) && $condition["ot_code"] != ""){
            $select->where("ot_code = ?",$condition["ot_code"]);
        }
        if(isset($condition["ot_attribute"]) && $condition["ot_attribute"] != ""){
            $select->where("ot_attribute = ?",$condition["ot_attribute"]);
        }
        if(isset($condition["roon_note"]) && $condition["roon_note"] != ""){
            $select->where("roon_note = ?",$condition["roon_note"]);
        }
        if(isset($condition["roon_add_time"]) && $condition["roon_add_time"] != ""){
            $select->where("roon_add_time = ?",$condition["roon_add_time"]);
        }
        if(isset($condition["ip"]) && $condition["ip"] != ""){
            $select->where("ip = ?",$condition["ip"]);
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