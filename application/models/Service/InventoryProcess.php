<?php
class Service_InventoryProcess
{
	/**
	 * 调整库存--需选择批次库存，才能进行调整
	 * @author solar
	 * @param int $ib_id
	 * @param int $new_quantity
	 * @param string $reason
	 * return true|string
	 */
	public function adjustInventory($ib_id, $new_quantity, $reason) {
		$new_quantity = intval($new_quantity);
		$db =  Common_Common::getAdapter();
		$db->beginTransaction();
		try{
			$ibRow = Service_InventoryBatch::getForUpdate($ib_id);	//锁行
			if(empty($ibRow)) throw new Exception('批次库存不存在');
			if(intval($ibRow['ib_hold_status']) !== 0) throw new Exception('锁定的批次库存不能调整');
			$outboundNum = Service_InventoryBatchOutbound::sumQuantity($ib_id);
			if($new_quantity < $outboundNum) throw new Exception('新库存最小只能调整到'.$outboundNum);
			if($new_quantity == 0) {
				Service_InventoryBatch::delete($ib_id);
			} else {
				//更新批次库存
				$ibUpdate['ib_quantity'] = $new_quantity;
				$ibUpdate['ib_update_time'] = date('Y-m-d H:i:s');
				//if($new_quantity == 0) $ibUpdate['ib_status'] = 0;
				Service_InventoryBatch::update($ibUpdate, $ib_id);
			}
			//批次库存日志
			$note = '库存调整：'.$reason;
			Service_InventoryBatchLog::log($ibRow, $ibRow['ib_quantity'], $new_quantity, $note);
			//产品库存
			$aProductInventory = Service_ProductInventory::getByWhProduct($ibRow['warehouse_id'], $ibRow['product_id']);
			$piRow = Service_ProductInventory::getForUpdate($aProductInventory['pi_id']);	//锁行
			$new_sellable = $new_quantity - $ibRow['ib_quantity'] + $piRow['pi_sellable'];
			if($new_sellable < 0) throw new Exception('库存异常，调整失败');
			$piUpdate['pi_sellable'] = $new_sellable;
			$piUpdate['pi_update_time'] = date('Y-m-d H:i:s');
			Service_ProductInventory::update($piUpdate, $piRow['pi_id']);
			//产品库存日志
			$piLog['product_id'] = $ibRow['product_id'];
			$piLog['product_barcode'] = $ibRow['product_barcode'];
			$piLog['warehouse_id'] = $ibRow['warehouse_id'];
			$piLog['reference_code'] = $ibRow['ib_id'];
			$piLog['user_id'] = Service_User::getUserId();
			$piLog['pil_onway'] = $piRow['pi_onway'];
			$piLog['pil_pending'] = $piRow['pi_pending'];
			$piLog['pil_sellable'] = $piRow['pi_sellable'];
			$piLog['pil_unsellable'] = $piRow['pi_unsellable'];
			$piLog['pil_reserved'] = $piRow['pi_reserved'];
			$piLog['pil_shipped'] = $piRow['pi_shipped'];			
			$piLog['pil_quantity'] = $new_sellable - $piRow['pi_sellable'];
			$piLog['pil_add_time'] = date('Y-m-d H:i:s');
			$piLog['pil_ip'] = $ibRow['product_id'];
			$piLog['pil_note'] = '批次库存['.$ibRow['ib_id'].']调整，可用库存从'.$piRow['pi_sellable'].'变为'.$new_sellable;
			Service_ProductInventoryLog::add($piLog);	
			//提交事务
			$db->commit();
		}catch(Exception $e){
			$db->rollback();
			return $e->getMessage();
		}
		return true;
	}
	
	/**
	 * 调整库存--自动调整批次库存
	 * @param unknown_type $productInventoryId
	 * @param unknown_type $newQty
	 * @param unknown_type $reason
	 */
	public function adjustInventorySpecial($productInventoryId,$newQty,$reason){
		$return = array (
				'state' => 0,
				'message' => ''
		);
		
		/*
		 * 1. 检查库存是否存在
		 */
		$productInventorycon = array(
				'company_code'=>Common_Company::getCompanyCode(),
				'pi_id'=>$productInventoryId
		);
		$resultProductInventory = Service_ProductInventory::getByCondition($productInventorycon);
		
		if(empty($resultProductInventory)){
			$return['message'] = '未能找到该库存信息，请重新操作'; 
			return $return;
		}
		
		/*
		 * 2.查询该库存的所有批次
		 */
		$productInventoryOldRow =  $resultProductInventory['0'];
		$inventoryBatchCon = array(
				'company_code'=>Common_Company::getCompanyCode(),
				'product_barcode'=>$productInventoryOldRow['product_barcode'],
				'warehouse_id'=>$productInventoryOldRow['warehouse_id']
				);
		$resultInventoryBatch = Service_InventoryBatch::getByCondition($inventoryBatchCon);
		if(empty($resultInventoryBatch)){
			$return['message'] = '未能找到批次库存信息，无法操作';
			return $return;
		}
		
		/*
		 * 2.1 根据批次库存数量，进行降序排列，方便下面的更新操作
		 */
		$IbParamsArr = array();
		$IbRow = array();
		foreach ($resultInventoryBatch as $key => $value) {
			$IbParamsArr[$value['ib_id']] = $value['ib_quantity'];
			$IbRow[$value['ib_id']] = $value;
		}
		//降序排列
		$IbParamsArr = array_flip($IbParamsArr);
		krsort($IbParamsArr);
		$IbParamsArr = array_flip($IbParamsArr);
		
		
		/*
		 * 2.2 判断待出库存
		 */
		$outboundNum = 0;
		foreach ($IbParamsArr as $keyId => $valueQty) {
			$outboundNum = $outboundNum + Service_InventoryBatchOutbound::sumQuantity($keyId);
		}
		
		if($newQty < $outboundNum){
			$return['message'] = '新库存最小只能调整到'.$outboundNum;
			return $return;
		}
		
		/*
		 * 3.判断批次库存更新方式，并更新
		 * 		a、减少库存，直接更新某一个批次库存的数量，在更新库存数量		
		 * 		b、增加库存，需要计算平均采购价，然后插入新的批次库存，在更新库存数量
		 * 		
		 */
		$db = Common_Common::getAdapter();
		$db->beginTransaction();
		
		try{
			$oldInventoryQty = intval($productInventoryOldRow['pi_sellable']) + intval($productInventoryOldRow['pi_reserved']);
			if($oldInventoryQty > $newQty){
				/*
				 * A. 减少库存
				 */
				if($newQty == 0){
					foreach ($IbParamsArr as $keyZero => $valueZero) {
						$IbParamsArr[$keyZero] = 0;
					}
				}else{
					$abatementNum = $oldInventoryQty - $newQty;
					foreach ($IbParamsArr as $keyAn => $valueAn) {
						if($valueAn > $abatementNum){
							$IbParamsArr[$keyAn] = $valueAn - $abatementNum;
							break;
						}else if($valueAn == $abatementNum){
							$IbParamsArr[$keyAn] = 0;
							break;
						}else if($valueAn < $abatementNum){
							$IbParamsArr[$keyAn] = 0;
							$abatementNum = $abatementNum - $valueAn;
						}
					}	
				}
				
				//更新批次库存中的数量
				
				foreach ($IbParamsArr as $keyUp1 => $valueUp1) {
					//添加批次库存日志
					$note = '库存调整：'.$reason;
					Service_InventoryBatchLog::log($IbRow[$keyUp1], $IbRow[$keyUp1]['ib_quantity'], $valueUp1, $note);
					
					//数量为0，删除批次库存
					if($valueUp1 == 0){
						Service_InventoryBatch::delete($keyUp1);
					}else if($IbRow[$keyUp1]['ib_quantity'] != $valueUp1){
						//更新批次库存
						$IbRow[$keyUp1]['ib_quantity'] = $valueUp1;
						$IbRow[$keyUp1]['ib_update_time'] = date('Y-m-d H:i:s');
						Service_InventoryBatch::update($IbRow[$keyUp1], $keyUp1);
					}
				}
				
			}else{
				/*
				 * B. 增加库存
				 */
				//计算采购平均价
				$purchaseTotalPic = 0.00;
				foreach ($resultInventoryBatch as $keyAd => $valueAd) {
					$valueAd['purchase_price'];
					$purchaseTotalPic = bcadd($valueAd['purchase_price'], $purchaseTotalPic,2);
				}
				//计算新增多少库存
				$addNum = $newQty - $oldInventoryQty;
				$purchasePic = bcdiv($purchaseTotalPic, count($resultInventoryBatch),2);
				
				$addIbRow = $resultInventoryBatch[0];
				$addIbRow['ib_id'] = '';
				$addIbRow['reference_no'] = '';
				$addIbRow['receiving_code'] = '';
				$addIbRow['po_code'] = '';
				$addIbRow['receiving_id'] = '';
				
				$addIbRow['ib_quantity'] = $addNum;
				$addIbRow['purchase_price'] = $purchasePic;
				$addIbRow['ib_note'] = '库存调整';
				$date_update = date('Y-m-d H:i:s');
				$addIbRow['ib_fifo_time'] = $date_update;
				$addIbRow['ib_add_time'] = $date_update;
				$addIbRow['ib_update_time'] = $date_update;
				$addIbRow['ib_type'] = 0;
				$addIbRow['ib_status'] = 1;
				$addIbRow['ib_hold_status'] = 0;
				
				//添加批次库存
				$add_ib_id = Service_InventoryBatch::add($addIbRow);
				
				//添加批次库存日志
				$addIbRow['ib_id'] = $add_ib_id;
				$note = '库存调整：'.$reason;
				Service_InventoryBatchLog::log($addIbRow,0, $addNum, $note);
				
			}
			
			/*
			 * 4. 更新产品库存
			 */
			$new_sellable = $newQty - $productInventoryOldRow['pi_reserved'];
			if($new_sellable < 0){
				throw new Exception('库存异常，调整失败');
			}
			$piUpdate = $productInventoryOldRow;
			$piUpdate['pi_sellable'] = $new_sellable;
			$piUpdate['pi_update_time'] = date('Y-m-d H:i:s');
			Service_ProductInventory::update($piUpdate, $piUpdate['pi_id']);
			//产品库存日志
			
			$piLog['product_id'] = $piUpdate['product_id'];
			$piLog['product_barcode'] = $piUpdate['product_barcode'];
			$piLog['warehouse_id'] = $piUpdate['warehouse_id'];
			$piLog['reference_code'] = '';
			$piLog['user_id'] = Service_User::getUserId();
			$piLog['pil_onway'] = $productInventoryOldRow['pi_onway'];
			$piLog['pil_pending'] = $productInventoryOldRow['pi_pending'];
			$piLog['pil_sellable'] = $productInventoryOldRow['pi_sellable'];
			$piLog['pil_unsellable'] = $productInventoryOldRow['pi_unsellable'];
			$piLog['pil_reserved'] = $productInventoryOldRow['pi_reserved'];
			$piLog['pil_shipped'] = $productInventoryOldRow['pi_shipped'];
			$piLog['pil_quantity'] = $new_sellable - $productInventoryOldRow['pi_sellable'];
			$piLog['pil_add_time'] = date('Y-m-d H:i:s');
			$piLog['pil_ip'] = '';
			$piLog['pil_note'] = '批次库存调整，可用库存从'.$productInventoryOldRow['pi_sellable'].'变为'.$new_sellable;
			Service_ProductInventoryLog::add($piLog);
			
			$return['state'] = 1;
			$return['message'] = "库存调整成功";
			$db->commit();
		}catch(Exception $e){
			$db->rollback();
			return $return['message'] = $e->getMessage();
		}
		return $return;
	}
	
	/**
	 * 移动货架
	 * @author solar
	 * @param int $ib_id
	 * @param string $new_location
	 * @param int $move_quantity
	 * @param string $reason
	 * @return true|string
	 */
	public function moveLocation($ib_id, $new_location, $move_quantity, $reason) {
		$db =  Common_Common::getAdapter();
		$db->beginTransaction();
		try{
			$ibRow = Service_InventoryBatch::getForUpdate($ib_id);	//锁行
			if(empty($ibRow)) throw new Exception('批次库存不存在');
			if(intval($ibRow['ib_hold_status']) !== 0) throw new Exception('锁定的批次库存不能移货架');
			if($move_quantity > $ibRow['ib_quantity']) throw new Exception('移动数量不能大于当前库存');
			if($ibRow['lc_code'] == $new_location) throw new Exception('当前货架和目标货架一样，不用移动');
			$aLocation = Service_Location::getByWhere(array('warehouse_id'=>$ibRow['warehouse_id'],'lc_code'=>$new_location));
			if(empty($aLocation)) throw new Exception('目标货架['.$new_location.']不存在');
			if($aLocation['lc_status']==0) throw new Exception('目标货架['.$new_location.']不可用');
			$lcIbRow = Service_InventoryBatch::getByWhere(array('warehouse_id'=>$ibRow['warehouse_id'],'lc_code'=>$new_location));
			if(!empty($lcIbRow) && $lcIbRow['product_id'] != $ibRow['product_id']) {
				throw new Exception('目标货架上的产品和当前货架的产品不一致');
			}
			if($move_quantity == $ibRow['ib_quantity']) {
				//直接修改货位
				$ibUpdate['lc_code'] = $new_location;
				$ibUpdate['ib_update_time'] = date('Y-m-d H:i:s');
				Service_InventoryBatch::update($ibUpdate, $ibRow['ib_id']);
				//日志
				$note = '从货架['.$ibRow['lc_code'].']移到货架['.$new_location.']，原因：'.$reason;
				Service_InventoryBatchLog::log($ibRow, $ibRow['ib_quantity'], $ibRow['ib_quantity'], $note);
			} else {
				$outboundNum = Service_InventoryBatchOutbound::sumQuantity($ib_id);
				$available = $ibRow['ib_quantity'] - $outboundNum;
				if($move_quantity > $available) throw new Exception('如果不是全部移动，最多只能移动'.$available);
				//新建一条批次库存
				$ibNew['lc_code'] = $new_location;
				$ibNew['product_id'] = $ibRow['product_id'];
				$ibNew['box_code'] = $ibRow['box_code'];
				$ibNew['product_barcode'] = $ibRow['product_barcode'];
				$ibNew['reference_no'] = $ibRow['reference_no'];
				$ibNew['application_code'] = $ibRow['application_code'];
				$ibNew['supplier_id'] = $ibRow['supplier_id'];
				$ibNew['warehouse_id'] = $ibRow['warehouse_id'];
				$ibNew['receiving_code'] = $ibRow['receiving_code'];
				$ibNew['receiving_id'] = $ibRow['receiving_id'];
				$ibNew['po_code'] = $ibRow['po_code'];
				$ibNew['lot_number'] = $ibRow['lot_number'];
				$ibNew['ib_status'] = $ibRow['ib_status'];
				$ibNew['ib_hold_status'] = $ibRow['ib_hold_status'];
				$ibNew['ib_quantity'] = $move_quantity;
				$ibNew['ib_fifo_time'] = $ibRow['ib_fifo_time'];
				$ibNew['ib_note'] = $ibRow['ib_note'];
				$ibNew['ib_add_time'] = date('Y-m-d H:i:s');
				Service_InventoryBatch::add($ibNew);
				//更新原来批次库存的数量
				$new_quantity = $ibRow['ib_quantity'] - $move_quantity;
				$ibUpdate['ib_quantity'] = $new_quantity;
				$ibUpdate['ib_update_time'] = date('Y-m-d H:i:s');
				Service_InventoryBatch::update($ibUpdate, $ibRow['ib_id']);
				//日志
				$note = '从货架['.$ibRow['lc_code'].']移'.$move_quantity.'到货架['.$new_location.']，原因：'.$reason;
				Service_InventoryBatchLog::log($ibRow, $ibRow['ib_quantity'], $new_quantity, $note);
			}
			//提交事务
			$db->commit();
		}catch(Exception $e){
			$db->rollback();
			return $e->getMessage();
		}
		return true;
	}
	
}
