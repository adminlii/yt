<?php
class Service_ProductShare extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_ProductShare|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_ProductShare();
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
    public static function update($row, $value, $field = "ps_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ps_id")
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
    public static function getByField($value, $field = 'ps_id', $colums = "*")
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
        
              'E0'=>'ps_id',
              'E1'=>'company_code',
              'E2'=>'warehouse_id',
              'E3'=>'ps_status',
              'E4'=>'add_time',
              'E5'=>'update_time',
              'E6'=>'release_time',
              'E7'=>'creator_id',
              'E8'=>'verifier_id',
              'E9'=>'modifier_id',
        );
        return $row;
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByInnerDetailCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByInnerDetailCondition($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param array $params
     * @return array
     */
    public function create($productId = '', $quantity = '', $warehouseId = '') {
    	
    	// 分享数据
    	$productArr = array();
    	$productArr[] = array(
    			"product_id" => $productId, 
    			"quantity" => $quantity, 
    			"warehouse_id" => $warehouseId);
    
    	return $this->createTransaction($productArr);
    }
    

    /**
     * @param array $params
     * @return array
     */
    public function createTransaction($productArr = array()) {
    
    	$return = array(
    			"state" => 0,
    			"message" => "Fail.",
    			"errorMsg" => array()
    	);
//     	print_r($productArr);die;
    	if(empty($productArr)) {
    		return $return;
    	}
    
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try{
    		$userId = Service_User::getUserId();
    		$companyCode = Service_User::getUserCompanyCode();
    		$date = date('Y-m-d H:i:s');
    		 
    		// 仓库
    		$warehouse = Common_DataCache::getWarehouseSimple();
    		// 库存更新对象
    		$shareInventory = array();
    		foreach($productArr as $val) {
    			
	    		$productRow = Service_Product::getByField($val['product_id']);
	    		 
	    		// 生成单号
	    		$code = Common_GetNumbers::getCode("ProductShare","","PS");
	    		
	    		// 分享主表
	    		$shareRow = array(
	    				'company_code' => $companyCode,
	    				'warehouse_id' => $val['warehouse_id'],
	    				'ps_code' => $code,
	    				'add_time' => $date,
	    				'update_time' => $date,
	    				'creator_id' => $userId,
	    				'modifier_id' => $userId,
	    		);
	    		 
	    		// 分享明细
	    		$shareDetail = array(
	    				'product_id' => $val['product_id'],
	    				'product_sku' => $productRow['product_sku'],
	    				'quantity' => $val['quantity'],
	    		);
	    		
	    		$psId = $this->add($shareRow);
	    		if(empty($psId)) {
	    			throw new Exception(Ec::Lang('operationFail'));
	    		}
	    		 
	    		$shareDetail['ps_id'] = $psId;
	    		Service_ProductShareDetail::add($shareDetail);
	    		
	    		$shareInventory[] = array(
	    				'customer_code' => $companyCode,
	    				'product_sku' => $productRow['product_sku'],
	    				'quantity' => $val['quantity'],
	    				'reference_no' => $code,
	    				'warehouse_code' => $warehouse[$val['warehouse_id']],
	    		);
    		}
    			
    		// 调整WMS库存数量
    		$objApi = new Common_ThirdPartWmsAPI();
    		$result = $objApi->shareInventory($shareInventory);
//     		print_r($result);die; 
    		if($result['ask'] == 'Failure') {
    			throw new Exception($result['errorMsg'][0]);
    		}

    		// 同步WMS库存数据
    		$invParam = array(
    				'company_code' => $companyCode,
    				'warehouse_code' => $warehouse[$val['warehouse_id']],
    				'product_sku' => $productRow['product_sku'],
    		);
    		
    		$wmsProcess = new Common_ThirdPartWmsAPIProcess();
    		$result = $wmsProcess->syncInventory($invParam);
    		if($result['ask'] == 'Failure') {
    			throw new Exception($result['message']);
    		}
    		
    		$db->commit();
    		$return['state'] = "1";
    		$return['message'] = Ec::Lang('operationSuccess');
    	}catch(Exception $e){
    		$db->rollback();
    		$return['errorMsg'][] = $e->getMessage();
    	}
    
    	return $return;
    }
    
    // 审核
    public function auditTransaction($ps_id = '') {
    	 
    	$return = array("state" => 0, "message" => "", "errorMsg" => "");
    	 
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try{
    		$userId = Service_User::getUserId();
    		$companyCode = Service_User::getUserCompanyCode();
    		$date = date('Y-m-d H:i:s');
    
    		// 判断状态
    		$shareRow = $this->getByField($ps_id);
    
    		// 分享库存明细
    		$shareDetailRow = Service_ProductShareDetail::getByField($ps_id, 'ps_id');
    		
    		// 更新库存
    		$row = array(
    				'product_id' => $shareDetailRow['product_id'],
    				'quantity' => $shareDetailRow['quantity'],
    				'operationType' => "1",
    				'warehouse_id' => $shareRow['warehouse_id'],
    				'reference_no' => $shareRow['ps_code'], //操作单号
    				'application_code' => 'audit', //操作类型
    		);
    		
    		if($shareRow['ps_status'] != '0') {
    			throw new Exception($shareDetailRow['product_sku'] . ' ' . Ec::Lang('errorState'));
    		}
    
    		$shareInventory = new Process_ShareInventory();
    		$result = $shareInventory->update($row);
//     		print_r($result);die;
    		if($result['state'] == 0) {
    			throw new Exception($shareDetailRow['product_sku'] . ' ' . $result['error'][0]['errorMsg']);
    		}
    		 
    		// 更新分享表状态
    		$row = array(
    				'ps_status' => 1,
    				'release_time' => $date,
    				'verifier_id' => $userId,
    				'update_time' => $date,
    				'modifier_id' => $userId,
    		);
    		 
    		$this->update($row, $ps_id);
    		$db->commit();
    
    		$return['state'] = 1;
    		$return['message'] = Ec::Lang('operationSuccess');
    	}catch(Exception $e){
    		$db->rollback();
    		$return['message'] = $e->getMessage();
    	}
    	 
    	return $return;
    }
    
    // 审核
    public function cancelTransaction($ps_id = '') {
    
    	$return = array("state" => 0, "message" => "", "errorMsg" => "");
    
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try{
    		$userId = Service_User::getUserId();
    		$companyCode = Service_User::getUserCompanyCode();
    		$date = date('Y-m-d H:i:s');
    
    		// 仓库
    		$warehouse = Common_DataCache::getWarehouseSimple();
    		
    		// 判断状态
    		$shareRow = $this->getByField($ps_id);
    
    		// 分享库存明细
    		$shareDetailRow = Service_ProductShareDetail::getByField($ps_id, 'ps_id');
    
    		// 更新库存
    		$row = array(
    				'product_id' => $shareDetailRow['product_id'],
    				'quantity' => $shareDetailRow['quantity'],
    				'operationType' => "1",
    				'warehouse_id' => $shareRow['warehouse_id'],
    				'reference_no' => $shareRow['ps_code'], //操作单号
    				'application_code' => 'audit', //操作类型
    		);
    
    		if($shareRow['ps_status'] != '0') {
    			$return['errorMsg'] = $shareDetailRow['product_sku'] . ' ' . Ec::Lang('errorState');
    			throw new Exception();
    		}

    		// 更新分享表状态
    		$row = array(
    				'ps_status' => 2,
    				'release_time' => $date,
    				'verifier_id' => $userId,
    				'update_time' => $date,
    				'modifier_id' => $userId,
    		);
    		
    		// 库存更新对象
    		$shareInventory = array();
    		$shareInventory[] = array(
    				'customer_code' => $companyCode,
    				'product_sku' => $productRow['product_sku'],
    				'quantity' => $val['quantity'],
    				'reference_no' => $shareRow['ps_code'],
    				'warehouse_code' => $warehouse[$val['warehouse_id']],
    		);
    		
    		// 调整WMS库存数量
    		$objApi = new Common_ThirdPartWmsAPI();
    		$result = $objApi->cancelShareInventory($shareInventory);
    		
    		//     		print_r($result);die;
    		if($result['ask'] == 'Failure') {
    			$return['errorMsg'] = $result['errorMsg'];
    			throw new Exception();
    		}

    		$this->update($row, $ps_id);
    		$db->commit();
    
    		$return['state'] = 1;
    		$return['message'] = Ec::Lang('operationSuccess');
    	}catch(Exception $e){
    		$db->rollback();
    		$return['message'] = Ec::Lang('operationFail');
    	}
    
    	return $return;
    }
}