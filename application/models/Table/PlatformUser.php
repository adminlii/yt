<?php
class Table_PlatformUser
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_PlatformUser();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_PlatformUser();
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
    public function update($row, $value, $field = "pu_id")
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
    public function delete($value, $field = "pu_id")
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
    public function getByField($value, $field = 'pu_id', $colums = "*")
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
        if(isset($condition["platform"]) && $condition["platform"] != ""){
            $select->where("platform = ?",$condition["platform"]);
        }
        if(isset($condition["user_account"]) && $condition["user_account"] != ""){
            $select->where("user_account = ?",$condition["user_account"]);
        }
        if(isset($condition["user_account_like"]) && $condition["user_account_like"] != ""){
        	$select->where("user_account like ?",'%' . $condition["user_account_like"] . '%');
        }

        if(isset($condition["user_account_arr"]) && !empty($condition["user_account_arr"])){
            $select->where("user_account in (?)",$condition["user_account_arr"]);
        }
        if(isset($condition["user_token"]) && $condition["user_token"] != ""){
            $select->where("user_token = ?",$condition["user_token"]);
        }
        if(isset($condition["refresh_token"]) && $condition["refresh_token"] != ""){
        	$select->where("refresh_token = ?",$condition["refresh_token"]);
        }
        if(isset($condition["app_key"]) && $condition["app_key"] != ""){
        	$select->where("app_key = ?",$condition["app_key"]);
        }
        if(isset($condition["app_signature"]) && $condition["app_signature"] != ""){
        	$select->where("app_signature = ?",$condition["app_signature"]);
        }
        if(isset($condition["short_name"]) && $condition["short_name"] != ""){
            $select->where("short_name = ?",$condition["short_name"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
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