<?php
class Product_AmazonMerchantListController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/amazon-merchant-list/"; 
        $this->serviceClass = new Service_AmazonMerchantListing();
    }

    public function listAction()
    {
        $supply_type_arr = Common_Type::supplyTypeArr();
        $status_arr = Common_Type::supplyStatusArr();
        $sync_status_arr = Common_Type::syncStatusArr();
        $user_account_arr = Service_User::getPlatformUserNew('do','amazon');//绑定店铺账号         
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
            $fulfillment_type = $this->getParam('fulfillment_type','');
            $user_account = $this->getParam('user_account','');
            $sync_status = $this->getParam('sync_status','');
            $auto_supply = $this->getParam('auto_supply','');
            
            $condition['item_status'] = $item_status;
            $condition['sell_type'] = $sell_type;
            
            $condition['auto_supply'] = $auto_supply; 
            $condition['sync_status'] = $sync_status;
            $condition['user_account'] = $user_account;
            $condition['user_account_arr'] = array_keys($user_account_arr);
            
            
            $type = $this->getParam('type','seller_sku');
            $code = $this->getParam('code','');
            switch ($type){
                case 'product_id':
                    
                    $code = preg_replace('/[^0-9A-Za-z]/', ' ', $code);
                    $code = trim($code);
                    if($code){
                        $code = explode(' ', $code);
                    }else{
                        $code = array();
                    }
                    $condition['product_id_arr'] = $code;
                    break;
                case 'listing_id':

                    $code = preg_replace('/[^0-9A-Za-z]/', ' ', $code);
                    $code = trim($code);
                    if($code){
                        $code = explode(' ', $code);
                    }else{
                        $code = array();
                    }
                    $condition['listing_id_arr'] = $code;
                    break;
                case 'asin1':

                    $code = preg_replace('/[^0-9A-Za-z]/', ' ', $code);
                    $code = trim($code);
                    if($code){
                        $code = explode(' ', $code);
                    }else{
                        $code = array();
                    }
                    $condition['asin1_arr'] = $code;
                    break;
                case 'asin2':

                    $code = preg_replace('/[^0-9A-Za-z]/', ' ', $code);
                    $code = trim($code);
                    if($code){
                        $code = explode(' ', $code);
                    }else{
                        $code = array();
                    }
                    $condition['asin2_arr'] = $code;
                    break;
                case 'asin3':

                    $code = preg_replace('/[^0-9A-Za-z]/', ' ', $code);
                    $code = trim($code);
                    if($code){
                        $code = explode(' ', $code);
                    }else{
                        $code = array();
                    }
                    $condition['asin3_arr'] = $code;
                    break;

                    case 'fulfillment_channel':
                        $condition['fulfillment_channel'] = trim($code);
                        break;
                case 'seller_sku_arr':
                    $code = preg_replace('/\s+/', ' ', $code);
                    $code = trim($code);
                    if($code){
                        $code = explode(' ', $code);
                    }else{
                        $code = array();
                    }
                    $condition['seller_sku_arr'] = $code;
                    break;
                    
                default:
                    $condition['seller_sku_like'] = trim($code);
            }


            $sell_qty_from = $this->getParam('sell_qty_from','');
            $sell_qty_to = $this->getParam('sell_qty_to','');
            
            $condition['sell_qty_from'] = $sell_qty_from;
            $condition['sell_qty_to'] = $sell_qty_to;

            $fulfillment_type = $this->getParam('fulfillment_type','');
            $condition['fulfillment_type'] = $fulfillment_type;
            
            foreach($condition as $k=>$v){
                if(!is_array($v)){
                    $condition[$k] = trim($v);
                }
            }
//             print_r($condition);exit;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $order_by = array(
                    'seller_sku asc',
                    'open_date asc'
                );
                $rows = $this->serviceClass->getByCondition($condition, '*', $pageSize, $page,$order_by);
                $data = array();
                foreach($rows as $k => $v){
                    $v['seller_sku'] = empty($v['seller_sku']) ? '--NoSku--' : $v['seller_sku'];
                    $v['sku'] = $v['seller_sku'];
                    $v['item_status'] = Ec::Lang($v['item_status']);
                    unset($v['item_description']);
                    $rArr = Service_ProductCombineRelationProcess::getRelation($v['seller_sku'], $v['user_account']);
                    $r = array();
                    if($rArr){
                        foreach($rArr as $kk => $vv){
                            $r[] = array('sub_sku'=>$vv['pcr_product_sku'],'sub_qty'=> $vv['pcr_quantity']);
                        }
                    }else{
                        $r[] = array('sub_sku'=>$v['seller_sku'],'sub_qty'=>1);
                    }
                    $v['warehouse_sku'] = $r;
//                     print_r($v);exit;
                    $supplySet = Service_SellerItemProcess::getSupplySet($v['product_id'],$v['seller_sku'],$v['user_account'],'amazon');
//                     print_r($supplySet);exit;
                    $v['supplySet'] = $supplySet;
//                     //已有补货设置
//                     $con = array(
// //                         'item_id' => $v['product_id'],
//                         'sku' => $v['seller_sku'],
//                         'user_account' => $v['user_account'],
//                         'platform' => $v['platform']
//                     );
                    
//                     $supplyQty = Service_SellerItemSupplyQty::getByCondition($con);
// //                     print_r($supplyQty);exit;
//                     if($supplyQty){
//                         $supplyQty = array_pop($supplyQty);
//                         $v['supply_type'] = $supplyQty['supply_type'];
//                         $v['supply_warehouse'] = $supplyQty['supply_warehouse'];
//                         $v['supply_qty'] = $supplyQty['qty'];
//                         $v['status'] = $supplyQty['status'];
                        
//                         $v['supply_sync_status'] = isset($sync_status_arr[$supplyQty['sync_status']])?$sync_status_arr[$supplyQty['sync_status']]:'SyncError';
//                         $v['supply_sync_time'] = empty($supplyQty['sync_time'])?'--':$supplyQty['sync_time'];
//                     }else{
//                         $v['supply_type'] = '';
//                         $v['supply_warehouse'] = '--';
//                         $v['supply_qty'] = '--';
//                         $v['status'] = null;
//                         $v['supply_sync_status'] = '--';
//                         $v['supply_sync_time'] = '--';
//                     }
//                     $v['supply_type_title'] = '未设置';                     
//                     if(!empty($v['supply_type'])){
//                         $v['supply_type_title'] = $supply_type_arr[$v['supply_type']];
//                         if($v['supply_type']=='1'){
//                             $v['supply_type_title'].=','.Ec::Lang('supply_warehouse').':'.$v['supply_warehouse'];
//                         }else{
//                             $v['supply_type_title'].=','.Ec::Lang('supply_qty').':'.$v['supply_qty'];
//                         }
//                     }
                    
                    //商品价格 start
                    $con = array(
                            'offer_seller_sku' => $v['seller_sku'],
                            'company_code' => $v['company_code'],
                            'user_account' => $v['user_account'],
                    );
                    $prices = Service_AmazonMyPriceForSku::getByCondition($con);
                    if($prices){
//                         print_r($prices);exit;
                        $prices = array_pop($prices);
                        $v['my_price'] = $prices;
                    }else{
                        $con = array(
                                'seller_sku' => $v['seller_sku'],
                                'company_code' => $v['company_code'],
                                'user_account' => $v['user_account'],
                        );
                        $prices = Service_AmazonMyPriceForSku::getByCondition($con);
                        if($prices){
                            $prices = array_pop($prices);
                            $v['my_price'] = $prices;
                        }
                    }
                    //商品价格 end

                    $v['acc'] = $v['user_account'];
                    
                    $v['status_title'] = isset($v['status'])?$status_arr[$v['status']]:'未设置';
                    $v['user_account'] = isset($user_account_arr[$v['user_account']])?$user_account_arr[$v['user_account']]['platform_user_name']:$v['user_account'];
                    
                    $priceSet = Service_AmazonMerchantListingPriceSet::getByField($v['listing_id'],'listing_id');
                    if($priceSet){
                        $priceSet['sync_status_title'] = isset($sync_status_arr[$priceSet['sync_status']])?$sync_status_arr[$priceSet['sync_status']]:'SyncError';
                        
                        $v['price_set'] = $priceSet;
                    }
                    $con = array('asin'=>$v['asin1'],'user_account'=>$v['acc']);
                    $lookup = Service_AmazonLookup::getByCondition($con);
                    if($lookup){
                        $lookup = array_pop($lookup);
                        $v['lookup'] = $lookup;
                    }
                    $rows[$k] = $v;
                    $data[$v['listing_id']] = $v;
                }
//                 print_r($data);exit;
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
        $this->view->warehouseArr=$warehouseArr;
        $this->view->warehouseJson=Zend_Json::encode($warehouseArr);
        

        $this->view->supply_type_arr = $supply_type_arr;         
        $this->view->status_arr = $status_arr;
        
        echo Ec::renderTpl($this->tplDirectory . "item_list.tpl", 'layout');
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
//         print_r($pu);exit;
        try{
            $data = array();
            foreach($pu as $listing_id => $v){
                $data[$listing_id] = array(
                        'supply_type' => $v['supply_type'],
                        'supply_warehouse' => $v['supply_warehouse'],
                        'qty' => $v['supply_qty'],
                        'status' => $v['status'],
                );
            }
            foreach($data as $listing_id => $v){
                $merchant_list = Service_AmazonMerchantListing::getByField($listing_id, 'listing_id');
                if(! $merchant_list){
                    throw new Exception('Inner Error-->' . $listing_id);
                }
                $row = array(
                    'platform' => 'amazon',
                    'company_code' => Common_Company::getCompanyCode(),
                    'user_account' => $merchant_list['user_account'],
                    'item_id' => $merchant_list['product_id'],
                    'sku' => $merchant_list['seller_sku'],
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
                            throw new Exception('请选择仓库-->'."ProductID:[{$merchant_list['product_id']}],Seller SKU[{$merchant_list['seller_sku']}]");
                        }
                        unset($row['sync_status']);
                        unset($row['qty']);
                        break;
                    case '2':
                        if(!preg_match('/^[0-9]+$/', $v['qty'])){
                            throw new Exception('请填写补货数(需为数字)-->'."ProductID:[{$merchant_list['product_id']}],Seller SKU[{$merchant_list['seller_sku']}]");
                        }
                        break;
                    default:
                        throw new Exception(Ec::Lang('supply_type_err'));
                }
                

                $con = array(
                        //'item_id' => $merchant_list['product_id'],
                        'sku' => $merchant_list['seller_sku'],
                        'user_account' => $merchant_list['user_account'],
                        'platform' => 'amazon',
                        'company_code' => Common_Company::getCompanyCode()
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

                $logContent = "Amazon补货数据初始化,初始化参数：\n" . print_r($row, true);
                Service_SellerItemProcess::log($row['item_id'], $logContent, 1111);
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
    
        echo Zend_Json::encode($return);
    }

    /**
     * 设置价格
     * @throws Exception
     */
    public function saveProductPriceAction()
    {
        $return = array(
                'ask' => 0,
                'message' => 'Fail.'
        );
        $pu = $this->getParam('pu', array());
//                 print_r($pu);exit;
        try{
            $data = array();
            foreach($pu as $listing_id => $v){
                $data[$listing_id] = array(
                        'regular_price' => $v['StandardPrice'],
                        'regular_price_currency' => $v['StandardPriceCurrency'],
                        'listing_price' => $v['SalePrice'],
                        'listing_price_currency' => $v['SalePriceCurrency'],
                        'start_date' => $v['StartDate'],
                        'end_date' => $v['EndDate'],
                );
            }
            foreach($data as $listing_id => $v){
                $merchant_list = Service_AmazonMerchantListing::getByField($listing_id, 'listing_id');
                if(! $merchant_list){
                    throw new Exception('Inner Error-->' . $listing_id);
                }
                $row = array(
                        'platform' => 'amazon',
                        'company_code' => Common_Company::getCompanyCode(),
                        'user_account' => $merchant_list['user_account'],

                        'listing_id' => $listing_id,
                        'seller_sku' => $merchant_list['seller_sku'],
                        'regular_price' => $v['regular_price'],
                        'regular_price_currency' => $v['regular_price_currency'],
                        'listing_price' => $v['listing_price'],
                        'listing_price_currency' => $v['listing_price_currency'],
                        'start_date' => $v['start_date'],
                        'end_date' => $v['end_date'],

                        'sync_status'=>'0',
                        'sync_time'=>'0000-00-00 00:00:00',
                        'op_user_id' => Service_User::getUserId()
                );
                $row = Common_ApiProcess::nullToEmptyString($row);
                
                if(!preg_match('/^[0-9\.]+$/', $v['regular_price'])){
                    throw new Exception('regular_price Error-->'."ProductID:[{$merchant_list['product_id']}],Seller SKU[{$merchant_list['seller_sku']}]");
                }
                //有折扣价
                if(!empty($v['listing_price'])){
                    //折扣是数字
                    if(!preg_match('/^[0-9\.]+$/', $v['listing_price'])){
                        throw new Exception('listing_price Error-->'."ProductID:[{$merchant_list['product_id']}],Seller SKU[{$merchant_list['seller_sku']}]");
                    }
                    //折扣与售价不同
                    if($v['listing_price']!=$v['regular_price']){
                        if(!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}/', $v['start_date'])){
                            throw new Exception('amazon_time Error-->'."ProductID:[{$merchant_list['product_id']}],Seller SKU[{$merchant_list['seller_sku']}]");
                        }
                        if(!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}/', $v['end_date'])){
                            throw new Exception('amazon_time Error-->'."ProductID:[{$merchant_list['product_id']}],Seller SKU[{$merchant_list['seller_sku']}]");
                        }
                        //判断当前时间<开始时间<结束时间==============待定
                
                        //判断折扣价<销售价=================待定
                
                    }else{
                        //折扣与售价相同
                        $row['start_date'] = '';
                        $row['end_date'] = '';
                    }
                }else{
                    //无折扣时，折扣==售价
                    $row['listing_price'] = $v['regular_price'];
                    $row['start_date'] = '';
                    $row['end_date'] = '';
                }           
                
                $exist = Service_AmazonMerchantListingPriceSet::getByField($listing_id,'listing_id');
    
                $row = Common_ApiProcess::nullToEmptyString($row);
                if($exist){                  
                    $row['update_time'] = now();
                    Service_AmazonMerchantListingPriceSet::update($row, $exist['id'], 'id');
                }else{
                    $row['add_time'] = now();
                    $row['update_time'] = now();
                    Service_AmazonMerchantListingPriceSet::add($row);
                }
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
    
        echo Zend_Json::encode($return);
    }
    public function getMyPriceForSkuAction(){
        try{
            $id = $this->getParam('id', '');
            $amazonMerchantListing = Service_AmazonMerchantListing::getByField($id, 'id');
//             print_r($amazonMerchantListing);exit;
            $user_account = $amazonMerchantListing['user_account'];
            $company_code = $amazonMerchantListing['company_code'];
            $seller_sku = $amazonMerchantListing['seller_sku'];
            
            $con = array(
                    'company_code' => $company_code,
                    'user_account' => $user_account,
                    'platform' => "amazon"
            );
            $resultPlatformUser = Service_PlatformUser::getByCondition($con);
            if($resultPlatformUser){
                $resultPlatformUser = array_pop($resultPlatformUser);
            }
            if(empty($resultPlatformUser)){
                throw new Exception(print_r($con, true) . 'Not Exist');
            }
            $token_id = $resultPlatformUser["user_token_id"];
            $token = $resultPlatformUser["user_token"];
            $saller_id = $resultPlatformUser["seller_id"];
            $site = $resultPlatformUser["site"];
            
            $company_code = $resultPlatformUser['company_code'];
            $user_account = $resultPlatformUser['user_account'];            
            // 逻辑处理
            $service = new Amazon_ProductMyPriceForSkuService($token_id, $token, $saller_id, $site);
            $service->setCompanyCode($company_code);
            $service->setUserAccount($user_account);
            $IdArr = array();
            $IdArr[] = $seller_sku;
            $rs = $service->GetMyPriceForSKU($IdArr);
            $rs = print_r($rs,true);
            $rs = preg_replace('/\n/','<br/>',$rs);
            $rs = preg_replace('/ /','&nbsp;',$rs);
            echo $rs;
        }catch (Exception $e){
            echo $e->getMessage();
        }        
    }
}