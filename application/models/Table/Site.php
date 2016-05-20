<?php
class Table_Site
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_Site();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_Site();
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
    public function update($row, $value, $field = "site_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "site_id")
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
    public function getByField($value, $field = 'site_id', $colums = "*")
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
        
        if(isset($condition["site_name_en"]) && $condition["site_name_en"] != ""){
            $select->where("site_name_en = ?",$condition["site_name_en"]);
        }
        if(isset($condition["site_name_cn"]) && $condition["site_name_cn"] != ""){
            $select->where("site_name_cn = ?",$condition["site_name_cn"]);
        }
        if(isset($condition["site_note"]) && $condition["site_note"] != ""){
            $select->where("site_note = ?",$condition["site_note"]);
        }
        if(isset($condition["site_currency"]) && $condition["site_currency"] != ""){
            $select->where("site_currency = ?",$condition["site_currency"]);
        }
        if(isset($condition["site_language"]) && $condition["site_language"] != ""){
            $select->where("site_language = ?",$condition["site_language"]);
        }
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["site_code"]) && $condition["site_code"] != ""){
            $select->where("site_code = ?",$condition["site_code"]);
        }
        if(isset($condition["site_country"]) && $condition["site_country"] != ""){
            $select->where("site_country = ?",$condition["site_country"]);
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