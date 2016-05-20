<?php
class Table_SysModuleStepContent
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_SysModuleStepContent();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_SysModuleStepContent();
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
    public function update($row, $value, $field = "smsc_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "smsc_id")
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
    public function getByField($value, $field = 'smsc_id', $colums = "*")
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
        
        if(isset($condition["sms_id"]) && $condition["sms_id"] != ""){
            $select->where("sms_id = ?",$condition["sms_id"]);
        }
        if(isset($condition["smsc_step_num"]) && $condition["smsc_step_num"] != ""){
            $select->where("smsc_step_num = ?",$condition["smsc_step_num"]);
        }
        if(isset($condition["smsc_step_code"]) && $condition["smsc_step_code"] != ""){
            $select->where("smsc_step_code = ?",$condition["smsc_step_code"]);
        }
        if(isset($condition["smsc_step_text"]) && $condition["smsc_step_text"] != ""){
            $select->where("smsc_step_text = ?",$condition["smsc_step_text"]);
        }
        if(isset($condition["smsc_step_content"]) && $condition["smsc_step_content"] != ""){
            $select->where("smsc_step_content = ?",$condition["smsc_step_content"]);
        }
        if(isset($condition["smsc_seq"]) && $condition["smsc_seq"] != ""){
            $select->where("smsc_seq = ?",$condition["smsc_seq"]);
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