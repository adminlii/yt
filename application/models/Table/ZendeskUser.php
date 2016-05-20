<?php
class Table_ZendeskUser
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ZendeskUser();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ZendeskUser();
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
    public function update($row, $value, $field = "id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "id")
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
    public function getByField($value, $field = 'id', $colums = "*")
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

        if(isset($condition["id_arr"]) && is_array($condition["id_arr"])&&!empty($condition["id_arr"])){
            $select->where("id in (?)",$condition["id_arr"]);
        }
        
        if(isset($condition["url"]) && $condition["url"] != ""){
            $select->where("url = ?",$condition["url"]);
        }
        if(isset($condition["name"]) && $condition["name"] != ""){
            $select->where("name = ?",$condition["name"]);
        }
        if(isset($condition["email"]) && $condition["email"] != ""){
            $select->where("email = ?",$condition["email"]);
        }
        if(isset($condition["created_at"]) && $condition["created_at"] != ""){
            $select->where("created_at = ?",$condition["created_at"]);
        }
        if(isset($condition["updated_at"]) && $condition["updated_at"] != ""){
            $select->where("updated_at = ?",$condition["updated_at"]);
        }
        if(isset($condition["time_zone"]) && $condition["time_zone"] != ""){
            $select->where("time_zone = ?",$condition["time_zone"]);
        }
        if(isset($condition["phone"]) && $condition["phone"] != ""){
            $select->where("phone = ?",$condition["phone"]);
        }
        if(isset($condition["photo"]) && $condition["photo"] != ""){
            $select->where("photo = ?",$condition["photo"]);
        }
        if(isset($condition["locale_id"]) && $condition["locale_id"] != ""){
            $select->where("locale_id = ?",$condition["locale_id"]);
        }
        if(isset($condition["locale"]) && $condition["locale"] != ""){
            $select->where("locale = ?",$condition["locale"]);
        }
        if(isset($condition["organization_id"]) && $condition["organization_id"] != ""){
            $select->where("organization_id = ?",$condition["organization_id"]);
        }
        if(isset($condition["role"]) && $condition["role"] != ""){
            $select->where("role = ?",$condition["role"]);
        }
        if(isset($condition["verified"]) && $condition["verified"] != ""){
            $select->where("verified = ?",$condition["verified"]);
        }
        if(isset($condition["external_id"]) && $condition["external_id"] != ""){
            $select->where("external_id = ?",$condition["external_id"]);
        }
        if(isset($condition["tags"]) && $condition["tags"] != ""){
            $select->where("tags = ?",$condition["tags"]);
        }
        if(isset($condition["alias"]) && $condition["alias"] != ""){
            $select->where("alias = ?",$condition["alias"]);
        }
        if(isset($condition["active"]) && $condition["active"] != ""){
            $select->where("active = ?",$condition["active"]);
        }
        if(isset($condition["shared"]) && $condition["shared"] != ""){
            $select->where("shared = ?",$condition["shared"]);
        }
        if(isset($condition["shared_agent"]) && $condition["shared_agent"] != ""){
            $select->where("shared_agent = ?",$condition["shared_agent"]);
        }
        if(isset($condition["last_login_at"]) && $condition["last_login_at"] != ""){
            $select->where("last_login_at = ?",$condition["last_login_at"]);
        }
        if(isset($condition["signature"]) && $condition["signature"] != ""){
            $select->where("signature = ?",$condition["signature"]);
        }
        if(isset($condition["details"]) && $condition["details"] != ""){
            $select->where("details = ?",$condition["details"]);
        }
        if(isset($condition["notes"]) && $condition["notes"] != ""){
            $select->where("notes = ?",$condition["notes"]);
        }
        if(isset($condition["custom_role_id"]) && $condition["custom_role_id"] != ""){
            $select->where("custom_role_id = ?",$condition["custom_role_id"]);
        }
        if(isset($condition["moderator"]) && $condition["moderator"] != ""){
            $select->where("moderator = ?",$condition["moderator"]);
        }
        if(isset($condition["ticket_restriction"]) && $condition["ticket_restriction"] != ""){
            $select->where("ticket_restriction = ?",$condition["ticket_restriction"]);
        }
        if(isset($condition["only_private_comments"]) && $condition["only_private_comments"] != ""){
            $select->where("only_private_comments = ?",$condition["only_private_comments"]);
        }
        if(isset($condition["restricted_agent"]) && $condition["restricted_agent"] != ""){
            $select->where("restricted_agent = ?",$condition["restricted_agent"]);
        }
        if(isset($condition["suspended"]) && $condition["suspended"] != ""){
            $select->where("suspended = ?",$condition["suspended"]);
        }
        if(isset($condition["user_fields"]) && $condition["user_fields"] != ""){
            $select->where("user_fields = ?",$condition["user_fields"]);
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