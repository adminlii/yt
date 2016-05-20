<?php
// SELECT * from amazon_merchant_listing GROUP BY product_id,seller_sku HAVING COUNT(*)>1;
// SELECT * FROM `amazon_merchant_listing` group by user_account,seller_sku HAVING COUNT(*)>1;
require_once 'XmlHandle.php';
class Amazon_ProductService extends Amazon_Service
{

    protected $_nextToken = '';

    protected $_hasNext = false;
    
    protected $_err = array();
    
    protected $_success = array();

    protected $_responseArr = array();
    
    protected $_reportRequestInfoArr = array();
    
    protected $_reportType='_GET_MERCHANT_LISTINGS_DATA_';

    public function setReportType($reportType){
        $this->_reportType = $reportType;
    }

    /**
     * 提交上传数据
     *
     * @param unknown_type $FeedContent
     */
    public function requestReport()
    {
        $service = $this->_service;
               
        $parameters = array(
                'Merchant' => $this->_tokenConfig['MERCHANT_ID'],
                'MarketplaceIdList' => array(
                        "Id" => array(
                                $this->_MarketplaceId,
                        )
                ),
                // 'Marketplace' =>$this->_MarketplaceId,,
                'ReportType' => $this->_reportType,
                'ReportOptions' => 'ShowSalesChannel=true'
        );
    
        $request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
    
        $response = $this->invokeRequestReport($service, $request);
        return $response;
    }

    public function invokeRequestReport(MarketplaceWebService_Client $service, $request)
    {
        $rs = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $response = $service->requestReport($request);
            Amazon_Service::log("Service Response=============================================================================");
            $this->_responseMetadata($response);
            // Ec::showError(print_r($response,true),'__amazon_');
            // Amazon_Service::log(print_r($response,true)); 
            Amazon_Service::log("RequestReportResponse");
            if($response->isSetRequestReportResult()){
                Amazon_Service::log("RequestReportResult");
                $requestReportResult = $response->getRequestReportResult();
                
                if($requestReportResult->isSetReportRequestInfo()){
                    $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                    $reportRequestInfoRow = $this->_saveReportRequestInfo($reportRequestInfo);
                    $rs['ask'] = 1;
                    $rs['message'] = 'Success';
                    $rs['data'] = $reportRequestInfoRow;
                }
            }
        }catch(MarketplaceWebService_Exception $ex){
            // 记录日志
            $exception = $this->logException($ex);
            $rs['exception'] = $exception;
        }
        return $rs;
    }
    
    public function getReportRequestList($start = '', $end = '', $reportRequestIdArr = array(), $ReportProcessingStatusList = array())
    {
        $rs = array(
                'ask' => 0,
                'message' => 'Fail.'
        );
        try{

            $service  = $this->_service;
            $parameters = array(
                    'MaxCount' => 100,
                    //             'MaxCount' => 5,
                    // 'ReportRequestIdList'=>array(
                    // "Id" => array(
                    // $reportRequestId
                    // )
                    // ),
                    'ReportTypeList' => array(
                            "Type" => array(
                                    $this->_reportType
                            )
                    ),
                    // 'ReportProcessingStatusList'=> array(
                    // "Status" => array(
                    // '_DONE_',
                    // '_SUBMITTED_',
                    // '_IN_PROGRESS_',
                    // '_CANCELLED_',
                    // '_DONE_NO_DATA_'
                    // )
                    // ),
                    // 'RequestedFromDate'=>$RequestedFromDate,
                    // 'RequestedToDate'=>$RequestedToDate,
                    'Merchant' => $this->_tokenConfig['MERCHANT_ID']
            );
            if($start){
                $RequestedFromDate = new DateTime(date('Y-m-d\TH:i:s\Z', strtotime($start)), new DateTimeZone('UTC'));
                $parameters['RequestedFromDate'] = $RequestedFromDate;
            }
            if($end){
                $RequestedToDate = new DateTime(date('Y-m-d\TH:i:s\Z', strtotime($end)), new DateTimeZone('UTC'));
                $parameters['RequestedToDate'] = $RequestedToDate;
            }
            if(is_array($reportRequestIdArr)&&!empty($reportRequestIdArr)){
                foreach($reportRequestIdArr as $v){
                    if(!is_string($v)){
                        throw new Exception('reportRequestIdArr格式不正确' . __LINE__);
                    }
                }
                $parameters['ReportRequestIdList']['Id'] = $reportRequestIdArr;
                //取消时间限制
                unset($parameters['RequestedFromDate']);
                unset($parameters['RequestedToDate']);
            }else{
                $con = array(
                    'user_account' => $this->_user_account,
                    'company_code' => $this->_company_code,
                    'ReportType' => $this->_reportType
                );
                $reportRequestList = Service_AmazonReportRequestInfo::getByCondition($con);
                
//                 print_r($con);exit;
                foreach($reportRequestList as $k=>$v){
                    if($v['ReportProcessingStatus']=='_SUBMITTED_'||$v['ReportProcessingStatus']=='_IN_PROGRESS_'||empty($v['ReportProcessingStatus'])){
                        $reportRequestIdArr[] = $v['ReportRequestId'];
                    }else{
                        unset($reportRequestList[$k]);
                    }                    
                }
                if(empty($reportRequestList)){
                    $rs['ask'] = 1;
                    $rs['message'] = 'Success'.'没有需要下载的报告' . __LINE__;
                    return $rs;
                    throw new Exception('没有需要下载的报告' . __LINE__);
                }
                $parameters['ReportRequestIdList']['Id'] = $reportRequestIdArr;
                //取消时间限制
                unset($parameters['RequestedFromDate']);
                unset($parameters['RequestedToDate']);
            }
            
            if(is_array($ReportProcessingStatusList)&&!empty($ReportProcessingStatusList)){
                foreach($ReportProcessingStatusList as $v){
                    if(!is_string($v)){
                        throw new Exception('ReportProcessingStatusList格式不正确' . __LINE__);
                    }
                }
                $diff = array_diff($ReportProcessingStatusList, $this->_ReportProcessingStatus);
                if($diff){
                    throw new Exception('ReportProcessingStatusList格式不正确' . __LINE__);
                }
                $parameters['ReportProcessingStatusList']['Status'] = $ReportProcessingStatusList;
            }
//                     print_r($parameters);exit;
            $request = new MarketplaceWebService_Model_GetReportRequestListRequest($parameters);
            
            //获取数据
            $this->invokeGetReportRequestList($service,$request);
            $rs['ask'] = 1;
            $rs['message'] = 'Success';
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $exception = $this->logException($ex);    
            $rs['exception'] = $exception;
        }
        $rs['reportRequestInfoArr'] = $this->_reportRequestInfoArr;
        return $rs;
    }

    public function invokeGetReportRequestList(MarketplaceWebService_Client $service, $request)
    {
        $response = $service->getReportRequestList($request);
        Amazon_Service::log("Service Response=============================================================================");
        $this->_responseMetadata($response);
        if($response->isSetGetReportRequestListResult()){
            $getReportRequestListResult = $response->getGetReportRequestListResult();
            
            if($getReportRequestListResult->isSetHasNext()){
                $this->_hasNext = $getReportRequestListResult->getHasNext(); 
                Amazon_Service::log("HasNext:" . $getReportRequestListResult->getHasNext() . "");
            }
            if($getReportRequestListResult->isSetNextToken()){
                $this->_nextToken = $getReportRequestListResult->getNextToken(); 
                Amazon_Service::log("NextToken:" . $getReportRequestListResult->getNextToken() . "");
            }
            $reportRequestInfoList = $getReportRequestListResult->getReportRequestInfoList();
            foreach($reportRequestInfoList as $reportRequestInfo){
                $this->_saveReportRequestInfo($reportRequestInfo);
            }
        }
        
        
        $this->getReportRequestListNextData($service);
    }

    /**
     * 获取下页的数据
     * @param MarketplaceWebService_Client $service
     */
    public function getReportRequestListNextData(MarketplaceWebService_Client $service){
        if($this->_hasNext && $this->_nextToken){
            $request = new MarketplaceWebService_Model_GetReportRequestListByNextTokenRequest();
            $request->setMerchant($this->_tokenConfig['MERCHANT_ID']);
            $request->setNextToken($this->_nextToken);
            try{
                $this->invokeGetReportRequestListByNextToken($service, $request);
            }catch(MarketplaceWebService_Exception $ex){
                // 记录日志
                $this->logException($ex);                
                $this->invokeGetReportRequestListByNextToken($service, $request);
            }
        }
    }
    
    public function invokeGetReportRequestListByNextToken(MarketplaceWebService_Client $service, $request)
    {
        $this->_hasNext = false;
        $this->_nextToken = '';

        $response = $service->getReportRequestListByNextToken($request);
        Amazon_Service::log("Service Response=============================================================================");
        $this->_responseMetadata($response);
         
        
        Amazon_Service::log("GetReportRequestListByNextTokenResponse");
        if($response->isSetGetReportRequestListByNextTokenResult()){
            Amazon_Service::log("GetReportRequestListByNextTokenResult");
            $getReportRequestListByNextTokenResult = $response->getGetReportRequestListByNextTokenResult();
            
            if($getReportRequestListByNextTokenResult->isSetHasNext()){
                $this->_hasNext = $getReportRequestListByNextTokenResult->getHasNext(); 
                Amazon_Service::log("HasNext:" . $getReportRequestListByNextTokenResult->getHasNext() . "");
            }
            if($getReportRequestListByNextTokenResult->isSetNextToken()){
                $this->_nextToken = $getReportRequestListByNextTokenResult->getNextToken(); 
                Amazon_Service::log("NextToken:" . $getReportRequestListByNextTokenResult->getNextToken() . "");
            }
            $reportRequestInfoList = $getReportRequestListByNextTokenResult->getReportRequestInfoList();
            foreach($reportRequestInfoList as $reportRequestInfo){
                $this->_saveReportRequestInfo($reportRequestInfo);
            }
            
        }
        $this->getReportRequestListNextData($service);
    }
    
    /**
     * 数据保存
     * @param unknown_type $reportRequestInfo
     */
    protected  function _saveReportRequestInfo($reportRequestInfo){

        $reportRequestInfoRow = array();
        Amazon_Service::log("ReportRequestInfo=======================");
        if($reportRequestInfo->isSetReportRequestId()){
            $reportRequestInfoRow['ReportRequestId'] = $reportRequestInfo->getReportRequestId();
            Amazon_Service::log("ReportRequestId: " . $reportRequestInfo->getReportRequestId() . "");
        }
        if($reportRequestInfo->isSetReportType()){
            $reportRequestInfoRow['ReportType'] = $reportRequestInfo->getReportType();
            Amazon_Service::log("ReportType: " . $reportRequestInfo->getReportType() . "");
        }
        if($reportRequestInfo->isSetStartDate()){
            $reportRequestInfoRow['StartDate'] = $reportRequestInfo->getStartDate()->format(DATE_FORMAT);
            Amazon_Service::log("StartDate: " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "");
        }
        if($reportRequestInfo->isSetEndDate()){
            $reportRequestInfoRow['EndDate'] = $reportRequestInfo->getEndDate()->format(DATE_FORMAT);
            Amazon_Service::log("EndDate: " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "");
        }
        if($reportRequestInfo->isSetSubmittedDate()){
            $reportRequestInfoRow['SubmittedDate'] = $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT);
            Amazon_Service::log("SubmittedDate: " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "");
        }
        if($reportRequestInfo->isSetReportProcessingStatus()){
            $reportRequestInfoRow['ReportProcessingStatus'] = $reportRequestInfo->getReportProcessingStatus();
            Amazon_Service::log("ReportProcessingStatus: " . $reportRequestInfo->getReportProcessingStatus() . "");
        }

        if($reportRequestInfo->isSetScheduled()){
            $reportRequestInfoRow['Scheduled'] = $reportRequestInfo->getScheduled();
            Amazon_Service::log("Scheduled: " . $reportRequestInfo->getScheduled() . "");
        }
        $reportRequestInfoRow['GeneratedReportId'] = $reportRequestInfo->GeneratedReportId;
        $reportRequestInfoRow['StartedProcessingDate'] = $reportRequestInfo->StartedProcessingDate;
        $reportRequestInfoRow['CompletedDate'] = $reportRequestInfo->CompletedDate;

//         print_r($reportRequestInfoRow);exit;
        $reportRequestInfoRow = Common_ApiProcess::nullToEmptyString($reportRequestInfoRow);

        $reportRequestInfoRow['company_code'] = $this->_company_code;
        $reportRequestInfoRow['user_account'] = $this->_user_account;
        $exist = Service_AmazonReportRequestInfo::getByField($reportRequestInfoRow['ReportRequestId'],'ReportRequestId');
        if($exist){
            Service_AmazonReportRequestInfo::update($reportRequestInfoRow,$exist['id'],'id');
        }else{
            Service_AmazonReportRequestInfo::add($reportRequestInfoRow);            
        }
        $this->_reportRequestInfoArr[] = $reportRequestInfoRow;
        return $reportRequestInfoRow;
    }
    

    /**
     * 下载已经完成且未下载过的报告
     */
    public function getAllReportNotLoad(){
        $db = Common_Common::getAdapter();
        $sql = "select * from amazon_report_request_info where ReportType='{$this->_reportType}' and ReportProcessingStatus='_DONE_' and company_code='{$this->_company_code}' and user_account='{$this->_user_account}'";
        $rows = $db->fetchAll($sql);
        foreach($rows as $row){
            $reportId = $row['GeneratedReportId'];
            if($reportId){
                $exist = Service_AmazonReportData::getByField($reportId,'ReportId');
                if(!$exist){
                    $this->getReport($reportId);
                }
            }
        }
    }

    public function getAllReportNotLoadNew(){
        $db = Common_Common::getAdapter();
        while(true){
            $sql = "select TYPE from amazon_report_request_info a left join amazon_report_data b on a.GeneratedReportId = b.ReportId where a.ReportType='{$this->_reportType}' and a.ReportProcessingStatus='_DONE_' and a.company_code='{$this->_company_code}' and a.user_account='{$this->_user_account}' and b.ReportId is null";
            $rowSql = str_replace('TYPE', 'a.*', $sql);
            $row = $db->fetchRow($rowSql);
            $countSql = str_replace('TYPE', 'count(*)', $sql);
            $count = $db->fetchOne($countSql);
            Common_ApiProcess::log("还有{$count}条数据待下载-->".$this->_user_account);
            if($row){
                $reportId = $row['GeneratedReportId'];
                if($reportId){
                    $exist = Service_AmazonReportData::getByField($reportId, 'ReportId');
                    if(! $exist){
                        $this->getReport($reportId);
                    }
                }  
            }else{
                break;
            }
            
        }
        
    }
    public function getReport($reportId){
        $rs = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        $service = $this->_service;
        try{
            Common_ApiProcess::log('报告ID' . $reportId);
            $request = new MarketplaceWebService_Model_GetReportRequest();
            $request->setMerchant($this->_tokenConfig['MERCHANT_ID']);
            $request->setReport(@fopen('php://memory', 'rw+'));
            $request->setReportId($reportId);
            // 获取数据
            $data = $this->invokeGetReport($service, $request);
            $rs['ask'] = 1;
            $rs['message'] = 'Success';
            $rs['data'] = $data;
        }catch(MarketplaceWebService_Exception $ex){
            // 记录日志
            $exception = $this->logException($ex);
            // 暂停n秒
            sleep(5);
            $rs['exception'] = $exception;
        }
        return $rs;
    }

    public function invokeGetReport(MarketplaceWebService_Client $service, $request)
    {
        $response = $service->getReport($request);
        $this->_responseMetadata($response);
        Amazon_Service::log("Service Response=============================================================================");
        
        Amazon_Service::log("GetReportResponse");
        if($response->isSetGetReportResult()){
            $getReportResult = $response->getGetReportResult();
            Amazon_Service::log("GetReport");
            if($getReportResult->isSetContentMd5()){
                Amazon_Service::log("ContentMd5:" . $getReportResult->getContentMd5() . "");
            }
        }
        
        $text = stream_get_contents($request->getReport());
        // Ec::showError($content,'__report');
        $ReportId = $request->getReportId();
        $this->_saveReport($ReportId, $text);
//         file_put_contents(APPLICATION_PATH . '/../data/log/___amzone_' . $ReportId .$this->_reportType. '.txt', print_r($text, true));
        // exit;
        // Amazon_Service::log("Report Contents:".stream_get_contents($request->getReport()) . "");
        
        Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "");
        
        return $this->_formatText($text);
    }
    /**
     * 保存报告
     * @param unknown_type $ReportId
     * @param unknown_type $data
     */
    public function _saveReport($ReportId,$text){
        $row = array('ReportId'=>$ReportId,'data'=>$text);
        $exist = Service_AmazonReportData::getByField($ReportId,'ReportId');
        if($exist){
            $row['update_time'] = now();
            Service_AmazonReportData::update($row, $ReportId,'ReportId');
        }else{

            $row['add_time'] = now();
            $row['update_time'] = now();
            Service_AmazonReportData::add($row);
        }
        //解析报告
        $reportRequestInfo = Service_AmazonReportRequestInfo::getByField($ReportId,'GeneratedReportId');
        switch($reportRequestInfo['ReportType']){
            case '_GET_MERCHANT_LISTINGS_DATA_':
                $this->_saveMerchantListing($text);
                break;
            case '_GET_MERCHANT_LISTINGS_DATA_LITE_':
                $this->_saveMerchantListingLite($text);
                break;
            case '_GET_MERCHANT_LISTINGS_DATA_LITER_':
                $this->_saveMerchantListingLiter($text);
                break;
                
            case '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_':
                $this->_saveOpenListing($text);
                break;
            case '_GET_MERCHANT_LISTINGS_DATA_BACK_COMPAT_':
                $this->_saveMerchantListingBackCompat($text);
                break;
            case '_GET_MERCHANT_CANCELLED_LISTINGS_DATA_':
                $this->_saveMerchantCancelledListing($text);
                break;
            case '_GET_MERCHANT_LISTINGS_DEFECT_DATA_':
                $this->_saveMerchantListingDefect($text);
                break;
                
        }        
    }
    
    public function test(){
        $db = Common_Common::getAdapter();
        $sql = "select * from amazon_report_request_info where ReportType='{$this->_reportType}' and ReportProcessingStatus='_DONE_' and company_code='{$this->_company_code}' and user_account='{$this->_user_account}'";
        $rows = $db->fetchAll($sql);
        foreach($rows as $row){
            $reportId = $row['GeneratedReportId'];
            if($reportId){
                $exist = Service_AmazonReportData::getByField($reportId,'ReportId');
                if($exist){
                    $text = $exist['data'];
                    $this->_ReportId = $exist['ReportId'];
//                     echo $text;exit;
                    $this->_saveMerchantListing($text);
                }
            }
        }
        
    }
    /**
     * 检测is_exist列是否存在
     */
    private function _checkColumnIsExistExist()
    {
        try{
            $table = 'amazon_merchant_listing';
            $sql = "desc {$table}";
            $rows = Common_Common::fetchAll($sql);
            $columns = array();
            foreach($rows as $v){
                $columns[] = $v['Field'];
            }
            if(! in_array('is_exist', $columns)){
                $sql = "ALTER TABLE `{$table}` ADD COLUMN `is_exist`  int(1) NULL DEFAULT '0' COMMENT ''";
                Common_Common::query($sql);
            }
        }catch(Exception $e){
            Common_ApiProcess::log($e->getMessage());
        }
    }
    
    /**
     * 保存数据
     * @param unknown_type $text
     */
    public function _saveMerchantListing($text){
        $db = Common_Common::getAdapter();
        $data = $this->_formatText($text);
        $this->_checkColumnIsExistExist();

        $table = 'amazon_merchant_listing';
        $sql = "update {$table} set is_exist=0 where company_code='{$this->_company_code}' and user_account='{$this->_user_account}';";
        $db->query($sql);        
        Amazon_Service::log($sql);
//         $con = array(
                
//         );
//         //判断是不是存在
//         $con['company_code'] = $this->_company_code;
//         $con['user_account'] = $this->_user_account;
//         //删除旧数据  start
//         $exists = Service_AmazonMerchantListing::getByCondition($con);
//         foreach($exists as $exist){
//             Service_AmazonMerchantListing::delete($exist['id'],'id');
//         }
//         //删除旧数据  end
        
        foreach($data as $v){
            $row = array(
                'listing_id' => $v['listing_id'],
                'seller_sku' => $v['seller_sku'],
                'item_name' => $v['item_name'],
                'item_description' => $v['item_description'],
                'price' => $v['price'],
                'quantity' => $v['quantity'],
                'open_date' => $v['open_date'],
                'image_url' => $v['image_url'],
                'item_is_marketplace' => $v['item_is_marketplace'],
                'product_id_type' => $v['product_id_type'],
                'zshop_shipping_fee' => $v['zshop_shipping_fee'],
                'item_note' => $v['item_note'],
                'item_condition' => $v['item_condition'],
                'zshop_category1' => $v['zshop_category1'],
                'zshop_browse_path' => $v['zshop_browse_path'],
                'zshop_storefront_feature' => $v['zshop_storefront_feature'],
                'asin1' => $v['asin1'],
                'asin2' => $v['asin2'],
                'asin3' => $v['asin3'],
                'will_ship_internationally' => $v['will_ship_internationally'],
                'expedited_shipping' => $v['expedited_shipping'],
                'zshop_boldface' => $v['zshop_boldface'],
                'product_id' => $v['product_id'],
                'bid_for_featured_placement' => $v['bid_for_featured_placement'],
                'add_delete' => $v['add_delete'],
                'pending_quantity' => $v['pending_quantity'],
                'fulfillment_channel' => $v['fulfillment_channel'],
                'is_exist'=>'1'
               
            );
            $row['fulfillment_type'] = 'FBA';
            if($v['fulfillment_channel']=='DEFAULT'){
                $row['fulfillment_type'] = 'MERCHANT';
            }
            $row['company_code'] = $this->_company_code;
            $row['user_account'] = $this->_user_account;
            $row = Ec_AutoRun::arrayNullToEmptyString($row);
            
            if($row['quantity']===''){//和配送方式相关
                $row['quantity'] = -1;
            }
            $row['item_status'] = 'on_sale';
            if(intval($row['quantity'])==0){//在售，停售
                $row['item_status'] = 'stop_sale';
            }            
//             print_r($row);exit;
            try{
                $con = array(
                    'seller_sku' => $v['seller_sku'],
                    'product_id' => $v['product_id']
                );
                $con['company_code'] = $this->_company_code;
                $con['user_account'] = $this->_user_account;
                $exist = Service_AmazonMerchantListing::getByCondition($con);
                if($exist){
                    $exist = array_pop($exist);
                    $row['update_time'] = now();
                    Service_AmazonMerchantListing::update($row, $exist['id'], 'id');
                }else{
                    $row['add_time'] = now();
                    $row['update_time'] = now();
                    Service_AmazonMerchantListing::add($row);
                }
                //默认补货
                Amazon_ProductServiceProcess::genDefaultSupplyQty($this->_user_account, $v['seller_sku']);

                //商品价格 start
                $con = array(
                        'offer_seller_sku' => $v['seller_sku'],
                        'company_code' => $this->_company_code,
                        'user_account' => $this->_user_account,
                );
                $prices = Service_AmazonMyPriceForSku::getByCondition($con);
                if(!$prices){
                    $con = array(
                        'seller_sku' => $v['seller_sku'],
                        'company_code' => $this->_company_code,
                        'user_account' => $this->_user_account
                    );
                    $prices = Service_AmazonMyPriceForSku::getByCondition($con);
                }
                if(!$prices){
                    //加入更新价格的任务
                    $table = Amazon_Common::cron_load_amazon_get_my_price_for_sku();
                    $sku = $v['Price']['SKU'];
                    $rowPrice = array(
                            'company_code' => $this->_company_code,
                            'user_account' => $this->_user_account,
                            'seller_sku' => $v['seller_sku']
                    );
                    $sql = "select * from {$table} where company_code='{$this->_company_code}' and user_account='{$this->_user_account}' and seller_sku='{$v['seller_sku']}'";
                    $exist = $db->fetchRow($sql);
                    if(! $exist){
                        $db->insert($table, $rowPrice);
                    }  
                } 
                //商品价格 end   

                
            }catch (Exception $e){
                file_put_contents(APPLICATION_PATH.'/../data/log/___amazon.txt', print_r($data,true));
            }            
        }
        //删除不存在的商品
        $sql = "delete from amazon_merchant_listing where is_exist=0 and company_code='{$this->_company_code}' and user_account='{$this->_user_account}';";
        $db->query($sql);
        Amazon_Service::log($sql);
    }
    /**
     * 保存数据
     * @param unknown_type $text
     */
    public function _saveMerchantListingLite($text){
        $data = $this->_formatText($text);
        foreach($data as $v){
            $row = array( 
                    'seller_sku' => $v['seller_sku'],
                    'product_id_lite' => $v['product_id'],
                    'price' => $v['price'],
                    'quantity' => $v['quantity'],
                     
            );
            $row = Common_ApiProcess::nullToEmptyString($row);

            if($row['quantity']===''){//和配送方式相关
                $row['quantity'] = -1;
            }
            $row['item_status'] = 'on_sale';
            if(intval($row['quantity'])==0){//在售，停售
                $row['item_status'] = 'stop_sale';
            }
            
            $row['company_code'] = $this->_company_code;
            $row['user_account'] = $this->_user_account;
            $row = Ec_AutoRun::arrayNullToEmptyString($row);
            //             print_r($row);exit;
            try{
                $con = array(
                    'seller_sku' => $v['seller_sku'],
                );
                $con['company_code'] = $this->_company_code;
                $con['user_account'] = $this->_user_account;
                
                $exist = Service_AmazonMerchantListing::getByCondition($con);
                if($exist){
                    $exist = array_pop($exist);                    
                    $row['update_time'] = now();
                    Service_AmazonMerchantListing::update($row, $exist['id'],'id');
                }else{
                    $row['add_time'] = now();
                    $row['update_time'] = now();
                    //Service_AmazonMerchantListing::add($row);
                }
            }catch (Exception $e){
                file_put_contents(APPLICATION_PATH.'/../data/log/___amazon.txt', print_r($data,true));
            }
    
        }
    }


    /**
     * 保存数据
     * @param unknown_type $text
     */
    public function _saveMerchantListingLiter($text){
        $data = $this->_formatText($text);
        
    }
    /**
     * 保存数据
     * @param unknown_type $text
     */
    public function _saveOpenListing($text){
        $data = $this->_formatText($text);
        return;
        foreach($data as $v){
            $row = array(
                    'sku' => $v['sku'],
                    'asin' => $v['asin'],
                    'price' => $v['price'],
                    'quantity' => $v['quantity'],
                     
            );
    
            $row['company_code'] = $this->_company_code;
            $row['user_account'] = $this->_user_account;
            $row = Ec_AutoRun::arrayNullToEmptyString($row);
            //             print_r($row);exit;
            try{
                $con = array(
                        'sku' => $v['sku'],
                        'asin' => $v['product_id']
                );
                $con['company_code'] = $this->_company_code;
                $con['user_account'] = $this->_user_account;
                $exist = Service_AmazonOpenListing::getByCondition($con);
                if($exist){
                    $exist = array_pop($exist);
                    $row['update_time'] = now();
                    Service_AmazonOpenListing::update($row, $exist['id'],'id');
                }else{
                    $row['add_time'] = now();
                    $row['update_time'] = now();
                    Service_AmazonOpenListing::add($row);
                }
            }catch (Exception $e){
                file_put_contents(APPLICATION_PATH.'/../data/log/___amazon.txt', print_r($data,true));
            }
    
        }
    }

    /**
     * 保存数据
     * @param unknown_type $text
     */
    public function _saveMerchantListingBackCompat($text){
        $data = $this->_formatText($text);
        return;
        foreach($data as $v){
            $row = array(
                'listing_id' => $v['listing_id'],
                'seller_sku' => $v['seller_sku'],
                'item_name' => $v['item_name'],
                'item_description' => $v['item_description'],
                'price' => $v['price'],
                'quantity' => $v['quantity'],
                'open_date' => $v['open_date'],
                'image_url' => $v['image_url'],
                'item_is_marketplace' => $v['item_is_marketplace'],
                'product_id_type' => $v['product_id_type'],
                'zshop_shipping_fee' => $v['zshop_shipping_fee'],
                'item_note' => $v['item_note'],
                'item_condition' => $v['item_condition'],
                'zshop_category1' => $v['zshop_category1'],
                'zshop_browse_path' => $v['zshop_browse_path'],
                'zshop_storefront_feature' => $v['zshop_storefront_feature'],
                'asin1' => $v['asin1'],
                'asin2' => $v['asin2'],
                'asin3' => $v['asin3'],
                'will_ship_internationally' => $v['will_ship_internationally'],
                'expedited_shipping' => $v['expedited_shipping'],
                'zshop_boldface' => $v['zshop_boldface'],
                'product_id' => $v['product_id'],
                'bid_for_featured_placement' => $v['bid_for_featured_placement'],
                'add_delete' => $v['add_delete'],
                'pending_quantity' => $v['pending_quantity'],
               
            );

            $row['company_code'] = $this->_company_code;
            $row['user_account'] = $this->_user_account;
            $row = Ec_AutoRun::arrayNullToEmptyString($row);
            
            if($row['quantity']===''){//和配送方式相关
                $row['quantity'] = -1;
            }
//             print_r($row);exit;
            try{
                $con = array(
                    'seller_sku' => $v['seller_sku'],
                    'product_id' => $v['product_id']
                );
                $con['company_code'] = $this->_company_code;
                $con['user_account'] = $this->_user_account;
                $exist = Service_AmazonMerchantListing::getByCondition($con);
                if($exist){
                    $exist = array_pop($exist);
                    $row['update_time'] = now();
                    Service_AmazonMerchantListing::update($row, $exist['id'], 'id');
                }else{
                    $row['add_time'] = now();
                    $row['update_time'] = now();
                    Service_AmazonMerchantListing::add($row);
                }
            }catch (Exception $e){
                file_put_contents(APPLICATION_PATH.'/../data/log/___amazon.txt', print_r($data,true));
            }
            
        }
    }


    /**
     * 保存数据
     * @param unknown_type $text
     */
    public function _saveMerchantCancelledListing($text){
        $data = $this->_formatText($text);
        return;
        foreach($data as $v){
//             print_r($v);
        }
           
    }


    /**
     * 保存数据
     * @param unknown_type $text
     */
    public function _saveMerchantListingDefect($text){
        $data = $this->_formatText($text);
        return;
        foreach($data as $v){
            print_r($v);
        }
         
    }
    
    
    /**
     * 格式化text为数组
     * @param unknown_type $text
     * @return multitype:multitype:string unknown
     */
    public function _formatText($text){ 
        $data = Amazon_Common::_formatText($text);
        foreach($data as $k=>$v){
            $v['company_code'] = $this->_company_code;
            $v['user_account'] = $this->_user_account;
            $data[$k] = $v;
        }
        Ec::showError(print_r($data,true),$this->_reportType);
        return $data;
    }
    public function getReportList(){
        $request = new MarketplaceWebService_Model_GetReportListRequest();
        $request->setMerchant($this->_MarketplaceId);
        $request->setAvailableToDate(new DateTime('now', new DateTimeZone('UTC')));
        $request->setAvailableFromDate(new DateTime('-3 months', new DateTimeZone('UTC')));
        $request->setAcknowledged(false); 
        try{
            $service = $this->_service;
            //获取数据
            $this->invokeGetReportList($service,$request);
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $this->logException($ex);    
        }
        
    }
    

    public function invokeGetReportList(MarketplaceWebService_Client $service, $request)
    {
        $response = $service->getReportList($request);
        Amazon_Service::log("Service Response=============================================================================");
        $this->_responseMetadata($response);
         
        
        Amazon_Service::log("GetReportListResponse");
        if($response->isSetGetReportListResult()){
            Amazon_Service::log("GetReportListResult");
            $getReportListResult = $response->getGetReportListResult();
            
            if($getReportListResult->isSetHasNext()){
                $this->_hasNext = $getReportListResult->getHasNext();
                Amazon_Service::log("HasNext:" . $getReportListResult->getHasNext() . "");
            }
            
            if($getReportListResult->isSetNextToken()){
                $this->_nextToken = $getReportListResult->getNextToken();
                Amazon_Service::log("NextToken:" . $getReportListResult->getNextToken() . "");
            }
            $reportInfoList = $getReportListResult->getReportInfoList();
            foreach($reportInfoList as $reportInfo){
                $this->_saveReportInfo($reportInfo);
            }
        }
        
        $this->getReportRequestListNextData($service);
    }

    /**
     * 获取下页的数据
     * @param MarketplaceWebService_Client $service
     */
    public function getReportListNextData(MarketplaceWebService_Client $service){
        if($this->_hasNext && $this->_nextToken){
            $request = new MarketplaceWebService_Model_GetReportListByNextTokenRequest();
            $request->setMerchant($this->_tokenConfig['MERCHANT_ID']);
            $request->setNextToken($this->_nextToken);
            try{
                $this->invokeGetReportListByNextToken($service, $request);
            }catch(MarketplaceWebService_Exception $ex){
                //记录日志
                $this->logException($ex);    
        
                $this->invokeGetReportListByNextToken($service, $request);
            }
        }
    }
    
    public function invokeGetReportListByNextToken(MarketplaceWebService_Client $service, $request)
    {
        $response = $service->getReportListByNextToken($request);
        Amazon_Service::log("Service Response=============================================================================");
        $this->_responseMetadata($response);
         
        
        Amazon_Service::log("GetReportListByNextTokenResponse");
        if($response->isSetGetReportListByNextTokenResult()){
            Amazon_Service::log("GetReportListByNextTokenResult");
            $getReportListByNextTokenResult = $response->getGetReportListByNextTokenResult();
            if($getReportListByNextTokenResult->isSetNextToken()){
                Amazon_Service::log("NextToken:" . $getReportListByNextTokenResult->getNextToken() . "");
            }
            if($getReportListByNextTokenResult->isSetHasNext()){
                Amazon_Service::log("HasNext:" . $getReportListByNextTokenResult->getHasNext() . "");
            }
            $reportInfoList = $getReportListByNextTokenResult->getReportInfo();
            foreach($reportInfoList as $reportInfo){
                $this->_saveReportInfo($reportInfo);
            }
        }
        $this->getReportListNextData($service);
    }

    /**
     * 数据保存
     * @param unknown_type $reportRequestInfo
     */
    protected  function _saveReportInfo($reportInfo){
        $reportInfoRow = array();
        Amazon_Service::log("ReportInfo");
        if($reportInfo->isSetReportId()){
            Amazon_Service::log("ReportId:" . $reportInfo->getReportId() . "");
        }
        if($reportInfo->isSetReportType()){
            Amazon_Service::log("ReportType:" . $reportInfo->getReportType() . "");
        }
        if($reportInfo->isSetReportRequestId()){
            Amazon_Service::log("ReportRequestId:" . $reportInfo->getReportRequestId() . "");
        }
        if($reportInfo->isSetAvailableDate()){
            Amazon_Service::log("AvailableDate:" . $reportInfo->getAvailableDate()->format(DATE_FORMAT) . "");
        }
        if($reportInfo->isSetAcknowledged()){
            Amazon_Service::log("Acknowledged:" . $reportInfo->getAcknowledged() . "");
        }
        if($reportInfo->isSetAcknowledgedDate()){
            Amazon_Service::log("AcknowledgedDate:" . $reportInfo->getAcknowledgedDate()->format(DATE_FORMAT) . "");
        }
        
        // print_r($reportRequestInfoRow);exit;
        $reportInfoRow = Common_ApiProcess::nullToEmptyString($reportInfoRow);
        
        $reportInfoRow['company_code'] = $this->_company_code;
        $reportInfoRow['user_account'] = $this->_user_account;
//         $exist = Service_AmazonReportRequestInfo::getByField($reportInfoRow['ReportId'], 'ReportId');
//         if($exist){
//             Service_AmazonReportRequestInfo::update($reportInfoRow, $exist['id'], 'id');
//         }else{
//             Service_AmazonReportRequestInfo::add($reportInfoRow);
//         }
    }
}