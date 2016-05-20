<?php
class Table_ShopifyProducts
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShopifyProducts();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShopifyProducts();
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
        
        if(isset($condition["id_arr"]) && is_array($condition["id_arr"])&&!empty($condition["id_arr"])){
            $select->where("id in (?)",$condition["id_arr"]);
        }     
        
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["user_account_arr"]) && is_array($condition["user_account_arr"])&&!empty($condition["user_account_arr"])){
            $select->where("user_account in (?)",$condition["user_account_arr"]);
        }
        if(isset($condition["handle"]) && $condition["handle"] != ""){
            $select->where("handle = ?",$condition["handle"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["published_at"]) && $condition["published_at"] != ""){
            $select->where("published_at = ?",$condition["published_at"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
        }
        if(isset($condition["product_type"]) && $condition["product_type"] != ""){
            $select->where("product_type = ?",$condition["product_type"]);
        }
        if(isset($condition["template_suffix"]) && $condition["template_suffix"] != ""){
            $select->where("template_suffix = ?",$condition["template_suffix"]);
        }
        if(isset($condition["title"]) && $condition["title"] != ""){
            $select->where("title = ?",$condition["title"]);
        }
        if(isset($condition["vendor"]) && $condition["vendor"] != ""){
            $select->where("vendor = ?",$condition["vendor"]);
        }
        if(isset($condition["tags"]) && $condition["tags"] != ""){
            $select->where("tags = ?",$condition["tags"]);
        }
        if(isset($condition["published_scope"]) && $condition["published_scope"] != ""){
            $select->where("published_scope = ?",$condition["published_scope"]);
        }

        if(isset($condition["sku_like"]) && $condition["sku_like"] != ""){
            $select->where("skus like ?","%{$condition["sku_like"]}%");
        }

        if(isset($condition["sell_qty_from"]) && $condition["sell_qty_from"] != ""){
            $select->where("inventory_quantity >= ?",$condition["sell_qty_from"]);
        }

        if(isset($condition["sell_qty_to"]) && $condition["sell_qty_to"] != ""){
            $select->where("inventory_quantity <= ?",$condition["sell_qty_to"]);
        }

        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
        if(isset($condition["recommand"]) && $condition["recommand"] != ""){
            $select->where("recommand = ?",$condition["recommand"]);
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