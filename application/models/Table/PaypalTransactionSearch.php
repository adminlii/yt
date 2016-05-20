<?php
class Table_PaypalTransactionSearch
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PaypalTransactionSearch();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PaypalTransactionSearch();
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
        
        if(isset($condition["l_timestamp"]) && $condition["l_timestamp"] != ""){
            $select->where("l_timestamp = ?",$condition["l_timestamp"]);
        }
        if(isset($condition["l_timezone"]) && $condition["l_timezone"] != ""){
            $select->where("l_timezone = ?",$condition["l_timezone"]);
        }
        if(isset($condition["l_email"]) && $condition["l_email"] != ""){
            $select->where("l_email = ?",$condition["l_email"]);
        }
        if(isset($condition["l_name"]) && $condition["l_name"] != ""){
            $select->where("l_name = ?",$condition["l_name"]);
        }
        if(isset($condition["l_transactionid"]) && $condition["l_transactionid"] != ""){
            $select->where("l_transactionid = ?",$condition["l_transactionid"]);
        }
        if(isset($condition["l_status"]) && $condition["l_status"] != ""){
            $select->where("l_status = ?",$condition["l_status"]);
        }
        if(isset($condition["l_amt"]) && $condition["l_amt"] != ""){
            $select->where("l_amt = ?",$condition["l_amt"]);
        }
        if(isset($condition["l_currencycode"]) && $condition["l_currencycode"] != ""){
            $select->where("l_currencycode = ?",$condition["l_currencycode"]);
        }
        if(isset($condition["l_feeamt"]) && $condition["l_feeamt"] != ""){
            $select->where("l_feeamt = ?",$condition["l_feeamt"]);
        }
        if(isset($condition["l_netamt"]) && $condition["l_netamt"] != ""){
            $select->where("l_netamt = ?",$condition["l_netamt"]);
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