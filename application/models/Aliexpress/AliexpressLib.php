<?php
/**
 * Aliexpress接口服务类
 * @author Frank
 * @date 2014-9-13
 */
class Aliexpress_AliexpressLib{
	
	/**
	 * 应用Key/签名
	 * @TODO 待申请自己的APP后，就可以更换授权模式了，现在是客户自己申请APP，自己使用
	 * @var unknown_type
	 */
	const APPLICATION_KEY = '';					//申请的商用APP Key
	const APPLICATION_SIGNATURE = '';			//申请的商用APP 签名
	
	/**
	 * OPEN API 固定路径
	 * @var unknown_type
	 */
	const APPLICATION_OPENAPI_URL = 'http://gw.api.alibaba.com:80/openapi/';
	
	/**
	 * File API 固定路径
	 * @var unknown_type
	 */
	const APPLICATION_FILEAPI_URL = 'http://gw.api.alibaba.com:80/fileapi/';
	
	/**
	 * API请求参数类型 和 API包名
	 * @var unknown_type
	 */
	const APPLICATION_PARAM_TYPE_NAMESPACE = 'param/1/aliexpress.open/';
	
	/**
	 * 时差，相比北京时间要慢16个小时
	 * @var unknown_type
	 */
	public static $Time_Difference = 16;
	
	/**
	 * 保护时间，用来推算，订单是否到了需要再更新的时间之后
	 * @var unknown_type
	 */
	public static $Protection_Time = 14;
	
	/**
	 * 订单的交易费点数
	 * PS：使用实际付款金额乘这个系数，然后加上联盟佣金，就是一个订单的交易费用
	 * @var unknown_type
	 */
	public static $Transaction_Fees = 0.05;
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(60);
	}
	
	/**
	 * 获得授权地址 ==> 商用授权APP
	 * @param unknown_type $app_url 如：http://www.ebtest.com
	 * @return string
	 */
	public static function getSignature($app_url){
		$appKey = self::APPLICATION_KEY;
		$appSecret = self::APPLICATION_SIGNATURE;
		$redirectUrl = $app_url . '/platform/aliexpress-authorize/set-code';
		 
		//生成签名
		$code_arr = array(
				'client_id' => $appKey,
				'redirect_uri' => $redirectUrl,
				'site' => 'aliexpress'
		);
		ksort($code_arr);
		foreach ($code_arr as $key=>$val){
			$sign_str .= $key . $val;
		}
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
		
		$get_code_url = "http://gw.api.alibaba.com/auth/authorize.htm?client_id={$appKey}&site=aliexpress&redirect_uri={$redirectUrl}&_aop_signature={$code_sign}";
		
		return $get_code_url;
	}
	
	/**
	 * 获得授权地址 ==> 属于卖家的授权APP
	 * @param unknown_type $app_url		如：http://www.ebtest.com
	 * @param unknown_type $app_key		客户申请App的key
	 * @param unknown_type $app_secret	客户申请App的签名串
	 * @return string
	 */
	public static function getSignatureForSellers($app_url,$app_key,$app_secret){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$redirectUrl = $app_url . '/platform/aliexpress-authorize/set-code';
			
		//生成签名
		$code_arr = array(
				'client_id' => $appKey,
				'redirect_uri' => $redirectUrl,
				'site' => 'aliexpress'
		);
		ksort($code_arr);
		foreach ($code_arr as $key=>$val){
			$sign_str .= $key . $val;
		}
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
		
		$get_code_url = "http://gw.api.alibaba.com/auth/authorize.htm?client_id={$appKey}&site=aliexpress&redirect_uri={$redirectUrl}&_aop_signature={$code_sign}";
		
		return $get_code_url;
	}
	
	/**
	 * 获得Token ==> 商用授权APP
	 * @param unknown_type $app_url		如：http://www.ebtest.com
	 * @param unknown_type $code		临时授权码
	 */
	public static function getToken($app_url,$code){
		$appKey = self::APPLICATION_KEY;
		$appSecret = self::APPLICATION_SIGNATURE;
		$redirectUrl = $app_url . '/order/aliexpress-authorize/set-token';
		
		$getToken_url = "https://gw.api.alibaba.com/openapi/http/1/system.oauth2/getToken/{$appKey}";
		$nvpReq = "grant_type=authorization_code&need_refresh_token=true&client_id={$appKey}&client_secret={$appSecret}&redirect_uri={$redirectUrl}&code={$code}";
		
		return self::call_aliexpress($getToken_url, $nvpReq);
	}
	
	/**
	 * 获得Token ==> 属于卖家的授权APP
	 * @param unknown_type $app_url			如：http://www.ebtest.com
	 * @param unknown_type $app_key			客户申请App的key
	 * @param unknown_type $app_secret		客户申请App的签名串
	 * @param unknown_type $code			临时授权码
	 * @throws Exception
	 * @return mixed
	 */
	public static function getTokenForSellers($app_url,$app_key,$app_secret,$code){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$redirectUrl = $app_url . '/order/aliexpress-authorize/set-token';
		
		$getToken_url = "https://gw.api.alibaba.com/openapi/http/1/system.oauth2/getToken/{$appKey}";
		$nvpReq = "grant_type=authorization_code&need_refresh_token=true&client_id={$appKey}&client_secret={$appSecret}&redirect_uri={$redirectUrl}&code={$code}";
			
		return self::call_aliexpress($getToken_url, $nvpReq);
	}
	
	/**
	 * 获取新的AccessToken ==> 商用授权APP
	 * @TODO 因为Aliexpress的AccessToken是每十个小时就过期，所以需要定时更新
	 * @param unknown_type $refresh_token	授权后返回的refresh_token
	 */
	public static function getNewAccessToken($refresh_token){
		$appKey = self::APPLICATION_KEY;
		$appSecret = self::APPLICATION_SIGNATURE;
		$refreshToken = $refresh_token;
	
		$getNewToken_url = "https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/getToken/{$appKey}";
		$nvpReq = "grant_type=refresh_token&client_id={$appKey}&client_secret={$appSecret}&refresh_token={$refreshToken}";
	
		return self::call_aliexpress($getNewToken_url, $nvpReq);
	}
	
	/**
	 *  获取新的AccessToken ==> 属于卖家的授权APP
	 * @TODO 因为Aliexpress的AccessToken是每十个小时就过期，所以需要定时更新
	 * @param unknown_type $refresh_token	授权后返回的refresh_token
	 * @param unknown_type $app_key			客户申请App的key
	 * @param unknown_type $app_secret		客户申请App的签名串
	 * @return array
	 */
	public static function getNewAccessTokenForSellers($refresh_token,$app_key,$app_secret){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$refreshToken = $refresh_token;
		
		$getNewToken_url = "https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/getToken/{$appKey}";
		$nvpReq = "grant_type=refresh_token&client_id={$appKey}&client_secret={$appSecret}&refresh_token={$refreshToken}";
		
		return self::call_aliexpress($getNewToken_url, $nvpReq);
	}
	
	/**
	 * 获取新的RefreshToekn ==> 属于卖家的授权APP
	 * @TODO 只有时间超过refresh_token_timeout的时间后，使用该接口才会正常返回数据。
	 * 		   否则会报错，如：Array ( [error] => invalid_request [error_description] => refreshToken is too long to expire with expireTime 20141222000009000-0800 )
	 * @param unknown_type $access_token	授权后返回的access_token
	 * @param unknown_type $refresh_token	授权后返回的refresh_token
	 * @param unknown_type $app_key			客户申请App的key
	 * @param unknown_type $app_secret		客户申请App的签名串
	 * @return array
	 */
	public static function getNewRefreshTokenForSellers($access_token,$refresh_token,$app_key,$app_secret){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$accessToken = $access_token;
		$refreshToken = $refresh_token;
		
		$getRefreshToken_url = "https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/postponeToken/{$appKey}";
		$nvpReq = "client_id={$app_key}&client_secret={$app_secret}&refresh_token={$refresh_token}&access_token={$accessToken}";
		
		return self::call_aliexpress($getRefreshToken_url, $nvpReq);
	}
	
	/**
	 * 获取新的RefreshToekn ==> 属于商用APP
	 * @TODO 只有时间超过refresh_token_timeout的时间后，使用该接口才会正常返回数据。
	 * 		   否则会报错，如：Array ( [error] => invalid_request [error_description] => refreshToken is too long to expire with expireTime 20141222000009000-0800 )
	 * @param unknown_type $access_token	授权后返回的access_token
	 * @param unknown_type $refresh_token	授权后返回的refresh_token
	 * @return array
	 */
	public static function getNewRefreshToken($access_token,$refresh_token){
		$appKey = self::APPLICATION_KEY;
		$appSecret = self::APPLICATION_SIGNATURE;
		$accessToken = $access_token;
		$refreshToken = $refresh_token;
	
		$getRefreshToken_url = "https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/postponeToken/{$appKey}";
		$nvpReq = "client_id={$appKey}&client_secret={$appSecret}&refresh_token={$refresh_token}&access_token={$accessToken}";
	
		return self::call_aliexpress($getRefreshToken_url, $nvpReq);
	}

	
	/**
	 * 查询订单列表 
	 * api.findOrderListQuery
	 * http://activities.aliexpress.com/open/dev/doc.php?spm=5261.6744729.972263401.3
	 * @param unknown_type $app_key				APP Key
	 * @param unknown_type $app_secret			APP 签名
	 * @param unknown_type $requestArr			请求参数数组（access_token 字段必传）
	 * @return Ambigous <multitype:, array>
	 */
	public static function getListOrdersForArr($app_key, $app_secret, $requestArr){
	
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.findOrderListQuery';								//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
		
		/*
		 * 组织参数
		 */
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
		
		/*
		 * 添加签名
		 */
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
	
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
	
		return self::call_aliexpress($post_url, $nvpReq);
	}
	
	/**
	 * 根据订单ID查询订单明细
	 * api.findOrderById
	 * http://activities.aliexpress.com/open/dev/doc.php?spm=5261.6744729.972263401.3
	 * @param unknown_type $app_key				APP Key
	 * @param unknown_type $app_secret			APP 签名
	 * @param unknown_type $requestArr			请求参数数组（access_token 字段必传）
	 * @return Ambigous <multitype:, mixed>
	 */
	public static function getOrderDetailById($app_key, $app_secret, $requestArr){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.findOrderById';										//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
		
		/*
		 * 组织参数
		*/
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
		
		/*
		 * 添加签名
		*/
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
		
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
		
		return self::call_aliexpress($post_url, $nvpReq);
	}

	/**
	 * 根据订单ID查询订单明细
	 * api.findOrderById
	 * http://activities.aliexpress.com/open/dev/doc.php?spm=5261.6744729.972263401.3
	 * @param unknown_type $app_key				APP Key
	 * @param unknown_type $app_secret			APP 签名
	 * @param unknown_type $requestArr			请求参数数组（access_token 字段必传）
	 * @return Ambigous <multitype:, mixed>
	 */
	public static function getOrderReceiptInfoById($app_key, $app_secret, $requestArr){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.findOrderReceiptInfo';										//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
	
		/*
		 * 组织参数
		*/
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
	
		/*
		 * 添加签名
		*/
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
	
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
	
		return self::call_aliexpress($post_url, $nvpReq);
	}
	
	/**
	 * 查询订单放款信息
	 * @param unknown_type $app_key			APP Key
	 * @param unknown_type $app_secret		APP 签名
	 * @param unknown_type $requestArr		请求参数数组（access_token 字段必传）
	 */
	public static function getLoanList($app_key, $app_secret, $requestArr){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.findLoanListQuery';									//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
		
		/*
		 * 组织参数
		*/
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
		
		/*
		 * 添加签名
		*/
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
		
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
		
		return self::call_aliexpress($post_url, $nvpReq);
	}
	
	/**
	 * 查询订单交易信息
	 * @param unknown_type $app_key			APP Key
	 * @param unknown_type $app_secret		APP 签名
	 * @param unknown_type $requestArr		请求参数数组（access_token 字段必传）
	 */
	public static function getOrderTradeInfo($app_key, $app_secret, $requestArr){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.findOrderTradeInfo';								//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
		
		/*
		 * 组织参数
		*/
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
		
		/*
		 * 添加签名
		*/
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
		
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
		
		return self::call_aliexpress($post_url, $nvpReq);
	}
	
	/**
	 * 获得速卖通平台发货服务商列表信息
	 * @param unknown_type $app_key
	 * @param unknown_type $app_secret
	 * @param unknown_type $requestArr
	 * @return Ambigous <multitype:, mixed>
	 */
	public static function getlistLogisticsService($app_key, $app_secret, $requestArr){
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.listLogisticsService';								//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
		
		/*
		 * 组织参数
		*/
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
		
		/*
		 * 添加签名
		*/
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
		
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
		
		return self::call_aliexpress($post_url, $nvpReq);
	}
	
	/**
	 * 初次标记发货
	 * @速卖通必须传入跟踪号进行标记发货
	 * @param unknown_type $app_key
	 * @param unknown_type $app_secret
	 * @param unknown_type $requestArr
	 * @return Ambigous <multitype:, mixed>
	 */
	public static function sellerShipment($app_key, $app_secret, $requestArr){
// 		print_r($requestArr);exit;
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.sellerShipment';									//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
		
		/*
		 * 组织参数
		*/
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
		
		/*
		 * 添加签名
		*/
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
		
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
		
		return self::call_aliexpress($post_url, $nvpReq);
	}
	
	/**
	 * 修改发货声明
	 * @TODO 修改声明发货(一个订单只能修改2次，只能修改声明发货后5日内的订单，请注意！)
	 * http://gw.api.alibaba.com/dev/doc/api.htm?ns=aliexpress.open&n=api.sellerModifiedShipment&v=1
	 * @param unknown_type $app_key
	 * @param unknown_type $app_secret
	 * @param unknown_type $requestArr
	 */
	public static function sellerModifiedShipment($app_key, $app_secret, $requestArr){
// 		print_r($requestArr);exit;
		$appKey = $app_key;
		$appSecret = $app_secret;
		$apiName = 'api.sellerModifiedShipment';							//接口名称
		$apiURL = self::APPLICATION_PARAM_TYPE_NAMESPACE . $apiName;		//请求接口类型及路径
		
		/*
		 * 组织参数
		*/
		$nvpReq = '';
		foreach ($requestArr as $key => $value) {
			$nvpReq .= (empty($nvpReq))?$key . '=' . $value:'&' . $key . '=' . $value;
		}
		
		/*
		 * 添加签名
		*/
		$signStr = self::getSignStr($appKey, $appSecret, $apiURL, $requestArr);
		$nvpReq .= "&_aop_signature={$signStr}";
		
		$post_url = self::APPLICATION_OPENAPI_URL . $apiURL ."/{$appKey}";
		
		return self::call_aliexpress($post_url, $nvpReq);
	}
	
	/**
	 * 获取API接口签名
	 * @param unknown_type $app_key			APP Key
	 * @param unknown_type $app_secret		APP 签名
	 * @param unknown_type $apiUrl			API 请求路径（不包含固定请求头）
	 * @param unknown_type $requestArr		请求参数数组
	 * @param unknown_type $app_url			APP回调URL，（可不填）
	 * @return string
	 */
	public static function getSignStr($app_key, $app_secret, $apiUrl, $requestArr, $app_url = 'http://eccang.com'){
		//固定URL
		$url = 'http://gw.api.alibaba.com/openapi';
		$appKey = $app_key;
		$appSecret = $app_secret;
		
		//拼接API路径和APP Key
		$apiInfo = "{$apiUrl}/" . $appKey;
		
		//组织参数
		ksort($requestArr);
		foreach ($requestArr as $key => $value) {
			$apiInfo .= $key . $value;
		}
		$sign_str = $apiInfo;
		
		//使用hmac_sha1算法，得出签名
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
		
		return $code_sign;
	}
	
	
	/**
	 * 发送POST请求
	 * @param unknown_type $post_url
	 * @param unknown_type $nvpReq
	 * @throws Exception
	 * @return array
	 */
	public static function call_aliexpress($post_url,$nvpReq){
// 		echo 'post_url:<br/><br/>';echo $post_url;echo '<br/><br/>';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$post_url);
		curl_setopt($ch, CURLOPT_VERBOSE, false);//显示过程
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		Common_ApiProcess::log("请求地址：".$post_url);
		Common_ApiProcess::log("请求参数：".$nvpReq);
		
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpReq);
        Common_ApiProcess::log('请求开始'); 
// 		echo 'nvpRequest:<br/><br/>';echo $nvpReq;echo '<br/><br/>';
		$response = curl_exec($ch);
// 		echo 'response:<br/><br/>';echo $response;echo '<br/><br/>';
        
        $ch_info = curl_getinfo($ch);
        $ch_error = curl_error($ch);
        Common_ApiProcess::log('请求结束'); 
        Common_ApiProcess::log('请求信息'.print_r($ch_info,true));  
        if($ch_error){
        	Common_ApiProcess::log('错误信息'.print_r($ch_error,true));
        }        
		
		if (curl_errno($ch)) {
			$_SESSION['curl_error_no']=curl_errno($ch);
			$_SESSION['curl_error_msg']=curl_error($ch);
			throw new Exception('发送请求参数异常，详情ERRNO:' . curl_errno($ch) . '  ERROR:' .curl_error($ch));
		}else{
			curl_close($ch);
		}
		$nvpResArray = json_decode($response,true);
		$nvpResArray = self::arr_process($nvpResArray);
		return $nvpResArray;
	}
	/**
	 * aliexpress上，order_id为float类型，需要转为字符串类型
	 * @param unknown_type $_arr
	 * @return string
	 */
	public static function arr_process($_arr)
	{
		$arr = array();
		if(is_array($_arr)){
			foreach($_arr as $key => $val){
				$val = is_array($val) ? self::arr_process($val) : (is_null($val)?$val:$val.'');
				$arr[$key] = $val;
			}
		}
		return $arr ;
	}
}