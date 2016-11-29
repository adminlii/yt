<?php
class Process_OrderUpload extends Process_Order
{

    protected $_excel_data = array();
    
    protected $_excel_column = array();

    protected $_successArr = array();

    protected $_failArr = array();

    protected $_errArr = array();
    
    protected $_errTipArr = array();
    
    protected $_formatArr = array();
    //不存在的国家
    protected $_notExistCountryArr = array();
    //客户自定义国家映射
    protected $_country_map = array();
    //选择的发件人
    protected $_default_shipper_account = 0;
    //选择的发件人
    protected $_default_shipper = null;

    public function exportColumnMap(){
        $map = array(
            //'order_id' => Ec::lang('系统主键'),
            //'order_create_code' => Ec::lang('订单创建方式'),
//             'customer_id' => Ec::lang('customer_id'),
//             'customer_channelid' => Ec::lang('customer_channelid'),
            'product_code' => Ec::lang('运输方式'),
            'shipper_hawbcode' => Ec::lang('客户单号'),
            'server_hawbcode' => Ec::lang('服务商单号'),
            //'channel_hawbcode' => Ec::lang('渠道换单号'),
            'country_code' => Ec::lang('目的国家'),
            'order_pieces' => Ec::lang('货物件数'),
            'order_weight' => Ec::lang('货物重量'),
            'order_status' => Ec::lang('订单状态'),
            'mail_cargo_type' => Ec::lang('邮政货物类型'),
        		'mail_cargo_type' => Ec::lang('邮政货物类型'),
            //'document_change_sign' => Ec::lang('服务商单号换号类型'),
//             'oda_checksign' => Ec::lang('oda_checksign'),
//             'oda_sign' => Ec::lang('oda_sign'),
//             'return_sign' => Ec::lang('return_sign'),
//             'hold_sign' => Ec::lang('订单拦截状态'),
            'buyer_id' => Ec::lang('买家ID'),
//             'platform_id' => Ec::lang('platform_id'),
//             'bs_id' => Ec::lang('bs_id'),
            'creater_id' => Ec::lang('创建人'),
            'create_date' => Ec::lang('创建时间'),
//             'modify_date' => Ec::lang('modify_date'),
            //'print_date' => Ec::lang('打印时间'),
            //'post_date' => Ec::lang('提交预报时间'),
            //'checkin_date' => Ec::lang('收货时间'),
            //'checkout_date' => Ec::lang('出货时间'),
//             'tms_id' => Ec::lang('tms_id'),
            //'transaction_id'=>Ec::Lang('交易ID'),
            //'order_info'=>Ec::Lang('订单备注'),
        
//             'shipper_account' => Ec::lang('shipper_account'),
            'shipper_name' => Ec::lang('发件人姓名'),
            'shipper_company' => Ec::lang('发件人公司名'),
            'shipper_countrycode' => Ec::lang('发件人国家'),
            'shipper_province' => Ec::lang('发件人省/州'),
            'shipper_city' => Ec::lang('发件人城市'),
            'shipper_street' => Ec::lang('发件人街道地址'),
            'shipper_postcode' => Ec::lang('发件人邮编'),
            //'shipper_areacode' => Ec::lang('发件人地区代码'),
            'shipper_telephone' => Ec::lang('发件人电话号码'),
            //'shipper_mobile' => Ec::lang('发件人手机'),
            //'shipper_email' => Ec::lang('发件人邮箱'),                
            //'shipper_fax' => Ec::lang('发件人传真'),
            //'shipper_certificatetype' => Ec::lang('发件人证件类型'),
            //'shipper_certificatecode' => Ec::lang('发件人证件号码'),
            //'shipper_mallaccount' => Ec::lang('发件的商城账号'),
                
            'consignee_name' => Ec::lang('收件人'),
            'consignee_company' => Ec::lang('收件人公司'),
            'consignee_countrycode' => Ec::lang('收件人国家'),
            'consignee_province' => Ec::lang('收件人省/州'),
            'consignee_city' => Ec::lang('收件人城市'),
            'consignee_street' => Ec::lang('收件人街道地址'),
            'consignee_postcode' => Ec::lang('收件人邮编'),
            //'consignee_areacode' => Ec::lang('收件人地区代码'),
            'consignee_telephone' => Ec::lang('收件人电话号码'),
            'consignee_mobile' => Ec::lang('收件人手机'),
            //'consignee_email' => Ec::lang('收件人邮箱'),
            //'consignee_fax' => Ec::lang('收件人传真'),
            //'consignee_mallaccount' => Ec::lang('收件人商城账号'),
            //'consignee_certificatetype' => Ec::lang('证件类型'),
            //'consignee_certificatecode' => Ec::lang('收件人证件号码'),
            //'consignee_credentials_period' => Ec::lang('证件有效期间'),

            'invoice_enname' => Ec::lang('英文申报品名'),
            'unit_code' => Ec::lang('单位'),
            'invoice_quantity' => Ec::lang('数量'),
            'invoice_unitcharge' => Ec::lang('单价'),
            'invoice_totalcharge' => Ec::lang('总金额'),
            'invoice_currencycode' => Ec::lang('币种'),
            'hs_code' => Ec::lang('海关协制编号'),
            'invoice_note' => Ec::lang('配货信息'),
            'invoice_url' => Ec::lang('商品销售网址')
        );
        return $map;
    }
    
    public function importInvoiceMap(){
        $map = array(
			'客户单号'=>'shipper_hawbcode',
			'申报品名1'=>'AAA_invoice_enname_1',
			'中文申报品名1'=>'AAA_invoice_cnname_1',
			'申报单价1'=>'AAA_invoice_unitcharge_1',
			'销售地址1'=>'AAA_invoice_url_1',
			'申报数量1'=>'AAA_invoice_quantity_1',
        	'申报重量1'=>'AAA_invoice_weight_1',
			'海关货物编号1'=>'AAA_hs_code_1',
			'配货备注1'=>'AAA_invoice_note_1',
			'申报品名2'=>'AAA_invoice_enname_2',
			'中文申报品名2'=>'AAA_invoice_cnname_2',
			'申报单价2'=>'AAA_invoice_unitcharge_2',
			'销售地址2'=>'AAA_invoice_url_2',
			'申报数量2'=>'AAA_invoice_quantity_2',
        	'申报重量2'=>'AAA_invoice_weight_2',
			'海关货物编号2'=>'AAA_hs_code_2',
			'配货备注2'=>'AAA_invoice_note_2',
			'申报品名3'=>'AAA_invoice_enname_3',
			'中文申报品名3'=>'AAA_invoice_cnname_3',
			'申报单价3'=>'AAA_invoice_unitcharge_3',
			'销售地址3'=>'AAA_invoice_url_3',
			'申报数量3'=>'AAA_invoice_quantity_3',
        	'申报重量3'=>'AAA_invoice_weight_3',
			'海关货物编号3'=>'AAA_hs_code_3',
			'配货备注3'=>'AAA_invoice_note_3',
        );
        return $map;
    }
    /**
     * 订单导出，基本版
     *
     * @param unknown_type $orderIds            
     */
    public function baseExportProcess($orderIds)
    {
        try{
            
            $status = Service_OrderProcess::getOrderStatus();
            $createTypes = Common_Type::orderCreateCode();
            $map = $this->exportColumnMap();
            $dataList = array();
            foreach($orderIds as $order_id){
                $data = array();
                $columns = array(
                    'order_id',
                    'order_create_code',
                    'customer_id',
                    'customer_channelid',
                    'product_code',
                    'shipper_hawbcode',
                    'server_hawbcode',
                    'channel_hawbcode',
                    'country_code',
                    'order_pieces',
                    'order_weight',
                    'order_status',
                    'mail_cargo_type',
                    'document_change_sign',
                    'oda_checksign',
                    'oda_sign',
                    'return_sign',
                    'hold_sign',
                    'buyer_id',
                    'platform_id',
                    'bs_id',
                    'creater_id',
                    'create_date',
                    'modify_date',
                    'print_date',
                    'post_date',
                    'checkin_date',
                    'checkout_date',
                    'tms_id',
                    'order_info',
                    'transaction_id'
                );
                $order = Service_CsdOrder::getByField($order_id, 'order_id', $columns);
                if($order['customer_id'] != Service_User::getCustomerId()){
                    throw new Exception(Ec::Lang('非法操作'));
                }
                // 创建人
                $u = Service_User::getByField($order['creater_id'], 'user_id');
                // print_r($u);exit;
                $order['creater_id'] = $u ? $u['user_name'] : '';
                // 创建方式
                $order['order_create_code'] = isset($createTypes[$order['order_create_code']]) ? $createTypes[$order['order_create_code']] : $order['order_create_code'];
                
                $order['order_status'] = isset($status[$order['order_status']]) ? $status[$order['order_status']]['name'] : Ec::Lang('异常状态');
                
                if($order['product_code']){
                	$product_ini = Common_Common::getProductAllByCode($order['product_code']);
                	$order['product_code'] = $product_ini['cname'];
                }
                
                $con = array(
                    'order_id' => $order_id
                );
                $invoiceColumns = array(
                    'invoice_enname',
                    'unit_code',
                    'invoice_quantity',
                    'invoice_totalcharge',
                    'invoice_currencycode',
                    'hs_code',
                    'invoice_note',
                    'invoice_url'
                );
                $invoice = Service_CsdInvoice::getByCondition($con, $invoiceColumns, 0, 0, 'invoice_id asc');
                foreach($invoice as $k => $v){
                    $v['invoice_unitcharge'] = $v['invoice_quantity'] ? ($v['invoice_totalcharge'] / $v['invoice_quantity']) : 0;
                    $invoice[$k] = $v;
                }
                // 表字段不存在
                $invoiceColumns[] = 'invoice_unitcharge';
                
                $con = array(
                    'order_id' => $order_id
                );
                $extservice = Service_CsdExtraservice::getByCondition($con);
                $fields = array(
                    'shipper_account',
                    'shipper_name',
                    'shipper_company',
                    'shipper_countrycode',
                    'shipper_province',
                    'shipper_city',
                    'shipper_street',
                    'shipper_postcode',
                    'shipper_areacode',
                    'shipper_telephone',
                    'shipper_mobile',
                    'shipper_email',
                    'shipper_certificatecode',
                    'shipper_certificatetype',
                    'shipper_fax',
                    'shipper_mallaccount',
                    'consignee_name',
                    'consignee_company',
                    'consignee_countrycode',
                    'consignee_province',
                    'consignee_city',
                    'consignee_street',
                    'consignee_postcode',
                    'consignee_areacode',
                    'consignee_telephone',
                    'consignee_mobile',
                    'consignee_fax',
                    'consignee_email',
                    'consignee_mallaccount',
                    'consignee_certificatetype',
                    'consignee_certificatecode',
                    'consignee_credentials_period'
                );
                $shipperConsignee = Service_CsdShipperconsignee::getByField($order_id, 'order_id', $fields);
                unset($shipperConsignee['order_id']);
                $order = array_merge($order, $shipperConsignee);
                // 去除无用列
                $keys = array_keys($order);
                $mapKeys = array_keys($map);
                foreach($keys as $key){
                    if(! in_array($key, $mapKeys)){
                        unset($order[$key]);
                    }
                }
                
                for($i = 1;$i <= 20;$i ++){
                    $suffix = '_' . $i;
                    foreach($invoiceColumns as $k => $v){
                        $order[$v . $suffix] = '';
                    }
                }
                array_unshift($invoice, array());
                unset($invoice[0]);
                foreach($invoice as $k => $v){
                    $suffix = '_' . $k;
                    foreach($v as $kk => $vv){
                        if(in_array($kk, $invoiceColumns)){
                            $order[$kk . $suffix] = $vv;
                        }
                    }
                }
                // 列转多语言
                $orderTmp = array();
                foreach($order as $k => $v){
                    $suffix = '';
                    if(preg_match('/_([0-9]+)$/', $k, $m)){
                        $k = preg_replace('/_([0-9]+)$/', '', $k);
                        $suffix = $m[1];
                    }
                    $k = isset($map[$k]) ? $map[$k] : $k;
                    $orderTmp[$k . $suffix] = $v;
                }
                // print_r($order);exit;
                $data = array();
                $orderTmp = Common_Common::arrayNullToEmptyString($orderTmp);
                // print_r($orderTmp);exit;
                $dataList[] = $orderTmp;
                // 日志
                $logRow = array(
                    'ref_id' => $order_id,
                    'log_content' => Ec::Lang('订单导出')
                );
                Service_OrderLog::add($logRow);
            }
            //print_r($dataList);exit;
            $fileName = Service_ExcelExport::exportToFile($dataList, '订单');
            Common_Common::downloadFile($fileName);
        }catch(Exception $e){
            header("Content-type: text/html; charset=utf-8");
            echo $e->getMessage();exit;
        }
    }

    /**
     * 读取上传的excel文件
     *
     * @param unknown_type $fileName            
     * @param unknown_type $filePath            
     * @return string mixed Ambigous
     */
    public function readUploadFile($fileName, $filePath, $sheet = 0)
    {
        $pathinfo = pathinfo($fileName);
        $fileData = array();
        
        if(isset($pathinfo["extension"]) && $pathinfo["extension"] == "xls"){
            $fileData = Common_Upload::readEXCEL($filePath, $sheet, true,false);
//                 print_r($fileData);exit;
            if(is_array($fileData)){
                $result = array();
                $columnMap = array();

                //php hack 
                //array_shift 会将所有的数字键名将改为从零开始计数，文字键名将不变
                $fileData = array_reverse($fileData,true);
                $firstRow = array_pop($fileData);
                $fileData = array_reverse($fileData,true);
                                
                foreach($firstRow as $key => $value){
                    if(isset($columnMap[$value])){
                        $firstRow[$key] = $columnMap[$value];
                    }
                }
                foreach($fileData as $key => $value){
                    foreach($value as $vKey => $vValue){
                        if($firstRow[$vKey] == ""){
                            continue;
                        }
                        $vValue = trim($vValue);
                        $result[$key][$firstRow[$vKey]] = $vValue;
                    }
                }
//                 print_r($fileData);exit;
                return $result;
            }else{
                return $fileData;
            }
        }else{
            return Ec::Lang('文件格式不正确，请上传xls文件');
        }
    }

    public function getExcelMapDhl()
    {
        $map = array(
            '客户单号' => 'shipper_hawbcode',
            '服务商单号' => 'server_hawbcode',
            '运输方式代码' => 'product_code',
            '收件人国家' => 'country_code',
            '发件人参考信息' => 'refer_hawbcode',
            //'买家ID' => 'buyer_id',
            //'交易ID' => 'transaction_id',
            //'订单备注' => 'order_info',
            '外包装件数' => 'order_pieces',
            '货物重量' => 'order_weight',
            '申报类型' => 'mail_cargo_type',
            '外包装长(cm)'=>'order_length',
            '外包装宽(cm)'=>'order_width',
            '外包装高(cm)'=>'order_height',
             
             
            '发件人公司' =>'shipper_company',
            '发件人姓名' => 'shipper_name',
            '发件人国家' => 'shipper_countrycode',
            //'省/州' =>   'shipper_province',
            '发件人城市' => 'shipper_city',
            '发件人地址1' => 'shipper_street1',
        	'发件人地址2' => 'shipper_street2',
        	'发件人地址3' => 'shipper_street3',
            '发件人电话' => 'shipper_telephone',
            '发件人邮编' => 'shipper_postcode',
            //'发件人传真' => 'shipper_fax',
    
            '收件人公司名' => 'consignee_company',
            '收件人姓名' => 'consignee_name',
            '收件人省/州' => 'consignee_province',
            '收件人城市' => 'consignee_city',
            '收件人邮编' => 'consignee_postcode',
            '收件人地址1' => 'consignee_street',
            '收件人地址2' => 'consignee_street2',
            '收件人地址3' => 'consignee_street3',
            //'收件人门牌号'=>'consignee_doorplate',
            '收件人电话' => 'consignee_telephone',
            '证件代码'=>'consignee_certificatetype',
            '证件号码' => 'consignee_certificatecode',
            //'收件人证件有效期间'=>'consignee_credentials_period',
            '收件人邮箱' => 'consignee_email',
            '收件人传真' => 'consignee_fax',
            '英文品名'=>'AAA_invoice_enname_1',
            '中文品名'=>'AAA_invoice_cnname_1',
            '发件人增值税号/企业海关十位编码'=>'AAA_invoice_shippertax_1',
            '收件人增值税号/企业海关十位编码'=>'AAA_invoice_consigneetax_1',
            '通关的申报价值'=>'AAA_invoice_totalcharge_all_1',
            'HSCODE'=>'AAA_hs_code_1',
            '快件保险（是，否）'=>'extraservice1',
            '保险价值' => 'insurance_value_gj',
            '额外服务选项（是，否）'=>'extraservice2',
        	'制作发票(是,否)'=>'invoice_print',
        	'日期'=>'makeinvoicedate',
        	'出口类型'=>'export_type',
        	'贸易条款'=>'trade_terms',
        	'发票号码'=>'invoicenum',
        	'付款方式'=>'pay_type',	
        	'注释'=>'fpnote',							
        );
        for($i = 1;$i <= 100;$i ++){
            //$map['配货信息' . $i] = 'AAA_invoice_note_' . $i;
            //$map['单价' . $i.'(usd)'] = 'AAA_invoice_unitcharge_' . $i;
            $map['单件重量' . $i] = 'AAA_invoice_weight_' . $i;
            //$map['销售地址' . $i] = 'AAA_invoice_url_' . $i;
            $map['长度' . $i.'（cm）'] = 'AAA_invoice_length_' . $i;
            $map['宽度' . $i.'（cm）'] = 'AAA_invoice_width_' . $i;
            $map['高度' . $i.'（cm）'] = 'AAA_invoice_height_' . $i;
            $map['件数' . $i] = 'AAA_invoice_quantity_' . $i;
            //$map['海关编码' . $i] = 'AAA_hs_code_' . $i;
            //$map['配货备注' . $i] = 'AAA_invoice_note_' . $i;
            $map['完整描述' . $i] = 'BBB_invoice_note_' . $i;
            $map['商品代码' . $i] = 'BBB_invoice_shipcode_' . $i;
            $map['单价'.$i.'（usd）'] = 'BBB_invoice_unitcharge_' . $i;
            $map['产地' . $i] = 'BBB_invoice_proplace_' . $i;
            $map['数量' . $i] = 'BBB_invoice_quantity_' . $i;
        }
        /*
         从标准模板中获取映射关系
         */
        /* $sql = "select sc_columncode,sc_name,sc_ename,sc_note,sc_require from csd_standard_column order by sc_no asc;";
         $db = Common_Common::getAdapter();
         $rows = $db->fetchAll($sql);
         $map = array();
         foreach($rows as $row){
         $map[$row['sc_name']] = $row['sc_columncode'];
         }   */
        //print_r($map);exit;
        return $map;
    }
    
    public function getExcelMap()
    {
         $map = array(
             '客户单号' => 'shipper_hawbcode',
             '服务商单号' => 'server_hawbcode',
             '运输方式' => 'product_code',
             '收件人国家（国家简称）' => 'country_code',
             '客户订单号' => 'refer_hawbcode',
             //'买家ID' => 'buyer_id',
             //'交易ID' => 'transaction_id',
             //'订单备注' => 'order_info',
             '外包装件数' => 'order_pieces',
             '货物重量' => 'order_weight',
             '包裹申报种类' => 'mail_cargo_type',
         	 '是否带电池' => 'battery',
             '外包装长(cm)'=>'order_length',
             '外包装宽(cm)'=>'order_width',
             '外包装高(cm)'=>'order_height',
             
             
             '发件公司名' =>'shipper_company',
             '发件人姓名' => 'shipper_name',
             '国家' => 'shipper_countrycode',
             '省/州' =>   'shipper_province',
             '城市' => 'shipper_city',
             '发件人详细地址' => 'shipper_street',
             '发件人电话' => 'shipper_telephone',
             '发件人邮编' => 'shipper_postcode',
             //'发件人传真' => 'shipper_fax',
            
             '收件人公司' => 'consignee_company',
             '收件人姓名' => 'consignee_name',
             '收件人省/州' => 'consignee_province',
             '收件人城市' => 'consignee_city',
             '邮编' => 'consignee_postcode',
             '收件地址1' => 'consignee_street',
             '收件地址2' => 'consignee_street2',
             '收件地址3' => 'consignee_street3',
             '收件人门牌号'=>'consignee_doorplate',
             '电话' => 'consignee_telephone',
             '证件代码'=>'consignee_certificatetype',
            '证件号码' => 'consignee_certificatecode',
             //'收件人证件有效期间'=>'consignee_credentials_period',
             '收件人邮箱' => 'consignee_email',
             '收件人传真' => 'consignee_fax'
         );
         for($i = 1;$i <= 100;$i ++){
             $map['申报英文品名' . $i] = 'AAA_invoice_enname_' . $i;
             $map['申报中文品名' . $i] = 'AAA_invoice_cnname_' . $i;
             $map['配货信息' . $i] = 'AAA_invoice_note_' . $i;
             $map['单价' . $i.'(usd)'] = 'AAA_invoice_unitcharge_' . $i;
             $map['重量' . $i.'(kg)'] = 'AAA_invoice_weight_' . $i;
             //$map['销售地址' . $i] = 'AAA_invoice_url_' . $i;
             $map['数量' . $i] = 'AAA_invoice_quantity_' . $i;
             $map['海关编码' . $i] = 'AAA_hs_code_' . $i;
             $map['配货信息' . $i] = 'AAA_invoice_note_' . $i;
             $map['SKU' . $i] = 'AAA_sku_' . $i;
             //$map['配货备注' . $i] = 'AAA_invoice_note_' . $i;
         }
        /*
                            从标准模板中获取映射关系
         */
        /* $sql = "select sc_columncode,sc_name,sc_ename,sc_note,sc_require from csd_standard_column order by sc_no asc;";
        $db = Common_Common::getAdapter();
        $rows = $db->fetchAll($sql);
        $map = array();
        foreach($rows as $row){
            $map[$row['sc_name']] = $row['sc_columncode'];
        }   */          
        //print_r($map);exit;
        return $map;
    }

    /**
     * 客户自定义国家映射
     * @param unknown_type $country_code
     * @return unknown
     */
    protected function _checkCountryExist($country_code){
        $db = Common_Common::getAdapter();
        $sql = "select * from idd_country_upload where country_value='{$country_code}';";
        $country = $db->fetchRow($sql);
        if(! $country){
			// 从客户自定义国家映射表中获取数据
// 			$customer_id = Service_User::getCustomerId ();
// 			$sql = "select * from csd_country_mapping where customer_id='{$customer_id}' and original_countryname='{$country_code}';";
// 			$db = Common_Common::getAdapter ();
// 			$map = $db->fetchRow ( $sql );
// 			if (! $map) {
// 				$this->_notExistCountryArr [] = $country_code;
// 			} else {
// 				$country_code = $map ['country_code'];
// 			}
			if (empty ( $this->_country_map [$country_code] )) {
				$this->_notExistCountryArr [] = $country_code;
			} else {
				$country_code = $this->_country_map [$country_code];
			}
		}        
        return $country_code;
    }
    
    public function getNotExistCountryArr(){
        foreach($this->_notExistCountryArr as $k=>$v){
            if(empty($v)){
                unset($this->_notExistCountryArr[$k]);
            }
        }
        $this->_notExistCountryArr = array_unique($this->_notExistCountryArr);
        return $this->_notExistCountryArr;
    }
    
    /**
     * 客户自定义国家映射
     * @param unknown_type $country_map
     */
    public function setCountryMap($country_map){
    	$customer_id = Service_User::getCustomerId();
    	$db = Common_Common::getAdapter();
    	$table = 'csd_country_mapping';
    	//客户自定义国家映射
    	foreach($country_map as $original_countryname=>$country_code){
    		if(!empty($country_code)&&$customer_id&&$original_countryname){
    			$sql = "select * from {$table} where customer_id='{$customer_id}' and original_countryname='{$original_countryname}';";
    			$exist = Common_Common::fetchRow($sql);
    			if(!$exist){
					$arr = array (
							'customer_id' => $customer_id,
							'original_countryname' => $original_countryname,
							'country_code' => $country_code,
							'cmp_createdate'=>date('Y-m-d H:i:s'), 
					);
					$db->insert($table, $arr);
				}else{
    				$sql = "update {$table} set country_code={$country_code} where cmp_id='{$exist['cmp_id']}'";
    				Common_Common::query($sql);
    			}
    		}
    	}
    	//所有自定义国家映射 
    	$sql = "select * from csd_country_mapping where customer_id='{$customer_id}';";
    	//             echo $sql;exit;
    	$csd_country_mapping = Common_Common::fetchAll($sql);
    	foreach($csd_country_mapping as $v){
    		$country_map[$v['original_countryname']] = $v['country_code'];
    	} 
        $this->_country_map = $country_map;
    }
    /**
     * 默认发件人
     * @param unknown_type $shipper_account
     */
    public function setDefaultShipperAccount($shipper_account){
        $this->_default_shipper_account = $shipper_account;
        if($this->_default_shipper_account){
            $this->_default_shipper = Service_CsiShipperTrailerAddress::getByField($this->_default_shipper_account, 'shipper_account');
        
        }
       
    }
    
    /**
     * 数据处理
     * @param unknown_type $fileData
     * @throws Exception
     */
    protected function _dataProcess($fileData, $singleCommitFlag = false)
    {
    	$map = $this->getExcelMap();
        //键值互换array_flip
        $map_flip = array_flip($map);
       
        // 列转换
        $fileDataFormat = array();
        foreach($fileData as $k => $v){
            foreach($v as $kk => $vv){
                if(isset($map[$kk])){
                    $fileDataFormat[$k][$map[$kk]] = $vv;
                }
            }
        }
        $this->_formatArr = $fileDataFormat;
        $dataArr = array();
        foreach($fileDataFormat as $k => $v){
            $data = array();            
            //验证国家是否存在,通过标准国家二字码与客户自定义国家映射
            $v['country_code'] = $this->_checkCountryExist($v['country_code']);
            //修改从excel读取到的原始数据  
            $fileData[$k][$map_flip['country_code']] = $v['country_code'];
            switch ($v['mail_cargo_type']){
                case 'Gift';$v['mail_cargo_type']=1;break;
                case 'Commercial Sample';$v['mail_cargo_type']=2;break;
                case 'Document';$v['mail_cargo_type']=3;break;
                default:$v['mail_cargo_type']=4;break;
            }
            
            //运输方式映射
            if(!empty($v['product_code'])){
            	if($v['product_code']=="E速宝专递"){
            		$v['product_code'] = "ESBR";
            	}else if($v['product_code']=="E速宝小包"){
            		$v['product_code'] = "ESB";
            	}
            }
            
            //如果目的国家确定
            if(!empty($v['country_code'])&&$v['product_code']){
            	$changeCode = Common_Common::getProductAllByCountryCode($v['country_code'],$v['product_code']);
            	if(!empty($changeCode)){
            		$v['product_code'] = $changeCode;
            	}	
            }
            
            $order = array(
                'product_code' => strtoupper($v['product_code']),
                'country_code' => strtoupper($v['country_code']),
                'refer_hawbcode' => strtoupper($v['refer_hawbcode']),
                'order_weight' => $v['order_weight'],
                'order_pieces' => $v['order_pieces'],
                 
                'order_length'=>$v['order_length'],
                'order_width'=>$v['order_width'],
                'order_height'=>$v['order_height'],
            
                'buyer_id' =>$v['buyer_id'],
                'order_id' => $v['order_id'],
                'order_create_code'=>'w',
                'customer_id'=>Service_User::getCustomerId(),
                'creater_id'=>Service_User::getUserId(),
                'modify_date'=>date('Y-m-d H:i:s'),
                'mail_cargo_type' => $v['mail_cargo_type'],
                'tms_id'=>Service_User::getTmsId(),
                'customer_channelid'=>Service_User::getChannelid(),
                'insurance_value' => trim($v['insurance_value1']),
            	'battery' => trim($v['battery']),
            		
            );
            /* if(empty($order['shipper_hawbcode'])){
                 $this->_errArr[] = Ec::Lang('客户单号不可为空');
            } */

            if(empty($order['product_code'])){
                 $this->_errArr[] = Ec::Lang('运输方式不可为空');
             }

             if(empty($order['country_code'])){
                 $this->_errArr[] = Ec::Lang('目的国家不可为空');
             }
            
            $volume=array(
            		'length'=>$v['length'],
            		'width'=>$v['width'],
            		'height'=>$v['height'],
            );
           
            $consignee = array(
                    'consignee_name' => $v['consignee_name'],
                    'consignee_company' => $v['consignee_company'],
                    'consignee_countrycode' => $v['country_code'],
                    'consignee_province' => $v['consignee_province'],
                    'consignee_city' => $v['consignee_city'],
                    'consignee_street' => $v['consignee_street'],
                    'consignee_street2' => $v['consignee_street2'],
                    'consignee_street3' => $v['consignee_street3'],
                    'consignee_postcode' => $v['consignee_postcode'],
                    'consignee_areacode' => $v['consignee_areacode'],
                    'consignee_telephone' => $v['consignee_telephone'],
                    'consignee_mobile' => $v['consignee_mobile'],
                    'consignee_fax' => $v['consignee_fax'],
                    'consignee_email' => $v['consignee_email'],
                    'consignee_certificatecode' => $v['consignee_certificatecode'],
                    'consignee_mallaccount' => $v['consignee_mallaccount'],
                    'consignee_credentials_period' => $v['consignee_credentials_period'],
                    'consignee_certificatetype' => $v['consignee_certificatetype'],
            		'consignee_tax_no' => $v['tax_no'],
                    'consignee_doorplate' => $v['consignee_doorplate'],
            );
             //print_r($consignee);exit;

            if(empty($consignee['consignee_name'])){
                 $this->_errArr[] = Ec::Lang('收件人姓名不可为空');
             }
           
             if(empty($consignee['consignee_street'])){
                 $this->_errArr[] = Ec::Lang('收件人地址不可为空');
             }
            //验证国家是否存在,通过标准国家二字码与客户自定义国家映射
            //$v['shipper_countrycode'] = $this->_checkCountryExist($v['shipper_countrycode']);  //修改从excel读取到的原始数据  
            //$fileData[$k][$map_flip['shipper_countrycode']] = $v['shipper_countrycode'];
            $shipper = array(
                    // 'shipper_account' => $v['shipper_account'],
                    'shipper_name' => $v['shipper_name'],
                    'shipper_company' => $v['shipper_company'],
                    'shipper_countrycode' => $v['shipper_countrycode'],
                    'shipper_province' => $v['shipper_province'],
                    'shipper_city' => $v['shipper_city'],
                    'shipper_street' => $v['shipper_street'],
                    'shipper_postcode' => $v['shipper_postcode'],
                    'shipper_areacode' => $v['shipper_areacode'],
                    'shipper_telephone' => $v['shipper_telephone'],
                    'shipper_mobile' => $v['shipper_mobile'],
                    'shipper_email' => $v['shipper_email'],
                    'shipper_certificatecode' => $v['shipper_certificatecode'],
                    'shipper_certificatetype' => $v['shipper_certificatetype'],
                    'shipper_fax' => $v['shipper_fax'],
                    'shipper_mallaccount' => $v['shipper_mallaccount']
            );
             //print_r($shipper);die;
            /* if(empty($shipper['shipper_name'])||empty($shipper['shipper_street'])){//excel中的发件人未填写，取默认发件人
//                 print_r($this->_default_shipper_account);exit;
                if($this->_default_shipper_account){
                    $shipperArr = Service_CsiShipperTrailerAddress::getByField($this->_default_shipper_account, 'shipper_account');
                    if($shipperArr){
                        $shipper = $shipperArr;
//                         print_r($shipper);exit;
                    }                    
                }
            } */
            
            $invoice = array();
            
            foreach($v as $kk => $vv){
                if(preg_match('/^AAA_/', $kk) && preg_match('/_[0-9]+$/', $kk)){
                    if(trim($vv) == ''){
                        continue;
                    }
                    $kk = preg_replace('/^AAA_/', '', $kk);
                    if(preg_match('/_([0-9]+)$/', $kk, $m)){
                        $kkk = preg_replace('/'.$m[0].'$/', '', $kk);
                        $invoice[$m[1]][$kkk] = $vv;
                        // print_r($m);exit;
                    }
                }
            }
            //去掉都为空的海关信息
            foreach ($invoice as $k_1=>$v1){
                $flag = false;
                foreach ($v1 as $vv){
                    if(!empty($vv)){
                        $flag=true;
                        break;
                    }
                }
                if(!$flag){
                    unset($invoice[$k_1]);
                }
            }
//             print_r($v);exit;
           /*   foreach($invoice as $kk=>$vv){                
                 if(empty($vv['invoice_enname'])){
                     $this->_errArr[] = Ec::Lang('海关报关品名不可为空',$kk);
                 }               
                 if(empty($vv['invoice_unitcharge'])){
                     $this->_errArr[] = Ec::Lang('申报单价不可为空',$kk);
                 }               
                 if(empty($vv['invoice_quantity'])){
                     $this->_errArr[] = Ec::Lang('申报品数量不可为空',$kk);
                 }
             } */
//            print_r($invoice);//exit;
            //模板新增
            //附加服务：是否退件、关税预付、保险类型、敏感货
            $goods = Service_AtdExtraserviceKind::getAll();
            $good =array();
            foreach ($goods as $gk=> $gv){
            	$good[$gv['extra_service_cnname']] = $gv['extra_service_kind'];//获取所有附加服务 如：$good['1_6元每票']->C1
            }  
            $service = array(
            		'is_return' => strtoupper($v['is_return'])=='Y' ? 'T1':'',//对应模板的“是否退件”
            		'pre_pay' => strtoupper($v['tariff_advance'])=='Y' ? 'G0':'',//对应模板的“关税预付”
            		'sensitve' => $good[$v['sensitive']] ? $good[$v['sensitive']]:'',//对应模板的“敏感货物”
            		'insurance_type' => $good[$v['insurance_type']] ? $good[$v['insurance_type']]:'',//对应模板的保险类型
            );
            foreach ($service as $sk => $sv){//去掉空的附加服务
            	if ($sv ==''){
            		unset($service[$sk]);
            	}
            }
          /*   if ($service['insurance_type']){
            	switch ($service['insurance_type']){//根据保险类型取对应的保险金额
            		case 'C1':
            			$value = 6;
            			break;
            		case 'C2':
            			$value = $order['insurance_value'];
            			break;	
            		case 'C3':
            			$value = 8;
            			break;
            		default:
            			$value = '';
            			break;
            	}	
            }
            $order['insurance_value'] = $value; */

            $data['order'] = $order;
            $data['consignee'] = $consignee;
            $data['shipper'] = $shipper;
            $data['invoice'] = $invoice;
            $data['service'] = $service;
            $data['volume']  =$volume;
            $dataArr[$k] = $data;
        }
        unset($fileDataFormat);
        foreach($dataArr as $k=>$data){
            $process = new Process_Order();
            try{
                $orderArr = $data['order'];
                $consigneeArr = $data['consignee'];
                $shipperArr = $data['shipper'];
                $invoiceArr = $data['invoice'];
                $extraservice = $data['service'];
                $volumeArr = $data['volume'];
                
               /*  print_r($orderArr);
                print_r($consigneeArr);
                print_r($shipperArr);
                print_r($invoiceArr);
                die; */
                $process->setVolume($volumeArr);
                $process->setOrder($orderArr);
                $process->setInvoice($invoiceArr);
                $process->setExtraservice($extraservice);
                $process->setShipper($shipperArr);
                $process->setConsignee($consigneeArr);
                $process->setCreateMethod('upload');
                $status = 'P';
                
                // 当为单个提交时调用事务方法
                if($singleCommitFlag) {
                	// 创建订单
                	$return = $process->createOrderTransaction($status);
                	if($return['ask'] == 0) {
                		throw new Exception();
                	}
                } else {
	                // 创建订单
	                $process->createOrder($status,true);
                }
                $this->_successArr[$k] = $fileData[$k];
            }catch(Exception $ee){   
//                 echo $ee->getMessage();exit;             
                $orderErrs = $process->getErrs();
                $orderErrs[] = $ee->getMessage();
                $this->_errArr[] = "行{$k}:".implode('; ', $orderErrs);
                $this->_errTipArr[$k] = "行{$k}:".implode('; ', $orderErrs);
                
                $this->_failArr[$k] = $fileData[$k];
            }
        }
        if($this->_errArr){
            throw new Exception('数据不合法，导入失败');
        }
    }

    /**
     * 数据处理dhl
     * @param unknown_type $fileData
     * @throws Exception
     */
    protected function _dataProcessDhl($fileData, $singleCommitFlag = false)
    {
        $map = $this->getExcelMapDhl();
        //键值互换array_flip
        $map_flip = array_flip($map);
         
        // 列转换
        $fileDataFormat = array();
        foreach($fileData as $k => $v){
            foreach($v as $kk => $vv){
                if(isset($map[$kk])){
                    $fileDataFormat[$k][$map[$kk]] = $vv;
                }
            }
        }
        $this->_formatArr = $fileDataFormat;
        $dataArr = array();
        //echo microtime_float().'<br>';
        //print_r($fileDataFormat);//die;
        $huilvres = Common_DataCache::getHuilv();
        foreach($fileDataFormat as $k => $v){
            $data = array();
            //验证国家是否存在,通过标准国家二字码与客户自定义国家映射
            //$v['country_code'] = $this->_checkCountryExist($v['country_code']);
            //修改从excel读取到的原始数据
            $fileData[$k][$map_flip['country_code']] = $v['country_code'];
            $v['mail_cargo_type']=$v['mail_cargo_type']=="文件"?3:4;
            $order = array(
                'product_code' => strtoupper($v['product_code']),
                'country_code' => strtoupper($v['country_code']),
                'refer_hawbcode' => strtoupper($v['refer_hawbcode']),
                'order_weight' => empty($v['order_weight'])?1:$v['order_weight'],
                'order_pieces' => empty($v['order_pieces'])?1:$v['order_pieces'],
                 
                'order_length'=>$v['order_length'],
                'order_width'=>$v['order_width'],
                'order_height'=>$v['order_height'],
            	'dangerousgoods'=>empty($v['dangerousgoods'])?0:1,
                'buyer_id' =>$v['buyer_id'],
                'order_id' => $v['order_id'],
                'order_create_code'=>'w',
                'customer_id'=>Service_User::getCustomerId(),
                'creater_id'=>Service_User::getUserId(),
                'modify_date'=>date('Y-m-d H:i:s'),
                'mail_cargo_type' => $v['mail_cargo_type'],
                'tms_id'=>Service_User::getTmsId(),
                'customer_channelid'=>Service_User::getChannelid(),
                'insurance_value' => trim($v['insurance_value1']),
                'insurance_value_gj' => $v['insurance_value_gj'],
            	'invoice_print'=>$v['invoice_print']=='否'?0:1,
            	//'makeinvoicedate'=> $v['makeinvoicedate'],
            	'makeinvoicedate'=> date('Y-m-d H:i:s'),
            	'export_type'=> $v['export_type'],
            	'trade_terms'=> $v['trade_terms'],
            	'invoicenum'=> $v['invoicenum'],
            	'pay_type'=> $v['pay_type'],
            	'fpnote'=> $v['fpnote'],
            	'untread'=>empty($v['untread'])?0:intval($v['untread']),
            );
    		if($v['invoice_print']=="形式发票"){
    			$order['invoice_type'] = 1;
    		}else if($v['invoice_print']=="商业发票"){
    			$order['invoice_type'] = 2;
    		}
            $volume=array(
                'length'=>$v['length'],
                'width'=>$v['width'],
                'height'=>$v['height'],
            );
             
            $consignee = array(
                'consignee_name' => $v['consignee_name'],
                'consignee_company' => $v['consignee_company'],
                'consignee_countrycode' => $v['country_code'],
                'consignee_province' => $v['consignee_province'],
                'consignee_city' => $v['consignee_city'],
                'consignee_street' => $v['consignee_street'],
                'consignee_street2' => $v['consignee_street2'],
                'consignee_street3' => $v['consignee_street3'],
                
                'consignee_postcode' => $v['consignee_postcode'],
                'consignee_areacode' => $v['consignee_areacode'],
                'consignee_telephone' => $v['consignee_telephone'],
                'consignee_mobile' => $v['consignee_mobile'],
                'consignee_fax' => $v['consignee_fax'],
                'consignee_email' => $v['consignee_email'],
                'consignee_certificatecode' => $v['consignee_certificatecode'],
                'consignee_mallaccount' => $v['consignee_mallaccount'],
                'consignee_credentials_period' => $v['consignee_credentials_period'],
                'consignee_certificatetype' => $v['consignee_certificatetype'],
                'consignee_tax_no' => $v['tax_no'],
                'consignee_certificatetype'=>'',
                'consignee_certificatecode'=>'',
            );
            //验证国家是否存在,通过标准国家二字码与客户自定义国家映射
            //$v['shipper_countrycode'] = $this->_checkCountryExist($v['shipper_countrycode']);  //修改从excel读取到的原始数据
           // $fileData[$k][$map_flip['shipper_countrycode']] = $v['shipper_countrycode'];
            //拼接收件人地址
            $shipperStree = str_replace('||', ' ',$v['shipper_street1']);
            $shipperStree.=empty($v['shipper_street2'])?'':'||'.str_replace('||', ' ',$v['shipper_street2']);
            $shipperStree.=empty($v['shipper_street3'])?'':'||'.str_replace('||', ' ',$v['shipper_street3']);
            $shipper = array(
                // 'shipper_account' => $v['shipper_account'],
                'shipper_name' => $v['shipper_name'],
                'shipper_company' => $v['shipper_company'],
                'shipper_countrycode' => $v['shipper_countrycode'],
                'shipper_province' => $v['shipper_province'],
                'shipper_city' => $v['shipper_city'],
                'shipper_street' => $shipperStree,
                'shipper_postcode' => $v['shipper_postcode'],
                'shipper_areacode' => $v['shipper_areacode'],
                'shipper_telephone' => $v['shipper_telephone'],
                'shipper_mobile' => $v['shipper_mobile'],
                'shipper_email' => $v['shipper_email'],
                'shipper_certificatecode' => $v['shipper_certificatecode'],
                'shipper_certificatetype' => $v['shipper_certificatetype'],
                'shipper_fax' => $v['shipper_fax'],
                'shipper_mallaccount' => $v['shipper_mallaccount']
            );
            //print_r($shipper);die;
            /* if(empty($shipper['shipper_name'])||empty($shipper['shipper_street'])){//excel中的发件人未填写，取默认发件人
                //                 print_r($this->_default_shipper_account);exit;
                if($this->_default_shipper_account){
                    $shipperArr = Service_CsiShipperTrailerAddress::getByField($this->_default_shipper_account, 'shipper_account');
                    if($shipperArr){
                        $shipper = $shipperArr;
                        //                         print_r($shipper);exit;
                    }
                }
            } */
    
            $invoice = array();
            $labelArr = array();
            foreach($v as $kk => $vv){
                if(preg_match('/^AAA_/', $kk) && preg_match('/_[0-9]+$/', $kk)){
                    if(trim($vv) == ''){
                        continue;
                    }
                    $kk = preg_replace('/^AAA_/', '', $kk);
                    if(preg_match('/_([0-9]+)$/', $kk, $m)){
                        $kkk = preg_replace('/'.$m[0].'$/', '', $kk);
                        $invoice[$m[1]][$kkk] = $vv;
                        // print_r($m);exit;
                    }
                }
                
                if(preg_match('/^BBB_/', $kk) && preg_match('/_[0-9]+$/', $kk)){
                	if(trim($vv) == ''){
                		continue;
                	}
                	$kk = preg_replace('/^BBB_/', '', $kk);
                	if(preg_match('/_([0-9]+)$/', $kk, $m)){
                		$kkk = preg_replace('/'.$m[0].'$/', '', $kk);
                		$labelArr[$m[1]][$kkk] = $vv;
                		// print_r($m);exit;
                	}
                }
            }
            //去掉都为空的海关信息
            foreach ($invoice as $k_1=>$v1){
                $flag = false;
                foreach ($v1 as $vv){
                    if(!empty($vv)){
                        $flag=true;
                        break;
                    }
                }
                if(!$flag){
                    unset($invoice[$k_1]);
                }
            }
            //print_r($invoice);die;
            $invoice_weight	= 0;
            $invoice_lenght = 0;
            $invoice_width 	= 0;
            $invoice_height	= 0;
            foreach ($invoice as $column=>$vc){
            	$invoice_weight+=$vc["invoice_weight"]*$vc["invoice_quantity"];
            	$invoice_lenght>$vc["invoice_length"]?"":$invoice_lenght=$vc["invoice_length"];
            	$invoice_width>$vc["invoice_width"]?"":$invoice_width=$vc["invoice_width"];
            	$invoice_height>$vc["invoice_height"]?"":$invoice_height=$vc["invoice_height"];
                if(!$vc['invoice_enname']){
                    $vc['invoice_enname'] = $invoice[1]['invoice_enname'];
                    $vc['invoice_cnname'] = $invoice[1]['invoice_cnname'];
                    $vc['invoice_shippertax'] = $invoice[1]['invoice_shippertax'];
                    $vc['invoice_consigneetax'] = $invoice[1]['invoice_consigneetax'];
                    $vc['invoice_totalcharge_all'] = $invoice[1]['invoice_totalcharge_all'];
                    $vc['hs_code'] = $invoice[1]['hs_code'];
                    $invoice[$column]=$vc;
                }
            }
            
            $order['order_length'] = $volume['length'] = intval($invoice_lenght);
            $order['order_width'] = $volume['width'] = intval($invoice_width);
            $order['order_height'] = $volume['height'] = intval($invoice_height);
            $order["order_weight"] = round($invoice_weight,1);
            
            foreach ($labelArr as $k_1_1=>$v1_1){
            	$flag = false;
            	foreach ($v1_1 as $vvv){
            		if(!empty($vvv)){
            			$flag=true;
            			break;
            		}
            	}
            	if(!$flag){
            		unset($labelArr[$k_1_1]);
            	}else{
            		//$labelArr[$k_1_1]['invoice_quantity'] =  $invoice[$k_1_1]['invoice_quantity'];
            	}
            }
            $service=array();
            
            if(!empty($invoice)){
                if($v['mail_cargo_type']==3){
                    //是否勾选文件保险
                    switch ($v['extraservice2']){
                    	case 'DHL文件29元保障服务':$service[] = 'C4';;break;
                    	case 'TNT文件3元保障服务':$service[] = 'C5';break;
                    	case 'TNT文件12元保障服务':$service[] = 'C6';break;
                    }
                    
                }else if($v['mail_cargo_type']==4){
      
                    //计算保险金额
                    if($v['extraservice1']=='是'){
                    	
                    	//huobi
                        $hv  = $huilvres['USD'];
                        if($order["product_code"]=="G_DHL"){
                        	if($v['insurance_value_gj']){
                        		$max_insurance = $invoice[1]['invoice_totalcharge_all']*$hv;
                        		$now_insurance = $v['insurance_value_gj'];
                        		if($max_insurance<$now_insurance){
                        			$this->_errArr[] = Ec::Lang('保险金额不得大于申报价值',$kk);
                        		}else{
                        			$service[] = 'C2';
                        			$order['insurance_value'] = intval(($now_insurance*0.01>100?$now_insurance*0.01:100)*10)/10;
                        		}
                        	}
                        }else{
                        	$max_insurance = $invoice[1]['invoice_totalcharge_all']*$hv;
                        	$order['insurance_value_gj'] = $max_insurance;
                        	$service[] = 'C2';
                        	$order['insurance_value'] = intval(($max_insurance>10000?$max_insurance*0.0015:10)*10)/10;
                        }
                        
                        
                    }
                }
            }
            //DHL 添加了规则，refer用来存取城市代码
            $condtion_sp['cityname'] = $shipper['shipper_city'];
            $condtion_sp['status'] =   1;
            $condtion_sp['productcode'] =   $order["product_code"];
            
            $server_csi_prs=new Service_CsiProductRuleShipper();
            $rs_cisprs = $server_csi_prs->getByCondition($condtion_sp);
            if($rs_cisprs[0]){
            	//如果是DHL不认的替换掉邮编和城市
            	if($rs_cisprs[0]['cityrname']&&$condtion_sp['productcode']=='G_DHL'){
            		if($shipper['shipper_street']){
            			$shipper['shipper_street'].=" ".$shipper['shipper_city'];
            		}
            		$shipper['shipper_city']=$rs_cisprs[0]['cityrname'];
            		$shipper['shipper_postcode']=$rs_cisprs[0]['postcode'];
            	}
            	//ref里面设定上citycode
            	$order['refer_hawbcode'] = $rs_cisprs[0]['citycode'];
            }
            $data['order'] = $order;
            $data['consignee'] = $consignee;
            $data['shipper'] = $shipper;
            $data['invoice'] = $invoice;
            $data['service'] = $service;
            $data['volume']  =$volume;
            $data['label']  =$labelArr;
            $dataArr[$k] = $data;
        }
        unset($fileDataFormat);
        //print_r($dataArr);die;
        foreach($dataArr as $k=>$data){
            $process = new Process_OrderDhl();
            try{
                $orderArr = $data['order'];
                $consigneeArr = $data['consignee'];
                $shipperArr = $data['shipper'];
                $invoiceArr = $data['invoice'];
                $extraservice = $data['service'];
                $volumeArr = $data['volume'];
                $labelArr  = $data['label'];
                $process->setVolume($volumeArr);
                $process->setOrder($orderArr);
                $process->setLabel($labelArr);
                $process->setInvoice($invoiceArr);
                $process->setExtraservice($extraservice);
                $process->setShipper($shipperArr);
                $process->setConsignee($consigneeArr);
                $process->setCreateMethod('upload');
                $status = 'P';
    
                // 当为单个提交时调用事务方法
                //echo microtime_float().'<br>';
               // var_dump($singleCommitFlag);die;
                if($singleCommitFlag) {
                    // 创建订单
                    $return = $process->createOrderTransaction($status);
                    if($return['ask'] == 0) {
                        throw new Exception();
                    }
                } else {
                    // 创建订单
                    $process->createOrder($status,true);

                    //echo microtime_float().'<br>';die;
                }
                $this->_successArr[$k] = $fileData[$k];
            }catch(Exception $ee){
                //                 echo $ee->getMessage();exit;
                $orderErrs = $process->getErrs();
                $orderErrs[] = $ee->getMessage();
                $this->_errArr[] = "行{$k}:".implode('; ', $orderErrs);
                $this->_errTipArr[$k] = "行{$k}:".implode('; ', $orderErrs);
    
                $this->_failArr[$k] = $fileData[$k];
                Ec::showError("**************start*************\r\n"
                				. print_r($ee->getMessage(),true)."\r\n"
                						. "**************end*************\r\n",
                						'UpdateError/info'.date("Ymd"));
            }
        }
        if($this->_errArr){
            throw new Exception('数据不合法，导入失败');
        }
    }
    
    public function upload($file){
        if($file['error']){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'));
        }
        if(empty($file)){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'));
        }
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];
        $pathinfo = pathinfo($fileName);
        if(! isset($pathinfo["extension"]) || $pathinfo["extension"] != "xls"){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'));
        }
        //保存历史上传文件
        $tmp_name = APPLICATION_PATH.'/../data/cache/'.Service_User::getUserId().'-order-import-'.date('Y-m-d_H-i-s').'.'.$pathinfo["extension"];
        @file_put_contents($tmp_name, file_get_contents($filePath));
        
        $fileData = $this->readUploadFile($fileName, $filePath, 0);
        //print_r($fileData);
        return $fileData;
    }
    /**
     * 订单批量导入 手工订单
     *
     * @param unknown_type $file            
     * @param unknown_type $tpl_id            
     * @param unknown_type $user_account            
     * @param unknown_type $platform            
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function importTransaction($file)
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        $successArr = array();
        $failArr = array();
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            //上传数据
            $fileData = $this->upload($file);
            // excel原始信息
            $this->_excel_data = $fileData;
            
//             print_r($fileData);exit;
            if(empty($fileData)){
                throw new Exception(Ec::Lang('文件中必须包含有内容'));
            }
            $clone = $fileData;
            $this->_excel_column = array_keys(array_shift($clone));
            // 数据处理
            $this->_dataProcess($fileData);
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '成功导入订单 ' . count($fileData) . " 个";
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
//             array_unshift($this->_errArr, $e->getMessage());
        }
        $return['errs'] = $this->_errArr;
        $return['errTips'] = $this->_errTipArr; 
        $return['excel_column'] = $this->_excel_column;
        
        $return['success_arr'] = $this->_successArr;
        $return['fail_arr'] = $this->_failArr;
        $return['excel_data'] = $fileData;
        
        $return['not_exist_country_arr'] = $this->getNotExistCountryArr();
//         print_r($return);exit;
        return $return;
    }

    public function submitBatchTransaction($fileData)
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        $successArr = array();
        $failArr = array();
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(empty($fileData)){
                throw new Exception(Ec::Lang('文件中必须包含有内容'));
            }
            $clone = $fileData;
            $this->_excel_column = array_keys(array_shift($clone));
            // 数据处理
            $this->_dataProcess($fileData);
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '成功导入订单 ' . count($fileData) . " 个";
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
            //array_unshift($this->_errArr, $e->getMessage());
        }
        $return['errs'] = $this->_errArr; 
        $return['errTips'] = $this->_errTipArr;         
        $return['excel_column'] = $this->_excel_column;
        
        $return['success_arr'] = $this->_successArr;
        $return['fail_arr'] = $this->_failArr;    
        $return['excel_data'] = $fileData; 
        
        $return['not_exist_country_arr'] = $this->getNotExistCountryArr();  
        return $return;
    }
    
    /**
     * 导入发票数据
     * @param unknown_type $fileData
     */
     public function importInvoiceTransaction($file)
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        
        // 读取数据
        $fileData = $this->readUploadFile($file['name'], $file['tmp_name'], 0);
        if(empty($fileData)){
        	$return['errorMsg'][] = Ec::Lang('文件中必须包含有内容');
        	return $return;
        }
        
        // 数据转换, 模板中文列转换成英文列
        $mapArr = $this->importInvoiceMap();
        // 列转换
        $fileDataFormat = array();
        foreach($fileData as $k => $v){
            foreach($v as $kk => $vv){
                if(isset($mapArr[$kk])){
                    $fileDataFormat[$k][$mapArr[$kk]] = $vv;
                }
            }
        }
//         print_r($fileDataFormat);
//         exit();
        
        // 格式化单个订单的申报数据，模板里面为一行，多列，最多支持3条申报信息，这里把列转换成行形式并以订单号为数组索引
        $dataArr = array();
        foreach($fileDataFormat as $k => $v) {
        	$invoice = array();
        	foreach($v as $kk => $vv){
        		if(preg_match('/^AAA_/', $kk) && preg_match('/_[0-9]+$/', $kk)){
        			if(trim($vv) == ''){
        				continue;
        			}
        			$kk = preg_replace('/^AAA_/', '', $kk);
        			if(preg_match('/_([0-9]+)$/', $kk, $m)){
        				$kkk = preg_replace('/'.$m[0].'$/', '', $kk);
        				$invoice[$m[1]][$kkk] = $vv;
        			}
        		}
        	}
        	
        	$dataArr[$v['shipper_hawbcode']] = $invoice;
        }
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        
        try{
        	
	        // 数据校验
	        $obj = new Process_Order();
	        $orderInvoice = $obj->_verifyInvoiceByCode($dataArr);
	        
	        // 订单验证异常
	        if($obj->_err) {
	        	$return['errorMsg'] = $obj->_err;
	        	return $return;
	        }
        
        	// 订单处理
        	foreach($orderInvoice as $order_id => $row){
        		//     			print_r($order_id);
        		//     			echo "<>";
        		//     			print_r($row);die;
        		$this->_editInvoiceProcess($order_id, $row);
        	}
        
        	$db->commit();
        	$return['ask'] = 1;
        	$return['message'] = Ec::Lang('操作成功');
        } catch(Exception $e) {
        	$db->rollback();
        	$return['message'] = Ec::Lang('操作失败');
        	$return['errorMsg'][] = $e->getMessage();
        }
        
        return $return;
    }
    
    /**
     * 保存文件到服务器留待后续自动服务处理
     */
    public function saveFileAndBatch($file, $reportId, $templateType){
    	
    	$fileName = $file['name'];
    	$filePath = $file['tmp_name'];
    	$pathinfo = pathinfo($fileName);
    	if(! isset($pathinfo["extension"]) || $pathinfo["extension"] != "xls"){
    		throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'));
    	}
    	
//     	print_r($fileName); echo "<1>";
//     	print_r($filePath); echo "<2>";
//     	die;
    	
    	//保存历史上传文件
    	$tmp_name = APPLICATION_PATH.'/../data/cache/'.Service_User::getUserId().'-order-import-'.date('Y-m-d_H-i-s').'.'.$pathinfo["extension"];
    	@file_put_contents($tmp_name, file_get_contents($filePath));
    
    	// 添加批次数据
    	$batch = array(
    			'customer_id' => Service_User::getCustomerId(),
    			'shipper_account' => $this->_default_shipper_account,
    			'report_id' => $reportId,
    			'template_type' => $templateType,
    			'filename' => $fileName,
    			'file_path' => $tmp_name,
    			'ccib_status' => 0,
    			'success_count' => 0,
    			'fail_count' => 0,
    			'createdate' => date('Y-m-d H:i:s'),
    			'creater_id' => Service_User::getUserId(),
    	);
    	
    	Service_CsdCustomerImportBatch::add($batch);
    }
    

    /**
     * 导入发票数据
     * @param unknown_type $fileData
     */
    public function importWeight($file)
    {
    	$return = array(
    			'ask' => 0,
    			'message' => '',
    			'successCount' => 0,
    			'failCount' => 0,
    			'errorMsg' => array(),
    	);
    	
    	// 读取数据
    	$fileData = $this->readUploadFile($file['name'], $file['tmp_name'], 0);
    	if(empty($fileData)){
    		$return['errorMsg'][] = Ec::Lang('文件中必须包含有内容');
    		return $return;
    	}
    	
    	// 数据校验
    	$obj = new Process_Order();
    	foreach($fileData as $k => $row) {
    		
    		$shipper_hawbcode = $row['运单号'];
    		$weight = $row['重量'];
    		
    		$result = $obj->_editWeightTransaction($shipper_hawbcode, $weight);
    		if($result['ask'] == 0) {
    			// 记录失败数
    			$return['failCount'] = $return['failCount'] + 1;
    			$return['errorMsg'][] = "第 " . $k . " 行 " . $shipper_hawbcode . " " . $result['message'];
    		} else {
    			// 记录成功数
    			$return['successCount'] = $return['successCount'] + 1;
    		}
    	}
    	
    	$return['ask'] = 1;
    	return $return;
    }
}