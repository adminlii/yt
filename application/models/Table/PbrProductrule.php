<?php
class Table_PbrProductrule
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PbrProductrule();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PbrProductrule();
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
    public function update($row, $value, $field = "rule_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rule_id")
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
    public function getByField($value, $field = 'rule_id', $colums = "*")
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
        
        if(isset($condition["product_code"]) && $condition["product_code"] != ""){
            $select->where("product_code = ?",$condition["product_code"]);
        }
        if(isset($condition["web_show_type"]) && $condition["web_show_type"] != ""){
            $select->where("web_show_type = ?",$condition["web_show_type"]);
        }
        if(isset($condition["web_label_tyep"]) && $condition["web_label_tyep"] != ""){
            $select->where("web_label_tyep = ?",$condition["web_label_tyep"]);
        }
        if(isset($condition["web_invoice_type"]) && $condition["web_invoice_type"] != ""){
            $select->where("web_invoice_type = ?",$condition["web_invoice_type"]);
        }
        if(isset($condition["web_required"]) && $condition["web_required"] != ""){
            $select->where("web_required = ?",$condition["web_required"]);
        }
        if(isset($condition["web_price_test"]) && $condition["web_price_test"] != ""){
            $select->where("web_price_test = ?",$condition["web_price_test"]);
        }
        if(isset($condition["web_document_rule"]) && $condition["web_document_rule"] != ""){
            $select->where("web_document_rule = ?",$condition["web_document_rule"]);
        }
        if(isset($condition["product_oda_type"]) && $condition["product_oda_type"] != ""){
            $select->where("product_oda_type = ?",$condition["product_oda_type"]);
        }
        if(isset($condition["arrive_zone_type"]) && $condition["arrive_zone_type"] != ""){
            $select->where("arrive_zone_type = ?",$condition["arrive_zone_type"]);
        }
        if(isset($condition["operation_og_id"]) && $condition["operation_og_id"] != ""){
            $select->where("operation_og_id = ?",$condition["operation_og_id"]);
        }
        if(isset($condition["return_address"]) && $condition["return_address"] != ""){
            $select->where("return_address = ?",$condition["return_address"]);
        }
        if(isset($condition["account_info"]) && $condition["account_info"] != ""){
            $select->where("account_info = ?",$condition["account_info"]);
        }
        if(isset($condition["optional_serve_type"]) && $condition["optional_serve_type"] != ""){
            $select->where("optional_serve_type = ?",$condition["optional_serve_type"]);
        }
        if(isset($condition["optional_servechannel_type"]) && $condition["optional_servechannel_type"] != ""){
            $select->where("optional_servechannel_type = ?",$condition["optional_servechannel_type"]);
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