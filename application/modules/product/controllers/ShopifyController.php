<?php
class Product_ShopifyController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/shopify/"; 
        $this->serviceClass = new Service_ShopifyProducts();
    }

    public function toWmsAction(){
        $limit = $this->_request->getParam('limit', 100);
        Common_ApiProcess::createOrderProductToWmsAll($limit);
        echo "==finish==";
    }
    
    public function listAction()
    {
        
        $user_account_arr = Service_User::getPlatformUserNew('do');//绑定店铺账号
//                 print_r($user_account_arr);exit;
        $this->view->user_account_arr = $user_account_arr;
        $this->view->user_account_arr_json = Zend_Json::encode($user_account_arr);
        if ($this->_request->isPost()) {
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);

            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;

            $return = array(
                "state" => 0,
                "message" => "No Data"
            );

            $orderBy = $this->_request->getParam('sort', '');
            $orderBy = empty($orderBy)?'':$orderBy;
            
            $condition = array();

            $status = $this->getParam('status','');
            $recommand = $this->getParam('recommand','');
            
            $item_status = $this->getParam('item_status','');
            
            $sell_type = $this->getParam('sell_type','');
            $need_supply = $this->getParam('need_supply','');
            $user_account = $this->getParam('user_account','');
            $sync_status = $this->getParam('sync_status','');
            $auto_supply = $this->getParam('auto_supply','');

            $condition['status'] = $status;
            $condition['recommand'] = $recommand;
            
            $condition['item_status'] = $item_status;
            $condition['sell_type'] = $sell_type;
            $condition['auto_supply'] = $auto_supply;
            $condition['need_supply'] = $need_supply;
            $condition['sync_status'] = $sync_status;
            $condition['user_account'] = $user_account;
//             $condition['user_account_arr'] = array_keys($user_account_arr);
            
            
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
                    $condition['id_arr'] = $code;
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
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $fileds = array(
                    'id'
                );
                $rows = $this->serviceClass->getByCondition($condition, $fileds, $pageSize, $page,$orderBy);
//                 print_r($rows);exit;
                $data = array();
                foreach($rows as $k=>$v){
                    $v = Common_ApiProcess::getProductById($v['id']);
                    unset($v['body_html']);
                    $rows[$k] = $v;
                    $data[$v['id']] = $v;
                }
//                 print_r($rows);exit;
                $return['data'] = $data;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }

        //创建order_product_to_wms数据
        Common_ApiProcess::createOrderProductToWmsAll();
        
        $con = array('warehouse_status'=>'1');
        $warehouse = Service_Warehouse::getByCondition($con);
        
        $warehouseArr = array();
        foreach($warehouse as $v){
            $warehouseArr[$v['warehouse_id']] = $v;
        }
        $this->view->warehouse=$warehouseArr;
        $this->view->warehouseJson=Zend_Json::encode($warehouseArr);
        echo Ec::renderTpl($this->tplDirectory . "product_list.tpl", 'layout');
    }
    /**
     * 设定推荐上下架
     */
    public function recommandAction(){
        set_time_limit(0);
        $wms_db = Zend_Registry::get('wms_db');
        $sql = "SELECT a.id,a.status, c.sum from 
                shopify_products a INNER JOIN
                shopify_products_variants b on a.id=b.product_id
                INNER JOIN (select product_barcode,SUM(pi_sellable)+SUM(pi_pending) sum from {$wms_db}.product_inventory  group by product_barcode) c on b.sku=c.product_barcode; ";
        
        $sql = "
                SELECT a.id,a.status, SUM(c.sum) as sum from 
                shopify_products a INNER JOIN
                shopify_products_variants b on a.id=b.product_id
                INNER JOIN (select product_barcode,SUM(pi_sellable)+SUM(pi_pending) sum from {$wms_db}.product_inventory  group by product_barcode) c on b.sku=c.product_barcode group by a.id; 
                
                ";

        $sql = "
        SELECT a.id,a.status, SUM(c.sum) as sum from
        shopify_products a INNER JOIN
        shopify_products_variants b on a.id=b.product_id
        INNER JOIN (select product_barcode,SUM(pi_sellable) sum from {$wms_db}.product_inventory  group by product_barcode) c on b.sku=c.product_barcode group by a.id;
        
        ";
        echo $sql;
        $db = Common_Common::getAdapter();
        $rs = $db->fetchAll($sql);        
        $updateRow = array('recommand'=>'0');
        Service_ShopifyProducts::update($updateRow, '1','1');   
//         print_r($rs);exit;
        foreach($rs as $v){
            $updateRow = array();
            if($v['status']==0){//已下架，判断仓库库存>0，推荐上架
                if($v['sum']>0){
                    $updateRow['recommand'] = '1';
                }
            }

            if($v['status']==1){//已上架,判断仓库库存<=0，推荐下架
                if($v['sum']<=0){
                    $updateRow['recommand'] = '2';
                }
            }
            
            if(!empty($updateRow)){
                Service_ShopifyProducts::update($updateRow, $v['id'],'id'); 
                print_r($updateRow); 
                print_r($v);              
            }
        }
        echo "ok";
    }
    /**
     * 下架----------------------------该方法废弃
     */
//     public function unpublishAction(){
//         $ids = $this->getParam('id', '');
//         $ids = preg_replace('/[^0-9]+/', ' ', $ids);
//         $ids = trim($ids);
//         $return = array();
//         if(! empty($ids)){
//             $ids = explode(' ', $ids);
//             $ids = array_unique($ids);
//             foreach($ids as $id){
//                 $rs = Common_ApiProcess::unpublishProductProcess($id);
//                 $return[] = $rs;
//             }
//         }
//         echo Zend_Json::encode($return);
//     }
    /**
     * 上下架
     */
    public function publishAction(){
        set_time_limit(0);
        $params = $this->getRequest()->getParams();
//         print_r($params);exit;
        $productIdArr = $this->getParam('product_id', array());
        $status = $this->getParam('product_status', '1');
        $supply_data = array();        
        foreach($productIdArr as $id => $id_val){
            
        }        
        $rs = Common_ShopifyProductPublishProcess::publishProductProcess($productIdArr, $status);
        echo Zend_Json::encode($rs);
    }

    /**
     * 修改在线数
     */
    public function updateSupplyQtyAction(){
        set_time_limit(0);
        $params = $this->getRequest()->getParams();
        $id_qty = $this->getParam('id_qty', array());
        $supply_data = array();
        foreach($id_qty as $id => $qty){
            $ids = explode('_', $id);
            $proId = $ids[0];
            $varId = $ids[1];
            $arr = array(
                    // 'shopify_product_id' => $proId,
                    'variant_id' => $varId,
                    'qty' => $qty
            );
            $supply_data[$proId][] = $arr;
        }
        $rs = Common_ShopifyProductQtyProcess::changeProductQtyProcess($supply_data);
        echo Zend_Json::encode($rs);
    }

    /**
     * 改价
     */
    public function updatePriceAction(){
        set_time_limit(0);
        $params = $this->getRequest()->getParams();
        $id_price = $this->getParam('id_price', array());
        $supply_data = array();foreach($id_price as $id => $priceArr){
            $ids = explode('_', $id);
            $proId = $ids[0];
            $varId = $ids[1];
            $arr = array(
                    // 'shopify_product_id' => $proId,
                    'variant_id' => $varId,
                    'price' => $priceArr['price'],
                    'compare_at_price' => $priceArr['compare_at_price'],
            );
            $supply_data[$proId][] = $arr;
        }
//         print_r($supply_data);exit;
        $rs = Common_ShopifyProductPriceProcess::changeProductPriceProcess($supply_data);
        echo Zend_Json::encode($rs);
    }
    
    /**
     * 更新产品信息
     * @throws Exception
     */
    public function getProductAction(){
        $return = array(
            'ask' => 0,
            'message' => 'Fail'
        );
        try{
            $productId = $this->getParam('id', '');
            $p = Service_ShopifyProducts::getByField($productId, 'id');
            if(! $p){
                throw new Exception('product not exists');
            }
            $log = '';
            $con = array(
                'user_account' => $p['user_account'],
                'company_code' => $p['company_code']
            );
            $pUser = Service_PlatformUser::getByCondition($con);
            if(empty($pUser)){
                throw new Exception('platform user not exists');
            }
            $pUser = $pUser[0];
            $shop_domain = $pUser['user_account'];
            $api_key = $pUser['user_token_id'];
            $secret = $pUser['user_token'];
            $password = $pUser['seller_id'];
            
            $process = new Common_ApiProcess($shop_domain, $api_key, $secret, $password);
            // 更新产品信息
            $data = $process->loadProductSingle($productId);
            $return['data'] = $data;
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        echo Zend_Json::encode($return);
    }
}