<?php

class Common_APIChannelDataBatchSet
{
    protected $accountKey = array();
    protected $orderKey = array();
    protected $shipperKey = array();
    protected $orderInvoiceItemKey = array();
    protected $orderItem = array();

    // 渠道ID
    protected $channelId = "";
    protected $serverProductCode = "";
    protected $serviceCode = "";
    protected $orderCode = "";
    protected $orderData = "";
    protected $accountData = array();
    protected $error = '';
    protected $data=array();
    
    public function __construct($serviceCode = '', $code = '', $channelId = '', $serverProductCode = '', $init = true)
    {
    	// API 主信息
        $serviceRow = Service_ApiService::getByField($serviceCode, 'as_code');
        if (empty($serviceRow)) {
            throw new Exception("API服务[{$serviceCode}]未配置");
        }
        
//         Ec::showError("---". print_r($serviceRow, true), 'dataSetByChannel_' . date('Y-m-d'));
        $this->serviceCode = $serviceRow["as_code"];
        $this->accountData = $serviceRow;
        $this->_code = $code;
        $this->channelId = $channelId;
        $this->serverProductCode = $serverProductCode;
        
        // API 授权信息,如TOKEN等
        $api_authorize_file = Service_ApiAuthorizeFile::getByCondition(array("as_id" => $serviceRow['as_id']));
        foreach ($api_authorize_file as $k => $row) {
        	$this->accountData[$row['af_file']] = $row['af_value'];
        }
        
        if("PRODUCTION" == $this->accountData['as_environment']) {
        	$this->accountData['as_url'] = $this->accountData['as_address'];
        } else {
        	$this->accountData['as_url'] = $this->accountData['as_sandbox_address'];
        }
        
        if($init) {
        	$this->_batchInit($code);
        }
    }

    public function setError($msg = '')
    {
        $this->error = $msg;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * @desc 获取系统配置
     * @return array
     */
    public static function getApiConfig()
    {
        // 验证token
    	$apiArray = array();
        $api = new Zend_Config_Ini(APPLICATION_PATH . '/configs/api.ini');
    	$api = $api->toArray();
        $oapi = $api['production']['api']['oapi'];
        if ($oapi) {
             $apiArray['oapi'] = $oapi;
        }
        
        return array(
            'token' => isset($apiArray['oapi']['toKen']) ? $apiArray['oapi']['toKen'] : '',
            'active' => isset($apiArray['oapi']['active']) ? $apiArray['oapi']['active'] : '0',
            'systemCode' => isset($apiArray['oapi']['systemCode']) ? $apiArray['oapi']['systemCode'] : 'ERP',
        );
    }

    

    /**
     * @desc 初始化订单数据
     */
  
    protected function _batchInit($departbatch_labelcode){
    	$db2=Common_Common::getAdapterForDb2();
    	
    		$sql="SELECT
    		d.departbatch_labelcode,
    		e.shipper_hawbcode,
    		db.bag_labelcode,
    		bs.checkout_grossweight,
    		bs.destination_countrycode,
    		sc.consignee_name,
    		sc.consignee_city,
    		sc.consignee_province,
    		sc.consignee_postcode,
    		sc.consignee_telephone,
    		sc.consignee_street
    		 
    		FROM
    		 
    		bsn_expressexport e
    		inner join bsn_business bs ON e.bs_id = bs.bs_id
    		inner join bsn_departbatch_express de ON de.bs_id = bs.bs_id
    		inner join bsn_departbatch_bag db ON de.bag_id = db.bag_id
    		inner join bsn_departurebatch d ON de.departbatch_id = d.departbatch_id
    		inner join bsn_shipperconsignee sc ON bs.bs_id = sc.bs_id
    		WHERE d.departbatch_labelcode = '{$departbatch_labelcode}'";//TODO
    		//         	      AND e.manifest_sign ='N'
    		//         	      LIMIT 0,50;";
    		 
    		
    		$order=$db2->fetchAll($sql);//TODO
    		$this->_synchronousOrder=$order;
    		//print_r($this->_synchronousOrder);
   
    } 
    
    
}