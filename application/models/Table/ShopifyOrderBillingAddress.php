<?php
class Table_ShopifyOrderBillingAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyOrderBillingAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyOrderBillingAddress();
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
    public function update($row, $value, $field = "soba_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "soba_id")
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
    public function getByField($value, $field = 'soba_id', $colums = "*")
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
        
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["code"]) && $condition["code"] != ""){
            $select->where("code = ?",$condition["code"]);
        }
        if(isset($condition["address1"]) && $condition["address1"] != ""){
            $select->where("address1 = ?",$condition["address1"]);
        }
        if(isset($condition["address2"]) && $condition["address2"] != ""){
            $select->where("address2 = ?",$condition["address2"]);
        }
        if(isset($condition["city"]) && $condition["city"] != ""){
            $select->where("city = ?",$condition["city"]);
        }
        if(isset($condition["company"]) && $condition["company"] != ""){
            $select->where("company = ?",$condition["company"]);
        }
        if(isset($condition["country"]) && $condition["country"] != ""){
            $select->where("country = ?",$condition["country"]);
        }
        if(isset($condition["first_name"]) && $condition["first_name"] != ""){
            $select->where("first_name = ?",$condition["first_name"]);
        }
        if(isset($condition["last_name"]) && $condition["last_name"] != ""){
            $select->where("last_name = ?",$condition["last_name"]);
        }
        if(isset($condition["latitude"]) && $condition["latitude"] != ""){
            $select->where("latitude = ?",$condition["latitude"]);
        }
        if(isset($condition["longitude"]) && $condition["longitude"] != ""){
            $select->where("longitude = ?",$condition["longitude"]);
        }
        if(isset($condition["phone"]) && $condition["phone"] != ""){
            $select->where("phone = ?",$condition["phone"]);
        }
        if(isset($condition["province"]) && $condition["province"] != ""){
            $select->where("province = ?",$condition["province"]);
        }
        if(isset($condition["zip"]) && $condition["zip"] != ""){
            $select->where("zip = ?",$condition["zip"]);
        }
        if(isset($condition["name"]) && $condition["name"] != ""){
            $select->where("name = ?",$condition["name"]);
        }
        if(isset($condition["country_code"]) && $condition["country_code"] != ""){
            $select->where("country_code = ?",$condition["country_code"]);
        }
        if(isset($condition["province_code"]) && $condition["province_code"] != ""){
            $select->where("province_code = ?",$condition["province_code"]);
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