<?php
class Product_ProductCombineRelationController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/product-combine-relation/";
        $this->serviceClass = new Service_ProductAttribute();
    }

    public function listAction()
    {

        $user_account_arr_new = Service_User::getPlatformUserAll();
        $user_account_arr_new_tmp = array();
        foreach($user_account_arr_new as $v){
            $user_account_arr_new_tmp[$v['user_account']] = $v;
        }
        if($this->_request->isPost()){

            $db = Common_Common::getAdapter();
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);
            
            $page = $page ? $page : 1;
            $page = max(0,$page);
            $pageSize = $pageSize ? $pageSize : 20;
            
            $return = array(
                    "state" => 0,
                    "message" => "No Data"
            );
            $con = array();

            $productSku = $this->_request->getParam('product_sku', '');
            $con['product_sku_like'] = trim($productSku);
            $acc = $this->_request->getParam('user_account', '');
            $con['user_account'] = $acc;

            $pcr_product_sku = $this->_request->getParam('pcr_product_sku', '');
            $pcr_product_sku_query_type = $this->_request->getParam('sub_sku_query_type', '');
            if($pcr_product_sku_query_type == '1'){
	            $con['pcr_product_sku'] = trim($pcr_product_sku);
            }else if($pcr_product_sku_query_type == '2'){
            	$con['pcr_product_sku_like'] = trim($pcr_product_sku);
            }else{
            	$con['pcr_product_sku'] = trim($pcr_product_sku);
            }
            $pcr_percent_from = $this->_request->getParam('pcr_percent_from', '');
            $pcr_percent_to = $this->_request->getParam('pcr_percent_to', '');
            $pcr_quantity_from = $this->_request->getParam('pcr_quantity_from', '');
            $pcr_quantity_to = $this->_request->getParam('pcr_quantity_to', '');
            $con['pcr_percent_from'] = trim($pcr_percent_from);            
            $con['pcr_percent_to'] = trim($pcr_percent_to);            
            $con['pcr_quantity_from'] = trim($pcr_quantity_from);            
            $con['pcr_quantity_to'] = trim($pcr_quantity_to);
            
            $count = Service_ProductCombineRelation::getByCondition($con,'count(*)');
            
            $return['total'] = $count; 
            if ($count) {
                $data = Service_ProductCombineRelation::getByCondition($con,'*',$pageSize,$page,'product_sku'); 
                foreach($data as $k=>$v){
//                     $v['user_account'] = $v['user_account']&&$user_account_arr_new_tmp[$v['user_account']]?$user_account_arr_new_tmp[$v['user_account']]['platform_user_name']:'';
                    $data[$k] = $v;
                }
                $return['data'] = $data;
                $return['state'] = 1;
            }
            die(Zend_Json::encode($return));
            
        }

        $this->view->user_account_arr = $user_account_arr_new_tmp;
        
        echo Ec::renderTpl($this->tplDirectory . "list.tpl", 'layout');
    }

    public function upload($file){

        $return = array(
                'ask' => 0,
                'message' => 'Request Err'
        );
        
         
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(empty($fileName)||empty($filePath)){
                throw new Exception('请选择文件');
            }
            $pathinfo = pathinfo($fileName);
            if ( !isset($pathinfo["extension"]) || $pathinfo["extension"] != "xls") {
                throw new Exception('请上传excel文件');
            }
            $data_tmp = Service_ProductTemplate::readUploadFile($fileName, $filePath);
            if(! is_array($data_tmp)){
                throw new Exception($data_tmp);
            }
            foreach($data_tmp as $k => $v){
                foreach($v as $k1 => $v1){
                    $v1 = trim($v1);
                    $v1 = preg_replace('/\s+/', ' ', $v1);
                    $v1 = str_replace('&nbsp;', ' ', $v1);
//                     $v1 = str_replace('，', ',', $v1);
//                     $v1 = preg_replace('/[^a-zA-Z0-9\*\-_,；;%]/', '', $v1);
                    $v[$k1] = $v1;
                }
                $data_tmp[$k] = $v;
            }
            $data = array();
            foreach($data_tmp as $v){
                $product_sku = $v['产品编号'];
                $product_sku = Service_ProductCombineRelationProcess::skuStrProcess($product_sku);
                $userAcc = $v['平台账号'];
                $userAcc = trim($userAcc);
        
                if(!empty($userAcc)){
                    $u = Service_PlatformUser::getByField($userAcc,'platform_user_name');
                    if(!$u){
                        throw new Exception('平台账号不存在:'.$userAcc);
                    }
                    $userAcc = $u['user_account'];
                }
        
                $data[$product_sku.'##'.$userAcc] = $v;
                //日志
                $logRow = array(
                        'product_sku' => $product_sku,
                        'log_content' => $v['组合产品编码'],
                        'user_account' => empty($userAcc)?'':$userAcc,
                        'pcrl_add_time' => date('Y-m-d H:i:s'),
                        'user_id' => Service_User::getUserId()
                );
                Service_ProductCombineRelationLog::add($logRow);
            }
        
            $product_combine_relation_arr = array();
            $product_combine_relation_unique_arr = array();
        
            foreach($data as $v){
                $product_sku = $v['产品编号'];
                $product_sku = trim($product_sku);
                $userAcc = $v['平台账号'];
                $userAcc = trim($userAcc);
        
                if(!empty($userAcc)){
                    $u = Service_PlatformUser::getByField($userAcc,'platform_user_name');
                    if(!$u){
                        throw new Exception('平台账号不存在:'.$userAcc);
                    }
                    $userAcc = $u['user_account'];
                }
        
                $product_combine_relation_unique_arr[$product_sku.'##'.$userAcc] = $product_sku;
                $arr = explode(',', $v['组合产品编码']);
                $percent = 0;
                foreach($arr as $v1){
                    if(!preg_match('/([a-zA-Z0-9\-_]+)\*([0-9]+)\*([0-9\.]+)\%/', $v1, $m)){
                        throw new Exception('数据格式不正确'.$product_sku.'==>'.$v['组合产品编码']);
                    }
                    $percent+=($m[3])*$m[2];
                    //                         echo ($m[3])*$m[2]."\n";
                    $relation = array(
                            'product_sku' => $product_sku,
                            'pcr_product_sku' => Service_ProductCombineRelationProcess::skuStrProcess($m[1]),
                            'pcr_quantity' => $m[2],
                            'pcr_percent' => $m[3],
                            'user_account' => empty($userAcc)?'':$userAcc,
                            'pcr_add_time' => date('Y-m-d H:i:s')
                    );

                    if($relation['product_sku']==$relation['pcr_product_sku']){
                        throw new Exception('Excel 数据不合法,对应关系不可以对应本身,'.$relation['product_sku']);
                    }
                    $product_combine_relation_arr[] = $relation;
                }
                //                     echo $percent;exit;
                if($percent!=100){
                    throw new Exception('比例不正确'.$product_sku.'==>'.$v['组合产品编码']);
                }
            }
            //                 print_r($product_combine_relation_unique_arr);exit;
            foreach($product_combine_relation_unique_arr as $k=> $v){ // 删除旧关系
        
                $arrr = explode('##', $k);
                $sql = "select * from product_combine_relation where product_sku='{$arrr[0]}' and user_account='{$arrr[1]}'";
                $rs = $db->fetchAll($sql);
                foreach($rs as $r){
                    Service_ProductCombineRelation::delete($r['pcr_id'], 'pcr_id');
                } /**/
                //                     Service_ProductCombineRelation::delete($v, 'product_sku');
            }
            //                 exit;
            foreach($product_combine_relation_arr as $v){ // 添加新关系
                Service_ProductCombineRelation::add($v);
            }
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '所有数据上传成功';
            $return['data'] = Zend_Json::encode($product_combine_relation_arr);
        }catch(Exception $e){
            $db->rollback();
        
            $return['ask'] = 0;
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function uploadNew($file){
    
        $return = array(
                'ask' => 0,
                'message' => 'Request Err'
        );
    
        $err = array();
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(empty($fileName)||empty($filePath)){
                throw new Exception('请选择文件');
            }
            $pathinfo = pathinfo($fileName);
            if ( !isset($pathinfo["extension"]) || $pathinfo["extension"] != "xls") {
                throw new Exception('请上传excel文件');
            }
            $tmp_name = APPLICATION_PATH.'/../data/session/'.Service_User::getUserId().'-product_combine_relation-'.date('Y-m-d_H-i-s').'.'.$pathinfo["extension"];

            @file_put_contents($tmp_name, file_get_contents($filePath));
            
            $data_tmp = Service_ProductTemplate::readUploadFile($fileName, $filePath);
            if(! is_array($data_tmp)){
                throw new Exception($data_tmp);
            }
            foreach($data_tmp as $k => $v){
                foreach($v as $k1 => $v1){
                    $v1 = trim($v1);
                    $v1 = str_replace('&nbsp;', ' ', $v1);
                    $v1 = preg_replace('/\s+/', ' ', $v1);
//                     $v1 = str_replace('，', ',', $v1);
//                     $v1 = preg_replace('/[^a-zA-Z0-9\*\-_,；;%\.]/', '', $v1);
                    $v[$k1] = $v1;
                }
                $data_tmp[$k] = $v;
            }
            $company_code = Common_Company::getCompanyCode();
            $data = array();
            foreach($data_tmp as $v){
                $product_sku = $v['产品编号']?$v['产品编号']:$v['平台销售SKU'];
                $product_sku = Service_ProductCombineRelationProcess::skuStrProcess($product_sku);
                $pcr_product_sku = $v['组合产品编码']?$v['组合产品编码']:$v['对应产品SKU'];
                $pcr_product_sku = Service_ProductCombineRelationProcess::skuStrProcess($pcr_product_sku);
                $pcr_quantity = $v['组合产品数量']?$v['组合产品数量']:$v['对应产品数量'];
                $pcr_pu_price = $v['组合产品单价']?$v['组合产品单价']:$v['对应产品采购单价'];
                $pcr_pu_price = $pcr_pu_price===''?1:$pcr_pu_price;
                $userAcc = $v['平台账号']?$v['平台账号']:$v['平台及账号'];
                $userAcc = trim($userAcc);
                if(empty($product_sku)){
                    continue;
                }
                if(empty($pcr_product_sku)){
                    continue;
                }
                
                if(!empty($userAcc)){
                    $u = Service_PlatformUser::getByField($userAcc,'user_account');
                    if(!$u){
                        throw new Exception('平台账号不存在:'.$userAcc);
                    }
                    $userAcc = $u['user_account'];
                }
                $lineKey = $product_sku.'##'.$userAcc;
                $data[$lineKey]['product_sku'] =$product_sku;
                $data[$lineKey]['user_account'] =empty($userAcc)?'':$userAcc;
                $data[$lineKey]['total'] =0;
                $data[$lineKey]['sub_sku'][] = array(
                    'pcr_product_sku' => $pcr_product_sku,
                    'pcr_quantity' => intval($pcr_quantity),
                    'pcr_pu_price' => $pcr_pu_price?floatval($pcr_pu_price):1,
                );   
                
            }
            
            foreach($data as $k=>$v){
                $total = 0;
                foreach($v['sub_sku'] as $kk=>$vv){
                    $con = array(
                            'invoice_code' => $vv['pcr_product_sku'],
                            'company_code' => Common_Company::getCompanyCode()
                    );
                    $invoiceInfo = Service_CsdInvoiceInfo::getByCondition($con);
                    if(empty($invoiceInfo)){
                        throw new Exception('品名代码不存在');
                    }
                    $invoiceInfo = array_pop($invoiceInfo);
                    
                    if(empty($vv['pcr_quantity'])||!preg_match('/^[0-9]+$/', $vv['pcr_quantity'])||$vv['pcr_quantity']==='0'){
                        $err[] = "[{$v['product_sku']}]".$vv['pcr_product_sku'].'数量必须大于0的数字';
                    }
                    if(empty($vv['pcr_pu_price'])||!is_numeric($vv['pcr_pu_price'])||floatval($vv['pcr_pu_price'])==0){
//                         $err[] = "[{$v['product_sku']}]".$vv['pcr_product_sku'].'采购价不能为空且需为大于零的数字';
                    }
                    $total+=$vv['pcr_quantity']*$vv['pcr_pu_price'];
                }
//                 echo $vv['pcr_pu_price'];exit;
                $data[$k]['total'] = $total;
            }
//             print_r($data);exit;
            if(!empty($err)){//判断是否数据异常
                throw new Exception('Excel 数据不合法');
            }
            foreach($data as $k=>$v){
                foreach($v['sub_sku'] as $kk=>$vv){
                    $percent = empty($v['total'])?0:($vv['pcr_pu_price']/$v['total']);
                    $v['sub_sku'][$kk]['pcr_percent'] = round($percent*100,3);
                }
                $data[$k] = $v;
            }

//             print_r($data);exit;
            foreach($data as $k=>$v){//删除旧数据              
                $sql = "select * from product_combine_relation where product_sku='{$v['product_sku']}' and user_account='{$v['user_account']}' and company_code='{$company_code}'";
                $rs = $db->fetchAll($sql);
                foreach($rs as $r){
                    Service_ProductCombineRelation::delete($r['pcr_id'], 'pcr_id');
                }
            }
            $relationArr = array();
            foreach($data as $k=>$v){
                $log_content = array();
                foreach($v['sub_sku'] as $kk=>$vv){                    
                    $keys = array_keys($vv);
                    $vals = array_values($vv);
                    $keys = implode('*', $keys);
                    $vals = implode('*', $vals);
                    $log_content[] = $keys.'='.$vals;

                    $relation = array(
                            'company_code'=>$company_code,
                            'product_sku' => strtoupper($v['product_sku']),
                            'pcr_product_sku' => strtoupper($vv['pcr_product_sku']),
                            'pcr_quantity' => $vv['pcr_quantity'],
                            'pcr_percent' => $vv['pcr_percent'],
                            'pcr_pu_price' =>$vv['pcr_pu_price'],
                            'user_account' =>$v['user_account'],
                            'pcr_add_time' => date('Y-m-d H:i:s')
                    );
                    if($relation['product_sku']==$relation['pcr_product_sku']){
//                         throw new Exception('Excel 数据不合法,对应关系不可以对应本身,'.$relation['product_sku']);
                    }
                    $relationArr[] = $relation;                    
                }
//                 print_r($log_content);exit;
                //日志
                $logRow = array(
                        'product_sku' => $v['product_sku'],
                        'log_content' =>  implode(' ---- ', $log_content),
                        'user_account' => $v['user_account'],
                        'pcrl_add_time' => date('Y-m-d H:i:s'),
                        'user_id' => Service_User::getUserId()
                );
                Service_ProductCombineRelationLog::add($logRow);
            }
//             print_r($relationArr);exit;
            foreach($relationArr as $relation){
                try{
                    Service_ProductCombineRelation::add($relation);
                }catch(Exception $ee){
                    throw new Exception(print_r($relation,true));
                } 
            }
            
            
            
//             print_r($data);exit;
            
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '所有数据上传成功';
            $return['data'] = Zend_Json::encode($data);
        }catch(Exception $e){
            $db->rollback();
    
            $return['ask'] = 0;
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $err;
        return $return;
    }
    public function uploadAction(){

        if($this->_request->isPost()){
            set_time_limit(0);
            $file = $_FILES['fileToUpload'];
            $return = $this->uploadNew($file);
            die(Zend_Json::encode($return));
        }
        echo Ec::renderTpl($this->tplDirectory . "upload.tpl", 'layout');
    }
    
    /**
     * 单独添加对应关系
     */
    public function addAction(){
    	$return = array(
    			'ask'=>0,
    			'message'=>'',
    			'error_message'=>array()
    			);
    	$company_code = Common_Company::getCompanyCode();
    	if($this->_request->isPost()){
	    	$product_sku = $this->_request->getParam('product_sku','');
	    	$user_account = $this->_request->getParam('user_account','');
	    	$sub_sku = $this->_request->getParam('pcr_product_sku',array());
	    	$sub_qty = $this->_request->getParam('pcr_quantity',array());
	    	$sub_pu_price = $this->_request->getParam('pcr_pu_price',array());
	    	//去除前后空格
	    	$product_sku = trim($product_sku);
    		$sub_qty = Common_ApiProcess::nullToEmptyString($sub_qty);
    		$sub_sku = Common_ApiProcess::nullToEmptyString($sub_sku);
    		$sub_pu_price = Common_ApiProcess::nullToEmptyString($sub_pu_price);
	    	
	    	$err = array();
	    	if(empty($product_sku)){
	    		$err[] = Ec::Lang('Platform_Sales_SKU','auto') . ' 不能为空.';
	    	}else{
	    		//大写
	    		$product_sku = Service_ProductCombineRelationProcess::skuStrProcess($product_sku);
	    	}
	    	
	    	if(empty($sub_sku) || empty($sub_qty) || empty($sub_pu_price)){
	    		$err[] = "请填写对应的SKU信息.";
	    	}
	    	
	    	$sub_sku_arr = array();
	    	$total = 0;
	    	$index = 1;
	    	foreach ($sub_sku as $key => $value) {
	    		$pcr_sku = Service_ProductCombineRelationProcess::skuStrProcess($sub_sku[$key]);
	    		$v = array(
	    				'product_sku'=>$product_sku,
	    				'pcr_product_sku'=>$pcr_sku,
	    				'pcr_quantity'=>$sub_qty[$key],
	    				'pcr_pu_price'=>$sub_pu_price[$key],
	    				'pcr_percent'=>'',
	    				'user_account'=>$user_account,
	    				'total'=>$sub_qty[$key] * $sub_pu_price[$key],
	    				'pcr_add_time' => date('Y-m-d H:i:s')
	    				);
	    		$sub_sku_arr[] = $v;
	    		
	    		$empty_sku = false;
	    		if(empty($pcr_sku)){
	    			$empty_sku = true;
	    			$err[] = "第 " .  $index . " 行，请填写对应SKU.";
	    		}else if($pcr_sku == $product_sku){
// 	    			$err[] = (($empty_sku)?"第 " .  $index . " 行，":"SKU：" . $pcr_sku) . " 对应SKU和平台销售SKU不能一样.";
	    		}
	    		if(empty($sub_qty[$key])){
	    			$err[] = (($empty_sku)?"第 " .  $index . " 行，":"SKU：" . $pcr_sku) . ' 数量必须大于0.';
	    		}
	    		if(empty($sub_pu_price[$key])){
// 	    			$err[] = (($empty_sku)?"第 " .  $index . " 行，":"SKU：" . $pcr_sku) . ' 采购价不能为空.';
	    		}
	    		
	    		$total +=  $sub_qty[$key] * $sub_pu_price[$key];
	    		$index++;
	    	}
// 	    	print_r($sub_sku_arr);
	    	if(!empty($err)){
	    		$return['error_message'] = $err;
	    		die(Zend_Json::encode($return));
	    	}
	    	
	    	$log_content = array();
	    	foreach ($sub_sku_arr as $key_2 => $value_2) {
	    		$log_content[] = "pcr_product_sku*pcr_quantity*pcr_pu_price*pcr_percent=" . 
	    							$value_2['pcr_product_sku'] . "*" . 
	    							$value_2['pcr_quantity'] . "*" . 
	    							$value_2['pcr_pu_price'] . "*" . 
	    							$value_2['pcr_percent'];
	    		
	    		$value_2['pcr_percent'] = round(($value_2['pcr_pu_price'] / $total)*100,3);
	    		unset($value_2['total']);
	    		$sub_sku_arr[$key_2] = $value_2;
	    	}
	    	//删除旧数据
	    	$db = Common_Common::getAdapter();
	    	$sql = "delete from product_combine_relation where product_sku='{$product_sku}' and user_account='{$user_account}' and company_code='{$company_code}'";
	    	$db->query($sql);
// 	    	print_r($sub_sku_arr);
// 	    	exit;
	    	foreach ($sub_sku_arr as $value) {
	    		try{
	    		    $value = Common_ApiProcess::nullToEmptyString($value);
	    		    $value['company_code'] = $company_code;
	    			Service_ProductCombineRelation::add($value);
	    		}catch(Exception $ee){
	    			throw new Exception(print_r($value,true));
	    		}
	    	}
	    	
	    	 //日志
            $logRow = array(
                    'product_sku' => $product_sku,
                    'log_content' =>  implode(',', $log_content),
                    'user_account' => $user_account,
                    'pcrl_add_time' => date('Y-m-d H:i:s'),
                    'user_id' => Service_User::getUserId()
            );
            Service_ProductCombineRelationLog::add($logRow);
	    	
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('Platform_Sales_SKU','auto') . "： " . $product_sku . " 对应关系，添加成功.";
    	}
    	die(Zend_Json::encode($return));
    }
    
    /**
     * 导出所有数据
     */
    public function exportAction(){
        set_time_limit(0);
        $sql = 'select * from product_combine_relation group by product_sku;';
        $db = Common_Common::getAdapter();
        $data = $db->fetchAll($sql);
        $dataList = array();
        foreach($data as $v){
            $sql = "select * from product_combine_relation where product_sku='".$v['product_sku']."'";
            $res = $db->fetchAll($sql);
            $sub = array();
            foreach($res as $vv){
                $sub[] = $vv['pcr_product_sku'].'*'.$vv['pcr_quantity'].'*'.$vv['pcr_percent'].'%';
            }
            $dataList[] = array('产品编号'=>$v['product_sku'],'组合产品编码'=>implode(',', $sub),'平台账号'=>$v['user_account']); 
        }

        $fileName = Service_ExcelExport::exportToFile($dataList, '产品关系导出');
        Common_Common::downloadFile($fileName);
    }
    
    public function updateAction(){

        $productSku = $this->_request->getParam('product_sku', '');
        $acc = $this->_request->getParam('user_account', '');

        $pcr_product_sku = $this->getParam('pcr_product_sku',array());
        $pcr_quantity = $this->getParam('pcr_quantity',array());
        $pcr_pu_price = $this->getParam('pcr_pu_price',array());
        $relationRow = array();
        foreach($pcr_product_sku as $k=>$v){
            $relationRow[$k]['product_sku'] = strtoupper($productSku);
            $relationRow[$k]['user_account'] = $acc;
            $relationRow[$k]['pcr_product_sku'] = strtoupper($v);
        }
        foreach($pcr_quantity as $k=>$v){
            $relationRow[$k]['pcr_quantity'] = $v;
        }
        
        foreach($pcr_pu_price as $k=>$v){
            $relationRow[$k]['pcr_pu_price'] = $v;
        }
        $result = array('ask'=>0,'message'=>'Fail');
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(empty($productSku)){
                throw new Exception('主SKU必填');
            }
            if(empty($relationRow)){
                throw new Exception('缺少子产品');
            }
//             print_r($relationRow);exit;
            $totalPrice = 0;
            foreach($relationRow as $k=>$v){
                if(empty($v['pcr_product_sku'])){
                    throw new Exception('子SKU不能为空');
                }
                if($productSku==$v['pcr_product_sku']){
//                     throw new Exception('Excel 数据不合法,对应关系不可以对应本身,'.$productSku);
                }
                if(empty($v['pcr_quantity'])||!preg_match('/^[0-9]$/',$v['pcr_quantity'])){
                    throw new Exception('数量必须大于0的整数');
                }
                if(empty($v['pcr_pu_price'])){
                    throw new Exception('采购价必须大于0');
                }
                $totalPrice+=$v['pcr_pu_price']*$v['pcr_quantity'];
            }
            foreach($relationRow as $k=>$v){
                $v['pcr_percent'] = round($v['pcr_pu_price']/$totalPrice*100,3);
                $relationRow[$k] = $v;
            }
            
//             print_r($relationRow);exit;
            
            $rows = Service_ProductCombineRelationProcess::getRelation($productSku, $acc);
            foreach($rows as $v){
                Service_ProductCombineRelation::delete($v['pcr_id'], 'pcr_id');
            }
            $log_content = array();
            foreach($relationRow as $v){
                $v['pcr_add_time'] = date('Y-m-d H:i:s');
                Service_ProductCombineRelation::add($v);
                $log_content[] = implode('*', array_keys($v)).'='.implode('*', array_values($v));
            }
            //日志
            $logRow = array(
                    'product_sku' => $productSku,
                    'log_content' =>  implode(',', $log_content),
                    'user_account' => $acc,
                    'pcrl_add_time' => date('Y-m-d H:i:s'),
                    'user_id' => Service_User::getUserId()
            );
            Service_ProductCombineRelationLog::add($logRow);
            $result['ask'] = 1;

            $result['message'] = 'Success';
            
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $result['message'] = $e->getMessage();
        }
        
        die(Zend_Json::encode($result));
        
    }
    
    /**
     * 明细
     */
    public function getDetailAction(){
        $result = array(
            'ask' => 0,
            'message' => 'No Data'
        );
        $productSku = $this->_request->getParam('sku', '');
        $acc = $this->_request->getParam('acc', '');
        $type = $this->_request->getParam('type', 'json');
        $rows = Service_ProductCombineRelationProcess::getRelation($productSku, $acc);
        if($rows){
            $result['ask'] = 1;
            $result['data'] = $rows;
        }
        // print_r($rows);exit;
        if($type=='json'){
            die(Zend_Json::encode($result));
            
        }else{
            $this->view->product_sku = $productSku;
            $this->view->acc = $acc;
            $this->view->ask = $result['ask'];
            $this->view->data = $rows;
            echo $this->view->render($this->tplDirectory . "edit.tpl");
//             echo Ec::renderTpl($this->tplDirectory . "edit.tpl", 'layout');
        }
    }
    /**
     * 删除
     */
    public function deleteAction(){
        $result = array(
            'ask' => 0,
            'message' => 'No Data'
        );
        $productSku = $this->_request->getParam('sku', '');
        $acc = $this->_request->getParam('acc', '');
        $rows = Service_ProductCombineRelationProcess::getRelation($productSku, $acc);
//         print_r($rows);exit;
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(empty($rows)){
                throw new Exception('组合产品关系不存在');
            }
            $log_content = array();
            foreach($rows as $v){
                Service_ProductCombineRelation::delete($v['pcr_id'], 'pcr_id');
                $log_content[] = implode('*', array_keys($v)).'='.implode('*', array_values($v));
            }
            // 日志
            $logRow = array(
                'product_sku' => $productSku,
                'log_content' => '删除产品组合关系,删除前对应关系为：'.implode(' ---- ', $log_content),
                'user_account' => $acc,
                'pcrl_add_time' => date('Y-m-d H:i:s'),
                'user_id' => Service_User::getUserId()
            );
            Service_ProductCombineRelationLog::add($logRow);
            $result['ask'] = 1;
            $result['message'] = 'Operation Success';
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $result['message'] = $e->getMessage();
        }
        die(Zend_Json::encode($result));
    }
    
    /**
     * 获取公司平台账号
     */
    public function getPlatformUserAction(){
    	$con = array('company_code'=>Common_Company::getCompanyCode());
    	$field = array('user_account','short_name','platform_user_name')  ;
    	$data = Service_PlatformUser::getByCondition($con,$field);
    	die(Zend_Json::encode($data));
    }
}