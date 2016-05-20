<?php
class Table_PaypalMassPay
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PaypalMassPay();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PaypalMassPay();
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
    public function update($row, $value, $field = "pmp_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pmp_id")
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
    public function getByField($value, $field = 'pmp_id', $colums = "*")
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
        
        if(isset($condition["receiver_type"]) && $condition["receiver_type"] != ""){
            $select->where("receiver_type = ?",$condition["receiver_type"]);
        }
        if(isset($condition["receiver_val"]) && $condition["receiver_val"] != ""){
            $select->where("receiver_val = ?",$condition["receiver_val"]);
        }
        if(isset($condition["receiver_val_like"]) && $condition["receiver_val_like"] != ""){
        	$select->where("receiver_val like ?",'%'.$condition["receiver_val_like"].'%');
        }
        
        if(isset($condition["amt"]) && $condition["amt"] != ""){
            $select->where("amt = ?",$condition["amt"]);
        }
        
        if(isset($condition["amt_from"]) && $condition["amt_from"] != ""){
        	$select->where("amt >= ?",$condition["amt_from"]);
        }
        if(isset($condition["amt_end"]) && $condition["amt_end"] != ""){
        	$select->where("amt <= ?",$condition["amt_end"]);
        } 
        
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["receiver_note"]) && $condition["receiver_note"] != ""){
            $select->where("receiver_note = ?",$condition["receiver_note"]);
        }
        if(isset($condition["refrence_no"]) && $condition["refrence_no"] != ""){
            $select->where("refrence_no = ?",$condition["refrence_no"]);
        }
        if(isset($condition["sys_note"]) && $condition["sys_note"] != ""){
            $select->where("sys_note = ?",$condition["sys_note"]);
        }
        if(isset($condition["create_id"]) && $condition["create_id"] != ""){
            $select->where("create_id = ?",$condition["create_id"]);
        }
        if(isset($condition["audit_id"]) && $condition["audit_id"] != ""){
            $select->where("audit_id = ?",$condition["audit_id"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
        if(isset($condition["paypal_tid"]) && $condition["paypal_tid"] != ""){
            $select->where("paypal_tid = ?",$condition["paypal_tid"]);
        }
        if(isset($condition["paypal_tid_empty"]) && $condition["paypal_tid_empty"] != ""){
        	$select->where("paypal_tid = ''");
        }
        if(isset($condition["pay_account"]) && $condition["pay_account"] != ""){
        	$select->where("pay_account = ?",$condition["pay_account"]);
        }
        if(isset($condition["pay_accounts"]) && $condition["pay_accounts"] != ""){
        	$condition["pay_accounts"][] = '';
        	$select->where("pay_account in (?)",$condition["pay_accounts"]);
        }
        if(isset($condition["rma_id"]) && $condition["rma_id"] != ""){
        	$select->where("rma_id = ?",$condition["rma_id"]);
        }

//         echo $select->__toString();exit;
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
//             echo $sql;exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}