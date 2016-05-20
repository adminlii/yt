<?php
class Table_Country
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Country();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Country();
    }

    /**
     *
     * @param
     *            $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "country_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->delete($where);
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
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public function getByField($value, $field = 'country_id', $colums = "*")
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
        
        if(isset($condition["country_name"]) && $condition["country_name"] != ""){
            $select->where("country_name = ?",$condition["country_name"]);
        }
        if(isset($condition["country_name_en"]) && $condition["country_name_en"] != ""){
            $select->where("country_name_en = ?",$condition["country_name_en"]);
        }
        if(isset($condition["country_local_name"]) && $condition["country_local_name"] != ""){
            $select->where("country_local_name = ?",$condition["country_local_name"]);
        }
        if(isset($condition["country_alias"]) && $condition["country_alias"] != ""){
            $select->where("country_alias = ?",$condition["country_alias"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?",$condition["country_code"]);
        }
        if(isset($condition["country_sort"]) && $condition["country_sort"] != ""){
            $select->where("country_sort = ?",$condition["country_sort"]);
        }
        if(isset($condition["country_short_name"]) && $condition["country_short_name"] != ""){
            $select->where("country_short_name = ?",$condition["country_short_name"]);
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