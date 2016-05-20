<?php
class Table_OrderLabel
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderLabel();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderLabel();
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
    public function update($row, $value, $field = "ol_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ol_id")
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
    public function getByField($value, $field = 'ol_id', $colums = "*")
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
        
        if(isset($condition["order_code"]) && $condition["order_code"] != ""){
            $select->where("order_code = ?",$condition["order_code"]);
        }
        if(isset($condition["org_path"]) && $condition["org_path"] != ""){
            $select->where("org_path = ?",$condition["org_path"]);
        }
        if(isset($condition["path"]) && $condition["path"] != ""){
            $select->where("path = ?",$condition["path"]);
        }
        if(isset($condition["sm_code"]) && $condition["sm_code"] != ""){
            $select->where("sm_code = ?",$condition["sm_code"]);
        }
        if(isset($condition["ol_file_type"]) && $condition["ol_file_type"] != ""){
            $select->where("ol_file_type = ?",$condition["ol_file_type"]);
        }
        if(isset($condition["ol_status"]) && $condition["ol_status"] != ""){
            $select->where("ol_status = ?",$condition["ol_status"]);
        }
        if(isset($condition["ol_run_qty"]) && $condition["ol_run_qty"] != ""){
            $select->where("ol_run_qty = ?",$condition["ol_run_qty"]);
        }
        if(isset($condition["ol_label_url"]) && $condition["ol_label_url"] != ""){
            $select->where("ol_label_url = ?",$condition["ol_label_url"]);
        }
        if(isset($condition["ol_note"]) && $condition["ol_note"] != ""){
            $select->where("ol_note = ?",$condition["ol_note"]);
        }
        if(isset($condition["ol_create_date"]) && $condition["ol_create_date"] != ""){
            $select->where("ol_create_date = ?",$condition["ol_create_date"]);
        }
        if(isset($condition["ol_update_time"]) && $condition["ol_update_time"] != ""){
            $select->where("ol_update_time = ?",$condition["ol_update_time"]);
        }
        if (isset($condition["ol_status_arr"]) && is_array($condition["ol_status_arr"]) && !empty($condition["ol_status_arr"])) {
        	$select->where("ol_status in(?)", $condition["ol_status_arr"]);
        }
        if (isset($condition["ol_run_qty_lt"]) && $condition["ol_run_qty_lt"] != "") {
        	$select->where("ol_run_qty <= ?", $condition["ol_run_qty_lt"]);
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