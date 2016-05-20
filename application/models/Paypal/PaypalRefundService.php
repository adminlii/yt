<?php
/**
 * Paypal退款服务
 * @author Frank
 * @date 2013-8-16 13:38:26
 *
 */
class Paypal_PaypalRefundService extends Ec_AutoRun{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $runPaypalRefund_ = 'runPaypalRefund_';
	
	/**
	 * Paypal API签名信息
	 */
	private static $api_username = null;
	private static $api_password = null;
	private static $api_signature = null;
	
	/**
	 * 构造器
	 */
	public function __construct()
	{
		set_time_limit(0);
	}
	/**
	 * 在AutoRun调用中被调用的方法，自动同步程序的入口
	 * @param unknown_type $loadId 
	 */
	public function callRefundTransaction($loadId){
		return $this->runRefundTransaction($loadId);
	}
		
	/**
	 * paypal退款
	 * @see Ec_AutoRun::run()
	 */
	public function runRefundTransaction($loadId){
		$i = 1;
		echo $i++ . ':进入服务<br/><br/>';
		
		/*
		 * 1.加载当前同步程序的控制参数
		 */
		$param 		 = $this->getLoadParam($loadId);
		echo $i++ . ':加载任务参数<br/><br/>';
		$companyCode = $param['company_code'];				//公司代码
		$paypalAccount = $param["user_account"];			//绑定的paypal账户
		$start 		 = $param["load_start_time"];			//开始时间（美国时间）
		$end    	 = $param["load_end_time"];				//结束时间（美国）
		$count 		 = $param["currt_run_count"];			//当前运行第几页
		$start 		 = date('Y-m-d H:i:s',strtotime($start)+3600*8);	//转换时间格式（北京时间）
		$end 		 = date('Y-m-d H:i:s',strtotime($end)+3600*8);  	//转换时间格式（北京时间）
		
		echo $i++ . ":审核时间段 $start ~ $end <br/><br/>";
		
		/*
		 * 2.查询paypal签名信息
		 */
		$resultEbayPaypal = Service_EbayPaypal::getByCondition(array('company_code'=>$companyCode,'paypal_account'=>paypal_account));
// 		$resultEbayPaypal = Service_EbayPaypal::getByField($paypalAccount,'paypal_account');
		echo $i++ . ':查询paypal签名<br/><br/>';
		if(empty($resultEbayPaypal)){
			$errorMessage = "paypal账户：$paypalAccount 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$runPaypalRefund_);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}
		$ebayAccount = $resultEbayPaypal[0]["ebay_account"];				//绑定的ebay账户
		self::$api_username = $resultEbayPaypal[0]['name'];
		self::$api_password = $resultEbayPaypal[0]['pass'];
		self::$api_signature = $resultEbayPaypal[0]['signature'];
		
		/*
		 * 3.查询时间段内的退款数据
		 */
		$rmaOrderCondition = array(
// 				'verifyDateFrom' => $start,
// 				'verifyDateEnd'  => $end,
				'rma_status'     => '2',
// 				'rma_ebay_account' => $ebayAccount,
				'rma_payment_account' => $paypalAccount,
				'is_not_rma_refund_type'=>-1
				);
		
		$resultRmaOrders = Service_RmaOrders::getByCondition($rmaOrderCondition);
		$resultRmaOrdersLength = count($resultRmaOrders);
		echo $i++ . ":查询退款数据(条数==>$resultRmaOrdersLength)<br/><br/>";
		//exit;
		
		/*
		 * 4.循环处理，退款数据
		 */
		if(!empty($resultRmaOrders)){
			echo $i++ . ":循环退款数据，封装参数<br/><br/>";
			foreach ($resultRmaOrders as $item) {
				try {
					if($item['rma_pay_ref_id'] != 'b2c'){
						/*
						 * 4.1 ebay平台订单，直接退款，
						 */
						$nvpStr = $this->getNvpStr($item);
						echo $i++ . ":封装paypal平台退款参数,交易ID：$item[rma_pay_ref_id]<br/><br/>";
						echo '请求参数 ' . $nvpStr . '<br/><br/>';
						$paypalResponse = Paypal_PaypalLib::call_RefundTransaction($nvpStr);
						echo $i++ . "调用paypal接口，执行退款,交易ID：$item[rma_pay_ref_id]<br/><br/>";
						
						$syncDate = date('Y-m-d H:i:s');//同步时间
						if("SUCCESS" == strtoupper($paypalResponse["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($paypalResponse["ACK"])) {
							echo $i++ . "退款执行<font style='color:red;'>成功</font>,交易ID：$item[rma_pay_ref_id]<br/><br/>";
							$rmaRow = array(
								'rma_sync_time'=> $syncDate,
								'rma_status' => '3',
								'rma_sync_message' =>''
								);
						} else  {
							echo $i++ . "退款执行<font style='color:red;'>失败</font>,交易ID：$item[rma_pay_ref_id]<br/><br/>";
							$message = '<MESSAGE>' . urldecode($paypalResponse['L_SHORTMESSAGE0']) . '</MESSAGE>';
							$message .= ' <MESSAGE>' . urldecode($paypalResponse['L_LONGMESSAGE0']) . '</MESSAGE>';
							$rmaRow = array(
									'rma_sync_time'=> $syncDate,
									'rma_status' => '4',
									'rma_sync_message' => $message
							);
						}
						
					}else{
						/*
						 * 4.2 B2C平台使用打款接口（未开发）
						 */
						echo $i++ . ":封装B2C平台退款参数<br/><br/>";
						echo $i++ . ":B2C平台暂不支持退款<br/><br/>";
						//打款接口....
						$message = '<MESSAGE>' . '非ebay平台订单，无paypal交易ID，暂不支持退款' . '</MESSAGE>';
						$rmaRow = array(
								'rma_sync_time'=> $syncDate,
								'rma_status' => '4',
								'rma_sync_message' => $message
						);
					}
					Service_RmaOrders::update($rmaRow, $item['rma_id']);
					echo $i++ . "更新退件主表，rma_id:$item[rma_id]<br/><br/>";
					Service_RmaOrderProduct::update(array('rmap_sync_time'=>$syncDate), $item['rma_id'],'rma_id');
					echo $i++ . "更新退件成品表，rma_id:$item[rma_id]<br/><br/>";
				} catch (Exception $e) {
					/*
					 * 运行异常
					 */
					$this->countLoad($loadId, 3,0);
					Ec::showError("paypal账户：$paypalAccount,处理：$start ~~ $end 时间段内的退款任务发生异常,错误原因：".$e->getMessage(), self::$runPaypalRefund_);
					return array('ask'=>'0','message'=>$e->getMessage());
				}
			}
		}else{
			//无处理数据，略过就好~么么哒
			echo $i++ . ":无退款数据<br/><br/>";
		}
		/*
		 * 4.3 处理完成，更新数据控制表
		 */
		$this->countLoad($loadId, 2, $resultRmaOrdersLength);
		return array(
				'ask' => '1',
				'message' => "paypal账户：$paypalAccount,已处理：$start ~~ $end 时间段内的退款任务."
		);
	}
	
	/**
	 * 组装退款借口请求参数url
	 * @param unknown_type $item
	 * @throws Exception
	 * @return string
	 */
	public function getNvpStr($item){
		/*
		 * 1.设置api签名
		*/
		$API_UserName	= self::$api_username;
		$API_Password	= self::$api_password;
		$API_Signature	= self::$api_signature;
		$nvpStr = "&USER=".urlencode($API_UserName);
		$nvpStr .= "&PWD=".urlencode($API_Password);
		$nvpStr .= "&SIGNATURE=".urlencode($API_Signature);
		
		/*
		 * 2. 确认退款数据
		*/
		$transactionID		= urlencode($item['rma_pay_ref_id']);				//交易ID
		if($item['rma_refund_type'] == '0'){									//退款类型：a、Full，全额退款；b、Partial，部分退款
			$refundType = urlencode('Full');
		}else if($item['rma_refund_type'] == '1'){
			$refundType = urlencode('Partial');
		}else{
			throw new Exception("paypal退款类型异常，类型：$item[rma_refund_type]，RMA_ID：$item[rma_id]");
		}		
		$currencyCode		= urlencode($item['rma_currency']);					//币种，举个栗子：USD
		$amount				= $item['rma_amount_total'];						//金额：适用于部分退款
		
		/*
		 * 3. 拼接条件,退款类型不同，参数不一样
		*/
		$nvpStr .= "&TRANSACTIONID=$transactionID";
		$nvpStr .= "&REFUNDTYPE=$refundType";
		$nvpStr .= "&CURRENCYCODE=$currencyCode";
		
		if($refundType == 'Full'){
			$nvpStr .= "&NOTE=$itme[rma_common]";
		}else{
			$nvpStr .= "&AMT=$amount&NOTE=$item[rma_common]";
		}
		
		return $nvpStr;
	} 
	
	
}