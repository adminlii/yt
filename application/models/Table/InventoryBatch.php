<?php
class Table_InventoryBatch
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_InventoryBatch();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_InventoryBatch();
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
    public function update($row, $value, $field = "ib_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ib_id")
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
    public function getByField($value, $field = 'ib_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }
    
    /**
     * 
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public function getDownInventoryByCondition($value, $field = 'ib_id', $colums = "*")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $colums);
        $select->joinLeft('receiving','receiving.receiving_id =' . $table . '.receiving_id',array('po_code','customer_code','to_warehouse_id'));
        $select->joinLeft('receiving_detail','receiving_detail.receiving_id =inventory_batch.receiving_id and receiving_detail.product_id=inventory_batch.product_id ',array('sm_code'));
    	$select->where("{$field} in (?)", $value);
    	return $this->_table->getAdapter()->fetchAll($select);
    }
    
    
    public function getByWhere($where)
    {
    	if(empty($where)) return array();
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, '*');
    	foreach($where as $field=>$value) {
    		$select->where($field.'=?', $value);
    	}
    	return $this->_table->getAdapter()->fetchRow($select);
    }
    
    public function getForUpdate($ib_id) {
    	$sql = 'select * from inventory_batch where ib_id='.$ib_id.' for update;';
    	return $this->_table->getAdapter()->fetchRow($sql);
    }

    public function getAll()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, "*");
        return $this->_table->getAdapter()->fetchAll($select);
    }
    
    
    public function getByConditionPurchase($productId = "",$wareHouse = ""){
    	$sql = "select ib.ib_quantity,ph.unit_price from inventory_batch ib
				LEFT JOIN purchase_order_product ph on ib.po_code = ph.po_code
				where ib.ib_status  = 1 and ib.product_id = ".$productId." and  ib.warehouse_id = ".$wareHouse." and ph.product_id = ".$productId;
    	return $this->_table->getAdapter()->fetchAll($sql);
    }
    
    public function getByConditionProduct($productId = "",$wareHouse = ""){
    	$sql = "select ib.ib_quantity,pt.product_purchase_value as unit_price from inventory_batch ib
				LEFT JOIN product pt on ib.product_id = pt.product_id
				where ib.ib_status  = 1 and ib.product_id = ".$productId." and  ib.warehouse_id = ".$wareHouse;
    	return $this->_table->getAdapter()->fetchAll($sql);
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
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["lc_code_like"]) && $condition["lc_code_like"] != ""){
            $select->where("lc_code like ?",'%'.$condition["lc_code_like"].'%');
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["box_code"]) && $condition["box_code"] != ""){
            $select->where("box_code = ?",$condition["box_code"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["supplier_id"]) && $condition["supplier_id"] !== ""){
            $select->where("supplier_id = ?",$condition["supplier_id"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if(isset($condition["lot_number"]) && $condition["lot_number"] != ""){
            $select->where("lot_number = ?",$condition["lot_number"]);
        }
        if(isset($condition["ib_type"]) && $condition["ib_type"] !== ""){
            $select->where("ib_type = ?",$condition["ib_type"]);
        }
        if(isset($condition["ib_status"]) && $condition["ib_status"] != ""){
            $select->where("ib_status = ?",$condition["ib_status"]);
        }
        if(isset($condition["ib_hold_status"]) && $condition["ib_hold_status"] != ""){
            $select->where("ib_hold_status = ?",$condition["ib_hold_status"]);
        }
        if(isset($condition["ib_quantity"]) && $condition["ib_quantity"] != ""){
            $select->where("ib_quantity = ?",$condition["ib_quantity"]);
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
     *@desc 统计库存
     * @return array|string
     */
    public function getByGroupLcCodeAndProductIdCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["box_code"]) && $condition["box_code"] != ""){
            $select->where("box_code = ?",$condition["box_code"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["supplier_id"]) && $condition["supplier_id"] != ""){
            $select->where("supplier_id = ?",$condition["supplier_id"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if(isset($condition["ib_status"]) && $condition["ib_status"] != ""){
            $select->where("ib_status = ?",$condition["ib_status"]);
        }
        if(isset($condition["ib_hold_status"]) && $condition["ib_hold_status"] != ""){
            $select->where("ib_hold_status = ?",$condition["ib_hold_status"]);
        }
        if(isset($condition["ib_quantity"]) && $condition["ib_quantity"] != ""){
            $select->where("ib_quantity = ?",$condition["ib_quantity"]);
        }

        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            $select->group(array('lc_code','product_id','ib_type'));
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
     * 下架中转单列表 条件查询
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionLeftJoin($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	$select->joinLeft('receiving','receiving.receiving_id =' . $table . '.receiving_id',array('po_code','customer_code','to_warehouse_id'));
    	$select->joinLeft('receiving_detail','receiving_detail.receiving_id =inventory_batch.receiving_id and receiving_detail.product_id=inventory_batch.product_id ',array('sm_code'));
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
    		$select->where("lc_code = ?",$condition["lc_code"]);
    	}
    	if(isset($condition["product_id"]) && $condition["product_id"] != ""){
    		$select->where("product_id = ?",$condition["product_id"]);
    	}
    	if(isset($condition["box_code"]) && $condition["box_code"] != ""){
    		$select->where("box_code = ?",$condition["box_code"]);
    	}
    	if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
    		$select->where("inventory_batch.reference_no = ?",$condition["reference_no"]);
    	}
    	if(isset($condition["application_code"]) && $condition["application_code"] != ""){
    		$select->where("application_code = ?",$condition["application_code"]);
    	}
    	if(isset($condition["supplier_id"]) && $condition["supplier_id"] != ""){
    		$select->where("supplier_id = ?",$condition["supplier_id"]);
    	}
    	if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
    		$select->where("inventory_batch.warehouse_id = ?",$condition["warehouse_id"]);
    	}

        if(isset($condition["sm_code"]) && $condition["sm_code"] != ""){
            $select->where("receiving_detail.sm_code = ?",$condition["sm_code"]);
        }

    	if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
    		$select->where("inventory_batch.receiving_code = ?",$condition["receiving_code"]);
    	}
    	if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
    		$select->where("receiving_id = ?",$condition["receiving_id"]);
    	}
    	if(isset($condition["lot_number"]) && $condition["lot_number"] != ""){
    		$select->where("lot_number = ?",$condition["lot_number"]);
    	}
    	if(isset($condition["ib_status"]) && $condition["ib_status"] != ""){
    		$select->where("ib_status = ?",$condition["ib_status"]);
    	}
    	if(isset($condition["ib_hold_status"]) && $condition["ib_hold_status"] != ""){
    		$select->where("ib_hold_status = ?",$condition["ib_hold_status"]);
    	}
        if(isset($condition["ib_type"]) && $condition["ib_type"] !== ""){
            $select->where("ib_type = ?",$condition["ib_type"]);
        }
    	if(isset($condition["ib_quantity"]) && $condition["ib_quanstity"] != ""){
    		$select->where("ib_quantity = ?",$condition["ib_quantity"]);
    	}
    	if(isset($condition["po_code"]) && $condition["po_code"] != ""){
    		$select->where("receiving.po_code = ?",$condition["po_code"]);
    	}
        if(isset($condition["to_warehouse_id"]) && $condition["to_warehouse_id"] !== ""){
            $select->where("receiving.to_warehouse_id = ?",$condition["to_warehouse_id"]);
        }
    	if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
    		$select->where("inventory_batch.product_barcode = ?",$condition["product_barcode"]);
    	}
    	if (isset($condition["dateFor"]) && $condition["dateFor"] != "") {
    		$select->where("inventory_batch.ib_fifo_time >= ?", $condition["dateFor"]);
    	}
    	if (isset($condition["dateTo"]) && $condition["dateTo"] != "") {
    		$select->where("inventory_batch.ib_fifo_time <= ?", $condition["dateTo"]);
    	}
    	
    	$select->where("((select inventory_batch.ib_quantity-(
			select SUM(inventory_batch_outbound.ibo_quantity) from inventory_batch_outbound
			where inventory_batch_outbound.ib_id = inventory_batch.ib_id
			and inventory_batch_outbound.product_id = inventory_batch.product_id
		) from DUAL) > 0 or ISNULL((select inventory_batch.ib_quantity-(
			select SUM(inventory_batch_outbound.ibo_quantity) from inventory_batch_outbound
			where inventory_batch_outbound.ib_id = inventory_batch.ib_id
			and inventory_batch_outbound.product_id = inventory_batch.product_id
		) from DUAL)))");
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


    public function listTakeStockLocation($condition) {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table.' as ib', array('lc_code','product_barcode','ib_quantity','ib_status','ib_hold_status'));
        $select->joinLeft('product as p', 'ib.product_id=p.product_id', 'customer_code');
        $select->joinLeft('inventory_batch_outbound AS ibo', 'ib.ib_id=ibo.ib_id', 'ibo_id');
        if(isset($condition["wa_code"]) && $condition["wa_code"] != ""){
            $select->joinLeft('location as l', 'ib.warehouse_id=l.warehouse_id and ib.lc_code=l.lc_code', '');
            $select->where("l.wa_code = ?",$condition["wa_code"]);
        }
        /*CONDITION_START*/
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("ib.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("ib.lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("ib.product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("p.customer_code = ?",$condition["customer_code"]);
        }
        //$select->where('ibo.ibo_id is null');
        $select->order(array('ib.lc_code','ib.product_barcode'));
        $sql = $select->__toString();
        return $this->_table->getAdapter()->fetchAll($sql);
    }

    public function listByWhere($where)
    {
        if(empty($where)) return array();
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, '*');
        $select->joinLeft('product as p', $table.'.product_id=p.product_id', array('customer_code'));
        foreach($where as $field=>$value) {
            $select->where($table.'.'.$field.'=?', $value);
        }
        return $this->_table->getAdapter()->fetchAll($select);
    }

    public function updateByField($row, $field) {
        $aWhere = array();
        foreach($field as $key=>$value) {
            $aWhere[] = $this->_table->getAdapter()->quoteInto("{$key}= ?", $value);
        }
        return $this->_table->update($row, implode(' AND ', $aWhere));
    }

    /**
     * @desc 用于盘点
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getLeftJoinGroupLcCodeSkuByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        if($type=='count(*)'){
            $showFields = 'COUNT(DISTINCT(inventory_batch.lc_code+inventory_batch.product_id))';
        }else{
            $showFields = array(
                'ib_id',
                'lc_code',
                'product_barcode',
                'sum(ib_quantity) as sum_quantity',
                'product_id',
                'p.customer_code',
            );
        }
        $select->from($table, $showFields);
        $select->joinLeft('product as p', $table.'.product_id=p.product_id', null);
        $select->joinLeft('inventory_batch_outbound as ibo', $table.'.ib_id=ibo.ib_id', null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["ibo_id_is_null"]) && $condition["ibo_id_is_null"] != ""){
            $select->where("ibo.ibo_id is null");
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("p.customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where($table.".lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["lc_code_like"]) && $condition["lc_code_like"] != ""){
            $select->where($table.".lc_code like ?",'%'.$condition["lc_code_like"].'%');
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where($table.".product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["box_code"]) && $condition["box_code"] != ""){
            $select->where($table.".box_code = ?",$condition["box_code"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where($table.".product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where($table.".reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where($table.".application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where($table.".warehouse_id = ?",$condition["warehouse_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where($table.".warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where($table.".receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["ib_status"]) && $condition["ib_status"] != ""){
            $select->where("ib_status = ?",$condition["ib_status"]);
        }
        if(isset($condition["ib_hold_status"]) && $condition["ib_hold_status"] != ""){
            $select->where("ib_hold_status = ?",$condition["ib_hold_status"]);
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
            $select->group(array('inventory_batch.product_id','inventory_batch.lc_code'));
            $sql = $select->__toString();
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }

    /**
     * @desc 中转订单出货
     * @param $ib_id
     * @return mixed
     */
    public function getLeftJoinReceivingFetchRow($ib_id)
    {
        $sql = 'select inventory_batch.*,receiving.to_warehouse_id from inventory_batch left join receiving on inventory_batch.receiving_id=receiving.receiving_id where ib_id=' . $ib_id . ' ';
        return $this->_table->getAdapter()->fetchRow($sql);
    }
    
}