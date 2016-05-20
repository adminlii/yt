<?php
class Product_AllowSkuController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/allow-sku/"; 
    }

    public function listAction()
    {

        if ($this->_request->isPost()) {
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
        
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
        
            $return = array(
                    "state" => 0,
                    "message" => "No Data"
            );
        
            $condition = array();
         
            $user_account = $this->getParam('user_account','');
        
            $condition['user_account'] = $user_account;
            $sku = $this->getParam('sku','');
        
            $condition['sku_like'] = $sku;

            $condition['company_code'] = Common_Company::getCompanyCode();
            foreach($condition as $k=>$v){
                if(!is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
            $count = Service_AllowSku::getByCondition($condition, 'count(*)');
            $return['total'] = $count;
        
            if ($count) {
                $rows = Service_AllowSku::getByCondition($condition, '*', $pageSize, $page);
                
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $user_account_arr = Common_Common::getPlatformUser();
        $this->view->user_account_arr = $user_account_arr;
        $this->view->user_account_arr_json = Zend_Json::encode($user_account_arr);
        
        $con = array('warehouse_status'=>'1');
        $warehouse = Service_Warehouse::getByCondition($con);
        
        $warehouseArr = array();
        foreach($warehouse as $v){
            $warehouseArr[$v['warehouse_id']] = $v;
        }
        $this->view->warehouse=$warehouseArr;
        $this->view->warehouseJson=Zend_Json::encode($warehouseArr);
        
        echo Ec::renderTpl($this->tplDirectory . "allow_sku_list.tpl", 'layout');
        
    }

   
    /**
     * 加入黑名单
     */
    public function addToBlackListAction(){
        $company_code = Common_Company::getCompanyCode();
        $userAccount = $this->getParam('user_account');
        $sku = $this->getParam('sku');
        $sku = trim($sku);
        $skuArr = explode("\n", $sku);
        $skuArr = array_unique($skuArr);
        $return = array(
            'ask' => 0,
            'message' => 'No Data'
        );
//         print_r($skuArr);exit;
        foreach($skuArr as $v){
            $v = trim($v);
            if(empty($v)){
                continue;
            }
            $result = Common_AllowSKU::addToBlackList($v,$company_code,$userAccount );
            $return['ask'] = 1;
            $return['message'] = '操作成功，结果如下';
            $return['result'][] = $result;
        }
        die(Zend_Json::encode($return));
    }

    /**
     * 解除黑名单
     */
    public function releaseBlackListAction(){
        $company_code = Common_Company::getCompanyCode();
        $userAccount = $this->getParam('user_account');
        $sku = $this->getParam('sku');
        
        $sku = trim($sku);
        $skuArr = explode("\n", $sku);
        $skuArr = array_unique($skuArr);
        $return = array('ask'=>0,'message'=>'No Data');
        foreach($skuArr as $v){
            $v = trim($v);
            if(empty($v)){
                continue;
            }
            $result = Common_AllowSKU::releaseBlackList($v,$company_code,$userAccount );
            $return['ask'] = 1;
            $return['message'] = '操作成功，结果如下';
            $return['result'][] = $result;
        }
        die(Zend_Json::encode($return));
    }
    
}