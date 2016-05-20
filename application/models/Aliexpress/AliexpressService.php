<?php
/**
 * 速卖通-父类，提供一些便捷函数
 * @author Frank
 * @date 2014-9-18 15:03:10
 */
class Aliexpress_AliexpressService extends Ec_AutoRun{
	
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'aliexpress_Service_';
	
	/**
	 * 时差
	 * @var unknown_type
	 */
	public static $time_difference = '';
	
	/**
	 * 转换速卖通时间格式
	 * @param unknown_type $aliexpress_date
	 * @return mixed	'y-m-d H:i:s'
	 */
	public static function convertDateFormat($aliexpress_date){
		if(empty($aliexpress_date)){
			return $aliexpress_date;
		}
		return preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})(\S{8})/','$1-$2-$3 $4:$5:$6',$aliexpress_date);
	}
	
	/**
	 * 检查是否刷新Token
	 * @param unknown_type $pu_id
	 * @throws Exception
	 */
// 	protected static function checkAliexpressToken($pu_id){
	public static function checkAliexpressToken($pu_id){
		if(!empty($pu_id)){
			$platform_user = Service_PlatformUser::getByField($pu_id,'pu_id');
			if(!empty($platform_user) && $platform_user['platform'] == 'aliexpress'){
				/*
				 * 1.检查Tefresh_Token是否过期
				 */
				$refresh_response = self::checkRefreshToken($platform_user);
				if($refresh_response['ask']){
					if($refresh_response['is_refresh']){
						return $refresh_response['data'];
					}
				}else{
					throw new Exception($refresh_response['message']);
				}
				
				/*
				 * 2.检查Access_Token是否过期
				 */
				$access_response = self::checkAccessToken($platform_user);
				if($access_response['ask']){
					if($access_response['is_refresh']){
						return $access_response['data'];
					}else{
						return $platform_user;
					}
				}else{
					throw new Exception($refresh_response['message']);
				}
				
			}else{
				throw new Exception('未能找到pu_id：'.$pu_id.' 的授权信息，或该授权账户不是Aliexpress账户');
			}
		}else{
			throw new Exception('pu_id：不能为空，请检查');
		}
	}
	
	/**
	 * 检查Refresh_Token是否过期，并更新
	 * @param unknown_type $platform_user
	 */
	private static function checkRefreshToken($platform_user){
		Ec::showError("开始检查Refresh_Token是否过期", self::$log_name);
		$return = array(
				'ask'=>0,				//是否调用成功
				'is_refresh'=>0,		//是否刷新Refresh_Token
				'data'=>array(),		//$platform_user
				'message'=>''			//返回信息
				);

		
// 		$date = date('Y-m-d H:i:s');
		$date = date("Y-m-d H:i:s",strtotime("+1 hour"));
		/*
		 * 1.检查Refresh_Token的过期时间，是否超过当前时间
		 */
		if($date > $platform_user['refresh_token_timeout']){
			$log_message = 'Refresh_Token过期，尝试刷新 \\\ 原始授权信息：' . print_r($platform_user,true);
			Ec::showError($log_message, self::$log_name);
					
			$pu_id = $platform_user['pu_id'];
			$access_token = $platform_user['user_token'];
			$refresh_token = $platform_user['refresh_token'];
			$app_key = $platform_user['app_key'];
			$app_secret = $platform_user['app_signature'];
			
			/*
			 * 2.调用接口，获取新的Refresh_Token
			 */
			$response = Aliexpress_AliexpressLib::getNewRefreshTokenForSellers($access_token, $refresh_token, $app_key, $app_secret);
			if(isset($response['aliId'])){
				$format = "Y-m-d H:i:s";
				$date = date($format);
				$expires_in_date = date($format,strtotime("+$response[expires_in] seconds",strtotime($date)));
				 
				$refresh_token_timeout_date = preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})(\S{8})/','$1-$2-$3 $4:$5:$6',$response['refresh_token_timeout']);
				
				$update = array(
					'aliId'=>$response['aliId'],
					'resource_owner'=>$response['resource_owner'],
					'refresh_token'=>$response['refresh_token'],
					'refresh_token_timeout'=>$refresh_token_timeout_date,
					'user_token'=>$response['access_token'],
					'expires_in'=>$expires_in_date
				);
				
				if($platform_user['aliId'] != $update['aliId']){
					$error = 'Refresh_Token更新异常：aliId 不相同，系统授权信息：' . print_r($platform_user,true) . '==》更新授权信息：' . print_r($response,true);
					$return['message'] = $error;
				}else{
					Service_PlatformUser::update($update, $pu_id);
					$new_platform_user = Service_PlatformUser::getByField($pu_id);
					$return['ask'] = 1;
					$return['is_refresh'] = 1;
					$return['data'] = $new_platform_user;
					
					$log_message = 'Refresh_Token刷新成功 --> 原始授权信息：' . print_r($platform_user,true) . ' \\\ 新授权信息：' . print_r($new_platform_user,true);
					Ec::showError($log_message, self::$log_name);
				}
			}else{
				$error = 'Refresh_Token获取异常，详情：' . print_r($response,true);
				$return['message'] = $error;
				
				$log_message = 'Refresh_Token刷新失败 --> 原始授权信息：' . print_r($platform_user,true) . ' \\\ 返回信息：' . print_r($response,true);
				Ec::showError($log_message, self::$log_name);
			}
		}else{
			$return['ask'] = 1;
			$return['is_refresh'] = 0;
			$return['message'] = 'Refresh_Token还未过期，无需获取新的Refresh_Token';
			Ec::showError("Refresh_Token未过期", self::$log_name);
		}
		
		return $return;
	}
	
	/**
	 * 检查Access_Token是否过期，并更新
	 * @param unknown_type $platform_user
	 */
	private static function checkAccessToken($platform_user){
		Ec::showError("开始检查Access_Token是否过期", self::$log_name);
		$return = array(
				'ask'=>0,				//是否调用成功
				'is_refresh'=>0,		//是否刷新Refresh_Token
				'data'=>array(),		//$platform_user
				'message'=>''			//返回信息
		);
// 		$date = date('Y-m-d H:i:s');
		$date = date("Y-m-d H:i:s",strtotime("+1 hour"));
		
		/*
		 * 1.检查Access_Token的过期时间，是否超过当前时间 
		 */
		if($date > $platform_user['expires_in']){
			$log_message = 'Access_Token过期，尝试刷新 \\\ 原始授权信息：' . print_r($platform_user,true);
			Ec::showError($log_message, self::$log_name);
				
			$pu_id = $platform_user['pu_id'];
			$refresh_token = $platform_user['refresh_token'];
			$app_key = $platform_user['app_key'];
			$app_secret = $platform_user['app_signature'];
				
			/*
			 * 2.调用接口，获取新的Access_Token
			*/
			$response = Aliexpress_AliexpressLib::getNewAccessTokenForSellers($refresh_token, $app_key, $app_secret);
			if(isset($response['aliId'])){
				$format = "Y-m-d H:i:s";
				$date = date($format);
				$expires_in_date = date($format,strtotime("+$response[expires_in] seconds",strtotime($date)));
				
				$update = array(
						'aliId'=>$response['aliId'],
						'resource_owner'=>$response['resource_owner'],
						'user_token'=>$response['access_token'],
						'expires_in'=>$expires_in_date
				);
				
				if($platform_user['aliId'] != $update['aliId']){
					$error = 'Access_Token更新异常：aliId 不相同，系统授权信息：' . print_r($platform_user,true) . '==》更新授权信息：' . print_r($response,true);
					$return['message'] = $error;
				}else{
					Service_PlatformUser::update($update, $pu_id);
					$new_platform_user = Service_PlatformUser::getByField($pu_id);
					$return['ask'] = 1;
					$return['is_refresh'] = 1;
					$return['data'] = $new_platform_user;
						
					$log_message = 'Access_Token刷新成功 --> 原始授权信息：' . print_r($platform_user,true) . ' \\\ 新授权信息：' . print_r($new_platform_user,true);
					Ec::showError($log_message, self::$log_name);
				}
			}else{
				$error = 'Access_Token获取异常，详情：' . print_r($response,true);
				$return['message'] = $error;
				
				$log_message = 'Access_Token刷新失败 --> 原始授权信息：' . print_r($platform_user,true) . ' \\\ 返回信息：' . print_r($response,true);
				Ec::showError($log_message, self::$log_name);
			}
		}else{
			$return['ask'] = 1;
			$return['is_refresh'] = 0;
			$return['message'] = 'Access_Token还未过期，无需获取新的Access_Token';
			Ec::showError("Access_Token未过期", self::$log_name);
		}
		
		return $return;
	}
	
}