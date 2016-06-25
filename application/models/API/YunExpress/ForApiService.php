<?php

class API_YunExpress_ForApiService extends Common_APIChannelDataSet
{
	// token信息
	protected $_user = "";
	protected $_orderOnline = "";
	
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
    
    	/*
    	 * 构造订单信息
    	*/
    	$data = array();
    
    	//销售产品
    	$items = array();
    	$data["ForecastNumber"] = $this->orderCode;
    
    	//获取渠道运输方式
    	$data["ChannelCode"] = $this->serverProductCode;
    	$data["Weight"] = $this->orderKey["weight"];
    	$data["Length"] = $this->orderKey["length"];
    	$data["Width"] = $this->orderKey["width"];
    	$data["Height"] = $this->orderKey["height"];
    	$data["type"]   = $this->orderKey["type"];
    	$data["isOrdangerousgoods"] = $this->orderKey["dangerousgoods"];
    	// 收件人
    	$data["ShippingCountryCode"] = $this->orderKey['consigneeCountryCode'];
    	
    	//国家
    	$ct=Service_IddCountry::getAll();
    	$country=array();
    	foreach ($ct as $ck=>$cv){
    		$country[$cv['country_code']]=$cv['country_enname'];
    	}
    	// TODO
    	$data["ShippingCountryEnName"] = $country[$this->orderKey['consigneeCountryCode']];
    	$data["ShippingFirstName"] = $this->orderKey['consigneeName'];
    	$data["ShippingLastName"] = "";
    	$data["ShippingAddress"] = $this->orderKey["consigneeStreet"];
    	$data["ShippingAddress1"] = $this->orderKey["consigneeStreet1"];
    	$data["ShippingAddress2"] = $this->orderKey["consigneeStreet2"];
    	$data["ShippingCity"] = $this->orderKey['consigneeCity'];
    	$data["ShippingState"] = $this->orderKey['consigneeStateOrProvince'];
    	$data["ShippingStateCode"] = $this->orderKey['consigneeStateOrProvince'];//TODO
    	$data["ShippingPhone"] = $this->orderKey['consigneePhone'];
    	$data["ShippingTaxId"] = "";
    	$data["ShippingZip"] = $this->orderKey['consigneePostalCode'];
    
    	/***发件人信息****/
    	$data["SenderFirstName"] = $this->shipperKey["shipperName"];
    	$data["SenderLastName"] = "";
    	$data["SenderCompany"] = $this->shipperKey["shipperCompanyName"];
    	$data["SenderAddress"] = $this->shipperKey["shipperStreet"];
    	$data["SenderState"] = $this->shipperKey["shipperStateOrProvince"];
    	$data["SenderCity"] = $this->shipperKey["shipperCity"];
    	$data["SenderPhone"] = $this->shipperKey["shipperPhone"];
    	$data["SenderZip"] = $this->shipperKey["shipperPostCode"];
    	$data["SenderCountryCode"] = $this->shipperKey["shipperCountryCode"];
    	
    	$declareInvoice = array();
    	//报关产品
    	foreach($this->orderInvoiceItemKey as $oKey=>$row){
    		$cnname = (!empty($row['titleCn']) ? $row['titleCn'] : $row['titleEn']);
    		$enname = (!empty($row['titleEn']) ? $row['titleEn'] : $row['titleCn']);
    		
    		$declareInvoice_temp = array();
    		$declareInvoice_temp["ApplicationEnName"] = $enname;
    		$declareInvoice_temp["ApplicationCnName"] = $cnname;
    		$declareInvoice_temp["UnitPrice"] = $row['value'];
    		$declareInvoice_temp["Qty"] = $row["quantity"];
    		$declareInvoice_temp["UnitWeight"] = $row["weight"];
    		$declareInvoice_temp["HSCode"] = $row["hsCode"];
    		$declareInvoice_temp["SKU"] = $row["sku"];
    		$declareInvoice_temp["Remark"] = $row["description"];
    		$declareInvoice_temp["Currency"] = $row["currencyCode"];
    		$declareInvoice_temp["SalesUrl"] = $row["url"];
    		$declareInvoice[] = $declareInvoice_temp;
    	}
    	
    	$data["applicationInfos"] = $declareInvoice;
        //保险额外服务
        $data["extservice"] = $this->orderExtservice;
        //
        $data["invoice_shippertax"]=$this->orderKey["invoice_shippertax"] ;
        $data["invoice_consigneetax"]=$this->orderKey["invoice_consigneetax"];
        //总价值
        $data['declaredValue']    = $this->orderKey["declaredValue"];
        //保险价值
        $data['insurance_value_gj']   =  empty($this->orderKey["insurance_value_gj"])?0:$this->orderKey["insurance_value_gj"];
        //注册编号
        $data["RegisterNumber"] = empty($this->customer_ext["registernumber"])?"":$this->customer_ext["registernumber"];
        $data["AgencyCode"]		= empty($this->customer_ext["agencycode"])?"":$this->customer_ext["agencycode"];
        //citycode
        if($data["ChannelCode"]=="G_DHL"||$data["ChannelCode"]=="TNT"){
        	//上传REF和绑定的账号
        	//$data["reference"] 		 = $this->orderKey["refer_hawbcode"];
        	$condtion_sp['citycode'] = $this->orderKey["refer_hawbcode"];
        	$condtion_sp['status']   =   1;
        	$condtion_sp['productcode'] =   $data["ChannelCode"];
        	$server_csi_prs=new Service_CsiProductRuleShipper();
        	$rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
        	if($rs_cisprs[0]){
        		//设定上发件人账号
        		$data['accuntnum'] = $rs_cisprs[0]['countnum'];
        		$data["reference"] = $rs_cisprs[0]['citycode'];
        	}
        }
        if($data["ChannelCode"]=="TNT"){
        	//是否废弃包裹
        	$data['untread'] = $this->orderKey["untread"];
        }
        $params = array('CustomerCode'=> $this->_user, 'packageMessage' => array($data));  

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
    	$url = $this->_orderOnline . "/api/Order/PacketOrder";
		$url = "http://test.hwcservice.com/ChinaPost/Api/Order/PacketOrder";
    	$result = $this->excuteService($url, json_encode($data), "POST");
		header("Content-type: text/html; charset=utf-8");
    
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
			curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
			
			if($method == 'POST') {
				curl_setopt($tuCurl, CURLOPT_POST, 1);
				curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $params);
			}
			
			curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($params)));

			// print_r($tuCurl);die;
			$data = curl_exec($tuCurl);
			
			
			
    		$data = Common_Common::objectToArray(json_decode($data));
    		$result["ack"] = 1;
    		$result["data"] = $data;
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
      	
      	$array_three['bxmoney'] = intval($this->orderKey["insurance_value_gj"]*100)/100;
      	$array_three['loanmoney'] = 0;
      	$array_three['minordernum'] = 0;
      	$array_three['mpostalnum'] = $this->orderData['server_hawbcode'];
      	$array_three['ordernum'] = $this->orderCode;
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
      	$xml = xml_encode($xmlarray['orders'],'orders','item');
      	$xml=preg_replace('/item_(\d)+/i','item', $xml);  
        $url="http://shipping2.ems.com.cn/partner/api/public/p/orderSpecial";
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
    			//在本地的对照库中找到地址，然后取出市 和 省
    			$condition['positionpname'] = strtoupper($positionename);
    			$res = Service_CsiGeographical::getByCondition($condition);
    			if($res[0]&&is_array($res[0])){
    				$citycname =  $res[0]['citycname'];
    				$provincecname = $res[0]['provincecname'];
    				//获取省区域代码
    				$url = 'http://shipping2.ems.com.cn/partner/api/public/p/area/cn/province/list';
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
    				$url = 'http://shipping2.ems.com.cn/partner/api/public/p/area/cn/city/list/'.$return_arr['provincecode'];
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
                $url = "http://test.hwcservice.com/ChinaPost/Api/TrackingService/QueryTrackingStatus?type=json";
                $sendParams = array(
                	"data"=>array("TrackingNumber"=>$TrackingNumber,"ChannelName"=>$ChannelName),
           			"RequestId"=>null,
                	"RequestTime"=>date('Y-m-d H:i:s'),
                	"Version"=>"0.0.0.3"	
                );
                $sendParams = json_encode($sendParams);
                $header =array("Content-Type:application/json; charset=utf-8");
                $result = $this->curl_send($url,$sendParams,$header,"post","tmsuser:123456");
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
    private function  curl_send($url,$data='',$header=array(),$type='get',$authentication=false){
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
 	
 	//推送TNTXML的服务
 	public function sendXmlToTntService(){
 	      $callResult = array("ack"=>0,"orderCode"=>$this->orderCode,"error"=>"","errorCode"=>"");
 	      $callResult["orderCode"] = $this->orderCode;
 	  try {
     	    $length_package = $this->orderKey["length"]/100;
     	    $width_package = $this->orderKey["width"]/100;
     	    $height_package = $this->orderKey["height"]/100;
     		$volume = floor($length_package*$width_package*$height_package*1000)/1000;
     		$type = $this->orderKey['type']==3?"D":"N";
     		if($type == "D"){
     		    $volume="0.0";
     		}
     		$consignee_mobile = empty($this->orderKey['consignee_mobile']) ? $this->orderKey['consignee_telephone'] : $this->orderKey['consignee_mobile'];
     		$consignee_telephone = empty($this->orderKey['consignee_telephone']) ? $this->orderKey['consignee_mobile'] : $this->orderKey['consignee_telephone'];
     		$xml = '<?xml version="1.0" encoding="utf-8"?>
<ESHIPPER>
  <LOGIN>
    <COMPANY></COMPANY>
    <PASSWORD></PASSWORD>
    <APPID>EC</APPID>
    <APPVERSION>2.2</APPVERSION>
  </LOGIN>
  <CONSIGNMENTBATCH>
    <GROUPCODE>1</GROUPCODE>
    <SENDER>
      <COMPANYNAME>'.$this->shipperKey["shipperCompanyName"].'</COMPANYNAME>
      <STREETADDRESS1>'.$this->shipperKey['shipperStreet'].'</STREETADDRESS1>
      <STREETADDRESS2/>
      <STREETADDRESS3/>
      <CITY>'.$this->shipperKey["shipperCity"].'</CITY>
      <PROVINCE>'.$this->shipperKey["shipperStateOrProvince"].'</PROVINCE>
      <POSTCODE>'.$this->shipperKey["shipperPostCode"].'</POSTCODE>
      <COUNTRY>'.$this->shipperKey["shipperCountryCode"].'</COUNTRY>
      <ACCOUNT>1</ACCOUNT>
      <VAT/>
      <CONTACTNAME>'.$this->shipperKey["shipperName"].'</CONTACTNAME>
      <CONTACTDIALCODE>'.$this->shipperKey["shipperPhone"].'</CONTACTDIALCODE>
      <CONTACTTELEPHONE>'.$this->shipperKey["shipperPhone"].'</CONTACTTELEPHONE>
      <CONTACTEMAIL/>
      <COLLECTION>
        <SHIPDATE>'.date("d/m/Y",time()+3600*24).'</SHIPDATE>
        <PREFCOLLECTTIME>
          <FROM>0900</FROM>
          <TO>1600</TO>
        </PREFCOLLECTTIME>
        <ALTCOLLECTTIME/>
        <COLLINSTRUCTIONS>this is import</COLLINSTRUCTIONS>
        <CONFIRMATIONEMAILADDRESS/>
      </COLLECTION>
    </SENDER>
    <CONSIGNMENT>
      <CONREF>'.$this->orderCode.'</CONREF>
      <CONNUMBER>'.$this->orderData['server_hawbcode'].'</CONNUMBER>
      <DETAILS>
        <DELIVERY/>
        <CONNUMBER>'.$this->orderData['server_hawbcode'].'</CONNUMBER>
        <CUSTOMERREF/>
        <CONTYPE>'.$type.'</CONTYPE>
        <PAYMENTIND/>
        <ITEMS>1</ITEMS>
        <TOTALWEIGHT>'.$this->orderKey['weight'].'</TOTALWEIGHT>
        <TOTALVOLUME>'.$volume.'</TOTALVOLUME>
        <CURRENCY>USD</CURRENCY>
        <GOODSVALUE>'.$this->orderKey['declaredValue'].'</GOODSVALUE>
        <SERVICE>TNT</SERVICE>
        <OPTION/>
        <DESCRIPTION/>
        <DELIVERYINST>this import</DELIVERYINST>
        <CUSTOMCONTROLIN/>
        <HAZARDOUS/>
        <UNNUMBER/>
        <PACKAGE>
          <ITEMS>1</ITEMS>
          <DESCRIPTION>this import</DESCRIPTION>
          <LENGTH>'.$length_package.'</LENGTH>
          <WIDTH>'.$width_package.'</WIDTH>
          <HEIGHT>'.$height_package.'</HEIGHT>
          <WEIGHT>'.$this->orderKey['weight'].'</WEIGHT>';
 			foreach($this->orderInvoiceItemKey as $oKey=>$row){
 				$cnname = (!empty($row['titleCn']) ? $row['titleCn'] : $row['titleEn']);
 				$enname = (!empty($row['titleEn']) ? $row['titleEn'] : $row['titleCn']);
 				$xml.='
          <ARTICLE>
            <ITEMS>'.$row['quantity'].'</ITEMS>
            <DESCRIPTION>'.$enname.'</DESCRIPTION>
            <WEIGHT>'.$row['weight'].'</WEIGHT>
            <INVOICEVALUE>'.$row['value'].'</INVOICEVALUE>
            <INVOICEDESC>this is import!'.$row['description'].'</INVOICEDESC>
            <HTS>'.$row['hsCode'].'</HTS>
            <COUNTRY/>
          </ARTICLE>';
 			}
 			$xml.='
        </PACKAGE>
 		<RECEIVER>
          <COMPANYNAME>'.$this->orderKey['consigneeCompanyName'].'</COMPANYNAME>
          <STREETADDRESS1>'.$this->orderKey['consigneeStreet'].'</STREETADDRESS1>
          <STREETADDRESS2>'.$this->orderKey['consigneeStreet1'].'</STREETADDRESS2>
          <STREETADDRESS3>'.$this->orderKey['consigneeStreet2'].'</STREETADDRESS3>
          <CITY>'.$this->orderKey['consigneeCity'].'</CITY>
          <PROVINCE>'.$this->orderKey['consigneeStateOrProvince'].'</PROVINCE>
          <POSTCODE>'.$this->orderKey['consigneePostalCode'].'</POSTCODE>
          <COUNTRY>'.$this->orderKey['consigneeCountryCode'].'</COUNTRY>
          <VAT/>
          <CONTACTNAME>'.$this->orderKey['consigneeName'].'</CONTACTNAME>
          <CONTACTDIALCODE>'.$consignee_mobile.'</CONTACTDIALCODE>
          <CONTACTTELEPHONE>'.$consignee_telephone.'</CONTACTTELEPHONE>
          <CONTACTEMAIL>'.$this->orderKey['consigneeEmail'].'</CONTACTEMAIL>
          <ACCOUNT/>
          <ACCOUNTCOUNTRY/>
          <DELIVERY/>
        </RECEIVER>
      </DETAILS>
    </CONSIGNMENT>
  </CONSIGNMENTBATCH>
  <ACTIVITY>
    <CREATE>
      <CONREF>'.$this->orderCode.'</CONREF>
    </CREATE>
    <PRINT>
      <REQUIRED>
        <CONREF>'.$this->orderCode.'</CONREF>
        <CONNUMBER>'.$this->orderData['server_hawbcode'].'</CONNUMBER>
      </REQUIRED>
      <CONNOTE>
        <CONREF>'.$this->orderCode.'</CONREF>
        <CONNUMBER>'.$this->orderData['server_hawbcode'].'</CONNUMBER>
      </CONNOTE>
      <LABEL>
        <CONREF>'.$this->orderCode.'</CONREF>
        <CONNUMBER>'.$this->orderData['server_hawbcode'].'</CONNUMBER>
      </LABEL>
      <MANIFEST>
        <CONREF>'.$this->orderCode.'</CONREF>
        <CONNUMBER>'.$this->orderData['server_hawbcode'].'</CONNUMBER>
        <GROUPCODE>1</GROUPCODE>
      </MANIFEST>
      <INVOICE>
        <CONREF>'.$this->orderCode.'</CONREF>
        <CONNUMBER>'.$this->orderData['server_hawbcode'].'</CONNUMBER>
      </INVOICE>
      <EMAILTO>
        <Type attribute="">SMTP</Type>
      </EMAILTO>
      <EMAILFROM>'.$this->orderKey['consigneeEmail'].'</EMAILFROM>
    </PRINT>
    <SHOW_GROUPCODE/>
  </ACTIVITY>
</ESHIPPER>
';
 	 $dir = APPLICATION_PATH."/../data/tntftp/".date("Ymd").DIRECTORY_SEPARATOR;
 	 $filename = date("His").rand(1,9999999999999999).'.xml';
 	 Common_Common::mkdirs($dir);
 	 while(file_exists($dir.$filename)){
 	 	$filename = date("His").rand(1,9999999999999999).'.xml';
 	 };
 	 $realpath = $dir.$filename;		
 	 file_put_contents($realpath,$xml);		
     $config = array(
            'hostname' => '120.25.169.143',
            'username' => 'root',
            'password' => 'root',
            'port' => 21
            );
     $ftp = new Process_Ftp();
     $ftp->connect($config);
     if(!$ftp->upload($realpath,$filename)){
         $callResult["ack"] = -1;
         $callResult["error"] = "订单号：".$this->orderCode."异常信息：上传xml失败";
         return $callResult;
     }
     $callResult["ack"] = 1;
     //记录日志
     Ec::showError("**************start*************\r\n"
     		. $this->orderCode."=>".$realpath
     		. "\r\n" . print_r($callResult, true)
     		. "**************end*************\r\n",
     		'YunExpress_API/notify_tnt_xml'.date("Ymd"));
 	} catch (Exception $e) {
 	    $callResult["error"] = "同步未知异常，订单号：".$this->orderCode."异常信息：".$e->getMessage();
 	}
 	return $callResult;		
 }
 
}