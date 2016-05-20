<?php
class Table_InventoryBatchOutbound
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_InventoryBatchOutbound();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_InventoryBatchOutbound();
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
    public function update($row, $value, $field = "ibo_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ibo_id")
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
    public function getByField($value, $field = 'ibo_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }
    
    public function sumQuantity($ib_id) {
    	$sql = 'select sum(ibo.ibo_quantity) as num from inventory_batch_outbound as ibo where ibo.ib_id='.$ib_id;
    	$result = $this->_table->getAdapter()->fetchRow($sql);
    	return intval($result['num']);
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
        
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["ibo_quantity"]) && $condition["ibo_quantity"] != ""){
            $select->where("ibo_quantity = ?",$condition["ibo_quantity"]);
        }
        if(isset($condition["ib_id"]) && $condition["ib_id"] != ""){
            $select->where("ib_id = ?",$condition["ib_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
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
     * list by in ibo_id
     * @author solar
     * @param array $ibo_ids
     * @return array
     */
    public function listInIboids($ibo_ids) {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
        $select->from($table, '*');
    	$select->where('ibo_id in(?)', $ibo_ids);
    	return $this->_table->getAdapter()->fetchAll($select);
    }


    public function getGroupByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["ibo_quantity"]) && $condition["ibo_quantity"] != ""){
            $select->where("ibo_quantity = ?",$condition["ibo_quantity"]);
        }
        if(isset($condition["ib_id"]) && $condition["ib_id"] != ""){
            $select->where("ib_id = ?",$condition["ib_id"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            $select->group(array('lc_code','product_id'));
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
     * @desc 获取核销数量
     * @param array $condition
     * @return array|string
     */
    public function getSumByReferenceNoAndProductId($referenceNo = '', $productId = 0)
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, 'sum(ibo_quantity) as quantity');
        $select->where("reference_no = ?", $referenceNo);
        $select->where("product_id = ?", $productId);
        return $this->_table->getAdapter()->fetchRow($select);
    }
    
}