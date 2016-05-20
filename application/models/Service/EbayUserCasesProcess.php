<?php
class Service_EbayUserCasesProcess extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayUserCases|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayUserCases();
        }
        return self::$_modelClass;
    }
    
    /**
     * 查询case（EBP开头的）纠纷明细
     * @param unknown_type $token
     * @param unknown_type $case_id
     * @param unknown_type $case_type
     * @return Ambigous <NULL, multitype:>
     */
    public static function getEBPCaseDetail($token, $case_id, $case_type){
    	$caseEBPDetailResponse = Ebay_EbayLib::getEBPCaseDetail($token, $case_id, $case_type);
    	return $caseEBPDetailResponse;
    }
    
    /**
     * 封装EBP开头类型的case的明细
     * @param unknown_type $ebayEBPCaseDetail
     */
    public static function convertEBPCaseDetail($ebayEBPCaseDetail){
    	$return = array(
    			'ask' => 0,
    			'data' => ''
    	);
    	if($ebayEBPCaseDetail != null && $ebayEBPCaseDetail['getEBPCaseDetailResponse']['ack'] == 'Success'){
    		$caseSummary = $ebayEBPCaseDetail['getEBPCaseDetailResponse']['caseSummary'];
    		$caseDetail = $ebayEBPCaseDetail['getEBPCaseDetailResponse']['caseDetail'];
    		$row['case_id'] = $caseSummary['caseId']['id'];						//caseID
    		$row['platform_user_name'] = $caseSummary['user']['userId'];		//平台登录账户
    		$row['buyer_id'] = $caseSummary['otherParty']['userId'];			//买家ID
    		 
    		foreach ($caseSummary['status'] as $status_key => $status_value) {
    			$row['status']			= $status_value;						//case当前状态
    		}
    		$row['item_id'] = $caseSummary['item']['itemId'];
    		$row['case_qty'] = $caseSummary['caseQuantity'];					//数量
    		$row['currency_id'] = $caseSummary['caseAmount attr']['currencyId'];//币种
    		$row['case_amount'] = $caseSummary['caseAmount'];					//case总金额
    		$row['respond_date'] = $caseSummary['respondByDate'];				//期限时间
    		$row['respond_date_sys'] = date('Y-m-d H:i:s',strtotime($row['respond_date']));
    	
    		$row['creation_date'] = $caseSummary['creationDate'];				//发起时间
    		$row['creation_date_sys'] = date('Y-m-d H:i:s',strtotime($row['creation_date']));
    		$row['expiry_date'] = date('Y-m-d H:i:s',strtotime("45 days",strtotime($row['creation_date_sys'])));
    	
    		$row['open_reason'] = $caseDetail['openReason'];					//开启原因
    		$row['decision'] = $caseDetail['decision'];							//决定
    		$row['fyf_credited'] = $caseDetail['FVFCredited'];					//
    		$row['global_id'] = $caseDetail['globalId'];						//站点
    		 
    		if(!empty($caseDetail['responseHistory']) && count($caseDetail['responseHistory']) > 0){
    			$rowHistoryMsg = array();
    			$loopHistoryData = array();
    			if($caseDetail['responseHistory']['creationDate'] != ''){
    				$loopHistoryData[] = $caseDetail['responseHistory'];
    			}else{
    				$loopHistoryData = $caseDetail['responseHistory'];
    			}
    			foreach ($loopHistoryData as $history_msg_key => $history_msg_value) {
    				$tmp['note'] = $history_msg_value['note'];
    				$tmp['author_role'] = $history_msg_value['author']['role'];
    				$tmp['activity_detail_code'] = $history_msg_value['activityDetail']['code'];
    				$tmp['activity_detail_description'] = $history_msg_value['activityDetail']['description'];
    				if(empty($tmp['note'])){	//没有内容，一般是关闭的case，所以使用说明，填补内容
    					$tmp['note'] = $tmp['activity_detail_description'];
    				}
    				$tmp['date_sys'] = date('Y-m-d H:i:s',strtotime($history_msg_value['creationDate']));//date($history_msg_value['creationDate']);
    				$tmp['date_ebay'] = $history_msg_value['creationDate'];
    				$rowHistoryMsg[$history_msg_key] = $tmp;
    			}
    			krsort($rowHistoryMsg);
    			$row['response_history'] = $rowHistoryMsg;						//历史往来消息
    		}
    	
    		$row['agreed_refund_amount'] = $caseDetail['agreedRefundAmount'];										//同意退款金额
    		$row['detail_status_description'] = $caseDetail['detailStatusInfo']['description'];						//明细状态说明
    		$row['detail_status_content'] = $caseDetail['detailStatusInfo']['content'];								//明细状态内容
    		$row['buyer_expectation_code'] = $caseDetail['initialBuyerExpectationDetail']['code'];					//买家期望code
    		$row['buyer_expectation_description'] = $caseDetail['initialBuyerExpectationDetail']['description'];	//买家期望说明
    	
    		$return['ask'] = 1;
    		$return['data'] = $row;
    	}
    	 
    	return $return;
    }

    /**
     * 查询当前case（EBP开头类型）可操作的接口
     * @param unknown_type $token
     * @param unknown_type $case_id
     * @param unknown_type $case_type
     */
    public static function getActivityOptions($token, $case_id, $case_type){
    	$activityOptionsResponse = Ebay_EbayLib::getActivityOptions($token, $case_id, $case_type);
    	return $activityOptionsResponse;
    }
    
    /**
     * 封装case可响应接口（EBP开头类型的）
     * @param unknown_type $ebayEBPActivityOptions
     */
    public static function convertEBPActivityOptions($ebayEBPActivityOptions){
    	$return = array(
    			'ask'=>0,
    			'data'=>''
    	);
    	/*
    	 * 1.定义case所支持的响应接口
    	*/
    	$defaultInterface = self::getCaseResponseDefaultInterface();
    	 
    	if($ebayEBPActivityOptions['getActivityOptionsResponse']['ack'] == 'Success'){
    		$activityOptions = $ebayEBPActivityOptions['getActivityOptionsResponse']['activityOptions'];
    		//     		print_r($activityOptions);
    		/*
    		 * 2.查找出对应的接口
    		*/
    		$row = array();
    		foreach ($defaultInterface as $key => $value) {
    			if(!empty($activityOptions[$key])){
    				foreach ($value as $key2 => $value2) {
    					//     					echo '1<br/>';
    					$row[$key2] = array(
    							'title'=>$value2,
    							'code'=>ucfirst($key2),
    							'buyerPreference'=>(($activityOptions[$key]['buyerPreference'] == 'true')?1:0)
    					);
    				}
    			}else{
    				foreach ($value as $key3 => $value3) {
    					//     					echo '2<br/>';
    					if(empty($row[$key3])){
    						// 							echo '2--3<br/>';
    						$row[$key3] = array(
    								'title'=>$value3,
    								'code'=>ucfirst($key3),
    								'buyerPreference'=>0
    						);
    					}
    				}
    			}
    		}
    		
    		$return['ask'] = 1;
    		$return['data'] = $row;
    	}
    	
    	return $return;
    }
    
    /**
     * 获得普通snad或inr类型case信息详情
     * @param unknown_type $token
     * @param unknown_type $case_id
     * @return Ambigous <NULL, multitype:>
     */
    public static function getDisputeDetail($token, $case_id){
	    $caseCommonDetailResponse = Ebay_EbayLib::getDisputeDetail($token, $case_id);
	    return $caseCommonDetailResponse;
    }
    
    /**
     * 封装UPI，CANCEL_TRANSACTION类型的case的明细
     * @param unknown_type $ebayCommonCaseDetail
     */
    public static function convertCommonCaseDetail($ebayCommonCaseDetail){
    	$return = array(
    			'ask'=>0,
    			'data'=>''
    	);
    	//     	print_r($ebayCommonCaseDetail);
    	if($ebayCommonCaseDetail != null && $ebayCommonCaseDetail['GetDisputeResponse']['Ack'] == 'Success'){
    		$disputeDetail = $ebayCommonCaseDetail['GetDisputeResponse']['Dispute'];
    		$row['dispute_id'] = $disputeDetail['DisputeID'];
    		$row['dispute_record_type'] = $disputeDetail['DisputeRecordType'];		//争议记录类型
    		$row['dispute_state'] = $disputeDetail['DisputeState'];					//
    		$row['dispute_status'] = $disputeDetail['DisputeStatus'];				//前期争议状态
    		$row['buyer_id'] = $disputeDetail['BuyerUserID'];						//买家ID
    		$row['platform_user_name'] = $disputeDetail['SellerUserID'];			//平台账户
    		$row['item_id'] = $disputeDetail['Item']['ItemID'];
    		$row['item_qty'] = $disputeDetail['Item']['Quantity'];
    		$row['converted_current_price'] = $disputeDetail['Item']['SellingStatus']['ConvertedCurrentPrice'];								//转换后的价格
    		$row['converted_current_currency_id'] = $disputeDetail['Item']['SellingStatus']['ConvertedCurrentPrice attr']['currencyID']; 	//转换后的币种
    		$row['current_price'] = $disputeDetail['Item']['SellingStatus']['CurrentPrice'];												//当前价格
    		$row['current_currency_id'] = $disputeDetail['Item']['SellingStatus']['CurrentPrice attr']['currencyID'];						//当前币种
    		$row['site'] = $disputeDetail['Item']['Site'];										//站点
    		$row['item_title'] = $disputeDetail['Item']['Title'];								//Title
    		$row['condition_id'] = $disputeDetail['Item']['ConditionID'];						//
    		$row['condition_display_name'] = $disputeDetail['Item']['ConditionDisplayName'];	//
    		$row['dispute_reason'] = $disputeDetail['DisputeReason'];							//争议原因
    		$row['dispute_explanation'] = $disputeDetail['DisputeExplanation'];					//争议说明
    		$row['dispute_credit_eligibility'] = $disputeDetail['DisputeCreditEligibility'];	//信用资格
    		$row['dispute_created_date'] = $disputeDetail['DisputeCreatedTime'];				//争议开启时间
    		$row['dispute_created_date_sys'] = date('Y-m-d H:i:s',strtotime($row['dispute_created_date']));
    		$row['dispute_modified_date'] = $disputeDetail['DisputeModifiedTime'];				//争议修改时间
    		$row['dispute_modified_date_sys'] = date('Y-m-d H:i:s',strtotime($row['dispute_modified_date']));
    
    		if(!empty($disputeDetail['DisputeMessage']) && count($disputeDetail['DisputeMessage']) > 0){
    			$rowMsg = array();
    			$loopHistoryData = array();
    			if($disputeDetail['DisputeMessage']['MessageID'] != ''){
    				$loopHistoryData[] = $disputeDetail['DisputeMessage'];
    			}else{
    				$loopHistoryData = $disputeDetail['DisputeMessage'];
    			}
    			 
    			foreach ($loopHistoryData as $key => $value) {
    				$tmp['msg_id'] = $value['MessageID'];						//消息ID
    				$tmp['msg_source'] = $value['MessageSource'];				//消息来源
    				$tmp['msg_creation_date'] = $value['MessageCreationTime'];	//消息创建时间
    				$tmp['msg_creation_date_sys'] = date('Y-m-d H:i:s',strtotime($tmp['msg_creation_date']));
    				$tmp['mesg_text'] = $value['MessageText'];					//消息内容
    				$rowMsg[$key] = $tmp;
    			}
    			$row['dispute_message'] = $rowMsg;
    		}
    
    		$row['refrence_no_platform'] = $disputeDetail['OrderLineItemID'];	//平台参考号
    
    		$return['ask'] = 1;
    		$return['data'] = $row;
    	}
    	return $return;
    }
    
    /**
     * 响应case的默认接口数组
     * @return multitype:multitype:string
     */
    public static function getCaseResponseDefaultInterface(){
    	$defaultInterface = array(
    			//     			'appealToCustomerSupport'=>array('appealToCustomerSupport'=>'发出上诉'),
    			//     			'escalateToCustomerSupport'=>array('escalateToCustomerSupport'=>'升级CASE'),
    			//     			'issueFullRefund'=>array('issueFullRefund'=>'提供全额退款'),
    			//     			'issuePartialRefund'=>array('issuePartialRefund'=>'提供部分退款'),
    			'offerOtherSolution'=>array('offerOtherSolution'=>'提供别的解决方案'),
    			//     			'provideRefundInfo'=>array('provideRefundInfo'=>'发送一个和退款相关的消息'),
    			'provideShippingInfo'=>array('provideShippingInfo'=>'提供发货信息及时间'),
    			'provideTrackingInfo'=>array('provideTrackingInfo'=>'提供跟踪单号信息'),
    			'provideShippingOrTrackingInfo'=>array('provideShippingInfo'=>'提供发货信息及时间','provideTrackingInfo'=>'提供跟踪单号信息'),
    			//     			'requestBuyerToReturn'=>array('offerRefundUponReturn'=>'退款前发出通知给买家'),
    	);
    
    	return $defaultInterface;
    }
    
    /**
     * 响应case的接口及调用方法
     * @return multitype:multitype:string
     */
    public static function getCaseResponseInterfaceCall(){
    	$defaultInterface = array(
    			'offerOtherSolution'=>"callOfferOtherSolution",
    			'provideShippingInfo'=>'callProvideShippingInfo',
    			'provideTrackingInfo'=>'callProvideTrackingInfo',
    	);
    
    	return $defaultInterface;
    }
    
    /**
     * 提供别的解决方案(发送case消息)
     * @param unknown_type $userAccount
     * @param unknown_type $caseId
     * @param unknown_type $caseType
     * @param unknown_type $msgContent
     */
    public static function callOfferOtherSolution($companyCode,$userAccount,$caseId,$caseType,$msgContent){
    	$return = array(
    			'state'=>0,
    			'message'=>''
    			);
    	$token =  self::getEbayUserToken($userAccount,$companyCode);
    	$ebayResponse = Ebay_EbayLib::offerOtherSolution($token, $caseId, $caseType, $msgContent);
    	
    	$sync_message = 'OfferOtherSolution#';
    	if($ebayResponse['offerOtherSolutionResponse']['ack'] == "Success"){
    		$return['state'] = 1;
    		$return['message'] = '提交case消息成功.';
    		$sync_message .= 'Success';
    	}else{
    		$error = $ebayResponse['offerOtherSolutionResponse']['errorMessage']['error'];
    		if(empty($error)){
    			$error = $ebayResponse['errorMessage']['error'];
    		}
    		if(!empty($error[0])){
    			 foreach ($error as $key => $value) {
    			 	$errorMessage[] = $value['message'];
    			 	$sync_message .= $value['message'] . '#';
    			 }
    		}else{
	    		$errorMessage[] = $error['message'];
	    		$sync_message .= $error['message'];
    		}
    		$return['message'] = $errorMessage;
    	}
    	$updateRow = array('sync_message'=>$sync_message,'sync_response_date'=>date("Y-m-d H:i:s"));
    	if($return['state']){
    		$updateRow['case_status'] = 'OTHER_PARTY_RESPONSE';
    	}
    	Service_EbayUserCases::update($updateRow, $caseId ,'case_id');
    	return $return;
    }
    
    /**
     * 提供发货承运商信息，及发货时间等
     * @param unknown_type $userAccount
     * @param unknown_type $caseId
     * @param unknown_type $caseType
     * @param unknown_type $carrierUsed
     * @param unknown_type $msgContent
     * @param unknown_type $shippedDate
     */
    public static function callProvideShippingInfo($companyCode,$userAccount,$caseId,$caseType,$carrierUsed,$msgContent,$shippedDate){
    	$return = array(
    			'state'=>0,
    			'message'=>''
    	);
    	$token =  self::getEbayUserToken($userAccount,$companyCode);
    	$ebayResponse = Ebay_EbayLib::provideShippingInfo($token, $caseId, $caseType, $carrierUsed, $msgContent, $shippedDate);
    	
		$sync_message = 'ProvideShippingInfo#';
    	if($ebayResponse['provideShippingInfoResponse']['ack'] == "Success"){
    		$return['state'] = 1;
    		$return['message'] = '提交case消息成功.';
    		$sync_message .= 'Success';
    	}else{
    		$error = $ebayResponse['provideShippingInfoResponse']['errorMessage']['error'];
    		if(empty($error)){
    			$error = $ebayResponse['errorMessage']['error'];
    		}
    		if(!empty($error[0])){
    			 foreach ($error as $key => $value) {
    			 	$errorMessage[] = $value['message'];
    			 	$sync_message .= $value['message'] . '#';
    			 }
    		}else{
	    		$errorMessage[] = $error['message'];
	    		$sync_message .= $error['message'];
    		}
    		$return['message'] = $errorMessage;
    	}
    	$updateRow = array('sync_message'=>$sync_message,'sync_response_date'=>date("Y-m-d H:i:s"));
    	if($return['state']){
    		$updateRow['case_status'] = 'OTHER_PARTY_RESPONSE';
    	}
		Service_EbayUserCases::update($updateRow, $caseId ,'case_id');
    	return $return;
    }
    
    /**
     * 提供轨迹单号信息等
     * @param unknown_type $userAccount
     * @param unknown_type $caseId
     * @param unknown_type $caseType
     * @param unknown_type $carrierUsed
     * @param unknown_type $msgContent
     * @param unknown_type $trackingNumber
     */
    public static function callProvideTrackingInfo($companyCode,$userAccount,$caseId,$caseType,$carrierUsed,$msgContent,$trackingNumber){
    	$return = array(
    			'state'=>0,
    			'message'=>''
    	);
    	$token =  self::getEbayUserToken($userAccount,$companyCode);
    	$ebayResponse = Ebay_EbayLib::provideTrackingInfo($token, $caseId, $caseType, $carrierUsed, $msgContent, $trackingNumber);
    	
    	$sync_message = 'ProvideTrackingInfo#';
    	if($ebayResponse['provideTrackingInfoResponse']['ack'] == "Success"){
    		$return['state'] = 1;
    		$return['message'] = '提交case消息成功.';
    		$sync_message .= 'Success';
    	}else{
    		$error = $ebayResponse['provideTrackingInfoResponse']['errorMessage']['error'];
    		if(empty($error)){
    			$error = $ebayResponse['errorMessage']['error'];
    		}
    		if(!empty($error[0])){
    			 foreach ($error as $key => $value) {
    			 	$errorMessage[] = $value['message'];
    			 	$sync_message .= $value['message'] . '#';
    			 }
    		}else{
	    		$errorMessage[] = $error['message'];
	    		$sync_message .= $error['message'];
    		}
    		$return['message'] = $errorMessage;
    	}
    	$updateRow = array('sync_message'=>$sync_message,'sync_response_date'=>date("Y-m-d H:i:s"));
    	if($return['state']){
    		$updateRow['case_status'] = 'OTHER_PARTY_RESPONSE';
    	}
    	Service_EbayUserCases::update($updateRow, $caseId ,'case_id');
    	return $return;
    }
    
    /**
     * 获得ebay账户token
     * @param unknown_type $user_account
     * @return mixed
     */
    public static function getEbayUserToken($user_account, $companyCode){
    	$token = Ebay_EbayLib::getUserToken($user_account,$companyCode);
//     	$result = Service_PlatformUser::getByField($user_account,"user_account");
//     	$token =  $result['user_token'];
    	return $token;
    }
}