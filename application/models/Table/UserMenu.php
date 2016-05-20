<?php
class Table_UserMenu
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_UserMenu();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_UserMenu();
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
    public function update($row, $value, $field = "um_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "um_id")
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
    public function getByField($value, $field = 'um_id', $colums = "*")
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
        
        if(isset($condition["um_title"]) && $condition["um_title"] != ""){
            $select->where("um_title = ?",$condition["um_title"]);
        }
        if(isset($condition["um_title_en"]) && $condition["um_title_en"] != ""){
            $select->where("um_title_en = ?",$condition["um_title_en"]);
        }
        if(isset($condition["um_url"]) && $condition["um_url"] != ""){
            $select->where("um_url = ?",$condition["um_url"]);
        }
        if(isset($condition["um_css"]) && $condition["um_css"] != ""){
            $select->where("um_css = ?",$condition["um_css"]);
        }
        if(isset($condition["um_color"]) && $condition["um_color"] != ""){
            $select->where("um_color = ?",$condition["um_color"]);
        }
        if(isset($condition["um_sort"]) && $condition["um_sort"] != ""){
            $select->where("um_sort = ?",$condition["um_sort"]);
        }
        if(isset($condition["parent_id"]) && $condition["parent_id"] !== ""){
            $select->where("parent_id = ?",$condition["parent_id"]);
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