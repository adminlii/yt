<?php
/**
 * 生成Amazon系统订单
 * @author Frank
 * @date 2013-11-16 11:23:24
 */
class Amazon_GenerateSystemOrders{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'AmazonGenerateSystemOrders_';
	
	/**
	 * 亚马逊订单生成系统订单
	 */
	public function callAmazonOrdersToSysOrder(){
		/*
		 * 1. 查询已经下载完毕的亚马逊订单
		 */
		$conAmazonOrderOriginal = array(
								'is_loaded'=>1
							);
		$resultAmazonOrderOriginal = Service_AmazonOrderOriginal::getByCondition($conAmazonOrderOriginal,'*',0,1,'last_update_date asc');
		
		/*
		 * 2. 订单不为空，查询Amazon订单的Items
		 */
		if(!empty($resultAmazonOrderOriginal)){
			$addRow = array();
			foreach ($resultAmazonOrderOriginal as $amazonOrdersKey => $amazonOrdersValue) {
				try {
					
					//检查是否有上传过手工订单
					$resultCheck = Service_Orders::getByField($amazonOrdersValue['amazon_order_id'],'refrence_no');
					//存在客户参考号与Amazon订单号一致时，修改状态为存在相同订单，进入下一次循环
					$aoo_id = $amazonOrdersValue['aoo_id'];
					if(!empty($resultCheck)){
						Service_AmazonOrderOriginal::update(array('is_loaded'=>'3'), $aoo_id,'aoo_id');
						continue;
					}
					$row = array();
					$conAmazonOrderPayment = array(
							'aoo_id'=>$aoo_id
					);
					$resultAmazonOrderPayment = Service_AmazonOrderPayment::getByCondition($conAmazonOrderPayment);
					
					$conAmazonOrderDetail = array(
							'aoo_id'=>$aoo_id
					);
					$site = Amazon_AmazonLib::getSiteByMarketplaceId($amazonOrdersValue['marketplace_id']);
					
					$resultAmazontOrderDetail = Service_AmazonOrderDetail::getByCondition($conAmazonOrderDetail);
					
					$orderRow = $this->conventOrders($amazonOrdersValue, $resultAmazontOrderDetail ,$site);
					
					$amazonOrderId = $amazonOrdersValue['amazon_order_id'];
					$payDate = $amazonOrdersValue['purchase_date'];
					$orderProductRow = $this->convertOrderProduct($resultAmazontOrderDetail, $site, $amazonOrderId, $payDate);
					
					$shippingAddress = $this->convertShippingAddress($amazonOrdersValue);
					
					//检查试运行
					foreach ($orderProductRow as $val_key => $val_value) {
						try {
							$exist = Common_AllowSKU::validateSku($val_value['product_sku'],$amazonOrdersValue['company_code'],$amazonOrdersValue['user_account']);
						} catch (Exception $e) {
							//更新订单信息，不在进行生成订单
							Service_AmazonOrderOriginal::update(array('is_loaded'=>'4'), $amazonOrderId,'amazon_order_id');
							//跳出循环
							throw new Exception("AmazonID:" . $amazonOrderId . " SKU:[{$val_value['product_sku']}] 不存在于试运行的SKU列表;[原始异常信息：".$e->getMessage()."]");
						}
					}
					$row['order'] = $orderRow;
					$row['orderProduct'] = $orderProductRow;
					$row['shippingAddress'] = $shippingAddress;
					$addRow[] = $row;
					
				} catch (Exception $e) {
					echo $e->getMessage();
					$date = date('Y-m-d H:i:s');
					Ec::showError("时间：'$date'封装参数，信息异常：" . $e->getMessage(), self::$log_name);
				}
			}
			
// 			print_r($addRow);
			if(count($addRow > 0)){
				$model = Service_Orders::getModelInstance();
				$db = $model->getAdapter();
				$db->beginTransaction();
				try{
					
					foreach ($addRow as $addKey => $addValue) {
						$addOrderRow = $addValue['order'];
						
						$addOrderProductRow = $addValue['orderProduct'];
						$addShippingAddress = $addValue['shippingAddress'];
						
						$refrence_no_platform = $addOrderRow['refrence_no_platform'];
						$resultOrders = $model->getByField($refrence_no_platform,'refrence_no_platform');
						
						if(empty($resultOrders)){
							try {
								
								//删除order_product
								Service_OrderProduct::delete($refrence_no_platform,'OrderID');
								//删除shipping_address
								Service_ShippingAddress::delete($refrence_no_platform,'OrderID');
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：删除订单明细和地址明细", self::$log_name);
								throw new Exception($e->getMessage());
							}
							
							$order_id = '';
							try {
								
							    $refrence_no_sys = Common_GetNumbers::getCode('CURRENT_ORDER_SYS_COUNT', 'SYS'); // 系统单号
							    $addOrderRow['refrence_no_sys'] = $refrence_no_sys;						    
								//插入orders表
								$order_id = $model->add($addOrderRow);
	// 							echo 'orderId: ' . $order_id . '<br/><br/>';
								//更新amazon_order_original
								Service_AmazonOrderOriginal::update(array('is_loaded'=>'2'), $refrence_no_platform,'amazon_order_id');
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：生成单头和更新订单原始表", self::$log_name);
								throw new Exception($e->getMessage());
							}
							
							if(empty($order_id)){
								throw new Exception("生成订单，未返回订单ID");
							}
							
							try {
								
								//插入order_product表
								if(!empty($addOrderProductRow)){
									foreach ($addOrderProductRow as $productKey => $productValue) {
										$productValue['order_id'] = $order_id;
										print_r($productValue);
										Service_OrderProduct::add($productValue);
									}
								}else{
									throw new Exception('<产品信息>为空，请检查:' . print_r($addValue,true));
								}
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：生成订单明细", self::$log_name);
								throw new Exception($e->getMessage());
							}
							
							try {
								
								//插入shipping_address
								if(!empty($addShippingAddress)){
									$shippingAddress_id = Service_ShippingAddress::add($addShippingAddress);
									$model->update(array('shipping_address_id'=>$shippingAddress_id), $order_id);
								}else{
									throw new Exception('<地址信息>为空，请检查:' . print_r($addValue,true));
								}
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：生成订单地址，并更新订单表地址ID", self::$log_name);
								throw new Exception($e->getMessage());
							}
							
// 							echo 'amazonOrderId: ' . $refrence_no_platform;

							try {
								
	    						//更新产品对应关系
	    						Service_OrderForWarehouseProcessNew::updateOrderProductWarehouseSku($refrence_no_platform);
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：更新产品对应关系", self::$log_name);
								throw new Exception($e->getMessage());
							}
    						
    						//插入日志
    						$logRow = array(
    								'ref_id' => $refrence_no_platform,
    								'log_content' => 'LOAD订单，生成Amazon订单',
    								'op_id' => '9'
    						);
    						Service_OrderLog::add($logRow);
						}
					}
					$db->commit();
					
				}catch (Exception $e){
					$db->rollBack();
					$date = date('Y-m-d H:i:s');
					Ec::showError("时间：'$date'生成系统订单出现异常,错误原因：".$e->getMessage(), self::$log_name);
					return array('ask'=>'0','message'=>$e->getMessage());
				}
			}
		}
	}
	
	/**
	 * 封装amazon订单信息，为系统订单
	 * @param unknown_type $amazonOrderOriginal
	 * @param unknown_type $resultAmazontOrderDetail
	 * @param unknown_type $site
	 * @return multitype:string unknown number
	 */
	public function conventOrders($amazonOrderOriginal,$resultAmazontOrderDetail ,$site){
		$row = array();
		if(!empty($amazonOrderOriginal) && !empty($resultAmazontOrderDetail)){
			$row['platform'] = 						'amazon';
			$row['data_source'] = 					'amazon';
			$row['order_type'] = 					'sale';
			$row['create_type'] = 					'api';
			$fulfillment_channel = $amazonOrderOriginal['fulfillment_channel'];
			if($fulfillment_channel == 'AFN'){		//亚马逊配送直接转到已发货
				$row['order_status'] = 					'4';
				$row['sync_status'] = 					'1';
				$row['sync_time'] = 					date('Y-m-d H:i:s');
			}else{
				//array('Unshipped','PartiallyShipped','Shipped')
				if($amazonOrderOriginal['order_status'] == 'Shipped' || $amazonOrderOriginal['order_status'] == 'PartiallyShipped'){
					$row['order_status'] = 					'4';
					$row['sync_status'] = 					'1';
				}else{
					$row['order_status'] = 					'2';
					$row['sync_status'] = 					'0';
				}
				$row['sync_time'] = 					'';
			}
			$row['create_method'] = 				'2';
			
			$row['customer_id'] = 					'';
			$row['company_code'] = 					$amazonOrderOriginal['company_code'];
			$row['shipping_method'] = 				'';
			$row['shipping_method_platform'] = 		'';
			$row['warehouse_id'] = 					'';
			$row['order_desc'] = 					'';
			$row['date_create'] = 					date('Y-m-d H:i:s');
			$row['date_release'] = 					'';
			$row['date_warehouse_shipping'] = 		'';
			$row['date_last_modify'] = 				date('Y-m-d H:i:s');
			$row['operator_id'] = 					'';
			$row['refrence_no'] = 					$amazonOrderOriginal['amazon_order_id'];
			$row['refrence_no_platform'] = 			$amazonOrderOriginal['amazon_order_id'];
			$row['refrence_no_sys'] = 				'';
			$row['shipping_address_id'] = 			'';
			$row['refrence_no_warehouse'] = 		'';
			$row['shipping_method_no'] = 			'';
			$row['date_create_platform'] = 			$amazonOrderOriginal['purchase_date'];
			$row['date_paid_platform'] = 			$amazonOrderOriginal['last_update_date'];
			$row['date_paid_int'] = 				'';		
			
			$totalAmount = 0;
			$totalShippingAmount = 0;
			$totalQty = 0;
			$is_one_piece = '1';
			foreach ($resultAmazontOrderDetail as $itemsKey => $itemsValue) {
				$totalAmount += $itemsValue['item_price_amount'] + $itemsValue['shipping_price_amount'];
				$totalShippingAmount += $itemsValue['shipping_price_amount'];
				$totalQty += $itemsValue['quantity_ordered'];
			}
			if($totalQty > 1){
				$is_one_piece = '0';
			}
			
			$row['amountpaid'] = 					$totalAmount;						//包运费
			$row['subtotal'] = 						round(($amazonOrderOriginal['amount'] - $totalShippingAmount),3);		//不包运费
			$row['ship_fee'] = 						$totalShippingAmount;				//运费
			
			$row['platform_fee'] = 					'0';								//平台费用
			$row['currency'] = 						$amazonOrderOriginal['currency_code'];
			$row['user_account'] = 					$amazonOrderOriginal['user_account'];
			$row['buyer_id'] = 						preg_replace('/\s+/','',strtolower($amazonOrderOriginal['buyer_name']));//amazon订单，无买家id，以收件人姓名为准
			$row['third_part_ship'] = 				'0';								//是否第三方仓库发货，默认0，审核订单的时候改变
			$row['is_merge'] = 						'0';
			$row['site'] = 							$amazonOrderOriginal['sales_channel'];
			$row['consignee_country'] = 			$amazonOrderOriginal['shipping_address_country_code'];
			$row['order_weight'] = 					'';
			$row['abnormal_type'] = 				'';
			$row['abnormal_reason'] = 				'';
			$row['buyer_name'] = 					$amazonOrderOriginal['buyer_name'];
			$row['buyer_mail'] = 					$amazonOrderOriginal['buyer_email'];
			$row['has_buyer_note'] = 				'';
			
			$row['is_one_piece'] = 					$is_one_piece;			//判断一下items
			$row['operator_note'] = 				'';
			$row['product_count'] = 				$totalQty;				//产品数量		
			$row['fulfillment_channel'] = 			$amazonOrderOriginal['fulfillment_channel'];
			$row['process_again'] = ($amazonOrderOriginal['fulfillment_channel'] == 'AFN')?'2':'1';//在处理标记，AFN是FBA订单，需要再次运行传入到仓库
			$row['ship_service_level'] =			$amazonOrderOriginal['ship_service_level'];
			$row['shipment_service_level_category']=$amazonOrderOriginal['shipment_service_level_category'];
		}
		return $row;	
	}
	
	/**
	 * 封装amazon订单地址信息，为系统地址
	 * @param unknown_type $amazonOrderOriginal
	 */
	public function convertShippingAddress($amazonOrderOriginal){
		$row = array();
		if(!empty($amazonOrderOriginal)){
			
			$row['Name'] = 				$amazonOrderOriginal['shipping_address_name'];
			$row['Street1'] = 			$amazonOrderOriginal['shipping_address_address1'];
			$row['Street2'] = 			$amazonOrderOriginal['shipping_address_address2'] . ' ' . $amazonOrderOriginal['shipping_address_address3'];
			//$row['Street3'] = 			$amazonOrderOriginal['shipping_address_address3'];
			$row['CityName'] = 			$amazonOrderOriginal['shipping_address_city'];
			$row['StateOrProvince'] = 	$amazonOrderOriginal['shipping_address_state'];
			$row['Country'] = 			$amazonOrderOriginal['shipping_address_country_code'];
			$resultCountry = Service_Country::getByField($amazonOrderOriginal['shipping_address_country_code'],'country_code');
			$row['CountryName'] = 		$resultCountry['country_name_en'];	
			$row['District'] =  		$amazonOrderOriginal['shipping_address_district'];
			$row['Phone'] = 			$amazonOrderOriginal['shipping_address_phone'];
			$row['PostalCode'] = 		$amazonOrderOriginal['shipping_address_postal_code'];
			$row['AddressID'] = 		'';
			$row['AddressOwner'] = 		'';
			$row['ExternalAddressID'] = '';
			$row['OrderID'] = 			$amazonOrderOriginal['amazon_order_id'];
			$row['Plat_code'] = 		'amazon';
			$row['company_code'] = 		$amazonOrderOriginal['company_code'];
			$row['create_date_sys'] = 	date('Y-m-d H:i:s');
			$row['modify_date_sys'] = 	date('Y-m-d H:i:s');
			$row['user_account'] = 		$amazonOrderOriginal['user_account'];
			$row['is_modify'] = 		'0';
		}
		return $row;
	}
	
	/**
	 * 封装amazon订单Itmes信息，为系统单身
	 * @param unknown_type $amazontOrderDetail
	 * @param unknown_type $site
	 * @param unknown_type $amazonOrderId
	 */
	public function convertOrderProduct($amazontOrderDetail, $site ,$amazonOrderId , $payDate){
		$row = array();
		if(!empty($amazontOrderDetail)){
			foreach ($amazontOrderDetail as $amazonOrderDetailKey => $amazonOrderDetailValue) {
				$product = array();
				$product['order_id'] = 			'';
				$product['product_id'] = 		'0';
				$product['product_sku'] = 		$amazonOrderDetailValue['seller_sku'];
				$product['warehouse_sku'] = 	'';
				$product['product_title'] = 	$amazonOrderDetailValue['title'];
				$product['op_quantity'] = 		$amazonOrderDetailValue['quantity_ordered'];
				$product['op_ref_tnx'] = 		$amazonOrderDetailValue['order_item_id'];
				$product['op_recv_account'] = 	'';
				$product['op_ref_item_id'] = 	$amazonOrderDetailValue['order_item_id'];
				$product['op_site'] = 			$site;
				$product['op_record_id'] = 		'';
				$product['op_ref_buyer_id'] = 	'';
				$product['op_ref_paydate'] = 	$payDate;
				$product['op_add_time'] = 		date('Y-m-d H:i:s');
				$product['op_update_time'] = 	date('Y-m-d H:i:s');
				$product['OrderID'] = 			$amazonOrderId;
				$product['OrderIDEbay'] = 		$amazonOrderId;
				$product['is_modify'] = 		'0';
				$product['pic'] = 				'';
				$product['url'] = 				'';
				$product['unit_price'] = 		$amazonOrderDetailValue['item_price_amount'] / $amazonOrderDetailValue['quantity_ordered'];
				$product['give_up'] = 			($amazonOrderDetailValue['quantity_ordered'] == 0)?1:0;   //是否废弃，item数量为0时，设置为1：表示该item不发货
				$product['currency_code'] = 	$amazonOrderDetailValue['item_price_currency_code'];
				$row[] = $product;
			}
		}
		return $row;
	}
	
	/**
	 * 将FBA订单发送至WMS
	 */
	public function runFbaOrderToWms(){
		
		/*
		 * 2、查询推送失败的订单
		*/
		$con_wait = array(
				'platform'=>'amazon',
				'order_type'=>'sale',
				'create_type'=>'api',
				'create_method'=>'2',
				'order_status'=>'4',				//已发货
				'fulfillment_channel'=>'AFN',	//FBA订单
				'process_again'=>'3',			//异常的
		);
		$result_orders = Service_Orders::getByCondition($con_wait);
		echo '<br/><br/>推送失败过的订单：<br/>';
		$this->callFbaOrderToWms($result_orders,false);
		
		
		/*
		 * 1、查询未推送的订单
		 */
		$con_wait['process_again'] = '2';		//第一次处理的
		$result_orders = Service_Orders::getByCondition($con_wait);
		echo '<br/><br/>未推送的订单：<br/>';
		$this->callFbaOrderToWms($result_orders,true);
		
		
	}
	
	/**
	 * 根据传入订单行数据，推送至WMS
	 * @param unknown_type $order_rows	订单数组
	 * @param unknown_type $is_log		是否记录错误日志
	 * @throws Exception
	 */
	public function callFbaOrderToWms($order_rows,$is_log_error){
		/*
		 * 1、得到FBA 订单
		*/
		$result_orders = $order_rows;
		
		/*
		 * 2、检查订单是否分配了仓库和运输方式
		 */
		$order_wait = array();			//可以发送到WMS的订单行数据
		$order_wait_code = array();		//可以发送到WMS的订单号
		foreach ($result_orders as $key_o => $value_o) {
			$order_sn = $value_o['refrence_no_platform'];
			$tmp_warehouse_id = $value_o['warehouse_id'];
			$tmp_shipping_method = $value_o['shipping_method'];
			if(empty($tmp_warehouse_id) || empty($tmp_shipping_method)){
				//给订单分配仓库的运输方式
				$response_ofs = self::orderFbaAllot($value_o, $fba_set_rows);
				if($response_ofs['ask'] == '1'){
					$order_wait[$order_sn] = $response_ofs['order'];
					$order_wait_code[] = $order_sn;
				}
			}else{
				$order_wait[$order_sn] = $response_ofs['order'];
				$order_wait_code[] = $order_sn;
			}
		}
		
		/*
		 * 3、准备数据，发送至WMS
		 */
// 		echo '发送至WMS的订单：';
// 		print_r($order_wait);
		if(!empty($order_wait) && !empty($order_wait_code)){
			$orderToWms = new Service_OrderForWarehouseProcessNew();
			//订单拆成50单，传一次WMS
			$refNoArr = array_chunk($order_wait_code, 50);
			foreach($refNoArr as $refNos){
				$paramsArr = array();
				foreach($refNos as $refNo){
					$orderCode = $refNo;
					try{
						/** ------- 检查仓库单号**开始  ------- */
						$o = Service_Orders::getByField($refNo,'refrence_no_platform');
						$wmsCode = Service_OrderProcess::updateAbnormalOrderReferenceNoExist($o['refrence_no_platform']);
						/**
						 * 更新系统单号
						 * 如果refrence_no_sys为空，则更新refrence_no_sys
						*/
						Service_OrderProcess::updateOrderReferenceNoSys($o['refrence_no_platform']);
			
						if($wmsCode){//直接更新仓库单号和其他信息
							Service_Orders::update(array('refrence_no_warehouse'=>$wmsCode,'process_again'=>'1','abnormal_reason'=>''), $refNo,'refrence_no_platform');
							throw new  Exception('FBA订单，已同步至仓库，更新仓库订单号：' . $wmsCode);
						}
						/** ------- 检查仓库单号**结束  ------- */
			
			
						/*
						 * 根据订单号，获得订单的信息
						*/
						$params = $orderToWms->getOrderIntegrate($orderCode);
						//删除历史记录
						Service_OrderProductToWms::delete($params['ReferenceNo'],'ref_id');
						foreach($params['orderProduct'] as $p){
							$opTwms = array(
									'product_sku' => $p['product_sku'],
									'warehouse_sku' => $p['sku'],
									'quantity' => $p['quantity'],
									'ref_tnx' => $p['refTnx'],
									'recv_account' =>empty( $p['recvAccount'])? '': $p['recvAccount'],
									'ref_item_id' => $p['refItemId'],
									'ref_buyer_id' => $p['refBuyerId'],
									'ref_pay_date' => $p['refPayDate'],
									'ref_id' => $params['ReferenceNo'],
			
									'subtotal' => $params['subtotal'],// 订单销售价
									'ship_fee' => $params['shippingCost'], // 运费
									'platform_fee' => $params['paypalFee'],// 手续费
									'finalvaluefee' => $params['finalvaluefee'],// 手续费
			
									'unit_price' => $p['TransactionPrice'],
									'unit_finalvaluefee' => $p['finalvaluefee'],
									'unit_platformfee' => $p['paypalFee'],
									'unit_shipfee' => $p['shipFee'],
			
									'currency_code' => $params['currencyCode'],
			
									'update_time' =>date('Y-m-d H:i:s'),
									'give_up' =>'0',
							);
							Service_OrderProductToWms::add($opTwms);
						}
						foreach($params['orderSpecialProduct'] as $p){
							$opTwms = array(
									'product_sku' => $p['product_sku'],
									'warehouse_sku' => $p['sku'],
									'quantity' => $p['quantity'],
									'ref_tnx' => $p['refTnx'],
									'recv_account' =>empty( $p['recvAccount'])? '': $p['recvAccount'],
									'ref_item_id' => $p['refItemId'],
									'ref_buyer_id' => $p['refBuyerId'],
									'ref_pay_date' => $p['refPayDate'],
									'ref_id' => $params['ReferenceNo'],
									 
									'subtotal' => $params['subtotal'],// 订单销售价
									'ship_fee' => $params['shippingCost'], // 运费
									'platform_fee' => $params['paypalFee'],// 手续费
									'finalvaluefee' => $params['finalvaluefee'],// 手续费
			
									'unit_price' => $p['TransactionPrice'],
									'unit_finalvaluefee' => $p['finalvaluefee'],
									'unit_platformfee' => $p['paypalFee'],
									'unit_shipfee' => $p['shipFee'],
			
									'currency_code' => $params['currencyCode'],
			
									'update_time' =>date('Y-m-d H:i:s'),
									'give_up' =>'1',
							);
							Service_OrderProductToWms::add($opTwms);
						}
						//删除历史记录
						Service_OrderDataToWms::delete($params['ReferenceNo'],'ref_id');
						$odtwRow = array(
								'ref_id' => $params['ReferenceNo'],
								'data' => serialize($params),
								'update_time' => date('Y-m-d H:i:s')
						);
						Service_OrderDataToWms::add($odtwRow);
						
						$paramsArr[] = $params;
						$log = array(
								'ref_id' =>$refNo,
								'create_time' => date('Y-m-d H:i:s'),
								'log_content' => 'FBA订单，同步到仓库',
								'data'=>print_r($params,true)
						);
						if($is_log_error){
							Service_OrderLog::add($log);
						}
						//更新仓库SKU内容
						$orderToWms->updateOrderProductWarehouseSku($orderCode);
					}catch(Exception $e){
						$log = array(
								'ref_id' =>$refNo,
								'create_time' => date('Y-m-d H:i:s'),
								'log_content' => $e->getMessage(),
						);
						Service_OrderLog::add($log);
						$failArr[] = array('ref_id'=>$refNo,'refrence_no_platform'=>$refNo,'message'=>array($e->getMessage()));//失败的订单
					}
				}
				
				if(empty($paramsArr)){
					continue;
				}
			
				try{
					$req = array(
							'service' => 'batchCreateSpecialOrder',		//仅适用于FBA订单推送
							'paramsJson' => json_encode(array(
									'paramsArr' => $paramsArr,
									'abnormal' => '1',					//缺货也要创建
									'autoShiped'=>'1'					//订单自动发货，现在仅适用于FBA订单
							))
					);
					//                 Ec::showError(print_r($req,true),'call_soap_service_request');
					try{//调用api失败
						Ec::showError(print_r($req,true),'FBA_!123123123');
						$return = $orderToWms->callSoapService($req);
					}catch (Exception $ee){
						Ec::showError("订单导入到wms异常,订单号如下：\n".implode(',', $refNos),'FBA_info_to_wms_fail_ref_id_');
			
// 						$info = "req:\n".$this->_soapClient->__getLastRequest();
// 						$info.= "\n";
// 						$info.= "res:\n".$this->_soapClient->__getLastResponse();
						$info.= "\n";
						$info.= "\n";
			
						Ec::showError($info,'FBA_to_wms_fail_info_'.date('Y-m-d_'));
			
						throw new Exception('API Internal error.','50000');
					}
					if($return['state'] == '0'){
						throw new Exception($return['message']);
					}
					if(! is_array($return['data'])){
						throw new Exception($return['message']);
					}
			
					foreach($return['data'] as $o){
						//只更新订单信息，不做状态更改
						$orderUpdateResult = $this->updateOrderStatus($o,$is_log_error);
						if($orderUpdateResult['ask'] == 1){
							$successArr[] = $orderUpdateResult; // wsm处理成功订单
							if($o['orderStatus'] == '3'){
								$quehuoArr[] = $orderUpdateResult;
							}
						}else{
							// $failArr[] = array('ref_id'=>$orderUpdateResult['refrence_no_platform'],'message'=>$orderUpdateResult['message']);//wms处理失败订单
							$failArr[] = $orderUpdateResult; // wms处理失败订单
						}
					}
					$result['state'] = 1;
				
				}catch(Exception $e){
					$result['message'] = $e->getMessage();
					$result['err_code'] = $e->getCode();
					Ec::showError($e->getMessage().print_r($failArr,true), 'FBA_info_to_wms_exception_');
			
					if($e->getCode()=='50000'){//服务器异常，跳出循环
						$result['state'] = 0;
						break;
					}
				}
			}
		}
		
		echo '正确[' .count($successArr). ']：';
		print_r($successArr);
		echo '<br/><br/>缺货[' .count($quehuoArr). ']：';
		print_r($quehuoArr);
		echo '<br/><br/>异常[' .count($failArr). ']：';
		print_r($failArr);
	}
	
	/**
	 * 更新订单状态
	 * @param unknown_type $result	创建订单，WMS回传的结果
	 * @param unknown_type $is_log_error	是否记录异常日志
	 * @return multitype:number string unknown Ambigous <string, unknown>
	 */
	public function updateOrderStatus($result,$is_log_error){
		$return = array('ask'=>0,'message'=>'','refrence_no_platform'=>$result['referenceNo'],'order_status'=>'0');
		$date = date('Y-m-d H:i:s');
		$response_error = false;
		if (isset($result['state']) && $result['state'] == '1') {
			$response_error = false;
			$return['ask'] = 1;
			$orderStatus = 3;
			$return['refrence_no_warehouse'] = $result['orderCode'];
			$return['ref_id'] = $result['orderCode'];
			$return['orderStatus'] = $result['orderStatus'];//判断是否库存不足
			$return['spCode'] = $result['spCode'];
			$quehuo = '';
			$sys_tips = '';
			if($result['orderStatus']=='3'){
				$orderStatus = 6;//缺货中
				$quehuo='，订单缺货<[' . print_r($result['OOS'],true) . ']>';
				//查询订单SKU信息
				$result_orderProduct = Service_OrderProduct::getByCondition(array('OrderID'=>$result['referenceNo']));
				$orderProductArr = array();
				foreach ($result_orderProduct as $p_key => $p_value) {
					$orderProductArr[$p_value['product_sku']] = $p_value;
				}
				 
				//循环对比哪个sku缺货，或全部缺货
				foreach ($result['OOS'] as $stock_key => $stock_value) {
					if(empty($sys_tips) && $stock_key == 'A'){ //全部缺货
						$sys_tips = 'all_stock';
						//             			break;
					}else if(empty($sys_tips) && $stock_key == 'B'){//部分缺货
						$sys_tips = 'part_stock';
					}
	
					foreach ($stock_value as $stock_detail_key => $stock_detail_value) {
						$result_orderProductWms = Service_OrderProductToWms::getByCondition(array('ref_id'=>$result['referenceNo'],'warehouse_sku'=>$stock_detail_key));
						 
						if(!empty($result_orderProductWms)){
							$result_orderProductWms = $result_orderProductWms[0];
							Service_OrderProductToWms::update(array('stock_quantity'=>$stock_detail_value), $result_orderProductWms['id']);
	
						}
					}
				}
			}
	
			$update = array(
					'sys_tips'=>$sys_tips,
					'date_release' => $date,
					'date_last_modify' => $date,
					'abnormal_reason'=>'',
					'process_again'=>'1',		//在处理状态为1，不在处理
					'service_status'=> empty($result['spCode'])?'0':'1',
					'service_provider'=>$result['spCode'],
					'refrence_no_warehouse' => isset($result['orderCode']) ? $result['orderCode'] : ''
			);
	
			$log = array(
					'ref_id' => $result['referenceNo'],
					'create_time' => $date,
					'log_content' => 'FBA订单，同步到仓库成功，返回仓库单号:' . (isset($result['orderCode']) ? $result['orderCode'] : ''). $quehuo,
					'data'=>'所有返回参数为：'.print_r($result,true)
			);
	
		} else {
			$response_error = true;
			$abnormlReason = '';
			if(is_array($result['message'])) {
				foreach($result['message'] as $m){
					$abnormlReason.=$m.";";
				}
			}else{
				$abnormlReason.=print_r($result['message'],true);
			}
			$orderStatus = 7;
			$update = array(
					'date_last_modify' => $date,
					'abnormal_type'=>'4',					//异常原因类型
					'abnormal_reason'=>$abnormlReason,		//异常原因
					'process_again'=>'3'					//新的在处理状态
			);
			$log = array(
					'ref_id' => $result['referenceNo'],
					'create_time' => $date,
					'log_content' => "FBA订单，同步到仓库失败，原因:".print_r($result['message'],true),
			);
			
			$return['message'] = isset($return['message']) ? $result['message'] : '';
			
		}
		
		Service_Orders::update($update, $result['referenceNo'], 'refrence_no_platform');
		if(!$is_log_error && $response_error){
			//不记录订单日志
		}else{
			Service_OrderLog::add($log);
		}
		return $return;
	}
	
	/**
	 * 给FBA订单指定仓库和运输方式
	 * @param unknown_type $order_row
	 */
	private static function orderFbaAllot($order_row){
		$return = array(
					'ask'=>'0',
					'message'=>'',
					'order'=>array()	//返回包含仓库和运输方式的订单信息
				);
		//所有FBA规则设置
		$fba_set = self::getOrderFbaSet();
		
		$order_user_account = $order_row['user_account'];
		$order_id = $order_row['order_id'];
		$order_sn = $order_row['refrence_no_platform'];
		$format = 'Y-m-d H:i:s';
		$is_bol = false;
		foreach ($fba_set as $key_fs => $value_fs) {
			$fs_user_account = $value_fs['user_account'];
			$fs_wh_id = $value_fs['warehouse_id'];
			$fs_sm_code = $value_fs['sm_code'];
			$fs_name = $value_fs['ofs_name'];
			
			if($fs_user_account == $order_user_account){
				//更新订单
				$row_update = array(
							'warehouse_id'=>$fs_wh_id,
							'shipping_method'=>$fs_sm_code,
							'date_last_modify'=>date($format),
							'abnormal_reason'=>'',
						);
				Service_Orders::update($row_update, $order_id);
				
				//插入日志
				$logRow = array(
						'ref_id' => $order_sn,
						'log_content' => '根据FBA订单规则['.$fs_name.']，分配仓库ID：'.$fs_wh_id.'，运输方式：' . $fs_sm_code,
						'op_id' => '9'
				);
				Service_OrderLog::add($logRow);

				$return['ask'] = '1';
				$order_row['warehouse_id'] = $fs_wh_id;
				$order_row['shipping_method'] = $fs_sm_code;
				$return['order'] = $order_row;
				$is_bol = true;
				break;
			}
		}
		
		//未分配成功的订单，记录异常
		if(!$is_bol){
			//更新订单
			$row_update_error = array(
					'abnormal_reason'=>'请设置FBA订单规则，为订单发送至WMS后数据统筹做准备',
					'date_last_modify'=>date($format)
			);
			Service_Orders::update($row_update_error, $order_id);
		}
		
		return $return;
	}
	
	/**
	 * 获得所有的FBA分配设置
	 */
	private static function getOrderFbaSet(){
		$con = array(
					'ofs_status'=>'1',
				);
		$result_ofs = Service_OrderFbaSet::getByCondition($con, '*', 0, 1, "ofs_level desc");
		
		return $result_ofs;
	}
}