<?php
/**
 * 查找操作符对应的信息
 * @author Frank
 * @date 2013-9-10 17:43:45 
 *
 */
class Service_MessageTemplateOperateProcess extends Common_Service
{
	/**
	 * 订单信息
	 * @var unknown_type
	 */
	private static $orders = null;
	/**
	 * 订单ID
	 * @var unknown_type
	 */
	private static $orderID = '';
	/**
	 * 第一次查询操作符对应值时，默认不查询的几个操作符
	 */
	private static	$DefaultNotQuery = array(
			'Paypal_ID',
			'Transaction_ID',
			'Shipping'
			);
	
	/**
	 * 获得所有操作符
	 * @return Ambigous <string, mixed>
	 */
	public static function AllMsgTemplateOperate(){
		
   		$resultTplOperate = Service_MessageTemplateOperate::getAll();
   		if(!empty($resultTplOperate)){
   			foreach ($resultTplOperate as $key => $value) {
   				if(in_array($value['operate_code'],self::$DefaultNotQuery)){
   					$resultTplOperate[$key]['firstQuery'] = 'N';
   				}else{
   					$resultTplOperate[$key]['firstQuery'] = 'Y';
   				}
   			}
   		}
   		
   		return $resultTplOperate;
	}
	
	/**
	 * 根据ebay消息ID，已经操作符数组，炒作操作符对应的值
	 * @param unknown_type $ebayMsgId
	 * @param unknown_type $operateArr
	 */
	public static function getMsgTemplateOperatesVal($ebayMsgId,$operateArr){
		$msgRow = Service_EbayMessage::getByField($ebayMsgId,'ebay_message_id');
		
		$return = array();
		foreach ($operateArr as $key => $value) {
			$return[] = self::getMsgTemplateOperateVal($msgRow, $value);
		}
		return $return;
	}
	
	/**
	 * 根据传入操作符查找及message信息查找操作符对应的值
	 * @param unknown_type $msgRowd
	 * @param unknown_type $operate
	 * @return string
	 */
	public static function getMsgTemplateOperateVal($msgRow,$operate){
		self::getOrders($msgRow);
		
		$method = 'get' . $operate;
		$return = self::$method($msgRow);
		$return['code'] = $operate;
		return $return;
	}
	
	/**
	 * 获取订单信息
	 * @param unknown_type $msgRow
	 */
	private static function getOrders($msgRow){
		if(!empty($msgRow['refrence_id']) && $msgRow['refrence_id'] != self::$orders){
			$resultOrders =  Service_Orders::getByField($msgRow['refrence_id']);
			self::$orders = $resultOrders;
			self::$orderID = $msgRow['refrence_id'];
		}else{
			if(!empty($msgRow['sender_id'])){
				$con = array (
						'op_ref_buyer_id' => $msgRow['sender_id'],
						'op_ref_item_id' => $msgRow['item_id']
				);
				$pro = Service_OrderProduct::getByCondition($con,'*',1,1,'op_ref_paydate desc');
				if(!empty($pro)){
					$order = Service_Orders::getByField($pro[0]['order_id'],'order_id');
					self::$orders = $order;
					self::$orderID = $order['order_id'];
				}
			}
		}
	}
	
	/**
	 * 获得买家ID
	 * @param unknown_type $msgRow  ebay消息
	 * @return multitype:
	 */
	private static function getBuyer_ID($msgRow){
		$return = array(
			'ask'=>0,
			'message'=>''
		);
		if(!empty($msgRow['sender_id'])){
			$return['ask'] = 1;
			$return['message'] = $msgRow['sender_id'];
		}else{
			$return['message'] = '未能找到买家ID信息';
		}
		return $return;
	}
	
	/**
	 * 获得买家姓名
	 * @param unknown_type $msgRow
	 */
	private static function getName($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		if(!empty(self::$orders)){
			$return['ask'] = 1;
			$return['message'] = self::$orders['buyer_name'];
		}else{
			$return['message'] = '未能找到关联订单，故没有买家姓名';
		}
		return $return;
	}
	
	/**
	 * 获得买家地址信息
	 * @param unknown_type $msgRow
	 * @return multitype:number string mixed
	 */
	private static function getAddress($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		if(!empty(self::$orders)){
			$shippingAddresId = self::$orders['shipping_address_id'];
			if(!empty($shippingAddresId)){
				$address = '';
				$resultShippingAddress = Service_ShippingAddress::getByField($shippingAddresId);
				if(!empty($resultShippingAddress)){
					$address .= $resultShippingAddress['Street1'];
					$address .= ' ' . $resultShippingAddress['Street2'];
					$address .= ' ' . $resultShippingAddress['StateOrProvince'];
					$address .= ' ' . $resultShippingAddress['CityName'];
					$address .= ' ' . $resultShippingAddress['CountryName'];
					$return['ask'] = 1;
					$return['message'] = $address;
				}
			}
		}
		
		if(!$return['ask']){
			$return['message'] = '未能找到关联订单，故没有买家地址信息';
		}
		return $return;
	}
	
	/**
	 * 获得消息产品标题
	 * @param unknown_type $msgRow
	 * @return multitype:number string unknown
	 */
	private static function getItem_Title($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		if(!empty($msgRow['item_title'])){
			$return['ask'] = 1;
			$return['message'] = $msgRow['item_title'];
		}else{
			$return['message'] = '消息没有产品标题';
		}
		return $return;
	}
	
	/**
	 * 获得订单寄出时间
	 * @param unknown_type $msgRow
	 * @return multitype:number string unknown_type
	 */
	private static function getDispatch_Time($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		if(!empty(self::$orders)){
			if(!empty(self::$orders['date_warehouse_shipping'])){
				$return['ask'] = 1;
				$return['message'] = self::$orders['date_warehouse_shipping'];
			}else{
				$return['message'] = '订单还未有寄出时间';
			}
		}else{
			$return['message'] = '未能找到关联订单，故没有订单寄出日期';
		}
		
		return $return;
	}
	
	/**
	 * 获得订单付款时间
	 * @param unknown_type $msgRow
	 * @return multitype:number string unknown_type
	 */
	private static function getPayment_Time($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		if(!empty(self::$orders)){
			$return['ask'] = 1;
			$return['message'] = self::$orders['date_paid_platform'];
		}else{
			$return['message'] = '未能找到关联订单，故没有订单付款时间';
		}
		
		return $return;
	}
	
	/**
	 * 获得相关订单金额
	 * @param unknown_type $msgRow
	 * @return multitype:number string
	 */
	private static function getPrice_Last($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		if(!empty(self::$orders)){
			$return['ask'] = 1;
			$return['message'] = self::$orders['amountpaid'] . ' ' . self::$orders['currency'];
		}else{
			$return['message'] = '未能找到关联订单，故没有订单金额';
		}
		
		return $return;
	}
	
	/**
	 * 获得客户最近一次的paypal交易ID
	 * @param unknown_type $msgRow
	 * @return multitype:number string mixed
	 */
	private static function getTransaction_ID($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		//查找关联订单的paypal交易ID
		if(!empty(self::$orders)){
			$resultEbayPayment = Service_EbayOrderPayment::getByField(self::$orders['refrence_no_platform'],'OrderID');
			if(!empty($resultEbayPayment)){
				$return['ask'] = 1;
				$return['message'] = $resultEbayPayment['referenceid'];
			}else{
				$return['message'] = '未能根据客户订单找到最近一次的paypal交易ID';
			}
		}else{
			//没有关联订单，查找最近的一个历史订单（60天内）的paypal交易ID
			//$date = date('Y-m-d',strtotime('-60 day'));
			$con = array(
				//'createDateFrom'=>$date,
				'buyer_id'=>$msgRow['sender_id'],
				);
			$resultOrdersRow = Service_Orders::getByCondition($con, $type = '*', $pageSize = 0, $page = 1, $order = "date_create_platform desc");
			if(!empty($resultOrdersRow)){
				$resultEbayPayment = Service_EbayOrderPayment::getByField($resultOrdersRow[0]['refrence_no_platform'],'OrderID');
				if(!empty($resultEbayPayment)){
					$return['ask'] = 1;
					$return['message'] = $resultEbayPayment['referenceid'];
				}
			}
		}
		
		if(!$return['ask'] && empty($return['message'])){
			$return['message'] = '未能找到跟买家有关的订单，故没有客户最近一次的paypal交易ID';
		}
		
		return $return;
	}
	
	/**
	 * 获得客户最近一次的paypal邮箱地址
	 * @param unknown_type $msgRow
	 * @return multitype:number string
	 */
	private static function getPaypal_ID($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		
		//调用客户最近一次的paypal交易ID，
		$returnTmp = self::getTransaction_ID($msgRow);
		if($returnTmp['ask']){
			$transactionID = $returnTmp['message'];
			$resultPaypalTransaction = Service_PaypalTransation::getByField($transactionID,'paypal_tid');
			if(!empty($resultPaypalTransaction)){
				$return['ask'] = 1;
				$return['message'] = $resultPaypalTransaction['pay_account'];
			}else{
				$return['message'] = "未能根据客户Paypal交易ID:$returnTmp[message],找到客户最近使用的Paypal邮箱地址";
			}
		}
		
		if(!$return['ask']){
			$return['message'] = '未能找到跟买家有关的订单，故没有客户最近使用的paypal邮箱地址';
		}
		
		return $return;
	}
	
	/**
	 * 获得最近发出订单日期到回信之间的天数
	 * @param unknown_type $msgRow
	 * @return multitype:number string
	 */
	private static function getDays_Count($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		$con = array(
			'buyer_id'=>$msgRow['sender_id'],
			'item_id' => $msgRow['item_id']
			);
		if(!empty(self::$orderID)){
			$con['not_order_id'] = self::$orderID;
		}
		$resultOrder = Service_Orders::getByCondition($con, $type = '*', $pageSize = 0, $page = 1, $order = "date_create_platform desc");
		if(!empty($resultOrder)){
			$dateShipping = $resultOrder[0]['date_warehouse_shipping'];
			if(!empty($dateShipping)){
				$dateNow = date('Y-m-d');
				$dateShipping = date('Y-m-d',strtotime($dateShipping));
				$days = round((strtotime($dateNow)-strtotime($dateShipping))/3600/24) ;
				$return['ask'] = 1;
				$return['message'] = $days;
			}else{
				$return['message'] = '客户的历史订单中没有订单寄出时间，故不能计算最近订单到回信之间的天数';
			}
		}else{
			$return['message'] = '未能找到跟买家有关的历史订单，故不能计算最近订单到回信之间的天数';
		}
		return $return;
	}
	
	/**
	 * 获得追踪号
	 * @param unknown_type $msgRow
	 * @return multitype:number string
	 */
	private static function getTracking($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		
		$return['ask'] = 1;
		if(!empty(self::$orders) && !empty(self::$orders['shipping_method_no'])){
			$return['message'] = self::$orders['shipping_method_no'];
		}else{
			$return['message'] = 'No Tracking';
		}
		
		return $return;
	}
	
	/**
	 * 获得历史订单总金额
	 * @param unknown_type $msgRow
	 * @return multitype:number string
	 */
	private static function getPrice_History($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		$con = array(
				'buyer_id'=>$msgRow['sender_id'],
				'item_id' => $msgRow['item_id']
		);
		//最近一个的一个历史订单，包括关联订单，时间往前推15天以内的
// 		if(!empty(self::$orderID)){
// 			$con['not_order_id'] = self::$orderID;
// 		}
		$resultOrder = Service_Orders::getByCondition($con, $type = '*', $pageSize = 0, $page = 1, $order = "date_create_platform desc");
		if(!empty($resultOrder)){
			$num = 0;
			foreach ($resultOrder as $key => $value) {
				$num = $num + $value['amountpaid'];
			}
			$num = number_format($num,2);
			$return['ask'] = 1;
			$return['message'] = $num;
		}else{
			$return['message'] = '未能找到跟买家有关的历史订单，故不能计算历史订单总金额';
		}
		
		return $return;
	}
	
	/**
	 * 获得订单寄出方式
	 * @param unknown_type $msgRow
	 * @return multitype:number string
	 */
	private static function getShipping($msgRow){
		$return = array(
				'ask'=>0,
				'message'=>''
		);
		
		if(!empty(self::$orders)){
			$warehouseId = self::$orders['warehouse_id'];
			$shippingMethodPlatform = self::$orders['shipping_method_platform'];
			$con = array(
				'platform_shipping_mark' => $shippingMethodPlatform,
				'warehouse_id' => $warehouseId
				);
			$resultShippingMethod = Service_ShippingServiceMapping::getByCondition($con);
			if(!empty($resultShippingMethod)){
				$return['ask'] = 1;
				$return['message'] = $resultShippingMethod[0]['warehouse_shipping_service'];
			}else{
				$return['message'] = '未能找到对应的订单寄出方式';
			}
		}else{
			$return['message'] = '未能找到跟买家有关的订单，故不能查找订单寄出方式';
		}
		
		return $return;
	}
}