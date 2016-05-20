<?php
class Process_Return
{
    
    public function getSyncType(){
        $config = Service_Config::getByField('RETURN_ORDER_VERIFY_SYNC_TYPE','config_attribute');
        if(!$config){
            $config = array(
                'config_attribute' => 'RETURN_ORDER_VERIFY_SYNC_TYPE',
                'config_value' => '1',
                'config_description' => '退件是否马上同步，1为建立后马上同步，0表示需要审核同步',
                'config_add_time' => date('Y-m-d H:i:s'),
                'config_update_time' => date('Y-m-d H:i:s'),
            );
            $config['config_id'] = Service_Config::add($config);
        }
        return $config['config_value'];
    }
	/**
	 * 验证参考单号是否存在
	 *
	 * @param string $refrenceNo
	 *            客户参考号
	 * @param string $receiving_code
	 *            平台入库单号
	 * @return boolean
	 */
	public function validateRefrenceNo($refrenceNo, $roCode = '')
	{
		
		if($refrenceNo){
			$con = array(
					'reference_no' => $refrenceNo
			);
			$rows = Service_ReturnOrders::getByCondition($con);
			foreach($rows as $k => $v){
				if($roCode && $roCode == $v['ro_code']){
					unset($rows[$k]);
				}
				if($v['ro_status']=='0'){
				    unset($rows[$k]);
				}
			}
			if($rows){
				throw new Exception(Ec::Lang('reference_no_exist',$refrenceNo), '30000');
			}
		}
	
		return true;
	}
	/**
	 * 验证订单号是否存在
	 *
	 * @return boolean
	 */
	public function validateOrderCode($refrence_no_platform, $roCode = '')
	{
        return true;
    }
	
	/**
	 * 数据验证
	 * @param unknown_type $row
	 * @throws Exception
	 * @return Ambigous <unknown, mixed>
	 */
	public function validate($row,$roCode=''){
	    $return_orders = $row['return_orders'];
	    $return_order_product = $row['return_order_product'];
	    if(empty($return_orders['ro_desc'])){
	        throw new Exception(Ec::Lang('ro_desc_can_not_empty'));
	    }
	    // 验证订单信息 start
	    if(empty($return_orders['refrence_no_platform'])){
	        throw new Exception(Ec::Lang('order_code_can_not_empty'));
	    }
	    $refrence_no_platform = $return_orders['refrence_no_platform'];
	    $order = Service_Orders::getByField($refrence_no_platform,'refrence_no_platform');
	    if(!$order){
	        throw new Exception(Ec::Lang('order_not_exist',$refrence_no_platform));
	    }
	    if($order['order_status']!='4'){
	        throw new Exception(Ec::Lang('order_create_return_deny',$refrence_no_platform));
	    }
	    $roCode = empty($roCode)?'':$roCode;
	    $ro = Service_ReturnOrders::getByField($roCode,'ro_code');
	    $con = array(
	            'refrence_no_platform' => $refrence_no_platform
	    );
	    $exists = Service_ReturnOrders::getByCondition($con);
	    foreach($exists as $k => $exit){
	        if($ro&&$ro['refrence_no_platform'] == $exit['refrence_no_platform']){
	            unset($exists[$k]);
	        }
	        if($exit['ro_status']=='0'){//已经作废的订单
	            unset($exists[$k]);
	        }
	    }
	    if(! empty($exists)){
	        throw new Exception(Ec::Lang('order_code_has_create_return_order'));
	    }
	    // 验证订单信息 end
	    $con = array('order_id'=>$order['order_id']);
	    $orderProductT = Service_OrderProduct::getByCondition($con);
	    $orderProduct = array();
	    foreach($orderProductT as $v){
	        $orderProduct[$v['product_id']] = $v;
	    }
	    $totalCount = 0;
	    foreach($return_order_product as $k=>$v){
	        if(empty($v['rop_quantity'])){
	            unset($return_order_product[$k]);
	            continue;
	        }
	        $product = Service_Product::getByField($v['product_id'],'product_id');
	        if(empty($product)){
	            throw new Exception(Ec::Lang('sku_not_exist',$v['product_sku']));
	        }	        
	        $v['rop_quantity'] = trim($v['rop_quantity']);
	        if(!preg_match('/^[0-9]+$/', $v['rop_quantity'])){
	            throw new Exception(Ec::Lang('quantity_must_numeric',$v['product_sku']));
	        }
            // 	        订单不包含该SKU
	        if(!isset($orderProduct[$v['product_id']])){
	            throw new Exception(Ec::Lang('order_do_not_contain_this_sku',$product['product_sku']));
	        }
            // 	        产品退货数量不可大于订单数量
	        if($orderProduct[$v['product_id']]['op_quantity']<$v['rop_quantity']){
	            throw new Exception(Ec::Lang('return_quantity_can_not_gt_op_quantity',$product['product_sku']));
	        }
	        $totalCount+=$v['rop_quantity'];
	        $v['product_sku'] = $product['product_sku'];
	        $v['product_barcode'] = $product['product_barcode'];
	        unset($v['product_sku']);
	        $return_order_product[$k] = $v;
	    }
	    if($totalCount==0){
	        throw new Exception(Ec::Lang('sku_count_must_gt_0'));
	    }
	    $row['return_orders'] = $return_orders;
	    $row['return_order_product'] = $return_order_product;

	    return $row;
	}
    /**
     * 创建产品
     * @param unknown_type $row
     * @param unknown_type $productId
     * @throws Exception
     */
    public function createTransaction($row, $roCode = '')
    {
        $return = array(
            'state' => 0,
            'ask' => 0,
            'message' => '',
            'errorMessage' => array(
                'Fail.'
            )
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $roCodeNew = $this->createSingle($row, $roCode);
            $syncType = $this->getSyncType();
            if($syncType=='1'){
                $this->dataToWarehouse($roCodeNew);
            }            
            $db->commit();
            $return['ask'] = 1;
            $return['state'] = 1;
            if($roCode!=''){
            	$return['message'] = Ec::Lang('return_order_update_success',$roCode);
            }else{
            	$return['message'] = Ec::Lang('return_order_create_success',$roCodeNew);
            }
           
        }catch(Exception $e){
            $db->rollback();
        	if($roCode!=''){
            	$return['message'] = Ec::Lang('return_order_update_fail',$roCode).',Reason:'.$e->getMessage();
            }else{
            	$return['message'] = Ec::Lang('return_order_create_fail',$roCodeNew).',Reason:'.$e->getMessage();
            }
            $return['errorMessage'] = array(
                $e->getMessage()
            );
        }
        return $return;
    }
    
    public function createSingle($row, $roCode=''){
    	$row = $this->validate($row);    	
	    $return_orders = $row['return_orders'];
	    $return_order_product = $row['return_order_product'];
	    //验证订单号	   
	    $this->validateOrderCode($return_orders['refrence_no_platform'],$roCode) ;
    	// 验证参考单号
    	$this->validateRefrenceNo($return_orders['reference_no'], $roCode);
    	
    	$return_orders['ro_status'] = '1';//新增，修改 状态为  待确认
    	
    	if(! empty($roCode)){    	    
    		$ro = Service_ReturnOrders::getByField($roCode, 'ro_code');   
    		if(!$ro){
    		    throw new Exception(Ec::Lang('ro_not_exist'));
    		} 	    
    		Service_ReturnOrders::update($return_orders, $roCode,'ro_code');    
    		Service_ReturnOrderProduct::delete($ro['ro_id'],'ro_id');
    		foreach($return_order_product as $p){
    		    $p['ro_id'] = $ro['ro_id'];
    		    $p['exception_process_instruction'] = $return_orders['ro_process_type'];//异常处理指令 0:无(存放不良品区);1:重新上架;2:退回;3:销毁;
    		    Service_ReturnOrderProduct::add($p);
    		}	
    	}else{   
    	    $roCode = Common_GetNumbers::getCode('RETURN_ORDER',$return_orders['company_code'],'R');
    	    $return_orders['ro_code'] = $roCode;
    		$ro_id = Service_ReturnOrders::add($return_orders);
    	    
    		foreach($return_order_product as $p){
    		    $p['ro_id'] = $ro_id;
    		    $p['exception_process_instruction'] = $return_orders['ro_process_type'];//异常处理指令 0:无(存放不良品区);1:重新上架;2:退回;3:销毁;
    		    Service_ReturnOrderProduct::add($p);
    		}
    	} 
    	return  $roCode;  	
    }
    /**
     * 审核
     * @param unknown_type $productId
     */
    public function verifyTransaction($roCode){ 
        $result = array('ask'=>0,'message'=>Ec::lang('return_order_verify_fail')); 

        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $this->verifySingle($roCode);
            $row = array();
            $result['ask'] = 1;
            $result['message'] = Ec::lang('return_order_verify_success',$roCode);
            $db->commit();
        }catch (Exception $e){
            $db->rollback();
            $result['message'] = Ec::lang('return_order_verify_fail',$roCode).',Reason:'.$e->getMessage();
        }   
        return $result;
    }
    /**
     * 审核
     * @param unknown_type $productId
     */
    public function verifySingle($roCode){
        $ro = Service_ReturnOrders::getByField($roCode,'ro_code');
        if(empty($ro)){
            throw new Exception(Ec::Lang('inner_error'));
        }
        if($ro['ro_status']!='1'){
            throw new Exception(Ec::Lang('return_order_operation_deny',$roCode));            
        }
        $this->dataToWarehouse($roCode);           
    }
    // 发送数据到WMS
    public function dataToWarehouse($roCode){
    	// 发送数据到WMS
    	$apiService = new Common_ThirdPartWmsAPI();
    	//测试，创建账号
    	$rs = $apiService->createReturnOrder($roCode);
    	//             print_r($rs);exit;
    	if($rs['ask']!='Failure'){
            // 修改同步
            $updateRow = array(
                'ro_sync_status' => '1',
                'ro_status' => '2',
                'verifier'=>Service_User::getUserId(),
                'ro_confirm_time'=>date('Y-m-d H:i:s')   
            );
    		Service_ReturnOrders::update($updateRow, $roCode,'ro_code');
    	}else{
    		throw new Exception(Ec::Lang('wms_error', $rs['message']));
    	}
    }

    /**
     * 废弃退货单
     * @param unknown_type $roCode
     * @return multitype:number string multitype:string  multitype:NULL  NULL
     */
    public function discardTransaction($roCode,$note='手工废弃'){
        $return = array(
            'state' => 0,
            'ask' => 0,
            'message' => '',
            'errorMessage' => array(
                'Fail.'
            )
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $ro = Service_ReturnOrders::getByField($roCode, 'ro_code');            
            if(!$ro){
                throw new Exception(Ec::Lang('return_order_not_exist',$roCode));
            }
            if($ro['ro_create_type']!='0'){
                throw new Exception(Ec::Lang('return_order_operation_deny',$roCode));                
            }
            $allowStatus = array('1','2');
            if(!in_array($ro['ro_status'],$allowStatus)){
                throw new Exception(Ec::Lang('return_order_operation_deny',$roCode));
            }
            if($ro['ro_status']=='2'){//已经发送到wms
                $wms = new Common_ThirdPartWmsAPI();
                $rs = $wms->cancelReturnOrders($roCode,$note);
                if($rs['ask']!='Success'){
                    throw new Exception($rs['message']);
                }
                $data = $rs['data'];
                Service_ReturnOrdersOperationNode::delete($roCode,'ro_code');
                if(!empty($data['return_orders_operation_node'])){
                    foreach($data['return_orders_operation_node'] as $v){
                        Service_ReturnOrdersOperationNode::add($v);
                    }
                }
            }
            $updateRow = array(
                'ro_status' => '0',
                'ro_update_time'=>date('Y-m-d H:i:s')                   
            );
            Service_ReturnOrders::update($updateRow, $roCode, 'ro_code');
            
            $db->commit();
            $return['ask'] = 1;
            $return['state'] = 2;
            $return['message'] = Ec::Lang('return_order_discard_success', $roCode);
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = Ec::Lang('return_order_discard_fail', $roCode) . ',Reason:' . $e->getMessage();
            
            $return['errorMessage'] = array(
                $e->getMessage()
            );
        }
        return $return;
    }
}