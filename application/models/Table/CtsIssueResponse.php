<?php
class Table_CtsIssueResponse
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CtsIssueResponse();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CtsIssueResponse();
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
    public function update($row, $value, $field = "response_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "response_id")
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
    public function getByField($value, $field = 'response_id', $colums = "*")
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
        
        if(isset($condition["issue_id"]) && $condition["issue_id"] != ""){
            $select->where("issue_id = ?",$condition["issue_id"]);
        }
        if(isset($condition["issue_response_code"]) && $condition["issue_response_code"] != ""){
            $select->where("issue_response_code = ?",$condition["issue_response_code"]);
        }
        if(isset($condition["message_type"]) && $condition["message_type"] != ""){
            $select->where("message_type = ?",$condition["message_type"]);
        }
        if(isset($condition["message_sendsign"]) && $condition["message_sendsign"] != ""){
            $select->where("message_sendsign = ?",$condition["message_sendsign"]);
        }
        if(isset($condition["st_id_create"]) && $condition["st_id_create"] != ""){
            $select->where("st_id_create = ?",$condition["st_id_create"]);
        }
        if(isset($condition["replay_name"]) && $condition["replay_name"] != ""){
            $select->where("replay_name = ?",$condition["replay_name"]);
        }
        if(isset($condition["message_content"]) && $condition["message_content"] != ""){
            $select->where("message_content = ?",$condition["message_content"]);
        }
        if(isset($condition["replay_createdate"]) && $condition["replay_createdate"] != ""){
            $select->where("replay_createdate = ?",$condition["replay_createdate"]);
        }
        if(isset($condition["first_refer_date"]) && $condition["first_refer_date"] != ""){
            $select->where("first_refer_date = ?",$condition["first_refer_date"]);
        }
        if(isset($condition["first_refer_name"]) && $condition["first_refer_name"] != ""){
            $select->where("first_refer_name = ?",$condition["first_refer_name"]);
        }
        if(isset($condition["refer_sign"]) && $condition["refer_sign"] != ""){
            $select->where("refer_sign = ?",$condition["refer_sign"]);
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