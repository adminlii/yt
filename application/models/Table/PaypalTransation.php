<?php
class Table_PaypalTransation
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PaypalTransation();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PaypalTransation();
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
    public function update($row, $value, $field = "transation_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "transation_id")
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
    public function getByField($value, $field = 'transation_id', $colums = "*")
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
        if(isset($condition["paypal_tid"]) && $condition["paypal_tid"] != ""){
            $select->where("paypal_tid = ?",$condition["paypal_tid"]);
        }
        if(isset($condition["amount_total"]) && $condition["amount_total"] != ""){
            $select->where("amount_total = ?",$condition["amount_total"]);
        }
        if(isset($condition["fee"]) && $condition["fee"] != ""){
            $select->where("fee = ?",$condition["fee"]);
        }
        if(isset($condition["amount_net"]) && $condition["amount_net"] != ""){
            $select->where("amount_net = ?",$condition["amount_net"]);
        }
        if(isset($condition["pay_account"]) && $condition["pay_account"] != ""){
            $select->where("pay_account = ?",$condition["pay_account"]);
        }
        if(isset($condition["currency"]) && $condition["currency"] != ""){
            $select->where("currency = ?",$condition["currency"]);
        }
        if(isset($condition["pay_name"]) && $condition["pay_name"] != ""){
            $select->where("pay_name = ?",$condition["pay_name"]);
        }
        if(isset($condition["pay_email"]) && $condition["pay_email"] != ""){
            $select->where("pay_email = ?",$condition["pay_email"]);
        }
        if(isset($condition["pay_type"]) && $condition["pay_type"] != ""){
            $select->where("pay_type = ?",$condition["pay_type"]);
        }
        if(isset($condition["pay_status"]) && $condition["pay_status"] != ""){
            $select->where("pay_status = ?",$condition["pay_status"]);
        }
        if(isset($condition["recv_account"]) && $condition["recv_account"] != ""){
            $select->where("recv_account = ?",$condition["recv_account"]);
        }
        if(isset($condition["transactionDateFrom"]) && $condition["transactionDateFrom"] != ""){
        	$select->where("transation_time >= ?",$condition["transactionDateFrom"]);
        }
        if(isset($condition["transactionDateEnd"]) && $condition["transactionDateEnd"] != ""){
        	$select->where("transation_time <= ?",$condition["transactionDateEnd"]);
        }
        if(isset($condition["recv_accounts"]) && $condition["recv_accounts"] != ""){
        	foreach ($condition["recv_accounts"] as $value) {
        		$accountCondition .= "'$value',";
        	}
        	$accountCondition = substr($accountCondition,0,-1);
        	$select->where("recv_account in ($accountCondition)");
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
    	if($type != 'count(*)'){
    		$type[] = "(select orders.refrence_no_platform from orders where orders.order_id = paypal_order_transaction.order_id) as refrence_no_platform";
    	}
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	
    	$opTable = new DbTable_PaypalOrderTransaction();
    	$tablePaypalOrder = $opTable->info('name');
    	if($type == 'count(*)'){
    		$select->joinInner($tablePaypalOrder, $table . '.transation_id = ' . $tablePaypalOrder . '.pt_id', null);
    	}else{
    		$select->joinInner($tablePaypalOrder, $table . '.transation_id = ' . $tablePaypalOrder . '.pt_id', array(
    				'pot_buyer_id',
    				'pot_first_name',
    				'pot_last_name',
    				'pot_status',
    				'order_id',
    				'pot_id'
    		));
    	}
    	
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    	
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["paypal_tid"]) && $condition["paypal_tid"] != ""){
    		$select->where("paypal_tid = ?",$condition["paypal_tid"]);
    	}
    	if(isset($condition["paypal_tid"]) && $condition["paypal_tid"] != ""){
    		$select->where("paypal_tid = ?",$condition["paypal_tid"]);
    	}
    	if(isset($condition["amount_total"]) && $condition["amount_total"] != ""){
    		$select->where("amount_total = ?",$condition["amount_total"]);
    	}
    	if(isset($condition["fee"]) && $condition["fee"] != ""){
    		$select->where("fee = ?",$condition["fee"]);
    	}
    	if(isset($condition["amount_net"]) && $condition["amount_net"] != ""){
    		$select->where("amount_net = ?",$condition["amount_net"]);
    	}
    	if(isset($condition["pay_account"]) && $condition["pay_account"] != ""){
    		$select->where("pay_account = ?",$condition["pay_account"]);
    	}
    	if(isset($condition["currency"]) && $condition["currency"] != ""){
    		$select->where("currency = ?",$condition["currency"]);
    	}
    	if(isset($condition["pay_name"]) && $condition["pay_name"] != ""){
    		$select->where("pay_name = ?",$condition["pay_name"]);
    	}
    	if(isset($condition["pay_email"]) && $condition["pay_email"] != ""){
    		$select->where("pay_email = ?",$condition["pay_email"]);
    	}
    	if(isset($condition["pay_type"]) && $condition["pay_type"] != ""){
    		$select->where("pay_type = ?",$condition["pay_type"]);
    	}
    	if(isset($condition["pay_status"]) && $condition["pay_status"] != ""){
    		$select->where("pay_status = ?",$condition["pay_status"]);
    	}
    	if(isset($condition["recv_account"]) && $condition["recv_account"] != ""){
    		$select->where("recv_account = ?",$condition["recv_account"]);
    	}
    	if(isset($condition["transactionDateFrom"]) && $condition["transactionDateFrom"] != ""){
    		$select->where("transation_time >= ?",$condition["transactionDateFrom"]);
    	}
    	if(isset($condition["transactionDateEnd"]) && $condition["transactionDateEnd"] != ""){
    		$select->where("transation_time <= ?",$condition["transactionDateEnd"]);
    	}
    	if(isset($condition["recv_accounts"]) && $condition["recv_accounts"] != "" && count($condition["recv_accounts"]) > 0){
    		foreach ($condition["recv_accounts"] as $value) {
    			$accountCondition .= "'$value',";
    		}
    		$accountCondition = substr($accountCondition,0,-1);
    		$select->where("recv_account in ($accountCondition)");
    	}
    	if(isset($condition["pot_status"]) && $condition["pot_status"] != ""){
    		$select->where("pot_status = ?",$condition["pot_status"]);
    	}
    	if(isset($condition["pot_buyer_id"]) && $condition["pot_buyer_id"] != ""){
    		$select->where($tablePaypalOrder . ".pot_buyer_id = ?",$condition["pot_buyer_id"]);
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