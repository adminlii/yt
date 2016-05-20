<?php
if(!function_exists('microtime_float')){
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", micromicrotime_float());
		return $usec + $sec;
	}
}
class Common_Svc
{

    protected $_companyCode = null;
    protected $_company = null;

    /**
     * 日志
     * 
     * @param unknown_type $error            
     */
    private function log($error)
    {
        $logger = new Zend_Log();
        $uploadDir = APPLICATION_PATH . "/../data/log/";
        $writer = new Zend_Log_Writer_Stream($uploadDir . 'api.log');
        $logger->addWriter($writer);
        $logger->info(date('Y-m-d H:i:s') . ': ' . $error . " \n");
    }

    /**
     * 数据初始化验证
     * 
     * @param unknown_type $req            
     * @throws Exception
     */
    private function init($req)
    {
        if(empty($req['appToken'])){
            throw new Exception(Ec::Lang('param_can_not_empty', 'appToken'), '50002');
        }
        if(empty($req['appKey'])){
            throw new Exception(Ec::Lang('param_can_not_empty', 'appKey'), '50003');
        }
        if(empty($req['paramsJson'])){
            // throw new Exception('paramsJson 不能为空');
        }
        if(empty($req['service'])){
            throw new Exception(Ec::Lang('param_can_not_empty', 'service'), '50004');
        }
        /**
         * 判断系统是否支持方法
         */
        if(! method_exists($this, $req['service'])){
            throw new Exception(Ec::Lang('param_invalid', 'service'), '50005');
        }
        $con = array(
            'ca_token' => $req['appToken'],
            'ca_key' => $req['appKey']
        );
        $company = Service_CustomerApi::getByCondition($con);
        if(! empty($company)){
            $company = $company[0];
            $this->_companyCode = $company['customer_code'];
            $sql = "select * from csi_customer where customer_code='{$company['customer_code']}';";
            $db = Common_Common::getAdapterForDb2();
            $customer = $db->fetchRow($sql);
            if(!$customer){
            	throw new Exception('客户代码非法',50001);
            }
            $company['tms_id'] = $customer['tms_id'];
            $this->_company = $company;
            $user = Service_User::getByField($company['user_id'],'user_id');
       		if(!$user){
            	throw new Exception('客户代码非法',50001);
            }
            $user['tms_id'] = $customer['tms_id'];
            $upRow = Service_UserPosition::getByField($user['up_id'], 'up_id');
            $user['upl_id'] = $upRow['upl_id'];
            
            $user['csi_customer'] = $customer;
            
        	$session = new Zend_Session_Namespace('userAuthorization');
        	
        	$session->user =$user;
        	$session->company_code =$customer ['customer_code'];
        	$session->customer_code =$customer ['customer_code'] ;

        	$session->user = $user;
        	$session->csi_customer = $customer;
        	$session->userId = $user['user_id'];
        	$session->customer_id = $user['customer_id'];
        	$session->customer_code = $customer['customer_code'];
        	$session->userCode = $user['user_code'];
        	

            // $this->_companyCode = '100002';
        }else{
            throw new Exception(Ec::Lang('invalid_token_key'), 50001);
        }
    }

    /**
     * 接口入口
     * 
     * @param string $req            
     * @return string
     */
    public function callService($req)
    {
    	$start = microtime_float();
        try{
            set_time_limit(0);
            // 对象转数组
            $req = Common_Common::objectToArray($req);
            $this->init($req);
            $service = $req['service'];
            if(isset($req['paramsJson'])){
                try{
                    $params = Zend_Json::decode($req['paramsJson']);
                }catch(Exception $ee){
                    throw new Exception(Ec::Lang('param_invalid', paramsJson), '50006');
                }
                Ec::showError(print_r($params, true), 'oms_req_'.date('Ymd'));
                $rs = $this->$service($params);
            }else{
                $rs = $this->$service();
            }
            $end = microtime_float();
            $rs['time_cost(s)'] = $end-$start;
            Ec::showError(print_r($rs, true), 'oms_res_'.date('Ymd'));
            $return = array(
                'response' => function_exists('to_json') ? to_json($rs) : Zend_Json::encode($rs)
            );
        }catch(Exception $e){
            $rs = array(
                'ask' => 'Failure',
                'message' => $e->getMessage(),
                'err_code' => $e->getCode()
            );
            $end = microtime_float();
            $rs['time_cost(s)'] = $end-$start;
            $return = array(
                'response' => function_exists('to_json') ? to_json($rs) : Zend_Json::encode($rs)
            );
        }
        $session = new Zend_Session_Namespace('userAuthorization');
        $session->unsetAll();
        return $return;
    }

    /**
     * 获取国家
     */
    public function getCountry($params = array())
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $con = array();
            $pageSize = empty($params['pageSize']) ? 0 : $params['pageSize'];
            $page = empty($params['page']) ? 0 : $params['page'];
            
            $return['pagination'] = array(
                'page' => $page,
                'pageSize' => $pageSize
            );
            
            $count = Service_Country::getByCondition($con, 'count(*)');
            $return['count'] = $count;
            $return['nextPage'] = $pageSize * $page && $pageSize * $page < $count ? 'true' : 'false';
            
            $field = array(
                'country_id',
                'country_code',
                'country_name',
                'country_name_en'
            );
            $country = Service_Country::getByCondition($con, $field, $pageSize, $page);
            // 特殊处理 start
            foreach($country as $k => $v){
                foreach($v as $kk => $vv){
                    $v[$kk] = trim($vv);
                }
                $country[$k] = $v;
            }
            // 特殊处理 end
            $return['data'] = $country;
            $return['ask'] = 'Success';
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['ask'] = 'Failure';
            $return['Error'] = array(
                'errMessage' => $e->getMessage(),
                'errCode' => $e->getCode()
            );
        }
        
        return $return;
    }

    /**
     * 获取国家
     */
    private function getCountryPagination($params = array())
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $pageSize = $params['pageSize'];
            $page = $params['page'];
            
            if(! isset($pageSize) || ! preg_match('/^[0-9]+$/', $pageSize) || intval($pageSize) == 0){
                throw new Exception('param pageSize error');
            }
            
            if(! isset($page) || ! preg_match('/^[0-9]+$/', $page) || intval($page) == 0){
                throw new Exception('param page error');
            }
            $return['pagination'] = array(
                'page' => $page,
                'pageSize' => $pageSize
            );
            $con = array();
            $count = Service_Country::getByCondition($con, 'count(*)');
            $return['count'] = $count;
            $return['nextPage'] = $pageSize * $page < $count ? 'true' : 'false';
            
            $field = array(
                'country_id',
                'country_code',
                'country_name',
                'country_name_en'
            );
            $country = Service_Country::getByCondition($con, $field, $pageSize, $page);
            
            $return['data'] = $country;
            $return['ask'] = 'Success';
            $return['message'] = 'Success';
        }catch(Exception $e){
            $return['ask'] = 'Failure';
            $return['Error'] = array(
                'errMessage' => $e->getMessage(),
                'errCode' => $e->getCode()
            );
        }
        
        return $return;
    }


    /**
     * 创建订单
     * 
     * @param unknown_type $orderInfo            
     * @param unknown_type $returnType            
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function createOrder($orderInfo)
    {
        $return = array(
				'ask' => 'Failure',
				'message' => '',
				'reference_no' => $orderInfo ['reference_no'],
        );

        $process = new Process_Order();
        try{
            $errors = array();
            if(empty($orderInfo['reference_no'])){
                throw new Exception(Ec::Lang('param_can_not_empty', 'reference_no'),5000);
            }
            $orderDetail = $orderInfo['ItemArr'];
            if(empty($orderDetail) || ! is_array($orderDetail)){
                throw new Exception(Ec::Lang('param_can_not_empty', 'ItemArr'),5000);
            } 
            $consignee = $orderInfo['Consignee'];
            $shipper = $orderInfo['Shipper'];
            if(empty($consignee) || ! is_array($consignee)){
                throw new Exception(Ec::Lang('param_can_not_empty', 'Consignee'),5000);
            } 
            if(empty($shipper) || ! is_array($shipper)){
                throw new Exception(Ec::Lang('param_can_not_empty', 'Shipper'),5000);
            } 
            $order = $orderInfo;
            $extra_service = $order['extra_service']; 
			if($extra_service){
				//特殊符号替换
				$extra_service = preg_replace('/[，,；;、。\.:!@#￥\$%\^&\*\|]+/', ';', $extra_service);
				$extra_service = preg_replace('/\s+/', '', $extra_service);
				$extra_service = trim($extra_service);
				$extra_service = trim($extra_service,';');	
							
			}
// 			print_r($extra_service);exit;
			if($extra_service){
				$extra_service = explode(';', $extra_service);
			}else{
				$extra_service = array();
			}
            $orderArr = array(
            		'product_code' => strtoupper($order['shipping_method']),
            		'country_code' => strtoupper($order['country_code']),
            		'refer_hawbcode' => strtoupper($order['reference_no']),
             		'shipper_hawbcode' => strtoupper($order['shipper_hawbcode']),
            		'server_hawbcode' => strtoupper($order['shipping_method_no']),
            		'order_weight' => $order['order_weight'],
            		'order_pieces' => $order['order_pieces'],
            		'buyer_id' =>$order['buyer_id'],
            		'order_create_code'=>'a',
            		'customer_id'=>Service_User::getCustomerId(),
            		'creater_id'=>Service_User::getUserId(),
            		'modify_date'=>date('Y-m-d H:i:s'),
            		'mail_cargo_type' => $order['mail_cargo_type'],
            		'tms_id'=>Service_User::getTmsId(),
            		'customer_channelid'=>Service_User::getChannelid(),
            		//投保金额
            		'insurance_value'=>$order['insurance_value'],
            );
            foreach($orderArr as $k=>$v){
            	if(!isset($v)){
            		$v = '';
            	}
            	$orderArr[$k] = $v;
            }
            $invoiceArr = array();
            foreach($orderDetail as $row){
            	$ivs = array(
            			'invoice_enname' => $row['invoice_enname'],
            			'invoice_cnname' => $row['invoice_cnname'],
            			'unit_code' => empty($row['unit_code']) ? 'PCE' : $row['unit_code'],
            			'invoice_quantity' => $row['invoice_quantity'],
            			'invoice_weight' => $row['invoice_weight'],
            			'invoice_totalWeight' => $row['invoice_weight']*$row['invoice_quantity'],
            			'invoice_unitcharge'=>$row['invoice_unitcharge'],
            			'invoice_totalcharge' => $row['invoice_unitcharge']*$row['invoice_quantity'],
            			'invoice_currencycode' => empty($row['invoice_currencycode']) ? 'USD' : $row['invoice_currencycode'],'',
            			'hs_code' => $row['hs_code'],
            			'invoice_note' => $row['invoice_note'],
            			'invoice_url' => $row['invoice_url'],
            			'sku' => $row['sku']
            	);
	            foreach($ivs as $k=>$v){
	            	if(!isset($v)){
	            		$v = '';
	            	}
	            	$ivs[$k] = $v;
	            }
            	$invoiceArr[] = $ivs;
            }
            
            $consigneeArr = array(
            		'consignee_countrycode' => strtoupper($order['country_code']),
            		'consignee_company' => $consignee['consignee_company'],
            		'consignee_province' => $consignee['consignee_province'],
            		'consignee_name' => $consignee['consignee_name'],
            		'consignee_city' => $consignee['consignee_city'],
            		'consignee_telephone' => $consignee['consignee_telephone'],
            		'consignee_mobile' => $consignee['consignee_mobile'],
            		'consignee_postcode' => $consignee['consignee_postcode'],
            		'consignee_email' => $consignee['consignee_email'],
            		'consignee_street' => $consignee['consignee_street'],
            		'consignee_street2' => $consignee['consignee_street2'],
            		'consignee_street3' => $consignee['consignee_street3'],
            		'consignee_certificatetype' => $consignee['consignee_certificatetype'],
            		'consignee_certificatecode' => $consignee['consignee_certificatecode'],
            		'consignee_credentials_period' => $consignee['consignee_credentials_period'],
            		'consignee_doorplate' => $consignee['consignee_doorplate'],
            		'consignee_taxno' => $consignee['consignee_taxno'],
            );
            foreach($consigneeArr as $k=>$v){
            	if(!isset($v)){
            		$v = '';
            	}
            	$consigneeArr[$k] = $v;
            }
            $shipperArr = array(
            		// 'shipper_account' => $v['shipper_account'],
            		'shipper_name' => $shipper['shipper_name'],
            		'shipper_company' => $shipper['shipper_company'],
            		'shipper_countrycode' => $shipper['shipper_countrycode'],
            		'shipper_province' => $shipper['shipper_province'],
            		'shipper_city' => $shipper['shipper_city'],
            		'shipper_street' => $shipper['shipper_street'],
            		'shipper_postcode' => $shipper['shipper_postcode'],
            		'shipper_areacode' => $shipper['shipper_areacode'],
            		'shipper_telephone' => $shipper['shipper_telephone'],
            		'shipper_mobile' => $shipper['shipper_mobile'],
            		'shipper_email' => $shipper['shipper_email'],
            		'shipper_fax' => $shipper['shipper_fax']
            );

            foreach($shipperArr as $k=>$v){
            	if(!isset($v)){
            		$v = '';
            	}
            	$shipperArr[$k] = $v;
            }
            $process->setOrder($orderArr);
            $process->setInvoice($invoiceArr);
            $process->setExtraservice($extra_service);       
            $process->setShipper($shipperArr);          
            $process->setConsignee($consigneeArr); 
            
//             $process
            $rs = $process->createOrderTransaction('P'); 
//             $return['rs'] = $rs; 
//             $apiErr = $process->getApiErr();
            if($rs['ask']){
	            $return['ask'] = 'Success';
	            $return['message'] = 'Success'; 
	            $return['shipping_method_no'] = $rs['order']['server_hawbcode'];
	            $return['order_code'] = $rs['order']['shipper_hawbcode'];
            }else{
            	if(!empty($rs['err'])) {
            		$return['Error'] = array(
            				'errMessage' => implode(", ", $process->getErrs()),
            				'errCode' => implode(", ", $process->getApiErr())
            		);
            	}
            	
            	$return['message'] = $rs['message'];
            } 
            //$return['rs'] = $rs;
        }catch(Exception $e){ 
            $return['ask'] = 'Failure';
            $return['message'] = $e->getMessage();
            $return['Error'] = array(
                'errMessage' => $e->getMessage(),
                'errCode' => $e->getCode()
            );
        }
        
        return $return;
    }
    

    /**
     * 批量创建订单
     *
     * @param unknown_type $orderInfo
     * @param unknown_type $returnType
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function batchCreateOrder($batchOrderInfo)
    {
    	// 批量创建订单数据
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    			'Result' => array(),
    			'Error' => array()
    	);
    	
    	if(empty($batchOrderInfo) || !is_array($batchOrderInfo)) {
    		$return['Error']  = array(
                'errMessage' => "无订单数据或格式不正确",
                'errCode' => ""
            );
    		
    		return $return;
    	}
    	
    	foreach($batchOrderInfo as $orderInfo) {
    		$return['Result'][] = $this->createOrder($orderInfo);
    	}
    	
    	$return['ask'] = 'Success';
    	$return['message'] = 'Success';
    	return $return;
    }

    /**
     * 修改订单
     * 
     * @param unknown_type $orderInfo            
     * @return Ambigous <multitype:Ambigous, multitype:string NULL
     *         multitype:NULL Ambigous <unknown_type, string> >
     */
    public function modifyOrder($orderInfo)
    {
        return $this->createOrder($orderInfo);
    }
    
    /**
     * 截单
     * 
     * @param unknown_type $refIds            
     * @param unknown_type $returnType            
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function cancelOrder($orderInfo)
    {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    			'reference_no' => $orderInfo ['reference_no'],
    	);
    	
    	$process = new Process_Order();
    	try{
    		$errors = array();
    		if(empty($orderInfo['reference_no'])){
    			throw new Exception(Ec::Lang('param_can_not_empty', 'reference_no'), 5000);
    		}
    		
    		// 查询订单
    		$condition = array('shipper_hawbcode' => $orderInfo['reference_no'], 'customer_id' => Service_User::getCustomerId());
    		$order = Service_CsdOrder::getByCondition($condition,'*',1,1,null);
    		if(empty($order)) {
    			// 客户单号
    			$condition = array('refer_hawbcode' => $orderInfo['reference_no'], 'customer_id' => Service_User::getCustomerId());
    			$order = Service_CsdOrder::getByCondition($condition,'*',1,1,null);
    			if(empty($order)) {
    				throw new Exception(Ec::Lang('order_not_exist'), 5000);
    			}
    		}
    	
    		// 删除订单
    		$rs = $process->verifyOrderBatchTransaction(array($order[0]['order_id']), "discard");
    		if($rs['ask']){
    			$return['ask'] = 'Success';
    			$return['message'] = 'Success';
    		} else {
    			
    			if(!empty($rs['err'])) {
    				$return['Error'] = array(
    						'errMessage' => implode(", ", $process->getErrs()),
    						'errCode' => implode(", ", $process->getApiErr())
    				);
    			}
    			
    			$return['message'] = $rs['message'];
    		}
    		
    	}catch(Exception $e){
    		$return['ask'] = 'Failure';
    		$return['message'] = $e->getMessage();
    		$return['Error'] = array(
    				'errMessage' => $e->getMessage(),
    				'errCode' => $e->getCode()
    		);
    	}
    	
    	return $return;
    }
    

    /**
     * 截单
     *
     * @param unknown_type $refIds
     * @param unknown_type $returnType
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function interceptOrder($orderInfo)
    {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    			'reference_no' => $orderInfo ['reference_no'],
    	);
    	 
    	$process = new Process_Order();
    	try{
    		$errors = array();
    		if(empty($orderInfo['reference_no'])){
    			throw new Exception(Ec::Lang('param_can_not_empty', 'reference_no'), 5000);
    		}
    
    		// 查询订单
    		$condition = array('shipper_hawbcode' => $orderInfo['reference_no'], 'customer_id' => Service_User::getCustomerId());
    		$order = Service_CsdOrder::getByCondition($condition,'*',1,1,null);
    		if(empty($order)) {
    			throw new Exception(Ec::Lang('order_not_exist'), 5000);
    		}
    		 
    		// 截单
    		$rs = $process->verifyOrderBatchTransaction(array($order[0]['order_id']), "intercept");
    		if($rs['ask']){
    			$return['ask'] = 'Success';
    			$return['message'] = 'Success';
    		}else{
    			$return['message'] = $rs['message'];
    		}
    
    	}catch(Exception $e){
    		$return['ask'] = 'Failure';
    		$return['message'] = $e->getMessage();
    		$return['Error'] = array(
    				'errMessage' => $e->getMessage(),
    				'errCode' => $e->getCode()
    		);
    	}
    	 
    	return $return;
    }

    /**
     * 批量获取订单信息
     * 
     * @param unknown_type $refIds            
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getOrderList($param)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $pageSize = $param['pageSize'];
            $page = $param['page'];
            
            if(! isset($pageSize) || ! preg_match('/^[0-9]+$/', $pageSize) || intval($pageSize) == 0){
                throw new Exception('param pageSize error');
            }
            if(intval($pageSize) > 100){
                $pageSize = 100;
            }
            if(! isset($page) || ! preg_match('/^[0-9]+$/', $page) || intval($page) == 0){
                throw new Exception('param page error');
            }
            $return['pagination'] = array(
                'page' => $page,
                'pageSize' => $pageSize
            );
            $con = array(
                'company_code' => $this->_companyCode,
                'refrence_no_platform' => $param['order_code'],
                'refrence_no_platform_arr' => $param['order_code_arr'],
                'create_date_from' => $param['create_date_from'],
                'create_date_to' => $param['create_date_to'],
                'modify_date_from' => $param['modify_date_from'],
                'modify_date_to' => $param['modify_date_to']
            );
            // 如果传入了订单号 时间参数无效 start
            if(! empty($con['refrence_no_platform']) || ! empty($con['refrence_no_platform'])){
                unset($con['create_date_from']);
                unset($con['create_date_to']);
                unset($con['modify_date_from']);
                unset($con['modify_date_to']);
            }
            // 如果传入了订单号 时间参数无效 end
            $field = array(
                'order_id',
//                 'company_code',
                'refrence_no_platform as order_code',
                'refrence_no as reference_no',
                'platform',
                'order_status', // 订单状态2：待发货审核，3待发货，4已发货，0：作废，
                                // 'order_type',
                                // 'sub_status',
                                // 'cancel_status',
                                // 'create_method',
                'shipping_method',
                'shipping_method_no as tracking_no',
//                 'warehouse_id',
                'warehouse_code',
                'order_weight',
                'order_desc',
                
                'date_create',
                'date_release',
                'date_warehouse_shipping as date_shipping',
                'date_last_modify as date_modify',
                
                // 'abnormal_type',
                // 'abnormal_reason',
                // 'is_one_piece',
                // 'product_count',
                
                'consignee_country_code',
                'consignee_country_name',
                'consignee_state',
                'consignee_city',
                'consignee_district',
                'consignee_street1 as consignee_address1',
                'consignee_street2 as consignee_address2',
                'consignee_street3 as consignee_address3',
                'consignee_postal_code as consigne_zipcode',
                'consignee_doorplate',
                'consignee_company',
                'consignee_name',
                
                'consignee_phone',
                'consignee_email'
            );
            // $field = '*';
            $count = Service_Orders::getByCondition($con, 'count(*)', $pageSize, $page);
            $return['nextPage'] = $pageSize * $page < $count ? 'true' : 'false';
            $results = Service_Orders::getByCondition($con, $field, $pageSize, $page);
            if(empty($results)){
                // throw new Exception(Ec::Lang('no_data'));
            }
            foreach($results as $k => $v){
                // 订单费用 start
                $field = array(
                    'ref_id as order_code',
                    // 'order_status',
                    'shipping_method',
                    'country_code',
                    'order_weight', // 订单重量 单位KG
                    'ship_cost', // 运费
                    'op_cost', // 操作费
                    'fuel_cost', // 燃油附加费
                    'register_cost', // 挂号费
                    'warehouse_cost', // 仓储费
                    'tariff_cost', // 关税
                    'incidental_cost' // 其他费用
                )
                ;
                $feeSummery = Service_OrderFeeSummery::getByField($v['order_code'], 'ref_id', $field);
                $feeSummery['currency_code'] = 'RMB';
                $v['fee_summery'] = $feeSummery;
                
                $con = array(
                    'ref_id' => $v['order_code']
                );
                $field = array(
                    'ft_code as type',
                    'bi_amount as amount',
                    'currency_code'
                );
                $orderFee = Service_OrderFee::getByCondition($con, $field);
//                 $v['order_fee'] = $orderFee;
                // 订单费用 end
                
                // 订单产品 start
                $con = array(
                    'order_id' => $v['order_id']
                );
                // 去除order_id
                unset($v['order_id']);
                $field = array(
//                     'product_id',
                    'product_sku',
                    'op_quantity as quantity'
                );
                $products = Service_OrderProduct::getByCondition($con, $field);
                $v['items'] = $products;
                // 订单产品 end
                $results[$k] = $v;
            }
            
            $return['count'] = $count;
            $return['data'] = $results;
            $return['ask'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 费用试算
     * 
     * @param unknown_type $param            
     * @throws Exception
     * @return multitype:string NULL Ambigous <multitype:Ambigous, Ambigous,
     *         string>
     */
    public function feeTrail($param)
    {
		$return = array (
				'ask' => 'Failure',
				'message' => '' 
		);
		try {
			
			$timeout = 1000;
			$options = array (
					"trace" => true,
					"connection_timeout" => $timeout,
					// "exceptions" => true,
					// "soap_version" => SOAP_1_1,
					// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
					// "stream_context" => $streamContext,
					"encoding" => "utf-8" 
			);
            $wsdl = Service_Config::getByField('TMS_FEE_TRAIL_WSDL', 'config_attribute');
            if(!$wsdl){
            	$wsdl = 'http://120.24.63.108:9001/APIServicesDelegate?wsdl';
            	//throw new Exception('TMS_FEE_TRAIL_WSDL not configration');
            }else{
            	$wsdl = $wsdl['config_value'];
            }           
			
			$client = new SoapClient ( $wsdl, $options );
			 
			$weight = $param['weight'];
			$country_code = $param['country_code'];
			$org_area = '';
			$length = $param['length'];
			$width = $param['width'];
			$height =  $param['height'];
			$cargo_type = "";

// 			$weight = '2';
			
			$arr = array (
					'strTms_id' => Service_User::getTmsId (),
					'strCustomer_id' => Service_User::getCustomerId (),
					'strWeight' => $weight,
					'strCountry_code' => $country_code,
					'strOg_id_pickup' => $org_area,
					'strLength' => $length,
					'strWidth' => $width,
					'strHeight' => $height,
					'strCargo_type' => $cargo_type 
			);
			try {
				// $rs =
				// $client->AttemptCalculate($tms_id,$customer_id,$weight,$country_code,$org_area,$length,$width,$height,$cargo_type);
				$rs = $client->AttemptCalculate ( $arr );
				$json = $rs->AttemptCalculateResult;
				$data = json_decode ( $json, true );
				if($data){
					$return ['ask'] = 'Success';
					$return ['message'] = 'Success';
					$return ['data'] = $data;
				}else{
					throw new Exception('系统暂未支持');
				}
				
// 				$return['req'] = $arr;
			} catch ( Exception $e ) {
				throw new Exception ( "试算失败." );
			}
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
		}
		return $return;
	}
	
	/**
	 * 获取附加服务
	 */
	public function getExtraService($params = array())
	{
		$return = array(
				'ask' => 'Failure',
				'message' => ''
		);
		try{
			$con = array();
			$shipping_method =  $params['shipping_method'];
			$country_code =  $params['country_code'];
			if(empty($shipping_method)){
				throw new Exception('运输方式不可为空');
			}
			if(empty($country_code)){
				throw new Exception('目的国家不可为空');
			}
			$serve_kind_arr = Process_ProductRule::optionalServeType($shipping_method, $country_code);
			foreach($serve_kind_arr as $k=>$v){
				$serve_kind_arr [$k] = array (
						'extra_service_kind' => $v ['extra_service_kind'],
						'extra_service_cnname' => $v ['extra_service_cnname'],
						'extra_service_enname' => $v ['extra_service_enname'],
						'extra_service_group' => $v ['extra_service_group'],
						'extra_service_note' => $v ['extra_service_note'], 
				);
			}
			// 特殊处理 end
			$return['data'] = $serve_kind_arr;
			$return['ask'] = 'Success';
			$return['message'] = 'Success';
		}catch(Exception $e){
			$return['ask'] = 'Failure';
			$return['Error'] = array(
					'errMessage' => $e->getMessage(),
					'errCode' => $e->getCode()
			);
		}
	
		return $return;
	}
	
	public function cargoTrackingService($params){
        $start = microtime_float(); 
		try {
			
			$timeout = 1000;
			$wsdl = Service_Config::getByField ( 'TMS_FEE_TRAIL_WSDL', 'config_attribute' );
			if (! $wsdl) {
				$wsdl = 'http://120.24.63.108:9001/APIServicesDelegate?wsdl';
				// throw new Exception('TMS_FEE_TRAIL_WSDL not configration');
			} else {
				$wsdl = $wsdl ['config_value'];
			}
			$options = array (
					"trace" => true,
					"connection_timeout" => $timeout,
				    "location" => $wsdl,
					// "exceptions" => true,
					// "soap_version" => SOAP_1_1,
					// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
					// "stream_context" => $streamContext,
					"encoding" => "utf-8" 
			);
			
			$client = new SoapClient ( $wsdl, $options );
			$params = Common_Common::objectToArray ( $params );
			$arr = array (
					'clientFlag' => $params ['clientFlag'],
					'json' => $params ['json'],
					'verifyData' => $params ['verifyData'] 
			);
			try {
				$rs = $client->CargoTrackingService ( $arr );
				$rs = Common_Common::objectToArray ( $rs );
				$CargoTrackingServiceResult = Zend_Json::decode($rs['CargoTrackingServiceResult']);
				$end = microtime_float();
				$CargoTrackingServiceResult['Timestamp'] = (($end-$start)*1000);
				$return = array (
						'response' => Zend_Json::encode($CargoTrackingServiceResult)
				);
				Ec::showError ( print_r ( $params, true ) . print_r ( $return, true ), 'cargo1_' );
			} catch ( Exception $e ) {
				throw new Exception ( "调用失败." );
			}
		} catch ( Exception $e ) {
			$end = microtime_float();
			$return = array (
					'response' => '{"ErrorCode":"50000","Msg":"' . $e->getMessage () . '","Timestamp":"'.(($end-$start)*1000).'"}' 
			);
		}
		return $return;
	}
	
	public function cargoTrackingService1($param = array()){

		$return = array (
				'ask' => 'Failure',
				'message' => ''
		);
        $start = microtime_float(); 
		try {
				
			$timeout = 1000;
			$options = array (
					"trace" => true,
					"connection_timeout" => $timeout,
					// "exceptions" => true,
					// "soap_version" => SOAP_1_1,
					// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
					// "stream_context" => $streamContext,
					"encoding" => "utf-8"
			);
			$wsdl = Service_Config::getByField('TMS_FEE_TRAIL_WSDL', 'config_attribute');
			if(!$wsdl){
				$wsdl = 'http://120.24.63.108:9001/APIServicesDelegate?wsdl';
				//throw new Exception('TMS_FEE_TRAIL_WSDL not configration');
			}else{
				$wsdl = $wsdl['config_value'];
			}
				
			$client = new SoapClient ( $wsdl, $options );
		 
			$arr = array (
					'clientFlag' => $param['clientFlag'],
					'json' => $param['json'],
					'verifyData' => $param['verifyData'], 
			);
			try { 
				$rs = $client->CargoTrackingService ( $arr );
				$return = $rs;
				$end = microtime_float();
				$return['time_cost(s)'] = $end-$start;
			} catch ( Exception $e ) {
				throw new Exception ( "试算失败." );
			}
		} catch ( Exception $e ) {
			$return ['message'] = $e->getMessage ();
			$end = microtime_float();
			$return['time_cost(s)'] = $end-$start;
		}
		return $return;
		
	}
	
	/**
	 * 获取轨迹
	 */
	public function getCargoTrack($params = array())
	{
		$return = array(
				'ask' => 'Failure',
				'message' => '',
				'Data' => array(),
				'Error' => array()
		);
		try{
			// 获取请求单号
			$orders = $params['codes'];
			if(empty($orders)) {
				$return['ask'] = 'Failure';
				$return['Error'] = array(
						'cnMessage' => "单号不能为空",
						'enMessage' => "Order Code. can not be empty"
				);
				
				return $return;
			} 
			
			$data = array();
			// 获取数据
			foreach($orders as $server_hawbcode) {
				$rs = Process_Track::getTrackDetailForApi($server_hawbcode);
				$data[] = $rs;
			}
			
			$return['Data'] = $data;
			$return['ask'] = 'Success';
			$return['message'] = 'Success';
		} catch(Exception $e){
			$return['ask'] = 'Failure';
			$return['Error'] = array(
					'cnMessage' => "异常：" . $e->getMessage(),
					'enMessage' => "Exception：" . $e->getMessage()
			);
		}
	
		return $return;
	}

	/**
	 * 获取订单数据
	 * @param unknown_type $refIds
	 * @param unknown_type $returnType
	 * @throws Exception
	 * @return multitype:Ambigous <string, mixed>
	 */
	public function getOrder($orderInfo)
	{
		$return = array(
				'ask' => 'Failure',
				'message' => '',
				'reference_no' => $orderInfo ['reference_no'],
		);
		 
		try{
			
			if(empty($orderInfo['reference_no'])){
				throw new Exception(Ec::Lang('param_can_not_empty', 'reference_no'), 5000);
			}
	
			// 查询订单
			$condition = array('shipper_hawbcode' => $orderInfo['reference_no'], 'customer_id' => Service_User::getCustomerId());
			$order = Service_CsdOrder::getByCondition($condition,'*',1,1,null);
			if(empty($order)) {
				$return['ask'] = 'Failure';
				$return['message'] = '订单不存在';
				return $return;
			}
			
			$order_id = $order[0]['order_id'];
			// 订单数据
			$order = $order[0];
			
			$orderArr = array(
					'shipping_method' => strtoupper($order['product_code']),
					'country_code' => strtoupper($order['country_code']),
					'reference_no' => strtoupper($order['refer_hawbcode']),
					'shipper_hawbcode' => strtoupper($order['shipper_hawbcode']),
					'shipping_method_no' => strtoupper($order['server_hawbcode']),
					'order_weight' => $order['order_weight'],
					'order_pieces' => $order['order_pieces'],
					'buyer_id' =>$order['buyer_id'],
					'modify_date'=>$order['modify_date'],
					'mail_cargo_type' => $order['mail_cargo_type'],
					'insurance_value'=>$order['insurance_value'],
			);
			
			// 查询收件人数据
			$shipperConsignee = Service_CsdShipperconsignee::getByField($order_id);
			if(!empty($shipperConsignee)) {
				// 收件人
				$consignee = array(
						'consignee_countrycode' => $order['country_code'],
						'consignee_company' => $shipperConsignee['consignee_company'],
						'consignee_province' => $shipperConsignee['consignee_province'],
						'consignee_name' => $shipperConsignee['consignee_name'],
						'consignee_city' => $shipperConsignee['consignee_city'],
						'consignee_telephone' => $shipperConsignee['consignee_telephone'],
						'consignee_mobile' => $shipperConsignee['consignee_mobile'],
						'consignee_postcode' => $consignee['consignee_postcode'],
						'consignee_email' => $shipperConsignee['consignee_email'],
						'consignee_street' => $shipperConsignee['consignee_street'],
						'consignee_street2' => $shipperConsignee['consignee_street2'],
						'consignee_street3' => $shipperConsignee['consignee_street3'],
						'consignee_certificatetype' => $shipperConsignee['consignee_certificatetype'],
						'consignee_certificatecode' => $shipperConsignee['consignee_certificatecode'],
						'consignee_credentials_period' => $shipperConsignee['consignee_credentials_period'],
						'consignee_doorplate' => $shipperConsignee['consignee_doorplate'],
						'consignee_taxno' => $shipperConsignee['consignee_taxno'],
				);
				
				// 发件人
				$shipper = array(
						// 'shipper_account' => $v['shipper_account'],
						'shipper_name' => $shipperConsignee['shipper_name'],
						'shipper_company' => $shipperConsignee['shipper_company'],
						'shipper_countrycode' => $shipperConsignee['shipper_countrycode'],
						'shipper_province' => $shipperConsignee['shipper_province'],
						'shipper_city' => $shipperConsignee['shipper_city'],
						'shipper_street' => $shipperConsignee['shipper_street'],
						'shipper_postcode' => $shipperConsignee['shipper_postcode'],
						'shipper_areacode' => $shipperConsignee['shipper_areacode'],
						'shipper_telephone' => $shipperConsignee['shipper_telephone'],
						'shipper_mobile' => $shipperConsignee['shipper_mobile'],
						'shipper_email' => $shipperConsignee['shipper_email'],
						'shipper_fax' => $shipperConsignee['shipper_fax']
				);
				
				$orderArr['Consignee'] = $consignee;
				$orderArr['Shipper'] = $shipper;
			}
			
			// 查询申报信息
			$invoice = Service_CsdInvoice::getByCondition(array('order_id' => $order_id));
			$itemArr = array();
			foreach ($invoice as $k => $row) {
				unset($row['order_id']);
				unset($row['invoice_id']);
				$itemArr[$k] = $row;
			}
			
			$orderArr['ItemArr'] = $itemArr;
			
			$return['data'] = $orderArr;
			$return['ask'] = 'Success';
			$return['message'] = 'Success';
		}catch(Exception $e){
			$return['ask'] = 'Failure';
			$return['message'] = $e->getMessage();
			$return['Error'] = array(
					'errMessage' => $e->getMessage(),
					'errCode' => $e->getCode()
			);
		}
		 
		return $return;
	}

	/**
	 * 修改订单预报重量
	 * @param unknown_type $refIds
	 * @param unknown_type $returnType
	 * @throws Exception
	 * @return multitype:Ambigous <string, mixed>
	 */
	public function modifyOrderWeight($orderWeightInfo)
	{
		$return = array(
				'ask' => 'Failure',
				'message' => '',
				'Result' => array(),
		);
		
		$obj = new Process_Order();
		
		foreach($orderWeightInfo as $k => $row) {
			$result_row = array();
			$result_row['order_code'] = $row['order_code']; 
			$result = $obj->_editWeightTransaction($row['order_code'], $row['weight']);
			if($result['ask'] == 0) {
				$result_row['ask'] = 'Failure';
				$result_row['Error'] = array(
					'errMessage' => $result['message'],
					'errCode' => ""
				);
			} else {
				$result_row['ask'] = 'Success';
				$result_row['message'] = 'Success';
			}
			
			$return['Result'][] = $result_row;
		}
		
		return $return;
	}

	/**
	 * 获取标签URL
	 * @throws Exception
	 */
	public function getLabelUrl($orderInfo) {
		// echo "1->"; print_r(date('Y-m-d H:i:s:S'));
		$return = array(
				'ask' => 'Failure',
				'message' => '',
				'Error' => array(),
				'type' => 'pdf',
				'url' => '',
		);
		
		// 客户参考号
		$refer_hawbcode = $orderInfo['reference_no'];
		$LablePaperType = $orderInfo['lable_type'];
		if(empty($refer_hawbcode)) {
			$return['Error'] = array('errCode' => '', 'errMessage' => "单号不能为空");
			return $return;
		}
		
		try {
			
			// 客户参考号查询
			$order = Service_CsdOrder::getByField($refer_hawbcode, 'refer_hawbcode');
			if(empty($order)) {
				// 运单号查
				$order = Service_CsdOrder::getByField($refer_hawbcode, 'shipper_hawbcode');
				if(empty($order)) {
					$return['Error'] = array('errCode' => '', 'errMessage' => "订单不存在");
					return $return;
				}
			}
			
			// 客户不匹配
			if($order['customer_id'] != Service_User::getCustomerId()) {
				$return['Error'] = array('errCode' => '', 'errMessage' => "客户不匹配");
				return $return;
			}
			
			// 取产品配置
			$product_rule = Service_PbrProductrule::getByField($order['product_code'], 'product_code');
			// 未配置标签
			if(empty($product_rule['label_config_id'])) {
				$return['Error'] = array('errCode' => '', 'errMessage' => "未配置标签");
				return $return;
			}
			$db = Common_Common::getAdapterForDb2();
			
			// 标签配置
			$label_config_id = $product_rule['label_config_id'];
			
			// 标签模板类型
			$sql = "select * from pbr_label_config where label_config_id='{$label_config_id}';";
			$labelConfig = $db->fetchRow($sql);
			if($labelConfig['atd_label_code'] == 'API'){
				$return['Error'] = array('errCode' => '', 'errMessage' => "未配置标签");
				return $return;
			}
			
			$configInfo = array(
					// 标签文件类型，参照标签文件类型
					// 1 PNG文件
					// 2 PDF文件
					'LableFileType' => 2,
					// 标签纸张类型，参照标签纸张类型
					// 1 标签纸张
					// 2 A4纸张/ A4不干胶纸张
					'LablePaperType' => $LablePaperType,
					// 标签内容类型，参照标签内容类型
					// 1 标签
					// 2 报关单
					// 3 配货单
					// 4 标签+报关单
					// 5 标签+配货单
					// 6 标签+报关单+配货单
					'LableContentType' => 1
			);
				
				// PDF 打印信息
			$pdfPrintInfo = array ();
			
			$orderInfoArr = array ();
			$countrys = Common_DataCache::getCountry ();

			$order_id =  $order['order_id'];
			
			// 历史数据 start
			$con = array (
				'order_id' => $order_id 
			);
			$invoice = Service_CsdInvoice::getByCondition ( $con, '*', 0, 0, 'invoice_id asc' );
			if (empty ( $invoice )) {
				throw new Exception ( Ec::Lang ( '申报信息不存在' ) );
			}
			
			foreach ( $invoice as $k => $v ) {
				$v ['invoice_unitcharge'] = $v ['invoice_quantity'] ? ($v ['invoice_totalcharge'] / $v ['invoice_quantity']) : 0;
				$invoice [$k] = $v;
			}
			
			// 额外服务 -- 开始
            $extraserviceKindArr = Service_AtdExtraserviceKind::getAll();
            $extraserviceKindByKey = array();
            foreach($extraserviceKindArr as $k => $row) {
                $extraserviceKindByKey[$row['extra_service_kind']] = $row;
            }
                	
            $extservice = Service_CsdExtraservice::getByCondition ( $con );
            $extservice_str = array ();
            foreach ( $extservice as $v ) {
                $extservice_str [] = isset($extraserviceKindByKey[$v ['extra_servicecode']]) ? $extraserviceKindByKey[$v ['extra_servicecode']]['extra_service_cnname'] : $v ['extra_servicecode'];
                	
                // 判断是否保险
                if($row['extra_servicecode'] == 'CO') {
                	$InsuranceSign = 'Y';
                } else {
                	$extraserviceKind = $extraserviceKindByKey[$v ['extra_servicecode']];
                	if(!empty($extraserviceKind) && $extraserviceKind['extra_service_group'] == 'C0') {
                		$InsuranceSign = 'Y';
                	}
                }
            }
            // 额外服务 -- 结束
            
			$shipperConsignee = Service_CsdShipperconsignee::getByField ( $order_id, 'order_id' );
			if (! $shipperConsignee) {
				throw new Exception ( Ec::Lang ( '收发件人信息不存在' ) );
			}
				
				$sql = "select * from pbr_public_shipper_address where product_code='{$order['product_code']}' and (country_code='{$order['country_code']}' or country_code='' or country_code is null) order by country_code desc;";
				$public_shipper_address = $db->fetchRow ( $sql );
				if ($public_shipper_address) { // 如果有公共发件人，取公共发件人信息
					$tmp = array (
							'server_channelid' => $public_shipper_address ['server_channelid'],
							'country_code' => $public_shipper_address ['country_code'],
							'shipper_account' => $public_shipper_address ['shipper_account'],
							'shipper_name' => $public_shipper_address ['shipper_name'],
							'shipper_company' => $public_shipper_address ['shipper_company'],
							'shipper_countrycode' => $public_shipper_address ['shipper_countrycode'],
							'shipper_province' => $public_shipper_address ['shipper_province'],
							'shipper_city' => $public_shipper_address ['shipper_city'],
							'shipper_street' => $public_shipper_address ['shipper_street'],
							'shipper_postcode' => $public_shipper_address ['shipper_postcode'],
							'shipper_areacode' => $public_shipper_address ['shipper_areacode'],
							'shipper_telephone' => $public_shipper_address ['shipper_telephone'],
							'shipper_mobile' => $public_shipper_address ['shipper_mobile'],
							'shipper_email' => $public_shipper_address ['shipper_email'],
							'shipper_certificatetype' => $public_shipper_address ['shipper_certificatetype'],
							'shipper_certificatecode' => $public_shipper_address ['shipper_certificatecode'],
							'shipper_fax' => $public_shipper_address ['shipper_fax'],
							'shipper_mallaccount' => $public_shipper_address ['shipper_mallaccount'] 
					);
					$shipperConsignee = array_merge ( $shipperConsignee, $tmp );
				}

				$productKind = Service_CsiProductkind::getByField ( $order ['product_code'], 'product_code' );
				$productRule = Service_PbrProductrule::getByField ( $order ['product_code'], 'product_code' );
				$label_config_id = $productRule ['label_config_id'];

				// 标签模板类型
				$sql = "select * from pbr_label_config where label_config_id='{$label_config_id}';";
				$labelConfig = $db->fetchRow ( $sql );
				if (! $labelConfig || empty ( $labelConfig ['atd_label_code'] )) {
					throw new Exception ( $order ['product_code'] . Ec::Lang ( '找不到标签模板类型' ) );
				}
				
				// 查找对应的PDF类型
				$sql = "select * from pbr_label_type where label_config_id='{$label_config_id}' and print_type = '{$LablePaperType}';";
				$pdfLabelConfig = $db->fetchRow ( $sql );
				if (empty ( $pdfLabelConfig )) {
					throw new Exception ( $order ['product_code'] . Ec::Lang ( '找不到PDF标签模板类型' ) );
				}
				
				$pdfPrintInfo = array (
						// 标签模板名称
						'LabelName' => $pdfLabelConfig ['label_name'],
						// 报关单模板名称
						'CustomName' => $pdfLabelConfig ['custom_name'],
						// 配货单模板名称
						'InvoiceName' => $pdfLabelConfig ['invoice_name']
				);
				
				$atd_label_code = $labelConfig ['atd_label_code'];
				$returnAdd = null;
				if ($labelConfig ['return_address_type'] == 'Y') {
					// 回邮地址
					$sql = "select * from pbr_return_address where label_config_id='{$label_config_id}' and country_code='{$order['country_code']}';";
					$returnAdd = $db->fetchRow ( $sql );
					if (! $returnAdd) {
						$sql = "select * from pbr_return_address where label_config_id='{$label_config_id}' order by rand();"; // 随机取一条
						$returnAdd = $db->fetchRow ( $sql );
					}
				}
				$arrivalZone = null;
				if ($labelConfig ['arrival_zone_type'] == 'Y') {
					$sql = "
					SELECT
					*
					FROM
					pbr_arrival_zone
					WHERE
					label_config_id = '{$label_config_id}'
					AND (
					country_code = '{$order['country_code']}'
					OR country_code = ''
					OR country_code IS NULL
					)
					AND (
					(
					ct_startpostcode <= '{$shipperConsignee['consignee_postcode']}'
					AND ct_endpostcode >= '{$shipperConsignee['consignee_postcode']}'
					)
					OR (
					ct_startpostcode = ''
					OR ct_startpostcode IS NULL
					)
					)
					AND (
					country_city = :country_city
					OR country_city = ''
					OR country_city IS NULL
					)
					order by  country_city desc
					";
					$r = $db->query ( $sql, array (
							'country_city' => $shipperConsignee ['consignee_city'] 
					) );
					// echo $r;exit;
					$arrivalZone = $r->fetchAll ();
					if ($arrivalZone) {
						$arrivalZone = array_shift ( $arrivalZone );
					}
				}
                
				$orderInfo = array (
						'LabelTemplateType' => strtoupper ( $atd_label_code ),
						// 客户代码
						'CustomerCode' => '' . Service_User::getCustomerCode (),
						// 客户订单号码，由客户自己定义
						'OrderNo' => $order ['shipper_hawbcode'],
						// 销售产品/运输方式代码
						'ProductCode' => $order ['product_code'],
						// 销售产品/运输方式名称
						'ProductName' => $productKind ['product_cnname'] . '',
						// 产品打印标记/产品英文简称
						'ProductPrintSign' => $productKind ['product_print_sign'] . '',
						// 服务商跟踪号码
						'TrackingNumber' => $order ['server_hawbcode'],
						
						// 发件人姓名
						'ShipperName' => $shipperConsignee ['shipper_name'],
						// 发件人公司名
						'ShipperCompanyName' => $shipperConsignee ['shipper_company'],
						// 发件人邮编
						'ShipperPostCode' => $shipperConsignee ['shipper_postcode'],
						// 发件人电话
						'ShipperPhone' => $shipperConsignee ['shipper_telephone'],
						// 发件人地址
						'ShipperAddress' => $shipperConsignee ['shipper_street'],
						// 发件人国家二字码
						'ShipperCountryCode' => $shipperConsignee ['shipper_countrycode'],
						// 发件人国家英文名
						'ShipperCountryEName' => $countrys [$shipperConsignee ['shipper_countrycode']] ? $countrys [$shipperConsignee ['shipper_countrycode']] ['country_enname'] : $shipperConsignee ['shipper_countrycode'],
						// 发件人国家中文名
						'ShipperCountryName' => $countrys [$shipperConsignee ['shipper_countrycode']] ? $countrys [$shipperConsignee ['shipper_countrycode']] ['country_enname'] : $shipperConsignee ['shipper_countrycode'],
						
						// 国家二字码
						'DestinationCountryCode' => $order ['country_code'],
						// 国家英文名
						'DestinationCountryEName' => $countrys [$order ['country_code']] ? $countrys [$order ['country_code']] ['country_enname'] : $order ['country_code'],
						// 国家中文名
						'DestinationCountryName' => $countrys [$order ['country_code']] ? $countrys [$order ['country_code']] ['country_cnname'] : $order ['country_code'],
						// 回邮地址
						'ReturnAddress' => $returnAdd ? $returnAdd ['return_address'] : '',
						// 收件人姓名
						'ConsigneeName' => $shipperConsignee ['consignee_name'],
						// 收货人公司名
						'ConsigneeCompanyName' => $shipperConsignee ['consignee_company'],
						// 收件人邮编
						'ConsigneePostCode' => $shipperConsignee ['consignee_postcode'],
						// 收件人电话
						'ConsigneePhone' => $shipperConsignee ['consignee_telephone'],
						// 收件人手机
						'ConsigneeTelephone' => $shipperConsignee ['consignee_mobile'],
						// 收件人城市
						'ConsigneeCity' => $shipperConsignee ['consignee_city'],
						// 收件人省/州
						'ConsigneeProvince' => $shipperConsignee ['consignee_province'],
						// 收件人街道
						'ConsigneeStreet' => $shipperConsignee ['consignee_street'],
						// 收件人地址
						'ConsigneeAddress' => $shipperConsignee ['consignee_street'] . ' ' . $shipperConsignee ['consignee_city'] . ' ' . $shipperConsignee ['consignee_province'],
						// 买家ID
						'BuyerID' => $printBuyerID == 'Y' ? $order ['buyer_id'] : '',
						// 标签上打印配货信息标记 (Y:打印 N:不打印) 默认 N:不打印
						'PrintDeclareInfoSign' => $PrintDeclareInfoSign,
						// 保险标记 (Y:保险 N:不保险) 默认 N:不保险
						'InsuranceSign' => $InsuranceSign,
						// 是否高价值 (Y:是 N:否) 默认 N:否
						'HighValueSign' => $HighValueSign,
						// 打印时间标记 (Y:打印 N:不打印) 默认 Y:打印
						'PrintTimeSign' => $PrintTimeSign,
						// 是否需要退件 (Y:需要退件 N:不需要退件) 默认 N:不需要退件
						'ReturnSign' => empty ( $order ['return_sign'] ) ? 'N' : strtoupper ( $order ['return_sign'] ),
						// 重量（单位：KG）默认0.2KG
						'Weight' => empty ( $order ['order_weight'] ) || floatval ( $order ['order_weight'] ) <= 0 ? 0.2 : $order ['order_weight'],
						// 件数(快件一票多件时打印多份)
						'Pieces' => $order ['order_pieces'],
						// 出货分区代码
						'ShipmentZoneCode' => $arrivalZone && $arrivalZone ['zone_code'] ? $arrivalZone ['zone_code'] : '',
						// 标签账号信息
						'LabelAccount' => $labelConfig && $labelConfig ['label_account'] ? $labelConfig ['label_account'] : '',
						// 标签签名信息
						'LabelSignature' => $labelConfig && $labelConfig ['label_signature'] ? $labelConfig ['label_signature'] : '',
						// 标签客户账号
						'Label_Customer_account' => $labelConfig && $labelConfig ['label_customer_account'] ? $labelConfig ['label_customer_account'] : '',
						// 标签特殊标记
						'Label_Special_mark' => $labelConfig && $labelConfig ['label_special_mark'] ? $labelConfig ['label_special_mark'] : '',
						// 航空公司
						'LabelAirlineCompany' => $labelConfig && $labelConfig ['label_airline_company'] ? $labelConfig ['label_airline_company'] : '',
						// 单位名称
						'LabelCompanyName' => $labelConfig && $labelConfig ['label_company_name'] ? $labelConfig ['label_company_name'] : '',
						// 检视人
						'LabelExaminePeople' => $labelConfig && $labelConfig ['label_examine_people'] ? $labelConfig ['label_examine_people'] : '',
						// 额外服务名称(多请用分隔符分割)
						'Extraservice' => implode ( ',', $extservice_str ),
						// 报关单模版类型(请参考模版类型表格)
						// NORMALY 通用报关单
						// ZYBG 中邮报关单
						'CustomsDeclareTemplateType' => $productRule ['web_invoice_type_code'] ? $productRule ['web_invoice_type_code'] : 'NORMALY',
						// 报关申报类型（默认 1:Gift）
						// 1 Gift
						// 2 Documents
						// 3 Commercial sample
						// 9 Other
						
						'DeclarationType' => empty ( $order ['mail_cargo_type'] ) ? 4 : $order ['mail_cargo_type'],
						// 货物描述
						'DeclareInvoiceArray' => array (),
						
						// 材积信息
						'ProductSpecifications' => ''
				);
				
				$DeclareInvoiceArray = array ();
				foreach ( $invoice as $v ) {
					// 货物描述
					$DeclareInvoiceArray [] = array (
							// 货物描述
							'GoodsDescription' => $v ['invoice_enname'],
							// 货物描述
							'GoodsDescriptionCN' => $v ['invoice_cnname'],
							// 备注
							'Remarks' => $v ['invoice_note'],
							// 单价
							'UnitPrice' => $v ['invoice_unitcharge'],
							// 数量
							'Quantity' => $v ['invoice_quantity'],
							// 总价
							'TotalCharge' => $v ['invoice_totalcharge'] 
					);
				}
				
				$orderInfo ['DeclareInvoiceArray'] = $DeclareInvoiceArray;
				
				$orderInfoArr [] = $orderInfo;
		} catch ( Exception $e ) {
			$return['Error'] = array('errCode' => '', 'errMessage' => $e->getMessage ());
			return $return;
		}
		
		$configInfoJson = Zend_Json::encode ( $configInfo );
		$orderInfoJson = Zend_Json::encode ( $orderInfoArr );
		$pdfPrintInfoJson = Zend_Json::encode ( $pdfPrintInfo );
		
		$reportPrintTokenConfig = Service_Config::getByField ( 'REPORT_PRINT_TOKEN', 'config_attribute' );
		if (! $reportPrintTokenConfig) {
			$return['Error'] = array('errCode' => '', 'errMessage' => '请配置REPORT_PRINT_TOKEN');
			return $return;
		}
		
		$token = 'ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL';
		$token = $reportPrintTokenConfig ['config_value'];
		$process = new Common_FastReport ( $token );
		$return = $process->MakeLableFileToBase64 ( $configInfoJson, $orderInfoJson, $pdfPrintInfoJson );
		if ($return ['ask'] == 1) {
			$return['url'] = $return['rs']->GetReportStringResult;
			return $return;
		} else {
			$return['Error'] = array('errCode' => '', 'errMessage' => $return ['message'] );
			return $return;
		}
	}
	
	/**
	 * 新增客户
	 */
	function addCustomer($customerInfo) {
		
		$return = array(
				'ask' => 'Failure',
				'message' => '',
		);
		
		if(empty($customerInfo)) {
			$return['message'] = "请求参数值不能为空";
			return $return;
		}
		
		// 客户数据不能为空
		$customer = array(
				'customer_code' => $customerInfo['customer_code'],
				'customer_shortname' => $customerInfo['customer_shortname'],
				'customer_allname' => $customerInfo['customer_allname'],
				'customerstatus_code' => $customerInfo['customerstatus_code'], // C	正式、I	已作废、L	已锁定、O	草稿
				'customerlevel_code' => 'L1',
				'customertype_code' => $customerInfo['customertype_code'],	// GR	个人、GS	公司
				'customersource_code' => 'S',
				'settlementtypes_code' => $customerInfo['settlementtypes_code'], // C	现金、H	半月结、M	月结、P	预付、W	周结
				'customer_createdate' => date('Y-m-d H:i:s'),
				'customer_createrid' => 0,
				'og_id' => $customerInfo['og_id'],
				'tms_id' => Service_User::getTmsId(),
		);
		
		$user = array(
				'user_code' => $customerInfo['user_code'],
				'is_admin' => 0,
				'user_password' => 'e10adc3949ba59abbe56e057f20f883e',
				'user_name' => $customerInfo['user_name'],
				'user_name_en' => $customerInfo['user_name_en'],
				'user_status' => 1,
				'user_email' => $customerInfo['user_email'],
				'user_phone' => $customerInfo['user_phone'],
				'user_mobile_phone' => $customerInfo['user_mobile_phone'],
				'user_add_time' => date('Y-m-d H:i:s'),
				'tms_id' => Service_User::getTmsId(),
		);
		
		// 添加客户
		$obj = new Service_CsiCustomer();
		$result = $obj->addCustomer($customer, $user);
		if($result['state'] == '1') {
			$return['ask'] = 'Success';
			$return['api_key'] = $result['api_key'];
			$return['api_token'] = $result['api_token'];
			$return['customer_code'] = $result['customer_code'];
		} 
		
		$return['message'] = $result['message'];
		return $return;
	}
}