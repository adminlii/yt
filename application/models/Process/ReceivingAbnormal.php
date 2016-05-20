<?php
class Process_ReceivingAbnormal
{
    
    public function getSyncType(){
        $config = Service_Config::getByField('RECEIVING_ABNORMAL_VERIFY_SYNC','config_attribute');
        if(!$config){
            $config = array(
                'config_attribute' => 'RECEIVING_ABNORMAL_VERIFY_SYNC',
                'config_value' => '1',
                'config_description' => '是否马上同步，1为建立后马上同步，0表示需要审核同步',
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
	public function validateRefrenceNo($refrenceNo, $raCode = '')
	{
		
		if($refrenceNo){
			$con = array(
					'ref_no' => $refrenceNo
			);
			$raws = Service_ReceivingAbnormal::getByCondition($con);
			foreach($raws as $k => $v){
				if($raCode && $raCode == $v['ra_code']){
					unset($raws[$k]);
				}
				if($v['ra_status']=='0'){
				    unset($raws[$k]);
				}
			}
			if($raws){
				throw new Exception(Ec::Lang('ref_no_exist',$refrenceNo), '30000');
			}
		}
	
		return true;
	}
	
	
	/**
	 * 数据验证
	 * @param unknown_type $raw
	 * @throws Exception
	 * @return Ambigous <unknown, mixed>
	 */
	public function validate($raw,$raCode=''){
	    $receiving_abnormal = $raw['receiving_abnormal'];
	    $receiving_abnormal_detail = $raw['receiving_abnormal_detail'];
	    if(empty($receiving_abnormal['ra_desc'])){
	        //throw new Exception(Ec::Lang('ra_desc_can_not_empty'));
	    }
	   
	    $totalCount = 0;
	    foreach($receiving_abnormal_detail as $k=>$v){
	        if(empty($v['rad_quantity'])){
	            unset($receiving_abnormal_detail[$k]);
	            continue;
	        }
	        $product = Service_Product::getByField($v['product_id'],'product_id');
	        if(empty($product)){
	            throw new Exception(Ec::Lang('sku_not_exist',$v['product_sku']));
	        }	        
	        $v['rad_quantity'] = trim($v['rad_quantity']);
	        if(!preg_match('/^[0-9]+$/', $v['rad_quantity'])){
	            throw new Exception(Ec::Lang('quantity_must_numeric',$v['product_sku']));
	        }
           
	        $totalCount+=$v['rad_quantity'];
	        $v['product_sku'] = $product['product_sku'];
	        $v['product_barcode'] = $product['product_barcode'];
	        unset($v['product_sku']);
	        $receiving_abnormal_detail[$k] = $v;
	    }
	    if($totalCount==0){
	        throw new Exception(Ec::Lang('sku_count_must_gt_0'));
	    }
	    $raw['receiving_abnormal'] = $receiving_abnormal;
	    $raw['receiving_abnormal_detail'] = $receiving_abnormal_detail;

	    return $raw;
	}
    /**
     * 创建产品
     * @param unknown_type $raw
     * @param unknown_type $productId
     * @throws Exception
     */
    public function createTransaction($raw, $raCode = '')
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
            $raCodeNew = $this->createSingle($raw, $raCode);
            $syncType = $this->getSyncType();
            if($syncType=='1'){
                $this->dataToWarehouse($raCodeNew);
            }            
            $db->commit();
            $return['ask'] = 1;
            $return['state'] = 1;
            if($raCode!=''){
            	$return['message'] = Ec::Lang('receiving_abnormal_update_success',$raCodeNew);
            }else{
            	$return['message'] = Ec::Lang('receiving_abnormal_create_success',$raCodeNew);
            }
           
        }catch(Exception $e){
            $db->rollback();
        	if($raCode!=''){
            	$return['message'] = Ec::Lang('receiving_abnormal_update_fail',$raCodeNew).',Reason:'.$e->getMessage();
            }else{
            	$return['message'] = Ec::Lang('receiving_abnormal_create_fail',$raCodeNew).',Reason:'.$e->getMessage();
            }
            $return['errorMessage'] = array(
                $e->getMessage()
            );
        }
        return $return;
    }
    
    public function createSingle($raw, $raCode=''){
    	$raw = $this->validate($raw);    	
	    $receiving_abnormal = $raw['receiving_abnormal'];
	    $receiving_abnormal_detail = $raw['receiving_abnormal_detail'];
	    
    	// 验证参考单号
    	$this->validateRefrenceNo($receiving_abnormal['ref_no'], $raCode);
    	
    	if(! empty($raCode)){    	    
    		$ra = Service_ReceivingAbnormal::getByField($raCode, 'ra_code');    	    
    		$result = Service_ReceivingAbnormal::update($receiving_abnormal, $raCode,'ra_code');    
    		Service_ReceivingAbnormalDetail::delete($ra['ra_id'],'ra_id');
    		foreach($receiving_abnormal_detail as $p){
    		    $p['ra_id'] = $ra['ra_id'];
    		    $p['ra_code'] = $raCode;
    		    Service_ReceivingAbnormalDetail::add($p);
    		}	
    	}else{   
    	    $raCode = Common_GetNumbers::getCode('RECEIVING_ABNORMAL',$receiving_abnormal['company_code'],'RA');
    	    $receiving_abnormal['ra_code'] = $raCode;
    		$ra_id = Service_ReceivingAbnormal::add($receiving_abnormal);
    	    
    		foreach($receiving_abnormal_detail as $p){
    		    $p['ra_id'] = $ra_id;
    		    $p['ra_code'] = $raCode;
    		    Service_ReceivingAbnormalDetail::add($p);
    		}
    	} 
    	return  $raCode;  	
    }
    /**
     * 审核
     * @param unknown_type $productId
     */
    public function verifyTransaction($raCode){ 
        $result = array('ask'=>0,'message'=>Ec::lang('receiving_abnormal_verify_fail')); 

        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $this->verifySingle($raCode);
            $raw = array();
            $result['ask'] = 1;
            $result['message'] = Ec::lang('receiving_abnormal_verify_success',$raCode);
            $db->commit();
        }catch (Exception $e){
            $db->rollback();
            $result['message'] = Ec::lang('receiving_abnormal_verify_fail',$raCode).',Reason:'.$e->getMessage();
        }   
        return $result;
    }
    /**
     * 审核
     * @param unknown_type $productId
     */
    public function verifySingle($raCode){
       
        $this->dataToWarehouse($raCode);           
    }
    // 发送数据到WMS
    public function dataToWarehouse($raCode){
        
        $ra = Service_ReceivingAbnormal::getByField($raCode,'ra_code');
        if(empty($ra)){
            throw new Exception(Ec::Lang('inner_error'));
        }
        if($ra['ra_sync_status']=='1'){
            return;
        }
    	// 发送数据到WMS
    	$apiService = new Common_ThirdPartWmsAPI();
    	//测试，创建账号
    	$rs = $apiService->createReceivingAbnormal($raCode);
    	
    	if($rs['ask']!='Failure'){
            // 修改同步
            $updateRow = array(
                'ra_sync_status' => '1'
            );
    		Service_ReceivingAbnormal::update($updateRow, $raCode,'ra_code');
    		
    	}else{
    		throw new Exception(Ec::Lang('wms_error', $rs['message']));
    	}
    }

}