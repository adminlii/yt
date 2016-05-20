<?php
class Platform_OrderController extends Ec_Controller_Action {
	public function preDispatch() {
        $this->tplDirectory = "platform/views/order/";
        $this->serviceClass = new Service_Orders();        
    }
	public function acsTAction(){
        $content = file_get_contents(__FILE__);
        preg_match_all('/\s+([a-zA-Z0-9]+)Action/', $content, $m);
        foreach($m[1] as $k => $v){
            if(in_array($v, array(
                'all',
                'index'
            ))){
                continue;
            }
            $arr[$v] =  strtolower(preg_replace('/([A-Z])/u', '-\\1', $v));
            
            echo $arr[$v]."\n";
        }
//         print_r($arr);
        exit();
    }
	public function listAction() {
	    $this->forward('list','order-list','order');
    }
	
	public function exportAction(){
		if($this->getRequest()->isPost()){
		    set_time_limit(0);
			$tpl_id = $this->_request->getParam('tpl_id','0');
			$orderIds = $this->_request->getParam('orderId',array());
			$process = new Service_OrderProcess();
			$process->exportProcess($orderIds, $tpl_id);
		}		
	}

	public function baseExportAction(){
		if($this->getRequest()->isPost()){
		    ini_set('memory_limit', '500M');
			$tpl_id = $this->_request->getParam('tpl_id','0');
			$orderIds = $this->_request->getParam('orderId',array());
			if(count($orderIds)>1000){
                header("Content-type: text/html; charset=utf-8"); 
			    die('一次最多导出1000条订单数据');
			}
			$process = new Service_OrderProcess();
			$process->baseExportProcess($orderIds);
		}
	}
	public function createAction() {
		$orderId = $this->getRequest()->getParam('order_id',0);
		if($orderId){
			$orderRow = Service_Orders::getByField($orderId,'order_id');
			
			if($orderRow){
				if($orderRow['create_method']==2){
					$this->_redirect('/order/order/detail/orderId/'.$orderRow['order_id']);exit;
				}else{
					$con = array('order_id'=>$orderId);
					$orderProducts = Service_OrderProduct::getByCondition($con);	
					$orderRow['order_product'] = $orderProducts;
					$addressRow = Service_ShippingAddress::getByField($orderRow['shipping_address_id'],'ShippingAddress_Id');

// 					print_r($addressRow);exit;
					$con = array('order_id'=>$orderId);
					$order_product_rows = Service_OrderProduct::getByCondition($con);
					foreach($order_product_rows as $k=>$v){
						$product = Service_Product::getByField($v['product_id'],'product_id');
						$order_product_rows[$k]['product_sku'] = $product['product_sku'];
						$order_product_rows[$k]['product_title'] = $product['product_title'];
						$order_product_rows[$k]['product_title_cn'] = $product['product_title_cn'];
					}
					$this->view->order = $orderRow;
					$this->view->address = $addressRow;
					$this->view->orderProduct = $order_product_rows;
				}
				
			}
		}
		if($this->getRequest()->isPost()){
			$orderR = array(
					/***********/
					//'order_id' => $this->getRequest()->getParam('',''),
					'platform' => $this->getRequest()->getParam('platform','b2c'),
					'order_status' => $this->getRequest()->getParam('order_status','1'),
					'create_method' => $this->getRequest()->getParam('create_method','1'),
					'customer_id' => $this->getRequest()->getParam('customer_id','0'),
					'company_code' => $this->getRequest()->getParam('company_code',''),
					'shipping_method' => $this->getRequest()->getParam('courier',''),
					'warehouse_id' => $this->getRequest()->getParam('warehouse_id','0'),
					'order_desc' => $this->getRequest()->getParam('order_desc',''),
					//'date_create' => date('Y-m-d H:i:s'),
					'date_release' => $this->getRequest()->getParam('date_release',''),
					'date_warehouse_shipping' => $this->getRequest()->getParam('date_warehouse_shipping',''),
					'date_last_modify' => date('Y-m-d H:i:s'),
					'operator_id' => $this->getRequest()->getParam('operator_id',''),
					'refrence_no' => $this->getRequest()->getParam('refrence_no',''),
					//'refrence_no_platform' => $this->getRequest()->getParam('refrence_no_platform',''),
					'shipping_address_id' => $this->getRequest()->getParam('shipping_address_id',''),
					'currency' => $this->getRequest()->getParam('currency',''),
					'refrence_no_warehouse' => $this->getRequest()->getParam('refrence_no_warehouse',''),
					'shipping_method_no' => $this->getRequest()->getParam('shipping_method_no',''),
					'sync_status' => $this->getRequest()->getParam('sync_status','0'),
					'sync_time' => date('Y-m-d H:i:s'),
					/***********/
					);
			$addressR = array(
					//'ShippingAddress_Id' => $this->getRequest()->getParam('',''),
					'Name' => $this->getRequest()->getParam('name',''),
					'Street1' => $this->getRequest()->getParam('address1',''),
					'Street2' => $this->getRequest()->getParam('address2',''),
					'CityName' => $this->getRequest()->getParam('city',''),
					'StateOrProvince' => $this->getRequest()->getParam('province',''),
					'Country' => $this->getRequest()->getParam('country',''),
					'CountryName' => $this->getRequest()->getParam('country',''),
					'Phone' => $this->getRequest()->getParam('telphone',''),
					'PostalCode' => $this->getRequest()->getParam('zipcode',''),
					'AddressID' => $this->getRequest()->getParam('address_id',''),
					'AddressOwner' => $this->getRequest()->getParam('address_owner',''),
					'ExternalAddressID' => $this->getRequest()->getParam('external_address_id',''),
					//'OrderID' => $this->getRequest()->getParam('',''),
					'Plat_code' => $this->getRequest()->getParam('plate_code','ec'),
					'company_code' => $this->getRequest()->getParam('company_code',''),
					//'create_date_sys' =>  date('Y-m-d H:i:s'),
					'modify_date_sys' =>  date('Y-m-d H:i:s'),
					'user_account'=>$this->getRequest()->getParam('user_account',''),
					);
			$orderProductRs = $this->getRequest()->getParam('op_quantity',array());
			$order_product = array();
			foreach($orderProductRs as $k=>$v){
				$order_product[] = array('product_id'=>$k,'op_quantity'=>$v);
			}	
			$row = array(
					'order'=>$orderR,
					'address'=>$addressR,					
                    'order_product'=>$order_product,
            );
			$process = new Service_OrderProcess();
			if($orderId){
				$return = $process->updateOrderTransaction($row, $orderId);
			}else{
				$return = $process->createOrderTransaction($row);
			}
			
			die(Zend_Json::encode($return));
		}
		$warehouses = Service_Warehouse::getAll();
// 		print_r($warehouses);exit;
		$this->view->warehouse = $warehouses;

		$countrys = Service_Country::getAll();
// 				print_r($countrys);exit;
		$this->view->country = $countrys;
		echo Ec::renderTpl ( $this->tplDirectory . "order_create.tpl", 'layout' );
	}
	private static function getHandOrderDataSource(){
		return $defunctStatus = array(
				"resend"=>"loadOrder",
				"line"=>"loadShippingAddress",
				);
	}
	/**
	 * 进入手工订单界面（订单来源：退件重发，线下订单）
	 */
	public function jumpHandOrderAction(){
		$defunctStatus = self::getHandOrderDataSource();
		$dataSource = $this->getRequest()->getParam("dataSource","");
		/*
		 * 1.判断请求来源
		 */
			echo '<head><meta http-equiv="content-type" content="text/html; charset=utf-8"/></head>';
		if(!$defunctStatus[$dataSource]){
			echo "请求异常，请勿修改请求地址！<br/><br/>";
		}else{
// 			echo "订单来源：$dataSource<br/><br/>";
			/*
			 * 2.跳转至对应的方法进行数据查询
			 */
			$this->$defunctStatus[$dataSource]($dataSource);
			
			/*
			 * 3.查询公共数据
			 */
			//数据来源
			$con_order_data_source = array('ods_status'=>'1',"*",0,1,'ods_seq desc');
			$result_ods = Service_OrderDataSource::getByCondition($con_order_data_source);
			$this->view->orderDataSource = $result_ods;
			//仓库
			$warehouses = Service_Warehouse::getAll();
			$this->view->warehouse = $warehouses;
			//发货方式
// 			$shippingMethod =  Service_ShippingMethod::getAll();
// 			$this->view->shippingMethod = $shippingMethod;
			
			$return_warehouse_shippingMethod = Service_OrderProcess::getWarehouseAndShippingMethodInfo();
			//运输方式信息
			$this->view->warehouseShippingMethodJson = Zend_Json::encode($return_warehouse_shippingMethod['warehouseShippingMethod']);
			//仓库信息
			$this->view->warehouseArr = $return_warehouse_shippingMethod['warehouse'];
			//国家
			$countrys = Service_Country::getAll();
			$this->view->country = $countrys;
			$user_account_arr = Service_User::getPlatformUser('do');//绑定店铺账号
			if(empty($user_account_arr)){
				$con = array(
						'company_code' => Common_Company::getCompanyCode(),
						'platform' => 'ebay',
						'status' => '1',
				);
				$result = Service_PlatformUser::getByCondition($con, array(
						'user_account'
				));
				foreach($result as $rr){
					$user_account_arr[] = $rr['user_account'];
				}
			}
			$this->view->user_account_arr = $user_account_arr;
			echo Ec::renderTpl ( $this->tplDirectory . "hand_orders_create.tpl", 'layout' );
		}
	}
	
	/**
	 * 退件重发，查询订单数据
	 * http://www.ebtest.com/order/order/jump-hand-order?dataSource=rma&order_id=153018&rma_id=11
	 */
	public function loadOrder($dataSource){
		/*
		 * 1.得到订单ID，和RmaId
		 */
		$orderId = $this->getRequest()->getParam("order_id",0);
		$rmaId = $this->getRequest()->getParam("rma_id",0);
		if($orderId){
			$orderRow = Service_Orders::getByField($orderId,'order_id');
			if($orderRow){
				$con = array('order_id'=>$orderId);
				$orderProducts = Service_OrderProduct::getByCondition($con);
				$orderRow['order_product'] = $orderProducts;
				$addressRow = Service_ShippingAddress::getByField($orderRow['refrence_no_platform'],'OrderID');
					
				$con = array('order_id'=>$orderId,'give_up'=>'0');
				$order_product_rows = Service_OrderProduct::getByCondition($con);
				foreach($order_product_rows as $k=>$v){
					$product = Service_Product::getByField($v['product_id'],'product_id');
					if(!empty($product)){
						$order_product_rows[$k]['product_sku'] = $product['product_sku'];
						$order_product_rows[$k]['product_title'] = $product['product_title'];
						$order_product_rows[$k]['product_title_cn'] = $product['product_title_cn'];
					}
				}
				$rmaOrder = Service_RmaOrders::getByField($rmaId);
				$rmaBackOrder = Service_Orders::getByField($rmaOrder['rma_back_order_id'],'order_id');
				$orderRow['operator_note'] = Ec::Lang('rma_order_sys_tips') . "：$rmaBackOrder[refrence_no_platform]";//来自系统默认备注，退件订单号
// 				$orderRow['platform'] = $dataSource;
// 				$orderRow['amountpaid_title'] = '原退件订单金额：' . $orderRow['amountpaid'];
				$orderRow['amountpaid'] = '0';
				$orderRow['subtotal'] = '0';
				$orderRow['ship_fee'] = '0';
				$orderRow['platform_fee'] = '0';				
				$orderRow['order_type'] = 'resend';
				$orderRow['create_type'] = 'hand';
				$orderRow['refrence_no'] = $rmaId;
				$orderRow['create_method'] = '1';
				$this->view->handOrderTitle = Ec::Lang('resend_rma');//"退件重发";
				$this->view->order = $orderRow;
				$this->view->address = $addressRow;
				$this->view->orderProduct = $order_product_rows;
			}else{
				echo '<span style="color:red;">未能找到订单信息</span>';
				exit;
			}
		}else{
			//异常
			echo '<span style="color:red;">'.Ec::Lang('missing_parameter').'</span>';//缺少参数
			exit;
		}
	}
	
	/**
	 * paypal线下订单，查询收货人信息
	 * www.ebtest.com/order/order/jump-hand-order?dataSource=line&ptId=4545
	 */
	public function loadShippingAddress($dataSource){
// 		echo "线下订单<br/>";
		/*
		 * 1.得到paypal付款记录的订单ID，用于查询收件人信息
		 */
		$paypalTransactionId = $this->getRequest()->getParam("ptId");
// 		echo "Paypal付款记录ID：$paypalTransactionId<br/><br/>";
		if($paypalTransactionId){
			/*
			 * 1.根据paypal收款记录，查询买家信息
			*/
			$resultPaypalOrderTransaction =  Service_PaypalOrderTransaction::getByField($paypalTransactionId,'pt_id');
			$resultPyapalTransaction = Service_PaypalTransation::getByField($paypalTransactionId);
// 			print_r($resultPaypalOrderTransaction);
			if(!empty($resultPaypalOrderTransaction)){
				$orderRow = array
				(
						"platform" => 'ebay',
						"order_type" => 'line',
						"create_type" => 'hand',
						"refrence_no" => $paypalTransactionId,//
						"order_status" => "2",
						"create_method" => "1",
						"amountpaid"=>$resultPyapalTransaction['amount_total'],
						"subtotal"=>$resultPyapalTransaction['amount_total'],
						"platform_fee"=>abs($resultPyapalTransaction['fee']),
						"currency"=>$resultPyapalTransaction['currency'],
						"buyer_id"=>$resultPaypalOrderTransaction['pot_buyer_id'],
						"consignee_country"=>$resultPaypalOrderTransaction['pot_country_code'],
						"buyer_name" => $resultPaypalOrderTransaction['pot_ship_name'],// Nathan Struck
						"buyer_mail" => $resultPyapalTransaction['pay_email'],// nathanxr6@hotmail.com
						"operator_note" => Ec::Lang('paypal_order_sys_tips') . "：$resultPaypalOrderTransaction[pot_paypal_id]",//来自paypal线下订单，paypal交易ID
						"order_desc" => $resultPaypalOrderTransaction['pot_note']
				);
				$addressRow = array(
						'Name' => $resultPaypalOrderTransaction['pot_ship_name'],
						'Street1' => $resultPaypalOrderTransaction['pot_ship_street1'],
						'Street2' => $resultPaypalOrderTransaction['pot_ship_street2'],
						'CityName' => $resultPaypalOrderTransaction['pot_ship_city'],
						'StateOrProvince' => $resultPaypalOrderTransaction['pot_ship_state'],
						'Country' => $resultPaypalOrderTransaction['pot_country_code'],
						'PostalCode' => $resultPaypalOrderTransaction['pot_ship_zip'],
				);
				$this->view->order = $orderRow;
				$this->view->address = $addressRow;
				$this->view->handOrderTitle = Ec::Lang('offline_orders');//"线下订单";
			}else{
				echo '<span style="color:red;">该线下订单未能找到收货人信息</span>';
				exit;
			}
		}else{
			//异常
			echo '<span style="color:red;">'.Ec::Lang('missing_parameter').'</span>';//缺少参数
			exit;
		}
	}
	
	/**
	 * rma重发订单，创建之前的验证
	 * @param unknown_type $dataSource
	 */
	public function loadOrderValidator($dataSource){
		$return = array (
				"ask" => 0,
				"message" => "",
		);
		$refrence_no = $this->getRequest()->getParam('refrence_no','');
		$rmaOrder = Service_RmaOrders::getByField($refrence_no);
		if(empty($rmaOrder)){
			$return['message'] .= '未找到RMA信息，请查看是否删除了RMA.';
			die(Zend_Json::encode($return));
		}
		
		$orderRow = Service_Orders::getByField($refrence_no,'refrence_no');
		if(!empty($orderRow)){
			$return['message'] .= '已创建创建过类似订单,请勿重复创建,订单号：' . $orderRow['refrence_no_platform'];
			die(Zend_Json::encode($return));
		}
		//echo '111';exit;
	}
	
	/**
	 * 线下订单，创建之前的验证
	 * @param unknown_type $dataSource
	 */
	public function loadShippingAddressValidator($dataSource){
		$return = array (
				"ask" => 0,
				"message" => "",
		);
		/*
		 * 判断paypal收款纪录状态是否可以被更改,或者已经被关联
		*/
		$refrence_no = $this->getRequest()->getParam('refrence_no','');
		$paypalOrderDetail = Service_PaypalOrderTransaction::getByField($refrence_no,'pt_id');
		if(!empty($paypalOrderDetail)){
			if($paypalOrderDetail['pot_status'] == '1'){
				$return['message'] = "paypal交易ID：$paypalOrderDetail[pot_paypal_id] 已经关联订单，不能创建手工订单！";
				die(Zend_Json::encode($return));
			}
		}else{
			$return['message'] = '未查询到Pyapal付款记录信息！';
			die(Zend_Json::encode($return));
		}
	}
	/**
	 * 退件重发，创建成功后的回调方法
	 */
	public function loadOrderCallback($dataSource,$orderId){
		return $return = array (
				"ask" => 1,
				"message" => "",
		);
	}
	/**
	 * 线下订单，创建成功回调方法
	 * @param unknown_type $orderId
	 */
	public function loadShippingAddressCallback($dataSource,$orderId){
		$return = array (
				"ask" => 0,
				"message" => "",
		);
// 		$this->loadShippingAddressValidator($dataSource);
		 
		/*
		 * 更新paypal线下订单的状态，已经关联ID
		 */
		$refrence_no = $this->getRequest()->getParam('refrence_no','');
		$paypalOrderRow = array(
				'pot_status'=>'1',
				'order_id'=>$orderId
		);
		try {
			Service_PaypalOrderTransaction::update($paypalOrderRow, $refrence_no,'pt_id');
			$return['ask'] = 1;
		} catch (Exception $e) {
			Service_Orders::delete($orderId);
			$return['message'] = "线下订单关联过程中出现异常";
		}
		return $return;
	}
	/**
	 * 创建手工订单（订单来源：退件重发，线下订单）
	 */
	public function createHandOrderAction() {
		$return = array (
			"ask" => 0,
			"message" => "",
		);
		$orderId = $this->getRequest()->getParam('order_id',0);
		/*
		 * 1.调用不同订单来源的验证方法
		 */
		$defunctStatus = self::getHandOrderDataSource();
		$dataSource = $this->getRequest()->getParam('order_type','');
		if(!$defunctStatus[$dataSource]){
			$tmpMessage = "非法的请求，请勿篡改请求参数";
			$return['message'] = $tmpMessage;
			die(Zend_Json::encode($return));
		}else{
			$mentoh = $defunctStatus[$dataSource]."Validator";
			$this->$mentoh($dataSource);
		}
		if($this->getRequest()->isPost()){
			/*
			 * 2.验证是否绑定店铺
			 */
			$tmpUser_account = $this->getRequest()->getParam('user_account','');
			if(empty($tmpUser_account)){
				$tmpMessage = "手工订单，必须绑定店铺账户";
				$return['message'] = $tmpMessage;
				die(Zend_Json::encode($return));
			}
			$tmpAmountpaid = $this->getRequest()->getParam('amountpaid','');
			$tmpAmountpaid = trim($tmpAmountpaid);
			if($tmpAmountpaid == ''){
				$tmpMessage = "手工订单，必须填写订单金额";
				$return['message'] = $tmpMessage;
				die(Zend_Json::encode($return));
			}
			$order_desc = $this->getRequest()->getParam('order_desc','');
			$orderR = array(
					/***********/
					//'order_id' => $this->getRequest()->getParam('',''),
					'platform' => $this->getRequest()->getParam('platform',''),
					'data_source' => $this->getRequest()->getParam('data_source',''),
					"order_type" => $this->getRequest()->getParam('order_type',''),
					"create_type" => $this->getRequest()->getParam('create_type',''),
					'order_status' => $this->getRequest()->getParam('order_status','2'),
					'create_method' => $this->getRequest()->getParam('create_method','1'),
					'customer_id' => $this->getRequest()->getParam('customer_id',''),
					'company_code' => Common_Company::getCompanyCode(),
					'shipping_method' => $this->getRequest()->getParam('shippingMetod',''),
					'warehouse_id' => $this->getRequest()->getParam('warehouse_id','0'),
					'order_desc' => $order_desc,
					'operator_note' => $this->getRequest()->getParam('operator_note',''),
					'date_create' => date('Y-m-d H:i:s'),
					'date_create_platform' => date('Y-m-d H:i:s'),
					'date_paid_platform' => date('Y-m-d H:i:s'),
// 					'date_release' => $this->getRequest()->getParam('date_release',''),
// 					'date_warehouse_shipping' => $this->getRequest()->getParam('date_warehouse_shipping',''),
					'date_last_modify' => date('Y-m-d H:i:s'),
					'operator_id' => $this->getRequest()->getParam('operator_id',''),
					'refrence_no' => $this->getRequest()->getParam('refrence_no',''),
					'refrence_no_platform' => $this->getRequest()->getParam('refrence_no_platform',''),
					'shipping_address_id' => $this->getRequest()->getParam('shipping_address_id',''),
					'currency' => $this->getRequest()->getParam('currency',''),
					'refrence_no_warehouse' => $this->getRequest()->getParam('refrence_no_warehouse',''),
					'shipping_method_no' => $this->getRequest()->getParam('shipping_method_no',''),
					'sync_status' => $this->getRequest()->getParam('sync_status','0'),
// 					'sync_time' => date('Y-m-d H:i:s'),
					'user_account' => $this->getRequest()->getParam('user_account',''),
					'amountpaid' => $this->getRequest()->getParam('amountpaid',''),
					'subtotal'=>$this->getRequest()->getParam('subtotal',''),
					'platform_fee'=>$this->getRequest()->getParam('platform_fee',''),
					'currency' => $this->getRequest()->getParam('currency',''),
					'buyer_id' => $this->getRequest()->getParam('buyer_id',''),
					'site' => $this->getRequest()->getParam('site',''),
					'consignee_country' => $this->getRequest()->getParam('country',''),
					'buyer_name' => $this->getRequest()->getParam('buyer_name',''),
					'buyer_mail' => $this->getRequest()->getParam('buyer_mail',''),
					'has_buyer_note'=> (!empty($order_desc)?1:0),
// 					'fulfillment_channel'=> $this->getRequest()->getParam('fulfillment_channel',''),
// 					'ship_service_level'=> $this->getRequest()->getParam('ship_service_level',''),
					'shipment_service_level_category'=> $this->getRequest()->getParam('shipment_service_level_category',''),
					'leave_comment'=> $this->getRequest()->getParam('leave_comment',''),
					'ebay_case_type'=> $this->getRequest()->getParam('ebay_case_type',''),
					/***********/
			);
// 			print_r($orderR);
			$countrys = Service_Country::getAll();
			$countryCode = $this->getRequest()->getParam('country','');
			$countryName = '';
			foreach ($countrys as $k => $v) {
				if($countryCode == $v['country_code']){
					$countryName = $v['country_name_en'];
					break;
				}
			}
			$addressR = array(
					//'ShippingAddress_Id' => $this->getRequest()->getParam('',''),
					'Name' => $this->getRequest()->getParam('name',''),
					'Street1' => $this->getRequest()->getParam('address1',''),
					'Street2' => $this->getRequest()->getParam('address2',''),
					'Street3' => $this->getRequest()->getParam('address3',''),
					'CityName' => $this->getRequest()->getParam('city',''),
					'StateOrProvince' => $this->getRequest()->getParam('province',''),
					'Country' => $countryCode,
					'CountryName' => $countryName,
					'Phone' => $this->getRequest()->getParam('telphone',''),
					'PostalCode' => $this->getRequest()->getParam('zipcode',''),
					'AddressID' => $this->getRequest()->getParam('address_id',''),
					'AddressOwner' => $this->getRequest()->getParam('address_owner',''),
					'ExternalAddressID' => $this->getRequest()->getParam('external_address_id',''),
					//'OrderID' => $this->getRequest()->getParam('',''),
					'Plat_code' => $this->getRequest()->getParam('plate_code','ec'),
					'company_code' => Common_Company::getCompanyCode(),
					//'create_date_sys' =>  date('Y-m-d H:i:s'),
					'modify_date_sys' =>  date('Y-m-d H:i:s'),
					'user_account'=>$this->getRequest()->getParam('user_account',''),
					'doorplate'=>$this->getRequest()->getParam('doorplate',''),
			);
			$orderProductSku = $this->getRequest()->getParam('op_product_sku',array());
			$orderProductRs = $this->getRequest()->getParam('op_quantity',array());
			$orderProductTitle = $this->getRequest()->getParam('op_product_title',array());
			
// 			print_r($addressR);exit;
			$order_product = array();
			
			foreach($orderProductRs as $k=>$v){
				$order_product[] = array(
						'product_id'=>$k,
						'op_quantity'=>trim($v),
						'product_sku'=>$orderProductSku[$k],
						'product_title'=>$orderProductTitle[$k]);
			}
			$row = array(
					'order'=>$orderR,
					'address'=>$addressR,
					'order_product'=>$order_product,
			);
			$process = new Service_OrderProcess();
			/*
			 * 3. 调用创建订单方法
			 */
			
			$return = $process->createOrderTransaction($row);
			
			/*
			 * 4.判断创建订单方法，调用不同订单来源的回调方法callback
			 */
			if($return['ask']){
				$mentoh = $defunctStatus[$dataSource]."Callback";
				$tmpReturn = $this->$mentoh($dataSource,$return['order_id']);
				if(!$tmpReturn['ask']){
					$return = $tmpReturn;
				}
			};
			die(Zend_Json::encode($return));
		}
	}
	/**
	 * 产品列表
	 */
	public function productListAction() {
		$result = array('state'=>0,'message'=>'No Data','total'=>0);
		$page = $this->_request->getParam('page', 1);
		$pageSize = $this->_request->getParam('pageSize', 20);
		$product_sku = $this->_request->getParam('product_sku', ""); 
		
		$product_sku = preg_replace('/\s+/', ' ', $product_sku);
		$product_sku = preg_replace('/,/', ' ', $product_sku);
		$product_sku = preg_replace('/，/', ' ', $product_sku);
		$product_sku = preg_replace('/\//', ' ', $product_sku);
		$product_sku = preg_replace('/、/', ' ', $product_sku);
		$product_sku = trim($product_sku);
				
// 		if($product_sku){
// 			$product_sku = explode(' ', $product_sku);
// 		}else{
// 			$product_sku = array();
// 		}
// 		$condition["product_sku_arr"] = $product_sku;
		$page = $page ? $page : 1;
		$pageSize = $pageSize ? $pageSize : 20;
		$condition = array();
		$condition["product_sku_like"] = $product_sku;
		$count = Service_Product::getByCondition($condition, "count(*)");
		if($count){
			$list = Service_Product::getByCondition($condition, "*", $pageSize, $page);
			$result['total'] = $count;
			$result['data'] = $list;
			$result['state'] = "1";
		}
		die(Zend_Json::encode($result));
	}
	
	/**
	 * 订单状态更新
	 */
	public function updateStatusAction() {
		$orderIds = $this->_request->getParam ( 'orderId', array () );
		$status = $this->_request->getParam ( 'status' );
		$process = new Service_OrderProcess ();
		$results = $process->updateOrderStatusMultiTransaction ( $orderIds, $status );
		die ( Zend_Json::encode ( $results ) );
	}
	

	/**
	 * 标记发货
	 */
	public function batchShipFlagAction() {
	    $orderIds = $this->_request->getParam ( 'orderId', array () );
	    $mark_action = $this->_request->getParam ( 'mark_shipping_action', array () );
	    $sync_status = 0;
	    $order_status_modify = true;
	    if('mark_shipping' == $mark_action){
	    	$sync_status = 6;
	    	$order_status_modify = false;
	    }else if('mark_shipping_and_order_delivery' == $mark_action){
	    	$sync_status = 3;
	    	$order_status_modify = true;
	    }else{
	    	$results = array(
	    			'ask'=>0,
	    			'message'=>Ec::Lang('mark_shipping_error','auto')//'请选择标记发货的动作'
	    	);
	    	die ( Zend_Json::encode($results));
	    }
	    
	    $process = new Service_OrderProcess ();
	    $results = $process->batchShipOrder($orderIds,$sync_status,$order_status_modify);
	    die ( Zend_Json::encode($results));
	}
	
	/**
	 * 标记订单已在平台做过发货，系统不在进行同步
	 */
	public function batchPlatformShipedFlagAction(){
		$orderIds = $this->_request->getParam ( 'orderId', array () );
		$process = new Service_OrderProcess ();
		$results = $process->batchShipOrder($orderIds,'5',false);
		die(Zend_Json::encode($results));
	}
	
	/**
	 * 截单
	 */
	public function cancelOrderAction(){
	    set_time_limit(0);
		$orderIds = $this->_request->getParam ( 'orderIds', '');
		$status = $this->_request->getParam ( 'status', '2');
		$reasonType = $this->_request->getParam ( 'reason_type', '4');
		$reason = $this->_request->getParam ( 'reason', '');
		$orderIds = trim($orderIds,' ;');
		if(empty($orderIds)){
            $orderIds = array();
        }else{
            $orderIds = explode(';', $orderIds);
        }
//         print_r($orderIds);exit;
		$results = Service_OrderProcess::orderCancelBatchTransaction($orderIds,$status,$reasonType,$reason);
// 		print_r($results);exit;
		die ( Zend_Json::encode ( $results ) );
	}
	/**
	 * 订单产品更新
	 */
	public function updateProductAction() {
		$sku = $this->_request->getParam ( 'sku', '' );
		$opId = $this->_request->getParam ( 'op_id', '' );		
		$result = Service_OrderProcess::changeOrderProductTransaction($opId,$sku);
		die ( Zend_Json::encode ( $result ) );
	}

	/**
	 * 订单详情，编辑界面
	 */
	public function detailAction() {	    
	    
	    $orderId = $this->getRequest()->getParam('orderId','');
        
	    //启用新方法
// 	    header('Location: /order/order/detail-new/orderId/'.$orderId);
	    header('Location: /order/order/detail-new2/orderId/'.$orderId);
// 	    header('Location: /order/order/detail-new5/orderId/'.$orderId);
	    exit;
	   
	    $view = $this->getRequest()->getParam('view','');
	    $order = Service_Orders::getByField($orderId,'order_id');
	    if(empty($order)){
	        $this->_redirect('/order/order/list');
	    }else{
	        if(!empty($order['warehouse_id'])){
	            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
	            $order['warehouse_name'] = $warehouse?$warehouse['warehouse_code']:'';
	        }
	        if($order['create_method']==1){
	
	            //$this->_redirect('/order/order/create/order_id/'.$order['order_id']);exit;
	        }
	        $address = Service_ShippingAddress::getByField($order['refrence_no_platform'],'OrderID');
	
	        $orderProductRows = Service_OrderProductProcess::getOrderProductActiveForSplit($order['refrence_no_platform']);
	        sort($orderProductRows);
	        // 			print_r($order_product);exit;
	        $order['address'] = $address;
	        $order['order_product'] = $orderProductRows;
	
	        $this->view->order = $order;
	        $this->view->view = $view;
	
	        //目的国 家
	        $countryArr = Service_Country::getAll();
	        $this->view->countryArr = $countryArr;
	        // 		print_r($order);exit;
	        echo Ec::renderTpl ( $this->tplDirectory . "order_detail_new.tpl", 'layout' );
	        	
	        	
	    }
	}

	/**
	 * 订单详情，编辑界面,新的逻辑
	
	public function detailNewAction() {
	    
	    $orderId = $this->getRequest()->getParam('orderId','');
	    $view = $this->getRequest()->getParam('view','');
	    $order = Service_Orders::getByField($orderId,'order_id');
	    if(empty($order)){
	        $this->_redirect('/order/order/list');
	    }else{
	        if(!empty($order['warehouse_id'])){
	            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
	            $order['warehouse_name'] = $warehouse?$warehouse['warehouse_code']:'';
	        }
	        if($order['create_method']==1){
	
	            //$this->_redirect('/order/order/create/order_id/'.$order['order_id']);exit;
	        }
	        $address = Service_ShippingAddress::getByField($order['refrence_no_platform'],'OrderID');
	
	        $orderProductRows = Service_OrderProductProcess::getOrderProductActiveForEdit($order['refrence_no_platform']);
	        $order_product =  $orderProductRows['productArr'];
	        sort($order_product);
	        // 			print_r($order_product);exit;
	        $order['address'] = $address;
	        $order['order_product'] = $orderProductRows['productArr'];
	        $order['order_product_mult'] = $orderProductRows['mult'];
	        $order['order_product_single'] = $orderProductRows['single'];
	
	        $this->view->order = $order;
	        $this->view->view = $view;
	
	        //目的国 家
	        $countryArr = Service_Country::getAll();
	        $this->view->countryArr = $countryArr;
	        // 		print_r($order);exit;
	        echo Ec::renderTpl ( $this->tplDirectory . "order_detail_new1.tpl", 'layout' );
	
	
	    }
	}
 	*/
	/**
	 * 订单详情，编辑界面,新的逻辑
	 */
	public function detailNew2Action() {
	     
	    $orderId = $this->getRequest()->getParam('orderId','');
	    $orderCode = $this->getRequest()->getParam('orderCode','');
	    $view = $this->getRequest()->getParam('view','');
	    
	    $order = array();
	    if(!empty($orderId)){
			$order = Service_Orders::getByField($orderId,'order_id');
	    }else if(!empty($orderCode)){
	    	$order = Service_Orders::getByField($orderCode,'refrence_no_platform');
	    }
	    
	    
	    if(empty($order)){
	        $this->_redirect('/order/order/list');
	    }else{
	        if(!empty($order['warehouse_id'])){
	            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
	            $order['warehouse_name'] = $warehouse?$warehouse['warehouse_code']:'';
	        }
	        if($order['create_method']==1){
	
	            //$this->_redirect('/order/order/create/order_id/'.$order['order_id']);exit;
	        }
	        $order['platform'] = strtolower($order['platform']);
// 	        print_r($order);exit; 
	        $address = Service_ShippingAddress::getByField($order['refrence_no_platform'],'OrderID');
	
	        $orderProductRows = Service_OrderProductProcess::getOrderProductActiveForEdit($order['refrence_no_platform']);
	        $order_product =  $orderProductRows['productArr'];
	        sort($order_product);
	        // 			print_r($order_product);exit;
	        $order['address'] = $address;
	        $order['order_product'] = $orderProductRows['productArr'];
	        $order['order_product_mult'] = $orderProductRows['mult'];
	        $order['order_product_single'] = $orderProductRows['single'];
	
	        $this->view->order = $order;
	        $this->view->view = $view;
	
	        //目的国 家
	        $countryArr = Service_Country::getAll();
	        $this->view->countryArr = $countryArr;
	        // 		print_r($order);exit;
	        echo Ec::renderTpl ( $this->tplDirectory . "order_detail_new3.tpl", 'layout' );
	
	
	    }
	}

	/**
	 * 订单详情，编辑界面,新的逻辑
	 
	public function detailNew3Action() {
	
	    $orderId = $this->getRequest()->getParam('orderId','');
	    $view = $this->getRequest()->getParam('view','');
	    $order = Service_Orders::getByField($orderId,'order_id');
	    if(empty($order)){
	        $this->_redirect('/order/order/list');
	    }else{
	        if(!empty($order['warehouse_id'])){
	            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
	            $order['warehouse_name'] = $warehouse?$warehouse['warehouse_code']:'';
	        }
	        if($order['create_method']==1){
	
	            //$this->_redirect('/order/order/create/order_id/'.$order['order_id']);exit;
	        }
	        $address = Service_ShippingAddress::getByField($order['refrence_no_platform'],'OrderID');
	
	        $orderProduct = Service_OrderProductProcess::getOrderProductActiveForEditNew($order['refrence_no_platform']);
// 	        print_r($orderProduct);exit;
	        $order['address'] = $address;
	        $order['order_product'] = $orderProduct;
	
	        $this->view->order = $order;
	        $this->view->view = $view;
	
	        //目的国 家
	        $countryArr = Service_Country::getAll();
	        $this->view->countryArr = $countryArr;
	        // 		print_r($order);exit;
	        echo Ec::renderTpl ( $this->tplDirectory . "order_detail_new4.tpl", 'layout' );
	
	
	    }
	}
	*/

	/**
	 * 订单详情，编辑界面,新的逻辑
	*/
	public function detailNew5Action() {
	
	    $orderId = $this->getRequest()->getParam('orderId','');
	    $view = $this->getRequest()->getParam('view','');
	    $order = Service_Orders::getByField($orderId,'order_id');
	    if(empty($order)){
	        $this->_redirect('/order/order/list');
	    }else{
	        if(!empty($order['warehouse_id'])){
	            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
	            $order['warehouse_name'] = $warehouse?$warehouse['warehouse_code']:'';
	        }
	        if($order['create_method']==1){
	
	            //$this->_redirect('/order/order/create/order_id/'.$order['order_id']);exit;
	        }
	        $order['platform'] = strtolower($order['platform']);
	        // 	        print_r($order);exit;
	        $address = Service_ShippingAddress::getByField($order['refrence_no_platform'],'OrderID');
	
	        $order_product = Service_OrderProductProcess::getOrderProductLatest($order['refrence_no_platform']);
	       
	        sort($order_product);
// 	        			print_r($order_product);exit;
	        $order['address'] = $address;
	        $order['order_product'] = $order_product;
	
	        $this->view->order = $order;
	        $this->view->view = $view;
	
	        //目的国 家
	        $countryArr = Service_Country::getAll();
	        $this->view->countryArr = $countryArr;
	        // 		print_r($order);exit;
	        echo Ec::renderTpl ( $this->tplDirectory . "order_detail_new5.tpl", 'layout' );
	
	
	    }
	}
	/**
	 * 订单更新 新方法
	 */
	public function updateOrderDetailNewAction() {
	    $order_id = $this->_request->getParam ( 'order_id', '' );
	    $multSkus = $this->_request->getParam ( 'product_sku_mult', array () );
	    $multQtys = $this->_request->getParam ( 'op_quantity_mult', array () );
	    
	    $singleSkus = $this->_request->getParam ( 'product_sku_single', array () );
	    $singleQtys = $this->_request->getParam ( 'op_quantity_single', array () );
	    

	    $giveupMult = $this->_request->getParam ( 'give_up_mult', array () );
	    $giveupSingle = $this->_request->getParam ( 'give_up_single', array () );
	    
// 	    $warehouseSkus = $this->_request->getParam ( 'warehouse_sku', array () );
// 	    $unitePrice = $this->_request->getParam ( 'unit_price', array () );
	    
// 	    print_r($this->getRequest()->getParams());exit;
	    //是否转代发货审核
	    $to_verify = $this->_request->getParam ( 'to_verify', false );
	    
	    
	    $mult = array();
	    foreach($multSkus as $key=>$sku){
	        $mult[$key]['product_sku'] = trim($sku);
	    }

	    foreach($multQtys as $key=>$qty){
	        $mult[$key]['op_quantity'] = trim($qty);
	    }

	    foreach($giveupMult as $key=>$val){
	        $mult[$key]['give_up'] = trim($val);
	    }
	    
	    $single = array();
	    foreach($singleSkus as $key=>$sku){
	        $single[$key]['product_sku'] = trim($sku);
	    }

	    foreach($singleQtys as $key=>$qty){
	        $single[$key]['op_quantity'] = trim($qty);
	    }

	    foreach($giveupSingle as $key=>$val){
	        $single[$key]['give_up'] = trim($val);
	    }
// 	    print_r($mult);
// 	    print_r($single);
// 	    exit;
	    $productSkuAdd = $this->_request->getParam ( 'product_sku_add', array () );
	    $titleAdd = $this->_request->getParam ( 'product_title_add', array () );
	    $opQtyAdd = $this->_request->getParam ( 'op_quantity_add', array () );
	    
	     
	    $productAdd = array();
	    foreach($productSkuAdd as $key=>$sku){
	        $productAdd[$key]['product_sku'] = trim($sku);
	    }
	    
	    foreach($titleAdd as $key=>$title){
	        $productAdd[$key]['product_title'] = trim($title);
	    }
	    foreach($opQtyAdd as $key=>$qty){
	        $productAdd[$key]['op_quantity'] = trim($qty);
	    }
	    
// 	    print_r($productAdd);exit;
	    $process = new Service_OrderProcess ();
	    $countryArr = $this->_request->getParam ( 'CountryName', '|' );
	    $countryArr = explode("|", $countryArr);
	    $address = array(
	            'Country'=>$countryArr[0],
	            'CountryName'=>$countryArr[1],
	            'PostalCode'=>$this->_request->getParam ( 'PostalCode', '' ),
	            'doorplate'=>$this->_request->getParam ( 'doorplate', '' ),
	            'StateOrProvince' => $this->_request->getParam ( 'StateOrProvince', '' ),
	            'CityName' => $this->_request->getParam ( 'CityName', '' ),
	            'Street1' => $this->_request->getParam ( 'Street1', '' ),
	            'Street2' => $this->_request->getParam ( 'Street2', '' ),
	            'Name' => $this->_request->getParam ( 'Name', '' ),
	            'ShippingAddress_Id' => $this->_request->getParam ( 'ShippingAddress_Id', '' ),
	            'Email' => $this->_request->getParam ( 'Email', '' ),
	            'Phone' => $this->_request->getParam ( 'Phone', '' ),
	    );
	    
	    $results = $process->updateOrderProductTransactionNew($order_id,$mult,$single,$productAdd,$address,$to_verify);
	    // 		print_r($arr);exit;
	    die ( Zend_Json::encode ( $results ) );
	}
	

	/**
	 * 订单更新 新方法
	 */
	public function updateOrderDetailNew1Action() {
	    $order_id = $this->_request->getParam ( 'order_id', '' );
	    $multSkus = $this->_request->getParam ( 'product_sku_mult', array () );
	    $multQtys = $this->_request->getParam ( 'op_quantity_mult', array () );
	     
	    $singleSkus = $this->_request->getParam ( 'product_sku_single', array () );
	    $singleQtys = $this->_request->getParam ( 'op_quantity_single', array () );
	   
	    //是否转代发货审核
	    $to_verify = $this->_request->getParam ( 'to_verify', false );
	     
	     
	    $mult = array();
	    foreach($multSkus as $key=>$sku){
	        $mult[$key]['product_sku'] = trim($sku);
	    }
	
	    foreach($multQtys as $key=>$qty){
	        $mult[$key]['op_quantity'] = trim($qty);
	    }
	    $single = array();
	    foreach($singleSkus as $key=>$sku){
	        $single[$key]['product_sku'] = trim($sku);
	    }
	
	    foreach($singleQtys as $key=>$qty){
	        $single[$key]['op_quantity'] = trim($qty);
	    }
	    // 	    print_r($mult);
	    // 	    print_r($single);
	    // 	    exit;
	    $productSkuAdd = $this->_request->getParam ( 'product_sku_add', array () );
	    $titleAdd = $this->_request->getParam ( 'product_title_add', array () );
	    $opQtyAdd = $this->_request->getParam ( 'op_quantity_add', array () );
	     
	
	    $productAdd = array();
	    foreach($productSkuAdd as $key=>$sku){
	        $productAdd[$key]['product_sku'] = trim($sku);
	    }
	     
	    foreach($titleAdd as $key=>$title){
	        $productAdd[$key]['product_title'] = trim($title);
	    }
	    foreach($opQtyAdd as $key=>$qty){
	        $productAdd[$key]['op_quantity'] = trim($qty);
	    }
	     
	    // 	    print_r($productAdd);exit;
	    $process = new Service_OrderProcess ();
	    $countryArr = $this->_request->getParam ( 'CountryName', '|' );
	    $countryArr = explode("|", $countryArr);
	    $address = array(
	            'Country'=>$countryArr[0],
	            'CountryName'=>$countryArr[1],
	            'PostalCode'=>$this->_request->getParam ( 'PostalCode', '' ),
	            'doorplate'=>$this->_request->getParam ( 'doorplate', '' ),
	            'StateOrProvince' => $this->_request->getParam ( 'StateOrProvince', '' ),
	            'CityName' => $this->_request->getParam ( 'CityName', '' ),
	            'Street1' => $this->_request->getParam ( 'Street1', '' ),
	            'Street2' => $this->_request->getParam ( 'Street2', '' ),
	            'Name' => $this->_request->getParam ( 'Name', '' ),
	            'ShippingAddress_Id' => $this->_request->getParam ( 'ShippingAddress_Id', '' ),
	            'Email' => $this->_request->getParam ( 'Email', '' ),
	    );
	     
	    $results = $process->updateOrderProductTransactionNew($order_id,$mult,$single,$productAdd,$address,$to_verify);
	    // 		print_r($arr);exit;
	    die ( Zend_Json::encode ( $results ) );
	}

	/**
	 * 方法禁用------------------------------------------------------------------
	 * 订单更新
	 */
	public function updateOrderDetailAction() {
	    $this->forward('update-order-detail-new-all');
	}
	/**
	 * 订单更新 新方法
	 */
	public function updateOrderDetailNewAllAction() {
	    $order_id = $this->_request->getParam ( 'order_id', '' );
	    $multSkus = $this->_request->getParam ( 'product_sku_mult', array () );
	    $multQtys = $this->_request->getParam ( 'op_quantity_mult', array () );
	     
	    $singleSkus = $this->_request->getParam ( 'product_sku_single', array () );
	    $singleQtys = $this->_request->getParam ( 'op_quantity_single', array () );
	     
	    // 	    $warehouseSkus = $this->_request->getParam ( 'warehouse_sku', array () );
	    // 	    $unitePrice = $this->_request->getParam ( 'unit_price', array () );
	     
	    // 	    print_r($this->getRequest()->getParams());exit;
	    //是否转代发货审核
	    $to_verify = $this->_request->getParam ( 'to_verify', false );
	     
	     
	    $mult = array();
	    foreach($multSkus as $key=>$sku){
	        $mult[$key]['product_sku'] = trim($sku);
	    }
	
	    foreach($multQtys as $key=>$qty){
	        $mult[$key]['op_quantity'] = trim($qty);
	    }
	    $single = array();
	    foreach($singleSkus as $key=>$sku){
	        $single[$key]['product_sku'] = trim($sku);
	    }
	
	    foreach($singleQtys as $key=>$qty){
	        $single[$key]['op_quantity'] = trim($qty);
	    }
	    // 	    print_r($mult);
	    // 	    print_r($single);
	    // 	    exit;
	    $productSkuAdd = $this->_request->getParam ( 'product_sku_add', array () );
	    $titleAdd = $this->_request->getParam ( 'product_title_add', array () );
	    $opQtyAdd = $this->_request->getParam ( 'op_quantity_add', array () );
	     
	
	    $productAdd = array();
	    foreach($productSkuAdd as $key=>$sku){
	        $productAdd[$key]['product_sku'] = trim($sku);
	    }
	     
	    foreach($titleAdd as $key=>$title){
	        $productAdd[$key]['product_title'] = trim($title);
	    }
	    foreach($opQtyAdd as $key=>$qty){
	        $productAdd[$key]['op_quantity'] = trim($qty);
	    }
	     
	    // 	    print_r($productAdd);exit;
	    $process = new Service_OrderProcess ();
	    $countryArr = $this->_request->getParam ( 'CountryName', '|' );
	    $countryArr = explode("|", $countryArr);
	    $address = array(
	            'Country'=>$countryArr[0],
	            'CountryName'=>$countryArr[1],
	            'PostalCode'=>$this->_request->getParam ( 'PostalCode', '' ),
	            'StateOrProvince' => $this->_request->getParam ( 'StateOrProvince', '' ),
	            'CityName' => $this->_request->getParam ( 'CityName', '' ),
	            'Street1' => $this->_request->getParam ( 'Street1', '' ),
	            'Street2' => $this->_request->getParam ( 'Street2', '' ),
	            'Name' => $this->_request->getParam ( 'Name', '' ),
	            'ShippingAddress_Id' => $this->_request->getParam ( 'ShippingAddress_Id', '' ),
	            'Email' => $this->_request->getParam ( 'Email', '' ),
	    );
	     
	    $results = $process->updateOrderProductTransactionNew($order_id,$mult,$single,$productAdd,$address,$to_verify);
	    // 		print_r($arr);exit;
	    die ( Zend_Json::encode ( $results ) );
	}
	
	/**
	 * 订单更新地址 新方法
	 */
	public function updateOrderDetailAddressAction() {
	    $order_id = $this->_request->getParam ( 'order_id', '' );
	     
	    // 	    print_r($productAdd);exit;
	    $process = new Service_OrderProcess ();
	    $countryArr = $this->_request->getParam ( 'CountryName', '|' );
	    //是否转代发货审核
	    $to_verify = $this->_request->getParam ( 'to_verify', false );
	    $countryArr = explode("|", $countryArr);
	    $address = array(
	            'Country'=>$countryArr[0],
	            'CountryName'=>$countryArr[1],
	            'PostalCode'=>$this->_request->getParam ( 'PostalCode', '' ),
	            'StateOrProvince' => $this->_request->getParam ( 'StateOrProvince', '' ),
	            'CityName' => $this->_request->getParam ( 'CityName', '' ),
	            'Street1' => $this->_request->getParam ( 'Street1', '' ),
	            'Street2' => $this->_request->getParam ( 'Street2', '' ),
	            'Name' => $this->_request->getParam ( 'Name', '' ),
	            'ShippingAddress_Id' => $this->_request->getParam ( 'ShippingAddress_Id', '' ),
	            'Email' => $this->_request->getParam ( 'Email', '' ),
	    );
	
	    $results = $process->updateOrderProductTransactionNew($order_id,array(),array(),array(),$address,$to_verify);
	    // 		print_r($arr);exit;
	    die ( Zend_Json::encode ( $results ) );
	}
	
	/**
	 * 订单更新产品 新方法
	 */
	public function updateOrderDetailProductAction() {
	    $order_id = $this->_request->getParam ( 'order_id', '' );
	    $multSkus = $this->_request->getParam ( 'product_sku_mult', array () );
	    $multQtys = $this->_request->getParam ( 'op_quantity_mult', array () );
	
	    $singleSkus = $this->_request->getParam ( 'product_sku_single', array () );
	    $singleQtys = $this->_request->getParam ( 'op_quantity_single', array () );
	
	    // 	    $warehouseSkus = $this->_request->getParam ( 'warehouse_sku', array () );
	    // 	    $unitePrice = $this->_request->getParam ( 'unit_price', array () );
	
	    // 	    print_r($this->getRequest()->getParams());exit;
	    //是否转代发货审核
	    $to_verify = $this->_request->getParam ( 'to_verify', false );
	
	
	    $mult = array();
	    foreach($multSkus as $key=>$sku){
	        $mult[$key]['product_sku'] = trim($sku);
	    }
	
	    foreach($multQtys as $key=>$qty){
	        $mult[$key]['op_quantity'] = trim($qty);
	    }
	    $single = array();
	    foreach($singleSkus as $key=>$sku){
	        $single[$key]['product_sku'] = trim($sku);
	    }
	
	    foreach($singleQtys as $key=>$qty){
	        $single[$key]['op_quantity'] = trim($qty);
	    }
	    
	    $productSkuAdd = $this->_request->getParam ( 'product_sku_add', array () );
	    $titleAdd = $this->_request->getParam ( 'product_title_add', array () );
	    $opQtyAdd = $this->_request->getParam ( 'op_quantity_add', array () );
	
	
	    $productAdd = array();
	    foreach($productSkuAdd as $key=>$sku){
	        $productAdd[$key]['product_sku'] = trim($sku);
	    }
	
	    foreach($titleAdd as $key=>$title){
	        $productAdd[$key]['product_title'] = trim($title);
	    }
	    foreach($opQtyAdd as $key=>$qty){
	        $productAdd[$key]['op_quantity'] = trim($qty);
	    }
	
	    // 	    print_r($productAdd);exit;
	    $process = new Service_OrderProcess ();
	
	    $results = $process->updateOrderProductTransaction($order_id,$mult,$single,$productAdd,array(),$to_verify);
	    // 		print_r($arr);exit;
	    die ( Zend_Json::encode ( $results ) );
	}
	
	
	/**
	 * 订单状态更新
	 */
	public function userDefinedTagAction() {
		
		if($this->getRequest()->isPost()){
		    set_time_limit(0);
			$return = array('ask'=>0,'message'=>'');
			$tagInput = $this->_request->getParam('tag_input','');
			$tagSelectVal = $this->_request->getParam('tag_select_val','');
			$orderStatus = $this->_request->getParam('order_status','');
			$order_ids = $this->_request->getParam('orderId',array());
			if(empty($tagInput) && $tagSelectVal == ''){
				$return['message'] = '请先选择或新增一个标记名称';
				die(Zend_Json::encode($return));
			}
			if(empty($order_ids)){
				$return['message'] = '请选择订单';
				die(Zend_Json::encode($return));
			}

			if(empty($orderStatus)){
			    $return['message'] = '参数错误，未选定主状态';
			    die(Zend_Json::encode($return));
			}
//             $con = array(
//                 'company_code' => Common_Company::getCompanyCode(),
//                 'tag_name' => trim($tagInput),
//                 'order_status'=>$orderStatus
//             );
// 			$configRow = Service_OrderTag::getByCondition($con);
			$otId = '';
			if($tagSelectVal != ''){
				if($tagSelectVal == '0'){
					$otId = $tagSelectVal;
					$tagInput = Ec::Lang('custom_tags_default');
				}else{
					$lang = Ec::getLang(1);
					$otId = $tagSelectVal;
					$result_order_tag = Service_OrderTag::getByField($otId,'ot_id');
					if($result_order_tag['status']){
						$tagInput = $result_order_tag['tag_name' . $lang];
					}else{
						$return['message'] = '该标记已被删除，请重新操作.';
						die(Zend_Json::encode($return));
					}
				}
				
			}else{
// 				$result = $this->serviceClass->add($row);
				
				$row = array (
						'company_code' => Common_Company::getCompanyCode (),
						'tag_name' => trim ( $tagInput ),
						'tag_name_en'=> trim ( $tagInput ),
						'create_id'=>Service_User::getUserId(),
						'modify_id'=>Service_User::getUserId(),
						'create_time' => date ( 'Y-m-d H:i:s' ) ,
						'last_update_time'=> date ( 'Y-m-d H:i:s' ) ,
                        'order_status'=>$orderStatus
				);
				$otId = Service_OrderTag::add ( $row );
			}			

			$process = new Service_OrderProcess ();
			$results = array();
			foreach($order_ids as $order_id){			   
			    $order = Service_Orders::getByField($order_id,'order_id');
			    $updateRow= array('ot_id'=>$otId);
			    Service_Orders::update($updateRow, $order_id,'order_id');
        		$logRow = array(
                    'ref_id' => $order['refrence_no_platform'],
                    'log_content' => ' 订单自定义标记为:'.$tagInput . '['.$otId.']',
                    'op_id' => ''
                );
			    Service_OrderLog::add($logRow);
			    $result = array('ask'=>1,'message'=>'订单自定义标记  ['.$tagInput.'] 成功','ref_id'=>$order['refrence_no_platform']);
			    $results[] = $result;
			}
			$return['result'] = $results;
			$return['ask'] = 1;
			die ( Zend_Json::encode ( $return ) );
		}		
	}
	
	/**
	 * 获得自定义标签
	 */
	public function getUserDefinedTagAction(){
		$order_status = $this->_request->getParam('order_status','');
		$con = array(
				'company_code' => Common_Company::getCompanyCode(),
				'order_status'=>$order_status,
				'status'=>1
		);
		$lang = Ec::getLang(1);
		if($order_status){
			$configRow = Service_OrderTag::getByCondition($con);
			$userTags = array();
			if(!empty($configRow)){
				$userTags[] = array('k'=>0,'ot_id'=>0,'text'=>Ec::Lang('custom_tags_default'));
			}
			
			foreach($configRow as $k=>$v){
				$userTags[] = array(
						'k'=> $v['ot_id'],
						'ot_id'=> $v['ot_id'],
						'text'=> $v['tag_name' . $lang],
						);
			}
			die ( Zend_Json::encode ( $userTags ) );
		}else{
			$configRow = Service_OrderTag::getByCondition($con);
			$configRowArr = array();
			foreach($configRow as $k=>$v){
				$v['k'] = $v['ot_id'];
				$v['text'] = $v['tag_name'];
				$configRowArr[$v['order_status']][] = $v;
			}
			die ( Zend_Json::encode ( $configRowArr ) );
		}
	}

	/**
	 * 删除自定义标签
	 */
	public function deleteDefinedTagAction() {
		if ($this->getRequest ()->isPost ()) {
			$otId = $this->_request->getParam ( 'ot_id', '0' );			
			$process = new Service_OrderProcess ();
			$return = $process->deleteDefinedTagTransaction ( $otId );
			die ( Zend_Json::encode ( $return ) );
		}
	}

	/**
	 * 获取自定义标签
	 */
	public function getDefinedTagAction() {
	    $con = array(
            'company_code' => Common_Company::getCompanyCode(),
            'order_status'=>$this->_request->getParam('order_status',''),
        );
		$configRow = Service_OrderTag::getByCondition($con);
		$configRowArr = array();
		foreach($configRow as $k=>$v){
			$v['k'] = $v['ot_id'];
			$v['text'] = $v['tag_name'];
			$configRowArr[$v['order_status']][] = $v;
		}
		
		die ( Zend_Json::encode ( $configRowArr ) );
	}
	/**
	 * 确认订单出库
	 */
	public function uploadToConfirmOrderDispatchAction() {
		$return = array (
				'ask' => 0,
				'message' => 'Request Method Err' 
		);
		if ($this->getRequest ()->isPost ()) {
			$tpl_id = $this->_request->getParam('tpl_id','0');
			$status = $this->_request->getParam('status','');
			$process = new Service_OrderProcess ();
			$return = $process->uploadToConfirmOrderDispatch ( $_FILES ['fileToUpload'],$tpl_id ,$status);
		}
		die ( Zend_Json::encode ( $return ) );
	}

	/**
	 * 上传确认模板
	 * @throws Exception
	 */
	public function uploadConfirmOrderFileAction() {
        $return = array(
            'ask' => 0,
            'message' => 'Request Method Err'
        );
        if($this->getRequest()->isPost()){
            $file = $_FILES['fileToUpload'];
            try{
                if($file['error']){
                    throw new Exception('请选择xls文件');
                }
                $fileName = $file['name'];
                $filePath = $file['tmp_name'];
                $pathinfo = pathinfo($fileName);
                if(isset($pathinfo["extension"]) && $pathinfo["extension"] == "xls"){
                    $fileData = Service_ProductTemplate::readUploadFile($fileName, $filePath);
                    if(empty($fileData)){
                        throw new Exception('文件中必须包含有内容');
                    }
                    $return = array(
                        'ask' => 1,
                        'data' => $fileData
                    );
                }else{
                    throw new Exception('文件格式不正确，请选择xls文件');
                }
            }catch(Exception $e){
                $return = array(
                    'ask' => 0,
                    'message' => $e->getMessage()
                );
            }
        }
        die(Zend_Json::encode($return));
    }
	/**
	 * 获取邮件模板
	 */
	public function getExportTemplateAction() {
		$return = array (
				'ask' => 0,
				'message' => 'Request Method Err'
		);
		$company_code = Common_Company::getCompanyCode();//测试使用
		if ($this->getRequest ()->isPost ()) {
			$type = $this->getRequest()->getParam('type','1');
			switch($type){
				case 1:
					$app_code = 'order_output';
					break;
				default:
					$app_code = 'order_input';
			}
			$con = array('defined_type'=>$type,'app_code'=>$app_code,'company_code_arr'=>array($company_code));
			$rows = Service_ExcelDefined::getByCondition($con);
			if(empty($rows)){
				$return['message'] = '还未导入模板';
			}else{
				$return['ask'] = 1;
				$return['result'] = $rows;
			}
		}
		die ( Zend_Json::encode ( $return ) );
	}
	
    /**
     * 为订单回复信息
     */
	public function feedBackMessageForOrderAction() {
	if ($this->getRequest ()->isPost ()) {
			
			$orderIds = $this->_request->getParam ( 'orderIds', '' );
			$platform = $this->_request->getParam ( 'message_platform', '' );
			
			$orderIds = trim($orderIds,",");
			$orderIds = trim($orderIds,";");
			$orderIds = explode(';', $orderIds);
			// 			print_r($messageIds);exit;
			$itemID = $this->_request->getParam ( 'item', '' );
			$subject = $this->_request->getParam ( 'subject', '' );
			$content = $this->_request->getParam ( 'content', '' );
			$language = $this->_request->getParam ( 'language', 'zh' );
			
			if($platform == 'b2c'){
				//b2c 发送邮件
				$return = Service_OrderProcess::sendMailForOrders($orderIds, $subject, $content,$language);
			}else if($platform == 'ebay'){
				//ebay站内信发送
				$return = Service_EbayMessageProcess::saveEbayFeedbackMessageTransaction ($orderIds, $subject, $content,$language );
			}else{
				 $return = array (
			            "ask" => 0,
			            "message" => "该订单类型，不能发送消息或者邮件"
	   				);
			}
			
			die ( Zend_Json::encode ( $return ) );
		}
	}
	/**
	 * 订单详情
	 */
	public function getOrdersItemAction(){
		if ($this->getRequest ()->isPost ()) {
			$orderIds = $this->_request->getParam ( 'orderId', array () );
			$con = array (
					'order_id_arr' => $orderIds 
			);
			$return = array (
					'ask' => 0,
					'message' => '请选择订单号' 
			);
			if (! empty ( $orderIds )) {
				$orderProducts = Service_OrderProduct::getByCondition ( $con );
				if (empty ( $orderProducts )) {
					$return ['message'] = '参数错误，数据库中不存在数据';
				} else {
					$result = array ();
					foreach ( $orderProducts as $k => $v ) {
						$result [$v ['op_ref_item_id']] = $v;
					}
					$return ['ask'] = 1;
					$return ['data'] = $result;
				}
			}
			
			die ( Zend_Json::encode ( $return ) );
		}
	}
    /**
     * 获取仓库运输方式
     */
	public function getWarehouseShippingAction(){
		if ($this->getRequest ()->isPost ()) {			
			$return = array (
					'ask' => 0,
					'message' => '请选择订单号'
			);
			$con = array('warehouse_status'=>'1');
			$warehouse = Service_Warehouse::getByCondition($con);
			$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
			$db = Common_Common::getAdapter(); 
			$warehouseArr = array();
			foreach($warehouse as $k=>$w){
				$shipping = array();
// 				$shipping[] = array('code'=>'DHL');
// 				$shipping[] = array('code'=>'UKCL');
				$conn = array('warehouse_id'=>$w['warehouse_id'],'sm_status'=>'1');
				$sql = 'select distinct b.* from '.$wms_db.'.shipping_method_settings a inner join '.$wms_db.'.shipping_method b on a.sm_id=b.sm_id where a.warehouse_id='.$w['warehouse_id'].' and b.sm_status=1';
//                 echo $sql;exit;
// 				$shippings = Service_ShippingMethod::getByCondition($conn,'*');
				$shippings = $db->fetchAll($sql);
				
				foreach($shippings as $s){
				    $s['sm_carrier_number'] = empty($s['sm_carrier_number'])?'第三方运输代码未设置':$s['sm_carrier_number'];
				    $shipping[] = array('code'=>$s['sm_code'],'sm_name_cn'=>$s['sm_name_cn'],'sm_carrier_number'=>$s['sm_carrier_number']);
				}
				$w['shipping'] = $shipping;
			    $warehouseArr[$w['warehouse_id']] = $w;
			}
// 			print_r($warehouseArr);exit;
			die ( Zend_Json::encode ( $warehouseArr ) );
		}
	}
	public function verifyLog($orderIds){
	    $refIds = array();
	    foreach($orderIds as $orderId){
	        $order = Service_Orders::getByField($orderId,'order_id');
	        $refIds[] = $order['refrence_no_platform'];
	    }
	    $user = Service_User::getLoginUser();
	    Ec::showError('订单审核,审核人：'.$user['user_name'].', 审核数量：'.count($orderIds)."\n订单号:".print_r($refIds,true),'verify_log_');
	}
	/**
	 * 订单审核,手工分仓
	 */
	public function orderVerifyAction(){
	    if ($this->getRequest ()->isPost ()) {
	        set_time_limit(0);
            $param = $this->_request->getParams();
            $orderIds = $this->_request->getParam('orderId', array());
            $warehouseId = $this->_request->getParam('warehouse_id', '');
            $shippingMethod = $this->_request->getParam('shipping_method', '');
            $warehouseVerifyShippingMethod = $this->_request->getParam('warehouse_verify_shipping_method', '0');
            $warehouseVerifyShippingMethod = $warehouseVerifyShippingMethod != '1' ? 0 : 1;
            $process = new Service_OrderProcess();
            $this->verifyLog($orderIds);
            $return = $process->orderVerifyBatchTransaction($orderIds, $warehouseId, $shippingMethod,2,$warehouseVerifyShippingMethod);
//             print_r($return);exit;
            die(json_encode($return));
        }
	}

	/**
	 * 订单审核,自动分仓
	 */
	public function orderVerifyNewAction(){
	    if ($this->getRequest ()->isPost ()) {
	        set_time_limit(0);
	        $param = $this->_request->getParams();
	        $orderIds = $this->_request->getParam('orderId', array());
            $warehouseVerifyShippingMethod = $this->_request->getParam('warehouse_verify_shipping_method', '0');
            $warehouseVerifyShippingMethod = $warehouseVerifyShippingMethod != '1' ? 0 : 1;
	        $process = new Service_OrderProcess();
            $this->verifyLog($orderIds);
	        $return = $process->orderVerifyBatchNewTransaction($orderIds,2,$warehouseVerifyShippingMethod);
	        //             print_r($return);exit;
	        die(json_encode($return));
	    }
	}
	/**
	 * 手动分仓
	 */
	public function orderSetWarehouseShipAction(){
	    if ($this->getRequest ()->isPost ()) {
	        set_time_limit(0);
	        $param = $this->_request->getParams();
	        $orderIds = $this->_request->getParam('orderId', array());
	        $warehouseId = $this->_request->getParam('warehouse_id', '');
	        $shippingMethod = $this->_request->getParam('shipping_method', '');
	        $process = new Service_OrderProcess();
	        $return = $process->orderSetWarehouseShipBatchTransaction($orderIds, $warehouseId, $shippingMethod); 	        
	        die(json_encode($return));
	    }
	}

	/**
	 * 自动分仓
	 */
	public function orderSetWarehouseShipAutoAction(){
	    if ($this->getRequest ()->isPost ()) {
	        set_time_limit(0);
	        $refIds = $this->_request->getParam('ref_id',array());
	        $param = $this->_request->getParams();
            //自动审单-------------------------------------------------
            Zend_Registry::set('auto_allot',true);
// 	        $process = new Service_OrderProcess();
// 	        $return = $process->orderSetWarehouseShipAutoTransaction();
	        $return = Service_OrderProcess::orderAllotTransaction($refIds);
	        die(json_encode($return));
	    }
	}
	/**
	 * 保存分配规则
	 * @throws Exception
	 */
	public function setAllotConditionAction(){
        /**
         * $OrderID = $this->_request->getParam('code','');
         * $OrderID = preg_replace('/\s+/', ' ', $OrderID);
         * $OrderID = preg_replace('/,/', ' ', $OrderID);
         * $OrderID = preg_replace('/，/', ' ', $OrderID);
         * $OrderID = preg_replace('/\//', ' ', $OrderID);
         * $OrderID = preg_replace('/、/', ' ', $OrderID);
         * $OrderID = trim($OrderID);
         *
         * if($OrderID){
         * $OrderID = explode(' ', $OrderID);
         * }else{
         * $OrderID = array();
         * }
         * $statusMap = array(
         * '0' => '0',
         * '1' => '1',
         * '2' => '2',
         * '3' => '3',
         * '4' => '4',
         * '5' => '5' ,
         * );
         *
         * $status = $this->_request->getParam('status','');
         *
         * $type = $this->_request->getParam('type','');
         * switch($type){
         * case "3":
         * $condition = array(
         * 'item_id_arr' => $OrderID,
         * 'order_status' => array_key_exists($status, $statusMap) ?
         * $statusMap[$status] : $status
         * );
         * break;
         * default:
         * $condition = array(
         * 'refrence_no_platform_arr' => $OrderID,
         * 'order_status' => array_key_exists($status, $statusMap) ?
         * $statusMap[$status] : $status
         * );
         * }
         * $isMore = $this->_request->getParam('is_more', '');
         * if(!empty($isMore)){
         * $condition['op_ref_buyer_id'] = $this->_request->getParam('buyer_id',
         * '');
         * $condition['site'] = $this->_request->getParam('site', '');
         * $condition['shipping_method_platform'] =
         * $this->_request->getParam('shipping_method_platform', '');
         *
         * $condition['priceFrom'] = $this->_request->getParam('priceFrom', '');
         * $condition['priceEnd'] = $this->_request->getParam('priceEnd', '');
         * $condition['createDateFrom'] =
         * $this->_request->getParam('createDateFrom', '');
         * $condition['createDateEnd'] =
         * $this->_request->getParam('createDateEnd', '');
         * $condition['payDateFrom'] = $this->_request->getParam('payDateFrom',
         * '');
         * $condition['payDateEnd'] = $this->_request->getParam('payDateEnd',
         * '');
         * }
         *
         * foreach($condition as $k=>$v){//去除条件中的空格
         * if(is_string($v)){
         * $condition[$k] = trim($v);
         * }
         * }
         */
        $retrun = array(
            'ask' => 0,
            'message' => '添加失败'
        );
        try{
            $params = $this->_request->getParams();
            // 去除不需要保留项
            unset($params['module']);
            unset($params['controller']);
            unset($params['action']);
            unset($params['warehouse_id']);
            unset($params['shipping_method']);
            unset($params['allot_name']);
//             print_r($params);
//             exit();
            
            if(empty($params['country']) && empty($params['site']) && empty($params['shipping_method_platform'])){
                throw new Exception('国家或者站点或者运输方式必须选一个');
            }
            $oac_name = $this->_request->getParam('allot_name', '');
            if(empty($oac_name)){
                throw new Exception('请输入保存名称');
            }
            $warehouseId = $this->_request->getParam('warehouse_id', '');
            if(empty($warehouseId)){
                throw new Exception('请选择分配仓库');
            }
            $shippingMethod = $this->_request->getParam('shipping_method', '');
            if(empty($shippingMethod)){
                throw new Exception('请选择分配运输方式');
            }
            $params = serialize($params);
            
            $row = array(
                'oac_name' => trim($oac_name),
                'oac_condition' => $params,
                'company_code' => Common_Company::getCompanyCode(),
                'warehouse_id' => $warehouseId,
                'shipping_method' => $shippingMethod,
                'oac_is_auto' => $this->_request->getParam('is_auto', '0')
            );
            $con = array(
                'oac_name' => trim($oac_name),
                'company_code' => Common_Company::getCompanyCode(),
            );
            $existsRow = Service_OrderAllotCondition::getByCondition($con);
            if($existsRow){
                throw new Exception('该名称已经存在');
            }
            if(Service_OrderAllotCondition::add($row)){
                $retrun = array(
                    'ask' => 1,
                    'message' => '添加成功'
                );
            }
        }catch(Exception $e){
            $retrun = array(
                'ask' => 0,
                'message' => $e->getMessage()
            );
        }
        die(Zend_Json::encode($retrun));
    }
   
    /**
     * 获取分配规则
     */
   public function getAllotConditionAction(){
      $con = array(
         'company_code' => Common_Company::getCompanyCode()
      );
      $result = Service_OrderAllotCondition::getByCondition($con);     
      foreach($result as $k=>$v){
         $result[$k]['oac_condition'] = unserialize($v['oac_condition']);
         $warehouse = Service_Warehouse::getByField($v['warehouse_id'],'warehouse_id');
         $result[$k]['warehouse_code'] = empty($warehouse)?'':$warehouse['warehouse_code'];
      }
      die(Zend_Json::encode($result));
   }

   /**
    *  删除分配规则
    */
   public function deleteAllotConditionAction(){
      $oacId = $this->_request->getParam('oac_id', '0');
      $retrun = array(
         'ask' => 0,
         'message' => '删除失败'
      );
      if(Service_OrderAllotCondition::delete($oacId, 'oac_id')){
         $retrun = array(
            'ask' => 1,
            'message' => '删除成功'
         );
      }
      die(Zend_Json::encode($retrun));
   }
   

   /**
    * item相关订单
    */
   public function itemHistoryOrderListAction(){
        $buyer_id = $this->_request->getParam('buyer_id', '');
        
        if($this->getRequest()->isPost()){
            set_time_limit(0);
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            
            $condition = array(
                'company_code' => Common_Company::getCompanyCode(),
                'buyer_id' => $buyer_id
            );
//             print_r($condition);exit;
            $count = Service_Orders::getByCondition($condition, 'count(*)');
            $return['total'] = $count;
            
            if($count){
            	$status_arr = Service_OrderProcess::getStatusArr('ebay');
                $rows = Service_Orders::getByCondition($condition, "*", $pageSize, $page, 'order_id desc');
                // print_r($rows);exit;
                foreach($rows as $k => $v){
                	$rows[$k]['OrderStatus'] = $status_arr[$v['order_status']]['name'];
                    if($v['create_method'] == 2){
                        if(strtoupper($v['platform']) == 'EBAY'){
                            $originalOrder = Service_EbayOrderOriginal::getByField($v['refrence_no_platform'], 'OrderID');
                            
                            if(is_array($originalOrder)){
                                $rows[$k] = array_merge($originalOrder, $v);
                            }
                            $rows[$k]['org'] = $originalOrder;
                            $con = array(
                                'order_id' => $v['order_id'],
                                'give_up_arr'=>array('0','1'),
                            );
                            $orderProducts = Service_OrderProduct::getByCondition($con);
                            foreach($orderProducts as $key => $val){
                                $conn = array(
                                    'TransactionID' => $val['op_ref_tnx'],
                                    'OrderId' => $v['refrence_no_platform'],
                                    'ItemID' => $val['op_ref_item_id']
                                );
                                $transaction = Service_EbayOrderTransaction::getByCondition($conn);
                                
                                if(isset($transaction[0])){
                                    $val = array_merge($val, $transaction[0]);
                                    $val['product_sku'] = empty($val['product_sku']) ? $val['sku'] : $val['product_sku'];
                                }
                                $orderProducts[$key] = $val;
                            }
                            $rows[$k]['order_product'] = $orderProducts;
                            $address = Service_ShippingAddress::getByField($v['refrence_no_platform'], 'OrderID');
                            // print_r($address);exit;
                            $rows[$k]['address'] = $address;
                        }
                    }else{
                        $address = Service_ShippingAddress::getByField($v['order_id'], 'OrderID');
                        // print_r($address);exit;
                        $rows[$k]['address'] = $address;
                        $con = array(
                            'order_id' => $v['order_id'],
                            'give_up_arr'=>array('0','1'),
                        );
                        $orderProducts = Service_OrderProduct::getByCondition($con);
                        $rows[$k]['order_product'] = $orderProducts;
                    }
                    $rows[$k]['order_product_count'] = count($orderProducts);                    
                    if(! empty($v['warehouse_id'])){
                        $warehouse = Service_Warehouse::getByField($v['warehouse_id'], 'warehouse_id');
                        if($warehouse){
                            $rows[$k]['warehouse'] = $warehouse;
                        }
                    }
                }
//                 print_r($rows);exit;
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
//             print_r($rows);exit;
            die(Zend_Json::encode($return));
        }
        $this->view->buyer_id = $buyer_id;
        echo Ec::renderTpl($this->tplDirectory . "item_history_order.tpl", 'layout');
    }

    /**
     * 订单合并
     */
    public function orderMergeAction(){
        if($this->getRequest()->isPost()){
            $orderIds = $this->getRequest()->getParam('orderId', array());
            //选择的平台发货方式
            $shipping_method_platform = $this->getRequest()->getParam('shipping_method_platform', '');
            $process = new Service_OrderProcess();
            $return = $process->orderMergeTransaction($orderIds,$shipping_method_platform);
            die(Zend_Json::encode($return));
        }
    }
    
    /**
     * 订单取消合并
     */
    public function orderMergeReverseAction(){
        if($this->getRequest()->isPost()){
            $return = array('ask'=>0,'message'=>'该操作禁止');
            /* 
            $orderId = $this->getRequest()->getParam('orderId', '');
            $process = new Service_OrderProcess();
            $return = $process->ordermergeReverseTransaction($orderId);
             */
            die(Zend_Json::encode($return));
        }
    }
    
    /**
     * 批量合并订单
     */
    public function batchOrderMergeAction(){
    	if($this->getRequest()->isPost()){
    	    
    		$orderIds = $this->getRequest()->getParam('orderId', array());
    		$con = array(
    				'order_id_arr'=>$orderIds
    				);
    		$result = Service_Orders::getByCondition($con);
    		
    		$mergeOrders = array();
    		$mergeOrders_spare = array();
    		foreach ($result as $orderK => $orderV) {
    			$order_id = $orderV['order_id'];
    			$buyer_id = $orderV['buyer_id'];
    			$name = $orderV['Name'];
    			$consignee_country = $orderV['consignee_country'];
    			$street1 = trim($orderV['Street1']);
    			$street2 = trim($orderV['Street2']);
    			$key = $buyer_id . '_' . $name  . '_' . $consignee_country . '_' . $street1  . '_' . $street2;
//     			if(isset($mergeOrders[$key])){
    				$mergeOrders[$key][] = $order_id;
    				$mergeOrders_spare[$order_id] = $orderV;
//     			}
    		}
    		
    		$process = new Service_OrderProcess();
    		$returns = array(
    				'ask' => 0,
    				'message' => array(),
    				'error_message' => array()
    		);
    		
    		foreach ($mergeOrders as $ordersIdK => $ordersIdV) {
    			if(count($ordersIdV) > 1){
    				try{
		    			$return = $process->orderMergeTransaction($ordersIdV);
		    			if($return['ask']){
		    				$returns['message'][] = implode(',', $return['ref_id_arr']).$return['message'];
		    			}else{
		    			    $returns['message'][] = implode(',', $return['ref_id_arr']).$return['message'];
		    			}
    				}catch (Exception $e){
    					$returns['error_message'][] = $e->getMessage();
    				}
    			}else{
    				$returns['error_message'][] = "订单：" . $mergeOrders_spare[$ordersIdV[0]]['refrence_no_platform'] . "  没有数据匹配的订单进行合并.";
    			}
    		}
    		$returns['ask'] = 1;
    		die(Zend_Json::encode($returns));
    	}
    }

    /**
     * 订单导入
     */
    public function importAction(){
        $con = array(
                'company_code' => Common_Company::getCompanyCode(),
              //  'platform' => 'ebay',
                'status' => '1',
        );
        $accounts = Service_PlatformUser::getByCondition($con, array(
                'user_account','platform','platform_user_name'
        ));
        $accountArr=array();
        foreach($accounts as $row){
//             $row['platform'] = $row['platform'] == 'ebay' ? 'b2c' : $row['platform'];
        	$tmp = in_array($row['platform'], array('ebay','amazon','aliexpress'))?$row['platform']:'b2c';
            $accountArr[$tmp][] = array(
	            		'user_account'=>$row['user_account'],
	            		'platform_user_name'=>$row['platform_user_name'],
            		);
            
        }
//         print_r($accountArr);exit;
        unset($accounts);
        if($this->getRequest()->isPost()){
            $return = array(
                'ask' => 0,
                'message' => 'Request Method Err'
            );
            $file = $_FILES['fileToUpload'];
            $tpl_id = $this->getRequest()->getParam('tpl_id', '1');
            $user_account = $this->getRequest()->getParam('account', '');
            $order_data_source = $this->getRequest()->getParam('order_data_source', '');
            $platform = $this->getRequest()->getParam('platform', 'ebay');
            
            $process = new Service_OrderProcess();
            $return = $process->importTransaction($file,$tpl_id,$user_account,$platform,$order_data_source);
            die(Zend_Json::encode($return));
        }
        $con_order_data_source = array('ods_status'=>'1',"*",0,1,'ods_seq desc');
        $result_ods = Service_OrderDataSource::getByCondition($con_order_data_source);
        
        $this->view->orderDataSource = $result_ods;
        $con = array('company_code'=>'xxxxxx');
        $excelDefinedRows = Service_ExcelDefined::getByCondition($con);
//         print_r($excelDefinedRows);exit;
		$this->view->tplList = $excelDefinedRows;
		$this->view->accounts = Zend_Json::encode($accountArr);;
		$this->view->warehouse = Service_Warehouse::getByCondition(array(),array('warehouse_code','warehouse_desc','warehouse_id'));
		
		$con = array('is_show'=>1);
		$platform = Service_Platform::getByCondition($con);
		$platform_arr = array();
		foreach($platform as $k=>$v){
			$v['platform'] = trim($v['platform']);
			$platform_arr[$v['platform']] = $v;
		}
		$this->view->platform = $platform_arr;
        echo Ec::renderTpl($this->tplDirectory . "order_import.tpl", 'layout-upload');
    }
    
    /**
     * 订单拆分
     */
    public function splitAction(){
        $this->forward('split-new');
    }

    /**
     * 废弃
     * 订单拆分
     */
    public function splitNewAction(){
        
        $orderId = $this->getRequest()->getParam('orderId', '');
    
        if($this->getRequest()->isPost()){ // 拆单操作
            $op = $this->_request->getParam('op',array());
            $opArr = array();
             
            foreach($op as $k=>$v){
                if(!preg_match('/^([0-9]+)_(.*)$/', $k, $m)){
                    $return = array(
                            'ask' => 0,
                            'message' => '提交的参数不合法'
                    );
                    die(Zend_Json::encode($return));
                }
                /*
                 if(isset($opArr[$m[1]][$m[3]])){
                $opArr[$m[1]][$m[3]]['op_quantity'] += $v;
                }else{
                $opArr[$m[1]][$m[3]] = array('product_sku'=>$m[3],'op_quantity'=>$v,'op_ref_item_id'=>$m[2]);
                }
                */
    
                $opArr[$m[1]][] = array('product_sku'=>$m[2],'op_quantity'=>$v);
    
    
            }
            //             print_r($opArr);exit;
            //             print_r($this->_request->getParams());exit;
    
            $process = new Service_OrderProcess();
            $return = $process->orderSplitTransaction($orderId,$opArr);
            die(Zend_Json::encode($return));
        }
    
        $order = Service_Orders::getByField($orderId, 'order_id');
    
        if(empty($order)){
            header('Content-Type:text/html;charset=utf-8 ');
            echo '订单不存在';
            exit();
        }

        $address = Service_ShippingAddress::getByField($order['refrence_no_platform'], 'OrderID');
        $order['address'] = $address;
        $orderProductRows = Service_OrderProductProcess::getOrderProductActiveForSplit($order['refrence_no_platform']);
        $productArr = array();        
        foreach($orderProductRows as $k=> $op){
            $tmp = $op;
            $tmp['op_quantity'] = 1;
            for($i = 0;$i < $op['op_quantity'];$i ++){
                $productArr[] = $tmp;
            }
        }
        
        $order['product'] = $productArr;
        if(count($productArr) <= 1||!in_array($order['order_status'],array(2,5,7))){
            header('Content-Type:text/html;charset=utf-8 ');
            echo '订单不可进行拆单操作-->' . $order['refrence_no_platform'];
            exit();
        }
        $this->view->order = $order;
        $this->view->orderProductJson = Zend_Json::encode($orderProductRows);
        echo Ec::renderTpl($this->tplDirectory . "order_split_new.tpl", 'layout');
    }
    /**
     * 订单导入
     */
    public function getOrderLogAction(){
        $return = array('ask'=>0,'message'=>'无订单日志');
        $ref_id = $this->getRequest()->getParam('ref_id', '');
        $con = array('ref_id'=>$ref_id);
        $data = Service_OrderLog::getByCondition($con,'*',0,0,'log_id desc');
//         print_r($data);exit;
        if($data){
            $statusArr = Service_OrderProcess::getStatusArr();//Service_OrderProcess::$statusArr;
            //清除掉一个默认的状态
            unset($statusArr['empty']);
            foreach($data as $k=>$v){
                $data[$k]['op_username'] = Ec::Lang('system');//'系统';
                $u = Service_User::getByField($v['op_user_id'],'user_id');
                if($u){
                    $data[$k]['op_username'] = $u['user_name'];
                }
            }
            $return['data'] = $data;
            $return['ask'] = 1;
            $return['statusArr'] = $statusArr;

        }
        die(Zend_Json::encode($return));
    }

    /**
     * 客服留言
     */
    public function orderNoteAction(){
        $ref_id = $this->getRequest()->getParam('ref_id', '');
        $order = Service_Orders::getByField($ref_id, 'refrence_no_platform');
        if($this->getRequest()->isPost()){
            $return = array(
                'ask' => 0,
                'message' => '操作失败'
            );
            $note_content = $this->getRequest()->getParam('note_content', '');
            $note_content = trim($note_content);
            try{
                if(empty($order)){
                    throw new Exception('订单不存在-->'.$ref_id);
                } 
                $allowStatus = array('2','3','4','5','6','7','0');
                if(!in_array($order['order_status'],$allowStatus)){
                    throw new Exception('订单状态不正确,只有 ‘待发货审核，待发货，已发货，冻结中，缺货，问题件，已废弃’ 订单才可进行客服留言');
                }
                if(empty($note_content)){
                    throw new Exception('请填写留言内容');
                }
                
                //记录日志
                $logRow = array(
                		'ref_id' => $ref_id,
                		'log_content' => '客服留言：' . ($note_content?$note_content:'空')
                );
                
                //记录备注
                $now = date('Y-m-d H:i');
                $user_id = Service_User::getUserId();
                
                Service_OrderLog::add($logRow);
                $updateRow = array(
                        'operator_note' => $note_content,
                		'operator_note_id' => $user_id,
                		'operator_note_time' => $now,
                        'date_last_modify' => $now,
                );
                
                Service_Orders::update($updateRow, $ref_id, 'refrence_no_platform');
                $return['ask'] = 1;
                $return['message'] = '操作成功';
                
            }catch(Exception $e){               
                $return['message'] = '操作失败,失败原因：'.$e->getMessage();
            }
            
            die(Zend_Json::encode($return));
        }
        
        die(Zend_Json::encode($order));
    }
    /**
     * 统计订单产品数量
     */
    public function orderProductCountTongjiAction(){
        $ref_id = $this->getRequest()->getParam('ref_id', '');
        $ref_id = trim($ref_id);
        $return = Service_OrderProcess::orderProductCountTongji($ref_id);
        die(Zend_Json::encode($return));
    }

    /**
     * 订单下载日志
     */
    public function orderLoadLogAction(){
        $ref_id = $this->getRequest()->getParam('ref_id', '');
        $ref_id = trim($ref_id);
        $con = array('ref_id'=>$ref_id);
        $return = array('ask'=>0,'message'=>'No Data');
        $data = Service_OrderLoadLog::getByCondition($con);
        if($data){
            foreach($data as $k=>$v){
                $data[$k]['log_content'] = print_r(unserialize($v['log_content']),true);                
            }
            $return['ask'] = 1;
            $return['data'] = $data;
        }
        die(Zend_Json::encode($return));
    }
    /**
     * 获取订单数据
     */
    public function getEbayDataAction(){
        $ref_id = $this->getRequest()->getParam('ref_id', '');
        $order = Service_Orders::getByField($ref_id, 'refrence_no_platform');
        
        $token = Ebay_EbayLib::getUserToken($order['user_account']);
        if(! $token){
            echo 'ebay用户不合法';
        }else{
			$data = Ebay_EbayLib::getEbayOrdersById ( $token, array (
					$ref_id 
			) );
			if ($data ['GetOrdersResponse'] ['Ack'] !== 'Failure') {
				$response = $data ['GetOrdersResponse'];

				$response = $response ['OrderArray'];

				$response = $response ['Order'];
				
				$dataOA = array ();
				if (isset ( $response [0] )) { // 只有一个订单
					$dataOA = $response;
				} else {
					$dataOA [] = $response;
				}
				foreach ( $dataOA as $k => $v ) {
					$v ['user_account'] = $order['user_account'];
					$v ['company_code'] = Common_Company::getCompanyCode ();
					$dataOA [$k] = $v;
				}
				// 保存到数据表
				$service = new Ebay_OrderEbayService ();
				$service->saveOrder ( $dataOA );

// 				foreach ( $dataOA as $k => $v ) {
// 					$order_sn = $v['OrderID'];
// 					Ebay_GenEbayOrderService::generateOrderSingleTransaction($order_sn);
// 				}
			}
			
			$data = print_r ( $data, true );
			$data = preg_replace ( "/\n/", '<br/>', $data );
			$data = preg_replace ( "/ /", '&nbsp;', $data );
			print_r ( $data );
		}
    }
    

    /**
     * 标记订单发货到ebay
     */
    public function completeSaleAction(){
        set_time_limit(0);
        $return = array();
        $refIds = $this->getRequest()->getParam('ref_id', array());
        $pro = new Ebay_OrderEbayService();
        foreach($refIds as $refId){
            $return[] = $pro->completeSaleNew($refId);            
        }        
        print_r($return);
        
    }
    
    public function ordersMarkAction(){
    	if($this->getRequest()->isPost()){
    		$return = array('ask'=>0,'message'=>'');
	    	$processAgainId = $this->getRequest()->getParam('process_again', '');
	    	$order_ids = $this->_request->getParam('orderId',array());
	    	
	    	if(empty($processAgainId)){
	    		$return['message'] = '请选择标记类型';
	    		die(Zend_Json::encode($return));
	    	}
	    	if(empty($order_ids)){
	    		$return['message'] = '请选择订单';
	    		die(Zend_Json::encode($return));
	    	}
	    	
	    	$process = new Service_OrderProcess ();
	    	$result = $process->updateOrderProcessAgainTransaction( $order_ids, $processAgainId );
	    	$return['result'] = $result;
	    	$return['ask'] = 1;
	    	die ( Zend_Json::encode ( $return ) );
    	}
    }
    
    /**
     * 历史购买订单
     */
    public function getCustomerOrderAction(){
        $refId = $this->getRequest()->getParam('ref_id', '');
        $result = Service_OrderDetailProcess::getCustomerOrder($refId);
        $return = array('ask'=>0,'message'=>'No Data');
        if(is_array($result)&&count($result)>0){
            $return['ask'] = 1;
            $return['data'] = $result;
        }else{
            $return['message'] = $result;
        }
        die(Zend_Json::encode($return));
    }

    /**
     * 邮件
     */
    public function getCustomerMessageAction(){
        $refId = $this->getRequest()->getParam('ref_id', '');
        $result = Service_OrderDetailProcess::getCustomerMessage($refId);
        $return = array('ask'=>0,'message'=>'No Data');
        if(is_array($result)&&count($result)>0){
            $return['ask'] = 1;
            $return['data'] = $result;
        }else{
            $return['message'] = $result;
        }
        die(Zend_Json::encode($return));
    }
    /**
     * case
     */
    public function getCustomerCaseAction(){
        $refId = $this->getRequest()->getParam('ref_id', '');
        $result = Service_OrderDetailProcess::getCustomerCase($refId);
        $return = array('ask'=>0,'message'=>'No Data');
        if(is_array($result)&&count($result)>0){
            $return['ask'] = 1;
            $return['data'] = $result;
        }else{
            $return['message'] = $result;
        }
        die(Zend_Json::encode($return));
    }
    /**
     * 评价
     */
    public function getCustomerFeedbackAction(){
        $refId = $this->getRequest()->getParam('ref_id', '');
        $result = Service_OrderDetailProcess::getCustomerFeedBack($refId);
        $return = array('ask'=>0,'message'=>'No Data');
        if(is_array($result)&&count($result)>0){
            $return['ask'] = 1;
            $return['data'] = $result;
        }else{
            $return['message'] = $result;
        }
        die(Zend_Json::encode($return));
    
    }

    /**
     * 验证是否导出过
     */
    public function checkExportAction(){
        $refIds = $this->getRequest()->getParam('ref_ids', '');
        $return = array();
        foreach($refIds as $refId){
            $result = array('ask'=>0,'message'=>'订单未导出','ref_id'=>$refId);
            $order = Service_Orders::getByField($refId,'refrence_no_platform');
            if(!$order){
                continue;
            }
            $result['wms_ref_id'] = $order['refrence_no_warehouse'];
            if($order['has_export']){
                $result['ask'] = 1;
                $result['message'] = '订单已经导出过';
            } 
            $return[] = $result;
        }        
        die(Zend_Json::encode($return));
    }

    /**
     * 一键拆单
     */
    public function splitOneClickAction(){
        if ($this->getRequest ()->isPost ()) {
            set_time_limit(0);
            $param = $this->getRequest()->getParams();
            $refId = $this->_request->getParam('refId', '');
            $entireSplit = $this->_request->getParam('entireSplit', '');
            if($entireSplit=='1'){
                $entireSplit = true;
            }else{
                $entireSplit = false;
            }
            $warehouseId = $this->_request->getParam('warehouse_id', '');
            $shippingMethod = $this->_request->getParam('shipping_method', '');
            $op_quantity_add = $this->_request->getParam('op_quantity_add', '');
            
            $process = new Service_OrderProcess();
            $no_stock_verify = true;
            //一键拆单
            $return = $process->splitOneClick($refId,$entireSplit,$op_quantity_add, $no_stock_verify);
            die(json_encode($return));
        }

        $orderId = $this->getRequest()->getParam('orderId','');        
        $order = Service_Orders::getByField($orderId,'order_id');
        if(!empty($order['warehouse_id'])){
            $warehouse = Service_Warehouse::getByField($order['warehouse_id'],'warehouse_id');
            $order['warehouse_name'] = $warehouse?$warehouse['warehouse_code']:'';            
        }else{
            $this->view->no_warehouse = 1;
        }
        if($order['create_method']==1){
        
            //$this->_redirect('/order/order/create/order_id/'.$order['order_id']);exit;
        }
        $order['platform'] = strtolower($order['platform']);  
        $address = Service_ShippingAddress::getByField($order['refrence_no_platform'],'OrderID');
        
        $order_product = Service_OrderProductProcess::getOrderProductLatest($order['refrence_no_platform']);
        
        sort($order_product);
//         	        			print_r($order_product);exit;
        $order['address'] = $address;
        $order['order_product'] = $order_product;
        
//         print_r($order);
        $this->view->order = $order;
        
        //目的国 家
        $countryArr = Service_Country::getAll();
        $this->view->countryArr = $countryArr;
        
            
        $order['order_product'] = $order_product;
        echo Ec::renderTpl ( $this->tplDirectory . "order_split_one_click.tpl", 'layout' );
        
    }

    /**
     * 一键拆单,批量
     */
    public function splitOneClickBatchAction(){
        if($this->getRequest()->isPost()){
            set_time_limit(0);
            $param = $this->getRequest()->getParams();
//             print_r($param);exit;
            $refIds = $this->_request->getParam('refId', array());
            $entireSplit = $this->_request->getParam('entireSplit', '');
            if($entireSplit=='1'){
                $entireSplit = true;
            }else{
                $entireSplit = false;
            }
//             var_dump($entireSplit);exit;
            $warehouseId = '';
            $shippingMethod = '';
            $op_quantity_add =  $this->_request->getParam('op_quantity_add', array());
            if(is_string($refIds)){
                $refIds = preg_replace('/[^a-zA-Z0-9\-]+/', ' ', $refIds);
                $refIds = trim($refIds);
                $refIds = explode(' ', $refIds);
            }
//             print_r($refIds);exit;
            $process = new Service_OrderProcess();
            $return = array();
            $no_stock_verify = true;//无库存订单是否也审核
            foreach($refIds as $refId){
                // 一键拆单
                $rs = $process->splitOneClick($refId, $entireSplit, $op_quantity_add, $no_stock_verify);
                $return[] = $rs;
            }
            die(json_encode($return));
        }
    }
    /**
     * 获取可用数量
     */
    public function getSellableAction(){
        $warehouseId = $this->_request->getParam('warehouse_id', '');
        $productSku = $this->getParam('sku','');
        $inventory = Service_OrderProductProcess::getProductSellable($warehouseId, $productSku);
       
        die(json_encode($inventory));
    }
    
    /**
     * 更新费用为负数的订单
     * */
    public function updateFeeAction0(){
        set_time_limit(600);
        $db = Common_Common::getAdapter();
        $sql = 'select  DISTINCT OrderID  from order_product where unit_price<0 or unit_finalvaluefee<0 or unit_platformfee<0 or unit_shipfee<0';
        $data = $db->fetchAll($sql);
        foreach($data as $v){
            try{                
                Service_OrderProductProcess::updateOrderProductUnitPriceFinalValueFee($v['OrderID']);
            }catch(Exception $e){
                echo $e->getMessage();
            }
        }
        echo '========================= success =============================';
    }
    

    /**
     * 更新订单费用
     * */
    public function updateFeeInputAction(){
        $refIds = $this->getParam('refIds','');
        $this->view->refIds = $refIds;
        if($this->getRequest()->isPost()){
            set_time_limit(600);
            $refIds = trim($refIds);
            if(!empty($refIds)){
                $refIds = preg_replace('/\s+/', ' ', $refIds);
                $refIds = explode(' ', $refIds);
                //         print_r($refIds);exit;
                foreach($refIds as $refId){
                    try{
                        Service_OrderProductProcess::updateOrderProductUnitPriceFinalValueFee($refId);
                    }catch(Exception $e){
                        echo $refId.$e->getMessage()."<br/>";
                    }
                }
                echo '========================= success =============================';
            }
            
        }
		echo Ec::renderTpl ( $this->tplDirectory . "update_order_fee.tpl", 'layout' );
    }
 
    /**
     * 获取仓库产品费用
     */
    public function getWmsOrderFeeAction(){
        $rest = new Common_Rest();
        $wmsCode = $this->getParam('wms_code','');
        if($wmsCode){
            try{                
                $result = $rest->getOrderIntegrateForWms($wmsCode);
                print_r($result);
            }catch(Exception $e){
                echo $wmsCode . $e->getMessage() . "<br/>";
            }
        }
    }
    /**
     * 订单费用对比
     */
    public function updateWmsEbFeeAction(){
        set_time_limit(600);
        $rest = new Common_Rest();
        $rest->updateWmsEbFee('2013-09-30', '2013-10-31', true);
    }

    /**
     * 订单费用对比
     */
    public function verifyWmsEbFeeAction(){
        set_time_limit(600);
        $rest = new Common_Rest();
        $rest->updateWmsEbFee('2013-09-30', '2013-10-02', false);
    }

    /**
     * 更新系统单号
     */
    public function updateRefNoSysAction(){
        set_time_limit(360);
        while(true){
            $db = Zend_Registry::get('db');
            $sql = "select  refrence_no_platform,order_id from orders where refrence_no_sys not LIKE 'SYS%' or refrence_no_sys is null limit 3000;";
            $data = $db->fetchAll($sql);
//             print_r($data);exit;
            foreach($data as $v){
                try{
                    Service_OrderProcess::updateOrderReferenceNoSys($v['refrence_no_platform']);
                }catch(Exception $e){
                    echo $v['refrence_no_platform'] . ":" . $e->getMessage()."\n";
                }
            }
            if(empty($data)){
                break;
            }
            unset($data);
//             sleep(10);
        }
        echo "======================================";
    }

    /**
     * 更新系统单号
     */
    public function updateListTypeAction(){
        set_time_limit(360);
        while(true){
            $db = Zend_Registry::get('db');
            $sql = "select  refrence_no_platform,order_id from orders where platform='ebay' and order_type='sale' and create_type='api' and item_list_type is null limit 3000;";
            $data = $db->fetchAll($sql);
            //             print_r($data);exit;
            foreach($data as $v){
                try{
                    Ebay_OrderEbayService::updateOrderListType($v['refrence_no_platform']);
                }catch(Exception $e){
                    echo $v['refrence_no_platform'] . ":" . $e->getMessage()."\n";
                }
            }
            if(empty($data)){
                break;
            }
            unset($data);
            //             sleep(10);
        }
        echo "======================================";
    }

    /**
     * 更新订单关键字
     */
    public function getKeywordAction(){
        Zend_Registry::set('SAPI_DEBUG',true);
        $refId = $this->getParam('ref_id','');
        $return = Service_OrderKeywordProcess::updateOrderKeyword($refId);
        echo $return;
    }
    /**
     * 手动触发更新订单关键字服务
     */
    public function cronUpdateOrderKeywordAction(){
        Zend_Registry::set('SAPI_DEBUG',true);
        $return = Service_OrderKeywordProcess::cronUpdateOrderKeyword();
    }
}