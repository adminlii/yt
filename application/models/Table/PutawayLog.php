<?php
class Table_PutawayLog
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PutawayLog();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PutawayLog();
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
    public function update($row, $value, $field = "pl_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pl_id")
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
    public function getByField($value, $field = 'pl_id', $colums = "*")
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
        
        if(isset($condition["putaway_id"]) && $condition["putaway_id"] != ""){
            $select->where("putaway_id = ?",$condition["putaway_id"]);
        }
        if(isset($condition["putaway_code"]) && $condition["putaway_code"] != ""){
            $select->where("putaway_code = ?",$condition["putaway_code"]);
        }
        if(isset($condition["pl_type"]) && $condition["pl_type"] != ""){
            $select->where("pl_type = ?",$condition["pl_type"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["pl_status_from"]) && $condition["pl_status_from"] != ""){
            $select->where("pl_status_from = ?",$condition["pl_status_from"]);
        }
        if(isset($condition["pl_status_to"]) && $condition["pl_status_to"] != ""){
            $select->where("pl_status_to = ?",$condition["pl_status_to"]);
        }
        if(isset($condition["pl_note"]) && $condition["pl_note"] != ""){
            $select->where("pl_note = ?",$condition["pl_note"]);
        }
        if(isset($condition["pl_ip"]) && $condition["pl_ip"] != ""){
            $select->where("pl_ip = ?",$condition["pl_ip"]);
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