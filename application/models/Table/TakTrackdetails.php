<?php
class Table_TakTrackdetails
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_TakTrackdetails();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_TakTrackdetails();
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
    public function update($row, $value, $field = "trk_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "trk_id")
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
    public function getByField($value, $field = 'trk_id', $colums = "*")
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
        
        if(isset($condition["tbs_id"]) && $condition["tbs_id"] != ""){
            $select->where("tbs_id = ?",$condition["tbs_id"]);
        }
        if(isset($condition["track_code"]) && $condition["track_code"] != ""){
            $select->where("track_code = ?",$condition["track_code"]);
        }
        if(isset($condition["track_source"]) && $condition["track_source"] != ""){
            $select->where("track_source = ?",$condition["track_source"]);
        }
        if(isset($condition["track_occur_date"]) && $condition["track_occur_date"] != ""){
            $select->where("track_occur_date = ?",$condition["track_occur_date"]);
        }
        if(isset($condition["track_area_description"]) && $condition["track_area_description"] != ""){
            $select->where("track_area_description = ?",$condition["track_area_description"]);
        }
        if(isset($condition["track_create_date"]) && $condition["track_create_date"] != ""){
            $select->where("track_create_date = ?",$condition["track_create_date"]);
        }
        if(isset($condition["track_create_person"]) && $condition["track_create_person"] != ""){
            $select->where("track_create_person = ?",$condition["track_create_person"]);
        }
        if(isset($condition["pass_back_date"]) && $condition["pass_back_date"] != ""){
            $select->where("pass_back_date = ?",$condition["pass_back_date"]);
        }
        
//         echo $select->__toString();
        
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
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getByConditionJoinAttach($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinInner("tak_trackattach", "tak_trackattach.trk_id = tak_trackdetails.trk_id", array('track_description'));
        $select->joinLeft("tak_trackcode", "tak_trackcode.track_code = tak_trackdetails.track_code", array('track_cnname','track_enname'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/
        
        if(isset($condition["tbs_id"]) && $condition["tbs_id"] != ""){
            $select->where("tbs_id = ?",$condition["tbs_id"]);
        }
        if(isset($condition["track_code"]) && $condition["track_code"] != ""){
            $select->where("track_code = ?",$condition["track_code"]);
        }
        if(isset($condition["track_source"]) && $condition["track_source"] != ""){
            $select->where("track_source = ?",$condition["track_source"]);
        }
        if(isset($condition["track_occur_date"]) && $condition["track_occur_date"] != ""){
            $select->where("track_occur_date = ?",$condition["track_occur_date"]);
        }
        if(isset($condition["track_area_description"]) && $condition["track_area_description"] != ""){
            $select->where("track_area_description = ?",$condition["track_area_description"]);
        }
        if(isset($condition["track_create_date"]) && $condition["track_create_date"] != ""){
            $select->where("track_create_date = ?",$condition["track_create_date"]);
        }
        if(isset($condition["track_create_person"]) && $condition["track_create_person"] != ""){
            $select->where("track_create_person = ?",$condition["track_create_person"]);
        }
        if(isset($condition["pass_back_date"]) && $condition["pass_back_date"] != ""){
            $select->where("pass_back_date = ?",$condition["pass_back_date"]);
        }
        
//         echo $select->__toString();
        
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