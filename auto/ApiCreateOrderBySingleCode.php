<?php

//您的待执行代码
require_once(dirname(__FILE__).'\config.php');
/**
 *  同步订单到物流服务商
 */
$loop = isset($argv[4]) ? $argv[4] : 0;
define('APPLICATION_PATH_FILE', dirname(dirname(__FILE__)));
$flagFile = APPLICATION_PATH_FILE . '/data/log/apiSynExpressCreateOrder';
if (file_exists($flagFile)) {
    //如果锁文件存在时间过长删除锁文件
    if (time() - filemtime($flagFile) > 1800) {
        @unlink($flagFile);
    }
}
//如果锁文件存在,程序已经运行.
/* if (file_exists($flagFile)) {
    echo "Is already running,please unlock! \n";
    exit;
} */
//加锁,创建锁文件
touch($flagFile);
if (preg_match('/linux/i', PHP_OS) || preg_match('/Unix/i', PHP_OS)) {
    chmod($flagFile, 0777);
}

define('RUNTIME', '[' . date('Y-m-d H:i:s') . '] ');
echo RUNTIME . "Starting!\n";
try {
   
	$sql = "select distinct formal_code,server_channelid from order_processing";
	//随机取运输方式
	$rows = Common_Common::fetchAll($sql);
	
	$db2=Common_Common::getAdapterForDb2();
	
	if (!empty($rows)) {
		foreach ($rows as $key => $val) {
			//echo $val['formal_code'] . "\r\n";
// 			//如果执行时间超出30分钟,则中断不再执行
// 			if ($runTime - $theTime > 1700) {
// 				Common_Common::myEcho("执行超出30分钟,强制中断\r\n");
// 				break;
// 			}

			//判断预报方式
			$sql="SELECT document_rule,order_config FROM `pbr_serverrule` 
			      WHERE server_channelid='{$val['server_channelid']}'";  
			$config=$db2->fetchRow($sql);
			
			$arr=array(
					'A,In',
					'A,Out'
			);
			$init=true;
			if (in_array($config['document_rule'], $arr)){
				$init=false;
			}
			
			
			$obj = new API_Common_ServiceExpressCreateOrder();
			$obj->createOrderToService($val['formal_code'],$config['order_config'],$init,$loop);
			$runTime = time();
		}
	}
	
} catch (Exception $e) {
    Common_Common::myEcho('[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n");
}
Common_Common::myEcho("[" . date('Y-m-d H:is') . "] End run\r\n");

//解锁,删除锁文件
unlink($flagFile);
