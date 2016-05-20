<?php
class Service_RmaOrders extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_RmaOrders|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_RmaOrders();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }
    
    /**
     * @param $row
     * @return mixed
     */
    public static function addRma($rmaRows,$rmaCancelOrder,$orderNo)
    {
    	$result = array (
    			"ask" => 0,
    			"message" => "操作失败"
    	);
    	$model = self::getModelInstance();
    	$db = $model->getAdapter();
		$db->beginTransaction();
    	try {
	    	foreach ($rmaRows as $k => $val) {
	    		$rowRma = $val;
	    		$rowRmaProduct = $rowRma['rma_product'];
	    		unset($rowRma['rma_product']);
	    		
	    		$resultRma_id = $model->add($rowRma);
		    	$rowRmaProduct['rma_id'] = $resultRma_id;
		    	Service_RmaOrderProduct::add($rowRmaProduct);
	    	}
	    	
	    	if($rmaCancelOrder && $rowRma['rma_refund_type'] == '0' && $rowRmaProduct['rmap_reason_id'] != '3'){
                 
                $status = 7;//问题件
                $reasonType = 4;//截单原因
                $reason = 'RMA全额退款，同时取消订单';  
	    	    
                $warehouseOrderAllow = array('3','4','6');
                $order = Service_Orders::getByField($rowRma['rma_back_order_id'],'order_id');
                
                if(in_array($order['order_status'],$warehouseOrderAllow)){//订单已经到达仓库，进行截单操作
                    $return = Service_OrderProcess::orderCancel($rowRma['rma_back_order_id'],$status,$reasonType,$reason);
                    if($return['ask']==1){//截单成功
                        Service_Orders::update(array("order_status"=>"0"), $rowRma['rma_back_order_id']);
                        $rowOrderLog = array(
                                "ref_id"=>$orderNo,
                                "log_content"=>$reason,
                                "create_time"=>date("Y-m-d H:i:s")
                        );
                        Service_OrderLog::add($rowOrderLog);
                    }else{
                        throw new Exception($return['message']);
                    }
                }else{
                    Service_Orders::update(array("order_status"=>"0"), $rowRma['rma_back_order_id']);
                    $rowOrderLog = array(
                            "ref_id"=>$orderNo,
                            "log_content"=>$reason,
                            "create_time"=>date("Y-m-d H:i:s")
                    );
                    Service_OrderLog::add($rowOrderLog);
                }
	    	     
	    	}
	    	$db->commit();
	    	$result['ask'] = 1;
	    	$result["message"] = "创建成功！";
	    	$result["rma_id"] = $resultRma_id;
    	} catch (Exception $e) {
    		$db->rollback();
    		$result['errorMessage'] = $e->getMessage();
    	}
    	return $result;
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "rma_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "rma_id")
    {
        $model = self::getModelInstance();
        $db = $model->getAdapter();
        try{
			$db->beginTransaction();
	        $model->delete($value, $field);
	        Service_RmaOrderProduct::delete($value,'rma_id');
	        $db->commit();
        }catch(Exception $e){
        	$db->rollBack();
        	throw new Exception('RMA删除失败');
        }
        return true;
    }
    
    /**
     * 获得rma信息，来自组合订单---不支持多重合并订单_(:3」∠)_
     * @param unknown_type $mergerOrderRmaRow		合并订单退款RMA记录
     * @param unknown_type $MergerOrderId			合并订单ID
     * @param unknown_type $rmaSku					选中的sku
     * @param unknown_type $rmaWarehouseSku			选中sku对应仓库SKU
     * @param unknown_type $rmaReason				退件原因
     * @param unknown_type $rmaWarehouseSkuQty		退件数量
     */
    public static function getRmaRowForMergerOrder($mergerOrderRmaRow, $MergerOrderId, $rmaSku, $rmaWarehouseSku, $rmaReason, $rmaWarehouseSkuQty){
//     	print_r($mergerOrderRmaRow);
    	unset($mergerOrderRmaRow['rmaReason']);
    	/*
    	 * 1. 查询被合并订单的详细数据
    	*/
    	$mergeOrderMapCon = array('order_id'=>$MergerOrderId);
    	$resultMergeOrderMap = Service_OrderMergeMap::getByCondition($mergeOrderMapCon);
    	 
    	$rmaSubOrder = array();			//sku所属被合并订单
    	$otherSubOrder = array();		//其他sku所属被合并订单
    	$skuCache = array();			//临时存储用，用来查询sku产品关系
    	foreach ($resultMergeOrderMap as $key1 => $value1) {
    		//查询被合并订单头信息
    		$resutlSubOrder =  Service_Orders::getByField($value1['sub_order_id']);
    	
    		//查询被合并订单的sku信息
    		$orderProductCon = array(
    				'order_id' => $value1['sub_order_id'],
    				'give_up'=>'0'
    		);
    		$orderProducts = Service_OrderProduct::getByCondition($orderProductCon);
    		//查询sku子产品关系
    		foreach ($orderProducts as $key2 => $value2) {
    			if(isset($skuCache[$value2['product_sku']])){
    				$r = $skuCache[$value2['product_sku']];
    			}else{
    				$conn = array('product_sku' => $value2['product_sku']);
    				$r = Service_ProductCombineRelationProcess::getRelation($value2['product_sku'],$resutlSubOrder['user_account'],$resutlSubOrder['company_code']);
    				$skuCache[$value2['product_sku']] = $r;
    			}
    		
    			if($r){
    				$value2['sub_product'] = $r;
    			}
    			$orderProducts[$key2] = $value2;
    		}
    		$resutlSubOrder['order_product'] = $orderProducts;
    	
    		//判断退件SKU所属的被合并订单
    		foreach ($orderProducts as $key3 => $value3) {
    			if($value3['product_sku'] == $rmaSku){
    				$rmaSubOrder = $resutlSubOrder;
    			}else{
    				$otherSubOrder[] = $resutlSubOrder;
    			}
    		}
    	}
    	/*
    	 * 2. 按金额从大到小排序
    	 */
    	$otherSubOrder = self::callBubbleSortForOrderAmounts($otherSubOrder);
//     	print_r($rmaSubOrder);
//     	print_r($otherSubOrder);
    	
    	/*
    	 * 3. 封装RMA行记录
    	 */
    	$rmaRows = array();
    	$rmaErrors = array();
    	$rmaAmountsTotal = $mergerOrderRmaRow['rma_amount_total'];
    	if($mergerOrderRmaRow['rma_refund_type'] == '0'){
    		/*
    		 * 3.1. 全额退款，所有被合并全部进行退款处理
    		 */
    		$rmaMainRow = self::getRmaRowsForMergerOrderBusiness(array(''=>$rmaSubOrder), 
    											$mergerOrderRmaRow, 
    											$rmaAmountsTotal, 
    											'0', 
    											$rmaReason, 
    											$rmaWarehouseSku,
    											$rmaWarehouseSku,
    											$rmaWarehouseSkuQty);
    		if($rmaMainRow['ask'] != '0'){
    			$rmaRows[] = $rmaMainRow['data'][0];
    		}else{
    			$rmaErrors = $rmaMainRow['errors'];
    		}
    		
    		$rmaOtherRow = self::getRmaRowsForMergerOrderBusiness($otherSubOrder, 
    											$mergerOrderRmaRow, 
    											$rmaAmountsTotal, 
    											'0', 
    											$rmaReason, 
    											$rmaWarehouseSku);
    		if($rmaOtherRow['ask'] != '0'){
    			foreach ($rmaOtherRow['data'] as $key4 => $value4) {
    				$rmaRows[] = $value4;
    			}
    		}else{
    			foreach ($rmaOtherRow['errors'] as $key5 => $value5) {
    				$rmaErrors[] = $value5;
    			}
    		}
    	}else{
    		/*
    		 * 3.2 部分退款，判断sku所属订单， 是否足够退款
    		 */
    		$tmp = ($rmaSubOrder['amountpaid'] > $rmaAmountsTotal)?true:false;
    		
    		$rmaMainRow = self::getRmaRowsForMergerOrderBusiness(array(''=>$rmaSubOrder),
												    				$mergerOrderRmaRow,
												    				$rmaAmountsTotal,
												    				$tmp?"1":"0",
												    				$rmaReason,
												    				$rmaWarehouseSku,
												    				$rmaWarehouseSku,
												    				$rmaWarehouseSkuQty);
    		if($rmaMainRow['ask'] != '0'){
    			$rmaRows[] = $rmaMainRow['data'][0];
    		}else{
    			$rmaErrors = $rmaMainRow['errors'];
    		}
    		
    		if(!$tmp){
	    		$rmaAmountsTotal = $rmaAmountsTotal - $rmaSubOrder['amountpaid'];
    			$rmaOtherRow = self::getRmaRowsForMergerOrderBusiness($otherSubOrder, 
					    											$mergerOrderRmaRow, 
					    											$rmaAmountsTotal, 
					    											'1', 
					    											$rmaReason, 
					    											$rmaWarehouseSku);
    			if($rmaOtherRow['ask'] != '0'){
    				foreach ($rmaOtherRow['data'] as $key6 => $value6) {
    					$rmaRows[] = $value6;
    				}
    			}else{
    				foreach ($rmaOtherRow['errors'] as $key7 => $value7) {
    					$rmaErrors[] = $value7;
    				}
    			}
    		}
    	}
    	
    	$return = array(
            		'ask' => 0,
    				'data' =>'',
    				'errors'
	            );
    	if(count($rmaErrors) > 0){
    		$return['errors'] = $rmaErrors;
    	}else{
    		$return['ask'] = 1;
    		$return['data'] = $rmaRows;
    	}
    	return $return;
    }
    
    /**
     * 获得rma信息，来自组合订单---支持多重合并订单o(*≧▽≦)ツ 
     * @param unknown_type $mergerOrderRmaRow		合并订单退款RMA记录
     * @param unknown_type $MergerOrderId			合并订单ID
     * @param unknown_type $rmaSku					选中的sku
     * @param unknown_type $rmaWarehouseSku			选中sku对应仓库SKU
     * @param unknown_type $rmaReason				退件原因
     * @param unknown_type $rmaWarehouseSkuQty		退件数量
     */
    public static function getRmaRowForMergerOrders($mergerOrderRmaRow, $MergerOrderId, $rmaSku, $rmaWarehouseSku, $rmaReason, $rmaWarehouseSkuQty){
    	//     	print_r($mergerOrderRmaRow);
    	unset($mergerOrderRmaRow['rmaReason']);
    	/*
    	 * 1. 查询被合并订单的Item信息
    	*/
    	$mergeOrderMapCon = array('order_id'=>$MergerOrderId,'give_up'=>0);
    	$resultMergeOrderProductMap = Service_OrderProduct::getByCondition($mergeOrderMapCon);
		
    	//检查是order_product记录上面是否存在原始的ebay订单号
    	foreach ($resultMergeOrderProductMap as $key0 => $value0) {
			if(empty($value0['OrderIDEbay'])){
				return array('ask' => 0,'errors'=>array('该合并订单，查询原始订单信息遇到困难，请联系技术支持！'));
			}
    	}
    	
    	$rmaSubOrder = array();			//sku所属被合并订单
    	$otherSubOrder = array();		//其他sku所属被合并订单
    	$skuCache = array();			//临时存储用，用来查询sku产品关系
    	foreach ($resultMergeOrderProductMap as $key1 => $value1) {
    		//查询被合并订单Itemde ,单头信息
    		$resutlSubOrder = Service_Orders::getByField($value1['OrderIDEbay'],'refrence_no_platform');
    		
    		//查询被合并原始订单的sku信息
    		$orderProducts = array($value1);
    		//查询sku子产品关系
    		foreach ($orderProducts as $key2 => $value2) {
    			if(isset($skuCache[$value2['product_sku']])){
    				$r = $skuCache[$value2['product_sku']];
    			}else{
    				$conn = array('product_sku' => $value2['product_sku']);
    				$r = Service_ProductCombineRelationProcess::getRelation($value2['product_sku'],$resutlSubOrder['user_account'],$resutlSubOrder['company_code']);
    				$skuCache[$value2['product_sku']] = $r;
    			}
    
    			if($r){
    				$value2['sub_product'] = $r;
    			}
    			$orderProducts[$key2] = $value2;
    		}
    		$resutlSubOrder['order_product'] = $orderProducts;
    		 
    		//判断退件SKU所属的被合并订单
    		foreach ($orderProducts as $key3 => $value3) {
    			if($value3['product_sku'] == $rmaSku){
    				$rmaSubOrder = $resutlSubOrder;
    			}else{
    				$otherSubOrder[] = $resutlSubOrder;
    			}
    		}
    	}
    	
    	/*
    	 * 2. 按金额从大到小排序
    	*/
    	$otherSubOrder = self::callBubbleSortForOrderAmounts($otherSubOrder);
// 		print_r($rmaSubOrder);
// 		print_r($otherSubOrder);
// 		exit;
    	 
    	/*
    	 * 3. 封装RMA行记录
    	*/
    	$rmaRows = array();
    	$rmaErrors = array();
    	$rmaAmountsTotal = $mergerOrderRmaRow['rma_amount_total'];
    	if($mergerOrderRmaRow['rma_refund_type'] == '0'){
    		/*
    		 * 3.1. 全额退款，所有被合并全部进行退款处理
    		*/
    		$rmaMainRow = self::getRmaRowsForMergerOrderBusiness(array(''=>$rmaSubOrder),
    				$mergerOrderRmaRow,
    				$rmaAmountsTotal,
    				'0',
    				$rmaReason,
    				$rmaWarehouseSku,
    				$rmaWarehouseSku,
    				$rmaWarehouseSkuQty);
    		if($rmaMainRow['ask'] != '0'){
    			$rmaRows[] = $rmaMainRow['data'][0];
    		}else{
    			$rmaErrors = $rmaMainRow['errors'];
    		}
    
    		$rmaOtherRow = self::getRmaRowsForMergerOrderBusiness($otherSubOrder,
    				$mergerOrderRmaRow,
    				$rmaAmountsTotal,
    				'0',
    				$rmaReason,
    				$rmaWarehouseSku);
    		if($rmaOtherRow['ask'] != '0'){
    			foreach ($rmaOtherRow['data'] as $key4 => $value4) {
    				$rmaRows[] = $value4;
    			}
    		}else{
    			foreach ($rmaOtherRow['errors'] as $key5 => $value5) {
    				$rmaErrors[] = $value5;
    			}
    		}
    	}else{
    		/*
    		 * 3.2 部分退款，判断sku所属订单， 是否足够退款
    		*/
    		$tmp = ($rmaSubOrder['amountpaid'] > $rmaAmountsTotal)?true:false;
    
    		$rmaMainRow = self::getRmaRowsForMergerOrderBusiness(array(''=>$rmaSubOrder),
    				$mergerOrderRmaRow,
    				$rmaAmountsTotal,
    				$tmp?"1":"0",
    				$rmaReason,
    				$rmaWarehouseSku,
    				$rmaWarehouseSku,
    				$rmaWarehouseSkuQty);
    		if($rmaMainRow['ask'] != '0'){
    			$rmaRows[] = $rmaMainRow['data'][0];
    		}else{
    			$rmaErrors = $rmaMainRow['errors'];
    		}
    
    		if(!$tmp){
    			$rmaAmountsTotal = $rmaAmountsTotal - $rmaSubOrder['amountpaid'];
    			$rmaOtherRow = self::getRmaRowsForMergerOrderBusiness($otherSubOrder,
    					$mergerOrderRmaRow,
    					$rmaAmountsTotal,
    					'1',
    					$rmaReason,
    					$rmaWarehouseSku);
    			if($rmaOtherRow['ask'] != '0'){
    				foreach ($rmaOtherRow['data'] as $key6 => $value6) {
    					$rmaRows[] = $value6;
    				}
    			}else{
    				foreach ($rmaOtherRow['errors'] as $key7 => $value7) {
    					$rmaErrors[] = $value7;
    				}
    			}
    		}
    	}
    	 
    	$return = array(
    			'ask' => 0,
    			'data' =>'',
    			'errors'
    	);
    	if(count($rmaErrors) > 0){
    		$return['errors'] = $rmaErrors;
    	}else{
    		$return['ask'] = 1;
    		$return['data'] = $rmaRows;
    	}
    	return $return;
    }
    
    
    /**
     * 根据订单金额，执行冒泡排序
     * 降序，按金额从大到小排序
     * @param unknown_type $orderArr
     */
    private static function callBubbleSortForOrderAmounts($orderArr){

    	$len = count($orderArr);
    	for ($i = 1; $i < $len; $i++) {
    		for ($j = $len -1 ; $j >= $i; $j--) {
    			if($orderArr[$j]['amountpaid'] > $orderArr[$j-1]['amountpaid']){
	    			$x=$orderArr[$j];
	    			$orderArr[$j]=$orderArr[$j-1];
	    			$orderArr[$j-1]=$x;
    			}
    		}
    	}
    	
    	return $orderArr;
    }
    
    /**
     * 根据退款类型及金额，封装RMA行记录返回
     * @param unknown_type $orderArr					订单数组
     * @param unknown_type $mergerOrderRmaRow			组合订单RMA信息
     * @param unknown_type $rmaMergerOrderAmounts		所需退款金额
     * @param unknown_type $rmaMergerOrderRefundType	退款类型
     * @param unknown_type $rmaReason					退款原因
     * @param unknown_type $rmaMergerOrderWarehouseSku	退件选择的sku
     * @param unknown_type $rmaStatisticsSku			统计用sku
     */
    private static function getRmaRowsForMergerOrderBusiness($orderArr, $mergerOrderRmaRow, $rmaMergerOrderAmounts, $rmaMergerOrderRefundType, $rmaReason, $rmaMergerOrderWarehouseSku, $rmaStatisticsSku = '', $rmaStatisticsSkuQty = '1'){
    	$return = array(
    			'ask'=>'1',
    			'data'=>'',
    			'errors'=>''
    			);
    	$rmaRows = array();								//放rma信息的数组
    	$residualAmount = $rmaMergerOrderAmounts;		//剩余金额，每次封装rma记录后，进行计算剩余退款金额，最后应该为0
    	$rmaErrors = array();							//用来放错误信息的集合
    	foreach($orderArr as $keyOrder => $value_order) {

    		$rmaRow = $mergerOrderRmaRow;
    		
    		/*
    		 * 1. 获得需要验证的仓库sku
    		 */
    		$warehouseSku = $rmaMergerOrderWarehouseSku;		//退件选择的sku,不变的
    		$statisticsSku = '';								//统计用sku
    		if(!empty($rmaStatisticsSku)){
    			/*
    			 * 2.1 有传入仓库sku，直接使用 
    			 */
    			$statisticsSku = $rmaStatisticsSku;
    		}else{
    			/*
    			 * 2.2  在订单中拿任意一个sku去仓库(优先使用，产品关系中的sku，若没有维护产品关系，直接使用订单产品中的sku)
    			 */
    			$isExistSubProduct = false;
    			foreach ($value_order['order_product'] as $key_product => $value_product){
    				if(isset($value_product['sub_product'])){
    					$statisticsSku = $value_product['sub_product'][0]['pcr_product_sku'];
    					$isExistSubProduct = true;
    					break;
    				}
    			}
    			
    			if(!$isExistSubProduct){
    				$statisticsSku = $value_order['order_product'][0]['product_sku'];
    			}
    		}
//     		print_r($warehouseSku);
//     		echo '---';
//     		print_r($statisticsSku);
    		
    		
    		/*
    		 * 3.1 验证退件选择的sku是否属于仓库sku
    		 */
//     		$warehouseProduct = Service_Product::getByField($warehouseSku,'product_sku');
//     		if(!empty($warehouseProduct)){
//     			$warehouseProductId = $warehouseProduct['product_id'];			//skuID
//     		}
    		$warehouseProductId = '';
    		$warehouseProduct = Service_Product::getByCondition(array('product_sku'=>$warehouseSku,'company_code'=>$value_order['company_code']));
    		if(!empty($warehouseProduct)){
    			$warehouseProductId = $warehouseProduct[0]['product_id'];
    		}
			else
			{
    			//SKU未维护到仓库
    			$rmaErrors[] = "SKU:<span style='color:Red;'>$warehouseSku</span> 未维护到仓库";
    		}
    		
    		/*
    		 * 3.2 验证统计用sku是否属于仓库sku
    		 */
//     		$statisticsProduct = Service_Product::getByField($statisticsSku,'product_sku');
//     		$statisticsProductId = '';
//     		if(!empty($statisticsProduct)){
//     			$statisticsProductId = $statisticsProduct['product_id'];		//skuID
//     		}
    		$statisticsProductId = '';
    		$statisticsProduct = Service_Product::getByCondition(array('product_sku'=>$warehouseSku,'company_code'=>$value_order['company_code']));
    		if(!empty($statisticsProduct)){
    			$statisticsProductId = $statisticsProduct[0]['product_id'];
    		}
    		else
    		{
    			//SKU未维护到仓库
    			$rmaErrors[] = "SKU:<span style='color:Red;'>$statisticsSku</span> 未维护到仓库";
    		}    		
//     		echo '=';
// 			print_r($warehouseProductId);
// 			echo '---';
// 			print_r($statisticsProductId);
// 			exit;
    		
    		/*
    		 * 4. 查看订单，所属paypal交易ID，是否能找到对应的paypal账户
    		 */
    		$orderPayCon = array(
    				'OrderID'=>$value_order['refrence_no_platform'],
    				'unPaymentstatus'=>'Failed'
    				);
    		$ebayOrderPayment = Service_EbayOrderPayment::getByCondition($orderPayCon);
    		$paypalTransationId = $ebayOrderPayment[0]['referenceid'];
//     		$ebayOrderPayment = Service_EbayOrderPayment::getByField($value_order['refrence_no_platform'],'OrderID');
//     		$paypalTransationId = $ebayOrderPayment['referenceid'];
    		$resultPaypalTransaction = Service_PaypalTransation::getByField($paypalTransationId,'paypal_tid');
    		if(!empty($resultPaypalTransaction)){
    			$rmaRow['rma_pay_ref_id']	=	$paypalTransationId;
    			$rmaRow['rma_currency'] = $resultPaypalTransaction['currency'];
    			$rmaRow['rma_payment_account'] = $resultPaypalTransaction['recv_account'];
    			$rmaRow['rma_receiving_account'] = $resultPaypalTransaction['pay_account'];
    		}else{
    							
    			$rmaErrors[]= "Paypal交易号：<span style='color:Red;'>$paypalTransationId</span> ,没有完整的交易信息，可前往“<span style='color:green;'>下载paypal交易记录</span>”页面，下载交易记录.";
    		}
    		
    		/*
    		 * 5. 判断退款类型和金额，封装订单的退款类型
    		 */
    		if($rmaMergerOrderRefundType == '0'){
    			//全额退款，直接使用订单金额
    			$rmaRow['rma_refund_type'] = '0';
    			$rmaRow['rma_amount_total'] = $value_order['amountpaid'];
    		}else{
    			//部分退款，判断剩余退款金额，与订单金额的大小关系
    			if($residualAmount > $value_order['amountpaid']){
    				//剩余退款金额，大于订单金额，该订单全额退款
    				$rmaRow['rma_refund_type'] = '0';
    				$rmaRow['rma_amount_total'] = $value_order['amountpaid'];
	    			$residualAmount = $residualAmount - $value_order['amountpaid'];
    			}else{
    				//剩余退款金额，小于订单金额，该订单部分退款
    				$rmaRow['rma_refund_type'] = '1';
    				$rmaRow['rma_amount_total'] = $residualAmount;
    				$residualAmount = $residualAmount - $residualAmount;
    			}
    		}
    		
    		/*
    		 * 6. 封装子单的
    		 */
    		$rmaRow['rma_refrence_no_platform'] = $value_order['refrence_no_platform'];	//平台参考号，记录被合并订单的单号
    		$rmaRow['buyer_id'] = $value_order['buyer_id'];
    		$rmaRow['rma_ebay_account'] = $value_order['user_account'];
    		
    		$rmaProduct = array();
    		$rmaProduct['rmap_reason_id'] = $rmaReason;
    		$rmaProduct['rmap_product_id'] = $warehouseProductId;
    		$rmaProduct['rmap_product_qty'] = $rmaStatisticsSkuQty;
    		$rmaProduct['rmap_statistics_product_id'] = $statisticsProductId;
    		$rmaProduct['rmap_amount_total'] = $rmaRow['rma_amount_total'];
    		$rmaRow['rma_product'] = $rmaProduct;
    		//将rma行记录，放入数组中
    		$rmaRows[] = $rmaRow;
    		
    		/*
    		 * 7. 当剩余退款金额为0时，代表不用再次循环，跳出循环
    		 */
    		if($residualAmount == 0){
    			break;
    		}
    	}
		if(count($rmaErrors) > 0){
			$return['ask'] = '0';
			$return['errors'] = $rmaErrors;
		}else{
			$return['data'] = $rmaRows;
		}
    	
    	return $return;
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'rma_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionBak($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionBak($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionForView($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionForView($condition, $type, $pageSize, $page, $order);
    }
    
    /**
     * paypal退款查询
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByConditionForRefund($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
    	$model = self::getModelInstance();
    	return $model->getByConditionForRefund($condition, $type, $pageSize, $page, $order);
    }    

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        $validateArr[] = array("name" =>EC::Lang('退件原因'), "value" =>$val["rmaReason"], "regex" => array("require",));
        //当退件原因为‘退件重发’时，退款类型可以不用验证
        if($val['rmaReason'] != '3'){
	        $validateArr[] = array("name" =>EC::Lang('退款类型'), "value" =>$val["rma_refund_type"], "regex" => array("require",));
	        $validateArr[] = array("name" =>EC::Lang('退款金额'), "value" =>$val["rma_amount_total"], "regex" => array("require","positive1","positive2","positive3"));
        }
        $validateArr[] = array("name" =>EC::Lang('创建人'), "value" =>$val["rma_creator_id"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('币种'), "value" =>$val["rma_currency"], "regex" => array("require",));
//         $validateArr[] = array("name" =>EC::Lang('收款账户'), "value" =>$val["rma_receiving_account"], "regex" => array("require","email"));
//         $validateArr[] = array("name" =>EC::Lang('PayPal交易ID'), "value" =>$val["rma_pay_ref_id"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('原单号'), "value" =>$val["rma_back_order_id"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'rma_id',
              'E1'=>'rma_create_date',
              'E2'=>'rma_verify_date',
              'E3'=>'rma_creator_id',
              'E4'=>'rma_verifyor_id',
              'E5'=>'rma_case_ref_id',
              'E6'=>'rma_case_type',
              'E7'=>'rma_common',
              'E8'=>'rma_amount_total',
              'E9'=>'rma_currency',
              'E10'=>'rma_receiving_account',
              'E11'=>'rma_sync_time',
              'E12'=>'rma_pay_ref_id',
              'E13'=>'rma_back_order_id',
              'E14'=>'rma_status',
        	  'E15'=>'rma_refund_type',
        	  'E16'=>'rma_payment_account',
        	  'E17'=>'rma_ebay_account',
        	  'E18'=>'rma_submit_date',
        	  'E19'=>'rma_note',
        	  'E20'=>'buyer_id',
        	  'E21'=>'rma_refrence_no_platform',
        	  'E22'=>'rma_sync_message',
        	  'E23'=>'company_code'
        );
        return $row;
    }

}