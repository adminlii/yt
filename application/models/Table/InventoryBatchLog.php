<?php
class Table_InventoryBatchLog
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_InventoryBatchLog();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_InventoryBatchLog();
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
    public function update($row, $value, $field = "ibl_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ibl_id")
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
    public function getByField($value, $field = 'ibl_id', $colums = "*")
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
        
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["supplier_id"]) && $condition["supplier_id"] != ""){
            $select->where("supplier_id = ?",$condition["supplier_id"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["ib_id"]) && $condition["ib_id"] != ""){
            $select->where("ib_id = ?",$condition["ib_id"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["ibl_note"]) && $condition["ibl_note"] != ""){
            $select->where("ibl_note = ?",$condition["ibl_note"]);
        }
        if(isset($condition["ibl_quantity_before"]) && $condition["ibl_quantity_before"] != ""){
            $select->where("ibl_quantity_before = ?",$condition["ibl_quantity_before"]);
        }
        if(isset($condition["ibl_quantity_after"]) && $condition["ibl_quantity_after"] != ""){
            $select->where("ibl_quantity_after = ?",$condition["ibl_quantity_after"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
        if(isset($condition["ibl_ip"]) && $condition["ibl_ip"] != ""){
            $select->where("ibl_ip = ?",$condition["ibl_ip"]);
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