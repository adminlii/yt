<?php
/**
 * Paypal拉取付款记录明细（线下订单）
 * @author Frank
 * @date 2013-8-17 17:17:05
 *
 */
class Paypal_PaypalTransactionDetailService extends Ec_AutoRun{
	/**
	 * 日志文件名
	 * @var unknown_type
	 */
	private static $runTransactionDetail_ = 'runTransactionDetail_';
	/**
	 * Paypal API签名信息
	 */
	private static $api_username = null;
	private static $api_password = null;
	private static $api_signature = null;
	/**
	 * 美国时差(减八小时)
	 */
	private static $timeDifference = '-8';
	/**
	 * 切割查询时间之后的查询，开始时间向前推移（减两分钟）
	 */
	private static $timePushForward = '-2';
	/**
	 * paypal交易明细
	 */
	private static $payPalSalesDetailRows = array();
	/**
	 * paypal交易货品信息
	 */
	private static $payPalSalesItemDetailRows = array();
	
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
	public function callTransactionDetail($loadId){
		return $this->runTransactionDetail($loadId);
	}
	
	/**
	 * paypal收款记录明细（线下订单）
	 * @see Ec_AutoRun::run()
	 */
	public function runTransactionDetail($loadId){
		$i = 1;
		echo $i++ . '、进入服务<br/><br/>';
		
		/*
		 * 1.加载当前同步程序的控制参数
		*/
		$param 		 = $this->getLoadParam($loadId);
		
		echo $i++ . '、加载任务参数<br/><br/>';
		$companyCode = $param['company_code'];					//公司代码
		$paypalAccount = $param['user_account'];	//绑定的paypal
		$start 		 = $param["load_start_time"];				//开始时间（美国时间）
		$end    	 = $param["load_end_time"];					//结束时间（美国时间）
		$count 		 = $param["currt_run_count"];				//当前运行第几页
		$start 		 = date('Y-m-d H:i:s',strtotime($start));	//转换时间格式（北京时间）
		$end 		 = date('Y-m-d H:i:s',strtotime($end));  	//转换时间格式（北京时间）
		$start 		 = date("Y-m-d",strtotime("-2 day")) . ' 22:00:00';		//因为拉取的问题，所以直接将时间限制为一天内的间隔
		$end 		 = date("Y-m-d",strtotime("+1 day"));
		/*
		 * 2.查询paypal签名信息
		*/
		$resultEbayPaypal = Service_EbayPaypal::getByCondition(array('company_code'=>$companyCode,'paypal_account'=>paypal_account));
// 		$resultEbayPaypal = Service_EbayPaypal::getByField($paypalAccount,'paypal_account');
		echo $i++ . '、查询paypal签名<br/><br/>';
		if(empty($resultEbayPaypal)){
			$errorMessage = "paypal账户：$paypalAccount 未维护签名信息，请维护！";
			Ec::showError($errorMessage, self::$runTransactionDetail_);
			return array (
					'ask' => '0',
					'message' => $errorMessage
			);
		}
		$ebayAccount = $resultEbayPaypal[0]["ebay_account"];	//绑定的ebay账户
		self::$api_username = $resultEbayPaypal[0]['name'];
		self::$api_password = $resultEbayPaypal[0]['pass'];
		self::$api_signature = $resultEbayPaypal[0]['signature'];
		
		/*
		 * 3. 查询该时段内的，收款记录。
		 * 	  a、查询是否拉取过交易明细
		 *    a、查询是否存在ebay订单交易。
		 * 	  b、剔除存在交易明细及ebay订单的交易号，剩下去查询交易明细
		 */
		$paypalTransactionCon = array(
					'transactionDateFrom'	=>$start,
				    'transactionDateEnd'	=>$end,
					'recv_account'	     	=>$paypalAccount
				);
		$resultPaypalTransaction = Service_PaypalTransation::getByCondition($paypalTransactionCon);
		echo $i++ . '、查询paypal收款记录,共： <font style="color:red;">' . count($resultPaypalTransaction) . '</font>条<br/><br/>';
		if(!empty($resultPaypalTransaction)){
			foreach ($resultPaypalTransaction as $key => $value) {
				/*
				 * 3.1 查询是否存在ebay订单付款记录
				 */
				$resultEbayPayMent = Service_EbayOrderPayment::getByField($value['paypal_tid'],'referenceid');
				$statusSuccess = array('Succeeded','CustomCode');
				if(!empty($resultEbayPayMent) && in_array($resultEbayPayMent['paymentstatus'], $statusSuccess)){
					//若paypal交易记录表中存在该记录，剔除该条,并跳出当前循环
					unset($resultPaypalTransaction[$key]);
					echo $i++ . "、ebay存在订单付款记录，剔除重复记录，交易ID $value[paypal_tid]<br/><br/>";
					continue;
				}
				
				/*
				 * 3.2 查询是否拉取过交易明细（根据外键）
				 */
				$resultPaypalOrder = Service_PaypalOrderTransaction::getByField($value['transation_id'],'pt_id');
				if(!empty($resultPaypalOrder)){
					//若存在paypal交易明细，剔除该条，并跳出当前循环
					unset($resultPaypalTransaction[$key]);
					echo $i++ . "、已拉取过交易明细，剔除重复记录，交易ID $value[paypal_tid]<br/><br/>";
					continue;
				}
			}
			//重新索引排序
			ksort($resultPaypalTransaction);
		}
		echo $i++ . '、剔除重复记录后,共： <font style="color:red;">' . count($resultPaypalTransaction) . '</font>条<br/><br/>';
		
		/*
		 * 4.封装参数，并调用请求参数(用于变更查询时间段参数)
		 */
		if(!empty($resultPaypalTransaction)){
			foreach ($resultPaypalTransaction as $key => $value) {
				try {
					$nvpStr = self::getNvpStr($value['paypal_tid']);
					$paypalResponse = Paypal_PaypalLib::call_GetTransactionDetail($nvpStr);
					echo $i++ . '、调用paypal接口查询，交易明细<br/><br/>';
					self::convertPayPalSalesDetails($paypalResponse, $value['transation_id']); 
				} catch (Exception $e) {
					/*
					 * 运行异常
					 */
					$this->countLoad($loadId, 3,0);
					Ec::showError("paypal账户：$paypalAccount,拉取：$start ~~ $end 时间段内付款记录明细异常,错误原因：".$e->getMessage(), self::$runTransactionDetail_);
					return array('ask'=>'0','message'=>$e->getMessage());
				}
			}
		}
		
		/*
		 * 5. 批量插入DB
		 */
		echo $i++ . ": 开始准备插入数据<br/><br/>";
		if(!empty(self::$payPalSalesDetailRows)){
			$model = Service_PaypalOrderTransaction::getModelInstance();
			$db = $model->getAdapter();
			try {
				$db->beginTransaction();
				foreach (self::$payPalSalesDetailRows as $salesKey => $salesValue) {
					//判断是否重复插入
					$tmpResult = $model->getByField($salesValue['pt_id'],'pt_id');
					if(empty($tmpResult)){
						$result = $model->add($salesValue);
						$items = self::$payPalSalesItemDetailRows[$salesKey];
						if(!empty($items)){
							foreach ($items as $itemKey => $itemValue) {
								$itemValue['pot_id'] = $result;
								Service_PaypalOrderDetail::add($itemValue);
							}
						}
					}
				}
				$db->commit();
				echo $i++ . ": 完成数据插入<br/><br/>";
			} catch (Exception $e) {
				echo $e->getMessage();
				$db->rollBack();
				$this->countLoad($loadId, 3,0);
				Ec::showError("paypal账户：$paypalAccount,插入：$start ~~ $end 时间段内付款记录明细异常,错误原因：".$e->getMessage(), self::$runTransactionDetail_);
				return array('ask'=>'0','message'=>$e->getMessage());
			}
		}else{
			echo $i++ . ": 无数据需要插入<br/><br/>";
		}
		
		/*
		 * 6. 处理完成，更新数据控制表
		*/
		$payPalSalesDetailRowsLength = count(self::$payPalSalesDetailRows);
		$this->countLoad($loadId, 2, $payPalSalesDetailRowsLength);
		return array(
				'ask' => '1',
				'message' => "paypal账户：$paypalAccount,已拉取：$start ~~ $end 时间段内的付款明细."
		);
	}
	
	/**
	 * 组装paypal交易明细接口，所需参数
	 * @param unknown_type $transactionId
	 * @return string
	 */
	public static function getNvpStr($transactionId){
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
		 * 2.设置交易ID
		 */
		$nvpStr .= "&TRANSACTIONID=$transactionId";

		return $nvpStr;
	}
	
	/**
	 * 处理paypal查询接口返回的数据
	 * @param unknown_type $paypalResponse
	 * @param unknown_type $transactionId
	 */
	public static function convertPayPalSalesDetails($paypalResponse,$tpId){
		$return_row = array();
		if($paypalResponse['ACK'] == 'Success'){
			$ptId 						= $tpId;														//paypal交易记录表ID
			$paypalTransactionId		= mysql_escape_string($paypalResponse['TRANSACTIONID']);		//paypal交易ID
			
			$countryCode				= mysql_escape_string($paypalResponse['COUNTRYCODE']);			//国家二字码
			$name						= mysql_escape_string($paypalResponse['SHIPTONAME']);			//收货人名
			$street1					= mysql_escape_string($paypalResponse['SHIPTOSTREET']);			//街道
			$street2					= mysql_escape_string($paypalResponse['SHIPTOSTREET2']);		//街道2
			$city						= mysql_escape_string($paypalResponse['SHIPTOCITY']);			//城市
			$state 						= mysql_escape_string($paypalResponse['SHIPTOSTATE']);			//州
			$country					= mysql_escape_string($paypalResponse['SHIPTOCOUNTRYCODE']);	//收货人国家二字码
			$countryName				= mysql_escape_string($paypalResponse['SHIPTOCOUNTRYNAME']);	//收货人国家名
			$zip						= mysql_escape_string($paypalResponse['SHIPTOZIP']);			//邮编
			$note						= mysql_escape_string($paypalResponse['NOTE']);					//备注
			
			$buyerId					= mysql_escape_string($paypalResponse['BUYERID']);				//买家ID
			$firstName					= mysql_escape_string($paypalResponse['FIRSTNAME']);			//名
			$lastName					= mysql_escape_string($paypalResponse['LASTNAME']);				//姓
			$orderTime					= mysql_escape_string($paypalResponse['ORDERTIME']);			//订单时间
			$AMT						= mysql_escape_string($paypalResponse['AMT']);					//金额
			$currencyCode				= mysql_escape_string($paypalResponse['CURRENCYCODE']);			//币种
			$transactionType			= Paypal_PaypalLib::str_rep($paypalResponse['TRANSACTIONTYPE']);					//交易类型
			
			//类型不能位快速结账
			if($transactionType == 'express-checkout'){
				return;
			}
			$row = array(
              'pt_id'					=>$ptId,
              'pot_paypal_id'			=>$paypalTransactionId,
              'pot_country_code'		=>$countryCode,
              'pot_ship_name'			=>$name,
              'pot_ship_street1'		=>$street1,
              'pot_ship_street2'		=>$street2,
              'pot_ship_city'			=>$city,
              'pot_ship_state'			=>$state,
              'pot_ship_county_code'	=>$country,
              'pot_ship_county_name'	=>$countryName,
              'pot_ship_zip'			=>$zip,
              'pot_buyer_id'			=>$buyerId,
              'pot_first_name'			=>$firstName,
              'pot_last_name'			=>$lastName,
              'pot_order_time'			=>date('Y-m-d H:i:s',strtotime($orderTime)),
              'pot_amt'					=>$AMT,
              'pot_currency_code'		=>$currencyCode,
        	  'pot_note'				=>$note,
			  'pot_status'				=>'0'	//未关联
        	);
			self::$payPalSalesDetailRows[$ptId] = $row;
			
			/*
			 * 拉取货品信息
			 */
			$sure = 0;
			$rowDetails = array();
			while(true){
				if(array_key_exists('L_NUMBER'.$sure,$paypalResponse)){
					$podName		 	= $paypalResponse['L_NAME'.$sure];					//货品名称
					$podNumber		 	= $paypalResponse['L_NUMBER'.$sure];				//货品ID
					$podQty		 		= $paypalResponse['L_QTY'.$sure];					//数量
					$podAmt			 	= $paypalResponse['L_AMT'.$sure];					//单价
					$podCurrencyCode	= $paypalResponse['L_CURRENCYCODE'.$sure];			//币种
					
					$rowDetail = array(
							'pot_name'				=>$podName,
							'pot_number'			=>$podNumber,
							'pot_qty'				=>$podQty,
							'pot_amt'				=>$podAmt,
							'pot_currency_code'		=>$podCurrencyCode,
						);
					$rowDetails[] = $rowDetail;
				}else{
					break;
				}
				$sure = $sure + 1;
			}
			
			self::$payPalSalesItemDetailRows[$ptId] = $rowDetails;
			
			$return_row = array(
					'detail'=>$row,
					'detail_items'=>$rowDetails
					);
		}
		return $return_row;
	}
	
	function str_rep($str){
		$str  = str_replace("'","&acute;",$str);
		$str  = str_replace("\"","&quot;",$str);
		return $str;
	}
		
}