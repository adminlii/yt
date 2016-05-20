<?php
class Service_AfterShipTrackings extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_AfterShipTrackings|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_AfterShipTrackings();
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
    public static function update($row, $value, $field = "id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "id")
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
    public static function getByField($value, $field = 'id', $colums = "*")
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
        
              'E0'=>'id',
              'E1'=>'company_code',
              'E2'=>'user_account',
              'E3'=>'created_at',
              'E4'=>'updated_at',
              'E5'=>'tracking_number',
              'E6'=>'slug',
              'E7'=>'active',
              'E8'=>'custom_fields',
              'E9'=>'customer_name',
              'E10'=>'origin_country_iso3',
              'E11'=>'destination_country_iso3',
              'E12'=>'emails',
              'E13'=>'smses',
              'E14'=>'expected_delivery',
              'E15'=>'order_id',
              'E16'=>'order_id_path',
              'E17'=>'shipment_type',
              'E18'=>'signed_by',
              'E19'=>'source',
              'E20'=>'tag',
              'E21'=>'title',
              'E22'=>'tracked_count',
              'E23'=>'unique_token',
        );
        return $row;
    }

}