<?php
class Product_PromotionalController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "product/views/promotional/";
        $this->serviceClass = new Service_SellerItemPromotional();
    }

    public function listAction()
    {
        $user_account_arr_new = Service_User::getPlatformUserAll('ebay');
        $user_account_arr_new_tmp = array();
        foreach($user_account_arr_new as $v){
            $user_account_arr_new_tmp[$v['user_account']] = $v;
        }
        if($this->_request->isPost()){
            
            $db = Common_Common::getAdapter();
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $page = max(0, $page);
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            $con = array();
            
            $user_account = $this->_request->getParam('user_account', '');
            $con['user_account'] = $user_account;
            $promotional_status = $this->_request->getParam('promotional_status', '');
            $con['promotional_status'] = $promotional_status;
            $promotional_sale_name = $this->_request->getParam('promotional_sale_name', '');
            $con['promotional_sale_name_like'] = $promotional_sale_name;
            $promotional_sale_id = $this->_request->getParam('promotional_sale_id', '');
            $con['promotional_sale_id'] = $promotional_sale_id;
            $item_id = $this->_request->getParam('item_id', '');
            $con['item_id_like'] = $item_id;
            
            $count = Service_SellerItemPromotional::getByCondition($con, 'count(*)');
            
            $return['total'] = $count;
            if($count){
                $data = Service_SellerItemPromotional::getByCondition($con, '*', $pageSize, $page);
                foreach($data as $k => $v){
                    
                    $data[$k] = $v;
                }
                $return['data'] = $data;
                $return['state'] = 1;
            }
            die(Zend_Json::encode($return));
        }
        
        $sql = "SELECT DISTINCT promotional_status  FROM `seller_item_promotional`;";
        $db = Common_Common::getAdapter();
        $syncStatus = $db->fetchAll($sql);
        // print_r($syncStatus);exit;
        
        $this->view->user_account_arr = $user_account_arr_new_tmp;
        $this->view->syncStatus = $syncStatus;
        echo Ec::renderTpl($this->tplDirectory . "list.tpl", 'layout');
    }

    /**
     * 更新历史
     */
    public function updateAction()
    {
        set_time_limit(0);
        $service = new Ebay_SetPromotionalSaleService();
        $return = $service->SavePromotionalSaleDetails('', '');
        die(Zend_Json::encode($return));
    }

    /**
     * 更新历史
     */
    public function reloadSingleAction()
    {
        set_time_limit(0);
        $service = new Ebay_SetPromotionalSaleService(); 
        $sip_id = $this->getParam('sip_id','');        
        $return = $service->reloadPromotionalSaleDetailsSingle($sip_id);
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
    
        if(! empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'sip_id')){
    
            $result = array(
                    'state' => 1,
                    'message' => '',
                    'data' => $rows
            );
        }
    
        die(Zend_Json::encode($result));
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '操作失败',
            'errorMessage' => array(
                '操作失败'
            )
        );
        
        if($this->_request->isPost()){
            try{                    
                set_time_limit(0);
                $service = new Ebay_SetPromotionalSaleService();
                $row = array(
                    'promotional_sale_name' => $this->getParam('promotional_sale_name', ''),
                    'discount_value' => $this->getParam('discount_value', ''),
                    'promotional_sale_start_time' => $this->getParam('promotional_sale_start_time', ''),
                    'promotional_sale_end_time' => $this->getParam('promotional_sale_end_time', '')
                );
                
                $paramId = $this->getParam('sip_id');
                
                if(! empty($paramId)){     
                    $db = Common_Common::getAdapter();
                    $db->beginTransaction();
                    try{
                        $result = $this->serviceClass->update($row, $paramId);
                        //数据更新
                        $rs = $service->syncPromotionalSingle($paramId);
                        if($rs['ask']=='1'){
                            $db->commit();
                            $return['state'] = 1;
                            $return['message'] = array(
                                    '操作成功'
                            );                            
                        }else{
                            $db->rollback();
                            $return['message'] = $rs['message'];
                            $return['errorMessage'] = $rs['errors'];
                            
                        }
                        
                    }catch(Exception $e){
                        $db->rollback();
                        $return['errorMessage'][] = $e->getMessage();
                    }
                   
                }else{
                    $userAccount = $this->getParam('user_account','');
                    $promotionalName = $row['promotional_sale_name'];
                    $percent = $row['discount_value'];
                    $startTime = $row['promotional_sale_start_time'];
                    $endTime = $row['promotional_sale_end_time'];
                    $rs = $service->createPromotionalSingle($userAccount, $promotionalName, $percent, $startTime, $endTime);
//                     print_r($rs);exit;
                    if($rs['SetPromotionalSaleResponse']['Ack']!='Failure'){
                        $row['promotional_sale_id'] = $rs['SetPromotionalSaleResponse']['PromotionalSaleID'];
                        $row['user_account'] = $userAccount;
                        $row['company_code'] = Common_Company::getCompanyCode();
                        $return['promotional_sale_id'] = $row['promotional_sale_id'];
                        $result = $this->serviceClass->add($row);
                        $return['state'] = 1;
                        $return['message'] = array(
                            '操作成功',
                            'PromotionalSaleID:' . $rs['SetPromotionalSaleResponse']['PromotionalSaleID']
                        );
                        $promotional_sale_id = $row['promotional_sale_id'];
                        $service->SavePromotionalSaleDetailsSingle($userAccount, $promotional_sale_id);
                    }else{
                        $errors = array();
                        $err = $rs['SetPromotionalSaleResponse']['Errors'];
                        if(isset($err[0])){
                            foreach($err as $ee){
                                $errors[] = "[{$userAccount}]:[{$ee['ErrorCode']}]".$ee['LongMessage'];
                            }
                        }else{
                            $errors[] = "[{$userAccount}]:[{$err['ErrorCode']}]".$err['LongMessage'];
                        }
                        $return['message'] = $errors;
                        $return['errorMessage'] = $errors;
                    }
                    
                } 
            }catch(Exception $e){
                $return['message'] = $e->getMessage();
                $return['errorMessage'][] = $e->getMessage();
            }
        }
        die(Zend_Json::encode($return));
    }
    
    /**
     * delete
     */
    public function deleteSingleAction()
    {
        set_time_limit(0);
        $service = new Ebay_SetPromotionalSaleService();
        $sip_id = $this->getParam('sip_id','');
        $return = $service->deletePromotionalSingle($sip_id);
        //数据更新
        $service->reloadPromotionalSaleDetailsSingle($sip_id);
        die(Zend_Json::encode($return));
    }
    

    /**
     * delete
     */
    public function syncSingleAction()
    {
        set_time_limit(0);
        $service = new Ebay_SetPromotionalSaleService();
        $sip_id = $this->getParam('sip_id','');
        $return = $service->syncPromotionalSingle($sip_id);
        //数据更新
        $service->reloadPromotionalSaleDetailsSingle($sip_id);
        die(Zend_Json::encode($return));
    }
    
}