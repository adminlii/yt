<?php
class Table_AliexpressLogisticsService
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AliexpressLogisticsService();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AliexpressLogisticsService();
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
    public function update($row, $value, $field = "als_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "als_id")
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
    public function getByField($value, $field = 'als_id', $colums = "*")
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
        
        if(isset($condition["recommend_order"]) && $condition["recommend_order"] != ""){
            $select->where("recommend_order = ?",$condition["recommend_order"]);
        }
        if(isset($condition["tracking_no_regex"]) && $condition["tracking_no_regex"] != ""){
            $select->where("tracking_no_regex = ?",$condition["tracking_no_regex"]);
        }
        if(isset($condition["logistics_company"]) && $condition["logistics_company"] != ""){
            $select->where("logistics_company = ?",$condition["logistics_company"]);
        }
        if(isset($condition["min_process_day"]) && $condition["min_process_day"] != ""){
            $select->where("min_process_day = ?",$condition["min_process_day"]);
        }
        if(isset($condition["max_process_day"]) && $condition["max_process_day"] != ""){
            $select->where("max_process_day = ?",$condition["max_process_day"]);
        }
        if(isset($condition["display_name"]) && $condition["display_name"] != ""){
            $select->where("display_name = ?",$condition["display_name"]);
        }
        if(isset($condition["service_name"]) && $condition["service_name"] != ""){
            $select->where("service_name = ?",$condition["service_name"]);
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