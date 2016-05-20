<?php
/**
 * amazon解析标记发货报告自动服务
 * @author Frank
 * @date 2013-11-26 11:12:41
 */ 
require_once('XmlHandle.php');
class Amazon_AmazonOrderFulfillmentResultService extends Ec_AutoRun{
/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $log_name = 'runOrderFulfillmentResult_';
	
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
	}
	
	
	/**
	 * 在AutoRun调用中被调用的方法，自动同步程序的入口
	 * @param unknown_type $loadId
	*/
	public function callOrderFulfillmentResult($loadId){
		return $this->runOrderFulfillmentResult($loadId);
	}
	
	public function runOrderFulfillmentResult($loadId){
		
		$i = 1;
		echo $i++ . ':进入服务<br/><br/>';
		
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
		
		echo $i++ . ":下载amazon订单时间段 $start ~ $end <br/><br/>";
		
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
		 * 3. 查询上传记录
		 */
		$conSubmitFeed = array(
				'type'=>$this->FeedType,
				'feed_processing_status'=>'_SUBMITTED_',
				'user_account'=>$amazonAccount
				);
		
		$resultSubmitFeed = Service_AmazonSubmitFeed::getByCondition($conSubmitFeed, '*', 5, $page = 1, $order = "sys_last_mod_date asc");
// 		print_r($resultSubmitFeed);
		
		if(empty($resultSubmitFeed)){
			echo $i++ . ':暂无上传记录，需要处理<br/><br/>';
			$this->countLoad($loadId, 2, 0);
			return array(
					'ask' => '1',
					'message' => "amazon账户：'$amazonAccount',已处理：$start ~~ $end 时间段内的上传记录报告."
			);
		}
		
		echo $i++ . ':循环查询上传记录处理结果<br/><br/>';
		/*
		 * 4. 查询上传记录的最新的处理状态
		 */
		$uploadRows = array();
		$batchIdArr = array();
		foreach ($resultSubmitFeed as $uploadKey => $uploadValue) {
			$row['batch_id'] = $uploadValue['feed_submission_id'];
			$row['sys_status'] = $uploadValue['feed_processing_status'];
			$row['amazon_status'] = '';
			$row['amazon_process_date'] = '';
			$row['file_name'] = '';
			$uploadRows[$row['batch_id']] = $row;
			$batchIdArr[] = $row['batch_id'];
		}
		
// 		$uploadRows['65781016603']['amazon_status'] = '_DONE_';
// 		$uploadRows['65781016603']['amazon_process_date'] = '2015-06-17T17:27:37Z';
// 		$uploadRows['65781016603']['file_name'] = '2015-07-16_65781016603.xml';
		
		echo '查询前  $uploadRows => '; 
		print_r($uploadRows);
		echo '<br><br>';
		echo '查询前  $batchIdArr => ';
		print_r($batchIdArr);
		echo '<br/><br/>';
// 		exit;
		
		$loopQuery = true;
		$loopIndex = 0;
		$objGetFeedSubmissionListService =  new Amazon_GetFeedSubmissionListService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site'], $this->_configSecretKey['auth_token']);
		while ($loopQuery){
			try {
				$responseList =  $objGetFeedSubmissionListService->getFeedSubmissionList($batchIdArr);
// 				print_r($responseList);exit;
				if($responseList['ask'] == 1 && empty($responseList['message'])){
					$loopQuery = false;
					echo $i++ . ':查询处理状态成功<br/><br/>';
					if ($responseList['data']->isSetGetFeedSubmissionListResult()) {
						$getFeedSubmissionListResult = $responseList['data']->getGetFeedSubmissionListResult();
						$feedSubmissionInfoList = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
						//改变数组$uploadRows对应的记录
						foreach ($feedSubmissionInfoList as $feedSubmissionInfo) {
							if ($feedSubmissionInfo->isSetFeedType() && $feedSubmissionInfo->getFeedType() != $this->FeedType)
							{
								continue;
							}
							if ($feedSubmissionInfo->isSetFeedProcessingStatus())
							{
								$uploadRows[$feedSubmissionInfo->getFeedSubmissionId()]['amazon_status'] = $feedSubmissionInfo->getFeedProcessingStatus();
							}
							if ($feedSubmissionInfo->isSetStartedProcessingDate())
							{
								$uploadRows[$feedSubmissionInfo->getFeedSubmissionId()]['amazon_process_date'] = $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT);
							}
						}
					}
				}
			} catch (Exception $e) {
				//允许出现多次错误，不返回
				echo $i++ . ':调用Amazon_GetFeedSubmissionListService 出现异常<br/><br/>';
			}
			//最大错误上限
			$loopIndex++;
			if($loopIndex == Amazon_GetFeedSubmissionListService::REQUEST_MAX){
				echo $i++ . ':调用Amazon_GetFeedSubmissionListService 到达异常上限，退出<br/><br/>';
				$this->countLoad($loadId, 3, 0);
				return array(
						'ask' => '0',
						'message' => "amazon账户：'$amazonAccount',处理：$start ~~ $end 时间段内的上传记录报告，查询不到amazon的处理状态"
				);
			}
		}
		
		
		/*
		 * 5. 循环，排除那些没有亚马逊处理结果的上传记录
		 */
		echo $i++ . ':剔除没有处理结果的上传记录<br/><br/>';
		foreach ($uploadRows as $removeKey => $removeValue) {
			if(empty($removeValue['amazon_status']) || $removeValue['amazon_status'] != '_DONE_'){
				unset($uploadRows[$removeKey]);
			}
		}
		ksort($uploadRows);
		echo '查询后  $uploadRows => '; 
		print_r($uploadRows);
		echo '<br/><br/>';
// 		exit;
		
		/*
		 * 6. 下载上传记录的处理结果
		 */
		$downloadFileArr = array();
		$objGetFeedSubmissionResultService = new Amazon_GetFeedSubmissionResultService($this->_configSecretKey['token_id'], $this->_configSecretKey['token'], $this->_configSecretKey['seller_id'], $this->_configSecretKey['site'], $this->_configSecretKey['auth_token']);
		echo $i++ . ':调用Amazon_GetFeedSubmissionResultService 下载报告<br/><br/>';
		foreach ($uploadRows as $downloadKey => $downloadValue) {
			try {
				echo $i++ . ':上传ID' . $downloadKey . ',开始下载报告<br/><br/>';
				$responseResult = $objGetFeedSubmissionResultService->getFeedSubmissionResult($downloadKey, $this->FeedType);
				echo $i++ . ':上传ID' . $downloadKey . ',下载报告结束<br/><br/>';
				if($responseResult['ask'] == 1 && !empty($responseResult['file_name'])){
					if ($responseResult['data']->isSetResponseMetadata()) {
						$uploadRows[$downloadKey]['file_name'] = $responseResult['file_name'];
					}
				}else{
					//记录异常
					Ec::showError(print_r($responseResult,true), self::$log_name .'_download');
				}
			} catch (Exception $e) {
				//出现错误不做处理
				echo $i++ . ':调用Amazon_GetFeedSubmissionResultService 出现异常<br/><br/>';
			}
		}
		
		/*
		 * 7. 循环，排除下载报告失败的记录
		*/
		echo $i++ . ':剔除下载报告失败的记录<br/><br/>';
		foreach ($uploadRows as $removeKey => $removeValue) {
			if(empty($removeValue['file_name'])){
				unset($uploadRows[$removeKey]);
			}
		}
		ksort($uploadRows);
		echo '下载后  $uploadRows => ';
		print_r($uploadRows);
		echo '<br/><br/>';
// 		exit;
		
		echo $i++ . ':开始解析上传报告<br/><br/>';
		$path =  Amazon_AmazonSubmitFeedLib::getReportPath($this->FeedType);
// 		echo $path;
// 		echo '<br/><br/>';
		
		$updateRowNum = 0;
		foreach ($uploadRows as $resolveKey => $resolveValue) {
			$file_path = $path . '/' . $resolveValue['file_name'];
			$data = XML_unserialize(file_get_contents($file_path));
// 			print_r($data);
// 			exit;
			if(!empty($data)){
				//没有错误，直接将上传记录对应的订单全部标记为发货
				$amazonBtId =  $data['AmazonEnvelope']['Message']['ProcessingReport']['DocumentTransactionID'];
				$amazonProcessStatus = $data['AmazonEnvelope']['Message']['ProcessingReport']['StatusCode'];
				$amazonSuccessNum = $data['AmazonEnvelope']['Message']['ProcessingReport']['ProcessingSummary']['MessagesSuccessful'];	//成功数量
				$amazonErrorNum = $data['AmazonEnvelope']['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithError'];		//异常数量
				$amazonWarningNum = $data['AmazonEnvelope']['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithWarning'];	//警告数量
				$amazonProcessedNum = $data['AmazonEnvelope']['Message']['ProcessingReport']['ProcessingSummary']['MessagesProcessed'];	//处理数量
				$is_Great = '[NO]';
				if($amazonErrorNum == 0){
					$is_Great = '[YES]';
				}
				$process_message = "Greate：$is_Great 账户：$amazonAccount 上传报告ID：$amazonBtId ，处理状态：$amazonProcessStatus ，共处理：$amazonProcessedNum ，成功数量：$amazonSuccessNum ，异常数量：$amazonErrorNum ，警告数量：$amazonWarningNum ;";
				echo $i++ . ':' . $process_message . '<br/><br/>';
				Ec::showError($process_message, self::$log_name);
// 				exit;
// 				print_r($amazonBtId);
// 				echo '<br/><br/>';
// 				print_r($amazonProcessStatus);
// 				echo '<br/><br/>';
// 				print_r($amazonErrorNum);
				if($amazonProcessStatus == 'Complete' && $amazonErrorNum == 0){
					echo $i++ . ':账户：'.$amazonAccount.',上传报告ID：'.$amazonBtId.' 正常<br/><br/>';
// 					exit;
					//更新标记发货的记录
					$num = $this->updateOrderProduct($amazonAccount,$amazonBtId, null);
					$updateRowNum += $num;
				}else{
					echo $i++ . ':' .'账户： '.$amazonAccount.',上传报告ID:' .$amazonBtId. ' 存在异常<br/><br/>';
// 					exit;
					//暂不处理有错误的订单，因为标记发货为程序自动处理，有错误，也是系统级的····
					$errorArr = $data['AmazonEnvelope']['Message']['ProcessingReport']['Result'];
					$errorMessageIdArr = array();
					foreach ($errorArr as $key => $value) {
						$errorMessageIdArr[$value['MessageID']] = $value['ResultDescription'];
					}
					$num = $this->updateOrderProduct($amazonAccount,$amazonBtId, $errorMessageIdArr);
					$updateRowNum += $num;
				}
			}
		}
		
		echo $i++ . ":更新Amazon的发货订单记录完成，共计 '$updateRowNum' 条<br/><br/>";
		$this->countLoad($loadId, 2,$updateRowNum);
		return array(
				'ask' => '1',
				'message' => "amazon账户：$amazonAccount,在: '$start' ~ '$end' 更新."
		);
	}
	
	/**
	 * 更新标记发货的订单，记录为成功,或为失败
	 * @param unknown_type $batchId
	 * @param unknown_type $errorMessageIdArr
	 */
	public function updateOrderProduct($amazonAccount,$batchId,$errorMessageIdArr){
		//明细日志
		$uop_olog = self::$log_name . '_detail';
		//时间戳
		$date_str = '时间戳：' . date('YmdHis') . ' ';
		Ec::showError("◤-----------------------------------$batchId start-----------------------------------◥",$uop_olog);
		/*
		 * 1. 查询上传记录表
		 */
		$resultAmazonSubmitFeed =  Service_AmazonSubmitFeed::getByField($batchId,'feed_submission_id');
// 		print_r($resultAmazonSubmitFeed);
		/*
		 * 2.查询对应关系表
		 */
		$conMapping = array(
				'asf_id'=>$resultAmazonSubmitFeed['asf_id']
				);
		$resultAmazonSubmitFeedMapped = Service_AmazonSubmitFeedMapped::getByCondition($conMapping);
		Ec::showError($date_str . "异常标发订单,账户：$amazonAccount ,上传报告ID：$batchId , 标记明细数量：" . count($errorMessageIdArr), $uop_olog);
		//剔除异常的信息
		foreach ($resultAmazonSubmitFeedMapped as $key0 => $value0) {
			if(isset($errorMessageIdArr[$value0['message_id']])){
				unset($resultAmazonSubmitFeedMapped[$key0]);
				Service_AmazonSubmitFeedMapped::update(array('sync_message'=>$errorMessageIdArr[$value0['message_id']]), $value0['asfm_id']);
			}else{
				Service_AmazonSubmitFeedMapped::update(array('sync_message'=>'Success'), $value0['asfm_id']);
			}
		}
		ksort($resultAmazonSubmitFeedMapped);
		
// 		print_r($resultAmazonSubmitFeedMapped);
		Ec::showError($date_str . "正常标发订单,账户：$amazonAccount ,上传报告ID：$batchId ,标记明细数量：" . count($resultAmazonSubmitFeedMapped), $uop_olog);
		$model = Service_OrderProduct::getModelInstance();
		$db = $model->getAdapter();
		$db->beginTransaction();
		
		$retrun_num = 0;
		try {

			//更新order_product
			$orderIDArr = array();
			if(!empty($resultAmazonSubmitFeedMapped)){
				foreach ($resultAmazonSubmitFeedMapped as $key1 => $value1) {
					$result = $model->update(array('sync_status'=>'1'), $value1['mapped_val2'],'op_id');
	// 				echo 'A--:' . $result;
	// 				echo '<br/><br/>';
					$orderIDArr[$value1['mapped_val1']] = $value1['mapped_val1'];
				}
				
				//更新orders
				foreach ($orderIDArr as $key2 => $value2) {
					$result = Service_Orders::update(array('sync_status'=>'1','sync_time'=>date('Y-m-d H:i:s'),'platform_ship_status'=>'1'), $key2,'refrence_no_platform');
					$order_log_content = 'Amazon订单标记发货成功';
					//成功--记录日志
					$logRow = array(
							'ref_id' => $key2,
							'log_content' => $order_log_content,
							'data' => print_r($value1,true),
							'op_id' => '9'
					);
					Service_OrderLog::add($logRow);
	// 				echo 'B--:' . $result;
	// 				echo '<br/><br/>';
				}
				
				//更新amazon_submit_feed
				$result = Service_AmazonSubmitFeed::update(array('feed_processing_status'=>'_DONE_','sys_last_mod_date'=>date('Y-m-d H:i:s')), $resultAmazonSubmitFeed['asf_id'],'asf_id');
	// 			echo 'C--:' . $result;
	// 			echo '<br/><br/>';
				
			}
			$db->commit();
			Ec::showError($date_str . "标记发货完成,账户：$amazonAccount ,上传报告ID：$batchId , 标记成订单：" . count($orderIDArr), $uop_olog);
			$retrun_num = count($orderIDArr);
		} catch (Exception $e) {
			$db->rollBack();
			$date = date('Y-m-d H:i:s');
			Ec::showError("amazon账户：'$amazonAccount',在 '$date'更新标记发货订单数据时出现异常,错误原因：".$e->getMessage(), self::$log_name);
		}
		
		Ec::showError("◣-----------------------------------$batchId end-----------------------------------◢",$uop_olog);
		return $retrun_num;
	}
}