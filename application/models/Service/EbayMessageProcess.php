<?php
class Service_EbayMessageProcess extends Common_Service
{
	private static function validateMessageIds($messageIds){
		if(empty($messageIds)){
			throw new Exception('param MessageIds empty...');
		}
	}
	
	public static function deleteTransaction($messageIds){
		$result = array (
				"ask" => 0,
				"message" => "操作失败" 
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			self::validateMessageIds($messageIds);
			foreach($messageIds as $messageId){
				$updateRow = array('status'=>'4');
				Service_EbayMessage::update($updateRow, $messageId,'ebay_message_id');
			}
			$db->commit ();
			$result = array (
					"ask" => 1,
					"message" => "操作成功"
			);
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode () 
			);
		}
		return $result;
	}
	
	public static function readTagTransaction($messageIds){
		$result = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			self::validateMessageIds($messageIds);
			foreach($messageIds as $messageId){
				$updateRow = array('status'=>'1');
				Service_EbayMessage::update($updateRow, $messageId,'ebay_message_id');
			}
			$db->commit ();
			$result = array (
					"ask" => 1,
					"message" => "Operation Success"
			);
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $result;
	}
	

	public static function highLightTransaction($messageIds,$level){
		$result = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			self::validateMessageIds($messageIds);
			foreach($messageIds as $messageId){
				$updateRow = array('level'=>$level);
				Service_EbayMessage::update($updateRow, $messageId,'ebay_message_id');
			}
			$db->commit ();
			$result = array (
					"ask" => 1,
					"message" => "Operation Success"
			);
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $result;
	}
	
	public  static function feedBackMessageTransaction($messageIds,$subject,$content,$msgPrcessStatus,$language='zh'){
		$return = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			self::validateMessageIds($messageIds);
			if(empty($subject)){
// 				throw new Exception('Subject Empty');				
			}
			if(empty($msgPrcessStatus) && $msgPrcessStatus == ''){
				throw new Exception('请选择处理进度');
			}
			if(empty($content)){
				throw new Exception('请填写Message内容');
			}
			$results = array();
			foreach($messageIds as $messageId){								
				$result = self::feedBackMessage($messageId, $subject, $content,$msgPrcessStatus,$language);	
				if($result){
				    $results[] = array('ask'=>'1','message'=>'回复成功');				    
				}else{
				    $results[] = array('ask'=>'0','message'=>'回复失败');
				}
			}
			$return = array (
					"ask" => 1,
					"message" => "操作完成",
			        'results'=>$results,
			);
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$return = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $return;
	}
	
	public static function feedBackMessage($messageId,$subject,$content,$msgPrcessStatus,$language='zh'){
		$resultEbayMessage = Service_EbayMessage::getByField($messageId,'ebay_message_id');
		if(empty($resultEbayMessage)){
			throw new Exception('未查找到该ebay消息');
		}else if($resultEbayMessage['response_status'] == '1'){
			throw new Exception('该消息已经处于‘已回复未到eBay’状态，若需要编辑，请先撤销');
		}
		
		//对比同步状态
		if ($resultEbayMessage ['response_sync'] == "1") { // 如果已经同步，则跳过
			return false;
		}
		
		//记录响应人
		$updateRow = array (
				'status' => '1',
		        'response_status'=>'1',
				'process_status'=>$msgPrcessStatus,
				'response_time' => date ( 'Y-m-d H:i:s' ),
				'customer_service_response' => Service_User::getUserId() 
		);
		Service_EbayMessage::update ( $updateRow, $messageId, 'ebay_message_id' );
		
		
		$content = self::formateFeedBackMessageContent ( $content,$language );
		
		$contentUpdateRow = array (
				'response_content' => $content
		);
		return Service_EbayMessageContent::update($contentUpdateRow, $messageId, 'ebay_message_id' );
	}
	
	/**
	 * 新增-ebay追加回复
	 */
	public static function feedBackMessageAppendTransaction($messageIds,$subject,$content,$msgPrcessStatus,$language='zh'){
		$return = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction();
		try {
			self::validateMessageIds($messageIds);
			if(empty($msgPrcessStatus) && $msgPrcessStatus == ''){
				throw new Exception('请选择处理进度');
			}
			if(empty($content)){
				throw new Exception('请填写Message内容');
			}
			$resultEbayMessage = Service_EbayMessage::getByField($messageIds[0],'ebay_message_id');
			if(empty($resultEbayMessage)){
				throw new Exception('未查找到该ebay消息');
			}
			/*
			mod:Frank
			date:2013-12-10 19:56:11
			note:不再验证消息是否已经同步，都可以进行追加消息
			// 如果已经同步，返回
			if($resultEbayMessage ['response_sync'] == "1") { 
				throw new Exception('消息已同步到ebay，不能进行消息追加');
			}
			*/
			$content = self::formateFeedBackMessageContent ( $content,$language );
			
			 //插入追加信息
			$contentAppendUpdateRow = array (
					'emca_ebay_message_id' => $messageIds[0],
					'emca_append_content' => $content,
					'emca_response_status' => '1',
					'emca_date_create' => date('Y-m-d H:i:s')
			);
			$resultAdd =  Service_EbayMessageContentAppend::add($contentAppendUpdateRow);
			
			//检查是否更新处理进度
			if($resultEbayMessage['process_status'] != $msgPrcessStatus){
				$updateEbayMessageRow = array (
						'process_status'=>$msgPrcessStatus
				);
				$resultUpdate = Service_EbayMessage::update($updateEbayMessageRow, $messageIds[0], 'ebay_message_id');
			}else{
				$resultUpdate = 1;
			}
			
			if($resultAdd && $resultUpdate){ 
				$return['ask'] = 1;
				$return['message'] = '消息追加成功';
			}else{
				throw new Exception('消息追加失败' . $resultAdd . '---' . $resultUpdate . '---' );
			}
			$db->commit ();
		}catch ( Exception $e ){
			$db->rollback ();
			$return = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $return;
	}
	
	//变量替换与清除html标记
	private static function formateFeedBackMessageContent($content,$language='zh'){
		//变量暂时未定义，格式如ubb代码
		$con = array('company_code',Common_Company::getCompanyCode());
		$rows = Service_MessageTemplateOperate::getByCondition($con);
		switch($language){
			case 'zh' :
				foreach($rows as $v){
					$content = preg_replace('/\['.$v['operate_code'].'\]/', $v['operate_name_cn'], $content);
				}
				break;
			default:
				foreach($rows as $v){
					$content = preg_replace('/\['.$v['operate_code'].'\]/', $v['operate_name_en'], $content);
				}
		}
		/* 
 		$content = strip_tags($content,'<p>');
 		$content = preg_replace('/<br(\s+)?(\/)?>/i', "\n", $content);
 		$content = preg_replace('/<p([^>]+)?>/i', "", $content);
 		$content = preg_replace('/<\/p>/i', "\n", $content); 
 		*/
		
		$content = Ebay_MessageEbayService::formatContent($content);
		
		return $content;
	}
	//uub代码
	public static function addMessageTemplateOperateTransaction($row){
		$return = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$operate = Service_MessageTemplateOperate::getByField($row['operate_code'],'operate_code');
			if(!empty($operate)){
				throw new Exception('操作符代码已经存在');
			}
			
			if(!Service_MessageTemplateOperate::add ( $row )){
				throw new Exception('插入数据库失败');
			}
			
			$return = array (
					"ask" => 1,
					"message" => "Operation Success"
			);
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$return = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $return;
		
	}
	//uub代码
	public static function updateMessageTemplateOperateTransaction($row,$operate_id){

		$return = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$operate = Service_MessageTemplateOperate::getByField($row['operate_code'],'operate_code');

			if(!empty($operate)&&$operate['operate_id']!=$operate_id){
				throw new Exception('操作符代码已经存在');
			}
				
			if(!Service_MessageTemplateOperate::update ( $row,$operate_id,'operate_id' )){
				throw new Exception('更新数据库失败');
			}
				
			$return = array (
					"ask" => 1,
					"message" => "Operation Success"
			);
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$return = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $return;
		
	}
	

	
	public static function getMessageNotSync($type,$pageSize,$page){
		$con = array('response_sync'=>'0','response_status'=>'1');
		return Service_EbayMessage::getByCondition($con,$type,$pageSize,$page,'user_account');		
	
	}
	
	//针对订单发送消息
	public  static function sendEbayMessageTransaction($userAccount,$orderIds,$subject,$content,$itemID,$language='zh'){
		$return = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {		
				
			if(empty($content)){
				throw new Exception('内容不能为空');
			}
			$content = self::formateFeedBackMessageContent ( $content,$language );
			
			$RecipientIDs = array();
       		$token= '';
       		foreach($orderIds as $orderId){
       			$refrence_no_platform = Service_Orders::getByField($orderId,'order_id',array('refrence_no_platform','company_code','user_account'));
       			if($refrence_no_platform){
       			    $userAccount = $refrence_no_platform['user_account'];
       			    $companyCode = $refrence_no_platform['company_code']; 
                    $token=Ebay_EbayLib::getUserToken($userAccount,$companyCode);
        
       				$buyeruserid = Service_EbayOrderOriginal::getByField($refrence_no_platform,'OrderID',array('buyeruserid'));
       				$innerCon = array('OrderId'=>$refrence_no_platform,'ItemID'=>$itemID);
       				$transactions = Service_EbayOrderTransaction::getByCondition($innerCon);
       				if($buyeruserid&&!empty($transactions)){
       					$RecipientIDs[] = $buyeruserid['buyeruserid'];
       				}
       			}
       		}
       		$RecipientIDs = array_unique($RecipientIDs);
       		$results = array();
       		foreach($RecipientIDs as $RecipientID){
       			$arr = array();
       			$result = Ebay_EbayLib::sendEbayMessageForOrder($token,$subject, $content, $RecipientIDs, $itemID);
       			if($result['AddMemberMessageAAQToPartnerResponse']['Ack']=='Failure'){
       				$arr['ask'] = 0;
       				if(!isset($result['AddMemberMessageAAQToPartnerResponse']['Errors'][0])){
       					$arr['message'] = $result['AddMemberMessageAAQToPartnerResponse']['Errors']['LongMessage'];       			
       				}else{
       					$arr['message'] = $result['AddMemberMessageAAQToPartnerResponse']['Errors'][0]['LongMessage'];
       				}       				
       			}else{
       				$arr['ask'] = 1;
       				$arr['message'] = 'Success';
       			}   
       			$arr['RecipientID'] = $RecipientID;  
       			$results[] = $arr;
       		}		
       		$return = array (
       				"ask" => 1,
       				"message" => "Operation Success",
       				'results'=>$results,
       		);
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$return = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $return;
	}

	//针对订单发送消息
	public  static function saveEbayFeedbackMessageTransaction($orderIds,$subject,$content,$language='zh'){
	    $return = array (
	            "ask" => 0,
	            "message" => "Operation Fail"
	    );
	    $db = Common_Common::getAdapter ();
	    $db->beginTransaction ();
	    try {
	        if(empty($subject)){
	            throw new Exception('标题不能为空');
	        }
	        if(empty($content)){
	            throw new Exception('内容不能为空');
	        }
	        $content = self::formateFeedBackMessageContent ( $content,$language );

	        foreach ($orderIds as $orderId){
	            $orderRow = Service_Orders::getByField($orderId,'order_id');
	            if(empty($orderRow)){
	                throw new Exception('orderId不存在-->'.$orderId);
	            }
	            $con = array('order_id'=>$orderId);
	            $productRows = Service_OrderProduct::getByCondition($con);
// 	        print_r($productRows);exit;
	            if(empty($productRows)){
	                throw new Exception('订单未绑定Item,订单号-->'.$orderRow['refrence_no_platform']);
	            }
	            foreach($productRows as $p){
	                if(empty($p['op_ref_item_id'])){
	                    continue;
	                }
	                $item = Service_SellerItem::getByField($p['op_ref_item_id'],'item_id');
	                if($item){
	                    if(strlen( $item['item_title']) > 30){
	                         $item['item_title'] = substr( $item['item_title'], 0, 30).'...';
	                    }
                        $subject = preg_replace('/\[item_title\]/', $item['item_title'], $subject);
                        $subject = preg_replace('/\[item_id\]/', $p['op_ref_item_id'], $subject);
                        
                    }else{
	                    $subject = 'Message For eBay User';
	                }
	                $row = array(
	                        'order_id' => $orderId,
	                		'company_code'=>$orderRow['company_code'],
	                        'ref_no_platform' => $orderRow['refrence_no_platform'],
	                        'user_account' => $orderRow['user_account'],
	                        'item_id' => $p['op_ref_item_id'],
	                        'buyer_id' => $orderRow['buyer_id'],
	                        'title' => $subject,
	                        'content' => $content,
	                        'sync_status' => '0',
	                        'create_time'=>date("Y-m-d H:i:s"),
	                );
	                if(!Service_EbayFeedbackMessage::add($row)){
	                    throw new Exception('保存message失败');
	                } 
	            }
	        }
	        $return = array (
	                "ask" => 1,
	                "message" => "操作成功，Message稍后会发送给买家，请关闭该提示，继续其它操作",
	        );
	        $db->commit ();
	    } catch ( Exception $e ) {
	        $db->rollback ();
	        $return = array (
	                "ask" => 0,
	                "message" => $e->getMessage (),
	                'errorCode' => $e->getCode ()
	        );
	    }
	    return $return;
	}
	
	/**
	 * 针对客户评价发送消息
	 * @param unknown_type $ecfIds
	 * @param unknown_type $subject
	 * @param unknown_type $content
	 * @param unknown_type $language
	 * @throws Exception
	 * @return Ambigous <multitype:number string , multitype:number NULL >
	 */
	public  static function saveEbayFeedbackMessageTransactionForCustomer($ecfIds,$subject,$content,$language='zh'){
		$return = array (
				"ask" => 0,
				"message" => "Operation Fail"
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			if(empty($subject)){
				throw new Exception('标题不能为空');
			}else if(strlen($subject) > 100){
				throw new Exception('标题不能超过100个字符');
			}
			if(empty($content)){
				throw new Exception('内容不能为空');
			}
			$content = self::formateFeedBackMessageContent ( $content,$language );
			
			foreach ($ecfIds as $ecfId){
				$customerFeedbackResult = Service_EbayCustomerFeedback::getByField($ecfId,'ecf_id');
				if(empty($customerFeedbackResult)){
					throw new Exception("客户评价不存在-->".$ecfId);
				}
				$userAccount = $customerFeedbackResult['ecf_ebay_account'];
				$refrenceNoPlatform = $customerFeedbackResult['ecf_order_line_item_id'];
				$buyerId = $customerFeedbackResult['ecf_commenting_user'];
				$itemId = $customerFeedbackResult['ecf_item_id'];
				$itemTitle = $customerFeedbackResult['ecf_item_title'];
				
				//替换ItemId
				$subject = preg_replace('/\[item_id\]/', $itemId, $subject);
				//替换ItemTitle
				if(!empty($itemTitle)){
					if(strlen($itemTitle) > 30){
						$itemTitle = substr( $itemTitle, 0, 30).'...';
					}
					$subject = preg_replace('/\[item_title\]/', $itemTitle, $subject);
				}else{
					$subject = 'Message For eBay User';
				}
				
				$row = array(
						'ref_no_platform' => $refrenceNoPlatform,
						'user_account' => $userAccount,
						'item_id' => $itemId,
						'buyer_id' => $buyerId,
						'title' => $subject,
						'content' => $content,
						'sync_status' => '0',
						'create_time'=>date("Y-m-d H:i:s"),
				);
				if(!Service_EbayFeedbackMessage::add($row) || !Service_EbayCustomerFeedback::update(array('ecf_proecss_status'=>'1'),$ecfId,'ecf_id')){
					throw new Exception('保存message失败');
				}
			}
			
			$return = array (
					"ask" => 1,
					"message" => "操作成功，Message稍后会发送给买家，请关闭该提示，继续其它操作",
			);
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$return = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode ()
			);
		}
		return $return;
	}
	
	public static function getTemplateGroupMessageTemplate(){
	    
	    $cacheFile = APPLICATION_PATH.'/../data/tpl_c/groupMessageTpl'.Common_Company::getCompanyCode().'.txt';
 	    if(file_exists($cacheFile)&&time()-filemtime($cacheFile)<=600){//
 	        $content = file_get_contents($cacheFile);
 	        $list = unserialize($content);
 	    }else{
	        //这一段写的很垃圾，暂时先这样
	        $con = array('company_code'=>Common_Company::getCompanyCode(),'group_pid'=>0);
	      
	        $list = Service_TemplateGroup::getByCondition($con);
// 	        print_r($list);exit;
	        foreach($list as $k=> $v){
	            $con = array('company_code'=>Common_Company::getCompanyCode(),'group_pid'=>$v['group_id']);
	            $list1 = Service_TemplateGroup::getByCondition($con);
	            foreach($list1 as $k1=> $v1){
	                $con = array('company_code'=>Common_Company::getCompanyCode(),'group_pid'=>$v1['group_id']);
	                $list2 = Service_TemplateGroup::getByCondition($con);
	                foreach($list2 as $k2=>$v2){
	                    $conn1 = array('template_group_id'=>$v2['group_id'],'status'=>'2');
	                    $templates = Service_MessageTemplate::getByCondition($conn1);
	                    if(empty($templates)){
	                    	unset($list2[$k2]);
	                    }else{
		                    foreach($templates as $k3=>$template){
		                        $conn2 = array('template_id'=>$template['template_id']);
		                        $lanContent = Service_MessageTemplateContent::getByCondition($conn2);
		                        $templates[$k3]['lan'] = $lanContent;
		                    }
			                $v2['templates'] = $templates ;
			                $list2[$k2] = $v2;
	                    }
	                }
                    ksort($list2);
	                $list1[$k1]['sub'] = $list2;
	            }
	            $list[$k]['sub'] = $list1;
	        }
	        file_put_contents($cacheFile, serialize($list));
 	    }
	    return $list;
	}
	//标记已回复
	public static function feedBackMessageTag($messageId,$type = 'message_id'){
      $return = array(
         'ask' => 0,
         'message' => ''
      );
      try{
         $message = Service_EbayMessage::getByField($messageId, $type);
         if(empty($message)){
            throw new Exception('message不存在');
         }
         if($message['response_sync'] == "1"){ // 如果已经同步，则跳过
            throw new Exception('message已经回复给买家，不可继续操作');
         }
         $updateRow = array(
            'status' => '1',
            'response_time' => date('Y-m-d H:i:s'),
	        'response_status'=>'3',
            'customer_service_response' => '' // 响应客服
         );
         if(! Service_EbayMessage::update($updateRow, $message['ebay_message_id'], 'ebay_message_id')){
            throw new Exception('标记回复失败');
         }
         $return = array(
               'ask' => 1,
               'message' => '标记回复成功'
         );
      }catch(Exception $e){
         $return = array(
            'ask' => 0,
            'message' => $e->getMessage()
         );
      }
      return $return;
   }
   /**
    * 撤销ebay消息回复
    * @param unknown_type $messageId
    * @param unknown_type $type
    */
   public static function unFeedBackMessageTag($messageId,$type = 'message_id'){
	   	$return = array(
	   			'ask' => 0,
	   			'message' => ''
	   	);
	   	try{
	   		$message = Service_EbayMessage::getByField($messageId, $type);
	   		if(empty($message)){
	   			throw new Exception('message不存在');
	   		}
	   		if($message['response_sync'] == "1"){ 
	   			/*
	   			 * 已经同步，不能操作
	   			 */
	   			throw new Exception("消息已经同步到ebay，无法撤销");
	   		}else{
	   			/*
	   			 * 回复到系统，未同步到ebay，可以进行撤销
	   			 */
		   		$updateRow = array(
		   				'response_time' => date('Y-m-d H:i:s'),
		   				'response_status'=>'0',
		   				'customer_service_response' => '' // 响应客服
		   		);
		   		if(! Service_EbayMessage::update($updateRow, $message['ebay_message_id'], 'ebay_message_id')){
		   			throw new Exception('撤销回复失败');
		   		}
		   		$return = array(
		   				'ask' => 1,
		   				'message' => '撤销回复成功'
		   		);
	   		}
	   	}catch(Exception $e){
	   		$return = array(
	   				'ask' => 0,
	   				'message' => $e->getMessage()
	   		);
	   	}
	   	return $return;
   }
      
      // 分配客服
   public static function messageAllot($messageId, $customer_service_id,$type='message_id')
   {
      $return = array(
            'ask' => 0,
            'message' => ''
      );
      try{
         $message = Service_EbayMessage::getByField($messageId, $type);
         if(empty($message)){
            throw new Exception('message不存在');
         }
         if($message['response_sync'] == "1"){ // 如果已经同步，则跳过
            throw new Exception('message已经回复给买家，不可继续操作');
         }
         $updateRow = array(
               'customer_service_id' => $customer_service_id
         );
         if(! Service_EbayMessage::update($updateRow, $message['ebay_message_id'], 'ebay_message_id')){
            throw new Exception('分配失败');
         }
         $return = array(
               'ask' => 1,
               'message' => '分配客服成功'
         );
      }catch(Exception $e){
         $return = array(
               'ask' => 0,
               'message' => $e->getMessage()
         );
      }
      return $return;
   }
   
   //自动补全一些不对称的TABLE,TD,DIV标签
   public static function check_html_format($string){
        preg_match_all("/<div([^>]*)>/", $string, $array0);
        preg_match_all("/<\/div>/", $string, $array1);
        $num0 = count($array0[0]);
        $num1 = count($array1[0]);
        $divNUM = abs($num0 - $num1);
        for($i = 0;$i < $divNUM;$i ++){
            if($num0 > $num1){
                $string .= "</div>";
            }else{
                $string = "<div>$string";
            }
            break;
        }
        preg_match_all("/<td([^>]*)>/", $string, $array0);
        preg_match_all("/<\/td>/", $string, $array1);
        $num0 = count($array0[0]);
        $num1 = count($array1[0]);
        $tdNUM = abs($num0 - $num1);
        for($i = 0;$i < $tdNUM;$i ++){
            if($num0 > $num1){
                $string .= "</td>";
            }else{
                $string = "<td>$string";
            }
            break;
        }
        preg_match_all("/<table([^>]*)>/", $string, $array0);
        preg_match_all("/<\/table>/", $string, $array1);
        $num0 = count($array0[0]);
        $num1 = count($array1[0]);
        $tableNUM = abs($num0 - $num1);
        for($i = 0;$i < $tableNUM;$i ++){
            if($num0 > $num1){
                $string .= "</table>";
            }else{
                $string = "<table>$string";
            }
            break;
        }
        if($tdNUM > 1 || $tdNUM > 1 || $tableNUM > 1){
            $string = self::check_html_format($string);
        }
        return $string;
    }

    /**
     * 格式化消息内容
     * @param string $content html内容
     * @return Ambigous <string, phpQuery, QueryTemplatesSource, QueryTemplatesParse, QueryTemplatesSourceQuery, phpQueryObject, unknown_type, void, boolean, mixed>
     */
    public static function formatEbayMessageContent($content){
        $content = html_entity_decode($content,ENT_QUOTES,'UTF-8');
        $content = preg_replace('/\<(\/?)html(\/?)\>/', '', $content);
        $content = preg_replace('/\<(\/?)body(\/?)\>/', '', $content);
        $content = preg_replace('/\<(\/?)head(\/?)\>/', '', $content);
        $content = preg_replace('/&nbsp;/', '', $content);
    
        return $content;
        //         echo preg_replace('/<(\/?)html(\/?)>/', '', '<html><head></head>');exit;
        //         echo $content;
        // exit;
        require_once 'phpQuery-onefile.php';
    
        $doc = phpQuery::newDocument($content);
    
    
        // Add the keys to the nav bar
        $result = $doc->find('#TextCTA');
        $TextCTA = array();
        foreach($result as $k => $v) {
            $TextCTA[] = '<div id="TextCTA">'.pq($v)->html().'</div>';
        }
    
        $result = $doc->find('#RawHtmlText');
        $RawHtmlText = array();
        foreach($result as $k => $v) {
            $RawHtmlText[] = pq($v)->html();
        }
        //         echo count($RawHtmlText);exit;
    
        $result = $doc->find('#ItemDetails');
        $ItemDetails = array();
        foreach($result as $k => $v) {
            $ItemDetails[] = pq($v)->html();
        }
    
        $html = ''; 
        foreach($TextCTA as $k => $v) {
            $html.=$v.'<hr/>';
            //             $html.=$RawHtmlText[$k];
        }  
        $html.=$ItemDetails[0];            
        
        return $html;
    }

    public static function getHistoryMessage($message){
        /*
         * 8.往来消息 ,同一个买家，同一个ItemID，接收时间小于当前消息时间
        */
        if(!$message['item_id']){
            $historyMessages = array();
            return $historyMessages;
        }
        $db = Common_Common::getAdapter();
        $sql = 'SELECT b.currt_content,b.response_content,a.send_time FROM `ebay_message` a INNER JOIN ebay_message_content b on a.message_id=b.message_id ';
        $sql .= "where 1=1 ";
        $sql .= " and a.sender_id='" . $message['sender_id'] . "'";
        $sql .= " and a.item_id='" . $message['item_id'] . "'";
        $sql .= " and a.send_time<'" . $message['send_time'] . "'";
    
        $sql .= " union ";
    
        $sql .= " SELECT '' currt_content,content response_content,sync_time send_time FROM `ebay_feedback_message` ";
        $sql .= " where buyer_id='" . $message['sender_id'] . "' ";
        $sql .= " and item_id='" . $message['item_id'] . "' ";
        $sql .= " and sync_time<'" . $message['send_time'] . "' ";
    
        $sql .= " order by send_time desc limit 100";
    
    
        $historyMessages = $db->fetchAll($sql);
        foreach($historyMessages as $k => $v){
            $v['response_content_text'] = $v['response_content'];
            $v['response_content_title'] = empty($v['response_content']) ? "无回复内容" : '本消息已被编辑过至少一次';
            $v['currt_content'] = Service_EbayMessageProcess::formatEbayMessageContent($v['currt_content'],false,false);
            $v['response_content'] = str_replace("\n", '<br/>', $v['response_content']);
            $historyMessages[$k] = $v;
        }
    
        return $historyMessages;
    
    }
}