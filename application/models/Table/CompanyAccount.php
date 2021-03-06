<?php
class Table_CompanyAccount
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CompanyAccount();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CompanyAccount();
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
    public function update($row, $value, $field = "ca_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ca_id")
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
    public function getByField($value, $field = 'ca_id', $colums = "*")
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
        if(isset($condition["ca_status"]) && $condition["ca_status"] != ""){
            $select->where("ca_status = ?",$condition["ca_status"]);
        }
        if(isset($condition["ca_balance"]) && $condition["ca_balance"] != ""){
            $select->where("ca_balance = ?",$condition["ca_balance"]);
        }
        if(isset($condition["ca_currency_code"]) && $condition["ca_currency_code"] != ""){
            $select->where("ca_currency_code = ?",$condition["ca_currency_code"]);
        }
        if(isset($condition["ca_add_date"]) && $condition["ca_add_date"] != ""){
            $select->where("ca_add_date = ?",$condition["ca_add_date"]);
        }
        if(isset($condition["ca_last_update_date"]) && $condition["ca_last_update_date"] != ""){
            $select->where("ca_last_update_date = ?",$condition["ca_last_update_date"]);
        }
        if(isset($condition["ca_update_id"]) && $condition["ca_update_id"] != ""){
            $select->where("ca_update_id = ?",$condition["ca_update_id"]);
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