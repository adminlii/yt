<?php
class Common_ReportSoap extends SoapClient {
	function __doRequest($request, $location, $saction, $version) {
		$doc = new DOMDocument ( '1.0' );
		$doc->loadXML ( $request );
		$requestXml = $doc->saveXML ();
		$location = preg_replace('/([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,})/', '$1.$2.$3.$4:$5', $location);
		//echo APPLICATION_PATH . '/../data/log/report_req_' . $saction . '.xml';exit;
		@file_put_contents ( APPLICATION_PATH . '/../data/log/report_location.txt', date('Y-m-d H:i:s')."\n".$location."\n\n",FILE_APPEND );
		$responseXml = $this->curlRequest ( $location, $requestXml );
		@file_put_contents ( APPLICATION_PATH . '/../data/log/report_res_' . $saction . '.xml', $responseXml );
		return $responseXml;
		// return parent::__doRequest($objWSSE->saveXML(), $location, $saction, $version);
	}
	
	// -------------------------------------------------------------------------------------------------------------
	public function curlRequest($url, $postData = '', $proxy = "") {
		// header('Content-type: text/xml');
		// echo $postData;exit;
		// file_put_contents(APPLICATION_PATH.'/..//public/royal_mail_shipping/xml/t.xml', $postData);
		// exit;
		// $postData = file_get_contents(APPLICATION_PATH.'/../public/royal_mail_shipping/xml/createShipmentRequest.xml');
		// exit;
		$proxy = trim ( $proxy );
		$user_agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
		$ch = curl_init (); // 初始化CURL 句柄
		if (! empty ( $proxy )) {
			curl_setopt ( $ch, CURLOPT_PROXY, $proxy ); // 设置代理服务器
		}
		// echo $url;exit;
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, true );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, true );
		curl_setopt ( $ch, CURLOPT_CAINFO, APPLICATION_PATH . '/../libs/mike.pem' );
		
		curl_setopt ( $ch, CURLOPT_SSLCERT, APPLICATION_PATH . '/../libs/mike.pem' ); // 客户端证书
		                                                                           
		// curl_setopt($ch, CURLOPT_SSLKEY, 'Peachtao`123456'); #客户端密钥
		
		curl_setopt ( $ch, CURLOPT_VERBOSE, true );
		
		curl_setopt ( $ch, CURLOPT_HEADER, true ); // 请求头是否包含在响应中
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt ( $ch, CURLOPT_URL, $url ); // 设置请求的URL
		                                     // curl_setopt($ch,
		                                     // CURLOPT_FAILONERROR, 1); //
		                                     // 启用时显示HTTP 状态码，默认行为是忽略编号小于等于400
		                                     // 的HTTP 信息
		                                     // curl_setopt($ch,
		                                     // CURLOPT_FOLLOWLOCATION,
		                                     // 1);//启用时会将服务器服务器返回的“Location:”放在header
		                                     // 中递归的返回给服务器
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ); // 设为TRUE
		                                             // 把curl_exec()结果转化为字串，而不是直接输出
		curl_setopt ( $ch, CURLOPT_POST, 1 ); // 启用POST 提交
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData ); // 设置POST 提交的字符串
		                                                 // curl_setopt($ch,
		                                                 // CURLOPT_PORT, 80);
		                                                 // //设置端口
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 300 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 ); // 超时时间
		curl_setopt ( $ch, CURLOPT_USERAGENT, $user_agent ); // HTTP 请求User-Agent:头
		curl_setopt ( $ch, CURLOPT_HEADER, false ); // 设为TRUE
		                                         // 在输出中包含头信息
		                                         // $fp =
		                                         // fopen("example_homepage.txt",
		                                         // "w");//输出文件
		                                         // curl_setopt($ch,
		                                         // CURLOPT_FILE,
		                                         // $fp);//设置输出文件的位置，值是一个资源类型，默认为STDOUT
		                                         // (浏览器)。
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Accept-Language: zh-cn',
				'Connection: Keep-Alive',
				'Cache-Control: no-cache',
				'Content-type: text/xml' 
		) ); // 设置HTTP 头信息
		
		$response = curl_exec ( $ch ); // 执行预定义的CURL
		$info = curl_getinfo ( $ch ); // 得到返回信息的特性
		$errno = curl_errno ( $ch );
		$error = curl_error ( $ch );
		if ($errno) {
			throw new Exception ( $error, $errno );
		}
		curl_close ( $ch );
		if ($info ['http_code'] == "405") {
			throw new Exception ( "bad proxy {$proxy}", 500 );
		}
		
		// header('Content-type: text/xml');
		// echo $response;exit;
		return $response;
	}
}
