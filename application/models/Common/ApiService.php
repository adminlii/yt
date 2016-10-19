<?php

/**
 * @desc WMS对外API 接口中token和service，versions是保留字段，接口中不可定义出此类参数
 */
class Common_ApiService
{
    protected $_active = 1; // 是否开启与API对接
    protected $_token = '';
    protected $_systemCode = '';
    protected $_requestLog = 1; // 是否开启记录请求信息
    protected $_responseLog = 1; // 是否开启记录响应信息
    protected $_language = 'zh_CN';

    /**
     *
     * @param $req
     * @throws Exception
     */
    private function authentication($req)
    {
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			$req = $this->filterReq($req);
    			if(empty($req['token'])){
    				$return['ret'] = 5;
    				$return['msg'] = Ec::Lang('token值缺失');
    				break;
    			}
    			
    			//验证用户code
    			if(empty($req['usercode'])){
    				$return['ret'] = 1;
    				$return['msg'] = Ec::Lang('用户名未填写');
    				break;
    			}
    			$rs=Service_ApiToken::getByField($req['token'],'token');
    			if(empty($rs)){
    				$return['ret'] = 7;
    				$return['msg'] = Ec::Lang('token不正确');
    				break;
    			}else if($rs['usercode']!=$req['usercode']){
    				$return['ret'] = 7;
    				$return['msg'] = Ec::Lang('token不正确');
    				break;
    			}
    			$return['ret'] = 0;
    			//验证完后直接删除
    			Service_ApiToken::delete($rs['userid']);
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			break;
    		}
    	}while(0);
    	return $return;
    }
	
    /**
     * 遍历请求串，先过滤字符，因为是用于接口，所以只考虑1维数组
     */
    private function filterReq($req){
    	array_walk($array, function (&$value,$key){
    		$value = trim(htmlspecialchars(addslashes($value)));
    	});
    	return $req;
    }
    
    /**
     * 设置Token
     */
    public function setToken($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			$req = $this->filterReq($req);
    			if(empty($req['usercode'])){
    				$return['ret'] = 1;
    				$return['msg'] = Ec::Lang('用户名未填写');
    				break;
    			}
    			if(empty($req['userpwd'])){
    				$return['ret'] = 1;
    				$return['msg'] = Ec::Lang('用户密码未填写');
    				break;
    			}
    			//验证用户身份是否授权
    			$userInfo = Service_User::getByField($req['usercode'],'user_code');
    			if(empty($userInfo)){
    				$return['ret'] = 2;
    				$return['msg'] = Ec::Lang('用户不存在');
    				break;
    			}
    			if(!Ec_Password::comparePassword($req['userpwd'], $userInfo['user_password'])){
    				$return['ret'] = 3;
    				$return['msg'] = Ec::Lang('用户密码错误');
    				break;
    			}
    			$token = md5($req['usercode'].rand(1, 9999).date('YmdHis'));
    			$res = Service_ApiToken::getByField($userInfo['user_id']);
    			if(empty($res)){
    				//插入id
    				$insertData = array(
    						'token'=>$token,
    						'userid'=>$userInfo['user_id'],
    						'usercode'=>$userInfo['user_code']
    				);
    				$rs = Service_ApiToken::add($insertData);
    				
    			}else{
    				//更新
    				$updateData = array('token'=>$token);
    				$rs = Service_ApiToken::update($updateData, $userInfo['user_id']);
    			}
    			if(empty($rs)){
    				$return['ret'] = 4;
    				$return['msg'] = Ec::Lang('生成Token失败,系统错误');
    				break;
    			}
    			$return['ret'] = 0;
    			$return['data'] = $token;
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			break;
    		}
    	}while(0);
    	return $return;
    }
    
    /**
     * 接口入口
     *
     * @param $req
     * @return array
     */
    public function callService($req)
    {
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
	        try {
	            // 记录请求数据
	            $this->_requestLog($req);
	            // 数据验证
	            $authrs = $this->authentication($req);
	            if($authrs['ret']!=0){
	            	$return = $authrs;
	            	break;
	            }
	            $service = $req ['service'];
	            $versions  = $req ['versions'];
	            unset($req ['service']);
	            unset($req ['versions']);
	            $apiclass_str = 'Common_ApiServiceV'.$versions;
	            if(!class_exists($apiclass_str)){
	            	$return['ret'] = 8;
	            	$return['msg'] = Ec::Lang('接口升级中，版本不正确');
	            	break;
	            }
	            
	            $apiclass = new $apiclass_str;
	            if(!method_exists($apiclass,$service)){
            		$return['ret'] = 6;
            		$return['msg'] = Ec::Lang('The system does not support method ' . $service);
            		break;
	            }
	            $return = $apiclass->$service ($req);
	        } catch (Exception $e) {
	        	$return['ret'] = -13;
	        	$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
	        	break;
	        }
        }while(0);
        $this->_responseLog($service, $return);
        return $return;
    }

    

    /**
     * 记录请求信息
     * @param $req
     */
    private function _requestLog($req)
    {
        if (!$this->_requestLog) {
            return;
        }
        try {
            $service = isset ($req ['service']) ? $req ['service'] : 'null';
            $logger = new Zend_Log ();
            $uploadDir = APPLICATION_PATH . "/../data/log/";
            $writer = new Zend_Log_Writer_Stream ($uploadDir . 'apiSvc_request_' . $service . '_data.log');
            $logger->addWriter($writer);
            $logger->info("\n" . date('Y-m-d H:i:s') . ":\n" . (print_r($req, true)));
        } catch (Exception $e) {
        }
    }

    /**
     * 记录请求信息
     *
     * @param $service
     * @param $req
     */
    private function _responseLog($service, $req)
    {
        if (!$this->_responseLog) {
            return;
        }
        try {
            $logger = new Zend_Log ();
            $uploadDir = APPLICATION_PATH . "/../data/log/";
            $writer = new Zend_Log_Writer_Stream ($uploadDir . 'apiSvc_response_' . $service . '_data.log');
            $logger->addWriter($writer);
            $logger->info("\n" . date('Y-m-d H:i:s') . ":\n" . (print_r($req, true)));
        } catch (Exception $e) {
            //
        }
    }

    /**
     * 错误日志
     *
     * @param $error
     */
    private function _log($error)
    {
        try {
            $logger = new Zend_Log ();
            $uploadDir = APPLICATION_PATH . "/../data/log/";
            $writer = new Zend_Log_Writer_Stream ($uploadDir . 'apiSvc.log');
            $logger->addWriter($writer);
            $logger->info(date('Y-m-d H:i:s') . ': ' . $error . " \n");
        } catch (Exception $e) {
            //
        }
    }
}