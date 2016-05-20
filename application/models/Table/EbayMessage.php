<?php
class Table_EbayMessage
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayMessage();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayMessage();
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
        /* CONDITION_START */
        
        if(isset($condition["message_id"]) && $condition["message_id"] != ""){
            $select->where("message_id = ?", $condition["message_id"]);
        }
        if(isset($condition["gt_message_id"]) && $condition["gt_message_id"] != ""){
            $select->where("message_id > ?", $condition["gt_message_id"]);
        }

        if(isset($condition["lt_message_id"]) && $condition["lt_message_id"] != ""){
            $select->where("message_id < ?", $condition["lt_message_id"]);
        }
        
        if(isset($condition["ebay_message_id"]) && $condition["ebay_message_id"] != ""){
            $select->where("ebay_message_id = ?", $condition["ebay_message_id"]);
        }
        if(isset($condition["sender_id"]) && $condition["sender_id"] != ""){
            $select->where("sender_id = ?", $condition["sender_id"]);
        }
        if(isset($condition["sender_id_arr"]) && !empty($condition["sender_id_arr"])){
            $select->where("sender_id in (?)", $condition["sender_id_arr"]);
        }
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?", $condition["receiving_id"]);
        }
        if(isset($condition["message_title"]) && $condition["message_title"] != ""){
            $select->where("message_title = ?", $condition["message_title"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?", $condition["company_code"]);
        }
        if(isset($condition["message_type"]) && $condition["message_type"] != ""){
            $select->where("message_type = ?", $condition["message_type"]);
        }
        if(isset($condition["message_type_id"]) && $condition["message_type_id"] != ""){
            $select->where("message_type_id = ?", $condition["message_type_id"]);
        }
        if(isset($condition["receive_type"]) && $condition["receive_type"] != ""){
            $select->where("receive_type = ?", $condition["receive_type"]);
        }
        
        if(isset($condition["receive_type_not_eq"]) && ! empty($condition["receive_type_not_eq"])){
            $select->where("receive_type not in(?)", $condition["receive_type_not_eq"]);
        }
        
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?", $condition["status"]);
        }
        if(isset($condition["status_arr"]) && ! empty($condition["status_arr"])){
            $select->where("status in(?)", $condition["status_arr"]);
        }
        
        if(isset($condition["level"]) && $condition["level"] != ""){
            $select->where("level = ?", $condition["level"]);
        }
        if(isset($condition["customer_service_id"]) && $condition["customer_service_id"] != ""){
            $select->where("customer_service_id = ?", $condition["customer_service_id"]);
        }
        if(isset($condition["send_mail"]) && $condition["send_mail"] != ""){
            $select->where("send_mail = ?", $condition["send_mail"]);
        }

        if(isset($condition["send_mail_arr"]) && !empty($condition["send_mail_arr"])){
            $select->where("send_mail in (?)", $condition["send_mail_arr"]);
        }
        
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?", $condition["item_id"]);
        }
        if(isset($condition["message_url"]) && $condition["message_url"] != ""){
            $select->where("message_url = ?", $condition["message_url"]);
        }
        if(isset($condition["refrence_id"]) && $condition["refrence_id"] != ""){
            $select->where("refrence_id = ?", $condition["refrence_id"]);
        }
        if(isset($condition["customer_service_response"]) && $condition["customer_service_response"] != ""){
            $select->where("customer_service_response = ?", $condition["customer_service_response"]);
        }
        if(isset($condition["response_sync"]) && $condition["response_sync"] != ""){
            $select->where("response_sync = ?", $condition["response_sync"]);
        }
        if(isset($condition["item_title"]) && $condition["item_title"] != ""){
            $select->where("item_title = ?", $condition["item_title"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?", $condition["user_account"]);
        }
        if(isset($condition["is_ebay_message"]) && $condition["is_ebay_message"] !== ""){
            if($condition["is_ebay_message"]==='0'){
                $select->where("sender_id != ?", 'ebay');
            }else{
                $select->where("sender_id = ?", 'ebay');
            }            
        }
                
        //属于分配店铺或者其让人分配--------------------------------------
        if(isset($condition["my_message"])){
        	/*
        	 * 调整sql，不在查询分配人
        	*/
//             $subsql = "(user_account in (?) and (customer_service_id is NULL or customer_service_id='".$condition["customer_service_id_"]."')) ";            
        	$subsql = "(user_account in (?)) ";
            $select->where($subsql,$condition["user_account_arr"]);
			/*
			 * 是否查看与之间相关的邮件
			 */
            if(isset($condition["service_related"]) && $condition["service_related"] !== ""){
            	$sql_append = "";
            	if($condition["service_related"] == 'Y'){
            		$sql_append = "(customer_service_id = '".$condition["customer_service_id_"]."')";
            	}else if($condition["service_related"] == 'N'){
//             		$sql_append = "(customer_service_id is NULL)";
            		$sql_append = "(customer_service_id != '".$condition["customer_service_id_"]."' OR customer_service_id is NULL)";
            	}
            	$select->where($sql_append);
            }
            
        }
        
        
        // ----------------------------------------------------
       
        if(isset($condition["response_status"])&&$condition["response_status"]!=''){ // 是否已经回复
            $select->where("response_status = ?",$condition["response_status"]);
        }

        if(isset($condition["response_status_arr"])&&!empty($condition["response_status_arr"])){ // 是否已经回复
            $select->where("response_status in (?)",$condition["response_status_arr"]);
        }
//                 echo $select;exit;
// 		Ec::showError($select->__toString() . '---' . print_r($condition,true),'message_sql');
        /* CONDITION_END */
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            if (! empty($orderBy)) {
                $select->order($orderBy);
            }
            if ($pageSize > 0 and $page > 0) {
                $start = ($page - 1) * $pageSize;
                $select->limit($pageSize, $start);
            }
            
            $sql = $select->__toString();
//             echo $sql;exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
    

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionBak($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
    
        $opTable = new DbTable_EbayMessageContent();
        $table1 = $opTable->info('name');
        if($type == 'count(*)'){
            $select->joinInner($table1, $table . '.message_id = ' . $table1 . '.message_id', null);
        }else{
            $select->joinInner($table1, $table . '.message_id = ' . $table1 . '.message_id', array(
                    'currt_content',
                    'history_content',
                    'response_content'
            ));
        }
    
        $select->where("1 =?", 1);
        /* CONDITION_START */
    
        if(isset($condition["message_id"]) && $condition["message_id"] != ""){
            $select->where("message_id = ?", $condition["message_id"]);
        }
        if(isset($condition["ebay_message_id"]) && $condition["ebay_message_id"] != ""){
            $select->where("ebay_message_id = ?", $condition["ebay_message_id"]);
        }
        if(isset($condition["sender_id"]) && $condition["sender_id"] != ""){
            $select->where("sender_id = ?", $condition["sender_id"]);
        }
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?", $condition["receiving_id"]);
        }
        if(isset($condition["message_title"]) && $condition["message_title"] != ""){
            $select->where("message_title = ?", $condition["message_title"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?", $condition["company_code"]);
        }
        if(isset($condition["message_type"]) && $condition["message_type"] != ""){
            $select->where("message_type = ?", $condition["message_type"]);
        }
        if(isset($condition["message_type_id"]) && $condition["message_type_id"] != ""){
            $select->where("message_type_id = ?", $condition["message_type_id"]);
        }
        if(isset($condition["receive_type"]) && $condition["receive_type"] != ""){
            $select->where("receive_type = ?", $condition["receive_type"]);
        }
    
        if(isset($condition["receive_type_not_eq"]) && ! empty($condition["receive_type_not_eq"])){
            $select->where("receive_type not in(?)", $condition["receive_type_not_eq"]);
        }
    
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?", $condition["status"]);
        }
        if(isset($condition["level"]) && $condition["level"] != ""){
            $select->where("level = ?", $condition["level"]);
        }
        if(isset($condition["customer_service_id"]) && $condition["customer_service_id"] != ""){
            $select->where("customer_service_id = ?", $condition["customer_service_id"]);
        }
        if(isset($condition["send_mail"]) && $condition["send_mail"] != ""){
            $select->where("send_mail = ?", $condition["send_mail"]);
        }
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?", $condition["item_id"]);
        }
        if(isset($condition["message_url"]) && $condition["message_url"] != ""){
            $select->where("message_url = ?", $condition["message_url"]);
        }
        if(isset($condition["refrence_id"]) && $condition["refrence_id"] != ""){
            $select->where("refrence_id = ?", $condition["refrence_id"]);
        }
        if(isset($condition["customer_service_response"]) && $condition["customer_service_response"] != ""){
            $select->where("customer_service_response = ?", $condition["customer_service_response"]);
        }
        if(isset($condition["response_sync"]) && $condition["response_sync"] != ""){
            $select->where("response_sync = ?", $condition["response_sync"]);
        }
        if(isset($condition["item_title"]) && $condition["item_title"] != ""){
            $select->where("item_title = ?", $condition["item_title"]);
        }
    
        //属于分配店铺或者其让人分配--------------------------------------
        if(!empty($condition["user_account_arr"])&&!empty($condition['customer_service_id_'])){
            $select->where("user_account in (?) or customer_service_id='".$condition['customer_service_id_']."'",$condition["user_account_arr"]);
        }else if(!empty($condition["user_account_arr"])){
            $select->where("user_account in (?)",$condition["user_account_arr"]);
        }elseif(!empty($condition["customer_service_id_"])){
            $select->where("customer_service_id = ?",$condition["customer_service_id_"]);
        }
    
        // ----------------------------------------------------
        if(isset($condition["response_content_not_null"]) && ! empty($condition["response_content_not_null"])){ // 是否有回复内容，正真回复
            $select->where($table1 . ".response_content !=''");
        }
    
        if(isset($condition["response_status"])&&$condition["response_status"]!=''){ // 是否已经回复
            $select->where("response_status = ?",$condition["response_status"]);
        }
    
//         echo $select;exit;
        /* CONDITION_END */
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            if (! empty($orderBy)) {
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

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionInnerJoinContent($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	if ($type == 'count(*)') {
			$select->joinInner ( 'ebay_message_content', $table . '.message_id = ebay_message_content.message_id', null );
		} else {
			$select->joinInner ( 'ebay_message_content', $table . '.message_id = ebay_message_content.message_id', array (
					'currt_content',
					'history_content',
					'response_content' 
			) );
		}
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["message_id"]) && $condition["message_id"] != ""){
    		$select->where("message_id = ?",$condition["message_id"]);
    	}
    	if(isset($condition["ebay_message_id"]) && $condition["ebay_message_id"] != ""){
    		$select->where("ebay_message_id = ?",$condition["ebay_message_id"]);
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
    
    	if(isset($condition["receive_type_not_eq"]) && !empty($condition["receive_type_not_eq"])){
    		$select->where("receive_type not in(?)",$condition["receive_type_not_eq"]);
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
    	if(isset($condition["customer_service_response"]) && $condition["customer_service_response"] != ""){
    		$select->where("customer_service_response = ?",$condition["customer_service_response"]);
    	}
    	if(isset($condition["response_sync"]) && $condition["response_sync"] != ""){
    		$select->where("response_sync = ?",$condition["response_sync"]);
    	}
    	if(isset($condition["item_title"]) && $condition["item_title"] != ""){
    		$select->where("item_title = ?",$condition["item_title"]);
    	}
    	
    	
    	if(isset($condition["response_content_not_null"]) && $condition["response_content_not_null"] != ""){
    		$select->where(" ebay_message_content.response_content!=''");
    	}
//     	        echo $select;exit;
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