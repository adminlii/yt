<?php
class Table_CtsIssueCount
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CtsIssueCount();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CtsIssueCount();
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
    public function update($row, $value, $field = "isc_no")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "isc_no")
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
    public function getByField($value, $field = 'isc_no', $colums = "*")
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
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["shipper_channel_id"]) && $condition["shipper_channel_id"] != ""){
            $select->where("shipper_channel_id = ?",$condition["shipper_channel_id"]);
        }
        if(isset($condition["isc_waitforreply"]) && $condition["isc_waitforreply"] != ""){
            $select->where("isc_waitforreply = ?",$condition["isc_waitforreply"]);
        }
        if(isset($condition["isc_replied"]) && $condition["isc_replied"] != ""){
            $select->where("isc_replied = ?",$condition["isc_replied"]);
        }
        if(isset($condition["isc_unrelease"]) && $condition["isc_unrelease"] != ""){
            $select->where("isc_unrelease = ?",$condition["isc_unrelease"]);
        }
        if(isset($condition["isc_notice"]) && $condition["isc_notice"] != ""){
            $select->where("isc_notice = ?",$condition["isc_notice"]);
        }
        if(isset($condition["isc_noticeread"]) && $condition["isc_noticeread"] != ""){
            $select->where("isc_noticeread = ?",$condition["isc_noticeread"]);
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