<?php
/**
 * 字符串替换
 * @param string $str
 * @param string|array $arg
 * @return string
 */
function gettext_strarg($str,$arg)
{
    $tr = array();
    $p = 0;
    if(isset($arg)){
        if(is_array($arg)){
            foreach($arg as $aarg){
                $tr['%' . ++ $p] = $aarg;
            }
        }else{
            $tr['%' . ++ $p] = $arg;
        }
        $str = strtr($str, $tr);
    }
    
    return $str;
}

class Ec_Lang
{

    private $_myTranslate;
    private $_customTranslate;
    public $_defaultLang = 'zh_CN';
    private static $_class = null;
    private $_curLang = null;
    public $_languages = array(
        'zh_CN' => 'zh_CN',
        'en_US' => 'en_US',
    );

    private function __construct()
    {
        $this->getCurrentLanguage();
        $this->loadLanguage();
    }

    public static function getInstance()
    {
        if (!isset(self::$_class)) {
            $c = __CLASS__;
            self::$_class = new $c;
        }
        return self::$_class;

    }

    public function getTranslate($str, $lang = null)
    {
        $translation = $str;
        if ($str != "" && null != $lang && $lang != $this->_curLang && isset($this->_languages[$lang])) {
            if ($this->_customTranslate != null) {
                $translation = $this->_customTranslate->translate($str);
            } else {
                try {
                    $this->_customTranslate = new Zend_Translate('array', APPLICATION_PATH . "/languages/" . $lang . '.php');
                    $translation = $this->_customTranslate->translate($str);
                } catch (Exception $e) {
                }
            }
        } elseif ($str != "") {
            if ($this->_myTranslate != null) {
                $translation = $this->_myTranslate->translate($str);
            }
        }

        return $translation;
    }

    public function translate($str,$param=null, $lang = null)
    {
        $str =  $this->getTranslate($str, $lang);
        
        return gettext_strarg($str,$param);
    }
    

    private function loadLanguage()
    {
        $noAppMoFile = false;
        try {
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $this->_myTranslate = Zend_Registry::get('Zend_Translate');
            } else {
                $this->_myTranslate = new Zend_Translate('array', APPLICATION_PATH . "/languages/" . $this->_curLang . '.php');
                Zend_Registry::set('Zend_Translate', $this->_myTranslate);
            }
        } catch (Exception $e) {
            $noAppMoFile = $e;
        }
        return $noAppMoFile;
    }


    public function getCurrentLanguage()
    {
        $user = new Zend_Session_Namespace('userAuthorization');
        if ($this->_curLang != null && $this->_curLang == $user->lang)
            return $this->_curLang;

        $currentLanguage = "";

        if (isset($user->lang)) {
            $currentLanguage = $user->lang;
        } else if (isset($_COOKIE['LANGUAGE'])) {
            $currentLanguage = $_COOKIE['LANGUAGE'];
        }

        if (!isset($this->_languages[$currentLanguage])) {
            $currentLanguage = $this->_defaultLang;
        }
        $this->_curLang = $currentLanguage;
        return $currentLanguage;
    }
    /**
     * 调用示例
     */
    public static function demo(){
        //指定语言
        echo Ec::Lang('test',array('dd'=>'d','cc'),'zh_CN');
        //默然语言
        echo Ec::Lang('test',array('dd'=>'d','cc'));
    }
}