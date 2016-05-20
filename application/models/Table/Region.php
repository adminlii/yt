<?php
class Table_Region
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Region();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Region();
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
    public function update($row, $value, $field = "region_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "region_id")
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
    public function getByField($value, $field = 'region_id', $colums = "*")
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
        if(isset($condition["region_id_arr"])&&is_array($condition["region_id_arr"])&&!empty($condition["region_id_arr"])){
            $select->where("region_id in (?)",$condition["region_id_arr"]);
            
        }
        if(isset($condition["parent_id"]) && $condition["parent_id"] != ""){
            $select->where("parent_id = ?",$condition["parent_id"]);
        }
        if(isset($condition["region_name"]) && $condition["region_name"] != ""){
            $select->where("region_name = ?",$condition["region_name"]);
        }
        if(isset($condition["region_type"]) && $condition["region_type"] != ""){
            $select->where("region_type = ?",$condition["region_type"]);
        }
        if(isset($condition["region_zip"]) && $condition["region_zip"] != ""){
            $select->where("region_zip = ?",$condition["region_zip"]);
        }
        if(isset($condition["phone_code"]) && $condition["phone_code"] != ""){
            $select->where("phone_code = ?",$condition["phone_code"]);
        }
        if(isset($condition["keyword"]) && $condition["keyword"] != ""){
            $select->where("keyword = ?",$condition["keyword"]);
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