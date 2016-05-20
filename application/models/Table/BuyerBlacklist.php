<?php
class Table_BuyerBlacklist
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_BuyerBlacklist();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_BuyerBlacklist();
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
    public function update($row, $value, $field = "bb_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "bb_id")
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
    public function getByField($value, $field = 'bb_id', $colums = "*")
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
        
        
        if(isset($condition["is_not_bb_id"]) && $condition["is_not_bb_id"] != ""){
        	$select->where("bb_id != ?",$condition["is_not_bb_id"]);
        }
        if(isset($condition["bb_platform"]) && $condition["bb_platform"] != ""){
            $select->where("bb_platform = ?",$condition["bb_platform"]);
        }
        if(isset($condition["bb_similitude_type"]) && $condition["bb_similitude_type"] != ""){
            $select->where("bb_similitude_type = ?",$condition["bb_similitude_type"]);
        }
        if(isset($condition["bb_similitude_val"]) && $condition["bb_similitude_val"] != ""){
            $select->where("bb_similitude_val = ?",$condition["bb_similitude_val"]);
        }
        if(isset($condition["name_like"]) && $condition["name_like"] != ""){
        	$select->where("bb_similitude_val like ?",'%' . $condition["name_like"] . '%');
        }
        if(isset($condition["bb_verify_str"]) && $condition["bb_verify_str"] != ""){
            $select->where("bb_verify_str = ?",$condition["bb_verify_str"]);
        }
        if(isset($condition["bb_processed_str"]) && $condition["bb_processed_str"] != ""){
        	$select->where("? like bb_processed_str",$condition["bb_processed_str"]);
        }
        if(isset($condition["bb_status"]) && $condition["bb_status"] != ""){
            $select->where("bb_status = ?",$condition["bb_status"]);
        }
        if(isset($condition["create_id"]) && $condition["create_id"] != ""){
            $select->where("create_id = ?",$condition["create_id"]);
        }
        if(isset($condition["modify_id"]) && $condition["modify_id"] != ""){
            $select->where("modify_id = ?",$condition["modify_id"]);
        }
        if(isset($condition["create_date"]) && $condition["create_date"] != ""){
            $select->where("create_date = ?",$condition["create_date"]);
        }
        /*CONDITION_END*/
       
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
//         	echo $select->__toString();exit;
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