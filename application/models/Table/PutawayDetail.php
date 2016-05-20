<?php
class Table_PutawayDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PutawayDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PutawayDetail();
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
    public function update($row, $value, $field = "pd_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pd_id")
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
    public function getByField($value, $field = 'pd_id', $colums = "*")
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
        
        if(isset($condition["pd_id_arr"]) && is_array($condition["pd_id_arr"])){
            $select->where("pd_id in(?)",$condition["pd_id_arr"]);
        }
        if(isset($condition["putaway_id"]) && $condition["putaway_id"] != ""){
            $select->where("putaway_id = ?",$condition["putaway_id"]);
        }
        if(isset($condition["qc_code"]) && $condition["qc_code"] != ""){
            $select->where("qc_code = ?",$condition["qc_code"]);
        }
        if(isset($condition["box_code"]) && $condition["box_code"] != ""){
            $select->where("box_code = ?",$condition["box_code"]);
        }
        if(isset($condition["putaway_code"]) && $condition["putaway_code"] != ""){
            $select->where("putaway_code = ?",$condition["putaway_code"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["pd_type"]) && $condition["pd_type"] != ""){
            $select->where("pd_type = ?",$condition["pd_type"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["pd_quantity"]) && $condition["pd_quantity"] != ""){
            $select->where("pd_quantity = ?",$condition["pd_quantity"]);
        }
        if(isset($condition["pd_lot_number"]) && $condition["pd_lot_number"] != ""){
            $select->where("pd_lot_number = ?",$condition["pd_lot_number"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if(isset($condition["pd_status"]) && $condition["pd_status"] !== ""){
            $select->where("pd_status = ?",$condition["pd_status"]);
        }
        if(isset($condition["putaway_user_id"]) && $condition["putaway_user_id"] != ""){
            $select->where("putaway_user_id = ?",$condition["putaway_user_id"]);
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

    public function getLeftJoinUserByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('user', 'user.user_id='.$table.'.putaway_user_id',null);
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["putaway_id"]) && $condition["putaway_id"] != ""){
            $select->where("putaway_id = ?",$condition["putaway_id"]);
        }
        if(isset($condition["qc_code"]) && $condition["qc_code"] != ""){
            $select->where("qc_code = ?",$condition["qc_code"]);
        }
        if(isset($condition["putaway_code"]) && $condition["putaway_code"] != ""){
            $select->where("putaway_code = ?",$condition["putaway_code"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["pd_type"]) && $condition["pd_type"] != ""){
            $select->where("pd_type = ?",$condition["pd_type"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["pd_quantity"]) && $condition["pd_quantity"] != ""){
            $select->where("pd_quantity = ?",$condition["pd_quantity"]);
        }
        if(isset($condition["pd_lot_number"]) && $condition["pd_lot_number"] != ""){
            $select->where("pd_lot_number = ?",$condition["pd_lot_number"]);
        }
        if (isset($condition["warehouse_id"]) && $condition["warehouse_id"] != "") {
            $select->where($table . ".warehouse_id = ?", $condition["warehouse_id"]);
        }

        if (isset($condition["dateFor"]) && $condition["dateFor"] != "") {
            $select->where("pd_add_time >=?", $condition["dateFor"] . ' 00:00:00');
        }

        if (isset($condition["dateTo"]) && $condition["dateTo"] != "") {
            $select->where("pd_add_time <=?", $condition["dateTo"] . ' 23:59:59');
        }

        if (isset($condition["pd_status"]) && $condition["pd_status"] != "") {
            $select->where("pd_status = ?", $condition["pd_status"]);
        }
        if (isset($condition["putaway_user_id"]) && $condition["putaway_user_id"] != "") {
            $select->where("putaway_user_id = ?", $condition["putaway_user_id"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where($table . ".warehouse_id in(?)", $condition["warehouse_id_in"]);
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