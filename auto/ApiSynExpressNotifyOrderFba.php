<?php
/**
 *  同步订单到物流服务商
 */
$loop = isset($argv[4]) ? $argv[4] : 0;
define('APPLICATION_PATH_FILE', dirname(dirname(__FILE__)));
$flagFile = APPLICATION_PATH_FILE . '/data/log/apiSynExpressNotifyOrdersFba';
if (file_exists($flagFile)) {
    //如果锁文件存在时间过长删除锁文件
    if (time() - filemtime($flagFile) > 1800) {
        @unlink($flagFile);
    }
}
//如果锁文件存在,程序已经运行.
 if (file_exists($flagFile)) {
    echo "Is already running,please unlock! \n";
    exit;
} 
//加锁,创建锁文件
touch($flagFile);
if (preg_match('/linux/i', PHP_OS) || preg_match('/Unix/i', PHP_OS)) {
    chmod($flagFile, 0777);
}
//您的待执行代码
require_once(dirname(__FILE__).'/config.php');
define('RUNTIME', '[' . date('Y-m-d H:i:s') . '] ');
echo RUNTIME . "Starting!\n";

try {
			$obj = new API_Common_ServiceExpressCreateOrder();
			$obj->notifyOrderToServiceFba();
			
} catch (Exception $e) {
	Common_Common::myEcho('[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n");
}
Common_Common::myEcho("[" . date('Y-m-d H:is') . "] End run\r\n");

//解锁,删除锁文件
unlink($flagFile);

Common_Common::myEcho("[" . date('Y-m-d H:is') . "] End run\r\n");

//解锁,删除锁文件
unlink($flagFile);
