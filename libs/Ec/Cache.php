<?php
/**
 * 缓存类，实现数据，输出缓存
 * @copyright version 0.1
 */

class Ec_Cache
{

    private static $_instance;
    protected $_cacheId = null;

    const CLEANING_MODE_ALL  = 'all';
    const CLEANING_MODE_OLD = 'old';

    protected $_options = array(
            'cache_dir' => null, // 数据缓存目录
            'life_time' => 7200, // 缓存时间
            'page_dir' => null, // 文本缓存目录
            'cache_prefix' => 'cache_'        //缓存前缀
    );

    private function __construct(){}
   
    //创建__clone方法防止对象被复制克隆
    private function __clone(){}
   
    /**
     * 取缓存对象，如果存在直接返回，如果不存在实例化本身
     * @return object cache
     */
    public static function getInstance(){
       
        if(! self::$_instance){
       
            self::$_instance = new self();
        }
       
        return self::$_instance;
    }
       
    /**
     * 设置缓存参数集
     * @param array $options 要设置的缓存参数集
     */
    public function setOptions($options = array()){
   
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
    }
   
    /**
     * 取得当前缓存参数，如果$name为空返回全部参数，否则返回该参数值
     * @param string $name 要返回的参数名称
     * @return string or array $option;
     */
    public function getOption($name = null){
   
        if(null === $name)
            return $this->_options;
   
        if (!is_string($name)) {
            throwException("不正确的参数名称 : $name");
        }
       
        if (array_key_exists($name, $this->_options)){
            return $this->_options[$name];
        }
    }
   
    /**
     * 设置缓存参数
     * @param array $options 要设置的缓存参数
     */
    protected function setOption($name, $value){
   
        if (!is_string($name)) {
            throwException("不正确的参数名称 : $name");
        }
        $name = strtolower($name);
        if (array_key_exists($name, $this->getOption())){
            $this->_options[$name] = $value;
        }
       
        if ($this->_options['cache_dir'] === null) {
            $this->setOption('cache_dir', $this->getTmpDir() . DIRECTORY_SEPARATOR);
        }
       
        if ($this->_options['page_dir'] === null) {
            $this->setOption('page_dir', $this->getTmpDir() . DIRECTORY_SEPARATOR);
        }
    }
   
    /**
     * 读取数据缓存，如果不存在或过期，返回false
     * @param string $id 缓存ID
     * @return false or data
     */
    public function load($id){

        $this->_cacheId = $id;       
        $file = $this->getOption('cache_dir') . $this->getOption('cache_prefix') . $this->_cacheId;
               
        if (@filemtime($file) >= time()){
       
            return unserialize(file_get_contents($file)); 
        } else {
            @unlink($file);
            return false;
        }
    }
   
    /**
     * 保存数据缓存，并设置缓存过期时间
     * @param array or string $data 要缓存的数据
     * @param int $lifeTime 缓存过期时间
     */
    public function save($data, $lifeTime = null){
   
        if(null !== $lifeTime)
            $this->setOption('life_time', $lifeTime);
   
        $file = $this->getOption('cache_dir') . $this->getOption('cache_prefix') . $this->_cacheId;
        $data = serialize($data);
        @file_put_contents($file, $data);
        @chmod($file, 0777);
        @touch($file, time() + $this->getOption('life_time'));
    }   
   
    /**
     * 读取输出缓存，如果不存在或缓存过期将重新开启输出缓存
     * @param string $id 缓存ID
     */
    public function start($id){

        $this->_cacheId = $id;
        $file = $this->getOption('page_dir') . $this->getOption('cache_prefix') . $this->_cacheId;
               
        if (@filemtime($file) >= time()){
       
            return file_get_contents($file);
        } else {
            @unlink($file);
            ob_start();
            return false;
        }
    }

    /**
     * 删除指定ID缓存
     * @param string $id 缓存ID
     */
    public function remove($id){

        $this->_cacheId = $id;
        //删除附合条件的数据缓存
        $file = $this->getOption('cache_dir') . $this->getOption('cache_prefix') . $this->_cacheId;
        @unlink($file);
        //删除附合条件的输出缓存
        $file = $this->getOption('page_dir') . $this->getOption('cache_prefix') . $this->_cacheId;
        @unlink($file);
    }
   
    /**
     * 保存输出缓存，并设置缓存过期时间
     * @param int $lifeTime 缓存过期时间
     */
    public function end($lifeTime = null){

        if(null !== $lifeTime)
            $this->setOption('life_time', $lifeTime);
   
        $file = $this->getOption('page_dir') . $this->getOption('cache_prefix') . $this->_cacheId;
        $data = ob_get_contents();
        ob_end_clean();
        @file_put_contents($file, $data);
        @chmod($file, 0777);
        @touch($file, time() + $this->getOption('life_time'));
    }
   
    /**
     * 根据参数清除相应缓存
     * @param string $mode 缓存类型，包括(CLEANING_MODE_ALL:所有缓存, CLEANING_MODE_OLD: 过期缓存)
     */
    public function clear($mode = CLEANING_MODE_OLD){
   
        $dirs = array('cache_dir', 'page_dir');
        foreach($dirs as $value){
            if(null != $this->getOption($value)){
                $files = scandir($this->getOption($value));
                switch ($mode) {

                    case CLEANING_MODE_ALL:
                    default:
                        foreach ($files as $val){
                            @unlink($this->getOption($value) . $val);
                        }
                        break;

                    case CLEANING_MODE_OLD:
                    default:
                        foreach ($files as $val){
                            if (filemtime($this->getOption($value) . $val) < time()){ 
                                @unlink($this->getOption($value) . $val); 
                            }
                        }
                        break;
                }
            }
        }
    }
   
    /**
     * 取临时文件夹为缓存文件夹
     * @return $dir 临时文件夹路径
     */
    public function getTmpDir(){
   
        $tmpdir = array();
        foreach (array($_ENV, $_SERVER) as $tab) {
            foreach (array('TMPDIR', 'TEMP', 'TMP', 'windir', 'SystemRoot') as $key) {
                if (isset($tab[$key])) {
                    if (($key == 'windir') or ($key == 'SystemRoot')) {
                        $dir = realpath($tab[$key] . '\\temp');
                    } else {
                        $dir = realpath($tab[$key]);
                    }
                    if ($this->_isGoodTmpDir($dir)) {
                        return $dir;
                    }
                }
            }
        }
        $upload = ini_get('upload_tmp_dir');
        if ($upload) {
            $dir = realpath($upload);
            if ($this->_isGoodTmpDir($dir)) {
                return $dir;
            }
        }
        if (function_exists('sys_get_temp_dir')) {
            $dir = sys_get_temp_dir();
            if ($this->_isGoodTmpDir($dir)) {
                return $dir;
            }
        }
        //通过尝试创建一个临时文件来检测
        $tempFile = tempnam(md5(uniqid(rand(), TRUE)), '');
        if ($tempFile) {
            $dir = realpath(dirname($tempFile));
            unlink($tempFile);
            if ($this->_isGoodTmpDir($dir)) {
                return $dir;
            }
        }
        if ($this->_isGoodTmpDir('/tmp')) {
            return '/tmp';
        }
        if ($this->_isGoodTmpDir('\\temp')) {
            return '\\temp';
        }
        throw new Exception('无法确定临时目录，请手动指定cache_dir', E_USER_ERROR);
    }

    /**
     * 验证给定的临时目录是可读和可写的
     *
     * @param string $dir 临时文件夹路径
     * @return boolean true or false 临时文件夹路径是否可读写
     */
    protected function _isGoodTmpDir($dir){
   
        if (is_readable($dir)) {
            if (is_writable($dir)) {
                return true;
            }
        }
        return false;
    }


}//endclass