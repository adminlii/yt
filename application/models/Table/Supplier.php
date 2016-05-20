<?php
class Table_Supplier
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Supplier();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Supplier();
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
    public function update($row, $value, $field = "supplier_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "supplier_id")
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
    public function getByField($value, $field = 'supplier_id', $colums = "*")
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
        if(isset($condition["supplier_code"]) && $condition["supplier_code"] != ""){
            $select->where("supplier_code = ?",$condition["supplier_code"]);
        }
        if(isset($condition["supplier_name"]) && $condition["supplier_name"] != ""){
            $select->where("supplier_name = ?",$condition["supplier_name"]);
        }
        if(isset($condition["level"]) && $condition["level"] != ""){
            $select->where("level = ?",$condition["level"]);
        }
        if(isset($condition["supplier_type"]) && $condition["supplier_type"] != ""){
            $select->where("supplier_type = ?",$condition["supplier_type"]);
        }
        if(isset($condition["account_type"]) && $condition["account_type"] != ""){
            $select->where("account_type = ?",$condition["account_type"]);
        }
        if(isset($condition["pay_type"]) && $condition["pay_type"] != ""){
            $select->where("pay_type = ?",$condition["pay_type"]);
        }
        if(isset($condition["pay_card"]) && $condition["pay_card"] != ""){
            $select->where("pay_card = ?",$condition["pay_card"]);
        }
        if(isset($condition["pay_name"]) && $condition["pay_name"] != ""){
            $select->where("pay_name = ?",$condition["pay_name"]);
        }
        if(isset($condition["pay_bank"]) && $condition["pay_bank"] != ""){
            $select->where("pay_bank = ?",$condition["pay_bank"]);
        }
        if(isset($condition["pc_id"]) && $condition["pc_id"] != ""){
            $select->where("pc_id = ?",$condition["pc_id"]);
        }
        if(isset($condition["supplier_teamwork_type"]) && $condition["supplier_teamwork_type"] != ""){
            $select->where("supplier_teamwork_type = ?",$condition["supplier_teamwork_type"]);
        }
        if(isset($condition["supplier_main_category_id"]) && $condition["supplier_main_category_id"] != ""){
            $select->where("supplier_main_category_id = ?",$condition["supplier_main_category_id"]);
        }
        if(isset($condition["supplier_status"]) && $condition["supplier_status"] != ""){
            $select->where("supplier_status = ?",$condition["supplier_status"]);
        }
        if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
            $select->where("buyer_id = ?",$condition["buyer_id"]);
        }
        if(isset($condition["account_cycleTime"]) && $condition["account_cycleTime"] != ""){
            $select->where("account_cycleTime = ?",$condition["account_cycleTime"]);
        }
        if(isset($condition["account_proportion"]) && $condition["account_proportion"] != ""){
            $select->where("account_proportion = ?",$condition["account_proportion"]);
        }
        if(isset($condition["supplier_qc_exception"]) && $condition["supplier_qc_exception"] != ""){
            $select->where("supplier_qc_exception = ?",$condition["supplier_qc_exception"]);
        }
        if(isset($condition["supplier_carrier"]) && $condition["supplier_carrier"] != ""){
            $select->where("supplier_carrier = ?",$condition["supplier_carrier"]);
        }
        if(isset($condition["supplier_ship_pay_type"]) && $condition["supplier_ship_pay_type"] != ""){
            $select->where("supplier_ship_pay_type = ?",$condition["supplier_ship_pay_type"]);
        }
        if(isset($condition["pay_platform"]) && $condition["pay_platform"] != ""){
            $select->where("pay_platform = ?",$condition["pay_platform"]);
        }
        if(isset($condition["contact_name"]) && $condition["contact_name"] != ""){
            $select->where("contact_name = ?",$condition["contact_name"]);
        }
        if(isset($condition["contact_tel"]) && $condition["contact_tel"] != ""){
            $select->where("contact_tel = ?",$condition["contact_tel"]);
        }
        if(isset($condition["contact_address"]) && $condition["contact_address"] != ""){
            $select->where("contact_address = ?",$condition["contact_address"]);
        }
        if(isset($condition["contact_zip"]) && $condition["contact_zip"] != ""){
            $select->where("contact_zip = ?",$condition["contact_zip"]);
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