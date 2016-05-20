<?php
class Table_EbayCustomerFeedback
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayCustomerFeedback();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayCustomerFeedback();
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
    public function update($row, $value, $field = "ecf_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ecf_id")
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
    public function getByField($value, $field = 'ecf_id', $colums = "*")
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
        
        if(isset($condition["ecf_feedback_id"]) && $condition["ecf_feedback_id"] != ""){
            $select->where("ecf_feedback_id = ?",$condition["ecf_feedback_id"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
        	$select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["ecf_commenting_user"]) && $condition["ecf_commenting_user"] != ""){
            $select->where("ecf_commenting_user = ?",$condition["ecf_commenting_user"]);
        }
        if(isset($condition["ecf_commenting_user_score"]) && $condition["ecf_commenting_user_score"] != ""){
            $select->where("ecf_commenting_user_score = ?",$condition["ecf_commenting_user_score"]);
        }
        if(isset($condition["ecf_comment_text"]) && $condition["ecf_comment_text"] != ""){
            $select->where("ecf_comment_text = ?",$condition["ecf_comment_text"]);
        }
        if(isset($condition["ecf_comment_type"]) && $condition["ecf_comment_type"] != ""){
            $select->where("ecf_comment_type = ?",$condition["ecf_comment_type"]);
        }
        if(isset($condition["ecf_role"]) && $condition["ecf_role"] != ""){
            $select->where("ecf_role = ?",$condition["ecf_role"]);
        }
        if(isset($condition["ecf_ebay_account_arr"]) && $condition["ecf_ebay_account_arr"] != ""){
        	$select->where("ecf_ebay_account in (?)",$condition["ecf_ebay_account_arr"]);
        }
        if(isset($condition["ecf_transaction_id"]) && $condition["ecf_transaction_id"] != ""){
            $select->where("ecf_transaction_id = ?",$condition["ecf_transaction_id"]);
        }
        if(isset($condition["ecf_order_line_item_id"]) && $condition["ecf_order_line_item_id"] != ""){
            $select->where("ecf_order_line_item_id = ?",$condition["ecf_order_line_item_id"]);
        }
        if(isset($condition["ecf_item_id"]) && $condition["ecf_item_id"] != ""){
            $select->where("ecf_item_id = ?",$condition["ecf_item_id"]);
        }
        if(isset($condition["ecf_item_title"]) && $condition["ecf_item_title"] != ""){
            $select->where("ecf_item_title = ?",$condition["ecf_item_title"]);
        }
        if(isset($condition["ecf_item_price"]) && $condition["ecf_item_price"] != ""){
            $select->where("ecf_item_price = ?",$condition["ecf_item_price"]);
        }
        if(isset($condition["ecf_currency_id"]) && $condition["ecf_currency_id"] != ""){
            $select->where("ecf_currency_id = ?",$condition["ecf_currency_id"]);
        }
        if(isset($condition["ecf_order_id"]) && $condition["ecf_order_id"] != ""){
            $select->where("ecf_order_id = ?",$condition["ecf_order_id"]);
        }
        if(isset($condition["ecf_message_id"]) && $condition["ecf_message_id"] != ""){
            $select->where("ecf_message_id = ?",$condition["ecf_message_id"]);
        }
        if(isset($condition["ecf_proecss_status"]) && $condition["ecf_proecss_status"] != ""){
            $select->where("ecf_proecss_status = ?",$condition["ecf_proecss_status"]);
        }
        if(isset($condition["ecf_feedback_revised"]) && $condition["ecf_feedback_revised"] != ""){
        	$select->where("ecf_feedback_revised = ?",$condition["ecf_feedback_revised"]);
        }
        if(isset($condition["commentDateFrom"]) && $condition["commentDateFrom"] != ""){
        	$select->where("ecf_comment_time > ?",$condition["commentDateFrom"]);
        }
        if(isset($condition["commentDateEnd"]) && $condition["commentDateEnd"] != ""){
        	$select->where("ecf_comment_time < ?",$condition["commentDateEnd"]);
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