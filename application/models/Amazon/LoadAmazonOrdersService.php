<?php
/**
 * amazon拉取订单自动服务
 * @author Frank
 * @date 2013-11-12 13:21:19
 */
class Amazon_LoadAmazonOrdersService{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'runListOrders_';
	
	private $_amazonOrderRow = array();
	
	private $_configSecretKey = array(
							'token_id' => null,
							'token' => null,
							'site' => null,
							'seller_id'=>null
							);
	
	
	/**
	 * amazon订单下载
	 * @see Ec_AutoRun::run()
	 */
	public function loadAmazonOrder($user_account,$start,$end,$company_code=''){
        $amazonAccount = $user_account; // 绑定的amazon账户
        $company_code = $company_code ? $company_code : Common_Company::getCompanyCode(); // 公司代码
        $start = $start.''; // 开始时间（美国时间）
        $end = $end.'';				//结束时间（美国）		
		
		$resultPlatformUser = Service_PlatformUser::getByField($amazonAccount,'user_account');
		
		$this->_configSecretKey['token_id'] = $resultPlatformUser["user_token_id"];
		$this->_configSecretKey['token'] = $resultPlatformUser["user_token"];
		$this->_configSecretKey['site'] = $resultPlatformUser["site"];
		$this->_configSecretKey['seller_id'] = $resultPlatformUser["seller_id"];
		
		/*
		 * 3. 创建amazon订单服务
		 */ 
		$ListOrdersService = new Amazon_ListOrdersService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site']);
		$ListOrdersServiceReturn = $ListOrdersService->getListOrders($start, $end);
		
		$nextToken = null;
		if($ListOrdersServiceReturn['ask']){
			$this->convertListOrders($ListOrdersServiceReturn['data']);
			if($ListOrdersServiceReturn['data']->getListOrdersResult()->isSetNextToken()){
				$nextToken = $ListOrdersServiceReturn['data']->getListOrdersResult()->getNextToken();
			}
		}else{
			$errorMessage = "amazon账户： '$amazonAccount',(getListOrders)运行异常->" . print_r($ListOrdersServiceReturn,true);			
			throw new Exception($errorMessage);
		}
		
		/*
		 * 4. 判断是否有nextToken，继续调用
		 */
		while(!empty($nextToken)){
			$ListOrdersByNextTokenServiceReturn = $this->getAmazonOrdersByNextToken($nextToken);
			if($ListOrdersByNextTokenServiceReturn['ask']){
				$this->convertListOrdersByNextToken($ListOrdersByNextTokenServiceReturn['data']);				
				if($ListOrdersByNextTokenServiceReturn['data']->getListOrdersByNextTokenResult()->isSetNextToken()){
					$nextToken = $ListOrdersByNextTokenServiceReturn['data']->getListOrdersByNextTokenResult()->getNextToken();
				}else{
					$nextToken = null;
				}
			}else{
				$errorMessage = "amazon账户： '$amazonAccount',(getAmazonOrdersByNextToken)运行异常->" . print_r($ListOrdersByNextTokenServiceReturn,true);
				throw new Exception($errorMessage);
			}
		}
		
		/*
		 * 5. 检查是否存在数据，校验重复-->保存
		 */
		$addRowNum = 0;
		if(count($this->_amazonOrderRow) > 0){
// 			print_r($this->_amazonOrderRow);
			try{
				foreach ($this->_amazonOrderRow as $orderKey => $orderValue) {
					
					$resultAmazonOrderOriginal = Service_AmazonOrderOriginal::getByField($orderKey,'amazon_order_id');
					if(empty($resultAmazonOrderOriginal)){
						$orderPayMent = array();
						if(isset($orderValue['orderPayment'])){
							$orderPayMent = $orderValue['orderPayment'];
							unset($orderValue['orderPayment']);
							ksort($orderValue);
						}
						//原始订单
						$orderValue['user_account'] = $amazonAccount;
						$orderValue['company_code'] = $company_code;
						$aoo_id = Service_AmazonOrderOriginal::add($orderValue);
						
						if(isset($orderValue['orderPayment'])){
							//付款类型
							foreach ($orderPayMent as $orderPayMentKey => $orderPayMentValue) {
								$orderPayMentValue['aoo_id'] = $aoo_id;
								$orderPayMentValue['amazon_order_id'] = $orderValue['amazon_order_id'];
								Service_AmazonOrderPayment::add($orderPayMentValue);
							}
						}
						$addRowNum += 1;
					}
				}
			}catch(Exception $e){
			    $errorMessage = "amazon账户：'$amazonAccount',下载订单信息出现异常,错误原因：".$e->getMessage();
			    throw new Exception($errorMessage);
			}
		}	
		$return = array();
		$return['ask'] = 1;
		$return['count'] = count($this->_amazonOrderRow);
		$return['data'] = $this->_amazonOrderRow;
		$return['message'] =  "amazon账户：$amazonAccount,已处理: '$start' ~ '$end' 的订单任务完成.";	
		return $return;
	}
	
	/**
	 * 
	 * @param unknown_type $nextToken
	 * @return Ambigous <multitype:number string MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenResponse , multitype:number string Ambigous <number, NULL, string, number, NULL, string, mixed> >
	 */
	private function getAmazonOrdersByNextToken($nextToken){
		$obj = new Amazon_ListOrdersByNextTokenService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site']);
		$return = $obj->getListOrdersByNextToken($nextToken);
		return $return;
	}
	
	/**
	 * 封装ListOrders返回的订单信息
	 * @param unknown_type $listOrdersResponse
	 */
	private function convertListOrders($listOrdersResponse){
		$response = $listOrdersResponse;
		$orderRow = array();
		if ($response->isSetListOrdersResult()) {
			$listOrdersResult = $response->getListOrdersResult();
			if ($listOrdersResult->isSetOrders()) {
				$orders = $listOrdersResult->getOrders();
				$orderList = $orders->getOrder();
				foreach ($orderList as $order) {
					$row = array();
					if ($order->isSetAmazonOrderId())
					{
// 						echo("                            " . $order->getAmazonOrderId() . "\n");
						$row['amazon_order_id'] = $order->getAmazonOrderId();
					}
					if ($order->isSetSellerOrderId())
					{
// 						echo("                            " . $order->getSellerOrderId() . "\n");
						$row['seller_order_id'] = $order->getSellerOrderId();
					}
					if ($order->isSetPurchaseDate())
					{
// 						echo("                            " . $order->getPurchaseDate() . "\n");
						$row['purchase_date'] = $order->getPurchaseDate();
					}
					if ($order->isSetLastUpdateDate())
					{
// 						echo("                            " . $order->getLastUpdateDate() . "\n");
						$row['last_update_date'] = $order->getLastUpdateDate();
					}
					if ($order->isSetOrderStatus())
					{
// 						echo("                            " . $order->getOrderStatus() . "\n");
						$row['order_status'] = $order->getOrderStatus();
					}
					if ($order->isSetFulfillmentChannel())
					{
// 						echo("                            " . $order->getFulfillmentChannel() . "\n");
						$row['fulfillment_channel'] = $order->getFulfillmentChannel();
					}
					if ($order->isSetSalesChannel())
					{
// 						echo("                            " . $order->getSalesChannel() . "\n");
						$row['sales_channel'] = $order->getSalesChannel();
					}
					if ($order->isSetOrderChannel())
					{
// 						echo("                            " . $order->getOrderChannel() . "\n");
						$row['order_channel'] = $order->getOrderChannel();
					}
					if ($order->isSetShipServiceLevel())
					{
// 						echo("                            " . $order->getShipServiceLevel() . "\n");
						$row['ship_service_level'] = $order->getShipServiceLevel();
					}
					if ($order->isSetShippingAddress()) {
						$shippingAddress = $order->getShippingAddress();
						if ($shippingAddress->isSetName())
						{
// 							echo("                                " . $shippingAddress->getName() . "\n");
							$row['shipping_address_name'] = $shippingAddress->getName();
						}
						if ($shippingAddress->isSetAddressLine1())
						{
// 							echo("                                " . $shippingAddress->getAddressLine1() . "\n");
							$row['shipping_address_address1'] = $shippingAddress->getAddressLine1();
						}
						if ($shippingAddress->isSetAddressLine2())
						{
// 							echo("                                " . $shippingAddress->getAddressLine2() . "\n");
							$row['shipping_address_address2'] = $shippingAddress->getAddressLine2();
						}
						if ($shippingAddress->isSetAddressLine3())
						{
// 							echo("                                " . $shippingAddress->getAddressLine3() . "\n");
							$row['shipping_address_address3'] = $shippingAddress->getAddressLine3();
						}
						if ($shippingAddress->isSetCity())
						{
// 							echo("                                " . $shippingAddress->getCity() . "\n");
							$row['shipping_address_city'] = $shippingAddress->getCity();
						}
						if ($shippingAddress->isSetCounty())
						{
// 							echo("                                " . $shippingAddress->getCounty() . "\n");
							$row['shipping_address_county'] = $shippingAddress->getCounty();
						}
						if ($shippingAddress->isSetDistrict())
						{
// 							echo("                                " . $shippingAddress->getDistrict() . "\n");
							$row['shipping_address_district'] = $shippingAddress->getDistrict();
						}
						if ($shippingAddress->isSetStateOrRegion())
						{
// 							echo("                                " . $shippingAddress->getStateOrRegion() . "\n");
							$row['shipping_address_state'] = $shippingAddress->getStateOrRegion();
						}
						if ($shippingAddress->isSetPostalCode())
						{
// 							echo("                                " . $shippingAddress->getPostalCode() . "\n");
							$row['shipping_address_postal_code'] = $shippingAddress->getPostalCode();
						}
						if ($shippingAddress->isSetCountryCode())
						{
// 							echo("                                " . $shippingAddress->getCountryCode() . "\n");
							$row['shipping_address_country_code'] = $shippingAddress->getCountryCode();
						}
						if ($shippingAddress->isSetPhone())
						{
// 							echo("                                " . $shippingAddress->getPhone() . "\n");
							$row['shipping_address_phone'] = $shippingAddress->getPhone();
						}
					}
					if ($order->isSetOrderTotal()) {
						$orderTotal = $order->getOrderTotal();
						if ($orderTotal->isSetCurrencyCode())
						{
// 							echo("                                " . $orderTotal->getCurrencyCode() . "\n");
							$row['currency_code'] = $orderTotal->getCurrencyCode();
						}
						if ($orderTotal->isSetAmount())
						{
// 							echo("                                " . $orderTotal->getAmount() . "\n");
							$row['amount'] = $orderTotal->getAmount();
						}
					}
					if ($order->isSetNumberOfItemsShipped())
					{
// 						echo("                            " . $order->getNumberOfItemsShipped() . "\n");
						$row['number_items_shipped'] = $order->getNumberOfItemsShipped();
					}
					if ($order->isSetNumberOfItemsUnshipped())
					{
// 						echo("                            " . $order->getNumberOfItemsUnshipped() . "\n");
						$row['number_items_unshipped'] = $order->getNumberOfItemsUnshipped();
					}
					if ($order->isSetPaymentExecutionDetail()) {
						$paymentExecutionDetail = $order->getPaymentExecutionDetail();
						$paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem();
						$row['orderPayment'] = array();
						foreach ($paymentExecutionDetailItemList as $paymentExecutionDetailItem) {
							$rowPayment = array();
							if ($paymentExecutionDetailItem->isSetPayment()) {
								$payment = $paymentExecutionDetailItem->getPayment();
								if ($payment->isSetCurrencyCode())
								{
// 									echo("                                        " . $payment->getCurrencyCode() . "\n");
									$rowPayment['currency_code'] = $payment->getCurrencyCode();
								}
								if ($payment->isSetAmount())
								{
// 									echo("                                        " . $payment->getAmount() . "\n");
									$rowPayment['amount'] = $payment->getAmount();
								}
							}
							if ($paymentExecutionDetailItem->isSetPaymentMethod())
							{
// 								echo("                                    " . $paymentExecutionDetailItem->getPaymentMethod() . "\n");
								$rowPayment['payment_method'] = $paymentExecutionDetailItem->getPaymentMethod();
							}
							$row['orderPayment'][] = $rowPayment;
						}
					}
					if ($order->isSetPaymentMethod())
					{
// 						echo("                            " . $order->getPaymentMethod() . "\n");
						$row['payment_method'] = $order->getPaymentMethod();
					}
					if ($order->isSetMarketplaceId())
					{
// 						echo("                            " . $order->getMarketplaceId() . "\n");
						$row['marketplace_id'] = $order->getMarketplaceId();
					}
					if ($order->isSetBuyerEmail())
					{
// 						echo("                            " . $order->getBuyerEmail() . "\n");
						$row['buyer_email'] = $order->getBuyerEmail();
					}
					if ($order->isSetBuyerName())
					{
// 						echo("                            " . $order->getBuyerName() . "\n");
						$row['buyer_name'] = $order->getBuyerName();
					}else{
						$row['buyer_name'] = '';
					}
					if ($order->isSetShipmentServiceLevelCategory())
					{
// 						echo("                            " . $order->getShipmentServiceLevelCategory() . "\n");
						$row['shipment_service_level_category'] = $order->getShipmentServiceLevelCategory();
					}
					if ($order->isSetShippedByAmazonTFM())
					{
// 						echo("                            " . $order->getShippedByAmazonTFM() . "\n");
						$row['shipped_amazon_tfm'] = $order->getShippedByAmazonTFM();
					}
					if ($order->isSetTFMShipmentStatus())
					{
// 						echo("                            " . $order->getTFMShipmentStatus() . "\n");
						$row['tfm_shipment_status'] = $order->getTFMShipmentStatus();
					}
					if ($response->isSetResponseMetadata()) {
						$responseMetadata = $response->getResponseMetadata();
						if ($responseMetadata->isSetRequestId())
						{
// 							echo("                    " . $responseMetadata->getRequestId() . "\n");
							$row['request_id'] = $responseMetadata->getRequestId();
						}
					}
					$orderRow[$row['amazon_order_id']] = $row;
					$this->_amazonOrderRow[$row['amazon_order_id']] = $row;
				}
			}
		}
		return $orderRow;
	}
	
	/**
	 * 封装ListOrdersByNextToken返回的订单信息
	 * @param unknown_type $listOrdersResponse
	 */
	private function convertListOrdersByNextToken($listOrdersByNextTokenResponse){
		$response = $listOrdersByNextTokenResponse;
		$orderRow = array();
		if ($response->isSetListOrdersByNextTokenResult()) {
			$listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult();
			if ($listOrdersByNextTokenResult->isSetOrders ()) {
				$orders = $listOrdersByNextTokenResult->getOrders ();
				$orderList = $orders->getOrder ();
				foreach ( $orderList as $order ) {
					$row = array();
					if ($order->isSetAmazonOrderId ()) {
// 						echo ("                            " . $order->getAmazonOrderId () . "\n");
						$row['amazon_order_id'] = $order->getAmazonOrderId();
					}
					if ($order->isSetSellerOrderId ()) {
// 						echo ("                            " . $order->getSellerOrderId () . "\n");
						$row['seller_order_id'] = $order->getSellerOrderId();
					}
					if ($order->isSetPurchaseDate ()) {
// 						echo ("                            " . $order->getPurchaseDate () . "\n");
						$row['purchase_date'] = $order->getPurchaseDate();
					}
					if ($order->isSetLastUpdateDate ()) {
// 						echo ("                            " . $order->getLastUpdateDate () . "\n");
						$row['last_update_date'] = $order->getLastUpdateDate();
					}
					if ($order->isSetOrderStatus ()) {
// 						echo ("                            " . $order->getOrderStatus () . "\n");
						$row['order_status'] = $order->getOrderStatus();
					}
					if ($order->isSetFulfillmentChannel ()) {
// 						echo ("                            " . $order->getFulfillmentChannel () . "\n");
						$row['fulfillment_channel'] = $order->getFulfillmentChannel();
					}
					if ($order->isSetSalesChannel ()) {
// 						echo ("                            " . $order->getSalesChannel () . "\n");
						$row['sales_channel'] = $order->getSalesChannel();
					}
					if ($order->isSetOrderChannel ()) {
// 						echo ("                            " . $order->getOrderChannel () . "\n");
						$row['order_channel'] = $order->getOrderChannel();
					}
					if ($order->isSetShipServiceLevel ()) {
// 						echo ("                            " . $order->getShipServiceLevel () . "\n");
						$row['ship_service_level'] = $order->getShipServiceLevel();
					}
					if ($order->isSetShippingAddress ()) {
						$shippingAddress = $order->getShippingAddress ();
						if ($shippingAddress->isSetName ()) {
// 							echo ("                                " . $shippingAddress->getName () . "\n");
							$row['shipping_address_name'] = $shippingAddress->getName();
						}
						if ($shippingAddress->isSetAddressLine1 ()) {
// 							echo ("                                " . $shippingAddress->getAddressLine1 () . "\n");
							$row['shipping_address_address1'] = $shippingAddress->getAddressLine1();
						}
						if ($shippingAddress->isSetAddressLine2 ()) {
// 							echo ("                                " . $shippingAddress->getAddressLine2 () . "\n");
							$row['shipping_address_address2'] = $shippingAddress->getAddressLine2();
						}
						if ($shippingAddress->isSetAddressLine3 ()) {
// 							echo ("                                " . $shippingAddress->getAddressLine3 () . "\n");
							$row['shipping_address_address3'] = $shippingAddress->getAddressLine3();
						}
						if ($shippingAddress->isSetCity ()) {
// 							echo ("                                " . $shippingAddress->getCity () . "\n");
							$row['shipping_address_city'] = $shippingAddress->getCity();
						}
						if ($shippingAddress->isSetCounty ()) {
// 							echo ("                                " . $shippingAddress->getCounty () . "\n");
							$row['shipping_address_county'] = $shippingAddress->getCounty();
						}
						if ($shippingAddress->isSetDistrict ()) {
// 							echo ("                                " . $shippingAddress->getDistrict () . "\n");
							$row['shipping_address_district'] = $shippingAddress->getDistrict();
						}
						if ($shippingAddress->isSetStateOrRegion ()) {
// 							echo ("                                " . $shippingAddress->getStateOrRegion () . "\n");
							$row['shipping_address_state'] = $shippingAddress->getStateOrRegion();
						}
						if ($shippingAddress->isSetPostalCode ()) {
// 							echo ("                                " . $shippingAddress->getPostalCode () . "\n");
							$row['shipping_address_postal_code'] = $shippingAddress->getPostalCode();
						}
						if ($shippingAddress->isSetCountryCode ()) {
// 							echo ("                                " . $shippingAddress->getCountryCode () . "\n");
							$row['shipping_address_country_code'] = $shippingAddress->getCountryCode();
						}
						if ($shippingAddress->isSetPhone ()) {
// 							echo ("                                " . $shippingAddress->getPhone () . "\n");
							$row['shipping_address_phone'] = $shippingAddress->getPhone();
						}
					}
					if ($order->isSetOrderTotal ()) {
						$orderTotal = $order->getOrderTotal ();
						if ($orderTotal->isSetCurrencyCode ()) {
// 							echo ("                                " . $orderTotal->getCurrencyCode () . "\n");
							$row['currency_code'] = $orderTotal->getCurrencyCode();
						}
						if ($orderTotal->isSetAmount ()) {
// 							echo ("                                " . $orderTotal->getAmount () . "\n");
							$row['amount'] = $orderTotal->getAmount();
						}
					}
					if ($order->isSetNumberOfItemsShipped ()) {
// 						echo ("                            " . $order->getNumberOfItemsShipped () . "\n");
						$row['number_items_shipped'] = $order->getNumberOfItemsShipped();
					}
					if ($order->isSetNumberOfItemsUnshipped ()) {
// 						echo ("                            " . $order->getNumberOfItemsUnshipped () . "\n");
						$row['number_items_unshipped'] = $order->getNumberOfItemsUnshipped();
					}
					if ($order->isSetPaymentExecutionDetail ()) {
						$paymentExecutionDetail = $order->getPaymentExecutionDetail ();
						$paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem();
						$row['orderPayment'] = array();
						foreach ( $paymentExecutionDetailItemList as $paymentExecutionDetailItem ) {
							$rowPayment = array();
							if ($paymentExecutionDetailItem->isSetPayment ()) {
								$payment = $paymentExecutionDetailItem->getPayment ();
								if ($payment->isSetCurrencyCode ()) {
// 									echo ("                                        " . $payment->getCurrencyCode() . "\n");
									$rowPayment['currency_code'] = $payment->getCurrencyCode();
								}
								if ($payment->isSetAmount ()) {
// 									echo ("                                        " . $payment->getAmount () . "\n");
									$rowPayment['amount'] = $payment->getAmount();
								}
							}
							if ($paymentExecutionDetailItem->isSetPaymentMethod ()) {
// 								echo ("                                    " . $paymentExecutionDetailItem->getPaymentMethod() . "\n");
								$rowPayment['payment_method'] = $paymentExecutionDetailItem->getPaymentMethod();
							}
							$row['orderPayment'][] = $rowPayment;
						}
					}
					if ($order->isSetPaymentMethod ()) {
// 						echo ("                            " . $order->getPaymentMethod () . "\n");
						$row['payment_method'] = $order->getPaymentMethod();
					}
					if ($order->isSetMarketplaceId ()) {
// 						echo ("                            " . $order->getMarketplaceId () . "\n");
						$row['marketplace_id'] = $order->getMarketplaceId();
					}
					if ($order->isSetBuyerEmail ()) {
// 						echo ("                            " . $order->getBuyerEmail () . "\n");
						$row['buyer_email'] = $order->getBuyerEmail();
					}
					if ($order->isSetBuyerName ()) {
// 						echo ("                            " . $order->getBuyerName () . "\n");
						$row['buyer_name'] = $order->getBuyerName();
					}
					if ($order->isSetShipmentServiceLevelCategory ()) {
// 						echo ("                            " . $order->getShipmentServiceLevelCategory () . "\n");
						$row['shipment_service_level_category'] = $order->getShipmentServiceLevelCategory();
					}
					if ($order->isSetShippedByAmazonTFM ()) {
// 						echo ("                            " . $order->getShippedByAmazonTFM () . "\n");
						$row['shipped_amazon_tfm'] = $order->getShippedByAmazonTFM();
					}
					if ($order->isSetTFMShipmentStatus ()) {
// 						echo ("                            " . $order->getTFMShipmentStatus () . "\n");
						$row['tfm_shipment_status'] = $order->getTFMShipmentStatus();
					}
					if ($response->isSetResponseMetadata ()) {
						$responseMetadata = $response->getResponseMetadata ();
						if ($responseMetadata->isSetRequestId ()) {
// 							echo ("                    " . $responseMetadata->getRequestId () . "\n");
							$row['request_id'] = $responseMetadata->getRequestId();
						}
					}
					$orderRow[$row['amazon_order_id']] = $row;
					$this->_amazonOrderRow[$row['amazon_order_id']] = $row;
				}
			}
		}
		return $orderRow;
	}
}