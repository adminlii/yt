<?php
class Service_ProductDevelopUpload
{

    private $_fileName;

    private $_filePath;

    private $_errLog = array();

    /**
     * 读取对应sheet内容
     */
    private function _readUploadFile($sheetName = '产品')
    {
        $fileName = $this->_fileName;
        $filePath = $this->_filePath;
        $pathinfo = pathinfo($fileName);
        $fileData = array();
        
        $fileData = Common_Upload::readEXCEL($filePath, $sheetName);
        $result = array();
        if(is_array($fileData)){
            foreach($fileData as $key => $value){
                if($key == 0){
                    continue;
                }
                foreach($value as $vKey => $vValue){
                    if($fileData[0][$vKey] == ""){
                        continue;
                    }                    
                    /*
                     * $vValue = htmlspecialchars($vValue); $vValue =
                     * str_replace(chr(10), "<br>", $vValue); $vValue =
                     * str_replace(chr(32), "&nbsp;", $vValue);
                     */
                    $result[$key][$fileData[0][$vKey]] = preg_replace('/^\(([^\(\)]+)\).*/', '$1', $vValue);
                }
            }
        }else{
            // $this->_errLog[] = "Sheet ‘{$sheetName}’ ".'异常：'.$fileData;
        }
        // var_dump($result);exit;
        return $result;
    }

    /**
     * 数据转换及校验是否缺少必要列
     *
     * @param unknown_type $data            
     * @param unknown_type $mapSheet            
     * @param unknown_type $sheetName            
     * @throws Exception
     * @return multitype: Ambigous unknown>
     */
    private function _sheetFormat($data, $map, $sheetName = '')
    {
        $result = array();
        if(empty($data)){
            return $result;
        }
        $tmp = array_slice($data, 0, 1);
        $tmp = $tmp[0];
        // print_r($mapSheet);
        // print_r($tmp);
        foreach($map as $k => $v){
            if(! isset($tmp[$k])){
                // '模板文件不正确，sheet:' . ($sheetName) . ' 缺少列:' . $k
                throw new Exception(Ec::Lang('template_err', array(
                    $sheetName,
                    $k
                )));
            }
        }
        // print_r($data);
        $mapKeys = array_keys($map);
        foreach($data as $k => $v){
            foreach($v as $kk => $vv){
                if(! in_array($kk, $mapKeys)){
                    continue;
                }
                // echo $mapSheet[$kk];exit;
                $result[$k][$map[$kk]] = trim($vv);
            }
        }
        // print_r($result);
        // print_r($map);
        // print_r($data);
        // exit;
        return $result;
    }

    /**
     * 产品批量更新导入
     * 把整个excel中的数据读取出来
     * 如果内容以小括号开始，则只是取小括号内的内容，其他内容忽略，如：(1)管理员，转换后变为1
     * 对数据进行整理，判断是否缺少必要字段，并将excel列名转换为数据库中的字段
     * 整理需要插入到相应表的数据，然后将数据插入到对应的表
     */
    public function uploadProductBaseTransaction($file, $lang='en',$cat0=0,$cat1=0,$cat2=0)
    {
        $return = array(
            'ask' => 0,
            'message' => ''
        );
        $err = array();
        $db = Common_Common::getAdapter();
        try{
            if($file['error']){
                // '请选择xls文件'
                throw new Exception(Ec::Lang('pls_select_xls'));
            }
            $this->_fileName = $file['name'];
            $this->_filePath = $file['tmp_name'];
            $data = array();
            // 产品
            $data = $this->_addProductBase($lang,$cat0,$cat1,$cat2);
            if(empty($data)){
                // '请往Sheet‘产品’中录入数据'
                throw new Exception(Ec::Lang('pls_fill_data_in_sheet_product'));
            }
            $return['ask'] = '1';
            // '所有数据处理完成'
            $return['message'] = Ec::Lang('all_data_done');
            $return['data'] = $data;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_errLog;
        return $return;
    }

    /**
     * 产品表
     */
    private function _addProductBase($lang='en',$cat_id0=0,$cat_id1=0,$cat_id2=0)
    {
        $map = array(
            '产品SKU/SKU' => 'product_sku',
            '产品名称/Product Title' => 'product_title',
            '申报价值(USD)/Declared Price' => 'product_declared_value',
            '申报名称/Declared Name' => 'product_declared_name',
            '含电池/Contain Battery' => 'contain_battery',
            
            '重量(KG)/Weight' => 'product_weight',
            '长(CM)/Length' => 'product_length',
            '宽(CM)/Width' => 'product_width',
            '高(CM)/Height' => 'product_height',
            '自定义编号/Customer Label' => 'reference_no'
        );
        $sheetName = '产品';
        $data = $this->_readUploadFile($sheetName); //
        $data = $this->_sheetFormat($data, $map, $sheetName);
        foreach($data as $v){
            try{
                foreach($v as $abc => $vvv){
                    $v[$abc] = trim($vvv);
                }
                if(empty($v['product_sku'])){
                    continue;
                }
                $product = array (
						'product_sku' => $v ['product_sku'],
						'reference_no' => $v ['reference_no'],
						'product_barcode' => Common_Company::getCompanyCode () . '-' . $v ['product_sku'],
						
						'company_code' => Common_Company::getCompanyCode (),
						'product_title_en' => $v ['product_title'],
						'product_title' => $v ['product_title'],
						
						'sale_status' => '',
						
						'product_weight' => $v ['product_weight'],
						'product_length' => $v ['product_length'],
						'product_width' => $v ['product_width'],
						'product_height' => $v ['product_height'],
						'pc_id' => '10',
						'contain_battery' => $v ['contain_battery'],
						'product_sales_value' => $v ['product_sales_value'],
						'product_purchase_value' => $v ['product_purchase_value'],
						'product_declared_value' => $v ['product_declared_value'],
						'product_declared_name' => $v ['product_declared_name'],
                        
						'cat_lang'=>$lang,
						'cat_id0' => $cat_id0,
						'cat_id1' => $cat_id1,
						'cat_id2' => $cat_id2 
				);
                $con = array('company_code'=>Common_Company::getCompanyCode(),'product_sku'=>$v['product_sku']);
				$row = Service_Product::getByCondition($con);
				$productId = '';
				if(!empty($row)){
					$row = $row[0];
					$productId = $row['product_id'];
				}
                $process = new Process_Product();
                $process->createSingle($product, $productId);
//                 $product_develop = $product = array();
                
//                 if($v['product_sku'] !== ''){
//                     $product['product_sku'] = $v['product_sku'];
//                     $product['product_barcode'] = $v['product_sku'];
//                 }
//                 if($v['product_title_en'] !== ''){
//                     $product['product_title_en'] = $v['product_title_en'];
//                 }
//                 if($v['product_title'] !== ''){
//                     $product['product_title'] = $v['product_title'];
//                 }
//                 if($v['product_sales_value'] !== ''){
//                     $product['product_sales_value'] = $v['product_sales_value'];
//                     if(empty($v['product_sales_value']) || ! preg_match('/^([0-9\.]+)$/', $v['product_sales_value'])){
//                         // 'SKU:'.$v['product_sku'].'‘销售价’不是数字'
//                         throw new Exception(Ec::Lang('sku_sale_not_num', $v['product_sku']));
//                     }
//                 }
//                 if($v['product_purchase_value'] !== ''){
//                     $product['product_purchase_value'] = $v['product_purchase_value'];
//                     if(empty($v['product_purchase_value']) || ! preg_match('/^([0-9\.]+)$/', $v['product_purchase_value'])){
//                         // 'SKU:'.$v['product_sku'].'‘采购价’不是数字'
//                         throw new Exception(Ec::Lang('sku_purchase_not_num', $v['product_sku']));
//                     }
//                 }
//                 if($v['product_declared_value'] !== ''){
//                     $product['product_declared_value'] = $v['product_declared_value'];
//                     if(empty($v['product_declared_value']) || ! preg_match('/^([0-9\.]+)$/', $v['product_declared_value'])){
//                         // 'SKU:'.$v['product_sku'].'‘申报价值’不是数字'
//                         throw new Exception(Ec::Lang('sku_declared_not_num', $v['product_sku']));
//                     }
//                 }
//                 if($v['pd_length'] !== ''){
//                     $product['product_length'] = $v['pd_length'];
//                     if(empty($v['pd_length']) || ! preg_match('/^([0-9\.]+)$/', $v['pd_length'])){
//                         // 'SKU:'.$v['product_sku'].'‘长’不是数字'
//                         throw new Exception(Ec::Lang('sku_length_not_num', $v['product_sku']));
//                     }
//                 }
                
//                 if($v['pd_width'] !== ''){
//                     $product['product_width'] = $v['pd_width'];
//                     if(empty($v['pd_width']) || ! preg_match('/^([0-9\.]+)$/', $v['pd_width'])){
//                         // 'SKU:'.$v['product_sku'].'‘宽’不是数字'
//                         throw new Exception(Ec::Lang('sku_width_not_num', $v['product_sku']));
//                     }
//                 }
//                 if($v['pd_height'] !== ''){
//                     $product['product_height'] = $v['pd_height'];
//                     if(empty($v['pd_height']) || ! preg_match('/^([0-9\.]+)$/', $v['pd_height'])){
//                         // 'SKU:'.$v['product_sku'].'‘高’不是数字'
//                         throw new Exception(Ec::Lang('sku_height_not_num', $v['product_sku']));
//                     }
//                 }
                
//                 if($v['pd_weight'] !== ''){
//                     $product['product_weight'] = $v['pd_weight'];
//                     if(empty($v['pd_weight']) || ! preg_match('/^([0-9\.]+)$/', $v['pd_weight'])){
//                         // 'SKU:'.$v['product_sku'].'‘重量’不是数字'
//                         throw new Exception(Ec::Lang('sku_weight_not_num', $v['product_sku']));
//                     }
//                 }
//                 if($v['product_category'] !== ''){
//                     $pc_id = 10;
//                     $category = Service_ProductCategory::getByField($v['product_category'], 'pc_shortname');
//                     if($category){
//                         $pc_id = $category['pc_id'];
//                     }
//                     $product['pc_id'] = $pc_id;
//                 }
                
//                 $con = array(
//                     'company_code' => Common_Company::getCompanyCode(),
//                     'product_sku' => $v['product_sku']
//                 );
//                 $exist = Service_Product::getByCondition($con);
                
//                 $log = '';
//                 if(empty($exist)){ // 新增
//                     $product['company_code'] = Common_Company::getCompanyCode();
//                     $product['product_barcode'] = $product['company_code'] . '-' . $product['product_sku'];
//                     $productId = Service_Product::add($product);
//                     $exist = Service_Product::getByField($productId, 'product_id');
//                     unset($product['product_sku']);
//                     $log .= '新增产品信息';
//                 }else{ // 修改
//                     $exist = $exist[0];
//                     unset($product['product_sku']);
//                     Service_Product::update($product, $exist['product_id'], 'product_id');
//                     $log .= '修改产品属性：';
//                     // 差异比较
//                     $diff = array_diff_assoc($product, $exist);
//                     foreach($diff as $kkk => $vvv){
//                         $log .= $kkk . ' from ' . $exist[$kkk] . ' to ' . $vvv . ';';
//                     }
//                 }
//                 // 日志
//                 $row = array(
//                     'pl_type' => '0',
//                     'user_id' => Service_User::getUserId(),
//                     'product_id' => $exist['product_id'],
//                     'pl_note' => $log,
//                     'pl_add_time' => date('Y-m-d H:i:s')
//                 );
//                 Service_ProductLog::add($row);
            }catch(Exception $e){
                $this->_errLog[] = '['.$v ['product_sku'].']'.$e->getMessage();
            }
        }
        return $data;
    }
    

    /**
     * 
     */
    public static function uploadAsnProduct($file)
    {
        $return = array(
                'ask' => 0,
                'message' => ''
        );
        $err = array();
        $db = Common_Common::getAdapter();
        try{
            if($file['error']){
                // '请选择xls文件'
                throw new Exception(Ec::Lang('pls_select_xls'));
            }
            $fileName = $file['name'];
            $filePath = $file['tmp_name'];
            
            $data = Common_UploadData::readUploadFile($fileName, $filePath,0);
            $map = array(
                '数量/Qty' => 'quantity',
                '箱号/BoxNo' => 'box_no',
                '包装/PackageType' => 'package_type',
            	'增值服务/ValueAdded' => 'value_added_type',
                'SKU' => 'product_sku'
            );
            $arr = array();
            foreach($data as $k=> $v){
                foreach($v as $kk=>$vv){
                    $arr[$k][$map[$kk]] = $vv;
                }
            }
            
            // 增值服务类型
            $valueAddedTypeArr = Common_DataCache::getValueAddedType();
            // 包装类型
            $packageTypeArr = Common_Type::packageType('auto');
            
            $products = array();
            foreach($arr as $k=>$v){
                if(empty($v['product_sku'])){
                    continue;
                }
                if(empty($v['quantity'])||empty($v['box_no'])||empty($v['package_type'])){
                    $err[] = "[{$v['product_sku']}]:数量/Qty|箱号/BoxNo|包装/PackageType Illegal";
                    continue;
                }
                if(!preg_match('/^[0-9]+$/', $v['quantity'])){
                    $err[] = "[{$v['product_sku']}]:数量/Qty Illegal";
                    continue;
                }
                if(!preg_match('/^[0-9]+$/', $v['box_no'])){
                    $err[] = "[{$v['product_sku']}]:箱号/BoxNo Illegal";
                    continue;
                }
                
                if(!preg_match('/\[([a-z]+)\].*/', $v['package_type'],$m)){
                    $err[] = "[{$v['product_sku']}]:包装/PackageType Illegal";
                    continue;
                }

                if(!$packageTypeArr[$m[1]]){
                	$err[] = "[{$v['product_sku']}]:包装/PackageType Illegal";
                	continue;
                }
                
                if(!empty($v['value_added_type']) && !preg_match('/\[(.+)\].*/', $v['value_added_type'],$vat)){
                	$err[] = "[{$v['product_sku']}]:增值服务/ValueAdded Illegal";
                	continue;
                }

                // 允许业务类型, 适用所有业务及入库单的额外服务
                $allowBusinessType = array(0, 1);
                $valueAddedType = $valueAddedTypeArr[$vat[1]];
//                 print_r($vat);die;
                if(!empty($vat[1]) && (empty($valueAddedType) || !in_array($valueAddedType['vat_business_type'], $allowBusinessType))) {
                	$err[] = "[{$v['product_sku']}]:增值服务/ValueAdded Illegal";
                	continue;
                }
                
                $v['package_type'] = $m[1];
                $v['value_added_type'] = $vat[1];
                $products[] = $v;
            }
            $asn_product = array();
            foreach($products as $p){
                $con = array(
                    'product_sku' => $p['product_sku'],
                    'company_code' => Common_Company::getCompanyCode()
                );
                $product = Service_Product::getByCondition($con);
                if(empty($product)){
                    $err[] = Ec::Lang('sku_not_exist', $p['product_sku']);
                    continue;
                }
                $product = $product[0];
                $asn_product[] = array(
                    'product_id' => $product['product_id'],
                    'product_barcode' => $product['product_barcode'],
                    'product_sku' => $product['product_sku'],
                    'product_title' => $product['product_title'],
                    'product_weight' => $product['product_weight'],
                    'rd_receiving_qty' => $p['quantity'],
                    'box_no' => $p['box_no'],
                    'package_type' => $p['package_type'],
                	'value_added_type' => $p['value_added_type'],
                    'line_weight' => $p['quantity']*$product['product_weight'],
                );
            }
            if(empty($err)){
                $return['ask'] = '1';
                // '所有数据处理完成'
                $return['message'] = Ec::Lang('all_data_done');
                $return['data'] = $asn_product;
            }else{
                
            }
            
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
            $err[] = $e->getMessage();
        }
        $return['err'] = $err;
        return $return;
    }
}