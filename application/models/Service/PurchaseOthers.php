<?php
class Service_PurchaseOthers extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PurchaseOrders|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PurchaseOrders();
        }
        return self::$_modelClass;
    }

    /**
     * 撤销PO单业务核心方法
     * @param unknown_type $paramsPo
     * @throws Exception
     * @return multitype:number string multitype:string  |multitype:number multitype:string  NULL
     */
    public static function revocationStatus($paramsPo = array(),$userId=""){
    	
    		$return = array();
    		$date = date('Y-m-d H:i:s');
     		//循环更新状态
    		foreach ($paramsPo as $key=>$val){
    			$db = Common_Common::getAdapter();
    			$db->beginTransaction();
    			try {
    				//获取明细主键
    				$paramPoId = $val['po_id'];
    				$poStatus = Service_PurchaseOrders::getByField($paramPoId,"po_id","po_status");
    				if (!empty($val['po_id'])) {
    					unset($val['po_id']);
    				}
    				 
    				//查询PO头信息
    				$poHead = Service_PurchaseOrders::getByField($paramPoId);
    				
    				if(empty($poHead)){
    					throw new Exception('采购单'.$val["po_code"].'不存在');
    				}
    				 
    				/*
    				 * update单头信息
    				 */
    				if(!Service_PurchaseOrders::update($val, $paramPoId)){
    					throw new Exception('采购单'.$val["po_code"].'更新状态为“撤销”失败！');
    				}
    				
    				//查询审批的PO单下所有商品的数量
    				$poProduct = Service_PurchaseOrderProduct::getByFieldJoinLeftPro($paramPoId);
    				if(!empty($poProduct)){
    					
    					foreach($poProduct as $ky=>$vl){
    						
    						/*
    						 * 修改采购单明细，明细状态po_status、和最新更新时间
    						 */
    						if(!Service_PurchaseOrderProduct::update(
    							array("po_status"=>$val["po_status"],"pop_update_time"=>$date), 
    							$vl["pop_id"])
    						){
    							throw new Exception('更新采购单'.$val["po_code"].'明细失败！');
    						};
    						
                            /*
                             * 更新计划库存
                             * 
                            */
    						$updateWhere = array("warehouse_id"=>$vl["warehouse_id"],"pi_product_id"=>$vl["product_id"]);
							$rows = Service_PurchaseInventory::getByCondition($updateWhere);
							$qty = 0;
							$updateFiled = array();
							//如果po单的状态是“待确认” 则需要修改计划库存的创建中的库存数量，如果是“交货确认” 则需要修改审核代采购数量
							if($poStatus["po_status"] == "1"){
								$qty = $rows[0]["qty_create"] - $vl["qty_expected"];
								$updateFiled = array("qty_create"=>$qty);
								
							}
							if($poStatus["po_status"] == "2"){
								$qty = $rows[0]["qty_release"] - $vl["qty_eta"];
								$updateFiled = array("qty_release"=>$qty);
							}
							
							Service_PurchaseInventory::updateQty($updateFiled, $updateWhere);
    					}
    				
    				}
    				 
    				//日志记录
    				$rowLog = array(
    						"pol_ref_no"=>$val["po_code"],
    						"pol_aciton_content"=>"撤销采购单",
    						"pol_action_operator"=>$userId,
    						"pol_action_date"=>$date,
    				);
    				$log = new Service_PurchaseOrdersLog();
    				$log->add($rowLog);
    				$db->commit();
    				$return[] = array(
    						'state' => 1,
    						'message'=>array('Success.'),
    						'errorMessage' => ''
    				);
    			} catch (Exception $e) {
		    		$db->rollBack();
		    		$return[] = array(
    				'state' => 0,
    				'message'=>array('Fail.'),
    				'errorMessage' => $e->getMessage()
    				);
    			}
    			
    		}
    	
    		return $return;
    }
    
    /**
     * 强制完成采购单
     * @param unknown_type $paramsPo
     * @param unknown_type $userId
     * @param unknown_type $poCode
     */
    public static function forcedComplete($paramPoId = "",$note=""){
    	$date = date('Y-m-d H:i:s');
    	$userId = Service_User::getUserId();//当前操作用户
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	$return = array();
    	$paramsPo = array();
    	try {
    		//获取明细主键
//     		$paramPoId = $paramsPo['po_id'];
//     		if (!empty($paramsPo['po_id'])) {
//     			unset($paramsPo['po_id']);
//     		}
    		$paramsStatus = "8";//完成状态
    		$paramsPo["po_status"] = $paramsStatus;
    		$paramsPo["po_update_time"] = $date;
    			
    		//查询PO头信息
    		$poHead = Service_PurchaseOrders::getByField($paramPoId);
    	
    		if(empty($poHead)){
    			throw new Exception('采购单不存在');
    		}
    		
    		$poCode = $poHead["po_code"];
    		
    		//查看是否有ASN号
    		$asnCode = Service_Receiving::getByField($poCode,"po_code","receiving_code");
    		if(empty($asnCode)){
    			throw new Exception('未找到采购单'.$poCode.'入库单号，请检查采购单状态是否可以强制完成！');
    		}
    		//判断状态是“完成”或者之后的状态，则不必进行强制完成
    		if($poHead["po_status"]>=8){
    			throw new Exception('采购单'.$poCode.'状态不符合强制完成要求！');
    		}
    		
    			
    		/*
    		 * update单头信息
    		*/
    		if(!Service_PurchaseOrders::update($paramsPo, $paramPoId)){
    			throw new Exception('采购单'.$poCode.'更新状态为“完成”失败！');
    		}
    	
    		//查询审批的PO单下所有商品的数量
    		$poProduct = Service_PurchaseOrderProduct::getByFieldJoinLeftPro($paramPoId);
    		if(!empty($poProduct)){
    			foreach($poProduct as $ky=>$vl){
    				/*
    				 * 修改采购单明细，明细状态po_status、和最新更新时间
    				*/
    				if(!Service_PurchaseOrderProduct::update(
    						array("po_status"=>$paramsPo["po_status"],"pop_update_time"=>$date),
    						$vl["pop_id"])
    				){
    					throw new Exception('更新采购单'.$poCode.'明细失败！');
    				};
    	
    			}
    	
    		}
    		
    		//ASN强制完成
    		$obj = new Service_ReceivingProcess();
    		
    		$result = $obj->forceComplete($asnCode, $note);
    		
    		if($result["state"] == 0){
    			throw new Exception("ASN错误：".$result["message"]);
    		}
    		//日志记录
    		$rowLog = array(
    				"pol_ref_no"=>$poCode,
    				"pol_aciton_content"=>"强制完成采购单",
    				"pol_action_operator"=>$userId,
    				"pol_action_date"=>$date,
    		);
    		$log = new Service_PurchaseOrdersLog();
    		$log->add($rowLog);
    		$db->commit();
    		$return = array(
    				'state' => 1,
    				'message'=>array('Success.'),
    				'errorMessage' => ''
    		);
    	} catch (Exception $e) {
    		$db->rollBack();
    		$return = array(
    				'state' => 0,
    				'message'=>array('Fail.'),
    				'errorMessage' => $e->getMessage()
    		);
    	}
    	
    	return $return;
    }
    
    /**
     * 创建采购批量导入产品sku
     * @param unknown_type $fileName
     * @param unknown_type $filePath
     * @param unknown_type $customerCode
     * @param unknown_type $customerId
     */
    public static function importPurchaseProduct($fileName,$filePath,$customerCode = 'EC001',$customerId = '1'){
    	$result = array('state' => 1, 'data' => array(), 'message' => array());
    	$fileData = Common_UploadData::readUploadFile($fileName, $filePath);
    	if (!isset($fileData[1]) || !is_array($fileData[1])) {
    		$result["state"] = "0";
    		$result['message'] = array('上传失败，无法解析文件内容;');
    		return $result;
    	}
    	
    	$productKey = self::purchaseProductKey();
    	
    	$productData = array();
    	foreach($fileData as $key=>$val){
    		//计算每条记录的列数必须足够3个
    		if (count($val) < 2) {
    			$result['message'][] = '第 ' . $key . ' 行,' . '数据异常.';
    			continue;
    		}
    		
    		//清除空值，过滤客户上传空数据
    		$filerArr = array_filter($val);
    		foreach ($productKey as $ok => $ov) {
    			$productData[$key][$ok] = isset($filerArr[$ok]) ? $filerArr[$ok] : '';
    		}
    		
    		if (empty( $val['SKU'])) {
    			$result['message'][] = '第 ' . $key . ' 行,' . 'SKU 不能为空！';
    			continue;
    		}
//     		if (empty( $val['unitPrice'])) {
//     			$result['message'][] = '第 ' . $key . ' 行,' . 'unitPrice (单价)不能为空！';
//     			continue;
//     		}
    		if (empty( $val['quantity'])) {
    			$result['message'][] = '第 ' . $key . ' 行,' . 'quantity (数量)不能为空！';
    			continue;
    		}
//     		if (empty( $val['currency'])) {
//     			$result['message'][] = '第 ' . $key . ' 行,' . 'currency (币种)不能为空！';
//     			continue;
//     		}
    		$productData[$key]["sku"] = $val['SKU'];
//     		$productData[$key]["unitPrice"] = $val['unitPrice'];
    		$productData[$key]["quantity"] = $val['quantity'];
//     		$productData[$key]["currency"] = $val['currency'];
    	}
    	
    	if(!empty($result['message'])){
    		$result["state"] = "0";
    	}
    	
    	$result["data"] = $productData;
    	
    	return $result;
    }
    
    public static function purchaseProductKey()
    {
    	$relationKeys = array(
    			'sku' => '',
//     			'unitPrice' => '',
    			'quantity' => '',
//     			'currency' => '',
    	);
    	return $relationKeys;
    }
    
    /**
     * 批量导入采购产品信息时，检查产品是否与供应商已经关联
     */
    public static function purchaseCheckProductChange($productArray = array(),$supplier = ""){
    	$userId = Service_User::getUserId();
    	$return = array("isRelevance"=>"");
    	$editeSupplierProduct = array();
    	$supplierProduct = Service_SupplierProduct::getByConditionJoinProduct(
    			array("supplier_id"=>$supplier,"sp_supplier_product_code"=>$productArray["sku"]),
    			array('supplier_id','sp_supplier_product_code','sp_last_price','sp_supplier_sku','currency_code'),0,1,"");
    	if(empty($supplierProduct)){
    		$return["isRelevance"] = "N";
    		$return["product_id"] = "";
    		$return["product_title"] = "";
    		$return["sp_supplier_sku"] = "";
    		$return["unitPrice"] = "";
    		$return["currency"] = "";
    	}else{
    		$return["isRelevance"] = "Y";
    		$return["product_id"] = $supplierProduct[0]["product_id"];
    		$return["product_title"] = $supplierProduct[0]["product_title"];
    		$return["sp_supplier_sku"] = $supplierProduct[0]["sp_supplier_sku"];
    		$return["unitPrice"] = $supplierProduct[0]["sp_last_price"];
    		$return["currency"] = $supplierProduct[0]["currency_code"];
    		//如果产品信息已经与供应商关联，检查单价 ，如果单价改变，则需要维护到供应商产品表
//     		if($productArray["unitPrice"] != $supplierProduct[0]["sp_last_price"]){
//     			$editeSupplierProduct[] = array(
//     					"sp_supplier_product_code"=>$productArray["sku"],
//     					"supplier_id"=>$supplier,
//     					"sp_last_price"=>$productArray["unitPrice"],
//     			);
//     		}
    	}
    	
//     	if(!empty($editeSupplierProduct)){
//     		try {
//     			//维护供应商产品信息
//     			Service_PurchaseOrders::eidtSupplierProduct($editeSupplierProduct,$userId);
//     		} catch (Exception $e) {
    			
//     		}
//     	}
    	
    	return $return;
    }
    
//     /**
//      * 
//      * @param unknown_type $poCodeArray
//      */
//     public static function mergerPurchase($poHead = array(),$poDetail = array()){
//     	$return = array();
//     	$date = date('Y-m-d H:i:s');
//     	if(empty($poHead) && empty($poDetail)){
//     		return $return;
//     	}
//     	try {
//     		/*
//     		 * 1、基础数据校验
//     		*/
//     		foreach($poHead as $key=>$val){
//     			if($val["po_status"] != 1){
//     				 throw new Exception("采购单："+$val['po_code']+'状态不是"待确认"，不能进行合并！');
//     			}
//     		}
    		 
//     		/*
//     		 * 2、合并单头
//     		*/
//     		$poNew = $poHead[0];
    		
//     		//要进行删除的po_id
//     		$po_idArray = array();
// 	    	for($i = 1;$i< count($poHead); $i++){
// 	    		$tem_array = $poHead[$i];
// 	    		$poNew["payable_amount"] =$poNew["payable_amount"] + $tem_array["payable_amount"];
// 	    		$po_idArray[] = $tem_array["po_id"];
// 			}
// 			$poNew["po_update_time"] = $date;
			
//     		/*
//     		 * 3、合并单身
//     		*/
// 			for($i = 1;$i< count($poDetail); $i++){
					 
// 				for($i = 1;$i< count($poDetail); $i++){
					
// 				}
// 			}
			
			
// 			$detail_update_temp = array();
// 			$detail_delete_temp = array();
// 			foreach($poDetail as $dKey=>$dVal){
// 				if($dVal["po_id"] == $poNew["po_id"]){
// 					$detail_update_temp[] = $dVal;
// 					continue;
// 				}
// 				$detail_delete_temp[] = $dVal;
// 			}
			
			
//     		/*
//     		 * 4、删除被合并的采购单
//     		*/
    		
//     	} catch (Exception $e) {
    		
//     	}
//     }
    

    
    
    
    
    
    
    
}