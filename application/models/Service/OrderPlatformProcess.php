<?php
class Service_OrderPlatformProcess { 
   
    /**
     * 平台订单状态
     * @return unknown
     */
    public static function getOrderPlatformStatus(){
    	$statusArr = array(
    			'1' => array(
    					'name' => Ec::Lang('待处理'),
    					'actions' => array(
    							'<input type="button" status="2" class="batch_generate_orders baseBtn " value="' . Ec::Lang('生成标准订单') . '">', // 发货审核
//     							'<input type="button" status="5" class=" baseBtn" value="'.Ec::Lang('转疑问件').'">',
    							'<input type="button" status="0" class="batch_discard baseBtn" value="' . Ec::Lang('废弃') . '">'
    					)
    			)
    			,
    			'2' => array(
    					'name' => Ec::Lang('已处理'),
    					'actions' => array(
    					)
    			),
    
    			'3' => array(
    					'name' => Ec::Lang('待发货'),
    					'actions' => array(
    							'<input type="button" status="4" class="batch_processed baseBtn" value="'.Ec::Lang('标记发货').'">'
    					)
    			),
    			'4' => array(
    					'name' => Ec::Lang('已发货'),
    					'actions' => array(
    					)
    			),
    			'5' => array(
    					'name' => Ec::Lang ('问题件'),
    					'actions' => array (
    					),
					'insufficient' => array (
							'1' => Ec::Lang ( 'fund_insufficient' ),
							'2' => Ec::Lang ( 'stock_insufficient' ),
					),
    				'abnormal_type' => array(
	                    '1' => Ec::Lang('abnormal_change_address'),
	                    '2' => Ec::Lang('abnormal_cancel_order'),
	                    '3' => Ec::Lang('abnormal_change_sku'),
	                    '4' => Ec::Lang('abnormal_other'),
	                )
    			),
    			'0' => array(
    					'name' => Ec::Lang('废弃'),
    					'actions' => array(
    							'<input type="button" status="1" class="batch_discard baseBtn" value="'.Ec::Lang('转待处理').'">',
    					)
    			)
    	);
    
    	return $statusArr;
    }
    
    /**
     * 平台订单表的被动更新
     * @param unknown_type $refId
     * @param unknown_type $row
     */
    public static function updateOrdersPlatform($refId,$row){
//     	print_r($row);
    	if($row['create_type'] == 'api' && $row['create_method'] == '2'){
    		unset($row['order_id']);
    		$order_status =  $row['order_status'];
    		 
    		//标准订单，映射平台订单状态
    		$orderPlatformStatusMap = array(
    				'2'=>'2',//待发货审核
    				'3'=>'3',//待发货
    				'4'=>'4',//已发货
    				'5'=>'5',//冻结中
    				'6'=>'5',//异常
    				'7'=>'5',//问题件
    				'0'=>'0',//已废弃
    		);
    		
    		$order_platform_status = $orderPlatformStatusMap[$order_status];
    		$row['order_status'] = $order_platform_status;
    		Service_OrdersPlatform::update($row, $refId,'refrence_no_platform');
    	}
    }
    
    /**
     * 批量生成标准订单
     * @param unknown_type $orderIds
     * @param unknown_type $status
     */
    public function generateStandardOrdersMultiTransaction($orderIds){
    	$results = array ();
    	$db = Common_Common::getAdapter ();
    	foreach ( $orderIds as $orderId ) {
    		$db->beginTransaction ();
    		try {
    			$result = $this->geterateStandardOrder($orderId);
    			$db->commit ();
    		} catch ( Exception $e ) {
    			$db->rollback ();
    			$result = array (
    					"ask" => 0,
    					"message" => $e->getMessage (),
    					'errorCode' => $e->getCode ()
    			);
    		}
    		$results [] = $result;
    	}
    	return $results;
    }
    
    /**
     * 平台订单生成标准订单
     * @param unknown_type $orderId
     * @param unknown_type $status
     * @throws exception
     */
    public function geterateStandardOrder($orderId,$field='order_id'){
    	$order_platform = Service_OrdersPlatform::getByField ( $orderId, $field );
    	if (empty ( $order_platform )) {
    		throw new exception ( "Data Not Exists-->".$field.": ".$orderId );
    	}
    	//只有待处理的订单可以生成
    	if($order_platform['order_status'] != '1') {
    		throw new exception ( "Order status is incorrect --> OrderCode: ".$order_platform['refrence_no_platform'] );
    	}
    	
    	//为0时才能进行标准订单生成
    	if($order_platform['is_generate'] != '0'){
    		throw new exception ( "Has been generated over the standard order --> OrderCode: ".$order_platform['refrence_no_platform'] );
    	}
    	
    	//检查单号重复
    	$order = Service_Orders::getByCondition(array('refrence_no_platform'=>$order_platform['refrence_no_platform']));
    	if(!empty($order)){
    		throw new exception ( "Already exists in the same number of single standard order --> OrderCode: ".$order_platform['refrence_no_platform'] );
    	}
    	$time = date ( "Y-m-d H:i:s" );
    	$orderPlatformRowUpdate = array('order_status'=>2,
    									'sync_status'=>'0',
						    			'sync_time'=>'',
						    			'date_last_modify'=>$time,
    									'is_generate'=>'1'
    								);
    	
    	//修改平台订单状态及生成标准
		if (!Service_OrdersPlatform::update ( $orderPlatformRowUpdate, $orderId, $field )) {
			throw new Exception ( "Internal error! Update Order Status Fail --> OrderCode: ".$order_platform['refrence_no_platform'], '50000' );
		}
		
		//准备数据插入订单表和订单明细表
		$orderProductPlatform = Service_OrderProductPlatform::getByCondition(array('order_id'=>$order_platform['order_id']));
		unset($order_platform['order_id']);
		unset($order_platform['is_generate']);
		$order_platform['order_status'] = 2;
		$orders_id = Service_Orders::add($order_platform);
		
		foreach ($orderProductPlatform as $key => $value) {
			unset($value['op_id']);
			$value['order_id'] = $orders_id;
			Service_OrderProduct::add($value);
		}
		
		$statusArr = $this->getOrderPlatformStatus();
		$remark = "平台订单生成标准订单 --> OrderCode:" . $order_platform['refrence_no_platform'];
		$logRow = array(
            'ref_id' => $order_platform['refrence_no_platform'],
	        'log_content' => $remark.' ,状态从‘' . $statusArr['1']['name'] . '’变为‘' . $statusArr[$orderPlatformRowUpdate['order_status']]['name'].'’',
            'op_id' => ''
        );
		
		$this->writeOrderLog ($logRow);
		$result = array (
				"ask" => 1,
				"message" => "Success",
				'orderId' => $orderId,
				'ref_id'=>$order_platform['refrence_no_platform']
		);
		return $result;
    }

	
	/**
	 * 批量更新订单状态
	 * @param unknown_type $orderIds
	 * @param unknown_type $status
	 * @return multitype:multitype:number NULL
	 */
	public function updateOrderStatusMultiTransaction( $orderIds,$status) {
		$results = array ();
		$db = Common_Common::getAdapter ();
		foreach ( $orderIds as $orderId ) {
			$db->beginTransaction ();
			try {				
				$result = $this->updateOrderStatus( $orderId, $status );
				$db->commit ();
			} catch ( Exception $e ) {
				$db->rollback ();
				$result = array (
						"ask" => 0,
						"message" => $e->getMessage (),
						'errorCode' => $e->getCode () 
				);
			}
			$results [] = $result;
		}
		return $results;
	}

	/**
	 * 更新订单状态
	 * @param unknown_type $orderId
	 * @param unknown_type $status
	 * @return Ambigous <multitype:number NULL , multitype:number string unknown >
	 */
	public function updateOrderStatusTransaction( $orderId,$status) {
		$result = array (
				"ask" => 0,
				"message" => "Order Update Status Fail" 
		);
		
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$result = $this->updateOrderStatus ( $orderId, $status );
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
	 * 更新订单状态
	 * @param unknown_type $orderId
	 * @param unknown_type $status
	 * @param unknown_type $field
	 * @param unknown_type $remark
	 * @throws exception
	 * @throws Exception
	 * @return multitype:number string unknown
	 */
	public function updateOrderStatus( $orderId,$status,$field='order_id',$remark='') {
		$order = Service_OrdersPlatform::getByField ( $orderId, $field );
		if (empty ( $order )) {
			throw new exception ( "Data Not Exists-->".$field.": ".$orderId );
		}
		// 草稿状态能更新，其他状态不可更新
		if ($order ['order_status'] == $status) {
			throw new exception ( "OrderStatus  Not Change-->Code: ".$order['refrence_no_platform'] );
		}
		$time = date ( "Y-m-d H:i:s" );	
		$orderRowUpdate = array('order_status'=>$status,'sync_status'=>'0','sync_time'=>'','date_last_modify'=>$time);
		
	    switch($order['order_status']){
            case 0:
                break;
            case 1:
                break;
            case 2:
            	//"已处理"不能修改状态
            	throw new Exception(Ec::Lang('order_operation_deny'));
                break;
            case 3:
                //"待发货"不能修改状态
                throw new Exception(Ec::Lang('order_operation_deny'));
                break;
            case 4:
            	//"已发货"不能修改状态
                throw new Exception(Ec::Lang('order_operation_deny'));
                break;
            case 5:
            	//"疑问件"不能修改状态
            	throw new Exception(Ec::Lang('order_operation_deny'));
                break;
            default:
        }
		// print_r($orderRow);exit;
		if (!Service_OrdersPlatform::update ( $orderRowUpdate, $orderId, $field )) {
			throw new Exception ( "Internal error! Update Order Status Fail-->Code: ".$order['refrence_no_platform'], '50000' );
		}
		
		$statusArr = $this->getOrderPlatformStatus();
		$remark = "修改订单状态";
		$logRow = array(
            'ref_id' => $order['refrence_no_platform'],
	        'log_content' => $remark.' 更新订单状态,状态从‘' . $statusArr[$order['order_status']]['name'] . '’变为‘' . $statusArr[$orderRowUpdate['order_status']]['name'].'’',
            'op_id' => ''
        );
		// print_r($logRow);exit;
		$this->writeOrderLog ( $logRow );
		$result = array (
				"ask" => 1,
				"message" => "Success",
				'orderId' => $orderId,
		        'ref_id'=>$order['refrence_no_platform']
		);
		return $result;
	}

	
	/* 日志 */
	public static function writeOrderLog($logRow) {
	    if(!empty($logRow)){
	        $logRow['create_time'] = date('Y-m-d H:i:s');
	        Service_OrderLog::add($logRow);	         
	    }
	}
	
	/**
	 * 批量标记发货
	 * @param unknown_type $orderIds
	 * @throws Exception
	 * @return multitype:number string multitype:multitype:string unknown  multitype:unknown   Ambigous <multitype:, multitype:unknown >
	 */
	public function batchShipOrder($orderIds ,$sync_status = '') {
		$results = array(
				'ask'=>0,
				'message'=>Ec::Lang('the_results_as_follows').'：'//处理结果如下：
		);
		$success = $fail = array();
		try {
			$orderArr = array();
			foreach($orderIds as $id){
				$orderPlatform = Service_OrdersPlatform::getByField($id,'order_id');
				$orderPlatformArr[] = $orderPlatform;
			}
			
			foreach ($orderPlatformArr as $key => $value) {
				$updateRow = array(
						'order_status' => 4,
						'date_last_modify' => date('Y-m-d H:i:s')
				);
				
				if(!empty($sync_status)){
					$updateRow['sync_status'] = $sync_status;
				}
				
				if(!Service_OrdersPlatform::update($updateRow, $value['order_id'], 'order_id')){
					$fail[] = array(
							'ref_id' => $value['refrence_no_platform'],
							'message' => Ec::Lang('operationFail')//'操作失败'
					);
					$logRow = array('ref_id'=>$value['refrence_no_platform'],'log_content' => '订单标记发货失败');
				}else{
					$success[] = array(
							'ref_id' => $value['refrence_no_platform'],
							'message' => 'Success'
					);
					$logRow = array('ref_id'=>$value['refrence_no_platform'],'log_content' => '订单标记发货成功');
				}
				$logRow['op_id'] = Service_User::getUserId();
				$this->writeOrderLog ($logRow);
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

}