<?php
require_once ('MarketplaceWebServiceOrders/Client.php');

/**
 * amazon拉取订单Item服务类
 * @author Frank
 * @date 2013-11-8 13:21:19
 */
class Amazon_ListOrderItemsByNextTokenService {
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'amazonListOrderItemsByNextToken_';
	
	/**
	 * 接口名称
	 * @var unknown_type
	 */
	const INTERFACE_NAME = 'ListOrderItems';
	
	/**
	 * 路径
	 * @var unknown_type
	 */
	const SERVICE_PATH = '/Orders/';
	
	/**
	 * 请求上限
	 * @var unknown_type
	 */
	const REQUEST_MAX = 30;
	
	/**
	 * 恢复个数
	 * @var unknown_type
	 */
	const RESTORE_NUM = 1;

	/**
	 * 恢复时间
	 * @var unknown_type
	 */
	const RESTORE_TIME_NUM = 2;
	/**
	 * 恢复时间单位
	 */
	const RESTORE_TIME_UNIT = 'second';
	
	
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
	 * 获得亚马逊订单Itmes
	 * @param unknown_type $ListOrderItmesNextToken	ListOrderItems接口查询返回的NextToken
	 */
	public function getListOrderItems($ListOrderItmesNextToken){
		$return = array(
				'ask'=>0,
				'message'=>'',
				'data'=>''
				);
		
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
			throw new Exception("amzon站点： $countryCode ，未能找到对应的亚马逊服务地址及商城编号.");
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
		$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsByNextTokenRequest();
		
		//商家ID
		$request->setSellerId($this->_tokenConfig['MERCHANT_ID']);
		
		//nextToken
		$request->setNextToken($value);
		
// 		print_r($amazonConfig);
// 		print_r($this->_tokenConfig);	
// 		print_r($curlConfig);
// 		print_r($marketplace_id);
// 		print_r($request);
		//调用
		try{
			$response = $service->listOrderItemsByNextToken($request);
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
	
	public function invokeListOrderItemsByNextToken(MarketplaceWebServiceOrders_Interface $service, $request)
	{
		try {
			$response = $service->listOrderItemsByNextToken($request);
	
			echo ("Service Response\n");
			echo ("=============================================================================\n");
	
			echo("        ListOrderItemsByNextTokenResponse\n");
			if ($response->isSetListOrderItemsByNextTokenResult()) {
				echo("            ListOrderItemsByNextTokenResult\n");
				$listOrderItemsByNextTokenResult = $response->getListOrderItemsByNextTokenResult();
				if ($listOrderItemsByNextTokenResult->isSetNextToken())
				{
					echo("                NextToken\n");
					echo("                    " . $listOrderItemsByNextTokenResult->getNextToken() . "\n");
				}
				if ($listOrderItemsByNextTokenResult->isSetAmazonOrderId())
				{
					echo("                AmazonOrderId\n");
					echo("                    " . $listOrderItemsByNextTokenResult->getAmazonOrderId() . "\n");
				}
				if ($listOrderItemsByNextTokenResult->isSetOrderItems()) {
					echo("                OrderItems\n");
					$orderItems = $listOrderItemsByNextTokenResult->getOrderItems();
					$orderItemList = $orderItems->getOrderItem();
					foreach ($orderItemList as $orderItem) {
						echo("                    OrderItem\n");
						if ($orderItem->isSetASIN())
						{
							echo("                        ASIN\n");
							echo("                            " . $orderItem->getASIN() . "\n");
						}
						if ($orderItem->isSetSellerSKU())
						{
							echo("                        SellerSKU\n");
							echo("                            " . $orderItem->getSellerSKU() . "\n");
						}
						if ($orderItem->isSetOrderItemId())
						{
							echo("                        OrderItemId\n");
							echo("                            " . $orderItem->getOrderItemId() . "\n");
						}
						if ($orderItem->isSetTitle())
						{
							echo("                        Title\n");
							echo("                            " . $orderItem->getTitle() . "\n");
						}
						if ($orderItem->isSetQuantityOrdered())
						{
							echo("                        QuantityOrdered\n");
							echo("                            " . $orderItem->getQuantityOrdered() . "\n");
						}
						if ($orderItem->isSetQuantityShipped())
						{
							echo("                        QuantityShipped\n");
							echo("                            " . $orderItem->getQuantityShipped() . "\n");
						}
						if ($orderItem->isSetItemPrice()) {
							echo("                        ItemPrice\n");
							$itemPrice = $orderItem->getItemPrice();
							if ($itemPrice->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $itemPrice->getCurrencyCode() . "\n");
							}
							if ($itemPrice->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $itemPrice->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetShippingPrice()) {
							echo("                        ShippingPrice\n");
							$shippingPrice = $orderItem->getShippingPrice();
							if ($shippingPrice->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $shippingPrice->getCurrencyCode() . "\n");
							}
							if ($shippingPrice->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $shippingPrice->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetGiftWrapPrice()) {
							echo("                        GiftWrapPrice\n");
							$giftWrapPrice = $orderItem->getGiftWrapPrice();
							if ($giftWrapPrice->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $giftWrapPrice->getCurrencyCode() . "\n");
							}
							if ($giftWrapPrice->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $giftWrapPrice->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetItemTax()) {
							echo("                        ItemTax\n");
							$itemTax = $orderItem->getItemTax();
							if ($itemTax->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $itemTax->getCurrencyCode() . "\n");
							}
							if ($itemTax->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $itemTax->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetShippingTax()) {
							echo("                        ShippingTax\n");
							$shippingTax = $orderItem->getShippingTax();
							if ($shippingTax->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $shippingTax->getCurrencyCode() . "\n");
							}
							if ($shippingTax->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $shippingTax->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetGiftWrapTax()) {
							echo("                        GiftWrapTax\n");
							$giftWrapTax = $orderItem->getGiftWrapTax();
							if ($giftWrapTax->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $giftWrapTax->getCurrencyCode() . "\n");
							}
							if ($giftWrapTax->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $giftWrapTax->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetShippingDiscount()) {
							echo("                        ShippingDiscount\n");
							$shippingDiscount = $orderItem->getShippingDiscount();
							if ($shippingDiscount->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $shippingDiscount->getCurrencyCode() . "\n");
							}
							if ($shippingDiscount->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $shippingDiscount->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetPromotionDiscount()) {
							echo("                        PromotionDiscount\n");
							$promotionDiscount = $orderItem->getPromotionDiscount();
							if ($promotionDiscount->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $promotionDiscount->getCurrencyCode() . "\n");
							}
							if ($promotionDiscount->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $promotionDiscount->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetPromotionIds()) {
							echo("                        PromotionIds\n");
							$promotionIds = $orderItem->getPromotionIds();
							$promotionIdList  =  $promotionIds->getPromotionId();
							foreach ($promotionIdList as $promotionId) {
								echo("                            PromotionId\n");
								echo("                                " . $promotionId);
							}
						}
						if ($orderItem->isSetCODFee()) {
							echo("                        CODFee\n");
							$CODFee = $orderItem->getCODFee();
							if ($CODFee->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $CODFee->getCurrencyCode() . "\n");
							}
							if ($CODFee->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $CODFee->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetCODFeeDiscount()) {
							echo("                        CODFeeDiscount\n");
							$CODFeeDiscount = $orderItem->getCODFeeDiscount();
							if ($CODFeeDiscount->isSetCurrencyCode())
							{
								echo("                            CurrencyCode\n");
								echo("                                " . $CODFeeDiscount->getCurrencyCode() . "\n");
							}
							if ($CODFeeDiscount->isSetAmount())
							{
								echo("                            Amount\n");
								echo("                                " . $CODFeeDiscount->getAmount() . "\n");
							}
						}
						if ($orderItem->isSetGiftMessageText())
						{
							echo("                        GiftMessageText\n");
							echo("                            " . $orderItem->getGiftMessageText() . "\n");
						}
						if ($orderItem->isSetGiftWrapLevel())
						{
							echo("                        GiftWrapLevel\n");
							echo("                            " . $orderItem->getGiftWrapLevel() . "\n");
						}
						if ($orderItem->isSetInvoiceData()) {
							echo("                        InvoiceData\n");
							$invoiceData = $orderItem->getInvoiceData();
							if ($invoiceData->isSetInvoiceRequirement())
							{
								echo("                            InvoiceRequirement\n");
								echo("                                " . $invoiceData->getInvoiceRequirement() . "\n");
							}
							if ($invoiceData->isSetBuyerSelectedInvoiceCategory())
							{
								echo("                            BuyerSelectedInvoiceCategory\n");
								echo("                                " . $invoiceData->getBuyerSelectedInvoiceCategory() . "\n");
							}
							if ($invoiceData->isSetInvoiceTitle())
							{
								echo("                            InvoiceTitle\n");
								echo("                                " . $invoiceData->getInvoiceTitle() . "\n");
							}
							if ($invoiceData->isSetInvoiceInformation())
							{
								echo("                            InvoiceInformation\n");
								echo("                                " . $invoiceData->getInvoiceInformation() . "\n");
							}
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