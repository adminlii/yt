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


}