<?php
class Table_AtdExtraserviceKind
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_AtdExtraserviceKind();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_AtdExtraserviceKind();
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
    public function update($row, $value, $field = "extra_service_kind")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "extra_service_kind")
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
    public function getByField($value, $field = 'extra_service_kind', $colums = "*")
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
        
        if(isset($condition["extra_service_cnname"]) && $condition["extra_service_cnname"] != ""){
            $select->where("extra_service_cnname = ?",$condition["extra_service_cnname"]);
        }
        if(isset($condition["extra_service_enname"]) && $condition["extra_service_enname"] != ""){
            $select->where("extra_service_enname = ?",$condition["extra_service_enname"]);
        }
        if(isset($condition["extra_service_group"]) && $condition["extra_service_group"] != ""){
            $select->where("extra_service_group = ?",$condition["extra_service_group"]);
        }
        if(isset($condition["extra_service_note"]) && $condition["extra_service_note"] != ""){
            $select->where("extra_service_note = ?",$condition["extra_service_note"]);
        }
        if(isset($condition["extra_service_webvisible"]) && $condition["extra_service_webvisible"] != ""){
            $select->where("extra_service_webvisible = ?",$condition["extra_service_webvisible"]);
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