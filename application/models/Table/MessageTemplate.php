<?php
class Table_MessageTemplate
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_MessageTemplate();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_MessageTemplate();
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
    public function update($row, $value, $field = "template_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "template_id")
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
    public function getByField($value, $field = 'template_id', $colums = "*")
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
        
        if(isset($condition["template_title"]) && $condition["template_title"] != ""){
            $select->where("template_title = ?",$condition["template_title"]);
        }
        if(isset($condition["template_name"]) && $condition["template_name"] != ""){
            $select->where("template_name = ?",$condition["template_name"]);
        }
        if(isset($condition["template_short_name"]) && $condition["template_short_name"] != ""){
            $select->where("template_short_name = ?",$condition["template_short_name"]);
        }
        if(isset($condition["template_group_id"]) && $condition["template_group_id"] != ""){
            $select->where("template_group_id = ?",$condition["template_group_id"]);
        }
        if(isset($condition["template_note"]) && $condition["template_note"] != ""){
            $select->where("template_note = ?",$condition["template_note"]);
        }
        if(isset($condition["template_type"]) && $condition["template_type"] != ""){
            $select->where("template_type = ?",$condition["template_type"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }

        if(isset($condition["template_short_name_like"]) && $condition["template_short_name_like"] != ""){//模糊查询
            $select->where("template_short_name like ?",'%'.$condition["template_short_name_like"].'%');
        }
        //
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
//             echo $sql;exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
    

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionInnerJoinContent($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        
        $opTable = new DbTable_MessageTemplateContent();
        $table1 = $opTable->info('name');
        if($type == 'count(*)'){
            $select->joinInner($table1, $table . '.template_id = ' . $table1 . '.template_id', null);
        }else{
            $select->joinInner($table1, $table . '.template_id = ' . $table1 . '.template_id', '*');
        }
        
        
        $select->where("1 =?", 1);
        /*CONDITION_START*/
    
        if(isset($condition["template_title"]) && $condition["template_title"] != ""){
            $select->where("template_title = ?",$condition["template_title"]);
        }
        if(isset($condition["template_name"]) && $condition["template_name"] != ""){
            $select->where("template_name = ?",$condition["template_name"]);
        }
        if(isset($condition["template_short_name"]) && $condition["template_short_name"] != ""){
            $select->where("template_short_name = ?",$condition["template_short_name"]);
        }
        if(isset($condition["template_group_id"]) && $condition["template_group_id"] != ""){
            $select->where("template_group_id = ?",$condition["template_group_id"]);
        }
        if(isset($condition["template_note"]) && $condition["template_note"] != ""){
            $select->where("template_note = ?",$condition["template_note"]);
        }
        if(isset($condition["template_type"]) && $condition["template_type"] != ""){
            $select->where("template_type = ?",$condition["template_type"]);
        }
        if(isset($condition["company_code"]) && $condition["company_code"] != ""){
            $select->where("company_code = ?",$condition["company_code"]);
        }
        if(isset($condition["status"]) && $condition["status"] != ""){
            $select->where("status = ?",$condition["status"]);
        }
    
        if(isset($condition["template_short_name_like"]) && $condition["template_short_name_like"] != ""){//模糊查询
            $select->where("template_short_name like ?",'%'.$condition["template_short_name_like"].'%');
        }
        
        if(isset($condition["template_note_like"]) && $condition["template_note_like"] != ""){
        	$select->where("template_note like ?",'%'.$condition["template_note_like"].'%');
        }
        
        //-------------------------------------------------
        if(isset($condition["language_code"]) && $condition["language_code"] != ""){//模糊查询
            $select->where($table1.".language_code = ?",$condition["language_code"]);
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
//                         echo $sql;exit;
            return $this->_table->getAdapter()->fetchAll($sql);
        }
    }
}