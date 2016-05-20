<?php
class Process_Product
{
    public static  $maxUpload = 8;//最多上传图片数
    public $_err = array(); 
	/**
	 * 验证参考单号是否存在
	 *
	 * @param string $refrenceNo
	 *            客户参考号
	 * @param string $receiving_code
	 *            平台入库单号
	 * @return boolean
	 */
	public function validateRefrenceNo($refrenceNo, $productId = '')
	{
		
		if($refrenceNo){
			$con = array(
					'reference_no' => $refrenceNo
			);
			$rows = Service_Product::getByCondition($con);
// 			print_r($rows);exit;
			foreach($rows as $k => $v){
				if($productId && $productId == $v['product_id']){
					unset($rows[$k]);
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
     * 创建产品
     * @param unknown_type $row
     * @param unknown_type $productId
     * @throws Exception
     */
    public function create($row, $productId = '')
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
            $this->createSingle($row, $productId);
            $db->commit();
            $return['ask'] = 1;
            $return['state'] = 2;
            if($productId!=''){
            	$return['message'] = Ec::Lang('product_update_success',$row['product_sku']);
            }else{
            	$return['message'] = Ec::Lang('product_create_success',$row['product_sku']);
            }
           
        }catch(Exception $e){
            $db->rollback();
        	if($productId!=''){
            	$return['message'] = Ec::Lang('product_update_fail',$row['product_sku']).',Reason:'.$e->getMessage();
            }else{
            	$return['message'] = Ec::Lang('product_create_fail',$row['product_sku']).',Reason:'.$e->getMessage();
            }
            $return['errorMessage'] = array(
                $e->getMessage()
            );
        }
        return $return;
    }
    private function _validate($row){

    	if($row['cat_id0'] == '' || $row['cat_id1'] == '' || $row['cat_id2'] == ''){
    	    $this->_err[] = Ec::Lang('sku_category_required', $row['product_sku']);
    		//throw new Exception(Ec::Lang('sku_category_required', $row['product_sku']));
    	}
        if(empty($row['product_title'])){
            $this->_err[] =Ec::Lang('product_title_required', $row['product_sku']);
            //throw new Exception(Ec::Lang('product_title_required', $row['product_sku']));
        }
        if(empty($row['product_sku']) || ! preg_match('/^([a-zA-Z0-9\-_\.]+)$/', $row['product_sku'])){//ruston0903 sku创建时允许点
            $this->_err[] = Ec::Lang('sku_invalid', $row['product_sku']);
            //throw new Exception(Ec::Lang('sku_invalid', $row['product_sku']));
        }

        if(strlen($row['product_sku'])>35){
            $this->_err[] = Ec::Lang('sku_invalid', $row['product_sku']);
            //throw new Exception(Ec::Lang('sku_invalid', $row['product_sku']));
        }
        
        // 增加判断条件，否则为0时会报错 RUSTON0719
        if($row['product_declared_value'] !== '' && $row['product_declared_value'] != 0){        	
            if(empty($row['product_declared_value']) || ! preg_match('/^([0-9\.]+)$/', $row['product_declared_value'])){
                // throw new Exception('SKU:' . $row['product_sku'] .
                // '‘申报价值’不是数字');
                $this->_err[] = Ec::Lang('sku_declared_not_num', $row['product_sku']);
                //throw new Exception(Ec::Lang('sku_declared_not_num', $row['product_sku']));
            }
         }
        $low = chr(0xa1);
        $high = chr(0xff);        
        if(empty($row['product_declared_name']) || preg_match("/[$low-$high]/", $row['product_declared_name'])){
            $this->_err[] = Ec::Lang('declared_name_can_not_empty_and_can_not_contain_chinese', $row['product_sku']);
            //throw new Exception(Ec::Lang('declared_name_can_not_empty_and_can_not_contain_chinese', $row['product_sku']));
        }        

        // 增加判断条件，否则为0时会报错 RUSTON0719
        if($row['product_weight'] !== '' && $row['product_weight'] != 0){
            if(empty($row['product_weight']) || ! preg_match('/^([0-9\.]+)$/', $row['product_weight'])){
                // throw new Exception('SKU:' . $row['product_sku'] .
                // '‘重量’不是数字');
                $this->_err[] = Ec::Lang('sku_weight_not_num', $row['product_sku']);
                //throw new Exception(Ec::Lang('sku_weight_not_num', $row['product_sku']));
            }
        }
        // 增加判断条件，否则为0时会报错 RUSTON0719
        if($row['product_length'] !== '' && $row['product_length'] != 0){
            if(empty($row['product_length']) || ! preg_match('/^([0-9\.]+)$/', $row['product_length'])){
                // throw new Exception('SKU:' . $row['product_sku'] .
                // '‘长’不是数字');
                $this->_err[] = Ec::Lang('sku_length_not_num', $row['product_sku']);
                //throw new Exception(Ec::Lang('sku_length_not_num', $row['product_sku']));
            }
        }
         
        // 增加判断条件，否则为0时会报错 RUSTON0719
        if($row['product_width'] !== '' && $row['product_width'] != 0){
            if(empty($row['product_width']) || ! preg_match('/^([0-9\.]+)$/', $row['product_width'])){
                // throw new Exception('SKU:' . $row['product_sku'] .
                // '‘宽’不是数字');
                $this->_err[] = Ec::Lang('sku_width_not_num', $row['product_sku']);
                //throw new Exception(Ec::Lang('sku_width_not_num', $row['product_sku']));
            }
        }
        // 增加判断条件，否则为0时会报错 RUSTON0719
        if($row['product_height'] !== '' && $row['product_height'] != 0){
            if(empty($row['product_height']) || ! preg_match('/^([0-9\.]+)$/', $row['product_height'])){
                // throw new Exception('SKU:' . $row['product_sku'] .
                // '‘高’不是数字');
                $this->_err[] = Ec::Lang('sku_height_not_num', $row['product_sku']);
                //throw new Exception(Ec::Lang('sku_height_not_num', $row['product_sku']));
            }
         }
         $maxUpload = Process_Product::$maxUpload;
         if(count($row['pa_id'])>$maxUpload){
             // throw new Exception('SKU:' . $row['product_sku'] .
             // '‘高’不是数字');
             $this->_err[] = Ec::Lang('pic_upload_can_not_more_then_xx', array($row['product_sku'],$maxUpload));
             //throw new Exception(Ec::Lang('pic_upload_can_not_more_then_xx', array($row['product_sku'],$maxUpload)));
         }
    	//RUSTON0904 验证申报品名的长度
         if(strlen($row['product_declared_name'])>25){
	         $this->_err[] = Ec::Lang('declared_name_can_not_length', array($row['product_declared_name']));
         }
    }
    public function createSingle($row, $productId=''){
        
        $row['product_sku'] = strtoupper($row['product_sku']);
        $row['product_barcode'] = strtoupper($row['product_barcode']);
        $row['reference_no'] = strtoupper($row['reference_no']);
        $row['cat_lang'] = in_array($row['cat_lang'],array('zh','en'))?$row['cat_lang']:'en';
// 		print_r($row);exit;
        $this->_validate($row);

        $pa_id_arr = $row['pa_id'];
        unset($row['pa_id']);
    	// 			print_r($row);exit;
    	// 验证参考单号
    	$this->validateRefrenceNo($row['reference_no'], $productId);
    	unset($row['product_status']);
    	if(! empty($productId)){ 
    		$product = Service_Product::getByField($productId, 'product_id');
    	    //产品状态
    	    if($product['product_status']!='2'){//产品状态:0 删除,1正式产品,2:开发产品
    	        throw new Exception("{$row['product_sku']}".Ec::Lang('product_operation_deny'));
    	    }
    	    //===================================校验失败
    	    if(!empty($this->_err)){
    	        throw new Exception(Ec::Lang('validate_err'));
    	    }  
    		//创建过入库单   		
            $con = array('product_id'=>$productId);
            $hasAsn = Service_ReceivingDetail::getByCondition($con,'count(*)');
            $row['have_asn'] = 0;
            if($hasAsn){//产品已经使用SKU不可编辑
	    		throw new Exception("{$row['product_sku']}".Ec::Lang('product_operation_deny'));
	    		unset($row['product_sku']);
	    		unset($row['product_barcode']); 
	    		$row['have_asn'] = 1;
            }
            //创建过订单
            $con = array('product_id'=>$productId);
            $hasOrder = Service_OrderProduct::getByCondition($con,'count(*)');
            if($hasOrder){
	    		throw new Exception("{$row['product_sku']}".Ec::Lang('product_operation_deny'));
            }
    		unset($row['sale_status']);
    		$product = Service_Product::getByField($productId, 'product_id');
    		unset($row['product_add_time']);
    		$row['product_update_time'] = date('Y-m-d H:i:s');
    		$result = Service_Product::update($row, $productId);
    		$log = array();
    		$diff = array_diff_assoc($row, $product);
    		if(!empty($diff)){
    			foreach($diff as $k => $v){
    				$log[] = $k . ' from ' . $product[$k] . ' to ' . $v;
    			}
    			$logRow = array(
    					'product_id' => $productId,
    					'pl_type' => '0',
    					'user_id' => Service_User::getUserId(),
    					'pl_note' => implode(';', $log),
    					'pl_add_time' => date('Y-m-d H:i:s'),
    					'pl_ip' => Common_Common::getIP()
    			);
    			Service_ProductLog::add($logRow);
    		}
    	
    	}else{
    		$exist = Service_Product::getByField($row['product_barcode'], 'product_barcode');
    		if($exist){
    			throw new Exception(Ec::Lang('sku_exist', $row['product_sku']));
    		}
    		//===================================校验失败
    		if(!empty($this->_err)){
    		    throw new Exception(Ec::Lang('validate_err'));
    		}
    		$row['sale_status']='0';
    		$row['product_status'] = '2';//产品状态:0 删除,1正式产品,2:开发产品
    		
    		$row['product_add_time'] = date('Y-m-d H:i:s');
    		$row['product_update_time'] = date('Y-m-d H:i:s');
    		$productId = Service_Product::add($row);
    		$product = Service_Product::getByField($productId, 'product_id');
    		$logRow = array(
    				'product_id' => $productId,
    				'pl_type' => '0',
    				'user_id' => Service_User::getUserId(),
    				'pl_note' => '新增产品',
    				'pl_add_time' => date('Y-m-d H:i:s'),
    				'pl_ip' => Common_Common::getIP()
    		);
    		Service_ProductLog::add($logRow);
    	}
    	// 发送数据到WMS
    	//$this->dataToWarehouse($productId);
    	
    	$con = array('product_id'=>$productId);
    	$attacheds = Service_ProductAttached::getByCondition($con);
    	foreach($attacheds as $att){
    	    $updateRow = array('product_id'=>'0');
    	    Service_ProductAttached::update($updateRow, $att['id']);
    	}
    	foreach($pa_id_arr as $pa_id){
    	    $updateRow = array('product_id'=>$productId);
    	    Service_ProductAttached::update($updateRow, $pa_id,'id');
    	}
    	
    	return $productId;
    }
    
    /**
     * 审核
     * @param unknown_type $productId
     */
    public function verify($productId){ 
        $result = array('ask'=>0,'message'=>Ec::lang('product_verify_fail')); 
        try{
            $product = Service_Product::getByField($productId,'product_id');
            if(empty($product)){
                throw new Exception(Ec::Lang('inner_error'));
            }
            if($product['product_status']!='2'){
                throw new Exception("[{$product['product_sku']}]".Ec::Lang('product_operation_deny'));
            }
            $this->dataToWarehouse($productId);
            $row = array();
            $row['product_status'] = '1';
            Service_Product::update($row, $productId);
            $result['ask'] = 1;
            $result['product_sku'] = $product['product_sku'];
            $result['message'] = Ec::lang('product_verify_success',$product['product_sku']);
        }catch (Exception $e){
            $result['message'] = $e->getMessage();
        }   
        return $result;
    }
    // 发送数据到WMS
    public function dataToWarehouse($productId){
    	// 发送数据到WMS
    	$apiService = new Common_ThirdPartWmsAPI();
    	//测试，创建账号
    	//             $rs = $apiService->createCompany($product['company_code']);
    	$rs = $apiService->createProduct($productId);
    	//             print_r($rs);exit;
    	if($rs['ask']!='Failure'){
    		//修改同步
    		$updateRow = array('sync_to_wms'=>'1','product_status'=>'1');
    		Service_Product::update($updateRow, $productId);
    	}else{
    		throw new Exception(Ec::Lang('wms_error', $rs['message']));
    	}
    }

    /**
     * 废弃
     * @param unknown_type $productId
     */
    public function discard($productId){
        $result = array('ask'=>0,'message'=>Ec::lang('product_verify_fail'));
        try{
            $product = Service_Product::getByField($productId,'product_id');
            if($product['product_status']!='2'){
                throw new Exception("[{$product['product_sku']}]".Ec::Lang('product_operation_deny'));
            }
            $row = array();
            $row['product_status'] = '0';
            Service_Product::update($row, $productId);
//             Service_Product::delete($productId,'product_id');
            $result['ask'] = 1;
            $result['product_sku'] = $product['product_sku'];
            $result['message'] = Ec::lang('product_discard_success',$product['product_sku']);
        }catch (Exception $e){
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * 删除
     * @param unknown_type $productId
     */
    public function delete($productId){
        $result = array('ask'=>0,'message'=>Ec::lang('product_delete_fail'));
        try{
            $product = Service_Product::getByField($productId,'product_id');
            if($product['product_status']!='0'){
                throw new Exception("[{$product['product_sku']}]".Ec::Lang('product_operation_deny'));
            }
            $row = array();
            $row['product_status'] = '0';
//             Service_Product::update($row, $productId);
            Service_Product::delete($productId,'product_id');
            $result['ask'] = 1;
            $result['product_sku'] = $product['product_sku'];
            $result['message'] = Ec::lang('product_delete_success',$product['product_sku']);
        }catch (Exception $e){
            $result['message'] = $e->getMessage();
        }
    
        return $result;
    }
}