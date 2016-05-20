<?php
class Service_WarehouseArea extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_WarehouseArea|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_WarehouseArea();
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
    public static function update($row, $value, $field = "wa_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "wa_id")
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
    public static function getByField($value, $field = 'wa_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('waCode'), "value" =>$val["wa_code"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('waName'), "value" =>$val["wa_name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('waNameEn'), "value" =>$val["wa_name_en"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('waType'), "value" =>$val["wa_type"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('category'), "value" =>$val["pc_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('warehouseCode'), "value" =>$val["warehouse_id"], "regex" => array("require","integer",));
        $validateArr[] = array("name" =>EC::Lang('status'), "value" =>$val["wa_status"], "regex" => array("positive"));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'wa_id',
              'E1'=>'wa_code',
              'E2'=>'wa_name',
              'E3'=>'wa_name_en',
              'E4'=>'wa_type',
              'E5'=>'pc_id',
              'E6'=>'warehouse_id',
              'E7'=>'wa_status',
              'E8'=>'wa_add_time',
              'E9'=>'wa_update_time',
        	  'E10'=>'company_code'
        );
        return $row;
    }

}