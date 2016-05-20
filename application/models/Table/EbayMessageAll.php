<?php
class Table_EbayMessageAll
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayMessageAll();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayMessageAll();
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
    public function update($row, $value, $field = "message_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "message_id")
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
    public function getByField($value, $field = 'message_id', $colums = "*")
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
        
        if(isset($condition["ebay_message_id"]) && $condition["ebay_message_id"] != ""){
            $select->where("ebay_message_id = ?",$condition["ebay_message_id"]);
        }
        if(isset($condition["ebay_ext_message_id"]) && $condition["ebay_ext_message_id"] != ""){
            $select->where("ebay_ext_message_id = ?",$condition["ebay_ext_message_id"]);
        }
        if(isset($condition["sender_id"]) && $condition["sender_id"] != ""){
            $select->where("sender_id = ?",$condition["sender_id"]);
        }
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if(isset($condition["message_title"]) && $condition["message_title"] != ""){
            $select->where("message_title = ?",$condition["message_title"]);
        }
        if(isset($condition["send_time"]) && $condition["send_time"] != ""){
            $select->where("send_time = ?",$condition["send_time"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["message_type"]) && $condition["message_type"] != ""){
            $select->where("message_type = ?",$condition["message_type"]);
        }
        if(isset($condition["message_type_id"]) && $condition["message_type_id"] != ""){
            $select->where("message_type_id = ?",$condition["message_type_id"]);
        }
        if(isset($condition["receive_type"]) && $condition["receive_type"] != ""){
            $select->where("receive_type = ?",$condition["receive_type"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
        if(isset($condition["level"]) && $condition["level"] != ""){
            $select->where("level = ?",$condition["level"]);
        }
        if(isset($condition["customer_service_id"]) && $condition["customer_service_id"] != ""){
            $select->where("customer_service_id = ?",$condition["customer_service_id"]);
        }
        if(isset($condition["send_mail"]) && $condition["send_mail"] != ""){
            $select->where("send_mail = ?",$condition["send_mail"]);
        }
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["message_url"]) && $condition["message_url"] != ""){
            $select->where("message_url = ?",$condition["message_url"]);
        }
        if(isset($condition["refrence_id"]) && $condition["refrence_id"] != ""){
            $select->where("refrence_id = ?",$condition["refrence_id"]);
        }
        if(isset($condition["create_time"]) && $condition["create_time"] != ""){
            $select->where("create_time = ?",$condition["create_time"]);
        }
        if(isset($condition["response_time"]) && $condition["response_time"] != ""){
            $select->where("response_time = ?",$condition["response_time"]);
        }
        if(isset($condition["customer_service_response"]) && $condition["customer_service_response"] != ""){
            $select->where("customer_service_response = ?",$condition["customer_service_response"]);
        }
        if(isset($condition["response_sync"]) && $condition["response_sync"] != ""){
            $select->where("response_sync = ?",$condition["response_sync"]);
        }
        if(isset($condition["response_sync_time"]) && $condition["response_sync_time"] != ""){
            $select->where("response_sync_time = ?",$condition["response_sync_time"]);
        }
        if(isset($condition["item_title"]) && $condition["item_title"] != ""){
            $select->where("item_title = ?",$condition["item_title"]);
        }
        if(isset($condition["question_type"]) && $condition["question_type"] != ""){
            $select->where("question_type = ?",$condition["question_type"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["response_status"]) && $condition["response_status"] != ""){
            $select->where("response_status = ?",$condition["response_status"]);
        }
        if(isset($condition["process_status"]) && $condition["process_status"] != ""){
            $select->where("process_status = ?",$condition["process_status"]);
        }
        if(isset($condition["folder_id"]) && $condition["folder_id"] != ""){
            $select->where("folder_id = ?",$condition["folder_id"]);
        }
        if(isset($condition["currt_content"]) && $condition["currt_content"] != ""){
            $select->where("currt_content = ?",$condition["currt_content"]);
        }
        if(isset($condition["response_content"]) && $condition["response_content"] != ""){
            $select->where("response_content = ?",$condition["response_content"]);
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