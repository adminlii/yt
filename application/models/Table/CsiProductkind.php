<?php
class Table_CsiProductkind
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsiProductkind();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsiProductkind();
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
    public function update($row, $value, $field = "product_code")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "product_code")
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
    public function getByField($value, $field = 'product_code', $colums = "*")
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
        
        if(isset($condition["product_cnname"]) && $condition["product_cnname"] != ""){
            $select->where("product_cnname = ?",$condition["product_cnname"]);
        }
        if(isset($condition["product_enname"]) && $condition["product_enname"] != ""){
            $select->where("product_enname = ?",$condition["product_enname"]);
        }
        if(isset($condition["product_groupcode"]) && $condition["product_groupcode"] != ""){
            $select->where("product_groupcode = ?",$condition["product_groupcode"]);
        }
        if(isset($condition["product_status"]) && $condition["product_status"] != ""){
            $select->where("product_status = ?",$condition["product_status"]);
        }
        if(isset($condition["product_trackstatus"]) && $condition["product_trackstatus"] != ""){
            $select->where("product_trackstatus = ?",$condition["product_trackstatus"]);
        }
        if(isset($condition["product_aging"]) && $condition["product_aging"] != ""){
            $select->where("product_aging = ?",$condition["product_aging"]);
        }
        if(isset($condition["product_operatenote"]) && $condition["product_operatenote"] != ""){
            $select->where("product_operatenote = ?",$condition["product_operatenote"]);
        }
        if(isset($condition["product_note"]) && $condition["product_note"] != ""){
            $select->where("product_note = ?",$condition["product_note"]);
        }
        if(isset($condition["tms_id"]) && $condition["tms_id"] != ""){
            $select->where("tms_id = ?",$condition["tms_id"]);
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