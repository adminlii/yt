<?php
class Default_MabangController extends Zend_Controller_Action {
	public function init() {
		$action = $this->_request->getActionName ();
		$this->tplDirectory = "default/views/default/";
	}
	public function receiveAction() {
		try {
			// $basedir = $_SERVER['DOCUMENT_ROOT'];
			$notify = $_POST ["notify"];
			if (empty ( $notify )) {
				$notify = $_GET ["notify"];
			}
			if (! empty ( $notify )) {
				// $fileName = $basedir.'/log/'.date('Y-m-d').'.log';
				// Mabang_Order_Common::writeFile($fileName, $notify, 'a'); //表示追加
				Ec::showError ( 'notify:' . $notify, '_mabang_notify_' . date ( 'Y-m-d' ) . "_" );
				$notify = base64_decode ( $notify );
				Ec::showError ( 'decode_notify' . $notify, '_mabang_notify_' . date ( 'Y-m-d' ) . "_" );
				$notify = json_decode ( $notify, true );
				$orderInfo = $notify ['orderInfo'];
				// $Platform=Mabang_Order_Common::getPlatformUser();
				if ($orderInfo ['count'] > 0) {
					$codes = implode ( ',', $orderInfo ['codes'] );
					$obj = new Mabang_Order_OrderServiceProcess ();
					$rs = $obj->orderListQuery ( $codes );
					print_r($rs);
				}
				echo ('success');
				exit ();
			}
		} catch ( Exception $e ) {
			Ec::showError ( 'mabangController_exception:' . $e->getMessage (), '_mabang_notify_error_' . date ( 'Y-m-d' ) . "_" );
			$return ['message'] = $e->getMessage ();
		}
	}
	/**
	 *标签打印
	 * @throws Exception
	 */
	public function printLabelAction(){
		$return = array (
				"ask" => 0,
				"message" => "No Data" 
		);
		
		$errArr = array ();
		try {
			$params = $this->getRequest ()->getParams ();
			$this->view->printerNo = $this->getParam ( 'printerNo', '0' );
			$LableFileType = $this->getParam ( 'LableFileType', '1' );
			$this->view->LableFileType = $LableFileType;
			$LablePaperType = $this->getParam ( 'LablePaperType', '1' );
			$this->view->LablePaperType = $LablePaperType;
			$LableContentType = $this->getParam ( 'LableContentType', '1' );
			$this->view->LableContentType = $LableContentType;
			$viewType = $this->getParam ( 'view', '0' ); // 0 为返回内容流， 1 为返回页面显示内容
			
			$PrintDeclareInfoSign = $this->getParam ( 'PrintDeclareInfoSign', 'N' );
			$InsuranceSign = $this->getParam ( 'InsuranceSign', 'N' );
			$HighValueSign = $this->getParam ( 'HighValueSign', 'N' );
			$PrintTimeSign = $this->getParam ( 'PrintTimeSign', 'N' );
			
			$printWeight = $this->getParam ( 'printWeight', 'N' );
			$printBuyerID = $this->getParam ( 'printBuyerID', 'N' );
			// print_r($printBuyerID);exit;
			$order_code = $this->getParam ( 'order_code', array () );
			// print_r($params);exit;
			$configInfo = array (
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
					'LableContentType' => $LableContentType,

					// Y 分开打印
					// N 一起打印
					'SeparateOrMerge' => 'N',
			);
			
// 			if ($LablePaperType == 1) {
// 				$labelPaperTypeConfig = Service_Config::getByField ( 'LABEL_PAPER_TYPE_CONFIG_LABEL', 'config_attribute' );
// 				if (! $labelPaperTypeConfig) {
// 					throw new Exception ( '系统未配置LABEL_PAPER_TYPE_CONFIG_LABEL' );
// 				}
// 				if (! preg_match ( '/^w:([0-9]+);h:([0-9]+)$/', $labelPaperTypeConfig ['config_value'], $m )) {
// 					throw new Exception ( 'LABEL_PAPER_TYPE_CONFIG_LABEL格式为w:100;h:100' );
// 				}
// 				$this->view->w = $m [1];
// 				$this->view->h = $m [2];
// 			} else {
// 				$labelPaperTypeConfig = Service_Config::getByField ( 'LABEL_PAPER_TYPE_CONFIG_A4', 'config_attribute' );
// 				if (! $labelPaperTypeConfig) {
// 					throw new Exception ( '系统未配置LABEL_PAPER_TYPE_CONFIG_A4' );
// 				}
// 				if (! preg_match ( '/^w:([0-9]+);h:([0-9]+)$/', $labelPaperTypeConfig ['config_value'], $m )) {
// 					throw new Exception ( 'LABEL_PAPER_TYPE_CONFIG_A4格式为w:210;h:297' );
// 				}
// 				$this->view->w = $m [1];
// 				$this->view->h = $m [2];
// 			}
			$customer_id = Service_User::getCustomerId ();
			$orderInfoArr = array ();
			$countrys = Common_DataCache::getCountry ();
			// print_r($countrys);exit;
			$db = Common_Common::getAdapter ();
			$sql = "select * from csd_order where (shipper_hawbcode='{$order_code}' or server_hawbcode='{$order_code}');";
			$order = $db->fetchRow ( $sql );
			// $order = Service_CsdOrder::getByField ( $order_id, 'order_id' );
			if (!$order) {
				throw new Exception ( Ec::Lang ( '订单号不存在或订单状态不正确,请确保订单已经申报' ) );
			}
			$order_id = $order ['order_id'];
			// 历史数据 start
			$con = array (
					'order_id' => $order_id 
			);
			$invoice = Service_CsdInvoice::getByCondition ( $con, '*', 0, 0, 'invoice_id asc' );
			if (empty ( $invoice )) {
				throw new Exception ( Ec::Lang ( '申报信息异常' ) );
			}
			foreach ( $invoice as $k => $v ) {
				$v ['invoice_unitcharge'] = $v ['invoice_quantity'] ? ($v ['invoice_totalcharge'] / $v ['invoice_quantity']) : 0;
				$invoice [$k] = $v;
			}
			
			// 额外服务
			$extraserviceKindArr = Service_AtdExtraserviceKind::getAll ();
			$extraserviceKindByKey = array ();
			foreach ( $extraserviceKindArr as $k => $row ) {
				$extraserviceKindByKey [$row ['extra_service_kind']] = $row;
			}
			
			$extservice = Service_CsdExtraservice::getByCondition ( $con );
			$extservice_str = array ();
			foreach ( $extservice as $v ) {
				$extservice_str [] = isset ( $extraserviceKindByKey [$v ['extra_servicecode']] ) ? $extraserviceKindByKey [$v ['extra_servicecode']] ['extra_service_cnname'] : $v ['extra_servicecode'];
			}
			
			$shipperConsignee = Service_CsdShipperconsignee::getByField ( $order_id, 'order_id' );
			if (! $shipperConsignee) {
				throw new Exception ( Ec::Lang ( '收发件人信息异常' ) );
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
                
			$atd_label_code = $labelConfig ['atd_label_code'];
			$returnAdd = null;
			if ($labelConfig ['return_address_type'] == 'Y') {
				// 回邮地址
				$sql = "select * from pbr_return_address where label_config_id='{$label_config_id}' and country_code='{$order['country_code']}';"; // 取对应国家
				$returnAdd = $db->fetchRow ( $sql );
				if (! $returnAdd) {
					$sql = "select * from pbr_return_address where label_config_id='{$label_config_id}' and (country_code='' or country_code is null);"; // 取得国家为空的
					$returnAdd = $db->fetchRow ( $sql );
				}
				if (! $returnAdd) {
					$sql = "select * from pbr_return_address where label_config_id='{$label_config_id}' order by rand();"; // 随机取一条
					$returnAdd = $db->fetchRow ( $sql );
				}
			}
			$arrivalZone = null;
			if ($labelConfig ['arrival_zone_type'] == 'Y') {
				// // 按照邮编分区
				// $sql = "select * from pbr_arrival_zone where
				// label_config_id='{$label_config_id}' and
				// country_code='{$order['country_code']}' and
				// (ct_startpostcode<='{$shipperConsignee['consignee_postcode']}'
				// and
				// ct_endpostcode>='{$shipperConsignee['consignee_postcode']}');";
				// // echo $sql;exit;
				// $arrivalZone = $db->fetchRow($sql);
				// if(! $arrivalZone){
				// if(! $arrivalZone){//按照国家
				// $sql = "select * from pbr_arrival_zone where
				// label_config_id='{$label_config_id}' and
				// country_code='{$order['country_code']}' ;";
				// $arrivalZone = $db->fetchRow($sql);
				// }
				// if(! $arrivalZone){
				// $sql = "select * from pbr_arrival_zone where
				// label_config_id='{$label_config_id}';"; //
				// $arrivalZone = $db->fetchRow($sql);
				// }
				// }
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
								order by country_city desc
								";
				// '{$shipperConsignee['consignee_city']}'
				// echo $sql;exit;
				$r = $db->query ( $sql, array (
						'country_city' => $shipperConsignee ['consignee_city'] 
				) );
				$arrivalZone = $r->fetchAll ();
				if ($arrivalZone) {
					$arrivalZone = array_shift ( $arrivalZone );
				}
			}
			// 验证保险
			$sql = "SELECT a.* FROM `csd_extraservice` a INNER JOIN atd_extraservice_kind b on a.extra_servicecode=b.extra_service_kind where b.extra_service_group='C0' and a.order_id='{$order['order_id']}';";
			$InsuranceSign = $db->fetchRow ( $sql );
			if ($InsuranceSign) {
				$InsuranceSign = 'Y';
			} else {
				$InsuranceSign = 'N';
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
                    'OrderNo' => $order['shipper_hawbcode'],
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
                    // 收件人电话
                    'ConsigneePhone' => $shipperConsignee['consignee_telephone'],
                    // 收件人手机
                    'ConsigneeTelephone' => $shipperConsignee['consignee_mobile'],
                    // 收件人城市
                    'ConsigneeCity' => $shipperConsignee['consignee_city'],
                    // 收件人省/州
                    'ConsigneeProvince' => $shipperConsignee['consignee_province'],
                    // 收件人街道
                    'ConsigneeStreet' => $shipperConsignee['consignee_street'],
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
                        // 总价
                        'TotalCharge' => $v['invoice_totalcharge']
                    );
                }
                
                $orderInfo['DeclareInvoiceArray'] = $DeclareInvoiceArray;
                
                $orderInfoArr[] = $orderInfo;
                
//         echo "---";
//         print_r($orderInfoArr);die;
//         if(! empty($errArr)){
//             header("Content-type: text/html; charset=utf-8");
//             foreach($errArr as $err){
//                 echo $err . '<br/>';
//             }
//             exit();
//         }
        
	        $configInfoJson = Zend_Json::encode($configInfo);
	        $orderInfoJson = Zend_Json::encode($orderInfoArr);
	        $pdfPrintInfoJson = Zend_Json::encode($pdfPrintInfo);
			
			$reportPrintTokenConfig = Service_Config::getByField ( 'REPORT_PRINT_TOKEN', 'config_attribute' );
			if (! $reportPrintTokenConfig) {
				throw new Exception ( '请配置REPORT_PRINT_TOKEN' );
			}
			$token = 'ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL';
			$token = $reportPrintTokenConfig ['config_value'];
			
			// PDF 输出
	        if($LableFileType == 2) {
	        	
	        	if(empty($pdfPrintInfo)) {
	        		header("Content-type: text/html; charset=utf-8");
	        		echo '该产品暂不支持PDF打印<br/>';
	        		exit();
	        	}
	        	
	        	$process = new Common_FastReport ($token);
	        	$return = $process->MakeLableFileToBase64($configInfoJson, $orderInfoJson, $pdfPrintInfoJson);
	//         	echo "<br/>3->"; print_r(date('Y-m-d H:i:s:S')); die;
	        	if($return['ask'] == 1) {
	        		if($viewType == 0) {
		        		header( "Content-type: application/pdf");
		        		echo $return['rs']->GetReportResult;
	        		} else {
	        			header("Content-type: text/html; charset=utf-8");
	        			echo '<object width="100%" height="100%" type="application/pdf" data="data:application/pdf;base64,'.base64_encode($return['rs']->GetReportResult).'"></object>';
	        		}
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
		        try {
		        	
			        if ($result ['ask'] == 1) {
						$rs = $result ['rs'];
						if ($rs ['Ack'] == 1) {
							if($viewType == 0) {
								header("Content-type: image/jpeg");
								echo $v['LableFile'];
								exit();
							} else {
								// header("Content-type: image/jpeg");
								foreach ( $rs ['LableArray'] as $v ) {//图片
									echo "<img src='data:image/png;base64,{$v['LableFile']}'/>";
									// echo base64_decode($v['LableFile']);
								}
							}
							$return ['ask'] = 1;
							$return ['message'] = 'Success';
							$return ['LableFileType'] = $configInfo ['LableFileType'];
							$this->view->lableArr = $rs ['LableArray'];
							$this->view->title = '订单' . $order_code;
						} else {
							$ErrorArray = $rs ['ErrorArray'];
							foreach ( $ErrorArray as $v ) {
								$errArr [] = $v ['Code'] . ' ' . $v ['CnMessage'] . ' ' . $v ['EnMessage'];
							}
						}
					} else {
						throw new Exception ($result ['message']);
					}
				} catch ( Exception $eee ) {
					array_unshift ( $errArr, $eee->getMessage () );
					$return ['message'] = $eee->getMessage ();
				}
				
				$return ['errArr'] = $errArr;
				// print_r($return);exit;
				$this->view->return = $return;
				if(!empty($errArr)){
					header("Content-type: text/html;charset=UTF-8");
					echo join(';', $errArr);
				}
	        }
			
// 				$process = new Common_Report ( $token );
// 				// print_r($orderInfoArr);exit;
// 				$result = $process->MakeLableFileToBase64 ( $configInfoJson, $orderInfoJson );
// 				// print_r($result);exit;
// 				if ($result ['ask'] == 1) {
// 					$rs = $result ['rs'];
// 					if ($rs ['Ack'] == 1) {
// 						if ($configInfo ['LableFileType'] == '1') {
// 							foreach ( $rs ['LableArray'] as $v ) {//图片
// 								header("Content-type: image/jpeg");
// 								echo "<img src='data:image/png;base64,{$v['LableFile']}'/>";
// 								// echo base64_decode($v['LableFile']);
// 							}
// 						} else {
// 							foreach ( $rs ['LableArray'] as $v ) {//pdf
// 								echo '<object width="100%" height="100%" type="application/pdf" data="data:application/pdf;base64,'.$v['LableFile'].'"></object>';
// 								// echo base64_decode($v['LableFile']);
// 							}
// 						}
// 						$return ['ask'] = 1;
// 						$return ['message'] = 'Success';
// 						$return ['LableFileType'] = $configInfo ['LableFileType'];
// 						$this->view->lableArr = $rs ['LableArray'];
// 						$this->view->title = '订单' . $order_code;
// 					} else {
// 						$ErrorArray = $rs ['ErrorArray'];
// 						foreach ( $ErrorArray as $v ) {
// 							$errArr [] = $v ['Code'] . ' ' . $v ['CnMessage'] . ' ' . $v ['EnMessage'];
// 						}
// 					}
// 				} else {
// 					throw new Exception ( $result ['message'] );
// 				}
		} catch ( Exception $eee ) {
			header("Content-type: text/html; charset=utf-8");
			print_r($eee->getMessage ());
			exit();
		}
// 		}
// 		$return ['errArr'] = $errArr;
// 		// print_r($return);exit;
// 		$this->view->return = $return;
// 		if(!empty($errArr)){
// 			header("Content-type: text/html;charset=UTF-8");
// 			echo join(';', $errArr);
// 		}
// 		echo $this->view->render ( 'order/views/label-print/label-print.js' );
	}
	
}
?>
