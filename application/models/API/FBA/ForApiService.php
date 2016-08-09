<?php

class API_FBA_ForApiService 
{
	
    public function __construct()
    {
    	// 创建日志目录
    	if (! is_dir ( APPLICATION_PATH . '/../data/log/FBA_API' )) {
    		mkdir(APPLICATION_PATH . '/../data/log/FBA_API', 0777);
    		chmod(APPLICATION_PATH . '/../data/log/FBA_API', 0777);
    	}
    }

    public function setParam($orderid)
    {
    	
    	//订单信息
    	$order = Service_CsdOrderfba::getByField($orderid);
    	$this->orderData = $order;
    	
    	$address = Service_CsdShipperconsigneefba::getByField($orderid, "order_id", "*");
   		$this->shipperKey["shipperCompanyName"] = $address["shipper_company"];
   		$this->shipperKey["shipperName"] = $address["shipper_name"];
   		$this->shipperKey["shipperCountryCode"] = $address["shipper_countrycode"];
   		$this->shipperKey["shipperStateOrProvince"] = $address["shipper_province"];
   		$this->shipperKey["shipperStreet"] = $address["shipper_street"];
   		$this->shipperKey["shipperCity"] = $address["shipper_city"];
   		$this->shipperKey["shipperPostCode"] = $address["shipper_postcode"];
   		$this->shipperKey["shipperPhone"] = empty($address["shipper_mobile"]) ? $address["shipper_telephone"] : $address["shipper_mobile"];
    	$this->shipperKey["shipperHouseNo"] = empty($address["shipper_doorplate"]) ? $address["shipper_doorplate"] : '';
    	$this->shipperKey["shipperAddress1"] = $address["shipper_city"];
    	$this->shipperKey["shipperAddress2"] = $address["shipper_province"];
    	$this->shipperKey["shipperEmail"] = $address["shipper_email"];
    	$this->shipperKey["shipperDistrict"] = $address["shipper_district"];
    	$this->shipperKey["shipperMobile"] = $address["shipper_mobile"];
    	//收件人信息
    	$this->orderKey["consigneeName"] = $address["storage"];
    	$this->orderKey["consigneeStateOrProvince"] = $address["consignee_province"];
    	$this->orderKey["consigneeCity"] = $address["consignee_city"];
    	$this->orderKey["consigneeStreet"] = $address["consignee_street"];
    	$this->orderKey["consigneePostalCode"] = $address["consignee_postcode"];
    	$this->orderKey["consigneeCountry"] = $address["consignee_countrycode"];
    	//订单信息
    	$this->orderKey["goodsName"] = "";
    	$this->orderKey["type"] = "";
    	$this->orderKey["quantity"] = $this->orderData['boxnum'];
    	$this->orderKey["productCode"] = $this->orderData['product_code'];;
    	$this->orderKey["orderSequence"] = $this->orderData["order_id"]; //订单序列号 order_id
    	$this->orderKey["orderNo"] = $this->orderData["shipper_hawbcode"]; //WMS订单号 order_code
    	$this->orderCode = $this->orderKey["orderNo"];
    	$this->orderKey["referenceID"] = ''; //WMS订单号 order_code
    	$this->orderKey["refer_hawbcode"] = empty($this->orderData["refer_hawbcode"])?"":$this->orderData["refer_hawbcode"]; //WMS订单号 order_code
    	
    	//服务信息
    	$this->orderKey["shippingMethod"] = $this->orderData["product_code"]; //订单运输方式
    	
    	
    	$this->orderKey["serviceCurrency"] = 'USD'; //服务币种
    	
    	
    	//订单产品信息
    	$invoice = Service_CsdInvoicefba::getByCondition(array("order_id" => $this->orderData["order_id"]), "*", 0, 1, "");
    	// 总申报数量
    	//订单的收件人
    	$total_declare_num = 0;
    	//var_dump($invoice);
    	$this->orderInvoiceItemKey = array();
    	foreach ($invoice as $key => $val) {
    		if(isset($this->orderInvoiceItemKey[$val["bagid"]])){
    			$this->orderInvoiceItemKey[$val["bagid"]]["infos"][]=array(
    					"goodname" => $val['goodname'],
    					"itemno" => $val["itemno"],
    					"quantity"=> $val["quantity"],	
    			);
    		}else{
	    		$this->orderInvoiceItemKey[$val["bagid"]] = array(
	    			"infos" =>array(
	    				array(
	    				   "goodname" => $val['goodname'],		
	    				   "itemno" => $val["itemno"],
	    				   "quantity"=> $val["quantity"], 		 		
	    				),		
	    			),	
	    			"length"=>$val['length'],
	    			"width" =>$val['width'],
	    			"height"=>$val['height'],
	    			"weight"=>$val['weight'],	
	    		);
    		}
    	}
    	
    	
    }   

    public function test(){
    	print_r($this->shipperKey);
    	print_r($this->orderKey);
    	print_r($this->orderInvoiceItemKey);
    }
    public function excuteService($url,$params,$method){
    	$result = array("ack"=>0,"message"=>"","data"=>"");
    	try {
    		$tuCurl = curl_init();
			curl_setopt($tuCurl, CURLOPT_URL, $url);
			curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
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
				//$error = '发送CURL时发生错误:'.curl_error($tuCurl).'(code:'.curl_errno($curl).')'.PHP_EOL;
				throw new Exception ('通知标签服务器失败！！！');
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
          
    //通知中邮接口
    public function  notifyOrderToService(){
      $callResult = array("ack"=>0,"orderCode"=>$this->orderCode,"error"=>"","errorCode"=>"");
 	  try {
 	  	$error = array();
 	  	for($i=1;$i<=$this->orderKey['quantity'];$i++){
 	  		$res = $this->excuteOrderToNotify($i);
 	  		$res = Common_Common::xml_to_array($res);
 	  		if($res['status']=='success'){
 	  			
 	  		}else{
 	  			$error[]=array("errorCode"=>$res['code'],"boxnum"=>$i,"errormsg"=>$res['description']);
 	  		}
 	  	}
 	  	if(empty($error)){
 	 		$callResult["ack"] = 1;
 	 	}else{
 	 		$callResult["ack"] = -1;
 	 		$callResult["error"] = $error;
 	 	}
 	 	
 	 	
 	} catch (Exception $e) {
 	    $callResult["error"] = "同步未知异常，订单号：".$this->orderCode."异常信息：".$e->getMessage();
 	}
 		return $callResult;
    }
    
    
    
    
    public function excuteOrderToNotify($boxnum){
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
      	$receiver['phone'] = "";
      	$receiver['mobile'] = "";
      	$receiver['country'] =  $this->orderKey['consigneeCountry'];
      	$receiver['prov'] = $this->orderKey["consigneeStateOrProvince"];
      	$receiver['city'] = $this->orderKey['consigneeCity'];
      	$receiver['company'] = "";
      	$receiver['street'] =  $this->orderKey["consigneeStreet"];
      	
      	//商品信息
      	$items = array();
      	//内件品名
      	$itemnames = '';
      	$_ordernum = 0;
      	foreach($this->orderInvoiceItemKey[$boxnum]["infos"] as $oKey=>$row){
      		$item = array();
      		$item['cnname'] = $row["goodname"];
      		$item['count'] = $row["quantity"];
      		$item['weight'] = 0;
      		$item['currency'] = "USD";
      		$item['cost'] = 0;
      		$item['intemcom'] = 'Harmless ingredients';
      		$item['origin'] = "CN";
      		$item['trade'] =1;
      		$item['enname'] = $row["itemno"];
      		$item['HS'] = "";
      		$item['intemsize'] = '';
      		$item['sellurl'] = '';
      		$items["item_".$oKey]=$item;
      	}
      	//第3级
      	$array_three=array();
      	$array_three['pretype'] = 60;
      	if($boxnum==1){
      		$mailnum = $this->orderKey['orderNo'];
      	}else{
      		$_orderCode  = substr($this->orderKey['orderNo'],2,-3);
      		$_orderCode  = intval($_orderCode)-$boxnum+1;
      		$mailnum = 'AS'.change_no($_orderCode).'CN';
      	}
      	$array_three['mailnum'] = $mailnum;
      	$array_three['rcvarea'] = 5;
      	$array_three['prptycode'] = $this->orderKey['type']==3?1:3;
      	switch ($this->orderKey["productCode"]){
      		case 'FBA1':$array_three['prodcode'] = 5320402291;break;
      		case 'FBA2':$array_three['prodcode'] = 5320402391;break;
      		default:$array_three['prodcode'] = 5320402491;break;	
      	}
      	
      	
      	$array_three['clctname'] = '';
      	$array_three['clctcode'] = '';
      	$array_three['actualweight'] = $this->orderInvoiceItemKey[$boxnum]["weight"]*1000;
      	$array_three['length'] = intval($this->orderInvoiceItemKey[$boxnum]["length"]*10)/10;
      	$array_three['width'] = intval($this->orderInvoiceItemKey[$boxnum]["width"]*10)/10;
      	$array_three['height'] = intval($this->orderInvoiceItemKey[$boxnum]["height"]*10)/10;
      	$array_three['volweight'] =  $array_three['actualweight'];
      	$array_three['billingweight'] = $array_three['actualweight'];
      	$array_three['bjmoney'] = 0;
      	
      	//$array_three['bxmoney'] = intval($this->orderKey["insurance_value_gj"]*100)/100;
      	$array_three['bxmoney'] = 0;
      	$array_three['loanmoney'] = 0;
      	$array_three['minordernum'] = $this->orderKey['quantity'];
      	$array_three['mpostalnum'] = $this->orderKey['orderNo'];
      	$array_three['ordernum'] = $this->orderKey['orderNo'];
      	$array_three['forecastshut'] = 0;
      	$array_three['internals'] = 1;
      	$array_three['portoffice'] = '';
      	$array_three['sendcountry'] = 'CN';
      	$array_three['mainminorder'] = $boxnum==1?2:3;
      	$array_three['mainbilling'] = 1;
      	$array_three['transport'] = 2;
      	$array_three['ordersources'] = '';
      	$array_three['identityplate'] = '';
      	$array_three['itemnames'] = "";      	
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
      	//echo $xml;return false;  
      	$url="http://shipping2.ems.com.cn/partner/api/public/p/orderSpecial";
        $data = $xml;
        $header = array("authenticate:pdfTest_dhfjh98983948jdf78475fj65375fjdhfj","version:international_eub_us_1.1");
        $result = $this->curl_send($url,$data,$header,"post");
        Ec::showError("**************start*************\r\n"
            . print_r($data, true)
            . "\r\n" . print_r($result, true)
            . "**************end*************\r\n",
            'FBA_API/Notify_ems_response_info'.date("Ymd"));
        return $result;
    }
    
    //中邮收寄 获取省和市是区域代码
    public function getQycode($positionename){
    	$return_arr = array();
    	do{
    		try{
    			//如果出现xxx,xxx的取第一个
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
 	
 	
 	
}