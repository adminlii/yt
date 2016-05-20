<?php 
class Service_PurchaseMenuProcess
{
	/**
	 * 处理收货异常
	 * @param unknown_type $doType 
	 * @param unknown_type $po_id
	 * @param unknown_type $po_code
	 * 
	 */
	public static function doReceivingExcetption($doType = "",$po_id = "",$po_code = ""){
		$db = Common_Common::getAdapter();
		$db->beginTransaction();
		$return = array();
		try {
			/*
			 * 完成采购单
			 */
			//完成采购单状态
			$result_temp = self::completePurchase($po_id,"收货异常，强制完成采购单");
			if($result_temp["state"] == "0"){
				throw new Exception($result_temp["message"]);
			}
			$date = date('Y-m-d H:i:s');
			$userId = Service_User::getUserId();
			/*
			 * 新建补货单
			* 创建一个审核状态下的采购单，并且申请一次采购付款金额
			*/
			$poCode = "";
			if($doType == "2"){
				$payable_amount = 0;
				 
				//查询原采购单，根据原有采购单，构建新的采购单
				$purchase_tmep = Service_PurchaseOrders::getByField($po_id);
				$poCode = Common_GetNumbers::getCode('create_po', $purchase_tmep["warehouse_id"], 'PO');
				$purchase_tmep["po_code"] = $poCode;//采购单号
				$purchase_tmep["create_type"] = "0";//创建类型
				$purchase_tmep["actually_amount"] = "0";//总实际支付金额
				$purchase_tmep["pay_status"] = "0";//付款状态
				$purchase_tmep["po_status"] = "3";//采购单状态
				$purchase_tmep["date_release"] = $date;//审核时间
				$purchase_tmep["date_create"] = $date;//创建时间
				$purchase_tmep["operator_create"] = $userId;//创建人
				$purchase_tmep["operator_release"] = $userId;//创建人
				$purchase_tmep["date_eta"] = $date;//ETA时间
				$purchase_tmep["po_update_time"] = "";//更新时间
				$purchase_tmep["pay_ship_amount"] = 0;//运输金额
				//$purchase_tmep["operator_release"] = $userId;//采购员
				unset($purchase_tmep["po_id"]);
				 
				 
				//查询原采购单明细
				$purchaseProduct_temp = Service_PurchaseOrderProduct::getDoReceiveException($po_id);
				foreach($purchaseProduct_temp as $key=>$val){
					$purchaseProduct_temp[$key]["po_code"] = $poCode;
					$purchaseProduct_temp[$key]["po_status"] = "3";
					$purchaseProduct_temp[$key]["qty_expected"] = $val["qty_eta"] - $val["qty_receving"];
					$purchaseProduct_temp[$key]["qty_eta"] = $val["qty_eta"] - $val["qty_receving"];
					$purchaseProduct_temp[$key]["qty_receving"] = "0";
					$purchaseProduct_temp[$key]["payable_amount"] = $val["unit_price"] * $purchaseProduct_temp[$key]["qty_eta"];
					$purchaseProduct_temp[$key]["actually_amount"] = "0";
					$purchaseProduct_temp[$key]["pop_update_time"] = "";
					$purchaseProduct_temp[$key]["note"] = "收货不足异常处理，补货采购！";
					unset($purchaseProduct_temp[$key]["pop_id"]);
			
					$payable_amount += $purchaseProduct_temp[$key]["payable_amount"];
				}
				$purchase_tmep["payable_amount"] = $payable_amount;
				$purRe = Service_PurchaseOrders::add($purchase_tmep);
				 
				foreach($purchaseProduct_temp as $tKey=>$tVal){
					$tVal["po_id"] = $purRe;
					Service_PurchaseOrderProduct::add($tVal);
				}
				 
				/*
				 * 采购付款申请,如果是“款到发货”类型的供应商，在采购单审核节点需要申请付款
				*/
				$supplier_temp = Service_Supplier::getByField($purchase_tmep['supplier_id'],"supplier_id",array('account_type','account_proportion'));
				if($supplier_temp["account_type"] == "2"){//款到发货
					//申请付款数
					$accountAmount = ($supplier_temp["account_proportion"]/100)*($purchase_tmep["payable_amount"]+$purchase_tmep["pay_ship_amount"]);
					$payResult = Service_PurchasePaymentProcess::excutePurchaseApply($purchase_tmep["po_code"],$accountAmount,"生成审核状态补货采购单申请付款","1","1");//申请财务付款
						
					if($payResult["status"] == "0"){
						throw new Exception('采购单'.$purchase_tmep["po_code"].'申请采购付款失败！');
					}
				}
				 
			}
			$db->commit();
			$return = array(
					'state' => 1,
					'message' => '',
					'po_code' => $poCode
			);
		} catch (Exception $e) {
			$db->rollBack();
			$return = array(
					'state' => 0,
					'message' => $e->getMessage()
			);
		}

		return $return;
	}
	
	public static function completePurchase($paramPoId = "",$note=""){
		$date = date('Y-m-d H:i:s');
    	$userId = Service_User::getUserId();//当前操作用户
    	$paramsPo = array();
    	$return = array();
    	try {
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
    		$return = array(
    				'state' => 1,
    				'message' => ''
    		);
    	} catch (Exception $e) {
    		$return = array(
    				'state' => 0,
    				'message' => $e->getMessage()
    		);
    	}
    	
    	return $return;
	}
}

?>