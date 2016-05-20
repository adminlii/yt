<?php
class Table_ShopifyOrderFulfillmentsLineItems
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyOrderFulfillmentsLineItems();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyOrderFulfillmentsLineItems();
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
        
        if(isset($condition["order_id"]) && $condition["order_id"] != ""){
            $select->where("order_id = ?",$condition["order_id"]);
        }
        if(isset($condition["fulfillment_id"]) && $condition["fulfillment_id"] != ""){
            $select->where("fulfillment_id = ?",$condition["fulfillment_id"]);
        }
        if(isset($condition["fulfillment_service"]) && $condition["fulfillment_service"] != ""){
            $select->where("fulfillment_service = ?",$condition["fulfillment_service"]);
        }
        if(isset($condition["fulfillment_status"]) && $condition["fulfillment_status"] != ""){
            $select->where("fulfillment_status = ?",$condition["fulfillment_status"]);
        }
        if(isset($condition["gift_card"]) && $condition["gift_card"] != ""){
            $select->where("gift_card = ?",$condition["gift_card"]);
        }
        if(isset($condition["grams"]) && $condition["grams"] != ""){
            $select->where("grams = ?",$condition["grams"]);
        }
        if(isset($condition["price"]) && $condition["price"] != ""){
            $select->where("price = ?",$condition["price"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["quantity"]) && $condition["quantity"] != ""){
            $select->where("quantity = ?",$condition["quantity"]);
        }
        if(isset($condition["requires_shipping"]) && $condition["requires_shipping"] != ""){
            $select->where("requires_shipping = ?",$condition["requires_shipping"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["taxable"]) && $condition["taxable"] != ""){
            $select->where("taxable = ?",$condition["taxable"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["variant_id"]) && $condition["variant_id"] != ""){
            $select->where("variant_id = ?",$condition["variant_id"]);
        }
        if(isset($condition["variant_title"]) && $condition["variant_title"] != ""){
            $select->where("variant_title = ?",$condition["variant_title"]);
        }
        if(isset($condition["vendor"]) && $condition["vendor"] != ""){
            $select->where("vendor = ?",$condition["vendor"]);
        }
        if(isset($condition["name"]) && $condition["name"] != ""){
            $select->where("name = ?",$condition["name"]);
        }
        if(isset($condition["variant_inventory_management"]) && $condition["variant_inventory_management"] != ""){
            $select->where("variant_inventory_management = ?",$condition["variant_inventory_management"]);
        }
        if(isset($condition["properties"]) && $condition["properties"] != ""){
            $select->where("properties = ?",$condition["properties"]);
        }
        if(isset($condition["product_exists"]) && $condition["product_exists"] != ""){
            $select->where("product_exists = ?",$condition["product_exists"]);
        }
        if(isset($condition["fulfillable_quantity"]) && $condition["fulfillable_quantity"] != ""){
            $select->where("fulfillable_quantity = ?",$condition["fulfillable_quantity"]);
        }
        if(isset($condition["tax_lines"]) && $condition["tax_lines"] != ""){
            $select->where("tax_lines = ?",$condition["tax_lines"]);
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