<?php
class Table_OrderProcessing
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderProcessing();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderProcessing();
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
    public function update($row, $value, $field = "ops_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ops_id")
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
    public function getByField($value, $field = 'ops_id', $colums = "*")
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
        
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["shipper_hawbcode"]) && $condition["shipper_hawbcode"] != ""){
            $select->where("shipper_hawbcode = ?",$condition["shipper_hawbcode"]);
        }
        if(isset($condition["ops_count"]) && $condition["ops_count"] != ""){
            $select->where("ops_count = ?",$condition["ops_count"]);
        }
        if(isset($condition["server_channelid"]) && $condition["server_channelid"] != ""){
            $select->where("server_channelid = ?",$condition["server_channelid"]);
        }
        if(isset($condition["formal_code"]) && $condition["formal_code"] != ""){
            $select->where("formal_code = ?",$condition["formal_code"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["ops_status"]) && $condition["ops_status"] != ""){
            $select->where("ops_status = ?",$condition["ops_status"]);
        }
        if(isset($condition["ops_syncing_status"]) && $condition["ops_syncing_status"] != ""){
            $select->where("ops_syncing_status = ?",$condition["ops_syncing_status"]);
        }
        if(isset($condition["ops_note"]) && $condition["ops_note"] != ""){
            $select->where("ops_note = ?",$condition["ops_note"]);
        }
        if(isset($condition["ops_create_date"]) && $condition["ops_create_date"] != ""){
            $select->where("ops_create_date = ?",$condition["ops_create_date"]);
        }
        if(isset($condition["ops_update_time"]) && $condition["ops_update_time"] != ""){
            $select->where("ops_update_time = ?",$condition["ops_update_time"]);
        }
        if(isset($condition["release_status"]) && $condition["release_status"] != ""){
            $select->where("release_status = ?",$condition["release_status"]);
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