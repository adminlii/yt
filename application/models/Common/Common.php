<?php
class Common_Common
{

    /**
     * @return mixed
     */
    public static function getIP()
    {
        if (@$_SERVER['HTTP_CLIENT_IP'] && @$_SERVER['HTTP_CLIENT_IP'] != 'unknown') {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (@$_SERVER['HTTP_X_FORWARDED_FOR'] && @$_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown') {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 获取服务器IP
     * @return string
     */
    public static function getRealIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    /**
     * 产生随机字符
     * @param $length
     * @param int $numeric
     * @return string
     */
    public static function random($length, $numeric = 0)
    {
        PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
        $seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[mt_rand(0, $max)];
        }
        return $hash;
    }
    
    public static function getfname($url)
    {
        // 返回$url最后出现"/"的位置
        $pos = strrpos($url, "/");
        $pos = $pos ? $pos : strrpos($url, "\\");
        $pos = $pos ? $pos : 0;
        if($pos == false){
            $pos = - 1;
        }
        // 取得$url长度
        $len = strlen($url);
        if($len < $pos){
            return false;
        }else{
            // substr截取指定位置指定长度的子字符串
            $filename = substr($url, $pos + 1, $len - $pos - 1);
            return $filename;
        }
    }
    /**
     * 文件下载
     */
    public static function downloadFile($file)
    {
        // if (!is_file($file)) { die("<b>404 File not found!</b>"); }
        // Gather relevent info about file
        $len = filesize($file);
        $filename = self::getfname($file);
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));
        // This will set the Content-Type to the appropriate setting for the file
        switch ($file_extension) {
            case "pdf":
                $ctype = "application/pdf";
                break;
            case "exe":
                $ctype = "application/octet-stream";
                break;
            case "zip":
                $ctype = "application/zip";
                break;
            case "doc":
                $ctype = "application/msword";
                break;
            case "xls":
                $ctype = "application/vnd.ms-excel";
                break;
            case "ppt":
                $ctype = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            case "mp3":
                $ctype = "audio/mpeg";
                break;
            case "wav":
                $ctype = "audio/x-wav";
                break;
            case "mpeg":
            case "mpg":
            case "mpe":
                $ctype = "video/mpeg";
                break;
            case "mov":
                $ctype = "video/quicktime";
                break;
            case "avi":
                $ctype = "video/x-msvideo";
                break;
            case "txt":
                $ctype = "text/plain";
                break;
            // The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
            case "php":
            case "htm":
            case "html":
                //  case "txt":
                die("<b>Cannot be used for " . $file_extension . " files!</b>");
                break;
            default:
                $ctype = "application/force-download";
        }
        ob_end_clean();
        // Begin writing headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        // Use the switch-generated Content-Type
        header("Content-Type: $ctype");
        // Force the download
        $header = "Content-Disposition: attachment; filename=" . $filename . ";";
        header($header);
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $len);
        @readfile($file);
        exit();
    }


    /**
     * 对象转数组
     * @param $obj
     * @return mixed
     */
    public static function objectToArray($obj)
    {
        $arr='';
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        if (is_array($_arr)) {
            foreach ($_arr as $key => $val) {
                $val = (is_array($val) || is_object($val)) ? self::objectToArray($val) : $val;
                $arr[$key] = $val;
            }
        }
        return $arr;
    }

    /*
     * 页面延迟跳转，并提示
     */
    public static function redirect($url, $msg)
    {
        $second = 3;
        $millisecond = $second * 1000;
        // 用html方法实现页面延迟跳转
        echo "<html>\n";
        echo "<head>\n";
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<meta http-equiv='refresh' content='{$second};url=" . $url . "'>\n";
        echo "</head>\n";
        echo "<body style='text-align:center;padding-top:100px;line-height:25px;'>\n";
        echo $msg . "</br>\n";
        echo "页面将在{$second}秒后自动跳转...</br>\n";
        echo "<a href='" . $url . "'>如果没有跳转，请点这里跳转</a>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit();

        // 用js方法实现页面延迟跳转
        echo $msg . "</br>";
        echo "页面将在3秒后自动跳转...</br>";
        echo "<a href='" . $url . "'>如果没有跳转，请点这里跳转</a>";
        echo "<script language='javascript'>setTimeout(\"window.location.href='" . $url . "'\",{$millisecond})</script>";
        exit();
    }

    /**
     * 字符串截取
     * @param $str
     * @param $len
     * @return string
     */
    public static function utf_substr($str, $len)
    {
        for ($i = 0; $i < $len; $i++) {
            $temp_str = substr($str, 0, 1);
            if (ord($temp_str) > 127) {
                $i++;
                if ($i < $len) {
                    $new_str[] = substr($str, 0, 3);
                    $str = substr($str, 3);
                }
            } else {
                $new_str[] = substr($str, 0, 1);
                $str = substr($str, 1);
            }
        }
        return join('', $new_str);
    }

    //清除wsdl缓存文件
    public static function clearWsdlTmp()
    {
        $dir = ini_get('soap.wsdl_cache_dir'); // 查找跟目录下file文件夹中的文件
        if (is_dir($dir)) {
            if ($dir_handle = opendir($dir)) {
                while (false !== ($file_name = readdir($dir_handle))) {
                    if ($file_name == '.' or $file_name == '..') {
                        continue;
                    } else {
                        //     					echo $file_name."\n";
                        if (preg_match('/^(wsdl).*/', $file_name)) {

                            @unlink($dir . "/" . $file_name);

                        }

                    }
                }
            }
            return true;
        }
        return false;
    }

    //清除wsdl缓存文件
    public static function clearWsdlCacheFile()
    {
        $dir = APPLICATION_PATH . '/../data/cache';
        if (is_dir($dir)) {
            if ($dir_handle = opendir($dir)) {
                while (false !== ($file_name = readdir($dir_handle))) {
                    if ($file_name == '.' or $file_name == '..') {
                        continue;
                    } else {
                        //     					echo $file_name."\n";
                        if (preg_match('/.*(wsdl)$/', $file_name)) {

                            @unlink($dir . "/" . $file_name);

                        }

                    }
                }
            }
            return true;
        }
        return false;
    }

    //清除超时图片缓存文件
    public static function clearImageTempFile()
    {
        $dir = APPLICATION_PATH . '/../data/images/temp';
        if (is_dir($dir)) {
            if ($dir_handle = opendir($dir)) {
                while (false !== ($file_name = readdir($dir_handle))) {
                    if ($file_name == '.' or $file_name == '..') {
                        continue;
                    } else {
                        // echo $file_name."\n";
                        $a = fileatime($dir . "/" . $file_name);
                        if (time() - $a > 3600) {
                            @unlink($dir . "/" . $file_name);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 判断是不是soap数组
     * @param array $input
     * @return boolean
     */
    public static function isSoapArray($input)
    {
        if (!is_array($input)) {
            return false;
        }
        $keys = array_keys($input);
        $isInt = true;
        foreach ($keys as $k) {
            if (!is_int($k)) {
                $isInt = false;
            }
        }
        return $isInt;
    }


    /**
     * 判断如果为一维数据转为多维
     * @param $arr
     * @return array
     */
    public static function multiArr($arr)
    {
        $return = array();
        $isMulti = false;
        foreach ($arr as $k => $v) {
            if (is_int($k)) {
                $isMulti = true;
            }
        }
        if (!$isMulti) {
            $return[] = $arr;
        } else {
            $return = $arr;
        }
        return $return;
    }

    /**
     * @param $url
     * @param int $timeout
     * @param array $header
     * @return mixed
     * @throws Exception
     */
    public static function http_request($url, $timeout = 300, $header = array())
    {
        if (!function_exists('curl_init')) {
            throw new Exception('server not install curl');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $data = curl_exec($ch);
        list ($header, $data) = explode("\r\n\r\n", $data);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 301 || $http_code == 302) {
            $matches = array();
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = trim(array_pop($matches));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
        }

        if ($data == false) {
            curl_close($ch);
        }
        @curl_close($ch);
        return $data;
    }

    public static function zip($fileName = '')
    {
        require_once 'archive.php';
        $zip = new zip_file($fileName);
        $zip->set_options(array('inmemory' => 1, 'recurse' => 0, 'storepaths' => 0));
        return $zip;
    }

    /**
     *过滤安全字符
     **/
    public static function filtrate($msg)
    {
        //$msg = str_replace('&','&amp;',$msg);
        //$msg = str_replace(' ','&nbsp;',$msg);
        $msg = str_replace('"', '&quot;', $msg);
        $msg = str_replace("'", '&#39;', $msg);
        $msg = str_replace("<", "&lt;", $msg);
        $msg = str_replace(">", "&gt;", $msg);
        $msg = str_replace("\t", "   &nbsp;  &nbsp;", $msg);
        //$msg = str_replace("\r","",$msg);
        $msg = str_replace("   ", " &nbsp; ", $msg);
        return $msg;
    }

    //清除文件
    public static function clearFile($uploadDir = '')
    {
        $dir = APPLICATION_PATH . '/../data/images/' . $uploadDir;
        if (is_dir($dir)) {
            if ($dir_handle = opendir($dir)) {
                while (false !== ($file_name = readdir($dir_handle))) {
                    @unlink($dir . "/" . $file_name);
                }
            }
            @rmdir($dir);
            return true;
        }
        return false;
    }
    
    public static function clearEmptySrc($content){
    	$content = preg_replace('/<img[^>]+src=(\s+)?["\']?(\s+)?["\']?(\s+)?(\/)?>/', '', $content);
    	$content = preg_replace('/<img([^>])+src(\s+)?=(\s+)?(\/)?>/i', '', $content);
    	return $content;
    }
    
    public static function getAdapter(){
        return Table_Orders::getInstance()->getAdapter();
        //使用下面的方法会使 事物提交失败
//     	return Zend_Registry::get('db');
    }
    
	/**
	 * 获取第二连接
	 * @return Ambigous <Zend_Db_Adapter_Abstract, unknown>
	 */
    public static function getAdapterForDb2(){
    	return Ec::getDb2();
    }

    /**
     * 获取第三连接
     * @return Ambigous <Zend_Db_Adapter_Abstract, unknown>
     */
    public static function getAdapterForDb3(){
    	return Ec::getDb3();
    }
    //字符串解密加密
    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    
    	$ckey_length = 6;	// 随机密钥长度 取值 0-32;
    	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    	// 当此值为 0 时，则不产生随机密钥
    
    	$key = md5($key ? $key : 'EC');
    	$keya = md5(substr($key, 0, 16));
    	$keyb = md5(substr($key, 16, 16));
    	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    
    	$cryptkey = $keya.md5($keya.$keyc);
    	$key_length = strlen($cryptkey);
    
    	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    	$string_length = strlen($string);
    
    	$result = '';
    	$box = range(0, 255);
    
    	$rndkey = array();
    	for($i = 0; $i <= 255; $i++) {
    		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
    	}
    
    	for($j = $i = 0; $i < 256; $i++) {
    		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
    		$tmp = $box[$i];
    		$box[$i] = $box[$j];
    		$box[$j] = $tmp;
    	}
    
    	for($a = $j = $i = 0; $i < $string_length; $i++) {
    		$a = ($a + 1) % 256;
    		$j = ($j + $box[$a]) % 256;
    		$tmp = $box[$a];
    		$box[$a] = $box[$j];
    		$box[$j] = $tmp;
    		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    	}
    
    	if($operation == 'DECODE') {
    		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
    			return substr($result, 26);
    		} else {
    			return '';
    		}
    	} else {
    		return $keyc.str_replace('=', '', base64_encode($result));
    	}
    }
    
    public static function jiexiEbayMessageContent($content){
                
        $regex_text = '/>\s+</';
        $content =  preg_replace($regex_text, '><', $content); //得到解析后的html
        
        $regex_text = '/(<div id=\"MarketSaftyTip\"><.*?>.*?<\/.*?><\/div>)/ism';
        $content =  preg_replace($regex_text, '', $content); //得到解析后的html
        
        
        $regex_text = '/(<div id=\"ReferenceId\"><.*?>.*?<\/.*?><\/div>)/ism';
        $content =  preg_replace($regex_text, '', $content); //得到解析后的html
        
        
        $regex_text = '/(<div id=\"Footer\"><.*?>.*?<\/.*?><\/div>)/ism';
        $content =  preg_replace($regex_text, '', $content); //得到解析后的html
        
        $regex_text = '/(<div id=\"Header\"><.*?>.*?<\/.*?><\/div><\/div>)/ism';
        $content =  preg_replace($regex_text, '', $content); //得到解析后的html

        
        return $content;
    }
    /**
     * 获取ebay用户名
     * @param unknown_type $userAccount
     * @return mixed|string
     */
    public static function getPlatformUserName($userAccount){
        $pUser = Service_PlatformUser::getByField($userAccount,'user_account');
        if($pUser){
            return $pUser['platform_user_name'];
        }else{
            return 'invalid user_account';
        }
    }

    /**
     * 获取ebay用户名
     * @param unknown_type $userAccount
     * @return mixed|string
     */
    public static function getPlatformUser($userAccounts=array()){
        $con = array('user_account_arr'=>$userAccounts);
        $pUser = Service_PlatformUser::getByCondition($con,array('user_account','platform_user_name'));
        $temp = array(); 
        foreach($pUser as $v){
            $temp[$v['user_account']] = $v;
        }       
        return $temp;
    }
    
    /**
     * array to xml
     * @param unknown_type $arr
     * @return string
     */
    public static function array2Xml($arr,$extends=array()){
        $writer = new Ec_Xml();
        $config = new Zend_Config($arr);
        foreach($extends as $k=>$v){
            $config->setExtend($k,$v);
        }        
        $writer->setConfig($config);
        $xml = $writer->render();
        return $xml;
    }

    //设置API的密匙
    public static function genApiToken($companyCode)
    {
        $company = Service_Company::getByField($companyCode, 'company_code');
        if(!$company){
            return false;
        }
        $token = md5(md5(strrev(md5($companyCode))));
        $key = md5(md5(md5(strrev(md5($companyCode.time())))));
        $row = array(
            'app_token' => $token,
            'app_key' => $key
        );
        Service_Company::update($row, $companyCode, 'company_code');
        $company['app_token'] = $token;
        $company['app_key'] = $key;
        return $company;
    }

    /**
     * 将null替换为空字符串
     *
     * @param unknown_type $arr
     * @throws Exception
     * @return string
     */
    public static function arrayNullToEmptyString($arr)
    {
        if(! is_array($arr)){
            throw new Exception('参数非数组');
        }
        foreach($arr as $k => $v){
            if(! isset($v)){
                $arr[$k] = '';
            }
        }
        return $arr;
    }

    /**
     * 检测列是否存在
     */
    public static function checkTableColumnExist($table,$column)
    {
        try{
            $sql = "desc {$table}";
            $rows = Common_Common::fetchAll($sql);
            $columns = array();
            foreach($rows as $v){
                $columns[] = $v['Field'];
            }
            if(! in_array($column, $columns)){
                $sql = "ALTER TABLE `{$table}` ADD COLUMN $column  varchar(200) NULL DEFAULT '' COMMENT ''";
                Common_Common::query($sql);
            }
        }catch(Exception $e){
            Common_ApiProcess::log($e->getMessage());
        }
    }

    public static function query($sql)
    {
        $db = Common_Common::getAdapter();
        $db->query($sql);
    }
    
    public static function fetchRow($sql)
    {
        $db = Common_Common::getAdapter();
        $data = $db->fetchRow($sql);
        return $data;
    }
    
    public static function fetchAll($sql)
    {
        $db = Common_Common::getAdapter();
        $data = $db->fetchAll($sql);
        return $data;
    }
    
    public static function fetchOne($sql)
    {
        $db = Common_Common::getAdapter();
        $data = $db->fetchOne($sql);
        return $data;
    }

    /*
     * @desc 命令模式下才输出
    */
    public static function myEcho($str)
    {
    	if (PHP_SAPI == 'cli') {
    		echo '[' . date('Y-m-d H:i:s') . ']' . iconv('UTF-8', 'GB2312', $str . "\n");
    	}
    }
    
    /**
     * @desc 删除文件夹内的文件
     * @param $dir
     * @return bool
     */
    public static function delDirFile($dir)
    {
    	if (!file_exists($dir)) {
    		return true;
    	}
    	if (is_dir($dir)) {
    		$dh = opendir($dir);
    		while ($file = readdir($dh)) {
    			if ($file != "." && $file != "..") {
    				$fullpath = $dir . "/" . $file;
    				if (!is_dir($fullpath)) {
    					unlink($fullpath);
    				}
    			}
    		}
    		closedir($dh);
    	}
    }
    
    /**
     * 递归创建路径
     */
    public static function mkdirs($path)
    {
    	if (!file_exists($path)) {
    		self::mkdirs(dirname($path));
    		mkdir($path, 0777);
    		chmod($path, 0777);
    	}
    }
    


    /**
     * g
     * @param unknown_type $url
     * @param unknown_type $base64_content
     * @throws Exception
     * @return mixed
     */
    public static function curlRequest($url, $base64_content) {
    	// initialise a CURL session
    	$connection = curl_init ();
    	// set method as POST
    	curl_setopt ( $connection, CURLOPT_POST, 1 );
    	// curl_setopt ( $connection, CURLOPT_HTTPHEADER, array (
    	// 'Content-type: application/x-www-form-urlencoded'
    	// ) );
    	curl_setopt ( $connection, CURLOPT_CUSTOMREQUEST, "POST" );
    	$params = array (
    			'base64_content' => $base64_content
    	);
    	// echo $params;exit;
    	curl_setopt ( $connection, CURLOPT_POSTFIELDS, $params );
    
    	curl_setopt ( $connection, CURLOPT_URL, $url );
    
    	// stop CURL from verifying the peer's certificate
    	curl_setopt ( $connection, CURLOPT_SSL_VERIFYPEER, 0 );
    	curl_setopt ( $connection, CURLOPT_SSL_VERIFYHOST, 0 );
    
    	// set it to return the transfer as a string from curl_exec
    	curl_setopt ( $connection, CURLOPT_RETURNTRANSFER, 1 );
    
    	// Send the Request
    	$response = curl_exec ( $connection );
    
    	// close the connection
    	curl_close ( $connection );
    
    	// return the response
    	// echo $response;exit;
    	$response = json_decode ( $response, true );
    	if (empty ( $response )) {
    		throw new Exception ( '返回结果错误,请检查请求的URL是否有空格' );
    	}
    	return $response;
    }
    
    /**
     * 获取图片绝对路径
     */
    public static function listDir($dir = null)
    {
    	$pngList = array();
    	if (!file_exists($dir)) {
    		return array();
    	}
    	if (is_dir($dir)) {
    		$dir_handle = opendir($dir);
    		if ($dir_handle) {
    			while (false !== ($file_name = readdir($dir_handle))) {
    				$key = basename($file_name);
    				$keyArr = explode(".", $key);
    				if (isset($keyArr[1]) && $keyArr[0] !== '') {
    					$pngList [$key] = $dir . "/" . $file_name;
    				}
    			}
    		}
    	} else {
    		return array();
    	}
    	ksort($pngList);
    	return $pngList;
    }
    
    /**
     * 日志
     *
     * @param unknown_type $str
     */
    public static function log($str)
    {
    	if (empty ( $str )) {
    		return;
    	}
    	// 		Ec::showError($str, 'shopify_load_data_');
    	if (Zend_Registry::isRegistered('SAPI_DEBUG') && Zend_Registry::get('SAPI_DEBUG') === true) {
    		echo '[' . date ( 'Y-m-d H:i:s' ) . ']' . iconv ( 'UTF-8', 'GB2312', $str . "\n" );
    	}
    }
    
    public static function curlRequestForDataMatrix($url, $postData = '', $proxy = "")
    {
    	$proxy = trim($proxy);
    	$user_agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
    	$ch = curl_init(); // 初始化CURL 句柄
    	if(! empty($proxy)){
    		curl_setopt($ch, CURLOPT_PROXY, $proxy); // 设置代理服务器
    	}
    	//         echo $url;exit;
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    	curl_setopt($ch, CURLOPT_HEADER, true); // 请求头是否包含在响应中
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($ch, CURLOPT_URL, $url); // 设置请求的URL
    	// curl_setopt($ch,
    	// CURLOPT_FAILONERROR, 1); //
    	// 启用时显示HTTP 状态码，默认行为是忽略编号小于等于400
    	// 的HTTP 信息
    	// curl_setopt($ch,
    	// CURLOPT_FOLLOWLOCATION,
    	// 1);//启用时会将服务器服务器返回的“Location:”放在header
    	// 中递归的返回给服务器
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设为TRUE
    	// 把curl_exec()结果转化为字串，而不是直接输出
    	curl_setopt($ch, CURLOPT_POST, 1); // 启用POST 提交
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // 设置POST 提交的字符串
    	// curl_setopt($ch,
    	// CURLOPT_PORT, 80);
    	// //设置端口
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 超时时间
    	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent); // HTTP 请求User-Agent:头
    	curl_setopt($ch, CURLOPT_HEADER, false); // 设为TRUE
    	// 在输出中包含头信息
    	// $fp =
    	// fopen("example_homepage.txt",
    	// "w");//输出文件
    	// curl_setopt($ch,
    	// CURLOPT_FILE,
    	// $fp);//设置输出文件的位置，值是一个资源类型，默认为STDOUT
    	// (浏览器)。
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    	'Accept-Language: zh-cn',
    	'Connection: Keep-Alive',
    	'Cache-Control: no-cache'
    			)); // 设置HTTP 头信息
    
    	$response = curl_exec($ch); // 执行预定义的CURL
    	if(empty($response)){
    		throw new Exception('service no data response');
    	}
    	$info = curl_getinfo($ch); // 得到返回信息的特性
    	$errno = curl_errno($ch);
    	$error = curl_error($ch);
    	if($errno){
    		throw new Exception($error, $errno);
    	}
    	curl_close($ch);
    	if($info['http_code'] == "405"){
    		throw new Exception("bad proxy {$proxy}", 500);
    	}
    
    	return $response;
    }
    
    //解析xml
    public static function object2array($object) { return @json_decode(@json_encode($object),1); }
    
    public static function xml_to_array($xml,$style="",$upper=0){
    	switch($style){
    		case "wchat":$xml=simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);break;
    		default:$xml=simplexml_load_string($xml);break;
    	}
    	if(empty($xml))
    		return false;
    	$return = self::object2array($xml);
    	switch($upper){
    		//键值大写
    		case 1:$return=array_change_key_case($return,CASE_UPPER);break;
    		case 2:$return=array_change_key_case($return,CASE_LOWER);break;
    	}
    	return $return;
    }
    
    //汇率接口
    public static function getHuilv(){
	    $returnArr= array();
	    $returnArr['USD']=6.5;//添加上美元的汇率
		$returnArr['CNY']=1;//添加上中国的汇率
		
		try{
			do{	
				$url = "http://data.bank.hexun.com/other/cms/fxjhjson.ashx?callback=PereMoreData";
				$content = file_get_contents($url);
				$reg = '/PereMoreData\((.*?)\)/is';
				preg_match_all($reg,$content,$m);
				if($m[1]){
					//print_r($m[1][0]);
					//将单引号转双引号
					$json_str = str_replace("'",'"',$m[1][0]);
					$json_str = str_replace("currency",'"currency"',$json_str);
					$json_str = str_replace("refePrice",'"refePrice"',$json_str);
					$json_str = str_replace("code",'"code"',$json_str);
					$json_str = preg_replace('/"currency":".*?",/','',$json_str);
					$decodeArr = json_decode($json_str,true);
					if(!is_array($decodeArr)){
						break;
					}
					
					$currencyArr = array("USD","HKD","JPY","MOP","PHP","SGD","KRW","THB","EUR","DKK","GBP","DEM","FRF","ITL","ESP","ATS","FIM","NOK","SEK","CHF","CAD","AUD","NZD");
					foreach($currencyArr as $cv){	
						foreach($decodeArr as $v){
							if($cv==trim($v['code'])){
								$returnArr[$cv]=$v['refePrice']/100;
								break;
							}
						}
					}
				}
			}while(0);
			
		}catch(Exception $e){
			
		}
    	return $returnArr;
    }
    
    //获取产品的详细信息
   	public static function getProductAllByCode($code){
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/product.ini', APPLICATION_ENV);
    	$temp   = $config->ascode->apichannel->toArray();
    	return isset($temp[$code])?$temp[$code]:false;
    }
    
    //根据国家获取相对于的物流产品(快件录单专用)
    public static function getProductAllByCountryCode($countrycode,$fromcode){
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/product.ini', APPLICATION_ENV);
    	$temp   = $config->ascode->channel->toArray();
    	$code = $fromcode.$countrycode;
    	return isset($temp[$code])?$temp[$code]:false;
    }
    
    //获取赛程渠道的产品号
	public static function getProductAllSaicheng($All=false){
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/product.ini', APPLICATION_ENV);
    	$temp   = $config->ascode->channel->toArray();
    	$temp_config  = $config->ascode->apichannel->toArray();
    	$return = array();
    	foreach ($temp as $k =>$v){
    		if(!empty($temp_config[$v])&&$temp_config[$v]['ccode']=='Saicheng'){
    			if(strpos($k,'ESBR')===false){
    				$return['ESB'][] = $v;
    			}else
    				$return['ESBR'][] = $v;
    		}
    	}
    	if($All){
    		$return = array_merge($return['ESB'],$return['ESBR']);
    	}
    	return $return;
    }
    
    /**
     * @return mixed
     */
    public static function renderPdf($pdffile)
    {
    	$file = fopen($pdffile,"r");
    	//返回的文件类型
    	Header("Content-type: application/pdf");
    	//按照字节大小返回
    	Header("Accept-Ranges: bytes");
    	//返回文件的大小
    	Header("Accept-Length: ".filesize($pdffile));
    	//修改之前，一次性将数据传输给客户端
    	//echo fread($file, filesize($filepath));
    	//修改之后，一次只传输1024个字节的数据给客户端
    	//向客户端回送数据
    	$buffer=1024;//
    	$file_count=0;
    	//判断文件是否读完
    	while (!feof($file)&&$file_count<filesize($pdffile)) {
    		//将文件读入内存
    		$file_count+=$buffer;
    		$file_data=fread($file,$buffer);
    		//每次向客户端回送1024个字节的数据
    		echo $file_data;
    	}
    	fclose($file);
    }
}