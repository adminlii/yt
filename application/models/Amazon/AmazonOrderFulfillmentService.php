<?php
/**
 * amazon标记发货自动服务
 * @author Frank
 * @date 2013-11-26 11:12:41
 */
class Amazon_AmazonOrderFulfillmentService extends Ec_AutoRun{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'runOrderFulfillment_';
	
	/**
	 * 秘钥信息
	 * @var unknown_type
	 */
	private $_configSecretKey = array(
			'token_id' => null,
			'token' => null,
			'site' => null,
			'seller_id'=>null,
			'auth_token'=>null,
	);
	
	/**
	 * 上传类型
	 */
	private $FeedType = '_POST_ORDER_FULFILLMENT_DATA_';
	
	/**
	 * 构造器
	*/
	public function __construct()
	{
		self::$log_name .= date('Y-m-d');
		set_time_limit(0);
		//生成目录
		$app_path = APPLICATION_PATH;
		 
		$amazon_path_01 = '/../data/xml/amazon/PaymentSettlement';
		$amazon_path_02 = '/../data/xml/amazon/OrderFulfillment';
		$arr_path = array(
				$app_path . $amazon_path_01,
				$app_path . $amazon_path_02
		);
		foreach ($arr_path as $key => $value) {
			if(!is_dir($value)){
// 				echo '生成目录：' . $value;
// 				echo '<br><br>';
				Common_Common::mkdirs($value);
			}else{
				//存在，跳过
// 				echo '目录已存在：' . $value;
// 				echo '<br><br>';
			}
		}
	}
	
	
	/**
	 * 在AutoRun调用中被调用的方法，自动同步程序的入口
	 * @param unknown_type $loadId
	*/
	public function callOrderFulfillment($loadId){
		return $this->runOrderFulfillment($loadId);
	}
	
	/**
	 * 标记发货
	 */
	public function runOrderFulfillment($loadId){
		$i = 1;
		echo $i++ . ':进入amazon标记发货服务<br/><br/>';
		
		/*
		 * 1. 加载当前同步程序的控制参数
		*/
		$param 		 = $this->getLoadParam($loadId);
		echo $i++ . ':加载任务参数<br/><br/>';
		$amazonAccount = $param["user_account"];			//绑定的amazon账户
		$company_code = $param['company_code'];				//公司代码
		$start 		 = $param["load_start_time"];			//开始时间（美国时间）
		$end    	 = $param["load_end_time"];				//结束时间（美国）
		$count 		 = $param["currt_run_count"];			//当前运行第几页
		
		echo $i++ . ":标记发货时间段 $start ~ $end <br/><br/>";
		
		/*
		 * 2. 查询amazon用户 Token信息
		*/
		$resultPlatformUser = Service_PlatformUser::getByField($amazonAccount,'user_account');
		echo $i++ . ':查询amazon签名<br/><br/>';
		if(empty($resultPlatformUser)){
			$errorMessage = "amazon账户：'$amazonAccount' 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$log_name);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}
		$this->_configSecretKey['token_id'] = $resultPlatformUser["user_token_id"];
		$this->_configSecretKey['token'] = $resultPlatformUser["user_token"];
		$this->_configSecretKey['site'] = $resultPlatformUser["site"];
		$this->_configSecretKey['seller_id'] = $resultPlatformUser["seller_id"];
		$this->_configSecretKey['auth_token'] = $resultPlatformUser["mws_auth_token"];
		
		/*
		 * 3.查询需要同步发货信息到amazon的订单
		*/
		$conOrders = array(
				'platform'=>'amazon',
				'order_type'=>'sale',
				'create_type'=>'api',
				'order_status'=>'3',
				'csd_order_status'=>'C',	// 只回写出库的订单
				'fulfillment_channel'=>'MFN',
				'sync_status_arr'=>array('0','3','6'),
				'user_account'=>$amazonAccount
				);
		$resultOrders = Service_Orders::getByConditionJoinCsdOrder($conOrders, '*', 100, 1, "date_create_platform asc");
// 		print_r($resultOrders);
		echo '<br/><br/>';
		if(count($resultOrders) == 0){
			$this->countLoad($loadId, 2,0);
			return array(
					'ask' => '1',
					'message' => "amazon账户：$amazonAccount,在: '$start' ~ '$end' 内没有数据需要标记发货"
			);
		} 
		/*
		 * 4. 查询订单所属SKU信息用于同步标记
		 */
		$ordersArr = array();
		foreach ($resultOrders as $orderKey => $orderValue) {
			$ordersArr[] = $orderValue['order_id'];
		}
		$conOrderProduct = array(
				'order_id_arr'=>$ordersArr,
				'give_up'=>'0'		//表示有效数据
// 				'sync_status'=>'0'	//因为订单可能需要更新跟踪号等，所以需要再次同步，所以不再控制Item的同步状态
				);
		$resultOrderProduct =  Service_OrderProduct::getByCondition($conOrderProduct, '*', 0, 1, "OrderIDEbay asc");
		echo '<br/><br/>';
// 		print_r($resultOrderProduct);
		
		/*
		 * 5. 查询运输方式，对应的amazon服务商信息，用于同步标记
		 */
		$conShippingMethodPlatform = array(
				'platform'=>'amazon'
				);
		$resultSMP = Service_ShippingMethodPlatform::getByCondition($conShippingMethodPlatform);
		
		$amzonData =  Amazon_AmazonSubmitFeedLib::getOrderFulfillmentXML($this->_configSecretKey['seller_id'], $resultOrders, $resultOrderProduct,$resultSMP);
		
		echo '<br/><br/>';
		print_r($amzonData);
// 		exit;
		if(!$amzonData['ask']){
			$this->countLoad($loadId, 2,0);
			return array (
					'ask' => '1',
					'message' => ''
			);
		}
		$FeedContent = $amzonData['xml'];
		
		
		/*
		 * 5. 创建amazon标记发货服务
		*/
		echo $i++ . ':new Amazon_SubmitFeedService()并调用<br/><br/>';
		$OrderFulfillment = new Amazon_SubmitFeedService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site'], $this->_configSecretKey['auth_token']);
		$orderFulfillmentResponse = array();
		for ($for_index = 0; $for_index < (Amazon_SubmitFeedService::REQUEST_MAX - 5); $for_index++) {
			$orderFulfillmentResponse = $OrderFulfillment->submitFeed($FeedContent, $this->FeedType);
			if($orderFulfillmentResponse['ask'] == '1' && !empty($orderFulfillmentResponse['data'])){
				break;	
			}
		}
		
		/*
		 * 6. 保存上传文件类型，数据到DB
		 */
		if($orderFulfillmentResponse['ask'] == '1' && !empty($orderFulfillmentResponse['data'])){
			echo $i++ . ':Amazon_SubmitFeedService 调用成功<br/><br/>';
			$response = $orderFulfillmentResponse['data'];
			$row['user_account'] = $amazonAccount;
			$row['type'] = $this->FeedType;
			$row['feed_content'] = $FeedContent;
			$row['sys_last_mod_date'] = date('Y-m-d H:i:s');
			if($response->isSetSubmitFeedResult()){
				echo $i++ . ':组织返回参数，<br/><br/>';
				$submitFeedResult = $response->getSubmitFeedResult();
				if ($submitFeedResult->isSetFeedSubmissionInfo()) {
					$feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
					if ($feedSubmissionInfo->isSetFeedSubmissionId())
					{
						$row['feed_submission_id'] = $feedSubmissionInfo->getFeedSubmissionId();
					}
					if ($feedSubmissionInfo->isSetFeedType())
					{
						$row['type'] = $feedSubmissionInfo->getFeedType();
					}
					if ($feedSubmissionInfo->isSetFeedProcessingStatus())
					{
						$row['feed_processing_status'] = $feedSubmissionInfo->getFeedProcessingStatus();
					}
				}
			}
			if ($response->isSetResponseMetadata()) {
				$responseMetadata = $response->getResponseMetadata();
				if ($responseMetadata->isSetRequestId())
				{
					$row['request_id'] = $responseMetadata->getRequestId();
				}
			}
			$model = Service_AmazonSubmitFeed::getModelInstance();
			$db = $model->getAdapter();
			try {
				$db->beginTransaction();
				//保存上传记录
				$asf_id = $model->add($row);
				//保存对应关系
				foreach ($amzonData['data'] as $key1 => $value1) {
					//更新订单记录为"已提交到Amazon，等待处理结果"
					Service_Orders::update(array('sync_status'=>'4','sync_time'=>date('Y-m-d H:i:s')), $key1,'refrence_no_platform');
					$log_tracking_no = '';
					$log_append = '';
					foreach ($value1 as $key2 => $value2) {
						$value2['asf_id'] = $asf_id;
						$log_tracking_no = $value2['tracking_no'];
						$log_append = $value2['append_log'];
						unset($value2['append_log']);
						Service_AmazonSubmitFeedMapped::add($value2);
					}
					
					$order_log_content = 'Amazon订单标提交标记发货信息';
					if(!empty($log_tracking_no)){
						$order_log_content .= '，跟踪号：' . $log_tracking_no;
					}
					//标记发货追加日志
					if(!empty($log_append)){
						$order_log_content .= '【' . $log_append . '】';
					}
					
					//成功--记录日志
					$logRow = array(
							'ref_id' => $key1,
							'log_content' => $order_log_content,
							'data' => print_r($value1,true),
							'op_id' => '9'
					);
					Service_OrderLog::add($logRow);
				}
				
				//保存发货时间
				foreach ($amzonData['shipTime'] as $key_st => $value_st) {
					Service_Orders::update(array('platform_ship_status'=>'0','platform_ship_time'=>$value_st), $key_st,'refrence_no_platform');
				}
				
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				$this->countLoad($loadId, 3,0);
				$date = date('Y-m-d H:i:s');
				Ec::showError("amazon账户：'$amazonAccount',在 '$date'标记发货出现异常,错误原因：".$e->getMessage(), self::$log_name);
				return array('ask'=>'0','message'=>$e->getMessage());
			}
		}else{
			echo $i++ . ':Amazon_SubmitFeedService 调用失败<br/><br/>';
			$this->countLoad($loadId, 3,0);
			$errorMessage = "amazon账户： '$amazonAccount',(getAmazonOrdersByNextToken)运行异常->" . print_r($orderFulfillmentResponse,true);
			Ec::showError($errorMessage, self::$log_name);
			return array('ask'=>'0','message'=>$errorMessage);
		}
		
		/*
		 * 7. 返回参数
		 */
		$addRowNum = count($ordersArr);
		echo $i++ . ":amazon订单标记发货服务执行完毕,总计上传数据 $addRowNum 条<br/><br/>";
		$this->countLoad($loadId, 2,$addRowNum);
		return array(
				'ask' => '1',
				'message' => "amazon账户：$amazonAccount,已处理: '$start' ~ '$end' 的订单标记发货任务完成."
		);
	}
	
	public function invokeSubmitFeed($orderFulfillmentResponse)
	{
		try {
			$response = $orderFulfillmentResponse;
	
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