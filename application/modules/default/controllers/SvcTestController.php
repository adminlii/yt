<?php
class Default_SvcTestController extends Zend_Controller_Action {

	public function orderAction() {
			
			
		$consignee = array(
						'consignee_company' =>'dfdfdf',
						'consignee_province' =>'',
						'consignee_name' =>'sdfsdfds',
						'consignee_city' =>'',
						'consignee_telephone' =>'',
						'consignee_mobile' =>'',
						'consignee_postcode' =>'',
						'consignee_email' =>'',
						'consignee_street' =>'sdfsdfsdf',
						'consignee_certificatetype' =>'',
						'consignee_certificatecode' =>'',
						'consignee_credentials_period' =>'',
				);
		$shipper = array(
						// 'shipper_account' =>'',
						'shipper_name' =>'',
						'shipper_company' =>'',
						'shipper_countrycode' =>'',
						'shipper_province' =>'',
						'shipper_city' =>'',
						'shipper_street' =>'',
						'shipper_postcode' =>'',
						'shipper_areacode' =>'',
						'shipper_telephone' =>'',
						'shipper_mobile' =>'',
						'shipper_email' =>'',
						'shipper_fax' =>'',
				);
		$itemArr = array();
		$itemArr[] = array(
            			'invoice_enname' => '21321',
            			'unit_code' => 'PCE',
            			'invoice_quantity' =>'5',
            			'invoice_unitcharge' => '5',
            			'invoice_currencycode' => 'USD',
            			'hs_code' => '',
            			'invoice_note' => '',
            			'invoice_url' => ''
            	);
		$params = array(
				'shipping_method' => 'DHLTH',
				'country_code' => 'DE',
// 				'reference_no'=>'ref-'.time(),
				'reference_no'=>'aaaa',
				'shipper_hawbcode'=>'',
				'shipping_method_no' => '',
				'order_weight' => '',
				'order_pieces' => '',
				'buyer_id' => '',
				'order_create_code' => 'w',
				'customer_id' => '',
				'creater_id' => Service_User::getUserId (),
				'modify_date' => date ( 'Y-m-d H:i:s' ),
				'mail_cargo_type' => '',
				'tms_id' => Service_User::getTmsId (),
				'customer_channelid' => Service_User::getChannelid (),
				
				'Consignee' => $consignee,
				'Shipper' => $shipper,
				'ItemArr' => $itemArr
		);
		$req = array(
				'service' => 'createOrder',
				'paramsJson' => Zend_Json::encode($params)
		);
		
		//print_r($req);die;
//         $req['appToken'] = '702411065d4bfb03f076112de0e4f052';
//         $req['appKey'] = '702411065d4bfb03f076112de0e4f05224e96e47d58bca841159737e36d71539';

		// YUN-LOCAL
        $req['appToken'] = 'dca093a88b3f9946197927495098219a';
        $req['appKey'] = 'dca093a88b3f9946197927495098219a4d4aa8925a302eb2989424d83156826f';
//         $req['appToken'] = '702411065d4bfb03f076112de0e4f052';
//         $req['appKey'] = '702411065d4bfb03f076112de0e4f05224e96e47d58bca841159737e36d71539';
// 		print_r($req);exit;
        // 超时
        $timeout = 1000;
        
        $streamContext = stream_context_create ( array (
        		'ssl' => array (
        				'verify_peer' => false,
        				'allow_self_signed' => true
        		),
        		// 'bindto' => $wmsConfig['3part']['BindTo'],
        		'socket' => array ()
        ) );
        
        
        $options = array (
        		"trace" => true,
        		"connection_timeout" => $timeout,
        		// "exceptions" => true,
        		// "soap_version" => SOAP_1_1,
        		// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
        		// "stream_context" => $streamContext,
        		"encoding" => "utf-8"
        );
        $wsdl = 'http://yuntoms.eccang.com/default/svc/wsdl';
        $client = new SoapClient ( $wsdl, $options );
//         $client = new Common_Svc();
        $result = $client->callService($req);
        header("Content-type: text/html; charset=utf-8");
        print_r($result);exit;
	}

	public function batchOrderAction() {
			
			
		$consignee = array(
						'consignee_company' =>'dfdfdf',
						'consignee_province' =>'',
						'consignee_name' =>'sdfsdfds',
						'consignee_city' =>'',
						'consignee_telephone' =>'',
						'consignee_mobile' =>'',
						'consignee_postcode' =>'',
						'consignee_email' =>'',
						'consignee_street' =>'sdfsdfsdf',
						'consignee_certificatetype' =>'',
						'consignee_certificatecode' =>'',
						'consignee_credentials_period' =>'',
				);
		$shipper = array(
						// 'shipper_account' =>'',
						'shipper_name' =>'',
						'shipper_company' =>'',
						'shipper_countrycode' =>'',
						'shipper_province' =>'',
						'shipper_city' =>'',
						'shipper_street' =>'',
						'shipper_postcode' =>'',
						'shipper_areacode' =>'',
						'shipper_telephone' =>'',
						'shipper_mobile' =>'',
						'shipper_email' =>'',
						'shipper_fax' =>'',
				);
		$itemArr = array();
		$itemArr[] = array(
            			'invoice_enname' => '21321',
            			'unit_code' => 'PCE',
            			'invoice_quantity' =>'5',
            			'invoice_unitcharge' => '5',
            			'invoice_currencycode' => 'USD',
            			'hs_code' => '',
            			'invoice_note' => '',
            			'invoice_url' => ''
            	);
		$params = array(
				'shipping_method' => 'DHLTH',
				'country_code' => 'DE',
				'reference_no'=>'ref-'.time(),
				'shipping_method_no' => '',
				'order_weight' => '',
				'order_pieces' => '',
				'buyer_id' => '',
				'order_create_code' => 'w',
				'customer_id' => '',
				'creater_id' => Service_User::getUserId (),
				'modify_date' => date ( 'Y-m-d H:i:s' ),
				'mail_cargo_type' => '',
				'tms_id' => Service_User::getTmsId (),
				'customer_channelid' => Service_User::getChannelid (),
				
				'Consignee' => $consignee,
				'Shipper' => $shipper,
				'ItemArr' => $itemArr
		);
		
		$orderArr = array($params, $params, $params);
		$req = array(
				'service' => 'batchCreateOrder',
				'paramsJson' => Zend_Json::encode($orderArr)
		);
        $req['appToken'] = 'dca093a88b3f9946197927495098219a';
        $req['appKey'] = 'dca093a88b3f9946197927495098219a4d4aa8925a302eb2989424d83156826f';
// 		print_r($req);exit;
        // 超时
        $timeout = 1000;
        
        $streamContext = stream_context_create ( array (
        		'ssl' => array (
        				'verify_peer' => false,
        				'allow_self_signed' => true
        		),
        		// 'bindto' => $wmsConfig['3part']['BindTo'],
        		'socket' => array ()
        ) );
        
        
        $options = array (
        		"trace" => true,
        		"connection_timeout" => $timeout,
        		// "exceptions" => true,
        		// "soap_version" => SOAP_1_1,
        		// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
        		// "stream_context" => $streamContext,
        		"encoding" => "utf-8"
        );
        $wsdl = 'http://yuntoms.eccang.com/default/svc/wsdl';
        $client = new SoapClient ( $wsdl, $options );
//         $client = new Common_Svc();
        $result = $client->callService($req);
        print_r($result);exit;
	}
	
	public function feeAction(){ 
		$params = array();
		$params['country_code'] = 'GB';
		$params['weight'] = '2';
		$params['length'] = '';
		$params['width'] = '';
		$params['height'] = '';
		$req = array(
				'service' => 'feeTrail',
				'paramsJson' => Zend_Json::encode($params)
		);
        $req['appToken'] = '549531ae08409a201e4263315eba4e44';
        $req['appKey'] = '549531ae08409a201e4263315eba4e4448c89aae81fc3c123528a76c8cc24d2a';
// 		print_r($req);exit;

        // 超时
        $timeout = 1000;
        
        $streamContext = stream_context_create ( array (
        		'ssl' => array (
        				'verify_peer' => false,
        				'allow_self_signed' => true
        		),
        		// 'bindto' => $wmsConfig['3part']['BindTo'],
        		'socket' => array ()
        ) );
        
        
        $options = array (
        		"trace" => true,
        		"connection_timeout" => $timeout,
        		// "exceptions" => true,
        		// "soap_version" => SOAP_1_1,
        		// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
        		// "stream_context" => $streamContext,
        		"encoding" => "utf-8"
        );
        $wsdl = 'http://www.toms.com/default/svc/wsdl';
        $client = new SoapClient ( $wsdl, $options );
//         $client = new Common_Svc();

        $result = $client->callService($req);
        print_r(json_decode($result->response,true));exit;
		
	}

	public function abAction(){
		$timeout = 1000;
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
		$wsdl = 'http://120.24.63.108:9001/APIServicesDelegate?wsdl';
		$client = new SoapClient($wsdl, $options);
		$tms_id = '1';
		$customer_id = '1';
		$weight = '2';
		$country_code = 'GB';
		$org_area = '';
		$length = '';
		$width = '';
		$height = '';
		$cargo_type = "";
			
		$arr = array (
				'strTms_id' => $tms_id,
				'strCustomer_id' => $customer_id,
				'strWeight' => $weight,
				'strCountry_code' => $country_code,
				'strOg_id_pickup' => $org_area,
				'strLength' => $length,
				'strWidth' => $width,
				'strHeight' => $height,
				'strCargo_type' => $cargo_type,
		);
		try {
			// 			$rs = $client->AttemptCalculate($tms_id,$customer_id,$weight,$country_code,$org_area,$length,$width,$height,$cargo_type);
			$rs = $client->AttemptCalculate($arr);
			$json = $rs->AttemptCalculateResult;
			$json = json_decode($json,true);
			print_r($json);
		} catch (Exception $e) {
			echo $e->getMessage().'__'.__LINE__;
		}
		// 		echo $client->__getLastRequestHeaders();exit;
		// 		echo 		$client->__getLastRequest();
	
	}
	
	public function apiTestAction(){
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<SOAP-ENV:Envelope xmlns:SOAP-ENV = \"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ns1=\"http://www.example.org/Ec/\">
					<SOAP-ENV:Body>
						<ns1:callService>
							<paramsJson>
							{
							  consignee: {
							    'buyer_id': '',
							    'consignee_certificatecode': '',
							    'consignee_certificatetype': '',
							    'consignee_city': 'Wollongong',
							    'consignee_company': '',
							    'consignee_credentials_period': '',
							    'consignee_email': '',
							    'consignee_mobile': '',
							    'consignee_name': 'cond11',
							    'consignee_postcode': '9999',
							    'consignee_province': 'NSW',
							    'consignee_street': 'addr1addr2addr3',
							    'consignee_telephone': '1234123'
							  },
							  'country_code': '',
							  'extra_service': '',
							  'insurance_value': 0,
							  'itemArr': [
							    {
							      'hs_code': '',
							      'invoice_cnname': 'qƷ',
							      'invoice_enname': 'gift',
							      'invoice_note': 'gift',
							      'invoice_quantity': 1,
							      'invoice_unitcharge': 2,
							      'invoice_url': '',
							      'unit_code': 'PCE'
							    }
							  ],
							  'mail_cargo_type': '4',
							  'order_pieces': 1,
							  'order_weight': 0.2,
							  'reference_no': '21341234AAA2',
							  'shipper': {
							    'shipper_areacode': '',
							    'shipper_city': '',
							    'shipper_company': '',
							    'shipper_countrycode': '',
							    'shipper_email': '',
							    'shipper_fax': '',
							    'shipper_mobile': '',
							    'shipper_name': '',
							    'shipper_postcode': '',
							    'shipper_province': '',
							    'shipper_street': '',
							    'shipper_telephone': ''
							  },
							  'shipping_method': '',
							  'shipping_method_no': ''
							}
							</paramsJson>
							<appToken>66020de445bacee2bb51a00f6ad4e9198f40810a2bec4b84300c057638025593</appToken>
							<appKey>66020de445bacee2bb51a00f6ad4e919</appKey>
							<service>createOrder</service>
						</ns1:callService>
					</SOAP-ENV:Body>
				</SOAP-ENV:Envelope>";
		
		$consignee = array(
				'consignee_company' =>'dfdfdf',
				'consignee_province' =>'dddd',
				'consignee_name' =>'sdfsdfds',
				'consignee_city' =>'ddd',
				'consignee_telephone' =>'ddd',
				'consignee_mobile' =>'dd',
				'consignee_postcode' =>'ddd',
				'consignee_email' =>'dddd',
				'consignee_street' =>'sdfsdfsdf',
				// 	 'consignee_certificatetype' =>'ID',
		// 	 'consignee_certificatecode' =>'ddd',
		// 	 'consignee_credentials_period' =>'dddd',
		);
		$shipper = array(
		// 'shipper_account' =>'',
				'shipper_name' =>'sss',
				'shipper_company' =>'ss',
				'shipper_countrycode' =>'ss',
				'shipper_province' =>'ss',
				'shipper_city' =>'ss',
				'shipper_street' =>'ss',
				'shipper_postcode' =>'ss',
				'shipper_areacode' =>'ss',
				'shipper_telephone' =>'ss',
				'shipper_mobile' =>'ss',
				'shipper_email' =>'ss',
				'shipper_fax' =>'ss',
		);
		$itemArr = array();
		$itemArr[] = array(
				'invoice_enname' => '21321',
				'invoice_cnname' => '21321',
				'unit_code' => 'PCE',
				'invoice_quantity' =>'5',
				'invoice_unitcharge' => '5',
				'invoice_currencycode' => 'USD',
				'hs_code' => '',
				'invoice_note' => '',
				'invoice_url' => ''
		);
		$params = array(
				'shipping_method' => 'ADTE',
				'country_code' => 'DE',
				'reference_no'=>'ref-'.time(),
				'shipping_method_no' => 'sdfsdfdsfsd',
				'order_weight' => '10',
				//'order_pieces' => '10',
				'buyer_id' => 'ddddd',
				'order_create_code' => 'w',
				'customer_id' => '',
				'creater_id' => Service_User::getUserId (),
				'modify_date' => date ( 'Y-m-d H:i:s' ),
				//'mail_cargo_type' => '1',
				'tms_id' => Service_User::getTmsId (),
				'customer_channelid' => Service_User::getChannelid (),
		
				'extra_service'=>'10;5Y',
				'insurance_value'=>'100',
				'Consignee' => $consignee,
				'Shipper' => $shipper,
				'ItemArr' => $itemArr
		);
		$req = array(
				'service' => 'createOrder',
				'paramsJson' => Zend_Json::encode($params)
		);
		$req['appToken'] = '66020de445bacee2bb51a00f6ad4e919';
		$req['appKey'] = '66020de445bacee2bb51a00f6ad4e9198f40810a2bec4b84300c057638025593';
		// 	 print_r($req);exit;
		// 超时
		$timeout = 1000;
		
		$streamContext = stream_context_create ( array (
				'ssl' => array (
						'verify_peer' => false,
						'allow_self_signed' => true
				),
				// 'bindto' => $wmsConfig['3part']['BindTo'],
				'socket' => array ()
		) );
		
		
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
		$wsdl = 'http://toms.eccang.com/default/svc/web-service';
		$client = new SoapClient ( $wsdl, $options );
		//         $client = new Common_Svc();
		$result = $client->callService($req);
		//         print_r($result);exit;
		print_r(json_decode($result->response,true));exit;
		
	}
	
	public function curlTestAction() {
		
		$url = "http://ectoms.eccang.com/default/svc/web-service";
		$param = array('codes' => array("LM946302395CN"));
		
		$paramJson=json_encode($param);
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<SOAP-ENV:Envelope xmlns:SOAP-ENV = \"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ns1=\"http://www.example.org/Ec/\">
		<SOAP-ENV:Body>
		<ns1:callService>
		<paramsJson>{$paramJson}</paramsJson>
		<appToken>dca093a88b3f9946197927495098219a</appToken>
		<appKey>dca093a88b3f9946197927495098219a4d4aa8925a302eb2989424d83156826f</appKey>
		<service>getCargoTrack</service>
		</ns1:callService>
		</SOAP-ENV:Body>
		</SOAP-ENV:Envelope>";
		
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Content-length: ".strlen($xml)));
		$tuData = curl_exec($tuCurl);
		
		curl_close($tuCurl);
     	print_r($tuData);			
	}

	public function getOrderAction() {
			
		$order_code = $this->_request->getParam("code", "");
		$params = array('reference_no' => $order_code);
		$req = array(
				'service' => 'getOrder',
				'paramsJson' => Zend_Json::encode($params)
		);
		
		$req['appToken'] = 'dca093a88b3f9946197927495098219a';
		$req['appKey'] = 'dca093a88b3f9946197927495098219a4d4aa8925a302eb2989424d83156826f';
		// 		print_r($req);exit;
		// 超时
		$timeout = 1000;
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
		
		header("Content-type: text/html; charset=utf-8");
		$wsdl = 'http://yuntoms.eccang.com/default/svc/wsdl';
		$client = new SoapClient ( $wsdl, $options );
		$result = $client->callService($req);
		print_r($result);exit;
	}
	

	public function modifyOrderAction() {
			
		$params = array(
				'order_code' => 'YT1529410000005200020',
				'weight' => '0.3'
		);
		
		$req = array(
				'service' => 'modifyOrderWeight',
				'paramsJson' => Zend_Json::encode(array($params))
		);
	
		$req['appToken'] = 'dca093a88b3f9946197927495098219a';
		$req['appKey'] = 'dca093a88b3f9946197927495098219a4d4aa8925a302eb2989424d83156826f';
		// 		print_r($req);exit;
		// 超时
		$timeout = 1000;
	
		$streamContext = stream_context_create ( array (
				'ssl' => array (
						'verify_peer' => false,
						'allow_self_signed' => true
				),
				// 'bindto' => $wmsConfig['3part']['BindTo'],
				'socket' => array ()
		) );
	
	
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
		$wsdl = 'http://yuntoms.eccang.com/default/svc/wsdl';
		$client = new SoapClient ( $wsdl, $options );
		//         $client = new Common_Svc();
		$result = $client->callService($req);
		print_r($result);exit;
	}
	

	public function getShippingMethodAction() {
			
		
		$req = array(
				'service' => 'getShippingMethod'
		);
	
		$req['appToken'] = '702411065d4bfb03f076112de0e4f052';
		$req['appKey'] = '702411065d4bfb03f076112de0e4f05224e96e47d58bca841159737e36d71539';
		// 		print_r($req);exit;
		// 超时
		$timeout = 1000;
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
	echo "---";
		header("Content-type: text/html; charset=utf-8");
		$wsdl = 'http://112.74.66.52:8080/default/svc/wsdl';
		$client = new SoapClient ( $wsdl, $options );
		$result = $client->callService($req);
		print_r($result);exit;
	}

	public function getEAction() {

		$params = array(
				'shipping_method' => 'PK0001',
				'country_code' => 'US'
		);
		
		$req = array(
				'service' => 'getExtraService',
				'paramsJson' => Zend_Json::encode($params)
		);
	
		$req['appToken'] = '702411065d4bfb03f076112de0e4f052';
		$req['appKey'] = '702411065d4bfb03f076112de0e4f05224e96e47d58bca841159737e36d71539';
	//			print_r($req);exit;
		// 超时
		$timeout = 1000;
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
		echo "---";
		header("Content-type: text/html; charset=utf-8");
		$wsdl = 'http://112.74.66.52:8080/default/svc/wsdl';
		$client = new SoapClient ( $wsdl, $options );
		$result = $client->callService($req);
		print_r($result);exit;
	}
	

	public function cancelOrderAction() {
			
		$params = array(
				'reference_no' => 'YT1529410000005200020',
		);
	
		$req = array(
				'service' => 'cancelOrder',
				'paramsJson' => Zend_Json::encode($params)
		);
	
		$req['appToken'] = 'dca093a88b3f9946197927495098219a';
		$req['appKey'] = 'dca093a88b3f9946197927495098219a4d4aa8925a302eb2989424d83156826f';
// 				print_r($req);exit;
		// 超时
		$timeout = 1000;
	
		$streamContext = stream_context_create ( array (
				'ssl' => array (
						'verify_peer' => false,
						'allow_self_signed' => true
				),
				// 'bindto' => $wmsConfig['3part']['BindTo'],
				'socket' => array ()
		) );
	
	
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
		$wsdl = 'http://yuntoms.eccang.com/default/svc/wsdl';
		$client = new SoapClient ( $wsdl, $options );
		//         $client = new Common_Svc();
		$result = $client->callService($req);
		header("Content-type: text/html; charset=utf-8");
		print_r($result);exit;
	}
	

	public function getLabelUrlAction() {
			
		$params = array(
				'reference_no' => 'YT15355100010200059',
				'lable_type' => '1',
		);
	
		$req = array(
				'service' => 'getLabelUrl',
				'paramsJson' => Zend_Json::encode($params)
		);
	
		$req['appToken'] = '702411065d4bfb03f076112de0e4f052';
		$req['appKey'] = '702411065d4bfb03f076112de0e4f05224e96e47d58bca841159737e36d71539';
// 				print_r($req);exit;
		// 超时
		$timeout = 1000;
	
		$streamContext = stream_context_create ( array (
				'ssl' => array (
						'verify_peer' => false,
						'allow_self_signed' => true
				),
				// 'bindto' => $wmsConfig['3part']['BindTo'],
				'socket' => array ()
		) );
	
	
		$options = array (
				"trace" => true,
				"connection_timeout" => $timeout,
				// "exceptions" => true,
				// "soap_version" => SOAP_1_1,
				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
				// "stream_context" => $streamContext,
				"encoding" => "utf-8"
		);
		$wsdl = 'http://yuntoms.eccang.com/default/svc/wsdl';
		$client = new SoapClient ( $wsdl, $options );
		//         $client = new Common_Svc();
		$result = $client->callService($req);
		header("Content-type: text/html; charset=utf-8");
		print_r($result);exit;
	}
	
}