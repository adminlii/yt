<?php
class Service_TransferOrderProcess { 
    public $_err = array();
    /**
     * 订单状态
     * @return unknown
     */
    public static function getOrderStatus(){
        $statusArr = array(
        	'1' => array(
        			'name' => Ec::Lang('transfer_order_status_1'),
        			'actions' => array(
        					'<input type="button" class="orderVerify baseBtn " value="' . Ec::Lang('order_verify') . '">', // 发货审核
        					'<input type="button" status="1" class="cancelOrder baseBtn" value="'.Ec::Lang('order_discard').'">',
        			)
        	),       		
            '2' => array(
                'name' => Ec::Lang('transfer_order_status_2'),
                'actions' => array(
                    '<input type="button" status="2" class="cancelOrder baseBtn" value="'.Ec::Lang('cancel_order').'">'
                ),
            )
            ,
            '3' => array(
                'name' => Ec::Lang('transfer_order_status_3'),
                'actions' => array(
                    //'<input type="button" status="7" class="cancelOrder baseBtn" value="'.Ec::Lang('cancel_order').'">'
                ),
            ),
            
            '4' => array(
                'name' => Ec::Lang('transfer_order_status_4'),
                'actions' => array(
                    //'<input type="button" status="7" class="cancelOrder baseBtn" value="'.Ec::Lang('cancel_order').'">'
                )
            ),
            '5' => array(
                'name' => Ec::Lang('transfer_order_status_5'),
                'actions' => array(
                    //'<input type="button" status="7" class="cancelOrder baseBtn" value="'.Ec::Lang('cancel_order').'">'
                )
            ),
            '0' => array(
                'name' => Ec::Lang('transfer_order_status_0'),
                'actions' => array(
                    //'<input type="button" class="updateStatus baseBtn" status="2" value="'.Ec::Lang('order_to_verify').'">'
                )
            )
        );
        
        return $statusArr;
    }

    /*
     * 订单内容审核
    */
    protected  function _orderValidate($orderData){
        $orderRow = $orderData['order'];
        $orderProduct = $orderData['order_product'];
        // 判断是否有产品
        if(empty($orderProduct)){
            // '产品为必填'
            $this->_err[] = Ec::Lang('pls_select_sku');
            //throw new Exception(Ec::Lang('pls_select_sku'),'30000');
        }else{
            foreach($orderProduct as $k=>$p){
                $productId = $p['product_id'];
                $product = Service_Product::getByField($productId,'product_id');
                //产品不存在
                if(empty($product)){
                    $this->_err[] = Ec::Lang('sku_not_exist');
                    //throw new Exception(Ec::Lang('sku_not_exist'),'30000');
                }
                
                // 产品不存在
                if($product['company_code'] != $orderRow['company_code']) {
                	$this->_err[] = Ec::Lang('sku_not_exist');
                }
                //产品未审核
                if($product['product_status']!='1'){
                    $this->_err[] = Ec::Lang('sku_not_verify',$product['product_sku']);
                    //throw new Exception(Ec::Lang('sku_not_verify',$product['product_sku']),'30000');
                }
                // 数量必须为数字且大于0'
                if(! preg_match('/^[0-9]+$/', $p['op_quantity']) || intval($p['op_quantity']) < 1){
                    $this->_err[] = Ec::Lang('sku_quantity_must_int_and_gt_0');
                    //throw new Exception(Ec::Lang('sku_quantity_must_int_and_gt_0'),'30000');
                }
                //传递参数
                $p['product_barcode'] = $product['product_barcode'];
                $p['product_sku'] = $product['product_sku'];
                $p['product_title'] = $product['product_title'];
                $orderProduct[$k] = $p;
            }
        }
    
        // 判断仓库
        if(empty($orderRow['warehouse_code'])){
            // '仓库必填'
            $this->_err[] = Ec::Lang('warehouse_can_not_empty');
            //throw new Exception(Ec::Lang('warehouse_can_not_empty'),'30000');
        }else{
            $warehouse = Service_Warehouse::getByField($orderRow['warehouse_code'],'warehouse_code');
            if(empty($warehouse)){
                $this->_err[] = Ec::Lang('warehouse_illagel',$orderRow['warehouse_code']);
                //throw new Exception(Ec::Lang('warehouse_illagel',$orderRow['warehouse_code']),'30000');                
            }
            $orderRow['warehouse_id'] = $warehouse['warehouse_id'];
        }
        
        // 判断目的仓库
        if(empty($orderRow['to_warehouse_code'])){
        	// '仓库必填'
        	$this->_err[] = Ec::Lang('warehouse_can_not_empty');
        	//throw new Exception(Ec::Lang('warehouse_can_not_empty'),'30000');
        }else{
        	$warehouse = Service_Warehouse::getByField($orderRow['to_warehouse_code'],'warehouse_code');
        	if(empty($warehouse)){
        		$this->_err[] = Ec::Lang('warehouse_illagel',$orderRow['to_warehouse_code']);
        		//throw new Exception(Ec::Lang('warehouse_illagel',$orderRow['warehouse_code']),'30000');
        	} else {
	        	$orderRow['to_warehouse_id'] = $warehouse['warehouse_id'];
	        	
	        	// 国家
	        	$country = Service_Country::getByField($warehouse['country_id']);
	        	if(!$country){
	        		$this->_err[] = Ec::Lang('warehouse_illagel',$orderRow['to_warehouse_code']);
	        	}
	        	// 国家代码
	        	$orderRow['consignee_country'] = $country['country_code'];
        	}
        }
//         print_r($orderRow);die;
        // 源仓库不能等于目的仓
        if($orderRow['warehouse_code'] == $orderRow['to_warehouse_code']) {
        	// '仓库必填'
        	$this->_err[] = Ec::Lang('warehouse_different');
        }

        // 运输方式
        if(empty($orderRow['shipping_method'])){
            // '仓库必填'
            $this->_err[] = Ec::Lang('shipping_method_can_not_empty');
            //throw new Exception(Ec::Lang('shipping_method_can_not_empty'),'30000');
        }
        
        // 公司代码
        if(empty($orderRow['company_code'])){
            // '仓库必填'
            $this->_err[] = Ec::Lang('company_code_can_not_empty');
            //throw new Exception(Ec::Lang('company_code_can_not_empty'),'30000');
        }
        
        $sql = "SELECT distinct a.sm_code,a.sm_name_cn,a.sm_name from shipping_method a INNER JOIN shipping_method_settings b on a.sm_id=b.sm_id INNER JOIN sm_area_map c on b.warehouse_id=c.warehouse_id where c.country_id='{$country['country_id']}' and c.warehouse_id='{$orderRow['warehouse_id']}' and a.sm_code='{$orderRow['shipping_method']}';";
        $db = Common_Common::getAdapter();
        $rs = $db->fetchAll($sql);
        if(empty($rs)){
            $this->_err[] = Ec::Lang('warehouse_country_not_support_shipping_method');
            //throw new Exception(Ec::Lang('warehouse_country_not_support_shipping_method'));
        }
        // 以上判断需要抛出异常
        
        $row = array(
                'order' => $orderRow,
                'order_product' => $orderProduct
        );
    
        return $row;
    }
    /**
     * 创建订单
     * @param unknown_type $row
     * @return Ambigous <multitype:number NULL , multitype:number string mixed >
     */
	public function createOrderTransaction($row) {
        $result = array(
            "ask" => 0,
            "message" => Ec::Lang('order_create_fail')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
			$twoCode = Common_GetNumbers::getCode ( 'TRANSFER_ORDER_COUNT', $row['order'] ['company_code'], 'TWO' ); // 订单号
			$row['order']['two_code'] = $twoCode;
			
			$result = $this->createOrder ( $row );
			$db->commit ();
			$result = array (
					"ask" => 1,
					"message" => Ec::Lang('create_order_success',$twoCode),
					'ref_id' => $twoCode
			);
		}catch(Exception $e){
            $db->rollback();
            $result = array(
                "ask" => 0,
                "message" => Ec::Lang('create_order_fail').',Reason:'.$e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
        $result['err'] = $this->_err;
        return $result;
    }
    
	/**
	 * 创建订单
	 * @param array $row
	 * @throws Exception
	 * @return multitype:number string mixed
	 */
	public function createOrder($row) {
		$time = date ( "Y-m-d H:i:s" );
		//验证输入的数据是否正确
		$row = $this->_orderValidate($row);	
		$orderRow = $row['order'];
		
		// 单号
		$orderCode = $orderRow['two_code'];
		
		if($this->_err){//有异常，抛出异常
		    throw new Exception(Ec::lang('validate_err'));
		}
		// 草稿
		$orderRow['order_status'] = '1';
		$orderRow['date_create'] = $time;
		
// 		print_r($orderRow); die;
		if (! $orderId = Service_TransferOrders::add ( $orderRow )) {
			throw new Exception ( Ec::lang('inner_db_error'), '50000' );
		}
				
		$orderProduct = $row['order_product'];
		foreach ( $orderProduct as $v ) {
			$productId = $v ['product_id'];
			$productSku = $v['product_sku'];
			$productBarcode = $v['product_barcode'];
			$productTitle = empty($v ['product_title'])?'':$v ['product_title'];
			
			$now = date("Y-m-d H:i:s");
			$orderProductRow = array (
					'to_id' => $orderId,
					'product_id' => $productId,
					'product_sku' => $productSku,
					'product_barcode' => $productBarcode,
					'product_title' => $productTitle,
					'quantity' => $v['op_quantity'],			        
			);
// 			print_r($orderProductRow);exit;
			
			if (! Service_TransferOrderProduct::add ( $orderProductRow )) {
				throw new Exception ( Ec::lang('inner_db_error'), '50000' );
			}
		}
		
		$logRow = array(
            'ref_id' => $orderCode,
            'log_content' => '创建订单',
            'op_id' => Service_User::getUserId()
        );
		$this->writeOrderLog ( $logRow );
	}
	/**
	 * 更新订单
	 * @param array $row
	 * @param string $refId
	 * @return Ambigous <multitype:number NULL , multitype:number string unknown >
	 */
	public function updateOrderTransaction($row, $refId) {
        $result = array(
            "ask" => 0,
            "message" => Ec::Lang('order_update_fail')
        );
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $result = $this->updateOrder($row, $refId);
            $db->commit();
            $result = array(
            		"ask" => 1,
            		'ref_id' => $refId,
            		"message" => Ec::Lang('update_order_success',$refId),
            );
        }catch(Exception $e){
            $db->rollback();
            $result = array(
                "ask" => 0,
            	'ref_id' => $refId,
                "message" => Ec::Lang('update_order_fail',$refId).',Reason:'.$e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
        $result['err'] = $this->_err;
        return $result;
    }
    
	/**
	 * 更新订单
	 * @param aray $row
	 * @param string $orderId
	 * @throws exception
	 * @throws Exception
	 * @return multitype:number string unknown
	 */
	public function updateOrder($row, $refId) {
        $order = Service_TransferOrders::getByField($refId, 'two_code');
        if(empty($order)){
            throw new exception(Ec::Lang('order_not_exist'),$refId);
        }
        $orderId = $order['to_id'];
        // 草稿状态能更新，其他状态不可更新
        if($order['order_status'] != 1) {
            throw new exception(Ec::Lang('order_edit_deny',$refId));
        }
//         print_r($row);
        // 验证输入的数据是否正确
        $row = $this->_orderValidate($row);
        $time = date("Y-m-d H:i:s");
        
        $orderRow = $row['order'];
        
        if($this->_err){//有异常，抛出异常
		    throw new Exception(Ec::lang('validate_err'));
        }
        
        // 更新头数据
        Service_TransferOrders::update($orderRow, $orderId);

        Service_TransferOrderProduct::delete($orderId, 'to_id');
        $orderProduct = $row['order_product'];
		foreach ( $orderProduct as $v ) {
			$productId = $v ['product_id'];
			$productSku = $v['product_sku'];
			$productBarcode = $v['product_barcode'];
			$productTitle = empty($v ['product_title'])?'':$v ['product_title'];
			
			$now = date("Y-m-d H:i:s");
			$orderProductRow = array (
					'to_id' => $orderId,
					'product_id' => $productId,
					'product_sku' => $productSku,
					'product_barcode' => $productBarcode,
					'product_title' => $productTitle,
					'quantity' => $v['op_quantity'],			        
			);
			
			if (! Service_TransferOrderProduct::add ( $orderProductRow )) {
				throw new Exception ( Ec::lang('inner_db_error'), '50000' );
			}
		}
                
        $logRow = array(
            'ref_id' => $refId,
            'log_content' => '更新订单',
            'op_id' => ''
        );
        $this->writeOrderLog($logRow);        
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
                //作废之后，不可操作
                throw new Exception(Ec::Lang('order_operation_deny'));
                break;
            case 1:
                
                break;
            case 2:
                
                break;
            case 3:
                //如果是要截单，需要从wms库存解冻，如果涉及到费用，需要费用回退，成功之后，状态变为作废
                throw new Exception(Ec::Lang('order_operation_deny'));
                //如果是要转为发货审核状态，需要从wms库存解冻，如果涉及到费用，需要费用回退，成功之后，状态变为发货审核
                break;
            case 4:
                //第三方仓库，可操作截单
                throw new Exception(Ec::Lang('order_operation_deny'));
                
                break;
            case 5:
                
                break;
            case 6:
                throw new Exception(Ec::Lang('order_operation_deny'));
                
                break;
            default:
        }
		// print_r($orderRow);exit;
		if (! Service_Orders::update ( $orderRowUpdate, $orderId, $field )) {
			throw new Exception ( "Internal error! Update Order Status Fail-->Code: ".$order['refrence_no_platform'], '50000' );
		}
	
		$statusArr = $this->getOrderStatus();
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
	        $logRow['op_id'] = Service_User::getUserId();
	        Service_TransferOrderLog::add($logRow);	         
	    }
	}	
	
    /**
     * 订单导出，基本版
     * @param unknown_type $orderIds
     */
	public function baseExportProcess($orderIds){		
		$dataList = array();
		foreach($orderIds as $id){
			$data = array ();
			$order = Service_Orders::getByField ( $id, 'order_id' );
				
// 			$data ['warehouse_code'] = $order ['warehouse_id'];
			$data [Ec::Lang('platform')] = strtoupper($order ['platform']);
			$data [Ec::Lang('refrence_no')] = $order ['refrence_no'];
			$data [Ec::Lang('order_code')] = $order ['refrence_no_platform'];
			$data [Ec::Lang('warehouse_name')] = $order ['warehouse_code'];
			$data [Ec::Lang('shipping_method')] = $order ['shipping_method'];
			$data [Ec::Lang('tracking_no')] = $order ['shipping_method_no'];
			$data [Ec::Lang('order_weight')] = $order ['order_weight'];
// 			$data [Ec::Lang('insurance_type')] = 'L';
// 			$data [Ec::Lang('transaction_id')] = $order ['transaction_id'];
			$data [Ec::Lang('consignee_name')] = $order ['consignee_name'];
			$data [Ec::Lang('consignee_company')] = '';
			$data [Ec::Lang('consignee_country')] = $order ['consignee_country_code'];
			$data [Ec::Lang('consignee_state')] = $order ['consignee_state'];
			$data [Ec::Lang('consignee_city')] = $order ['consignee_city'];
			$data [Ec::Lang('consignee_street')] = $order ['consignee_street1'].' '.$order['consignee_street2'].' '.$order['consignee_street3'];
			$data [Ec::Lang('consignee_zip')] = $order ['consignee_postal_code'];
			$data [Ec::Lang('consignee_email')] = $order ['consignee_email'];
			$data [Ec::Lang('consignee_phone')] = $order ['consignee_phone'];
			$data [Ec::Lang('order_desc')] = $order ['order_desc'];
// 			$data [Ec::Lang('operator_note')] = $order ['operator_note'];
				
			$con = array (
					'order_id' => $id,
			);
			$fileds = array('product_sku','op_quantity');
			$orderProducts = Service_OrderProduct::getByCondition ( $con, '*' );
			for ($i=0;$i<50;$i++){//最多50个sku
				if(isset($orderProducts[$i])){
					$p = $orderProducts[$i];
					$sku = $p ['product_sku'];
					$qty = $p ['op_quantity'];
				}else{					
				    $sku='';					
				    $qty='';
				}

				$data [Ec::Lang('SKU') . ($i + 1)] = $sku;
				$data [Ec::Lang('quantity') . ($i + 1)] = $qty;
			}
			
			$dataList [] = $data;
		}		
		
			
		$fileName = Service_ExcelExport::exportToFile($dataList, 'Orders');
		Common_Common::downloadFile($fileName);
	}
	
	/**
	 * 删除自定义标记
	 * @param unknown_type $otId
	 * @return multitype:number string
	 */
	public function deleteDefinedTagTransaction($otId){
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$updateRow = array (
					'ot_id' => '0' 
			);
			Service_Orders::update ( $updateRow,$otId, 'ot_id' );//状态还原
			
			Service_OrderTag::delete ( $otId, 'ot_id' );//删除只能定义标记
			
			$db->commit ();
			$return = array('ask'=>1,'message'=>'delete success');
		} catch ( Exception $e ) {
			$db->rollback ();
			$return = array('ask'=>0,'message'=>Ec::Lang('inner_error'));
		}
		return $return;
		
	}


	/**
	 * 读取上传的excel文件
	 * @param unknown_type $fileName
	 * @param unknown_type $filePath
	 * @return string|mixed|Ambigous <multitype:, string>
	 */
	public  function readUploadFile($fileName, $filePath,$sheet=0)
	{
	    $pathinfo = pathinfo($fileName);
	    $fileData = array();
	
	    if ( isset($pathinfo["extension"]) && $pathinfo["extension"] == "xls") {
	        $fileData = Common_Upload::readEXCEL($filePath,$sheet,false);
	        if (is_array($fileData)) {
	            $result = array();
	            $columnMap = array();
	            foreach ($fileData[0] as $key => $value) {
	                if (isset($columnMap[$value])) {
	                    $fileData[0][$key] = $columnMap[$value];
	                }
	            }
	            foreach ($fileData as $key => $value) {
	                if ($key == 0) {
	                    continue;
	                }
	                foreach ($value as $vKey => $vValue) {
	                    if ($fileData[0][$vKey] == ""){
	                        continue;
	                    }
	                    $vValue = trim($vValue);
	                    $result[$key][$fileData[0][$vKey]] =$vValue;
	                }
	            }
	            return $result;
	        }else{
	            return $fileData;
	        }
	    }else{
	        return '文件格式不正确，请上传xls文件';
	    }
	}
	
	/**
	 * 订单批量导入 手工订单
	 * @param unknown_type $file
	 * @param unknown_type $tpl_id
	 * @param unknown_type $user_account
	 * @param unknown_type $platform
	 * @throws Exception
	 * @return multitype:number string NULL
	 */
	public function importTransaction($file){
	    $return = array(
	            'ask' => 0,
	            'message' => ''
	    );
	    $map = array (
				'仓库代码/Warehouse Code' => 'warehouse_code',
				'参考编号/Reference Code' => 'refrence_no',
				'派送方式/Delivery Style' => 'shipping_method',
				'销售平台/Sales Platform' => 'platform',
				'收件人姓名/Consignee Name' => 'consignee_name',
				'收件人公司/Consignee Company' => 'consignee_company',
				'收件人国家/Consignee Country' => 'consignee_country_code',
				'州/Province' => 'consignee_state',
				'城市/City' => 'consignee_city',
				'街道/Street' => 'consignee_street1',
				'门牌号/Doorplate' => 'consignee_doorplate',
				'邮编/Zip Code' => 'consignee_zip',
				'收件人Email/Consignee Email' => 'consignee_email',
				'收件人电话/Consignee Phone' => 'consignee_phone',
				'备注/Remark' => 'operator_note',
				'SKU1' => 'op_sku_1',
				'数量1/Quantity 1' => 'op_quantity_1',
				'SKU2' => 'op_sku_2',
				'数量2/Quantity 2' => 'op_quantity_2',
				'SKU3' => 'op_sku_3',
				'数量3/Quantity 3' => 'op_quantity_3',
				'SKU4' => 'op_sku_4',
				'数量4/Quantity 4' => 'op_quantity_4',
				'SKU5' => 'op_sku_5',
				'数量5/Quantity 5' => 'op_quantity_5',
	    		'SKU6' => 'op_sku_6',
	    		'数量6/Quantity 6' => 'op_quantity_6',
	    		'SKU7' => 'op_sku_7',
	    		'数量7/Quantity 7' => 'op_quantity_7',
	    		'SKU8' => 'op_sku_8',
	    		'数量8/Quantity 8' => 'op_quantity_8',
				'SKU9' => 'op_sku_9',
				'数量9/Quantity 9' => 'op_quantity_9',
				'SKU10' => 'op_sku_10',
				'数量10/Quantity 10' => 'op_quantity_10',
		);
		$platformMap = array (
				'amazon' => 'amazon',
				'ebay' => 'ebay',
				'aliexpress' => 'aliexpress',
				'b2c' => 'b2c',
				'other' => 'other' 
		);
		$errs = array();
		$successCount = $failCount = 0;
	    $db = Common_Common::getAdapter();
	    $db->beginTransaction ();
	    try{
	        if($file['error']){
	            throw new Exception('请选择xls文件');
	        }
	        if(empty($file)){
	            throw new Exception('参数错误');
	        }
	        $fileName = $file['name'];
	        $filePath = $file['tmp_name'];
	        $pathinfo = pathinfo($fileName);
	        if(isset($pathinfo["extension"]) && $pathinfo["extension"] == "xls"){
	            $fileData = $this->readUploadFile($fileName, $filePath,0);
	            if(empty($fileData)){
	                throw new Exception('文件中必须包含有内容');
	            }
	            //列转换
	            $fileDataFormat = array();
	            foreach($fileData as $k=> $v){
	            	foreach($v as $kk=>$vv){
	            		$fileDataFormat[$k][$map[$kk]] = $vv;
	            	}
	            }
	            
	            foreach($fileDataFormat as $k=>$v){
	            	try{
	                  $v ['platform'] = strtolower($v ['platform']);
						$orderR = array (
								'company_code' => Common_Company::getCompanyCode (),
								// 'order_id' =>
								// $this->getRequest()->getParam('',''),
								'platform' => !isset($platformMap[$v ['platform']])||empty($v ['platform'])?'Other':$platformMap[$v ['platform']],
								'order_status' => '2',
								'create_method' => '1',
								'customer_id' => Common_Company::getCompanyCode (),
								'shipping_method' => $v ['shipping_method'],
								// 'warehouse_id' =>
								// $this->getRequest()->getParam('warehouse_id',
								// '0'),
								'warehouse_code' => $v ['warehouse_code'],
								'order_desc' => $v['operator_note'],
								'operator_note' => $v ['operator_note'],
								'refrence_no' => $v ['refrence_no'],
								
								'consignee_county' => $v ['consignee_county'],
								'consignee_country_code' => $v ['consignee_country_code'],
								'consignee_country_name' => $v ['consignee_country_name'],
								'consignee_city' => $v ['consignee_city'],
								'consignee_state' => $v ['consignee_state'],
								'consignee_postal_code' => $v ['consignee_zip'],
								'consignee_company' => $v ['consignee_company'],
								'consignee_street1' => $v ['consignee_street1'],
								'consignee_street2' => '',
								'consignee_street3' => '',
								'consignee_doorplate' => $v ['consignee_doorplate'],
								'consignee_name' => $v ['consignee_name'],
								'consignee_phone' => $v ['consignee_phone'],
								'consignee_email' => $v ['consignee_email'],
								'date_last_modify' => date ( 'Y-m-d H:i:s' ) 
						);
						$orderProductRs = array();
						foreach($v as $kk=>$vv){
							if(preg_match('/^op_/',$kk)){
								if(trim($vv)==''){
									continue;
								}
								$aaa = explode('_', $kk);							
								$orderProductRs[$aaa[2]][$aaa[1]] = $vv;
							}
						}
						$order_product = array ();
						foreach ( $orderProductRs as $op) {
							$con = array('product_sku'=>$op['sku'],'costomer_code'=>Common_Company::getCompanyCode());
							$product = Service_Product::getByCondition($con);
							if(empty($product)){
								throw new Exception(Ec::Lang('sku_not_exist',$op['sku']));
							}
							$product = $product[0];
							$order_product [] = array (
									'product_id' => $product['product_id'],
									'op_quantity' => $op['quantity'] 
							);
						}
						
						$refrence_no_platform = Common_GetNumbers::getCode ( 'CURRENT_ORDER_COUNT', $orderR['company_code'], '' ); // 订单号
						$refrence_no_sys = $refrence_no_platform; // 系统单号
						
						$orderR ['refrence_no_sys'] = $refrence_no_sys;
						
						$orderR ['refrence_no_platform'] = $refrence_no_platform;
						$row = array (
								'order' => $orderR,
								'order_product' => $order_product 
						);
						$process = new Service_OrderProcess ();
						try{
						    $process->createOrder( $row );
						   
						}catch (Exception $eeee){
						    //异常
						    if($process->_err){
						        $errs[$k+1] = $process->_err;
						    }
						    throw new Exception($eeee->getMessage());
						}
						
						$successCount++;
					}catch(Exception $eee){
						$failCount++;
						//$errs[$k+1] = array($eee->getMessage());
						//throw new Exception($eee->getMessage(),$eee->getCode());
					}
				}
				//print_r($errs);exit;
				if($errs){
				    throw new Exception('数据不合法，导入失败');
				}
	            $return['ask'] = 1 ;
	            $return['message'] = '共导入订单 '.count($fileDataFormat)." 个";
	            $db->commit();
	
	        }else{
	            throw new Exception('文件格式不正确，请选择xls文件');
	        }
	    }catch(Exception $e){
	        $db->rollback();
	        $return['message'] = $e->getMessage();
	    }
	    $return['errs'] = $errs;
	    return $return;
	}
	
    //=================================================================================
    /**
     * 截单申请
     * @param array $orderIds
     * @param int $status
     * @param int $reasonType
     * @param string $reason 
     * @return array
     */
    public static function orderCancelBatchTransaction($refIds, $reason='截单'){
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(!is_array($refIds)){
                throw new Exception('参数 $orderIds 必须为数组');
            }
            $results = array();
            foreach($refIds as $refId){
                $result = self::orderCancel($refId, $reason);                
                $results[] = $result;
            }
            $db->commit();
            $return['ask'] = '1';
            $return['result'] = $results;            
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }        
        return $return;
    }

    /**
     * 截单申请
     * 发起截单申请，如果wms未操作，返回成功，标记截单成功
     * 如果仓库已经操作，返回成功，并返回警告信息，标记截单申请为处理中,待wms回调标记截单成功或者已出库
     * @param int $orderId
     * @param int $status
     * @param int $reasonType
     * @param string $reason 
     * @return array
     */
    public static function orderCancel($refId, $reasonType = '2', $reason='截单'){
        $return = array(
            'ask' => 0,
            'message' => '',
            'ref_id' => $refId,
            'cancel_status'=>'0'
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
       
        try{
        	
            // 发送数据到WMS
            $apiService = new Common_ThirdPartWmsAPI();
            $rs = $apiService->cancelTransferOrder($refId, $reason);
            if($rs['ask']!='Failure'){ // 截单成功
            	if($reasonType == '2') { 
            		$updateRow['order_status'] = 0;
            	} else {
            		$updateRow['order_status'] = 1;
            	}
            	
                $updateRow['cancel_status'] = 1;
                $updateRow['date_last_modify'] = date('Y-m-d H:i:s');
                
                // 更新内容
                if(! Service_TransferOrders::update($updateRow, $refId, 'two_code')){
                    throw new Exception(Ec::Lang('inner_error'), 50000);
                }
                $content = 'OMS截单请求发送成功，取消原因:' . $reason;
                $logRow = array(
                    'ref_id' => $refId,
                    'log_content' => $content,
                    'op_id' => ''
                );
                
                self::writeOrderLog($logRow);
                
                $return['ask'] = 1;
                $return['message'] = 'Success';
                $db->commit();
            }else{//截单失败
                throw new Exception(Ec::Lang('wms_error', $rs['message']));
            }
            
//             echo "aaa"; print_r($rs);
        }catch(Exception $e){
            $db->rollback();
            $content = '截单失败,'.$e->getMessage();
            $logRow = array(
                    'ref_id' => $refId,
                    'log_content' => $content,
                    'op_id' => ''
            );
            self::writeOrderLog($logRow);
            $return['message'] = $e->getMessage();
            
        }
        return $return;
    }

    /**
     * 转仓单批量审核
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string NULL >
     */
    public static function orderVerifyBatch($refIds){
        $return = array();
        $successCount = 0;
        $failCount = 0;
        $quehuoCount = 0;//缺货
        $fundCount = 0;//欠费
        $all = array();
        $successArr = array();
        $failArr = array();
        foreach($refIds as $refId){
            $result = self::orderVerifyTransaction($refId);
            switch ($result['ask']){
				case 0 :
					$failArr [] = $result;
					$failCount ++;
					break;
				case 1 :
					$successArr [] = $result;
					$successCount ++;
					break;
				case 2 :
					$successArr [] = $result;
					$successCount ++;
					$fundCount ++;
					break;
				case 3 :
					$successArr [] = $result;
					$successCount ++;
					$quehuoCount ++;
					break;
			}
            $all[] = $result;
        }
        $return['success_count'] = $successCount;
        $return['quehuo_count'] = $quehuoCount;
        $return['fund_count'] = $fundCount;
        $return['fail_count'] = $failCount;
        $return['result'] = $failArr;
        return $return;
    }
    /**
     * 转仓单发货审核,将订单信息发送到仓库系统
     * 判断当前状态，如果已经审核通过，直接返回，其他状态，抛出异常
     * @param unknown_type $orderIds
     * @return multitype:number string NULL Ambigous <multitype:, mixed,
     *         multitype:number string NULL multitype:multitype:unknown
     *         multitype:unknown NULL Ambigous <multitype:, multitype:number
     *         string unknown Ambigous <string, unknown> > >
     */
    public static function orderVerifyTransaction($refId)
    {
        $return = array(
            'ask' => 0, // 0异常，1成功，2:欠费,3缺货
            'message' => '',
            'ref_id' => $refId
        );
        $orderNoStock = false; // 订单缺货
        $abnormalLog = array();
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $rs = self::orderVerify($refId);
            $db->commit();
            //订单费用
// 			$wmsProcess = new Common_ThirdPartWmsAPIProcess();
//             $wmsProcess->syncOrder($refId);
            
            $return['ask'] = 1;
            if($rs==1){
            	$return['ask'] = 1;
            }
            if($rs==2){
            	$return['ask'] = 2;
            }
            if($rs==3){
            	$return['ask'] = 3;
            }
            $return['message'] = 'Success';
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
            // 记录订单操作日志
            $content = '订单审核失败，失败原因:' . $e->getMessage();
            // 这里还有日志信息，以后添加
            $logRow = array(
                'ref_id' => $refId,
                'log_content' => $content,
                'op_id' => ''
            );
            self::writeOrderLog($logRow);
        }
        return $return;
    }
    /**
     * 转仓单发货审核,将订单信息发送到仓库系统
     * 判断当前状态，如果已经审核通过，直接返回，其他状态，抛出异常
     * @param unknown_type $orderIds
     * @return multitype:number string NULL Ambigous <multitype:, mixed,
     *         multitype:number string NULL multitype:multitype:unknown
     *         multitype:unknown NULL Ambigous <multitype:, multitype:number
     *         string unknown Ambigous <string, unknown> > >
     */
    public static function orderVerify($refId)
    {
    	$result = 0;
        $order = Service_TransferOrders::getByField($refId, 'two_code');
        
        if(empty($order)){
            throw new Exception(Ec::lang('order_not_exist'));
        }
        if($order['order_status'] != 1){
            throw new Exception(Ec::lang('order_op_deny'));
        }
        
        // 发送数据到WMS
        $apiService = new Common_ThirdPartWmsAPI();
        $rs = $apiService->createTransferOrder($refId);
//         $rs = array('ask' => 'Sucess');
        if($rs['ask']!='Failure'){
        		$updateRow = array(
        			'order_status' => '2',
        			'date_release' => date('Y-m-d H:i:s'),
        			'date_last_modify' => date('Y-m-d H:i:s')
        		);
            
            // 更新内容
            if(!Service_TransferOrders::update($updateRow, $refId, 'two_code')){
                throw new Exception(Ec::Lang('inner_error'));
            }

            //更新费用
            self::updateOrderFee($refId, $rs['data']);
            
            $content = '订单审核Success';
            // 这里还有日志信息，以后添加
            $logRow = array(
                'ref_id' => $refId,
                'log_content' => $content,
                'op_id' => ''
            );
            self::writeOrderLog($logRow);
        }else{
            throw new Exception(Ec::Lang('wms_error', $rs['message']));
        }
        return $result;
    }


    /**@desc 更新订单费用(创建订单、获取订单费用、共用此方法)
     * @param string $orderCode
     * @param array $orderFeeArr
     */
    public static function updateOrderFee($orderCode = '', $orderFeeArr = array())
    {
        $orderRow = Service_TransferOrders::getByField($orderCode, 'two_code');
        if (empty($orderRow)) {
            return;
        }
        //================================费用 start
        // 费用
        Service_OrderFee::delete($orderCode, 'ref_id');
        //订单费用
        $orderFeeSummery = array(
            'ship_cost' => 0,
            'op_cost' => 0,
            'fuel_cost' => 0,
            'register_cost' => 0,
            'tariff_cost' => 0,
            'incidental_cost' => 0,
            'warehouse_cost' => 0,
        );
        foreach ($orderFeeArr['order_fee'] as $fee) {
            $feeRow = array(
                'ref_id' => $orderRow['two_code'],
                'customer_code' => $orderRow['company_code'],
                'cs_code' => '',
                'ft_code' => $fee['ft_code'],
                'bi_amount' => $fee['bi_amount'],
                'currency_code' => $fee['currency_code'],
                'currency_rate' => $fee['currency_rate'],
                'bi_sp_type' => 2, //均为预付
                'bi_creator_id' => 0,
                'bi_balance_sign' => $fee['bi_balance_sign'],
                'bi_writeoff_sign' => 'n',
                'bi_credit_pay' => 0,
                'bi_note' => $fee['bi_note'],
                'bi_billing_date' => $fee['bi_chargeable_time']
            );
            Service_OrderFee::add($feeRow);

            switch (strtoupper($fee['ft_code'])) {
                case 'SHIPPING' :
                    $orderFeeSummery['ship_cost'] = $fee['bi_amount'];
                    break;
                case 'WHOSCOW' : //操作费用
                case 'WHOSCOP' :
                case 'LOC' :
                case 'OPF' :
                    $orderFeeSummery['op_cost'] += $fee['bi_amount'];
                    break;
                case 'FSC' :
                    $orderFeeSummery['fuel_cost'] += $fee['bi_amount'];
                    break;
                case 'DT' : //关税
                    $orderFeeSummery['tariff_cost'] += $fee['bi_amount'];
                    break;
                case 'RSF' : //挂号
                    $orderFeeSummery['register_cost'] += $fee['bi_amount'];
                    break;
                case 'WHF' : //仓租
                    $orderFeeSummery['warehouse_cost'] += $fee['bi_amount'];
                    break;
                default: //其它费用
                    $orderFeeSummery['incidental_cost'] += $fee['bi_amount'];
                    break;
            }
            //                     print_r($feeRow);exit;
        }
        //计费重量
        $orderFeeSummery['order_weight'] = $orderFeeArr['charged_weight'];
        $orderFeeSummery['customer_code'] = $orderRow['company_code'];
        $orderFeeSummery['shipping_method'] = $orderRow['shipping_method'];
        $orderFeeSummery['country_code'] = $orderRow['consignee_country'];
        $orderFeeSummery['business_type'] = '2'; // 业务类型，转仓单
        //费用更新
        $feeExist = Service_OrderFeeSummery::getByField($orderRow['two_code'], 'ref_id');
        if ($feeExist) {
            Service_OrderFeeSummery::update($orderFeeSummery, $orderRow['two_code'], 'ref_id');
        } else {
            $orderFeeSummery['ref_id'] = $orderRow['two_code'];
            Service_OrderFeeSummery::add($orderFeeSummery);
        }
    }
}