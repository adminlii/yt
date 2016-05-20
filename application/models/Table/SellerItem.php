<?php
class Table_SellerItem
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_SellerItem();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_SellerItem();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
        $row['last_modify_time'] = date('Y-m-d H:i:s');
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "si_id")
    {
        $row['last_modify_time'] = date('Y-m-d H:i:s');
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "si_id")
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
    public function getByField($value, $field = 'si_id', $colums = "*")
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

        if(isset($condition["item_id_arr"]) && is_array($condition["item_id_arr"])&&!empty($condition["item_id_arr"])){
            $select->where("item_id in (?)",$condition["item_id_arr"]);
        }
        
        if(isset($condition["item_status"]) && $condition["item_status"] != ""){
            $select->where("item_status = ?",$condition["item_status"]);
            
            if($condition["item_status"]=='Active'){//销售中产品，结束时间需小于当前时间 
//                 $select->where("unix_timestamp(DATE_add(end_time,INTERVAL 8 HOUR ))>= unix_timestamp('".date('Y-m-d H:i:s')."') ");                 
            }
            
        }
//         echo $select;exit;
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }

        if(isset($condition["sku_like"]) && $condition["sku_like"] != ""){
            $select->where("sku like ?",'%'.$condition["sku_like"].'%');
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

        if(isset($condition["need_supply"]) && $condition["need_supply"] != ""){
            $select->where("need_supply = ?",$condition["need_supply"]);
        }

        if(isset($condition["sell_type"]) && $condition["sell_type"] != ""){
            $select->where("sell_type = ?",$condition["sell_type"]);
        }

        if(isset($condition["sold_qty_from"]) && $condition["sold_qty_from"] != ""){
            $select->where("sold_qty >= ?",$condition["sold_qty_from"]);
        }

        if(isset($condition["sold_qty_to"]) && $condition["sold_qty_to"] != ""){
            $select->where("sold_qty <= ?",$condition["sold_qty_to"]);
        }

        if(isset($condition["sell_qty_to"]) && $condition["sell_qty_to"] != ""){
            $select->where("sell_qty-sold_qty <= ?",$condition["sell_qty_to"]);
        }

        if(isset($condition["sell_qty_from"]) && $condition["sell_qty_from"] != ""){
            $select->where("sell_qty-sold_qty >= ?",$condition["sell_qty_from"]);
        }

        if(isset($condition["sync_status"]) && $condition["sync_status"] != ""){
            $select->where("sync_status = ?",$condition["sync_status"]);
        }
        
//         echo $select;exit;
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