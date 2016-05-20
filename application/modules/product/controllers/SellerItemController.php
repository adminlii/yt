<?php
class Product_SellerItemController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/seller-item/"; 
        $this->serviceClass = new Service_SellerItem();
    }

    public function listAction()
    {
        $supply_type_arr = Common_Type::supplyTypeArr();
        $status_arr = Common_Type::supplyStatusArr();
        $sync_status_arr = Common_Type::syncStatusArr();
        $user_account_arr = Service_User::getPlatformUserNew('do');//绑定店铺账号         
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
            
            $item_status = $this->getParam('item_status','');

            $sell_type = $this->getParam('sell_type','');
            $no_stock_online = $this->getParam('no_stock_online','');
            $need_supply = $this->getParam('need_supply','');
            $user_account = $this->getParam('user_account','');
            $sync_status = $this->getParam('sync_status','');
            $auto_supply = $this->getParam('auto_supply','');
            
            $condition['item_status'] = $item_status;
            $condition['sell_type'] = $sell_type;
            $no_stock_online = preg_replace('/[^0-9]+/',' ',$no_stock_online);
            $no_stock_online = trim($no_stock_online);
            if(!empty($no_stock_online)){
                $no_stock_online = explode(' ', $no_stock_online);
                $condition['no_stock_online_arr'] = $no_stock_online;
            }
            $condition['auto_supply'] = $auto_supply;
            $condition['need_supply'] = $need_supply;
            $condition['sync_status'] = $sync_status;
            $condition['user_account'] = $user_account;
            $condition['user_account_arr'] = array_keys($user_account_arr);
            
            
            $type = $this->getParam('type','sku');
            $code = $this->getParam('code','');
            switch ($type){
                case 'item_id':
 
                    $code = preg_replace('/[^0-9]/', ' ', $code);
                    $code = preg_replace('/\s+/', ' ', $code);
                    $code = trim($code);
                    if($code){
                        $code = explode(' ', $code);
                    }else{
                        $code = array();
                    }
                    $condition['item_id_arr'] = $code;
                    break;
                    
                default:
                    $condition['sku_like'] = trim($code);
                    
            }

            $sold_qty_from = $this->getParam('sold_qty_from','');
            $sold_qty_to = $this->getParam('sold_qty_to','');
            
            $condition['sold_qty_from'] = $sold_qty_from;
            $condition['sold_qty_to'] = $sold_qty_to;
            

            $sell_qty_from = $this->getParam('sell_qty_from','');
            $sell_qty_to = $this->getParam('sell_qty_to','');
            
            $condition['sell_qty_from'] = $sell_qty_from;
            $condition['sell_qty_to'] = $sell_qty_to;
            
            foreach($condition as $k=>$v){
                if(!is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
//             print_r($condition);exit;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) { 
                $rows = $this->serviceClass->getByCondition($condition, '*', $pageSize, $page);
                $data = array();
                foreach($rows as $k=>$v){
                    $v = Service_SellerItemProcess::getSellerItemDetail($v['item_id']);
//                     print_r($v);exit;
                    foreach($v as $kk=>$vv){
                        if(is_null($vv)){
                            $vv = Ec::Lang('nothing','auto');//'无';
                        }
                        $v[$kk] = $vv;
                    }
                    $rows[$k] = $v;
                    $data[$v['item_id']] = $v;
                }
//                 print_r($rows);exit;
                $return['data'] = $data;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }

//         $user_account_arr = Common_Common::getPlatformUser();

        
        $this->view->user_account_arr = $user_account_arr;
        $this->view->user_account_arr_json = Zend_Json::encode($user_account_arr);

        $con = array('warehouse_status'=>'1');
        $warehouse = Service_Warehouse::getByCondition($con);
        
        $warehouseArr = array();
        foreach($warehouse as $v){
            $warehouseArr[$v['warehouse_id']] = $v;
        }
        $this->view->warehouse=$warehouseArr;
        $this->view->warehouseArr=$warehouseArr;
        $this->view->warehouseJson=Zend_Json::encode($warehouseArr);
        

        
        $this->view->supply_type_arr = $supply_type_arr;
        $this->view->status_arr = $status_arr;
        
        echo Ec::renderTpl($this->tplDirectory . "seller_item_list.tpl", 'layout');
    }

    /**
     * =======================废弃=================
     * 更新补货数量
     */
    public function updateSupplyQtyAction(){
        $return = array(
            "state" => 0,
            "message" => "Request Err"
        );
        
        if($this->_request->isPost()){
            print_r($this->getRequest()->getParams());exit;
            set_time_limit(0);
            $params = $this->getRequest()->getParam('supply_qty', array());
            foreach($params as $k => $v){
                $params[$k] = trim($v);
            }
            // print_r($params);exit;
            $arr = array();
            try{
                foreach($params as $k => $qty){
                    if(! preg_match('/^[\-0-9]+$/', $qty)){
                        throw new Exception('补货数量必须为数字');
                    }
                    if(preg_match('/^([0-9]+)_([0-9]+)$/', $k, $m)){
                        $arr[$m[1]]['item_id'] = $m[1];
                        
                        if(isset($arr[$m[1]]['qty'])){
                            $arr[$m[1]]['qty'] += ($qty>0?$qty:0);
                        }else{
                            $arr[$m[1]]['qty'] = ($qty>0?$qty:0);
                        }
                        
                        $arr[$m[1]]['var'][$m[2]] = $qty;
                        
                    }elseif(preg_match('/^([0-9]+)$/', $k, $m)){
                        $arr[$m[1]]['item_id'] = $m[1];
                        $arr[$m[1]]['qty'] = $qty;
                    }else{
                        throw new Exception('参数错误');
                    }
                }
//                 print_r($arr);exit;
                $process = new Service_SellerItemProcess();
                $return = $process->updateSupplyQtyTransaction($arr);
                // print_r($return);exit;
            }catch(Exception $e){
                $return['message'] = $e->getMessage();
            }
        }
        die(Zend_Json::encode($return));
    }

    /**
     * 改价
     */
    public function updatePriceAction(){
    	$return = array(
    			"ask" => 0,
    			"message" => "Request Err"
    	);
    
    	if($this->_request->isPost()){
//     		print_r($this->getRequest()->getParams());exit;
    		set_time_limit(0);
    		$params = $this->getRequest()->getParam('price', array());
    		$data = array();
    		foreach ( $params as $item_id => $v ) {
				foreach ( $v as $sku => $price ) {
					$data [] = array (
							'item_id' => $item_id,
							'sku' => $sku,
							'price' => trim($price) 
					);
				}
			}
// 			print_r ( $data );
// 			exit (); 
    		try{
    		    if(empty($data)){
    		        throw new Exception('No Data Need Process...');
    		    }
    			foreach($data as $k => $v){
    				if(! is_numeric($v['price'])){
    					throw new Exception("[ItemID:{$v['item_id']}][SKU:{$v['sku']}]".'价格必须为数字');
    				}
    			}
    			$results = array();
    			$itemIds = array();
    			foreach($data as $v){
    			    $itemIds[] = $v['item_id'];
    				$rs = Service_SellerItemProcess::syncPriceSingle($v['item_id'], $v['sku'], $v['price']); 
    				$str = print_r($rs,true);
    				$str = preg_replace('/\n/', '<br/>', $str);
    				$str = str_replace(' ', '&nbsp;', $str);
    				$rs['data_str'] = $str;
    				$rs['request'] = $v;
    				$results[] = $rs;
    			}
    			
    			$itemIds = array_unique($itemIds);
    			foreach($itemIds as $itemId){
    			    Ebay_ItemEbayService::updateItem($itemId);
    			}
    			
    			$return['ask'] = 1;
    			$return['rs'] = $results;
    			$return['message'] = 'All Request Finish';
    		}catch(Exception $e){
    			$return['message'] = $e->getMessage();
    		}
    	}
    	die(Zend_Json::encode($return));
    }
    /**
     * 设置补货
     * @throws Exception
     */
    public function saveSupplyTypeAction()
    {
        $return = array(
                'ask' => 0,
                'message' => 'Fail.'
        );
        $pu = $this->getParam('pu', array());
        $var = $this->getParam('var', array());
        //         print_r($pu);exit;
        try{
            $platform = 'ebay';
            $data = array();
            //单品或拍卖
            foreach($pu as $item_id => $v){
                $item = Service_SellerItem::getByField($item_id,'item_id');
                $data[] = array(
                        'company_code'=>$item['company_code'],
                        'user_account'=>$item['user_account'],
                        'item_id'=>$item['item_id'],
                        'sku'=>$item['sku'],
                        'supply_type' => $v['supply_type'],
                        'supply_warehouse' => $v['supply_warehouse'],
                        'qty' => $v['supply_qty'],
                        'status' => $v['status'],
                        'sell_type'=>$item['sell_type'],
                );                
            }
            //多品
            foreach($var as $var_id => $v){
                $variation = Service_SellerItemVariations::getByField($var_id,'variation_id');
                $item = Service_SellerItem::getByField($variation['item_id'],'item_id');
                $data[] = array(
                        'company_code'=>$item['company_code'],
                        'user_account'=>$item['user_account'],
                        'item_id'=>$variation['item_id'],
                        'sku'=>$variation['sku'],
                        'supply_type' => $v['supply_type'],
                        'supply_warehouse' => $v['supply_warehouse'],
                        'qty' => $v['supply_qty'],
                        'status' => $v['status'],
                        'sell_type'=>$item['sell_type'],
                        'variation_id'=>$variation['variation_id'],
                );
            }
//             print_r($data);exit;
            foreach($data as $v){
                $row = array(
                        'platform' => $platform,
                        'company_code' => $v['company_code'],
                        'user_account' => $v['user_account'],
                        'item_id' => $v['item_id'],
                        'sku' => $v['sku'],
                        'supply_type' => $v['supply_type'],
                        'supply_warehouse' => $v['supply_warehouse'],
                        'qty' => $v['qty'],
                        'status' => $v['status'],
                        'sync_status' => '0',
                        'sync_time' => '0000-00-00 00:00:00',
                        'op_user_id' => Service_User::getUserId()
                );
                //                 print_r($row);exit;
                switch($v['supply_type']){
                    case '1':
                        if(empty($v['supply_warehouse'])){
                            throw new Exception('请选择仓库-->'."ItemID:[{$v['item_id']}],SKU[{$v['sku']}]");
                        }
//                         unset($row['sync_status']);
                        unset($row['qty']);
                        break;
                    case '2'://自定义补货
                        if(!preg_match('/^[0-9]+$/', $v['qty'])){
                            throw new Exception('请填写补货数(需为数字)-->'."ItemID:[{$v['item_id']}],SKU[{$v['sku']}]");
                        }                        
                        //原先的补货设置
                        $updateRow = array(
                            'supply_qty' => $v['qty'],
                            'sync_status' => '0',
                            'sync_time' => '0000-00-00 00:00:00'
                        );
//                         print_r($updateRow);exit;
                        if($v['sell_type']==2){
                            Service_SellerItemVariations::update($updateRow, $v['variation_id'],'variation_id');
                        }else{
                            Service_SellerItemVariations::update($updateRow, $v['item_id'],'item_id');
                        }
                        break;
                    default:
                        throw new Exception(Ec::Lang('supply_type_err'));
                }    
    
                $con = array(
                    'item_id' => $v['item_id'],
                    'sku' => $v['sku'],
                    'user_account' => $v['user_account'],
                    'platform' => $v['platform'],
                    'company_code' => $v['company_code'],
                );
                $exist = Service_SellerItemSupplyQty::getByCondition($con);
    
                $row = Common_ApiProcess::nullToEmptyString($row);
                if($exist){
                    $exist = array_pop($exist);
                    $row['update_time'] = now();
                    Service_SellerItemSupplyQty::update($row, $exist['id'], 'id');
                }else{
                    $row['add_time'] = now();
                    $row['update_time'] = now();
                    Service_SellerItemSupplyQty::add($row);
                }
                // 日志
                $logContent = "Ebay补货数据初始化,初始化参数：\n" . print_r($row, true);
                Service_SellerItemProcess::log($row['item_id'], $logContent, 3333);                
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
    
        echo Zend_Json::encode($return);
    }
    

    /**
     * 更新item
     */
    public function updateItemAction(){
        $return = array(
                "state" => 0,
                "message" => "Request Err"
        );
    
        if($this->_request->isPost()){
            set_time_limit(0);
            $item_ids = $this->getRequest()->getParam('item_id', array());

            $data = array();
            foreach($item_ids as $itemId){
                $result = Ebay_ItemEbayService::updateItem($itemId);
                $data[] = array(
                    'ItemID' => $itemId,
                    'Ack' => $result['GetItemResponse']['Ack'],
                    'Error' => isset($result['GetItemResponse']['Errors'])?print_r($result['GetItemResponse']['Errors'], true):false
                );
            }
            $return['data'] = $data;
            $return['ask'] = 1;
        }
        die(Zend_Json::encode($return));
    }

    /**
     * 更新item
     */
    public function getEbayItemAction(){
        $return = array(
                "state" => 0,
                "message" => "Request Err"
        );
    
        if($this->_request->isPost()){
            set_time_limit(0);
            $itemId = $this->getRequest()->getParam('item_id','');
            
            $itemId = preg_replace('/[^0-9]/', '', $itemId);
            $item = Service_SellerItem::getByField($itemId,'item_id');

            $token = Ebay_EbayLib::getUserToken($item['user_account']);
            
            $result = Ebay_EbayLib::GetItem($token, $itemId);
            if($result['GetItemResponse']['Ack'] == 'Success'){
                $item = $result['GetItemResponse']['Item'];
                unset($item['Description']);
                $itemStr = 'Quantity-QuantitySold:'.$item['Quantity'].'-'.$item['SellingStatus']['QuantitySold'].'='.($item['Quantity']-$item['SellingStatus']['QuantitySold']).'<br/>'.print_r($item,true);
                $itemStr = str_replace("\n", '<br/>', $itemStr);
                $itemStr = str_replace(" ", '&nbsp;', $itemStr);
                
                $return['message'] = $itemStr;
                $return['ask'] = 1;
            }else{
                $Errors = $result['GetItemResponse']['Errors'];
                $itemStr = print_r($Errors,true);
                $itemStr = str_replace("\n", '<br/>', $itemStr);
                $itemStr = str_replace(" ", '&nbsp;', $itemStr);
                $return['message'] = $itemStr;
            }
             
        }
        die(Zend_Json::encode($return));
    }

    /**
     * 查看item
     */
    public function viewEbayItemAction(){
        $return = array(
                "state" => 0,
                "message" => "No Data"
        );
    
        if($this->_request->isPost()){
            set_time_limit(0);
            $itemId = $this->getRequest()->getParam('item_id','');
    
            $itemId = preg_replace('/[^0-9]/', '', $itemId);
            $itemLog = Service_ItemLog::getByField($itemId,'item_id');
            if($itemLog){
                $content = unserialize($itemLog['content']);
                unset($content['Description']);
                $content = print_r($content,true);
                $content = str_replace("\n", '<br/>', $content);
                $content = str_replace(" ", '&nbsp;', $content);
                
                $return['message'] = $content;
                $return['state'] = 1;
            }
             
        }
        die(Zend_Json::encode($return));
    }
    /**
     * 补货数量同步到ebay
     */
    public function syncSupplyQtyAction(){
        $return = array(
            "state" => 0,
            "message" => "Request Err"
        );
        
        if($this->_request->isPost()){
            set_time_limit(0);
            
            $itemIds = $this->getRequest()->getParam('item_id', array());
            $process = new Service_SellerItemProcess();
            $return = array(
                'ask' => 1,
                'message' => '(系统会忽略无需补货的Item)操作结果如下'
            );
            
            $success = $fail = $rs =$rsArr= array();
            $updateRsArr = array();
            try {
                if(empty($itemIds)){
                    throw new Exception('没有需要补货的Item');
                }
                $itemIds = array_unique($itemIds);
                    
                // 更新按仓补货数量
                $process = new Common_SupplyQtyProcess();
                foreach($itemIds as $itemId){
                    $con = array(
                        'item_id' => $itemId
                    );
                    $rows = Service_SellerItemSupplyQty::getByCondition($con);
                    
                    foreach($rows as $k => $v){
                        if($v['supply_type'] != 1){
                            continue;
                        }
                        $wh_code = $v['supply_warehouse'];
                        $userAccount = $v['user_account'];
                        $product_sku = $v['sku'];
                        if(empty($product_sku)){
                            continue;
                        }
                        if(empty($wh_code)){
                            continue;
                        }
                        $qty = $process->getWarehouseInventory($userAccount, $product_sku, $wh_code, $v);
                        $v['qty'] = $qty;
                        // 更新补货数为产品可用数
                        $sql = "update seller_item_supply_qty set qty={$qty} where id='{$v['id']}';";
                        Common_Common::query($sql);
                    }
                }
                

                $process = new Service_SellerItemProcess();
                foreach($itemIds as $itemId){                   
                    $item = Service_SellerItem::getByField($itemId, 'item_id');
                    switch($item['sell_type']){
                        case '2':
                            $con = array(
                                'item_id' => $item['item_id']
                            );
                            $variations = Service_SellerItemVariations::getByCondition($con);
                            foreach($variations as $kk => $variation){
                                // $rsArr[] = $process->syncSupplyQtySingleHand($itemId, $variation['sku'], $variation['supply_qty']);
                                $con = array(
                                    'item_id' => $item['item_id'],
                                    'sku' => $variation['sku']
                                );
                                $supQty = Service_SellerItemSupplyQty::getByCondition($con);
                                if($supQty){
                                    $supQty = $supQty[0];
                                    $rsArr[] = $process->syncSupplyQtySingleHand($itemId, $variation['sku'], $supQty['qty']);
                                }
                            }
                            break;
                        case '1':
                            // $rsArr[] = $process->syncSupplyQtySingleHand($itemId, $item['sku'], $item['supply_qty']);
                            $con = array(
                                    'item_id' => $item['item_id'],
                                    'sku' => $item['sku']
                            );
                            $supQty = Service_SellerItemSupplyQty::getByCondition($con);
                            if($supQty){
                                $supQty = $supQty[0];                                
                                $rsArr[] = $process->syncSupplyQtySingleHand($itemId, $item['sku'], $supQty['qty']);
                            }
                            break;
                        default:
                    }
                    foreach($rsArr as $r){
                        $rr = array(
                                'item_id' => $r['item_id'],
                                'sku' => $r['sku'],
                                'supply_qty' => $r['supply_qty'],
                                'message' => $r['message']
                        );
                        if($r['ask'] == 1){
                            $success[] = $rr;
                        }else{
                            $fail[] = $rr;
                        }
                    }
                } 
                //数据同步到本地
                foreach($itemIds as $itemId){
                    $updateRs = Ebay_ItemEbayService::updateItem($itemId);
                    $updateRsArr[] = $updateRs;
                }
                
                $return['ask'] = 1;
                $return['message'] = '补货完成';
            } catch (Exception $e) {
                $return['message'] = $e->getMessage();
            }
            $return['success'] = $success;
            $return['fail'] = $fail;
            $return['rs'] = $rsArr;
            $return['updateRsArr'] = $updateRsArr;
            
        }
        die(Zend_Json::encode($return));
    }

    /**
     * 补货数量同步到ebay
     */
    public function syncSupplyQtyNewAction(){
        $this->forward('sync-supply-qty');
    }
    /**
     * 加入黑名单
     */
    public function addToBlackListAction(){
        $userAccount = $this->getParam('user_account');
        $sku = $this->getParam('sku');
        $itemId = $this->getParam('item_id','');
        
        $sku = trim($sku);
        $skuArr = explode("\n", $sku);
        $skuArr = array_unique($skuArr);
        $return = array(
            'ask' => 0,
            'message' => 'No Data'
        );
//         print_r($skuArr);exit;
        foreach($skuArr as $v){
            $v = trim($v);
            if(empty($v)){
                continue;
            }
            $result = Service_SellerItemProcess::addToBlackList($userAccount, $v,$itemId);
            $return['ask'] = 1;
            $return['message'] = '操作成功，结果如下';
            $return['result'][] = $result;
        }
        die(Zend_Json::encode($return));
    }

    /**
     * 解除黑名单
     */
    public function releaseBlackListAction(){
        $userAccount = $this->getParam('user_account');
        $sku = $this->getParam('sku');
        $itemId = $this->getParam('item_id','');
        $sku = trim($sku);
        $skuArr = explode("\n", $sku);
        $skuArr = array_unique($skuArr);
        $return = array('ask'=>0,'message'=>'No Data');
        foreach($skuArr as $v){
            $v = trim($v);
            if(empty($v)){
                continue;
            }
            $result = Service_SellerItemProcess::releaseBlackList($userAccount,$v,$itemId);
            $return['ask'] = 1;
            $return['message'] = '操作成功，结果如下';
            $return['result'][] = $result;
        }
        die(Zend_Json::encode($return));
    }
    /**
     * 获取黑名单
     */
    public function getBlackListAction(){

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
         
            $user_account = $this->getParam('user_account','');
        
            $condition['user_account'] = $user_account;
            $sku = $this->getParam('sku','');
        
            $condition['sku_like'] = $sku;
        
            foreach($condition as $k=>$v){
                if(!is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
            $count = Service_SellerItemBlackList::getByCondition($condition, 'count(*)');
            $return['total'] = $count;
        
            if ($count) {
                $rows = Service_SellerItemBlackList::getByCondition($condition, '*', $pageSize, $page);
                
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $user_account_arr =  Service_User::getPlatformUserNew('do','ebay');//Common_Common::getPlatformUser();
        $this->view->user_account_arr = $user_account_arr;
        $this->view->user_account_arr_json = Zend_Json::encode($user_account_arr);
        
        $con = array('warehouse_status'=>'1');
        $warehouse = Service_Warehouse::getByCondition($con);
        
        $warehouseArr = array();
        foreach($warehouse as $v){
            $warehouseArr[$v['warehouse_id']] = $v;
        }
        $this->view->warehouse=$warehouseArr;
        $this->view->warehouseJson=Zend_Json::encode($warehouseArr);
        
        echo Ec::renderTpl($this->tplDirectory . "seller_item_black_list.tpl", 'layout');
        
    }
    
    /**
     * 日志
     */
    public function getLogAction(){
        $itemId = $this->getParam('item_id', '');
        $type = $this->getParam('type', '1');//$type=1 操作日志  $type=2 补货日志
        $return = array(
            "ask" => 0,
            "message" => "No Data"
        );
        //操作日志
        $con = array(
            'item_id' => $itemId,
//             'type' => $type
        );
        $data = Service_SellerItemOpLog::getByCondition($con,"*",100,1,'siol_id desc');
        if($data){
            foreach($data as $k=>$v){
                if($v['op_user_id']){
                    $uRow = Service_User::getByField($v['op_user_id'],'user_id');
                    $v['user_name'] = $uRow['user_name'];
                }else{
                    $v['user_name'] = '系统';
                }
                $data[$k] = $v;
            }
            $return['data'] = $data;
            $return['ask'] = 1;
        }
        die(Zend_Json::encode($return));
    }
    /**
     * 补货日志
     */
    public function getSupLogAction(){
        $itemId = $this->getParam('item_id', '');
        $type = $this->getParam('type', '1');//$type=1 操作日志  $type=2 补货日志
        $return = array(
                "ask" => 0,
                "message" => "No Data"
        );       
    
        //补货日志
        $con = array(
                'item_id' => $itemId,
        );
        $data = Service_SellerItemSupLog::getByCondition($con,"*",100,1,'sisl_id desc');
        if($data){
            foreach($data as $k=>$v){
                if($v['op_user_id']){
                    $uRow = Service_User::getByField($v['op_user_id'],'user_id');
                    $v['user_name'] = $uRow['user_name'];
                }else{
                    $v['user_name'] = '系统';
                }
                $data[$k] = $v;
            }
            $return['data'] = $data;
            $return['ask'] = 1;
        }
    
    
    
        die(Zend_Json::encode($return));
    }
    
    /**
     * 查看图片
     */
    public function viewImgAction(){
        $itemId = $this->getParam('item_id', '');
        $account = $this->getParam('acc', '');
        $item = Service_SellerItem::getByField($itemId,'item_id');
        if($item&&$item['pic_path']){
            header('Location: '.$item['pic_path']);
        }else{
            header("Location: /images/base/noimg.jpg");            
        }
    }

    /**
     * 跳转到ebay
     */
    public function hrefEbayAction(){
        $itemId = $this->getParam('item_id', '');
        $acc = $this->getParam('acc', '');
        $item = Service_SellerItem::getByField($itemId,'item_id');
        if($item&&$item['item_url']){
            header('Location: '.$item['item_url']);
        }else{
            $result = Ebay_ItemEbayService::updateItem($itemId,$acc);
            if($result['GetItemResponse']['Ack']=='Success'){
                header('Location: '.$result['Item']['ListingDetails']['ViewItemURL']);
            }else{
                echo '404';
            }
        }
    }
    /**
     * 设定无货在线
     */
    public function setNoStockOnlineAction(){
        $no_stock_online_val = $this->getParam('no_stock_online', '');
        $item_id_arr = $this->getParam('item_id');
        $service = new Ebay_NoStockOnlineService();
        $return = $service->noStockOnlineSet($item_id_arr, $no_stock_online_val);
        echo Zend_Json::encode($return);
        exit();
    }
    
    /**
     * 补货日志
     */
    public function getSupLogListAction(){
        $user_account_arr = Service_User::getPlatformUserNew('do'); // 绑定店铺账号
        if($this->_request->isPost()){
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            
            $condition = array();
            
            $user_account = $this->getParam('user_account', '');
            
            $condition['user_account'] = $user_account;
            $condition['user_account_arr'] = array_keys($user_account_arr);
            
            $item_id = $this->getParam('item_id', '');
            $sku = $this->getParam('sku', '');

            $condition['item_id'] = $item_id;
            $condition['sku_like'] = $sku;
            foreach($condition as $k => $v){
                if(! is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
            
            $count = Service_SellerItemSupLog::getByConditionInnerJoinSellerItem($condition, 'count(*)');
            $return['total'] = $count;
            
            if($count){
                $rows = Service_SellerItemSupLog::getByConditionInnerJoinSellerItem($condition, '*', $pageSize, $page,'sisl_id desc');
                $data = array();
                foreach($rows as $k => $v){
                    //===============
//                     $v['user_account'] = $user_account_arr[$v['user_account']];
                    if($v['op_user_id']){
                        $uRow = Service_User::getByField($v['op_user_id'],'user_id');
                        $v['user_name'] = $uRow['user_name'];
                    }else{
                        $v['user_name'] = '系统';
                    }                
                    $rows[$k] = $v;
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        
        $this->view->user_account_arr = $user_account_arr;
        $this->view->user_account_arr_json = Zend_Json::encode($user_account_arr);
        
        echo Ec::renderTpl($this->tplDirectory . "seller_item_sup_list.tpl", 'layout');
    }
    
    /**
     * 多品图片
     */
    public function variationImgAction(){
        $itemId = $this->getParam('item_id', '0');
        $sku = $this->getParam('sku', 'sku');
        try{            
            $sql = "
            SELECT
            c.*
            FROM
            seller_item_variations a
            INNER JOIN `seller_item_variations_attr` b ON a.variation_id = b.variation_id
            INNER JOIN ebay_item_pictures c ON b.item_id = c.item_id
            AND b.`name` = c.`name`
            AND b.val = c.val
            WHERE
            a.item_id = '{$itemId}'
            AND a.sku = '{$sku}'  order by c.id asc;
            ";
            $row = Common_Common::fetchRow($sql);
            if(! $row){
                $sql = "
                SELECT
                c.*
                FROM
                seller_item_variations a
                INNER JOIN `seller_item_variations_attr` b ON a.variation_id = b.variation_id
                INNER JOIN ebay_item_pictures c ON b.item_id = c.item_id
                AND b.`name` = c.`name`
                AND b.val = c.val
                WHERE
                a.item_id = '{$itemId}' order by c.id asc;
                ";
                $row = Common_Common::fetchRow($sql);
            }
            if(! $row){
                $sql = "                                   
                    SELECT
                    	*
                    FROM
                    	ebay_item_pictures a
                    WHERE 
                    a.item_id = '{$itemId}'  order by a.id asc;
                ";
                $row = Common_Common::fetchRow($sql);                
            }
            if(! empty($row)){
//                 shuffle($row);                
//                 $row = array_pop($row);
                //print_r($row);exit;
                header("Location: " . $row['picture_url']);
            }else{
                header("Location: /images/base/noimg.jpg");
            }
        }catch(Exception $e){
            header("Location: /images/base/noimg.jpg");
        }
    }
    
    public function tAction(){
        set_time_limit(0);
        $rows = Service_SellerItem::getByCondition(array(), array(
            'item_id'
        ));
        foreach($rows as $row){
            Ebay_ItemEbayService::saveSellerItemNew($row['item_id']);
        }
        echo '--------------------';
    }
    
    public function getWarehouseInventoryLogAction(){
        $userAccount = $this->getParam('acc','');
        $product_sku = $this->getParam('sku','');
        $wh_code = $this->getParam('wh_code','');
        
        $process = new Common_SupplyQtyProcess();
        $qty = $process->getWarehouseInventory($userAccount, $product_sku, $wh_code, array());
        
        $log = $process->getWarehouseInventoryLog();
        
        
        $log = print_r($log,true);

        $log = preg_replace('/ /', '&nbsp;', $log);
        $log = preg_replace('/\n/', '</br>', $log);
        
        echo $log;
    }
}