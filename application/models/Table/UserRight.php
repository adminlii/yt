<?php
class Table_UserRight
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_UserRight();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_UserRight();
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
    public function update($row, $value, $field = "ur_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ur_id")
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
    public function getByField($value, $field = 'ur_id', $colums = "*")
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
        
        if(isset($condition["um_id"]) && $condition["um_id"] != ""){
            $select->where("um_id = ?",$condition["um_id"]);
        }
        if(isset($condition["ur_name"]) && $condition["ur_name"] != ""){
            $select->where("ur_name = ?",$condition["ur_name"]);
        }
        if(isset($condition["ur_name_like"]) && $condition["ur_name_like"] != ""){
            $select->where("ur_name like ?","%{$condition["ur_name_like"]}%");
        }
        if(isset($condition["ur_name_en"]) && $condition["ur_name_en"] != ""){
            $select->where("ur_name_en = ?",$condition["ur_name_en"]);
        }
        if(isset($condition["ur_description"]) && $condition["ur_description"] != ""){
            $select->where("ur_description = ?",$condition["ur_description"]);
        }
        if(isset($condition["ur_url"]) && $condition["ur_url"] != ""){
            $select->where("ur_url = ?",$condition["ur_url"]);
        }
        if(isset($condition["ur_type"]) && $condition["ur_type"] != ""){
            $select->where("ur_type = ?",$condition["ur_type"]);
        }
        if(isset($condition["ur_module"]) && $condition["ur_module"] != ""){
            $select->where("ur_module = ?",$condition["ur_module"]);
        }
        if(isset($condition["ur_icon"]) && $condition["ur_icon"] != ""){
            $select->where("ur_icon = ?",$condition["ur_icon"]);
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