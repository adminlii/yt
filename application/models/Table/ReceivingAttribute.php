<?php
class Table_ReceivingAttribute
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingAttribute();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingAttribute();
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
    public function update($row, $value, $field = "ra_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ra_id")
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
    public function getByField($value, $field = 'ra_id', $colums = "*")
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
        
        if(isset($condition["receiving_id"]) && $condition["receiving_id"] != ""){
            $select->where("receiving_id = ?",$condition["receiving_id"]);
        }
        if(isset($condition["receiving_code"]) && $condition["receiving_code"] != ""){
            $select->where("receiving_code = ?",$condition["receiving_code"]);
        }
        if(isset($condition["refercence_form_id"]) && $condition["refercence_form_id"] != ""){
            $select->where("refercence_form_id = ?",$condition["refercence_form_id"]);
        }
        if(isset($condition["ie_port"]) && $condition["ie_port"] != ""){
            $select->where("ie_port = ?",$condition["ie_port"]);
        }
        if(isset($condition["form_type"]) && $condition["form_type"] != ""){
            $select->where("form_type = ?",$condition["form_type"]);
        }
        if(isset($condition["traf_name"]) && $condition["traf_name"] != ""){
            $select->where("traf_name = ?",$condition["traf_name"]);
        }
        if(isset($condition["wrap_type"]) && $condition["wrap_type"] != ""){
            $select->where("wrap_type = ?",$condition["wrap_type"]);
        }
        if(isset($condition["traf_mode"]) && $condition["traf_mode"] != ""){
            $select->where("traf_mode = ?",$condition["traf_mode"]);
        }
        if(isset($condition["trade_mode"]) && $condition["trade_mode"] != ""){
            $select->where("trade_mode = ?",$condition["trade_mode"]);
        }
        if(isset($condition["trans_mode"]) && $condition["trans_mode"] != ""){
            $select->where("trans_mode = ?",$condition["trans_mode"]);
        }
        if(isset($condition["conta_id"]) && $condition["conta_id"] != ""){
            $select->where("conta_id = ?",$condition["conta_id"]);
        }
        if(isset($condition["conta_model"]) && $condition["conta_model"] != ""){
            $select->where("conta_model = ?",$condition["conta_model"]);
        }
        if(isset($condition["pack_no"]) && $condition["pack_no"] != ""){
            $select->where("pack_no = ?",$condition["pack_no"]);
        }
        if(isset($condition["conta_wt"]) && $condition["conta_wt"] != ""){
            $select->where("conta_wt = ?",$condition["conta_wt"]);
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