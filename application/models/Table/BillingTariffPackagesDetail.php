<?php
class Table_BillingTariffPackagesDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_BillingTariffPackagesDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_BillingTariffPackagesDetail();
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
    public function update($row, $value, $field = "btpd_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "btpd_id")
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
    public function getByField($value, $field = 'btpd_id', $colums = "*")
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
        
        if(isset($condition["tp_id"]) && $condition["tp_id"] != ""){
            $select->where("tp_id = ?",$condition["tp_id"]);
        }
        if(isset($condition["btp_id"]) && $condition["btp_id"] != ""){
            $select->where("btp_id = ?",$condition["btp_id"]);
        }
        if(isset($condition["btpd_code"]) && $condition["btpd_code"] != ""){
            $select->where("btpd_code = ?",$condition["btpd_code"]);
        }
        if(isset($condition["btpd_status"]) && $condition["btpd_status"] != ""){
            $select->where("btpd_status = ?",$condition["btpd_status"]);
        }
        if(isset($condition["btpd_charge_time_start"]) && $condition["btpd_charge_time_start"] != ""){
            $select->where("btpd_charge_time_start = ?",$condition["btpd_charge_time_start"]);
        }
        if(isset($condition["btpd_charge_time_end"]) && $condition["btpd_charge_time_end"] != ""){
            $select->where("btpd_charge_time_end = ?",$condition["btpd_charge_time_end"]);
        }
        if(isset($condition["btpd_installation_fee"]) && $condition["btpd_installation_fee"] != ""){
            $select->where("btpd_installation_fee = ?",$condition["btpd_installation_fee"]);
        }
        if(isset($condition["btpd_maintenance_costs"]) && $condition["btpd_maintenance_costs"] != ""){
            $select->where("btpd_maintenance_costs = ?",$condition["btpd_maintenance_costs"]);
        }
        if(isset($condition["btpd_orders_max"]) && $condition["btpd_orders_max"] != ""){
            $select->where("btpd_orders_max = ?",$condition["btpd_orders_max"]);
        }
        if(isset($condition["btpd_limit_orders"]) && $condition["btpd_limit_orders"] != ""){
            $select->where("btpd_limit_orders = ?",$condition["btpd_limit_orders"]);
        }
        if(isset($condition["btpd_limit_orders_val"]) && $condition["btpd_limit_orders_val"] != ""){
            $select->where("btpd_limit_orders_val = ?",$condition["btpd_limit_orders_val"]);
        }
        if(isset($condition["btpd_orders"]) && $condition["btpd_orders"] != ""){
            $select->where("btpd_orders = ?",$condition["btpd_orders"]);
        }
        if(isset($condition["btpd_single_ticket_fees"]) && $condition["btpd_single_ticket_fees"] != ""){
            $select->where("btpd_single_ticket_fees = ?",$condition["btpd_single_ticket_fees"]);
        }
        if(isset($condition["btpd_orders_exceeded"]) && $condition["btpd_orders_exceeded"] != ""){
            $select->where("btpd_orders_exceeded = ?",$condition["btpd_orders_exceeded"]);
        }
        if(isset($condition["btpd_exceed_orders_expenses"]) && $condition["btpd_exceed_orders_expenses"] != ""){
            $select->where("btpd_exceed_orders_expenses = ?",$condition["btpd_exceed_orders_expenses"]);
        }
        if(isset($condition["btpd_total_amount"]) && $condition["btpd_total_amount"] != ""){
            $select->where("btpd_total_amount = ?",$condition["btpd_total_amount"]);
        }
        if(isset($condition["btpd_actually_paid_amount"]) && $condition["btpd_actually_paid_amount"] != ""){
            $select->where("btpd_actually_paid_amount = ?",$condition["btpd_actually_paid_amount"]);
        }
        if(isset($condition["tp_currency_code"]) && $condition["tp_currency_code"] != ""){
            $select->where("tp_currency_code = ?",$condition["tp_currency_code"]);
        }
        if(isset($condition["btpd_add_date"]) && $condition["btpd_add_date"] != ""){
            $select->where("btpd_add_date = ?",$condition["btpd_add_date"]);
        }
        if(isset($condition["btpd_toaccount_date"]) && $condition["btpd_toaccount_date"] != ""){
            $select->where("btpd_toaccount_date = ?",$condition["btpd_toaccount_date"]);
        }
        if(isset($condition["btpd_verify_date"]) && $condition["btpd_verify_date"] != ""){
            $select->where("btpd_verify_date = ?",$condition["btpd_verify_date"]);
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