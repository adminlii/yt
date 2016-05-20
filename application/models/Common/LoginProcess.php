<?php
class Common_LoginProcess
{

    public static function getSystemSync()
    {
        $config = Zend_Registry::get('log-sync');
        $config = $config->toArray();
//         var_dump($config);exit;
        return $config;
    }

    public static function getEbLogout()
    {
        $config = self::getSystemSync();
        return $config['wms']['url']['logout'];
    }

    public static function getEbLogin()
    {
        $config = self::getSystemSync();
        return $config['wms']['url']['login'];
    }
}