<?php
define ('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');
require_once 'XmlHandle.php';
class Amazon_Service
{

    protected $_service = null;
    protected $_company_code = '';
    protected $_user_account = '';
    protected $_MarketplaceId = '';    
    protected $_config = array();

    protected $_ReportProcessingStatus = array(
            '_DONE_',
            '_SUBMITTED_',
            '_IN_PROGRESS_',
            '_CANCELLED_',
            '_DONE_NO_DATA_'
    );
    protected $_RequestId = '';

    // 加载自定义类，主要是放在models下的类
    // private 禁止外部调用
    /**
     * @param string $class
     */
    private static function autoload ($class) {
        $file = preg_replace("/_/", "/", $class) . '.php';
//         echo $file;exit;
        // 自动加载自定义类
        if (preg_match('/^(MarketplaceWebService)/i', $file)) {
            require_once (ucfirst($file));
        }
    }
    /**
     * 构造器
     */
    public function __construct($token_id, $token, $saller_id, $site)
    {    
        //自定义加载
        spl_autoload_register(array(
            __CLASS__,
            'autoload'
        ));
        
        // 访问秘钥ID
        $this->_tokenConfig['AWS_ACCESS_KEY_ID'] = $token_id;
        // 访问秘钥
        $this->_tokenConfig['AWS_SECRET_ACCESS_KEY'] = $token;
        // 销售ID
        $this->_tokenConfig['MERCHANT_ID'] = $saller_id;
        // 站点
        $this->_tokenConfig['SITE'] = $site;
        // 应用名称
        $this->_tokenConfig['APPLICATION_NAME'] = Amazon_AmazonLib::APPLICATION_NAME;
        // 应用版本
        $this->_tokenConfig['APPLICATION_VERSION'] = Amazon_AmazonLib::APPLICATION_VERSION;
        
        /*
         * 秘钥
         */
        $countryCode = $this->_tokenConfig['SITE'];
        
        /*
         * 2. 取得亚马逊站点、地址
         */
        $amazonConfig = Amazon_AmazonLib::getAmazonConfig();
        if(empty($amazonConfig[$countryCode])){
            throw new Exception("amzon站点： $countryCode ，未能找到对应的亚马逊服务地址及商城编号.");
        }
        $this->_MarketplaceId = $amazonConfig[$countryCode]['marketplace_id'];
        /*
         * 3. 初始化配置信息，创建request对象
         */
        $serviceUrl = $amazonConfig[$countryCode]['service_url'];
        $config = array(
            'ServiceURL' => $serviceUrl,
            'ProxyHost' => null,
            'ProxyPort' => - 1,
            'MaxErrorRetry' => 3
        );
        $this->_config = $config;
        $service = new MarketplaceWebService_Client($this->_tokenConfig['AWS_ACCESS_KEY_ID'], $this->_tokenConfig['AWS_SECRET_ACCESS_KEY'], $config, $this->_tokenConfig['APPLICATION_NAME'], $this->_tokenConfig['APPLICATION_VERSION']);
       
        $this->_service = $service;
    }
    public function setCompanyCode($company_code){
        $this->_company_code = $company_code;
    }
    public function setUserAccount($user_account){
        $this->_user_account = $user_account;
    }

    protected function _responseMetadata($response){
        if($response->isSetResponseMetadata()){
            Amazon_Service::log("ResponseMetadata");
            $responseMetadata = $response->getResponseMetadata();
            if($responseMetadata->isSetRequestId()){
                $this->_RequestId = $responseMetadata->getRequestId();
                Amazon_Service::log("RequestId:" . $responseMetadata->getRequestId() . "");
            }
        }
        Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "");
    }
    /**
     * 提交上传数据
     *
     * @param unknown_type $FeedContent
     */
    public function requestReport()
    {
       
        $service = $this->_service;
//         $d = new DateTime(date('Y-m-d\TH:i:s\Z', strtotime('-1day')), new DateTimeZone('UTC'));
        // $d = $d->format('Y-m-d\TH:i:s');
        // $d = str_replace("+0000", "+08:00", $d);
    
        $parameters = array(
                'Merchant' => $this->_tokenConfig['MERCHANT_ID'],
                'MarketplaceIdList' => array(
                        "Id" => array(
                                $this->_MarketplaceId,
                        )
                ),
                // 'Marketplace' =>$amazonConfig[$countryCode]['marketplace_id'],
//                 'StartDate' => $d,
                'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
                'ReportOptions' => 'ShowSalesChannel=true'
        );
    
        $request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
    
        $response = $this->invokeRequestReport($service, $request);
        return $response;
    }
    
    public function invokeRequestReport(MarketplaceWebService_Client $service, $request)
    {
        try{
            $response = $service->requestReport($request);
            // Ec::showError(print_r($response,true),'__amazon_');
            // Amazon_Service::log(print_r($response,true));
            echo ("Service Response");
            echo ("=============================================================================");
    
            Amazon_Service::log("RequestReportResponse");
            if($response->isSetRequestReportResult()){
                Amazon_Service::log("RequestReportResult");
                $requestReportResult = $response->getRequestReportResult();
    
                if($requestReportResult->isSetReportRequestInfo()){
    
                    $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                    Amazon_Service::log("ReportRequestInfo");
                    if($reportRequestInfo->isSetReportRequestId()){
                        Amazon_Service::log("ReportRequestId:" . $reportRequestInfo->getReportRequestId());
                    }
                    if($reportRequestInfo->isSetReportType()){
                        Amazon_Service::log("ReportType:" . $reportRequestInfo->getReportType());
                    }
                    if($reportRequestInfo->isSetStartDate()){
                        Amazon_Service::log("StartDate:" . $reportRequestInfo->getStartDate()->format(DATE_FORMAT));
                    }
                    if($reportRequestInfo->isSetEndDate()){
                        Amazon_Service::log("EndDate:" . $reportRequestInfo->getEndDate()->format(DATE_FORMAT));
                    }
                    if($reportRequestInfo->isSetSubmittedDate()){
                        Amazon_Service::log("SubmittedDate:" . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT));
                    }
                    if($reportRequestInfo->isSetReportProcessingStatus()){
                        Amazon_Service::log("ReportProcessingStatus:" . $reportRequestInfo->getReportProcessingStatus());
                    }
                }
            }
            if($response->isSetResponseMetadata()){
                Amazon_Service::log("ResponseMetadata");
                $responseMetadata = $response->getResponseMetadata();
                if($responseMetadata->isSetRequestId()){
                    Amazon_Service::log("RequestId:" . $responseMetadata->getRequestId());
                }
            }
            Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata());
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $this->logException($ex);    
        }
    }

    public function getReportRequestList($start = '', $end = '', $reportRequestIdArr = array(), $ReportProcessingStatusList = array())
    {
        $service  = $this->_service;
        $parameters = array(
                'MaxCount' => 100,
                //             'MaxCount' => 5,
                // 'ReportRequestIdList'=>array(
                // "Id" => array(
                // $reportRequestId
                // )
                // ),
        //             'ReportTypeList' => array(
                //                 "Type" => array(
                        //                     '_GET_MERCHANT_LISTINGS_DATA_'
                        //                 )
                //             ),
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
                if(is_string($v)){
                    throw new Exception('reportRequestIdArr格式不正确' . __LINE__);
                }
            }
            $parameters['ReportRequestIdList']['Id'] = $reportRequestIdArr;
        }
        if(is_array($ReportProcessingStatusList)&&!empty($ReportProcessingStatusList)){
            foreach($ReportProcessingStatusList as $v){
                if(is_string($v)){
                    throw new Exception('ReportProcessingStatusList格式不正确' . __LINE__);
                }
            }
            $diff = array_diff($ReportProcessingStatusList, $this->_ReportProcessingStatus);
            if($diff){
                throw new Exception('ReportProcessingStatusList格式不正确' . __LINE__);
            }
            $parameters['ReportProcessingStatusList']['Status'] = $ReportProcessingStatusList;
        }
        //         print_r($parameters);exit;
        $request = new MarketplaceWebService_Model_GetReportRequestListRequest($parameters);
    
        try{
            //获取数据
            $this->invokeGetReportRequestList($service,$request);
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $this->logException($ex);    
        }
    }
    
    public function invokeGetReportRequestList(MarketplaceWebService_Client $service, $request)
    {
        try{
            $response = $service->getReportRequestList($request);
    
            Amazon_Service::log ("Service Response");
            Amazon_Service::log ("=============================================================================");
    
            Amazon_Service::log("GetReportRequestListResponse");
            if($response->isSetGetReportRequestListResult()){
                Amazon_Service::log("GetReportRequestListResult");
                $getReportRequestListResult = $response->getGetReportRequestListResult();
    
                if($getReportRequestListResult->isSetHasNext()){
                    Amazon_Service::log("HasNext:" . $getReportRequestListResult->getHasNext() . "");
                }
                if($getReportRequestListResult->isSetNextToken()){
                    Amazon_Service::log("NextToken:" . $getReportRequestListResult->getNextToken() . "");
                }
                $reportRequestInfoList = $getReportRequestListResult->getReportRequestInfoList();
                foreach($reportRequestInfoList as $reportRequestInfo){
                    Amazon_Service::log("ReportRequestInfo");
                    if($reportRequestInfo->isSetReportRequestId()){
                        Amazon_Service::log("ReportRequestId: " . $reportRequestInfo->getReportRequestId() . "");
                    }
                    if($reportRequestInfo->isSetReportType()){
                        Amazon_Service::log("ReportType: " . $reportRequestInfo->getReportType() . "");
                    }
                    if($reportRequestInfo->isSetStartDate()){
                        Amazon_Service::log("StartDate: " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "");
                    }
                    if($reportRequestInfo->isSetEndDate()){
                        Amazon_Service::log("EndDate: " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "");
                    }
                    if($reportRequestInfo->isSetSubmittedDate()){
                        Amazon_Service::log("SubmittedDate: " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "");
                    }
                    if($reportRequestInfo->isSetReportProcessingStatus()){
                        Amazon_Service::log("ReportProcessingStatus: " . $reportRequestInfo->getReportProcessingStatus() . "");
                    }
                }
            }
            if($response->isSetResponseMetadata()){
                Amazon_Service::log("ResponseMetadata");
                $responseMetadata = $response->getResponseMetadata();
                if($responseMetadata->isSetRequestId()){
                    Amazon_Service::log("RequestId: " . $responseMetadata->getRequestId() . "");
                }
            }
    
            Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "");
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $this->logException($ex);    
        }
    }
    
    public function invokeGetReportRequestListByNextToken(MarketplaceWebService_Interface $service, $request)
    {
        try{
            $response = $service->getReportRequestListByNextToken($request);
    
            Amazon_Service::log("Service Response");
            Amazon_Service::log("=============================================================================");
    
            Amazon_Service::log("GetReportRequestListByNextTokenResponse");
            if($response->isSetGetReportRequestListByNextTokenResult()){
                Amazon_Service::log("GetReportRequestListByNextTokenResult");
                $getReportRequestListByNextTokenResult = $response->getGetReportRequestListByNextTokenResult();
                if($getReportRequestListByNextTokenResult->isSetNextToken()){
                    Amazon_Service::log("NextToken:" . $getReportRequestListByNextTokenResult->getNextToken() . "");
                }
                if($getReportRequestListByNextTokenResult->isSetHasNext()){
                    Amazon_Service::log("HasNext:" . $getReportRequestListByNextTokenResult->getHasNext() . "");
                }
                $reportRequestInfoList = $getReportRequestListByNextTokenResult->getReportRequestInfoList();
                foreach($reportRequestInfoList as $reportRequestInfo){
                    Amazon_Service::log("ReportRequestInfo");
                    if($reportRequestInfo->isSetReportRequestId()){
                        Amazon_Service::log("ReportRequestId:" . $reportRequestInfo->getReportRequestId() . "");
                    }
                    if($reportRequestInfo->isSetReportType()){
                        Amazon_Service::log("ReportType:" . $reportRequestInfo->getReportType() . "");
                    }
                    if($reportRequestInfo->isSetStartDate()){
                        Amazon_Service::log("StartDate:" . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "");
                    }
                    if($reportRequestInfo->isSetEndDate()){
                        Amazon_Service::log("EndDate:" . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "");
                    }
                    if($reportRequestInfo->isSetSubmittedDate()){
                        Amazon_Service::log("SubmittedDate:" . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "");
                    }
                    if($reportRequestInfo->isSetReportProcessingStatus()){
                        Amazon_Service::log("ReportProcessingStatus:" . $reportRequestInfo->getReportProcessingStatus() . "");
                    }
                }
            }
            if($response->isSetResponseMetadata()){
                Amazon_Service::log("ResponseMetadata");
                $responseMetadata = $response->getResponseMetadata();
                if($responseMetadata->isSetRequestId()){
                    Amazon_Service::log("RequestId:" . $responseMetadata->getRequestId() . "");
                }
            }
    
            Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "");
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $this->logException($ex);    
        }
    }
    
    
    public function invokeGetReportList(MarketplaceWebService_Interface $service, $request)
    {
        try{
            $response = $service->getReportList($request);
    
            Amazon_Service::log("Service Response");
            Amazon_Service::log("=============================================================================");
    
            Amazon_Service::log("GetReportListResponse");
            if($response->isSetGetReportListResult()){
                Amazon_Service::log("GetReportListResult");
                $getReportListResult = $response->getGetReportListResult();
                if($getReportListResult->isSetNextToken()){
                    Amazon_Service::log("NextToken:" . $getReportListResult->getNextToken() . "");
                }
                if($getReportListResult->isSetHasNext()){
                    Amazon_Service::log("HasNext:" . $getReportListResult->getHasNext() . "");
                }
                $reportInfoList = $getReportListResult->getReportInfoList();
                foreach($reportInfoList as $reportInfo){
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
                }
            }
            if($response->isSetResponseMetadata()){
                Amazon_Service::log("ResponseMetadata");
                $responseMetadata = $response->getResponseMetadata();
                if($responseMetadata->isSetRequestId()){
                    Amazon_Service::log("RequestId:" . $responseMetadata->getRequestId() . "");
                }
            }
    
            Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "");
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $this->logException($ex);    
        }
    }
    
    public function invokeGetReportListByNextToken(MarketplaceWebService_Interface $service, $request)
    {
        try{
            $response = $service->getReportListByNextToken($request);
    
            Amazon_Service::log("Service Response");
            Amazon_Service::log("=============================================================================");
    
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
                }
            }
            if($response->isSetResponseMetadata()){
                Amazon_Service::log("ResponseMetadata");
                $responseMetadata = $response->getResponseMetadata();
                if($responseMetadata->isSetRequestId()){
                    Amazon_Service::log("RequestId:" . $responseMetadata->getRequestId() . "");
                }
            }
            Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "");
        }catch(MarketplaceWebService_Exception $ex){
            //记录日志
            $this->logException($ex);    
        }
    }
    
    
    public function invokeGetReport(MarketplaceWebService_Interface $service, $request)
    {
        try {
            $response = $service->getReport($request);
    
            Amazon_Service::log("Service Response");
            Amazon_Service::log("=============================================================================");
    
            Amazon_Service::log("GetReportResponse");
            if ($response->isSetGetReportResult()) {
                $getReportResult = $response->getGetReportResult();
                Amazon_Service::log("GetReport");
                if ($getReportResult->isSetContentMd5()) {
                    Amazon_Service::log("ContentMd5:" . $getReportResult->getContentMd5() . "");
                }
            }
            if ($response->isSetResponseMetadata()) {
                Amazon_Service::log("ResponseMetadata");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId())
                {
                    Amazon_Service::log("RequestId:" . $responseMetadata->getRequestId() . "");
                }
            }
    
            Amazon_Service::log("Report Contents:".stream_get_contents($request->getReport()) . "");
    
            Amazon_Service::log("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "");
        } catch (MarketplaceWebService_Exception $ex) {
            //记录日志
            $this->logException($ex);    
        }
    }
    
    public static function log($str){

        if (empty ( $str )) {
            return;
        }
        Ec::showError($str, '__amazon_log_');
        if (Zend_Registry::isRegistered('SAPI_DEBUG') && Zend_Registry::get('SAPI_DEBUG') === true) {
            echo '[' . date ( 'Y-m-d H:i:s' ) . ']' . iconv ( 'UTF-8', 'GB2312', $str . "\n" );
        }        
    }
    /**
     * 保存报告内容
     * @param unknown_type $ReportId
     * @param unknown_type $data
     */
    public static function  saveReportData($ReportId,$data){
        $exist = Service_AmazonReportData::getByField($ReportId, 'ReportId');
        $row = array(
            'ReportId' => $ReportId,
            'data' => $data
        );
        if($exist){
            $row['update_time'] = now();
            Service_AmazonReportData::update($row, $ReportId, 'ReportId');
        }else{
            $row['add_time'] = now();
            $row['update_time'] = now();
            Service_AmazonReportData::add($row);
        }
    }

    protected function array2xml($info, &$xml)
    {
        foreach($info as $key => $value){
            if(is_array($value)){
                if(is_numeric($key)){
                    $key = array_pop(array_keys($value));
                    $value = array_pop($value);
                }
                $subnode = $xml->addChild("{$key}");
                $this->array2xml($value, $subnode);
            }else{
                $xml->addChild("{$key}", htmlspecialchars("$value"));                
            }
        }
    }
    
    protected function getXmlContent($arr){
            // creating object of SimpleXMLElement
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><AmazonEnvelope xsi:noNamespaceSchemaLocation=\"amzn-envelope.xsd\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"></AmazonEnvelope>");
        
        // function call to convert array to xml
        $this->array2xml($arr, $xml);
        
        // 2. output xml
        $xml = $xml->asXML();
        $xml = trim($xml);
        file_put_contents(APPLICATION_PATH . '/../data/log/_amazon_inventory.xml', $xml);
        return $xml;
    }
    /**
     * 记录日志
     * @param unknown_type $ex
     */
    protected function logException($ex){
        $exception = array(
            "Caught Exception: " . $ex->getMessage(),
            "Response Status Code: " . $ex->getStatusCode(),
            "Error Code: " . $ex->getErrorCode(),
            "Error Type: " . $ex->getErrorType(),
            "Request ID: " . $ex->getRequestId(),
            "XML: " . $ex->getXML(),
            "ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata()
        );
        Amazon_Service::log(print_r($exception, true));
        return $exception;
    }

}