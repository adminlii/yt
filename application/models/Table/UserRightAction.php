<?php
class Table_UserRightAction
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_UserRightAction();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_UserRightAction();
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
    public function update($row, $value, $field = "ura_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ura_id")
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
    public function getByField($value, $field = 'ura_id', $colums = "*")
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
        
        if(isset($condition["ura_title"]) && $condition["ura_title"] != ""){
            $select->where("ura_title = ?",$condition["ura_title"]);
        }
        if(isset($condition["ura_title_en"]) && $condition["ura_title_en"] != ""){
            $select->where("ura_title_en = ?",$condition["ura_title_en"]);
        }
        if(isset($condition["ura_title_alias"]) && $condition["ura_title_alias"] != ""){
            $select->where("ura_title_alias = ?",$condition["ura_title_alias"]);
        }
        if(isset($condition["ura_status"]) && $condition["ura_status"] != ""){
            $select->where("ura_status = ?",$condition["ura_status"]);
        }
        if(isset($condition["ura_display"]) && $condition["ura_display"] != ""){
            $select->where("ura_display = ?",$condition["ura_display"]);
        }
        if(isset($condition["ura_module"]) && $condition["ura_module"] != ""){
            $select->where("ura_module = ?",$condition["ura_module"]);
        }
        if(isset($condition["ura_module_gt"]) && $condition["ura_module_gt"] != ""){
            $select->where("ura_module != ?",$condition["ura_module_gt"]);
        }
        if(isset($condition["ura_controller"]) && $condition["ura_controller"] != ""){
            $select->where("ura_controller = ?",$condition["ura_controller"]);
        }
        if(isset($condition["ura_action"]) && $condition["ura_action"] != ""){
            $select->where("ura_action = ?",$condition["ura_action"]);
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

    public function getModule()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, "*");
        $select->group("ura_module");
        return $this->_table->getAdapter()->fetchAll($select);
    }
}