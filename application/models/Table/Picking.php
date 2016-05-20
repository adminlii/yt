<?php
class Table_Picking
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Picking();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Picking();
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
    public function update($row, $value, $field = "picking_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "picking_id")
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
    public function getByField($value, $field = 'picking_id', $colums = "*")
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
        
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["picking_code"]) && $condition["picking_code"] != ""){
            $select->where("picking_code = ?",$condition["picking_code"]);
        }
        if(isset($condition["picker_id"]) && $condition["picker_id"] != ""){
            $select->where("picker_id = ?",$condition["picker_id"]);
        }
        if(isset($condition["creater_id"]) && $condition["creater_id"] != ""){
            $select->where("creater_id = ?",$condition["creater_id"]);
        }
        if(isset($condition["picking_order_cnt"]) && $condition["picking_order_cnt"] != ""){
            $select->where("picking_order_cnt = ?",$condition["picking_order_cnt"]);
        }
        if(isset($condition["picking_lc_cnt"]) && $condition["picking_lc_cnt"] != ""){
            $select->where("picking_lc_cnt = ?",$condition["picking_lc_cnt"]);
        }
        if(isset($condition["picking_item_cnt"]) && $condition["picking_item_cnt"] != ""){
            $select->where("picking_item_cnt = ?",$condition["picking_item_cnt"]);
        }
        if(isset($condition["picking_status"]) && $condition["picking_status"] != ""){
            $select->where("picking_status = ?",$condition["picking_status"]);
        }
        if(isset($condition["is_assign"]) && $condition["is_assign"] != ""){
            $select->where("is_assign = ?",$condition["is_assign"]);
        }
        if(isset($condition["picking_type"]) && $condition["picking_type"] != ""){
            $select->where("picking_type = ?",$condition["picking_type"]);
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