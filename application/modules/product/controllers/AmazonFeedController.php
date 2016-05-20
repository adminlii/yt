<?php
class Product_AmazonFeedController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "product/views/amazon-feed/";
        $this->serviceClass = new Service_AmazonFeed();
    }

    public function listAction()
    {
        $user_account_arr = Service_User::getPlatformUserNew('do', 'amazon'); // 绑定店铺账号
        if($this->_request->isPost()){
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
            $FeedProcessingStatus = $this->getParam('FeedProcessingStatus', '');
            $FeedType = $this->getParam('FeedType', '');
            
            $condition['FeedProcessingStatus'] = $FeedProcessingStatus;
            $condition['FeedType'] = $FeedType;
            
            $condition['user_account'] = $user_account;
            $condition['user_account_arr'] = array_keys($user_account_arr);
            
            foreach($condition as $k => $v){
                if(! is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
            // print_r($condition);exit;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;
            
            if($count){
                $rows = $this->serviceClass->getByCondition($condition, '*', $pageSize, $page);
                $data = array();
                foreach($rows as $k => $v){                    
                    $v['user_account'] = isset($user_account_arr[$v['user_account']]) ? $user_account_arr[$v['user_account']]['platform_user_name'] : $v['user_account'];
                    
                    $feedResult = Service_AmazonFeedResult::getByField($v['FeedSubmissionId'], 'FeedSubmissionId');
                    $v['download'] = 'NO';
                    $v['MessagesProcessed'] = '--';
                    $v['MessagesSuccessful'] = '--';
                    $v['MessagesWithError'] = '--';
                    $v['MessagesWithWarning'] = '--';
                    if($feedResult){
                        $v['MessagesProcessed'] = $feedResult['MessagesProcessed'];
                        $v['MessagesSuccessful'] = $feedResult['MessagesSuccessful'];
                        $v['MessagesWithError'] = $feedResult['MessagesWithError'];
                        $v['MessagesWithWarning'] = $feedResult['MessagesWithWarning'];
                        $v['download'] = 'YES';
                    }
                    $rows[$k] = $v;
                }
                // print_r($rows);exit;
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        
        // $user_account_arr = Common_Common::getPlatformUser();
        $this->view->user_account_arr = $user_account_arr;
        $this->view->user_account_arr_json = Zend_Json::encode($user_account_arr);

        $db = Common_Common::getAdapter();
        $FeedTypeArr = $db->fetchAll("select distinct FeedType from amazon_feed");
        $FeedProcessingStatusArr = $db->fetchAll("select distinct FeedProcessingStatus from amazon_feed");

        $this->view->FeedTypeArr = $FeedTypeArr;
        $this->view->FeedProcessingStatusArr = $FeedProcessingStatusArr;
        
        echo Ec::renderTpl($this->tplDirectory . "feed_list.tpl", 'layout');
    }
    /**
     * 请求内容
     */
    public function viewFeedContentAction()
    {
        $feed_id = $this->getParam('feed_id', '');
        $feed = Service_AmazonFeed::getByField($feed_id, 'FeedSubmissionId');
        
        if($feed && $feed['FeedContent']){
            header("Content-type: text/xml; charset=utf-8");
            echo $feed['FeedContent'];
        }else{
            echo 'No Data';
        }
        
        exit();
    }

    /**
     * 报告结果
     */
    public function viewFeedResultAction()
    {
        $feed_id = $this->getParam('feed_id', '');
        $feed = Service_AmazonFeedResult::getByField($feed_id, 'FeedSubmissionId');
        if($feed && $feed['data']){
            header("Content-type: text/xml; charset=utf-8");
            echo $feed['data'];
        }else{
            echo 'No Data';
        }
        
        exit();
    }
    /**
     * 手动提交
     * @throws Exception
     */
    public function submitFeedAction(){
        try{
            $user_account = $this->getParam('acc', '---');
            $type = $this->getParam('type', '_POST_PRODUCT_PRICING_DATA_');
            $con = array(
                'company_code' => Common_Company::getCompanyCode(),
                'user_account' => $user_account,
                'platform' => "amazon"
            );
            $resultPlatformUser = Service_PlatformUser::getByCondition($con);
            if($resultPlatformUser){
                $resultPlatformUser = array_pop($resultPlatformUser);
            }
            if(empty($resultPlatformUser)){
                throw new Exception(print_r($con, true) . 'Not Exist');
            }
            
            // print_r($resultPlatformUser);exit;
            $token_id = $resultPlatformUser["user_token_id"];
            $token = $resultPlatformUser["user_token"];
            $saller_id = $resultPlatformUser["seller_id"];
            $site = $resultPlatformUser["site"];
            
            $company_code = $resultPlatformUser['company_code'];
            $user_account = $resultPlatformUser['user_account'];
            switch(strtoupper($type)){
                case '_POST_PRODUCT_PRICING_DATA_':
                    $service = new Amazon_PriceService($token_id, $token, $saller_id, $site);
                    $service->setCompanyCode($company_code);
                    $service->setUserAccount($user_account);
                    $service->setFeedType($type);                    
                    $rs = $service->submitFeed();
                    if($rs['ask']==1){
                        $priceListing = $service->getPriceListing();
                        foreach($priceListing as $v){
                            Service_AmazonMerchantListingPriceSet::update(array('sync_status'=>'1'), $v['id'],'id');
                        }
                    }
                    break;
                case '_POST_INVENTORY_AVAILABILITY_DATA_':
                    $service = new Amazon_InventoryService($token_id, $token, $saller_id, $site);
                    $service->setCompanyCode($company_code);
                    $service->setUserAccount($user_account);
                    $service->setFeedType($type);                    
                    $rs = $service->submitFeed();
                    if($rs['ask']==1){
                        $inventoryList = $service->getInventoryListing();
                        foreach($inventoryList as $v){
                            Service_SellerItemSupplyQty::update(array('sync_status'=>'1'), $v['id'],'id');
                        }
                    }
                    break;
                default:
                    throw new Exception('Type Exception-->' . $type);
            }
            
            $rs = print_r($rs, true);
            $rs = preg_replace('/\n+/', '<br/>', $rs);
            $rs = preg_replace('/\s/', '&nbsp;', $rs);
            echo $rs;
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * 手动获取结果
     * @throws Exception
     */
    public function getFeedResultAction(){
        try{            
            $feed_id = $this->getParam('feed_id', '');
            $feed = Service_AmazonFeed::getByField($feed_id, 'FeedSubmissionId');
            
            $con = array(
                'user_account' => $feed['user_account'],
                'platform' => "amazon"
            );
            $resultPlatformUser = Service_PlatformUser::getByCondition($con);
            if($resultPlatformUser){
                $resultPlatformUser = array_pop($resultPlatformUser);
            }
            if(empty($resultPlatformUser)){
                throw new Exception(print_r($con, true) . 'Not Exist');
            }
//             print_r($resultPlatformUser);exit;
            $token_id = $resultPlatformUser["user_token_id"];
            $token = $resultPlatformUser["user_token"];
            $saller_id = $resultPlatformUser["seller_id"];
            $site = $resultPlatformUser["site"];
            
            $company_code = $resultPlatformUser['company_code'];
            $user_account = $resultPlatformUser['user_account'];
            $service = new Amazon_PriceService($token_id, $token, $saller_id, $site);
            $service->setCompanyCode($company_code);
            $service->setUserAccount($user_account);
            // $service->submitFeed();
            $rs = $service->getFeedSubmissionList('', '', array(
                $feed_id
            ));

            $feed = Service_AmazonFeed::getByField($feed_id, 'FeedSubmissionId');
            $rs1 = '';
            if($feed['FeedProcessingStatus']=='_DONE_'){
                $rs1 = $service->getFeedSubmissionResult($feed_id);
                unset($rs1['response']['data']);
            }            
            $rs = print_r($rs,true).print_r($rs1,true);
            $rs = preg_replace('/\n+/', '<br/>', $rs);
            $rs = preg_replace('/\s/', '&nbsp;', $rs);
            echo $rs;
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    //测试示例
    public function anaAction(){
        Amazon_FeedService::analyzeXml(11015448890);
        
    }
    //测试示例
    public function demoAction(){
        Amazon_PriceServiceProcess::demo();
    }
}