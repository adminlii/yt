<?php
/**
 * 订单明细
 * @author max
 *
 */
class Amazon_Order_OrderItemService {
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
	protected $_orderItemArr = array ();
	protected $_orderItemObjectArr = array ();
	protected $_exceptionArr = array ();
	protected $_amazon_order_id = '';
	protected $_aoo_id = '';
	protected $_orderOrg = null;
	
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

	public function setAmazonOrderId($amazon_order_id) {
		$this->_amazon_order_id = $amazon_order_id;
	}
	
	public function setAooId($aoo_id){
		$this->_aoo_id = $aoo_id;
		$amazonOrder = Service_AmazonOrderOriginal::getByField($aoo_id,'aoo_id');
		if(!$amazonOrder){
			throw new Exception('amazonOrder不存在');
		}
		$this->_orderOrg = $amazonOrder;
		$this->_amazon_order_id =$amazonOrder['amazon_order_id'];
		$this->_user_account =$amazonOrder['user_account'];
		$this->_company_code =$amazonOrder['company_code']; 
	}
	
	public function getOrderItemList($amazon_order_id=null) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' ,
				'amazon_order_id'=>$amazon_order_id,
		);
		if($amazon_order_id){
			$this->_amazon_order_id = $amazon_order_id;				
		}
		try {
			if(!$this->_user_account||!$this->_company_code){
				throw new Exception('user_account/company_code未设置');
			}

			if(!$this->_amazon_order_id){
				throw new Exception('amazon_order_id未设置');
			}
			$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest ();
			$request->setSellerId ( $this->_tokenConfig ['MERCHANT_ID'] );
			$request->setAmazonOrderId ( $amazon_order_id );
			$con = array (
					'amazon_order_id' => $this->_amazon_order_id,
					'user_account' => $this->_user_account,
					'company_code' => $this->_company_code 
			);
			$exists = Service_AmazonOrderDetail::getByCondition($con);
			// print_r($request->getAmazonOrderId()) ;exit;
			$this->invokeListOrderItems ( $request );
			// 下载下一页
			$this->getOrderItemsListNextData ();
			//删除旧数据
			foreach ($exists as $v){
				Service_AmazonOrderDetail::delete($v['aod_id'],'aod_id');
			}

			// 更新下载状态
			if(!$this->_orderOrg){
				$con = array('amazon_order_id'=>$amazon_order_id,'user_account'=>$this->_user_account,'company_code'=>$this->_company_code);
				$orderOrg = Service_AmazonOrderOriginal::getByCondition($con);
				if($orderOrg){
					$orderOrg = array_pop($orderOrg);
					$upRow = array('is_loaded'=>'1');
					//更新下载状态
					Service_AmazonOrderOriginal::update($upRow, $orderOrg['aoo_id'],'aoo_id');
				}
			}else{
				$upRow = array (
					'is_loaded' => '1' 
				);
				// 更新下载状态
				Service_AmazonOrderOriginal::update ( $upRow, $this->_orderOrg ['aoo_id'], 'aoo_id' );
			}
			
			//更新关系
			$sql = "update amazon_order_detail a inner join amazon_order_original b on a.amazon_order_id=b.amazon_order_id and a.user_account=b.user_account and a.company_code=b.company_code set a.aoo_id=b.aoo_id;";
			Common_Common::query($sql);
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
		} catch ( MarketplaceWebServiceOrders_Exception $ex ) {
			// 记录日志
			$this->logException ( $ex );
			$return ['message'] = "Amazon请求失败，" . $ex->getMessage ();
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		
		Ec::showError ( print_r ( $this->_orderItemArr, true ), '__amazon_order_item_info' );
		$return ['err'] = $this->_err;
		$return ['excepton'] = $this->_exceptionArr;
		$return ['orderItemArr'] = $this->_orderItemArr;
		return $return;
	}
	
	/**
	 * 发送请求
	 *
	 * @param unknown_type $request        	
	 */
	protected function invokeListOrderItems($request) {
		$this->_hasNext = false;
		$this->_nextToken = '';
		$service = $this->_service;
		$response = $service->listOrderItems ( $request );
		
		Common_ApiProcess::log ( "Service Response" );
		Common_ApiProcess::log ( "=============================================================================" );
		
		Common_ApiProcess::log ( "        ListOrderItemsResponse" );
		if ($response->isSetListOrderItemsResult ()) {
			Common_ApiProcess::log ( "            ListOrderItemsResult" );
			$listOrderItemsResult = $response->getListOrderItemsResult ();
			if ($listOrderItemsResult->isSetNextToken ()) {
				$this->_hasNext = true;
				$this->_nextToken = $listOrderItemsResult->getNextToken ();
				Common_ApiProcess::log ( "                NextToken" );
				Common_ApiProcess::log ( "                    " . $listOrderItemsResult->getNextToken () );
			}
			if ($listOrderItemsResult->isSetAmazonOrderId ()) {
				Common_ApiProcess::log ( "                AmazonOrderId" );
				Common_ApiProcess::log ( "                    " . $listOrderItemsResult->getAmazonOrderId () );
			}
			if ($listOrderItemsResult->isSetOrderItems ()) {
				Common_ApiProcess::log ( "                OrderItems" );
				$orderItems = $listOrderItemsResult->getOrderItems ();
				$orderItemList = $orderItems->getOrderItem ();
				foreach ( $orderItemList as $orderItem ) {
					$this->_saveOrderItemsInfo ( $orderItem );
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
	protected function getOrderItemsListNextData() {
		$service = $this->_service;
		while ( $this->_hasNext && $this->_nextToken ) {
			$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsByNextTokenRequest ();
			$request->setSellerId ( $this->_tokenConfig ['MERCHANT_ID'] );
			$request->setNextToken ( $this->_nextToken );
			$this->invokeListOrderItemsByNextToken ( $request );
		}
	}
	/**
	 * 发送请求
	 *
	 * @param MarketplaceWebServiceOrders_Client $service        	
	 * @param unknown_type $request        	
	 */
	protected function invokeListOrderItemsByNextToken($request) {
		$service = $this->_service;
		$this->_hasNext = false;
		$this->_nextToken = '';
		$response = $service->listOrderItemsByNextToken ( $request );
		
		Common_ApiProcess::log ( "Service Response" );
		Common_ApiProcess::log ( "=============================================================================" );
		
		Common_ApiProcess::log ( "        ListOrderItemsByNextTokenResponse" );
		if ($response->isSetListOrderItemsByNextTokenResult ()) {
			Common_ApiProcess::log ( "            ListOrderItemsByNextTokenResult" );
			$listOrderItemsByNextTokenResult = $response->getListOrderItemsByNextTokenResult ();
			if ($listOrderItemsByNextTokenResult->isSetNextToken ()) {
				$this->_hasNext = true;
				$this->_nextToken = $listOrderItemsByNextTokenResult->getNextToken ();
				Common_ApiProcess::log ( "                NextToken" );
				Common_ApiProcess::log ( "                    " . $listOrderItemsByNextTokenResult->getNextToken () );
			}
			if ($listOrderItemsByNextTokenResult->isSetAmazonOrderId ()) {
				Common_ApiProcess::log ( "                AmazonOrderId" );
				Common_ApiProcess::log ( "                    " . $listOrderItemsByNextTokenResult->getAmazonOrderId () );
			}
			if ($listOrderItemsByNextTokenResult->isSetOrderItems ()) {
				Common_ApiProcess::log ( "                OrderItems" );
				$orderItems = $listOrderItemsByNextTokenResult->getOrderItems ();
				$orderItemList = $orderItems->getOrderItem ();
				foreach ( $orderItemList as $orderItem ) {
					$this->_saveOrderItemsInfo ( $orderItem );
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
	protected function _saveOrderItemsInfo($orderItem) {
		$this->_orderItemObjectArr [] = $orderItem;
		$orderItemRow = array ();
		Common_ApiProcess::log ( "                    OrderItem" );
		if ($orderItem->isSetASIN ()) {
			Common_ApiProcess::log ( "                        ASIN" );
			Common_ApiProcess::log ( "                            " . $orderItem->getASIN () );
			$orderItemRow ['asin'] = $orderItem->getASIN ();
		}
		if ($orderItem->isSetSellerSKU ()) {
			Common_ApiProcess::log ( "                        SellerSKU" );
			Common_ApiProcess::log ( "                            " . $orderItem->getSellerSKU () );
			$orderItemRow ['seller_sku'] = $orderItem->getSellerSKU ();
		}
		if ($orderItem->isSetOrderItemId ()) {
			Common_ApiProcess::log ( "                        OrderItemId" );
			Common_ApiProcess::log ( "                            " . $orderItem->getOrderItemId () );
			$orderItemRow ['order_item_id'] = $orderItem->getOrderItemId ();
		}
		if ($orderItem->isSetTitle ()) {
			Common_ApiProcess::log ( "                        Title" );
			Common_ApiProcess::log ( "                            " . $orderItem->getTitle () );
			$orderItemRow ['title'] = $orderItem->getTitle ();
		}
		if ($orderItem->isSetQuantityOrdered ()) {
			Common_ApiProcess::log ( "                        QuantityOrdered" );
			Common_ApiProcess::log ( "                            " . $orderItem->getQuantityOrdered () );
			$orderItemRow ['quantity_ordered'] = $orderItem->getQuantityOrdered ();
		}
		if ($orderItem->isSetQuantityShipped ()) {
			Common_ApiProcess::log ( "                        QuantityShipped" );
			Common_ApiProcess::log ( "                            " . $orderItem->getQuantityShipped () );
			$orderItemRow ['quantity_shipped'] = $orderItem->getQuantityShipped ();
		}
		if ($orderItem->isSetItemPrice ()) {
			Common_ApiProcess::log ( "                        ItemPrice" );
			$itemPrice = $orderItem->getItemPrice ();
			if ($itemPrice->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $itemPrice->getCurrencyCode () );
				$orderItemRow ['item_price_currency_code'] = $itemPrice->getCurrencyCode ();
			}
			if ($itemPrice->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $itemPrice->getAmount () );
				$orderItemRow ['item_price_amount'] = $itemPrice->getAmount ();
			}
		}
		if ($orderItem->isSetShippingPrice ()) {
			Common_ApiProcess::log ( "                        ShippingPrice" );
			$shippingPrice = $orderItem->getShippingPrice ();
			if ($shippingPrice->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $shippingPrice->getCurrencyCode () );
				$orderItemRow ['shipping_price_currency_code'] = $shippingPrice->getCurrencyCode ();
			}
			if ($shippingPrice->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $shippingPrice->getAmount () );
				$orderItemRow ['shipping_price_amount'] = $shippingPrice->getAmount ();
			}
		}
		if ($orderItem->isSetGiftWrapPrice ()) {
			Common_ApiProcess::log ( "                        GiftWrapPrice" );
			$giftWrapPrice = $orderItem->getGiftWrapPrice ();
			if ($giftWrapPrice->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $giftWrapPrice->getCurrencyCode () );
				$orderItemRow ['gift_wrap_price_currency_code'] = $giftWrapPrice->getCurrencyCode ();
			}
			if ($giftWrapPrice->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $giftWrapPrice->getAmount () );
				$orderItemRow ['gift_wrap_price_amount'] = $giftWrapPrice->getAmount ();
			}
		}
		if ($orderItem->isSetItemTax ()) {
			Common_ApiProcess::log ( "                        ItemTax" );
			$itemTax = $orderItem->getItemTax ();
			if ($itemTax->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $itemTax->getCurrencyCode () );
				$orderItemRow ['item_tax_currency_code'] = $itemTax->getCurrencyCode ();
			}
			if ($itemTax->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $itemTax->getAmount () );
				$orderItemRow ['item_tax_amount'] = $itemTax->getAmount ();
			}
		}
		if ($orderItem->isSetShippingTax ()) {
			Common_ApiProcess::log ( "                        ShippingTax" );
			$shippingTax = $orderItem->getShippingTax ();
			if ($shippingTax->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $shippingTax->getCurrencyCode () );
				$orderItemRow ['shipping_tax_currency_code'] = $shippingTax->getCurrencyCode ();
			}
			if ($shippingTax->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $shippingTax->getAmount () );
				$orderItemRow ['shipping_tax_amount'] = $shippingTax->getAmount ();
			}
		}
		if ($orderItem->isSetGiftWrapTax ()) {
			Common_ApiProcess::log ( "                        GiftWrapTax" );
			$giftWrapTax = $orderItem->getGiftWrapTax ();
			if ($giftWrapTax->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $giftWrapTax->getCurrencyCode () );
				$orderItemRow ['gift_wrap_tax_currency_code'] = $giftWrapTax->getCurrencyCode ();
			}
			if ($giftWrapTax->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $giftWrapTax->getAmount () );
				$orderItemRow ['gift_wrap_tax_amount'] = $giftWrapTax->getAmount ();
			}
		}
		if ($orderItem->isSetShippingDiscount ()) {
			Common_ApiProcess::log ( "                        ShippingDiscount" );
			$shippingDiscount = $orderItem->getShippingDiscount ();
			if ($shippingDiscount->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $shippingDiscount->getCurrencyCode () );
				$orderItemRow ['shipping_discount_currency_code'] = $shippingDiscount->getCurrencyCode ();
			}
			if ($shippingDiscount->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $shippingDiscount->getAmount () );
				$orderItemRow ['shipping_discount_amount'] = $shippingDiscount->getAmount ();
			}
		}
		if ($orderItem->isSetPromotionDiscount ()) {
			Common_ApiProcess::log ( "                        PromotionDiscount" );
			$promotionDiscount = $orderItem->getPromotionDiscount ();
			if ($promotionDiscount->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $promotionDiscount->getCurrencyCode () );
				$orderItemRow ['promotion_discount_currency_code'] = $promotionDiscount->getCurrencyCode ();
			}
			if ($promotionDiscount->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $promotionDiscount->getAmount () );
				$orderItemRow ['promotion_discount_amount'] = $promotionDiscount->getAmount ();
			}
		}
		if ($orderItem->isSetPromotionIds ()) {
			Common_ApiProcess::log ( "                        PromotionIds" );
			$promotionIds = $orderItem->getPromotionIds ();
			$promotionIdList = $promotionIds->getPromotionId ();
			$promotionIdArr = array ();
			foreach ( $promotionIdList as $promotionId ) {
				Common_ApiProcess::log ( "                            PromotionId" );
				Common_ApiProcess::log ( "                                " . $promotionId );
				$promotionIdArr [] = $promotionId;
			}
			$orderItemRow ['promotionIdArr'] = $promotionIdList;
		}
		if ($orderItem->isSetCODFee ()) {
			Common_ApiProcess::log ( "                        CODFee" );
			$CODFee = $orderItem->getCODFee ();
			if ($CODFee->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $CODFee->getCurrencyCode () );
				$orderItemRow ['cod_fee_currency_code'] = $CODFee->getCurrencyCode ();
			}
			if ($CODFee->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $CODFee->getAmount () );
				$orderItemRow ['cod_fee_amount'] = $CODFee->getAmount ();
			}
		}
		if ($orderItem->isSetCODFeeDiscount ()) {
			Common_ApiProcess::log ( "                        CODFeeDiscount" );
			$CODFeeDiscount = $orderItem->getCODFeeDiscount ();
			if ($CODFeeDiscount->isSetCurrencyCode ()) {
				Common_ApiProcess::log ( "                            CurrencyCode" );
				Common_ApiProcess::log ( "                                " . $CODFeeDiscount->getCurrencyCode () );
				$orderItemRow ['cod_fee_discount_currency_code'] = $CODFeeDiscount->getCurrencyCode ();
			}
			if ($CODFeeDiscount->isSetAmount ()) {
				Common_ApiProcess::log ( "                            Amount" );
				Common_ApiProcess::log ( "                                " . $CODFeeDiscount->getAmount () );
				$orderItemRow ['cod_fee_discount_amount'] = $CODFeeDiscount->getAmount ();
			}
		}
		if ($orderItem->isSetGiftMessageText ()) {
			Common_ApiProcess::log ( "                        GiftMessageText" );
			Common_ApiProcess::log ( "                            " . $orderItem->getGiftMessageText () );
			$orderItemRow ['gift_message_text'] = $orderItem->getGiftMessageText ();
		}
		if ($orderItem->isSetGiftWrapLevel ()) {
			Common_ApiProcess::log ( "                        GiftWrapLevel" );
			Common_ApiProcess::log ( "                            " . $orderItem->getGiftWrapLevel () );
			$orderItemRow ['gift_wrap_level'] = $orderItem->getGiftWrapLevel ();
		}
		if ($orderItem->isSetInvoiceData ()) {
			Common_ApiProcess::log ( "                        InvoiceData" );
			$invoiceData = $orderItem->getInvoiceData ();
			if ($invoiceData->isSetInvoiceRequirement ()) {
				Common_ApiProcess::log ( "                            InvoiceRequirement" );
				Common_ApiProcess::log ( "                                " . $invoiceData->getInvoiceRequirement () );
				$orderItemRow ['invoice_requirement'] = $invoiceData->getInvoiceRequirement ();
			}
			if ($invoiceData->isSetBuyerSelectedInvoiceCategory ()) {
				Common_ApiProcess::log ( "                            BuyerSelectedInvoiceCategory" );
				Common_ApiProcess::log ( "                                " . $invoiceData->getBuyerSelectedInvoiceCategory () );
				$orderItemRow ['buyer_selected_invoice_category'] = $invoiceData->getBuyerSelectedInvoiceCategory ();
			}
			if ($invoiceData->isSetInvoiceTitle ()) {
				Common_ApiProcess::log ( "                            InvoiceTitle" );
				Common_ApiProcess::log ( "                                " . $invoiceData->getInvoiceTitle () );
				$orderItemRow ['invoice_title'] = $invoiceData->getInvoiceTitle ();
			}
			if ($invoiceData->isSetInvoiceInformation ()) {
				Common_ApiProcess::log ( "                            InvoiceInformation" );
				Common_ApiProcess::log ( "                                " . $invoiceData->getInvoiceInformation () );
				$orderItemRow ['invoice_information'] = $invoiceData->getInvoiceInformation ();
			}
		}
		if ($orderItem->isSetConditionId ()) {
			$orderItemRow ['condition_id'] = $orderItem->getConditionId ();
		}
		
		if ($orderItem->isSetConditionSubtypeId ()) {
			$orderItemRow ['condition_subtype_id'] = $orderItem->getConditionSubtypeId ();
		}
		if ($orderItem->isSetConditionNote ()) {
			$orderItemRow ['condition_note'] = $orderItem->getConditionNote ();
		}
		
		if ($orderItem->isSetScheduledDeliveryStartDate ()) {
			$orderItemRow ['scheduled_delivery_start_date'] = $orderItem->getScheduledDeliveryStartDate ();
		}
		
		if ($orderItem->isSetScheduledDeliveryEndDate ()) {
			$orderItemRow ['scheduled_delivery_end_date'] = $orderItem->getScheduledDeliveryEndDate ();
		}
// 		$item = new MarketplaceWebServiceOrders_Model_OrderItem();
// 		$item->getScheduledDeliveryEndDate();

		Common_Common::checkTableColumnExist('amazon_order_detail', 'user_account');
		Common_Common::checkTableColumnExist('amazon_order_detail', 'company_code');
		Common_Common::checkTableColumnExist('amazon_order_detail', 'create_time_sys');
		Common_Common::checkTableColumnExist('amazon_order_detail', 'update_time_sys');
		$item = array (
				'amazon_order_id' => $this->_amazon_order_id,
				'asin' => $orderItemRow ['asin'],
				'seller_sku' => $orderItemRow ['seller_sku'],
				'order_item_id' => $orderItemRow ['order_item_id'],
				'title' => $orderItemRow ['title'],
				'quantity_ordered' => $orderItemRow ['quantity_ordered'],
				'quantity_shipped' => $orderItemRow ['quantity_shipped'],
				'gift_message_text' => $orderItemRow ['gift_message_text'],
				'gift_wrap_level' => $orderItemRow ['gift_wrap_level'],
				'item_price_currency_code' => $orderItemRow ['item_price_currency_code'],
				'item_price_amount' => $orderItemRow ['item_price_amount'],
				'shipping_price_currency_code' => $orderItemRow ['shipping_price_currency_code'],
				'shipping_price_amount' => $orderItemRow ['shipping_price_amount'],
				'gift_wrap_price_currency_code' => $orderItemRow ['gift_wrap_price_currency_code'],
				'gift_wrap_price_amount' => $orderItemRow ['gift_wrap_price_amount'],
				'item_tax_currency_code' => $orderItemRow ['item_tax_currency_code'],
				'item_tax_amount' => $orderItemRow ['item_tax_amount'],
				'shipping_tax_currency_code' => $orderItemRow ['shipping_tax_currency_code'],
				'shipping_tax_amount' => $orderItemRow ['shipping_tax_amount'],
				'gift_wrap_tax_currency_code' => $orderItemRow ['gift_wrap_tax_currency_code'],
				'gift_wrap_tax_amount' => $orderItemRow ['gift_wrap_tax_amount'],
				'shipping_discount_currency_code' => $orderItemRow ['shipping_discount_currency_code'],
				'shipping_discount_amount' => $orderItemRow ['shipping_discount_amount'],
				'promotion_discount_currency_code' => $orderItemRow ['promotion_discount_currency_code'],
				'promotion_discount_amount' => $orderItemRow ['promotion_discount_amount'],
				'cod_fee_currency_code' => $orderItemRow ['cod_fee_currency_code'],
				'cod_fee_amount' => $orderItemRow ['cod_fee_amount'],
				'cod_fee_discount_currency_code' => $orderItemRow ['cod_fee_discount_currency_code'],
				'cod_fee_discount_amount' => $orderItemRow ['cod_fee_discount_amount'],
				'invoice_requirement' => $orderItemRow ['invoice_requirement'],
				'invoice_buyer_selected_category' => $orderItemRow ['invoice_buyer_selected_category'],
				'invoice_title' => $orderItemRow ['invoice_title'],
				'invoice_information' => $orderItemRow ['invoice_information'],
				'condition_id' => $orderItemRow ['condition_id'],
				'condition_subtype_id' => $orderItemRow ['condition_subtype_id'],
				'condition_note' => $orderItemRow ['condition_note'],
				'scheduled_delivery_start_date' => $orderItemRow ['scheduled_delivery_start_date'],
				'scheduled_delivery_end_date' => $orderItemRow ['scheduled_delivery_end_date']
		);
		$item['user_account'] = $this->_user_account;
		$item['company_code'] = $this->_company_code;
		$item['create_time_sys'] = date('Y-m-d H:i:s');
		$item['update_time_sys'] = date('Y-m-d H:i:s'); 

		$item = Ec_AutoRun::arrayNullToEmptyString($item);
		Service_AmazonOrderDetail::add($item);
		
		$this->_orderItemArr [] = $orderItemRow;
	}
}