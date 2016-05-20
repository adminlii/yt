<?php
class Table_EbayOrderPayment
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayOrderPayment();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayOrderPayment();
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
    public function update($row, $value, $field = "eop_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "eop_id")
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
    public function getByField($value, $field = 'eop_id', $colums = "*")
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
        
        if(isset($condition["paymentstatus"]) && $condition["paymentstatus"] != ""){
            $select->where("paymentstatus = ?",$condition["paymentstatus"]);
        }
        if(isset($condition["unPaymentstatus"]) && $condition["unPaymentstatus"] != ""){
        	$select->where("paymentstatus != ?",$condition["unPaymentstatus"]);
        }
        if(isset($condition["payer"]) && $condition["payer"] != ""){
            $select->where("payer = ?",$condition["payer"]);
        }
        if(isset($condition["payee"]) && $condition["payee"] != ""){
            $select->where("payee = ?",$condition["payee"]);
        }
        if(isset($condition["paymentamount"]) && $condition["paymentamount"] != ""){
            $select->where("paymentamount = ?",$condition["paymentamount"]);
        }
        if(isset($condition["referenceid"]) && $condition["referenceid"] != ""){
            $select->where("referenceid = ?",$condition["referenceid"]);
        }
        if(isset($condition["feeorcreditamount"]) && $condition["feeorcreditamount"] != ""){
            $select->where("feeorcreditamount = ?",$condition["feeorcreditamount"]);
        }
        if(isset($condition["OrderID"]) && $condition["OrderID"] != ""){
            $select->where("OrderID = ?",$condition["OrderID"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
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