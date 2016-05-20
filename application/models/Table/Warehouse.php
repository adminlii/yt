<?php
class Table_Warehouse
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Warehouse();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Warehouse();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        if (isset($row['warehouse_code'])) {
            $row['warehouse_code'] = strtoupper($row['warehouse_code']);
        }
        $row['warehouse_update_time'] = date('Y-m-d H:i:s');
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "warehouse_id")
    {
        if (isset($row['warehouse_code'])) {
            $row['warehouse_code'] = strtoupper($row['warehouse_code']);
        }
        $row['warehouse_update_time'] = date('Y-m-d H:i:s');
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "warehouse_id")
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
    public function getByField($value, $field = 'warehouse_id', $colums = "*")
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
        
        if(isset($condition["warehouse_code"]) && $condition["warehouse_code"] != ""){
            $select->where("warehouse_code = ?",$condition["warehouse_code"]);
        }
        if(isset($condition["warehouse_code_like"]) && $condition["warehouse_code_like"] != ""){
            $select->where("warehouse_code like ?",'%'.$condition["warehouse_code_like"].'%');
        }
        if(isset($condition["warehouse_status"]) && $condition["warehouse_status"] != ""){
            $select->where("warehouse_status = ?",$condition["warehouse_status"]);
        }
        if(isset($condition["country_id"]) && $condition["country_id"] != ""){
            $select->where("country_id = ?",$condition["country_id"]);
        }
        if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
            $select->where("warehouse_id = ?",$condition["warehouse_id"]);
        }
        if (isset($condition["warehouse_id_arr"]) && is_array($condition["warehouse_id_arr"]) && !empty($condition["warehouse_id_arr"])) {
            $select->where("warehouse_id in(?)", $condition["warehouse_id_arr"]);
        }
        if(isset($condition["country_id_neq"]) && $condition["country_id_neq"] != ""){
            $select->where("country_id != ?",$condition["country_id_neq"]);
        }
        if(isset($condition["warehouse_desc"]) && $condition["warehouse_desc"] != ""){
            $select->where("warehouse_desc = ?",$condition["warehouse_desc"]);
        }
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] != ""){
        	$select->where("is_systematic = ?",$condition["is_systematic"]);
        }
        if(isset($condition["warehouse_service"]) && $condition["warehouse_service"] != ""){
        	$select->where("warehouse_service = ?",$condition["warehouse_service"]);
        }
        
        if(isset($condition["warehouse_virtual"]) && $condition["warehouse_virtual"] != ""){
            $select->where("warehouse_virtual = ?",$condition["warehouse_virtual"]);
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
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getStandardWarehouse($type = array("warehouse_id","warehouse_code","warehouse_desc"))
    {
    	$select = $this->_table->getAdapter()->select();
    	$table = $this->_table->info('name');
    	$select->from($table, $type);
    	$select->where("1 =?", 1);
    	/*CONDITION_START*/
    	
    	$select->where("warehouse_type = ?",0);
    	$select->where("warehouse_status = ?",1);
    	
    	/*CONDITION_END*/
    	$sql = $select->__toString();
    	return $this->_table->getAdapter()->fetchAll($sql);
    }

    /**
     * @desc 获取头程仓库
     * @param array $type
     * @return array
     */
    public function getFirstWarehouse($type = array("warehouse_id", "warehouse_code", "warehouse_desc"))
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        $select->where("warehouse_type = ?", 1);
        $select->where("warehouse_status = ?", 1);
        /*CONDITION_END*/
        $sql = $select->__toString();
        return $this->_table->getAdapter()->fetchAll($sql);
    }


    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getJoinLeftByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft('warehouse_config', 'warehouse_config.warehouse_id=warehouse.warehouse_id');
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["warehouse_code"]) && $condition["warehouse_code"] != ""){
            $select->where("warehouse_code = ?",$condition["warehouse_code"]);
        }
        if(isset($condition["warehouse_code_like"]) && $condition["warehouse_code_like"] != ""){
            $select->where("warehouse_code like ?",'%'.$condition["warehouse_code_like"].'%');
        }
        if(isset($condition["warehouse_status"]) && $condition["warehouse_status"] != ""){
            $select->where("warehouse_status = ?",$condition["warehouse_status"]);
        }
        if(isset($condition["country_id"]) && $condition["country_id"] != ""){
            $select->where("country_id = ?",$condition["country_id"]);
        }
        if(isset($condition["state"]) && $condition["state"] != ""){
            $select->where("state = ?",$condition["state"]);
        }
        if(isset($condition["city"]) && $condition["city"] != ""){
            $select->where("city = ?",$condition["city"]);
        }
        if(isset($condition["city"]) && $condition["city"] != ""){
        	$select->where("city = ?",$condition["city"]);
        }
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] != ""){
            $select->where("is_systematic = ?",$condition["is_systematic"]);
        }
        if(isset($condition["warehouse_service"]) && $condition["warehouse_service"] != ""){
        	$select->where("warehouse_service = ?",$condition["warehouse_service"]);
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

    /**
     * @Group Country
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getGroupCountryByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        $select->joinInner("country", "country.country_id=warehouse.country_id",array('country_code','country_name','country_name_en'));
        /*CONDITION_START*/

        if(isset($condition["warehouse_code"]) && $condition["warehouse_code"] != ""){
            $select->where("warehouse_code = ?",$condition["warehouse_code"]);
        }
        if(isset($condition["warehouse_status"]) && $condition["warehouse_status"] != ""){
            $select->where("warehouse_status = ?",$condition["warehouse_status"]);
        }
        if(isset($condition["country_id"]) && $condition["country_id"] != ""){
            $select->where("country_id = ?",$condition["country_id"]);
        }
        if(isset($condition["is_systematic"]) && $condition["is_systematic"] != ""){
        	$select->where("is_systematic = ?",$condition["is_systematic"]);
        }
        if(isset($condition["warehouse_service"]) && $condition["warehouse_service"] != ""){
        	$select->where("warehouse_service = ?",$condition["warehouse_service"]);
        }
        
        $select->group(array('country.country_id'));
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