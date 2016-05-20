<?php
class Ebay_GenEbayOrderService
{

    public static function cron_gen_ebay_order()
    {
        $db = Common_Common::getAdapter();
        $table = 'cron_gen_ebay_order';
        $timestamp = APPLICATION_PATH . '/../data/log/' . $table;
        $sql = "
        CREATE TABLE if not exists `{$table}` (
        `order_sn` varchar(64) NOT NULL COMMENT '订单ID',
        `user_account` varchar(64) NOT NULL COMMENT '',
        `company_code` varchar(64) NOT NULL COMMENT '',
        `eo_id` varchar(64) NOT NULL COMMENT '',
        PRIMARY KEY (`order_sn`),
        UNIQUE KEY `order_sn` (`order_sn`),
        UNIQUE KEY `eo_id` (`eo_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='';
        ";
        $db->query($sql);
        
        return $table;
    }

    /**
     * 生成订单
     */
    public static function generateOrder()
    {
        $db = Common_Common::getAdapter();
        $table = Ebay_GenEbayOrderService::cron_gen_ebay_order();
        $sql = "SELECT order_sn,eo_id,user_account,company_code FROM `ebay_order` where created=0;";
        $data = $db->query($sql);
        foreach($data as $v){
        	try {
        		$db->insert($table,$v);
        	} catch (Exception $e) {
        		//==================
        	}        	
        }
        
        while(true){
            $db = Common_Common::getAdapter();
            $sql = "select count(*) from {$table}";
            $count = $db->fetchOne($sql);
            
            $sql = "select * from {$table} order by RAND() limit 1";
            $item = $db->fetchRow($sql);
            if($item){
                $order_sn = $item['order_sn'];
                Common_ApiProcess::log("还剩{$count}条订单待处理" . "，当前ebay订单ID:" . $item['order_sn']);
                
                $sql = "delete from {$table} where order_sn='{$item['order_sn']}';";
                $db->query($sql);
                // 生成订单
                $rs = Ebay_GenEbayOrderService::generateOrderSingleTransaction($order_sn);
            }else{
                break;
            }
        }
    }

    /**
     * 生成订单
     *
     * @param unknown_type $order_sn            
     */
    public static function generateOrderSingleTransaction($order_sn)
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail'
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            // 生成订单
            Ebay_GenEbayOrderService::generateOrderSingle($order_sn);
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $db->rollback();
            // $updateRow = array('is_load'=>'2');
            // Service_EbayOrder::update($updateRow, $order_sn,'order_sn');
            Common_ApiProcess::log("[{$order_sn}][" . $e->getCode() . "]" . $e->getMessage());
            $return['message'] = $e->getMessage();
            $return['err_code'] = $e->getCode();
        }
        return $return;
    }

    /**
     * 生成订单
     *
     * @param unknown_type $order_sn            
     */
    public static function generateOrderSingle($order_sn)
    {
        $genOrder = new Ebay_Order_GenOrder($order_sn);
        return $genOrder->genOrder();
    }
}