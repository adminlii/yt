<?php
/**
 * 功能目的:
 * 验证ebay订单是否地址为空,如果为空,将订单号存入table_cron_load_ebay_item_transactions表,系统单独下载该订单
 * @author Administrator
 *
 */
class Ebay_OrderEbayCheckAddress
{

    private $_user_account = '';

    private $_company_code = '';
    
//     private $_order_sn='';
    
//     private $_item_id = '';
    
//     private $_transaction_id = '';
    
 

    public function __construct(){
    	
    }
    public function setUserAccount($userAccount){
        $this->_user_account = $userAccount;
    }

    public function setCompanyCode($companyCode){
        $this->_company_code = $companyCode;
    }
    /**
     * 请求ebay
     *
     * @param unknown_type $userAccount            
     * @param unknown_type $start            
     * @param unknown_type $end            
     * @param unknown_type $orderIds            
     * @throws Exception
     * @return number
     */
    public function callEbay($order_sn,$item_id,$transaction_id='')
    {
        $userAccount = $this->_user_account;
        if(empty($userAccount)){
            throw new Exception(' userAccount Empty');
        }
        $companyCode = $this->_company_code;
        if(empty($companyCode)){
            throw new Exception(' companyCode Empty');
        }
        $token = Ebay_EbayLib::getUserToken($this->_user_account,$companyCode);
        if(! $token){
            throw new Exception($userAccount . ' UserToken Ivalid');
        }
        if(empty($item_id)){
        	throw new Exception(' item_id Empty');
        }
        if($transaction_id===''){
        	throw new Exception(' transaction_id Empty');
        }
        Common_ApiProcess::log("开始下载订单:[{$order_sn}]");

        $config = array(
        		'token' => $token,
        		'devid' => Common_Company::getEbayDevid(),
        		'appid' => Common_Company::getEbayAppid(),
        		'certid' => Common_Company::getEbayCertid(),
        		'serverurl' => Common_Company::getEbayServerurl(),
        		'version' => Common_Company::getEbayVersion(),
        		'siteid' => '0'
        );
        $svc = new Ebay_EbayLibTrading($config);
        $param = array(
        		'RequesterCredentials' => array(
        				'eBayAuthToken' => $config['token']
        		)
        );
        $param['IncludeContainingOrder'] = 'true';
        $param['IncludeFinalValueFee'] = 'true';
        $param['IncludeVariations'] = 'true';
        $param['DetailLevel'] = 'ReturnAll';
        $param['WarningLevel'] = 'Low';
          
        $param['ItemID'] = $item_id;
       
        if($transaction_id){
        	$param['TransactionID'] = $transaction_id;
        }
        $data = $svc->request('GetItemTransactions', $param);        
         
        $GetItemTransactionsResponse = $data['GetItemTransactionsResponse'];
        if($GetItemTransactionsResponse['Ack']=='Success'){
			$ShippingAddress = $GetItemTransactionsResponse ['TransactionArray'] ['Transaction'] ['Buyer'] ['BuyerInfo'] ['ShippingAddress'];
			$add = array (
					'address_id' => $ShippingAddress ['AddressID'],
					'address_owner' => $ShippingAddress ['AddressOwner'],
					'city_name' => $ShippingAddress ['CityName'],
					'country' => $ShippingAddress ['Country'],
					'country_name' => $ShippingAddress ['CountryName'],
					
					'consignee_name' => $ShippingAddress ['Name'],
					'consignee_phone' => $ShippingAddress ['Phone'],
					'consignee_zip' => $ShippingAddress ['PostalCode'],
					'consignee_state' => $ShippingAddress ['StateOrProvince'],
					'consignee_street1' => $ShippingAddress ['Street1'],
					'consignee_street2' => $ShippingAddress ['Street2'] 
			);
			$add = Ec_AutoRun::arrayNullToEmptyString ( $add );
			Service_EbayOrder::update ( $add, $order_sn, 'order_sn' );
		}else{
			Common_ApiProcess::log(print_r($GetItemTransactionsResponse,true));
		}
		return $GetItemTransactionsResponse;
    }
 
}