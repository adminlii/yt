<?php
class Process_ProductRule
{

    /**
     * 产品对应的国家
     */
    public static function arriveCountryType($product_code = '')
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $serve_kind_arr = array();
            $sql = "select * from pbr_productrule where product_code='{$product_code}' and arrive_zone_type='Y';";
            $db = Common_Common::getAdapterForDb2();
            $rule = $db->fetchRow($sql);
            $zoneArr = array();
            if($rule){
                // 满足该条件，表示所有国家
                $sql = "select * from pbr_arrival_zone where rule_id='{$rule['rule_id']}'  and (country_code='' or country_code is null);";
                $serve = $db->fetchRow($sql);
                if(! $serve){
                    // 满足该条件，表示指定国家
                    $sql = "select distinct country_code from pbr_arrival_zone where rule_id='{$rule['rule_id']}';";
                    $data = $db->fetchRow($sql);
                    foreach($data as $v){
                        $zoneArr[] = $v['country_code'];
                    }
                }
            }
            $zoneArr = array_unique($zoneArr);
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $zoneArr;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }
    
    public static function arrivalCountry($product_code){
        $sql = "select * from pbr_productrule where product_code='{$product_code}' and arrive_zone_type='Y';";
        $db = Common_Common::getAdapterForDb2();
        $rule = $db->fetchRow($sql);
        $serve = array();
        if($rule){
            // 满足该条件，表示所有国家
            $sql = "select * from pbr_arrival_area where rule_id='{$rule['rule_id']}'";
            $serve = $db->fetchAll($sql);
        }
        
        $con = array();
        if($serve){
            $country_code_arr = array();
            foreach($serve as $v){
                $country_code_arr[] = $v['ct_code'];
            }
            $country_code_arr = array_unique($country_code_arr);
            $con['country_code_arr'] = $country_code_arr;
        }
        
        $countrys = Service_IddCountry::getByCondition($con);
        $countryArr = array();
        foreach($countrys as $k=>$v){
            $countryArr[$v['country_code']] = $v;
        }
        return $countryArr;
    }

    public static function getProductByCountryCode($country_code){
        
    }
    /**
     * 获取产品与国家对应的必填项
     * csi_productkind
     * pbr_productrule
     * pbr_country_required
     * bd_webelement
     */
    public static function webRequired($product_code = '', $country_code = '')
    {
        $web_elements = array();
        try{
            // 产品，目的国家，地址，申报信息必须包含--->$web_elements
            $web_elements[] = 'product_code';
            
            $web_elements[] = 'country_code';
            // 申报信息
            $web_elements[] = 'invoice_wrap';
            // 发件人
            $web_elements[] = 'submiter_wrap';
            
            // 收件人
            $web_elements[] = 'consignee_name';
            // 收件人地址
            $web_elements[] = 'consignee_street';
            
            $web_elements[] = 'invoice_enname';
            
            $web_elements[] = 'invoice_quantity';
            
            $web_elements[] = 'invoice_unitcharge';
            
            //附加服务
//             $web_elements[] = 'product_extraservice_wrap';
            
//             $web_elements[] = 'hs_code';

            
            $sql = "select * from pbr_productrule where product_code='{$product_code}' and web_required='Y';";
            $db = Common_Common::getAdapter();
            $rule = $db->fetchRow($sql);
            if($rule){
                // 有国家，取国家，没国家，去除国家选项
                $sql = "select * from pbr_country_required where rule_id='{$rule['rule_id']}' and country_code='{$country_code}';";
                $requireds = $db->fetchAll($sql);
                if(! $requireds){
                    $sql = "select * from pbr_country_required where rule_id='{$rule['rule_id']}' and (country_code='' or country_code is null);";
                    $requireds = $db->fetchAll($sql);
                }
                foreach($requireds as $required){
                    $sql = "select * from bd_webelement where web_element_id='{$required['web_element_id']}';";
                    $element = $db->fetchRow($sql);
                    if($element){
                        $web_elements[] = $element['web_element_value'];
                    }
                }
            }            
            $web_elements = array_unique($web_elements);
        }catch(Exception $e){
            Ec::showError($e->getMessage(), __CLASS__ . '_' . __METHOD__ . '_err');
        }
        
        return $web_elements;
    }

    /**
     * 获取产品与国家对应的必填项
     * csi_productkind
     * pbr_productrule
     * pbr_country_required
     * bd_webelement
     */
    public static function webRequiredObj($product_code = '', $country_code = '')
    {
        $web_elements = array();
        try{
            $sql = "select * from pbr_productrule where product_code='{$product_code}' and web_required='Y';";
            $db = Common_Common::getAdapterForDb2();
            $rule = $db->fetchRow($sql);
            if($rule){
                // 有国家，取国家，没国家，去除国家选项
                $sql = "select * from pbr_country_required where rule_id='{$rule['rule_id']}' and country_code='{$country_code}';";
                $requireds = $db->fetchAll($sql);
                if( !$requireds){
                    $sql = "select * from pbr_country_required where rule_id='{$rule['rule_id']}' and (country_code='' or country_code is null);";
                    $requireds = $db->fetchAll($sql);
                }
                foreach($requireds as $required){
                    $sql = "select * from bd_webelement where web_element_id='{$required['web_element_id']}';";
                    $element = $db->fetchRow($sql);
                    if($element){
                        $web_elements[$element['web_element_value']] = $element;
                    }
                }
            }
//             $web_elements = array_unique($web_elements);
        }catch(Exception $e){
            Ec::showError($e->getMessage(), __CLASS__ . '_' . __METHOD__ . '_err');
        }
    
        return $web_elements;
    }
    
    /**
     * 获取附加服务
     */
    public static function optionalServeType($product_code = '', $country_code = '')
    {
        $serve_kind_arr = array();
        $sql = "select * from pbr_productrule where product_code='{$product_code}' and optional_serve_type='Y';";
        $db = Common_Common::getAdapterForDb2();
        $rule = $db->fetchRow($sql);
        if($rule){
            // 有国家，取国家，没国家，去除国家选项
            $sql = "select * from pbr_optional_serve where rule_id='{$rule['rule_id']}' and country_code='{$country_code}';";
            $serves = $db->fetchAll($sql);
            if(! $serves){
                $sql = "select * from pbr_optional_serve where rule_id='{$rule['rule_id']}' and (country_code='' or country_code is null);";
                $serves = $db->fetchAll($sql);
            }
            if($serves){
            	
            	// 获取所有指定客户的额外服务
            	$sql = "select extra_service_kind_id, customer_id from csi_customer_extra_service;";
            	$customer_extra = $db->fetchAll($sql);
            	
            	// 按额外服务代码分组
            	$customer_extra_arr = array();
            	if(!empty($customer_extra)) {
					foreach ($customer_extra as $row) {
						$customer_extra_arr[$row['extra_service_kind_id']][]  = $row['customer_id'];
					}
            	}
            	
                foreach($serves as $serve){
                    $v = $serve['serve_code'];
                    $sql = "select * from atd_extraservice_kind where extra_service_kind ='{$v}' and extra_service_webvisible='Y';";
                    
                    $serve_kind = $db->fetchRow($sql);
                    if($serve_kind){
                    	
                    	// 额外服务暂时只支持按组绑定客户使用，故取到组代码
                    	$extra_service_group = empty($serve_kind['extra_service_group']) ? $serve_kind['extra_service_kind'] : $serve_kind['extra_service_group'];
                    	// 当存在客户限制并且是当前服务，而且不等于当前客户时，不添加到客户可选服务里面
                    	if(!empty($customer_extra_arr) 
                    			&& isset($customer_extra_arr[$extra_service_group]) 
                    			&& !in_array(Service_User::getCustomerId(), $customer_extra_arr[$extra_service_group])) {
                    		continue;
                    	}
                    	
                        $serve_kind['checked'] = 0;
                        $serve_kind_arr[strtoupper($v)] = $serve_kind;
                    }
                }
            }
            
        }

//      print_r($serve_kind_arr);exit;
        return $serve_kind_arr;
    }

    /**
     * 验证ODA
     */
    public static function productOdaType($product_code = '')
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $serve_kind_arr = array();
            $sql = "select * from pbr_productrule where product_code='{$product_code}';";
            $db = Common_Common::getAdapterForDb2();
            $rule = $db->fetchRow($sql);
            if($rule && ! empty($rule['product_oda_type'])){ // 有ODA
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $serve_kind_arr;
        }catch(Exception $e){
            
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }

    /**
     * 运输方式
     * 
     * @return Ambigous <mixed, multitype:, string>
     */
    public static function getProductKind()
    {
        $con = array(
            'product_status' => 'Y',
            'tms_id' => Service_User::getTmsId()
        );
        $productKind = Service_CsiProductkind::getByCondition($con,'*',0,0,'product_cnname asc');
        foreach($productKind as $k => $v){
            $rule = Service_PbrProductrule::getByField($v['product_code'], 'product_code');
            if(! $rule || $rule['web_show_type'] != 'Y'){
                unset($productKind[$k]);
            }
        }
        return $productKind;
    }
    
    /**
     * 获取附加服务组
     */
    public static function getExtraServiceGroup()
    {
    	$serve_kind_arr = array();
    	$sql = "SELECT * FROM atd_extraservice_kind aek WHERE aek.extra_service_kind IN 
					(SELECT extra_service_group FROM atd_extraservice_kind)";
    	$db = Common_Common::getAdapterForDb2();
    	$kind_rows = $db->fetchAll($sql);
    	if($kind_rows) {
    		foreach($kind_rows as $k => $row) {
    			$serve_kind_arr[strtoupper($row['extra_service_kind'])] = $row;
    		}
    	}
    	
    	return $serve_kind_arr;
    }
}