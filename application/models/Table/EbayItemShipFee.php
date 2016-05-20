<?php
class Table_EbayItemShipFee
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayItemShipFee();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayItemShipFee();
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
        
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["ship_type"]) && $condition["ship_type"] != ""){
            $select->where("ship_type = ?",$condition["ship_type"]);
        }
        if(isset($condition["shipping_service"]) && $condition["shipping_service"] != ""){
            $select->where("shipping_service = ?",$condition["shipping_service"]);
        }
        if(isset($condition["shipping_service_cost"]) && $condition["shipping_service_cost"] != ""){
            $select->where("shipping_service_cost = ?",$condition["shipping_service_cost"]);
        }
        if(isset($condition["shipping_service_cost_currency"]) && $condition["shipping_service_cost_currency"] != ""){
            $select->where("shipping_service_cost_currency = ?",$condition["shipping_service_cost_currency"]);
        }
        if(isset($condition["shipping_service_addtion_cost"]) && $condition["shipping_service_addtion_cost"] != ""){
            $select->where("shipping_service_addtion_cost = ?",$condition["shipping_service_addtion_cost"]);
        }
        if(isset($condition["shipping_service_addtion_cost_currency"]) && $condition["shipping_service_addtion_cost_currency"] != ""){
            $select->where("shipping_service_addtion_cost_currency = ?",$condition["shipping_service_addtion_cost_currency"]);
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