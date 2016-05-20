<?php
class Product_ItemEntryController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/item-entry/"; 
        $this->serviceClass = new Service_EbayAccountEntry();
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

            $account_details_entry_type = $this->getParam('account_details_entry_type','');
            $item_id = $this->getParam('item_id','');   
            $user_account = $this->getParam('user_account','');
            
            $condition['account_details_entry_type'] = $account_details_entry_type; 
            $condition['user_account'] = $user_account;

            $condition['item_id'] = $item_id;
           

            foreach($condition as $k=>$v){
                if(!is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) { 
                $rows = $this->serviceClass->getByCondition($condition, '*', $pageSize, $page);
                foreach($rows as $k=>$v){
                                       
                    $rows[$k] = $v;
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }

        $user_account_arr = Service_User::getPlatformUserAll('ebay');  
        
        $this->view->user_account_arr = $user_account_arr;
        $this->view->user_account_arr_json = Zend_Json::encode($user_account_arr);
        $sql = "SELECT DISTINCT account_details_entry_type FROM `ebay_account_entry`; ";
        $db = Common_Common::getAdapter();
        $account_details_entry_type = $db->fetchAll($sql);
        $this->view->account_details_entry_type = $account_details_entry_type;
        
        echo Ec::renderTpl($this->tplDirectory . "item_list.tpl", 'layout');
    }

    /**
     * 更新item
     */
    public function reloadAction(){
        $return = array(
                "state" => 0,
                "message" => "Request Err"
        );
    
        if($this->_request->isPost()){
            set_time_limit(0);
            $itemId = $this->getRequest()->getParam('item_id','');
            
            $itemId = preg_replace('/[^0-9]/', '', $itemId);
            $item = Service_SellerItem::getByField($itemId,'item_id');
            
            $result = Ebay_ItemEbayService::GetAccount($item['user_account'], $item['item_id']);
            $return['state'] = $result['ask'];
            $return['message'] = $result['message'];
            $itemStr = print_r($result['response'],true);
            $itemStr = str_replace("\n", '<br/>', $itemStr);
            $itemStr = str_replace(" ", '&nbsp;', $itemStr);
            $return['response_str'] = $itemStr;
        }
        die(Zend_Json::encode($return));
    }
    
}