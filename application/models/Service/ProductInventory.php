<?php
class Service_ProductInventory extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductInventory|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductInventory();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $row['pi_add_time']=date('Y-m-d H:i:s');
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "pi_id")
    {
        $row['pi_update_time']=date('Y-m-d H:i:s');
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pi_id")
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
    public static function getByField($value, $field = 'pi_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }
    
    public static function getByWhProduct($warehouseId=0, $productId =0, $colums = "*") {
    	$model = self::getModelInstance();
    	return $model->getByWhProduct($warehouseId, $productId, $colums);
    }
    
    public static function getForUpdate($pi_id) {
    	$model = self::getModelInstance();
    	return $model->getForUpdate($pi_id);
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
    public static function getByConditionJoinProduct($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionJoinProduct($condition, $type, $pageSize, $page, $order);
    }
    public static function getInventoryBatch($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = ""){
    	$model = self::getModelInstance();
    	return $model->getInventoryBatch($condition,$type,$pageSize,$page,$orderBy);
    }

    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'pi_id',
              'E1'=>'product_barcode',
              'E2'=>'product_id',
              'E3'=>'warehouse_id',
              'E4'=>'pi_onway',
              'E5'=>'pi_pending',
              'E6'=>'pi_sellable',
              'E7'=>'pi_unsellable',
              'E8'=>'pi_reserved',
              'E9'=>'pi_shipped',
              'E10'=>'pi_hold',
              'E11'=>'pi_add_time',
              'E12'=>'pi_update_time',
        	  'E13'=>'company_code'
        );
        return $row;
    }

}