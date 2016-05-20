<?php
class Table_OrderDataSource
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderDataSource();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderDataSource();
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
    public function update($row, $value, $field = "ods_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ods_id")
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
    public function getByField($value, $field = 'ods_id', $colums = "*")
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
        
        
        if(isset($condition["ods_code_like"]) && $condition["ods_code_like"] != ""){
        	$select->where("ods_code like ?",'%' . $condition["ods_code_like"] . '%');
        }
    	if(isset($condition["ods_name_like"]) && $condition["ods_name_like"] != ""){
            $select->where("ods_name like ?",'%' . $condition["ods_name_like"] . '%');
        }
        
        if(isset($condition["ods_code"]) && $condition["ods_code"] != ""){
        	$select->where("ods_code = ?",$condition["ods_code"]);
        }
        if(isset($condition["ods_name"]) && $condition["ods_name"] != ""){
            $select->where("ods_name = ?",$condition["ods_name"]);
        }
        if(isset($condition["ods_name_en"]) && $condition["ods_name_en"] != ""){
            $select->where("ods_name_en = ?",$condition["ods_name_en"]);
        }
        if(isset($condition["ods_note"]) && $condition["ods_note"] != ""){
            $select->where("ods_note = ?",$condition["ods_note"]);
        }
        if(isset($condition["ods_seq"]) && $condition["ods_seq"] != ""){
            $select->where("ods_seq = ?",$condition["ods_seq"]);
        }
        if(isset($condition["ods_status"]) && $condition["ods_status"] != ""){
            $select->where("ods_status = ?",$condition["ods_status"]);
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