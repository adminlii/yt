<?php
class Table_OrderFee
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderFee();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderFee();
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
    public function update($row, $value, $field = "of_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "of_id")
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
    public function getByField($value, $field = 'of_id', $colums = "*")
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
        
        if(isset($condition["ref_id"]) && $condition["ref_id"] != ""){
            $select->where("ref_id = ?",$condition["ref_id"]);
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["cs_code"]) && $condition["cs_code"] != ""){
            $select->where("cs_code = ?",$condition["cs_code"]);
        }
        if(isset($condition["ft_code"]) && $condition["ft_code"] != ""){
            $select->where("ft_code = ?",$condition["ft_code"]);
        }
        if(isset($condition["bi_amount"]) && $condition["bi_amount"] != ""){
            $select->where("bi_amount = ?",$condition["bi_amount"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["currency_rate"]) && $condition["currency_rate"] != ""){
            $select->where("currency_rate = ?",$condition["currency_rate"]);
        }
        if(isset($condition["bi_sp_type"]) && $condition["bi_sp_type"] != ""){
            $select->where("bi_sp_type = ?",$condition["bi_sp_type"]);
        }
        if(isset($condition["bi_creator_id"]) && $condition["bi_creator_id"] != ""){
            $select->where("bi_creator_id = ?",$condition["bi_creator_id"]);
        }
        if(isset($condition["bi_balance_sign"]) && $condition["bi_balance_sign"] != ""){
            $select->where("bi_balance_sign = ?",$condition["bi_balance_sign"]);
        }
        if(isset($condition["bi_writeoff_sign"]) && $condition["bi_writeoff_sign"] != ""){
            $select->where("bi_writeoff_sign = ?",$condition["bi_writeoff_sign"]);
        }
        if(isset($condition["bi_credit_pay"]) && $condition["bi_credit_pay"] != ""){
            $select->where("bi_credit_pay = ?",$condition["bi_credit_pay"]);
        }
        if(isset($condition["bi_note"]) && $condition["bi_note"] != ""){
            $select->where("bi_note = ?",$condition["bi_note"]);
        }
        if(isset($condition["bi_billing_date"]) && $condition["bi_billing_date"] != ""){
            $select->where("bi_billing_date = ?",$condition["bi_billing_date"]);
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