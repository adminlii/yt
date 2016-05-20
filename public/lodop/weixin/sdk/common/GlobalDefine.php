<?php 
if(!defined('IN_SYS')) {
	die('Access Denied');
}

define("WEIXIN_TOKEN", "ruston2014");	//填写你的token
/****说明：学习、测试过程中，对于新手，建议一直打开DEBUG和WEIXIN_VALID，有经验者在首次验证成功后，可以关闭WEIXIN_VALID。****/
define('DEBUG', 'true');			//屏蔽本行将关闭DEBUG，导致两个结果：1，关闭日志功能；2.关闭系统自检。在非生产环境中，请一直打开DEBUG，生产环境中，请关闭DEBUG。
define('WEIXIN_VALID', 'true');		//在微信管理界面配置接口信息的url和token时，不能屏蔽本行。通过验证后，在非生产环境中，可以屏蔽本行，不再进行微信消息来源验证。生产环境中，请不要屏蔽本行。如果打开了WEIXIN_VALID，并且打开了DEBUG，用浏览器打开http://你的域名/sdk/page.php?action=check_host，会进行系统自检。
/**************************************************************************************************************/

(DEBUG == 'true')?error_reporting(7):error_reporting(0);
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);	//程序目录
define('LOG_TYPE', 'EVERYTIME_WRITE');//EVERYTIME_WRITE：每次交互都生成一个新的日志文件，用于研究交互的细节，文件名精确到毫秒，会生成很多日志文件；DAY_WRITE：只记录最近一次交互的日志，文件名为当天日期，一天只有一个日志文件，并且文件很小；DAY_APPEND：追加当天所有的交互日志在一个文件中，文件名为当天日期，一天只有一个日志文件，但文件会很大。

$GLOBALS['APP_INFO'] = array(
	'YUYU' => array(
		'appId' => 'wx016aca45692ecfa9',					//填写你的appId
		'appSecret' => '11d21a896250f22402bb5fc3ffe89c7b',	//填写你的appSecret
	)
);

define('SUBSCRIBE_WELCOME', '欢迎来到鱼渔微信，我们的网址是www.yidongapi.com。');
?>
