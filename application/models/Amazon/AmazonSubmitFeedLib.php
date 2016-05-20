<?php
/**
 * amazon  SubmitFeed接口服务类
 * @author Frank
 * @date 2013-11-20 16:52:52
 */
class Amazon_AmazonSubmitFeedLib{
	/**
	 * amazon api版本号
	 * @var unknown_type
	 */
	const SERVICE_VERSION = '2009-01-01';
	
	/**
	 * 应用名称/版本
	 * @var unknown_type
	 */
	const APPLICATION_NAME = 'ECPhpScratchpad';
	const APPLICATION_VERSION = '1.0';
	
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(60);
	}
		
	/**
	 * 获得亚马逊站点的服务地址及商城代码
	 */
	public static function getAmazonConfig(){
		$configArr = array(
				//北美
				'CA'=>array('marketplace_id'=>'A2EUQ1WTGCTBG2'
						,'service_url'=>'https://mws.amazonservices.ca'),
				'US'=>array('marketplace_id'=>'ATVPDKIKX0DER'
						,'service_url'=>'https://mws.amazonservices.com'),
				//欧洲
				'DE'=>array('marketplace_id'=>'A1PA6795UKMFR9'
						,'service_url'=>'https://mws.amazonservices.de'),
				'ES'=>array('marketplace_id'=>'A1RKKUPIHCS9HS'
						,'service_url'=>'https://mws-eu.amazonservices.com'),
				'FR'=>array('marketplace_id'=>'A13V1IB3VIYZZH'
						,'service_url'=>'https://mws.amazonservices.fr'),
				'IN'=>array('marketplace_id'=>'A21TJRUUN4KGV'
						,'service_url'=>'https://mws.amazonservices.in'),
				'IT'=>array('marketplace_id'=>'APJ6JRA9NG5V4'
						,'service_url'=>'https://mws.amazonservices.it'),
				'UK'=>array('marketplace_id'=>'A1F83G8C2ARO7P'
						,'service_url'=>'https://mws.amazonservices.co.uk'),
				//远东(叼毛)
				'JP'=>array('marketplace_id'=>'A1VC38T7YXB528'
						,'service_url'=>'https://mws.amazonservices.jp'),
				//中国
				'CN'=>array('marketplace_id'=>'AAHKV2X7AFYLW'
						,'service_url'=>'https://mws.amazonservices.com.cn'),
			);
		return $configArr;
	}
	
	/**
	 * 根据上传数据类型，获取报告下载路径
	 */
	public static function getReportPath($FeedType){
		$configArray = array(
				'_POST_ORDER_FULFILLMENT_DATA_'			=>	APPLICATION_PATH . '/../data/xml/amazon/OrderFulfillment',		//确认订单配送
				'_GET_PAYMENT_SETTLEMENT_DATA_'  		=>  APPLICATION_PATH . '/../data/xml/amazon/PaymentSettlement' 		//订单交易费用
				);
		;
		$path = isset($configArray[$FeedType])?$configArray[$FeedType]:'';
		return $path;
	}
	
	/**
	 * 将订单信息，Items信息封装成amazon所需要的xml信息
	 * @param unknown_type $sellerId
	 * @param unknown_type $resultOrders
	 * @param unknown_type $resultOrderProduct
	 */
	public static function getOrderFulfillmentXML($sellerId,$resultOrders,$resultOrderProduct,$resultSMP){
		$return = array(
				'ask'=>0,
				'data'=>array(),
				'xml'=>''
		);
		
		//使用平台参考号作为key
		$orderArr = array();
		foreach ($resultOrders as $orderKey => $orderValue) {
			$orderArr[$orderValue['refrence_no']] = $orderValue;
		}
		
		//使用amazon订单ID作为key，将需要标记的sku分类到订单下面
		$orderProduct = array();
		foreach ($resultOrderProduct as $productKey => $productValue) {
			$orderProduct[$productValue['OrderIDEbay']][] = $productValue;
		}
		
		//使用运输方式代码作为key，需要运输方式对应的承运商以及发货方式
		$SMPArr = array();
		foreach ($resultSMP as $SMPkey => $SMPvalue) {
			$SMPArr[$SMPvalue['shipping_method_code']] = $SMPvalue;
		}
		
		//xml头
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
						<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
		//销售ID
		$xml .= '<Header>
					<DocumentVersion>1.01</DocumentVersion>
			        <MerchantIdentifier>' . $sellerId . '</MerchantIdentifier>
			     </Header>
			     <MessageType>OrderFulfillment</MessageType>';
		
		//xml主体
		$index = 1;
		$mappingArr = array();
		$platformShipTimeArr = array();
		//设置一个默认发货时间（优先使用订单的发货时间）
		$fulfillmentDate = date('Y-m-d H:i:s');
// 		echo '$fulfillmentDate:' . $fulfillmentDate . '<br/>';
		$fulfillmentDate_platform = new DateTime($fulfillmentDate, new DateTimeZone('UTC'));
		$fulfillmentDate_platform = $fulfillmentDate_platform->format(DateTime::ISO8601);
		$fulfillmentDate_platform = str_replace("+0000", "+08:00", $fulfillmentDate_platform);
		foreach ($orderProduct as $listKey => $listValue) {
			foreach ($listValue as $itemKey => $itemValue) {
				$OrderID = $itemValue['OrderID'];
				$AmazonOrder = $orderArr[$OrderID];
				$AmazonOrderItemCode = $itemValue['op_ref_tnx'];
				$AmazonOrderItemQTY = $itemValue['op_quantity'];
				if(empty($AmazonOrderItemCode)){
					//没有平台的orderItemId，不用处理标记发货
					continue;
				}
				$row = array();
				if(!empty($AmazonOrder)){
					//检查订单标记发货时间和仓库发货时间是否为空，不为空，作为订单的发货时间
					$order_fulfillmentDate = '';
					if(!empty($AmazonOrder['platform_ship_time']) && strtotime($AmazonOrder['platform_ship_time'])>strtotime('2000-01-01')){
						$order_fulfillmentDate = $AmazonOrder['platform_ship_time'];
// 						echo '$platform_ship_time:' . $order_fulfillmentDate . '<br/>';
					}else if(!empty($AmazonOrder['date_warehouse_shipping']) && strtotime($AmazonOrder['date_warehouse_shipping'])>strtotime('2000-01-01')){
						$order_fulfillmentDate = $AmazonOrder['date_warehouse_shipping'];
// 						echo '$date_warehouse_shipping:' . $order_fulfillmentDate . '<br/>';
					}
// 					echo '$order_fulfillmentDate:' . $order_fulfillmentDate . '<br/>';
					
					if(!empty($order_fulfillmentDate)){
						$fulfillmentDate_platform = new DateTime($order_fulfillmentDate, new DateTimeZone('UTC'));
						$fulfillmentDate_platform = $fulfillmentDate_platform->format(DateTime::ISO8601);
						$fulfillmentDate_platform = str_replace("+0000", "+08:00", $fulfillmentDate_platform);
						$fulfillmentDate = $order_fulfillmentDate;
					}
// 					echo '最后：'.$fulfillmentDate.'<br/>';
// 					echo '最后：'.$fulfillmentDate_platform.'<br/>';
					
					$carrierName = (!empty($AmazonOrder['carrier_name'])?$AmazonOrder['carrier_name']:$AmazonOrder['shipping_method_platform']);//承运商
					$shippingMethod = $AmazonOrder['ship_service_level'];
					$objShippingMethod = $SMPArr[$AmazonOrder['shipping_method']];
					$shippingMethod = (!empty($objShippingMethod))?$objShippingMethod['platform_shipping_mark']:'';
					$trackNo = $AmazonOrder['shipping_method_no'];
					$xml .= '<Message>
							<MessageID>' . $index . '</MessageID>
							<OperationType>Update</OperationType>
							<OrderFulfillment>
								<AmazonOrderID>' . $listKey . '</AmazonOrderID>
								<FulfillmentDate>' . $fulfillmentDate_platform .  '</FulfillmentDate>
								<FulfillmentData>
									<CarrierName>' . $carrierName . '</CarrierName>';
		            if(!empty($shippingMethod)){
						$xml .= '<ShippingMethod>' . $shippingMethod . '</ShippingMethod>';
		            }
		            
					if(!empty($trackNo)){
						$xml .= 	'<ShipperTrackingNumber>' . $trackNo . '</ShipperTrackingNumber>';
					}
					$xml .=		'</FulfillmentData>
								<Item>
									<AmazonOrderItemCode>' . $AmazonOrderItemCode . '</AmazonOrderItemCode>
								    <Quantity>' . $AmazonOrderItemQTY . '</Quantity>
								</Item>
							</OrderFulfillment>
						</Message>';
					$row['message_id'] = $index;
					$row['mapped_val1'] = $OrderID;				//记录为系统单号
					$row['mapped_val2'] = $itemValue['op_id'];	//记录为order_product_id
					$row['mapped_val3'] = $AmazonOrderItemCode; //记录为亚马逊的amazon_order_item_code
					$row['tracking_no'] = (!empty($trackNo))?$trackNo:'';
					$mappingArr[$OrderID][] = $row;						//映射记录
					$platformShipTimeArr[$OrderID] = $fulfillmentDate;	//发货时间记录
					$index++;
				}
			}
		}
		$xml .= '</AmazonEnvelope>';
		 
		if($index != 1){
			$return['ask'] = 1;
			$return['data'] = $mappingArr;
			$return['shipTime'] = $platformShipTimeArr;
			$return['xml'] = $xml;
		}
		
		return $return;
	}
}