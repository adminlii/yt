<?php

class Common_APIChannelDataSet
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

    public function __construct($serviceCode = '', $orderCode = '', $channelId = '', $serverProductCode = '', $order_config='', $init = true)
    {
    	// API 主信息
        $serviceRow = Service_ApiService::getByField($serviceCode, 'as_code');
        if (empty($serviceRow)) {
            throw new Exception("API服务[{$serviceCode}]未配置");
        }
        
//         Ec::showError("---". print_r($serviceRow, true), 'dataSetByChannel_' . date('Y-m-d'));
        $this->serviceCode = $serviceRow["as_code"];
        $this->accountData = $serviceRow;
        $this->orderCode = $orderCode;
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
        	$this->_init($order_config);
        }else{
        	$this->_initByBsn($order_config);
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
     * 获取发件人地址
     * 先根据产品取地址，如果不存在，根据渠道取
     * @param unknown_type $product_code
     * @param unknown_type $server_channelid
     */
    public function getShipperAddress($product_code = '', $server_channelid = '', $country_code = '') {
    	
    	$address = array();
    	// 根据产品找发件人
    	if(!empty($product_code)) {
    		$condition = array('product_code' => $product_code);
    		$address_rows = Service_PbrPublicShipperAddress::getByCondition($condition);
    		foreach($address_rows as $k => $row) {
    			if(empty($row['country_code'])) {
    				$address = $row;
    				break;
    			}
    			
    			// 如果有国家限制，必须匹配国家
    			if(!empty($row['country_code']) && $row['country_code'] == $country_code) {
    				$address = $row;
    				break;
    			} 
    		}
    	}
    	
    	if(!empty($address) || empty($server_channelid)) {
    		return $address;
    	}
    	
    	// 根据渠道取数据
    	$condition = array('server_channelid' => $server_channelid);
    	$address_rows = Service_PbrPublicShipperAddress::getByCondition($condition);
    	foreach($address_rows as $k => $row) {
    		if(empty($row['country_code'])) {
    			$address = $row;
    			break;
    		}
    		
    		// 如果有国家限制，必须匹配国家
    		if(!empty($row['country_code']) && $row['country_code'] == $country_code) {
    			$address = $row;
    			break;
    		}
    	}
    	
    	return $address;
    }

    /**
     * @desc 初始化订单数据
     */
    protected function _init($order_config='')
    {
    	$this->_getOrderByConfig($order_config);
    	
    	
        $address = Service_CsdShipperconsignee::getByField($this->orderData['order_id'], "order_id", "*");
        // 根据国家代码随机取一个
        //$shipperAddress = $this->getShipperAddress($this->orderData["product_code"], $this->channelId, $this->orderData["country_code"]);
        $shipperAddress = array();//当前没有公共发件人（系统预设的发件人，不是用户预设的发件人）
        //发件人信息
        if (!empty($shipperAddress)){
	        $this->shipperKey["shipperCompanyName"] = $shipperAddress["shipper_company"];
	        $this->shipperKey["shipperName"] = $shipperAddress["shipper_name"];
	        $this->shipperKey["shipperCountryCode"] = $shipperAddress["shipper_countrycode"];
	        $this->shipperKey["shipperStateOrProvince"] = $shipperAddress["shipper_province"];
	        $this->shipperKey["shipperStreet"] = $shipperAddress["shipper_street"];
	        $this->shipperKey["shipperCity"] = $shipperAddress["shipper_city"];
	        $this->shipperKey["shipperPostCode"] = $shipperAddress["shipper_postcode"];
	        $this->shipperKey["shipperPhone"] = empty($shipperAddress["shipper_mobile"]) ? $shipperAddress["shipper_telephone"] : $shipperAddress["shipper_mobile"];
	        $this->shipperKey["shipperHouseNo"] = empty($shipperAddress["shipper_doorplate"]) ? $shipperAddress["shipper_doorplate"] : '';
	        $this->shipperKey["shipperAddress1"] = $shipperAddress["shipper_city"];
	        $this->shipperKey["shipperAddress2"] = $shipperAddress["shipper_province"];
	        $this->shipperKey["shipperEmail"] = $shipperAddress["shipper_email"];
	        $this->shipperKey["shipperDistrict"] = $shipperAddress["shipper_district"];
	        $this->shipperKey["shipperMobile"] = $shipperAddress["shipper_mobile"];
        }else{
        	$this->shipperKey["shipperCompanyName"] = $address["shipper_company"];
        	$this->shipperKey["shipperName"] = $address["shipper_name"];
        	$this->shipperKey["shipperCountryCode"] = $address["shipper_countrycode"];
        	$this->shipperKey["shipperStateOrProvince"] = $address["shipper_province"];
        	$this->shipperKey["shipperStreet"] = $address["shipper_street"];
        	$this->shipperKey["shipperCity"] = $address["shipper_city"];
        	$this->shipperKey["shipperPostCode"] = $address["shipper_postcode"];
        	$this->shipperKey["shipperPhone"] = empty($address["shipper_mobile"]) ? $address["shipper_telephone"] : $address["shipper_mobile"];
        	$this->shipperKey["shipperHouseNo"] = empty($address["shipper_doorplate"]) ? $address["shipper_doorplate"] : '';
        	$this->shipperKey["shipperAddress1"] = $address["shipper_city"];
        	$this->shipperKey["shipperAddress2"] = $address["shipper_province"];
        	$this->shipperKey["shipperEmail"] = $address["shipper_email"];
        	$this->shipperKey["shipperDistrict"] = $address["shipper_district"];
        	$this->shipperKey["shipperMobile"] = $address["shipper_mobile"];
        }
        //收件人信息
        $this->orderKey["consigneeName"] = $address["consignee_name"];
        $this->orderKey["consigneeFirstName"] = $address["consignee_name"];
        $this->orderKey["consigneeLastName"] = "";
        $this->orderKey["consigneeCompanyName"] = $address["consignee_company"];
        $this->orderKey["consigneeStateOrProvince"] = $address["consignee_province"];
        $this->orderKey["consigneeCity"] = $address["consignee_city"];
        $this->orderKey["consigneeStreet"] = $address["consignee_street"];
        $this->orderKey["consigneeStreet1"] = $address["consignee_street2"];
        $this->orderKey["consigneeStreet2"] = $address["consignee_street3"];
        $this->orderKey["consigneePostalCode"] = $address["consignee_postcode"];
        $this->orderKey["consigneePhone"] = empty($address["consignee_mobile"]) ? $address["consignee_telephone"] : $address["consignee_mobile"];
        $this->orderKey["consignee_mobile"] = $address["consignee_mobile"];
        $this->orderKey["consignee_telephone"] = $address["consignee_telephone"];
        $this->orderKey["consigneeEmail"] = $address["consignee_email"];
        $this->orderKey["consigneeCountryCode"] = $address['consignee_countrycode'];
        $this->orderKey["consigneeAddress1"] = $address["consignee_city"];
        $this->orderKey["consigneeAddress2"] = $address["consignee_province"];
        $this->orderKey["consigneeHouseNo"] = $address["consignee_doorplate"];
        $this->orderKey["consigneeDistrict"] = $address["consignee_district"];

        //订单信息
        $this->orderKey["goodsName"] = "";
        $this->orderKey["type"] = $this->orderData['mail_cargo_type'];
        $this->orderKey["quantity"] = $this->orderData['order_pieces'];
        $this->orderKey["productCode"] = $this->orderData['product_code'];;
        $this->orderKey["orderSequence"] = $this->orderData["order_id"]; //订单序列号 order_id
        $this->orderKey["orderNo"] = $this->orderData["shipper_hawbcode"]; //WMS订单号 order_code
        $this->orderKey["referenceID"] = ''; //WMS订单号 order_code

        //服务信息
        $this->orderKey["channelCodeServer"] = $this->serverProductCode; //API服务代码 如:USPS\UPS
        $this->orderKey["channelCode"] = $this->serviceCode;// TODO 确定字段
        $this->orderKey["shippingMethod"] = $this->orderData["product_code"]; //订单运输方式
        
        //渠道对应的服务商服务代码
        //运输方式
        $this->orderKey["serverCodeType"] = $this->serverProductCode; //服务
//         $this->orderKey["pkType"] = '';
//         if ($shipping["sct_id"] > 0) {
//             $pktype = Service_SpServiceChannelPktype::getByField($shipping["sct_id"]);
//             $this->orderKey["pkType"] = $pktype["sct_api_code"]; //包裹类型
//         }
        //一般三方才可能会保险服务
        $this->orderKey["serviceCurrency"] = 'USD'; //服务币种
//         $this->orderKey["UnitOfMeasurement"] = 'LBS'; //服务币种

        //重量单位
        $unit = 'KG';
        $this->orderKey["UnitOfWeight"] = $unit;

        $this->orderKey["validateAddress"] = 'FALSE'; //服务币种
        $this->orderKey["description"] = ''; //说明
        $this->orderKey["shipperExportTax_flag"] = ''; //是否由发件人支付关税
        $this->orderKey["senderAccount"] = ''; //担保账号
        $this->orderKey["payerAccount"] = ''; //付款账号
        $this->orderKey["currencyCode"] = ''; //是否由发件人支付关税
        $this->orderKey["length"] = $this->orderData['length'];
        $this->orderKey["height"] = $this->orderData['height'];
        $this->orderKey["width"] = $this->orderData['width'];
        //保险价值
        $this->orderKey["insurance_value_gj"] = $this->orderData['insurance_value_gj'];
        //系统信息
        $configArr = self::getApiConfig();
        $this->orderKey["system_code"] = $configArr['systemCode'];
        $this->orderKey["language"] = 'zh_CN';


        $this->orderKey["paydate"] = ''; //付款时间
        $this->orderKey["labelFormat"] = 'PDF'; //标签类型 如:png
        $this->orderKey["labelType"] = ''; //非国际件使用:International Default USPS 注意判断是否为国际件

        $this->orderKey["dateAdvance"] = '7'; 
        $this->orderKey["labelSubtype"] = ''; //Integrated None
        $this->orderKey["labelSize"] = '';


        //注意国际件
        $this->orderKey["integratedFormType"] = ''; //Form2976A、Form2976
        $this->orderKey["imageFormat"] = ''; //Form2976A、Form2976
        
        //申报价值
        $declaredValue = 0;
        //发件人增值税/商品服务税号
        $invoice_shippertax = '';
        //收件人增值税/商品服务税号
        $invoice_consigneetax ='';
        //订单产品信息
        $invoice = Service_CsdInvoice::getByCondition(array("order_id" => $this->orderData["order_id"]), "*", 0, 1, "");
		// 总申报数量
		//订单的收件人
        $total_declare_num = 0;
        //var_dump($invoice);
        $this->orderInvoiceItemKey = array();
        foreach ($invoice as $key => $val) {
            if($val['invoice_totalcharge_all']>0&&$declaredValue==0){
               $declaredValue   =$val['invoice_totalcharge_all'];
            }else
            //申报价值
            $declaredValue += $val['invoice_totalcharge'];
            if(empty($invoice_shippertax)){
                $invoice_shippertax = $val['invoice_shippertax'];
            }
            if(empty($invoice_consigneetax)){
                $invoice_consigneetax = $val['invoice_consigneetax'];
            }
            $this->orderInvoiceItemKey[] = array(
                "product_id" => "", //ID
                "description" => $val["invoice_note"], //描述
                "quantity" => $val["invoice_quantity"], //数量
                "weight" => $val["invoice_weight"], //单重
                "value" => round($val["invoice_totalcharge"]/$val["invoice_quantity"],3), //申报价值
                "sku" => $val["sku"], //SKU
                "warehouseSku" => "", //SKU
                "titleCn" => $val["invoice_cnname"], //title
                "titleEn" => $val["invoice_enname"], //
                "refTnx" => "", //Ref Transaction ID
                "refItemId" => "", //Ref Item id
                "currencyCode" => $val["invoice_currencycode"], //买家ID
                "hsCode" => $val["hs_code"], //买家ID
                "url" => $val["invoice_url"], //买家ID
            );
            
            $total_declare_num += $val["invoice_quantity"];
        }
        
        $this->orderKey["declaredValue"] = $declaredValue;
        $this->orderKey["invoice_shippertax"] = $invoice_shippertax;
        $this->orderKey["invoice_consigneetax"] = $invoice_consigneetax;
        //保险
        $con = array(
            'order_id' => $this->orderData['order_id']
        );
        $extservice = Service_CsdExtraservice::getByCondition($con);
        $_extservice = array();
        //是否是文件保险
        //$is_extservice_c4 = false;
        foreach ($extservice as $extsv){
            $_extservice[]=array(
                "servicecode"=>   $extsv['extra_servicecode'],
                "servicevalue"=>   $extsv['extra_servicevalue'],
            );
            if($extsv['extra_servicecode']=='C4'){
                $this->orderKey["insurance_value_gj"]="29.00";
            }
        }
        $this->orderExtservice = $_extservice;
        // 当重量为空时, 默认为0.2kg
        $this->orderKey["weight"] = empty($this->orderData["order_weight"]) ? 0.2 : $this->orderData["order_weight"];
        $this->orderKey["total_declare_num"] = $total_declare_num;
		
        //客户额外信息
        $this->customer_ext = Service_UserExtendYb::getByField($this->orderData["customer_id"]);
    }

    protected function _paramsSet()
    {
        return array(
            'account' => $this->accountKey,
            'order' => $this->orderKey,
            'shipper' => $this->shipperKey,
            'orderInvoiceItem' => $this->orderInvoiceItemKey,
            'orderItem' => $this->orderItem,
        );
    }
    
    protected function _initByBsn($order_config=''){
    	
    	$orderInfo=$this->getOrderInfoByBsn($order_config);
    	
    	//发件人信息
    	//根据国家代码随机取一个
    	$shipperAddress = $this->getShipperAddress($this->orderData["product_code"], $this->channelId, $this->orderData["destination_countrycode"]);
    	if (!empty($shipperAddress)){
	    	$this->shipperKey["shipperCompanyName"] = $shipperAddress["shipper_company"];
	    	$this->shipperKey["shipperName"] = $shipperAddress["shipper_name"];
	    	$this->shipperKey["shipperCountryCode"] = $shipperAddress["country_code"];
	    	$this->shipperKey["shipperStateOrProvince"] = $shipperAddress["shipper_province"];
	    	$this->shipperKey["shipperStreet"] = $shipperAddress["shipper_street"];
	    	$this->shipperKey["shipperCity"] = $shipperAddress["shipper_city"];
	    	$this->shipperKey["shipperPostCode"] = $shipperAddress["shipper_postcode"];
	    	$this->shipperKey["shipperPhone"] = empty($shipperAddress["shipper_mobile"]) ? $shipperAddress["shipper_telephone"] : $shipperAddress["shipper_mobile"];
	    	$this->shipperKey["shipperHouseNo"] = empty($shipperAddress["shipper_doorplate"]) ? $shipperAddress["shipper_doorplate"] : '';
	    	$this->shipperKey["shipperAddress1"] = $shipperAddress["shipper_city"];
	    	$this->shipperKey["shipperAddress2"] = $shipperAddress["shipper_province"];
	    	$this->shipperKey["shipperEmail"] = $shipperAddress["shipper_email"];
	    	$this->shipperKey["shipperDistrict"] = $shipperAddress["shipper_district"];
	    	$this->shipperKey["shipperMobile"] = $shipperAddress["shipper_mobile"];
    	}else{
    		$this->shipperKey["shipperCompanyName"] = $orderInfo["shipper_company"];
    		$this->shipperKey["shipperName"] = $orderInfo["shipper_name"];
    		$this->shipperKey["shipperCountryCode"] = $orderInfo["shipper_countrycode"];
    		$this->shipperKey["shipperStateOrProvince"] = $orderInfo["shipper_province"];
    		$this->shipperKey["shipperStreet"] = $orderInfo["shipper_street"];
    		$this->shipperKey["shipperCity"] = $orderInfo["shipper_city"];
    		$this->shipperKey["shipperPostCode"] = $orderInfo["shipper_postcode"];
    		$this->shipperKey["shipperPhone"] = empty($orderInfo["shipper_mobile"]) ? $orderInfo["shipper_telephone"] : $orderInfo["shipper_mobile"];
    		$this->shipperKey["shipperHouseNo"] = empty($orderInfo["shipper_doorplate"]) ? $orderInfo["shipper_doorplate"] : '';
    		$this->shipperKey["shipperAddress1"] = $orderInfo["shipper_city"];
    		$this->shipperKey["shipperAddress2"] = $orderInfo["shipper_province"];
    		$this->shipperKey["shipperEmail"] = $orderInfo["shipper_email"];
    		$this->shipperKey["shipperDistrict"] = $orderInfo["shipper_district"];
    		$this->shipperKey["shipperMobile"] = $orderInfo["shipper_mobile"];
    	}
    	  
    	
    	//收件人信息
    	$this->orderKey["consigneeName"] = $orderInfo["consignee_name"];
    	$this->orderKey["consigneeFirstName"] = $orderInfo["consignee_name"];
    	$this->orderKey["consigneeLastName"] = "";
    	$this->orderKey["consigneeCompanyName"] = $orderInfo["consignee_company"];
    	$this->orderKey["consigneeStateOrProvince"] = $orderInfo["consignee_province"];
    	$this->orderKey["consigneeCity"] = $orderInfo["consignee_city"];
    	$this->orderKey["consigneeStreet"] = $orderInfo["consignee_street"];
    	$this->orderKey["consigneePostalCode"] = $orderInfo["consignee_postcode"];
    	$this->orderKey["consigneePhone"] = empty($orderInfo["consignee_mobile"]) ? $orderInfo["consignee_telephone"] : $orderInfo["consignee_mobile"];
    	$this->orderKey["consignee_mobile"] = $orderInfo["consignee_mobile"];
    	$this->orderKey["consignee_telephone"] = $orderInfo["consignee_telephone"];
    	$this->orderKey["consigneeEmail"] = $orderInfo["consignee_email"];
    	$this->orderKey["consigneeCountryCode"] = $orderInfo['consignee_countrycode'];
    	$this->orderKey["consigneeAddress1"] = $orderInfo["consignee_street2"];
    	$this->orderKey["consigneeAddress2"] = $orderInfo["consignee_street3"];
    	$this->orderKey["consigneeHouseNo"] = $orderInfo["consignee_doorplate"];
    	$this->orderKey["consigneeDistrict"] = $orderInfo["consignee_district"];
    	
    	//订单信息
    	$this->orderKey["goodsName"] = "";
    	$this->orderKey["type"] = "";
    	$this->orderKey["quantity"] = $this->orderData['shipper_pieces'];
    	$this->orderKey["productCode"] = $this->orderData['product_code'];;
    	$this->orderKey["orderSequence"] = $this->orderData["order_id"]; //订单序列号 order_id  TODO
    	$this->orderKey["orderNo"] = $this->orderData["shipper_hawbcode"]; //WMS订单号 order_code
    	$this->orderKey["referenceID"] = ''; //WMS订单号 order_code
    	
    	//服务信息
    	$this->orderKey["channelCodeServer"] = $this->serverProductCode; //API服务代码 如:USPS\UPS
    	$this->orderKey["channelCode"] = $this->serviceCode;// TODO 确定字段
    	$this->orderKey["shippingMethod"] = $this->orderData["product_code"]; //订单运输方式
    	
    	//渠道对应的服务商服务代码
    	//运输方式
    	$this->orderKey["serverCodeType"] = $this->serverProductCode; //服务
    	//         $this->orderKey["pkType"] = '';
    	//         if ($shipping["sct_id"] > 0) {
    	//             $pktype = Service_SpServiceChannelPktype::getByField($shipping["sct_id"]);
    	//             $this->orderKey["pkType"] = $pktype["sct_api_code"]; //包裹类型
    	//         }
    	//一般三方才可能会保险服务
    	$this->orderKey["serviceCurrency"] = 'USD'; //服务币种
    	//         $this->orderKey["UnitOfMeasurement"] = 'LBS'; //服务币种
    	
    	//重量单位
    	$unit = 'KG';
    	$this->orderKey["UnitOfWeight"] = $unit;
    	
    	$this->orderKey["validateAddress"] = 'FALSE'; //服务币种
    	$this->orderKey["description"] = ''; //说明
    	$this->orderKey["shipperExportTax_flag"] = ''; //是否由发件人支付关税
    	$this->orderKey["senderAccount"] = ''; //担保账号
    	$this->orderKey["payerAccount"] = ''; //付款账号
    	$this->orderKey["currencyCode"] = ''; //是否由发件人支付关税
    	
    	// 当重量为空时, 默认为0.2kg
    	$this->orderKey["weight"] = empty($orderInfo["checkin_grossweight"]) ? 0.2 : $orderInfo["checkin_grossweight"];
    	
    	$this->orderKey["length"] = $orderInfo['involume_length'];
    	$this->orderKey["height"] = $orderInfo['involume_height'];
    	$this->orderKey["width"] = $orderInfo['involume_width'];
    	
    	//系统信息
    	$configArr = self::getApiConfig();
    	$this->orderKey["system_code"] = $configArr['systemCode'];
    	$this->orderKey["language"] = 'zh_CN';
    	
    	
    	$this->orderKey["paydate"] = ''; //付款时间
    	$this->orderKey["labelFormat"] = 'PDF'; //标签类型 如:png
    	$this->orderKey["labelType"] = ''; //非国际件使用:International Default USPS 注意判断是否为国际件
    	
    	$this->orderKey["dateAdvance"] = '7';
    	$this->orderKey["labelSubtype"] = ''; //Integrated None
    	$this->orderKey["labelSize"] = '';
    	
    	
    	//注意国际件
    	$this->orderKey["integratedFormType"] = ''; //Form2976A、Form2976
    	$this->orderKey["imageFormat"] = ''; //Form2976A、Form2976
    	
    	//申报价值
    	$declaredValue = 0;
    	//订单产品信息
    	$invoice = $this->getInvoice($this->orderData['bs_id']);
    	// 总申报数量
    	$total_declare_num = 0;
    	foreach ($invoice as $key => $val) {
    	
    		//申报价值
    		$declaredValue += $val['invoice_totalcharge'];
    	
    		$this->orderInvoiceItemKey[] = array(
    				"product_id" => "", //ID
    				"description" => $val["invoice_note"]?$val["invoice_note"]:'', //描述
    				"quantity" => $val["invoice_quantity"], //数量
    				"weight" => $val["invoice_weight"], //单重
    				"value" => round($val["invoice_totalcharge"]/$val["invoice_quantity"],3), //申报价值
    				"sku" => $val["sku"], //SKU
    				"warehouseSku" => "", //SKU
    				"titleCn" => $val["invoice_cnname"], //title
    				"titleEn" => $val["invoice_enname"], //
    				"refTnx" => "", //Ref Transaction ID
    				"refItemId" => "", //Ref Item id
    				"currencyCode" => $val["invoice_currencycode"], //买家ID
    				"hsCode" => $val["hs_code"], //买家ID
    				"url" => $val["invoice_url"], //买家ID
    		);
    	
    		$total_declare_num += $val["invoice_quantity"];
    	}
    	
    	$this->orderKey["declaredValue"] = $declaredValue;
    	
    	
    	$this->orderKey["total_declare_num"] = $total_declare_num;
    	 
    }
    
    public function getOrderInfoByBsn($order_config=''){
    	
    	$db2=Common_Common::getAdapterForDb2();
    	$sql="SELECT * FROM bsn_shipperconsignee bs
		      INNER JOIN bsn_cargovolume bc ON bc.bs_id=bs.bs_id
		      INNER JOIN bsn_expressexport be ON be.bs_id=bs.bs_id
		      INNER JOIN bsn_business bb ON bb.bs_id=be.bs_id
		      WHERE be.shipper_hawbcode='{$this->orderCode}';";
    	$re=$db2->fetchRow($sql);
    	
    	if (empty($re)) {
    	$this->setError("订单不存在");
    	throw new Exception('订单不存在');
    	}
    	
    	
    	$config=strtoupper($order_config);
    	if ($config=='YD'){
    		$hawbcode='serve_hawbcode';
    	}else{
    		$hawbcode='shipper_hawbcode';
    	}
    	
    	$this->orderData=$re;
    	$this->orderCode = $this->orderData[$hawbcode];
    	return $re;
    		
    }
    
    public function getInvoice($bs_id){
    	$db2=Common_Common::getAdapterForDb2();
    	$sql="SELECT * FROM bsn_invoice WHERE bs_id='{$bs_id}';";
    	$re=$db2->fetchAll($sql);
    	return $re;
    }
    
    
    protected function _getOrderByConfig($order_config=''){
    	
    	$re=Service_CsdOrder::getByField($this->orderCode, 'shipper_hawbcode', "*");
    	if (empty($re)) {
    		$this->setError("订单不存在");
    		throw new Exception('订单不存在');
    	}
    	
    	$config=strtoupper($order_config);
    	if ($config=='YD'){
    		$hawbcode='server_hawbcode';
    	}else{
    		$hawbcode='shipper_hawbcode';
    	}
    	
    	$this->orderData =$re;
    	$this->orderCode = $this->orderData[$hawbcode];
    }
}