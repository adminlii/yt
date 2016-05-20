<?php
class Ebay_EbayServiceCommon
{

    /**
     * 批量下载订单
     */
    public static function cronLoadOrderBatch()
    {
        $db = Common_Common::getAdapter();
        $table = self::table_cron_load_ebay_order();        
        while(true){
            $db = Common_Common::getAdapter();
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            Common_ApiProcess::log("还剩{$count}条订单待处理=========================");
            $sql = "select * from {$table} order by company_code, user_account limit 100";
            $items = $db->fetchAll($sql); 
            if($items){
                try{
                    $group = array();
                    foreach($items as $item){
						$key = $item ['user_account'] . '_' . $item ['company_code'];
						
						$group [$key] ['user_account'] = $item ['user_account'];
						$group [$key] ['company_code'] = $item ['company_code'];
						
						$group [$key] ['order_sn_arr'] [] = $item ['order_sn'];
					}
                    //print_r($group);exit;
                    foreach($group as $sub){
                    	$user_account = $sub['user_account'];
                    	$company_code = $sub['company_code'];
                    	$order_sn_arr = $sub['order_sn_arr'];
                        $service = new Ebay_LoadEbayOrderService();
                        $service->setUserAccount($user_account);
                        $service->setCompanyCode($company_code);
                        $start = date('Y-m-d\TH:i:s.000\Z', strtotime('-120days'));
                        $end = date('Y-m-d\TH:i:s.000\Z');
                        $service->callEbay($start, $end, $order_sn_arr);
                    }
                    
                    foreach($items as $item){
                        $sql = "delete from {$table} where order_sn='{$item['order_sn']}';";
                        Common_ApiProcess::log($sql);
                        $db->query($sql);
                    }
                }catch(Exception $e){
                    Common_ApiProcess::log($e->getMessage());
                }
            }else{
                break;
            }
        }
    }

    /**
     * 下载订单
     */
    public static function cronLoadOrder()
    {
        $db = Common_Common::getAdapter();
        $table = self::table_cron_load_ebay_order();
        
        while(true){
            $db = Common_Common::getAdapter();
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            
            $sql = "select * from {$table} order by RAND() limit 1";
            $item = $db->fetchRow($sql);
            if($item){
                $sql = "delete from {$table} where order_sn='{$item['order_sn']}';";
                $db->query($sql);
                
                Common_ApiProcess::log("还剩{$count}条订单待处理" . "，当前ebay订单ID:" . $item['order_sn']);
                $order_sn = $item['order_sn'];
                if(! preg_match('/^[0-9]+(\-[0-9]+)?$/', $order_sn)){
                    Common_ApiProcess::log('不是ebay订单=======================================================');
                    continue;
                }
                $order = Service_EbayOrder::getByField($order_sn, 'order_sn');
                $acc = $item['user_account'];
                if($order){
                    $acc = $order['user_account'];
                }
                $acc = empty($acc) ? '' : $acc;
                $token = Ebay_EbayLib::getUserToken($acc);
                if(empty($token)){
                    continue;
                }
                
                $service = new Ebay_LoadEbayOrderService();
                $start = date('Y-m-d\TH:i:s.000\Z', strtotime('-120days'));
                $end = date('Y-m-d\TH:i:s.000\Z');
                $order_sn_arr = array(
                    $order_sn
                );
                $service->callEbay($start, $end, $order_sn_arr);
            }else{
                break;
            }
        }
    }

    /**
     * 下载订单
     */
    public static function loadEbayOrderLatest($minutes = 60)
    {
        $com = array(
            'platform' => 'ebay',
            'status' => '1'
        );
        $pUsers = Service_PlatformUser::getByCondition($com);
        foreach($pUsers as $pUser){
            $acc = $pUser['user_account'];
            $service = new Ebay_LoadEbayOrderService();
            $start = date('Y-m-d\TH:i:s.000\Z', strtotime('-' . (8 * 60 + $minutes) . 'minutes'));
            $end = date('Y-m-d\TH:i:s.000\Z', strtotime('+' . (8 * 60) . 'minutes'));
            $service->callEbay($acc, $start, $end);
        }
    }

    /**
     * 订单标记发货到ebay
     * 
     * @param unknown_type $account            
     */
    public static function CompleteSale($account = '')
    {
        $table = 'cron_complete_order_ebay';
        $db = Common_Common::getAdapter();
        // 已发货订单
        $sql = 'select a.refrence_no_platform ref_id,a.user_account  from orders a inner join csd_order co ON a.refrence_no = co.shipper_hawbcode where 1=1';
        $sql .= " and a.order_status='3'";
        $sql .= " and co.order_status='C'";
        $sql .= " and a.platform='ebay'";
        $sql .= " and a.create_type='api'";
        $sql .= " and a.order_type='sale'";
        $sql .= " and a.sync_status in('0','2','3','6')";
        if($account){
            $sql .= " and a.user_account = '" . $account . "'";
        }
        $sql .= '  limit 0,200;';
        Common_ApiProcess::log($sql);
        $orders = $db->fetchAll($sql);
        
//         // 待发货，缺货，客户手动出发的订单
//         $sql2 = 'select  a.refrence_no_platform ref_id,a.user_account  from orders a where 1=1';
//         $sql2 .= " and a.order_status in (3)";
//         $sql2 .= " and a.platform='ebay'";
//         $sql2 .= " and a.create_type='api'";
//         $sql2 .= " and a.order_type='sale'";
//         $sql2 .= " and a.sync_status = 6";
//         // $sql2 .= " and a.refrence_no_warehouse is not null";
//         if($account){
//             $sql2 .= " and a.user_account = '" . $account . "'";
//         }
//         $sql2 .= '  limit 0,200;';
//         Common_ApiProcess::log($sql2);
//         $orders_hand = $db->fetchAll($sql2);
//         foreach($orders_hand as $key => $value){
//             $orders[] = $value;
//         }
        
        $service = new Ebay_OrderEbayService();
        $sqls = array();
        $sqls[] = "
        CREATE TABLE if not exists `{$table}` (
        `ref_id` varchar(32) NOT NULL COMMENT '平台订单号',
        `user_account` varchar(32) NOT NULL COMMENT 'ebay账号',
        PRIMARY KEY (`ref_id`),
        UNIQUE KEY `ref_id` (`ref_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='需要同步的订单';
        ";
        foreach($orders as $v){
            $sqls[] = "replace into {$table}(ref_id,user_account) values('{$v['ref_id']}','{$v['user_account']}');";
        }
        foreach($sqls as $sql){
            $db->query($sql);
        }
        
        while(true){
            $db = Zend_Registry::get('db');
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            Common_ApiProcess::log($count . " order need complete sale");
            
            $sql = "select * from {$table} order by RAND() limit 1";
            $order = $db->fetchRow($sql);
            if($order){
                $result = $service->completeSaleNew($order['ref_id']);
                $msg = "complete order,ask:" . $result['ask'] . ',ref_id:' . $order['ref_id'] . ',user_account' . $order['user_account'] . ',Ack: ' . $result['Ack'] . ',message:' . $result['message'] . "";
                
                Common_ApiProcess::log($msg);
                $sql = "delete from {$table} where ref_id='{$order['ref_id']}';";
                Common_ApiProcess::log($sql);
                
                $db->query($sql);
            }else{
                break;
            }
        }
    }

    /**
     * 手工拉取Item
     * 调用示例，查看/auto/loadEbayOrderHand.php
     * 
     * @throws Exception
     */
    public static function loadEbayItemHand($company_code = false, $user_account = false, $day = false, $right_now = true)
    {
        $table = 'cron_load_item_hand';
        $db = Common_Common::getAdapter();
        $sqls = array();
        $sqls[] = "
        CREATE TABLE if not exists `{$table}` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `platform` varchar(32) NOT NULL DEFAULT '' COMMENT '平台',
        `user_account` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
        `company_code` varchar(32) NOT NULL DEFAULT '' COMMENT '公司代码',
        `start` varchar(64) NOT NULL,
        `end` varchar(64) NOT NULL,
        PRIMARY KEY (`id`)
        ) COMMENT='下载n天至昨天的数据，任务表';
        ";
        if($company_code && $user_account && $day){
            $con = array(
                'platform' => 'ebay',
                'company_code' => $company_code,
                'user_account' => $user_account,
                'status' => '1'
            );
            $pUsers = Service_PlatformUser::getByCondition($con);
            
            if(empty($pUsers)){
                throw new Exception("platform[ebay]company_code:[{$company_code}]user_account:[{$user_account}]不存在该条件的有效平台用户");
            }
            $pUsers = array_pop($pUsers);
            
            $start = strtotime($day);
            $now = date('Y-m-d H:i:s');
            $end = strtotime($now);
            $between = $end - $start;
            $count = $between % 36000 == 0 ? ($between / 36000) : ($between / 36000 + 1); // 按照10小时拆分
            $count = intval($count);
            $dateArr = splitDate($day, $now, $count);
            foreach($dateArr as $v){
                $row = array(
                    'platform' => 'ebay',
                    'company_code' => $company_code,
                    'user_account' => $user_account,
                    'start' => $v['start'],
                    'end' => $v['end']
                );
                $sqls[] = "insert into {$table}(platform,company_code,user_account,start,end) values('{$row['platform']}','{$row['company_code']}','{$row['user_account']}','{$row['start']}','{$row['end']}');";
            }
        }
        foreach($sqls as $sql){
            Common_ApiProcess::log($sql);
            $db->query($sql);
        }
        
        while($right_now){
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            Common_ApiProcess::log("还有{$count}条待处理==================================");
            
            $sql = "select * from {$table} order by rand() limit 1";
            // Common_ApiProcess::log($sql);
            $row = $db->fetchRow($sql);
            if($row){
                $orderEbay = new Ebay_ItemEbayService();
                $type = 'Start';
                try{
                    $total = $orderEbay->callEbay($row['user_account'], $row['start'], $row['end'], $type);
                    Common_ApiProcess::log("[{$type}][{$row['user_account']}] {$row['start']}~{$row['end']}有{$total}条item");
                }catch(Exception $e){
                    Common_ApiProcess::log("[{$type}][{$row['user_account']}] {$row['start']}~{$row['end']}异常," . $e->getMessage());
                    Ec::showError("[{$type}][{$row['user_account']}] {$row['start']}~{$row['end']}异常," . $e->getMessage(), 'load_ebay_item_hand_');
                }
                
                $type = 'End';
                try{
                    $total = $orderEbay->callEbay($row['user_account'], $row['start'], $row['end'], $type);
                    Common_ApiProcess::log("[{$type}][{$row['user_account']}] {$row['start']}~{$row['end']}有{$total}条item");
                }catch(Exception $e){
                    Common_ApiProcess::log("[{$type}][{$row['user_account']}] {$row['start']}~{$row['end']}异常," . $e->getMessage());
                    Ec::showError("[{$type}][{$row['user_account']}] {$row['start']}~{$row['end']}异常," . $e->getMessage(), 'load_ebay_item_hand_');
                }
                
                // 运行一条，删除一条
                $sql = "delete from {$table} where id='{$row['id']}';";
                Common_ApiProcess::log($sql);
                $db->query($sql);
            }else{
                break;
            }
        }
    }

    /**
     * 手工拉单
     * 调用示例，查看/auto/loadEbayOrderHand.php
     * 
     * @throws Exception
     */
    public static function loadEbayOrderHand($company_code = false, $user_account = false, $day = false, $right_now = true)
    {
        $table = 'cron_load_order_hand';
        $db = Common_Common::getAdapter();
        $sqls = array();
        $sqls[] = "
        CREATE TABLE if not exists `{$table}` (
        `id` int(10) NOT NULL AUTO_INCREMENT,        
        `platform` varchar(32) NOT NULL DEFAULT '' COMMENT '平台',
        `user_account` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
        `company_code` varchar(32) NOT NULL DEFAULT '' COMMENT '公司代码',
        `start` varchar(64) NOT NULL,
        `end` varchar(64) NOT NULL,
        `add_time` varchar(64) NOT NULL,        
        PRIMARY KEY (`id`)
        ) COMMENT='下载n天至昨天的数据，任务表';
        ";
        if($company_code && $user_account && $day){
            $con = array(
                'platform' => 'ebay',
                'company_code' => $company_code,
                'user_account' => $user_account,
                'status' => '1'
            );
            $pUsers = Service_PlatformUser::getByCondition($con);
            
            if(empty($pUsers)){
                throw new Exception("platform[ebay]company_code:[{$company_code}]user_account:[{$user_account}]不存在该条件的有效平台用户");
            }
            $pUsers = array_pop($pUsers);
            
            $start = strtotime($day);
            $now = date('Y-m-d H:i:s');
            $end = strtotime($now);
            $between = $end - $start;
            $count = $between % 7200 == 0 ? ($between / 7200) : ($between / 7200 + 1); // 按照两小时拆分
            $count = intval($count);
            $dateArr = splitDate($day, $now, $count);
            foreach($dateArr as $v){
                $row = array(
                    'platform' => 'ebay',
                    'company_code' => $company_code,
                    'user_account' => $user_account,
                    'start' => $v['start'],
                    'end' => $v['end'],
                    'add_time' => now()
                );
                $sqls[] = "insert into {$table}(platform,company_code,user_account,start,end,add_time) values('{$row['platform']}','{$row['company_code']}','{$row['user_account']}','{$row['start']}','{$row['end']}','{$row['add_time']}');";
            }
        }
        foreach($sqls as $sql){
            Common_ApiProcess::log($sql);
            $db->query($sql);
        }
        
        while($right_now){
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            Common_ApiProcess::log("还有{$count}条待处理==================================");
            
            $sql = "select * from {$table} order by rand() limit 1";
            // Common_ApiProcess::log($sql);
            $row = $db->fetchRow($sql);
            if($row){
                Common_ApiProcess::log("[{$row['user_account']}] {$row['start']}~{$row['end']}");
                $orderEbay = new Ebay_LoadEbayOrderService();
                $return = $orderEbay->handerLoadOrder($row['start'], $row['end'], $row['user_account']);
                if(is_array($return)){
                    Common_ApiProcess::log($return['message']);
                }
                // 运行一条，删除一条
                $sql = "delete from {$table} where id='{$row['id']}';";
                Common_ApiProcess::log($sql);
                $db->query($sql);
            }else{
                break;
            }
        }
    }

    public static function table_cron_load_ebay_item()
    {
        $db = Common_Common::getAdapter();
        $table = 'cron_load_ebay_item';
        $sql = "
        CREATE TABLE if not exists `{$table}` (
        `item_id` varchar(64) NOT NULL COMMENT '订单ID',
        `user_account` varchar(64) NOT NULL COMMENT 'user_account',
        `company_code` varchar(64) NOT NULL COMMENT 'company_code',
        PRIMARY KEY (`item_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='';
        ";
        $db->query($sql);
        return $table;
    }

    public static function table_cron_load_ebay_order()
    {
        $db = Common_Common::getAdapter();
        $table = 'cron_load_ebay_order';
        $sql = "
        CREATE TABLE if not exists `{$table}` (
        `order_sn` varchar(64) NOT NULL COMMENT '订单ID',
        `user_account` varchar(64) NOT NULL COMMENT 'user_account',
        `company_code` varchar(64) NOT NULL COMMENT 'company_code',
        PRIMARY KEY (`order_sn`),
        KEY `user_account` (`user_account`)
        )DEFAULT CHARSET=utf8 COMMENT='';
        ";
        $db->query($sql);
        return $table;
    }

    /**
     * 下载Item
     */
    public static function cronLoadItem($item_id = '', $company_code = '', $user_account = '')
    {
        $db = Common_Common::getAdapter();
        $table = self::table_cron_load_ebay_item();
        
        if(! empty($item_id) && ! empty($user_account)){
            try{
                $arr = array(
                    'item_id' => $item_id,
                    'company_code' => $company_code,
                    'user_account' => $user_account
                );
                $db->insert($table, $arr);
            }catch(Exception $e){
                //
            }
            
        }
        while(true){
            $db = Common_Common::getAdapter();
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            
            $sql = "select * from {$table} order by RAND() limit 1";
            $item = $db->fetchRow($sql);
            if($item){
                $sql = "delete from {$table} where item_id='{$item['item_id']}';";
                $db->query($sql);                
                Common_ApiProcess::log("还剩{$count}条记录待处理" . "，当前ID:" . $item['item_id']);
                $item_id = $item['item_id'];
                $itemRow = Service_EbayItem::getByField($item_id, 'item_id');
                $acc = $item['user_account'];
                if($itemRow){
                    $acc = $itemRow['user_account'];
                    $comp = $itemRow['company_code'];
                }
                $return = Ebay_ItemEbayService::updateItem($item_id, $acc,$comp);
                if(is_array($return)){
                    $msg = "[{$item_id}][{$acc}]" . ":" . $return['GetItemResponse']['Ack'] . "";
                    Common_ApiProcess::log($msg);
                }
            }else{
                break;
            }
        }
    }

    public static function table_cron_load_ebay_item_transactions(){
		$db = Common_Common::getAdapter ();
		$table = 'table_cron_load_ebay_item_transactions';
		
		$sql = "show tables like '{$table}';";
		$exist = Common_Common::fetchRow ( $sql );
		if (! $exist) {
			$sql = "
    		CREATE TABLE if not exists `{$table}` (
    		id int not null auto_increment,
    		`order_sn` varchar(64) NOT NULL COMMENT '订单ID',
    		`user_account` varchar(64) NOT NULL COMMENT '',
    		`company_code` varchar(64) NOT NULL COMMENT '',
    		`item_id` varchar(64) NOT NULL COMMENT '',
    		`transaction_id` varchar(64) NOT NULL COMMENT '',
    		PRIMARY KEY (`id`),
    		KEY `order_sn` (`order_sn`),
    		KEY `user_account` (`user_account`)
    		)DEFAULT CHARSET=utf8 COMMENT='';
    		";
			Common_Common::query ( $sql );
		}
		// 无地址订单
		$sql = "select a.company_code,a.user_account,b.order_sn,b.item_id,b.transaction_id from ebay_order a INNER JOIN ebay_order_detail b on a.order_sn=b.order_sn   where a.country='' and created!='1';";
		$data = $db->fetchAll ( $sql );
		Common_ApiProcess::log ( '无地址订单' . count ( $data ) . '个' );
		Ec::showError ( print_r ( $data, true ), 'table_cron_load_ebay_item_transactions' );
		foreach ( $data as $v ) {
			$arr = array (
					'company_code' => $v ['company_code'],
					'user_account' => $v ['user_account'],
					'order_sn' => $v ['order_sn'],
					'item_id' => $v ['item_id'],
					'transaction_id' => $v ['transaction_id'] 
			);
			$sql = "select * from {$table} where order_sn='{$v['order_sn']}' and user_account='{$v['user_account']}' and company_code='{$v['company_code']}' ";
			$exist = $db->fetchRow ( $sql );
			if (! $exist) {
				$db->insert ( $table, $arr );
			}
		}
		
		return $table;
	}
    /**
     * 下载无地址订单
     */
    public static function cronLoadOrderNoAddressByItemTransactions(){
    	$db = Common_Common::getAdapter();
    	$table = Ebay_EbayServiceCommon::table_cron_load_ebay_item_transactions();
    	while(true){
    		$db = Common_Common::getAdapter();
    		$sql = "select count(*) from {$table}";
    		$count = $db->fetchOne($sql);
    
    		$sql = "select * from {$table}   limit 1";
    		$item = $db->fetchRow($sql);
    		if($item){
    			$sql = "delete from {$table} where id='{$item['id']}';";
    			$db->query($sql);
    
    			Common_ApiProcess::log("还剩{$count}条记录待处理" . "，当前ID:" . $item['order_sn']);
    			$order_sn = $item['order_sn'];
    			$user_account = $item['user_account'];
    			$company_code = $item['company_code'];
    			$item_id = $item['item_id'];
    			$transaction_id = $item['transaction_id'];
    			$svc = new Ebay_OrderEbayCheckAddress();
    			$svc->setCompanyCode($company_code);
    			$svc->setUserAccount($user_account);
    			//下载信息
    			$rs = $svc->callEbay($order_sn, $item_id,$transaction_id);
    		}else{
    			break;
    		}
    	}
    }
    /**
     * 日志输出
     * 
     * @param unknown_type $msg            
     * @param unknown_type $output            
     */
    public static function log($msg, $save = false)
    {
        if(PHP_SAPI == 'cli'){
            echo '[' . date('Y-m-d H:i:s') . ']' . iconv('UTF-8', 'GB2312', $msg . "\n");
        }
        if($save){
            Ec::showError($msg, 'cron_log_');
        }
    }
}