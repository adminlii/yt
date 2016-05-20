<?php
class Table_ProductDevelop
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ProductDevelop();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ProductDevelop();
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
    public function update($row, $value, $field = "pd_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "pd_id")
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
    public function getByField($value, $field = 'pd_id', $colums = "*")
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
    
    public function getDevelopingCount(){
    	$sql = "select count(pd_status) as count from product_develop where product_develop.pd_status <> 10";
    	return $this->_table->getAdapter()->fetchRow($sql);
    }
    
    public function getDevelopedCount(){
    	$sql = "select count(pd_status) as count from product_develop where product_develop.pd_status = 10";
    	return $this->_table->getAdapter()->fetchRow($sql);
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

        if(isset($condition["pd_id"]) && $condition["pd_id"] != ""){
            $select->where("pd_id = ?",$condition["pd_id"]);
        }

        if(isset($condition["pd_id_arr"]) && !empty($condition["pd_id_arr"])){
            $select->where("pd_id in(?)",$condition["pd_id_arr"]);
        }
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?",$condition["product_code"]);
        }
        if(isset($condition["product_title"]) && $condition["product_title"] != ""){
            $select->where("product_title = ?",$condition["product_title"]);
        }
        if(isset($condition["product_title_en"]) && $condition["product_title_en"] != ""){
            $select->where("product_title_en = ?",$condition["product_title_en"]);
        }
        if(isset($condition["product_sku"]) && $condition["product_sku"] != ""){
            $select->where("product_sku = ?",$condition["product_sku"]);
        }
        if(isset($condition["product_category_ebay"]) && $condition["product_category_ebay"] != ""){
            $select->where("product_category_ebay = ?",$condition["product_category_ebay"]);
        }
        if(isset($condition["product_category"]) && $condition["product_category"] != ""){
            $select->where("product_category = ?",$condition["product_category"]);
        }
        if(isset($condition["es_id"]) && $condition["es_id"] != ""){
            $select->where("es_id = ?",$condition["es_id"]);
        }
        if(isset($condition["pd_weight"]) && $condition["pd_weight"] != ""){
            $select->where("pd_weight = ?",$condition["pd_weight"]);
        }
        if(isset($condition["pd_height"]) && $condition["pd_height"] != ""){
            $select->where("pd_height = ?",$condition["pd_height"]);
        }
        if(isset($condition["pd_width"]) && $condition["pd_width"] != ""){
            $select->where("pd_width = ?",$condition["pd_width"]);
        }
        if(isset($condition["pd_length"]) && $condition["pd_length"] != ""){
            $select->where("pd_length = ?",$condition["pd_length"]);
        }
        if(isset($condition["pd_type"]) && $condition["pd_type"] != ""){
            $select->where("pd_type = ?",$condition["pd_type"]);
        }
        if(isset($condition["pd_reason"]) && $condition["pd_reason"] != ""){
            $select->where("pd_reason = ?",$condition["pd_reason"]);
        }
        if(isset($condition["pd_status"]) && $condition["pd_status"] != ""){
            $select->where("pd_status = ?",$condition["pd_status"]);
        }
        if(isset($condition["is_competitor"]) && $condition["is_competitor"] != ""){
            $select->where("is_competitor = ?",$condition["is_competitor"]);
        }
        if(isset($condition["pd_phototype"]) && $condition["pd_phototype"] != ""){
            $select->where("pd_phototype = ?",$condition["pd_phototype"]);
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

    /**
     * @desc获取默认供应商及单价
     * @param string $sku
     * @return mixed
     */
    public function getDevelopDefaultSupplierId($sku = '')
    {
        $sql = "SELECT pd_id,supplier_id,supplier_code FROM `product_develop` LEFT JOIN supplier on product_develop.default_supplier_code=supplier.supplier_code WHERE product_develop.default_supplier_code!='' and product_sku='" . $sku . "'";
        return $this->_table->getAdapter()->fetchRow($sql);
    }

}