<?php
class Table_CsdInvoice
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsdInvoice();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsdInvoice();
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
    public function update($row, $value, $field = "invoice_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "invoice_id")
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
    public function getByField($value, $field = 'invoice_id', $colums = "*")
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
        
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["invoice_enname"]) && $condition["invoice_enname"] != ""){
            $select->where("invoice_enname = ?",$condition["invoice_enname"]);
        }
        if(isset($condition["unit_code"]) && $condition["unit_code"] != ""){
            $select->where("unit_code = ?",$condition["unit_code"]);
        }
        if(isset($condition["invoice_quantity"]) && $condition["invoice_quantity"] != ""){
            $select->where("invoice_quantity = ?",$condition["invoice_quantity"]);
        }
        if(isset($condition["invoice_totalcharge"]) && $condition["invoice_totalcharge"] != ""){
            $select->where("invoice_totalcharge = ?",$condition["invoice_totalcharge"]);
        }
        if(isset($condition["invoice_currencycode"]) && $condition["invoice_currencycode"] != ""){
            $select->where("invoice_currencycode = ?",$condition["invoice_currencycode"]);
        }
        if(isset($condition["hs_code"]) && $condition["hs_code"] != ""){
            $select->where("hs_code = ?",$condition["hs_code"]);
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