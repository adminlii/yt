<?php
include('DES.php');
class Default_IndexController extends Ec_Controller_DefaultAction
{
    public $_loginSuccessUrl = '/';
    public $_authCode = 1; //是否用验证码
    private static $log_name = 'AppForPlatform_Authorized_';

    public function preDispatch()
    {
        $this->view->authCode = $this->_authCode;
        $this->view->errMsg = '';
        $this->tplDirectory = "default/views/default/";
    }
    
    /**
     * 系统选择
     */
    public function selectVersionAction(){
    	echo $this->view->render($this->tplDirectory . 'select_version.tpl');
    }

    /**
     * 数据库查看
     */
    public function dbConfigAction(){
    	$pw = $this->getParam('pw','');
    	if(md5($pw)=='f673872e0234fcd2e8a12e3bfe30f02d'){
    		$db = Common_Common::getAdapter();
    		print_r($db->getConfig());
    	}   	
    }
    /**
     * 快捷登陆
     */
    public function quickLoginAction(){
    	
    	$errMsg = '';
    	$bol = true;
    	if ($this->_request->isPost()) {
    		$token = $this->_request->getParam('token', '');
    		//无法解析字符串
	        $token = Common_Common::authcode($token,'DECODE');
	        if(empty($token)) {
	        	$errMsg = '秘钥丢失，请您重新登陆.';
	        	$bol = false;
	        }
	        
	        $token = unserialize($token);
	    	if (!is_array($token) || empty($token)) {
	        	$errMsg = '无法解析秘钥，请您重新登陆.';
	        	$bol = false;
	        }
	    	if (!isset($token['user_code']) || !isset($token['check_number'])) {
	        	$errMsg = '秘钥参数丢失，请重新登陆.';
	        	$bol = false;
	        }
	        
	        if($bol){
				//解析校验码
				$user_code = $token['user_code'];
				$check_number = $token['check_number'];
				$check_number_bol = true;
				
				$dateTime = date('Y-m-d');
				if($check_number != $dateTime){
					$errMsg = '校验码过期，请重新登陆.';
					$check_number_bol = false;
				}
				
				//校验用户是否存在
				if($check_number_bol){
					$con = array(
						'user_code'=>$user_code,
						);
					$result = Service_User::getByCondition($con);
					if(!empty($result)){
						$param['userName'] = $result[0]['user_code'];
						$param['userPass'] = $result[0]['user_password'];
						$param['valid'] = $this->_authCode;
						
						//API登陆
						$result = Service_User::login($param, 1);
						setcookie('currentPage','',-1,'/');
						if (isset($result['state']) && $result['state'] == '1') {
							$this->_redirect($this->_loginSuccessUrl);
							exit;
						} else {
							$errMsg = isset($result['message']) ? $result['message'] : '';
						}
					}
				}
	        }
    	}else{
    		$errMsg = '系统没有检测到秘钥，请重新登陆.';
    	}
    	$this->view->errMsg = $errMsg;
    	echo $this->view->render($this->tplDirectory . 'select_version.tpl');
    }
    
    /**
     * 快捷登陆-YUNPOST
     */
    public function apiLoginAction(){
    	 
    	$test_user_code = Common_Company::getDBConfig();
    	if(isset($test_user_code['TEST_USER_CODE']) && $test_user_code['TEST_USER_CODE'] != '') {
	    	//解析校验码
	    	$con = array(
	    		'user_code'=> $test_user_code['TEST_USER_CODE']['config_value'],
	    	);
	    	
	    	$result = Service_User::getByCondition($con);
	    	if(!empty($result)){
	    		$param['userName'] = $result[0]['user_code'];
	    		$param['userPass'] = $result[0]['user_password'];
	    		$param['valid'] = $this->_authCode;
	    
	    		//API登陆
	    		$result = Service_User::login($param,1);
	    		setcookie('currentPage','',-1,'/');
	    		if (isset($result['state']) && $result['state'] == '1') {
	    			$this->_redirect($this->_loginSuccessUrl);
	    			exit;
	    		} else {
	    			$errMsg = isset($result['message']) ? $result['message'] : '';
	    		}
	    	}
    	}
    	
    	
    	echo $this->view->render($this->tplDirectory . 'select_version.tpl');
    }

    public function indexAction()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        if ($userAuth->userId && $userAuth->isLogin) {
            $this->view->user=Service_User::getLoginUser();
            echo Ec::renderTpl("", "system-layout-new");
           // $this->_redirect('/order/order/create');
//             die;
        }else{
            $this->view->errMsg = '';
//             echo $this->view->render($this->tplDirectory . 'login.tpl');
            echo $this->view->render($this->tplDirectory . 'select_version.tpl');
        }
        
    }

    public function loginAction()
    {
        $errMsg = '';
        if ($this->_request->isPost()) {
            $param['userName'] = $this->_request->getParam('userName', '');
            $param['userPass'] = $this->_request->getParam('userPass', '');
            $param['authCode'] = $this->_request->getParam('authCode', '');
            $param['valid'] = $this->_authCode;
            $result = Service_User::login($param);
            setcookie('currentPage','',-1,'/');
            if (isset($result['state']) && $result['state'] == '1') {
                $this->_redirect($this->_loginSuccessUrl);
            } else {
                $errMsg = isset($result['message']) ? $result['message'] : '';
            }
        }
        $this->view->errMsg = $errMsg;
//         echo $this->view->render($this->tplDirectory . 'login.tpl');
        echo $this->view->render($this->tplDirectory . 'select_version.tpl');
    }

    /**
     * 订单追踪
     */
    public function getTrackDetailAction()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        try{
            $errMsg = '';
            if($this->_request->isPost()){
                $authCode = $this->_request->getParam('authCode', null);
                
                $code = $this->getParam('code', '');
                
                $order_code = strtoupper($code);
                $order_code = preg_replace('/[^a-zA-Z0-9_\-]+/', ' ', $code);
                $order_code = preg_replace('/\s+/', ' ', $order_code);
                $order_code = trim($order_code);
                if($order_code){
                    $order_code = explode(' ', $order_code);
                }
                
                if(isset($authCode)){
                    $verifyCodeObj = new Common_Verifycode();
                    $verifyCodeObj->set_sess_name('AdminVerifyCode'); // 重置验证码
                    if(empty($authCode) || ! $verifyCodeObj->is_true($authCode)){
                        throw new Exception(Ec::Lang('verifyCodeMessage'));
                    }
                }else{
                    if(! $userAuth->user){
                        throw new Exception(Ec::Lang('AccessDeny'));
                    }
                }
                // print_r($order_code);exit;
                if(empty($order_code) || ! is_array($order_code)){
                    throw new Exception(Ec::Lang('至少需要一个单号'));
                }else{
                    $rsArr = array();
                    foreach($order_code as $server_hawbcode){
                        $rs = Process_Track::getTrackDetail($server_hawbcode);
                        //调取信息
                        if($rs['ask']){
                        	$obj  = 	new API_YunExpress_ForApiService();
                        	//插入头程
                        	//测试用线上删除
                        	$server_hawbcode1 = "BZ001452877US";
                        	$gettrackDetail_rs = $obj->gettrackDetail(2,array("server_code"=>$server_hawbcode1));
                        	if($gettrackDetail_rs['ack']==1){
                        		$data = Common_Common::xml_to_array($gettrackDetail_rs['data']);
                        		
                        		//头插入
                        		$tracklist = $data["trace"];
                        		//更换key值
                        		foreach ($tracklist as $key=>$val){
                        			$tracklist[$key]['Datetime'] = $val['acceptTime'];
                        			$tracklist[$key]['Location'] = $val['acceptAddress'];
                        			$tracklist[$key]['Info']     = $val['remark'];
                        		}
                        		if(is_array($tracklist)&&count($tracklist)>0){
                        			$rs['data']['detail'] = array_merge(array_reverse($tracklist),$rs['data']['detail']);
                        		}
                        	}
                        	//根据订单获取渠道号
                        	//不直接插最简单的order_prossing 找渠道 是因为没有给他设置索引
                        	$orderinfo = Service_CsdOrder::getByField($server_hawbcode,$rs['code_type']);
                        	if(empty($orderinfo))
                        		continue;
							if($orderinfo["product_code"] == "NZ_CP" || $orderinfo["product_code"] == "NZ_DP" || $orderinfo["product_code"] == "NZ_LZ"){
								$channelid = 1;  //NZ_CP，NZ_DP，NZ_LZ对应渠道SAICHENG
							}else if ($orderinfo["product_code"] =="TNT"){
								$channelid = 73;
							}else{
								$channelid = 2;  //G_DHL对应渠道DHL
							}
							$sql = "select sc.formal_code from csi_servechannel sc where sc.server_channelid = {$channelid}";
							$db = Common_Common::getAdapterForDb2();
							$channcel = $db->fetchRow($sql);
							$channcel  = $channcel["formal_code"];
                        	//测试用线上删除
                        	$server_hawbcode = "61299992140425388429";
                        	$channcel = "Fedex";
                        	$gettrackDetail_rs = $obj->gettrackDetail(1,array("server_code"=>$server_hawbcode,"channel"=>$channcel));
                        	if($gettrackDetail_rs['ack']==1){
                        		$data = json_decode($gettrackDetail_rs['data'],1);
                        		//头插入
                        		$tracklist = $data["Data"]["TEvent"];
                        		if(is_array($tracklist)&&count($tracklist)>0){
                        			//倒序合并
                        			$rs['data']['detail'] = array_merge(array_reverse($tracklist),$rs['data']['detail']);
                        		}
                        	}
                        }
                        $rsArr[] = $rs;

                    }
                    $this->view->rsArr = $rsArr;
                }
            }
        }catch(Exception $e){
            $this->view->trackErrMsg = $e->getMessage();
        }
        
        if($userAuth->user){
            $this->view->user = $userAuth->user;
        }
        $this->tplDirectory = "order/views/track/";
        echo Ec::renderTpl($this->tplDirectory . "track_detail.tpl", 'layout');
    } 
    
    
    public function logoutAction()
    {
        $session = new Zend_Session_Namespace('userAuthorization');
        $session->unsetAll();
        session_destroy();
        $errMsg = '';
        setcookie('currentPage','',-1,'/');
        $this->view->errMsg = $errMsg;
//         echo $this->view->render($this->tplDirectory . 'login.tpl');
        echo $this->view->render($this->tplDirectory . 'select_version.tpl');
    }

    public function getLoginStatusAction(){
    	$user_id = Service_User::getUserId();
    	
    	$con = array(
    			'user_id'=>$user_id,
    			'ull_status'=>1
    			);
    	$result_userLoginLog = Service_UserLoginLog::getByCondition($con);
    	$num = count($result_userLoginLog);
    	//bol为1时，为初始登陆
    	$bol = 0;
    	if($num == 1 || $num == 0){
    		$bol = 1;
    	}else{
    		$bol = 0;
    	}
    	$return = array(
//     			TODO 测试第一次登陆
//     			'state'=>1,
    			'state'=>$bol,
    			'message'=>''
    			);
    	die(Zend_Json::encode($return));
    }

    public function barcodeAction()
    {
        Common_Barcode::barcode($this->_request->code);
        exit;
    }

    public function barcode1Action()
    {
    	Common_Barcode::barcode1($this->_request->code);
    	exit;
    }
    public function barcode2Action()
    {
    	Common_Barcode::barcode2($this->_request->code);
    	exit;
    }

    public function verifyCodeAction()
    {
        $verifyCode = new Common_Verifycode();
        $verifyCode->set_sess_name('AdminVerifyCode');
        echo $verifyCode->render();
    }
    
    public function testAction(){
    	$ebayAppDevKey = Common_Company::getEbayDevid();
    	$des_key = $this->formatNum($ebayAppDevKey,8);
    	echo '解密Key：' . $des_key . '<br/>';
    	
    	$key = "abcdefgh";
		$input = "a";
		$crypt = new Common_DesTool("123456");
		echo "加密值:".$crypt->encrypt($input)."<br/>";
		echo "解密值:".$crypt->decrypt($crypt->encrypt($input))."<br/>";
		
		
		$ss = new Common_DesTool("ABCDEF");
		echo "错误的解密值:".$ss->decrypt($ss->encrypt($input))."<br/>";
    	
    }
    
    public function test222Action(){
    	
    	$des = new Crypt_DES();
    	$des->setKey('12345678');
    	$plaintext = 'a';
    	$jiami = base64_encode($des->encrypt($plaintext));
    	echo "加密值:".$jiami."<br/>";
    	echo "解密值:".$des->decrypt(base64_decode($jiami))."<br/>";
    	
    	$des->setKey('abcdefgh');
    	echo "错误的解密值:".$des->decrypt(base64_decode($jiami));
    }
    
    /**
     * 接收appforebay的链接请求
     * 		1、没注册：让其注册
     * 		2、注册过：直接登录（使用传入的ebaytkn进行对比）
     */
    public function appForEbayAction()
    {
    	/*
    	 * 1、获得ebaytkn
    	*/
    	$ebayAppToken = $this->_request->getParam('ebaytkn','');
    	if(empty($ebayAppToken)){
    		//注册开启验证码
    		$this->view->authCode = 1;
    		$this->view->regMsg = Ec::Lang("registerEbayAppMsg");
    		//进入注册页面
    		echo Ec::renderTpl("default/views/register/" . "register.tpl", 'layout');
    		exit;
    	}
//     	echo '原始Token：' . $ebayAppToken . '<br/>';
    	
    	/*
    	 * 2、解析对比
    	* 		1、获取ebay DevKey，取前八位作为key
    	* 		2、解密获取的ebaytkn
    	*/
    	$ebayAppDevKey = Common_Company::getEbayDevid();
    	$des_key = $this->formatNum($ebayAppDevKey,8);
//     	echo '解密Key：' . $des_key . '<br/>';
    	 
    	$DesTool = new Common_DesTool($des_key);
    	$ebayAppEiasToken = $DesTool->decrypt($DesTool->encrypt($ebayAppToken));
//     	echo '解密Token：' . $ebayAppEiasToken . '<br/>';
    	
    	$wsdl = "http://app.ebay.cn/webServices/SubscriptionInformationWebService?wsdl";
//     	$wsdl = "http://sandbox.app.ebay.cn/webServices/SubscriptionInformationWebService?wsdl";
    	$client = new SoapClient($wsdl, array(
    			'trace' => 1,
    			'exceptions' => 1,
    			'encoding' => 'UTF-8'
    	));
    	
    	$isEbayAuthorized = false;
    	try{
    		$param = array(
    				'eBayEIASTokenId'=>$ebayAppEiasToken,
    				'acDevKey'=>Common_Company::getAppFroEbayDevKey(),
    				'acAppKey'=>Common_Company::getAppFroEbayAppKey(),
    				'appUUID'=>Common_Company::getAppFroEbayErpAppUuid(),
    				);
    		$ret = $client->getUserSubscription(array('parameters'=>$param));
    		
    		if ($ret->return){
    			$ret = Common_Common::objectToArray($ret);
    			$return = $ret['return'];
    			
    			if($return['error']['code'] == '1111'){
    				$isEbayAuthorized = true;
    			}else{
    				Ec::showError("存在异常信息，请求参数：" . print_r($param,true) . "----异常信息：" . print_r($return,true), self::$log_name);
    			}
    		}else{
    			Ec::showError("返回参数异常，请求参数：" . print_r($param,true), self::$log_name);
			}
    		   
    	}catch(Exception $e){
    		Ec::showError("发送请求异常，请求参数：" . print_r($param,true) . "----异常信息：" . $e->getMessage(), self::$log_name);
    	}
    	
    	/*
    	 * 3、对比传入的原始值，查看是否解密失败
    	*/
//     	if($ebayAppToken == $ebayAppEiasToken){
//     		Ec::showError('原始token解密失败，原始值与解密值一致.', self::$log_name);
// 			echo Ec::renderTpl($this->tplDirectory . "register/" . "register.tpl", 'layout');
//     		exit;
//     	}
    	
    	/*
    	 * 4. 查看是否授权注册
    	 * 		a、已在ebayAPP中心进行了授权
    	* 		b、是否已授权注册
    	* 		C、已授权注册-》登陆；还未注册-》注册
    	*/
    	$user_sources = "AppForEbay";
    	$isAuthorized = array('state'=>0);
//     	if($isEbayAuthorized){//TODO 暂时关闭授权验证
		if(true){
    		$isAuthorized = $this->checkAuthorizedRegister($ebayAppToken, $user_sources);
    	}
    	
    	/*
    	 * API登陆条件
    	 * 		a、应用在ebay授权了
    	 * 		b、已经在系统进行过注册
    	 */
    	if($isAuthorized['state']){
    		 
    		//     		echo '已经授权并注册过，正在登陆...';
    		$param = array();
    		$param['userName'] = $isAuthorized['data']['user_code'];
    		$param['userPass'] = $isAuthorized['data']['user_password'];
    		$param['valid'] = 0;
    		//     		print_r($param);
    
    		$result = Service_User::login($param,true);
    		//     		print_r($result);
    			
    		if (isset($result['state']) && $result['state'] == '1') {
    			$this->_redirect($this->_loginSuccessUrl);
    		} else {
    			$errMsg = isset($result['message']) ? $result['message'] : '';
    		}
    		$this->view->errMsg = $errMsg;
    		//     		echo $this->view->render($this->tplDirectory . "default/" . "select_version.tpl");
    		//     		exit;
    		echo Ec::renderTpl("default/views/default/" . "select_version.tpl", 'layout');
    	}else{
    		//原始加密Token放在注册页面
    		$this->view->eBayEIASTokenId = $ebayAppToken;
    		$this->view->userSources = $user_sources;
    		//注册开启验证码
    		$this->view->authCode = 1;
    		$this->view->regMsg = Ec::Lang("registerEbayAppMsg");
    		//进入注册页面
    		echo Ec::renderTpl("default/views/register/" . "register.tpl", 'layout');
    	}
    }
    
    /**
     * 校验是否正常授权注册过
     */
    private function checkAuthorizedRegister($platform_token,$user_sources){
    	$return = array(
    			'state'=>0,
    			'message'=>'',
    			'data'=>''
    	);
    	$con = array(
    			'platform_token'=>$platform_token,
    			'user_sources'=>$user_sources
    	);
    	$result = Service_User::getByCondition($con);
    	if(!empty($result)){
    		$return['state'] = 1;
    		$return['data'] = $result[0];
    	}
    	return $return;
    }
    
    /**
     * 字符串截取，不足位数使用0进行补全
     * @param unknown_type $str
     * @param unknown_type $formatLen
     * @return string
     */
    public function formatNum($str, $formatLen){
    	$strConvert = substr($str, 0,$formatLen);
    	$len = strlen($strConvert);
    	if($len < $formatLen){
    		for ($i = 0; $i < $formatLen - $len; $i++) {
    			$strConvert .= '0';
    		}
    	}
    	return $strConvert;
    }
    
    /**
     * 密码找回
     */
    public function passwordRecoveryAction(){
    	$return = array(
    			'state' => 0,
    			'message' => '密码找回失败'
    	);
    	
    	$user_name = $this->_request->getParam("fp_user_name",'');
    	$user_email = $this->_request->getParam("fp_email",'');
    	
    	try{
	    	if(empty($user_name)){
	    		throw new Exception("账户不能为空");
	    	}else if(empty($user_email)){
	    		throw new Exception("Email不能为空");
	    	}
	    	$con = array(
	    			'user_code'=>$user_name
	    			);
	    	$result = Service_User::getByCondition($con);
	    	if(empty($result)){
	    		throw new Exception("账户：$user_name 未能找到用户信息，请核对账户名是否输入正确.");
	    	}else if($result[0]['is_admin'] != 1){
	    		throw new Exception("抱歉，您的账户类型为子账户，请使用管理员账户进行密码重置.");
	    	}else if($result[0]['user_email'] != $user_email){
	    		throw new Exception("抱歉，您提供的注册Email与注册信息不符，请核对Email是否输入正确.");
	    	}
	    	$resetPassword = rand(6,89898787);
	    	
	    	$updateUser = array(
	    			'user_password'=>Ec_Password::getHash($resetPassword),
	    			'user_update_time'=>date('Y-m-d H:i:s')
	    			);
	    	$response = Service_User::update($updateUser, $result[0]['user_id']);
	    	if($response){
	    		$url = $this->getRequest()->getHttpHost();
    			$url = 'http://' . $url;
    			
    			$content_customer = "尊敬的客户：" . $result[0]['user_name'] . " 您好!<br>"
    					. "我们已将您的密码重置为：" . $resetPassword . "<br>"
    					. "登陆系统后，建议您改为自己特有的密码!!";
    			
    			$paramsCustomer = array(
					'bodyType' => 'html',
					'email' => array($user_email),
					'subject' => '商业渠道发运密码找回 ' . date('Y-m-d H:i:s'),
					'body' => $content_customer
					);
    		 
    			$bol = Common_Email::sendMail($paramsCustomer);
    			$return['state'] = 1;
    			$return['message'] = "密码重置成功，请前往您的注册邮箱，查看。";
	    	}
    	}catch(Exception $e){
    		$return['message'] = $e->getMessage();
    	}
    	die(Zend_Json::encode($return));
    }
    
    public function appForAliexpressAction(){
    	echo 'test';
    }
    
    
    public function abcAction(){
//         require_once 'array2xml2.php';
        $product = array();
        $product[] = array('order_code'=>'SO1234560','sku'=>'sku01','quantity'=>100);
        $product[] = array('order_code'=>'SO1234560','sku'=>'sku01','quantity'=>100);
        $product[] = array('order_code'=>'SO1234560','sku'=>'sku01','quantity'=>100);
        $product[] = array('order_code'=>'SO1234560','sku'=>'sku01','quantity'=>100);
        $arr = array('order_code'=>'SO1234560','products'=>array('product'=>$product));
        echo Common_Common::array2Xml($arr);
    }

    /**
     * @查看产品图片
     * @desc WMS 全站使用
     */
    public function viewProductImgAction()
    {
        $product_id = $this->getParam('product_id', '');
        $attached = Service_ProductAttached::getByField($product_id, 'product_id');
        if($attached){
            if($attached['pa_file_type'] == 'img'){
                $path = Ec_Upload::getBasePath() . trim($attached['pa_path'], '/');
                header( "Content-type: image/jpg");
                echo file_get_contents($path);
            }elseif($attached['pa_file_type'] == 'link'){
                $pic = $attached['pa_path'];
                header("Location: " . $pic);
            }
        }else{
            $pic = '/images/base/noimg.jpg';
            header("Location: " . $pic);
        }
    }
    
    /**
     * 查看图片
     */
    public function viewImgAction(){
        $pa_id = $this->getParam('pa_id', '');
        $attached = Service_ProductAttached::getByField($pa_id, 'id');
        if($attached){
            if($attached['pa_file_type'] == 'img'){
                $path = Ec_Upload::getBasePath() . trim($attached['pa_path'], '/');
                header( "Content-type: image/jpg");
                echo file_get_contents($path);
            }elseif($attached['pa_file_type'] == 'link'){
                $pic = $attached['pa_path'];
                header("Location: " . $pic);
            }
        }else{
            $pic = '/images/base/noimg.jpg';
            header("Location: " . $pic);
        }
    }
    
    /**
     * 产品列表
     */
    public function productListAction() {
        $this->view->productKind = Process_ProductRule::getProductKind();
        echo Ec::renderTpl("order/views/product_kind/product_kind_list.tpl", 'layout');
    } 

    /**
     * 获取登录TOKEN
     */
    public function getErpLoginTokenAction() {
    	// 获取登录用户
    	$user = Service_User::getLoginUser();
    	if(empty($user)) {
    		echo $this->view->render($this->tplDirectory . 'select_version.tpl');
    		exit;
    	}
    	
    	// 取admin的账号数据
    	if($user['is_admin'] != 1 || empty($user['user_erp_authentication_code'])) {
    		$user_row = Service_User::getByCondition(array('customer_id' => $user['csi_customer']['customer_id'], 'is_admin' => 1));
    		$user = $user_row[0];
    	}

    	// 登录URL
    	$url ="http://erp.ez-wms.com/default/quick-login";
    	$syscode = "";
    	$token = "";
    	
    	// 取ERP URL 
    	$sql = "select * from web_newsconfig where news_type IN ('ERP_TOKEN','ERP_TMS_SYSCODE','ERP_URL');";
        $rows = Common_Common::fetchAll($sql);
    	foreach($rows as $k => $row) {
    		if($row['news_type'] == 'ERP_TOKEN') {
    			$token = $row['news_note'];
    			continue;
    		}
    		if($row['news_type'] == 'ERP_TMS_SYSCODE') {
    			$syscode = $row['news_note'];
    			continue;
    		}
    		if($row['news_type'] == 'ERP_URL') {
    			$url = $row['news_note'];
    		}
    	}
    	
    	$data = array(
    			'user_code'=>$user['user_code'],							//账户
    			'user_name'=>$user['user_name'],							//名称
    			'check_number'=>$user['user_erp_authentication_code'],		//校验码
    			'token'=>$token,											//在线ERP的Token
    			'sys_code'=>$syscode,										//客户代码TMS001，WMS001之类的
    			'date_code'=>date('YmdH'),									//时间码，用于控制连接登陆时效，精确到小时
    	);
    	
    	//序列化
    	$date_str = serialize($data);
    	//加密
    	$str = Common_Common::authcode($date_str,'CODE');
    	//url编码
    	$url_str = urlencode($str);
    	
    	$return = array("access_token" => $url_str, 'url' => $url);
    	die(Zend_Json::encode($return));
    }
    
    /**
     * 回调账号数据
     */
    public function collbackByErpAction() {
    	
    		$return = array("ack" => "Fail", 'message' => "Fail.");
    		$token = $this->_request->getParam('access_token', '');
    		
    		// 记录日志
    		Ec::showError("接收TOKEN：" . print_r($token,true), "collback-by-erp");
    		
    		//无法解析字符串
    		$token = Common_Common::authcode($token, 'DECODE');
    		if (empty($token)) {
    			$return['message'] = '秘钥丢失，请您重新登陆.';
    			die(Zend_Json::encode($return));
    		}
    		
    		Ec::showError("-1-", "collback-by-erp");
    	
    		// 反序列化并记录日志
    		$token = unserialize($token);
    		Ec::showError("接收数据：" . print_r($token,true), "collback-by-erp");
    		
    		if (!is_array($token) || empty($token)) {
    			$return['message'] = '无法解析秘钥，请您重新登陆.';
    			Ec::showError("错误：" . print_r($return,true), "collback-by-erp");
    			die(Zend_Json::encode($return));
    		}
    		Ec::showError("-2-", "collback-by-erp");
    		
    		if (!isset($token['user_code']) || !isset($token['check_number'])) {
    			$return['message'] = '秘钥参数丢失，请重新登陆.';
    			Ec::showError("错误：" . print_r($return,true), "collback-by-erp");
    			die(Zend_Json::encode($return));
    		}
    		
    		Ec::showError("-3-", "collback-by-erp");
    		
    		$sql = "select * from web_newsconfig where news_type = 'TOKEN';";
    		$row = Common_Common::fetchRow($sql);
    		
    		if (!empty($row) && $row['news_note'] != $token['token']) {
    			$return['message'] = 'TOKEN异常，请重新登陆.';
    			Ec::showError("错误：" . print_r($return,true), "collback-by-erp");
    			die(Zend_Json::encode($return));
    		}
    		
    		Ec::showError("-4-", "collback-by-erp");
    		
    		//解析校验码
    		$user_code = $token['user_code'];
    		$check_number = $token['check_number'];
    		$date_code = $token['date_code'];
    		 
    		$dateTime = date('YmdH');
    		if ($date_code != $dateTime) {
    			$return['message'] = '校验码过期，请重新登陆.';
    			Ec::showError("错误：" . print_r($return,true), "collback-by-erp");
    			die(Zend_Json::encode($return));
    		}
    		
    		Ec::showError("-5-", "collback-by-erp");
    		
    		// 更新客户
    		$result = Service_User::getByField($user_code, "user_code");
    		if (!empty($result) && $token['system_code'] == 'ERP') {
    			Ec::showError("-6-", "collback-by-erp");
    			$update_row = array('user_erp_authentication_code' => $check_number);
    			Service_User::update($update_row, $result['user_id']);
    		}
    		
    		Ec::showError("-7-", "collback-by-erp");
    		
    		$return = array("ack" => "Success", 'message' => "Success");
    		die(Zend_Json::encode($return));
    }
    
    /**
     * 获取标签
     */
    public function getLabelAction() {
    	$code = $this->getParam('code', '');
    	$index = $this->getParam('index', '0');
    	if(empty($code)) {
    		$pic = '/images/base/noimg.png';
    		header("Location: " . $pic);
    		die;
    	}
    	 
    	// 去掉单号后面的 .png
    	if(strrpos($code,'.')) {
    		$code = substr($code, 0, strrpos($code,'.'));
    	}
    	
    	$order_label = Service_OrderLabel::getByField($code, "order_code");
    	if($order_label['ol_file_type'] == 'png') {
	    	if(empty($order_label)) {
	    		$pic = '/images/base/noimg.png';
	    		header("Location: " . $pic);
	    		die;
	    	}
	    	 
	    	// 第一张图片为标签
	    	$path = $order_label['path'] . "/" . $index .  ".png";
	    	if (!file_exists($path)) {
	    		$pic = '/images/base/noimg.png';
	    		header("Location: " . $pic);
	    		die;
	    	}
	    	
	    	header( "Content-type: image/png");
    	} else if($order_label['ol_file_type'] == 'html') {
    		if(empty($order_label)) {
    			die;
    		}
    		 
    		// 第一张HTML为标签
    		$path = $order_label['path'] . "/" . $index .  ".html";
    		if (!file_exists($path)) {
    			die;
    		}
    		
    		header( "Content-type: text/html");
    	} else {
    		if(empty($order_label)) {
    			die;
    		}
    		 
    		// 第一张PDF为标签
    		$path = $order_label['path'] . "/" . $index .  ".pdf";
    		if (!file_exists($path)) {
    			die;
    		}
    		
    		header( "Content-type: application/pdf");
    	}
    	
    	echo file_get_contents($path);
    }
        
    /**
     * 获取发票
     */
    public function getInvoiceLabelAction() {
    	$code = $this->getParam('code', '');
    	$index = $this->getParam('index', '0');
    	if(empty($code)) {
    		$pic = '/images/base/noimg.png';
    		header("Location: " . $pic);
    		die;
    	}
    	// 去掉单号后面的 .png
        if(strrpos($code,'.')) {
    		$code = substr($code, 0, strrpos($code,'.'));
    	}
    	
    	$order_label = Service_OrderLabel::getByField($code, "order_code");
    	if($order_label['ol_file_type'] == 'png') {
	    	if(empty($order_label)) {
	    		$pic = '/images/base/noimg.png';
	    		header("Location: " . $pic);
	    		die;
	    	}
	    	 
	    	// 图片
	    	$path = $order_label['path'] . "/invoice/" . $index .  ".png";
	    	if (!file_exists($path)) {
	    		$pic = '/images/base/noimg.png';
	    		header("Location: " . $pic);
	    		die;
	    	}
	    	
	    	header( "Content-type: image/png");
    	} if($order_label['ol_file_type'] == 'html') {
    		if(empty($order_label)) {
    			die;
    		}
    		 
    		// HTML
    		$path = $order_label['path'] . "/invoice/" . $index .  ".html";
    		if (!file_exists($path)) {
    			die;
    		}
    		
    		header( "Content-type: text/html");
    	} else {
    		if(empty($order_label)) {
    			die;
    		}
    		 
    		// PDF
    		$path = $order_label['path'] . "/invoice/" . $index .  ".pdf";
    		if (!file_exists($path)) {
    			die;
    		}
    		
    		header( "Content-type: application/pdf");
    	}
    	
    	echo file_get_contents($path);
    }
        
    /**
     * 获取子单
     */
    public function getSubLabelAction() {
    	$code = $this->getParam('code', '');
    	$index = $this->getParam('index', '0');
    	if(empty($code)) {
    		$pic = '/images/base/noimg.png';
    		header("Location: " . $pic);
    		die;
    	}
    	// 去掉单号后面的 .png
        if(strrpos($code,'.')) {
    		$code = substr($code, 0, strrpos($code,'.'));
    	}
    	
    	$order_label = Service_OrderLabel::getByField($code, "order_code");
    	if($order_label['ol_file_type'] == 'png') {
	    	if(empty($order_label)) {
	    		$pic = '/images/base/noimg.png';
	    		header("Location: " . $pic);
	    		die;
	    	}
	    	 
	    	// 图片
	    	$path = $order_label['path'] . "/sub/" . $index .  ".png";
	    	if (!file_exists($path)) {
	    		$pic = '/images/base/noimg.png';
	    		header("Location: " . $pic);
	    		die;
	    	}
	    	
	    	header( "Content-type: image/png");
    	} if($order_label['ol_file_type'] == 'html') {
    		if(empty($order_label)) {
    			die;
    		}
    		 
    		// HTML
    		$path = $order_label['path'] . "/sub/" . $index .  ".html";
    		if (!file_exists($path)) {
    			die;
    		}
    		
    		header( "Content-type: text/html");
    	} else {
    		if(empty($order_label)) {
    			die;
    		}
    		 
    		// PDF
    		$path = $order_label['path'] . "/sub/" . $index .  ".pdf";
    		if (!file_exists($path)) {
    			die;
    		}
    		
    		header( "Content-type: application/pdf");
    	}
    	
    	echo file_get_contents($path);
    }

    /**
     * 帮助
     */
    public function helpAction() {
    	echo $this->view->render($this->tplDirectory . '/help_document.tpl');
    }
    public function printfba1Action(){
    	try{
    		set_time_limit(0);
    		$order_id_arr = $this->getParam('orderId', array());
    		$order_id_arr =  explode(',', $order_id_arr);
    		if(empty($order_id_arr) || !is_array($order_id_arr)){
    			throw new Exception(Ec::Lang('没有需要打印的订单'));
    		}
    		$result = array();
    		$condition["order_id_in"] = $order_id_arr;
    		$orderlist = Service_CsdOrderfba::getByCondition($condition);
    		foreach($orderlist as $order){
    			/* if($order['customer_id']!=Service_User::getCustomerId()){
    			 continue;
    			} */
    			$updateRow = array();
    			$updateRow['print_date'] = date('Y-m-d H:i:s');
    			Service_CsdOrderfba::update($updateRow, $order['order_id'], 'order_id');
    
    			$result[] = $order;
    		}
    		$this->view->data = $result;
    		//调用模板
    		echo $this->view->render($this->tplDirectory . "A3.tpl");
    	}catch(Exception $e){
    		header("Content-type: text/html; charset=utf-8");
    		echo $e->getMessage();
    		exit();
    	}
    }
}