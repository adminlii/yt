<?php
class Table_ProductBorrowInventory
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductBorrowInventory();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductBorrowInventory();
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
    public function update($row, $value, $field = "pbi_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pbi_id")
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
    public function getByField($value, $field = 'pbi_id', $colums = "*")
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
        if(isset($condition["from_company_code"]) && $condition["from_company_code"] != ""){
            $select->where("from_company_code = ?",$condition["from_company_code"]);
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
        if(isset($condition["pbi_borrowed"]) && $condition["pbi_borrowed"] != ""){
            $select->where("pbi_borrowed = ?",$condition["pbi_borrowed"]);
        }
        if(isset($condition["pbi_reserved"]) && $condition["pbi_reserved"] != ""){
            $select->where("pbi_reserved = ?",$condition["pbi_reserved"]);
        }
        if(isset($condition["pbi_shipped"]) && $condition["pbi_shipped"] != ""){
            $select->where("pbi_shipped = ?",$condition["pbi_shipped"]);
        }
        if(isset($condition["pbi_unused"]) && $condition["pbi_unused"] != ""){
            $select->where("pbi_unused = ?",$condition["pbi_unused"]);
        }
        if(isset($condition["pbi_return"]) && $condition["pbi_return"] != ""){
            $select->where("pbi_return = ?",$condition["pbi_return"]);
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
    	$select->joinInner("product", "product.product_id = product_borrow_inventory.product_id",array());
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("product_borrow_inventory.company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["from_company_code"]) && $condition["from_company_code"] != ""){
    		$select->where("from_company_code = ?",$condition["from_company_code"]);
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
    	if(isset($condition["pbi_borrowed"]) && $condition["pbi_borrowed"] != ""){
    		$select->where("pbi_borrowed = ?",$condition["pbi_borrowed"]);
    	}
    	if(isset($condition["pbi_reserved"]) && $condition["pbi_reserved"] != ""){
    		$select->where("pbi_reserved = ?",$condition["pbi_reserved"]);
    	}
    	if(isset($condition["pbi_shipped"]) && $condition["pbi_shipped"] != ""){
    		$select->where("pbi_shipped = ?",$condition["pbi_shipped"]);
    	}
    	if(isset($condition["pbi_unused"]) && $condition["pbi_unused"] != ""){
    		$select->where("pbi_unused = ?",$condition["pbi_unused"]);
    	}
    	if(isset($condition["pbi_return"]) && $condition["pbi_return"] != ""){
    		$select->where("pbi_return = ?",$condition["pbi_return"]);
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
    		return $this->_table->getAdapter()->fetchAll($sql);
    	}
    }
}