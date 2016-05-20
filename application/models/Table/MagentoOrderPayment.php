<?php
class Table_MagentoOrderPayment
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MagentoOrderPayment();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MagentoOrderPayment();
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
    public function update($row, $value, $field = "mop_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "mop_id")
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
    public function getByField($value, $field = 'mop_id', $colums = "*")
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
        
        if(isset($condition["mo_id"]) && $condition["mo_id"] != ""){
            $select->where("mo_id = ?",$condition["mo_id"]);
        }
        if(isset($condition["parent_id"]) && $condition["parent_id"] != ""){
            $select->where("parent_id = ?",$condition["parent_id"]);
        }
        if(isset($condition["amount_ordered"]) && $condition["amount_ordered"] != ""){
            $select->where("amount_ordered = ?",$condition["amount_ordered"]);
        }
        if(isset($condition["shipping_amount"]) && $condition["shipping_amount"] != ""){
            $select->where("shipping_amount = ?",$condition["shipping_amount"]);
        }
        if(isset($condition["base_amount_ordered"]) && $condition["base_amount_ordered"] != ""){
            $select->where("base_amount_ordered = ?",$condition["base_amount_ordered"]);
        }
        if(isset($condition["base_shipping_amount"]) && $condition["base_shipping_amount"] != ""){
            $select->where("base_shipping_amount = ?",$condition["base_shipping_amount"]);
        }
        if(isset($condition["method"]) && $condition["method"] != ""){
            $select->where("method = ?",$condition["method"]);
        }
        if(isset($condition["cc_exp_month"]) && $condition["cc_exp_month"] != ""){
            $select->where("cc_exp_month = ?",$condition["cc_exp_month"]);
        }
        if(isset($condition["cc_exp_year"]) && $condition["cc_exp_year"] != ""){
            $select->where("cc_exp_year = ?",$condition["cc_exp_year"]);
        }
        if(isset($condition["cc_ss_start_month"]) && $condition["cc_ss_start_month"] != ""){
            $select->where("cc_ss_start_month = ?",$condition["cc_ss_start_month"]);
        }
        if(isset($condition["cc_ss_start_year"]) && $condition["cc_ss_start_year"] != ""){
            $select->where("cc_ss_start_year = ?",$condition["cc_ss_start_year"]);
        }
        if(isset($condition["payment_id"]) && $condition["payment_id"] != ""){
            $select->where("payment_id = ?",$condition["payment_id"]);
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