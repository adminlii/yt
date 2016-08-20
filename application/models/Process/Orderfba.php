<?php
class Process_Orderfba
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
    
    public function setOrder($order)
    {   
        $this->_order = $order;
        unset($this->_order['order_id']);
        $this->_order_id = $order['order_id'];
        $filelist = array("invoicelistfile"=>empty($order['invoicelistrel'])?"":$order['invoicelistrel'],
        				  "invoicefile"=>empty($order['invoicerel'])?"":$order['invoicerel'],
        		
        );
        $this->setInvoiceFile($filelist);
    }

    public function setShipper($shipper)
    {
        $this->_shipper = $shipper;
    }

    public function setConsignee($consignee)
    {
        $this->_consignee = $consignee;
    }

    public function setInvoiceFile($file)
    {
    	$orderUploadfba  = 	new Process_OrderfbaUpload;
    	//保留发票
    	$saveDir = APPLICATION_PATH.'/../public/fba/';
    	try {
    		if(!empty($file['invoicefile'])){
    			$this->invoicefile =$file['invoicefile'];
    		}
    		//解析装箱单数据
    		if(!empty($file['invoicelistfile'])){
    			/*
    			$savepath = $saveDir.'invoicelist/';
    			$invoicelistData = $orderUploadfba->readUploadFile($file['invoicelistfile'], $savepath.$file['invoicelistfile'],1);
    			if(!empty($invoicelistData)&&is_array($invoicelistData)){
    				$_invoicelist = array();
    				foreach ($invoicelistData as $v){
    					if(strpos($v[0],'C/NO')!==false){
    						$invoice = array();
    						//获取箱号
    						$invoice['bagid'] = preg_replace('/[^0-9]/','',$v[0]);
    						$invoice['goodname'] = $v[1];
    						$invoice['itemno'] = $v[2];
    						$invoice['quantity'] = $v[3];
    						list($length,$width,$height) = explode('*', $v[4]);
    						$invoice['length'] = $length;
    						$invoice['width'] = $width;
    						$invoice['height'] = $height;
    						$invoice['weight'] = $v[5];
    						$_invoicelist[] = $invoice;
    					}
    				}
    				$this->invoicelistfile = $file['invoicelistfile'];
    				$this->_invoice = $_invoicelist;
    			}
    			*/
    			$_invoicelist = array();
    			if(!$this->_order['boxnum']){
    				//随便+1列
    				$invoice['bagid'] = 1;
    				$invoice['goodname'] = '对讲机';
    				$invoice['itemno'] = 'UV-5R';
    				$invoice['quantity'] = 50;
    				$invoice['length'] = 31;
    				$invoice['width'] = 30;
    				$invoice['height'] = 25;
    				$invoice['weight'] = 27;
    				$_invoicelist[] = $invoice;
    			}else{
    				for ($index = 0;$index<$this->_order['boxnum'];$index++){
    					$invoice = array();
    					//获取箱号
    					$invoice['bagid'] = $index+1;
    					$invoice['goodname'] = '对讲机';
    					$invoice['itemno'] = 'UV-5R';
    					$invoice['quantity'] = 50;
    					$invoice['length'] = 31;
    					$invoice['width'] = 30;
    					$invoice['height'] = 25;
    					$invoice['weight'] = 27;
    					$_invoicelist[] = $invoice;
    				}
    			}
    			$this->invoicelistfile = $file['invoicelistfile'];
    			$this->_invoice = $_invoicelist;
    		}	
    	} catch (Exception $e) {
    		$this->_err[] = $e->getMessage();
    	}
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
        		$this->_err[] = Ec::Lang('FBA订单号不合法,字符长度必须大于1或小于50') . "[{$this->_order['refer_hawbcode']}]";
        	}
        	
            $con = array(
                'refer_hawbcode' => $this->_order['refer_hawbcode']
            );
//             print_r($con);die;
            $shipper_hawbcode_arr = Service_CsdOrderfba::getByCondition($con);
            if($shipper_hawbcode_arr){
                $shipper_hawbcode_exist = false;
                foreach($shipper_hawbcode_arr as $v){
                    if($this->_order_id != $v['order_id']){
                        $shipper_hawbcode_exist = true;
                    }
                }
                
                if($shipper_hawbcode_exist){
                    $this->_err[] = Ec::Lang('FBA订单号已存在') . "[{$this->_order['refer_hawbcode']}]";
                    $this->_apiErr[] = "ORDER_REFER_ISEXISTS";
                }
                
            }
        }else{
        	$this->_err[] = Ec::Lang('FBA订单号不能为空');
        } 
        
        // 验证运输方式,
        if($this->_order['product_code'] === ''){
            $this->_err[] = Ec::Lang('运输方式不可为空');
        }else{
            /* // $product = Service_CsiProductkind::getByField($this->_order['product_code'], 'product_code');
            $product = $this->_getProduct($this->_order['product_code']);
            // echo $sql;
//             print_r($this->_order);
//             print_r($product); exit;
            if(! $product){
                $this->_err[] = Ec::Lang('运输方式不支持', $this->_order['product_code']);
            }else{
                $this->_order['product_code'] = $product['product_code'];
            } */
        }
      	
        if($this->_order['boxnum'] === ''){
        	$this->_err[] = Ec::Lang('箱数不可为空');
        }else{
        	//                     print_r($invoice);exit;
        	if(! is_numeric($this->_order['boxnum'])){
        		$this->_err[] = Ec::Lang('箱数必须为整数');
        	}
        }
        
        // 验证发件人
        if(empty($this->_shipper)){
            $this->_err[] = Ec::Lang('发件人信息不可为空');
        }else{
            if($this->_shipper['shipper_countrycode'] === ''){
                // $this->_err[] = Ec::Lang('发件人国家不可为空');
            }else{
                 //$country = Service_IddCountry::getByField($this->_shipper['shipper_countrycode'], 'country_code');
                $country = $this->_getCountry($this->_shipper['shipper_countrycode']);
                if(! $country){
                    $this->_err[] = Ec::Lang('发件人国家不存在', $this->_shipper['shipper_countrycode']);
                }else{
                    $this->_shipper['shipper_countrycode'] = $country['country_code'];
                }
            }
            if($this->_shipper['shipper_name'] === ''){
                // $this->_err[] = Ec::Lang('发件人姓名不可为空');
            }
            if($this->_shipper['shipper_street'] === ''){
                // $this->_err[] = Ec::Lang('发件人地址不可为空');
            }
        }
        
        // 验证收件人
        if(empty($this->_consignee)){
            $this->_err[] = Ec::Lang('收件人信息不可为空');
        }else{
            // 收件人必填项
            if($this->_consignee['consignee_countrycode'] == ''){
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
           
            if($this->_consignee['consignee_street'] === ''){
                $this->_err[] = Ec::Lang('收件人地址不可为空');
            }
            if ($this->_consignee['consignee_city'] === ''){
            	$this->_err[] = Ec::Lang('收件人城市不可为空');
            }
            if(empty($this->_consignee['consignee_postcode'])){
            	$this->_err[] = Ec::Lang('收件人邮编不可为空');
            }
        }
        
        
        // 验证申报信息
        if(empty($this->_invoice)){
            $this->_err[] = Ec::Lang('装箱单没数据');
        }else{
        }

      
        if(empty($this->invoicefile)){
        	$this->_err[] = Ec::Lang('请上传发票');
        }
        
        
    
	}

    private function _validateElements()
    {
       
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
                // FBA
                'F',
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

    public function createOrder($status)
    {
        $status = strtoupper($status);
        // 必填项验证
        //$this->_validateElements();
        
        if(! empty($this->_err)){
            throw new Exception(Ec::Lang('订单数据不合法'));
        }
        
        $this->_validate();
        if(! empty($this->_err)){
            throw new Exception(Ec::Lang('订单数据不合法'));
        }
       
        //验证地址异步处理的时候验证
        if(!empty($this->_shipper['shipper_city'])){
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
          	"F"
        );
        if(! in_array($status, $statusArr)){
            throw new Exception(Ec::Lang('订单状态不合法'));
        }
        //huanhao
        $shipper_hawbcode = $this->changeNO();
        $this->changeNOadd($this->_order['boxnum']);
        $this->_order['shipper_hawbcode'] = $shipper_hawbcode;
        $order = array(
            'customer_id' => $this->_order['customer_id'],
            'product_code' => $this->_order['product_code'],
            'refer_hawbcode' => $this->_order['refer_hawbcode'],
            'shipper_hawbcode' => $shipper_hawbcode,
            'server_hawbcode' => $this->_order['server_hawbcode'],
            //'channel_hawbcode' => $this->_order['channel_hawbcode'],
            'creater_id' => $this->_order['creater_id'],
            'modify_date' => date('Y-m-d H:i:s'),
        	'boxnum'      => $this->_order['boxnum'] ,
        	'packlistfile' => $this->invoicelistfile,
        	'invoicefile' =>	$this->invoicefile,
        	'order_status' =>	$status,
        );
        
       
        $customer_channelid = $this->_order['customer_channelid']?$this->_order['customer_channelid']:Service_User::getChannelid();
        if($customer_channelid){
        	$order['customer_channelid'] = $customer_channelid;
        }
       
        
        $order = Common_Common::arrayNullToEmptyString($order);
        if(empty($this->_order_id)){
            // 新增
            $order['create_date'] = date('Y-m-d H:i:s');
            $order['creater_id'] = $this->_order['creater_id'];
            $this->_order_id = Service_CsdOrderfba::add($order);
            $this->_log[] = Ec::Lang('订单新增');
        }
        // 删除旧数据 start
        Service_CsdInvoicefba::delete($this->_order_id, 'order_id');
        Service_CsdShipperconsigneefba::delete($this->_order_id, 'order_id');
        // 删除旧数据 end
        // 数据保存 start
        //echo "<pre>";print_r($this->_invoice);die;
        if(!empty($this->_invoice)){
            foreach($this->_invoice as $row){
                // print_r($row);
                $ivs = array(
                    'order_id' => $this->_order_id,
                    'bagid' => $row['bagid'],
                	'goodname' => $row['goodname'],
                    'itemno' => $row['itemno'],
                	'quantity' => $row['quantity'],
                	'length' => $row['length'],
                	'width' => $row['width'],
                	'height' => $row['height'],
               		'weight' => $row['weight'],
                );
                $ivs = Common_Common::arrayNullToEmptyString($ivs);
                //$sql="insert into csd_invoice (invoice_weight,invoice_totalWeight) values('2','6')";
                Service_CsdInvoicefba::add($ivs);
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
            //'shipper_areacode' => $this->_shipper['shipper_areacode'],
            'shipper_telephone' => $this->_shipper['shipper_telephone'],
            'shipper_mobile' => $this->_shipper['shipper_mobile'],
            //'shipper_email' => $this->_shipper['shipper_email'],
            //'shipper_certificatecode' => $this->_shipper['shipper_certificatecode'],
            //'shipper_certificatetype' => $this->_shipper['shipper_certificatetype'],
            //'shipper_fax' => $this->_shipper['shipper_fax'],
            //'shipper_mallaccount' => $this->_shipper['shipper_mallaccount']
        );
        $consignee = array(
            'consignee_countrycode' => $this->_consignee['consignee_countrycode'],
            'consignee_province' => $this->_consignee['consignee_province'],
            'consignee_city' => $this->_consignee['consignee_city'],
            'consignee_street' => $this->_consignee['consignee_street'],
            'consignee_postcode' => $this->_consignee['consignee_postcode'],
            'storage'=> $this->_consignee['storage'],
        );
        $shipper_consignee = array_merge($shipper, $consignee);
        $shipper_consignee['order_id'] = $this->_order_id;
        // print_r($shipper_consignee); exit;
        $shipper_consignee = Common_Common::arrayNullToEmptyString($shipper_consignee);
        Service_CsdShipperconsigneefba::add($shipper_consignee);
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
            $order = Service_CsdOrderfba::getByField($order_id, 'order_id');
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
                case 'exportfba': // 导出fba订单
                    $allowStatus = array(
                        'F',
                    );
                    $this->_verify_tip = '';
                    break;
                case 'printfba': // 打印fba标签
                    $allowStatus = array(
                        'F',
                    );
                    $this->_verify_tip = '此操作只允许对FBA订单进行操作，请确认您选择的订单信息是否正确';
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
    protected function _verifyProcess($order_id, $op)
    {
        $order = Service_CsdOrderfba::getByField($order_id, 'order_id');
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
            
            case 'exportfba': // 导出
                $log_content[] = Ec::Lang('订单导出');
                
                break;
			case 'printfba' : // 打印
				$updateRow ['print_date'] = date ( 'Y-m-d H:i:s' );
				$log_content [] = Ec::Lang ( '订单打印' );
				
				break;
			
            default:
                throw new Exception(Ec::Lang('不合法的操作'));
        }
        Service_CsdOrderfba::update($updateRow, $order_id, 'order_id');
      
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
                /* $getTrackingCode = Service_CsdOrder::getByField($order_id, 'order_id');
                if(empty($getTrackingCode['server_hawbcode']) && $op == "print"){
                    continue;
                } */
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
    public function changeNO() {
    	//从换号池中取出最新的id
    	$db = Common_Common::getAdapter();
    	//$product = $this->_order['product_code'];
    	$product = "FBA";
    	$sql = "select * from csi_changeno where product = '{$product}' and status=1";
    
    	$data = $db->fetchRow($sql);
    	$shipper_no = $data['no_now'];
    	if($shipper_no < $data['no_start']){
    		throw new Exception('换号池已空，无法生成新的订单号');
    	}
    	return  'AS'.change_no($shipper_no).'CN';
    	
    }
    
    //换号池++
    public function changeNOadd($num=1) {
    	//从换号池中取出最新的id
    	$db = Common_Common::getAdapter();
    	//$product = $this->_order['product_code'];
    	$product ="FBA";
    	$_sql = "update csi_changeno set no_now=no_now-{$num} where product='{$product}'";
    	//$where = $db->quoteInto('no_now = no_now+1');
    	$data = $db->query($_sql);
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