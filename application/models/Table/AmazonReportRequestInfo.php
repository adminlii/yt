<?php
class Table_AmazonReportRequestInfo
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonReportRequestInfo();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonReportRequestInfo();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        $row['create_time'] = now();
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
        if(isset($condition["ReportRequestId"]) && $condition["ReportRequestId"] != ""){
            $select->where("ReportRequestId = ?",$condition["ReportRequestId"]);
        }
        if(isset($condition["ReportType"]) && $condition["ReportType"] != ""){
            $select->where("ReportType = ?",$condition["ReportType"]);
        }
        if(isset($condition["StartDate"]) && $condition["StartDate"] != ""){
            $select->where("StartDate = ?",$condition["StartDate"]);
        }
        if(isset($condition["EndDate"]) && $condition["EndDate"] != ""){
            $select->where("EndDate = ?",$condition["EndDate"]);
        }
        if(isset($condition["Scheduled"]) && $condition["Scheduled"] != ""){
            $select->where("Scheduled = ?",$condition["Scheduled"]);
        }
        if(isset($condition["SubmittedDate"]) && $condition["SubmittedDate"] != ""){
            $select->where("SubmittedDate = ?",$condition["SubmittedDate"]);
        }
        if(isset($condition["ReportProcessingStatus"]) && $condition["ReportProcessingStatus"] != ""){
            $select->where("ReportProcessingStatus = ?",$condition["ReportProcessingStatus"]);
        }
        if(isset($condition["GeneratedReportId"]) && $condition["GeneratedReportId"] != ""){
            $select->where("GeneratedReportId = ?",$condition["GeneratedReportId"]);
        }
        if(isset($condition["StartedProcessingDate"]) && $condition["StartedProcessingDate"] != ""){
            $select->where("StartedProcessingDate = ?",$condition["StartedProcessingDate"]);
        }
        if(isset($condition["CompletedDate"]) && $condition["CompletedDate"] != ""){
            $select->where("CompletedDate = ?",$condition["CompletedDate"]);
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