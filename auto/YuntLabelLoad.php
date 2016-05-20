<?php
/**
 * 添加同步订单任务列表
 */
require_once ('config.php');
define('APPLICATION_PATH_FILE', dirname(dirname(__FILE__)));
$flagFile = APPLICATION_PATH_FILE . '/data/log/yunlabelload';
if (file_exists($flagFile)) {
	//如果锁文件存在时间过长删除锁文件
	if (time() - filemtime($flagFile) > 1000) {
		@unlink($flagFile);
	}
}
//如果锁文件存在,程序已经运行.
if (file_exists($flagFile)) {
	Common_Common::myEcho("Is already running,please unlock! \n");
	exit;
}
//加锁,创建锁文件
touch($flagFile);
if (preg_match('/linux/i', PHP_OS) || preg_match('/Unix/i', PHP_OS)) {
	chmod($flagFile, 0777);
}
//您的待执行代码


$orderCode = isset($argv[1]) ? $argv[1] : '';
$serviceCode = isset($argv[2]) ? $argv [2] : '';
define('RUNTIME', '[' . date('Y-m-d H:i:s') . '] ');
echo RUNTIME . "Starting!\n";
try {
	
	$obj = new API_YunExpress_LoadLabel();
	$obj->loadLabel();
} catch (Exception $e) {
	echo '[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n";
}
echo "[" . date('Y-m-d H:is') . "] End run\r\n";

//解锁,删除锁文件
unlink($flagFile);
