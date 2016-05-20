<?php
class Product_PromotionalSetController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/promotional-set/"; 
        $this->serviceClass = new Service_SellerItemPromotionalSet();
    }

    public function listAction()
    {
        $user_account_arr_new = Service_User::getPlatformUserAll('ebay');
        $user_account_arr_new_tmp = array();
        foreach($user_account_arr_new as $v){
            $user_account_arr_new_tmp[$v['user_account']] = $v;
        }
        $syncStatus = Ebay_SetPromotionalSaleService::$syncStatus;
        if($this->_request->isPost()){
        
            $db = Common_Common::getAdapter();
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
        
            $page = $page ? $page : 1;
            $page = max(0,$page);
            $pageSize = $pageSize ? $pageSize : 20;
        
            $return = array(
                    "state" => 0,
                    "message" => "No Data"
            );
            $con = array();

            $user_account = $this->_request->getParam('user_account', '');
            $con['user_account'] = $user_account;
            $item_id = $this->_request->getParam('item_id', '');
            $item_id = preg_replace('([^0-9]+)', ' ', $item_id);
            $item_id = trim($item_id);
            
            if(!empty($item_id)){
                $item_id_arr = explode(' ', $item_id);                
                $con['item_id_arr'] = $item_id_arr;               
            }
            
            $status = $this->_request->getParam('status', '');
            $con['status'] = $status;
            $count = Service_SellerItemPromotionalSet::getByCondition($con,'count(*)');
        
            $return['total'] = $count;
            if ($count) {
                $data = Service_SellerItemPromotionalSet::getByCondition($con,'*',$pageSize,$page);
                foreach($data as $k=>$v){
                    $v['user_account'] = $v['user_account']&&$user_account_arr_new_tmp[$v['user_account']]?$user_account_arr_new_tmp[$v['user_account']]['platform_user_name']:'';
                    
                    $u = Service_User::getByField($v['user_id'],'user_id');
                    $v['user_name'] = '';
                    if($u){
                        $v['user_name'] = $u['user_name'];
                    }

                    $v['status_title'] = isset($syncStatus[$v['status']])?$syncStatus[$v['status']]:'';
                    $v['auth'] = $v['user_id']==Service_User::getUserId()?'1':0;
                    
                    $data[$k] = $v;
                }
                $return['data'] = $data;
                $return['state'] = 1;
            }
            die(Zend_Json::encode($return));        
        }        

        $this->view->user_account_arr = $user_account_arr_new_tmp;
        $this->view->syncStatus = $syncStatus;        
        
        echo Ec::renderTpl($this->tplDirectory . "list.tpl", 'layout');
        
    }
    

    /**
     * 订单导入
     */
    public function importAction(){
        
        if($this->getRequest()->isPost()){
            $return = array(
                    'ask' => 0,
                    'message' => 'Request Method Err'
            );
            $file = $_FILES['fileToUpload'];
    
            $process = new Ebay_SetPromotionalSaleService();
            $return = $process->uploadPromotionalSet($file);
            die(Zend_Json::encode($return));
        }
        echo Ec::renderTpl($this->tplDirectory . "promotional_import.tpl", 'layout-upload');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array(
                '操作失败'
            )
        );
        
        if($this->_request->isPost()){
            try{
                $itemId = $this->getParam('item_id');
                
                $item = Service_SellerItem::getByField($itemId, 'item_id');
                if(! $item){
                    throw new Exception('ItemID [' . $itemId . '] not exists');
                }
                
                $row = array(
                    'user_account' => $item['user_account'],
                    'company_code' => $item['company_code'],
                    'item_id' => $item['item_id'],
                    'percent' => $this->getParam('percent', ''),
                    'start_time' => $this->getParam('start_time', ''),
                    'end_time' => $this->getParam('end_time', ''),                        
                    'last_modify_time' => date('Y-m-d H:i:s')
                );
                $row['status'] = '0';
                $row['log'] = '';
                if(strtotime('-365day') > strtotime($row['start_time'])){
                    throw new Exception('StartTime 参数错误');
                }
                if(strtotime('-365day') > strtotime($row['end_time'])){
                    throw new Exception('EndTime 参数错误');
                }
                if(! preg_match('/^([0-9]+)(\.[0-9]+)?$/', $row['percent'])){
                    throw new Exception('Percent(%) 参数错误');
                }
                $paramId = $this->getParam('sips_id');
                
                if(! empty($paramId)){
                    $exit = $this->serviceClass->getByField($paramId, 'sips_id');
                    
                    if($exit['user_id'] != Service_User::getUserId()){
                        throw new Exception('没有编辑权限');
                    }
                    $result = $this->serviceClass->update($row, $paramId);
                }else{
                    $row['user_id'] = Service_User::getUserId();
                    $row['status'] = '0';
                    $result = $this->serviceClass->add($row);
                }
                if($result){
                    $return['state'] = 1;
                    $return['message'] = array(
                        '操作成功'
                    );
                }
            }catch(Exception $e){
                $return['message'] = $e->getMessage();
                $return['errorMessage'][] = $e->getMessage();
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
        
        if(! empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'sips_id')){
            
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
            "message" => "操作失败"
        );
        try{
            if($this->_request->isPost()){
                $paramId = $this->_request->getPost('paramId');
                if(! empty($paramId)){
                    $exit = $this->serviceClass->getByField($paramId, 'sips_id');
                    if($exit['user_id'] != Service_User::getUserId()){
                        throw new Exception('没有操作权限');
                    }
                    if($this->serviceClass->delete($paramId)){
                        $result['state'] = 1;
                        $result['message'] = '操作成功';
                    }
                }
            }
        }catch(Exception $e){            
            $result['message'] = $e->getMessage();
            $result['errorMessage'][] = $e->getMessage();
        }
        die(Zend_Json::encode($result));
    }
    
    public function syncSingleAction(){
        $sips_id = $this->_request->getParam('sips_id', '');
        $service = new Ebay_SetPromotionalSaleService();
        $return = $service->syncPromotionalSetSingle($sips_id);
        die(Zend_Json::encode($return));
    }
}