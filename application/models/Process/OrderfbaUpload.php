<?php
class Process_OrderfbaUpload 
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
    			'boxnum' =>'箱数',
    			'storage'=>'仓库',
    			'order_id' => Ec::lang('系统主键'),
    			'product_code' => Ec::lang('运输方式'),
    			'shipper_hawbcode' => Ec::lang('客户单号'),
    			'server_hawbcode' => Ec::lang('服务商单号'),
    			'channel_hawbcode' => Ec::lang('渠道换单号'),
    			'shipper_name' => Ec::lang('发件人姓名'),
    			'shipper_company' => Ec::lang('发件人公司名'),
    			'shipper_countrycode' => Ec::lang('发件人国家'),
    			'shipper_province' => Ec::lang('发件人省/州'),
    			'shipper_city' => Ec::lang('发件人城市'),
    			'shipper_street' => Ec::lang('发件人街道地址'),
    			'shipper_postcode' => Ec::lang('发件人邮编'),
    			'shipper_areacode' => Ec::lang('发件人地区代码'),
    			'shipper_telephone' => Ec::lang('发件人电话号码'),
    			'shipper_mobile' => Ec::lang('发件人手机'),
    			'shipper_email' => Ec::lang('发件人邮箱'),
    			'shipper_fax' => Ec::lang('发件人传真'),
    			'shipper_certificatetype' => Ec::lang('发件人证件类型'),
    			'shipper_certificatecode' => Ec::lang('发件人证件号码'),
    			'shipper_mallaccount' => Ec::lang('发件的商城账号'),
    
    			'consignee_name' => Ec::lang('收件人'),
    			'consignee_countrycode' => '国家',
    			'consignee_province' => '州省/state',
    			'consignee_city' => '城市/City',
    			'consignee_street' => '地址',
    			'consignee_postcode' => '邮编/zip',
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
    		$map = $this->exportColumnMap();
    		$dataList = array();
    		foreach($orderIds as $order_id){
    			$data = array();
    			$columns = array(
    					'customer_id',
    					'refer_hawbcode',
    					'product_code',
    					'boxnum',
    			);
    			$order = Service_CsdOrderfba::getByField($order_id, 'order_id', $columns);
    			if($order['customer_id'] != Service_User::getCustomerId()){
    				throw new Exception(Ec::Lang('非法操作'));
    			}
    			
    			$con = array(
    					'order_id' => $order_id
    			);
    			$fields = array(
    					'storage',
    					'consignee_countrycode',
    					'consignee_province',
    					'consignee_city',
    					'consignee_street',
    					'consignee_postcode',
    					'shipper_name',
    					'shipper_countrycode',
    					'shipper_province',
    					'shipper_city',
    					'shipper_street',
    					'shipper_postcode',
    					'shipper_telephone',
    			);
    			$shipperConsignee = Service_CsdShipperconsigneefba::getByField($order_id, 'order_id', $fields);
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
               /*  $result = array();
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
                } */
//                 print_r($fileData);exit;
                return $fileData;
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
            $map['重量' . $i] = 'AAA_invoice_weight_' . $i;
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
             '运输方式代码' => 'product_code',
             '收件人国家' => 'country_code',
             '客户订单号' => 'refer_hawbcode',
             //'买家ID' => 'buyer_id',
             //'交易ID' => 'transaction_id',
             //'订单备注' => 'order_info',
             '外包装件数' => 'order_pieces',
             '货物重量' => 'order_weight',
             '申报类型' => 'mail_cargo_type',
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
                case '礼品';$v['mail_cargo_type']=1;break;
                case '商品货样';$v['mail_cargo_type']=2;break;
                case '文件';$v['mail_cargo_type']=3;break;
                default:$v['mail_cargo_type']=4;break;
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
            $v['shipper_countrycode'] = $this->_checkCountryExist($v['shipper_countrycode']);  //修改从excel读取到的原始数据  
            $fileData[$k][$map_flip['shipper_countrycode']] = $v['shipper_countrycode'];
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
            if(empty($shipper['shipper_name'])||empty($shipper['shipper_street'])){//excel中的发件人未填写，取默认发件人
//                 print_r($this->_default_shipper_account);exit;
                if($this->_default_shipper_account){
                    $shipperArr = Service_CsiShipperTrailerAddress::getByField($this->_default_shipper_account, 'shipper_account');
                    if($shipperArr){
                        $shipper = $shipperArr;
//                         print_r($shipper);exit;
                    }                    
                }
            }
            
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
        foreach($dataArr as $k=>$data){
            $process = new Process_Order();
            try{
                $orderArr = $data['order'];
                $consigneeArr = $data['consignee'];
                $shipperArr = $data['shipper'];
                $invoiceArr = $data['invoice'];
                $extraservice = $data['service'];
                $volumeArr = $data['volume'];
                
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
	                $process->createOrder($status);
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
   
    
    public function upload($file,$savepath,$available=array(),$skip=false,$shell=0){
    	if($file['error']){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'));
        }
        if(empty($file)){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'));
        }
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];
        $pathinfo = pathinfo($fileName);
        if(! isset($pathinfo["extension"]) ||(!empty($available)&&!in_array($pathinfo["extension"], $available)) ){
            throw new Exception(Ec::Lang('文件格式不正确，请上传'.join(',', $available).'文件'));
        }
        do{
        	$filename = date('YmdHis').'_'.rand(1, 10000).'.'.$pathinfo["extension"];
        }while(file_exists($savepath.$filename));
        move_uploaded_file($filePath,$savepath.$filename);
        //file_put_contents($savepath.$filename, file_get_contents($filePath));
        $fileData = array('path'=>$filename);
        if($skip){
        	$_fileData = $this->readUploadFile($fileName, $filePath, $shell);
        	$fileData['data'] = $_fileData;
        }
        return $fileData;
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
    
}