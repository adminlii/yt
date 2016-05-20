<?php
/**
 * 与第三方仓库对接
 * @author Administrator
 */
class Common_ThirdPartWmsAPIProcess
{
    /**
     * 去除前后空格
     * @param unknown_type $v
     * @return unknown|string
     */
    private function arr_trim($v){
        if(!is_array($v)){
            return $v;
        }
        foreach($v as $kk=>$vv){
            $v[$kk] = trim($vv);
        }
        return $v;
    }
    /**
     * 同步国家到OMS
     */
    public function syncCountry()
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getCountry();
        Ec::showError(print_r($result,true),'_sync_country');
        
        if($result['ask'] == 'Success'){        
            Service_Country::delete('1', '1');
            foreach($result['data'] as $v){
                try{         
                    $v = $this->arr_trim($v);        
                    Service_Country::add($v);                    
                }catch(Exception $e){
                    log($e->getMessage());
                    throw new Exception(print_r($v,true));
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
    }
    /**
     * 同步揽收地址
     */
    public function syncReceivingArea()
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
    
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getReceivingArea();
    
        if($result['ask'] == 'Success'){
            Service_ReceivingAreaMap::delete('1', '1');
            foreach($result['data'] as $v){
                try{
                    $v = $this->arr_trim($v);  
                    Service_ReceivingAreaMap::add($v);
                }catch(Exception $e){
                    log($e->getMessage());
                    throw new Exception(print_r($v,true));
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
    }
    
    
    /**
     * 同步品类到OMS
     */
    public function syncCategory()
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
    
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getCategory();
//         print_r($result);exit;
        if($result['ask'] == 'Success'){
            Service_ProductCategory::delete('1', '1');
            foreach($result['data'] as $v){
                try{
                    $v = $this->arr_trim($v);  
                    Service_ProductCategory::add($v);
                }catch(Exception $e){
                    log($e->getMessage());
                    throw new Exception(print_r($v,true));
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
    }
    /**
     * 同步费用类型到OMS
     */
    public function syncFeeType()
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
    
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getFeeType();
        //         print_r($result);exit;
        if($result['ask'] == 'Success'){
            Service_FeeType::delete('1', '1');
            foreach($result['data'] as $v){
                try{
                    $v = $this->arr_trim($v);  
                    Service_FeeType::add($v);
                }catch(Exception $e){
                    log($e->getMessage());
                    throw new Exception(print_r($v,true));
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
    }
    
    /**
     * 同步订单操作节点到OMS
     */
    public function syncOrderOperationType()
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
    
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getOrderOperationType();
        //         print_r($result);exit;
        if($result['ask'] == 'Success'){
            Service_OrderOperationType::delete('1', '1');
            foreach($result['data'] as $v){
                try{
                    $v = $this->arr_trim($v);  
                    Service_OrderOperationType::add($v);
                }catch(Exception $e){
                    log($e->getMessage());
                    throw new Exception(print_r($v,true));
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
    }
    
    /**
     * 同步仓库到OMS
     */
    public function syncWarehouse()
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
    
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getWarehouse();
        Ec::showError(print_r($result,true),'_sync_warehouse');
        if($result['ask'] == 'Success'){
            Service_Warehouse::delete('1', '1');
            foreach($result['data'] as $v){
                try{
                    $v = $this->arr_trim($v);  
                    Service_Warehouse::add($v);
                }catch(Exception $e){
                    log($e->getMessage());
                    throw new Exception(print_r($e->getMessage(), true));
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
    }
    /**
     * 同步产品单位到OMS
     */
     public function syncProductUom(){

         $return = array(
                 'ask' => 0,
                 'message' => ''
         );
         
         $process = new Common_ThirdPartWmsAPI();
         $result = $process->getProductUom();
         if($result['ask'] == 'Success'){
             Service_ProductUom::delete('1', '1');
             foreach($result['data'] as $v){
                 try{
                    $v = $this->arr_trim($v);  
                     Service_ProductUom::add($v);
                 }catch(Exception $e){
                     log($e->getMessage());
                     throw new Exception(print_r($e->getMessage(), true));
                 }
             }
             $return['ask'] = 1;
             $return['message'] = 'Success';
         }else{
             $return['message'] = $result['message'];
         }
         return $return;
         
         
     }
     /**
      * 同步产品质检项到OMS
      */
     public function syncQcOption(){

         $return = array(
                 'ask' => 0,
                 'message' => ''
         );
         
         $process = new Common_ThirdPartWmsAPI();
         $result = $process->getQcOption();
         
         if($result['ask'] == 'Success'){
             Service_ProductQcOptions::delete('1', '1');
             foreach($result['data'] as $v){
                 try{
                    $v = $this->arr_trim($v);  
                     Service_ProductQcOptions::add($v);
                 }catch(Exception $e){
                     log($e->getMessage());
                     throw new Exception(print_r($e->getMessage(), true));
                 }
             }
             $return['ask'] = 1;
             $return['message'] = 'Success';
         }else{
             $return['message'] = $result['message'];
         }
         return $return;
         
     }

    /**
     * WMS同步仓库运输方式到OMS
     */
    public function syncWarehouseShipment()
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getWareouseShipment();

        if ($result['ask'] == 'Success') {
            $db = Common_Common::getAdapter();
            $db->beginTransaction();
            try {
                Service_ShippingMethod::delete('1', '1');
                foreach ($result['data'] as $v) {
                    $setting = $v['settings'];
                    unset($v['settings']);
                    $map = $v['map'];
                    unset($v['map']);
                    $v = $this->arr_trim($v);
                    $smArr = array(
                        'sm_id' => $v['sm_id'],
                        'sm_code' => $v['sm_code'],
                        'sm_short_name' => $v['sm_short_name'],
                        'sm_name_cn' => $v['sm_name_cn'],
                        'sm_type' => $v['sm_type'],
                        'sm_status' => $v['sm_status'],
                        'sm_delivery_time_min' => $v['sm_delivery_time_min'],
                        'sm_delivery_time_max' => $v['sm_delivery_time_max'],
                        'sm_delivery_time_avg' => $v['sm_delivery_time_avg'],
                    );
                    Service_ShippingMethod::add($smArr);
                    Service_ShippingMethodSettings::delete($v['sm_id'], 'sm_id');
                    foreach ($setting as $vv) {
                        $vv = $this->arr_trim($vv);
                        $smsArr = array(
                            'sm_id' => $vv['sm_id'],
                            'warehouse_id' => $vv['warehouse_id'],
                            'sms_id' => $vv['sms_id'],
                            'sms_status' => $vv['sms_status'],
                        );
                        Service_ShippingMethodSettings::add($smsArr);
                    }
                    /*
                    Service_SmAreaMap::delete($v['sm_id'], 'sm_id');
                    foreach ($map as $vv) {
                        $vv = $this->arr_trim($vv);
                        Service_SmAreaMap::add($vv);
                    }
                    */
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                log($e->getMessage());
                throw new Exception(print_r($e->getMessage(), true));
            }
        } else {
            $return['message'] = $result['message'];
        }
        // 缓存清理
//         Common_DataCache::getShipTypeCountryMap(1);
        return $return;
    }

    /**
     * 同步客户信息到wms
     */
    public function createAllCustomer()
    {
        $customers = Service_Company::getAll();
        $result = array();
        foreach($customers as $c){
            $process = new Common_ThirdPartWmsAPI();
            $rs = $process->createCompany($c['company_code']);
            $this->log($rs['ask'] . ' ' . $rs['message']);
            $result[] = $rs;
        }
        
        return $result;
    }

    /**
     * 同步产品信息到wms
     */
    public function createAllProduct()
    {
        $products = Service_Product::getAll();
        $result = array();
        foreach($products as $product){
            $process = new Common_ThirdPartWmsAPI();
            $rs = $process->createProduct($product['product_id']);
            $this->log($rs['ask'] . ' ' . $rs['message']);
            $result[] = $rs;
        }
        
        return $result;
    }
    /**
     * 从wms更新产品信息到OMS
     */
    public function syncProduct($barcode){
    
        $return = array(
                'ask' => 0,
                'message' => ''
        );
         
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getProduct($barcode);
        if($result['ask'] == 'Success'){
            $product = $result['data'];
            $updateRow = array(
                'product_length' => $product['product_length'],
                'product_width' => $product['product_width'],
                'product_height' => $product['product_height'],
                'product_net_weight' => $product['product_net_weight'],
                'product_weight' => $product['product_weight']
            );
            Service_Product::update($updateRow, $barcode,'product_barcode');
            
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
         
         
    }
    public function syncOrder($refId){
        $wmsService = new Common_ThirdPartWmsAPI();
        $rs = $wmsService->getOrder($refId);
//         print_r($rs);exit;
        Ec::showError(print_r($rs,true),'__getOrder');
        if($rs['ask']!='Failure'){
            $rs = $rs['data'];
//             [order_status] => 8
//             [problem_status] => 0
//             [underreview_status] => 0//1:欠费 2：缺货
//             [intercept_status] => 0
//             [sync_cost_status] => 0
//             [sync_status] => 0
//             [order_waiting_status] => 0
//             [order_picking_status] => 0
                // 出库状态
            $shipStatusArr = array(
                '8',
                '9',
                '10',
                '11',
                '12',
                '13'
            );

            if($rs['order_status']>=4&&$rs['order_status']<8){//待出库
            	//状态
            	$updateRow = array(
            			'order_status' => '3',
            			'sub_status'=>'0',
            	);
            	Service_Orders::update($updateRow, $refId,'refrence_no_platform');
            }
            if(in_array($rs['order_status'],$shipStatusArr)){
                //状态
                $updateRow = array(
                        'order_status' => '4',
                        'shipping_method_no' => $rs['ship_order']['tracking_number'],
                        'order_weight' => $rs['ship_order']['so_weight'],
                );
                
                if(isset($rs['order_operation_time']['ship_time'])){
                	$updateRow['date_warehouse_shipping'] = $rs['order_operation_time']['ship_time'];
                }
                Service_Orders::update($updateRow, $refId,'refrence_no_platform');
                    
            }

            if($rs['order_status']=='0'||$rs['order_status']==0){//作废
            	//状态
            	$updateRow = array(
            			'order_status' => '0',
            			'sub_status'=>'0',
            	);
            	Service_Orders::update($updateRow, $refId,'refrence_no_platform');
            }
            //更新费用
            Service_OrderProcess::updateOrderFee($refId, $rs);
            /*
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
            foreach($rs['order_fee'] as $fee){
            	$feeRow = array(
            			'ref_id' => $rs['order_code'],
            			'customer_code' => $rs['customer_code'],
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
                $feeRow = $this->arr_trim($feeRow);  
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
            $order = Service_Orders::getByField($rs['order_code'],'refrence_no_platform');
            $orderFeeSummery['customer_code'] = $order['company_code'];
            $orderFeeSummery['shipping_method'] = $order['shipping_method'];
            $orderFeeSummery['order_weight'] = $rs['charged_weight'];
            $orderFeeSummery['country_code'] = $order['consignee_country_code'];
            $orderFeeSummery['date_release'] = $order['date_release'];//审核时间
            $orderFeeSummery['order_status'] = $order['order_status'];//订单当前状态            
            
            //费用更新
            $feeExist = Service_OrderFeeSummery::getByField($rs['order_code'],'ref_id');
            if($feeExist){
            	Service_OrderFeeSummery::update($orderFeeSummery,$rs['order_code'],'ref_id');
            }else{
            	$orderFeeSummery['ref_id']=$rs['order_code'];
            	Service_OrderFeeSummery::add($orderFeeSummery);
            }
            */


            //物流轨迹
            if(!empty($rs['track_info'])){
                Service_ShipTrackInfo::delete($rs['order_code'],'order_code');
                foreach($rs['track_info'] as $track){
                    $exist = Service_ShipTrackInfo::getByField($track['sti_id'],'sti_id');
                    if($exist){
                        $sti_id = $track['sti_id'];
                        unset($track['sti_id']);
                        Service_ShipTrackInfo::update($track,$sti_id,'sti_id');
                    }else{
                        Service_ShipTrackInfo::add($track);
                    }
                }
            }
            //签收信息
            if(!empty($rs['track_pod'])){
                Service_ShipTrackPod::delete($rs['order_code'],'order_code');
                foreach($rs['track_pod'] as $pod){
                    $exist = Service_ShipTrackPod::getByField($pod['stp_id'],'stp_id');
                    if($exist){
                        $stp_id = $pod['stp_id'];
                        unset($pod['stp_id']);
                        Service_ShipTrackPod::update($pod,$stp_id,'stp_id');
                    }else{
                        Service_ShipTrackPod::add($pod);
                    }
                    
                }
            }
            //库存同步
            foreach($rs['order_product'] as $p){
            	$obj = array(
            			'company_code' => $rs['customer_code'],
            			'warehouse_code' => $wmsService->getWarehouseCode($rs['warehouse_id']),
            			'product_sku' => $wmsService->getProductSku($p['product_barcode']),
            	);
            	$this->syncInventory($obj);
            }
            
            $con = array(
                'ref_id' => $rs['order_code'],
                'system' => 'wms'
            );
            $logRows = Service_OrderLog::getByCondition($con);
            foreach($logRows as $v){
                Service_OrderLog::delete($v['log_id'],'log_id');
            }
            $opType = Service_OrderOperationType::getAll();
            foreach($opType as $k=>$v){
                unset($opType[$k]);                
                $opType[$v['oot_code']] = $v;
            }
            //操作节点
            $nodes = $rs['order_operation_node'];
            foreach($nodes as $node){
                $logRow = array(
                    'ref_id' => $rs['order_code'],
                    'log_content' => $opType[$node['oot_code']]['oot_name'].' At: '.$node['oon_add_time'],
                    'create_time' => date('Y-m-d H:i:s'),
                    'system' => 'wms',
                    'op_id'=>'0',
                    'op_user_id'=>'0'
                );                
                Service_OrderLog::add($logRow);
            }
        }
    }
    /**
     * 物流轨迹，签收信息
     * @param unknown_type $refId
     */
    public static function syncOrderTrack($refId){
        $wmsService = new Common_ThirdPartWmsAPI();
        $rs = $wmsService->getOrderTrack($refId);
        if($rs['ask']!='Failure'){
            Service_ShipTrackInfo::delete($refId,'order_code');
            Service_ShipTrackPod::delete($refId,'order_code');
            $trackingInfos = $rs['data']['track_info'];
            $trackingPods = $rs['data']['track_pod'];
            foreach($trackingInfos as $row){
                unset($row['sti_id']);
                Service_ShipTrackInfo::add($row);
            }
            foreach($trackingPods as $row){
                unset($row['stp_id']);
                Service_ShipTrackPod::add($row); 
            }  
        }   
//         print_r($rs);exit;  
        return $rs;
    }
    /**
     * 库存同步到OMS
     */
    public function syncReceiving($receiving_code)
    {
        $wmsService = new Common_ThirdPartWmsAPI();
        
        $rs = $wmsService->getAsn($receiving_code);
        if($rs['ask']!='Failure'){
            $rs = $rs['data'];
//             print_r($rs);exit;
            // 更新asn
            $updateRow = array(
                    'receiving_status' => $rs['receiving_status'],
                    'receiving_transfer_status' => $rs['receiving_transfer_status'],
                    'receiving_exception' => $rs['receiving_exception'],
                    'receiving_exception_handle' => $rs['receiving_exception_handle']
            );
            Service_Receiving::update($updateRow, $rs['receiving_code'],'receiving_code');
            Ec::showError(print_r($updateRow,true),'create_asn_to_wms_update_');
            //明细处理
            foreach($rs['receiving_detail'] as $p){
                // 更新asn明细，                 
                $con = array('receiving_code'=>$rs['receiving_code'],'product_barcode'=>$p['product_barcode']);
                $exist = Service_ReceivingDetail::getByCondition($con);
                $exist = $exist[0];
                $updateRow = array(
                        'rd_received_qty' => $p['rd_received_qty'],
                        'rd_putaway_qty' => $p['rd_putaway_qty'],
                        'rd_transfer_status'=>$p['rd_transfer_status'],
                        'receiving_exception'=>$p['receiving_exception'],
                        'receiving_exception_handle'=>$p['receiving_exception_handle'],
                        'exception_process_instruction'=>$p['exception_process_instruction'],
                        'rd_status'=>$p['rd_status'],
                        'rd_update_time'=>date('Y-m-d H:i:s')
                );
                Service_ReceivingDetail::update($updateRow, $exist['rd_id'],'rd_id');
                //更新库存
                $customer_code = $rs['customer_code'];
                $warehouse_code = $wmsService->getWarehouseCode($rs['warehouse_id']);
                $product_sku = $wmsService->getProductSku($p['product_barcode']);
                $invParam = array(
                        'company_code' => $customer_code,
                        'warehouse_code' => $warehouse_code,
                        'product_sku' => $product_sku,
                );
                $subrs = $this->syncInventory($invParam);
//                 print_r($subrs);
            }
            Service_ReceivingDetailBatch::delete($rs['receiving_code'],'receiving_code');
            if(!empty($rs['receiving_detail_batch'])){
                //明细处理
                foreach($rs['receiving_detail_batch'] as $p){
                    $d = array(
                        'rdb_id' => $p['rdb_id'],
                        'receiving_id' => $p['receiving_id'],
                        'qc_code' => $p['qc_code'],
                        'receiving_code' => $p['receiving_code'],
                        'receiving_line_no' => $p['receiving_line_no'],
                        'product_barcode' => $p['product_barcode'],
                        'product_id' => $p['product_id'],
                        'rdb_weight' => $p['rdb_weight'],
                        'rdb_putaway_qty' => $p['rdb_putaway_qty'],
                        'rdb_received_qty' => $p['rdb_received_qty'],
                        'packaged' => $p['packaged'],
                        'non_packaged_qty' => $p['non_packaged_qty'],
                        'labeled' => $p['labeled'],
                        'non_labeled_qty' => $p['non_labeled_qty'],
                        'rdb_note' => $p['rdb_note'],
                        'receiving_user_id' => $p['receiving_user_id'],
                        'rdb_add_time' => $p['rdb_add_time'],
                        'rdb_update_time' => $p['rdb_update_time']
                    );
                    Service_ReceivingDetailBatch::add($d);
                } 
            }
            
            Service_ReceivingOperationNode::delete($rs['receiving_code'],'receiving_code');
            foreach($rs['receiving_operation_node'] as $v){
                Service_ReceivingOperationNode::add($v);
            }
        }
        return $rs;
    }
    
    /**
     * 库存同步到OMS
     */
    public function syncInventory($obj)
    {
        $process = new Common_ThirdPartWmsAPI();
        $page = 1;        
        while(true){
            $param = array(
                    'customer_code' => $obj['company_code'],
                    'warehouse_code' => empty($obj['warehouse_code'])?'':$obj['warehouse_code'],
                    'sku' => empty($obj['product_sku'])?'':$obj['product_sku'],
                    'page'=>$page,
                    'pageSize'=>200,
            );
            $result = $process->getInventory($param);
            if($result['ask'] == 'Success'){
                if(!empty($result['data'])){
                    foreach($result['data'] as $v){
                        try{
                            $con = array(
                                    'product_id' => $process->getProductId($v['product_barcode']),
                                    'warehouse_id' => $v['warehouse_id']
                            );
                            $v['product_id'] = $con['product_id'];
                    
                            $row = array(
                                    'company_code' => $v['customer_code'],
                                    'product_sku' => $process->getProductSku($v['product_barcode']),
                                    'product_barcode' => $v['product_barcode'],
                                    'customer_id' => '',
                                    'product_id' => $v['product_id'],
                                    'warehouse_id' => $v['warehouse_id'],
                                    'warehouse_code' => $v['warehouse_code'],
                                    'pi_planned' => $v['pi_planned'],
                                    'pi_onway' => $v['pi_onway'],
                                    'pi_pending' => $v['pi_pending'],
                                    'pi_sellable' => $v['pi_sellable'],
                                    'pi_unsellable' => $v['pi_unsellable'],
                                    'pi_reserved' => $v['pi_reserved'],
                                    'pi_shipped' => $v['pi_shipped'],
                                    'pi_hold' => $v['pi_hold'],
                                    'pi_no_stock' => $v['pi_no_stock'],
                                    'pi_warning_qty' => $v['pi_warning_qty'],
                                    'buyer_id' => '0',
                                    'pi_add_time' => $v['pi_add_time'],
                                    'pi_update_time' => date('Y-m-d H:i:s'),
                            		'pi_shared' => $v['pi_shared'],
                            );
                    
                            $inventory = Service_ProductInventory::getByCondition($con);
                            if($inventory){
                                $inventory = $inventory[0];
                                Service_ProductInventory::update($row, $inventory['pi_id'], 'pi_id');
                            }else{
                                Service_ProductInventory::add($row);
                            }
                        }catch(Exception $e){
                            log($e->getMessage());
                        }
                    } 
                }else{
                    break;//跳出循环
                }                
            }else{
                log($result['message']);
                break;//跳出循环
            }
            $page++;
        }
        
        return $result;
    }

    /**
     * 库存同步到OMS
     */
    public function syncAllInventory()
    {
        $db = Zend_Registry::get('db');
        $table = 'cron_sync_inventory';
        // 临时表 start
        $sql = "
        CREATE TABLE if not exists `{$table}` (
         id int not null auto_increment,
        `company_code` varchar(64) NOT NULL COMMENT '',
        `warehouse_code` varchar(64) NOT NULL COMMENT '',
        `product_barcode` varchar(64) NOT NULL COMMENT '',
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='需要更新的订单费用';
        ";
        $db->query($sql);
        // 临时表 end
        // 当临时表没数据 插入需要更新的订单号 start
        $sql = "select count(*) from {$table}";
        $count = $db->fetchOne($sql);
        if($count <= 0){
            $sqls = array();
            $sqls = array();
            $customers = Service_Company::getByCondition(array(), 'company_code');
            $warehouses = Service_Warehouse::getByCondition(array(), 'warehouse_name');
            foreach($customers as $c){
                foreach($warehouses as $w){
                    $sqls[] = "insert into {$table}(company_code,warehouse_code) values('{$c['customer_id']}','{$w['warehouse_name']}');";
                }
            }
            foreach($sqls as $sql){
                $db->query($sql);
            }
        }
        // 当临时表没数据 插入需要更新的订单号 end
        
        $process = new Common_ThirdPartWmsAPI();
        while(true){
            $db = Zend_Registry::get('db');
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            $msg = $count . " item need update";
            self::log($msg);
            // 随机取一条，避免在多任务运行时同时取得同一条记录
            $sql = "select * from {$table} order by RAND() limit 1";
            $obj = $db->fetchRow($sql);
            if($obj){
                $param = array(
                    'company_code' => $obj['company_code'],
                    'warehouse_code' => $obj['warehouse_code']
                );
                $result = $process->getInventory($param);
                if($result['ask'] == 'Success'){
                    foreach($result['data'] as $v){
                        try{
                            $v['customer_id'] = $this->customerAuth->customer['customer_id'];
                            $con = array(
                                'product_id' => $process->getProductId($v['product_barcode']),
                                'warehouse_id' => $process->getWarehouseId($v['warehouse_code'])
                            );
                            $inventory = Service_ProductInventory::getByCondition($con);
                            $v['product_id'] = $con['product_id'];
                            $v['warehouse_id'] = $con['warehouse_id'];
                            $v['product_sku'] = $process->getProductSku($v['product_barcode']);
                            unset($v['product_barcode']);
                            unset($v['warehouse_code']);
                            
                            if($inventory){
                                $inventory = $inventory[0];
                                Service_ProductInventory::update($v, $inventory['pi_id'], 'pi_id');
                            }else{
                                Service_ProductInventory::add($v);
                            }
                        }catch(Exception $e){
                            log($e->getMessage());
                        }
                    }
                }else{
                    log($result['message']);
                }
            }else{
                break;
            }
        }
    }

    /**
     * 同步订单费用到OMS
     */
    public function syncOrderFee()
    {
        $db = Zend_Registry::get('db');
        $table = 'cron_sync_order_fee';
        // 临时表 start
        $sql = "
            CREATE TABLE if not exists `{$table}` (
            `refrence_no_platform` varchar(64) NOT NULL COMMENT '',
            PRIMARY KEY (`refrence_no_platform`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='需要更新的订单费用';
            ";
        $db->query($sql);
        // 临时表 end
        // 当临时表没数据 插入需要更新的订单号 start
        $sql = "select count(*) from {$table}";
        $count = $db->fetchOne($sql);
        if($count <= 0){
            $sqls = array();
            $sqlArr = array();
            $sqlArr[] = 'SELECT refrence_no_platform  FROM `orders` where 1=1;';
            foreach($sqlArr as $sql){
                self::log($sql);
                $itemIds = $db->fetchAll($sql);
                foreach($itemIds as $v){
                    $sqls[] = "replace into {$table}(refrence_no_platform) values('{$v['refrence_no_platform']}');";
                }
            }
            foreach($sqls as $sql){
                $db->query($sql);
            }
        }
        // 当临时表没数据 插入需要更新的订单号 end
        
        $process = new Common_ThirdPartWmsAPI();
        while(true){
            $db = Zend_Registry::get('db');
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            $msg = $count . "  need update";
            self::log($msg);
            // 随机取一条，避免在多任务运行时同时取得同一条记录
            $sql = "select * from {$table} order by RAND() limit 1";
            $obj = $db->fetchRow($sql);
            if($obj){
                $sql = "delete from {$table} where refrence_no_platform='{$obj['refrence_no_platform']}';";
                self::log($sql);
                $db->query($sql);
                
                $rs = $process->getOrderFee($obj['refrence_no_platform']);
                if($rs['ask'] == 'Success'){
                    // 费用记录
                    // -------------------------------------------------------------
                    print_r($rs);
                    exit();
                }
            }else{
                break;
            }
        }
    }


    /**
     * 同步客户状态
     */
    public function syncCompany($companyCode)
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        $param = array(
            'customer_code' => $companyCode,
        );
        $process = new Common_ThirdPartWmsAPI();
        $result = $process->getCompany($companyCode);
        $return['result'] = $result;
        
        if($result['ask'] == 'Success'){
            $row = array(
                'company_status'=>$result['data']['customer_status'],
            	'company_update_time'=>date('Y-m-d H:i:s')
            );
            $result_company_update = Service_Company::update($row,$companyCode,'company_code');
            $row_user = array(
            		'user_update_time'=>date('Y-m-d H:i:s')
            		);
            if($result['data']['customer_status'] == 2){
            	$row_user['user_status'] = 1;
            }else{
            	$row_user['user_status'] = 2;
            }
            $result_user = Service_User::getByCondition(array('company_code'=>$companyCode,'is_admin'=>1));
            if(!empty($result_user)){
            	Service_User::update($row_user, $result_user[0]['user_id']);
            	$userId = $result_user[0]['user_id'];
            	//权限初始化
            	$rights = Service_UserRight::getAll();
            	Service_UserRightMap::delete($userId,'user_id');
            	foreach($rights as $r){
            		$row = array('ur_id'=>$r['ur_id'],'user_id'=>$userId);
            		Service_UserRightMap::add($row);
            	}
            }
            if(!empty($result['data']['customer_balance'])){
                $balance = $result['data']['customer_balance'];
                Service_CustomerBalance::delete($balance['cb_id'],'cb_id');
                Service_CustomerBalance::add($balance);
            }            
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }else{
            $return['message'] = $result['message'];
        }
        return $return;
    }

    /**
     * 同步退货订单信息
     * @param unknown_type $roCode
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function syncReturnOrder($roCode){
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        try{
            $wms = new Common_ThirdPartWmsAPI();
            $rs = $wms->getReturnOrders($roCode);
            if($rs['ask'] != 'Success'){
                throw new Exception($rs['message']);
            }
            $data = $rs['data'];
            Service_ReturnOrdersOperationNode::delete($roCode, 'ro_code');
            if(! empty($data['return_orders_operation_node'])){
                foreach($data['return_orders_operation_node'] as $v){
                    Service_ReturnOrdersOperationNode::add($v);
                }
            }
            $updateRow = array('ro_status'=>$data['ro_status']);
            Service_ReturnOrders::update($updateRow, $roCode, 'ro_code');
            $return['ask'] = 1;
        }catch (Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
   
    
    /**
     * 同步费用流水
     * @param unknown_type $roCode
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function syncCustomerBalanceLog($companyCode){
        $return = array(
                'ask' => 0,
                'message' => ''
        );
        try{
            $wms = new Common_ThirdPartWmsAPI();
            $page = 1;
            while(true){
                $param = array(
                    'customer_code' => $companyCode,
                    'pageSize' => '20',
                    'page' => $page
                );
                $rs = $wms->getCustomerBalanceLog($param);
               
                if($rs['ask'] != 'Success'){
                    throw new Exception($rs['message']);
                }
                $data = $rs['data'];
                if(! empty($data)){//有数据
                    foreach($data as $v){
                        $exist = Service_CustomerBalanceLog::getByField($v['cbl_id'],'cbl_id');
                        if(!$exist){
                            Service_CustomerBalanceLog::add($v);
                        }                       
                    }
                }else{
                    break;
                }
                $page++;
            }

            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch (Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    /**
     * 同步特采，销毁
     * @param unknown_type $raCode
     * @throws Exception
     */
    public function syncReceivingAbnormal($raCode)
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        try{
            $wms = new Common_ThirdPartWmsAPI();
            $rs = $wms->getReceivingAbnormal($raCode);
            if($rs['ask'] != 'Success'){
                throw new Exception($rs['message']);
            }
            $data = $rs['data'];
            $ra = $data['ra'];
            $detail = $data['detail'];
            $updateRow = array(
                'receiving_code' => $ra['receiving_code'],
                'po_code' => $ra['po_code'],
                'ra_status' => $ra['ra_status']
            );
            Service_ReceivingAbnormal::update($updateRow, $ra['ra_code'], 'ra_code');
            
            foreach($detail as $v){
                $updateRow = array(
                    'rad_status' => $v['rad_status'],
                    'qc_code' => $v['qc_code'],
                    'is_qc' => $v['is_qc'],
                    'rad_note' => $v['rad_note'],
                    'rad_update_time' => date('Y-m-d H:i:s')
                );
                $con = array('ra_code'=>$ra['ra_code'],'product_barcode'=>$v['product_barcode']);
                $rad = Service_ReceivingAbnormalDetail::getByCondition($con);
                if(empty($rad)){
                    throw new Exception(Ec::Lang('inner_error'));
                }
                $rad = $rad[0];
                Service_ReceivingAbnormalDetail::update($updateRow, $rad['rad_id'],'rad_id');
            }
            
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    /**
     * 日志输出
     * 
     * @param unknown_type $log            
     */
    private function log($log)
    {
        if(PHP_SAPI == 'cli'){
            echo $log . "\n";
        }
    }
//ruston 0919 质检重量输入错误临时修改程序
    public static function productWeightUpdate($refer_no,$weight){
	    $result['ask'] = "success";
        $result['message'] = $weight;
	    $row=Service_Product::getByCondition(array('product_barcode'=>$refer_no),'*');
        switch ( count($row) )
        {
        	case 0:
        		$result['ask'] = "Failure";
        		$result['message']='SKU不存在';
        		break;
        	case 1:
        		if(Service_Product::update(array('product_weight'=>$weight),$refer_no,'product_barcode')){
	        		$result['ask'] = "success";
        			$result['message'] = '修改成功';
        		}else{
	        		$result['ask'] = "Failure";
        			$result['message']='系统异常';
        		}
        		break;
        	default:
        		$result['ask'] = "Failure";
        		$result['message']='SKU不唯一';
        		break;
        }
        return $result;
    }
    //ruston 0919 临时添加邮编
    public static function addPostCode($refer_no,$zone){
	    $result['ask'] = "success";
        $result['message'] = $refer_no;
        if(preg_match('/^\d{6}$/', $refer_no)&&preg_match('/^\d{1}$/', $zone)){
	        
		    $row=Service_TransportPickAreaMap::getByCondition(array('post_code'=>$refer_no), 'count(*)', 0, 1);
	        switch ( $row )
	        {
	        	case 0:
	        		if(Service_TransportPickAreaMap::add(array('post_code'=>$refer_no,'country'=>'RU','zone_code'=>$zone))){
						    $result['ask'] = "success";
	        				$result['message'] = 'oms添加成功!';
					    }else{
						    $result['ask'] = "Failure";
	        				$result['message']='系统异常';
					    }
	        		break;
	        	default:
	        		$result['ask'] = "Failure";
	        		$result['message']='邮编已存在';
	        		break;
	        }
    	}else{
	    			$result['ask'] = "Failure";
	        		$result['message']='数据异常';
    	}
        return $result;
    }
    

    /**
     * 同步转仓单状态
     * @param unknown_type $refId
     */
    public function syncTransferOrder($refId, $action) {
    	switch($action) {
    		case 'ship':
    			$this->syncTransferOrderShip($refId);
    			break;
    		case 'receiving':
    			$this->syncTransferOrderReceiving($refId);
    			break;
    		case 'putaway':
    			$this->syncTransferOrderPutaway($refId);
    			break;
    		default:
    			throw new Exception(Ec::Lang('param_error', 'method'));
    	}
    }
    
    /**
     * 转仓单收货
     * @param unknown_type $refId
     */
    private function syncTransferOrderReceiving($refId) {
    	$wmsService = new Common_ThirdPartWmsAPI();
    	$rs = $wmsService->getAsn($refId);
    	if($rs['ask']!='Failure'){
    		$rs = $rs['data'];
    		//             print_r($rs);exit;
    		$orderRow = Service_TransferOrders::getByField($rs['reference_no'], 'two_code');
    		if(empty($orderRow)) {
    			EC::showError("转仓单不存在, 单号：" . $rs['reference_no'], "sync_transfer");
    			return;
    		}
    
    		// 转运中 改成 收货中
    		if($orderRow['order_status'] == '3') {
    			$updateRow = array('order_status' => 4, 'date_last_modify' => date('Y-m-d H:i:s'));
    	   
    			Service_TransferOrders::update($updateRow, $rs['reference_no'],'two_code');
    		}
    	}
    	return $rs;
    }
    
    /**
     * 转仓单上架
     * @param unknown_type $refId
     */
    private function syncTransferOrderPutaway($refId) {
    	$wmsService = new Common_ThirdPartWmsAPI();
    	$rs = $wmsService->getAsn($refId);
    	if($rs['ask']!='Failure'){
    		$rs = $rs['data'];
    		//             print_r($rs);exit;
    		$orderRow = Service_TransferOrders::getByField($rs['reference_no'], 'two_code');
    		if(empty($orderRow)) {
    			EC::showError("转仓单不存在, 单号：" . $rs['reference_no'], "sync_transfer");
    			return;
    		}
    		 
    		// 获取明细数据
    		$orderProductArr = array();
    		$con = array("to_id" => $orderRow['to_id']);
    		$orderProductRows = Service_TransferOrderProduct::getByCondition($con);
    		 
    		// 按产品条码分组数据
    		foreach($orderProductRows as $row) {
    			$orderProductArr[$row['product_barcode']] = $row;
    		}
    		 
    		//     		EC::showError(print_r($orderProductArr, 1), "sync_transfer");
    		//     		EC::showError(print_r($rs['receiving_detail'], 1), "sync_transfer");
    		//明细处理
    		foreach($rs['receiving_detail'] as $p){
    
    			$updateProductRow = array(
    					'quantity_receiving' => $p['rd_received_qty'],
    					'quantity_putaway' => $p['rd_putaway_qty'],
    					'update_time'=>date('Y-m-d H:i:s')
    			);
    			 
    			$orderProductRow = $orderProductArr[$p['product_barcode']];
    			if(empty($orderProductRow)) {
    				EC::showError("产品不存在, 单号：" . $rs['reference_no'] . ",产品代码：" . $p['product_barcode'], "sync_transfer");
    				continue;
    			}
    
    			// 保存上架数量 用于判断是否上架完成
    			$orderProductArr[$p['product_barcode']]['rd_putaway_qty'] = $p['rd_putaway_qty'];
    
    			// 更新收货数量
    			Service_TransferOrderProduct::update($updateProductRow, $orderProductRow['top_id']);
    
    			//更新库存
    			$customer_code = $rs['customer_code'];
    			$warehouse_code = $wmsService->getWarehouseCode($rs['warehouse_id']);
    			$product_sku = $wmsService->getProductSku($p['product_barcode']);
    			$invParam = array(
    					'company_code' => $customer_code,
    					'warehouse_code' => $warehouse_code,
    					'product_sku' => $product_sku,
    			);
    			$subrs = $this->syncInventory($invParam);
    			//                 print_r($subrs);
    		}
    
    		$finishFlag = true;
    		foreach($orderProductArr as $row) {
    			 
    			// 如果订单数量小于上架数量表示未完成上架
    			if($row['quantity'] > $row['rd_putaway_qty']) {
    				$finishFlag = false;
    				break;
    			}
    		}
    		//     		EC::showError(print_r($orderProductArr, 1), "sync_transfer");
    
    		// 当上架完成时，更新为已上架状态
    		// 收货中 改成 已完成
    		if($finishFlag) {
    			$updateRow = array('order_status' => 5, 'date_last_modify' => date('Y-m-d H:i:s'));
    
    			Service_TransferOrders::update($updateRow, $rs['reference_no'],'two_code');
    		}
    	}
    	return $rs;
    }
    
    /**
     * 转仓单出货
     */
    private function syncTransferOrderShip($refId) {
    	$wmsService = new Common_ThirdPartWmsAPI();
    	$rs = $wmsService->getOrder($refId);
    	//         print_r($rs);exit;
    	Ec::showError(print_r($rs,true),'sync_transfer');
    	if($rs['ask']!='Failure'){
    		$rs = $rs['data'];
    		// 出库状态
    		$shipStatusArr = array(
    				'8',
    				'9',
    				'10',
    				'11',
    				'12',
    				'13'
    		);
    		 
    		$orderRow = Service_TransferOrders::getByField($refId, 'two_code');
    		if(empty($orderRow)) {
    			EC::showError("转仓单不存在, 单号：" . $refId, "sync_transfer");
    			return;
    		}
    
    		// 待发货 转 转运中
    		if(in_array($rs['order_status'], $shipStatusArr) && $orderRow['order_status'] == '2'){
    			//状态
    			$updateRow = array(
    					'order_status' => '3',
    					'shipping_method_no' => $rs['ship_order']['tracking_number'],
    					'order_weight' => $rs['ship_order']['so_weight'],
    					'date_warehouse_shipping' => $rs['order_operation_time']['ship_time'],
    					'date_last_modify' => date('Y-m-d H:i:s')
    			);
    			 
    			Service_TransferOrders::update($updateRow, $refId,'two_code');
    		}
    
    		//更新费用
    		Service_OrderProcess::updateOrderFee($refId, $rs);
    		 
    		//物流轨迹
    		if(!empty($rs['track_info'])){
    			Service_ShipTrackInfo::delete($rs['order_code'],'order_code');
    			foreach($rs['track_info'] as $track){
    				$exist = Service_ShipTrackInfo::getByField($track['sti_id'],'sti_id');
    				if($exist){
    					$sti_id = $track['sti_id'];
    					unset($track['sti_id']);
    					Service_ShipTrackInfo::update($track,$sti_id,'sti_id');
    				}else{
    					Service_ShipTrackInfo::add($track);
    				}
    			}
    		}
    
    		//签收信息
    		if(!empty($rs['track_pod'])){
    			Service_ShipTrackPod::delete($rs['order_code'],'order_code');
    			foreach($rs['track_pod'] as $pod){
    				$exist = Service_ShipTrackPod::getByField($pod['stp_id'],'stp_id');
    				if($exist){
    					$stp_id = $pod['stp_id'];
    					unset($pod['stp_id']);
    					Service_ShipTrackPod::update($pod,$stp_id,'stp_id');
    				}else{
    					Service_ShipTrackPod::add($pod);
    				}
    				 
    			}
    		}
    
    		//库存同步
    		foreach($rs['order_product'] as $p){
    			// 源仓库库存
    			$obj = array(
    					'company_code' => $rs['customer_code'],
    					'warehouse_code' => $wmsService->getWarehouseCode($rs['warehouse_id']),
    					'product_sku' => $wmsService->getProductSku($p['product_barcode']),
    			);
    			$this->syncInventory($obj);
    			 
    			// 目的仓库存
    			$obj = array(
    					'company_code' => $rs['customer_code'],
    					'warehouse_code' => $wmsService->getWarehouseCode($rs['to_warehouse_id']),
    					'product_sku' => $wmsService->getProductSku($p['product_barcode']),
    			);
    			$this->syncInventory($obj);
    		}
    		 
    		$con = array(
    				'ref_id' => $rs['order_code'],
    				'system' => 'wms'
    		);
    		$logRows = Service_TransferOrderLog::getByCondition($con);
    		foreach($logRows as $v){
    			Service_TransferOrderLog::delete($v['log_id'],'log_id');
    		}
    		$opType = Service_OrderOperationType::getAll();
    		foreach($opType as $k=>$v){
    			unset($opType[$k]);
    			$opType[$v['oot_code']] = $v;
    		}
    
    		//操作节点
    		$nodes = $rs['order_operation_node'];
    		foreach($nodes as $node){
    			$logRow = array(
    					'ref_id' => $rs['order_code'],
    					'log_content' => $opType[$node['oot_code']]['oot_name'].' At: '.$node['oon_add_time'],
    					'create_time' => date('Y-m-d H:i:s'),
    					'system' => 'wms',
    					'op_id'=>'0',
    					'op_user_id'=>'0'
    			);
    			Service_TransferOrderLog::add($logRow);
    		}
    	}
    }
     
    /**
     * 同步费用类型到OMS
     */
    public function syncValueAddedType()
    {
    	$return = array(
    			'ask' => 0,
    			'message' => ''
    	);
    
    	$process = new Common_ThirdPartWmsAPI();
    	$result = $process->getValueAddedType();
    	//         print_r($result);exit;
    	EC::showError(print_r($result,true), "--valueAdd--");
    	if($result['ask'] == 'Success'){
    		Service_ValueAddedType::delete('1', '1');
    
    		foreach($result['data'] as $v){
    			try{
    				$v = $this->arr_trim($v);
    				Service_ValueAddedType::add($v);
    			}catch(Exception $e){
    				log($e->getMessage());
    				throw new Exception(print_r($v,true));
    			}
    		}
    		$return['ask'] = 1;
    		$return['message'] = 'Success';
    	}else{
    		$return['message'] = $result['message'];
    	}
    	return $return;
    }
}