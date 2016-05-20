<?php
class Table_Receiving
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Receiving();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Receiving();
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
    public function update($row, $value, $field = "receiving_id")
    {
        $row['receiving_update_time'] = date('Y-m-d H:i:s');        
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "receiving_id")
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
    public function getByField($value, $field = 'receiving_id', $colums = "*")
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
        if(isset($condition["receiving_id_arr"]) && $condition["receiving_id_arr"] != ""){
        	$select->where("receiving_id in (?)",$condition["receiving_id_arr"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }

        if(isset($condition["receiving_code_arr"]) && is_array($condition["receiving_code_arr"]) &&!empty($condition["receiving_code_arr"])){
            $select->where("receiving_code in(?)",$condition["receiving_code_arr"]);
        }
        if(isset($condition["reference_no_like"]) && $condition["reference_no_like"] != ""){
            $select->where("reference_no like ?",'%'.$condition["reference_no_like"].'%');
        }
        if (isset($condition["tracking_number"]) && $condition["tracking_number"] != "") {
            $select->where("tracking_number = ?", $condition["tracking_number"]);
        }
        if (isset($condition["tracking_number_like"]) && $condition["tracking_number_like"] != "") {
            $select->where("tracking_number like ?", '%' . $condition["tracking_number_like"] . '%');
        }
        if (isset($condition["reference_no"]) && $condition["reference_no"] != "") {
            $select->where("reference_no = ?", $condition["reference_no"]);
        }
        if (isset($condition["refer_no"]) && $condition["refer_no"] != "") {
            $select->where("reference_no = ?", $condition["refer_no"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if (isset($condition["to_warehouse_id"]) && $condition["to_warehouse_id"] !== "") {
            $select->where("to_warehouse_id = ?", $condition["to_warehouse_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["receiving_type"]) && $condition["receiving_type"] != ""){
            $select->where("receiving_type = ?",$condition["receiving_type"]);
        }
        if(isset($condition["receiving_status"]) && $condition["receiving_status"] != ""){
            $select->where("receiving_status = ?",$condition["receiving_status"]);
        }
        if(isset($condition["dateFor"]) && $condition["dateFor"] != ""){
            $select->where("receiving_add_time >=?",$condition["dateFor"].' 00:00:00');
        }

        if(isset($condition["dateTo"]) && $condition["dateTo"] != ""){
            $select->where("receiving_add_time <=?",$condition["dateTo"].' 23:59:59');
        }
        
        //时间段 start
        if(isset($condition["receiving_add_time_from"]) && $condition["receiving_add_time_from"] != ""){
            $select->where("receiving_add_time >=?",$condition["receiving_add_time_from"]);
        }        
        if(isset($condition["receiving_add_time_to"]) && $condition["receiving_add_time_to"] != ""){
            $select->where("receiving_add_time <=?",$condition["receiving_add_time_to"]);
        }

        if(isset($condition["receiving_update_time_from"]) && $condition["receiving_update_time_from"] != ""){
            $select->where("receiving_update_time >=?",$condition["receiving_update_time_from"]);
        }
        if(isset($condition["receiving_update_time_to"]) && $condition["receiving_update_time_to"] != ""){
            $select->where("receiving_update_time <=?",$condition["receiving_update_time_to"]);
        }
        //时间段 end
        if(isset($condition["po_code"]) && $condition["po_code"] != ""){
        	$select->where("po_code = ?",$condition["po_code"]);
        }

        if(isset($condition["income_type"]) && $condition["income_type"] != ""){
        	$select->where("income_type = ?",$condition["income_type"]);
        }

        if(isset($condition["shipping_method"]) && $condition["shipping_method"] != ""){
        	$select->where("shipping_method = ?",$condition["shipping_method"]);
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
     * @desc 入库单列表
     * @return array|string
     */
    public function getSearchByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        if (isset($condition["productBarcode"]) && $condition["productBarcode"] != "") {
            $select = $this->_table->getAdapter()->select();
            $table = $this->_table->info('name');
            if ('count(*)' == $type) {
                //统计总数
                $select->from($table, 'count(DISTINCT(receiving.receiving_id))');
            } else {
                $select->from($table, $type);
            }
            $select->joinInner("receiving_detail", "receiving_detail.receiving_id=receiving.receiving_id", null);
            $select->where("1 =?", 1);
            /*CONDITION_START*/
            if (isset($condition["receiving_code"]) && $condition["receiving_code"] != "") {
                $select->where("receiving.receiving_code = ?", $condition["receiving_code"]);
            }
            if (isset($condition["tracking_number"]) && $condition["tracking_number"] != "") {
                $select->where("receiving.tracking_number = ?", $condition["tracking_number"]);
            }
            if (isset($condition["tracking_number_like"]) && $condition["tracking_number_like"] != "") {
                $select->where("receiving.tracking_number like ?", '%' . $condition["tracking_number_like"] . '%');
            }
            if (isset($condition["reference_no_like"]) && $condition["reference_no_like"] != "") {
                $select->where("reference_no like ?", '%' . $condition["reference_no_like"] . '%');
            }
            if (isset($condition["reference_no"]) && $condition["reference_no"] != "") {
                $select->where("reference_no = ?", $condition["reference_no"]);
            }
            if (isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== "") {
                $select->where("warehouse_id = ?", $condition["warehouse_id"]);
            }
            if (isset($condition["to_warehouse_id"]) && $condition["to_warehouse_id"] !== "") {
                $select->where("to_warehouse_id = ?", $condition["to_warehouse_id"]);
            }
            if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
                $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
            }
            if (isset($condition["customer_id"]) && $condition["customer_id"] != "") {
                $select->where("customer_id = ?", $condition["customer_id"]);
            }
            if (isset($condition["customer_code"]) && $condition["customer_code"] != "") {
                $select->where("customer_code = ?", $condition["customer_code"]);
            }
            if (isset($condition["receiving_type"]) && $condition["receiving_type"] != "") {
                $select->where("receiving_type = ?", $condition["receiving_type"]);
            }
            if (isset($condition["receiving_status"]) && $condition["receiving_status"] != "") {
                $select->where("receiving_status = ?", $condition["receiving_status"]);
            }
            if (isset($condition["dateFor"]) && $condition["dateFor"] != "") {
                $select->where("receiving_add_time >=?", $condition["dateFor"] . ' 00:00:00');
            }

            if (isset($condition["dateTo"]) && $condition["dateTo"] != "") {
                $select->where("receiving_add_time <=?", $condition["dateTo"] . ' 23:59:59');
            }
            if (isset($condition["po_code"]) && $condition["po_code"] != "") {
                $select->where("po_code = ?", $condition["po_code"]);
            }
            if (isset($condition["productBarcode"]) && $condition["productBarcode"] != "") {
                $select->where("receiving_detail.product_barcode = ?", $condition["productBarcode"]);
            }

//             echo $select->__toString();
//             exit;
            /*CONDITION_END*/
            if ('count(*)' == $type) {
                return $this->_table->getAdapter()->fetchOne($select);
            } else {
                $select->group($table.'.receiving_id');
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
        } else {
            return $this->getByCondition($condition, $type, $pageSize, $page, $orderBy);
        }
    }
    
    public function getByOrderNotReceiving($type = '*', $pageSize = 0, $page = 1){
    	$db = Common_Common::getAdapter();
    	$sql = "select ".$type." from receiving where receiving.to_warehouse_id in (
					select warehouse_id from service_relational where sp_id = (
						select sp_id from service_provider where service_provider.sp_code = '4PX'
					)
				) and receiving.receiving_status < '7' and receiving.receiving_status <> '0';";
    	
    	/*CONDITION_END*/
    	if ('count(*)' == $type) {
    		return $db->fetchOne($sql);
    	} else {
    		if ($pageSize > 0 and $page > 0) {
    			$start = ($page - 1) * $pageSize;
    			$sql = $sql." LIMIT ".$pageSize." OFFSET ".$start;
    		}
    		return $db->fetchAll($sql);
    	}
    }

}