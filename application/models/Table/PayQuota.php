<?php
class Table_PayQuota
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PayQuota();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PayQuota();
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
    public function update($row, $value, $field = "pq_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pq_id")
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
    public function getByField($value, $field = 'pq_id', $colums = "*")
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
        
        if(isset($condition["up_id"]) && $condition["up_id"] != ""){
            $select->where("up_id = ?",$condition["up_id"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
        }
        if(isset($condition["amount"]) && $condition["amount"] != ""){
            $select->where("amount = ?",$condition["amount"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["create_id"]) && $condition["create_id"] != ""){
            $select->where("create_id = ?",$condition["create_id"]);
        }
        if(isset($condition["modify_id"]) && $condition["modify_id"] != ""){
            $select->where("modify_id = ?",$condition["modify_id"]);
        }
        if(isset($condition["user_name_like"]) && $condition["user_name_like"] != ""){
        	$select->where("user_id in (select user.user_id from user where user_name like ? or user_name_en like ?)",'%'.$condition["user_name_like"].'%');
        }
        if(isset($condition["quota_type"]) && $condition["quota_type"] != ""){
        	if($condition["quota_type"] == '1'){
        		$select->where("up_id != 0");
        	}
        	if($condition["quota_type"] == '2'){
        		$select->where("user_id != 0");
        	}
        }
        if(isset($condition["amount_refund"]) && $condition["amount_refund"] != ""){
        	$select->where("amount_refund = ?",$condition["amount_refund"]);
        }
        if(isset($condition["amount_refund_not_audit"]) && $condition["amount_refund_not_audit"] != ""){
        	$select->where("amount_refund_not_audit = ?",$condition["amount_refund_not_audit"]);
        }
        if(isset($condition["is_not_audit"]) && $condition["is_not_audit"] != ""){
        	$select->where("is_not_audit = ?",$condition["is_not_audit"]);
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
//             echo $select->__toString();exit;
            $sql = $select->__toString();
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}