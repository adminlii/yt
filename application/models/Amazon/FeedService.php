<?php
class Amazon_FeedService extends Amazon_Service
{

    protected $_nextToken = '';

    protected $_hasNext = false;

    protected $_xmlContent = '';
    
    protected $_feedInfoArr = array();

    protected $_feedType = '_POST_PRODUCT_PRICING_DATA_';

    protected $_FeedProcessingStatusListArr = array(
        '_AWAITING_ASYNCHRONOUS_REPLY_',
        '_CANCELLED_',
        '_DONE_',
        '_IN_PROGRESS_',
        '_IN_SAFETY_NET_',
        '_SUBMITTED_',
        '_UNCONFIRMED_'
    );


    public function setFeedType($feedType)
    {
        $this->_feedType = $feedType;
    }
    /**
     * 该方法子类需要重写
     * @return string
     */
    public function getXml(){

        $data = array();
        $feed = $this->getXmlContent($data);
        return $feed;
    }
    /**
     * 提交数据
     */
    public function submitFeed()
    {
        try{
            $feed = $this->getXml();
//                     header('Content-Type:text/xml');
//                     echo $feed;exit;
            $this->_xmlContent = $feed;
            
            $feedHandle = @fopen('php://temp', 'rw+');
            fwrite($feedHandle, $feed);
            rewind($feedHandle);
            $parameters = array(
                    'Merchant' => $this->_tokenConfig['MERCHANT_ID'],
                    'MarketplaceIdList' => array(
                            "Id" => array(
                                    $this->_MarketplaceId
                            )
                    ),
                    'FeedType' => $this->_feedType,
                    'FeedContent' => $feedHandle,
                    'PurgeAndReplace' => false,
                    'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true))
            );
            
            rewind($feedHandle);
            // print_r($parameters);exit;
            $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
            
            $service = $this->_service;
            $response = $this->invokeSubmitFeed($service, $request);
            
            @fclose($feedHandle);
            
            return $response;
        }catch (Exception $e){
            $rs = array(
                    'ask' => 0,
                    //'没有需要更新的数据'
                    'message' => $e->getMessage()
            );
            if($e->getCode() == '999'){
                $rs['ask'] = 1;
            } 
            return $rs;           
        }
    }

    public function invokeSubmitFeed(MarketplaceWebService_Client $service, $request)
    {
        $rs = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            Amazon_Service::log("数据开始提交->" . $this->_user_account);
            $response = $service->submitFeed($request);
            $this->_responseMetadata($response);
            Amazon_Service::log("SubmitFeedResponse=============================================================================");
            if($response->isSetSubmitFeedResult()){
                Amazon_Service::log("SubmitFeedResult");
                $submitFeedResult = $response->getSubmitFeedResult();
                if($submitFeedResult->isSetFeedSubmissionInfo()){
                    $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                    $this->_saveFeed($feedSubmissionInfo);                    
                    $rs['ask'] = 1;
                    $rs['message'] = 'Success';
                }
            }
        }catch(MarketplaceWebService_Exception $ex){
            $exception = $this->logException($ex);
            $rs['exception'] = $exception;
        }
        $rs['xml'] = htmlspecialchars($this->_xmlContent);
        return $rs;
    }

    /**
     * 数据保存
     * 
     * @param unknown_type $submitFeedResult            
     */
    private function _saveFeed($feedSubmissionInfo)
    {
        $feedInfoRow = array();
        Amazon_Service::log("FeedSubmissionInfo======================");
        if($feedSubmissionInfo->isSetFeedSubmissionId()){
            $feedInfoRow['FeedSubmissionId'] = $feedSubmissionInfo->getFeedSubmissionId();
            Amazon_Service::log("FeedSubmissionId:" . $feedSubmissionInfo->getFeedSubmissionId());
        }
        if($feedSubmissionInfo->isSetFeedType()){
            $feedInfoRow['FeedType'] = $feedSubmissionInfo->getFeedType();
            Amazon_Service::log("FeedType:" . $feedSubmissionInfo->getFeedType());
        }
        if($feedSubmissionInfo->isSetSubmittedDate()){
            $feedInfoRow['SubmittedDate'] = $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT);
            Amazon_Service::log("SubmittedDate:" . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT));
        }
        if($feedSubmissionInfo->isSetFeedProcessingStatus()){
            $feedInfoRow['FeedProcessingStatus'] = $feedSubmissionInfo->getFeedProcessingStatus();
            Amazon_Service::log("FeedProcessingStatus:" . $feedSubmissionInfo->getFeedProcessingStatus());
        }
        
        $feedInfoRow['RequestId'] = $this->_RequestId;
        $feedInfoRow['company_code'] = $this->_company_code;
        $feedInfoRow['user_account'] = $this->_user_account;
        $feedInfoRow['create_time'] = date('Y-m-d H:i:s');
        
        $feedInfoRow = Common_ApiProcess::nullToEmptyString($feedInfoRow);
        $this->_feedInfoArr[] = $feedInfoRow;
        $exist = Service_AmazonFeed::getByField($feedInfoRow['FeedSubmissionId'], 'FeedSubmissionId');
        if($exist){
            Service_AmazonFeed::update($feedInfoRow, $feedInfoRow['FeedSubmissionId'], 'FeedSubmissionId');
        }else{
            $feedInfoRow['FeedContent'] = $this->_xmlContent; 
            Service_AmazonFeed::add($feedInfoRow);
        }
    }

    public function getFeedSubmissionList($fromDate = '', $toDate = '', $FeedSubmissionIdListArr = array(), $FeedProcessingStatusListArr = array())
    {
        $this->_feedInfoArr = array();
        $parameters = array(
            'MaxCount' => 100,            
            'FeedTypeList' => array(
                "Type" => array(
                    $this->_feedType
                )
            ),
            'Merchant' => $this->_tokenConfig['MERCHANT_ID']
        );
        
        if($fromDate){
            $fromDate = new DateTime(date('Y-m-d\TH:i:s\Z', strtotime($fromDate)), new DateTimeZone('UTC'));
            $parameters['SubmittedFromDate'] = $fromDate;
        }
        if($toDate){
            $toDate = new DateTime(date('Y-m-d\TH:i:s\Z', strtotime($toDate)), new DateTimeZone('UTC'));
            $parameters['SubmittedToDate'] = $toDate;
        }
//             print_r($FeedSubmissionIdListArr);exit;
        if(is_array($FeedSubmissionIdListArr) && ! empty($FeedSubmissionIdListArr)){
            foreach($FeedSubmissionIdListArr as $v){
                if(!is_string($v)){
                    throw new Exception('FeedSubmissionIdListArr格式不正确' . __LINE__);
                }
            }
            $parameters['FeedSubmissionIdList']['Id'] = $FeedSubmissionIdListArr;
            unset($parameters['SubmittedFromDate']);
            unset($parameters['SubmittedToDate']);
        }else{
            $FeedSubmissionIdListArr = array();
            $con = array(
                    'user_account' => $this->_user_account,
                    'company_code' => $this->_company_code,
                    'FeedType' => $this->_feedType
            );
            $feedList = Service_AmazonFeed::getByCondition($con);

//             print_r($feedList);exit;
            foreach($feedList as $k=>$v){
                if($v['FeedProcessingStatus']!='_DONE_'){
                    $FeedSubmissionIdListArr[] = $v['FeedSubmissionId'];
                }else{
                    unset($feedList[$k]);
                }
            }
            if(empty($FeedSubmissionIdListArr)){
                $rs['ask'] = 1;
                $rs['message'] = 'Success'.'没有需要下载的报告' . __LINE__;
                return $rs;
                throw new Exception('没有需要下载的报告' . __LINE__);
            }
            $parameters['FeedSubmissionIdList']['Id'] = $FeedSubmissionIdListArr;
            unset($parameters['SubmittedFromDate']);
            unset($parameters['SubmittedToDate']);
            
        }
//         print_r($FeedSubmissionIdListArr);exit;
        if(is_array($FeedProcessingStatusListArr) && ! empty($FeedProcessingStatusListArr)){
            foreach($FeedProcessingStatusListArr as $v){
                if(!is_string($v)){
                    throw new Exception('FeedProcessingStatusListArr格式不正确' . __LINE__);
                }
            }
            $diff = array_diff($FeedProcessingStatusListArr, $this->_FeedProcessingStatusListArr);
            if($diff){
                throw new Exception('FeedProcessingStatusListArr格式不正确' . __LINE__);
            }
            $parameters['FeedProcessingStatusList']['Status'] = $FeedProcessingStatusListArr;
        }
        $request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest($parameters); 
        $rs = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            // 获取数据
            $this->invokeGetFeedSubmissionList($this->_service, $request);
            $rs['ask'] = 1;
            $rs['message'] = 'Success';
        }catch(MarketplaceWebService_Exception $ex){
            // 记录日志
            $exception = $this->logException($ex);
            $rs['exception'] = $exception;
        }
        $rs['feedInfoArr'] = $this->_feedInfoArr;
        return $rs;
    }

    public function invokeGetFeedSubmissionList(MarketplaceWebService_Client $service, $request)
    {
        try{
            $response = $service->getFeedSubmissionList($request);
            Amazon_Service::log("Response=============================================================================");
            $this->_responseMetadata($response);
            
            Amazon_Service::log("GetFeedSubmissionListResponse");
            if($response->isSetGetFeedSubmissionListResult()){
                Amazon_Service::log("GetFeedSubmissionListResult");
                $getFeedSubmissionListResult = $response->getGetFeedSubmissionListResult();
                if($getFeedSubmissionListResult->isSetNextToken()){
                    $this->_nextToken = $getFeedSubmissionListResult->getNextToken();
                    Amazon_Service::log("NextToken:" . $getFeedSubmissionListResult->getNextToken());
                }
                if($getFeedSubmissionListResult->isSetHasNext()){
                    $this->_hasNext = $getFeedSubmissionListResult->getHasNext();
                    Amazon_Service::log("HasNext:" . $getFeedSubmissionListResult->getHasNext());
                }
                $feedSubmissionInfoList = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
                foreach($feedSubmissionInfoList as $feedSubmissionInfo){
                    $this->_saveFeed($feedSubmissionInfo);
                }
            }
        }catch(MarketplaceWebService_Exception $ex){
            $this->logException($ex);
        }
        
        $this->getFeedSubmissionListNextData($service);
    }

    /**
     * 获取下页的数据
     * @param MarketplaceWebService_Client $service
     */
    public function getFeedSubmissionListNextData(MarketplaceWebService_Client $service){
        if($this->_hasNext && $this->_nextToken){
            $request = new MarketplaceWebService_Model_GetFeedSubmissionListByNextTokenRequest();
            $request->setMerchant($this->_tokenConfig['MERCHANT_ID']);
            $request->setNextToken($this->_nextToken);
            try{
                $this->invokeGetFeedSubmissionListByNextToken($service, $request);
            }catch(MarketplaceWebService_Exception $ex){
                // 记录日志
                $this->logException($ex);
                $this->invokeGetFeedSubmissionListByNextToken($service, $request);
            }
        }
    }
    

    public function invokeGetFeedSubmissionListByNextToken(MarketplaceWebService_Interface $service, $request)
    {
        $this->_hasNext = false;
        $this->_nextToken = '';
        
        $response = $service->getFeedSubmissionListByNextToken($request);
        Amazon_Service::log("Service Response=============================================================================");
        
        $this->_responseMetadata($response);
        
        Amazon_Service::log("GetFeedSubmissionListByNextTokenResponse");
        if($response->isSetGetFeedSubmissionListByNextTokenResult()){
            Amazon_Service::log("GetFeedSubmissionListByNextTokenResult");
            $getFeedSubmissionListByNextTokenResult = $response->getGetFeedSubmissionListByNextTokenResult();
            if($getFeedSubmissionListByNextTokenResult->isSetNextToken()){
                $this->_nextToken = $getFeedSubmissionListByNextTokenResult->getNextToken(); 
                Amazon_Service::log("NextToken:" . $getFeedSubmissionListByNextTokenResult->getNextToken());
            }
            if($getFeedSubmissionListByNextTokenResult->isSetHasNext()){
                $this->_hasNext = $getFeedSubmissionListByNextTokenResult->getHasNext(); 
                Amazon_Service::log("HasNext:" . $getFeedSubmissionListByNextTokenResult->getHasNext());
            }
            $feedSubmissionInfoList = $getFeedSubmissionListByNextTokenResult->getFeedSubmissionInfoList();
            foreach($feedSubmissionInfoList as $feedSubmissionInfo){
                $this->_saveFeed($feedSubmissionInfo);
            }
        }
        
        $this->getFeedSubmissionListNextData($service);
    }

    /**
     * 获取未下载的报告
     */
    public function getAllFeedSubmissionNotLoadNew(){
        $db = Common_Common::getAdapter();
        while(true){
            $sql = "select TYPE from amazon_feed a left join amazon_feed_result b on a.FeedSubmissionId = b.FeedSubmissionId where a.FeedType='{$this->_feedType}' and a.FeedProcessingStatus='_DONE_' and a.company_code='{$this->_company_code}' and a.user_account='{$this->_user_account}' and b.FeedSubmissionId is null";
            $rowSql = str_replace('TYPE', 'a.*', $sql);
            $row = $db->fetchRow($rowSql);
            $countSql = str_replace('TYPE', 'count(*)', $sql);
            $count = $db->fetchOne($countSql);
            Common_ApiProcess::log("还有{$count}条数据待下载-->".$this->_user_account);
            if($row){
                $FeedSubmissionId = $row['FeedSubmissionId'];
                if($FeedSubmissionId){
                    $exist = Service_AmazonFeedResult::getByField($FeedSubmissionId, 'FeedSubmissionId');
                    if(! $exist){
                        $this->getFeedSubmissionResult($FeedSubmissionId);
                    }
                }
            }else{
                break;
            }
    
        }
    
    }
    /**
     * 下载报告
     * @param unknown_type $feedSubmissionId
     */
    public function getFeedSubmissionResult($feedSubmissionId){
        $rs = array(
            'ask' => 0,
            'message' => 'Fail.'
        ); 
        try{
            $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
            $request->setMerchant($this->_tokenConfig['MERCHANT_ID']);
            $request->setFeedSubmissionId($feedSubmissionId);            
            
            $request->setFeedSubmissionResult(@fopen('php://memory', 'rw+'));
            $response = $this->invokeGetFeedSubmissionResult($this->_service, $request);
            $rs['ask'] = 1;
            $rs['message'] = 'Success';
            $rs['response'] = $response;
        }catch(MarketplaceWebService_Exception $ex){
            // 记录日志
            $exception = $this->logException($ex);
            $rs['exception'] = $exception;
            // 暂停n秒
            sleep(5);
        }
        return $rs;
    }
    
    /**
     * 保存结果
     * @param MarketplaceWebService_Interface $service
     * @param unknown_type $request
     */
    public function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request)
    {
        $response = $service->getFeedSubmissionResult($request);
        
        Amazon_Service::log("Service Response=============================================================================");
        $this->_responseMetadata($response);
        
        Amazon_Service::log("GetFeedSubmissionResultResponse");
        if($response->isSetGetFeedSubmissionResultResult()){
            $getFeedSubmissionResultResult = $response->getGetFeedSubmissionResultResult();
            Amazon_Service::log("GetFeedSubmissionResult");
            
            if($getFeedSubmissionResultResult->isSetContentMd5()){
                Amazon_Service::log("ContentMd5" . $getFeedSubmissionResultResult->getContentMd5() . "");
            }
            
        }
        $FeedSubmissionId =  $request->getFeedSubmissionId() ;
        $content = stream_get_contents($request->getFeedSubmissionResult());
        file_put_contents(APPLICATION_PATH . '/../data/log/___amzone_' . $request->getFeedSubmissionId() . $this->_feedType . '.xml', print_r($content, true));
        $row = array(
            'FeedSubmissionId' => $request->getFeedSubmissionId(),
            'data' => $content,
        );
        $exist = Service_AmazonFeedResult::getByField($request->getFeedSubmissionId(),'FeedSubmissionId');
        if($exist){
            $row['update_time'] = now();
            Service_AmazonFeedResult::update($row, $exist['id'],'id');
        }else{
            $row['add_time'] = now();
            $row['update_time'] = now();
            Service_AmazonFeedResult::add($row);            
        }        
        $this->analyzeXml($FeedSubmissionId,true);
        $row['xml_html'] = htmlspecialchars($content);
        return $row;
    }
    /**
     * 解析XML,判断请求结果
     * @param unknown_type $FeedSubmissionId
     */
    public static function analyzeXml($FeedSubmissionId,$updateData=false){
        $req = Service_AmazonFeed::getByField($FeedSubmissionId, 'FeedSubmissionId');
        $res = Service_AmazonFeedResult::getByField($FeedSubmissionId, 'FeedSubmissionId');
        if(!$req){
            return false;
        }
        if(!$res){
            return false;
        }
        $reqXml = $req['FeedContent'];
        $reqArr = XML_unserialize($reqXml);
        if(empty($reqArr)){
            return false;
        }
        
        $ReqMessages = $reqArr['AmazonEnvelope']['Message'];
        $ReqMessageArr = array();
        if(isset($ReqMessages[0])){
            $ReqMessageArr = $ReqMessages;
        }else{
            $ReqMessageArr[] = $ReqMessages;
        }
//         print_r($ReqMessageArr);exit;
        
        $resXml = $res['data'];
        $resArr = XML_unserialize($resXml);
        if(empty($resArr)){
            return false;
        }
        //判断结果
        $ProcessingSummary = $resArr['AmazonEnvelope']['Message']['ProcessingReport']['ProcessingSummary'];
        if(!isset($ProcessingSummary)){
            Ec::showError($resXml,'___FeedResult__');
        }else{
            if($ProcessingSummary['MessagesProcessed']==$ProcessingSummary['MessagesSuccessful']){
        
            }            
            $updateRow = array(
                'MessagesProcessed' => $ProcessingSummary['MessagesProcessed'],
                'MessagesSuccessful' => $ProcessingSummary['MessagesSuccessful'],
                'MessagesWithError' => $ProcessingSummary['MessagesWithError'],
                'MessagesWithWarning' => $ProcessingSummary['MessagesWithWarning']
            );
            Service_AmazonFeedResult::update($updateRow, $FeedSubmissionId, 'FeedSubmissionId');
            $res = array_merge($res,$updateRow);    
        }

        // print_r($reqArr);
        
        // $resXml = $res['data'];
        // $resArr = XML_unserialize($resXml);
        // print_r($resArr);
        // exit();
        // header('Content-Type:text/xml');
        // echo $reqXml;
        // exit();
        
        switch($req['FeedType']){
            case '_POST_PRODUCT_PRICING_DATA_':
                //加入更新价格的任务
                $db = Common_Common::getAdapter();
                $table = Amazon_Common::cron_load_amazon_get_my_price_for_sku();
                foreach($ReqMessageArr as $v){
                    $sku = $v['Price']['SKU'];
                    $row = array(
                        'company_code' => $this->_company_code,
                        'user_account' => $this->_user_account,
                        'seller_sku' => $sku
                    );
                    $sql = "select * from {$table} where company_code='{$this->_company_code}' and user_account='{$this->_user_account}' and seller_sku='{$sku}'";
                    $exist = $db->fetchRow($sql);
                    if(! $exist){
                        $db->insert($table, $row);
                    }
                }
                
                break;
            
            case '_POST_INVENTORY_AVAILABILITY_DATA_':
                //库存更新结果======================
                if(isset($ProcessingSummary)){
                    //全部成功,更新补货状态
                    if($ProcessingSummary['MessagesProcessed']==$ProcessingSummary['MessagesSuccessful']){
                        foreach($ReqMessageArr as $ReqMessage){
                            $con = array(
                                    'company_code' => $req['company_code'],
                                    'user_account' => $req['user_account'],
                                    'platform' => 'amazon',
                                    'sku'=>$ReqMessage['Inventory']['SKU']
                            )
                            ;
                            $supQtyRow = Service_SellerItemSupplyQty::getByCondition($con);
                            if($supQtyRow){
                                if(count($supQtyRow)>1){
                                    Ec::showError(print_r($supQtyRow,true),'_POST_INVENTORY_AVAILABILITY_DATA_ERR_');
                                }
                                $supQtyRow = array_pop($supQtyRow);
                                
                                $updateRow = array('sync_status'=>'1','sync_time'=>now());
                                Service_SellerItemSupplyQty::update($updateRow, $supQtyRow['id'],'id');
                            }                            
                        }
                    }else{
                        //暂时无差别==========================
                        foreach($ReqMessageArr as $ReqMessage){
                            $con = array(
                                    'company_code' => $req['company_code'],
                                    'user_account' => $req['user_account'],
                                    'platform' => 'amazon',
                                    'sku'=>$ReqMessage['Inventory']['SKU']
                            )
                            ;
                            $supQtyRow = Service_SellerItemSupplyQty::getByCondition($con);
                            if($supQtyRow){
                                if(count($supQtyRow)>1){
                                    Ec::showError(print_r($supQtyRow,true),'_POST_INVENTORY_AVAILABILITY_DATA_ERR_');
                                }
                                $supQtyRow = array_pop($supQtyRow);
                        
                                $updateRow = array('sync_status'=>'1','sync_time'=>now());
                                Service_SellerItemSupplyQty::update($updateRow, $supQtyRow['id'],'id');
                            }
                        }
                    }
                }
                break;
            default:
        }
    }
	
    
}