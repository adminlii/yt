<?php
class Table_PbrPublicShipperAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PbrPublicShipperAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PbrPublicShipperAddress();
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
    public function update($row, $value, $field = "psa_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "psa_id")
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
    public function getByField($value, $field = 'psa_id', $colums = "*")
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
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?",$condition["product_code"]);
        }
        if(isset($condition["server_channelid"]) && $condition["server_channelid"] != ""){
            $select->where("server_channelid = ?",$condition["server_channelid"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?",$condition["country_code"]);
        }
        if(isset($condition["shipper_account"]) && $condition["shipper_account"] != ""){
            $select->where("shipper_account = ?",$condition["shipper_account"]);
        }
        if(isset($condition["shipper_name"]) && $condition["shipper_name"] != ""){
            $select->where("shipper_name = ?",$condition["shipper_name"]);
        }
        if(isset($condition["shipper_company"]) && $condition["shipper_company"] != ""){
            $select->where("shipper_company = ?",$condition["shipper_company"]);
        }
        if(isset($condition["shipper_countrycode"]) && $condition["shipper_countrycode"] != ""){
            $select->where("shipper_countrycode = ?",$condition["shipper_countrycode"]);
        }
        if(isset($condition["shipper_province"]) && $condition["shipper_province"] != ""){
            $select->where("shipper_province = ?",$condition["shipper_province"]);
        }
        if(isset($condition["shipper_city"]) && $condition["shipper_city"] != ""){
            $select->where("shipper_city = ?",$condition["shipper_city"]);
        }
        if(isset($condition["shipper_street"]) && $condition["shipper_street"] != ""){
            $select->where("shipper_street = ?",$condition["shipper_street"]);
        }
        if(isset($condition["shipper_postcode"]) && $condition["shipper_postcode"] != ""){
            $select->where("shipper_postcode = ?",$condition["shipper_postcode"]);
        }
        if(isset($condition["shipper_areacode"]) && $condition["shipper_areacode"] != ""){
            $select->where("shipper_areacode = ?",$condition["shipper_areacode"]);
        }
        if(isset($condition["shipper_telephone"]) && $condition["shipper_telephone"] != ""){
            $select->where("shipper_telephone = ?",$condition["shipper_telephone"]);
        }
        if(isset($condition["shipper_mobile"]) && $condition["shipper_mobile"] != ""){
            $select->where("shipper_mobile = ?",$condition["shipper_mobile"]);
        }
        if(isset($condition["shipper_email"]) && $condition["shipper_email"] != ""){
            $select->where("shipper_email = ?",$condition["shipper_email"]);
        }
        if(isset($condition["shipper_certificatetype"]) && $condition["shipper_certificatetype"] != ""){
            $select->where("shipper_certificatetype = ?",$condition["shipper_certificatetype"]);
        }
        if(isset($condition["shipper_certificatecode"]) && $condition["shipper_certificatecode"] != ""){
            $select->where("shipper_certificatecode = ?",$condition["shipper_certificatecode"]);
        }
        if(isset($condition["shipper_fax"]) && $condition["shipper_fax"] != ""){
            $select->where("shipper_fax = ?",$condition["shipper_fax"]);
        }
        if(isset($condition["shipper_mallaccount"]) && $condition["shipper_mallaccount"] != ""){
            $select->where("shipper_mallaccount = ?",$condition["shipper_mallaccount"]);
        }
        if(isset($condition["create_date_sys"]) && $condition["create_date_sys"] != ""){
            $select->where("create_date_sys = ?",$condition["create_date_sys"]);
        }
        if(isset($condition["modify_date_sys"]) && $condition["modify_date_sys"] != ""){
            $select->where("modify_date_sys = ?",$condition["modify_date_sys"]);
        }
        if(isset($condition["modify_st_id"]) && $condition["modify_st_id"] != ""){
            $select->where("modify_st_id = ?",$condition["modify_st_id"]);
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