<?php
require_once ('MarketplaceWebServiceOrders/Client.php');

/**
 * amazon拉取订单服务类--根据next_token
 * @author Frank
 * @date 2013-11-8 13:21:19
 */
class Amazon_ListOrdersByNextTokenService {
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'amazonListOrdersByNextToken_';
	
	/**
	 * 接口名称
	 * @var unknown_type
	 */
	const INTERFACE_NAME = 'ListOrdersByNextToken';
	
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
							'APPLICATION_NAME' => null,
							'APPLICATION_VERSION' => null,
							'MERCHANT_ID' => null,
							'SITE'=>null
							);
	
	/**
	 * 构造器
	 */
	public function __construct($token_id , $token , $saller_id ,$site)
	{
		//访问秘钥ID
		$this->_tokenConfig['AWS_ACCESS_KEY_ID'] = $token_id;
		//访问秘钥
		$this->_tokenConfig['AWS_SECRET_ACCESS_KEY'] = $token;
		//应用名称
		$this->_tokenConfig['APPLICATION_NAME'] = Amazon_AmazonLib::APPLICATION_NAME;
		//应用版本
		$this->_tokenConfig['APPLICATION_VERSION'] = Amazon_AmazonLib::APPLICATION_VERSION;
		//销售ID
		$this->_tokenConfig['MERCHANT_ID'] = $saller_id;
		//站点
		$this->_tokenConfig['SITE'] = $site;
	}
	
	/**
	 * 获得亚马逊订单
	 * @param unknown_type $listOrderNextToken	ListOrders接口查询返回的NextToken
	 */
	public function getListOrdersByNextToken($listOrderNextToken){
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
		$request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
		//商家ID
		$request->setSellerId($this->_tokenConfig['MERCHANT_ID']);
		//NextToken
		$request->setNextToken($listOrderNextToken);
		
// 		print_r($amazonConfig);
// 		print_r($this->_tokenConfig);	
// 		print_r($curlConfig);
// 		print_r($marketplace_id);
		//调用
		try{
			$response = $service->listOrdersByNextToken ( $request );
			$return['ask'] = 1;
			$return['data'] = $response;
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 2);
		} catch (MarketplaceWebServiceOrders_Exception $ex) {
			$errorMessage = '错误代码：' . $ex->getErrorCode() . '--请求ID：' . $ex->getRequestId() . '--错误信息：' . print_r($ex,true);
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 处理订单任务发生异常,错误原因：" . print_r($ex,true), self::$log_name);
		} catch (Exception $e){
			$errorMessage = '错误代码：' . $e->getCode() . '--错误信息：' . $e->getMessage();
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 处理订单任务发生异常,错误原因：" . print_r($e,true), self::$log_name);
		}
		return $return;
	}
	
	/**
	 * 发送请求
	 * @param MarketplaceWebServiceOrders_Interface $service
	 * @param unknown_type $request
	 */
	public function invokeListOrdersByNextToken(MarketplaceWebServiceOrders_Interface $service, $request)
	{
		try {
			$response = $service->listOrdersByNextToken ( $request );
			
			echo ("Service Response\n");
			echo ("=============================================================================\n");
			
			echo ("        ListOrdersByNextTokenResponse\n");
			if ($response->isSetListOrdersByNextTokenResult ()) {
				echo ("            ListOrdersByNextTokenResult\n");
				$listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult ();
				if ($listOrdersByNextTokenResult->isSetNextToken ()) {
					echo ("                NextToken\n");
					echo ("                    " . $listOrdersByNextTokenResult->getNextToken () . "\n");
				}
				if ($listOrdersByNextTokenResult->isSetCreatedBefore ()) {
					echo ("                CreatedBefore\n");
					echo ("                    " . $listOrdersByNextTokenResult->getCreatedBefore () . "\n");
				}
				if ($listOrdersByNextTokenResult->isSetLastUpdatedBefore ()) {
					echo ("                LastUpdatedBefore\n");
					echo ("                    " . $listOrdersByNextTokenResult->getLastUpdatedBefore () . "\n");
				}
				if ($listOrdersByNextTokenResult->isSetOrders ()) {
					echo ("                Orders\n");
					$orders = $listOrdersByNextTokenResult->getOrders ();
					$orderList = $orders->getOrder ();
					foreach ( $orderList as $order ) {
						echo ("                    Order\n");
						if ($order->isSetAmazonOrderId ()) {
							echo ("                        AmazonOrderId\n");
							echo ("                            " . $order->getAmazonOrderId () . "\n");
						}
						if ($order->isSetSellerOrderId ()) {
							echo ("                        SellerOrderId\n");
							echo ("                            " . $order->getSellerOrderId () . "\n");
						}
						if ($order->isSetPurchaseDate ()) {
							echo ("                        PurchaseDate\n");
							echo ("                            " . $order->getPurchaseDate () . "\n");
						}
						if ($order->isSetLastUpdateDate ()) {
							echo ("                        LastUpdateDate\n");
							echo ("                            " . $order->getLastUpdateDate () . "\n");
						}
						if ($order->isSetOrderStatus ()) {
							echo ("                        OrderStatus\n");
							echo ("                            " . $order->getOrderStatus () . "\n");
						}
						if ($order->isSetFulfillmentChannel ()) {
							echo ("                        FulfillmentChannel\n");
							echo ("                            " . $order->getFulfillmentChannel () . "\n");
						}
						if ($order->isSetSalesChannel ()) {
							echo ("                        SalesChannel\n");
							echo ("                            " . $order->getSalesChannel () . "\n");
						}
						if ($order->isSetOrderChannel ()) {
							echo ("                        OrderChannel\n");
							echo ("                            " . $order->getOrderChannel () . "\n");
						}
						if ($order->isSetShipServiceLevel ()) {
							echo ("                        ShipServiceLevel\n");
							echo ("                            " . $order->getShipServiceLevel () . "\n");
						}
						if ($order->isSetShippingAddress ()) {
							echo ("                        ShippingAddress\n");
							$shippingAddress = $order->getShippingAddress ();
							if ($shippingAddress->isSetName ()) {
								echo ("                            Name\n");
								echo ("                                " . $shippingAddress->getName () . "\n");
							}
							if ($shippingAddress->isSetAddressLine1 ()) {
								echo ("                            AddressLine1\n");
								echo ("                                " . $shippingAddress->getAddressLine1 () . "\n");
							}
							if ($shippingAddress->isSetAddressLine2 ()) {
								echo ("                            AddressLine2\n");
								echo ("                                " . $shippingAddress->getAddressLine2 () . "\n");
							}
							if ($shippingAddress->isSetAddressLine3 ()) {
								echo ("                            AddressLine3\n");
								echo ("                                " . $shippingAddress->getAddressLine3 () . "\n");
							}
							if ($shippingAddress->isSetCity ()) {
								echo ("                            City\n");
								echo ("                                " . $shippingAddress->getCity () . "\n");
							}
							if ($shippingAddress->isSetCounty ()) {
								echo ("                            County\n");
								echo ("                                " . $shippingAddress->getCounty () . "\n");
							}
							if ($shippingAddress->isSetDistrict ()) {
								echo ("                            District\n");
								echo ("                                " . $shippingAddress->getDistrict () . "\n");
							}
							if ($shippingAddress->isSetStateOrRegion ()) {
								echo ("                            StateOrRegion\n");
								echo ("                                " . $shippingAddress->getStateOrRegion () . "\n");
							}
							if ($shippingAddress->isSetPostalCode ()) {
								echo ("                            PostalCode\n");
								echo ("                                " . $shippingAddress->getPostalCode () . "\n");
							}
							if ($shippingAddress->isSetCountryCode ()) {
								echo ("                            CountryCode\n");
								echo ("                                " . $shippingAddress->getCountryCode () . "\n");
							}
							if ($shippingAddress->isSetPhone ()) {
								echo ("                            Phone\n");
								echo ("                                " . $shippingAddress->getPhone () . "\n");
							}
						}
						if ($order->isSetOrderTotal ()) {
							echo ("                        OrderTotal\n");
							$orderTotal = $order->getOrderTotal ();
							if ($orderTotal->isSetCurrencyCode ()) {
								echo ("                            CurrencyCode\n");
								echo ("                                " . $orderTotal->getCurrencyCode () . "\n");
							}
							if ($orderTotal->isSetAmount ()) {
								echo ("                            Amount\n");
								echo ("                                " . $orderTotal->getAmount () . "\n");
							}
						}
						if ($order->isSetNumberOfItemsShipped ()) {
							echo ("                        NumberOfItemsShipped\n");
							echo ("                            " . $order->getNumberOfItemsShipped () . "\n");
						}
						if ($order->isSetNumberOfItemsUnshipped ()) {
							echo ("                        NumberOfItemsUnshipped\n");
							echo ("                            " . $order->getNumberOfItemsUnshipped () . "\n");
						}
						if ($order->isSetPaymentExecutionDetail ()) {
							echo ("                        PaymentExecutionDetail\n");
							$paymentExecutionDetail = $order->getPaymentExecutionDetail ();
							$paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem ();
							foreach ( $paymentExecutionDetailItemList as $paymentExecutionDetailItem ) {
								echo ("                            PaymentExecutionDetailItem\n");
								if ($paymentExecutionDetailItem->isSetPayment ()) {
									echo ("                                Payment\n");
									$payment = $paymentExecutionDetailItem->getPayment ();
									if ($payment->isSetCurrencyCode ()) {
										echo ("                                    CurrencyCode\n");
										echo ("                                        " . $payment->getCurrencyCode () . "\n");
									}
									if ($payment->isSetAmount ()) {
										echo ("                                    Amount\n");
										echo ("                                        " . $payment->getAmount () . "\n");
									}
								}
								if ($paymentExecutionDetailItem->isSetPaymentMethod ()) {
									echo ("                                PaymentMethod\n");
									echo ("                                    " . $paymentExecutionDetailItem->getPaymentMethod () . "\n");
								}
							}
						}
						if ($order->isSetPaymentMethod ()) {
							echo ("                        PaymentMethod\n");
							echo ("                            " . $order->getPaymentMethod () . "\n");
						}
						if ($order->isSetMarketplaceId ()) {
							echo ("                        MarketplaceId\n");
							echo ("                            " . $order->getMarketplaceId () . "\n");
						}
						if ($order->isSetBuyerEmail ()) {
							echo ("                        BuyerEmail\n");
							echo ("                            " . $order->getBuyerEmail () . "\n");
						}
						if ($order->isSetBuyerName ()) {
							echo ("                        BuyerName\n");
							echo ("                            " . $order->getBuyerName () . "\n");
						}
						if ($order->isSetShipmentServiceLevelCategory ()) {
							echo ("                        ShipmentServiceLevelCategory\n");
							echo ("                            " . $order->getShipmentServiceLevelCategory () . "\n");
						}
						if ($order->isSetShippedByAmazonTFM ()) {
							echo ("                        ShippedByAmazonTFM\n");
							echo ("                            " . $order->getShippedByAmazonTFM () . "\n");
						}
						if ($order->isSetTFMShipmentStatus ()) {
							echo ("                        TFMShipmentStatus\n");
							echo ("                            " . $order->getTFMShipmentStatus () . "\n");
						}
					}
				}
			}
			if ($response->isSetResponseMetadata ()) {
				echo ("            ResponseMetadata\n");
				$responseMetadata = $response->getResponseMetadata ();
				if ($responseMetadata->isSetRequestId ()) {
					echo ("                RequestId\n");
					echo ("                    " . $responseMetadata->getRequestId () . "\n");
				}
			}
			
			echo ("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata () . "\n");
		} catch ( MarketplaceWebServiceOrders_Exception $ex ) {
			echo ("Caught Exception: " . $ex->getMessage () . "\n");
			echo ("Response Status Code: " . $ex->getStatusCode () . "\n");
			echo ("Error Code: " . $ex->getErrorCode () . "\n");
			echo ("Error Type: " . $ex->getErrorType () . "\n");
			echo ("Request ID: " . $ex->getRequestId () . "\n");
			echo ("XML: " . $ex->getXML () . "\n");
			echo ("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata () . "\n");
		}
	}
}