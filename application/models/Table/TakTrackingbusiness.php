<?php
class Table_TakTrackingbusiness
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_TakTrackingbusiness();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_TakTrackingbusiness();
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
    public function update($row, $value, $field = "tbs_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "tbs_id")
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
    public function getByField($value, $field = 'tbs_id', $colums = "*")
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
        
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["track_server_code"]) && $condition["track_server_code"] != ""){
            $select->where("track_server_code = ?",$condition["track_server_code"]);
        }
        if(isset($condition["shipper_hawbcode"]) && $condition["shipper_hawbcode"] != ""){
            $select->where("shipper_hawbcode = ?",$condition["shipper_hawbcode"]);
        }
        if(isset($condition["server_hawbcode"]) && $condition["server_hawbcode"] != ""){
            $select->where("server_hawbcode = ?",$condition["server_hawbcode"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?",$condition["country_code"]);
        }
        if(isset($condition["new_operation_status"]) && $condition["new_operation_status"] != ""){
            $select->where("new_operation_status = ?",$condition["new_operation_status"]);
        }
        if(isset($condition["new_error_code"]) && $condition["new_error_code"] != ""){
            $select->where("new_error_code = ?",$condition["new_error_code"]);
        }
        if(isset($condition["new_operation_date"]) && $condition["new_operation_date"] != ""){
            $select->where("new_operation_date = ?",$condition["new_operation_date"]);
        }
        if(isset($condition["new_track_code"]) && $condition["new_track_code"] != ""){
            $select->where("new_track_code = ?",$condition["new_track_code"]);
        }
        if(isset($condition["new_track_date"]) && $condition["new_track_date"] != ""){
            $select->where("new_track_date = ?",$condition["new_track_date"]);
        }
        if(isset($condition["new_track_location"]) && $condition["new_track_location"] != ""){
            $select->where("new_track_location = ?",$condition["new_track_location"]);
        }
        if(isset($condition["new_track_comment"]) && $condition["new_track_comment"] != ""){
            $select->where("new_track_comment = ?",$condition["new_track_comment"]);
        }
        if(isset($condition["close_code"]) && $condition["close_code"] != ""){
            $select->where("close_code = ?",$condition["close_code"]);
        }
        if(isset($condition["hash_code"]) && $condition["hash_code"] != ""){
            $select->where("hash_code = ?",$condition["hash_code"]);
        }
        if(isset($condition["close_date"]) && $condition["close_date"] != ""){
            $select->where("close_date = ?",$condition["close_date"]);
        }
        if(isset($condition["signatory_name"]) && $condition["signatory_name"] != ""){
            $select->where("signatory_name = ?",$condition["signatory_name"]);
        }
        if(isset($condition["start_track_date"]) && $condition["start_track_date"] != ""){
            $select->where("start_track_date = ?",$condition["start_track_date"]);
        }
        if(isset($condition["end_track_date"]) && $condition["end_track_date"] != ""){
            $select->where("end_track_date = ?",$condition["end_track_date"]);
        }
        if(isset($condition["reference_date"]) && $condition["reference_date"] != ""){
            $select->where("reference_date = ?",$condition["reference_date"]);
        }
        if(isset($condition["create_date"]) && $condition["create_date"] != ""){
            $select->where("create_date = ?",$condition["create_date"]);
        }
        if(isset($condition["pass_back_date"]) && $condition["pass_back_date"] != ""){
            $select->where("pass_back_date = ?",$condition["pass_back_date"]);
        }
        if(isset($condition["shipper_hawbcode_tracksign"]) && $condition["shipper_hawbcode_tracksign"] != ""){
            $select->where("shipper_hawbcode_tracksign = ?",$condition["shipper_hawbcode_tracksign"]);
        }
        if(isset($condition["web_order_id"]) && $condition["web_order_id"] != ""){
            $select->where("web_order_id = ?",$condition["web_order_id"]);
        }
        if(isset($condition["sys_bs_id"]) && $condition["sys_bs_id"] != ""){
            $select->where("sys_bs_id = ?",$condition["sys_bs_id"]);
        }
        if(isset($condition["show_sign"]) && $condition["show_sign"] != ""){
            $select->where("show_sign = ?",$condition["show_sign"]);
        }
        if(isset($condition["tms_id"]) && $condition["tms_id"] != ""){
            $select->where("tms_id = ?",$condition["tms_id"]);
        }
        if(isset($condition["code"]) && $condition["code"] != ""){
        	$tmp_code = $condition["code"];
            $select->where("shipper_hawbcode = '$tmp_code' or server_hawbcode = '$tmp_code'");
        }
        
//         echo $select->__toString();exit;
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