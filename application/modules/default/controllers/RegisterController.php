<?php
class Default_RegisterController extends Ec_Controller_DefaultAction
{

    public $_authCode = 1; // 是否用验证码
    public function preDispatch()
    {
        $this->view->authCode = 1;
        $this->tplDirectory = "default/views/register/";
    }

public function indexAction()
    {
        if($this->getRequest()->isPost()){
            $params = $this->getRequest()->getParams();
            $return = array(
                'ask' => 0,
                'message' => '账号注册失败'
            );
            try{                
                $row = array(
                    'user_code' => trim($this->getParam('user_code', '')) ,
                    'customer_id' => Common_GenCompanyCode::genCompanyCode(),
                    'is_admin' => '1',
                    'up_id' => '1',
                    'user_password' => $this->getParam('user_password', ''),
                    'user_password_confirm' => $this->getParam('user_password_confirm', ''),
                    'user_name' => $this->getParam('user_name', ''),
                    'user_status' => '2',
                    'user_email' => $this->getParam('user_email', ''),
                    'user_mobile_phone' => $this->getParam('user_mobile_phone', ''),
                    'user_phone' => $this->getParam('user_phone', ''),
                    'user_add_time' => date('Y-m-d H:i:s'),
                    'user_last_login' => date('Y-m-d H:i:s'),
                    'user_update_time' => date('Y-m-d H:i:s'),
                	'tms_id'=>1,	
                    'user_password_update_time' => date('Y-m-d H:i:s'),
                	'user_sources'=>$this->getParam('user_sources', ''),
                	'platform_token'=>$this->getParam('platform_token', '')
                );
                if(empty($row['user_sources'])){
                	unset($row['user_sources']);
                }
                if(empty($row['platform_token'])){
                	unset($row['platform_token']);
                }
                $authCode = $this->getParam('authCode','');//验证码
               
                $codeExist = Service_User::getByField($row['user_code'], 'user_code');
                if($codeExist){
                    throw new Exception('用户名已被使用');
                }
                if(! preg_match('/^([a-zA-Z0-9_\-]+)$/', $row['user_code'])){
                    throw new Exception('用户名只能由字母，数字，下划线，中划线组成');
                }
                /* if(empty($row['user_password'])||strlen($row['user_password'])<6){
                    throw new Exception('密码不能为空且长度必须>=6');
                } */
                $regex = "/^(?=.{6,16})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$/";
                if(empty($row['user_password'])||!preg_match($regex, $row['user_password'], $matches)){
                	throw new Exception('密码必须为6~16位,同时包含数字字母组合，请检查.');
                }
                
                if(empty($row['user_password_confirm'])){
                    throw new Exception('确认密码不能为空');
                }
                
                if($row['user_password'] != $row['user_password_confirm']){
                    throw new Exception('两次密码不一致');
                }
                unset($row['user_password_confirm']);
                
                if (!eregi("^[a-zA-Z0-9_\.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}$", $row['user_email'])) {
                	throw new Exception(Ec::Lang('validateEmail'));
                }
                
                $emailExist = Service_User::getByField($row['user_email'], 'user_email');
                if($emailExist){
                    throw new Exception('邮箱已被使用');
                }
               	if(empty($row['user_mobile_phone']) && empty($row['user_phone'])){
               		throw new Exception(Ec::Lang('phone_is_not_empty'));
               	}
               	
               	if(empty($row['user_name'])){
               		throw new Exception('姓名不能为空');
               	}
               	
               	$company_name = $this->getParam('company_name', '');
               	if(empty($company_name)){
               		throw new Exception("公司名称不能为空");
               	}
                
                $verifyCodeObj = new Common_Verifycode();
                $verifyCodeObj->set_sess_name('AdminVerifyCode'); //重置验证码
                if (!$verifyCodeObj->is_true($authCode)) {                    
                    throw new Exception(Ec::Lang('verifyCodeMessage'));
                }
                //邮箱验证的验证码
                $activate_code = rand(6,89898787);
                $row['user_activate_code'] = $activate_code;
                
                //密码加密
                $row['user_password'] = Ec_Password::getHash($row['user_password']);
                $userId = Service_User::add($row);
                //公司
                $companyRow = array(
                    'company_code' => $row['customer_id'],
                    'company_name' => $company_name,
                	'company_update_time'=>date('Y-m-d H:i:s')
                );
                $db = Common_Common::getAdapterForDb2();
                $db->insert('company',$companyRow );
               // Service_Company::add($companyRow);
                //推送客户信息至WMS
                $obj = new Common_ThirdPartWmsAPI();
                $return_wms = $obj->createCompany($row['customer_id']);
                
                //暂时给定一个关联customer_id 的表
                
                $row_user_extend_yb = array(
                    'customer_id' =>$row['customer_id'],
                    'collecter_name' =>$this->getParam('collecter_name', ''),
                    'collecter_mobile_phone' =>$this->getParam('collecter_mobile_phone', '185-65653327'),
                    'vip_code' =>$this->getParam('vip_code', ''),
                    
                );
                $db  = Common_Common::getAdapter();
                $db->insert('user_extend_yb', $row_user_extend_yb);
                //发送验证邮件
                $row['company_name']=$company_name;
                $this->sendEmail($row);
                
                $return['ask'] = 1;
                $return['message'] = '账号注册成功';
                $this->view->jumpMsg = $row['user_code'] . "账号注册成功";
            }catch(Exception $e){
                $return['message'] = $e->getMessage();
            }
            die(Zend_Json::encode($return));
        }
        echo $this->view->render($this->tplDirectory . 'register.tpl');
    }
    
    public function sendEmail($row){
    	/*
    	 * 1、发送通知邮件给维护人员
    	*/
     	/* $content = '客户注册：<br>'
     	.'公司名：' . $row['company_name'] .'<br>'
     	.'登录名：' . $row['user_code'] .'<br>'
     	.'邮箱：' . $row['user_email'] .'<br>'
     	.'手机：' . $row['user_mobile_phone'] .'<br>'
     	.'电话：' . $row['user_phone']; */

    	$config = Zend_Registry::get('config');
     	$notice = $config->mails->config->register->notice;
     	/* $paramsPersonnel = array(
     			'bodyType' => 'html',
     			'email' => array($notice),
     			'subject' => 'OMS 注册客户: '.$row['user_code'],
     			'body' => $content
     	); */
    	
    	$url = $this->getRequest()->getHttpHost();
    	$url = 'http://' . $url;
    	
    	$content_customer = "尊敬的客户：" . $row['user_name'] . " 您好!<br>"
    			. "恭喜您成功注册商业渠道发运系统，请牢记您的登录名：" . $row['user_code'] . "<br>"
    			. "即刻可以验证邮箱，请点击以下链接：<br>"
    			. $url .'shipping/default/register/activate-email?user_code='. $row['user_code'] . '&activate=' . $row['user_activate_code']
    			. "<br><br>打不开链接？复制以上地址在浏览器打开即可。";
    	
    	/*
    	 * 2、发送确认邮件给客户
    	*/

    	$paramsCustomer = array(
    			'bodyType' => 'html',
    			'email' => array($row['user_email']),
    			'subject' => '商业渠道发运注册成功' . date('Y-m-d H:i:s'),
    			'body' => $content_customer
    	);
    	 
    	$bol = Common_Email::sendMail($paramsCustomer);

        /*
         * 3、发送通知邮件给客服
         */
     	 //Common_Email::sendMail($paramsPersonnel);
    	return $bol;
    
    }
    
    /**
     * 验证邮箱
     */
    public function activateEmailAction()
    {
        $email = $this->_request->getParam("user_code", "");
        $activate_code = $this->_request->getParam("activate", "");
        if (empty($email) || empty($activate_code)) {
            $this->view->errMsg = "帐户及激活码不能为空";
        } else {
            $conUser = array(
                'user_code' => $email,
                'user_activate_code' => $activate_code
            );
            $userRow = Service_User::getByCondition($conUser);
            if (empty($userRow)) {
                $this->view->errMsg = "未找到匹配的注册信息或已过期，请重新注册.";
            } else {
                if ($userRow[0]['email_verify'] == '0') {
                    //激活成功，并修邮箱验证状态
                    $userUpdata = array(
     					'user_status'=>1,
                        'email_verify' => 1,
                    );
                    Service_User::update($userUpdata, $userRow[0]['user_id'], 'user_id');
                	//插入csi_customer
                    $csiCustomer_row = array(
                    	"customer_id"=>$userRow[0]["customer_id"],
                    	"customer_code"=>$userRow[0]["user_code"],
                    	"customer_shortname"=>$userRow[0]["user_name"],
                    		"customer_allname"=>$userRow[0]["customer_code"],
                    		"customerstatus_code"=>"C",
                    		"customerlevel_code"=>"L1",
                    		"customertype_code"=>"GS",
                    		"customersource_code"=>"C",
                    		"settlementtypes_code"=>"P",
                    		"customer_createdate"=>date("Y-m-d H:i:s"),
                    		"tms_id"=>1,
                    		"owe_intercept"=>"N",
                    			
                    );
                	Service_CsiCustomer::add($csiCustomer_row);
                }
                //$this->view->errMsg = "邮箱验证成功，请等待客服与您联系开通账户.";
                //推送客户信息至WMS
                //$obj = new Common_ThirdPartWmsAPI();
                //$obj->updateCompany($userRow[0]['company_code']);

                $this->view->successUserCode = $conUser['user_code'];
            }
        }
        echo $this->view->render('default/views/default/select_version.tpl');
    }

    /**
     * 验证账户是否已经存在
     * 
     * @return number
     */
    public function verifyUserAction()
    {
        $return = array(
            'ask' => 0,
            'message' => '账号已经存在'
        );
        $user_code = $this->getParam('user_code', '');
        
        $exist = Service_User::getByField($user_code, 'user_code');
        if($exist){
            $return['ask'] = 1;
            $return['message'] = '账号已经存在';
        }else{
            $return['ask'] = 0;
            $return['message'] = '账号不存在';
        }
        
        die(Zend_Json::encode($return));
    }

    /**
     * 验证账户是否已经存在
     * 
     * @return number
     */
    public function verifyEmailAction()
    {
        $return = array(
            'ask' => 0,
            'message' => '账号已经存在'
        );
        $user_email = $this->getParam('user_email', '');
        
        $exist = Service_User::getByField($user_email, 'user_email');
        if($exist){
            $return['ask'] = 1;
            $return['message'] = '邮箱已经存在';
        }else{
            $return['ask'] = 0;
            $return['message'] = '邮箱不存在';
        }
        
        die(Zend_Json::encode($return));
    }
    
    
}