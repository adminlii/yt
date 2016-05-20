<?php

class Ec_Controller_Plugins_Lang extends Zend_Controller_Plugin_Abstract
{
    protected $_lang = '';

    public function __construct($lang = '')
    {
        $this->_lang = $lang;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $thisLanguage = isset($_COOKIE['LANGUAGE']) && !empty($_COOKIE['LANGUAGE']) && in_array($_COOKIE['LANGUAGE'], array('zh_CN', 'en_US')) ? $_COOKIE['LANGUAGE'] : $this->_lang;
        $language = $request->getParam('LANGUAGE', '');
        if ((!empty($language) && in_array($language, array('zh_CN', 'en_US')) && $language != $thisLanguage)) {
            setcookie('LANGUAGE', $language, time() + 6400, '/');
        }
        if (!isset($_COOKIE['LANGUAGE']) || !in_array($_COOKIE['LANGUAGE'], array('zh_CN', 'en_US'))) {
            setcookie('LANGUAGE', $thisLanguage, time() + 6400, '/');
        }
    }

}