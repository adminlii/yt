<?php
class Table_EbayOrderExternalTransaction
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayOrderExternalTransaction();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayOrderExternalTransaction();
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
    public function update($row, $value, $field = "eoet_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "eoet_id")
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
    public function getByField($value, $field = 'eoet_id', $colums = "*")
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
        
        if(isset($condition["order_sn"]) && $condition["order_sn"] != ""){
            $select->where("order_sn = ?",$condition["order_sn"]);
        }
        if(isset($condition["external_transaction_id"]) && $condition["external_transaction_id"] != ""){
            $select->where("external_transaction_id = ?",$condition["external_transaction_id"]);
        }
        if(isset($condition["external_transaction_status"]) && $condition["external_transaction_status"] != ""){
            $select->where("external_transaction_status = ?",$condition["external_transaction_status"]);
        }
        if(isset($condition["external_transaction_time"]) && $condition["external_transaction_time"] != ""){
            $select->where("external_transaction_time = ?",$condition["external_transaction_time"]);
        }
        if(isset($condition["fee_or_credit_amount"]) && $condition["fee_or_credit_amount"] != ""){
            $select->where("fee_or_credit_amount = ?",$condition["fee_or_credit_amount"]);
        }
        if(isset($condition["fee_or_credit_amount_currency"]) && $condition["fee_or_credit_amount_currency"] != ""){
            $select->where("fee_or_credit_amount_currency = ?",$condition["fee_or_credit_amount_currency"]);
        }
        if(isset($condition["payment_or_refund_amount"]) && $condition["payment_or_refund_amount"] != ""){
            $select->where("payment_or_refund_amount = ?",$condition["payment_or_refund_amount"]);
        }
        if(isset($condition["payment_or_refund_amount_currency"]) && $condition["payment_or_refund_amount_currency"] != ""){
            $select->where("payment_or_refund_amount_currency = ?",$condition["payment_or_refund_amount_currency"]);
        }
        if(isset($condition["create_time_sys"]) && $condition["create_time_sys"] != ""){
            $select->where("create_time_sys = ?",$condition["create_time_sys"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["update_time_sys"]) && $condition["update_time_sys"] != ""){
            $select->where("update_time_sys = ?",$condition["update_time_sys"]);
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