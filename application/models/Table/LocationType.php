<?php
class Table_LocationType
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_LocationType();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_LocationType();
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
    public function update($row, $value, $field = "lt_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "lt_id")
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
    public function getByField($value, $field = 'lt_id', $colums = "*")
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
        
        if(isset($condition["lt_code"]) && $condition["lt_code"] != ""){
            $select->where("lt_code = ?",$condition["lt_code"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["lt_description"]) && $condition["lt_description"] != ""){
            $select->where("lt_description = ?",$condition["lt_description"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if(isset($condition["lt_width"]) && $condition["lt_width"] != ""){
            $select->where("lt_width = ?",$condition["lt_width"]);
        }
        if(isset($condition["lt_length"]) && $condition["lt_length"] != ""){
            $select->where("lt_length = ?",$condition["lt_length"]);
        }
        if(isset($condition["lt_height"]) && $condition["lt_height"] != ""){
            $select->where("lt_height = ?",$condition["lt_height"]);
        }
        if(isset($condition["lt_vol"]) && $condition["lt_vol"] != ""){
            $select->where("lt_vol = ?",$condition["lt_vol"]);
        }
        if(isset($condition["lt_status"]) && $condition["lt_status"] != ""){
            $select->where("lt_status = ?",$condition["lt_status"]);
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
//             echo $sql;
//             exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}