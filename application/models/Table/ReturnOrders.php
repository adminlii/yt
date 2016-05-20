<?php
class Table_ReturnOrders
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReturnOrders();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReturnOrders();
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
    public function update($row, $value, $field = "ro_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ro_id")
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
    public function getByField($value, $field = 'ro_id', $colums = "*")
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
        if(isset($condition["refrence_no_platform"]) && $condition["refrence_no_platform"] != ""){
            $select->where("refrence_no_platform = ?",$condition["refrence_no_platform"]);
        }
        if(isset($condition["refrence_no_warehouse"]) && $condition["refrence_no_warehouse"] != ""){
            $select->where("refrence_no_warehouse = ?",$condition["refrence_no_warehouse"]);
        }
        if(isset($condition["refrence_no"]) && $condition["refrence_no"] != ""){
            $select->where("refrence_no = ?",$condition["refrence_no"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["ro_code"]) && $condition["ro_code"] != ""){
            $select->where("ro_code = ?",$condition["ro_code"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["creater"]) && $condition["creater"] != ""){
            $select->where("creater = ?",$condition["creater"]);
        }
        if(isset($condition["verifier"]) && $condition["verifier"] != ""){
            $select->where("verifier = ?",$condition["verifier"]);
        }
        if(isset($condition["expected_date"]) && $condition["expected_date"] != ""){
            $select->where("expected_date = ?",$condition["expected_date"]);
        }
        if(isset($condition["receiving_exception"]) && $condition["receiving_exception"] != ""){
            $select->where("receiving_exception = ?",$condition["receiving_exception"]);
        }
        if(isset($condition["ro_is_all"]) && $condition["ro_is_all"] != ""){
            $select->where("ro_is_all = ?",$condition["ro_is_all"]);
        }
        if(isset($condition["ro_type"]) && $condition["ro_type"] != ""){
            $select->where("ro_type = ?",$condition["ro_type"]);
        }
        if(isset($condition["ro_status"]) && $condition["ro_status"] != ""){
            $select->where("ro_status = ?",$condition["ro_status"]);
        }
        if(isset($condition["ro_sync_status"]) && $condition["ro_sync_status"] != ""){
            $select->where("ro_sync_status = ?",$condition["ro_sync_status"]);
        }
        if(isset($condition["ro_process_type"]) && $condition["ro_process_type"] != ""){
            $select->where("ro_process_type = ?",$condition["ro_process_type"]);
        }
        if(isset($condition["ro_create_type"]) && $condition["ro_create_type"] != ""){
            $select->where("ro_create_type = ?",$condition["ro_create_type"]);
        }
        if(isset($condition["ro_desc"]) && $condition["ro_desc"] != ""){
            $select->where("ro_desc = ?",$condition["ro_desc"]);
        }
        if(isset($condition["ro_note"]) && $condition["ro_note"] != ""){
            $select->where("ro_note = ?",$condition["ro_note"]);
        }
        if(isset($condition["ro_add_time"]) && $condition["ro_add_time"] != ""){
            $select->where("ro_add_time = ?",$condition["ro_add_time"]);
        }
        if(isset($condition["ro_confirm_time"]) && $condition["ro_confirm_time"] != ""){
            $select->where("ro_confirm_time = ?",$condition["ro_confirm_time"]);
        }
        if(isset($condition["ro_update_time"]) && $condition["ro_update_time"] != ""){
            $select->where("ro_update_time = ?",$condition["ro_update_time"]);
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