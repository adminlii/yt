<?php
class Process_OrderDhl
{

    protected $_order = array();

    protected $_existOrder = null;

    protected $_order_id = 0;

    protected $_shipper = array();

    protected $_consignee = array();

    protected $_invoice = array();

    protected $_extraservice = array();
    
    protected $_my_service = array();

    protected $_err = array();
    
    protected $_apiErr = array();

    protected $_log = array();
    // 审核时的提示信息
    protected $_verify_tip = '';

    protected $_web_require_err = array();
    //
    protected $_existInvoice = null;

    protected $_existShipperConsignee = null;

    protected $_existExtservice = null;

    protected $_create_method = 'single';
    
    protected $_volume = null;

    public function getErrs()
    {
        return $this->_err;
    }

    public function getApiErr()
    {
    	return $this->_apiErr;
    }
    
    public function setCreateMethod($create_method)
    {
        $this->_create_method = $create_method;
    }
    public function setUuid($uuid)
    {
    	$this->_uuid = $uuid;
    }
    
    public function setVolume($volume)
    {
    	$this->_volume = $volume;
    }
    
    public function setOrder($order)
    {   
        $this->_order = $order;
        unset($this->_order['order_id']);
        $this->_order_id = $order['order_id'];
    }

    public function setShipper($shipper)
    {
        $this->_shipper = $shipper;
    }

    public function setConsignee($consignee)
    {
        $this->_consignee = $consignee;
    }

    public function setInvoice($invoice)
    {
        if(!empty($invoice)){
            foreach($invoice as $k => $v){
                empty($v['invoice_unitcharge'])&&$v['invoice_unitcharge']=0;
                $v['invoice_totalcharge'] = floatval($v['invoice_unitcharge']) * floatval($v['invoice_quantity']);
                $v['invoice_totalWeight'] = floatval($v['invoice_weight']) * floatval($v['invoice_quantity']);
                // 为传递币种,默认USD
                $v['invoice_currencycode'] = empty($v['invoice_currencycode']) ? 'USD' : $v['invoice_currencycode'];
                // unset($v['invoice_unitcharge']);
                
                
                $invoice[$k] = $v;
            }
        }
        $this->_invoice = $invoice;
    }
	
    public function setLabel($labelArr)
    {
    	$this->_label = $labelArr;
    }
    
    
    public function setExtraservice($service)
    {	$service = is_null($service)?array():$service;
    	$this->_my_service = $service;
        $serviceArr = array();
        $db = Common_Common::getAdapterForDb2();
        foreach($service as $k => $v){
            $sql = "select * from atd_extraservice_kind where extra_service_kind='{$v}';";
            $svc = $db->fetchRow($sql);
            if($svc){
                $svc['extra_service_kind'] = strtoupper($svc['extra_service_kind']);
                $serviceArr[$svc['extra_service_kind']] = array(
                    'extra_servicecode' => $svc['extra_service_kind'],
                    'extra_servicevalue' => $svc['extra_service_cnname'],
                    'extra_servicenote' => $svc['extra_service_note'],
                    'extra_createdate' => date('Y-m-d H:i:s'),
                    'extra_createrid' => Service_User::getUserId(),
                	//服务分组
                	'extra_service_group'=>$svc['extra_service_group']
                );
            }
        }
        $this->_extraservice = $serviceArr;
    }

  
     protected function _getCountry($country_code)
    {
        $db = Common_Common::getAdapter();
        $sql = "select * from idd_country_upload where country_value='{$country_code}'";
        $country = $db->fetchRow($sql);
        return $country;
    }
    
    
    // 运输方式（销售产品）支持 产品中文名，产品代码，产品英文名称匹配
    protected function _getProduct($product_code)
    {
        $db = Common_Common::getAdapterForDb2();
        $sql = "select * from csi_productkind where 
                product_status='Y' 
                and tms_id='".Service_User::getTmsId()."' 
                and (product_code='{$product_code}' or product_cnname='{$product_code}' or product_enname='{$product_code}');";
        $productKind = $db->fetchAll($sql);
//         print_r($sql);die;
        foreach($productKind as $k => $v){
            $rule = Service_PbrProductrule::getByField($v['product_code'], 'product_code');
            if(! $rule || $rule['web_show_type'] != 'Y'){
                unset($productKind[$k]);
            }
        }
        if($productKind){
            $productKind = array_pop($productKind);
        }
        return $productKind;
    }

    /**
     * 验证
     */
    protected function _validate()
    {
        // 验证客户单号
        if(!empty($this->_order['refer_hawbcode'])){
//         	if(!preg_match('/^[a-zA-Z0-9\-_]+$/', $this->_order['refer_hawbcode'])){
//         		$this->_err[] = Ec::Lang('参考单号不合法,只能包含字母数字中横线下划线') . "[{$this->_order['refer_hawbcode']}]";
//         	}
        	if(strlen($this->_order['refer_hawbcode']) < 1 || strlen($this->_order['refer_hawbcode']) > 50) {
        		$this->_err[] = Ec::Lang('参考单号不合法,字符长度必须大于1或小于等于50') . "[{$this->_order['refer_hawbcode']}]";
        	}
        	
            $con = array(
                'refer_hawbcode' => $this->_order['refer_hawbcode']
            );
//             print_r($con);die;
            
			if(false){
	            $shipper_hawbcode_arr = Service_CsdOrder::getByCondition($con);
	            if($shipper_hawbcode_arr){
	                $shipper_hawbcode_exist = false;
	                foreach($shipper_hawbcode_arr as $v){
	                    if($this->_order_id != $v['order_id']){
	                        $shipper_hawbcode_exist = true;
	                    }
	                }
	                
	                if($shipper_hawbcode_exist){
	                    $this->_err[] = Ec::Lang('参考单号已存在') . "[{$this->_order['refer_hawbcode']}]";
	                    $this->_apiErr[] = "ORDER_REFER_ISEXISTS";
	                }
	                
	            }
			}
        } else {
        	// 读取配置判断客户单号是否可以为空
            $config = Common_Company::getDBConfig();
            $rule_config = $config['REFER_HAWBCODE_IS_NOT_NULL'];
            // 当存在配置并且等于1时，表示单号必填
            if(!empty($rule_config) && $rule_config['config_value'] == '1') {
            	$this->_err[] = Ec::Lang('客户单号不可为空');
            }
        }
        
        // 读取配置判断运单号校验规则
        $config = Common_Company::getDBConfig();
        $rule_config = $config['SHIPPER_HAWBCODE_RULE'];
        
        $rule = "A";
        $prefix = "";
        $separator = "-";
        if(!empty($rule_config) && !empty($rule_config['config_value'])) {
        	$rule_arr = split(":", $rule_config['config_value']);
        	$rule = $rule_arr[0];
        	$prefix = $rule_arr[1];
        	$separator = $rule_arr[2];
        }
        
        if($rule == 'A') {
        	if(empty($this->_order['shipper_hawbcode'])) {
        		// $this->_order['shipper_hawbcode'] = Common_GetNumbers::getCode('CURRENT_ORDER_COUNT', sprintf("%05d", $this->_order['customer_id']), ''); // 订单号
        		$this->_order['shipper_hawbcode'] = Common_GetYuntNumbers::getCode('CURRENT_ORDER_COUNT_'.Service_User::getCustomerCode(), Service_User::getCustomerCode()); // 订单号
        	} else if(!preg_match('/^EMS.{16}$/', $this->_order['shipper_hawbcode'])) {
        		$this->_err[] = Ec::Lang('运单号格式错误');
        	}
        } else {
        	$this->_order['shipper_hawbcode'] = Common_GetNumbers::getCode('CURRENT_ORDER_COUNT_'.Service_User::getCustomerCode(), Service_User::getCustomerCode(), $prefix, $separator); // 订单号
        }
        
        
        // 验证运输方式,
        if($this->_order['product_code'] === ''){
            $this->_err[] = Ec::Lang('运输方式不可为空');
        }else{
            // $product = Service_CsiProductkind::getByField($this->_order['product_code'], 'product_code');
            $product = $this->_getProduct($this->_order['product_code']);
            // echo $sql;
//             print_r($this->_order);
//             print_r($product); exit;
            if(! $product){
                $this->_err[] = Ec::Lang('运输方式不支持', $this->_order['product_code']);
            }else{
                $this->_order['product_code'] = $product['product_code'];
            }
        }
        // 验证国家
        if(empty($this->_order['country_code'])){
            $this->_err[] = Ec::Lang('目的国家不可为空');
        }else{
            // $country = Service_IddCountry::getByField($this->_order['country_code'], 'country_code');            
           	$country = $this->_getCountry($this->_order['country_code']);
            if(! $country){
                $this->_err[] = Ec::Lang('国家不存在', $this->_order['country_code']);
            }else{
                $this->_order['country_code'] = $country['country_code'];
            }
        }
        //验证运输方式是否到达目的国家
        if($this->_order['product_code']&&$this->_order['country_code']){
            //
            $product_code = $this->_order['product_code'];
            $country_code = $this->_order['country_code'];
            $countryArr = Process_ProductRule::arrivalCountry($product_code);
            //print_r($countryArr);exit;
            if(!isset($countryArr[$country_code])){
                $this->_err[] = Ec::Lang('运输方式%1不到达该国家%2',array($product_code,$country_code));
            }
        }
        // 客户单号(验证)
        
        // 验证发件人
        if(empty($this->_shipper)){
            $this->_err[] = Ec::Lang('发件人信息不可为空');
        }else{
            if($this->_shipper['shipper_countrycode'] === ''){
                 $this->_err[] = Ec::Lang('发件人国家不可为空');
            }else{
                 //$country = Service_IddCountry::getByField($this->_shipper['shipper_countrycode'], 'country_code');
                $country = $this->_getCountry($this->_shipper['shipper_countrycode']);
                if(! $country){
                    $this->_err[] = Ec::Lang('发件人国家不存在', $this->_shipper['shipper_countrycode']);
                }else{
                    $this->_shipper['shipper_countrycode'] = $country['country_code'];
                }
            }
         	if(empty($this->_shipper['shipper_name'])){
                 $this->_err[] = Ec::Lang('发件人姓名不可为空');
            }else if(!preg_match('/^[a-zA-Z\s\.&,]{1,35}$/',$this->_shipper['shipper_name'])){
            		$this->_err[] = "发件人姓名不可为非英文，长度最多35字符";
            }
            if(!empty($this->_shipper['shipper_city'])){
            	if(!preg_match('/^[a-zA-Z\s]{1,35}$/',$this->_shipper['shipper_city'])){
            		$this->_err[] = "发件人城市不可为非英文，长度最多35字符";
            	}
            }else{
            	$this->_err[] = Ec::Lang('发件人城市不可为空');
            }
            if(!$this->_shipper['shipper_street']){
                 $this->_err[] = Ec::Lang('发件人地址不可为空');
            }
            if(!$this->_shipper['shipper_company']){
            	$this->_err[] = Ec::Lang('发件人公司不可为空');
            }else if(!preg_match('/^[a-zA-Z\d\s\.&,]{1,35}$/',$this->_shipper['shipper_company'])){
            		$this->_err[] = "发件人公司不可为非英文，长度最多35字符";
            }
            if(!$this->_shipper['shipper_telephone']){
            	$this->_err[] = Ec::Lang('发件人电话不可为空');
            }else if(!preg_match('/^[0-9]{4,25}$/',$this->_shipper['shipper_telephone'])){
            		$this->_err[] = "发件人电话应4到25位数字";
            }
            if(empty($this->_shipper['shipper_postcode'])){
            	$this->_err[] = Ec::Lang('发件人邮编不可为空');
            }
        }
        
        // 验证收件人
        if(empty($this->_consignee)){
            $this->_err[] = Ec::Lang('收件人信息不可为空');
        }else{
            // 收件人必填项
            if(empty($this->_consignee['consignee_countrycode'])){
                 $this->_err[] = Ec::Lang('收件人国家不可为空');
            }else{
                
                //$country = Service_IddCountry::getByField($this->_consignee['consignee_countrycode'], 'country_code');
                $country = $this->_getCountry($this->_consignee['consignee_countrycode']);
                if(! $country){
                    $this->_err[] = Ec::Lang('收件人国家不存在', $this->_consignee['consignee_countrycode']);
                }else{
                    $this->_consignee['consignee_countrycode'] = $country['country_code'];
                }
            }
            if(empty($this->_consignee['consignee_name'])){
                $this->_err[] = Ec::Lang('收件人姓名不可为空');
            }else{
                if(!preg_match('/^[a-zA-Z\s\.&,]{1,35}$/', $this->_consignee['consignee_name'])){
            		$this->_err[] = Ec::Lang('收件人姓名不允许出现非英文，长度最多35字符');
            	}
            }
            if($this->_consignee['consignee_street'] === ''){
                $this->_err[] = Ec::Lang('收件人地址不可为空');
            }else{
               if(!preg_match('/^[\w\W]{0,35}$/', $this->_consignee['consignee_street'])){
            		$this->_err[] = Ec::Lang('收件人地址长度最多35字符');
               }
            }
            
            
            if($this->_consignee['consignee_street2'] === ''){
            	//$this->_err[] = Ec::Lang('收件人地址不可为空');
            }else{
            	if(!preg_match('/^[\w\W]{0,35}$/', $this->_consignee['consignee_street2'])){
            		$this->_err[] = Ec::Lang('收件人地址2长度最多35字符');
            	}
            }
            
        	if($this->_consignee['consignee_street3'] === ''){
            	//$this->_err[] = Ec::Lang('收件人地址不可为空');
            }else{
            	if(!preg_match('/^[\w\W]{0,35}$/', $this->_consignee['consignee_street3'])){
            		$this->_err[] = Ec::Lang('收件人地址3长度最多35字符');
            	}
            }
            
            
            if (empty($this->_consignee['consignee_city'])){
            	$this->_err[] = Ec::Lang('收件人城市不可为空');
            }else{
               if(!preg_match('/^[a-zA-Z\s]{1,35}$/', $this->_consignee['consignee_city'])){
            		$this->_err[] = Ec::Lang('收件人城市不允许出现非英文，长度最多35字符');
            	}
            }
            if(empty($this->_consignee['consignee_postcode'])){
            	$this->_err[] = Ec::Lang('收件人邮编不可为空');
            }
            if(empty($this->_consignee['consignee_company'])){
            	$this->_err[] = Ec::Lang('收件人公司不可为空');
            }else if(!preg_match('/^[a-zA-Z\d\s\.&,]{1,35}$/',$this->_consignee['consignee_company'])){
            		$this->_err[] = "收件人公司不可为非英文，长度最多35字符";
            }
        }
        
        // 验证必填项
        if($this->_consignee['consignee_telephone']){
        	if(!preg_match("/^(\d){4,25}$/",$this->_consignee['consignee_telephone'])){
        		$this->_err[] = Ec::Lang('收件人电话格式为4-25位纯数字');
        	}
        }else{
        	$this->_err[] = Ec::Lang('收件人电话不能为空');
        }
        
        
        
        if(!empty($this->_order['order_weight'])){
            if(! is_numeric($this->_order['order_weight'])){
                $this->_err[] = Ec::Lang('货物重量必须为数字');
            }
        }else{
           $this->_err[] = Ec::Lang('货物重量不能为空');
        }
        
        //TNT需要验证外包装的最大长宽高
        if($this->_order['order_length']){
            if(! is_numeric($this->_order['order_length'])){
                $this->_err[] = Ec::Lang('包装长度必须为数字');
            }
        }
        
        if($this->_order['order_width']){
            if(! is_numeric($this->_order['order_width'])){
                $this->_err[] = Ec::Lang('包装宽度必须为数字');
            }
        }
        
        
        if($this->_order['order_height']){
            if(! is_numeric($this->_order['order_height'])){
                $this->_err[] = Ec::Lang('包装高度必须为数字');
            }
        }
        
        if($this->_order['mail_cargo_type'] !== '') {
			// TODO DBW
        	$sql = "select * from atd_mail_cargo_type where mail_cargo_code='{$this->_order['mail_cargo_type']}' or mail_cargo_cnname='{$this->_order['mail_cargo_type']}' or mail_cargo_enname='{$this->_order['mail_cargo_type']}'";
            $db = Common_Common::getAdapterForDb2();
            $mail_cargo_type = $db->fetchRow($sql);
            if(!$mail_cargo_type){
                $this->_err[] = Ec::Lang('包裹申报种类不合法');
            }else{
                $this->_consignee['mail_cargo_type'] = $mail_cargo_type['mail_cargo_code'];
            }
        }else{
            $this->_consignee['mail_cargo_type'] = '4';
        }
        
        if($this->_order['order_pieces'] !== ''){
            if(! preg_match('/^[0-9]+$/', $this->_order['order_pieces']) || intval($this->_order['order_pieces']) <= 0){
                $this->_err[] = Ec::Lang('外包装件数必须为大于0的整数');
            }
        }else{
            $this->_order['order_pieces'] = '1';
        }
        // echo $this->_consignee['consignee_certificatecode'];exit;

        //证件类型验证   
        
        if(!empty($this->_consignee['consignee_certificatetype'])){
            $sql = "select * from atd_certificate_type where certificate_type='{$this->_consignee['consignee_certificatetype']}' or certificate_type_cnname='{$this->_consignee['consignee_certificatetype']}' or certificate_type_enname='{$this->_consignee['consignee_certificatetype']}'";

            $db = Common_Common::getAdapterForDb2();
            $atd_certificate_type = $db->fetchRow($sql);
            if(!$atd_certificate_type){
                $this->_err[] = Ec::Lang('证件类型不合法');
            }else{
                $this->_consignee['consignee_certificatetype'] = $atd_certificate_type['certificate_type'];
            }
        }else{
            $this->_consignee['consignee_certificatetype'] = '';
        }
        //证件号码验证
        if(!empty($this->_consignee['consignee_certificatecode'])){
            if(! preg_match('/^[0-9A-Za-z]+$/', $this->_consignee['consignee_certificatecode'])){
                $this->_err[] = Ec::Lang('证件号码只能包含数字和字母');
            }
        }else{
            $this->_consignee['consignee_certificatecode'] = '';
        }
       	$_totalpice=0;
        // 验证申报信息
        if(empty($this->_invoice)&&$this->_order['mail_cargo_type']!=3){
            $this->_err[] = Ec::Lang('申报信息不可为空');
        }else{
            if($this->_create_method == 'single'){
                // sort($this->_invoice);
            }
            // print_r($this->_invoice);exit;
            foreach($this->_invoice as $k => $invoice){ //Ec::showError("result:".print_r($this->_invoice,true)."\n", '_Ssssss_' . date('Y-m-d') . "_");
                if(empty($invoice['invoice_enname'])){
                    $this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('申报品名不可为空');
                }
                if(empty($invoice['invoice_cnname'])){
                    $this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('中文申报品名不可为空');
                }
                if(empty($invoice['invoice_quantity'])){
                    $this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('申报数量不可为空');
                }else{
                    if(! preg_match('/^[0-9]+$/', $invoice['invoice_quantity']) || intval($invoice['invoice_quantity']) <= 0){
                        $this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('申报数量必须为大于0的整数');
                    }else{
                    	$_totalpice += $invoice['invoice_quantity'];
                    }
                }
                if($invoice['invoice_unitcharge']!=0&&empty($invoice['invoice_unitcharge'])){
                    $this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('申报单价不可为空');
                }else{
//                     print_r($invoice);exit;
                    if(! is_numeric($invoice['invoice_unitcharge'])){
                        $this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('申报单价必须为数字');
                    }
                }
                if(empty($invoice['invoice_weight'])){
                	$this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('申报重量不可为空');
                }else{
                	//                     print_r($invoice);exit;
                	if(! is_numeric($invoice['invoice_weight'])||$invoice['invoice_weight']<=0){
                		$this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('申报重量必须为大于0数字');
                	}
                }
            }
        }

        //附加服务验证
        $product_code = $this->_order['product_code'];
        $country_code = $this->_order['country_code'];
        $serve_kind_arr = Process_ProductRule::optionalServeType($product_code, $country_code);
        //print_r($serve_kind_arr);exit;
        $serve_kind_code_arr = array_keys($serve_kind_arr);
        
        foreach($this->_my_service as $k => $svc_code){
        	if(!in_array($svc_code, $serve_kind_code_arr)){
        		$this->_err[] = Ec::Lang('运输方式与国家不支持该附加服务')."[{$svc_code}]".$serve_kind_arr[$svc_code]['extra_service_cnname'] ;
        	}
        }
        $extraservice_group_key = array();

        foreach($this->_extraservice as $row){
            $svc = array(
                'extra_servicecode' => $row['extra_servicecode'],
                'extra_servicevalue' => $row['extra_servicevalue'],
                'extra_servicenote' => $row['extra_servicenote'],
                'extra_createdate' => $row['extra_createdate'],
                'extra_createrid' => $row['extra_createrid'],
            );
           
            // 同一组别附加只能选择一项
            if($row['extra_service_group'] != '' && isset($extraservice_group_key[$row['extra_service_group']])) {
            	$extraservice_kind = Service_AtdExtraserviceKind::getByField($row['extra_service_group']);
            	$this->_err[] = $extraservice_kind['extra_service_cnname'] . Ec::Lang('只能选一个');
            }
           
            // 当为保险时，必须填保险金额
            
			if ($row['extra_service_group'] == 'C0'){
				switch ($row['extra_servicecode']){//根据保险类型取对应的保险金额
					case 'C1':
						$value = 6;
						break;
					case 'C2':
						$value = $this->_order['insurance_value'];
						break;
					case 'C3':
						$value = 8;
						break;
					case 'C4':
						$value = 29;
						break;
					case 'C5':
						$value = 3;
						break;
					case 'C6':
						$value = 12;
						break;
					default:
						$value = '';		 
				}
				$this->_order['insurance_value'] = $value;
			}
            
            
            //投保金额
            if($row['extra_service_group'] == 'C0' && empty($this->_order['insurance_value'])) {
            	$this->_err[] = Ec::Lang('购买保险时, 投保金额必填！');
            }
            
            // 保存分组KEY
            $extraservice_group_key[$row['extra_service_group']] = $row['extra_service_group'];
        }
        
//         print_r($this->_invoice);die;
        //体积验证
        
        
        
        
        
        //地址校验
        $validate = new Common_AddressCheck_Validate();
        $validate->setCtCode($country_code);
        $validate->setPkCode($product_code);
        $validate->setConsignee($this->_consignee);
        $validate->setInvoice($this->_invoice);
        $validate->setVolume($this->_volume);
        
        $addressCheckRs = $validate->validate();
         
        if(!$addressCheckRs['ask']){
        	$err = $addressCheckRs['err'];
        	 $this->_err[] = $addressCheckRs['message']."<br/>&nbsp;&nbsp;".implode(";<br/>&nbsp;&nbsp;", $err);
        } else {
        	$this->_consignee = $addressCheckRs['consignee'];
        	$this->_volume=$addressCheckRs['volume'];
        }
        
        //验证发票制作
        if($this->_order['invoice_print']){
        	if(empty($this->_label)){
        		$this->_err[]="商品信息必须填写";
        	}
        	//总件数
        	$totalpice 	= 0;
        	$totalvalue = 0;
        	foreach ($this->_label as $labelk => $label ){
        		if(!$label['invoice_note'])
        			$this->_err[] = "(" . Ec::Lang('发票信息') . $labelk . ")" . Ec::Lang('完整描述不可为空');
        		if(!$label['invoice_quantity'])
        			$this->_err[] = "(" . Ec::Lang('发票信息') . $labelk . ")" . Ec::Lang('数量不可为空');
        		else{
        			if(! is_numeric($label['invoice_quantity'])||$label['invoice_quantity']<=0){
        				$this->_err[] = $this->_err[] = "(" . Ec::Lang('发票信息') . $labelk . ")" . Ec::Lang('数量必须为大于0数字');
        			}else{
        				$totalpice +=$label['invoice_quantity'];
        			}
        		}
        		if(!$label['invoice_unitcharge']){
        			$this->_err[] = "(" . Ec::Lang('发票信息') . $labelk . ")" . Ec::Lang('单价不可为空');
        		}else{
	    			//                     print_r($invoice);exit;
	    			if(! is_numeric($label['invoice_unitcharge'])||$label['invoice_unitcharge']<=0){
	    				$this->_err[] = $this->_err[] = "(" . Ec::Lang('发票信息') . $labelk . ")" . Ec::Lang('单价必须为大于0数字');
	    			}else{
	    				if($label['invoice_quantity']){
	    					$totalvalue += $label['invoice_quantity']*$label['invoice_unitcharge'];
	    				}
	    			}
	    		}
        		
        	}
        	//校验发票信息正确
        	if(!empty($this->_invoice[1]['invoice_totalcharge_all'])){
        		//校验总价值
        		if($totalvalue!=$this->_invoice[1]['invoice_totalcharge_all']){
        			$this->_err[] = "(" . Ec::Lang('发票信息') .")" . Ec::Lang('总价值和申报价值不一致');
        		}
        	}
        	//校验件数
        	if($totalpice!=$_totalpice){
        		//$this->_err[] =  "(" . Ec::Lang('发票信息') .")" . Ec::Lang('总件数不一致');
        	}
        }
        
	}

    private function _validateElements()
    {
        $web_elements = Process_ProductRule::webRequiredObj($this->_order['product_code'], $this->_order['country_code']);
        $order = array(
            'order_create_code' => strtoupper($this->_order['order_create_code']),
            'customer_id' => $this->_order['customer_id'],
            // 'customer_channelid' => $this->_order['customer_channelid'],
            'product_code' => $this->_order['product_code'],
            'refer_hawbcode' => $this->_order['refer_hawbcode'],
            'shipper_hawbcode' => $this->_order['shipper_hawbcode'],
            'server_hawbcode' => $this->_order['server_hawbcode'],
            'channel_hawbcode' => $this->_order['channel_hawbcode'],
            'country_code' => $this->_order['country_code'],
            'order_pieces' => $this->_order['order_pieces'],
            'order_weight' => $this->_order['order_weight'],
            // 'order_status' => $this->_order['order_status'],
            'mail_cargo_type' => strtoupper($this->_order['mail_cargo_type']),
            // 'document_change_sign' => $this->_order['document_change_sign'],
            // 'oda_checksign' => $this->_order['oda_checksign'],
            // 'oda_sign' => $this->_order['oda_sign'],
            'return_sign' => $this->_order['return_sign'],
            // 'hold_sign' => $this->_order['hold_sign'],
            'buyer_id' => $this->_order['buyer_id'],
            'platform_id' => $this->_order['platform_id'],
            // 'bs_id' => $this->_order['bs_id'],
            'creater_id' => $this->_order['creater_id'],
            // 'create_date' => $this->_order['create_date'],
            'modify_date' => date('Y-m-d H:i:s'),
            // 'print_date' => $this->_order['print_date'],
            // 'post_date' => $this->_order['post_date'],
            // 'checkin_date' => $this->_order['checkin_date'],
            // 'checkout_date' => $this->_order['checkout_date'],
            'tms_id' => $this->_order['tms_id'],
        	
        );
       
        foreach($order as $k => $v){
            if(isset($web_elements[$k]) && $v === ''){
                $this->_err[] = $web_elements[$k]['web_element_name'] . "({$web_elements[$k]['web_element_ename']})" . Ec::Lang('不可为空');
            }
        }
        
        if($this->_create_method == 'single'){
            // sort($this->_invoice);
        }
        foreach($this->_invoice as $o => $row){
            // print_r($row);
            $ivs = array(
                'invoice_enname' => $row['invoice_enname'],
                'unit_code' => empty($row['unit_code']) ? 'PCE' : $row['unit_code'],
                'invoice_quantity' => $row['invoice_quantity'],
                'invoice_totalcharge' => $row['invoice_totalcharge'],
                //新增
                'invoice_weight'=>$row['invoice_weight'],
            	'invoice_totalWeight'=>$row['invoice_totalWeight'],
            	//新增结束
            	'invoice_currencycode' => $row['invoice_currencycode'],
                'hs_code' => $row['hs_code'],
                'invoice_note' => $row['invoice_note'],
                'invoice_url' => $row['invoice_url']
            );
            
            foreach($ivs as $k => $v){
                if(isset($web_elements[$k]) && $v === ''){
                    $this->_err[] = $web_elements[$k]['web_element_name'] . "({$web_elements[$k]['web_element_ename']})" . " [{$o}] " . Ec::Lang('不可为空');
                }
            }
        }
        
        $volume=array(
        	'length'=>$this->_volume['length'],
        	'width'=>$this->_volume['width'],
        	'height'=>$this->_volume['height'],		
        );
        foreach ($volume as $k =>$v){
        	if (isset($web_elements[$k]) &&  $v ==''){
        		$this->_err[] = $web_elements[$k]['web_element_name'] . "({$web_elements[$k]['web_element_ename']})" . Ec::Lang('不可为空');
        	}
        }
      
        
        $shipper = array(
            'shipper_account' => $this->_shipper['shipper_account'],
            'shipper_name' => $this->_shipper['shipper_name'],
            'shipper_company' => $this->_shipper['shipper_company'],
            'shipper_countrycode' => $this->_shipper['shipper_countrycode'],
            'shipper_province' => $this->_shipper['shipper_province'],
            'shipper_city' => $this->_shipper['shipper_city'],
            'shipper_street' => $this->_shipper['shipper_street'],
            'shipper_postcode' => $this->_shipper['shipper_postcode'],
            'shipper_areacode' => $this->_shipper['shipper_areacode'],
            'shipper_telephone' => $this->_shipper['shipper_telephone'],
            'shipper_mobile' => $this->_shipper['shipper_mobile'],
            'shipper_email' => $this->_shipper['shipper_email'],
            'shipper_certificatecode' => $this->_shipper['shipper_certificatecode'],
            'shipper_certificatetype' => $this->_shipper['shipper_certificatetype'],
            'shipper_fax' => $this->_shipper['shipper_fax'],
            'shipper_mallaccount' => $this->_shipper['shipper_mallaccount']
        );
        
        foreach($shipper as $k => $v){
            if(isset($web_elements[$k]) && $v === ''){
                $this->_err[] = $web_elements[$k]['web_element_name'] . "({$web_elements[$k]['web_element_ename']})" . Ec::Lang('不可为空');
            }
        }
        $consignee = array(
            'consignee_name' => $this->_consignee['consignee_name'],
            //'consignee_company' => $this->_consignee['consignee_company'],
            'consignee_countrycode' => $this->_consignee['consignee_countrycode'],
            //'consignee_province' => $this->_consignee['consignee_province'],
            'consignee_city' => $this->_consignee['consignee_city'],
            'consignee_street' => $this->_consignee['consignee_street'],
            'consignee_postcode' => $this->_consignee['consignee_postcode'],
            'consignee_areacode' => $this->_consignee['consignee_areacode'],
            //'consignee_telephone' => $this->_consignee['consignee_telephone'],
            //'consignee_mobile' => $this->_consignee['consignee_mobile'],
            'consignee_fax' => $this->_consignee['consignee_fax'],
            'consignee_email' => $this->_consignee['consignee_email'],
            'consignee_certificatecode' => $this->_consignee['consignee_certificatecode'],
            'consignee_mallaccount' => $this->_consignee['consignee_mallaccount'],
            'consignee_credentials_period' => $this->_consignee['consignee_credentials_period'],
            'consignee_certificatetype' => $this->_consignee['consignee_certificatetype']
        );
        
        foreach($consignee as $k => $v){
            if(isset($web_elements[$k]) && $v === ''){
                $this->_err[] = $web_elements[$k]['web_element_name'] . "({$web_elements[$k]['web_element_ename']})" . Ec::Lang('不可为空');
            }
        }
        foreach($this->_extraservice as $row){
            $svc = array(
                'extra_servicecode' => $row['extra_servicecode'],
                'extra_servicevalue' => $row['extra_servicevalue'],
                'extra_servicenote' => $row['extra_servicenote'],
                'extra_createdate' => $row['extra_createdate'],
                'extra_createrid' => $row['extra_createrid'],
            );
            foreach($svc as $k => $v){
                if(isset($web_elements[$k]) && $v === ''){
                    $this->_err[] = $web_elements[$k]['web_element_name'] . "({$web_elements[$k]['web_element_ename']})" . "[{$svc['extra_servicevalue']}]" . Ec::Lang('不可为空');
                }
            }
        }
    }

    protected function _getOrderChangeLog()
    {
        // 历史数据 start
        
        // 历史数据 end
    }

    public function createOrderTransaction($status)
    {
        $return = array(
            'ask' => 0,
            'message' => Ec::Lang('订单操作失败')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $log = array();
        try{
            $status = strtoupper($status);
            $statusArr = array(
                // 草稿
                'D',
                // 预报
                'P'
            );
            if(! in_array($status, $statusArr)){
                throw new Exception(Ec::Lang('订单状态不合法'));
            }
            $this->createOrder($status);
            
            $successTip = Ec::Lang('订单保存草稿成功');
            if($status == 'P'){
                $successTip = Ec::Lang('订单提交预报成功');
            }
            $db->commit();
            // 提交预报
            if($status == 'P'){
            	// 订单处理
            	$this->_verifyRs = $this->_verifyProcess($this->_order_id, 'verify');
            }
            //日志记录start
            $logrow = array();
            $logrow['requestid'] = $this->_uuid;
            $logrow['type'] = 1;
            $logrow['detail'] = '同步创建订单结束';
            list($usec, $sec) = explode(" ", microtime());
            $logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
            $db = Common_Common::getAdapter();
            $db ->insert('logapi', $logrow);
            //日志记录end
            $this->_order['order_id'] = $this->_order_id;
            $return['ask'] = 1;
            if($this->_existOrder){
                $return['message'] = Ec::Lang('订单更新成功');
            }else{}
            $return['message'] = $successTip;
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = "服务异常：" . trim($e->getMessage());
            Ec::showError($e->getMessage(), 'Order_Create');
//             array_unshift($this->_err, $e->getMessage());
        }
        $return['err'] = $this->_err;
        $return['order_id'] = $this->_order_id;
        $return['order'] = $this->_order;
        return $return;
    }
	
    public function createOrderTransactionapi($status)
    {
    	$return = array(
    			'ask' => 0,
    			'message' => Ec::Lang('订单操作失败')
    	);
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	$log = array();
    	try{
    		$status = strtoupper($status);
    		$statusArr = array(
    				// 草稿
    				'D',
    				// 预报
    				'P'
    		);
    		if(! in_array($status, $statusArr)){
    			throw new Exception(Ec::Lang('订单状态不合法'));
    		}
    		$this->createOrder($status,1);
    
    		$successTip = Ec::Lang('订单保存草稿成功');
    		if($status == 'P'){
    			$successTip = Ec::Lang('订单提交预报成功');
    		}
    		$db->commit();
    		$this->_order['order_id'] = $this->_order_id;
    		$return['ask'] = 1;
    		if($this->_existOrder){
    			$return['message'] = Ec::Lang('订单更新成功');
    		}else{}
    		$return['message'] = $successTip;
    	}catch(Exception $e){
    		$db->rollback();
    		$return['message'] = "服务异常：" . trim($e->getMessage());
    		Ec::showError($e->getMessage(), 'Order_Create');
    		//             array_unshift($this->_err, $e->getMessage());
    	}
    	$return['err'] = $this->_err;
    	$return['order_id'] = $this->_order_id;
    	$return['order'] = $this->_order;
    	return $return;
    }
    
    public function createOrder($status,$import=FALSE)
    {
        $status = strtoupper($status);
        
        // 必填项验证
        $this->_validateElements();
        
        if(! empty($this->_err)){
            throw new Exception(Ec::Lang('订单验证失败'));
        }
        
        $this->_validate();
        if(! empty($this->_err)){
            throw new Exception(Ec::Lang('订单验证失败'));
        }
       
        //验证地址异步处理的时候验证
        if(!$import&&!empty($this->_shipper['shipper_city'])){
        	$positionename = $this->_shipper['shipper_city'];
        	$_positionename = strpos($positionename,",");
        	if($_positionename!==false){
        		$positionename=substr($positionename,0,$_positionename);
        	}
        	$positionename = preg_replace('/\s/','',$positionename);
        	//在本地的对照库中找到地址，然后取出市 和 省
        	$graphicalcondition['positionpname'] = strtoupper($positionename);
        	$res = Service_CsiGeographical::getByCondition($graphicalcondition);
        	if(empty($res))
        		throw new Exception(Ec::Lang('请核实是否是中国地区的城市拼音，该地区无法通过中邮收寄接口'));
        }
        
        $statusArr = array(
            // 草稿
            'D',
            // 预报
            'P'
        );
        if(! in_array($status, $statusArr)){
            throw new Exception(Ec::Lang('订单状态不合法'));
        }
//         echo "<3>";die;
        $order = array(
            'order_create_code' => strtoupper($this->_order['order_create_code']),
            'customer_id' => $this->_order['customer_id'],
            // 'customer_channelid' => $this->_order['customer_channelid'],
            'product_code' => $this->_order['product_code'],
            'refer_hawbcode' => $this->_order['refer_hawbcode'],
            'shipper_hawbcode' => $this->_order['shipper_hawbcode'],
            'server_hawbcode' => $this->_order['server_hawbcode'],
            'channel_hawbcode' => $this->_order['channel_hawbcode'],
            'country_code' => $this->_order['country_code'],
            'order_pieces' => $this->_order['order_pieces'],
            'order_weight' => $this->_order['order_weight'],
            // 'order_status' => $this->_order['order_status'],
            'mail_cargo_type' => strtoupper($this->_order['mail_cargo_type']),
            // 'document_change_sign' => $this->_order['document_change_sign'],
            // 'oda_checksign' => $this->_order['oda_checksign'],
            // 'oda_sign' => $this->_order['oda_sign'],
            'return_sign' => $this->_order['return_sign'],
            // 'hold_sign' => $this->_order['hold_sign'],
            'buyer_id' => $this->_order['buyer_id'],
            'platform_id' => $this->_order['platform_id'],
            // 'bs_id' => $this->_order['bs_id'],
            'creater_id' => $this->_order['creater_id'],
            // 'create_date' => $this->_order['create_date'],
            'modify_date' => date('Y-m-d H:i:s'),
            // 'print_date' => $this->_order['print_date'],
            // 'post_date' => $this->_order['post_date'],
            // 'checkin_date' => $this->_order['checkin_date'],
            // 'checkout_date' => $this->_order['checkout_date'],
            'tms_id' => $this->_order['tms_id'],
            /*'length'=>$this->_volume['length']?$this->_volume['length']:0,
            'width'=>$this->_volume['width']?$this->_volume['width']:0,
            'height'=>$this->_volume['height']?$this->_volume['height']:0,*/
            'length'=>$this->_order['order_length']?$this->_order['order_length']:10,
            'width'=>$this->_order['order_width']?$this->_order['order_width']:10,
            'height'=>$this->_order['order_height']?$this->_order['order_height']:10,
            'insurance_value_gj'=>$this->_order['insurance_value_gj'],
            'dangerousgoods'=>$this->_order['dangerousgoods'],
            //'customer_channelid'=>$this->_order['customer_channelid']?$this->_order['customer_channelid']:Service_User::getChannelid(),
        	'untread'=>$this->_order['untread'],
        	'invoice_type'=>$this->_order['invoice_type'],
        );
        
        $order['order_weight'] =  empty($order['order_weight'])?0:$order['order_weight'];
       
        $customer_channelid = $this->_order['customer_channelid']?$this->_order['customer_channelid']:Service_User::getChannelid();
        if($customer_channelid){
        	$order['customer_channelid'] = $customer_channelid;
        }
        foreach($this->_extraservice as $row){
            if(strtoupper($row['extra_servicecode'])=='T1'){
                $order['return_sign'] = 'Y';//T1	海外退件需退回	海外退件需退回		海外退件需退回	Y            
            }
        }
        // 全部转大写
        foreach($order as $k => $v){
            $order[$k] = strtoupper($v);
        }
        unset($order['order_id']);
        // 提交预报？保存草稿
        $order['order_status'] = 'D';
        $this->_order['order_status'] = $status;
        //echo __LINE__;
        
        if($this->_order['invoice_print']){
        	$order['makeinvoicedate'] = $this->_order['makeinvoicedate']?$this->_order['makeinvoicedate']:'';
        	$order['export_type'] = $this->_order['export_type']?$this->_order['export_type']:'';
        	$order['trade_terms'] = $this->_order['trade_terms']?$this->_order['trade_terms']:'';
        	$order['invoicenum'] = $this->_order['invoicenum']?$this->_order['invoicenum']:'';
        	$order['pay_type'] = $this->_order['pay_type']?$this->_order['pay_type']:'';
        	$order['fpnote'] = $this->_order['fpnote']?$this->_order['fpnote']:'';
        }
        
        $order = Common_Common::arrayNullToEmptyString($order);
       
        if(empty($this->_order_id)){
            // 新增
            $order['create_date'] = date('Y-m-d H:i:s');
            $order['creater_id'] = $this->_order['creater_id'];
            $order['order_status'] = "S";//丢到换号中
            $this->_order_id = Service_CsdOrder::add($order);
            $this->_log[] = Ec::Lang('订单新增');
        }else{
            unset($order['create_date']);
            unset($order['creater_id']);
            unset($order['order_create_code']);
            
            // 编辑
            $existOrder = Service_CsdOrder::getByField($this->_order_id, 'order_id');
            if($existOrder['customer_id'] != Service_User::getCustomerId()){
                throw new Exception(Ec::Lang('非法操作'));
            }
            $allowStatus = array(
                'D',
                'Q'
            );
            if(! in_array($existOrder['order_status'], $allowStatus)){
                throw new Exception(Ec::Lang('订单不允许编辑'));
            }
            
            // 更新
            Service_CsdOrder::update($order, $this->_order_id, 'order_id');
            $this->_log[] = Ec::Lang('订单编辑');
            
            $this->_getOrderChangeLog();
            $this->_existOrder = $existOrder;
        }
        // 删除旧数据 start
        Service_CsdInvoice::delete($this->_order_id, 'order_id');
        Service_CsdShipperconsignee::delete($this->_order_id, 'order_id');
        Service_CsdExtraservice::delete($this->_order_id, 'order_id');
        // 删除旧数据 end
        // 数据保存 start
        //echo "<pre>";print_r($this->_invoice);die;
        if(!empty($this->_invoice)){
            foreach($this->_invoice as $invoicek =>$row){
                // print_r($row);
                $ivs = array(
                    'order_id' => $this->_order_id,
                    'invoice_enname' => $row['invoice_enname'],
                    'invoice_cnname' => $row['invoice_cnname'],
                    'unit_code' => empty($row['unit_code']) ? 'PCE' : $row['unit_code'],
                    'invoice_quantity' => $row['invoice_quantity'],
                	'invoice_totalcharge' => $row['invoice_totalcharge'],
                	//新增
                	'invoice_weight' => $row['invoice_weight'],
                	'invoice_totalWeight' => $row['invoice_totalWeight'],
                	//新增结束
                    'invoice_currencycode' => $row['invoice_currencycode'],
                    'hs_code' => $row['hs_code'],
                    'invoice_note' => $row['invoice_note'],
                    'invoice_url' => $row['invoice_url'],
                    'sku' => $row['sku'],
                    'invoice_length'    =>  empty($row['invoice_length'])?0:$row['invoice_length'],
                    'invoice_width'     =>  empty($row['invoice_width'])?0:$row['invoice_width'],
                    'invoice_height'    =>  empty($row['invoice_height'])?0:$row['invoice_height'],
                    'invoice_shippertax'    =>  empty($row['invoice_shippertax'])?'':$row['invoice_shippertax'],
                    'invoice_consigneetax'    =>  empty($row['invoice_consigneetax'])?'':$row['invoice_consigneetax'],
                    'invoice_totalcharge_all'    =>  empty($row['invoice_totalcharge_all'])?0:$row['invoice_totalcharge_all'],
                );
                $ivs = Common_Common::arrayNullToEmptyString($ivs);
                //print_r($ivs);die;
                //$sql="insert into csd_invoice (invoice_weight,invoice_totalWeight) values('2','6')";
                Service_CsdInvoice::add($ivs);
            }
        }
        
        if(!empty($this->_label)&&$this->_order['invoice_print']){
        	foreach($this->_label as $labelk =>$lrow){
        		// print_r($row);
        		$ivs = array(
        				'order_id' => $this->_order_id,
        				'invoice_quantity' => $lrow['invoice_quantity'],
        				'invoice_note' => $lrow['invoice_note'],
        				'invoice_shipcode' => empty($lrow['invoice_shipcode'])?"":$lrow['invoice_shipcode'],
        				'invoice_unitcharge' => empty($lrow['invoice_unitcharge'])?"":$lrow['invoice_unitcharge'],
        				'invoice_proplace' => empty($lrow['invoice_proplace'])?"":$lrow['invoice_proplace'],
        		);
        		
        		$ivs = Common_Common::arrayNullToEmptyString($ivs);
        		Service_CsdInvoiced::add($ivs);
        	}
        }
        
        $shipper = array(
            'shipper_account' => $this->_shipper['shipper_account'],
            'shipper_name' => $this->_shipper['shipper_name'],
            'shipper_company' => $this->_shipper['shipper_company'],
            'shipper_countrycode' => $this->_shipper['shipper_countrycode'],
            'shipper_province' => $this->_shipper['shipper_province'],
            'shipper_city' => $this->_shipper['shipper_city'],
            'shipper_street' => $this->_shipper['shipper_street'],
            'shipper_postcode' => $this->_shipper['shipper_postcode'],
            'shipper_areacode' => $this->_shipper['shipper_areacode'],
            'shipper_telephone' => $this->_shipper['shipper_telephone'],
            'shipper_mobile' => $this->_shipper['shipper_mobile'],
            'shipper_email' => $this->_shipper['shipper_email'],
            'shipper_certificatecode' => $this->_shipper['shipper_certificatecode'],
            'shipper_certificatetype' => $this->_shipper['shipper_certificatetype'],
            'shipper_fax' => $this->_shipper['shipper_fax'],
            'shipper_mallaccount' => $this->_shipper['shipper_mallaccount']
        );
        //替换地址||=》" "
        //$shipper['shipper_street']= str_replace("||", " ", $shipper['shipper_street']);
        $consignee = array(
            'consignee_name' => $this->_consignee['consignee_name'],
            'consignee_company' => $this->_consignee['consignee_company'],
            'consignee_countrycode' => $this->_consignee['consignee_countrycode'],
            'consignee_province' => $this->_consignee['consignee_province'],
            'consignee_city' => $this->_consignee['consignee_city'],
            'consignee_street' => $this->_consignee['consignee_street'],
            'consignee_street2' => $this->_consignee['consignee_street2'],
            'consignee_street3' => $this->_consignee['consignee_street3'],
            'consignee_postcode' => $this->_consignee['consignee_postcode'],
            'consignee_areacode' => $this->_consignee['consignee_areacode'],
            'consignee_telephone' => $this->_consignee['consignee_telephone'],
            'consignee_mobile' => $this->_consignee['consignee_mobile'],
            'consignee_fax' => $this->_consignee['consignee_fax'],
            'consignee_email' => $this->_consignee['consignee_email'],
            'consignee_certificatecode' => $this->_consignee['consignee_certificatecode'],
            'consignee_mallaccount' => $this->_consignee['consignee_mallaccount'],
            'consignee_credentials_period' => $this->_consignee['consignee_credentials_period'],
            'consignee_certificatetype' => $this->_consignee['consignee_certificatetype'],
            'consignee_doorplate' => $this->_consignee['consignee_doorplate'],
        	'consignee_taxno' => $this->_consignee['consignee_tax_no']	
        );
        $shipper_consignee = array_merge($shipper, $consignee);
        $shipper_consignee['order_id'] = $this->_order_id;
        // print_r($shipper_consignee); exit;
        $shipper_consignee = Common_Common::arrayNullToEmptyString($shipper_consignee);
        Service_CsdShipperconsignee::add($shipper_consignee);
        
        foreach($this->_extraservice as $row){
            $svc = array(
                'order_id' => $this->_order_id,
                'extra_servicecode' => $row['extra_servicecode'],
                'extra_servicevalue' => $row['extra_servicevalue'],
                'extra_servicenote' => $row['extra_servicenote'],
                'extra_createdate' => $row['extra_createdate'],
                'extra_createrid' => $row['extra_createrid']
            );
            //投保
            if($row['extra_service_group']=='C0'){
            	$svc['extra_servicevalue'] = $this->_order['insurance_value'];
            }
            $svc = Common_Common::arrayNullToEmptyString($svc);
            Service_CsdExtraservice::add($svc);
        }
        // 数据保存 end
        
        // 日志
        $logRow = array(
            'ref_id' => $this->_order_id,
            'log_content' => implode(';\n', $this->_log)
        );
        Service_OrderLog::add($logRow);
        
        // 提交预报
        if($status == 'P'){
            $this->_verifyValidate($this->_order_id, 'verify');
            
            // 订单验证异常
            if($this->_err){
                throw new Exception(Ec::Lang('信息异常，处理中断'));
            }
            if($import){
            	$this->_verifyRs = $this->_verifyProcess($this->_order_id, 'verify',true);
            }
            // 订单处理
            //
        }
    }

    /**
     * 订单操作
     * 
     * @param unknown_type $order_id            
     * @param unknown_type $op            
     * @throws Exception
     */
    protected function _verifyValidate($order_id, $op)
    {
        try{
            $order = Service_CsdOrder::getByField($order_id, 'order_id');
            if(! $order){
                throw new Exception(Ec::Lang('订单不存在或已删除') . '-->' . $order_id);
            } 
            if($order['customer_id'] != Service_User::getCustomerId()){
                throw new Exception(Ec::Lang('非法操作'));
            }
            // 1.草稿 D
            // 2.已预报 P
            // 2.换号中 S
            // 3.已入仓 V
            // 4.已发货 C
            // 5.暂存件 Q
            // 0.已废弃 E
            switch(strtolower($op)){
                case 'verify': // 调教预报
                    $allowStatus = array(
                        'D',
                        'Q',
                        'U',
			'S',
                    );
                    $this->_verify_tip = '此操作只允许对草稿、暂存、问题件状态的订单进行操作，请确认您选择的订单信息是否正确';
                    break;
                case 'pause': // 暂存
                    $allowStatus = array(
                        'D'
                    );
                    $this->_verify_tip = '此操作只允许对草稿状态的订单进行操作，请确认您选择的订单信息是否正确';
                    break;
                case 'discard': // 废弃
                    $allowStatus = array(
                        'D',
                        'Q',
                        'P',
                        'U',
                        'S',
                    );
                    $this->_verify_tip = '此操作只允许对草稿、暂存状态的订单进行操作，请确认您选择的订单信息是否正确';
                    break;
                case 'intercept': // 拦截
                    $allowStatus = array(
                        'P',
                        'V'
                    );
                    $this->_verify_tip = '此操作只允许对已预报、已入仓状态的订单进行操作，请确认您选择的订单信息是否正确';
                    break;
                
                case 'discard2draft': // 转草稿
                    $allowStatus = array(
                        'E',
                        'Q'
                    );
                    $this->_verify_tip = '此操作只允许对已废弃订单进行操作，请确认您选择的订单信息是否正确';
                    // $this->_verify_tip = '此操作禁止，请确认您选择的订单信息是否正确';
                    break;
                
                case 'export': // 导出
                    $allowStatus = array(
                        'D',
                        'P',
                        'V',
                        'C',
                        'Q',
                        'E'
                    );
                    $this->_verify_tip = '';
                    break;
                
                case 'print': // 打印
                    $allowStatus = array(
                        'P',
                        'V',
                        'C'
                    );
                    $this->_verify_tip = '此操作只允许对已预报、已入仓、已发货状态的订单进行操作，请确认您选择的订单信息是否正确';
                    break;
				case 'printasn' : // 打印
					$allowStatus = array (
							'P' 
					);
					$this->_verify_tip = '此操作只允许对已预报状态的订单进行操作，请确认您选择的订单信息是否正确';
					break;
				
				case 'printinvoice' : // 打印
					$allowStatus = array (
							'P' 
					);
					$this->_verify_tip = '此操作只允许对已预报状态的订单进行操作，请确认您选择的订单信息是否正确';
					break;
                default:
                    throw new Exception(Ec::Lang('不合法的操作'));
            }
            $order['order_status'] = strtoupper($order['order_status']);
            
            if(! in_array($order['order_status'], $allowStatus)){
                throw new Exception(Ec::Lang('订单不允许该操作') . '&nbsp;&nbsp;[' . $order['shipper_hawbcode'] . ']');
            }
        }catch(Exception $e){
            $this->_err[] = $e->getMessage();
        }
    }

    public function getExistData()
    {
        
        // 验证订单信息的正确与完整 start
        $con = array(
            'order_id' => $this->_order_id
        );
        $this->_existInvoice = Service_CsdInvoice::getByCondition($con);
        $this->_existShipperConsignee = Service_CsdShipperconsignee::getByCondition($con);
        $this->_existExtservice = Service_CsdExtraservice::getByCondition($con);
        // 验证订单信息的正确与完整 end
    }

    /**
     * 订单操作
     *
     * @param unknown_type $order_id            
     * @param unknown_type $op            
     * @throws Exception
     */
    protected function _verifyProcess($order_id, $op,$import=false)
    {
        $order = Service_CsdOrder::getByField($order_id, 'order_id');
        $updateRow = array(
            'modify_date' => date('Y-m-d H:i:s')
        );
        
        $db = Common_Common::getAdapter();
        // D 草稿
        // S 换号中
        // A 可用订单
        // P 已预报
        // V 已收货
        // C 已出仓
        // E 已废弃
        $order_process = array();
        $log_content = array();
        switch(strtolower($op)){
            case 'verify': // 提交预报
                //$updateRow['order_status'] = 'P';
                //$updateRow['post_date'] = date('Y-m-d H:i:s');
                $log_content[] = Ec::Lang('订单提交预报');
                // 换号验证 start==============================
                // 涉及到表
                // pbr_productrule
                // atd_customer_document_type
                // atd_regist_code_available
                // atd_regist_code_used
                $product = Service_PbrProductrule::getByField($order['product_code'], 'product_code');
                if(! $product){
                    throw new Exception(Ec::Lang('产品不存在') . "[{$order['product_code']}]");
                }
               
//                 print_r($product);die;
                // 换号
                 if($import)
                	$result = $this->changeNO($order, $product['web_document_rule'],$import);
               	else
                	$result = $this->changeNO($order, $product['web_document_rule']);
               
               
              	/*
              	 * document_change_sign 结果说明
              	 * N: 不用换号
              	 * C: 符合规则不换号
              	 * L： 单号库取号
              	 * A：API换号
              	 */ 
                // 当为API换号，并且不是及时换号时订单状态改成换号中
            	/* if($result['document_change_sign'] == 'A' && $result['type'] == '1') {
            		$updateRow['order_status'] = 'S';
            	}
            	
            	// 更新单号
            	$updateRow['server_hawbcode'] = $result['server_hawbcode'];	
            	
                //服务商单号换号类型，l为本地单号分配，a为api换号，n为未换号,C:客户单号作为服务商单号
                $updateRow['document_change_sign'] = strtoupper($result['document_change_sign']);
                //换号日志
                if($order['server_hawbcode']!=$updateRow['server_hawbcode']){
                    $log_content[] = Ec::Lang('服务商换号') . ',' . Ec::Lang('原服务商单号') . ' ' . ($order['server_hawbcode']?$order['server_hawbcode']:Ec::Lang('为空')) . ' ' . Ec::Lang('更改为') . ' ' . $updateRow['server_hawbcode'];
                    $order['server_hawbcode'] = $updateRow['server_hawbcode'];
                } */
                // 换号验证 end==============================
                
                // 插入轨迹 start
                // TODO DB2 最好换API对接
                $db2 = Common_Common::getAdapterForDb2();
                $rule_id = $product['rule_id'];
                $sql = "select * from pbr_customerrule where rule_id='{$rule_id}';";
                $web_trackshow = 'N';
                $customerrule = $db2->fetchRow($sql);
                if($customerrule){
                    $web_trackshow = $customerrule['web_trackshow'];
                }
                //获取收件人
                $shipperconsigneeInfo = Service_CsdShipperconsignee::getByField($order_id);
                // 轨迹主干
                $tak_trackingbusiness = array(
                    'customer_id' => $order['customer_id'],
                    'track_server_code' => '',
                    'shipper_hawbcode' => $order['shipper_hawbcode'],
                    'server_hawbcode' => $order['server_hawbcode'],
                    'country_code' => $order['country_code'],
//                     'new_operation_status' => '',
//                     'new_error_code' => '',
                    'new_operation_date' => date('Y-m-d H:i:s'),
                    'new_track_code' => 'IR',
                    'new_track_date' => date('Y-m-d H:i:s'),
//                     'new_track_location' => '',
                    'new_track_comment' => Ec::Lang('运单电子信息已收到'),
//                     'close_code' => '',
//                     'hash_code' => '',
//                     'close_date' => '',
                    'signatory_name' => empty($shipperconsigneeInfo["consignee_name"])?'':$shipperconsigneeInfo["consignee_name"],
//                     'start_track_date' => '',
//                     'end_track_date' => '',
//                     'reference_date' => '',
                    'create_date' => date('Y-m-d H:i:s'),
//                     'pass_back_date' => '',
                    'shipper_hawbcode_tracksign' => '',
                    'web_order_id' => $order['order_id'],
//                     'sys_bs_id' => '',
                    'show_sign' => $web_trackshow,
                    'tms_id' => $order['tms_id']
                );
                $db2->insert('tak_trackingbusiness', $tak_trackingbusiness);
                $tbs_id = $db2->lastInsertId();
                // 轨迹内容主干
                $tak_trackdetails = array(
                    'tbs_id' => $tbs_id,
                    'track_code' => 'IR',
                    'track_source' => "W",
                    'track_occur_date' => date('Y-m-d H:i:s'),
                    'track_area_description' => '',
                    'track_create_date' => date('Y-m-d H:i:s'),
                    'track_create_person' => Service_User::getUserName(),
//                     'pass_back_date' => ''
                );
                $db2->insert('tak_trackdetails', $tak_trackdetails);
                $trk_id = $db2->lastInsertId();
                // 轨迹内容
                $tak_trackattach = array(
                    'trk_id' => $trk_id,
                    'track_description' => Ec::Lang('订单提交预报')
                );
                $db2->insert('tak_trackattach', $tak_trackattach);
                $tak_trackhawbcode_arr = array(
                    $order['shipper_hawbcode'],
                    $order['server_hawbcode'],
                    $order['channel_hawbcode']
                );
                // 相关单据
                $tak_trackhawbcode_arr = array_unique($tak_trackhawbcode_arr);
                foreach($tak_trackhawbcode_arr as $track_hawbcode){
                    if(empty($track_hawbcode)){
                        continue;
                    }
                    $tak_trackhawbcode = array(
                        'tbs_id' => $tbs_id,
                        'tms_id' => $order['tms_id'],
                        'track_hawbcode' => $track_hawbcode
                    );
                    $db2->insert('tak_trackhawbcode', $tak_trackhawbcode);
                }
                //平台订单
                if(strtolower($order['order_create_code'])=='p'){
                	
                	$product_kind = Service_CsiProductkind::getByField($order['product_code'], "product_code");
                    // 更新运输方式与跟踪号
                    $row = array(
                        'carrier_name' => $product_kind['product_carrier_name'],
                        'shipping_method' => $order['product_code'],
                        'shipping_method_no' => $updateRow['server_hawbcode']
                    );
                    Service_Orders::update($row, $order['refer_hawbcode'], 'refrence_no');
                }
                // 插入轨迹 end
                break;
            case 'pause': // 暂存
                          // $updateRow['order_status'] = 'Q';
                $updateRow['order_status'] = 'Q';
                $log_content[] = Ec::Lang('订单暂存');
                break;
            case 'discard': // 废弃
            	
            	$channelid = $order['channel_id'];
            	
            	/* $sql = "select sc.formal_code from csi_servechannel sc where sc.server_channelid = '{$channelid}'";
            	$db2 =Common_Common::getAdapterForDb2(); 
            	$channels = $db2->fetchRow($sql);
            	
            	$objCommon = new API_Common_ServiceCommonClass();
            	$channel = $objCommon->getServiceChannelByFormalCode($channels['formal_code']);
            	if (empty($channel)) {
            		throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
            	}
            	//服务对应的数据类
            	$class = $objCommon->getForApiServiceClass($channel['as_code']);
            	if (empty($class)) {
            		throw new Exception("无法获取到[{$formalCode}]对应的数据映射类");
            	}
            	
            	if (class_exists($class)) {
            		$obj = new $class();
            	} else {
            		throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
            	}
            	//test
            	$obj->setParam($channel['as_code'], $order['shipper_hawbcode'], $channel['server_channelid'], $channel['server_product_code'],false);
            	//setParam(API服务代码，订单号，服务商渠道ID，服务商系统的产品代码，是否初始化订单数据，取客户单号OR运单号，取csd_order表OR bsn_XX表数据)
            	$re = $obj->cancelOrder();
            	
            	if (!$re['ack']){
            		throw new Exception("订单作废失败"); 
            	} */
                $updateRow['order_status'] = 'E';
                $updateRow['refer_hawbcode'] = $order['refer_hawbcode'] ;
                $updateRow['shipper_hawbcode'] = $order['shipper_hawbcode'] ;
                $updateRow['server_hawbcode'] = $order['server_hawbcode'] ;
                
                $log_content[] = Ec::Lang('订单废弃');
                break;
            case 'intercept': // 拦截
                if($order['order_status'] == 'P'){
                    // 已预报
                    // 直接在CSI_ORDER 表中 holdsign 更改标识为Y
                    
                }
                if($order['order_status'] == 'V'){
                    // 已入仓
                }
              
                
                //$updateRow['order_status'] = 'E';
                $updateRow['hold_sign'] = 'Y';
                
                //$updateRow['shipper_hawbcode'] = $order['shipper_hawbcode'] . 'D' . Common_Common::random(3, 1);
                //$updateRow['server_hawbcode'] = $order['server_hawbcode'] . 'D' . Common_Common::random(3, 1);
                
                $log_content[] = Ec::Lang('订单拦截');
                break;
            case 'discard2draft': // 转草稿
                $updateRow['order_status'] = 'D';
                $log_content[] = Ec::Lang('订单转草稿');
                break;
            case 'export': // 导出
                $log_content[] = Ec::Lang('订单导出');
                
                break;
			case 'print' : // 打印
				$updateRow ['print_date'] = date ( 'Y-m-d H:i:s' );
				$log_content [] = Ec::Lang ( '订单打印' );
				
				break;
			
			case 'printasn' : // 打印
				$log_content [] = Ec::Lang ( '订单打印交货清单' );
				break;
			case 'printinvoice' : // 打印
				$log_content [] = Ec::Lang ( '订单打印形式发票' );
				break;
            default:
                throw new Exception(Ec::Lang('不合法的操作'));
        }
        if(!empty($updateRow))
       	 Service_CsdOrder::update($updateRow, $order_id, 'order_id');
        
        // 日志
        $logRow = array(
            'ref_id' => $order_id,
            'log_content' => implode(";\n", $log_content)
        );
        Service_OrderLog::add($logRow);
        $order = array_merge($order, $updateRow);
        $this->_order = $order;
        return $order;
    }

    /**
     * 审核
     */
    public function verifyOrderBatchTransaction($orderIdArr, $op)
    {
        $return = array(
            'ask' => 0,
            'message' => Ec::Lang($op) . Ec::Lang('操作失败')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $rsArr = array();
        try{
            if(empty($orderIdArr)){
                throw new Exception(Ec::Lang('没有选择订单'));
            }
            // 订单验证
            foreach($orderIdArr as $order_id){
                $this->_verifyValidate($order_id, $op);
            }
            // 订单验证异常
            if($this->_err){
                throw new Exception(Ec::Lang('信息异常，处理中断'));
            }
           
            // 订单处理
            foreach($orderIdArr as $order_id){
                //如果跟踪号没有获取 则不能打印
                $getTrackingCode = Service_CsdOrder::getByField($order_id, 'order_id');
                if(empty($getTrackingCode['server_hawbcode']) && $op == "print"){
                    continue;
                }
                $rs = $this->_verifyProcess($order_id, $op);
                $rsArr[] = $rs;
            }
            
            $db->commit();
            if(empty($rsArr)){
                $return['ask'] = 1;
                $return['message'] = Ec::Lang($op) . Ec::Lang('操作失败');
            }else{
                $return['ask'] = 1;
                $return['message'] = Ec::Lang($op) . Ec::Lang('操作成功');
            }

        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
            array_unshift($this->_err, $e->getMessage());
        }
        //
        if(! empty($this->_err) && ! empty($this->_verify_tip)){
            array_unshift($this->_err, $this->_verify_tip);
        }
        $return['err'] = $this->_err;
        $return['rs'] = $rsArr;
        return $return;
    }

    /**
     * 订单审核
     *
     * @param unknown_type $order_id            
     * @param unknown_type $op            
     * @throws Exception
     * @return multitype:number string NULL multitype:
     */
    public function verifyOrderSingleTransaction($order_id, $op)
    {
        $return = array(
            'ask' => 0,
            'message' => Ec::Lang($op) . Ec::Lang('操作失败')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $rsArr = array();
        try{
            // 订单验证
            $this->_verifyValidate($order_id, $op);
            
            // 订单验证异常
            if($this->_err){
                throw new Exception(Ec::Lang('订单状态异常，处理中断'));
            }
            // 订单处理
            $rsArr[] = $this->_verifyProcess($order_id, $op);
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = Ec::Lang($op) . Ec::Lang('操作成功');
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
            $this->_err[] = $e->getMessage();
        }
        $return['err'] = $this->_err;
        $return['rs'] = $rsArr;
        return $return;
    }
    
    /**
     * 获取服务商单号
     * 1. 获取物流产品对应的
     */ 
    public function changeNO($order = array(), $web_document_rule = '',$import=false) {
    	// 返回结果
    	$return = array(
    					'document_change_sign' => 'N', 
		    			'message' => '', 
		    			'type' => 0, 
		    			'server_hawbcode' => ''
    			);
    	
    	// 当服务商单号为空时，等于客户单号
    	if(empty($order['server_hawbcode'])) {
    		//$order['server_hawbcode'] = $order['shipper_hawbcode'];
            $order['server_hawbcode'] = "";
    	}
    	
    	// 当为空时直接返回
    	if(empty($web_document_rule)) {
    		// 当服务商单号为空时
    		$return['server_hawbcode'] = $order['server_hawbcode'];
    		return $return;
    	}
    	
    	/*
    	 * 获取单据规则，规则字段值通过分号“;”分隔，前部分为换号类型, 后部分为单据规则ID， 如 Y;1
    	 * 前部分值说明：Y为每个单提交预报时换号，N为符合号码规则不换号
    	 */ 
    	$web_document_rule_arr = preg_split('/;/', $web_document_rule);
    	// 规则ID
    	$customer_document_type_id = $web_document_rule_arr[1];
    	// 查询规则
    	$sql = "select *
	    	from atd_customer_document_type
	    	where customer_document_type_id='{$customer_document_type_id}'";
    	
    	// 规则不存在直接返回
    	// TODO DB2
    	$db = Common_Common::getAdapterForDb2();
    	
    	
    	// 符合号码规则不换号
    	if(strtoupper($web_document_rule_arr[0]) == 'N') {
    		
    		$atd_customer_document_type = $db->fetchRow($sql);
    		if(!$atd_customer_document_type) {
    			throw new Exception(Ec::Lang('产品换号规则不存在')."[{$order['product_code']}]");
    		}
    		 
    		// 订单号码规则,正则表达式
    		$document_rule = $atd_customer_document_type['document_rule'];
    		
    		// 校验是否符合规则
    		if($document_rule && preg_match('/' . $document_rule . '/', $order['server_hawbcode'])) { 
    			$return['server_hawbcode'] = $order['server_hawbcode'];
    			$return['document_change_sign'] = 'C';
    			return $return;
    		}
    	}
    	
    	// 非API换号, 单号池取号==========开始===========
    	if(strtoupper($web_document_rule_arr[0]) != 'A') {
    		// 号码池
    		$sql = "SELECT
						*
					FROM
						atd_regist_code_available a
					INNER JOIN atd_regist_document_head b ON a.regist_code_id = b.regist_code_id
					AND a.customer_document_type_id = b.customer_document_type_id
					WHERE
						a.customer_document_type_id = '{$customer_document_type_id}'
					AND b.regist_status = 'Y'
					LIMIT 1;";
    		$atd_regist_code_available = $db->fetchRow($sql);
    		if(! $atd_regist_code_available){
    			throw new Exception(Ec::Lang('订单服务商单号不足,请尝试选择少量订单进行操作') . "[{$order['product_code']}]");
    		}
    		
    		// 从号码池中删除
    		$sql = "DELETE
					FROM
						atd_regist_code_available
					WHERE
						code_id = '{$atd_regist_code_available['code_id']}'";
    		$db->query($sql);
    		
    		// 插入已用号码池
    		$used = array(
    				//'code_id' => $atd_regist_code_available['code_id'],
    				'regist_code_id' => $atd_regist_code_available['regist_code_id'],
    				'customer_document_type_id' => $atd_regist_code_available['customer_document_type_id'],
    				'regist_code' => $atd_regist_code_available['regist_code'],
    				'bs_id' => $order['order_id'],
    				'used_date' => date('Y-m-d H:i:s')
    		);
    		$db->insert('atd_regist_code_used', $used);
    		
    		$return['document_change_sign'] = 'L';
    		$return['server_hawbcode'] = $atd_regist_code_available['regist_code'];
    		return $return;
    	}
       /*  if($order["product_code"] == "NZ_CP" || $order["product_code"] == "NZ_DP" || $order["product_code"] == "NZ_LZ"){
            $listId['string'] = 1;  //NZ_CP，NZ_DP，NZ_LZ对应渠道SAICHENG
        }else if($order["product_code"] == "TNT"){
        	$listId['string'] = 73;
        }else if($order["product_code"] == "ESB"){
        	$listId['string'] = 74;
        }else{
            $listId['string'] = 2;  //G_DHL对应渠道DHL
        } */
		
        $rs_produckconfig = Common_Common::getProductAllByCode($order["product_code"]);
        $listId['string'] = $rs_produckconfig['cid'];

    	$channelid = "";
    	if(is_array($listId['string'])) {
    		$channelid = $listId['string'][0];
    	} else {
    		$channelid = $listId['string'];
    	}
    	$sql = "select sc.formal_code from csi_servechannel sc where sc.server_channelid = {$channelid}";
    	
    	$channcel = $db->fetchRow($sql);
    	if(empty($channcel)) {
    	    throw new Exception(Ec::Lang('该销售产品对应的换号渠道不存在') . "[{$order['product_code']}]");
    	}
    	$obj = new API_Common_ChangeNOFactory();
    	
    	if($import){
    		$changeNOFactory = new API_Common_ChangeNOFactory();
    		$result = $changeNOFactory->changeNOByForecast($order['order_id'], $order['server_hawbcode'], $channcel['formal_code'], $listId['string'],$order['shipper_hawbcode']);
    	}else{
    		$changeNOFactory = new API_Common_AsyncChangeNo();
    		$result = $changeNOFactory->changeNOByForecast($order['order_id'], $order['server_hawbcode'], $channcel['formal_code'], $listId['string'],$order['shipper_hawbcode'],$this->_uuid);
    	}
    	
    	//
    	
    	if(!$result['ack']) {
    		throw new Exception(Ec::Lang('换号失败。') . $result['message']);
    	}
    	//更新csd_order表
    	$update_row = array(
    		'channel_id'=> $channelid
    	);
    	service_csdOrder::update($update_row, $order['order_id']);
    	
    	$return['document_change_sign'] = 'A';
    	$return['type'] = $result['type'];
    	$return['server_hawbcode'] = $result['trackingNumber'];
    	
    	// API 换号==========结束===========
    	return $return;
    }
    
    /**
     * 根据销售产品获取渠道
     * @param unknown_type $productCode
     */
    private function getChannelByProduct($order = array()) {
    	
    	$timeout = 1000;
    	$wsdl = Service_Config::getByField('TMS_FEE_TRAIL_WSDL', 'config_attribute');
    	if(!$wsdl){
    		$wsdl = 'http://127.0.0.1:9001/APIServicesDelegate?wsdl';
    	}else{
    		$wsdl = $wsdl['config_value'];
    	}
    	
    	$options = array (
    			"trace" => true,
    			"connection_timeout" => $timeout,
    			"encoding" => "utf-8"
    	);
    	
    	$client = new SoapClient($wsdl, $options);
    	$csd_shipperconsignee = Service_CsdShipperconsignee::getByField($order['order_id'], "order_id");
    	$postcode = "";
    	if(empty($csd_shipperconsignee)) {
    		$postcode = $csd_shipperconsignee['consignee_postcode'];
    	}    	
  	
    	$req = array (
    			'product_code' => $order['product_code'],
    			'cargo_type' => 'W',
    			'paymentmode_code' => 'P',
    			'country_code' => $order['country_code'],
    			'destination_postcode' => $postcode,
    			'shipper_chargeweight' => $order['order_weight'],
    			'checkin_og_id' => Service_User::getOgId(),
    			'tms_id' => Service_User::getTmsId (),
    	);
    	try {

    		$rs = $client->ChannelSelectReturnList($req);
    		$lst = $rs->ChannelSelectReturnListResult;
    		return Common_Common::objectToArray($lst);
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    	
    	return "";
    }
    
    /**
     * 编辑申报信息
     */
    public function editInvoiceTransaction($invoiceArr)
    {
    	$return = array(
    			'ask' => 0,
    			'message' => Ec::Lang('操作失败')
    	);
    	
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try{
    		if(empty($invoiceArr)){
    			throw new Exception(Ec::Lang('没有申报信息'));
    		}

    		$this->_verifyInvoice($invoiceArr);
    		
    		// 订单验证异常
    		if($this->_err) {
    			$return['err'] = $this->_err;
    			return $return;
    		}
    
    		// 订单处理
    		foreach($invoiceArr as $order_id => $row){
//     			print_r($order_id); 
//     			echo "<>";
//     			print_r($row);die;
    			$this->_editInvoiceProcess($order_id, $row);
    		}
    
    		$db->commit();
    		$return['ask'] = 1;
    		$return['message'] = Ec::Lang('操作成功');
    	}catch(Exception $e){
    		$db->rollback();
    		$return['message'] = Ec::Lang('操作失败');
    		$this->_err[] = $e->getMessage();
    	}
    	
    	//
    	$return['err'] = $this->_err;
    	return $return;
    }
    
    // 验证申报信息
    public function _verifyInvoice($orderInvoiceArr) {
        if(empty($orderInvoiceArr))
            return false;
    	foreach($orderInvoiceArr as $k => $orderInvoice){
    		
    		$csd_order = Service_CsdOrder::getByField($k);
    		// 只有草稿、预报状态可以修改发票
    		$statusArr = array(
    				// 草稿
    				'D',
    				// 预报
    				'P'
    		);
    		
    		if(! in_array($csd_order['order_status'], $statusArr)){
    			$this->_err[] =  $csd_order['shipper_hawbcode'] . " " . Ec::Lang('只有草稿或预报状态的订单支持修改发票');
    			continue;
    		}
    		
    		foreach($orderInvoice as $invoice) {
	    		if($invoice['invoice_enname'] === ''){
	    			$this->_err[] =  $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报品名不可为空');
	    		}
	    		if($invoice['invoice_cnname'] === ''){
	    			//$this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('中文申报品名不可为空');
	    		}
	    		if($invoice['invoice_quantity'] === ''){
	    			$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报数量不可为空');
	    		}else{
	    			if(! preg_match('/^[0-9]+$/', $invoice['invoice_quantity']) || intval($invoice['invoice_quantity']) <= 0){
	    				$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报数量必须为大于0的整数');
	    			}
	    		}
	    		if($invoice['invoice_unitcharge'] === ''){
	    			$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报单价不可为空');
	    		}else{
	    			//                     print_r($invoice);exit;
	    			if(! is_numeric($invoice['invoice_unitcharge'])){
	    				$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报单价必须为数字');
	    			}
	    		}
	    		if($invoice['invoice_weight'] === ''){
	    			//$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报重量不可为空');
	    		}else{
	    			//                     print_r($invoice);exit;
	    			if(! is_numeric($invoice['invoice_weight'])){
	    				//$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报重量必须为数字');
	    			}
	    		}
    		}
    	}
    }
    
    // 验证申报信息
    public function _verifyInvoiceByCode($orderInvoiceArr) {
    	
    	$orderInvoiceResult = array();
    	if(empty($orderInvoiceArr))
    	    return false;
    	foreach($orderInvoiceArr as $k => $orderInvoice){
    		
    		$csd_order = Service_CsdOrder::getByField($k, 'shipper_hawbcode');
    		// 只有草稿、预报状态可以修改发票
    		$statusArr = array(
    				// 草稿
    				'D',
    				// 已提交
    				'P'
    		);
    		
    		// 判断不能为空
    		if(empty($csd_order)) {
    			$this->_err[] = $k . " " . Ec::Lang('单号不存在');
    			continue;
    		}
    		
    		if(! in_array($csd_order['order_status'], $statusArr)){
    			$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('只有草稿或预报状态的订单支持修改发票');
    			continue;
    		}
    		
    		foreach($orderInvoice as $invoice) {
	    		if($invoice['invoice_enname'] === ''){
	    			$this->_err[] =  $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报品名不可为空');
	    		}
	    		if($invoice['invoice_cnname'] === ''){
	    			//$this->_err[] = "(" . Ec::Lang('申报信息') . $k . ")" . Ec::Lang('中文申报品名不可为空');
	    		}
	    		if($invoice['invoice_quantity'] === ''){
	    			$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报数量不可为空');
	    		}else{
	    			if(! preg_match('/^[0-9]+$/', $invoice['invoice_quantity']) || intval($invoice['invoice_quantity']) <= 0){
	    				$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报数量必须为大于0的整数');
	    			}
	    		}
	    		if($invoice['invoice_unitcharge'] === ''){
	    			$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报单价不可为空');
	    		}else{
	    			//                     print_r($invoice);exit;
	    			if(! is_numeric($invoice['invoice_unitcharge'])){
	    				$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报单价必须为数字');
	    			}
	    		}
	    		if($invoice['invoice_weight'] === ''){
	    			//$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报重量不可为空');
	    		}else{
	    			//                     print_r($invoice);exit;
	    			if(! is_numeric($invoice['invoice_weight'])){
	    				//$this->_err[] = $csd_order['shipper_hawbcode'] . " " . Ec::Lang('申报重量必须为数字');
	    			}
	    		}
    		}
    		
    		// 转换成用ID保存
    		$orderInvoiceResult[$csd_order['order_id']] = $orderInvoice;
    	}
    	
    	return $orderInvoiceResult;
    }
    
    // 编辑申报信息处理
    public function _editInvoiceProcess($orderId, $invoice) {
    	// 删除原发票
    	Service_CsdInvoice::delete($orderId, 'order_id');
    	
    	// 新增发票
    	foreach ($invoice as $row) {
    		
    		// print_r($row);
    		$ivs = array(
    				'order_id' => $orderId,
    				'invoice_enname' => $row['invoice_enname'],
    				'invoice_cnname' => $row['invoice_cnname'],
    				'unit_code' => empty($row['unit_code']) ? 'PCE' : $row['unit_code'],
    				'invoice_quantity' => $row['invoice_quantity'],
    				'invoice_totalcharge' => round($row['invoice_unitcharge'] * $row['invoice_quantity'],3),
    				'invoice_weight'=>$row['invoice_weight'],
    				'invoice_totalWeight'=>round($row['invoice_weight']*$row['invoice_quantity'],3),
    				'invoice_currencycode' => 'USD',
    				'hs_code' => $row['hs_code'],
    				'invoice_note' => $row['invoice_note'],
    				'invoice_url' => $row['invoice_url']
    		);
    		$ivs = Common_Common::arrayNullToEmptyString($ivs);
    		Service_CsdInvoice::add($ivs);
    	}
    	
    	// 记录日志
    	$logRow = array(
    			'ref_id' => $orderId,
    			'system' => 'oms',
    			'create_time' => date('Y-m-d H:i:s'),
    			'log_content' => "修改发票信息"
    	);
    	Service_OrderLog::add($logRow);
    }

    // 编辑订单重量
    public function _editWeightTransaction($shipper_hawbcode, $weight) {
    	
    	$return = array(
    			'ask' => 0,
    			'message' => Ec::Lang('操作失败')
    	);
    	
		if(empty($shipper_hawbcode)) {
			$return['message'] = "单号不能为空!";
			return $return;
		}
    	
		if(!preg_match('/^-?[0-9]+(.{0,1}[0-9]*)$/', $weight)) {
			$return['message'] = "重量必须是数字!";
			return $return;
		}
		
		$csd_order = Service_CsdOrder::getByField($shipper_hawbcode, 'shipper_hawbcode');
		if(empty($csd_order)) {
			$csd_order = Service_CsdOrder::getByField($shipper_hawbcode, 'refer_hawbcode');
			if(empty($csd_order)) {
				$return['message'] = "单号不存在!";
				return $return;
			}
		}
		
		$allowStatus = array(
				'D',
				'S',
				'P'
		);
		if(! in_array($csd_order['order_status'], $allowStatus)){
			$return['message'] = "只支持“草稿”，“换号中”，“已预报”等状态更新重量!";
			return $return;
		}
		
		$db = Common_Common::getAdapter();
		$db->beginTransaction();
		
		try {
			
			$update_row = array('order_weight' => $weight);
			Service_CsdOrder::update($update_row, $csd_order['order_id']);
	    	 
	    	// 记录日志
	    	$logRow = array(
	    			'ref_id' => $csd_order['order_id'],
	    			'system' => 'oms',
	    			'create_time' => date('Y-m-d H:i:s'),
	    			'log_content' => "修改订单重量，原重量：" . $csd_order['order_weight'] . " ，新重量：" . $weight
	    	);
	    	Service_OrderLog::add($logRow);
	    	
	    	
	    	$db->commit();
	    	
	    	$return['message'] = Ec::Lang("operationSuccess");
	    	$return['ask'] = 1;
		} catch(Exception $e) {
			$return['message'] = $e->getMessage();
		}
		
		return $return;
    }
}