<?php
class Table_ReceivingAbnormalDetail
{
    protected $_table = null;

    public function __construct()
    {
        $this->_table = new DbTable_ReceivingAbnormalDetail();
    }

    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    public static function getInstance()
    {
        return new Table_ReceivingAbnormalDetail();
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
    public function update($row, $value, $field = "rad_id")
    {
        $where = $this->_table->getAdapter()->quoteInto("{$field}= ?", $value);
        return $this->_table->update($row, $where);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete($value, $field = "rad_id")
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
    public function getByField($value, $field = 'rad_id', $colums = "*")
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
        
        if(isset($condition["ra_id"]) && $condition["ra_id"] != ""){
            $select->where("ra_id = ?",$condition["ra_id"]);
        }
        if(isset($condition["ra_code"]) && $condition["ra_code"] != ""){
            $select->where("ra_code = ?",$condition["ra_code"]);
        }
        if(isset($condition["rad_status"]) && $condition["rad_status"] != ""){
            $select->where("rad_status = ?",$condition["rad_status"]);
        }
        if(isset($condition["rad_status_in"]) && is_array($condition["rad_status_in"])){
            $select->where("rad_status in(?)",$condition["rad_status_in"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["qc_code"]) && $condition["qc_code"] != ""){
            $select->where("qc_code = ?",$condition["qc_code"]);
        }
        if(isset($condition["is_qc"]) && $condition["is_qc"] != ""){
            $select->where("is_qc = ?",$condition["is_qc"]);
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
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @return array|string
     */
    public function getJoinLeftProductByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = "")
    {
        $select = $this->_table->getAdapter()->select();
        $table = $this->_table->info('name');
        $select->from($table, $type);
        $select->joinLeft("product", "product.product_id=receiving_abnormal_detail.product_id",array('product_title','product_title_en'));
        $select->where("1 =?", 1);
        /*CONDITION_START*/

        if(isset($condition["ra_id"]) && $condition["ra_id"] != ""){
            $select->where("ra_id = ?",$condition["ra_id"]);
        }
        if(isset($condition["ra_code"]) && $condition["ra_code"] != ""){
            $select->where("ra_code = ?",$condition["ra_code"]);
        }
        if(isset($condition["rad_status"]) && $condition["rad_status"] != ""){
            $select->where("rad_status = ?",$condition["rad_status"]);
        }
        if(isset($condition["rad_status_in"]) && is_array($condition["rad_status_in"])){
            $select->where("rad_status in(?)",$condition["rad_status_in"]);
        }
        if(isset($condition["product_id"]) && $condition["product_id"] != ""){
            $select->where("receiving_abnormal_detail.product_id = ?",$condition["product_id"]);
        }
        if(isset($condition["product_barcode"]) && $condition["product_barcode"] != ""){
            $select->where("receiving_abnormal_detail.product_barcode = ?",$condition["product_barcode"]);
        }
        if(isset($condition["lc_code"]) && $condition["lc_code"] != ""){
            $select->where("lc_code = ?",$condition["lc_code"]);
        }
        if(isset($condition["qc_code"]) && $condition["qc_code"] != ""){
            $select->where("qc_code = ?",$condition["qc_code"]);
        }
        if(isset($condition["is_qc"]) && $condition["is_qc"] != ""){
            $select->where("receiving_abnormal_detail.is_qc = ?",$condition["is_qc"]);
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