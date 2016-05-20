<?php
class Order_ProductRuleController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "order/views/product_rule/";
        $this->serviceClass = new Service_PbrProductrule();
    }

    /**
     * 产品对应的国家
     */
    public function arriveZoneTypeAction()
    {
        $return = array(
                'ask' => 0,
                'message' => 'Fail.'
        );
        try{
            $product_code = $this->getParam('product_code', '');
            $serve_kind_arr = array();
            $sql = "select * from pbr_productrule where product_code='{$product_code}' and arrive_zone_type='Y';";
            $db = Common_Common::getAdapter();
            $rule = $db->fetchRow($sql);
            $zoneArr = array();
            if($rule){
                // 满足该条件，表示所有国家
                $sql = "select * from pbr_arrival_area where rule_id='{$rule['rule_id']}'";
                $serve = $db->fetchRow($sql);
                if(! $serve){
                    // 满足该条件，表示指定国家
                    $sql = "select distinct country_code from pbr_arrival_area where rule_id='{$rule['rule_id']}';";
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
    
        echo Zend_Json::encode($return);
    }

    /**
     * 产品对应的国家
     */
    public function getCountryAction()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $product_code = $this->getParam('product_code', '');
            $countrys = Process_ProductRule::arrivalCountry($product_code);
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $countrys;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
    }

    /**
     * 产品对应的国家
     */
    public function getProductAction()
    {
        $return = array(
                'ask' => 0,
                'message' => 'Fail.'
        );
        try{
            $country_code = $this->getParam('country_code', '');
            $countrys = Process_ProductRule::arrivalCountry($country_code);
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $countrys;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
    
        echo Zend_Json::encode($return);
    }
    /**
     * 获取产品与国家对应的必填项
     */
    public function webRequiredAction()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $product_code = $this->getParam('product_code', '');
            $country_code = $this->getParam('country_code', ''); 
            $web_element = Process_ProductRule::webRequired($product_code,$country_code);
            
            // 产品，目的国家，地址，申报信息必须包含--->$web_element
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('必填项加载完成');
            $return['data'] = $web_element;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
    }

    /**
     * 获取附加服务
     */
    public function optionalServeTypeAction()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $product_code = $this->getParam('product_code', '');
            $country_code = $this->getParam('country_code', '');
            $order_id = $this->getParam('order_id', '');
            $serve_kind_arr = Process_ProductRule::optionalServeType($product_code, $country_code);
            $serve_kind_code_arr = array_keys($serve_kind_arr);
            
            if($order_id){
                $con = array(
                    'order_id' => $order_id
                );
                $extservice = Service_CsdExtraservice::getByCondition($con);
                foreach($extservice as $v){
                    $extra_servicecode = strtoupper($v['extra_servicecode']);
                    if(in_array($extra_servicecode, $serve_kind_code_arr)){
                        $serve_kind_arr[$extra_servicecode]['checked'] = 1;
                    }                    
                }
            }
            
            // 按级别分组额外服务
            $serve_kind_arr_by_group = array();
            foreach($serve_kind_arr as $row) {
            	$extra_service_group = empty($row['extra_service_group']) ? $row['extra_service_kind'] : $row['extra_service_group'];
            	$serve_kind_arr_by_group[$extra_service_group][] = $row;
            }
            
            $extraServiceKindGroup = Process_ProductRule::getExtraServiceGroup();
//             print_r($serve_kind_arr);
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $serve_kind_arr_by_group;
            $return['group'] = $extraServiceKindGroup;
        }catch(Exception $e){
            
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
    }

    public function allAction(){

        $return = array(
                'ask' => 0,
                'message' => 'Fail.'
        );
        try{
            $product_code = $this->getParam('product_code', '');
            $country_code = $this->getParam('country_code', '');
            $order_id = $this->getParam('order_id', '');
            $serve_kind_arr = Process_ProductRule::optionalServeType($product_code, $country_code);
            $serve_kind_code_arr = array_keys($serve_kind_arr);
        
            if($order_id){
                $con = array(
                        'order_id' => $order_id
                );
                $extservice = Service_CsdExtraservice::getByCondition($con);
                foreach($extservice as $v){
                    $extra_servicecode = strtoupper($v['extra_servicecode']);
                    if(in_array($extra_servicecode, $serve_kind_code_arr)){
                        $serve_kind_arr[$extra_servicecode]['checked'] = 1;
                    }
                }
            }
            $return['serve_kind_arr'] = $serve_kind_arr;

            $web_element = Process_ProductRule::webRequired($product_code,$country_code);
            
            $return['web_element'] = $web_element;
            
            
            
            //             print_r($serve_kind_arr);
            $return['ask'] = 1;
            $return['message'] = 'Success';
            
            
        }catch(Exception $e){        
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
        
    }
    /**
     * 验证ODA
     */
    public function productOdaTypeAction()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        try{
            $product_code = $this->getParam('product_code', '');
            $serve_kind_arr = array();
            $sql = "select * from pbr_productrule where product_code='{$product_code}';";
            $db = Common_Common::getAdapter();
            $rule = $db->fetchRow($sql);
            if($rule && ! empty($rule['product_oda_type'])){ // 有ODA
            }
            $return['ask'] = 1;
            $return['message'] = 'Success';
            $return['data'] = $serve_kind_arr;
        }catch(Exception $e){
            
            $return['message'] = $e->getMessage();
        }
        
        echo Zend_Json::encode($return);
    }
}