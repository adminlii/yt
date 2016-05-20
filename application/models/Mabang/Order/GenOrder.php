<?php
/**
 * 生成Mabangs系统订单 For TMS&OMS
 * @author Max
 * @date 2015-07-04 10:03:42
 */
class Mabang_Order_GenOrder{
	protected $_user_account = 'not_set';
	protected $_company_code = 'not_set';
	protected $_order = array();
	protected $_moo_id = '';
	protected $_customerCode = '';
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
	private $_customer_channelid=0;
		
	public function setMooId($moo_id) {
		$this->_moo_id = $moo_id;
	}
	public function setCustomerCode($customerCode){
		$this->_customerCode = $customerCode;
		}
	private function _getOrder(){
		Service_Orders::delete ($this->_refrence_no_platform, 'refrence_no_platform' );
		Service_ShippingAddress::delete ( $this->_refrence_no_platform, 'OrderID' );
		Service_OrderProduct::delete ( $this->_refrence_no_platform, 'OrderID' );
	}

	private function _convertOrderRow(){	
		if (! $this->_orderOrg) {
			throw new Exception ( 'orderOrg没有设置' );
		}
		$orderOrg = $this->_orderOrg;
		
		$row = array ();
		
		$row ['platform'] = 'mabang';
		$row ['data_source'] = 'mabang';
		$row ['order_type'] = 'sale';
		$row ['create_type'] = 'api';
		// 当前订单状态
		$curr_order_status = $orderOrg ['status'];
		$status = '2';
		if ($curr_order_status=='4') {
			// 已出库的订单，直接转为【已发货】
			$status = '2';
			$row ['sync_status'] = '1';
			$this->_platform_ship_status = 1;
			$this->_platform_ship_time = date('Y-m-d H:i:s');
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，配送渠道：' . $orderOrg['expresschannelname'] . ' [已出库]';
		} else if ($curr_order_status == '2') {
			//待入库
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，[待入库]';
			//============================待确认==============================
// 			$status = '1'; 
		} else if ($curr_order_status == '3') {
			//已入库
			$status = '2'; 
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，[已入库]';
		} else if ($curr_order_status == '5') {
			// 已完成
			$status = '2'; 
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，配送渠道：' . $orderOrg['expresschannelname'] . ' [已完成]';
			 
		} 
		else if ($curr_order_status == '6') {
			// 已确认
			$status = '2'; 
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，配送渠道：' . $orderOrg['expresschannelname'] . ' [已确认]';
		}
		else if ($curr_order_status == '-1') {
			// 异常订单
			$status = '1'; 
			$row ['abnormal_reason'] = '订单状态：' . $curr_order_status . '，[异常订单]';
		}
		$row ['order_status'] = $status;
		$row ['create_method'] = '2';
		//$user = Service_User::getByField($orderOrg ['user_id']);
		//$this->_customer_channelid = $user ['customer_channelid'];
		//$row ['customer_id'] = $user ['customer_id'];
		//$row ['user_id'] = $orderOrg['user_id'];
		$row ['company_code'] = $this->_company_code;
		$row ['user_account'] = $this->_user_account;
		$row ['shipping_method'] = $orderOrg ['myexpresschannelcustomerCode'];
		$row ['shipping_method_platform'] = '';
		$row ['warehouse_id'] = '';
		$row ['order_desc'] = '';
		$row ['date_create'] = date ( 'Y-m-d H:i:s' );
		$row ['date_release'] = '';
		$row ['date_warehouse_shipping'] = '';
		$row ['date_last_modify'] = date ( 'Y-m-d H:i:s' );
		$row ['operator_id'] = '';
		$row ['refrence_no'] = $orderOrg ['code'];
		$row ['refrence_no_platform'] = $this->_refrence_no_platform;
		
		$row ['refrence_no_sys'] = '';
		$row ['shipping_address_id'] = '';
		$row ['refrence_no_warehouse'] = '';
		$row ['shipping_method_no'] = '';
		$row ['date_create_platform'] = $orderOrg ['timeCreated'];
		$row ['date_paid_platform'] = '';
		$row ['date_paid_int'] = '';
		
		$row ['finalvaluefee'] = round ( $orderOrg ['weightForcast'], 3 ); // 交易费
		                                                                                                                                           // //平台费用
		$row ['currency'] =''; //$orderOrg ['order_currency_code'];
		$row ['buyer_id'] = preg_replace ( '/\s+/', '', strtolower ( $orderOrg ['receive_receiver'] ) ); // amazon订单，无买家id，以收件人姓名为准
		$row ['third_part_ship'] = '0'; // 是否第三方仓库发货，默认0，审核订单的时候改变
		$row ['is_merge'] = '0';
		$row ['site'] = ''; // 速卖通没有平台概念
		$row ['consignee_country'] = $orderOrg ['shippingCountryCode'];
		$row ['order_weight'] = $orderOrg['weightForcast'];
		$row ['abnormal_type'] = ''; 
		$row ['buyer_name'] = $orderOrg ['receive_receiver'];
		$row ['buyer_mail'] = '';
		$row ['has_buyer_note'] = '';
		$is_one_piece='1';
		if ($orderOrg['itemListQuantity'] > 1) {
			$is_one_piece = '0';
		}
		
		$row ['is_one_piece'] = $is_one_piece; // 判断一下items
		$row ['operator_note'] = '';
		$row ['product_count'] = $orderOrg['itemListQuantity']; // 产品数量
		$row ['order_desc'] = $orderOrg['remark']; // 买家留言
		$row ['shipping_method_platform'] = $orderOrg['expresschannelname']; // 平台运输方式
		
		//$row ['amountpaid'] = $orderOrg ['pay_amount']; // 包运费（使用实际付款金额字段）
		//$row ['subtotal'] = round ( ($orderOrg ['pay_amount'] - $totalShippingAmount), 3 ); // 交易额（不包运费）
		$row ['ship_fee'] = $orderOrg['priceReal']; // 运费
		
		$this->_orderRow = $row;
	}
	/**
	 * 封装amazon订单地址信息，为系统地址
	 */
	public function _convertAddressRow(){
		if (! $this->_orderOrg) {
			throw new Exception ( 'orderOrg没有设置' );
		}
		$orderOrg = $this->_orderOrg;
		$row = array ();
		if(empty($orderOrg['shippingCountryCode'])){
			throw new Exception ( '订单无配送国家' );			
		}
		if(empty($orderOrg['receive_street1'])){
			throw new Exception ( '订单无配送地址' );			
		}
		if(empty($orderOrg['receive_receiver'])){
			throw new Exception ( '订单无收件人' );			
		}
		$row ['Name'] = $orderOrg ['receive_receiver'];
		$row ['Street1'] = $orderOrg ['receive_street1'];
		//$row ['Street2'] = $orderOrg ['shipping_address_address2'] . ' ' . $orderOrg ['shipping_address_address3'];
		// $row['Street3'] = $orderOrg['shipping_address_address3'];
		$row ['CityName'] = $orderOrg ['receive_city'];
		//$row ['StateOrProvince'] = $orderOrg ['shipping_address_state'];
		$row ['Country'] = $orderOrg ['receive_countryCode'];
		
		$resultCountry = Service_IddCountry::getByField ( $orderOrg ['receive_countryCode'], 'country_code' );
		$row ['CountryName'] = $resultCountry ? $resultCountry ['country_enname'] : $orderOrg ['receive_countryCode'];
		
		$row ['StateOrProvince'] = $orderOrg ['receive_province'];
		//$row ['telephone'] = ;
		$row ['Phone'] = $orderOrg ['receive_telephone'];
		//$row ['email'] =  $orderOrg ['receive_email'];
		$row ['PostalCode'] = $orderOrg ['receive_zipcode'];
		$row ['AddressID'] = '';
		$row ['AddressOwner'] = '';
		$row ['ExternalAddressID'] = '';
		$row ['OrderID'] = $this->_refrence_no_platform;
		$row ['Plat_code'] = 'mabang';
		$row ['company_code'] = $this->_company_code;
		$row ['user_account'] = $this->_user_account;
		$row ['create_date_sys'] = date ( 'Y-m-d H:i:s' );
		$row ['modify_date_sys'] = date ( 'Y-m-d H:i:s' );
		$row ['is_modify'] = '0';
		$this->_addressRow = $row;
		return $row;
	}
	private function _convertOrderProductArr(){
		if(!$this->_orderOrg){
			throw new Exception('orderOrg没有设置');
		}
		$orderOrg = $this->_orderOrg;
		$con = array (
				'moo_id' => $orderOrg ['moo_id']
		);
		$detailArr = Service_MabangShipmentList::getByCondition ( $con );
		if(empty($detailArr)){
			throw new Exception('订单明细不存在');
		}
		$this->_orderProductOrg = $detailArr;
		/**明细**/
		foreach ( $detailArr as $detail ) {
			$product = array ();
			$product ['order_id'] = '';
			$product ['product_id'] = '0';
			$product ['product_sku'] = empty($detail ['sku'])?'-NoSKU-':$detail ['sku'];
			$product ['warehouse_sku'] = '';
			$product ['product_title'] = $detail ['productName'];
			$product ['op_quantity'] = $detail ['quantity'];
			$product ['op_weight'] = $detail ['weight'];
			$product ['op_ref_tnx'] = '';
			$product ['op_recv_account'] = '';
			$product ['op_ref_item_id'] = '';//$detail ['product_id'];
			$product ['op_site'] = '';
			$product ['op_record_id'] = '';
			$product ['op_ref_buyer_id'] = '';
			$product ['op_ref_paydate'] = '';
			$product ['op_add_time'] = date ( 'Y-m-d H:i:s' );
			$product ['op_update_time'] = date ( 'Y-m-d H:i:s' );
			$product ['OrderID'] = $this->_refrence_no_platform;
			$product ['OrderIDEbay'] = $this->_refrence_no;
			$product ['is_modify'] = '0';
			$product ['pic'] = $orderOrg ['imgurl_a4_a'];
			$product ['url'] = $detail ['itemUrl'];
			$product ['unit_price'] = $detail ['declareValue'];
			$product ['give_up'] = 0; // 是否废弃，item数量为0时，设置为1：表示该item不发货
			$product ['currency_code'] = '';//$detail ['product_unit_price_currency_code'];
			$product ['create_type'] = 'api';//创建类型：api:系统自动创建，hand：手工新增
			$this->_orderProductArr[] = $product;
		}
		/**明细**/
	}
	private function _check(){
		$moo_id = $this->_moo_id ;
		if(!$this->_moo_id){
			throw new Exception('没有传入参数');
		}
		if(empty($this->_customerCode)){
			throw new Exception('运输方式代码不可为空');			
		}
		$orderOrg = Service_MabangOrderOriginal::getByField($moo_id,'moo_id');
		if(!$orderOrg){
			throw new Exception('订单原始信息不存在');			
		}
		
		$order_id = $orderOrg ['code'];
		
		$this->_user_account = $orderOrg['user_account'];
		$this->_company_code = $orderOrg['company_code'];
		$this->_refrence_no = $orderOrg['code'];		 
		$refrence_no_platform = 'MB' . sprintf ( '%010s', $orderOrg ['moo_id'] );
		$this->_refrence_no_platform = $refrence_no_platform;
		$this->_orderOrg = $orderOrg;
		//print_r($orderOrg);exit;
		$this->_convertOrderProductArr(); 
		$this->_convertOrderRow();
		$this->_convertAddressRow();
		$this->_getOrder();
	}
	/**
	 * 生成订单并预报
	 * @return multitype:number string NULL multitype:
	 */
	public function genOrder(){
		//ini_set('display_errors', true);
  		//error_reporting(E_ALL);
		$return = array('ask'=>0,'message'=>'Fail');
		//$db = Common_Common::getAdapter();
		//$db->beginTransaction();
		try {
				// 数据验证
			$this->_check ();
// 				print_r($this->_addressRow);exit;
			$refrence_no_platform = $this->_refrence_no_platform;
			if (! $this->_order) {
				//没有创建订单，创建操作订单
				//地址
				// 删除shipping_address
				Service_ShippingAddress::delete ( $refrence_no_platform, 'OrderID' );
				
				$this->_addressRow ['OrderID'] = $refrence_no_platform;
				$this->_addressRow ['user_account'] = $this->_user_account;
				$this->_addressRow ['company_code'] = $this->_company_code;
				//$this->_addressRow ['user_account'] = 'peachtao@sina.com';
				//$this->_addressRow ['company_code'] = '10000005';
				
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
				//生成标准订单并预报
				$CustomerId=Service_User::getCustomerId();
				$shipperArr = $this->getShipper();
				$warehouse_code='';
				$verify = New Platform_OrderVerify();
                $verify->setRefId($refrence_no_platform); 
                $verify->setShipper($shipperArr);
                $verify->setProductCode($this->_customerCode);
//                 echo $refrence_no_platform;exit;
// 				$verify->setCustomer_channelid($this->_customer_channelid);//没有该方法
                $verify->setWarehouseCode($warehouse_code);
// 				print_r($warehouse_code)   ;exit;    
				//提交预报        
                $verify->setStatus('P');
                $rs = $verify->process(); 
              
               $shipper_hawbcode=$rs['rs']['order']['shipper_hawbcode'];
				if($rs['ask'] == 1){
					/* 向马帮上传确认信息 */
					
					$csd_order_row = Service_CsdOrder::getByField ( $shipper_hawbcode, 'shipper_hawbcode');
					$params = array (
							'code' => $this->_orderRow['refrence_no'],
							"changeStatus"=>'accept',
							'supplierInnerCode'=>$csd_order_row['server_hawbcode']
					);		
					Mabang_MabangLib::updateOrderStatus($params );
				}
				else
				{
					$vrs=$rs['rs'];
					$message=$rs['message'];
					$separator='：';
					$errors = $vrs['err'];
					if (count($errors) ==1 &&  strpos($errors[0], '客户单号已存在') > -1)
					{
						$csd_order_row = Service_CsdOrder::getByField ( $shipper_hawbcode, 'shipper_hawbcode');

						$params = array (
							'code' => $this->_orderRow['refrence_no'],
							"changeStatus"=>'accept',
							'supplierInnerCode'=>$csd_order_row['server_hawbcode']
						);		
						Mabang_MabangLib::updateOrderStatus($params);
						
						$rs['ask'] = 1;
						$rs['message'] = '订单已经预报成功';
						$updateRow = array (
								'order_status' => '3',
								'abnormal_reason' => '',
								'abnormal_type' => '0',
						);
						Service_Orders::update ( $updateRow, $this->_refrence_no_platform, 'refrence_no_platform' );
						
					}
					else {
						if(! empty($errors)){
							 $message.=":".join(',',$errors);
						}
						$params = array (
							'code' => $this->_orderRow['refrence_no'],
							"changeStatus"=>'exception',
							'processMessage'=>$message
						);	
						Mabang_MabangLib::updateOrderStatus ($params );
					}
				}
				//print_r($rs); 
			} else {
				// 更新平台标记发货信息
				$updateRow = array (
						'platform_ship_status' => $this->_platform_ship_status,
						'platform_ship_time' => $this->_platform_ship_time 
				);				
				Service_Orders::update ( $updateRow, $this->_refrence_no_platform, 'refrence_no_platform' );
			}
			//$db->commit ();
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
		} catch (Exception $e) {
			//$db->rollback();
			Ec::showError('GenOrderException:'.$e->getMessage(), '_mabang_receivelog_' . date('Y-m-d') . "_");
			$params = array (
						'code' => $this->_orderRow['refrence_no'],
						"changeStatus"=>'exception',
						'processMessage'=>'生成运单出错'.$e->getMessage()
				);	
			Mabang_MabangLib::updateOrderStatus ($params );
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
			$upRow = array('is_loaded'=>'2');
			//Service_MabangOrderOriginal::update($upRow, $this->_moo_id,'moo_id');
		Service_MabangOrderOriginal::update($upRow, $this->_moo_id,'moo_id');
// 		print_r($return);exit;
		return $return;
	}

	public function getShipper(){
		$orderOrg = $this->_orderOrg;
		$shipper = array (
				'customer_id' => '',
				'customer_channelid' => '',
				'shipper_account' => '',
				'shipper_name' => $orderOrg['pickup_contact'],
				'shipper_company' => '',
				'shipper_countrycode' => 'CN',
				'shipper_province' => $orderOrg['pickup_province'],
				'shipper_city' => $orderOrg['pickup_city'],
				'shipper_street' => $orderOrg['pickup_area'].' '.$orderOrg['pickup_address'],
				'shipper_postcode' => $orderOrg['pickup_zipcode'],
				'shipper_areacode' => '',
				'shipper_telephone' => $orderOrg['pickup_telephone'],
				'shipper_mobile' => $orderOrg['pickup_mobile'],
				'shipper_email' => '',
				'shipper_certificatetype' => '',
				'shipper_certificatecode' => '',
				'shipper_fax' => '',
				'shipper_mallaccount' => '',
				'is_default' => '',
				'create_date_sys' => '',
				'modify_date_sys' =>'',
				'is_modify' => ''
		);
		return $shipper;
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
				'moo_id',
				'user_account',
				'company_code',
				'order_id' 
		);
		$orderOrgArr = Service_AliexpressOrderOriginal::getByCondition ( $con, $filed );


		foreach ( $orderOrgArr as $orderOrg ) {
			$process = new Aliexpress_Order_GenOrder ();
			$moo_id = $orderOrg ['moo_id']; 
			$process->setAooId ( $moo_id );
			$rs = $process->genOrder ();
			$return [] = $rs;
			Ec::showError(print_r($rs,true),'_aliexpress_gen_order_batch');
// 			exit;
		}
		return $return;
	}
	
}