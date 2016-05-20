<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cl
 * Date: 13-6-17
 * Time: 下午4:21
 * To change this template use File | Settings | File Templates.
 */
ini_set("display_errors", "OFF");
require_once 'eBaySession.php';
require_once 'eBaySoapSession.php';

require_once 'XmlHandle.php';

//require_once 'Zend/Config/Xml.php';
class Ebay_EbayLib{

    public static function getUserToken($userAccount, $companyCode)
    {
        return Common_Company::getUserToken($userAccount, $companyCode);
    }
    /**
     * 获取EBAY 交易数据
     * @param $token
     * @param $start
     * @param $end
     * @param $loadCount
     * @return array|null
     */
    public static function GetItemTransactions($token,$ItemID,$TransactionID=''){
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                        <GetItemTransactionsRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
                          <ItemID>'.$ItemID.'</ItemID>';
          if($TransactionID!==''){
              $requestXmlBody .='<TransactionID>'.$TransactionID.'</TransactionID>';
          }                       
          
         $requestXmlBody .='<RequesterCredentials>
                            <eBayAuthToken>'.$token.'</eBayAuthToken>
                          </RequesterCredentials>
                        </GetItemTransactionsRequest>';
        $session = new eBaySession($token,
                Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
                Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
                Common_Company::getEbayVersion(), '0',
                'GetItemTransactions');
//                 echo $requestXmlBody;exit;
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        
        try{
            $data = XML_unserialize($responseXml);
            $headeer = $session->getEbayHeaders();
        }catch (Exception $e){
            echo $e->getMessage()."ebaylib";exit;
        }
        return $data;
    }
    /**
     * 获取EBAY ORDER数据
     * @param $token
     * @param $start
     * @param $end
     * @param $loadCount
     * @return array|null
     */
    public static  function  getEbayOrders($token,$start,$end,$page,$orderIds=array()){
        $orderIdString = '';
        if(!empty($orderIds)){
            $orderIdString.='<OrderIDArray>';
            foreach($orderIds as $id){
                $orderIdString.='<OrderID>'.$id.'</OrderID>';
            }
            $orderIdString.='</OrderIDArray>';
        }
		$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
    			<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    			  <RequesterCredentials>
    				<eBayAuthToken>' . $token . '</eBayAuthToken>
    			  </RequesterCredentials>
                <ModTimeFrom>' . $start . '</ModTimeFrom>
                <ModTimeTo>' . $end . '</ModTimeTo>
                   <Pagination>
    					<EntriesPerPage>100</EntriesPerPage>
    					<PageNumber>' . $page . '</PageNumber>
    				  </Pagination>
    			     <DetailLevel>ReturnAll</DetailLevel>
    				<IncludeFinalValueFee>true</IncludeFinalValueFee>
    				<OrderRole>Seller</OrderRole>
					
			        '.$orderIdString.'
    			</GetOrdersRequest>';

        $session = new eBaySession($token,
            Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
            Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
            Common_Company::getEbayVersion(), '0',
            'GetOrders');
//         echo $requestXmlBody;exit;
        $responseXml = $session->sendHttpRequest($requestXmlBody);

        try{
			$data = XML_unserialize ( $responseXml );
			$headeer = $session->getEbayHeaders();
// 			file_put_contents ( 'eb_response_arr.txt', print_r ( $data, true ) );
//			Ec::showError("\n\n".date('Y-m-d H:i:s')."\nrequest:\n".print_r($headeer,true)."\n".preg_replace('/>\s+</','><',$requestXmlBody)."\n\nresponse:\n".preg_replace('/>\s+</','><',$responseXml)."\nreponse_arr:\n".print_r($data,true),'ebxml__' );
		}catch (Exception $e){
            echo $e->getMessage()."ebaylib";
        }
        return $data;
    }

    /**
     * 获取EBAY ORDER数据
     * @param $token
     * @param $start
     * @param $end
     * @param $loadCount
     * @return array|null
     */
    public static  function  getEbayOrdersId($token,$start,$end,$page){
       
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
    			<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    			  <RequesterCredentials>
    				<eBayAuthToken>' . $token . '</eBayAuthToken>
    			  </RequesterCredentials>
                    <ModTimeFrom>' . $start . '</ModTimeFrom>
                    <ModTimeTo>' . $end . '</ModTimeTo>
                   <Pagination>
    					<EntriesPerPage>100</EntriesPerPage>
    					<PageNumber>' . $page . '</PageNumber>
    				  </Pagination>
    			     <DetailLevel>ReturnAll</DetailLevel>
			        <OutputSelector>OrderArray.Order.OrderID</OutputSelector>
			        <OutputSelector>HasMoreOrders</OutputSelector>
			        <OutputSelector>PaginationResult</OutputSelector>
			        <OutputSelector>PaginationResult.TotalNumberOfPages</OutputSelector>
			        <OutputSelector>PaginationResult.TotalNumberOfEntries</OutputSelector>
    				<IncludeFinalValueFee>false</IncludeFinalValueFee>
    				<OrderRole>Seller</OrderRole>
    			</GetOrdersRequest>';
        
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), '0', 'GetOrders');
        // echo $requestXmlBody;exit;
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        
        try{
            $data = XML_unserialize($responseXml);
            $headeer = $session->getEbayHeaders();
        }catch(Exception $e){
            echo $e->getMessage() . "ebaylib";
        }
        return $data;
    }

    /**
     * 获取ebay session id
     */
    public  static function GetAccount($token,$page,$ItemID=''){    
        $requestXmlBody = '
        <?xml version="1.0" encoding="utf-8"?>
        <GetAccountRequest xmlns="urn:ebay:apis:eBLBaseComponents">
           <RequesterCredentials>
            <eBayAuthToken>'.$token.'</eBayAuthToken>
          </RequesterCredentials>
          <ExcludeBalance>false</ExcludeBalance>
          <ExcludeSummary>false</ExcludeSummary>
          <IncludeConversionRate>true</IncludeConversionRate>
          ';
        if(!empty($ItemID)){
            $requestXmlBody.='<ItemID>'.$ItemID.'</ItemID>';
        }
        $requestXmlBody.='
          <Pagination>
            <EntriesPerPage>40</EntriesPerPage>
            <PageNumber>'.$page.'</PageNumber>
          </Pagination>
        </GetAccountRequest>';
        $session = new eBaySession($token,
            Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
            Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
            Common_Company::getEbayVersion(), '0',
            'GetAccount');
//         echo $requestXmlBody;exit;
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        
        try{
			$data = XML_unserialize ( $responseXml ); 
        }catch (Exception $e){
            echo $e->getMessage()."ebaylib";
        }
        return $data;
    }
    

    /**
     * 获取ebay session id
     */
    public function GetSessionID(){
    	$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                            <GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                    		<RuName>'.Common_Company::getEbayRuname().'</RuName>
                    		</GetSessionIDRequest>';
    	$session = new eBaySession('',
    			Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
    			Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
    			Common_Company::getEbayVersion(), '0',
    			'GetSessionID');

        Ec::showError(Common_Company::getEbayServerurl(),'2');
        Ec::showError('请求eBay，生成SessionID参数：' . print_r($requestXmlBody,1) ,'auth_ebay_20140821');
    	$responseXml = $session->sendHttpRequest($requestXmlBody);
    	$responseDoc = new DomDocument();
//         echo $responseXml;exit;
    	$responseDoc->loadXML($responseXml);

    	$errors     = $responseDoc->getElementsByTagName('Errors');

    	$data       = XML_unserialize($responseXml);
    	$getdata    = $data['GetSessionIDResponse'];
    	Ec::showError('生成SessionID：' . print_r($data,1) ,'auth_ebay_20140821');
    	//print_r($getdata);
    	if('Success' == $getdata['Ack']){
    		$sessionid  = @$getdata['SessionID'];
    		return $sessionid;
    	}else{
    		return false;
    	}
    }


    /**
     * 获取用户店铺TOKEN
     */
    public function GetToken($sessionid = '', $store_name = '', $appname = ''){

        if(empty($sessionid) || empty($store_name)){
            return false;
        }

        $requestxml	= '<?xml version="1.0" encoding="utf-8"?>
                		<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                		<RequesterCredentials></RequesterCredentials>
                		<SessionID>'.$sessionid.'</SessionID>
                		</FetchTokenRequest>';

        //$session = new eBaySession('', $this->config['DEVID'], $this->config['APPID'], $this->config['CERTID'], $this->config['SERVERURL'], '557','0',
        $session = new eBaySession('',
        		Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
        		Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
        		Common_Company::getEbayVersion(), '0', 'FetchToken');

        $responseXml = $session->sendHttpRequest($requestxml);

        $responseDoc = new DomDocument();

        $responseDoc->loadXML($responseXml);

        $errors      = $responseDoc->getElementsByTagName('Errors');

        $data        = XML_unserialize($responseXml);

        $getdata 	 = $data['FetchTokenResponse'];
		//print_r($getdata);
        if('Success' == $getdata['Ack']){
            return $getdata;
        } else {
            return false;
        }

    }
    /**
     * 获取用户店铺TOKEN
     */
    public function GetTokenNew($sessionid = ''){
        if(empty($sessionid)){
            return false;
        }        
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                		<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                		<RequesterCredentials></RequesterCredentials>
                		<SessionID>' . $sessionid . '</SessionID>
                		</FetchTokenRequest>';
        
        $session = new eBaySession('', Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), '0', 'FetchToken');
        Ec::showError('请求eBay，请求Token参数：' . print_r($requestxml,1) ,'auth_ebay_20140821');
        $responseXml = $session->sendHttpRequest($requestxml);
        
        $responseDoc = new DomDocument();
        
        $responseDoc->loadXML($responseXml);
        
        $errors = $responseDoc->getElementsByTagName('Errors');
        
        $data = XML_unserialize($responseXml);
        
        $getdata = $data['FetchTokenResponse'];
        Ec::showError('请求eBay，获得Token：' . print_r($responseXml,1) ,'auth_ebay_20140821');
        // print_r($getdata);
        if('Success' == $getdata['Ack']){
            return $getdata;
        }else{
            return false;
        }
    }

    
    /**
     * 获取messageID
     * @param $start date 开始时间
     * @param $end date 结束时间
     * @param $token string 凭证
     * @return $data Array 某一时间段所有的MessageId
     */
    public function GetEbayMessageID($start,$end,$token,$fold='0'){
        if(!isset($fold)){
            $fold = 0;
        }
        $requestxml  = '';
        $requestxml .= '<?xml version="1.0" encoding="utf-8"?>';
        $requestxml .= '<GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestxml .= '<RequesterCredentials>';
        $requestxml .= '<eBayAuthToken>' . $token . '</eBayAuthToken>';
        $requestxml .= '</RequesterCredentials>';
        $requestxml .= '<StartTime>' . $start . '</StartTime>';
        $requestxml .= '<EndTime>' . $end . '</EndTime>';
        $requestxml .= '<FolderID>' . $fold . '</FolderID>';
        $requestxml .= '<DetailLevel>ReturnHeaders</DetailLevel>';
        $requestxml .= '</GetMyMessagesRequest>';
        
        $mark = "GetMyMessages";
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), '0', $mark);
        
        $responseXml = $session->sendHttpRequest($requestxml);
        
        $responseDoc = new DomDocument();
        
        $responseDoc->loadXML($responseXml);
        
        $errors = $responseDoc->getElementsByTagName('Errors');
        
        $data = XML_unserialize($responseXml);
        
        return $data;
    }


    /**
     * 获取messageID
     * @param $start date 开始时间
     * @param $end date 结束时间
     * @param $token string 凭证
     * @return $data Array 某一时间段所有的MessageId
     */
    public function GetEbayMessageHeader($start,$end,$token,$fold='0'){
        return $this->GetEbayMessageID($start, $end, $token,$fold);
    }
    /**
     * 获取messageID
     * @param $start date 开始时间
     * @param $end date 结束时间
     * @param $token string 凭证
     * @return $data Array 某一时间段所有的MessageId
     */
    public function GetEbayMessageSummary($start,$end,$token){
        $requestxml	= '<?xml version="1.0" encoding="utf-8"?>
						<GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						<RequesterCredentials>
								<eBayAuthToken>'.$token.'</eBayAuthToken>
						</RequesterCredentials>
						   	<StartTime>'.$start.'</StartTime>
						   	<EndTime>'.$end.'</EndTime>
						  	<DetailLevel>ReturnSummary</DetailLevel>
						</GetMyMessagesRequest>';
    
        $mark = "GetMyMessages";
        $session = new eBaySession($token,
                Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
                Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
                Common_Company::getEbayVersion(), '0', $mark);
    
    
        $responseXml = $session->sendHttpRequest($requestxml);
    
        $responseDoc = new DomDocument();
    
        $responseDoc->loadXML($responseXml);
    
        $errors      = $responseDoc->getElementsByTagName('Errors');
    
        $data        = XML_unserialize($responseXml);
         
        return $data;
    }

    public static  function  getEbayOrdersById($token,$ids){
        $orderIds = "";
        foreach($ids as $v){
            $orderIds .= "<OrderID>{$v}</OrderID>";
        }
        
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
    			<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    			  <RequesterCredentials>
    				<eBayAuthToken>' . $token . '</eBayAuthToken>
    			  </RequesterCredentials>
                  <OrderIDArray>
                    ' . $orderIds . '
                  </OrderIDArray>
                   <Pagination>
    					<EntriesPerPage>100</EntriesPerPage>
    					<PageNumber>1</PageNumber>
    				  </Pagination>
    			     <DetailLevel>ReturnAll</DetailLevel>
    				<IncludeFinalValueFee>true</IncludeFinalValueFee>
    				<OrderRole>Seller</OrderRole>
    			</GetOrdersRequest>';
        
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), '0', 'GetOrders');
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        try{
            $data = XML_unserialize($responseXml);
            $headeer = $session->getEbayHeaders();
        }catch(Exception $e){
            echo $e->getMessage() . "ebaylib";
        }
        return $data;
    }
    
    public static function CompleteSale($token,$orderId,$trackNumber,$shippingMethod,$shipTime){
        
        $shipment = '';
        if(!empty($shippingMethod)&&!empty($trackNumber)&&strtoupper($trackNumber)!='NULL'){
            if($shippingMethod=='DHL'){
                $shipment.='<Shipment>
                		<ShipmentTrackingDetails>
                			<ShipmentTrackingNumber>'.$trackNumber.'</ShipmentTrackingNumber>
                			<ShippingCarrierUsed>'.$shippingMethod.'</ShippingCarrierUsed>
                		</ShipmentTrackingDetails>      
                        <ShippedTime>'.$shipTime.'</ShippedTime>          		
                	</Shipment>';
            }else{
                $shipment.='<Shipment>
                		<ShipmentTrackingDetails>
                			<ShipmentTrackingNumber>'.$trackNumber.'</ShipmentTrackingNumber>
                			<ShippingCarrierUsed>'.$shippingMethod.'</ShippingCarrierUsed>
                		</ShipmentTrackingDetails>
                        <ShippedTime>'.$shipTime.'</ShippedTime>
                	</Shipment>';
            }                       
        }
        
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                        	<WarningLevel>Low</WarningLevel>
                        	<OrderID>'.$orderId.'</OrderID>
                        	<Paid>true</Paid>
                        	<Shipped>true</Shipped>
                        	'.$shipment.'
                        	<RequesterCredentials>
                        		<eBayAuthToken>'.$token.'</eBayAuthToken>
                        	</RequesterCredentials>
                        </CompleteSaleRequest>';
        $mark = "CompleteSale";
        $session = new eBaySession($token,
                Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
                Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
                Common_Company::getEbayVersion(), '0', $mark);
        
        
        $responseXml = $session->sendHttpRequest($requestxml);
        
        $responseDoc = new DomDocument();
        
        $responseDoc->loadXML($responseXml);
        
        $errors      = $responseDoc->getElementsByTagName('Errors');
        
        $data        = XML_unserialize($responseXml);
         
        return $data;
    }
    /**
     * 获取messageInfo
     * @param $token string 用户凭证
     * @param $MessageId int 单条message的Id
     * @return $data Array 单条message的详细信息
     */
    public function GetEbayMessageInfo($token,$MessageId){
    	$requestxml	= '<?xml version="1.0" encoding="utf-8"?>
						 <GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents"> <DetailLevel>ReturnMessages</DetailLevel>
						 <RequesterCredentials>
						 <eBayAuthToken>'.$token.'</eBayAuthToken>
						 </RequesterCredentials>
						 <MessageIDs>
						 <MessageID>'.$MessageId.'</MessageID>
						 </MessageIDs>
						 </GetMyMessagesRequest>';
						 
		$mark = "GetMyMessages";
		$session = new eBaySession($token,
				Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
				Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
				Common_Company::getEbayVersion(), '0', $mark);
		
		
		$responseXml = $session->sendHttpRequest($requestxml);
		
		$responseDoc = new DomDocument();
		
		$responseDoc->loadXML($responseXml);
		
		$errors      = $responseDoc->getElementsByTagName('Errors');
		
		$data        = XML_unserialize($responseXml);
    	
		return $data;
    }
    

    /**
     * 获取批量messageInfo
     * @param $token string 用户凭证
     * @param $MessageId int 单条message的Id
     * @return $data Array 单条message的详细信息
     */
    public function GetEbayMessageInfoBatch($token,$MessageIdArr,$selected=false){
        $MessageIdArr = array_splice($MessageIdArr,0,10);//前10条        
        $ids = '';
        foreach($MessageIdArr as $MessageId){
            $ids.='<MessageID>'.$MessageId.'</MessageID>';
        }
        if($selected){
            $requestxml	= '<?xml version="1.0" encoding="utf-8"?>
						 <GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents"> <DetailLevel>ReturnMessages</DetailLevel>
						 <RequesterCredentials>
						 <eBayAuthToken>'.$token.'</eBayAuthToken>
						 </RequesterCredentials>
						 <MessageIDs>
						'.$ids.'
						 </MessageIDs>
                         <OutputSelector>Messages.Message.Content,Messages.Message.Text,Messages.Message.MessageID</OutputSelector>
						 </GetMyMessagesRequest>';
        }else{

            $requestxml	= '<?xml version="1.0" encoding="utf-8"?>
						 <GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents"> <DetailLevel>ReturnMessages</DetailLevel>
						 <RequesterCredentials>
						 <eBayAuthToken>'.$token.'</eBayAuthToken>
						 </RequesterCredentials>
						 <MessageIDs>
						'.$ids.'
						 </MessageIDs>
						 </GetMyMessagesRequest>';
        }
        	
        $mark = "GetMyMessages";
        $session = new eBaySession($token,
                Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
                Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
                Common_Company::getEbayVersion(), '0', $mark);
    
    
        $responseXml = $session->sendHttpRequest($requestxml);
    
        $responseDoc = new DomDocument();
    
        $responseDoc->loadXML($responseXml);
    
        $errors      = $responseDoc->getElementsByTagName('Errors');
    
        $data        = XML_unserialize($responseXml);
        
//         Ec::showError(print_r($data,true),'con');
        return $data;
    }
    
    
    /**
     * 获取feedback(已废弃)
     * @param unknown_type $start
     * @param unknown_type $end
     * @param unknown_type $token
     */
	public function GetFeedback($start,$end,$token){
// 		echo 25%26;
// 		exit;
		//获取所有评论的信息
		$uname = 'testuser_ezwmsjason';
		
		$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<GetFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						  <RequesterCredentials>
						    <eBayAuthToken>'.$token.'</eBayAuthToken>
						  </RequesterCredentials>
						  <UserID>'.$uname.'</UserID>
						  <DetailLevel>ReturnAll</DetailLevel>
						  <Pagination>
						    <EntriesPerPage>100</EntriesPerPage>
						    <PageNumber>1</PageNumber>
						  </Pagination>
						</GetFeedbackRequest>';
		
		/*
		$requestxml = '<?xml version="1.0" encoding="utf-8"?>
		<RespondToFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <RequesterCredentials>
		    <eBayAuthToken>'.$token.'</eBayAuthToken>
		  </RequesterCredentials>
		  <TargetUserID>testuser_ezwmsxiaoqiang</TargetUserID>
		  <ItemID>110117925222</ItemID>
		  <TransactionID>0</TransactionID> 
		  <ResponseType>Reply</ResponseType> 
		  <ResponseText>.好差啊.</ResponseText> 
		</RespondToFeedbackRequest>';
		*/
		
		
		$session = new eBaySession($token,
				Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
				Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
				Common_Company::getEbayVersion(), '0', 'GetFeedback');
		
		
		$responseXml = $session->sendHttpRequest($requestxml);
		
		$responseDoc = new DomDocument();
		
		$responseDoc->loadXML($responseXml);
		
		$errors      = $responseDoc->getElementsByTagName('Errors');
		
		$data        = XML_unserialize($responseXml);
		 
		
		print_r($data);
	}


    public static function GetUser($token){
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <GetUserRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>'.$token.'</eBayAuthToken>
                          </RequesterCredentials>
                        </GetUserRequest> ';

        $session = new eBaySession('',
            Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
            Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
            '823', '0',
            'getUser');
        $responseXml = $session->sendHttpRequest($requestxml);
        $data        = XML_unserialize($responseXml);
        return $data;

    }

    public static function GetEbayItem($start, $end, $token, $page = 1,$type='Start') {
        if($type=='Start'){
            return self::GetSellerList($start, $end, $token, $page);
        }else{
            return self::GetSellerListEnd($start, $end, $token, $page);
        }        
    }
    /**
     * 开始的item
     * @param unknown_type $start
     * @param unknown_type $end
     * @param unknown_type $token
     * @param unknown_type $page
     * @return Ambigous <NULL, multitype:>
     */
    public static function GetSellerList($start, $end, $token, $page = 1) {
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>' . $token . '</eBayAuthToken>
                          </RequesterCredentials>
                            <WarningLevel>High</WarningLevel>
                            <GranularityLevel>Fine</GranularityLevel>
                          <StartTimeFrom>' . $start . '</StartTimeFrom>
                          <StartTimeTo>' . $end . '</StartTimeTo>
                          <IncludeWatchCount>true</IncludeWatchCount>
                          <IncludeVariations>true</IncludeVariations>
                           <Pagination>
                            <EntriesPerPage>100</EntriesPerPage>
                            <PageNumber>' . $page . '</PageNumber>
                          </Pagination>
                        </GetSellerListRequest>';
        $requestxml = preg_replace ( '/>\s+</', '><', $requestxml );
//         echo $requestxml;exit;
        $session = new eBaySession ( $token, Common_Company::getEbayDevid (), Common_Company::getEbayAppid (), Common_Company::getEbayCertid (), Common_Company::getEbayServerurl (), '823', '0', 'GetSellerList' );
        $responseXml = $session->sendHttpRequest ( $requestxml );
        $data = XML_unserialize ( $responseXml );
        return $data;
    }


    /**
     * 结束的item
     * @param unknown_type $start
     * @param unknown_type $end
     * @param unknown_type $token
     * @param unknown_type $page
     * @return Ambigous <NULL, multitype:>
     */
    public static function GetSellerListEnd($start, $end, $token, $page = 1) {
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>' . $token . '</eBayAuthToken>
                          </RequesterCredentials>
                            <WarningLevel>High</WarningLevel>
                            <GranularityLevel>Fine</GranularityLevel>
                          <EndTimeFrom>' . $start . '</EndTimeFrom>
                          <EndTimeTo>' . $end . '</EndTimeTo>
                          <IncludeWatchCount>true</IncludeWatchCount>
                          <IncludeVariations>true</IncludeVariations>
                           <Pagination>
                            <EntriesPerPage>100</EntriesPerPage>
                            <PageNumber>' . $page . '</PageNumber>
                          </Pagination>
                        <DetailLevel>ReturnAll</DetailLevel>
                        </GetSellerListRequest>';
        $requestxml = preg_replace ( '/>\s+</', '><', $requestxml );
        $session = new eBaySession ( $token, Common_Company::getEbayDevid (), Common_Company::getEbayAppid (), Common_Company::getEbayCertid (), Common_Company::getEbayServerurl (), '823', '0', 'GetSellerList' );
        $responseXml = $session->sendHttpRequest ( $requestxml );
        $data = XML_unserialize ( $responseXml );
        return $data;
    }

    public static function GetItem($token, $ItemID) {
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                <GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <RequesterCredentials>
                    <eBayAuthToken>'.$token.'</eBayAuthToken>
                  </RequesterCredentials>
                  <ItemID>'.$ItemID.'</ItemID>
                  <DetailLevel>ReturnAll</DetailLevel>
                  <IncludeItemSpecifics>true</IncludeItemSpecifics>
                </GetItemRequest>';
        $requestxml = preg_replace ( '/>\s+</', '><', $requestxml );
        $session = new eBaySession ( $token, Common_Company::getEbayDevid (), Common_Company::getEbayAppid (), Common_Company::getEbayCertid (), Common_Company::getEbayServerurl (), '823', '0', 'GetItem' );
        $responseXml = $session->sendHttpRequest ( $requestxml );
        $data = XML_unserialize ( $responseXml );
        //      $responseXml = preg_replace ( '/>\s+</', '><', $responseXml );
        //      file_put_contents ( APPLICATION_PATH . '/../data/log/GetSellerList.txt', $requestxml . "\n" . $responseXml . "\n\n", FILE_APPEND );
    
        //        print_r($data);exit;
        return $data;
    }
    
    /**
     * 修改产品的库存
     * @param string $token
     * @param array $inventoryArr
     * @return Ambigous <NULL, multitype:>
     */
    public static function ReviseInventoryStatus($token,$inventoryArr){
    
        $InventoryStatus = '';
        foreach($inventoryArr as $v){
            $InventoryStatus .= '<InventoryStatus>';
    
            $InventoryStatus .= '<ItemID>' . $v['item_id'] . '</ItemID>';
            $InventoryStatus .= '<Quantity>' . $v['qty'] . '</Quantity>';
            if(isset($v['start_price'])){
                $InventoryStatus .= '<StartPrice>' . $v['start_price'] . '</StartPrice>';
            }
            if(isset($v['sku'])){
                $InventoryStatus .= '<SKU>' . $v['sku'] . '</SKU>';
            }
            $InventoryStatus .= '</InventoryStatus>';
        }
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>'.$token.'</eBayAuthToken>
                          </RequesterCredentials>
                          <ErrorLanguage>en_US</ErrorLanguage>
                          <WarningLevel>High</WarningLevel>
                          '.$InventoryStatus.'
                        </ReviseInventoryStatusRequest>';
        $requestxml = preg_replace('/>\s+</', '><', $requestxml);
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), '823', '0', 'ReviseInventoryStatus');
        $responseXml = $session->sendHttpRequest($requestxml);
        $data = XML_unserialize($responseXml);
        return $data;
    }
    /**
     * 修改产品的库存
     * @param string $token
     * @param array $inventoryArr
     * @return Ambigous <NULL, multitype:>
     */
    public static function ReviseInventoryStatusSingle($token,$inventory){
    
        $InventoryStatus = '';
        $InventoryStatus .= '<InventoryStatus>';
        $InventoryStatus .= '<ItemID>' . $inventory['item_id'] . '</ItemID>';
        $InventoryStatus .= '<Quantity>' . $inventory['qty'] . '</Quantity>';
        if(isset($inventory['start_price'])){
            $InventoryStatus .= '<StartPrice>' . $inventory['start_price'] . '</StartPrice>';
        }
        if(isset($inventory['sku'])){
            $InventoryStatus .= '<SKU>' . $inventory['sku'] . '</SKU>';
        }
        $InventoryStatus .= '</InventoryStatus>';
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>'.$token.'</eBayAuthToken>
                          </RequesterCredentials>
                          <ErrorLanguage>en_US</ErrorLanguage>
                          <WarningLevel>Low</WarningLevel>
                          '.$InventoryStatus.'
                        </ReviseInventoryStatusRequest>';
        $requestxml = preg_replace('/>\s+</', '><', $requestxml);
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), '823', '0', 'ReviseInventoryStatus');
        $responseXml = $session->sendHttpRequest($requestxml);
        $data = XML_unserialize($responseXml);
        return $data;
    }

    /**
     * 修改产品的库存
     * @param string $token
     * @param array $priceArr
     * @return Ambigous <NULL, multitype:>
     */
    public static function RevisePriceStatusSingle($token,$price){
    
    	$InventoryStatus = '';
    	$InventoryStatus .= '<InventoryStatus>';
    	$InventoryStatus .= '<ItemID>' . $price['item_id'] . '</ItemID>';
    	if(isset($price['start_price'])){
    		$InventoryStatus .= '<StartPrice>' . $price['start_price'] . '</StartPrice>';
    	}
    	if(isset($price['sku'])){
    		$InventoryStatus .= '<SKU>' . $price['sku'] . '</SKU>';
    	}
    	$InventoryStatus .= '</InventoryStatus>';
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>'.$token.'</eBayAuthToken>
                          </RequesterCredentials>
                          <ErrorLanguage>en_US</ErrorLanguage>
                          <WarningLevel>Low</WarningLevel>
                          '.$InventoryStatus.'
                        </ReviseInventoryStatusRequest>';
    	$requestxml = preg_replace('/>\s+</', '><', $requestxml);
    	$session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), '823', '0', 'ReviseInventoryStatus');
    	$responseXml = $session->sendHttpRequest($requestxml);
    	$data = XML_unserialize($responseXml);
    	return $data;
    }
    /**
     * @param string $token                 
     * @return Ambigous <NULL, multitype:>
     */
    public static function ReviseItemOutOfStockControl($token, $itemId, $OutOfStockControl = 'true',$qty=null)
    {
        $quantity = '';
        if(isset($qty)){
//             $quantity = '<Quantity>'.$qty.'</Quantity>';
        }
        
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>' . $token . '</eBayAuthToken>
                          </RequesterCredentials>
                          <ErrorLanguage>en_US</ErrorLanguage>
                          <WarningLevel>Low</WarningLevel>
                           <Item>
                            <ItemID>' . $itemId . '</ItemID>
                            <OutOfStockControl>' . $OutOfStockControl . '</OutOfStockControl>  
                           '.$quantity.'                                   
                          </Item>
                        </ReviseItemRequest>';
        $requestxml = preg_replace('/>\s+</', '><', $requestxml);
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), '823', '0', 'ReviseItem');
        $responseXml = $session->sendHttpRequest($requestxml);
        $data = XML_unserialize($responseXml);
        return $data;
    }
    /**
     * 修改产品的数量
     * @param unknown_type $token
     * @param unknown_type $ItemID
     * @param unknown_type $qty
     * @param unknown_type $variations
     * @param unknown_type $variationSet
     * @return Ambigous <NULL, multitype:>
     */
    public static function ReviseFixedPriceItem($token, $ItemID,$qty=0,$variations=array(),$variationSet=array())
    {
        $variationStr = '';
        $variationSetStr = '';
        $qtyStr = '';
        if($variations){
            $variationStr .= '<Variations>';
            if($variationSet){
                $variationSetStr .= '<VariationSpecificsSet>';
                foreach($variationSet as $k => $v){
                    $variationSetStr .= '<NameValueList>';
                    $variationSetStr .= '<Name>' . $k . '</Name>';
                    foreach($v as $vv){
                        $variationSetStr .= '<Value>' . $vv . '</Value>';
                    }
                    $variationSetStr .= '</NameValueList>';
                }
    
                $variationSetStr .= '</VariationSpecificsSet>';
    
                $variationStr .= $variationSetStr;
            }
    
    
            foreach($variations as $v){
                $variationStr .= '<Variation>';
                $variationStr .= '<SKU>' . $v['sku'] . '</SKU>';
//                 $variationStr .= '<StartPrice>' . $v['start_price'] . '</StartPrice>';
                $variationStr .= '<Quantity>' . $v['qty'] . '</Quantity>';
                if($v['attr']){
                    $variationStr .= '<VariationSpecifics>';
                    foreach($v['attr'] as $k => $vv){
                        $variationStr .= '<NameValueList>';
                        $variationStr .= '<Name>' . $k . '</Name>';
                        if(is_array($vv)){
                            foreach($vv as $vvv){
                                $variationStr .= '<Value>' . $vvv . '</Value>';
                            }
                        }else{
                            $variationStr .= '<Value>' . $vv . '</Value>';
                        }
                        $variationStr .= '</NameValueList>';
                    }
    
                    $variationStr .= '</VariationSpecifics>';
                }
    
                $variationStr .= '</Variation>';
            }
            $variationStr .= '</Variations>';
            $qtyStr = '';
        }else{
            $qtyStr = '<Quantity>' . $qty . '</Quantity>';
        }
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                <ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <RequesterCredentials>
                    <eBayAuthToken>' . $token . '</eBayAuthToken>
                  </RequesterCredentials>
                 <Item>
                  <ItemID>' . $ItemID . '</ItemID>
                  ' . $qtyStr . '
                   ' . $variationStr . '
                  </Item>
                </ReviseFixedPriceItemRequest>';
        $requestxml = preg_replace('/>\s+</', '><', $requestxml);
//         echo $requestxml;exit;
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), '823', '0', 'ReviseFixedPriceItem');
        $responseXml = $session->sendHttpRequest($requestxml);
        $data = XML_unserialize($responseXml);
        return $data;
    }
    
    /**
     *
     * 回复message(已废弃)
     * @param string $token 凭证 $ebayMessageId,$RecipientID,$itemID
     * @param string $body 内容
     * @param int $ebayMessageId ebay message id
     * @param string $RecipientID 收件人ID
     * @param int $itemID 产品ID
     */
    public static function sendEbayMessage($token,$body,$RecipientIDs,$itemID){
        $Recipient = '';
        $body = preg_replace('/<br(\s+)?(\/)?>/i', "\n", $body);
        	
        foreach($RecipientIDs as $RecipientID){
            $Recipient.='<AddMemberMessagesAAQToBidderRequestContainer>
						    <CorrelationID>'.$RecipientID.'</CorrelationID>
						    <ItemID>'.$itemID.'</ItemID>
						    <MemberMessage>
						      <Body>
						        '.strip_tags($body).'
						      </Body>
						      <RecipientID>'.$RecipientID.'</RecipientID>
						    </MemberMessage>
						  </AddMemberMessagesAAQToBidderRequestContainer>';
        }
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<AddMemberMessagesAAQToBidderRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						  <RequesterCredentials>
						    <eBayAuthToken>ABC...123</eBayAuthToken>
						  </RequesterCredentials>
						  					'.$Recipient.'
						</AddMemberMessagesAAQToBidderRequest>';
    
        $session = new eBaySession($token,
                Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
                Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
                Common_Company::getEbayVersion(), '0', 'AddMemberMessagesAAQToBidder');
    
    
        $responseXml = $session->sendHttpRequest($requestxml);
    
        $responseDoc = new DomDocument();
    
        $responseDoc->loadXML($responseXml);
    
        $errors      = $responseDoc->getElementsByTagName('Errors');
    
        $data        = XML_unserialize($responseXml);
    
        return $data;
        //print_r($data);
    
    }
    
    /**
     *
     * 回复message(AddMemberMessageRTQ接口,ItemID选填)
     * @param string $token 凭证 $ebayMessageId,$RecipientID,$itemID
     * @param string $body 内容
     * @param int $ebayMessageId ebay message id
     * @param string $RecipientID 收件人ID
     * @param int $itemID 产品ID(选填)
     */
    public function ebayAnswerMessage($token,$body,$ebayMessageId,$RecipientID,$itemID){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<AddMemberMessageRTQRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						  <RequesterCredentials>
						    <eBayAuthToken>' . $token . '</eBayAuthToken>
						  </RequesterCredentials>';
    	if(!empty($itemID)){
    		$requestxml .= '<ItemID>' . $itemID . '</ItemID>';
    	}
    	$requestxml .= '<MemberMessage>
						    <Body>' . $body . '</Body>
						    <DisplayToPublic>true</DisplayToPublic>
						    <EmailCopyToSender>true</EmailCopyToSender>
						    <ParentMessageID>' . $ebayMessageId . '</ParentMessageID>
						    <RecipientID>' . $RecipientID . '</RecipientID>
						  </MemberMessage>
						</AddMemberMessageRTQRequest>';
    
    	$session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), '0', 'AddMemberMessageRTQ');
    
    	$responseXml = $session->sendHttpRequest($requestxml);
    
    	$responseDoc = new DomDocument();
    
    	$responseDoc->loadXML($responseXml);
    
    	$errors = $responseDoc->getElementsByTagName('Errors');
    
    	$data = XML_unserialize($responseXml);
    	return $data;
    }
    
    /**
     *
     * 回复message(使用AddMemberMessageAAQToPartner接口,ItemID必填)
     * @param string $token 凭证 $ebayMessageId,$RecipientID,$itemID
     * @param string $body 内容
     * @param int $ebayMessageId ebay message id
     * @param string $RecipientID 收件人ID
     * @param int $itemID 产品ID(必填)
     */
    public static function sendEbayMessageForOrder($token,$subject,$body,$RecipientID,$itemID){
         
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<AddMemberMessageAAQToPartnerRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						  <RequesterCredentials>
						    <eBayAuthToken>'.$token.'</eBayAuthToken>
						  </RequesterCredentials>
			    			<ItemID>'.$itemID.'</ItemID>
    						<MemberMessage>
    							<Subject>'.$subject.'</Subject>
						    	<Body>'.$body.'</Body>
						    	<QuestionType>CustomizedSubject</QuestionType>
						  		<RecipientID>'.$RecipientID.'</RecipientID>
  							</MemberMessage>
						</AddMemberMessageAAQToPartnerRequest>';
    
        $session = new eBaySession($token,
                Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
                Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
                Common_Company::getEbayVersion(), '0', 'AddMemberMessageAAQToPartner');
    
    
        $responseXml = $session->sendHttpRequest($requestxml);
    
        $responseDoc = new DomDocument();
    
        $responseDoc->loadXML($responseXml);
    
        $errors      = $responseDoc->getElementsByTagName('Errors');
    
        $data        = XML_unserialize($responseXml);
        
//         $requestxml = preg_replace('/>\s+</', "><", $requestxml);
//         $responseXml = preg_replace('/>\s+</', "><", $responseXml);
//         Ec::showError($requestxml."\n".$responseXml."\n\n",'ooo___');
//     	Ec::showError(print_r($data,true),'ooo');
        return $data;
        //print_r($data);
    
    }
    
    /**
     * 获得订单评价
     * @param unknown_type $token
     * @param unknown_type $pages
     * @param unknown_type $orderLineItemID
     * @return Ambigous <NULL, multitype:>
     */
    public static function getFeedbacks($token,$pages = 1,$orderLineItemID = ''){
    	
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?> 
							 <GetFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents"> 
							 <RequesterCredentials> 
							 <eBayAuthToken>'.$token.'</eBayAuthToken> 
							 </RequesterCredentials>
							 <Pagination>		 
							 	<EntriesPerPage>200</EntriesPerPage>
							 	<PageNumber>'.$pages.'</PageNumber>
							 </Pagination>';
		$requestxml .= (!empty($orderLineItemID))?'<OrderLineItemID>' .$orderLineItemID. '</OrderLineItemID>':'';
		$requestxml .=      '<FeedbackType>FeedbackReceived</FeedbackType>
							 <DetailLevel>ReturnAll</DetailLevel>		 
							 </GetFeedbackRequest>';
    	
    	$session = new eBaySession($token,
    			Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
    			Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
    			Common_Company::getEbayVersion(), '0', 'GetFeedback');
    	
    	$responseXml = $session->sendHttpRequest($requestxml);
    	    	
    	$responseDoc = new DomDocument();
    	
    	$responseDoc->loadXML($responseXml);
    	
    	$errors      = $responseDoc->getElementsByTagName('Errors');
    	
    	$data        = XML_unserialize($responseXml);
//     	print_r($errors);
//     	echo '<br/><br/>';
//     	print_r($data);
		return $data;
    }
    
    /**
     * 建立促销分类
     * @param unknown_type $token
     * @param unknown_type $startTime
     * @param unknown_type $endTime
     * @return Ambigous <NULL, multitype:>
     */
    public static function SetPromotionalSale($token,$promotionalName,$siteID,$pencent,$startTime,$endTime,$action='Add',$SaleID=''){
        $PromotionalSaleID = '';
        if(!empty($SaleID)){//当action为Delete或者Update,必须要
            $PromotionalSaleID = '<PromotionalSaleID>'.$SaleID.'</PromotionalSaleID>';
        }
        
        if(($action=='Delete'||$action=='Update')&&empty($SaleID)){
            throw new Exception('PromotionalSaleID 参数缺失');
        }
//         echo $action;exit;
        if($action=='Add'){
            $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                <SetPromotionalSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <RequesterCredentials>
                    <eBayAuthToken>' . $token . '</eBayAuthToken>
                  </RequesterCredentials>
                  <WarningLevel>Low</WarningLevel>
                  <Action>'.$action.'</Action>
                  <PromotionalSaleDetails>                    
                    <PromotionalSaleName>'.$promotionalName.'</PromotionalSaleName>
                    <PromotionalSaleType>PriceDiscountOnly</PromotionalSaleType>
                    <DiscountType>Percentage</DiscountType>
                    <DiscountValue>'.$pencent.'</DiscountValue>
                    <PromotionalSaleStartTime>' . $startTime . '</PromotionalSaleStartTime>
                    <PromotionalSaleEndTime>' . $endTime . '</PromotionalSaleEndTime>
                  </PromotionalSaleDetails>
                </SetPromotionalSaleRequest>';
        }elseif($action=='Update'){
            $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                <SetPromotionalSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <RequesterCredentials>
                    <eBayAuthToken>' . $token . '</eBayAuthToken>
                  </RequesterCredentials>
                  <WarningLevel>Low</WarningLevel>
                  <Action>'.$action.'</Action>
                  <PromotionalSaleDetails>
                    '.$PromotionalSaleID.'
                    <PromotionalSaleName>'.$promotionalName.'</PromotionalSaleName>
                    <PromotionalSaleType>PriceDiscountOnly</PromotionalSaleType>
                    <DiscountType>Percentage</DiscountType>
                    <DiscountValue>'.$pencent.'</DiscountValue>
                    <PromotionalSaleStartTime>' . $startTime . '</PromotionalSaleStartTime>
                    <PromotionalSaleEndTime>' . $endTime . '</PromotionalSaleEndTime>
                  </PromotionalSaleDetails>
                </SetPromotionalSaleRequest>';
        }elseif($action=='Delete'){
            $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                <SetPromotionalSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <RequesterCredentials>
                    <eBayAuthToken>' . $token . '</eBayAuthToken>
                  </RequesterCredentials>
                  <WarningLevel>Low</WarningLevel>
                  <Action>'.$action.'</Action>
                  <PromotionalSaleDetails>'.$PromotionalSaleID.'  </PromotionalSaleDetails>
                </SetPromotionalSaleRequest>';
        }
//         echo $requestxml;exit;
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), $siteID, 'SetPromotionalSale');
        
        $responseXml = $session->sendHttpRequest($requestxml);
        
        $responseDoc = new DomDocument();
        
        $responseDoc->loadXML($responseXml);
        
        $errors = $responseDoc->getElementsByTagName('Errors');
        
        $data = XML_unserialize($responseXml);
        return $data;
    }
    /**
     * 建立促销明细
     * @param unknown_type $token
     * @param unknown_type $startTime
     * @param unknown_type $endTime
     * @return Ambigous <NULL, multitype:>
     */
    public static function SetPromotionalSaleListings($token,$saleID,$itemIDArr,$action='Add'){
                
        if(empty($itemIDArr)){
            throw new Exception('PromotionalSaleItemIDArray 缺失');
        }
        $itemIDs = '';
        foreach($itemIDArr as $itemID){
            $itemIDs .= '<ItemID>' . $itemID . '</ItemID>';
        }
        if($action=='Delete'){
            $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <SetPromotionalSaleListingsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>' . $token . '</eBayAuthToken>
                          </RequesterCredentials>
                          <Action>'.$action.'</Action>
                          <PromotionalSaleID>' . $saleID . '</PromotionalSaleID>
                          <PromotionalSaleItemIDArray>' . $itemIDs . '</PromotionalSaleItemIDArray>
                        </SetPromotionalSaleListingsRequest>';
        }else{
            $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <SetPromotionalSaleListingsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                          <RequesterCredentials>
                            <eBayAuthToken>' . $token . '</eBayAuthToken>
                          </RequesterCredentials>
                          <Action>'.$action.'</Action>
                          <PromotionalSaleID>' . $saleID . '</PromotionalSaleID>
                          <PromotionalSaleItemIDArray>' . $itemIDs . '</PromotionalSaleItemIDArray>
                          <AllAuctionItems>false</AllAuctionItems>
                          <AllFixedPriceItems>false</AllFixedPriceItems>
                          <AllStoreInventoryItems>false</AllStoreInventoryItems>
                        </SetPromotionalSaleListingsRequest>';
        }
//         echo $requestxml;exit;
        
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), '0', 'SetPromotionalSaleListings');
        
        $responseXml = $session->sendHttpRequest($requestxml);
        
        $responseDoc = new DomDocument();
        
        $responseDoc->loadXML($responseXml);
        
        $errors = $responseDoc->getElementsByTagName('Errors');
        
        $data = XML_unserialize($responseXml);
        return $data;
    }

    /**
     * 获取促销
     * @param unknown_type $token
     * @param unknown_type $startTime
     * @param unknown_type $endTime
     * @return Ambigous <NULL, multitype:>
     */
    public static function GetPromotionalSaleDetails($token,$SaleID='',$SaleStatus=array()){
        $PromotionalSaleStatus = '';
        $PromotionalSaleID='';
        if(!empty($SaleID)){//如果传入了ID，状态忽略
            $PromotionalSaleID= '<PromotionalSaleID>'.$SaleID.'</PromotionalSaleID>';
        }else{
            foreach($SaleStatus as $s){
                $PromotionalSaleStatus.='<PromotionalSaleStatus>'.$s.'</PromotionalSaleStatus>';
            } 
        }
        
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                <GetPromotionalSaleDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                   <RequesterCredentials>
                      <eBayAuthToken>' . $token . '</eBayAuthToken>
                   </RequesterCredentials>
                  <WarningLevel>Low</WarningLevel>
                  '.$PromotionalSaleID.'
                  '.$PromotionalSaleStatus.'
                </GetPromotionalSaleDetailsRequest>';
        
        $session = new eBaySession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(), Common_Company::getEbayCertid(), Common_Company::getEbayServerurl(), Common_Company::getEbayVersion(), '0', 'GetPromotionalSaleDetails');
        
        $responseXml = $session->sendHttpRequest($requestxml);
        
        $responseDoc = new DomDocument();
        
        $responseDoc->loadXML($responseXml);
        
        $errors = $responseDoc->getElementsByTagName('Errors');
        
        $data = XML_unserialize($responseXml);
        return $data;
    }
    /**
     * 获得ebay账户的cases信息
     */
    public static function getUserCases($token,$pages= 1){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
							<getUserCasesRequest xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns="http://www.ebay.com/marketplace/resolution/v1/services">
  								<caseStatusFilter>
    								<caseStatus>CLOSED</caseStatus>
    								<caseStatus>OPEN</caseStatus>
  								</caseStatusFilter>
    							<paginationInput>
								    <entriesPerPage>200</entriesPerPage>
							    	<pageNumber>' . $pages . '</pageNumber>
								</paginationInput>
    							<sortOrder>CREATION_DATE_DESCENDING</sortOrder>
    						</getUserCasesRequest>';
    	try{
    		
    		$session = new eBaySoapSession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(),
    				Common_Company::getEbayCertid(), Common_Company::getEbayEndpoint(), "getUserCases");

    		$responseXml = $session->sendHttpRequest($requestxml);
    		//print_r($responseXml);
    		$responseDoc = new DomDocument();
    		
    		$responseDoc->loadXML($responseXml);
    		$errors      = $responseDoc->getElementsByTagName('Errors');
    		$data        = XML_unserialize($responseXml);
//     		print_r($data);
    		return  $data;
    	}catch(Exception $e){
    		echo $e->getMessage();
    	}
    }
    
    /**
     * 获得EBP开头的case信息详情
     */
    public static function getEBPCaseDetail($token,$caseId,$caseType){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
							<getEBPCaseDetailRequest xmlns="http://www.ebay.com/marketplace/resolution/v1/services">
								<caseId>
									<id>' . $caseId . '</id>
									<type>' . $caseType . '</type>
								</caseId>
							</getEBPCaseDetailRequest>';
    	try{
    		$session = new eBaySoapSession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(),
    				Common_Company::getEbayCertid(), Common_Company::getEbayEndpoint(), "getEBPCaseDetail");
    		$responseXml = $session->sendHttpRequest($requestxml);
    		
    		//print_r($responseXml);
    		$responseDoc = new DomDocument();
    		$responseDoc->loadXML($responseXml);
    		$errors      = $responseDoc->getElementsByTagName('Errors');
    		$data        = XML_unserialize($responseXml);
//     		print_r($data);
    		return  $data;
    	}catch(Exception $e){
    		echo $e->getMessage();
    	}
    }
    
    /**
     * 获得普通snad或inr类型case信息详情
     */
    public static function getDisputeDetail($token,$caseId){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<GetDisputeRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    						<RequesterCredentials> 
							 	<eBayAuthToken>'.$token.'</eBayAuthToken>
							</RequesterCredentials>
							<DisputeID>' . $caseId . '</DisputeID>
						</GetDisputeRequest>';
    	
    	$session = new eBaySession($token,
    			Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
    			Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
    			Common_Company::getEbayVersion(), '0', 'GetDispute');
    	 
    	$responseXml = $session->sendHttpRequest($requestxml);
    	
    	$responseDoc = new DomDocument();
    	 
    	$responseDoc->loadXML($responseXml);
    	 
    	$errors      = $responseDoc->getElementsByTagName('Errors');
    	 
    	$data        = XML_unserialize($responseXml);

    	return $data;
    }
    
    /**
     * 查看客户期望的case响应接口（case类型：EBP_INR，EBP_SNAD）
     * 此API，根据客户的纠纷期待，返回客户希望得到响应结果的API。例如：客户期待收到SKU，该接口就会返回，提供承运商和提供跟踪号信息的接口给我们。
     * @param unknown_type $token
     * @param unknown_type $caseId
     * @param unknown_type $caseType
     */
    public static function getActivityOptions($token,$caseId,$caseType){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<getActivityOptionsRequest xmlns="http://www.ebay.com/marketplace/resolution/v1/services">
							<caseId>
						    	<id>' . $caseId . '</id>
						    	<type>' . $caseType . '</type>
						  	</caseId>
						</getActivityOptionsRequest>';
    	
    	try{
    		 
    		$session = new eBaySoapSession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(),
    				Common_Company::getEbayCertid(), Common_Company::getEbayEndpoint(), "getActivityOptions");
    		 
    		$responseXml = $session->sendHttpRequest($requestxml);
    		//print_r($responseXml);
    		$responseDoc = new DomDocument();
    		 
    		$responseDoc->loadXML($responseXml);
    		$errors      = $responseDoc->getElementsByTagName('Errors');
    		$data        = XML_unserialize($responseXml);
    		//     		print_r($data);
    		return  $data;
    	}catch(Exception $e){
    		echo $e->getMessage();
    	}
    }
    
    /**
     * 响应case（UPI类型）--取消交易
     * @param unknown_type $token			
     * @param unknown_type $itemId
     * @param unknown_type $transactionID
     * @return Ambigous <NULL, multitype:>
     */
    public static function addDisputeCancelTransactionForUPI($token , $itemId , $transactionID){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<AddDisputeRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    						<RequesterCredentials> 
							 	<eBayAuthToken>'.$token.'</eBayAuthToken>
							</RequesterCredentials>
						  	<DisputeExplanation>BuyerNoLongerWantsItem</DisputeExplanation>
						  	<DisputeReason>TransactionMutuallyCanceled</DisputeReason>
						  	<ItemID>' . $itemId . '</ItemID>
						  	<TransactionID>' . $transactionID . '</TransactionID>
						</AddDisputeRequest>';
    	
    	$session = new eBaySession($token,
    			Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
    			Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
    			Common_Company::getEbayVersion(), '0', 'AddDispute');
    	
    	$responseXml = $session->sendHttpRequest($requestxml);
    	 
    	$responseDoc = new DomDocument();
    	
    	$responseDoc->loadXML($responseXml);
    	
    	$errors      = $responseDoc->getElementsByTagName('Errors');
    	
    	$data        = XML_unserialize($responseXml);
    	
    	return $data;
    }
    
    /**
     * 响应case(EBP开头的类型)--发送其他解决方案
     * @param unknown_type $token
     * @param unknown_type $caseId
     * @param unknown_type $caseType
     * @param unknown_type $msgContent
     * @return Ambigous <NULL, multitype:>
     */
    public static function offerOtherSolution($token,$caseId,$caseType,$msgContent){
    	//<![CDATA[' . $msgContent . ']]> 用来转义消息中的特殊字符
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<offerOtherSolutionRequest xmlns="http://www.ebay.com/marketplace/resolution/v1/services">
							<caseId>
						    	<id>' . $caseId . '</id>
								<type>' . $caseType . '</type>
							</caseId>
							<messageToBuyer><![CDATA[' . $msgContent . ']]></messageToBuyer>
						</offerOtherSolutionRequest>';
    	
    	try{
			
    		$session = new eBaySoapSession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(),
    				Common_Company::getEbayCertid(), Common_Company::getEbayEndpoint(), "offerOtherSolution");
//     		print_r($requestxml);
    		$responseXml = $session->sendHttpRequest($requestxml);
//     		print_r($responseXml);
    		$responseDoc = new DomDocument();
    		 
    		$responseDoc->loadXML($responseXml);
    		$errors      = $responseDoc->getElementsByTagName('Errors');
    		$data        = XML_unserialize($responseXml);
//     		print_r($data);
    		return  $data;
    	}catch(Exception $e){
    		echo $e->getMessage();
    	}
    }
    
    /**
     * 响应case(EBP开头的类型)--发送货运信息
     * @param unknown_type $token
     * @param unknown_type $caseId
     * @param unknown_type $caseType
     * @param unknown_type $carrierUsed		发货承运商的名称
     * @param unknown_type $msgContent		消息内容（选填，Max.length:1000）
     * @param unknown_type $shippedDate		发货时间（美国时间,如：2011-03-18T19:14:46.17Z）
     * @return Ambigous <NULL, multitype:>
     */
    public static function provideShippingInfo($token,$caseId,$caseType,$carrierUsed,$msgContent,$shippedDate){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<provideShippingInfoRequest xmlns="http://www.ebay.com/marketplace/resolution/v1/services">
						  <carrierUsed>' . $carrierUsed . '</carrierUsed>
						  <caseId>
						    <id>' . $caseId . '</id>
						    <type>' . $caseType . '</type>
						  </caseId>
						  <comments><![CDATA[' . $msgContent . ']]></comments>
						  <shippedDate>' . $shippedDate . '</shippedDate>
						</provideShippingInfoRequest>';
    	try{
    		$session = new eBaySoapSession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(),
    				Common_Company::getEbayCertid(), Common_Company::getEbayEndpoint(), "provideShippingInfo");
// 			print_r($requestxml);
    		$responseXml = $session->sendHttpRequest($requestxml);
//     		print_r($responseXml);
    		$responseDoc = new DomDocument();
    		 
    		$responseDoc->loadXML($responseXml);
    		$errors      = $responseDoc->getElementsByTagName('Errors');
    		$data        = XML_unserialize($responseXml);
// 			print_r($data);
    		return  $data;
    	}catch(Exception $e){
    		echo $e->getMessage();
    	}
    }
    /**
     * 响应case(EBP开头的类型)--发送轨迹单号信息
     * @param unknown_type $token
     * @param unknown_type $caseId
     * @param unknown_type $caseType
     * @param unknown_type $carrierUsed		发货承运商的名称
     * @param unknown_type $msgContent		消息内容（选填，Max.length:1000）
     * @param unknown_type $trackingNumber	单号
     * @return Ambigous <NULL, multitype:>
     */
    public static function provideTrackingInfo($token,$caseId,$caseType,$carrierUsed,$msgContent,$trackingNumber){
    	$requestxml = '<?xml version="1.0" encoding="utf-8"?>
						<provideTrackingInfoRequest xmlns="http://www.ebay.com/marketplace/resolution/v1/services">
						  <carrierUsed>' . $carrierUsed . '</carrierUsed>
						  <caseId>
						  	<id>' . $caseId . '</id>
						    <type>' . $caseType . '</type>
						  </caseId>
						  <comments><![CDATA[' . $msgContent . ']]></comments>
						  <trackingNumber>' . $trackingNumber . '</trackingNumber>
						</provideTrackingInfoRequest>';
    	try{
    		$session = new eBaySoapSession($token, Common_Company::getEbayDevid(), Common_Company::getEbayAppid(),
    				Common_Company::getEbayCertid(), Common_Company::getEbayEndpoint(), "provideTrackingInfo");
//     		print_r($requestxml);
    		$responseXml = $session->sendHttpRequest($requestxml);
//     		print_r($responseXml);
    		$responseDoc = new DomDocument();
    		 
    		$responseDoc->loadXML($responseXml);
    		$errors      = $responseDoc->getElementsByTagName('Errors');
    		$data        = XML_unserialize($responseXml);
//     		print_r($data);
    		return  $data;
    	}catch(Exception $e){
    		echo $e->getMessage();
    	}
    }
    /**
     * 响应case(EBP开头的类型)--全额退款
     */
    
    /**
     * 响应case(EBP开头的类型)--部分退款
     */
    
    public static function str_rep($str){
    
    	$str  = str_replace("'","&acute;",$str);
    
    	$str  = str_replace("\"","&quot;",$str);
    
    	return $str;
    
    }

    public static function GetEbayTime($token){
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <GeteBayTimeRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                        </GeteBayTimeRequest>';
    
        $session = new eBaySession('',
                Common_Company::getEbayDevid(),Common_Company::getEbayAppid(),
                Common_Company::getEbayCertid(),Common_Company::getEbayServerurl(),
                '823', '0',
                'GeteBayTime');
        $responseXml = $session->sendHttpRequest($requestxml);
        $data        = XML_unserialize($responseXml);
    
        print_r($data);exit;
    }
    
    public static function GetSingleItem($itemId){
        $requestxml = '<?xml version="1.0" encoding="utf-8"?>
                        <GetSingleItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                        <ItemID>'.$itemId.'</ItemID>
                        </GetSingleItemRequest>';
        $session = new eBayShoppingSession(Common_Company::getEbayShoppingEndpoint(), Common_Company::getEbayAppid(), '0', Common_Company::getEbayVersion(), 'GetSingleItem');
        
        $responseXml = $session->sendHttpRequest($requestxml);
        
        try{
            $data = XML_unserialize($responseXml);
            $headeer = $session->getEbayHeaders();
        }catch(Exception $e){
            echo $e->getMessage() . "ebaylib";
            exit();
        }
        return $data;
    }
}