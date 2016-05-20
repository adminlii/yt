<?php
class Table_FeeType
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_FeeType();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_FeeType();
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
    public function update($row, $value, $field = "ft_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ft_id")
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
    public function getByField($value, $field = 'ft_id', $colums = "*")
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
        
        if(isset($condition["ft_code"]) && $condition["ft_code"] != ""){
            $select->where("ft_code = ?",$condition["ft_code"]);
        }
        if(isset($condition["ft_name_en"]) && $condition["ft_name_en"] != ""){
            $select->where("ft_name_en = ?",$condition["ft_name_en"]);
        }
        if(isset($condition["ft_name_cn"]) && $condition["ft_name_cn"] != ""){
            $select->where("ft_name_cn = ?",$condition["ft_name_cn"]);
        }
        if(isset($condition["ft_note"]) && $condition["ft_note"] != ""){
            $select->where("ft_note = ?",$condition["ft_note"]);
        }
        if(isset($condition["ft_add_time"]) && $condition["ft_add_time"] != ""){
            $select->where("ft_add_time = ?",$condition["ft_add_time"]);
        }
        if(isset($condition["ft_update_time"]) && $condition["ft_update_time"] != ""){
            $select->where("ft_update_time = ?",$condition["ft_update_time"]);
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