<?php
class Table_OrderAuditSet
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderAuditSet();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderAuditSet();
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
    public function update($row, $value, $field = "oas_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "oas_id")
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
    public function getByField($value, $field = 'oas_id', $colums = "*")
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
        
        if(isset($condition["audit_set_name"]) && $condition["audit_set_name"] != ""){
            $select->where("audit_set_name = ?",$condition["audit_set_name"]);
        }
        if(isset($condition["audit_set_name_like"]) && $condition["audit_set_name_like"] != ""){
        	$select->where("audit_set_name like ?",'%' . $condition["audit_set_name_like"] . '%');
        }
        
        if(isset($condition["audit_action_value"]) && $condition["audit_action_value"] != ""){
            $select->where("audit_action_value = ?",$condition["audit_action_value"]);
        }
        if(isset($condition["audit_level"]) && $condition["audit_level"] != ""){
            $select->where("audit_level = ?",$condition["audit_level"]);
        }
        if(isset($condition["is_auto_audit"]) && $condition["is_auto_audit"] != ""){
            $select->where("is_auto_audit = ?",$condition["is_auto_audit"]);
        }
        if(isset($condition["audit_status"]) && $condition["audit_status"] != ""){
            $select->where("audit_status = ?",$condition["audit_status"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
        /*CONDITION_END*/
//         echo $select->__toString();
//         exit;
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