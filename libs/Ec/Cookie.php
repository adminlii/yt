<?php

/**
 * 类名：Ec_Cookie.php
 * 功能：Cookie常用操作
 */
class Ec_Cookie
{

    private static $PREFIX = '';

    public static function setPrefix ($a = '')
    {
        if ($a != '') {
            self::$PREFIX = $a;
        }
    }
    // 判断Cookie是否存在
    public static function is_set ($name)
    {
        return isset($_COOKIE[self::$PREFIX . $name]);
    }
    
    // 获取某个Cookie值
    public static function get ($name)
    {
        $exists = self::is_set($name);
        if ($exists) {
            $value = $_COOKIE[self::$PREFIX . $name];
            $value = base64_decode(unserialize($value));
            return $value;
        } else {
            return null;
        }
    }
    
    // 设置某个Cookie值
    public static function set ($name, $value, $expire = '', $path = '', $domain = '')
    {
        if ($expire == '') {
            $expire = '';
        }
        if (empty($path)) {
            $path = "";
        }
        if (empty($domain)) {
            $domain = "";
        }
        $expire = ! empty($expire) ? time() + $expire : 0;
        $value = base64_encode(serialize($value));
        setcookie(self::$PREFIX . $name, $value, $expire, $path, $domain);
        $_COOKIE[self::$PREFIX . $name] = $value;
    }
    
    // 删除某个Cookie值
    public static function delete ($name)
    {
        self::set($name, '', time() - 3600);
        if (isset($_COOKIE[self::$PREFIX . $name])) {
            unset($_COOKIE[self::$PREFIX . $name]);
        }
    }
    
    // 清空Cookie值
    public static function clear ()
    {
        unset($_COOKIE);
    }
}
?>