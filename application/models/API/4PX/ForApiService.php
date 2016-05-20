<?php

class API_4PX_ForApiService extends Common_APIChannelDataSet
{
	// token信息
	protected $_token = "";
	protected $_orderOnline = "";
	protected $_orderOnlineTool = "";
	
    public function __construct()
    {
    	// 创建日志目录
    	if (! is_dir ( APPLICATION_PATH . '/../data/log/4PX_API' )) {
    		mkdir(APPLICATION_PATH . '/../data/log/4PX_API', 0777);
    		chmod(APPLICATION_PATH . '/../data/log/4PX_API', 0777);
    	}
    }

    public function setParam($serviceCode = '', $orderCode = '', $channelId = '', $serverProductCode = '')
    {
        parent::__construct($serviceCode, $orderCode, $channelId, $serverProductCode);
        
        $this->_token = isset ( $this->accountData ["as_token"] ) ? $this->accountData ["as_token"] : '';
        $this->_orderOnline = isset ( $this->accountData ["as_address"] ) ? $this->accountData ["as_address"] : '';
        $this->_orderOnlineTool = isset ( $this->accountData ["as_address_other"] ) ? $this->accountData ["as_address_other"] : '';
    }

    public function getData()
    {
        return $this->_paramsSet();
    }
    
    /**
     * 创建并预报4PX物流订单
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
    			$callResult["error"] = $result["message"]."";
    			$callResult["errorCode"] = "001";
    			return $callResult;
    		}
    			
    		if($result["ack"]){
    			$result_temp = $result["data"]["return"];
    
    			if(strtoupper(trim($result_temp["ack"])) == "SUCCESS"){
    				$callResult["ack"] = 1;
    				$callResult["trackingNumber"] = $result_temp["trackingNumber"];
    			}else {
    				//4px异常信息
    				$errorMessage = "";
    				if(!empty($result_temp["errors"][0])){
    					foreach ($result_temp["errors"] as $eKey=>$eVal){
    						$errorMessage .= $eVal["cnMessage"].",".$eVal["cnAction"];
    					}
    				}else{
    					$errorMessage = $result_temp["errors"]["cnMessage"].",".$result_temp["errors"]["cnAction"];
    				}
    					
    				$callResult["error"] = $errorMessage;
    				$callResult["errorCode"] = "002";
    			}
    		}
    			
    	} catch (Exception $e) {
    		$callResult["error"] = "同步未知异常，订单号：".$orderCode."异常信息：".$e->getMessage();
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
    	$data["orderNo"] = $this->orderCode;
    
    	//获取渠道运输方式
    	$data["productCode"] = $this->orderKey["channelCodeServer"];
    	$data["returnSign"] = 'N'; // TODO 通过渠道配置
    
    	$data["destinationCountryCode"] = $this->orderKey['consigneeCountryCode'];
    
    	$data["consigneeName"] = $this->orderKey['consigneeName'];
    	$data["street"] = $this->orderKey['consigneeStreet'];
    	$data["city"] = $this->orderKey['consigneeCity'];
    	$data["stateOrProvince"] = $this->orderKey['consigneeStateOrProvince'];
    	$data["consigneeTelephone"] = $this->orderKey['consigneePhone'];
    	$data["consigneeEmail"] = $this->orderKey['consigneeEmail'];
    	$data["consigneePostCode"] = $this->orderKey['consigneePostalCode'];
    	$data["mctCode"] = "5";
//     	$data["cargoCode"] = "P";
    
    	/***发件人信息****/
    	$data["shipperCity"] = $this->shipperKey["shipperCity"];
    	$data["shipperStateOrProvince"] = $this->shipperKey["shipperStateOrProvince"];
    	$data["shipperName"] = $this->shipperKey["shipperName"];
    	$data["shipperCompanyName"] = $this->shipperKey["shipperCompanyName"];
    	$data["shipperTelephone"] = $this->shipperKey["shipperPhone"];
    	$data["shipperAddress"] = $this->shipperKey["shipperStreet"];
    
    	$declareInvoice = array();
    	//报关产品
    	foreach($this->orderInvoiceItemKey as $oKey=>$row){
    		$cnname = (!empty($row['titleCn']) ? $row['titleCn'] : $row['titleEn']);
    		$enname = (!empty($row['titleEn']) ? $row['titleEn'] : $row['titleCn']);
    		
    		$declareInvoice_temp = array();
    		$declareInvoice_temp["eName"] = $enname;
    		$declareInvoice_temp["name"] = $cnname;
    		$declareInvoice_temp["unitPrice"] = $row['value'];
    		$declareInvoice_temp["declarePieces"] = $row["quantity"];
    		// 			$declareInvoice_temp["declareUnitCode"] = "PCE";
    		$declareInvoice[] = $declareInvoice_temp;
    	}
    		
    	$data["declareInvoice"] = $declareInvoice;
    
    	$sysResult = $this->createAndPreAlertOrderService($data);
    
    	Ec::showError("**************start*************\r\n"
    			. print_r($data, true)
    			. "\r\n" . print_r($sysResult, true)
    			. "**************end*************\r\n",
    			'4PX_API/4pxExpress_create_response_info'.date("Ymd"));
    	return $sysResult;
    }
    
    /**
     * 创建并预报订单
     * @param unknown_type $data
     * @return multitype:number string NULL array
     */
    public function createAndPreAlertOrderService($data = array()){
    	/*
    	 * 请求服务
    	*/
    	$params = array(
    			'arg0' => $this->_token,
    			'arg1' => $data
    	);
    	$result = $this->excuteService($this->_orderOnline,$params,"createAndPreAlertOrderService");
    
    	return $result;
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
    
    /**
     * 拦截订单
     * @param unknown_type $data 订单数组 $data = {"R12312312","R32342342"}
     * @return multitype:number string NULL array
     */
    public function cargoHoldService($data = array()){
    	$return = array("ack"=>0,"message"=>"");
    	/*
    	 * 请求服务
    	*/
    	$params = array(
    			'arg0' => $this->_token,
    			'arg1' => $data
    	);
    	$result = $this->excuteService($this->_orderOnlineTool,$params,"cargoHoldService");
    	if($result["ack"] == 1){
    		//调用成功
    		if(strtoupper(trim($result["data"]["return"]["ack"])) == "SUCCESS"){
    			$return["ack"] = 1;
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
    
    /**
     * 查询订单
     * @param unknown_type $data
     */
    public function findOrderService($data = array()){
    	$return = array("ack"=>0,"message"=>"","data"=>"");
    	/*
    	 * 请求服务
    	*/
    	$params = array(
    			'arg0' => $this->_token,
    			'arg1' => $data
    	);
    	$result = $this->excuteService($this->_orderOnline,$params,"findOrderService");
    
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
    	$options = array(
    			"trace" => true,
    			"exceptions" => true,
    			"connection_timeout" => 1000,
    			"encoding" => "utf-8",
    	);
    	 
    	try {
    		$client = new SoapClient($url, $options);
    		$data = $client->$method($params);
    		$data = self::object_array($data);
    		$result["ack"] = 1;
    		$result["data"] = $data;
    	} catch (SoapFault  $e) {
    		$result["message"] = $e->getMessage();
    	}
    	 
    	return $result;
    }
    
    public static function object_array($array){
    	if(is_object($array)){
    		$array = (array)$array;
    	}
    	if(is_array($array)){
    		foreach($array as $key=>$value){
    			$array[$key] = self::object_array($value);
    		}
    	}
    	return $array;
    }
}