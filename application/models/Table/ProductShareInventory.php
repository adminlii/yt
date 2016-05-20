<?php
class Table_ProductShareInventory
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductShareInventory();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductShareInventory();
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
    public function update($row, $value, $field = "psi_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "psi_id")
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
    public function getByField($value, $field = 'psi_id', $colums = "*")
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
        
        if(isset($condition["pi_id"]) && $condition["pi_id"] != ""){
            $select->where("pi_id = ?",$condition["pi_id"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["psi_shared"]) && $condition["psi_shared"] != ""){
            $select->where("psi_shared = ?",$condition["psi_shared"]);
        }
        if(isset($condition["psi_sharing"]) && $condition["psi_sharing"] != ""){
            $select->where("psi_sharing = ?",$condition["psi_sharing"]);
        }
        if(isset($condition["psi_stopped_share"]) && $condition["psi_stopped_share"] != ""){
            $select->where("psi_stopped_share = ?",$condition["psi_stopped_share"]);
        }
        if(isset($condition["psi_borrowed"]) && $condition["psi_borrowed"] != ""){
            $select->where("psi_borrowed = ?",$condition["psi_borrowed"]);
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
    public function getByInnerProductCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	
    	$select->joinInner("product", "product.product_id = product_share_inventory.product_id",array());
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["pi_id"]) && $condition["pi_id"] != ""){
    		$select->where("pi_id = ?",$condition["pi_id"]);
    	}
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("product_share_inventory.company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["product_id"]) && $condition["product_id"] != ""){
    		$select->where("product_id = ?",$condition["product_id"]);
    	}
    	if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
    		$select->where("product_sku = ?",$condition["product_sku"]);
    	}
    	if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
    		$select->where("warehouse_id = ?",$condition["warehouse_id"]);
    	}
    	if(isset($condition["psi_stopped_share"]) && $condition["psi_stopped_share"] != ""){
    		$select->where("psi_stopped_share > ?",$condition["psi_stopped_share"]);
    	}
    	if(isset($condition["psi_borrowed"]) && $condition["psi_borrowed"] != ""){
    		$select->where("psi_borrowed > ?",$condition["psi_borrowed"]);
    	}
    	if(isset($condition["psi_sharing"]) && $condition["psi_sharing"] != ""){
    		$select->where("psi_sharing > ?",$condition["psi_sharing"]);
    	}
    	if(isset($condition["company_code_unequal"]) && $condition["company_code_unequal"] != ""){
    		$select->where("product_share_inventory.company_code != ?",$condition["company_code_unequal"]);
    	}
    	if(isset($condition["product_sku_like"]) && $condition["product_sku_like"] != ""){
    		$select->where("product.product_sku like ?",'%' . $condition["product_sku_like"] . '%');
    	}
    	if(isset($condition["product_title_like"]) && $condition["product_title_like"] != ""){
    		$select->where("product.product_title like ?",'%' . $condition["product_title_like"] . '%');
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
//     		echo $select->__toString(); die;
    		return $this->_table->getAdapter()->fetchAll($sql);
    	}
    }
}