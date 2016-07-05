<?php
class Table_CsdOrderfba
{

    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsdOrderfba();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsdOrderfba();
    }

    /**
     *
     * @param
     *            $row
     * @return mixed
     */
    public function add($row)
    {
        return $this->_table->insert($row);
    }

    /**
     *
     * @param
     *            $row
     * @param
     *            $value
     * @param string $field            
     * @return mixed
     */
    public function update($row, $value, $field = "order_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     *
     * @param
     *            $value
     * @param string $field            
     * @return mixed
     */
    public function delete($value, $field = "order_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->delete($where);
    }

    /**
     *
     * @param
     *            $value
     * @param string $field            
     * @param string $colums            
     * @return mixed
     */
    public function getByField($value, $field = 'order_id', $colums = "*")
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
     *
     * @param array $condition            
     * @param string $type            
     * @param int $pageSize            
     * @param int $page            
     * @param string $orderBy            
     * @return array string
     */
    public function getByCondition1($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "", $groupBy = '')
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->where("1 =?", 1);
        /* CONDITION_START */
        
        if(isset($condition["order_create_code"]) && $condition["order_create_code"] != ""){
            $select->where("order_create_code = ?", $condition["order_create_code"]);
        }
        
        if(isset($condition["shipper_hawbcode_arr"]) && ! empty($condition["shipper_hawbcode_arr"]) && is_array($condition["shipper_hawbcode_arr"])){
            $select->where("shipper_hawbcode in (?) ", $condition["shipper_hawbcode_arr"]);
        }
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?", $condition["customer_id"]);
        }
        if(isset($condition["customer_channelid"]) && $condition["customer_channelid"] != ""){
            $select->where("customer_channelid = ?", $condition["customer_channelid"]);
        }
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?", $condition["product_code"]);
        }
        if(isset($condition["shipper_hawbcode"]) && $condition["shipper_hawbcode"] != ""){
            $select->where("shipper_hawbcode = ?", $condition["shipper_hawbcode"]);
        }
        if(isset($condition["server_hawbcode"]) && $condition["server_hawbcode"] != ""){
            $select->where("server_hawbcode = ?", $condition["server_hawbcode"]);
        }
        if(isset($condition["channel_hawbcode"]) && $condition["channel_hawbcode"] != ""){
            $select->where("channel_hawbcode = ?", $condition["channel_hawbcode"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?", $condition["country_code"]);
        }
        if(isset($condition["order_pieces"]) && $condition["order_pieces"] != ""){
            $select->where("order_pieces = ?", $condition["order_pieces"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?", $condition["order_status"]);
        }
        if(isset($condition["mail_cargo_type"]) && $condition["mail_cargo_type"] != ""){
            $select->where("mail_cargo_type = ?", $condition["mail_cargo_type"]);
        }
        if(isset($condition["document_change_sign"]) && $condition["document_change_sign"] != ""){
            $select->where("document_change_sign = ?", $condition["document_change_sign"]);
        }
        if(isset($condition["oda_checksign"]) && $condition["oda_checksign"] != ""){
            $select->where("oda_checksign = ?", $condition["oda_checksign"]);
        }
        if(isset($condition["oda_sign"]) && $condition["oda_sign"] != ""){
            $select->where("oda_sign = ?", $condition["oda_sign"]);
        }
        if(isset($condition["return_sign"]) && $condition["return_sign"] != ""){
            $select->where("return_sign = ?", $condition["return_sign"]);
        }
        if(isset($condition["hold_sign"]) && $condition["hold_sign"] != ""){
            $select->where("hold_sign = ?", $condition["hold_sign"]);
        }
        if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
            $select->where("buyer_id = ?", $condition["buyer_id"]);
        }
        if(isset($condition["platform_id"]) && $condition["platform_id"] != ""){
            $select->where("platform_id = ?", $condition["platform_id"]);
        }
        if(isset($condition["bs_id"]) && $condition["bs_id"] != ""){
            $select->where("bs_id = ?", $condition["bs_id"]);
        }
        if(isset($condition["creater_id"]) && $condition["creater_id"] != ""){
            $select->where("creater_id = ?", $condition["creater_id"]);
        }
        if(isset($condition["create_date"]) && $condition["create_date"] != ""){
            $select->where("create_date = ?", $condition["create_date"]);
        }
        if(isset($condition["modify_date"]) && $condition["modify_date"] != ""){
            $select->where("modify_date = ?", $condition["modify_date"]);
        }
        if(isset($condition["print_date"]) && $condition["print_date"] != ""){
            $select->where("print_date = ?", $condition["print_date"]);
        }
        if(isset($condition["post_date"]) && $condition["post_date"] != ""){
            $select->where("post_date = ?", $condition["post_date"]);
        }
        if(isset($condition["checkin_date"]) && $condition["checkin_date"] != ""){
            $select->where("checkin_date = ?", $condition["checkin_date"]);
        }
        if(isset($condition["checkout_date"]) && $condition["checkout_date"] != ""){
            $select->where("checkout_date = ?", $condition["checkout_date"]);
        }
        if(isset($condition["tms_id"]) && $condition["tms_id"] != ""){
            $select->where("tms_id = ?", $condition["tms_id"]);
        }
        // echo $select;exit;
        /* CONDITION_END */
        if('count(*)' == $type){
            return $this->_table->getAdapter()->fetchOne($select);
        }else{
            if(! empty($orderBy)){
                $select->order($orderBy);
            }
            if(! empty($groupBy)){
                $select->group($groupBy);
            }
            if($pageSize > 0 and $page > 0){
                $start = ($page - 1) * $pageSize;
                $select->limit($pageSize, $start);
            }
            $sql = $select->__toString();
            // echo $select;exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }

    /**
     *
     * @param array $condition            
     * @param string $type            
     * @param int $pageSize            
     * @param int $page            
     * @param string $orderBy            
     * @return array string
     */
    public function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "", $groupBy = '')
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        if($type == 'count(*)'){
            $select->joinInner('csd_shipperconsigneefba', $table . '.order_id = csd_shipperconsigneefba.order_id', null);
        }else{
            $select->joinInner('csd_shipperconsigneefba', $table . '.order_id = csd_shipperconsigneefba.order_id', '*');
        }
        
        $select->where("1 =?", 1);
        /* CONDITION_START */
        
        if(isset($condition["order_create_code"]) && $condition["order_create_code"] != ""){
            $select->where("order_create_code = ?", $condition["order_create_code"]);
        }
        
        if(isset($condition["shipper_hawbcode_arr"]) && ! empty($condition["shipper_hawbcode_arr"]) && is_array($condition["shipper_hawbcode_arr"])){
            $select->where("shipper_hawbcode in (?)", $condition["shipper_hawbcode_arr"]);
        }
        
        if(isset($condition["shipper_hawbcode_like"]) && $condition["shipper_hawbcode_like"] != ""){
            $select->where("shipper_hawbcode like ?", "%{$condition["shipper_hawbcode_like"]}%");
        }
        
        if(isset($condition["refer_hawbcode_arr"]) && ! empty($condition["refer_hawbcode_arr"]) && is_array($condition["refer_hawbcode_arr"])){
            $select->where("refer_hawbcode in (?) ", $condition["refer_hawbcode_arr"]);
        }
        
        if(isset($condition["refer_hawbcode_like"]) && $condition["refer_hawbcode_like"] != ""){
            $select->where("refer_hawbcode like ?", "%{$condition["refer_hawbcode_like"]}%");
        }
        
        if(isset($condition["server_hawbcode_arr"]) && ! empty($condition["server_hawbcode_arr"]) && is_array($condition["server_hawbcode_arr"])){
            $select->where("server_hawbcode in (?) ", $condition["server_hawbcode_arr"]);
        }
        
        if(isset($condition["server_hawbcode_like"]) && $condition["server_hawbcode_like"] != ""){
            $select->where("server_hawbcode like ?", "%{$condition["server_hawbcode_like"]}%");
        }
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?", $condition["customer_id"]);
        }
        if(isset($condition["customer_channelid"]) && $condition["customer_channelid"] != ""){
            $select->where("customer_channelid = ?", $condition["customer_channelid"]);
        }
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?", $condition["product_code"]);
        }
        if(isset($condition["refer_hawbcode"]) && $condition["refer_hawbcode"] != ""){
            $select->where("refer_hawbcode = ?", $condition["refer_hawbcode"]);
        }
        if(isset($condition["shipper_hawbcode"]) && $condition["shipper_hawbcode"] != ""){
            $select->where("shipper_hawbcode = ?", $condition["shipper_hawbcode"]);
        }
        if(isset($condition["server_hawbcode"]) && $condition["server_hawbcode"] != ""){
            $select->where("server_hawbcode = ?", $condition["server_hawbcode"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?", $condition["country_code"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?", $condition["order_status"]);
        }
        if(isset($condition["creater_id"]) && $condition["creater_id"] != ""){
            $select->where("creater_id = ?", $condition["creater_id"]);
        }
        if(isset($condition["create_date"]) && $condition["create_date"] != ""){
            $select->where("create_date = ?", $condition["create_date"]);
        }
        if(isset($condition["modify_date"]) && $condition["modify_date"] != ""){
            $select->where("modify_date = ?", $condition["modify_date"]);
        }
        
        if(isset($condition["consignee_name_like"]) && $condition["consignee_name_like"] != ""){
            $select->where("consignee_name like ?", "%" . $condition["consignee_name_like"] . "%");
        }
        
        if(isset($condition["create_date_to"]) && $condition["create_date_to"] != ""){
            $select->where("create_date <= ?", $condition["create_date_to"]);
        }
        
        if(isset($condition["create_date_from"]) && $condition["create_date_from"] != ""){
            $select->where("create_date >= ?", $condition["create_date_from"]);
        }
        if(isset($condition["order_status_unequals"]) && $condition["order_status_unequals"] != ""){
        	$select->where("order_status != ?", $condition["order_status_unequals"]);
        }
        if(isset($condition["order_id_in"]) && is_array($condition["order_id_in"]) && count($condition["order_id_in"]) > 0){
        	$select->where("csd_orderfba.order_id in (?)", $condition["order_id_in"]);
        }
        
         //echo $select;
         //exit();
        /* CONDITION_END */
        if('count(*)' == $type){
            return $this->_table->getAdapter()->fetchOne($select);
        }else{
            if(! empty($orderBy)){
                $select->order($orderBy);
            }
            if(! empty($groupBy)){
                $select->group($groupBy);
            }
            if($pageSize > 0 and $page > 0){
                $start = ($page - 1) * $pageSize;
                $select->limit($pageSize, $start);
            }
            $sql = $select->__toString();
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }

    /**
     *
     * @param array $condition            
     * @param string $type            
     * @param int $pageSize            
     * @param int $page            
     * @param string $orderBy            
     * @return array string
     */
    public function getByConditionJoinInvoice($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "", $groupBy = '')
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
		$select->joinInner('csd_shipperconsignee', $table . '.order_id = csd_shipperconsignee.order_id', null);
		$select->joinLeft('csd_invoice', $table . '.order_id = csd_invoice.order_id', null);
        
        $select->where("1 =?", 1);
        /* CONDITION_START */
        
        if(isset($condition["order_create_code"]) && $condition["order_create_code"] != ""){
            $select->where("order_create_code = ?", $condition["order_create_code"]);
        }
        
        if(isset($condition["shipper_hawbcode_arr"]) && ! empty($condition["shipper_hawbcode_arr"]) && is_array($condition["shipper_hawbcode_arr"])){
            $select->where("shipper_hawbcode in (?) or server_hawbcode in (?)", $condition["shipper_hawbcode_arr"]);
        }
        
        if(isset($condition["shipper_hawbcode_like"]) && $condition["shipper_hawbcode_like"] != ""){
            $select->where("shipper_hawbcode like ? or server_hawbcode like ?", "%{$condition["shipper_hawbcode_like"]}%");
        }
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?", $condition["customer_id"]);
        }
        if(isset($condition["customer_channelid"]) && $condition["customer_channelid"] != ""){
            $select->where("customer_channelid = ?", $condition["customer_channelid"]);
        }
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?", $condition["product_code"]);
        }
        if(isset($condition["shipper_hawbcode"]) && $condition["shipper_hawbcode"] != ""){
            $select->where("shipper_hawbcode = ?", $condition["shipper_hawbcode"]);
        }
        if(isset($condition["server_hawbcode"]) && $condition["server_hawbcode"] != ""){
            $select->where("server_hawbcode = ?", $condition["server_hawbcode"]);
        }
        if(isset($condition["channel_hawbcode"]) && $condition["channel_hawbcode"] != ""){
            $select->where("channel_hawbcode = ?", $condition["channel_hawbcode"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?", $condition["country_code"]);
        }
        if(isset($condition["order_pieces"]) && $condition["order_pieces"] != ""){
            $select->where("order_pieces = ?", $condition["order_pieces"]);
        }
        if(isset($condition["order_status"]) && $condition["order_status"] != ""){
            $select->where("order_status = ?", $condition["order_status"]);
        }
        if(isset($condition["mail_cargo_type"]) && $condition["mail_cargo_type"] != ""){
            $select->where("mail_cargo_type = ?", $condition["mail_cargo_type"]);
        }
        if(isset($condition["document_change_sign"]) && $condition["document_change_sign"] != ""){
            $select->where("document_change_sign = ?", $condition["document_change_sign"]);
        }
        if(isset($condition["oda_checksign"]) && $condition["oda_checksign"] != ""){
            $select->where("oda_checksign = ?", $condition["oda_checksign"]);
        }
        if(isset($condition["oda_sign"]) && $condition["oda_sign"] != ""){
            $select->where("oda_sign = ?", $condition["oda_sign"]);
        }
        if(isset($condition["return_sign"]) && $condition["return_sign"] != ""){
            $select->where("return_sign = ?", $condition["return_sign"]);
        }
        if(isset($condition["hold_sign"]) && $condition["hold_sign"] != ""){
            $select->where("hold_sign = ?", $condition["hold_sign"]);
        }
        if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
            $select->where("buyer_id = ?", $condition["buyer_id"]);
        }
        if(isset($condition["platform_id"]) && $condition["platform_id"] != ""){
            $select->where("platform_id = ?", $condition["platform_id"]);
        }
        if(isset($condition["bs_id"]) && $condition["bs_id"] != ""){
            $select->where("bs_id = ?", $condition["bs_id"]);
        }
        if(isset($condition["creater_id"]) && $condition["creater_id"] != ""){
            $select->where("creater_id = ?", $condition["creater_id"]);
        }
        if(isset($condition["create_date"]) && $condition["create_date"] != ""){
            $select->where("create_date = ?", $condition["create_date"]);
        }
        if(isset($condition["modify_date"]) && $condition["modify_date"] != ""){
            $select->where("modify_date = ?", $condition["modify_date"]);
        }
        if(isset($condition["print_date"]) && $condition["print_date"] != ""){
            $select->where("print_date = ?", $condition["print_date"]);
        }
        if(isset($condition["post_date"]) && $condition["post_date"] != ""){
            $select->where("post_date = ?", $condition["post_date"]);
        }
        if(isset($condition["checkin_date"]) && $condition["checkin_date"] != ""){
            $select->where("checkin_date = ?", $condition["checkin_date"]);
        }
        if(isset($condition["checkout_date"]) && $condition["checkout_date"] != ""){
            $select->where("checkout_date = ?", $condition["checkout_date"]);
        }
        if(isset($condition["tms_id"]) && $condition["tms_id"] != ""){
            $select->where("tms_id = ?", $condition["tms_id"]);
        }
        
        if(isset($condition["consignee_name_like"]) && $condition["consignee_name_like"] != ""){
            $select->where("consignee_name like ?", "%" . $condition["consignee_name_like"] . "%");
        }
        
        if(isset($condition["create_date_to"]) && $condition["create_date_to"] != ""){
            $select->where("create_date <= ?", $condition["create_date_to"]);
        }
        
        if(isset($condition["create_date_from"]) && $condition["create_date_from"] != ""){
            $select->where("create_date >= ?", $condition["create_date_from"]);
        }
        if(isset($condition["order_status_unequals"]) && $condition["order_status_unequals"] != ""){
        	$select->where("order_status != ?", $condition["order_status_unequals"]);
        }
        if(isset($condition["order_id_in"]) && is_array($condition["order_id_in"]) && count($condition["order_id_in"]) > 0){
        	$select->where("csd_order.order_id in (?)", $condition["order_id_in"]);
        }
        
//         echo $select;
//         exit();
        /* CONDITION_END */
        if('count(*)' == $type){
            return $this->_table->getAdapter()->fetchOne($select);
        }else{
            if(! empty($orderBy)){
                $select->order($orderBy);
            }
            if(! empty($groupBy)){
                $select->group($groupBy);
            }
            if($pageSize > 0 and $page > 0){
                $start = ($page - 1) * $pageSize;
                $select->limit($pageSize, $start);
            }
            $sql = $select->__toString();
            // echo $select;exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}