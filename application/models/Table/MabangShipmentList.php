<?php
class Table_MabangShipmentList
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MabangShipmentList();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MabangShipmentList();
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
    public function update($row, $value, $field = "msl_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "msl_id")
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
    public function getByField($value, $field = 'msl_id', $colums = "*")
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
        
        if(isset($condition["moo_id"]) && $condition["moo_id"] != ""){
            $select->where("moo_id = ?",$condition["moo_id"]);
        }
        if(isset($condition["code"]) && $condition["code"] != ""){
            $select->where("code = ?",$condition["code"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["productName"]) && $condition["productName"] != ""){
            $select->where("productName = ?",$condition["productName"]);
        }
        if(isset($condition["declareNameCn"]) && $condition["declareNameCn"] != ""){
            $select->where("declareNameCn = ?",$condition["declareNameCn"]);
        }
        if(isset($condition["declareNameEn"]) && $condition["declareNameEn"] != ""){
            $select->where("declareNameEn = ?",$condition["declareNameEn"]);
        }
        if(isset($condition["weight"]) && $condition["weight"] != ""){
            $select->where("weight = ?",$condition["weight"]);
        }
        if(isset($condition["quantity"]) && $condition["quantity"] != ""){
            $select->where("quantity = ?",$condition["quantity"]);
        }
        if(isset($condition["declareValue"]) && $condition["declareValue"] != ""){
            $select->where("declareValue = ?",$condition["declareValue"]);
        }
        if(isset($condition["itemUrl"]) && $condition["itemUrl"] != ""){
            $select->where("itemUrl = ?",$condition["itemUrl"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["create_time_sys"]) && $condition["create_time_sys"] != ""){
            $select->where("create_time_sys = ?",$condition["create_time_sys"]);
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