<?php
class Service_InventoryBatch extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_InventoryBatch|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_InventoryBatch();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "ib_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ib_id")
    {
        $model = self::getModelInstance();
        return $model->delete($value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'ib_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }
    
    public static function getByWhere($where) {
    	$model = self::getModelInstance();
    	return $model->getByWhere($where);
    }
    
    public static function getForUpdate($ib_id) {
    	$model = self::getModelInstance();
    	return $model->getForUpdate($ib_id);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }

    public static function getLeftJoinGroupLcCodeSkuByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getLeftJoinGroupLcCodeSkuByCondition($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionPurchase($productId = "",$wareHouse = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionPurchase($productId,$wareHouse);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionProduct($productId = "",$wareHouse = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionProduct($productId,$wareHouse);
    }
    
    public static function getByGroupLcCodeAndProductIdCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByGroupLcCodeAndProductIdCondition($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * 下架中转单列表
     * @param unknown_type $condition
     * @param unknown_type $type
     * @param unknown_type $pageSize
     * @param unknown_type $page
     * @param unknown_type $order
     * @return Ambigous <multitype:, string>
     */
    public static function getByConditionLeftJoin($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = ""){
    	$model = self::getModelInstance();
    	return $model->getByConditionLeftJoin($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * 下架中转单
     * @param unknown_type $condition
     * @param unknown_type $type
     * @param unknown_type $pageSize
     * @param unknown_type $page
     * @param unknown_type $order
     * @return Ambigous <multitype:, string>
     */
    public static function getDownInventoryByCondition($value, $field = 'ib_id', $colums = "*"){
    	$model = self::getModelInstance();
    	return $model->getDownInventoryByCondition($value, $field, $colums);
    }
    
    public static function getDownInventoryInfo($value, $field = 'ib_id', $colums = "*"){
    	$model = self::getModelInstance();
    	return $model->getByField($value, $field, $colums);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        return  Common_Validator::formValidator($validateArr);
    }
    

    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'ib_id',
              'E1'=>'lc_code',
              'E2'=>'product_id',
              'E3'=>'box_code',
              'E4'=>'product_barcode',
              'E5'=>'reference_no',
              'E6'=>'application_code',
              'E7'=>'supplier_id',
              'E8'=>'warehouse_id',
              'E9'=>'receiving_code',
              'E10'=>'receiving_id',
              'E11'=>'lot_number',
              'E12'=>'ib_status',
              'E13'=>'ib_hold_status',
              'E14'=>'ib_quantity',
              'E15'=>'ib_fifo_time',
              'E16'=>'ib_note',
              'E17'=>'ib_add_time',
              'E18'=>'ib_update_time',
              'E19'=>'ib_type',
        	  'E20'=>'company_code'
        );
        return $row;
    }


    /**
     * @desc 导出库存
     * @param array $params
     * @return array
     */
    public static function exportExcel($params=array())
    {
        $validateArr = $error = array();

        return  Common_Validator::formValidator($validateArr);
    }


    public static function listTakeStockLocation($condition) {
        $result = $deprecated = array();
        $model = self::getModelInstance();
        $list = $model->listTakeStockLocation($condition);
        foreach($list as &$row) {
            $key = $row['lc_code'].'_'.$row['product_barcode'];
            if(isset($deprecated[$key])) continue;
            if($row['ib_status']==0 || $row['ib_hold_status']!=0 || $row['ibo_id']!=null) {
                $deprecated[$key] = 1;
                continue;
            }
            if(isset($result[$key])) {
                $result[$key]['ib_quantity'] += $row['ib_quantity'];
            } else {
                $result[$key] = $row;
            }
        }
        return $result;
    }

    public static function listByWhere($where) {
        $model = self::getModelInstance();
        return $model->listByWhere($where);
    }

    public static function updateByField($row, $field) {
        $model = self::getModelInstance();
        return $model->updateByField($row, $field);
    }

}