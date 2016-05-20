<?php
/**
 * paypal接口服务类
 * @author Frank
 * @date 2013-8-17 
 */
class Paypal_PaypalLib{
	/**
	 * Paypal 付款类型的值
	 * （付款类型一共三种Authorization ，Order ，Sale 适合我司使用的是Sale，详细请参看Paypal的API文档，当中关于DoExpressCheckoutPayment的参数定义）
	 */
	private static $paypal_payment_type = 'Sale';
	
	/**
	 * paypal支付币种
	 */
	private static $paypal_currency_code = 'USD';
	
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(0);
	}
	
	/**
	 * paypal退款接口
	 * @param unknown_type $nvpStr
	 * @return Ambigous <multitype:string, multitype:string >
	 */
	public static function call_RefundTransaction($nvpStr){
		return self::call_Paypal('RefundTransaction', $nvpStr);
	}
	
	/**
	 * paypal查询交易记录接口
	 * @param unknown_type $nvpStr
	 */
	public static function call_TransactionSeach($nvpStr){
		return self::call_Paypal('TransactionSearch', $nvpStr);
	}
	
	/**
	 * paypal查询交易明细接口
	 * @param unknown_type $nvpStr
	 * @return Ambigous <multitype:string, multitype:string >
	 */
	public static function call_GetTransactionDetail($nvpStr){
		return self::call_Paypal('GetTransactionDetails', $nvpStr);
	}
	
	/**
	 * paypal发起快速收款节奏请求
	 * @param unknown_type $nvpStr
	 * @return Ambigous <multitype:string, multitype:string >
	 */
	public static function call_SetExpressCheckout($nvpStr){
		return self::call_Paypal('SetExpressCheckout', $nvpStr);
	}
	
	/**
	 * paypal查询发起“快速收款结账”请求的客户信息
	 * @param unknown_type $nvpStr
	 * @return Ambigous <multitype:string, multitype:string >
	 */
	public static function call_GetExpressCheckoutDetails($nvpStr){
		return self::call_Paypal('GetExpressCheckoutDetails', $nvpStr);
	}
	
	/**
	 * paypal执行快速收款节奏动作
	 * @param unknown_type $nvpStr
	 * @return Ambigous <multitype:string, multitype:string >
	 */
	public static function call_DoExpressCheckoutPayment($nvpStr){
		return self::call_Paypal('DoExpressCheckoutPayment', $nvpStr);
	}
	
	/**
	 * 根据给予的请求url，发送到paypal端
	 * @param unknown_type $methodName
	 * @param unknown_type $nvpStr
	 * @param unknown_type $account
	 * @return multitype:string
	 */
	public static function call_Paypal($methodName,$nvpStr){

		/*
		 * 1.设置请求url、版本等
		*/
		define('API_ENDPOINT', 		Common_Company::getPaypalEndpoint());
		define('USE_PROXY',			FALSE);
		define('PROXY_HOST', 		'127.0.0.1');
		define('PROXY_PORT', 		'808');
		define('VERSION', 			'57.0');//查询：57.0--退款：51.0
		$API_Endpoint 				= API_ENDPOINT;
		$version					= VERSION;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
	
		if(USE_PROXY){
// 			curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT);
		}
	
		$nvpreq =  "METHOD=".urlencode($methodName);
		$nvpreq .= "&VERSION=".urlencode($version);
		$nvpreq .= $nvpStr;
	
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
// 		echo '请求链接：<br/><br/>';
// 		var_dump($nvpreq);
// 		echo '<br/><br/>';
	
		$response = curl_exec($ch);
// 		$ch_info = curl_getinfo($ch);
// 		print_r($ch_info);
		$nvpResArray = self::deformatNVP($response);
		$nvpReqArray = self::deformatNVP($nvpreq);
// 		$_SESSION['nvpReqArray']=$nvpReqArray;
	
		if (curl_errno($ch)) {
			$_SESSION['curl_error_no']=curl_errno($ch) ;
			$_SESSION['curl_error_msg']=curl_error($ch);
			throw new Exception('paypal发送请求参数异常，详情ERRNO:' . curl_errno($ch) . '  ERROR:' .curl_error($ch));
		}else{
			curl_close($ch);
		}
	
		return $nvpResArray;
	}
	
	/**
	 * 转移参数
	 * @param unknown_type $nvpstr
	 * @return multitype:string
	 */
	 public static function deformatNVP($nvpstr)
	{
	
		$intial=0;
	
		$nvpArray = array();
	
		while(strlen($nvpstr)){
	
			$keypos= strpos($nvpstr,'=');
	
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
	
			$keyval=substr($nvpstr,$intial,$keypos);
	
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
	
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
	
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	
		}
	
		return $nvpArray;
	
	}
	
	public static function str_rep($str){
		$str  = str_replace("'","&acute;",$str);
		$str  = str_replace("\"","&quot;",$str);
		return $str;
	}
	
	/**
	 * 获得PayPal发起快速收款结账的参数
	 * @param unknown_type $company_code	公司代码
	 * @param unknown_type $refNo			系统参考号
	 * @param unknown_type $amount			金额
	 * @param unknown_type $url				当前系统地址（如：www.ec.com）
	 * @return string
	 */
	public static function getSetExpressCheckoutNVP($company_code,$refNo,$amount,$url){
		//指定收款账户
		$API_UserName = Common_Company::getPaypalReceivableUsername();
		$API_Password = Common_Company::getPaypalReceivablePassword();
		$API_Signature = Common_Company::getPaypalReceivableSignature();
		 
		$nvpStr = "&USER=".urlencode($API_UserName);
		$nvpStr .= "&PWD=".urlencode($API_Password);
		$nvpStr .= "&SIGNATURE=".urlencode($API_Signature);
		 
		//获取项目域名地址，用于拼接地址
		$url = 'http://' . $url;
		//http://www.heb-oms.com/fee/recharge/paypal-callback
		$successURL = $url . '/fee/recharge/paypal-callback';
		$cancelURL = $url;
// 		echo 'SuccessURL: '.$successURL;
// 		echo '<br/>';
// 		echo 'CancelURL: '.$cancelURL;
// 		echo '<br/><br/>';
// 		exit;
		
		//CUSTOM：自定义的一个信息用于存放单号或者其他的（在GetExpressCheckoutDetails的响应结果中可以拿到）
		$nvpStr .= "&CUSTOM=".urlencode($refNo);
		 
		//RETURNURL:客户选择通过PayPal付款后其浏览器将要返回的URL(PayPalPayPal建议该参数的值为客户确认订单和付款)
		$nvpStr .= "&RETURNURL=".urlencode($successURL);
		 
		//CANCELURL:客户不批准使用PayPal  向您付款时将返回到的URL(PayPal建议该参数的值为客户选择通过PayPal,付款或签订结算协议的最初页面)
		$nvpStr .= "&CANCELURL=".urlencode($cancelURL);
		 
		//HDRIMG:我们提供的一个图片地址，放置在paypal支付页面的头部（非必需）
		$headerImg = $url . '/images/pay/paypal_header.png';
// 		echo $headerImg;
// 		exit;
		$nvpStr .= "&HDRIMG=".urlencode($headerImg);
		 
		/*PAYMENTACTION:
		 *  	 Sale表示这是您正进行收款的最终销售（适用于我司）,
		*  	 Authorization表示该付款是通过“PayPal授权和捕获”结算的基本授权
		*  	 Order表示该付款是通过“PayPal授权和捕获”结算的订单授权
		*/
		$nvpStr .= "&PAYMENTACTION=".urlencode(self::$paypal_payment_type);
		 
		//DESC:客户PayPal支付时，给予收款方（我司账户）的留言，说明等描述
		$nvpStr .= "&DESC=".urlencode("客户：" . $company_code ."，通过仓储OMS进行在线充值(DESC)");
		
		//NOTETEXT:客户在paypal上付款是，进行的留言
		$nvpStr .= "&NOTETEXT=".urlencode("客户：" . $company_code ."，通过仓储OMS进行在线充值(NOTETEXT)");
		
		//设置物品明细
		$nvpStr .= "&L_NAME0=".urlencode(Ec::Lang('api_paypal_l_name'));		//物品名称
		$nvpStr .= "&L_DESC0=".urlencode(Ec::Lang('api_paypal_l_desc'));		//物品描述
		$nvpStr .= "&L_AMT0=".urlencode($amount);								//物品成本
		$nvpStr .= "&L_QTY0=".urlencode(1);										//物品数量
		 
		//金额币种
		$nvpStr .= "&AMT=".urlencode($amount);									//总金额
		$nvpStr .= "&CURRENCYCODE=".urlencode(self::$paypal_currency_code);		//币种：默认->USD
		$nvpStr .= "&NOSHIPPING=".urlencode(1);									//设置为不显示送货地址
		 
		return $nvpStr;
	}
	
	/**
	 * 获得发起PayPal快速收款结账的#客户信息#参数
	 * @param unknown_type $PayPalToken		发起快速结账后，回传的token（URL中）
	 */
	public static function getGetExpressCheckoutDetailsNVP($PayPalToken){
		//指定收款账户
		$API_UserName = Common_Company::getPaypalReceivableUsername();
		$API_Password = Common_Company::getPaypalReceivablePassword();
		$API_Signature = Common_Company::getPaypalReceivableSignature();
		
		$nvpStr = "&USER=".urlencode($API_UserName);
		$nvpStr .= "&PWD=".urlencode($API_Password);
		$nvpStr .= "&SIGNATURE=".urlencode($API_Signature);
		
		$nvpStr .= '&TOKEN=' . $PayPalToken;
		return $nvpStr;
	}
	
	/**
	 * 获得paypal执行快速收款参数
	 * @param unknown_type $PayPalToken		获取paypal支付账户的信息接口中返回的token
	 * @param unknown_type $payerId			唯一的PayPal客户账户识别号
	 * @param unknown_type $paymentType		付款类型
	 * @param unknown_type $totalAmount		总金额
	 * @param unknown_type $currencyCode	币种(默认：USD)
	 */
	public static function getDoExpressCheckoutPaymentNVP($PayPalToken, $payerId, $paymentType, $totalAmount, $currencyCode){
		//指定收款账户
		$API_UserName = Common_Company::getPaypalReceivableUsername();
		$API_Password = Common_Company::getPaypalReceivablePassword();
		$API_Signature = Common_Company::getPaypalReceivableSignature();
		
		$nvpStr = "&USER=".urlencode($API_UserName);
		$nvpStr .= "&PWD=".urlencode($API_Password);
		$nvpStr .= "&SIGNATURE=".urlencode($API_Signature);
		
		$nvpStr .= '&TOKEN=' . $PayPalToken;
		$nvpStr .= '&PAYERID=' . $payerId;
		$nvpStr .= '&PAYMENTACTION=' . $paymentType;
		$nvpStr .= '&AMT=' . $totalAmount;
		$nvpStr .= '&CURRENCYCODE=' . $currencyCode;
		
		return $nvpStr;
	}
}