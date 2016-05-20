<?php
class Table_OsOperatingStatistics
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_OsOperatingStatistics();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_OsOperatingStatistics();
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
    public function update($row, $value, $field = "os_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "os_id")
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
    public function getByField($value, $field = 'os_id', $colums = "*")
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
    	if ('count(*)' != $type) {
    		$type = array(
    				"*"
    				);
    		$type[] = "(select os_operating_statistics_node.os_name from os_operating_statistics_node where os_operating_statistics_node.os_node = os_operating_statistics.os_node) as os_name";
    		$type[] = "(select os_operating_statistics_node.os_data_type from os_operating_statistics_node where os_operating_statistics_node.os_node = os_operating_statistics.os_node) as os_data_type";
    		$type[] = "(select os_operating_statistics_node.os_ur_id from os_operating_statistics_node where os_operating_statistics_node.os_node = os_operating_statistics.os_node) as os_ur_id";
    	}
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        
        $opTable = new DbTable_OsOperatingStatisticsPanel();
        $tableOsOperatingStatisticsPanel = $opTable->info('name');
        if('count(*)' == $type){
        	$select->joinInner($tableOsOperatingStatisticsPanel, $table . '.os_panel_id = ' . $tableOsOperatingStatisticsPanel . '.panel_id', null);
        }else{
        	$select->joinInner($tableOsOperatingStatisticsPanel, $table . '.os_panel_id = ' . $tableOsOperatingStatisticsPanel . '.panel_id', array(
        			'osm_code',
        			'panel_name',
        			'panel_type'
        	));
        }
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["os_application_code"]) && $condition["os_application_code"] != ""){
            $select->where("os_application_code = ?",$condition["os_application_code"]);
        }
        if(isset($condition["os_node"]) && $condition["os_node"] != ""){
            $select->where("os_node = ?",$condition["os_node"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["os_node_amount"]) && $condition["os_node_amount"] != ""){
            $select->where("os_node_amount = ?",$condition["os_node_amount"]);
        }
        if(isset($condition["os_warehouse_id"]) && $condition["os_warehouse_id"] != ""){
            $select->where("os_warehouse_id = ?",$condition["os_warehouse_id"]);
        }
        if(isset($condition["os_user_account"]) && $condition["os_user_account"] != ""){
            $select->where("os_user_account = ?",$condition["os_user_account"]);
        }
        if(isset($condition["os_user_account_arr"]) && $condition["os_user_account_arr"] != ""){
        	$select->where("os_user_account in (?)",$condition["os_user_account_arr"]);
        }
        if(isset($condition["os_panel_id"]) && $condition["os_panel_id"] != ""){
            $select->where("os_panel_id = ?",$condition["os_panel_id"]);
        }
        if(isset($condition["os_panel_id_arr"]) && $condition["os_panel_id_arr"] != ""){
        	$select->where("os_panel_id in (?)",$condition["os_panel_id_arr"]);
        }
        if(isset($condition["osm_code_arr"]) && $condition["osm_code_arr"] != ""){
        	$select->where($tableOsOperatingStatisticsPanel . ".osm_code in (?)",$condition["osm_code_arr"]);
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
//             print_r($sql);
//             exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}