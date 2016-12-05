<?php

class Default_ApiController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('svc');
    }

    public function svcAction()
    {
        set_time_limit(0);
        error_reporting(0);
        $return = array(
            'ask' => 'Failure',
            'message' => '数据格式不正确'
        );
        try {
            $json = file_get_contents('php://input');
            if (empty ($json)) {
                throw new Exception ('无请求数据');
            }
            // 请求格式为json
            $req = json_decode($json, true);
            Ec::showError(print_r($req, true), 'loadOrder_condition' . date('Y-m-d'));
            if (!$req) {
                throw new Exception ('数据格式需为json格式');
            }
            $svc = new Common_ApiSvc();
            $return = $svc->callService($req);
        } catch (Exception $e) {
            $return ['message'] = $e->getMessage();
        }
        die(Zend_Json::encode($return));
    }


    public function serviceDebugAction()
    {
        $return = array(
            'ask' => 'Failure',
            'message' => '数据格式不正确'
        );
        try {
            $json = file_get_contents('php://input');
            Ec::showError($json, 'serviceDebug_');
            if (empty ($json)) {
                throw new Exception ('无请求数据');
            }
            // 请求格式为json
            $req = json_decode($json, true);
            if (!$req) {
                throw new Exception ('数据格式需为json格式');
            }
            $svc = new Common_ApiSvc();
            $return = $svc->callService($req);
        } catch (Exception $e) {
            $return ['message'] = $e->getMessage();
        }
        echo json_encode($return);
    }
    

    /**
     * 接口入口
     *
     * @param $req
     * @return array
     */
    public function testAction()
    {
    	$service = '';
    	try {
    		
    		$obj = new Common_ApiSvcService();
    		$return = $obj->loadOrder();
    	} catch (Exception $e) {
    		$return = array(
    				'ask' => 'Failure',
    				'message' => $e->getMessage()
    		);
    	}
    	// 记录响应数据
    	echo json_encode($return);
    }
    
    /**
     * 接口入口
     *
     * @param $req
     * @return array
     */
    public function testSyncAction()
    {
    	$service = '';
    	try {
    		$params = array(
    				'ask' => 0,
    				'orderNo' => '1212121212',
    				'apiCode' => 'DEDHL',
    				'smCode' => 'DEDHL',
    				'errorCode' => '',
    				'message' => '操作成功',
    				'errorMessage' => '',
    				'data' => array('trackingNumber' =>1,
    						'fileType' =>'PDF',
    						'serviceNumber' =>1,
    						'syncExpressTime' =>1),
    				);
    		
    		$obj = new Common_ApiSvcService();
    		$return = $obj->backTrackingNo($params);
    	} catch (Exception $e) {
    		$return = array(
    				'ask' => 'Failure',
    				'message' => $e->getMessage()
    		);
    	}
    	// 记录响应数据
    	echo json_encode($return);
    }
    
    /**
     * 中邮TOMS通知接受接口
     */ 
	public function receiveAction() {
		//echo 111;die;
		header("Content-type:text/html;charset=utf-8");
		$param = $this->_request->getParams();
		$json = file_get_contents('php://input');
		// 请求格式为json
		$notice = json_decode($json, true);
		
		//Ec::showError(print_r($notice), "---test---");
		$obj = new API_YunExpress_ForApiService();
		$result = $obj->receiveNotice($notice);
		//if(isset($result['message']))
		//unset($result['message']);
        echo  Zend_Json::encode($result);

	}

	/**
	 * 异步通知中邮TOMS接口
	 */
	public function changeNoAction() {
		ignore_user_abort(true);
		set_time_limit(0);
		$debug = array();
		try {
			header("Content-type:text/html;charset=utf-8");
			$param = $this->_request->getParams();
			$obj = new API_YunExpress_ForApiService();
			$scdOrder = Service_CsdOrder::getByField($param["order_id"], 'order_id');
			$param['server_product_code'] = $scdOrder['product_code'];
			$debug["line1"] = $param;
			$debug["line2"] = $scdOrder;
			$channel['server_product_code'] = $scdOrder['product_code'];
			$obj->setParam("YUNEXPRESS", $scdOrder['shipper_hawbcode'], '', $channel['server_product_code'],0);
			$debug["line3"] = $scdOrder['shipper_hawbcode']."|".$channel['server_product_code'];
			//日志记录start
			$logrow = array();
			$logrow['requestid'] = $param["uuid"];
			$logrow['type'] = 1;
			$logrow['detail'] = '异步请求TMS创建标签开始';
			list($usec, $sec) = explode(" ", microtime());
            $logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
			$db = Common_Common::getAdapter();
			$db ->insert('logapi', $logrow);
			//日志记录end
			$result = $obj->sendToTms($param["uuid"]);
			
			$param["shipper_hawbcode"] = $scdOrder['shipper_hawbcode'];
			$returnArr = array("ack"=>0,"message"=>"","param"=>$param);
			if(!empty($result)){
				$res  = json_decode($result,1);
				if(!empty($res['Data'])){
					$returnArr["ack"]=1;
					$returnArr["data"]=$res['Data']['TrackingNumber'];
					$returnArr["_data"]=$res['Data']['SmallLabelNumber'];
					$debug["line4"] = 1;
				}else{
					$returnArr["ack"]=2;
					$returnArr["errorcode"] = isset($res["ResponseError"]["Code"])?$res["ResponseError"]["Code"]:"";
					$returnArr["message"]   = isset($res["ResponseError"]["LongMessage"])?$res["ResponseError"]["LongMessage"]:"";
					$debug["line4"] = 2;
				}
					
			}
		} catch (Exception $e) {
			$debug['errmsg'] = $e->getTraceAsString().$e->getMessage();
		}
		Ec::showError("**************start*************\r\n"
				."获取异步通知结果\r\n"
				. microtime_float()."\r\n"
				. $result."\r\n"
				.print_r($debug,true)."\r\n"
				.print_r($returnArr,true)."\r\n"		
				. "**************end*************\r\n",
				'YunExpress_API/Create_async_info'.date("Ymd"));
		//更新订单
		$url  = $_SERVER["HTTP_HOST"]."/default/api/do-after-change-no";
		$data = json_encode($returnArr); 
		$obj->curl_send($url,$data,array(),"post");
		
	}
	//异步更新订单（为了防止ChangeNo获取TMS标签超时>10s，pdo数据库连接会超时）
	public function doAfterChangeNoAction(){
		$json = file_get_contents('php://input');
		
		
		$result = json_decode($json,true);
		//日志记录start
		$logrow = array();
		$logrow['requestid'] = $result["param"]["uuid"];
		$logrow['type'] = 1;
		$logrow['detail'] = '异步请求TMS创建标签结束';
		list($usec, $sec) = explode(" ", microtime());
		$logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
		$db = Common_Common::getAdapter();
		$db ->insert('logapi', $logrow);
		//日志记录end
		Ec::showError("**************start*************\r\n"
				. microtime_float()."\r\n"
						. print_r($result,true)."\r\n"
								. "**************end*************\r\n",
								'YunExpress_API/Create_async_info'.date("Ymd"));
		
		$order_process = array(
				'order_id' => $result["param"]["order_id"],
				'server_channelid' => $result["param"]['server_channelid'],
				'shipper_hawbcode' => $result["param"]['shipper_hawbcode'],
				'formal_code' => $result["param"]['formalCode'],
				'ops_create_date' => date('Y-m-d H:i:s'),
		);
		if($result["ack"]==1){
			// 更新单号，订单状态改为"P"已预报
			$update_order = array('server_hawbcode' => $result['data'],"order_status" => "P");
			if(!empty($result['_data'])&&$result['_data']!='null'){
				$update_order['server_hawbcode'] = $result['_data'];
				$update_order['small_hawbcode']  = $result['data'];
			}
			Service_CsdOrder::update($update_order, $result["param"]["order_id"]);
			//更新物流主干
			$update_TakTrackingbusiness = array('server_hawbcode' => $result['data']);
			Service_TakTrackingbusiness::update($update_TakTrackingbusiness,$result["param"]['shipper_hawbcode'],"shipper_hawbcode");
			$order_process['trackingnumber_status'] =1;//成功获取跟踪号
			$order_process['ops_status'] =1;
		}else if($result["ack"]==2){
			if(!empty($result['errorcode'])&&$result['errorcode']!="0x00000002"){
				//丢到问题件
				// 更新单号，订单状态改为"D"草稿
				$_update_order = array("order_status" => "D");
				Service_CsdOrder::update($_update_order, $result["param"]["order_id"]);
				
				// 更新预报数据状态--预报异常
				$order_process["ops_status"]=20;
				$order_process["ops_note"]=$result['message'];
			}else{
				$order_process['ops_status'] =1;
				//调用服务
				$obj = new API_YunExpress_ForApiService();
				$obj->setParam("YUNEXPRESS", $result["param"]['shipper_hawbcode'], '', $result["param"]['server_product_code'],0);
				$tbresult = $obj->createAndPreAlertOrderServiceByCode();
				if($tbresult['ack'] == '0') {
						// 如果同步订单失败，更新订单状态改为"D"草稿
						// 更新单号，订单状态改为"D"草稿
						$_update_order = array("order_status" => "D");
						Service_CsdOrder::update($_update_order, $result["param"]["order_id"]);
						$order_process["ops_status"]=20;
						$order_process["ops_note"]  = $tbresult['error'];
				}
			}
		}
		Service_OrderProcessing::add($order_process);
		//日志记录start
		$logrow = array();
		$logrow['requestid'] = $result["param"]["uuid"];
		$logrow['type'] = 1;
		$logrow['detail'] = '异步请求TMS创建标签处理结果结束';
		list($usec, $sec) = explode(" ", microtime());
		$logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
		$db = Common_Common::getAdapter();
		$db ->insert('logapi', $logrow);
		//日志记录end
	}
	
	//异步更新订单（为了防止ChangeNo获取TMS标签超时>10s，pdo数据库连接会超时）
	public function doproAfterChangeNoAction(){
		$json = file_get_contents('php://input');
		$result = json_decode($json,true);
		Ec::showError("**************start*************\r\n"
				. microtime_float()."\r\n"
						. print_r($result,true)."\r\n"
								. "**************end*************\r\n",
								'YunExpress_API/Create_asynclx_info'.date("Ymd"));
		if($result["ack"]==1){
			// 更新单号，订单状态改为"P"已预报
			$update_order = array('server_hawbcode' => $result['data'],"order_status" => "P");
			Service_CsdOrder::update($update_order, $result["param"]["order_id"]);
			//更新物流主干
			//$update_TakTrackingbusiness = array('server_hawbcode' => $result['data']);
			//Service_TakTrackingbusiness::update($update_TakTrackingbusiness,$result["param"]['shipper_hawbcode'],"shipper_hawbcode");
			$order_process['trackingnumber_status'] =1;//成功获取跟踪号
			$order_process['ops_status'] =1;
		}else if($result["ack"]==2){
			if(!empty($result['errorcode'])&&$result['errorcode']!="0x00000002"){
				//丢到问题件
				// 更新单号，订单状态改为"D"草稿
				$_update_order = array("order_status" => "D");
				Service_CsdOrder::update($_update_order, $result["param"]["order_id"]);
	
				// 更新预报数据状态--预报异常
				$order_process["ops_status"]=20;
				$order_process["ops_note"]=$result['message'];
			}else{
				//复原
				$order_process["ops_syncing_status"] = 0;
				$order_process["ops_status"]=0;
			}
		}
		Service_OrderProcessing::update($order_process,$result["param"]["ops_id"]);
	}
	
	public function test1Action()
	{
		sleep(20);
		$return = array(
			"Data"	=>array("OrderID"=>"EMS16195209080","TrackingNumber"=>"AS000001933CN"),
		);
		// 记录响应数据
		echo json_encode($return);
	}
	
	public function test21Action(){
		$sc = new Common_ApiService();
		$req  = array(
				'usercode'=>'1',
				'userpwd'=>'123456',
				
		);
		$req['userpwd'] = urlencode(base64_encode($req['userpwd']));
		print_r($sc->setToken($req));
	}
	
	public function test22Action(){
		$arr = array(
			'usercode'=>'1',
			'versions'=>1,
			'service'=>'create',
			'token'=>'72b2b888d57fb245b903ae5218a0ebaf',
			'order'=>array(
					'refer_hawbcode'=>'',
					'order_weight'=>1,
					'order_pieces'=>1,
					'order_length'=>10,
					'order_width'=>10,
					'order_height'=>10,
					'mail_cargo_type'=>4,
					'product_code'=>'ESB',
					'country_code'=>'DE',
			),
			'consignee'=>array(
				    'consignee_company'=>'asc',
					'consignee_name'=>'asc',
					'consignee_province'=>'',
					'consignee_email'=>'',
					'consignee_street'=>'9B Griffin Ave,Epsom',
					'consignee_street2'=>'',
					'consignee_street3'=>'',
					'consignee_telephone'=>'121212123',
					'consignee_city'=>'Berlin',
					'consignee_mobile'=>'',
					'consignee_postcode'=>'10115',
			),
			'shipper'=>array(
					'shipper_company'=>'STEVE COM',
					'shipper_name'=>'STEVE LIU',
					'shipper_countrycode'=>'CN',
					'shipper_province'=>'RFEWREWREWR',
					'shipper_street'=>'510||MINZHI DA DAO',
					'shipper_telephone'=>'075577887',
					'shipper_city'=>'shenzhen',
					'shipper_postcode'=>'210001',
			),
			'invoice'=>array(
					array(
							'invoice_enname'=>'case',
							'invoice_cnname'=>'盒子',
							'invoice_quantity'=>1,
							'invoice_unitcharge'=>10,
							'invoice_weight'=>1.5,
							'invoice_note'=>'',
							'invoice_url'=>'',
							'unit_code'=>'PCE',
							'sku'=>'shkk',
							'hs_code'=>''
					),
			)							
		);
		print_r(json_encode($arr));
	}
	
	public function test23Action(){
		$arr = array(
				'usercode'=>'1',
				'versions'=>1,
				'service'=>'createDhl',
				'token'=>'79d96dea0366f5e91e20a9f2da4c8966',
				'order'=>array(
						'refer_hawbcode'=>'',
						'dangerousgoods'=>1,
						'mail_cargo_type'=>4,
						'country_code'=>'DE',
						'insurance_value_gj'=>'15',
						'invoice_totalcharge_all'=>'22',
				),
				'consignee'=>array(
						'consignee_company'=>'asc',
						'consignee_name'=>'asc',
						'consignee_province'=>'',
						'consignee_email'=>'',
						'consignee_street'=>'9B Griffin Ave,Epsom',
						'consignee_street2'=>'',
						'consignee_street3'=>'',
						'consignee_telephone'=>'121212123',
						'consignee_city'=>'Berlin',
						'consignee_mobile'=>'',
						'consignee_postcode'=>'10115',
				),
				'shipper'=>array(
						'shipper_company'=>'STEVE COM',
						'shipper_name'=>'STEVE LIU',
						'shipper_countrycode'=>'CN',
						'shipper_province'=>'RFEWREWREWR',
						'shipper_street'=>'510||MINZHI DA DAO',
						'shipper_telephone'=>'075577887',
						'shipper_city'=>'shenzhen',
						'shipper_postcode'=>'210001',
				),
				'productinformations'=>array(
						array(
								'invoice_quantity'=>1,
								'invoice_weight'=>1,
								'invoice_length'=>10,
								'invoice_width'=>10,
								'invoice_height'=>10,
						),
						array(
								'invoice_quantity'=>2,
								'invoice_weight'=>2,
								'invoice_length'=>10,
								'invoice_width'=>10,
								'invoice_height'=>10,
						),
				),
				'productinformations_ext'=>array(
						'invoice_enname'=>'case',
						'invoice_cnname'=>'盒子',
						'invoice_currencycode'=>'USD',
						'invoice_shippertax'=>'test',
						'invoice_consigneetax'=>'test1',
						'hs_code'=>'test2',
				),
				'invoice'=>array(
						'makeinvoicedate'=>'2016-10-11',
						'export_type'=>'Permanent',
						'trade_terms'=>'DAP-Delivered at Place',
						'invoicenum'=>'test3',
						'pay_type'=>'freight prepaid',
						'fpnote'=>'test4',
						'invoice_type'=>1,
						'detail'=>
						array(
								array(
										'invoice_note'=>'test5',
										'invoice_quantity'=>1,
										'invoice_shipcode'=>'test7',
										'invoice_unitcharge'=>2,
										'invoice_proplace'=>'CN',
								),
								array(
										'invoice_note'=>'test6',
										'invoice_quantity'=>2,
										'invoice_shipcode'=>'test8',
										'invoice_unitcharge'=>10,
										'invoice_proplace'=>'CN',
								),
								
						),
				),
				'extraservice'=>array(
						'C2'
				)
		);
		print_r(json_encode($arr));
	}
	
	
	public function test24Action(){
		$arr = array(
				'usercode'=>'1',
				'versions'=>1,
				'service'=>'getLabel',
				'token'=>'79d96dea0366f5e91e20a9f2da4c8966',
				'order_num'=>'EMS1628450000',
				
		);
		print_r(json_encode($arr));
	}
	
	public function test25Action(){
		$arr = array(
				'usercode'=>'1',
				'versions'=>1,
				'service'=>'createFba',
				'token'=>'79d96dea0366f5e91e20a9f2da4c8966',
				'order_num'=>'EMS1628450000',
				'order'=>array(
						'refer_hawbcode'=>'',
						'product_code'=>'FBA1',
						'boxnum'=>10,
						'storage'=>'PHX3',
				),
				'shipper'=>array(
						'shipper_company'=>'STEVE COM',
						'shipper_name'=>'STEVE LIU',
						'shipper_countrycode'=>'CN',
						'shipper_province'=>'RFEWREWREWR',
						'shipper_street'=>'510||MINZHI DA DAO',
						'shipper_telephone'=>'075577887',
						'shipper_city'=>'shenzhen',
						'shipper_postcode'=>'210001',
				),
				'fileData'=>array(
						'invoicelist'=>array(
							'data'=>'','ext'=>'xlsx'	
						),
						'invoice'=>array(
							'data'=>'','ext'=>'xlsx'
						),
				)
		);
		print_r(json_encode($arr));
	}
	
public function test26Action(){
		$arr = array(
				'usercode'=>'1',
				'versions'=>1,
				'service'=>'createTnt',
				'token'=>'79d96dea0366f5e91e20a9f2da4c8966',
				'order'=>array(
						'refer_hawbcode'=>'',
						'dangerousgoods'=>1,
						'mail_cargo_type'=>4,
						'country_code'=>'US',
						//'insurance_value_gj'=>'1344.26',
						'invoice_totalcharge_all'=>'200',
						'DESCRIPTION'=>'this is test data',
						'service_code'=>'P15N'
				),
				'consignee'=>array(
						'consignee_company'=>'asc',
						'consignee_name'=>'asc',
						'consignee_province'=>'',
						'consignee_email'=>'',
						'consignee_street'=>'9B Griffin Ave,Epsom',
						'consignee_street2'=>'',
						'consignee_street3'=>'',
						'consignee_telephone'=>'121212123',
						'consignee_city'=>'Berlin',
						'consignee_mobile'=>'',
						'consignee_postcode'=>'10115',
				),
				'shipper'=>array(
						'shipper_company'=>'STEVE COM',
						'shipper_name'=>'STEVE LIU',
						'shipper_countrycode'=>'CN',
						'shipper_province'=>'RFEWREWREWR',
						'shipper_street'=>'510',
						'shipper_street2'=>'MINZHI DA DAO',
						'shipper_street3'=>'NI||DAO',
						'shipper_telephone'=>'075577887',
						'shipper_city'=>'shenzhen',
						'shipper_postcode'=>'210001',
				),
				'productinformations'=>array(
						'pack'=>array(
							array('ITEMS'=>1,'WEIGHT'=>10,"LENGTH"=>10,"WIDTH"=>10,"HEIGHT"=>10),		
						),
						'packdetail'=>array(
							array("packId"=>0,"invoice_enname"=>"test","invoice_quantity"=>20,"invoice_weight"=>0.5,"invoice_unitcharge"=>10,"hs_code"=>"test1","invoice_proplace"=>"CN"),	
						)
				),
				'productinformations_ext'=>array(
						'invoice_currencycode'=>'USD',
						'invoice_shippertax'=>'test',
						'invoice_consigneetax'=>'test1',
				),
				'invoice'=>array(
						'makeinvoicedate'=>'2016-10-11',
						'export_type'=>'Permanent',
						'trade_terms'=>'DAP-Delivered at Place',
						'invoicenum'=>'test3',
						'pay_type'=>'freight prepaid',
						'fpnote'=>'test4',
						'invoice_type'=>1,
						
				),
				'extraservice'=>array(
						'C2'
				)
		);
		print_r(json_encode($arr));
	} 
	
	
	public function rtoDoAction()
	{
		$return = array('ret'=>-1,'msg'=>'','data'=>array()); 
		try {
			$json = file_get_contents('php://input');
			if (empty ($json)) {
				throw new Exception ('无请求数据');
			}
			// 请求格式为json
			$req = json_decode($json, true);
			if (!$req) {
				throw new Exception ('数据格式需为json格式');
			}
			$sc = new Common_ApiService();
			$return = $sc->callService($req);
		} catch (Exception $e) {
			$return ['msg'] = $e->getMessage();
		}
		echo json_encode($return);
	}
	
	public function getTokenAction()
	{
		$return = array('ret'=>-1,'msg'=>'','data'=>array());
		try {
			$json = file_get_contents('php://input');
			if (empty ($json)) {
				throw new Exception ('无请求数据');
			}
			// 请求格式为json
			$req = json_decode($json, true);
			if (!$req) {
				throw new Exception ('数据格式需为json格式');
			}
			$sc = new Common_ApiService();
			$req  = array(
					'usercode'=>'1',
					'userpwd'=>'123456',
			
			);
			$return = $sc->setToken($req);
		} catch (Exception $e) {
			$return ['msg'] = $e->getMessage();
		}
		echo json_encode($return);
	}
	
	public function getStorageAction()
	{
		$return = array('ret'=>-1,'msg'=>'','data'=>array());
		try {
			$storageStore	=	new Service_StorageStore();
			$rs = $storageStore->getByCondition(null,'*',0,1);
			if(empty($rs)){
				throw new Exception ('服务器异常，请稍后重试');
			}else{
				$return['ret']=0;
				$return['data']=$rs;
			}
		} catch (Exception $e) {
			$return ['msg'] = $e->getMessage();
		}
		echo json_encode($return);
	}
	
	public function test27Action(){
		$token = substr(md5('1480072717263~!@#$%^&4512*-678'),8,16);
        $privateKey =        $token;
        $iv =                $token;
        $encryptedData = base64_decode(urldecode('DevrK3DiJEPEuQJAfsl9BA%3D%3D'));
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        var_dump(rtrim($decrypted,"\0"));
	}
}