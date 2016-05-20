<?php
class Table_AfterShipTrackings
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AfterShipTrackings();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AfterShipTrackings();
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
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
        }
        if(isset($condition["tracking_number"]) && $condition["tracking_number"] != ""){
            $select->where("tracking_number = ?",$condition["tracking_number"]);
        }
        if(isset($condition["slug"]) && $condition["slug"] != ""){
            $select->where("slug = ?",$condition["slug"]);
        }
        if(isset($condition["active"]) && $condition["active"] != ""){
            $select->where("active = ?",$condition["active"]);
        }
        if(isset($condition["custom_fields"]) && $condition["custom_fields"] != ""){
            $select->where("custom_fields = ?",$condition["custom_fields"]);
        }
        if(isset($condition["customer_name"]) && $condition["customer_name"] != ""){
            $select->where("customer_name = ?",$condition["customer_name"]);
        }
        if(isset($condition["origin_country_iso3"]) && $condition["origin_country_iso3"] != ""){
            $select->where("origin_country_iso3 = ?",$condition["origin_country_iso3"]);
        }
        if(isset($condition["destination_country_iso3"]) && $condition["destination_country_iso3"] != ""){
            $select->where("destination_country_iso3 = ?",$condition["destination_country_iso3"]);
        }
        if(isset($condition["emails"]) && $condition["emails"] != ""){
            $select->where("emails = ?",$condition["emails"]);
        }
        if(isset($condition["smses"]) && $condition["smses"] != ""){
            $select->where("smses = ?",$condition["smses"]);
        }
        if(isset($condition["expected_delivery"]) && $condition["expected_delivery"] != ""){
            $select->where("expected_delivery = ?",$condition["expected_delivery"]);
        }
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["order_id_path"]) && $condition["order_id_path"] != ""){
            $select->where("order_id_path = ?",$condition["order_id_path"]);
        }
        if(isset($condition["shipment_type"]) && $condition["shipment_type"] != ""){
            $select->where("shipment_type = ?",$condition["shipment_type"]);
        }
        if(isset($condition["signed_by"]) && $condition["signed_by"] != ""){
            $select->where("signed_by = ?",$condition["signed_by"]);
        }
        if(isset($condition["source"]) && $condition["source"] != ""){
            $select->where("source = ?",$condition["source"]);
        }
        if(isset($condition["tag"]) && $condition["tag"] != ""){
            $select->where("tag = ?",$condition["tag"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["tracked_count"]) && $condition["tracked_count"] != ""){
            $select->where("tracked_count = ?",$condition["tracked_count"]);
        }
        if(isset($condition["unique_token"]) && $condition["unique_token"] != ""){
            $select->where("unique_token = ?",$condition["unique_token"]);
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