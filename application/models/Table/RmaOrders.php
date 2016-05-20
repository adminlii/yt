<?php
class Table_RmaOrders
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_RmaOrders();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_RmaOrders();
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
    public function update($row, $value, $field = "rma_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rma_id")
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
    public function getByField($value, $field = 'rma_id', $colums = "*")
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
        if(isset($condition["is_not_rma_refund_type"]) && $condition["is_not_rma_refund_type"] != ""){
        	$select->where("rma_refund_type != ?",$condition["is_not_rma_refund_type"]);
        }
        if(isset($condition["rma_creator_id"]) && $condition["rma_creator_id"] != ""){
            $select->where("rma_creator_id = ?",$condition["rma_creator_id"]);
        }
        if(isset($condition["rma_verifyor_id"]) && $condition["rma_verifyor_id"] != ""){
            $select->where("rma_verifyor_id = ?",$condition["rma_verifyor_id"]);
        }
        if(isset($condition["rma_case_ref_id"]) && $condition["rma_case_ref_id"] != ""){
            $select->where("rma_case_ref_id = ?",$condition["rma_case_ref_id"]);
        }
        if(isset($condition["rma_case_type"]) && $condition["rma_case_type"] != ""){
            $select->where("rma_case_type = ?",$condition["rma_case_type"]);
        }
        if(isset($condition["rma_common"]) && $condition["rma_common"] != ""){
            $select->where("rma_common = ?",$condition["rma_common"]);
        }
        if(isset($condition["rma_amount_total"]) && $condition["rma_amount_total"] != ""){
            $select->where("rma_amount_total = ?",$condition["rma_amount_total"]);
        }
        if(isset($condition["rma_currency"]) && $condition["rma_currency"] != ""){
            $select->where("rma_currency = ?",$condition["rma_currency"]);
        }
        if(isset($condition["rma_receiving_account"]) && $condition["rma_receiving_account"] != ""){
            $select->where("rma_receiving_account like ?","%{$condition["rma_receiving_account"]}%");
        }
        if(isset($condition["rma_payment_account"]) && $condition["rma_payment_account"] != ""){
        	$select->where("rma_payment_account = ?",$condition["rma_payment_account"]);
        }
        if(isset($condition["rma_ebay_account"]) && $condition["rma_ebay_account"] != ""){
        	$select->where("rma_ebay_account = ?",$condition["rma_ebay_account"]);
        }
        if(isset($condition["rma_sync_time"]) && $condition["rma_sync_time"] != ""){
            $select->where("rma_sync_time = ?",$condition["rma_sync_time"]);
        }
        if(isset($condition["rma_pay_ref_id"]) && $condition["rma_pay_ref_id"] != ""){
            $select->where("rma_pay_ref_id = ?",$condition["rma_pay_ref_id"]);
        }
        if(isset($condition["rma_back_order_id"]) && $condition["rma_back_order_id"] != ""){
            $select->where("rma_back_order_id = ?",$condition["rma_back_order_id"]);
        }
        if(isset($condition["rma_status"]) && $condition["rma_status"] != ""){
            $select->where("rma_status = ?",$condition["rma_status"]);
        }
        if(isset($condition["createDateFrom"]) && $condition["createDateFrom"] != ""){
        	$select->where("rma_create_date >= ?",$condition["createDateFrom"]);
        }
        if(isset($condition["createDateEnd"]) && $condition["createDateEnd"] != ""){
        	$select->where("rma_create_date <= ?",$condition["createDateEnd"]);
        }
        
        if(isset($condition["verifyDateFrom"]) && $condition["verifyDateFrom"] != ""){
        	$select->where("rma_verify_date >= ?",$condition["verifyDateFrom"]);
        }
        if(isset($condition["verifyDateEnd"]) && $condition["verifyDateEnd"] != ""){
        	$select->where("rma_verify_date <= ?",$condition["verifyDateEnd"]);
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
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	
    	$opTable = new DbTable_RmaOrderProduct();
    	$tableRmaProduct = $opTable->info('name');
    	if($type == 'count(*)'){
    		$select->joinInner($tableRmaProduct, $table . '.rma_id = ' . $tableRmaProduct . '.rma_id', null);
    	}else{
    		$select->joinInner($tableRmaProduct, $table . '.rma_id = ' . $tableRmaProduct . '.rma_id', array(
    				'rmap_amount_total',
    				'rmap_reason_id',
    				'rmap_product_qty'
    		));
    	}
    	
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["rma_back_order_id"]) && $condition["rma_back_order_id"] != ""){
    		$select->where($table .".rma_back_order_id = ?",$condition["rma_back_order_id"]);
    	}
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where($table .".company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["rmap_product_id"]) && $condition["rmap_product_id"] != ""){
    		$select->where($tableRmaProduct . ".rmap_product_id = ?",$condition["rmap_product_id"]);
    	}
    	if(isset($condition["rma_creator_id"]) && $condition["rma_creator_id"] != ""){
    		$select->where("rma_creator_id = ?",$condition["rma_creator_id"]);
    	}
    	if(isset($condition["rma_verifyor_id"]) && $condition["rma_verifyor_id"] != ""){
    		$select->where("rma_verifyor_id = ?",$condition["rma_verifyor_id"]);
    	}
    	if(isset($condition["rma_case_ref_id"]) && $condition["rma_case_ref_id"] != ""){
    		$select->where("rma_case_ref_id = ?",$condition["rma_case_ref_id"]);
    	}
    	if(isset($condition["rma_case_type"]) && $condition["rma_case_type"] != ""){
    		$select->where("rma_case_type = ?",$condition["rma_case_type"]);
    	}
    	if(isset($condition["rma_common"]) && $condition["rma_common"] != ""){
    		$select->where("rma_common = ?",$condition["rma_common"]);
    	}
    	if(isset($condition["rma_amount_total"]) && $condition["rma_amount_total"] != ""){
    		$select->where("rma_amount_total = ?",$condition["rma_amount_total"]);
    	}
    	if(isset($condition["rma_currency"]) && $condition["rma_currency"] != ""){
    		$select->where("rma_currency = ?",$condition["rma_currency"]);
    	}
    	if(isset($condition["rma_receiving_account"]) && $condition["rma_receiving_account"] != ""){
    		$select->where("rma_receiving_account = ?",$condition["rma_receiving_account"]);
    	}
    	if(isset($condition["rma_payment_account"]) && $condition["rma_payment_account"] != ""){
    		$select->where("rma_payment_account = ?",$condition["rma_payment_account"]);
    	}
    	if(isset($condition["rma_ebay_account"]) && $condition["rma_ebay_account"] != ""){
    		$select->where("rma_ebay_account = ?",$condition["rma_ebay_account"]);
    	}
    	if(isset($condition["rma_sync_time"]) && $condition["rma_sync_time"] != ""){
    		$select->where("rma_sync_time = ?",$condition["rma_sync_time"]);
    	}
    	if(isset($condition["rma_pay_ref_id"]) && $condition["rma_pay_ref_id"] != ""){
    		$select->where("rma_pay_ref_id = ?",$condition["rma_pay_ref_id"]);
    	}
    	if(isset($condition["rma_status"]) && $condition["rma_status"] != ""){
    		$select->where("rma_status = ?",$condition["rma_status"]);
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
    public function getByConditionForView($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	if ('count(*)' != $type) {
	    	$type[] = "(select orders.refrence_no_platform from orders where orders.order_id = rma_orders.rma_back_order_id) as refrence_no_platform";
	    	$type[] = "(select orders.amountpaid from orders where orders.order_id = rma_orders.rma_back_order_id) as amountpaid";
	    	$type[] = "(select orders.currency from orders where orders.order_id = rma_orders.rma_back_order_id) as order_currency";
	    	//$type[] = "(select orders.buyer_id from orders where orders.order_id = rma_orders.rma_back_order_id) as buyer_id";
	    	//$type[] = "(select temtopwms.product.product_sku from temtopwms.product where temtopwms.product.product_id = rma_order_product.rmap_product_id) as product_sku";
    	}
    	

    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	
    	$opTable = new DbTable_RmaOrderProduct();
    	$tableRmaProduct = $opTable->info('name');
    	if ('count(*)' == $type) {
    		$select->joinInner($tableRmaProduct, $table . '.rma_id = ' . $tableRmaProduct . '.rma_id', null);
    	}else{
	    	$select->joinInner($tableRmaProduct, $table . '.rma_id = ' . $tableRmaProduct . '.rma_id', array(
	    			'rmap_product_id',
	    			'rmap_reason_id'
	    			));
    	}
    	
    	
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    	
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["rmap_product_id"]) && $condition["rmap_product_id"] != ""){
    		foreach ($condition["rmap_product_id"] as $value) {
    			$productCondition .= "'$value',";
    		}
			$productCondition = substr($productCondition,0,-1);
    		$select->where($tableRmaProduct . ".rmap_product_id in ($productCondition)");
    	}
    	if(isset($condition["rma_creator_id"]) && $condition["rma_creator_id"] != ""){
    		$select->where("rma_creator_id = ?",$condition["rma_creator_id"]);
    	}    	
    	if(isset($condition["rma_verifyor_id"]) && $condition["rma_verifyor_id"] != ""){
    		$select->where("rma_verifyor_id = ?",$condition["rma_verifyor_id"]);
    	}
    	if(isset($condition["rma_case_ref_id"]) && $condition["rma_case_ref_id"] != ""){
    		$select->where("rma_case_ref_id = ?",$condition["rma_case_ref_id"]);
    	}
    	if(isset($condition["rma_case_type"]) && $condition["rma_case_type"] != ""){
    		$select->where("rma_case_type = ?",$condition["rma_case_type"]);
    	}
    	if(isset($condition["rma_common"]) && $condition["rma_common"] != ""){
    		$select->where("rma_common = ?",$condition["rma_common"]);
    	}
    	if(isset($condition["rma_amount_total"]) && $condition["rma_amount_total"] != ""){
    		$select->where("rma_amount_total = ?",$condition["rma_amount_total"]);
    	}
    	if(isset($condition["rma_currency"]) && $condition["rma_currency"] != ""){
    		$select->where("rma_currency = ?",$condition["rma_currency"]);
    	}
    	if(isset($condition["rma_receiving_account"]) && $condition["rma_receiving_account"] != ""){
    		$select->where("rma_receiving_account like ?","%{$condition["rma_receiving_account"]}%");
    	}
    	if(isset($condition["rma_payment_account"]) && $condition["rma_payment_account"] != ""){
    		$select->where("rma_payment_account = ?",$condition["rma_payment_account"]);
    	}
    	if(isset($condition["rma_ebay_account"]) && $condition["rma_ebay_account"] != ""){
    		$select->where("rma_ebay_account = ?",$condition["rma_ebay_account"]);
    	}
    	if(isset($condition["rma_sync_time"]) && $condition["rma_sync_time"] != ""){
    		$select->where("rma_sync_time = ?",$condition["rma_sync_time"]);
    	}
    	if(isset($condition["rma_pay_ref_id"]) && $condition["rma_pay_ref_id"] != ""){
    		$select->where("rma_pay_ref_id = ?",$condition["rma_pay_ref_id"]);
    	}
    	if(isset($condition["rma_back_order_id"]) && $condition["rma_back_order_id"] != ""){
    		$select->where("rma_back_order_id = ?",$condition["rma_back_order_id"]);
    	}
    	if(isset($condition["rma_status"]) && $condition["rma_status"] != ""){
    		$select->where("rma_status = ?",$condition["rma_status"]);
    	}
    	if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
    		$select->where($table . ".buyer_id = ?",$condition["buyer_id"]);
    	}
    	if(isset($condition["createDateFrom"]) && $condition["createDateFrom"] != ""){
    		$select->where("rma_create_date >= ?",$condition["createDateFrom"]);
    	}
    	if(isset($condition["createDateEnd"]) && $condition["createDateEnd"] != ""){
    		$select->where("rma_create_date <= ?",$condition["createDateEnd"]);
    	}
    
    	if(isset($condition["verifyDateFrom"]) && $condition["verifyDateFrom"] != ""){
    		$select->where("rma_verify_date >= ?",$condition["verifyDateFrom"]);
    	}
    	if(isset($condition["verifyDateEnd"]) && $condition["verifyDateEnd"] != ""){
    		$select->where("rma_verify_date <= ?",$condition["verifyDateEnd"]);
    	}
    	
    	if(isset($condition["submitDateFrom"]) && $condition["submitDateFrom"] != ""){
    		$select->where("rma_submit_date >= ?",$condition["submitDateFrom"]);
    	}
    	if(isset($condition["submitDateEnd"]) && $condition["submitDateEnd"] != ""){
    		$select->where("rma_submit_date <= ?",$condition["submitDateEnd"]);
    	}
    	if(isset($condition["rma_type"]) && $condition["rma_type"] != ""){
    		if($condition["rma_type"] == '1'){
    			$select->where("rma_refund_type = ?",'-1');
    		}else{
    			$select->where("rma_refund_type != ?",'-1');
    		}
    		
    	}
    	if(isset($condition["rma_ebay_accounts"]) && !empty($condition["rma_ebay_accounts"])){
    		$accountCondition = "";
    		foreach ($condition["rma_ebay_accounts"] as $value) {
    			$accountCondition .= "'$value',";
    		}
    		$accountCondition = substr($accountCondition,0,-1);
    		$select->where("rma_ebay_account in ($accountCondition)");
    	}
    	
    	/*CONDITION_END*/
//     	print_r($select->__toString());
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
    
    /**
     * paypal退款查询
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionForRefund($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	if ('count(*)' != $type) {
    		$type[] = "(select orders.refrence_no_platform from orders where orders.order_id = rma_orders.rma_back_order_id) as refrence_no_platform";
    		$type[] = "(select orders.amountpaid from orders where orders.order_id = rma_orders.rma_back_order_id) as amountpaid";
    		$type[] = "(select orders.currency from orders where orders.order_id = rma_orders.rma_back_order_id) as order_currency";    
    		
    		$type[] = "(select ebay_order_payment.paymentamount from ebay_order_payment where ebay_order_payment.referenceid = rma_orders.rma_pay_ref_id) as paymentamount";
    		
    		$type[] = "rma_order_product.rmap_reason_id";
    	}
    	 
    
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	 
    	$opTable = new DbTable_RmaOrderProduct();
    	$tableRmaProduct = $opTable->info('name');
    	$select->joinInner($tableRmaProduct, $table . '.rma_id = ' . $tableRmaProduct . '.rma_id', null);
    	 
    	 
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    	if(isset($condition["rmap_product_id"]) && $condition["rmap_product_id"] != ""){
    		foreach ($condition["rmap_product_id"] as $value) {
    			$productCondition .= "'$value',";
    		}
    		$productCondition = substr($productCondition,0,-1);
    		$select->where($tableRmaProduct . ".rmap_product_id in ($productCondition)");
    	}
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["is_not_rma_refund_type"]) && $condition["is_not_rma_refund_type"] != ""){
    		$select->where("rma_refund_type != ?",$condition["is_not_rma_refund_type"]);
    	}
    	if(isset($condition["rma_creator_id"]) && $condition["rma_creator_id"] != ""){
    		$select->where("rma_creator_id = ?",$condition["rma_creator_id"]);
    	}
    	if(isset($condition["rma_verifyor_id"]) && $condition["rma_verifyor_id"] != ""){
    		$select->where("rma_verifyor_id = ?",$condition["rma_verifyor_id"]);
    	}
    	if(isset($condition["rma_case_ref_id"]) && $condition["rma_case_ref_id"] != ""){
    		$select->where("rma_case_ref_id = ?",$condition["rma_case_ref_id"]);
    	}
    	if(isset($condition["rma_case_type"]) && $condition["rma_case_type"] != ""){
    		$select->where("rma_case_type = ?",$condition["rma_case_type"]);
    	}
    	if(isset($condition["rma_common"]) && $condition["rma_common"] != ""){
    		$select->where("rma_common = ?",$condition["rma_common"]);
    	}
    	if(isset($condition["rma_amount_total"]) && $condition["rma_amount_total"] != ""){
    		$select->where("rma_amount_total = ?",$condition["rma_amount_total"]);
    	}
    	if(isset($condition["rma_currency"]) && $condition["rma_currency"] != ""){
    		$select->where("rma_currency = ?",$condition["rma_currency"]);
    	}
    	if(isset($condition["rma_receiving_account"]) && $condition["rma_receiving_account"] != ""){
    		$select->where("rma_receiving_account like ?","%{$condition["rma_receiving_account"]}%");
    	}
    	if(isset($condition["rma_payment_account"]) && $condition["rma_payment_account"] != ""){
    		$select->where("rma_payment_account = ?",$condition["rma_payment_account"]);
    	}
    	if(isset($condition["rma_ebay_account"]) && $condition["rma_ebay_account"] != ""){
    		$select->where("rma_ebay_account = ?",$condition["rma_ebay_account"]);
    	}
    	if(isset($condition["rma_sync_time"]) && $condition["rma_sync_time"] != ""){
    		$select->where("rma_sync_time = ?",$condition["rma_sync_time"]);
    	}
    	if(isset($condition["rma_pay_ref_id"]) && $condition["rma_pay_ref_id"] != ""){
    		$select->where("rma_pay_ref_id = ?",$condition["rma_pay_ref_id"]);
    	}
    	if(isset($condition["rma_back_order_id"]) && $condition["rma_back_order_id"] != ""){
    		$select->where("rma_back_order_id = ?",$condition["rma_back_order_id"]);
    	}
    	if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
    		$select->where("buyer_id = ?",$condition["buyer_id"]);
    	}
    	if(isset($condition["rma_status"]) && $condition["rma_status"] != ""){
    		$select->where("rma_status = ?",$condition["rma_status"]);
    	}else{
    		$select->where("rma_status != '0'");
    	}
    	if(isset($condition["createDateFrom"]) && $condition["createDateFrom"] != ""){
    		$select->where("rma_create_date >= ?",$condition["createDateFrom"]);
    	}
    	if(isset($condition["createDateEnd"]) && $condition["createDateEnd"] != ""){
    		$select->where("rma_create_date <= ?",$condition["createDateEnd"]);
    	}
    
    	if(isset($condition["verifyDateFrom"]) && $condition["verifyDateFrom"] != ""){
    		$select->where("rma_verify_date >= ?",$condition["verifyDateFrom"]);
    	}
    	if(isset($condition["verifyDateEnd"]) && $condition["verifyDateEnd"] != ""){
    		$select->where("rma_verify_date <= ?",$condition["verifyDateEnd"]);
    	}
    	 
    	if(isset($condition["submitDateFrom"]) && $condition["submitDateFrom"] != ""){
    		$select->where("rma_submit_date >= ?",$condition["submitDateFrom"]);
    	}
    	if(isset($condition["submitDateEnd"]) && $condition["submitDateEnd"] != ""){
    		$select->where("rma_submit_date <= ?",$condition["submitDateEnd"]);
    	}
    	if(isset($condition["rma_ebay_accounts"]) && $condition["rma_ebay_accounts"] != "" && count($condition["rma_ebay_accounts"]) > 0){
    		$accountCondition = "";
    		foreach ($condition["rma_ebay_accounts"] as $value) {
    			$accountCondition .= "'$value',";
    		}
    		$accountCondition = substr($accountCondition,0,-1);
    		$select->where("rma_ebay_account in ($accountCondition)");
    	}
    	
//     	echo $select->__toString();
//     	exit;
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