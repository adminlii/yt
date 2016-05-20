<?php
error_reporting(0);//开启错误报告
class Common_Rest
{
    function createOrder($input)
    {
        $input = Common_Common::authcode($input);
        $input = unserialize($input);
        return $input;
    }

    /*
     * @desc 更新发货信息
     * @param $orderArray orderCode、warehouseShippingDate、trackingNumber
     * @return array state
     * @throws Exception
     */
    public function updateOrderDeliveryStatus($orderArray)
    {
        $result = array(
            "state" => 0,
            "orderCode" => '',
            "message" => "Operation Fail"
        );
        //解码
        $orderArray = Common_Common::authcode($orderArray, 'DECODE');
        if (empty($orderArray)) {
            $result['message'] = 'Could not parse parameter.';
            return $result;
        }

        $orderArray = unserialize($orderArray);
        Ec::showError(print_r($orderArray,true),'from_wms');

        if (!isset($orderArray['orderCode']) || $orderArray['orderCode'] == '') {
            $result['message'] = '订单号不能为空.';
            return $result;
        }
        if (!isset($orderArray['warehouseShippingDate']) || $orderArray['warehouseShippingDate'] == '') {
            $result['message'] = '仓库出货时间不能为空.';
            return $result;
        }

        if (!isset($orderArray['trackingNumber']) || $orderArray['trackingNumber'] == '') {
//             $result['message'] = '订单追踪号码不能为空.';
//             return $result;
        }
        $result['orderCode']=$orderArray['orderCode'];
        $date = date('Y-m-d H:i:s');
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try {
            $orderRow = Service_Orders::getByField($orderArray['orderCode'], 'refrence_no_platform');
            if (empty($orderRow)) {
                throw new Exception('OrderCode:' . $orderArray['orderCode'] . ' 不存在.');
            }
            if (!in_array($orderRow['order_status'],array(3,4,6,7))) {
                throw new Exception('Current state does not allow operation.');
            }
            $updateArr = array(
                'order_status' => 4,
                'sync_status'=>'3',
                'date_last_modify' => $date,
                'date_warehouse_shipping' => $orderArray['warehouseShippingDate'],
                'shipping_method_no' => $orderArray['trackingNumber'],
                'shipping_method' => $orderArray['shippingMethod'],
                'order_weight' => $orderArray['weight']
            );
            
            if(!Service_Orders::update($updateArr, $orderArray['orderCode'], 'refrence_no_platform')){
                throw new Exception('Update Order Ship Fail.');
            }
            $orderLog=array(
                'ref_id'=>$orderArray['orderCode'],
                'log_content'=>'API Update Order Status '.$orderRow['order_status'].' to 4,shipping_method_no:'.$orderRow['shipping_method_no'].' to: '.$orderArray['trackingNumber'].print_r($orderArray,true),
                'create_time'=>$date
            );
            Service_OrderLog::add($orderLog);
            $db->commit();
            $result = array(
                "state" => 1,
                "message" => "Operation Success"
            );
        } catch (Exception $e) {
            $db->rollback();
            $result['message'] =$e->getMessage();
        }
        return $result;
    }

    //获取eub账号
    public function getEubAccount($account){
        
        if(empty($account)){
            $result = Service_EubAccount::getAll();
        }else{
            $con = array('ebay_account'=>$account);
            $result = Service_EubAccount::getByCondition($con);
        }
        return $result;
    }
    

    //获取eub账号
    public function updateInsufficientOrderStatus($ebOrderId){
        $result = array(
            "state" => 1,
            'refrence_no_platform' => $ebOrderId,
            "message" => "Operation Fail"
        );
//         Ec::showError($ebOrderId,'--------------------------');
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $order = Service_Orders::getByField(trim($ebOrderId), 'refrence_no_platform');
            if(empty($order)){
                throw new Exception('订单不存在-->eb_order_id:' . $ebOrderId);
            }elseif($order['order_status'] == 6){
                $updateRow = array(
                    'order_status' => 3
                );
                if(! Service_Orders::update($updateRow, $ebOrderId, 'refrence_no_platform')){
                    $result['state'] = 0;
                    throw new Exception('订单更新状态失败-->eb_order_id:' . $ebOrderId);
                }
                
                $logRow = array(
                    'ref_id' => $ebOrderId,
                    'log_content' => 'API创建缺货转为待发货',
                    'op_id' => ''
                );
                $logRow['create_time'] = date('Y-m-d H:i:s');
                Service_OrderLog::add($logRow);
                
                $result['message'] = '订单更新状态成功-->eb_order_id:' . $ebOrderId;
            }else{
                throw new Exception('订单不不是缺货订单-->eb_order_id:' . $ebOrderId);
            }
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /*
     * 作废啦-----------------------------------
     * 获取订单费用
     * @param unknown_type $warehouseOrderCode
     * @throws Exception
     * @return multitype:number string NULL Ambigous <multitype:, multitype:string unknown_type unknown mixed Ambigous <number, unknown> number Ambigous <string, mixed> Ambigous <number, mixed> >
     */
    public function getOrderIntegrate($warehouseOrderCode){
        $return = array(
            'ask' => 0,
            'message' => 'No Data'
        );
        try{
            $order = Service_Orders::getByField($warehouseOrderCode, 'refrence_no_warehouse');
            if(!$order){
                throw new Exception('仓库订单号 '. $warehouseOrderCode .' 不存在于订单系统');
            }

            $obj = new Service_OrderForWarehouseProcessNew();
            $data = $obj->getOrderIntegrate($order['refrence_no_platform']);
            $result = array(
                'ReferenceNoWms'=>$data['ReferenceNoWms'],
                'ReferenceNo' => $data['ReferenceNo'],
                'currencyCode' => $data['currencyCode'],
                'subtotal' => $data['subtotal'],
                'shippingCost' => $data['shippingCost'],
                'paypalFee' => $data['paypalFee'],
                'finalvaluefee' => $data['finalvaluefee'],
                'orderProduct' => $data['orderProduct'],                    
            );
            $return['result'] = $result;
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    

    /*
     * 均摊费用不正确订单费用
     * @param unknown_type $warehouseOrderCode
     * @throws Exception
     * @return multitype:number string NULL Ambigous <multitype:, multitype:string unknown_type unknown mixed Ambigous <number, unknown> number Ambigous <string, mixed> Ambigous <number, mixed> >
     */
    public function getOrderIntegrateForWms($warehouseOrderCode){
        $return = array(
                'ask' => 0,
                'message' => 'No Data'
        );
        try{
            $order = Service_Orders::getByField($warehouseOrderCode, 'refrence_no_warehouse');
            if(!$order){
                throw new Exception('仓库订单号 '. $warehouseOrderCode .' 不存在于订单系统');
            }
//             print_r($order);exit;
            if($order['platform']=='ebay'&&$order['is_merge']=='0'){
                $org = Service_EbayOrderOriginal::getByField($order['refrence_no_platform'],'OrderID');
                if($org){
                    $order['platform_fee'] = $org['feeorcreditamount'];
                    $order['ship_fee'] = $org['shippingservicecost'];
                }
            }
            $subtotal = $order['subtotal'];
            $shipfee = $order['ship_fee'];
            $platformfee = $order['platform_fee'];
            $finalvaluefee = $order['finalvaluefee'];
            
            $db = Common_Common::getAdapter();
            
            $wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
            $sql = "select sum(op_quantity) sum from {$wms_db}.order_product where order_code = '{$warehouseOrderCode}'";

            $order_product_count =$db->fetchOne($sql);
//             echo $order_product_count;exit;
            
            $sql = "select * from {$wms_db}.order_product where order_code = '{$warehouseOrderCode}'";
            $data = $db->fetchAll($sql);
//             print_r($data);exit;
            $total_subtotal = 0;
            $total_finalvaluefee = 0;
            $total_platformfee = 0;
            $total_shipfee = 0;
            foreach($data as $k=>$v){//均价                
                $total_subtotal+=$v['op_sales_price']*$v['op_quantity'];
                $total_finalvaluefee+=$v['op_final_value_fee']*$v['op_quantity'];
                $total_platformfee+=$v['op_paypal_fee']*$v['op_quantity'];
                $total_shipfee +=$v['op_ship_fee']*$v['op_quantity'];
                $total_product_count+=$v['op_quantity'];
            }
            
            $productArr = array();
            foreach($data as $v){
                if($total_subtotal>0){
                    $v['op_sales_price'] = (($v['op_sales_price']*$v['op_quantity']/$total_subtotal)*$subtotal)/$v['op_quantity'];
                }else{
                    $v['op_sales_price'] = $subtotal/$total_product_count;
                }
        
                if($shipfee>0&&$total_shipfee>0&&$total_subtotal>0){
                    $v['op_ship_fee'] = (($v['op_ship_fee']*$v['op_quantity']/$total_subtotal)*$shipfee)/$v['op_quantity'];
                }else{
                    $v['op_ship_fee'] = $shipfee/$total_product_count;
                }
                if($total_finalvaluefee>0){
                    $v['op_final_value_fee'] = (($v['op_final_value_fee']*$v['op_quantity']/$total_finalvaluefee)*$finalvaluefee)/$v['op_quantity'];
                }else{
                    $v['op_final_value_fee'] = $finalvaluefee/$total_product_count;
                }
        
                if($total_platformfee>0){
                    $v['op_paypal_fee'] = (($v['op_paypal_fee']*$v['op_quantity']/$total_platformfee)*$platformfee)/$v['op_quantity'];
                }else{
                    $v['op_paypal_fee'] = $platformfee/$total_product_count;
                }        
                $productArr[$v['product_barcode']] = $v;
            }
            
            $result = array(
                    'ReferenceNoWms'=>$order['refrence_no_warehouse'],
                    'ReferenceNo' => $order['refrence_no_platform'],
                    'currencyCode' => $order['currency'],
                    'subtotal' => $subtotal,
                    'shippingCost' => $shipfee,
                    'paypalFee' => $platformfee,
                    'finalvaluefee' => $finalvaluefee,
                    'orderProduct' => $productArr,
            );            
            $return['result'] = $result;
            $return['ask'] = 1;
            $return['message'] = 'Success';

            $sql = "select * from {$wms_db}.order_special_product where order_code = '{$warehouseOrderCode}'";
            $exists = $db->fetchRow($sql);
            if($exists){
                Ec::showError($warehouseOrderCode,'_需要手工调整运费的订单_');
                $return['ask'] = 2;
                $return['message'] = '订单需要手工调整运费';
            }
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    /*
     * WMS标记问题件
     * @param unknown_type $refId 仓库单号
     * @param unknown_type $message 异常原因
     */
    public function markOrderAbnormal($refId,$message){
        $return = array('state'=>0,'message'=>'');
        $order = Service_Orders::getByField($refId,'refrence_no_warehouse');
        try{
            if(! $order){
                throw new Exception('仓库订单号不存在，不可标记问题件-->' . $refId);
            }
            // 判断订单状态
            $allowStatusArr = array(
                '3',
                '4'
            );
            if(! in_array($order['order_status'], $allowStatusArr)){
                // 日志记录
                $logRow = array(
                        'ref_id' => $order['refrence_no_platform'],
                        'log_content' => '订单状态不正确，强制标记问题件,订单状态从'.$order['order_status'].' To 7'
                );
                Service_OrderLog::add($logRow);                
//                 throw new Exception('订单状态不正确，不可标记问题件');
            }
            // 标记问题件
            $updateRow = array(
                'order_status' => '7',
                'abnormal_reason' => $message,
                'abnormal_type' => '5',//wms标记问题件
            );
            Service_Orders::update($updateRow, $order['order_id'], 'order_id');
            // 日志记录
            $logRow = array(
                'ref_id' => $order['refrence_no_platform'],
                'log_content' => "订单标记问题件，原因：".$message.' 。订单状态从'.$order['order_status'].' To 7'
            );
            Service_OrderLog::add($logRow);
            $return['state'] = 1;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }
    

    /*
     * 标记服务商
     * @param unknown_type $refId 订单单号
     * @param unknown_type $serviceStatus 状态
     * @param unknown_type $serviceProvider 服务商
     * @param unknown_type $trackNo 跟踪号
     */
    public function markServiceProviders($params){

        $return = array(
                'state' => 0,
                'message' => 'Fail'
        );
        // 解码
        $params = Common_Common::authcode($params, 'DECODE');
        if(empty($params)){
            $return['message'] = 'Could not parse parameter.';
            return $return;
        }
        
        $params = unserialize($params);
        
        $refId = $params['refId'];
        $serviceStatus = $params['serviceStatus'];
        $serviceProvider = $params['serviceProvider'];
        $trackNo = $params['trackingNumber'];
        
        try{
            if(empty($refId)){
                throw new Exception('订单号必传');
            }
            $order = Service_Orders::getByField($refId, 'refrence_no_platform');
            if(! $order){
                throw new Exception('订单号不存在-->' . $refId);
            }
            // 服务商和同步状态
            $updateRow = array('date_last_modify'=>date('Y-m-d H:i:s'))

            ;
            if($serviceStatus && $serviceStatus == 2){
                $updateRow['service_status'] = $serviceStatus;
            }
            if($serviceProvider){
                $updateRow['service_provider'] = $serviceProvider;
            }
            if($trackNo){
                $updateRow['shipping_method_no'] = $trackNo;
            }

//             print_r($updateRow);exit;
            Service_Orders::update($updateRow, $order['order_id'], 'order_id');
            // 日志记录
            $logRow = array(
                'ref_id' => $order['refrence_no_platform'],
                'log_content' => '订单已经同步到服务商'
            );
            Service_OrderLog::add($logRow);
            $return['message'] = 'Success';
            $return['state'] = 1;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }
    
    /*
     * 订单费用对比
     */
    public function updateWmsEbFee($start = '2013-11-30',$end='2013-12-30',$update=true){
        $db = Common_Common::getAdapter();
        $wms_db = Zend_Registry::get('wms_db'); // Wsm 数据库名
        $sql = "select order_id,order_code,reference_no,add_time from {$wms_db}.orders where add_time >= '{$start}' and add_time <= '{$end}' and order_status>0 and order_type=0 order by add_time desc";
        $orders = $db->fetchAll($sql);
//         print_r($orders);exit;
        $result = array();
        $wmsCodes = array();
        foreach($orders as $r){
            $db->beginTransaction();
            try{
                $err = array();
                $sql = "select * from  {$wms_db}.order_sales_cost where order_code='{$r['order_code']}'";
                $row = $db->fetchRow($sql);
                
                $order = Service_Orders::getByField($r['reference_no'], 'refrence_no_platform');
                
                if(!$order){
                    throw new Exception('订单不存在');
                }
                if($order['platform']=='ebay'&&$order['is_merge']=='0'){
                    $org = Service_EbayOrderOriginal::getByField($order['refrence_no_platform'],'OrderID');
                    if($org){
                        $order['platform_fee'] = $org['feeorcreditamount'];
                        $order['ship_fee'] = $org['shippingservicecost'];
                    }
                }

                // print_r($order);exit;
                if($row['ebay_amountpaid'] != $order['amountpaid']&&abs($row['ebay_amountpaid']- $order['amountpaid'])>0.01){
                    $err[] = 'ebay_amountpaid != amountpaid '.$row['ebay_amountpaid'] .' '. $order['amountpaid'];
                } 
                if($row['ebay_total'] != $order['subtotal']&&abs($row['ebay_total']- $order['subtotal'])>0.01){
                    $err[] = 'ebay_total != subtotal '.$row['ebay_total'] .' '. $order['subtotal'];
                }
                if($row['ebay_shipping_cost'] != $order['ship_fee']&&abs($row['ebay_shipping_cost']- $order['ship_fee'])>0.01){
                    $err[] = 'ebay_shipping_cost != ship_fee '.$row['ebay_shipping_cost'] .' '. $order['ship_fee'];
                }
                if($row['ebay_final_value_fee'] != $order['finalvaluefee']&&abs($row['ebay_final_value_fee']- $order['finalvaluefee'])>0.01){
                    $err[] = 'ebay_final_value_fee != finalvaluefee '.$row['ebay_final_value_fee'] .' '. $order['finalvaluefee'];
                }
                if($row['paypal_fee'] != $order['platform_fee']&&abs($row['paypal_fee']- $order['platform_fee'])>0.01){
                    $err[] = 'paypal_fee != platform_fee '.$row['paypal_fee'] .' '. $order['platform_fee'];
                }
                
                if($err){                
                    if($update===true){
                        $updateRow = array(
                                'ebay_amountpaid'=>$order['amountpaid'],
                                'ebay_total' => $order['subtotal'],
                                'ebay_shipping_cost' => $order['ship_fee'],
                                'ebay_final_value_fee' => $order['finalvaluefee'],
                                'paypal_fee' => $order['platform_fee'],
                        );
                        $updateStr = array();
                        foreach($updateRow as $k=>$v){
                            $updateStr[]= $k.'='."'{$v}'";
                        }
                        $updateStr = implode(',', $updateStr);
                
                        $db->query("update {$wms_db}.order_sales_cost set ". $updateStr.' where osc_id = '.$row['osc_id']);
                
                        $db->query("update {$wms_db}.orders set sync_cost_status=6 where order_code='{$r['order_code']}'");
                        $rowDetail = $this->getOrderIntegrateForWms($r['order_code']);
                        if($rowDetail['ask']=='1'){
                            $rowDetail = $rowDetail['result']['orderProduct'];
                            foreach($rowDetail as $p){
                                $opId = $p['op_id'];
                                unset($p['op_id']);
                                unset($p['op_ship_fee']);
                                $updateStr = array();
                                $p['op_update_time'] = date('Y-m-d H:i:s');
                                foreach($p as $k=>$v){
                                    $updateStr[]= $k.'='."'{$v}'";
                                }
                                $updateStr = implode(',', $updateStr);
                                $db->query("update {$wms_db}.order_product set ".$updateStr. ' where op_id = '.$opId);
                            }
                        }
                    }
                
                
                    $wmsCodes[] = $r['order_code'];
                    $r['platform'] = $order['platform'];
                    $r['err'] = $err;
                    $result[] = $r;
                
                }
                $db->commit();
            }catch(Exception $e){
                $db->rollback();
                echo print_r($r,true).$e->getMessage();
            }
        }
        if($result){
            echo implode("\n ", $wmsCodes)."\n";
            print_r($result);
            Ec::showError(print_r($result, true), 'eb_ec_fee_');
        }
        echo '=================== finish =======================';
    }
    
    /**
     * 创建ERP用户
     */
    public function createUser($params){
    	//校验码
    	$sys_check_number = 'ECERP_20140313';
    	//返回信息
    	$return = array(
    			'ask'=>0,
    			'message'=>''
    	);
    	
    	try{
    	/*
    	 * 1、解密传入参数
    	*/
//     	Ec::showError(print_r($params, true), 'ERP_Sync_');exit;
    	$params = Common_Common::authcode($params, 'DECODE');
        //无法解析字符串
        if (empty($params)) {
        	$result['message'] = 'Could not parse parameter errorCode:V001.';
           	return $result;
        }
        //解析
        $params = unserialize($params);
        if (!is_array($params) || empty($params)) {
        	$result['message'] = 'Could not parse parameter errorCode:V002.';
            return $result;
        }
        if (!isset($params['user_code']) || !isset($params['user_sources']) || !isset($params['user_phone'])) {
            $result['message'] = 'Could not parse parameter errorCode:V003.';
            return $result;
        }
//     	$params = serialize($params);
//     	$params = Common_Common::authcode($params, 'DECODE');
    	
    	if(isset($params)){
    		$check_number = $params['check_number'];
    		if($check_number != $sys_check_number){
    			$return['message'] = '校验码：' . $check_number . ' 与ERP系统不一致，请重新核对！';
    			return $return;
    		}
    
    		$user_name = $params['user_name'];
    		$user_code = $params['user_code'];
    		$user_password = $params['user_password'];
    		$user_email = $params['user_email'];
    		$email_verify = $params['email_verify'];
    		$user_phone = $params['user_phone'];
    		$user_note = $params['user_note'];
    		$user_sources = $params['user_sources'];
    		 
    		/*
    		 * 2、检查数据是否重复
    		*/
    		$conUser = array(
    				'user_code'=>$user_code,
    		);
    		$result = Service_User::getByCondition($conUser);
    		if(!empty($result)){
    			$return['message'] = '登陆邮箱:' . $user_code . ' 已进行过数据同步!';
    			return $return;
    		}
    		 
    		/*
    		 * 3、插入数据
    		*/
    		$row = array(
    				'is_admin'=>1,
    				'user_password'=>Ec_Password::getHash($user_password),
    				'user_code'=>$user_code,
    				'user_name'=>$user_name,
    				'user_status'=>1,
    				'user_email'=>$user_email,
    				'email_verify'=>$email_verify,
    				'ud_id'=>0,
    				'up_id'=>1,
    				'user_phone'=>$user_phone,
    				'user_note'=>$user_note,
    				'user_supervisor_id'=>0,
    				'user_add_time'=>date('Y-m-d H:i:s'),
    				'user_update_time'=>date('Y-m-d H:i:s'),
    				'user_sources'=>$user_sources,
    		);
    		$userId = Service_User::add($row);
    		$updateRow = array('company_code'=>'ec-'.$userId);
    		Service_User::update($updateRow, $userId,'user_id');
    		 
    		/*
    		 * 4、Success
    		*/
    		$return['ask'] = 1;
    		$return['message'] = '创建用户成功';
    		return $return;
    	}else{
    		$return['message'] = '无数据传入，请检查传入参数！';
    		return $return;
    	}
    	}catch (Exception $e){
	    	Ec::showError(print_r($params, true), 'ERP_Sync_');
	    	Ec::showError(print_r($e->getMessage(), true), 'ERP_Sync_');
	    	$return['message'] = 'API error ：' . $e->getMessage();
	    	return $return;
    	}
    }
}