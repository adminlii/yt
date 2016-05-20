<?php
class Table_CtsCustomerIssuekind
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_CtsCustomerIssuekind();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_CtsCustomerIssuekind();
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
    public function update($row, $value, $field = "issuekind_code")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "issuekind_code")
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
    public function getByField($value, $field = 'issuekind_code', $colums = "*")
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
        
        if(isset($condition["issuekind_cnname"]) && $condition["issuekind_cnname"] != ""){
            $select->where("issuekind_cnname = ?",$condition["issuekind_cnname"]);
        }
        if(isset($condition["issuekind_content"]) && $condition["issuekind_content"] != ""){
            $select->where("issuekind_content = ?",$condition["issuekind_content"]);
        }
        if(isset($condition["isu_interactionsign"]) && $condition["isu_interactionsign"] != ""){
            $select->where("isu_interactionsign = ?",$condition["isu_interactionsign"]);
        }
        if(isset($condition["issue_enable"]) && $condition["issue_enable"] != ""){
            $select->where("issue_enable = ?",$condition["issue_enable"]);
        }
        if(isset($condition["issue_class_code"]) && $condition["issue_class_code"] != ""){
            $select->where("issue_class_code = ?",$condition["issue_class_code"]);
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