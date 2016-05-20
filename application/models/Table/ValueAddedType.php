<?php
class Table_ValueAddedType
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ValueAddedType();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ValueAddedType();
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
    public function update($row, $value, $field = "vat_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "vat_id")
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
    public function getByField($value, $field = 'vat_id', $colums = "*")
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
        
        if(isset($condition["vat_code"]) && $condition["vat_code"] != ""){
            $select->where("vat_code = ?",$condition["vat_code"]);
        }
        if(isset($condition["vat_business_type"]) && $condition["vat_business_type"] != ""){
            $select->where("vat_business_type = ?",$condition["vat_business_type"]);
        }
        if(isset($condition["vat_status"]) && $condition["vat_status"] != ""){
            $select->where("vat_status = ?",$condition["vat_status"]);
        }
        if(isset($condition["vat_name_en"]) && $condition["vat_name_en"] != ""){
            $select->where("vat_name_en = ?",$condition["vat_name_en"]);
        }
        if(isset($condition["vat_name_cn"]) && $condition["vat_name_cn"] != ""){
            $select->where("vat_name_cn = ?",$condition["vat_name_cn"]);
        }
        if(isset($condition["vat_note"]) && $condition["vat_note"] != ""){
            $select->where("vat_note = ?",$condition["vat_note"]);
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