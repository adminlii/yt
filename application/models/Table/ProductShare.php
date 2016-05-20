<?php
class Table_ProductShare
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductShare();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductShare();
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
    public function update($row, $value, $field = "ps_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ps_id")
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
    public function getByField($value, $field = 'ps_id', $colums = "*")
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
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["ps_status"]) && $condition["ps_status"] != ""){
            $select->where("ps_status = ?",$condition["ps_status"]);
        }
        if(isset($condition["creator_id"]) && $condition["creator_id"] != ""){
            $select->where("creator_id = ?",$condition["creator_id"]);
        }
        if(isset($condition["verifier_id"]) && $condition["verifier_id"] != ""){
            $select->where("verifier_id = ?",$condition["verifier_id"]);
        }
        if(isset($condition["modifier_id"]) && $condition["modifier_id"] != ""){
            $select->where("modifier_id = ?",$condition["modifier_id"]);
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
    public function getByInnerDetailCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	 
    	$select->joinInner("product_share_detail", "product_share.ps_id = product_share_detail.ps_id", array());
    	$select->joinInner("product", "product.product_id = product_share_detail.product_id", array());
    	 
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("product_share.company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
    		$select->where("warehouse_id = ?",$condition["warehouse_id"]);
    	}
    	if(isset($condition["ps_status"]) && $condition["ps_status"] != ""){
    		$select->where("ps_status = ?",$condition["ps_status"]);
    	}
    	if(isset($condition["creator_id"]) && $condition["creator_id"] != ""){
    		$select->where("creator_id = ?",$condition["creator_id"]);
    	}
    	if(isset($condition["verifier_id"]) && $condition["verifier_id"] != ""){
    		$select->where("verifier_id = ?",$condition["verifier_id"]);
    	}
    	if(isset($condition["modifier_id"]) && $condition["modifier_id"] != ""){
    		$select->where("modifier_id = ?",$condition["modifier_id"]);
    	}
    	if(isset($condition["dateFor"]) && $condition["dateFor"] != ""){
    		$select->where("add_time >=?",$condition["dateFor"].' 00:00:00');
    	}
    	 
    	if(isset($condition["dateTo"]) && $condition["dateTo"] != ""){
    		$select->where("add_time <=?",$condition["dateTo"].' 23:59:59');
    	}
    	if(isset($condition["product_barcode_like"]) && $condition["product_barcode_like"] != ""){
    		$select->where("product_share_detail.product_sku like ?",'%'.$condition["product_barcode_like"].'%');
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
//     		print_r($sql);die;
    		return $this->_table->getAdapter()->fetchAll($sql);
    	}
    }
}