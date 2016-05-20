<?php
set_time_limit(0);
ini_set('max_execution_time', '72000');
ini_set('memory_limit', '2048M');
// error_reporting ( E_ERROR | E_WARNING | E_PARSE );
error_reporting(0);
date_default_timezone_set('Asia/Shanghai'); // 配置地区
define('APPLICATION_PATH', dirname(dirname(__FILE__)) . '/application');
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../libs'),
    realpath(APPLICATION_PATH . '/models'),
    realpath(APPLICATION_PATH . '/modules'),
    // 配置亚马逊libs的绝对路径
    realpath(APPLICATION_PATH . '/models/Amazon'),
    APPLICATION_PATH,
    get_include_path()
)));

require_once ('function_common.php');
// echo get_include_path();exit;
require_once ('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

require_once ('Ec/Bootstrap.php');

Zend_Session::start();
$config = new Zend_Config_Ini(dirname(dirname(__FILE__)) . '/application/configs/config.ini', 'production');
$server = new Zend_Config_Ini(dirname(dirname(__FILE__)) . '/application/configs/db.ini', 'production');
Zend_Registry::set('config', $config);


// print_r($server->toArray());exit;
$serverArr = $server->toArray();
Zend_Registry::set("wms_db", $serverArr['server']['wms_db']);
Zend_Registry::set('dbprefix', $serverArr['server']['dbprefix']);

//自动审单-------------------------------------------------
Zend_Registry::set('auto_allot',true);
/**
 * 与自动加载起冲突了，原因未知
 */
$params = array(
    // 'persistent'=>true,//持久链接
    'host' => $server->resources->multidb->db1->host,
    'port' => $server->resources->multidb->db1->port,
    'username' => $server->resources->multidb->db1->username,
    'password' => $server->resources->multidb->db1->password,
    'dbname' => $server->resources->multidb->db1->dbname,
    'charset' => $server->resources->multidb->db1->charset,
    'profiler' => $server->resources->multidb->db1->profiler,
    'isdefaulttableadapter' => $server->resources->multidb->db1->isdefaulttableadapter,
);
// require_once 'Common/Service.php';
// print_r($params);exit;

Zend_Registry::set('dbparams', $params);

/**
 * 初始化数据库连接
 */
function initDb()
{
    $params = Zend_Registry::get('dbparams');
    $db = Zend_Db::factory('PDO_MYSQL', $params);
    $db->query('set names utf8');
//     $db->query('SET SESSION wait_timeout=65535');    
    Zend_Db_Table_Abstract::setDefaultAdapter($db);
    $db->getConnection();
    Zend_Registry::set('db', $db);
    Zend_Registry::set('debug', $db->getProfiler()->getEnabled());
}

/**
 * 检测数据库是否连接
 */
function ping()
{
    $db = Zend_Registry::get('db');
    if(! $db->isConnected()){
        initDb();
        // 日志
        $db = Zend_Registry::get('db');
        $log = '数据库连接丢失，重新连接,连接后状态：' . $db->isConnected();
        echo $log;
        Ec::showError($log, 'db_reconnect_');
    }
}

/**
 * 自动运行日志
 *
 * @param unknown_type $name            
 */
function autoLog($name = 'auto')
{
    
    $name = preg_replace('/(\.php)$/', '', $name);
    @file_put_contents(APPLICATION_PATH . '/../data/log/auto' . date('_Y-m-d') . '.txt', date('Y-m-d H:i:s') . " {$name}[php]" . "\n\n", FILE_APPEND);
    
    /*
     * 自动运行 记录日志表 start
     */
    $db = Zend_Registry::get('db');
    $table = 'cron_auto_log';
    $sqls = array();
    $sqls[] = "
    CREATE TABLE if not exists `{$table}` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `add_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
    `add_date` date NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加日期',
    PRIMARY KEY (`id`)
    ) COMMENT='下载n天至昨天的数据，任务表';
    ";
    $sqls[] = "insert into {$table}(name,add_datetime,add_date) values('{$name}',now(),'".date('Y-m-d')."');";
    
    foreach($sqls as $sql){
        $db->query($sql);
    }
    /*
     * 自动运行 记录日志表 end
     */
}

/**
 * 后台任务开始 传入的是当前任务文件名
 *
 * @param unknown_type $fileName            
 */
function sapiStart($fileName, $mult = true)
{
    $fileName = preg_replace('/(\.php)$/', '', $fileName);
    $flagFile = APPLICATION_PATH . "/../data/log/{$fileName}";
    
    if(file_exists($flagFile)){
        echo "The {$fileName}[php] running\r\n";
        if(!$mult){//是否多线程运行
            exit;
        }        
    }
    file_put_contents($flagFile, now());
    
    echo "[" . now() . "]{$fileName}[php] Starting!\n";
}

/**
 * 后台任务结束 传入的是当前任务文件名
 *
 * @param unknown_type $fileName            
 */
function sapiEnd($fileName)
{
    $fileName = preg_replace('/(\.php)$/', '', $fileName);
    $flagFile = APPLICATION_PATH . "/../data/log/{$fileName}";
    echo "[" . now() . "]{$fileName} End run\r\n";
    @unlink($flagFile);
}

initDb();

Zend_Registry::set ( 'SAPI_DEBUG', false );// 是否允许SAPI_DEBUG
$my_argv = array();
foreach ( $argv as $k => $v ) {
	if (strtolower ( $v ) === 'sapi') {
		Zend_Registry::set ( 'SAPI_DEBUG', true );
		unset ( $argv [$k] );
	}else{
	    $my_argv[] = $v;
	}
}
$argv = $my_argv;
//sort ( $argv );
// 传入的参数
$account = isset($argv[1]) ? $argv[1] : '';
$company_code = isset($argv[2]) ? $argv[2] : '';
//debug


//初始化定时任务
Common_RunControl::initAccount();
?>