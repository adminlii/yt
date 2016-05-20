<?php
class Table_AddressBlacklist
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AddressBlacklist();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AddressBlacklist();
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
    public function update($row, $value, $field = "ab_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ab_id")
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
    public function getByField($value, $field = 'ab_id', $colums = "*")
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
        if(isset($condition["is_not_ab_id"]) && $condition["is_not_ab_id"] != ""){
        	$select->where("ab_id != ?",$condition["is_not_ab_id"]);
        }
        
        if(isset($condition["ab_processed_str"]) && $condition["ab_processed_str"] != ""){
        	$select->where("? like ab_processed_str",$condition["ab_processed_str"]);
        }
        if(isset($condition["name_like"]) && $condition["name_like"] != ""){
        	$select->where("ab_similitude_name like ?",'%'. $condition["name_like"] . '%');
        }
        if(isset($condition["ab_similitude_name"]) && $condition["ab_similitude_name"] != ""){
            $select->where("ab_similitude_name = ?",$condition["ab_similitude_name"]);
        }
        if(isset($condition["ab_verify_str"]) && $condition["ab_verify_str"] != ""){
            $select->where("ab_verify_str = ?",$condition["ab_verify_str"]);
        }
        if(isset($condition["ab_status"]) && $condition["ab_status"] != ""){
        	$select->where("ab_status = ?",$condition["ab_status"]);
        }
        if(isset($condition["create_date"]) && $condition["create_date"] != ""){
            $select->where("create_date = ?",$condition["create_date"]);
        }
        /*CONDITION_END*/
//         echo $select->__toString();
//         exit;
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