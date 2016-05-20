<?php
class Process_OrderTemplate
{

    protected $_excel_data = array();

    protected $_excel_column = array();

    protected $_successArr = array();

    protected $_failArr = array();

    protected $_errArr = array();

    protected $_errTipArr = array();

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
//             print_r($fileData);//exit;
            if(is_array($fileData)){
                if(!empty($fileData)){
                    $fileData = array_shift($fileData);
                    if(empty($fileData)){
                        throw new Exception( Ec::Lang('模板文件第一行无数据'));
                    }
                    array_unshift($fileData, '');
                    unset($fileData[0]);
//                     $fileData = array_reverse($fileData,true);
//                     $firstRow = array_pop($fileData);
//                     $fileData = array_reverse($fileData,true);
//                     print_r($fileData);exit;
                    return $fileData;
                }else{
                    throw new Exception( Ec::Lang('模板无数据'));
                }
                
            }else{
                throw new Exception($fileData);
            }
        }else{
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'));
        }
    }

    /**
     * 标准模板列
     * @throws Exception
     * @return Ambigous <string, multitype:, mixed>
     */
    public function getStandardColumn(){
        $db = Common_Common::getAdapter();
        $sql = "select * from csd_standard_column order by sc_no asc;";
        $data = $db->fetchAll($sql);
        $arr = array();
        foreach($data as $v){
            $arr[$v['sc_columncode']] = $v;
        }
        return $arr;
    }

    /**
     * 标准模板列
     * @throws Exception
     * @return Ambigous <string, multitype:, mixed>
     */
    public function getMappingType(){
        $db = Common_Common::getAdapter();
        $sql = "select * from csd_column_mappingtype;";
        return $db->fetchAll($sql);
    }
    
    
    
    /**
     * 标准模板
     * @throws Exception
     * @return Ambigous <string, multitype:, mixed>
     */
    public function getStandardTemplate(){
        $standardTemplate = APPLICATION_PATH . '/../public/file/normal_order.xls';
        if(! file_exists($standardTemplate)){
            throw new Exception(Ec::Lang('标准模板不存在'));
        }
        $standardTemplateName = basename($standardTemplate);
        $standardData = $this->readUploadFile($standardTemplateName, $standardTemplate, 0);      
        return $standardData;
    }

    /**
     * 获取保存的excel列头
     * @param unknown_type $report_id
     * @return multitype:unknown
     */
    public function getReportTemplate($report_id){
        //标准模板列
        $standardColumnMap = $this->getStandardColumn();
        
        $db = Common_Common::getAdapter();
        $sql = "select * from csd_customer_reportcolumn where report_id = '{$report_id}' order by column_id asc;";
        $userTemplate = $db->fetchAll($sql);
        $arr = array();
        foreach($userTemplate as $v){
            $column_id = $v['column_id'];
            $sql = "select * from csd_customer_reportmapping where report_id='{$report_id}' and column_id='{$column_id}';";
            $map = $db->fetchRow($sql);
            if($map){
                $v['map_title'] = $standardColumnMap[$map['sc_columncode']]['sc_name'];
            }
            $arr[$v['column_id']] = $v;
        }        
        return $arr;
    }
    
    
    public function getUserTemplate($file)
    {
        $return = array(
            'ask' => 0,
            'message' => Ec::Lang('fail')
        );
        try{
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
            
            $fileData = $this->readUploadFile($fileName, $filePath, 0);
            $customer_column = array();
            foreach($fileData as $column_id => $column_name){
                $customer_column[$column_id] = array(
                    'column_id' => $column_id,
                    'column_name' => $column_name
                );
            }
            $return['file_name'] = $fileName;
            $return['file_data'] = $customer_column;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('操作成功');
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 保存客户模板
     * @param unknown_type $arr
     */
    public function saveUserTemplate($customer_column,$standard_column,$fileName='',$report_id=''){
        $return = array(
            'ask' => 0,
            'message' => Ec::Lang('fail')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            //标准模板列
            $standardColumnMap = $this->getStandardColumn();
            
            foreach($standard_column as $k => $v){
//                 $v['sc_require'] = strtoupper($v['sc_require']);
//                 if($v['sc_require']=='Y'&&empty($v['mt_value'])){
//                     $this->_errArr[] = $v['sc_name'] . Ec::Lang('必须映射');
//                 }
                $column = $standardColumnMap[$v['sc_columncode']];//标准列
                $require = $column['sc_require'];
                $require = strtoupper($require);
                if($require=='Y'&&empty($v['mt_value'])){
                    $this->_errArr[] = $v['sc_name'] . Ec::Lang('必须映射');
                }                
            }
            if($this->_errArr){
                throw new Exception(Ec::Lang('数据验证异常'));
            }
            if($report_id){
                $sql = "select * from csd_customer_report where report_id='{$report_id}'";
                $exist = $db->fetchRow($sql);
                if(! $exist){
                    throw new Exception(Ec::Lang('Inner Error'));
                }
                $report = array(
                    'report_modifydate' => date('Y-m-d H:i:s'),
                    'report_modify_id' => Service_User::getUserId()
                );
                $where = $db->quoteInto('report_id = ?', $report_id);
                
                $db->update('csd_customer_report', $report, $where);
            }else{
                $report = array(
                    'customer_id' => Service_User::getCustomerId(),
                    'report_filename' => $fileName,
                    'report_createdate' => date('Y-m-d H:i:s'),
                    'report_creater_id' => Service_User::getUserId(),
                    'report_modifydate' => date('Y-m-d H:i:s'),
                    'report_modify_id' => Service_User::getUserId()
                );
                $db->insert('csd_customer_report', $report);
                $report_id = $db->lastInsertId();
            }
            $where = $db->quoteInto('report_id = ?', $report_id);
            $db->delete('csd_customer_reportcolumn', $where);
            // echo $report_id;
            foreach($customer_column as $k => $v){
                $row = array(
                    'report_id' => $report_id,
                    'column_id' => $v['column_id'],
                    'column_name' => $v['column_name']
                );
                // print_r($row);
                $db->insert('csd_customer_reportcolumn', $row);
            }
            
            $where = $db->quoteInto('report_id = ?', $report_id);
            $db->delete('csd_customer_reportmapping', $where);
            
            foreach($standard_column as $k => $v){                
                if($v['mt_value']){
                    $columnIdArr = explode(',', $v['mt_value']);
                    foreach($columnIdArr as $column_id){
                        $column_name = $customer_column[$column_id]['column_name'];
                        $row = array(
                                'report_id' => $report_id,
                                'sc_id' => $v['sc_id'],
                                'sc_columncode' => $v['sc_columncode'],
                                'mt_code' => $v['mt_code'],
                                'mt_value' => $v['mt_value'],
                                'column_id'=> $column_id,
                                'column_name'=> $column_name,
                        );
                        $db->insert('csd_customer_reportmapping', $row);
                    }
                }else{
//                     $row = array(
//                             'report_id' => $report_id,
//                             'sc_id' => $v['sc_id'],
//                             'sc_columncode' => $v['sc_columncode'],
//                             'mt_code' => $v['mt_code'],
//                             'mt_value' => $v['mt_value'],
//                             'column_id'=>'',
//                             'column_name'=>'',
//                     );
//                     $db->insert('csd_customer_reportmapping', $row);
                }
                
            }
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('操作成功');
            $return['report_id'] = $report_id;
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_errArr;
        return $return;
    }
    /**
     * 保存客户模板
     * @param unknown_type $arr
     */
    public function deleteTemplate($report_id){
        $return = array(
            'ask' => 0,
            'message' => Ec::Lang('fail')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $where = $db->quoteInto('report_id = ?', $report_id);
            $db->delete('csd_customer_report', $where);
            $db->delete('csd_customer_reportcolumn', $where);            
            $db->delete('csd_customer_reportmapping', $where);            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('删除成功');
            $return['report_id'] = $report_id;
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_errArr;
        return $return;
    }
    /**
     * 生成标准模板
     */
    public static function genStandardColumn(){
        $upload = new Process_OrderUpload();
        $map = $upload->getExcelMap();
        // exit;
        $process = new Process_OrderTemplate();
        // 标准模板
        $standardTemplate = $process->getStandardTemplate();
        $db = Common_Common::getAdapter();
        foreach($standardTemplate as $k => $v){
            $v = trim($v);
            if(! $map[$v]){
                continue;
            }
            $arr = array(
                'sc_no' => $k,
                'sc_columncode' => $map[$v],
                'sc_name' => $v,
                'sc_ename' => trim(str_replace('_', ' ', str_replace('AAA_', '', $map[$v]))),
                'sc_note' => $v,
                'sc_require'=>'',
            );
            switch(strtolower($map[$v])){//标准模板必填项
                case 'country_code'://目的国家
                    
                case 'consignee_name'://收件人姓名
                    
                case 'consignee_street'://收件人地址
                    $arr['sc_require'] = 'Y';
                    break;
                default:
            }
            try{
                $sql = "select * from csd_standard_column where sc_columncode='{$map[$v]}';";
                $exist = $db->fetchRow($sql);
                if(! $exist){
                    $db->insert('csd_standard_column', $arr);
                }else{
                    $where = $db->quoteInto('sc_id = ?', $exist['sc_id']);
                    
                    $db->update('csd_standard_column', $arr, $where);
                }
            }catch(Exception $e){
                print_r($v);
                print_r($arr);
            }
        }
//         print_r($map);
        print_r($standardTemplate);
        exit();
    }
    
}