<?php
class Process_OrderProcess {
	
    public static function getStatusArr($platform = ''){
    	
    	$statusArr = array(
    			'2' => array(
    					'name' => Ec::Lang('order_status_2','auto'),//待发货审核
    					'actions' => array(
    							'<input type="button" class="orderVerify baseBtn " value="'. Ec::Lang('order_verify','auto').'">',//发货审核
    							'<input type="button" status="5" class="updateStatus baseBtn" value="'.Ec::Lang('order_freeze','auto').'">',//冻结
    							'<input type="button" class="orderMerge baseBtn" value="'.Ec::Lang('order_merge','auto').'">',//订单合并
    	
    							//'<input type="button" class="orderMergeReverse baseBtn" value="还原已合并订单">',
    							'<input type="button" class="orderSplit baseBtn" value="'.Ec::Lang('order_split','auto').'">',//订单拆分
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="operatorNoteBtn baseBtn" value="'.Ec::Lang('service_notes','auto').'">',//客服备注
    	
    							//'<input type="button" class="orderSetWarehouseShipBtn baseBtn" value="'.Ec::Lang('hand_of_positions','auto').'" style="display:none;">',//手工分仓
    							'<input type="button" class="orderSetWarehouseShipAutoBtn baseBtn" value="'.Ec::Lang('automatic_warehouse','auto').'">',//自动分仓
    							'<input type="button" class="batchOrderMerge baseBtn" value="'.Ec::Lang('automatic_merged_orders','auto').'">',//自动合并订单
    							//'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'" style="display:none;">',//标记发货到eBay
    							'<input type="button" class="zidingyibiaoji baseBtn" value="'.Ec::Lang('custom_tags','auto').'" style="" order_status="2">',//自定义标记
    					)
    			),
    			'3' => array(
    					'name' => Ec::Lang('order_status_3','auto'),//待发货
    					'actions' => array(
    							'<input type="button" class="exportBtn baseBtn" value="'.Ec::Lang('exporting_to_the_warehouse','auto').'">',//导出给仓库
    							'<input type="button" status="7" class="cancelOrder baseBtn" value="'.Ec::Lang('cancel_order','auto').'">',//截单
    							'<input type="button" class="biaojifahuo baseBtn" value="'.Ec::Lang('batch_import_shipments_mark','auto').'">',//批量导入标记发货
    							'<input type="button" class="biaojifahuoSelect baseBtn" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="ordersMark baseBtn " value="'.Ec::Lang('marked_handled','auto').'">',//标记为已处理
    							'<input type="button" class="unOrdersMark baseBtn base_hide" value="'.Ec::Lang('cancel_order_mark','auto').'">',//取消订单标记
    							'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'" style="display:none;">',//标记发货到eBay
    							'<input type="button" class="operatorNoteBtn baseBtn" value="'.Ec::Lang('service_notes','auto').'">',//客服备注
    							'<input type="button" class="zidingyibiaoji baseBtn" value="'.Ec::Lang('custom_tags','auto').'" style="" order_status="3">',//自定义标记
    							'<input type="button" class="platform_shipped baseBtn" value="'.Ec::Lang('platform_shipped','auto').'" style="" order_status="3">',//自定义标记
    					),
    					'process_again' =>array(
    							'1'=>Ec::Lang('process_again_1','auto'),//未处理
    							'2'=>Ec::Lang('process_again_2','auto'),//已处理
    					)
    			),
    			'4' => array(
    					'name' => Ec::Lang('order_status_4','auto'),//已发货
    					'actions' => array(
    							'<input type="button" status="7" class="cancelOrder baseBtn" value="'.Ec::Lang('cancel_order','auto').'">',//截单
    							'<input type="button" class="biaojifahuo baseBtn" value="'.Ec::Lang('batch_update_TRACKINGNO','auto').'">',//批量更新TRACKINGNO
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="operatorNoteBtn baseBtn" value="'.Ec::Lang('service_notes','auto').'">',//客服备注
    							'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'" style="display:none;">',//标记发货到eBay
    							'<input type="button" class="zidingyibiaoji baseBtn" value="'.Ec::Lang('custom_tags','auto').'" style="" order_status="4">',//自定义标记
    					)
    			),
    			'5' => array(
    					'name' => Ec::Lang('order_status_5','auto'),//冻结中
    					'actions' => array(
    							'<input type="button" status="2" class="updateStatus baseBtn" value="'.Ec::Lang('order_to_verify','auto').'">',//转待发货审核
    							'<input type="button" status="0" class="deleteBtn baseBtn" value="'.Ec::Lang('order_invalid','auto').'">',//作废
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="biaojifahuoSelect biaojifahuoSelectForEbay baseBtn" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
    							'<input type="button" class="operatorNoteBtn baseBtn" value="'.Ec::Lang('service_notes','auto').'">',//客服备注
    							//'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'" style="display:none;">',//标记发货到eBay
    							'<input type="button" class="zidingyibiaoji baseBtn" value="'.Ec::Lang('custom_tags','auto').'" style="" order_status="5">',//自定义标记
    							
    							'<input type="button" class="orderVerify baseBtn " value="'. Ec::Lang('order_verify','auto').'">',//发货审核
    							'<input type="button" class="orderSetWarehouseShipAutoBtn baseBtn" value="'.Ec::Lang('automatic_warehouse','auto').'">',//自动分仓
    					)
    			),
    			'6' => array(
    					'name' => Ec::Lang('stock_insufficient','auto'),//缺货中
    					'actions' => array(
    							'<input type="button" status="7" class="cancelOrder baseBtn" value="'.Ec::Lang('cancel_order','auto').'">',//截单
    							'<input type="button" status="0" class="splitOneClickBtn baseBtn" value="'.Ec::Lang('split_one_click','auto').'">',//一键拆单
    							'<input type="button" status="0" class="splitOneClickBatchBtn baseBtn" value="'.Ec::Lang('split_one_click_batch','auto').'">',//一键拆单
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="biaojifahuo baseBtn" value="'.Ec::Lang('batch_import_shipments_mark','auto').'">',//批量导入标记发货
    							//'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'">',//标记发货到eBay
    							'<input type="button" class="biaojifahuoSelect  baseBtn" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
    							'<input type="button" class="operatorNoteBtn baseBtn" value="'.Ec::Lang('service_notes','auto').'">',//客服备注
    							'<input type="button" class="zidingyibiaoji baseBtn" value="'.Ec::Lang('custom_tags','auto').'" style="" order_status="6">',//自定义标记
    	
    					)
    			),
    			'7' => array(
    					'name' => Ec::Lang('order_status_7','auto'),//问题件
    					'actions' => array(
    							'<input type="button" status="2" class="updateStatus baseBtn" value="'.Ec::Lang('order_to_verify','auto').'">',//转待发货审核
    							'<input type="button" class="orderVerify baseBtn " value="'.Ec::Lang('order_verify','auto').'">',//发货审核
    							'<input type="button" status="0" class="deleteBtn baseBtn" value="'.Ec::Lang('order_invalid','auto').'">',//作废
    							//                             '<input type="button" status="7" class="cancelOrder baseBtn" value="截referenceNo exist订单" title="如果订单审核报referenceNo xxxx exists;请使用该按钮...">',
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="biaojifahuoSelect biaojifahuoSelectForEbay baseBtn" value="'.Ec::Lang('select_orders_shipped_mark','auto').'">',//选择订单标记发货
    							'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'" style="display:none;">',//标记发货到eBay
    							'<input type="button" class="zidingyibiaoji baseBtn" value="'.Ec::Lang('custom_tags','auto').'" style="" order_status="7">',//自定义标记
    							'<input type="button" class="operatorNoteBtn baseBtn" value="'.Ec::Lang('service_notes','auto').'">',//备注
    							
    							'<input type="button" status="5" class="updateStatus baseBtn" value="'.Ec::Lang('order_freeze','auto').'">',//冻结
    					),
    					'abnormal_type' =>array(
    							'1'=>Ec::Lang('abnormal_change_address','auto'),//换地址
    							'2'=>Ec::Lang('abnormal_cancel_order','auto'),//取消订单
    							'3'=>Ec::Lang('abnormal_change_sku','auto'),//换SKU
    							'6'=>Ec::Lang('abnormal_duanhuo','auto'),//同步服务商失败
    							'4'=>Ec::Lang('abnormal_other','auto'),//其他
    							'5'=>Ec::Lang('abnormal_sync_service_fail','auto'),//同步服务商失败
    					)
    			),
    			'0' => array(
    					'name' => Ec::Lang('order_status_0','auto'),//已作废
    					'actions' => array(
    							'<input type="button" class="updateStatus baseBtn" status="2" value="'.Ec::Lang('order_to_verify','auto').'">',//转代发货审核
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="operatorNoteBtn baseBtn" value="'.Ec::Lang('service_notes','auto').'">',//客服备注
    							'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'" style="display:none;">',//标记发货到eBay
    					)
    			),
    			'1' => array(
    					'name' => Ec::Lang('order_status_1','auto'),//未付款
    					'actions' => array(
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    							'<input type="button" class="completeSale baseBtn" value="'.Ec::Lang('mark_shipped','auto').'" style="display:none;">',//标记发货到eBay
    							'<input type="button" class="unPaidToPaidBtn baseBtn" status="2" value="'.Ec::Lang('order_to_verify','auto').'" >',//转带发货审核
    							'<input type="button" class="deleteBtn baseBtn" status="0" value="'.Ec::Lang('order_invalid','auto').'" >'//作废
    					)
    			),
    			'empty'=> array(
    					'actions' => array(
    							'<input type="button" class="messageBtn baseBtn" value="'.$messageBtn.'">',//站内信通知
    					)
    			)
    	);
    	return $statusArr;
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
	public function createOrder($row) {
		$time = date ( "Y-m-d H:i:s" );
		//验证输入的数据是否正确
		$this->_orderValidate($row);		
		
		$refrence_no_platform = Common_GetNumbers::getCode('CURRENT_ORDER_COUNT','WEC');//订单号

		$refrence_no_sys = Common_GetNumbers::getCode('CURRENT_ORDER_SYS_COUNT','SYS');//系统单号
		
		// print_r($orderRow);exit;

		$addressRow = $row['address'];
		$addressRow['create_date_sys'] = $time;
		$addressRow['OrderID'] = $refrence_no_platform;
		if (! $ShippingAddress_Id=Service_ShippingAddress::add ( $addressRow )) {
		    throw new Exception ( "Internal error! Create Order Fail ...", '50000' );
		}

		$orderRow = $row['order'];
		$orderRow['refrence_no_sys'] = $refrence_no_sys;
		
		$orderRow['refrence_no_platform'] = $refrence_no_platform;
		$orderRow['date_create'] = $time;
		$orderRow['shipping_address_id'] = $ShippingAddress_Id;
// 		print_r($orderRow);exit;
		
		if (! $orderId = Service_Orders::add ( $orderRow )) {
			throw new Exception ( "Internal error! Create Order Fail..", '50000' );
		}
		
		$orderProduct = $row['order_product'];
// 		print_r($orderProduct);exit;
        $subTotal = 0;
        $qtuSum = 0;
		foreach ( $orderProduct as $v ) {
			$productId = $v ['product_id'];
			$productSku = $v ['product_sku'];
			$productTitle = empty($v ['product_title'])?'':$v ['product_title'];
			$qty = $v ['op_quantity'];
			if(empty($productSku)){
				//'产品SKU不可为空'
			    throw new Exception(Ec::Lang('product_sku_can_not_be_empty'));
			}
			if(empty($qty)||!preg_match('/^[0-9]+$/', $qty)){
				//数量必须为数字且大于0
			    throw new Exception('SKU:'.$productSku.Ec::Lang('sku_quantity_must_int_and_gt_0'));
			}
			$qtuSum+=$qty;
			$now = date("Y-m-d H:i:s");
			$orderProductRow = array (
					'order_id' => $orderId,
					'product_id' => 0,
					'product_title' => isset($v['product_title'])?$v['product_title']:'',
					'product_sku' => $productSku,
					'op_quantity' => $qty,
					'op_ref_tnx' => isset($v['op_ref_tnx'])?$v['op_ref_tnx']:'',
					'op_ref_item_id' => isset($v['op_ref_item_id'])?$v['op_ref_item_id']:'',
					'op_ref_buyer_id' => isset($v['op_ref_buyer_id'])?$v['op_ref_buyer_id']:'',
					'op_site' => isset($v['op_site'])?$v['op_site']:'',
					'op_record_id' => isset($v['op_record_id'])?$v['op_record_id']:'',
					'pic' => isset($v['pic'])?$v['pic']:'',
					'url' => isset($v['url'])?$v['url']:'',
			        
					'unit_price' => isset($v['unit_price'])?$v['unit_price']:'0',
					'unit_finalvaluefee' => isset($v['unit_finalvaluefee'])?$v['unit_finalvaluefee']:'0',			        
					'currency_code' => isset($v['currency_code'])?$v['currency_code']:'',
			        
			        'OrderID'=>$refrence_no_platform,
			        'OrderIDEbay'=>isset($v['OrderIDEbay'])?$v['OrderIDEbay']:'',
			        
			        'create_type'=>isset($v['create_type'])?$v['create_type']:'',
			        'give_up'=>isset($v['give_up'])?$v['give_up']:'0',
			        
					'op_ref_paydate' => $now,
					'op_add_time' => $now,
					'op_update_time' => $now,
			);
// 			print_r($orderProductRow);exit;
			$subTotal+=$orderProductRow['unit_price']*$qty;
			
			if (! Service_OrderProduct::add ( $orderProductRow )) {
				throw new Exception ( "Internal error! Create Order Fail ....".print_r($orderProductRow,true), '50000' );
			}
		}
		$updateRow = array(
		        'product_count' => $qtuSum,
		        'subtotal' => $orderRow['subtotal']?$orderRow['subtotal']:$subTotal,
		        'amountpaid' => $orderRow['amountpaid']?$orderRow['amountpaid']:($subTotal + $orderRow['ship_fee'])
		);

		$updateRow['amountpaid'] = $updateRow['subtotal']+$orderRow['ship_fee'];
		
		if($qtuSum==1){
            $updateRow['is_one_piece'] = '1';
        }else{
            $updateRow['is_one_piece'] = '0';
        }
        $updateRow['process_again'] = '1';
		Service_Orders::update($updateRow, $refrence_no_platform,'refrence_no_platform');
		
		Service_OrderProductProcess::updateOrderProductUnitPriceFinalValueFee($refrence_no_platform);//均价
// 		$order = Service_Orders::getByField ( $orderId, 'order_id' );
		$logRow = array(
            'ref_id' => $refrence_no_platform,
            'log_content' => '创建订单' ,
            'op_id' => ''
        );
		$this->writeOrderLog ( $logRow );
		$result = array (
				"ask" => 1,
				"message" => "Create Order Success!",
				'ref_id' => $refrence_no_platform,
		        'order_id'=>$orderId
		);
		return $result;
	}
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
	 * 更新订单
	 * @param unknown_type $row
	 * @param unknown_type $orderId
	 * @throws exception
	 * @throws Exception
	 * @return multitype:number string unknown
	 */
	public function updateOrder($row, $orderId) {
		$order = Service_Orders::getByField ( $orderId, 'order_id' );
		if (empty ( $order )) {
			throw new exception ( "Orders Not Exists-->{$orderId}" );
		}

		// 草稿状态能更新，其他状态不可更新
		if ($order ['order_status'] != 1) {
			throw new exception ( "Order Can't Edit,OrderStatus Is Not Draft-->{$orderId}" );
		}

		//验证输入的数据是否正确
		$this->_orderValidate($row);
		
		$time = date ( "Y-m-d H:i:s" );
		
		$orderRow = $row['order'];		
		// print_r($orderRow);exit;
		if (! Service_Orders::update ( $orderRow, $orderId, 'order_id' )) {
			throw new Exception ( "Internal error! Update Order Fail", '50000' );
		}
		
		$addressRow = $row['address'];
		if (! Service_ShippingAddress::update ( $addressRow, $orderId, 'OrderId' )) {
			throw new Exception ( "Internal error!========== Update Order Fail ...", '50000' );
		}
		
		Service_OrderProduct::delete ( $orderId, 'order_id' );
		$order_product = $row ['order_product'];
		foreach ($order_product  as $v ) {
			$productId = $v ['product_id'];
			$qty = $v ['op_quantity'];
			$product = Service_Product::getByField ( $productId, 'product_id' );
			if(empty($product)){
				throw new Exception('Product Not Exists -->'.$productId);
			}
			$now = date("Y-m-d H:i:s");
			$orderProductRow = array (
					'order_id' => $orderId,
					'product_id' => $productId,
					'product_sku' => $product['product_sku'],
					'product_title' => $product['product_title'],
					'op_quantity' => $qty,
					'op_ref_tnx' => '',
					'op_ref_item_id' => '',
					'op_ref_buyer_id' => '',
					'op_ref_paydate' => $now,
					'op_add_time' => $now,
					'op_update_time' => $now,
			);
			if (! Service_OrderProduct::add ( $orderProductRow )) {
				throw new Exception ( "Internal error! Update Order Fail ....", '50000' );
			}
		}
		
		$remark = "Order Contents Update";
		$logRow = array(
            'ref_id' => $orderRow['refrence_no_platform'],
            'log_content' => '更新订单' ,
            'op_id' => ''
        );
		$this->writeOrderLog ( $logRow );
		$result = array (
				"ask" => 1,
				"message" => "Update Order Success!",
				'orderId' => $orderId 
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
			$results [] = $result;
		}
		return $results;
	}

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
		$order = Service_Orders::getByField ( $orderId, $field );
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
                
                break;
            case 3:
                //如果是要截单，需要从wms库存解冻，如果涉及到费用，需要费用回退，成功之后，状态变为作废
                throw new Exception(Ec::Lang('order_status_inoperable'));//'订单状态不可操作'
                //如果是要转为发货审核状态，需要从wms库存解冻，如果涉及到费用，需要费用回退，成功之后，状态变为发货审核
                break;
            case 4:
                //第三方仓库，可操作截单
                throw new Exception(Ec::Lang('order_status_inoperable'));//'订单状态不可操作'
                
                break;
            case 5:
                
                break;
            case 6:
                throw new Exception(Ec::Lang('order_status_inoperable'));//'订单状态不可操作'
                
                break;
            default:
        }
		// print_r($orderRow);exit;
		if (! Service_Orders::update ( $orderRowUpdate, $orderId, $field )) {
			throw new Exception ( "Internal error! Update Order Status Fail-->Code: ".$order['refrence_no_platform'], '50000' );
		}
	
	
		$remark = "Order Status Update";
		$logRow = array(
            'ref_id' => $order['refrence_no_platform'],
            'log_content' => $remark.' 更新订单状态,状态从'.$order['order_status'].'变为'.$orderRowUpdate['order_status'] ,
            'op_id' => ''
        );
		// print_r($logRow);exit;
		$this->writeOrderLog ( $logRow );
		$result = array (
				"ask" => 1,
				//订单操作成功
				"message" => Ec::Lang('order_operation_success_tips') . "!-->Code: ".$order['refrence_no_platform'],
				'orderId' => $orderId
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