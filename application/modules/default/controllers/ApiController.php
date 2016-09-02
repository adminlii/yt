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
}