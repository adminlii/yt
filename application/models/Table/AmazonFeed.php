<?php
class Table_AmazonFeed
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonFeed();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonFeed();
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
    public function update($row, $value, $field = "id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "id")
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
    public function getByField($value, $field = 'id', $colums = "*")
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
        
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["user_account_arr"]) && is_array($condition["user_account_arr"]) && ! empty($condition["user_account_arr"])){
            $select->where("user_account in (?)", $condition["user_account_arr"]);
        }
        if(isset($condition["FeedSubmissionId"]) && $condition["FeedSubmissionId"] != ""){
            $select->where("FeedSubmissionId = ?",$condition["FeedSubmissionId"]);
        }
        if(isset($condition["FeedType"]) && $condition["FeedType"] != ""){
            $select->where("FeedType = ?",$condition["FeedType"]);
        }
        if(isset($condition["SubmittedDate"]) && $condition["SubmittedDate"] != ""){
            $select->where("SubmittedDate = ?",$condition["SubmittedDate"]);
        }
        if(isset($condition["FeedProcessingStatus"]) && $condition["FeedProcessingStatus"] != ""){
            $select->where("FeedProcessingStatus = ?",$condition["FeedProcessingStatus"]);
        }
        if(isset($condition["RequestId"]) && $condition["RequestId"] != ""){
            $select->where("RequestId = ?",$condition["RequestId"]);
        }
        if(isset($condition["FeedContent"]) && $condition["FeedContent"] != ""){
            $select->where("FeedContent = ?",$condition["FeedContent"]);
        }
        if(isset($condition["create_time"]) && $condition["create_time"] != ""){
            $select->where("create_time = ?",$condition["create_time"]);
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