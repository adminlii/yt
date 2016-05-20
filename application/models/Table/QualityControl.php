<?php
class Table_QualityControl
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_QualityControl();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_QualityControl();
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
    public function update($row, $value, $field = "qc_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "qc_id")
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
    public function getByField($value, $field = 'qc_id', $colums = "*")
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
        
        if(isset($condition["qc_code"]) && $condition["qc_code"] != ""){
            $select->where("qc_code = ?",$condition["qc_code"]);
        }
        if(isset($condition["rd_id"]) && $condition["rd_id"] !== ""){
            $select->where("rd_id = ?",$condition["rd_id"]);
        }
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if (isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== "") {
            $select->where("warehouse_id = ?", $condition["warehouse_id"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["qc_type"]) && $condition["qc_type"] !== ""){
            $select->where("qc_type = ?",$condition["qc_type"]);
        }
        if(isset($condition["qc_status"]) && $condition["qc_status"] !== ""){
            $select->where("qc_status = ?",$condition["qc_status"]);
        }
        if(isset($condition["qc_status_in"]) && is_array($condition["qc_status_in"])){
            $select->where("qc_status in(?)",$condition["qc_status_in"]);
        }
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["dateFor"]) && $condition["dateFor"] != ""){
            $select->where("qc_add_time >=?",$condition["dateFor"]);
        }
        
        //该条件为统计看板设置
        if(isset($condition["dateBorder"]) && $condition["dateBorder"] != ""){
        	$select->where("qc_finish_time >=?",$condition["dateBorder"]);
        }

        if(isset($condition["dateTo"]) && $condition["dateTo"] != ""){
            $select->where("qc_add_time <=?",$condition["dateTo"]);
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


    public function getLeftJoinAsnByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('receiving', "receiving.receiving_id=".$table.'.receiving_id',null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        if(isset($condition["po_code"]) && $condition["po_code"] != ""){
            $select->where("receiving.po_code = ?",$condition["po_code"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] !== ""){
            $select->where("product_id = ?",$condition["product_id"]);
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