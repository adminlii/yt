<?php
/**
 * 生成Amazon系统订单
 * @author Max
 * @date 2014-12-30 00:42:48
 */
class Amazon_Order_GenOrder{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private  $log_name = 'AmazonGenerateSystemOrders_';

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
	private $_orderPaymentOrg = array();
	private $_platform_ship_status = 0;
	private $_platform_ship_time='';
	
	private $_abnormal_reason = '';
	
	private $_site = '';
	
	public function setAooId($aoo_id) {
		$this->_aoo_id = $aoo_id;
	}
	
	private function _getOrder(){
		if(empty($this->_refrence_no_platform)||empty($this->_refrence_no)){
			throw new Exception('refrence_no_platform/refrence_no未设置');
		}
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
			if($order['order_status']==1){
				Service_Orders::delete($order['order_id'],'order_id');
				Service_OrderProduct::delete($order['order_id'],'order_id');
				Service_ShippingAddress::delete($order['refrence_no_platform'],'OrderID');
			}else{
				$this->_order = $order;
			}
			
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
				if ($order ['order_status'] == 1) {
					Service_Orders::delete ( $order ['order_id'], 'order_id' );
					Service_OrderProduct::delete ( $order ['order_id'], 'order_id' );
					Service_ShippingAddress::delete ( $order ['refrence_no_platform'], 'OrderID' );
				} else {
					$this->_order = $order;
				}
			}
		}
	}
 
	private function getSite(){
		if (! $this->_orderOrg) {
			throw new Exception ( 'orderOrg没有设置' );
		}
		$orderOrg = $this->_orderOrg;
		$site = Amazon_AmazonLib::getSiteByMarketplaceId ( $orderOrg ['marketplace_id'] );
		$this->_site = $site;
	}
	
	private function _check(){
		$aoo_id = $this->_aoo_id ;
		if(!$this->_aoo_id){
			throw new Exception('没有传入参数');
		}
		$orderOrg = Service_AmazonOrderOriginal::getByField($aoo_id,'aoo_id');
		if(!$orderOrg){
			throw new Exception('订单原始信息不存在');
		}
		$order_id = $orderOrg ['order_id'];
	
		$this->_user_account = $orderOrg['user_account'];
		$this->_company_code = $orderOrg['company_code'];
		$this->_refrence_no = $orderOrg['amazon_order_id'];
		$refrence_no_platform = 'AM' . sprintf ( '%010s', $orderOrg ['aoo_id'] );
		$this->_refrence_no_platform = $refrence_no_platform;
		$this->_orderOrg = $orderOrg;
	
		$this->getSite();
		
		$this->convertOrderProduct();
		$this->conventOrders();
		$this->convertShippingAddress();
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
				//发货状态
				$this->_orderRow['platform_ship_status'] =  $this->_platform_ship_status;
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
							'platform_ship_status' => $this->_platform_ship_status,
							'platform_ship_time' => $this->_platform_ship_time
					);
					Service_Orders::update ( $updateRow, $refrence_no_platform, 'refrence_no_platform' );
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

		// 更新原始信息状态
		if ($err_msg && preg_match ( '/SQLSTATE/', $err_msg )) {
				
		}else{
			$upRow = array('is_loaded'=>'2');
			Service_AmazonOrderOriginal::update($upRow, $this->_aoo_id,'aoo_id');
		}
		return $return;
	}
	
	
	/**
	 * 封装amazon订单信息，为系统订单
	 */
	public function conventOrders(){
		if (! $this->_orderOrg) {
			throw new Exception ( 'orderOrg没有设置' );
		}
		$orderOrg = $this->_orderOrg;
		
		if (! $this->_orderProductOrg) {
			throw new Exception ( 'orderProductOrg没有设置' );
		}
		$resultAmazontOrderDetail = $this->_orderProductOrg;
		
		$site = $this->_site;
		
		$row = array ();
		$row ['platform'] = 'amazon';
		$row ['data_source'] = 'amazon';
		$row ['order_type'] = 'sale';
		$row ['create_type'] = 'api';
		$fulfillment_channel = $orderOrg ['fulfillment_channel'];
		if ($fulfillment_channel == 'AFN') { // 亚马逊配送直接转到已发货
			$row ['order_status'] = '2';
			$row ['sync_status'] = '1';
			$row ['sync_time'] = date ( 'Y-m-d H:i:s' );
			$this->_platform_ship_status = '1';
			$this->_abnormal_reason = '当前订单状态OrderStatus:'.$orderOrg ['order_status'].',配送渠道FulfillmentChannel:'.$orderOrg ['fulfillment_channel'].'亚马逊配送';
		} else {
			// array('Unshipped','PartiallyShipped','Shipped')
			if ($orderOrg ['order_status'] == 'Shipped' || $orderOrg ['order_status'] == 'PartiallyShipped') {
				$row ['order_status'] = '2';
				$row ['sync_status'] = '1';
				$this->_platform_ship_status = '1';
				$this->_abnormal_reason = '当前订单状态OrderStatus:'.$orderOrg ['order_status'].',配送渠道FulfillmentChannel:'.$orderOrg ['fulfillment_channel'].',订单已经发货';
			} else if($orderOrg ['order_status']=='Unshipped') {
				$row ['order_status'] = '2';
				$row ['sync_status'] = '0';
				$this->_abnormal_reason = '当前订单状态OrderStatus:'.$orderOrg ['order_status'].',配送渠道FulfillmentChannel:'.$orderOrg ['fulfillment_channel'].'';
			}else {
				$row ['order_status'] = '1';
				$row ['sync_status'] = '0';
			}
			$row ['sync_time'] = '';
		}
		$row ['create_method'] = '2';

		$row ['abnormal_type'] = '';
		$row ['abnormal_reason'] = $this->_abnormal_reason;
		$row ['customer_id'] = '';
		$row ['user_account'] = $this->_user_account;
		$row ['company_code'] = $this->_company_code;
		$row ['shipping_method'] = '';
		$row ['shipping_method_platform'] = '';
		$row ['warehouse_id'] = '';
		$row ['order_desc'] = '';
		$row ['date_create'] = date ( 'Y-m-d H:i:s' );
		$row ['date_release'] = '';
		$row ['date_warehouse_shipping'] = '';
		$row ['date_last_modify'] = date ( 'Y-m-d H:i:s' );
		$row ['operator_id'] = '';
		$row ['refrence_no'] = $this->_refrence_no;
		$row ['refrence_no_platform'] = $this->_refrence_no_platform;
		$row ['refrence_no_sys'] = '';
		$row ['shipping_address_id'] = '';
		$row ['refrence_no_warehouse'] = '';
		$row ['shipping_method_no'] = '';
		$row ['date_create_platform'] = $orderOrg ['purchase_date'];
		$row ['date_paid_platform'] = $orderOrg ['last_update_date'];
		$row ['date_paid_int'] = '';
		
		$totalAmount = 0;
		$totalShippingAmount = 0;
		$totalQty = 0;
		$is_one_piece = '1';
		foreach ( $resultAmazontOrderDetail as $itemsKey => $itemsValue ) {
			$totalAmount += $itemsValue ['item_price_amount'] + $itemsValue ['shipping_price_amount'];
			$totalShippingAmount += $itemsValue ['shipping_price_amount'];
			$totalQty += $itemsValue ['quantity_ordered'];
		}
		if ($totalQty > 1) {
			$is_one_piece = '0';
		}
		
		$row ['amountpaid'] = $totalAmount; // 包运费
		$row ['subtotal'] = round ( ($orderOrg ['amount'] - $totalShippingAmount), 3 ); // 不包运费
		$row ['ship_fee'] = $totalShippingAmount; // 运费
		
		$row ['platform_fee'] = '0'; // 平台费用
		$row ['currency'] = $orderOrg ['currency_code'];
		$row ['buyer_id'] = preg_replace ( '/\s+/', '', strtolower ( $orderOrg ['buyer_name'] ) ); // amazon订单，无买家id，以收件人姓名为准
		$row ['third_part_ship'] = '0'; // 是否第三方仓库发货，默认0，审核订单的时候改变
		$row ['is_merge'] = '0';
		$row ['site'] = $orderOrg ['sales_channel'];
		$row ['consignee_country'] = $orderOrg ['shipping_address_country_code'];
		$row ['order_weight'] = '';
		$row ['buyer_name'] = $orderOrg ['buyer_name'];
		$row ['buyer_mail'] = $orderOrg ['buyer_email'];
		$row ['has_buyer_note'] = '';
		
		$row ['is_one_piece'] = $is_one_piece; // 判断一下items
		$row ['operator_note'] = '';
		$row ['product_count'] = $totalQty; // 产品数量
		$row ['fulfillment_channel'] = $orderOrg ['fulfillment_channel'];
		$row ['process_again'] = ($orderOrg ['fulfillment_channel'] == 'AFN') ? '2' : '1'; // 在处理标记，AFN是FBA订单，需要再次运行传入到仓库
		$row ['ship_service_level'] = $orderOrg ['ship_service_level'];
		$row ['shipment_service_level_category'] = $orderOrg ['shipment_service_level_category'];
		
		$this->_orderRow = $row;
		return $row;
	}
	
	/**
	 * 封装amazon订单地址信息，为系统地址
	 */
	public function convertShippingAddress(){
		if (! $this->_orderOrg) {
			throw new Exception ( 'orderOrg没有设置' );
		}
		$orderOrg = $this->_orderOrg;
		$row = array ();
		if(empty($orderOrg['shipping_address_country_code'])){
			throw new Exception ( '订单无配送国家' );			
		}
		if(empty($orderOrg['shipping_address_address1'])&&empty($orderOrg['shipping_address_address2'])&&empty($orderOrg['shipping_address_address3'])){
			throw new Exception ( '订单无配送地址' );			
		}
		if(empty($orderOrg['shipping_address_name'])){
			throw new Exception ( '订单无收件人' );			
		}
		
		$row ['Name'] = $orderOrg ['shipping_address_name'];
		$row ['Street1'] = $orderOrg ['shipping_address_address1'];
		$row ['Street2'] = $orderOrg ['shipping_address_address2'] . ' ' . $orderOrg ['shipping_address_address3'];
		// $row['Street3'] = $orderOrg['shipping_address_address3'];
		$row ['CityName'] = $orderOrg ['shipping_address_city'];
		$row ['StateOrProvince'] = $orderOrg ['shipping_address_state'];
		$row ['Country'] = $orderOrg ['shipping_address_country_code'];
		
		$resultCountry = Service_Country::getByField ( $orderOrg ['shipping_address_country_code'], 'country_code' );
		$row ['CountryName'] = $resultCountry ? $resultCountry ['country_name_en'] : $orderOrg ['shipping_address_country_code'];
		
		$row ['District'] = $orderOrg ['shipping_address_district'];
		$row ['Phone'] = $orderOrg ['shipping_address_phone'];
		$row ['PostalCode'] = $orderOrg ['shipping_address_postal_code'];
		$row ['AddressID'] = '';
		$row ['AddressOwner'] = '';
		$row ['ExternalAddressID'] = '';
		$row ['OrderID'] = $this->_refrence_no_platform;
		$row ['Plat_code'] = 'amazon';
		$row ['company_code'] = $this->_company_code;
		$row ['user_account'] = $this->_user_account;
		$row ['create_date_sys'] = date ( 'Y-m-d H:i:s' );
		$row ['modify_date_sys'] = date ( 'Y-m-d H:i:s' );
		$row ['is_modify'] = '0';
		$this->_addressRow = $row;
		return $row;
	}
	
	/**
	 * 封装amazon订单Itmes信息，为系统单身
	 */
	public function convertOrderProduct(){
		if(!$this->_orderOrg){
			throw new Exception('orderOrg没有设置');
		}
		$orderOrg = $this->_orderOrg;
		$con = array (
				'aoo_id' => $orderOrg ['aoo_id']
		);
		$detailArr = Service_AmazonOrderDetail::getByCondition ( $con );
// 		print_r($detailArr);exit;
		if(empty($detailArr)){
			throw new Exception('订单明细不存在');
		}
		$this->_orderProductOrg = $detailArr;
		
		$row = array(); 
		foreach ($detailArr as  $p) {
			$product = array();
			$product['order_id'] = 			'';
			$product['product_id'] = 		'0';
			$product['product_sku'] = 		$p['seller_sku']?$p['seller_sku']:'-No-SKU-';
			$product['warehouse_sku'] = 	'';
			$product['product_title'] = 	$p['title'];
			$product['op_quantity'] = 		$p['quantity_ordered'];
			$product['op_ref_tnx'] = 		$p['order_item_id'];
			$product['op_recv_account'] = 	'';
			$product['op_ref_item_id'] = 	$p['order_item_id'];
			$product['op_site'] = 			$this->_site;
			$product['op_record_id'] = 		'';
			$product['op_ref_buyer_id'] = 	'';
			$product['op_ref_paydate'] = 	'';
			$product['op_add_time'] = 		date('Y-m-d H:i:s');
			$product['op_update_time'] = 	date('Y-m-d H:i:s');
			$product['OrderID'] = 			$this->_refrence_no_platform;
			$product['OrderIDEbay'] = 		$this->_refrence_no;
			$product['is_modify'] = 		'0';
			$product['pic'] = 				'';
			$product['url'] = 				'';
			$product['unit_price'] = 		$p['item_price_amount'] / ($p['quantity_ordered']?$p['quantity_ordered']:1);
			$product['give_up'] = 			($p['quantity_ordered'] == 0)?1:0;   //是否废弃，item数量为0时，设置为1：表示该item不发货
			$product['currency_code'] = 	$p['item_price_currency_code'];
			$row[] = $product;
		} 
		$this->_orderProductArr = $row;
		return $row;
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
				'amazon_order_id'
		);
		$orderOrgArr = Service_AmazonOrderOriginal::getByCondition ( $con, $filed );
	
		foreach ( $orderOrgArr as $orderOrg ) {
			$process = new Amazon_Order_GenOrder ();
			$aoo_id = $orderOrg ['aoo_id'];
			$process->setAooId ( $aoo_id );
			$rs = $process->genOrder ();
			Common_ApiProcess::log('AmazonOrderId['.$orderOrg['amazon_order_id'].'],ask:'.$rs['ask'].',message:'.$rs['message']);//exit;
			$return [] = $rs;
			Ec::showError(print_r($rs,true),'_amazon_gen_order_batch');
			// 			exit;
		}
		return $return;
	}
	
}