<?php
/**
 * 采购付款流水业务类
 * @author C
 *
 */
class Service_PurchasePaymentProcess
{
	/**
	 * 获取采购单全额付款金额
	 * @param unknown_type $poCode 采购单号
	 * @return number 全额
	 */
	public function getPurchasePaymentFeeAll($poCode = ""){
		$resultAmount = 0;
		
		//获取采购单全额
		$purchase = Service_PurchaseOrders::getByField($poCode,"po_code",array('payable_amount'));
		$amount = $purchase["payable_amount"];
		
		//获取付款流水表已付款项
		$paymentAmount = 0;
		$payment = Service_PurchasePayment::getFeeByOrder($poCode,"po_code",array('pp_amount'));
		foreach($payment as $key=>$val){
			$paymentAmount += $val["pp_amount"];
		}
		$resultAmount = $amount - $paymentAmount;
		
		return $resultAmount;
	} 
	
	/**
	 * 按收货数量计算过支付额
	 * @param unknown_type $astParams 
	 * $astParams[0] -> 单号
	 * $astParams[1] -> 按到货数量（如果是空值表示无需过滤此条件）
	 * $astParams[2] -> 按未到数量（如果是空值表示无需过滤此条件）
	 */
	public function getArrivalAmount($astParams = array()){
		
		$db = Common_Common::getAdapter();
		$sql = "select qty_receving,qty_eta,qty_expected,payable_amount,unit_price from purchase_order_product where po_code = '".$astParams[0]."'";
		$result = $db->fetchAll($sql);
		
		$resultAmount = 0.000;
		//
		if(!empty($astParams[1]) && $astParams[1] != ""){
			foreach ($result as $key=>$val){
				$resultAmount += $val["qty_receving"]*$val["unit_price"];
			}
		}
		if(!empty($astParams[2]) && $astParams[2] != ""){
			foreach ($result as $key=>$val){
				$qty = $val["qty_eta"]==0?$val["qty_expected"]:$val["qty_eta"];
				$resultAmount += ($qty-$val["qty_receving"])*$val["unit_price"];
			}
		}
		
		return $resultAmount;
	}
	
	/**
	 * 按QC情况计算过支付额
	 * @param unknown_type $astParams 
	 * $astParams[0] -> 单号
	 * $astParams[1] -> 按QC合格数量（如果是空值表示无需过滤此条件）
	 * $astParams[2] -> 按QC不合格数量（如果是空值表示无需过滤此条件）
	 */
	public function getQCAmount($astParams = array()){
		$db = Common_Common::getAdapter();
		$sql = "select qc_quantity_sellable,
				qc_quantity_unsellable,
				(select unit_price from purchase_order_product where po_code = (
					select po_code from receiving where receiving_id = quality_control.receiving_id) and product_id = quality_control.product_id) as unit_price
				from quality_control where receiving_id = (
					select receiving_id from receiving where po_code = '".$astParams[0]."'
				)";
		$result = $db->fetchAll($sql);
		
		$resultAmount = 0.000;
		
		if(!empty($astParams[1]) && $astParams[1] != ""){
			foreach ($result as $key=>$val){
				$resultAmount += $val["qc_quantity_sellable"]*$val["unit_price"];
			}
		}
		
		if(!empty($astParams[2]) && $astParams[2] != ""){
			foreach ($result as $key=>$val){
				$resultAmount += $val["qc_quantity_unsellable"]*$val["unit_price"];
			}
		}
		
		return $resultAmount;
	}
	
	/**
	 * 获取申请支付采购单单头
	 * @param unknown_type $poCode 采购单号
	 * @return multitype:
	 */
	public function getPurchaseHead($poCode = ""){
		$db = Common_Common::getAdapter();
		$sql = "select po_code,payable_amount,purchase_orders.supplier_id,
				(select receiving_code from receiving where receiving.po_code = purchase_orders.po_code) as receiving_code,
				(select supplier.supplier_name from supplier where supplier.supplier_id = purchase_orders.supplier_id) as supplier_name
				from purchase_orders where po_code = '".$poCode."'";
		$result = $db->fetchRow($sql);
		return $result;
	}
	
	/**
	 * 获取申请支付采购产品信息
	 * @param unknown_type $poCode 采购单号
	 * @return multitype:
	 */
	public function getPurchaseDetail($poCode = ""){
		$db = Common_Common::getAdapter();
		$sql = "select product.product_barcode,
				purchase_orders.supplier_id,
				product.product_title,
				purchase_order_product.unit_price,
				purchase_order_product.qty_eta,
				purchase_order_product.qty_receving,
				quality_control.qc_quantity_sellable,
				quality_control.qc_quantity_unsellable
				from purchase_order_product
				LEFT JOIN purchase_orders on purchase_order_product.po_id = purchase_orders.po_id
				LEFT JOIN receiving on purchase_orders.po_code = receiving.po_code
				LEFT JOIN quality_control on receiving.receiving_id = quality_control.receiving_id
				LEFT JOIN product on purchase_order_product.product_id = product.product_id
				where purchase_order_product.po_code = '".$poCode."'";
		$result = $db->fetchAll($sql);
		return $result;
	}
	
	
	public function runPurchasePayDayService($startDateTime = "",$endDateTime = ""){
		
	}
	
	public function runPurchasePayMouthService($startDateTime = "",$endDateTime = ""){
		
	}
	
	/**
	 * 支持自动服务的供应商计费服务
	 * 该方法用于自动计算每天订单产生的下架产品对于供应商的计费
	 * @param unknown_type $startDateTime 开始时间
	 * @param unknown_type $endDateTime 结束时间
	 * @param unknown_type $supplierId 供应商
	 */
	public function runPurchasePaymentSupplier($startDateTime = "",$endDateTime = ""){
// 		$db = Common_Common::getAdapter();
// 		$db->beginTransaction();
// 		try {
			/**
			 * 1、获取时间段内的下架的订单SKU
			 * 运用分页策略，以免数据量过大造成运行时异常
			 */
			$pageSize = 2;
			$page = 0;
			$count = self::getByBatchOrder("count(*)", $pageSize, $page);
			$totalPage = ceil($count/$pageSize);
			if($count > 0){
				for($i = 1;$i<=$totalPage;$i++){
					$page++;
					//批量获取下架SKU
					$orderBatch = self::getByBatchOrder("*", $pageSize, $page);
					
					foreach ($orderBatch as $key=>$val){
					   /*
						* 通过入库单产品成本获取采购单
						* +---------------------------------------------+
						* 因为大部份为海外仓，库存多个采购单拼成一个中转ASN入库的，
						* 所以批次库存以及下架批次中的po_code大部份情况下是空值，
						* 必须要关联查询receiving_detail_cost表，
						* 才可以准确统计来自另一个采购单
						* +---------------------------------------------+
						*/
						$po = self::getByReceivingPo($val["receiving_id"],$val["product_id"]);
						
						//计算采购费用
						
						
					}
					
					
				}
			
			}
			/**
			 * 2、计算SKU采购费用
			 */
			
			
			
			/**
			 * 3、写入采购财务流水信息
			 */
			
			
// 		$db->commit();
// 		} catch (Exception $e) {
			
// 			$db->rollBack();
// 		}
		
	}
	
	/**
	 * 支持自动服务的供应商计费服务
	 * 该方法用于自动将 
	 * @param unknown_type $startDateTime
	 * @param unknown_type $endDateTime
	 * @param unknown_type $supplierId
	 */
	public static function runPurchaseMouthSupplier($startDateTime = "",$endDateTime = ""){
		
	}
	
	public static function getByReceivingPo($receivingId = "",$productId = ""){
		$db = Common_Common::getAdapter(); 
		$sql = "select po_code from receiving_detail_cost 
				where receiving_detail_cost.receiving_id = ".$receivingId." 
				and receiving_detail_cost.product_id = ".$productId;
		return $db->fetchRow($sql);
	}
	
	public static function getByBatchOrder($type = '*', $pageSize = 0, $page = 1){
		$db = Common_Common::getAdapter();
		$sql = "select ".$type." from (
					select overseas_picking_detail.receiving_id,overseas_picking_detail.product_id,SUM(overseas_picking_detail.opd_quantity) as opd_quantity
					from overseas_picking_detail 
					where pd_add_time >= '2013-10-01 00:00:00' and pd_add_time <= '2013-10-03 23:59:59' and overseas_picking_detail.receiving_id <> 0
					GROUP BY overseas_picking_detail.receiving_id,overseas_picking_detail.product_id 
					UNION ALL
					select picking_detail.receiving_id,picking_detail.product_id,SUM(picking_detail.pd_quantity) as opd_quantity
					from picking_detail where picking_detail.pd_add_time >=  '2013-10-01 00:00:00' 
					and picking_detail.pd_add_time <= '2013-10-03 23:59:59'  and picking_detail.receiving_id <> 0
					GROUP BY picking_detail.receiving_id,picking_detail.product_id
				) as cc" ;
		
		
		/*CONDITION_END*/
		if ('count(*)' == $type) {
			return $db->fetchOne($sql);
		} else {
			if ($pageSize > 0 and $page > 0) {
				$start = ($page - 1) * $pageSize;
				$sql = $sql." LIMIT ".$pageSize." OFFSET ".$start;
			}
			return $db->fetchAll($sql);
		}
	}
	
	public function excutePurchaseApplyNotTransaction($po_code = "",$applyAmount = "",$remark = "",$source = "1",$effect = "1")
	{
		$return = array("status"=>"0","message"=>"");
		try {
			/*
			 *1、校验数据
			*/
			//校验采购单是否存在
			$onePurchase = Service_PurchaseOrders::getByField($po_code,"po_code",array('po_code','payable_amount','po_id','currency_code','supplier_id','warehouse_id','currency_rate'));
			if(empty($onePurchase)){
					throw new Exception("采购单不存在！");
			}
			$applyAmount = trim($applyAmount);
			if(!preg_match("/^[0-9]+\.?[0-9]*$/",$applyAmount)){
				throw new Exception("申请金额必须是数字！");
			}
			
			//校验采购单申请数据是否大于采购单剩余未申请的费用
			//获取该采购已申请金额
			$applyAmountArray = Service_PurchasePayment::getFeeByOrder($po_code,"po_code",array('pp_amount'));
			$apply_temp = 0.000;
			foreach($applyAmountArray as $aKey=>$aVal){
				$apply_temp += $aVal["pp_amount"];
			}
			//采购单剩余未申请金额
			$checkAmount = $onePurchase["payable_amount"] - $apply_temp;
			if($applyAmount > $checkAmount){
				throw new Exception("您申请的支付费用超出该采购单允许申请的范围，请核对金额再提交（可申请金额 <= 采购单总金额-该采购单已申请金额）");
			}
			
			$operation_mode = "1";
			$supplier_temp = Service_Supplier::getByField($onePurchase["supplier_id"],"supplier_id","*");
			if($supplier_temp["account_type"] == "4"){//如果供应商结算方式为售后付款，则为代运营模式
				$operation_mode = "2";
			}
			
			/*
			 * 2、组装流水数据
			*/
			$ppCode = Common_GetNumbers::getCode('createPurchaseFee', "", 'PP');//流水号
// 			$currencyRows = Service_Currency::getByField($onePurchase["currency_code"],"currency_code",array('currency_rate'));//汇率
			$date = date('Y-m-d H:i:s');
			$purchasePayment = array();//采购付款流水表
			$purchasePayment["pp_no"] = $ppCode;
			$purchasePayment["po_code"] = $po_code;
			$purchasePayment["pp_source_type"] = "1";
			$purchasePayment["pp_effect_status"] = $effect;
			$purchasePayment["po_id"] = $onePurchase["po_id"];
			$purchasePayment["pp_applicant"] = Service_User::getUserId();
			
			/*
			 * 校验是否需要业务员审核采购费用，如果需要，生成的流水状态为“待审核”
			 * 如果不需要,生成的流水状态为“已审核（待付款）”
			 * 
			 */
			$isAudit = Common_Config::purchaseIsCheck();
			$ppStatus = "1";
			if(!$isAudit){
				$ppStatus = "3";
				$purchasePayment["pp_verifier"] = Service_User::getUserId();//审核人ID
			}
			$purchasePayment["pp_status"] = $ppStatus;//费用流水状态
			
			$purchasePayment["pp_operation_mode"] = $operation_mode;
			$purchasePayment["pp_amount"] = $applyAmount;
			$purchasePayment["currency_code"] = $onePurchase["currency_code"];
			$purchasePayment["currency_rate"] = $onePurchase["currency_rate"];//货币汇率
			$purchasePayment["pp_remark"] = $remark;
			$purchasePayment["pp_add_time"] = $date;
			$purchasePayment["pp_application_time"] = $date;
			$purchasePayment["pp_effective_time"] = $date;
// 			$purchasePayment["pp_purchase_audit"] = "1";//采购员未审核
			$ppId = Service_PurchasePayment::add($purchasePayment);
			if(!$ppId){
				throw new Exception("采购费用申请失败！申请支付主信息添加失败！");
			}
			$purchasePaymentLog = array();//采购付款历史表
			$purchasePaymentLog["pp_id"] = $ppId;
			$purchasePaymentLog["ppl_add_time"] = $date;
			$purchasePaymentLog["ppl_ip"] = Common_Common::getIP();
			$user = "0";
			if($source == '1'){
				$user = Service_User::getUserId();
			}
			$purchasePaymentLog["user_id"] = $user;
			$purchasePaymentLog["ppl_comments"] = "申请支付";
			
			$polId = Service_PurchasePaymentLog::add($purchasePaymentLog);
			if(!$polId){
				throw new Exception("采购费用申请失败！申请支付日志记录失败！");
			}
			
			/*
			 * 如果不需要采购员审核费用，则需要新增财务流水
			 */
			if(!$isAudit){
				$fm_no = Common_GetNumbers::getCode('createFinancialFee', "", 'PM');//获取财务流水号
				$fm_type = "0";
				if($applyAmount < 0){
					$fm_type = "1";
				}
				
				//获取供应商
				$sb_code = Common_PaymentProcess::getSupplierBillNo($onePurchase["supplier_id"]);
                //是否需要财务审核
                $fmStatus = 1;
                if (!Common_Config::financialManagementIsCheck()) {
                    $fmStatus = 3;
                }
				$financialManagement = array(
							"fm_no"=>$fm_no,
							"sb_code"=>$sb_code,
							"fm_type"=>$fm_type,
							"supplier_id"=>$onePurchase["supplier_id"],
							"warehouse_id"=>$onePurchase["warehouse_id"],
							"fm_source_type"=>"1",
							"fm_apply_pay_type"=>$supplier_temp["pay_type"],
							"fm_pay_company_name"=>$supplier_temp["supplier_name"],//收款公司
							"fm_pay_card"=>$supplier_temp["pay_card"],//收款卡号或地址
							"fm_pay_name"=>$supplier_temp["pay_name"],//收款人名称
							"fm_pay_bank"=>$supplier_temp["pay_bank"],//收款银行备注
							"fm_pay_platform"=>$supplier_temp["pay_platform"],//支付平台代码 
							"pm_code"=>"BANK",//支付方式CODE  payment_method 表
							"fm_applicant"=>Service_User::getUserId(),//申请人、负责人ID
							"fm_verifier"=>Service_User::getUserId(),//审核人ID
							"fm_status"=>$fmStatus,//付款状态，待审核
							"fm_amount"=>$applyAmount,//支付金额
							"currency_code"=>$onePurchase["currency_code"],//币种
							"currency_rate"=>$onePurchase["currency_rate"],//货币汇率
// 							"fm_no"=>$fm_no,//备注
							"fm_add_time"=>$date,//创建时间
							"fm_application_time"=>$date,//申请时间
							"fm_effective_time"=>$date,//生效时间
// 							"fm_verify_time"=>$date,//审核时间
						);
				$finaMe = Service_FinancialManagement::add($financialManagement);
				if(!$finaMe){
					throw new Exception("采购费用申请失败！添加财务流水失败！");
				}
				
				/*
				 * 回写财务流水号
				 */
				$upPay = Service_PurchasePayment::update(array("fm_no"=>$fm_no),$ppId,"pp_id");
				if(!$upPay){
					throw new Exception("采购费用申请失败！回写财务流水号失败！");
				}
				
			}
			$return["status"] = "1";
		} catch (Exception $e) {
			
			$return["message"] = $e->getMessage();
		}
		return $return;
	}
	
	/**
	 * 采购单支付申请（submit）
	 * @param unknown_type $po_code 采购单号
	 * @param unknown_type $applyAmount 申请金额
	 * @param unknown_type $remark 备注
	 * @param unknown_type $source 来源 0:手工。1:系统
	 * @param unknown_type $effect 是否生效 0：未生效  1：已生效
	 */
	public static function excutePurchaseApply($po_code = "",$applyAmount = "",$remark = "",$source = "1",$effect = "1"){
		$return = array("status"=>"0","message"=>"");
		$db = Common_Common::getAdapter();
		$db->beginTransaction();
		
		try {
			$return = self::excutePurchaseApplyNotTransaction($po_code,$applyAmount ,$remark ,$source ,$effect);
			$db->commit();
			$return["status"] = "1";
		} catch (Exception $e) {
			$db->rollBack();
			$return["message"] = $e->getMessage();
		}
		
		return $return;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}