<?php
/**
 * 生成Aliexpress系统订单
 * @author Frank
 * @date 2014-09-22 12:00:24
 */
class Aliexpress_GenerateSystemOrders{
	/**
	 * 日志文件名--生成订单逻辑
	 * @var unknown_type
	 */
	private static $log_name = 'AliexpressGenerateSystemOrders_';
	
	/**
	 * 日志文件名--调用接口
	 * @var unknown_type
	 */
	private static $log_name_interface = 'AliexpressGenerateSystemOrdersInterface_';
	
	/**
	 * 每次查询订单明细的上限
	 * @var unknown_type
	 */
	private static $PAGE_SIZE_MAX = 100;
	
	/**
	 * 判断更新订单时的查询上限
	 * @var unknown_type
	 */
	private static $PAGE_SIZE_AGAIN_MAX = 500;
	
	/**
	 * 速卖通订单生成系统订单
	 */
	public function callAliexpressOrdersToSysOrder($user_account,$company_code){
		$i = 1;
		$addRowNum = 0;
// 		echo $i++ . '、进入[生成Aliexpress订单]服务<br/><br/>';
		/*
		 * 1、查询下载完毕的速卖通订单
		 */
		$con_AliexpressOrderOriginal = array(
					'is_loaded'=>1,      
				);
		if(!empty($user_account)){
			$con_AliexpressOrderOriginal['user_account'] = $user_account;
		}
		if(!empty($company_code)){
			$con_AliexpressOrderOriginal['company_code'] = $company_code;
		}
		$resultAliexpressOrderOriginal = Service_AliexpressOrderOriginal::getByCondition($con_AliexpressOrderOriginal, '*', self::$PAGE_SIZE_MAX, 1, "sys_last_update asc");
// 		print_r($resultAliexpressOrderOriginal);exit;
		/*
		 * 2、订单不为空，查询ALiexpress的Items信息
		 */
		if(!empty($resultAliexpressOrderOriginal)){
// 			echo $i++ . '、存在原始订单信息，准备开始生成订单<br/><br/>';
			$addRow = array();
			foreach ($resultAliexpressOrderOriginal as $aliexpressOrdersKey => $aliexpressOrdersValue) {
				try {
					$aoo_id = $aliexpressOrdersValue['aoo_id'];
					$aliexpress_id = $aliexpressOrdersValue['order_id'];
					//检查是否有上传过手工订单
					$resultCheck = Service_Orders::getByField($aliexpress_id,'refrence_no');
					//存在客户参考号与ALiexpress订单号一致时，修改状态为存在相同订单，进入下一次循环
					if(!empty($resultCheck)){
						if($resultCheck['refrence_no'] == $resultCheck['refrence_no_platform']){
// 							echo $i++ . '、存在相同订单号--并且订单号和参考号一致，'.$resultCheck['refrence_no_platform'].'<br/><br/>';
							switch ($resultCheck['order_status']){
								case '1':
									Service_AliexpressOrderOriginal::update(array('is_loaded'=>'3'), $aoo_id,'aoo_id');
									break;
								case '0':
									Service_AliexpressOrderOriginal::update(array('is_loaded'=>'7'), $aoo_id,'aoo_id');
									break;
								default:
									Service_AliexpressOrderOriginal::update(array('is_loaded'=>'5'), $aoo_id,'aoo_id');
									break;
							}
						}else{
// 							echo $i++ . '、存在相同订单号，'.$resultCheck['refrence_no_platform'].'<br/><br/>';
							Service_AliexpressOrderOriginal::update(array('is_loaded'=>'5'), $aoo_id,'aoo_id');
						}
						continue;
					}
					
					$row = array();
					$con_AliexpressOrderDetail = array(
								'aoo_id'=>$aliexpressOrdersValue['aoo_id']
							);
					$result_ALiexpressOrderDetail = Service_AliexpressOrderDetail::getByCondition($con_AliexpressOrderDetail);
					
					//封装--单头
					$response_order = $this->convertOrders($aliexpressOrdersValue, $result_ALiexpressOrderDetail);
// 					print_r($response_order);
					
					//封装--单身
					$payDate = $aliexpressOrdersValue['gmt_pay_time'];
					$order_product = $this->convertOrderProduct($result_ALiexpressOrderDetail, $aliexpress_id, $payDate);
// 					print_r($order_product);
					
					//封装--地址
					$shipping_address = $this->convertOrderAddress($aliexpressOrdersValue, $aliexpress_id);
// 					print_r($shipping_address);
					
					/** TODO 试运行检查，后期MAX调整【2014-12-10 14:04:39】
					 //检查试运行
					 foreach ($order_product as $val_key => $val_value) {
					 	
					 $exist = Common_AllowSKU::validateSku($val_value['product_sku'],$aliexpressOrdersValue['company_code'],$aliexpressOrdersValue['user_account']);
					 if(!$exist){
					 //更新订单信息，不在进行生成订单
					 Service_AliexpressOrderOriginal::update(array('is_loaded'=>'6'), $aliexpress_id,'order_id');
					 //跳出循环
					 throw new Exception("SKU:[{$val_value['product_sku']}] 不存在于试运行的SKU列表");
					 echo $i++ . '、不在试运行：'.$aliexpress_id.'<br/><br/>';
					 }
					 }
					 **/
					$row['order'] = $response_order;
					$row['orderProduct'] = $order_product;
					$row['shippingAddress'] = $shipping_address;
					$addRow[] = $row;
// 					print_r($row);
				} catch (Exception $e) {
					echo $e->getMessage();
					$date = date('Y-m-d H:i:s');
					Ec::showError("时间：'$date'封装参数，信息异常：" . $e->getMessage(), self::$log_name);
				}
			}
			
			if(count($addRow > 0)){
				$model = Service_Orders::getModelInstance();
				$db = $model->getAdapter();
				$db->beginTransaction();
				
				try {
					foreach ($addRow as $addKey => $addValue) {
						$refrence_no_platform = $addValue['order']['order_no'];
						$addOrderRow = $addValue['order']['data'];
						$is_loaded = $addValue['order']['is_loaded'];
						if(empty($addOrderRow)){
							Service_AliexpressOrderOriginal::update(array('is_loaded'=>$is_loaded), $refrence_no_platform,'order_id');
							continue;
						}
					
						$addOrderProductRow = $addValue['orderProduct'];
						$addShippingAddress = $addValue['shippingAddress'];
					
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
								Service_AliexpressOrderOriginal::update(array('is_loaded'=>$is_loaded), $refrence_no_platform,'order_id');
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
								foreach ($addOrderProductRow as $productKey => $productValue) {
									$productValue['order_id'] = $order_id;
// 									print_r($productValue);
									Service_OrderProduct::add($productValue);
								}
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：生成订单明细", self::$log_name);
								throw new Exception($e->getMessage());
							}
								
							try {
						
								//插入shipping_address
								$shippingAddress_id = Service_ShippingAddress::add($addShippingAddress);
								$model->update(array('shipping_address_id'=>$shippingAddress_id), $order_id);
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：生成订单地址，并更新订单表地址ID", self::$log_name);
								throw new Exception($e->getMessage());
							}
								
							// 							echo 'amazonOrderId: ' . $refrence_no_platform;
							/**  TODO 注释产品关系，后期MAX调整【2014-12-10 14:04:39】
							try {
						
								//更新产品对应关系
								Service_OrderForWarehouseProcessNew::updateOrderProductWarehouseSku($refrence_no_platform);
							} catch (Exception $e) {
								$date = date('Y-m-d H:i:s');
								Ec::showError("时间：'$date'生成系统订单出现异常,发生点：更新产品对应关系", self::$log_name);
								throw new Exception($e->getMessage());
							}
							**/
							
							//插入日志
							$logRow = array(
									'ref_id' => $refrence_no_platform,
									'log_content' => 'LOAD订单，生成Aliexpress订单',
									'op_id' => '9'
							);
							Service_OrderLog::add($logRow);
// 							echo $i++ . '、生成系统订单：'.$refrence_no_platform.'<br/><br/>';
							
							$addRowNum++;
						}
					}
					$db->commit();
				} catch (Exception $e) {
					$db->rollBack();
					$date = date('Y-m-d H:i:s');
					$gso_error = "时间：'$date'生成系统订单出现异常,错误原因：".$e->getMessage();
					Ec::showError($gso_error, self::$log_name);
// 					echo $i++ . '、'.$gso_error.'<br/><br/>';
					return array('ask'=>'0','message'=>$e->getMessage());
				}
			}
		}else{
// 			echo $i++ . '、无订单需要生成<br/><br/>';
		}
		
		return array(
				'ask' => '1',
				'count' => $addRowNum,
				'message' => "Aliexpress账户：$user_account,已处理订单生成任务完成."
		);
	}
	
	/**
	 * 封装原始订单信息为，系统标准单头
	 * @param unknown_type $aliexpressOrderOriginal
	 * @param unknown_type $aliexpressOrderDetail
	 */
	public function convertOrders($aliexpressOrderOriginal, $aliexpressOrderDetail){
		$return = array(
					'order_no'=>'',
					'data'=>'',
					'is_loaded'=>'',
				);
		$row = array();
		$is_loaded = 2;
		if(!empty($aliexpressOrderOriginal) && !empty($aliexpressOrderDetail)){
			$row['platform'] = 						'aliexpress';
			$row['data_source'] = 					'aliexpress';
			$row['order_type'] = 					'sale';
			$row['create_type'] = 					'api';
			
			//已发货--订单状态
			$shiped_status = array('SELLER_PART_SEND_GOODS','WAIT_BUYER_ACCEPT_GOODS');
			//当前订单状态
			$curr_order_status = $aliexpressOrderOriginal['order_status'];
			//当前订单付款状态
			$curr_fund_status = $aliexpressOrderOriginal['fund_status'];
			if(in_array($curr_order_status, $shiped_status)){
				//已发货和已结束并付款成功的订单，直接转为【已发货】
				$row['order_status'] = 					'4';
				$row['sync_status'] = 					'1';
				$is_loaded = 2;
			}else if($curr_order_status == 'FINISH'){
// 				//已结束，未付款，【废弃】
// 				$row['order_status'] = 					'0';
// 				$row['sync_status'] = 					'0';
// 				$is_loaded = 2;
				//已结束，未付款和付款成功的订单【废弃】
				if($curr_fund_status == 'NOT_PAY'){
					$row['abnormal_reason'] = '已结束，未付款 [订单废弃]';
				}else if($curr_fund_status == 'PAY_SUCCESS'){
					$row['abnormal_reason'] = '已结束，付款成功 [风控未通过，订单废弃]';
				}else{
					$row['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [订单废弃]';
				}
				$row['order_status'] = 	'0';
				$row['sync_status'] = 	'0';
				$is_loaded = 2;
				
			}else if($curr_order_status == 'WAIT_SELLER_SEND_GOODS'){
				//等待卖家发货，直接转为【待发货审核】
				$row['order_status'] = 					'2';
				$row['sync_status'] = 					'0';
				$is_loaded = 2;
			}else if($curr_order_status == 'PLACE_ORDER_SUCCESS'){
				//未付款订单，【付款未完成】
				$row['order_status'] = 					'1';
				$row['sync_status'] = 					'0';
				$row['process_again'] = 				'3';	//等待付款，还需定时检查订单付款状态
				$is_loaded = 3;
			}else if($curr_order_status == 'RISK_CONTROL'){
				//风控订单，【付款未完成】
				$row['order_status'] = 					'1';
				$row['sync_status'] = 					'0';
				$row['process_again'] = 				'4';	//已付款，需等到订单状态变为：等待卖家发货
				$is_loaded = 3;
			}else if($curr_order_status == 'IN_CANCEL'){
				//申请取消，【不生成订单】，只修改原始表状态
				$is_loaded = 3;									
				$return['is_loaded'] = $is_loaded;
				$return['order_no'] = $aliexpressOrderOriginal['order_id'];
				return $return;
			}else if($curr_order_status == 'FUND_PROCESSING'){
				//退款，直接生成【作废订单】
				$row['order_status'] = 					'0';
				$row['sync_status'] = 					'0';
				$is_loaded = 7;				//不在处理订单
			}
// 			$row['sync_time'] = 					'';
			
			$row['create_method'] = 				'2';
// 			$row['customer_id'] = 					'';
			$row['company_code'] = 					$aliexpressOrderOriginal['company_code'];
			$row['shipping_method'] = 				'';
			$row['shipping_method_platform'] = 		'';
			$row['warehouse_id'] = 					'';
			$row['order_desc'] = 					'';
			$row['date_create'] = 					date('Y-m-d H:i:s');
			$row['date_release'] = 					'';
			$row['date_warehouse_shipping'] = 		'';
			$row['date_last_modify'] = 				date('Y-m-d H:i:s');
			$row['operator_id'] = 					'';
			$row['refrence_no'] = 					$aliexpressOrderOriginal['order_id'];
			$row['refrence_no_platform'] = 			$aliexpressOrderOriginal['order_id'];
			$row['refrence_no_sys'] = 				'';
			$row['shipping_address_id'] = 			'';
			$row['refrence_no_warehouse'] = 		'';
			$row['shipping_method_no'] = 			'';
			$row['date_create_platform'] = 			$aliexpressOrderOriginal['gmt_create'];
			$row['date_paid_platform'] = 			$aliexpressOrderOriginal['gmt_pay_time'];
			$row['date_paid_int'] = 				'';
				
			$totalAmount = 0;
			$totalShippingAmount = $aliexpressOrderOriginal['logistics_amount'];
			$totalQty = 0;
			$is_one_piece = '1';
			$buyer_note = '';
			$shipping_method_platform = '';
			foreach ($aliexpressOrderDetail as $itemsKey => $itemsValue) {
				//产品数量相加
				$totalQty += $itemsValue['product_count'];
				$buyer_note .= (!empty($itemsValue['memo']))?$itemsValue['memo'] . ' ':'';
				$shipping_method_platform = $itemsValue['logistics_type'];
			}
			if($totalQty > 1){
				$is_one_piece = '0';
			}
			$row['shipping_method_platform'] = 		$shipping_method_platform;														//平台运输方式
			$row['amountpaid'] = 					$aliexpressOrderOriginal['pay_amount'];											//包运费（使用实际付款金额字段）
			$row['subtotal'] = 						round(($aliexpressOrderOriginal['pay_amount'] - $totalShippingAmount),3);		//交易额（不包运费）
			$row['ship_fee'] = 						$totalShippingAmount;				//运费
				
			$row['finalvaluefee'] = 				round($aliexpressOrderOriginal['escrow_fee'] + ($aliexpressOrderOriginal['pay_amount'] * Aliexpress_AliexpressLib::$Transaction_Fees),3);		//交易费									//平台费用
			$row['currency'] = 						$aliexpressOrderOriginal['order_currency_code'];
			$row['user_account'] = 					$aliexpressOrderOriginal['user_account'];
			$row['buyer_id'] = 						preg_replace('/\s+/','',strtolower($aliexpressOrderOriginal['buyer_login_id']));//amazon订单，无买家id，以收件人姓名为准
			$row['third_part_ship'] = 				'0';								//是否第三方仓库发货，默认0，审核订单的时候改变
			$row['is_merge'] = 						'0';
			$row['site'] = 							'';									//速卖通没有平台概念
			$row['consignee_country'] = 			$aliexpressOrderOriginal['country_code'];
			$row['order_weight'] = 					'';
			$row['abnormal_type'] = 				'';
			$row['abnormal_reason'] = 				'';
			$row['buyer_name'] = 					$aliexpressOrderOriginal['buyer_signer_fullname'];
			$row['buyer_mail'] = 					$aliexpressOrderOriginal['buyer_email'];
			$row['has_buyer_note'] = 				'';
				
			$row['is_one_piece'] = 					$is_one_piece;			//判断一下items
			$row['operator_note'] = 				'';
			$row['product_count'] = 				$totalQty;				//产品数量
			$row['order_desc']	= 					$buyer_note;			//买家留言
		}
		
		$return['data'] = $row;
		$return['is_loaded'] = $is_loaded;
		$return['order_no'] = $aliexpressOrderOriginal['order_id'];
		return $return;
	}
	
	/**
	 * 封装原始订单信息为，标准单身
	 * @param unknown_type $aliexpressOrderDetail
	 * @param unknown_type $aliexpress_id
	 * @param unknown_type $payDate
	 */
	public function convertOrderProduct($aliexpressOrderDetail ,$aliexpress_id , $payDate){
		$row = array();
		if(!empty($aliexpressOrderDetail)){
			foreach ($aliexpressOrderDetail as $aliexpressOrderDetailKey => $aliexpressOrderDetailValue) {
				$product = array();
				$product['order_id'] = 			'';
				$product['product_id'] = 		'0';
				$product['product_sku'] = 		$aliexpressOrderDetailValue['sku_code'];
				$product['warehouse_sku'] = 	'';
				$product['product_title'] = 	$aliexpressOrderDetailValue['product_name'];
				$product['op_quantity'] = 		$aliexpressOrderDetailValue['product_count'];
				$product['op_ref_tnx'] = 		'';
				$product['op_recv_account'] = 	'';
				$product['op_ref_item_id'] = 	$aliexpressOrderDetailValue['product_id'];
				$product['op_site'] = 			'';
				$product['op_record_id'] = 		'';
				$product['op_ref_buyer_id'] = 	'';
				$product['op_ref_paydate'] = 	$payDate;
				$product['op_add_time'] = 		date('Y-m-d H:i:s');
				$product['op_update_time'] = 	date('Y-m-d H:i:s');
				$product['OrderID'] = 			$aliexpress_id;
				$product['OrderIDEbay'] = 		$aliexpress_id;
				$product['is_modify'] = 		'0';
				$product['pic'] = 				$aliexpressOrderDetailValue['product_img_url'];
				$product['url'] = 				$aliexpressOrderDetailValue['product_snap_url'];
				$product['unit_price'] = 		$aliexpressOrderDetailValue['product_unit_price_amount'];
				$product['give_up'] = 			0;   //是否废弃，item数量为0时，设置为1：表示该item不发货
				$product['currency_code'] = 	$aliexpressOrderDetailValue['product_unit_price_currency_code'];
				$row[] = $product;
			}
		}
		return $row;
	}
	
	/**
	 * 分钟原始订单信息为，地址
	 * @param unknown_type $aliexpressOrderOriginal
	 */
	public function convertOrderAddress($aliexpressOrderOriginal, $aliexpress_id){
		$row = array();
		if(!empty($aliexpressOrderOriginal)){
				
			$row['Name'] = 				$aliexpressOrderOriginal['contact_person'];
			$row['Street1'] = 			$aliexpressOrderOriginal['detail_address'];
			$address2 = $aliexpressOrderOriginal['address2'];
			$address = $aliexpressOrderOriginal['address'];
			$row['Street2'] = 			((!empty($address2))?$address2:'') . ' ' . ((!empty($address))?$address:'');
			//$row['Street3'] = 			$amazonOrderOriginal['shipping_address_address3'];
			$row['CityName'] = 			$aliexpressOrderOriginal['city'];
			$row['StateOrProvince'] = 	$aliexpressOrderOriginal['province'];
			$row['Country'] = 			$aliexpressOrderOriginal['country_code'];
			$resultCountry = Service_Country::getByField($aliexpressOrderOriginal['country_code'],'country_code');
			$row['CountryName'] = 		$resultCountry['country_name_en'];
			$row['District'] =  		'';
			
			$phone = '';
			if(!empty($aliexpressOrderOriginal['mobile_no'])){
				$phone = $aliexpressOrderOriginal['mobile_no'];
			}else{
				$phone = $aliexpressOrderOriginal['phone_country'] . '-' . $aliexpressOrderOriginal['phone_area'] . '-' . $aliexpressOrderOriginal['phone_number']; 
			}
			$row['Phone'] = 			$phone;
			$row['PostalCode'] = 		$aliexpressOrderOriginal['zip'];
			$row['AddressID'] = 		'';
			$row['AddressOwner'] = 		'';
			$row['ExternalAddressID'] = '';
			$row['OrderID'] = 			$aliexpress_id;
			$row['Plat_code'] = 		'aliexpress';
			$row['company_code'] = 		$aliexpressOrderOriginal['company_code'];
			$row['create_date_sys'] = 	date('Y-m-d H:i:s');
			$row['modify_date_sys'] = 	date('Y-m-d H:i:s');
			$row['user_account'] = 		$aliexpressOrderOriginal['user_account'];
			$row['is_modify'] = 		'0';
		}
		return $row;
	}
}