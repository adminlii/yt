<?php
class Table_SearchFilter
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_SearchFilter();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_SearchFilter();
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
    public function update($row, $value, $field = "sf_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "sf_id")
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
    public function getByField($value, $field = 'sf_id', $colums = "*")
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
        if(isset($condition["parent_id"]) && $condition["parent_id"] != ""){
            $select->where("parent_id = ?",$condition["parent_id"]);
        }
        if(isset($condition["search_label"]) && $condition["search_label"] != ""){
            $select->where("search_label = ?",$condition["search_label"]);
        }
        if(isset($condition["search_value"]) && $condition["search_value"] != ""){
            $select->where("search_value = ?",$condition["search_value"]);
        }
        if(isset($condition["search_sort"]) && $condition["search_sort"] != ""){
            $select->where("search_sort = ?",$condition["search_sort"]);
        }
        if(isset($condition["filter_action_id"]) && $condition["filter_action_id"] != ""){
            $select->where("filter_action_id = ?",$condition["filter_action_id"]);
        }
        if(isset($condition["search_tips"]) && $condition["search_tips"] != ""){
            $select->where("search_tips = ?",$condition["search_tips"]);
        }
        if(isset($condition["search_input_id"]) && $condition["search_input_id"] != ""){
            $select->where("search_input_id = ?",$condition["search_input_id"]);
        }
        if(isset($condition["sf_desc"]) && $condition["sf_desc"] != ""){
            $select->where("sf_desc = ?",$condition["sf_desc"]);
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


    public function getLeftJoinByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('user_right', 'user_right.ur_id='.$table.'.filter_action_id',array('user_right.ur_name as urName'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["parent_id"]) && $condition["parent_id"] != ""){
            $select->where("parent_id = ?",$condition["parent_id"]);
        }
        if(isset($condition["search_label"]) && $condition["search_label"] != ""){
            $select->where("search_label = ?",$condition["search_label"]);
        }
        if(isset($condition["search_value"]) && $condition["search_value"] != ""){
            $select->where("search_value = ?",$condition["search_value"]);
        }
        if(isset($condition["search_sort"]) && $condition["search_sort"] != ""){
            $select->where("search_sort = ?",$condition["search_sort"]);
        }
        if(isset($condition["filter_action_id"]) && $condition["filter_action_id"] != ""){
            $select->where("filter_action_id = ?",$condition["filter_action_id"]);
        }
        if(isset($condition["search_tips"]) && $condition["search_tips"] != ""){
            $select->where("search_tips = ?",$condition["search_tips"]);
        }
        if(isset($condition["search_input_id"]) && $condition["search_input_id"] != ""){
            $select->where("search_input_id = ?",$condition["search_input_id"]);
        }
        if(isset($condition["sf_desc"]) && $condition["sf_desc"] != ""){
            $select->where("sf_desc = ?",$condition["sf_desc"]);
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