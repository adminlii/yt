<?php
class Table_ZendeskTickets
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ZendeskTickets();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ZendeskTickets();
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
        
        if(isset($condition["url"]) && $condition["url"] != ""){
            $select->where("url = ?",$condition["url"]);
        }
        if(isset($condition["external_id"]) && $condition["external_id"] != ""){
            $select->where("external_id = ?",$condition["external_id"]);
        }
        if(isset($condition["via_channel"]) && $condition["via_channel"] != ""){
            $select->where("via_channel = ?",$condition["via_channel"]);
        }
        if(isset($condition["via_source_from_address"]) && $condition["via_source_from_address"] != ""){
            $select->where("via_source_from_address = ?",$condition["via_source_from_address"]);
        }
        if(isset($condition["via_source_from_name"]) && $condition["via_source_from_name"] != ""){
            $select->where("via_source_from_name = ?",$condition["via_source_from_name"]);
        }
        if(isset($condition["via_source_to_address"]) && $condition["via_source_to_address"] != ""){
            $select->where("via_source_to_address = ?",$condition["via_source_to_address"]);
        }
        if(isset($condition["via_source_to_name"]) && $condition["via_source_to_name"] != ""){
            $select->where("via_source_to_name = ?",$condition["via_source_to_name"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
        }
        if(isset($condition["type"]) && $condition["type"] != ""){
            $select->where("type = ?",$condition["type"]);
        }
        if(isset($condition["subject"]) && $condition["subject"] != ""){
            $select->where("subject = ?",$condition["subject"]);
        }
        if(isset($condition["description"]) && $condition["description"] != ""){
            $select->where("description = ?",$condition["description"]);
        }
        if(isset($condition["priority"]) && $condition["priority"] != ""){
            $select->where("priority = ?",$condition["priority"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
        if(isset($condition["recipient"]) && $condition["recipient"] != ""){
            $select->where("recipient = ?",$condition["recipient"]);
        }
        if(isset($condition["requester_id"]) && $condition["requester_id"] != ""){
            $select->where("requester_id = ?",$condition["requester_id"]);
        }
        if(isset($condition["submitter_id"]) && $condition["submitter_id"] != ""){
            $select->where("submitter_id = ?",$condition["submitter_id"]);
        }
        if(isset($condition["assignee_id"]) && $condition["assignee_id"] != ""){
            $select->where("assignee_id = ?",$condition["assignee_id"]);
        }
        if(isset($condition["organization_id"]) && $condition["organization_id"] != ""){
            $select->where("organization_id = ?",$condition["organization_id"]);
        }
        if(isset($condition["group_id"]) && $condition["group_id"] != ""){
            $select->where("group_id = ?",$condition["group_id"]);
        }
        if(isset($condition["forum_topic_id"]) && $condition["forum_topic_id"] != ""){
            $select->where("forum_topic_id = ?",$condition["forum_topic_id"]);
        }
        if(isset($condition["problem_id"]) && $condition["problem_id"] != ""){
            $select->where("problem_id = ?",$condition["problem_id"]);
        }
        if(isset($condition["has_incidents"]) && $condition["has_incidents"] != ""){
            $select->where("has_incidents = ?",$condition["has_incidents"]);
        }
        if(isset($condition["due_at"]) && $condition["due_at"] != ""){
            $select->where("due_at = ?",$condition["due_at"]);
        }
        if(isset($condition["result_type"]) && $condition["result_type"] != ""){
            $select->where("result_type = ?",$condition["result_type"]);
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