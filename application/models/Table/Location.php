<?php
class Table_Location
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Location();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Location();
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
    public function update($row, $value, $field = "lc_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "lc_id")
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
    public function getByField($value, $field = 'lc_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
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
        if(isset($condition["lc_code_like"]) && $condition["lc_code_like"] != ""){
            $select->where("lc_code like ?",'%'.$condition["lc_code_like"].'%');
        }
    	if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }        
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["lc_note"]) && $condition["lc_note"] != ""){
            $select->where("lc_note = ?",$condition["lc_note"]);
        }
        if(isset($condition["lc_status"]) && $condition["lc_status"] != ""){
            $select->where("lc_status = ?",$condition["lc_status"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["warehouse_id_in"]) &&  is_array($condition["warehouse_id_in"])){
            $select->where("warehouse_id in(?)",$condition["warehouse_id_in"]);
        }
        if(isset($condition["lt_code"]) && $condition["lt_code"] != ""){
            $select->where("lt_code = ?",$condition["lt_code"]);
        }
        if(isset($condition["wa_code"]) && $condition["wa_code"] != ""){
            $select->where("wa_code = ?",$condition["wa_code"]);
        }
        if(isset($condition["lc_sort"]) && $condition["lc_sort"] != ""){
            $select->where("lc_sort = ?",$condition["lc_sort"]);
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


    public function getLeftJoinWarehouseAreaByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('warehouse_area', 'warehouse_area.wa_code='.$table.'.wa_code',null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where("warehouse_area.company_code = ?",$condition["company_code"]);
        	$select->where("location.company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["lc_code_like"]) && $condition["lc_code_like"] != ""){
            $select->where("lc_code like ?",'%'.$condition["lc_code_like"].'%');
        }
        if(isset($condition["lc_status"]) && $condition["lc_status"] != ""){
            $select->where("lc_status = ?",$condition["lc_status"]);
        }
        if(isset($condition["wa_type"]) && $condition["wa_type"] !== ""){
            $select->where("warehouse_area.wa_type = ?",$condition["wa_type"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("location.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["warehouse_id_in"]) &&  is_array($condition["warehouse_id_in"])){
            $select->where("location.warehouse_id in(?)",$condition["warehouse_id_in"]);
        }
        if(isset($condition["lt_code"]) && $condition["lt_code"] != ""){
            $select->where("lt_code = ?",$condition["lt_code"]);
        }
        if(isset($condition["wa_code"]) && $condition["wa_code"] != ""){
            $select->where("warehouse_area.wa_code = ?",$condition["wa_code"]);
        }
        if(isset($condition["lc_sort"]) && $condition["lc_sort"] != ""){
            $select->where("lc_sort = ?",$condition["lc_sort"]);
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
     * @desc 空货架或正在使用
     */
    public function getLeftJoinInventoryBatchByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('inventory_batch', 'inventory_batch.lc_code='.$table.'.lc_code and inventory_batch.warehouse_id='.$table.'.warehouse_id',array('product_id','product_barcode'));
        $select->joinLeft('warehouse_area', 'warehouse_area.wa_code='.$table.'.wa_code',null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("location.lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["lc_code_like"]) && $condition["lc_code_like"] != ""){
            $select->where("location.lc_code like ?",'%'.$condition["lc_code_like"].'%');
        }
        if(isset($condition["lc_status"]) && $condition["lc_status"] != ""){
            $select->where("lc_status = ?",$condition["lc_status"]);
        }
        if(isset($condition["lt_code"]) && $condition["lt_code"] != ""){
            $select->where("lt_code = ?",$condition["lt_code"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== ""){
            $select->where("location.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["wa_code"]) && $condition["wa_code"] != ""){
            $select->where("warehouse_area.wa_code = ?",$condition["wa_code"]);
        }
        if(isset($condition["batch_lc_code"]) && $condition["batch_lc_code"] != ""){
            $select->where("inventory_batch.lc_code is null ");
        }
        if(isset($condition["product_id"]) && $condition["product_id"] !== ""){
            $select->orWhere("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== ""){
            $select->where("location.warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["lc_id"]) && $condition["lc_id"] != ""){
            $select->where("lc_id >= ?",$condition["lc_id"]);
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