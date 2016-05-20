<?php
/**
 * amazon拉取订单Items自动服务
 * @author Frank
 * @date 2013-11-14 13:21:19
 */
class Amazon_AmazonOrderItemsService extends Ec_AutoRun{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'runListOrderItems_';
	
	private $_configSecretKey = array(
							'token_id' => null,
							'token' => null,
							'site' => null,
							'seller_id'=>null
							);
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(0);
	}
	/**
	 * 亚马逊订单Itmes
	 */
	private static $amazonOrderItemsRow = array();
	
	/**
	 * 在AutoRun调用中被调用的方法，自动同步程序的入口
	 * @param unknown_type $loadId
	 */
	public function callListOrderItems($loadId){
		return $this->runListOrderItems($loadId);
	}
	
	/**
	 * amazon订单Itmes下载
	 * @see Ec_AutoRun::run()
	 */
	public function runListOrderItems($loadId){
		$i = 1;
		echo $i++ . ':进入下载amazon订单Items服务<br/><br/>';
		
		/*
		 * 1. 加载当前同步程序的控制参数
		*/
		$param 		 = $this->getLoadParam($loadId);
		echo $i++ . ':加载任务参数<br/><br/>';
		$amazonAccount = $param["user_account"];			//绑定的amazon账户
		$start 		 = $param["load_start_time"];			//开始时间（美国时间）
		$end    	 = $param["load_end_time"];				//结束时间（美国）
		$count 		 = $param["currt_run_count"];			//当前运行第几页
		$nowDate = date('Y-m-d H:i:s');
		echo $i++ . ":下载amazon订单Itmes，时间 '$nowDate' <br/><br/>";
		
		/*
		 * 2. 查询amazon用户 Token信息
		 */
		$resultPlatformUser = Service_PlatformUser::getByField($amazonAccount,'user_account');
		echo $i++ . ':查询amazon签名<br/><br/>';
		if(empty($resultPlatformUser)){
			$errorMessage = "amazon账户：'$amazonAccount' 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$log_name);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}
		$this->_configSecretKey['token_id'] = $resultPlatformUser["user_token_id"];
		$this->_configSecretKey['token'] = $resultPlatformUser["user_token"];
		$this->_configSecretKey['site'] = $resultPlatformUser["site"];
		$this->_configSecretKey['seller_id'] = $resultPlatformUser["seller_id"];
		
		/*
		 * 3. 查询未下载Items的Amazon订单
		 */
		$conAmazonOrderOriginal = array(
				'user_account'=>$amazonAccount,
				'is_loaded'=>'0'
				);
		$rowNum = Amazon_ListOrderItemsService::REQUEST_MAX - 2;
		$resultAmazonOrderOriginal = Service_AmazonOrderOriginal::getByCondition($conAmazonOrderOriginal,'*',$rowNum,1,'last_update_date desc');
		//无数据，直接返回
		$loadAmazonOrders = array();
		if(empty($resultAmazonOrderOriginal)){
			$this->countLoad($loadId, 2, 0);
			return array(
					'ask' => '1',
					'message' => "amazon账户：$amazonAccount,已处理: '$start' ~ '$end' 的订单Items任务完成."
			);
		}else{
			foreach ($resultAmazonOrderOriginal as $itemKey => $itemValue) {
				$loadAmazonOrders[$itemValue['amazon_order_id']] = $itemValue;
			}
		}
		
		/*
		 * 4. 循环查询Amazon订单的Items
		 */
		//请求ListOrderItems是否失败过
		$callListOrderItemsErrorBol = false;
		foreach ($loadAmazonOrders as $amazonOrderKey => $amazonOrderValue) {
		    ping();//检测数据库连接
			/*
			 * 5.1 调用接口查询Items
			 */
		    echo '订单ID：“' . $amazonOrderValue['aoo_id'] . '” 拉取Item-----<br/><br/>';
			echo $i++ . ':new Amazon_ListOrderItemsService()并调用<br/><br/>';
			$listOrderItmesService = new Amazon_ListOrderItemsService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site']);
			$amazonOrderId = $amazonOrderKey;
			$ListOrderItemsServiceReturn = $listOrderItmesService->getListOrderItems($amazonOrderId);
			
			$nextToken = null;
			if($ListOrderItemsServiceReturn['ask']){
				echo $i++ . ':调用成功，组织参数<br/><br/>';
				$this->convertListOrderItems($ListOrderItemsServiceReturn['data'], $amazonOrderId ,$amazonOrderValue['aoo_id']);
				
				if($ListOrderItemsServiceReturn['data']->getListOrderItemsResult()->isSetNextToken()){
					echo $i++ . ':数据不全，使用nextToken继续调用<br/><br/>';
					$nextToken = $ListOrderItemsServiceReturn['data']->getListOrderItemsResult()->getNextToken();
				}
			}else{
				echo $i++ . ':new Amazon_ListOrderItemsService() 调用失败->' . $ListOrderItemsServiceReturn['message'] . '<br/><br/>';
				//使用nextToken请求，出现异常，不处理只记录日志
				$errorMessage = "amazonOrderId： '$amazonOrderId',(getListOrderItems)运行异常->" . print_r($ListOrderItemsServiceReturn,true);
				Ec::showError($errorMessage, self::$log_name);
				$callListOrderItemsErrorBol = true;
			}
			
			/*
			 * 5.2  判断是否有nextToken，继续调用
			*/
			while(!empty($nextToken)){
				$ListOrderItemsByNextTokenServiceReturn = $this->getAmazonOrderItemsByNextToken($nextToken);
				if($ListOrderItemsByNextTokenServiceReturn['ask']){
					echo $i++ . ':$nextToken调用成功，组织参数<br/><br/>';
					$this->convertListOrderItemsByNextToken($ListOrderItemsByNextTokenServiceReturn['data'], $amazonOrderId ,$amazonOrderValue['aoo_id']);
					
					if($ListOrderItemsByNextTokenServiceReturn['data']->getListOrderItemsByNextTokenResult()->isSetNextToken()){
						echo $i++ . ':数据不全，继续使用nextToken调用<br/><br/>';
						$nextToken = $ListOrderItemsByNextTokenServiceReturn['data']->getListOrderItemsByNextTokenResult()->getNextToken();
					}else{
						$nextToken = null;
					}
				}else{
					echo $i++ . ':new Amazon_ListOrderItemsService() 调用失败->' . $ListOrderItemsServiceReturn['message'] . '<br/><br/>';
					$this->countLoad($loadId, 3,0);
					$errorMessage = "amazon账户： '$amazonAccount',(getListOrderItems)运行异常->" . print_r($ListOrderItemsServiceReturn,true);
					Ec::showError($errorMessage, self::$log_name);
					return array('ask'=>'0','message'=>$errorMessage);
				}
			}
		}
		
		/*
		 * 6. 检查是否存在数据，校验重复-->保存
		 */
		$addRowNum = 0;
		if(count(self::$amazonOrderItemsRow) > 0){
			echo $i++ . ':开始校验重复数据<br/><br/>';
			echo 'Items数据：<br/><br/>';
// 			print_r(self::$amazonOrderItemsRow);
			echo '<br/>';
// 			$db = $model->getAdapter();
			$db = Common_Common::getAdapter();
			try{
				$db->beginTransaction();
				//写入amazonItems
				foreach (self::$amazonOrderItemsRow as $amazonOrderItemsRowKey => $amazonOrderItemsRowValue) {
					$amazonOrderId_c = $amazonOrderItemsRowKey;	//amazon订单号
					$addBol = false;
					//先删除明细
					Service_AmazonOrderDetail::delete($amazonOrderId_c,'amazon_order_id');
					foreach ($amazonOrderItemsRowValue as $Itemskey => $ItemsValue) {
						$orderItemId_c = $Itemskey;	//order_itemt_id
						$conAmazonOrderDetail = array(
								'amazon_order_id'=>$ItemsValue['amazon_order_id'],
								'order_item_id'=>$ItemsValue['order_item_id']);
						$resultAmazonOrderDetail = Service_AmazonOrderDetail::getByCondition($conAmazonOrderDetail);
						if(empty($resultAmazonOrderDetail)){
							$promotionIdList = array();
							if(isset($ItemsValue['promotionIdList'])){
								$promotionIdList = $ItemsValue['promotionIdList'];
								unset($ItemsValue['promotionIdList']);
								ksort($ItemsValue);
							}
							$aod_id = Service_AmazonOrderDetail::add($ItemsValue);							
							if(isset($ItemsValue['promotionIdList'])){
								//付款类型
								foreach ($promotionIdList as $promotionIdListKey => $promotionIdListValue) {
									$promotionIdValue['aod_id'] = $aod_id;
									$promotionIdValue['amazon_order_id'] = $amazonOrderId_c;
									Service_AmazonOrderDetailPromotion::add($promotionIdValue);
								}
							}
							$addRowNum += 1;
							$addBol = true;
							

							/* ---------------补货任务 start--------------------- */
							//自定义补货
							$sql = "update seller_item_supply_qty set sync_status=0 where supply_type='2' and platform='amazon' and user_account='{$amazonAccount}'  and sku='{$ItemsValue['seller_sku']}'";
							Common_Common::query($sql);
							//按仓补货
							Common_SupplyQtyProcess::order_product_platform_oversee('amazon',$amazonAccount,$ItemsValue['seller_sku']);
							/* ---------------补货任务 end--------------------- */
							
							
						}
					}
					if($addBol){
						Service_AmazonOrderOriginal::update(array('is_loaded'=>1), $amazonOrderId_c,'amazon_order_id');
					}
				}
				$db->commit();
			}catch(Exception $e){
				$db->rollBack();
				$this->countLoad($loadId, 3,0);
				$date = date('Y-m-d H:i:s');
				Ec::showError("amazon账户：'$amazonAccount',在 '$date'下载订单Items信息出现异常,错误原因：".$e->getMessage(), self::$log_name);
				return array('ask'=>'0','message'=>$e->getMessage());
			}
		}else{
			echo $i++ . ':无数据需要校验<br/><br/>';
		}
		
		/*
		 * 6.  处理完成，更新数据控制表
		*/
		echo $i++ . ":下载amazon订单Items服务执行完毕,总计插入数据 $addRowNum 条<br/><br/>";
		$returnStatus = 2;
		if($callListOrderItemsErrorBol && $addRowNum == 0){
			$returnStatus = 3;
		}
		$this->countLoad($loadId, $returnStatus, $addRowNum);
		return array(
				'ask' => '1',
				'message' => "amazon账户：$amazonAccount,已处理: '$start' ~ '$end' 的订单Items任务完成."
		);
	}
	
	/**
	 * 
	 * @param unknown_type $nextToken
	 * @return Ambigous <multitype:number string MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenResponse , multitype:number string Ambigous <number, NULL, string, number, NULL, string, mixed> >
	 */
	private function getAmazonOrderItemsByNextToken($nextToken){
		$obj = new Amazon_ListOrderItemsByNextTokenService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site']);
		$return = $obj->getListOrderItems($nextToken);
		return $return;
	}
	
	/**
	 * 封装ListOrderItems返回的Itmes信息
	 * @param unknown_type $listOrderItemsResponse
	 * @param unknown_type $amazonOrderId
	 * @param unknown_type $aoo_id
	 * @return multitype:multitype:multitype: unknown NULL
	 */
	private function convertListOrderItems($listOrderItemsResponse, $amazonOrderId, $aoo_id){
		$response = $listOrderItemsResponse;
		$itemsRow = array();
		if ($response->isSetListOrderItemsResult()) {
			$listOrderItemsResult = $response->getListOrderItemsResult();
		
			if ($listOrderItemsResult->isSetAmazonOrderId())
			{
				$amazonOrderId = $listOrderItemsResult->getAmazonOrderId();
			}
			if ($listOrderItemsResult->isSetOrderItems()) {
				
				$orderItems = $listOrderItemsResult->getOrderItems();
				$orderItemList = $orderItems->getOrderItem();
				foreach ($orderItemList as $orderItem) {
					$row = array();
					$row['amazon_order_id'] = $amazonOrderId;
					$row['aoo_id'] = $aoo_id;
					if ($orderItem->isSetASIN())
					{
// 						echo("                            " . $orderItem->getASIN() . "\n");
						$row['asin'] = $orderItem->getASIN();
					}
					if ($orderItem->isSetSellerSKU())
					{
// 						echo("                            " . $orderItem->getSellerSKU() . "\n");
						$row['seller_sku'] = $orderItem->getSellerSKU();
					}
					if ($orderItem->isSetOrderItemId())
					{
// 						echo("                            " . $orderItem->getOrderItemId() . "\n");
						$row['order_item_id'] = $orderItem->getOrderItemId();
					}
					if ($orderItem->isSetTitle())
					{
// 						echo("                            " . $orderItem->getTitle() . "\n");
						$row['title'] = $orderItem->getTitle();
					}
					if ($orderItem->isSetQuantityOrdered())
					{
// 						echo("                            " . $orderItem->getQuantityOrdered() . "\n");
						$row['quantity_ordered'] = $orderItem->getQuantityOrdered();
					}
					if ($orderItem->isSetQuantityShipped())
					{
// 						echo("                            " . $orderItem->getQuantityShipped() . "\n");
						$row['quantity_shipped'] = $orderItem->getQuantityShipped();
					}
					if ($orderItem->isSetItemPrice()) {
// 						echo("                        ItemPrice\n");
						$itemPrice = $orderItem->getItemPrice();
						if ($itemPrice->isSetCurrencyCode())
						{
// 							echo("                                " . $itemPrice->getCurrencyCode() . "\n");
							$row['item_price_currency_code'] = $itemPrice->getCurrencyCode();
						}
						if ($itemPrice->isSetAmount())
						{
// 							echo("                                " . $itemPrice->getAmount() . "\n");
							$row['item_price_amount'] = $itemPrice->getAmount();
						}
					}
					if ($orderItem->isSetShippingPrice()) {
// 						echo("                        ShippingPrice\n");
						$shippingPrice = $orderItem->getShippingPrice();
						if ($shippingPrice->isSetCurrencyCode())
						{
// 							echo("                                " . $shippingPrice->getCurrencyCode() . "\n");
							$row['shipping_price_currency_code'] = $shippingPrice->getCurrencyCode();
						}
						if ($shippingPrice->isSetAmount())
						{
// 							echo("                                " . $shippingPrice->getAmount() . "\n");
							$row['shipping_price_amount'] = $shippingPrice->getAmount();
						}
					}
					if ($orderItem->isSetGiftWrapPrice()) {
// 						echo("                        GiftWrapPrice\n");
						$giftWrapPrice = $orderItem->getGiftWrapPrice();
						if ($giftWrapPrice->isSetCurrencyCode())
						{
// 							echo("                                " . $giftWrapPrice->getCurrencyCode() . "\n");
							$row['gift_wrap_price_currency_code'] = $giftWrapPrice->getCurrencyCode();
						}
						if ($giftWrapPrice->isSetAmount())
						{
// 							echo("                                " . $giftWrapPrice->getAmount() . "\n");
							$row['gift_wrap_price_amount'] = $giftWrapPrice->getAmount();
						}
					}
					if ($orderItem->isSetItemTax()) {
// 						echo("                        ItemTax\n");
						$itemTax = $orderItem->getItemTax();
						if ($itemTax->isSetCurrencyCode())
						{
// 							echo("                                " . $itemTax->getCurrencyCode() . "\n");
							$row['item_tax_currency_code'] = $itemTax->getCurrencyCode();
						}
						if ($itemTax->isSetAmount())
						{
// 							echo("                                " . $itemTax->getAmount() . "\n");
							$row['item_tax_amount'] = $itemTax->getAmount();
						}
					}
					if ($orderItem->isSetShippingTax()) {
// 						echo("                        ShippingTax\n");
						$shippingTax = $orderItem->getShippingTax();
						if ($shippingTax->isSetCurrencyCode())
						{
// 							echo("                                " . $shippingTax->getCurrencyCode() . "\n");
							$row['shipping_tax_currency_code'] = $shippingTax->getCurrencyCode();
						}
						if ($shippingTax->isSetAmount())
						{
// 							echo("                                " . $shippingTax->getAmount() . "\n");
							$row['shipping_tax_amount'] = $shippingTax->getAmount();
						}
					}
					if ($orderItem->isSetGiftWrapTax()) {
// 						echo("                        GiftWrapTax\n");
						$giftWrapTax = $orderItem->getGiftWrapTax();
						if ($giftWrapTax->isSetCurrencyCode())
						{
// 							echo("                                " . $giftWrapTax->getCurrencyCode() . "\n");
							$row['gift_wrap_tax_currency_code'] = $giftWrapTax->getCurrencyCode();
						}
						if ($giftWrapTax->isSetAmount())
						{
// 							echo("                                " . $giftWrapTax->getAmount() . "\n");
							$row['gift_wrap_tax_amount'] = $giftWrapTax->getAmount();
						}
					}
					if ($orderItem->isSetShippingDiscount()) {
// 						echo("                        ShippingDiscount\n");
						$shippingDiscount = $orderItem->getShippingDiscount();
						if ($shippingDiscount->isSetCurrencyCode())
						{
// 							echo("                                " . $shippingDiscount->getCurrencyCode() . "\n");
							$row['shipping_discount_currency_code'] = $shippingDiscount->getCurrencyCode();
						}
						if ($shippingDiscount->isSetAmount())
						{
// 							echo("                                " . $shippingDiscount->getAmount() . "\n");
							$row['shipping_discount_amount'] = $shippingDiscount->getAmount();
						}
					}
					if ($orderItem->isSetPromotionDiscount()) {
// 						echo("                        PromotionDiscount\n");
						$promotionDiscount = $orderItem->getPromotionDiscount();
						if ($promotionDiscount->isSetCurrencyCode())
						{
// 							echo("                                " . $promotionDiscount->getCurrencyCode() . "\n");
							$row['promotion_discount_currency_code'] = $promotionDiscount->getCurrencyCode();
						}
						if ($promotionDiscount->isSetAmount())
						{
// 							echo("                                " . $promotionDiscount->getAmount() . "\n");
							$row['promotion_discount_amount'] = $promotionDiscount->getAmount();
						}
					}
					if ($orderItem->isSetPromotionIds()) {
						$promotionIds = $orderItem->getPromotionIds();
						$promotionIdList  =  $promotionIds->getPromotionId();
						$row['promotionIdList'] = array();
						foreach ($promotionIdList as $promotionId) {
// 							echo("                                " . $promotionId);
							$row['promotionIdList'][] = $promotionId;
						}
					}
					if ($orderItem->isSetCODFee()) {
// 						echo("                        CODFee\n");
						$CODFee = $orderItem->getCODFee();
						if ($CODFee->isSetCurrencyCode())
						{
// 							echo("                                " . $CODFee->getCurrencyCode() . "\n");
							$row['cod_fee_currency_code'] = $CODFee->getCurrencyCode();
						}
						if ($CODFee->isSetAmount())
						{
// 							echo("                                " . $CODFee->getAmount() . "\n");
							$row['cod_fee_amount'] = $CODFee->getAmount();
						}
					}
					if ($orderItem->isSetCODFeeDiscount()) {
// 						echo("                        CODFeeDiscount\n");
						$CODFeeDiscount = $orderItem->getCODFeeDiscount();
						if ($CODFeeDiscount->isSetCurrencyCode())
						{
// 							echo("                                " . $CODFeeDiscount->getCurrencyCode() . "\n");
							$row['cod_fee_discount_currency_code'] = $CODFeeDiscount->getCurrencyCode();
						}
						if ($CODFeeDiscount->isSetAmount())
						{
// 							echo("                                " . $CODFeeDiscount->getAmount() . "\n");
							$row['cod_fee_discount_amount'] = $CODFeeDiscount->getAmount();
						}
					}
					if ($orderItem->isSetGiftMessageText())
					{
// 						echo("                            " . $orderItem->getGiftMessageText() . "\n");
						$row['gift_message_text'] = $orderItem->getGiftMessageText();
					}
					if ($orderItem->isSetGiftWrapLevel())
					{
// 						echo("                            " . $orderItem->getGiftWrapLevel() . "\n");
						$row['gift_wrap_level'] = $orderItem->getGiftWrapLevel();
					}
					if ($orderItem->isSetInvoiceData()) {
// 						echo("                        InvoiceData\n");
						$invoiceData = $orderItem->getInvoiceData();
						if ($invoiceData->isSetInvoiceRequirement())
						{
// 							echo("                                " . $invoiceData->getInvoiceRequirement() . "\n");
							$row['invoice_requirement'] = $invoiceData->getInvoiceRequirement();
						}
						if ($invoiceData->isSetBuyerSelectedInvoiceCategory())
						{
// 							echo("                                " . $invoiceData->getBuyerSelectedInvoiceCategory() . "\n");
							$row['invoice_buyer_selected_category'] = $invoiceData->getBuyerSelectedInvoiceCategory();
						}
						if ($invoiceData->isSetInvoiceTitle())
						{
// 							echo("                                " . $invoiceData->getInvoiceTitle() . "\n");
							$row['invoice_title'] = $invoiceData->getInvoiceTitle();
						}
						if ($invoiceData->isSetInvoiceInformation())
						{
// 							echo("                                " . $invoiceData->getInvoiceInformation() . "\n");
							$row['invoice_information'] = $invoiceData->getInvoiceInformation();
						}
					}
					
					if ($response->isSetResponseMetadata()) {
// 						echo("            ResponseMetadata\n");
						$responseMetadata = $response->getResponseMetadata();
						if ($responseMetadata->isSetRequestId())
						{
// 							echo("                    " . $responseMetadata->getRequestId() . "\n");
							$row['request_id'] = $responseMetadata->getRequestId();
						}
					}
					$itemsRow[$amazonOrderId][$row['order_item_id']] = $row;
					self::$amazonOrderItemsRow[$amazonOrderId][$row['order_item_id']] = $row;
				}
			}
		}
		return $itemsRow;
	}
	
	/**
	 * 封装ListOrderItmesByNextToken返回的Items信息
	 * @param unknown_type $listOrderItmesByNextTokenResponse
	 * @param unknown_type $amazonOrderId
	 * @param unknown_type $aoo_id
	 * @return multitype:multitype:multitype: unknown NULL
	 */
	private function convertListOrderItemsByNextToken($listOrderItmesByNextTokenResponse, $amazonOrderId , $aoo_id){
		$response = $listOrdersByNextTokenResponse;
		$itemsRow = array();
		if ($response->isSetListOrderItemsByNextTokenResult()) {
			if ($listOrderItemsByNextTokenResult->isSetAmazonOrderId())
			{
// 				echo("                AmazonOrderId\n");
				echo("                    " . $listOrderItemsByNextTokenResult->getAmazonOrderId() . "\n");
			}
			if ($listOrderItemsByNextTokenResult->isSetOrderItems()) {
// 				echo("                OrderItems\n");
				$orderItems = $listOrderItemsByNextTokenResult->getOrderItems();
				$orderItemList = $orderItems->getOrderItem();
				foreach ($orderItemList as $orderItem) {
					$row = array();
					$row['amazon_order_id'] = $amazonOrderId;
					$row['aoo_id'] = $aoo_id;
					if ($orderItem->isSetASIN())
					{
// 						echo("                            " . $orderItem->getASIN() . "\n");
						$row['asin'] = $orderItem->getASIN();
					}
					if ($orderItem->isSetSellerSKU())
					{
// 						echo("                            " . $orderItem->getSellerSKU() . "\n");
						$row['seller_sku'] = $orderItem->getSellerSKU();
					}
					if ($orderItem->isSetOrderItemId())
					{
// 						echo("                            " . $orderItem->getOrderItemId() . "\n");
						$row['order_item_id'] = $orderItem->getOrderItemId();
					}
					if ($orderItem->isSetTitle())
					{
// 						echo("                            " . $orderItem->getTitle() . "\n");
						$row['title'] = $orderItem->getTitle();
					}
					if ($orderItem->isSetQuantityOrdered())
					{
// 						echo("                            " . $orderItem->getQuantityOrdered() . "\n");
						$row['quantity_ordered'] = $orderItem->getQuantityOrdered();
					}
					if ($orderItem->isSetQuantityShipped())
					{
// 						echo("                            " . $orderItem->getQuantityShipped() . "\n");
						$row['quantity_shipped'] = $orderItem->getQuantityShipped();
					}
					if ($orderItem->isSetItemPrice()) {
						echo("                        ItemPrice\n");
						$itemPrice = $orderItem->getItemPrice();
						if ($itemPrice->isSetCurrencyCode())
						{
// 							echo("                                " . $itemPrice->getCurrencyCode() . "\n");
							$row['item_price_currency_code'] = $itemPrice->getCurrencyCode();
						}
						if ($itemPrice->isSetAmount())
						{
// 							echo("                                " . $itemPrice->getAmount() . "\n");
							$row['item_price_amount'] = $itemPrice->getAmount();
						}
					}
					if ($orderItem->isSetShippingPrice()) {
// 						echo("                        ShippingPrice\n");
						$shippingPrice = $orderItem->getShippingPrice();
						if ($shippingPrice->isSetCurrencyCode())
						{
// 							echo("                                " . $shippingPrice->getCurrencyCode() . "\n");
							$row['shipping_price_currency_code'] = $shippingPrice->getCurrencyCode();
						}
						if ($shippingPrice->isSetAmount())
						{
// 							echo("                                " . $shippingPrice->getAmount() . "\n");
							$row['shipping_price_amount'] = $shippingPrice->getAmount();
						}
					}
					if ($orderItem->isSetGiftWrapPrice()) {
// 						echo("                        GiftWrapPrice\n");
						$giftWrapPrice = $orderItem->getGiftWrapPrice();
						if ($giftWrapPrice->isSetCurrencyCode())
						{
// 							echo("                                " . $giftWrapPrice->getCurrencyCode() . "\n");
							$row['gift_wrap_price_currency_code'] = $giftWrapPrice->getCurrencyCode();
						}
						if ($giftWrapPrice->isSetAmount())
						{
// 							echo("                                " . $giftWrapPrice->getAmount() . "\n");
							$row['gift_wrap_price_amount'] = $giftWrapPrice->getAmount();
						}
					}
					if ($orderItem->isSetItemTax()) {
// 						echo("                        ItemTax\n");
						$itemTax = $orderItem->getItemTax();
						if ($itemTax->isSetCurrencyCode())
						{
// 							echo("                                " . $itemTax->getCurrencyCode() . "\n");
							$row['item_tax_currency_code'] = $itemTax->getCurrencyCode();
						}
						if ($itemTax->isSetAmount())
						{
// 							echo("                                " . $itemTax->getAmount() . "\n");
							$row['item_tax_amount'] = $itemTax->getAmount();
						}
					}
					if ($orderItem->isSetShippingTax()) {
// 						echo("                        ShippingTax\n");
						$shippingTax = $orderItem->getShippingTax();
						if ($shippingTax->isSetCurrencyCode())
						{
// 							echo("                                " . $shippingTax->getCurrencyCode() . "\n");
							$row['shipping_tax_currency_code'] = $shippingTax->getCurrencyCode();
						}
						if ($shippingTax->isSetAmount())
						{
// 							echo("                            Amount\n");
// 							echo("                                " . $shippingTax->getAmount() . "\n");
							$row['shipping_tax_amount'] = $shippingTax->getAmount();
						}
					}
					if ($orderItem->isSetGiftWrapTax()) {
// 						echo("                        GiftWrapTax\n");
						$giftWrapTax = $orderItem->getGiftWrapTax();
						if ($giftWrapTax->isSetCurrencyCode())
						{
// 							echo("                                " . $giftWrapTax->getCurrencyCode() . "\n");
							$row['gift_wrap_tax_currency_code'] = $giftWrapTax->getCurrencyCode();
						}
						if ($giftWrapTax->isSetAmount())
						{
// 							echo("                                " . $giftWrapTax->getAmount() . "\n");
							$row['gift_wrap_tax_amount'] = $giftWrapTax->getAmount();
						}
					}
					if ($orderItem->isSetShippingDiscount()) {
// 						echo("                        ShippingDiscount\n");
						$shippingDiscount = $orderItem->getShippingDiscount();
						if ($shippingDiscount->isSetCurrencyCode())
						{
// 							echo("                                " . $shippingDiscount->getCurrencyCode() . "\n");
							$row['shipping_discount_currency_code'] = $shippingDiscount->getCurrencyCode();
						}
						if ($shippingDiscount->isSetAmount())
						{
// 							echo("                                " . $shippingDiscount->getAmount() . "\n");
							$row['shipping_discount_amount'] = $shippingDiscount->getAmount();
						}
					}
					if ($orderItem->isSetPromotionDiscount()) {
// 						echo("                        PromotionDiscount\n");
						$promotionDiscount = $orderItem->getPromotionDiscount();
						if ($promotionDiscount->isSetCurrencyCode())
						{
// 							echo("                                " . $promotionDiscount->getCurrencyCode() . "\n");
							$row['promotion_discount_currency_code'] = $promotionDiscount->getCurrencyCode();
						}
						if ($promotionDiscount->isSetAmount())
						{
// 							echo("                            Amount\n");
// 							echo("                                " . $promotionDiscount->getAmount() . "\n");
							$row['promotion_discount_amount'] = $promotionDiscount->getAmount();
						}
					}
					if ($orderItem->isSetPromotionIds()) {
						$promotionIds = $orderItem->getPromotionIds();
						$promotionIdList  =  $promotionIds->getPromotionId();
						$row['promotionIdList'] = array();
						foreach ($promotionIdList as $promotionId) {
// 							echo("                                " . $promotionId);
							$row['promotionIdList'][] = $promotionId;
						}
					}
					if ($orderItem->isSetCODFee()) {
// 						echo("                        CODFee\n");
						$CODFee = $orderItem->getCODFee();
						if ($CODFee->isSetCurrencyCode())
						{
// 							echo("                                " . $CODFee->getCurrencyCode() . "\n");
							$row['cod_fee_currency_code'] = $CODFee->getCurrencyCode();
						}
						if ($CODFee->isSetAmount())
						{
// 							echo("                            Amount\n");
// 							echo("                                " . $CODFee->getAmount() . "\n");
							$row['cod_fee_amount'] = $CODFee->getAmount();
						}
					}
					if ($orderItem->isSetCODFeeDiscount()) {
// 						echo("                        CODFeeDiscount\n");
						$CODFeeDiscount = $orderItem->getCODFeeDiscount();
						if ($CODFeeDiscount->isSetCurrencyCode())
						{
// 							echo("                                " . $CODFeeDiscount->getCurrencyCode() . "\n");
							$row['cod_fee_discount_currency_code'] = $CODFeeDiscount->getCurrencyCode();
						}
						if ($CODFeeDiscount->isSetAmount())
						{
// 							echo("                                " . $CODFeeDiscount->getAmount() . "\n");
							$row['cod_fee_discount_amount'] = $CODFeeDiscount->getAmount();
						}
					}
					if ($orderItem->isSetGiftMessageText())
					{
// 						echo("                            " . $orderItem->getGiftMessageText() . "\n");
						$row['gift_message_text'] = $orderItem->getGiftMessageText();
					}
					if ($orderItem->isSetGiftWrapLevel())
					{
// 						echo("                            " . $orderItem->getGiftWrapLevel() . "\n");
						$row['gift_wrap_level'] = $orderItem->getGiftWrapLevel();
					}
					if ($orderItem->isSetInvoiceData()) {
// 						echo("                        InvoiceData\n");
						$invoiceData = $orderItem->getInvoiceData();
						if ($invoiceData->isSetInvoiceRequirement())
						{
// 							echo("                                " . $invoiceData->getInvoiceRequirement() . "\n");
							$row['invoice_requirement'] = $invoiceData->getInvoiceRequirement();
						}
						if ($invoiceData->isSetBuyerSelectedInvoiceCategory())
						{
// 							echo("                                " . $invoiceData->getBuyerSelectedInvoiceCategory() . "\n");
							$row['invoice_buyer_selected_category'] = $invoiceData->getBuyerSelectedInvoiceCategory();
						}
						if ($invoiceData->isSetInvoiceTitle())
						{
// 							echo("                                " . $invoiceData->getInvoiceTitle() . "\n");
							$row['invoice_title'] = $invoiceData->getInvoiceTitle();
						}
						if ($invoiceData->isSetInvoiceInformation())
						{
// 							echo("                                " . $invoiceData->getInvoiceInformation() . "\n");
							$row['invoice_information'] = $invoiceData->getInvoiceInformation();
						}
					}
						
					if ($response->isSetResponseMetadata()) {
// 						echo("            ResponseMetadata\n");
						$responseMetadata = $response->getResponseMetadata();
						if ($responseMetadata->isSetRequestId())
						{
// 							echo("                RequestId\n");
// 							echo("                    " . $responseMetadata->getRequestId() . "\n");
							$row['request_id'] = $responseMetadata->getRequestId();
						}
					}
// 					$itemsRow[$row['order_item_id']] = $row;
// 					self::$amazonOrderItemsRow[$row['order_item_id']] = $row;
					$itemsRow[$amazonOrderId][$row['order_item_id']] = $row;
					self::$amazonOrderItemsRow[$amazonOrderId][$row['order_item_id']] = $row;
				}
			}
		}
		return $itemsRow;
	}
}