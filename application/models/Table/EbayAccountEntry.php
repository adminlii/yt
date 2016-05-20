<?php
class Table_EbayAccountEntry
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayAccountEntry();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayAccountEntry();
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
        
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["account_details_entry_type"]) && $condition["account_details_entry_type"] != ""){
            $select->where("account_details_entry_type = ?",$condition["account_details_entry_type"]);
        }
        if(isset($condition["description"]) && $condition["description"] != ""){
            $select->where("description = ?",$condition["description"]);
        }
        if(isset($condition["balance_currency"]) && $condition["balance_currency"] != ""){
            $select->where("balance_currency = ?",$condition["balance_currency"]);
        }
        if(isset($condition["balance"]) && $condition["balance"] != ""){
            $select->where("balance = ?",$condition["balance"]);
        }
        if(isset($condition["date"]) && $condition["date"] != ""){
            $select->where("date = ?",$condition["date"]);
        }
        if(isset($condition["gross_detail_amount_currency"]) && $condition["gross_detail_amount_currency"] != ""){
            $select->where("gross_detail_amount_currency = ?",$condition["gross_detail_amount_currency"]);
        }
        if(isset($condition["gross_detail_amount"]) && $condition["gross_detail_amount"] != ""){
            $select->where("gross_detail_amount = ?",$condition["gross_detail_amount"]);
        }
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["memo"]) && $condition["memo"] != ""){
            $select->where("memo = ?",$condition["memo"]);
        }
        if(isset($condition["conversion_rate_currency"]) && $condition["conversion_rate_currency"] != ""){
            $select->where("conversion_rate_currency = ?",$condition["conversion_rate_currency"]);
        }
        if(isset($condition["conversion_rate"]) && $condition["conversion_rate"] != ""){
            $select->where("conversion_rate = ?",$condition["conversion_rate"]);
        }
        if(isset($condition["net_detail_amount_currency"]) && $condition["net_detail_amount_currency"] != ""){
            $select->where("net_detail_amount_currency = ?",$condition["net_detail_amount_currency"]);
        }
        if(isset($condition["net_detail_amount"]) && $condition["net_detail_amount"] != ""){
            $select->where("net_detail_amount = ?",$condition["net_detail_amount"]);
        }
        if(isset($condition["ref_number"]) && $condition["ref_number"] != ""){
            $select->where("ref_number = ?",$condition["ref_number"]);
        }
        if(isset($condition["vat_percent"]) && $condition["vat_percent"] != ""){
            $select->where("vat_percent = ?",$condition["vat_percent"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["order_line_item_id"]) && $condition["order_line_item_id"] != ""){
            $select->where("order_line_item_id = ?",$condition["order_line_item_id"]);
        }
        if(isset($condition["transaction_id"]) && $condition["transaction_id"] != ""){
            $select->where("transaction_id = ?",$condition["transaction_id"]);
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