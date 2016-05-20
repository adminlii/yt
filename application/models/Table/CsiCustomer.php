<?php
class Table_CsiCustomer
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsiCustomer();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsiCustomer();
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
    public function update($row, $value, $field = "customer_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "customer_id")
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
    public function getByField($value, $field = 'customer_id', $colums = "*")
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
        if(isset($condition["customer_shortname"]) && $condition["customer_shortname"] != ""){
            $select->where("customer_shortname = ?",$condition["customer_shortname"]);
        }
        if(isset($condition["customer_allname"]) && $condition["customer_allname"] != ""){
            $select->where("customer_allname = ?",$condition["customer_allname"]);
        }
        if(isset($condition["customerstatus_code"]) && $condition["customerstatus_code"] != ""){
            $select->where("customerstatus_code = ?",$condition["customerstatus_code"]);
        }
        if(isset($condition["customerlevel_code"]) && $condition["customerlevel_code"] != ""){
            $select->where("customerlevel_code = ?",$condition["customerlevel_code"]);
        }
        if(isset($condition["customertype_code"]) && $condition["customertype_code"] != ""){
            $select->where("customertype_code = ?",$condition["customertype_code"]);
        }
        if(isset($condition["customersource_code"]) && $condition["customersource_code"] != ""){
            $select->where("customersource_code = ?",$condition["customersource_code"]);
        }
        if(isset($condition["settlementtypes_code"]) && $condition["settlementtypes_code"] != ""){
            $select->where("settlementtypes_code = ?",$condition["settlementtypes_code"]);
        }
        if(isset($condition["customer_createdate"]) && $condition["customer_createdate"] != ""){
            $select->where("customer_createdate = ?",$condition["customer_createdate"]);
        }
        if(isset($condition["customer_createrid"]) && $condition["customer_createrid"] != ""){
            $select->where("customer_createrid = ?",$condition["customer_createrid"]);
        }
        if(isset($condition["server_status"]) && $condition["server_status"] != ""){
            $select->where("server_status = ?",$condition["server_status"]);
        }
        if(isset($condition["og_id"]) && $condition["og_id"] != ""){
            $select->where("og_id = ?",$condition["og_id"]);
        }
        if(isset($condition["tms_id"]) && $condition["tms_id"] != ""){
            $select->where("tms_id = ?",$condition["tms_id"]);
        }
        if(isset($condition["start_time"]) && $condition["start_time"] != ""){
            $select->where("start_time = ?",$condition["start_time"]);
        }
        if(isset($condition["sameday_time"]) && $condition["sameday_time"] != ""){
            $select->where("sameday_time = ?",$condition["sameday_time"]);
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