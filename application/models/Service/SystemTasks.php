<?php
/**
 * 系统任务
 * @author Frank
 * @date 2013年11月7日17:52:36
 *
 */
class Service_SystemTasks  extends Common_Service{
	
	
	/**
	 * 检查退款订单，批量创建到仓库进行计费核对
	 */
	public static function batchCreateRefundOrder(){
		$i = 0;
		$startDate = date("Y-m-d H:i:s");
		echo $i + 1 . '、进入创建退款订单服务，时间：'.$startDate.'<br/><br/>';
		$table = new DbTable_RmaOrders();
		$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
		$db = $table->getAdapter();
		$sql = "select t1.rma_id,
				t1.rma_refund_type,
				t1.rma_refrence_no_platform,
				t1.buyer_id,
				t1.rma_amount_total,
				t1.rma_currency,
				t1.rma_verify_date,
				t1.rma_ebay_account,
				t1.rma_payment_account,
				t2.rmap_product_qty,
				t2.rmap_product_id,
				(select tp.product_sku from $wms_db.product tp where tp.product_id = t2.rmap_product_id) as sku,
				(select oo.subtotal from orders oo where oo.refrence_no_platform = t1.rma_refrence_no_platform) as subtotal,
				(select oo.ship_fee from orders oo where oo.refrence_no_platform = t1.rma_refrence_no_platform) as ship_fee,
				(select oo.order_status from orders oo where oo.refrence_no_platform = t1.rma_refrence_no_platform) as order_status,
				(select oo.site from orders oo where oo.refrence_no_platform = t1.rma_refrence_no_platform) as site,
				(select oo.consignee_country from orders oo where oo.refrence_no_platform = t1.rma_refrence_no_platform) as country,
				(select oo.platform from orders oo where oo.refrence_no_platform = t1.rma_refrence_no_platform) as platform ,
				(select oo.warehouse_id from orders oo where oo.refrence_no_platform = t1.rma_refrence_no_platform) as warehouse_id
				from rma_orders t1,rma_order_product t2	
				where t1.rma_id = t2.rma_id	
				and t1.rma_refund_type in (0,1)	
				and t1.rma_status in (3,4)  
				and t1.is_cro = 0 
				order by t1.rma_id desc 
				LIMIT 0,500";
		
		$data = $db->fetchAll($sql);
		if(!empty($data) && count($data) > 0){
			echo $i + 1 . '、有退款数据，封装数据<br/><br/>';
			$refundOrders = array();
			$waitUpdateRma = array();
			foreach ($data as $key => $value) {
				$orderProductArr = array();
				$orderProductArr[] = array(
				            'sku'=>$value['sku'],
				            'buyer_id'=>$value['buyer_id'],
				            'quantity'=>$value['rmap_product_qty'],
				            'refund_amount'=>$value['rma_amount_total'],
							'shipping_fee'=>$value['ship_fee'],
							'sales_amount'=>$value['subtotal'],
							'recv_account'=>$value['rma_payment_account'],
				        );
				//若全额退款，检查订单状态，如果发货了，将退款状态改为1（部分退款）
				if($value['rma_refund_type'] == '0' && in_array($value['order_status'], array(4,5,6,7))){
					$value['rma_refund_type'] = '1';
				}
				$orderKeys = array(
		            'CustomerCode' => 'EC001',
					'refundType'=>$value['rma_refund_type'],
					'warehouse_id' => $value['warehouse_id'],
		            'ReferenceNo' => $value['rma_refrence_no_platform'],
		            'seller_id' => $value['rma_ebay_account'],
		            'site_id' => $value['site'],
		            'Country' => $value['country'],
		            'currencyCode' => $value['rma_currency'], //币种
		            'paydate' => $value['rma_verify_date'], //付款时间
		            'platform' => $value['platform'], //平台代码
		            'createType' => 'api', //创建类型
		            'orderProduct' =>$orderProductArr,
		        );
				$refundOrders[] = $orderKeys;
				$waitUpdateRma[$value['rma_refrence_no_platform']] = $value['rma_id'];
			}
			$tmpNum1 = count($waitUpdateRma);
			echo $i + 1 . '、需创建退款订单数量：'. $tmpNum1 .'<br/><br/>';
			$obj = new Service_OrderForWarehouseProcessNew();
			$result = $obj->batchCreateRefundOrder($refundOrders);
			
			if(!empty($result) && count($result) > 0){
				echo $i + 1 . '、请求得到响应<br/><br/>';
				//创建退款订单成功，标记退款记录为：已创建退款订单
				if($result['state'] == 1){
					$batchUpdateRow = array();
					$tmpNum2 = 0;
					$tmpNum3 = 0;
					foreach ($result['data'] as $dataK => $dataV) {
						$rma_id = $waitUpdateRma[$dataV['referenceNo']];
						
						if($dataV['state'] != 1){
							$updateVal = 2;
							Ec::showError(print_r($dataV,true),'batch_create_refund_order');
							$tmpNum2++;
						}else{
							$updateVal = 1;
							$tmpNum3++;
						}
						$batchUpdateRow[$rma_id] = $updateVal;
					}
					echo $i + 1 . '、创建成功数量：'.$tmpNum3.'创建失败数量：'.$tmpNum2.'<br/><br/>';
				}
				if(count($batchUpdateRow) > 0){
					foreach ($batchUpdateRow as $sqlK => $sqlV) {
						Service_RmaOrders::update(array('is_cro'=>$sqlV), $sqlK,'rma_id');
					}
					$tmpNum4 = count($batchUpdateRow);
					echo $i + 1 . '、本地数据共更新 '.$tmpNum4.' 条<br/><br/>';
				}
			}
			
		}else{
			echo $i + 1 . '、无退款数据，不做操作<br/><br/>';
		}
		$endDate = date("Y-m-d H:i:s");
		echo $i + 1 . '、创建退款订单结束，时间：'.$endDate.'<br/><br/>';
	}
}
