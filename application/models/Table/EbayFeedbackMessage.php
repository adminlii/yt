<?php
class Table_EbayFeedbackMessage
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayFeedbackMessage();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayFeedbackMessage();
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
    public function update($row, $value, $field = "efm_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "efm_id")
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
    public function getByField($value, $field = 'efm_id', $colums = "*")
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
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["ref_no_platform"]) && $condition["ref_no_platform"] != ""){
            $select->where("ref_no_platform = ?",$condition["ref_no_platform"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["content"]) && $condition["content"] != ""){
            $select->where("content = ?",$condition["content"]);
        }
        if(isset($condition["sync_status"]) && $condition["sync_status"] != ""){
            $select->where("sync_status = ?",$condition["sync_status"]);
        }
        if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
        	$select->where("buyer_id = ?",$condition["buyer_id"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
        	$select->where("user_account = ?",$condition["user_account"]);
        }
        
        if(isset($condition["createDateFrom"]) && $condition["createDateFrom"] != ""){
        	$select->where("create_time > ?",$condition["createDateFrom"]);
        }
        if(isset($condition["createDateEnd"]) && $condition["createDateEnd"] != ""){
        	$select->where("create_time < ?",$condition["createDateEnd"]);
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