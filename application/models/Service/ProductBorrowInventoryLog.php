<?php
class Service_ProductBorrowInventoryLog extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductBorrowInventoryLog|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductBorrowInventoryLog();
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
    public static function update($row, $value, $field = "pbil_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pbil_id")
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
    public static function getByField($value, $field = 'pbil_id', $colums = "*")
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
        
              'E0'=>'pbil_id',
              'E1'=>'pbi_id',
              'E2'=>'company_code',
              'E3'=>'from_company_code',
              'E4'=>'product_id',
              'E5'=>'product_sku',
              'E6'=>'warehouse_id',
              'E7'=>'pbi_borrowed',
              'E8'=>'pbi_reserved',
              'E9'=>'pbi_shipped',
              'E10'=>'pbi_unused',
              'E11'=>'pbi_return',
              'E12'=>'reference_no',
              'E13'=>'application_code',
              'E14'=>'pbil_type',
              'E15'=>'pbil_quantity',
              'E16'=>'from_type',
              'E17'=>'to_type',
              'E18'=>'add_time',
              'E19'=>'creator_id',
        );
        return $row;
    }

}