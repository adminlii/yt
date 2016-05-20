<?php
class Table_PaypalTransactionDetailItem
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PaypalTransactionDetailItem();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PaypalTransactionDetailItem();
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
    public function update($row, $value, $field = "id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "id")
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
    public function getByField($value, $field = 'id', $colums = "*")
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
        
        if(isset($condition["transactionid"]) && $condition["transactionid"] != ""){
            $select->where("transactionid = ?",$condition["transactionid"]);
        }
        if(isset($condition["l_name"]) && $condition["l_name"] != ""){
            $select->where("l_name = ?",$condition["l_name"]);
        }
        if(isset($condition["l_number"]) && $condition["l_number"] != ""){
            $select->where("l_number = ?",$condition["l_number"]);
        }
        if(isset($condition["l_qty"]) && $condition["l_qty"] != ""){
            $select->where("l_qty = ?",$condition["l_qty"]);
        }
        if(isset($condition["l_taxamt"]) && $condition["l_taxamt"] != ""){
            $select->where("l_taxamt = ?",$condition["l_taxamt"]);
        }
        if(isset($condition["l_shippingamt"]) && $condition["l_shippingamt"] != ""){
            $select->where("l_shippingamt = ?",$condition["l_shippingamt"]);
        }
        if(isset($condition["l_handlingamt"]) && $condition["l_handlingamt"] != ""){
            $select->where("l_handlingamt = ?",$condition["l_handlingamt"]);
        }
        if(isset($condition["l_currencycode"]) && $condition["l_currencycode"] != ""){
            $select->where("l_currencycode = ?",$condition["l_currencycode"]);
        }
        if(isset($condition["l_amt"]) && $condition["l_amt"] != ""){
            $select->where("l_amt = ?",$condition["l_amt"]);
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