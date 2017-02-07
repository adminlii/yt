<?php
class Common_ApiServiceV1
{
    //创建订单接口
    
    //创建FBA订单
    public function createFba($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			$userInfo = Service_User::getByField($req['usercode'],'user_code');
    			$order 	  = filter_input_m($req['order']);
    			$fileData = filter_input_m($req['fileData']);
    			$shipper  = filter_input_m($req['shipper']);
    			$orderArr = array(
    			'product_code'=> 	$order['product_code'],
    			'refer_hawbcode'=> 	$order['refer_hawbcode'],
    			'boxnum'=> 	$order['boxnum'],
    			'customer_id'=>$userInfo['customer_id'],
	            'creater_id'=>$userInfo['user_id'],
    			'modify_date'=>date('Y-m-d H:i:s'),
    			'customer_channelid'=>null,
    			'invoicelistrel'=>'',
    			'invoicerel'=>'',		
	    		);
	    		if(!empty($orderArr['product_code'])){
	    			if(!in_array($orderArr['product_code'], array('FBA1'))){
	    				$return['ret'] = 22;
	    				$return['msg'] = Ec::Lang('暂不支持该运输方式');;
	    				break;
	    			}
	    		}
	    		if(empty($fileData)){
	    			$return['ret'] = 22;
	    			$return['msg'] = Ec::Lang('没有传入装箱单，发票数据');;
	    			break;
	    		}
	    		if(empty($fileData['invoicelist']['data'])||empty($fileData['invoicelist']['ext'])){
	    			$return['ret'] = 22;
	    			$return['msg'] = Ec::Lang('没有传入装箱单数据或者文件后缀名');;
	    			break;
	    		}
	    		if(empty($fileData['invoice']['data'])||empty($fileData['invoice']['ext'])){
	    			$return['ret'] = 22;
	    			$return['msg'] = Ec::Lang('没有传入发票数据或者文件后缀名');;
	    			break;
	    		}
	    		//保存文件
	    		$saveDir = APPLICATION_PATH.'/../public/fba/';
	    		$upres = $this->saveAsfile($fileData['invoicelist']['data'], $saveDir.'invoicelist/',$fileData['invoicelist']['ext']);
    			if($upres['ret']!=0){
    				$return = $upres;
    				break;
    			}
    			$orderArr['invoicelistrel'] = $upres['data'];
    			$upres = $this->saveAsfile($fileData['invoice']['data'], $saveDir.'invoice/',$fileData['invoice']['ext']);
    			if($upres['ret']!=0){
    				$return = $upres;
    				break;
    			}
    			$orderArr['invoicerel'] = $upres['data'];
    			if(empty($order['storage'])){
    				$return['ret'] = 18;
    				$return['msg'] = Ec::Lang('未选择仓库');;
    				break;
    			}
    			
    			$storageStore	=	new Service_StorageStore();
    			$condition =array();
    			$condition['storage'] = $order['storage'];
    			$storageStorers = $storageStore->getByCondition($condition,'*',0,1);
    			if(empty($storageStorers)){
    				$return['ret'] = 19;
    				$return['msg'] = Ec::Lang('未找到仓库信息');;
    				break;
    			}
    			$consignee = $storageStorers[0];
	    		$consigneeArr = array(
	    			'consignee_countrycode'=> $consignee['country'],
	    			'storage'=> $consignee['storage'],
	    			'consignee_province'=> $consignee['state'],
	    			'consignee_postcode'=> $consignee['zip'],
	    			'consignee_city'=> $consignee['city'],
	    			'consignee_street'=> $consignee['stree'],
	    		);
	    		$shipperArr = array(
	    				'shipper_name' => $shipper['shipper_name'],
	    				'shipper_company' => $shipper['shipper_company'],
	    				'shipper_countrycode' => $shipper['shipper_countrycode'],
	    				'shipper_province' => $shipper['shipper_province'],
	    				'shipper_city' => $shipper['shipper_city'],
	    				'shipper_street' => $shipper['shipper_street'],
	    				'shipper_postcode' => $shipper['shipper_postcode'],
	    				'shipper_areacode' => '',
	    				'shipper_telephone' => $shipper['shipper_telephone'],
	    				'shipper_mobile' => $shipper['shipper_mobile'],
	    				'shipper_email' => '',
	    				'shipper_certificatecode' => '',
	    				'shipper_certificatetype' => '',
	    				'shipper_fax' => '',
	    				'shipper_mallaccount' => ''
	    		);
	    		$process = new Process_Orderfba();
	    		$process->setOrder($orderArr);
	    		$process->setShipper($shipperArr);
	    		$process->setConsignee($consigneeArr);
	    		$cres = $process->createOrderTransaction('F');
    			if($cres['ask']!=1){
    				$return['ret'] = 1001;
    				$return['err_arr'] = $cres['err'];
    				break;
    			}
    			$return['ret'] = 0;
    			$return['data'] = $cres['order']['shipper_hawbcode'];
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			break;
    		}
    	}while(0);
    	return $return;
    }
    //创建DHL订单
    public function createDhl($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			$userInfo = Service_User::getByField($req['usercode'],'user_code');
    			$order =    filter_input_m($req['order']);
    			$invoice =  filter_input_m($req['productinformations']);
    			$invoice_ext =  filter_input_m($req['productinformations_ext']);
    			$consignee = filter_input_m($req['consignee']);
    			$shipper  = filter_input_m($req['shipper']);
	          	$invoice1 = filter_input_m($req['invoice']);
	          	$extraservice= filter_input_m($req['extraservice']);
	            $orderArr = array(
	                'product_code' => 'G_DHL',
	                'country_code' => strtoupper($order['country_code']),
	                'refer_hawbcode' => empty($order['refer_hawbcode'])?'':strtoupper($order['refer_hawbcode']),
	                'order_weight' => 1,
	                'order_pieces' => 1,
	                 
	                'order_length'=>10,
	                'order_width'=>10,
	                'order_height'=>10,
	            	'dangerousgoods'=>empty($order['dangerousgoods'])?0:1,
	                'buyer_id' =>$order['buyer_id'],
	                'order_create_code'=>'a',
	                'customer_id'=>$userInfo['customer_id'],
	                'creater_id'=>$userInfo['user_id'],
	                'modify_date'=>date('Y-m-d H:i:s'),
	                'mail_cargo_type' => $order['mail_cargo_type'],
	                'tms_id'=>1,
	                'customer_channelid'=>null,
	                'insurance_value' => '',
	                'insurance_value_gj' => $order['insurance_value_gj'],
	            	'invoice_print'=>empty($invoice1)?0:1,
	            	'makeinvoicedate'=> empty($invoice1['makeinvoicedate'])?'':$invoice1['makeinvoicedate'],
	            	'export_type'=> empty($invoice1['export_type'])?'':$invoice1['export_type'],
	            	'trade_terms'=> empty($invoice1['trade_terms'])?'':$invoice1['trade_terms'],
	            	'invoicenum'=> empty($invoice1['invoicenum'])?'':$invoice1['invoicenum'],
	            	'pay_type'=> empty($invoice1['pay_type'])?'':$invoice1['pay_type'],
	            	'fpnote'=> empty($invoice1['fpnote'])?'':$invoice1['fpnote'],
	            	'untread'=>0,
	            );
	            //添加一个发票类型
	            if($orderArr["invoice_print"]==1){
	            	$orderArr["invoice_type"]=$invoice1['invoice_type'];
	            }else{
	            	$orderArr["invoice_type"]=0;
	            }
	    		
	            //限制枚举
	            if(!empty($orderArr['mail_cargo_type'])){
	            	if(!in_array($orderArr['mail_cargo_type'], array(3,4))){
	            		$return['ret'] = 14;
	            		$return['msg'] = Ec::Lang('不支持的货物类型');
	            		break;
	            	}
	            }
	            
	            if(!empty($extraservice)){
	            	if($order['mail_cargo_type']==4&&$extraservice[0]!='C2'){
	            		$return['ret'] = 14;
	            		$return['msg'] = Ec::Lang('物品请选择C2类保险产品');
	            		break;
	            	}
	            	if($order['mail_cargo_type']==3&&$extraservice[0]!='C4'){
	            		$return['ret'] = 14;
	            		$return['msg'] = Ec::Lang('文件请选择C4类保险产品');
	            		break;
	            	}
	            	if($extraservice[0]=='C2'){
	            		//计算保险
	            		$huilv  = Common_DataCache::getHuilv();
	            		$curencycode = empty($invoice_ext['invoice_currencycode'])?'USD':$invoice_ext['invoice_currencycode'];
	            		if(empty($huilv[$curencycode])){
	            			$return['ret'] = 10;
	            			$return['msg'] = Ec::Lang('当前系统未缓存该币种汇率');
	            			break;
	            		}
	            		if(empty($order['invoice_totalcharge_all'])||empty($order['insurance_value_gj'])){
	            			$return['ret'] = 11;
	            			$return['msg'] = Ec::Lang('订单总价值或者订单保险价值为空');
	            			break;
	            		}
	            		$max_insurance = $order['invoice_totalcharge_all']*$huilv[$curencycode];
	            		$now_insurance = $order['insurance_value_gj'];
	            		if($max_insurance<$now_insurance){
	            			$return['ret'] = 12;
	            			$return['msg'] = Ec::Lang('保险金额不得大于申报价值');
	            			break;
	            		}
            			$orderArr['insurance_value'] = intval(($now_insurance*0.01>100?$now_insurance*0.01:100)*10)/10;
	            	}
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
	                'consignee_certificatetype' => '',
	                'consignee_certificatecode' => '',
	                'consignee_credentials_period' => '',
	                'consignee_doorplate' => '',
	            );
	    
	            $consignee['shipper_account'] = ! empty($consignee['shipper_account']) ? $consignee['shipper_account'] : '';
	            //发件人地址拼接
	            $streetArr = array();
	            if(!empty($shipper['shipper_street'])){
	            	$streetArr[] = $shipper['shipper_street'];
	            }
	            if(!empty($shipper['shipper_street2'])){
	            	$streetArr[] = $shipper['shipper_street2'];
	            }
	            if(!empty($shipper['shipper_street3'])){
	            	$streetArr[] = $shipper['shipper_street3'];
	            }
	            $street = join('||', $streetArr);
	            $shipperArr = array(
	                'shipper_name' => $shipper['shipper_name'],
	                'shipper_company' => $shipper['shipper_company'],
	                'shipper_countrycode' => $shipper['shipper_countrycode'],
	                'shipper_province' => $shipper['shipper_province'],
	                'shipper_city' => $shipper['shipper_city'],
	                'shipper_street' => $street,
	                'shipper_postcode' => $shipper['shipper_postcode'],
	                'shipper_areacode' => '',
	                'shipper_telephone' => $shipper['shipper_telephone'],
	                'shipper_mobile' => '',
	                'shipper_email' => '',
	                'shipper_certificatecode' => '',
	                'shipper_certificatetype' => '',
	                'shipper_fax' => '',
	                'shipper_mallaccount' => ''
	            );
	            
	            //dhl 中 根据货物计算 包裹信息
	            $invoice_weight	= 0;
	            $invoice_lenght = 0;
	            $invoice_width 	= 0;
	            $invoice_height	= 0;
	            foreach ($invoice as $column=>$vc){
	            	$invoice_weight+=$vc["invoice_weight"]*$vc["invoice_quantity"];
	            	$invoice_lenght>$vc["invoice_length"]?"":$invoice_lenght=$vc["invoice_length"];
	            	$invoice_width>$vc["invoice_width"]?"":$invoice_width=$vc["invoice_width"];
	            	$invoice_height>$vc["invoice_height"]?"":$invoice_height=$vc["invoice_height"];
	            	$invoiceArr[$column]=$vc;
	                if(!$vc['invoice_enname']){
	                    $vc['invoice_enname'] = $invoice_ext['invoice_enname'];
	                    $vc['invoice_cnname'] = $invoice_ext['invoice_cnname'];
	                    $vc['invoice_currencycode'] = $invoice_ext['invoice_currencycode'];
	                    $vc['invoice_shippertax'] = empty($invoice_ext['invoice_shippertax'])?'':$invoice_ext['invoice_shippertax'];
	                    $vc['invoice_consigneetax'] = empty($invoice_ext['invoice_consigneetax'])?'':$invoice_ext['invoice_consigneetax'];
	                    $vc['invoice_totalcharge_all'] = $order['invoice_totalcharge_all'];
	                    $vc['hs_code'] = empty($invoice_ext['hs_code'])?'':$invoice_ext['hs_code'];
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
	            
	            //枚举检测
	            if(!empty($invoice1)){
	            	if(empty($orderArr['export_type'])){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('制作发票时出口类型应必填');
	            		break;
	            	}
	            	if(empty($orderArr['trade_terms'])){
	            		$return['ret'] = 16;
	            		$return['msg'] = Ec::Lang('制作发票时贸易条款应必填');
	            		break;
	            	}
	            	if(empty($orderArr['pay_type'])){
	            		$return['ret'] = 17;
	            		$return['msg'] = Ec::Lang('制作发票时付款方式应必填');
	            		break;
	            	}
	            }
	            if(!empty($orderArr['export_type'])){
	            	$exitsExport_type = array('Permanent','Temporary','Repair/Return');
	            	if(!in_array($orderArr['export_type'], $exitsExport_type)){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('不支持的出口类型');
	            		break;
	            	}
	            }
	            if(!empty($orderArr['trade_terms'])){
	            	$exitstrade_terms = array(
	            		'DAP-Delivered at Place','EXW-Ex Works','FCA-Free Carrier',
	            		'CPT-Carried Paid To','CIP-Carriage and insurance Paid','DAT--Delivered at Terminal',
	            		'DDP-Delivered Duty Paid'
	            	);
	            	if(!in_array($orderArr['trade_terms'], $exitstrade_terms)){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('不支持的贸易方式');
	            		break;
	            	}
	            }
	            if(!empty($orderArr['pay_type'])){
	            	$exitspay_type = array('freight prepaid');
	            	if(!in_array($orderArr['pay_type'], $exitspay_type)){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('不支持的付费方式');
	            		break;
	            	}
	            }
	          
	            //标签打印 add
	            $labelArr = $invoice1['detail'];
	          
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
	            /* print_r($volumeArr);
	            print_r($orderArr);
	            print_r($invoiceArr);
	            print_r($labelArr);
	            print_r($extraservice);
	            print_r($shipperArr);
	            print_r($consigneeArr);
	            die; */
	            $cres = $process->createOrderTransactionApi('P');
    			if($cres['ask']!=1){
    				$return['ret'] = 1001;
    				$return['err_arr'] = $cres['err'];
    				break;
    			}
    			$return['ret'] = 0;
    			$return['data'] = $cres['order']['shipper_hawbcode'];
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			$return['msg_ext'] = $e->getMessage();
    			break;
    		}
    	}while(0);
    	return $return;
    }
    //创建TNT订单
    public function createTnt($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			$userInfo = Service_User::getByField($req['usercode'],'user_code');
    			$order = filter_input_m($req['order']);
    			$invoice = filter_input_m($req['productinformations']);
    			$invoice_ext =  filter_input_m($req['productinformations_ext']);
    			$consignee = filter_input_m($req['consignee']);
    			$shipper  = filter_input_m($req['shipper']);
    			$invoice1 = filter_input_m($req['invoice']);
    			$extraservice= filter_input_m($req['extraservice']);
    			$orderArr = array(
	                'product_code' => 'TNT',
	                'country_code' => strtoupper($order['country_code']),
	                'refer_hawbcode' => strtoupper($order['refer_hawbcode']),
	                'order_weight' => 0.01,
	                'order_pieces' => 10,
	                'order_length'=>10,
                	'order_width'=>10,
                	'order_height'=>10,
            		'dangerousgoods'=>empty($order['dangerousgoods'])?0:1,
                	'buyer_id' =>'',
	                'order_create_code'=>'a',
	                'customer_id'=>$userInfo['customer_id'],
	                'creater_id'=>$userInfo['user_id'],
	                'modify_date'=>date('Y-m-d H:i:s'),
	                'mail_cargo_type' => $order['mail_cargo_type'],
	                'tms_id'=>1,
	                'customer_channelid'=>null,
	                'insurance_value' => '',
	                'insurance_value_gj' => $order['insurance_value_gj'],
	            	'invoice_print'=>empty($invoice1)?0:1,
	            	'makeinvoicedate'=> empty($invoice1['makeinvoicedate'])?'':$invoice1['makeinvoicedate'],
	            	'export_type'=> empty($invoice1['export_type'])?'':$invoice1['export_type'],
	            	'trade_terms'=> empty($invoice1['trade_terms'])?'':$invoice1['trade_terms'],
	            	'invoicenum'=> empty($invoice1['invoicenum'])?'':$invoice1['invoicenum'],
	            	'pay_type'=> empty($invoice1['pay_type'])?'':$invoice1['pay_type'],
	            	'fpnote'=> empty($invoice1['fpnote'])?'':$invoice1['fpnote'],
	            	'untread'=>empty($order['untread'])?0:intval($order['untread']),
    				'service_code'=>$order['service_code'],
    			);
	            //添加一个发票类型
	            if($orderArr["invoice_print"]==1){
	            	$orderArr["invoice_type"]=$invoice1['invoice_type'];
	            }else{
	            	$orderArr["invoice_type"]=0;
	            }
	            //枚举检测
	            if(!empty($orderArr["service_code"])){
	            	$service_codeFilter =$orderArr['mail_cargo_type']==3?array('P15D'):array('P15N','P48N','S48F','S728','S87','S88'); 
	            	if(!in_array($orderArr["service_code"], $service_codeFilter)){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('服务类型不匹配');
	            		break;
	            	}
	            }
	            if(!empty($invoice1)){
	            	if(empty($orderArr['export_type'])){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('制作发票时出口类型应必填');
	            		break;
	            	}
	            	if(empty($orderArr['trade_terms'])){
	            		$return['ret'] = 16;
	            		$return['msg'] = Ec::Lang('制作发票时贸易条款应必填');
	            		break;
	            	}
	            	if(empty($orderArr['pay_type'])){
	            		$return['ret'] = 17;
	            		$return['msg'] = Ec::Lang('制作发票时付款方式应必填');
	            		break;
	            	}
	            }
	            if(!empty($orderArr['export_type'])){
	            	$exitsExport_type = array('Permanent','Temporary','Repair/Return');
	            	if(!in_array($orderArr['export_type'], $exitsExport_type)){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('不支持的出口类型');
	            		break;
	            	}
	            }
	            if(!empty($orderArr['trade_terms'])){
	            	$exitstrade_terms = array(
	            			'DAP-Delivered at Place','EXW-Ex Works','FCA-Free Carrier',
	            			'CPT-Carried Paid To','CIP-Carriage and insurance Paid','DAT--Delivered at Terminal',
	            			'DDP-Delivered Duty Paid'
	            	);
	            	if(!in_array($orderArr['trade_terms'], $exitstrade_terms)){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('不支持的贸易方式');
	            		break;
	            	}
	            }
	            if(!empty($orderArr['pay_type'])){
	            	$exitspay_type = array('freight prepaid');
	            	if(!in_array($orderArr['pay_type'], $exitspay_type)){
	            		$return['ret'] = 15;
	            		$return['msg'] = Ec::Lang('不支持的付费方式');
	            		break;
	            	}
	            }
	            //限制枚举
	            if(!empty($orderArr['mail_cargo_type'])){
	            	if(!in_array($orderArr['mail_cargo_type'], array(3,4))){
	            		$return['ret'] = 14;
	            		$return['msg'] = Ec::Lang('不支持的货物类型');
	            		break;
	            	}
	            }
	    		//货币类型
	            $orderArr['currencytype'] =  $invoice_ext['invoice_currencycode'];
	            $orderArr['invoice_totalcharge_all'] =  $order['invoice_totalcharge_all'];
	            $orderArr['invoice_shippertax'] =  $invoice_ext['invoice_shippertax'];
	            $orderArr['invoice_consigneetax'] =  $invoice_ext['invoice_consigneetax'];
	            $orderArr['order_info'] = $order['DESCRIPTION'];
	            
	            if(!empty($extraservice)){
	            	if($order['mail_cargo_type']==4&&$extraservice[0]!='C2'){
	            		$return['ret'] = 14;
	            		$return['msg'] = Ec::Lang('物品请选择C2类保险产品');
	            		break;
	            	}
	            	if($order['mail_cargo_type']==3&&!in_array($extraservice[0], array('C5','C6'))){
	            		$return['ret'] = 14;
	            		$return['msg'] = Ec::Lang('文件请选择C5,C6类保险产品');
	            		break;
	            	}
	            	if($extraservice[0]=='C2'){
	            		//计算保险
	            		$huilv  = Common_DataCache::getHuilv();
	            		$curencycode = empty($invoice_ext['invoice_currencycode'])?'USD':$invoice_ext['invoice_currencycode'];
	            		if(empty($huilv[$curencycode])){
	            			$return['ret'] = 10;
	            			$return['msg'] = Ec::Lang('当前系统未缓存该币种汇率');
	            			break;
	            		}
	            		if(empty($order['invoice_totalcharge_all'])){
	            			$return['ret'] = 11;
	            			$return['msg'] = Ec::Lang('订单总价值为空');
	            			break;
	            		}
	            		$max_insurance = $order['invoice_totalcharge_all']*$huilv[$curencycode];
	            		$max_insurance = intval($max_insurance*100)/100;
	            		$orderArr['insurance_value_gj'] = $max_insurance;
	            		$orderArr['insurance_value'] = intval(($max_insurance>10000?$max_insurance*0.0015:10)*10)/10;
	            	}
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
	                'consignee_certificatetype' => '',
	                'consignee_certificatecode' => '',
	                'consignee_credentials_period' => '',
	                'consignee_doorplate' => '',
	            );
	    
	            $consignee['shipper_account'] = ! empty($consignee['shipper_account']) ? $consignee['shipper_account'] : '';
	            //发件人地址拼接
	            $streetArr = array();
	            if(!empty($shipper['shipper_street'])){
	            	$streetArr[] = str_replace('||', ' ',$shipper['shipper_street']);
	            }
	            if(!empty($shipper['shipper_street2'])){
	            	$streetArr[] = str_replace('||', ' ',$shipper['shipper_street2']);
	            }
	            if(!empty($shipper['shipper_street3'])){
	            	$streetArr[] = str_replace('||', ' ',$shipper['shipper_street3']);
	            }
	            $street = join('||', $streetArr);
	            $shipperArr = array(
	                'shipper_name' => $shipper['shipper_name'],
	                'shipper_company' => $shipper['shipper_company'],
	                'shipper_countrycode' => $shipper['shipper_countrycode'],
	                'shipper_province' => $shipper['shipper_province'],
	                'shipper_city' => $shipper['shipper_city'],
	                'shipper_street' => $street,
	                'shipper_postcode' => $shipper['shipper_postcode'],
	                'shipper_areacode' => '',
	                'shipper_telephone' => $shipper['shipper_telephone'],
	                'shipper_mobile' => '',
	                'shipper_email' => '',
	                'shipper_certificatecode' => '',
	                'shipper_certificatetype' => '',
	                'shipper_fax' => '',
	                'shipper_mallaccount' => ''
	            );
	            $invoiceArr = array('pack'=>$invoice['pack'],'invoice'=>$invoice['packdetail']);
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
	            $process->setOrder($orderArr);
	            $process->setInvoice($invoiceArr);
	            $process->setExtraservice($extraservice);
	            $process->setShipper($shipperArr);
	            $process->setConsignee($consigneeArr);
    			$cres = $process->createOrderTransactionApi('P');
    			if($cres['ask']!=1){
    				$return['ret'] = 1001;
    				$return['err_arr'] = $cres['err'];
    				break;
    			}
    			$return['ret'] = 0;
    			$return['data'] = $cres['order']['shipper_hawbcode'];
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			$return['msg_ext'] = $e->getMessage();
    			break;
    		}
    	}while(0);
    	return $return;
    }
    //创建ESB订单
    public function create($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
		    	 $userInfo = Service_User::getByField($req['usercode'],'user_code');
		    	 $order = filter_input_m($req['order']);
		    	 $invoice = filter_input_m($req['invoice']);
		    	 $consignee = filter_input_m($req['consignee']);
		    	 $shipper  = filter_input_m($req['shipper']);
		    	 if(empty($order['country_code'])||empty($order['product_code'])){
		    	 	$return['ret'] = 9;
		    	 	$return['msg'] = Ec::Lang('产品名或者目的地国家未填写');
		    	 	break;
		    	 }
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
		    	 		
		    	 		'order_create_code'=>'a',
		    	 		'customer_id'=>$userInfo['customer_id'],
		    	 		'creater_id'=>$userInfo['user_id'],
		    	 		'modify_date'=>date('Y-m-d H:i:s'),
		    	 		'mail_cargo_type' => $order['mail_cargo_type'],
		    	 		'tms_id'=>$order['tms_id'],
		    	 		'customer_channelid'=>null,
		    	 		//'insurance_value' => trim($order['insurance_value1']),
		    	 		'battery'=>empty($order['battery'])?'':$order['battery'],
		    	 );
		    	 //限制枚举
		    	 if(!empty($orderArr['mail_cargo_type'])){
		    	 	if(!in_array($orderArr['mail_cargo_type'], array(1,2,3,4))){
		    	 		$return['ret'] = 14;
		    	 		$return['msg'] = Ec::Lang('不支持的货物类型');
		    	 		break;
		    	 	}
		    	 }
		    	 $volumeArr=array(
		    	 		'length'=>$order['order_length'],
		    	 		'width'=>$order['order_width'],
		    	 		'height'=>$order['order_height'],
		    	 
		    	 );
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
		    	 // php hack
		    	 if(! empty($invoice)){
		    	 	array_unshift($invoice, array());
		    	 	unset($invoice[0]);
		    	 }
		    	 $process = new Process_Order();
		    	 $process->setVolume($volumeArr);
		    	 $process->setOrder($orderArr);
		    	 $process->setInvoice($invoice);
		    	 $process->setShipper($shipperArr);
		    	 $process->setConsignee($consigneeArr);
		    	 //             $process
		    	 $cres = $process->createOrderTransactionApi('P');
		    	 if($cres['ask']!=1){
		    	 	$return['ret'] = 1001;
		    	 	$return['err_arr'] = $cres['err'];
		    	 	break;
		    	 }
		    	 $return['ret'] = 0;
		    	 $return['data'] = $cres['order']['shipper_hawbcode'];
    	 } catch (Exception $e) {
    	 	$return['ret'] = -13;
    	 	$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    	 	break;
    	 }
    	 }while(0);
    	 return $return;
    }
    //获取标签（不支持批量）
 	public function getLabel($req){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
		    	 $userInfo = Service_User::getByField($req['usercode'],'user_code');
		    	 $shipper_hawbcode = $req['order_num'];
		    	 if(empty($shipper_hawbcode)){
		    	 	$return['ret'] = 15;
		    	 	$return['msg'] = Ec::Lang('查询单号为空');
		    	 	break;
		    	 }
		    	 $type = 'org';
		    	 $order = Service_CsdOrder::getByField($shipper_hawbcode, 'shipper_hawbcode');
		    	 if(! $order){
		    	 	//去fba里查
		    	 	$type = 'fba';
		    	 	$order = Service_CsdOrderfba::getByField($shipper_hawbcode, 'shipper_hawbcode');
		    	 	if(! $order){
		    	 		$return['ret'] = 16;
		    	 		$return['msg'] = Ec::Lang('订单不存在');
		    	 		break;
		    	 	}
		    	 }
		    	 
		    	
		    	 
		    	 if($order['customer_id'] != $userInfo['customer_id']){
		    	 	$return['ret'] = 17;
		    	 	$return['msg'] = Ec::Lang('非法操作');
		    	 	break;
		    	 }
		    	 
		    	 if($type=='org'){
		    	 	//如果订单不是已预报给出提示
		    	 	if($order['order_status']=='D'){
		    	 		$err_condition = array(
		    	 				"order_id" => $order['order_id'],
		    	 		);
		    	 		$orderWrongMsg = Service_OrderProcessing::getByCondition(
		    	 				$err_condition,
		    	 				array("order_processing.ops_note", "order_processing.ops_status","order_processing.ems_status"),
		    	 				20,
		    	 				1,
		    	 				array('order_processing.order_id'));
		    	 		foreach($orderWrongMsg as $wk => $wv){
		    	 			$error_msg = $wv['ops_note'];
		    	 			//$ems_status = $wv['ems_status'];
		    	 		}
		    	 		$return['ret'] = 23;
		    	 		$return['msg'] = Ec::Lang('该订单是问题件;'.$error_msg);
		    	 		break;
		    	 	}
		    	 	if($order['order_status']=='S'){
		    	 		$return['ret'] = 24;
		    	 		$return['msg'] = Ec::Lang('该订单还在预报中');
		    	 		break;
		    	 	}
		    	 	if(!empty($order['small_hawbcode'])){
		    	 		$order['server_hawbcode'] = $order['small_hawbcode'];
		    	 	}
		    	 	$printParam["Data"][] = $order['server_hawbcode'];
		    	 	$printParam["Version"] = "0.0.0.3";
		    	 	$printParam["RequestTime"] = date("Y-m-d H:i:s");
		    	 	$printParam["RequestId"] = "a2b23daa-a519-48cc-b5c6-e0ebbfeada2b";
		    	 	$pdfPrintParamJson = Zend_Json::encode($printParam);
		    	 	$process = new Common_FastReport ('');
		    	 	//$return = $process->MakeLableFileToBase64($configInfoJson, $orderInfoJson, $pdfPrintInfoJson);
		    	 	$res = $process->PrintLabel($pdfPrintParamJson, "POST");
		    	 	if($res['ack'] == 1) {
		    	 		$pdfData = $res["data"]["Data"];
		    	 		$return['ret'] = 0;
		    	 		$return['data'] = $pdfData;
		    	 		break;
		    	 	} else {
		    	 		$return['ret'] = 1001;
		    	 		$return['msg'] = $res['message'];;
		    	 		break;
		    	 	}
		    	 	
		    	 }else{
		    	 	//创建文件
		    	 	$savepath = APPLICATION_PATH.'/../public/fba/print/';
		    	 	do{
		    	 		$filename = date('YmdHis').'_'.rand(1, 10000);
		    	 	}while(file_exists($savepath.$filename.'.pdf'));
		    	 	$htmlFileName = "http://".$_SERVER['HTTP_HOST'].'/default/index/printfba1?orderId='.$order['order_id'];
		    	 	$pdfFileName  = $savepath.$filename.'.pdf';
		    	 	//shell调用xml
		    	 	if(!file_exists($pdfFileName)){
		    	 		if(ENVIRONMENT=='dev')
		    	 			shell_exec("wkhtmltopdf --page-height 150 --page-width 100 --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 {$htmlFileName} {$pdfFileName}");
		    	 		else
		    	 			exec("/usr/local/wkhtmltox/bin/./wkhtmltopdf --page-height 150 --page-width 100 --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 {$htmlFileName} {$pdfFileName}");
		    	 	}
		    	 	//创建失败
		    	 	if(!file_exists($pdfFileName)){
		    	 		$return['ret'] = 204;
		    	 		$return['msg'] =  Ec::Lang('系统繁忙，请稍后重试');
		    	 		break;
		    	 	}else{
		    	 		$pdfData = base64_encode(file_get_contents($pdfFileName));
		    	 		$return['ret'] = 0;
		    	 		$return['data'] = $pdfData;
		    	 		break;
		    	 	}
		    	 }
    	 } catch (Exception $e) {
    	 	$return['ret'] = -13;
    	 	$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    	 	break;
    	 }
    	 }while(0);
    	 return $return;
    }
    
    
    //fba转存文件
    private  function saveAsfile($filedata,$savepath,$ext=FALSE){
    	$return = array('ret'=>-1,'msg'=>'','data'=>array());
    	do{
    		try {
    			if(!$ext){
    				$return['ret'] = 20;
    				$return['msg'] = Ec::Lang('文件名后缀未填写');
    				break;
    			}
    			$available = array('xls','xlsx');
    			if(!in_array($ext,$available)){
    				$return['ret'] = 21;
    				$return['msg'] = '当前只支持'.join(',', $available).'文件格式';
    				break;
    			}
    			
    			//转存
    			do{
    				$filename = date('YmdHis').'_'.rand(1, 10000).'.'.$ext;
    			}while(file_exists($savepath.$filename));
    			file_put_contents($savepath.$filename, base64_decode($filedata));
    			$return['ret'] = 0;
    			$return['data'] = $filename;
    		} catch (Exception $e) {
    			$return['ret'] = -13;
    			$return['msg'] = Ec::Lang('服务器繁忙请稍后尝试');
    			break;
    		}
    	}while(0);
    	return $return;
    }
}