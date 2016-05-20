<?php
class Table_CsdInvoiceInfo
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsdInvoiceInfo();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsdInvoiceInfo();
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
    public function update($row, $value, $field = "id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "id")
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
    public function getByField($value, $field = 'id', $colums = "*")
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
        if(isset($condition["invoice_code"]) && $condition["invoice_code"] != ""){
            $select->where("invoice_code = ?",$condition["invoice_code"]);
        }
        if(isset($condition["invoice_cnname"]) && $condition["invoice_cnname"] != ""){
            $select->where("invoice_cnname = ?",$condition["invoice_cnname"]);
        }

        if(isset($condition["keyword"]) && $condition["keyword"] != ""){
            $select->where("invoice_cnname like ? or invoice_enname like ? or invoice_code like ?","%{$condition['keyword']}%");
        }
        
        if(isset($condition["invoice_enname"]) && $condition["invoice_enname"] != ""){
            $select->where("invoice_enname = ?",$condition["invoice_enname"]);
        }
        if(isset($condition["unit_code"]) && $condition["unit_code"] != ""){
            $select->where("unit_code = ?",$condition["unit_code"]);
        }
        if(isset($condition["invoice_unitcharge"]) && $condition["invoice_unitcharge"] != ""){
            $select->where("invoice_unitcharge = ?",$condition["invoice_unitcharge"]);
        }
        if(isset($condition["invoice_currencycode"]) && $condition["invoice_currencycode"] != ""){
            $select->where("invoice_currencycode = ?",$condition["invoice_currencycode"]);
        }
        if(isset($condition["hs_code"]) && $condition["hs_code"] != ""){
            $select->where("hs_code = ?",$condition["hs_code"]);
        }
        if(isset($condition["invoice_note"]) && $condition["invoice_note"] != ""){
            $select->where("invoice_note = ?",$condition["invoice_note"]);
        }
        if(isset($condition["invoice_url"]) && $condition["invoice_url"] != ""){
            $select->where("invoice_url = ?",$condition["invoice_url"]);
        }
        if(isset($condition["add_time"]) && $condition["add_time"] != ""){
            $select->where("add_time = ?",$condition["add_time"]);
        }
        if(isset($condition["update_time"]) && $condition["update_time"] != ""){
            $select->where("update_time = ?",$condition["update_time"]);
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