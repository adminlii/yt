<?php
class Platform_OrderProcess {
    
    /**
     * 订单暂存
     * @param unknown_type $ref_id_arr
     */
    public function order_hold($ref_id_arr){
        $return = array('ask'=>0,'message'=>'Fail');
        try {
            if(empty($ref_id_arr)){
                throw new Exception(Ec::Lang('没有订单需要操作'));
            }
            $results = array();
            foreach($ref_id_arr as $ref_id){
                $hold = New Platform_OrderHold();
                $hold->setRefId($ref_id);
                $rs = $hold->process();
                $results[] = $rs;
            } 
            $return['results'] = $results;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单处理结束');
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }

    /**
     * 订单暂存
     * @param unknown_type $ref_id_arr
     */
    public function order_draft($ref_id_arr){
        $return = array('ask'=>0,'message'=>'Fail');
        try {
            if(empty($ref_id_arr)){
                throw new Exception(Ec::Lang('没有订单需要操作'));
            }
            $results = array();
            foreach($ref_id_arr as $ref_id){
                $draft = New Platform_OrderDraft();
                $draft->setRefId($ref_id);
                $rs = $draft->process();
                $results[] = $rs;
            }
            $return['results'] = $results;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单处理结束');
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }
    
        return $return;
    }
    /**
     * 订单审核
     * @param unknown_type $ref_id_arr
     * @param unknown_type $shipper_account
     * @param unknown_type $product_code
     * @param unknown_type $warehouse_code
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function order_verify($ref_id_arr,$shipper_account,$product_code,$warehouse_code='',$status='D'){
        $return = array('ask'=>0,'message'=>'Fail');
        try {
            if(empty($ref_id_arr)){
                throw new Exception(Ec::Lang('没有订单需要操作'));
            }
            $shipperArr = Service_CsiShipperTrailerAddress::getByField($shipper_account, 'shipper_account');
//             print_r($ref_id_arr);exit;
            $results = array();
            foreach($ref_id_arr as $ref_id){
                $verify = New Platform_OrderVerify();
                $verify->setRefId($ref_id);                
                $verify->setShipper($shipperArr);
//                 echo $product_code;exit;
                $verify->setProductCode($product_code);
                $verify->setWarehouseCode($warehouse_code);
                $verify->setStatus($status);
                $rs = $verify->process();
                $results[] = $rs;
            }
            $return['results'] = $results;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单处理结束');
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }
    
        return $return;
    }

    /**
     * 标记发货
     * @param unknown_type $order_arr
     * @throws Exception
     * @return multitype:number string NULL multitype:multitype:number string NULL multitype:
     */
    public function order_ship_mark($order_arr){
        $return = array('ask'=>0,'message'=>'Fail');
        try {
            if(empty($order_arr)){
                throw new Exception(Ec::Lang('没有订单需要操作'));
            }          
            $results = array();
            foreach($order_arr as $order){
                $ref_id = $order['ref_id'];
                $carrier_name = $order['carrier_name'];
                $shipping_method_no = $order['shipping_method_no'];
                $process = New Platform_OrderShipMark();
                $process->setRefId($ref_id);
                $process->setCarrierName($carrier_name);
                $process->setShippingMethod($shipping_method_no);
                $rs = $process->process();
                
                $results[] = $rs;
            }
            $return['results'] = $results;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单处理结束');
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }
    
        return $return;
    }

    /**
     * 分配运输方式
     * @param unknown_type $order_arr
     * @throws Exception
     * @return multitype:number string NULL multitype:multitype:number string NULL multitype:
     */
    public function order_allot($order_arr){
        $return = array('ask'=>0,'message'=>'Fail');
        try {
            if(empty($order_arr)){
                throw new Exception(Ec::Lang('没有订单需要操作'));
            }
            $results = array();
            foreach($order_arr as $order){
                $ref_id = $order['ref_id'];
                $shipping_method = $order['shipping_method'];
//                 print_r($order);exit;
//                 echo $shipping_method;exit;
                $process = New Platform_OrderAllot();
                $process->setRefId($ref_id);
                $process->setShippingMethod($shipping_method);
                $rs = $process->process();
    
                $results[] = $rs;
            }
            $return['results'] = $results;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单处理结束');
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }
    
        return $return;
    }
    /**
     * 标记发货
     * @param unknown_type $ref_id_arr
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function order_complete_sale($order_arr,$immediately=false){
        $return = array('ask'=>0,'message'=>'Fail');
        $results = array();
        $results1 = array();
        try {
            if(empty($order_arr)){
                throw new Exception(Ec::Lang('没有订单需要操作'));
            }
            
            foreach($order_arr as $o){
                $ref_id = $o['ref_id'];
                $carrier_name = $o['carrier_name'];
                $shipping_method_no = $o['shipping_method_no'];
                $process = New Platform_OrderShipMark();
                $process->setRefId($ref_id);
                $process->setCarrierName($carrier_name);
                $process->setShippingMethod($shipping_method_no);
                $rs = $process->process();
                $results[] = $rs;
                $order = Service_Orders::getByField($ref_id,'refrence_no_platform');
                $platform = strtolower($order['platform']);
                switch($platform){
                    case 'ebay':
                        $completeSale = new Ebay_Order_CompleteSale($ref_id);
                        $rs1 =  $completeSale->completeSale();
                        $results1[] = $rs1;
                        break;
                    case 'amazon':
                        throw new Exception('系统未支持amazon标记发货,系统开发中，敬请期待...');
                        break;
                    case 'aliexpress':
                        $completeSale = new Aliexpress_Order_CompleteSale($ref_id);
                        $rs1 =  $completeSale->completeSale();
                        $results1[] = $rs1;
                        break;
                    default:
                        throw new Exception($platform.'平台不支持标记发货');
                }
            }
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单处理结束');
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }    
        $return['results'] = $results;
        $return['results1'] = $results1;
        return $return;
    }

    public function order_complete_sale_ebay(){
        
    }
    
    /**
     * 间隔天数
     * @return number|mixed
     */
    public static function load_platform_order_day(){
    	$config = Service_Config::getByField('LOAD_PLATFORM_ORDER_DAY', 'config_attribute');
    	if(!$config){
    		return 3;
    	}
    	return $config['config_value'];
    }
    /**
     * 下载平台订单
     * @param unknown_type $user_account
     * @param unknown_type $company_code
     * @param unknown_type $start
     * @param unknown_type $end
     * @throws Exception
     * @return multitype:number string NULL multitype:
     */
    public function load_platform_order($user_account,$company_code,$start,$end,$day=3){
        $return = array('ask'=>0,'message'=>'Fail');
        try {
        	//间隔天数
        	$day = $this->load_platform_order_day();
            if(empty($user_account)){
                throw new Exception(Ec::Lang('账号不可为空'));
            }
            if(empty($company_code)){
                throw new Exception(Ec::Lang('公司代码不可为空'));
            }
            if(empty($start)){
                throw new Exception(Ec::Lang('起始时间不可为空'));
            }
            if(empty($end)){
                throw new Exception(Ec::Lang('结束时间不可为空'));
            } 
            $start_time = strtotime($start);
            $end_time = strtotime($end);
            if($start_time>$end_time){
                throw new Exception(Ec::Lang('结束时间必须大于开始时间'));                
            }
            if($end_time-$start_time>3600*24*$day){
                throw new Exception(Ec::Lang('开始时间与结束时间间隔不可大于'.$day.'天'));
            }
            $con = array('user_account'=>$user_account,'company_code'=>$company_code);
            $pUser = Service_PlatformUser::getByCondition($con);
            if(empty($pUser)){
                throw new Exception(Ec::Lang('账户不存在'));                
            }
//             print_r($pUser);exit;
            //取得第一条账号信息
            $pUser = array_pop($pUser);            
            $return['platform'] = strtoupper($pUser['platform']);
            //转大写
            $platform = strtoupper($pUser['platform']);
            switch($platform){
                case 'EBAY':
                	$return['load_platform_order_rs'] = array('ask'=>0,'message'=>'下载订单失败');
                	$msgArr = array();
                	try {
                		$svc = new Ebay_LoadEbayOrderService();
                		$svc->setCompanyCode($company_code);
                		$svc->setUserAccount($user_account);
                		$start = date('Y-m-d\TH:i:s.000\Z',strtotime($start));
                		$end = date('Y-m-d\TH:i:s.000\Z',strtotime($end));
                		$return['start'] = $start;
                		$return['end'] = $end;
                		$count = $svc->callEbay($start, $end);

                		$return['load_platform_order_rs']['ask'] = 1;
                		$return['load_platform_order_rs']['message'] = 'Success';

                		$orderList = $svc->getOrderData();
                		$msgArr[] = "下载订单列表成功,在时间段{$start}~{$end}之内，共".count($orderList)."个订单";
                		foreach($orderList as $order_sn=> $order){
                			$rs = Ebay_GenEbayOrderService::generateOrderSingleTransaction($order_sn);
                			if($rs['ask']){
                				$msgArr[] = "[{$order_sn}]生成平台订单成功";		
                			}else{
                				$msgArr[] = "[{$order_sn}]生成平台订单失败，失败原因:".$rs['message'];	                				
                			}
                		}               		

                		$return['count'] = $count;
                		$return['orderList'] = $orderList;
                	} catch (Exception $e) {
                		$msgArr[] = "下载订单失败,失败原因：".$e->getMessage();
                	}
                	$return['load_platform_order_rs']['msgArr'] = $msgArr;
                    break;
                    
                case 'AMAZON'://      
                	//超时设置 
                	set_time_limit(0);   
//                 	$genOrderArr = Amazon_Order_GenOrder::genOrderBatch();
//                 	print_r($genOrderArr);exit;
//                 	Zend_Registry::set('SAPI_DEBUG', true);    
                	$idArr = array (
//                 			'303-6794182-2845920',
//                 			'028-2742057-1673943',
//                 			'302-3209453-6386734',
//                 			'305-9619201-0694755'
                	);
					$svc = new Amazon_Order_OrderServiceProcess ();
//                 	echo __LINE__;exit;
					if(!empty($idArr)) {						
						$orderList = $svc->getOrderByOrderIdArr($user_account,$company_code, $idArr);
					}else{
						$orderList = $svc->getOrderList($user_account,$company_code, $start, $end);
					}              	
					 
                	$return['load_platform_order_rs'] = $orderList;
                    break;
                case 'ALIEXPRESS1111'://废弃
//                     $obj = new Aliexpress_AliexpressOrderListService();
                    
//                     $rs = $obj->loadOrderList($company_code, $user_account, $start, $end);
//                     $return['loadOrderList'] = $rs;
// //                     print_r($rs);exit;
                    
//                     $obj = new Aliexpress_AliexpressOrderDetailService();
//                     $rs = $obj->loadOrderDetail($company_code, $user_account);
//                     $return['loadOrderDetail'] = $rs;
// //                     print_r($rs);exit;
                    
//                     $obj = new Aliexpress_GenerateSystemOrders();
//                     $rs = $obj->callAliexpressOrdersToSysOrder($user_account,$company_code);
//                     $return['callAliexpressOrdersToSysOrder'] = $rs;
// //                     print_r($rs);exit;
                    
                    break;
                case 'ALIEXPRESS':
//                 	$genOrderArr = Aliexpress_Order_GenOrder::genOrderBatch();
//                 	print_r($genOrderArr);
//                 	exit;
                    $obj = new Aliexpress_Order_OrderServiceProcess();                    
                    $orderListQuery = $obj->orderListQuery( $user_account,$company_code, $start, $end);
                    $return['load_platform_order_rs'] = $orderListQuery;                    
                    break;
                case 'MABANG' :	
                     $obj = new Mabang_Order_OrderServiceProcess ();
                    
                     $orderListQuery = $obj->orderListQueryByTime ( $user_account, $company_code, $start, $end );
                    		
                     $return ['load_platform_order_rs'] = $orderListQuery;
                    break;
                default:
                    throw new Exception(Ec::Lang('不支持的平台'));                    
                
            }
            //没有sku的填充
            $sql = "update order_product set product_sku='-NO-SKU-' where product_sku='' or product_sku is null;";
            Common_Common::query($sql);
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('订单拉取完毕');
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
            $return['exception'] =  $e->getLine();
           
        }
//         print_r($return);exit;
        //日志
		Ec::showError ( print_r ( $return, true ), 'load_platform_order_'.$platform ); 
        return $return;
    }
    /**
     * 创建订单
     * @param unknown_type $row
     * @return Ambigous <multitype:number NULL , multitype:number string mixed >
     */
	public function createOrderTransaction($row) {
		$result = array (
				"ask" => 0,
				"message" => "Create Order Fail" 
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$result = $this->createOrder ( $row );
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode () 
			);
		}
		return $result;
	}
	//占位，下一步添加该功能..................................................
	private function _orderValidate($orderData){
		$orderRow = $orderData['order'];
		$addressRow = $orderData['address'];
		$orderProduct = $orderData['order_product'];
		//判断是否有产品
		if(empty($orderProduct)){
			//产品为必填
		    throw new Exception(Ec::Lang('product_required'));
		}
		//判断仓库
		if(empty($orderRow['warehouse_id'])){
		    //throw new Exception('仓库必填');
		}
		//判断国家 
		if(empty($addressRow['Country'])){
			//收件人国家必填
		    throw new Exception(Ec::Lang('recipient_country_required'));
		}
		//判断收件人信息

		if(empty($addressRow['StateOrProvince'])){
// 		    throw new Exception('省份/州必填');
		}

		if(empty($addressRow['CityName'])){
			//城市必填
		    throw new Exception(Ec::Lang('city_required'));
		}
		if(empty($addressRow['Street1'])&&empty($addressRow['Street2'])&&empty($addressRow['Street3'])){
			//地址必填一个
		    throw new Exception(Ec::Lang('address_required_a'));
		}
		if(empty($addressRow['Name'])){
			//收件人必填
		    throw new Exception(Ec::Lang('recipient_required'));
		}
		//以上判断需要抛出异常
		
	}
	/**
	 * 创建订单
	 * @param unknown_type $row
	 * @throws Exception
	 * @return multitype:number string mixed
	 */
	public function createOrder($row) {}
	/**
	 * 更新订单
	 * @param unknown_type $row
	 * @param unknown_type $orderId
	 * @return Ambigous <multitype:number NULL , multitype:number string unknown >
	 */
	public function updateOrderTransaction($row, $orderId) {
		$result = array (
				"ask" => 0,
				"message" => "Order Update Fail" 
		);
		
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$result = $this->updateOrder ( $row, $orderId );
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode () 
			);
		}
		return $result;
	}
	
	/**
	 * 批量更新订单状态
	 * @param unknown_type $orderIds
	 * @param unknown_type $status
	 * @return multitype:multitype:number NULL
	 */
	public function updateOrderStatusMultiTransaction( $orderIds,$status) {}

	/**
	 * 批量标记发货
	 * @param unknown_type $orderIds
	 * @throws Exception
	 * @return multitype:number string multitype:multitype:string unknown  multitype:unknown   Ambigous <multitype:, multitype:unknown >
	 */
	public function batchShipOrder($orderIds ,$sync_status = '',$order_status_modify = true) {
	    $results = array(
	    			'ask'=>0,
	    			'message'=>Ec::Lang('the_results_as_follows').'：'//处理结果如下：
	    		);
        $success = $fail = array();
        try {
            $orderArr = array();
            $orderOutOfStockArr = array();
            $orderData = array();
            foreach($orderIds as $id){
                $order = Service_Orders::getByField($id,'order_id');
                if($order['order_status'] == '6'){
                	$orderOutOfStockArr[] = $order['refrence_no_platform'];
                }else{
	                $orderArr[] = $order['refrence_no_warehouse'];
                }
                $orderData[] = array(
                		'state'=>1,
                		'referenceNo'=>$order['refrence_no_platform'],
                		'message'=>'Operation Successfully Completed(1)'
                		);
            }
            
            $response = null;
//             print_r($orderArr);
//             print_r($orderOutOfStockArr);
//             echo '----';
            if($order_status_modify){
            	//修改订单状态时，需要标记仓库发货
	            $process = new Service_OrderForWarehouseProcessNew();
	            $result_other = $process ->batchShipOrder($orderArr);
	            
	            $result_outofstock = $process->batchStopOrder($orderOutOfStockArr);
	            $response = array(
	            		$result_other,
	            		$result_outofstock
	            		);
            }else{
            	//不修改状态,只发货
            	$response = array(
	            			array(
            					'state'=>1,
            					'data'=>$orderData
	            			)
            			);
            }
            
//             print_r($result);
//             exit;
			foreach ($response as $key => $result) {
				if($result['state']=='1'){
					foreach($result['data'] as $row){
						if($row['state'] == '1'){
							$updateRow = array(
									'date_last_modify' => date('Y-m-d H:i:s')
							);
							if($order_status_modify){
								$updateRow['order_status'] = 4;
								$updateRow['date_warehouse_shipping'] = date('Y-m-d H:i:s');
							}
							if($sync_status != ''){
								$updateRow['sync_status'] = $sync_status;
							}
							
							if(!Service_Orders::update($updateRow, $row['referenceNo'], 'refrence_no_platform')){
								$fail[] = array(
										'ref_id' => $row['referenceNo'],
										'message' => Ec::Lang('operationFail')//'操作失败'
								);
								$logRow = array('ref_id'=>$row['referenceNo'],'log_content' => '更新订单失败');
							}else{
								$success[] = array(
										'ref_id' => $row['referenceNo'],
										'message' => $row['message']
								);
								$logRow = array('ref_id'=>$row['referenceNo'],'log_content' => $row['message']);
							}
						}else{
							$fail[] = array(
									'ref_id' => $row['referenceNo'],
									'message' => $row['message']
							);
							$logRow = array('ref_id'=>$row['referenceNo'],'log_content' => $row['message']);
						}
						if($logRow){
							$log_content_title = '';
							if($sync_status == '5'){//订单已在平台标记发货
								$log_content_title = '标记订单，已在平台上标记发货，不在做标记发货处理，';
							}else{
								if($order_status_modify){
									$log_content_title = '标记发货->订单转至已发货，并标记发货，';
								}else{
									$log_content_title = '标记发货->订单标记发货，';
								}
							}
							
							$logRow['log_content'] = $log_content_title . $logRow['log_content'];
							Service_OrderLog::add($logRow);
						}
					}
				}else{
					throw new Exception($result['message'],$result['err_code']);
				}
			}
        } catch ( Exception $e ) {
            $results = array (
                    "ask" => 0,
                    "message" => $e->getMessage (),
                    'errorCode' => $e->getCode ()
            );
        }
        $results['success'] = $success;
        $results['fail'] = $fail;
        Ec::showError(print_r($results,true),'xxxxxx');
	    return $results;
	}
	
	


	/**
	 * 发货审核事物,将订单信息发送到仓库系统
	 * @param unknown_type $orderIds
	 * @param unknown_type $warehouseId
	 * @param unknown_type $shippingMethod
	 * @param unknown_type $audit_type  默认人工审核
	 * @param unknown_type $warehouseVerifyShippingMethod  需仓库确定实物真正的仓库配送方式
	 * @throws Exception
	 * @return multitype:number string NULL Ambigous <multitype:, mixed, multitype:number string NULL multitype:unknown  Ambigous <multitype:, multitype:unknown multitype:NULL  > multitype:number string unknown Ambigous <string, unknown>  >
	 */
    public static function orderVerifyBatchTransaction($orderIds, $warehouseId, $shippingMethod, $audit_type = '2', $warehouseVerifyShippingMethod = 0)
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
        $errors = array();
        try{
            if(empty($orderIds)){
            	//'没有选择订单'
                throw new Exception(Ec::Lang('please_select_orders'));
            }
            
            if(empty($warehouseId)){
            	//'没有选择仓库'
                throw new Exception(Ec::Lang('unallocated_warehouse_orders'));
            }
            if(empty($shippingMethod)){
            	//'没有选择运输方式'
                throw new Exception(Ec::Lang('no_transport_orders'));
            }
            // 逻辑：判断是否是自营仓库
            // 将订单信息查询出来，传给wms
            $return['ask'] = 1;
            $results = array();
            $needToWarehouseRefNos = array();
            foreach($orderIds as $orderId){
                $db = Common_Common::getAdapter();
                $db->beginTransaction();
                $order = Service_Orders::getByField($orderId, 'order_id');
                try{
                    if(empty($order)){
                    	//'订单不存在'
                        throw new Exception(Ec::Lang('order_not_exist'));
                    }
                    $allowArr = array('2','5','7');
                    if(!in_array($order['order_status'],$allowArr)){
                    	//订单状态异常
                        throw new Exception(Ec::Lang('order_status_exception'));                        
                    }
                    $updateRow = array(
                            'shipping_method' => $shippingMethod,
                            'warehouse_id' => $warehouseId,
                            'date_release' => date('Y-m-d H:i:s'),
                            'date_last_modify' => date('Y-m-d H:i:s'),
                    		'audit_type'=>$audit_type,
                    		'check_shipping_method'=>$warehouseVerifyShippingMethod,
                    );
                   
                    switch(strtolower($order['platform'])){
                        case 'amazon':
                            $con = array('platform'=>'amazon','short_code'=>$shippingMethod);
                            $shippingMethodPlatform = Service_ShippingMethodPlatform::getByCondition($con);
                            if(!$shippingMethodPlatform||empty($shippingMethodPlatform[0]['carrier'])){
                                //该运输方式'.$shippingMethod.'未设定对应的承运商名称,请从’系统管理->运输方式映射‘设置
                            	throw new Exception(Ec::Lang('shipping_methods_not_set_carrier','auto',$shippingMethod));
                            }
                                                        
                            $shippingMethodPlatform = $shippingMethodPlatform[0];
                            $updateRow['shipping_method_platform'] = $shippingMethodPlatform['carrier'];
                            break;
                    } 
                     /* */

                    if(! Service_Orders::update($updateRow, $orderId, 'order_id')){
                        throw new Exception('Inner Error');
                    }
                    // 这里还有日志信息，以后添加
                    $note = '';
                    if ($warehouseVerifyShippingMethod == '1') {
                        $note .= ',需仓库确定实物真正的仓库配送方式';
                    }
                    $logRow = array(
                        'ref_id' => $order['refrence_no_platform'],
                        'log_content' => '订单审核,分配订单仓库ID:' . $warehouseId . ',分配运输方式：' . $shippingMethod.$note,
                        'op_id' => ''
                    );
                    self::writeOrderLog($logRow);
                    
                    $db->commit();
                    
                    $needToWarehouseRefNos[]= $order['refrence_no_platform'];
                }catch(Exception $ee){
                    $db->rollback();                    
                    $rs = array(
                            'ask' => 0,
                            'message' => $ee->getMessage(),
                            'order_id' => $orderId,
                            'order_status' => $order ? $order['order_status'] : '',
                            'ref_id' => $order ? $order['refrence_no_platform'] : '',
                            'refrence_no_platform' => $order ? $order['refrence_no_platform'] : ''
                    );
                    // 这里还有日志信息，以后添加
                    if($order){
                        $logRow = array(
                                'ref_id' => $order['refrence_no_platform'],
                                'log_content' => '订单审核失败,无法分配订单仓库ID:' . $warehouseId . ',分配运输方式：' . $shippingMethod.',失败原因：'.$ee->getMessage(),
                                'op_id' => ''
                        );
                        self::writeOrderLog($logRow);
                    }
                    
                    $errors[] = $rs;
                }
            }
            $return['errors'] = $errors;
            if(!empty($needToWarehouseRefNos)){
                $wmsProcess = new Service_OrderForWarehouseProcessNew();
                $apiReturn = $wmsProcess->submit($needToWarehouseRefNos);//将数据发送到仓库
                if($apiReturn['state']){
                    $return['ask'] = 1;
                }else{
                    $return['ask'] = 0;
                    $return['message'] =  $apiReturn['message'];
                }
                //             $return['result'] = $results;
                $return['apiReturn'] = $apiReturn;
            }
            
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
//         Ec::showError(print_r($return,true),'__________');
//         print_r($return);exit;
        return $return;
    } 

    /**
     *  发货审核,将订单信息发送到仓库系统
     * @param unknown_type $orderIds
     * @param unknown_type $audit_type  默认人工审核
     * @param unknown_type $warehouseVerifyShippingMethod  需仓库确定实物真正的仓库配送方式
     * @return multitype:number string NULL Ambigous <multitype:, mixed, multitype:number string NULL multitype:multitype:unknown  multitype:unknown NULL   Ambigous <multitype:, multitype:number string unknown Ambigous <string, unknown> > >
     */
    public static function orderVerifyBatchNewTransaction($orderIds, $audit_type = '2', $warehouseVerifyShippingMethod = 0)
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
        try{
            if(empty($orderIds)){
            	//'没有选择订单'
                throw new Exception(Ec::Lang('please_select_orders'));
            }
    
            // 逻辑：判断是否是自营仓库
            // 将订单信息查询出来，传给wms
            $errors = array();
            $needToWarehouseRefNos = array();
            foreach($orderIds as $orderId){
                $order = Service_Orders::getByField($orderId, 'order_id');
                try{
                    if(empty($order)){
                    	//'订单不存在'
                        throw new Exception(Ec::Lang('order_not_exist'));
                    }
                    $allowArr = array('2','5','7');
                    if(!in_array($order['order_status'],$allowArr)){
                    	//订单状态异常
                        throw new Exception(Ec::Lang('order_status_exception'));
                    }
                    
                    if(empty($order['warehouse_id'])){
                    	//'订单未分配仓库'
                        throw new Exception(Ec::Lang('unallocated_warehouse_orders'));                        
                    } 
                    if(empty($order['shipping_method'])){
                    	//'订单未分配运输方式'
                        throw new Exception(Ec::Lang('no_transport_orders'));                        
                    }
                    $updateRow = array(
                            'date_release' => date('Y-m-d H:i:s'),
                            'date_last_modify' => date('Y-m-d H:i:s'),
                    		'audit_type'=>$audit_type,
                    		'check_shipping_method'=>$warehouseVerifyShippingMethod,
                    );
                     
                    switch(strtolower($order['platform'])){
                        case 'amazon':
                            $shippingMethod = $order['shipping_method'];
                            $con = array('platform'=>'amazon','short_code'=>$shippingMethod);
                            $shippingMethodPlatform = Service_ShippingMethodPlatform::getByCondition($con);
                            if(!$shippingMethodPlatform||empty($shippingMethodPlatform[0]['carrier'])){
                                //'该运输方式'.$shippingMethod.'未设定对应的承运商名称'
                            	throw new Exception(Ec::Lang('shipping_methods_not_set_carrier','auto',$shippingMethod));
                            }
                    
                            $shippingMethodPlatform = $shippingMethodPlatform[0];
                            $updateRow['shipping_method_platform'] = $shippingMethodPlatform['carrier'];
                            break;
                    }
                    /* */
                    
                    if(! Service_Orders::update($updateRow, $orderId, 'order_id')){
                        throw new Exception('Inner Error');
                    }
                    $needToWarehouseRefNos[]= $order['refrence_no_platform'];
                }catch(Exception $ee){
                    $rs = array(
                        'ask' => 0,
                        'message' => $ee->getMessage(),
                        'order_id' => $orderId,
                        'order_status' => $order ? $order['order_status'] : '',
                        'ref_id' => $order ? $order['refrence_no_platform'] : '',
                        'refrence_no_platform' => $order ? $order['refrence_no_platform'] : ''
                    );
                    $errors[] = $rs;
                }
            }
            $return['errors'] = $errors;
            if(!empty($needToWarehouseRefNos)){//已经分配好仓库和运输方式
                $wmsProcess = new Service_OrderForWarehouseProcessNew();
                $apiReturn = $wmsProcess->submit($needToWarehouseRefNos);//将数据发送到仓库    
                
                $return['apiReturn'] = $apiReturn;
                if($apiReturn['state']==0){
                    if($apiReturn['err_code']=='5000'){
                        throw new Exception($apiReturn['message']);
                    }
                }      
            }
    
            $return['ask'] = '1';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        } 
        return $return;
    }
    /**
     * 订单手工分仓 需要指定对应的仓库和运输方式
     * @param unknown_type $orderIds
     * @param unknown_type $warehouseId
     * @param unknown_type $shippingMethod
     * @throws Exception
     * @return multitype:number string NULL multitype:multitype:string unknown NULL Ambigous <string, mixed>
     */
    public static function orderSetWarehouseShipBatchTransaction($orderIds,$warehouseId,$shippingMethod){
        $return = array(
                'ask' => 0,
                'message' => ''
        );
        try{
            if(empty($orderIds)){
            	//没有选择订单
                throw new Exception(Ec::Lang('please_select_orders'));
            }
    
            if(empty($warehouseId)){
            	//没有选择仓库
                throw new Exception(Ec::Lang('unallocated_warehouse_orders'));
            }
            if(empty($shippingMethod)){
            	//没有选择运输方式
                throw new Exception(Ec::Lang('no_transport_orders'));
            }
            // 逻辑：判断是否是自营仓库
            // 将订单信息查询出来，传给wms
            $return['ask'] = 1;
            $results = array();
            $needToWarehouseRefNos = array();
            foreach($orderIds as $orderId){
                $db = Common_Common::getAdapter();
                $db->beginTransaction();
                try{
                    $order = Service_Orders::getByField($orderId, 'order_id');
                    if(empty($order)){
                    	//订单不存在
                        throw new Exception(Ec::Lang('order_not_exist'));
                    }
                    $allowArr = array('2','5','7');
                    if(!in_array($order['order_status'],$allowArr)){
                        //订单状态异常
                    	throw new Exception(Ec::Lang('order_status_exception'));
                    }
                    $updateRow = array(
                            'shipping_method' => $shippingMethod,
                            'warehouse_id' => $warehouseId,
                            'date_release' => date('Y-m-d H:i:s'),
                            'date_last_modify' => date('Y-m-d H:i:s')
                    );
                    if(! Service_Orders::update($updateRow, $orderId, 'order_id')){
                        throw new Exception('Inner Error');
                    }
                    // 这里还有日志信息，以后添加
                    $logRow = array(
                            'ref_id' => $order['refrence_no_platform'],
                            'log_content' => '订单审核,分配订单仓库ID:' . $warehouseId . ',分配运输方式：' . $shippingMethod,
                            'op_id' => ''
                    );
                    self::writeOrderLog($logRow);
    
                    $db->commit();
                    $rs = array(
                        'ask' => '1',
                        'message' => Ec::Lang('distribution_warehouses_and_transport_success'),// '分配仓库和运输方式成功'
                        'order_id' => $orderId,
                        'ref_id' => $order['refrence_no_platform']
                    );
                    $needToWarehouseRefNos[]= $order['refrence_no_platform'];
                }catch(Exception $ee){
                    $db->rollback();
                    $rs = array(
                        'ask' => 0,
                        'message' => $ee->getMessage(),
                        'order_id' => $orderId,
                        'ref_id' => $order ? $order['refrence_no_platform'] : ''
                    );
                }
                $results[$orderId] = $rs;
            }

            $return['ask'] = '1';
            $return['result'] = $results;
            
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }  

    /**
     * 获得订单产品数组的，SKU对应关系信息
     * @param unknown_type $order_row			订单行
     * @param unknown_type $order_product_rows	订单产品数组
     */
    public static function getProductCombineRelationList($order_row,$order_product_rows){            
        // 已经提交到仓库，取当时的对应关系,该信息记录在warehouse_sku字段，内容格式如下(XL609*1*40.000;XL907*1*40.000;XL909*1*20.000)
        $toWmsStatus = array(
            '3',
            '4',
            '6'
        );
        if(in_array($order_row['order_status'], $toWmsStatus)){
            foreach($order_product_rows as $key => $val){
                $val['product_sku'] = empty($val['product_sku']) ? '--NoSku--' : $val['product_sku'];
                /*
                 * 产品没有对应关系，warehouse_sku记录的是 $val['product_sku'] . '*' . $val['op_quantity']. '*100.000'
                 */
                $r = array();
                if(! empty($val['warehouse_sku']) && ($val['warehouse_sku'] != $val['product_sku'] . '*' . $val['op_quantity'] . '*100.000')){
                    $rArr = explode(';', $val['warehouse_sku']);
                    foreach($rArr as $k => $vv){
                        // XL609*1*40.000;XL907*1*40.000;XL909*1*20.000
                        $t = explode('*', $vv);
                        $r[] = array(
                            'op_quantity' => $val['op_quantity'],
                            'product_sku' => $val['product_sku'],
                            'pcr_product_sku' => $t[0],
                            'pcr_quantity' => $t[1],
                            'pcr_percent' => $t[2]
                        );
                    }
                }else{
                    $r[] = array(
                        'op_quantity' => $val['op_quantity'],
                        'product_sku' => $val['product_sku'],
                        'pcr_product_sku' => $val['product_sku'],
                        'pcr_quantity' => 1,
                        'pcr_percent' => '100.000'
                    );
                }
                $val['sub_product'] = $r;                
                $order_product_rows[$key] = $val;
            }
        }else{
            foreach($order_product_rows as $key => $val){
                $val['product_sku'] = empty($val['product_sku']) ? '--NoSku--' : $val['product_sku'];                
                $rArr = Service_ProductCombineRelationProcess::getRelation($val['product_sku'], $order_row['user_account']);                
                $r = array();
                if($rArr){
                    foreach($rArr as $k => $vv){
                        $r[] = array(
                            'op_quantity' => $val['op_quantity'],
                            'product_sku' => $val['product_sku'],
                            'pcr_product_sku' => $vv['pcr_product_sku'],
                            'pcr_quantity' => $vv['pcr_quantity'],
                            'pcr_percent' => $vv['pcr_percent']
                        );
                    }
                }else{
                    $r[] = array(
                        'op_quantity' => $val['op_quantity'],
                        'product_sku' => $val['product_sku'],
                        'pcr_product_sku' => $val['product_sku'],
                        'pcr_quantity' => 1,
                        'pcr_percent' => '100.000'
                    );
                }
                $val['sub_product'] = $r;
                $order_product_rows[$key] = $val;
            }
        }
        
        return $order_product_rows;
    }
}