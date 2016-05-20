<?php
/**
 * 速卖通-订单标记发货服务
 * @author Frank
 * @date 2014-9-26 15:03:10
 */
class Aliexpress_AliexpressOrderShipmentService extends Aliexpress_AliexpressService{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'aliexpress_Order_Shipment';
	
	/**
	 * 每次查询订单的上限
	 * @var unknown_type
	 */
	private static $PAGE_SIZE_MAX = 30;
	
	/**
	 * 速卖通订单
	 */
	private static $aliexpressOrderRow = array();
	
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(0);
	}
	
	/**
	 * 在AutoRun调用中被调用的方法，自动同步程序的入口
	 * @param unknown_type $loadId
	 */
	public function callOrderShipment($loadId){
		return $this->runOrderShipment($loadId);
	}
	
	/**
	 * Aliexpress 订单标记发货
	 * @see Ec_AutoRun::run()
	 */
	public function runOrderShipment($loadId){
		$i = 1;
		echo $i++ . '、进入服务<br/><br/>';
		
		/*
		 * 1.加载当前同步程序的控制参数
		*/
		$param 		 = $this->getLoadParam($loadId);
		$user_account = $param["user_account"];					//绑定的Aliexpress账户
		$start 		 = $param["load_start_time"];				//开始时间
		$end    	 = $param["load_end_time"];					//结束时间
		$count 		 = $param["currt_run_count"];				//当前运行第几页
		echo $i++ . "、加载任务参数,UserAccount：$user_account ,start: $start ,end：$end <br/><br/>";
		
		/*
		 * 2.查询Aliexpress授权信息
		*/
		echo $i++ . '、查询Aliexpress签名<br/><br/>';
		$result_PlatformUser = Service_PlatformUser::getByField($user_account,'user_account');
		
		if(empty($result_PlatformUser)){
			echo $i++ . "、Aliexpress账户：‘$user_account’ 未查询到签名信息<br/><br/>";
			$errorMessage = "Aliexpress账户：$user_account 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$log_name);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}else if($result_PlatformUser['status'] != 1){
			echo $i++ . "、Aliexpress账户：‘$user_account’ 未生效<br/><br/>";
			$errorMessage = "Aliexpress账户：$user_account 未生效";
			Ec::showError($errorMessage, self::$log_name);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}
		
		/*
		 * 3.检查Token是否过期
		 * 是：更新，并返回最新授权信息
		 * 否：直接返回
		 */
		echo $i++ . "、检查Token是否过期 <br/><br/>";
		try {
			$result_PlatformUser = self::checkAliexpressToken($result_PlatformUser['pu_id']);
		} catch (Exception $e) {
			//记录Token检查是否异常
			Ec::showError($e->getMessage(), self::$log_name);
			return array (
					'ask' => '0',
					'message' => $e->getMessage()
			);
		}
		
		/*
		 * 4.查询订单表，需要查询明显的订单
		 */
		echo $i++ . "、查询需要标记发货的订单 <br/><br/>";
		$con_shipment = array(
				'platform'=>'aliexpress',
				'order_type'=>'sale',
				'create_type'=>'api',
				'order_status'=>'3',
				'csd_order_status'=>'C',	// 只回写出库的订单
				'sync_status_arr'=>array('0','3'),
				'user_account'=>$user_account
				);
		$resultOrders = Service_Orders::getByConditionJoinCsdOrder($con_shipment, '*', self::$PAGE_SIZE_MAX, 1, "date_create_platform asc");
// 		print_r($resultOrders);
		echo '<br/><br/>';
		if(count($resultOrders) == 0){
			echo $i++ . "、没有符合的订单，返回 <br/><br/>";
			$this->countLoad($loadId, 2,0);
			return array(
					'ask' => '1',
					'message' => "Aliexpress账户：$user_account,在: '$start' ~ '$end' 内没有数据需要标记发货"
			);
		}
		
		/*
		 * 5.整理发货数据
		 */
		echo $i++ . "、整理发货数据<br/><br/>";
		if(!empty($resultOrders) && count($resultOrders) > 0){
			foreach ($resultOrders as $key_bz => $value_bz) {
				$refrence_no_platform = $value_bz['refrence_no'];				//订单号
				$is_merge = $value_bz['is_merge'];								//订单操作类型，0：未操作，1：合并订单，2：被合并订单，3：被拆分订单，4：拆分订单
				try {
					$shipment_type = ''; 					//发货接口
					$order_shipping_row = array(
								'ref_code'=>$refrence_no_platform,				//订单号
								'aliexpress_id' => '',							//速卖通订单号
								'service_name' => (!empty($value_bz['carrier_name'])?$value_bz['carrier_name']:$value_bz['shipping_method_platform']),//承运商
								'tracking_no' => $value_bz['shipping_method_no'],				//跟踪号
								'tracking_website' => '',						//当service_name=other的情况时，需要填写对应的追踪网址
								'send_type' => '',								//发货类型，ALL：全部发货，PART：部分发货
								'flag'=>'0',									//跟踪号类型，标志，0：正常跟踪号标记，1：模拟跟踪号
							);
					$old_shipping_row = array();								//上一次的发货记录
					switch ($is_merge){
						case '0'://未做操作
							echo $i++ . "、未做操作：$refrence_no_platform <br/><br/>";
							$result_aliexpress_order = Service_AliexpressOrderOriginal::getByField($refrence_no_platform,'order_id');
							if(!empty($result_aliexpress_order)){
								//查询订单标记发货次数,最后标记的记录，排在最前面
								$con_shipment_list = array(
												'aliexpress_id'=>$refrence_no_platform,
												'sync_status'=>'SC200_RSC200_RRS1',		//同步成功的标志
										);
								$result_shipment_list = Service_AliexpressShipmentList::getByCondition($con_shipment_list, '*', 0, 1, "asl_id desc");
								$old_shipping_row = $result_shipment_list[0];
								if(!empty($result_shipment_list)){
									$shipment_type = 'sellerModifiedShipment';
									$shipment_qty = count($result_shipment_list);
									if($shipment_qty >= 3){
										//插入异常日志
										throw new Exception('Aliexpress订单标记次数超过3次，已不能再次标记');
									}
								}else{
									$shipment_type = 'sellerShipment';
								}
								$order_shipping_row['send_type'] = 'ALL';
								$order_shipping_row['aliexpress_id'] = $refrence_no_platform;
								
								//Key使用速卖通订单号
								self::$aliexpressOrderRow[$order_shipping_row['ref_code']] = array(
													'type'=>$shipment_type,
													'shipment_row'=>$order_shipping_row,
													'old_shipment_row'=>$old_shipping_row,
										);
							}else{
								throw new Exception('未能找到原始订单信息，单号：' . $refrence_no_platform);
							}
							
							break;
						case '1'://合并订单
							echo $i++ . "、合并的：$refrence_no_platform <br/><br/>";
							$con_merge = array(
									'ref_no_platform'=>$refrence_no_platform,
								);
							$merge_result = Service_OrderMergeMap::getByCondition($con_merge);
							throw new Exception('合并订单暂不能标记发货，单号：' . $refrence_no_platform);
							break;
						case '4'://拆分订单
							echo $i++ . "、拆分的：$refrence_no_platform <br/><br/>";
							$con_split = array(
										'OrderID'=>$refrence_no_platform,
										'give_up'=>'0',
										'create_type'=>'api',
									);
							$result_split = Service_OrderProduct::getByCondition($con_split);
							try {
								if(empty($result_split)){
									Ec::showError('拆分订单，没有需要标记发货的订单明细[ErrorCode:001]，单号：' . $refrence_no_platform,self::$log_name);
									throw new Exception('拆分订单，没有需要标记的信息[ErrorCode:001]，不再进行标记发货');								
								}else{
									$order_tags = array();
									foreach ($result_split as $key_rs => $value_rs) {
										if(!empty($value_rs['op_ref_item_id']) && !empty($value_rs['OrderIDEbay'])){
											//使用原始单号为Key，防止重复标记
											$order_tags[$value_rs['OrderIDEbay']] = $refrence_no_platform;
										}
									}
								
									if(empty($order_tags)){
										Ec::showError('拆分订单，没有需要标记发货的订单明细[ErrorCode:002]，单号：' . $refrence_no_platform,self::$log_name);
										throw new Exception('拆分订单，没有需要标记的信息[ErrorCode:002]，不再进行标记发货');
									}else{
										foreach ($order_tags as $key_tags => $value_tags) {
											//查询订单标记发货次数,最后标记的记录，排在最前面
											$con_shipment_list = array(
													'aliexpress_id'=>$key_tags,
													'sync_status'=>'SC200_RSC200_RRS1',		//同步成功的标志
											);
											$result_shipment_list = Service_AliexpressShipmentList::getByCondition($con_shipment_list, '*', 0, 1, "asl_id desc");
											$old_shipping_row = $result_shipment_list[0];
											if(!empty($result_shipment_list)){
												$shipment_type = 'sellerModifiedShipment';
												$shipment_qty = count($result_shipment_list);
												if($shipment_qty >= 1){
													//插入异常日志													
													throw new Exception('Aliexpress拆分订单已标记过一次，已不能再次标记');
												}
											}else{
												$shipment_type = 'sellerShipment';
											}
											
											$order_shipping_row['send_type'] = 'ALL';
											$order_shipping_row['aliexpress_id'] = $key_tags;
											
											//Key使用速卖通的订单号
											self::$aliexpressOrderRow[$order_shipping_row['ref_code']] = array(
													'type'=>$shipment_type,
													'shipment_row'=>$order_shipping_row,
													'old_shipment_row'=>$old_shipping_row,
											);
										}
									}
									break;
								}
							} catch (Exception $e) {
								//记录异常原因，更新订单同步状态
								$orders_update_split = array(
										'sync_status'=>'2',
										'sync_time'=>date($format),
								);
								
								$order_log_add_split = array(
										'ref_id' => $refrence_no_platform,
										'log_content' => $e->getMessage(),
										'op_id' => '9'
								);
								Service_Orders::update($orders_update_split, $refrence_no_platform,'refrence_no');
								Service_OrderLog::add($order_log_add_split);
								break;
							}
							
							throw new Exception('拆分订单暂不能标记发货，单号：' . $refrence_no_platform);
							break;
						default:
							throw new Exception('未匹配的订单类型，单号：' . $refrence_no_platform);
							break;
					}
					
				} catch (Exception $e) {
					echo $i++ . "、封装订单标记发货信息出错：" . $e->getMessage();
					Ec::showError("封装订单标记发货信息出错：" . $e->getMessage(),self::$log_name);
				}
				
			}
// 			print_r(self::$aliexpressOrderRow);
// 			return;
// 			exit;
			/*
			 * 调用对应接口，进行标记发货
			*/
			$addRowNum = 0;
			echo $i++ . "、开始标记发货 <br/><br/>";
			if(!empty(self::$aliexpressOrderRow) && count(self::$aliexpressOrderRow) > 0){
				print_r(self::$aliexpressOrderRow);
				echo '<br/><br/>';
				foreach (self::$aliexpressOrderRow as $key_s => $value_s) {
					$model = Service_Orders::getModelInstance();
					$db = $model->getAdapter();
					$db->beginTransaction();
					try {
						$orders_refrence_no_platform = $value_s['shipment_row']['ref_code'];
						$type = $value_s['type'];
						$shipment_row = $value_s['shipment_row'];
						$old_shipment_row = $value_s['old_shipment_row'];
						$format = 'Y-m-d H:i:s';
						
						$app_key = $result_PlatformUser['app_key'];
						$app_secret = $result_PlatformUser['app_signature'];
						$access_token = $result_PlatformUser['user_token'];
						$orderId = $shipment_row['aliexpress_id'];
						$trackNo = $shipment_row['tracking_no'];
						$serviceName = $shipment_row['service_name'];
						$trackingWebsite = $shipment_row['tracking_website'];
						$sendType = $shipment_row['send_type'];
						$sendType = strtolower($sendType);			//发货状态，转小写(速卖通只支持小写，WFK！)
						
						$response_shipment_call = null;
						switch ($type){
							case 'sellerShipment':		//首次标记发货
								echo $i++ . "、首次标记发货 <br/><br/>";
								$params = array(
						    			'outRef'=>$orderId,						//【必填】速卖通订单号
								    	'serviceName'=>$serviceName,			//【必填】用户选择的实际发货物流服务（物流服务key：该接口根据api.listLogisticsService列出平台所支持的物流服务 进行获取目前所支持的物流。）
								    	'logisticsNo'=>$trackNo,				//【必填】物流追踪号
										'description'=>'',						//备注(只能输入英文，且长度限制在512个字符。）
										'sendType'=>$sendType,					//【必填】状态包括：全部发货(all)、部分发货(part)
										'trackingWebsite'=>$trackingWebsite,	//当serviceName=other的情况时，需要填写对应的追踪网址
										'access_token'=>$access_token,			//【必填】Token
								);
								print_r($params);
								echo '<br/><br/>';
								
								$orders_update_row = array();
								$response_shipment_call = Aliexpress_AliexpressLib::sellerShipment($app_key, $app_secret, $params);
								print_r($response_shipment_call);
								echo '<br/><br/>';
								$order_log_content = 'Aliexpress订单标记发货成功，跟踪号：' . $trackNo;
								break;
							case 'sellerModifiedShipment':		//修改标记发货
								echo $i++ . "、修改标记发货 <br/><br/>";
								$oldServiceName = $old_shipment_row['service_name'];
								$oldLogisticsNo = $old_shipment_row['tracking_no'];
								$params = array(
										'outRef'=>$orderId,						//【必填】速卖通订单号
										'oldServiceName'=>$oldServiceName,		//【必填】OLD用户选择的实际发货物流服务
										'oldLogisticsNo'=>$oldLogisticsNo,		//【必填】OLD物流追踪号
										'newServiceName'=>$serviceName,			//【必填】NEW用户选择的实际发货物流服务
										'newLogisticsNo'=>$trackNo,				//【必填】NEW物流追踪号
										'description'=>'',						//备注(只能输入英文，且长度限制在512个字符。）
										'sendType'=>$sendType,					//【必填】状态包括：全部发货(all)、部分发货(part)
										'trackingWebsite'=>$trackingWebsite,	//当serviceName=other的情况时，需要填写对应的追踪网址
										'access_token'=>$access_token,			//【必填】Token
								);
								print_r($params);
								echo '<br/><br/>';
								
								$orders_update_row = array();
								$response_shipment_call = Aliexpress_AliexpressLib::sellerModifiedShipment($app_key, $app_secret, $params);
								print_r($response_shipment_call);
								echo '<br/><br/>';
								$order_log_content = 'Aliexpress订单修改标记发货成功，New跟踪号：' . $trackNo . ' Old跟踪号：' . $oldLogisticsNo;
								break;
							default:
								throw new Exception('未定义的标记发货方式：' . print_r($value_s));
								break;
						}
						
						if(!empty($response_shipment_call) && $response_shipment_call['Status']['Code'] == '200' 
								&&  $response_shipment_call['Responses'][0]['Status']['Code'] == '200' 
								&&  $response_shipment_call['Responses'][0]['Result']['success'] == '1'){
							echo $i++ . "、标记发货成功 <br/><br/>";
							//成功--记录日志
							$logRow = array(
									'ref_id' => $orders_refrence_no_platform,
									'log_content' => $order_log_content,
									'data' => print_r($response_shipment_call,true),
									'op_id' => '9'
							);
							Service_OrderLog::add($logRow);
							$orders_update_row = array(
									'sync_status'=>'1',
									'platform_ship_status'=>'1',
									'sync_time'=>date($format),
							);
							$shipment_row['sys_create_date'] = date($format);
							$shipment_row['sync_message'] = print_r($response_shipment_call,true);
							$shipment_row['sync_status'] = 'SC' . $response_shipment_call['Status']['Code'] . 
															'_RSC' . $response_shipment_call['Responses'][0]['Status']['Code'] . 
															'_RRS' . $response_shipment_call['Responses'][0]['Result']['success'];
							Service_AliexpressShipmentList::add($shipment_row);
							$addRowNum++;
						}else{
							echo $i++ . "、标记发货失败 <br/><br/>";
							//失败
							$orders_update_row = array(
									'sync_status'=>'2',
									'sync_time'=>date($format),
							);
							Ec::showError("API标记发货失败，参数：" . print_r($params,true) . '返回：' . print_r($response_shipment_call,true),self::$log_name);
							
							$shipment_row['sys_create_date'] = date($format);
							$shipment_row['sync_message'] = print_r($response_shipment_call,true);
							$shipment_row['sync_status'] = 'SC' . $response_shipment_call['Status']['Code'] .
														   '_RSC' . $response_shipment_call['Responses'][0]['Status']['Code'] .
														   '_RRS' . $response_shipment_call['Responses'][0]['Result']['success'];
							Service_AliexpressShipmentList::add($shipment_row);
						}
						Service_Orders::update($orders_update_row, $orders_refrence_no_platform,'refrence_no');
						
						$db->commit();
					} catch (Exception $e) {
						$db->rollBack();
						echo $i++ . "、标记发货异常：" . print_r($e->getMessage(),true) . '<br/><br/>';
						Ec::showError("标记发货异常：" . $e->getMessage(),self::$log_name);
					}
				}
			}
			
			/*
			 * 6.  处理完成，更新数据控制表
			*/
			echo $i++ . "、Aliexpress标记发货服务执行完毕,总计 $addRowNum 条数据进行发货<br/><br/>";
			$this->countLoad($loadId, 2, $addRowNum);
			return array(
					'ask' => '1',
					'message' => "Aliexpress账户：$user_account,已处理: '$start' ~ '$end' 的订单标发发货任务完成."
			);
		}else{
			echo $i++ . "、无数据需要标记发货<br/><br/>";
		}
		
		echo $i++ . "、Aliexpress标记发货服务执行完毕,无数据需要进行发货<br/><br/>";
		$this->countLoad($loadId, 2, 0);
		return array(
				'ask' => '1',
				'message' => "Aliexpress账户：$user_account,已处理: '$start' ~ '$end' 的订单标发发货任务完成."
		);
		
	}
	
	
}