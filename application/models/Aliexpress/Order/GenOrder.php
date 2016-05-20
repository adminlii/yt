<?php
/**
 * 生成Aliexpress系统订单 For TMS&OMS
 * @author Max
 * @date 2014-12-29 16:20:13
 */
class Aliexpress_Order_GenOrder{
	protected $_user_account = 'not_set';
	protected $_company_code = 'not_set';
	protected $_order = array();
	protected $_aoo_id = '';
	protected $_platform_user = null;
	
	private $_orderRow = array();
	private $_orderProductArr = array();
	private $_addressRow = array();
	
	private $_refrence_no_platform='';
	private $_refrence_no='';

	private $_orderOrg = array();
	private $_orderProductOrg = array();
	private $_platform_ship_status = 0;
	private $_platform_ship_time='';
		
	public function setAooId($aoo_id) {
		$this->_aoo_id = $aoo_id;
	}
	
	private function _getOrder(){
		$con = array (
				'refrence_no_platform' => $this->_refrence_no_platform,
				'user_account' => $this->_user_account,
				'company_code' => $this->_company_code 
		);
		$order = Service_Orders::getByCondition ( $con );
		if ($order) {
			if (count ( $order ) > 1) {
				throw new Exception ( '订单数据异常' );
			}
			$order = array_pop ( $order );
			$this->_order = $order;
		} else {
			$con = array (
					'refrence_no' => $this->_refrence_no,
					'user_account' => $this->_user_account,
					'company_code' => $this->_company_code 
			);
			$order = Service_Orders::getByCondition ( $con );
			if ($order) {
				if (count ( $order ) > 1) {
					throw new Exception ( '订单数据异常' );
				}
				$order = array_pop ( $order );
				$this->_order = $order;
			}
		}
	}

	private function _convertOrderRow(){
		if (! $this->_orderOrg) {
			throw new Exception ( 'orderOrg没有设置' );
		}
		$orderOrg = $this->_orderOrg;
		
		$row = array ();
		
		$row ['platform'] = 'aliexpress';
		$row ['data_source'] = 'aliexpress';
		$row ['order_type'] = 'sale';
		$row ['create_type'] = 'api';
		
		// 已发货--订单状态
		$shiped_status = array (
				'SELLER_PART_SEND_GOODS',
				'WAIT_BUYER_ACCEPT_GOODS' 
		);
		// 当前订单状态
		$curr_order_status = $orderOrg ['order_status'];
		// 当前订单付款状态
		$curr_fund_status = $orderOrg ['fund_status'];
		if (in_array ( $curr_order_status, $shiped_status )) {
			// 已发货和已结束并付款成功的订单，直接转为【已发货】
			$status = '2';
			$row ['sync_status'] = '1';
			$this->_platform_ship_status = 1;
			$this->_platform_ship_time = date('Y-m-d H:i:s');
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [已发货]';
		} else if ($curr_order_status == 'FINISH') {
			// //已结束，未付款，【废弃】 
			// 已结束，未付款和付款成功的订单【废弃】
			if ($curr_fund_status == 'NOT_PAY') {
				$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [已结束，未付款 ]';
			} else if ($curr_fund_status == 'PAY_SUCCESS') {
				$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status .'[已结束，付款成功, 风控未通过，订单结束]';
			} else {
				$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [订单结束]';
			}
			$status = '1'; 
		} else if ($curr_order_status == 'WAIT_SELLER_SEND_GOODS') {
			// 等待卖家发货，直接转为【待发货审核】
			$status = '2'; 
		} else if ($curr_order_status == 'PLACE_ORDER_SUCCESS') {
			// 未付款订单，【付款未完成】
			$status = '1'; 
			$row ['process_again'] = '3'; // 等待付款，还需定时检查订单付款状态
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [等待付款]';
			 
		} else if ($curr_order_status == 'RISK_CONTROL') {
			// 风控订单，【付款未完成】
			$status = '1';
			$row ['process_again'] = '4'; // 已付款，需等到订单状态变为：等待卖家发货
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [已付款,等待系统确认]';
			 
		} else if ($curr_order_status == 'IN_CANCEL') {
			// 申请取消，【不生成订单】，只修改原始表状态
			$status = '1';
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [申请取消订单]';
			 
		} else if ($curr_order_status == 'FUND_PROCESSING') {
			// 退款，直接生成【作废订单】
			$status = '1';
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，付款状态：' . $curr_fund_status . ' [退款中]';
			 
		}
		$row ['order_status'] = $status;
		$row ['create_method'] = '2';
		// $row['customer_id'] = '';
		$row ['company_code'] = $this->_company_code;
		$row ['user_account'] = $this->_user_account;
		$row ['shipping_method'] = '';
		$row ['shipping_method_platform'] = '';
		$row ['warehouse_id'] = '';
		$row ['order_desc'] = '';
		$row ['date_create'] = date ( 'Y-m-d H:i:s' );
		$row ['date_release'] = '';
		$row ['date_warehouse_shipping'] = '';
		$row ['date_last_modify'] = date ( 'Y-m-d H:i:s' );
		$row ['operator_id'] = '';
		$row ['refrence_no'] = $orderOrg ['order_id'];
		$row ['refrence_no_platform'] = $this->_refrence_no_platform;
		
		$row ['refrence_no_sys'] = '';
		$row ['shipping_address_id'] = '';
		$row ['refrence_no_warehouse'] = '';
		$row ['shipping_method_no'] = '';
		$row ['date_create_platform'] = $orderOrg ['gmt_create'];
		$row ['date_paid_platform'] = $orderOrg ['gmt_pay_time'];
		$row ['date_paid_int'] = '';
		
		$row ['finalvaluefee'] = round ( $orderOrg ['escrow_fee'] + ($orderOrg ['pay_amount'] * Aliexpress_AliexpressLib::$Transaction_Fees), 3 ); // 交易费
		                                                                                                                                           // //平台费用
		$row ['currency'] = $orderOrg ['order_currency_code'];
		$row ['buyer_id'] = preg_replace ( '/\s+/', '', strtolower ( $orderOrg ['buyer_login_id'] ) ); // amazon订单，无买家id，以收件人姓名为准
		$row ['third_part_ship'] = '0'; // 是否第三方仓库发货，默认0，审核订单的时候改变
		$row ['is_merge'] = '0';
		$row ['site'] = ''; // 速卖通没有平台概念
		
		// 速卖通部分国家转换处理
		$row ['consignee_country'] = self::getCountryAliasesMapping($orderOrg ['country_code']);
		$row ['order_weight'] = '';
		$row ['abnormal_type'] = ''; 
		$row ['buyer_name'] = $orderOrg ['buyer_signer_fullname'];
		$row ['buyer_mail'] = $orderOrg ['buyer_email'];
		$row ['has_buyer_note'] = '';
		
		if (! $this->_orderProductOrg) {
			$this->_convertOrderProductArr ();
		}
		$detailArr = $this->_orderProductOrg;
		
		$totalAmount = 0;
		$totalShippingAmount = $orderOrg ['logistics_amount'];
		$totalQty = 0;
		$is_one_piece = '1';
		$buyer_note = '';
		$shipping_method_platform = '';
		foreach ( $detailArr as $detail ) {
			// 产品数量相加
			$totalQty += $detail ['product_count'];
			$buyer_note .= (! empty ( $detail ['memo'] )) ? $detail ['memo'] . ' ' : '';
			$shipping_method_platform = $detail ['logistics_type'];
		}
		if ($totalQty > 1) {
			$is_one_piece = '0';
		}
		
		$row ['is_one_piece'] = $is_one_piece; // 判断一下items
		$row ['operator_note'] = '';
		$row ['product_count'] = $totalQty; // 产品数量
		$row ['order_desc'] = $buyer_note; // 买家留言
		$row ['shipping_method_platform'] = $shipping_method_platform; // 平台运输方式
		
		$row ['amountpaid'] = $orderOrg ['pay_amount']; // 包运费（使用实际付款金额字段）
		$row ['subtotal'] = round ( ($orderOrg ['pay_amount'] - $totalShippingAmount), 3 ); // 交易额（不包运费）
		$row ['ship_fee'] = $totalShippingAmount; // 运费
		
		$this->_orderRow = $row;
	}
	private function _convertAddressRow(){
		if (! $this->_orderOrg) {
			throw new Exception ( 'orderOrg没有设置' );
		}
		$orderOrg = $this->_orderOrg;
		
		/*
		 * 地址
		 */
		$address = array ();
		$address ['Name'] = $orderOrg ['contact_person'];
		$address ['Street1'] = $orderOrg ['detail_address'];
		$address2 = $orderOrg ['address2'];
		$address3 = $orderOrg ['address'];
		$address ['Street2'] = ((! empty ( $address2 )) ? $address2 : '') . ' ' . ((! empty ( $address3 )) ? $address3 : '');
		// $address['Street3'] =
		// $amazonOrderOriginal['shipping_address_address3'];
		$address ['CityName'] = $orderOrg ['city'];
		$address ['StateOrProvince'] = $orderOrg ['province'];
		
		$country_code = self::getCountryAliasesMapping($orderOrg ['country_code']);
		$address ['Country'] = $country_code;
		$resultCountry = Service_Country::getByField ( $country_code, 'country_code' );
		$address ['CountryName'] = $resultCountry ['country_name_en'];
		$address ['District'] = '';
		
		$phone = '';
		if (! empty ( $orderOrg ['mobile_no'] )) {
			$phone = $orderOrg ['mobile_no'];
		} else {
			$phone = $orderOrg ['phone_country'] . '-' . $orderOrg ['phone_area'] . '-' . $orderOrg ['phone_number'];
		}
		$address ['Phone'] = $phone;
		$address ['PostalCode'] = $orderOrg ['zip'];
		$address ['AddressID'] = '';
		$address ['AddressOwner'] = '';
		$address ['ExternalAddressID'] = '';
		$address ['OrderID'] = $this->_refrence_no_platform;
		$address ['Plat_code'] = 'aliexpress';
		$address ['company_code'] =  $this->_company_code;
		$address ['create_date_sys'] = date ( 'Y-m-d H:i:s' );
		$address ['modify_date_sys'] = date ( 'Y-m-d H:i:s' );
		$address ['user_account'] = $this->_user_account;
		$address ['is_modify'] = '0';

		if(!$address['Country']){
			throw new Exception('订单无目的国家');
		}
		if(empty($address ['Street1'])&&empty($address ['Street2'])){
			throw new Exception('订单无详细地址');			
		}
		$this->_addressRow = $address;
		/*
		 * 地址
		 */
	}
	
	private function _convertOrderProductArr(){
		if(!$this->_orderOrg){
			throw new Exception('orderOrg没有设置');
		}
		$orderOrg = $this->_orderOrg;
		$con = array (
				'aoo_id' => $orderOrg ['aoo_id']
		);
		$detailArr = Service_AliexpressOrderDetail::getByCondition ( $con );
		if(empty($detailArr)){
			throw new Exception('订单明细不存在');
		}
		$this->_orderProductOrg = $detailArr;
		/**明细**/
		foreach ( $detailArr as $detail ) {
			$product = array ();
			$product ['order_id'] = '';
			$product ['product_id'] = '0';
			$product ['product_sku'] = empty($detail ['sku_code'])?'-NoSKU-':$detail ['sku_code'];
			$product ['warehouse_sku'] = '';
			$product ['product_title'] = $detail ['product_name'];
			$product ['op_quantity'] = $detail ['product_count'];
			$product ['op_ref_tnx'] = '';
			$product ['op_recv_account'] = '';
			$product ['op_ref_item_id'] = $detail ['product_id'];
			$product ['op_site'] = '';
			$product ['op_record_id'] = '';
			$product ['op_ref_buyer_id'] = '';
			$product ['op_ref_paydate'] = '';
			$product ['op_add_time'] = date ( 'Y-m-d H:i:s' );
			$product ['op_update_time'] = date ( 'Y-m-d H:i:s' );
			$product ['OrderID'] = $this->_refrence_no_platform;
			$product ['OrderIDEbay'] = $this->_refrence_no;
			$product ['is_modify'] = '0';
			$product ['pic'] = $detail ['product_img_url'];
			$product ['url'] = $detail ['product_snap_url'];
			$product ['unit_price'] = $detail ['product_unit_price_amount'];
			$product ['give_up'] = 0; // 是否废弃，item数量为0时，设置为1：表示该item不发货
			$product ['currency_code'] = $detail ['product_unit_price_currency_code'];
			$this->_orderProductArr[] = $product;
		}
		/**明细**/
	}
	private function _check(){
		$aoo_id = $this->_aoo_id ;
		if(!$this->_aoo_id){
			throw new Exception('没有传入参数');
		}
		$orderOrg = Service_AliexpressOrderOriginal::getByField($aoo_id,'aoo_id');
		if(!$orderOrg){
			throw new Exception('订单原始信息不存在');			
		}
		$order_id = $orderOrg ['order_id'];
		
		$this->_user_account = $orderOrg['user_account'];
		$this->_company_code = $orderOrg['company_code'];
		$this->_refrence_no = $orderOrg['order_id'];		 
		$refrence_no_platform = 'AL' . sprintf ( '%010s', $orderOrg ['aoo_id'] );
		$this->_refrence_no_platform = $refrence_no_platform;
		$this->_orderOrg = $orderOrg;

		$this->_convertOrderProductArr(); 
		$this->_convertOrderRow();
		$this->_convertAddressRow();
		$this->_getOrder();
	}
	public function genOrder(){
		$return = array('ask'=>0,'message'=>'Fail');
		$db = Common_Common::getAdapter();
		$db->beginTransaction();
		try {
				// 数据验证
			$this->_check ();

			$refrence_no_platform = $this->_refrence_no_platform;
			if (! $this->_order) {
				//没有创建订单，创建操作订单
				//地址
				// 删除shipping_address
				Service_ShippingAddress::delete ( $refrence_no_platform, 'OrderID' );
				$this->_addressRow ['OrderID'] = $refrence_no_platform;
				$this->_addressRow ['user_account'] = $this->_user_account;
				$this->_addressRow ['company_code'] = $this->_company_code;
				$shipping_address_id = Service_ShippingAddress::add ( $this->_addressRow );
				
				$refrence_no_sys = Common_ApiProcess::getRefrenceSysCode(); // 系统单号
				$this->_orderRow['refrence_no_sys'] = $refrence_no_sys;
				$this->_orderRow['shipping_address_id'] = $shipping_address_id;
				$this->_orderRow['platform_ship_status'] = $this->_platform_ship_status;
				$this->_orderRow['platform_ship_time'] = $this->_platform_ship_time;				
				
				$order_id = Service_Orders::add ( $this->_orderRow );
				// 删除order_product
				Service_OrderProduct::delete ( $refrence_no_platform, 'OrderID' );
							
				//明细
				foreach ( $this->_orderProductArr as $k=> $p ) {
					$p ['order_id'] = $order_id;
					$p ['OrderID'] = $refrence_no_platform;
					Service_OrderProduct::add ( $p );
					$this->_orderProductArr[$k] = $p;
				}
			} else {
				// 更新平台标记发货信息
				$updateRow = array (
						'abnormal_reason' => $this->_orderRow['abnormal_reason'],
						'order_status' => $this->_orderRow['order_status'],
						'platform_ship_status' => $this->_platform_ship_status,
						'platform_ship_time' => $this->_platform_ship_time 
				);				
				Service_Orders::update ( $updateRow, $this->_refrence_no_platform, 'refrence_no_platform' );
			}
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
			$db->commit ();
		} catch (Exception $e) {
			$db->rollback();
			$return['message'] = $e->getMessage();
			$err_msg = $e->getMessage();
		}

		$return['refrence_no'] = $this->_refrence_no;
		$return['refrence_no_platform'] = $this->_refrence_no_platform;
		$return['orderOrg'] = $this->_orderOrg;
		$return['orderProductOrg'] = $this->_orderProductOrg;
		$return['orderRow'] = $this->_orderRow;
		$return['orderProductArr'] = $this->_orderProductArr;
		$return['addressRow'] = $this->_addressRow;
		//更新原始信息状态
		
		if ($err_msg && preg_match ( '/SQLSTATE/', $err_msg )) {
		
		}else{
			$upRow = array('is_loaded'=>'2');
			Service_AliexpressOrderOriginal::update($upRow, $this->_aoo_id,'aoo_id');
		}
		Service_AliexpressOrderOriginal::update($upRow, $this->_aoo_id,'aoo_id');
		return $return;
	}
	
	/**
	 * 速卖通订单生成系统订单
	 */
	public static function genOrderBatch($user_account='',$company_code=''){
		$return = array ();
		$con = array (
				'user_account' => $user_account,
				'company_code' => $company_code,
				'is_loaded' => 1 
		);
		$filed = array (
				'aoo_id',
				'user_account',
				'company_code',
				'order_id' 
		);
		$orderOrgArr = Service_AliexpressOrderOriginal::getByCondition ( $con, $filed );


		foreach ( $orderOrgArr as $orderOrg ) {
			$process = new Aliexpress_Order_GenOrder ();
			$aoo_id = $orderOrg ['aoo_id']; 
			$process->setAooId ( $aoo_id );
			$rs = $process->genOrder ();
			$return [] = $rs;
			Ec::showError(print_r($rs,true),'_aliexpress_gen_order_batch');
// 			exit;
		}
		return $return;
	}
	
	/**
	 * 返回国家二字码
	 * @param unknown_type $country_code
	 */
	public static function getCountryAliasesMapping($country_code) {
		$str = $country_code;
		
		// 国家映射
		$arr = array(
				'UK' => 'GB',	// 英国
				'SRB' => 'RS',	// 塞尔维亚共和国
				'MNE' => 'ME',	// 黑山共和国
				);
		
		if(empty($country_code)) { 
			return $str;
		}
		
		// 当存在以上国家时，做数据转换
		if(isset($arr[$country_code])) {
			$str = $arr[$country_code];
		}
		
		return $str;
	}
}