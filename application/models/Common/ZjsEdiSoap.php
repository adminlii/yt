<?php 
class Common_ZjsEdiSoap extends SoapClient
{

    protected $_username = '';

    protected $_password = '';

    protected $_nonce = '';
    
    protected $_nonce_base64 = '';

    protected $_created = '';

    protected $_password_digest = '';

    public function setUserName($userName)
    {
        $this->_username = $userName;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
    }

    public function getNonce()
    {
        return $this->_nonce;
    }

    public function getNonceBase64()
    {
        return $this->_nonce_base64;
    }
    
    public function getCreated()
    {
        return $this->_created;
    }

    public function getPasswordDigest()
    {
        return $this->_password_digest;
    }

    function __doRequest($request, $location, $saction, $version)
    {
        $doc = new DOMDocument('1.0');
        $doc->loadXML($request);
         
        $requestXml = $doc->saveXML();  
//         echo $requestXml;exit;
        $responseXml =  $this->curlRequest($location,$requestXml); 
        var_dump($responseXml);exit;
        return $responseXml; 
    }

    // -------------------------------------------------------------------------------------------------------------
    public function curlRequest($url, $postData = '', $proxy = "")
    {
//         header('Content-type: text/xml');
//         echo $postData;exit;
//         file_put_contents(APPLICATION_PATH.'/..//public/royal_mail_shipping/xml/t.xml', $postData);
//         exit;
//         $postData = file_get_contents(APPLICATION_PATH.'/../public/royal_mail_shipping/xml/createShipmentRequest.xml');
//         exit;
        $proxy = trim($proxy);
        $user_agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
        $ch = curl_init(); // 初始化CURL 句柄
        if(! empty($proxy)){
            curl_setopt($ch, CURLOPT_PROXY, $proxy); // 设置代理服务器
        }
//         echo $url;exit;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); 
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
        'Cache-Control: no-cache',
		'Content-type: application/x-www-form-urlencoded;charset=UTF-8'
                )); // 设置HTTP 头信息
                
        $response = curl_exec($ch); // 执行预定义的CURL
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

//         header('Content-type: text/xml');
//         echo $response;exit;   
        return $response;
    }
}
