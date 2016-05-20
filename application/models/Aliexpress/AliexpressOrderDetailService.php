<?php
/**
 * 速卖通-查询订单明细服务
 * @author Frank
 * @date 2014-9-20 15:03:10
 */
class Aliexpress_AliexpressOrderDetailService extends Aliexpress_AliexpressService{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'aliexpress_OrderDetail_';
	
	/**
	 * 每次查询订单明细的上限
	 * @var unknown_type
	 */
	private static $PAGE_SIZE_MAX = 150;
	
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
	 * Aliexpress 订单明细查询
	 * @see Ec_AutoRun::run()
	 */
	public function loadOrderDetail($load_company_code,$load_user_aacount){
		$i = 1;
// 		echo $i++ . '、进入服务<br/><br/>';
		$addRowNum = 0;
		
		/*
		 * 1.加载当前同步程序的控制参数
		*/
		$company_code = $load_company_code;
		$user_account = $load_user_aacount;					//绑定的Aliexpress账户
// 		echo $i++ . "、加载任务参数CompanyAccount：$company_code,UserAccount：$user_account <br/><br/>";
	
		/*
		 * 2.查询Aliexpress授权信息
		*/
// 		echo $i++ . '、查询Aliexpress签名<br/><br/>';
		$con_pu = array(
				'company_code'=>$company_code,
				'user_account'=>$user_account,
				);
		$result_PlatformUser = Service_PlatformUser::getByCondition($con_pu);
	
		if(empty($result_PlatformUser)){
// 			echo $i++ . "、Aliexpress账户：‘$user_account’ 未查询到签名信息<br/><br/>";
			$errorMessage = "Aliexpress账户：$user_account 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$log_name);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}else{
			$result_PlatformUser = $result_PlatformUser[0];
			if($result_PlatformUser['status'] != 1){
// 				echo $i++ . "、Aliexpress账户：‘$user_account’ 未生效<br/><br/>";
				$errorMessage = "Aliexpress账户：$user_account 未生效";
				Ec::showError($errorMessage, self::$log_name);
				return array (
						'ask' => '0',
						'message' => $errorMessage
				);
			}
		}
	
		/*
		 * 3.检查Token是否过期
		* 是：更新，并返回最新授权信息
		* 否：直接返回
		*/
// 		echo $i++ . "、检查Token是否过期 <br/><br/>";
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
		 * 4.查询原始订单表，需要查询明显的订单
		*/
// 		echo $i++ . "、查询未下载订单明细的订单 <br/><br/>";
		$con_load = array(
				'company_code'=>$company_code,
				'user_account'=>$user_account,
				'is_loaded'=>'0'
		);
		$result_AliexpressOrders = Service_AliexpressOrderOriginal::getByCondition($con_load, '*', self::$PAGE_SIZE_MAX, 1, 'aoo_id desc');
	
		if(!empty($result_AliexpressOrders) && count($result_AliexpressOrders) > 0){
			/*
			 * 5.组织参数,并调用订单明细查询接口
			*/
			$app_key = $result_PlatformUser['app_key'];
			$app_secret = $result_PlatformUser['app_signature'];
			$access_token = $result_PlatformUser['user_token'];
				
			foreach ($result_AliexpressOrders as $key_o => $value_o) {
				$aoo_id = $value_o['aoo_id'];
				$orderId = $value_o['order_id'];
				$params = array(
						'orderId'=>$orderId,
						'access_token'=>$access_token,
				);
	
				$response = null;
				try {
					$response = Aliexpress_AliexpressLib::getOrderDetailById($app_key, $app_secret, $params);
				} catch (Exception $e) {
// 					echo $i++ . "、订单明细接口调用异常 <br/><br/>";
					Ec::showError('参数：' . print_r($params) . ' 异常：' . $e->getMessage(), self::$log_name);
				}
	
				//查看接口调用是否成功
				if(isset($response['Status']) && $response['Status']['Code'] == '200' && $response['Responses']['0']['Status']['Code'] == '200'){
					/*
					 * 成功
					* 	封装参数
					*/
					$aliexpress_result = $response['Responses']['0']['Result'];
					//封装参数
// 					echo $i++ . "、封装数据<br/><br/>";
					// 					print_r($aliexpress_result);exit;
					self::convertOrderInfo($aoo_id, $aliexpress_result);
						
				}else{
					/*
					 * 失败
					* 	记录日志，并返回
					*/
// 					echo $i++ . "、调用接口，返回异常信息，详情：". print_r($response,true) ."<br/><br/>";
					$log_message = print_r($response,true);
					Ec::showError($log_message,self::$log_name);
				}
			}
		}else{
// 			echo $i++ . "、没有订单需要下载明细 <br/><br/>";
		}
	
		/*
		 * 5、检查下载订单数据-->校验重复-->保存-->返回
		*/
// 		echo $i++ . "、检查数据<br/><br/>";
		$addRowNum = 0;
		if(count(self::$aliexpressOrderRow) > 0){
			// 			print_r(self::$aliexpressOrderRow);
			foreach (self::$aliexpressOrderRow as $key_u => $value_u) {
				$order_row = $value_u['order'];
				$aoo_id = $order_row['aoo_id'];
				$order_id = $order_row['order_id'];
// 	            print_r($order_row);exit;
				$order_log = $value_u['log'];
				unset($order_row['aoo_id']);
				try {
					Service_AliexpressOrderOriginal::update($order_row, $aoo_id);
					$update_log = array(
							'order_code'=>$order_id,
							'content'=>print_r($order_log,true),
							'add_time'=>date('Y-m-d H:i:s')
					);
					Service_AliexpressOrderLog::add($update_log);
					$addRowNum++;
				} catch (Exception $e) {
					echo $i++ . "、订单数据更新异常 ：".$e->getMessage()."\n".print_r($order_row,true)."\n";
					Ec::showError('订单ID：' . $aoo_id . ' 再更新异常：' . $e->getMessage(), self::$log_name);
				}
			}
				
		}else{
// 			echo $i++ . '、无数据需要校验<br/><br/>';
		}
	
		//生成订单
// 		$obj = new Aliexpress_GenerateSystemOrders();
// 		$obj->callAliexpressOrdersToSysOrder($user_account,$company_code);
	
		/*
		 * 6.  处理完成，更新数据控制表
		*/
// 		echo $i++ . "、更新Aliexpress订单明细服务执行完毕,总计更新数据 $addRowNum 条<br/><br/>";
// 		$this->countLoad($loadId, 2, $addRowNum);
		return array(
				'ask' => '1',
				'count' => $addRowNum,
				'message' => "Aliexpress账户：$user_account,已处理订单更新任务完成."
		);
	
	}
	
	/**
	 * 封装Aliexpress返回的订单信息
	 * @param unknown_type $aoo_id
	 * @param unknown_type $aliexpress_result
	 * @param unknown_type $type	缺省值，可不填，主要用于订单信息的【再拉取】
	 */
	public static function convertOrderInfo($aoo_id, $aliexpress_result , $type = 'detail_load'){
		$is_loaded = 0;
		switch ($type){
			case 'detail_load':
				$is_loaded = 1;
				break;
			case 'detail_load_again':
				$is_loaded = 4;
				break;
			default:
				$is_loaded = 1;
		}
		
		$date = date('Y-m-d H:i:s');
		$value = $aliexpress_result;
// 		print_r($aliexpress_result);exit;
			$order_row = array(
					'aoo_id' => $aoo_id,												//订单主表ID
					'order_id' => $value['id'],											//速卖通订单号
					'order_status' => $value['orderStatus'],							//订单状态
					'frozen_status'  => $value['frozenStatus'],							//冻结状态		
					'issue_status'  => $value['issueStatus'],							//纠纷状态
					'sys_last_update' => $date,											//系统最后修改时间
														
					'logistics_status' => $value['logisticsStatus'],					//物流状态					

					'loan_amount' => $value['loanInfo']['loanAmount']['amount'],																	//放款金额				
					'loan_amount_cent' => $value['loanInfo']['loanAmount']['cent'],																	//放款金额-分
					'loan_amount_cent_factor' => $value['loanInfo']['loanAmount']['centFactor'],													//放款金额-分的定义(1块除100)
					'loan_amount_currency_code' => $value['loanInfo']['loanAmount']['currencyCode'],												//放款金额-币种
					'loan_amount_currency_default_fraction_digits' => $value['loanInfo']['loanAmount']['currency']['defaultFractionDigits'],		//放款金额-货币,小数点位数
					'loan_amount_currency_currency_code' => $value['loanInfo']['loanAmount']['currency']['currencyCode'],							//放款金额-货币,币种
					'loan_amount_currency_symbol' => $value['loanInfo']['loanAmount']['currency']['symbol'],										//放款金额-货币,符号
					'loan_info_time' => $value['loanInfo']['loanTime'],										//放款信息-时间
// 					'loan_info_amount' => $value[''],														//放款信息-金额
					'loan_status' => $value['loanStatus'],													//放款状态
					
					'gmt_modified' => self::convertDateFormat($value['gmtModified']),						//订单修改时间
					'gmt_trade_end' => self::convertDateFormat($value['gmtTradeEnd']),						//交易结束时间
					'buyer_last_name' => $value['buyerInfo']['lastName'],									//买家，名
					'buyer_first_name' => $value['buyerInfo']['firstName'],									//买家，姓
					'buyer_country_code' => $value['buyerInfo']['country'],									//买家，国家
					'buyer_email' => $value['buyerInfo']['email'],											//买家，Email
					
					'logistics_amount' => $value['logisticsAmount']['amount'],															//物流金额
					'logistics_cent' => $value['logisticsAmount']['cent'],																//物流金额-分
					'logistics_cent_factor' => $value['logisticsAmount']['centFactor'],													//物流金额-分的定义(1块除100)
					'logistics_currency_code' => $value['logisticsAmount']['currencyCode'],												//物流金额-币种
					'logistics_currency_default_fraction_digits' => $value['logisticsAmount']['currency']['defaultFractionDigits'],		//物流金额-货币,小数点位数
					'logistics_currency_currency_code' => $value['logisticsAmount']['currency']['currencyCode'],						//物流金额-货币,币种
					'logistics_currency_symbol' => $value['logisticsAmount']['currency']['symbol'],										//物流金额-货币,符号

					'logistics_type_code' => $value['logisticInfoList'][0]['logisticsTypeCode'],										//物流公司类型					
					'gmt_received' => $value['logisticInfoList'][0]['gmtReceived'],														//妥投时间
					'receive_status' => $value['logisticInfoList'][0]['receiveStatus'],													//妥投状态
					'logistics_no' => $value['logisticInfoList'][0]['logisticsNo'],														//物流追踪号
					'logistics_service_name' => $value['logisticInfoList'][0]['logisticsServiceName'],									//发货物流服务key
					'gmt_send' => self::convertDateFormat($value['logisticInfoList'][0]['gmtSend']),									//发货时间
					
					'order_amount' => $value['orderAmount']['amount'],																	//订单金额
					'order_cent' => $value['orderAmount']['cent'],																		//订单金额-分
					'order_cent_factor' => $value['orderAmount']['centFactor'],															//订单金额-分的定义(1块除100)
					'order_currency_code' => $value['orderAmount']['currencyCode'],														//订单金额-币种
					'order_currency_default_fraction_digits' => $value['orderAmount']['currency']['defaultFractionDigits'],				//订单金额-货币,小数点位数
					'order_currency_currency_code' => $value['orderAmount']['currency']['currencyCode'],								//订单金额-货币,币种
					'order_currency_symbol' => $value['orderAmount']['currency']['symbol'],												//订单金额-货币,符号
					
					'init_oder_amount' => $value['initOderAmount']['amount'],															//产品总金额
					'init_oder_cent' => $value['initOderAmount']['cent'],																//产品总金额-分
					'init_oder_cent_factor' => $value['initOderAmount']['centFactor'],													//产品总金额-分的定义(1块除100)
					'init_oder_currency_code' => $value['initOderAmount']['currencyCode'],												//产品总金额-币种
					'init_oder_currency_default_fraction_digits' => $value['initOderAmount']['currency']['defaultFractionDigits'],		//产品总金额-货币,小数点位数
					'init_oder_currency_currency_code' => $value['initOderAmount']['currency']['currencyCode'],							//产品总金额-货币,币种
					'init_oder_currency_symbol' => $value['initOderAmount']['currency']['symbol'],										//产品总金额-货币,符号
					
					'refund_info' => print_r($value['refundInfo'],true),					//退款信息-print_r()
					'order_msg_list' => print_r($value['orderMsgList'],true),					//留言-print_r()
					'opr_log_dto_list' => print_r($value['oprLogDtoList'],true),				//订单操作日志-print_r()
					'seller_operator_login_id' => $value['sellerOperatorLoginId'],		//订单负责人登陆ID					
					
					
					'gmt_pay_success' => self::convertDateFormat($value['gmtPaySuccess']),			//支付成功时间(与订单列表中gmtPayTime字段意义相同)
					'seller_operator_aliidloginid' => $value['sellerOperatorAliidloginid'],			//卖家子帐号
					'escrow_fee' => $value['escrowFee'],											//交易佣金
					
					'country_code' => $value['receiptAddress']['country'],					//地址-国家
					'contact_person' => $value['receiptAddress']['contactPerson'],			//地址-收件人
					'address' => $value['receiptAddress']['address'],						//地址-地址
					'address2' => $value['receiptAddress']['address2'],						//地址-地址2
					'detail_address' => $value['receiptAddress']['detailAddress'],			//地址-详细地址
					'province' => $value['receiptAddress']['province'],						//地址-州/省
					'city' => $value['receiptAddress']['city'],								//地址-城市
					'zip' => $value['receiptAddress']['zip'],								//地址-邮编
					'mobile_no' => $value['receiptAddress']['mobileNo'],					//地址-手机号码
					'phone_country' => $value['receiptAddress']['phoneCountry'],			//地址-电话国家
					'phone_area' => $value['receiptAddress']['phoneArea'],					//地址-电话分区
					'phone_number' => $value['receiptAddress']['phoneNumber'],				//地址-电话号码
					'fax_country' => $value['receiptAddress']['faxCountry'],				//地址-传真国家
					'fax_area' => $value['receiptAddress']['faxArea'],						//地址-传真分区
					'fax_number' => $value['receiptAddress']['faxNumber'],					//地址-传真号码
					
					'is_loaded' => $is_loaded,			//加载状态--0：下载订单列表，1：下载订单明细，2：生成待审核订单，3：付款未完成，风控等订单（需要继续拉取状态），4：相同订单号，5：不在试运行
			);
			
		foreach ($order_row as $key_o_emp => $value_o_emp) {
			if($value_o_emp == null){
				$order_row[$key_o_emp] = '';
			}
		}
		$order_id = $order_row['order_id'] . '';
		self::$aliexpressOrderRow[$order_id]['order'] = $order_row;
		self::$aliexpressOrderRow[$order_id]['log'] = $value;
// 		print_r($order_row);exit;
		return $order_row;
	}
	
}