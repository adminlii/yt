<?php
class API_SGPPS_ForApiService extends Common_APIChannelDataBatchSet {
	// token信息
	protected $_user = "";
	protected $_orderOnline = "";
	public function __construct() {
		// 创建日志目录
		if (! is_dir ( APPLICATION_PATH . '/../data/log/SGDGM_API' )) {
			mkdir ( APPLICATION_PATH . '/../data/log/SGDGM_API', 0777 );
			chmod ( APPLICATION_PATH . '/../data/log/SGDGM_API', 0777 );
		}
	}
	public function setParam($serviceCode = '', $code = '', $channelId = '', $serverProductCode = '', $init = true) {
		parent::__construct ( $serviceCode, $code, $channelId, $serverProductCode, $init );
		
		$this->_user = isset ( $this->accountData ["as_user"] ) ? $this->accountData ["as_user"] : '';
		$this->_orderOnline = isset ( $this->accountData ["as_address"] ) ? $this->accountData ["as_address"] : '';
	}
	public function getData() {
		return $this->_paramsSet ();
	}
	
	public function createAndPreAlertOrderServiceByAllCode() {

		// 所有创建订单业务方法统一返回格式：调用状态、跟踪号、EC订单号、错误信息、错误代码
		// errorCode 001 系统异常、内部消化 ,002 业务异常，需要操作员处理
		$callResult = array (
				"ack" => 0,
				"departbatch_labelcode" => $this->_code,
				"error" => "",
				"errorCode" => ""
		);
		
		try {
			/*
			 * 1、验证订单是否可用
			*/
			// 调用服务接口，同步审核订单
			$result = $this->excuteBatchOrderTo ($data);
		   
		
			if (! $result ["ack"]) {
				$callResult ["error"] = $result ["message"] . "";
				$callResult ["errorCode"] = "001";
				return $callResult;
			}
				
			if ($result ["ack"]) {
				$result_temp = $result ["data"] ['Item'];
		
				if ( trim ( $result_temp ["Result"]  ) == "1001") {
					$callResult ["ack"] = 1;
					$callResult ["message"] = $result_temp['Message'];
				} else {
					
					$errors = $result_temp ["ErrorConsignmentNumberList"];
					$error='';
					foreach ($errors as $key => $val){
						$error .='总包号：'.$val['ConsignmentNumber'].$result_temp ['Message'].',原因：'.$val['ErrorMessage'];
							
					}
					
					$callResult ["message"] = $result_temp['Message'];
					$callResult ["error"] = $error;
					$callResult ["errorCode"] = $result_temp['Result'];
				}
				
			}
		} catch ( Exception $e ) {
			$callResult ["error"] = "同步未知异常，订单号：" . $this->orderCode . "异常信息：" . $e->getMessage ();
		}
		
		return $callResult;
		
		
	}
	
	
	// 调用服务接口
	public function excuteBatchOrderTo(){
		try {
			//构造订单信息
			foreach ($this->_synchronousOrder as $kk =>$vv){
				$order=Service_CsdOrder::getByField($vv['shipper_hawbcode'],'shipper_hawbcode');
				if (!$order){
					throw new Exception("订单不存在");
				}
				$invoice = Service_CsdInvoice::getByCondition( array("order_id"=>$order["order_id"]) );
				// 总申报数量
				$declareInvoice_temp = array ();
				$declareInvoice=array();
				foreach ($invoice as $key => $val) {
					//申报价值
					$cnname = (! empty ( $val["invoice_cnname"] ) ? $val["invoice_cnname"] : $val["invoice_enname"]);
					$enname = (! empty ( $val["invoice_enname"] ) ? $val["invoice_enname"] : $val["invoice_cnname"]);
					$declareInvoice_temp ["Qty"] = $val ["invoice_quantity"];
					$declareInvoice_temp ["UnitWeight"] = $val ["invoice_weight"];
					$declareInvoice_temp ["UnitPrice"] = round($val["invoice_totalcharge"]/$val["invoice_quantity"],3);
					$declareInvoice_temp ["ApplicationName"] = $enname ? $enname : $cnname;
					$declareInvoice_temp ["SKU"] = $val["sku"];
					$declareInvoice_temp ["HSCode"] = $val ["hs_code"];
					$declareInvoice [] = $declareInvoice_temp;
				}
			
				$detail[] = array (
						"ForecastNumber" => $vv['shipper_hawbcode'],
						"BoxUpNumbers" => $vv['bag_labelcode'],
						"Weight" =>$vv['checkout_grossweight']?$vv['checkout_grossweight']:0.2,
						"CountryCode" => $vv['destination_countrycode'],
						"ShippingFirstName" => $vv['consignee_name'],
						"ShippingLastName" => '',
						"ShippingAddress3" =>'',
						"ShippingCity" => $vv['consignee_city'],
						"ShippingState" => $vv ['consignee_province'],
						"ShippingZip" => $vv['consignee_postcode'],
						"ShippingPhone" => $vv['consignee_telephone'],
						"ShippingAddress1" => $vv ["consignee_street"],
						"ShippingAddress2" => '',
						"SingaporeApplication" => $declareInvoice
				);
			
			}
			$params = array (
					array(
							"ConsignmentNumber" => $this->_code,
							"CustomerCode" => $this->_user,
							"ChannelCode" =>$this->serviceCode,
							"SingaporeDgmDetails" => $detail
					)
			);
			$sysResult = $this->createAndPreAlertOrderService ( $params );
			
		} catch (Exception $e) {
			$sysResult['message']=$e->getMessage();
		}
		
		Ec::showError (
		"**************start*************\r\n"
		. print_r ( $params, true ) . "\r\n"
		. print_r ( $sysResult, true )
		."**************end*************\r\n",
		'SGDGM_API/Create_response_info' . date ( "Ymd" ) );
		
		return $sysResult;
	}	
	
	
	
	
	/**
	 * 创建并预报订单
	 * 
	 * @param unknown_type $data        	
	 * @return multitype:number string NULL array
	 */
	public function createAndPreAlertOrderService($data = array()) {
		$url = $this->_orderOnline . "/api/Special/SingaporeDGMForecast";		
		$result = $this->excuteService ( $url, json_encode ( $data ), "POST" );
		
		return $result;
	}
	
	public function excuteService($url, $params, $method) {
		$result = array (
				"ack" => 0,
				"message" => "",
				"data" => "" 
		);
		try {
			$tuCurl = curl_init ();
			curl_setopt ( $tuCurl, CURLOPT_URL, $url );
			curl_setopt ( $tuCurl, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt ( $tuCurl, CURLOPT_CUSTOMREQUEST, $method );
			curl_setopt ( $tuCurl, CURLOPT_RETURNTRANSFER, 1 );
			
			if ($method == 'POST') {
				curl_setopt ( $tuCurl, CURLOPT_POST, 1 );
				curl_setopt ( $tuCurl, CURLOPT_POSTFIELDS, $params );
			}
			
			curl_setopt ( $tuCurl, CURLOPT_HTTPHEADER, array (
					"Content-Type: application/json; charset=utf-8",
					"Content-length: " . strlen ( $params ) 
			) );
			
			// print_r($tuCurl);die;
			$data = curl_exec ( $tuCurl );
			
			$data = Common_Common::objectToArray ( json_decode ( $data ) );
			$result ["ack"] = 1;
			$result ["data"] = $data;
		} catch ( Exception $e ) {
			$result ["message"] = $e->getMessage ();
		}
		
		Ec::showError ( 
		"**************start*************\r\n" . 
		print_r ( $params, true ) . "\r\n" . 
		print_r ( $data, true ) .
		 "**************end*************\r\n", 
		 'YunExpress_API/Create_response_info' . date ( "Ymd" ) );
		
		return $result;
	}
	
}	