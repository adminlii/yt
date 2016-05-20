<?php
class Table_UserRightHeaderMap
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_UserRightHeaderMap();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_UserRightHeaderMap();
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
    public function update($row, $value, $field = "urhm_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "urhm_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->delete($where);
    }

    public function deleteByReferIdAndType($referId='',$type='')
    {
        if($referId=='' || $type=='' ){
            return false;
        }
        $where = $this->_table->getAdapter()->quoteInto("refer_id= ?", $referId);
        $where.= $this->_table->getAdapter()->quoteInto("urhm_type= ?", $type);
        return $this->_table->delete($where);
    }

    public function deleteByReferIdAndUrId($referId='',$urId=array(),$type='1')
    {
        if($referId=='' || empty($urId) ){
            return false;
        }
        $where = $this->_table->getAdapter()->quoteInto("refer_id= ?", $referId);
        $where.= $this->_table->getAdapter()->quoteInto(" and urhm_type= ?", $type);
        $where.= $this->_table->getAdapter()->quoteInto(" and ur_id in(?)", $urId);
        return $this->_table->delete($where);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public function getByField($value, $field = 'urhm_id', $colums = "*")
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
        
        if(isset($condition["ur_id"]) && $condition["ur_id"] != ""){
            $select->where("ur_id = ?",$condition["ur_id"]);
        }
        if(isset($condition["refer_id"]) && $condition["refer_id"] != ""){
            $select->where("refer_id = ?",$condition["refer_id"]);
        }
        if(isset($condition["urhm_type"]) && $condition["urhm_type"] != ""){
            $select->where("urhm_type = ?",$condition["urhm_type"]);
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

    /**
     * @param string $userId
     * @param string $positionId
     * @return array
     */
    public function getLeftUserRightByCondition($userId = '', $positionId = '', $orderBy = array('urhm_type desc', 'u_sort', 'um_sort'))
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, '*');
        $select->joinLeft('user_right', 'user_right.ur_id=' . $table . '.ur_id');
        $select->joinLeft('user_menu as um', 'um.um_id=user_right.um_id', array('um.um_sort as u_sort'));
        $select->joinLeft('user_menu', 'user_menu.um_id=um.parent_id', array('um_sort'));
        $select->where("1 =?", 1);
        $select->where("refer_id = ?", $userId)->where('urhm_type=1');
        $select->orWhere("refer_id = ?", $positionId)->where('urhm_type=0');
        $select->order($orderBy);
        $sql = $select->__toString();
        return $this->_table->getAdapter()->fetchAll($sql);
    }
}