<?php
class Table_CsiConsigneeTrailerAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsiConsigneeTrailerAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsiConsigneeTrailerAddress();
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
    public function update($row, $value, $field = "consignee_account")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "consignee_account")
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
    public function getByField($value, $field = 'consignee_account', $colums = "*")
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
        
        if(isset($condition["not_consignee_account"]) && $condition["not_consignee_account"] != ""){
        	$select->where("consignee_account != ?",$condition["not_consignee_account"]);
        }
        if(isset($condition["customer_id"]) && $condition["customer_id"] != ""){
            $select->where("customer_id = ?",$condition["customer_id"]);
        }
        if(isset($condition["customer_channelid"]) && $condition["customer_channelid"] != ""){
            $select->where("customer_channelid = ?",$condition["customer_channelid"]);
        }
        if(isset($condition["consignee_name"]) && $condition["consignee_name"] != ""){
            $select->where("consignee_name = ?",$condition["consignee_name"]);
        }
        if(isset($condition["consignee_company"]) && $condition["consignee_company"] != ""){
            $select->where("consignee_company = ?",$condition["consignee_company"]);
        }
        if(isset($condition["consignee_countrycode"]) && $condition["consignee_countrycode"] != ""){
            $select->where("consignee_countrycode = ?",$condition["consignee_countrycode"]);
        }
        if(isset($condition["consignee_province"]) && $condition["consignee_province"] != ""){
            $select->where("consignee_province = ?",$condition["consignee_province"]);
        }
        if(isset($condition["consignee_city"]) && $condition["consignee_city"] != ""){
            $select->where("consignee_city = ?",$condition["consignee_city"]);
        }
        if(isset($condition["consignee_street"]) && $condition["consignee_street"] != ""){
            $select->where("consignee_street = ?",$condition["consignee_street"]);
        }
        if(isset($condition["consignee_postcode"]) && $condition["consignee_postcode"] != ""){
            $select->where("consignee_postcode = ?",$condition["consignee_postcode"]);
        }
        if(isset($condition["consignee_areacode"]) && $condition["consignee_areacode"] != ""){
            $select->where("consignee_areacode = ?",$condition["consignee_areacode"]);
        }
        if(isset($condition["consignee_telephone"]) && $condition["consignee_telephone"] != ""){
            $select->where("consignee_telephone = ?",$condition["consignee_telephone"]);
        }
        if(isset($condition["consignee_mobile"]) && $condition["consignee_mobile"] != ""){
            $select->where("consignee_mobile = ?",$condition["consignee_mobile"]);
        }
        if(isset($condition["consignee_email"]) && $condition["consignee_email"] != ""){
            $select->where("consignee_email = ?",$condition["consignee_email"]);
        }
        if(isset($condition["consignee_certificatetype"]) && $condition["consignee_certificatetype"] != ""){
            $select->where("consignee_certificatetype = ?",$condition["consignee_certificatetype"]);
        }
        if(isset($condition["consignee_certificatecode"]) && $condition["consignee_certificatecode"] != ""){
            $select->where("consignee_certificatecode = ?",$condition["consignee_certificatecode"]);
        }
        if(isset($condition["consignee_fax"]) && $condition["consignee_fax"] != ""){
            $select->where("consignee_fax = ?",$condition["consignee_fax"]);
        }
        if(isset($condition["consignee_mallaccount"]) && $condition["consignee_mallaccount"] != ""){
            $select->where("consignee_mallaccount = ?",$condition["consignee_mallaccount"]);
        }
        if(isset($condition["is_default"]) && $condition["is_default"] != ""){
            $select->where("is_default = ?",$condition["is_default"]);
        }
        if(isset($condition["create_date_sys"]) && $condition["create_date_sys"] != ""){
            $select->where("create_date_sys = ?",$condition["create_date_sys"]);
        }
        if(isset($condition["modify_date_sys"]) && $condition["modify_date_sys"] != ""){
            $select->where("modify_date_sys = ?",$condition["modify_date_sys"]);
        }
        if(isset($condition["is_modify"]) && $condition["is_modify"] != ""){
            $select->where("is_modify = ?",$condition["is_modify"]);
        }
        /*CONDITION_END*/
//         echo $select->__toString();exit;
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
//             echo $sql;exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}