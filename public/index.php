<?php

// date_default_timezone_set('Asia/Shanghai');//配置地区
// echo date('Y-m-d H:i:s',strtotime('Feb 21, 2014 12:35:55 PST'))."\n";exit;
// date_default_timezone_set('UTC +8');//配置地区
// echo date('Y-m-d H:i:s',strtotime('2014-02-21T04:11:07.780Z'))."\n";
// echo date('Y-m-d H:i:s',strtotime('2014-02-21T04:11:07.619Z'))."\n";
// exit;
// echo sprintf('%04d',10);exit;
/**
 * 传入日期格式 返回下次账单日
 * @param unknown_type $currentBillingDate
 * @return boolean|s    tring
 */
/* function getNextBillingDate($currentBillingDate){
    if(!preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$currentBillingDate,$match)){
        return false;
    }
    $y = $match[1];
    $m = $match[2];
    $d = $match[3];
    $t = date('t', strtotime("{$y}-{$m}-01"));
    $d = $d<$t?$d:$t;
    
    $nextMonthFirstDay = date('Y-m-d', strtotime('next month', strtotime($y . '-' . $m . '-01'))); // 下月第一天
    $nextMonth = date('Y-m', strtotime($nextMonthFirstDay)); // 下月第一天
    $nextMonthLastDay = date('t', strtotime($nextMonthFirstDay)); // 下月最后一天
    
    $d = $d < $nextMonthLastDay ? $d : $nextMonthLastDay;
    return $nextMonth . '-' . $d;
}
echo getNextBillingDate('2013-3-31');exit;
echo date('Y-m-d H:i:s',strtotime('+1 month',strtotime('2013-12-29')));exit; */
// echo '<br/>';
// echo date('Y-m-d H:i:s',strtotime('-3 month'));
// exit;
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return $usec + $sec;
} 
$start_t = microtime_float();

error_reporting(0);//开启错误报告
//error_reporting(8191);//开启错误报告
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set('Asia/Shanghai');//配置地区
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
//定义数据过滤方法    
defined('FILTER_METHOD')
    || define('FILTER_METHOD','htmlspecialchars');
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../libs'),
	realpath(APPLICATION_PATH . '/platform'),
	realpath(APPLICATION_PATH . '/models'),
	realpath(APPLICATION_PATH . '/modules'),
	//配置亚马逊libs的绝对路径
	realpath(APPLICATION_PATH . '/models/Amazon'),
	APPLICATION_PATH,
    get_include_path(),
)));
require_once APPLICATION_PATH . '/../libs/MyPdf.php';
require_once 'function_common.php'; 
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/server.ini'
);

$application->bootstrap();
//$application->getBootstrap()->getResource("frontController")->setParam('useDefaultControllerAlways', true);
$application->run();
