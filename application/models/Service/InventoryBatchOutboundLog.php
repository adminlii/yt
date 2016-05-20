<?php
class Service_InventoryBatchOutboundLog extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_InventoryBatchOutboundLog|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_InventoryBatchOutboundLog();
        }
        return self::$_modelClass;
    }
    
    /**
     * 日志
     * @author solar
     * @param array $iboRow
     * @param string $note
     * @return boolean
     */
    public static function log($iboRow, $note) {
    	$row['lc_code'] = $iboRow['lc_code'];
    	$row['ibo_id'] = $iboRow['ibo_id'];
    	$row['ib_id'] = $iboRow['ib_id'];
    	$row['reference_no'] = $iboRow['reference_no'];
    	$row['application_code'] = $iboRow['application_code'];
    	$row['ibol_quantity'] = $iboRow['ibo_quantity'];
    	$row['user_id'] = Service_User::getUserId();
    	$row['ibol_ip'] = Common_Common::getIP();
    	$row['ibol_add_time'] = date('Y-m-d H:i:s');
    	$row['ibol_note'] = $note;
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
    public static function update($row, $value, $field = "ibol_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ibol_id")
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
    public static function getByField($value, $field = 'ibol_id', $colums = "*")
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
        
              'E0'=>'ibol_id',
              'E1'=>'lc_code',
              'E2'=>'ibo_id',
              'E3'=>'ib_id',
              'E4'=>'reference_no',
              'E5'=>'application_code',
              'E6'=>'ibol_quantity',
              'E7'=>'user_id',
              'E8'=>'ibol_ip',
              'E9'=>'ibol_add_time',
              'E10'=>'ibol_note',
        );
        return $row;
    }

}