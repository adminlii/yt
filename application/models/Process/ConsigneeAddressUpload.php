<?php
class Process_ConsigneeAddressUpload
{

    protected $_excel_data = array();
    
    protected $_excel_column = array();

    protected $_successArr = array();

    protected $_failArr = array();

    protected $_errArr = array();
    
    protected $_errTipArr = array();
    
    protected $_formatArr = array();
    

    public function exportColumnMap(){
        $map = array(
            "consignee_name"=>"联系人",
        	"consignee_company"=>"公司",
        	"consignee_countrycode"=>"国家",
        	"consignee_province"=>"州",
       		"consignee_city"=>"城市",
       		"consignee_street"=>"地址1",
       		"consignee_street1"=>"地址2",
       		"consignee_street2"=>"地址3",
       		"consignee_postcode"=>"邮编",
       		"consignee_telephone"=>"电话"
        );
        return $map;
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
           
            if(is_array($fileData)){
                $result = array();
                $columnMap = array();

                //php hack 
                //array_shift 会将所有的数字键名将改为从零开始计数，文字键名将不变
                $firstRow = array_shift($fileData);
                $flipHeadRow = array_flip($this->exportColumnMap());
                foreach ($fileData as $filedatak=>$filedatav){
                	$_row = array();
                	foreach ($filedatav as $filedatakk=>$filedatavv){
                		$_row[$flipHeadRow[$firstRow[$filedatakk]]]=$filedatavv;
                	}
                	$result[]=$_row;
                }
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
            '发件人地址' => 'shipper_street',
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
            '发件人增值税/商品服务税号'=>'AAA_invoice_shippertax_1',
            '收件人增值税/商品服务税号'=>'AAA_invoice_consigneetax_1',
            '通关的申报价值'=>'AAA_invoice_totalcharge_all_1',
            '协调商品代码'=>'AAA_hs_code_1',
            '快件保险（是，否）'=>'extraservice1',
            '保险价值' => 'insurance_value_gj',
            '额外服务选项（是，否）'=>'extraservice2',
        );
        for($i = 1;$i <= 100;$i ++){
            $map['配货信息' . $i] = 'AAA_invoice_note_' . $i;
            //$map['单价' . $i.'(usd)'] = 'AAA_invoice_unitcharge_' . $i;
            $map['重量' . $i] = 'AAA_invoice_weight_' . $i;
            //$map['销售地址' . $i] = 'AAA_invoice_url_' . $i;
            $map['长度' . $i.'（cm）'] = 'AAA_invoice_length_' . $i;
            $map['宽度' . $i.'（cm）'] = 'AAA_invoice_width_' . $i;
            $map['高度' . $i.'（cm）'] = 'AAA_invoice_height_' . $i;
            $map['件数' . $i] = 'AAA_invoice_quantity_' . $i;
            //$map['海关编码' . $i] = 'AAA_hs_code_' . $i;
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
     * 数据处理
     * @param unknown_type $fileData
     * @throws Exception
     */
    public  function _dataProcess($fileData)
    {
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	if(empty($fileData)){
    		$this->_errArr[] = "导入数据为空";
    	}
    	$CsiConsigneeTrailerAddress = new Service_CsiConsigneeTrailerAddress();
    	$customer_id= Service_User::getCustomerId();
    	$customer_channelid = Service_User::getChannelid();
        foreach($fileData as $k=>$data){
        	$errorArr=array();
            try{
            
            	$errorArr = $CsiConsigneeTrailerAddress->validator($data);
            	//$data = Common_Common::arrayNullToEmptyString($data);
            	$format = 'Y-m-d H:i:s';
            	$data['modify_date_sys'] = date($format);
            	$data['customer_id'] = $customer_id;
            	$data['customer_channelid'] = $customer_channelid;
            	//验证国家编号是否存在
            	if(empty($this->_getCountry($data['consignee_countrycode']))){
            		$errorArr[]="目的国家不存在";		
            	}
            	$result = $CsiConsigneeTrailerAddress->add($data);
            	if($result){
            		$this->_successArr[$k] = $fileData[$k];
            	}else{
            		$errorArr[]="新增失败(服务器繁忙)";
            	}
            	
            }catch(Exception $ee){
                $errorArr[] = $ee->getMessage();
                
            }
            if(!empty($errorArr)){
            	$this->_errArr[] = "行{$k}:".implode('; ', $errorArr);
            }
        }
        if($this->_errArr){
        	$db->rollback();
        }else{
        	$db->commit();
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
        //$tmp_name = APPLICATION_PATH.'/../data/cache/'.Service_User::getUserId().'-order-import-'.date('Y-m-d_H-i-s').'.'.$pathinfo["extension"];
       // @file_put_contents($tmp_name, file_get_contents($filePath));
        
        $fileData = $this->readUploadFile($fileName, $filePath, 0);
        return $fileData;
    }
    
    public function getErr(){
    	return $this->_errArr;
    }
    protected function _getCountry($country_code)
    {
    	$db = Common_Common::getAdapter();
    	$sql = "select * from idd_country_upload where country_value='{$country_code}'";
    	$country = $db->fetchRow($sql);
    	return $country;
    }
}