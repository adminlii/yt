<?php
/**
 * 只有符合以下的条件才能返回买家的email地址：
 * 1. 调用者自己和这个Item存在交易关系。
 * 2. Call必须在交易被创建后的某段时间内调用；出于信用与安全相关政策的考虑，这个时间段是未指定的，并且在不同的站点也不同。
 * 在实际操作中，我们建议您在一天之内调用能够获取Transaction相关的 Call，以便可以得到所有的最新信息。例如，每天调用GetSellerTransactions，
 * 将ModTimeFilter设置为上一次调用时间到当前的时间。如果您调用GetItemTransactions来单独获取某些Item的交易，
 * 您可以在得到Item被卖出的通知后，立刻调用这个Call。
 * @author Administrator
 *
 */
class Ebay_LoadEbayOrderService extends Ec_AutoRun
{
    private $_sup_task = false;

    private $_user_account = '';
    
    private $_company_code = '';
    
    private $_order_data = array();
    
    public function setUserAccount($user_account)
    {
        $this->_user_account = $user_account;
    }
    
    public function setCompanyCode($company_code)
    {
        $this->_company_code = $company_code;
    }
    public function loadEbayOrder($loadId)
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        // 得到当前同步订单的关键参数
        $param = $this->getLoadParam($loadId);
        $userAccount = $param["user_account"];
        $companyCode = $param["company_code"];

        $this->_user_account = $userAccount;
        $this->_company_code = $companyCode;
        
        $start = $param["load_start_time"];
        $end = $param["load_end_time"];
        $count = $param["currt_run_count"];
        $this->_user_account = $userAccount;
        $this->_company_code = $companyCode;
                
        try{
            // 从ebay上获取数据并保存
            $this->callEbay($start, $end);
            $orderCount = count($this->_order_data);
            $this->countLoad($loadId, 2, $orderCount); // 运行结束
            
            $return['ask'] = 1;
            $return['message'] = "eBay Time : " . $start . " ~ " . $end . ',' . $userAccount .', '.$companyCode. ' TotalNumberOfEntries ' . $orderCount;
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0);
            Ec::showError("账号：" . $userAccount .' ,'.$companyCode. '发生错误，eBay时间：' . $start . ' To ' . $end . ',错误原因：' . $e->getMessage(), 'runOrder_Fail_');
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    public function getOrderData(){
        return $this->_order_data;
    }
    
    // load数据
    public function callEbay($start, $end, $OrderIDArr = array())
    {

        $userAccount = $this->_user_account;
        $companyCode = $this->_company_code;
        
        if(empty($userAccount)){
            throw new Exception('UserAccount未赋值');
        }
        if(empty($companyCode)){
            throw new Exception('CompanyCode未赋值');
        }
        $token = Ebay_EbayLib::getUserToken($userAccount,$companyCode);
        if(! $token){
            throw new Exception($userAccount . ' UserToken Ivalid');
        }
//         echo $token;
//         exit;
        // 第一次运行EBAY API 并获取总条数，
        $dataResponse = array();
        $TotalNumberOfEntries = 0;
        $page = 0;
        $orderCount = 0;
        while(true){
            $page ++;
            $data = Ebay_EbayLib::getEbayOrders($token, $start, $end, $page, $OrderIDArr);
//             print_r($data);exit;
            
            if($data['GetOrdersResponse']['Ack'] == 'Failure'){
                throw new Exception(print_r($data['GetOrdersResponse'], true));
            }
            $response = $data['GetOrdersResponse'];
//             print_r($response);exit;
            $total = $response['PaginationResult']['TotalNumberOfEntries'];
            
            
            $response = $response['OrderArray'];
            if(isset($response['Order'])){
                $response = $response['Order'];
                $orderArr = array();
                if(isset($response[0])){ // 只有一个订单
                    $orderArr = $response;
                }else{
                    $orderArr[] = $response;
                }
                $orderCount += count($orderArr); // 统计数量
                
                foreach($orderArr as $k => $v){
                    $orderArr[$k] = $v;
                    $this->_order_data[$v['OrderID']] = $v;
                }
                $this->saveOrder($orderArr); // 保存到数据表
            }

            if($TotalNumberOfEntries == 0){
                $TotalNumberOfEntries = $total;
            }elseif($TotalNumberOfEntries != $total){
                //发生交叉异常，重新开始拉取订单
                $page = 0;
                $TotalNumberOfEntries = $total;
                continue;
            }
            if($data['GetOrdersResponse']['HasMoreOrders'] != 'true'){ // 不成功或者没有下一页
                                                                       // 程序终止,注意：返回的HasMoreItems
                                                                       // 是字符串类型
                break;
            }
        }
//         echo $TotalNumberOfEntries;exit;
        return $TotalNumberOfEntries;
    }

    /**
     * 数据保存
     * 
     * @param unknown_type $orderArr            
     * @throws Exception
     */
    private function saveOrder($orderArr)
    {

        $userAccount = $this->_user_account;
        $companyCode = $this->_company_code;

        if(empty($userAccount)){
            throw new Exception('UserAccount未赋值');
        }
        if(empty($companyCode)){
            throw new Exception('CompanyCode未赋值');
        }        
        // 循环保存数据
        foreach($orderArr as $v){
            //日志
            $this->orderLoadLog($v);
            try{
                $this->saveEbayOrder($v);  
            }catch(Exception $e){
                Ec::showError($companyCode."," . $userAccount . ",订单号：" . $v["OrderID"] . "\n" . print_r($e->getMessage(), true), 'load_order_inner_');
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * 日志
     * @param unknown_type $order
     */
    public function orderLoadLog($order){
        $order_sn = $order["OrderID"];
        // load订单日志
        $loadLogRow = array(
            'ref_id' => $order_sn,
            'log_content_serialize' => serialize($order),
            'log_content' => print_r($order, true),
            'create_time' => date('Y-m-d H:i:s')
        );
        $exist = Service_OrderLoadLog::getByField($order_sn, 'ref_id');
        if($exist){
            unset($loadLogRow['ref_id']);
            Service_OrderLoadLog::update($loadLogRow, $order_sn, 'ref_id');
        }else{
            Service_OrderLoadLog::add($loadLogRow);
        }
    }
    
    // ====================================================================================================
    /*
     * OrderID Subtotal Total AdjustmentAmount currencyID AmountPaid currencyID
     * AmountSaved currencyID BuyerCheckoutMessage BuyerUserID CancelReason
     * CheckoutStatus_eBayPaymentStatus
     * CheckoutStatus_IntegratedMerchantCreditCardEnabled
     * CheckoutStatus_LastModifiedTime CheckoutStatus_PaymentMethod
     * CheckoutStatus_Status CreatedTime PaidTime ShippedTime CreatingUserRole
     * EIASToken IntegratedMerchantCreditCardEnabled IsMultiLegShipping
     * OrderStatus PaymentHoldStatus SellerEIASToken SellerEmail SellerUserID
     * ShippingAddress_AddressID ShippingAddress_AddressOwner
     * ShippingAddress_CityName ShippingAddress_Country
     * ShippingAddress_CountryName ShippingAddress_ExternalAddressID
     * ShippingAddress_Name ShippingAddress_Phone ShippingAddress_PostalCode
     * ShippingAddress_StateOrProvince ShippingAddress_Street1
     * ShippingAddress_Street2 ShippingServiceSelected_ShippingService
     * ShippingServiceSelected_ShippingServiceCost currencyID
     * ShippingServiceSelected_ShippingServicePriority
     * ShippingServiceSelected_ShippingServiceAdditionalCost currencyID
     * ShippingServiceSelected_ShippingInsuranceCost currencyID
     * ShippingServiceSelected_ImportCharge currencyID
     * ShippingServiceSelected_ExpeditedService //多 PaymentMethods //多
     * ShippingDetails_ShipmentTrackingDetails_ShipmentTrackingNumber
     * ShippingDetails_ShipmentTrackingDetails_ShippingCarrierUsed //多
     * ExternalTransaction_ExternalTransactionID
     * ExternalTransaction_ExternalTransactionStatus
     * ExternalTransaction_ExternalTransactionTime
     * ExternalTransaction_FeeOrCreditAmount currencyID
     * ExternalTransaction_PaymentOrRefundAmount currencyID //多
     * MonetaryDetails_Payments_Payment_FeeOrCreditAmount currencyID
     * MonetaryDetails_Payments_Payment_Payee
     * MonetaryDetails_Payments_Payment_Payer
     * MonetaryDetails_Payments_Payment_PaymentAmount currencyID
     * MonetaryDetails_Payments_Payment_PaymentReferenceID
     * MonetaryDetails_Payments_Payment_PaymentStatus
     * MonetaryDetails_Payments_Payment_PaymentTime
     * MonetaryDetails_Payments_Payment_ReferenceID //多
     * TransactionArray_Transaction_ActualHandlingCost currencyID
     * TransactionArray_Transaction_ActualShippingCost currencyID
     * TransactionArray_Transaction_Buyer_Email
     * TransactionArray_Transaction_CreatedDate
     * TransactionArray_Transaction_FinalValueFee currencyID
     * TransactionArray_Transaction_TransactionSiteID
     * TransactionArray_Transaction_Platform
     * TransactionArray_Transaction_InvoiceSentTime
     * TransactionArray_Transaction_Item_IntegratedMerchantCreditCardEnabled
     * TransactionArray_Transaction_Item_ItemID
     * TransactionArray_Transaction_Item_SellerInventoryID
     * TransactionArray_Transaction_Item_Site
     * TransactionArray_Transaction_Item_SKU
     * TransactionArray_Transaction_Item_Title
     * TransactionArray_Transaction_Item_Url
     * TransactionArray_Transaction_Item_ConditionID
     * TransactionArray_Transaction_Item_ConditionDisplayName
     * TransactionArray_Transaction_ShippingDetails_SellingManagerSalesRecordNumber
     * TransactionArray_Transaction_OrderLineItemID
     * TransactionArray_Transaction_QuantityPurchased
     * TransactionArray_Transaction_ShippedTime
     * TransactionArray_Transaction_Status_IntegratedMerchantCreditCardEnabled
     * TransactionArray_Transaction_Status_PaymentHoldStatus
     * TransactionArray_Transaction_Status_PaymentMethodUsed
     * TransactionArray_Transaction_TransactionID
     * TransactionArray_Transaction_TransactionPrice currencyID
     */
    /**
     * 单个订单数据格式化
     */
    public function formatResponse($orderData)
    {        
        Common_ApiProcess::log("订单号:".$orderData['OrderID']);
        $data = array();
        // 订单主信息 start
        $ebay_order = array(
            'OrderID' => $orderData['OrderID'],
            'Subtotal' => $orderData['Subtotal'],
            'Subtotal_currencyID' => $orderData['Subtotal attr']['currencyID'],
            'Total' => $orderData['Total'],
            'Total_currencyID' => $orderData['Total attr']['currencyID'],
            'AdjustmentAmount' => $orderData['AdjustmentAmount'],
            'AdjustmentAmount_currencyID' => $orderData['AdjustmentAmount attr']['currencyID'],
            'AmountPaid' => $orderData['AmountPaid'],
            'AmountPaid_currencyID' => $orderData['AmountPaid attr']['currencyID'],
            'AmountSaved' => $orderData['AmountSaved'],
            'AmountSaved_currencyID' => $orderData['AmountSaved attr']['currencyID'],
            'BuyerCheckoutMessage' => $orderData['BuyerCheckoutMessage'],
            'BuyerUserID' => $orderData['BuyerUserID'],
            'CancelReason' => $orderData['CancelReason'],
            'CreatedTime' => $orderData['CreatedTime'],
            'PaidTime' => $orderData['PaidTime'],
            'ShippedTime' => $orderData['ShippedTime'],
            'CreatingUserRole' => $orderData['CreatingUserRole'],
            'EIASToken' => $orderData['EIASToken'],
            'IntegratedMerchantCreditCardEnabled' => $orderData['IntegratedMerchantCreditCardEnabled'],
            'IsMultiLegShipping' => $orderData['IsMultiLegShipping'],
            'OrderStatus' => $orderData['OrderStatus'],
            'PaymentHoldStatus' => $orderData['PaymentHoldStatus'],
            'SellerEIASToken' => $orderData['SellerEIASToken'],
            'SellerEmail' => $orderData['SellerEmail'],
            'SellerUserID' => $orderData['SellerUserID'],
            
            'CheckoutStatus_eBayPaymentStatus' => $orderData['CheckoutStatus']['eBayPaymentStatus'],
            'CheckoutStatus_IntegratedMerchantCreditCardEnabled' => $orderData['CheckoutStatus']['IntegratedMerchantCreditCardEnabled'],
            'CheckoutStatus_LastModifiedTime' => $orderData['CheckoutStatus']['LastModifiedTime'],
            'CheckoutStatus_PaymentMethod' => $orderData['CheckoutStatus']['PaymentMethod'],
            'CheckoutStatus_Status' => $orderData['CheckoutStatus']['Status'],
            'ShippingAddress_AddressID' => $orderData['ShippingAddress']['AddressID'],
            'ShippingAddress_AddressOwner' => $orderData['ShippingAddress']['AddressOwner'],
            'ShippingAddress_CityName' => $orderData['ShippingAddress']['CityName'],
            'ShippingAddress_Country' => $orderData['ShippingAddress']['Country'],
            'ShippingAddress_CountryName' => $orderData['ShippingAddress']['CountryName'],
            'ShippingAddress_ExternalAddressID' => $orderData['ShippingAddress']['ExternalAddressID'],
            'ShippingAddress_Name' => $orderData['ShippingAddress']['Name'],
            'ShippingAddress_Phone' => $orderData['ShippingAddress']['Phone'],
            'ShippingAddress_PostalCode' => $orderData['ShippingAddress']['PostalCode'],
            'ShippingAddress_StateOrProvince' => $orderData['ShippingAddress']['StateOrProvince'],
            'ShippingAddress_Street1' => $orderData['ShippingAddress']['Street1'],
            'ShippingAddress_Street2' => $orderData['ShippingAddress']['Street2'],
            'ShippingServiceSelected_ShippingService' => $orderData['ShippingServiceSelected']['ShippingService'],
            'ShippingServiceSelected_ShippingServiceCost' => $orderData['ShippingServiceSelected']['ShippingServiceCost'],
            'ShippingServiceSelected_ShippingServiceCost_currencyID' => $orderData['ShippingServiceSelected']['ShippingServiceCost attr']['currencyID'],
            'ShippingServiceSelected_ShippingServicePriority' => $orderData['ShippingServiceSelected']['ShippingServicePriority'],
            'ShippingServiceSelected_ShippingServiceAdditionalCost' => $orderData['ShippingServiceSelected']['ShippingServiceAdditionalCost'],
            'ShippingServiceSelected_ShippingServiceAdditionalCost_currencyID' => $orderData['ShippingServiceSelected']['ShippingServiceAdditionalCost attr']['currencyID'],
            'ShippingServiceSelected_ShippingInsuranceCost' => $orderData['']['ShippingInsuranceCost'],
            'ShippingServiceSelected_ShippingInsuranceCost_currencyID' => $orderData['ShippingServiceSelected']['ShippingInsuranceCost attr']['currencyID'],
            'ShippingServiceSelected_ImportCharge' => $orderData['ShippingServiceSelected']['ImportCharge'],
            'ShippingServiceSelected_ImportCharge_currencyID' => $orderData['ShippingServiceSelected']['ImportCharge attr']['currencyID'],
            'ShippingServiceSelected_ExpeditedService' => $orderData['ShippingServiceSelected']['ExpeditedService'],
            'PaymentMethods' => is_array($orderData['PaymentMethods']) ? implode(';', $orderData['PaymentMethods']) : $orderData['PaymentMethods']
        );
        // 订单主信息 end
        $data['ebay_order'] = $ebay_order;
        // 付款信息 start
        $payment = $orderData['MonetaryDetails']?$orderData['MonetaryDetails']['Payments']['Payment']:null;
//         $payment = $orderData['MonetaryDetails']['Payments'];
        $paymentArr = array();
        if(isset($payment[0])){
            $paymentArr = $payment;
        }elseif(isset($payment)){
            $paymentArr[] = $payment;
        }
        $ebay_order_payment = array();
        foreach($paymentArr as $pay){
            $ebay_order_payment[] = array(
                'MonetaryDetails_Payments_Payment_FeeOrCreditAmount' => $pay['FeeOrCreditAmount'],
                'MonetaryDetails_Payments_Payment_FeeOrCreditAmount_currencyID' => $pay['FeeOrCreditAmount attr']['currencyID'],
                'MonetaryDetails_Payments_Payment_Payee' => $pay['Payee'],
                'MonetaryDetails_Payments_Payment_Payer' => $pay['Payer'],
                'MonetaryDetails_Payments_Payment_PaymentAmount' => $pay['PaymentAmount'],
                'MonetaryDetails_Payments_Payment_PaymentAmount_currencyID' => $pay['PaymentAmount attr']['currencyID'],
                'MonetaryDetails_Payments_Payment_PaymentReferenceID' => $pay['PaymentReferenceID'],
                'MonetaryDetails_Payments_Payment_PaymentStatus' => $pay['PaymentStatus'],
                'MonetaryDetails_Payments_Payment_PaymentTime' => $pay['PaymentTime'],
                'MonetaryDetails_Payments_Payment_ReferenceID' => $pay['ReferenceID']
            );
        }
        // 付款信息 end
        $data['payment'] = $ebay_order_payment;
        // 外部交易信息 start
        $ExternalTransaction = $orderData['ExternalTransaction'];
        $ExternalTransactionArr = array();
        if(isset($ExternalTransaction[0])){
            $ExternalTransactionArr = $ExternalTransaction;
        }elseif(isset($ExternalTransaction)){
            $ExternalTransactionArr[] = $ExternalTransaction;
        }
        $external_transaction = array();
        foreach($ExternalTransactionArr as $ext_trans){
            $external_transaction[] = array(
                'ExternalTransaction_ExternalTransactionID' => $ext_trans['ExternalTransactionID'],
                'ExternalTransaction_ExternalTransactionStatus' => $ext_trans['ExternalTransactionStatus'],
                'ExternalTransaction_ExternalTransactionTime' => $ext_trans['ExternalTransactionTime'],
                'ExternalTransaction_FeeOrCreditAmount' => $ext_trans['FeeOrCreditAmount'],
                'ExternalTransaction_FeeOrCreditAmount_currencyID' => $ext_trans['FeeOrCreditAmount attr']['currencyID'],
                'ExternalTransaction_PaymentOrRefundAmount' => $ext_trans['PaymentOrRefundAmount'],
                'ExternalTransaction_PaymentOrRefundAmount_currencyID' => $ext_trans['PaymentOrRefundAmount attr']['currencyID']
            );
        }
        // 外部交易信息 end
        $data['external_transaction'] = $external_transaction;
        // 订单明细 start
        $Transaction = $orderData['TransactionArray']['Transaction'];
        $TransactionArr = array();
        if(isset($Transaction[0])){
            $TransactionArr = $Transaction;
        }elseif(isset($Transaction)){
            $TransactionArr[] = $Transaction;
        }
        $ebay_order_detail = array();
        foreach($TransactionArr as $trans){
            if(isset($trans['Variation'])){
                if(! empty($trans['Variation']['SKU'])){
                    $trans['Item']['SKU'] = $trans['Variation']['SKU'];
                }
                if(! empty($trans['Variation']['VariationTitle'])){
                    $trans['Item']['Title'] = $trans['Variation']['VariationTitle'];
                }
                if(! empty($trans['Variation']['VariationViewItemURL'])){
                    $trans['Item']['Url'] = $trans['Variation']['VariationViewItemURL'];
                }
            }
            $ebay_order_detail[] = array(
                'TransactionArray_Transaction_ActualHandlingCost' => $trans['ActualHandlingCost'],
                'TransactionArray_Transaction_ActualHandlingCost_currencyID' => $trans && $trans['ActualHandlingCost attr'] ? $trans['ActualHandlingCost attr']['currencyID'] : '',
                'TransactionArray_Transaction_ActualShippingCost' => $trans['ActualShippingCost'],
                'TransactionArray_Transaction_ActualShippingCost_currencyID' => $trans && $trans['ActualShippingCost attr'] ? $trans['ActualShippingCost attr']['currencyID'] : '',
                'TransactionArray_Transaction_Buyer_Email' => $trans && $trans['Buyer'] ? $trans['Buyer']['Email'] : '',
                'TransactionArray_Transaction_CreatedDate' => $trans['CreatedDate'],
                'TransactionArray_Transaction_FinalValueFee' => $trans['FinalValueFee'],
                'TransactionArray_Transaction_FinalValueFee_currencyID' => $trans && $trans['FinalValueFee attr'] ? $trans['FinalValueFee attr']['currencyID'] : '',
                'TransactionArray_Transaction_TransactionSiteID' => $trans['TransactionSiteID'],
                'TransactionArray_Transaction_Platform' => $trans['Platform'],
                'TransactionArray_Transaction_InvoiceSentTime' => $trans['InvoiceSentTime'],
                'TransactionArray_Transaction_OrderLineItemID' => $trans['OrderLineItemID'],
                'TransactionArray_Transaction_QuantityPurchased' => $trans['QuantityPurchased'],
                'TransactionArray_Transaction_ShippedTime' => $trans['ShippedTime'],
                'TransactionArray_Transaction_TransactionID' => $trans['TransactionID'],
                'TransactionArray_Transaction_TransactionPrice' => $trans['TransactionPrice'],
                    
                'TransactionArray_Transaction_TransactionPrice_currencyID' => $trans && $trans['TransactionPrice attr'] ? $trans['TransactionPrice attr']['currencyID'] : "",
                'TransactionArray_Transaction_Item_IntegratedMerchantCreditCardEnabled' => $trans && $trans['Item'] ? $trans['Item']['IntegratedMerchantCreditCardEnabled'] : '',
                'TransactionArray_Transaction_Item_ApplicationData' => $trans&&$trans['Item']?$trans['Item']['ApplicationData']:'',
                'TransactionArray_Transaction_Item_ItemID' => $trans&&$trans['Item']?$trans['Item']['ItemID']:'',
                'TransactionArray_Transaction_Item_SellerInventoryID' => $trans&&$trans['Item']?$trans['Item']['SellerInventoryID']:'',
                'TransactionArray_Transaction_Item_Site' => $trans&&$trans['Item']?$trans['Item']['Site']:'',
                'TransactionArray_Transaction_Item_SKU' => $trans&&$trans['Item']?$trans['Item']['SKU']:'',
                'TransactionArray_Transaction_Item_Title' => $trans&&$trans['Item']?$trans['Item']['Title']:'',
                'TransactionArray_Transaction_Item_Url' => $trans&&$trans['Item']?$trans['Item']['Url']:'',
                'TransactionArray_Transaction_Item_ConditionID' => $trans&&$trans['Item']?$trans['Item']['ConditionID']:'',
                'TransactionArray_Transaction_Item_ConditionDisplayName' => $trans&&$trans['Item']?$trans['Item']['ConditionDisplayName']:'',
                    
                'TransactionArray_Transaction_ShippingDetails_SellingManagerSalesRecordNumber' => $trans['ShippingDetails'] ? $trans['ShippingDetails']['SellingManagerSalesRecordNumber'] : '',
                
                'TransactionArray_Transaction_Status_IntegratedMerchantCreditCardEnabled' => $trans['Status']?$trans['Status']['IntegratedMerchantCreditCardEnabled']:'',
                'TransactionArray_Transaction_Status_PaymentHoldStatus' =>  $trans['Status']?$trans['Status']['PaymentHoldStatus']:'',
                'TransactionArray_Transaction_Status_PaymentMethodUsed' => $trans['Status']? $trans['Status']['PaymentMethodUsed']:''
            );
            
        }
        
        // 订单明细 end
        $data['ebay_order_detail'] = $ebay_order_detail;

        // 订单运输方式与跟踪号 start
        $order_ship = array();
        foreach($TransactionArr as $trans){
            $shipmentArr = array();
            if(!$trans['ShippingDetails']){
                continue;
            }
            $shipment = $trans['ShippingDetails']['ShipmentTrackingDetails'];
            if(isset($shipment[0])){
                $shipmentArr = $shipment;
            }elseif(isset($shipment)){
                $shipmentArr[] = $shipment;
            }
            foreach($shipmentArr as $ship){
                $order_ship[] = array(
                        'OrderLineItemID'=>$trans['OrderLineItemID'],
                        'ShippingDetails_ShipmentTrackingDetails_ShipmentTrackingNumber' => $ship['ShipmentTrackingNumber'],
                        'ShippingDetails_ShipmentTrackingDetails_ShippingCarrierUsed' => $ship['ShippingCarrierUsed']
                );
            }
        }
        // 订单运输方式与跟踪号 end
        $data['order_ship'] = $order_ship;

//         print_r($data);exit;
        return $data;
    }


    /**
     * 保存数据
     */
    public function saveEbayOrder($orderData)
    {
        $userAccount = $this->_user_account;
        $companyCode = $this->_company_code;
        
        if(empty($userAccount)){
            throw new Exception('UserAccount未赋值');
        }
        if(empty($companyCode)){
            throw new Exception('CompanyCode未赋值');
        }
        
        // 格式化数据
        $data = $this->formatResponse($orderData);
        // 订单主信息 start
        $order = $data['ebay_order'];
        $ebay_order = array(
            'order_sn' => $order['OrderID'],
            'subtotal' => $order['Subtotal'],
            'subtotal_currency' => $order['Subtotal_currencyID'],
            'total' => $order['Total'],
            'total_currency' => $order['Total_currencyID'],
            'adjustment_amount' => $order['AdjustmentAmount'],
            'adjustment_amount_currency' => $order['AdjustmentAmount_currencyID'],
            'amoun_paid' => $order['AmountPaid'],
            'amoun_paid_currency' => $order['AmountPaid_currencyID'],
            'amount_saved' => $order['AmountSaved'],
            'amount_saved_currency' => $order['AmountSaved_currencyID'],
            'buyer_checkout_message' => $order['BuyerCheckoutMessage'],
            'buyer_user_id' => $order['BuyerUserID'],
            'cancel_reason' => $order['CancelReason'],
            'created_time' => $order['CreatedTime'],
            'paid_time' => $order['PaidTime'],
            'shipped_time' => $order['ShippedTime'],
            'creating_user_role' => $order['CreatingUserRole'],
            'eias_token' => $order['EIASToken'],
            'integrated_merchant_credit_card_enabled' => $order['IntegratedMerchantCreditCardEnabled'],
            'is_multi_leg_shipping' => $order['IsMultiLegShipping'],
            'payment_hold_status' => $order['PaymentHoldStatus'],
            'seller_eias_token' => $order['SellerEIASToken'],
            'seller_email' => $order['SellerEmail'],
            'seller_user_id' => $order['SellerUserID'],

            'order_status' => $order['OrderStatus'],//订单状态

            'checkout_status' => $order['CheckoutStatus_Status'],//付款状态
            'checkout_payment_status' => $order['CheckoutStatus_eBayPaymentStatus'],//到账状态
            'checkout_last_modified_time' => $order['CheckoutStatus_LastModifiedTime'],
            'checkout_payment_method' => $order['CheckoutStatus_PaymentMethod'],
                
            'address_id' => $order['ShippingAddress_AddressID'],
            'address_owner' => $order['ShippingAddress_AddressOwner'],
            'city_name' => $order['ShippingAddress_CityName'],
            'country' => $order['ShippingAddress_Country'],
            'country_name' => $order['ShippingAddress_CountryName'],
            'external_address_id' => $order['ShippingAddress_ExternalAddressID'],
            
            'consignee_name' => $order['ShippingAddress_Name'],
            'consignee_phone' => $order['ShippingAddress_Phone'],
            'consignee_zip' => $order['ShippingAddress_PostalCode'],
            'consignee_state' => $order['ShippingAddress_StateOrProvince'],
            'consignee_street1' => $order['ShippingAddress_Street1'],
            'consignee_street2' => $order['ShippingAddress_Street2'],
            
            'shipping_service' => $order['ShippingServiceSelected_ShippingService'],
            'shipping_service_cost' => $order['ShippingServiceSelected_ShippingServiceCost'],
            'shipping_service_cost_currency' => $order['ShippingServiceSelected_ShippingServiceCost_currencyID'],
            'shipping_service_priority' => $order['ShippingServiceSelected_ShippingServicePriority'],
            'shipping_service_additional_cost' => $order['ShippingServiceSelected_ShippingServiceAdditionalCost'],
            'shipping_service_additional_cost_currency' => $order['ShippingServiceSelected_ShippingServiceAdditionalCost_currencyID'],
            'shipping_insurance_cost' => $order['ShippingServiceSelected_ShippingInsuranceCost'],
            'shipping_insurance_cost_currency' => $order['ShippingServiceSelected_ShippingInsuranceCost_currencyID'],
            'import_charge' => $order['ShippingServiceSelected_ImportCharge'],
            'importCharge_currency' => $order['ShippingServiceSelected_ImportCharge_currencyID'],
            'expedited_service' => $order['ShippingServiceSelected_ExpeditedService'],
            'payment_methods' => $order['PaymentMethods'],
            'company_code' => $companyCode,
            'user_account' => $userAccount,
            'created'=>'0',
        );
        //获取客户邮箱 start
        $TransactionArr = $data['ebay_order_detail'];
        $buyer_email = '';
        foreach($TransactionArr as $trans){
            $ebay_order_detail = array(
                    'buyer_email' => $trans['TransactionArray_Transaction_Buyer_Email'],
            );
            $ebay_order_detail = $this->arrayNullToEmptyString($ebay_order_detail);
            if(preg_match('/@/', $ebay_order_detail['buyer_email'])){
                $buyer_email = $ebay_order_detail['buyer_email'];
            }
        }
        if(!empty($buyer_email)){
            $ebay_order['buyer_email'] = $buyer_email;
        }
        //获取客户邮箱 end
        
        $ebay_order = $this->arrayNullToEmptyString($ebay_order);
        $fields = array_keys($ebay_order);
        $ebay_order_row = Service_EbayOrder::getByField($ebay_order['order_sn'], 'order_sn', $fields);
        if($ebay_order_row){
            $this->_sup_task = false;
            // 差异比较
            $diff = array_diff_assoc($ebay_order, $ebay_order_row);
            unset($diff['created']);
            if(! empty($diff)){
                // 有差异，记录日志，并更新
                $log = array();
                foreach($diff as $k => $v){
                    $log[] = $k . ':from [' . $ebay_order_row[$k] . ']to[' . $ebay_order[$k] . ']';
                }
                $logRow = array('order_sn'=>$order['OrderID'],'content'=>implode("\n", $log),'create_time_sys'=>date('Y-m-d H:i:s'),'update_time_sys'=>date('Y-m-d H:i:s'));
                Service_EbayOrderLog::add($logRow);
            }
            //print_r($diff);
            $ebay_order['update_time_sys'] = date('Y-m-d H:i:s');
            Service_EbayOrder::update($ebay_order, $ebay_order_row['order_sn'], 'order_sn');           
        }else{
            $this->_sup_task = true;
            $ebay_order['buyer_email'] = $buyer_email;
            $ebay_order['created'] = '0';
            $ebay_order['create_time_sys'] = date('Y-m-d H:i:s');
            $ebay_order['update_time_sys'] = date('Y-m-d H:i:s');
            Service_EbayOrder::add($ebay_order);
        }
        // 订单主信息 end

        // 付款信息 start
        // 删除旧记录
        Service_EbayOrderPayments::delete($ebay_order['order_sn'], 'order_sn');
        $paymentArr = $data['payment'];
        foreach($paymentArr as $pay){
            $ebay_order_payment = array(
                'order_sn' => $order['OrderID'],
                'reference_id' => $pay['MonetaryDetails_Payments_Payment_ReferenceID'],
                
                'fee_or_credit_amount' => $pay['MonetaryDetails_Payments_Payment_FeeOrCreditAmount'],
                'fee_or_credit_amount_currency' => $pay['MonetaryDetails_Payments_Payment_FeeOrCreditAmount_currencyID'],
                'payee' => $pay['MonetaryDetails_Payments_Payment_Payee'],
                'payer' => $pay['MonetaryDetails_Payments_Payment_Payer'],
                'payment_amount' => $pay['MonetaryDetails_Payments_Payment_PaymentAmount'],
                'payment_amount_currency' => $pay['MonetaryDetails_Payments_Payment_PaymentAmount_currencyID'],
                'payment_reference_id' => $pay['MonetaryDetails_Payments_Payment_PaymentReferenceID'],
                'payment_status' => $pay['MonetaryDetails_Payments_Payment_PaymentStatus'],
                'payment_time' => $pay['MonetaryDetails_Payments_Payment_PaymentTime'],
                'company_code' => $companyCode,
                'user_account' => $userAccount,
                'update_time_sys'=>date('Y-m-d H:i:s')
            );
            $ebay_order_payment = $this->arrayNullToEmptyString($ebay_order_payment);
            Service_EbayOrderPayments::add($ebay_order_payment);
        }
        // 付款信息 end
        
        // 外部交易信息 start
        // 删除旧记录
        Service_EbayOrderExternalTransaction::delete($ebay_order['order_sn'], 'order_sn');
        $ExternalTransactionArr = $data['external_transaction'];
        foreach($ExternalTransactionArr as $ext_trans){
            $external_transaction = array(
                'order_sn' => $order['OrderID'],
                'external_transaction_id' => $ext_trans['ExternalTransaction_ExternalTransactionID'],
                'external_transaction_status' => $ext_trans['ExternalTransaction_ExternalTransactionStatus'],
                'external_transaction_time' => $ext_trans['ExternalTransaction_ExternalTransactionTime'],
                'fee_or_credit_amount' => $ext_trans['ExternalTransaction_FeeOrCreditAmount'],
                'fee_or_credit_amount_currency' => $ext_trans['ExternalTransaction_FeeOrCreditAmount_currencyID'],
                'payment_or_refund_amount' => $ext_trans['ExternalTransaction_PaymentOrRefundAmount'],
                'payment_or_refund_amount_currency' => $ext_trans['ExternalTransaction_PaymentOrRefundAmount_currencyID'],
                'company_code' => $companyCode,
                'user_account' => $userAccount,
                'update_time_sys'=>date('Y-m-d H:i:s')
            );
            $external_transaction = $this->arrayNullToEmptyString($external_transaction);
            Service_EbayOrderExternalTransaction::add($external_transaction);
        }
        // 外部交易信息 end
        $db = Common_Common::getAdapter();
        // 订单明细 start        
        // 删除旧记录
        Service_EbayOrderDetail::delete($ebay_order['order_sn'], 'order_sn');
        $TransactionArr = $data['ebay_order_detail'];
        foreach($TransactionArr as $trans){
        	$sku = $trans['TransactionArray_Transaction_Item_SKU'];
        	$title = $trans['TransactionArray_Transaction_Item_Title'];
        	if(empty($sku)){//title作为sku
        		$sku = md5($title);
        	}
            $ebay_order_detail = array(
                'order_sn' => $order['OrderID'],
                'item_id' => $trans['TransactionArray_Transaction_Item_ItemID'],
                'actual_handling_cost' => $trans['TransactionArray_Transaction_ActualHandlingCost'],
                'actual_handling_cost_currency' => $trans['TransactionArray_Transaction_ActualHandlingCost_currencyID'],
                'actual_shipping_cost' => $trans['TransactionArray_Transaction_ActualShippingCost'],
                'actual_shipping_cost_currency' => $trans['TransactionArray_Transaction_ActualShippingCost_currencyID'],
                'buyer_email' => $trans['TransactionArray_Transaction_Buyer_Email'],
                'created_date' => $trans['TransactionArray_Transaction_CreatedDate'],
                'final_value_fee' => $trans['TransactionArray_Transaction_FinalValueFee'],
                'final_value_fee_currency' => $trans['TransactionArray_Transaction_FinalValueFee_currencyID'],
                'transaction_site_id' => $trans['TransactionArray_Transaction_TransactionSiteID'],
                'platform' => $trans['TransactionArray_Transaction_Platform'],
                'invoice_sent_time' => $trans['TransactionArray_Transaction_InvoiceSentTime'],
                'order_line_item_id' => $trans['TransactionArray_Transaction_OrderLineItemID'],
                'quantity_purchased' => $trans['TransactionArray_Transaction_QuantityPurchased'],
                'shipped_time' => $trans['TransactionArray_Transaction_ShippedTime'],
                'transaction_id' => $trans['TransactionArray_Transaction_TransactionID'],
                'transaction_price' => $trans['TransactionArray_Transaction_TransactionPrice'],
                'transaction_price_currency' => $trans['TransactionArray_Transaction_TransactionPrice_currencyID'],
                
                'integrated_merchant_credit_card_enabled' => $trans['TransactionArray_Transaction_Item_IntegratedMerchantCreditCardEnabled'],
                'application_data' => $trans['TransactionArray_Transaction_Item_ApplicationData'],
                'seller_inventory_id' => $trans['TransactionArray_Transaction_Item_SellerInventoryID'],
                'site' => $trans['TransactionArray_Transaction_Item_Site'],
                'sku' => $sku,
                'title' => $title,
                'url' => $trans['TransactionArray_Transaction_Item_Url'],
                'condition_id' => $trans['TransactionArray_Transaction_Item_ConditionID'],
                'condition_display_name' => $trans['TransactionArray_Transaction_Item_ConditionDisplayName'],
                
                'selling_manager_sales_record_number' => $trans['TransactionArray_Transaction_ShippingDetails_SellingManagerSalesRecordNumber'],
                
                'payment_hold_status' => $trans['TransactionArray_Transaction_Status_PaymentHoldStatus'],
                'payment_method_used' => $trans['TransactionArray_Transaction_Status_PaymentMethodUsed'],
                'company_code' => $companyCode,
                'user_account' => $userAccount,
                'update_time_sys'=>date('Y-m-d H:i:s')
            );
            
            $ebay_order_detail = $this->arrayNullToEmptyString($ebay_order_detail);
            Service_EbayOrderDetail::add($ebay_order_detail);
            
            //item不存在,加入任务表
            $itemExist = Service_SellerItem::getByField($ebay_order_detail['item_id'],'item_id');
            if(!$itemExist){
                $table = Ebay_EbayServiceCommon::table_cron_load_ebay_item();                
                try{
                    $arr = array(
                        'item_id' => $ebay_order_detail['item_id'],
                        'company_code' => $companyCode,
                        'user_account' => $userAccount
                    );
                    $db->insert($table, $arr);
                }catch(Exception $e){
                    //
                }
            }
            
        }
        // 订单明细 end
        
        // 订单运输方式与跟踪号 start
        
        // 删除旧记录
        Service_EbayOrderShipDetail::delete($ebay_order['order_sn'], 'order_sn');
        $shipmentArr = $data['order_ship'];
        foreach($shipmentArr as $ship){
            $order_ship = array(
                'order_sn' => $order['OrderID'],
                'order_line_item_id' => $ship['OrderLineItemID'],
                'tracking_number' => $ship['ShippingDetails_ShipmentTrackingDetails_ShipmentTrackingNumber'],
                'carrier_used' => $ship['ShippingDetails_ShipmentTrackingDetails_ShippingCarrierUsed'],
                'company_code' => $companyCode,
                'user_account' => $userAccount,
                'create_time_sys'=>date('Y-m-d H:i:s'),
                'update_time_sys'=>date('Y-m-d H:i:s')
            );
            $order_ship = $this->arrayNullToEmptyString($order_ship);
            Service_EbayOrderShipDetail::add($order_ship);
        }
        // 订单运输方式与跟踪号 start      
        return $data;
    }


}