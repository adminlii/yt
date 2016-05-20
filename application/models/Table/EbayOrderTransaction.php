<?php
class Table_EbayOrderTransaction
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EbayOrderTransaction();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EbayOrderTransaction();
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
    public function update($row, $value, $field = "EbayTransaction_Id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "EbayTransaction_Id")
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
    public function getByField($value, $field = 'EbayTransaction_Id', $colums = "*")
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

        if(isset($condition["OrderId"]) && $condition["OrderId"] != ""){
            $select->where("OrderId = ?",$condition["OrderId"]);
        }
        if(isset($condition["OrderId_arr"]) && !empty($condition["OrderId_arr"])){
            $select->where("OrderId in (?)",$condition["OrderId_arr"]);
        }
        
        if(isset($condition["Buyer_Mail"]) && $condition["Buyer_Mail"] != ""){
            $select->where("Buyer_Mail = ?",$condition["Buyer_Mail"]);
        }
        if(isset($condition["SellingManagerSalesRecordNumber"]) && $condition["SellingManagerSalesRecordNumber"] != ""){
            $select->where("SellingManagerSalesRecordNumber = ?",$condition["SellingManagerSalesRecordNumber"]);
        }
        if(isset($condition["shippingcarrierused"]) && $condition["shippingcarrierused"] != ""){
            $select->where("shippingcarrierused = ?",$condition["shippingcarrierused"]);
        }
        if(isset($condition["shipmenttrackingnumber"]) && $condition["shipmenttrackingnumber"] != ""){
            $select->where("shipmenttrackingnumber = ?",$condition["shipmenttrackingnumber"]);
        }
        if(isset($condition["ItemID"]) && $condition["ItemID"] != ""){
            $select->where("ItemID = ?",$condition["ItemID"]);
        }
        if(isset($condition["Site"]) && $condition["Site"] != ""){
            $select->where("Site = ?",$condition["Site"]);
        }
        if(isset($condition["Title"]) && $condition["Title"] != ""){
            $select->where("Title = ?",$condition["Title"]);
        }
        if(isset($condition["ConditionID"]) && $condition["ConditionID"] != ""){
            $select->where("ConditionID = ?",$condition["ConditionID"]);
        }
        if(isset($condition["ConditionDisplayName"]) && $condition["ConditionDisplayName"] != ""){
            $select->where("ConditionDisplayName = ?",$condition["ConditionDisplayName"]);
        }
        if(isset($condition["QuantityPurchased"]) && $condition["QuantityPurchased"] != ""){
            $select->where("QuantityPurchased = ?",$condition["QuantityPurchased"]);
        }
        if(isset($condition["PaymentHoldStatus"]) && $condition["PaymentHoldStatus"] != ""){
            $select->where("PaymentHoldStatus = ?",$condition["PaymentHoldStatus"]);
        }
        if(isset($condition["TransactionID"]) && $condition["TransactionID"] != ""){
            $select->where("TransactionID = ?",$condition["TransactionID"]);
        }
        if(isset($condition["TransactionPrice"]) && $condition["TransactionPrice"] != ""){
            $select->where("TransactionPrice = ?",$condition["TransactionPrice"]);
        }
        if(isset($condition["finalvaluefee"]) && $condition["finalvaluefee"] != ""){
            $select->where("finalvaluefee = ?",$condition["finalvaluefee"]);
        }
        if(isset($condition["TransactionSiteID"]) && $condition["TransactionSiteID"] != ""){
            $select->where("TransactionSiteID = ?",$condition["TransactionSiteID"]);
        }
        if(isset($condition["Platform"]) && $condition["Platform"] != ""){
            $select->where("Platform = ?",$condition["Platform"]);
        }
        if(isset($condition["actualshippingcost"]) && $condition["actualshippingcost"] != ""){
            $select->where("actualshippingcost = ?",$condition["actualshippingcost"]);
        }
        if(isset($condition["actualhandlingcost"]) && $condition["actualhandlingcost"] != ""){
            $select->where("actualhandlingcost = ?",$condition["actualhandlingcost"]);
        }
        if(isset($condition["OrderLineItemID"]) && $condition["OrderLineItemID"] != ""){
            $select->where("OrderLineItemID = ?",$condition["OrderLineItemID"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
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