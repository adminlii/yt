<?php
class Table_ProductInventoryLog
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductInventoryLog();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductInventoryLog();
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
    public function update($row, $value, $field = "pil_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pil_id")
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
    public function getByField($value, $field = 'pil_id', $colums = "*")
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
        
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["reference_code"]) && $condition["reference_code"] != ""){
            $select->where("reference_code = ?",$condition["reference_code"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
        if(isset($condition["pil_onway"]) && $condition["pil_onway"] != ""){
            $select->where("pil_onway = ?",$condition["pil_onway"]);
        }
        if(isset($condition["pil_pending"]) && $condition["pil_pending"] != ""){
            $select->where("pil_pending = ?",$condition["pil_pending"]);
        }
        if(isset($condition["pil_sellable"]) && $condition["pil_sellable"] != ""){
            $select->where("pil_sellable = ?",$condition["pil_sellable"]);
        }
        if(isset($condition["pil_unsellable"]) && $condition["pil_unsellable"] != ""){
            $select->where("pil_unsellable = ?",$condition["pil_unsellable"]);
        }
        if(isset($condition["pil_reserved"]) && $condition["pil_reserved"] != ""){
            $select->where("pil_reserved = ?",$condition["pil_reserved"]);
        }
        if(isset($condition["pil_shipped"]) && $condition["pil_shipped"] != ""){
            $select->where("pil_shipped = ?",$condition["pil_shipped"]);
        }
        if(isset($condition["from_it_code"]) && $condition["from_it_code"] != ""){
            $select->where("from_it_code = ?",$condition["from_it_code"]);
        }
        if(isset($condition["to_it_code"]) && $condition["to_it_code"] != ""){
            $select->where("to_it_code = ?",$condition["to_it_code"]);
        }
        if(isset($condition["pil_quantity"]) && $condition["pil_quantity"] != ""){
            $select->where("pil_quantity = ?",$condition["pil_quantity"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["pil_note"]) && $condition["pil_note"] != ""){
            $select->where("pil_note = ?",$condition["pil_note"]);
        }
        if(isset($condition["pil_ip"]) && $condition["pil_ip"] != ""){
            $select->where("pil_ip = ?",$condition["pil_ip"]);
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