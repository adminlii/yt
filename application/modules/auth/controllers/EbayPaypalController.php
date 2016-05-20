<?php
class Auth_EbayPaypalController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/";
        $this->serviceClass = new Service_EbayPaypal();
    }

    public function listAction()
    {
        $user_account_arr_result = Service_User::getPlatformUserAll('ebay');
        $user_account_arr_new = array();
        $user_account_arr = array();
        foreach($user_account_arr_result as $v){
            $user_account_arr_new[$v['user_account']] = $v;
            $user_account_arr[] = $v['user_account'];
        }
        
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
            
            $condition = $this->serviceClass->getMatchFields($params);
            $condition['company_code'] = Common_Company::getCompanyCode();
            /*
             * 查询该用户的所有ebay账户
             */
            $condition['ebay_accounts'] = $user_account_arr;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;
            
            if($count){
                $showFields = array(
                    
                    'paypal_account',
                    'name',
                    'pass',
                    'signature',
                    'ebay_account',
                    'ep_id'
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array(
                    'ep_id desc'
                ));
                foreach($rows as $key1 => $value1){
                    $platform_user_name = $user_account_arr_new[$value1['E5']]['platform_user_name'];
                    $value1['platform_user_name'] = $platform_user_name;
                    $rows[$key1] = $value1;
                }
                
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $this->view->user_account_arr = $user_account_arr_new;
        echo Ec::renderTpl($this->tplDirectory . "ebay_paypal_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array(
                'Fail.'
            )
        );
        
        if($this->_request->isPost()){
            $params = $this->_request->getParams();
            $row = array(
                
                'ep_id' => '',
                'user_id' => '',
                'paypal_account' => '',
                'name' => '',
                'pass' => '',
                'signature' => '',
                'ebay_account' => ''
            );
            $row = $this->serviceClass->getMatchEditFields($params, $row);
            $row['company_code'] = Common_Company::getCompanyCode();
            $paramId = $row['ep_id'];
            if(! empty($row['ep_id'])){
                unset($row['ep_id']);
            }
            $errorArr = $this->serviceClass->validator($row);
            
            /*
             * ebay可以绑定多个paypal收款账户，故ebay账户与paypal账户为一对多关系
             * 只验证ebay账户是否重复绑定了paypal账户
             * 擦擦，没有确定ebay账户是否绑定多个paypal账户，先使用适应ebay的业务模式，允许绑定多个paypal账户
             */
            if(empty($errorArr) && empty($paramId)){
                $con = array(
                    "ebay_account" => trim($row['ebay_account']),
                    "paypal_account" => trim($row['paypal_account']),
                    "company_code" => trim($row['company_code'])
                );
                $resultEbayPaypal = Service_EbayPaypal::getByCondition($con);
                if(! empty($resultEbayPaypal)){
                    foreach($resultEbayPaypal as $key => $value){
                        if($value['paypal_account'] == $row['paypal_account']){
                            $errorArr = array(
                                '' => "ebay账户 <font style='color:red;'>$row[ebay_account]</font>,已绑定过paypal账户 <font style='color:red;'>$value[paypal_account]</font>，请勿重复绑定!"
                            );
                            break;
                        }
                    }
                }
            }
            
            if(! empty($errorArr)){
                $return = array(
                    'state' => 0,
                    'message' => '',
                    'errorMessage' => $errorArr
                );
                die(Zend_Json::encode($return));
            }
            
            if(! empty($paramId)){
                $result = $this->serviceClass->update($row, $paramId);
            }else{
                $row['user_id'] = Service_User::getUserId();
                $result = $this->serviceClass->add($row);
            }
            if($result['ask']){
                $return['state'] = 1;
                $return['message'] = array(
                    'Success.'
                );
            }else{
                $return['message'] = array(
                    $result['message']
                );
            }
        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array(
            'state' => 0,
            'message' => 'Fail',
            'data' => array()
        );
        $paramId = $this->_request->getParam('paramId', '');
        if(! empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'ep_id')){
            $rows = $this->serviceClass->getVirtualFields($rows);
            $result = array(
                'state' => 1,
                'message' => '',
                'data' => $rows
            );
        }
        die(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $result = array(
            "state" => 0,
            "message" => "Fail."
        );
        if($this->_request->isPost()){
            $paramId = $this->_request->getPost('paramId');
            if(! empty($paramId)){
                if($this->serviceClass->delete($paramId)){
                    $result['state'] = 1;
                    $result['message'] = 'Success.';
                }
            }
        }
        die(Zend_Json::encode($result));
    }
}