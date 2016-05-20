<?php
class Table_CompanyPaymentDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CompanyPaymentDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CompanyPaymentDetail();
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
    public function update($row, $value, $field = "cpd_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "cpd_id")
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
    public function getByField($value, $field = 'cpd_id', $colums = "*")
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
        
        if(isset($condition["cpd_platform"]) && $condition["cpd_platform"] != ""){
            $select->where("cpd_platform = ?",$condition["cpd_platform"]);
        }
        if(isset($condition["cpd_status"]) && $condition["cpd_status"] != ""){
            $select->where("cpd_status = ?",$condition["cpd_status"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["cpd_code"]) && $condition["cpd_code"] != ""){
            $select->where("cpd_code = ?",$condition["cpd_code"]);
        }
        if(isset($condition["cpd_ref_no"]) && $condition["cpd_ref_no"] != ""){
            $select->where("cpd_ref_no = ?",$condition["cpd_ref_no"]);
        }
        if(isset($condition["cpd_code_platform"]) && $condition["cpd_code_platform"] != ""){
            $select->where("cpd_code_platform = ?",$condition["cpd_code_platform"]);
        }
        if(isset($condition["cpd_ref_no_platform"]) && $condition["cpd_ref_no_platform"] != ""){
            $select->where("cpd_ref_no_platform = ?",$condition["cpd_ref_no_platform"]);
        }
        if(isset($condition["cpd_amount"]) && $condition["cpd_amount"] != ""){
            $select->where("cpd_amount = ?",$condition["cpd_amount"]);
        }
        if(isset($condition["cpd_currency_code"]) && $condition["cpd_currency_code"] != ""){
            $select->where("cpd_currency_code = ?",$condition["cpd_currency_code"]);
        }
        if(isset($condition["cpd_add_date"]) && $condition["cpd_add_date"] != ""){
            $select->where("cpd_add_date = ?",$condition["cpd_add_date"]);
        }
        if(isset($condition["cpd_last_update_date"]) && $condition["cpd_last_update_date"] != ""){
            $select->where("cpd_last_update_date = ?",$condition["cpd_last_update_date"]);
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