<?php
class Service_OrderProductProcess
{
    public static function getOrderProductActive($refId){
    
    }

    /**
     * 订单编辑
     * @param unknown_type $refId
     */
    public static function getOrderProductActiveForEdit($refId){
        $con = array(
            'OrderID' => $refId,
            'give_up_arr' => array(
                '0',
                '1'
            ),
        );
        $order = Service_Orders::getByField ( $refId, 'refrence_no_platform' );
    
        $orderProducts = Service_OrderProduct::getByCondition ( $con, '*', 0, 1 );
        foreach($orderProducts as $k=>$p){
            //设置默认值
            $p['op_ref_item_id'] = empty($p['op_ref_item_id'])?'0':$p['op_ref_item_id'];
            $p['op_ref_tnx'] = empty($p['op_ref_tnx'])?'0':$p['op_ref_tnx'];
            $p['op_recv_account'] = empty($p['op_recv_account'])?'':$p['op_recv_account'];
            $p['buyer_id'] = empty($p['buyer_id'])?'':$p['buyer_id'];
            
            $p['unit_finalvaluefee'] = empty($p['unit_finalvaluefee'])?0:$p['unit_finalvaluefee'];
            $p['unit_price'] = empty($p['unit_price'])?0:$p['unit_price'];
            $p['create_type'] = empty($p['create_type'])?'api':$p['create_type'];
//             ksort($p);
            $orderProducts[$k] = $p;
        }
        //         print_r($orderProducts);
        $combArr = array();
        $combOrgArr = array();//未拆分的原始产品
        $total_product_count = 0;//订单总产品数
        foreach ( $orderProducts as $k => $p ) {//获取仓库sku
            
            $conn = array(
                    'product_sku' => $p['product_sku']
            );
            $combRows = Service_ProductCombineRelationProcess::getRelation($p['product_sku'],$order['user_account'],$order['company_code']);
           
            if($combRows){
                $combOrgArr[$p['op_id']] = $p;
                unset($orderProducts[$k]);//取消已经处理过的产品
                foreach($combRows as $row){ // 组合产品           
                    $total_product_count+=$p['op_quantity'] * $row['pcr_quantity']; 
                    
                    $sub_unit_finalvaluefee = $p['unit_finalvaluefee']*$row['pcr_percent']/100;//预先设置的比例
                    $sub_unit_price = $p['unit_price']*$row['pcr_percent']/100;//预先设置的比例
        
                    if(isset($combArr[$row['pcr_product_sku']])){
                        $combArr[$row['pcr_product_sku']]['op_quantity'] += $p['op_quantity'] * $row['pcr_quantity'];
                        $combArr[$row['pcr_product_sku']]['unit_finalvaluefee'] += ($sub_unit_finalvaluefee)*$p['op_quantity'] * $row['pcr_quantity'];
                        $combArr[$row['pcr_product_sku']]['unit_price'] += ($sub_unit_price)*$p['op_quantity'] * $row['pcr_quantity'];
                    }else{
                        $combArr[$row['pcr_product_sku']] = array(
                                'ebay_sku' => $p['product_sku'],
                                'product_type' => 'mult',
                                
                                'create_type' => $p['create_type'],
                                'give_up' => $p['give_up'],
                                'product_sku' => $row['pcr_product_sku'],
                                'product_title' => $p['product_title'],
                                'op_quantity' => $p['op_quantity'] * $row['pcr_quantity'],
                                'op_ref_tnx' => $p['op_ref_tnx'],
                                'op_ref_item_id' => $p['op_ref_item_id'],
                                'op_ref_buyer_id' => $order['buyer_id'],
                                'op_ref_paydate' => $p['op_ref_paydate'],
        
                                'unit_finalvaluefee'=>($sub_unit_finalvaluefee)*$p['op_quantity'] * $row['pcr_quantity'],//成交费用
                                'unit_price'=>($sub_unit_price)*$p['op_quantity'] * $row['pcr_quantity'], //销售价格
                                'currency_code'=>$p['currency_code'], //币种
        
                                'op_recv_account'=>$p['op_recv_account'],
                                'op_site'=>$p['op_site'],
                                'OrderID'=>$p['OrderID'],
                                'OrderIDEbay'=>$p['OrderIDEbay'],
                                'pic'=>$p['pic'],
                                'url'=>$p['url'],
        
                        );
                    }
                }
            }
        }

        foreach($combArr as $k=>$v){//均价
            $unit_platformfee = $order['finalvaluefee']&&$order['finalvaluefee']>0?($order['platform_fee']*$v['unit_finalvaluefee']/($order['finalvaluefee']*$v['op_quantity'])):0;
            $v['unit_platformfee'] = round($unit_platformfee,3);//单个产品paypal费
            $v['unit_finalvaluefee'] = round($v['unit_finalvaluefee']/$v['op_quantity'],3);//单个产品成交费
            $v['unit_price'] = round($v['unit_price']/$v['op_quantity'],3);//单个产品价格
            $combArr[$k] = $v;
        }       
        
        $notCombArr = array();
        foreach ( $orderProducts as $k => $p ) { // 获取仓库sku   
            $total_product_count+=$p['op_quantity'];  
            $unit_platformfee = $order['finalvaluefee']&&$order['finalvaluefee']>0?($order['platform_fee']*$p['unit_finalvaluefee']/$order['finalvaluefee']):0;//单个产品paypal费
            
            $notCombArr[$p['op_id']] = array(
                'op_id' => $p['op_id'],
                'ebay_sku' => $p['product_sku'],
                'product_type' => 'single',
                    
                'create_type' => $p['create_type'],
                'give_up' => $p['give_up'],
                'product_sku' => $p['product_sku'],
                'product_title' => $p['product_title'],
                'op_quantity' => $p['op_quantity'],
                'op_ref_tnx' => $p['op_ref_tnx'],
                'op_ref_item_id' => $p['op_ref_item_id'],
                'op_ref_buyer_id' => $order['buyer_id'],
                'op_ref_paydate' => $p['op_ref_paydate'],
                'unit_platformfee' => round($unit_platformfee,3),
                
                'unit_finalvaluefee' => ($p['unit_finalvaluefee']), // 成交费用
                'unit_price' => ($p['unit_price']) , // 销售价格
                'currency_code' => $p['currency_code'], // 币种
                
                'op_recv_account' => $p['op_recv_account'],
                'op_site' => $p['op_site'],
                'OrderID' => $p['OrderID'],
                'OrderIDEbay' => $p['OrderIDEbay'],
                'pic' => $p['pic'],
                'url' => $p['url']
            );

        }

        $productArr = $combArr+$notCombArr;
//         print_r($combArr);
//         print_r($notCombArr);
//         sort($productArr);
//         print_r($productArr);
//         exit;
        
        $result = array(
            'mult' => $combArr,//组合产品拆分后的子产品
            'single' => $notCombArr,//非组合产品
                
            'combOrgArr'=>$combOrgArr,//组合产品原始记录
            'productArr' => $productArr,//所有产品
        );        
        //         print_r($productArr);exit;
        return $result;
         
    }
    /**
     * 拆单
     * @param unknown_type $refId
     */
    public static function getOrderProductActiveForSplit($refId,$give_up = '0'){
        $con = array (
                'OrderID' => $refId,
                'give_up'=>$give_up,
        );
        $order = Service_Orders::getByField ( $refId, 'refrence_no_platform' );
    
        $orderProducts = Service_OrderProduct::getByCondition ( $con, '*', 0, 1 );
        foreach($orderProducts as $k=>$v){
            ksort($v);
            $orderProducts[$k] = $v;
        }
//         print_r($orderProducts);
        $productArr = array();
        foreach ( $orderProducts as $k => $p ) {//获取仓库sku
            //设置默认值
            $p['op_ref_item_id'] = empty($p['op_ref_item_id'])?'0':$p['op_ref_item_id'];
            $p['op_ref_tnx'] = empty($p['op_ref_tnx'])?'0':$p['op_ref_tnx'];
            $p['op_recv_account'] = empty($p['op_recv_account'])?'':$p['op_recv_account'];
            $p['buyer_id'] = empty($p['buyer_id'])?'':$p['buyer_id'];
            $p['create_type'] = empty($p['create_type'])?'api':$p['create_type'];
            
            $p['unit_finalvaluefee'] = empty($p['unit_finalvaluefee'])?0:$p['unit_finalvaluefee'];
            $p['unit_price'] = empty($p['unit_price'])?0:$p['unit_price'];

            $conn = array(
                    'product_sku' => $p['product_sku']
            );
            $combRows = Service_ProductCombineRelationProcess::getRelation($p['product_sku'],$order['user_account'],$order['company_code']);
            if($combRows){
                foreach($combRows as $row){ // 组合产品

                    $sub_unit_finalvaluefee = $p['unit_finalvaluefee']*$row['pcr_percent']/100;//预先设置的比例
                    $sub_unit_price = $p['unit_price']*$row['pcr_percent']/100;//预先设置的比例
                    
                    if(isset($productArr[$row['pcr_product_sku']])){
                        $productArr[$row['pcr_product_sku']]['op_quantity'] += $p['op_quantity'] * $row['pcr_quantity'];
                        $productArr[$row['pcr_product_sku']]['unit_finalvaluefee'] += ($sub_unit_finalvaluefee)*$p['op_quantity'] * $row['pcr_quantity'];
                        $productArr[$row['pcr_product_sku']]['unit_price'] += ($sub_unit_price)*$p['op_quantity'] * $row['pcr_quantity'];
                    }else{
                        $productArr[$row['pcr_product_sku']] = array(
                            'ebay_sku' => $p['product_sku'],
                            'product_sku' => $row['pcr_product_sku'],
                                
                            'give_up' => $p['give_up'],                                
                            'create_type' => $p['create_type'],
                            
                            'product_title' => $p['product_title'],
                            'op_quantity' => $p['op_quantity'] * $row['pcr_quantity'],
                            'op_ref_tnx' => $p['op_ref_tnx'],
                            'op_ref_item_id' => $p['op_ref_item_id'],
                            'op_ref_buyer_id' => $order['buyer_id'],
                            'op_ref_paydate' => $p['op_ref_paydate'],
                            

                            'unit_finalvaluefee'=>($sub_unit_finalvaluefee)*$p['op_quantity'] * $row['pcr_quantity'],//成交费用
                            'unit_price'=>($sub_unit_price)*$p['op_quantity'] * $row['pcr_quantity'], //销售价格
                            'currency_code'=>$p['currency_code'], //币种
                            
                            
                            'op_recv_account'=>$p['op_recv_account'],
                            'op_site'=>$p['op_site'],
                            'OrderID'=>$p['OrderID'],
                            'OrderIDEbay'=>$p['OrderIDEbay'],
                            'pic'=>$p['pic'],
                            'url'=>$p['url'],
                                
                        );
                    }
                }
            }else{
                $pExist = Service_ProductCombineRelation::getByField($p['product_sku'],'pcr_product_sku');
    
                if(isset($productArr[$p['product_sku']])){
                    $productArr[$p['product_sku']]['op_quantity'] += $p['op_quantity'];
                    $productArr[$p['product_sku']]['unit_finalvaluefee'] += ($p['unit_finalvaluefee'])*$p['op_quantity'];
                    $productArr[$p['product_sku']]['unit_price'] += ($p['unit_price'])*$p['op_quantity'];
                }else{
                    $productArr[$p['product_sku']] = array(
                        'ebay_sku' => $p['product_sku'],
                        'product_sku' => $p['product_sku'],
                        'create_type' => $p['create_type'],
                        'give_up' => $p['give_up'],  
                        
                        'product_title' => $p['product_title'],
                        'op_quantity' => $p['op_quantity'],
                        'op_ref_tnx' => $p['op_ref_tnx'],
                        'op_ref_item_id' => $p['op_ref_item_id'],
                        'op_ref_buyer_id' => $order['buyer_id'],
                        'op_ref_paydate' => $p['op_ref_paydate'],
                        
                        'unit_finalvaluefee'=>($p['unit_finalvaluefee'])*$p['op_quantity'],//成交费用
                        'unit_price'=>($p['unit_price'])*$p['op_quantity'], //销售价格
                        'currency_code'=>$p['currency_code'], //币种

                        
                        'op_recv_account'=>$p['op_recv_account'],
                        'op_site'=>$p['op_site'],
                        'OrderID'=>$p['OrderID'],
                        'OrderIDEbay'=>$p['OrderIDEbay'],
                        'pic'=>$p['pic'],
                        'url'=>$p['url'],
                    );
                }
            }
        }
        $total_subtotal = 0;
        $total_finalvaluefee = 0;
        $total_product_count = 0;//订单总产品数
        foreach($productArr as $k=>$v){//均价
            $total_product_count+=$v['op_quantity'];
            $total_subtotal+=$v['unit_price'];
            $total_finalvaluefee+=$v['unit_finalvaluefee'];

            $unit_platformfee = $order['finalvaluefee']&&$order['finalvaluefee']>0?($order['platform_fee']*($v['unit_finalvaluefee']/$v['op_quantity'])/($order['finalvaluefee'])):0;//单个产品paypal费
            
            $v['unit_platformfee'] = round($unit_platformfee,3);//单个产品paypal费
        
            $v['unit_finalvaluefee'] = round($v['unit_finalvaluefee']/$v['op_quantity'],3);//单个产品成交费
            $v['unit_price'] = round($v['unit_price']/$v['op_quantity'],3);//单个产品价格
            $productArr[$k] = $v;
        }
        return $productArr;
    }
    
    /**
     * 更新订单行费用
     * @param unknown_type $refId
     * @throws Exception
     * @return boolean
     */
    public static function updateOrderProductUnitPriceFinalValueFee($refId){
        $order = Service_Orders::getByField ( $refId, 'refrence_no_platform' );
        if(empty($order)){
            throw new Exception('订单不存在');
        }
        $amountpaid = $order['amountpaid']?$order['amountpaid']:0;//总费用
        if($order['subtotal']==0&&$order['amountpaid']>0){
            $order['subtotal'] = $order['amountpaid'];
        }
        if($order['platform']=='ebay'&&$order['is_merge']=='0'){
            $org = Service_EbayOrderOriginal::getByField($refId,'OrderID');
            if($org){
                $order['platform_fee'] = $org['feeorcreditamount'];
                $order['ship_fee'] = $org['shippingservicecost'];
            }
        }
        $subtotal = $order['subtotal']?$order['subtotal']:0;//交易额
        $shipfee = $order['ship_fee']?$order['ship_fee']:0;//运费
        $platformfee = $order['platform_fee']?$order['platform_fee']:0;//平台费用
        $finalvaluefee = $order['finalvaluefee']?$order['finalvaluefee']:0;//交易费用
        
        $con = array(
            'OrderID' => $refId,
            'give_up_arr' => array(
                '0',
                '1'
            ),
        );
        
        $orderProducts = Service_OrderProduct::getByCondition ( $con, '*');  
        $total_subtotal = 0;
        $total_finalvaluefee = 0;
        $total_platformfee = 0;
        
        $total_product_count = 0;//订单总产品数        
        foreach($orderProducts as $k=>$v){//均价
            $total_product_count+=$v['op_quantity'];
            $total_subtotal+=$v['unit_price']*$v['op_quantity'];
            $total_finalvaluefee+=$v['unit_finalvaluefee']*$v['op_quantity']; 
            $total_platformfee+=$v['unit_platformfee']*$v['op_quantity']; 
        }  
        
        foreach($orderProducts as $k=>$v){
            if($total_subtotal>0){
                $v['unit_price'] = (($v['unit_price']*$v['op_quantity']/$total_subtotal)*$subtotal)/$v['op_quantity'];  
                $v['unit_shipfee'] = (($v['unit_price']*$v['op_quantity']/$total_subtotal)*$shipfee)/$v['op_quantity'];        
            }else{
                $v['unit_price'] = $subtotal/$total_product_count;    
                $v['unit_shipfee'] = $shipfee/$total_product_count;           
            }

            if($total_finalvaluefee>0){
                $v['unit_finalvaluefee'] = (($v['unit_finalvaluefee']*$v['op_quantity']/$total_finalvaluefee)*$finalvaluefee)/$v['op_quantity'];
            }else{
                $v['unit_finalvaluefee'] = $finalvaluefee/$total_product_count;
            }

            if($total_platformfee>0){
                $v['unit_platformfee'] = (($v['unit_platformfee']*$v['op_quantity']/$total_platformfee)*$platformfee)/$v['op_quantity'];
            }else{
                $v['unit_platformfee'] = $platformfee/$total_product_count;
            }
            
            $updateRow = array(
                    'unit_price' => $v['unit_price'],
                    'unit_finalvaluefee' => $v['unit_finalvaluefee'],
                    'unit_platformfee' => $v['unit_platformfee'],
                    'unit_shipfee' => $v['unit_shipfee'],
            );
            Service_OrderProduct::update($updateRow, $v['op_id'], 'op_id');
            $orderProducts[$k] = $v;
        }        
        return $orderProducts;
    }
}