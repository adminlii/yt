<?php
class Table_EbayOrderOriginal
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayOrderOriginal();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayOrderOriginal();
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
    public function update($row, $value, $field = "eoo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "eoo_id")
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
    public function getByField($value, $field = 'eoo_id', $colums = "*")
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
        
        if(isset($condition["OrderID"]) && $condition["OrderID"] != ""){
            $select->where("OrderID = ?",$condition["OrderID"]);
        }
        if(isset($condition["OrderStatus"]) && $condition["OrderStatus"] != ""){
            $select->where("OrderStatus = ?",$condition["OrderStatus"]);
        }
        if(isset($condition["adjustmentamount"]) && $condition["adjustmentamount"] != ""){
            $select->where("adjustmentamount = ?",$condition["adjustmentamount"]);
        }
        if(isset($condition["amountpaid"]) && $condition["amountpaid"] != ""){
            $select->where("amountpaid = ?",$condition["amountpaid"]);
        }
        if(isset($condition["amountsaved"]) && $condition["amountsaved"] != ""){
            $select->where("amountsaved = ?",$condition["amountsaved"]);
        }
        if(isset($condition["eBayPaymentStatus"]) && $condition["eBayPaymentStatus"] != ""){
            $select->where("eBayPaymentStatus = ?",$condition["eBayPaymentStatus"]);
        }
        if(isset($condition["PaymentMethod"]) && $condition["PaymentMethod"] != ""){
            $select->where("PaymentMethod = ?",$condition["PaymentMethod"]);
        }
        if(isset($condition["CheckoutStatus"]) && $condition["CheckoutStatus"] != ""){
            $select->where("CheckoutStatus = ?",$condition["CheckoutStatus"]);
        }
        if(isset($condition["ShippingService"]) && $condition["ShippingService"] != ""){
            $select->where("ShippingService = ?",$condition["ShippingService"]);
        }
        if(isset($condition["shippingservicecost"]) && $condition["shippingservicecost"] != ""){
            $select->where("shippingservicecost = ?",$condition["shippingservicecost"]);
        }
        if(isset($condition["sellingmanagersalesrecordnumber"]) && $condition["sellingmanagersalesrecordnumber"] != ""){
            $select->where("sellingmanagersalesrecordnumber = ?",$condition["sellingmanagersalesrecordnumber"]);
        }
        if(isset($condition["SellerEmail"]) && $condition["SellerEmail"] != ""){
            $select->where("SellerEmail = ?",$condition["SellerEmail"]);
        }
        if(isset($condition["subtotal"]) && $condition["subtotal"] != ""){
            $select->where("subtotal = ?",$condition["subtotal"]);
        }
        if(isset($condition["total"]) && $condition["total"] != ""){
            $select->where("total = ?",$condition["total"]);
        }
        if(isset($condition["externaltransactionid"]) && $condition["externaltransactionid"] != ""){
            $select->where("externaltransactionid = ?",$condition["externaltransactionid"]);
        }
        if(isset($condition["feeorcreditamount"]) && $condition["feeorcreditamount"] != ""){
            $select->where("feeorcreditamount = ?",$condition["feeorcreditamount"]);
        }
        if(isset($condition["paymentorrefundamount"]) && $condition["paymentorrefundamount"] != ""){
            $select->where("paymentorrefundamount = ?",$condition["paymentorrefundamount"]);
        }
        if(isset($condition["buyeruserid"]) && $condition["buyeruserid"] != ""){
            $select->where("buyeruserid = ?",$condition["buyeruserid"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["currency"]) && $condition["currency"] != ""){
            $select->where("currency = ?",$condition["currency"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["buyer_note"]) && $condition["buyer_note"] != ""){
            $select->where("buyer_note = ?",$condition["buyer_note"]);
        }
        if(isset($condition["LastModifiedTime"]) && $condition["LastModifiedTime"] != ""){
            $select->where("LastModifiedTime >= ?",$condition["LastModifiedTime"]);
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
    
    public function getByConditionBak($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = ""){
    	
    	
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	
    	$opTable = new DbTable_EbayOrderPayment();
    	$tablePayment = $opTable->info('name');
    	if($type == 'count(*)'){
    		$select->joinInner($tablePayment, $table . '.OrderID = ' . $tablePayment . '.OrderID', null);
    	}else{
    		$select->joinInner($tablePayment, $table . '.OrderID = ' . $tablePayment . '.OrderID', array(
    				'referenceid'
    		));
    	}
    	
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    	if(isset($condition["OrderID"]) && $condition["OrderID"] != ""){
    		$select->where($table . ".OrderID = ?",$condition["OrderID"]);
    	}
    	if(isset($condition["paymentstatus"]) && $condition["paymentstatus"] != ""){
    		$select->where($tablePayment . ".paymentstatus = ?",$condition["paymentstatus"]);
    	}
    	if(isset($condition["unPaymentstatus"]) && $condition["unPaymentstatus"] != ""){
    		$select->where($tablePayment . ".paymentstatus != ?",$condition["unPaymentstatus"]);
    	}
    	if(isset($condition["OrderStatus"]) && $condition["OrderStatus"] != ""){
    		$select->where("OrderStatus = ?",$condition["OrderStatus"]);
    	}
    	if(isset($condition["adjustmentamount"]) && $condition["adjustmentamount"] != ""){
    		$select->where("adjustmentamount = ?",$condition["adjustmentamount"]);
    	}
    	if(isset($condition["amountpaid"]) && $condition["amountpaid"] != ""){
    		$select->where("amountpaid = ?",$condition["amountpaid"]);
    	}
    	if(isset($condition["amountsaved"]) && $condition["amountsaved"] != ""){
    		$select->where("amountsaved = ?",$condition["amountsaved"]);
    	}
    	if(isset($condition["eBayPaymentStatus"]) && $condition["eBayPaymentStatus"] != ""){
    		$select->where("eBayPaymentStatus = ?",$condition["eBayPaymentStatus"]);
    	}
    	if(isset($condition["PaymentMethod"]) && $condition["PaymentMethod"] != ""){
    		$select->where("PaymentMethod = ?",$condition["PaymentMethod"]);
    	}
    	if(isset($condition["CheckoutStatus"]) && $condition["CheckoutStatus"] != ""){
    		$select->where("CheckoutStatus = ?",$condition["CheckoutStatus"]);
    	}
    	if(isset($condition["ShippingService"]) && $condition["ShippingService"] != ""){
    		$select->where("ShippingService = ?",$condition["ShippingService"]);
    	}
    	if(isset($condition["shippingservicecost"]) && $condition["shippingservicecost"] != ""){
    		$select->where("shippingservicecost = ?",$condition["shippingservicecost"]);
    	}
    	if(isset($condition["sellingmanagersalesrecordnumber"]) && $condition["sellingmanagersalesrecordnumber"] != ""){
    		$select->where("sellingmanagersalesrecordnumber = ?",$condition["sellingmanagersalesrecordnumber"]);
    	}
    	if(isset($condition["SellerEmail"]) && $condition["SellerEmail"] != ""){
    		$select->where("SellerEmail = ?",$condition["SellerEmail"]);
    	}
    	if(isset($condition["subtotal"]) && $condition["subtotal"] != ""){
    		$select->where("subtotal = ?",$condition["subtotal"]);
    	}
    	if(isset($condition["total"]) && $condition["total"] != ""){
    		$select->where("total = ?",$condition["total"]);
    	}
    	if(isset($condition["externaltransactionid"]) && $condition["externaltransactionid"] != ""){
    		$select->where("externaltransactionid = ?",$condition["externaltransactionid"]);
    	}
    	if(isset($condition["feeorcreditamount"]) && $condition["feeorcreditamount"] != ""){
    		$select->where("feeorcreditamount = ?",$condition["feeorcreditamount"]);
    	}
    	if(isset($condition["paymentorrefundamount"]) && $condition["paymentorrefundamount"] != ""){
    		$select->where("paymentorrefundamount = ?",$condition["paymentorrefundamount"]);
    	}
    	if(isset($condition["buyeruserid"]) && $condition["buyeruserid"] != ""){
    		$select->where("buyeruserid = ?",$condition["buyeruserid"]);
    	}
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where($table . ".company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["currency"]) && $condition["currency"] != ""){
    		$select->where("currency = ?",$condition["currency"]);
    	}
    	if(isset($condition["user_account"]) && $condition["user_account"] != ""){
    		$select->where("user_account = ?",$condition["user_account"]);
    	}
    	if(isset($condition["buyer_note"]) && $condition["buyer_note"] != ""){
    		$select->where("buyer_note = ?",$condition["buyer_note"]);
    	}
    	if(isset($condition["LastModifiedTime"]) && $condition["LastModifiedTime"] != ""){
    		$select->where("LastModifiedTime >= ?",$condition["LastModifiedTime"]);
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