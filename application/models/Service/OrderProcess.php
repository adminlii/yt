<?php
class Service_OrderProcess { 
    public $_err = array();
    /**
     * 订单状态
     * @return unknown
     */
    public static function getOrderStatus(){


        $actions = array(
            '<input type="button" class="verifyBtn baseBtn opBtn" op="verify" value="' . Ec::Lang('提交预报') . '">', 
           // '<input type="button" class="pauseBtn baseBtn opBtn" op="pause" value="' . Ec::Lang('暂存') . '">', 
            '<input type="button" class="interceptBtn baseBtn opBtn" op="intercept" value="' . Ec::Lang('拦截') . '">',
            //'<input type="button" class="deleteBtn baseBtn opBtn" op="discard" value="' . Ec::Lang('废弃') . '">',
//             '<input type="button" class="discard2draftBtn baseBtn opBtn" op="discard2draft" value="' . Ec::Lang('转草稿') . '">',
            '<input type="button" class="printBtn baseBtn " op="print" value="' . Ec::Lang('打印') . '">',
            '<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
        );
        $statusArr = array(
            '' => array(
                'name' => Ec::Lang('all_orders'),
                'actions' => array(
                    //'<input type="button" class="verifyBtn baseBtn opBtn" op="verify" value="' . Ec::Lang('提交预报') . '">',
                    //'<input type="button" class="pauseBtn baseBtn opBtn" op="pause" value="' . Ec::Lang('暂存') . '">', 
                    //'<input type="button" class="interceptBtn baseBtn opBtn" op="intercept" value="' . Ec::Lang('拦截') . '">',
                    //'<input type="button" class="discardBtn baseBtn opBtn" op="discard" value="' . Ec::Lang('废弃') . '">',
                    //'<input type="button" class="discard2draftBtn baseBtn opBtn" op="discard2draft" value="' . Ec::Lang('转草稿') . '">',
                    //'<input type="button" class="printBtn baseBtn " op="print" value="' . Ec::Lang('打印') . '">',
                    '<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
                )
            ),

            'D' => array(
                    'name' => Ec::Lang('order_status_7'),
                    'actions' => array(
                        '<input type="button" class="verifyBtn baseBtn opBtn" op="verify" value="' . Ec::Lang('提交预报') . '">', 
                        //'<input type="button" class="pauseBtn baseBtn opBtn" op="pause" value="' . Ec::Lang('暂存') . '">',  
                        '<input type="button" class="editInvoiceBtn baseBtn" op="editInvoice" value="' . Ec::Lang('编辑申报信息') . '">',  
                        '<input type="button" class="importInvoiceBtn baseBtn" op="importInvoice" value="' . Ec::Lang('上传申报信息') . '">',  
                        '<input type="button" class="importWeightBtn baseBtn" op="importWeight" value="' . Ec::Lang('上传更新重量') . '">',  
                        //'<input type="button" class="discardBtn baseBtn opBtn" op="discard" value="' . Ec::Lang('废弃') . '">',
                        '<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
                    )
            ),
            'S' => array(
                'name' => Ec::Lang('order_status_8'),
                'actions' => array(
                    //'<input type="button" class="discardBtn baseBtn opBtn" op="discard" value="' . Ec::Lang('废弃') . '">',
                ),
            ),
            'P' => array(
                'name' => Ec::Lang('order_status_2'),
                'actions' => array(
                   // '<input type="button" class="interceptBtn baseBtn opBtn" op="intercept" value="' . Ec::Lang('拦截') . '">',                		
                    //'<input type="button" class="discardBtn baseBtn opBtn" op="discard" value="' . Ec::Lang('废弃') . '">',
                	//'<input type="button" class="editInvoiceBtn baseBtn" op="editInvoice" value="' . Ec::Lang('编辑申报信息') . '">',
                //'<input type="button" class="importInvoiceBtn baseBtn" op="importInvoice" value="' . Ec::Lang('上传申报信息') . '">',
                	//'<input type="button" class="importWeightBtn baseBtn" op="importWeight" value="' . Ec::Lang('上传更新重量') . '">',
                    '<input type="button" class="printBtn baseBtn " op="print" value="' . Ec::Lang('打印') . '">',
                   // '<input type="button" class="printInvoiceBtn baseBtn" op="printInvoice" value="' . Ec::Lang('打印形式发票') . '">',
                    '<input type="button" class="printAsnBtn baseBtn" op="printAsn" value="' . Ec::Lang('打印交货清单') . '">',
                    '<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
                ),
                'process_again' => array(
                    //'Y' => Ec::Lang('问题件'),
                )
            ),
            'V' => array(
               // 'name' => Ec::Lang('order_status_3'),
                'actions' => array(
                    '<input type="button" class="interceptBtn baseBtn opBtn" op="intercept" value="' . Ec::Lang('拦截') . '">',
                    '<input type="button" class="printBtn baseBtn " op="print" value="' . Ec::Lang('打印') . '">',
                    '<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
                ),
                
            ),
            
            'C' => array(
               // 'name' => Ec::Lang('order_status_4'),
                'actions' => array(
                    '<input type="button" class="printBtn baseBtn " op="print" value="' . Ec::Lang('打印') . '">',
                    '<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
                )
            ),
            /* 'Q' => array(
                    'name' => Ec::Lang('order_status_5'),
                    'actions' => array(
                    '<input type="button" class="verifyBtn baseBtn opBtn" op="verify" value="' . Ec::Lang('提交预报') . '">', 
                   // '<input type="button" class="discardBtn baseBtn opBtn" op="discard" value="' . Ec::Lang('废弃') . '">',
                    '<input type="button" class="discard2draftBtn baseBtn opBtn" op="discard2draft" value="' . Ec::Lang('转草稿') . '">',
                    '<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
                )
            ),
            'U' => array(
                'name' => Ec::Lang('order_status_7'),
                'abnormal_type' => array(
//                     '1' => Ec::Lang('abnormal_change_address'),
//                     '2' => Ec::Lang('abnormal_intercept_order'),
//                     '3' => Ec::Lang('abnormal_change_sku'),
//                     '4' => Ec::Lang('abnormal_other')
                    '5' => Ec::Lang('abnormal_change_no_fail')
                ),
            	'actions' => array(
            			'<input type="button" class="verifyBtn baseBtn opBtn" op="verify" value="' . Ec::Lang('提交预报') . '">',
            			//'<input type="button" class="discardBtn baseBtn opBtn" op="discard" value="' . Ec::Lang('废弃') . '">',
            			'<input type="button" class="discard2draftBtn baseBtn opBtn" op="discard2draft" value="' . Ec::Lang('转草稿') . '">',
            			'<input type="button" class="exportBtn baseBtn " op="export" value="' . Ec::Lang('导出') . '">'
            	)
            ),
            'E' => array(
                'name' => Ec::Lang('order_status_0'),
                'actions' => array(
                    '<input type="button" class="discard2draftBtn baseBtn opBtn" op="discard2draft" value="' . Ec::Lang('转草稿') . '">',
                ),
            ) */
            
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
                // 验证申报价值格式ruston0924
                if(!preg_match('/^[0-9]+\.?[0-9]{2}$/',$p['parcel_declared_price'])||!preg_match('/^[0-9]+\.?[0-9]{2}$/',$p['parcel_declared_value'])){
                    $this->_err[] = Ec::Lang('product_parcel_declared_value_no');
                    //throw new Exception(Ec::Lang('sku_quantity_must_int_and_gt_0'),'30000');
                }
                // 验证申报价值是否有问题ruston0924
                if($p['parcel_declared_price']*$p['op_quantity']!=$p['parcel_declared_value']){
                    $this->_err[] = Ec::Lang('product_parcel_declared_value_no');
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
        // 判断国家
        if(empty($orderRow['consignee_country_code'])){
            // '收件人国家必填'
            $this->_err[] = Ec::Lang('consignee_country_can_not_empty');
            //throw new Exception(Ec::Lang('consignee_country_can_not_empty'),'30000');
        }else{
            $country = Service_Country::getByField($orderRow['consignee_country_code'],'country_code');
            if(!$country){
                $this->_err[] = Ec::Lang('country_code_illagel',$orderRow['consignee_country_code']);
                //throw new Exception(Ec::Lang('country_code_illagel',$orderRow['consignee_country_code']),'30000');
            }
            $orderRow['consignee_county'] = $country['country_code'];
            $orderRow['consignee_country_name'] = $country['country_name_en'];
//             $orderRow['country_id'] = $country['country_id'];
        }
        // 判断收件人信息
    
        if(empty($orderRow['consignee_state'])){
            // //throw new Exception('省份/州必填');
        }

        if(empty($orderRow['consignee_phone'])){
            // '电话'
            $this->_err[] = Ec::Lang('consignee_phone_can_not_empty');
            //throw new Exception(Ec::Lang('consignee_phone_can_not_empty'),'30000');
        }
        
        /*if(!preg_match('/^([0-9]{1,11})$/', $orderRow['consignee_phone'])){
            // '电话'
            $this->_err[] = Ec::Lang('consignee_phone_only_numric_and_length_less_then_11');
            //throw new Exception(Ec::Lang('consignee_phone_only_numric_and_length_less_then_11'),'30000');
        }*/ // 去掉11位数字的限制，4PX的预报接口也会去掉 RUSTON0813
        if(empty($orderRow['consignee_city'])){
            // '城市必填'
            $this->_err[] = Ec::Lang('consignee_city_can_not_empty');
            //throw new Exception(Ec::Lang('consignee_city_can_not_empty'),'30000');
        }
        $orderRow['consignee_street1'] = preg_replace('/\s+/', ' ', $orderRow['consignee_street1']);
        $orderRow['consignee_street2'] = preg_replace('/\s+/', ' ', $orderRow['consignee_street2']);
        $orderRow['consignee_street3'] = preg_replace('/\s+/', ' ', $orderRow['consignee_street3']);
        if(empty($orderRow['consignee_street1']) && empty($orderRow['consignee_street2']) && empty($orderRow['consignee_street3'])){
            // '地址必填一个'
            $this->_err[] = Ec::Lang('consignee_street_can_not_empty');
            //throw new Exception(Ec::Lang('consignee_street_can_not_empty'),'30000');
        }
        $street = trim($orderRow['consignee_street1']).' '.trim($orderRow['consignee_street2']);
        $street = preg_replace('/\s+/', ' ', $street);
        if(strlen($street)>90){
            // 地址'太长'
            $this->_err[] = Ec::Lang('consignee_street_too_long');
            //throw new Exception(Ec::Lang('consignee_street_too_long'),'30000');
        }
        
        if(empty($orderRow['consignee_name'])){
            // '收件人必填'
            $this->_err[] = Ec::Lang('consignee_name_can_not_empty');
            //throw new Exception(Ec::Lang('consignee_name_can_not_empty'),'30000');
        }
        
        if($orderRow['consignee_country_code']=='RU'){
            if(empty($orderRow['consignee_postal_code'])){
                // '邮编'
                $this->_err[] = Ec::Lang('consignee_postal_code_can_not_empty');
                //throw new Exception(Ec::Lang('consignee_postal_code_can_not_empty'),'30000');
            }
            if(!preg_match('/^[0-9]{6}$/',$orderRow['consignee_postal_code'])){
                // '邮编'
                //             //throw new Exception(Ec::Lang('consignee_postal_code_illagel'),'30000');
            }
            // '邮编'
            $area = Service_TransportPickAreaMap::getByField($orderRow['consignee_postal_code'],'post_code');
            if(empty($area)){
                $this->_err[] = Ec::Lang('consignee_postal_code_illagel');
                //throw new Exception(Ec::Lang('consignee_postal_code_illagel'),'30000');
            }
        }
        //RUSTON0904 判断收件人省/州不能为空
        if(empty($orderRow['consignee_state'])){
            // '收件人省/州必填'
            $this->_err[] = Ec::Lang('consignee_state_can_not_empty');
        }
        //$sql = "SELECT distinct a.sm_code,a.sm_name_cn,a.sm_name from shipping_method a INNER JOIN shipping_method_settings b on a.sm_id=b.sm_id INNER JOIN sm_area_map c on b.warehouse_id=c.warehouse_id where c.country_id='{$country['country_id']}' and c.warehouse_id='{$orderRow['warehouse_id']}' and a.sm_code='{$orderRow['shipping_method']}';";
        $sql = "SELECT distinct a.sm_code,a.sm_name,a.sm_name_cn from shipping_method a INNER JOIN shipping_method_settings b on a.sm_id=b.sm_id where b.warehouse_id='{$orderRow['warehouse_id']}' and a.sm_status=1 and b.sms_status=1 and a.sm_code='{$orderRow['shipping_method']}';";
        $db = Common_Common::getAdapter();
        $rs = $db->fetchAll($sql);
        if(empty($rs)){
            $this->_err[] = Ec::Lang('warehouse_country_not_support_shipping_method');
            //throw new Exception(Ec::Lang('warehouse_country_not_support_shipping_method'));
        }
        // 以上判断需要抛出异常
        
        $platform = Service_Platform::getByField($orderRow['platform'],'platform');
        if(!$platform){
            $orderRow['platform'] = 'Other';
        }
        $orderRow['platform'] = strtoupper($orderRow['platform']);
        //是否有留言标记(订单备注)
        $orderRow['has_buyer_note'] = empty($orderRow['order_desc']) ? '0' : '1';
		//处理阿里传来的国际订单号 RUSTON0719
		$orderRow['mail_no'] = empty($orderRow['mail_no']) ? '' : $orderRow['mail_no'];
        
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
			$refrence_no_platform = Common_GetNumbers::getCode ( 'CURRENT_ORDER_COUNT', $row['order'] ['company_code'], '' ); // 订单号
			
			$refrence_no_sys = $refrence_no_platform; // 系统单号
			
			$row['order']['refrence_no_sys'] = $refrence_no_sys;
			
			$row['order']['refrence_no_platform'] = $refrence_no_platform;
			$result = $this->createOrder ( $row );
			$db->commit ();
			$result = array (
					"ask" => 1,
					"message" => Ec::Lang('create_order_success',$refrence_no_platform),
					'ref_id' => $refrence_no_platform
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
     * 验证参考单号是否存在
     * @param string $refrenceNo 客户参考号
     * @param string $refId 平台订单号
     * @return boolean
     */
    public function validateRefrenceNo($refrenceNo,$refId=''){
        if($refrenceNo){
            $con = array(
                'refrence_no' => $refrenceNo
            );
            $rows = Service_Orders::getByCondition($con);
            foreach($rows as $k => $v){
                if($refId == $v['refrence_no_platform']){
                    unset($rows[$k]);
                    continue;
                }
                if($v['order_status']=='0'){
                    unset($rows[$k]);
                    continue;
                }
            }
            if($rows){
                $this->_err[] = Ec::Lang('reference_no_exist',$refrenceNo);
                //throw new Exception(Ec::Lang('reference_no_exist',$refrenceNo), '30000');
            }
        }
        
        return true;
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
		//验证参考单号
		$this->validateRefrenceNo($orderRow['refrence_no'],'');
		
		if($this->_err){//有异常，抛出异常
		    throw new Exception(Ec::lang('validate_err'));
// 		    throw new Exception($this->_err[0]); // 抛出实际的错误信息，便于了解定位问题所在 RUSTON0719
		}
		$orderRow['order_status'] = '2';

		$orderRow['date_create'] = $time;
		$refrence_no_platform = $orderRow['refrence_no_platform'];
		
		if (! $orderId = Service_Orders::add ( $orderRow )) {
			throw new Exception ( Ec::lang('inner_db_error'), '50000' );
		}
				
		$orderProduct = $row['order_product'];
        $qtuSum = 0;
		foreach ( $orderProduct as $v ) {
			$productId = $v ['product_id'];
			$productSku = $v['product_sku'];
			$productBarcode = $v['product_barcode'];
			$productTitle = empty($v ['product_title'])?'':$v ['product_title'];
			$parcelDeclaredValue = empty($v ['parcel_declared_value'])? 0:$v ['parcel_declared_value'];//申报价值 RUSTON0724
			$parcelDeclaredName = empty($v ['parcel_declared_name'])?'':$v ['parcel_declared_name'];//申报名称 RUSTON0724
			$qty = $v ['op_quantity'];
			
			$qtuSum+=$qty;
			$now = date("Y-m-d H:i:s");
			$orderProductRow = array (
					'order_id' => $orderId,
			        'OrderID'=>$refrence_no_platform,
					'product_id' => $productId,
					'product_sku' => $productSku,
					'product_barcode' => $productBarcode,
					'op_quantity' => $qty,			        
					'product_title' => $productTitle,
			        //以下信息不重要 start
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
			        'OrderIDEbay'=>isset($v['OrderIDEbay'])?$v['OrderIDEbay']:'',			        
			        'create_type'=>isset($v['create_type'])?$v['create_type']:'',
					'op_ref_paydate' => $now,
					'order_item_id'=> isset($v['order_item_id'])?$v['order_item_id']:'', //阿里订单商品ID RUSTON0719
			        //以下信息不重要 end
					'op_add_time' => $now,
					'op_update_time' => $now,
					'parcel_declared_value' => $parcelDeclaredValue, //申报价值 RUSTON0724
					'parcel_declared_name' => $parcelDeclaredName, //申报名称 RUSTON0724
			);
// 			print_r($orderProductRow);exit;
			
			if (! Service_OrderProduct::add ( $orderProductRow )) {
				throw new Exception ( Ec::lang('inner_db_error'), '50000' );
			}
		}
		$updateRow = array(
		        'product_count' => $qtuSum,
		);
		if($qtuSum==1){
            $updateRow['is_one_piece'] = '1';
        }else{
            $updateRow['is_one_piece'] = '0';
        }
        
		Service_Orders::update($updateRow, $refrence_no_platform,'refrence_no_platform');
		
		$logRow = array(
            'ref_id' => $refrence_no_platform,
            'log_content' => '创建订单' ,
            'op_id' => ''
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
        $order = Service_Orders::getByField($refId, 'refrence_no_platform');
        if(empty($order)){
            throw new exception(Ec::Lang('order_not_exist'),$refId);
        }
        $orderId = $order['order_id'];
        // 草稿状态能更新，其他状态不可更新
        $allowStatus = array(2,7);
        if(!in_array($order['order_status'],$allowStatus)){
            // "Order Can't Edit,OrderStatus Is Not Draft-->{$orderId}"
            throw new exception(Ec::Lang('order_edit_deny',$refId));
        }
        // 验证输入的数据是否正确
        $row = $this->_orderValidate($row);
        $time = date("Y-m-d H:i:s");
        
        $orderRow = $row['order'];
        
        //验证参考单号
        $this->validateRefrenceNo($orderRow['refrence_no'],$order['refrence_no_platform']);
        
        if($this->_err){//有异常，抛出异常
		    throw new Exception(Ec::lang('validate_err'));
        }
        
        if(! Service_Orders::update($orderRow, $orderId, 'order_id')){
            throw new Exception(Ec::lang('inner_db_error'), '50000');
        }

        Service_OrderProduct::delete($orderId, 'order_id');
        $order_product = $row['order_product'];
        foreach($order_product as $v){

            $productId = $v ['product_id'];
            $productSku = $v['product_sku'];
            $productBarcode = $v['product_barcode'];
            $productTitle = empty($v ['product_title'])?'':$v ['product_title'];
            $qty = $v ['op_quantity'];
            $parcelDeclaredValue = empty($v ['parcel_declared_value'])? 0:$v ['parcel_declared_value'];//申报价值 RUSTON0929
			$parcelDeclaredName = isset($v ['parcel_declared_name'])?empty($v ['parcel_declared_name'])?'':$v ['parcel_declared_name']:'';//申报名称 RUSTON0929
            $now = date("Y-m-d H:i:s");
            $orderProductRow = array(
                'order_id' => $orderId,
                'product_id' => $productId,
                'product_sku' => $productSku,
                'product_barcode' => $productBarcode,
                'product_title' => $productTitle,
                'op_quantity' => $qty,
                // 以下信息不重要 start
                'op_ref_tnx' => '',
                'op_ref_item_id' => '',
                'op_ref_buyer_id' => '',
                'op_ref_paydate' => $now,
                // 以下信息不重要 end
                'op_add_time' => $now,
                'op_update_time' => $now,
                'parcel_declared_value' => $parcelDeclaredValue, //申报价值 RUSTON0929
            );
            if(!empty($parcelDeclaredName))$orderProductRow['parcel_declared_name']=$parcelDeclaredName;//申报名称 RUSTON0929
            if(! Service_OrderProduct::add($orderProductRow)){
                throw new Exception(Ec::lang('inner_db_error'), '50000');
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
                //如果是要拦截，需要从wms库存解冻，如果涉及到费用，需要费用回退，成功之后，状态变为作废
                throw new Exception(Ec::Lang('order_operation_deny'));
                //如果是要转为发货审核状态，需要从wms库存解冻，如果涉及到费用，需要费用回退，成功之后，状态变为发货审核
                break;
            case 4:
                //第三方仓库，可操作拦截
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
	        Service_OrderLog::add($logRow);	         
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
				/*'SKU1' => 'op_sku_1',
				'数量/Quantity 1' => 'op_quantity_1',
				'申报单价1' => 'op_price_1',
				'SKU2' => 'op_sku_2',
				'数量/Quantity 2' => 'op_quantity_2',
				'SKU3' => 'op_sku_3',
				'数量/Quantity 3' => 'op_quantity_3',
				'SKU4' => 'op_sku_4',
				'数量/Quantity 4' => 'op_quantity_4',
				'SKU5' => 'op_sku_5',
				'数量/Quantity 5' => 'op_quantity_5',
	    		'SKU6' => 'op_sku_6',
	    		'数量/Quantity 6' => 'op_quantity_6',
	    		'SKU7' => 'op_sku_7',
	    		'数量/Quantity 7' => 'op_quantity_7',
	    		'SKU8' => 'op_sku_8',
	    		'数量/Quantity 8' => 'op_quantity_8',
				'SKU9' => 'op_sku_9',
				'数量/Quantity 9' => 'op_quantity_9',
				'SKU10' => 'op_sku_10',
				'数量/Quantity 10' => 'op_quantity_10',*/
		);
		//ruston0903 sku上传种类添加
		for ( $i=1; $i <=73 ; $i++ )
		{ 
			$map['SKU'.$i] = 'op_sku_'.$i;
			$map['数量/Quantity '.$i] = 'op_quantity_'.$i;
			$map['申报单价'.$i] = 'op_price_'.$i;//ruston0926 申报价值
		}
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
								
								'consignee_county' => isset($v ['consignee_county'])?$v ['consignee_county']:'',
								'consignee_country_code' => $v ['consignee_country_code'],
								'consignee_country_name' => isset($v ['consignee_country_name'])?$v ['consignee_country_name']:'',
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
								$errs[$k][0]='sku:'.$op['sku'].'不存在';//ruston0903 判断sku是否存在
								throw new Exception(Ec::Lang('sku_not_exist',$op['sku']));
							}
							$product = $product[0];
							$order_product [] = array (
									'product_id' => $product['product_id'],
									'op_quantity' => $op['quantity'], 
									'parcel_declared_price' => $op['price'], //ruston0926 申报价值
									'parcel_declared_value' => $op['quantity']*$op['price'], //ruston0926 申报价值
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
     * 拦截申请
     * @param array $orderIds
     * @param int $status
     * @param int $reasonType
     * @param string $reason 
     * @return array
     */
    public static function orderCancelBatchTransaction($refIds,$status=7,$reasonType=4,$reason='拦截'){
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
                $result = self::orderCancel($refId, $status, $reasonType, $reason);                
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
     * 拦截申请
     * 发起拦截申请，如果wms未操作，返回成功，标记拦截成功
     * 如果仓库已经操作，返回成功，并返回警告信息，标记拦截申请为处理中,待wms回调标记拦截成功或者已出库
     * @param int $orderId
     * @param int $status
     * @param int $reasonType
     * @param string $reason 
     * @return array
     */
    public static function orderCancel($refId,$status=7,$reasonType=4,$reason='拦截'){
        $return = array(
            'ask' => 0,
            'message' => '',
            'ref_id' => $refId,
            'cancel_status'=>'0'
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
       
        try{
        	
        	// 存在借用产品时取消借用的销售数量
        	self::cancelUpdateBorrowInventoy($refId);
        	
            // 发送数据到WMS
            $apiService = new Common_ThirdPartWmsAPI();
            $rs = $apiService->cancelOrder($refId, $reason);
            if($rs['ask']!='Failure'){ // 拦截成功
                if($rs['waiting'] == '1'){
                    $return['cancel_status'] = 1;//拦截状态
                    $updateRow = array(
                        'cancel_status' => 1, // 订单拦截状态:0:无异常，1:拦截中,2:拦截成功,3:拦截失败
                        'abnormal_type' => $reasonType,
                        'abnormal_reason' => $reason
                    );
                }else{
                    $return['cancel_status'] = 2;//拦截状态
                    $updateRow = array(
                        'order_status' => '0',
                        'abnormal_type' => $reasonType,
                        'abnormal_reason' => $reason,
                        'cancel_status' => '2'
                    );
                }
                $updateRow['date_last_modify'] = date('Y-m-d H:i:s');
                // 更新内容
                if(! Service_Orders::update($updateRow, $refId, 'refrence_no_platform')){
                    throw new Exception(Ec::Lang('inner_error'), 50000);
                }
                $content = 'OMS拦截请求发送成功，WSM返回结果:' . $rs['message'];
                $logRow = array(
                    'ref_id' => $refId,
                    'log_content' => $content,
                    'op_id' => ''
                );
                Service_OrderProcess::writeOrderLog($logRow);
                
                $return['ask'] = 1;
                $return['message'] = 'Success';
                $db->commit();
            }else{//拦截失败
                throw new Exception(Ec::Lang('wms_error', $rs['message']));
            }
        }catch(Exception $e){
            $db->rollback();
            //拦截失败
            $updateRow = array(
                    'abnormal_type' => $reasonType,
                    'abnormal_reason' => $reason,
                    'cancel_status'=>3,
            );
            $return['cancel_status'] = 3;//拦截状态
            // 更新内容
            if(! Service_Orders::update($updateRow, $refId, 'refrence_no_platform')){
                throw new Exception(Ec::Lang('inner_error'),50000);
            }
            $content = '拦截失败,'.$e->getMessage();
            $logRow = array(
                    'ref_id' => $refId,
                    'log_content' => $content,
                    'op_id' => ''
            );
            Service_OrderProcess::writeOrderLog($logRow);
            $return['message'] = $e->getMessage();
            
        }
        return $return;
    }

    /**
     * 订单批量审核
     * @param unknown_type $orderIds
     * @param unknown_type $warehouseId
     * @param unknown_type $shippingMethod
     * @return multitype:Ambigous <multitype:number, multitype:number string NULL >
     */
    public static function orderVerifyBatch($refIds,$forceVerify=true,$warehouseId='',$shippingMethod=''){
        $return = array();
        $successCount = 0;
        $failCount = 0;
        $quehuoCount = 0;//缺货
        $fundCount = 0;//欠费
        $all = array();
        $successArr = array();
        $failArr = array();
        foreach($refIds as $refId){
            $result = self::orderVerifyTransaction($refId,$forceVerify,$warehouseId,$shippingMethod);
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
     * 发货审核,将订单信息发送到仓库系统
     * 判断当前状态，如果已经审核通过，直接返回，其他状态，抛出异常
     * 如果传递了$warehouseId和$shippingMethod，表示订单需要重新分仓
     * @param unknown_type $orderIds
     * @return multitype:number string NULL Ambigous <multitype:, mixed,
     *         multitype:number string NULL multitype:multitype:unknown
     *         multitype:unknown NULL Ambigous <multitype:, multitype:number
     *         string unknown Ambigous <string, unknown> > >
     */
    public static function orderVerifyTransaction($refId,$forceVerify=true,$warehouseId='',$shippingMethod='')
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
            $rs = self::orderVerify($refId,$forceVerify,$warehouseId,$shippingMethod);
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
            Service_OrderProcess::writeOrderLog($logRow);
        }
        return $return;
    }
    /**
     * 发货审核,将订单信息发送到仓库系统
     * 判断当前状态，如果已经审核通过，直接返回，其他状态，抛出异常
     * 如果传递了$warehouseId和$shippingMethod，表示订单需要重新分仓
     * @param unknown_type $orderIds
     * @return multitype:number string NULL Ambigous <multitype:, mixed,
     *         multitype:number string NULL multitype:multitype:unknown
     *         multitype:unknown NULL Ambigous <multitype:, multitype:number
     *         string unknown Ambigous <string, unknown> > >
     */
    public static function orderVerify($refId,$forceVerify=true,$warehouseId='',$shippingMethod='')
    {
    	$result = 0;
        $order = Service_Orders::getByField($refId, 'refrence_no_platform');
        
        if(empty($order)){
            throw new Exception(Ec::lang('order_not_exist'));
        }
        $allowArr = array(
            '2',
            '7'
        );
        if(! in_array($order['order_status'], $allowArr)){
            throw new Exception(Ec::lang('order_op_deny'));
        }
        
        $updateRow = array(
            'date_release' => date('Y-m-d H:i:s'),
            'date_last_modify' => date('Y-m-d H:i:s')
        );
        if(! empty($warehouseId) && ! empty($shippingMethod)){ // 重新分配仓库
            $warehouse = Service_Warehouse::getByField($warehouseId, 'warehouse_id');
            $updateRow['warehouse_id'] = $warehouseId;
            $updateRow['warehouse_code'] = $warehouse['warehouse_code'];
            $updateRow['shipping_method'] = $shippingMethod;
            
            // 赋值
            $order['warehouse_id'] = $warehouseId;
            $order['warehouse_code'] = $warehouse['warehouse_code'];
            $order['shipping_method'] = $shippingMethod;
        }
        
        if(empty($order['warehouse_code'])){
            throw new Exception(Ec::lang('warehouse_code_empty'));
        }
        if(empty($order['shipping_method'])){
            throw new Exception(Ec::lang('shipping_method_empty'));
        }
        // 更新内容
        if(! Service_Orders::update($updateRow, $refId, 'refrence_no_platform')){
            throw new Exception(Ec::Lang('inner_error'));
        }
        
        // 更新借用库存
        self::updateBorrowInventoy($order['order_id'], $order['refrence_no_platform'], $order['company_code'], $order['warehouse_id']);
        
        $forceVerify = $forceVerify?1:0;
        // 发送数据到WMS
        $apiService = new Common_ThirdPartWmsAPI();
        $rs = $apiService->createOrder($refId,$forceVerify);
        if($rs['ask']!='Failure'){
        	if($rs['orderStatus']=='4'){//4正常，3:有异常
        		$updateRow = array(
        				'order_status' => '3'
        		);
        		$result = 1;
        	}else{
        		$updateRow = array(
        				'order_status' => '6'
        		);
        		if(isset($rs['abnormalStatus'])){
        			$updateRow['sub_status'] = $rs['abnormalStatus'];//1:费用不足，2:库存不足
        		}
        		if($rs['abnormalStatus']=='1'){
        			$result = 2;
        		}else{
        			$result = 3;
        		}
        		
        	}
            
            // 更新内容
            if(!Service_Orders::update($updateRow, $refId, 'refrence_no_platform')){
                throw new Exception(Ec::Lang('inner_error'));
            }
            
            //同步检查订单是否需要更新平台订单
            //$updatePlatformRow = Service_Orders::getByField($order['order_id']);
            //Service_OrderPlatformProcess::updateOrdersPlatform($refId, $updatePlatformRow);

            //更新费用
            self::updateOrderFee($refId, $rs['data']);
            /*
			//================================费用 start
            // 费用
            Service_OrderFee::delete($refId, 'ref_id');
            //订单费用
            $orderFeeSummery = array (
            		'ship_cost' => 0,
            		'op_cost' => 0,
            		'fuel_cost' => 0,
            		'register_cost' => 0,
            		'tariff_cost' => 0,
            		'incidental_cost' => 0,
            		'warehouse_cost' => 0,
            );
            foreach($rs['data']['orderFee'] as $fee){
            	$feeRow = array(
            			'ref_id' => $refId,
            			'customer_code' => $order['company_code'],
            			'cs_code' => $fee['cs_code'],
            			'ft_code' => $fee['ft_code'],
            			'bi_amount' => $fee['bi_amount'],
            			'currency_code' => $fee['currency_code'],
            			'currency_rate' => $fee['currency_rate'],
            			'bi_sp_type' => $fee['bi_sp_type'],
            			'bi_creator_id' => $fee['bi_creator_id'],
            			'bi_balance_sign' => $fee['bi_balance_sign'],
            			'bi_writeoff_sign' => $fee['bi_writeoff_sign'],
            			'bi_credit_pay' => $fee['bi_credit_pay'],
            			'bi_note' => $fee['bi_note'],
            			'bi_billing_date' => $fee['bi_billing_date']
            	);
            	Service_OrderFee::add($feeRow);
            
            	switch ($fee['ft_code']){
            		case 'shipping' :
            			$orderFeeSummery['ship_cost'] = $fee['bi_amount'];
            			break;
            
            		case 'opByWeight' :
            			$orderFeeSummery['op_cost'] += empty($orderFeeSummery['op_cost'])?0:$fee['bi_amount'];
            			break;
            		case 'opByPiece' :
            			$orderFeeSummery['op_cost'] += empty($orderFeeSummery['op_cost'])?0:$fee['bi_amount'];
            			break;
            	}
            	//                     print_r($feeRow);exit;
            }
            $orderFeeSummery['customer_code'] = $order['company_code'];
            $orderFeeSummery['shipping_method'] = $order['shipping_method'];
            $orderFeeSummery['order_weight'] = $rs['data']['charged_weight'];
            $orderFeeSummery['country_code'] = $order['consignee_country_code'];
            //费用更新
            $feeExist = Service_OrderFeeSummery::getByField($refId,'ref_id');
            if($feeExist){
            	Service_OrderFeeSummery::update($orderFeeSummery,$refId,'ref_id');
            }else{
            	$orderFeeSummery['ref_id']=$refId;
            	Service_OrderFeeSummery::add($orderFeeSummery);
            }
            //==============================费用 end
            */
            $content = '订单审核Success';
            // 这里还有日志信息，以后添加
            $logRow = array(
                'ref_id' => $refId,
                'log_content' => $content,
                'op_id' => ''
            );
            Service_OrderProcess::writeOrderLog($logRow);
        }else{
            throw new Exception(Ec::Lang('wms_error', $rs['message']));
        }
        return $result;
    }
    
    /**
     * 更新产品库存数据
     * 订单审核，拦截，下架，出库，入库，上架，QC
     * @param unknown_type $productBarcode
     * @param unknown_type $warehouseCode
     */
    public function updateInventory($productBarcode,$warehouseCode){
        $con = array('product_barcode'=>$productBarcode,'warehouse_code'=>$warehouseCode);
        $inventory = Service_ProductInventory::getByCondition($con);
        if($inventory){
            $inventory = $inventory[0];
        }else{
            
        }
    }


    /**@desc 更新订单费用(创建订单、获取订单费用、共用此方法)
     * @param string $orderCode
     * @param array $orderFeeArr
     */
    public static function updateOrderFee($orderCode = '', $orderFeeArr = array())
    {
        $orderRow = Service_Orders::getByField($orderCode, 'refrence_no_platform');
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
                'ref_id' => $orderRow['refrence_no_platform'],
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
        $orderFeeSummery['country_code'] = $orderRow['consignee_country_code'];
        //费用更新
        $feeExist = Service_OrderFeeSummery::getByField($orderRow['refrence_no_platform'], 'ref_id');
        if ($feeExist) {
            Service_OrderFeeSummery::update($orderFeeSummery, $orderRow['refrence_no_platform'], 'ref_id');
        } else {
            $orderFeeSummery['ref_id'] = $orderRow['refrence_no_platform'];
            Service_OrderFeeSummery::add($orderFeeSummery);
        }
    }

    /**
     * 审核订单时： 更新借用库存处理，当订单产品为借用库存时，更新借用库存数据
     * @param unknown_type $orderId
     */
    private static function updateBorrowInventoy($orderId = '', $orderCode = '', $companyCode = '', $warehouseId = '') {
    	$con = array(
    		'order_id' => $orderId
    	);
    	$detail = Service_OrderProduct::getByCondition($con);
    	
    	foreach($detail as $p) {
    		
    		$product = Service_Product::getByField($p['product_barcode'],'product_barcode');
    		
    		// 判断判断是否借用产品,非借用产品直接跳出不处理
    		if($product['company_code'] == $companyCode) {
    			continue;
    		}
    		
    		// 更新借用库存
    		$borrowRow = array(
    				'product_id' => $product['product_id'],
    				'quantity' => $p['op_quantity'],
    				'company_code' => $companyCode,
    				'operationType' => "2",
    				'warehouse_id' => $warehouseId,
    				'application_code' => 'auditOrder', //操作类型
    				'reference_no' => $orderCode, // 单号
    		);
    		
    		$borrowInventory = new Process_BorrowInventory();
    		$result = $borrowInventory->update($borrowRow);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    		
    		// 更新原分享库存
    		$row = array(
    				'product_id' => $product['product_id'],
    				'quantity' => $p['op_quantity'],
    				'operationType' => "5",
    				'warehouse_id' => $warehouseId,
    				'application_code' => 'auditOrder', 	//操作类型
    				'reference_no' => $orderCode, 	// 单号
    		);
    		 
    		$shareInventory = new Process_ShareInventory();
    		$result = $shareInventory->update($row);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    	}
    }

    /**
     * 取消订单时： 更新借用库存处理，当订单产品为借用库存时，更新借用库存数据
     * @param unknown_type $refId
     */
    private static function cancelUpdateBorrowInventoy($refId) {
    	// 获取订单
    	$order = Service_Orders::getByField($refId, 'refrence_no_platform');
    	
    	$con = array(
    			'order_id' => $order['order_id']
    	);
    	$detail = Service_OrderProduct::getByCondition($con);
    	 
    	//     	$borrowRowArr = array();
    	 
    	foreach($detail as $p) {
    
    		$product = Service_Product::getByField($p['product_barcode'],'product_barcode');
    
    		// 判断判断是否借用产品,非借用产品直接跳出不处理
    		if($product['company_code'] == $order['company_code']) {
    			continue;
    		}
    
    		// 更新借用库存
    		$borrowRow = array(
    				'product_id' => $product['product_id'],
    				'quantity' => $p['op_quantity'],
    				'company_code' => $order['company_code'],
    				'operationType' => "3", // 取消订单
    				'warehouse_id' => $order['warehouse_id'],
    				'application_code' => 'cancelOrder', //操作类型
    				'reference_no' => $order['order_code'], // 单号
    		);
    
    		//     		$borrowRowArr[] = $borrowRow;
    		 
    		$borrowInventory = new Process_BorrowInventory();
    		$result = $borrowInventory->update($borrowRow);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    
    		// 更新原分享库存
    		$row = array(
    				'product_id' => $product['product_id'],
    				'quantity' => $p['op_quantity'],
    				'operationType' => "6",// 取消订单
    				'warehouse_id' => $order['warehouse_id'],
    				'application_code' => 'cancelOrder', 	//操作类型
    				'reference_no' => $order['order_code'], 	// 单号
    		);
    		 
    		$shareInventory = new Process_ShareInventory();
    		$result = $shareInventory->update($row);
    		if($result['state'] == 0) {
    			throw new Exception($result['error'][0]['errorMsg']);
    		}
    	}
    	 
    	//     	print_r($borrowRowArr);die;
    }
}