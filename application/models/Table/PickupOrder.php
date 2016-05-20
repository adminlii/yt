<?php
class Table_PickupOrder
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PickupOrder();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PickupOrder();
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
    public function update($row, $value, $field = "tms_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "tms_id")
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
    public function getByField($value, $field = 'tms_id', $colums = "*")
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
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["status_type"]) && $condition["status_type"] != ""){
            $select->where("status_type = ?",$condition["status_type"]);
        }
        if(isset($condition["addres_id"]) && $condition["addres_id"] != ""){
            $select->where("addres_id = ?",$condition["addres_id"]);
        }
        if(isset($condition["addres_name"]) && $condition["addres_name"] != ""){
            $select->where("addres_name = ?",$condition["addres_name"]);
        }
        if(isset($condition["pickup_og_id"]) && $condition["pickup_og_id"] != ""){
            $select->where("pickup_og_id = ?",$condition["pickup_og_id"]);
        }
        if(isset($condition["bags"]) && $condition["bags"] != ""){
            $select->where("bags = ?",$condition["bags"]);
        }
        if(isset($condition["pieces"]) && $condition["pieces"] != ""){
            $select->where("pieces = ?",$condition["pieces"]);
        }
        if(isset($condition["weight"]) && $condition["weight"] != ""){
            $select->where("weight = ?",$condition["weight"]);
        }
        if(isset($condition["pickup_server_id"]) && $condition["pickup_server_id"] != ""){
            $select->where("pickup_server_id = ?",$condition["pickup_server_id"]);
        }
        if(isset($condition["pickup_type_code"]) && $condition["pickup_type_code"] != ""){
            $select->where("pickup_type_code = ?",$condition["pickup_type_code"]);
        }
        if(isset($condition["driver_id"]) && $condition["driver_id"] != ""){
            $select->where("driver_id = ?",$condition["driver_id"]);
        }
        if(isset($condition["track_number"]) && $condition["track_number"] != ""){
            $select->where("track_number = ?",$condition["track_number"]);
        }
        if(isset($condition["arrivalbatch_id"]) && $condition["arrivalbatch_id"] != ""){
            $select->where("arrivalbatch_id = ?",$condition["arrivalbatch_id"]);
        }
        if(isset($condition["create_date"]) && $condition["create_date"] != ""){
            $select->where("create_date = ?",$condition["create_date"]);
        }
        if(isset($condition["modify_date"]) && $condition["modify_date"] != ""){
            $select->where("modify_date = ?",$condition["modify_date"]);
        }
        if(isset($condition["confirmor_id"]) && $condition["confirmor_id"] != ""){
            $select->where("confirmor_id = ?",$condition["confirmor_id"]);
        }
        if(isset($condition["confirm_date"]) && $condition["confirm_date"] != ""){
            $select->where("confirm_date = ?",$condition["confirm_date"]);
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