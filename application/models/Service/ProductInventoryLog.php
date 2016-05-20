<?php
class Service_ProductInventoryLog extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductInventoryLog|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductInventoryLog();
        }
        return self::$_modelClass;
    }
    
    /**
     * 装袋日志
     * @author solar
     * @param int $product_id
     * @param int $pil_reserved
     * @param int $pil_shipped
     * @param int $pil_quantity
     * @param string $note
     * @return boolean
     */
    public static function bagLog($product_id, $pil_reserved, $pil_shipped, $pil_quantity, $note) {
    	 $row['product_id'] = $product_id;
    	 $row['warehouse_id'] = Service_User::getUserWarehouseId();
    	 $row['user_id'] = Service_User::getUserId();
    	 $row['pil_reserved'] = $pil_reserved;
    	 $row['pil_shipped'] = $pil_shipped;
    	 $row['pil_quantity'] = $pil_quantity;    	 
    	 $row['pil_add_time'] = date('Y-m-d H:i:s');
    	 $row['pil_ip'] = Common_Common::getIP();
    	 $row['pil_note'] = $note;
    	 return self::add($row);
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $row['pil_ip']=Common_Common::getIP();
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "pil_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pil_id")
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
    public static function getByField($value, $field = 'pil_id', $colums = "*")
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
        
              'E0'=>'pil_id',
              'E1'=>'product_id',
              'E2'=>'product_barcode',
              'E3'=>'warehouse_id',
              'E4'=>'reference_code',
              'E5'=>'user_id',
              'E6'=>'pil_onway',
              'E7'=>'pil_pending',
              'E8'=>'pil_sellable',
              'E9'=>'pil_unsellable',
              'E10'=>'pil_reserved',
              'E11'=>'pil_shipped',
              'E12'=>'from_it_code',
              'E13'=>'to_it_code',
              'E14'=>'pil_quantity',
              'E15'=>'application_code',
              'E16'=>'pil_add_time',
              'E17'=>'pil_note',
              'E18'=>'pil_ip',
        );
        return $row;
    }

}