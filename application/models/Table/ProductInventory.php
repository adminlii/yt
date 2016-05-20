<?php
class Table_ProductInventory
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductInventory();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductInventory();
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
    public function update($row, $value, $field = "pi_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pi_id")
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
    public function getByField($value, $field = 'pi_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }

    public function getByWhProduct($warehouseId=0, $productId =0, $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("product_id = ?", $productId);
        $select->where("warehouse_id = ?", $warehouseId);        
        return $this->_table->getAdapter()->fetchRow($select);
    }
    
    public function getForUpdate($pi_id) {
    	$sql = 'select * from product_inventory where pi_id='.$pi_id.' for update;';
    	return $this->_table->getAdapter()->fetchRow($sql);
    }

    public function getAll()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, "*");
        return $this->_table->getAdapter()->fetchAll($select);
    }
    
    public function getInventoryBatch($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$sql1 = "select * from (";
    	if(array("count(*)") == $type){
    		$sql2 = "select inventory_batch.product_barcode 
    					from inventory_batch 
    					left join warehouse on warehouse.warehouse_id = inventory_batch.warehouse_id where ib_quantity > 0 ";
    	}else{
    		$sql2 = "select ".implode(",",$type)." 
    					from inventory_batch 
    					left join warehouse on warehouse.warehouse_id = inventory_batch.warehouse_id where ib_quantity > 0 ";
    	}
		
		$sql3 = "GROUP BY product_barcode";
		$sql4 =") as cc";
		
		$condition_where = "";
		if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
			$condition_where .= "and product_barcode like '%".$condition["product_barcode"]."%' ";
		}
		
		if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
			$condition_where .= "and inventory_batch.warehouse_id = ".$condition["warehouse_id"]." ";
		}
		
		if(isset($condition["dateFor"]) && $condition["dateFor"] != ""){
			$condition_where .= "and ib_add_time >= '".$condition["dateFor"]."' ";
		}
		
		if(isset($condition["dateTo"]) && $condition["dateTo"] != ""){
			$condition_where .= "and ib_add_time <= '".$condition["dateTo"]."' ";
		}
		$sql2 .= $condition_where;
    	/*CONDITION_END*/
    	if (array("count(*)") == $type) {
    		$sql = "select count(*) from (".$sql2.$sql3.") as cc";
    		return $this->_table->getAdapter()->fetchOne($sql);
    	} else {
    		if (!empty($orderBy)) {
    			$sql3 = $sql." order by ".$orderBy;
    		}
    		if ($pageSize > 0 and $page > 0) {
    			$start = ($page - 1) * $pageSize;
    			$sql3 = $sql3." limit ".$pageSize." OFFSET ".$start;
    		}
    		
    		$sql = $sql1.$sql2.$sql3.$sql4;
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
    public function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["pi_id"]) && $condition["pi_id"] != ""){
        	$select->where("pi_id = ?",$condition["pi_id"]);
        }
        if(isset($condition["pi_sellableNumber"]) && $condition["pi_sellableNumber"] != ""){
        	$select->where("pi_sellable > ?",$condition["pi_sellableNumber"]);
        }
        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }        
        if(isset($condition["product_sku_arr"]) && !empty($condition["product_sku_arr"])&&is_array($condition["product_sku_arr"])){
            $select->where("product_sku in (?)",$condition["product_sku_arr"]);
        }
        if(isset($condition["product_sku_like"]) && $condition["product_sku_like"] != ""){
            $select->where("product_sku like ?",'%'.$condition["product_sku_like"].'%');
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["product_barcode_like"]) && $condition["product_barcode_like"] != ""){
            $select->where("product_barcode like ?",'%'.$condition["product_barcode_like"].'%');
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if(isset($condition["pi_onway"]) && $condition["pi_onway"] != ""){
            $select->where("pi_onway = ?",$condition["pi_onway"]);
        }
        if(isset($condition["pi_pending"]) && $condition["pi_pending"] != ""){
            $select->where("pi_pending = ?",$condition["pi_pending"]);
        }
        if(isset($condition["pi_sellable"]) && $condition["pi_sellable"] != ""){
            $select->where("pi_sellable = ?",$condition["pi_sellable"]);
        }
        if(isset($condition["pi_unsellable"]) && $condition["pi_unsellable"] != ""){
            $select->where("pi_unsellable = ?",$condition["pi_unsellable"]);
        }

        if(isset($condition["pi_unsellable_gt"]) && $condition["pi_unsellable_gt"] != ""){
            $select->where("pi_unsellable > ?",$condition["pi_unsellable_gt"]);
        }
        if(isset($condition["pi_reserved"]) && $condition["pi_reserved"] != ""){
            $select->where("pi_reserved = ?",$condition["pi_reserved"]);
        }
        if(isset($condition["pi_shipped"]) && $condition["pi_shipped"] != ""){
            $select->where("pi_shipped = ?",$condition["pi_shipped"]);
        }
        if(isset($condition["pi_hold"]) && $condition["pi_hold"] != ""){
            $select->where("pi_hold = ?",$condition["pi_hold"]);
        }
        if(isset($condition["pi_no_stock"]) && $condition["pi_no_stock"] != ""){
            $select->where("pi_no_stock = ?",$condition["pi_no_stock"]);
        }
        if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
            $select->where("buyer_id = ?",$condition["buyer_id"]);
        }

        if(isset($condition["qty_type"]) && $condition["qty_type"] != ""){

            $condition["qty_from"] = intval($condition["qty_from"]);
            $condition["qty_to"] = intval($condition["qty_to"]);
            if(isset($condition["qty_from"]) && !empty($condition["qty_from"])){
                $select->where($condition["qty_type"]." >= ?",$condition["qty_from"]);
            }
            if(isset($condition["qty_to"]) && !empty($condition["qty_to"])){
                $select->where($condition["qty_type"]." <= ?",$condition["qty_to"]);
            }
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
            //特殊处理
            $sql = preg_replace('/`pi_in_used`/', 'pi_sellable+pi_reserved', $sql);
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
    public function getByConditionJoinProduct($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	$select->joinLeft("product","product_inventory.product_id = product.product_id",array('product_sales_value'));
    	$select->joinLeft("warehouse","product_inventory.warehouse_id = warehouse.warehouse_id",array('warehouse_code','warehouse_desc'));
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["pi_sellableNumber"]) && $condition["pi_sellableNumber"] != ""){
    		$select->where("pi_sellable > ?",$condition["pi_sellableNumber"]);
    	}
    	if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
    		$select->where("product_inventory.product_barcode like ?","%{$condition["product_barcode"]}%");
    	}
    	if(isset($condition["product_id"]) && $condition["product_id"] != ""){
    		$select->where("product_id = ?",$condition["product_id"]);
    	}
    	if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
    		$select->where("product_inventory.warehouse_id = ?",$condition["warehouse_id"]);
    	}
    	if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
    		$select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
    	}
    	if(isset($condition["pi_onway"]) && $condition["pi_onway"] != ""){
    		$select->where("pi_onway = ?",$condition["pi_onway"]);
    	}
    	if(isset($condition["pi_pending"]) && $condition["pi_pending"] != ""){
    		$select->where("pi_pending = ?",$condition["pi_pending"]);
    	}
    	if(isset($condition["pi_sellable"]) && $condition["pi_sellable"] != ""){
    		$select->where("pi_sellable = ?",$condition["pi_sellable"]);
    	}
    	if(isset($condition["pi_unsellable"]) && $condition["pi_unsellable"] != ""){
    		$select->where("pi_unsellable = ?",$condition["pi_unsellable"]);
    	}
    	if(isset($condition["pi_reserved"]) && $condition["pi_reserved"] != ""){
    		$select->where("pi_reserved = ?",$condition["pi_reserved"]);
    	}
    	if(isset($condition["pi_shipped"]) && $condition["pi_shipped"] != ""){
    		$select->where("pi_shipped = ?",$condition["pi_shipped"]);
    	}
    	if(isset($condition["pi_hold"]) && $condition["pi_hold"] != ""){
    		$select->where("pi_hold = ?",$condition["pi_hold"]);
    	}
    	if(isset($condition["dateFor"]) && $condition["dateFor"] != ""){
    		$select->where("pi_add_time >= ?",$condition["dateFor"]);
    	}
    	if(isset($condition["dateTo"]) && $condition["dateTo"] != ""){
    		$select->where("pi_add_time <= ?",$condition["dateTo"]);
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