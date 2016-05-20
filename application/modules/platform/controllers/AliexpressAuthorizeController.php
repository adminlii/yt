<?php
class Platform_AliexpressAuthorizeController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "platform/views/aliexpress_user/";
        $this->serviceClass = new Service_PlatformUser();
    }

    /**
     * Aliexpress账号授权
     */
    public function authorizeAction(){
    	$puId = $this->getParam('pu_id', '0');
    	$platformUser = Service_PlatformUser::getByField($puId, 'pu_id');
    	if($platformUser){
    		if($platformUser['platform'] == 'aliexpress'){
    			$this->view->platformUser = $platformUser;
    			echo Ec::renderTpl($this->tplDirectory . "aliexpress_authorize_index.tpl", 'layout');
    		}else{
    			echo 'Platform Err';
    		}
    	}else{
    		echo Ec::renderTpl($this->tplDirectory . "aliexpress_authorize_index.tpl", 'layout');
    	}
    }
    
    /**
     * 获取速卖通授权地址
     */
    public function getSidAction(){
    	
    	$user_account = $this->_request->getParam('user_account', '');
    	$app_key = $this->_request->getParam('app_key', '');
    	$app_signature = $this->_request->getParam('app_signature', '');
    	$pu_id = $this->_request->getParam('pu_id', '');
    	
    	$user_account = trim($user_account);
    	$app_key = trim($app_key);
    	$app_signature = trim($app_signature);
    	if(preg_match('/[\.#]/', $user_account)){
    		die(json_encode(array(
    				'status' => '2',
    				'message' => '账号不可包含.#等特殊字符.'
    		)));
    	}
    	if($pu_id){
    	
    	}else{
    		$con1 = array(
    				'user_account' => $user_account,
    				'company_code' => Common_Company::getCompanyCode()
    		);
    		// 检查账号是否存在数据库中
    		$exists1 = Service_PlatformUser::getByCondition($con1);
    		if(count($exists1) > 0){
    			die(json_encode(array(
    					'status' => '2',
    					'message' => '该账户已存在.'
    			)));
    		}
    		
    		$con2 = array(
    				'app_key'=>$app_key,
    				'company_code' => Common_Company::getCompanyCode()
    		);
    		// 检查APP Key是否存在数据库中
    		$exists2 = Service_PlatformUser::getByCondition($con2);
    		if(count($exists2) > 0){
    			die(json_encode(array(
    					'status' => '2',
    					'message' => '该APP Key已存在.'
    			)));
    		}
    		
    		$con3 = array(
    				'app_signature'=>$app_signature,
    				'company_code' => Common_Company::getCompanyCode()
    		);
    		// 检查APP 签名是否存在数据库中
    		$exists3 = Service_PlatformUser::getByCondition($con3);
    		if(count($exists3) > 0){
    			die(json_encode(array(
    					'status' => '2',
    					'message' => '该APP 签名已存在.'
    			)));
    		}
    	}
    	Ec::showError('开始账户授权：' . $user_account ,'auth_aliexpress_20140821');
    	$app_url = 'http://' . $_SERVER ['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
    	$code_url = Aliexpress_AliexpressLib::getSignatureForSellers($app_url, $app_key, $app_signature);
    	
    	die(json_encode(array(
    				'status' => '1',
    				'url' => $code_url
    		)));
    }
    
    /**
     * 速卖通回调，返回的临时授权码
     */
    public function setCodeAction(){
    	$code = $this->_request->getParam('code');
    	$params = $this->_request->getParams();
    	Ec::showError('返回临时授权码：' . print_r($params,true),'aliexpress_Code');
		if(!empty($code)){
			//放入Session
	    	$session = new Zend_Session_Namespace('Aliexpress_Authorize');
	        $session->unsetAll();
	        $session->aliexpress_code = $code;
	        
	        $this->view->aliexpress_code = $code;
		}
    	echo Ec::renderTpl($this->tplDirectory . "aliexpress_authorize_code.tpl", 'layout');
    }
    
    /**
     * 获取速卖通回调的临时授权码
     */
    public function getCodeAction(){
    	$result = array(
    			'state'=>0,
    			'data'=>'',
    			'message'=>'获得临时授权码失败，请确认账户是否已经登陆授权成功.',
    			);
    	
    	$session = new Zend_Session_Namespace('Aliexpress_Authorize');
    	$code = $session->aliexpress_code;
    	if(!empty($code)){
    		$result['state'] = 1;
    		$result['data'] = $code;
    	}
    	die(json_encode($result));
    }
    
    /**
     * 速卖通回调，返回Token
     */
    public function setTokenAction(){
    	$params = $this->_request->getParams();
    	Ec::showError('返回Token' . print_r($params,true),'aliexpress_Code');
    }
    
    /**
     * 获取速卖通Token
     */
    public function getTokenAction(){
    	$result = array(
    			'status'=>0,
    			'data'=>'',
    			'message'=>'获取Token失败，请返回第一步操作',
    			);
    	$code = $this->_request->getParam('code', '');
    	$app_key = $this->_request->getParam('app_key', '');
    	$app_signature = $this->_request->getParam('app_signature', '');
    	$app_url = 'http://' . $_SERVER ['SERVER_NAME'];	//应用地址
    									//临时授权码
    	$response = Aliexpress_AliexpressLib::getTokenForSellers($app_url, $app_key, $app_signature, $code);
    	Ec::showError('返回Token信息：' . print_r($response,true),'aliexpress_Code');
    	
    	if(isset($response['aliId'])){
	    	$result['data'] = $response;
	    	$result['status'] = 1;
	    	$result['message'] = '获取Token成功';
    	}
    	die(json_encode($result));
    }
    
    /**
     * 保存Aliexpress授权信息
     */
    public function saveAction(){
    	$pu_id = $this->_request->getParam('pu_id','');
    	$user_account = $this->_request->getParam('user_account','');    	
    	$short_name = $this->_request->getParam('short_name','');
    	$platform_user_name = $this->_request->getParam('platform_user_name','');
    	
    	
    	
    	$app_key = $this->_request->getParam('app_key','');
    	$app_signature = $this->_request->getParam('app_signature','');
    	
    	$token = $this->_request->getParam('token','');
    	$aliId = $this->_request->getParam('aliId','');
    	$resource_owner = $this->_request->getParam('resource_owner','');
    	$expires_in = $this->_request->getParam('expires_in','');
    	$refresh_token = $this->_request->getParam('refresh_token','');
    	$refresh_token_timeout = $this->_request->getParam('refresh_token_timeout','');
    	
    	$format = "Y-m-d H:i:s";
    	$date = date($format);
    	$expires_in_date = date($format,strtotime("+$expires_in seconds",strtotime($date)));
    	
    	$refresh_token_timeout_date = preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})(\S{8})/','$1-$2-$3 $4:$5:$6',$refresh_token_timeout);
    	$arr = array(
    			'platform' => 'aliexpress',
    			'user_account' => $user_account,
    			'short_name' => $short_name,
    			'platform_user_name' => $platform_user_name,
    			'company_code' => Common_Company::getCompanyCode(),
    			'status' => 1,
    			
    			'user_token' => $token,
    			'refresh_token'=>$refresh_token,
    			'refresh_token_timeout'=>$refresh_token_timeout_date,
    			'expires_in'=>$expires_in_date,
    			'resource_owner'=>$resource_owner,
    			'aliId'=>$aliId,
    			'app_key'=>$app_key,
    			'app_signature'=>$app_signature,
    	);
//     	print_r($arr);
//     	exit;
    	if($pu_id){
            Service_PlatformUser::update($arr,$pu_id,'pu_id');
        }else{
        	$con = array(
        			'user_account' => $arr['user_account'],
        			'company_code' => Common_Company::getCompanyCode()
        	);
        	 
        	// 检查账号是否存在数据库中
        	$exists = Service_PlatformUser::getByCondition($con);
        	 
        	if(count($exists) > 0){
        		die(json_encode(array(
        				'status' => 0,
        				'message' => '该账户名称已存在.'
        		)));
        	}
        	$con = array(
        			'refresh_token' => $arr['refresh_token'],
        	);
        	// 检查账号是否存在数据库中
        	$exists = Service_PlatformUser::getByCondition($con);
        	if(count($exists) > 0){
        		die(json_encode(array(
        				'status' => 0,
        				'message' => '该账户已经授权过，请检查.'
        		)));
        	}
        	Service_PlatformUser::add($arr);
        	
        	//Common_Company::initAcc(Common_Company::getCompanyCode(), 'aliexpress', $arr['user_account']);
        }
    	
    	die(json_encode(array(
    			'status' => '1',
    			'message' => '保存成功'
    	)));
    }
    
}