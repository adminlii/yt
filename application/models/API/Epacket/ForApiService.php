<?php
include_once 'XmlHandle.php';
class API_Epacket_ForApiService extends Common_APIChannelDataSet
{
	
	/*
	 * 生产
	*/
	protected $_version = "international_eub_us_1.1";
	
	protected $_authenticate = "";
	
	protected $_url = "";
	
	protected $_customer = "";
	
    public function __construct()
    {
    	// 创建日志目录
    	if (! is_dir ( APPLICATION_PATH . '/../data/log/EUBOF_API' )) {
    		mkdir(APPLICATION_PATH . '/../data/log/EUBOF_API', 0777);
    		chmod(APPLICATION_PATH . '/../data/log/EUBOF_API', 0777);
    	}
    }

    public function setParam($serviceCode = '', $orderCode = '', $channcelId = '', $server_product_code = '')
    {
        parent::__construct($serviceCode, $orderCode, $channcelId, $server_product_code);
        
//         print_r($this->accountData);die;
        $this->_authenticate = $this->accountData["as_token"];
        $this->_version = $this->accountData["as_version"];
        $this->_url = $this->accountData['as_url'];
        $this->_customer = $this->accountData['as_user'];
    }
    
    /**
     * @return multitype:multitype:
     */
    public function getData()
    {
    	return $this->_paramsSet();
    }

    /**
     * 创建订单
     * @param unknown_type $data
     */
    public function createAndPreAlertOrderService($data){
    	/*
    	 * 设置url
    	 * $this->_url . "
    	 * $this->_url . "
    	*/
    	$url = $this->_url . "/order/";
    
    	//设置请求头 post
    	$post_header = array("Content-type: text/xml","version: ".$this->_version,"authenticate: ".$this->_authenticate);
    
//     	print_r($post_header);die;
    	/*
    	 * 请求服务
    	*/
    	$result = $this->excuteServicePostMethod($url,$post_header,$data);
    
    	/*
    	 * 处理请求结果
    	*/
    	return $result;
    
    }
    
    /**
     * 校验订单
     * @param unknown_type $data
     */
    public function validate($data){
    	/*
    	 * 设置url
    	*/
    	$url = $this->_url . "/validate";
    
    	//设置请求头 post
    	$post_header = array("Content-type: text/xml","version: ".$this->_version,"authenticate: ".$this->_authenticate);
    
    	/*
    	 * 请求服务
    	*/
    	$result = $this->excuteServicePostMethod($url,$post_header,$data);
    
    	/*
    	 * 处理请求结果
    	*/
    	return $result;
    }
    
    /**
     * 查询订单
     * @param unknown_type $code
     * @return Ambigous <mixed, string, unknown>
     */
    public function findOrderService($code){
    	$url = $this->_url . "/order/".$code;
    
    	$get_header = array("version:".$this->_version,"authenticate: ".$this->_authenticate);
    
    	$result  = self::excuteServiceGetMethod($url,$get_header,"GET");
    
    	return $result;
    }
    
    /**
     * 获取轨迹信息
     * @param unknown_type $code
     * @return multitype:number string multitype: mixed NULL
     */
    public function cargoTrackingService($code){
    	$url = $this->_url . "/track/query/cn/".$code;
    
    	$get_header = array("version:".$this->_version,"authenticate: ".$this->_authenticate);
    
    	$result  = self::excuteServiceGetMethod($url,$get_header,"GET");
    
    	return $result;
    }
    
    /**
     * 获取国内区域
     * @return multitype:number string multitype: mixed NULL
     */
    public function getProvince(){
    	$url = $this->_url . "/area/cn/province/list";
    
    	$get_header = array("version:".$this->_version,"authenticate: ".$this->_authenticate);
    	 
    	$result  = self::excuteServiceGetMethod($url,$get_header,"GET");
    
    	return $result;
    }
    
    /**
     * 获取标签
     * @param unknown_type $code
     * @return string
     */
    public function getLabel($code,$orderCode) {
    	$str = $this->_authenticate.$code;
    	$md5auth = md5($str);
//     	$url = "http://labels.ems.com.cn/partner/api/public/p/static/label/download/".$md5auth."/".$code.".pdf";
    	$url = $this->accountData['as_label_url'] .$md5auth."/".$code.".pdf";
    	$get_header = array("version:".$this->_version,"authenticate: ".$this->_authenticate);
    
    	$result  = self::excuteServiceGetMethod($url,$get_header,"GET");
    	if(!empty($result['result'])){
    		if(substr($result['result'], 0,6) == '<html>'){
    			$result["state"] = 0;
    			$result["message"] = $result['result'];
    			return $result;
    		}
    	}else{
    		$result["state"] = 0;
    		$result["message"] = "EUB未返回标签信息.";
    		return $result;
    	}
    
    	try {
	    	// 文件
	    	$path = APPLICATION_PATH . $this->accountData['as_label_path'] . "/" . $orderCode;
	    	// 转成图片保存
	    	API_Common_ChangeNOFactory::autoPdf2png($result['result'], $path);
	    	
	    	//删除原标签
	    	Service_OrderLabel::delete($orderCode,'order_code');
	    	
	    	//加入到标签里面
	    	$labelRow = array(
	    			"order_code" => $orderCode,
	    			"path" => $path,
	    			"ol_label_url" => $url,
	    			"sm_code" => '',
	    	);
	    	Service_OrderLabel::add($labelRow);
    	} catch(Exception $e) {
    		$result["state"] = 0;
    		$result["message"] = "下载标签失败." . $e->getMessage();
    		return $result;
    	}
    	
    	return $result;
    }
    
    /**
     * 取消订单
     * @return Ambigous <mixed, string, unknown>
     * success:Array ( [state] => 1 [message] => [result] => Array ( [response] => Array ( [version] => international_eub_us_1.1 [status] => success [code] => S01 [description] => 撤销订单：LN361549165CN 成功 ) ) [errNo] => 0 )
     * error:  Array ( [state] => 1 [message] => [result] => Array ( [response] => Array ( [version] => international_eub_us_1.1 [status] => error [code] => R03 [description] => 邮件号：LN361549165CNs 不存在 ) ) [errNo] => 0 )
     */
    public function cargoHoldService($code){
    	$return = array("state"=>0,"message"=>"");
    
    	$url = $this->_url . "/order/".$code;
    
    	$get_header = array("version:".$this->_version,"authenticate: ".$this->_authenticate);
    	 
    	$result  = self::excuteServiceGetMethod($url,$get_header,"DELETE");
    
    	$result["result"] = XML_unserialize($result["result"]);
    
    	if($result["state"]){
    		$response = $result["result"]["response"];
    		if(isset($response["status"]) && strtoupper(trim($response["status"])) == "SUCCESS"){
    			$return["state"] = 1;
    		}else{
    			$return["message"] = $response["description"];
    		}
    	}else{
    		$return["message"] = $result["message"];
    	}
    
    	return $return;
    }
    
    public function excuteServiceGetMethod($url,$get_header,$method='GET'){
    	$return = array('state' => 0, 'message' => '', 'result' => "", 'errNo' => 0);
    	try {
    		$ch = curl_init($url);
    		$method = strtoupper($method);
    			
    		if(!in_array($method, array("GET","PUT","POST","DELETE"))){
    			throw new Exception("请求方法类型只能为：GET、PUT、POST、DELETE");
    		}
    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); //SSL
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//SSL
    			
    		curl_setopt($ch, CURLOPT_HTTPHEADER, $get_header);
    			
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//這個若是沒設 , curl_exec($curl) 會直接印出來
    		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
    			
    		curl_setopt ($ch, CURLOPT_HEADER, 0); // 得到回傳的HTTP頁面.
    			
    		//设置超时时间
    		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    			
    		$apiResult = curl_exec($ch);
    			
    		$errNo = curl_errno($ch);
    		if ($errNo) {
    			//返回错误码
    			$return['errNo'] = $errNo;
    			$errorStr = curl_error($ch);
    			switch ((int)$errNo) {
    				case 6: //避免一直发邮件 URL报错
    					break;
    				case 7: //无法通过 connect() 连接至主机或代理服务器
    					break;
    				case 28: //超时
    					break;
    				case 56: //接收网络数据失败
    					break;
    				default:
    					Common_Email::sendErrorMessage('EpacketApiError', 'errNo:' . $errNo . " error:" . $errorStr . ' URL: ' . $url  . "\r\n result:" . print_r(json_decode($apiResult, true), true));
    					break;
    			}
    			throw new Exception($errorStr);
    		}
    		curl_close($ch);
    		$return['state'] = 1;
    
    		//返回数据
    		$return['result'] = $apiResult;//XML_unserialize($apiResult);
    	} catch (Exception $e) {
    		$return['state'] = 0;
    		$return['message'] = $e->getMessage();
    	}
    
    	/*
    	$date = date("Ymd");
    	Ec::showError("**************start*************\r\n"
    			. $url
    			. print_r($get_header, true)
    			. "\r\n" . print_r($return, true)
    			. "**************end*************\r\n",
    			'EUBOF_API/epacket_create_response_info'.$date);
    	*/
    	
    	return $return;
    }
    
    /**
     * 调用服务 post
     * @param unknown_type $url 请求服务连接
     * @param unknown_type $data 请求参数
     * @return mixed 调用结果
     */
    public function excuteServicePostMethod($url, $post_header,$post_data)
    {
    	$return = array('state' => 0, 'message' => '', 'result' => array(), 'errNo' => 0);
    	$apiResult = "";
    	try {
    		$ch = curl_init($url);
    		curl_setopt($ch, CURLOPT_URL, $url);
    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    			
    		curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    
    		//设置超时时间
    		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    
    		//API返回数据
    		$apiResult = curl_exec($ch);
    		$errNo = curl_errno($ch);
    			
    		if ($errNo) {
    			//返回错误码
    			$return['errNo'] = $errNo;
    			$errorStr = curl_error($ch);
    			switch ((int)$errNo) {
    				case 6: //避免一直发邮件 URL报错
    					break;
    				case 7: //无法通过 connect() 连接至主机或代理服务器
    					break;
    				case 28: //超时
    					break;
    				case 56: //接收网络数据失败
    					break;
    				default:
    					Common_Email::sendErrorMessage('EpacketApiError', 'errNo:' . $errNo . " error:" . $errorStr . ' URL: ' . $url . "\r\n Data:" . print_r($post_data, true) . "\r\n result:" . print_r(json_decode($apiResult, true), true));
    					break;
    			}
    			throw new Exception($errorStr);
    		}
    		curl_close($ch);
    		$return['state'] = 1;
    		//返回数据
    		$return['result'] = XML_unserialize($apiResult);
    	} catch (Exception $e) {
    		$return['state'] = 0;
    		$return['message'] = $e->getMessage();
    	}
    	
    	$date = date("Ymd");
    	Ec::showError("**************start*************\r\n"
    			. $url
    			. print_r($post_header, true)
    			. print_r($post_data, true)
    			. "\r\n" . $return['state']
    			. "\r\n" . $apiResult
    			. "**************end*************\r\n",
    			'EUBOF_API/epacket_create_response_info'.$date);
    
    	return $return;
    }
    
    /**
     * 创建并预报e邮宝线下订单
     * @param unknown_type $orderCode 订单号
     */
    public function createAndPreAlertOrderServiceByCode(){
    
    	//所有创建订单业务方法统一返回格式：调用状态、跟踪号、EC订单号、错误信息、错误代码
    	//errorCode 001 系统异常、内部消化 ,002 业务异常，需要操作员处理
    	$callResult = array("ack"=>0,
    			"orderCode"=>$this->orderCode,
    			"trackingNumber"=>"",
    			"error"=>"",
    			"errorCode"=>"");
    	
    	$callResult["orderCode"] = $this->orderCode;
    	
    	try {
    		
    		/**
    		 * 
    		 */   
    		/*
    		 * 1、验证订单是否可用
    		*/
    		//单号不能为空
    		if(empty($this->orderCode)){
    			$callResult["error"] = "单号不能为空！";
    			return $callResult;
    		}
    			
    		//调用服务接口，同步审核订单
    		$request_xml = $this->getOrderContent($this->orderCode);
//     		print_r($request_xml);die;
    		$result = $this->createAndPreAlertOrderService($request_xml);
    		
    		/*
    		$date = date("Ymd");
    		Ec::showError("**************start*************\r\n"
    				. print_r($request_xml, true)
    				. "\r\n" . print_r($result, true)
    				. "**************end*************\r\n",
    				'EUBOF_API/epacket_create_response_info'.$date);
    			*/
    		
    		if(!$result["state"]){
    			$callResult["error"] = $result["message"];
    			$callResult["errorCode"] = "001";
    			return $callResult;
    		}
    			
    		if($result["state"]){
    			$result_temp = $result["result"];
    
    			if(isset($result_temp["order"]["mailnum"]) && !empty($result_temp["order"]["mailnum"])){
    				$callResult["ack"] = 1;
    				$callResult["trackingNumber"] = $result_temp["order"]["mailnum"];
    				    				
    				//获取标签
    				$getResult = $this->getLabel($callResult["trackingNumber"],$this->orderCode);
    				if($getResult['state'] == '0') {
    					$label_err = array(
    							'order_code' => $this->orderCode,
    							'tracking_code' => $callResult["trackingNumber"],
    							'service_code' => $this->serviceCode,
    							'service_product_code' => $this->serverProductCode,
    							'create_date' => date('Y-M-D h:i:s'),
    							'modify_date' => date('Y-M-D h:i:s'),
    							'note' => $getResult['message'],
    							);
    					Service_OrderLabelErr::add($row);
    				}	
    			
    			}else {
    				//e邮宝异常信息
    				$errorMessage = $result_temp["response"]["description"];
    
    				$callResult["error"] = $errorMessage;
    				$callResult["errorCode"] = "002";
    			}
    		}
    
    	} catch (Exception $e) {
    		$callResult["error"] = "同步失败，异常信息：".$e->getMessage();
    	}
    
    	return $callResult;
    }
    
    /**
     * 获取预报XML
     * @param unknown_type $orderCode
     * @throws Exception
     * @return string
     */
    public function getOrderContent($orderCode){
    
    	// 申报信息
    	$items ="<items>";
    	
    	// 申报单重
    	$unit_weight = round($this->orderKey['weight']/$this->orderKey['total_declare_num'], 2);
//     	print_r($this->orderKey); echo "---";
//     	print_r($unit_weight);die;
    	foreach($this->orderInvoiceItemKey as $k => $row){
    		$cnname = (!empty($row['titleCn']) ? $row['titleCn'] : $row['titleEn']);
    		$enname = (!empty($row['titleEn']) ? $row['titleEn'] : $row['titleCn']);
    		
    		
    		// 最长64
    		$cnname = mb_substr($cnname, 0, 60, "UTF-8");
    		$enname = mb_substr($enname, 0, 60, "UTF-8");
    		
	    	$items.="<item>".
	    			"<cnname><![CDATA[ ".$cnname." ]]></cnname>".
	    			"<enname><![CDATA[ ".$enname." ]]></enname>".
	    			"<count>".$row['quantity']."</count>".
	    			"<weight>".round($unit_weight * $row['quantity'], 2)."</weight>".
	    			"<delcarevalue>".$row['value']."</delcarevalue>".
	    			"<origin>CN</origin>".
	    			"</item>";
    	}
    	$items .="</items>";
    	
//     	print_r($items);die;
    
    	$xmlData = "<?xml version='1.0'?>".
    			"<orders xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>".
    			'<order>'.
    			'<orderid>'.$orderCode.'</orderid>'.
    			'<operationtype>0</operationtype>'.
    			'<producttype>0</producttype>'.
    			'<customercode>'.$this->_customer.'</customercode>'.
    			'<vipcode></vipcode>'.
    			'<clcttype>0</clcttype>'.
    			'<pod>false</pod>'.
    			'<untread>Returned</untread>'.
    			'<volweight>0</volweight>'.
    			'<startdate>'.date("Y-m-d")."T".date("H:i:s").'</startdate>'.
    			'<enddate>'.date("Y-m-d",strtotime("+2 day")).'T'.date("H:i:s").'</enddate>'.
    			'<printcode>01</printcode>'.
    			'<sender>'.
    			'<name>'.$this->shipperKey["shipperName"].'</name>'.
    			'<postcode>'.$this->shipperKey["shipperPostCode"].'</postcode>'.
    			'<phone>'.$this->shipperKey["shipperPhone"].'</phone>'.
    			'<mobile>'.$this->shipperKey["shipperMobile"].'</mobile>'.
    			'<country>'.$this->shipperKey["shipperCountryCode"].'</country>'.
    			'<province>'.$this->shipperKey["shipperStateOrProvince"].'</province>'.
    			'<city>'.$this->shipperKey["shipperCity"].'</city>'.
    			'<county>'.$this->shipperKey["shipperDistrict"].'</county>'.
    			'<company>'.$this->shipperKey["shipperCompanyName"].'</company>'.
    			'<street>'.$this->shipperKey["shipperStreet"].'</street>'.
    			'<email>'.$this->shipperKey["shipperEmail"].'</email>'.
    			'</sender>'.
    			'<receiver>'.
    			'<name><![CDATA[ '.$this->orderKey['consigneeName'].' ]]></name>'.
    			'<postcode>'.$this->orderKey['consigneePostalCode'].'</postcode>'.
    			'<phone>'.$this->orderKey['consigneePhone'].'</phone>'.
    			'<mobile>'.$this->orderKey['consigneePhone'].'</mobile>'.
    			'<country>'.$this->orderKey['consigneeCountryCode'].'</country>'.
    			'<province><![CDATA[ '.$this->orderKey['consigneeStateOrProvince'].' ]]></province>'.
    			'<city><![CDATA[ '.$this->orderKey['consigneeCity'].' ]]></city>'.
    			'<county><![CDATA[ '.$this->orderKey['consigneeDistrict'].' ]]></county>'.
    			'<street><![CDATA[ '.$this->orderKey['consigneeStreet'].' ]]></street>'.
    			'</receiver>'.
    			'<collect>'.
    			'<name>'.$this->shipperKey["shipperName"].'</name>'.
    			'<postcode>'.$this->shipperKey["shipperPostCode"].'</postcode>'.
    			'<phone>'.$this->shipperKey["shipperPhone"].'</phone>'.
    			'<mobile>'.$this->shipperKey["shipperPhone"].'</mobile>'.
    			'<country>'.$this->shipperKey["shipperCountryCode"].'</country>'.
    			'<province>'.$this->shipperKey["shipperStateOrProvince"].'</province>'.
    			'<city>'.$this->shipperKey["shipperCity"].'</city>'.
    			'<county>'.$this->shipperKey["shipperDistrict"].'</county>'.
    			'<company>'.$this->shipperKey["shipperCompanyName"].'</company>'.
    			'<street>'.$this->shipperKey["shipperStreet"].'</street>'.
    			'<email>'.$this->shipperKey["shipperEmail"].'</email>'.
    			'</collect>'.
    			$items.
    			'<remark/>'.
    			'</order>'.
    			'</orders>';
    	return $xmlData;
    }
    
}