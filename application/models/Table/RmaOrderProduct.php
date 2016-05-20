<?php
class Table_RmaOrderProduct
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_RmaOrderProduct();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_RmaOrderProduct();
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
    public function update($row, $value, $field = "rmap_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rmap_id")
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
    public function getByField($value, $field = 'rmap_id', $colums = "*")
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
        
        if(isset($condition["rma_id"]) && $condition["rma_id"] != ""){
            $select->where("rma_id = ?",$condition["rma_id"]);
        }
        if(isset($condition["rmap_amount_total"]) && $condition["rmap_amount_total"] != ""){
            $select->where("rmap_amount_total = ?",$condition["rmap_amount_total"]);
        }
        if(isset($condition["rmap_sync_time"]) && $condition["rmap_sync_time"] != ""){
            $select->where("rmap_sync_time = ?",$condition["rmap_sync_time"]);
        }
        if(isset($condition["rmap_reason_id"]) && $condition["rmap_reason_id"] != ""){
            $select->where("rmap_reason_id = ?",$condition["rmap_reason_id"]);
        }
        if(isset($condition["rmap_product_id"]) && $condition["rmap_product_id"] != ""){
            $select->where("rmap_product_id = ?",$condition["rmap_product_id"]);
        }
        if(isset($condition["rmap_product_qty"]) && $condition["rmap_product_qty"] != ""){
            $select->where("rmap_product_qty = ?",$condition["rmap_product_qty"]);
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