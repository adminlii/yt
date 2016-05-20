<?php 
class Pickup_TrackController extends Ec_Controller_Action {
	private $_wsdl = '';
	public function preDispatch() {
		$this->tplDirectory = "pickup/views/track/";
		$wsdl = 'http://edi.zjs.com.cn/test4/receive.asmx?wsdl';
		$this->_wsdl = $wsdl;
	}
	public function listAction() {
	}
	public function testAction() {
	}
	
	public function receiveDemoAction() {
		$return = array (
				'ask' => 'Failure',
				'message' => '' 
		);
		try {
			
			$zjs = new Common_ZjsEdi ();
			$rdm1 = Common_Common::random(4); // 随机数
			$rdm2 = Common_Common::random(4);; // 随机数
			
			$clientFlag = "TestClient"; // 客户标识
			$strSeed = "17AF4124-BC69-4A5F-A439-1505E30DF24B"; // 客户密钥
			$strConst = "0123456789abc"; // 常量值

			$config = Service_Config::getByField ( 'TMS_EDI_ZJS_RECEIVE_AUTH', 'config_attribute' );
			if ($config) {
				$val =  $config['config_value'];
				$arr = preg_split('/\*#\*/', $val);
				if(count($arr)!=3){
					throw new Exception('系统内部错误');
				}
				$clientFlag = $arr[0]; // 客户标识
				$strSeed = $arr[1]; // 客户密钥
				$strConst = $arr[2]; // 常量值
				
			} 
			$itemStock = '';
			$items = array (
					array (
							'itemName' => 'Nokia N73',
							'itemNumber' => '2',
							'itemValue' => '2000.50',
							'itemWeight' => '5.00',
							'itemVolume' => '10*5*3' 
					),
					array (
							'itemName' => 'Nokia N71',
							'itemNumber' => '2',
							'itemValue' => '2000.50',
							'itemWeight' => '5.00',
							'itemVolume' => '10*5*3' 
					),
			);
			foreach($items as $item){				
				$itemStock.= preg_replace('/<\?.+\?>/', '', $zjs->getXmlContent($item,'item'));
			}
			$orderParam = array(
					'logisticProviderID' => $clientFlag,
					'orderNo' => '96534',
// 					'subOrderNo' => '',
// 					'tradeNo' => '',
					'mailNo' => '22',
					'type' => '1',
					'flag' => '0',
					'sender' => array (
							'name' => '张三',
							'postCode' => '310013',
							'phone' => '231234134',
							'mobile' => '13575745195',
							'prov' => '北京市',
							'city' => '北京市',
							'district' => '朝阳区',
							'address' => '详细地址' 
					),
					'receiver' => array (
							'name' => '张三',
							'postCode' => '310013',
							'phone' => '231234134',
							'mobile' => '13575745195',
							'prov' => '北京市',
							'city' => '北京市',
							'district' => '朝阳区',
							'address' => '详细地址' 
					),
					
					'sendStartTime' => '2008-08-24 08:00:00',
					'sendEndTime' => '2008-08-24 17:00:00',
					'codAmount' => '2000',
					'items' => 'ITEM_STOCK',//占位
					
					'itemsName' => '衣服*2|鞋子*3',
					'itemsNumber' => '5',
					'itemsWeight' => '8.20',
					'itemsVolume' => '2*3*10*2,4*9*7*3',
					'itemsValue' => '2000',
					'insuranceValue' => '0.0',
// 					'loadRequire' => '',
					'remark' => '易碎品',
					'dataFlag' => '数据标识',
			);
			$xml = $zjs->getXmlContent($orderParam,'RequestOrder');
			$xml= preg_replace('/<\?.+\?>/', '', $xml);
			$xml = str_replace('ITEM_STOCK', $itemStock, $xml);
//        		header('Content-type: text/xml');
// 			echo $xml;exit;
// 			$xml = "<zjs>001</zjs>"; // 请求报文
			$params = array (
					'rdm1' => $rdm1,
					'rdm2' => $rdm2,
					'clientFlag' => $clientFlag,
					'xml' => $xml,
					'strSeed' => $strSeed,
					'strConst' => $strConst,
					'rdm1' => $rdm1 
			);
			$verifyData = $zjs->getVerifyData ( $params );
			
			try {
				// $clientFlag = '';
				// $xml = '';
				// $verifyData = '';
				$req = array (
						'clientFlag' => $clientFlag,
						'xml' => $xml,
						'verifyData' => $verifyData 
				);

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
				$wsdl = Service_Config::getByField ( 'TMS_EDI_ZJS_RECEIVE_WSDL', 'config_attribute' );
				if (! $wsdl) {
					$wsdl = 'http://edi.zjs.com.cn/test4/receive.asmx?wsdl';
					// throw new Exception('TMS_FEE_TRAIL_WSDL not configration');
				} else {
					$wsdl = $wsdl ['config_value'];
				}
					
				// 			$client = new Common_ZjsEdiSoap ( $wsdl, $options );
				$client = new SoapClient ( $wsdl, $options );
					
				
				$rs = $client->OrderXML ( $req );
				$rs = Common_Common::objectToArray ( $rs );
				if(!preg_match('/^<\?xml version="1\.0" encoding="utf\-8" \?>/', $rs ['OrderXMLResult'])){
					throw new Exception($rs ['OrderXMLResult']);
				}
				$data = XML_unserialize ( $rs ['OrderXMLResult'] );
				$data = $data['Response'];
				// $rs = $client->OrderXML ( $clientFlag,$xml,$verifyData);
				print_r ( $data );
				exit ();
			} catch ( Exception $e ) {
				throw new Exception ( "调用失败,原因:".$e->getMessage() );
			}
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		print_r ( $return );
		exit ();
		return $return;
	}

	public function cancelAction() {
		$return = array (
				'ask' => 'Failure',
				'message' => ''
		);
		try {
				
			$zjs = new Common_ZjsEdi ();
			$rdm1 = Common_Common::random(4); // 随机数
			$rdm2 = Common_Common::random(4);; // 随机数
			$clientFlag = "TestClient"; // 客户标识
			$strSeed = "17AF4124-BC69-4A5F-A439-1505E30DF24B"; // 客户密钥
			$strConst = "0123456789abc"; // 常量值
				 
			$orderParam = array(
					'logisticProviderID' => $clientFlag,
					'orderNo' => '96534', 
					'infoType' => 'INSTRUCTION',
					'infoContent' => 'WITHDRAW',					 
					'remark' => '截单',
			);
			$xml = $zjs->getXmlContent($orderParam,'UpdateInfo');
			 
// 			       		header('Content-type: text/xml');
// 						echo $xml;exit;
			// 			$xml = "<zjs>001</zjs>"; // 请求报文
			$params = array (
					'rdm1' => $rdm1,
					'rdm2' => $rdm2,
					'clientFlag' => $clientFlag,
					'xml' => $xml,
					'strSeed' => $strSeed,
					'strConst' => $strConst,
					'rdm1' => $rdm1
			);
			$verifyData = $zjs->getVerifyData ( $params );
				
			try {
				// $clientFlag = '';
				// $xml = '';
				// $verifyData = '';
				$req = array (
						'clientFlag' => $clientFlag,
						'xml' => $xml,
						'verifyData' => $verifyData
				);
	
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
				$wsdl = Service_Config::getByField ( 'TMS_EDI_ZJS_RECEIVE_WSDL', 'config_attribute' );
				if (! $wsdl) {
					$wsdl = 'http://edi.zjs.com.cn/test4/receive.asmx?wsdl';
					// throw new Exception('TMS_FEE_TRAIL_WSDL not configration');
				} else {
					$wsdl = $wsdl ['config_value'];
				}
					
				// 			$client = new Common_ZjsEdiSoap ( $wsdl, $options );
				$client = new SoapClient ( $wsdl, $options );
					
	
				$rs = $client->OrderXML ( $req );
				$rs = Common_Common::objectToArray ( $rs );
				if(!preg_match('/^<\?.+\?>/', $rs ['OrderXMLResult'])){
					throw new Exception($rs ['OrderXMLResult']);
				}
				$data = XML_unserialize ( $rs ['OrderXMLResult'] );
				$data = $data['Response'];
				// $rs = $client->OrderXML ( $clientFlag,$xml,$verifyData);
				print_r ( $data );
				exit ();
			} catch ( Exception $e ) {
				throw new Exception ( "调用失败,原因:".$e->getMessage() );
			}
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		print_r ( $return );
		exit ();
		return $return;
	}

	public function getAction() {
		$return = array (
				'ask' => 'Failure',
				'message' => '' 
		);
		try {
			
			$zjs = new Common_ZjsEdi ();
			$rdm1 = Common_Common::random(4); // 随机数
			$rdm2 = Common_Common::random(4);; // 随机数
			
			$clientFlag = "ZJS_GuoJi"; // 客户标识 
			$strSeed = "CEC74FED-9516-4F39-AFB1-CC5D7B3BD08E"; // 客户密钥
			$strConst = "z宅J急S送g"; //
 
						
			
			$orderStock = '';
			$orders = array (
					array (
							'mailNo' => '3847109081',
							'orderNo' => '96534', 
					),
			);
			foreach($orders as $order){				
				$orderStock.= preg_replace('/<\?.+\?>/', '', $zjs->getXmlContent($order,'order'));
			}
			$orderParam = array(
					'logisticProviderID' => $clientFlag,
					 
					'orders' => 'ORDER_STOCK',//占位
					 
			);
			$xml = $zjs->getXmlContent($orderParam,'BatchQueryRequest');
			$xml= preg_replace('/<\?.+\?>/', '', $xml);
			$xml = str_replace('ORDER_STOCK', $orderStock, $xml);
//        		header('Content-type: text/xml');
// 			echo $xml;exit;
// 			$xml = "<zjs>001</zjs>"; // 请求报文
			$params = array (
					'rdm1' => $rdm1,
					'rdm2' => $rdm2,
					'clientFlag' => $clientFlag,
					'xml' => $xml,
					'strSeed' => $strSeed,
					'strConst' => $strConst,
					'rdm1' => $rdm1 
			);
			$verifyData = $zjs->getVerifyData ( $params );
			
			try {
				// $clientFlag = '';
				// $xml = '';
				// $verifyData = '';
				$req = array (
						'clientFlag' => $clientFlag,
						'xml' => $xml,
						'verifyData' => $verifyData 
				);

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
				$wsdl = Service_Config::getByField ( 'TMS_EDI_ZJS_TRACK_WSDL', 'config_attribute' );
				if (! $wsdl) {
					$wsdl = 'http://edi.zjs.com.cn/svst/tracking.asmx?wsdl';
					// throw new Exception('TMS_FEE_TRAIL_WSDL not configration');
				} else {
					$wsdl = $wsdl ['config_value'];
				}
					
				// 			$client = new Common_ZjsEdiSoap ( $wsdl, $options );
				$client = new SoapClient ( $wsdl, $options );
					
				
				$rs = $client->Get ( $req );
// 				print_r($rs);exit;
				$rs = Common_Common::objectToArray ( $rs );
				if(!preg_match('/^<\?xml.+\?>/', $rs ['GetResult'])){
					throw new Exception($rs ['GetResult']);
				}
				$data = XML_unserialize ( $rs ['GetResult'] );
				$data = $data['BatchQueryResponse'];
				// $rs = $client->OrderXML ( $clientFlag,$xml,$verifyData);
				print_r ( $data );
				exit ();
			} catch ( Exception $e ) {
				throw new Exception ( "调用失败,原因:".$e->getMessage() );
			}
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		print_r ( $return );
		exit ();
		return $return;
	}

	public function cargoTrackingAction() {
		$timeout = 1000;
		try {
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
			$clientFlag = 'OMSClient';
			$json = '{"ReferenceNumber": "RM005085246CN"}';
			$verifyData = '7xSkf681caa43847cf8eaca80d45pjSY';
			$wsdl = 'http://www.toms.com/default/svc/wsdl';
			$client = new SoapClient ( $wsdl, $options );
			$req = array (
					'clientFlag' => $clientFlag,
					'json' => $json,
					'verifyData' => $verifyData 
			);
			$result = $client->cargoTrackingService ( $req );
			// echo __LINE__;exit;
			echo ($client->__getLastResponse ());
			exit ();
			// $client = new Common_Svc();
			// $result = $client->cargoTrackingService($clientFlag,$json,$verifyData);
			print_r ( $result );
			exit ();
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	

	public function cargoTracking1Action() {		
        $timeout = 1000;
        try {
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
        	$clientFlag = 'OMSClient';
        	$json = '{"ReferenceNumber": "RM005085246CN"}';
        	$verifyData = '7xSkf681caa43847cf8eaca80d45pjSY';
        	$wsdl = 'http://www.toms.com/default/svc/wsdl';
        	$client = new SoapClient ( $wsdl, $options );
        	$req = array('clientFlag'=>$clientFlag,'json'=>$json,'verifyData'=>$verifyData);
        	$result = $client->cargoTrackingService($req);
        	print_r($result);exit;
        	print_r(json_decode($result->response,true));exit;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        
	}
	
	public function trackingAction() {
		$service = 'http://edi.zjs.com.cn/svst/tracking.asmx';
		$zjs = new Common_ZjsEdi ();
		$rdm1 = "01ab"; // 随机数
		$rdm2 = "02zy"; // 随机数
		$clientFlag = "ZJS_GuoJi"; // 客户标识
		$xml = "<zjs>001</zjs>"; // 请求报文
		$strSeed = "CEC74FED-9516-4F39-AFB1-CC5D7B3BD08E"; // 客户密钥
		$strConst = "z宅J急S送g"; // 常量值
		$params = array (
				'rdm1' => $rdm1,
				'rdm2' => $rdm2,
				'clientFlag' => $clientFlag,
				'xml' => $xml,
				'strSeed' => $strSeed,
				'strConst' => $strConst,
				'rdm1' => $rdm1 
		);
		$strVerifyData = $zjs->getVerifyData ( $params );
		
		echo $strVerifyData;
		exit ();
	}
	public function demoAction() {
		$zjs = new Common_ZjsEdi ();
		$strVerifyData = $zjs->demo ();
		
		echo $strVerifyData;
		exit ();
	}
}