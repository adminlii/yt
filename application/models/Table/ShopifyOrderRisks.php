<?php
class Table_ShopifyOrderRisks
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyOrderRisks();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyOrderRisks();
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
        
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["cause_cancel"]) && $condition["cause_cancel"] != ""){
            $select->where("cause_cancel = ?",$condition["cause_cancel"]);
        }
        if(isset($condition["checkout_id"]) && $condition["checkout_id"] != ""){
            $select->where("checkout_id = ?",$condition["checkout_id"]);
        }
        if(isset($condition["display"]) && $condition["display"] != ""){
            $select->where("display = ?",$condition["display"]);
        }
        if(isset($condition["message"]) && $condition["message"] != ""){
            $select->where("message = ?",$condition["message"]);
        }
        if(isset($condition["merchant_message"]) && $condition["merchant_message"] != ""){
            $select->where("merchant_message = ?",$condition["merchant_message"]);
        }
        if(isset($condition["source"]) && $condition["source"] != ""){
            $select->where("source = ?",$condition["source"]);
        }
        if(isset($condition["score"]) && $condition["score"] != ""){
            $select->where("score = ?",$condition["score"]);
        }
        if(isset($condition["recommendation"]) && $condition["recommendation"] != ""){
            $select->where("recommendation = ?",$condition["recommendation"]);
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