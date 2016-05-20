<?php
class Service_ReturnOrders extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ReturnOrders|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ReturnOrders();
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
    public static function update($row, $value, $field = "ro_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ro_id")
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
    public static function getByField($value, $field = 'ro_id', $colums = "*")
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
                'E0' => 'ro_id',
                'E1' => 'receiving_code',
                'E2' => 'refrence_no_platform',
                'E3' => 'warehouse_id',
                'E4' => 'creater',
                'E5' => 'verifier',
                'E6' => 'expected_date',
                'E7' => 'ro_status',
                'E8' => 'ro_sync_status',
                'E9' => 'ro_process_type',
                'E10' => 'ro_create_type',
                'E11' => 'ro_desc',
                'E12' => 'ro_note',
                'E13' => 'ro_add_time',
                'E14' => 'ro_confirm_time',
                'E15' => 'ro_update_time',
                'E16' => 'ro_is_all',
                'E17' => 'ro_type',
                'E18' => 'ro_code',
                'E19' => 'company_code',                

                'E20'=>'company_code',
                'E21'=>'refrence_no_platform',
                'E22'=>'refrence_no_warehouse',
                'E23'=>'refrence_no',
        );
        return $row;
    }

}