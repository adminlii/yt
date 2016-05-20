<?php
require_once ('MarketplaceWebServiceOrders/Client.php');

/**
 * amazon拉取订单服务类
 * @author Frank
 * @date 2013-11-8 13:21:19
 */
class Amazon_ListOrdersService {
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'amazonListOrders_';
	
	/**
	 * 接口名称
	 * @var unknown_type
	 */
	const INTERFACE_NAME = 'ListOrders';
	
	/**
	 * 路径
	 * @var unknown_type
	 */
	const SERVICE_PATH = '/Orders/';
	
	/**
	 * 请求上限
	 * @var unknown_type
	 */
	const REQUEST_MAX = 6;
	
	/**
	 * 恢复个数
	 * @var unknown_type
	 */
	const RESTORE_NUM = 1;

	/**
	 * 恢复时间
	 * @var unknown_type
	 */
	const RESTORE_TIME_NUM = 1;
	/**
	 * 恢复时间单位
	 */
	const RESTORE_TIME_UNIT = 'minute';
	
	
	/**
	 * 秘钥信息
	 * @var unknown_type
	 */
	private $_tokenConfig = array ('AWS_ACCESS_KEY_ID' => null,
							'AWS_SECRET_ACCESS_KEY' => null,
							'MERCHANT_ID' => null,
							'SITE'=>null,
							'APPLICATION_NAME' => null,
							'APPLICATION_VERSION' => null,
							);
	
	/**
	 * 构造器
	 */
	public function __construct($token_id , $token , $saller_id , $site)
	{
		//访问秘钥ID
		$this->_tokenConfig['AWS_ACCESS_KEY_ID'] = $token_id;
		//访问秘钥
		$this->_tokenConfig['AWS_SECRET_ACCESS_KEY'] = $token;
		//销售ID
		$this->_tokenConfig['MERCHANT_ID'] = $saller_id;
		//站点
		$this->_tokenConfig['SITE'] = $site;
		//应用名称
		$this->_tokenConfig['APPLICATION_NAME'] = Amazon_AmazonLib::APPLICATION_NAME;
		//应用版本
		$this->_tokenConfig['APPLICATION_VERSION'] = Amazon_AmazonLib::APPLICATION_VERSION;
	}
	
	/**
	 * 获得亚马逊订单
	 * @param unknown_type $startDate	开始时间
	 * @param unknown_type $endDate		结束时间
	 */
	public function getListOrders($startDate, $endDate){
		$return = array(
				'ask'=>0,
				'message'=>'',
				'data'=>''
				);
		
		/*
		 * 秘钥
		 */
// 		$this->getAmazonKey();
		$countryCode = $this->_tokenConfig['SITE'];
		/*
		 * 1. 检查接口运行情况
		 */
		$returnCheck = Amazon_AmazonLib::checkAmazonRunControl(self::INTERFACE_NAME, $this->_tokenConfig['MERCHANT_ID'], $countryCode, self::REQUEST_MAX);
		
		if(!$returnCheck['ask']){
			$return['message'] = $returnCheck['message'];
			return $return;
		}
		
		/*
		 * 2. 取得亚马逊站点、地址
		 */
		$amazonConfig = Amazon_AmazonLib::getAmazonConfig();
		if(empty($amazonConfig[$countryCode])){
			$return['message'] = "amzon站点： $countryCode ，未能找到对应的亚马逊服务地址及商城编号.";
			return $return;
		}
		
		/*
		 * 3. 初始化配置信息，创建request对象
		 */
		$serviceUrl = $amazonConfig[$countryCode]['service_url'] . self::SERVICE_PATH . Amazon_AmazonLib::SERVICE_VERSION;
		$curlConfig = array (
				'ServiceURL' => $serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		$service = new MarketplaceWebServiceOrders_Client(
				$this->_tokenConfig['AWS_ACCESS_KEY_ID'],
				$this->_tokenConfig['AWS_SECRET_ACCESS_KEY'],
				$this->_tokenConfig['APPLICATION_NAME'],
				$this->_tokenConfig['APPLICATION_VERSION'],
				$curlConfig);
		$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
		
		//商家ID
		$request->setSellerId($this->_tokenConfig['MERCHANT_ID']);
			
		//创建时间
// 		$request->setCreatedAfter(new DateTime($startDate, new DateTimeZone('UTC')));
// 		$request->setCreatedBefore(new DateTime($endDate, new DateTimeZone('UTC')));
 		//最后修改时间
		$request->setLastUpdatedAfter(new DateTime($startDate, new DateTimeZone('UTC')));
		$request->setLastUpdatedBefore(new DateTime($endDate, new DateTimeZone('UTC')));
		$request->setMaxResultsPerPage(100);

		//订单状态
 		$orderStatus = new MarketplaceWebServiceOrders_Model_OrderStatusList();
 		$orderStatus->setStatus(array('Unshipped','PartiallyShipped','Shipped'));//Shipped
 		$request->setOrderStatus($orderStatus);
 		/*
 		以下列表向您显示有效的订单状态值：
 		PendingAvailability
 		只有预订订单才有此状态。订单已生成，但是付款未授权，且商品的发售日期是将来的某一天。 订单尚不能进行发货。请注意：仅在日本 (JP)，Preorder 才是 OrderType 的一个可能的值。
 		Pending
 		订单已生成，但是付款未授权。订单尚不能进行发货。 请注意，对于 OrderType = Standard 的订单，初始的订单状态是 Pending。 对于 OrderType = Preorder 的订单（仅适用于 JP） 初始的订单状态是 PendingAvailability，且当进入付款授权流程时， 订单状态将变为 Pending。
 		Unshipped
 		付款已经过授权，订单已准备好进行发货，但订单中商品尚未发运。
 		PartiallyShipped
 		订单中的一个或多个（但并非全部）商品已经发货。
 		Shipped
 		订单中的所有商品均已发货。
 		InvoiceUnconfirmed
 		订单内所有的商品都已发货，但是卖家还没有向亚马逊确认已经向买家寄出发票。 请注意：此参数仅适用于中国地区。
 		Canceled
 		订单已取消。
 		Unfulfillable
 		订单无法进行配送。该状态仅适用于通过亚马逊零售网站之外的渠道下达但由亚马逊进行配送的订单。
 		在此版本的“订单 API”部分中，必须同时使用未发货和已部分发货。仅使用其中一个状态值，则会返回错误。
 		
 		默认值：全部
 		*/
 		
		
		//商城代码
		$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
		$marketplace_id = $amazonConfig[$countryCode]['marketplace_id'];
		$marketplaceIdList->setId(array($marketplace_id));
		$request->setMarketplaceId($marketplaceIdList);
		
		
// 		//订单号
// 		$request->setSellerOrderId('卖家订单号');

		
// 		print_r($amazonConfig);
// 		print_r($this->_tokenConfig);	
// 		print_r($curlConfig);
// 		print_r($marketplace_id);
// 		print_r($request);
// 		exit;
		//调用
// 		$data = $this->callAmazonListOrders($service, $request);
		try{
			$response = $service->listOrders($request);
			$return['ask'] = 1;
			$return['data'] = $response;
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 2);
		} catch (MarketplaceWebServiceOrders_Exception $ex) {
			$errorMessage = '错误代码：' . $ex->getErrorCode() . '--请求ID：' . $ex->getRequestId() . '--错误信息：' . print_r($ex,true);
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 处理：$startDate ~~ $endDate 时间段内的订单任务发生异常,错误原因：" . print_r($ex,true), self::$log_name);
		}catch (Exception $e){
			$errorMessage = '错误代码：' . $e->getCode() . '--错误信息：' . $e->getMessage();
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 处理：$startDate ~~ $endDate 时间段内的订单任务发生异常,错误原因：" . print_r($e,true), self::$log_name);
		}
		return $return;
	}
	
	/**
	 * 发送请求
	 * @param MarketplaceWebServiceOrders_Interface $service
	 * @param unknown_type $request
	 */
	public function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
	{
		try {
			$response = $service->listOrders($request);
	
			echo ("Service Response\n");
			echo ("=============================================================================\n");
	
			echo("        ListOrdersResponse\n");
			if ($response->isSetListOrdersResult()) {
				echo("            ListOrdersResult\n");
				$listOrdersResult = $response->getListOrdersResult();
				if ($listOrdersResult->isSetNextToken())
				{
					echo("                NextToken\n");
					echo("                    " . $listOrdersResult->getNextToken() . "\n");
				}
				if ($listOrdersResult->isSetCreatedBefore())
				{
					echo("                CreatedBefore\n");
					echo("                    " . $listOrdersResult->getCreatedBefore() . "\n");
				}
				if ($listOrdersResult->isSetLastUpdatedBefore())
				{
					echo("                LastUpdatedBefore\n");
					echo("                    " . $listOrdersResult->getLastUpdatedBefore() . "\n");
				}
				if ($listOrdersResult->isSetOrders()) {
					echo("                Orders\n");
					$orders = $listOrdersResult->getOrders();
					$orderList = $orders->getOrder();
					foreach ($orderList as $order) {
						echo("                    Order\n");
						if ($order->isSetAmazonOrderId())
						{
							echo("                        AmazonOrderId\n");
							echo("                            " . $order->getAmazonOrderId() . "\n");
						}
						if ($order->isSetSellerOrderId())
						{
							echo("                        SellerOrderId\n");
							echo("                            " . $order->getSellerOrderId() . "\n");
						}
						if ($order->isSetPurchaseDate())
						{
							echo("                        PurchaseDate\n");
							echo("                            " . $order->getPurchaseDate() . "\n");
						}
						if ($order->isSetLastUpdateDate())
						{
							echo("                        LastUpdateDate\n");
							echo("                            " . $order->getLastUpdateDate() . "\n");
						}
						if ($order->isSetOrderStatus())
						{
							echo("                        OrderStatus\n");
							echo("                            " . $order->getOrderStatus() . "\n");
						}
						if ($order->isSetFulfillmentChannel())
						{
							echo("                        FulfillmentChannel\n");
							echo("                            " . $order->getFulfillmentChannel() . "\n");
						}
						if ($order->isSetSalesChannel())
						{
							echo("                        SalesChannel\n");
							echo("                            " . $order->getSalesChannel() . "\n");
						}
						if ($order->isSetOrderChannel())
						{
							echo("                        OrderChannel\n");
							echo("                            " . $order->getOrderChannel() . "\n");
						}
						if ($order->isSetShipServiceLevel())
						{
							echo("                        ShipServiceLevel\n");
							echo("                            " . $order->getShipServiceLevel() . "\n");
						}
						if ($order->isSetShippingAddress()) {
							echo("                        ShippingAddress\n");
							$shippingAddress = $order->getShippingAddress();
							if ($shippingAddress->isSetName())
							{
								echo("                            Name\n");
								echo("                                " . $shippingAddress->getName() . "\n");
							}
							if ($shippingAddress->isSetAddressLine1())
							{
								echo("                            AddressLine1\n");
								echo("                                " . $shippingAddress->getAddressLine1() . "\n");
							}
							if ($shippingAddress->isSetAddressLine2())
							{
								echo("                            AddressLine2\n");
								echo("                                " . $shippingAddress->getAddressLine2() . "\n");
							}
							if ($shippingAddress->isSetAddressLine3())
							{
								echo("                            AddressLine3\n");
								echo("                                " . $shippingAddress->getAddressLine3() . "\n");
							}
							if ($shippingAddress->isSetCity())
							{
								echo("                            City\n");
								echo("                                " . $shippingAddress->getCity() . "\n");
							}
							if ($shippingAddress->isSetCounty())
							{
								echo("                            County\n");
								echo("                                " . $shippingAddress->getCounty() . "\n");
							}
							if ($shippingAddress->isSetDistrict())
							{
								echo("                            District\n");
								echo("                                " . $shippingAddress->getDistrict() . "\n");
							}
							if ($shippingAddress->isSetStateOrRegion())
							{
								echo("                            StateOrRegion\n");
								echo("                                " . $shippingAddress->getStateOrRegion() . "\n");
							}
							if ($shippingAddress->isSetPostalCode())
							{
								echo("                            PostalCode\n");
								echo("                                " . $shippingAddress->getPostalCode() . "\n");
							}
							if ($shippingAddress->isSetCountryCode())
							{
								echo("                            CountryCode\n");
								echo("                                " . $shippingAddress->getCountryCode() . "\n");
							}
							if ($shippingAddress->isSetPhone())
							{
								echo("                            Phone\n");
								echo("                                " . $shippingAddress->getPhone() . "\n");
							}
						}
						if ($order->isSetOrderTotal()) {
							echo("                        OrderTotal\n");
							$orderTotal = $order->getOrderTotal();
							if ($orderTotal->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $orderTotal->getCurrencyCode() . "\n");
							}
							if ($orderTotal->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $orderTotal->getAmount() . "\n");
							}
						}
						if ($order->isSetNumberOfItemsShipped())
						{
							echo("                        NumberOfItemsShipped\n");
							echo("                            " . $order->getNumberOfItemsShipped() . "\n");
						}
						if ($order->isSetNumberOfItemsUnshipped())
						{
							echo("                        NumberOfItemsUnshipped\n");
							echo("                            " . $order->getNumberOfItemsUnshipped() . "\n");
						}
						if ($order->isSetPaymentExecutionDetail()) {
							echo("                        PaymentExecutionDetail\n");
							$paymentExecutionDetail = $order->getPaymentExecutionDetail();
							$paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem();
							foreach ($paymentExecutionDetailItemList as $paymentExecutionDetailItem) {
								echo("                            PaymentExecutionDetailItem\n");
								if ($paymentExecutionDetailItem->isSetPayment()) {
									echo("                                Payment\n");
									$payment = $paymentExecutionDetailItem->getPayment();
									if ($payment->isSetCurrencyCode())
									{
										echo("                                    CurrencyCode\n");
										echo("                                        " . $payment->getCurrencyCode() . "\n");
									}
									if ($payment->isSetAmount())
									{
										echo("                                    Amount\n");
										echo("                                        " . $payment->getAmount() . "\n");
									}
								}
								if ($paymentExecutionDetailItem->isSetPaymentMethod())
								{
									echo("                                PaymentMethod\n");
									echo("                                    " . $paymentExecutionDetailItem->getPaymentMethod() . "\n");
								}
							}
						}
						if ($order->isSetPaymentMethod())
						{
							echo("                        PaymentMethod\n");
							echo("                            " . $order->getPaymentMethod() . "\n");
						}
						if ($order->isSetMarketplaceId())
						{
							echo("                        MarketplaceId\n");
							echo("                            " . $order->getMarketplaceId() . "\n");
						}
						if ($order->isSetBuyerEmail())
						{
							echo("                        BuyerEmail\n");
							echo("                            " . $order->getBuyerEmail() . "\n");
						}
						if ($order->isSetBuyerName())
						{
							echo("                        BuyerName\n");
							echo("                            " . $order->getBuyerName() . "\n");
						}
						if ($order->isSetShipmentServiceLevelCategory())
						{
							echo("                        ShipmentServiceLevelCategory\n");
							echo("                            " . $order->getShipmentServiceLevelCategory() . "\n");
						}
						if ($order->isSetShippedByAmazonTFM())
						{
							echo("                        ShippedByAmazonTFM\n");
							echo("                            " . $order->getShippedByAmazonTFM() . "\n");
						}
						if ($order->isSetTFMShipmentStatus())
						{
							echo("                        TFMShipmentStatus\n");
							echo("                            " . $order->getTFMShipmentStatus() . "\n");
						}
					}
				}
			}
			if ($response->isSetResponseMetadata()) {
				echo("            ResponseMetadata\n");
				$responseMetadata = $response->getResponseMetadata();
				if ($responseMetadata->isSetRequestId())
				{
					echo("                RequestId\n");
					echo("                    " . $responseMetadata->getRequestId() . "\n");
				}
			}
	
			echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
		} catch (MarketplaceWebServiceOrders_Exception $ex) {
			echo("Caught Exception: " . $ex->getMessage() . "\n");
			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
			echo("Error Code: " . $ex->getErrorCode() . "\n");
			echo("Error Type: " . $ex->getErrorType() . "\n");
			echo("Request ID: " . $ex->getRequestId() . "\n");
			echo("XML: " . $ex->getXML() . "\n");
			echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
		}
	}
}