<?php
class Table_ReceivingCost
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingCost();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingCost();
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
    public function update($row, $value, $field = "rc_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rc_id")
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
    public function getByField($value, $field = 'rc_id', $colums = "*")
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
        
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["shipment_type"]) && $condition["shipment_type"] != ""){
            $select->where("shipment_type = ?",$condition["shipment_type"]);
        }
        if(isset($condition["weight"]) && $condition["weight"] != ""){
            $select->where("weight = ?",$condition["weight"]);
        }
        if(isset($condition["volume_weight"]) && $condition["volume_weight"] != ""){
            $select->where("volume_weight = ?",$condition["volume_weight"]);
        }
        if(isset($condition["declared_value"]) && $condition["declared_value"] != ""){
            $select->where("declared_value = ?",$condition["declared_value"]);
        }
        if(isset($condition["insurance_value"]) && $condition["insurance_value"] != ""){
            $select->where("insurance_value = ?",$condition["insurance_value"]);
        }
        if(isset($condition["tariff_fee"]) && $condition["tariff_fee"] != ""){
            $select->where("tariff_fee = ?",$condition["tariff_fee"]);
        }
        if(isset($condition["shipping_fee"]) && $condition["shipping_fee"] != ""){
            $select->where("shipping_fee = ?",$condition["shipping_fee"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["currency_rate"]) && $condition["currency_rate"] != ""){
            $select->where("currency_rate = ?",$condition["currency_rate"]);
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