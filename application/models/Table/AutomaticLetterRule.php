<?php
class Table_AutomaticLetterRule
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AutomaticLetterRule();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AutomaticLetterRule();
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
    public function update($row, $value, $field = "alr_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "alr_id")
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
    public function getByField($value, $field = 'alr_id', $colums = "*")
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
        
        if(isset($condition["rule_name"]) && $condition["rule_name"] != ""){
            $select->where("rule_name = ?",$condition["rule_name"]);
        }
        if(isset($condition["rule_platform"]) && $condition["rule_platform"] != ""){
            $select->where("rule_platform = ?",$condition["rule_platform"]);
        }
        if(isset($condition["alt_id"]) && $condition["alt_id"] != ""){
        	$select->where("alt_id = ?",$condition["alt_id"]);
        }
        if(isset($condition["altc_id"]) && $condition["altc_id"] != ""){
            $select->where("altc_id = ?",$condition["altc_id"]);
        }
        if(isset($condition["language_code"]) && $condition["language_code"] != ""){
            $select->where("language_code = ?",$condition["language_code"]);
        }
        if(isset($condition["rule_level"]) && $condition["rule_level"] != ""){
            $select->where("rule_level = ?",$condition["rule_level"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
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