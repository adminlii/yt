<?php
class Table_ReceivingAbnormal
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingAbnormal();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingAbnormal();
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
    public function update($row, $value, $field = "ra_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ra_id")
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
    public function getByField($value, $field = 'ra_id', $colums = "*")
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
        if(isset($condition["ra_code"]) && $condition["ra_code"] != ""){
            $select->where("ra_code = ?",$condition["ra_code"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["po_code"]) && $condition["po_code"] != ""){
            $select->where("po_code = ?",$condition["po_code"]);
        }
        if(isset($condition["ref_no"]) && $condition["ref_no"] != ""){
            $select->where("ref_no = ?",$condition["ref_no"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["ra_type"]) && $condition["ra_type"] != ""){
            $select->where("ra_type = ?",$condition["ra_type"]);
        }
        if(isset($condition["ra_status"]) && $condition["ra_status"] != ""){
            $select->where("ra_status = ?",$condition["ra_status"]);
        }
        if(isset($condition["ra_creater"]) && $condition["ra_creater"] != ""){
            $select->where("ra_creater = ?",$condition["ra_creater"]);
        }
        if(isset($condition["ra_operator"]) && $condition["ra_operator"] != ""){
            $select->where("ra_operator = ?",$condition["ra_operator"]);
        }
        if(isset($condition["ra_desc"]) && $condition["ra_desc"] != ""){
            $select->where("ra_desc = ?",$condition["ra_desc"]);
        }
        if(isset($condition["ra_add_time"]) && $condition["ra_add_time"] != ""){
            $select->where("ra_add_time = ?",$condition["ra_add_time"]);
        }
        if(isset($condition["ra_update_time"]) && $condition["ra_update_time"] != ""){
            $select->where("ra_update_time = ?",$condition["ra_update_time"]);
        }
        if(isset($condition["ra_sync_status"]) && $condition["ra_sync_status"] != ""){
            $select->where("ra_sync_status = ?",$condition["ra_sync_status"]);
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