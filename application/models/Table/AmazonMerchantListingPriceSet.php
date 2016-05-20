<?php
class Table_AmazonMerchantListingPriceSet
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AmazonMerchantListingPriceSet();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AmazonMerchantListingPriceSet();
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
    public function update($row, $value, $field = "id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "id")
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
    public function getByField($value, $field = 'id', $colums = "*")
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
        
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["listing_id"]) && $condition["listing_id"] != ""){
            $select->where("listing_id = ?",$condition["listing_id"]);
        }
        if(isset($condition["seller_sku"]) && $condition["seller_sku"] != ""){
            $select->where("seller_sku = ?",$condition["seller_sku"]);
        }
        if(isset($condition["regular_price"]) && $condition["regular_price"] != ""){
            $select->where("regular_price = ?",$condition["regular_price"]);
        }
        if(isset($condition["regular_price_currency"]) && $condition["regular_price_currency"] != ""){
            $select->where("regular_price_currency = ?",$condition["regular_price_currency"]);
        }
        if(isset($condition["listing_price"]) && $condition["listing_price"] != ""){
            $select->where("listing_price = ?",$condition["listing_price"]);
        }
        if(isset($condition["listing_price_currency"]) && $condition["listing_price_currency"] != ""){
            $select->where("listing_price_currency = ?",$condition["listing_price_currency"]);
        }
        if(isset($condition["start_date"]) && $condition["start_date"] != ""){
            $select->where("start_date = ?",$condition["start_date"]);
        }
        if(isset($condition["end_date"]) && $condition["end_date"] != ""){
            $select->where("end_date = ?",$condition["end_date"]);
        }
        if(isset($condition["sync_status"]) && $condition["sync_status"] != ""){
            $select->where("sync_status = ?",$condition["sync_status"]);
        }
        if(isset($condition["sync_time"]) && $condition["sync_time"] != ""){
            $select->where("sync_time = ?",$condition["sync_time"]);
        }
        if(isset($condition["add_time"]) && $condition["add_time"] != ""){
            $select->where("add_time = ?",$condition["add_time"]);
        }
        if(isset($condition["update_time"]) && $condition["update_time"] != ""){
            $select->where("update_time = ?",$condition["update_time"]);
        }
        if(isset($condition["op_user_id"]) && $condition["op_user_id"] != ""){
            $select->where("op_user_id = ?",$condition["op_user_id"]);
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