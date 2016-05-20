<?php
class Table_TariffPackages
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_TariffPackages();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_TariffPackages();
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
    public function update($row, $value, $field = "tp_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "tp_id")
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
    public function getByField($value, $field = 'tp_id', $colums = "*")
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
        
        if(isset($condition["tp_level"]) && $condition["tp_level"] != ""){
            $select->where("tp_level = ?",$condition["tp_level"]);
        }
        if(isset($condition["tp_code"]) && $condition["tp_code"] != ""){
            $select->where("tp_code = ?",$condition["tp_code"]);
        }
        if(isset($condition["tp_name"]) && $condition["tp_name"] != ""){
            $select->where("tp_name = ?",$condition["tp_name"]);
        }
        if(isset($condition["tp_desc"]) && $condition["tp_desc"] != ""){
            $select->where("tp_desc = ?",$condition["tp_desc"]);
        }
        if(isset($condition["tp_installation_fee"]) && $condition["tp_installation_fee"] != ""){
            $select->where("tp_installation_fee = ?",$condition["tp_installation_fee"]);
        }
        if(isset($condition["tp_orders_start"]) && $condition["tp_orders_start"] != ""){
            $select->where("tp_orders_start = ?",$condition["tp_orders_start"]);
        }
        if(isset($condition["tp_orders_end"]) && $condition["tp_orders_end"] != ""){
            $select->where("tp_orders_end = ?",$condition["tp_orders_end"]);
        }
        if(isset($condition["tp_limit_orders"]) && $condition["tp_limit_orders"] != ""){
            $select->where("tp_limit_orders = ?",$condition["tp_limit_orders"]);
        }
        if(isset($condition["tp_maintenance_costs"]) && $condition["tp_maintenance_costs"] != ""){
            $select->where("tp_maintenance_costs = ?",$condition["tp_maintenance_costs"]);
        }
        if(isset($condition["tp_single_ticket_fees"]) && $condition["tp_single_ticket_fees"] != ""){
            $select->where("tp_single_ticket_fees = ?",$condition["tp_single_ticket_fees"]);
        }
        if(isset($condition["tp_currency_code"]) && $condition["tp_currency_code"] != ""){
            $select->where("tp_currency_code = ?",$condition["tp_currency_code"]);
        }
        if(isset($condition["tp_server"]) && $condition["tp_server"] != ""){
            $select->where("tp_server = ?",$condition["tp_server"]);
        }
        if(isset($condition["tp_implement"]) && $condition["tp_implement"] != ""){
            $select->where("tp_implement = ?",$condition["tp_implement"]);
        }
        if(isset($condition["tp_aftermarket"]) && $condition["tp_aftermarket"] != ""){
            $select->where("tp_aftermarket = ?",$condition["tp_aftermarket"]);
        }
        if(isset($condition["tp_two_domain_names"]) && $condition["tp_two_domain_names"] != ""){
            $select->where("tp_two_domain_names = ?",$condition["tp_two_domain_names"]);
        }
        if(isset($condition["tp_login_logo"]) && $condition["tp_login_logo"] != ""){
            $select->where("tp_login_logo = ?",$condition["tp_login_logo"]);
        }
        if(isset($condition["tp_upgrade"]) && $condition["tp_upgrade"] != ""){
            $select->where("tp_upgrade = ?",$condition["tp_upgrade"]);
        }
        if(isset($condition["tp_data_retention"]) && $condition["tp_data_retention"] != ""){
            $select->where("tp_data_retention = ?",$condition["tp_data_retention"]);
        }
        if(isset($condition["tp_customize"]) && $condition["tp_customize"] != ""){
            $select->where("tp_customize = ?",$condition["tp_customize"]);
        }
        if(isset($condition["tp_add_date"]) && $condition["tp_add_date"] != ""){
            $select->where("tp_add_date = ?",$condition["tp_add_date"]);
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