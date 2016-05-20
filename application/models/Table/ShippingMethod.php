<?php
class Table_ShippingMethod
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShippingMethod();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShippingMethod();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        if (isset($row['sm_code'])) {
            $row['sm_code'] = strtoupper($row['sm_code']);
        }
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "sm_id")
    {
        if (isset($row['sm_code'])) {
            $row['sm_code'] = strtoupper($row['sm_code']);
        }
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "sm_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->delete($where);
    }
    
    public function getHeadShippingMethod($warehouse = ""){
    	$db = Common_Common::getAdapter();
    	$sql = "select shipping_method.sm_code,shipping_method.sm_name_cn
				from shipping_method
				LEFT JOIN shipping_method_settings on shipping_method.sm_id = shipping_method_settings.sm_id
				where shipping_method_settings.warehouse_id = ".$warehouse;
    	
    	return $headMethod = $db->fetchAll($sql);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public function getByField($value, $field = 'sm_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }
    
    public function getbyChangeProduct($value = ""){
    	$sql = "SELECT 
					(case 
						shipping_method.sm_carrier_number 
						when '' then shipping_method.sm_code 
						else shipping_method.sm_carrier_number 
						end ) as sm_carrier_number
				FROM `shipping_method` WHERE (sm_code = '".$value."')";
    	return $this->_table->getAdapter()->fetchRow($sql);
    }

    public function getAll()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, "*");
        return $this->_table->getAdapter()->fetchAll($select);
    }
    
    public function getByConditionGetLike($searchMethod = ""){
    	$sql = "select sm_code,sm_name_cn from shipping_method where sm_code like 
    			'%".$searchMethod."%' or sm_name_cn like '%".$searchMethod."%' and sm_status = 1";
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
        
        if(isset($condition["sm_code"]) && $condition["sm_code"] != ""){
            $select->where("sm_code = ?",$condition["sm_code"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where("company_code in (?)",array($condition["company_code"],''));
        }
        if(isset($condition["sm_name_cn"]) && $condition["sm_name_cn"] != ""){
            $select->where("sm_name_cn = ?",$condition["sm_name_cn"]);
        }
        if(isset($condition["sm_name"]) && $condition["sm_name"] != ""){
            $select->where("sm_name = ?",$condition["sm_name"]);
        }
        if(isset($condition["sm_status"]) && $condition["sm_status"] !== ""){
            $select->where("sm_status = ?",$condition["sm_status"]);
        }
        if(isset($condition["sm_class_code"]) && $condition["sm_class_code"] != ""){
            $select->where("sm_class_code = ?",$condition["sm_class_code"]);
        }
        if(isset($condition["sm_is_tracking"]) && $condition["sm_is_tracking"] !== ""){
            $select->where("sm_is_tracking = ?",$condition["sm_is_tracking"]);
        }
        if(isset($condition["sm_is_validate_remote"]) && $condition["sm_is_validate_remote"] != ""){
            $select->where("sm_is_validate_remote = ?",$condition["sm_is_validate_remote"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["warehouse_id_arr"]) && is_array($condition["warehouse_id_arr"])){
            $select->where("warehouse_id in(?)",$condition["warehouse_id_arr"]);
        }
        if(isset($condition["sm_fee_type"]) && $condition["sm_fee_type"] !== ""){
            $select->where("sm_fee_type = ?",$condition["sm_fee_type"]);
        }
        if(isset($condition["sm_calc_type"]) && $condition["sm_calc_type"] !== ""){
            $select->where("sm_calc_type = ?",$condition["sm_calc_type"]);
        }
        if(isset($condition["sm_id_ne"]) && $condition["sm_id_ne"] !== ""){
            $select->where("sm_id != ?",$condition["sm_id_ne"]);
        }
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] !== ""){
        	$select->where("is_systematic = ?",$condition["is_systematic"]);
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


    public function getByInnerJoinCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        if ('count(*)' == $type) {
            //统计总数
            $select->from($table, 'count(DISTINCT(shipping_method.sm_code))');
        } else {
            $select->from($table, $type);
        }
        $select->joinInner('shipping_method_settings as sms', 'sms.sm_id='.$table.'.sm_id',null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["sm_code"]) && $condition["sm_code"] != ""){
            $select->where($table.".sm_code = ?",$condition["sm_code"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where($table.".company_code in (?)",array($condition["company_code"],''));
        }
        if(isset($condition["sm_status"]) && $condition["sm_status"] !== ""){
            $select->where("sm_status = ?",$condition["sm_status"]);
        }
        if(isset($condition["sm_class_code"]) && $condition["sm_class_code"] != ""){
            $select->where("sm_class_code = ?",$condition["sm_class_code"]);
        }
        if(isset($condition["sm_is_tracking"]) && $condition["sm_is_tracking"] !== ""){
            $select->where("sm_is_tracking = ?",$condition["sm_is_tracking"]);
        }
        if(isset($condition["sm_is_validate_remote"]) && $condition["sm_is_validate_remote"] != ""){
            $select->where("sm_is_validate_remote = ?",$condition["sm_is_validate_remote"]);
        }
        if(isset($condition["sm_id_ne"]) && $condition["sm_id_ne"] !== ""){
            $select->where($table.".sm_id != ?",$condition["sm_id_ne"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== ""){
            $select->where("sms.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["warehouse_id_arr"]) && is_array($condition["warehouse_id_arr"])){
            $select->where("sms.warehouse_id in(?)",$condition["warehouse_id_arr"]);
        }
        if(isset($condition["smt_fee_type"]) && $condition["smt_fee_type"] !== ""){
            $select->where("sms.smt_fee_type = ?",$condition["smt_fee_type"]);
        }
        if(isset($condition["smt_type"]) && $condition["smt_type"] !== ""){
            $select->where("sms.smt_type = ?",$condition["smt_type"]);
        }
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] !== ""){
        	$select->where($table.".is_systematic = ?",$condition["is_systematic"]);
        }
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            $select->group($table.'.sm_code');
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

    public function getByLeftJoinCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        if ('count(*)' == $type) {
            //统计总数
            $select->from($table, 'count(DISTINCT(shipping_method.sm_code))');
        } else {
            $select->from($table, $type);
        }
        $select->joinLeft('shipping_method_settings as sms', 'sms.sm_id='.$table.'.sm_id' . " and sms.company_code in ('','$condition[company_code]')",null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["sm_code"]) && $condition["sm_code"] != ""){
            $select->where($table.".sm_code = ?",$condition["sm_code"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where($table.".company_code in (?)",array($condition["company_code"],''));
//         	$select->where("sms.company_code in (?)",array($condition["company_code"],''));
        }
        
        if(isset($condition["sm_code_like"]) && $condition["sm_code_like"] != ""){
            $select->where($table.".sm_code like ?",'%'.$condition["sm_code_like"].'%');
        }
        if(isset($condition["sm_status"]) && $condition["sm_status"] !== ""){
            $select->where("sm_status = ?",$condition["sm_status"]);
        }
        if(isset($condition["sm_class_code"]) && $condition["sm_class_code"] != ""){
            $select->where("sm_class_code = ?",$condition["sm_class_code"]);
        }
        if(isset($condition["sm_is_tracking"]) && $condition["sm_is_tracking"] !== ""){
            $select->where("sm_is_tracking = ?",$condition["sm_is_tracking"]);
        }
        if(isset($condition["sm_is_validate_remote"]) && $condition["sm_is_validate_remote"] != ""){
            $select->where("sm_is_validate_remote = ?",$condition["sm_is_validate_remote"]);
        }
        if(isset($condition["sm_id_ne"]) && $condition["sm_id_ne"] !== ""){
            $select->where($table.".sm_id != ?",$condition["sm_id_ne"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== ""){
            $select->where("sms.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["warehouse_id_arr"]) && is_array($condition["warehouse_id_arr"])){
            $select->where("sms.warehouse_id in(?)",$condition["warehouse_id_arr"]);
        }
        if(isset($condition["smt_fee_type"]) && $condition["smt_fee_type"] !== ""){
            $select->where("sms.smt_fee_type = ?",$condition["smt_fee_type"]);
        }
        if(isset($condition["smt_type"]) && $condition["smt_type"] !== ""){
            $select->where("sms.smt_type = ?",$condition["smt_type"]);
        }
        
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] !== ""){
        	$select->where($table.".is_systematic = ?",$condition["is_systematic"]);
        }
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            $select->group($table.'.sm_code');
            if (!empty($orderBy)) {
                $select->order($orderBy);
            }
            if ($pageSize > 0 and $page > 0) {
                $start = ($page - 1) * $pageSize;
                $select->limit($pageSize, $start);
            }
            $sql = $select->__toString();
//             echo $select->__toString();
//             exit;
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
    public function getByConditionPageLike($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    
    	if(isset($condition["shipping_warehouse_id"]) && $condition["shipping_warehouse_id"] != ""){
    		$select->where("warehouse_id = ?",$condition["shipping_warehouse_id"]);
    	}
    	
    	if(isset($condition["method"]) && $condition["method"] != ""){
    		$select->where("sm_code like ?","%{$condition["method"]}%");
    		$select->orWhere("sm_name_cn like ?","%{$condition["method"]}%");
    	}
    	
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
    		$select->where("company_code in (?)",array($condition["company_code"],''));
    	}
    	if(isset($condition["is_systematic"]) && $condition["is_systematic"] !== ""){
    		$select->where("is_systematic = ?",$condition["is_systematic"]);
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
     * @desc 获取匹配仓库的运输方式
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByLeftJoinWarehouseCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        if ('count(*)' == $type) {
            //统计总数
            $select->from($table, 'count(DISTINCT(shipping_method.sm_code))');
        } else {
            $select->from($table, $type);
        }
        $select->joinLeft('shipping_method_settings as sms', 'sms.sm_id='.$table.'.sm_id',null);
        $select->joinLeft('warehouse', 'warehouse.warehouse_id=sms.warehouse_id',null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where($table.".company_code in (?)",array($condition["company_code"],''));
        }
        if(isset($condition["sm_code"]) && $condition["sm_code"] != ""){
            $select->where($table.".sm_code = ?",$condition["sm_code"]);
        }
        if(isset($condition["sm_status"]) && $condition["sm_status"] !== ""){
            $select->where("sm_status = ?",$condition["sm_status"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== ""){
            $select->where("warehouse.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["warehouse_type"]) && $condition["warehouse_type"] !== ""){
            $select->where("warehouse.warehouse_type = ?",$condition["warehouse_type"]);
        }
        if(isset($condition["warehouse_status"]) && $condition["warehouse_status"] !== ""){
            $select->where("warehouse.warehouse_status = ?",$condition["warehouse_status"]);
        }
        if(isset($condition["warehouse_id_arr"]) && is_array($condition["warehouse_id_arr"])){
            $select->where("warehouse.warehouse_id in(?)",$condition["warehouse_id_arr"]);
        }
        if(isset($condition["smt_fee_type"]) && $condition["smt_fee_type"] !== ""){
            $select->where("sms.smt_fee_type = ?",$condition["smt_fee_type"]);
        }
        if(isset($condition["smt_type"]) && $condition["smt_type"] !== ""){
            $select->where("sms.smt_type = ?",$condition["smt_type"]);
        }
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] !== ""){
        	$select->where($table.".is_systematic = ?",$condition["is_systematic"]);
        }
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            $select->group($table.'.sm_code');
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