<?php
/**
 * 手工下载paypal交易记录服务类
 * @author Frank
 * @date 2013-10-16 19:41:20
 *
 */
class Service_PaypalTransationProcess extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;
    /**
     * Paypal API签名信息
     */
    private static $api_username = null;
    private static $api_password = null;
    private static $api_signature = null;

    /**
     * @return Table_PaypalTransation|null
     */
    public static function getModelInstance()
    {
        set_time_limit(0);
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PaypalTransation();
        }
        return self::$_modelClass;
    }

    /**
     * @param $rows
     * @return mixed
     */
    private static function addPaypalTransactionRows($rows)
    {
		if(!empty($rows)){
			$model = Service_PaypalTransation::getModelInstance();
			$db = $model->getAdapter();
			try {
				$db->beginTransaction();
				foreach ($rows as $key => $value) {
					$result = $model->add($value);
					$resultEbayOrderPayment = Service_EbayOrderPayment::getByField($value['paypal_tid'],'referenceid');
					if(!empty($resultEbayOrderPayment) && empty($resultEbayOrderPayment['recv_account'])){
						Service_EbayOrderPayment::update(array('recv_account'=>$value['recv_account']), $resultEbayOrderPayment['eop_id']);
					}
				}
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				return array('ask'=>'0','message'=>$e->getMessage());
			}
			$payPalSalesRowsLength = count($rows);
			$message = "下载Paypal交易记录成功，共 $payPalSalesRowsLength 条.";
			return array('ask'=>'1','message'=>$message);
		}
    }
    
    /**
     * 
     * @param unknown_type $rows
     */
    private static function addPaypalTransactionDetailRows($rows){
    	if(!empty($rows)){
    		$model = Service_PaypalOrderTransaction::getModelInstance();
    		$db = $model->getAdapter();
    		try {
    			$db->beginTransaction();
    			foreach ($rows as $salesKey => $salesValue) {
    				$valRow = $salesValue['detail'];
    				$valItemsRows =$salesValue['detail_items'];
    				//判断是否重复插入
    				$tmpResult = $model->getByField($valRow['pt_id'],'pt_id');
    				if(empty($tmpResult)){
    					$result = $model->add($valRow);
    					$items = $valItemsRows;
    					if(!empty($items)){
    						foreach ($items as $itemKey => $itemValue) {
    							$itemValue['pot_id'] = $result;
    							Service_PaypalOrderDetail::add($itemValue);
    						}
    					}
    				}
    			}
    			$db->commit();
//     			echo $i++ . ": 完成数据插入<br/><br/>";
    		} catch (Exception $e) {
//     			echo $e->getMessage();
    			$db->rollBack();
    			return array('ask'=>'0','message'=>$e->getMessage());
    		}
    		$payPalSalesRowsDeatilLength = count($rows);
    	}
    	$message = "下载Paypal交易记录及明细成功，共 $payPalSalesRowsDeatilLength 条.";
    	return array('ask'=>'1','message'=>$message);
    }
    
    /**
     * 
     */
    private static function loadPaypalSignature($paypalAccount){
    	/*
    	 * 1. 查询paypal签名信息
    	*/
    	$resultEbayPaypal = Service_EbayPaypal::getByField($paypalAccount,'paypal_account');
    	if(empty($resultEbayPaypal)){
    		$errorMessage = "paypal账户：$paypalAccount 未维护签名信息，请维护！";
    		throw new Exception($errorMessage);
    	}
    	self::$api_username = $resultEbayPaypal['name'];
    	self::$api_password = $resultEbayPaypal['pass'];
    	self::$api_signature = $resultEbayPaypal['signature'];
    }
    
    /**
     * 根据paypal交易ID下载交易记录
     * @param unknown_type $paypalAccount
     * @param unknown_type $TransactionId
     */
    public function callPaypalSearchById($companyCode,$paypalAccount,$TransactionId){
    	try{
	    	/*
	    	 * 1. 获得请求连接
	    	 */
	    	self::loadPaypalSignature($paypalAccount);
	    	$nvpStr_transcription = self::getPaypalTransactionNvpStr($paypalAccount, $TransactionId);
    		/*
    		 * 2. 发送请求
    		 */
    		$paypalResponse = Paypal_PaypalLib::call_TransactionSeach($nvpStr_transcription);
    		//print_r($paypalResponse);
    	} catch (Exception $e) {
    		//异常
    		return array('ask'=>'0','message'=>$e->getMessage());
    	}
		
    	/*
    	 * 3. 封装返回信息
    	 */
    	$rows = Paypal_PaypalTransactionSearchService::convertPayPalSalesDetails($paypalResponse, $paypalAccount,$companyCode);
    	
    	/*
    	 * 4. 判断是否存在交易记录
    	 */
    	if(count($rows) > 0){
    		
    		//验证交易记录是否已经存在
	    	$rows_transaction = self::removeDuplicateRowByTransaction($rows);
    		if(count($rows_transaction) > 0){
    			//执行插入操作
    			$return = self::addPaypalTransactionRows($rows_transaction);
    			//同时调用交易明细的接口
    			if($return['ask']){
	    			$reutrn = self::callPaypalTransactionDetail($paypalAccount, $rows_transaction);
    			}
    		}else{
    			$return = self::callPaypalTransactionDetail($paypalAccount, $rows);
    		}
    		return $return;
    	}else{
    		return array('ask'=>'0','message'=>"Paypal账户：$paypalAccount 未找到交易ID为 $TransactionId 的交易记录");
    	}
    }
    
    
    
    /**
     * 根据交易时间下载交易记录
     * @param unknown_type $paypalAccount
     * @param unknown_type $startDate
     * @param unknown_type $endDate
     */
    public function callPaypalSearchByDate($companyCode,$paypalAccount,$startDate,$endDate){
    	try{
	    	/*
	    	 * 1. 获得请求连接
	    	*/
    		$astart = date('Y-m-d',strtotime($startDate))."T".date('H:i:s',strtotime($startDate))."Z";
    		$aend = date('Y-m-d',strtotime($endDate))."T".date('H:i:s',strtotime($endDate))."Z";
    		$requestStart = $astart;
    		$requestEnd = $aend;
	    	
    		self::loadPaypalSignature($paypalAccount);
    		$nvpStr = self::getPaypalTransactionNvpStr($paypalAccount, null,$astart,$aend);

    		
    		//是否发生请求
    		$callBool = true;
    		//所有交易记录
    		$rows_All = array();
    		while(true){
    			if($callBool){
    				$paypalResponse = Paypal_PaypalLib::call_TransactionSeach($nvpStr);
    			}
    			/*
    			 * 2.1 若查询结果被截留，重设查询条件（时间切割）
    			*/
    			if(strtoupper($paypalResponse["ACK"]) == "SUCCESSWITHWARNING" &&
    					strtoupper($paypalResponse["L_ERRORCODE0"]) == "11002"){
    				$tmpTime = Paypal_PaypalTransactionSearchService::getIntervalTime($requestStart, $requestEnd);	//计算查询时间的间隔
    				$tmpCut = round($tmpTime / 2);
    				$requestEnd = Paypal_PaypalTransactionSearchService::getChangeTimeByMinutes($requestEnd, '-' . $tmpCut);
    				$requestEnd = Paypal_PaypalTransactionSearchService::getChangeTimeByHour($requestEnd, Paypal_PaypalTransactionSearchService::$timeDifference);
    				$requestEnd = Paypal_PaypalTransactionSearchService::getPaypalTimeType($requestEnd);
    				$nvpStr = self::getPaypalTransactionNvpStr($paypalAccount, null,$requestStart,$requestEnd);
    			}else{
    				/*
    				 * 4.2 处理拆分时间段后的，正常的返回结果，并再次推移时间
    				*/
    				$rows = Paypal_PaypalTransactionSearchService::convertPayPalSalesDetails($paypalResponse, $paypalAccount,$companyCode);
    				$rows_All = array_merge($rows_All,$rows);
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
    					$requestEnd = Paypal_PaypalTransactionSearchService::getChangeTimeByMinutes($requestEnd, Paypal_PaypalTransactionSearchService::$timePushForward);
    					$requestEnd = Paypal_PaypalTransactionSearchService::getChangeTimeByHour($requestEnd, Paypal_PaypalTransactionSearchService::$timeDifference);
    					$requestStart = Paypal_PaypalTransactionSearchService::getPaypalTimeType($requestEnd);
    					$requestEnd = $aend;
    					$nvpStr = self::getPaypalTransactionNvpStr($paypalAccount, null,$requestStart,$requestEnd);
    				}
    			}
    		}
    	} catch (Exception $e) {
    		//异常
    		return array('ask'=>'0','message'=>$e->getMessage());
    	}
    	
    	/*
    	 * 4. 判断是否存在交易记录
    	*/
    	if(count($rows_All) > 0){
    	
    		//验证交易记录是否已经存在
    		$rows_transaction = self::removeDuplicateRowByTransaction($rows_All);
    		if(count($rows_transaction) > 0){
    			//执行插入操作
    			$return = self::addPaypalTransactionRows($rows_transaction);
    			//同时调用交易明细的接口
    			if($return['ask']){
    				$reutrn = self::callPaypalTransactionDetail($paypalAccount, $rows_transaction);
    			}
    		}else{
    			$return = self::callPaypalTransactionDetail($paypalAccount, $rows_All);
    		}
    		return $return;
    	}else{
    		return array('ask'=>'0','message'=>"$astart ~ $aend 时间段内，Paypal账户：$paypalAccount 没有交易记录.");
    	}
    }
    
    /**
     * 根据交易记录，下载交易明细
     * @param unknown_type $paypalAccount
     * @param unknown_type $rows_transaction
     */
    private static function callPaypalTransactionDetail($paypalAccount,$rows_transaction){
    	/*
    	 * 1. 验证是否为线下订单
    	*/
    	$rows_detail = self::removeDuplicateRowByDetail($rows_transaction);
    	if(count($rows_detail) > 0){
    		$rows = array();
    		foreach ($rows_detail as $key => $value) {
    			$nvpStr_detail = self::getPaypalTransactionDetailNvpStr($paypalAccount, $value['paypal_tid']);
    			$paypalResponse = Paypal_PaypalLib::call_GetTransactionDetail($nvpStr_detail);
    			//echo $i++ . '、调用paypal接口查询，交易明细<br/><br/>';
    			$reslutPaypalTr =  Service_PaypalTransation::getByField($value['paypal_tid'],'paypal_tid');
    			
    			$return_row = Paypal_PaypalTransactionDetailService::convertPayPalSalesDetails($paypalResponse, $reslutPaypalTr['transation_id']);
    			if(!empty($return_row)){
	    			$rows[] = $return_row;
    			}
    		}
			return self::addPaypalTransactionDetailRows($rows);
    	}else{
    		return array('ask'=>'1','message'=>'已完成交易记录及明细下载.');
    	}
    }
    
    /**
     * 删除已存在的交易记录，然后返回需要插入的数据
     * @param unknown_type $rows
     */
    private static function removeDuplicateRowByTransaction($rows){
    	if(!empty($rows)){
    		foreach ($rows as $key => $value) {
    			
    			$result = Service_PaypalTransation::getByCondition(array(
    					'paypal_tid' => $value['paypal_tid'],
    					'recv_account' => $value['recv_account']
    			));
    			
    			//存在重复记录，剔除
    			if(!empty($result)){
//     				echo ": 剔除重复数据，交易ID $value[paypal_tid]<br/><br/>";
    				unset($rows[$key]);
    			}
    		}
    		//重新索引排序
    		ksort($rows);
    	}
    	
    	return $rows;
    }
    
    /**
     * 删除交易记录中，不需要拉取交易明细记录
     * @param unknown_type $rows
     * @return unknown
     */
    private static function removeDuplicateRowByDetail($rows){
    	if(!empty($rows)){
    		foreach ($rows as $key => $value) {
    			/*
    			 * 1. 查询是否存在ebay订单付款记录
    			*/
    			$resultEbayPayMent = Service_EbayOrderPayment::getByField($value['paypal_tid'],'referenceid');
//     			print_r($resultEbayPayMent);
				$statusSuccess = array('Succeeded','CustomCode');
				
    			if(!empty($resultEbayPayMent) && in_array($resultEbayPayMent['paymentstatus'], $statusSuccess)){
    				//若paypal交易记录表中存在该记录，剔除该条,并跳出当前循环
    				unset($rows[$key]);
    				//echo $i++ . "、ebay存在订单付款记录，剔除重复记录，交易ID $value[paypal_tid]<br/><br/>";
    				continue;
    			}
    	
    			/*
    			 * 2 查询是否拉取过交易明细（根据外键）
    			*/
    			$resultPaypalOrder = Service_PaypalOrderTransaction::getByField($value['paypal_tid'],'pot_paypal_id');
//     			print_r($resultPaypalOrder);
    			if(!empty($resultPaypalOrder)){
    				//若存在paypal交易明细，剔除该条，并跳出当前循环
    				unset($rows[$key]);
    				//echo $i++ . "、已拉取过交易明细，剔除重复记录，交易ID $value[paypal_tid]<br/><br/>";
    				continue;
    			}
    		}
    		//重新索引排序
    		ksort($rows);
    	}
    	
    	return $rows;
    }
    
    /**
     * 获得paypal交易查询请求连接
     * @param unknown_type $paypalAccount
     * @param unknown_type $TransactionId
     * @param unknown_type $startDate
     * @param unknown_type $endDate
     * @throws Exception
     * @return string
     */
    private static function getPaypalTransactionNvpStr($paypalAccount,$TransactionId,$startDate,$endDate){
    	/*
    	 * 1. 查询paypal签名信息
    	 */
//     	$resultEbayPaypal = Service_EbayPaypal::getByField($paypalAccount,'paypal_account');
//     	if(empty($resultEbayPaypal)){
//     		$errorMessage = "paypal账户：$paypalAccount 未维护签名信息，请维护！";
//     		throw new Exception($errorMessage);
//     	}
//     	$API_UserName = $resultEbayPaypal['name'];
//     	$API_Password = $resultEbayPaypal['pass'];
//     	$API_Signature = $resultEbayPaypal['signature'];
    	
    	/*
    	 * 1. 设置账户签名
    	*/
    	$nvpStr = "&USER=".urlencode(self::$api_username);		//API账户
    	$nvpStr .= "&PWD=".urlencode(self::$api_password);		//API密码
    	$nvpStr .= "&SIGNATURE=".urlencode(self::$api_signature);	//API签名
    	$nvpStr .= "&TRANSACTIONCLASS=Received";		//类型收款
    	/*
    	 * 2.设置请求参数
    	 */
    	if(!empty($TransactionId)){
	    	//交易ID不为空，只根据交易ID信息
    		$TransactionId = strtoupper($TransactionId);
    		$TransactionId = trim($TransactionId);    		
    		$nvpStr .="&TRANSACTIONID=$TransactionId";	//paypal交易ID
    		$nvpStr .= "&STARTDATE=1999-01-01T00:00:00";			//开始时间
//     		$nvpStr = "&ENDDATE=2013-08-29T00:00:00";			//开始时间
    	}else{
    		$startDateUs = $startDate;
    		$endDateUs = $endDate;
    		$nvpStr .= "&STARTDATE=$startDateUs";			//开始时间
    		$nvpStr .= "&ENDDATE=$endDateUs";				//结束时间
    	}
    	
    	return $nvpStr;
    }

    /**
     * 获得paypal交易明细请求连接
     * @param unknown_type $paypalAccount
     * @param unknown_type $TransactionId
     * @throws Exception
     * @return string
     */
    private static function getPaypalTransactionDetailNvpStr($paypalAccount,$TransactionId){
    	/*
    	 * 1. 查询paypal签名信息
    	*/
//     	$resultEbayPaypal = Service_EbayPaypal::getByField($paypalAccount,'paypal_account');
//     	if(empty($resultEbayPaypal)){
//     		$errorMessage = "paypal账户：$paypalAccount 未维护签名信息，请维护！";
//     		throw new Exception($errorMessage = "paypal账户：$paypalAccount 未维护签名信息，请维护！");
//     	}
//     	$API_UserName = $resultEbayPaypal['name'];
//     	$API_Password = $resultEbayPaypal['pass'];
//     	$API_Signature = $resultEbayPaypal['signature'];
//     	self::$api_username = $resultEbayPaypal['name'];
//     	self::$api_password = $resultEbayPaypal['pass'];
//     	self::$api_signature = $resultEbayPaypal['signature'];
		
    	/*
    	 * 1. 设置账户签名
    	 */
    	$nvpStr = "&USER=".urlencode(self::$api_username);			//API账户
    	$nvpStr .= "&PWD=".urlencode(self::$api_password);			//API密码
    	$nvpStr .= "&SIGNATURE=".urlencode(self::$api_signature);		//API签名
    	
    	/*
    	 * 2.设置交易ID
    	*/
    	$nvpStr .= "&TRANSACTIONID=$TransactionId";
    	
    	return $nvpStr;
    }
}