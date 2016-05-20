<?php
class Service_ReturnOrdersProcess extends Common_Service
{

	/**
	 * 新增退件订单
	 * @param unknown_type $orders
	 * @param unknown_type $return_order_row
	 * @param unknown_type $return_order_product_row
	 */
	public static function addReturnOrders($orders ,$return_order_row ,$return_order_product_rows){
		$return = array(
				'ask'=>0,
				'message'=>''
				);
		/*
		 * 1. 转换参数，准备直连仓库API
		 */
		$ec_order = array(
				'orderCode'=>$return_order_row['refrence_no_warehouse'],
				'description'=>$return_order_row['ro_note'],
				'etaDate'=>$return_order_row['expected_date'],
				'processType'=>$return_order_row['ro_process_type'],//1，退件入库 2，退件重发
				'type'=>$return_order_row['ro_type'],				//1:物流 2:订单信息 3:其它
				);
		$ec_order_product = array();
		foreach ($return_order_product_rows as $key => $value) {
			$row = array(
				'sku' => $value['product_barcode'],
				'qty' => $value['rop_quantity'],
				'processInstruction' => $value['exception_process_instruction'], //异常处理指令
				'description' => $value['rop_note']
					);
			$ec_order_product[] = $row;
		}
		
		$orderArray = array(
				'order' => $ec_order,
				'items' => $ec_order_product,
				'operationType' => 0, //是否操作收货 0:否 1:是
		);
		
		/*
		 * 2.直连仓库API
		 */
		$obj = new Service_OrderForWarehouseProcessNew();//$orderArray
  		$response = $obj->createReturnOrder($orderArray);
  		if($response['state']){
//   			$response['orderCode'];
//   			$response['message'];
  			$return_order_row['receiving_code'] = $response['asnCode'];
  			$return_order_row['ro_code'] = $response['rmaCode'];
  			
  			/*
  			 * 3.创建EB系统退件订单
  			 */
  			$db = Common_Common::getAdapter();
  			try{
  				$db->beginTransaction();
  				$ro_id = Service_ReturnOrders::add($return_order_row);
  				if($ro_id){
  					foreach ($return_order_product_rows as $key2 => $value2) {
  						$value2['ro_id'] = $ro_id;
  						Service_ReturnOrderProduct::add($value2);
  					}
	  				$db->commit();
	  				$return['ask'] = 1;
	  				$return['rma_code'] = $response['rmaCode'];
  				}else{
  					$db->rollback();
  					$return['message'] = '创建失败(若多次失败，请联系技术支持！)';
  				}
  			}catch (Exception $e){
  				$db->rollback();
  			}
  		}else{
	  		$return['message'] = $response['message'];
  			
  		}
  		return $return;
	}
}