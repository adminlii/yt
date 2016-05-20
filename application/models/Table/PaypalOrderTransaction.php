<?php
class Table_PaypalOrderTransaction
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PaypalOrderTransaction();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PaypalOrderTransaction();
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
    public function update($row, $value, $field = "pot_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pot_id")
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
    public function getByField($value, $field = 'pot_id', $colums = "*")
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
        
        if(isset($condition["pt_id"]) && $condition["pt_id"] != ""){
            $select->where("pt_id = ?",$condition["pt_id"]);
        }
        if(isset($condition["pot_paypal_id"]) && $condition["pot_paypal_id"] != ""){
            $select->where("pot_paypal_id = ?",$condition["pot_paypal_id"]);
        }
        if(isset($condition["pot_country_code"]) && $condition["pot_country_code"] != ""){
            $select->where("pot_country_code = ?",$condition["pot_country_code"]);
        }
        if(isset($condition["pot_ship_name"]) && $condition["pot_ship_name"] != ""){
            $select->where("pot_ship_name = ?",$condition["pot_ship_name"]);
        }
        if(isset($condition["pot_ship_street1"]) && $condition["pot_ship_street1"] != ""){
            $select->where("pot_ship_street1 = ?",$condition["pot_ship_street1"]);
        }
        if(isset($condition["pot_ship_street2"]) && $condition["pot_ship_street2"] != ""){
            $select->where("pot_ship_street2 = ?",$condition["pot_ship_street2"]);
        }
        if(isset($condition["pot_ship_city"]) && $condition["pot_ship_city"] != ""){
            $select->where("pot_ship_city = ?",$condition["pot_ship_city"]);
        }
        if(isset($condition["pot_ship_state"]) && $condition["pot_ship_state"] != ""){
            $select->where("pot_ship_state = ?",$condition["pot_ship_state"]);
        }
        if(isset($condition["pot_ship_county_code"]) && $condition["pot_ship_county_code"] != ""){
            $select->where("pot_ship_county_code = ?",$condition["pot_ship_county_code"]);
        }
        if(isset($condition["pot_ship_county_name"]) && $condition["pot_ship_county_name"] != ""){
            $select->where("pot_ship_county_name = ?",$condition["pot_ship_county_name"]);
        }
        if(isset($condition["pot_ship_zip"]) && $condition["pot_ship_zip"] != ""){
            $select->where("pot_ship_zip = ?",$condition["pot_ship_zip"]);
        }
        if(isset($condition["pot_buyer_id"]) && $condition["pot_buyer_id"] != ""){
            $select->where("pot_buyer_id = ?",$condition["pot_buyer_id"]);
        }
        if(isset($condition["pot_first_name"]) && $condition["pot_first_name"] != ""){
            $select->where("pot_first_name = ?",$condition["pot_first_name"]);
        }
        if(isset($condition["pot_last_name"]) && $condition["pot_last_name"] != ""){
            $select->where("pot_last_name = ?",$condition["pot_last_name"]);
        }
        if(isset($condition["pot_amt"]) && $condition["pot_amt"] != ""){
            $select->where("pot_amt = ?",$condition["pot_amt"]);
        }
        if(isset($condition["pot_currency_code"]) && $condition["pot_currency_code"] != ""){
            $select->where("pot_currency_code = ?",$condition["pot_currency_code"]);
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