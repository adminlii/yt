<?php
class Process_OrderUploadUserTemplate extends Process_OrderUpload
{

    protected $_tmp_table = 'csd_customer_reportcolumn_tmp';
    // 匹配到的自定义模板
    protected $_match_report = array();

    protected $_report_id = 0; 
    
    protected $_template_type = 'standard'; // standard标准模板,customer客户自定义模板
    /**
     * 读取上传文件列头
     *
     * @param unknown_type $fileName            
     * @param unknown_type $filePath            
     * @return string mixed Ambigous
     */
    public function readUploadFileHeader($file)
    {
        if($file['error']){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件','1001'));
        }
        if(empty($file)){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件','1001'));
        }
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];
        $sheet = 0;
        $pathinfo = pathinfo($fileName);
        if(! isset($pathinfo["extension"]) || $pathinfo["extension"] != "xls"){
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件'),'1001');
        }
        $pathinfo = pathinfo($fileName);
        $fileData = array();
        if(isset($pathinfo["extension"]) && $pathinfo["extension"] == "xls"){
            $fileData = Common_Upload::readEXCEL($filePath, $sheet, true, false);
           // echo "<pre>";print_r($fileData);exit;
            if(is_array($fileData)){
                if(!empty($fileData) && count($fileData) > 1){
                    $fileData = array_shift($fileData);
                    if(empty($fileData)){
                        throw new Exception(Ec::Lang('模板文件第一行无数据'));
                    }
                    array_unshift($fileData, '');
                    unset($fileData[0]);
                    // $fileData = array_reverse($fileData,true);
                    // $firstRow = array_pop($fileData);
                    // $fileData = array_reverse($fileData,true);
                    // print_r($fileData);exit;
                    return $fileData;
                } else {
                    throw new Exception(Ec::Lang('模板无数据'));
                }
            }else{
                throw new Exception($fileData);
            }
        }else{
            throw new Exception(Ec::Lang('文件格式不正确，请上传xls文件',1001));
        }
    }

    /**
     * 创建临时表,用后删除
     * 
     * @param unknown_type $prefix            
     * @return string
     */
    public function getTmpTable()
    {
        $db = Common_Common::getAdapter();
        $config = $db->getConfig();
        $tmp_table = 'csd_customer_reportcolumn_' . time();
        while(true){
            $tmp_table = 'csd_customer_reportcolumn__' . time() . _ . Common_Common::random(10, 1);
            $sql = "SHOW TABLES where Tables_in_{$config['dbname']}='{$tmp_table}'";
            $row = $db->fetchRow($sql);
            if(! $row){
                break;
            }
        }
        $sql = "CREATE TABLE `{$tmp_table}` (
                  `id` int(5) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID',
                  `column_id` int(3) NOT NULL COMMENT '列id，同报表内从1开始累计，每次加1',
                  `column_name` varchar(50) NOT NULL COMMENT '列名称，一般为列标题头内容'
                )COMMENT='临时表';
                ";
        $db->query($sql);
        $this->_tmp_table = $tmp_table;
        return $tmp_table;
    }

    public function getExcelMap()
    {
        return parent::getExcelMap();
    }

    /**
     * 模板转换
     * 自定义模板转为标准模板
     * 
     * @param unknown_type $fileData            
     * @param unknown_type $report_id            
     * @return Ambigous <multitype:, string>
     */
    protected function dataTransform($fileData, $report_id, $reportcolumnArr)
    {
        $db = Common_Common::getAdapter();
        // 标准列
        $sql = "SELECT * FROM  csd_standard_column a order by a.sc_no asc;";
        $map = $db->fetchAll($sql);
        $mapArr = array();
        // print_r($reportcolumnArr);
        // 映射列,可能一对多
        foreach($map as $v){
            $sql = "select DISTINCT sc_id,sc_columncode,mt_code,mt_value,report_id from csd_customer_reportmapping where report_id='{$report_id}' and sc_id='{$v['sc_id']}'";
            $row = $db->fetchRow($sql);
            if($row){
                // 标准列对应的客户模板列
                $mt_value = $row['mt_value'];
                $column_id_arr = explode(',', $mt_value);
                // print_r($column_id_arr);exit;
                foreach($column_id_arr as $column_id){
                    $mapArr[$v['sc_name']][] = $reportcolumnArr[$column_id];
                }
            }else{
                $mapArr[$v['sc_name']] = array();
            }
        }
        // print_r($reportcolumnArr);
        // print_r($mapArr);exit;
        
        // print_r($fileData);exit;
        
        $fileDataArr = array();
        foreach($fileData as $k => $v){
            foreach($mapArr as $kk => $vv){
                $mt_value_map = $vv;
                $val = '';
                foreach($mt_value_map as $vvv){
                    $val = $val . '' . $v[$vvv];
                }
                $fileDataArr[$k][$kk] = $val;
            }
        }
        // print_r($fileDataArr);
        // exit();
        return $fileDataArr;
    }

    /**
     * 上传文件根据模板映射整理成标准模板数据
     * 
     * @param unknown_type $file            
     * @throws Exception
     * @return Ambigous <Ambigous, multitype:, string>
     */
    public function getImportFileData($file)
    {
        $this->_template_type = '';
        //$db = Common_Common::getAdapter();
        // 自定义模板excel第一列
        //$header = $this->readUploadFileHeader($file);
        //var_dump($header);
        // //临时表，用后删除
        // $tmp_table = $this->getTmpTable();
        // $tmp_table = 'csd_customer_reportcolumn__1414742877_2292825346';
        // $db->delete($tmp_table,'1=1');//exit;
        // foreach($header as $column_id=>$column_name){
        // $row = array(
        // 'column_id' => $column_id,
        // 'column_name' => $column_name
        // );
        // $db->insert($tmp_table, $row);
        // }
        $report_id = 0;
        // 取最新修改过的模板
        //$sql = "select * from csd_customer_report where customer_id='" . Service_User::getCustomerId() . "' or customer_id is null or customer_id ='' or customer_id ='0'  order by customer_id desc, report_modifydate desc;";
        //$reportArr = $db->fetchAll($sql);
        // 遍历自定义模板,比较列序号与列名
       /*  foreach($reportArr as $report){
            // 按照序号排列
            $sql = "select * from csd_customer_reportcolumn  where  report_id='{$report['report_id']}' order by column_id asc;";
            $reportcolumn = $db->fetchAll($sql);
            $reportcolumnArr = array();
            foreach($reportcolumn as $v){
                $reportcolumnArr[$v['column_id']] = $v['column_name'];
            }
//             echo "1-";
//             print_r($header);
//             echo "-2-";
//             print_r($reportcolumnArr); exit;
            $diff = array_diff_assoc($header, $reportcolumnArr);//echo "<pre>";print_r($diff);die;
            if(empty($diff)){ // 列头完全一致，取当前模板(序号与列名一致)
                $report_id = $report['report_id'];
                $this->_match_report = $report;
                $this->_report_id = $report_id;
                $this->_template_type = 'customer';
                // 上传数据
                $fileData = $this->upload($file);
                // 转为标准模板数据
                $fileData = $this->dataTransform($fileData, $report_id, $reportcolumnArr);
                
                break;
            }
             
        } */
        if(! $report_id){
            // 取标准模板列
           /*  $sql = "SELECT sc_no,sc_name FROM `csd_standard_column` order by sc_no asc";
            $reportcolumn = $db->fetchAll($sql);
            $reportcolumnArr = array();
            foreach($reportcolumn as $v){
                $reportcolumnArr[$v['sc_no']] = $v['sc_name'];
            }
            
            $diff = array_diff_assoc($header, $reportcolumnArr); */
//             print_r($header);
//             print_r($reportcolumnArr);
//             print_r($diff);exit;
            $diff = '';
            if(! empty($diff)){
                $this->_report_id = 0; // 与标准模板不匹配
                throw new Exception(Ec::Lang('没有找到匹配的模板'));
            }else{
                $this->_template_type = 'standard';
                // 使用标准模板
                // 上传数据
                $fileData = $this->upload($file);
            }
        }
       
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
            // 获取到上传数据,并整理成标准模板数据
            $fileData = $this->getImportFileData($file);
            // excel原始信息
            $this->_excel_data = $fileData;
            
            //print_r($fileData);exit;
            if(empty($fileData)){
                throw new Exception(Ec::Lang('文件中必须包含有内容'));
            }
        	if(count($fileData)>500){
                throw new Exception(Ec::Lang('当前批量导入仅支持400条数据，请拆分多个文件分批导入'));
            }
            $clone = $fileData;
            
            $this->_excel_column = array_keys(array_shift($clone));
            if(in_array('制作发票(是,否)', $this->_excel_column)){
                $this->_dataProcessDhl($fileData);
                
            }else
            // 数据处理
            $this->_dataProcess($fileData);
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '成功导入订单 ' . count($fileData) . " 个";
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
            $return['error_code'] = $e->getCode();
//             echo $e->getMessage();exit;
            array_unshift($this->_errArr, $e->getMessage());
        }
        // 匹配到的模板
        $return['report'] = $this->_match_report;
        // 匹配到的模板ID
        $return['report_id'] = $this->_report_id;
        // 模板类型
        $return['template_type'] = $this->_template_type;
        
        $return['errs'] = $this->_errArr;
        $return['errTips'] = $this->_errTipArr;
        $return['excel_column'] = $this->_excel_column;
        
        $return['success_arr'] = $this->_successArr;
        $return['fail_arr'] = $this->_failArr;
        $return['excel_data'] = $fileData;
        
        $return['not_exist_country_arr'] = $this->_notExistCountryArr;
        // print_r($return);exit;
        return $return;
    }

    /**
     * 使用标准模板
     * @see Process_OrderUpload::submitBatchTransaction()
     */
    public function submitBatchTransaction($fileData)
    {
        $return = parent::submitBatchTransaction($fileData);
        //var_dump($return);
        // 匹配到的模板
        $return['report'] = $this->_match_report;
        // 匹配到的模板ID
        $return['report_id'] = $this->_report_id;
        // 模板类型
        $return['template_type'] = $this->_template_type;
        
        $return['not_exist_country_arr'] = $this->_notExistCountryArr;
        
        return $return;
    }

    /**
     * 订单批量导入 手工订单(异步)
     * 先保存文件到服务器然后处理
     * @param unknown_type $file
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function importByAsynchTransaction($file)
    {
    	$return = array(
    			'ask' => 0,
    			'message' => ''
    	);
    
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	try{
    		// 获取到上传数据,并整理成标准模板数据
    		$this->saveFileData($file);
    
    		$db->commit();
    		
    		$return['ask'] = 1;
    		$return['message'] = "文件保存成功, 请稍后查看结果。";
    	}catch(Exception $e){
    		$db->rollback();
    		$return['message'] = $e->getMessage();
    	}
    	
    	// 模板类型
    	$return['template_type'] = $this->_template_type;
    	
    	// print_r($return);exit;
    	return $return;
    }
    
    /**
     * 上传文件根据模板映射整理成标准模板数据
     *
     * @param unknown_type $file
     * @throws Exception
     * @return Ambigous <Ambigous, multitype:, string>
     */
    public function saveFileData($file)
    {
    	$db = Common_Common::getAdapter();
    	// 自定义模板excel第一列
    	$header = $this->readUploadFileHeader($file);
    	
    	$report_id = 0;
    	// 取最新修改过的模板
    	$sql = "select * from csd_customer_report where customer_id='" . Service_User::getCustomerId() . "' or customer_id is null or customer_id ='' or customer_id ='0'  order by customer_id desc, report_modifydate desc;";
    	$reportArr = $db->fetchAll($sql);
    	// 遍历自定义模板,比较列序号与列名
    	foreach($reportArr as $report){
    		// 按照序号排列
    		$sql = "select * from csd_customer_reportcolumn  where  report_id='{$report['report_id']}' order by column_id asc;";
    		$reportcolumn = $db->fetchAll($sql);
    		$reportcolumnArr = array();
    		foreach($reportcolumn as $v){
    			$reportcolumnArr[$v['column_id']] = $v['column_name'];
    		}
    		//             echo "1-";
    		//             print_r($header);
    		//             echo "-2-";
    		//             print_r($reportcolumnArr); exit;
    		$diff = array_diff_assoc($header, $reportcolumnArr);
    		if(empty($diff)){ // 列头完全一致，取当前模板(序号与列名一致)
    			$report_id = $report['report_id'];
//     			$this->_match_report = $report;
//     			$this->_report_id = $report_id;
    			$this->_template_type = 'customer';
    			// 上传数据
    			$this->saveFileAndBatch($file, $report_id, 'C');
    			break;
    		}
    		// print_r($diff);
    	}
    	if(! $report_id){
    		// 取标准模板列
    		$sql = "SELECT sc_no,sc_name FROM `csd_standard_column` order by sc_no asc";
    		$reportcolumn = $db->fetchAll($sql);
    		$reportcolumnArr = array();
    		foreach($reportcolumn as $v){
    			$reportcolumnArr[$v['sc_no']] = $v['sc_name'];
    		}
    
    		$diff = array_diff_assoc($header, $reportcolumnArr);
    		//             print_r($header);
    		//             print_r($reportcolumnArr);
    		//             print_r($diff);exit;
    		if(! empty($diff)){
    			throw new Exception(Ec::Lang('没有找到匹配的模板'));
    		} else {
    			// 使用标准模板
    			// 上传数据
    			$this->_template_type = 'standard';
    			$this->saveFileAndBatch($file, 0, 'S');
    		}
    	}
    }
    
    /**
     * 处理导入批次
     */
    public function processImportBatch() {
    	// 查询批次数据
    	$batchs = Service_CsdCustomerImportBatch::getByCondition(array('ccib_status' => '0'));
    	if(empty($batchs)) {
    		echo Common_Common::myEcho("无待处理批次数据");
    		return;
    	}
    	
    	echo Common_Common::myEcho("此次需处理 " . count($batchs) . " 批数据");
    	foreach ($batchs as $k => $row) {
    		
    		// 设置客户信息(由于原逻辑用到了会话里面的一些属性，而后台服务没有，故在执行前需初始化用户，客户数据)
    		$user = Service_User::getByField($row['creater_id']);
    		$sql = "select * from csi_customer where customer_id='{$row['customer_id']}';";
            $customer = Common_Common::fetchRow($sql);
            
            Service_User::setUser($user, $customer);
            
            // 设置发件人数据
            $this->setDefaultShipperAccount($row['shipper_account']);
            
    		$this->processImportBatchTransaction($row);
    		
    		// 完成后销毁
    		Service_User::destroyUser($user, $customer);
    	}
    	
    	echo Common_Common::myEcho("客户导入批次处理完成");
    }
    
    /**
     * 单个导入批次事务
     */
    public function processImportBatchTransaction($batch = array()) {
    	
    	// 获取批次数据
    	$fileData = $this->readUploadFile($batch['filename'], $batch['file_path'], 0);
    	
    	// 当为客户模板则转换成标签模板处理
    	if($batch['template_type'] == 'S') {
    		
    		// 按照序号排列
    		$sql = "select * from csd_customer_reportcolumn  where  report_id='{$batch['report_id']}' order by column_id asc;";
    		$reportcolumn = Common_Common::getAdapter()->fetchAll($sql);
    		$reportcolumnArr = array();
    		foreach($reportcolumn as $v){
    			$reportcolumnArr[$v['column_id']] = $v['column_name'];
    		}
    		
    		// 转为标准模板数据
    		$fileData = $this->dataTransform($fileData, $batch['report_id'], $reportcolumnArr);
    	} 
    	
    	try {
	    	// 数据处理
	    	$this->_dataProcess($fileData, true);
    	} catch (Exception $e) {
    		// 
    	}
    	
//     	echo "<1>"; print_r($this->_failArr); 
//     	echo "<2>"; print_r($this->_successArr);
//     	die;
    	// 更新批次数据
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	
    	try {
    		
    		// 保存错误数据
    		foreach($this->_errTipArr as $k => $val) {
    			$batch_detail = array(
    					'ccib_id' => $batch['ccib_id'],
    					'line_row' => $k,
    					'shipper_hawbcode' => $this->_formatArr[$k]['shipper_hawbcode'],
    					'note' => $val,
    			);
    			
    			Service_CsdCustomerImportBatchDetail::add($batch_detail);
    		}
    		
    		// 更新批次
    		$batch_update = array(
    				'ccib_status' => 1,
    				'success_count' => count($this->_successArr), 
    				'fail_count' => count($this->_failArr), 
    				'modifydate' => date('Y-m-d H:i:s'), 
    				);
	    	Service_CsdCustomerImportBatch::update($batch_update, $batch['ccib_id']);
	    	
	    	$db->commit();
    	} catch(Exception $e) {
    		$db->rollback();
    		echo Common_Common::myEcho("批次ID: " . $batch['ccib_id'] . " 处理失败。" . $e->getMessage());
    	}
    }
}