<?php
/**
 * 订单列表
 * @author max
 *
 */
class Amazon_Order_OrderService {
	protected $_service = null;
	protected $_company_code = '';
	protected $_user_account = '';
	protected $_MarketplaceId = '';
	protected $_config = array ();
	protected $_RequestId = '';
	protected $_nextToken = '';
	protected $_hasNext = false;
	protected $_err = array ();
	protected $_success = array ();
	protected $_responseArr = array ();
	protected $_reportRequestInfoArr = array ();
	protected $_orderArr = array ();
	protected $_orderObjectArr = array ();
	protected $_exceptionArr = array ();
	
	/**
	 * 记录日志
	 *
	 * @param unknown_type $ex        	
	 */
	protected function logException($ex) {
		$exception = array (
				"Caught Exception: " . $ex->getMessage (),
				"Response Status Code: " . $ex->getStatusCode (),
				"Error Code: " . $ex->getErrorCode (),
				"Error Type: " . $ex->getErrorType (),
				"Request ID: " . $ex->getRequestId (),
				"XML: " . $ex->getXML (),
				"ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata () 
		);
		Amazon_Service::log ( print_r ( $exception, true ) );
		$this->_exceptionArr [] = $exception;
		return $exception;
	}
	
	/**
	 * 构造器
	 */
	public function __construct($token_id, $token, $seller_id, $site) {
		
		// 访问秘钥ID
		$this->_tokenConfig ['AWS_ACCESS_KEY_ID'] = $token_id;
		// 访问秘钥
		$this->_tokenConfig ['AWS_SECRET_ACCESS_KEY'] = $token;
		// 销售ID
		$this->_tokenConfig ['MERCHANT_ID'] = $seller_id;
		// 站点
		$this->_tokenConfig ['SITE'] = $site;
		// 应用名称
		$this->_tokenConfig ['APPLICATION_NAME'] = Amazon_AmazonLib::APPLICATION_NAME;
		// 应用版本
		$this->_tokenConfig ['APPLICATION_VERSION'] = Amazon_AmazonLib::APPLICATION_VERSION;
		/*
		 * 秘钥
		 */
		$countryCode = $this->_tokenConfig ['SITE'];
		
		/*
		 * 2. 取得亚马逊站点、地址
		 */
		$amazonConfig = Amazon_AmazonLib::getAmazonConfig ();
		if (empty ( $amazonConfig [$countryCode] )) {
			throw new Exception ( "amzon站点： $countryCode ，未能找到对应的亚马逊服务地址及商城编号." );
		}
		$this->_MarketplaceId = $amazonConfig [$countryCode] ['marketplace_id'];
		/*
		 * 3. 初始化配置信息，创建request对象
		 */
		$serviceUrl = $amazonConfig [$countryCode] ['service_url'] . '/Orders/' . Amazon_AmazonLib::SERVICE_VERSION;
		// echo $serviceUrl;exit;
		$config = array (
				'ServiceURL' => $serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => - 1,
				'MaxErrorRetry' => 3 
		);
		$this->_config = $config;
		$service = new MarketplaceWebServiceOrders_Client ( $this->_tokenConfig ['AWS_ACCESS_KEY_ID'], $this->_tokenConfig ['AWS_SECRET_ACCESS_KEY'], $this->_tokenConfig ['APPLICATION_NAME'], $this->_tokenConfig ['APPLICATION_VERSION'], $config );
		
		$this->_service = $service;
	}
	public function setCompanyCode($company_code) {
		$this->_company_code = $company_code;
	}
	public function setUserAccount($user_account) {
		$this->_user_account = $user_account;
	}
	public function getOrderByOrderIdArr($idArr) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$idArr = array_unique ( $idArr );
			sort ( $idArr );
			if (count ( $idArr ) > 50) {
				throw new Exception ( '数量超出50个' );
			}
			$request = new MarketplaceWebServiceOrders_Model_GetOrderRequest ();
			$request->setSellerId ( $this->_tokenConfig ['MERCHANT_ID'] );
			$list = new MarketplaceWebServiceOrders_Model_OrderIdList ();
			
			$list->withId ( $idArr );
			$request->withAmazonOrderId ( $list );
			
			// print_r($request->getAmazonOrderId()) ;exit;
			$this->invokeGetOrder ( $request );

			$return ['ask'] = 1;
			$return ['message'] = 'Success';
		} catch ( MarketplaceWebServiceOrders_Exception $ex ) {
			// 记录日志
			$this->logException ( $ex );
			$return ['message'] = "Amazon请求失败，" . $ex->getMessage ();
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		Ec::showError ( print_r ( $this->_orderArr, true ), '__amazon_order_info' );
		$return ['err'] = $this->_err;
		$return ['excepton'] = $this->_exceptionArr;
		$return ['orderArr'] = $this->_orderArr;
		return $return;
	}
	public function getOrderList($start, $end) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest ();
			$request->setSellerId ( $this->_tokenConfig ['MERCHANT_ID'] );
			
			$request->setLastUpdatedAfter(new DateTime ( $start, new DateTimeZone ( 'UTC' ) ) );
			$request->setLastUpdatedBefore ( new DateTime ( $end, new DateTimeZone ( 'UTC' ) ) );

 
// 			exit;
			$request->setMaxResultsPerPage ( 100 );
			
			// 订单状态
			// $orderStatus = new MarketplaceWebServiceOrders_Model_OrderStatusList
			// ();
			// $orderStatus->setStatus ( array (
			// 'Unshipped',
			// 'PartiallyShipped',
			// 'Shipped'
			// ) ); // Shipped
			// $request->setOrderStatus ( $orderStatus );
			
			// 商城代码
			$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList ();
			$marketplaceIdList->setId ( array (
					$this->_MarketplaceId 
			) );
			$request->setMarketplaceId ( $marketplaceIdList );
			
			// print_r($request->getAmazonOrderId()) ;exit;
			$this->invokeListOrders ( $request );
			// 下载下一页
			$this->getOrderListNextData ();
			
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
		} catch ( MarketplaceWebServiceOrders_Exception $ex ) {
			// 记录日志
			$this->logException ( $ex );
			$return ['message'] = "Amazon请求失败，".$ex->getMessage ();
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		
		Ec::showError ( print_r ( $this->_orderArr, true ), '__amazon_order_info' );
		$return ['err'] = $this->_err;
		$return ['excepton'] = $this->_exceptionArr;
		$return ['orderArr'] = $this->_orderArr;
		return $return;
	}
	/**
	 * Get Order Action Sample
	 * This operation takes up to 50 order ids and returns the corresponding
	 * orders.
	 *
	 * @param MarketplaceWebServiceOrders_Client $service
	 *        	instance of MarketplaceWebServiceOrders_Client
	 * @param mixed $request
	 *        	MarketplaceWebServiceOrders_Model_GetOrder or array of
	 *        	parameters
	 */
	protected function invokeGetOrder($request) {
		$service = $this->_service;
		$response = $service->getOrder ( $request );
		
		Common_ApiProcess::log ( "Service Response" );
		Common_ApiProcess::log ( "=============================================================================" );
		
		Common_ApiProcess::log ( "        GetOrderResponse" );
		if ($response->isSetGetOrderResult ()) {
			Common_ApiProcess::log ( "            GetOrderResult" );
			$getOrderResult = $response->getGetOrderResult ();
			if ($getOrderResult->isSetOrders ()) {
				Common_ApiProcess::log ( "                Orders" );
				$orders = $getOrderResult->getOrders ();
				$orderList = $orders->getOrder ();
				foreach ( $orderList as $order ) {
					$this->_saveOrderInfo ( $order );
				}
			}
		}
		$this->_responseMetadata ( $response );
	}
	
	/**
	 * 发送请求
	 *
	 * @param MarketplaceWebServiceOrders_Client $service        	
	 * @param unknown_type $request        	
	 */
	protected function invokeListOrders($request) {
		$this->_hasNext = false;
		$this->_nextToken = '';
		$service = $this->_service;
		$response = $service->listOrders ( $request );
		
		Common_ApiProcess::log ( "Service Response" );
		Common_ApiProcess::log ( "=============================================================================" );
		
		Common_ApiProcess::log ( "        ListOrdersResponse" );
		if ($response->isSetListOrdersResult ()) {
			Common_ApiProcess::log ( "            ListOrdersResult" );
			$listOrdersResult = $response->getListOrdersResult ();
			if ($listOrdersResult->isSetNextToken ()) {
				$this->_hasNext = true;
				$this->_nextToken = $listOrdersResult->getNextToken ();
				Common_ApiProcess::log ( "                NextToken" );
				Common_ApiProcess::log ( "                    " . $listOrdersResult->getNextToken () );
			}
			if ($listOrdersResult->isSetCreatedBefore ()) {
				Common_ApiProcess::log ( "                CreatedBefore" );
				Common_ApiProcess::log ( "                    " . $listOrdersResult->getCreatedBefore () );
			}
			if ($listOrdersResult->isSetLastUpdatedBefore ()) {
				Common_ApiProcess::log ( "                LastUpdatedBefore" );
				Common_ApiProcess::log ( "                    " . $listOrdersResult->getLastUpdatedBefore () );
			}
			if ($listOrdersResult->isSetOrders ()) {
				Common_ApiProcess::log ( "                Orders" );
				$orders = $listOrdersResult->getOrders ();
				$orderList = $orders->getOrder ();
				foreach ( $orderList as $order ) {
					$this->_saveOrderInfo ( $order );
				}
			}
		}
		$this->_responseMetadata ( $response );
	}
	
	/**
	 * 获取下页的数据
	 *
	 * @param MarketplaceWebService_Client $service        	
	 */
	protected function getOrderListNextData() {
		$service = $this->_service;
		while ( $this->_hasNext && $this->_nextToken ) {
			$request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest ();
			$request->setSellerId ( $this->_tokenConfig ['MERCHANT_ID'] );
			$request->setNextToken ( $this->_nextToken );
			$this->invokeListOrdersByNextToken ( $request );
		}
	}
	/**
	 * 发送请求
	 *
	 * @param MarketplaceWebServiceOrders_Client $service        	
	 * @param unknown_type $request        	
	 */
	protected function invokeListOrdersByNextToken($request) {
		$service = $this->_service;
		$this->_hasNext = false;
		$this->_nextToken = '';
		$response = $service->listOrdersByNextToken ( $request );
		
		Common_ApiProcess::log ( "Service Response" );
		Common_ApiProcess::log ( "=============================================================================" );
		
		Common_ApiProcess::log ( "        ListOrdersByNextTokenResponse" );
		if ($response->isSetListOrdersByNextTokenResult ()) {
			Common_ApiProcess::log ( "            ListOrdersByNextTokenResult" );
			$listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult ();
			if ($listOrdersByNextTokenResult->isSetNextToken ()) {
				$this->_hasNext = true;
				$this->_nextToken = $listOrdersByNextTokenResult->getNextToken ();
				
				Common_ApiProcess::log ( "                NextToken" );
				Common_ApiProcess::log ( "                    " . $listOrdersByNextTokenResult->getNextToken () );
			}
			if ($listOrdersByNextTokenResult->isSetCreatedBefore ()) {
				Common_ApiProcess::log ( "                CreatedBefore" );
				Common_ApiProcess::log ( "                    " . $listOrdersByNextTokenResult->getCreatedBefore () );
			}
			if ($listOrdersByNextTokenResult->isSetLastUpdatedBefore ()) {
				Common_ApiProcess::log ( "                LastUpdatedBefore" );
				Common_ApiProcess::log ( "                    " . $listOrdersByNextTokenResult->getLastUpdatedBefore () );
			}
			if ($listOrdersByNextTokenResult->isSetOrders ()) {
				Common_ApiProcess::log ( "                Orders" );
				$orders = $listOrdersByNextTokenResult->getOrders ();
				$orderList = $orders->getOrder ();
				foreach ( $orderList as $order ) {
					$this->_saveOrderInfo ( $order );
				}
			}
		}
		$this->_responseMetadata ( $response );
	}
	protected function _responseMetadata($response) {
		if ($response->isSetResponseMetadata ()) {
			Amazon_Service::log ( "ResponseMetadata" );
			$responseMetadata = $response->getResponseMetadata ();
			if ($responseMetadata->isSetRequestId ()) {
				$this->_RequestId = $responseMetadata->getRequestId ();
				Amazon_Service::log ( "RequestId:" . $responseMetadata->getRequestId () . "" );
			}
		}
		Amazon_Service::log ( "ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata () . "" );
	}
	protected function _saveOrderInfo($order) {
		$this->_orderObjectArr [] = $order;
		$orderInfoRow = array ();
		Common_ApiProcess::log ( "                    Order" );
		if ($order->isSetAmazonOrderId ()) {
			Common_ApiProcess::log ( "                        AmazonOrderId" );
			Common_ApiProcess::log ( "                            " . $order->getAmazonOrderId () );
			$orderInfoRow ['amazon_order_id'] = $order->getAmazonOrderId ();
		}
		if ($order->isSetSellerOrderId ()) {
			Common_ApiProcess::log ( "                        SellerOrderId" );
			Common_ApiProcess::log ( "                            " . $order->getSellerOrderId () );
			$orderInfoRow ['seller_order_id'] = $order->getSellerOrderId ();
		}
		if ($order->isSetPurchaseDate ()) {
			Common_ApiProcess::log ( "                        PurchaseDate" );
			Common_ApiProcess::log ( "                            " . $order->getPurchaseDate () );
			$orderInfoRow ['purchase_date'] = $order->getPurchaseDate ();
		}
		if ($order->isSetLastUpdateDate ()) {
			Common_ApiProcess::log ( "                        LastUpdateDate" );
			Common_ApiProcess::log ( "                            " . $order->getLastUpdateDate () );
			$orderInfoRow ['last_update_date'] = $order->getLastUpdateDate ();
		}
		if ($order->isSetOrderStatus ()) {
			Common_ApiProcess::log ( "                        OrderStatus" );
			Common_ApiProcess::log ( "                            " . $order->getOrderStatus () );
			$orderInfoRow ['order_status'] = $order->getOrderStatus ();
		}
		if ($order->isSetFulfillmentChannel ()) {
			Common_ApiProcess::log ( "                        FulfillmentChannel" );
			Common_ApiProcess::log ( "                            " . $order->getFulfillmentChannel () );
			$orderInfoRow ['fulfillment_channel'] = $order->getFulfillmentChannel ();
		}
		if ($order->isSetSalesChannel ()) {
			Common_ApiProcess::log ( "                        SalesChannel" );
			Common_ApiProcess::log ( "                            " . $order->getSalesChannel () );
			$orderInfoRow ['sales_channel'] = $order->getSalesChannel ();
		}
		if ($order->isSetOrderChannel ()) {
			Common_ApiProcess::log ( "                        OrderChannel" );
			Common_ApiProcess::log ( "                            " . $order->getOrderChannel () );
			$orderInfoRow ['order_channel'] = $order->getOrderChannel ();
		}
		if ($order->isSetShipServiceLevel ()) {
			Common_ApiProcess::log ( "                        ShipServiceLevel" );
			Common_ApiProcess::log ( "                            " . $order->getShipServiceLevel () );
			$orderInfoRow ['ship_service_level'] = $order->getShipServiceLevel ();
		}
		if ($order->isSetShippingAddress ()) {
			$addressInfo = array ();
			Common_ApiProcess::log ( "                        ShippingAddress" );
			$shippingAddress = $order->getShippingAddress ();
			if ($shippingAddress->isSetName ()) {
				Common_ApiProcess::log ( "                            Name" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getName () );
				$addressInfo ['shipping_address_name'] = $shippingAddress->getName ();
				$orderInfoRow ['shipping_address_name'] = $shippingAddress->getName ();
			}
			if ($shippingAddress->isSetAddressLine1 ()) {
				Common_ApiProcess::log ( "                            AddressLine1" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getAddressLine1 () );
				$addressInfo ['shipping_address_address_line1'] = $shippingAddress->getAddressLine1 ();
				$orderInfoRow ['shipping_address_address1'] = $shippingAddress->getAddressLine1 ();
			}
			if ($shippingAddress->isSetAddressLine2 ()) {
				Common_ApiProcess::log ( "                            AddressLine2" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getAddressLine2 () );
				$addressInfo ['shipping_address_address_line2'] = $shippingAddress->getAddressLine2 ();
				$orderInfoRow ['shipping_address_address2'] = $shippingAddress->getAddressLine2 ();
			}
			if ($shippingAddress->isSetAddressLine3 ()) {
				Common_ApiProcess::log ( "                            AddressLine3" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getAddressLine3 () );
				$addressInfo ['shipping_address_address_line3'] = $shippingAddress->getAddressLine3 ();
				$orderInfoRow ['shipping_address_address3'] = $shippingAddress->getAddressLine3 ();
			}
			if ($shippingAddress->isSetCity ()) {
				Common_ApiProcess::log ( "                            City" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getCity () );
				$addressInfo ['shipping_address_city'] = $shippingAddress->getCity ();
				$orderInfoRow ['shipping_address_city'] = $shippingAddress->getCity ();
			}
			if ($shippingAddress->isSetCounty ()) {
				Common_ApiProcess::log ( "                            County" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getCounty () );
				$addressInfo ['shipping_address_county'] = $shippingAddress->getCounty ();
				$orderInfoRow ['shipping_address_county'] = $shippingAddress->getCounty ();
			}
			if ($shippingAddress->isSetDistrict ()) {
				Common_ApiProcess::log ( "                            District" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getDistrict () );
				$addressInfo ['shipping_address_district'] = $shippingAddress->getDistrict ();
				$orderInfoRow ['shipping_address_district'] = $shippingAddress->getDistrict ();
			}
			if ($shippingAddress->isSetStateOrRegion ()) {
				Common_ApiProcess::log ( "                            StateOrRegion" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getStateOrRegion () );
				$addressInfo ['shipping_address_state_or_region'] = $shippingAddress->getStateOrRegion ();
				$orderInfoRow ['shipping_address_state_or_region'] = $shippingAddress->getStateOrRegion ();
			}
			if ($shippingAddress->isSetPostalCode ()) {
				Common_ApiProcess::log ( "                            PostalCode" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getPostalCode () );
				$addressInfo ['shipping_address_postal_code'] = $shippingAddress->getPostalCode ();
				$orderInfoRow ['shipping_address_postal_code'] = $shippingAddress->getPostalCode ();
			}
			if ($shippingAddress->isSetCountryCode ()) {
				Common_ApiProcess::log ( "                            CountryCode" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getCountryCode () );
				$addressInfo ['shipping_address_country_code'] = $shippingAddress->getCountryCode ();
				$orderInfoRow ['shipping_address_country_code'] = $shippingAddress->getCountryCode ();
			}
			if ($shippingAddress->isSetPhone ()) {
				Common_ApiProcess::log ( "                            Phone" );
				Common_ApiProcess::log ( "                                " . $shippingAddress->getPhone () );
				$addressInfo ['shipping_address_phone'] = $shippingAddress->getPhone ();
				$orderInfoRow ['shipping_address_phone'] = $shippingAddress->getPhone ();
			}
			$orderInfoRow ['addressInfo'] = $addressInfo;
		}
		if ($order->isSetOrderTotal ()) {
			Common_ApiProcess::log ( "                        OrderTotal" );
			$orderTotal = $order->getOrderTotal ();
			if ($orderTotal->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $orderTotal->getCurrencyCode () );
				$orderInfoRow ['currency_code'] = $orderTotal->getCurrencyCode ();
			}
			if ($orderTotal->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $orderTotal->getAmount () );
				$orderInfoRow ['amount'] = $orderTotal->getAmount ();
			}
		}
		if ($order->isSetNumberOfItemsShipped ()) {
			Common_ApiProcess::log ( "                        NumberOfItemsShipped" );
			Common_ApiProcess::log ( "                            " . $order->getNumberOfItemsShipped () );
			$orderInfoRow ['number_of_items_shipped'] = $order->getNumberOfItemsShipped ();
		}
		if ($order->isSetNumberOfItemsUnshipped ()) {
			Common_ApiProcess::log ( "                        NumberOfItemsUnshipped" );
			Common_ApiProcess::log ( "                            " . $order->getNumberOfItemsUnshipped () );
			$orderInfoRow ['number_of_items_unshipped'] = $order->getNumberOfItemsUnshipped ();
		}
		$paymentInfoArr = array ();
		if ($order->isSetPaymentExecutionDetail ()) {
			Common_ApiProcess::log ( "                        PaymentExecutionDetail" );
			$paymentExecutionDetail = $order->getPaymentExecutionDetail ();
			$paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem ();
			
			foreach ( $paymentExecutionDetailItemList as $paymentExecutionDetailItem ) {
				$paymentInfo = array ();
				Common_ApiProcess::log ( "                            PaymentExecutionDetailItem" );
				if ($paymentExecutionDetailItem->isSetPayment ()) {
					Common_ApiProcess::log ( "                                Payment" );
					$payment = $paymentExecutionDetailItem->getPayment ();
					if ($payment->isSetCurrencyCode ()) {
						Common_ApiProcess::log ( "                                    CurrencyCode" );
						Common_ApiProcess::log ( "                                        " . $payment->getCurrencyCode () );
						$paymentInfo ['currency_code'] = $payment->getCurrencyCode ();
					}
					if ($payment->isSetAmount ()) {
						Common_ApiProcess::log ( "                                    Amount" );
						Common_ApiProcess::log ( "                                        " . $payment->getAmount () );
						$paymentInfo ['amount'] = $payment->getAmount ();
					}
				}
				if ($paymentExecutionDetailItem->isSetPaymentMethod ()) {
					Common_ApiProcess::log ( "                                PaymentMethod" );
					Common_ApiProcess::log ( "                                    " . $paymentExecutionDetailItem->getPaymentMethod () );
					$paymentInfo ['payment_method'] = $paymentExecutionDetailItem->getPaymentMethod ();
				}
				$paymentInfoArr [] = $paymentInfo;
			}
			$orderInfoRow ['paymentInfoArr'] = $paymentInfoArr;
		}
		if ($order->isSetPaymentMethod ()) {
			Common_ApiProcess::log ( "                        PaymentMethod" );
			Common_ApiProcess::log ( "                            " . $order->getPaymentMethod () );
			$orderInfoRow ['payment_method'] = $order->getPaymentMethod ();
		}
		if ($order->isSetMarketplaceId ()) {
			Common_ApiProcess::log ( "                        MarketplaceId" );
			Common_ApiProcess::log ( "                            " . $order->getMarketplaceId () );
			$orderInfoRow ['marketplace_id'] = $order->getMarketplaceId ();
		}
		if ($order->isSetBuyerEmail ()) {
			Common_ApiProcess::log ( "                        BuyerEmail" );
			Common_ApiProcess::log ( "                            " . $order->getBuyerEmail () );
			$orderInfoRow ['buyer_email'] = $order->getBuyerEmail ();
		}
		if ($order->isSetBuyerName ()) {
			Common_ApiProcess::log ( "                        BuyerName" );
			Common_ApiProcess::log ( "                            " . $order->getBuyerName () );
			$orderInfoRow ['buyer_name'] = $order->getBuyerName ();
		}
		if ($order->isSetShipmentServiceLevelCategory ()) {
			Common_ApiProcess::log ( "                        ShipmentServiceLevelCategory" );
			Common_ApiProcess::log ( "                            " . $order->getShipmentServiceLevelCategory () );
			$orderInfoRow ['shipment_service_level_category'] = $order->getShipmentServiceLevelCategory ();
		}
		if ($order->isSetShippedByAmazonTFM ()) {
			Common_ApiProcess::log ( "                        ShippedByAmazonTFM" );
			Common_ApiProcess::log ( "                            " . $order->getShippedByAmazonTFM () );
			$orderInfoRow ['shipped_by_amazon_tfm'] = $order->getShippedByAmazonTFM ();
		}
		if ($order->isSetTFMShipmentStatus ()) {
			Common_ApiProcess::log ( "                        TFMShipmentStatus" );
			Common_ApiProcess::log ( "                            " . $order->getTFMShipmentStatus () );
			$orderInfoRow ['tfm_shipment_status'] = $order->getTFMShipmentStatus ();
		}
		
		if ($order->isSetOrderType ()) {
			$orderInfoRow ['order_type'] = $order->getOrderType ();
		}
		if ($order->isSetEarliestShipDate ()) {
			$orderInfoRow ['earliest_ship_date'] = $order->getEarliestShipDate ();
		}
		if ($order->isSetLatestShipDate ()) {
			$orderInfoRow ['latest_ship_date'] = $order->getLatestShipDate ();
		}
		if ($order->isSetShippedByAmazonTFM ()) {
			$orderInfoRow ['shipped_amazon_tfm'] = $order->getShippedByAmazonTFM ();
			$orderInfoRow ['shipped_by_amazon_tfm'] = $order->getShippedByAmazonTFM ();
		}
		if ($order->isSetCbaDisplayableShippingLabel ()) {
			$orderInfoRow ['cba_displayable_shipping_label'] = $order->getCbaDisplayableShippingLabel ();
		} 
// 		$o = new MarketplaceWebServiceOrders_Model_Order();
// 		$o->getEarliestShipDate();
		$amazonOrder = array (
				'amazon_order_id' => $orderInfoRow ['amazon_order_id'],
				'seller_order_id' => $orderInfoRow ['seller_order_id'],
				'purchase_date' => $orderInfoRow ['purchase_date'],
				'last_update_date' => $orderInfoRow ['last_update_date'],
				'order_status' => $orderInfoRow ['order_status'],
				'fulfillment_channel' => $orderInfoRow ['fulfillment_channel'],
				'sales_channel' => $orderInfoRow ['sales_channel'],
				'order_channel' => $orderInfoRow ['order_channel'],
				'ship_service_level' => $orderInfoRow ['ship_service_level'],
				'order_type' => $orderInfoRow ['order_type'],
				'currency_code' => $orderInfoRow ['currency_code'],
				'amount' => $orderInfoRow ['amount'],
				'payment_method' => $orderInfoRow ['payment_method'],
				'marketplace_id' => $orderInfoRow ['marketplace_id'],
				'buyer_email' => $orderInfoRow ['buyer_email'],
				'buyer_name' => $orderInfoRow ['buyer_name'],
				'earliest_ship_date' => $orderInfoRow ['earliest_ship_date'],
				'latest_ship_date' => $orderInfoRow ['latest_ship_date'],
				'shipment_service_level_category' => $orderInfoRow ['shipment_service_level_category'],
				'shipped_amazon_tfm' => $orderInfoRow ['shipped_amazon_tfm'],
				'tfm_shipment_status' => $orderInfoRow ['tfm_shipment_status'],
				'cba_displayable_shipping_label' => $orderInfoRow ['cba_displayable_shipping_label'],
				'number_items_shipped' => $orderInfoRow ['number_of_items_shipped'],
				'number_items_unshipped' => $orderInfoRow ['number_of_items_unshipped'],
				'shipping_address_name' => $orderInfoRow ['shipping_address_name'],
				'shipping_address_phone' => $orderInfoRow ['shipping_address_phone'],
				'shipping_address_country_code' => $orderInfoRow ['shipping_address_country_code'],
				'shipping_address_state' => $orderInfoRow ['shipping_address_state_or_region'],
				'shipping_address_district' => $orderInfoRow ['shipping_address_district'],
				'shipping_address_county' => $orderInfoRow ['shipping_address_county'],
				'shipping_address_city' => $orderInfoRow ['shipping_address_city'],
				'shipping_address_postal_code' => $orderInfoRow ['shipping_address_postal_code'],
				'shipping_address_address1' => $orderInfoRow ['shipping_address_address1'],
				'shipping_address_address2' => $orderInfoRow ['shipping_address_address2'],
				'shipping_address_address3' => $orderInfoRow ['shipping_address_address3'],
				'request_id' => '',
				'is_loaded' => '0' ,
				'company_code' => $this->_company_code,
				'user_account' => $this->_user_account,
				'create_time_sys' => date ( 'Y-m-d H:i:s' ),
				'update_time_sys' => date ( 'Y-m-d H:i:s' ),
		);
		Common_Common::checkTableColumnExist('amazon_order_original', 'create_time_sys');
		Common_Common::checkTableColumnExist('amazon_order_original', 'update_time_sys');
		$amazonOrder = Ec_AutoRun::arrayNullToEmptyString($amazonOrder);
		$con = array (
				'amazon_order_id' => $orderInfoRow ['amazon_order_id'],
				'company_code' => $this->_company_code,
				'user_account' => $this->_user_account 
		);
		$exist = Service_AmazonOrderOriginal::getByCondition($con);
		if($exist){
			unset($amazonOrder['create_time_sys']);
			$exist = array_pop($exist);
			$aoo_id = $exist['aoo_id'];
			Service_AmazonOrderOriginal::update($amazonOrder, $aoo_id,'aoo_id');
			//日志
			$diff = array_diff_assoc($amazonOrder, $exist);
			unset($diff['update_time_sys']);
			unset($diff['create_time_sys']);
			if($diff){
				// 日志
				$log = array ();
				foreach ( $diff as $k => $v ) {
					$log [] = $k . ' from ' . $exist [$k] . ' to ' . $v;
				}
				$logRow = array (
						'amazon_order_id' => $orderInfoRow ['amazon_order_id'],
						'company_code' => $this->_company_code,
						'user_account' => $this->_user_account,
						'content' => implode ( "\n", $log ),
						'create_time_sys' => date ( 'Y-m-d H:i:s' ) 
				);
				$table = 'amazon_order_log';				
				$sql = "show tables like '{$table}';";
				$exist = Common_Common::fetchRow ( $sql );
				if (! $exist) {
					$sql = "
						CREATE TABLE IF NOT EXISTS `{$table}` (
						  `aol_id` int(11) NOT NULL auto_increment,
						  `amazon_order_id` varchar(60) NOT NULL default '',
						  `company_code` varchar(60) NOT NULL default '',
						  `user_account` varchar(60) NOT NULL default '',
						  `content` text,
						  `create_time_sys` varchar(60) NOT NULL default '' COMMENT '创建时间',
						  PRIMARY KEY  (`aol_id`),
						  KEY `amazon_order_id` (`amazon_order_id`)
						) ;					
						";
					Common_Common::query ( $sql );
				}
				$db = Common_Common::getAdapter ();
				$db->insert ( $table, $logRow );
			}
			
		}else{
			$aoo_id = Service_AmazonOrderOriginal::add($amazonOrder);
		}
		$amazonOrder['aoo_id'] = $aoo_id;
		//删除历史数据
		Service_AmazonOrderPayment::delete($aoo_id,'aoo_id');
		foreach($paymentInfoArr as $p){
			$paymentRow = array (
					'aoo_id'=>$aoo_id,
					'amazon_order_id' => $orderInfoRow ['amazon_order_id'],
					'currency_code' => $p ['currency_code'],
					'amount' => $p ['amount'],
					'payment_method' => $p ['payment_method'],
					'company_code' => $this->_company_code,
					'user_account' => $this->_user_account 
			);
			Service_AmazonOrderPayment::add($paymentRow);
			$amazonOrder['paymentArr'][] = $paymentRow;
		}
		$this->_orderArr [] = $amazonOrder;
	}
}