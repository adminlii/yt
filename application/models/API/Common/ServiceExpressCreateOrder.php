<?php

class API_Common_ServiceExpressCreateOrder
{

	/**
	 * @desc 判断任务&订单状态是否符合通知条件
	 * @param string $ops_id
	 * @param string $orderCode
	 * @param int $status
	 * @return array
	 */
	private function vailOrderProcessingTransaction($ops_id = '0', $orderCode = '', $status = 1)
	{
		$result = array("ask" => 0, 'message' => '', 'data' => array());
		$db = Common_Common::getAdapter();
		$db->beginTransaction();
		$date = date('Y-m-m H:i:s');
		$ask = 1;
		try {
			$objOps = Service_OrderProcessing::getByField($ops_id);
	
			if ($objOps["ops_syncing_status"] != '0') {
				throw new Exception("执行中或已完成", 500);
			}
			//是否更新为处理中
			if ($status == '1') {
				Service_OrderProcessing::update(array("ops_syncing_status" => "1", 'ops_update_time' => $date), $ops_id, "ops_id");
			}
			
			$db->commit();
			$result['ask'] = $ask;
		} catch (Exception $e) {
			$db->rollBack();
			$result['ask'] = 0;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}
	
    /**
     * @desc 通知物流订单
     * @param array $conditionArr
     * @param int $loop
     */
    public function createOrderToService($formalCode,$order_config, $init=true,$loop = 0)
    {
        Common_Common::myEcho('开始通知订单！');
        Common_Common::myEcho('通知中。。。。。。');
        $date = date('Y-m-d H:i:s');

        /*
         * 1、获取所有需要通知到服务商的订单
        */
        $pageSize = 20;
        $page = 1;
        $condition = array(
            "ops_status" => "0",
            "sync_service_status" => array(0, 2),
        	"formal_code" => $formalCode,	
        );
        
        //指定渠道
        if (empty($formalCode)) {
        	Common_Common::myEcho('未指定运输方式直接返回');
        	return;
        }

        $count = Service_OrderProcessing::getByCondition($condition, "count(*)");
        $totalPage = ceil($count / $pageSize);

        
        //指定页数
        $totalPage = $loop == '0' ? $totalPage : ($loop > $totalPage ? $totalPage : $loop);
        //减少执行时间
        if ($count == 0) {
            Ec::showError("此次请求，无需要同步订单！" . $date, 'express_create_orders_excute');
            Common_Common::myEcho('本次没有需要同步的订单.....over....');
            return;
        }
        
        /*
         * 2、获取订单对应的物流服务商
        */
        $objCommon = new API_Common_ServiceCommonClass();
        $channel = $objCommon->getServiceChannelByFormalCode($formalCode);
        if (empty($channel)) {
        	throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
        }

		//所有渠道都走API_YunExpress_ForApiService
		$channel['as_code'] = "YUNEXPRESS";
        $class = $objCommon->getForApiServiceClass($channel['as_code']);
        if (empty($class)) {
        	throw new Exception("无法获取到[{$formalCode}]对应的数据映射类");
        }
        if (class_exists($class)) {
        	$obj = new $class();
        } else {
        	throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
        }
        
        //调整超时
        $theTime = $runTime = time();
        //按页同步订单
        for ($i = 1; $i <= $totalPage; $i++) {
            $synchronousOrder = Service_OrderProcessing::getByCondition(
                $condition,
                array("order_processing.order_id","order_processing.ops_id", "order_processing.ops_syncing_status","order_processing.shipper_hawbcode", "order_processing.ops_type"),
                $pageSize,
                $page,
                "RAND()");

            foreach ($synchronousOrder as $key => $val) {
//                 if ($runTime - $theTime > 1700) {
//                     Common_Common::myEcho('执行时间超出限制,强制中断');
//                     return;
//                 }
                Common_Common::myEcho(print_r($val,true));
                try {
                	
                    //任务&订单状态判断
                    $vResult = $this->vailOrderProcessingTransaction($val["ops_id"], $val["shipper_hawbcode"], 1);

					//获取物流产品20160428,之前是取的产品对应的渠道
					$scdOrder = Service_CsdOrder::getByField($val["order_id"], 'order_id');
					$channel['server_product_code'] = $scdOrder['product_code'];

                    //设置参数 API代码、订单号
                    $obj->setParam($channel['as_code'], $val['shipper_hawbcode'], $channel['server_channelid'], $channel['server_product_code'],$order_config,$init);
                 
                    // 当为已同步时间，先判断是否存在删除预报的接口，如果有，先做删除原预报，再重新预报
                    if(true == method_exists($obj, 'deleteForecast') && $val['ops_syncing_status'] == 1) {
                    	$obj->deleteForecast();
                    }
                  
                    $result = $obj->createAndPreAlertOrderServiceByCode();
					
                    // 更新同步结果
                    $ops_status = 1;
                    $ops_note = "";
					//if($result['ack'] == '0') {
					if($result['ack'] == '0') {
						// 如果同步订单失败，更新订单状态改为"D"草稿
						$update_order = array("order_status" => "D");
						Service_CsdOrder::update($update_order, $val["order_id"]);

						$ops_status = "20";
						$ops_note = $result['error'];
					}                    

                    /*
                     * 4、处理同步结果
                    */
                    $order_process = array('ops_status' => $ops_status, 'ops_syncing_status' => 1, 'ops_note' => $ops_note);
                    Service_OrderProcessing::update($order_process, $val['ops_id']);
                    
                    Common_Common::myEcho($val['shipper_hawbcode'] . '处理完成...');
                } catch (Exception $e) {
                	Common_Common::myEcho($val['shipper_hawbcode'] . '处理异常...'.$e->getMessage());
                    Ec::showError("同步未知异常，订单号：" . $val["shipper_hawbcode"] . "异常信息：" . $e->getMessage(), 'express_create_orders_excute');
                }
                $runTime=time();
            }
        }
        
        Common_Common::myEcho('所有订单同步操作完成.....');
    }

    /**
     * 强制放行
     */
    public function compulsoryRelease($formalCode, $loop = 0) {
    	
    	Common_Common::myEcho('开始放行订单！');
    	Common_Common::myEcho('放行中。。。。。。');
    	$date = date('Y-m-d H:i:s');
    	
    	/*
    	 * 1、获取所有需要同步到服务商的订单
    	*/
    	$pageSize = 20;
    	$page = 1;
    	$condition = array(
    		"release_status" => 1,
        	"formal_code" => $formalCode,	
    	);
    	
    	//指定渠道
        if (empty($formalCode)) {
        	Common_Common::myEcho('未指定运输方式直接返回');
        	return;
        }
    	
    	$count = Service_OrderProcessing::getByCondition($condition, "count(*)");
    	$totalPage = ceil($count / $pageSize);
    	
    	//指定页数
    	$totalPage = $loop == '0' ? $totalPage : ($loop > $totalPage ? $totalPage : $loop);
    	//减少执行时间
    	if ($count == 0) {
    		Ec::showError("此次请求，无需要放行订单！" . $date, 'express_release_orders_excute');
    		Common_Common::myEcho('本次没有需要放行的订单.....over....');
    		return;
    	}
    	
    	// 放行服务
    	$objCommon = new API_Common_ServiceCommonClass();
    	$channel = $objCommon->getServiceChannelByFormalCode($formalCode);
    	if (empty($channel)) {
    		throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
    	}
    	
    	$class = $objCommon->getForApiServiceClass($channel['as_code']);
    	if (empty($class)) {
    		throw new Exception("无法获取到[{$formalCode}]对应的数据映射类");
    	}
    	if (class_exists($class)) {
    		$obj = new $class();
    	} else {
    		throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
    	}
    	
    	//调整超时
    	$theTime = $runTime = time();
    	//按页同步订单
    	for ($i = 1; $i <= $totalPage; $i++) {
    		$synchronousOrder = Service_OrderProcessing::getByCondition(
    				$condition,
    				array("order_processing.ops_id", "order_processing.shipper_hawbcode", "order_processing.ops_type"),
    				$pageSize,
    				$page,
    				"RAND()");
    	
    		foreach ($synchronousOrder as $key => $val) {
    			 
    			try {  				
    				 
    				$obj->setParam($channel['as_code'], $val['shipper_hawbcode'], $channel['server_channelid'], $channel['server_product_code'], $document_rule,$order_config);
    				// 调用放行接口
    				$return = $obj->americaLinePassAddress(array($val['shipper_hawbcode']));
    				
    				// 更新同步结果
    				$release_status = 2;
    				// 默认为 地址校验失败
    				$ops_status = "";
    				$ops_note = "";
    				
    				// 当存在返回
    				if($return['ack'] == "0" || $return['data']['Item']['Result'] != '1001') {
    					// 放行失败
    					$ops_status = "23";
    					$ops_note = "";
    					if(!empty($return['data']['ResultDesc'])) {
    						$ops_note .= $return['data']['ResultDesc'];
    					}
    					
    					// 
    					if(!empty($return['data']['Item']['ErrorOrders'][0]['ErrorMessage'])) {
    					 	$ops_note .= $return['data']['Item']['ErrorOrders'][0]['ErrorMessage'];
    					}
    				} 
    				
    				/*
    				 * 4、处理同步结果
    				*/
    				$order_process = array('release_status' => $release_status);
    				if(!empty($ops_status)) {
    					// 放行失败更新放行失败状态
    					$order_process['ops_status'] = $ops_status;
    					$order_process['ops_note'] = $ops_note;
    				}
    				
    				Service_OrderProcessing::update($order_process, $val['ops_id']);
    	
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理完成...');
    			} catch (Exception $e) {
    				Common_Common::myEcho($val['shipper_hawbcode'] . '处理异常...');
    				Ec::showError("同步未知异常，订单号：" . $val["shipper_hawbcode"] . "异常信息：" . $e->getMessage(), 'express_release_orders_excute');
    			}
    			$runTime=time();
    		}
    	}
    	Common_Common::myEcho('所有订单同步操作完成.....');
    }
    
    //物流通知
    public function notifyOrderToService($formalCode,$order_config, $init=true,$loop = 0)
    {
        Common_Common::myEcho('开始订单通知！');
        Common_Common::myEcho('通知中。。。。。。');
        $date = date('Y-m-d H:i:s');
    
        /*
         * 1、获取所有需要通知到服务商的订单
        */
        $pageSize = 20;
        $page = 1;
        //order_status in ('P','V','C') and a.notify_ems_rs=0 and a.server_hawbcode is not null
        $sql_count = "select count(*) as count from csd_order where ";
        $sql       = "select order_id,shipper_hawbcode from csd_order where  ";
        $where = "order_status in ('P','V','C') and notify_ems_rs=0 and server_hawbcode is not null";
    
        //指定渠道
        if (empty($formalCode)) {
            Common_Common::myEcho('未指定运输方式直接返回');
            return;
        }
        //Service_CsdOrder::getByCondition($condition, "count(*)");
        $count = Common_Common::fetchRow($sql_count.$where);
        $count = $count?$count["count"]:0;
        $totalPage = ceil($count / $pageSize);
        //指定页数
        $totalPage = $loop == '0' ? $totalPage : ($loop > $totalPage ? $totalPage : $loop);
        //减少执行时间
        if ($count == 0) {
            Ec::showError("此次请求，无需要通知订单！" . $date, 'express_notify_orders_excute');
            Common_Common::myEcho('本次没有需要通知的订单.....over....');
            return;
        }

        /*
         * 2、获取订单对应的物流服务商
         */
    
        $objCommon = new API_Common_ServiceCommonClass();
        $channel = $objCommon->getServiceChannelByFormalCode($formalCode);
        if (empty($channel)) {
            throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
        }
    
        //所有渠道都走API_YunExpress_ForApiService
        $channel['as_code'] = "YUNEXPRESS";
        $class = $objCommon->getForApiServiceClass($channel['as_code']);
        if (empty($class)) {
            throw new Exception("无法获取到[{$formalCode}]对应的数据映射类");
        }
        if (class_exists($class)) {
            $obj = new $class();
        } else {
            throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
        }
        //调整超时
        $theTime = $runTime = time();
        //按页通知订单
        for ($i = 1; $i <= $totalPage; $i++) {
            $query_sql = $sql.$where." order by rand() limit ".($i-1)*$totalPage.','.$pageSize;
            $synchronousOrder =  Common_Common::fetchAll($query_sql);
    
            foreach ($synchronousOrder as $key => $val) {
                Common_Common::myEcho(print_r($val,true));
                try {
                    //设置参数 API代码、订单号
                    $obj->setParam($channel['as_code'], $val['shipper_hawbcode'], $channel['server_channelid'], $channel['server_product_code'],$order_config,$init);
                    $result = $obj->notifyOrderToService();
                    	
                    // 更新通知结果
                    $ops_status = 1;
                    $ops_note = "";
                    //if($result['ack'] == '0') {
                    if($result['ack'] == '0') {
                        // 如果通知订单失败，更新订单状态改为"D"草稿
                       /*  $update_order = array("notify_ems_rs" => 1);
                        Service_CsdOrder::update($update_order, $val["order_id"]);
    
                        $ops_status = "20";
                        $ops_note = $result['error']; */
                    }
    
                    /*
                     * 4、处理通知结果
                     */
                    $update_order = array("notify_ems_rs" => 1);
                    Service_CsdOrder::update($update_order, $val["order_id"]);
    
                    Common_Common::myEcho($val['shipper_hawbcode'] . '处理完成...');
                } catch (Exception $e) {
                    Common_Common::myEcho($val['shipper_hawbcode'] . '处理异常...'.$e->getMessage());
                    Ec::showError("通知未知异常，订单号：" . $val["shipper_hawbcode"] . "异常信息：" . $e->getMessage(), 'express_create_orders_excute');
                }
                $runTime=time();
            }
        }
    
        Common_Common::myEcho('所有订单通知操作完成.....');
    }
    
    
}