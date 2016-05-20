<?php
class Process_SkuRelationUpload
{

    protected $_excel_data = array();

    protected $_excel_column = array();

    protected $_successArr = array();

    protected $_failArr = array();

    protected $_errArr = array();

    protected $_errTipArr = array();

    protected $_company_code = null;

    protected $_dataArr = array();

    public function setCompanyCode($company_code)
    {
        $this->_company_code = $company_code;
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
            $fileData = Common_Upload::readEXCEL($filePath, $sheet, true, false);
            // print_r($fileData);exit;
            if(is_array($fileData)){
                $result = array();
                $columnMap = array();
                
                // php hack
                // array_shift 会将所有的数字键名将改为从零开始计数，文字键名将不变
                $fileData = array_reverse($fileData, true);
                $firstRow = array_pop($fileData);
                $fileData = array_reverse($fileData, true);
                
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
                // print_r($fileData);exit;
                return $result;
            }else{
                return $fileData;
            }
        }else{
            return Ec::Lang('文件格式不正确，请上传xls文件');
        }
    }

    public function getExcelMap()
    {
        $map = array(
            '平台SKU' => 'product_sku',
            '平台账号' => 'user_account'
        );
        for($i = 1;$i <= 100;$i ++){
            $map['申报品名' . $i] = 'AAA_pcr_product_sku_' . $i;
            $map['申报数量' . $i] = 'AAA_pcr_quantity_' . $i;
            $map['数量' . $i] = 'AAA_pcr_quantity_' . $i;
            
            $map['对应SKU' . $i] = 'AAA_pcr_product_sku_' . $i;
            $map['对应数量' . $i] = 'AAA_pcr_quantity_' . $i;
        }
        
        return $map;
    }

    /**
     * 数据处理
     *
     * @param unknown_type $fileData            
     * @throws Exception
     */
    protected function _dataProcess($fileData)
    {
        $this->_company_code = $this->_company_code ? $this->_company_code : Common_Company::getCompanyCode();
        $map = $this->getExcelMap();
        // 键值呼唤array_flip
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
        // print_r($fileDataFormat);exit;
        $dataArr = array();
        foreach($fileDataFormat as $k => $v){
            $data = array();
            $data['product_sku'] = $v['product_sku'];
            $data['user_account'] = $v['user_account'];
            $data['company_code'] = $this->_company_code;
            $invoice = array();
            
            foreach($v as $kk => $vv){
                if(preg_match('/^AAA_/', $kk) && preg_match('/_[0-9]+$/', $kk)){
                    if(trim($vv) == ''){
                        continue;
                    }
                    $kk = preg_replace('/^AAA_/', '', $kk);
                    if(preg_match('/_([0-9]+)$/', $kk, $m)){
                        $kkk = preg_replace('/' . $m[0] . '$/', '', $kk);
                        $invoice[$m[1]][$kkk] = $vv;
                    }
                }
            }
            
            $data['invoice'] = $invoice;
            $dataArr[$k] = $data;
        }
        $this->_dataArr = $dataArr;
        
        if($this->_errArr){
            throw new Exception('数据不合法，导入失败');
        }
    }

    public function upload($file)
    {
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
        // 保存历史上传文件
        $tmp_name = APPLICATION_PATH . '/../data/cache/' . Service_User::getUserId() . '-sku-relation-' . date('Y-m-d_H-i-s') . '.' . $pathinfo["extension"];
        @file_put_contents($tmp_name, file_get_contents($filePath));
        
        $fileData = $this->readUploadFile($fileName, $filePath, 0);
        // print_r($fileData);exit;
        return $fileData;
    }

    /**
     * 批量导入 手工订单
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
            // 上传数据
            $fileData = $this->upload($file);
            // excel原始信息
            $this->_excel_data = $fileData;
            
            // print_r($fileData);exit;
            if(empty($fileData)){
                throw new Exception(Ec::Lang('文件中必须包含有内容'));
            }
            $clone = $fileData;
            $this->_excel_column = array_keys(array_shift($clone));
            // 数据处理
            $this->_dataProcess($fileData);
            
            $dataArr = $this->_dataArr;
            foreach($dataArr as $k => $data){
                $process = new Process_SkuRelationProcess();
                try{
                    $process->setCompanyCode($this->_company_code);
                    $process->setUserAccount($data['user_account']);
                    $process->setProductSku($data['product_sku']);
                    $process->setPcrProductSkuArr($data['invoice']);
                    // print_r($data);exit;
                    //
                    $rs = $process->process();
                    if($rs['ask']){
                        $this->_successArr[$k] = $fileData[$k];
                    }else{
                        throw new Exception($rs['message']);
                    }
                }catch(Exception $ee){
                    // echo $ee->getMessage();exit;
                    $orderErrs = $process->getErrs();
                    // $this->_errArr[] = $ee->getMessage();
                    $this->_errArr[] = "行{$k}:" . implode('; ', $orderErrs);
                    $this->_errTipArr[$k] = "行{$k}:" . implode('; ', $orderErrs);
                    $this->_failArr[$k] = $fileData[$k];
                }
            }
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '成功导入 ' . count($fileData) . " 个";
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
            // array_unshift($this->_errArr, $e->getMessage());
        }
        $return['errs'] = $this->_errArr;
        $return['errTips'] = $this->_errTipArr;
        $return['excel_column'] = $this->_excel_column;
        
        $return['success_arr'] = $this->_successArr;
        $return['fail_arr'] = $this->_failArr;
        $return['excel_data'] = $fileData;
        
        // print_r($return);exit;
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
            // array_unshift($this->_errArr, $e->getMessage());
        }
        $return['errs'] = $this->_errArr;
        $return['errTips'] = $this->_errTipArr;
        $return['excel_column'] = $this->_excel_column;
        
        $return['success_arr'] = $this->_successArr;
        $return['fail_arr'] = $this->_failArr;
        $return['excel_data'] = $fileData;
        
        return $return;
    }
}