<?php
class Table_BulletinBoard
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_BulletinBoard();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_BulletinBoard();
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
    public function update($row, $value, $field = "bb_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "bb_id")
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
    public function getByField($value, $field = 'bb_id', $colums = "*")
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
        
        if(isset($condition["code"]) && $condition["code"] != ""){
            $select->where("code = ?",$condition["code"]);
        }
        if(isset($condition["v_code"]) && $condition["v_code"] != ""){
            $select->where("v_code = ?",$condition["v_code"]);
        }
        if(isset($condition["v_title"]) && $condition["v_title"] != ""){
            $select->where("v_title = ?",$condition["v_title"]);
        }
        if(isset($condition["v_title_en"]) && $condition["v_title_en"] != ""){
            $select->where("v_title_en = ?",$condition["v_title_en"]);
        }
        if(isset($condition["v_operator"]) && $condition["v_operator"] != ""){
            $select->where("v_operator = ?",$condition["v_operator"]);
        }
        if(isset($condition["v_content"]) && $condition["v_content"] != ""){
            $select->where("v_content = ?",$condition["v_content"]);
        }
        if(isset($condition["v_content_en"]) && $condition["v_content_en"] != ""){
            $select->where("v_content_en = ?",$condition["v_content_en"]);
        }
        if(isset($condition["v_add_time"]) && $condition["v_add_time"] != ""){
            $select->where("v_add_time = ?",$condition["v_add_time"]);
        }
        if(isset($condition["v_published"]) && $condition["v_published"] != ""){
        	$select->where("v_published = ?",$condition["v_published"]);
        }
        if(isset($condition["current_date"]) && $condition["current_date"] != ""){
        	$select->where("v_published < ?",$condition["current_date"]);
        }
        if(isset($condition["v_pop_up_display"]) && $condition["v_pop_up_display"] != ""){
        	$select->where("v_pop_up_display = ?",$condition["v_pop_up_display"]);
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