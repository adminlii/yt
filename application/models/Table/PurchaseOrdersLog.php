<?php
class Table_PurchaseOrdersLog
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PurchaseOrdersLog();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PurchaseOrdersLog();
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
    public function update($row, $value, $field = "pol_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pol_id")
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
    public function getByField($value, $field = 'pol_id', $colums = "*")
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
        
        if(isset($condition["pol_ref_no"]) && $condition["pol_ref_no"] != ""){
            $select->where("pol_ref_no = ?",$condition["pol_ref_no"]);
        }
        if(isset($condition["pol_ref_line_no"]) && $condition["pol_ref_line_no"] != ""){
            $select->where("pol_ref_line_no = ?",$condition["pol_ref_line_no"]);
        }
        if(isset($condition["pol_aciton_content"]) && $condition["pol_aciton_content"] != ""){
            $select->where("pol_aciton_content = ?",$condition["pol_aciton_content"]);
        }
        if(isset($condition["pol_action_operator"]) && $condition["pol_action_operator"] != ""){
            $select->where("pol_action_operator = ?",$condition["pol_action_operator"]);
        }
        if(isset($condition["pol_action_date"]) && $condition["pol_action_date"] != ""){
            $select->where("pol_action_date = ?",$condition["pol_action_date"]);
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
    
    public function getByFieldJoinLeft($value, $field = 'pol_id', $colums = "*")
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $colums);
    	$select->joinLeft('user','purchase_orders_log.pol_action_operator =  user.user_id',array('user_name'));
    	$select->where("{$field} = ?", $value);
    	 
    	return $this->_table->getAdapter()->fetchAll($select);
    }
}