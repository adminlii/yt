<?php
/**
 * API换号工厂类
 * @author Administrator
 *
 */
class API_Common_AsyncChangeNo
{

    /**
     * @desc 预报换号
     * @param $orderCode
     * @return string
     */
    public function changeNOByForecast($orderId, $orderCode, $formalCode,$stringId=array(""),$shipperCode,$uuid)
    {
        // 返回结果
    	$return = array('ack' => '0', 
    					"type"=> 0,
		    			'message' => '', 
		    			'trackingNumber' => '', 
    			);
    	
    	try {
    		
    		// 获取服务处理类
			//所有渠道都走API_YunExpress_ForApiService
			$channelCode = "YUNEXPRESS";
    		$objCommon = new API_Common_ServiceCommonClass();
    		$channel = $objCommon->getServiceChannelByFormalCode($formalCode);
    		if (empty($channel)) {
    			throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
    		}
    		//日志记录start
    		$logrow = array();
    		$logrow['requestid'] = $uuid;
    		switch($formalCode){
    			case "ESB":$_type = 1;break;
    			case "DHL":$_type = 3;break;
    			case "TNT":$_type = 4;break;
    			default:$_type=2;break;
    		}
    		$logrow['type'] = $_type;
    		$logrow['detail'] = '异步请求创建标签开始step1';
    		list($usec, $sec) = explode(" ", microtime());
            $logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
    		$db = Common_Common::getAdapter();
    		$db ->insert('logapi', $logrow);
    		//日志记录end
    		//异步模式换号
    		$this->getChangeNo($orderId,$shipperCode,$channel['server_channelid'],$formalCode,$uuid);
    		/* // 当换号模式为延时时，插入换号记录由延时服务去换号
    		if($channel['as_docking_mode'] == '1' || $channel['as_docking_mode'] == '2') {
    			 $order_process = array(
    			 		'order_id' => $orderId,
    			 		'server_channelid' => $channel['server_channelid'],
    			    	'shipper_hawbcode' => $shipperCode,
    			    	'formal_code' => $formalCode,
    			    	'ops_create_date' => date('Y-m-d H:i:s'),
    			 );
    			 Service_OrderProcessing::add($order_process);
    			    	
    			 // 返回
    			 $return['trackingNumber'] = $orderCode;
    			 $return['type'] = $channel['as_docking_mode'];
    			 $return['ack'] = 1;
    			 return $return;
    		}
    		 */
    		$return['ack'] = 1;
    		$return['type'] = 1;
    		$return['trackingNumber']= '';
    	} catch (Exception $e) {
    		$return['ack'] = 0;
    		$return['message'] = $e->getMessage();
    	}
    	
    	return $return;
        
    }
    
    //异步换号
    public function getChangeNo($orderid,$shipperCode,$server_channelid,$formalCode,$uuid){
    	 // 返回结果
    	$return = array('ack' => '0', 
    					"type"=> 0,
		    			'message' => '', 
		    			'trackingNumber' => '', 
    			);
    	
    	try {
    		//日志记录start
    		$logrow = array();
    		$logrow['requestid'] = $uuid;
    		$logrow['type'] = 1;
    		$logrow['detail'] = '异步请求创建标签开始step2';
    		list($usec, $sec) = explode(" ", microtime());
            $logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
    		$db = Common_Common::getAdapter();
    		$db ->insert('logapi', $logrow);
    		//日志记录end
    		set_time_limit(0);
    		$query = array("server_channelid"=>$server_channelid,"order_id"=>$orderid,"formalCode"=>$formalCode,"uuid"=>$uuid);
    		$query = http_build_query($query);
    		$path = "/default/api/change-no?".$query;
    		$ch = curl_init();
    		curl_setopt($ch,CURLOPT_URL,$_SERVER["HTTP_HOST"].$path);
    		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    		curl_setopt($ch,CURLOPT_TIMEOUT,1);
    		$result = curl_exec($ch);
    		curl_close($ch);
    		Ec::showError("**************start*************\r\n"
    				.$result."\r\n"
    				. microtime_float()."\r\n"
    				. $query."\r\n"
    				. "**************end*************\r\n",
    				'YunExpress_API/Create_async_info'.date("Ymd"));
    	}catch (Exception $e) {
    		$return['ack'] = 0;
    		$return['message'] = $e->getMessage();
    	}
    	return $return;
    }
    
}