<?php
class Table_EubAddress
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_EubAddress();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_EubAddress();
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
    public function update($row, $value, $field = "eadd_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "eadd_id")
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
    public function getByField($value, $field = 'eadd_id', $colums = "*")
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
        
        if(isset($condition["ref_id"]) && $condition["ref_id"] != ""){
            $select->where("ref_id = ?",$condition["ref_id"]);
        }
        if(isset($condition["pname"]) && $condition["pname"] != ""){
            $select->where("pname = ?",$condition["pname"]);
        }
        if(isset($condition["pcompany"]) && $condition["pcompany"] != ""){
            $select->where("pcompany = ?",$condition["pcompany"]);
        }
        if(isset($condition["pcountry"]) && $condition["pcountry"] != ""){
            $select->where("pcountry = ?",$condition["pcountry"]);
        }
        if(isset($condition["pprovince"]) && $condition["pprovince"] != ""){
            $select->where("pprovince = ?",$condition["pprovince"]);
        }
        if(isset($condition["pcity"]) && $condition["pcity"] != ""){
            $select->where("pcity = ?",$condition["pcity"]);
        }
        if(isset($condition["pdis"]) && $condition["pdis"] != ""){
            $select->where("pdis = ?",$condition["pdis"]);
        }
        if(isset($condition["pstreet"]) && $condition["pstreet"] != ""){
            $select->where("pstreet = ?",$condition["pstreet"]);
        }
        if(isset($condition["pzip"]) && $condition["pzip"] != ""){
            $select->where("pzip = ?",$condition["pzip"]);
        }
        if(isset($condition["pmobile"]) && $condition["pmobile"] != ""){
            $select->where("pmobile = ?",$condition["pmobile"]);
        }
        if(isset($condition["ptel"]) && $condition["ptel"] != ""){
            $select->where("ptel = ?",$condition["ptel"]);
        }
        if(isset($condition["pemail"]) && $condition["pemail"] != ""){
            $select->where("pemail = ?",$condition["pemail"]);
        }
        if(isset($condition["rname_en"]) && $condition["rname_en"] != ""){
            $select->where("rname_en = ?",$condition["rname_en"]);
        }
        if(isset($condition["rcompany_en"]) && $condition["rcompany_en"] != ""){
            $select->where("rcompany_en = ?",$condition["rcompany_en"]);
        }
        if(isset($condition["rcountry_en"]) && $condition["rcountry_en"] != ""){
            $select->where("rcountry_en = ?",$condition["rcountry_en"]);
        }
        if(isset($condition["rprovince_en"]) && $condition["rprovince_en"] != ""){
            $select->where("rprovince_en = ?",$condition["rprovince_en"]);
        }
        if(isset($condition["rcity_en"]) && $condition["rcity_en"] != ""){
            $select->where("rcity_en = ?",$condition["rcity_en"]);
        }
        if(isset($condition["rdis_en"]) && $condition["rdis_en"] != ""){
            $select->where("rdis_en = ?",$condition["rdis_en"]);
        }
        if(isset($condition["rstreet_en"]) && $condition["rstreet_en"] != ""){
            $select->where("rstreet_en = ?",$condition["rstreet_en"]);
        }
        if(isset($condition["rzip_en"]) && $condition["rzip_en"] != ""){
            $select->where("rzip_en = ?",$condition["rzip_en"]);
        }
        if(isset($condition["rmobile_en"]) && $condition["rmobile_en"] != ""){
            $select->where("rmobile_en = ?",$condition["rmobile_en"]);
        }
        if(isset($condition["remail_en"]) && $condition["remail_en"] != ""){
            $select->where("remail_en = ?",$condition["remail_en"]);
        }
        if(isset($condition["rname"]) && $condition["rname"] != ""){
            $select->where("rname = ?",$condition["rname"]);
        }
        if(isset($condition["rcompany"]) && $condition["rcompany"] != ""){
            $select->where("rcompany = ?",$condition["rcompany"]);
        }
        if(isset($condition["rcountry"]) && $condition["rcountry"] != ""){
            $select->where("rcountry = ?",$condition["rcountry"]);
        }
        if(isset($condition["rprovince"]) && $condition["rprovince"] != ""){
            $select->where("rprovince = ?",$condition["rprovince"]);
        }
        if(isset($condition["rcity"]) && $condition["rcity"] != ""){
            $select->where("rcity = ?",$condition["rcity"]);
        }
        if(isset($condition["rdis"]) && $condition["rdis"] != ""){
            $select->where("rdis = ?",$condition["rdis"]);
        }
        if(isset($condition["rstreet"]) && $condition["rstreet"] != ""){
            $select->where("rstreet = ?",$condition["rstreet"]);
        }
        if(isset($condition["rzip"]) && $condition["rzip"] != ""){
            $select->where("rzip = ?",$condition["rzip"]);
        }
        if(isset($condition["rmobile"]) && $condition["rmobile"] != ""){
            $select->where("rmobile = ?",$condition["rmobile"]);
        }
        if(isset($condition["remail"]) && $condition["remail"] != ""){
            $select->where("remail = ?",$condition["remail"]);
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