<?php
class Service_ProductBorrowInventory extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductBorrowInventory|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductBorrowInventory();
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
    public static function update($row, $value, $field = "pbi_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pbi_id")
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
    public static function getByField($value, $field = 'pbi_id', $colums = "*")
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
        
              'E0'=>'pbi_id',
              'E1'=>'company_code',
              'E2'=>'from_company_code',
              'E3'=>'product_id',
              'E4'=>'product_sku',
              'E5'=>'warehouse_id',
              'E6'=>'pbi_borrowed',
              'E7'=>'pbi_reserved',
              'E8'=>'pbi_shipped',
              'E9'=>'pbi_unused',
              'E10'=>'pbi_return',
              'E11'=>'add_time',
              'E12'=>'update_time',
        );
        return $row;
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByWarehouseProduct($warehouse_id = '', $product_id = '', $company_code = '', $colums = "*")
    {
    	$condition = array("warehouse_id" => $warehouse_id, "product_id" => $product_id, "company_code" => $company_code);
    	 
    	$row = self::getByCondition($condition, $colums, 0, 0, array());
    	 
    	if(!empty($row)) {
    		return $row[0];
    	}
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByInnerProductCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByInnerProductCondition($condition, $type, $pageSize, $page, $order);
    }
    

    /**
     * 退回共享
     * @param string $pbi_id
     * @param string $quantity
     */
    public function sendBack($pbi_id = '', $quantity = 0, $note) {
    	 
    	$return = array("state" => 0, "message" => "", "code" => "");
    	 
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	 
    	try{
    		$userId = Service_User::getUserId();
    		$date = date('Y-m-d H:i:s');
    		
    		// 仓库
    		$warehouse = Common_DataCache::getWarehouseSimple();
    		 
    		// 数量不能大于借用中数量
    		$borrowInventoryRow = $this->getByField($pbi_id);
    		if($borrowInventoryRow['pbi_unused'] < $quantity) {
    			throw new Exception(Ec::Lang('quantityError'));
    		}
    
    		// 更新库存
    		$row = array(
    				'product_id' => $borrowInventoryRow['product_id'],
    				'quantity' => $quantity,
    				'operationType' => "4",
    				'warehouse_id' => $borrowInventoryRow['warehouse_id'],
    				'application_code' => 'sendBack', //操作类型
    				'note' => $note
    		);
    		 
    		$borrowInventory = new Process_BorrowInventory();
    		$result = $borrowInventory->update($row);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    		
    		// 更新分享库存
    		$row = array(
    				'product_id' => $borrowInventoryRow['product_id'],
    				'quantity' => $quantity,
    				'operationType' => "4",
    				'warehouse_id' => $borrowInventoryRow['warehouse_id'],
    				'application_code' => 'sendBack', //操作类型
    				'note' => $note,
    		);
    		 
    		$shareInventory = new Process_ShareInventory();
    		$result = $shareInventory->update($row);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    		
    		// 调整WMS库存数量
    		// 库存更新对象
    		$shareInventoryArr = array();
    		$shareInventoryArr[] = array(
    				'customer_code' => $borrowInventoryRow['company_code'],
    				'product_sku' => $borrowInventoryRow['product_sku'],
    				'quantity' => $quantity,
    				'to_customer_code' => $borrowInventoryRow['from_company_code'],
    				'warehouse_code' => $warehouse[$borrowInventoryRow['warehouse_id']],
    		);
//     		    		print_r($shareInventoryArr);die;
    		
    		$objApi = new Common_ThirdPartWmsAPI();
    		$result = $objApi->sendBackShareInventory($shareInventoryArr);
    		
//     		    		print_r($result);die;
    		
    		if($result['ask'] == 'Failure') {
    			throw new Exception($result['errorMsg'][0]);
    		}
    		 
    		$db->commit();
    		 
    		$return['state'] = 1;
    		$return['message'] = Ec::Lang('operationSuccess');
    	}catch(Exception $e){
    		$db->rollback();
    		$return['message'] = $e->getMessage();
    	}
    	 
    	return $return;
    }
}