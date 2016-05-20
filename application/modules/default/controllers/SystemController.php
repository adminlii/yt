<?php
class Default_SystemController extends Ec_Controller_DefaultAction
{ 

    public function preDispatch()
    {
        $this->tplDirectory = "default/views/default/";
        $this->_userAuth = new Zend_Session_Namespace('userAuthorization');
        
        if(! $this->_userAuth->isLogin){
            $this->_redirect('/default/index/login');
            exit();
        }
    }
    
    public function header1Action(){
        $userId = isset($this->_userAuth->userId) ? $this->_userAuth->userId : '-1';
        $user = Service_User::getLoginUser();
        $this->view->user = $user;
        $this->view->logout = '/default/index/logout';
        $this->view->userId = $userId;
        /*
         * echo $this->view->render($this->tplDirectory . 'header.tpl');
         */
        echo $this->view->render($this->tplDirectory . 'header1.tpl');
    }
    /*
     * 布局头部
     */
    public function headerAction()
    {
        $userId = isset($this->_userAuth->userId) ? $this->_userAuth->userId : '-1';
        $user = Service_User::getLoginUser();
        $result = array(
            'state' => 0,
            'data' => array()
        );
        
        $userMenu = isset($this->_userAuth->Acl['menuArr']) ? $this->_userAuth->Acl['menuArr'] : array();
        if($this->_request->isPost()){
            if(count($userMenu)){
                $result = array(
                    'state' => 1,
                    'data' => $userMenu
                );
            }
            die(Zend_Json::encode($result));
        }
        // if(empty($userMenu)){
        $acl = new Ec_Controller_Plugins_Acl();
        $acl->getUserAuthInfo();
        $userMenu = $acl->getMenuArr();
        //废除的一级菜单
        $_menu_unset= array('平台订单','查询工具','费用管理','问题件管理','提货管理');
        $_menu2_unset = array('自定义国家映射','自定义模板','到货清单','运费试算','API设置');
        foreach ($userMenu as $k=>$v){
            if(in_array($v['parent']['value'],$_menu_unset)){
                unset($userMenu[$k]);
            }else{
                foreach ($v['item'][0]['item'] as $kk=>$vv){
                    if(in_array($vv['value'],$_menu2_unset)){
                        unset($userMenu[$k]['item'][0]['item'][$kk]);
                    }
                }
            }
        }
        $this->_userAuth->Acl['menuArr'] = $userMenu;
        // }
        
        $this->view->lang = Ec::getLang();
//         print_r($userMenu);exit;
        unset($userMenu['2-5']['item'][1]['item']['12']); // RUSTON20140822
        $this->view->menu = $userMenu;
        $this->view->userId = $userId;
        $this->view->user = $user;
        $this->view->logout = '/default/index/logout';
        
        /*
         * echo $this->view->render($this->tplDirectory . 'header.tpl');
         */
        echo $this->view->render($this->tplDirectory . 'header_new.tpl');
    }
    
    /*
     * 后台左导航
     */
    public function leftMenuAction()
    {
        $result = array(
            'state' => 0,
            'data' => array()
        );
        $userMenu = isset($this->_userAuth->Acl['menuArr']) ? $this->_userAuth->Acl['menuArr'] : array();
        if($this->_request->isPost()){
            if(count($userMenu)){
                $result = array(
                    'state' => 1,
                    'data' => $userMenu
                );
            }
            die(Zend_Json::encode($result));
        }
        $this->view->menu = $userMenu;
        echo $this->view->render($this->tplDirectory . 'left_menu.tpl');
    }

    public function homeAction()
    {
    	//公告,弹出显示
    	$bulletin = array('state'=>0,'total'=>0,'data'=>array());
    	$bulletinRows = Service_BulletinBoard::getByCondition(array("v_pop_up_display"=>1, "published_end"=>date("Y-m-d H:i:s")),array('bb_id', 'v_title', 'v_content', 'v_published'),0,1,array('v_published desc'));
    	
    	if (!empty($bulletinRows)) {
    	 
    		foreach ($bulletinRows as $key=>$val) {
    			//换行、空格
    			$val['v_content'] = str_replace("\r\n", "<br/>", $val['v_content']);
    			$val['v_content'] = str_replace(" ", "&nbsp;", $val['v_content']);
    			$bulletinRows[$key]['v_content'] = $val['v_content'];
    		}
    		 
    		$bulletin['state'] = 1;
    		$bulletin['total'] = Service_BulletinBoard::getByCondition(array("v_system_unequals"=>"1", "v_pop_up_display"=>1, "published_end"=>date("Y-m-d H:i:s")), 'count(*)');
    		$bulletin['data'] = $bulletinRows;
    		
    	}
    	$this->view->bulletin = Zend_Json::encode($bulletin);
    	
        $db = Common_Common::getAdapter();
        // TODO DB2 获取TMS的数据库连接  
        $db2 = Common_Common::getAdapterForDb2();
        $user = Service_User::getLoginUser();
        $this->view->user = $user;
        // ===============================昨日订单统计 start===============================
        // 创建订单数create_date
        $sql = "select count(*) from csd_order where customer_id='".Service_User::getCustomerId()."' and create_date>='".date('Y-m-d',strtotime('-1day'))."' and create_date<='".date('Y-m-d')."';";
        $createCount = $db->fetchOne($sql);
        $this->view->create_count = $createCount;
        // 仓库收货数checkin_date
        $sql = "select count(*) from csd_order where customer_id='".Service_User::getCustomerId()."' and checkin_date>='".date('Y-m-d',strtotime('-1day'))."' and checkin_date<='".date('Y-m-d')."';";
        $checkinCount = $db->fetchOne($sql);
        $this->view->checkin_count = $checkinCount;
        // 仓库出货数checkout_date
        $sql = "select count(*) from csd_order where customer_id='".Service_User::getCustomerId()."' and  checkout_date>='".date('Y-m-d',strtotime('-1day'))."' and checkout_date<='".date('Y-m-d')."';";
        $checkoutCount = $db->fetchOne($sql);
        $this->view->checkout_count = $checkoutCount;
        // ===============================昨日订单统计 end===============================
        // ===============================今日订单统计 start===============================
        // 创建订单数create_date
        $sql = "select count(*) from csd_order where customer_id='".Service_User::getCustomerId()."' and create_date>='".date('Y-m-d')."';";
        $createCount = $db->fetchOne($sql);
        $this->view->create_count_today = $createCount;
        // 仓库收货数checkin_date
        $sql = "select count(*) from csd_order where customer_id='".Service_User::getCustomerId()."' and checkin_date>='".date('Y-m-d')."';";
        $checkinCount = $db->fetchOne($sql);
        $this->view->checkin_count_today = $checkinCount;
        // 仓库出货数checkout_date
        $sql = "select count(*) from csd_order where customer_id='".Service_User::getCustomerId()."' and checkout_date>='".date('Y-m-d')."';";
        $checkoutCount = $db->fetchOne($sql);
        $this->view->checkout_count_today = $checkoutCount;
        // ===============================今日订单统计 end===============================
        // ===============================联系人信息 start===============================
        $supporter = Service_CsiShippersupporter::getByField($user['customer_id'], 'customer_id');
        if($supporter){
        	
            // 客服
            $sql = "
            SELECT * from csi_shippersupporter a 
            INNER JOIN hmr_staff b on 
            a.express_servicerid=b.st_id 
            INNER JOIN hmr_staffattach c 
            on b.st_id=c.st_id 
            where 
            a.customer_id={$user['customer_id']};";
            $shippersupporter = $db2->fetchRow($sql);
            $this->view->shippersupporter = $shippersupporter;
            // 业务员
            $sql = "
            SELECT * from csi_shippersupporter a 
            INNER JOIN hmr_staff b on 
            a.express_sallerid=b.st_id 
            INNER JOIN hmr_staffattach c 
            on b.st_id=c.st_id 
            where 
            a.customer_id={$user['customer_id']};";
            $saller = $db2->fetchRow($sql);
            $this->view->saller = $saller;
            // 结算员
            $sql = "
            SELECT * from csi_shippersupporter a 
            INNER JOIN hmr_staff b on 
            a.express_dunnerid=b.st_id 
            INNER JOIN hmr_staffattach c 
            on b.st_id=c.st_id 
            where 
            a.customer_id={$user['customer_id']};";
            $dunner = $db2->fetchRow($sql);
            $this->view->dunner = $dunner;
        }else{}
			
		// ===============================联系人信息
		// end===============================
			
		// print_r($user);
		// exit();
		$customer_id = Service_User::getCustomerId ();
		// $sql = "select customer_id , fund_mode , csp_amount a from
		// stm_customersurplus where customer_id ='{$customer_id}';";
		$sql = "
        select a.customer_id,a.sm_code , a.sc_time_balances from seq_surplusorder a ,
        (select sm_code sm_code, max(sc_surplusorderid) sc_surplusorderid  from  seq_surplusorder sur WHERE sur.customer_id = '{$customer_id}'  group by sm_code) b
        where
        a.sc_surplusorderid = b.sc_surplusorderid and a.customer_id='{$customer_id}'
        ";
		$row1 = $db2->fetchAll( $sql );
		// echo $sql;exit;
		$sql = "select customer_id , sum(cca_total) -sum(cca_reclaim) c, sum(cca_remain)-sum(cca_reclaim) d from cdt_customer_credit_assign where cca_enddate <=NOW() and cca_begindate >= NOW() and  customer_id ='{$customer_id}' group by customer_id ;";
		
		$row2 = $db2->fetchRow( $sql );
		$tmp = array ();
		foreach ( $row1 as $v ) {
			$tmp [$v ['sm_code']] = $v;
		}
		// print_r($tmp);exit;
		$zonglan = array (
				'a' => $tmp ['EE'] ? $tmp ['EE'] ['sc_time_balances'] : 0,
				'b' => $tmp ['DE'] ? $tmp ['DE'] ['sc_time_balances'] : 0,
				'c' => $row2 ? $row2 ['c'] : 0,
				'd' => $row2 ? $row2 ['d'] : 0 
		);
		$this->view->zonglan = $zonglan;
		$this->view->customer_code = Service_User::getCustomerCode();
		
		//客户等级
		$show=Service_Config::getByField('SHOW_CUSTOMER_LEVEL','config_attribute');
		$toShow=$show['config_value'];
		if (strtoupper($toShow)=='Y'){
			$db2=Common_Common::getAdapterForDb2();
			$sql="SELECT * FROM csi_customer WHERE customer_id='{$customer_id}';";
			$level=$db2->fetchRow($sql);
			if ($level){
				$level=substr($level['customerlevel_code'], 1);
				for ($i=1;$i<=$level;$i++){
					$level_sign .= '★';
				}
			}
		}
	    $this->view->level=$level_sign; 
		

		$tms_id = Service_User::getTmsId();
		$sql = "select * from web_newsconfig where tms_id='{$tms_id}' and news_type='INDEX';";
		$row = Common_Common::fetchRow($sql);
		$this->view->content = '';
		if($row){
			$this->view->content = $row['news_note'];
		}
		
        echo Ec::renderTpl($this->tplDirectory . "home.tpl", 'layout');
    }

    public function rightGuildAction()
    {
        echo $this->view->render($this->tplDirectory . "right_guild.tpl");
    }

    /**
     * @查看产品图片
     * WMS 全站使用
     */
    public function viewProductImgAction()
    {
        $product_id = $this->getParam('product_id', '');
        $attached = Service_ProductAttached::getByField($product_id, 'product_id');
        if($attached){
            if($attached['pa_file_type'] == 'img'){
                $path = Ec_Upload::getBasePath() . trim($attached['pa_path'], '/');
                header("Content-type: image/jpg");
                echo file_get_contents($path);
            }elseif($attached['pa_file_type'] == 'link'){
                $pic = $attached['pa_path'];
                header("Location: " . $pic);
            }
        }else{
            $pic = '/images/base/noimg.jpg';
            header("Location: " . $pic);
        }
    }

    /**
     * 查看图片
     */
    public function viewImgAction()
    {
        $pa_id = $this->getParam('pa_id', '');
        $attached = Service_ProductAttached::getByField($pa_id, 'id');
        if($attached){
            if($attached['pa_file_type'] == 'img'){
                $path = Ec_Upload::getBasePath() . trim($attached['pa_path'], '/');
                header("Content-type: image/jpg");
                echo file_get_contents($path);
            }elseif($attached['pa_file_type'] == 'link'){
                $pic = $attached['pa_path'];
                header("Location: " . $pic);
            }
        }else{
            $pic = '/images/base/noimg.jpg';
            header("Location: " . $pic);
        }
    }

    /**
     * @查看产品图片
     * WMS 全站使用
     */
    public function viewItemImgAction()
    {
        $item_id = $this->getRequest()->getParam('item_id', '0');
        $sellerItem = Service_SellerItem::getByField($item_id, 'item_id');
        if($sellerItem && $sellerItem['pic_path']){
            $picArr = explode('#:|:#', $sellerItem['pic_path']);
            header("Location: " . $picArr[0]);
            exit();
        }
        
        header("Location: /images/base/noimg.jpg");
        exit();
    }

    /**
     * @查看产品详情
     * WMS 全站使用
     */
    public function itemJumpAction()
    {
        $item_id = $this->getRequest()->getParam('item_id', '0');
        $sellerItem = Service_SellerItem::getByField($item_id, 'item_id');
        if($sellerItem){
            header("Location: " . $sellerItem['item_url']);
            exit();
        }
    }

    public function getSearchFilterAction()
    {
        $result = array(
            'state' => 0,
            'message' => 'Fail',
            'data' => array()
        );
        $actionId = $this->_request->getParam('quick', '-1');
        
        $actionId = empty($actionId) ? '-1' : $actionId;
        if(! empty($actionId)){
            $condition = array(
                "filter_action_id" => $actionId
            );
            $rows = Service_SearchFilter::getByCondition($condition, '*', 50, 1, array(
                'parent_id asc',
                'search_sort asc'
            ));
            if(! empty($rows)){
                $sType = $sFilter = array();
                foreach($rows as $key => $value){
                    if($value['parent_id'] == '0'){
                        $sType[$value['search_sort'] . '_' . $value['sf_id']] = $value;
                        $sFilter[$value['sf_id']] = $value;
                    }elseif(isset($sFilter[$value['parent_id']])){
                        $sType[$sFilter[$value['parent_id']]['search_sort'] . '_' . $value['parent_id']]['item'][] = $value;
                    }
                }
                ksort($sType);
                $result = array(
                    'state' => 1,
                    'message' => '',
                    'data' => $sType
                );
            }
        }
        die(Zend_Json::encode($result));
    }

    /**
     * 获取自定义导航
     */
    public function getSkyeQuiKeyAction()
    {
        $result = array(
            'state' => 0,
            'message' => 'Fail',
            'data' => array(),
            'userId' => 0
        );
        $data = Service_UserRightHeaderMap::getSkyeQuiKey();
        if(! empty($data)){
            $userId = isset($this->_userAuth->userId) ? $this->_userAuth->userId : '0';
            $result = array(
                'state' => 1,
                'userId' => $userId,
                'data' => $data
            );
        }
        die(Zend_Json::encode($result));
    }

    /**
     * 获取用户权限
     * 设置快捷导航
     */
    public function getSkyeQuiKeyListAction()
    {
        $result = array(
            'state' => 0,
            'message' => '',
            'data' => array()
        );
        $userId = isset($this->_userAuth->userId) ? $this->_userAuth->userId : '0';
        $data = Service_User::getUserRightCustomByUserId($userId);
        if(! empty($data)){
            $result = array(
                'state' => 1,
                'data' => $data
            );
        }
        die(Zend_Json::encode($result));
    }

    public function updateSkyeQuiKeyAction()
    {
        $result = array(
            'state' => 0,
            'message' => '操作失败.',
            'data' => array()
        );
        if($this->_request->isPost()){
            $actionIdArr = $this->_request->getParam('actionId', array());
            $result = Service_UserRightHeaderMap::updateSkyeQuiKey($actionIdArr);
            if($result){
                $result = array(
                    'state' => 1
                );
            }
        }
        die(Zend_Json::encode($result));
    }

    /**
     * 返回运输方式代码
     * @use 打印机设置
     */
    public function setupPrinterAction()
    {
        $result = array(
            'state' => 1,
            'data' => array(
                'A4'
            )
        );
        if($this->_request->isPost()){
            $smArr = Service_ShippingMethod::getShippingMethodSimple();
            if(! empty($smArr)){
                $smArr = array_merge(array(
                    'A4',
                    '70x30',
                    '100x30'
                ), $smArr);
                $result = array(
                    'state' => 1,
                    'data' => $smArr
                );
            }
        }
        die(Zend_Json::encode($result));
    }

    /**
     * @系统数据看板
     */
    public function getBoardAction()
    {
        $result = array(
            'state' => 0,
            'data' => array()
        );
        $type = trim($this->_request->getParam('type', ''));
        $model = trim($this->_request->getParam('model', ''));
        die(Zend_Json::encode($result));
    }

    /**
     * 获得店铺销售数据
     */
    public function getShopSales()
    {
        /*
         * 1. 查询店铺账户
         */
        $user_account_arr_result = Service_User::getPlatformUserNew('do');
        $user_acccount_arr = array();
        foreach($user_account_arr_result as $key => $value){
            $user_acccount_arr[] = array(
                'user_account' => $value['user_account'],
                'platform_user_name' => $value['platform_user_name']
            );
        }
        $user_account_con = "";
        for($i = 0;$i < count($user_acccount_arr);$i ++){
            $user_account_con .= (count($user_acccount_arr) == ($i + 1)) ? "'" . $user_acccount_arr[$i]['user_account'] . "'" : "'" . $user_acccount_arr[$i]['user_account'] . "',";
        }
        
        // 公司代码
        $company_code = Common_Company::getCompanyCode();
        // 统计sql
        $sql_all = "SELECT count(t.subtotal) as num, SUM(t.subtotal) as subtotal, t.currency as currency FROM orders t WHERE t.company_code = '$company_code' and t.order_status in (3,4)  and t.user_account in ($user_account_con) GROUP BY t.currency;";
        
        /*
         * 2. 累计销售数据
         */
        $subtotal_all = 0.00;
        $num_all = 0;
        $table = new DbTable_Orders();
        $db = $table->getAdapter();
        if(! empty($user_account_con)){
            $data_all = $db->fetchAll($sql_all);
            
            if(! empty($data_all)){
                foreach($data_all as $key1 => $value1){
                    $subtotal1 = $value1['subtotal'];
                    $currency1 = $value1['currency'];
                    $subtotal_rmb1 = Common_CustomerFeeProcess::changeCurrency($subtotal1, $currency1, Common_CustomerFeeProcess::$currency_rmb);
                    $subtotal_all = bcadd(floatval($subtotal_rmb1['value']), floatval($subtotal_all), 2);
                    
                    $num1 = $value1['num'];
                    $num_all = $num_all + $num1;
                }
            }
        }
        
        /*
         * 3. 最近7天，销售数据
         */
        $subtotal_seven_days = 0.00;
        $num_seven_days = 0;
        $days = - 7;
        $format = "Y-m-d H:i:s";
        $date_seven_days = date($format, strtotime("$days days", strtotime(date($format))));
        $sql_seven_days = "SELECT count(t.subtotal) as num, SUM(t.subtotal) as subtotal, t.currency as currency FROM orders t WHERE t.company_code = '$company_code' and t.order_status in (3,4) AND t.date_release > '$date_seven_days'  and t.user_account in ($user_account_con) GROUP BY t.currency;";
        
        if(! empty($user_account_con)){
            $data_seven_days = $db->fetchAll($sql_seven_days);
            if(! empty($data_seven_days)){
                foreach($data_seven_days as $key2 => $value2){
                    $subtotal2 = $value2['subtotal'];
                    $currency2 = $value2['currency'];
                    $subtotal_rmb2 = Common_CustomerFeeProcess::changeCurrency($subtotal2, $currency2, Common_CustomerFeeProcess::$currency_rmb);
                    $subtotal_seven_days = bcadd(floatval($subtotal_rmb2['value']), floatval($subtotal_seven_days), 2);
                    
                    $num2 = $value2['num'];
                    $num_seven_days = $num_seven_days + $num2;
                }
            }
        }
        
        $return = array(
            array(
                'num' => $num_all,
                'subtotal' => $subtotal_all,
                'title' => '累计销售订单{0}单，销售额：{1} RMB'
            ),
            array(
                'num' => $num_seven_days,
                'subtotal' => $subtotal_seven_days,
                'title' => '近七天达成{0}笔交易，共计：{1} RMB'
            )
        );
        // print_r($return);
        // die(Zend_Json::encode($return));
        return $return;
    }

    /**
     * 获得店铺绑定信息
     */
    public function getAccountBinding()
    {
        /*
         * 1.查看ebay账户绑定情况
         */
        $ebayAccountBinding = 0;
        $companyCode = Common_Company::getCompanyCode();
        $con_platformUser = array(
            'company_code' => $companyCode,
            'platform' => 'ebay'
        );
        $result_platformUser = Service_PlatformUser::getByCondition($con_platformUser);
        if(! empty($result_platformUser)){
            $ebayAccountBinding = 1;
        }
        
        /*
         * 2.查看paypal账户的绑定情况
         */
        // 若ebay账户未绑定，那么paypal账户也肯定没有绑定
        $paypalAccountBinding = 0;
        if($ebayAccountBinding){
            $com_ebayPaypal = array(
                'company_code' => $companyCode
            );
            $result_ebayPaypal = Service_EbayPaypal::getByCondition($com_ebayPaypal);
            /*
             * 2.1 判断ebay店铺账户是否都已经已经关联paypal账户
             */
            foreach($result_platformUser as $key1 => $value1){
                $platformUser_ebay = $value1['user_account'];
                foreach($result_ebayPaypal as $key2 => $value2){
                    if($platformUser_ebay == $value2['ebay_account']){
                        // 移除ebay平台账户
                        unset($result_platformUser[$key1]);
                    }
                }
            }
            // 重新排序,若平台账户数据还有存在，那证明ebay账户还没有绑定paypal账户
            ksort($result_platformUser);
            if(count($result_platformUser) == 0){
                $paypalAccountBinding = 1;
            }
        }
        
        /*
         * 3. 检查店铺绑定情况
         */
        $userAccountBingding = 0;
        $con_user = array(
            'company_code' => $companyCode
        );
        $result_user = Service_User::getByCondition($con_user);
        $user_arr = array();
        foreach($result_user as $key => $value){
            $user_arr[] = $value['user_id'];
        }
        
        $user_id_con = "";
        for($i = 0;$i < count($user_arr);$i ++){
            $user_id_con .= (count($user_arr) == ($i + 1)) ? "" . $user_arr[$i] . "" : "" . $user_arr[$i] . ",";
        }
        $sql = "select DISTINCT(fss.user_id) from filter_set fss where fss.user_id in ($user_id_con);";
        $table = new DbTable_FilterSet();
        $db = $table->getAdapter();
        $data = $db->fetchAll($sql);
        if(! empty($data)){
            foreach($data as $key3 => $value3){
                if(in_array($value3['user_id'], $user_arr)){
                    $user_arr = array_flip($user_arr);
                    unset($user_arr[$value3['user_id']]);
                    $user_arr = array_flip($user_arr);
                }
            }
            ksort($user_arr);
            if(count($user_arr) == 0){
                $userAccountBingding = 1;
            }
        }
        
        /*
         * 4. 检查是否设置了分仓规则
         */
        $orderAllotSetBingding = 0;
        $con_orderAllotSet = array(
            'company_code' => $companyCode
        );
        $result_orderAllotSet = Service_OrderAllotSet::getByCondition($con_orderAllotSet);
        if(! empty($result_orderAllotSet)){
            $orderAllotSetBingding = 1;
        }
        
        $return = array(
            array(
                'ur_id' => '11',
                'title' => 'eBya账户',
                'title_1' => '已授权',
                'title_0' => '未授权',
                'bol' => $ebayAccountBinding
            ),
            array(
                'ur_id' => '28',
                'title' => 'Paypal账户',
                'title_1' => '已关联',
                'title_0' => '未全部关联',
                'bol' => $paypalAccountBinding
            ),
            array(
                'ur_id' => '30',
                'title' => '分仓规则',
                'title_1' => '已设置',
                'title_0' => '未设置',
                'bol' => $orderAllotSetBingding
            ),
            array(
                'ur_id' => '22',
                'title' => '店铺账户',
                'title_1' => '已分配',
                'title_0' => '未全部分配',
                'bol' => $userAccountBingding
            )
        );
        // print_r($return);
        return $return;
    }

    /**
     * 获得控制台数据
     */
    public function getControlDataAction()
    {
        $shopSales = $this->getShopSales();
        $accountBinding = $this->getAccountBinding();
        $user = Service_User::getLoginUser();
        
        $return = array(
            'ask' => 1,
            'is_admin' => $user['is_admin'],
            'data' => array(
                'sales' => $shopSales,
                'binding' => $accountBinding
            )
        );
        // print_r($return);
        die(Zend_Json::encode($return));
    }

    /**
     * 获得待处理订单数据
     */
    public function getOrderProcess($userAccount_arr)
    {
        /*
         * 1.定义查询条件
         */
        $condition_arr = array(
            array(
                'status' => array(
                    2
                ),
                'title' => '待审核订单：',
                'condition' => array(
                    'has_buyer_note' => 1,
                    'has_operator_note' => 1,
                    'has_warehouse' => '0'
                )
            ),
            array(
                'status' => array(
                    3
                ),
                'title' => '待发货订单：',
                'condition' => array(
                    'has_buyer_note' => 1,
                    'has_operator_note' => 1,
                    'has_warehouse' => '0'
                )
            ),
            array(
                'status' => array(
                    5,
                    6
                ),
                'title' => '冻结与缺货订单：',
                'condition' => array(
                    'has_buyer_note' => 1,
                    'has_operator_note' => 1,
                    'has_warehouse' => '0'
                )
            )
        );
        /*
         * 2.条件说明
         */
        $condition_arr_title = array(
            'has_buyer_note' => '客户留言订单：',
            'has_operator_note' => '客服备注订单：',
            'has_warehouse' => '未分仓订单：'
        );
        $condition_arr_ur_id = array(
            'has_buyer_note' => '16',
            'has_operator_note' => '16',
            'has_warehouse' => '16'
        );
        /*
         * 3. 循环查询
         */
        $return = array();
        if(! empty($userAccount_arr)){
            $companyCode = Common_Company::getCompanyCode();
            foreach($condition_arr as $conKey => $conValue){
                $con_order_base = array(
                    'company_code' => $companyCode,
                    'user_account_arr' => $userAccount_arr
                );
                $con_order = $con_order_base;
                $con_order['order_status_arr'] = $conValue['status'];
                $count_total = Service_Orders::getByCondition($con_order, 'count(*)');
                $result = array(
                    'total' => $count_total,
                    'title' => $conValue['title'],
                    'filter' => array()
                );
                foreach($conValue['condition'] as $key1 => $value1){
                    $con_order[$key1] = $value1;
                    $count_filter = Service_Orders::getByCondition($con_order, 'count(*)');
                    $result['filter'][] = array(
                        'title' => $condition_arr_title[$key1],
                        'ur_id' => $condition_arr_ur_id[$key1],
                        'total' => $count_filter
                    );
                    unset($con_order[$key1]);
                }
                $return[] = $result;
            }
        }else{
            foreach($condition_arr as $conKey => $conValue){
                $result = array(
                    'total' => 0,
                    'title' => $conValue['title'],
                    'filter' => array()
                );
                foreach($conValue['condition'] as $key1 => $value1){
                    $result['filter'][] = array(
                        'title' => $condition_arr_title[$key1],
                        'ur_id' => $condition_arr_ur_id[$key1],
                        'total' => 0
                    );
                }
                $return[] = $result;
            }
        }
        // print_r($return);
        return $return;
    }

    /**
     * 获得系统数据看板
     */
    public function getSystemBoardAction()
    {
        // $this->getShopSales();
        // $this->getAccountBinding();
        // 获得前台传入的参数
        $warehouseId = $this->_request->getParam("warehouse_id", "");
        $userAccount = $this->_request->getParam("user_account", "");
        $osmCodeArr = $this->_request->getParam("osm_code", array());
        $ospIdArr = $this->_request->getParam("osp_id", array());
        
        /*
         * 1.1. 查询模块所属的panelID
         */
        $panelIdArr = array();
        if(! empty($osmCodeArr)){
            $conPanel = array(
                'osm_code_arr' => $osmCodeArr
            );
            $resultPanel = Service_OsOperatingStatisticsPanel::getByCondition($conPanel);
            if(! empty($resultPanel)){
                foreach($resultPanel as $key1 => $value1){
                    $panelIdArr[] = $value1['panel_id'];
                }
            }
        }
        
        /*
         * 1.2. 剔除模块所属panelID与$ospIdArr之间的重复ID
         */
        if(! empty($ospIdArr)){
            if(! empty($panelIdArr) && count($panelIdArr) > 0){
                foreach($ospIdArr as $key2 => $value2){
                    if(! in_array($value2, $panelIdArr)){
                        $panelIdArr[] = $value2;
                    }
                }
            }else{
                $panelIdArr = $ospIdArr;
            }
        }
        
        /*
         * 2. 查询所有的面板节点数据
         */
        if($userAccount == 'all'){
            $user_account_arr_result = Service_User::getPlatformUserNew('do');
            foreach($user_account_arr_result as $key => $value){
                $userAccount_arr[] = $value['user_account'];
            }
        }else{
            $userAccount_arr = array(
                $userAccount
            );
        }
        
        $conNode = array(
            'os_warehouse_id' => $warehouseId,
            'os_user_account_arr' => $userAccount_arr,
            // 'os_user_account_arr'=>$userAccountArr,
            'os_panel_id_arr' => $panelIdArr,
            'company_code' => Common_Company::getCompanyCode()
        );
        $resultNode = Service_OsOperatingStatistics::getByCondition($conNode, '*', 0, 1, array(
            'os_operating_statistics.os_node',
            'os_operating_statistics.os_date_refresh asc'
        ));
        
        // 查询所有店铺数据，进行数据整合
        if($userAccount == 'all'){
            // print_r($resultNode);
            $resultNode = $this->makeNodeDataMerge($resultNode);
        }
        /*
         * 3.将节点数据分类（模块和非模块区分开来）
         */
        if(! empty($resultNode)){
            $dataNode = array();
            // 无模块代码的code
            $defaultModule = "unModuleCode";
            foreach($resultNode as $key3 => $value3){
                if(! empty($value3['osm_code'])){
                    $dataNode[$value3['osm_code']][] = $value3;
                }else{
                    $dataNode[$defaultModule][] = $value3;
                }
            }
            // print_r($dataNode);exit;
            
            /*
             * 4.将区分开后的节点数据合并为完整的面板数据
             */
            $returnData = array(
                'module' => array(),
                'unModule' => array()
            );
            foreach($dataNode as $key4 => $value4){
                if($key4 != $defaultModule){
                    $panelData = $this->getPanelData($value4);
                    $returnData['module'][] = $panelData;
                    // $returnData['module'][$key4] = $panelData;
                    // print_r($panelData);exit;
                }else{
                    // 处理非模块类型的（也就是页面上的展示的单个面板数据）
                    $panelData = $this->getPanelData($value4);
                    // //因为非模块类型，全部是单独面板，所以要拆分开
                    // foreach ($panelData as $key5 => $value5) {
                    // $returnData['unModule'][] = array($value5);
                    // }
                    $returnData['unModule'][] = $panelData;
                    // print_r($panelData);exit;
                }
            }
            // print_r($returnData);exit;
        }
        
        $return = array(
            'ask' => 0,
            'data' => '',
            'data_orderProcess' => $this->getOrderProcess($userAccount_arr)
        );
        // print_r($return['data_orderProcess']);
        if(! empty($returnData)){
            $return['ask'] = 1;
            $return['data'] = $returnData;
            // print_r($returnData);
        }
        
        // print_r($returnData);exit;
        die(Zend_Json::encode($return));
    }

    /**
     * 组织节点数据成为完整的panel
     * 
     * @param unknown_type $data            
     * @return multitype:multitype:unknown
     */
    private function getPanelData($data)
    {
        // 处理模块类型的节点数据
        $prevPanelId = ''; // 前一个面板ID
        $nodeIndex = array(); // 一个存放节点特定下标（'os_code' + '_' + 'panel_id'）的数字，用来当前节点是否和上一个节点属于同一个panel
        $panelData = array(); // 存放组织完成的面板数据，一个下标对应一个面板
        $incrementedMun = 0;
        $panelId = '';
        $pabelUrId = '';
        $panel_type = '';
        $pabelVal = '';
        $panelTitle = '';
        $panelName = '';
        foreach($data as $nodeKey => $nodeVal){
            // $panelId = $nodeVal['os_panel_id'];
            $tmpNodeIndexKey = $nodeVal['os_node'] . '_' . $nodeVal['os_panel_id'];
            if(! in_array($tmpNodeIndexKey, $nodeIndex)){
                if(empty($prevPanelId)){
                    $prevPanelId = $panelId;
                }else 
                    if($prevPanelId != $nodeVal['os_panel_id']){
                        $prevPanelId = $nodeVal['os_panel_id'];
                        // 前一个面板ID与当前面板ID不一致是，清空下标
                        unset($nodeIndex);
                        // ksort($nodeIndex);
                        // 同时将前一个面板的数据丢在$panelData中
                        $panelData[] = array(
                            'panelId' => $panelId,
                            'panel_type' => $panel_type,
                            'name' => $panelName,
                            'title' => $panelTitle,
                            'val' => $pabelVal,
                            'ur_id' => $pabelUrId
                        );
                        unset($nodeIndex);
                        // ksort($nodeIndex);
                        unset($panelTitle);
                        // ksort($panelTitle);
                        unset($pabelVal);
                        // ksort($pabelVal);
                        unset($pabelUrId);
                        // ksort($$pabelUrId);
                    }
                $panelName = $nodeVal['panel_name'];
                $panelTitle[] = array(
                    'text' => $nodeVal['os_name'],
                    'type' => $nodeVal['os_data_type']
                );
                $incrementedMun = 0;
            }else{
                $incrementedMun = $incrementedMun + 1;
            }
            $panelId = $nodeVal['os_panel_id'];
            $panel_type = $nodeVal['panel_type'];
            $nodeIndex[] = $tmpNodeIndexKey;
            $pabelVal[$incrementedMun][] = $nodeVal['os_node_amount'];
            $pabelUrId[$incrementedMun][] = $nodeVal['os_ur_id'];
        }
        // 最后一个面板循环完成之后，因为逻辑判断原因，是不会放入$panelData中的，所以，补放一次
        $panelData[] = array(
            'panelId' => $panelId,
            'panel_type' => $panel_type,
            'name' => $panelName,
            'title' => $panelTitle,
            'val' => $pabelVal,
            'ur_id' => $pabelUrId
        );
        // print_r($panelData);
        return $panelData;
    }

    /**
     * 将节点数据进行累加合并
     */
    private function makeNodeDataMerge($rows)
    {
        $rows_new = array();
        if(! empty($rows)){
            foreach($rows as $key1 => $value1){
                /*
                 * 1.设置一个唯一Key，用来累加数据值
                 */
                $tmp_key = $value1['os_application_code'] . '_' . $value1['os_node'] . '_' . $value1['os_date_refresh'];
                
                /*
                 * 2.判断新建数组是否存在该key值
                 */
                if(empty($rows_new[$tmp_key])){
                    // 不存在直接丢进去
                    $rows_new[$tmp_key] = $value1;
                }else{
                    // 存在，判断节点值的类型，进行累加赋值操作
                    $row_val = $rows_new[$tmp_key];
                    if($row_val['os_data_type'] == 'int'){
                        $row_val['os_node_amount'] = (int)$row_val['os_node_amount'] + (int)$value1['os_node_amount'];
                    }else 
                        if($row_val['os_data_type'] == 'date'){
                            // 统一刷新时间的日期是一样的，所以不用管
                        }else 
                            if($row_val['os_data_type'] == 'percentage'){
                                $num = ((int)$row_val['os_node_amount'] + (int)$value1['os_node_amount']) / 2;
                                $num = round($num, 2); //
                                $row_val['os_node_amount'] = $num . '%';
                            }
                    $rows_new[$tmp_key] = $row_val;
                }
            }
        }
        return $rows_new;
    }

    /**
     * 获得仓库数据
     */
    public function getWarehouseDataAction()
    {
        $return = array(
            'ask' => 1,
            'data' => Service_Warehouse::getAll()
        );
        
        die(Zend_Json::encode($return));
    }

    /**
     * 获得帮助页面所需参数
     */
    public function getHelpDataAction()
    {
        $return = array(
            'state' => 0,
            'main' => '',
            'parallel' => '',
            'content' => ''
        );
        /*
         * 1. 获得前台传入的参数
         */
        $uId = intval($this->_request->getParam("paramId", ""));
        if($uId != '' && $uId != 0){
            /*
             * 2. 查询系统帮助设置表，查找对应的步骤ID
             */
            $resultSysHelpSet = Service_SysHelpSet::getByField($uId, 'ur_id');
            if(! empty($resultSysHelpSet)){
                // 主线步骤ID
                $mainStepId = $resultSysHelpSet['sms_main_step_id'];
                // 并行步骤ID
                $parallelStepId = $resultSysHelpSet['sms_parallel_step_id'];
                /*
                 * 3. 查询步骤ID对应的步骤明细
                 */
                if(! empty($mainStepId)){
                    // $resultSysModuleStepMain = Service_SysModuleStep::getByField($mainStepId,'sms_id');
                    $conMain = array(
                        'sms_id' => $mainStepId
                    ); // $condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = ""
                    $resultStepContentMain = Service_SysModuleStepContent::getByCondition($conMain, "*", 0, 1, "sys_module_step_content.smsc_seq asc");
                }
                $isExistParallel = false;
                if(! empty($parallelStepId)){
                    $resultSysModuleStepParallel = Service_SysModuleStep::getByField($parallelStepId, 'sms_id');
                    $isExistParallel = true;
                    
                    $conParallel = array(
                        'sms_id' => $parallelStepId
                    );
                    $resultStepContentParallel = Service_SysModuleStepContent::getByCondition($conParallel, "*", 0, 1, "sys_module_step_content.smsc_seq asc");
                }
                
                /*
                 * 4. 封装明细数据给前台
                 */
                $rowMain = array();
                $rowContent = array();
                foreach($resultStepContentMain as $key1 => $value1){
                    $tmpRowMain = array();
                    $tmpRowMain['StepNum'] = $value1['smsc_step_num'];
                    $tmpRowMain['StepCode'] = $value1['smsc_step_code'];
                    $tmpRowMain['StepText'] = $value1['smsc_step_text'];
                    $rowContent[] = array(
                        'key' => $value1['smsc_step_code'],
                        'val' => $value1['smsc_step_content']
                    );
                    $rowMain[] = $tmpRowMain;
                }
                
                if($isExistParallel){
                    $rowParallel = array(
                        'StepNumStart' => $resultSysModuleStepParallel['smsc_start_num'],
                        'StepNumEnd' => $resultSysModuleStepParallel['smsc_end_num'],
                        'ParallelNodeDetail' => array()
                    );
                    foreach($resultStepContentParallel as $key2 => $value2){
                        $tmpRowParallel = array();
                        $tmpRowParallel['StepNum'] = $value2['smsc_step_num'];
                        $tmpRowParallel['StepCode'] = $value2['smsc_step_code'];
                        $tmpRowParallel['StepText'] = $value2['smsc_step_text'];
                        $rowContent[] = array(
                            'key' => $value2['smsc_step_code'],
                            'val' => $value2['smsc_step_content']
                        );
                        $rowParallel['ParallelNodeDetail'][] = $tmpRowParallel;
                    }
                }
                
                $return['state'] = 1;
                $return['content'] = $rowContent;
                $return['main'] = $rowMain;
                if($isExistParallel){
                    $return['parallel'] = $rowParallel;
                }
            }
        }
        // print_r($return);
        die(Zend_Json::encode($return));
    }

    /**
     * 进入打印机设置
     */
    public function printerAction()
    {
        /*
         * 1. 纸张类型的设置项
         */
        $returnPaper = array();
        $paperRows = array(
            'A4' => 'A4纸打印机',
            '70x30' => '产品条码',
            '100x30' => '产品条码',
            'receiving' => '送货单',
            'maitou' => '唛头'
        );
        if(! empty($paperRows)){
            foreach($paperRows as $k => $v){
                $returnPaper[$k] = $v;
            }
        }
        
        $this->view->paper = $returnPaper;
        
        $this->view->paperJson = Zend_Json::encode($returnPaper);
        echo Ec::renderTpl($this->tplDirectory . "printer_set.tpl", 'layout');
    }
    
    /**
     * 获得公告信息
     */
    public function getBulletinBoardAction(){
    	//     	{code:'',pageSize:"8"};
    	$return = array(
    			"state" => 0,
    			"message" => '',
    			"total" => 0,
    			"data" => array()
    	);
    
    	$code = $this->_request->getParam("code","");
    	$page = intval($this->_request->getParam("page",""));
    	$pageSize = intval($this->_request->getParam("pageSize",""));
    
    	$con = array(
//     			'code'=>$code,
    			'current_date'=>date('y-m-d H:i:s')
    	);
    	$page = $page ? $page : 1;
    	$pageSize = $pageSize ? $pageSize : 20;
    	$pageSize = $pageSize || !is_numeric($pageSize) > 20 ? 20 : $pageSize;
    	$total = Service_BulletinBoard::getByCondition($con,'count(*)');
    	if ($total) {
    		$result = Service_BulletinBoard::getByCondition($con, $type = '*', $pageSize, $page = 1, "v_published desc");
    		//处理正文中的特殊字符，替换成<br>
    		foreach ($result as $key => $value) {
    			$publish_time = $value['v_published'];
    			$time = date('H:i:s',strtotime($publish_time));
    			if($time == '00:00:00'){
    				$value['v_published'] = date('Y-m-d',strtotime($publish_time));
    			}
    			//$value['v_content'] = preg_replace('\t', '<br/>', $value['v_content']);
    			$qian=array(" "," ","\t","\n","\r");$hou=array("","","<br/>","<br/>","&nbsp;");
    			$value['v_content'] = str_replace($qian,$hou,$value['v_content']);
    			$result[$key] = $value;
    		}
//     		print_r($result);die;
    		$return['state'] = 1;
    		$return['total'] = $total;
    		$return['data'] = $result;
    	}
    
    	die(Zend_Json::encode($return));
    }
    
}