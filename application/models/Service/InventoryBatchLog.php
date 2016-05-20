<?php
class Service_InventoryBatchLog extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_InventoryBatchLog|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_InventoryBatchLog();
        }
        return self::$_modelClass;
    }
    
    /**
     * 批次日志
     * @author solar
     * @param array $ibRow
     * @param int $before
     * @param int $after
     * @param string $note
     * @return boolean
     */
    public static function log($ibRow, $before, $after, $note) {
    	$row['company_code'] = $ibRow['company_code'];
    	$row['lc_code'] = $ibRow['lc_code'];
    	$row['product_id'] = $ibRow['product_id'];
    	$row['product_barcode'] = $ibRow['product_barcode'];
    	$row['supplier_id'] = $ibRow['supplier_id'];
    	$row['warehouse_id'] = $ibRow['warehouse_id'];
    	$row['ib_id'] = $ibRow['ib_id'];
    	$row['receiving_code'] = $ibRow['receiving_code'];
    	$row['reference_no'] = $ibRow['reference_no'];
    	$row['application_code'] = $ibRow['application_code'];
    	$row['ibl_note'] = $note;
    	$row['ibl_quantity_before'] = $before;
    	$row['ibl_quantity_after'] = $after;
    	$row['user_id'] = Service_User::getUserId();
    	$row['ibl_ip'] = Common_Common::getIP();
    	$row['ibl_add_time'] = date('Y-m-d H:i:s');
    	return self::add($row);
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
    public static function update($row, $value, $field = "ibl_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ibl_id")
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
    public static function getByField($value, $field = 'ibl_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
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
        
              'E0'=>'ibl_id',
              'E1'=>'lc_code',
              'E2'=>'product_id',
              'E3'=>'product_barcode',
              'E4'=>'supplier_id',
              'E5'=>'warehouse_id',
              'E6'=>'ib_id',
              'E7'=>'receiving_code',
              'E8'=>'reference_no',
              'E9'=>'application_code',
              'E10'=>'ibl_note',
              'E11'=>'ibl_quantity_before',
              'E12'=>'ibl_quantity_after',
              'E13'=>'user_id',
              'E14'=>'ibl_ip',
              'E15'=>'ibl_add_time',
        	  'E16'=>'company_code'
        );
        return $row;
    }

}