<?php
class Service_Product extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_Product|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_Product();
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
    public static function update($row, $value, $field = "product_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }
    
    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "product_id")
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
    public static function getByField($value, $field = 'product_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }


    /**
     * @param $value
     * @param string $field
     * @return array
     */
    public static function getByProduct($value, $field = 'product_id')
    {
        $model = self::getModelInstance();
        $result = $model->getByField($value, $field, '*');
        if (!empty($result)) {
            $lang=Ec::getLang(1);
            $result['title']=isset($result['product_title'.$lang])?$result['product_title'.$lang]:'';
            $result['category'] = Service_ProductCategory::getByField($result['pc_id'], 'pc_id');
            $result['category']['name']=isset($result['category']['pc_name'.$lang])?$result['category']['pc_name'.$lang]:'';
            if ($result['pce_id'] != '0') {
                $result['ebayCategory'] = Service_ProductCategoryEbay::getByField($result['pce_id'], 'pce_id');
            }
        }
        return $result;
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
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionForSku($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionForSku($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionLike($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionLike($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionLeftAttach($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionLeftAttach($condition, $type, $pageSize, $page, $order);
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
        
              'E0'=>'product_id',
              'E1'=>'product_sku',
              'E2'=>'product_barcode',
              'E3'=>'customer_code',
              'E4'=>'customer_id',
              'E5'=>'product_title_en',
              'E6'=>'product_title',
              'E7'=>'product_status',
              'E8'=>'product_receive_status',
              'E9'=>'pu_code',
              'E10'=>'product_length',
              'E11'=>'product_width',
              'E12'=>'product_height',
              'E13'=>'product_net_weight',
              'E14'=>'product_weight',
              'E15'=>'product_sales_value',
              'E16'=>'product_purchase_value',
              'E17'=>'product_declared_value',
              'E18'=>'product_is_qc',
              'E19'=>'product_barcode_type',
              'E20'=>'product_type',
              'E21'=>'pc_id',
              'E22'=>'pce_id',
              'E23'=>'product_add_time',
              'E24'=>'product_update_time',
              'E022'=>'product_category_ebay.pce_title',
        );
        return $row;
    }

    /**
     * 关联获取跟PRODUCT有关的所有表信息
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getAllByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getAllByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * 获取借用的产品
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getBorrowByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getBorrowByCondition($condition, $type, $pageSize, $page, $order);
    }

}