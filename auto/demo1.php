<?php
require_once ('config.php');
echo PHP_SAPI;
// 运行记录112 
autoLog(basename(__FILE__));
// 任务开始输出
sapiStart(basename(__FILE__));

try{ // 逻辑处理
    //$result = Service_UserSystem::getAll();
    $return = array(
        'ask' => 'Failure',
        'message' => '',
        'Error' => array(),
        'type' => 'pdf',
        'url' => '',
    );

    // 客户参考号
    //$refer_hawbcode = $orderInfo['reference_no'];
    //$LablePaperType = $orderInfo['lable_type'];
    $refer_hawbcode = "WU000001";
    $LablePaperType = 4;

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
        var_dump($order);
        var_dump(111);

        // 客户不匹配
        /*if($order['customer_id'] != Service_User::getCustomerId()) {
            $return['Error'] = array('errCode' => '', 'errMessage' => "客户不匹配");
            return $return;
        }*/



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
        $label_config_id = 16;

        // 标签模板类型
        $sql = "select * from pbr_label_config where label_config_id='{$label_config_id}';";
        $labelConfig = $db->fetchRow($sql);
        var_dump($labelConfig);
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

        $configInfoJson = Zend_Json::encode ( $configInfo );
        var_dump(222);
        var_dump($configInfoJson);

        // PDF 打印信息
        $pdfPrintInfo = array ();

        $orderInfoArr = array ();
        $countrys = Common_DataCache::getCountry ();

        $order_id =  $order['order_id'];
        $order_id =  $order['order_id'];

        // 历史数据 start
        $con = array (
            'order_id' => $order_id
        );
        $invoice = Service_CsdInvoice::getByCondition ( $con, '*', 0, 0, 'invoice_id asc' );
        var_dump(333);
        var_dump($invoice);
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
        var_dump(555);
        var_dump($shipperConsignee);
        if (! $shipperConsignee) {
            throw new Exception ( Ec::Lang ( '收发件人信息不存在' ) );
        }
        var_dump(555555);
        var_dump($order['product_code']);
        var_dump($order['country_code']);
        $order['product_code'] = "PK0001";
        $order['country_code'] = "BR";
        $sql = "select * from pbr_public_shipper_address where product_code='{$order['product_code']}' and (country_code='{$order['country_code']}' or country_code='' or country_code is null) order by country_code desc;";
        $public_shipper_address = $db->fetchRow ( $sql );
        var_dump(666);
        var_dump($public_shipper_address);
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
        var_dump(777777);
        var_dump($labelConfig);
        if (! $labelConfig || empty ( $labelConfig ['atd_label_code'] )) {
            throw new Exception ( $order ['product_code'] . Ec::Lang ( '找不到标签模板类型' ) );
        }

        // 查找对应的PDF类型
        var_dump($label_config_id);
        var_dump($LablePaperType);
        $label_config_id = 16;
        $LablePaperType = 1;
        $sql = "select * from pbr_label_type where label_config_id='{$label_config_id}' and print_type = '{$LablePaperType}';";
        $pdfLabelConfig = $db->fetchRow ( $sql );
        var_dump(88888888);
        var_dump($pdfLabelConfig);
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
    var_dump(99999999999);
    var_dump($configInfoJson);
    var_dump(99999999999);
    var_dump($orderInfoJson);
    var_dump(99999999999);
    var_dump($pdfPrintInfoJson);

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
}catch(Exception $e){
    echo '[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n";
}
// 任务结束输出 
sapiEnd(basename(__FILE__));

echo $start = date('Y-m-d\TH:i:s.000\Z', strtotime('-9hour'));;