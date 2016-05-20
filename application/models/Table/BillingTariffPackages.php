<?php
class Table_BillingTariffPackages
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_BillingTariffPackages();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_BillingTariffPackages();
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
    public function update($row, $value, $field = "btp_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "btp_id")
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
    public function getByField($value, $field = 'btp_id', $colums = "*")
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
        if(isset($condition["tp_id"]) && $condition["tp_id"] != ""){
            $select->where("tp_id = ?",$condition["tp_id"]);
        }
        if(isset($condition["btp_status"]) && $condition["btp_status"] != ""){
            $select->where("btp_status = ?",$condition["btp_status"]);
        }
        if(isset($condition["btp_is_billing"]) && $condition["btp_is_billing"] != ""){
            $select->where("btp_is_billing = ?",$condition["btp_is_billing"]);
        }
        if(isset($condition["btp_free_time"]) && $condition["btp_free_time"] != ""){
            $select->where("btp_free_time = ?",$condition["btp_free_time"]);
        }
        if(isset($condition["btp_received_installation_fee"]) && $condition["btp_received_installation_fee"] != ""){
            $select->where("btp_received_installation_fee = ?",$condition["btp_received_installation_fee"]);
        }
        if(isset($condition["btp_last_billable_date"]) && $condition["btp_last_billable_date"] != ""){
            $select->where("btp_last_billable_date = ?",$condition["btp_last_billable_date"]);
        }
        if(isset($condition["btp_recent_billing_date"]) && $condition["btp_recent_billing_date"] != ""){
            $select->where("btp_recent_billing_date = ?",$condition["btp_recent_billing_date"]);
        }
        if(isset($condition["btp_next_billing_date"]) && $condition["btp_next_billing_date"] != ""){
            $select->where("btp_next_billing_date = ?",$condition["btp_next_billing_date"]);
        }
        if(isset($condition["btp_recent_btpd_code"]) && $condition["btp_recent_btpd_code"] != ""){
            $select->where("btp_recent_btpd_code = ?",$condition["btp_recent_btpd_code"]);
        }
        if(isset($condition["btp_check_date"]) && $condition["btp_check_date"] != ""){
            $select->where("btp_check_date = ?",$condition["btp_check_date"]);
        }
        if(isset($condition["btp_update_date"]) && $condition["btp_update_date"] != ""){
            $select->where("btp_update_date = ?",$condition["btp_update_date"]);
        }
        if(isset($condition["btp_add_date"]) && $condition["btp_add_date"] != ""){
            $select->where("btp_add_date = ?",$condition["btp_add_date"]);
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