<?php
class Product_AmazonCommonController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "product/views/amazon-common/";
        $this->serviceClass = new Service_AmazonReportRequestInfo();
    }

    public function listAction()
    {
        $con = array()

        ;
        $fields = array(
            'user_account',
            'platform_user_name',
            'platform'
        );
        $user_account_arr = Service_PlatformUser::getByCondition($con, $fields, 0, 0, 'platform');
        foreach($user_account_arr as $k=>$v){
            $v['platform'] = strtolower($v['platform'] );
            $user_account_arr[$k] = $v;
        }
        if($this->_request->isPost()){
            $return = array(
                'ask' => 0,
                'message' => 'Fail.'
            );

            // 1:试运行，0：全面启动
            $trial_operation = Service_Config::getByField('SYSTEM_SUPPER_ADMIN_KEY', 'config_attribute');
            if(! $trial_operation){
                $trial_operation = array(
                        'config_attribute' => 'SYSTEM_SUPPER_ADMIN_KEY',
                        'config_value' => 'eccang',
                        'config_description' => '解锁码',
                        'config_add_time' => now(),
                        'config_update_time' => now()
                );
                Service_Config::add($trial_operation);
            }
            try{
                $params = $this->getRequest()->getParams();
                
                // print_r($params);
                // $platform = $this->getParam('platform', 'amazon');
                $acc = $this->getParam('user_account', '');
                if(!preg_match('/\[([^\[\]]+)\](.*)/', $acc, $m)){
                    throw new Exception('参数user_account 格式不正确');
                }
                $platform = $m[1];
                $acc = $m[2];
                $method = $this->getParam('method', '');
                $status = $this->getParam('status', '1');
                $company_code = Common_Company::getCompanyCode();
                
                $key = $this->getParam('key', '');
                if($trial_operation['config_value']!=$key){
                    throw new Exception('解锁码错误');
                }
                if(!empty($method)){
                    if(!preg_match('/^([a-zA-Z0-9]+)_/', $method, $m)){
                        throw new Exception('参数method 格式不正确');
                    }
                    if(strtoupper($platform)!=strtoupper($m[1])){
                        throw new Exception('平台账号与任务不匹配');                        
                    }
                    // print_r($platform);exit;
                    $obj = new Amazon_Common();
                    if(method_exists($obj, $method)){
                        $return = $obj->$method($platform, $acc, $status, $company_code);
                    }else{
                        $return['message'] = 'Method Not Exist--->' . $method;
                    }
                }else{
                    $return = Amazon_Common::accountRunControlInit($acc, $platform,$status,$company_code);                    
                }
                
            }catch(Exception $e){
                $return['message'] = $e->getMessage();
            }
            echo Zend_Json::encode($return);
            exit();
        }
        
        $this->view->user_account_arr = $user_account_arr;

        $methods = get_class_methods('Amazon_Common');
        foreach($methods as $k => $v){
            if(! preg_match('/Switch$/', $v)){
                unset($methods[$k]);
            }
        }
        sort($methods);
        $methodArr = array();
        foreach($methods as $v){
            preg_match('/^([a-zA-Z0-9]+)_/', $v,$m);
            $methodArr[] = array('clazz'=>strtolower($m[1]),'method'=>$v);
        }
        $this->view->methods = $methodArr;
        
        $this->view->statusArr = array(
            '1' => '启用',
            '0' => '禁用'
        );
        
        echo Ec::renderTpl($this->tplDirectory . "run_control_set.tpl", 'layout');
    }
}