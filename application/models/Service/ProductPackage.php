<?php
class Service_ProductPackage extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductPackage|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductPackage();
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
    public static function update($row, $value, $field = "pp_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pp_id")
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
    public static function getByField($value, $field = 'pp_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('warehouse'), "value" =>$val["warehouse_id"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('barcode'), "value" =>$val["pp_barcode"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('quantity'), "value" =>$val["pp_quantity"], "regex" => array("require","positive",));
        $validateArr[] = array("name" =>EC::Lang('cost'), "value" =>$val["pp_cost"], "regex" => array("require","positive",));
        $validateArr[] = array("name" =>EC::Lang('price'), "value" =>$val["pp_price"], "regex" => array("require","positive",));
        $validateArr[] = array("name" =>EC::Lang('currencyCode'), "value" =>$val["currency_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('nameEn'), "value" =>$val["pp_name_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('nameCn'), "value" =>$val["pp_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('length'), "value" =>$val["pp_length"], "regex" => array("require","positive",));
        $validateArr[] = array("name" =>EC::Lang('width'), "value" =>$val["pp_width"], "regex" => array("require","positive",));
        $validateArr[] = array("name" =>EC::Lang('height'), "value" =>$val["pp_height"], "regex" => array("require","positive",));
        $validateArr[] = array("name" =>EC::Lang('weight'), "value" =>$val["pp_weight"], "regex" => array("require","positive",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'pp_id',
              'E1'=>'warehouse_id',
              'E2'=>'customer_code',
              'E3'=>'customer_id',
              'E4'=>'pp_barcode',
              'E5'=>'pp_type',
              'E6'=>'pp_status',
              'E7'=>'pp_quantity',
              'E8'=>'pp_cost',
              'E9'=>'pp_price',
              'E10'=>'currency_code',
              'E11'=>'pp_name_en',
              'E12'=>'pp_name',
              'E13'=>'pp_length',
              'E14'=>'pp_width',
              'E15'=>'pp_height',
              'E16'=>'pp_weight',
              'E17'=>'pp_add_time',
              'E18'=>'pp_update_time',
        );
        return $row;
    }

}