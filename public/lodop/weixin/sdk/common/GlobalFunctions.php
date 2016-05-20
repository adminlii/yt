<?php
if(!defined('IN_SYS')) {
	die('Access Denied');
}

/**
 * 日志函数的入口
 * @param string $logMessage 日志内容
 */
function miniLog($logMessage)
{
	if (DEBUG != 'true') return;
	if (is_array($logMessage)) $logMessage = print_r($logMessage,true);
	if (strpos($logMessage, '<xml>') === 0){
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($logMessage);
		$dom->formatOutput = TRUE;
		$logMessage =  $dom->saveXml();
		$logMessage = ltrim($logMessage, '<?xml version="1.0"?>');		
	}

	$st = debug_backtrace();

	$function = ''; //调用yuyuLog的函数名
	$file = '';     //调用yuyuLog的文件名
	$line = '';     //调用yuyuLog的行号
	foreach($st as $item) {
		if($file) {
			$function = $item['function'];
			break;
		}
		if($item['function'] == 'yuyuLog') {//单独调用
			if (strlen($item['file'])>0){
				$file = $item['file'];
				$line = $item['line'];
			}
		}
		if($item['function'] == 'log') {//通过Wechat类调用
			if (strlen($item['file'])>0){
				$file = $item['file'];
				$line = $item['line'];
			}
		}
	}

	$function = $function ? $function : 'main';

	//为了缩短日志的输出，file只取最后一截文件名
	$file = explode(DIRECTORY_SEPARATOR, rtrim($file, DIRECTORY_SEPARATOR));
	$file = $file[count($file)-2].DIRECTORY_SEPARATOR.$file[count($file)-1];
	$secondLine = "[$file] [$line] [$function]";
	MiniLog::instance(S_ROOT . "data/log/")->log("minilog_", $secondLine . "\n" . $logMessage);
}

/**
 * 接口层日志函数
 */
function yuyuLog($logMessage = "no error msg")
{
	miniLog($logMessage);
}

//获取当前时间，毫秒级别,如果startTime传入，则计算当前时间与startTime的时间差
function getMillisecond($startTime = false) {
	$endTime = microtime(true) * 1000;

	if($startTime !== false) {
		$consumed = $endTime - $startTime;
		return round($consumed);
	}
	return $endTime;
}

function getIp()
{
	if (isset($_SERVER)){
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")){
			$realip = getenv("HTTP_X_FORWARDED_FOR");
		} else if (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
	}

	return $realip;
}
?>