<?php
/**
 * Mabang接口服务类
 * @author Frank
 * @date 2015-7-2
 */
class Mabang_MabangLib{
	/**
	 * 查询订单列表 
	 * api.biaoju.order.find
	 * http://www.i8956.com/interface/index.php 正式地址
	 * http://www.sandbox.i8956.com/interface/index.php 测试地址
	 * @param unknown_type $app_id				APP ID
	 * @param unknown_type $app_key				APP Key
	 * @param unknown_type $requestArr			请求参数数组（access_token 字段必传）
	 * @return Ambigous <multitype:, array>
	 */
	public static function getListOrdersForArr($requestArr){
// 		print_r($requestArr);exit;
		$obj = new Mabang_Order_Common(); 
		$platform_user = $obj->getPlatformUser();
		$appId = $platform_user ['user_token_id'];
		$app_Key = $platform_user ['user_token'];
		$apiURL = $platform_user ['url'];//请求接口URL
		
		$apiName = 'api.biaoju.order.find';//接口名称
		$encodeparams=base64_encode(json_encode($requestArr));
		$t=time();
		$sign=md5('api='.$apiName.'&apiAccountId='.$appId.'&encodeParams='.$encodeparams.'&timestamp='.$t.$app_Key);
		/*
		 * 组织参数
		 */
		$nvpReq = 'api='.$apiName.'&timestamp='.$t.'&apiAccountId='.$appId.'&encodeParams='.$encodeparams.'&sign='.$sign;

        Ec::showError('查询订单列表'.$apiURL.'?'.$nvpReq, '_mabang_receivelog' . date('Y-m-d') . "_");
		return self::call_mabang($apiURL, $nvpReq);
	}
	public static function updateOrderStatus($requestArr){
			$obj = new Mabang_Order_Common(); 
			$platform_user = $obj->getPlatformUser();
			$appId = $platform_user ['user_token_id'];
			$app_Key = $platform_user ['user_token'];
			$apiURL = $platform_user ['url'];//请求接口URL
			$apiName = 'api.biaoju.order.update';//接口名称
			$encodeparams=base64_encode(json_encode($requestArr));
			$t=time();
			$sign=md5('api='.$apiName.'&apiAccountId='.$appId.'&encodeParams='.$encodeparams.'&timestamp='.$t.$app_Key);
			$nvpReq = 'api='.$apiName.'&timestamp='.$t.'&apiAccountId='.$appId.'&encodeParams='.$encodeparams.'&sign='.$sign;

			$nvpResArray =  self::call_mabang($apiURL, $nvpReq);

			Ec::showError("请求信息:".print_r($requestArr,true)."\n".'更新状态'.$apiURL.'?'.$nvpReq."\n接收到的信息:\n".print_r($nvpResArray,true), '_mabang_receivelog_' . date('Y-m-d') . "_");
			
			return $nvpResArray;
	}
		/**
	 * 发送POST请求
	 * @param unknown_type $post_url
	 * @param unknown_type $nvpReq
	 * @throws Exception
	 * @return array
	 */
	public static function call_mabang($post_url,$nvpReq){

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
			Ec::showError('发送请求参数异常，详情ERRNO:' . curl_errno($ch) . '  ERROR:' .curl_error($ch), '_mabang_receivelog_' . date('Y-m-d') . "_");
			throw new Exception('发送请求参数异常，详情ERRNO:' . curl_errno($ch) . '  ERROR:' .curl_error($ch));
		}else{
			curl_close($ch);
		}
		//echo $response;exit;
		$nvpResArray = json_decode($response,true);
		Ec::showError('接收到的信息'.$response, '_mabang_receivelog_' . date('Y-m-d') . "_");
		//print_r($nvpResArray);exit;
		//$nvpResArray = self::arr_process($nvpResArray);
		return $nvpResArray;
	}
}
	
?>