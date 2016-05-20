<?php
class Table_ReceivingDetailCost
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingDetailCost();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingDetailCost();
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
    public function update($row, $value, $field = "rdc_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rdc_id")
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
    public function getByField($value, $field = 'rdc_id', $colums = "*")
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
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] !== ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["supplier_id"]) && $condition["supplier_id"] !== ""){
            $select->where("supplier_id = ?",$condition["supplier_id"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
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
     * @desc 获取库存批次原入库单成本
     * @param $ibId
     * @param string $colums
     * @return array
     */
    public function getProductCostsByIbId($ibId='0',$colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $colums);
        $select->joinLeft('inventory_batch as ib', 'ib.receiving_id=receiving_detail_cost.receiving_id and ib.product_id=receiving_detail_cost.product_id',null);
        $select->where("ib.ib_id=?", $ibId);
        return $this->_table->getAdapter()->fetchAll($select);
    }

    /**
     * @desc 判断是否同一个入库单存在两个供应商
     * @param int $receivingId
     * @param int $productId
     * @return mixed
     */
    public function getSupplierCountByCondition($receivingId = 0, $productId = 0)
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, 'COUNT(DISTINCT(supplier_id)) as total');
        $select->where("receiving_id = ?", $receivingId);
        $select->where("product_id = ?", $productId);
        return $this->_table->getAdapter()->fetchRow($select);
    }
}