<?php
class Order_OrderController extends Ec_Controller_Action
{
    
    public function preDispatch()
    {
        $this->tplDirectory = "order/views/order/";
        $this->serviceClass = new Service_Orders();
    }

    public function listAction()
    {
        $this->forward('list', 'order-list', 'order');
    }
    
    /**
     * 订单详情
     * @throws Exception
     */
    public function detailAction(){

        try{
            $statusArr = Service_OrderProcess::getOrderStatus();
            
            $order_id = $this->getRequest()->getParam('order_id', '');
            if(empty($order_id)){
                throw new Exception(Ec::Lang('参数错误'));
            }
            $order = Service_CsdOrder::getByField($order_id, 'order_id');
            if(!$order){
                throw new Exception(Ec::Lang('订单不存在'));
            }
            if($order['customer_id']!=Service_User::getCustomerId()){
                throw new Exception(Ec::Lang('非法操作'));
            }
            $order['order_status'] = isset($statusArr[$order['order_status']])?$statusArr[$order['order_status']]['name']:$order['order_status'];
            // 历史数据 start
            $con = array(
                    'order_id' => $order_id
            );
            $invoice = Service_CsdInvoice::getByCondition($con,'*',0,0,'invoice_id asc');
            if(empty($invoice)&&$order['mail_cargo_type']!=3){
                throw new Exception(Ec::Lang('申报信息不存在'));
            }
            foreach($invoice as $k=>$v){
                $v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
                $invoice[$k] = $v;
            }
 

            $service = array();
            $services = Service_AtdExtraserviceKind::getAll();
            foreach ($services as $key => $val){
            	$group = Service_AtdExtraserviceKind::getByField($val['extra_service_kind'],'extra_service_kind','extra_service_group');
            	$groupName = Service_AtdExtraserviceKind::getByField($group['extra_service_group'],'extra_service_kind','extra_service_cnname');
            	$name = $group['extra_service_group'] ? $groupName['extra_service_cnname'].':':'';
				$str = ($group['extra_service_group'] == 'C0' ) ? substr($val['extra_service_cnname'], 2) : $val['extra_service_cnname'];
            	$service[$val['extra_service_kind']] = $name.$str;
            }
            
            $extservices = Service_CsdExtraservice::getByCondition($con); //echo "<pre>";print_r($extservices);die;
            $extservice = '';
            foreach ($extservices as $k => $v){
            	if ($v[extra_servicecode] == 'C2'){
            		$se = $service[$v[extra_servicecode]].$v[extra_servicevalue]."元每票; ";
            	}else{
            		$se = $service[$v[extra_servicecode]].'; ';
            	}
            	$extservice .= $se;
            }
            $extservice = trim($extservice,';');
            $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
            if(!$shipperConsignee){
                throw new Exception(Ec::Lang('收发件人信息不存在'));
            }
            // 历史数据 end
            $this->view->order = $order;
            $this->view->invoice = $invoice;
            $this->view->shipperConsignee = $shipperConsignee;
            $this->view->extservice = $extservice;
            $con = array(
                    'ref_id' => $order_id
            );
            $logArr = Service_OrderLog::getByCondition($con,'*',0,0,'log_id asc');
            foreach($logArr as $k=>$v){
                $v['user_name'] = Ec::Lang('系统');
                if($v['op_user_id']){
                    $u = Service_User::getByField($v['op_user_id'],'user_id');
                    $v['user_name'] = $u['user_name'];
                }
            
                $logArr[$k] = $v;
            }
            //print_r($con);exit;
            $this->view->logArr = $logArr;
            echo Ec::renderTpl($this->tplDirectory . "order_detail.tpl", 'layout');
        }catch (Exception $e){
            header("Content-type: text/html; charset=utf-8");
            echo $e->getMessage();exit;
            $this->forward('deny','error','default');
        }
       
    }
    /**
     * 手工创建订单
     */
    public function createAction()
    {
    	$uuid = create_guid();
    	
        $order_id = $this->getRequest()->getParam('order_id', '');
        $cpy = $this->getRequest()->getParam('cpy', null);
        if($this->getRequest()->isPost()){
        	
            $orderR = array();
            $params = $this->getRequest()->getParams();
            //订单头
            $order = $this->getParam('order',array());
            //收件人,发件人
            $consignee = $this->getParam('consignee',array());
            //申报信息
            $invoice = $this->getParam('invoice',array());
            //额外服务
            $extraservice = $this->getParam('extraservice',array());
            //预报？草稿？
            $status = $this->getParam('status','1');
            
            //日志记录start
            $logrow = array();
            $logrow['requestid'] = $uuid;
             
            $logrow['type'] = $order['product_code']=="ESB"?1:2;
            $logrow['detail'] = '创建订单';
            list($usec, $sec) = explode(" ", microtime());
            $logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
            $db = Common_Common::getAdapter();
            $db ->insert('logapi', $logrow);
            //日志记录end
            
            //NZ走的是赛程
            $changeCode = Common_Common::getProductAllByCountryCode($order['country_code'], $order['product_code']);
            if(!empty($changeCode)){
            	$order['product_code'] = $changeCode;
            }
            
            $orderArr = array(
                'product_code' => strtoupper($order['product_code']),
                'country_code' => strtoupper($order['country_code']),
                'refer_hawbcode' => strtoupper($order['refer_hawbcode']),
                'order_weight' => $order['order_weight'],
                'order_pieces' => $order['order_pieces'],
            	
            		'order_length'=>$order['order_length'],
            		'order_width'=>$order['order_width'],
            		'order_height'=>$order['order_height'],
            		
                'buyer_id' =>$order['buyer_id'],
                'order_id' => $order['order_id'],
                'order_create_code'=>'w',
                'customer_id'=>Service_User::getCustomerId(),
                'creater_id'=>Service_User::getUserId(),
                'modify_date'=>date('Y-m-d H:i:s'),
                'mail_cargo_type' => $order['mail_cargo_type'],
                'tms_id'=>Service_User::getTmsId(),
                'customer_channelid'=>Service_User::getChannelid(),
                'insurance_value' => trim($order['insurance_value1']),
            	'battery'=>empty($order['battery'])?'':$order['battery'],
            		
            );
            $volumeArr=array(
            	'length'=>$order['order_length'],
            	'width'=>$order['order_width'],
            	'height'=>$order['order_height'],	
            		
            );

            /*$return = array(
                'ask' => 0,
                'message' => Ec::Lang('订单操作失败')
            );
             $return["message"] = $volumeArr['length'];
            die(Zend_Json::encode($return));*/

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
            );
            
            $consignee['shipper_account'] = ! empty($consignee['shipper_account']) ? $consignee['shipper_account'] : '';
            $shipperArr = Service_CsiShipperTrailerAddress::getByField($consignee['shipper_account'], 'shipper_account');            
            $invoiceArr = array();
            foreach($invoice as $column=>$v){
                foreach($v as $kk=>$vv){
                    if($kk==0)
                        continue;
                    $kk1=$kk-1;
                    $invoiceArr[$kk1][$column] = $vv;
                }
            }

            /*$return = array(
                'ask' => 0,
                'message' => Ec::Lang('订单操作失败')
            );
            $return["message"] = $invoiceArr[0]["sku"];
            die(Zend_Json::encode($return));*/

              
            // php hack 
            if(! empty($invoiceArr)){
                array_unshift($invoiceArr, array());
                unset($invoiceArr[0]);
            }
            //如果类型是文件，则可以允许海关物品无关联
           /*  if($order['mail_cargo_type']==3){
                $isNotNullOfInvoice=false;
                foreach ($invoiceArr as $v){
                    foreach ($v as $k=>$vv){
                        if(!empty($vv)&&$k!='unit_code'){
                            $isNotNullOfInvoice = true;
                            break;
                        } 
                    }
                }
                !$isNotNullOfInvoice&&$invoiceArr=array();
            } */
//             print_r($orderArr);
//             print_r($invoiceArr);
//             print_r($extraservice);
//             print_r($shipperArr);
//             print_r($consigneeArr);
//             exit;
//print_r($orderArr);die;
            $process = new Process_Order();
            $process->setVolume($volumeArr);
            $process->setOrder($orderArr);
            $process->setInvoice($invoiceArr);
            $process->setExtraservice($extraservice);       
            $process->setShipper($shipperArr);          
            $process->setConsignee($consigneeArr);
            $process->setUuid($uuid);
//             $process
            $return = $process->createOrderTransaction($status);
            
//             print_r($params);exit;
            die(Zend_Json::encode($return));
        }
        
        if($order_id){
            try {
                $order = Service_CsdOrder::getByField($order_id, 'order_id');
                if(!$order){
                    throw new Exception(Ec::Lang('订单不存在或已删除'));
                }
                if($order['customer_id']!=Service_User::getCustomerId()){
                    throw new Exception(Ec::Lang('非法操作'));
                }
                // 历史数据 start
                $con = array(
                        'order_id' => $order_id
                );
                $invoice = Service_CsdInvoice::getByCondition($con,'*',0,0,'invoice_id asc');
                
                foreach($invoice as $k=>$v){
                    $v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
                    $v['invoice_weight'] = $v['invoice_weight']?($v['invoice_totalWeight']/$v['invoice_quantity']):0;
                    $invoice[$k] = $v;
                }
                
                $atd_extraservice_kind_arr = Common_DataCache::getAtdExtraserviceKindAll();
                $extservice = Service_CsdExtraservice::getByCondition($con);
                foreach($extservice as $v){
                	$extra_servicecode = $v['extra_servicecode'];
                	//保险费 C0
                	if($atd_extraservice_kind_arr[$extra_servicecode]['extra_service_group']=='C0'){
                		$order['insurance_value'] = $v['extra_servicevalue'];
                	}
                }
                $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
                // 历史数据 end
                if($cpy){
                    unset($order['order_id']);
                    if($order['order_status']!='E'){
                        unset($order['shipper_hawbcode']);
                        //unset($order['refer_hawbcode']);
                        unset($order['server_hawbcode']);
                    }
                }//print_r($order);die;
                $this->view->order = $order;
                $this->view->invoice = $invoice;
                $this->view->shipperConsignee = $shipperConsignee;
                $this->view->extservice = $extservice;
            } catch (Exception $e) {
                header("Content-type: text/html; charset=utf-8");
                echo $e->getMessage();exit;                
            }
            
            
//             print_r($order);exit;
        }else{
			$op = $this->getParam ( 'op', '' );
			if ($op == 'fast-create-order') {
				$product_code = $this->getParam ( 'product_code', '' );
				$country_code = $this->getParam ( 'country_code', '' );
				$order = array (
						'product_code' => $product_code,
						'country_code' => $country_code 
				);
				$this->view->order = $order;
			}
		}

		$countrys = Service_IddCountry::getByCondition(null, '*', 0, 0, '');
        $this->view->country = $countrys;
        $product_kind = Process_ProductRule::getProductKind();
        $aviable_kind = array("ESB","ESBR");
        foreach ($product_kind as $pro_kind_k=>$pro_kind_v){
        	if(!in_array($pro_kind_v["product_code"], $aviable_kind)){
        		unset($product_kind[$pro_kind_k]);
        	}
        }
        $this->view->productKind = $product_kind;

        // print_r($productKind);
        $con = array('unit_status'=>'ON');
        $units = Service_AddDeclareunit::getByCondition($con);
        $this->view->units = $units;

        //证件类型
        $con = array();
        $certificates = Service_AtdCertificateType::getByCondition($con);
        $this->view->certificates = $certificates;  

        //邮政包裹申报种类表
        $con = array();
        $mailCargoTypes = Service_AtdMailCargoType::getByCondition($con);
        $this->view->mailCargoTypes = $mailCargoTypes; 
        //选取默认收件人
        $this->view->shipperCustom=$this->getShipper($order_id);
        
        $html =  Ec::renderTpl($this->tplDirectory . "order_create1.tpl",'system-layout-0506');
        $html = preg_replace('/>\s+</','><',$html);
        echo $html;
    }

    
    public function createfbaAction()
    {
    	if($this->getRequest()->isPost()){
    		$params = $this->getRequest()->getParams();
    		//订单头
    		$order = $this->getParam('order',array());
    		//收件人,发件人
    		$consignee = $this->getParam('consignee',array());
    		//预报？草稿？
    		$status = $this->getParam('status','F');
    		$orderArr = array(
    			'product_code'=> 	$order['product_code'],
    			'refer_hawbcode'=> 	$order['refer_hawbcode'],
    			'boxnum'=> 	$order['boxnum'],
    			'customer_id'=>Service_User::getCustomerId(),
    			'creater_id'=>Service_User::getUserId(),
    			'modify_date'=>date('Y-m-d H:i:s'),
    			'customer_channelid'=>Service_User::getChannelid(),
    			'invoicelistrel'=>$order['invoicelistrel'],
    			'invoicerel'=>$order['invoicerel'],		
    		);
    
    		$consigneeArr = array(
    			'consignee_countrycode'=> $consignee['consignee_countrycode'],
    			'storage'=> $consignee['storage'],
    			'consignee_province'=> $consignee['consignee_province'],
    			'consignee_postcode'=> $consignee['consignee_postcode'],
    			'consignee_city'=> $consignee['consignee_city'],
    			'consignee_street'=> $consignee['consignee_street'],
    		);
    		//$invoiceArr =array();
    		$shipperArr = Service_CsiShipperTrailerAddress::getByField($consignee['shipper_account'], 'shipper_account');
    		$process = new Process_Orderfba();
    		$process->setOrder($orderArr);
    		$process->setShipper($shipperArr);
    		$process->setConsignee($consigneeArr);
    		$return = $process->createOrderTransaction($status);
    		die(Zend_Json::encode($return));
    	}
    	
    	$countrys = Service_IddCountry::getByCondition(null, '*', 0, 0, '');
    	$this->view->country = $countrys;
    	$storageStore = Service_StorageStore::getByCondition(array("country_arr"=>array("US")), '*', 0, 0, '');
    	$this->view->storageStore = $storageStore;
    	//$this->view->productKind = Process_ProductRule::getProductKind();
    	//$con = array('unit_status'=>'ON');
    	//选取默认收件人
    	$this->view->shipperCustom=$this->getShipper($order_id);
    	$html =  Ec::renderTpl($this->tplDirectory . "order_createfba.tpl",'system-layout-0506');
    	$html = preg_replace('/>\s+</','><',$html);
    	echo $html;
    }
    
    /**
     * 手工创建DHL订单
     */
    public function createdhlAction()
    {
    	$uuid = create_guid();
       $order_id = $this->getRequest()->getParam('order_id', '');
        $cpy = $this->getRequest()->getParam('cpy', null);
        if($this->getRequest()->isPost()){
            $orderR = array();
            $params = $this->getRequest()->getParams();
            //订单头
            $order = $this->getParam('order',array());
            //收件人,发件人
            $consignee = $this->getParam('consignee',array());
            //发件人
            $shipper = $this->getParam('shipper',array());
            //申报信息
            $invoice = $this->getParam('invoice',array());
            $invoice1 = $this->getParam('invoice1',array());
            //额外服务
            $extraservice = $this->getParam('extraservice',array());
            //预报？草稿？
            $status = $this->getParam('status','1');
            //日志记录start
            $logrow = array();
            $logrow['requestid'] = $uuid;
             
            $logrow['type'] = 3;
            $logrow['detail'] = '创建订单';
            list($usec, $sec) = explode(" ", microtime());
            $logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
            $db = Common_Common::getAdapter();
            $db ->insert('logapi', $logrow);
            //日志记录end
            $orderArr = array(
                'product_code' => strtoupper($order['product_code']),
                'country_code' => strtoupper($order['country_code']),
                'refer_hawbcode' => strtoupper($order['refer_hawbcode']),
                'order_weight' => $order['order_weight'],
                'order_pieces' => $order['order_pieces'],
                 
                'order_length'=>$order['order_length'],
                'order_width'=>$order['order_width'],
                'order_height'=>$order['order_height'],
            	'dangerousgoods'=>empty($order['dangerousgoods'])?0:1,
                'buyer_id' =>$order['buyer_id'],
                'order_id' => $order['order_id'],
                'order_create_code'=>'w',
                'customer_id'=>Service_User::getCustomerId(),
                'creater_id'=>Service_User::getUserId(),
                'modify_date'=>date('Y-m-d H:i:s'),
                'mail_cargo_type' => $order['mail_cargo_type'],
                'tms_id'=>Service_User::getTmsId(),
                'customer_channelid'=>Service_User::getChannelid(),
                'insurance_value' => trim($order['insurance_value']),
                'insurance_value_gj' => $order['insurance_value_gj'],
            	'invoice_print'=>empty($order['invoice_print'])?0:1,
            	'makeinvoicedate'=> $order['makeinvoicedate'],
            	'export_type'=> $order['export_type'],
            	'trade_terms'=> $order['trade_terms'],
            	'invoicenum'=> $order['invoicenum'],
            	'pay_type'=> $order['pay_type'],
            	'fpnote'=> $order['fpnote'],
            	'untread'=>empty($order['untread'])?0:intval($order['untread']),
            );
            //添加一个发票类型
            if($orderArr["invoice_print"]==1){
            	$orderArr["invoice_type"]=$order['invoice_type'];
            }else{
            	$orderArr["invoice_type"]=0;
            }
            
            /*$return = array(
             'ask' => 0,
             'message' => Ec::Lang('订单操作失败')
            );
            $return["message"] = $volumeArr['length'];
            die(Zend_Json::encode($return));*/
    
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
            );
    
            $consignee['shipper_account'] = ! empty($consignee['shipper_account']) ? $consignee['shipper_account'] : '';
            //$shipperArr = Service_CsiShipperTrailerAddress::getByField($consignee['shipper_account'], 'shipper_account');
            $shipperArr = array(
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
                'shipper_certificatecode' => $shipper['shipper_certificatecode'],
                'shipper_certificatetype' => $shipper['shipper_certificatetype'],
                'shipper_fax' => $shipper['shipper_fax'],
                'shipper_mallaccount' => $shipper['shipper_mallaccount']
            );
            
            $invoiceArr = array();
            foreach($invoice as $column=>$v){
                foreach($v as $kk=>$vv){
                $invoiceArr[$kk][$column] = $vv;
                }
            }
               //去掉都为空的海关信息
            foreach ($invoiceArr as $k=>$v){
                $flag = false;
                foreach ($v as $vv){
                    if(!empty($vv)){
                        $flag=true;
                        break;
                    }
                }
                if(!$flag){
                    unset($invoiceArr[$k]);
                }
            }
            //dhl 中 根据货物计算 包裹信息
            $invoice_weight	= 0;
            $invoice_lenght = 0;
            $invoice_width 	= 0;
            $invoice_height	= 0;
            foreach ($invoiceArr as $column=>$vc){
            	$invoice_weight+=$vc["invoice_weight"]*$vc["invoice_quantity"];
            	$invoice_lenght>$vc["invoice_length"]?"":$invoice_lenght=$vc["invoice_length"];
            	$invoice_width>$vc["invoice_width"]?"":$invoice_width=$vc["invoice_width"];
            	$invoice_height>$vc["invoice_height"]?"":$invoice_height=$vc["invoice_height"];
            	$invoiceArr[$column]=$vc;
                if(!$vc['invoice_enname']){
                    $vc['invoice_enname'] = $invoice['invoice_enname'][0];
                    $vc['invoice_cnname'] = $invoice['invoice_cnname'][0];
                    $vc['invoice_currencycode'] = $invoice['invoice_currencycode'][0];
                    $vc['invoice_shippertax'] = $invoice['invoice_shippertax'][0];
                    $vc['invoice_consigneetax'] = $invoice['invoice_consigneetax'][0];
                    $vc['invoice_totalcharge_all'] = $invoice['invoice_totalcharge_all'][0];
                    $vc['hs_code'] = $invoice['hs_code'][0];
                    $invoiceArr[$column]=$vc;
                }
                
            }
            $orderArr['order_length'] = $volumeArr['length'] = intval($invoice_lenght);
            $orderArr['order_width'] = $volumeArr['width'] = intval($invoice_width);
            $orderArr['order_height'] = $volumeArr['height'] = intval($invoice_height);
            $orderArr["order_weight"] = round($invoice_weight,1);
            
            if(! empty($invoiceArr)){
                array_unshift($invoiceArr, array());
                unset($invoiceArr[0]);
            }
            
          
            //标签打印 add
            $labelArr = array();
            foreach($invoice1 as $column=>$v){
            	foreach($v as $kk=>$vv){
            		$labelArr[$kk][$column] = $vv;
            	}
            }
            //去掉都为空的海关信息
            foreach ($labelArr as $k=>$v){
            	$flag = false;
            	foreach ($v as $vv){
            		if(!empty($vv)&&$vv!='CN'){
            			$flag=true;
            			break;
            		}
            	}
            	if(!$flag){
            		unset($labelArr[$k]);
            	}
            }
            if(! empty($labelArr)){
            	array_unshift($labelArr, array());
            	unset($labelArr[0]);
            }
            //DHL 添加了规则，refer用来存取城市代码
            $condtion_sp['cityname'] = $shipper['shipper_city'];
            $condtion_sp['status'] =   1;
            $condtion_sp['productcode'] =   $orderArr["product_code"];
            $server_csi_prs=new Service_CsiProductRuleShipper();
            $rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
            if($rs_cisprs[0]){
            	//如果是DHL不认的替换掉邮编和城市
            	if($rs_cisprs[0]['cityrname']&&$condtion_sp['productcode']=='G_DHL'){
            		if($shipperArr['shipper_street']){
            			$shipperArr['shipper_street'].=" ".$shipperArr['shipper_city'];
            		}
            		$shipperArr['shipper_city']=$rs_cisprs[0]['cityrname'];
            		$shipperArr['shipper_postcode']=$rs_cisprs[0]['postcode'];
            	}
            	//ref里面设定上citycode
            	$orderArr['refer_hawbcode'] = $rs_cisprs[0]['citycode'];
            }
            $process = new Process_OrderDhl();
            $process->setVolume($volumeArr);
            $process->setOrder($orderArr);
            $process->setInvoice($invoiceArr);
            $process->setLabel($labelArr);
            $process->setExtraservice($extraservice);
            $process->setShipper($shipperArr);
            $process->setConsignee($consigneeArr);
            $process->setUuid($uuid);
            //             $process
            $return = $process->createOrderTransactionApi($status);
    
            //             print_r($params);exit;
            die(Zend_Json::encode($return));
        }
    
        if($order_id){
            try {
                $order = Service_CsdOrder::getByField($order_id, 'order_id');
                if(!$order){
                    throw new Exception(Ec::Lang('订单不存在或已删除'));
                }
                if($order['customer_id']!=Service_User::getCustomerId()){
                    throw new Exception(Ec::Lang('非法操作'));
                }
                // 历史数据 start
                $con = array(
                    'order_id' => $order_id
                );
               /*  $invoice = Service_CsdInvoice::getByCondition($con,'*',0,0,'invoice_id asc');
    
                foreach($invoice as $k=>$v){
                    $v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
                    $v['invoice_weight'] = $v['invoice_weight']?($v['invoice_totalWeight']/$v['invoice_quantity']):0;
                    $invoice[$k] = $v;
                } */
    
                /* $atd_extraservice_kind_arr = Common_DataCache::getAtdExtraserviceKindAll();
                $extservice = Service_CsdExtraservice::getByCondition($con);
                foreach($extservice as $v){
                    $extra_servicecode = $v['extra_servicecode'];
                    //保险费 C0
                    if($atd_extraservice_kind_arr[$extra_servicecode]['extra_service_group']=='C0'){
                        $order['insurance_value'] = $v['extra_servicevalue'];
                    }
                } */
                $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
                // 历史数据 end
                if($cpy){
                    unset($order['order_id']);
                    if($order['order_status']!='E'){
                        unset($order['shipper_hawbcode']);
                        unset($order['refer_hawbcode']);
                        unset($order['server_hawbcode']);
                    }
                }//print_r($order);die;
                $this->view->order = $order;
                //$this->view->invoice = $invoice;
                //分割收件人地址
                $shipperstreeArr = explode("||", $shipperConsignee["shipper_street"]);
                $shipperConsignee["shipper_street1"]=$shipperstreeArr[0]?$shipperstreeArr[0]:'';
                $shipperConsignee["shipper_street2"]=$shipperstreeArr[1]?$shipperstreeArr[1]:'';
                $shipperConsignee["shipper_street3"]=$shipperstreeArr[2]?$shipperstreeArr[2]:'';
                $this->view->shipperConsignee = $shipperConsignee;
                //var_dump($shipperConsignee);
                $this->view->extservice = $extservice;
            } catch (Exception $e) {
                header("Content-type: text/html; charset=utf-8");
                echo $e->getMessage();exit;
            }
        }else{
            $op = $this->getParam ( 'op', '' );
            if ($op == 'fast-create-order') {
                $product_code = $this->getParam ( 'product_code', '' );
                $country_code = $this->getParam ( 'country_code', '' );
                $order = array (
                    'product_code' => $product_code,
                    'country_code' => $country_code
                );
                $this->view->order = $order;
            }
        }
        
        $countrys = Process_ProductRule::arrivalCountry('G_DHL');
        //var_dump($countrys);
        //$countrys = Service_IddCountry::getByCondition(null, '*', 0, 0, '');
        $this->view->country = $countrys;
    
        $product_kind = Process_ProductRule::getProductKind();
        $aviable_kind = array("G_DHL");
        foreach ($product_kind as $pro_kind_k=>$pro_kind_v){
        	if(!in_array($pro_kind_v["product_code"], $aviable_kind)){
        		unset($product_kind[$pro_kind_k]);
        	}
        }
        $this->view->productKind =$product_kind;
        $con = array('unit_status'=>'ON');
        $units = Service_AddDeclareunit::getByCondition($con);
        $this->view->units = $units;
    
        //证件类型
        $con = array();
        $certificates = Service_AtdCertificateType::getByCondition($con);
        $this->view->certificates = $certificates;
    
        //邮政包裹申报种类表
        $con = array();
        $mailCargoTypes = Service_AtdMailCargoType::getByCondition($con);
        $this->view->mailCargoTypes = $mailCargoTypes;
        //选取默认收件人
        //$this->view->shipperCustom=$this->getShipper($order_id,1);
        //var_dump($this->getShipper($order_id,1));
        $html =  Ec::renderTpl($this->tplDirectory . "order_create_dhl.tpl", 'system-layout-0506');
        $html = preg_replace('/>\s+</','><',$html);
        echo $html;
    }
    
    /**
     * 手工创建TNT订单
     */
	public function createtntAction()
    {
    	$uuid = create_guid();
        $order_id = $this->getRequest()->getParam('order_id', '');
        $cpy = $this->getRequest()->getParam('cpy', null);
        if($this->getRequest()->isPost()){
            $orderR = array();
            $params = $this->getRequest()->getParams();
            //订单头
            $order = $this->getParam('order',array());
            //收件人,发件人
            $consignee = $this->getParam('consignee',array());
            //发件人
            $shipper = $this->getParam('shipper',array());
            //申报信息
            $invoice = json_decode($params['invoice'],1);
            //额外服务
            $extraservice = $this->getParam('extraservice',array());
            //预报？草稿？
            $status = $this->getParam('status','1');
            //日志记录start
            $logrow = array();
            $logrow['requestid'] = $uuid;
             
            $logrow['type'] = 3;
            $logrow['detail'] = '创建订单';
            list($usec, $sec) = explode(" ", microtime());
            $logrow['creattime'] = date("Y-m-d H:i:s|",$sec-3600*8).$usec;
            $db = Common_Common::getAdapter();
            $db ->insert('logapi', $logrow);
            //日志记录end
            $orderArr = array(
                'product_code' => 'TNT',
                'country_code' => strtoupper($order['country_code']),
                'refer_hawbcode' => strtoupper($order['refer_hawbcode']),
                'order_weight' => 0.01,
                'order_pieces' => 1,
                'order_length'=>10,
                'order_width'=>10,
                'order_height'=>10,
            	'dangerousgoods'=>empty($order['dangerousgoods'])?0:1,
                'buyer_id' =>'',
                'order_id' => $order['order_id'],
                'order_create_code'=>'w',
                'customer_id'=>Service_User::getCustomerId(),
                'creater_id'=>Service_User::getUserId(),
                'modify_date'=>date('Y-m-d H:i:s'),
                'mail_cargo_type' => $order['mail_cargo_type'],
                'tms_id'=>Service_User::getTmsId(),
                'customer_channelid'=>Service_User::getChannelid(),
                'insurance_value' => trim($order['insurance_value']),
                'insurance_value_gj' => $order['insurance_value_gj'],
            	'invoice_print'=>empty($order['invoice_print'])?0:1,
            	'makeinvoicedate'=> $order['makeinvoicedate'],
            	'export_type'=> $order['export_type'],
            	'trade_terms'=> $order['trade_terms'],
            	'invoicenum'=> $order['invoicenum'],
            	'pay_type'=> $order['pay_type'],
            	'fpnote'=> $order['fpnote'],
            	'untread'=>empty($order['untread'])?0:intval($order['untread']),
            	'service_code'=>empty($params['servicecode'])?'':trim($params['servicecode']),
            );
            //添加一个发票类型
            if($orderArr["invoice_print"]==1){
            	$orderArr["invoice_type"]=$order['invoice_type'];
            }else{
            	$orderArr["invoice_type"]=0;
            }
    		//货币类型
            $orderArr['currencytype'] =  $params['currencytype'];
            $orderArr['invoice_totalcharge_all'] =  $order['invoice_totalcharge_all'];
            $orderArr['invoice_shippertax'] =  $params['invoice_shippertax'];
            $orderArr['invoice_consigneetax'] =  $params['invoice_consigneetax'];
            $orderArr['order_info'] = $params['DESCRIPTION'];
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
                'consignee_certificatetype' => '',
                'consignee_certificatecode' => '',
                'consignee_credentials_period' => '',
                'consignee_doorplate' => '',
            );
    
            $consignee['shipper_account'] = ! empty($consignee['shipper_account']) ? $consignee['shipper_account'] : '';
            $shipperArr = array(
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
                'shipper_certificatecode' => '',
                'shipper_certificatetype' => '',
                'shipper_fax' => '',
                'shipper_mallaccount' => ''
            );
            
            $invoiceArr = $invoice;
            //DHL 添加了规则，refer用来存取城市代码
            $condtion_sp['cityname'] = $shipper['shipper_city'];
            $condtion_sp['status'] =   1;
            $condtion_sp['productcode'] =   $orderArr["product_code"];
            $server_csi_prs=new Service_CsiProductRuleShipper();
            $rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
            if($rs_cisprs[0]){
            	//如果是DHL不认的替换掉邮编和城市
            	if($rs_cisprs[0]['cityrname']&&$condtion_sp['productcode']=='G_DHL'){
            		if($shipperArr['shipper_street']){
            			$shipperArr['shipper_street'].=" ".$shipperArr['shipper_city'];
            		}
            		$shipperArr['shipper_city']=$rs_cisprs[0]['cityrname'];
            		$shipperArr['shipper_postcode']=$rs_cisprs[0]['postcode'];
            	}
            	//ref里面设定上citycode
            	$orderArr['refer_hawbcode'] = $rs_cisprs[0]['citycode'];
            }
            $process = new Process_OrderTnt();
            //$process->setVolume($volumeArr);
            $process->setOrder($orderArr);
            $process->setInvoice($invoiceArr);
            $process->setExtraservice($extraservice);
            $process->setShipper($shipperArr);
            $process->setConsignee($consigneeArr);
            $process->setUuid($uuid);
            $return = $process->createOrderTransaction($status);
            die(Zend_Json::encode($return));
        }
    
        if($order_id){
            try {
                $order = Service_CsdOrder::getByField($order_id, 'order_id');
                if(!$order){
                    throw new Exception(Ec::Lang('订单不存在或已删除'));
                }
                if($order['customer_id']!=Service_User::getCustomerId()){
                    throw new Exception(Ec::Lang('非法操作'));
                }
                // 历史数据 start
                $con = array(
                    'order_id' => $order_id
                );
               /*  $invoice = Service_CsdInvoice::getByCondition($con,'*',0,0,'invoice_id asc');
    
                foreach($invoice as $k=>$v){
                    $v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
                    $v['invoice_weight'] = $v['invoice_weight']?($v['invoice_totalWeight']/$v['invoice_quantity']):0;
                    $invoice[$k] = $v;
                } */
    
                /* $atd_extraservice_kind_arr = Common_DataCache::getAtdExtraserviceKindAll();
                $extservice = Service_CsdExtraservice::getByCondition($con);
                foreach($extservice as $v){
                    $extra_servicecode = $v['extra_servicecode'];
                    //保险费 C0
                    if($atd_extraservice_kind_arr[$extra_servicecode]['extra_service_group']=='C0'){
                        $order['insurance_value'] = $v['extra_servicevalue'];
                    }
                } */
                $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
                // 历史数据 end
                if($cpy){
                    unset($order['order_id']);
                    if($order['order_status']!='E'){
                        unset($order['shipper_hawbcode']);
                        unset($order['refer_hawbcode']);
                        unset($order['server_hawbcode']);
                    }
                }//print_r($order);die;
                $this->view->order = $order;
                //$this->view->invoice = $invoice;
                //分割收件人地址
                $shipperstreeArr = explode("||", $shipperConsignee["shipper_street"]);
                $shipperConsignee["shipper_street1"]=$shipperstreeArr[0]?$shipperstreeArr[0]:'';
                $shipperConsignee["shipper_street2"]=$shipperstreeArr[1]?$shipperstreeArr[1]:'';
                $shipperConsignee["shipper_street3"]=$shipperstreeArr[2]?$shipperstreeArr[2]:'';
                $this->view->shipperConsignee = $shipperConsignee;
                //var_dump($shipperConsignee);
                //$this->view->extservice = $extservice;
            } catch (Exception $e) {
                header("Content-type: text/html; charset=utf-8");
                echo $e->getMessage();exit;
            }
        }else{
            $op = $this->getParam ( 'op', '' );
            if ($op == 'fast-create-order') {
                $product_code = $this->getParam ( 'product_code', '' );
                $country_code = $this->getParam ( 'country_code', '' );
                $order = array (
                    'product_code' => $product_code,
                    'country_code' => $country_code
                );
                $this->view->order = $order;
            }
        }
        
        $countrys = Process_ProductRule::arrivalCountry('TNT');
        $countrys = Service_IddCountry::getByCondition(null, '*', 0, 0, '');
        $this->view->country = $countrys;
        //邮政包裹申报种类表
        $con = array();
        $mailCargoTypes = Service_AtdMailCargoType::getByCondition($con);
        $this->view->mailCargoTypes = $mailCargoTypes;
        $html =  Ec::renderTpl($this->tplDirectory . "order_create_tnt.tpl", 'system-layout-tnt');
        $html = preg_replace('/>\s+</','><',$html);
        echo $html;
    }
    
    /**
     * 订单导入
     */
    public function importAction()
    {
        $this->forward('import-user-template');
        
//         if($this->getRequest()->isPost()){
//             set_time_limit(0);
//             ini_set('memory_limit', '1024M');
//             $return = array(
//                 'ask' => 0,
//                 'message' => 'Request Method Err'
//             );
            
//             $file = $_FILES['fileToUpload'];
//             // print_r($file);exit;
//             $process = new Process_OrderUpload();
//             if($file && $file['tmp_name'] && $file['size'] > 0 && empty($file['error'])){
                
//                 $result = $process->importTransaction($file);
//             }else{
//                 $param = $this->getParam('fileData', array());
//                 // print_r($param);exit;
//                 $result = $process->submitBatchTransaction($param);
//             }
//             // $result['ask'] = 0;
//             $this->view->result = $result;
//             // print_r($result);//exit;
//         }
        
//         echo Ec::renderTpl($this->tplDirectory . "order_import.tpl", 'layout-upload');
    }
 

    /**
     * 订单导入
     */
    public function importUserTemplateAction()
    {
        $this->view->productKind = Process_ProductRule::getProductKind();
        if($this->getRequest()->isPost()){
        	
            set_time_limit(0);
            ini_set('memory_limit', '1024M');
            $return = array(
                'ask' => 0,
                'message' => 'Request Method Err'
            );
            $param = $this->getRequest()->getParams();
            //var_dump($param);
                // 默认发件人
            $shipper_account = $this->getParam('shipper_account', 0);
//             print_r($shipper_account);exit;
            //国家映射
            //$country_map = $this->getParam('country_map', array());
            $ansych = $this->getParam('ansych', '');
//                 print_r($country_map);exit;
            $file = $_FILES['fileToUpload'];
            $process = new Process_OrderUploadUserTemplate();
            //设置默认发件人
            if($shipper_account){
            	
            }
                //$process->setDefaultShipperAccount($shipper_account);
            //$process->setCountryMap($country_map);
            
            if($file && $file['tmp_name'] && $file['size'] > 0 && empty($file['error'])) {
            	// 异步提交模式
            	if($ansych) {
            		$result = $process->importByAsynchTransaction($file);
            	} else {
            		// 及时提交并返回结果
            		//echo microtime_float().'<br>';
                	$result = $process->importTransaction($file);
            	}
            }else{
                $param = $this->getParam('fileData', array());
                //print_r($param);exit;
                //$process->setCountryMap($countryMapArr);
                $result = $process->submitBatchTransaction($param);
            }
            
            $result['ansych'] = $ansych;
            // $result['ask'] = 0;
            $this->view->result = $result;
            $this->view->result_str = print_r($result, true);
            
            $notExistCountryArr = $process->getNotExistCountryArr();
            if(! empty($notExistCountryArr)){
                $this->view->notExistCountryArr = $notExistCountryArr;
                $this->view->countrys = Common_DataCache::getCountry();
            }
            
            echo $this->view->render($this->tplDirectory . "order_import_file_content.tpl");
            exit();
            // print_r($result);//exit;
        }
       
        $sql = "select report_filename,report_file_path from csd_customer_report where customer_id=0 or customer_id='' or customer_id is null  order by report_id";
        $baseTemplate = Common_Common::fetchAll($sql);
        $this->view->baseTemplate = $baseTemplate;
        // print_r(Common_DataCache::getCountry());exit;
        echo Ec::renderTpl($this->tplDirectory . "order_import.tpl", 'layout-upload');
    }
    /**
     * 订单审核
     */
    public function verifyAction()
    {
        if($this->getRequest()->isPost()){
            set_time_limit(0);
            $param = $this->_request->getParams();
            $orderIdArr = $this->_request->getParam('order_id', array());
            $op = $this->_request->getParam('op', '');
            //FBA的操作池
            $fba_arr = array('exportfba','printfba');
            if(in_array($op, $fba_arr)){
            	$process = new Process_Orderfba();
            	$return = $process->verifyOrderBatchTransaction($orderIdArr, $op);
            }else{
            	$process = new Process_Order();
            	$return = $process->verifyOrderBatchTransaction($orderIdArr, $op);
            }
            
            // print_r($return);exit;
            die(json_encode($return));
        }
    }
    
    /**
     * 获取发件人信息方法
     */
    protected function getShipper($order_id,$getDefault=false){
        $con = array(
            'customer_id' => Service_User::getCustomerId()
        );
        
        $con['customer_channelid'] = Service_User::getChannelid();
        $submiters = Service_CsiShipperTrailerAddress::getByCondition($con);
        if(empty($submiters))
            return array();
        if($order_id){
            $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
            if($shipperConsignee){
                foreach($submiters as $k=>$v){
                    if($shipperConsignee['shipper_account']==$v['shipper_account']){
                        $v['is_default'] = 1;
                    }else{
                        $v['is_default'] = 0;
                    }
                    $submiters[$k] = $v;
                }
            }
        }
        //只有一条发件信息，默认选中
        if(count($submiters)==1){
            $submiters[0]['is_default'] = 1;
        }
        
        if($getDefault){
            if(count($submiters)==1){
                $submiters = $submiters[0];
            }else{
                $default_key = -1;
                foreach ($submiters as $k=>$v){
                    if($v['is_default']==1){
                        $default_key = $k;
                        break;
                    }
                }
                $submiters=$default_key>=0?$submiters[$default_key]:$submiters[0];
            }
        }
        
        return $submiters;
    }
    
    
    /**
     * 获取发件人信息 
     */
    public function getSubmiterAction(){
        $order_id = $this->getParam('order_id', '');
        $type     = $this->getParam('type', 0);
        $con = array(
            'customer_id' => Service_User::getCustomerId()
        );

        $con['customer_channelid'] = Service_User::getChannelid();
        $submiters = Service_CsiShipperTrailerAddress::getByCondition($con);        
        if($order_id){
            $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
            if($shipperConsignee){
                foreach($submiters as $k=>$v){
                    if($shipperConsignee['shipper_account']==$v['shipper_account']){
                        $v['is_default'] = 1;                        
                    }else{
                        $v['is_default'] = 0;
                    }
                    $submiters[$k] = $v;
                }
            }
        }
        //只有一条发件信息，默认选中
        if(count($submiters)==1){
            $submiters[0]['is_default'] = 1;
        }
        $this->view->submiters = $submiters; 
        if(!$type)
            echo $this->view->render($this->tplDirectory . "order_submiter.tpl");
        else 
            exit(json_encode($submiters));
    }
    
    /**
     * 支持的运输方式
     */
    public function getProductAction(){
        $productKind = Process_ProductRule::getProductKind();
        echo Zend_Json::encode($productKind);
    }
    /**
     * 导出订单
     */
    public function exportAction(){
        $order_id_arr = $this->getParam('orderId', array());
        $process = new Process_OrderUpload();
        $process->baseExportProcess($order_id_arr);
    }

    /**
     * 订单打印
     */
    public function printAction()
    {
        $this->tplDirectory = "order/views/template/";
        try{
            set_time_limit(0);
            $order_id_arr = $this->getParam('orderId', array());
            $type = $this->getParam('type', 'label');
            if(empty($order_id_arr) || !is_array($order_id_arr)){
                throw new Exception(Ec::Lang('没有需要打印的订单'));
            }
            $result = array();
            foreach($order_id_arr as $order_id){
                $rs = Service_CsdOrderProcess::getOrderInfo($order_id);
                $data = $rs['data'];
                $order = $data['order'];
                if($order['customer_id']!=Service_User::getCustomerId()){
                    continue;
                }

                //如果跟踪号没有获取 则不能打印
                $getTrackingCode = Service_CsdOrder::getByField($order_id, 'order_id');
                if(empty($getTrackingCode['server_hawbcode'])){
                    continue;
                }

                //========================================
                $updateRow = array();
                $updateRow['print_date'] = date('Y-m-d H:i:s');                 
                Service_CsdOrder::update($updateRow, $order_id, 'order_id');                
                // 日志
                $logRow = array(
                        'ref_id' => $order_id,
                        'log_content' => Ec::Lang('订单打印')
                );
                Service_OrderLog::add($logRow);
                //======================================== 
                //验证回邮地址
                $result[] = $rs;
            }
            if(empty($result)){
                throw new Exception('没有需要打印的订单');
            }
            $this->view->result = $result;

            $this->view->customer_name = '协议客户名称xxxxxx';
            $this->view->return_address = '退件单位名称xxxxxxx';
            $this->view->label_desc = '标签说明标签说明标签说明标签说明标签说明标签说明标签说明标签说明标签说明标签说明标签说明标签说明标签说明标签说明';
            
//             echo $this->view->render($this->tplDirectory . "B4.tpl");
            echo $this->view->render($this->tplDirectory . "A3.tpl");
//             echo $this->view->render($this->tplDirectory . "Common.tpl");
        }catch(Exception $e){
            header("Content-type: text/html; charset=utf-8");
            echo $e->getMessage();
            exit();
        }
    }
    
    /**
     * 获取申报信息
     */
    public function getInvoiceAction() {
    	if($this->getRequest()->isPost()){
    		
    		$orderIdArr = $this->_request->getParam('order_id', array());
    		$condition = array();
    		$condition['order_id_in'] = $orderIdArr;
    		$condition['customer_id'] = Service_User::getCustomerId();
    		
    		$fields = array('csd_order.order_id', 
		    				'shipper_hawbcode',
		    				'country_code',
		    				'csd_invoice.invoice_cnname',
		    				'csd_invoice.invoice_enname',
		    				'csd_invoice.unit_code',
		    				'csd_invoice.invoice_quantity',
		    				'csd_invoice.invoice_totalcharge',
		    				'csd_invoice.hs_code',
		    				'csd_invoice.invoice_note',
		    				'csd_invoice.invoice_url');
    		
    		// 获取订单信息
    		$invoice = Service_CsdOrder::getByConditionJoinInvoice($condition, $fields);
    		foreach($invoice as $k=>$v){
    			$v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
    			$invoice[$k] = $v;
    		}
    		
    		$return['state'] = 1;
    		$return['data'] = $invoice;
    		
    		$con = array('unit_status'=>'ON');
    		$units = Service_AddDeclareunit::getByCondition($con);
    		$return['units'] = $units;
    		// print_r($return);exit;
    		die(json_encode($return));
    	}
    }
    
    // 编辑申报信息
    public function editInvoiceAction() {
    	if($this->getRequest()->isPost()){
    
    		$invoice = $this->_request->getParam('invoice', array());
    		$invoiceArr = array();
    		// 按订单转换数组
    		foreach($invoice as $order => $row) {
    			// 转换单个订单的多条申报数据
	    		foreach($row as $column => $v){
	    			foreach($v as $kk=>$vv){
	    				$invoiceArr[$order][$kk][$column] = $vv;
	    			}
	    		}
    		}
    		
//     		print_r($invoiceArr);

    		$process = new Process_Order();
    		$return = $process->editInvoiceTransaction($invoiceArr);
    
    		die(json_encode($return));
    	}
    }
    
    public function checkRefrenceNoAction(){
		$return = array (
				'ask' => 1,
				'message' => '' 
		);
		$order_id = $this->getParam ( 'order_id', '' );
		$refrence_no = $this->getParam ( 'refrence_no', '' );
		if (!empty ( $refrence_no )) { 
			$con = array (
					'shipper_hawbcode' => $refrence_no 
			);
			$shipper_hawbcode_arr = Service_CsdOrder::getByCondition ( $con );
			 
			foreach ( $shipper_hawbcode_arr as $k=>$v ) {
				if ($order_id == $v ['order_id']) {
					unset($shipper_hawbcode_arr[$k]);
				}
			}			
			if (!empty($shipper_hawbcode_arr)) {
				$return['ask'] = 0;
				$return ['message'] = "客户单号系统已经存在";
			}			 
		}
		
		die ( Zend_Json::encode ( $return ) );
	}
	
	/**
	 * 导入发票数据
	 */
	public function importInvoiceAction() {
		if($this->getRequest()->isPost()) {
		
			set_time_limit(0);
			ini_set('memory_limit', '1024M');
			$return = array(
					'ask' => 0,
					'message' => 'Request Method Err'
			);
		
			$file = $_FILES['FileName'];
			$obj = new Process_OrderUpload();
			$return = $obj->importInvoiceTransaction($file);
		
			die(Zend_Json::encode($return));
		}
	}
	
	/**
	 * 导入重量数据
	 */
	public function importWeightAction() {
		if($this->getRequest()->isPost()) {
		
			set_time_limit(0);
			ini_set('memory_limit', '1024M');
			$return = array(
					'ask' => 0,
					'message' => 'Request Method Err'
			);
		
			$file = $_FILES['WeightFileName'];
			$obj = new Process_OrderUpload();
			$return = $obj->importWeight($file);
		
// 			print_r($return); die;
			die(Zend_Json::encode($return));
		}
	}

	/**
	 * 获取导入批次数据
	 */
	public function getImportBatchAction() {
		
		$batch = Service_CsdCustomerImportBatch::getByCondition(array('customer_id' => Service_User::getCustomerId()), '*', 10, 1, 'ccib_id desc');
		$this->view->batch = $batch;
		
		if($this->getRequest()->isPost()) {
				
			$return = array(
					'ask' => 0,
					'message' => ''
			);
				
			$return['data'] = $batch;
			die(Zend_Json::encode($return));
		}
		
		echo Ec::renderTpl($this->tplDirectory . "order_import_batch.tpl", 'layout');
	}
	
	/**
	 * 获取导入批次数据
	 */
	public function getImportBatchDetailAction() {
		if($this->getRequest()->isPost()) {
			
			$return = array(
					'ask' => 0,
					'message' => ''
			);
			
			$ccib_id = $this->getParam('id', '');
			$batch_detail = Service_CsdCustomerImportBatchDetail::getByCondition(array('ccib_id' => $ccib_id));
			$return['data'] = $batch_detail;
			
			die(Zend_Json::encode($return));
		}
	}
	
	//获得当前shipperAdress
	public function shipperAdressInfoAction(){
		$return = array(
				'ask' => 0,
				'message' => ''
		);
		$CsiShipperTrailerAddress = new Service_CsiShipperTrailerAddress();
		$condition['customer_id'] = Service_User::getCustomerId();
		$condition['customer_channelid'] = Service_User::getChannelid();
		$showFields=array(
				'shipper_account',
				'shipper_name',
				'shipper_company',
				'shipper_countrycode',
				'shipper_province',
				'shipper_city',
				'shipper_street',
				'shipper_postcode',
				'shipper_telephone',
				'shipper_mobile',
				'shipper_email',
				'shipper_certificatetype',
				'shipper_certificatecode',
				'shipper_fax',
				'shipper_mallaccount',
				'is_default',
		);
		$rows = $CsiShipperTrailerAddress->getByCondition($condition,'*', 0, 1, array('shipper_account asc'));
		$return['ask'] =1;
		$return['data']=empty($rows)?array():$rows;
		die(Zend_Json::encode($return));
	}
	
	public function shipperAdressAction(){
		$isxhr = $this->getParam('xhr','');
		$CsiShipperTrailerAddress = new Service_CsiShipperTrailerAddress();
	 	$condition['customer_id'] = Service_User::getCustomerId();
    	$condition['customer_channelid'] = Service_User::getChannelid();
    	$showFields=array(
    			'shipper_account',
    			'shipper_name',
    			'shipper_company',
    			'shipper_countrycode',
    			'shipper_province',
    			'shipper_city',
    			'shipper_street',
    			'shipper_postcode',
    			'shipper_telephone',
    			'shipper_mobile',
    			'shipper_email',
    			'shipper_certificatetype',
    			'shipper_certificatecode',
    			'shipper_fax',
    			'shipper_mallaccount',
    			'is_default',
    	);
    	$rows = $CsiShipperTrailerAddress->getByCondition($condition,'*', 0, 1, array('shipper_account asc'));
    	$this->view->rows = $rows;
    	if(!empty($isxhr)){
    		$res = array('ack'=>0,'msg'=>'','data'=>array());
    		if(!empty($rows)){
    			$res['ack'] = 1 ;
    			$res['data'] = $rows;
    		}
    		echo json_encode($res);
    	}else
    		echo $this->view->render($this->tplDirectory . "shipper_address.tpl");
	}
	
	public function shipperAdressDelAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		if ($this->_request->isPost()) {
			$CsiShipperTrailerAddress = new Service_CsiShipperTrailerAddress();
			$paramId = $this->_request->getPost('paramId');
			if (!empty($paramId)) {
				if ($CsiShipperTrailerAddress->delete($paramId)) {
					$result['state'] = 1;
					$result['message'] = 'Success.';
				}
			}
		}
		die(Zend_Json::encode($result));
	}
	
	public function shipperAdressEditAction(){
		if ($this->_request->isPost()) {
			$return = array(
					'state' => 0,
					'message' => '',
					'errorMessage'=>array('Fail.')
			);
			$params = $this->_request->getParams();
			$row = array(
					'shipper_account'=>'',
					'shipper_name'=>'',
					'shipper_company'=>'',
					'shipper_countrycode'=>'',
					'shipper_province'=>'',
					'shipper_city'=>'',
					'shipper_street'=>'',
					'shipper_postcode'=>'',
					'shipper_telephone'=>'',
					'is_default'=>'0',
			);
			$CsiShipperTrailerAddress = new Service_CsiShipperTrailerAddress();
			$row = $CsiShipperTrailerAddress->getMatchEditFields($params,$row);
			$paramId = $row['shipper_account'];
			if (!empty($row['shipper_account'])) {
				unset($row['shipper_account']);
			}
			foreach ($row as $key => $value) {
				$row[$key] = ($value != '')?trim($value):$value;
			}
			
			$errorArr = $CsiShipperTrailerAddress->validator($row);
			//加上过滤条件
			if(!empty($row['shipper_name'])){
				if(!preg_match('/^[a-zA-Z\s\.%&\(\)\{\},\$-;#@\*\[\]【】]+$/', $row['shipper_name'])) {
					$errorArr[]= "发件人姓名不能为非英文";
				}
			}
			if(!empty($row['shipper_company'])){
				if(!preg_match('/^[a-zA-Z\s\.%&\(\)\{\},\$-;#@\*\[\]【】]+$/', $row['shipper_company'])) {
					$errorArr[]= "发件人公司不能为非英文";
				}
			}
			 
			if(!empty($row['shipper_province'])){
				if(!preg_match('/^[a-zA-Z\s]+$/', $row['shipper_province'])) {
					$errorArr[]= "发件人州省不能为非英文";
				}
			}
			if(!empty($row['shipper_city'])){
				if(!preg_match('/^[a-zA-Z\s]+$/', $row['shipper_city'])) {
					$errorArr[]= "发件人城市不能为非英文";
				}
			}
			
			if(!empty($row['shipper_telephone'])){
				if(!preg_match('/^(\d){4,25}$/', $row['shipper_telephone'])) {
					$errorArr[]= "电话格式应为4-25位纯数字";
				}
			}
			if(!empty($row['shipper_postcode'])){
				if(!preg_match('/^[0-9]{6,12}$/', $row['shipper_postcode'])) {
					$errorArr[]= "发件人邮编应为6-12位数字";
				}
			}
			
			if (!empty($errorArr)) {
				$return = array(
						'state' => 0,
						'message'=>'',
						'errorMessage' => $errorArr
				);
				die(Zend_Json::encode($return));
			}
			$row = Common_Common::arrayNullToEmptyString($row);
			$format = 'Y-m-d H:i:s';
			$row['modify_date_sys'] = date($format);
			$row['customer_id'] = Service_User::getCustomerId();
			$row['customer_channelid'] = Service_User::getChannelid();
			if (!empty($paramId)) {
				$row['is_modify'] = '1';
				$result = $CsiShipperTrailerAddress->update($row, $paramId);
				$shipper_account = $paramId;
			} else {
				$row['create_date_sys'] = date($format);
				$result = $CsiShipperTrailerAddress->add($row);
				$shipper_account = $result;
			}
		
			if($row['is_default']){
				Service_CsiShipperTrailerAddress::update(array('is_default'=>'0'), Service_User::getCustomerId(), 'customer_id');
				Service_CsiShipperTrailerAddress::update(array('is_default'=>'1'), $shipper_account, 'shipper_account');
			}
		
			if ($result) {
				$return['state'] = 1;
				$return['message'] = array('Success.');
			}
			die(Zend_Json::encode($return));
		
		}
		
	}
	
	public function consigneeAdressAction(){
		$isxhr = $this->getParam('xhr','');
		$CsiConsigneeTrailerAddress = new Service_CsiConsigneeTrailerAddress();
		$condition['customer_id'] = Service_User::getCustomerId();
		$condition['customer_channelid'] = Service_User::getChannelid();
		$showFields=array(
				'consignee_account',
				'consignee_name',
				'consignee_company',
				'consignee_countrycode',
				'consignee_province',
				'consignee_city',
				'consignee_street',
				'consignee_street1',
				'consignee_street2',
				'consignee_postcode',
				'consignee_telephone',
				'consignee_mobile',
				'consignee_email',
				'consignee_certificatetype',
				'consignee_certificatecode',
				'consignee_fax',
				'consignee_mallaccount',
				'is_default',
		);
		$rows = $CsiConsigneeTrailerAddress->getByCondition($condition,'*', 0, 1, array('consignee_account asc'));
		foreach ($rows as $k=>$v){
			$result_country = Service_IddCountry::getByField($v['consignee_countrycode'],'country_code');
			$rows[$k]['country_cnname']=$result_country['country_cnname'];
		}
		$this->view->rows = $rows;
		//print_r($rows);
		if(!empty($isxhr)){
			$res = array('ack'=>0,'msg'=>'','data'=>array());
			if(!empty($rows)){
				$res['ack'] = 1 ;
				$res['data'] = $rows;
			}
			echo json_encode($res);
		}else
			echo $this->view->render($this->tplDirectory . "consignee_address.tpl");
	}
	
	public function consigneeAdressEditAction(){
		if ($this->_request->isPost()) {
			$return = array(
					'state' => 0,
					'message' => '',
					'errorMessage'=>array('Fail.')
			);
			$params = $this->_request->getParams();
			$row = array(
					'consignee_account'=>'',
					'consignee_name'=>'',
					'consignee_company'=>'',
					'consignee_countrycode'=>'',
					'consignee_province'=>'',
					'consignee_city'=>'',
					'consignee_street'=>'',
					'consignee_street1'=>'',
					'consignee_street2'=>'',
					'consignee_postcode'=>'',
					'consignee_telephone'=>'',
					'is_default'=>'0',
			);
			$CsiConsigneeTrailerAddress = new Service_CsiConsigneeTrailerAddress();

			$row = $CsiConsigneeTrailerAddress->getMatchEditFields($params,$row);
			$paramId = $row['consignee_account'];
			if (!empty($row['consignee_account'])) {
				unset($row['consignee_account']);
			}
			foreach ($row as $key => $value) {
				$row[$key] = ($value != '')?trim($value):$value;
			}
			$errorArr = $CsiConsigneeTrailerAddress->validator($row);
			if (!empty($errorArr)) {
				$return = array(
						'state' => 0,
						'message'=>'',
						'errorMessage' => $errorArr
				);
				die(Zend_Json::encode($return));
			}
			$row = Common_Common::arrayNullToEmptyString($row);
			$format = 'Y-m-d H:i:s';
			$row['modify_date_sys'] = date($format);
			$row['customer_id'] = Service_User::getCustomerId();
			$row['customer_channelid'] = Service_User::getChannelid();
			if (!empty($paramId)) {
				$row['is_modify'] = '1';
				$result = $CsiConsigneeTrailerAddress->update($row, $paramId);
				$consignee_account = $paramId;
			} else {
				$row['create_date_sys'] = date($format);
				$result = $CsiConsigneeTrailerAddress->add($row);
				$consignee_account = $result;
			}
	
			if($row['is_default']){
				Service_CsiConsigneeTrailerAddress::update(array('is_default'=>'0'), Service_User::getCustomerId(), 'customer_id');
				Service_CsiConsigneeTrailerAddress::update(array('is_default'=>'1'), $consignee_account, 'consignee_account');
			}
	
			if ($result) {
				$return['state'] = 1;
				$return['message'] = array('Success.');
			}
			die(Zend_Json::encode($return));
	
		}
	
	}
	
	public function consigneeAdressDelAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		if ($this->_request->isPost()) {
			$CsiConsigneeTrailerAddress = new Service_CsiConsigneeTrailerAddress();
			$paramId = $this->_request->getPost('paramId');
			if (!empty($paramId)) {
				$db = Common_Common::getAdapter();
				$db->beginTransaction();
				$delflag=1;
				foreach ($paramId as $k=>$v){
					if (!$CsiConsigneeTrailerAddress->delete($v)) {
						$delflag=0;
						break;
					}
				}
				if($delflag){
					$db->commit();
					$result['state'] = 1;
					$result['message'] = 'Success.';
				}else
					$db->rollback();
			}	
		}
		die(Zend_Json::encode($result));
	}
	
	//批量导入收件人
	public function consigneeAdressUploadAction(){
		$consignee_addreeupload = new Process_ConsigneeAddressUpload();
		$uploadData = $consignee_addreeupload->upload($_FILES["connectFile"]);
		if(! empty($uploadData)){
              array_unshift($uploadData, array());
              unset($uploadData[0]);
        }
		$consignee_addreeupload->_dataProcess($uploadData);
		$errs = $consignee_addreeupload->getErr();
		if(empty($consignee_addreeupload->getErr())){
			$this->_redirect('/order/order/consignee-adress');
		}else{
			$this->view->html = $errs;
			echo $this->view->render($this->tplDirectory . "error_uploadconadress.tpl");
		}
		
	}
	//邮编记录查询
	public function getPostcodeListAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		if ($this->_request->isPost()) {
			$productcode = !empty($this->_request->getPost('dc'))?$this->_request->getPost('dc'):'G_DHL';
			$condition = array();
			$condition['countrycode'] = !empty($this->_request->getPost('cd'))?$this->_request->getPost('cd'):'';
			$condition['cityename'] = !empty($this->_request->getPost('cn'))?$this->_request->getPost('cn'):'';;
			$condition['postcode'] = !empty($this->_request->getPost('pc'))?$this->_request->getPost('pc'):'';;
			$condition['status'] = 1;
			if($productcode=='G_DHL')
				$csiPostcodeRule	=	new Service_CsiPostcodeRule();
			else 
				$csiPostcodeRule	=	new Service_CsiPostcodeRuleTnt();
			if (!empty($condition)) {
				//获取总数
				$pagesize = 50;
				$page = empty($this->_request->getPost('p'))?1:intval($this->_request->getPost('p'));
				$count = $csiPostcodeRule->getByCondition($condition,'count(*)');
				$allpage = ceil($count/$pagesize);
				if($page>$allpage){
					$result['state']   = 2;
					$result['nextpage'] = -1;
				}else{
					$res = $csiPostcodeRule->getByCondition($condition,array('id','cityename','postcode','provinceename'),$pagesize,$page);
					//如果是中国的话，DHL渠道会带上校验规则
					if(!empty($res)){
						if($condition['countrycode']=="CN"){
							//$condtion_sp['cityname'] = $condition['cityename'];
							$condtion_sp['status'] =   1;
							$condtion_sp['productcode'] =   $productcode;
							$server_csi_prs=new Service_CsiProductRuleShipper();
							$rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
						    if($rs_cisprs){
						    	foreach ($res as $k=>$v){
						    		foreach ($rs_cisprs as $vv){
						    			$_cityname = $v['cityename'];
						    			$_cityname_exits = strpos($_cityname,"-");
						    			if($_cityname_exits!==false){
						    				$_cityname=substr($_cityname,0,$_cityname_exits);
						    			}
						    			if($_cityname==$vv['cityname']){
						    				$res[$k]['dhlcount'] = $vv['countnum'];
						    				$res[$k]['citycode'] = $vv['citycode'];
						    				continue;
						    			}
						    		} 
						    	}
						    }
						}
						$result['state']   = 1;
						$result['data']    = $res;
						$result['message'] = 'Success.';
						$result['nextpage'] = $page==$allpage?-1:++$page;
						$result['totalpage'] = $allpage;
						//搜索条件
						$result['select'] = array('cd'=>$condition['countrycode'],'cn'=>$condition['cityename'],'pc'=>$condition['postcode']);
					}
				}
				
			}
		}
		die(Zend_Json::encode($result));
	}
	
	//邮编记录查询
	public function getTntPostcodeListAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		do{
			if ($this->_request->isPost()) {
				$productcode = !empty($this->_request->getPost('dc'))?$this->_request->getPost('dc'):'G_DHL';
				$condition = array();
				$condition['countrycode'] = !empty($this->_request->getPost('cd'))?$this->_request->getPost('cd'):'';
				if(!empty($this->_request->getPost('cn')))
				$condition['cityename'] = $this->_request->getPost('cn');
				if(!empty($this->_request->getPost('pc')))
				$condition['postcode'] = $this->_request->getPost('pc');
				//print_r($condition);
				$condition['status'] = 1;
				if(empty($condition['countrycode'])){
					$result['state']   = 0;
					$result['nextpage'] = -1;
					break;
				}
				if (!empty($condition)) {
					//获取总数
					$pagesize = 50;
					$page = empty($this->_request->getPost('p'))?1:intval($this->_request->getPost('p'));
					$tableName = 'csi_postcode_rule_tnt_'.strtolower($condition['countrycode']);
					$_condition = 'countrycode="'.$condition['countrycode'].'"';
					foreach ($condition as $k=>$v){
						if($k=='cityename'){
							$_condition .= ' AND cityename like "'.$condition['cityename'].'%"';
						}
						if($k=='postcode'){
							$_condition .= ' AND postcode like "'.$condition['postcode'].'%"';
						}
					}
					$db = Common_Common::getAdapterForDb3();
					$sql  = "select count(*) as count from ".$tableName." where ".$_condition;
					//echo $sql;
					$count = $db->fetchRow($sql);
					$count = $count['count'];
					$allpage = ceil($count/$pagesize);
					if($page>$allpage){
						$result['state']   = 2;
						$result['nextpage'] = -1;
						break;
					}else{
						$limitx = ($page-1)*$pagesize;
						$sql = "select * from ".$tableName." where ".$_condition." limit ".$limitx.','.$pagesize;
						//echo $sql;
						$res = $db->fetchAll($sql);
						//如果是中国的话，DHL渠道会带上校验规则
						if(!empty($res)){
							if($condition['countrycode']=="CN"){
								//$condtion_sp['cityname'] = $condition['cityename'];
								$condtion_sp['status'] =   1;
								$condtion_sp['productcode'] =   $productcode;
								$server_csi_prs=new Service_CsiProductRuleShipper();
								$rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
								if($rs_cisprs){
									foreach ($res as $k=>$v){
										foreach ($rs_cisprs as $vv){
											$_cityname = $v['cityename'];
											$_cityname_exits = strpos($_cityname,"-");
											if($_cityname_exits!==false){
												$_cityname=substr($_cityname,0,$_cityname_exits);
											}
											if($_cityname==$vv['cityname']){
												$res[$k]['dhlcount'] = $vv['countnum'];
												$res[$k]['citycode'] = $vv['citycode'];
												continue;
											}
										}
									}
								}
							}
							$result['state']   = 1;
							$result['data']    = $res;
							$result['message'] = 'Success.';
							$result['nextpage'] = $page==$allpage?-1:++$page;
							$result['totalpage'] = $allpage;
							//搜索条件
							$result['select'] = array('cd'=>$condition['countrycode'],'cn'=>empty($condition['cityename'])?'':$condition['cityename'],'pc'=>empty($condition['postcode'])?'':$condition['postcode']);
						}
					}
			
				}
			}
		}while (0);
		
		die(Zend_Json::encode($result));
	}
	
	//汇率查询
	public function getCurrencyListAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		$res = Common_DataCache::getHuilv();
		$result['state']=1;
		$result['data']    = $res;
		die(Zend_Json::encode($result));
	}
	
	//邮编记录查询
	public function getStorageAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		if ($this->_request->isPost()) {
			$condition = array();
			$condition['storage'] = !empty($this->_request->getPost('storage'))?$this->_request->getPost('storage'):'';
			if(!$condition['storage']){
				$result["state"] =-1;
				$result["message"] ="没有数据提交";
			}
			$storageStore	=	new Service_StorageStore();
			$rs = $storageStore->getByCondition($condition,'*',0,1);
			if(!empty($rs)){
				$result["state"] =1;
				$result["data"] =$rs[0];
			}
		}
		die(Zend_Json::encode($result));
	}
	
	
	/**
	 * 导出订单
	 */
	public function exportfbaAction(){
		$type = $this->getParam('type',2);
		if($type == 2){
			$order_id_arr = $this->getParam('orderId', array());
			$process = new Process_OrderfbaUpload();
			$process->baseExportProcess($order_id_arr);
		}else if($type == 1){
			//导出附件的功能
			header("Content-type:text/html;charset=utf-8");
			//导出附件
			$savepath	=	'../public/fba/save';
			$order_id = $this->getParam('orderid','');
			if(empty($order_id))
				exit;
			//获取详细信息
			$order_info = Service_CsdOrderfba::getByField($order_id);
			if(empty($order_info)){
				echo '未找到订单数据';die;
			}
			$filesavepath = '../public/fba/';
			$zipdown	= new Common_FileToZip($savepath,$order_info['shipper_hawbcode'].'.zip');
			$filelist	=	array();
			
			if($order_info['invoicefile']&&file_exists($filesavepath.'invoice/'.$order_info['invoicefile']))
				$filelist[]	=	$filesavepath.'invoice/'.$order_info['invoicefile'];
			if($order_info['packlistfile']&&file_exists($filesavepath.'invoicelist/'.$order_info['packlistfile']))
				$filelist[]	=	$filesavepath.'invoicelist/'.$order_info['packlistfile'];
			$zipdown->toZip($filelist,true);
		}
	}
	/**
	 * 打印FBA标签
	 */
	public function printfbaAction(){
		try{
			set_time_limit(0);
			$order_id_arr = $this->getParam('orderId', array());
			if(empty($order_id_arr) || !is_array($order_id_arr)){
				throw new Exception(Ec::Lang('没有需要打印的订单'));
			}
			//创建文件
			$savepath = APPLICATION_PATH.'/../public/fba/print/';
			do{
				$filename = date('YmdHis').'_'.rand(1, 10000);
			}while(file_exists($savepath.$filename.'.pdf'));
			$htmlFileName = "http://".$_SERVER['HTTP_HOST'].'/default/index/printfba1?orderId='.join(',', $order_id_arr);
			$pdfFileName  = $savepath.$filename.'.pdf';
			//shell调用xml
			if(!file_exists($pdfFileName)){
				shell_exec("wkhtmltopdf --page-height 150 --page-width 100 --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 {$htmlFileName} {$pdfFileName}");
    			//exec('/usr/local/wkhtmltox/bin/./wkhtmltopdf --page-height 150 --page-width 100 --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 {$htmlFileName} {$pdfFileName}');
			}
			//创建失败
			if(!file_exists($pdfFileName)){
				exit("创建pdf失败");
			}else{
				$this->redirect("/fba/print/{$filename}.pdf");
			}
		}catch(Exception $e){
			header("Content-type: text/html; charset=utf-8");
			echo $e->getMessage();
			exit();
		}
	}
	
//邮编记录查询
	public function uploadFileAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		try {
			if ($this->_request->isPost()) {
				$orderUploadfba  = 	new Process_OrderfbaUpload;
				//保留发票
				$saveDir = APPLICATION_PATH.'/../public/fba/';
				//分批上传
				if(isset($_FILES['invoice'])){
					$savepath = $saveDir.'invoice/';
					$upload_invoice_rs = $orderUploadfba->upload($_FILES['invoice'], $savepath,array('xls','xlsx'));
				}else if(isset($_FILES['invoicelist'])){
					$savepath = $saveDir.'invoicelist/';
					$upload_invoice_rs = $orderUploadfba->upload($_FILES['invoicelist'], $savepath,array('xls','xlsx'));
				}
				$path = $upload_invoice_rs['path'];
				if(!file_exists($savepath.$path)){
					$result['state'] = -1;
					$result['message'] = '文件保存失败';
				}else{
					$result['state'] = 1;
					$result['data'] = $path;
				}
			}
		} catch (Exception $e) {
			$result['state'] = -13;
			$result['message'] = $e->getMessage();
		}
		
		die(Zend_Json::encode($result));
	}
	
//邮编记录查询
	public function getPostCodeRuleAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		if ($this->_request->isPost()) {
			$productcode = !empty($this->_request->getPost('dc'))?$this->_request->getPost('dc'):'';
			$condition = array();
			$condition['productcode'] = $productcode;
			$condition['cityname'] = !empty($this->_request->getPost('cn'))?$this->_request->getPost('cn'):'';;
			$condition['status'] = 1;
			if (!empty($condition)) {
				$server_csi_prs=new Service_CsiProductRuleShipper();
				$res = $server_csi_prs->getByCondition($condition);
				if(!empty($res)){
					$result['state']   = 1;
					$result['data']    = $res;
					$result['message'] = 'Success.';
				}
			}	
		}
		die(Zend_Json::encode($result));
	}
	
	//常用内容
	public function dhlContentsAction(){
		$CsiDhlContents = new Service_CsiDhlContents();
		$condition['customer_id'] = Service_User::getCustomerId();
		$condition['customer_channelid'] = Service_User::getChannelid();
		$showFields=array(
				'cname',
				'ename',
				'dangerousgoods',
		);
		$rows = $CsiDhlContents->getByCondition($condition,'*', 0, 1, array('content_account asc'));
		
		$this->view->rows = $rows;
		//print_r($rows);
		echo $this->view->render($this->tplDirectory . "dhl_contents.tpl");
	}
	
	public function dhlContentsEditAction(){
		if ($this->_request->isPost()) {
			$return = array(
					'state' => 0,
					'message' => '',
					'errorMessage'=>array('Fail.')
			);
			$params = $this->_request->getParams();
			$row = array(
					'content_account'=>empty($params['content_account'])?"":$params['content_account'],
					'cname'=>empty($params['cname'])?"":trim($params['cname']),
					'ename'=>empty($params['ename'])?"":trim($params['ename']),
					'dangerousgoods'=>empty($params['dangerousgoods'])?0:1,
			);
			$CsiDhlContents = new Service_CsiDhlContents();
	
			$paramId = $row['content_account'];
			if (!empty($row['content_account'])) {
				unset($row['content_account']);
			}
			$errorArr = $CsiDhlContents->validator($row);
			if (!empty($errorArr)) {
				$return = array(
						'state' => 0,
						'message'=>'',
						'errorMessage' => $errorArr
				);
				die(Zend_Json::encode($return));
			}
			$row = Common_Common::arrayNullToEmptyString($row);
			$format = 'Y-m-d H:i:s';
			$row['modify_date_sys'] = date($format);
			$row['customer_id'] = Service_User::getCustomerId();
			$row['customer_channelid'] = Service_User::getChannelid();
			if (!empty($paramId)) {
				$row['is_modify'] = '1';
				$result = $CsiDhlContents->update($row, $paramId);
				$consignee_account = $paramId;
			} else {
				$row['create_date_sys'] = date($format);
				$result = $CsiDhlContents->add($row);
				$consignee_account = $result;
			}
	
			if ($result) {
				$return['state'] = 1;
				$return['message'] = array('Success.');
			}
			die(Zend_Json::encode($return));
	
		}
	
	}
	
	public function dhlContentsDelAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		if ($this->_request->isPost()) {
			$CsiDhlContents = new Service_CsiDhlContents();
			$paramId = $this->_request->getPost('paramId');
			if (!empty($paramId)) {
				$db = Common_Common::getAdapter();
				$db->beginTransaction();
				$delflag=1;
				foreach ($paramId as $k=>$v){
					if (!$CsiDhlContents->delete($v)) {
						$delflag=0;
						break;
					}
				}
				if($delflag){
					$db->commit();
					$result['state'] = 1;
					$result['message'] = 'Success.';
				}else
					$db->rollback();
			}
		}
		die(Zend_Json::encode($result));
	}
	
	
	public function deleteOrderAction(){
		$result = array(
				"state" => 0,
				"message" => "Fail."
		);
		if ($this->_request->isPost()) {
			$CsdOrder = new Service_CsdOrder();
			$order_id = $this->_request->getPost('order_id');
			if (!empty($order_id)) {
				$db = Common_Common::getAdapter();
				$db->beginTransaction();
				$delflag=1;
				foreach ($order_id as $k=>$v){
					if (!$CsdOrder->delete($v)) {
						$delflag=0;
						break;
					}else{
						//其他表的删除是否有必要
					}
				}
				if($delflag){
					$db->commit();
					$result['state'] = 1;
					$result['message'] = 'Success.';
				}else
					$db->rollback();
			}
		}
		die(Zend_Json::encode($result));
	}
}


