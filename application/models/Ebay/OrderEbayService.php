<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cl
 * Date: 13-6-17
 * Time: 下午4:21
 * To change this template use File | Settings | File Templates.
 */
//set_error_handler('error_function',E_WARNING);
class Ebay_OrderEbayService extends Ec_AutoRun{
    
    public function loadEbayOrder($loadId){
        //得到当前同步订单的关键参数
        $param=$this->getLoadParam($loadId);
        // Ec::showError(var_export($param,1),'params'.time());
        $userAccount=$param["user_account"];
        $companyCode=$param["company_code"];
        $start=$param["load_start_time"];
        $end=$param["load_end_time"];
        $count = $param["currt_run_count"];
        try {
            $orderCount = $this->callEbay($userAccount, $start, $end);           
        	$this->countLoad ( $loadId, 2, $orderCount);//运行结束
            return array('ask'=>'1','message'=>"eBay Time : ".$start." ~ ".$end.','.$userAccount.' order count '.$orderCount);
        } catch (Exception $e) {
            Common_ApiProcess::log("下载订单异常:".$e->getMessage());
            $this->countLoad($loadId, 3,0);
            Ec::showError("账号：".$userAccount.'发生错误，eBay时间：'.$start.' To '.$end.',错误原因：'.$e->getMessage(), 'runOrder_Fail_');
            return array('ask'=>'0','message'=>$e->getMessage());
        }

    }
    /**
     * 数据保存
     * @param unknown_type $dataOA
     * @throws Exception
     */
    public function saveOrder($dataOA){
		// 循环保存数据
		if (! is_array ( $dataOA )) {
			throw new Exception ( '数据错误' );
		}
		$db = Common_Common::getAdapter();
		$newEbayService = new Ebay_LoadEbayOrderService();
		foreach ( $dataOA as $v ) {
	        //日志
	        $v ["OrderID"] = trim ( $v ["OrderID"] );
	        $newEbayService->orderLoadLog($v ["OrderID"], $v);
		    // 该处应当整个处理做一个事物
		    $db->beginTransaction();
	        $userAccount = $v['user_account'];
		    try{
		        if(empty($userAccount)){
		            throw new Exception('saveOrder user_account not set');
		        }
                
                //方法调用顺序不可更换 start===================================
                //$addressId = $this->saveShippingAddress ( $v );
                
                if($v ["OrderStatus"]=='Completed'){//保存数据
                       //废弃
//                     $this->saveOrders ( $v,$addressId );
                }	
		        $this->saveTransation ( $v);		        	
		        $this->savePayment ( $v);		        	
		        $this->saveBuyer($v);
		        $this->saveEbayOrderOragin ( $v );
		        //方法调用顺序不可更换 end===================================

		        $newEbayService->saveOrderData($v);
		        $db->commit();
		        
		    }catch(Exception $e){
		       $db->rollback();
		       throw new Exception($e->getMessage()); 
		    }
		}
	}
    /**
     * 客户管理
     * @param unknown_type $data
     * @param unknown_type $userAccount
     */
	public function saveBuyer($data){
	    $userAccount = $data['user_account'];
	    if(!isset($data['TransactionArray']['Transaction'][0])){
	        $TransactionArrayT = array();
	        $TransactionArrayT[] = $data['TransactionArray']['Transaction'];
	        $TransactionArray = $TransactionArrayT;
	    }else{
	        $TransactionArray = $data['TransactionArray']['Transaction'];
	    }
	    $buyerMail = $TransactionArray [0]['Buyer']['Email'];
	    $arr = array(
	            'platform'=>'ebay',
	            'buyer_account' => empty($data ['BuyerUserID'])?'xxxxxxxxxxxxxxxx':$data ['BuyerUserID'],
        );
	    $exists = Service_Buyer::getByCondition($arr);

	    $arr['buyer_name'] = empty($data ['ShippingAddress']['Name'])?'':$data ['ShippingAddress']['Name'];
	    
	   
	    $arr['user_account'] = $userAccount;
	    $arr['buyer_tel'] = empty($data ['ShippingAddress']['Phone'])?'':$data ['ShippingAddress']['Phone'];
	    
	    if(empty($exists)){
	        $arr['buyer_email'] = $buyerMail;
	        Service_Buyer::add($arr);
	    }else{
	        if($buyerMail&&$buyerMail!='Invalid Request'&&$buyerMail!=''){
	            $arr['buyer_email'] = $buyerMail;
	        }
	        $exists = $exists[0];
	        Service_Buyer::update($arr,$exists['bid'],'bid');
	    }	   
	}
    /**
     * 根据EBAY数据构造数据库数据对象
     * @param $data
     */
    public function  saveEbayOrderOragin($data) {
        $userAccount = $data['user_account'];
        $OrderId = $data['OrderID'];
		$result = '';
		/**
		 * 解析数组，保存EBAY数据
		 */
		$CheckoutStatus = $data ["CheckoutStatus"];
		// 发货方式
		$ShippingDetails = $data ["ShippingDetails"];
		$ShippingServiceOptions = array ();
		if ($ShippingDetails != null) {
			$ShippingServiceOptions = $ShippingDetails ["ShippingServiceOptions"];
		}
		// 外部交易信息
		$ExternalTransaction = $data ["ExternalTransaction"];
		
		$ShippingServiceSelected = $data ["ShippingServiceSelected"];
		
		// 费用数据
		$MonetaryDetails = $data ["MonetaryDetails"];
		$Fee = array ();
		
		if ($MonetaryDetails != null) {
			$Payments = $MonetaryDetails ["Payments"];
			
			$Payment = array ();
			$Payer = @$Payments ["Payment"] ["Payer"];
			if ($Payer == null) {
				$Payment = $Payments ["Payment"];
			} else {
				$Payment [] = $Payments ["Payment"];
			}
			// 支付时间
			$paidtime = @$data ["PaidTime"];
			
			foreach ( $Payment as $v ) {
				// 保留交易费不为0的费用数据
				$temp = @$v ["FeeOrCreditAmount"];
				
				if ($temp != null) {
					if ($temp > 0) {
						$Fee = $v;
						if ($paidtime == null) {
							// 当支付时间为空时，选取支付金额大于0的支付时间作为付款时间
							$paidtime = @$Fee ["PaymentTime"];
						}
					}
				}
			}
			// 如果未空值，任意获取一条
			if ($Fee == null) {
				$Fee = $Payment [0];
			}
		}

		
		// EBAY订单主要数据
		$ebay_order_original = array (
		        "OrderID" => $OrderId,
		        "OrderStatus" => @$data ["OrderStatus"],
		        "AdjustmentAmount" => @$data ["AdjustmentAmount"],
		        "AmountPaid" => @$data ["AmountPaid"],
		        "AmountSaved" => @$data ["AmountSaved"],
		        "eBayPaymentStatus" => @$CheckoutStatus ["eBayPaymentStatus"],
		        "LastModifiedTime" => @$CheckoutStatus ["LastModifiedTime"],
		        "CheckoutStatus" => @$CheckoutStatus ["Status"],
		        "PaymentMethod" => @$CheckoutStatus ["PaymentMethod"],
		        "ShippingService" => @$ShippingServiceSelected ["ShippingService"],
		        "shippingservicecost" => @$ShippingServiceSelected ["ShippingServiceCost"],
		        "sellingmanagersalesrecordnumber" => @$ShippingDetails ["sellingmanagersalesrecordnumber"],
		        "CreatedTime" => @$data ["CreatedTime"],
		        "SellerEmail" => @$data ["SellerEmail"],
		        "subtotal" => @$data ["Subtotal"],
		        "Total" => @$data ["Total"],
		        "externaltransactionid" => @$Fee ["ReferenceID"],
		        "externaltransactiontime" => @$Fee ["PaymentTime"],
		        "FeeOrCreditAmount" => @$Fee ["FeeOrCreditAmount"],
		        "paymentorrefundamount" => @$Fee ["PaymentAmount"],
		        "buyeruserid" => @$data ["BuyerUserID"],
		        "paidtime" => $Fee ["PaymentTime"],
		        "shippedtime" => @$data ["ShippedTime"],
		        "currency" => @$data ["AmountPaid attr"] ["currencyID"],
		        "company_code" => Common_Company::getCompanyCode (),
		        "user_account" => $userAccount,
		        "buyer_note" => @$data ["BuyerCheckoutMessage"]
		)
		;
		$ebay_order_original ['modify_date_sys'] = date ( 'Y-m-d H:i:s' );
		
		// 保存到数据库，存在即更新，不存在则插入		
		$exist = Service_EbayOrderOriginal::getByField($OrderId,'OrderID');
		if ($exist != null) {
		    // EBAY订单主要数据
		    if($exist['OrderStatus']=='Completed'&&$exist['CheckoutStatus']=='Complete'&&$exist['eBayPaymentStatus']=='NoPaymentFailure'){//已经付款订单
		        $ebay_order_original = array(
                    "AdjustmentAmount" => @$data["AdjustmentAmount"],
                    "LastModifiedTime" => @$CheckoutStatus["LastModifiedTime"],
                    "ShippingService" => @$ShippingServiceSelected["ShippingService"],
		        )
		        ;
		    }
			Service_EbayOrderOriginal::update ( $ebay_order_original, $exist["eoo_id"], 'eoo_id' );
			
		} else {		    
			$ebay_order_original ['create_date_sys'] = date ( 'Y-m-d H:i:s' );
			Service_EbayOrderOriginal::add ( $ebay_order_original );
		}
	}

    public function saveTransation($data){
        $OrderId = $data['OrderID'];
        $userAccount = $data['user_account'];
		$result = "";
		// 单个交易数据
		$TransactionArray = $data ["TransactionArray"];
		
		if ($TransactionArray != null) {
			$Transaction = array ();
			// 向下取2级判断是否能取到值，若能取到，则说明数组为1维，否则是2维
			$temp = @$TransactionArray ['Transaction'] ['OrderLineItemID'];
			if ($temp == null) {
				$Transaction = $TransactionArray ["Transaction"];
			} else {
				$Transaction = $TransactionArray;
			}
			
			foreach ( $Transaction as $v ) {
				
				$ShippingDetails = $v ["ShippingDetails"];
				$Item = $v ["Item"];
				
				$OrderLineItemID = $v ["OrderLineItemID"];
				if ($OrderLineItemID == null) {
					continue;
				}
				$sku = @$Item ["SKU"];
				$tempItem = @$v ["Variation"];
				if (! empty ( $tempItem )) {
					$skuTemp = @$tempItem ["SKU"];
					if (! empty ( $skuTemp )) {
						$sku = $skuTemp;
					}
				}
				$itemId = $Item['ItemID'];
				
				$ebay_oder_transaction = array (
						"OrderID" => @$data ["OrderID"],
						"Buyer_Mail" => @$v ["Buyer"] ["Email"],
						"SellingManagerSalesRecordNumber" => @$ShippingDetails ["SellingManagerSalesRecordNumber"],
						"shippingcarrierused" => @$ShippingDetails ["ShipmentTrackingDetails"] ["ShippingCarrierUsed"],
						"shipmenttrackingnumber" => @$ShippingDetails ["ShipmentTrackingDetails"] ["ShipmentTrackingNumber"],
						"CreatedDate" => @$v ["CreatedDate"],
						"ItemID" => @$Item ["ItemID"],
						"Site" => @$Item ["Site"],
						"Title" => @$Item ["Title"],
						"ConditionID" => @$Item ["ConditionID"],
						"ConditionDisplayName" => @$Item ["ConditionDisplayName"],
						"QuantityPurchased" => @$v ["QuantityPurchased"],
						"TransactionID" => @$v ["TransactionID"],
						"TransactionPrice" => @$v ["TransactionPrice"],
						"FinalValueFee" => @$v ["FinalValueFee"],
						"TransactionSiteID" => @$v ["TransactionSiteID"],
						"Platform" => @$v ["Platform"],
						"ActualShippingCost" => @$v ["ActualShippingCost"],
						"ActualHandlingCost" => @$v ["ActualHandlingCost"],
						"OrderLineItemID" => @$v ["OrderLineItemID"],
						"sku" => $sku,
				        'user_account'=>$userAccount,
				        'company_code'=>Common_Company::getCompanyCode(),
				        'create_date_sys'=>date('Y-m-d H:i:s'),
				        'modify_date_sys'=>date('Y-m-d H:i:s'), 
				);
				// 保存到数据库，存在即更新，不存在则插入
				$condtion = array (
						"OrderId" => $OrderId,
						"OrderLineItemID" => $OrderLineItemID 
				);
				$exist = Service_EbayOrderTransaction::getByCondition ( $condtion );
				if ($exist != null) {
                    $condtion = array(
                        'OrderID' => $OrderId,
                        'CheckoutStatus' => 'Complete',
                        'eBayPaymentStatus' => 'NoPaymentFailure'
                    );
                    $complete = Service_EbayOrderOriginal::getByCondition($condtion);
                    if($complete){ // 已经付款过，跳过后续操作
                        return false;
                    }
                    Service_EbayOrderTransaction::update($ebay_oder_transaction, $exist[0]["EbayTransaction_Id"], 'EbayTransaction_Id');

                } else {
					Service_EbayOrderTransaction::add ( $ebay_oder_transaction );

				}

			}
		}
	}
    
	
    public function  savePayment($data){

        $OrderId = $data['OrderID'];
        $userAccount = $data['user_account'];
			// 费用数据
		$MonetaryDetails = $data ["MonetaryDetails"];
		if ($MonetaryDetails != null) {
			$Payments = $MonetaryDetails ["Payments"];
			$Payment = array ();
			$Payer = @$Payments ["Payment"] ["Payer"];
			if ($Payer == null) {
				$Payment = $Payments ["Payment"];
			} else {
				$Payment [] = $Payments ["Payment"];
			}
			
			foreach ( $Payment as $v ) {
				
				$ReferenceID = @$v ["ReferenceID"];
				
				$ebay_order_payment = array (
						"OrderID" => @$data ["OrderID"],
						"paymentstatus" => @$v ["PaymentStatus"],
						"payer" => @$v ["Payer"],
						"Payee" => @$v ["Payee"],
						"PaymentTime" => @$v ["PaymentTime"],
						"PaymentAmount" => @$v ["PaymentAmount"],
						"ReferenceID" => @$v ["ReferenceID"],
						"FeeOrCreditAmount" => @$v ["FeeOrCreditAmount"],
						"company_code"=>Common_Company::getCompanyCode(),
						"create_date_sys" =>date('Y-m-d H:i:s'),
						"modify_date_sys" =>date('Y-m-d H:i:s'),
				);
				// 保存到数据库，存在即更新，不存在则插入
				$condtion = array (
						"OrderID" => $OrderId,
						"referenceid" => $ReferenceID 
				);

				if(!empty($v ["ReferenceID"])){
				    Service_PaypalOrderTransaction::delete($v ["ReferenceID"],'pot_paypal_id');
				}				
				$exist = Service_EbayOrderPayment::getByCondition ( $condtion );
				if ($exist != null) {
				    $condtion = array('OrderID'=>$OrderId,'CheckoutStatus'=>'Complete','eBayPaymentStatus'=>'NoPaymentFailure');
				    $complete = Service_EbayOrderOriginal::getByCondition ( $condtion );
				    if($complete){//已经付款过，跳过后续操作
				        return false;
				    }
					unset($ebay_order_payment['create_date_sys']);
					Service_EbayOrderPayment::update ( $ebay_order_payment, $exist [0] ["eop_id"], 'eop_id' );
				} else {
					Service_EbayOrderPayment::add ( $ebay_order_payment );
				}
			}
		}
	}
    public function saveShippingAddress($data){
        $OrderId = $data['OrderID'];
        $userAccount = $data['user_account'];
			// 收件人数据
		$ShippingAddressTemp = $data ["ShippingAddress"];
		$ShippingAddress = array (
				"OrderID" => @$data ["OrderID"],
				// "ShippingAddress_Id"=>@$ShippingAddressTemp["ShippingAddress_Id"],
				"Name" => @$ShippingAddressTemp ["Name"],
				"Street1" => @$ShippingAddressTemp ["Street1"],
				"Street2" => @$ShippingAddressTemp ["Street2"],
				"CityName" => @$ShippingAddressTemp ["CityName"],
				"StateOrProvince" => @$ShippingAddressTemp ["StateOrProvince"],
				"Country" => @$ShippingAddressTemp ["Country"],
				"CountryName" => @$ShippingAddressTemp ["CountryName"],
				"Phone" => @$ShippingAddressTemp ["Phone"],
				"PostalCode" => @$ShippingAddressTemp ["PostalCode"],
				"AddressID" => @$ShippingAddressTemp ["AddressID"],
		        'AddressOwner'=>@$ShippingAddressTemp ["AddressOwner"],
				"ExternalAddressID" => @$ShippingAddressTemp ["ExternalAddressID"],
				"Plat_code" => @$ShippingAddressTemp ["Plat_code"] 
		)
		;
		// 保存到数据库，存在即更新，不存在则插入
		
		$exist = Service_ShippingAddress::getByField ( $OrderId, 'OrderID' );
		
		if ($exist != null) {
		    $complete = Service_ShippingAddress::getByField($OrderId,'OrderID');
		    if($complete&&$complete['is_modify']){//手工修改过地址，不再更新
		        return $exist ['ShippingAddress_Id'];
		    }		    
			unset ( $ShippingAddress ['ShippingAddress_Id'] );
						
		    Service_ShippingAddress::update ( $ShippingAddress, $exist ['ShippingAddress_Id'], 'ShippingAddress_Id' );		     
			
			return $exist ['ShippingAddress_Id'];
		} else {
			
			return Service_ShippingAddress::add ( $ShippingAddress );
		}
	}

    /**
     * 废弃 ===========================
     * 添加订单
     * @param unknown_type $data
     * @param unknown_type $addressId
     * @return boolean
     */
    public function saveOrders($data,$addressId){
        return;
        $OrderId = $data['OrderID'];
        $userAccount = $data['user_account'];
        
		$statusMap = array (
				'Completed' => '2',
				'Active' => '1',
				'Cancelled' => '0',
				'Inactive' => '0',
				'Shipped' => '4',
				'Complete' => '1' 
		);
		$siteId = '';
		$buyerMail = '';
		$purchaseQty = 0;//订单产品数量
		
		if(!isset($data['TransactionArray']['Transaction'][0])){
		    $TransactionArrayT = array();
		    $TransactionArrayT[] = $data['TransactionArray']['Transaction'];
		    $TransactionArray = $TransactionArrayT;
		}else{
		    $TransactionArray = $data['TransactionArray']['Transaction'];
		}
        foreach($TransactionArray as $trax){
		    $purchaseQty+=$trax['QuantityPurchased'];
		}
		$siteId = $TransactionArray [0] ['Item']['Site'];
		$buyerMail = $TransactionArray [0]['Buyer']['Email'];
			
		$order_status = isset ( $data ["OrderStatus"] ) ? $statusMap [$data ["OrderStatus"]] : '0';
        // load订单日志
        $loadLogRow = array(
            'ref_id' => $OrderId,
            'log_content' => serialize($data),
            'create_time' => date('Y-m-d H:i:s')
        );
        if(Service_OrderLoadLog::getByField($OrderId,'ref_id')){
            unset($loadLogRow['ref_id']);
            Service_OrderLoadLog::update($loadLogRow,$OrderId,'ref_id');            
        }else{
            Service_OrderLoadLog::add($loadLogRow);            
        }
		
		if($data ["CheckoutStatus"]['Status']=='Complete'){//已付款
		    $order_status = 2;
		    if($data ["CheckoutStatus"]['eBayPaymentStatus']!='NoPaymentFailure'){//付款未完成
		        $order_status = 1;
		        $con = array(
                    'ref_id' => $OrderId,
                    'op_id'=>'999',
                );
		        $exist = Service_OrderLog::getByCondition($con);
		        foreach($exist as $l){
		            Service_OrderLog::delete($l['log_id'],'log_id');
		        }
		        $logRow = array(
		                'ref_id' => $OrderId,
		                'log_content' => '付款但是未到账',
		                'data'=>print_r($data["CheckoutStatus"], true),
		                'op_id'=>'999',
		        );
		        Service_OrderLog::add($logRow);
		    }
		}
		$platform_fee = 0;
		// 费用数据
		$MonetaryDetails = $data ["MonetaryDetails"];
		if ($MonetaryDetails != null) {
            $Fee = array();
            $Payments = $MonetaryDetails["Payments"];
            $Payment = array();
            
            if(isset($Payments[0])){
                $Payment = $Payments["Payment"];
            }else{
                $Payment[] = $Payments["Payment"];
            }
            foreach($Payment as $v){
                // 保留交易费不为0的费用数据
                $temp = @$v["FeeOrCreditAmount"];
                
                if($temp != null){
                    if($temp > 0){
                        $Fee = $v;
                    }
                }
            }
            // 如果未空值，任意获取一条
            if($Fee == null){
                $Fee = $Payment[0];
            }
            $platform_fee = empty($Fee['FeeOrCreditAmount'])?0:$Fee['FeeOrCreditAmount'];
        }
		
		$row = array (
		        "platform" => 'ebay',

		        'order_type' => 'sale',//订单类型，sale正常销售订单,resend重发订单,refound退款,line线下订单
		        'create_type' => 'api',//创建类型：api下载的订单，upload批量上传订单，hand手工创建订单
		        
		        "order_status" => $order_status,
		        "create_method" => '2',
		        "refrence_no_platform" => $OrderId,
		        "shipping_address_id" => $addressId,
		        	
		        "company_code" => Common_Company::getCompanyCode (),
		        //"sync_status" => '0',
		        //'sync_time' => '0000-00-00 00:00:00',
		        "order_desc" => @$data ["BuyerCheckoutMessage"],
		        'date_create' => date ( 'Y-m-d H:i:s' ),
		        'date_last_modify' => date ( "Y-m-d H:i:s" ),
		        	
		        'shipping_method_platform' => @$data['ShippingServiceSelected'] ["ShippingService"],
		        'refrence_no_platform' => @$data ['OrderID'],
		        'currency' => @$data ['AmountPaid attr'] ['currencyID'],
		        	
		        'date_create_platform' => $this->getLocalTime(@$data ['CreatedTime']),
		        'date_paid_platform' => $this->getLocalTime(@$data ['PaidTime']),
		        'date_warehouse_shipping' => !empty($data ['ShippedTime'])?$this->getLocalTime(@$data ['ShippedTime']):null,

		        'amountpaid' => @$data ['AmountPaid'],
		        'subtotal' => @$data ['Subtotal'],
        		'ship_fee' => @$data['ShippingServiceSelected'] ['ShippingServiceCost'],
        		'platform_fee' => $platform_fee,
		        
		        'user_account' => $userAccount,
		        'buyer_id' => empty($data ['BuyerUserID'])?'':$data ['BuyerUserID'],
		        'buyer_name'=>empty($data ['ShippingAddress']['Name'])?'':$data ['ShippingAddress']['Name'],
		        'buyer_mail'=>empty($buyerMail)?'':$buyerMail,
		        'site' => $siteId,
		        'consignee_country' => isset ( $data ['ShippingAddress'] ['Country'] ) ? $data ['ShippingAddress'] ['Country'] : '',
		        'has_buyer_note'=>!empty($data ["BuyerCheckoutMessage"])?'1':'0',
		        'is_one_piece'=>$purchaseQty==1?'1':'0',//一票一件？
		);
		if (isset ( $data ['ShippedTime'] )) { // 已经出库了
		    $row ['order_status'] = '4';
		    $row ['sync_status'] = '1';
		    $row ['sync_time'] = $this->getLocalTime($data ['ShippedTime']);
		}		
		
		$exist = Service_Orders::getByField ( $OrderId, 'refrence_no_platform' );
		if ($exist) {
			
		    $condtion = array('OrderID'=>$OrderId,'CheckoutStatus'=>'Complete','eBayPaymentStatus'=>'NoPaymentFailure');
		    $complete = Service_EbayOrderOriginal::getByCondition ( $condtion );
		   
		    if($complete){ // 已经付款过，跳过后续操作
                $updateRow = array(
                    'shipping_method_platform' => @$data['ShippingServiceSelected']["ShippingService"],
                    'subtotal' => @$data ['Subtotal'],
                    'ship_fee' => @$data['ShippingServiceSelected'] ['ShippingServiceCost'],
                    'platform_fee' => $platform_fee,
                );
                if (isset ( $data ['ShippedTime'] )) { // 已经出库了                   
                    $updateRow ['sync_status'] = '1';
                    $updateRow ['sync_time'] = date ( 'Y-m-d H:i:s' );
                }                
                Service_Orders::update($updateRow, $exist['order_id'], 'order_id');
                return false;
            }
		    $order_id = $exist ['order_id'];

		    $opArr = array('2','3','4','5','6','7','0');
		    if(in_array($exist['order_status'],$opArr)){//订单操作未完成，不更新状态
		        unset($row['order_status']);
		    }
		    Service_Orders::update ( $row, $order_id, 'order_id' );		    
		    $this->saveOrderProduct($data, $order_id);	
		} else {	
		    $refrence_no_sys = Common_GetNumbers::getCode('CURRENT_ORDER_SYS_COUNT','SYS');//系统单号
		    
		    $row['refrence_no_sys'] = $refrence_no_sys;
		    
			$order_id =  Service_Orders::add ( $row );	
		    $this->saveOrderProduct($data, $order_id);
		}
	    //更新产品对应关系
	    Service_OrderForWarehouseProcessNew::updateOrderProductWarehouseSku($OrderId);
			
	}

    /**
     * 废弃==========================
     * 添加更新订单产品
     * @param unknown_type $data
     * @param unknown_type $order_id
     */
    public function saveOrderProduct($data,$order_id){ 
        return;
        $account = $data['user_account'];
        $TransactionArray = $data["TransactionArray"];
        
        Service_OrderProduct::delete($data["OrderID"], 'OrderID');
        if($TransactionArray != null){
            $Transaction = array();
            
            if(isset($TransactionArray['Transaction'][0])){ // 多个产品
                $Transaction = $TransactionArray["Transaction"];
            }else{
                $Transaction[] = $TransactionArray["Transaction"];
            }
            $orderFinalValueFee = 0;
            foreach($Transaction as $v){
                $itemId = @$v["Item"]["ItemID"];
                
                $sku = ! empty($v["Item"]['SKU']) ? $v["Item"]['SKU'] : "";
                if($v["Variation"]){
//                     Ec::showError(print_r($data, true), 'order_variation_');
                }
                $tempItem = @$v["Variation"];
                if(! empty($tempItem)){
                    $skuTemp = @$tempItem["SKU"];
                    if(! empty($skuTemp)){
                        $sku = $skuTemp;
                    }
                }
                $url = '';
                $pic = '';
                $itemRow = Service_SellerItem::getByField($itemId, 'item_id');
                if($itemRow){
                    $url = $itemRow['item_url'];
                    $picArr = $itemRow['pic_path'] ? explode('#:|:#', $itemRow['pic_path']) : '';
                    $pic = empty($picArr) ? 'null' : $picArr[0];
                }
                
                $sellerItem = Service_SellerItem::getByField($itemId,'item_id');
                $op_recv_account = '';
                if($sellerItem){
                    $op_recv_account = $sellerItem['paypal_email_address'];
                }
                
                $product_title = @$v["Item"]['Title'];
                $product_title = $product_title ? $product_title : '';
                $orderFinalValueFee+=$v['FinalValueFee'];
                
                $row = array(
                    'order_id' => $order_id,
                    'product_id' => 0,
                    'product_sku' => $sku,
                    'warehouse_sku' => '',
                    'product_title' => $product_title,
                    
                    'op_quantity' => @$v["QuantityPurchased"],
                    'op_ref_tnx' => @$v["TransactionID"],
                    'OrderID' => @$data["OrderID"],
                    'OrderIDEbay' => @$data["OrderID"],
                    'op_recv_account'=>$op_recv_account,
                    'op_ref_item_id' => $itemId,
                    'op_ref_buyer_id' => empty($data['BuyerUserID']) ? '' : $data['BuyerUserID'],
                    'op_ref_paydate' => $this->getLocalTime($data["PaidTime"]),
                    'op_site' => @$v["Item"]["Site"],
                    'op_record_id' => @$v["ShippingDetails"]['SellingManagerSalesRecordNumber'],
                    'op_add_time' => date("Y-m-d H:i:s"),
                    'op_update_time' => date("Y-m-d H:i:s"),
                    'unit_price' => $v['TransactionPrice'],//单价
                    'unit_finalvaluefee'=>$v['FinalValueFee']/$v['QuantityPurchased'],//单个成交费
                    'transaction_price'=>$v['TransactionPrice'] *$v['QuantityPurchased'],  //总成交费
                    'currency_code' => $v['TransactionPrice attr']['currencyID'],
                    'url' => $url,
                    'pic' => $pic
                );
                Service_OrderProduct::add($row);
            }
            $updateRow = array('finalvaluefee'=>$orderFinalValueFee);//总成交费
            Service_Orders::update($updateRow,$data["OrderID"],'refrence_no_platform');
            //订单销售类型
            Ebay_OrderEbayService::updateOrderListType($data["OrderID"]);
        }
    }
    /*
     * 废弃
     * 订单销售类型
     */
    public static function updateOrderListType($refId){
        $item_list_type_arr = array();
        $con = array('OrderID'=>$refId);
        $orderProduct = Service_OrderProduct::getByCondition($con);
        foreach($orderProduct as $p){
            $itemId = $p['op_ref_item_id'];
            if(empty($itemId)){
                continue;
            }
            $itemRow = Service_SellerItem::getByField($itemId, 'item_id');
            if($itemRow){
                if(empty($itemRow['list_type'])){
                    $item_list_type_arr['null'] = $itemId;//无法判定
                }else{
                    $item_list_type_arr[$itemRow['list_type']] = $itemRow['list_type'];
                }
            }else{
                $item_list_type_arr['null'][] = $itemId;
            }
        }
        
        
        //订单销售类型 1:拍卖，2:一口价，3:一口价+拍卖,null无法判定
        $item_list_type = '0';
        if(!isset($item_list_type_arr['null'])){
            //键值互换
            $item_list_type_arr = array_flip($item_list_type_arr);
            if(isset($item_list_type_arr['Chinese'])){
                $item_list_type = 1;
            }
            if(isset($item_list_type_arr['StoresFixedPrice'])){
                $item_list_type = 2;
            }
            if(isset($item_list_type_arr['FixedPriceItem'])){
                $item_list_type = 2;
            }
            if(isset($item_list_type_arr['StoresFixedPrice'])&&isset($item_list_type_arr['Chinese'])){
                $item_list_type = 3;
            }
            if(isset($item_list_type_arr['FixedPriceItem'])&&isset($item_list_type_arr['Chinese'])){
                $item_list_type = 3;
            }
        }
        //Ec::showError($refId.'='.$item_list_type."\n".print_r($item_list_type_arr,true),'order_item_list_type_');
        $updateRow = array('item_list_type'=>$item_list_type);//订单销售类型
        Service_Orders::update($updateRow,$refId,'refrence_no_platform');
        
    }

    /**
     * 统计一段时间段内EBAY订单数量
     * @param $token
     * @param $start
     * @param $end
     * @return int
     */
    public function countEbayOrder($userAccount,$start,$end){
		$token = Ebay_EbayLib::getUserToken ( $userAccount );
		$count = 0;
		
		$data = Ebay_EbayLib::getEbayOrders ( $token, $start, $end, 1 );
		// Ec::showError(var_export($data,true),"sadasads");
		$dataOA = array ();
		
		if ($data != null) {
			$dataResponse = @$data ['GetOrdersResponse'];
			if ($dataResponse != null && $dataResponse ['Ack'] == 'Success') {
				$count = @$dataResponse ['PaginationResult'] ['TotalNumberOfEntries'];
			}
		}
		return $count;
	}


	/**
	 * 请求ebay
	 * @param unknown_type $userAccount
	 * @param unknown_type $start
	 * @param unknown_type $end
	 * @param unknown_type $orderIds
	 * @throws Exception
	 * @return number
	 */
	public function callEbay($userAccount,$start,$end,$orderIds=array()){
	    $token = Ebay_EbayLib::getUserToken($userAccount);
	    if(! $token){
	        throw new Exception($userAccount . ' UserToken Ivalid');
	    }
	    Common_Common::checkTableColumnExist('orders', 'platform_ship_status');
	    Common_Common::checkTableColumnExist('orders', 'platform_ship_time');
        Common_ApiProcess::log("开始下载订单:[{$userAccount}][{$start}~{$end}]");
// 	    throw new Exception("1\n");
	    $TotalNumberOfEntries = 0;
	    // 第一次运行EBAY API 并获取总条数，
	    $page = 0;
	    $orderCount = 0;
	    while(true){
	        $page ++;
	        $data = Ebay_EbayLib::getEbayOrders($token, $start, $end, $page,$orderIds);
	       
	        if($data['GetOrdersResponse']['Ack'] == 'Failure'){
	            throw new Exception(print_r($data['GetOrdersResponse'], true));
	        }
	        $response = $data['GetOrdersResponse'];
	
	        $total = $response['PaginationResult']['TotalNumberOfEntries'];
            Common_ApiProcess::log("共{$total}条记录，当前第{$page}页");
	
	        if($TotalNumberOfEntries == 0){
	            $TotalNumberOfEntries = $total;
	        }elseif($TotalNumberOfEntries != $total){	            
// 	            Ec::showError("userAccount:{$userAccount},{$start}~{$end},load订单发生了交叉异常。。。。", 'intersect_order_load_');
                Common_ApiProcess::log("userAccount:{$userAccount},{$start}~{$end},load订单发生了交叉异常。。。。");
                $page=0;
	            $TotalNumberOfEntries = $total;
	            continue;
	            //throw new Exception('load订单发生了交叉异常。。。。');
	        }
	
	        $response = $response['OrderArray'];
	        if(isset($response['Order'])){
	            $response = $response['Order'];
	            $dataOA = array();
	            if(isset($response[0])){ // 只有一个订单
	                $dataOA = $response;
	            }else{
	                $dataOA[] = $response;
	            }
	            $orderCount += count($dataOA); // 统计数量
	            foreach($dataOA as $k => $v){
	                $v['user_account'] = $userAccount;
	                $v['company_code'] = Common_Company::getCompanyCode();
	                $dataOA[$k] = $v;
	            }
	            $this->saveOrder($dataOA); // 保存到数据表
	        }
	        if($data['GetOrdersResponse']['HasMoreOrders'] != 'true'){ // 不成功或者没有下一页
	            // 程序终止,注意：返回的HasMoreItems
	            // 是字符串类型
	            break;
	        }
	    }
	    return $orderCount;
	}
    /**
     * 
     * @param unknown_type $start
     * @param unknown_type $end
     * @param unknown_type $userAccount
     * @param unknown_type $ebOrderId
     * @param unknown_type $auto
     * @throws Exception
     * @return multitype:string
     */
	public function handerLoadOrder($start,$end,$userAccount,$ebOrderId=array()){
	    //得到当前同步订单的关键参数
	   
	    try {
	        $orderCount = $this->callEbay($userAccount, $start, $end,$ebOrderId);
	        return array('ask'=>1,'message'=>"eBay Time : ".$start." ~ ".$end.','.$userAccount.' order count '.$orderCount,'order_ids'=>'');
	    } catch (Exception $e) {
            Common_ApiProcess::log("下载订单异常:".$e->getMessage());
	        return array('ask'=>0,'message'=>"账号：".$userAccount.'发生错误，eBay时间：'.$start.' To '.$end.',错误原因：'.$e->getMessage());
	    }
	
	}
    //===============================load order start=============================================
    
	//---------------------------------------------- 旧的标记发货 start 废弃

	//订单出库更新eBay信息
	public function completeEbayOrder($account='',$company_code=''){
	    return $this->completeEbayOrderNew($account,$company_code);
	}	
	
	/**
	 * 订单出库更新eBay信息
	 * @param unknown_type $account
	 * @return multitype:multitype:number string unknown NULL
	 */
	public function completeEbayOrderNew($account='',$company_code=''){
	    	     
	    $sql = 'select  a.refrence_no_platform  from orders a where 1=1';
	    $sql .=" and a.order_status='4'";
	    $sql .=" and a.platform='ebay'";
	    $sql .=" and a.create_type='api'";
	    $sql .=" and a.order_type='sale'";
	    $sql .=" and a.sync_status in('0','2','3')";
	    if($account){
	        $sql .=" and a.user_account = '".$account."'";
	    }
	    if($company_code){
	        $sql .=" and a.company_code = '".$company_code."'";
	    }
// 	    	    echo $sql;exit;
	    $db = Common_Common::getAdapter();
	    $orders = $db->fetchAll($sql);
	    // 	    print_r($orders);//exit;
	    $return = array();
	    foreach($orders as $order){
	        $result = $this->completeSaleNew($order['refrence_no_platform']);
	        $return[] = $result;
	    }
	    // 	    exit;
	    return $return;
	}
	
	/**
	 * ----------------------------------------------废弃 
	 * 标记发货到ebay
	 * @param unknown_type $refId
	 * @throws Exception
	 * @return multitype:number string NULL
	 */
	public function completeEbayOrderByRefId($refId,$force=false){
	    return $this->completeSaleNew($refId);
	    
	    /*
	     * -----------------------------------------------以下废弃--------------------------------------------------------        
	     */
	    $return = array('ask'=>0,'message'=>'同步失败','ref_id'=>$refId);
	    try{
	        $order = Service_Orders::getByField($refId,'refrence_no_platform');
	        if(!$order){
	            throw new Exception('订单不存在或已删除');
	        }
	        if($order['platform']!='ebay'){
	            throw new Exception('参数platform!=ebay');
	        }
	        if($order['create_type']!='api'){
// 	            throw new Exception('参数create_type!=api');
	        }
	        if($order['order_type']!='sale'){
	            throw new Exception('参数order_type!=sale');
	        }
	        if(!$force){
	            $allow = array(
	                    '0',
	                    '2',
	                    '3'
	            );
	            if(!in_array($order['sync_status'], $allow)){
	                throw new Exception('订单同步状态不正确');
	            }
	        }
	        $con = array('order_id'=>$order['order_id']);
	        $orderProduct = Service_OrderProduct::getByCondition($con);
	        $needCompleteArr = array();
	        foreach($orderProduct as $v){
    	        if(! preg_match('/^[0-9]+(\-[0-9]+)?$/', $v['OrderIDEbay'])){//订单号不符合ebay规则
        	        continue;
        	    }
	            $return['OrderIDEbay'][] = $v['OrderIDEbay'];
	            $param = array(
	                    'OrderIDEbay' => $v['OrderIDEbay'],
	                    'refrence_no_platform' => $order['refrence_no_platform'],
	                    'shipping_method' => $order['shipping_method'],
	                    'shipping_method_no' => $order['shipping_method_no'],
	                    'date_warehouse_shipping' => $order['date_warehouse_shipping'],
                        'shipping_method_platform'=>$order['shipping_method_platform'],
	                    'user_account'=>$order['user_account']
	            );
	            $needCompleteArr[$v['OrderIDEbay']] = $param;
	        }
	        
	        foreach($needCompleteArr as $param){
	            $updateRow = array(
	                    'date_warehouse_shipping' => $param['date_warehouse_shipping'],
	                    'shipping_method_no' => $param['shipping_method_no'],
	                    'shipping_method' => $param['shipping_method'],
	            );
	            Service_Orders::update($updateRow,$param['OrderIDEbay'],'refrence_no_platform');	                          
	        }	
	        foreach($needCompleteArr as $param){
	            $this->completeEbayOrderProcess($param);
	        }        
	        $updateRow = array('sync_status'=>1,'sync_time'=>date('Y-m-d'));
	        Service_Orders::update($updateRow,$refId,'refrence_no_platform');
	        
	        $content = '订单标记发货同步到eBay成功';
	        // 这里还有日志信息，以后添加
	        $logRow = array(
	                'ref_id' => $refId,
	                'log_content' => $content,
	                'op_id' => ''
	        );
	        Service_OrderProcess::writeOrderLog($logRow);

	        $return['message'] = '同步成功';
	        $return['ask'] = 1;
	        
	    }catch(Exception $e){
	        $return['message'] = $e->getMessage();
	    }
	    return $return;
	}
	
	/**
	 * ----------------------------------------------废弃
	 * 订单出库更新eBay信息处理
	 * @param unknown_type $row
	 * $param = array(
                    'OrderIDEbay' => $order['OrderIDEbay'],
                    'refrence_no_platform' => $order['refrence_no_platform'],
                    'shipping_method' => $order['shipping_method'],
                    'shipping_method_no' => $order['shipping_method_no'],
                    'date_warehouse_shipping' => $order['date_warehouse_shipping'],
                    'user_account'=>$order['user_account']
                );
	 * @throws Exception
	 */
	public function completeEbayOrderProcess($row){
	    $row = $this->formatCompleteData($row);
	    if(! preg_match('/^[0-9]+(\-[0-9]+)?$/', $row['OrderIDEbay'])){//订单号不符合ebay规则
	        throw new Exception('订单号不符合ebay规则-->'.$row['refrence_no_platform'].'['.$row['OrderIDEbay'].']');
	    }	
	    /**----------------------------------------------------------------------**/	   
	    $logRow = array(
	            'ref_id' => $row['OrderIDEbay'],
	            'log_content' => '订单标记发货开始同步到ebay',
	            'data'=>'同步参数：' . print_r($row, true),
	            'op_id' => ''
	    );
	    Service_OrderProcess::writeOrderLog($logRow);
	
	    $userAccount = $row["user_account"];
	    $token = Ebay_EbayLib::getUserToken($userAccount);
	
	    if(!$token){
	        throw new Exception('账号'.$userAccount.'未设置秘钥');
	    }
	    $ooo = Service_Orders::getByField($row['OrderIDEbay'],'refrence_no_platform');
	    $allowSyncStatus = array('0','1','2','3');
	    if(!in_array($ooo['sync_status'],$allowSyncStatus)){//判断同步状态
	        continue;
	    }
	    
	    $return = Ebay_EbayLib::CompleteSale($token, $row['OrderIDEbay'], $row['shipping_method_no'], $row['shipping_method'], $row['date_warehouse_shipping']);
	    $return['request'] = $row;

	    
	    if($return['CompleteSaleResponse']['Ack']=='Failure'){//email

	        $body = print_r($return,true);
	        $body = str_replace("\n", '<br/>', $body);
	        $body = str_replace(" ", '&nbsp;', $body);
	        
	        $mailParam = array(
	                'bodyType' => 'html',
	                'email' => array('eb-error@eccang.com'),
	                'subject' => '标记发货不成功 ['. date('Y-m-d H:i:s').']',
	                'body' => $body,
	        );
	        Common_Email::send($mailParam);
	    }
	    
// 	    print_r($return);exit;
	    if($return['CompleteSaleResponse']['Ack'] != 'Failure'){ // 当同步状态为3时，继续同步	        
	        $updateRow = array(
	                'sync_status' => '1',
	                'sync_time' => date('Y-m-d H:i:s')
	        );
	        $updateResult = Service_Orders::update($updateRow, $row['OrderIDEbay'], 'refrence_no_platform');
	        $content = '订单标记发货同步到eBay成功';
	        // 这里还有日志信息，以后添加
	        $logRow = array(
	                'ref_id' => $row['OrderIDEbay'],
	                'log_content' => $content,
	                'data'=>'标记参数：' . print_r($row, true),
	                'op_id' => ''
	        );
	        Service_OrderProcess::writeOrderLog($logRow);
	        
	        
	    }else{
	        // 记录错误信息 
	        $updateRow = array(
	                'sync_status' => '2',
	                'sync_time' => date('Y-m-d H:i:s')
	        );
	        $content = '订单同步异常';
	        if($return['CompleteSaleResponse']['Errors']['ErrorCode'] == '12006'){
	            $updateRow['sync_status'] = '5'; // 订单已被删除
	            $content .= '订单已被删除';
	        }elseif($return['CompleteSaleResponse']['Errors']['ErrorCode'] == '21916964'){
	            $updateRow['sync_status'] = '3'; // 数据异常，运单号被占用
	            $content .= '订单运单号被占用';
	        }elseif($return['CompleteSaleResponse']['Errors']['ErrorCode'] == '21919089'){
	            $updateRow['sync_status'] = '1'; // 已经标记发货了。。。
	            $content = '订单已经标记发货了';
	        }elseif($return['CompleteSaleResponse']['Errors']['ErrorCode'] == '20822'){
	            $updateRow['sync_status'] = '6'; // Invalid ItemID or TransactionID
	            $content = 'Invalid ItemID or TransactionID';
	        }
	        //日志
	        $logRow = array(
	                'ref_id' => $row['OrderIDEbay'],
	                'log_content' => $content,
	                'data'=>'标记信息'.print_r($row,true)."\n".'eBay返回信息：' . print_r($return['CompleteSaleResponse']['Errors'], true),
	                'op_id' => ''
	        );
	        Service_OrderProcess::writeOrderLog($logRow);

	        $updateResult = Service_Orders::update($updateRow, $row['OrderIDEbay'], 'refrence_no_platform');
	                
	        if($row['refrence_no_platform']!=$row['OrderIDEbay']){//拆单或者合单
	            $logRow = array(
	                    'ref_id' => $row['refrence_no_platform'],
	                    'log_content' => $content,
	                    'op_id' => ''
	            );
	            Service_OrderProcess::writeOrderLog($logRow);
	        }
	
	        throw new Exception(print_r($return['CompleteSaleResponse']['Errors'], true));
	    }	
 
	}

	//----------------------------------------------旧的标记发货 end 废弃

	/**
	 * 获取标记发货运输方式
	 * @param unknown_type $row
	 * @return string
	 */
	public function getShippingMark($row){
	    if($row['carrier_name']){//如果有承运商，使用承运商代码
	        $row['shipping_method'] = $row['carrier_name'];
	        return $row;
	    }
	    $con = array(
	            'platform' => $row['platform'],
	            'shipping_method_code' => $row['shipping_method']
	    );
	    $serviceRow = Service_ShippingMethodPlatform::getByCondition($con);//仓库运输方式优先
	    if($serviceRow){//
	        $serviceRow = array_pop($serviceRow);
	    }else{
	        $con = array(
	                'platform' => $row['platform'],
	                'shipping_method_code' => $row['shipping_method_platform']
	        );
	        $serviceRow = Service_ShippingMethodPlatform::getByCondition($con);
	        if($serviceRow){
	            $serviceRow = array_pop($serviceRow);
	        }
	    }
	     
	    if($serviceRow && !empty($serviceRow['platform_shipping_mark'])){//有运输方式对应关系，取对应关系值
	        $row['shipping_method'] = $serviceRow['platform_shipping_mark'];
	    }else{
	        $row['shipping_method'] = '';//没有运输方式对应关系，设置运输代码为空
	    }
	    return $row;
	}
	/**
	 * 格式化数据
	 * @param unknown_type $row
	 * @return Ambigous <string, unknown_type>
	 */
	public function formatCompleteData($row){
	    $shipping_method = $row['shipping_method'];
	    $shipping_method_platform = $row['shipping_method_platform'];
	    // 时间格式化
	    if(empty($row['date_warehouse_shipping']) || strtotime($row['date_warehouse_shipping']) < strtotime('2001-01-01')){
	        $row['shipping_method_no'] = '';
	        $row['shipping_method'] = '';
	    }else{
	        $row['date_warehouse_shipping'] = $this->getEbayTime($row['date_warehouse_shipping']); // 转换为eBay时间
	        $row['date_warehouse_shipping'] = date('Y-m-d\TH:i:s.000\Z', strtotime($row['date_warehouse_shipping']));
            
	        $row['shipping_method_no'] = empty($row['shipping_method_no']) ? '' : $row['shipping_method_no'];
	    }
	    //获取标记发货运输方式
	    $row = $this->getShippingMark($row);
	    //==================================
	    if(empty($row['shipping_method'])){
	        $message = "订单仓库发货方式:[{$shipping_method}],买家选择发货方式:[{$shipping_method_platform}]";
	        $message.= ",系统未找到对应的 [平台发货运输方式(买家所看到的运输方式)],系统忽略”平台发货运输方式(买家所看到的运输方式)“与”运单号“,请进入 ”运输方式映射“ 设定";
	        // 这里还有日志信息，以后添加
	        $logRow = array(
	                'ref_id' => $row['refrence_no_platform'],
	                'log_content' => $message,
                    'data'=>'标记参数：' . print_r($row, true),
	                'op_id' => ''
	        );
	        Service_OrderProcess::writeOrderLog($logRow);
	    }	    
	    
	    return $row;
	}
	
	//-----------------------------------------------------------新的标记发货 start
	/**
	 * 获取订单相关订单
	 * @param unknown_type $refId
	 * @param unknown_type $update
	 * @return unknown
	 */
	public function getEbayOrderByRefId($refId,$update=true){
	    $order = Service_Orders::getByField($refId,'refrence_no_platform');
	    $db = Common_Common::getAdapter();
	    $sql = "select a.user_account,a.company_code,a.carrier_name,a.refrence_no_platform,a.date_warehouse_shipping,a.shipping_method_no,a.shipping_method,b.OrderIDEbay from orders a INNER JOIN order_product b on a.refrence_no_platform = b.OrderID where a.refrence_no_platform='{$refId}'";
	    $result = $db->fetchAll($sql);
	    foreach($result as $k=>$v){
	        if(empty($v['OrderIDEbay'])){
	            unset($result[$k]);
	            continue;
	        }
	        if($update){
	            $updateRow = array(
	                    'date_warehouse_shipping' => $v['date_warehouse_shipping'],
	                    'shipping_method_no' => $v['shipping_method_no'],
	                    'shipping_method' => $v['shipping_method'],
	            );
	            Service_Orders::update($updateRow,$v['OrderIDEbay'],'refrence_no_platform');
	        }	        
	    }	    
	    return $result;
	}
	/**
	 * 标记发货
	 * @param unknown_type $refId
	 * @return Ambigous <multitype:number string unknown , NULL, multitype:>
	 */
	public function completeSaleNew($refId){
	    $completeSale = new Ebay_Order_CompleteSale($refId);
	    return $completeSale->completeSale();
	    
	    $return = array('ask'=>0,'message'=>'','ref_id'=>$refId,'Ack'=>'Failure');
        $field = array(
            'user_account',
            'date_warehouse_shipping',
            'shipping_method_no',
            'shipping_method',
            'refrence_no_platform',
            'refrence_no',
            'sync_status',
            'shipping_method_platform',
            'platform',
            'data_source',
            'carrier_name'
        );
	    $row = Service_Orders::getByField($refId,'refrence_no_platform',$field);
	    
	    if(!$row){
	        return false;
	    }
	    $userAccount = $row["user_account"];
	    $token = Ebay_EbayLib::getUserToken($userAccount);
	    if(!$token){
	        return false;
	    }
	    //额外处理
	    $row = $this->formatCompleteData($row);
	    if($row['sync_status']=='1'){
	        $return['ask'] = 1;
	        $return['Ack'] = 'Success';
	        $return['message'] = '订单标记发货同步到eBay成功';
// 	        return $return;
	    }
// 	    print_r($row);exit;
	    if(! preg_match('/^[0-9]+(\-[0-9]+)?$/', $row['refrence_no'])){//订单号不符合ebay规则 
	        $result = $this->getEbayOrderByRefId($row['refrence_no_platform'],false);
	        foreach($result as $k=> $v){
	            $order = Service_Orders::getByField($v['OrderIDEbay'],'refrence_no');
	           
	            if($order['sync_status']=='1'){//已经同步，从数组中取消
	                unset($result[$k]);//
	            }
	            if($order['sync_status']=='5'){//订单已被删除
	                unset($result[$k]);//
	            }
	            if($order['sync_status']=='6'){//Invalid ItemID or TransactionID
	                unset($result[$k]);//
	            }
	        }
	        $subOrderRefIds = array();
	        foreach($result as $k=> $v){
	            $subOrderRefIds[] = $v['OrderIDEbay'];
	        }
	         
	        if(!empty($result)){//还有未同步的子订单
	            $message = '该订单为拆分/合并订单，还有对应订单没有同步完成,对应订单号'.implode(',', $subOrderRefIds);
	            $logRow = array(
	                    'ref_id' => $row['refrence_no'],
	                    'log_content' => $message,
	                    'op_id' => ''
	            );
	            Service_OrderProcess::writeOrderLog($logRow);
	            $return['ask'] = 0; 
	            $return['message'] = $message;
	        }else{
	            $message = '订单同步完成';
                $logRow = array(
                    'ref_id' => $row['refrence_no'],
                    'log_content' => '订单同步完成',
                    'op_id' => ''
                );
                Service_OrderProcess::writeOrderLog($logRow);
                $updateRow = array(
                    'sync_status' => '1',
                    'sync_time' => date('Y-m-d H:i:s')
                );
                $updateResult = Service_Orders::update($updateRow, $row['refrence_no_platform'], 'refrence_no_platform');
                $return['ask'] = 1;
	            $return['Ack'] = 'Success';
                $return['message'] = $message;
            }
	    }else{
	        if($row['sync_status']=='3'){//运单号被占用,只同步标记发货状态
	            $row['shipping_method_no']='';
	        }
	        
	        Ec::showError(print_r($row,true),'completeEbayOrder');
	        $rs = Ebay_EbayLib::CompleteSale($token, $row['refrence_no'], $row['shipping_method_no'], $row['shipping_method'], $row['date_warehouse_shipping']);
	        Ec::showError(print_r($rs,true),'completeEbayOrder');
            //避免API返回空值
            $return['Ack'] = isset($rs['CompleteSaleResponse']['Ack']) ? $rs['CompleteSaleResponse']['Ack'] : 'Failure';
            if ($return['Ack'] != 'Failure') {
	            $o = Service_Orders::getByField($row['refrence_no_platform'], 'refrence_no_platform');
	            if($o['sync_status']!='1'){//判断下，避免垃圾日志
	                $updateRow = array(
	                        'sync_status' => '1',
	                        'sync_time' => date('Y-m-d H:i:s')
	                );
	                $updateResult = Service_Orders::update($updateRow, $row['refrence_no_platform'], 'refrence_no_platform');
	                $message = '订单标记发货，并同步到eBay成功->未上传跟踪号';
	                if(!empty( $row['shipping_method'])&&!empty( $row['shipping_method_no'])&&strtoupper( $row['shipping_method_no'])!='NULL'){
	                	$message = "订单标记发货，并同步到eBay成功->跟踪号上传成功[承运商：".$row['shipping_method']." - 跟踪号：".$row['shipping_method_no']."]";
	                }
	               
	                // 这里还有日志信息，以后添加
	                $logRow = array(
	                        'ref_id' => $row['refrence_no_platform'],
	                        'log_content' => $message,
	                        'data'=>'标记参数：' . print_r($row, true),
	                        'op_id' => ''
	                );
	                Service_OrderProcess::writeOrderLog($logRow);
	            }
	           
	            $return['ask'] = 1;
	            $return['message'] = $message;
	             
	        }else{
                // 记录错误信息
                $updateRow = array(
                    'sync_status' => '2',
                    'sync_time' => date('Y-m-d H:i:s')
                );
                $message = '订单同步异常,';
                $errorCode = isset($rs['CompleteSaleResponse']['Errors']['ErrorCode']) ? $rs['CompleteSaleResponse']['Errors']['ErrorCode'] : '';
                switch ($errorCode) {
                    case '12006':
                        $updateRow['sync_status'] = '5'; // 订单已被删除
                        $message .= '订单已被删除';
                        break;
                    case '21916964':
                        $updateRow['sync_status'] = '3'; // 数据异常，运单号被占用
                        $message .= '订单运单号被占用';
                        break;
                    case '21919089':
                        $updateRow['sync_status'] = '1'; // 已经标记发货了。。。
                        $message = '订单已经标记发货了';
                        break;
                    case '20822':
                        $updateRow['sync_status'] = '6'; // Invalid ItemID or TransactionID
                        $message .= 'Invalid ItemID or TransactionID';
                        break;
                }

	            //日志
	            $logRow = array(
	                    'ref_id' => $row['refrence_no_platform'],
	                    'log_content' => $message,
	                    'data'=>'标记信息'.print_r($row,true)."\n".'eBay返回信息：' . print_r($rs['CompleteSaleResponse']['Errors'], true),
	                    'op_id' => $updateRow['sync_status']+1000
	            );
	            Service_OrderProcess::writeOrderLog($logRow);
	            	        
	            $updateResult = Service_Orders::update($updateRow, $row['refrence_no_platform'], 'refrence_no_platform');
	            $return['ask'] = 0;
	            $return['message'] = $message;
	        }
	    }
	    
	    return $return;
	}
	//-----------------------------------------------------------新的标记发货 end
}