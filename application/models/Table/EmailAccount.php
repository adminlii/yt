<?php
class Table_EmailAccount
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EmailAccount();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EmailAccount();
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
    public function update($row, $value, $field = "ea_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "ea_id")
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
    public function getByField($value, $field = 'ea_id', $colums = "*")
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
        
        if(isset($condition["not_ea_id"]) && $condition["not_ea_id"] != ""){
        	$select->where("ea_id != ?",$condition["not_ea_id"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["ea_user_name"]) && $condition["ea_user_name"] != ""){
            $select->where("ea_user_name = ?",$condition["ea_user_name"]);
        }
        if(isset($condition["ea_user_name_like"]) && $condition["ea_user_name_like"] != ""){
        	$select->where("ea_user_name like ?",'%' . $condition["ea_user_name_like"] . '%');
        }
        
        if(isset($condition["ea_password"]) && $condition["ea_password"] != ""){
            $select->where("ea_password = ?",$condition["ea_password"]);
        }
        if(isset($condition["send_name"]) && $condition["send_name"] != ""){
            $select->where("send_name = ?",$condition["send_name"]);
        }
        if(isset($condition["email_address"]) && $condition["email_address"] != ""){
            $select->where("email_address = ?",$condition["email_address"]);
        }
        if(isset($condition["server_type"]) && $condition["server_type"] != ""){
            $select->where("server_type = ?",$condition["server_type"]);
        }
        if(isset($condition["imap_server"]) && $condition["imap_server"] != ""){
            $select->where("imap_server = ?",$condition["imap_server"]);
        }
        if(isset($condition["imap_port"]) && $condition["imap_port"] != ""){
            $select->where("imap_port = ?",$condition["imap_port"]);
        }
        if(isset($condition["smtp_server"]) && $condition["smtp_server"] != ""){
            $select->where("smtp_server = ?",$condition["smtp_server"]);
        }
        if(isset($condition["smtp_port"]) && $condition["smtp_port"] != ""){
            $select->where("smtp_port = ?",$condition["smtp_port"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
        if(isset($condition["user_id"]) && $condition["user_id"] != ""){
            $select->where("user_id = ?",$condition["user_id"]);
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