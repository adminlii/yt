<?php
class Table_ProductShareInventoryLog
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductShareInventoryLog();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductShareInventoryLog();
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
    public function update($row, $value, $field = "psil_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "psil_id")
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
    public function getByField($value, $field = 'psil_id', $colums = "*")
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
        
        if(isset($condition["psi_id"]) && $condition["psi_id"] != ""){
            $select->where("psi_id = ?",$condition["psi_id"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["psi_shared"]) && $condition["psi_shared"] != ""){
            $select->where("psi_shared = ?",$condition["psi_shared"]);
        }
        if(isset($condition["psi_sharing"]) && $condition["psi_sharing"] != ""){
            $select->where("psi_sharing = ?",$condition["psi_sharing"]);
        }
        if(isset($condition["psi_stopped_share"]) && $condition["psi_stopped_share"] != ""){
            $select->where("psi_stopped_share = ?",$condition["psi_stopped_share"]);
        }
        if(isset($condition["psi_borrowed"]) && $condition["psi_borrowed"] != ""){
            $select->where("psi_borrowed = ?",$condition["psi_borrowed"]);
        }
        if(isset($condition["reference_no"]) && $condition["reference_no"] != ""){
            $select->where("reference_no = ?",$condition["reference_no"]);
        }
        if(isset($condition["application_code"]) && $condition["application_code"] != ""){
            $select->where("application_code = ?",$condition["application_code"]);
        }
        if(isset($condition["psil_type"]) && $condition["psil_type"] != ""){
            $select->where("psil_type = ?",$condition["psil_type"]);
        }
        if(isset($condition["psil_quantity"]) && $condition["psil_quantity"] != ""){
            $select->where("psil_quantity = ?",$condition["psil_quantity"]);
        }
        if(isset($condition["from_type"]) && $condition["from_type"] != ""){
            $select->where("from_type = ?",$condition["from_type"]);
        }
        if(isset($condition["to_type"]) && $condition["to_type"] != ""){
            $select->where("to_type = ?",$condition["to_type"]);
        }
        if(isset($condition["creator_id"]) && $condition["creator_id"] != ""){
            $select->where("creator_id = ?",$condition["creator_id"]);
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