<?php
class Table_EbayUserCases
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayUserCases();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayUserCases();
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
    public function update($row, $value, $field = "uc_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "uc_id")
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
    public function getByField($value, $field = 'uc_id', $colums = "*")
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
        if(isset($condition["case_id"]) && $condition["case_id"] != ""){
            $select->where("case_id = ?",$condition["case_id"]);
        }
        if(isset($condition["case_type"]) && $condition["case_type"] != ""){
            $select->where("case_type = ?",$condition["case_type"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
            $select->where("buyer_id = ?",$condition["buyer_id"]);
        }
        if(isset($condition["case_status"]) && $condition["case_status"] != ""){
            $select->where("case_status = ?",$condition["case_status"]);
        }
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["item_title"]) && $condition["item_title"] != ""){
            $select->where("item_title = ?",$condition["item_title"]);
        }
        if(isset($condition["transaction_id"]) && $condition["transaction_id"] != ""){
            $select->where("transaction_id = ?",$condition["transaction_id"]);
        }
        if(isset($condition["case_qty"]) && $condition["case_qty"] != ""){
            $select->where("case_qty = ?",$condition["case_qty"]);
        }
        if(isset($condition["currency_id"]) && $condition["currency_id"] != ""){
            $select->where("currency_id = ?",$condition["currency_id"]);
        }
        if(isset($condition["case_amount"]) && $condition["case_amount"] != ""){
            $select->where("case_amount = ?",$condition["case_amount"]);
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
    	if ('count(*)' != $type) {
    		//$type[] = "(select t.OrderID from order_product t where t.op_ref_item_id = ebay_user_cases.item_id and t.op_ref_buyer_id = ebay_user_cases.buyer_id order by t.op_ref_paydate desc LIMIT 0,1) as refrence_no_platform";
    	}
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    	
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["case_id"]) && $condition["case_id"] != ""){
    		$select->where("case_id = ?",$condition["case_id"]);
    	}
    	if(isset($condition["case_type"]) && $condition["case_type"] != ""){
    		$select->where("case_type in (?)",$condition["case_type"]);
    	}
    	if(isset($condition["user_account"]) && $condition["user_account"] != ""){
    		$select->where("user_account in (?)",$condition["user_account"]);
    	}
    	if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
    		$select->where("buyer_id = ?",$condition["buyer_id"]);
    	}
    	if(isset($condition["case_status_unclose"]) && $condition["case_status_unclose"] != ""){
    		$select->where("case_status not in (?)",$condition["case_status_unclose"]);
    	}
    	if(isset($condition["case_status_close"]) && $condition["case_status_close"] != ""){
    		$select->where("case_status in (?)",$condition["case_status_close"]);
    	}
    	if(isset($condition["case_status"]) && $condition["case_status"] != ""){
    		$select->where("case_status in (?)",$condition["case_status"]);
    	}    	
    	if(isset($condition["item_id"]) && $condition["item_id"] != ""){
    		$select->where("item_id = ?",$condition["item_id"]);
    	}
    	if(isset($condition["item_title"]) && $condition["item_title"] != ""){
    		$select->where("item_title = ?",$condition["item_title"]);
    	}
    	if(isset($condition["transaction_id"]) && $condition["transaction_id"] != ""){
    		$select->where("transaction_id = ?",$condition["transaction_id"]);
    	}
    	if(isset($condition["case_qty"]) && $condition["case_qty"] != ""){
    		$select->where("case_qty = ?",$condition["case_qty"]);
    	}
    	if(isset($condition["currency_id"]) && $condition["currency_id"] != ""){
    		$select->where("currency_id = ?",$condition["currency_id"]);
    	}
    	if(isset($condition["case_amount"]) && $condition["case_amount"] != ""){
    		$select->where("case_amount = ?",$condition["case_amount"]);
    	}
    	
    	if(isset($condition["caseCreationDateFrom"]) && $condition["caseCreationDateFrom"] != ""){
    		$select->where("case_creation_date > ?",$condition["caseCreationDateFrom"]);
    	}
    	if(isset($condition["caseCreationDateEnd"]) && $condition["caseCreationDateEnd"] != ""){
    		$select->where("case_creation_date < ?",$condition["caseCreationDateEnd"]);
    	}
    	
    	/*CONDITION_END*/
//     	echo $select->__toString();
//     	exit;
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