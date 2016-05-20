<?php
class Service_PurchaseOrderProduct extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PurchaseOrderProduct|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PurchaseOrderProduct();
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
    public static function update($row, $value, $field = "pop_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    public static function updateByProduct($row, $poId = 0, $productId = 0)
    {
        $model = self::getModelInstance();
        return $model->updateByProduct($row, $poId, $productId);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pop_id")
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
    public static function getByField($value, $field = 'po_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }
    
    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByFieldIn($value, $field = 'po_id', $colums = "*")
    {
    	$model = self::getModelInstance();
    	return $model->getByFieldIn($value, $field, $colums);
    }
    
    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getEtaConfirmInfo($value, $field = 'po_code', $colums = "*")
    {
    	$model = self::getModelInstance();
    	return $model->getEtaConfirmInfo($value, $field, $colums);
    }
    
    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByFieldProductDetail($value, $field = 'po_id', $colums = "*")
    {
    	$model = self::getModelInstance();
    	return $model->getByFieldProductDetail($value, $field, $colums);
    }
    
    public static function getByFieldPrintDetail($value, $field = 'po_id', $colums = "*"){   
    	$model = self::getModelInstance();
    	return $model->getByFieldPrintDetail($value, $field, $colums);
    }
    
    public static function getByFieldJoinLeft($value, $field = 'po_id', $colums = "*")
    {
    	$model = self::getModelInstance();
    	return $model->getByFieldJoinLeft($value, $field, $colums);
    }
    
    public static function getByFieldJoinLeftPro($value, $field = 'purchase_order_product.po_id', $colums = "*")
    {
    	$model = self::getModelInstance();
    	return $model->getByFieldJoinLeftPro($value, $field, $colums);
    }
    
    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }
    
    public static function getDoReceiveException($poId = ""){
    	$model = self::getModelInstance();
    	return $model->getDoReceiveException($poId);
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
    public static function getByConditionProductDetail($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionProductDetail($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionStatistics($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionStatistics($condition, $type, $pageSize, $page, $order);
    }

    /**
     * @desc 关联产品表
     * @return mixed
     */
    public static function getJoinInnerProductByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getJoinInnerProductByCondition($condition, $type, $pageSize, $page, $order);
    }
    
    public static function getAllExportPurchaseProduct($value, $field = 'purchase_order_product.po_id', $colums = "*")
    {
    	$model = self::getModelInstance();
    	return $model->getAllExportPurchaseProduct($value, $field, $colums);
    }
    
    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
//         $validateArr[] = array("name" =>EC::Lang('预期数量'), "value" =>$val["qty_expected"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('服务商数量'), "value" =>$val["qty_eta"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('总应付金额'), "value" =>$val["payable_amount"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('单价'), "value" =>$val["unit_price"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('运输方式'), "value" =>$val["shipping_method_id"], "regex" => array("require",));
        
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'po_id',
              'E1'=>'po_code',
              'E2'=>'po_status',
              'E3'=>'pop_id',
              'E4'=>'product_id',
              'E5'=>'qty_expected',
              'E6'=>'qty_receving',
              'E7'=>'payable_amount',
              'E8'=>'actually_amount',
              'E9'=>'unit_price',
              'E10'=>'shipping_method_id',
        	  'E11'=>'qty_eta',
        );
        return $row;
    }

}