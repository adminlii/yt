<?php
class Service_PurchaseOpration extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PurchaseOrders|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PurchaseOrders();
        }
        return self::$_modelClass;
    }

    /**
     * 采购单eta撤销业务逻辑
     * @param unknown_type $poEidtObj
     * @param unknown_type $productEditObje
     * @param unknown_type $purchaseInven
     */
    public static function etaDerecognition($poEidtObj=array(),$productEditObje=array(),$purchaseInven=array(),$poCode){
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try {
    		$model = self::getModelInstance();
    		
    		$paramsUserId = Service_User::getUserId();//当前操作用户
    		$date = date('Y-m-d H:i:s');
    		/*
    		 * 1、修改采购单头信息
    		*/
    		if(!$model->update($poEidtObj["order"], $poEidtObj["po_id"])){
    			throw new Exception('修改'.'采购单'.$poCode.'单头信息时数据库异常');
    		}
    		 
    		 
    		/*
    		 * 2、修改产品信息
    		*/
    		foreach($productEditObje as $proKey=>$proVal){
    			if(empty($proVal["pop_id"])){
    				throw new Exception('采购单'.$poCode.'明细信息数据缺失！');
    			}
    			
    			$paramId = $proVal["pop_id"];
    			unset($proVal["pop_id"]);
    			
    			if(!Service_PurchaseOrderProduct::update($proVal, $paramId)){
    				throw new Exception('修改'.'采购单'.$poCode.'明细信息时数据库异常');
    			}
    			
    		}
    		 
    		/*
    		 * 3、修改计划库存信息
    		*/
    		foreach($purchaseInven as $invKey=>$invVal){
    			if(empty($invVal["pi_id"])){
    				throw new Exception('修改采购单'.$poCode.'计划库存信息数据缺失！');
    			}
    			 
    			$invId = $invVal["pi_id"];
    			unset($invVal["pi_id"]);
    			 
    			if(!Service_PurchaseInventory::update($invVal, $invId)){
    				throw new Exception('修改采购单'.$poCode.'计划库存信息时数据库异常！');
    			}
    		}
    		   
    		/*
    		 * 4、记录日志
    		 */
    		$rowLog = array(
    				"pol_ref_no"=>$poCode,
    				"pol_aciton_content"=>"撤销交期确认",
    				"pol_action_operator"=>$paramsUserId,
    				"pol_action_date"=>$date,
    		);
    		$log = new Service_PurchaseOrdersLog();
    		$log->add($rowLog);
    		
    		$db->commit();
    		$return = array(
    				'state' => 1,
    				'message'=>array('Success.'),
    				'errorMessage' => ''
    		);
    	
    		return $return;
    	} catch (Exception $e) {
    		$db->rollBack();
    		$return = array(
    				'state' => 0,
    				'poCode' => $poCode,
    				'message'=>array('Fail.'),
    				'errorMessage' => $e->getMessage()
    		);
    		return $return;
    	}
    	
    	
    }
    

    /**
     * 创建采购单
     * @param unknown_type $poHead
     * @param unknown_type $poBody
     * @param unknown_type $supplierProduct
     * @param unknown_type $purchaseInventory
     * @param unknown_type $rowLog
     */
    public static function createPurchaseOrder($poHead=array(),$poBody=array()
    			,$supplierProduct=array(),$purchaseInventory =array(),$rowLog=array(),$userId){
    	
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try {
    		$model = self::getModelInstance();
    		/*
    		 * 1、添加po单头信息
    		 */
    		if(empty($poHead)){
    			throw new Exception('采购单头信息为空，数据异常！');
    		}
    		
    		//add
    		$poId = $model->add($poHead);
    		if(!$poId){
    			throw new Exception('新建采购单头，数据库异常！');
    		}
    		
    		if(empty($poBody)){
    			throw new Exception('采购单身信息为空，数据异常！');
    		}
    		
    		/*
    		 * 2、添加po单身信息
    		*/
    		//为每个product信息添加po_id
    		foreach ($poBody as $key=>$val){
    			$val["po_id"] = $poId;
    			if(!Service_PurchaseOrderProduct::add($val)){
    				throw new Exception('新建采购单身，数据异常！');
    			}
    		}
    		
    		/*
    		 * 3、添加供应商产品信息
    		*/
    		if(!empty($supplierProduct)){
    			Service_PurchaseOrders::eidtSupplierProduct($supplierProduct,$userId);
    		}
    		
    		/*
    		 * 5、添加日志
    		*/
    		foreach($rowLog as $logKey=>$logVal){
    			Service_PurchaseOrdersLog::add($logVal);
    		}
    		$db->commit();
    		$return = array(
    				'state' => 1,
    				'message'=>'Success',
    				'poCode'=>$poHead["po_code"],
    		);
    		return $return;
    	} catch (Exception $e) {
    		$db->rollBack();
    		$return = array(
    				'state' => 0,
    				'message'=>$e->getMessage(),
    		);
    		return $return;
    	}
    	
    	
    	
    	
    	
    	
    	
    	
    }
}