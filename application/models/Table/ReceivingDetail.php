<?php
class Table_ReceivingDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingDetail();
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
    public function update($row, $value, $field = "rd_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rd_id")
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
    public function getByField($value, $field = 'rd_id', $colums = "*")
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
        
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] !== ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if (isset($condition["receiving_code_in"]) && is_array($condition["receiving_code_in"])) {
            $select->where("receiving_code in(?)", $condition["receiving_code_in"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["receiving_line_no"]) && $condition["receiving_line_no"] != ""){
            $select->where("receiving_line_no = ?",$condition["receiving_line_no"]);
        }
        if(isset($condition["rd_status"]) && $condition["rd_status"] !== ""){
            $select->where("rd_status = ?",$condition["rd_status"]);
        }
        if(isset($condition["rd_status_in"]) && is_array($condition["rd_status_in"])){
            $select->where("rd_status in(?)",$condition["rd_status_in"]);
        }
        if(isset($condition["rd_transfer_status"]) && $condition["rd_transfer_status"] !== ""){
            $select->where("rd_transfer_status = ?",$condition["rd_transfer_status"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["is_qc"]) && $condition["is_qc"] != ""){
            $select->where("is_qc = ?",$condition["is_qc"]);
        }
        if(isset($condition["is_priority"]) && $condition["is_priority"] != ""){
            $select->where("is_priority = ?",$condition["is_priority"]);
        }
        if(isset($condition["rd_id_arr"]) &&  is_array($condition["rd_id_arr"])){
            $select->where("rd_id in(?)",$condition["rd_id_arr"]);
        }
        if(isset($condition["box_no"]) && $condition["box_no"] != ""){
            $select->where("box_no = ?",$condition["box_no"]);
        }
        if(isset($condition["package_type"]) && $condition["package_type"] != ""){
            $select->where("package_type = ?",$condition["package_type"]);
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
     * @desc 中转下架
     * @return array|string
     */
    public function getLeftJoinByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('receiving', 'receiving.receiving_id=' . $table . '.receiving_id', array('to_warehouse_id', 'customer_code', 'reference_no'));
        $select->joinLeft('product', 'product.product_id=' . $table . '.product_id', array('product_title_en', 'pc_id'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        $select->where("receiving.receiving_transfer_status in(?)",array(1,2));
        $select->where($table.".rd_transfer_status = 0");

        if (isset($condition["receiving_id"]) && $condition["receiving_id"] != "") {
            $select->where("receiving.receiving_id = ?", $condition["receiving_id"]);
        }
        if (isset($condition["receiving_code"]) && $condition["receiving_code"] != "") {
            $select->where("receiving.receiving_code = ?", $condition["receiving_code"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("receiving.warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if (isset($condition["customer_code"]) && $condition["customer_code"] != "") {
            $select->where("receiving.customer_code = ?", $condition["customer_code"]);
        }
        if (isset($condition["sm_code"]) && $condition["sm_code"] != "") {
            $select->where("sm_code = ?", $condition["sm_code"]);
        }
        if (isset($condition["reference_no"]) && $condition["reference_no"] != "") {
            $select->where("receiving.reference_no = ?", $condition["reference_no"]);
        }
        if (isset($condition["dateFor"]) && $condition["dateFor"] != "") {
            $select->where("receiving.receiving_add_time >= ?", $condition["dateFor"]);
        }
        if (isset($condition["dateTo"]) && $condition["dateTo"] != "") {
            $select->where("receiving.receiving_add_time <= ?", $condition["dateTo"]);
        }
        if (isset($condition["to_warehouse_id"]) && $condition["to_warehouse_id"] != "") {
            $select->where("receiving.to_warehouse_id = ?", $condition["to_warehouse_id"]);
        }
        if (isset($condition["transitWarehouseId"]) && $condition["transitWarehouseId"] !== "") {
            $select->where("receiving.warehouse_id = ?", $condition["transitWarehouseId"]);
        }
        if (isset($condition["rd_status"]) && $condition["rd_status"] !== "") {
            $select->where("rd_status = ?", $condition["rd_status"]);
        }
        if (isset($condition["product_id"]) && $condition["product_id"] != "") {
            $select->where($table . ".product_id = ?", $condition["product_id"]);
        }
        if (isset($condition["product_barcode"]) && $condition["product_barcode"] != "") {
            $select->where($table . ".product_barcode = ?", $condition["product_barcode"]);
        }
        if (isset($condition["product_category"]) && $condition["product_category"] != "") {
            $select->where("product.pc_id = ?", $condition["product_category"]);
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
     * @desc 用于创建中转单
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getLeftJoinReceivingByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('receiving', 'receiving.receiving_id=' . $table . '.receiving_id', array('to_warehouse_id', 'customer_code','warehouse_id','receiving_type','customer_id','receiving_transfer_status'));
        $select->joinLeft('product', 'product.product_id=' . $table . '.product_id', array('product_title_en', 'pc_id'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if (isset($condition["receiving_id"]) && $condition["receiving_id"] != "") {
            $select->where("receiving.receiving_id = ?", $condition["receiving_id"]);
        }
        if (isset($condition["receiving_code"]) && $condition["receiving_code"] != "") {
            $select->where("receiving.receiving_code = ?", $condition["receiving_code"]);
        }
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("receiving.warehouse_id in(?)", $condition["warehouse_id_in"]);
        }
        if (isset($condition["customer_code"]) && $condition["customer_code"] != "") {
            $select->where("receiving.customer_code = ?", $condition["customer_code"]);
        }
        if (isset($condition["sm_code"]) && $condition["sm_code"] != "") {
            $select->where("sm_code = ?", $condition["sm_code"]);
        }
        if (isset($condition["rd_id"]) && $condition["rd_id"] != "") {
            $select->where($table.".rd_id = ?", $condition["rd_id"]);
        }
        if (isset($condition["to_warehouse_id"]) && $condition["to_warehouse_id"] != "") {
            $select->where("receiving.to_warehouse_id = ?", $condition["to_warehouse_id"]);
        }
        if (isset($condition["transitWarehouseId"]) && $condition["transitWarehouseId"] !== "") {
            $select->where("receiving.warehouse_id = ?", $condition["transitWarehouseId"]);
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
     * @desc 关联产品
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getLeftJoinProductByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $colums=array(
            'rd_id',
            'receiving_code',
            'rd_status',
            'product_id',
            'product_barcode',
            'rd_receiving_qty',
            'rd_putaway_qty',
            'rd_received_qty',
            'is_qc',
        );
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('product', 'product.product_id=' . $table . '.product_id', array('product_weight', 'product_length','product_width','product_height','pc_id','hs_code','product_receive_status','product_declared_value','currency_code','product_title'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        if (isset($condition["rd_id_arr"]) && $condition["rd_id_arr"] != "") {
            $select->where($table.".rd_id in(?)", $condition["rd_id_arr"]);
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
    public function getJoinInnerReceivingByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinInner('receiving', 'receiving.receiving_id=receiving_detail.receiving_id',array('warehouse_id','to_warehouse_id','customer_code','receiving_status','receiving_type','receiving_detail.rdc_id'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["receiving_id"]) && $condition["receiving_id"] !== ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if (isset($condition["receiving_code_in"]) && is_array($condition["receiving_code_in"])) {
            $select->where("receiving_code in(?)", $condition["receiving_code_in"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["receiving_line_no"]) && $condition["receiving_line_no"] != ""){
            $select->where("receiving_line_no = ?",$condition["receiving_line_no"]);
        }
        if(isset($condition["rd_id"]) && $condition["rd_id"] != ""){
            $select->where("rd_id = ?",$condition["rd_id"]);
        }
        if(isset($condition["rd_id_arr"]) &&  is_array($condition["rd_id_arr"])){
            $select->where("rd_id in(?)",$condition["rd_id_arr"]);
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