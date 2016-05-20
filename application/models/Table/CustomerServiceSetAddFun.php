<?php
class Table_CustomerServiceSetAddFun
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CustomerServiceSet();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CustomerServiceSetAddFun();
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionGroup($condition = array(), $type = '*', $groupBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["sell_range_type"]) && $condition["sell_range_type"] != ""){
            $select->where("sell_range_type = ?",$condition["sell_range_type"]);
        }
        if(isset($condition["message_type_id"]) && $condition["message_type_id"] != ""){
            $select->where("message_type_id = ?",$condition["message_type_id"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["customer_service_id"]) && $condition["customer_service_id"] != ""){
            $select->where("customer_service_id = ?",$condition["customer_service_id"]);
        }
        if(isset($condition["customer_service_status"]) && $condition["customer_service_status"] != ""){
            $select->where("customer_service_status = ?",$condition["customer_service_status"]);
        }
        /*CONDITION_END*/
        if ('count(*)' == $type) {
            return $this->_table->getAdapter()->fetchOne($select);
        } else {
            if (!empty($groupBy)) {
                $select->group($groupBy);
            }
            $sql = $select->__toString();
            
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}