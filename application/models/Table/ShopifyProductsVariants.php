<?php
class Table_ShopifyProductsVariants
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyProductsVariants();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyProductsVariants();
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
        
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["sku"]) && $condition["sku"] != ""){
            $select->where("sku = ?",$condition["sku"]);
        }
        if(isset($condition["barcode"]) && $condition["barcode"] != ""){
            $select->where("barcode = ?",$condition["barcode"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["compare_at_price"]) && $condition["compare_at_price"] != ""){
            $select->where("compare_at_price = ?",$condition["compare_at_price"]);
        }
        if(isset($condition["fulfillment_service"]) && $condition["fulfillment_service"] != ""){
            $select->where("fulfillment_service = ?",$condition["fulfillment_service"]);
        }
        if(isset($condition["grams"]) && $condition["grams"] != ""){
            $select->where("grams = ?",$condition["grams"]);
        }
        if(isset($condition["inventory_management"]) && $condition["inventory_management"] != ""){
            $select->where("inventory_management = ?",$condition["inventory_management"]);
        }
        if(isset($condition["inventory_policy"]) && $condition["inventory_policy"] != ""){
            $select->where("inventory_policy = ?",$condition["inventory_policy"]);
        }
        if(isset($condition["option1"]) && $condition["option1"] != ""){
            $select->where("option1 = ?",$condition["option1"]);
        }
        if(isset($condition["option2"]) && $condition["option2"] != ""){
            $select->where("option2 = ?",$condition["option2"]);
        }
        if(isset($condition["option3"]) && $condition["option3"] != ""){
            $select->where("option3 = ?",$condition["option3"]);
        }
        if(isset($condition["position"]) && $condition["position"] != ""){
            $select->where("position = ?",$condition["position"]);
        }
        if(isset($condition["price"]) && $condition["price"] != ""){
            $select->where("price = ?",$condition["price"]);
        }
        if(isset($condition["requires_shipping"]) && $condition["requires_shipping"] != ""){
            $select->where("requires_shipping = ?",$condition["requires_shipping"]);
        }
        if(isset($condition["taxable"]) && $condition["taxable"] != ""){
            $select->where("taxable = ?",$condition["taxable"]);
        }
        if(isset($condition["inventory_quantity"]) && $condition["inventory_quantity"] != ""){
            $select->where("inventory_quantity = ?",$condition["inventory_quantity"]);
        }
        if(isset($condition["old_inventory_quantity"]) && $condition["old_inventory_quantity"] != ""){
            $select->where("old_inventory_quantity = ?",$condition["old_inventory_quantity"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
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