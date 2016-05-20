<?php
class Order_ReportController extends Ec_Controller_Action
{

    public function preDispatch()
    {}

    public function versionAction()
    {
        $process = new Common_Report('ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL');
        $rs = $process->GetVersion();
        header("Content-type: text/html; charset=utf-8");
        print_r($rs);
        exit();
    }

    public function printLabelAction()
    {
//     	echo "1->"; print_r(date('Y-m-d H:i:s:S'));
    	set_time_limit(0);
    	ini_set('memory_limit', '500M');
    	
        $params = $this->getRequest()->getParams();
        $LableFileType = $this->getParam('LableFileType', '1');
        $this->view->LableFileType = $LableFileType;
        $LablePaperType = $this->getParam('LablePaperType', '1');
        $this->view->LablePaperType = $LablePaperType;
        $LableContentType = $this->getParam('LableContentType', '1');
        $this->view->LableContentType = $LableContentType;
        
        $PrintDeclareInfoSign = $this->getParam('PrintDeclareInfoSign', 'N');
        $InsuranceSign = $this->getParam('InsuranceSign', 'N');
        $HighValueSign = $this->getParam('HighValueSign', 'N');
        $PrintTimeSign = $this->getParam('PrintTimeSign', 'N');
        
        $printWeight = $this->getParam('printWeight', 'N');
        $printBuyerID = $this->getParam('printBuyerID', 'N');
        // print_r($printBuyerID);exit;
        $order_id_arr = $this->getParam('order_id', array());
        
        if(empty($order_id_arr)){
            header("Content-type: text/html; charset=utf-8");
            echo Ec::Lang('没有需要打印的订单');
            exit();
            // throw new Exception(Ec::Lang('没有需要打印的订单'));
        }
        $errArr = array();
        // print_r($params);exit;
        $configInfo = array(
            // 标签文件类型，参照标签文件类型
            // 1 PNG文件
            // 2 PDF文件
            'LableFileType' => $LableFileType,
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
            'LableContentType' => $LableContentType
        );
        
        // PDF 打印信息
        $pdfPrintInfo = array();
        
        $orderInfoArr = array();
        $countrys = Common_DataCache::getCountry();
        // print_r($countrys);exit;
        $db = Common_Common::getAdapterForDb2();
        foreach($order_id_arr as $order_id){
            try{
                $order = Service_CsdOrder::getByField($order_id, 'order_id');
                if(! $order){
                    throw new Exception(Ec::Lang('订单不存在'));
                }
                if($order['customer_id'] != Service_User::getCustomerId()){
                    throw new Exception(Ec::Lang('非法操作'));
                }
                // 历史数据 start
                $con = array(
                    'order_id' => $order_id
                );
                $invoice = Service_CsdInvoice::getByCondition($con, '*', 0, 0, 'invoice_id asc');
                if(empty($invoice)){
                    throw new Exception(Ec::Lang('申报信息不存在'));
                }
                foreach($invoice as $k => $v){
                    $v['invoice_unitcharge'] = $v['invoice_totalcharge'] ? ($v['invoice_totalcharge'] / $v['invoice_quantity']) : 0;
                    $v['invoice_unitweight'] = $v['invoice_totalWeight'] ? ($v['invoice_totalWeight'] / $v['invoice_quantity']) : 0;
                    $invoice[$k] = $v;
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
                	if($row['extra_servicecode'] == 'C0') {
                		$InsuranceSign = 'Y';
                	} else {
                		$extraserviceKind = $extraserviceKindByKey[$v ['extra_servicecode']];
                		if(!empty($extraserviceKind) && $extraserviceKind['extra_service_group'] == 'C0') {
                			$InsuranceSign = 'Y';
                		}
                	}
                }
                // 额外服务 -- 结束
                
                $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id, 'order_id');
                if(! $shipperConsignee){
                    throw new Exception(Ec::Lang('收发件人信息不存在'));
                }
                
                $sql = "select * from pbr_public_shipper_address where product_code='{$order['product_code']}' and (country_code='{$order['country_code']}' or country_code='' or country_code is null) order by country_code desc;";
                $public_shipper_address = $db->fetchRow($sql);
                if($public_shipper_address){ // 如果有公共发件人，取公共发件人信息
                    $tmp = array(
                        'server_channelid' => $public_shipper_address['server_channelid'],
                        'country_code' => $public_shipper_address['country_code'],
                        'shipper_account' => $public_shipper_address['shipper_account'],
                        'shipper_name' => $public_shipper_address['shipper_name'],
                        'shipper_company' => $public_shipper_address['shipper_company'],
                        'shipper_countrycode' => $public_shipper_address['shipper_countrycode'],
                        'shipper_province' => $public_shipper_address['shipper_province'],
                        'shipper_city' => $public_shipper_address['shipper_city'],
                        'shipper_street' => $public_shipper_address['shipper_street'],
                        'shipper_postcode' => $public_shipper_address['shipper_postcode'],
                        'shipper_areacode' => $public_shipper_address['shipper_areacode'],
                        'shipper_telephone' => $public_shipper_address['shipper_telephone'],
                        'shipper_mobile' => $public_shipper_address['shipper_mobile'],
                        'shipper_email' => $public_shipper_address['shipper_email'],
                        'shipper_certificatetype' => $public_shipper_address['shipper_certificatetype'],
                        'shipper_certificatecode' => $public_shipper_address['shipper_certificatecode'],
                        'shipper_fax' => $public_shipper_address['shipper_fax'],
                        'shipper_mallaccount' => $public_shipper_address['shipper_mallaccount']
                    );
                    $shipperConsignee = array_merge($shipperConsignee, $tmp);
                }
                
                $productKind = Service_CsiProductkind::getByField($order['product_code'], 'product_code');
                $productRule = Service_PbrProductrule::getByField($order['product_code'], 'product_code');
                $label_config_id = $productRule['label_config_id'];
                
                // 标签模板类型
                $sql = "select * from pbr_label_config where label_config_id='{$label_config_id}';";
                $labelConfig = $db->fetchRow($sql);
                if(! $labelConfig || empty($labelConfig['atd_label_code'])){
                    throw new Exception($order['product_code'] . Ec::Lang('找不到标签模板类型'));
                }
                
                // 查找对应的PDF类型
                $sql = "select * from pbr_label_type where label_config_id='{$label_config_id}' and print_type = '{$LablePaperType}';";
                $pdfLabelConfig = $db->fetchRow($sql);
                if(!empty($pdfLabelConfig)) {
                	$pdfPrintInfo = array(
                			// 标签模板名称
                			'LabelName' => $pdfLabelConfig['label_name'],
                			// 报关单模板名称
                			'CustomName' => $pdfLabelConfig['custom_name'],
                			// 配货单模板名称
                			'InvoiceName' => $pdfLabelConfig['invoice_name']
                	);
                }
                
//                 print_r($pdfPrintInfo);die;
                $atd_label_code = $labelConfig['atd_label_code'];
                $returnAdd = null;
                if($labelConfig['return_address_type'] == 'Y'){
                    // 回邮地址
                    $sql = "select * from pbr_return_address where label_config_id='{$label_config_id}' and country_code='{$order['country_code']}';";
                    $returnAdd = $db->fetchRow($sql);
                    if(! $returnAdd){
                        $sql = "select * from pbr_return_address where label_config_id='{$label_config_id}' order by rand();"; // 随机取一条
                        $returnAdd = $db->fetchRow($sql);
                    }
                }
                
                $arrivalZone = null;	// 拣货分区
                if($labelConfig['arrival_zone_type'] == 'Y'){
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
                    $r = $db->query($sql,array('country_city'=>$shipperConsignee['consignee_city']));
//                     echo $r;exit; 
                    $arrivalZone = $r->fetchAll();
                    if($arrivalZone){
                    	$arrivalZone = array_shift($arrivalZone);
                    }
                }
                
                $chargeZone = null;  	// 计费分区
                if($labelConfig['charge_zone_type'] == 'Y'){
                    $sql = "
                            SELECT
                            	*
                            FROM
                            	pbr_label_charge_zone
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
                    $r = $db->query($sql,array('country_city'=>$shipperConsignee['consignee_city']));
//                     echo $r;exit; 
                    $zoneCode = $r->fetchAll();
                    if($zoneCode){
                    	$chargeZone = array_shift($zoneCode);
                    }
                }
                
                $orderInfo = array(
                    // 标签模版类型(请参考模版类型表格)
                    // NORMALY 通用快件标签
                    // SGPY 新加坡小包平邮标签
                    // SGGH 新加坡小包挂号标签
                    // HKPY 香港小包平邮标签
                    // HKGH 香港小包挂号标签
                    // LYTPYAU 联邮通平邮澳洲标签
                    // LYTPYOT 联邮通平邮欧洲地区标签
                    // LYTGHUS 联邮通挂号美国标签
                    // LYTGHAU 联邮通挂号澳洲标签
                    // LYTGHOT 联邮通挂号欧洲地区标签
                    // CNGH 中国小包挂号标签
                    // CNPY 中国小包平邮标签
                    'LabelTemplateType' => strtoupper($atd_label_code),
                    // 客户代码
                    'CustomerCode' => '' . Service_User::getCustomerCode(),
                    // 客户订单号码，由客户自己定义
                    'OrderNo' => $order['refer_hawbcode'],
                    // 运单号
                    'Shipper_hawbcode' => $order['shipper_hawbcode'],
                    // 销售产品/运输方式代码
                    'ProductCode' => $order['product_code'],
                    // 销售产品/运输方式名称
                    'ProductName' => $productKind['product_cnname'] . '',
                    // 产品打印标记/产品英文简称
                    'ProductPrintSign' => $productKind['product_print_sign'] . '',
                    // 服务商跟踪号码
                    'TrackingNumber' => $order['server_hawbcode'],
                    
                    // 发件人姓名
                    'ShipperName' => $shipperConsignee['shipper_name'],
                    // 发件人公司名
                    'ShipperCompanyName' => $shipperConsignee['shipper_company'],
                    // 发件人邮编
                    'ShipperPostCode' => $shipperConsignee['shipper_postcode'],
                    // 发件人电话
                    'ShipperPhone' => $shipperConsignee['shipper_telephone'],
                    // 发件人地址
                    'ShipperAddress' => $shipperConsignee['shipper_street'],
                    // 发件人国家二字码
                    'ShipperCountryCode' => $shipperConsignee['shipper_countrycode'],
                    // 发件人国家英文名
                    'ShipperCountryEName' => $countrys[$shipperConsignee['shipper_countrycode']] ? $countrys[$shipperConsignee['shipper_countrycode']]['country_enname'] : $shipperConsignee['shipper_countrycode'],
                    // 发件人国家中文名
                    'ShipperCountryName' => $countrys[$shipperConsignee['shipper_countrycode']] ? $countrys[$shipperConsignee['shipper_countrycode']]['country_enname'] : $shipperConsignee['shipper_countrycode'],
                    
                    // 国家二字码
                    'DestinationCountryCode' => $order['country_code'],
                    // 国家英文名
                    'DestinationCountryEName' => $countrys[$order['country_code']] ? $countrys[$order['country_code']]['country_enname'] : $order['country_code'],
                    // 国家中文名
                    'DestinationCountryName' => $countrys[$order['country_code']] ? $countrys[$order['country_code']]['country_cnname'] : $order['country_code'],
                    // 回邮地址
                    'ReturnAddress' => $returnAdd ? $returnAdd['return_address'] : '',
                    // 收件人姓名
                    'ConsigneeName' => $shipperConsignee['consignee_name'],
                    // 收货人公司名
                    'ConsigneeCompanyName' => $shipperConsignee['consignee_company'],
                    // 收件人邮编
                    'ConsigneePostCode' => $shipperConsignee['consignee_postcode'],
                    // 收件人手机
                    'ConsigneePhone' => $shipperConsignee['consignee_mobile'],
                    // 收件人电话
                    'ConsigneeTelephone' => $shipperConsignee['consignee_telephone'],
                    // 收件人城市
                    'ConsigneeCity' => $shipperConsignee['consignee_city'],
                    // 收件人省/州
                    'ConsigneeProvince' => $shipperConsignee['consignee_province'],
                    // 收件人街道(地址1)
                    'ConsigneeStreet' => $shipperConsignee['consignee_street'],
                    // 收件人地址2
                    'ConsigneeAddress2' => $shipperConsignee['consignee_street2'],
                    // 收件人地址3
                    'ConsigneeAddress3' => $shipperConsignee['consignee_street3'],
                    // 收件人地址
                    'ConsigneeAddress' => $shipperConsignee['consignee_street'] . ' ' . $shipperConsignee['consignee_city'] . ' ' . $shipperConsignee['consignee_province'],
                    // 买家ID
                    'BuyerID' => $printBuyerID == 'Y' ? $order['buyer_id'] : '',
                    // 标签上打印配货信息标记 (Y:打印 N:不打印) 默认 N:不打印
                    'PrintDeclareInfoSign' => $PrintDeclareInfoSign,
                    // 保险标记 (Y:保险 N:不保险) 默认 N:不保险
                    'InsuranceSign' => $InsuranceSign,
                    // 是否高价值 (Y:是 N:否) 默认 N:否
                    'HighValueSign' => $HighValueSign,
                    // 打印时间标记 (Y:打印 N:不打印) 默认 Y:打印
                    'PrintTimeSign' => $PrintTimeSign,
                    // 是否需要退件 (Y:需要退件 N:不需要退件) 默认 N:不需要退件
                    'ReturnSign' => empty($order['return_sign'])?'N':strtoupper($order['return_sign']),
                    // 重量（单位：KG）默认0.2KG
                    'Weight' => empty($order['order_weight']) || floatval($order['order_weight']) <= 0 ? 0.2 : $order['order_weight'],
                    // 件数(快件一票多件时打印多份)
                    'Pieces' => $order['order_pieces'],
                    // 出货分区代码
                    'ShipmentZoneCode' => $arrivalZone && $arrivalZone['zone_code'] ? $arrivalZone['zone_code'] : '',
                    // 计费分区代码
                    'Charge_zone' => $chargeZone && $chargeZone['zone_code'] ? $chargeZone['zone_code'] : '',
                    // 标签账号信息
                    'LabelAccount' => $labelConfig && $labelConfig['label_account'] ? $labelConfig['label_account'] : '',
                    // 标签签名信息
                    'LabelSignature' => $labelConfig && $labelConfig['label_signature'] ? $labelConfig['label_signature'] : '',
                	// 标签客户账号
                	'Label_Customer_account' => $labelConfig && $labelConfig['label_customer_account'] ? $labelConfig['label_customer_account'] : '',
                	// 标签特殊标记
                	'Label_Special_mark' => $labelConfig && $labelConfig['label_special_mark'] ? $labelConfig['label_special_mark'] : '',
                	// 航空公司
                	'LabelAirlineCompany' => $labelConfig && $labelConfig['label_airline_company'] ? $labelConfig['label_airline_company'] : '',
                	// 单位名称
                	'LabelCompanyName' => $labelConfig && $labelConfig['label_company_name'] ? $labelConfig['label_company_name'] : '',
                	// 检视人
                	'LabelExaminePeople' => $labelConfig && $labelConfig['label_examine_people'] ? $labelConfig['label_examine_people'] : '',
                    // 额外服务名称(多请用分隔符分割)
                    'Extraservice' => implode(',', $extservice_str),
                    // 报关单模版类型(请参考模版类型表格)
                    // NORMALY 通用报关单
                    // ZYBG 中邮报关单
                    'CustomsDeclareTemplateType' => $productRule['web_invoice_type_code'] ? $productRule['web_invoice_type_code'] : 'NORMALY',
                    // 报关申报类型（默认 1:Gift）
                    // 1 Gift
                    // 2 Documents
                    // 3 Commercial sample
                    // 9 Other
                    
                    'DeclarationType' => empty($order['mail_cargo_type']) ? 4 : $order['mail_cargo_type'],
                    // 货物描述
                    'DeclareInvoiceArray' => array(),
                		
                	// 材积信息
                    'ProductSpecifications' => '',
                		
                	// 标签URL	
                	'LabelUrl' => 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"] . '/default/index/get-label/code/' . $order['shipper_hawbcode'] . ".png",
                	'InvoiceUrl' => 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"] . '/default/index/get-invoice-label/code/' . $order['shipper_hawbcode'] . ".png",
//                 	'LabelUrl' => 'http://120.24.63.108:8081/default/index/get-label/code/' . $order['shipper_hawbcode'] . ".png",
//                 	'InvoiceUrl' => 'http://120.24.63.108:8081/default/index/get-invoice-label/code/' . $order['shipper_hawbcode'] . ".png",
                );
                
                $DeclareInvoiceArray = array();
                foreach($invoice as $v){
                    // 货物描述
                    $DeclareInvoiceArray[] = array(
                        // 货物描述
                        'GoodsDescription' => $v['invoice_enname'],
                        // 货物描述
                        'GoodsDescriptionCN' => $v['invoice_cnname'],
                        // 备注
                        'Remarks' => $v['invoice_note'],
                        // 单价
                        'UnitPrice' => $v['invoice_unitcharge'],
                        // 数量
                        'Quantity' => $v['invoice_quantity'],
                        // 重量
                        'DeclareWeight' => $v['invoice_unitweight'],
                        // 总重量
                        'TotalWeight' => $v['invoice_totalWeight'],
                        // 总价
                        'TotalCharge' => $v['invoice_totalcharge']
                    );
                }
                
                $orderInfo['DeclareInvoiceArray'] = $DeclareInvoiceArray;
                
                $orderInfoArr[] = $orderInfo;

                //获取打印标签
                
                $printParam["Data"][] = $order['server_hawbcode'];

            }catch(Exception $e){
                $errArr[] = $e->getMessage();
            }
        }
//         echo "---";
//         print_r($orderInfoArr);die;
        if(! empty($errArr)){
            header("Content-type: text/html; charset=utf-8");
            foreach($errArr as $err){
                echo $err . '<br/>';
            }
            exit();
        }
        
        
        $configInfoJson = Zend_Json::encode($configInfo);
        $orderInfoJson = Zend_Json::encode($orderInfoArr);
        $pdfPrintInfoJson = Zend_Json::encode($pdfPrintInfo);

        $printParam["Version"] = "0.0.0.3";
        $printParam["RequestTime"] = date("Y-m-d H:i:s");
        $printParam["RequestId"] = "a2b23daa-a519-48cc-b5c6-e0ebbfeada2b";
        $pdfPrintParamJson = Zend_Json::encode($printParam);

        $reportPrintTokenConfig = Service_Config::getByField('REPORT_PRINT_TOKEN', 'config_attribute');
        if(!$reportPrintTokenConfig){
        	throw new Exception('请配置REPORT_PRINT_TOKEN');
        }
        $token = 'ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL';
        $token = $reportPrintTokenConfig['config_value'];

//         echo "<br/>2->"; print_r(date('Y-m-d H:i:s:S'));
        
        // PDF 输出
        if($LableFileType == 2) {
        	
        	if(empty($pdfPrintInfo)) {
        		header("Content-type: text/html; charset=utf-8");
        		echo '该产品暂不支持PDF打印<br/>';
        		exit();
        	}
        	
        	$process = new Common_FastReport ($token);
        	//$return = $process->MakeLableFileToBase64($configInfoJson, $orderInfoJson, $pdfPrintInfoJson);
            $return = $process->PrintLabel($pdfPrintParamJson, "POST");
//         	echo "<br/>3->"; print_r(date('Y-m-d H:i:s:S')); die;
        	if($return['ack'] == 1) {
                $pdfData = $return["data"]["Data"];
                $trackingCodes = $printParam["Data"];
                $PdfReturn = $process->CreatePdfFile($pdfData,$trackingCodes);
                echo  Zend_Json::encode($return);
        		header("Location: {$PdfReturn}" );
        		exit();
        	} else {
        		header("Content-type: text/html; charset=utf-8");
        		print_r($return['message']);
        		exit();
        	}
        } else {
        
        	// 图片输出
	        $process = new Common_Report ( $token );
	//         header("Content-type: text/html; charset=utf-8");
	//         print_r($configInfoJson);
	//         print_r($orderInfoJson);exit;
	        $return = $process->MakeLableFileToBase64($configInfoJson, $orderInfoJson);
	        // print_r($return);exit;
	        if($return['ask'] == 1){
	            $rs = $return['rs'];
	            if($rs['Ack'] == 1){
	                $this->view->LableFileType = $configInfo['LableFileType'];
	                $this->view->lableArr = $rs['LableArray'];
	                echo $this->view->render('order/views/report/print_report.tpl');
	            }else{
	                header("Content-type: text/html; charset=utf-8");
	                $ErrorArray = $rs['ErrorArray'];
	                foreach($ErrorArray as $v){
	                    echo $v['Code'];
	                    echo $v['CnMessage'];
	                    echo $v['EnMessage'];
	                    echo "<br/>";
	                }
	            }
	        } else {
	            header("Content-type: text/html; charset=utf-8");
	            print_r($return['message']);
	            exit();
	        }
        }
    }

    public function labelAction()
    {
        $configInfo = array(
            // 标签文件类型，参照标签文件类型
            // 1 PNG文件
            // 2 PDF文件
            'LableFileType' => '2',
            // 标签纸张类型，参照标签纸张类型
            // 1 标签纸张
            // 2 A4纸张/ A4不干胶纸张
            'LablePaperType' => '3',
            // 标签内容类型，参照标签内容类型
            // 1 标签
            // 2 报关单
            // 3 配货单
            // 4 标签+报关单
            // 5 标签+配货单
            // 6 标签+报关单+配货单
            'LableContentType' => '6'
        );
        $orderInfo = array(
            // 标签模版类型(请参考模版类型表格)
            // NORMALY 通用快件标签
            // SGPY 新加坡小包平邮标签
            // SGGH 新加坡小包挂号标签
            // HKPY 香港小包平邮标签
            // HKGH 香港小包挂号标签
            // LYTPYAU 联邮通平邮澳洲标签
            // LYTPYOT 联邮通平邮欧洲地区标签
            // LYTGHUS 联邮通挂号美国标签
            // LYTGHAU 联邮通挂号澳洲标签
            // LYTGHOT 联邮通挂号欧洲地区标签
            // CNGH 中国小包挂号标签
            // CNPY 中国小包平邮标签
            'LabelTemplateType' => 'SGPY',
            // 客户代码
            'CustomerCode' => '' . Service_User::getCustomerCode(),
            // 客户订单号码，由客户自己定义
            'OrderNo' => '13213213213',
            // 销售产品/运输方式代码
            'ProductCode' => 'ProductCode',
            // 销售产品/运输方式名称
            'ProductName' => 'ProductName',
            // 产品打印标记/产品英文简称
            'ProductPrintSign' => 'ProductPrintSign',
            // 服务商跟踪号码
            'TrackingNumber' => 'TrackingNumber',
            // 国家二字码
            'DestinationCountryCode' => 'CN',
            // 国家英文名
            'DestinationCountryEName' => 'China',
            // 国家中文名
            'DestinationCountryName' => '中国',
            // 回邮地址
            'ReturnAddress' => '回邮地址',
            // 收件人姓名
            'ConsigneeName' => '收件人',
            // 收货人公司名
            'ConsigneeCompanyName' => '易仓',
            // 收件人邮编
            'ConsigneePostCode' => '838000',
            // 收件人电话
            'ConsigneePhone' => '18664957043',
            // 收件人地址
            'ConsigneeAddress' => '收件人地址',
            // 买家ID
            'BuyerID' => '买家ID',
            // 标签上打印配货信息标记 (Y:打印 N:不打印) 默认 N:不打印
            'PrintDeclareInfoSign' => 'Y',
            // 保险标记 (Y:保险 N:不保险) 默认 N:不保险
            'InsuranceSign' => 'Y',
            // 是否高价值 (Y:是 N:否) 默认 N:否
            'HighValueSign' => '',
            // 打印时间标记 (Y:打印 N:不打印) 默认 Y:打印
            'PrintTimeSign' => 'Y',
            // 是否需要退件 (Y:需要退件 N:不需要退件) 默认 N:不需要退件
            'ReturnSign' => 'Y',
            // 重量（单位：KG）默认0.2KG
            'Weight' => '0.5KG',
            // 件数(快件一票多件时打印多份)
            'Pieces' => '4',
            // 出货分区代码
            'ShipmentZoneCode' => '分区',
            // 标签账号信息
            'LabelAccount' => 'LabelAccount',
            // 标签签名信息
            'LabelSignature' => '标签签名信息',
            // 额外服务名称(多请用分隔符分割)
            'Extraservice' => 'P,T',
            // 报关申报类型（默认 1:Gift）
            // 1 Gift
            // 2 Documents
            // 3 Commercial sample
            // 9 Other
            
            'DeclarationType' => '4',
            // 货物描述
            'DeclareInvoiceArray' => array()
        );
        
        $DeclareInvoiceArray = array();
        // 货物描述
        $DeclareInvoiceArray[] = array(
            // 货物描述
            'GoodsDescription' => 'GoodsDescription',
            // 备注
            'Remarks' => 'Remarks',
            // 单价
            'UnitPrice' => '10',
            // 数量
            'Quantity' => '10',
            // 总价
            'TotalCharge' => '100'
        );
        // 货物描述
        $DeclareInvoiceArray[] = array(
            // 货物描述
            'GoodsDescription' => 'GoodsDescription',
            // 备注
            'Remarks' => 'Remarks',
            // 单价
            'UnitPrice' => '10',
            // 数量
            'Quantity' => '10',
            // 总价
            'TotalCharge' => '100'
        );
        $orderInfo['DeclareInvoiceArray'] = $DeclareInvoiceArray;
        $orderInfoArr = array();
        $orderInfoArr[] = $orderInfo;
        $orderInfoArr[] = $orderInfo;
        $orderInfoArr[] = $orderInfo;
        $orderInfoArr[] = $orderInfo;
        // $orderInfoArr[] = $orderInfo;
        $configInfoJson = Zend_Json::encode($configInfo);
        $orderInfoJson = Zend_Json::encode($orderInfoArr);

        $reportPrintTokenConfig = Service_Config::getByField('REPORT_PRINT_TOKEN', 'config_attribute');
        if(!$reportPrintTokenConfig){
        	throw new Exception('请配置REPORT_PRINT_TOKEN');
        }
        $token = 'ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL';
        $token = $reportPrintTokenConfig['config_value'];
         
        $process = new Common_Report ( $token );
        
        $return = $process->MakeLableFileToBase64($configInfoJson, $orderInfoJson);
        if($return['ask'] == 1){
            $rs = $return['rs'];
            if($rs['Ack'] == 1){
                if($configInfo['LableFileType'] == '1'){
                    // header("Content-type: image/jpeg");
                    foreach($rs['LableArray'] as $v){
                        echo "<img src='data:image/png;base64,{$v['LableFile']}'/>";
                        // echo base64_decode($v['LableFile']);
                    }
                }else{
                    foreach($rs['LableArray'] as $v){
                        $filename = APPLICATION_PATH . '/../data/cache/a.pdf';
                        $data = base64_decode($v['LableFile']);
                        file_put_contents($filename, $data);
                        // echo '<object width="100%" height="100%" type="application/pdf" data="data:application/pdf;base64,' . $v['LableFile'] . '"></object>';
                        // echo base64_decode($v['LableFile']);
                    }
                }
            }else{
                
                header("Content-type: text/html; charset=utf-8");
                $ErrorArray = $rs['ErrorArray'];
                foreach($ErrorArray as $v){
                    echo $v['Code'];
                    echo $v['CnMessage'];
                    echo $v['EnMessage'];
                    echo "\n";
                }
            }
        }else{
            header("Content-type: text/html; charset=utf-8");
            print_r($return['message']);
            exit();
        }
    }

    public function labelPdfAction()
    {
        $configInfo = array(
            // 标签文件类型，参照标签文件类型
            // 1 PNG文件
            // 2 PDF文件
            'LableFileType' => '1',
            // 标签纸张类型，参照标签纸张类型
            // 1 标签纸张
            // 2 A4纸张/ A4不干胶纸张
            'LablePaperType' => '1',
            // 标签内容类型，参照标签内容类型
            // 1 标签
            // 2 报关单
            // 3 配货单
            // 4 标签+报关单
            // 5 标签+配货单
            // 6 标签+报关单+配货单
            'LableContentType' => '6'
        );
        $orderInfo = array(
            // 标签模版类型(请参考模版类型表格)
            // NORMALY 通用快件标签
            // SGPY 新加坡小包平邮标签
            // SGGH 新加坡小包挂号标签
            // HKPY 香港小包平邮标签
            // HKGH 香港小包挂号标签
            // LYTPYAU 联邮通平邮澳洲标签
            // LYTPYOT 联邮通平邮欧洲地区标签
            // LYTGHUS 联邮通挂号美国标签
            // LYTGHAU 联邮通挂号澳洲标签
            // LYTGHOT 联邮通挂号欧洲地区标签
            // CNGH 中国小包挂号标签
            // CNPY 中国小包平邮标签
            'LabelTemplateType' => 'SGPY',
            // 客户代码
            'CustomerCode' => '' . Service_User::getCustomerCode(),
            // 客户订单号码，由客户自己定义
            'OrderNo' => '13213213213',
            // 销售产品/运输方式代码
            'ProductCode' => 'ProductCode',
            // 销售产品/运输方式名称
            'ProductName' => 'ProductName',
            // 产品打印标记/产品英文简称
            'ProductPrintSign' => 'ProductPrintSign',
            // 服务商跟踪号码
            'TrackingNumber' => 'TrackingNumber',
            // 国家二字码
            'DestinationCountryCode' => 'CN',
            // 国家英文名
            'DestinationCountryEName' => 'China',
            // 国家中文名
            'DestinationCountryName' => '中国',
            // 回邮地址
            'ReturnAddress' => '回邮地址',
            // 收件人姓名
            'ConsigneeName' => '收件人',
            // 收货人公司名
            'ConsigneeCompanyName' => '易仓',
            // 收件人邮编
            'ConsigneePostCode' => '838000',
            // 收件人电话
            'ConsigneePhone' => '18664957043',
            // 收件人地址
            'ConsigneeAddress' => '收件人地址',
            // 买家ID
            'BuyerID' => '买家ID',
            // 标签上打印配货信息标记 (Y:打印 N:不打印) 默认 N:不打印
            'PrintDeclareInfoSign' => 'Y',
            // 保险标记 (Y:保险 N:不保险) 默认 N:不保险
            'InsuranceSign' => 'Y',
            // 是否高价值 (Y:是 N:否) 默认 N:否
            'HighValueSign' => '',
            // 打印时间标记 (Y:打印 N:不打印) 默认 Y:打印
            'PrintTimeSign' => 'Y',
            // 是否需要退件 (Y:需要退件 N:不需要退件) 默认 N:不需要退件
            'ReturnSign' => 'Y',
            // 重量（单位：KG）默认0.2KG
            'Weight' => '0.5KG',
            // 件数(快件一票多件时打印多份)
            'Pieces' => '4',
            // 出货分区代码
            'ShipmentZoneCode' => '分区',
            // 标签账号信息
            'LabelAccount' => 'LabelAccount',
            // 标签签名信息
            'LabelSignature' => '标签签名信息',
            // 额外服务名称(多请用分隔符分割)
            'Extraservice' => 'P,T',
            // 报关申报类型（默认 1:Gift）
            // 1 Gift
            // 2 Documents
            // 3 Commercial sample
            // 9 Other
            
            'DeclarationType' => '4',
            // 货物描述
            'DeclareInvoiceArray' => array()
        );
        
        $DeclareInvoiceArray = array();
        // 货物描述
        $DeclareInvoiceArray[] = array(
            // 货物描述
            'GoodsDescription' => 'GoodsDescription',
            // 备注
            'Remarks' => 'Remarks',
            // 单价
            'UnitPrice' => '10',
            // 数量
            'Quantity' => '10',
            // 总价
            'TotalCharge' => '100'
        );
        // 货物描述
        $DeclareInvoiceArray[] = array(
            // 货物描述
            'GoodsDescription' => 'GoodsDescription',
            // 备注
            'Remarks' => 'Remarks',
            // 单价
            'UnitPrice' => '10',
            // 数量
            'Quantity' => '10',
            // 总价
            'TotalCharge' => '100'
        );
        $orderInfo['DeclareInvoiceArray'] = $DeclareInvoiceArray;
        $orderInfoArr = array();
        $orderInfoArr[] = $orderInfo;
        // $orderInfoArr[] = $orderInfo;
        $configInfoJson = Zend_Json::encode($configInfo);
        $orderInfoJson = Zend_Json::encode($orderInfoArr);
        $reportPrintTokenConfig = Service_Config::getByField('REPORT_PRINT_TOKEN', 'config_attribute');
        if(!$reportPrintTokenConfig){
        	throw new Exception('请配置REPORT_PRINT_TOKEN');
        }
        $token = 'ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL';
        $token = $reportPrintTokenConfig['config_value'];
        	
        $process = new Common_Report ( $token ); 
        
        $return = $process->MakeLableFileToBase64($configInfoJson, $orderInfoJson);
        if($return['ask'] == 1){
            $rs = $return['rs'];
            if($rs['Ack'] == 1){
                // header("Content-type: image/jpeg");
                foreach($rs['LableArray'] as $v){
                    $filename = APPLICATION_PATH . '/../data/tpl_c/' . Common_Common::random(10, 1) . '.pdf';
                    file_put_contents($filename, base64_decode($v['LableFile']));
                }
                Common_Common::downloadFile($filename);
                exit();
            }else{
                
                header("Content-type: text/html; charset=utf-8");
                $ErrorArray = $rs['ErrorArray'];
                foreach($ErrorArray as $v){
                    echo $v['Code'];
                    echo $v['CnMessage'];
                    echo $v['EnMessage'];
                    echo "\n";
                }
            }
        }else{
            header("Content-type: text/html; charset=utf-8");
            print_r($return['message']);
            exit();
        }
    }
    
    

    
}