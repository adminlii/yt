<?php
class Table_CsdCustomerImportBatch
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsdCustomerImportBatch();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsdCustomerImportBatch();
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
    public function update($row, $value, $field = "ccib_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ccib_id")
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
    public function getByField($value, $field = 'ccib_id', $colums = "*")
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
        if(isset($condition["shipper_account"]) && $condition["shipper_account"] != ""){
            $select->where("shipper_account = ?",$condition["shipper_account"]);
        }
        if(isset($condition["filename"]) && $condition["filename"] != ""){
            $select->where("filename = ?",$condition["filename"]);
        }
        if(isset($condition["file_path"]) && $condition["file_path"] != ""){
            $select->where("file_path = ?",$condition["file_path"]);
        }
        if(isset($condition["ccib_status"]) && $condition["ccib_status"] != ""){
            $select->where("ccib_status = ?",$condition["ccib_status"]);
        }
        if(isset($condition["success_count"]) && $condition["success_count"] != ""){
            $select->where("success_count = ?",$condition["success_count"]);
        }
        if(isset($condition["fail_count"]) && $condition["fail_count"] != ""){
            $select->where("fail_count = ?",$condition["fail_count"]);
        }
        if(isset($condition["createdate"]) && $condition["createdate"] != ""){
            $select->where("createdate = ?",$condition["createdate"]);
        }
        if(isset($condition["creater_id"]) && $condition["creater_id"] != ""){
            $select->where("creater_id = ?",$condition["creater_id"]);
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