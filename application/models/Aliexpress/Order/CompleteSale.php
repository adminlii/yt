<?php
/**
 * 速卖通标记发货
 * @author Qinxh
 *
 */
class Aliexpress_Order_CompleteSale
{
    //订单号
    private $_refrence_no_platform = '';
    
    // 订单
    private $_order = array();
    
    // 错误
    private $_errArr = array();

    public function __construct($refrence_no_platform)
    {
        $this->_refrence_no_platform = $refrence_no_platform;
    }
    
    private function _getOrder()
    {
        $field = array(
            'user_account',
            'date_warehouse_shipping',
            'shipping_method_no',
            'shipping_method',
            'refrence_no_platform',
            'refrence_no',
            'order_status',
            'sync_status',
            'shipping_method_platform',
            'platform',
            'data_source',
            'carrier_name',
            'platform_ship_time'
        );
        
        $this->_order = Service_Orders::getByField($this->_refrence_no_platform, 'refrence_no_platform', $field);
        if($this->_order){
            if($this->_order['order_status']=='0'){
                throw new Exception('已废弃订单不可标记发货');
            }
        }
    }

    private function _completeSaleOrder()
    {
    	
    	// 账号
    	$user_account = $this->_order['user_account'];
    	
    	/*
		 * 1.查询Aliexpress授权信息
		*/
		$result_PlatformUser = Service_PlatformUser::getByField($user_account,'user_account');
		
		if(empty($result_PlatformUser)){
			$errorMessage = "Aliexpress账户：$user_account 未维护签名信息，请维护！";
			throw new Exception($errorMessage);
		}else if($result_PlatformUser['status'] != 1){
			$errorMessage = "Aliexpress账户：$user_account 未生效";
			throw new Exception($errorMessage);
		}
		
		
		/*
		 * 2. 检查Token是否过期
		* 是：更新，并返回最新授权信息
		* 否：直接返回
		*/
		try {
			$result_PlatformUser = Aliexpress_AliexpressService::checkAliexpressToken($result_PlatformUser['pu_id']);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
				
		$shipment_row = array(
				'ref_code'=> $this->_order['refrence_no'],		//订单号
				'aliexpress_id' => '',							//速卖通订单号
				'service_name' => $this->_order['carrier_name'],//承运商
				'tracking_no' => $this->_order['shipping_method_no'],				//跟踪号
				'tracking_website' => '',						//当service_name=other的情况时，需要填写对应的追踪网址
				'send_type' => '',								//发货类型，ALL：全部发货，PART：部分发货
				'flag'=>'0',									//跟踪号类型，标志，0：正常跟踪号标记，1：模拟跟踪号
		);
		$old_shipment_row = array();								//上一次的发货记录
		
		// 当承运商为OTHER时,取标发地址
		if(empty($shipment_row['service_name']) || $shipment_row['service_name'] == 'OTHER') {
			$sql = "select * from pbr_product_platform_map m where m.product_code = '{$this->_order['shipping_method']}' and m.platform = 'aliexpress';";
			$row = Common_Common::fetchRow($sql);
			
			if(!empty($row)) {
				$shipment_row['service_name'] = (empty($shipment_row['service_name']) ? $row['carrier_name'] : $shipment_row['service_name']);
				if($shipment_row['service_name'] == 'OTHER') {
					$shipment_row['tracking_website'] = $row['track_web_site'];
				}
			}
			
		}
		
		$result_aliexpress_order = Service_AliexpressOrderOriginal::getByField($this->_order['refrence_no'], 'order_id');
		if(!empty($result_aliexpress_order)){
			//查询订单标记发货次数,最后标记的记录，排在最前面
			$con_shipment_list = array(
					'aliexpress_id'=>$this->_order['refrence_no'],
					'sync_status'=>'SC200_RSC200_RRS1',		//同步成功的标志
			);
			$result_shipment_list = Service_AliexpressShipmentList::getByCondition($con_shipment_list, '*', 0, 1, "asl_id desc");
			$old_shipment_row = $result_shipment_list[0];
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
			$shipment_row['send_type'] = 'ALL';
			$shipment_row['aliexpress_id'] = $this->_order['refrence_no'];
		}else{
			throw new Exception('未能找到原始订单信息，单号：' . $this->_order['refrence_no']);
		}
		
		$model = Service_Orders::getModelInstance();
		$db = $model->getAdapter();
		$db->beginTransaction();
		try {
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
			switch ($shipment_type){
				case 'sellerShipment':		//首次标记发货
					$params = array(
							'outRef'=>$orderId,						//【必填】速卖通订单号
							'serviceName'=>$serviceName,			//【必填】用户选择的实际发货物流服务（物流服务key：该接口根据api.listLogisticsService列出平台所支持的物流服务 进行获取目前所支持的物流。）
							'logisticsNo'=>$trackNo,				//【必填】物流追踪号
							'description'=>'',						//备注(只能输入英文，且长度限制在512个字符。）
							'sendType'=>$sendType,					//【必填】状态包括：全部发货(all)、部分发货(part)
							'trackingWebsite'=>$trackingWebsite,	//当serviceName=other的情况时，需要填写对应的追踪网址
							'access_token'=>$access_token,			//【必填】Token
					);
		
					$orders_update_row = array();
					$response_shipment_call = Aliexpress_AliexpressLib::sellerShipment($app_key, $app_secret, $params);
					$order_log_content = 'Aliexpress订单标记发货成功，跟踪号：' . $trackNo;
					break;
				case 'sellerModifiedShipment':		//修改标记发货
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
		
					$orders_update_row = array();
					$response_shipment_call = Aliexpress_AliexpressLib::sellerModifiedShipment($app_key, $app_secret, $params);
					$order_log_content = 'Aliexpress订单修改标记发货成功，New跟踪号：' . $trackNo . ' Old跟踪号：' . $oldLogisticsNo;
					break;
				default:
					throw new Exception('未定义的标记发货方式：' . $shipment_type);
					break;
			}
		
// 			$a = Array ( 
// 					[InvokeStartTime] => 20150807224807944-0700 
// 					[InvokeCostTime] => 21 
// 					[Status] => Array ( 
// 							[Code] => 500 
// 							[Message] => Internal Server Error ) 
// 					[Responses] => Array ( 
// 											[0] => Array ( 
// 												[Status] => Array ( [Code] => 500 
// 																	[Message] => Internal Server Error )
// 												[error_code] => 15-1006 
// 											    [error_message] => Tracking website cannot be null when using seller's shipping method! 
// 											    [exception] => Tracking website cannot be null when using seller's shipping method! 
// 										) 
// 					) 
// 			);
			if(!empty($response_shipment_call) && $response_shipment_call['Status']['Code'] == '200'
					&& $response_shipment_call['Responses'][0]['Status']['Code'] == '200' 
					&& $response_shipment_call['Responses'][0]['Result']['success'] == '1') {
				
				//成功--记录日志
				$logRow = array(
						'ref_id' => $this->_refrence_no_platform,
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
				
				Service_Orders::update($orders_update_row, $this->_refrence_no_platform,'refrence_no_platform');
			} else {
// 				print_r($response_shipment_call);die;
				throw new Exception($response_shipment_call['Responses'][0]['error_message']);
			}
			
			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			// 异常
			throw new Exception($e->getMessage());
		}
    }

    /**
     * 标记发货
     *
     * @return Ambigous <multitype:number string unknown , NULL, multitype:>
     */
    public function completeSale()
    {
        $return = array(
            'ask' => 0,
            'message' => '',
            'ref_id' => $this->_refrence_no_platform,
            'Ack' => 'Failure'
        );
        try{
            if(empty($this->_refrence_no_platform)){
                throw new Exception('参数订单号错误');
            }
            
            $this->_getOrder();
            
            if(empty($this->_order)){
                throw new Exception('订单不存在');
            }
            
            $this->_completeSaleOrder();
            
            $logRow = array(
                'ref_id' => $this->_refrence_no_platform,
                'log_content' => $message,
                'data' => implode("\n", $this->_logArr),
                'op_id' => ''
            );
            Service_OrderProcess::writeOrderLog($logRow);
            
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['Ack'] = 'Success';
        }catch(Exception $e){            
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }

}