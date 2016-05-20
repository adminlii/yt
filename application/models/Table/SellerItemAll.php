<?php
class Table_SellerItemAll
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_SellerItemAll();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_SellerItemAll();
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
    public function update($row, $value, $field = "sia_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "sia_id")
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
    public function getByField($value, $field = 'sia_id', $colums = "*")
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
        
        if(isset($condition["item_id"]) && $condition["item_id"] != ""){
            $select->where("item_id = ?",$condition["item_id"]);
        }
        if(isset($condition["item_status"]) && $condition["item_status"] != ""){
            $select->where("item_status = ?",$condition["item_status"]);
        }
        if(isset($condition["no_stock_online"]) && $condition["no_stock_online"] != ""){
            $select->where("no_stock_online = ?",$condition["no_stock_online"]);
        }
        if(isset($condition["start_time"]) && $condition["start_time"] != ""){
            $select->where("start_time = ?",$condition["start_time"]);
        }
        if(isset($condition["end_time"]) && $condition["end_time"] != ""){
            $select->where("end_time = ?",$condition["end_time"]);
        }
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["currency"]) && $condition["currency"] != ""){
            $select->where("currency = ?",$condition["currency"]);
        }
        if(isset($condition["price_sell"]) && $condition["price_sell"] != ""){
            $select->where("price_sell = ?",$condition["price_sell"]);
        }
        if(isset($condition["price_purchase"]) && $condition["price_purchase"] != ""){
            $select->where("price_purchase = ?",$condition["price_purchase"]);
        }
        if(isset($condition["item_title"]) && $condition["item_title"] != ""){
            $select->where("item_title = ?",$condition["item_title"]);
        }
        if(isset($condition["item_url"]) && $condition["item_url"] != ""){
            $select->where("item_url = ?",$condition["item_url"]);
        }
        if(isset($condition["category_id"]) && $condition["category_id"] != ""){
            $select->where("category_id = ?",$condition["category_id"]);
        }
        if(isset($condition["category_name"]) && $condition["category_name"] != ""){
            $select->where("category_name = ?",$condition["category_name"]);
        }
        if(isset($condition["pic_path"]) && $condition["pic_path"] != ""){
            $select->where("pic_path = ?",$condition["pic_path"]);
        }
        if(isset($condition["site"]) && $condition["site"] != ""){
            $select->where("site = ?",$condition["site"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["warehouse_sku"]) && $condition["warehouse_sku"] != ""){
            $select->where("warehouse_sku = ?",$condition["warehouse_sku"]);
        }
        if(isset($condition["is_binding_auction"]) && $condition["is_binding_auction"] != ""){
            $select->where("is_binding_auction = ?",$condition["is_binding_auction"]);
        }
        if(isset($condition["sell_qty"]) && $condition["sell_qty"] != ""){
            $select->where("sell_qty = ?",$condition["sell_qty"]);
        }
        if(isset($condition["sold_qty"]) && $condition["sold_qty"] != ""){
            $select->where("sold_qty = ?",$condition["sold_qty"]);
        }
        if(isset($condition["sell_type"]) && $condition["sell_type"] != ""){
            $select->where("sell_type = ?",$condition["sell_type"]);
        }
        if(isset($condition["item_location"]) && $condition["item_location"] != ""){
            $select->where("item_location = ?",$condition["item_location"]);
        }
        if(isset($condition["auto_supply"]) && $condition["auto_supply"] != ""){
            $select->where("auto_supply = ?",$condition["auto_supply"]);
        }
        if(isset($condition["need_supply"]) && $condition["need_supply"] != ""){
            $select->where("need_supply = ?",$condition["need_supply"]);
        }
        if(isset($condition["paypal_email_address"]) && $condition["paypal_email_address"] != ""){
            $select->where("paypal_email_address = ?",$condition["paypal_email_address"]);
        }
        if(isset($condition["list_type"]) && $condition["list_type"] != ""){
            $select->where("list_type = ?",$condition["list_type"]);
        }
        if(isset($condition["fee_insertion"]) && $condition["fee_insertion"] != ""){
            $select->where("fee_insertion = ?",$condition["fee_insertion"]);
        }
        if(isset($condition["fee_insertion_currency"]) && $condition["fee_insertion_currency"] != ""){
            $select->where("fee_insertion_currency = ?",$condition["fee_insertion_currency"]);
        }
        if(isset($condition["var_sku"]) && $condition["var_sku"] != ""){
            $select->where("var_sku = ?",$condition["var_sku"]);
        }
        if(isset($condition["var_sku_desc"]) && $condition["var_sku_desc"] != ""){
            $select->where("var_sku_desc = ?",$condition["var_sku_desc"]);
        }
        if(isset($condition["var_qty"]) && $condition["var_qty"] != ""){
            $select->where("var_qty = ?",$condition["var_qty"]);
        }
        if(isset($condition["var_qty_sold"]) && $condition["var_qty_sold"] != ""){
            $select->where("var_qty_sold = ?",$condition["var_qty_sold"]);
        }
        if(isset($condition["var_start_pice"]) && $condition["var_start_pice"] != ""){
            $select->where("var_start_pice = ?",$condition["var_start_pice"]);
        }
        if(isset($condition["var_currency"]) && $condition["var_currency"] != ""){
            $select->where("var_currency = ?",$condition["var_currency"]);
        }
        if(isset($condition["last_sale_time"]) && $condition["last_sale_time"] != ""){
            $select->where("last_sale_time = ?",$condition["last_sale_time"]);
        }
        if(isset($condition["last_modify_time"]) && $condition["last_modify_time"] != ""){
            $select->where("last_modify_time = ?",$condition["last_modify_time"]);
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