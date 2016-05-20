<?php
/**
 * 马帮-查询订单列表服务
 * @author Max
 */
class Mabang_Order_OrderService extends Mabang_Order_Common {
	/**
	 * 日志文件名
	 *
	 * @var unknown_type
	 */
	protected $log_name = 'mabang_OrderList_';
	protected $_pageSize = 50;
	protected $_orderArr = array ();
	protected $_orderOrgArr = array ();
	
	/**
	 * 马帮订单
	 */
	protected $mabangOrderRow = array ();
	
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
	public function orderListQuery($codes) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		
		try {
			/*
			 * 4.组织参数,并调用订单列表查询接口
			 */
			$pageSize = $this->_pageSize;
			// Common_ApiProcess::log ( "转换参数类型,start: $request_start ,end：$request_end" );
			
			$loop_call = true;
			$loop_tootal_count = 0;
			$page = 0;
			while ( true ) {
				$page ++;
				$params = array (
						'codes' => $codes 
				);
				$response = Mabang_MabangLib::getListOrdersForArr ( $params );
				$response_org = $response;
				
				// 查看接口调用是否成功
				if (isset ( $response ['ErrorCode'] ) && $response ['ErrorCode'] == '9999') {
				} else {
					throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response, true ) );
				}
				
				if ($response ['ErrorCode'] != '9999') {
					throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response_org, true ) );
				}
				$result = $response ['Data'];
				// if($result["pagination"]["totalCount"]>0){
				// print_r ( $response_org );exit;
				// }
				// $totalItem = $result ['totalItem'];
				$orderList = $result ['orders'] ? $result ['orders'] : array ();
				$pagination = $result ['pagination'];
				
				// 数据保存
				foreach ( $orderList as $order ) {
					// print_r( $order);exit;
					// exit;
					$this->_orderOrgArr [] = $order;
					
					$this->_saveOrderInfo ( $order );
				}
				if (($page - 1) * $pageSize + count ( $orderList ) >= $pagination ['totalCount']) {
					break;
				}
			}
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
			$return ['orderOrgArr'] = $this->_orderOrgArr;
			$return ['orderArr'] = $this->_orderArr;
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		// print_r($return);exit;
		return $return;
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
	public function orderListQueryByTime($start, $end) {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			/*
			 * 4.组织参数,并调用订单列表查询接口
			 */
			$pageSize = $this->_pageSize;
			// Common_ApiProcess::log ( "转换参数类型,start: $request_start ,end：$request_end" );
			
			$loop_call = true;
			$loop_tootal_count = 0;
			$page = 0;
			while ( true ) {
				$page ++;
				$params = array (
						'dateFrom' => $start,
						'dateTo' => $end,
						'page' => $page,
						'rowsPerPage' => 100 ,
						'status'=>2,
				);
				$response = Mabang_MabangLib::getListOrdersForArr ( $params );
				$response_org = $response;
				// 查看接口调用是否成功
				if (isset ( $response ['ErrorCode'] ) && $response ['ErrorCode'] == '9999') {
					// 正常
				} else {
					throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response, true ) );
				}
				
				if ($response ['ErrorCode'] != '9999') {
					throw new Exception ( "调用接口，返回异常信息，详情：" . print_r ( $response_org, true ) );
				}
				$result = $response ['Data'];
				
				// if($result["pagination"]["totalCount"]>0){
				// print_r ( $response_org );exit;
				// }
				// $totalItem = $result ['totalItem'];
				$orderList = $result ['orders'] ? $result ['orders'] : array ();
				$pagination = $result ['pagination'];
				
				// 数据保存
				foreach ( $orderList as $order ) {
					// print_r( $order);exit;
					// exit;
					$this->_orderOrgArr [] = $order;
					$this->_saveOrderInfo ( $order );
				}
				if (($page - 1) * $pageSize + count ( $orderList ) >= $pagination ['totalCount']) {
					break;
				}
			}
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
			$return ['orderArr'] = $this->_orderArr;
			// $return ['orderOrgArr'] = $this->_orderOrgArr;
		} catch ( Exception $e ) {
			// print_r($e);exit;
			
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
	
	/**
	 * 登录
	 * 
	 * @param unknown $logisticsKeys        	
	 * @throws Exception
	 */
	public function doLogin($logisticsKeys,$value) {
		
		try {
			// $logisticsKeys="peachtao@sina.com";
			$company_code = $logisticsKeys ['api_key'];
			$user_account = $logisticsKeys ['api_secret'];
			// ====================调试==========================================
// 			$company_code = '10000005';
// 			$user_account = "peachtao@sina.com";
			$con = array (
					'company_code' => $company_code,
					'user_account' => $user_account
			);
			$apiuser = Service_PlatformUser::getByCondition ( $con );
			if (empty ( $apiuser )) 		// 如果商户不存在,返回异常信息给马帮
			{
				throw new Exception ( '商户不存在(api_key和api_secret无效)' );
			} else {
				$apiuser = array_pop ( $apiuser );
			}
			
			$db2=Common_Common::getAdapterForDb2();
			
			$sql = "select * from csi_customer where customer_code='{$company_code}';";
			$customer = $db2->fetchRow ( $sql );
			if (! $customer) {
				throw new Exception ( 'API账号非法' . $sql, 50001 );
			}
			
			
			$user_id = $apiuser ['seller_id'] . '';
			$user = Service_User::getByField ( $user_id, 'user_id' );
			if (! $user) {
				throw new Exception ( '客户代码非法', 50001 );
			}
			$user ['tms_id'] = $customer ['tms_id'];
			$upRow = Service_UserPosition::getByField ( $user ['up_id'], 'up_id' );
			$user ['upl_id'] = $upRow ['upl_id'];
			
			$user ['csi_customer'] = $customer;
			
			$session = new Zend_Session_Namespace ( 'userAuthorization' );
			
			$session->user = $user;
			$session->company_code = $customer ['customer_code'];
			$session->customer_code = $customer ['customer_code'];
			
			$session->user = $user;
			$session->csi_customer = $customer;
			$session->userId = $user ['user_id'];
			$session->customer_id = $user ['customer_id'];
			$session->customer_code = $customer ['customer_code'];
			$session->userCode = $user ['user_code'];
		} catch (Exception $e) {
			$params = array (
					'code' => $value ['code'],
					"changeStatus" => 'exception',
					'processMessage' => '商户不存在(api_key和api_secret无效)'
			);
			Mabang_MabangLib::updateOrderStatus ( $params );
				throw new Exception ($e->getMessage(),$e->getCode());
		}
		
	}
	
	/**
	 * 封装Mabang返回的订单信息
	 *
	 * @param unknown_type $user_account        	
	 * @param unknown_type $aliexpress_result        	
	 */
	protected function _saveOrderInfo($value) {
		$date = date ( 'Y-m-d H:i:s' );
		// print_r($value['customer']['logisticsKeys']['zjs']['api_key']);exit;
		$logisticsKeys = $value ['customer'] ['logisticsKeys'];
		
		$logisticsKeys = array_pop ( $logisticsKeys );
		
		// 获取登录信息
		$this->doLogin ( $logisticsKeys, $value);
		
		$company_code = $logisticsKeys ['api_key'];
		$user_account = $logisticsKeys ['api_secret'];
		// $this->_companyCode = '100002';
		
		// print_r($apiuser);
		// exit;
		
		$customerID_session = Service_User::getCustomerId ();
		if ($customerID_session == '0') {
			// Service_User::loginByUser_id($apiuser[0]['user_id']);
		}
		$weightforcast = $value ['weightForcast'] / 1000;
		$weightreal = $value ['weightReal'] / 1000;
		if ($weightforcast < 0.01) {
			$weightforcast = 0.01;
		}
		if ($weightreal < 0.01) {
			$weightreal = 0.01;
		}
		$order_row = array (
				'company_code' => $company_code, // 公司代码
				'user_account' => $user_account, // 马帮账号
				//'user_id' => Service_User::getUserId(), // 用户ID
				'create_time_sys' => date ( 'Y-m-d H:i:s' ), // 订单拉取时间
				'update_time_sys' => date ( 'Y-m-d H:i:s' ), // 订单更新时间
				'code' => $value ['code'], // 马帮订单号
				'platformTradeCode' => $value ['platformTradeCode'], // 平台交易号
				'status' => $value ['status'], // 订单状态,2:待入库,3:已入库,4:已出库,5:已完成,6:已确认(供应商已下载),-1:异常订单
				'hasException' => $value ['hasException'], // 是否有异常,0:无,1:有
				'processMessage' => $value ['processMessage'], // 异常信息,物流供应商处理信息
				
				'packageId' => $value ['packageId'], // packageIdD
				'priceForcast' => $value ['priceForcast'], // 预计订单费用
				'priceReal' => $value ['priceReal'], // 实际订单费用
				'shippingCountryCode' => $value ['shippingCountryCode'], // 包裹地址国家代码
				'timeCreated' => $value ['timeCreated'], // 订单生成时间
				'weightForcast' => $weightforcast, // 客户预报订单重量
				'weightReal' => $weightreal, // 供应商实际称重订单重量
				'length' => $value ['length'], // 包裹长
				'width' => $value ['width'], // 包裹宽
				'height' => $value ['height'], // 包裹高
				'productNameCn' => $value ['productNameCn'], // 订单申报物品中文名
				'productNameEn' => $value ['productNameEn'], // 订单申报物品英文名
				'productValue' => $value ['productValue'], // 订单申报价格
				'remark' => $value ['remark'], // 备注
				'itemListQuantity' => $value ['itemListQuantity'], // 货物数量
				'pickup_contact' => $value ['addressPickup'] ['contact'], // 寄件人姓名
				'pickup_province' => $value ['addressPickup'] ['province'], // 寄件人所属省
				'pickup_city' => $value ['addressPickup'] ['city'], // 寄件人所属城市
				'pickup_area' => $value ['addressPickup'] ['area'], // 寄件人所属地区
				'pickup_address' => $value ['addressPickup'] ['address'], // 寄件人详细地址
				'pickup_telephone' => $value ['addressPickup'] ['telephone'], // 寄件人联系电话
				'pickup_mobile' => $value ['addressPickup'] ['mobile'], // 寄件人手机
				'pickup_zipcode' => $value ['addressPickup'] ['zipcode'], // 寄件人邮编
				'back_contact' => $value ['addressBack'] ['contact'], // 退件人姓名
				'back_province' => $value ['addressBack'] ['province'], // 退件人所属省
				'back_city' => $value ['addressBack'] ['city'], // 退件人所属城市
				'back_area' => $value ['addressBack'] ['area'], // 退件人所属地区
				'back_address' => $value ['addressBack'] ['address'], // 退件人详细地址
				'back_telephone' => $value ['addressBack'] ['telephone'], // 退件人联系电话
				'back_mobile' => $value ['addressBack'] ['mobile'], // 退件人手机
				'back_zipcode' => $value ['addressBack'] ['zipcode'], // 退件人邮编
				'receive_countryCode' => $value ['addressReceive'] ['countryCode'], // 收件人国家
				'receive_receiver' => $value ['addressReceive'] ['receiver'], // 收件人姓名
				'receive_province' => $value ['addressReceive'] ['province'], // 收件人省名
				'receive_city' => $value ['addressReceive'] ['city'], // 收件人城市
				'receive_street1' => $value ['addressReceive'] ['street1'], // 收件人地址
				'receive_telephone' => $value ['addressReceive'] ['telephone'], // 收件人联系方式
				'receive_email' => $value ['addressReceive'] ['email'], // 收件人电子邮箱
				'receive_zipcode' => $value ['addressReceive'] ['zipcode'], // 收件人邮编
				'expresschannelcode' => $value ['expressChannelCode'], // 物流渠道运单号
				'expresschannelname' => $value ['expressChannel'] ['name'], // 渠道名称
				'expresschanneltype' => $value ['expressChannel'] ['channelType'], // 渠道类型,1:国际邮政,2:邮政平邮,3:邮政挂号
				'myexpresschannelname' => $value ['myExpressChannel'] ['name'], // 供应商自定义渠道名称
				'myexpresschannelcustomerCode' => $value ['myExpressChannel'] ['customerCode'], // 供应商自定义渠道代码
				'htmlurl_b10_10_a' => $value ['labelHTMLUrl'] ['b10_10'] ['a'], // 地址单地址
				'htmlurl_b10_10_c' => $value ['labelHTMLUrl'] ['b10_10'] ['c'], // 报关单地址
				'htmlurl_b10_10_ac' => $value ['labelHTMLUrl'] ['b10_10'] ['ac'], // 地址单和报关单
				'htmlurl_a4_a' => $value ['labelHTMLUrl'] ['a4'] ['a'], // 地址单地址
				'htmlurl_a4_c' => $value ['labelHTMLUrl'] ['a4'] ['c'], // 报关单地址
				'htmlurl_a4_ac' => $value ['labelHTMLUrl'] ['a4'] ['ac'], // 地址单和报关单
				'pdfurl_b10_10_a' => $value ['labelPDFUrl'] ['b10_10'] ['a'], // 地址单地址
				'pdfurl_b10_10_c' => $value ['labelPDFUrl'] ['b10_10'] ['c'], // 报关单地址
				'pdfurl_b10_10_ac' => $value ['labelPDFUrl'] ['b10_10'] ['ac'], // 地址单和报关单
				'pdfurl_a4_a' => $value ['labelPDFUrl'] ['a4'] ['a'], // 地址单地址
				'pdfurl_a4_c' => $value ['labelPDFUrl'] ['a4'] ['c'], // 报关单地址
				'pdfurl_a4_ac' => $value ['labelPDFUrl'] ['a4'] ['ac'], // 地址单和报关单
				'imgurl_b10_10_a' => $value ['labelPDFUrl'] ['b10_10'] ['a'], // 地址单地址
				'imgurl_b10_10_c' => $value ['labelPDFUrl'] ['b10_10'] ['c'], // 报关单地址
				'imgurl_a4_a' => $value ['labelIMGUrl'] ['a4'] ['a'], // 地址单地址
				'imgurl_a4_c' => $value ['labelIMGUrl'] ['a4'] ['c'], // 报关单地址
				'customer_username' => $value ['customer'] ['username'], // 卖家账号
				'customer_name' => $value ['customer'] ['name']  // 卖家姓名
				);
		
		// print_r($apiuser[0]['company_code']);exit;
		$order_row = Ec_AutoRun::arrayNullToEmptyString ( $order_row );
		
		foreach ( $order_row as $k => $v ) {
			$order_row [$k] = $v . '';
		}
		$con = array (
				'code' => $order_row ['code'] 
		// 'company_code' => $company_code,
		// 'user_account' => $user_account
				);
		
		$exist = Service_MabangOrderOriginal::getByCondition ( $con );
		
		$db = Common_Common::getAdapter ();
		// print_r($exist);exit;
		if (! empty ( $exist )) {
			unset ( $order_row ['create_time_sys'] );
			$exist = array_pop ( $exist );
			$moo_id = $exist ['moo_id'];
			Service_MabangOrderOriginal::update ( $order_row, $moo_id, 'moo_id' );
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
						'order_code' => $order_row ['code'],
						'content' => implode ( "\n", $log ),
						'add_time' => date ( 'Y-m-d H:i:s' ) 
				);
				$table = 'mabang_order_log';
				$db->insert ( $table, $logRow );
			}
		} else {
			$moo_id = Service_MabangOrderOriginal::add ( $order_row );
			Common_ApiProcess::log ( 'moo_id:' . $moo_id );
		}
		$order_row ['moo_id'] = $moo_id;
		// cao==============================
		Service_MabangShipmentList::delete ( $moo_id, 'moo_id' );
		Service_MabangShipmentList::delete ( $order_row ['code'], 'code' );
		$response_products = $value ['itemList'];
		$product_list_row = array ();
		foreach ( $response_products as $key_p => $product ) {
			$product_row = array (
					'moo_id' => $moo_id, // 马帮主表ID
					'code' => $order_row ['code'], // 马帮订单号
					'sku' => $product ['sku'], // 商品SKU
					'productName' => $product ['productName'], // 商品名称
					'declareNameCn' => $product ['declareNameCn'], // 商品中文申报名称
					'declareNameEn' => $product ['declareNameEn'], // 商品英文申报名称
					'weight' => $product ['weight'], // 商品单件重量
					'quantity' => $product ['quantity'], // 数量
					'declareValue' => $product ['declareValue'], // float
					'itemUrl' => $product ['itemUrl'], // 交易平台商品URL地址
					'user_account' => $user_account,
					'company_code' => $company_code,
					'create_time_sys' => date ( 'Y-m-d H:i:s' ),
					'update_time_sys' => date ( 'Y-m-d H:i:s' ) 
			);
			$product_row = Ec_AutoRun::arrayNullToEmptyString ( $product_row );
			
			foreach ( $product_row as $k => $v ) {
				$product_row [$k] = $v . '';
			}
			Service_MabangShipmentList::add ( $product_row );
			$order_row ['order_product'] [] = $product_row;
		}
		$this->_orderArr [] = $order_row;
	}
}