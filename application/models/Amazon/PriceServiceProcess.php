<?php
class Amazon_PriceServiceProcess extends Ec_AutoRun
{

    private $token_id = '';

    private $token = '';

    private $saller_id = '';

    private $site = '';

    private $company_code = '';

    private $account = '';

    private $start = '';

    private $end = '';

    private $_service = null;

    private $_feedType = null;

    private function _amazonInit($loadId)
    {
        // 得到当前同步订单的关键参数
        $param = $this->getLoadParam($loadId);
        
        // Ec::showError(var_export($param,1),'params'.time());
        $account = $param["user_account"];
        $this->start = $param["load_start_time"];
        $this->end = $param["load_end_time"];
        
        $con = array(
            'platform' => 'amazon',
            'status' => 1,
            'user_account' => $account
        );
        
        $resultPlatformUser = Service_PlatformUser::getByCondition($con);
        if(empty($resultPlatformUser)){
            throw new Exception('账号不存在/未激活-->'.$account);
        }
        $resultPlatformUser = array_pop($resultPlatformUser);
        
        $this->token_id = $resultPlatformUser["user_token_id"];
        $this->token = $resultPlatformUser["user_token"];
        $this->saller_id = $resultPlatformUser["seller_id"];
        $this->site = $resultPlatformUser["site"];
        
        $this->company_code = $resultPlatformUser['company_code'];
        $this->account = $resultPlatformUser['user_account'];
        
        // 类初始化
        $service = new Amazon_PriceService($this->token_id, $this->token, $this->saller_id, $this->site);
        $service->setCompanyCode($this->company_code);
        $service->setUserAccount($this->account);
        if(isset($this->_feedType)){
            $service->setFeedType($this->_feedType);
        }
        // $service->submitFeed();
        $this->_service = $service;
    }

    private function _setFeedType($feedType)
    {
        $this->_feedType = $feedType;
    }

    /**
     * 提交数据
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazon1SubmitFeed($loadId)
    {
        try{
            $this->_amazonInit($loadId);
            Common_ApiProcess::log('创建报告' . $this->account);
            
            // 逻辑处理
            $rs = $this->_service->submitFeed();
            if($rs['ask'] == 1){
                $this->countLoad($loadId, 2, 0); // 运行结束
                
                $priceListing = $this->_service->getPriceListing();
                foreach($priceListing as $v){
                    Service_AmazonMerchantListingPriceSet::update(array('sync_status'=>'1'), $v['id'],'id');
                }
            }else{
                throw new Exception(print_r($rs['exception'], true));
            }
            
            return array(
                'ask' => 1,
                'message' => "数据提交到Amazon-->" . $this->account
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), 'amazon1SubmitFeed');
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 获取列表
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazon2GetFeedSubmissionList($loadId)
    {
        try{
            $this->_amazonInit($loadId);
            Common_ApiProcess::log('开始下载报告列表' . $this->account);
            // 逻辑处理
            // 不传递结束时间（建议）
            $this->end = null;
            $rs = $this->_service->getFeedSubmissionList($this->start, $this->end);
            
            if($rs['ask'] == 1){
                $this->countLoad($loadId, 2, 0); // 运行结束
            }else{
                throw new Exception(print_r($rs['exception'], true));
            }
            
            return array(
                'ask' => 1,
                'message' => "报告列表下载完成" . $this->account
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), 'amazon2GetFeedSubmissionList');
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 获取结果
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazon3GetFeedSubmissionResult($loadId)
    {
        try{
            $this->_amazonInit($loadId);
            Common_ApiProcess::log('开始下载报告' . $this->account);
            // 逻辑处理
            
            $this->_service->getAllFeedSubmissionNotLoadNew();
            
            $this->countLoad($loadId, 2, 0); // 运行结束
            
            return array(
                'ask' => 1,
                'message' => "报告下载完毕" . $this->account
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), 'amazon3GetFeedSubmissionResult');
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    public static function demo()
    {
        try{            
            $con = array(
                'user_account' => "missmayqinke@gmail.com",
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
            $service = new Amazon_PriceService($token_id, $token, $saller_id, $site);
            $service->setCompanyCode($company_code);
            $service->setUserAccount($user_account);
            // $service->submitFeed();
            $service->getFeedSubmissionList('', '', '');
            // $service->getFeedSubmissionResult('11007668564');
            // $service->getAllFeedSubmissionNotLoadNew();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}