<?php
class Table_EbayOrderPayments
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayOrderPayments();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayOrderPayments();
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
    public function update($row, $value, $field = "eod_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "eod_id")
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
    public function getByField($value, $field = 'eod_id', $colums = "*")
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
        if(isset($condition["reference_id"]) && $condition["reference_id"] != ""){
            $select->where("reference_id = ?",$condition["reference_id"]);
        }
        if(isset($condition["fee_or_credit_amount"]) && $condition["fee_or_credit_amount"] != ""){
            $select->where("fee_or_credit_amount = ?",$condition["fee_or_credit_amount"]);
        }
        if(isset($condition["fee_or_credit_amount_currency"]) && $condition["fee_or_credit_amount_currency"] != ""){
            $select->where("fee_or_credit_amount_currency = ?",$condition["fee_or_credit_amount_currency"]);
        }
        if(isset($condition["payee"]) && $condition["payee"] != ""){
            $select->where("payee = ?",$condition["payee"]);
        }
        if(isset($condition["payer"]) && $condition["payer"] != ""){
            $select->where("payer = ?",$condition["payer"]);
        }
        if(isset($condition["payment_amount"]) && $condition["payment_amount"] != ""){
            $select->where("payment_amount = ?",$condition["payment_amount"]);
        }
        if(isset($condition["payment_amount_currency"]) && $condition["payment_amount_currency"] != ""){
            $select->where("payment_amount_currency = ?",$condition["payment_amount_currency"]);
        }
        if(isset($condition["payment_reference_id"]) && $condition["payment_reference_id"] != ""){
            $select->where("payment_reference_id = ?",$condition["payment_reference_id"]);
        }
        if(isset($condition["payment_status"]) && $condition["payment_status"] != ""){
            $select->where("payment_status = ?",$condition["payment_status"]);
        }
        if(isset($condition["payment_time"]) && $condition["payment_time"] != ""){
            $select->where("payment_time = ?",$condition["payment_time"]);
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