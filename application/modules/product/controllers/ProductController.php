<?php
class Product_ProductController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/product/";
        $this->serviceClass = new Service_ProductAttribute();
    }

    /**
     * 产品信息查询
     * @see Ec_Controller_Action::listAction()
     */
	public function listAction()
    {
    	$config = Zend_Registry::get('config')->toArray();
    	$rs = Service_OrderForWarehouseProcessNew::getSystemWms();
    	$url = $rs['us_url'];
    	
        $status = Common_Type::getProductStatus('auto');
        $saleStatus = Common_Type::getSaleStatus('auto');
        $obj = new Service_OrderForWarehouseProcessNew();
        if($this->getRequest()->isPost()){
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            $product_is_qc = $this->_request->getParam('product_is_qc', '');
            $product_status = $this->_request->getParam('product_status', '');
            $sale_status = $this->_request->getParam('sale_status', '');
            $type = $this->_request->getParam('type', 1);
            $code = $this->_request->getParam('code', 20);
            $category_id = $this->_request->getParam('category_id', '');
            $dateFrom = $this->_request->getParam('dateFrom', '');
            $dateTo = $this->_request->getParam('dateTo', '');
            $priceFrom = $this->_request->getParam('priceFrom', '');
            $priceTo = $this->_request->getParam('priceTo', '');
            $inventoryFrom = $this->_request->getParam('inventoryFrom', '');
            $inventoryTo = $this->_request->getParam('inventoryTo', '');
            $order_by = $this->getParam('order_by','');
            
            $wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
            $db = Common_Common::getAdapter();
            $page = $page ? $page : 1;
            $page = max(0,$page);
            $pageSize = $pageSize ? $pageSize : 20;
             
            $return = array(
            		"state" => 0,
            		"message" => "No Data"
            );
            $sql = 'select TYPE from '.$wms_db.'.product a';
            $sql.=' inner join '.$wms_db.'.product_develop d on d.pd_id = a.pd_id ';
            $sql.=' where 1=1';
            
            if($product_status!=''){
            	$sql.=" and a.product_status='".$product_status."'";
            }
            
            if($sale_status!=''){
            	$sql.=" and a.sale_status='".$sale_status."'";
            }
             
            if($product_is_qc!=''){
            	$sql.=" and a.product_is_qc='".$product_is_qc."'";
            }
             
            $code = trim($code);
            $type = trim($type);
            if($code!=''){
            	switch($type){
            		case 'title':
            			$sql.=" and a.product_title like '%".$code."%'";
            			break;
            		case 'barcode':
            			$sql.=" and a.product_barcode like '%".$code."%'";
            			break;
            		default:
            			$sql.=" and a.product_sku like '%".$code."%'";
            			 
            	}
            }
            
            if(!empty($category_id)){
            	$category_id = trim($category_id);
            	$sql.=" and a.pc_id='".$category_id."'";
            }
             
            if(! empty($dateFrom)){
            	$dateFrom = trim($dateFrom);
            	$sql .= " and unix_timestamp(a.product_add_time)>=unix_timestamp('" . $dateFrom . "')";
            }
            if(! empty($dateTo)){
            	$dateTo = trim($dateTo);
            	$sql .= " and unix_timestamp(a.product_add_time)<=unix_timestamp('" . $dateTo . "')";
            }
             
            if(!empty($priceFrom)){
            	$priceFrom = trim($priceFrom);
            	$sql.=" and a.product_declared_value>='".$priceFrom."'";
            }
            if(!empty($priceTo)){
            	$priceTo = trim($priceTo);
            	$sql.=" and a.product_declared_value<='".$priceTo."'";
            }
            if(!empty($inventoryFrom)){
            	$inventoryFrom = trim($inventoryFrom);
            }
            if(!empty($inventoryTo)){
            	$inventoryTo = trim($inventoryTo);
            }
             
            $count = $db->fetchOne(str_replace('TYPE', 'count(*)', $sql));
             
            $return['total'] = $count;
            $return['status'] = $status;
            $return['wms_url'] = $url;
            if ($count) {
            	if($order_by){
            		$sql.=' order by '.$order_by;
            	}
            	$sql.=' limit '.($page-1)*$pageSize.' ,'.$pageSize;
            	$sql = str_replace('TYPE', 'a.*,d.default_supplier_code', $sql);
            	$data = $db->fetchAll($sql);
				foreach($data as $k=>$v){
            		$v['sale_status'] = !isset($v['sale_status'])?1:$v['sale_status'];
            		$v['sale_status_title'] = $saleStatus[$v['sale_status']];
            		$data[$k] = $v;
            	}
            	$return['data'] = $data;
            	$return['state'] = 1;
            }
            die(Zend_Json::encode($return));
        }
        $categoryArr = $obj->getProductCategory();
//         print_r($categoryArr);
//         exit;
        $this->view->category = $categoryArr['data'];
        $this->view->categoryJson = Zend_Json::encode($categoryArr['data']);

        $this->view->statusJson = Zend_Json::encode($status);
        $this->view->status = $status;
        
        $this->view->saleStatusJson = Zend_Json::encode($saleStatus);
        $this->view->saleStatus = $saleStatus;

        $return_warehouse_shippingMethod = Service_OrderProcess::getWarehouseAndShippingMethodInfo();
        //仓库
        $this->view->warehouse=$return_warehouse_shippingMethod['warehouse'];
        
        //运输代码
        $this->view->shippingMethod=$return_warehouse_shippingMethod['shippingMethod'];
        
        //国家
        $countryArr = Service_Country::getByCondition(null,'*',0,9999,"country_code");
        $this->view->country=$countryArr;
        
        echo Ec::renderTpl($this->tplDirectory . "develop_list_new.tpl", 'layout');
    }
    
	/**
	 * 产品信息明细
	 */
    public function detailAction()
    {
    	$return_warehouse_shippingMethod = Service_OrderProcess::getWarehouseAndShippingMethodInfo();
    	//仓库
    	$warehouseArr = $return_warehouse_shippingMethod['warehouse'];
//     	$warehouses = Service_Warehouse::getAll();
//     	$warehouseArr = array();
//     	foreach($warehouses as $w){
//     		$warehouseArr[$w['warehouse_id']] = $w;
//     	}
    	//         print_r($warehouseArr);exit;
    	$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
    	$this->view->warehouseArr = $warehouseArr;
    
    	$db = Common_Common::getAdapter();
    	$prodId = $this->getRequest()->getParam('product_id', '');
    	$prodSku = $this->getRequest()->getParam('product_sku', '');
    	if(!empty($prodId)){
    		$sql_product = "select t.* from ".$wms_db.".product t where t.product_id = " . $prodId;
    		$product = $db->fetchAll($sql_product);
    	}else if(!empty($prodSku)){
    		$sql_product = "select t.* from ".$wms_db.".product t where t.product_sku = '" . $prodSku . "'";
    		$product = $db->fetchAll($sql_product);
    	}

//     	$product = Service_Product::getByField($prodId,'product_id');
    	if(empty($product)){
    		echo 'product not exists';exit;
    	}else{
    		$product = $product[0];
    		$prodId = $product['product_id'];
    	}
//     	print_r($product);
    	
    	$sql_product_category = "select t.* from ".$wms_db.".product_category t where t.pc_id = " . $product['pc_id'];
    	$category = $db->fetchAll($sql_product_category);
//     	$category = Service_ProductCategory::getByField($product['pc_id'],'$sql_product = "select t.* from ".$wms_db.".product t where t.product_id = " . $prodId;
    	if($category){
    		$category = $category[0];
    		$product['category_name'] = $category['pc_name_en'];
    		$product['category'] = $category;
    	}
    	$product['hs_name'] = '';
    	if($product['hs_code']){
    		$sql_hsAttribute = "select t.* from ".$wms_db.".hs_attribute t where t.hs_code = '" . $product['hs_code'] . "'";
    		$hsRow = $db->fetchAll($sql_hsAttribute);
//     		$hsRow =  Service_HsAttribute::getByField($product['hs_code'],'hs_code');
    		if($hsRow){
    			$hsRow = $hsRow[0];
    			$product['hs_name'] = $hsRow['hs_name'];
    		}
    	}
    	//         print_r($product);exit;
    	$sql_languages = "select t.* from ".$wms_db.".languages t";
    	$languages = $db->fetchAll($sql_languages);
//     	$languages = Service_Languages::getAll();
    	foreach($languages as $v){
    		$languageArr[$v['languages_id']] = $v;
    	}
//     	print_r($languageArr);
//     	echo '<br/><br/><br/>';
    	$this->view->languageArr = $languageArr;
    	$this->view->product = $product;

    	$rs = Service_OrderForWarehouseProcessNew::getSystemWms(); 
    	
    	$wms_url = trim($rs['us_url'],'/');
//     	$serverConfig = Zend_Registry::get('server');
    	$sql_product_images = "select t.* from ".$wms_db.".product_images t where t.pd_id = " . $product['pd_id'];
    	
    	$attachs = $db->fetchAll($sql_product_images);
    	foreach($attachs as $k=>$v){
    		$v['src'] = $wms_url.'/default/index/view-img?eb_pi_id='.$v['pi_id'];
    		$attachs[$k] = $v;
    	}
//     	$con = array('pd_id'=>$product['pd_id']);
//     	$attachs = Service_ProductImages::getByCondition($con);
//     	foreach($attachs as $k=>$v){
//     		$attachs[$k]['src'] = $serverConfig['swfupload']['url_prefix'].$v['pi_path'];
//     	}
//     	        print_r($attachs);exit;
    	$this->view->attachs = $attachs;
    	$config = Zend_Registry::get('config')->toArray();
    	$url = $rs['us_url'];
    	$this->view->attachs_wms_url = $url;
    	
    	//产品开发表
    	$sql_product_develop = "select t.* from ".$wms_db.".product_develop t where t.pd_id = " . $product['pd_id'];
    	$productDev = $db->fetchAll($sql_product_develop);
    	$productDev = $productDev[0];
//     	$productDev = Service_ProductDevelop::getByField($product['pd_id'],'pd_id');
    	//供应商产品表
    	$sql_supplier_product = "select t.* from ".$wms_db.".supplier_product  t where t.pd_id = " . $product['pd_id'];
    	$supplierProducts = $db->fetchAll($sql_supplier_product);
//     	$con = array('pd_id'=>$product['pd_id']);
//     	$supplierProducts = Service_SupplierProduct::getByCondition($con);
    	$puPrice = 0;
    	foreach($supplierProducts as $k=>$v){
    		//供应商表
    		$sql_supplier = "select t.* from ".$wms_db.".supplier t where t.supplier_id = " . $v['supplier_id'];
    		$supplier = $db->fetchAll($sql_supplier);
    		$supplier = $supplier[0];
//     		$supplier = Service_Supplier::getByField($v['supplier_id'],'supplier_id');
    		$supplierProducts[$k]['supplier'] = $supplier;
    		if(empty($puPrice)){
    			$puPrice = $v;
    			$productDev['default_supplier_code'] = $supplier['supplier_code'];
    		}
    		if($productDev['default_supplier_code']==$supplier['supplier_code']){
    			$puPrice = $v;
    			$productDev['default_supplier_code'] = $supplier['supplier_code'];
    		}
    	}
    	if(empty($puPrice)){
    		$puPrice = array('sp_last_price'=>'0','currency_code'=>'RMB');
    	}
    	$this->view->puPrice = $puPrice;
    	$this->view->supplierProduct = $supplierProducts;
    	$this->view->productDev = $productDev;
    
    	//产品说明
    	$sql_product_develop_explanation = "select t.* from ".$wms_db.".product_develop_explanation t where t.pd_id = " . $product['pd_id'];
    	$explanation = $db->fetchAll($sql_product_develop_explanation);
//     	$con = array('pd_id'=>$product['pd_id']);
//     	$explanation = Service_ProductDevelopExplanation::getByCondition($con);
    
    	$this->view->explanation = $explanation;
    
    	//报价日志 取最近10条
    	$sql_supplier_product_log = "select t.* from ".$wms_db.".supplier_product_log t where t.pd_id = " . $product['pd_id'] . " order by t.spl_id desc limit 0,10";
    	$splRows = $db->fetchAll($sql_supplier_product_log);
//     	$splRows = Service_SupplierProductLog::getByCondition($con,'*',10,1,'spl_id desc');
    	$this->view->splRows = $splRows;
    
    	$supplierIds = $userIds = array();
    	$useIds_str = "";
    	$supplierIds_str = "";
    	foreach($splRows as $r){
    		$supplierIds[] = $r['supplier_id'];
    		$supplierIds_str .= "'" . $r['supplier_id'] . "',";
    		
    		$userIds[] = $r['buyer_id'];
    		$userIds[] = $r['creater_id'];
    		$userIds[] = $r['updater_id'];
    	}
    	$supplierIds_str = substr($supplierIds_str, 0,strlen($supplierIds_str) - 1);
    	
    	foreach ($userIds as $user_id_value) {
    		$useIds_str .= "'" . $user_id_value . "',";
    	}
    	
    	$useIds_str = substr($useIds_str, 0,strlen($useIds_str) - 1);
    	
    	$supplierIds = array_unique($supplierIds);
    	$userIds = array_unique($userIds);
		if($useIds_str != ''){
	    	$sql_user = "select t.* from ".$wms_db.".user t where t.user_id in (".$useIds_str.")";
	    	$userArr = $db->fetchAll($sql_user);
		}
    	
//     	$con = array('user_id_arr'=>$userIds);
//     	$userArr = Service_User::getByCondition($con);
    	$userKArr = array();
    	foreach($userArr as $u){
    		$userKArr[$u['user_id']] = $u;
    	}
    	$this->view->userKArr = $userKArr;
//     	$con = array('supplier_id_arr'=>$supplierIds);
//     	$supplierArr = Service_Supplier::getByCondition($con);
		if($supplierIds_str != ''){
			$sql_supplier_by_id = "select t.* from ".$wms_db.".supplier t where t.supplier_id in (".$supplierIds_str.")";
			$supplierArr = $db->fetchAll($sql_supplier_by_id);
		}
    	$supplierKArr = array();
    	foreach($supplierArr as $u){
    		$supplierKArr[$u['supplier_id']] = $u;
    	}
    	$this->view->supplierKArr = $supplierKArr;
    	//         print_r($splRows);exit;
    	//产品采购
    	
//     	$con = array('product_id'=>$prodId);
//     	$poProducts = Service_PurchaseOrderProduct::getByCondition($con,'*',10,1,'pop_id desc');
		$sql_purchase_order_product = "select t.* from ".$wms_db.".purchase_order_product t where t.product_id = " . $prodId;
		$poProducts = $db->fetchAll($sql_purchase_order_product);
    	$this->view->poProducts = $poProducts;
    	$sql = 'select * from';
    	//产品订单
    	$con = array('product_id'=>$prodId);
    	//         $porder = Service_OrderProduct::getByCondition($con,'*',10,1,'op_id desc');
    	$sql = 'select * from '.$wms_db.'.orders a inner join '.$wms_db.'.order_product b on a.order_id=b.order_id where b.product_id='.$prodId.' and a.order_status!=0 order by op_id desc limit 100';
    
    	$porder =  $db->fetchAll($sql);
    
    	$this->view->porder = $porder;
    
    
    	//产品asn
    	$con = array('product_id'=>$prodId);
    	//         $poProducts = Service_ReceivingDetail::getByCondition($con,'*',10,1,'rd_id desc');
    	$sql = 'select * from '.$wms_db.'.receiving a inner join '.$wms_db.'.receiving_detail b on a.receiving_id=b.receiving_id where b.product_id='.$prodId.' and a.receiving_status!=0 order by rd_id desc limit 10';
    
    	$poProducts = $db->fetchAll($sql);
    	$this->view->pasn = $poProducts;
    
    	//         print_r($poProducts);exit;
    	//产品库存
//     	$con = array('product_id'=>$prodId);
//     	$inventory = Service_ProductInventory::getByCondition($con);
		$sql_product_inventory = "select t.* from ".$wms_db.".product_inventory t where t.product_id = " .$prodId;
		$inventory = $db->fetchAll($sql_product_inventory);
    	foreach($inventory as $k=>$v){
//     		$warehouse = Service_Warehouse::getByField($v['warehouse_id'],'warehouse_id',array('warehouse_code','warehouse_desc'));
			$sql_warehouse = "select t.warehouse_code, t.warehouse_desc from ".$wms_db.".warehouse t where t.warehouse_id = " . $v['warehouse_id'];
			$warehouse = $db->fetchAll($sql_warehouse);
			$warehouse = $warehouse[0];
    		$inventory[$k]['warehouse_name'] = $warehouse['warehouse_code'].' ['.$warehouse['warehouse_desc'].']';
    	}
    	$this->view->inventory = $inventory;
    
    	//产品做题平均销量
//     	$con = array('product_id'=>$prodId);
//     	$avgSale = Service_WarehouseProductSales::getByCondition($con);
		$sql_warehouse_product_sales = "select t.* from ".$wms_db.".warehouse_product_sales t where t.product_id = " . $prodId;
		$avgSale = $db->fetchAll($sql_warehouse_product_sales);
    	$saleTypeArr = array(
    			'1' => '3天平均销量',
    			'2' => '7天平均销量',
    			'3' => '14天平均销量',
    			'4' => '30天平均销量'
    	);
    	$trendTypeArr = array(
    			'0'=> '--',
    			'1' => '持续上升 (销量参考值为：3天平均销量)',
    			'2' => '波动上升 (销量参考值为：7天平均销量)',
    			'3' => '波动下降 (销量参考值为：14天平均销量)',
    			'4' => '持续下降 (销量参考值为：30天平均销量)'
    	);
    	$avgSaleArr = array();
    	foreach($avgSale as $k=>$v){
    		if($v['sales_type']==0){
    			//                 $avgSale[$k]['sales_type_title'] = '';
    		}else{
    			//                 $warehouse = Service_Warehouse::getByField($v['warehouse_id'],'warehouse_id');
    			//                 $avgSale[$k]['sales_type_title'] = $warehouse['warehouse_code']."[".$warehouse['warehouse_desc']."] ".$saleTypeArr[$v['sales_type']]." : ".$v['qty_sales'];
    			//                 $avgSale[$k]['warehouse_name'] = $warehouse['warehouse_code']."[".$warehouse['warehouse_desc']."] ";
    		}
    		$v['trend'] = $trendTypeArr[$v['sales_type']];
    		$avgSale[$k] = $v;
    		$avgSaleArr[$v['warehouse_id']] = $v;
    
    	}
    	foreach($warehouseArr as $k=>$v){
    		if(!isset($avgSaleArr[$k])){
    			$avgSaleArr[$k] = array(
    					'warehouse_id' => $v['warehouse_id'],
    					'qty_sales' => '0',
    					'sales_type' => '0',
    					'qty_day3' => '0',
    					'qty_day7' => '0',
    					'qty_day14' => '0',
    					'qty_day30' => '0',
    					'trend' => $trendTypeArr[0]
    			);
    		}
    	}
    	//         print_r($avgSaleArr);exit;
    	$this->view->avgSale = $avgSaleArr;
    	$this->view->trendTypeArr = $trendTypeArr;
    	//产品配件
//     	$con = array('pd_id'=>$product['pd_id']);
//     	$fitting = Service_ProductDevelopFitting::getByCondition($con);

    	$sql_product_develop_fitting = "select t.* from ".$wms_db.".product_develop_fitting t where t.pd_id = " . $product['pd_id'];
    	$fitting = $db->fetchAll($sql_product_develop_fitting);
    	$this->view->fitting = $fitting;
    	//产品日志 取最近10条
//     	$con = array('product_id'=>$prodId);
//     	$productLog = Service_ProductLog::getByCondition($con,'*', 10 , 1,'pl_id desc');
		$sql_product_log = "select t.* from ".$wms_db.".product_log t where t.product_id = " . $prodId . " order by t.pl_id desc limit 0,10";
		$productLog = $db->fetchAll($sql_product_log);
    	
    	$statusArr = array('0'=>'不可用','1'=>'可用');
    	foreach($productLog as $k=>$v){
    		$productLog[$k]['pl_type_title'] = $v['pl_type']==0?'内容修改':'状态修改';
    		$productLog[$k]['pl_statu_pre_title'] = $statusArr[$v['pl_statu_pre']];
    		$productLog[$k]['pl_statu_now_title'] = $statusArr[$v['pl_statu_pre']];
    
    	}
    	$this->view->productLog = $productLog;
    	//产品描述
//     	$con = array('pd_id'=>$product['pd_id']);
//     	$prodDesc = Service_ProductDevelopDescription::getByCondition($con);
		$sql_product_develop_description = "select t.* from ".$wms_db.".product_develop_description t where t.pd_id = " .$product['pd_id'];
		$prodDesc = $db->fetchAll($sql_product_develop_description);
//     	print_r($prodDesc);
//     	echo '<br><br>';
    	foreach($prodDesc as $k_pd=>$v_pd){
    		if(isset($languageArr[$v_pd['language_id']])){
    			$prodDesc[$k_pd]['language_name'] = $languageArr[$v_pd['language_id']]['name'];
    		}else{
    			$prodDesc[$k_pd]['language_name'] = '默认';
    		}
    	}
//     	print_r($prodDesc);
//     	exit;
    	$this->view->prodDesc = $prodDesc;
    
    
    	//         //配货策略
    	//         $con = array('product_id'=>$prodId);
    	//         $pss = Service_ProductSupplyStrategy::getByCondition($con);
    	//         $strategySel = array();
    	//         if(empty($pss)){
    	//             $con = array('warehouse_id'=>$product['warehouse_id']);
    	//             $wss = Service_WarehouseSupplyStrategy::getByCondition($con);
    	//             foreach($wss as $v){
    	//                 $strategySel[$v['wss_type']] = $v['wss_value'];
    	//             }
    	//         }else{
    	//             foreach($pss as $v){
    	//                 $strategySel[$v['pss_value']] = $v['pss_type'];
    	//             }
    	//         }
    	//         $strategyType = Service_ProductSupplyStrategyType::getAll();
    
    	//         foreach($strategyType as $k=>$v){
    	// //             echo $v['sst_name_en'];
    	//             $strategyType[$k]['val'] = $strategySel[$v['sst_name_en']];
    	//         }
    	// //         print_r($strategyType);exit;
    	//         $this->view->strategyType = $strategyType;
    
    	//基础数据
//     	$categoryArr = Service_ProductCategory::getAll();
    	$obj_wms_api = new Service_OrderForWarehouseProcessNew();
    	$result_wms_pc = $obj_wms_api->getProductCategory();
    	$categoryArr = $result_wms_pc['data'];
    	$this->view->categoryArr = $categoryArr;
    
    	$currencyArr =  Service_Country::getByCondition(null,'*',0,9999,"country_code");
    	$this->view->currencyArr = $currencyArr;
    
    	$sql = "select distinct hs_code,hs_name FROM ".$wms_db.".hs_attribute";
    	$hsAttr = $db->fetchAll($sql);
    
    	$this->view->hsAttr = $hsAttr;
    	$this->view->show = 'tab';
    	$edit = $this->_request->getParam('edit','1');
    	if($edit){
    		$edit = true;
    	}else{
    		$edit = false;
    	}
    
    	$this->view->edit = $edit;
    
    	$this->view->orderStatusArr = Common_Type::wmsOrderStatus();//Common_Status::orderStatus();
    
    	echo Ec::renderTpl($this->tplDirectory . "develop_detail.tpl", 'layout');
    }
    
    /**
     * 产品打印
     */
    public function printVerityAction(){
    	 
    	$return = array("state"=>0,"message"=>array(),"data"=>"","count"=>0);
    	if($this->_request->isPost()){
    		$productCodes = $this->_request->getParam('productCodes',"");
    		$warehouse = $this->_request->getParam('print_warehouse',"");
    		$productArr = explode(",",$productCodes);
    		$db = Common_Common::getAdapter();
    		foreach($productArr as $key=>$val){
    			if(empty($val)){
    				continue;
    			}
    			$sql = "select t.barcode from product_barcode_map t where t.product_barcode = '" . $val . "' and t.warehouse_id = " . $warehouse;
    			$objBarcode = $db->fetchAll($sql);
//     			$objBarcode = Service_ProductBarcodeMap::getByCondition(array("product_barcode"=>$val,"warehouse_id"=>$warehouse),array("barcode"),0,1,"");
    			 
    			if(empty($objBarcode)){
    				$return["message"][] = $val."，未配置该仓库产品条码，请确认配置再进行打印，本次打印将忽略该产品！";
    			}
    		}
    
    		$return["count"] = count($return["message"]);
    	}
    	 
    	die(Zend_Json::encode($return));
    }
    
    /**
     * 打印标签
     */
    public function printAction()
    {
    	$paper = $this->getParam('paper', 'A4');
    	$lodop = $this->getParam('lodop', '');
    	$productArr = $this->_request->getParam('product', '');
    	$wahouse_id = $this->_request->getParam('wahouse_id', '');
    	$productArr = explode(',', $productArr);
    	 
    	$productList = array();
    	$wms_db = Zend_Registry::get('wms_db');//Wsm 数据库名
    	$db = Common_Common::getAdapter();
    	foreach($productArr as $id_qty){
    		$arr = explode('_', $id_qty);
    		$productId = $arr[0];
    		$qty = $arr[1];
    		// 过滤
    		if(! preg_match('/^[0-9]+$/', $qty)){
    			continue;
    		}
    		$sql = "select product.*,product_barcode_map.barcode from ".$wms_db.".product
					LEFT JOIN ".$wms_db.".product_barcode_map on product.product_barcode = product_barcode_map.product_barcode
					where product_barcode_map.warehouse_id = ".$wahouse_id."
					and product.product_id = ".$productId;
    		$p = $db->fetchRow($sql);
    
    		if(! $p || ($p['product_type'] != "0"&&$p['product_type'] !=0)){
    			continue;
    		}
    		//     		print_r($p);exit;
    		$item = array(
    				'product_barcode' => $p['barcode'],
    				'customer_code' => $p['customer_code'],
    				'pc_name' => ''
    		);
    		$p['pc_id'] = empty($p['pc_id']) ? '' : $p['pc_id'];
    		$sql_product_category = "select t.* from ".$wms_db.".product_category t where t.pc_id = " . $p['pc_id'];
    		$category = $db->fetchRow($sql_product_category);
//     		$category = Service_ProductCategory::getByField($p['pc_id'], 'pc_id');
    
    		if($category){
    			$item['pc_name'] = $category['pc_name_en'];
    		}
    		for($i = 0;$i < $qty;$i ++){
    			$productList[] = $item;
    		}
    	}
    	//     	print_r($productList);exit;
    
    	$this->view->paper = '70x30';
    
    	$this->view->title = '产品条码打印-' . time();
    
    	$this->view->w = 70; // A4纸张宽度
    	$this->view->h = 30; // A4纸张高度
    	$htmlArr = array();
    	foreach($productList as $product){
    		$this->view->row = $product;
    		$html = $this->view->render($this->tplDirectory . "product-label.tpl");
    		$html = preg_replace('/\s+/', ' ', $html); // 去除换行
    		$html = preg_replace('/\'/', '"', $html); // 引号
    		//     		echo $html;exit;
    		$htmlArr[] = $html;
    	}
    	 
    	$chunk = array_chunk($htmlArr, 30);
    	$this->view->htmlArrChunk = $chunk;
    	$this->view->count = count($htmlArr);
    	echo $this->view->render($this->tplDirectory . "product-label.js");
    }
    
    public function ebImgAction()
    {
        $itemId = $this->getRequest()->getParam('item_id','');
        $itemRow = Service_SellerItem::getByField($itemId,'item_id');
        $pic = '/images/base/noimg.jpg';
        if($itemRow&&$itemRow['pic_path']){
            $picArr = $itemRow['pic_path'];
            $picArr = explode('#:|:#', $picArr);
            $pic = $picArr[0];
        }
        header("Location: ".$pic);
        exit();
    }

    //根据关键字搜索模板
    public function getByKeywordAction(){
        $sku = $this->_request->getParam('term', '');
        $limit = $this->_request->getParam('limit', '20');
         
        $company_code = Common_Company::getCompanyCode();
        $db = Common_Common::getAdapter();
        $wms_db = Zend_Registry::get('wms_db');
//         $sql = "select * from {$wms_db}.product where (product_sku like '%{$sku}%' or product_title like '%{$sku}%')  limit {$limit}";
        $sql = "select * from {$wms_db}.product where product_sku like '%{$sku}%' limit {$limit}";
        $result = $db->fetchAll($sql);
        $lang = Ec::getLang(1);
        foreach($result as $k=>$v){
            $v['label'] = $v['product_sku'].'['.$v['product_title'.$lang].']';
            $v['value'] = $v['product_sku'];
            $v['product_title'] = $v['product_title'.$lang];
            $result[$k] = $v;
        }
        die(Zend_Json::encode($result));
    }

    //根据SKU搜索产品
    public function getProductBySkuAction(){
        $sku = $this->_request->getParam('sku', '');
        $con = array(
                'product_sku' => trim($sku),
        );
        $product = Service_Product::getByCondition($con,'*');
        $return = array('ask'=>0,'message'=>Ec::Lang('sku_not_exist',$sku));
        if(!empty($product)){
            $return['ask'] = 1;
            $return['product'] = $product[0];
        }
        die(Zend_Json::encode($return));
    }
}