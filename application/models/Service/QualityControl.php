<?php
class Service_QualityControl extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_QualityControl|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_QualityControl();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $row['qc_add_time']=date('Y-m-d H:i:s');
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "qc_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "qc_id")
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
    public static function getByField($value, $field = 'qc_id', $colums = "*")
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
        
              'E0'=>'qc_id',
              'E1'=>'qc_code',
              'E2'=>'receiving_id',
              'E3'=>'warehouse_id',
              'E4'=>'receiving_code',
              'E5'=>'product_barcode',
              'E6'=>'product_id',
              'E7'=>'customer_code',
              'E8'=>'customer_id',
              'E9'=>'qc_operator_id',
              'E10'=>'qc_quantity',
              'E11'=>'qc_received_quantity',
              'E12'=>'qc_quantity_sellable',
              'E13'=>'qc_quantity_unsellable',
              'E14'=>'qc_status',
              'E15'=>'lc_code',
              'E16'=>'qc_note',
              'E17'=>'qc_add_time',
              'E18'=>'qc_update_time',
              'E19'=>'rd_id',
              'E20'=>'qc_finish_time',
              'E21'=>'qc_type',
        );
        return $row;
    }

}