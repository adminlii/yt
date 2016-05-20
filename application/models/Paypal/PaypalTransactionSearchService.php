<?php
/**
 * Paypal收款记录拉取服务
 * @author Frank
 * @date 2013-8-17 17:17:05
 *
 */
class Paypal_PaypalTransactionSearchService extends Ec_AutoRun{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $runTransactionSearch_ = 'runTransactionSearch_';
	/**
	 * Paypal API签名信息
	 */
	private static $api_username = null;
	private static $api_password = null;
	private static $api_signature = null;
	/**
	 * 美国时差(减八小时)
	 */
	public static $timeDifference = '-8';
	/**
	 * 切割查询时间之后的查询，开始时间向前推移（减两分钟）
	 */
	public static $timePushForward = '-2';
	/**
	 * paypal收款数据
	 */
	private static $payPalSalesRows = array();
		
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
	public function callTransactionSearch($loadId){
		return $this->runTransactionSearch($loadId);
	}
	
	/**
	 * paypal收款记录拉取
	 * @see Ec_AutoRun::run()
	 */
	public function runTransactionSearch($loadId){
		$i = 1;
		echo $i++ . '、进入服务<br/><br/>';
	
		/*
		 * 1.加载当前同步程序的控制参数
		*/
		$param 		 = $this->getLoadParam($loadId);

		echo $i++ . '、加载任务参数<br/><br/>';
		$companyCode = $param['company_code'];					//公司代码
		$paypalAccount = $param["user_account"];				//绑定的paypal账户
		$start 		 = $param["load_start_time"];				//开始时间(美国时间)
		$end    	 = $param["load_end_time"];					//结束时间(美国时间)
		$count 		 = $param["currt_run_count"];				//当前运行第几页
		
		/*
		 * 2.查询paypal签名信息
		*/
		$resultEbayPaypal = Service_EbayPaypal::getByCondition(array('company_code'=>$companyCode,'paypal_account'=>paypal_account));
// 		$resultEbayPaypal = Service_EbayPaypal::getByField($paypalAccount,'paypal_account');
		echo $i++ . '、查询paypal签名<br/><br/>';
		
		if(empty($resultEbayPaypal)){
			echo $i++ . "paypal账户：‘$paypalAccount’ 未查询到签名信息";
			$errorMessage = "paypal账户：$paypalAccount 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$runTransactionSearch_);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}
		
		$ebayAccount = $resultEbayPaypal[0]['ebay_account'];
		self::$api_username = $resultEbayPaypal[0]['name'];
		self::$api_password = $resultEbayPaypal[0]['pass'];
		self::$api_signature = $resultEbayPaypal[0]['signature'];
					
		/*
		 * 3.转换为接口所需的时间格式(用作对比判断，不做更改)
		 * 	a、减去8小时
		 * 	b、转换为美国时间格式(paypal API需要)
		 *  PS：因数据库传入的时间就是美国时间，故不作处理
		 */
		$astart = $start;
		$aend = $end;
		echo $i++ . "、拉取时间段 (美国) $astart ~ $aend <br/><br/>";
		
		/*
		 * 4.封装参数，并调用请求参数(用于变更查询时间段参数)
		 */
		$requestStart = $astart;
		$requestEnd = $aend;
		$nvpStr = self::getNvpStr($requestStart, $requestEnd);
		echo $i++ . "、封装paypal查询交易记录参数，paypal账户：$paypalAccount<br/><br/>";
// 		echo '请求参数 ' . $nvpStr . '<br/><br/>';
		$callBool = true;//是否发生请求
		while(true){
			try {
				if($callBool){
					$paypalResponse = Paypal_PaypalLib::call_TransactionSeach($nvpStr);
				
				}
				/*
				 * 4.1 若查询结果被截留，重设查询条件（时间切割）
				 */
				if(strtoupper($paypalResponse["ACK"]) == "SUCCESSWITHWARNING" && 
						strtoupper($paypalResponse["L_ERRORCODE0"]) == "11002"){
					$tmpTime = self::getIntervalTime($requestStart, $requestEnd);	//计算查询时间的间隔
					echo $i++ . "、间隔时间  $tmpTime 分钟<br/><br/>";
					$tmpCut = round($tmpTime / 2);
					$requestEnd = self::getChangeTimeByMinutes($requestEnd, '-' . $tmpCut);
					$requestEnd = self::getChangeTimeByHour($requestEnd, self::$timeDifference);
					$requestEnd = self::getPaypalTimeType($requestEnd);
					$nvpStr = self::getNvpStr($requestStart, $requestEnd);
					echo $i++ . ":数据不全，缩短时间段为  $requestStart ~ $requestEnd <br/><br/>";
				}else{
					/*
					 * 4.2 处理拆分时间段后的，正常的返回结果，并再次推移时间
					 */
					echo $i++ . "、接口返回参数，时间段为  $requestStart ~ $requestEnd <br/><br/>";
					self::convertPayPalSalesDetails($paypalResponse, $paypalAccount,$companyCode);
					if($requestStart == $astart && $requestEnd == $aend){
						//一次请求查询所有数据，直接跳出循环
						break;
					}else if($requestEnd == $aend){
						//拆分请求时间段后，最后一次查询结束时间应与原始值一致，跳出循环
						break;
					}else{
						/*
						 * 4.3 防止漏掉数据，拆分时间后的查询，将开始时间向前推移几分钟
						 */
						$requestEnd = self::getChangeTimeByMinutes($requestEnd, self::$timePushForward);
						$requestEnd = self::getChangeTimeByHour($requestEnd, self::$timeDifference);
						$requestStart = self::getPaypalTimeType($requestEnd);
						$requestEnd = $aend;
						$nvpStr = self::getNvpStr($requestStart, $requestEnd);
					}
				}
			} catch (Exception $e) {
				/*
				 * 运行异常
				*/
				$this->countLoad($loadId, 3,0);
				Ec::showError("paypal账户：$paypalAccount,拉取：$start ~~ $end 时间段内付款记录异常,错误原因：".$e->getMessage(), self::$runTransactionSearch_);
				return array('ask'=>'0','message'=>$e->getMessage());
			}
		}
		
		/*
		 * 5. 验证是否存在交易记录 
		 */
		$payPalSalesRowsLength = 0;
		if(!empty(self::$payPalSalesRows)){
			foreach (self::$payPalSalesRows as $key => $value) {
				$rows = Service_PaypalTransation::getByCondition(array(
							'paypal_tid' => $value['paypal_tid'],
							'recv_account' => $value['recv_account']
						));
				//存在重复记录，剔除
				if(!empty($rows)){
					echo $i++ . ": 剔除重复数据，交易ID $value[paypal_tid]<br/><br/>";
					unset(self::$payPalSalesRows[$key]);
				}
			}
			//重新索引排序
			ksort(self::$payPalSalesRows);
		}
		
		/*
		 * 6. 检查重复后，批量插入DB
		*/
		if(!empty(self::$payPalSalesRows)){
			$model = Service_PaypalTransation::getModelInstance();
			$db = $model->getAdapter();
			try {
				$db->beginTransaction();
				foreach (self::$payPalSalesRows as $key => $value) {
					$result = $model->add($value);
					$resultEbayOrderPayment = Service_EbayOrderPayment::getByField($value['paypal_tid'],'referenceid');
					if(!empty($resultEbayOrderPayment) && empty($resultEbayOrderPayment['recv_account'])){
						Service_EbayOrderPayment::update(array('recv_account'=>$value['recv_account']), $resultEbayOrderPayment['eop_id']);
					}
				}
				$db->commit();
				echo $i++ . ": 完成数据插入<br/><br/>";
			} catch (Exception $e) {
				$db->rollBack();
				$this->countLoad($loadId, 3,0);
				Ec::showError("paypal账户：$paypalAccount,插入：$start ~~ $end 时间段内付款记录异常,错误原因：".$e->getMessage(), self::$runTransactionSearch_);
				return array('ask'=>'0','message'=>$e->getMessage());
			}
			$payPalSalesRowsLength = count(self::$payPalSalesRows);
		}else{
			echo $i++ . ": 无数据需要插入<br/><br/>";
		}
		echo $i++ . ": $astart ~ $aend 内，共有 <font style='color:red;'>$payPalSalesRowsLength</font> 条数据<br/><br/>";
		
		/*
		 * 7. 处理完成，更新数据控制表
		*/
		$this->countLoad($loadId, 2, $payPalSalesRowsLength);
		return array(
				'ask' => '1',
				'message' => "paypal账户：$paypalAccount,已拉取：$start ~~ $end 时间段内的付款记录."
		);
	}
	
	/**
	 * 得到paypalapi所需的时间格式(美国时间格式)
	 * @param unknown_type $date
	 */
	public static function getPaypalTimeType($date){
		return date('Y-m-d',strtotime($date))."T".date('H:i:s',strtotime($date))."Z";
	}
	
	/**
	 * 获取两个时间之间的间隔（分钟）
	 * @param unknown_type $start 	开始时间
	 * @param unknown_type $end		结束时间
	 * @param unknown_type $type	返回时间类型(M：分钟、S：秒)
	 */
	public static function getIntervalTime($start , $end , $type = 'M'){
		if(strtotime($end) > strtotime($start)){
			$tmpDate = strtotime($end) - strtotime($start);
		}else{
			$tmpDate = strtotime($start) - strtotime($end);
		}
		if($type == 'M'){
			$tmpDate = $tmpDate/60;
		}
		return $tmpDate;
	}
	
	/**
	 * 将某个时间向前/后推移 XX 分钟	
	 * @param unknown_type $date	时间
	 * @param unknown_type $minutes	向前推移的时间(+ 先后推移，- 先前推移)
	 * @param unknown_type $format  时间格式(默认：Y-m-d H:i:s)
	 * 
	 */
	public static function getChangeTimeByMinutes($date , $minutes ,$format = 'Y-m-d H:i:s'){
		return date($format,strtotime("$minutes minutes",strtotime($date)));
	}
	
	/**
	 * 将某个时间向前/后推移 XX 小时
	 * @param unknown_type $date	时间
	 * @param unknown_type $hour	向前推移的时间(+ 先后推移，- 先前推移)
	 * @param unknown_type $format  时间格式(默认：Y-m-d H:i:s)
	 *
	 */
	public static function getChangeTimeByHour($date , $hour ,$format = 'Y-m-d H:i:s'){
		return date($format,strtotime("$hour hour",strtotime($date)));
	}
	
	/**
	 * 组装paypal交易查询接口，所需参数
	 * @param unknown_type $astart
	 * @param unknown_type $aend
	 * @return string
	 */
	public static function getNvpStr($astart , $aend){
		/*
		 * 1.设置api签名
		*/
		$API_UserName	= self::$api_username;
		$API_Password	= self::$api_password;
		$API_Signature	= self::$api_signature;
		$nvpStr = "&USER=".urlencode($API_UserName);			//API账户
		$nvpStr .= "&PWD=".urlencode($API_Password);			//API密码	
		$nvpStr .= "&SIGNATURE=".urlencode($API_Signature);		//API签名
		
		/*
		 * 2.设置查询时间段
		 */
		$nvpStr .= "&STARTDATE=$astart";				//开始时间
		$nvpStr .= "&ENDDATE=$aend";					//结束时间
		
		/*
		 * 3.设置款项类型（收款）
		 */
		$nvpStr .= "&TRANSACTIONCLASS=Received";		//类型收款

		return $nvpStr;
	}
	
	/**
	 * 处理paypal查询接口返回的数据
	 * @param unknown_type $paypalResponse
	 * @param unknown_type $paypalAccount
	 * @param unknown_type $companyCode
	 * @return multitype:multitype:unknown string mixed
	 */
	public static function convertPayPalSalesDetails($paypalResponse,$paypalAccount,$companyCode){
		/*
		 * 加载paypal收款账户，过滤自身账户的打款记录
		 */
		$table = new DbTable_EbayPaypal();
		$db = $table->getAdapter();
		$sql = "select DISTINCT(t.paypal_account) from ebay_paypal t where t.company_code = '$companyCode'";
		$data = $db->fetchAll($sql);
		$recvAccountArr = array();
		foreach ($data as $rpKey => $rpValue) {
			$recvAccountArr[] = $rpValue['paypal_account'];
		}
		
		$sure = 0;
		$i = 0;
		$rows = array();
		while(true){
			if(array_key_exists('L_TRANSACTIONID'.$sure,$paypalResponse)){
				//付款状态
				$status		 = $paypalResponse['L_STATUS'.$sure];
				if($status == "Completed"){
					// 					$success = $success+1;
				}
				//交易类型
				$type 		 = $paypalResponse['L_TYPE'.$sure];
				//总额
				$gross		 = $paypalResponse['L_AMT'.$sure];
				//手续费
				$fee		 = $paypalResponse['L_FEEAMT'.$sure];
				//净额（减去手续费后的金额）
				$net		 = $paypalResponse['L_NETAMT'.$sure];
				//币种
				$currencyCode= $paypalResponse['L_CURRENCYCODE'.$sure];
				//发生时间
				$time	     = $paypalResponse['L_TIMESTAMP'.$sure];
				//转换时间戳
				//$time		 = strtotime($time);
				//付款人姓名
				$name	     = Paypal_PaypalLib::str_rep($paypalResponse['L_NAME'.$sure]);
				//交易ID，可用来查询交易记录详细信息
				$tid		 = $paypalResponse['L_TRANSACTIONID'.$sure];
				//关联ID
				$L_CURRENCYCODE		 = $paypalResponse['L_CURRENCYCODE'.$sure];
				//付款人email
				$email		 = $paypalResponse['L_EMAIL'.$sure];
				// 				$mail	     = $paypalResponse['L_EMAIL'.$sure];
								    	
				$successStatus = array(
						'Held',
						'Completed',
						'Cleared',
						'Partially Refunded',
						'Refunded',
						'Reversed'
						);
				if($name != "Bank Account"  && $name !='eBay, Inc' 
						&& in_array($status, $successStatus) && $type != 'Fee Revers' && !in_array($email, $recvAccountArr)){
// 					echo "<font style='color:red;'>$sure -- $i</font>、";
// 					echo "交易ID：$tid /--付款状态：$status /--交易类型：$type<br/>";
// 					echo "金额：$gross /--手续费：$fee /--净额：$net /--币种：$currencyCode <br>";
// 					echo "付款人姓名： $name /--付款人Email：$email /--时间：$time<br/><br/>";
					$row = array(
							'paypal_tid'=>$tid,
							'company_code'=>$companyCode,
							'amount_total'=>$gross,
							'fee'=>$fee,
							'amount_net'=>$net,
							'pay_account'=>$email,
							'transation_time'=>date('Y-m-d H:i:s',strtotime($time)),
							'currency'=>$currencyCode,
							'pay_name'=>$name,
							'pay_email'=>$email,
							'pay_type'=>$type,
							'pay_status'=>$status,
							'recv_account'=>$paypalAccount,
					);
					self::$payPalSalesRows[$tid] = $row;
					$rows[$tid] = $row;
					$i = $i +1;
				}
			}else{
				break;
			}
			$sure	= $sure +1;
		}
		
		return $rows;
	}
		
}