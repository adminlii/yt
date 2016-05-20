<?php
class Table_ReceivingDetailBatch
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingDetailBatch();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingDetailBatch();
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
    public function update($row, $value, $field = "rdb_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rdb_id")
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
    public function getByField($value, $field = 'rdb_id', $colums = "*")
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
        
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["receiving_line_no"]) && $condition["receiving_line_no"] != ""){
            $select->where("receiving_line_no = ?",$condition["receiving_line_no"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["rdb_weight"]) && $condition["rdb_weight"] != ""){
            $select->where("rdb_weight = ?",$condition["rdb_weight"]);
        }
        if(isset($condition["rdb_putaway_qty"]) && $condition["rdb_putaway_qty"] != ""){
            $select->where("rdb_putaway_qty = ?",$condition["rdb_putaway_qty"]);
        }
        if(isset($condition["rdb_received_qty"]) && $condition["rdb_received_qty"] != ""){
            $select->where("rdb_received_qty = ?",$condition["rdb_received_qty"]);
        }
        if(isset($condition["packaged"]) && $condition["packaged"] != ""){
            $select->where("packaged = ?",$condition["packaged"]);
        }
        if(isset($condition["non_packaged_qty"]) && $condition["non_packaged_qty"] != ""){
            $select->where("non_packaged_qty = ?",$condition["non_packaged_qty"]);
        }
        if(isset($condition["labeled"]) && $condition["labeled"] != ""){
            $select->where("labeled = ?",$condition["labeled"]);
        }
        if(isset($condition["non_labeled_qty"]) && $condition["non_labeled_qty"] != ""){
            $select->where("non_labeled_qty = ?",$condition["non_labeled_qty"]);
        }
        if(isset($condition["rdb_note"]) && $condition["rdb_note"] != ""){
            $select->where("rdb_note = ?",$condition["rdb_note"]);
        }
        if(isset($condition["receiving_user_id"]) && $condition["receiving_user_id"] != ""){
            $select->where("receiving_user_id = ?",$condition["receiving_user_id"]);
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
    public function getByConditionSystemBoard($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	$select->joinLeft("receiving","receiving_detail_batch.receiving_id = receiving.receiving_id",array('warehouse_id'));
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    	
    	if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
    		$select->where("receiving.warehouse_id = ?",$condition["warehouse_id"]);
    	}
    
    	if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
    		$select->where("receiving_id = ?",$condition["receiving_id"]);
    	}
    	if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
    		$select->where("receiving_code = ?",$condition["receiving_code"]);
    	}
    	if(isset($condition["receiving_line_no"]) && $condition["receiving_line_no"] != ""){
    		$select->where("receiving_line_no = ?",$condition["receiving_line_no"]);
    	}
    	if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
    		$select->where("product_barcode = ?",$condition["product_barcode"]);
    	}
    	if(isset($condition["product_id"]) && $condition["product_id"] != ""){
    		$select->where("product_id = ?",$condition["product_id"]);
    	}
    	if(isset($condition["rdb_weight"]) && $condition["rdb_weight"] != ""){
    		$select->where("rdb_weight = ?",$condition["rdb_weight"]);
    	}
    	if(isset($condition["rdb_putaway_qty"]) && $condition["rdb_putaway_qty"] != ""){
    		$select->where("rdb_putaway_qty = ?",$condition["rdb_putaway_qty"]);
    	}
    	if(isset($condition["rdb_received_qty"]) && $condition["rdb_received_qty"] != ""){
    		$select->where("rdb_received_qty = ?",$condition["rdb_received_qty"]);
    	}
    	if(isset($condition["packaged"]) && $condition["packaged"] != ""){
    		$select->where("packaged = ?",$condition["packaged"]);
    	}
    	if(isset($condition["non_packaged_qty"]) && $condition["non_packaged_qty"] != ""){
    		$select->where("non_packaged_qty = ?",$condition["non_packaged_qty"]);
    	}
    	if(isset($condition["labeled"]) && $condition["labeled"] != ""){
    		$select->where("labeled = ?",$condition["labeled"]);
    	}
    	if(isset($condition["non_labeled_qty"]) && $condition["non_labeled_qty"] != ""){
    		$select->where("non_labeled_qty = ?",$condition["non_labeled_qty"]);
    	}
    	if(isset($condition["rdb_note"]) && $condition["rdb_note"] != ""){
    		$select->where("rdb_note = ?",$condition["rdb_note"]);
    	}
    	if(isset($condition["receiving_user_id"]) && $condition["receiving_user_id"] != ""){
    		$select->where("receiving_user_id = ?",$condition["receiving_user_id"]);
    	}
    	
    	if(isset($condition["rdb_add_time_for"]) && $condition["rdb_add_time_for"] != ""){
    		$select->where("rdb_add_time >= ?",$condition["rdb_add_time_for"].' 00:00:00');
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