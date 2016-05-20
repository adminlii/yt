<?php
/**
 * amazon上传数据服务类
 * @author Frank
 * @date 2013-11-15 20:21:49
 */
class Amazon_SubmitFeedService{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'amazonSubmitFeeds_';
	
	/**
	 * 接口名称
	 * @var unknown_type
	 */
	const INTERFACE_NAME = 'SubmitFeed';
	
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
	const RESTORE_TIME_NUM = 2;
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
	 * 提交上传数据
	 * @param unknown_type $FeedContent
	 */
	public function submitFeed($FeedContent,$FeedType){
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

		//$fulfillmentDate = gmdate("Y-m-d\TH:i:s\Z");//new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('UTC'));
		
		$feedHandle = @fopen('php://temp', 'rw+');
		fwrite($feedHandle, $FeedContent);
		rewind($feedHandle);
		$parameters = array(
				'Merchant' => $this->_tokenConfig['MERCHANT_ID'],
				'MarketplaceIdList' => array("Id" => array($amazonConfig[$countryCode]['marketplace_id'])),
				'FeedType' => $FeedType,
				'FeedContent' => $feedHandle,
				'PurgeAndReplace' => false,
				'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true))
				);
		rewind($feedHandle);
		
		$request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
		
// 		echo '<br/><br/>';
// 		print_r($this->_tokenConfig);
// 		echo '<br/><br/>';
// 		print_r($parameters);
// 		echo '<br/><br/>';
// 		print_r($request);
// 		echo '<br/><br/>';
// 		print_r($service);
// 		echo '<br/><br/>';
// 		print_r($FeedContent);
// 		echo '<br/><br/>';
// 		$xmlll = simplexml_load_string($FeedContent);
// 		var_dump($xmlll);
// 		$data = XML_unserialize($xmlll);
// 		echo '<br/><br/>';
// 		echo '----------------';
// 		echo '<br/><br/>';
// 		var_dump($data);
// 		exit;
		try{
			echo self::$log_name . $i++ . ':开始调用<br/><br/>';
			$response = $service->submitFeed($request);
			echo self::$log_name . $i++ . ':调用结束<br/><br/>';
// 			$response = $this->invokeSubmitFeed($service, $request);
			$return['ask'] = 1;
			$return['data'] = $response;
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 2);
		} catch (MarketplaceWebService_Exception $ex) {
			echo self::$log_name . $i++ . ':调用异常<br/><br/>';
			$errorMessage = '错误代码：' . $ex->getErrorCode() . '--请求ID：' . $ex->getRequestId() . '--错误信息：' . print_r($ex,true);
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 处理：$startDate ~~ $endDate 时间段内的订单任务发生异常,错误原因：" . print_r($ex,true), self::$log_name);
		}catch (Exception $e){
			echo self::$log_name . $i++ . ':调用异常<br/><br/>';
			$errorMessage = '错误代码：' . $e->getCode() . '--错误信息：' . $e->getMessage();
			Amazon_AmazonLib::closeAmazonRunControl($returnCheck['paramId'], 3 , $errorMessage);
			$return['message'] = $errorMessage;
			Ec::showError($this->_tokenConfig['MERCHANT_ID'] . '--' . $this->_tokenConfig['SITE'] . " -> 处理：$startDate ~~ $endDate 时间段内的订单任务发生异常,错误原因：" . print_r($e,true), self::$log_name);
		}
		@fclose($feedHandle);
		return $return;
		
	}
	
	public function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request)
	{
		try {
			$response = $service->submitFeed($request);
	
			echo ("Service Response\n");
			echo ("=============================================================================\n");
	
			echo("        SubmitFeedResponse\n");
			if ($response->isSetSubmitFeedResult()) {
				echo("            SubmitFeedResult\n");
				$submitFeedResult = $response->getSubmitFeedResult();
				if ($submitFeedResult->isSetFeedSubmissionInfo()) {
					echo("                FeedSubmissionInfo\n");
					$feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
					if ($feedSubmissionInfo->isSetFeedSubmissionId())
					{
						echo("                    FeedSubmissionId\n");
						echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
					}
					if ($feedSubmissionInfo->isSetFeedType())
					{
						echo("                    FeedType\n");
						echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
					}
					if ($feedSubmissionInfo->isSetSubmittedDate())
					{
						echo("                    SubmittedDate\n");
						echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
					}
					if ($feedSubmissionInfo->isSetFeedProcessingStatus())
					{
						echo("                    FeedProcessingStatus\n");
						echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
					}
					if ($feedSubmissionInfo->isSetStartedProcessingDate())
					{
						echo("                    StartedProcessingDate\n");
						echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
					}
					if ($feedSubmissionInfo->isSetCompletedProcessingDate())
					{
						echo("                    CompletedProcessingDate\n");
						echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
					}
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