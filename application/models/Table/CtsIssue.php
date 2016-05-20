<?php
class Table_CtsIssue
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CtsIssue();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CtsIssue();
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
    public function update($row, $value, $field = "issue_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "issue_id")
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
    public function getByField($value, $field = 'issue_id', $colums = "*")
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
        
        if(isset($condition["tms_id"]) && $condition["tms_id"] != ""){
            $select->where("tms_id = ?",$condition["tms_id"]);
        }
        if(isset($condition["issue_class_code"]) && $condition["issue_class_code"] != ""){
            $select->where("issue_class_code = ?",$condition["issue_class_code"]);
        }
        if(isset($condition["issue_kind_code"]) && $condition["issue_kind_code"] != ""){
            $select->where("issue_kind_code = ?",$condition["issue_kind_code"]);
        }
        if(isset($condition["issue_status"]) && $condition["issue_status"] != ""){
            $select->where("issue_status = ?",$condition["issue_status"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["shipper_channel_id"]) && $condition["shipper_channel_id"] != ""){
            $select->where("shipper_channel_id = ?",$condition["shipper_channel_id"]);
        }
        if(isset($condition["bs_id"]) && $condition["bs_id"] != ""){
            $select->where("bs_id = ?",$condition["bs_id"]);
        }
        if(isset($condition["shipper_hawbcode"]) && $condition["shipper_hawbcode"] != ""){
            $select->where("shipper_hawbcode = ?",$condition["shipper_hawbcode"]);
        }
        if(isset($condition["server_hawbcode"]) && $condition["server_hawbcode"] != ""){
            $select->where("server_hawbcode = ?",$condition["server_hawbcode"]);
        }
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?",$condition["product_code"]);
        }
        if(isset($condition["isu_lastprocessdate"]) && $condition["isu_lastprocessdate"] != ""){
            $select->where("isu_lastprocessdate = ?",$condition["isu_lastprocessdate"]);
        }
        if(isset($condition["st_id_process"]) && $condition["st_id_process"] != ""){
            $select->where("st_id_process = ?",$condition["st_id_process"]);
        }
        if(isset($condition["isu_lastfeedbackdate"]) && $condition["isu_lastfeedbackdate"] != ""){
            $select->where("isu_lastfeedbackdate = ?",$condition["isu_lastfeedbackdate"]);
        }
        if(isset($condition["st_id_unholdassigned"]) && $condition["st_id_unholdassigned"] != ""){
            $select->where("st_id_unholdassigned = ?",$condition["st_id_unholdassigned"]);
        }
        if(isset($condition["isu_unholddate"]) && $condition["isu_unholddate"] != ""){
            $select->where("isu_unholddate = ?",$condition["isu_unholddate"]);
        }
        if(isset($condition["st_id_unhold"]) && $condition["st_id_unhold"] != ""){
            $select->where("st_id_unhold = ?",$condition["st_id_unhold"]);
        }
        if(isset($condition["isu_releasedate"]) && $condition["isu_releasedate"] != ""){
            $select->where("isu_releasedate = ?",$condition["isu_releasedate"]);
        }
        if(isset($condition["st_id_release"]) && $condition["st_id_release"] != ""){
            $select->where("st_id_release = ?",$condition["st_id_release"]);
        }
        if(isset($condition["isu_webreplysign"]) && $condition["isu_webreplysign"] != ""){
            $select->where("isu_webreplysign = ?",$condition["isu_webreplysign"]);
        }
        if(isset($condition["isu_interactionsign"]) && $condition["isu_interactionsign"] != ""){
            $select->where("isu_interactionsign = ?",$condition["isu_interactionsign"]);
        }
        if(isset($condition["isu_holdsign"]) && $condition["isu_holdsign"] != ""){
            $select->where("isu_holdsign = ?",$condition["isu_holdsign"]);
        }
        if(isset($condition["st_id_create"]) && $condition["st_id_create"] != ""){
            $select->where("st_id_create = ?",$condition["st_id_create"]);
        }
        if(isset($condition["isu_createdate"]) && $condition["isu_createdate"] != ""){
            $select->where("isu_createdate = ?",$condition["isu_createdate"]);
        }
        if(isset($condition["isu_closedate"]) && $condition["isu_closedate"] != ""){
            $select->where("isu_closedate = ?",$condition["isu_closedate"]);
        }
        if(isset($condition["st_id_close"]) && $condition["st_id_close"] != ""){
            $select->where("st_id_close = ?",$condition["st_id_close"]);
        }
        if(isset($condition["checkin_og_id"]) && $condition["checkin_og_id"] != ""){
            $select->where("checkin_og_id = ?",$condition["checkin_og_id"]);
        }
        if(isset($condition["checkin_date"]) && $condition["checkin_date"] != ""){
            $select->where("checkin_date = ?",$condition["checkin_date"]);
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
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByJoinCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft("cts_issuestatus", "cts_issuestatus.issue_status = cts_issue.issue_status",array("issue_status_cnname"));
        $select->joinLeft("cts_customer_issuekind", "cts_customer_issuekind.issuekind_code = cts_issue.issue_kind_code",array("issuekind_cnname"));
        $select->joinLeft("csi_productkind", "csi_productkind.product_code = cts_issue.product_code",array("product_cnname"));
        $select->joinLeft("cts_issue_response", "cts_issue_response.issue_id = cts_issue.issue_id",array());
        $select->joinLeft("hmr_staff", "hmr_staff.st_id = cts_issue.st_id_process",array("st_name"));
        
        /*CONDITION_START*/
        
        if(isset($condition["isu_interactionsign"]) && $condition["isu_interactionsign"] != "" && $condition["issue_status"] == 'N'){
        	$select->where("EXISTS (
										SELECT
											1
										FROM
											cts_issue_response b
										WHERE
											b.issue_id = cts_issue.issue_id
										AND b.refer_sign = ?
									)", "N");
        }else{
        	$select->where("1 =?", 1);
        }
        
        if(isset($condition["issue_kind_code"]) && $condition["issue_kind_code"] != ""){
            $select->where("issue_kind_code = ?",$condition["issue_kind_code"]);
        }
        if(isset($condition["issue_status"]) && $condition["issue_status"] != ""){
            $select->where("cts_issue.issue_status = ?",$condition["issue_status"]);
        }
        if(isset($condition["shipper_hawbcode"]) && $condition["shipper_hawbcode"] != ""){
            $select->where("shipper_hawbcode = ?",$condition["shipper_hawbcode"]);
        }
        if(isset($condition["server_hawbcode"]) && $condition["server_hawbcode"] != ""){
            $select->where("server_hawbcode = ?",$condition["server_hawbcode"]);
        }
        if(isset($condition["code"]) && $condition["code"] != ""){
        	$select->where("shipper_hawbcode like ?",'%'.$condition["code"].'%');
        	$select->orWhere("server_hawbcode like ?",'%'.$condition["code"].'%');
        }
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?",$condition["product_code"]);
        }
        if(isset($condition["isu_createdate"]) && $condition["isu_createdate"] != ""){
            $select->where("isu_createdate = ?",$condition["isu_createdate"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["shipper_channel_id"]) && $condition["shipper_channel_id"] != ""){
            $select->where("shipper_channel_id = ?",$condition["shipper_channel_id"]);
        }
        if(isset($condition["isu_interactionsign"]) && $condition["isu_interactionsign"] != ""){
            $select->where("cts_customer_issuekind.isu_interactionsign = ?",$condition["isu_interactionsign"]);
        }
        
        
        $select->group("cts_issue.issue_id");
        
        /*CONDITION_END*/
        if ('count(*)' == $type) {
        	$sql_temp = "select count(*) from (".$select->__toString().") as cc";
            return $this->_table->getAdapter()->fetchOne($sql_temp);
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