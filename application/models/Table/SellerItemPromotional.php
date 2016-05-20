<?php
class Table_SellerItemPromotional
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_SellerItemPromotional();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_SellerItemPromotional();
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
    public function update($row, $value, $field = "sip_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "sip_id")
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
    public function getByField($value, $field = 'sip_id', $colums = "*")
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
        
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["promotional_sale_id"]) && $condition["promotional_sale_id"] != ""){
            $select->where("promotional_sale_id = ?",$condition["promotional_sale_id"]);
        }
        if(isset($condition["promotional_sale_name"]) && $condition["promotional_sale_name"] != ""){
            $select->where("promotional_sale_name = ?",$condition["promotional_sale_name"]);
        }
        if(isset($condition["promotional_sale_name_like"]) && $condition["promotional_sale_name_like"] != ""){
            $select->where("promotional_sale_name like ?","%{$condition["promotional_sale_name_like"]}%");
        }
        
        if(isset($condition["promotional_sale_item_id_array"]) && $condition["promotional_sale_item_id_array"] != ""){
            $select->where("promotional_sale_item_id_array = ?",$condition["promotional_sale_item_id_array"]);
        }

        if(isset($condition["item_id_like"]) && $condition["item_id_like"] != ""){
            $select->where("promotional_sale_item_id_array like ?","%{$condition["item_id_like"]}%");
        }
        if(isset($condition["promotional_status"]) && $condition["promotional_status"] != ""){
            $select->where("promotional_status = ?",$condition["promotional_status"]);
        }
        if(isset($condition["discount_type"]) && $condition["discount_type"] != ""){
            $select->where("discount_type = ?",$condition["discount_type"]);
        }
        if(isset($condition["discount_value"]) && $condition["discount_value"] != ""){
            $select->where("discount_value = ?",$condition["discount_value"]);
        }
        if(isset($condition["promotional_sale_start_time"]) && $condition["promotional_sale_start_time"] != ""){
            $select->where("promotional_sale_start_time = ?",$condition["promotional_sale_start_time"]);
        }
        if(isset($condition["promotional_sale_end_time"]) && $condition["promotional_sale_end_time"] != ""){
            $select->where("promotional_sale_end_time = ?",$condition["promotional_sale_end_time"]);
        }
        if(isset($condition["promotional_sale_type"]) && $condition["promotional_sale_type"] != ""){
            $select->where("promotional_sale_type = ?",$condition["promotional_sale_type"]);
        }
        if(isset($condition["last_modify_time"]) && $condition["last_modify_time"] != ""){
            $select->where("last_modify_time = ?",$condition["last_modify_time"]);
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