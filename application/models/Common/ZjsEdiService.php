<?php
class Common_ZjsEdiService {
	private $_pickupOrder = array ();
	private $_clientFlag = '';
	private $_sender = array ();
	private $_receiver = array ();
	public function setClientFlag($clientFlag) {
		$this->_clientFlag = $clientFlag;
	}
	public function getOrder($pickup_id) { 
		
		$pickupOrder = Service_PickupOrder::getByField ( $pickup_id, 'pickup_order_id' );
// 		print_r($pickupOrder);exit;
		
		$pickup_og_id = $pickupOrder ['pickup_og_id'];
		$sql = "select * from pickup_organization where pickup_og_id='{$pickup_og_id}';";
		$pickup_organization = Common_Common::fetchRow ( $sql );
// 		print_r($pickup_organization);exit;
		$pickupOrder ['receiver'] = $pickup_organization;
		$this->_receiver = $pickup_organization;
		$sender = Service_UserAddress::getByField ( $pickupOrder ['address_id'], 'address_id' );
// 		print_r($sender);exit;
		$this->_sender = $sender;
		$pickupOrder ['sender'] = $sender;
		$this->_pickupOrder = $pickupOrder;
	}
	public function getReceiveXml() {
		$orderParam = array (
				'logisticProviderID' => $this->_clientFlag,
				'orderNo' => $this->_pickupOrder ['pickup_order_id'],
				// 'subOrderNo' => '',
				// 'tradeNo' => '',
				'mailNo' => $this->_pickupOrder ['track_number'],
				'type' => '1',
				'flag' => '0',
				'sender' => array (
						'name' => $this->_sender ['contact'],
						'postCode' => $this->_sender ['postal_code'],
						'phone' => $this->_sender ['phone'],
						'mobile' => '',
						'prov' => $this->_sender ['state'],
						'city' => $this->_sender ['city'],
						'district' => $this->_sender ['district'],
						'address' => str_replace('*#*', ' ', $this->_sender ['street']) 
				),
				'receiver' => array (
						'name' => $this->_receiver ['contact'],
						'postCode' => $this->_receiver ['postal_code'],
						'phone' => $this->_receiver ['phone'],
						'mobile' => '',
						'prov' => $this->_receiver ['state'],
						'city' => $this->_receiver ['city'],
						'district' => $this->_receiver ['district'],
						'address' => $this->_receiver ['street'] 
				),
				
				// 'sendStartTime' => '2008-08-24 08:00:00',
				// 'sendEndTime' => '2008-08-24 17:00:00',
				// 'codAmount' => '2000',
				
				'itemsName' => '宅急送国际物流货物*' . $this->_pickupOrder ['bags'],
				'itemsNumber' => $this->_pickupOrder ['pieces'],
				'itemsWeight' => $this->_pickupOrder ['weight'],
				// 'itemsVolume' => '2*3*10*2,4*9*7*3',
				// 'itemsValue' => '2000',
				'insuranceValue' => '0.0',
				// 'loadRequire' => '',
				'remark' => '' 
		// 'dataFlag' => '数据标识',
				);
		$zjs = new Common_ZjsEdi ();
		$xml = $zjs->getXmlContent ( $orderParam, 'RequestOrder' );
		$xml = preg_replace ( '/<\?.+\?>/', '', $xml );
		return $xml;
	}
	public function receive($pickup_id) {
		$return = array (
				'ask' => 0,
				'message' => '',
				'data' => array () 
		);
		try {
			$zjs = new Common_ZjsEdi ();
			$rdm1 = Common_Common::random ( 4 ); // 随机数
			$rdm2 = Common_Common::random ( 4 );
			; // 随机数
			
			$clientFlag = "TestClient"; // 客户标识
			$strSeed = "17AF4124-BC69-4A5F-A439-1505E30DF24B"; // 客户密钥
			$strConst = "0123456789abc"; // 常量值
			
			$config = Service_Config::getByField ( 'TMS_EDI_ZJS_RECEIVE_AUTH', 'config_attribute' );
			if ($config) {
				$val = $config ['config_value'];
				$arr = preg_split ( '/\*#\*/', $val );
				if (count ( $arr ) != 3) {
					throw new Exception ( '系统内部错误' );
				}
				$clientFlag = $arr [0]; // 客户标识
				$strSeed = $arr [1]; // 客户密钥
				$strConst = $arr [2]; // 常量值
			}
			$this->_clientFlag = $clientFlag;
			
			$this->getOrder ( $pickup_id );
			$xml = $this->getReceiveXml ();
			
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
				// throw new Exception('TMS_EDI_ZJS_RECEIVE_WSDL not configration');
			} else {
				$wsdl = $wsdl ['config_value'];
			}
			
			// $client = new Common_ZjsEdiSoap ( $wsdl, $options );
			$client = new SoapClient ( $wsdl, $options );
			
			$rs = $client->OrderXML ( $req );
			Ec::showError(print_r($req,true)."\n".print_r($rs,true),'zjs_receive_');
			$rs = Common_Common::objectToArray ( $rs );
			if (! preg_match ( '/^<\?xml version="1\.0" encoding="utf\-8" \?>/', $rs ['OrderXMLResult'] )) {
				throw new Exception ( $rs ['OrderXMLResult'] );
			}
			$data = XML_unserialize ( $rs ['OrderXMLResult'] );
			$data = $data ['Response'];
			$return ['data'] = $data;
			// $rs = $client->OrderXML ( $clientFlag,$xml,$verifyData);
			if ($data ['success'] == 'true' || $data ['success'] === true) {
				$return ['ask'] = 1;
				$return ['message'] = $data ['reason'];
			} else {
				$return ['message'] = $data ['reason'];
			}
		} catch ( Exception $e ) {
			$return ['message'] = "调用失败,原因:" . $e->getMessage ();
		}
		return $return;
	}
	public function getCancelXml() {
		$orderParam = array (
				'logisticProviderID' => $this->_clientFlag,
				'orderNo' => $this->_pickupOrder ['pickup_order_id'],
				'infoType' => 'INSTRUCTION',
				'infoContent' => 'WITHDRAW',
				'remark' => '截单' 
		);
		$zjs = new Common_ZjsEdi ();
		$xml = $zjs->getXmlContent ( $orderParam, 'UpdateInfo' );
		$xml = preg_replace ( '/<\?.+\?>/', '', $xml );
		return $xml;
	}
	public function cancel($pickup_id) {
		$return = array (
				'ask' => 0,
				'message' => '',
				'data' => array () 
		);
		try {
			
			$zjs = new Common_ZjsEdi ();
			$rdm1 = Common_Common::random ( 4 ); // 随机数
			$rdm2 = Common_Common::random ( 4 );
			; // 随机数
			
			$clientFlag = "TestClient"; // 客户标识
			$strSeed = "17AF4124-BC69-4A5F-A439-1505E30DF24B"; // 客户密钥
			$strConst = "0123456789abc"; // 常量值
			
			$config = Service_Config::getByField ( 'TMS_EDI_ZJS_RECEIVE_AUTH', 'config_attribute' );
			if ($config) {
				$val = $config ['config_value'];
				$arr = preg_split ( '/\*#\*/', $val );
				if (count ( $arr ) != 3) {
					throw new Exception ( '系统内部错误' );
				}
				$clientFlag = $arr [0]; // 客户标识
				$strSeed = $arr [1]; // 客户密钥
				$strConst = $arr [2]; // 常量值
			}
			$this->_clientFlag = $clientFlag;
			
			$this->getOrder ( $pickup_id );
			$xml = $this->getCancelXml ();
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
				// throw new Exception('TMS_EDI_ZJS_RECEIVE_WSDL not configration');
			} else {
				$wsdl = $wsdl ['config_value'];
			}
			
			// $client = new Common_ZjsEdiSoap ( $wsdl, $options );
			$client = new SoapClient ( $wsdl, $options );
			
			$rs = $client->OrderXML ( $req );
			Ec::showError(print_r($req,true)."\n".print_r($rs,true),'zjs_cancel_');
			$rs = Common_Common::objectToArray ( $rs );
			if (! preg_match ( '/^<\?xml version="1\.0" encoding="utf\-8" \?>/', $rs ['OrderXMLResult'] )) {
				throw new Exception ( $rs ['OrderXMLResult'] );
			}
			$data = XML_unserialize ( $rs ['OrderXMLResult'] );
			$data = $data ['Response'];
			$return ['data'] = $data;
			// $rs = $client->OrderXML ( $clientFlag,$xml,$verifyData);
			if ($data ['success'] == 'true' || $data ['success'] === true) {
				$return ['ask'] = 1;
				$return ['message'] = $data ['reason'];
			} else {
				$return ['message'] = $data ['reason'];
			}
		} catch ( Exception $e ) {
			$return ['message'] = "调用失败,原因:" . $e->getMessage ();
		}
		return $return;
	}
	public function getQueryXml() {
		$zjs = new Common_ZjsEdi ();
		$orderStock = '';
		$orders = array ();
		$orders [] = array (
				'mailNo' => $this->_pickupOrder ['track_number'],
				'orderNo' => $this->_pickupOrder ['pickup_order_id'] 
		);
		foreach ( $orders as $order ) {
			$orderStock .= preg_replace ( '/<\?.+\?>/', '', $zjs->getXmlContent ( $order, 'order' ) );
		}
		$orderParam = array (
				'logisticProviderID' => $this->_clientFlag,
				'orders' => 'ORDER_STOCK'  // 占位
				);
		
		$xml = $zjs->getXmlContent ( $orderParam, 'BatchQueryRequest' );
		$xml = preg_replace ( '/<\?.+\?>/', '', $xml );
		$xml = str_replace ( 'ORDER_STOCK', $orderStock, $xml );
		
		return $xml;
	}
	public function query($pickup_id) {
		$return = array (
				'ask' => 0,
				'message' => '',
				'data' => array () 
		);
		try {
			
			$zjs = new Common_ZjsEdi ();
			$rdm1 = Common_Common::random ( 4 ); // 随机数
			$rdm2 = Common_Common::random ( 4 );  // 随机数
			
			$clientFlag = "ZJS_GuoJi"; // 客户标识
			$strSeed = "CEC74FED-9516-4F39-AFB1-CC5D7B3BD08E"; // 客户密钥
			$strConst = "z宅J急S送g"; //
			
			$config = Service_Config::getByField ( 'TMS_EDI_ZJS_TRACK_AUTH', 'config_attribute' ); 
			if ($config) {
				$val = $config ['config_value'];
				$arr = preg_split ( '/\*#\*/', $val );
				if (count ( $arr ) != 3) {
					throw new Exception ( '系统内部错误' );
				}
				$clientFlag = $arr [0]; // 客户标识
				$strSeed = $arr [1]; // 客户密钥
				$strConst = $arr [2]; // 常量值
			}
			$this->_clientFlag = $clientFlag; 
			$this->getOrder ( $pickup_id ); 
			$xml = $this->getQueryXml ();
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
				// throw new Exception('TMS_EDI_ZJS_TRACK_WSDL not configration');
			} else {
				$wsdl = $wsdl ['config_value'];
			}
			
			// $client = new Common_ZjsEdiSoap ( $wsdl, $options );
			$client = new SoapClient ( $wsdl, $options );
			
			$rs = $client->Get ( $req );
			Ec::showError(print_r($req,true)."\n".print_r($rs,true),'zjs_query_');
			$rs = Common_Common::objectToArray ( $rs );
			if (! preg_match ( '/^<\?.+\?>/', $rs ['GetResult'] )) {
				throw new Exception ( "调用失败,原因:" . $rs ['GetResult'] );
			}
			$data = XML_unserialize ( $rs ['GetResult'] );
			$data = $data ['BatchQueryResponse'];
			if(empty($data['orders'])||empty($data['orders']['order'])||empty($data['orders']['order']['steps'])||empty($data['orders']['order']['steps']['step'])){
				throw new Exception('暂无轨迹信息');
			}
			if(!empty($data['orders']['order']['steps']['step'])){
				if(isset($data['orders']['order']['steps']['step']['acceptTime'])){
					$step = $data['orders']['order']['steps']['step'];
					unset($data['orders']['order']['steps']['step']);
					$data['orders']['order']['steps']['step'][0] = $step;					
				}
			}
			$return['steps'] = array_reverse($data['orders']['order']['steps']['step']);
			$return ['data'] = $data;
			$return ['ask'] = 1;
			$return ['message'] = $data ['reason'];
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
}