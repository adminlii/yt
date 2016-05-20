<?php
class Platform_PlatformUserController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "platform/views/platform-user/";
        $this->serviceClass = new Service_PlatformUser();
    }

    public function listAction()
    {
        if($this->_request->isPost()){
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            
            $params = $this->_request->getParams();
            // print_r($params);
            $condition = array();
            $condition['platform'] = $this->_request->getParam('platform', '');
            $condition['status'] = $this->_request->getParam('status', '');
            $condition['user_account_like'] = $this->_request->getParam('user_account', '');
            // echo Service_User::getCustomerCode();exit;
            $condition['company_code'] = Service_User::getCustomerCode();
//             print_r($condition);
//             exit();
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;
            
            if($count){
                $rows = $this->serviceClass->getByCondition($condition, '*', $pageSize, $page, array(
                    'pu_id desc'
                ));
                foreach($rows as $key => $value){
                    if($value['status'] == 1){
                        $value['status'] = "启用";
                    }elseif($value['status'] == 0){
                        $value['status'] = "禁用";
                    }
                    $rows[$key] = $value;
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            // print_r($return);
            
            die(Zend_Json::encode($return));
        }
        $con = array(
            'is_show' => 1
        );
        $platform = Service_Platform::getByCondition($con);
        $platform_arr = array();
        foreach($platform as $k => $v){
            if($v['platform'] != 'b2c'){
                $v['platform'] = trim($v['platform']);
                $platform_arr[$v['platform']] = $v;
            }
        }
        // print_r($platform_arr);exit;
         $this->view->company_code = Common_Company::getCompanyCode();
        $this->view->platform = $platform_arr;
        echo Ec::renderTpl($this->tplDirectory . "platform_user_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage'=>array('Fail.')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();            
            $row = array(
              'pu_id'=>'',
              'short_name'=>'',
              'user_token_id'=>'',
              'user_token'=>'',
              'site'=>'',
              'seller_id'=>'',
              'currency_code'=>'',
              'status'=>'',
              'platform_user_name'=>'',
            );
            
            $row=$this->serviceClass->getMatchEditFields($params,$row);
            
            $paramId = $row['pu_id'];
            if (!empty($row['pu_id'])) {
                unset($row['pu_id']);
            }
            
            if (!empty($paramId)) {
                $result = $this->serviceClass->update($row, $paramId,'pu_id');
            } else {
                $result = $this->serviceClass->add($row);
            }
            if ($result) {
                $return['state'] = 1;
                $return['message'] = array('Success.');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'pu_id')) {
            $rows=$this->serviceClass->getVirtualFields($rows);
            $result = array('state' => 1, 'message' => '', 'data' => $rows);
        }
        die(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $result = array(
            "state" => 0,
            "message" => "Fail."
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            if (!empty($paramId)) {
                if ($this->serviceClass->delete($paramId)) {
                    $result['state'] = 1;
                    $result['message'] = 'Success.';
                }
            }
        }
        die(Zend_Json::encode($result));
    }
    
    public function  newEbayAuthAction(){
        $puId = $this->getParam('pu_id', '0');
        $platformUser = Service_PlatformUser::getByField($puId, 'pu_id');
        if($platformUser){
            if($platformUser['platform'] == 'ebay'){
                $this->view->platformUser = $platformUser;
                echo Ec::renderTpl($this->tplDirectory . "ebay_authorize_index.tpl", 'layout');
            }else{
                echo 'Platform Err';
            }
        }else{
            echo Ec::renderTpl($this->tplDirectory . "ebay_authorize_index.tpl", 'layout');
        }
    }

    public function getSidAction(){
        try {
            $user_account = $this->_request->getParam('user_account', '');
            $pu_id = $this->_request->getParam('pu_id', '');
            $user_account = trim($user_account);
            if(!preg_match('/^[a-zA-Z0-9_\-]+$/', $user_account)){
                die(json_encode(array(
                        'status' => '2',
                        'message' => '账号名称只能包含字母，数字，下划线_，中划线-'
                )));
            }
            if($pu_id){
            
            }else{
                $con = array(
                        'user_account' => $user_account,
                        'company_code' => Common_Company::getCompanyCode()
                );
                // 检查账号是否存在数据库中
                $exists = $this->serviceClass->getByCondition($con);
            
                if(count($exists) > 0){
                    die(json_encode(array(
                            'status' => '2',
                            'message' => '该账户已存在'
                    )));
                }
            }
            Ec::showError('开始账户授权：' . $user_account ,'auth_ebay_20140821');
            
            $config = Common_Company::getEbayConfig();
            //         print_r($config);exit;
            //         print_r($exists);exit;
            $ebayLib = new Ebay_EbayLib();
            $sessionID = $ebayLib->GetSessionID();
            if(false == $sessionID){
                die(json_encode(array(
                        'status' => '0',
                        'message' => 'err sessionId!'
                )));
            }else{
                $url = Common_Company::getEbayLoginUrl() . "?SignIn&runame=" . Common_Company::getEbayRuname() . "&SessID=" . $sessionID;
                //             echo $url;exit;
                Ec::showError('返回eBay登陆链接：' . $url ,'auth_ebay_20140821');
                die(json_encode(array(
                        'status' => '1',
                        'message' => $sessionID,
                        'url' => $url
                )));
            }
        } catch (Exception $e) {
            die(json_encode(array(
                    'status' => '0',
                    'message' => $e->getMessage(),
                    'url' => $url
            )));
        }
        
    }

    public function getTokenAction(){
        $sessionId = $this->_request->getParam('sessionID', '');
        Ec::showError('获得SessionID,准备拉取Token：' . $sessionId ,'auth_ebay_20140821');
        $ebayLib = new Ebay_EbayLib();
    
        $data = $ebayLib->GetTokenNew($sessionId);
        Ec::showError('拉取Token完毕：' . print_r($data,1) ,'auth_ebay_20140821');
//         print_r($data);exit;
        if(false !== $data){
            //跟新数据库
            $token = $data['eBayAuthToken'];
            die(json_encode(array('status'=>1,'msg'=>'获取Token成功','token'=>$token)));
            
        }else{
            die(json_encode(array('status'=>0,'msg'=>'Failed!')));
        }
    }

    public function saveAction(){
        $puId = $this->getParam('pu_id','0');
        $arr = array(
            'platform' => 'ebay',
            'user_account' => $this->getParam('user_account',''),
            'user_token' => $this->getParam('token',''),
            'short_name' => $this->getParam('short_name',''),
            'company_code' => Common_Company::getCompanyCode(),
            'status' => 1,
            'platform_user_name' => $this->getParam('platform_user_name',''),
        );
        if($puId){
            $this->serviceClass->update($arr,$puId,'pu_id');
        }else{
            $con = array(
                    'user_account' => $arr['user_account'],
                    'company_code' => Common_Company::getCompanyCode()
            );
            // 检查账号是否存在数据库中
            $exists = $this->serviceClass->getByCondition($con);
            
            if(count($exists) > 0){
                die(json_encode(array(
                        'status' => 0,
                        'message' => '该账户已存在'
                )));
            }
            $con = array(
                'user_token' => $this->getParam('token','------'),
            );
            // 检查账号是否存在数据库中
            $exists = $this->serviceClass->getByCondition($con);
            if(count($exists) > 0){
                die(json_encode(array(
                        'status' => 0,
                        'message' => '该Token已存在'
                )));
            }
            $this->serviceClass->add($arr);
            //初始化账户
            Common_Company::initAcc(Common_Company::getCompanyCode(), 'ebay', $arr['user_account']);
        }
        
        die(json_encode(array(
                'status' => '1',
                'message' => '保存成功'
        )));
        
    }

    public function saveAmazonAction(){
        try{
            $arr = array(
                'platform' => $this->getParam('platform', 'amazon'),
                'user_account' => $this->getParam('user_account', ''),
//                 'short_name' => $this->getParam('short_name', ''),
                'seller_id' => $this->getParam('seller_id', ''),
                'site' => $this->getParam('site', ''),
                'user_token_id' => $this->getParam('user_token_id', ''),
            	'user_token' => $this->getParam('user_token', ''),
                'company_code' => Common_Company::getCompanyCode(),
                'status' => $this->getParam('status', '1'),
                'platform_user_name' => $this->getParam('platform_user_name', '')
            );
            $arr['short_name'] = $arr['user_account']; 
            foreach($arr as $k=>$v){
                if(empty($v)){
                    throw new Exception($k.'不能为空');
                }else{
                	$arr[$k] = trim($v);
                }
            }
            $con = array(
                'user_account' => $arr['user_account'],
                'company_code' => Common_Company::getCompanyCode()
            );
            // 检查账号是否存在数据库中
            $exists = $this->serviceClass->getByCondition($con);
            if(count($exists) > 0){
                throw new Exception('该账户已存在');
            }
            $this->serviceClass->add($arr);
            
            //初始化账户
            if($arr['platform'] == 'amazon'){
	            Common_Company::initAcc(Common_Company::getCompanyCode(), 'amazon', $arr['user_account']);
            }
            
            die(json_encode(array(
                'status' => '1',
                'msg' => '保存成功'
            )));
        }catch(Exception $e){
            die(json_encode(array(
                    'status' => 0,
                    'msg' => $e->getMessage(),
            )))
            ;
        }
    } 

    public function saveMabangAction(){
    	try{
    		$arr = array(
    				'platform' => $this->getParam('platform', 'mabang'),
    				'user_account' => $this->getParam('user_account', ''),
    				'company_code' => Common_Company::getCompanyCode(),
    				'status' => $this->getParam('status', '1'),
    				'platform_user_name' => $this->getParam('platform_user_name', ''),
    				'seller_id' => Service_User::getUserId(),
    		);
    		$arr['short_name'] = $arr['user_account'];
    		foreach($arr as $k=>$v){
    			if(empty($v)){
    				throw new Exception($k.'不能为空');
    			}else{
    				$arr[$k] = trim($v);
    			}
    		}
    		$con = array(
    				'user_account' => $arr['user_account'],
    				'company_code' => Common_Company::getCompanyCode()
    		);
    		// 检查账号是否存在数据库中
    		$exists = $this->serviceClass->getByCondition($con);
    		if(count($exists) > 0){
    			throw new Exception('该账户已存在');
    		}
    		$this->serviceClass->add($arr);
    
    		//初始化账户
    		if($arr['platform'] == 'amazon'){
    			 
    		}
    
    		die(json_encode(array(
    				'status' => '1',
    				'msg' => '保存成功'
    		)));
    	}catch(Exception $e){
    		die(json_encode(array(
    				'status' => 0,
    				'msg' => $e->getMessage(),
    		)))
    		;
    	}
    }
}