<?php
/**
 * 获得Amazon上传数据的处理结果
 * @author Administrator
 *
 */
class Amazon_GetFeedSubmissionResultService{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'getFeedSubmissionResult_';
	
	/**
	 * 接口名称
	 * @var unknown_type
	 */
	const INTERFACE_NAME = 'GetFeedSubmissionResult';
	
	/**
	 * 路径
	 * @var unknown_type
	 */
	const SERVICE_PATH = '/Feeds/';
	
	/**
	 * 请求上限
	 * @var unknown_type
	 */
	const REQUEST_MAX = 15;
	
	/**
	 * 恢复个数
	 * @var unknown_type
	 */
	const RESTORE_NUM = 1;
	
	/**
	 * 恢复时间
	 * @var unknown_type
	 */
	const RESTORE_TIME_NUM = 1;
	/**
	 * 恢复时间单位
	 */
	const RESTORE_TIME_UNIT = 'minute';
	
	
	/**
	 * 秘钥信息
	 * @var unknown_type
	 */
	private $_tokenConfig = array (
			'AWS_ACCESS_KEY_ID' => null,
			'AWS_SECRET_ACCESS_KEY' => null,
			'MERCHANT_ID' => null,
			'SITE'=>null,
			'APPLICATION_NAME' => null,
			'APPLICATION_VERSION' => null,
	);
	
	/**
	 * 构造器
	*/
	public function __construct($token_id , $token , $saller_id , $site)
	{
		//访问秘钥ID
		$this->_tokenConfig['AWS_ACCESS_KEY_ID'] = $token_id;
		//访问秘钥
		$this->_tokenConfig['AWS_SECRET_ACCESS_KEY'] = $token;
		//销售ID
		$this->_tokenConfig['MERCHANT_ID'] = $saller_id;
		//站点
		$this->_tokenConfig['SITE'] = $site;
		//应用名称
		$this->_tokenConfig['APPLICATION_NAME'] = Amazon_AmazonLib::APPLICATION_NAME;
		//应用版本
		$this->_tokenConfig['APPLICATION_VERSION'] = Amazon_AmazonLib::APPLICATION_VERSION;
	}
	
	/**
	 * 获得上传数据的处理报告
	 * @param unknown_type $feedSubmissionId
	 * @return multitype:number string Ambigous <number, NULL, string, number, NULL, string, mixed>
	 */
	public function getFeedSubmissionResult($FeedSubmissionId,$FeedType){
		$return = array(
				'ask'=>0,
				'message'=>'',
				'data'=>''
		);
		
		/*
		 * 秘钥
		*/
		$countryCode = $this->_tokenConfig['SITE'];
		/*
		 * 1. 检查接口运行情况
		*/
		$returnCheck = Amazon_AmazonLib::checkAmazonRunControl(self::INTERFACE_NAME, $this->_tokenConfig['MERCHANT_ID'], $countryCode, self::REQUEST_MAX);
		
		if(!$returnCheck['ask']){
			$return['message'] = $returnCheck['message'];
			return $return;
		}
		
		/*
		 * 2. 取得亚马逊站点、地址
		*/
		$amazonConfig = Amazon_AmazonSubmitFeedLib::getAmazonConfig();
		if(empty($amazonConfig[$countryCode])){
			$return['message'] = "amzon站点： $countryCode ，未能找到对应的亚马逊服务地址及商城编号.";
			return $return;
		}
		
		/*
		 * 3. 初始化配置信息，创建request对象
		*/
		$serviceUrl = $amazonConfig[$countryCode]['service_url'];// . self::SERVICE_PATH . Amazon_AmazonSubmitFeedLib::SERVICE_VERSION;
		$config = array (
				'ServiceURL' => $serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		
		$service = new MarketplaceWebService_Client(
				$this->_tokenConfig['AWS_ACCESS_KEY_ID'],
				$this->_tokenConfig['AWS_SECRET_ACCESS_KEY'],
				$config,
				$this->_tokenConfig['APPLICATION_NAME'],
				$this->_tokenConfig['APPLICATION_VERSION']);
		$download_path = Amazon_AmazonSubmitFeedLib::getReportPath($FeedType);
		$download_name = date('Y-m-d') . '_' . $FeedSubmissionId . '.xml';
		$filename = $download_path . '/' . $download_name;
// 		$filename = __DIR__.'/file.xml';
// 		echo $filename;exit;
		$handle = @fopen($filename, 'w+');
		$parameters = array (
				'Marketplace'=>$amazonConfig[$countryCode]['marketplace_id'],
				'Merchant' => $this->_tokenConfig['MERCHANT_ID'],
				'FeedSubmissionId' => $FeedSubmissionId,
				'FeedSubmissionResult' => $handle,
		);
		
		$request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);
		echo '<br/><br/>';
		print_r($this->_tokenConfig);
		echo '<br/><br/>';
		print_r($parameters);
		echo '<br/><br/>';
		print_r($request);
		echo '<br/><br/>';
		print_r($service);
		echo '<br/><br/>';
		print_r($filename);
		echo '<br/><br/>';
		
		
		try{
			$response = $service->getFeedSubmissionResult($request);
// 			$response = $this->invokeGetFeedSubmissionResult($service, $request);
			$return['ask'] = 1;
			$return['file_name'] = $download_name;
			$return['data'] = $response;
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 2);
		} catch (MarketplaceWebService_Exception $ex) {
			$errorMessage = '错误代码：' . $ex->getErrorCode() . '--请求ID：' . $ex->getRequestId() . '--错误信息：' . print_r($ex,true);
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 下载上传记录报告发生异常,错误原因：" . print_r($ex,true), self::$log_name);
		}catch (Exception $e){
			$errorMessage = '错误代码：' . $e->getCode() . '--错误信息：' . $e->getMessage();
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 下载上传记录报告发生异常,错误原因：" . print_r($e,true), self::$log_name);
		}
		return $return;
	}

	public function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request)
	{
		try {
			$response = $service->getFeedSubmissionResult($request);
	
			echo ("Service Response\n");
			echo ("=============================================================================\n");
	
			echo("        GetFeedSubmissionResultResponse\n");
			if ($response->isSetGetFeedSubmissionResultResult()) {
				$getFeedSubmissionResultResult = $response->getGetFeedSubmissionResultResult();
				echo ("            GetFeedSubmissionResult");
	
				if ($getFeedSubmissionResultResult->isSetContentMd5()) {
					echo ("                ContentMd5");
					echo ("                " . $getFeedSubmissionResultResult->getContentMd5() . "\n");
				}
			}
			if ($response->isSetResponseMetadata()) {
				echo("            ResponseMetadata\n");
				$responseMetadata = $response->getResponseMetadata();
				if ($responseMetadata->isSetRequestId())
				{
					echo("                RequestId\n");
					echo("                    " . $responseMetadata->getRequestId() . "\n");
				}
			}
			
			echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
			
		} catch (MarketplaceWebService_Exception $ex) {
			echo("Caught Exception: " . $ex->getMessage() . "\n");
			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
			echo("Error Code: " . $ex->getErrorCode() . "\n");
			echo("Error Type: " . $ex->getErrorType() . "\n");
			echo("Request ID: " . $ex->getRequestId() . "\n");
			echo("XML: " . $ex->getXML() . "\n");
			echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
		}
		return $response;
	}
}