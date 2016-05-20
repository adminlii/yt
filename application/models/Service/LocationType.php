<?php
class Service_LocationType extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_LocationType|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_LocationType();
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
    public static function update($row, $value, $field = "lt_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "lt_id")
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
    public static function getByField($value, $field = 'lt_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('locationTypeCode'), "value" =>$val["lt_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('warehouse'), "value" =>$val["warehouse_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('width'), "value" =>$val["lt_width"], "regex" => array("require","positive1",));
        $validateArr[] = array("name" =>EC::Lang('length'), "value" =>$val["lt_length"], "regex" => array("require","positive1",));
        $validateArr[] = array("name" =>EC::Lang('height'), "value" =>$val["lt_height"], "regex" => array("require","positive1",));
        $validateArr[] = array("name" =>EC::Lang('volume'), "value" =>$val["lt_vol"], "regex" => array("require","positive1",));
        $validateArr[] = array("name" =>EC::Lang('status'), "value" =>$val["lt_status"], "regex" => array("positive"));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'lt_id',
              'E1'=>'lt_code',
              'E2'=>'lt_description',
              'E3'=>'warehouse_id',
              'E4'=>'lt_width',
              'E5'=>'lt_length',
              'E6'=>'lt_height',
              'E7'=>'lt_vol',
              'E8'=>'lt_status',
              'E9'=>'lt_add_time',
              'E10'=>'lt_update_time',
        	  'E11'=>'company_code'
        );
        return $row;
    }

}