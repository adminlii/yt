<?php
class Table_CsiServechannel
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CsiServechannel();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CsiServechannel();
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
    public function update($row, $value, $field = "server_channelid")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "server_channelid")
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
    public function getByField($value, $field = 'server_channelid', $colums = "*")
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
        
        if(isset($condition["server_id"]) && $condition["server_id"] != ""){
            $select->where("server_id = ?",$condition["server_id"]);
        }
        if(isset($condition["formal_code"]) && $condition["formal_code"] != ""){
            $select->where("formal_code = ?",$condition["formal_code"]);
        }
        if(isset($condition["server_product_code"]) && $condition["server_product_code"] != ""){
            $select->where("server_product_code = ?",$condition["server_product_code"]);
        }
        if(isset($condition["server_channel_cnname"]) && $condition["server_channel_cnname"] != ""){
            $select->where("server_channel_cnname = ?",$condition["server_channel_cnname"]);
        }
        if(isset($condition["server_channel_enname"]) && $condition["server_channel_enname"] != ""){
            $select->where("server_channel_enname = ?",$condition["server_channel_enname"]);
        }
        if(isset($condition["servicer_id"]) && $condition["servicer_id"] != ""){
            $select->where("servicer_id = ?",$condition["servicer_id"]);
        }
        if(isset($condition["server_channel_enable"]) && $condition["server_channel_enable"] != ""){
            $select->where("server_channel_enable = ?",$condition["server_channel_enable"]);
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