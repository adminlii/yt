<?php
class Platform_OrderController extends Ec_Controller_Action {
	public function preDispatch() {

		$this->tplDirectory = "platform/views/order/";
		$this->serviceClass = new Service_Orders();

		$platform = $this->getParam('platform','');
// 		$user_account_arr = Service_User::getPlatformUser('do',$platform);//绑定店铺账号
		$con = array('company_code'=>Service_User::getCustomerCode());
		$user_account_arr = Service_PlatformUser::getByCondition($con);//
//         print_r($user_account_arr);exit;
		$this->user_account_arr = $user_account_arr;
		$this->view->load_order_day = 7;
	}
	public function listAction() {
        $platform = $this->_request->getParam('platform', '');
        $this->view->platform = $platform;
        $user_account_arr = $this->user_account_arr;
        
        if($this->_request->isPost()){
            set_time_limit(0);
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                "state" => 0,
                "message" => "No Data"
            );
            
            $status = $this->_request->getParam('status', '');
            $condition = array(
                'company_code' => Common_Company::getCompanyCode()
            );
            
            $condition['platform'] = $this->_request->getParam('platform', ''); // 平台
            $user_account = $this->_request->getParam('user_account', ''); // 账号
            $condition['user_account'] = $user_account;
            $condition['consignee_country'] = $this->getRequest()->getParam('country', ''); // 国家
            $keyword = $this->getRequest()->getParam('keyword', ''); // 关键字
            $condition['order_status'] = $this->getRequest()->getParam('status', ''); // 状态
            $condition['platform_ship_status'] = $this->getRequest()->getParam('platform_ship_status', ''); // 状态
            $keyword = trim($keyword);
            $company_code = Common_Company::getCompanyCode();
            if($keyword!=''){
            	//关键字搜索，需要限制数量
            	$sql = "SELECT order_id FROM `orders_keyword` where company_code='{$company_code}'";
            	if($user_account){
            		$sql.=" and user_account='{$user_account}'";
            	}
            	$sql.=" and keyword like '%{$keyword}%' limit 1000;";
            	//             echo $sql;exit;
            	$order_id_arr_keyword = Common_Common::fetchAll($sql);
            	if(empty($order_id_arr_keyword)){
            		die(Zend_Json::encode($return));
            	}
            	$oIdArr = array();
            	foreach($order_id_arr_keyword as $order_id){
            		$oIdArr[] = $order_id['order_id'];
            	}
            	$conditionItem = array('order_id_arr'=>$oIdArr);
            	$condition = array_merge($conditionItem, $condition);
            }
            
            $refrence_no = $this->_request->getParam('refrence_no','');
            $refrence_no = preg_replace('/[^a-zA-Z0-9_\-]/', ' ', $refrence_no);
            $refrence_no = preg_replace('/\s+/', ' ', $refrence_no);
            $refrence_no = trim($refrence_no);
            
            if($refrence_no) {
            	$refrence_no = explode(' ', $refrence_no);
            	if(count($refrence_no)==1){
            		$condition['refrence_no'] = $refrence_no[0];
            	}else{
            		$condition['refrence_no_arr'] = $refrence_no;
            	}
            }else{
            	$refrence_no = array();
            }
            
            foreach($condition as $k => $v){ // 去除条件中的空格
                if(is_string($v)){
                    $condition[$k] = trim($v);
                }
            }
            $orderBy = '';
            switch($status){
                case '2':
                    $orderBy = array(
                        'date_paid_platform desc'
                    );
                    break;
                case '3':
                    $orderBy = array(
                        'date_release desc'
                    );
                    break;
                case '4':
                    $orderBy = array(
                        'date_warehouse_shipping desc'
                    );
                    break;
                case '5':
                    $orderBy = array(
                        'date_last_modify desc'
                    );
                    break;
                case '6':
                    $orderBy = array(
                        'date_release desc'
                    );
                    break;
                case '7':
                    $orderBy = array(
                        'date_last_modify desc'
                    );
                    break;
                case '0':
                    $orderBy = array(
                        'date_create_platform desc'
                    );
                    break;
                case '1':
                    $orderBy = array(
                        'date_create_platform desc'
                    );
                    break;
                default:
                    $orderBy = array(
                        'date_paid_platform desc'
                    );
            }
            $count = Service_Orders::getByCondition($condition, 'count(*)');
            $return['total'] = $count;
//             print_r($condition);exit;
            if($count){
                $rows = Service_Orders::getByCondition($condition, "*", $pageSize, $page, $orderBy);
                $sellerItemCache = array();
                $skuCache = array();
                $warehouseCache = array();                
                foreach($rows as $k => $v){                    
                    $v['date_release'] = strtotime($v['date_release']) < strtotime('2000-01-01') ? '' : $v['date_release'];
                    $v['date_paid_platform'] = strtotime($v['date_paid_platform']) < strtotime('2000-01-01') ? '' : $v['date_paid_platform'];
                    $v['date_warehouse_shipping'] = strtotime($v['date_warehouse_shipping']) < strtotime('2000-01-01') ? '' : $v['date_warehouse_shipping'];
                    
                    $v['date_release'] = (! empty($v['date_release'])) ? substr($v['date_release'], 0, 16) : $v['date_release'];
                    $v['date_paid_platform'] = (! empty($v['date_paid_platform'])) ? substr($v['date_paid_platform'], 0, 16) : $v['date_paid_platform'];
                    
                    $v['date_warehouse_shipping'] = (! empty($v['date_warehouse_shipping'])) ? substr($v['date_warehouse_shipping'], 0, 16) : $v['date_warehouse_shipping'];
                  
                    $v['date_last_modify'] = (! empty($v['date_last_modify'])) ? substr($v['date_last_modify'], 0, 16) : $v['date_last_modify'];
                    
                    $v['date_create'] = (! empty($v['date_create'])) ? substr($v['date_create'], 0, 16) : $v['date_create'];
                    
                    $v['date_create_platform'] = (! empty($v['date_create_platform'])) ? substr($v['date_create_platform'], 0, 16) : $v['date_create_platform'];
                    
                    $rows[$k] = $v;
                    $data[$v['order_id']] = $v;
                }
//                 print_r($data);exit;
                $return['data'] = $data;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        
        // 目的国 家
        $countryArr = Common_DataCache::getCountry();
        
        $this->view->countryArr = $countryArr;
        // 站点
        $con = array(
            'platform' => $platform
        );
        $sites = Service_Site::getByCondition($con);
        $this->view->sites = $sites;
        
        $statusArr = Platform_OrderStatus::getStatusArr();
        
        $this->view->statusArrJson = Zend_Json::encode($statusArr);
        $this->view->statusArr = $statusArr;
        
        $platform = 'b2c';
        $this->view->user_account_arr = $this->user_account_arr;
        
        $this->view->load_platform_order_day = Platform_OrderProcess::load_platform_order_day();
        // 新订单界面(暂时用于amazon)
        $this->view->tplfile = $this->tplDirectory . "order_list_list_" . $platform . ".tpl";
        echo Ec::renderTpl($this->tplDirectory . "order_list_list.tpl", 'layout');
    }
	
    /**
     * 订单明细
     */
    public function getListDetailListAction(){
        $results = array();
    
        $orderIdArr = $this->getRequest()->getParam('order_id_arr', array());
        $db = Common_Common::getAdapter();
    
        $sql = 'select a.order_id,a.platform,a.warehouse_id,a.user_account,a.order_status,b.*,a.user_account,a.refrence_no_platform from orders a inner join shipping_address b on a.refrence_no_platform=b.OrderID where a.order_id in ('.implode(',', $orderIdArr).')';
        $data = $db->fetchAll($sql);
//                 echo $sql;exit;
   
        $skuCache = array();
        foreach($data as $v){
            $result = array('ask'=>0,'message'=>'');
            try{
                $con = array(
                        'order_id' => $v['order_id'],
                        'give_up_arr' => array(
                                '0',
                                '1'
                        )
                );
                $orderProducts = Service_OrderProduct::getByCondition($con);
                $orderProductArr = array();
                $orderProducts = Process_OrderProcess::getProductCombineRelationList($v, $orderProducts);
//                 print_r($orderProducts);exit;
                $v['order_product'] = $orderProducts;
    
                $result['ask'] = 1;
                $result['data'] = $v;
            }catch(Exception $e){
                $result['message'] = $e->getMessage();
            }
            $results[] = $result;
        }
//                 print_r($results);exit;
        die(Zend_Json::encode($results));
    }
    /**
     * 各个状态订单数量统计
     */
    public function getStatisticsAction(){
        $platform = $this->getRequest()->getParam('platform', '');
        
        $db = Common_Common::getAdapter();

        $table = Common_ApiProcess::getOrderTongjiTable();
        $select = $db->select();
        $select->from('orders', 'order_status,count(*) as count');
        $select->where('company_code = ?', Common_Company::getCompanyCode());
        
        
        $select->group('order_status');
//         echo $select;exit;
        $tongji = $db->fetchAll($select);
        //
        $reTongji->reTongji = 0;
        die(Zend_Json::encode($tongji));
    }

    /**
     * 各平台统计
     */
    public function getStatisticsPlatformAction(){
        $platform = $this->getRequest()->getParam('platform', '');
    
        $db = Common_Common::getAdapter();
    
        $table = Common_ApiProcess::getOrderTongjiTable();
        $select = $db->select();
        $select->from('orders', 'platform,count(*) as count');
        $select->where('company_code = ?', Common_Company::getCompanyCode());    
    
        $select->group('platform');
//                 echo $select;exit;
        $tongji = $db->fetchAll($select);
        //
        $reTongji->reTongji = 0;
        die(Zend_Json::encode($tongji));
    }
    
    /**
     * 查询可合并及客户留言订单数量等等
     */
    public function getOrderAttachedPropertyAction(){
    	
    	$return = array(
    			'state'=>0,
    			'message'=>'Fail.'
    			);
    	
    	//订单状态--根据不同的状态，返回不同的订单附属属性
    	$status = $this->_request->getParam('status','');
    	//平台
    	$platform = $this->_request->getParam('platform','ebay');
    	
    	
    	//客户可查看的店铺
    	$user_account_arr = $this->user_account_arr;
    	$user_account_condition="";
    	foreach($user_account_arr as $v){
    		$user_account_condition=$user_account_condition."'".$v."',";
    	}
    	$user_account_condition=$user_account_condition."'"."'";
    	
    	switch($status){
    		case '2':	//待发货审核
    			$db = Common_Common::getAdapter();    			
    			$sql_merge = "select SUM(tt.num) as num from (select count(t.buyer_id) as num,t.buyer_id from orders t where t.order_status= 2 and t.platform = '".$platform."' and t.user_account in (".$user_account_condition.") group by t.user_account,t.buyer_id,t.buyer_name HAVING COUNT(*)>1) tt";
//     			echo $sql_merge;exit;
//     			$sql_merge = "select count(*) as num from orders t1 where t1.platform = '".$platform."' and t1.order_status = 2 and user_account in (".$user_account_condition.") and t1.has_buyer_note = 1;";
    			$result_order_merge = $db->fetchAll($sql_merge);
    			
    			$sql_order_desc = "select count(*) as num from orders t1 where t1.platform = '".$platform."' and t1.order_status = 2 and user_account in (".$user_account_condition.") and t1.has_buyer_note = 1;";
    			
    			$result_order_desc = $db->fetchAll($sql_order_desc);
    			$return['state'] = 1;
    			//'可合并订单 10 个; 客户留言订单 10 个;'
    			$return['message'] = Ec::Lang('order_status_attached_property_tips_02','auto',array((!empty($result_order_merge[0]['num'])?$result_order_merge[0]['num'] :0),$result_order_desc[0]['num']));
    			break;
    		case '3':	//待发货
    			break;
    		case '4':	//已发货
    			break;
    		case '5':	//冻结中
    			break;
    		case '6':	//缺货
    			break;
    		case '7':	//问题件
    			break;
    		case '0':	//已废弃
    			$return['state'] = 1;
    			$return['message'] = Ec::Lang('order_status_attached_property_tips_00','auto');
    			break;
    		case '1':	//未付款
    			break;
    		default:	//近六个月的订单
    			
    			break;
    	}
    	
    	die(Zend_Json::encode($return));
    }
    
  
}