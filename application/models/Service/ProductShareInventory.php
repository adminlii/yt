<?php
class Service_ProductShareInventory extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductShareInventory|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductShareInventory();
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
    public static function update($row, $value, $field = "psi_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "psi_id")
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
    public static function getByField($value, $field = 'psi_id', $colums = "*")
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
        
              'E0'=>'psi_id',
              'E1'=>'pi_id',
              'E2'=>'company_code',
              'E3'=>'product_id',
              'E4'=>'product_sku',
              'E5'=>'warehouse_id',
              'E6'=>'psi_shared',
              'E7'=>'psi_sharing',
              'E8'=>'psi_stopped_share',
              'E9'=>'psi_borrowed',
              'E10'=>'add_time',
              'E11'=>'update_time',
        );
        return $row;
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByWarehouseProduct($warehouse_id = '', $product_id = '', $colums = "*")
    {
    	$condition = array("warehouse_id" => $warehouse_id, "product_id" => $product_id);
    	
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
     * 停止共享
     * @param string $psi_id
     * @param string $quantity
     */
    public function stopShareTransaction($psi_id = '', $quantity = 0) {
    	
    	$return = array("state" => 0, "message" => "", "code" => "");
    	
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	
    	try{
    		$userId = Service_User::getUserId();
    		$date = date('Y-m-d H:i:s');
    	
    		// 仓库
    		$warehouse = Common_DataCache::getWarehouseSimple();
    		
    		// 数量不能大于共享中数量
    		$shareInventoryRow = $this->getByField($psi_id);
    		if($shareInventoryRow['psi_sharing'] < $quantity) {
    			throw new Exception(Ec::Lang('quantityError'));
    		}
    		
    		// 更新库存
    		$row = array(
    				'product_id' => $shareInventoryRow['product_id'],
    				'quantity' => $quantity,
    				'operationType' => "3",
    				'warehouse_id' => $shareInventoryRow['warehouse_id'],
    				'application_code' => 'stopShare', //操作类型
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
    				'customer_code' => $shareInventoryRow['company_code'],
    				'product_sku' => $shareInventoryRow['product_sku'],
    				'quantity' => $val['quantity'],
    				'warehouse_code' => $warehouse[$shareInventoryRow['warehouse_id']],
    		);
    		$objApi = new Common_ThirdPartWmsAPI();
    		$result = $objApi->cancelShareInventory($shareInventoryArr);
    		
    		//     		print_r($result);die;
    		
    		if($result['ask'] == 'Failure') {
    			throw new Exception($result['error']['errorMsg']);
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
    

    /**
     * 强制退还
     * @param string $psi_id
     * @param string $quantity
     */
    public function stopBorrowTransaction($psi_id = '', $quantity = 0) {
    	 
    	$return = array("state" => 0, "message" => "", "code" => "");
    	 
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	 
    	try{
    		$userId = Service_User::getUserId();
    		$date = date('Y-m-d H:i:s');
    		 
    		// 仓库
    		$warehouse = Common_DataCache::getWarehouseSimple();
    
    		// 数量不能大于借用中数量
    		$shareInventoryRow = $this->getByField($psi_id);
    		if($shareInventoryRow['psi_borrowed'] < $quantity) {
    			throw new Exception(Ec::Lang('quantityError'));
    		}
    
    		// 更新库存
    		$row = array(
    				'product_id' => $shareInventoryRow['product_id'],
    				'quantity' => $quantity,
    				'operationType' => "3",
    				'warehouse_id' => $shareInventoryRow['warehouse_id'],
    				'application_code' => 'stopShare', //操作类型
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
    				'customer_code' => $shareInventoryRow['company_code'],
    				'product_sku' => $shareInventoryRow['product_sku'],
    				'quantity' => $val['quantity'],
    				'warehouse_code' => $warehouse[$shareInventoryRow['warehouse_id']],
    		);
    		$objApi = new Common_ThirdPartWmsAPI();
    		$result = $objApi->cancelShareInventory($shareInventoryArr);
    
    		//     		print_r($result);die;
    
    		if($result['ask'] == 'Failure') {
    			throw new Exception($result['error']['errorMsg']);
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

    /**
     * 库存借用
     * @param string $psi_id
     * @param string $quantity
     * @param string $company_code
     */
    public function borrowTransaction($psi_id = '', $quantity = 0, $company_code = '') {
    	 
    	$return = array("state" => 0, "message" => "", "code" => "");
    	 
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	 
    	try{
    		$userId = Service_User::getUserId();
    		$date = date('Y-m-d H:i:s');
    		
    		// 仓库
    		$warehouse = Common_DataCache::getWarehouseSimple();
    		
    		// 数量不能大于共享中数量
    		$shareInventoryRow = $this->getByField($psi_id);
    		if($shareInventoryRow['psi_sharing'] < $quantity) {
    			throw new Exception(Ec::Lang('quantityError'));
    		}
    		
    		// 生成单号
    		$code = Common_GetNumbers::getCode("ProductBorrow","","PB");
    		 
    		// 借用主表
    		$borrowRow = array(
    				'company_code' => $company_code,
    				'from_company_code' => $shareInventoryRow['company_code'],
    				'warehouse_id' => $shareInventoryRow['warehouse_id'],
    				'pb_code' => $code,
    				'add_time' => $date,
    				'update_time' => $date,
    				'creator_id' => $userId,
    				'modifier_id' => $userId,
    		);
    		
    		// 借用明细
    		$borrowDetail = array(
    				'product_id' => $shareInventoryRow['product_id'],
    				'product_sku' => $shareInventoryRow['product_sku'],
    				'quantity' => $quantity,
    		);
    		 
    		$pbId = Service_ProductBorrow::add($borrowRow);
    		if(empty($pbId)) {
    			$return['errorMsg'][] = Ec::Lang('operationFail');
    			return;
    		}
    		
    		$shareDetail['pb_id'] = $pbId;
    		Service_ProductBorrowDetail::add($borrowDetail);
    
    		// 更新分享库存
    		$row = array(
    				'product_id' => $shareInventoryRow['product_id'],
    				'quantity' => $quantity,
    				'operationType' => "2",
    				'warehouse_id' => $shareInventoryRow['warehouse_id'],
    				'application_code' => 'borrow', //操作类型
    				'reference_no' => $code, // 单号
    		);
    		 
    		$shareInventory = new Process_ShareInventory();
    		$result = $shareInventory->update($row);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    		
    		// 更新借用库存
    		$row = array(
    				'product_id' => $shareInventoryRow['product_id'],
    				'quantity' => $quantity,
    				'company_code' => $company_code,
    				'operationType' => "1",
    				'warehouse_id' => $shareInventoryRow['warehouse_id'],
    				'application_code' => 'borrow', //操作类型
    				'reference_no' => $code, // 单号
    		);
    		 
    		$borrowInventory = new Process_BorrowInventory();
    		$result = $borrowInventory->update($row);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    		
    		// 调整WMS库存数量
    		// 库存更新对象
    		$shareInventoryArr = array();
    		$shareInventoryArr[] = array(
    				'customer_code' => $shareInventoryRow['company_code'],
    				'product_sku' => $shareInventoryRow['product_sku'],
    				'quantity' => $quantity,
    				'to_customer_code' => $company_code,
    				'reference_no' => $code, // 单号
    				'warehouse_code' => $warehouse[$shareInventoryRow['warehouse_id']],
    		);
//     		print_r($shareInventoryArr);die;
    		
    		$objApi = new Common_ThirdPartWmsAPI();
    		$result = $objApi->borrowShareInventory($shareInventoryArr);
    		
//     		print_r($result);die;
    		
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