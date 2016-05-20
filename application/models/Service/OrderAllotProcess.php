<?php
class Service_OrderAllotProcess {
    /**
     * 订单自动分仓 全规则
     * @param unknown_type $allotAgain
     * @throws Exception
     * @return multitype:number multitype: NULL multitype:multitype:string unknown NULL   multitype:multitype:string unknown
     */
    public static function orderAllotTransaction($allotAgain=true,$refIds=array(),$companyCode=''){
        $return = array(
                'ask' => 0,
                'success_count' => 0,
                'fail_count' => 0,
                'result' => array()
        );
        $successArr = array();
        $failArr = array();
        try{
            $db = Common_Common::getAdapter();
            $sql = '
                select a.* from orders a
                where 1=1
                and a.order_status in (2,5)
                ';
            if($allotAgain){
                $sql .= ' and a.warehouse_id is null';
                $sql .= ' and a.shipping_method is null';
            }
            if($refIds){
                $refIds = implode("','", $refIds);
                $sql.=" and a.refrence_no_platform in ('".$refIds."')";
            }
            if($companyCode){
                $sql.=" and a.company_code ='{$companyCode}'";
            }
            $sql.=' limit 10000';
//             echo $sql;exit;
            // 待审核订单，冻结订单
            $orders = $db->fetchAll($sql);
    
            //             print_r($orders);exit;
            //开始分仓
            foreach($orders as $k => $order){
            //分仓规则
            $allotSet = self::_getAllotSet($order['company_code']);
            $result = self::orderAllot($order, $allotSet);
            if($result['ask']){//成功
            $successArr[] = array(
            'ask' => 1,
            'message' => $result['message'],
            'order_id' => $order['order_id'],
            'ref_id' => $order['refrence_no_platform']
                    );
            }else{//失败
                    $failArr[] = array(
                            'ask' => 0,
                            'message' => $result['message'],
            'order_id' => $order['order_id'],
                            'ref_id' => $order['refrence_no_platform']
                    );
            }
            }
    
                            $return['ask'] = 1;
            }catch(Exception $e){
                            $return['message'] = $e->getMessage();
            }
    
            $return['success_count'] = count($successArr);
            $return['fail_count'] = count($failArr);
            $return['success'] = $successArr;
            $return['fail'] = $failArr;
                    return $return;
    }
    /**
     * 统计SKU和数量,为分仓规则服务
     * @param mix $v
     * @return number
     */
    protected static function _orderAllotFormat($order){
        $con = array(
                'order_id' => $order['order_id'],
                'give_up' => '0'
        );
        $orderProducts = Service_OrderProduct::getByCondition($con);
        $orderSkuArr = array();
        $orderProductCount = 0;
        $order_weight = 0;
        foreach($orderProducts as $key => $val){
            $val['product_sku'] = empty($val['product_sku']) ? '--NoSku--' : $val['product_sku'];
            $orderSkuArr[] =  $val['product_sku'];
            $conn = array(
                    'product_sku' => $val['product_sku']
            );
            $rRows = Service_ProductCombineRelationProcess::getRelation($val['product_sku'],$order['user_account'],$order['company_code']);
            if($rRows){
                foreach($rRows as $r){
                    $orderSkuArr[] =  $r['pcr_product_sku'];
                    $orderProductCount+=$val['op_quantity']*$r['pcr_quantity'];
                    $prod = Service_Product::getByField($r['pcr_product_sku'],'product_sku');
                    $order_weight+=$prod['product_weight'];
                }
            }else{
                $orderProductCount+=$val['op_quantity'];
                $prod = Service_Product::getByField($val['product_sku'],'product_sku');
                $order_weight+=$prod['product_weight'];
            }
        }
    
        $orderOrg = Service_EbayOrderOriginal::getByField($order['refrence_no_platform'],'OrderID');
        $order['amountpaid'] = $order['amountpaid']?$order['amountpaid']:0;
        $order['ship_fee'] = $order['ship_fee']?$order['ship_fee']:0;
        if($orderOrg){
            $order['amountpaid'] = $orderOrg['amountpaid'];
            $order['ship_fee'] = $orderOrg['shippingservicecost'];
        }
    
        $order['order_sku_arr'] = $orderSkuArr;
        $order['order_product_count'] = $orderProductCount;
        $order['weight'] = $order_weight;
        //         print_r($order);exit;
        return $order;
    
    }
    /**
     * 获取所有有效分仓规则
     * @return Ambigous <mixed, multitype:, string>
     */
    protected static function _getAllotSet($company_code='NoCompanyCode'){
        // 所有分仓规则
        $con = array(
                'allot_action_type' => '1',
                'company_code'=>$company_code,
        );
        $allotSet = Service_OrderAllotSet::getByCondition($con, '*', 0, 0, 'allot_level desc'); // 优先级排序
        foreach($allotSet as $k=>$v){
    
            $con = array(
                    'order_allot_set_id' => $v['allot_set_id']
            );
            $allotSetCon = Service_OrderAllotSetCondition::getByCondition($con);
            $allotSet[$k]['set_con'] = $allotSetCon;
        }
    
        return $allotSet;
    }
    /**
     * 单个订单分仓
     * @param unknown_type $order
     * @param unknown_type $allotSet
     * @throws Exception
     * @return multitype:number string NULL
     */
    public static function orderAllot($order,$allotSet=null){
    
        if(empty($allotSet)){
            $allotSet = self::_getAllotSet($order['company_code']);
            if(empty($allotSet)){
                throw new Exception("没有设定分仓规则");
            }
        }
        $order = self::_orderAllotFormat($order);
        //         print_r($order);
        $allowStatus = array('2','5','7');
        if(!in_array($order['order_status'],$allowStatus)){
            throw new Exception('订单状态不允许分仓');
        }
        $result = array('ask'=>0,'message'=>'无匹配的分仓规则');
        foreach($allotSet as $v){ // 循环分仓规则
            //             print_r($v);
            $allot_action_value = unserialize($v['allot_action_value']);
            if(! is_array($allot_action_value)){
                throw new Exception('未设置仓库和仓库运输方式');
            }
            $warehouse_id = $allot_action_value['warehouse_id'];
            $shipping_method = $allot_action_value['shipping_method'];
    
            $allotSetCon = $v['set_con'];
    
            $conOk = true;
    
            foreach($allotSetCon as $sub){
                switch($sub['condition_type']){
                    case 'user_account':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        if(! in_array($order['user_account'], $val)){
                            $conOk = false;
                        }
                        break;
                    case 'order_site':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        if(! in_array($order['site'], $val)){
                            $conOk = false;
                        }
                        break;
                    case 'shipping_method_platform':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
    
                        if(! in_array($order['shipping_method_platform'], $val)){
                            $conOk = false;
                        }
                        break;
                    case 'consignee_country':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        if(! in_array($order['consignee_country'], $val)){
                            $conOk = false;
                        }
                        break;
                    case 'product_sku':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        if(! array_intersect($order['order_sku_arr'], $val)){
                            $conOk = false;
                        }
                        break;
                    case 'product_count':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        $param = array();
                        foreach($val as $sv){
                            $tt = explode(':', $sv);
                            $param[$tt[0]] = $tt[1];
                        }
                        if($param['from_val'] !== ''){
                            switch($param['from_type']){
                                case 'gt': // 大于
                                    if($order['order_product_count'] <= $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'ge': // 大于等于
                                    if($order['order_product_count'] < $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
    
                        if($param['to_val'] !== ''){
                            switch($param['to_type']){
                                case 'lt': // 小于
                                    if($order['order_product_count'] >= $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'le': // 小于等于
                                    if($order['order_product_count'] > $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
                        break;
                    case 'weight':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        $param = array();
                        foreach($val as $sv){
                            $tt = explode(':', $sv);
                            $param[$tt[0]] = $tt[1];
                        }
                        if($param['from_val'] !== ''){
                            switch($param['from_type']){
                                case 'gt': // 大于
                                    if($order['weight'] <= $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'ge': // 大于等于
                                    if($order['weight'] < $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
    
                        if($param['to_val'] !== ''){
                            switch($param['to_type']){
                                case 'lt': // 小于
                                    if($order['weight'] >= $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'le': // 小于等于
                                    if($order['weight'] > $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
                        break;
                    case 'ship_fee':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        $param = array();
                        foreach($val as $sv){
                            $tt = explode(':', $sv);
                            $param[$tt[0]] = $tt[1];
                        }
    
                        if($param['currency']!=$order['currency']){
                            $conOk = false;
                        }
                        if($param['from_val'] !== ''){
                            switch($param['from_type']){
                                case 'gt': // 大于
                                    if($order['ship_fee'] <= $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'ge': // 大于等于
                                    if($order['ship_fee'] < $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
    
                        if($param['to_val'] !== ''){
                            switch($param['to_type']){
                                case 'lt': // 小于
                                    if($order['ship_fee'] >= $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'le': // 小于等于
                                    if($order['ship_fee'] > $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
                        break;
    
                    case 'order_puchase':
                        $val = $sub['set_value'];
                        $val = explode(';', $val);
                        $param = array();
                        foreach($val as $sv){
                            $tt = explode(':', $sv);
                            $param[$tt[0]] = $tt[1];
                        }
                        if($param['currency']!=$order['currency']){
                            $conOk = false;
                        }
                        if($param['from_val'] !== ''){
                            switch($param['from_type']){
                                case 'gt': // 大于
                                    if($order['amountpaid'] <= $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'ge': // 大于等于
                                    if($order['amountpaid'] < $param['from_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
    
                        if($param['to_val'] !== ''){
                            switch($param['to_type']){
                                case 'lt': // 小于
                                    if($order['amountpaid'] >= $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                                case 'le': // 小于等于
                                    if($order['amountpaid'] > $param['to_val']){
                                        $conOk = false;
                                    }
                                    break;
                            }
                        }
                        break;
    
                    default:
                }
            }
    
            if($conOk){ //所有分仓规则都成立
                $db = Common_Common::getAdapter();
                try{
                    $updateRow = array(
                            'warehouse_id' => $warehouse_id,
                            'shipping_method' => $shipping_method
                    );
                     
                    switch(strtolower($order['platform'])){
                        case 'amazon':
                            $con = array('platform'=>'amazon','short_code'=>$shipping_method);
                            $shippingMethodPlatform = Service_ShippingMethodPlatform::getByCondition($con);
                            if(!$shippingMethodPlatform||empty($shippingMethodPlatform[0]['carrier'])){
                                throw new Exception('该运输方式'.$shipping_method.'未设定对应的承运商名称');
                            }
    
                            $shippingMethodPlatform = $shippingMethodPlatform[0];
                            $updateRow['shipping_method_platform'] = $shippingMethodPlatform['carrier'];
                            break;
                    }
                    /**/
                    Service_Orders::update($updateRow, $order['order_id'], 'order_id');
                    $logRow = array(
                            'ref_id' => $order['refrence_no_platform'],
                            'log_content' => '订单按照分仓规则自动分仓,仓库ID：' . $warehouse_id . '，运输方式：' . $shipping_method,
                            'data'=> '分仓规则:' . print_r($v, true),
                    );
                    Service_OrderLog::add($logRow);
                    $successArr[] = array(
                            'ask' => '1',
                            'message' => '分配仓库和运输方式成功',
                            'order_id' => $order['order_id'],
                            'ref_id' => $order['refrence_no_platform']
                    );
                    $result['ask'] = 1;
                    $result['message'] = '分仓成功';
                }catch(Exception $ee){
                    $result['message'] = $ee->getMessage();
                }
                break;//跳出循环
            }
            //             exit;
        } // 循环分仓规则 结束
    
        return $result;
    }
    
}