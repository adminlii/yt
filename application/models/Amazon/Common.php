<?php
/**
 * 自动任务控制初始化===============================
 * @author Administrator
 *
 */
class Amazon_Common
{

    public static function cron_load_amazon_get_my_price_for_sku()
    {
		$db = Common_Common::getAdapter ();
		$table = 'cron_load_amazon_get_my_price_for_sku';
		$sql = "show tables like '{$table}';";
		$exist = $db->fetchRow ( $sql );
		if (! $exist) {
			$sql = "
        	CREATE TABLE if not exists `{$table}` (
        	`id` int(11) NOT NULL AUTO_INCREMENT,
        	`company_code` varchar(200) NOT NULL,
        	`user_account` varchar(60) NOT NULL,
        	`seller_sku` varchar(100) NOT NULL DEFAULT '' comment 'insert into {$table}(company_code,user_account,seller_sku) select company_code,user_account,seller_sku from amazon_merchant_listing order by user_account;',
        	PRIMARY KEY (`id`),
        	KEY `user_account` (`user_account`)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='下载Amazon销售价';
        	";
			$db->query ( $sql );
		}
		
		return $table;
	}

    /**
     * 格式化text为数组
     *
     * @param unknown_type $text            
     * @return multitype:multitype:string unknown
     */
    public static function _formatText($text)
    {
        $text = preg_replace('/\n$/', '', $text);
        $text = explode("\n", $text);
        $data = array();
        $header = array();
        foreach($text as $k => $v){
            $v = preg_replace('/\n$/', '', $v);
            if($k == 0){
                $line = $v;
                $line = explode("\t", $line);
                $header = $line;
                // print_r($header);
                // exit();
            }elseif(! empty($v)){
                $line = $v;
                $line = explode("\t", $line);
                $tmp = array();
                foreach($line as $kk => $vv){
                    $header[$kk] = str_replace('-', '_', $header[$kk]);
                    $tmp[$header[$kk]] = $vv;
                }
                $data[$k] = $tmp;
            }
        }
        return $data;
    }
    /**
     * 初始化账号自动运行任务
     * @param unknown_type $userAccount
     * @param unknown_type $platform
     */
    public static function accountRunControlInit($userAccount,$platform,$status=1,$company_code=''){
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        $methods = get_class_methods('Amazon_Common');
        foreach($methods as $k => $v){
            if(! preg_match('/Switch$/', $v)){
                unset($methods[$k]);
            }
        }
        $company_code = empty($company_code)?Common_Company::getCompanyCode():$company_code;
        $platform = strtolower($platform);
        $obj = new Amazon_Common();
        foreach($methods as $method){
            if(preg_match('/^' . $platform . '/i', $method)){
                $rs = $obj->$method($platform, $userAccount, $status,$company_code );
                $return['rs'][] = $rs;
            }
        }
        $return['message'] = 'Success';
        $return['ask'] = 1;
        return $return;
    }
    /**
     * 修改自动运行状态
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @param unknown_type $run_app_arr            
     * @throws Exception
     */
    private static function _modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr, $run_interval_minute = 60)
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.',
            'run_app_arr'=>$run_app_arr
        );
        try{
            // 验证账号
            $con = array(
                // 'status' => 1,
                'platform' => $platform,
                'user_account' => $acc,
                'company_code' => $company_code
            );
            $resultPlatformUser = Service_PlatformUser::getByCondition($con);
            if(empty($resultPlatformUser)){
                throw new Exception('账号不存在-->' . $acc);
            }
            
            $run_interval_minute = empty($run_interval_minute) ? 60 : $run_interval_minute;
            $run_interval_minute = preg_match('/^[0-9]+$/', $run_interval_minute) ? $run_interval_minute : 60;
            
            foreach($run_app_arr as $run_app){
                $con = array(
                    'platform' => $platform,
                    'user_account' => $acc,
                    //'company_code' => $company_code,
                    'run_app' => $run_app
                );
                $exists = Service_RunControl::getByCondition($con);
                
                if($status == '1'){ // 启用
                    $status = '1';
                }else{ // 禁用
                    $status = '0';
                }
                if(empty($exists)){
                    $row = array(
                        'company_code' => $company_code,
                        'platform' => $platform,
                        'user_account' => $acc,
                        'run_app' => $run_app,
                        'run_interval_minute' => $run_interval_minute,
                        'start_time' => '00:00:00',
                        'end_time' => '24:00:00',
                        'last_run_time' => date('Y-m-d H:i:s', strtotime('-60days')),
                        'status' => $status
                    );
                    Service_RunControl::add($row);
                }else{
                    $updateRow = array(
                        'status' => $status
                    );
                    foreach($exists as $exist){
                        Service_RunControl::update($updateRow, $exist['run_id'], 'run_id');
                    }
                }
            }
            
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            Amazon_Service::log($e->getMessage());
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 获取在售商品报告
     * 开启/关闭
     *
     * @param unknown_type $acc            
     * @param unknown_type $status            
     */
    public static function Amazon__GET_MERCHANT_LISTINGS_DATA_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'amazonListing1RequestReport',
            'amazonListing2GetRequestReportList',
            'amazonListing3GetReport'
        );
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 可售商品报告精简版
     * 开启/关闭
     *
     * @param unknown_type $acc            
     * @param unknown_type $status            
     */
    public static function Amazon__GET_MERCHANT_LISTINGS_DATA_LITE_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'amazonListing1RequestReportLite',
            'amazonListing2GetRequestReportListLite',
            'amazonListing3GetReportLite'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 上传商品报价
     * 开启/关闭
     *
     * @param unknown_type $acc            
     * @param unknown_type $status            
     */
    public static function Amazon__POST_PRODUCT_PRICING_DATA_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'amazon1SubmitFeed',
            'amazon2GetFeedSubmissionList',
            'amazon3GetFeedSubmissionResult'
        );
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 上传商品库存
     * 开启/关闭
     *
     * @param unknown_type $acc            
     * @param unknown_type $status            
     */
    public static function Amazon__POST_INVENTORY_AVAILABILITY_DATA_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'submitInventoryFeed',
            'getInventoryFeedSubmissionList',
            'getInventoryFeedSubmissionResult'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载产品图片与链接
     *
     * @param unknown_type $acc
     * @param unknown_type $status
     */
    public static function Amazon__amazonListingEcs_Switch($platform, $acc, $status, $company_code = '')
    {
    	$run_app_arr = array(
    			'amazonListingEcs'
    	);
    
    	return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }
    
    /**
     * Amazon下载订单明细
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Amazon_callListOrderItems_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'callListOrderItems'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * Amazon下载订单头
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Amazon_callListOrders_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'callListOrders'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * amazon标记发货
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Amazon_callOrderFulfillment_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'callOrderFulfillment'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * amazon标记发货结果
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Amazon_callOrderFulfillmentResult_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'callOrderFulfillmentResult'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * ebay下载Feedback
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Ebay_loadEbayFeedback_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'loadEbayFeedback'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    
    /**
     * 下载ebay产品
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Ebay_loadEbayItem_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'loadEbayItem',
            'loadEbayItemEnd',
            'loadEbayItemActive'
        );        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载ebay消息
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Ebay_loadEbayMessage_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'loadEbayMessage'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载ebay订单
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Ebay_loadEbayOrder_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'loadEbayOrder'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载ebay纠纷
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Ebay_loadEbayUserCases_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'loadEbayUserCases'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载paypal交易明细
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Paypal_callTransactionDetail_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'callTransactionDetail'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载paypal交易主信息
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Paypal_callTransactionSearch_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'callTransactionSearch'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载shopify订单
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Shopify_loadShopifyOrder_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'loadShopifyOrder'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 下载shopify订单
     *
     * @param unknown_type $platform
     * @param unknown_type $acc
     * @param unknown_type $status
     * @param unknown_type $company_code
     * @return multitype:number string NULL
     */
    public static function Shopify_loadShopifyProduct_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
                'loadShopifyProduct'
        );
    
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }
    /**
     * 下载zendesk的Ticket
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Zendesk_loadZendeskTicket_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'loadZendeskTicket'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }
    

    /**
     * 修改ebay的商品在线数量
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Ebay_reviseInventory_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'reviseEbayInventory'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 修改shopify的商品在线数量
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Shopify_reviseInventory_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'reviseShopifyInventory'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    

    /**
     * 修改ebay的商品价格
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Ebay_revisePrice_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'reviseEbayPrice'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 修改shopify的商品价格
     *
     * @param unknown_type $platform            
     * @param unknown_type $acc            
     * @param unknown_type $status            
     * @param unknown_type $company_code            
     * @return multitype:number string NULL
     */
    public static function Shopify_revisePrice_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'reviseShopifyPrice'
        );
        
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }

    /**
     * 修改shopify的上下架
     *
     * @param unknown_type $platform
     * @param unknown_type $acc
     * @param unknown_type $status
     * @param unknown_type $company_code
     * @return multitype:number string NULL
     */
    public static function Shopify_revisePublishStatus_Switch($platform, $acc, $status, $company_code = '')
    {
        $run_app_arr = array(
            'revisePublishStatusDown',
            'revisePublishStatusUp',
        );
    
        return self::_modifyRunControl($platform, $acc, $status, $company_code, $run_app_arr);
    }
}