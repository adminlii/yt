<?php
class Table_ShippingMethodSettings
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShippingMethodSettings();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShippingMethodSettings();
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
    public function update($row, $value, $field = "sms_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "sms_id")
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
    public function getByField($value, $field = 'sms_id', $colums = "*")
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
        
        if(isset($condition["sm_id"]) && $condition["sm_id"] != ""){
            $select->where("sm_id = ?",$condition["sm_id"]);
        }
        if(isset($condition["smt_fee_type"]) && $condition["smt_fee_type"] != ""){
            $select->where("smt_fee_type = ?",$condition["smt_fee_type"]);
        }
        if(isset($condition["smt_type"]) && $condition["smt_type"] != ""){
            $select->where("smt_type = ?",$condition["smt_type"]);
        }
        if(isset($condition["sms_customer_type"]) && $condition["sms_customer_type"] != ""){
            $select->where("sms_customer_type = ?",$condition["sms_customer_type"]);
        }
        if(isset($condition["sms_is_volume"]) && $condition["sms_is_volume"] != ""){
            $select->where("sms_is_volume = ?",$condition["sms_is_volume"]);
        }
        if(isset($condition["neq_sms_id"]) && $condition["neq_sms_id"] != ""){
            $select->where("sms_id != ?",$condition["neq_sms_id"]);
        }
        if(isset($condition["sms_supported_type"]) && $condition["sms_supported_type"] !== ""){
            $select->where("sms_supported_type = ?",$condition["sms_supported_type"]);
        }
        if (isset($condition["sms_supported_type_in"]) && is_array($condition["sms_supported_type_in"])) {
            $select->where("sms_supported_type in(?)", $condition["sms_supported_type_in"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] !== ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] !== ""){
        	$select->where("company_code in (?)",array('',$condition["company_code"]));
        }
        
        if (isset($condition["warehouse_id_in"]) && is_array($condition["warehouse_id_in"])) {
            $select->where("warehouse_id in (?)", $condition["warehouse_id_in"]);
        }
        
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] != ""){
        	$select->where("is_systematic = ?",$condition["is_systematic"]);
        }
        if(isset($condition["sms_status"]) && $condition["sms_status"] != ""){
            $select->where("sms_status = ?",$condition["sms_status"]);
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