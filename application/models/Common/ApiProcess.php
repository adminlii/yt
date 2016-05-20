<?php
class Common_ApiProcess
{

    /**
     * 系统单号
     * @return string
     */
    public static function getRefrenceSysCode(){
        $refrence_no_sys = '';
        while(true){
            $refrence_no_sys = Common_GetNumbers::getCode('CURRENT_ORDER_SYS_COUNT', 'SYS'); // 系统单号
            if(! Service_Orders::getByField($refrence_no_sys, 'refrence_no_sys')){
                break;
            }
        }
        return $refrence_no_sys;
    }
    
    /**
     * 未设置的值默认为空字符串
     * 
     * @param unknown_type $arr            
     * @return unknown Ambigous unknown>
     */
    public static function nullToEmptyString($arr)
    {
        if(! is_array($arr)){
            return $arr;
        }
        foreach($arr as $k => $v){
            $arr[$k] = isset($v) ? (is_string($v) ? trim($v) : $v) : '';
        }
        return $arr;
    }

    /**
     * 日志
     * 
     * @param unknown_type $str            
     */
    public static function log($str)
    {
		if (empty ( $str )) {
			return;
		}
// 		Ec::showError($str, 'shopify_load_data_');
		if (Zend_Registry::isRegistered('SAPI_DEBUG') && Zend_Registry::get('SAPI_DEBUG') === true) {
			echo '[' . date ( 'Y-m-d H:i:s' ) . ']' . iconv ( 'UTF-8', 'GB2312', $str . "\n" );
		}
	}
    
	public static function getOrderTongjiTable(){
	    $db = Common_Common::getAdapter();
	    $table = 'orders_tongji';
	    $sqls = array();
	    $sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	    `ref_id` varchar(64) NOT NULL,
	    `platform` varchar(64) NOT NULL DEFAULT '',
	    `user_account` varchar(64) NOT NULL DEFAULT '',
	    `company_code` varchar(64) NOT NULL DEFAULT '',
	    `order_status` int(10) NOT NULL DEFAULT '0' comment'replace into {$table}(ref_id,platform,user_account,company_code,order_status,is_merge) SELECT refrence_no_platform,platform,user_account,company_code,order_status,is_merge from orders;delete from orders_tongji where is_merge=2;',
	    `is_merge` int(10) NOT NULL DEFAULT '0',
	    PRIMARY KEY (`ref_id`),
	    KEY `platform` (`platform`),
	    KEY `user_account` (`user_account`),
	    KEY `is_merge` (`is_merge`),
	    KEY `order_status` (`order_status`)
	    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	    ";
	    foreach($sqls as $sql){
	        $db->query($sql);
	    }
	    return $table;
	}

    public static function orderTongji($force = false)
    {
        $timestamp = APPLICATION_PATH . '/../data/log/orderTongji';
        if($force || ! file_exists($timestamp) || filemtime($timestamp) + 60 * 20 < time()){ // 时间戳文件不存在或者创建时间超过了20分钟
            @unlink($timestamp);
            file_put_contents($timestamp, now());
            Common_ApiProcess::log("生成统计信息");
            $db = Common_Common::getAdapter();
            $table = Common_ApiProcess::getOrderTongjiTable();
            
            $sqls[] = "delete from {$table};";
            $sqls[] = 'delete FROM orders WHERE refrence_no_platform IS NULL;';
            $sqls[] = "replace into {$table}(ref_id,platform,user_account,company_code,order_status,is_merge) SELECT refrence_no_platform,platform,user_account,company_code,order_status,is_merge from orders;";
            $sqls[] = "delete from orders_tongji where is_merge=2;";
            
            $db = Common_Common::getAdapter();
            foreach($sqls as $sql){
                $db->query($sql);
            }
        }
    }

    public static function orderTongjiSingle($refId,$field = 'refrence_no_platform'){
        $db = Common_Common::getAdapter();
        $table = Common_ApiProcess::getOrderTongjiTable();
        
        $sqls[] = "replace into {$table}(ref_id,platform,user_account,company_code,order_status,is_merge) SELECT refrence_no_platform,platform,user_account,company_code,order_status,is_merge from orders where {$field}='{$refId}';";
        
        $db = Common_Common::getAdapter();
        foreach($sqls as $sql){
            $db->query($sql);
        }
    }
    /**
     * 一位数组字符串拼接
     */
    public static function array_key_val_string($arr){
        $keys = array_keys($arr);
        $vals = array_values($arr);
        $data = array();
        foreach($keys as $k=>$v){
            $data[] = $v.':'.$vals[$k];
        }
        return implode(";", $data);
    }
    
    /**
     * 两个数组有差异
     * @param unknown_type $new
     * @param unknown_type $exist
     * @return string
     */
    public static function array_diff_to_string($new,$exist){
        $diff = array_diff_assoc($new, $exist); //
        if(! empty($diff)){
            $log = array();
            foreach($diff as $k => $v){
                $log[] = $k . ':from [' . $exist[$k] . ']to[' . $new[$k] . ']';
            }            
            return implode("\n", $log);
        }
        return '';
    }
}