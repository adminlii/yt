<?php
class Table_WarehouseOperationMode
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_WarehouseOperationMode();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_WarehouseOperationMode();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        $row['wom_add_time'] = date('Y-m-d H:i:s');
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "wom_id")
    {
        $row['wom_update_time'] = date('Y-m-d H:i:s');
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "wom_id")
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
    public function getByField($value, $field = 'wom_id', $colums = "*")
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
        
        if(isset($condition["wom_name"]) && $condition["wom_name"] != ""){
            $select->where("wom_name = ?",$condition["wom_name"]);
        }
        if(isset($condition["wom_desc"]) && $condition["wom_desc"] != ""){
            $select->where("wom_desc = ?",$condition["wom_desc"]);
        }
        if(isset($condition["wom_status"]) && $condition["wom_status"] != ""){
            $select->where("wom_status = ?",$condition["wom_status"]);
        }
        if(isset($condition["is_display"]) && $condition["is_display"] != ""){
            $select->where("is_display = ?",$condition["is_display"]);
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

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getJoinInnerWarehouseWomMapByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinInner('warehouse_wom_map as wwm', 'wwm.wom_id='.$table.'.wom_id',array('warehouse_id','wwm_id'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        if(isset($condition["wom_id"]) && $condition["wom_id"] != ""){
            $select->where($table.".wom_id = ?",$condition["wom_id"]);
        }
        if(isset($condition["wom_type"]) && $condition["wom_type"] != ""){
            $select->where("wom_type = ?",$condition["wom_type"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("wwm.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["wom_status"]) && $condition["wom_status"] != ""){
            $select->where("wom_status = ?",$condition["wom_status"]);
        }
        if(isset($condition["is_display"]) && $condition["is_display"] != ""){
            $select->where("is_display = ?",$condition["is_display"]);
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