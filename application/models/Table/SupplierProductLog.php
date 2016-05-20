<?php
class Table_SupplierProductLog
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_SupplierProductLog();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_SupplierProductLog();
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
    public function update($row, $value, $field = "spl_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "spl_id")
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
    public function getByField($value, $field = 'spl_id', $colums = "*")
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
        
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["supplier_code"]) && $condition["supplier_code"] != ""){
            $select->where("supplier_code = ?",$condition["supplier_code"]);
        }
        if(isset($condition["supplier_id"]) && $condition["supplier_id"] != ""){
            $select->where("supplier_id = ?",$condition["supplier_id"]);
        }
        if(isset($condition["sp_unit_price"]) && $condition["sp_unit_price"] != ""){
            $select->where("sp_unit_price = ?",$condition["sp_unit_price"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["sp_eta_time"]) && $condition["sp_eta_time"] != ""){
            $select->where("sp_eta_time = ?",$condition["sp_eta_time"]);
        }
        if(isset($condition["sp_min_qty"]) && $condition["sp_min_qty"] != ""){
            $select->where("sp_min_qty = ?",$condition["sp_min_qty"]);
        }
        if(isset($condition["creater_id"]) && $condition["creater_id"] != ""){
            $select->where("creater_id = ?",$condition["creater_id"]);
        }
        if(isset($condition["sp_add_time"]) && $condition["sp_add_time"] != ""){
            $select->where("sp_add_time = ?",$condition["sp_add_time"]);
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