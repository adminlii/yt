<?php
/**
 * 马帮-查询订单列表服务
 * @author Max
 */
class Mabang_Order_Common {
	protected $_user_account = 'not_set';
	protected $_company_code = 'not_set';
	protected $_platform_user = null;
	public function setCompanyCode($company_code) {
		$this->_company_code = $company_code;
	}
	public function setUserAccount($user_account) {
		$this->_user_account = $user_account;
	}
	public function writeFile($file,$str,$mode='w')
	{
		$oldmask = @umask(0);
		$fp = @fopen($file,$mode);
		@flock($fp, 3);
		if(!$fp)
		{
			Return false;
		}
		else
		{
			@fwrite($fp,$str."\n");
			@fclose($fp);
			@umask($oldmask);
			Return true;
		}
	}
	public function getPlatformUser() 
	{
		if(!$this->_platform_user){
			$PlatformID=Service_Config::getByField("MABANG_API_ACCOUNT_ID","config_attribute","config_value");
			$PlatformKey=Service_Config::getByField("MABANG_API_ACCOUNT_KEY","config_attribute","config_value");
			$PlatformUrl=Service_Config::getByField("MABANG_API_REQUEST_URL","config_attribute","config_value");
			$this->_platform_user['user_token_id']=$PlatformID['config_value'];
			$this->_platform_user['user_token']=$PlatformKey['config_value'];
			$this->_platform_user['url']=$PlatformUrl['config_value'];
		}		
		return $this->_platform_user;
	}

	/**
	 * 转换速卖通时间格式
	 * @param unknown_type $aliexpress_date
	 * @return mixed	'y-m-d H:i:s'
	 */
	public static function convertDateFormat($aliexpress_date){
		if(empty($aliexpress_date)){
			return $aliexpress_date;
		}
		return preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/','$1-$2-$3 $4:$5:$6',$aliexpress_date);
	}
	/**
	 * 检查是否刷新Token
	 *
	 * @param unknown_type $pu_id        	
	 * @throws Exception
	 */
	// protected static function checkAliexpressToken($pu_id){
	public function checkAliexpressToken() {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$platform_user = $this->getPlatformUser ();
			/*
			 * 1.检查Tefresh_Token是否过期
			 */
			$rs = $this->checkRefreshToken ();
			if (! $rs ['ask']) {
				throw new Exception ( $rs ['message'] );
			}
			
			/*
			 * 2.检查Access_Token是否过期
			 */
			$rs = $this->checkAccessToken ();
			if (! $rs ['ask']) {
				throw new Exception ( $rs ['message'] );
			}
			
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
	/**
	 * 检查Refresh_Token是否过期，并更新
	 *
	 * @param unknown_type $platform_user        	
	 */
	public function checkRefreshToken() {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$platform_user = $this->getPlatformUser ();
			
			$date = date ( "Y-m-d H:i:s", strtotime ( "+10 days" ) );
			/*
			 * 1.检查Refresh_Token的过期时间，是否超过当前时间
			 */
			if ($date > $platform_user ['refresh_token_timeout']) {
				$log_message = 'Refresh_Token过期，尝试刷新 \\\ 原始授权信息：' . print_r ( $platform_user, true );
				
				$pu_id = $platform_user ['pu_id'];
				$access_token = $platform_user ['user_token'];
				$refresh_token = $platform_user ['refresh_token'];
				$app_key = $platform_user ['app_key'];
				$app_secret = $platform_user ['app_signature'];
				
				/*
				 * 2.调用接口，获取新的Refresh_Token
				 */
				$response = Aliexpress_AliexpressLib::getNewRefreshTokenForSellers ( $access_token, $refresh_token, $app_key, $app_secret );
				if (! isset ( $response ['aliId'] )) {
					throw new Exception ( 'Refresh_Token刷新失败，详情：' . print_r ( $response, true ) );
				}
				
				$format = "Y-m-d H:i:s";
				$date = date ( $format );
				$expires_in_date = date ( $format, strtotime ( "+$response[expires_in] seconds", strtotime ( $date ) ) );
				
				$refresh_token_timeout_date = preg_replace ( '/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})(\S{8})/', '$1-$2-$3 $4:$5:$6', $response ['refresh_token_timeout'] );
				
				$update = array (
						'aliId' => $response ['aliId'],
						'resource_owner' => $response ['resource_owner'],
						'refresh_token' => $response ['refresh_token'],
						'refresh_token_timeout' => $refresh_token_timeout_date,
						'user_token' => $response ['access_token'],
						'expires_in' => $expires_in_date 
				);
				
				if ($platform_user ['aliId'] != $update ['aliId']) {
					$error = 'Refresh_Token更新异常：aliId 不相同，系统授权信息：' . print_r ( $platform_user, true ) . '==》更新授权信息：' . print_r ( $response, true );
					throw new Exception ( $error );
				} else {
					Service_PlatformUser::update ( $update, $pu_id );
				}
			}
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
	
	/**
	 * 检查Access_Token是否过期，并更新
	 *
	 * @param unknown_type $platform_user        	
	 */
	public function checkAccessToken() {
		$return = array (
				'ask' => 0,
				'message' => 'Fail.' 
		);
		try {
			$platform_user = $this->getPlatformUser ();
			$date = date ( "Y-m-d H:i:s", strtotime ( "+1 hour" ) );
			/*
			 * 1.检查Access_Token的过期时间，是否超过当前时间
			 */
			if ($date > $platform_user ['expires_in']) {
				$pu_id = $platform_user ['pu_id'];
				$refresh_token = $platform_user ['refresh_token'];
				$app_key = $platform_user ['app_key'];
				$app_secret = $platform_user ['app_signature'];
				/*
				 * 2.调用接口，获取新的Access_Token
				 */
				$response = Aliexpress_AliexpressLib::getNewAccessTokenForSellers ( $refresh_token, $app_key, $app_secret );
				if (! isset ( $response ['aliId'] )) {
					throw new Exception ( 'Refresh_Token刷新失败，详情：' . print_r ( $response, true ) );
				}
				
				$format = "Y-m-d H:i:s";
				$date = date ( $format );
				$expires_in_date = date ( $format, strtotime ( "+{$response['expires_in']} seconds", strtotime ( $date ) ) );
				
				$update = array (
						'aliId' => $response ['aliId'],
						'resource_owner' => $response ['resource_owner'],
						'user_token' => $response ['access_token'],
						'expires_in' => $expires_in_date 
				);
				
				if ($platform_user ['aliId'] != $update ['aliId']) {
					$error = 'Access_Token更新异常：aliId 不相同，系统授权信息：' . print_r ( $platform_user, true ) . '==》更新授权信息：' . print_r ( $response, true );
					throw new Exception ( $error );
				} else {
					Service_PlatformUser::update ( $update, $pu_id );
				}
			}
			$return ['ask'] = 1;
			$return ['message'] = 'Success';
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
}