<?php
class Table_ProductCategory
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductCategory();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductCategory();
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
    public function update($row, $value, $field = "pc_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }
    
    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pc_id")
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
    public function getByField($value, $field = 'pc_id', $colums = "*")
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
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["pc_name_en"]) && $condition["pc_name_en"] != ""){
            $select->where("pc_name_en = ?",$condition["pc_name_en"]);
        }
        if(isset($condition["pc_name"]) && $condition["pc_name"] != ""){
            $select->where("pc_name = ?",$condition["pc_name"]);
        }
        if(isset($condition["pc_shortname"]) && $condition["pc_shortname"] != ""){
            $select->where("pc_shortname = ?",$condition["pc_shortname"]);
        }
        if(isset($condition["pc_hs_code"]) && $condition["pc_hs_code"] != ""){
            $select->where("pc_hs_code = ?",$condition["pc_hs_code"]);
        }
        if(isset($condition["pc_sort_id"]) && $condition["pc_sort_id"] != ""){
            $select->where("pc_sort_id = ?",$condition["pc_sort_id"]);
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