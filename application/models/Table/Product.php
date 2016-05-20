<?php
class Table_Product
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Product();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public function getTable()
    {
    	return $this->_table;
    }
    public static function getInstance()
    {
        return new Table_Product();
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
    public function update($row, $value, $field = "pu_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }
    
    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pu_id")
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
    public function getByField($value, $field = 'product_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $dbname = $this->_table->info('schema');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }

    public function getAll()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $dbname = $this->_table->info('schema');
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
        $dbname = $this->_table->info('schema');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
        	$select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }

        if(isset($condition["product_sku_arr"]) && !empty($condition["product_sku_arr"])){
        	$select->where("product_sku in (?)",$condition["product_sku_arr"]);
        }

        if(isset($condition["product_sku_like"]) && !empty($condition["product_sku_like"])){
            $select->where("product_sku like ?",'%'.$condition["product_sku_like"].'%');
        } 
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }

        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["product_title_en"]) && $condition["product_title_en"] != ""){
            $select->where("product_title_en = ?",$condition["product_title_en"]);
        }
        if(isset($condition["product_title"]) && $condition["product_title"] != ""){
            $select->where("product_title = ?",$condition["product_title"]);
        }
        if(isset($condition["product_title_like"]) && $condition["product_title_like"] != ""){
        	$select->where("product_title like ?",'%' . $condition["product_title_like"] . '%');
        }
        if(isset($condition["product_status"]) && $condition["product_status"] != ""){
            $select->where("product_status = ?",$condition["product_status"]);
        }
        if(isset($condition["product_receive_status"]) && $condition["product_receive_status"] != ""){
            $select->where("product_receive_status = ?",$condition["product_receive_status"]);
        }
        if(isset($condition["pu_code"]) && $condition["pu_code"] != ""){
            $select->where("pu_code = ?",$condition["pu_code"]);
        }
        if(isset($condition["product_length"]) && $condition["product_length"] != ""){
            $select->where("product_length = ?",$condition["product_length"]);
        }
        if(isset($condition["product_width"]) && $condition["product_width"] != ""){
            $select->where("product_width = ?",$condition["product_width"]);
        }
        if(isset($condition["product_height"]) && $condition["product_height"] != ""){
            $select->where("product_height = ?",$condition["product_height"]);
        }
        if(isset($condition["product_net_weight"]) && $condition["product_net_weight"] != ""){
            $select->where("product_net_weight = ?",$condition["product_net_weight"]);
        }
        if(isset($condition["product_weight"]) && $condition["product_weight"] != ""){
            $select->where("product_weight = ?",$condition["product_weight"]);
        }
        if(isset($condition["product_sales_value"]) && $condition["product_sales_value"] != ""){
            $select->where("product_sales_value = ?",$condition["product_sales_value"]);
        }
        if(isset($condition["product_purchase_value"]) && $condition["product_purchase_value"] != ""){
            $select->where("product_purchase_value = ?",$condition["product_purchase_value"]);
        }
        if(isset($condition["product_declared_value"]) && $condition["product_declared_value"] != ""){
            $select->where("product_declared_value = ?",$condition["product_declared_value"]);
        }
        if(isset($condition["product_is_qc"]) && $condition["product_is_qc"] != ""){
            $select->where("product_is_qc = ?",$condition["product_is_qc"]);
        }
        if(isset($condition["product_barcode_type"]) && $condition["product_barcode_type"] != ""){
            $select->where("product_barcode_type = ?",$condition["product_barcode_type"]);
        }
        if(isset($condition["product_type"]) && $condition["product_type"] != ""){
            $select->where("product_type = ?",$condition["product_type"]);
        }
        if(isset($condition["pc_id"]) && $condition["pc_id"] != ""){
            $select->where("pc_id = ?",$condition["pc_id"]);
        }
        if(isset($condition["pce_id"]) && $condition["pce_id"] != ""){
            $select->where("pce_id = ?",$condition["pce_id"]);
        }
        if(isset($condition["product_add_time"]) && $condition["product_add_time"] != ""){
            $select->where("product_add_time = ?",$condition["product_add_time"]);
        }
        /*CONDITION_END*/
//         echo $select->__toString();
//         exit;
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
    public function getAllByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $dbname = $this->_table->info('schema');
        $select->from($table,$type)
            ->joinLeft('product_category_ebay', $table.'.pce_id = product_category_ebay.pce_id',null);
       // $select->from($table, $type);
        $select->where("1 =?", 1);

        /*CONDITION_START*/

        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }

        if(isset($condition["product_sku_like"]) && $condition["product_sku_like"] != ""){
            $select->where("product_sku like ?","%{$condition["product_sku_like"]}%");
        }
        
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["product_title_en"]) && $condition["product_title_en"] != ""){
            $select->where("product_title_en = ?",$condition["product_title_en"]);
        }
        if(isset($condition["product_title"]) && $condition["product_title"] != ""){
            $select->where("product_title = ?",$condition["product_title"]);
        }
        if(isset($condition["product_status"]) && $condition["product_status"] != ""){
            $select->where("product_status = ?",$condition["product_status"]);
        }
        if(isset($condition["product_receive_status"]) && $condition["product_receive_status"] != ""){
            $select->where("product_receive_status = ?",$condition["product_receive_status"]);
        }
        if(isset($condition["pu_code"]) && $condition["pu_code"] != ""){
            $select->where("pu_code = ?",$condition["pu_code"]);
        }
        if(isset($condition["product_length"]) && $condition["product_length"] != ""){
            $select->where("product_length = ?",$condition["product_length"]);
        }
        if(isset($condition["product_width"]) && $condition["product_width"] != ""){
            $select->where("product_width = ?",$condition["product_width"]);
        }
        if(isset($condition["product_height"]) && $condition["product_height"] != ""){
            $select->where("product_height = ?",$condition["product_height"]);
        }
        if(isset($condition["product_net_weight"]) && $condition["product_net_weight"] != ""){
            $select->where("product_net_weight = ?",$condition["product_net_weight"]);
        }
        if(isset($condition["product_weight"]) && $condition["product_weight"] != ""){
            $select->where("product_weight = ?",$condition["product_weight"]);
        }
        if(isset($condition["product_sales_value"]) && $condition["product_sales_value"] != ""){
            $select->where("product_sales_value = ?",$condition["product_sales_value"]);
        }
        if(isset($condition["product_purchase_value"]) && $condition["product_purchase_value"] != ""){
            $select->where("product_purchase_value = ?",$condition["product_purchase_value"]);
        }
        if(isset($condition["product_declared_value"]) && $condition["product_declared_value"] != ""){
            $select->where("product_declared_value = ?",$condition["product_declared_value"]);
        }
        if(isset($condition["product_is_qc"]) && $condition["product_is_qc"] != ""){
            $select->where("product_is_qc = ?",$condition["product_is_qc"]);
        }
        if(isset($condition["product_barcode_type"]) && $condition["product_barcode_type"] != ""){
            $select->where("product_barcode_type = ?",$condition["product_barcode_type"]);
        }
        if(isset($condition["product_type"]) && $condition["product_type"] != ""){
            $select->where("product_type = ?",$condition["product_type"]);
        }
        if(isset($condition["pc_id"]) && $condition["pc_id"] != ""){
            $select->where("pc_id = ?",$condition["pc_id"]);
        }
        if(isset($condition["pce_id"]) && $condition["pce_id"] != ""){
            $select->where("pce_id = ?",$condition["pce_id"]);
        }
        if(isset($condition["product_add_time"]) && $condition["product_add_time"] != ""){
            $select->where("product_add_time = ?",$condition["product_add_time"]);
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
             //die($select->__toString());
            //die("okkkkkkkkkkkkkkkkkkk");
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
    public function getByConditionForSku($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$dbname = $this->_table->info('schema');
    	$select->from($table, $type);
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
    		$select->where("product_sku like ?","%{$condition["product_sku"]}%");
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
    public function getByConditionLike($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
    		$select->where("product_sku = ?",$condition["product_sku"]);
    	}
    	if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
    		$select->where("product_barcode like ?","%{$condition["product_barcode"]}%");
    	}

    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
    		$select->where("customer_code = ?",$condition["customer_code"]);
    	}
    	if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
    		$select->where("customer_id = ?",$condition["customer_id"]);
    	}
    	if(isset($condition["product_title_en"]) && $condition["product_title_en"] != ""){
    		$select->where("product_title_en = ?",$condition["product_title_en"]);
    	}
    	if(isset($condition["product_title"]) && $condition["product_title"] != ""){
    		$select->where("product_title = ?",$condition["product_title"]);
    	}
    	if(isset($condition["product_status"]) && $condition["product_status"] != ""){
    		$select->where("product_status = ?",$condition["product_status"]);
    	}
    	if(isset($condition["sale_status"]) && $condition["sale_status"] != ""){
    		$select->where("sale_status = ?",$condition["sale_status"]);
    	}
    	if(isset($condition["product_receive_status"]) && $condition["product_receive_status"] != ""){
    		$select->where("product_receive_status = ?",$condition["product_receive_status"]);
    	}
    	if(isset($condition["pu_code"]) && $condition["pu_code"] != ""){
    		$select->where("pu_code = ?",$condition["pu_code"]);
    	}
    	if(isset($condition["product_length"]) && $condition["product_length"] != ""){
    		$select->where("product_length = ?",$condition["product_length"]);
    	}
    	if(isset($condition["product_width"]) && $condition["product_width"] != ""){
    		$select->where("product_width = ?",$condition["product_width"]);
    	}
    	if(isset($condition["product_height"]) && $condition["product_height"] != ""){
    		$select->where("product_height = ?",$condition["product_height"]);
    	}
    	if(isset($condition["product_net_weight"]) && $condition["product_net_weight"] != ""){
    		$select->where("product_net_weight = ?",$condition["product_net_weight"]);
    	}
    	if(isset($condition["product_weight"]) && $condition["product_weight"] != ""){
    		$select->where("product_weight = ?",$condition["product_weight"]);
    	}
    	if(isset($condition["product_sales_value"]) && $condition["product_sales_value"] != ""){
    		$select->where("product_sales_value = ?",$condition["product_sales_value"]);
    	}
    	if(isset($condition["product_purchase_value"]) && $condition["product_purchase_value"] != ""){
    		$select->where("product_purchase_value = ?",$condition["product_purchase_value"]);
    	}
    	if(isset($condition["product_declared_value"]) && $condition["product_declared_value"] != ""){
    		$select->where("product_declared_value = ?",$condition["product_declared_value"]);
    	}
    	if(isset($condition["product_is_qc"]) && $condition["product_is_qc"] != ""){
    		$select->where("product_is_qc = ?",$condition["product_is_qc"]);
    	}
    	if(isset($condition["product_barcode_type"]) && $condition["product_barcode_type"] != ""){
    		$select->where("product_barcode_type = ?",$condition["product_barcode_type"]);
    	}
    	if(isset($condition["product_type"]) && $condition["product_type"] != ""){
    		$select->where("product_type = ?",$condition["product_type"]);
    	}
    	if(isset($condition["pc_id"]) && $condition["pc_id"] != ""){
    		$select->where("pc_id = ?",$condition["pc_id"]);
    	}
    	if(isset($condition["pce_id"]) && $condition["pce_id"] != ""){
    		$select->where("pce_id = ?",$condition["pce_id"]);
    	}
    	if(isset($condition["product_add_time"]) && $condition["product_add_time"] != ""){
    		$select->where("product_add_time = ?",$condition["product_add_time"]);
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
     * 注： 同一产品会有多条附加表数据，故同一产品可能查出多条数据
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionLeftAttach($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	
    	$select->joinLeft("product_attached", "product_attached.product_id = product.product_id", "*");
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
    		$select->where("product_sku = ?",$condition["product_sku"]);
    	}
    	if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
    		$select->where("product_barcode like ?","%{$condition["product_barcode"]}%");
    	}
    
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("company_code = ?",$condition["company_code"]);
    	}
    	if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
    		$select->where("customer_code = ?",$condition["customer_code"]);
    	}
    	if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
    		$select->where("customer_id = ?",$condition["customer_id"]);
    	}
    	if(isset($condition["product_title_en"]) && $condition["product_title_en"] != ""){
    		$select->where("product_title_en = ?",$condition["product_title_en"]);
    	}
    	if(isset($condition["product_title"]) && $condition["product_title"] != ""){
    		$select->where("product_title = ?",$condition["product_title"]);
    	}
    	if(isset($condition["product_status"]) && $condition["product_status"] != ""){
    		$select->where("product_status = ?",$condition["product_status"]);
    	}
    	if(isset($condition["sale_status"]) && $condition["sale_status"] != ""){
    		$select->where("sale_status = ?",$condition["sale_status"]);
    	}
    	if(isset($condition["product_receive_status"]) && $condition["product_receive_status"] != ""){
    		$select->where("product_receive_status = ?",$condition["product_receive_status"]);
    	}
    	if(isset($condition["pu_code"]) && $condition["pu_code"] != ""){
    		$select->where("pu_code = ?",$condition["pu_code"]);
    	}
    	if(isset($condition["product_length"]) && $condition["product_length"] != ""){
    		$select->where("product_length = ?",$condition["product_length"]);
    	}
    	if(isset($condition["product_width"]) && $condition["product_width"] != ""){
    		$select->where("product_width = ?",$condition["product_width"]);
    	}
    	if(isset($condition["product_height"]) && $condition["product_height"] != ""){
    		$select->where("product_height = ?",$condition["product_height"]);
    	}
    	if(isset($condition["product_net_weight"]) && $condition["product_net_weight"] != ""){
    		$select->where("product_net_weight = ?",$condition["product_net_weight"]);
    	}
    	if(isset($condition["product_weight"]) && $condition["product_weight"] != ""){
    		$select->where("product_weight = ?",$condition["product_weight"]);
    	}
    	if(isset($condition["product_sales_value"]) && $condition["product_sales_value"] != ""){
    		$select->where("product_sales_value = ?",$condition["product_sales_value"]);
    	}
    	if(isset($condition["product_purchase_value"]) && $condition["product_purchase_value"] != ""){
    		$select->where("product_purchase_value = ?",$condition["product_purchase_value"]);
    	}
    	if(isset($condition["product_declared_value"]) && $condition["product_declared_value"] != ""){
    		$select->where("product_declared_value = ?",$condition["product_declared_value"]);
    	}
    	if(isset($condition["product_is_qc"]) && $condition["product_is_qc"] != ""){
    		$select->where("product_is_qc = ?",$condition["product_is_qc"]);
    	}
    	if(isset($condition["product_barcode_type"]) && $condition["product_barcode_type"] != ""){
    		$select->where("product_barcode_type = ?",$condition["product_barcode_type"]);
    	}
    	if(isset($condition["product_type"]) && $condition["product_type"] != ""){
    		$select->where("product_type = ?",$condition["product_type"]);
    	}
    	if(isset($condition["pc_id"]) && $condition["pc_id"] != ""){
    		$select->where("pc_id = ?",$condition["pc_id"]);
    	}
    	if(isset($condition["pce_id"]) && $condition["pce_id"] != ""){
    		$select->where("pce_id = ?",$condition["pce_id"]);
    	}
    	if(isset($condition["product_add_time"]) && $condition["product_add_time"] != ""){
    		$select->where("product_add_time = ?",$condition["product_add_time"]);
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
    public function getBorrowByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$dbname = $this->_table->info('schema');
    	$select->from($table, $type);
    	$select->joinInner("product_borrow_map", "product_borrow_map.product_id=product.product_id", array());
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
    		$select->where("product_sku = ?",$condition["product_sku"]);
    	}
    	if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
    		$select->where("reference_no = ?",$condition["reference_no"]);
    	}
    
    	if(isset($condition["product_sku_arr"]) && !empty($condition["product_sku_arr"])){
    		$select->where("product_borrow_map.product_sku in (?)",$condition["product_sku_arr"]);
    	}
    
    	if(isset($condition["product_sku_like"]) && !empty($condition["product_sku_like"])){
    		$select->where("product_borrow_map.product_sku like ?",'%'.$condition["product_sku_like"].'%');
    	}
    	if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
    		$select->where("product_borrow_map.product_barcode = ?",$condition["product_barcode"]);
    	}
    
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("product_borrow_map.company_code = ?",$condition["company_code"]);
    	}
    
    	if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
    		$select->where("customer_code = ?",$condition["customer_code"]);
    	}
    	if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
    		$select->where("customer_id = ?",$condition["customer_id"]);
    	}
    	if(isset($condition["product_title_en"]) && $condition["product_title_en"] != ""){
    		$select->where("product_title_en = ?",$condition["product_title_en"]);
    	}
    	if(isset($condition["product_title"]) && $condition["product_title"] != ""){
    		$select->where("product_title = ?",$condition["product_title"]);
    	}
    	if(isset($condition["product_title_like"]) && $condition["product_title_like"] != ""){
    		$select->where("product_title like ?",'%' . $condition["product_title_like"] . '%');
    	}
    	if(isset($condition["product_status"]) && $condition["product_status"] != ""){
    		$select->where("product_status = ?",$condition["product_status"]);
    	}
    	if(isset($condition["product_receive_status"]) && $condition["product_receive_status"] != ""){
    		$select->where("product_receive_status = ?",$condition["product_receive_status"]);
    	}
    	if(isset($condition["pu_code"]) && $condition["pu_code"] != ""){
    		$select->where("pu_code = ?",$condition["pu_code"]);
    	}
    	if(isset($condition["product_length"]) && $condition["product_length"] != ""){
    		$select->where("product_length = ?",$condition["product_length"]);
    	}
    	if(isset($condition["product_width"]) && $condition["product_width"] != ""){
    		$select->where("product_width = ?",$condition["product_width"]);
    	}
    	if(isset($condition["product_height"]) && $condition["product_height"] != ""){
    		$select->where("product_height = ?",$condition["product_height"]);
    	}
    	if(isset($condition["product_net_weight"]) && $condition["product_net_weight"] != ""){
    		$select->where("product_net_weight = ?",$condition["product_net_weight"]);
    	}
    	if(isset($condition["product_weight"]) && $condition["product_weight"] != ""){
    		$select->where("product_weight = ?",$condition["product_weight"]);
    	}
    	if(isset($condition["product_sales_value"]) && $condition["product_sales_value"] != ""){
    		$select->where("product_sales_value = ?",$condition["product_sales_value"]);
    	}
    	if(isset($condition["product_purchase_value"]) && $condition["product_purchase_value"] != ""){
    		$select->where("product_purchase_value = ?",$condition["product_purchase_value"]);
    	}
    	if(isset($condition["product_declared_value"]) && $condition["product_declared_value"] != ""){
    		$select->where("product_declared_value = ?",$condition["product_declared_value"]);
    	}
    	if(isset($condition["product_is_qc"]) && $condition["product_is_qc"] != ""){
    		$select->where("product_is_qc = ?",$condition["product_is_qc"]);
    	}
    	if(isset($condition["product_barcode_type"]) && $condition["product_barcode_type"] != ""){
    		$select->where("product_barcode_type = ?",$condition["product_barcode_type"]);
    	}
    	if(isset($condition["product_type"]) && $condition["product_type"] != ""){
    		$select->where("product_type = ?",$condition["product_type"]);
    	}
    	if(isset($condition["pc_id"]) && $condition["pc_id"] != ""){
    		$select->where("pc_id = ?",$condition["pc_id"]);
    	}
    	if(isset($condition["pce_id"]) && $condition["pce_id"] != ""){
    		$select->where("pce_id = ?",$condition["pce_id"]);
    	}
    	if(isset($condition["product_add_time"]) && $condition["product_add_time"] != ""){
    		$select->where("product_add_time = ?",$condition["product_add_time"]);
    	}
    	/*CONDITION_END*/
    	//         echo $select->__toString();
    	//         exit;
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