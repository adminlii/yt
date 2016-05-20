<?php
class Product_InventoryController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/product/";
        $this->serviceClass = new Service_ProductAttribute();
    }
    
    public function listAction()
    {
    	$return_warehouse_shippingMethod = Service_OrderProcess::getWarehouseAndShippingMethodInfo();
    	//仓库
    	$wh = $return_warehouse_shippingMethod['warehouse'];
    	//$obj = new Service_OrderForWarehouseProcessNew();
    	$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
    	$db = Common_Common::getAdapter();
//     	$wh= Common_DataCache::getWarehouse();
    
    	$con = array();
//     	$field = array('user_id','user_code','user_name','user_name_en');
//     	$usersT = Service_User::getByCondition($con,$field);
		$sql_user = "select t.user_id,t.user_code,t.user_name,t.user_name_en from ".$wms_db.".user t";
		$usersT = $db->fetchAll($sql_user);
		//产品销售状态
		$product_sell_arr = $saleStatus = Common_Type::getSaleStatus('auto');
		
    	$users = array();
    	foreach($usersT as $v){
    		$users[$v['user_id']] = $v;
    	}
    	//         print_r($users);exit;
    
    	if ($this->_request->isPost()) {
    		$page = $this->_request->getParam('page', 1);
    		$pageSize = $this->_request->getParam('pageSize', 20);
    
    		$page = $page ? $page : 1;
    		$pageSize = $pageSize ? $pageSize : 20;
    
    		$return = array(
    				"state" => 0,
    				"message" => "No Data"
    		);
    
    		$condition = array();
    		$condition['barcode_type'] = trim($this->getParam('barcode_type','1'));
    		$tmp_barcode = trim($this->getParam('product_barcode',''));
    		switch ($condition['barcode_type']){
    			case '1':
    				$condition['product_barcode_like'] = $tmp_barcode;
    				break;
    			case '2';
    				$condition['warehouse_product_barcode_like'] = $tmp_barcode;
    				break;
    			default:
    				$condition['product_barcode_like'] = $tmp_barcode;
    				break;
    		}
//     		
    		$condition['warehouse_id'] = $this->getParam('warehouse_id','');
    		$condition['qty_type'] = $this->getParam('qty_type','');
    		$condition['qty_from'] = $this->getParam('qty_from','');
    		$condition['qty_to'] = $this->getParam('qty_to','');
    		$condition['buyer_id'] = $this->getParam('buyer_id','');
    		$condition['warehouse_id'] = $this->getParam('warehouse_id','');
    		$condition['sale_status'] = $this->getParam('sale_status','');
    		
    		//用户仓库
//     		$condition['warehouse_id_in'] = Service_User::getUserWarehouseIds();
    		foreach($condition as $k=>$v){
    			if(!is_array($v)){
    				$condition[$k] = trim($v);
    			}
    		}
    		$sql_inventory_conut = $this->getInventorySql($condition,"count(*)",$pageSize, $page);
    		$count = $db->fetchone($sql_inventory_conut);
//     		$count = $this->serviceClass->getByCondition($condition, 'count(*)');
    		$return['total'] = $count;
    
    		$orderBy = $this->getParam('order_by','pi_id desc');
    
    		if ($count) {
    			$showFields = array(
    					'product_barcode',
    					'warehouse_id',
    					'pi_onway',
    					'pi_pending',
    					'pi_sellable',
    					'pi_unsellable',
    					'pi_outbound',
    					'pi_reserved',
    					'pi_shipped',
    					'pi_no_stock',
    					'pi_warning_qty',
    					'pi_update_time',
    					'pi_id',
    					'buyer_id',
    					'product_id',
    					'pi_outbound'
    			);
    			// $showFields =
    			// $this->serviceClass->getFieldsAlias($showFields);
    			$sql_inventory = $this->getInventorySql($condition,"*", $pageSize, $page, $orderBy);
//     			exit;
    			$rows = $db->fetchAll($sql_inventory);
//     			$rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, $orderBy);
//     			$proc = new Service_SupplyStrategyProcess();
    			$wms_api = new Service_OrderForWarehouseProcessNew();
    			
    			foreach($rows as $k => $v){
	    			$result_wms = $wms_api->getInventoryIntegrate($v);
    				$v = $result_wms['data'];//$proc->getInventoryIntegrate($v);
    				$v['buyer_name'] = isset($users[$v['buyer_id']])?$users[$v['buyer_id']]['user_name']:'无';
    				$rows[$k] = $v;
    				
    				//待销毁库存
    				$sql = "select SUM(receiving_abnormal_detail.rad_quantity) as quantity from ".$wms_db.".receiving_abnormal_detail
							LEFT JOIN ".$wms_db.".receiving_abnormal on receiving_abnormal_detail.ra_id = receiving_abnormal.ra_id
							where receiving_abnormal.ra_status in (0,1) and receiving_abnormal.ra_type = 1
                    		and receiving_abnormal_detail.product_barcode = '".$v["product_barcode"]."'
							and receiving_abnormal.warehouse_id = ".$v["warehouse_id"];
//     				echo $sql;exit;
    				$abnormalCount = $db->fetchRow($sql);
    				$count_temp = 0;
    				if(!empty($abnormalCount["quantity"]) && $abnormalCount["quantity"] > 0){
    					$count_temp = $abnormalCount["quantity"];
    				}
    				$rows[$k]["abnormal_count"] = $count_temp;
    				
    				$sale_status_title = (($product_sell_arr[$v['sale_status']])?$product_sell_arr[$v['sale_status']]:'');
    				$rows[$k]["sale_status_title"] = $sale_status_title;
    			}
    			$return['data'] = $rows;
    			$return['warehouse'] = $wh;
    			$return['state'] = 1;
    			$return['message'] = "";
    		}
    		die(Zend_Json::encode($return));
    	}
    	set_time_limit(0);
    	$sql = "update ".$wms_db.".product_inventory set pi_no_stock=0;";
    	$db->query($sql);//缺货库存统计
    	$sql = "select a.warehouse_id,b.product_id,sum(b.op_quantity) op_qty from ".$wms_db.".orders a INNER JOIN ".$wms_db.".order_product b on a.order_id = b.order_id where a.order_status=3 group by a.warehouse_id,b.product_id;";
    	$result = $db->fetchAll($sql);
    	foreach($result as $v){
    		$db->update($wms_db.'.product_inventory', array(
    				'pi_no_stock' => $v['op_qty']
    		), 'warehouse_id=' . $v['warehouse_id'] . ' and product_id=' . $v['product_id']);
    	}
    	$this->view->whJson = Zend_Json::encode($wh);
    
    	$this->view->users = $users;
    	
    	$this->view->warehouse = $wh;
    	
    	$this->view->saleStatus = $saleStatus;
    	 
    
    	echo Ec::renderTpl($this->tplDirectory . "product_inventory_index.tpl", 'layout');
    }

    private function getInventorySql($condition = array(), $type = '*', $pageSize = 0, $page = 1, $orderBy = ""){
//     	$condition['product_barcode_like'] = trim($this->getParam('product_barcode',''));
//     	$condition['warehouse_id'] = $this->getParam('warehouse_id','');
//     	$condition['qty_type'] = $this->getParam('qty_type','');
//     	$condition['qty_from'] = $this->getParam('qty_from','');
//     	$condition['qty_to'] = $this->getParam('qty_to','');
//     	$condition['buyer_id'] = $this->getParam('buyer_id','');
    	
    	$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
    	$showFields = "t1.*, t2.warehouse_product_barcode, t3.sale_status";
    	if($type == 'count(*)'){
    		$showFields = $type;
    	}
    	$sql = "select $showFields from ".$wms_db.".product_inventory t1 INNER JOIN ".$wms_db.".product t3 ON t1.product_id = t3.product_id LEFT JOIN ".$wms_db.".product_barcode_map t2 " . 
    			"ON t1.product_barcode = t2.product_barcode and t1.warehouse_id = t2.warehouse_id where 1=1 ";
    	
    	if(isset($condition["product_barcode_like"]) && $condition["product_barcode_like"] != ""){
//     		$select->where("product_barcode like ?",'%'.$condition["product_barcode_like"].'%');
    		$sql .= " and t1.product_barcode like '%" . $condition["product_barcode_like"] . "%'";
    	}
    	if(isset($condition["warehouse_product_barcode_like"]) && $condition["warehouse_product_barcode_like"] != ""){
    		//     		$select->where("product_barcode like ?",'%'.$condition["product_barcode_like"].'%');
    		$sql .= " and t2.warehouse_product_barcode like '%" . $condition["warehouse_product_barcode_like"] . "%'";
    	}
    	
    	if(isset($condition["warehouse_id"]) && $condition["warehouse_id"] != ""){
//     		$select->where("warehouse_id = ?",$condition["warehouse_id"]);
    		$sql .= " and t1.warehouse_id = " . $condition["warehouse_id"];
    	}
    	if(isset($condition["buyer_id"]) && $condition["buyer_id"] != ""){
//     		$select->where("buyer_id = ?",$condition["buyer_id"]);
    		$sql .= " and t1.buyer_id = " . $condition["buyer_id"];
    	}
    	if(isset($condition["qty_type"]) && $condition["qty_type"] != ""){
    		$condition["qty_from"] = intval($condition["qty_from"]);
    		$condition["qty_to"] = intval($condition["qty_to"]);
    		if(isset($condition["qty_from"]) && !empty($condition["qty_from"])){
//     			$select->where($condition["qty_type"]." >= ?",$condition["qty_from"]);
    			$sql .= " and t1.".$condition["qty_type"]." >= " . $condition["qty_from"];
    		}
    		if(isset($condition["qty_to"]) && !empty($condition["qty_to"])){
//     			$select->where($condition["qty_type"]." <= ?",$condition["qty_to"]);
    			$sql .= " and t1.".$condition["qty_type"]." <= " . $condition["qty_to"];
    		}
    	}
    	if(isset($condition["sale_status"]) && $condition["sale_status"] != ""){
    		$sql .= " and t3.sale_status = " . $condition["sale_status"];
    	}
    	
    	
    	if (!empty($orderBy)) {
    		$sql .= " order by $orderBy";
    	}
    	if($type == 'count(*)'){
    		
    	}else{
    		if ($pageSize > 0 && $page > 0) {
    			$start = ($page - 1);
    			$sql .= " limit $start,$pageSize";
    		}
    	}
    	
//     	echo $sql;exit;
    	return $sql;
    }
    
    /**
     * 在途库存
     */
    public function getOnWayDetailAction(){
    	$warehouse_id = $this->getParam('wid','0');
    	$product_id = $this->getParam('pid','0');
    	$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
    	$db = Common_Common::getAdapter();
    	//a.receiving_code,a.warehouse_id,a.receiving_status,a.receiving_add_time,b.product_barcode,b.product_id,b.rd_receiving_qty
    	$sql = 'SELECT * FROM '.$wms_db.'.receiving a INNER JOIN '.$wms_db.'.receiving_detail b on a.receiving_id=b.receiving_id  where a.receiving_status>=5 and b.rd_received_qty=0 and a.warehouse_id='.$warehouse_id.' and b.product_id='.$product_id.';';
    	//         echo $sql;exit;
    	$result = $db->fetchAll($sql);
    	$statusArr = Common_Type::receivingStatus();//Common_Status::receivingStatus();
    	foreach($result as $k=>$v){
    		$v['status_title'] = $statusArr[$v['receiving_status']];
    		$result[$k] = $v;
    	}
    	die(Zend_Json::encode($result));
    
    }
}