<?php
/**
 * ebay标记发货
 * @author Max
 *
 */
class Ebay_Order_CompleteSale
{
    //订单号
    private $_refrence_no_platform = '';
    //订单
    private $_order = null;
    //订单行
    private $_order_product = array();
    //原始订单号
    private $_OrderIdEbayArr = array();
    // 跟踪号
    private $_ShipmentTrackingNumber = '';
    // 运输方式
    private $_ShippingCarrierUsed = '';
    // 发货时间
    private $_ShippedTime = '';
    //ebay调用基类
    private $_svc = null;
    //ebay token
    private $_token = '';
    //错误信息
    private $_errArr = array();
    // 日志
    private $_logArr = array();
    // 订单是否经过修改,original原始订单，change修改过的订单,判断是否有不发，判断是否是拆分订单，合并订单
    private $_order_change_type = 'original';
    
    private $_complete_sale_error_code = 0;//错误代码

    public function __construct($refrence_no_platform)
    {
        $this->_refrence_no_platform = $refrence_no_platform;
        Common_Common::checkTableColumnExist('orders', 'complete_sale_error_code');
    }
    
    // public function test()
    // {
    // $config = array(
    // 'token' => '1111111111111111111111111',
    // 'devid' => Common_Company::getEbayDevid(),
    // 'appid' => Common_Company::getEbayAppid(),
    // 'certid' => Common_Company::getEbayCertid(),
    // 'serverurl' => Common_Company::getEbayServerurl(),
    // 'version' => Common_Company::getEbayVersion(),
    // 'siteid' => '0'
    // );
    // $svc = new Ebay_EbayLibTrading($config);
    // $param = array(
    // 'RequesterCredentials' => array(
    // 'eBayAuthToken' => $config['token']
    // ),
    // 'WarningLevel' => 'High',
    // 'ItemID' => 'op_ref_item_id',
    // 'TransactionID' => 'op_ref_tnx',
    // 'Paid' => 'true',
    // 'Shipped' => 'true',
    // 'Shipment' => array(
    // // 'ShipmentTrackingDetails' => array(
    // // array(
    // // 'ShipmentTrackingNumber' => 'ShipmentTrackingNumber',
    // // 'ShippingCarrierUsed' => 'ShipmentTrackingNumber'
    // // ),
    // // array(
    // // 'ShipmentTrackingNumber' => 'ShipmentTrackingNumber',
    // // 'ShippingCarrierUsed' => 'ShipmentTrackingNumber'
    // // )
    // // ),
    // 'ShipmentTrackingDetails' => array(
    // 'ShipmentTrackingNumber' => 'ShipmentTrackingNumber',
    // 'ShippingCarrierUsed' => 'ShipmentTrackingNumber'
    // ),
    // 'ShippedTime' => 'ShippedTime'
    // )
    // );
    // $requestXml = $svc->getXmlContent('CompleteSale', $param);
    
    // header('Content-Type:text/xml');
    // echo $requestXml;
    // exit();
    // }
    private function _getOrder()
    {
        $field = array(
            'user_account',
            'date_warehouse_shipping',
            'company_code',
            'shipping_method_no',
            'shipping_method',
            'refrence_no_platform',
            'order_status',
            'sync_status',
            'shipping_method_platform',
            'platform',
            'data_source',
            'carrier_name',
            'platform_ship_time',
            'complete_sale_error_code'
        );
        $this->_order = Service_Orders::getByField($this->_refrence_no_platform, 'refrence_no_platform', $field);
        if($this->_order){
            if($this->_order['order_status']=='0'){
                throw new Exception('已废弃订单不可标记发货');
            }
            $userAccount = $this->_order["user_account"];
            $companyCode = $this->_order["company_code"];
            $token = Ebay_EbayLib::getUserToken($userAccount, $companyCode);
            if(! $token){
                throw new Exception('账号禁用或者授权未完成');
            }
            $config = array(
                'token' => $token,
                'devid' => Common_Company::getEbayDevid(),
                'appid' => Common_Company::getEbayAppid(),
                'certid' => Common_Company::getEbayCertid(),
                'serverurl' => Common_Company::getEbayServerurl(),
                'version' => Common_Company::getEbayVersion(),
                'siteid' => '0'
            );
            $this->_svc = new Ebay_EbayLibTrading($config);
            $this->_token = $token;
        }
    }

    /**
     * 发货明细
     */
    private function _getOrderProduct()
    {
        $con = array(
            'OrderID' => $this->_refrence_no_platform,
            'give_up' => '0'
        );
        $order_product = Service_OrderProduct::getByCondition($con);
        foreach($order_product as $k => $v){
            // 排除非无item_id和tnx_id的订单行
            if(empty($v['op_ref_item_id']) || ! preg_match('/^[0-9]+$/', $v['op_ref_item_id']) || ! preg_match('/^[0-9]+$/', $v['op_ref_tnx'])){
                unset($order_product[$k]);
            }
            if(intval($v['give_up'])!=0){
                unset($order_product[$k]);
            }
            $this->_OrderIdEbayArr[] = $v['OrderIDEbay'];
        }
        //关联订单号
        $this->_OrderIdEbayArr = array_unique($this->_OrderIdEbayArr);
        foreach($this->_OrderIdEbayArr as $k=>$v){
            if(empty($v)){
                unset($this->_OrderIdEbayArr[$k]);
            }    
        }
        
        $this->_order_product = $order_product;
    }

    private function _getOrderChangeType()
    {
        if(! preg_match('/^[0-9]+(\-[0-9]+)?$/', $this->_refrence_no_platform)){
            $this->_order_change_type = 'change';
        }
    }

    /**
     * 获取标记发货运输方式
     *
     * @param unknown_type $this->_order            
     * @return string
     */
    private function _getShippingCarrierUsed()
    {
        if($this->_order['carrier_name']){ // 如果有承运商，使用承运商代码
            $this->_ShippingCarrierUsed = $this->_order['carrier_name'];
        }else{  

        	$sql = "select * from pbr_product_platform_map m where m.product_code = '{$this->_order['shipping_method']}' and m.platform = 'ebay';";
        	$row = Common_Common::fetchRow($sql);
        		
        	if(!empty($row)) {
        		$this->_ShippingCarrierUsed = $row['carrier_name'];
        	} else {
            	$this->_ShippingCarrierUsed = $this->_order['shipping_method_platform'];
        	}
        }
    }

    /**
     * 发货时间
     *
     * @param unknown_type $this->_order            
     * @return Ambigous <string, unknown_type>
     */
    private function _getShippedTime()
    {
        if($this->_order['platform_ship_time']){ // 有平台发货时间，取平台发货时间
            $this->_ShippedTime = $this->_order['platform_ship_time'];
        }else{
            // 时间格式化
            if(empty($this->_order['date_warehouse_shipping']) || strtotime($this->_order['date_warehouse_shipping']) < strtotime('2001-01-01')){
                // throw new Exception('发货时间不正确');
                $this->_ShippedTime = Ec_AutoRun::getEbayTime(date('Y-m-d H:i:s')); // 转换为eBay时间
                $this->_ShippedTime = date('Y-m-d\TH:i:s.000\Z', strtotime($this->_ShippedTime));
            }else{
                $this->_order['date_warehouse_shipping'] = Ec_AutoRun::getEbayTime($this->_order['date_warehouse_shipping']); // 转换为eBay时间
                $this->_ShippedTime = date('Y-m-d\TH:i:s.000\Z', strtotime($this->_order['date_warehouse_shipping']));
            }
        }
        return $this->_order;
    }

    /**
     * 跟踪号
     */
    private function _getShipmentTrackingNumber()
    {
        $this->_ShipmentTrackingNumber = empty($this->_order['shipping_method_no']) ? '' : $this->_order['shipping_method_no'];
    }

    private function _completeSaleOrder()
    {
        $param = array(
            'RequesterCredentials' => array(
                'eBayAuthToken' => $this->_token
            ),
            'WarningLevel' => 'Low',
            'OrderID' => $this->_refrence_no_platform,
//             'Paid' => 'true',
            'Shipped' => 'true',
            'Shipment' => array(
                'ShippedTime' => $this->_ShippedTime
            )
        );
        if($this->_ShipmentTrackingNumber && $this->_ShippingCarrierUsed){
            $param['Shipment']['ShipmentTrackingDetails'] = array(
                'ShipmentTrackingNumber' => $this->_ShipmentTrackingNumber,
                'ShippingCarrierUsed' => $this->_ShippingCarrierUsed
            );
        }

        $rs = $this->_svc->request('CompleteSale', $param);
        // 处理返回结果
        $this->_processRs($rs, $param);
    }

    private function _completeSaleOrderLine($p)
    {
        $param = array(
            'RequesterCredentials' => array(
                'eBayAuthToken' => $this->_token
            ),
            'WarningLevel' => 'Low',
//             'OrderID'=>$p['OrderIDEbay'],
            'ItemID' => $p['op_ref_item_id'],
            'TransactionID' => $p['op_ref_tnx'],
//             'Paid' => 'true',
            'Shipped' => 'true',
            'Shipment' => array(
                'ShippedTime' => $this->_ShippedTime
            )
        );
        if($this->_ShipmentTrackingNumber && $this->_ShippingCarrierUsed){
            $param['Shipment']['ShipmentTrackingDetails'] = array(
                'ShipmentTrackingNumber' => $this->_ShipmentTrackingNumber,
                'ShippingCarrierUsed' => $this->_ShippingCarrierUsed
            );
        }
        $rs = $this->_svc->request('CompleteSale', $param);
        // 处理返回结果
        $this->_processRs($rs, $param);
    }

    /**
     * 标记发货
     *
     * @return Ambigous <multitype:number string unknown , NULL, multitype:>
     */
    public function completeSale()
    {
        $return = array(
            'ask' => 0,
            'message' => '',
            'ref_id' => $this->_refrence_no_platform,
            'Ack' => 'Failure'
        );
        try{
            if(empty($this->_refrence_no_platform)){
                throw new Exception('参数订单号错误');
            }
            $this->_getOrder();
            $this->_getOrderProduct();
            $this->_getShippedTime();
            $this->_getShippingCarrierUsed();
            $this->_getShipmentTrackingNumber();
            
            if(empty($this->_order)){
                throw new Exception('订单不存在');
            }
            $this->_getOrderChangeType();
            
            if(empty($this->_svc)){
                throw new Exception('初始化程序异常');
            }
            //运单号重复
            if($this->_order['sync_status'] == 2 && $this->_order['complete_sale_error_code'] == '21916964'){//取消运单号
                $this->_ShipmentTrackingNumber = '';
                $this->_ShippingCarrierUsed = '';
            }
            if($this->_order_change_type == 'original'){ // 原始订单，整个订单标记发货
                $this->_completeSaleOrder();
            }else{//修改过的订单，如拆分订单，合并订单，需要按照item去标记发货
                if(empty($this->_order_product)){
                    throw new Exception('没有需要标记发货的订单明细',1);
                }
                
                foreach($this->_order_product as $p){
                    // 订单行标记发货
                    $this->_completeSaleOrderLine($p);
                    sleep(3);
                }
            }
            
            // 处理结果
            if(! empty($this->_errArr)){
                throw new Exception('订单标记发货失败');
            }
            $updateRow = array(
                'sync_status' => '1',
                'sync_time' => now()
            );
            Service_Orders::update($updateRow, $this->_refrence_no_platform, 'refrence_no_platform');
            
            if($this->_ShipmentTrackingNumber && $this->_ShippingCarrierUsed){
                $message = "订单标记发货，并同步到eBay成功->跟踪号上传成功[承运商：" . $this->_ShippingCarrierUsed . " - 跟踪号：" . $this->_ShipmentTrackingNumber . " - 标记发货时间：" . $this->_ShippedTime . "]";
            }else{
                $message = '订单标记发货，并同步到eBay成功->无跟踪号' . " - 标记发货时间：" . $this->_ShippedTime . "";
            }
            
            $logRow = array(
                'ref_id' => $this->_refrence_no_platform,
                'log_content' => $message,
                'data' => implode("\n", $this->_logArr),
                'op_id' => ''
            );
            Service_OrderProcess::writeOrderLog($logRow);
            
            try{
                // 加入订单更新任务列表
                $table = Ebay_EbayServiceCommon::table_cron_load_ebay_order();
                foreach($this->_OrderIdEbayArr as $o_sn){
                    $arr = array(
                            'order_sn' => $o_sn,
                            'user_account' => $this->_order['user_account']
                    );
                    $db = Common_Common::getAdapter();
                    $sql = "select * from {$table} where order_sn='{$o_sn}';";                    
                    $exist = Common_Common::fetchRow($sql);
                    if(!$exist){
                        $db->insert($table, $arr);
                    }                    
                }                
            }catch(Exception $ee){}
            
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['Ack'] = 'Success';
        }catch(Exception $e){            
            $return['message'] = $e->getMessage();
            if($e->getCode()==1){//'没有需要标记发货的订单明细'
                $updateRow = array(
                        'sync_status' => '1',
                        'sync_time' => now()
                );
                Service_Orders::update($updateRow, $this->_refrence_no_platform, 'refrence_no_platform');
            }else{
                $updateRow = array(
                        'sync_status' => '2',
                        'complete_sale_error_code'=>$this->_complete_sale_error_code,
                        'sync_time' => now()
                );
                
                $errors = $this->_getCompleteSaleError();
                //错误原因
                $message = $errors[$this->_complete_sale_error_code];
                Service_Orders::update($updateRow, $this->_refrence_no_platform, 'refrence_no_platform');
                $logRow = array(
                        'ref_id' => $this->_refrence_no_platform,
                        'log_content' => $e->getMessage() . ",".$message.",订单标记发货同步参数[承运商：" . $this->_ShippingCarrierUsed . " - 跟踪号：" . $this->_ShipmentTrackingNumber . " - 标记发货时间：" . $this->_ShippedTime . "]",
                        'data' => implode("\n", $this->_logArr),
                        'op_id' => '1002'
                );
                Service_OrderProcess::writeOrderLog($logRow);
            }
            
            
        }
        $return['err'] = $this->_errArr;
        $return['logArr'] = $this->_logArr;
        return $return;
    }

    private function _getCompleteSaleError(){
        $errors = array(
            '12006' => '订单已被删除',
            '21916964' => '订单运单号被占用,下次将取消运单号进行标记发货',
            '21919089' => '订单已经标记发货了',
            '20822' => 'Invalid ItemID or TransactionID'
        );
        return $errors;
    }
    /**
     * 对响应结果进行处理
     *
     * @param unknown_type $rs            
     * @param unknown_type $param            
     */
    private function _processRs($rs, $param)
    {
        unset($param['RequesterCredentials']);
        $log = "请求数据:\n" . print_r($param, true) . "\n响应结果:\n" . print_r($rs, true);
        $this->_logArr[] = $log; // 用来记录到日志表
        //错误代码
        $this->_complete_sale_error_code = 0;
        Ec::showError($log, __CLASS__ . __FUNCTION__ . 'completeEbayOrder_');
        // 避免API返回空值
        $return['Ack'] = isset($rs['CompleteSaleResponse']['Ack']) ? $rs['CompleteSaleResponse']['Ack'] : 'Failure';
        if($return['Ack'] != 'Failure'){
            // 标记成功
        }else{
            // 该文件需要定时发送邮件======================================
            Ec::showError($log, 'completeEbayOrder_fail_');
            // 记录错误信息            
            $message = '订单标记发货异常,';
            $errorCode = isset($rs['CompleteSaleResponse']['Errors']['ErrorCode']) ? $rs['CompleteSaleResponse']['Errors']['ErrorCode'] : '';
            //错误代码
            $this->_complete_sale_error_code = $errorCode;
            $errors = $this->_getCompleteSaleError();
            if(!empty($errors[$errorCode])){
                $message = $errors[$errorCode];
            }
//             switch($errorCode){
//                 case '12006':
//                     $updateRow['sync_status'] = '5'; // 订单已被删除
//                     $message .= '订单已被删除';
//                     break;
//                 case '21916964':
//                     $updateRow['sync_status'] = '3'; // 数据异常，运单号被占用
//                     $message .= '订单运单号被占用';
//                     break;
//                 case '21919089':
//                     $updateRow['sync_status'] = '1'; // 已经标记发货了。。。
//                     $message = '订单已经标记发货了';
//                     break;
//                 case '20822':
//                     $updateRow['sync_status'] = '6'; // Invalid ItemID or TransactionID
//                     $message .= 'Invalid ItemID or TransactionID';
//                     break;
//             }
            // 日志
            // 抛出异常
            $this->_errArr[] = $message . "\n标记信息\n" . print_r($param, true) . "\n返回信息：\n" . print_r($rs['CompleteSaleResponse']['Errors'], true);
        }
    }
}