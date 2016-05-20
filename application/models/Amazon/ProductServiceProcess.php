<?php
class Amazon_ProductServiceProcess extends Ec_AutoRun
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

    private $_reportType = null;

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
        $service = new Amazon_ProductService($this->token_id, $this->token, $this->saller_id, $this->site);
        $service->setCompanyCode($this->company_code);
        $service->setUserAccount($this->account);
        if(isset($this->_reportType)){
            $service->setReportType($this->_reportType);
        }
        $this->_service = $service;
    }

    private function _setReportType($reportType)
    {
        $this->_reportType = $reportType;
    }

    /**
     * 获取产品图片和链接
     * @param unknown_type $loadId
     * @return multitype:number string
     */
    public function amazonListingEcs($loadId){
    	try{
    		$this->_amazonInit($loadId);
   			$table = Amazon_EcsProcess::cron_load_amazon_asin_lookup();
   			//字段检测
   			Common_Common::checkTableColumnExist('amazon_lookup', 'company_code');
   			
   			$sql = '';
    		$sql.= " SELECT a.asin1,a.seller_sku,a.user_account,a.company_code from amazon_merchant_listing a LEFT JOIN amazon_lookup b on a.asin1=b.asin and a.user_account=b.user_account  and a.company_code=b.company_code where 1=1";
    		$sql.= " and a.company_code='{$this->company_code}'";
    		$sql.= " and a.user_account='{$this->account}'";
    		$sql.= " and a.item_status='on_sale'";
    		$sql.= " and b.asin is null";
    		Common_ApiProcess::log('创建Amazon下载产品图片与产品链接任务' . $sql);
    		
    		//Common_Common::query($sql);
    		$data = Common_Common::fetchAll($sql);
    		$db = Common_Common::getAdapter();
    		foreach($data as $v){
    			try {
    				$sql = "select * from {$table} where asin='{$v ['asin1']}' and user_account='{$v ['user_account']}' and company_code='{$v ['company_code']}';";    				
    				$exist = Common_Common::fetchRow($sql);
    				if(!$exist){
    					$arr = array (
    							'asin' => $v ['asin1'],
    							'sku' => $v ['seller_sku'],
    							'user_account' => $v ['user_account'],
    							'company_code' => $v ['company_code']
    					);
    					$db->insert($table,$arr);
    				}
    				
    			} catch (Exception $e) {
    				Common_ApiProcess::log($e->getMessage().'=======================================');
    				
    			}
				
			}
            $this->countLoad($loadId, 2, count($data)); // 运行结束
    		return array(
    				'ask' => 1,
    				'message' => "创建Amazon下载产品图片与产品链接任务-->" . $this->account
    		);
    	}catch(Exception $e){
    		$this->countLoad($loadId, 3, 0); // 运行异常
    		Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), 'amazonListing1RequestReport');
    		return array(
    				'ask' => 0,
    				'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
    		);
    	}
    	
    }
    /**
     * 在售产品
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazonListing1RequestReport($loadId)
    {
        try{
            $this->_amazonInit($loadId);
            Common_ApiProcess::log('创建报告' . $this->account);
            
            // 逻辑处理
            $rs = $this->_service->requestReport();
            if($rs['ask'] == 1){
                $this->countLoad($loadId, 2, 0); // 运行结束
            }else{
                throw new Exception(print_r($rs['exception'], true));
            }
            
            return array(
                'ask' => 1,
                'message' => "数据提交到Amazon-->" . $this->account
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), 'amazonListing1RequestReport');
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 在售产品
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazonListing2GetRequestReportList($loadId)
    {
        try{
            $this->_amazonInit($loadId);
            Common_ApiProcess::log('开始下载报告列表' . $this->account);
            // 逻辑处理
            // 不传递结束时间（建议）
            $this->end = null;
            $rs = $this->_service->getReportRequestList($this->start, $this->end);
            
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
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), 'amazonListing1RequestReport');
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 在售产品
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazonListing3GetReport($loadId)
    {
        try{
            $this->_amazonInit($loadId);
            Common_ApiProcess::log('开始下载报告' . $this->account);
            // 逻辑处理
            
            $this->_service->getAllReportNotLoadNew();
            
            $this->countLoad($loadId, 2, 0); // 运行结束
            
            return array(
                'ask' => 1,
                'message' => "报告下载完毕" . $this->account
            );
        }catch(Exception $e){
            $this->countLoad($loadId, 3, 0); // 运行异常
            Ec::showError("账号：" . $this->account . '发生错误,错误原因：' . $e->getMessage(), 'amazonListing1RequestReport');
            return array(
                'ask' => 0,
                'message' => "账号：" . $this->account . '发生错误错误原因：' . $e->getMessage()
            );
        }
    }

    /**
     * 在售产品(简版)
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazonListing1RequestReportLite($loadId)
    {
        $this->_setReportType('_GET_MERCHANT_LISTINGS_DATA_LITE_');
        return $this->amazonListing1RequestReport($loadId);
    }

    /**
     * 在售产品(简版)
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazonListing2GetRequestReportListLite($loadId)
    {
        $this->_setReportType('_GET_MERCHANT_LISTINGS_DATA_LITE_');
        return $this->amazonListing2GetRequestReportList($loadId);
    }

    /**
     * 在售产品(简版)
     *
     * @param unknown_type $loadId            
     * @return multitype:string
     */
    public function amazonListing3GetReportLite($loadId)
    {
        $this->_setReportType('_GET_MERCHANT_LISTINGS_DATA_LITE_');
        return $this->amazonListing3GetReport($loadId);
    }


    /**
     * 可售产品(简版)
     *
     * @param unknown_type $loadId
     * @return multitype:string
     */
    public function amazonListing1RequestReportBackCompat($loadId)
    {
        $this->_setReportType('_GET_MERCHANT_LISTINGS_DATA_BACK_COMPAT_');
        return $this->amazonListing1RequestReport($loadId);
    }
    
    /**
     * 可售产品(简版)
     *
     * @param unknown_type $loadId
     * @return multitype:string
     */
    public function amazonListing2GetRequestReportListBackCompat($loadId)
    {
        $this->_setReportType('_GET_MERCHANT_LISTINGS_DATA_BACK_COMPAT_');
        return $this->amazonListing2GetRequestReportList($loadId);
    }
    
    /**
     * 可售产品(简版)
     *
     * @param unknown_type $loadId
     * @return multitype:string
     */
    public function amazonListing3GetReportBackCompat($loadId)
    {
        $this->_setReportType('_GET_MERCHANT_LISTINGS_DATA_BACK_COMPAT_');
        return $this->amazonListing3GetReport($loadId);
    }
    
    public static function formatReport($reportId){
        $report = Service_AmazonReportData::getByField($reportId,'ReportId');
        if($report){
            $data = Amazon_Common::_formatText($report['data']);
            print_r($data);
        }else{
            echo '===no-record===';
        }
        
    }
    

    /**
     * 生成默认补货数(当下载Item时,调用该方法)
     * @param unknown_type $item_id
     */
    public static function genDefaultSupplyQty($acc,$sku){
        try{
            if(empty($acc) | empty($sku)){
                return;
            }
            $con = array(
                    'seller_sku' => $sku,
                    'user_account' => $acc
            );
            // 不调用该方法
            $merchantListing = Service_AmazonMerchantListing::getByCondition($con);
            if(! $merchantListing){
                return;
            }
            Common_ApiProcess::log("[{$acc}][{$sku}] 生成默认补货数");
            $sup_qty_arr = array();
            foreach($merchantListing as $v){
                $row = array(
                        'platform' => 'amazon',
                        'company_code' => $v['company_code'],
                        'user_account' => $v['user_account'],
                        'item_id' => $v['product_id'],
                        'sku' => $v['seller_sku'],
                        'qty' => $v['quantity'],
                        'add_time' => date('Y-m-d H:i:s')
                );
                $sup_qty_arr[] = $row;
            }
            
            foreach($sup_qty_arr as $sup_qty){
                $con = array(
                        'platform' => $sup_qty['platform'],
                        // 'company_code' => $sup_qty['company_code'],
                        'user_account' => $sup_qty['user_account'],
                        //                 'item_id' => $sup_qty['item_id'],
                        'sku' => $sup_qty['sku']
                );
                // 未设置补货数,初始化一个补货数量
                $exist = Service_SellerItemSupplyQty::getByCondition($con);
                if(empty($exist)){
                    $sup_qty['add_time'] = now();
                    $sup_qty['update_time'] = now();
                    $sup_qty['op_user_id'] = Service_User::getUserId();
                    $sup_qty['sync_status'] = '0';
                    $sup_qty['sync_time'] = '0000-00-00 00:00:00';
            
                    // 按照账号设置默认补货
                    $sql = "select a.* from platform_user_supply_set a inner join platform_user b on a.pu_id=b.pu_id where b.user_account='{$sup_qty['user_account']}';";
                    $supSet = Common_Common::fetchRow($sql);
                    // print_r($sql);exit;
                    if($supSet){
                        // 补货状态
                        $sup_qty['status'] = $supSet['status'];
                        if($supSet['supply_type'] == '1'){
                            $sup_qty['supply_type'] = $supSet['supply_type']; // 补货策略，1:按仓补货,.2:自定义补货数
                            $sup_qty['supply_warehouse'] = $supSet['supply_warehouse'];
                            //$sup_qty['qty'] = $supSet['supply_qty'];
                        }else{
                            // $sup_qty['supply_type'] = $supSet['supply_type'];//补货策略，1:按仓补货,.2:自定义补货数
                            $sup_qty['supply_type'] = '2';
                            $sup_qty['supply_warehouse'] = $supSet['supply_warehouse'];
                        }
            
                        Service_SellerItemSupplyQty::add($sup_qty);
                        $logContent = "Amazon补货数据初始化,初始化参数：\n" . print_r($sup_qty, true);
                        Service_SellerItemProcess::log($sup_qty['item_id'], $logContent, 1111);
                    }
                }
            }
        }catch (Exception $e){
            file_put_contents(APPLICATION_PATH.'/../data/log/___amazon_gen_default_sup_qty.txt', $e->getMessage());            
        }
        
    }
}