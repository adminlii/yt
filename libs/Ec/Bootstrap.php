<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * @var Zend_Log
     */
    protected $_logger;

    /**
     * @var Zend_Application_Module_Autoloader
     */
    protected $_resourceLoader;

    /**
     * @var Zend_Controller_Front
     */
    public $frontController;

    /**
     * ini config
     */
    protected function _initConfig()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', $this->getEnvironment());

        Zend_Registry::set('config', $config);
    }

    protected function _initLogging()
    {
        $logger = new Zend_Log();

        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/../data/log/Ec_Error.log");
        $logger->addWriter($writer);

        if ('production' == $this->getEnvironment()) {
            $filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
            $logger->addFilter($filter);
        }

        $this->_logger = $logger;
        Zend_Registry::set('log', $logger);
    }

    protected function _initDefaultController()
    {
        $this->bootstrap('frontController');
        $frontController = $this->getResource('frontController');
        $frontController->setParam('noViewRenderer', true);
    }

    /**
     * ini layout
     */
    public function _initLayout()
    {
        $layout = new Zend_Layout();
        $layout->setLayoutPath(APPLICATION_PATH . '/layouts');
        Zend_Registry::set('layout', $layout);
    }

    public function _initSmartyViews()
    {
        $config = Zend_Registry::get('config');
        $view = new Ec_View_Smarty($config->smartyConfig->template_dir, $config->smartyConfig->config);
        Zend_Registry::set('EcView', $view);
    }

    //初始化自定义动作助手
    public function _initActionHelpers()
    {
        //Zend_Controller_Action_HelperBroker::addPrefix('Ec_Controller_Action_Helpers');
    }

    protected function _initAutoModules()
    {
        $modules = new Ec_Loader_AutoModules();
        //var_dump($modules);
    }

    //控制器插件
    protected function _initControllerPlugin()
    {
        $front = Zend_Controller_Front::getInstance();
        $options = $this->getOption('resources');
        $defaultLanguage = isset($options['locale']['default'])?$options['locale']['default']:'zh_CN';
        $front->registerPlugin(new Ec_Controller_Plugins_Lang($defaultLanguage));
        $front->registerPlugin(new Ec_Controller_Plugins_Acl());
        // $front->registerPlugin(new Ec_Controller_Plugins_Test());
    }


    public function _initDb()
    {
        $ops = $this->getOptions(); 
        

        $dbparams = $ops['resources']['multidb']['db1'];
        
        $db = Zend_Db::factory('PDO_MYSQL', $dbparams);
        $db->query('set names utf8');
        //     $db->query('SET SESSION wait_timeout=65535');
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        $db->getConnection();
        Zend_Registry::set('db', $db);
        Zend_Registry::set('debug', $db->getProfiler()->getEnabled());
        
 
        Zend_Registry::set("wms_db", $ops['server']['wms_db']);
        
        
        Zend_Registry::set("dbprefix", $ops['server']['dbprefix']); 

        Zend_Registry::set('sql_query', '');
        Zend_Registry::set('sql_select', '');
        Zend_Registry::set('selectCount', 0);
        
        $title = '国际物流管理系统';
        $systemTitle = Service_Config::getByField('SYSTEM_TITLE', 'config_attribute');
        if($systemTitle){ 
        	$title = $systemTitle['config_value'];
        } 
        Zend_Registry::set('system_title',$title); 
        /*
         * 数据库连接参数
         */
        $dbConfig = $db->getConfig();
        $dbparams = array (
                'host' => $dbConfig['host'],
                'port' => $dbConfig['port'],
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
                'dbname' => $dbConfig['dbname'],
                'charset'=>$dbConfig['charset'],
//                 'isdefaulttableadapter'=>$dbConfig['isDefaultTableAdapter'],
        );
        
        Zend_Registry::set('dbparams', $dbparams);        
    }
    
    public function _initWarehouse ()
    {
        $ops = $this->getOptions();
//         Zend_Registry::set ( "wms", $ops['wms'] );
    }
    
    public function _initLang(){
        $request = new Zend_Controller_Request_Http();
        $langArr =  array('zh_CN', 'en_US');
        $language = $request->getParam('LANGUAGE', '');
        if (!empty($language)&&in_array($language, $langArr)) {
            setcookie('LANGUAGE', $language, time() + 6400, '/');
            $_COOKIE['LANGUAGE'] = $language;
        }
    }
}

/**
 * Ec Class static functions
 */
class Ec
{

    /**
     * getConfig
     */
    public static function getConfig($key)
    {
        return Zend_Registry::get('config')->get($key);
    }

    /**
     * rend smarty tpl
     */
    public static function renderTpl($tpl, $layoutTemplate = null, $title = null, $scripts = null, $meta = null)
    {
        $layout = Zend_Registry::get('layout');
/*        $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        if (null != $layoutTemplate && '' != $layoutTemplate) {
            $layoutTemplate = $module . '/' . $layoutTemplate;
            $layout->setLayout($layoutTemplate);
        }*/

        $layout->setLayout($layoutTemplate);

        if (is_array($scripts)) {
            foreach ($scripts as $script) {
                $layout->getView()
                    ->headScript()
                    ->appendFile(self::getConfig('baseUrl') . $script);
            }
        }

        $title = null == $title ? self::getConfig('webTitle') : $title;
        $layout->getView()->headTitle($title);

        if ($meta) {
            if (isset($meta['keyword'])) {
                $layout->getView()
                    ->headMeta()
                    ->appendName('keywords', trim($meta['keyword']));
            }
            if (isset($meta['description'])) {
                $layout->getView()
                    ->headMeta()
                    ->appendName('description', trim($meta['description']));
            }
        }
        if(!empty($tpl)){
            $layout->content = Zend_Registry::get('EcView')->render($tpl);
        }

        $layout->lang = Ec::getLang();
        $host =  $_SERVER['SERVER_NAME'];
        $host= preg_replace('/[^\.]+\.([^.]+)\.([^.]+)/', '$1.$2', $host);
        $layout->domain =$host;
			

        $tms_id = Service_User::getTmsId();
        $sql = "select * from web_newsconfig where tms_id='{$tms_id}' and news_type='LOGO';";
        $row = Common_Common::fetchRow($sql);
//         print_r($row);exit;
        $layout->logo = '/images/head/zjs_logo.png';
        if($row){
        	$layout->logo = $row['news_note'];
        }      
        $layout->system_title = Zend_Registry::get('system_title') ;  
        return $layout->render();
    }

    public static function setupMail()
    {
//         try {
//             $mail = Zend_Registry::get('mail');
//             if ($mail instanceof Zend_Mail)
//                 return $mail;
//         } catch (Exception $e) {

            $config = Zend_Registry::get('config');
            $mailConfig = array(
                'auth' => $config->mails->config->auth,
                'username' => $config->mails->config->username,
                'password' => $config->mails->config->password,
                'port' => $config->mails->config->port,
// 					'ssl' => $config->mails->config->ssl,
            );
            $transport = new Zend_Mail_Transport_Smtp($config->mails->server, $mailConfig);
            Zend_Mail::setDefaultTransport($transport);
            $mail = new Zend_Mail('utf-8');
            $mail->setFrom($config->mails->from,  $config->mails->config->name);
            Zend_Registry::set('mail', $mail);
            return $mail;
//         }
    }

    //输出所有sql查询
    public static function debug()
    {
        $isDebug = Zend_Registry::get('debug');
        if (!$isDebug) {
            return;
        }

        $dblog = "";
        $db = Zend_Registry::get('db');
        $profiler = $db->getProfiler();

        $totalTime = $profiler->getTotalElapsedSecs();
        $queryCount = $profiler->getTotalNumQueries();
        $longestTime = 0;
        $longestQuery = null;
        $allSelect = '';
        $selectCount = 0;
        if (false === $profiler->getQueryProfiles()) {
            return;
        }

        foreach ($profiler->getQueryProfiles() as $query) {

            $elapsed = $query->getElapsedSecs();

            $sql = strtr($query->getQuery(),
                array(
                    "\r\n" => ' ',
                    "\r" => ' ',
                    "\n" => ' ',
                    "\t" => ''
                ));
            $sql = trim($sql);
            $sql = $sql . ";";
            if (preg_match('/^SELECT/i', $sql)) {
                $allSelect .= "  " . $sql . "\n";
                $selectCount++;
            }

            if ($elapsed > $longestTime) {
                $longestTime = $elapsed;
                $longestQuery = $sql;
            }
            $dblog .= "$elapsed millisecond: -> \t " . $sql . "  \n";
        }

        $dblog .= 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "\n";
        $dblog .= 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "\n";
        $dblog .= 'Longest query length: ' . $longestTime . " seconds\n";
        $dblog .= "Longest query: " . $longestQuery . " \n";
        $dblog .= 'Queries per second: ' . $queryCount / $totalTime . "\n";
        $dblog .= "All Select:\n" . $allSelect . "";

        Zend_Registry::set('sql_query', $dblog);
        Zend_Registry::set('sql_select', $allSelect);
        Zend_Registry::set('selectCount', $selectCount);

        self::logError($dblog);
    }

    private static function logError($log)
    {
        $logger = new Zend_Log();
        $uploadDir = APPLICATION_PATH . "/../data/log/";
        $writer = new Zend_Log_Writer_Stream($uploadDir . 'Ec_query.log');
        $logger->addWriter($writer);
        $logger->info("\n" . date('Y-m-d H:i:s') . ":\n" . trim($log));
    }

    public static function cache($subDir = '', $directoryLevel = 0)
    {
        $config = Zend_Registry::get('config');
        $backend = $config->cacheBackend;

        if (empty($backend)) {
            $backend = 'File';
        }
        $frontendOptions = array(
            'caching' => $config->caching_Zend,
            'lifeTime' => $config->cacheLifeTime, // 设置缓存时间,如果不设置就永久缓存
            'automatic_serialization' => true
        );

        if ($backend == 'Memcached') {
            $backendOptions = array(
                'servers' => array(
                    array(
                        'host' => $config->memcache->host,
                        'port' => $config->memcache->port,
                        'persistent' => true
                    )
                ),
                'compression' => true,
            );
            // 取得一个Zend_Cache_Core 对象
            return Zend_Cache::factory('Core', 'Memcached', $frontendOptions, $backendOptions);
        } elseif ($backend == 'Sqlite') {
            $backendOptions = array(
                'cache_db_complete_path' => $config->sqliteFile,
                'automatic_vacuum_factor' => 10,
            );
            // 取得一个Zend_Cache_Core 对象
            return Zend_Cache::factory('Core', 'Sqlite', $frontendOptions, $backendOptions);
        } else {
            $uploadDir = $config->cacheDir;
            $uploadDir = !empty($uploadDir) ? $uploadDir : APPLICATION_PATH . "/../data/cache/";
            if (!empty($subDir)) {
                $uploadDir = preg_match('/\/$/', $uploadDir) ? $uploadDir . $subDir . "/" : $uploadDir . "/" . $subDir . "/";
            }

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777);
            }
            $backendOptions = array(
                'file_name_prefix' => 'Ec',
                'cache_dir' => $uploadDir,
                'hashed_directory_level' => $directoryLevel,
            );
            // 取得一个Zend_Cache_Core 对象
            return Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        }

    }

    public static function Lang($str,$param=null, $lang = null)
    {
        return Ec_Lang::getInstance()->translate($str,$param, $lang);
    }

    /**
     * @用于返回当前语言
     * @param bool $short
     * @return string
     */
    public static function getLang($short = false)
    {
        /* $langArray = array(
            'zh_CN', 'en_US'
        );*/
        $language = isset($_COOKIE['LANGUAGE']) ? $_COOKIE['LANGUAGE'] : 'zh_CN';
      //  $language = in_array($language, $langArray) ? $language : 'zh_CN';

        if ($short) {
            if ($language == 'zh_CN') {
                $language = '';
            } else {
                $language = '_en';
            }
        }
        return $language;
    }

    public static function showError($log, $time = '')
    {
        try {
            $logger = new Zend_Log();
            $uploadDir = APPLICATION_PATH . "/../data/log/";
            $writer = new Zend_Log_Writer_Stream($uploadDir . $time . '_err.log');
            $logger->addWriter($writer);
            $logger->info("\n" . date('Y-m-d H:i:s') . ":\n" . trim($log));
        } catch (Exception $e) {

        }
    }
    
    public static function getDb2(){
    	$server = new Zend_Config_Ini(APPLICATION_PATH . '/../application/configs/db.ini', 'production');
    	$params = $server->get('resources')->get('multidb')->get('db2')->toArray();
    	$db = Zend_Db::factory('PDO_MYSQL', $params);
    	$db->query('set names utf8');
    	return $db;
    }
    
    public static function getDb3(){
    	$server = new Zend_Config_Ini(APPLICATION_PATH . '/../application/configs/db.ini', 'production');
    	$params = $server->get('resources')->get('multidb')->get('db3')->toArray();
    	$db = Zend_Db::factory('PDO_MYSQL', $params);
    	$db->query('set names utf8');
    	return $db;
    }
}