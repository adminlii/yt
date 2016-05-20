<?php
class Amazon_InventoryServiceProcess extends Ec_AutoRun
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
            throw new Exception('账号不存在');
        }
        $resultPlatformUser = array_pop($resultPlatformUser);
        
        $this->token_id = $resultPlatformUser["user_token_id"];
        $this->token = $resultPlatformUser["user_token"];
        $this->saller_id = $resultPlatformUser["seller_id"];
        $this->site = $resultPlatformUser["site"];
        
        $this->company_code = $resultPlatformUser['company_code'];
        $this->account = $resultPlatformUser['user_account'];
        
        // 类初始化
        $service = new Amazon_InventoryService($this->token_id, $this->token, $this->saller_id, $this->site);
        $service->setCompanyCode($this->company_code);
        $service->setUserAccount($this->account);
        if(isset($this->_feedType)){
            $service->setfeedType($this->_feedType);
        }
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
    public function submitInventoryFeed($loadId)
    {
        try{
            $this->_amazonInit($loadId);          
            
            Common_ApiProcess::log('创建报告' . $this->account);
            // 逻辑处理
            $rs = $this->_service->submitFeed();
            if($rs['ask'] == 1){
                //更新补货状态
                $inventoryListing = $this->_service->getInventoryListing(); 
                $MessageArr = $this->_service->getMessageArr(); 

                $this->countLoad($loadId, 2, count($MessageArr)); // 运行结束
                foreach($inventoryListing as $v){
                    Service_SellerItemSupplyQty::update(array('sync_status'=>'1'), $v['id'],'id');
                }
//                 $MessageArr[] = array(
//                 		'Message' => array(
//                 				'MessageID' => $k + 1,
//                 				'OperationType' => 'Update',
//                 				'Inventory' => array(
//                 						// 'SwitchFulfillmentTo' => 'MFN',
//                 						'SKU' => $v['sku'],
//                 						'Quantity' => $qty
//                 				)
//                 		)
//                 );
				try {
					//补货日志
					foreach($MessageArr as $msg){
						$sku = $msg['Message']['Inventory']['SKU'];
						$qty =  $msg['Message']['Inventory']['Quantity'];
						$supLog = array(
								'item_id' => '',
								'sku' => $sku,
								'supply_qty' => $qty,
								'sell_qty' => '',
								'platform'=>'amazon',
								'user_account'=>$this->account,
								'company_code'=>$this->company_code
						);
						Service_SellerItemSupLog::add($supLog);
					}
				} catch (Exception $e) {
					//
					Common_ApiProcess::log($e->getMessage());
				}
				
            }else{
                throw new Exception(print_r($rs, true));
            }
            
            return array(
                'ask' => 1,
                'message' => "数据提交到Amazon[{$rs['message']}]-->" . $this->account
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), __CLASS__.__METHOD__);
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 更新列表
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function getInventoryFeedSubmissionList($loadId)
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
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), __FUNCTION__);
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 下载报告
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function getInventoryFeedSubmissionResult($loadId)
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
            
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), __FUNCTION__);
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }
}