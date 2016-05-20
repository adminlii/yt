<?php
class Table_ProductPackage
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductPackage();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductPackage();
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
    public function update($row, $value, $field = "pp_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pp_id")
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
    public function getByField($value, $field = 'pp_id', $colums = "*")
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
        
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["customer_code"]) && $condition["customer_code"] != ""){
            $select->where("customer_code = ?",$condition["customer_code"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["pp_barcode"]) && $condition["pp_barcode"] != ""){
            $select->where("pp_barcode = ?",$condition["pp_barcode"]);
        }
        if(isset($condition["pp_type"]) && $condition["pp_type"] != ""){
            $select->where("pp_type = ?",$condition["pp_type"]);
        }
        if(isset($condition["pp_status"]) && $condition["pp_status"] != ""){
            $select->where("pp_status = ?",$condition["pp_status"]);
        }
        if(isset($condition["pp_quantity"]) && $condition["pp_quantity"] != ""){
            $select->where("pp_quantity = ?",$condition["pp_quantity"]);
        }
        if(isset($condition["pp_cost"]) && $condition["pp_cost"] != ""){
            $select->where("pp_cost = ?",$condition["pp_cost"]);
        }
        if(isset($condition["pp_price"]) && $condition["pp_price"] != ""){
            $select->where("pp_price = ?",$condition["pp_price"]);
        }
        if(isset($condition["currency_code"]) && $condition["currency_code"] != ""){
            $select->where("currency_code = ?",$condition["currency_code"]);
        }
        if(isset($condition["pp_name_en"]) && $condition["pp_name_en"] != ""){
            $select->where("pp_name_en = ?",$condition["pp_name_en"]);
        }
        if(isset($condition["pp_name"]) && $condition["pp_name"] != ""){
            $select->where("pp_name = ?",$condition["pp_name"]);
        }
        if(isset($condition["pp_length"]) && $condition["pp_length"] != ""){
            $select->where("pp_length = ?",$condition["pp_length"]);
        }
        if(isset($condition["pp_width"]) && $condition["pp_width"] != ""){
            $select->where("pp_width = ?",$condition["pp_width"]);
        }
        if(isset($condition["pp_height"]) && $condition["pp_height"] != ""){
            $select->where("pp_height = ?",$condition["pp_height"]);
        }
        if(isset($condition["pp_weight"]) && $condition["pp_weight"] != ""){
            $select->where("pp_weight = ?",$condition["pp_weight"]);
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