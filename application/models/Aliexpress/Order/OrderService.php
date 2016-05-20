<?php
/**
 * 速卖通-查询订单列表服务
 * @author Max
 */
class Aliexpress_Order_OrderService extends Aliexpress_Order_Common {
	/**
	 * 日志文件名
	 *
	 * @var unknown_type
	 */
	protected $log_name = 'aliexpress_OrderList_';
	protected $_pageSize = 50;
	protected $_orderArr = array ();
	protected $_orderOrgArr = array ();
	
	/**
	 * 速卖通订单
	 */
	protected $aliexpressOrderRow = array ();
	
	/**
	 * 构造器
	 */
	public function __construct() {
		set_time_limit ( 0 );
	}
	
	/**
	 * Aliexpress 订单列表查询
	 *
	 * @see Ec_AutoRun::run()
	 */
	public function runOrderListQuery($loadId) {
	}
	/**
	 * Aliexpress 订单列表查询
	 * *totalItem:订单总数
	 * *orderList:订单列表
	 * *loanAmount:贷款金额 amount:订单总计金额
	 * cent:分
	 * currencyCode:货币代码
	 * centFactor:分的定义,1块除100
	 * currency:货币 defaultFractionDigits:小数位数
	 * symbol:货币符号
	 * childID:子订单ID
	 * orderId:订单号
	 * gmtCreate:订单创建
	 * orderDetailUrl:订单详情链接
	 * buyerLoginId:买家登录ID
	 * sellerLoginId:卖家登录ID
	 * buyerSignerFullname:买家全名
	 * sellerSignerFullname:卖家全名
	 * timeoutLeftTime:超时剩余时间
	 * payAmount:支付金额()loanAmount:放款金额
	 * orderStatus:订单状态
	 * bizType:订单类型（ae_commonAE普通订单、SAFEPAY 批发或者sourcing小单 、QC 验货 、SC_SHIP 海运 ）gmtPayTime:支付时间（和订单详情中gmtPaysuccess字段意义相同。)
	 * paymentType:支付类型
	 * fundStatus:资金状态
	 * logisticsStatus:物流状态
	 * issueStatus:纠纷状态
	 * frozenStatus:冻结状态
	 * gmtSendGoodsTime:发货时间
	 * productList:子订单列表
	 * leftSendGoodDay:剩余发货时间（天）
	 * leftSendGoodHour:剩余发货时间（小时）
	 * leftSendGoodMin:剩余发货时间（分钟）
	 * productName:产品名称
	 * productSnapUrl:产品镜像链接
	 * productImgUrl:产品图片链接
	 * productStandard:产品规格
	 * productCount:产品数量
	 * productUnit:产品单位
	 * productUnitPrice:产品单位价格
	 * sonOrderStatus:子订单状态
	 * totalProductAmount:总产品数量
	 * childId:子订单ID
	 * productId:产品ID
	 * buyerSignerFirstName:买家FirstName
	 * buyerSignerLastName:买家LastName
	 * sellerSignerFirstName:卖家FirstName
	 * sellerSignerLastName:卖家LastName
	 * logisticsType:物流类型
	 * logisticsServiceName:物流服务
	 * logisticsAmount:物流金额
	 * goodsPrepareTime:备货时间
	 * deliveryTime:妥投时间
	 * sendGoodsTime:发货时间
	 * skuCode:sku码
	 * issueMode:纠纷类型
	 * memo:订单备注
	 * freightCommitDay:限时达
	 * MoneyBack3x:假一赔三
	 * isCanSubmitIssue:子订单是否能提交纠纷。
	 */
	public function orderListQuery($start, $end) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$user_account = $this->_user_account;
			$company_code = $this->_company_code;
			
			/*
			 * 3.检查Token是否过期 是：更新，并返回最新授权信息 否：直接返回
			 */
			$this->checkAliexpressToken ();
			
			$platform_user = $this->getPlatformUser ();
			
			// print_r($platform_user);exit;
			/*
			 * 4.组织参数,并调用订单列表查询接口
			 */
			$app_key = $platform_user ['app_key'];
			$app_secret = $platform_user ['app_signature'];
			$access_token = $platform_user ['user_token'];
			
			$pageSize = $this->_pageSize;
			$format = 'm/d/Y H:i:s';
			$request_start = date ( $format, strtotime ( $start ) );
			$request_end = date ( $format, strtotime ( $end ) );
			Common_ApiProcess::log ( "转换参数类型,start: $request_start ,end：$request_end" );
			
			$page = 0;
			$loop_call = true;
			$loop_tootal_count = 0;
			while ( true ) {
				$page ++;
				$params = array (
						'page' => $page,
						'pageSize' => $pageSize,
						'createDateStart' => $request_start,
						'createDateEnd' => $request_end,
						'access_token' => $access_token 
				);
				$response = Aliexpress_AliexpressLib::getListOrdersForArr ( $app_key, $app_secret, $params );
				$response_org = $response;
				Ec::showError ( print_r ( $response, true ), '_aliexpress_order' );
				// print_r ( $response );
				// exit ();
				// 查看接口调用是否成功
				if (isset ( $response ['Status'] ) && $response ['Status'] ['Code'] == '200') {
					//正常
				} else {
					throw new Exception ( "调用接口，返回异常信息，详情：" . $response['Responses'][0]['error_message']);
				}
				$response = $response ['Responses'];
				$response = array_pop ( $response );
				
				if ($response ['Status'] ['Code'] != '200') {
					throw new Exception ( "调用接口，返回异常信息，详情：" . $response['Responses'][0]['error_message']);
				}
				$result = $response ['Result'];
				$totalItem = $result ['totalItem'];
				$orderList = $result ['orderList']?$result ['orderList']:array();
				// file_put_contents(APPLICATION_PATH.'/../data/log/________________.txt', print_r($orderList,true));exit;
				// 数据保存
				foreach ( $orderList as $order ) {
					$this->_orderOrgArr [] = $order;
					$this->_saveOrderInfo ( $order );
				}
				if (($page - 1) * $pageSize + count ( $orderList ) >= $totalItem) {
					break;
				}
			}
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
			
			$return ['orderArr'] = $this->_orderArr;
			// $return ['orderOrgArr'] = $this->_orderOrgArr;
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
	/**
	 * 订单明细
	 * *id:主订单id
	 * *gmtCreate:交易创建时间
	 * *gmtModified:修改订单时间
	 * orderStatus:订单状态
	 * sellerOperatorLoginId:负责人loginId
	 * paymentType:付款方式 (migs信用卡支付走人民币渠道； migs102MasterCard支付并且走人民币渠道； migs101Visa支付并且走人民币渠道； pp101 PayPal； mb MoneyBooker渠道； tt101 Bank Transfer支付； wu101 West Union支付； wp101 Visa走美金渠道的支付； wp102 Mastercard 走美金渠道的支付； qw101 QIWI支付； cybs101 Visa走CYBS渠道的支付； cybs102 Mastercard 走CYBS渠道的支付； wm101
	 * WebMoney支付； ebanx101 巴西Beloto支付 ；)
	 * initOderAmount:产品总金额
	 * gmtPaySuccess:支付成功时间(与订单列表中gmtPayTime字段意义相同)
	 * orderAmount:订单金额
	 * logisticsAmount:物流金额
	 * escrowFee:交易佣金
	 * fundStatus: 资金状态(NOT_PAY,未付款； PAY_SUCCESS,付款成功； WAIT_SELLER_CHECK，卖家验款) logisticsStatus:物流状态"WAIT_SELLER_SEND_GOODS"等待卖家发货;"SELLER_SEND_PART_GOODS"卖家部分发货；"SELLER_SEND_GOODS"卖家已发货；"BUYER_ACCEPT_GOODS"买家已确认收货；"NO_LOGISTICS"没有物流流转信息)
	 * issueStatus:纠纷状态("NO_ISSUE"无纠纷；"IN_ISSUE"纠纷中；“END_ISSUE”纠纷结束。)
	 * frozenStatus:冻结状态("NO_FROZEN"无冻结；"IN_FROZEN"冻结中；)
	 * gmtTradeEnd:交易结束时间
	 * buyerloginid: 买家帐号
	 * buyerSignerFullname:买家全名
	 * sellerSignerFullname: 卖家名称
	 * sellerOperatorAliidloginid:卖家子帐号
	 * receiptAddress:收货地址
	 * contactPerson：收件人全名
	 * loanStatus:放款状态
	 * buyerInfo:买家信息
	 * issueInfo:纠纷信息
	 * refundInfo:退款信息(退款原因, 退款状态,(WAIT_REFUND("wait_refund"),等待退款 REFUND_OK("refund_ok"),退款成功 REFUND_FROZEN("refund_frozen");退款冻结) ) loanInfo:放款信息("loan_none"无放款；"wait_loan"等待放款；"loan_ok"放款成功)
	 * logisticInfoList:物流信息([ { "logisticsTypeCode": "物流公司类型", "gmtReceived": " 妥投时间", "logisticsNo": "物流追踪号", "logisticsServiceName": "发货物流服务key", "gmtSend": "发货时间", "receiveStatus": " 妥投状态 " } ])
	 * childOrderList:子订单信息
	 * orderMsgList:留言信息
	 * ChildOrderExtInfoList:子订单产品信息。
	 * oprLogDtoList":订单操作日志 [ { "id": 操作日志ID, "gmtModified": "修改时间", "gmtCreate": "创建时间", "actionType": "操作类型", "childOrderId": 子订单ID, "operator": "操作者", "orderId": 订单ID }
	 *
	 * @param unknown_type $order_id        	
	 * @throws Exception
	 * @return multitype:number string mixed NULL
	 */
	public function orderDetailQuery($order_id) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$user_account = $this->_user_account;
			$company_code = $this->_company_code;
			/*
			 * 检查Token是否过期
			 */
			$this->checkAliexpressToken ();
			
			$platform_user = $this->getPlatformUser ();
			/*
			 * 4.组织参数,并调用订单列表查询接口
			 */
			$app_key = $platform_user ['app_key'];
			$app_secret = $platform_user ['app_signature'];
			$access_token = $platform_user ['user_token'];
			
			$params = array (
					'orderId' => $order_id,
					'access_token' => $access_token 
			);
			$response = Aliexpress_AliexpressLib::getOrderDetailById ( $app_key, $app_secret, $params );
			
			$response_org = $response;
			Ec::showError ( print_r ( $response, true ), '_aliexpress_order_detail' );
			
			// 查看接口调用是否成功
			if (isset ( $response ['Status'] ) && $response ['Status'] ['Code'] == '200') {
				//=============
			} else {
				throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response, true ) );
			}
			$response = $response ['Responses'];
			$response = array_pop ( $response );
			
			if ($response ['Status'] ['Code'] != '200') {
				throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response_org, true ) );
			}
			$orderDetailOrg = $response['Result'];
			//数据保存
			$orderDetail = $this->_saveOrderDetail($orderDetailOrg);
			
			$return ['ask'] = 1;
			$return ['message'] = 'Success';

			$return ['response'] = $response_org;
			$return ['orderDetailOrg'] = $orderDetailOrg; 
			$return ['orderDetail'] = $orderDetail; 
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
	/**
	 * 收件人地址
	 * *zip: 邮编
	 * *address2: 地址2
	 * *detailAddress: 详细地址
	 * country: 国家
	 * city: 城市
	 * phoneNumber: 电话号码
	 * province: 州
	 * phoneArea: 电话区号
	 * phoneCountry: 国家区号
	 * contactPerson: 收件人
	 * mobileNo: 手机号
	 * 
	 * @param unknown_type $order_id        	
	 * @throws Exception
	 * @return multitype:number string mixed NULL
	 */
	public function getOrderReceiptInfoById($order_id) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$user_account = $this->_user_account;
			$company_code = $this->_company_code;
			/*
			 * 检查Token是否过期
			 */
			$this->checkAliexpressToken ();
			
			$platform_user = $this->getPlatformUser ();
			/*
			 * 4.组织参数,并调用订单列表查询接口
			 */
			$app_key = $platform_user ['app_key'];
			$app_secret = $platform_user ['app_signature'];
			$access_token = $platform_user ['user_token'];
			
			$params = array (
					'orderId' => $order_id,
					'access_token' => $access_token 
			);
			$response = Aliexpress_AliexpressLib::getOrderReceiptInfoById ( $app_key, $app_secret, $params );
			
			$response_org = $response;
			Ec::showError ( print_r ( $response, true ), '_aliexpress_order_detail' );
// 			print_r ( $response );
// 			exit ();
			// 查看接口调用是否成功
			if (isset ( $response ['Status'] ) && $response ['Status'] ['Code'] == '200') {
			} else {
				throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response, true ) );
			}
			$response = $response ['Responses'];
			$response = array_pop ( $response );
			
			if ($response ['Status'] ['Code'] != '200') {
				throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response_org, true ) );
			}
			
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
			
			$return ['response'] = $response;
			// $return ['orderOrgArr'] = $this->_orderOrgArr;
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}

	public function completeSale($order_id) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.'
		);
		try {
			$user_account = $this->_user_account;
			$company_code = $this->_company_code;
			/*
			 * 检查Token是否过期
			*/
			$this->checkAliexpressToken ();
				
			$platform_user = $this->getPlatformUser ();
			/*
			 * 4.组织参数,并调用订单列表查询接口
			*/
			$app_key = $platform_user ['app_key'];
			$app_secret = $platform_user ['app_signature'];
			$access_token = $platform_user ['user_token'];
				
			$params = array (
					'orderId' => $order_id,
					'access_token' => $access_token
			); 
			//====================首次标记发货
			$params = array(
					'outRef'=>$orderId,						//【必填】速卖通订单号
					'serviceName'=>$serviceName,			//【必填】用户选择的实际发货物流服务（物流服务key：该接口根据api.listLogisticsService列出平台所支持的物流服务 进行获取目前所支持的物流。）
					'logisticsNo'=>$trackNo,				//【必填】物流追踪号
					'description'=>'',						//备注(只能输入英文，且长度限制在512个字符。）
					'sendType'=>$sendType,					//【必填】状态包括：全部发货(all)、部分发货(part)
					'trackingWebsite'=>$trackingWebsite,	//当serviceName=other的情况时，需要填写对应的追踪网址
					'access_token'=>$access_token,			//【必填】Token
			);
			$response = Aliexpress_AliexpressLib::sellerShipment($app_key, $app_secret, $params);
			//修改发货标记，最多两次，且需要在首次标记发货之后的5日内
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
			$response = Aliexpress_AliexpressLib::sellerModifiedShipment($app_key, $app_secret, $params);
				
			$response_org = $response;
			Ec::showError ( print_r ( $response, true ), '_aliexpress_order_ship' );
			print_r ( $response );
			exit ();
			// 查看接口调用是否成功
			if (isset ( $response ['Status'] ) && $response ['Status'] ['Code'] == '200') {
			} else {
				throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response, true ) );
			}
			$response = $response ['Responses'];
			$response = array_pop ( $response );
				
			if ($response ['Status'] ['Code'] != '200') {
				throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response_org, true ) );
			}
				
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
				
			$return ['response'] = $response;
			// $return ['orderOrgArr'] = $this->_orderOrgArr;
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
	
	/**
	 * 封装Aliexpress返回的订单信息
	 *
	 * @param unknown_type $user_account        	
	 * @param unknown_type $aliexpress_result        	
	 */
	protected function _saveOrderInfo($value) { 
			// 添加不存在的字段
			Common_Common::checkTableColumnExist ( 'aliexpress_order_original', 'user_account' );
			Common_Common::checkTableColumnExist ( 'aliexpress_order_original', 'company_code' );
			Common_Common::checkTableColumnExist ( 'aliexpress_order_original', 'create_time_sys' );
			Common_Common::checkTableColumnExist ( 'aliexpress_order_original', 'update_time_sys' );
			
			Common_Common::checkTableColumnExist ( 'aliexpress_order_detail', 'user_account' );
			Common_Common::checkTableColumnExist ( 'aliexpress_order_detail', 'company_code' );
			Common_Common::checkTableColumnExist ( 'aliexpress_order_detail', 'create_time_sys' );
			Common_Common::checkTableColumnExist ( 'aliexpress_order_detail', 'update_time_sys' );
			$date = date ( 'Y-m-d H:i:s' );
			$order_row = array (
					'order_id' => $value ['orderId'], // 速卖通订单号
					'order_status' => $value ['orderStatus'], // 订单状态
					'biz_type' => $value ['bizType'], // 订单类型
					'frozen_status' => $value ['frozenStatus'], // 冻结状态
					'issue_status' => $value ['issueStatus'], // 纠纷状态
			
					'buyer_login_id' => $value ['buyerLoginId'], // 买家登陆ID
					'buyer_signer_fullname' => $value ['buyerSignerFullname'], // 买家全名
					'seller_login_id' => $value ['sellerLoginId'], // 卖家登陆ID
					'seller_signer_fullname' => $value ['sellerSignerFullname'], // 卖家全名
					'fund_status' => $value ['fundStatus'], // 资金状态
					'payment_type' => $value ['paymentType'], // 支付类型
					'gmt_pay_time' => $this->convertDateFormat ( $value ['gmtPayTime'] ), // 支付时间
			
					'gmt_create' => $this->convertDateFormat ( $value ['gmtCreate'] ), // 订单创建时间
// 					'sys_creation_date' => $date, // 系统创建时间
// 					'sys_last_update' => $date, // 系统最后修改时间
					'gmt_send_goods_time' => $this->convertDateFormat ( $value ['gmtSendGoodsTime'] ), // 订单发货时间
					'timeout_left_time' => $value ['timeoutLeftTime'], // 超时剩余时间
					'logistics_status' => $value ['logisticsStatus'], // 物流状态
					'order_detail_url' => $value ['orderDetailUrl'], // 订单详情链接
					'left_send_good_day' => $value ['leftSendGoodDay'], // 剩余发货时间（天）,
					'left_send_good_hour' => $value ['leftSendGoodHour'], // 剩余发货时间（小时）,
					'left_send_good_min' => $value ['leftSendGoodMin'], // 剩余发货时间（分钟）,
					'has_request_loan' => $value ['hasRequestLoan'],
			
					'pay_amount' => $value ['payAmount'] ['amount'], // 付款金额
					'pay_amount_cent' => $value ['payAmount'] ['cent'], // 付款金额-分
					'pay_amount_cent_factor' => $value ['payAmount'] ['centFactor'], // 支付金额-分的定义(1块除100)
					'pay_amount_currency_code' => $value ['payAmount'] ['currencyCode'], // 支付金额-币种
					'pay_amount_currency_default_fraction_digits' => $value ['payAmount'] ['currency'] ['defaultFractionDigits'], // 支付金额-货币,小数点位数
					'pay_amount_currency_currency_code' => $value ['payAmount'] ['currency'] ['currencyCode'], // 支付金额-货币,币种
					'pay_amount_currency_symbol' => $value ['payAmount'] ['currency'] ['symbol'], // 支付金额-货币,符号
			
					// 'loan_amount' => $value[''],
					// 'loan_amount_cent' => $value[''],
					// 'loan_amount_cent_factor' => $value[''],
					// 'loan_amount_currency_code' => $value[''],
					// 'loan_amount_currency_default_fraction_digits' => $value[''],
					// 'loan_amount_currency_currency_code' => $value[''],
					// 'loan_amount_currency_symbol' => $value[''],
					// 'gmt_modified' => $value[''], //订单修改时间
					// 'gmt_trade_end' => $value[''], //交易结束时间
					// 'buyer_last_name' => $value[''], //买家，名
					// 'buyer_first_name' => $value[''], //buyer_first_name
					// 'buyer_country_code' => $value[''], //buyer_country_code
					// 'buyer_email' => $value[''], //买家，Email
					// 'logistics_amount' => $value[''], //物流金额
					// 'logistics_cent' => $value[''], //物流金额-分
					// 'logistics_cent_factor' => $value[''], //物流金额-分的定义(1块除100)
					// 'logistics_currency_code' => $value[''], //物流金额-币种
					// 'logistics_currency_default_fraction_digits' => $value[''], //物流金额-货币,小数点位数
					// 'logistics_currency_currency_code' => $value[''], //物流金额-货币,币种
					// 'logistics_currency_symbol' => $value[''], //物流金额-货币,符号
					// 'logistics_type_code' => $value[''], //物流公司类型
					// 'gmt_received' => $value[''], //妥投时间
					// 'receive_status' => $value[''], //妥投状态
					// 'logistics_no' => $value[''], //物流追踪号
					// 'logistics_service_name' => $value[''], //发货物流服务key
					// 'gmt_send' => $value[''], //发货时间
					// 'order_amount' => $value[''], //订单金额
					// 'order_cent' => $value[''], //订单金额-分
					// 'order_cent_factor' => $value[''], //订单金额-分的定义(1块除100)
					// 'order_currency_code' => $value[''], //订单金额-币种
					// 'order_currency_default_fraction_digits' => $value[''], //订单金额-货币,小数点位数
					// 'order_currency_currency_code' => $value[''], //订单金额-货币,币种
					// 'order_currency_symbol' => $value[''], //订单金额-货币,符号
					// 'init_oder_amount' => $value[''], //产品总金额
					// 'init_oder_cent' => $value[''], //产品总金额-分
					// 'init_oder_cent_factor' => $value[''], //产品总金额-分的定义(1块除100)
					// 'init_oder_currency_code' => $value[''], //产品总金额-币种
					// 'init_oder_currency_default_fraction_digits' => $value[''], //产品总金额-货币,小数点位数
					// 'init_oder_currency_currency_code' => $value[''], //产品总金额-货币,币种
					// 'init_oder_currency_symbol' => $value[''], //产品总金额-货币,符号
					// 'refund_info' => $value[''], //退款信息-print_r()
					// 'order_msg_list' => $value[''], //留言-print_r()
					// 'opr_log_dto_list' => $value[''], //订单操作日志-print_r()
					// 'seller_operator_login_id' => $value[''], //订单负责人登陆ID
					// 'loan_info_amount' => $value[''], //放款信息-金额
					// 'loan_info_time' => $value[''], //放款信息-时间
					// 'loan_status' => $value[''], //放款状态
					// 'gmt_pay_success' => $value[''], //支付成功时间(与订单列表中gmtPayTime字段意义相同)
					// 'seller_operator_aliidloginid' => $value[''], //卖家子帐号
					// 'escrow_fee' => $value[''], //交易佣金
					// 'country_code' => $value[''], //地址-国家
					// 'contact_person' => $value[''], //地址-收件人
					// 'address' => $value[''], //地址-地址
					// 'address2' => $value[''], //地址-地址2
					// 'detail_address' => $value[''], //地址-详细地址
					// 'province' => $value[''], //地址-州/省
					// 'city' => $value[''], //地址-城市
					// 'zip' => $value[''], //地址-邮编
					// 'mobile_no' => $value[''], //地址-手机号码
					// 'phone_country' => $value[''], //地址-电话国家
					// 'phone_area' => $value[''], //地址-电话分区
					// 'phone_number' => $value[''], //地址-电话号码
					// 'fax_country' => $value[''], //地址-传真国家
					// 'fax_area' => $value[''], //地址-传真分区
					// 'fax_number' => $value[''], //地址-传真号码
					// // 加载状态--0：下载订单列表，1：下载订单明细，2：生成待审核订单，3：付款未完成，风控等订单（需要继续拉取状态），4：相同订单号，5：不在试运行
					'is_loaded' => '0',
					'user_account' => $this->_user_account,
					'company_code' => $this->_company_code,
					'create_time_sys' => date ( 'Y-m-d H:i:s' ),
					'update_time_sys' => date ( 'Y-m-d H:i:s' )
			);
			$order_row = Ec_AutoRun::arrayNullToEmptyString ( $order_row );
			foreach ( $order_row as $k => $v ) {
				$order_row [$k] = $v . '';
			}
			$con = array (
					'order_id' => $order_row ['order_id'],
					'user_account' => $this->_user_account, // 店铺账户
					'company_code' => $this->_company_code
			);
			// print_r($con);exit;
			$exist = Service_AliexpressOrderOriginal::getByCondition ( $con );
			$db = Common_Common::getAdapter ();
			// print_r($exist);exit;
			if (! empty ( $exist )) {
				unset ( $order_row ['create_time_sys'] );
				$exist = array_pop ( $exist );
				$aoo_id = $exist ['aoo_id'];
				Service_AliexpressOrderOriginal::update ( $order_row, $aoo_id, 'aoo_id' );
				// 日志
				$diff = array_diff_assoc ( $order_row, $exist );
				unset ( $diff ['update_time_sys'] );
				unset ( $diff ['create_time_sys'] );
				if ($diff) {
					// 日志
					$log = array ();
					foreach ( $diff as $k => $v ) {
						$log [] = $k . ' from ' . $exist [$k] . ' to ' . $v;
					}
					$logRow = array (
							'order_code' => $order_row ['order_id'],
							'content' => implode ( "\n", $log ),
							'add_time' => date ( 'Y-m-d H:i:s' )
					);
					$table = 'aliexpress_order_log';
					$db->insert ( $table, $logRow );
				}
			} else {
				$aoo_id = Service_AliexpressOrderOriginal::add ( $order_row );
				Common_ApiProcess::log('aoo_id:'.$aoo_id);
			}
			$order_row['aoo_id'] = $aoo_id;
			//cao==============================
			Service_AliexpressOrderDetail::delete ( $aoo_id, 'aoo_id' );
			$response_products = $value ['productList'];
			$product_list_row = array ();
			foreach ( $response_products as $key_p => $product ) {
				$product_row = array (
						'aoo_id' => $aoo_id,
						'child_id' => $product ['childId'], // 子订单号
						'order_id' => $product ['orderId'], // 主订单号
						'son_order_status' => $product ['sonOrderStatus'], // 子订单状态
						'goods_prepare_time' => $product ['goodsPrepareTime'], // 配货时间
						'memo' => $product ['memo'], // 订单备注
						'sku_code' => $product ['skuCode'], // SKU
						'product_id' => $product ['productId'], // 产品ID
						'product_count' => $product ['productCount'], // 产品数量
						'product_unit' => $product ['productUnit'], // 产品单位
						'product_img_url' => $product ['productImgUrl'], // 产品预览图--50*50的小图
						'product_name' => $product ['productName'], // 产品名称
						'product_standard' => $product ['productStandard'], // 产品规格高
						'product_snap_url' => $product ['productSnapUrl'], // 产品镜像链接
						'show_status' => $product ['showStatus'], // 显示状态
							
						'product_unit_price_amount' => $product ['productUnitPrice'] ['amount'], // 产品单价
						'product_unit_price_cent' => $product ['productUnitPrice'] ['cent'], // 产品单价-分
						'product_unit_price_cent_factor' => $product ['productUnitPrice'] ['centFactor'], // 产品单价-分的定义(1块除100)
						'product_unit_price_currency_code' => $product ['productUnitPrice'] ['currencyCode'], // 产品单价-币种
						'product_unit_price_currency_default_fraction_digits' => $product ['productUnitPrice'] ['currency'] ['defaultFractionDigits'], // 产品单价-货币,小数点位数
						'product_unit_price_currency_currency_code' => $product ['productUnitPrice'] ['currency'] ['currencyCode'], // 产品单价-货币,币种
						'product_unit_price_currency_symbol' => $product ['productUnitPrice'] ['currency'] ['symbol'], // 产品单价-货币,符号
							
						'total_product_amount' => $product ['totalProductAmount'] ['amount'], // 产品总金额
						'total_product_cent' => $product ['totalProductAmount'] ['cent'], // 产品总金额-分
						'total_product_cent_factor' => $product ['totalProductAmount'] ['centFactor'], // 产品总金额-分的定义(1块除100)
						'total_product_currency_code' => $product ['totalProductAmount'] ['currencyCode'], // 产品总金额-币种
						'total_product_currency_default_fraction_digits' => $product ['totalProductAmount'] ['currency'] ['defaultFractionDigits'], // 产品总金额-货币,小数点位数
						'total_product_currency_currency_code' => $product ['totalProductAmount'] ['currency'] ['currencyCode'], // 产品总金额-货币,币种
						'total_product_currency_symbol' => $product ['totalProductAmount'] ['currency'] ['symbol'], // 产品总金额-货币,符号
							
						'freight_commit_day' => $product ['freightCommitDay'], // 限时达
						'can_submit_issue' => $product ['canSubmitIssue'], // 子订单是否能提交纠纷
						'issue_status' => $product ['issueStatus'], // 纠纷状态
						'issue_mode' => $product ['issueMode'], // 纠纷类型
						'logistics_type' => $product ['logisticsType'], // 物流类型
						'logistics_service_name' => $product ['logisticsServiceName'], // 物流服务
						'money_back_three' => $product ['moneyBack3x'], // 假一赔三
						'send_goods_time' => $product ['sendGoodsTime'], // 发货时间
						'delivery_time' => $product ['deliveryTime'], // 妥投时间
						// 资金状态
						'fund_status' => $product ['fundStatus'],
						'user_account' => $this->_user_account,
						'company_code' => $this->_company_code,
						'create_time_sys' => date ( 'Y-m-d H:i:s' ),
						'update_time_sys' => date ( 'Y-m-d H:i:s' )
				);
				$product_row = Ec_AutoRun::arrayNullToEmptyString ( $product_row );
				foreach ( $product_row as $k => $v ) {
					$product_row [$k] = $v . '';
				}
				Service_AliexpressOrderDetail::add ( $product_row );
				$order_row ['order_product'] [] = $product_row;
			}
			$this->_orderArr [] = $order_row; 
		 
		
	}
	
	private function _saveOrderDetail($value){
		$date = date ( 'Y-m-d H:i:s' );
		$row = array (
				// 'aoo_id' => $value['aoo_id'], //订单主表ID
				'order_id' => $value ['id'], // 速卖通订单号
				'order_status' => $value ['orderStatus'], // 订单状态
				'frozen_status' => $value ['frozenStatus'], // 冻结状态
				'issue_status' => $value ['issueStatus'], // 纠纷状态
				'sys_last_update' => $date, // 系统最后修改时间
				
				'logistics_status' => $value ['logisticsStatus'], // 物流状态
				
				'loan_amount' => $value ['loanInfo'] ['loanAmount'] ['amount'], // 放款金额
				'loan_amount_cent' => $value ['loanInfo'] ['loanAmount'] ['cent'], // 放款金额-分
				'loan_amount_cent_factor' => $value ['loanInfo'] ['loanAmount'] ['centFactor'], // 放款金额-分的定义(1块除100)
				'loan_amount_currency_code' => $value ['loanInfo'] ['loanAmount'] ['currencyCode'], // 放款金额-币种
				'loan_amount_currency_default_fraction_digits' => $value ['loanInfo'] ['loanAmount'] ['currency'] ['defaultFractionDigits'], // 放款金额-货币,小数点位数
				'loan_amount_currency_currency_code' => $value ['loanInfo'] ['loanAmount'] ['currency'] ['currencyCode'], // 放款金额-货币,币种
				'loan_amount_currency_symbol' => $value ['loanInfo'] ['loanAmount'] ['currency'] ['symbol'], // 放款金额-货币,符号
				'loan_info_time' => $value ['loanInfo'] ['loanTime'], // 放款信息-时间
				                                                    // 'loan_info_amount'
				                                                    // => $value[''],
				                                                    // //放款信息-金额
				'loan_status' => $value ['loanStatus'], // 放款状态
				
				'gmt_modified' => $this->convertDateFormat ( $value ['gmtModified'] ), // 订单修改时间
				'gmt_trade_end' => $this->convertDateFormat ( $value ['gmtTradeEnd'] ), // 交易结束时间
				'buyer_last_name' => $value ['buyerInfo'] ['lastName'], // 买家，名
				'buyer_first_name' => $value ['buyerInfo'] ['firstName'], // 买家，姓
				'buyer_country_code' => $value ['buyerInfo'] ['country'], // 买家，国家
				'buyer_email' => $value ['buyerInfo'] ['email'], // 买家，Email
				
				'logistics_amount' => $value ['logisticsAmount'] ['amount'], // 物流金额
				'logistics_cent' => $value ['logisticsAmount'] ['cent'], // 物流金额-分
				'logistics_cent_factor' => $value ['logisticsAmount'] ['centFactor'], // 物流金额-分的定义(1块除100)
				'logistics_currency_code' => $value ['logisticsAmount'] ['currencyCode'], // 物流金额-币种
				'logistics_currency_default_fraction_digits' => $value ['logisticsAmount'] ['currency'] ['defaultFractionDigits'], // 物流金额-货币,小数点位数
				'logistics_currency_currency_code' => $value ['logisticsAmount'] ['currency'] ['currencyCode'], // 物流金额-货币,币种
				'logistics_currency_symbol' => $value ['logisticsAmount'] ['currency'] ['symbol'], // 物流金额-货币,符号
				
				'logistics_type_code' => $value ['logisticInfoList'] [0] ['logisticsTypeCode'], // 物流公司类型
				'gmt_received' => $value ['logisticInfoList'] [0] ['gmtReceived'], // 妥投时间
				'receive_status' => $value ['logisticInfoList'] [0] ['receiveStatus'], // 妥投状态
				'logistics_no' => $value ['logisticInfoList'] [0] ['logisticsNo'], // 物流追踪号
				'logistics_service_name' => $value ['logisticInfoList'] [0] ['logisticsServiceName'], // 发货物流服务key
				'gmt_send' => $this->convertDateFormat ( $value ['logisticInfoList'] [0] ['gmtSend'] ), // 发货时间
				
				'order_amount' => $value ['orderAmount'] ['amount'], // 订单金额
				'order_cent' => $value ['orderAmount'] ['cent'], // 订单金额-分
				'order_cent_factor' => $value ['orderAmount'] ['centFactor'], // 订单金额-分的定义(1块除100)
				'order_currency_code' => $value ['orderAmount'] ['currencyCode'], // 订单金额-币种
				'order_currency_default_fraction_digits' => $value ['orderAmount'] ['currency'] ['defaultFractionDigits'], // 订单金额-货币,小数点位数
				'order_currency_currency_code' => $value ['orderAmount'] ['currency'] ['currencyCode'], // 订单金额-货币,币种
				'order_currency_symbol' => $value ['orderAmount'] ['currency'] ['symbol'], // 订单金额-货币,符号
				
				'init_oder_amount' => $value ['initOderAmount'] ['amount'], // 产品总金额
				'init_oder_cent' => $value ['initOderAmount'] ['cent'], // 产品总金额-分
				'init_oder_cent_factor' => $value ['initOderAmount'] ['centFactor'], // 产品总金额-分的定义(1块除100)
				'init_oder_currency_code' => $value ['initOderAmount'] ['currencyCode'], // 产品总金额-币种
				'init_oder_currency_default_fraction_digits' => $value ['initOderAmount'] ['currency'] ['defaultFractionDigits'], // 产品总金额-货币,小数点位数
				'init_oder_currency_currency_code' => $value ['initOderAmount'] ['currency'] ['currencyCode'], // 产品总金额-货币,币种
				'init_oder_currency_symbol' => $value ['initOderAmount'] ['currency'] ['symbol'], // 产品总金额-货币,符号
				
				'refund_info' => print_r ( $value ['refundInfo'], true ), // 退款信息-print_r()
				'order_msg_list' => print_r ( $value ['orderMsgList'], true ), // 留言-print_r()
				'opr_log_dto_list' => print_r ( $value ['oprLogDtoList'], true ), // 订单操作日志-print_r()
				'seller_operator_login_id' => $value ['sellerOperatorLoginId'], // 订单负责人登陆ID
				
				'gmt_pay_success' => $this->convertDateFormat ( $value ['gmtPaySuccess'] ), // 支付成功时间(与订单列表中gmtPayTime字段意义相同)
				'seller_operator_aliidloginid' => $value ['sellerOperatorAliidloginid'], // 卖家子帐号
				'escrow_fee' => $value ['escrowFee'], // 交易佣金
				
				'country_code' => $value ['receiptAddress'] ['country'], // 地址-国家
				'contact_person' => $value ['receiptAddress'] ['contactPerson'], // 地址-收件人
				'address' => $value ['receiptAddress'] ['address'], // 地址-地址
				'address2' => $value ['receiptAddress'] ['address2'], // 地址-地址2
				'detail_address' => $value ['receiptAddress'] ['detailAddress'], // 地址-详细地址
				'province' => $value ['receiptAddress'] ['province'], // 地址-州/省
				'city' => $value ['receiptAddress'] ['city'], // 地址-城市
				'zip' => $value ['receiptAddress'] ['zip'], // 地址-邮编
				'mobile_no' => $value ['receiptAddress'] ['mobileNo'], // 地址-手机号码
				'phone_country' => $value ['receiptAddress'] ['phoneCountry'], // 地址-电话国家
				'phone_area' => $value ['receiptAddress'] ['phoneArea'], // 地址-电话分区
				'phone_number' => $value ['receiptAddress'] ['phoneNumber'], // 地址-电话号码
				'fax_country' => $value ['receiptAddress'] ['faxCountry'], // 地址-传真国家
				'fax_area' => $value ['receiptAddress'] ['faxArea'], // 地址-传真分区
				'fax_number' => $value ['receiptAddress'] ['faxNumber'], // 地址-传真号码
				                                                       // 加载状态--0：下载订单列表，1：下载订单明细，2：生成待审核订单，3：付款未完成，风控等订单（需要继续拉取状态），4：相同订单号，5：不在试运行
				'is_loaded' => '1',
				'user_account' => $this->_user_account,
				'company_code' => $this->_company_code,
				'update_time_sys' => date ( 'Y-m-d H:i:s' ) 
		);
		$row = Ec_AutoRun::arrayNullToEmptyString ( $row );
		foreach ( $row as $k => $v ) {
			$row [$k] = $v . '';
		}
		// print_r ( $row );
		// exit ();
		$con = array (
				'order_id' => $row ['order_id'],
				'company_code' => $this->_company_code,
				'user_account' => $this->_user_account 
		);
		$exist = Service_AliexpressOrderOriginal::getByCondition ( $con );
		if ($exist) {
			$exist = array_pop ( $exist );
			$aoo_id = $exist ['aoo_id'];
			Service_AliexpressOrderOriginal::update ( $row, $aoo_id, 'aoo_id' );
			
			// 日志
			$diff = array_diff_assoc ( $row, $exist );
			unset ( $diff ['update_time_sys'] );
			unset ( $diff ['create_time_sys'] );
			if ($diff) {
				// 日志
				$log = array ();
				foreach ( $diff as $k => $v ) {
					$log [] = $k . ' from ' . $exist [$k] . ' to ' . $v;
				}
				$logRow = array (
						'order_code' => $row ['order_id'],
						'content' => implode ( "\n", $log ),
						'add_time' => date ( 'Y-m-d H:i:s' ) 
				);
				$db = Common_Common::getAdapter ();
				$table = 'aliexpress_order_log';
				$db->insert ( $table, $logRow );
			}
		} else {
			// 订单头不存在,给出警告信息
			$row ['is_load'] = '99';
			$aoo_id = Service_AliexpressOrderOriginal::add ( $row );
		}
		return $row;
	}
}