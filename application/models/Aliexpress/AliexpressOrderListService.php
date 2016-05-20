<?php
/**
 * 速卖通-查询订单列表服务
 * @author Frank
 * @date 2014-9-18 15:03:10
 */
class Aliexpress_AliexpressOrderListService extends Aliexpress_AliexpressService{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'aliexpress_OrderList_';
	
	/**
	 * 接口返回的每页最大数量
	 * @var unknown_type
	 */
	private static $PAGE_SIZE_MAX = 50;
	
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
	 * Aliexpress 订单列表查询
	 * @see Ec_AutoRun::run()
	 */
	public function loadOrderList($load_company_code,$load_user_account,$load_start_date,$load_end_date){
		$i = 1;
// 		echo $i++ . '、进入服务<br/><br/>';
	
		/*
		 * 1.加载当前同步程序的控制参数
		*/
		$company_code = $load_company_code;
		$user_account = $load_user_account;				//绑定的Aliexpress账户
		$start 		 = $load_start_date;					//开始时间
		$end    	 = $load_end_date;					//结束时间
// 		echo $i++ . "、加载任务参数CompanyAccount：$company_code,UserAccount：$user_account ,start: $start ,end：$end <br/><br/>";
	
		/*
		 * 2.查询Aliexpress授权信息
		*/
// 		echo $i++ . '、查询Aliexpress签名<br/><br/>';
		$con_pu = array(
				'company_code'=>$company_code,
				'user_account'=>$user_account,
				);
		$result_PlatformUser = Service_PlatformUser::getByCondition($con_pu);
// 	        print_r($result_PlatformUser);exit;
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
		 * 4.组织参数,并调用订单列表查询接口
		*/
		$app_key = $result_PlatformUser['app_key'];
		$app_secret = $result_PlatformUser['app_signature'];
		$access_token = $result_PlatformUser['user_token'];
	
		$page = 1;
		$pageSize = self::$PAGE_SIZE_MAX;
		$format = 'm/d/Y H:i:s';
		$request_start = date($format,strtotime($start));
		$request_end = date($format,strtotime($end));
// 		echo $i++ . "、转换参数类型,start: $request_start ,end：$request_end <br/><br/>";
	
		$loop_call = true;
		$loop_tootal_count = 0;
		while($loop_call){
			$params = array(
					'page'=>$page,
					'pageSize'=>$pageSize,
					'createDateStart'=>$request_start,
					'createDateEnd'=>$request_end,
					'access_token'=>$access_token,
			);
				
			$response = null;
			try {
				$response = Aliexpress_AliexpressLib::getListOrdersForArr($app_key, $app_secret, $params);
				// 				print_r($response);exit;
			} catch (Exception $e) {
// 				echo $i++ . "、调用订单接口异常<br/><br/>";
				Ec::showError('参数：' . print_r($params) . ' 异常：' . $e->getMessage(), self::$log_name);
				return array (
						'ask' => '0',
						'message' => $e->getMessage()
				);
			}
				
			//查看接口调用是否成功
			if(isset($response['Status']) && $response['Status']['Code'] == '200' && $response['Responses']['0']['Status']['Code'] == '200'){
				/*
				 * 成功
				* 	a、检查分页是否超过50条（超过需要再次请求）
				* 	b、组织当前参数
				*/
				$curr_total_count = $response['Responses']['0']['Result']['totalItem'];			//总共，可以返回的订单数量
				$curr_order_list = $response['Responses']['0']['Result']['orderList'];			//当前页的订单列信息
				$loop_tootal_count += self::$PAGE_SIZE_MAX;										//累计可返还订单数量
				if($curr_total_count > $loop_tootal_count){
// 					echo $i++ . "、返回数量[$curr_total_count]，超过最大单页数量[$loop_tootal_count]，还将继续调用 <br/><br/>";
					$page++;
					$loop_call = true;		//继续调用
				}else{
// 					echo $i++ . "、返回数量[$curr_total_count]，未超过最大单页数量[$loop_tootal_count]，跳出循环<br/><br/>";
					$loop_call = false;		//不在调用
				}
	
				//封装参数
// 				echo $i++ . "、封装数据<br/><br/>";
				self::convertOrderInfo($user_account, $curr_order_list);
			}else{
				/*
				 * 失败
				* 	记录日志，并返回
				*/
// 				echo $i++ . "、调用接口，返回异常信息，详情：". print_r($response,true) ."<br/><br/>";
				$log_message = print_r($response,true);
				Ec::showError($log_message,self::$log_name);
// 				$this->countLoad($loadId, 3,0);
				return array (
						'ask' => '0',
						'message' => $log_message
				);
			}
		}
	
		/*
		 * 5、检查下载订单数据-->校验重复-->保存-->返回
		*/
// 		echo $i++ . "、检查数据<br/><br/>";
		$addRowNum = 0;
		if(count(self::$aliexpressOrderRow) > 0){
			$model = Service_AliexpressOrderOriginal::getModelInstance();
			$db = $model->getAdapter();
			$db->beginTransaction();
			try {
				foreach (self::$aliexpressOrderRow as $key_o => $value_o) {
					$aliexpress_order_id = $key_o;							//速卖通订单号
					$order_product = $value_o['order_product_list'];		//订单
					unset($value_o['order_product_list']);
					$order = $value_o;										//订单产品
// 										echo $aliexpress_order_id . '<br/><br/>';
					$resultAliexpressOrderOriginal = Service_AliexpressOrderOriginal::getByField($aliexpress_order_id,'order_id');
// 					print_r($resultAliexpressOrderOriginal);exit;
					if(empty($resultAliexpressOrderOriginal)){
						if($order['order_status'] == 'WAIT_SELLER_SEND_GOODS'){
							$order['company_code'] = $company_code;
							$aoo_id = Service_AliexpressOrderOriginal::add($order);
							//删除可能存在的明细
							Service_AliexpressOrderDetail::delete($aliexpress_order_id,'order_id');
							foreach ($order_product as $key_i => $value_i) {
								$value_i['aoo_id'] = $aoo_id;
								Service_AliexpressOrderDetail::add($value_i);
							}
							$addRowNum++;
						}else{
// 							echo $i++ . "、订单号：$aliexpress_order_id ，不是待发货状态【".$order['order_status']."】<br/><br/>";
						}
					}else{
// 						echo $i++ . "、订单号：$aliexpress_order_id ，数据重复<br/><br/>";
					}
				}
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
// 				$this->countLoad($loadId, 3,0);
				$date = date('Y-m-d H:i:s');
				$error_message = "Aliexpress账户：'$user_account',在 '$date'下载订单信息出现异常,错误原因：".$e->getMessage();
// 				echo $i++ . "、$error_message<br/><br/>";
				Ec::showError($error_message, self::$log_name);
				return array('ask'=>'0','message'=>$e->getMessage());
			}
		}else{
// 			echo $i++ . '、无数据需要校验<br/><br/>';
		}
	
		/*
		 * 6.  处理完成，更新数据控制表
		*/
// 		echo $i++ . "、下载Aliexpress订单服务执行完毕,总计插入数据 $addRowNum 条<br/><br/>";
// 		$this->countLoad($loadId, 2, $addRowNum);
		return array(
				'ask' => '1',
				'count' => $addRowNum,
				'message' => "Aliexpress账户：$user_account,已处理: '$start' ~ '$end' 的订单下载任务完成."
		);
	}
	
	/**
	 * 封装Aliexpress返回的订单信息
	 * @param unknown_type $user_account
	 * @param unknown_type $aliexpress_result
	 */
	private static function convertOrderInfo($user_account,$aliexpress_result){
		$date = date('Y-m-d H:i:s');
		$order_list_row = array();
		if(empty($aliexpress_result)){
		    return $order_list_row;
		}
		foreach ($aliexpress_result as $key => $value) {
			$order_row = array(
					'order_id' => 	$value['orderId'],									//速卖通订单号
					'order_status' => $value['orderStatus'],							//订单状态
					'biz_type' => $value['bizType'],									//订单类型
					'user_account'  => $user_account,									//店铺账户
					'frozen_status'  => $value['frozenStatus'],							//冻结状态		
					'issue_status'  => $value['issueStatus'],							//纠纷状态
					
					'buyer_login_id'  => $value['buyerLoginId'],						//买家登陆ID
					'buyer_signer_fullname'  => $value['buyerSignerFullname'],			//买家全名
					'seller_login_id'  => $value['sellerLoginId'],						//卖家登陆ID
					'seller_signer_fullname'  => $value['sellerSignerFullname'],		//卖家全名
					'fund_status'  => $value['fundStatus'],								//资金状态
					'payment_type'  => $value['paymentType'],							//支付类型
					'gmt_pay_time'  => self::convertDateFormat($value['gmtPayTime']),	//支付时间	
														
					'gmt_create' => self::convertDateFormat($value['gmtCreate']),		//订单创建时间
					'sys_creation_date' => $date,										//系统创建时间
					'sys_last_update' => $date,											//系统最后修改时间
					'gmt_send_goods_time' => self::convertDateFormat($value['gmtSendGoodsTime']),		//订单发货时间
					'timeout_left_time' => $value['timeoutLeftTime'],					//超时剩余时间
					'logistics_status' => $value['logisticsStatus'],					//物流状态
					'order_detail_url' => $value['orderDetailUrl'],						//订单详情链接
					'left_send_good_day' => $value['leftSendGoodDay'],					//剩余发货时间（天）, 
					'left_send_good_hour' => $value['leftSendGoodHour'],				//剩余发货时间（小时）, 
					'left_send_good_min' => $value['leftSendGoodMin'],					//剩余发货时间（分钟）, 
					'has_request_loan' => $value['hasRequestLoan'],						
					
					'pay_amount' => $value['payAmount']['amount'],																	//付款金额
					'pay_amount_cent' => $value['payAmount']['cent'],																//付款金额-分
					'pay_amount_cent_factor' => $value['payAmount']['centFactor'],													//支付金额-分的定义(1块除100)
					'pay_amount_currency_code' => $value['payAmount']['currencyCode'],												//支付金额-币种
					'pay_amount_currency_default_fraction_digits' => $value['payAmount']['currency']['defaultFractionDigits'],		//支付金额-货币,小数点位数
					'pay_amount_currency_currency_code' => $value['payAmount']['currency']['currencyCode'],							//支付金额-货币,币种
					'pay_amount_currency_symbol' => $value['payAmount']['currency']['symbol'],								//支付金额-货币,符号
					

					/*
					
					'loan_amount' => $value[''],
					'loan_amount_cent' => $value[''],
					'loan_amount_cent_factor' => $value[''],
					'loan_amount_currency_code' => $value[''],
					'loan_amount_currency_default_fraction_digits' => $value[''],
					'loan_amount_currency_currency_code' => $value[''],
					'loan_amount_currency_symbol' => $value[''],
					
					'gmt_modified' => $value[''],						//订单修改时间
					'gmt_trade_end' => $value[''],						//交易结束时间
					'buyer_last_name' => $value[''],					//买家，名
					'buyer_first_name' => $value[''],					//buyer_first_name
					'buyer_country_code' => $value[''],					//buyer_country_code
					'buyer_email' => $value[''],						//买家，Email
					
					'logistics_amount' => $value[''],					//物流金额
					'logistics_cent' => $value[''],						//物流金额-分
					'logistics_cent_factor' => $value[''],				//物流金额-分的定义(1块除100)
					'logistics_currency_code' => $value[''],			//物流金额-币种
					'logistics_currency_default_fraction_digits' => $value[''],		//物流金额-货币,小数点位数
					'logistics_currency_currency_code' => $value[''],				//物流金额-货币,币种
					'logistics_currency_symbol' => $value[''],						//物流金额-货币,符号

					'logistics_type_code' => $value[''],							//物流公司类型					
					'gmt_received' => $value[''],									//妥投时间
					'receive_status' => $value[''],									//妥投状态
					'logistics_no' => $value[''],									//物流追踪号
					'logistics_service_name' => $value[''],							//发货物流服务key
					'gmt_send' => $value[''],										//发货时间
					
					'order_amount' => $value[''],									//订单金额
					'order_cent' => $value[''],										//订单金额-分
					'order_cent_factor' => $value[''],								//订单金额-分的定义(1块除100)
					'order_currency_code' => $value[''],							//订单金额-币种
					'order_currency_default_fraction_digits' => $value[''],			//订单金额-货币,小数点位数
					'order_currency_currency_code' => $value[''],					//订单金额-货币,币种
					'order_currency_symbol' => $value[''],							//订单金额-货币,符号
					
					'init_oder_amount' => $value[''],								//产品总金额
					'init_oder_cent' => $value[''],									//产品总金额-分
					'init_oder_cent_factor' => $value[''],							//产品总金额-分的定义(1块除100)
					'init_oder_currency_code' => $value[''],						//产品总金额-币种
					'init_oder_currency_default_fraction_digits' => $value[''],		//产品总金额-货币,小数点位数
					'init_oder_currency_currency_code' => $value[''],				//产品总金额-货币,币种
					'init_oder_currency_symbol' => $value[''],						//产品总金额-货币,符号
					
					'refund_info' => $value[''],					//退款信息-print_r()
					'order_msg_list' => $value[''],					//留言-print_r()
					'opr_log_dto_list' => $value[''],				//订单操作日志-print_r()
					'seller_operator_login_id' => $value[''],		//订单负责人登陆ID
					'loan_info_amount' => $value[''],				//放款信息-金额
					'loan_info_time' => $value[''],					//放款信息-时间
					'loan_status' => $value[''],					//放款状态
					'gmt_pay_success' => $value[''],				//支付成功时间(与订单列表中gmtPayTime字段意义相同)
					'seller_operator_aliidloginid' => $value[''],	//卖家子帐号
					'escrow_fee' => $value[''],						//交易佣金
					
					'country_code' => $value[''],					//地址-国家
					'contact_person' => $value[''],					//地址-收件人
					'address' => $value[''],						//地址-地址
					'address2' => $value[''],						//地址-地址2
					'detail_address' => $value[''],					//地址-详细地址
					'province' => $value[''],						//地址-州/省
					'city' => $value[''],							//地址-城市
					'zip' => $value[''],							//地址-邮编
					'mobile_no' => $value[''],						//地址-手机号码
					'phone_country' => $value[''],					//地址-电话国家
					'phone_area' => $value[''],						//地址-电话分区
					'phone_number' => $value[''],					//地址-电话号码
					'fax_country' => $value[''],					//地址-传真国家
					'fax_area' => $value[''],						//地址-传真分区
					'fax_number' => $value[''],						//地址-传真号码
					
					*/
					
					'is_loaded' => '0',						//加载状态--0：下载订单列表，1：下载订单明细，2：生成待审核订单，3：付款未完成，风控等订单（需要继续拉取状态），4：相同订单号，5：不在试运行
			);
			
			foreach ($order_row as $key_o_emp => $value_o_emp) {
				if($value_o_emp == null){
					$order_row[$key_o_emp] = '';
				}
			}
			
			
			$response_products = $value['productList'];
			$product_list_row = array();
			foreach ($response_products as $key_p => $value_p) {
				$product_row = array(
					'aoo_id' => '',
					'child_id' => $value_p['childId'],						//子订单号
					'order_id' => $value_p['orderId'],						//主订单号
					'son_order_status' => $value_p['sonOrderStatus'],		//子订单状态
					'goods_prepare_time' => $value_p['goodsPrepareTime'],	//配货时间		
					'memo' => $value_p['memo'],								//订单备注
					'sku_code' => $value_p['skuCode'],						//SKU
					'product_id' => $value_p['productId'],					//产品ID
					'product_count' => $value_p['productCount'],			//产品数量
					'product_unit' => $value_p['productUnit'],				//产品单位
					'product_img_url' => $value_p['productImgUrl'],			//产品预览图--50*50的小图
					'product_name' => $value_p['productName'],				//产品名称
					'product_standard' => $value_p['productStandard'],		//产品规格高
					'product_snap_url' => $value_p['productSnapUrl'],		//产品镜像链接
					'show_status' => $value_p['showStatus'],				//显示状态
					
					'product_unit_price_amount' => $value_p['productUnitPrice']['amount'],														//产品单价
					'product_unit_price_cent' => $value_p['productUnitPrice']['cent'],															//产品单价-分
					'product_unit_price_cent_factor' => $value_p['productUnitPrice']['centFactor'],												//产品单价-分的定义(1块除100)
					'product_unit_price_currency_code' => $value_p['productUnitPrice']['currencyCode'],											//产品单价-币种
					'product_unit_price_currency_default_fraction_digits' => $value_p['productUnitPrice']['currency']['defaultFractionDigits'],	//产品单价-货币,小数点位数
					'product_unit_price_currency_currency_code' => $value_p['productUnitPrice']['currency']['currencyCode'],					//产品单价-货币,币种
					'product_unit_price_currency_symbol' => $value_p['productUnitPrice']['currency']['symbol'],									//产品单价-货币,符号
					
					'total_product_amount' => $value_p['totalProductAmount']['amount'],															//产品总金额
					'total_product_cent' => $value_p['totalProductAmount']['cent'],																//产品总金额-分
					'total_product_cent_factor' => $value_p['totalProductAmount']['centFactor'],												//产品总金额-分的定义(1块除100)
					'total_product_currency_code' => $value_p['totalProductAmount']['currencyCode'],											//产品总金额-币种
					'total_product_currency_default_fraction_digits' => $value_p['totalProductAmount']['currency']['defaultFractionDigits'],	//产品总金额-货币,小数点位数
					'total_product_currency_currency_code' => $value_p['totalProductAmount']['currency']['currencyCode'],						//产品总金额-货币,币种
					'total_product_currency_symbol' => $value_p['totalProductAmount']['currency']['symbol'],									//产品总金额-货币,符号
					
					'freight_commit_day' => $value_p['freightCommitDay'],					//限时达
					'can_submit_issue' => $value_p['canSubmitIssue'],						//子订单是否能提交纠纷
					'issue_status' => $value_p['issueStatus'],								//纠纷状态
					'issue_mode' => $value_p['issueMode'],									//纠纷类型
					'logistics_type' => $value_p['logisticsType'],							//物流类型
					'logistics_service_name' => $value_p['logisticsServiceName'],			//物流服务
					'money_back_three' => $value_p['moneyBack3x'],							//假一赔三
					'send_goods_time' => $value_p['sendGoodsTime'],							//发货时间
					'delivery_time' => $value_p['deliveryTime'],							//妥投时间
					'fund_status' => $value_p['fundStatus'],								//资金状态
					);
				
				foreach ($product_row as $key_p_emp => $value_p_emp) {
					if($value_p_emp == null){
						$product_row[$key_p_emp] = '';
					}
				}
				
				$product_list_row[] = $product_row;
			}
			
			$order_row['order_product_list'] = $product_list_row;
			$order_id = $order_row['order_id'] . '';
			$order_list_row[$order_id] = $order_row;
			self::$aliexpressOrderRow[$order_id] = $order_row;
		}
// 		print_r($order_list_row);
// 		exit;
		return $order_list_row;
	}
	
}