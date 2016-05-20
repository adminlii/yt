<?php
class Table_PaypalTransactionDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PaypalTransactionDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PaypalTransactionDetail();
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
    public function update($row, $value, $field = "transactionid")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "transactionid")
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
    public function getByField($value, $field = 'transactionid', $colums = "*")
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
        
        if(isset($condition["receiverbusiness"]) && $condition["receiverbusiness"] != ""){
            $select->where("receiverbusiness = ?",$condition["receiverbusiness"]);
        }
        if(isset($condition["receiveremail"]) && $condition["receiveremail"] != ""){
            $select->where("receiveremail = ?",$condition["receiveremail"]);
        }
        if(isset($condition["receiverid"]) && $condition["receiverid"] != ""){
            $select->where("receiverid = ?",$condition["receiverid"]);
        }
        if(isset($condition["email"]) && $condition["email"] != ""){
            $select->where("email = ?",$condition["email"]);
        }
        if(isset($condition["payerid"]) && $condition["payerid"] != ""){
            $select->where("payerid = ?",$condition["payerid"]);
        }
        if(isset($condition["payerstatus"]) && $condition["payerstatus"] != ""){
            $select->where("payerstatus = ?",$condition["payerstatus"]);
        }
        if(isset($condition["countrycode"]) && $condition["countrycode"] != ""){
            $select->where("countrycode = ?",$condition["countrycode"]);
        }
        if(isset($condition["shiptoname"]) && $condition["shiptoname"] != ""){
            $select->where("shiptoname = ?",$condition["shiptoname"]);
        }
        if(isset($condition["shiptostreet"]) && $condition["shiptostreet"] != ""){
            $select->where("shiptostreet = ?",$condition["shiptostreet"]);
        }
        if(isset($condition["shiptocity"]) && $condition["shiptocity"] != ""){
            $select->where("shiptocity = ?",$condition["shiptocity"]);
        }
        if(isset($condition["shiptostate"]) && $condition["shiptostate"] != ""){
            $select->where("shiptostate = ?",$condition["shiptostate"]);
        }
        if(isset($condition["shiptocountrycode"]) && $condition["shiptocountrycode"] != ""){
            $select->where("shiptocountrycode = ?",$condition["shiptocountrycode"]);
        }
        if(isset($condition["shiptocountryname"]) && $condition["shiptocountryname"] != ""){
            $select->where("shiptocountryname = ?",$condition["shiptocountryname"]);
        }
        if(isset($condition["shiptozip"]) && $condition["shiptozip"] != ""){
            $select->where("shiptozip = ?",$condition["shiptozip"]);
        }
        if(isset($condition["addressowner"]) && $condition["addressowner"] != ""){
            $select->where("addressowner = ?",$condition["addressowner"]);
        }
        if(isset($condition["addressstatus"]) && $condition["addressstatus"] != ""){
            $select->where("addressstatus = ?",$condition["addressstatus"]);
        }
        if(isset($condition["custom"]) && $condition["custom"] != ""){
            $select->where("custom = ?",$condition["custom"]);
        }
        if(isset($condition["salestax"]) && $condition["salestax"] != ""){
            $select->where("salestax = ?",$condition["salestax"]);
        }
        if(isset($condition["shipdiscount"]) && $condition["shipdiscount"] != ""){
            $select->where("shipdiscount = ?",$condition["shipdiscount"]);
        }
        if(isset($condition["insuranceamount"]) && $condition["insuranceamount"] != ""){
            $select->where("insuranceamount = ?",$condition["insuranceamount"]);
        }
        if(isset($condition["buyerid"]) && $condition["buyerid"] != ""){
            $select->where("buyerid = ?",$condition["buyerid"]);
        }
        if(isset($condition["closingdate"]) && $condition["closingdate"] != ""){
            $select->where("closingdate = ?",$condition["closingdate"]);
        }
        if(isset($condition["timestamp"]) && $condition["timestamp"] != ""){
            $select->where("timestamp = ?",$condition["timestamp"]);
        }
        if(isset($condition["correlationid"]) && $condition["correlationid"] != ""){
            $select->where("correlationid = ?",$condition["correlationid"]);
        }
        if(isset($condition["ack"]) && $condition["ack"] != ""){
            $select->where("ack = ?",$condition["ack"]);
        }
        if(isset($condition["version"]) && $condition["version"] != ""){
            $select->where("version = ?",$condition["version"]);
        }
        if(isset($condition["build"]) && $condition["build"] != ""){
            $select->where("build = ?",$condition["build"]);
        }
        if(isset($condition["firstname"]) && $condition["firstname"] != ""){
            $select->where("firstname = ?",$condition["firstname"]);
        }
        if(isset($condition["lastname"]) && $condition["lastname"] != ""){
            $select->where("lastname = ?",$condition["lastname"]);
        }
        if(isset($condition["transactiontype"]) && $condition["transactiontype"] != ""){
            $select->where("transactiontype = ?",$condition["transactiontype"]);
        }
        if(isset($condition["paymenttype"]) && $condition["paymenttype"] != ""){
            $select->where("paymenttype = ?",$condition["paymenttype"]);
        }
        if(isset($condition["ordertime"]) && $condition["ordertime"] != ""){
            $select->where("ordertime = ?",$condition["ordertime"]);
        }
        if(isset($condition["amt"]) && $condition["amt"] != ""){
            $select->where("amt = ?",$condition["amt"]);
        }
        if(isset($condition["feeamt"]) && $condition["feeamt"] != ""){
            $select->where("feeamt = ?",$condition["feeamt"]);
        }
        if(isset($condition["taxamt"]) && $condition["taxamt"] != ""){
            $select->where("taxamt = ?",$condition["taxamt"]);
        }
        if(isset($condition["shippingamt"]) && $condition["shippingamt"] != ""){
            $select->where("shippingamt = ?",$condition["shippingamt"]);
        }
        if(isset($condition["handlingamt"]) && $condition["handlingamt"] != ""){
            $select->where("handlingamt = ?",$condition["handlingamt"]);
        }
        if(isset($condition["currencycode"]) && $condition["currencycode"] != ""){
            $select->where("currencycode = ?",$condition["currencycode"]);
        }
        if(isset($condition["paymentstatus"]) && $condition["paymentstatus"] != ""){
            $select->where("paymentstatus = ?",$condition["paymentstatus"]);
        }
        if(isset($condition["pendingreason"]) && $condition["pendingreason"] != ""){
            $select->where("pendingreason = ?",$condition["pendingreason"]);
        }
        if(isset($condition["reasoncode"]) && $condition["reasoncode"] != ""){
            $select->where("reasoncode = ?",$condition["reasoncode"]);
        }
        if(isset($condition["shippingmethod"]) && $condition["shippingmethod"] != ""){
            $select->where("shippingmethod = ?",$condition["shippingmethod"]);
        }
        if(isset($condition["protectioneligibility"]) && $condition["protectioneligibility"] != ""){
            $select->where("protectioneligibility = ?",$condition["protectioneligibility"]);
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