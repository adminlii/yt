<?php
class Service_OrderKeywordProcess
{

    /**
     * 关键字，查询服务，包括订单号，仓库单号，参考号，sku，itemID，TransactionID,RecordNo，买家账号，买家邮箱
     *
     * @param unknown_type $refId            
     * @throws Exception
     */
    public static function updateOrderKeyword($refId,$field='refrence_no_platform')
    {
    	$start = time();
        try{
            self::checkColumnKeywordExist();
            $table = self::get_table_orders_keyword();
//             echo $field;exit;
            $allowFields = array('order_id','refrence_no_platform');
            if(!in_array($field, $allowFields)){
                throw new Exception('参数错误');
            }
            $keyword = array();
            $order = Service_Orders::getByField($refId, $field);
            if(! $order){
                throw new Exception('订单号不存在');
            }
            //重置参数默认值==========================注意...
            $refId = $order['refrence_no_platform'];

            Common_ApiProcess::log('RefId-->' . $refId . ',生成订单关键字');
            
            $keyword[] = $order['refrence_no_platform'];//平台单号
            $keyword[] = $order['refrence_no'];//参考号
            $keyword[] = $order['refrence_no_sys'];//系统单号
            $keyword[] = $order['refrence_no_warehouse'];//仓库单号
            $keyword[] = $order['shipping_method_no'];//运输方式
            $keyword[] = $order['consignee_country'];//目的国家
            $keyword[] = $order['buyer_id'];//买家ID
            $keyword[] = $order['buyer_name'];//收件人名称
            $keyword[] = $order['buyer_mail'];//收件人邮箱
            $keyword[] = $order['fulfillment_channel'];//Amazon配送渠道
            
            $con = array(
                'order_id' => $order['order_id']
            );
            $orderProducts = Service_OrderProduct::getByCondition($con);
            if(empty($orderProducts)){
//                 throw new Exception('订单明细不存在');
            }
            
            $orderProductArr = array();
            $toWmsStatus = array(
                '3',
                '4',
                '6'
            );
            // 已经提交到仓库，取当时的对应关系,该信息记录在warehouse_sku字段，内容格式如下(XL609*1*40.000;XL907*1*40.000;XL909*1*20.000)
            if(in_array($order['order_status'], $toWmsStatus)){
                foreach($orderProducts as $key => $val){
                    $val['product_sku'] = empty($val['product_sku']) ? '--NoSku--' : $val['product_sku'];
                    $keyword[] = $val['product_sku'];//平台SKU
                    $keyword[] = $val['op_recv_account'];//
                    $keyword[] = $val['op_ref_tnx'];//eBay订单交易号
                    $keyword[] = $val['op_ref_item_id'];//ebay ItemID
                    $keyword[] = $val['op_record_id'];//eBay交易流水号[RecordNo]
                    $keyword[] = $val['op_ref_buyer_id'];//买家ID
                    $keyword[] = $val['op_site'];//站点
                    
                    /*
                     * 产品没有对应关系，warehouse_sku记录的是 $val['product_sku'] . '*' . $val['op_quantity']. '*100.000'
                     */
                    if(! empty($val['warehouse_sku']) && ($val['warehouse_sku'] != $val['product_sku'] . '*' . $val['op_quantity'] . '*100.000')){
                        $rArr = explode(';', $val['warehouse_sku']);
                        foreach($rArr as $k => $vv){
                            $t = explode('*', $vv);
                            $keyword[] = trim($t[0]);
                        }
                    }
                }
            }else{
                foreach($orderProducts as $key => $val){
                    $val['product_sku'] = empty($val['product_sku']) ? '--NoSku--' : $val['product_sku'];
                    $keyword[] = $val['product_sku'];
                    $keyword[] = $val['op_recv_account'];
                    $keyword[] = $val['op_ref_tnx'];
                    $keyword[] = $val['op_ref_item_id'];
                    $keyword[] = $val['op_record_id'];
                    $keyword[] = $val['op_ref_buyer_id'];
                    $keyword[] = $val['op_site'];
                    
                    $rArr = Service_ProductCombineRelationProcess::getRelation($val['product_sku'], $order['user_account']);
                    
                    foreach($rArr as $k => $vv){
                        $keyword[] = $vv['pcr_product_sku'];
                    }
                }
            }
            $address = Service_ShippingAddress::getByField($refId, 'OrderID');
            if($address){
                $keyword[] = $address['Name'];//收件人姓名
                $keyword[] = $address['Country'];//收件人国家
                $keyword[] = $address['CountryName'];//
                $keyword[] = $address['Phone'];//收件人手机
                $keyword[] = $address['PostalCode'];//收件人邮编
                $keyword[] = $address['doorplate'];//收件人门牌号
            }
            
            $sql = "select * from order_merge_map where ref_no_platform='{$refId}' or sub_ref_no_platform='{$refId}'";
            $merge_rows = Common_Common::fetchAll($sql);
            foreach($merge_rows as $row){
                $keyword[] = $row['ref_no_platform'];
                $keyword[] = $row['sub_ref_no_platform'];
            }
            $con = array('order_sn'=>$refId);
            $payments = Service_EbayOrderPayments::getByCondition($con);
            foreach($payments as $payment){
                $keyword[] = $payment['reference_id'];//交易信息
            }           

            $keyword = array_unique($keyword);
            $keyword[] = 'ALL';
            foreach($keyword as $k => $v){
                $v = trim($v);
                if(empty($v)){
                    unset($keyword[$k]);
                }
            }
            $updateRow = array(
                'keyword' => implode('*#*', $keyword)
            );
            $keyword = implode('*#*', $keyword);
            $keyword = preg_replace('/\'/','',$keyword);
            //删除旧数据
            $sql = "delete from {$table} where order_id='{$order['order_id']}';";
            $db = Common_Common::getAdapter();
            $db->query($sql);
            //插入新数据
			$row = array (
					'order_id' => $order ['order_id'],
					'keyword' => $keyword,
					'company_code'=>$order['company_code'],
					'user_account'=>$order['user_account'],
					'refrence_no_platform' => $order ['refrence_no_platform'],
					'create_time_sys' => date ( 'Y-m-d H:i:s' ) 
			);
            $db->insert($table,$row);  
            $end = time();
            $time_cost = $end-$start;
            Common_ApiProcess::log("耗时{$time_cost}s");
            // print_r($keyword);exit;
            return $keyword;
        }catch(Exception $e){
            Common_ApiProcess::log('RefId-->' . $refId . ',Err-->' . $e->getMessage());
            return '';
        }
    }

    /**
     * 临时表
     *
     * @return string
     */
    public static function cron_update_order_keyword()
    {
        $table = 'cron_update_order_keyword';
        $db = Common_Common::getAdapter();

        $sql = "show tables like '{$table}';";
        $exist = Common_Common::fetchRow($sql);
        if(!$exist){
	        $sql = "
	        CREATE TABLE if not exists `{$table}` (
	        `order_sn` varchar(64) NOT NULL COMMENT '订单ID',
	        PRIMARY KEY (`order_sn`)
	        )DEFAULT CHARSET=utf8 COMMENT='';
	        ";
	        $db->query($sql);
        }
        
        return $table;
    }

    /**
     * 添加更新订单关键字服务
     *
     * @param unknown_type $refId            
     */
    public static function cronUpdateOrderKeywordTaskAdd($refId, $field = 'refrence_no_platform')
    {
        $table = self::cron_update_order_keyword();
        if($field == 'refrence_no_platform'){
            $sql = "replace into {$table}(order_sn) values('{$refId}')";
            Common_Common::query($sql);
        }elseif($field == 'order_id'){
            $order = Service_Orders::getByField($refId, 'order_id');
            if($order){
                $refId = $order['refrence_no_platform'];
                $sql = "replace into {$table}(order_sn) values('{$refId}')";
                Common_Common::query($sql);
            }
        }
    }

    /**
     * 检测keyword列是否存在
     */
    public static function checkColumnKeywordExist()
    {
        try{
            $sql = "desc orders";
            $rows = Common_Common::fetchAll($sql);
            $columns = array();
            foreach($rows as $v){
                $columns[] = $v['Field'];
            }
            if(! in_array('keyword', $columns)){
                $sql = "ALTER TABLE `orders` ADD COLUMN `keyword`  varchar(500) NULL DEFAULT '' COMMENT '关键字，查询服务，包括订单号，仓库单号，参考号，sku，itemID，TransactionID,RecordNo，买家账号，买家邮箱,运单号等等'";
                Common_Common::query($sql);
            }
        }catch(Exception $e){
            Common_ApiProcess::log($e->getMessage());
        }
    }

    /**
     * 订单关键字表
     */
    private static function get_table_orders_keyword()
    {
		$table = 'orders_keyword';
		try {
			$sql = "show tables like '{$table}';";
			$exist = Common_Common::fetchRow ( $sql );
			if (! $exist) {
				$sql = "
	    			CREATE TABLE IF NOT EXISTS `{$table}` (
	    			`order_id` int(11) NOT NULL auto_increment,
	    			`refrence_no_platform` varchar(60) NOT NULL default '' comment '订单号',
	    			`keyword` varchar(1000) comment '关键字',
	    			`create_time_sys` varchar(60) NOT NULL default '' COMMENT '创建时间',
	    			PRIMARY KEY  (`order_id`),
	    			KEY `refrence_no_platform` (`refrence_no_platform`),
	    			KEY `keyword` (`keyword`)
	    			) ;
    			";
				Common_Common::query ( $sql );
			}
		} catch ( Exception $e ) {
			Common_ApiProcess::log ( $e->getMessage () );
		}
		return $table;
	}
    /**
     * 循环遍历更新订单信息
     */
    public static function cronUpdateOrderKeyword()
    {
		$table = self::cron_update_order_keyword ();
		while ( true ) {
			$db = Common_Common::getAdapter ();
			$sql = "select count(*) from {$table}";
			$count = $db->fetchOne ( $sql );
			Common_ApiProcess::log ( "还剩{$count}条订单待处理=========================" );
			$sql = "select * from {$table} order by rand() limit 100";
			$items = $db->fetchAll ( $sql );
			if (! empty ( $items )) {
				foreach ( $items as $item ) {
					$refId = $item ['order_sn'];
					$sql = "delete from {$table} where order_sn='{$refId}';";
					Common_ApiProcess::log ( $sql );
					$db->query ( $sql );
					self::updateOrderKeyword ( $refId, 'refrence_no_platform' );
				}
			} else {
				break;
			}
		}
	}
}