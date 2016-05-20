<?php
class Table_OrderProductToWms
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OrderProductToWms();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OrderProductToWms();
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
        
        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["warehouse_sku"]) && $condition["warehouse_sku"] != ""){
            $select->where("warehouse_sku = ?",$condition["warehouse_sku"]);
        }
        if(isset($condition["quantity"]) && $condition["quantity"] != ""){
            $select->where("quantity = ?",$condition["quantity"]);
        }
        if(isset($condition["ref_tnx"]) && $condition["ref_tnx"] != ""){
            $select->where("ref_tnx = ?",$condition["ref_tnx"]);
        }
        if(isset($condition["recv_account"]) && $condition["recv_account"] != ""){
            $select->where("recv_account = ?",$condition["recv_account"]);
        }
        if(isset($condition["ref_item_id"]) && $condition["ref_item_id"] != ""){
            $select->where("ref_item_id = ?",$condition["ref_item_id"]);
        }
        if(isset($condition["ref_buyer_id"]) && $condition["ref_buyer_id"] != ""){
            $select->where("ref_buyer_id = ?",$condition["ref_buyer_id"]);
        }
        if(isset($condition["ref_pay_date"]) && $condition["ref_pay_date"] != ""){
            $select->where("ref_pay_date = ?",$condition["ref_pay_date"]);
        }
        if(isset($condition["ref_id"]) && $condition["ref_id"] != ""){
            $select->where("ref_id = ?",$condition["ref_id"]);
        }
        if(isset($condition["unit_price"]) && $condition["unit_price"] != ""){
            $select->where("unit_price = ?",$condition["unit_price"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["finalvaluefee"]) && $condition["finalvaluefee"] != ""){
            $select->where("finalvaluefee = ?",$condition["finalvaluefee"]);
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