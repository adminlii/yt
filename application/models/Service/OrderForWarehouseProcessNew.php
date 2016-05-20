<?php
class Service_OrderForWarehouseProcessNew
{
    private $_client = null;
    private $_soapClient = null;
    private $_domain = '';
    private $_abnormal = 1;//是否强制审核
    private $_customerCode = 'EC001';
    private $_orderStatus = '4';

    /**
     * 获取wms的某些信息,请查阅WMS系统的user_system表
     * @throws Exception
     * @return unknown
     */
    public static function getSystemWms(){
        $wms_db = Zend_Registry::get('wms_db');
        $db = Common_Common::getAdapter();
        $sql = "select * from {$wms_db}.user_system where us_code='WMS';";
        $rs = $db->fetchRow($sql);
        if(!$rs){
            throw new Exception("{$wms_db}.user_system error");
        } 
        return $rs;
    }
    public static function getWmsUrl(){
        //获取wms的某些信息,请查阅WMS系统的user_system表
        $rs = Service_OrderForWarehouseProcessNew::getSystemWms();
        return $rs['us_url'];
    }

    /**
     * @desc 设置Client
     */
    public function setClient()
    {
        //获取wms的某些信息,请查阅WMS系统的user_system表
        $rs = Service_OrderForWarehouseProcessNew::getSystemWms();
//         print_r($rs);exit;
//         $config = Zend_Registry::get('config')->toArray();
        $this->_domain = trim($rs['us_url'],'/'). "/default/rest/service"; 
//         echo $this->_domain;exit;
        $client = new Zend_Rest_Client ($this->_domain );
        $client->getHttpClient()->setConfig(array('keepalive'=>true,'timeout'=>6000));//设定超时 
        $this->_client = $client;
    }
    public function getClient(){
        if(!$this->_client){
            $this->setClient();
        }
        return $this->_client;
    }
    
    public function getSoapClient(){
        if(!$this->_soapClient){
            $this->setSoapClient();
        }
        return $this->_soapClient;
    }

    /**
     * @desc 设置Client
     */
    public function setSoapClient()
    {
        // 获取wms的某些信息,请查阅WMS系统的user_system表
        $rs = Service_OrderForWarehouseProcessNew::getSystemWms();
        
        $wsdl = trim($rs['us_url'], '/') . "/default/svc/wsdl";
        $wsdl_file = trim($rs['us_url'], '/') . "/default/svc/wsdl-file?wsdl";
        
        $omsConfig = array(
            'active' => '1',
            'appToken' => 'df3c89455ff73a1d67d7ad5ad6598eb3',
            'appKey' => 'dea70dff3ce494c965346de0199086cc',
            'timeout' => '6000',
            'wsdl' => $wsdl,
            'wsdl-file' => $wsdl_file
        );
        
        $wsdl = $omsConfig['wsdl'];
        $this->_appToken = $omsConfig['appToken'];
        $this->_appKey = $omsConfig['appKey'];
        // 超时
        $timeout = isset($omsConfig['timeout']) && is_numeric($omsConfig['timeout']) ? $omsConfig['timeout'] : 1000;
        
        $streamContext = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true
            ),
            'socket' => array()
        ));
        
        $options = array(
            "trace" => true,
            "connection_timeout" => $timeout,
            "encoding" => "utf-8"
        );
        
        $client = new SoapClient($wsdl, $options);
        $this->_soapClient = $client;
    }
    
    /**
     * @发送订单到仓库
     * @param string $orderCode
     * $param boolean $force 是否异常同步订单可重复尝试发送
     * @return array state
     */
    public function submit($refNos)
    {
        return $this->sendWarehouseBatch($refNos);
    }

    public function validateWarehouseShipping($orderRow){
        $db = Common_Common::getAdapter();
        //         print_r($orderRow);exit;
        if(empty($orderRow['warehouse_id'])){
            throw new Exception('订单未分配仓库');
        }
        
        if(empty($orderRow['shipping_method'])){
            throw new Exception('订单未分配运输方式');
        }
        
        
        $whRow = Service_Warehouse::getByField($orderRow['warehouse_id']);
        if(empty($whRow)){
            throw new Exception('仓库ID:' . $orderRow['warehouse_id'] . '不存在.');
        }
        
        $wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
        
        $sql = 'select b.* from '.$wms_db.'.shipping_method_settings a inner join '.$wms_db.'.shipping_method b on a.sm_id=b.sm_id where a.warehouse_id='.$orderRow['warehouse_id'].' and b.sm_code="'.$orderRow['shipping_method'].'" and b.sm_status=1';
        //                 echo $sql;exit;
        $shippings = $db->fetchAll($sql);
        if(empty($shippings)){
            throw new Exception('该仓库不支持该运输方式，请重新指定仓库与运输方式');
        }
        $orderRow['warehouse_code'] = $whRow['warehouse_code'];
        return $orderRow;
    }
    
    /**
     * 获取订单产品信息
     * @param unknown_type $orderRow
     * @param unknown_type $give_up
     * @throws Exception
     * @return multitype:multitype:number unknown  multitype:number unknown Ambigous <>
     */
    private function _getOrderProduct($orderRow,$give_up='0'){
        $orderProduct = array();
        $con = array(
                'order_id' => $orderRow['order_id'],
                'give_up' => $give_up,
        );
        $orderProductRows = Service_OrderProduct::getByCondition($con, '*');
        
        foreach($orderProductRows as $key => $product){
            if($orderRow['date_paid_platform'] == '0000-00-00 00:00:00'||empty($orderRow['date_paid_platform'])){
                $orderRow['date_paid_platform'] = $product['op_ref_paydate'];
            }
            if($product['op_ref_paydate'] == '0000-00-00 00:00:00'||empty($product['op_ref_paydate'])){
                $product['op_ref_paydate'] = $orderRow['date_paid_platform'];
            }
            //设置默认值
            $product['op_ref_item_id'] = empty($product['op_ref_item_id'])?'0':$product['op_ref_item_id'];
            $product['op_ref_tnx'] = empty($product['op_ref_tnx'])?'0':$product['op_ref_tnx'];
            $product['op_recv_account'] = empty($product['op_recv_account'])?'':$product['op_recv_account'];
            $product['buyer_id'] = empty($product['buyer_id'])?'':$product['buyer_id'];
        
            if(empty($product['op_recv_account'])&&!empty($product['op_ref_item_id'])){//取得收款账号
                $sellerItem = Service_SellerItem::getByField($product['op_ref_item_id'],'item_id');
                if($sellerItem){
                    $product['op_recv_account'] = $sellerItem['paypal_email_address'];
        
                    $u = array('op_recv_account'=>$sellerItem['paypal_email_address']);
                    Service_OrderProduct::update($u, $product['op_id'],'op_id');
                }
            }
        
            $unit_finalvaluefee = empty($product['unit_finalvaluefee'])?0:$product['unit_finalvaluefee'];
            $unit_price = empty($product['unit_price'])?0:$product['unit_price'];
            $unit_platformfee = empty($product['unit_platformfee'])?0:$product['unit_platformfee'];
            $unit_shipfee = empty($product['unit_shipfee'])?0:$product['unit_shipfee'];
        
            $conn = array(
                    'product_sku' => $product['product_sku']
            );
            $combRows = Service_ProductCombineRelationProcess::getRelation($product['product_sku'],$orderRow['user_account']);
            
            if($combRows){
                $combCount = 0;//组合产品总个数
                foreach($combRows as $row){ // 组合产品
                    $combCount+= $row['pcr_quantity'];
                }        
        
                foreach($combRows as $row){ // 组合产品
                    $sub_unit_finalvaluefee = $unit_finalvaluefee*$row['pcr_percent']/100;//预先设置的比例
                    $sub_unit_price = $unit_price*$row['pcr_percent']/100;//预先设置的比例
                    $sub_unit_platformfee = $unit_platformfee*$row['pcr_percent']/100;//预先设置的比例
                    $sub_unit_shipfee = $unit_shipfee*$row['pcr_percent']/100;//预先设置的比例
                    //全部为大写
                    $key = trim(strtoupper($row['pcr_product_sku']));
                    if(isset($orderProduct[$key])){
                        $orderProduct[$key]['quantity'] += $product['op_quantity'] * $row['pcr_quantity'];
                        $orderProduct[$key]['finalvaluefee'] += ($sub_unit_finalvaluefee)*$product['op_quantity'] * $row['pcr_quantity'];
                        $orderProduct[$key]['TransactionPrice'] += ($sub_unit_price)*$product['op_quantity'] * $row['pcr_quantity'];
                        $orderProduct[$key]['paypalFee'] += ($sub_unit_platformfee)*$product['op_quantity'] * $row['pcr_quantity'];
                        $orderProduct[$key]['shipFee'] += ($sub_unit_shipfee)*$product['op_quantity'] * $row['pcr_quantity'];
                    }else{
                        $orderProduct[$key] = array(        
                                'product_sku'=>$product['product_sku'],
                                'sku' => $row['pcr_product_sku'],
                                'quantity' => $product['op_quantity'] * $row['pcr_quantity'],
                                'refTnx' => $product['op_ref_tnx'],
                                'refItemId' => $product['op_ref_item_id'],
                                'refBuyerId' => $orderRow['buyer_id'],
                                'refPayDate' => $product['op_ref_paydate'],
        
                                'finalvaluefee'=>($sub_unit_finalvaluefee)*$product['op_quantity'] * $row['pcr_quantity'],//成交费用
                                'TransactionPrice'=>($sub_unit_price)*$product['op_quantity'] * $row['pcr_quantity'], //销售价格
                                'paypalFee'=>($sub_unit_platformfee)*$product['op_quantity'] * $row['pcr_quantity'], //平台费
                                'shipFee'=>($sub_unit_shipfee)*$product['op_quantity'] * $row['pcr_quantity'], //运费
        
                                'recvAccount'=>$product['op_recv_account']
                        );
                    }
                }
            }else{
                $key = trim(strtoupper($product['product_sku']));
                if(isset($orderProduct[$key])){
                    $orderProduct[$key]['quantity'] += $product['op_quantity'];
                    $orderProduct[$key]['finalvaluefee'] += ($unit_finalvaluefee)*$product['op_quantity'];
                    $orderProduct[$key]['TransactionPrice'] += ($unit_price)*$product['op_quantity'];
                    $orderProduct[$key]['paypalFee'] += ($unit_platformfee)*$product['op_quantity'];
                    $orderProduct[$key]['shipFee'] += ($unit_shipfee)*$product['op_quantity'];
                }else{
                    $orderProduct[$key] = array(
                            'product_sku'=>$product['product_sku'],
                            'sku' => $product['product_sku'],
                            'quantity' => $product['op_quantity'],
                            'refTnx' => $product['op_ref_tnx'],
                            'refItemId' => $product['op_ref_item_id'],
                            'refBuyerId' => $orderRow['buyer_id'],
                            'refPayDate' => $product['op_ref_paydate'],

                            'finalvaluefee'=>($unit_finalvaluefee)*$product['op_quantity'],//成交费用
                            'TransactionPrice'=>($unit_price)*$product['op_quantity'], //销售价格
                            'paypalFee'=>($unit_platformfee)*$product['op_quantity'],//平台费
                            'shipFee'=>($unit_shipfee)*$product['op_quantity'], //运费
                            'recvAccount'=>$product['op_recv_account']
                    );
                }
            }
        }
        return $orderProduct;
    }
    /**
     * 订单数据整合
     * @param unknown_type $orderCode
     * @throws Exception
     * @return array
     */
    public function getOrderIntegrateNew($orderCode,$validate=true){
    
        $db = Common_Common::getAdapter();
        $orderRow = Service_Orders::getByField($orderCode, 'refrence_no_platform');

        if(empty($orderRow)){
            throw new Exception('订单不存在');
        }
        if($orderRow['platform']!='ebay'||$orderRow['order_type']=='line'||$orderRow['create_type']=='hand'||$orderRow['create_type']=='upload'){
            Service_OrderProductProcess::updateOrderProductUnitPriceFinalValueFee($orderCode);
        }else if(!preg_match('/^[0-9\-]$/', $orderRow['refrence_no_platform'])){//拆单 或者合单
            Service_OrderProductProcess::updateOrderProductUnitPriceFinalValueFee($orderCode);
        }else{
            Service_OrderProductProcess::updateOrderProductUnitPriceFinalValueFee($orderCode);
        }
        
        if($validate){
            $orderRow = $this->validateWarehouseShipping($orderRow);            
        }

        $addressRow = Service_ShippingAddress::getByField($orderCode, 'OrderID');
        if(empty($addressRow)){
            throw new Exception('地址信息不存在.');
        }
        if(strtoupper($addressRow['Country'])=='AA'){
            throw new Exception('[APO]地址，请重新确认国家.');
        }
        $customerCode = $this->_customerCode;
        if(!empty($orderRow['customer_id'])){
            // 调用WMS客户表
            $customerObj = new Table_Customer();
            $customerRow = $customerObj->getByField($orderRow['customer_id'], 'customer_id', array(
                    'customer_id',
                    'customer_code'
            ));
            if(empty($customerRow)){
                throw new Exception('客户不存在.');
            }
            $customerCode = $customerRow['customer_code'];
        }
        
        //检查是否为线下订单，查询原始订单的跟踪号
        if($orderRow['platform'] == 'ebay' && $orderRow['order_type'] == 'resend' && $orderRow['create_type'] == 'hand' && !empty($orderRow['refrence_no'])){
        	$result_rma_order = Service_RmaOrders::getByField($orderRow['refrence_no'],'rma_id');
        	if(!empty($result_rma_order) && $result_rma_order['rma_refund_type'] == '-1' && !empty($result_rma_order['rma_back_order_id'])){
        		$resend_order = Service_Orders::getByField($result_rma_order['rma_back_order_id'],'order_id');
        		if(!empty($resend_order)){
        			$orderRow['ref_tracking_number'] = $resend_order['shipping_method_no'];
        		}
        	}
        }else if($orderRow['platform'] == 'aliexpress' && 
        		($orderRow['order_type'] == 'line' && $orderRow['create_type'] == 'upload') ||
        		($orderRow['order_type'] == 'sale' && $orderRow['create_type'] == 'api')){
        	//检查速卖通订单，需要传入速卖通原始单号 
        	$orderRow['ref_tracking_number'] = $orderRow['refrence_no'];
        }
        
        $addressRow['doorplate'] = trim($addressRow['doorplate']);
        
        //拆分门牌号 start
        if(empty($addressRow['doorplate'])){
            $con = array('country_code'=>$orderRow['consignee_country'],'sm_code'=>$orderRow['shipping_method']);            
            $doorplatRuleExist = Service_DoorplateRuleOpration::getByCondition($con);
           
            if(!empty($doorplatRuleExist)){
                $add = $addressRow['Street1'].' '.$addressRow['Street2'].' '.$addressRow['Street3'];
                $add = preg_replace('/\s+/', ' ', $add);
                $add = trim($add);
                if(preg_match('/[0-9]+/', $add)){
                    $prefix = 1;//1,从第一个数字开始，2，从最后一个数字开始
                    
                    if($prefix==1){
                        if(preg_match('/[0-9]+(.*)?$/', $add,$m)){
                            $doorplate = $m[0];
                        }
                        
                    }else{
                        if(preg_match_all('/[0-9]+/', $add,$m)){
                            //取最后一个数字开始
                            $tmp = array_pop($m[0]);
                            
                            preg_match('/'.$tmp.'([^0-9]+)?$/', $add,$mm);
                            $doorplate = $mm[0];
                        }
                    }
                    $newAdd = str_replace($doorplate, '', $add);
                    $updateRow = array(
                            'Street1' => $newAdd,
                            'Street2' => '',
                            'Street3' => '',
                            'doorplate' => $doorplate,
                            'is_modify'=> '1'
                    );
//                     if($orderCode=='WEC1405300040'){//测试
//                         print_r($updateRow);exit;
//                     }
                    Service_ShippingAddress::update($updateRow,$orderCode, 'OrderID');
                    // 这里还有日志信息，以后添加
                    $logRow = array(
                            'ref_id' => $orderCode,
                            'log_content' => '订单从地址中拆分门牌号,地址 from:['.$add."] to [{$newAdd}],门牌号为[{$doorplate}]",
                            'op_id' => ''
                    );
                    Service_OrderProcess::writeOrderLog($logRow);
                    
                    $addressRow['Street1'] = $newAdd;
                    $addressRow['Street2'] = '';
                    $addressRow['Street3'] = '';
                    $addressRow['doorplate'] = $doorplate;
                }
            }            
        }
        //拆分门牌号 end
//         exit;
        $params = array(
                'CustomerCode' => $customerCode,
                'OrderStatus' => $this->_orderStatus,
                'ReferenceNo' => $orderCode,
                'ReferenceNoWms'=>empty($orderRow['refrence_no_warehouse'])?'':$orderRow['refrence_no_warehouse'],
                'ReferenceNoUnique'=>$orderRow['refrence_no_sys'],//系统唯一单号，长度11位
                'ShippingMethod' => $orderRow['shipping_method'],
                'parcelContents' => '',
                'parcelDeclaredValue' => '',
                'Country' => $addressRow['Country'],
                'FirstName' => $addressRow['Name'],
                // 'LastName' =>'',
                'WarehouseCode' => $orderRow['warehouse_code'],
                'doorplate' => $addressRow['doorplate'],//门牌号
                'Address1' => $addressRow['Street1'],
                'Address2' => $addressRow['Street2'].' '.$addressRow['Street3'],
                'City' => $addressRow['CityName'],
                'State/Provice' => $addressRow['StateOrProvince'],
                'Postalcode' => $addressRow['PostalCode'],
                'Email' => empty($orderRow['buyer_mail'])?'':str_replace('Invalid Request', '', $orderRow['buyer_mail']),
                'Company' => '',
                'PhoneNo' => str_replace('Invalid Request', '', $addressRow['Phone']),
                //'remark' => empty($orderRow['order_desc'])?'':$orderRow['order_desc'],
                'seller_id' => $orderRow['user_account'],
                'remark' => empty($orderRow['operator_note'])?'':$orderRow['operator_note'],
                
                'currencyCode' => $orderRow['currency'],                
                'subtotal' => $orderRow['subtotal']==0&&$orderRow['amountpaid']>0?$orderRow['amountpaid']:$orderRow['subtotal'], // 订单销售价
                'shippingCost' => $orderRow['ship_fee'], // 运费
                'paypalFee' => $orderRow['platform_fee'], // 手续费
                'finalvaluefee' => $orderRow['finalvaluefee'], // 交易费
                
                'site_id' => empty($orderRow['site'])?'':$orderRow['site'],
                'paydate'=>$orderRow['date_paid_platform'],
                'platform'=>$orderRow['platform'],
                'orderPlatformType' => $orderRow['order_type'], // 平台类型
                'createType' => $orderRow['create_type'], //创建类型
                'ref_tracking_number' => $orderRow['ref_tracking_number'], //原始订单跟踪号（适用于重发订单）
                'check_shipping_method' => $orderRow['check_shipping_method'], //需仓库确定实物真正的仓库配送方式

        );
        $params['amountpaid'] = $orderRow['amountpaid']?$orderRow['amountpaid']:($params['subtotal']+$params['shippingCost']); // 订单总价
        $params['orderProduct'] = $this->_getOrderProduct($orderRow,'0');
        //         file_put_contents(APPLICATION_PATH.'/../data/log/_order_param.txt', print_r($params,true));
        $total_subtotal = 0;
        $total_finalvaluefee = 0;  
        $total_product_count = 0;//订单总产品数
        foreach($params['orderProduct'] as $k=>$v){//均价
//             $total_product_count+=$v['quantity'];
//             $total_subtotal+=$v['TransactionPrice'];
//             $total_finalvaluefee+=$v['finalvaluefee'];
            //手续费
            $v['finalvaluefee'] = round($v['finalvaluefee']/$v['quantity'],3);
            $v['TransactionPrice'] = round($v['TransactionPrice']/$v['quantity'],3);
            
            $v['paypalFee'] = round($v['paypalFee']/$v['quantity'],3);
            $v['shipFee'] = round($v['shipFee']/$v['quantity'],3);
            
//             round($v['TransactionPrice']/$params['subtotal'],3);
            $params['orderProduct'][$k] = $v;
        }
//         sort($params['orderProduct']);
       
        //暂不发货产品
        $orderSpecialProduct = $this->_getOrderProduct($orderRow,'1');
        foreach($orderSpecialProduct as $k=>$v){//均价
            $v['finalvaluefee'] = round($v['finalvaluefee']/$v['quantity'],3);
            
            $v['TransactionPrice'] = round($v['TransactionPrice']/$v['quantity'],3);
            $v['paypalFee'] = round($v['paypalFee']/$v['quantity'],3);
            $v['shipFee'] = round($v['shipFee']/$v['quantity'],3);
            //amazon订单产品取消后数量为0，做特殊处理
            $v['quantity'] = $v['quantity']>0?$v['quantity']:1;
            $orderSpecialProduct[$k] = $v;
        }
        $params['orderSpecialProduct'] = $orderSpecialProduct;
//         sort($params['orderProductNotShip']); 
//         file_put_contents(APPLICATION_PATH.'/../data/log/_order_param.txt', print_r($params,true));      
//         print_r($params);exit;	
        return $params;    
    }
    
    /**
     * 更新SKU对应的仓库SKU
     * warehouse_sku=pcr_product_sku*pcr_quantity*pcr_percent;pcr_product_sku*pcr_quantity*pcr_percent
     * @param unknown_type $refId
     */
    public static function updateOrderProductWarehouseSku($refId){
        $orderRow = Service_Orders::getByField($refId,'refrence_no_platform');
        if(!$orderRow){
            throw new Exception('订单不存在:'.$refId);
        }
        $con = array(
            'order_id' => $orderRow['order_id']
        );
        $orderProductRows = Service_OrderProduct::getByCondition($con, '*');
        
        foreach($orderProductRows as $key => $product){            
            $conn = array(
                'product_sku' => $product['product_sku']
            );
            $combRows = Service_ProductCombineRelationProcess::getRelation($product['product_sku'], $orderRow['user_account']);
            $warehouseSkuArr = array();
            if($combRows){                
                foreach($combRows as $row){ // 组合产品
                    $warehouseSkuArr[] = $row['pcr_product_sku'] . '*' . $row['pcr_quantity']. '*' . $row['pcr_percent'];
                }
            }else{
                $warehouseSkuArr[] = $product['product_sku'] . '*' . $product['op_quantity']. '*100.000' ;
            }
            $updateRow = array('warehouse_sku'=>implode(';',$warehouseSkuArr));
            Service_OrderProduct::update($updateRow, $product['op_id'],'op_id');
        }
    }
    /**
     * 废弃------------------------------------------
     * 订单数据整合
     * @param unknown_type $orderCode
     * @throws Exception
     * @return array
     */
    public function getOrderIntegrate($orderCode){        
        return $this->getOrderIntegrateNew($orderCode);
    }
    /**
     * @desc 通过Rest同步订单到仓库
     * @param array $orderRow
     * @return array|mixed
     * @throws Exception
     */
    public function sendWarehouseBatch($refNoArr)
    {
        $result = array(
            'state' => 0,
            'message' => Ec::Lang('order_audit_result_00','auto'),//'数据已经提交到仓库处理'
        ); 
        $successArr = $failArr = $quehuoArr = array();
         
        $refNoArr = array_chunk($refNoArr, 50);
        foreach($refNoArr as $refNos){
            $paramsArr = array();
            foreach($refNos as $refNo){
                $orderCode = $refNo;
                try{  
                     /* -----   截单开始   ----- */                
                    $o = Service_Orders::getByField($refNo,'refrence_no_platform');
                    $wmsCode = Service_OrderProcess::updateAbnormalOrderReferenceNoExist($o['refrence_no_platform']);
                    /**
                     * 更新系统单号
                     * 如果refrence_no_sys为空，则更新refrence_no_sys
                     */
                    Service_OrderProcess::updateOrderReferenceNoSys($o['refrence_no_platform']);
                    
                    if($wmsCode){//截单
                    	//'订单曾经提交到仓配系统，请联系管理员处理'
                        throw new Exception(Ec::Lang('order_audit_result_01','auto'));
                        
                        $log = array(
                                'ref_id' => $o['refrence_no_platform'],
                                'create_time' => date('Y-m-d H:i:s'),
                                'log_content' => Ec::Lang('order_audit_result_02','auto'),
                        		//'订单审核，订单曾经提交到仓配系统，系统自动截单并重新提交数据到仓配系统'
                        );
                        Service_OrderLog::add($log);
                        
                        //'订单审核，订单曾经提交到仓配系统，系统自动截单'
                        $rs = $this->stopWarehouseOrder($wmsCode,'EC001',Ec::Lang('order_audit_result_03','auto'));
                        if($rs['state']!=1){//截单操作失败
                            //'订单曾经提交到仓配系统，系统自动截单,截单结果：'
                            throw new Exception(Ec::Lang('order_audit_result_04','auto') . $rs['message']);
                        }
                        
                        if(!empty($rs['waiting'])){//订单已下架，请联系仓库操作人员协助完成截单
                            throw new Exception(Ec::Lang('order_audit_result_05','auto'));                                
                        }
                    } 
                    /* -----   截单结束   ----- */
                    
                    
                    /*
                     * 根据订单号，获得订单的信息
                     */
                    $params = $this->getOrderIntegrate($orderCode);
                    //删除历史记录
                    Service_OrderProductToWms::delete($params['ReferenceNo'],'ref_id');
                    foreach($params['orderProduct'] as $p){
                        $opTwms = array(
                                'product_sku' => $p['product_sku'],
                                'warehouse_sku' => $p['sku'],
                                'quantity' => $p['quantity'],
                                'ref_tnx' => $p['refTnx'],
                                'recv_account' =>empty( $p['recvAccount'])? '': $p['recvAccount'],
                                'ref_item_id' => $p['refItemId'],
                                'ref_buyer_id' => $p['refBuyerId'],
                                'ref_pay_date' => $p['refPayDate'],
                                'ref_id' => $params['ReferenceNo'],
            
                                'subtotal' => $params['subtotal'],// 订单销售价
                                'ship_fee' => $params['shippingCost'], // 运费
                                'platform_fee' => $params['paypalFee'],// 手续费
                                'finalvaluefee' => $params['finalvaluefee'],// 手续费
            
                                'unit_price' => $p['TransactionPrice'],
                                'unit_finalvaluefee' => $p['finalvaluefee'],
                                'unit_platformfee' => $p['paypalFee'],
                                'unit_shipfee' => $p['shipFee'],
            
                                'currency_code' => $params['currencyCode'],
            
                                'update_time' =>date('Y-m-d H:i:s'),
                                'give_up' =>'0',
                        );
                        Service_OrderProductToWms::add($opTwms);
                    }
                    foreach($params['orderSpecialProduct'] as $p){
                        $opTwms = array(
                                'product_sku' => $p['product_sku'],
                                'warehouse_sku' => $p['sku'],
                                'quantity' => $p['quantity'],
                                'ref_tnx' => $p['refTnx'],
                                'recv_account' =>empty( $p['recvAccount'])? '': $p['recvAccount'],
                                'ref_item_id' => $p['refItemId'],
                                'ref_buyer_id' => $p['refBuyerId'],
                                'ref_pay_date' => $p['refPayDate'],
                                'ref_id' => $params['ReferenceNo'],
                                 
                                'subtotal' => $params['subtotal'],// 订单销售价
                                'ship_fee' => $params['shippingCost'], // 运费
                                'platform_fee' => $params['paypalFee'],// 手续费
                                'finalvaluefee' => $params['finalvaluefee'],// 手续费
            
                                'unit_price' => $p['TransactionPrice'],
                                'unit_finalvaluefee' => $p['finalvaluefee'],
                                'unit_platformfee' => $p['paypalFee'],
                                'unit_shipfee' => $p['shipFee'],
            
                                'currency_code' => $params['currencyCode'],
            
                                'update_time' =>date('Y-m-d H:i:s'),
                                'give_up' =>'1',
                        );
                        Service_OrderProductToWms::add($opTwms);
                    }
                    //删除历史记录
                    Service_OrderDataToWms::delete($params['ReferenceNo'],'ref_id');
                    $odtwRow = array(
                            'ref_id' => $params['ReferenceNo'],
                            'data' => serialize($params),
                            'update_time' => date('Y-m-d H:i:s')
                    );
                    Service_OrderDataToWms::add($odtwRow);
            
                    $paramsArr[] = $params;
                    $log = array(
                            'ref_id' =>$refNo,
                            'create_time' => date('Y-m-d H:i:s'),
                            'log_content' => '订单审核，同步到仓库',
                            'data'=>print_r($params,true)
                    );
                    Service_OrderLog::add($log);
                    //更新仓库SKU内容
                    $this->updateOrderProductWarehouseSku($orderCode);
                }catch(Exception $e){
                    $log = array(
                            'ref_id' =>$refNo,
                            'create_time' => date('Y-m-d H:i:s'),
                            'log_content' => $e->getMessage(),
                    );
                    Service_OrderLog::add($log);
                    $failArr[] = array('ref_id'=>$refNo,'refrence_no_platform'=>$refNo,'message'=>array($e->getMessage()));//失败的订单
                }
            }
            if(empty($paramsArr)){
                continue;
            }
            
            try{
                $req = array(
                    'service' => 'batchCreateOrder',
                    'paramsJson' => json_encode(array(
                        'abnormal' => $this->_abnormal,
                        'paramsArr' => $paramsArr
                    ))
                );
//                 Ec::showError(print_r($req,true),'call_soap_service_request');
                try{//调用api失败                    
                    $return = $this->callSoapService($req);
                }catch (Exception $ee){
                    Ec::showError("订单导入到wms异常,订单号如下：\n".implode(',', $refNos),'order_info_to_wms_fail_ref_id_');

                    $info = "req:\n".$this->_soapClient->__getLastRequest();
                    $info.= "\n";
                    $info.= "res:\n".$this->_soapClient->__getLastResponse();
                    $info.= "\n";
                    $info.= "\n";
                
                    Ec::showError($info,'order_to_wms_fail_info_'.date('Y-m-d_'));
                
                    throw new Exception('API Internal error.','50000');
                }
                if($return['state'] == '0'){
                    throw new Exception($return['message']);
                }
                if(! is_array($return['data'])){
                    throw new Exception($return['message']);
                }
                
                foreach($return['data'] as $o){
                    $orderUpdateResult = $this->updateOrderStatus($o);
                    if($orderUpdateResult['ask'] == 1){
                        $successArr[] = $orderUpdateResult; // wsm处理成功订单
                        if($o['orderStatus'] == '3'){
                            $quehuoArr[] = $orderUpdateResult;
                        }
                    }else{
                        // $failArr[] = array('ref_id'=>$orderUpdateResult['refrence_no_platform'],'message'=>$orderUpdateResult['message']);//wms处理失败订单
                        $failArr[] = $orderUpdateResult; // wms处理失败订单
                    }
                }
                $result['state'] = 1;
                
                
            }catch(Exception $e){
                $result['message'] = $e->getMessage();
                $result['err_code'] = $e->getCode();
                Ec::showError($e->getMessage().print_r($failArr,true), 'order_info_to_wms_exception_');
                
                if($e->getCode()=='50000'){//服务器异常，跳出循环
                    $result['state'] = 0;
                    break;
                }
            }
        }
        $result['fail'] = $failArr;
        $result['success'] = $successArr;
        $result['quehuo'] = $quehuoArr;
        

        $result['fail_count'] = count($failArr);
        $result['success_count'] = count($successArr);
        $result['quehuo_count'] = count($quehuoArr);
        return $result;
    }
    private function callSoapService($req)
    {
        $client = $this->getSoapClient();
        $req['appToken'] = $this->_appToken;
        $req['appKey'] = $this->_appKey;
        $result = $client->callService($req);
        $result = Common_Common::objectToArray($result);
        $return = json_decode($result['response']);
        $return = Common_Common::objectToArray($return);
        return $return;
    }
//更新订单状态
    private function updateOrderStatus($result){
        $return = array('ask'=>0,'message'=>'','refrence_no_platform'=>$result['referenceNo'],'order_status'=>'0');
        $date = date('Y-m-d H:i:s');
        if (isset($result['state']) && $result['state'] == '1') {
            $return['ask'] = 1;
            $orderStatus = 3;
            $return['refrence_no_warehouse'] = $result['orderCode'];
            $return['ref_id'] = $result['orderCode'];
            $return['orderStatus'] = $result['orderStatus'];//判断是否库存不足
            $return['spCode'] = $result['spCode'];
            $quehuo = '';
            $sys_tips = '';
            if($result['orderStatus']=='3'){
                $orderStatus = 6;//缺货中
                $quehuo='，订单缺货<[' . print_r($result['OOS'],true) . ']>';
            	//查询订单SKU信息
            	$result_orderProduct = Service_OrderProduct::getByCondition(array('OrderID'=>$result['referenceNo']));
            	$orderProductArr = array();
            	foreach ($result_orderProduct as $p_key => $p_value) {
            		$orderProductArr[$p_value['product_sku']] = $p_value;
            	}
            	
            	//循环对比哪个sku缺货，或全部缺货
            	foreach ($result['OOS'] as $stock_key => $stock_value) {
            		if(empty($sys_tips) && $stock_key == 'A'){ //全部缺货
            			$sys_tips = 'all_stock';
//             			break;
            		}else if(empty($sys_tips) && $stock_key == 'B'){//部分缺货
            			$sys_tips = 'part_stock';
            		}
            		
            		foreach ($stock_value as $stock_detail_key => $stock_detail_value) {
            			$result_orderProductWms = Service_OrderProductToWms::getByCondition(array('ref_id'=>$result['referenceNo'],'warehouse_sku'=>$stock_detail_key));
            			
            			if(!empty($result_orderProductWms)){
            				$result_orderProductWms = $result_orderProductWms[0];            				 
        					Service_OrderProductToWms::update(array('stock_quantity'=>$stock_detail_value), $result_orderProductWms['id']);
            				
            			}
            		}
            	}
            }
            
            $update = array(
            		'sys_tips'=>$sys_tips,
                    'date_release' => $date,
                    'date_last_modify' => $date,
                    'order_status' => $orderStatus,
//                     'sync_status' => 1,
                    'abnormal_reason'=>'',
                    'service_status'=> empty($result['spCode'])?'0':'1',
                    'service_provider'=>$result['spCode'],
                    'refrence_no_warehouse' => isset($result['orderCode']) ? $result['orderCode'] : ''
            );
            
            $log = array(
                    'ref_id' => $result['referenceNo'],
                    'create_time' => $date,
                    'log_content' => '订单审核，同步到仓库,返回仓库单号:' . (isset($result['orderCode']) ? $result['orderCode'] : ''). $quehuo,
                    'data'=>'所有返回参数为：'.print_r($result,true)
            );            
            
        } else {       
            $abnormlReason = '';
            if(is_array($result['message'])) {
                foreach($result['message'] as $m){
                    $abnormlReason.=$m.";";
                }
            }else{
                $abnormlReason.=print_r($result['message'],true);
            }
            $orderStatus = 7;
            $update = array(
                    'date_last_modify' => $date,
                    'order_status' => $orderStatus,//问题件
                    'sync_status' => 0,
                    'abnormal_type'=>4,//异常原因类型
                    'abnormal_reason'=>$abnormlReason,//异常原因
            );
            $log = array(
                    'ref_id' => $result['referenceNo'],
                    'create_time' => $date,
                    'log_content' => "订单同步到仓库失败，失败原因:".print_r($result['message'],true),
            );
            $return['message'] = isset($return['message']) ? $result['message'] : '';  

        }
        $return['order_status'] = $orderStatus;
        
        Service_Orders::update($update, $result['referenceNo'], 'refrence_no_platform');
        Service_OrderLog::add($log);
        return $return;
    }
    /**
     * @截单操作 方法废弃
     * @param string $orderCode
     * @return array
     * @throws Exception
     */
    public function stopOrder($orderCode = '')
    {
        $date = date('Y-m-d H:i:s');
        $result = array('state' => 0, 'message' => '', 'orderCode' => $orderCode);
        try {
            $orderRow = Service_Orders::getByField($orderCode, 'refrence_no_platform');
            if (empty($orderRow)) {
                throw new Exception('订单号:' . $orderCode . '不存在.');
            }
            $warehouseOrderCode = $orderRow['refrence_no_warehouse'];
            if (!empty($warehouseOrderCode)) {
                $customerCode = $this->_customerCode;
                if ($orderRow['customer_id'] != '') {
                    //调用WMS客户表
                    $customerObj = new Table_Customer();
                    $customerRow = $customerObj->getByField($orderRow['customer_id'], 'customer_id', array('customer_id', 'customer_code'));
                    if (empty($customerRow)) {
                        throw new Exception('客户不存在.');
                    }
                    $customerCode = $customerRow['customer_code'];
                }
                $return = $this->stopWarehouseOrder($warehouseOrderCode,$customerCode);
                if (!isset($return['state']) || $return['state'] != '1') {
                    $log = array(
                        'ref_id' => $orderCode,
                        'create_time' => $date,
                        'log_content' => Ec::Lang('cancel_order_failure_reasons','auto') . (isset($return['message']) ? $return['message'] : Ec::Lang('interface_calls_fails','auto')),//'截单失败:' XXX OR 调用API失败
                    );
                    Service_OrderLog::add($log);
                    throw new Exception(Ec::Lang('oo_warehouse_intercept_failure','auto') . '：' . $return['message']); //仓库截单失败
                }
            }
            $update = array(
                'order_status' => '0',
                'date_last_modify' => $date,
            );
            if (Service_Orders::update($update, $orderCode, 'refrence_no_platform')) {
                $log = array(
                    'ref_id' => $orderCode,
                    'create_time' => $date,
                    'log_content' => Ec::Lang('oo_intercept_success'),//'截单成功.'
                );
                Service_OrderLog::add($log);
            }
            //'截单成功'
            $result = array('state' => 1, 'message' => Ec::Lang('oo_intercept_success'), 'orderCode' => $orderCode);
            
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @desc 仓库截单
     * @param $warehouseOrderCode,$customerCode
     * @return array state message=string
     */
    public function stopWarehouseOrder($warehouseOrderCode = '', $customerCode = 'EC001',$reason='')
    {
    	$reason = (empty($reason))?Ec::Lang('oo_intercept','auto'):$reason;
        $result = array('state' => 0, 'message' => '','waiting'=>0);
        try {
            $order = Service_Orders::getByField($warehouseOrderCode,'refrence_no_warehouse');
            if(empty($order)){
                throw new Exception('仓库参考号不存在'.$warehouseOrderCode);
            }
            $this->setClient();
            $params = array(
                'CustomerCode' => $customerCode,
                'OrderCode' => $warehouseOrderCode,
                'Reason'=>$reason,
            );
            $result['request'] = $params;
            $params = serialize($params);
            Ec::showError('Process_API(param):' . print_r($params,true),'refund_order_canenl_');
            $params = Common_Common::authcode($params, 'CODE');
            try{
                $return = $this->_client->stopOrder($params)->post();
            }catch(Exception $eee){
                throw new Exception($eee->getMessage(),'50000');
            }
            Ec::showError('Process_API(response):' . print_r($return,true),'refund_order_canenl_');
            if ($return->status == 'success') {
                $return = $return->getIterator()->stopOrder;               
                $return = Common_Common::objectToArray($return);
                $result['response'] = $return;
                if (isset($return['state']) && $return['state'] == '1') {
                    $result['state'] = 1;
                    $msgExt = '';
                    if(isset($return['waiting'])&&$return['waiting']==1){
                        $msgExt = Ec::Lang('swo_intercept_waring','auto');//',订单已下架，请联系仓库操作人员协助完成截单';
                        $result['message'] = Ec::Lang('swo_intercept_success','auto') . $msgExt; //'订单拦截成功' + waring 
                    }else{
                        $result['message'] = Ec::Lang('oo_intercept_success','auto'); // '截单成功';                        
                    }
                    $result['waiting'] = isset($return['waiting'])?$return['waiting']:0;
                    $log = array(
                        'ref_id' => $order['refrence_no_platform'],
                        'create_time' => date('Y-m-d H:i:s'),
                        'log_content' => Ec::Lang('swo_intercept_success','auto') . $msgExt,//'订单拦截成功.'
                    ); 
                                     
                } else {
                    $log = array(
                        'ref_id' => $order['refrence_no_platform'],
                        'create_time' => date('Y-m-d H:i:s'),
                        'log_content' => Ec::Lang('cancel_order_failure_reasons','auto') . print_r($return['message'], true),//'截单失败:'
                    );
                    $result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
                }
                Service_OrderLog::add($log);
            } else {
                throw new Exception('API Internal error.');
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $result['err_code'] = $e->getCode();            
        }

        return $result;
    }

    /**
     * @desc 仓库标记发货
     * @param $warehouseOrderCode,$customerCode
     * @return array state message=string
     */
    public function batchShipOrder($orderArr)
    {
        $result = array('state' => 0, 'message' => '');
        try {
            $this->setClient();           
            $params = serialize($orderArr);
            $params = Common_Common::authcode($params, 'CODE');
            try{
                $return = $this->_client->batchShipOrder($params)->post();
            }catch(Exception $eee){
                throw new Exception($eee->getMessage(),'50000');
            }
    
            if ($return->status == 'success') {
                $return = $return->getIterator()->batchShipOrder;
                $return = Common_Common::objectToArray($return);
                //print_r($return);exit;
                if (isset($return['state']) && $return['state'] == '1') {
                    $result['state'] = 1;
                    $result['data'] = $return['data'];
                } else {
                    $result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
                }
            } else {
                throw new Exception('API Internal error.');
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $result['err_code'] = $e->getCode();
        }
        return $result;
    }
    
    /**
     * 批量仓库截单
     * refrence_no_platform
     */
    public function batchStopOrder($refrenceNoPlatformArr){
    	$result = array('state' => 1, 'message' => '','data'=>array());
    	foreach ($refrenceNoPlatformArr as $key => $refNo) {
    		$orderDataSub = array(
    				'state'=>0,
    				'referenceNo'=>$refNo,
    				'message'=>'Operation Successfully Completed'
    		);
    		
    		try {
    			/* -----   截单开始   ----- */
    			$o = Service_Orders::getByField($refNo,'refrence_no_platform');
    			$wmsCode = Service_OrderProcess::updateAbnormalOrderReferenceNoExist($o['refrence_no_platform']);
    			 
    			if($wmsCode){//截单
    				$log = array(
    						'ref_id' => $o['refrence_no_platform'],
    						'create_time' => date('Y-m-d H:i:s'),
    						'log_content' => Ec::Lang('oo_out_of_stock_order_mark_shippments_intercept'),
    						//'缺货订单->标记发货，拦截仓库订单，不在仓库操作.'
    				);
    				Service_OrderLog::add($log);
//     				/'缺货订单标记发货，拦截仓库订单，不在仓库操作.'
    				$rs = $this->stopWarehouseOrder($wmsCode,'EC001',Ec::Lang('oo_out_of_stock_order_mark_shippments_intercept'));
    				if($rs['state']!=1){//截单操作失败
    					//'订单曾经提交到仓配系统，系统自动截单,截单结果：'
    					throw new Exception(Ec::Lang('order_audit_result_04','auto') . $rs['message']);
    				}
    				if(!empty($rs['waiting'])){
    					//订单已下架，请联系仓库操作人员协助完成截单
    					throw new Exception(Ec::Lang('swo_intercept_waring'));
    				}
    			}
    			$orderDataSub['state'] = 1;
    			/* -----   截单结束   ----- */
    		} catch (Exception $e) {
    			$orderDataSub['message'] = $e->getMessage();
    		}
    		
    		$result['data'][] = $orderDataSub;
    	}
    	return $result;
    }

    /**
     * @desc批量创建退款订单到仓库
     * @param $warehouseOrderCode,$customerCode
     * @return array state message=string    //stopWarehouseOrder
     */
    public function batchCreateRefundOrder($refundOrders = array())
    {
    	$result = array('state' => 0, 'message' => '');
    	try {
    
    		$params = $refundOrders;
    		$params = serialize($params);
    		$params = Common_Common::authcode($params, 'CODE');
    
    		try{
    			$this->setClient();
    			$return = $this->_client->batchCreateRefundOrder($params)->post();
    		}catch(Exception $eee){
    			throw new Exception($eee->getMessage(),'50000');
    		}
    
    		if ($return->status == 'success') {
    			$return = $return->getIterator()->batchCreateRefundOrder;
    			$return = Common_Common::objectToArray($return);
    			if (isset($return['state']) && $return['state'] == '1') {
    				$result['state'] = 1;
    				$result['data'] = $return['data'];
    				$result['message'] = '批量创建退款订单请求成功';
    			} else {
    				$result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
    			}
    			 
    		} else {
    			throw new Exception('API Internal error.');
    		}
    	} catch (Exception $e) {
    		$result['message'] = $e->getMessage();
    		$result['err_code'] = $e->getCode();
    	}
    	return $result;
    }
    

    /**
     * @验证是否可以导出
     * @param string $orderCode
     * @return array
     * @throws Exception
     */
    public function checkExportOrder($orderCode = '',$force=false)
    {
        
        $date = date('Y-m-d H:i:s');
        $result = array(
            'state' => 0,
            'message' => '',
            'orderCode' => $orderCode
        );
        if(!$force){//不验证
            $result['state'] = 1;
            return $result;
        }
        try{
            $orderRow = Service_Orders::getByField($orderCode, 'refrence_no_platform');
            if(empty($orderRow)){
                throw new Exception('订单号:' . $orderCode . '不存在.');
            }
            $warehouseOrderCode = $orderRow['refrence_no_warehouse'];
            if(! empty($warehouseOrderCode)){
                $this->setClient();
                $return = $this->_client->checkExportOrder($orderRow['user_account'],$warehouseOrderCode)->post();

                if($return->status == 'success'){
                    $return = $return->getIterator()->checkExportOrder;
                    $return = Common_Common::objectToArray($return);
//                     print_r($return);exit;
                    if(isset($return['state']) && $return['state'] == '1'){
                        $result['state'] = 1;
                        $result['message'] = '订单可导出Excel';
                    }else{
                        throw new Exception('订单不可导出Excel:' . $return['message']);
                    }
                }else{
                    throw new Exception('API Internal error.'.print_r($return,true));
                }
            }
        }catch(Exception $e){
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param array $dataArr =array('processType'=>int,'order'=>array(1),'items'=>array(2))
     * @return array
     */
    public function returnOrderProcess($dataArr = array())
    {
        $result = array('state' => 0, 'message' => '', 'roCode' => '');
        try {
            $date = date('Y-m-d H:i:s');
            $upRow = array(
                'ro_confirm_time' => $date,
                'ro_update_time' => $date,
            );
            if (!isset($dataArr['order']['ro_code']) || empty($dataArr['order']['ro_code'])) {
                throw new Exception('请求参数异常，退件单号不能为空.');
            }
            $result['roCode'] = $dataArr['order']['ro_code'];
            $roRow = Service_ReturnOrders::getByField($dataArr['order']['ro_code'], 'ro_code');
            if (empty($roRow)) {
                throw new Exception('无法找到匹配的退件订单');
            }
            switch ((int)$dataArr['processType']) {
                case 1: //退件入库
                    if ($roRow['ro_status'] != '1') {
                        throw new Exception('请确认状态,当前状态不允许操作');
                    }
                    //提交Rest
                    $params = array(
                        'processType' => 1, 'order' => $dataArr['order'], 'items' => $dataArr['items']
                    );
                    $upRow['ro_note'] = (isset($dataArr['order']['ro_note']) ? $dataArr['order']['ro_note'] : '');
                    $upRow['ro_status'] = 2;
                    $upRow['ro_process_type'] = 1;
                    break;
                case 2: //退件重发
                    if ($roRow['ro_status'] != '1') {
                        throw new Exception('请确认状态,当前状态不允许操作');
                    }
                    $params = array(
                        'processType' => 2, 'order' => $roRow, 'items' => array()
                    );
                    $upRow['ro_process_type'] = 2;
                    $upRow['ro_status'] = 2;
                    break;
                case 3: //作废退件
                    if (!in_array($roRow['ro_status'], array(1, 2))) {
                        throw new Exception('请确认状态,当前状态不允许操作');
                    }
                    $params = array(
                        'processType' => 3, 'order' => $roRow, 'items' => array()
                    );
                    $upRow['ro_status'] = 0; //删除
                    break;
                case 4: //操作收货
                    unset($upRow['ro_confirm_time']);
                    if ($roRow['ro_status'] != '2') {
                        throw new Exception('请确认状态,当前状态不允许操作.' . $roRow['ro_status']);
                    }
                    if ($roRow['ro_process_type'] != '1') {
                        throw new Exception('请确认处理方式,不允许操作.' . $roRow['ro_process_type']);
                    }
                    $params = array(
                        'processType' => 4, 'order' => $roRow, 'items' => array()
                    );
                    $upRow['ro_status'] = 4; //完成
                    break;
                case 5://客服修改
                	if ($roRow['ro_status'] != '2') {
                		throw new Exception('请确认状态,当前状态不允许操作.' . $roRow['ro_status']);
                	}
                	$roRow['ro_note'] = $dataArr['order']['ro_note'];
                	$roRow['tracking_no'] = $dataArr['order']['tracking_no'];
                	$roRow['expected_date'] = $dataArr['order']['expected_date'];
                	
                	$params = array(
                			'processType' => 5, 'order' => $roRow, 'items' =>  $dataArr['order']['items']
                	);
                	
                	$upRow['ro_note'] = $dataArr['order']['ro_note'];
                	$upRow['tracking_no'] = $dataArr['order']['tracking_no'];
                	$upRow['expected_date'] = $dataArr['order']['expected_date'];
                	break;
                case 13: //操作强制完成
                    unset($upRow['ro_confirm_time']);
                    if ($roRow['ro_status'] != '3') {
                        throw new Exception('请确认状态,当前状态不允许操作.' . $roRow['ro_status']);
                    }
                    $params = array(
                        'processType' => 13, 'order' => $roRow, 'items' => array()
                    );
                    $upRow['ro_status'] = 4; //完成
                    break;
                default:
                    throw new Exception('处理方式异常');
                    break;
            }
//             print_r($params);
//             exit;
            $params = serialize($params);
            $params = Common_Common::authcode($params, 'CODE');
            try {
                $this->setClient();
                $return = $this->_client->updateReturnOrdersProcessInstruction($params)->post();
            } catch (Exception $eee) {
                throw new Exception($eee->getMessage(), '50000');
            }

            if ($return->status == 'success') {
                $return = $return->getIterator()->updateReturnOrdersProcessInstruction;
                $return = Common_Common::objectToArray($return);
                if (isset($return['state']) && $return['state'] == '1') {

                    if (!Service_ReturnOrders::update($upRow, $roRow['ro_id'], 'ro_id')) {
                        throw new Exception('更新数据失败');
                    }
                    //退件入库指定处理方式
                    if (isset($dataArr['items']) && !empty($dataArr['items']) && $dataArr['processType'] == '1') {
                        foreach ($dataArr['items'] as $val) {
                            if (!isset($val['product_barcode'])) {
                                throw new Exception('SKU不能为空');
                            }
                            $ropRows = Service_ReturnOrderProduct::getByCondition(array('ro_id' => $roRow['ro_id'], 'product_barcode' => $val['product_barcode']), '*', 1);
                            if (empty($ropRows)) {
                                throw new Exception('SKU:' . $val['product_barcode'] . '不存在');
                            }
                            $up = array('exception_process_instruction' => $val['exception_process_instruction'],
                                'rop_note' => $val['rop_note'],
                                'rop_update_time' => $date
                            );
                            Service_ReturnOrderProduct::update($up, $ropRows[0]['rop_id'], 'rop_id');
                        }
                    }
                    //客服修改
                    if(isset($dataArr['order']['items']) && !empty($dataArr['order']['items']) && $dataArr['processType'] == '5'){
                    	foreach ($dataArr['order']['items'] as $val) {
                    		if (!isset($val['sku'])) {
                    			throw new Exception('SKU不能为空');
                    		}
                    		$ropRows = Service_ReturnOrderProduct::getByCondition(array('ro_id' => $roRow['ro_id'], 'product_barcode' => $val['sku']), '*', 1);
                    		if (empty($ropRows)) {
                    			throw new Exception('SKU:' . $val['sku'] . '不存在');
                    		}
                    		$up = array(
                    				'rop_quantity'=>$val['qty'],
                    				'exception_process_instruction' => $val['processInstruction'],
                    				'rop_note' => $val['description'],
                    				'rop_update_time' => $date
                    		);
                    		Service_ReturnOrderProduct::update($up, $ropRows[0]['rop_id'], 'rop_id');
                    	}
                    }
                    $result['state'] = 1;
                    $result['message'] = '操作成功';

                } else {
                    $result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
                }

            } else {
                throw new Exception('API Internal error.');
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $result['err_code'] = $e->getCode();
        }
        return $result;
    }   
    
    /**
     * @desc创建退件订单到仓库
    * @param unknown_type $returnOrders
    * @throws Exception
    * @return multitype:number string Ambigous <string, unknown> NULL Ambigous <>
    */
    public function createReturnOrder($returnOrders = array())
    {
    	$result = array('state' => 0, 'message' => '');
    	try {
        	$params = $returnOrders;
        	$params = serialize($params);
        	$params = Common_Common::authcode($params, 'CODE');
    
        	try{
        	$this->setClient();
        	$return = $this->_client->createReturnOrder($params)->post();
        	}catch(Exception $eee){
        	throw new Exception($eee->getMessage(),'50000');
        	}
    
        	if ($return->status == 'success') {
        	$return = $return->getIterator()->createReturnOrder;
        		$return = Common_Common::objectToArray($return);
        		if (isset($return['state']) && $return['state'] == '1') {
        		$result = $return;
        	} else {
        			$result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
        		}
    
        		} else {
        			throw new Exception('API Internal error.');
        	}
	    } catch (Exception $e) {
	    	$result['message'] = $e->getMessage();
	    	$result['err_code'] = $e->getCode();
	    }
	    return $result;
    }
    
    /**
     * @desc 费用试算
     * @param unknown_type $params
     * @throws Exception
     * @return multitype:number string Ambigous <string, unknown> NULL Ambigous <>
     */
    public function trial($params = array())
    {
    	$result = array('state' => 0, 'message' => '');
    	try {
    		$params = serialize($params);
    		$params = Common_Common::authcode($params, 'CODE');
    
    		try{
    			$this->setClient();
    			$return = $this->_client->trial($params)->post();
    		}catch(Exception $eee){
    			throw new Exception($eee->getMessage(),'50000');
    		}
    
    		if ($return->status == 'success') {
    			$return = $return->getIterator()->trial;
                $result = Common_Common::objectToArray($return);
    
    		} else {
    			throw new Exception('API Internal error.');
    		}
    	} catch (Exception $e) {
    		$result['message'] = $e->getMessage();
    		$result['err_code'] = $e->getCode();
    	}
    	return $result;
    }
    
    /**
     * @desc 获得产品类目信息
     * @throws Exception
     * @return multitype:number string Ambigous <string, unknown> NULL Ambigous <>
     */
    public function getProductCategory($params = array())
    {
    	$result = array('state' => 0, 'message' => '');
    	try {
    		$params = serialize($params);
    		$params = Common_Common::authcode($params, 'CODE');
    		
    		try{
    			$this->setClient();
    			$return = $this->_client->getProductCategory($params)->post();
    		}catch(Exception $eee){
    			throw new Exception($eee->getMessage(),'50000');
    		}
    
    		if ($return->status == 'success') {
    			$return = $return->getIterator()->getProductCategory;
    			$return = Common_Common::objectToArray($return);
//     			    			print_r($return);
//     			    			exit;
    			if (isset($return['state']) && $return['state'] == '1') {
    				$tmp = array();
    				foreach ($return['data'] as $key => $value) {
    					$tmp[$value['pc_id']] = $value;
    				}
    				$return['data'] = $tmp;
    				$result = $return;
    			} else {
    				$result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
    			}
    
    		} else {
    			throw new Exception('API Internal error.');
    		}
    	} catch (Exception $e) {
    		$result['message'] = $e->getMessage();
    		$result['err_code'] = $e->getCode();
    	}
    	return $result;
    }
    
    /**
     * @desc 查询产品信息
     * @param unknown_type $params
     * @throws Exception
     * @return multitype:number string Ambigous <string, unknown> NULL Ambigous <>
     */
    public function productList($params = array())
    {
    	$result = array('state' => 0, 'message' => '');
    	try {
    		$params = serialize($params);
    		$params = Common_Common::authcode($params, 'CODE');
    		
    		try{
    			$this->setClient();
    			$return = $this->_client->productList($params)->post();
    		}catch(Exception $eee){
    			throw new Exception($eee->getMessage(),'50000');
    		}
    
    		if ($return->status == 'success') {
    			$return = $return->getIterator()->productList;
    			$return = Common_Common::objectToArray($return);
    			//     			print_r($return);
    			//     			exit;
    			if (isset($return['state']) && $return['state'] == '1') {
    				$result = $return;
    			} else {
    				$result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
    			}
    
    		} else {
    			throw new Exception('API Internal error.');
    		}
    	} catch (Exception $e) {
    		$result['message'] = $e->getMessage();
    		$result['err_code'] = $e->getCode();
    	}
    	return $result;
    }
    
    /**
     * @desc 查询采购策略
     * @param unknown_type $params
     * @throws Exception
     * @return multitype:number string Ambigous <string, unknown> NULL Ambigous <>
     */
    public function getInventoryIntegrate($params = array())
    {
    	$result = array('state' => 0, 'message' => '');
    	try {
    		$params = serialize($params);
    		$params = Common_Common::authcode($params, 'CODE');
    
    		try{
    			$this->setClient();
    			$return = $this->_client->getInventoryIntegrate($params)->post();
    		}catch(Exception $eee){
    			throw new Exception($eee->getMessage(),'50000');
    		}
    
    		if ($return->status == 'success') {
    			$return = $return->getIterator()->getInventoryIntegrate;
    			$return = Common_Common::objectToArray($return);
    			//     			print_r($return);
    			//     			exit;
    			if (isset($return['state']) && $return['state'] == '1') {
    				$result = $return;
    			} else {
    				$result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
    			}
    
    		} else {
    			throw new Exception('API Internal error.');
    		}
    	} catch (Exception $e) {
    		$result['message'] = $e->getMessage();
    		$result['err_code'] = $e->getCode();
    	}
    	return $result;
    }
    
    
    public function getServiceChannel(){
    	$req = array(
    			'service' => 'getServiceChannel',
    			'paramsJson' => json_encode(array(
    					'warehouse_id' => '',
    					'sm_code' => ''
    			))
    	);
		
    	$response = array();
    	try{
    		$response = $this->callSoapService($req);
    	}catch (Exception $ee){
    		Ec::showError("获取发货渠道信息异常：\n".implode(',', $refNos),'getServiceChannel_fail_');
    	
    		$info = "req:\n".$this->_soapClient->__getLastRequest();
    		$info.= "\n";
    		$info.= "res:\n".$this->_soapClient->__getLastResponse();
    		$info.= "\n";
    		$info.= "\n";
    		Ec::showError($info,'getServiceChannel_soap_fail_');
    		
    		return array();
    	}
    	
    	$result = array();
    	if($response['state'] == '1'){
    		$result = $response['data'];
    	}
    	
    	return $result;
    }
    
}