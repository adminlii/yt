<?php
class Table_OrderFeeSummery
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderFeeSummery();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderFeeSummery();
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
        
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["ref_id"]) && $condition["ref_id"] != ""){
            $select->where("ref_id = ?",$condition["ref_id"]);
        }
        if(isset($condition["shipping_method"]) && $condition["shipping_method"] != ""){
            $select->where("shipping_method = ?",$condition["shipping_method"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?",$condition["country_code"]);
        }
        if(isset($condition["order_weight"]) && $condition["order_weight"] != ""){
            $select->where("order_weight = ?",$condition["order_weight"]);
        }
        if(isset($condition["ship_cost"]) && $condition["ship_cost"] != ""){
            $select->where("ship_cost = ?",$condition["ship_cost"]);
        }
        if(isset($condition["op_cost"]) && $condition["op_cost"] != ""){
            $select->where("op_cost = ?",$condition["op_cost"]);
        }
        if(isset($condition["fuel_cost"]) && $condition["fuel_cost"] != ""){
            $select->where("fuel_cost = ?",$condition["fuel_cost"]);
        }
        if(isset($condition["register_cost"]) && $condition["register_cost"] != ""){
            $select->where("register_cost = ?",$condition["register_cost"]);
        }
        if(isset($condition["warehouse_cost"]) && $condition["warehouse_cost"] != ""){
            $select->where("warehouse_cost = ?",$condition["warehouse_cost"]);
        }
        if(isset($condition["tariff_cost"]) && $condition["tariff_cost"] != ""){
            $select->where("tariff_cost = ?",$condition["tariff_cost"]);
        }
        if(isset($condition["incidental_cost"]) && $condition["incidental_cost"] != ""){
            $select->where("incidental_cost = ?",$condition["incidental_cost"]);
        }
        if(isset($condition["bi_billing_date"]) && $condition["bi_billing_date"] != ""){
            $select->where("bi_billing_date = ?",$condition["bi_billing_date"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?",$condition["order_status"]);
        }
        if(isset($condition["date_release_from"]) && $condition["date_release_from"] != ""){
            $select->where("date_release >= ?",$condition["date_release_from"]);
        }
        if(isset($condition["date_release_to"]) && $condition["date_release_to"] != ""){
            $select->where("date_release <= ?",$condition["date_release_to"]);
        }
//         echo $select;exit;
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