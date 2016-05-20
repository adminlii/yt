<?php
class Service_SupplyStrategyProcess
{
    /*
     * 产品的平均销量
     */
    public static function warehouseProductSale($companyCode='')
    {
        $db = Common_Common::getAdapter();
        
        $sql = 'SELECT DISTINCT a.product_id,b.warehouse_id FROM `order_product` a INNER JOIN orders b on a.order_id=b.order_id where b.order_status>=3;';
        
        $orderProducts = $db->fetchAll($sql); // 统计所有有销量车产品
        
        foreach($orderProducts as $p){
            $product = Service_Product::getByField($p['product_id'],'product_id');
            if($product['product_status']!=1){//不是正式产品，略过
                continue;
            }
            $con = array(
                'product_id' => $p['product_id'],
                'warehouse_id' => $p['warehouse_id']
            );
            
            $exist = Service_WarehouseProductSales::getByCondition($con);
            if(empty($exist)){
                $row = array(
                    'product_id' => $p['product_id'],
                    'warehouse_id' => $p['warehouse_id']
                );
                Service_WarehouseProductSales::add($row);
            }
        }
        
        $saleProduct = Service_WarehouseProductSales::getAll();
        foreach($saleProduct as $sp){ // 统计销量
            $sqlBase = "SELECT round((case when SUM(b.op_quantity) is null then 0 else SUM(b.op_quantity)  end )/DAYPLACE,3) FROM `orders` a ";
            $sqlBase .= " INNER JOIN order_product b ";
            $sqlBase .= " on a.order_id=b.order_id ";
            $sqlBase .= " where 1=1";
            $sqlBase .= " and a.order_status>=3 ";//已经操作的订单
            $sqlBase .= " and b.product_id=" . $sp['product_id'] . " ";
            $sqlBase .= " and a.warehouse_id = " . $sp['warehouse_id'] . " ";
            $sqlBase .= " and unix_timestamp (a.add_time)>unix_timestamp(DATE_add(NOW(),INTERVAL -24*DAYPLACE HOUR ) );";
//             echo $sqlBase;exit;
            // Ec::showError($sqlBase,'--------------------------');exit;
            
            $day = 3;
            $qty_day3 = $db->fetchOne(str_replace('DAYPLACE', $day, $sqlBase));
            
            $day = 7;
            $qty_day7 = $db->fetchOne(str_replace('DAYPLACE', $day, $sqlBase));
            
            $day = 14;
            $qty_day14 = $db->fetchOne(str_replace('DAYPLACE', $day, $sqlBase));
            
            $day = 30;
            $qty_day30 = $db->fetchOne(str_replace('DAYPLACE', $day, $sqlBase));
            
            $trendArr = self::getTrend($qty_day3, $qty_day7, $qty_day14, $qty_day30);
            $sales_type = $trendArr['sales_type'];
            $qty_sales = $trendArr['qty_sales'];
            
            $updateRow = array(
                'qty_day3' => $qty_day3,
                'qty_day7' => $qty_day7,
                'qty_day14' => $qty_day14,
                'qty_day30' => $qty_day30,
                'qty_sales' => $qty_sales,
                'sales_type' => $sales_type,
            );
            Service_WarehouseProductSales::update($updateRow, $sp['wps_id'], 'wps_id');
        }
    }
    /**
     * 趋势
     * @param unknown_type $qty_day3
     * @param unknown_type $qty_day7
     * @param unknown_type $qty_day14
     * @param unknown_type $qty_day30
     * @return multitype:number unknown
     */
    public static function getTrend($qty_day3,$qty_day7,$qty_day14,$qty_day30){
        if($qty_day3 > $qty_day7 && $qty_day7 > $qty_day14 && $qty_day14 > $qty_day30){ // 持续上升
            $sales_type = 1;
            $qty_sales = $qty_day3; // 平均销量
        }elseif(($qty_day3 + $qty_day7) >= ($qty_day14 + $qty_day30)){ // 波动上升
            $sales_type = 2;
            $qty_sales = $qty_day7; // 平均销量
        }elseif($qty_day3 < $qty_day7 && $qty_day7 < $qty_day14 && $qty_day14 < $qty_day30){ // 持续下降
            $sales_type = 3;
            $qty_sales = $qty_day14<=1?0:$qty_day14; // 平均销量
        }elseif(($qty_day3 + $qty_day7) < ($qty_day14 + $qty_day30)){ // 波动下降
            $sales_type = 4;
            $qty_sales = $qty_day30<=1?0:$qty_day30; // 平均销量
        }else{
            $sales_type = 0;
            $qty_sales = $qty_day30<=1?0:$qty_day30; // 平均销量
        }
        return array(
            'sales_type' => $sales_type,
            'qty_sales' => $qty_sales
        );
    }
    /*
     * 自动采购策略 计划生成逻辑说明： 
     * 1，手工运行生成计划
     * 2，取product_supply_strategy（产品供应策略表）数据，根据（备货天数*销量+补货周期*销量）计算数量。
     * 3，可用库存+在途库存-待出库存-缺货库存-计算的数量 4，如果第3点得出的是负数，则得出计划采购数量
     * 5，根据计划数量-(purchase_inventory)表中所有数量，生成计划表数据（purchase_product_plan）
     * 6，生成计划表数量后，需要修改purchase_inventory表，SKU的已计划数量
     */
    public static function productSupplyStrategy($product_id_arr = array(),$createWarehouseProductSale=true)
    {
        if($createWarehouseProductSale){
            self::warehouseProductSale();//产品平均销量            
        }
        $con = array('product_id_arr'=>$product_id_arr);
        $productSales = Service_WarehouseProductSales::getByCondition($con);
        $db = Common_Common::getAdapter();

        $saleTypeArr = array(
                '1' => '3天平均销量',
                '2' => '7天平均销量',
                '3' => '14天平均销量',
                '4' => '30天平均销量'
        );

        $trendypeArr = array(
                '1' => '持续上升',
                '2' => '波动上升',
                '3' => '波动下降',
                '4' => '持续下降'
        );
        foreach($productSales as $ps){
            $sqlLog = '';
            $db->beginTransaction();
            $product_id = $ps['product_id'];
            $warehouse_id = $ps['warehouse_id'];
            $plan_type="1";
            try{                
                $product = Service_Product::getByField($product_id, 'product_id');
               
                if(! $product){
                    throw new Exception('数据异常，产品数据不存在-->product_id:' . $product_id);
                } 
                if($product['product_status']!=1){//产品为下架或者还未完成开发，不生成采购建议
                    throw new Exception('产品状态异常-->product_id:' . $product_id);
                }
                $saleCountArr = array(
                        '1' => $ps['qty_day3'],
                        '2' => $ps['qty_day7'],
                        '3' => $ps['qty_day14'],
                        '4' => $ps['qty_day30']
                );
                $planNote = 'SKU:'.$product['product_sku']."(ProductID:".$product_id.",仓库ID:".$warehouse_id."),销量：3天平均（{$ps['qty_day3']}），7天平均（{$ps['qty_day7']}），14天平均（{$ps['qty_day14']}），30天平均（{$ps['qty_day30']}）\n";

                $planNote.="当前产品处于".$trendypeArr[$ps['sales_type']]."，取".$saleTypeArr[$ps['sales_type']]."（".$saleCountArr[$ps['sales_type']]."）作为补货计划基数\n";
                $sql = 'select sum(pss_value) sum  from product_supply_strategy
                    where product_id=' . $ps['product_id'] . '
                    and warehouse_id=' . $ps['warehouse_id'] . '
                    and sales_type=' . $ps['sales_type']; // 按照仓库,产品分组，获取采购天数
                $sqlLog.=$sql.";\n";
                $days = $db->fetchOne($sql);
                
                if(is_null($days)){ // 未设置产品的采购策略，则以仓库采购策略为依据
                    $planNote.="当前产品未设置采购策略，以仓库".$saleTypeArr[$ps['sales_type']]."作为运算公式\n";
                    
                    $sql = 'select  sum(wss_value) sum from warehouse_supply_strategy 
                            where 1=1 
                            and warehouse_id=' . $ps['warehouse_id'] . '
                            and sales_type=' . $ps['sales_type']; // 按照仓库,产品分组，获取采购天数
                    $sqlLog.=$sql.";\n";
                    $days = $db->fetchOne($sql);
                    $planNote.='当前仓库(仓库Id：'.$warehouse_id.")的采购策略计算天数为".$days."天\n";
                    if(is_null($days)){
                        throw new Exception('SKU:' . $product['product_sku'] . '没有设置产品采购策略，同时也没有设置仓库ID：' . $warehouse_id . '的 采购策略');
                    }
                }else{
                    $planNote.="产品采购策略计算天数为".$days."天\n";
                }
                $count = $ps['qty_sales'] * $days; // 采购数量
                $planNote.="当前产品应备货数量为".$count.'个 ('.$days." * ".$ps['qty_sales'].")\n"; 
                                                   
                // 缺货数量，需要统计
                $sql = 'select case when sum(b.op_quantity) is not null then sum(b.op_quantity) else 0 end sum from orders a ';
                $sql .= ' inner join order_product b on a.order_id=b.order_id ';
                $sql .= ' where 1=1 ';
                $sql .= ' and a.order_status=3 ';//状态为3，表示缺货
                $sql .= ' and a.warehouse_id=' . $warehouse_id;
                $sql .= ' and b.product_id=' . $product_id;
                $sqlLog.=$sql.";\n";
                $quehuoSum = $db->fetchOne($sql); // 缺货数量

                //如果有缺货的数量，则采购计划为缺货类型
                if(!empty($quehuoSum)){
                    if($quehuoSum>0){
                        $plan_type="3";
                    }
                }
                $planNote.="当前产品缺货数量为".$quehuoSum."个\n";
                
                $count += $quehuoSum; // 总共需要采购数+缺货数
                $planNote.="当前产品应备货数量+缺货数量=待采购总数".$count."个\n";
                
                if($count <= 0){ // 无需采购
                    throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                }
                $con = array(
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id
                );
                $inventoryRow = Service_ProductInventory::getByCondition($con);
                if($inventoryRow){ // 已有库存
                    $inventoryRow = $inventoryRow[0];
                    // 已有库存-待采购数
                    $invCount = $inventoryRow['pi_sellable'] + $inventoryRow['pi_onway'] + $inventoryRow['pi_pending']+ $inventoryRow['pi_planned'];
                    $poCount =  $invCount - $count;
                    $planNote .= "当前产品已有库存数量为".$invCount."个(实际库存:" . $inventoryRow['pi_sellable'] . "+在途库存:" . $inventoryRow['pi_onway'] . "+待上架库存:" . $inventoryRow['pi_pending']. "+中转库存:" . $inventoryRow['pi_planned'].")"  ;
                    $planNote .= "\n";
                    
                }else{
                    $poCount = 0 - $count;
                    $planNote.="当前产品已有库存数量为0个\n";
                }

                $planNote.="当前产品待采购数量".abs($poCount)."个\n";
                
                if($poCount >= 0){ // 无需采购
                    throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                }
                
                // 计划采购数，由负数转为正数，取绝对值
                $poCount = abs($poCount);
                $con = array(
                    'pi_product_id' => $product_id,
                    'warehouse_id' => $warehouse_id
                );
                // 采购库存
                $poInventory = Service_PurchaseInventory::getByCondition($con);
                if($poInventory){
                    $poInventory = $poInventory[0];
                    $poInvCount = ($poInventory['qty_plan'] + $poInventory['qty_create'] + $poInventory['qty_release']);
                    $poCount = $poCount - $poInvCount;
                    $planNote.="已有采购库存".$poInvCount."个(计划数量:".$poInventory['qty_plan'] ."+正在创建数量:".$poInventory['qty_create']  ."+已经创建数量:".  $poInventory['qty_release'].')';
                    $planNote.="\n";
                }else{
                    $poInvCount = 0;
                    $planNote.="已有采购库存".(0)."个\n";
                }
                $planNote.="经过运算，当前产品建议采购数量为".$poCount."个(待采购数量-已有采购库存),";
                
                if($poCount <= 0){ // 无需采购
                    throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                }
                $poCount = ceil($poCount);//向上取整                
                $planNote.="建议采购数量取整数为".$poCount."个\n";
                if($poInventory){
                    $updateRow = array( // 更新计划采购数
                        'qty_plan' => $poInventory['qty_plan'] + $poCount
                    );
                    Service_PurchaseInventory::update($updateRow, $poInventory['pi_id'], 'pi_id');
                    $planNote.="<span>产品有采购库存记录，更新qty_plan:".$poInventory['qty_plan']."为".($poInventory['qty_plan'] + $poCount)."</span>\n";
                }else{
                    $addRow = array( // 新增采购库存
                        'qty_plan' => $poCount,
                        'qty_create' => 0,
                        'qty_release' => 0,
                        'pi_sku' => $product['product_sku'],
                        'pi_product_id' => $product_id,
                        'warehouse_id' => $warehouse_id
                    );
                    Service_PurchaseInventory::add($addRow);
                    $planNote.="<span>产品没有采购库存记录，添加采购库存数量:". $poCount."</span>\n";
                }

                $popArr = array(
                        'plan_status' => '0',
                        'product_id' => $product_id,
                        'warehouse_id' => $warehouse_id,
                        'plan_qty' => $poCount,
                        'paln_date' => date('Y-m-d H:i:s'),
                        // 'release_date' => date('Y-m-d H:i:s'),
                        // 'operator_release' => '',
                        'plan_type' =>$plan_type,
                        'plan_note' => $planNote,
                );
                // 添加计划
                Service_PurchaseProductPlan::add($popArr);
                $db->commit();
            }catch(Exception $e){
                $db->rollback();
                Ec::showError('product_id:'.$product_id.',warehouse_id:'.$warehouse_id."发生异常,异常原因：\n".$e->getMessage(), 'auto_product_supply_strategy_');
            }
//             Ec::showError($sqlLog, 'auto_product_supply_strategy_log_');
        }
    }
    
    /*
     * 以下方法忽略
     */

    /*
     *  无效方法
     * 自动采购策略
     * 计划生成逻辑说明：
     *  1，手工运行生成计划
     *  2，取product_supply_strategy（产品供应策略表）数据，根据（备货天数*销量+补货周期*销量）计算数量。
     *  3，可用库存+在途库存-待出库存-缺货库存-计算的数量
     *  4，如果第3点得出的是负数，则得出计划采购数量
     *  5，根据计划数量-(purchase_inventory)表中所有数量，生成计划表数据（purchase_product_plan）
     *  6，生成计划表数量后，需要修改purchase_inventory表，SKU的已计划数量
     */
    private static function ______________________________________________________________autoProductSupplyStrategy()
    {
        $db = Common_Common::getAdapter();
        $sql = 'select product_id,warehouse_id,sum(pss_value) sum from product_supply_strategy group by product_id,warehouse_id'; // 按照仓库,产品分组，获取采购天数
        $result = $db->fetchAll($sql);
        
        foreach($result as $v){
            $db->beginTransaction();
            try{
                $product_id = $v['product_id'];
                $warehouse_id = $v['warehouse_id'];
                $product = Service_Product::getByField($product_id, 'product_id');
                if(! $product){
                    throw new Exception('数据异常，产品数据不存在-->product_id:' . $product_id);
                }
                $day = 7; // 仓库产品最近7天销量
                $sql = " SELECT";
                $sql .= " case when SUM(b.op_quantity) is null then 0 else SUM(b.op_quantity)  end as c ";
                $sql .= " FROM `orders` a INNER JOIN order_product b ";
                $sql .= " on a.order_code=b.order_code ";
                $sql .= " where 1=1 ";
                $sql .= " and a.warehouse_id=PRODUCTID ";
                $sql .= " and b.product_id=WAREHOUSEID ";
                $sql .= " and unix_timestamp (a.add_time)>unix_timestamp(DATE_add(NOW(),INTERVAL -24*" . $day . " HOUR ) )";
                
                $sql = str_replace("PRODUCTID", $product_id, $sql);
                $sql = str_replace("WAREHOUSEID", $warehouse_id, $sql);
                $count = $db->fetchOne($sql);
                
                // 缺货数量，需要统计
                $sql = 'select case when sum(b.op_quantity)>0 then sum(b.op_quantity) else 0 end sum from orders a ';
                $sql .= ' inner join order_product b on a.order_id=b.order_id ';
                $sql .= ' where 1=1 ';
                $sql .= ' and a.order_status=3 ';
                $sql .= ' and a.warehouse_id=' . $warehouse_id;
                $sql .= ' and b.product_id=' . $product_id;
                $quehuoSum = $db->fetchOne($sql); // 缺货数量
                
                $avg = ($count / $day); // 7天平均销量
                $count = ceil($avg * $v['sum']); // 总共需要采购数取整
                
                $count += $quehuoSum; // 总共需要采购数+缺货数
                
                if($count == 0){ // 无需采购
                    throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                }
                $con = array(
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id
                );
                $inventoryRow = Service_ProductInventory::getByCondition($con);
                if($inventoryRow){ // 已有库存
                    $inventoryRow = $inventoryRow[0];
                    // 已有库存-待采购数
                    $poCount = $inventoryRow['pi_sellable'] + $inventoryRow['pi_onway'] + $inventoryRow['pi_pending'] - $inventoryRow['pi_reserved'] - $count;
                }else{
                    $poCount = 0 - $count;
                }
                
                if($poCount >= 0){ // 无需采购
                    throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                }
                
                // 计划采购数，由负数转为正数，取绝对值
                $poCount = abs($poCount);
                $con = array(
                    'pi_product_id' => $product_id,
                    'warehouse_id' => $warehouse_id
                );
                // 采购库存
                $poInventory = Service_PurchaseInventory::getByCondition($con);
                if($poInventory){
                    $poInventory = $poInventory[0];
                    $poCount = $poCount - $poInventory['qty_plan'] - $poInventory['qty_create'] - $poInventory['qty_release'];
                }
                if($poCount <= 0){ // 无需采购
                    throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                }
                
                $popArr = array(
                    'plan_status' => '0',
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                    'plan_qty' => $poCount,
                    'paln_date' => date('Y-m-d H:i:s'),
                    // 'release_date' => date('Y-m-d H:i:s'),
                    // 'operator_release' => '',
                    'plan_type' => '1'
                );
                // 添加计划
                Service_PurchaseProductPlan::add($popArr);
                if($poInventory){
                    $updateRow = array( // 更新计划采购数
                        'qty_plan' => $poInventory['qty_plan'] + $poCount
                    );
                    Service_PurchaseInventory::update($updateRow, $poInventory['pi_id'], 'pi_id');
                }else{
                    $addRow = array( // 新增采购库存
                        'qty_plan' => $poCount,
                        'qty_create' => 0,
                        'qty_release' => 0,
                        'pi_sku' => $product['product_sku'],
                        'pi_product_id' => $product_id,
                        'warehouse_id' => $warehouse_id
                    );
                    Service_PurchaseInventory::add($addRow);
                }
                
                $db->commit();
            }catch(Exception $e){
                $db->rollback();
                Ec::showError($e->getMessage(), 'auto_product_supply_strategy_');
            }
        }
    }
    
    /*
     * 无效方法 自动采购策略 计划生成逻辑说明： 1，手工运行生成计划
     * 2，取product_supply_strategy（产品供应策略表）数据，根据（备货天数*销量+补货周期*销量）计算数量。
     * 3，可用库存+在途库存-待出库存-缺货库存-计算的数量 4，如果第3点得出的是负数，则得出计划采购数量
     * 5，根据计划数量-(purchase_inventory)表中所有数量，生成计划表数据（purchase_product_plan）
     * 6，生成计划表数量后，需要修改purchase_inventory表，SKU的已计划数量
     */
    private static function ______________________________________________________________autoWarehouseSupplyStrategy()
    {
        $db = Common_Common::getAdapter();
        $sql = 'select warehouse_id,sum(wss_value) sum from warehouse_supply_strategy group by warehouse_id'; // 按照仓库分组，获取采购天数
        $result = $db->fetchAll($sql);
        
        foreach($result as $v){
            $con = array(
                'warehouse_id' => $v['warehouse_id']
            );
            $productInventoryRows = Service_ProductInventory::getByCondition($con);
            
            foreach($productInventoryRows as $productInventoryRow){
                $db->beginTransaction();
                try{
                    $product_id = $productInventoryRow['product_id'];
                    $warehouse_id = $v['warehouse_id'];
                    
                    $product = Service_Product::getByField($product_id, 'product_id');
                    if(! $product){
                        throw new Exception('数据异常，产品数据不存在-->product_id:' . $product_id);
                    }
                    $con = array(
                        'warehouse_id' => $warehouse_id,
                        'product_id' => $product_id
                    );
                    $productSupplyStrategy = Service_ProductSupplyStrategy::getByCondition($con);
                    if($productSupplyStrategy){ // 有自定义采购计划，跳过
                        throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '已经自己的采购计划');
                    }
                    
                    $day = 7; // 仓库产品最近7天销量
                    $sql = " SELECT";
                    $sql .= " case when SUM(b.op_quantity) is null then 0 else SUM(b.op_quantity)  end as c ";
                    $sql .= " FROM `orders` a INNER JOIN order_product b ";
                    $sql .= " on a.order_code=b.order_code ";
                    $sql .= " where 1=1 ";
                    $sql .= " and a.warehouse_id=PRODUCTID ";
                    $sql .= " and b.product_id=WAREHOUSEID ";
                    $sql .= " and unix_timestamp (a.add_time)>unix_timestamp(DATE_add(NOW(),INTERVAL -24*" . $day . " HOUR ) )";
                    
                    $sql = str_replace("PRODUCTID", $product_id, $sql);
                    $sql = str_replace("WAREHOUSEID", $warehouse_id, $sql);
                    $count = $db->fetchOne($sql);
                    
                    // 缺货数量，需要统计
                    $sql = 'select case when sum(b.op_quantity)>0 then sum(b.op_quantity) else 0 end sum from orders a ';
                    $sql .= ' inner join order_product b on a.order_id=b.order_id ';
                    $sql .= ' where 1=1 ';
                    $sql .= ' and a.order_status=3 ';
                    $sql .= ' and a.warehouse_id=' . $warehouse_id;
                    $sql .= ' and b.product_id=' . $product_id;
                    $quehuoSum = $db->fetchOne($sql); // 缺货数量
                    
                    $avg = ($count / $day); // 7天平均销量
                    $count = ceil($avg * $v['sum']); // 总共需要采购数取整
                    
                    $count += $quehuoSum; // 总共需要采购数+缺货数
                    
                    if($count == 0){ // 无需采购
                        throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                    }
                    $con = array(
                        'pi_product_id' => $product_id,
                        'warehouse_id' => $warehouse_id
                    );
                    $inventoryRow = Service_ProductInventory::getByCondition($con);
                    if($inventoryRow){ // 已有库存
                        $inventoryRow = $inventoryRow[0];
                        // 已有库存-待采购数
                        $poCount = $inventoryRow['pi_sellable'] + $inventoryRow['pi_onway'] + $inventoryRow['pi_pending'] - $inventoryRow['pi_reserved'] - $count;
                    }else{
                        $poCount = 0 - $count;
                    }
                    
                    if($poCount >= 0){ // 无需采购
                        throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                    }
                    
                    // 计划采购数，由负数转为正数，取绝对值
                    $poCount = abs($poCount);
                    $con = array(
                        'product_id' => $product_id,
                        'warehouse_id' => $warehouse_id
                    );
                    // 采购库存
                    $poInventory = Service_PurchaseInventory::getByCondition($con);
                    if($poInventory){
                        $poInventory = $poInventory[0];
                        $poCount = $poCount - $poInventory['qty_plan'] - $poInventory['qty_create'] - $poInventory['qty_release'];
                    }
                    if($poCount <= 0){ // 无需采购
                        throw new Exception('SKU:' . $product['product_sku'] . ',仓库id:' . $warehouse_id . '无需新增采购计划');
                    }
                    
                    $popArr = array(
                        'plan_status' => '0',
                        'product_id' => $product_id,
                        'warehouse_id' => $warehouse_id,
                        'plan_qty' => $poCount,
                        'paln_date' => date('Y-m-d H:i:s'),
                        // 'release_date' => date('Y-m-d H:i:s'),
                        // 'operator_release' => '',
                        'plan_type' => '1'
                    );
                    // 添加计划
                    Service_PurchaseProductPlan::add($popArr);
                    if($poInventory){
                        $updateRow = array( // 更新计划采购数
                            'qty_plan' => $poInventory['qty_plan'] + $poCount
                        );
                        Service_PurchaseInventory::update($updateRow, $poInventory['pi_id'], 'pi_id');
                    }else{
                        $addRow = array( // 新增采购库存
                            'qty_plan' => $poCount,
                            'qty_create' => 0,
                            'qty_release' => 0,
                            'pi_sku' => $product['product_sku'],
                            'pi_product_id' => $product_id,
                            'warehouse_id' => $warehouse_id
                        );
                        Service_PurchaseInventory::add($addRow);
                    }
                    
                    $db->commit();
                }catch(Exception $e){
                    $db->rollback();
                    Ec::showError($e->getMessage(), 'auto_product_supply_strategy_');
                }
            }
        }
    }
    
    /**
     * 统计预警库存
     */
    public function calProductWarningQty(){
        $db = Common_Common::getAdapter();
        //统计预警库存
        $rows = Service_ProductInventory::getByCondition(array(), '*');
        foreach($rows as $k => $v){
            $v = $this->getInventoryIntegrate($v);
            $db->update('product_inventory', array(
                    'pi_warning_qty' => $v['pi_warning_qty']
            ), 'warehouse_id=' . $v['warehouse_id'] . ' and product_id=' . $v['product_id']);
        }
    }
    
    
    /**
     * 获取采购策略等信息
     * @param array $inventoryRow
     * @return Ambigous <number, string>
     */
    public  function getInventoryIntegrate($inventoryRow){
        $db = Common_Common::getAdapter();
        $v = $inventoryRow;
        $productId = $v['product_id'];
        $warehouseId = $v['warehouse_id'];
    
        $product = Service_Product::getByField($v['product_id'],'product_id');
        $v['product_title'] = $product['product_title'];
        $v['pi_in_used'] = $v['pi_sellable']+$v['pi_reserved'];
        /*
         $sql = 'select case when sum(op_quantity) is not null then sum(op_quantity) else 0 end from orders a inner join order_product b on a.order_id = b.order_id where a.order_status=3 and b.product_id= '.$v['product_id'].' and a.warehouse_id = '.$v['warehouse_id'];
        $pi_no_stock = $db->fetchOne($sql);
        //                     echo $sql.';<br/>';
        $v['pi_no_stock'] = $pi_no_stock;
        */
        $pi_no_stock = $v['pi_no_stock'];
        $v['pi_no_stock_days'] = 0;
        if($pi_no_stock>0){//缺货天数
            $sql = 'select to_days(now()) - to_days(add_time) as no_stock_days, add_time from orders a inner join order_product b on a.order_id = b.order_id where a.order_status=3 and b.product_id= '.$v['product_id'].' and a.warehouse_id = '.$v['warehouse_id'].' order by add_time asc limit 1';
            $pi_no_stock_days = $db->fetchRow($sql);
            //                     echo $sql.';<br/>';
            $v['pi_no_stock_days'] = $pi_no_stock_days['no_stock_days'];
            $v['pi_no_stock_max_day'] = $pi_no_stock_days['add_time'];
        }
    
        $v['pi_warning'] = 0;//是否预警
        $v['pi_can_sale_days'] = $v['pi_sellable']?'999':'0';//可售天数
        $v['pi_warning_qty'] = 0;//预警数量
    
        $sql = 'select qty_sales,sales_type sale_type from warehouse_product_sales where product_id= '.$v['product_id'].' and warehouse_id = '.$v['warehouse_id'];
        $product_sales = $db->fetchRow($sql);
        //                     echo $sql.';<br/>';
         
        if($product_sales&&$product_sales['qty_sales']){//有出售记录
            $can_sale_day = $v['pi_sellable']/$product_sales['qty_sales'];//可售天数
            $strategy_day = 0;//采购策略天数
            $sql = 'select sum(pss_value) from product_supply_strategy where product_id= '.$v['product_id'].' and warehouse_id = '.$v['warehouse_id'].' and sales_type='.$product_sales['sale_type'];
            $strategy_day = $db->fetchOne($sql);
            //                     echo $sql.';<br/>';
            if(empty($strategy_day)){
                $sql = 'select sum(wss_value) from warehouse_supply_strategy where  warehouse_id = '.$v['warehouse_id'].' and sales_type='.$product_sales['sale_type'];
                $strategy_day = $db->fetchOne($sql);
                //                     echo $sql.';<br/>';
                if(empty($strategy_day)){
                    $strategy_day = 0;
                }
            }
            $can_sale_day =  round($can_sale_day,2);
            $v['pi_can_sale_days'] = $can_sale_day;
            //预警数量
            $v['pi_warning_qty'] = intval($product_sales['qty_sales']*$strategy_day);
            if($can_sale_day<$strategy_day){
                //                            echo  $v['product_id'].'  ';//exit;
                $v['pi_warning'] = 1;
                $v['pi_warning_message'] = '可售数量：'.$v['pi_sellable'].'，日均销量：'.$product_sales['qty_sales'].','."\n".'可售天数'.$can_sale_day.'，预警天数：'.$strategy_day."\n，预警数量：".$v['pi_warning_qty'].',可售天数小于预警天数';
            }
        }
    
        return $v;
    }


    /**
     * 设置默认采购员
     */
    public function setInventoryBuyerId(){
        //设置默认采购员
        $rows = Service_Product::getByCondition(array(), '*');
        foreach($rows as $k => $v){
            $buyerId = $this->getInventoryBuyerId($v['product_id']);
            $row = array(
                    'buyer_id' => $buyerId
            );
            Service_ProductInventory::update($row, $v['product_id'],'product_id'); 
            Service_Product::update($row, $v['product_id'],'product_id');            
//             $db->update('product_inventory', $row, 'product_id=' . $v['product_id']);
        }
    }


    /**
     * 统计预警库存
     */
    public function getInventoryBuyerId($productId){
        try{            
            $product = Service_Product::getByField($productId, 'product_id');
            if(empty($product['pd_id'])){
                throw new Exception('产品没有与产品开发关联');
            }
            $productDev = Service_ProductDevelop::getByField($product['pd_id'], 'pd_id');
            if(empty($productDev['default_supplier_code'])){//产品有们默认供应商，查找默认供应商
                $supPro = Service_SupplierProduct::getByField($product['pd_id'], 'pd_id');
                if(! $supPro){
                    $buyerId = '0';
                }else{
                    $sup = Service_Supplier::getByField($supPro['supplier_id'], 'supplier_id');
                    if(! $sup){
                        throw new Exception('供应商数据异常');
                    }
                    $buyerId = $sup['buyer_id'];
                }
            }else{
                $sup = Service_Supplier::getByField($productDev['default_supplier_code'], 'supplier_code');
                if(! $sup){
                    throw new Exception('供应商代码不存在' . $productDev['default_supplier_code']);
                }
                $buyerId = $sup['buyer_id'];
            }
        }catch(Exception $e){
            $buyerId = '0';
        }
        
        $buyerId = empty($buyerId) ? '0' : $buyerId;
        return $buyerId;
    }
}