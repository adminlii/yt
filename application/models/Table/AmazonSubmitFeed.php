<?php
class Table_AmazonSubmitFeed
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonSubmitFeed();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonSubmitFeed();
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
    public function update($row, $value, $field = "asf_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "asf_id")
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
    public function getByField($value, $field = 'asf_id', $colums = "*")
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
        
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["type"]) && $condition["type"] != ""){
            $select->where("type = ?",$condition["type"]);
        }
        if(isset($condition["request_id"]) && $condition["request_id"] != ""){
            $select->where("request_id = ?",$condition["request_id"]);
        }
        if(isset($condition["feed_submission_id"]) && $condition["feed_submission_id"] != ""){
            $select->where("feed_submission_id = ?",$condition["feed_submission_id"]);
        }
        if(isset($condition["feed_processing_status"]) && $condition["feed_processing_status"] != ""){
            $select->where("feed_processing_status = ?",$condition["feed_processing_status"]);
        }
        if(isset($condition["feed_content"]) && $condition["feed_content"] != ""){
            $select->where("feed_content = ?",$condition["feed_content"]);
        }
        if(isset($condition["sys_creation_date"]) && $condition["sys_creation_date"] != ""){
            $select->where("sys_creation_date = ?",$condition["sys_creation_date"]);
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