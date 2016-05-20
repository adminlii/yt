<?php
/**
 * amazon拉取订单Items自动服务
 * @author Frank
 * @date 2013-11-14 13:21:19
 */
class Amazon_LoadAmazonOrderItemsService
{

    /**
     * 日志文件名
     *
     * @var unknown_type
     */
    private static $log_name = 'runListOrderItems_';

    private $_amazonOrderItemsRow = array();

    private $_configSecretKey = array(
        'token_id' => null,
        'token' => null,
        'site' => null,
        'seller_id' => null
    );

    /**
     * amazon订单Itmes下载
     *
     * @see Ec_AutoRun::run()
     */
    public function loadAmazonListOrderItems($user_account, $company_code = '')
    {
        $amazonAccount = $user_account; // 绑定的amazon账户
        $company_code = $company_code ? $company_code : Common_Company::getCompanyCode(); // 公司代码
        
        $resultPlatformUser = Service_PlatformUser::getByField($amazonAccount, 'user_account');
        
        $this->_configSecretKey['token_id'] = $resultPlatformUser["user_token_id"];
        $this->_configSecretKey['token'] = $resultPlatformUser["user_token"];
        $this->_configSecretKey['site'] = $resultPlatformUser["site"];
        $this->_configSecretKey['seller_id'] = $resultPlatformUser["seller_id"];
        
        /*
         * 3. 查询未下载Items的Amazon订单
         */
        $conAmazonOrderOriginal = array(
            'user_account' => $amazonAccount,
            'is_loaded' => '0'
        );
        $rowNum = Amazon_ListOrderItemsService::REQUEST_MAX - 2;
        $resultAmazonOrderOriginal = Service_AmazonOrderOriginal::getByCondition($conAmazonOrderOriginal, '*', $rowNum, 1, 'last_update_date desc');
        // 无数据，直接返回
        $loadAmazonOrders = array();
        foreach($resultAmazonOrderOriginal as $itemKey => $itemValue){
            $loadAmazonOrders[$itemValue['amazon_order_id']] = $itemValue;
        }
        
        /*
         * 4. 循环查询Amazon订单的Items
         */
        // 请求ListOrderItems是否失败过
        $callListOrderItemsErrorBol = false;
        foreach($loadAmazonOrders as $amazonOrderKey => $amazonOrderValue){
            $listOrderItmesService = new Amazon_ListOrderItemsService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site']);
            $amazonOrderId = $amazonOrderKey;
            $ListOrderItemsServiceReturn = $listOrderItmesService->getListOrderItems($amazonOrderId);
            
            $nextToken = null;
            if($ListOrderItemsServiceReturn['ask']){
                $this->convertListOrderItems($ListOrderItemsServiceReturn['data'], $amazonOrderId, $amazonOrderValue['aoo_id']);
                if($ListOrderItemsServiceReturn['data']->getListOrderItemsResult()->isSetNextToken()){
                    $nextToken = $ListOrderItemsServiceReturn['data']->getListOrderItemsResult()->getNextToken();
                }
                /*
                 * 5.2 判断是否有nextToken，继续调用
                 */
                while(! empty($nextToken)){
                    $ListOrderItemsByNextTokenServiceReturn = $this->getAmazonOrderItemsByNextToken($nextToken);
                    if($ListOrderItemsByNextTokenServiceReturn['ask']){
                        $this->convertListOrderItemsByNextToken($ListOrderItemsByNextTokenServiceReturn['data'], $amazonOrderId, $amazonOrderValue['aoo_id']);
                        if($ListOrderItemsByNextTokenServiceReturn['data']->getListOrderItemsByNextTokenResult()->isSetNextToken()){
                            $nextToken = $ListOrderItemsByNextTokenServiceReturn['data']->getListOrderItemsByNextTokenResult()->getNextToken();
                        }else{
                            $nextToken = null;
                        }
                    }else{
                        $errorMessage = "amazon账户： '$amazonAccount',(getListOrderItems)运行异常->" . print_r($ListOrderItemsServiceReturn, true);
                        throw new Exception($errorMessage);
                    }
                }
            }else{
                // 使用nextToken请求，出现异常，不处理只记录日志
                $errorMessage = "amazonOrderId： '$amazonOrderId',(getListOrderItems)运行异常->" . print_r($ListOrderItemsServiceReturn, true);
                throw new Exception($errorMessage);
            }
        }
        
        /*
         * 6. 检查是否存在数据，校验重复-->保存
         */
        $addRowNum = 0;
        if(count($this->_amazonOrderItemsRow) > 0){
            try{
                // 写入amazonItems
                foreach($this->_amazonOrderItemsRow as $amazonOrderItemsRowKey => $amazonOrderItemsRowValue){
                    $amazonOrderId_c = $amazonOrderItemsRowKey;
                    $addBol = false;
                    foreach($amazonOrderItemsRowValue as $Itemskey => $ItemsValue){
                        $orderItemId_c = $Itemskey;
                        $conAmazonOrderDetail = array(
                            'amazon_order_id' => $amazonOrderId_c,
                            'order_item_id' => $orderItemId_c
                        );
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
                                // 付款类型
                                foreach($promotionIdList as $promotionIdListKey => $promotionIdListValue){
                                    $promotionIdValue['aod_id'] = $aod_id;
                                    $promotionIdValue['amazon_order_id'] = $amazonOrderId_c;
                                    Service_AmazonOrderDetailPromotion::add($promotionIdValue);
                                }
                            }
                            $addRowNum += 1;
                            $addBol = true;
                        }
                    }
                    if($addBol){
                        Service_AmazonOrderOriginal::update(array(
                            'is_loaded' => 1
                        ), $amazonOrderId_c, 'amazon_order_id');
                    }
                }
            }catch(Exception $e){
                $errorMessage = "amazon账户：'$amazonAccount',下载订单Items信息出现异常,错误原因：" . $e->getMessage();
                throw new Exception($errorMessage);
            }
        }
        
        /*
         * 6. 处理完成，更新数据控制表
         */
        $return = array();
        $return['ask'] = 1;
        $return['count'] = $addRowNum;
        $return['message'] = "amazon账户：$amazonAccount,订单Items任务完成.";
        return $return;
    }

    /**
     *
     * @param unknown_type $nextToken            
     * @return Ambigous <multitype:number string MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenResponse , multitype:number string Ambigous <number, NULL, string, number, NULL, string, mixed> >
     */
    private function getAmazonOrderItemsByNextToken($nextToken)
    {
        $obj = new Amazon_ListOrderItemsByNextTokenService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site']);
        $return = $obj->getListOrderItems($nextToken);
        return $return;
    }

    /**
     * 封装ListOrderItems返回的Itmes信息
     *
     * @param unknown_type $listOrderItemsResponse            
     * @param unknown_type $amazonOrderId            
     * @param unknown_type $aoo_id            
     * @return multitype:multitype:multitype: unknown NULL
     */
    private function convertListOrderItems($listOrderItemsResponse, $amazonOrderId, $aoo_id)
    {
        $response = $listOrderItemsResponse;
        $itemsRow = array();
        if($response->isSetListOrderItemsResult()){
            $listOrderItemsResult = $response->getListOrderItemsResult();
            
            if($listOrderItemsResult->isSetAmazonOrderId()){
                $amazonOrderId = $listOrderItemsResult->getAmazonOrderId();
            }
            if($listOrderItemsResult->isSetOrderItems()){
                
                $orderItems = $listOrderItemsResult->getOrderItems();
                $orderItemList = $orderItems->getOrderItem();
                foreach($orderItemList as $orderItem){
                    $row = array();
                    $row['amazon_order_id'] = $amazonOrderId;
                    $row['aoo_id'] = $aoo_id;
                    if($orderItem->isSetASIN()){
                        // echo(" " . $orderItem->getASIN() . "\n");
                        $row['asin'] = $orderItem->getASIN();
                    }
                    if($orderItem->isSetSellerSKU()){
                        // echo(" " . $orderItem->getSellerSKU() . "\n");
                        $row['seller_sku'] = $orderItem->getSellerSKU();
                    }
                    if($orderItem->isSetOrderItemId()){
                        // echo(" " . $orderItem->getOrderItemId() . "\n");
                        $row['order_item_id'] = $orderItem->getOrderItemId();
                    }
                    if($orderItem->isSetTitle()){
                        // echo(" " . $orderItem->getTitle() . "\n");
                        $row['title'] = $orderItem->getTitle();
                    }
                    if($orderItem->isSetQuantityOrdered()){
                        // echo(" " . $orderItem->getQuantityOrdered() . "\n");
                        $row['quantity_ordered'] = $orderItem->getQuantityOrdered();
                    }
                    if($orderItem->isSetQuantityShipped()){
                        // echo(" " . $orderItem->getQuantityShipped() . "\n");
                        $row['quantity_shipped'] = $orderItem->getQuantityShipped();
                    }
                    if($orderItem->isSetItemPrice()){
                        // echo(" ItemPrice\n");
                        $itemPrice = $orderItem->getItemPrice();
                        if($itemPrice->isSetCurrencyCode()){
                            // echo(" " . $itemPrice->getCurrencyCode() . "\n");
                            $row['item_price_currency_code'] = $itemPrice->getCurrencyCode();
                        }
                        if($itemPrice->isSetAmount()){
                            // echo(" " . $itemPrice->getAmount() . "\n");
                            $row['item_price_amount'] = $itemPrice->getAmount();
                        }
                    }
                    if($orderItem->isSetShippingPrice()){
                        // echo(" ShippingPrice\n");
                        $shippingPrice = $orderItem->getShippingPrice();
                        if($shippingPrice->isSetCurrencyCode()){
                            // echo(" " . $shippingPrice->getCurrencyCode() . "\n");
                            $row['shipping_price_currency_code'] = $shippingPrice->getCurrencyCode();
                        }
                        if($shippingPrice->isSetAmount()){
                            // echo(" " . $shippingPrice->getAmount() . "\n");
                            $row['shipping_price_amount'] = $shippingPrice->getAmount();
                        }
                    }
                    if($orderItem->isSetGiftWrapPrice()){
                        // echo(" GiftWrapPrice\n");
                        $giftWrapPrice = $orderItem->getGiftWrapPrice();
                        if($giftWrapPrice->isSetCurrencyCode()){
                            // echo(" " . $giftWrapPrice->getCurrencyCode() . "\n");
                            $row['gift_wrap_price_currency_code'] = $giftWrapPrice->getCurrencyCode();
                        }
                        if($giftWrapPrice->isSetAmount()){
                            // echo(" " . $giftWrapPrice->getAmount() . "\n");
                            $row['gift_wrap_price_amount'] = $giftWrapPrice->getAmount();
                        }
                    }
                    if($orderItem->isSetItemTax()){
                        // echo(" ItemTax\n");
                        $itemTax = $orderItem->getItemTax();
                        if($itemTax->isSetCurrencyCode()){
                            // echo(" " . $itemTax->getCurrencyCode() . "\n");
                            $row['item_tax_currency_code'] = $itemTax->getCurrencyCode();
                        }
                        if($itemTax->isSetAmount()){
                            // echo(" " . $itemTax->getAmount() . "\n");
                            $row['item_tax_amount'] = $itemTax->getAmount();
                        }
                    }
                    if($orderItem->isSetShippingTax()){
                        // echo(" ShippingTax\n");
                        $shippingTax = $orderItem->getShippingTax();
                        if($shippingTax->isSetCurrencyCode()){
                            // echo(" " . $shippingTax->getCurrencyCode() . "\n");
                            $row['shipping_tax_currency_code'] = $shippingTax->getCurrencyCode();
                        }
                        if($shippingTax->isSetAmount()){
                            // echo(" " . $shippingTax->getAmount() . "\n");
                            $row['shipping_tax_amount'] = $shippingTax->getAmount();
                        }
                    }
                    if($orderItem->isSetGiftWrapTax()){
                        // echo(" GiftWrapTax\n");
                        $giftWrapTax = $orderItem->getGiftWrapTax();
                        if($giftWrapTax->isSetCurrencyCode()){
                            // echo(" " . $giftWrapTax->getCurrencyCode() . "\n");
                            $row['gift_wrap_tax_currency_code'] = $giftWrapTax->getCurrencyCode();
                        }
                        if($giftWrapTax->isSetAmount()){
                            // echo(" " . $giftWrapTax->getAmount() . "\n");
                            $row['gift_wrap_tax_amount'] = $giftWrapTax->getAmount();
                        }
                    }
                    if($orderItem->isSetShippingDiscount()){
                        // echo(" ShippingDiscount\n");
                        $shippingDiscount = $orderItem->getShippingDiscount();
                        if($shippingDiscount->isSetCurrencyCode()){
                            // echo(" " . $shippingDiscount->getCurrencyCode() . "\n");
                            $row['shipping_discount_currency_code'] = $shippingDiscount->getCurrencyCode();
                        }
                        if($shippingDiscount->isSetAmount()){
                            // echo(" " . $shippingDiscount->getAmount() . "\n");
                            $row['shipping_discount_amount'] = $shippingDiscount->getAmount();
                        }
                    }
                    if($orderItem->isSetPromotionDiscount()){
                        // echo(" PromotionDiscount\n");
                        $promotionDiscount = $orderItem->getPromotionDiscount();
                        if($promotionDiscount->isSetCurrencyCode()){
                            // echo(" " . $promotionDiscount->getCurrencyCode() . "\n");
                            $row['promotion_discount_currency_code'] = $promotionDiscount->getCurrencyCode();
                        }
                        if($promotionDiscount->isSetAmount()){
                            // echo(" " . $promotionDiscount->getAmount() . "\n");
                            $row['promotion_discount_amount'] = $promotionDiscount->getAmount();
                        }
                    }
                    if($orderItem->isSetPromotionIds()){
                        $promotionIds = $orderItem->getPromotionIds();
                        $promotionIdList = $promotionIds->getPromotionId();
                        $row['promotionIdList'] = array();
                        foreach($promotionIdList as $promotionId){
                            // echo(" " . $promotionId);
                            $row['promotionIdList'][] = $promotionId;
                        }
                    }
                    if($orderItem->isSetCODFee()){
                        // echo(" CODFee\n");
                        $CODFee = $orderItem->getCODFee();
                        if($CODFee->isSetCurrencyCode()){
                            // echo(" " . $CODFee->getCurrencyCode() . "\n");
                            $row['cod_fee_currency_code'] = $CODFee->getCurrencyCode();
                        }
                        if($CODFee->isSetAmount()){
                            // echo(" " . $CODFee->getAmount() . "\n");
                            $row['cod_fee_amount'] = $CODFee->getAmount();
                        }
                    }
                    if($orderItem->isSetCODFeeDiscount()){
                        // echo(" CODFeeDiscount\n");
                        $CODFeeDiscount = $orderItem->getCODFeeDiscount();
                        if($CODFeeDiscount->isSetCurrencyCode()){
                            // echo(" " . $CODFeeDiscount->getCurrencyCode() . "\n");
                            $row['cod_fee_discount_currency_code'] = $CODFeeDiscount->getCurrencyCode();
                        }
                        if($CODFeeDiscount->isSetAmount()){
                            // echo(" " . $CODFeeDiscount->getAmount() . "\n");
                            $row['cod_fee_discount_amount'] = $CODFeeDiscount->getAmount();
                        }
                    }
                    if($orderItem->isSetGiftMessageText()){
                        // echo(" " . $orderItem->getGiftMessageText() . "\n");
                        $row['gift_message_text'] = $orderItem->getGiftMessageText();
                    }
                    if($orderItem->isSetGiftWrapLevel()){
                        // echo(" " . $orderItem->getGiftWrapLevel() . "\n");
                        $row['gift_wrap_level'] = $orderItem->getGiftWrapLevel();
                    }
                    if($orderItem->isSetInvoiceData()){
                        // echo(" InvoiceData\n");
                        $invoiceData = $orderItem->getInvoiceData();
                        if($invoiceData->isSetInvoiceRequirement()){
                            // echo(" " . $invoiceData->getInvoiceRequirement() . "\n");
                            $row['invoice_requirement'] = $invoiceData->getInvoiceRequirement();
                        }
                        if($invoiceData->isSetBuyerSelectedInvoiceCategory()){
                            // echo(" " . $invoiceData->getBuyerSelectedInvoiceCategory() . "\n");
                            $row['invoice_buyer_selected_category'] = $invoiceData->getBuyerSelectedInvoiceCategory();
                        }
                        if($invoiceData->isSetInvoiceTitle()){
                            // echo(" " . $invoiceData->getInvoiceTitle() . "\n");
                            $row['invoice_title'] = $invoiceData->getInvoiceTitle();
                        }
                        if($invoiceData->isSetInvoiceInformation()){
                            // echo(" " . $invoiceData->getInvoiceInformation() . "\n");
                            $row['invoice_information'] = $invoiceData->getInvoiceInformation();
                        }
                    }
                    
                    if($response->isSetResponseMetadata()){
                        // echo(" ResponseMetadata\n");
                        $responseMetadata = $response->getResponseMetadata();
                        if($responseMetadata->isSetRequestId()){
                            // echo(" " . $responseMetadata->getRequestId() . "\n");
                            $row['request_id'] = $responseMetadata->getRequestId();
                        }
                    }
                    $itemsRow[$amazonOrderId][$row['order_item_id']] = $row;
                    $this->_amazonOrderItemsRow[$amazonOrderId][$row['order_item_id']] = $row;
                }
            }
        }
        return $itemsRow;
    }

    /**
     * 封装ListOrderItmesByNextToken返回的Items信息
     *
     * @param unknown_type $listOrderItmesByNextTokenResponse            
     * @param unknown_type $amazonOrderId            
     * @param unknown_type $aoo_id            
     * @return multitype:multitype:multitype: unknown NULL
     */
    private function convertListOrderItemsByNextToken($listOrderItmesByNextTokenResponse, $amazonOrderId, $aoo_id)
    {
        $response = $listOrderItmesByNextTokenResponse;
        $itemsRow = array();
        if($response->isSetListOrderItemsByNextTokenResult()){
            $listOrderItemsByNextTokenResult = $response->getSetListOrderItemsByNextTokenResult();
            if($listOrderItemsByNextTokenResult->isSetAmazonOrderId()){
                // echo(" AmazonOrderId\n");
                // echo (" " . $listOrderItemsByNextTokenResult->getAmazonOrderId() . "\n");
            }
            if($listOrderItemsByNextTokenResult->isSetOrderItems()){
                // echo(" OrderItems\n");
                $orderItems = $listOrderItemsByNextTokenResult->getOrderItems();
                $orderItemList = $orderItems->getOrderItem();
                foreach($orderItemList as $orderItem){
                    $row = array();
                    $row['amazon_order_id'] = $amazonOrderId;
                    $row['aoo_id'] = $aoo_id;
                    if($orderItem->isSetASIN()){
                        // echo(" " . $orderItem->getASIN() . "\n");
                        $row['asin'] = $orderItem->getASIN();
                    }
                    if($orderItem->isSetSellerSKU()){
                        // echo(" " . $orderItem->getSellerSKU() . "\n");
                        $row['seller_sku'] = $orderItem->getSellerSKU();
                    }
                    if($orderItem->isSetOrderItemId()){
                        // echo(" " . $orderItem->getOrderItemId() . "\n");
                        $row['order_item_id'] = $orderItem->getOrderItemId();
                    }
                    if($orderItem->isSetTitle()){
                        // echo(" " . $orderItem->getTitle() . "\n");
                        $row['title'] = $orderItem->getTitle();
                    }
                    if($orderItem->isSetQuantityOrdered()){
                        // echo(" " . $orderItem->getQuantityOrdered() . "\n");
                        $row['quantity_ordered'] = $orderItem->getQuantityOrdered();
                    }
                    if($orderItem->isSetQuantityShipped()){
                        // echo(" " . $orderItem->getQuantityShipped() . "\n");
                        $row['quantity_shipped'] = $orderItem->getQuantityShipped();
                    }
                    if($orderItem->isSetItemPrice()){
                        echo ("                        ItemPrice\n");
                        $itemPrice = $orderItem->getItemPrice();
                        if($itemPrice->isSetCurrencyCode()){
                            // echo(" " . $itemPrice->getCurrencyCode() . "\n");
                            $row['item_price_currency_code'] = $itemPrice->getCurrencyCode();
                        }
                        if($itemPrice->isSetAmount()){
                            // echo(" " . $itemPrice->getAmount() . "\n");
                            $row['item_price_amount'] = $itemPrice->getAmount();
                        }
                    }
                    if($orderItem->isSetShippingPrice()){
                        // echo(" ShippingPrice\n");
                        $shippingPrice = $orderItem->getShippingPrice();
                        if($shippingPrice->isSetCurrencyCode()){
                            // echo(" " . $shippingPrice->getCurrencyCode() . "\n");
                            $row['shipping_price_currency_code'] = $shippingPrice->getCurrencyCode();
                        }
                        if($shippingPrice->isSetAmount()){
                            // echo(" " . $shippingPrice->getAmount() . "\n");
                            $row['shipping_price_amount'] = $shippingPrice->getAmount();
                        }
                    }
                    if($orderItem->isSetGiftWrapPrice()){
                        // echo(" GiftWrapPrice\n");
                        $giftWrapPrice = $orderItem->getGiftWrapPrice();
                        if($giftWrapPrice->isSetCurrencyCode()){
                            // echo(" " . $giftWrapPrice->getCurrencyCode() . "\n");
                            $row['gift_wrap_price_currency_code'] = $giftWrapPrice->getCurrencyCode();
                        }
                        if($giftWrapPrice->isSetAmount()){
                            // echo(" " . $giftWrapPrice->getAmount() . "\n");
                            $row['gift_wrap_price_amount'] = $giftWrapPrice->getAmount();
                        }
                    }
                    if($orderItem->isSetItemTax()){
                        // echo(" ItemTax\n");
                        $itemTax = $orderItem->getItemTax();
                        if($itemTax->isSetCurrencyCode()){
                            // echo(" " . $itemTax->getCurrencyCode() . "\n");
                            $row['item_tax_currency_code'] = $itemTax->getCurrencyCode();
                        }
                        if($itemTax->isSetAmount()){
                            // echo(" " . $itemTax->getAmount() . "\n");
                            $row['item_tax_amount'] = $itemTax->getAmount();
                        }
                    }
                    if($orderItem->isSetShippingTax()){
                        // echo(" ShippingTax\n");
                        $shippingTax = $orderItem->getShippingTax();
                        if($shippingTax->isSetCurrencyCode()){
                            // echo(" " . $shippingTax->getCurrencyCode() . "\n");
                            $row['shipping_tax_currency_code'] = $shippingTax->getCurrencyCode();
                        }
                        if($shippingTax->isSetAmount()){
                            // echo(" Amount\n");
                            // echo(" " . $shippingTax->getAmount() . "\n");
                            $row['shipping_tax_amount'] = $shippingTax->getAmount();
                        }
                    }
                    if($orderItem->isSetGiftWrapTax()){
                        // echo(" GiftWrapTax\n");
                        $giftWrapTax = $orderItem->getGiftWrapTax();
                        if($giftWrapTax->isSetCurrencyCode()){
                            // echo(" " . $giftWrapTax->getCurrencyCode() . "\n");
                            $row['gift_wrap_tax_currency_code'] = $giftWrapTax->getCurrencyCode();
                        }
                        if($giftWrapTax->isSetAmount()){
                            // echo(" " . $giftWrapTax->getAmount() . "\n");
                            $row['gift_wrap_tax_amount'] = $giftWrapTax->getAmount();
                        }
                    }
                    if($orderItem->isSetShippingDiscount()){
                        // echo(" ShippingDiscount\n");
                        $shippingDiscount = $orderItem->getShippingDiscount();
                        if($shippingDiscount->isSetCurrencyCode()){
                            // echo(" " . $shippingDiscount->getCurrencyCode() . "\n");
                            $row['shipping_discount_currency_code'] = $shippingDiscount->getCurrencyCode();
                        }
                        if($shippingDiscount->isSetAmount()){
                            // echo(" " . $shippingDiscount->getAmount() . "\n");
                            $row['shipping_discount_amount'] = $shippingDiscount->getAmount();
                        }
                    }
                    if($orderItem->isSetPromotionDiscount()){
                        // echo(" PromotionDiscount\n");
                        $promotionDiscount = $orderItem->getPromotionDiscount();
                        if($promotionDiscount->isSetCurrencyCode()){
                            // echo(" " . $promotionDiscount->getCurrencyCode() . "\n");
                            $row['promotion_discount_currency_code'] = $promotionDiscount->getCurrencyCode();
                        }
                        if($promotionDiscount->isSetAmount()){
                            // echo(" Amount\n");
                            // echo(" " . $promotionDiscount->getAmount() . "\n");
                            $row['promotion_discount_amount'] = $promotionDiscount->getAmount();
                        }
                    }
                    if($orderItem->isSetPromotionIds()){
                        $promotionIds = $orderItem->getPromotionIds();
                        $promotionIdList = $promotionIds->getPromotionId();
                        $row['promotionIdList'] = array();
                        foreach($promotionIdList as $promotionId){
                            // echo(" " . $promotionId);
                            $row['promotionIdList'][] = $promotionId;
                        }
                    }
                    if($orderItem->isSetCODFee()){
                        // echo(" CODFee\n");
                        $CODFee = $orderItem->getCODFee();
                        if($CODFee->isSetCurrencyCode()){
                            // echo(" " . $CODFee->getCurrencyCode() . "\n");
                            $row['cod_fee_currency_code'] = $CODFee->getCurrencyCode();
                        }
                        if($CODFee->isSetAmount()){
                            // echo(" Amount\n");
                            // echo(" " . $CODFee->getAmount() . "\n");
                            $row['cod_fee_amount'] = $CODFee->getAmount();
                        }
                    }
                    if($orderItem->isSetCODFeeDiscount()){
                        // echo(" CODFeeDiscount\n");
                        $CODFeeDiscount = $orderItem->getCODFeeDiscount();
                        if($CODFeeDiscount->isSetCurrencyCode()){
                            // echo(" " . $CODFeeDiscount->getCurrencyCode() . "\n");
                            $row['cod_fee_discount_currency_code'] = $CODFeeDiscount->getCurrencyCode();
                        }
                        if($CODFeeDiscount->isSetAmount()){
                            // echo(" " . $CODFeeDiscount->getAmount() . "\n");
                            $row['cod_fee_discount_amount'] = $CODFeeDiscount->getAmount();
                        }
                    }
                    if($orderItem->isSetGiftMessageText()){
                        // echo(" " . $orderItem->getGiftMessageText() . "\n");
                        $row['gift_message_text'] = $orderItem->getGiftMessageText();
                    }
                    if($orderItem->isSetGiftWrapLevel()){
                        // echo(" " . $orderItem->getGiftWrapLevel() . "\n");
                        $row['gift_wrap_level'] = $orderItem->getGiftWrapLevel();
                    }
                    if($orderItem->isSetInvoiceData()){
                        // echo(" InvoiceData\n");
                        $invoiceData = $orderItem->getInvoiceData();
                        if($invoiceData->isSetInvoiceRequirement()){
                            // echo(" " . $invoiceData->getInvoiceRequirement() . "\n");
                            $row['invoice_requirement'] = $invoiceData->getInvoiceRequirement();
                        }
                        if($invoiceData->isSetBuyerSelectedInvoiceCategory()){
                            // echo(" " . $invoiceData->getBuyerSelectedInvoiceCategory() . "\n");
                            $row['invoice_buyer_selected_category'] = $invoiceData->getBuyerSelectedInvoiceCategory();
                        }
                        if($invoiceData->isSetInvoiceTitle()){
                            // echo(" " . $invoiceData->getInvoiceTitle() . "\n");
                            $row['invoice_title'] = $invoiceData->getInvoiceTitle();
                        }
                        if($invoiceData->isSetInvoiceInformation()){
                            // echo(" " . $invoiceData->getInvoiceInformation() . "\n");
                            $row['invoice_information'] = $invoiceData->getInvoiceInformation();
                        }
                    }
                    
                    if($response->isSetResponseMetadata()){
                        // echo(" ResponseMetadata\n");
                        $responseMetadata = $response->getResponseMetadata();
                        if($responseMetadata->isSetRequestId()){
                            // echo(" RequestId\n");
                            // echo(" " . $responseMetadata->getRequestId() . "\n");
                            $row['request_id'] = $responseMetadata->getRequestId();
                        }
                    }
                    // $itemsRow[$row['order_item_id']] = $row;
                    // $this->_amazonOrderItemsRow[$row['order_item_id']] = $row;
                    $itemsRow[$amazonOrderId][$row['order_item_id']] = $row;
                    $this->_amazonOrderItemsRow[$amazonOrderId][$row['order_item_id']] = $row;
                }
            }
        }
        return $itemsRow;
    }
}