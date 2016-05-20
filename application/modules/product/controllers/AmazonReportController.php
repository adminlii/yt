<?php
class Product_AmazonReportController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "product/views/amazon-report/";
        $this->serviceClass = new Service_AmazonReportRequestInfo();
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
            $ReportRequestId = $this->getParam('ReportRequestId', '');
            $ReportType = $this->getParam('ReportType', '');
            $ReportProcessingStatus = $this->getParam('ReportProcessingStatus', '');
            $GeneratedReportId = $this->getParam('GeneratedReportId', '');
            
            $condition['ReportRequestId'] = $ReportRequestId;
            $condition['ReportType'] = $ReportType;
            $condition['ReportProcessingStatus'] = $ReportProcessingStatus;
            $condition['GeneratedReportId'] = $GeneratedReportId;
            
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
                    $v['download'] = 'NO';
                    if($v['GeneratedReportId']){
                        $report = Service_AmazonReportData::getByField($v['GeneratedReportId'], 'ReportId',array('ReportId'));
                        if($report){
                            $v['download'] = 'YES';
                        }
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
        $ReportTypeArr = $db->fetchAll("select distinct ReportType from amazon_report_request_info");
        $ReportProcessingStatusArr = $db->fetchAll("select distinct ReportProcessingStatus from amazon_report_request_info");

        $this->view->ReportTypeArr = $ReportTypeArr;
        $this->view->ReportProcessingStatusArr = $ReportProcessingStatusArr;
        
        echo Ec::renderTpl($this->tplDirectory . "report_list.tpl", 'layout');
    }

    /**
     * 创建报告
     * @throws Exception
     */
    public function requestReportAction(){
        try {
            $user_account = $this->getParam('acc','---');
            $type = $this->getParam('type','_GET_MERCHANT_LISTINGS_DATA_LITE_');
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
            $token_id = $resultPlatformUser["user_token_id"];
            $token = $resultPlatformUser["user_token"];
            $saller_id = $resultPlatformUser["seller_id"];
            $site = $resultPlatformUser["site"];
            
            $company_code = $resultPlatformUser['company_code'];
            $user_account = $resultPlatformUser['user_account'];
            // 逻辑处理
            $service = new Amazon_ProductService($token_id, $token, $saller_id, $site);
            $service->setCompanyCode($company_code);
            $service->setUserAccount($user_account);
            $service->setReportType($type);
            $rs = $service->requestReport();
            $rs = print_r($rs,true);
            $rs = preg_replace('/\n/','<br/>',$rs);
            $rs = preg_replace('/ /','&nbsp;',$rs);
            echo $rs;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 报告结果
     */
    public function viewReportDataAction()
    {
        $ReportId = $this->getParam('report_id', '');
        $report = Service_AmazonReportData::getByField($ReportId, 'ReportId');
        if($report && $report['data']){
            $data = Amazon_Common::_formatText($report['data']);
            foreach($data as $k => $v){
                unset($v['item_description']);
                $data[$k] = $v;
            }
            $rs = print_r($data, true);
            
            $rs = preg_replace('/\n+/', '<br/>', $rs);
            $rs = preg_replace('/\s/', '&nbsp;', $rs);
            echo $rs;
        }else{
            echo 'No Data';
        }
        exit();
    }

    /**
     * 手动获取结果
     * @throws Exception
     */
    public function getReportResultAction(){
        try{            
            $ReportRequestId = $this->getParam('report_request_id', '');
            
//             echo $ReportRequestId;exit;
            $report = Service_AmazonReportRequestInfo::getByField($ReportRequestId, 'ReportRequestId');
            
            $con = array(
                'user_account' => $report['user_account'],
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
            $service = new Amazon_ProductService($token_id, $token, $saller_id, $site);
            $service->setCompanyCode($company_code);
            $service->setUserAccount($user_account);
            
            $rs = $service->getReportRequestList('', '', array(
                $ReportRequestId
            ));

            $report = Service_AmazonReportRequestInfo::getByField($ReportRequestId, 'ReportRequestId');
            $rs1 = '';
            if($report['ReportProcessingStatus']=='_DONE_'&&!empty($report['GeneratedReportId'])){                
                $download = Service_AmazonReportData::getByField($report['GeneratedReportId'],'ReportId');
                if(!$download){
                    $rs1 = $service->getReport($report['GeneratedReportId']);
                    //可能造成数据假死
                    unset($rs1['data']);
                }else{
                    //可能造成数据假死
                    unset($download['data']);
                }                
            }            
            $rs = print_r($rs,true).print_r($rs1,true);
            $rs = preg_replace('/\n+/', '<br/>', $rs);
            $rs = preg_replace('/\s/', '&nbsp;', $rs);
            echo $rs;
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}