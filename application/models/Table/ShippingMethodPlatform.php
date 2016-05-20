<?php
class Table_ShippingMethodPlatform
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ShippingMethodPlatform();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ShippingMethodPlatform();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function add($row)
    {
    	$row['add_time'] = date('Y-m-d H:i:s');
    	$row['update_time'] = date('Y-m-d H:i:s');
        return $this->_table->insert($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($row, $value, $field = "shipping_method_id")
    {
    	$row['update_time'] = date('Y-m-d H:i:s');
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "shipping_method_id")
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
    public function getByField($value, $field = 'shipping_method_id', $colums = "*")
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

        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["company_code_arr"]) && !empty($condition["company_code_arr"])){
            $select->where("company_code in (?)",$condition["company_code_arr"]);
        }
        if(isset($condition["name_cn"]) && $condition["name_cn"] != ""){
            $select->where("name_cn = ?",$condition["name_cn"]);
        }
        if(isset($condition["name_en"]) && $condition["name_en"] != ""){
            $select->where("name_en = ?",$condition["name_en"]);
        }
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["shipping_method_code"]) && $condition["shipping_method_code"] != ""){
            $select->where("shipping_method_code = ?",$condition["shipping_method_code"]);
        }
        if(isset($condition["short_code"]) && $condition["short_code"] != ""){
            $select->where("short_code = ?",$condition["short_code"]);
        }
        if(isset($condition["site"]) && $condition["site"] != ""){
            $select->where("site = ?",$condition["site"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["platform_shipping_mark"]) && $condition["platform_shipping_mark"] != ""){
            $select->where("platform_shipping_mark = ?",$condition["platform_shipping_mark"]);
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