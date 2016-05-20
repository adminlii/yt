<?php
class Table_ProductUom
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductUom();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductUom();
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
    public function update($row, $value, $field = "pu_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pu_id")
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
    public function getByField($value, $field = 'pu_id', $colums = "*")
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
        
        if(isset($condition["pu_code"]) && $condition["pu_code"] != ""){
            $select->where("pu_code = ?",$condition["pu_code"]);
        }
        if(isset($condition["pu_name"]) && $condition["pu_name"] != ""){
            $select->where("pu_name = ?",$condition["pu_name"]);
        }
        if(isset($condition["pu_name_en"]) && $condition["pu_name_en"] != ""){
            $select->where("pu_name_en = ?",$condition["pu_name_en"]);
        }
        if(isset($condition["pu_hs_code"]) && $condition["pu_hs_code"] != ""){
            $select->where("pu_hs_code = ?",$condition["pu_hs_code"]);
        }
        if(isset($condition["pu_sort"]) && $condition["pu_sort"] != ""){
            $select->where("pu_sort = ?",$condition["pu_sort"]);
        }
        if(isset($condition["pu_update_time"]) && $condition["pu_update_time"] != ""){
            $select->where("pu_update_time = ?",$condition["pu_update_time"]);
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