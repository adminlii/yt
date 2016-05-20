<?php
class Table_ProductCategoryEbay
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductCategoryEbay();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductCategoryEbay();
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
    public function update($row, $value, $field = "pce_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pce_id")
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
    public function getByField($value, $field = 'pce_id', $colums = "*")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $dbname = $this->_table->info('schema');
        $select->from($table, $colums);
        $select->where("{$field} = ?", $value);
        return $this->_table->getAdapter()->fetchRow($select);
    }

    public function getAll()
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $dbname = $this->_table->info('schema');
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
        $dbname = $this->_table->info('schema');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["pce_title_like"]) && $condition["pce_title_like"] != ""){
            $select->where("pce_title like ?",'%'.$condition["pce_title_like"].'%');
        }
        if(isset($condition["pce_title"]) && $condition["pce_title"] != ""){
            $select->where("pce_title = ?",$condition["pce_title"]);
        }
        if(isset($condition["pce_ebay_cid"]) && $condition["pce_ebay_cid"] != ""){
            $select->where("pce_ebay_cid = ?",$condition["pce_ebay_cid"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["pce_level"]) && $condition["pce_level"] != ""){
            $select->where("pce_level = ?",$condition["pce_level"]);
        }
        if(isset($condition["pce_hs_code"]) && $condition["pce_hs_code"] != ""){
            $select->where("pce_hs_code = ?",$condition["pce_hs_code"]);
        }
        if(isset($condition["pce_parent_id"]) && $condition["pce_parent_id"] != ""){
            $select->where("pce_parent_id = ?",$condition["pce_parent_id"]);
        }
        if(isset($condition["pce_status"]) && $condition["pce_status"] != ""){
            $select->where("pce_status = ?",$condition["pce_status"]);
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