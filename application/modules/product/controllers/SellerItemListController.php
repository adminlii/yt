<?php
class Product_SellerItemListController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/seller-item-list/"; 
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
            
            $out_of_stock_control = $this->getParam('out_of_stock_control','');
            
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

            $condition['out_of_stock_control'] = $out_of_stock_control;
            
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
            $count = $this->serviceClass->getByConditionInnerJoinVariation($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) { 
            	$fields = array('item_id','sku');
                $rows = $this->serviceClass->getByConditionInnerJoinVariation($condition, $fields, $pageSize, $page);
                $data = array();
                $itemArr = array();
                foreach($rows as $k=>$v){
                    $v = Service_SellerItemProcess::getSellerItemDetailNew($v['item_id'],$v['sku']);
//                     print_r($v);exit;
                    foreach($v as $kk=>$vv){
                        if(is_null($vv)){
                            $vv = Ec::Lang('nothing','auto');//'无';
                        }
                        $v[$kk] = $vv;
                    }
                    $rows[$k] = $v;
                    $data[$v['variation_id']] = $v;
                    $itemArr[$v['item_id']] = $v;
                }
//                 print_r($data);exit;
                $return['data'] = $data;
                $return['item_arr'] = $itemArr;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }

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

    public function initNotVariationAction(){
		$sql = "select a.* from seller_item a left join seller_item_variations b on a.item_id=b.item_id where b.item_id is null;";
		echo $sql.'<br/>';
		$rows = Common_Common::fetchAll ( $sql );
		foreach ( $rows as $row ) {
			$itemVariation = array (
					"item_id" => $row ['item_id'],
					"product_sku" => $row ['sku'],
					"sku" => $row ['sku'] ? $row ['sku'] : '-NoSku-',
					"sku_desc" => '',
					"qty" => $row ["sell_qty"],
					"qty_sold" => $row ['sold_qty'],
					"start_pice" => $row ['price_sell'],
					"currency" => $row ['currency'] 
			);
			
			$exist = Service_SellerItemVariations::getByField ( $row ['item_id'], 'item_id' );
			if ($exist) {
				$variation_id = $exist ['variation_id'];
				Service_SellerItemVariations::update ( $itemVariation, $variation_id, "variation_id" );
			} else {
				$itemVariation ['supply_qty'] = $row ['supply_qty']; // 补货数
				$variation_id = Service_SellerItemVariations::add ( $itemVariation );
			}
		}
		
		echo '====================================';
	}
	
	public function tAction(){
		$item_id = '171533628412';
		$svc = new Ebay_Item_OutOfStockControl();
		$svc->setItemId($item_id);
		$svc->setOutOfStockControl('false');
		$svc->setQty('35');
		$rs = $svc->outOfStockControlProcess();
		print_r($rs);
	}
}