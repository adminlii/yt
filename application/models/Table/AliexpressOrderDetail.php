<?php
class Table_AliexpressOrderDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AliexpressOrderDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AliexpressOrderDetail();
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
    public function update($row, $value, $field = "aod_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "aod_id")
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
    public function getByField($value, $field = 'aod_id', $colums = "*")
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
        
        if(isset($condition["aoo_id"]) && $condition["aoo_id"] != ""){
            $select->where("aoo_id = ?",$condition["aoo_id"]);
        }
        if(isset($condition["child_id"]) && $condition["child_id"] != ""){
            $select->where("child_id = ?",$condition["child_id"]);
        }
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["son_order_status"]) && $condition["son_order_status"] != ""){
            $select->where("son_order_status = ?",$condition["son_order_status"]);
        }
        if(isset($condition["goods_prepare_time"]) && $condition["goods_prepare_time"] != ""){
            $select->where("goods_prepare_time = ?",$condition["goods_prepare_time"]);
        }
        if(isset($condition["memo"]) && $condition["memo"] != ""){
            $select->where("memo = ?",$condition["memo"]);
        }
        if(isset($condition["sku_code"]) && $condition["sku_code"] != ""){
            $select->where("sku_code = ?",$condition["sku_code"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_count"]) && $condition["product_count"] != ""){
            $select->where("product_count = ?",$condition["product_count"]);
        }
        if(isset($condition["product_unit"]) && $condition["product_unit"] != ""){
            $select->where("product_unit = ?",$condition["product_unit"]);
        }
        if(isset($condition["product_img_url"]) && $condition["product_img_url"] != ""){
            $select->where("product_img_url = ?",$condition["product_img_url"]);
        }
        if(isset($condition["product_name"]) && $condition["product_name"] != ""){
            $select->where("product_name = ?",$condition["product_name"]);
        }
        if(isset($condition["product_standard"]) && $condition["product_standard"] != ""){
            $select->where("product_standard = ?",$condition["product_standard"]);
        }
        if(isset($condition["product_snap_url"]) && $condition["product_snap_url"] != ""){
            $select->where("product_snap_url = ?",$condition["product_snap_url"]);
        }
        if(isset($condition["show_status"]) && $condition["show_status"] != ""){
            $select->where("show_status = ?",$condition["show_status"]);
        }
        if(isset($condition["product_unit_price_amount"]) && $condition["product_unit_price_amount"] != ""){
            $select->where("product_unit_price_amount = ?",$condition["product_unit_price_amount"]);
        }
        if(isset($condition["product_unit_price_cent"]) && $condition["product_unit_price_cent"] != ""){
            $select->where("product_unit_price_cent = ?",$condition["product_unit_price_cent"]);
        }
        if(isset($condition["product_unit_price_cent_factor"]) && $condition["product_unit_price_cent_factor"] != ""){
            $select->where("product_unit_price_cent_factor = ?",$condition["product_unit_price_cent_factor"]);
        }
        if(isset($condition["product_unit_price_currency_code"]) && $condition["product_unit_price_currency_code"] != ""){
            $select->where("product_unit_price_currency_code = ?",$condition["product_unit_price_currency_code"]);
        }
        if(isset($condition["product_unit_price_currency_default_fraction_digits"]) && $condition["product_unit_price_currency_default_fraction_digits"] != ""){
            $select->where("product_unit_price_currency_default_fraction_digits = ?",$condition["product_unit_price_currency_default_fraction_digits"]);
        }
        if(isset($condition["product_unit_price_currency_currency_code"]) && $condition["product_unit_price_currency_currency_code"] != ""){
            $select->where("product_unit_price_currency_currency_code = ?",$condition["product_unit_price_currency_currency_code"]);
        }
        if(isset($condition["product_unit_price_currency_symbol"]) && $condition["product_unit_price_currency_symbol"] != ""){
            $select->where("product_unit_price_currency_symbol = ?",$condition["product_unit_price_currency_symbol"]);
        }
        if(isset($condition["total_product_amount"]) && $condition["total_product_amount"] != ""){
            $select->where("total_product_amount = ?",$condition["total_product_amount"]);
        }
        if(isset($condition["total_product_cent"]) && $condition["total_product_cent"] != ""){
            $select->where("total_product_cent = ?",$condition["total_product_cent"]);
        }
        if(isset($condition["total_product_cent_factor"]) && $condition["total_product_cent_factor"] != ""){
            $select->where("total_product_cent_factor = ?",$condition["total_product_cent_factor"]);
        }
        if(isset($condition["total_product_currency_code"]) && $condition["total_product_currency_code"] != ""){
            $select->where("total_product_currency_code = ?",$condition["total_product_currency_code"]);
        }
        if(isset($condition["total_product_currency_default_fraction_digits"]) && $condition["total_product_currency_default_fraction_digits"] != ""){
            $select->where("total_product_currency_default_fraction_digits = ?",$condition["total_product_currency_default_fraction_digits"]);
        }
        if(isset($condition["total_product_currency_currency_code"]) && $condition["total_product_currency_currency_code"] != ""){
            $select->where("total_product_currency_currency_code = ?",$condition["total_product_currency_currency_code"]);
        }
        if(isset($condition["total_product_currency_symbol"]) && $condition["total_product_currency_symbol"] != ""){
            $select->where("total_product_currency_symbol = ?",$condition["total_product_currency_symbol"]);
        }
        if(isset($condition["freight_commit_day"]) && $condition["freight_commit_day"] != ""){
            $select->where("freight_commit_day = ?",$condition["freight_commit_day"]);
        }
        if(isset($condition["can_submit_issue"]) && $condition["can_submit_issue"] != ""){
            $select->where("can_submit_issue = ?",$condition["can_submit_issue"]);
        }
        if(isset($condition["issue_status"]) && $condition["issue_status"] != ""){
            $select->where("issue_status = ?",$condition["issue_status"]);
        }
        if(isset($condition["issue_mode"]) && $condition["issue_mode"] != ""){
            $select->where("issue_mode = ?",$condition["issue_mode"]);
        }
        if(isset($condition["logistics_type"]) && $condition["logistics_type"] != ""){
            $select->where("logistics_type = ?",$condition["logistics_type"]);
        }
        if(isset($condition["logistics_service_name"]) && $condition["logistics_service_name"] != ""){
            $select->where("logistics_service_name = ?",$condition["logistics_service_name"]);
        }
        if(isset($condition["money_back_three"]) && $condition["money_back_three"] != ""){
            $select->where("money_back_three = ?",$condition["money_back_three"]);
        }
        if(isset($condition["fund_status"]) && $condition["fund_status"] != ""){
            $select->where("fund_status = ?",$condition["fund_status"]);
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