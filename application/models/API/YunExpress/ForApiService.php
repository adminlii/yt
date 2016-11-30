<?php

class API_YunExpress_ForApiService extends Common_APIChannelDataSet
{
	// token信息
	protected $_user = "";
	protected $_orderOnline = "";
	//接口
	//protected $postOrdertotmsApi="http://112.126.68.251:8088/v5/api/Order/PacketOrder?type=json";
	protected $postOrdertotmsApi="https://202.104.134.94/chinapost/api/Order/PacketOrder?type=json";
	//protected $notifyTms = "http://112.126.68.251:8088/v5/api/LabelPrintService/PrintTomsLabel?type=json";
	protected $notifyTms = "https://202.104.134.94/chinapost/api/LabelPrintService/PrintTomsLabel?type=json";
    public function __construct()
    {
    	// 创建日志目录
    	if (! is_dir ( APPLICATION_PATH . '/../data/log/YunExpress_API' )) {
    		mkdir(APPLICATION_PATH . '/../data/log/YunExpress_API', 0777);
    		chmod(APPLICATION_PATH . '/../data/log/YunExpress_API', 0777);
    	}
    }

    public function setParam($serviceCode = '', $orderCode = '', $channelId = '', $serverProductCode = '',$order_config, $init = true)
    {
        parent::__construct($serviceCode, $orderCode, $channelId, $serverProductCode,$order_config, $init);
        
        $this->_user = isset ( $this->accountData ["as_user"] ) ? $this->accountData ["as_user"] : '';
        $this->_orderOnline = isset ( $this->accountData ["as_address"] ) ? $this->accountData ["as_address"] : '';
    }

    public function getData()
    {
        return $this->_paramsSet();
    }
    
    /**
     * 创建并预报中邮物流订单
     * @param unknown_type $orderCode 订单号
     */
    public function createAndPreAlertOrderServiceByCode(){
    	//所有创建订单业务方法统一返回格式：调用状态、跟踪号、EC订单号、错误信息、错误代码
    	//errorCode 001 系统异常、内部消化 ,002 业务异常，需要操作员处理
    	$callResult = array("ack"=>0,"orderCode"=>$this->orderCode,"trackingNumber"=>"","error"=>"","errorCode"=>"");
    	$callResult["orderCode"] = $this->orderCode;
    	
    	try {
    		/*
    		 * 1、验证订单是否可用
    		*/
    		//调用服务接口，同步审核订单
    		$result = $this->excuteOrderTo();
    		if(!$result["ack"]){
    			/*$callResult["error"] = $result["message"]."";
    			$callResult["errorCode"] = "001";*/
				$callResult["error"] = $result["data"]['ResultCode'];
				$callResult["errorCode"] = $result["data"]['ResultDesc'];
    			return $callResult;
    		}
    			
    		if($result["ack"]){
    			$result_temp = $result["data"]['Item'][0];
    
    			if(strtoupper(trim($result_temp["Result"])) == "1001"){
    				$callResult["ack"] = 1;
    			}else {
    				
    				$errorMessage = "";
    				foreach ($result_temp["ErrorPacketOrderList"] as $eKey=>$eVal){
    					$errorMessage .= $eVal["Message"];
    				}
    					
    				$callResult["error"] = $errorMessage;
    			}
    		}
    			
    	} catch (Exception $e) {
    		$callResult["error"] = "同步未知异常，订单号：".$this->orderCode."异常信息：".$e->getMessage();
    	}
    
    	return $callResult;
    }
    
    //调用服务接口
    public function excuteOrderTo(){
    	if($this->serverProductCode!='TNT')
    		$params = $this->bindNoticeData('normal');
    	else
    		$params = $this->bindNoticeData('tnt');
        $sysResult = $this->createAndPreAlertOrderService($params);
  
    	Ec::showError("**************start*************\r\n"
    			. print_r($params, true)
    			. "\r\n" . print_r($sysResult, true)
    			. "**************end*************\r\n",
    			'YunExpress_API/Create_response_info'.date("Ymd"));
    	return $sysResult;
    }
    
    /**
     * 创建并预报订单
     * @param unknown_type $data
     * @return multitype:number string NULL array
     */
    public function createAndPreAlertOrderService($data = array()) {
    	//$url = $this->_orderOnline . "/api/Order/PacketOrder";
		//$url = "http://test.hwcservice.com/ChinaPost/Api/Order/PacketOrder";
		//$url = "http://112.126.68.251:8088/v4/api/Order/PacketOrder";
		$url = $this->postOrdertotmsApi;
		$result = $this->excuteService($url, json_encode($data), "POST");
		header("Content-type: text/html; charset=utf-8");
    
    	return $result;
    }
    
    /**
     * 预报订单
     * @param unknown_type $data
     * @return multitype:number string NULL array
     */
    public function PreAlertOrderService($sendParams = array()) {
    	//$url = "http://test.hwcservice.com/ChinaPost/Api/LabelPrintService/PrintTomsLabel?type=json";
    	//$url = "http://112.126.68.251:8088/v4/api/LabelPrintService/PrintTomsLabel?type=json";
    	$url = $this->notifyTms;
    	$sendParams = json_encode($sendParams);
    	$header =array("Content-Type:application/json; charset=utf-8");
    	//$result = $this->curl_send($url,$sendParams,$header,"post","tmsuser:1234567890");
    	$result = $this->curl_send($url,$sendParams,$header,"post","tmsuser:123456");
    	return $result;
    }
    
    /**
     * 获取跟踪号
     * @param unknown_type $data
     * @return multitype:number string NULL array
     * $data = array(
     * 			'0 => array('CustomerCode' => '1002',
     * 						'ForecastNumber' => 'YT1530910000005200001'
     * 						)
     * 		   )
     */
    public function getTrackingNumber($data = "") {
    	$url = $this->_orderOnline . "/api/Order/GetTrackingNumber";
    	 
    	$result = $this->excuteService($url, json_encode($data), "POST");
    
    	return $result;
    }
    
    /**
     * 获取标签
     * @param unknown_type $data
     * @return multitype:number string NULL array
     * $data = array(
     * 			'0 => array('CustomerCode' => '1002',
     * 						'ForecastNumber' => 'YT1530910000005200001'
     * 						)
     * 		   )
     */
    public function getLabel($data = "") {
    	$url = $this->_orderOnline . "/api/Order/GetLabelsByForecastNumber";
    	 
    	$result = $this->excuteService($url, json_encode($data), "POST");
    
    	return $result;
    }
    
    /**
     * 强制放行
     * @param unknown_type $data
     * @return multitype:number string NULL array
     * $data = array(
     * 			'0 => array('CustomerCode' => '1002',
     * 						'ForecastNumber' => 'YT1530910000005200001'
     * 						)
     * 		   )
     */
    public function americaLinePassAddress($codeArr = array()) {
    	$url = $this->_orderOnline . "/api/Special/AmericaLinePassAddress";
    	
    	$data = array();
    	foreach ($codeArr as $k => $val) {
    		$data[] = array('CustomerCode' => $this->_user, 'ForecastNumber' => $val);
    	}
    	
    	$result = $this->excuteService($url, json_encode($data), "POST");
    
    	return $result;
    }
    
    /**
     * 删除预报
     * @param unknown_type $data
     * @return multitype:number string NULL array
     * $data = array(
     * 			'0 => array('CustomerCode' => '1002',
     * 						'ForecastNumber' => 'YT1530910000005200001'
     * 						)
     * 		   )
     */
    public function deleteForecast() {
    	$url = $this->_orderOnline . "/api/Order/DeleteForecast";
    	 
    	$data = array('CustomerCode' => $this->_user, 'ForecastNumber' => $this->orderCode);
    	return $this->excuteService($url, json_encode($data), "POST");
    }
    
    /**
     * 获取轨迹信息
     * @param unknown_type $data
     * @return multitype:number string NULL array
     */
    public function cargoTrackingService($data = array()){
    	$return = array("ack"=>0,"message"=>"","data"=>"");
    	/*
    	 * 请求服务
    	*/
    	$params = array(
    			'arg0' => $this->_token,
    			'arg1' => $data
    	);
    	$result = $this->excuteService($this->_orderOnlineTool,$params,"cargoTrackingService");
    	if($result["ack"] == 1){
    		//调用成功
    		if(strtoupper(trim($result["data"]["return"]["ack"])) == "SUCCESS"){
    			$return["ack"] = 1;
    			$return["data"] = $result["data"]["return"];
    		}else{
    			$return["message"] = $result["data"]["return"]["errors"]["cnMessage"];
    		}
    	}else{
    		$return["message"] = $result["message"];
    	}
    
    	/*
    	 * 处理请求结果
    	*/
    	return $return;
    }
    
    public function excuteService($url,$params,$method){
    	$result = array("ack"=>0,"message"=>"","data"=>"");
    	try {
    		$tuCurl = curl_init();
			curl_setopt($tuCurl, CURLOPT_URL, $url);
			curl_setopt($tuCurl ,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($tuCurl ,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($tuCurl,CURLOPT_TIMEOUT,10);
			if($method == 'POST') {
				curl_setopt($tuCurl, CURLOPT_POST, 1);
				curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $params);
			}
			
			curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($params)));

			// print_r($tuCurl);die;
			$data = curl_exec($tuCurl);
			
			if(curl_errno($tuCurl) != 0){
				$error = '发送CURL时发生错误:'.curl_error($tuCurl).'(code:'.curl_errno($curl).')'.PHP_EOL;
				//throw new Exception ('通知标签服务器失败！！');
				throw new Exception ($error);
				curl_close($tuCurl);
				
			}else{
	    		$data = Common_Common::objectToArray(json_decode($data));
	    		$result["ack"] = 1;
	    		$result["data"] = $data;
			}
    	} catch (Exception  $e) {
    		$result["message"] = $e->getMessage();
    	}
    	
    	Ec::showError("**************start*************\r\n"
    			. print_r($params, true)
    			. "\r\n"
    			. print_r($data, true)
    			. "**************end*************\r\n",
    			'YunExpress_API/Create_response_info'.date("Ymd"));
    	 
    	return $result;
    }
    
    /**
     * 保存标签
     */
    public function saveLabel($order_code = "", $urlArr = array()) {
    	
    	$result = array("state"=>0,"message"=>"");
    	if(empty($urlArr)) {
    		$result['message'] = "标签URL为空"; 
    		return $result;
    	}
    	
    	try {
    		
    		$csd_order = Service_CsdOrder::getByField($order_code, 'shipper_hawbcode');
    		
    		$ori_url = "";
    		$ol_file_type = "png";
    		$host = "http://" . $_SERVER['HTTP_HOST'];
//     			Ec::showError(print_r($urlArr, true), "--test--");
    		
    		// 标签个数计数
    		$mainLabelCount = $subLabelCount = $invoiceCount = 0;
    		
    		// 转成图片保存
    		foreach ($urlArr as $k => $row) {
    			$row = Common_Common::objectToArray($row);
    			$url = $row['Url'];
    			$type = $row['LabelType'];
    			
    			// 原始URL
    			$ori_url .= $url . ";";
    			
    			$return = $this->excuteGetService($url);
    			if($return['ack'] == 0) {
    				Common_Common::myEcho($order_code . " 标签下载失败...");
    				continue;
    			}
	    		
	    		// 增加标签计数
	    		switch($type) {
	    			case '1002':
	    				$ol_file_type = $this->saveLabelFile($order_code, $return['data'], $url, 'sub', $subLabelCount);
	    				$subLabelCount++;
	    				break;
	    			case '1003':
	    				$ol_file_type = $this->saveLabelFile($order_code, $return['data'], $url, 'invoice', $invoiceCount);
	    				$invoiceCount++;
	    				break;
	    			default:
	    				$ol_file_type = $this->saveLabelFile($order_code, $return['data'], $url, '', $mainLabelCount);
	    				$mainLabelCount++;
	    				break;
	    		}
    		}
    		
    		//删除原标签
    		Service_OrderLabel::delete($csd_order['shipper_hawbcode'],'order_code');
    		$path = APPLICATION_PATH . "/../data/" . $ol_file_type . "/" . $order_code;
    		//加入到标签里面
    		$labelRow = array(
    				"order_code" => $csd_order['shipper_hawbcode'],
    				"path" => $path,
    				"ol_label_url" => $host,
    				"sm_code" => '',
    				"ol_file_type" => $ol_file_type,
    				"ol_label_url_ori" => $ori_url,
    				"ol_main_label_count" => $mainLabelCount,
    				"ol_sub_label_count" => $subLabelCount,
    				"ol_invoice_label_count" => $invoiceCount,
    		);
    		Service_OrderLabel::add($labelRow);
    		 
    		Common_Common::myEcho($order_code . " 标签保存成功...");
    		$result["state"] = 1;
    	} catch(Exception $e) {
    		$result["state"] = 0;
    		$result["message"] = "保存标签失败." . $e->getMessage();
    	}
    	
    	Ec::showError("**************start*************\r\n"
    			. print_r($order_code, true)
    			. "\r\n"
    			. print_r($result, true)
    			. "**************end*************\r\n",
    			'YunExpress_API/SaveLabel_info'.date("Ymd"));
    	
    	return $result;
    }
    
    /**
     * 保存标签文件
     * @param unknown_type $code
     * @param unknown_type $url
     * @param unknown_type $type
     * @param unknown_type $index
     */
    private function saveLabelFile($code, $content, $url, $type, $index) {
    	
    	$ol_file_type = "";
    	
    	// 文件
    	if(eregi("pdf$", $url)) {
    		$ol_file_type = "pdf";
    	} else if(eregi("jpg$", $url) || eregi("png$", $url) || eregi("jpeg$", $url)) {
    		$ol_file_type = "png";
    	} else {
    		$ol_file_type = "html";
    		$content = Process_LabelImages::imagesUrlToBase64($content, "");
    	}
    	
    	// 路径
    	$path = APPLICATION_PATH . "/../data/" . $ol_file_type . "/" . $code;
    	if(!empty($type)) {
    		$path = $path . "/" . $type;
    	}
    	// 文件名
    	$filename = $path . "/" . $index . "." . $ol_file_type;
    	
    	EC::showError(print_r($filename,true), "--test---");
    	//创建文件夹
    	Common_Common::mkdirs($path);
    	file_put_contents($filename,  $content);
    	
    	return $ol_file_type;
    }
    
    /**
     * 执行GET方法
     * @param unknown_type $url
     * @return multitype:number string mixed NULL
     */
    private function excuteGetService($url) {
    	 
    	$result = array("ack"=>0,"message"=>"","data"=>"");
    	 
    	try {
    
    		$tuCurl = curl_init();
    		curl_setopt($tuCurl, CURLOPT_URL, $url);
    		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
    		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, 'GET');
    
    		// print_r($tuCurl);die;
    		$data = curl_exec($tuCurl);
    		$result["ack"] = 1;
    		$result["data"] = $data;
    	} catch (Exception  $e) {
    		$result["message"] = $e->getMessage();
    	}
    
    	return $result;
    }
    
    /**
     * 接收通知信息
     * //     {
//     	"CustomerCode": "1001",
//     	"ForecastNumber": "YT2015056050",
//     	"ChannelCode": "1001",
//     	"CoNumber": "Co2015100800010000225",
//     	"NotifyType": 1002,
//     	"PushStatus": 1010,  1010 成功 1011 失败
//     	"notifyResult": {
//     		"LabelUrl": null,
//     		"TrackNumber": "154561231465435",
//     		"Result": null
//     },
//     "Message": "预报成功!"
//     }
 * 
 * 1001 预报结果通知
1002 跟踪号推送通知
1003 验证结果
1004 服务商标签通知
1005 轨迹通知
     */
    public function receiveNotice($notice = array()) {
    	
    	$result = array("ack"=>0,"message"=>"");
    	
    	if(empty($notice)) {
    		return $result;
    	}
    	
    	// 预报单号
    	$shipper_hawbcode = $notice['ForeCastNumber'];
    	
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	
    	
    	try {
    		
    		
	    	// 获取预报数据
	    	$order_process = Service_OrderProcessing::getByField($shipper_hawbcode,"shipper_hawbcode");

	    	if(!empty($order_process)) {
		    	// 通知类型
		    	$notifyType = $notice['NotifyType'];
		    	// 处理状态
		    	$pushStatus = $notice['PushStatus'];
		    	 
		    	// 存在跟踪号， 标签标志
		    	$trackingnumber_status = $order_process['trackingnumber_status'];
		    	$label_status = $order_process['label_status'];
		    	
		    	// 删除标签
		    	$delete_sign = "0";
		    	
		    	switch ($notifyType) {
		    		case "1001": // 预报结果通知
		    			
		    			// 更新预报数据状态--预报异常
		    			$update_row = array('ops_status' => 20, 'ops_note' => $notice['Message']);
		    			
		    			$result['ack'] = "1";
		    			$result['message'] = "预报结果处理完成";
		    			break;
		    		case "1002": // 跟踪号结果通知 如果订单
		    			
		    			// 处理状态
		    			if($pushStatus == '1011') {

							// 异常更新
							$csd_order = Service_CsdOrder::getByField($shipper_hawbcode,"shipper_hawbcode");
							if(empty($csd_order)) {
								$result['message'] = "预报订单数据不存在";
								break;
							}

							// 更新单号，订单状态改为"D"草稿
							$update_order = array("order_status" => "D");
							Service_CsdOrder::update($update_order, $order_process['order_id']);

		    				// 更新预报数据状态--预报异常
		    				$update_row = array('ops_status' => 20, 'ops_note' => $notice['Message']);
							$result['ack'] = "1";
							$result['message'] = "跟踪号结果异常";
		    			} else {
		    				if(!empty($notice['notifyResult']['TrackNumber'])) {
		    					
		    					// 更新服务商单号
		    					$csd_order = Service_CsdOrder::getByField($shipper_hawbcode,"shipper_hawbcode");
		    					if(empty($csd_order)) {
		    						$result['message'] = "预报订单数据不存在";
		    						break;
		    					}
		    					
		    					// 更新单号，订单状态改为"P"已预报
		    					$update_order = array('server_hawbcode' => $notice['notifyResult']['TrackNumber'],"order_status" => "P");
		    					if(!empty($notice['notifyResult']['SmallLabelNumber'])){
		    						$update_order['small_hawbcode'] = $notice['notifyResult']['TrackNumber'];
		    						$update_order['server_hawbcode'] = $notice['notifyResult']['SmallLabelNumber'];
		    					}
		    					Service_CsdOrder::update($update_order, $order_process['order_id']);
		    					//更新物流主干
		    					$update_TakTrackingbusiness = array('server_hawbcode' => $notice['notifyResult']['TrackNumber']);
		    					Service_TakTrackingbusiness::update($update_TakTrackingbusiness,$shipper_hawbcode,"shipper_hawbcode");
		    					// 查询订单
		    					$log_content[] = Ec::Lang('服务商换号') . ',' . Ec::Lang('原服务商单号') . ' ' . ($csd_order['server_hawbcode']?$csd_order['server_hawbcode']:Ec::Lang('为空')) . ' ' . Ec::Lang('更改为') . ' ' . $update_order['server_hawbcode'];
		    					
		    					// 日志
		    					$logRow = array(
		    							'ref_id' => $order_process['order_id'],
		    							'log_content' => implode(";\n", $log_content)
		    					);
		    					
		    					$trackingnumber_status = "1";
		    					
		    					// 获取到跟踪号，当只有跟踪号的情况，可以删除记录
		    					$delete_sign = 1;
								$result['ack'] = "1";
								$result['message'] = "跟踪号结果处理完成";
		    				}else{
								$result['ack'] = "0";
								$result['message'] = "跟踪号不能为空";
							}
		    			}
		    			

		    			break;
		    		case "1003": // 验证结果通知
		    			
		    			// 处理状态
		    			if($pushStatus == '1011') {
		    				// 更新预报数据状态--预报异常
		    				$update_row = array('ops_status' => 22, 'ops_note' => $notice['Message']);
		    			}
		    			
		    			$result['ack'] = "1";
		    			$result['message'] = "验证结果处理完成";
		    			break;
		    		case "1004": // 服务商标签通知
		    			
		    			// 处理状态
		    			if($pushStatus == '1010') {
		    				
		    				if(!empty($notice['notifyResult']['TrackNumber'])) {
		    				
		    					// 更新服务商单号
		    					$csd_order = Service_CsdOrder::getByField($shipper_hawbcode,"shipper_hawbcode");
		    					if(empty($csd_order)) {
		    						$result['message'] = "预报订单数据不存在";
		    						break;
		    					}
		    				
		    					// 更新单号
		    					$update_order = array('server_hawbcode' => $notice['notifyResult']['TrackNumber']);
		    					Service_CsdOrder::update($update_order, $order_process['order_id']);
		    				
		    					// 查询订单
		    					$log_content[] = Ec::Lang('服务商换号') . ',' . Ec::Lang('原服务商单号') . ' ' . ($csd_order['server_hawbcode']?$csd_order['server_hawbcode']:Ec::Lang('为空')) . ' ' . Ec::Lang('更改为') . ' ' . $update_order['server_hawbcode'];
		    				
		    					// 日志
		    					$logRow = array(
		    							'ref_id' => $order_process['order_id'],
		    							'log_content' => implode(";\n", $log_content)
		    					);
		    				
		    					$trackingnumber_status = "1";
		    					
		    					// 获取到跟踪号，当只有跟踪号的情况，可以删除记录
		    					$delete_sign = 1;
		    				}
		    				
		    				// 保存标签
		    				if(!empty($notice['notifyResult']['LabelUrl'])) {
			    				// 保存标签
			    				$label_result = $this->saveLabel($shipper_hawbcode, json_decode($notice['notifyResult']['LabelUrl']));
			    				if($label_result['state']) {
			    					$label_status = 1;
			    					// 获取到标签可以删除记录
			    					$delete_sign = 1;
			    				} 
		    				}
		    				
		    			} else {
		    				// 更新预报数据状态--预报异常
		    				$update_row = array('ops_status' => 21, 'ops_note' => $notice['Message']);
		    			}
		    			
		    			$result['ack'] = "1";
		    			$result['message'] = "服务商标签处理完成";
		    			
		    			break;
		    		case "1005": // 轨迹通知
		    			break;
		    		case "1006": // 服务商跟踪号标签通知
		    			
		    			// 处理状态
		    			if($pushStatus == '1010') {
		    				
		    				if(!empty($notice['notifyResult']['TrackNumber'])) {
		    				
		    					// 更新服务商单号
		    					$csd_order = Service_CsdOrder::getByField($shipper_hawbcode,"shipper_hawbcode");
		    					if(empty($csd_order)) {
		    						$result['message'] = "预报订单数据不存在";
		    						break;
		    					}
		    				
		    					// 更新单号
		    					$update_order = array('server_hawbcode' => $notice['notifyResult']['TrackNumber']);
 		    					Service_CsdOrder::update($update_order, $order_process['order_id']);
		    				
		    					// 查询订单
		    					$log_content[] = Ec::Lang('服务商换号') . ',' . Ec::Lang('原服务商单号') . ' ' . ($csd_order['server_hawbcode']?$csd_order['server_hawbcode']:Ec::Lang('为空')) . ' ' . Ec::Lang('更改为') . ' ' . $update_order['server_hawbcode'];
		    				
		    					// 日志
		    					$logRow = array(
		    							'ref_id' => $order_process['order_id'],
		    							'log_content' => implode(";\n", $log_content)
		    					);
		    				
		    					$trackingnumber_status = "1";
		    					// 获取到跟踪号可以删除记录
		    					$delete_sign = 1;
		    				}
		    				
		    				if(!empty($notice['notifyResult']['LabelUrl'])) {
			    				// 保存标签
			    				$label_result = $this->saveLabel($shipper_hawbcode, json_decode($notice['notifyResult']['LabelUrl']));
			    				if($label_result['state']) {
			    					$label_status = 1;
			    					// 获取到标签可以删除记录
			    					$delete_sign = 1;
			    				} 
		    				}
		    				
		    			} else {
		    				// 更新预报数据状态--预报异常
		    				$update_row = array('ops_status' => 21, 'ops_note' => $notice['Message']);
		    			}
		    			
		    			$result['ack'] = "1";
		    			$result['message'] = "服务商标签处理完成";
		    			
		    			break;
		    		default:
		    			break;	
		    	}
		    	
		    	// 标签状态
		    	$update_row['trackingnumber_status'] = $trackingnumber_status;
		    	$update_row['label_status'] = $label_status;
		    	Service_OrderProcessing::update($update_row, $order_process['ops_id']);
		    	
		    	// 判断是否删除记录
		    	if($delete_sign) {
		    		// 获取渠道数据
		    		$sql = "select sc.is_api_return_label, sc.is_api_return_trackNum from csi_servechannel sc where sc.server_channelid = {$order_process['server_channelid']}";
		    		$db2 = Common_Common::getAdapterForDb2();
		    		$channel_row = $db2->fetchOne($sql);
		    		if($channel_row 
		    				&& $channel_row['is_api_return_label'] == $label_status 
		    				&& $channel_row['is_api_return_trackNum'] == $trackingnumber_status) {
		    			Service_OrderProcessing::delete($order_process['ops_id']);
		    		}
		    	}
			    	
		    	
	    	} else {
	    		$result['message'] = "预报数据不存在";
	    	}
	    	
	    	$db->commit();
    	} catch(Exception $e) {
    		$db->rollback();
    		$result['message'] = $e->getMessage();
    	}
    	
    	// 记录日志
    	Ec::showError("**************start*************\r\n"
    			. print_r($notice, true)
    			. "\r\n"
    			. print_r($result, true)
    			. "**************end*************\r\n",
    			'YunExpress_API/Receive_info'.date("Ymd"));

		return $result;
    }
    
    
    //通知中邮接口
    public function  notifyOrderToService(){
      $callResult = array("ack"=>0,"orderCode"=>$this->orderCode,"error"=>"","errorCode"=>"");
 	  try {
 	 	$res = $this->excuteOrderToNotify();
 	 	$res = Common_Common::xml_to_array($res);
 	 	if($res['status']=='success'){
 	 		$callResult["ack"] = 1;
 	 	}else{
 	 		$callResult["ack"] = -1;
 	 		$callResult["errorCode"] = $res['code'];
 	 		$callResult["error"] = $res['description'];
 	 	}
 	} catch (Exception $e) {
 	    $callResult["error"] = "同步未知异常，订单号：".$this->orderCode."异常信息：".$e->getMessage();
 	}
 		return $callResult;
    }
    public function excuteOrderToNotify(){
    
        /*
         * 构造订单信息xml
       	 */
      	$xmlarray= array();
      	//寄件人信息
      	$sender = array();
      	$sender['name'] = $this->shipperKey["shipperName"];
      	$sender['postcode'] = $this->shipperKey["shipperPostCode"];
      	$sender['phone'] = $this->shipperKey["shipperPhone"];
      	$sender['mobile'] = $this->shipperKey["shipperPhone"];
      	$sender['country'] = $this->shipperKey["shipperCountryCode"];
      	$sender['provcode'] = "";
      	$sender['citycode'] = $this->shipperKey["shipperCity"];
      	//获取区域代码
      	$codeArr = $this->getQycode($sender['citycode']);
      	if(is_array($codeArr)&&count($codeArr)==2){
      		$sender['provcode'] = $codeArr['provincecode'];
      		$sender['citycode'] = $codeArr['citycode'];
      	}
      	$sender['countycode'] = '';
      	$sender['company'] = $this->shipperKey["shipperCompanyName"];
      	$sender['street'] = $this->shipperKey["shipperStreet"];
      	//收件人信息
      	$receiver = array();
      	$receiver['name'] =  $this->orderKey['consigneeName'];
      	$receiver['postcode'] = $this->orderKey['consigneePostalCode'];
      	$receiver['phone'] = empty($this->orderKey['consignee_telephone']) ? $this->orderKey['consignee_mobile'] : $this->orderKey['consignee_telephone'];
      	$receiver['mobile'] = empty($this->orderKey['consignee_mobile']) ? $this->orderKey['consignee_telephone'] : $this->orderKey['consignee_mobile'];
      	$receiver['country'] =  $this->orderKey['consigneeCountryCode'];
      	$receiver['prov'] = $this->orderKey["consigneeStateOrProvince"];
      	$receiver['city'] = $this->orderKey['consigneeCity'];
      	$receiver['county'] = $this->orderKey['consigneeCountryCode'];
      	$receiver['company'] = $this->orderKey["consigneeCompanyName"];
      	$receiver['street'] =  $this->orderKey["consigneeStreet"];
      	if(!empty($this->orderKey["consigneeStreet1"]))
      		$receiver['street_extra_1'] = $this->orderKey["consigneeStreet1"];
      	if(!empty($this->orderKey["consigneeStreet2"]))
      	$receiver['street_extra_2'] = $this->orderKey["consigneeStreet2"];
      	//商品信息
      	$items = array();
      	//内件品名
      	$itemnames = '';
      	$_ordernum = 0;
      	foreach($this->orderInvoiceItemKey as $oKey=>$row){
      		$item = array();
      		$cnname = (!empty($row['titleCn']) ? $row['titleCn'] : $row['titleEn']);
      		$enname = (!empty($row['titleEn']) ? $row['titleEn'] : $row['titleCn']);
      		if($itemnames&&$enname!=$itemnames){
      			$itemnames.=','.$enname;
      		}else if(!$itemnames){
      			$itemnames.=$enname;
      		}
      		$item['cnname'] = $cnname;
      		$item['count'] = $row["quantity"];
      		$_ordernum+=$item['count'];
      		$item['weight'] = intval($row["weight"]*$row["quantity"]*1000)/1000;
      		$item['currency'] = $row["currencyCode"];
      		$item['cost'] = intval($row['value']*$row["quantity"]*100)/100;
      		$item['intemcom'] = 'Harmless ingredients';
      		$item['origin'] = "CN";
      		$item['trade'] = $this->orderKey['type']==3?1:3;
      		$item['enname'] = $enname;
      		$item['HS'] = $row["hsCode"];
      		$item['intemsize'] = '';
      		$item['sellurl'] = '';
      		$items["item_".$oKey]=$item;
      	}
      	//第3级
      	$array_three=array();
      	$array_three['pretype'] = 60;
      	$array_three['mailnum'] = $this->orderData['server_hawbcode'];
      	$array_three['rcvarea'] = 5;
      	$array_three['prptycode'] = $this->orderKey['type']==3?1:3;
      	//$array_three['prodcode'] = '';
      	switch ($this->serverProductCode){
      		case "TNT":$array_three['prodcode'] = $this->orderKey['type']==3?5120101991:5320101991 ;break;
      		case "G_DHL":$array_three['prodcode'] = $this->orderKey['type']==3?5120501991:5320501991 ;break;
      		case "ESB":$array_three['prodcode'] =5320402991 ;break;
      		default:$array_three['prodcode'] =5320401991 ;break;
      	}
      	
      	$array_three['clctname'] = '';
      	$array_three['clctcode'] = '';
      	$array_three['actualweight'] = $this->orderKey["weight"]*1000;
      	$array_three['length'] = intval($this->orderKey["length"]*10)/10;
      	$array_three['width'] = intval($this->orderKey["width"]*10)/10;
      	$array_three['height'] = intval($this->orderKey["height"]*10)/10;
      	$array_three['volweight'] =  $array_three['actualweight'];
      	$array_three['billingweight'] = $array_three['actualweight'];
      	$array_three['bjmoney'] = 0;
      	
      	//$array_three['bxmoney'] = intval($this->orderKey["insurance_value_gj"]*100)/100;
      	$array_three['bxmoney'] = intval($this->orderKey["insurance_value_gj"]);
      	$array_three['loanmoney'] = 0;
      	$array_three['minordernum'] = 0;
      	if($this->serverProductCode=="G_DHL"){
      		$array_three['minordernum']=$_ordernum;
      	}
      	$array_three['mpostalnum'] = $this->orderData['server_hawbcode'];
      	$array_three['ordernum'] = empty($this->orderData['small_hawbcode'])?$this->orderCode:$this->orderData['small_hawbcode'];
      	if(false!==strpos($array_three['ordernum'],"019931265099999891")){
      		$array_three['ordernum'] = str_replace("019931265099999891", "", $array_three['ordernum']);
      	}
      	$array_three['forecastshut'] = 0;
      	$array_three['internals'] = 1;
      	$array_three['portoffice'] = '';
      	$array_three['sendcountry'] = 'CN';
      	$array_three['mainminorder'] = 1;
      	//$array_three['mainbilling'] = 1;
      	$array_three['transport'] = 2;
      	$array_three['ordersources'] = '';
      	$array_three['identityplate'] = '';
      	$array_three['itemnames'] = $itemnames;      	
      	$array_three['sender'] = $sender;
      	$array_three['receiver'] = $receiver;
      	$array_three['items'] = $items;
      	//第2级
      	$array_two = array();
      	$array_two['order'] = $array_three;
      	//第1级
      	$array_one = array();
      	$array_one['pretype'] = 60;
      	$array_one['orgcode'] = ''; 
      	$array_one['custcode'] = ''; //大客户id
      	$array_one['postInfos'] = $array_two;
      	//根root
      	$xmlarray['orders'] = $array_one;
      	$xmlarray = xml_filterInArr($xmlarray);
      	$xml = xml_encode($xmlarray['orders'],'orders','item');
      	$xml=preg_replace('/item_(\d)+/i','item', $xml);  
        $url="http://shipping.ems.com.cn/partner/api/public/p/orderSpecial";
        $data = $xml;
        $header = array("authenticate:pdfTest_dhfjh98983948jdf78475fj65375fjdhfj","version:international_eub_us_1.1");
        $result = $this->curl_send($url,$data,$header,"post");
        Ec::showError("**************start*************\r\n"
            . print_r($data, true)
            . "\r\n" . print_r($result, true)
            . "**************end*************\r\n",
            'YunExpress_API/Notify_ems_response_info'.date("Ymd"));
        return $result;
    }
    
    //中邮收寄 获取省和市是区域代码
    public function getQycode($positionename){
    	$return_arr = array();
    	do{
    		try{
    			//如果出现xxx,xxx的取第一个
    			$positionename = str_replace('-', ',', $positionename);
    			$_positionename = strpos($positionename,",");
    			if($_positionename!==false){
    				$positionename=substr($positionename,0,$_positionename);
    			}
    			$positionename = preg_replace('/\s/','',$positionename);
    			//在本地的对照库中找到地址，然后取出市 和 省
    			$condition['positionpname'] = strtoupper($positionename);
    			$res = Service_CsiGeographical::getByCondition($condition);
    			if($res[0]&&is_array($res[0])){
    				$citycname =  $res[0]['citycname'];
    				$provincecname = $res[0]['provincecname'];
    				//获取省区域代码
    				$url = 'http://shipping.ems.com.cn/partner/api/public/p/area/cn/province/list';
    				$header = array("authenticate:pdfTest_dhfjh98983948jdf78475fj65375fjdhfj","version:international_eub_us_1.1");
    				$res = $this->curl_send($url,'',$header);
    				if(is_array($res)){
    					break;
    				}
    				$proinceArr =$this->decode_xml_zy($res);
    				if(!$proinceArr[$provincecname]){
    					break;
    				}
    				$return_arr['provincecode'] = $proinceArr[$provincecname];
    				//然后再去调取该省下面的市
    				$url = 'http://shipping.ems.com.cn/partner/api/public/p/area/cn/city/list/'.$return_arr['provincecode'];
    				$res = $this->curl_send($url,'',$header);
    				if(is_array($res)){
    					break;
    				}
    				$proinceArr =$this->decode_xml_zy($res);
    				if(!$proinceArr[$citycname]){
    					break;
    				}
    				$return_arr['citycode'] = $proinceArr[$citycname];
    			}
    		}catch (Exception $e){
    		
    		}
    	}while (0);  
    	
    	return $return_arr;
    }
    
    //解析中邮区域代码xml
    private function decode_xml_zy($res){
    	$proinceArr = array();
    	$xml = new DOMDocument();
    	$xml->loadXML($res);
    	$areaDom = $xml->getElementsByTagName("area");
    	foreach($areaDom as $area){
    		$proinceArr[$area->nodeValue]=$area->attributes->item(0)->nodeValue;
    	}
    	return $proinceArr;
    }
    
    //跟踪物流
    public function gettrack_pross($param,$dataformat=false){
        $result = array("ack"=>0,"message"=>"","errorCode"=>"");
        try{
            //获取主干信息
            $takbussiness       = Service_TakTrackingbusiness::getByField($param["code"],$param["hawbcode"]);
            print_r($takbussiness);
            $serve_hawbcode     = $takbussiness["server_hawbcode"];
            $mailnum_hawbcode   = $takbussiness["track_server_code"];
            //关联所用的主键
            $tbs_id             = $takbussiness["tbs_id"];
            //获取详细信息
            $trackdetailserve   = $this->gettrackDetail(1,$serve_hawbcode);
            if($trackdetailserve['ack']!=1){
               $result= $trackdetailserve;
               $result['type'] = 1;
               break;
            }
            if($mailnum_hawbcode){
                $trackdetailmailnum = $this->gettrackDetail(2,$mailnum_hawbcode);
                if($trackdetailmailnum['ack']!=1){
                    $result= $trackdetailmailnum;
                    $result['type'] = 2;
                    break;
                }
            }
            //
            $msg = $trackdetailserve["data"].$trackdetailmailnum["data"];
            $result['data'] = $msg ; 
            $dataNow = date('Y-m-d H:i:s');
            $row=array(
                "tbs_id"=>$tbs_id,
                'track_code' => 'IR',
                'track_source' => "W",
                'track_occur_date' => $dataNow,
                'track_area_description' => '',
                'track_create_date' => $dataNow,
                'track_create_person' => "",
                'track_area_description' => $msg,
            );
            Service_TakTrackdetails::add($row);
           //要不要修改主干上的事件待定
            $udprow= array(
                "new_track_date" =>$dataNow,
                "new_track_comment"=>$msg
            );
            Service_TakTrackingbusiness::update($udprow, $tbs_id);
        } catch (Exception  $e) {
            $result["message"] = $e->getMessage();
        }
        // 记录日志
        Ec::showError("**************start*************\r\n"
        		. print_r($param, true)
        		. "\r\n"
        				. print_r($result, true)
        				. "**************end*************\r\n",
        				'YunExpress_API/gettrack_pross'.date("Ymd"));
        if($dataformat){
           switch ($dataformat){
               case "json":$result=json_encode($result);break;
               default:;break;
           } 
        }
        return $result;
    }
    public function gettrackDetail($server_codetype,$param){
        if($server_codetype==1){
            $return = $this->getTrack_serve_hawbcode($param);
        }else{
            $return =$this->getTrack_mailnum_hawbcode($param);
        }
        return $return ;
    }
    private function getTrack_serve_hawbcode($param){
        $return = array("ack"=>0,"message"=>"");
        do{
            if(empty($param)){
                $return['ack'] = -1;
                $return['message']="没有需求查询的服务号";
                break;
            }
            $result  = $this->sendData_track_serve($param);
            if($result['ret']!=1){
                $return = $result;
                break;
            }
        }while(0);
        return $return;
    }
    private function getTrack_mailnum_hawbcode($param){
    	$return = array("ack"=>0,"message"=>"");
    	do{
    		if(empty($param)){
    			$return['ack'] = -1;
    			$return['message']="没有需求查询的服务号";
    			break;
    		}
    		$result  = $this->sendData_track_mailnum($param);
    		if($result['ret']!=1){
    			$return = $result;
    			break;
    		}
    	}while(0);
    	return $return;
    }
    
    private function sendData_track_serve($param){
        $return = array("ack"=>0,"message"=>"");
        do{
            try {
                if(empty($param)){
                    $return['ack'] = -1;
                    $return['message']="没有需求查询的服务号";
                    break;
                }
                //$serve_code = $param['server_hawbcode'];
                $TrackingNumber = $param["server_code"];
                $ChannelName	= $param["channel"]; 	
                $url = "http://112.126.68.251:8088/v4/api/TrackingService/QueryTrackingStatus?type=json";
                $sendParams = array(
                	"data"=>array("TrackingNumber"=>$TrackingNumber,"ChannelName"=>$ChannelName),
           			"RequestId"=>null,
                	"RequestTime"=>date('Y-m-d H:i:s'),
                	"Version"=>"0.0.0.3"	
                );
                
                $sendParams = json_encode($sendParams);
                print_r($sendParams);
                $header =array("Content-Type:application/json; charset=utf-8");
                $result = $this->curl_send($url,$sendParams,$header,"post","tmsuser:1234567890");
                if(is_array($result)){
                    $return['ack'] = -2;
                    $return["message"]=$result['error'];
                    break;
                }else if(empty($result)){
                    $return['ack'] = -3;
                    $return["message"]="没有接受到任何信息";
                    break;
                }
                $return['ack'] = 1;
                $return['data'] = $result;
            } catch (Exception $e) {
                $return['ack'] = -13;
                $return['message']=$e->getMessage();
                break;
            }
        }while(0);
        return $return;
    }
    private function sendData_track_mailnum($param){
    	$return = array("ack"=>0,"message"=>"");
    	do{
    		try {
    			if(empty($param)){
    				$return['ack'] = -1;
    				$return['message']="没有需求查询的服务号";
    				break;
    			}
    			$TrackingNumber = $param["server_code"];
    			$url = "http://shipping.ems.com.cn/partner/api/public/p/track/query/cn/".$TrackingNumber;
    			$header =array("version: international_eub_us_1.1",
    						   "authenticate: CommercialServices_fd51e7677e62336a933088af2c9241b6"
    			);
    			
    			$result = $this->curl_send($url,'',$header);
    			if(is_array($result)){
    				$return['ack'] = -2;
    				$return["message"]=$result['error'];
    				break;
    			}else if(empty($result)||$result=='null'){
    				$return['ack'] = -3;
    				$return["message"]="没有接受到任何信息";
    				break;
    			}
    			$return['ack'] = 1;
    			$return['data'] = $result;
    		} catch (Exception $e) {
    			$return['ack'] = -13;
    			$return['message']=$e->getMessage();
    			break;
    		}
    	}while(0);
    	return $return;
    }
    public function  curl_send($url,$data='',$header=array(),$type='get',$authentication=false){
          $curl = curl_init();
          curl_setopt($curl,CURLOPT_URL,$url);
          curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
          curl_setopt($curl ,CURLOPT_SSL_VERIFYPEER,false);
          curl_setopt($curl ,CURLOPT_SSL_VERIFYHOST,FALSE);
          curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)');
          curl_setopt($curl,CURLOPT_FOLLOWLOCATION,1);
          curl_setopt($curl,CURLOPT_AUTOREFERER,1);
          if('post' === $type){
           curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
           curl_setopt($curl,CURLOPT_POST,1);
           curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
          }
          if(!empty($header)){
              curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
          }
          if($authentication){
          	curl_setopt($curl, CURLOPT_USERPWD, $authentication);
          }
          $result = curl_exec($curl);
          if(curl_errno($curl) != 0){
           $error = '发送CURL时发生错误:'.curl_error($curl).'(code:'.curl_errno($curl).')'.PHP_EOL;
           curl_close($curl);
           return array("error"=>$error);
          }
          curl_close($curl);
          return $result;
 	}
 	
 	//通知数据组装
 	private function bindNoticeData($type){
 		$class = 'bindNoticeData'.ucfirst($type);
 		return $this->$class();
 	}
 	
 	private function bindNoticeDataNormal(){
 		/*
 		 * 构造订单信息
 		*/
 		$data = array();
 			
 		//销售产品
 		$items = array();
 		$data["OrderID"] = $this->orderCode;
 			
 		//获取渠道运输方式
 		$ChannelCode = $this->serverProductCode;
 		$ParcelInformation["Weight"] = $this->orderKey["weight"]*1000;
 		$ParcelInformation["WeightUnit"] =3;
 		$ParcelInformation["Length"] = $this->orderKey["length"];
 		$ParcelInformation["Width"] = $this->orderKey["width"];
 		$ParcelInformation["Height"] = $this->orderKey["height"];
 		$ParcelInformation["SizeUnit"]   = 2;
 		$ParcelInformation["ExistDangerousGoods"] = $this->orderKey["dangerousgoods"]?true:false;
 		$ParcelInformation["ProductInformations"] = null;
 		
 		$declareInvoice = array();
 		//报关产品
 		foreach($this->orderInvoiceItemKey as $oKey=>$row){
 			$cnname = (!empty($row['titleCn']) ? $row['titleCn'] : $row['titleEn']);
 			$enname = (!empty($row['titleEn']) ? $row['titleEn'] : $row['titleCn']);
 		
 			$declareInvoice_temp = array();
 			$declareInvoice_temp["Description"] = $enname.'-'.$cnname;
 			$declareInvoice_temp["Value"] = $row['value'];
 			$declareInvoice_temp["Quantity"] = $row["quantity"];
 			$declareInvoice_temp["Weight"] = $row["weight"];
 			$declareInvoice_temp["WeightUnit"] = 4;
 			$declareInvoice_temp["HSCode"] = $row["hsCode"];
 			$declareInvoice_temp["Sku"] = $row["sku"];
 			$declareInvoice_temp["Remark"] = $row["description"];
 			$declareInvoice_temp["Currency"] = $row["currencyCode"];
 			$declareInvoice_temp["ProductUrl"] = $row["url"];
 			$declareInvoice[] = $declareInvoice_temp;
 		}
 		$ParcelInformation["ProductInformations"] = $declareInvoice;
 		$data["ParcelInformation"] = $ParcelInformation;
 		
 		// 收件人
 		//$data["ShippingCountryCode"] = $this->orderKey['consigneeCountryCode'];
 			
 		//国家
 		$ct=Service_IddCountry::getAll();
 		$country=array();
 		foreach ($ct as $ck=>$cv){
 			$country[$cv['country_code']]=$cv['country_enname'];
 		}
 		
 		$RecipientAddress = array();
 		// TODO
 		$RecipientAddress["Country"] = $this->orderKey['consigneeCountryCode'];
 		$RecipientAddress["FirstName"] = $this->orderKey['consigneeName'];
 		$RecipientAddress["LastName"] = "";
 		$RecipientAddress["Company"] = $this->orderKey["consigneeCompanyName"];
 		$RecipientAddress["StreetAddress"] = $this->orderKey["consigneeStreet"];
 		$RecipientAddress["StreetAddress2"] = $this->orderKey["consigneeStreet1"];
 		$RecipientAddress["StreetAddress3"] = $this->orderKey["consigneeStreet2"];
 		$RecipientAddress["City"] = $this->orderKey['consigneeCity'];
 		$RecipientAddress["State"] = $this->orderKey['consigneeStateOrProvince'];
 		$RecipientAddress["IsResidential"] = false;
 		$RecipientAddress["PhoneNumber"] = $this->orderKey['consigneePhone'];
 		$RecipientAddress["ZIPCode"] = $this->orderKey['consigneePostalCode'];
 		
 		$data['RecipientAddress'] = $RecipientAddress;
 		
 		/***发件人信息****/
 		$ShipperAddress = array();
 		$ShipperAddress["FirstName"] = $this->shipperKey["shipperName"];
 		$ShipperAddress["LastName"] = "";
 		$ShipperAddress["Company"] = $this->shipperKey["shipperCompanyName"];
 		if($this->shipperKey["shipperStreet"]){
 			$streeArr = explode("||", $this->shipperKey["shipperStreet"]);
 			$ShipperAddress["StreetAddress"] = $streeArr[0]?$streeArr[0]:'';
 			$ShipperAddress["StreetAddress2"] = $streeArr[1]?$streeArr[1]:'';
 			$ShipperAddress["StreetAddress3"] = $streeArr[2]?$streeArr[2]:'';
 		}
 		$ShipperAddress["State"] = $this->shipperKey["shipperStateOrProvince"];
 		$ShipperAddress["City"] = $this->shipperKey["shipperCity"];
 		$ShipperAddress["PhoneNumber"] = $this->shipperKey["shipperPhone"];
 		$ShipperAddress["ZIPCode"] = $this->shipperKey["shipperPostCode"];
 		$ShipperAddress["Country"] = $this->shipperKey["shipperCountryCode"];
 		$ShipperAddress["Email"] = null;
 		$ShipperAddress = print_r(json_encode($ShipperAddress),true);
 		//冗余字段
 		$RedundancyField = array();
 		$RedundancyField['Battery'] = $this->orderData['battery'];
 		$RedundancyField['ShipperAddress'] = $ShipperAddress;
 		$RedundancyField['ProductCode'] = $this->orderKey['type']==3?"D":"N";
 		
 		//保险额外服务
 		$extservice = $this->orderExtservice;
 		if($extservice[0]['servicevalue']){
 			$RedundancyField['InsuredFee']= $extservice[0]['servicevalue'];
 		}
 		$RedundancyField["ShipperEIN"]=$this->orderKey["invoice_shippertax"] ;
 		$RedundancyField["RecipientEIN"]=$this->orderKey["invoice_consigneetax"];
 		$RedundancyField["CommodityCode"] = $declareInvoice[0]['HSCode']?$declareInvoice[0]['HSCode']:'';
 		//总价值
 		$RedundancyField['DeclaredValue']    = empty($this->orderKey["declaredValue"])?"0.01":$this->orderKey["declaredValue"];
 		//保险价值
 		if(!empty($this->orderKey["insurance_value_gj"])){
 			$RedundancyField['InsuredAmount'] = $this->orderKey["insurance_value_gj"];
 		}
 		//注册编号
 		//$data["RegisterNumber"] = empty($this->customer_ext["registernumber"])?"":$this->customer_ext["registernumber"];
 		//$data["AgencyCode"]		= empty($this->customer_ext["agencycode"])?"":$this->customer_ext["agencycode"];
 		//citycode
 		if($ChannelCode=="G_DHL"||$ChannelCode=="TNT"){
 			//上传REF和绑定的账号
 			//$data["reference"] 		 = $this->orderKey["refer_hawbcode"];
 			$condtion_sp['citycode'] = $this->orderKey["refer_hawbcode"];
 			$condtion_sp['status']   =   1;
 			$condtion_sp['productcode'] =   $ChannelCode;
 			$server_csi_prs=new Service_CsiProductRuleShipper();
 			$rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
 			if($rs_cisprs[0]){
 				//设定上发件人账号
 				$RedundancyField['AccuntNum'] = $rs_cisprs[0]['countnum'];
 				$RedundancyField["Reference"] = $rs_cisprs[0]['citycode'];
 			}
 		}
 		if($ChannelCode=="TNT"){
 			//是否废弃包裹
 			$RedundancyField['Abandon'] = $this->orderKey["untread"];
 		}
 		//$params = array('CustomerCode'=> $this->_user, 'packageMessage' => array($data));
 		$params["Version"] = '0.0.0.3';
 		$params["RequestId"] = empty($uuid)?"":$uuid;
 		$data["Token"] = '99999999999999999999999999999999';
 		$product_set_rs=Common_Common::getProductAllByCode($ChannelCode);
 		$data["ChannelName"] =$product_set_rs['ccode'];
 		$data["ServiceTypeCode"] =$product_set_rs['name'];
 		$data["WarehouseCode"] =$product_set_rs['area'];
 		$data["LabelMarkText"] =null;
 		$data["RedundancyField"] =$RedundancyField;
 		$params["data"] = $data;
 		return $params;
 	}
 	private function bindNoticeDataTnt(){
 		/*
 		 * 构造订单信息
 		*/
 		$data = array();
 			
 		//销售产品
 		$items = array();
 		$data["OrderID"] = $this->orderCode;
 			
 		//获取渠道运输方式
 		$ChannelCode = $this->serverProductCode;
 		$ParcelInformation["Weight"] = $this->orderKey["weight"]*1000;
 		$ParcelInformation["WeightUnit"] =3;
 		$ParcelInformation["Length"] = $this->orderKey["length"];
 		$ParcelInformation["Width"] = $this->orderKey["width"];
 		$ParcelInformation["Height"] = $this->orderKey["height"];
 		$ParcelInformation["SizeUnit"]   = 2;
 		$ParcelInformation["ExistDangerousGoods"] = $this->orderKey["dangerousgoods"]?true:false;
 		$ParcelInformation["ProductInformations"] = null;
 			
 		$declareInvoice = array();
 		//报关产品
 		foreach($this->orderInvoiceItemKey as $oKey=>$row){
 			$cnname = (!empty($row['titleCn']) ? $row['titleCn'] : $row['titleEn']);
 			$enname = (!empty($row['titleEn']) ? $row['titleEn'] : $row['titleCn']);
 				
 			$declareInvoice_temp = array();
 			$declareInvoice_temp["Description"] = $enname.'-'.$cnname;
 			$declareInvoice_temp["Value"] = $row['value'];
 			$declareInvoice_temp["Quantity"] = $row["quantity"];
 			$declareInvoice_temp["Weight"] = $row["weight"];
 			$declareInvoice_temp["WeightUnit"] = 4;
 			$declareInvoice_temp["HSCode"] = $row["hsCode"];
 			$declareInvoice_temp["Sku"] = $row["sku"];
 			$declareInvoice_temp["Remark"] = $row["description"];
 			$declareInvoice_temp["Currency"] = $row["currencyCode"];
 			$declareInvoice_temp["ProductUrl"] = $row["url"];
 			$declareInvoice[] = $declareInvoice_temp;
 		}
 		$ParcelInformation["ProductInformations"] = $declareInvoice;
 		$data["ParcelInformation"] = $ParcelInformation;
 			
 	
 		//国家
 		$ct=Service_IddCountry::getAll();
 		$country=array();
 		foreach ($ct as $ck=>$cv){
 			$country[$cv['country_code']]=$cv['country_enname'];
 		}
 	
 		$RecipientAddress = array();
 		// TODO
 		$RecipientAddress["Country"] = $this->orderKey['consigneeCountryCode'];
 		$RecipientAddress["FirstName"] = $this->orderKey['consigneeName'];
 		$RecipientAddress["LastName"] = "";
 		$RecipientAddress["Company"] = $this->orderKey["consigneeCompanyName"];
 		$RecipientAddress["StreetAddress"] = $this->orderKey["consigneeStreet"];
 		$RecipientAddress["StreetAddress2"] = $this->orderKey["consigneeStreet1"];
 		$RecipientAddress["StreetAddress3"] = $this->orderKey["consigneeStreet2"];
 		$RecipientAddress["City"] = $this->orderKey['consigneeCity'];
 		$RecipientAddress["State"] = $this->orderKey['consigneeStateOrProvince'];
 		$RecipientAddress["IsResidential"] = false;
 		$RecipientAddress["PhoneNumber"] = $this->orderKey['consigneePhone'];
 		$RecipientAddress["ZIPCode"] = $this->orderKey['consigneePostalCode'];
 			
 		$data['RecipientAddress'] = $RecipientAddress;
 			
 		/***发件人信息****/
 		$ShipperAddress = array();
 		$ShipperAddress["FirstName"] = $this->shipperKey["shipperName"];
 		$ShipperAddress["LastName"] = "";
 		$ShipperAddress["Company"] = $this->shipperKey["shipperCompanyName"];
 		if($this->shipperKey["shipperStreet"]){
 			$streeArr = explode("||", $this->shipperKey["shipperStreet"]);
 			$ShipperAddress["StreetAddress"] = $streeArr[0]?$streeArr[0]:'';
 			$ShipperAddress["StreetAddress2"] = $streeArr[1]?$streeArr[1]:'';
 			$ShipperAddress["StreetAddress3"] = $streeArr[2]?$streeArr[2]:'';
 		}
 		$ShipperAddress["State"] = $this->shipperKey["shipperStateOrProvince"];
 		$ShipperAddress["City"] = $this->shipperKey["shipperCity"];
 		$ShipperAddress["PhoneNumber"] = $this->shipperKey["shipperPhone"];
 		$ShipperAddress["ZIPCode"] = $this->shipperKey["shipperPostCode"];
 		$ShipperAddress["Country"] = $this->shipperKey["shipperCountryCode"];
 		$ShipperAddress["Email"] = null;
 		$ShipperAddress = print_r(json_encode($ShipperAddress),true);
 		//冗余字段
 		$RedundancyField = array();
 		
 		//获取TNTpackages信息
 		
 		$TNTpackages = array();
 		
 		$packages = array();
 		
 		foreach ($this->orderInvoiceItemKey as $oKey=>$row){
 			$ARTICLE = array();
 			$ARTICLE['ITEMS'] =  $row['quantity'];
 			$ARTICLE['DESCRIPTION'] =  $row['titleEn'];
 			$ARTICLE['WEIGHT'] =  $row['weight'];
 			$ARTICLE['INVOICEVALUE'] =  $row['value'];
 			$ARTICLE['INVOICEDESC'] =  "";
 			$ARTICLE['HTS']   = $row['hsCode'];
 			$ARTICLE['COUNTRY'] =  $row['invoice_proplace'];
 			
 			
 			if(!isset($packages[$row['packageid']])){
 				$_packages = array();
 				$_packages_invoice = json_decode($row['packinfo'],1);
 				$_packages['ITEMS'] = $_packages_invoice[0]['ITEMS'];
 				$_packages['DESCRIPTION'] ="";
 				$_packages['LENGTH'] =$_packages_invoice[0]['LENGTH']/100;
 				$_packages['WIDTH'] =$_packages_invoice[0]['WIDTH']/100;
 				$_packages['HEIGHT'] =$_packages_invoice[0]['HEIGHT']/100;
 				$_packages['WEIGHT'] = $_packages_invoice[0]['WEIGHT']*$_packages_invoice[0]['ITEMS'];
 				$_packages['VOLUME'] =round($_packages['LENGTH']*$_packages['WIDTH']*$_packages['HEIGHT'],3);
 				$packages[$row['packageid']] = $_packages;
 			}
 			
 			$packages[$row['packageid']]['ARTICLE'][] = $ARTICLE;
 		}
 		$RedundancyField['TNTPackages'] = print_r(json_encode($packages),true);;
 		$RedundancyField['ShipperAddress'] = $ShipperAddress;
 		$RedundancyField['ProductCode'] = $this->orderKey['type']==3?"D":"N";
 			
 		//保险额外服务
 		$extservice = $this->orderExtservice;
 		if($extservice[0]['servicevalue']){
 			$RedundancyField['InsuredFee']= $extservice[0]['servicevalue'];
 		}
 		$RedundancyField["ShipperEIN"]=$this->orderKey["invoice_shippertax"] ;
 		$RedundancyField["RecipientEIN"]=$this->orderKey["invoice_consigneetax"];
 		$RedundancyField["CommodityCode"] = $declareInvoice[0]['HSCode']?$declareInvoice[0]['HSCode']:'';
 		//总价值
 		$RedundancyField['DeclaredValue']    = empty($this->orderKey["declaredValue"])?"0.01":$this->orderKey["declaredValue"];
 		//保险价值
 		if(!empty($this->orderKey["insurance_value_gj"])){
 			$RedundancyField['InsuredAmount'] = $this->orderKey["insurance_value_gj"];
 		}
 		if($ChannelCode=="TNT"){
 			//上传REF和绑定的账号
 			$condtion_sp['citycode'] = $this->orderKey["refer_hawbcode"];
 			$condtion_sp['status']   =   1;
 			$condtion_sp['productcode'] =   $ChannelCode;
 			$server_csi_prs=new Service_CsiProductRuleShipper();
 			$rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
 			if($rs_cisprs[0]){
 				//设定上发件人账号
 				$RedundancyField['AccuntNum'] = $rs_cisprs[0]['countnum'];
 				$RedundancyField["Reference"] = $rs_cisprs[0]['citycode'];
 			}
 		}
 		if($ChannelCode=="TNT"){
 			//是否废弃包裹
 			$RedundancyField['Abandon'] = $this->orderKey["untread"];
 		}
 		//$params = array('CustomerCode'=> $this->_user, 'packageMessage' => array($data));
 		$params["Version"] = '0.0.0.3';
 		$params["RequestId"] = empty($uuid)?"":$uuid;
 		$data["Token"] = '99999999999999999999999999999999';
 		$product_set_rs=Common_Common::getProductAllByCode($ChannelCode);
 		$data["ChannelName"] =$product_set_rs['ccode'];
 		//$data["ServiceTypeCode"] =$product_set_rs['name'];
 		$data["ServiceTypeCode"] = $this->orderData['service_code'];
 		$data["WarehouseCode"] =$product_set_rs['area'];
 		$data["LabelMarkText"] =null;
 		$data["RedundancyField"] =$RedundancyField;
 			
 		$params["data"] = $data;
 		return $params;
 	}
 	
 	//异步通知TMS
 	public function sendToTms($uuid=false){
 		if($this->serverProductCode!='TNT')
 			$params = $this->bindNoticeData('normal');
 		else 
 			$params = $this->bindNoticeData('tnt');
 		$sysResult = $this->PreAlertOrderService($params);
 		Ec::showError("**************start*************\r\n"
 				. print_r(json_encode($params), true)
 				. "\r\n" . print_r($sysResult, true)
 				. "**************end*************\r\n",
 				'YunExpress_API/Async_Create_response_info'.date("Ymd"));
 		
 		return $sysResult;
 	}
 
}