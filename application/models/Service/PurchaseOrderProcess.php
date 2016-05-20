<?php
class Service_PurchaseOrderProcess 
{
    public static $statusArr = array(
        '0' => array(
            'name' => '作废',
            'actions' => array(
                
            )
        ),
        '1' => array(
            'name' => '新建(待审批)',
            'actions' => array(
                '<input type="button" value="交货确认" id="etaConfirm" class="baseBtn" style="margin-left: 20px;" />',
                '<input type="button" value="撤销采购单" id="revocationPurchase" class="baseBtn" style="margin-left: 10px; " />'
            )
        ),
        '2' => array(
            'name' => '交货确认',
            'actions' => array(
                '<input type="button" value="审批" id="eximain" class="baseBtn" style="margin-left: 20px; " />',
                '<input type="button" value="撤销确认" id="derecognition" class="baseBtn" style="margin-left: 10px; " />',
                '<input type="button" value="撤销采购单" id="revocationPurchase" class="baseBtn" style="margin-left: 10px; " />'
            )
        ),
        '3' => array(
            'name' => '未到货',
            'actions' => array(
                
            )
        ),
        '4' => array(
            'name' => '部分到货',
            'actions' => array(
                '<input type="button" value="强制完成采购单" id="forcedComplete" class="baseBtn" style="margin-left: 10px; " />'
            )
        ),
//         '5' => array(
//             'name' => '待检验',
//             'actions' => array(
//                 '<input type="button" value="强制完成采购单" id="forcedComplete" class="baseBtn" style="margin-left: 10px; " />'
//             )
//         ),
//         '6' => array(
//             'name' => '已检验',
//             'actions' => array(
//                 '<input type="button" value="强制完成采购单" id="forcedComplete" class="baseBtn" style="margin-left: 10px; " />'
//             )
//         ),
        '7' => array(
            'name' => '已完成',
            'actions' => array(
            )
        ),
            
    )
    ;
    /*
     * 创建采购单时，将采购计划设置为已经审批
     * 更新采购库存数据
     * 建立采购表
     * 建立采购明细表
     */
    public static function createPoByPlanIds($planIds,$supplier_code,$to_warehouse_id=0){
        $supplier = Service_Supplier::getByField($supplier_code, 'supplier_code');
        if(! $supplier){
            throw new Exception('供应商不存在');
        }
        $db = Common_Common::getAdapter();
        $sql = 'SELECT 
                	a.*,
                	b.product_sku,
                    b.pd_id
                FROM `purchase_product_plan` a 
                INNER JOIN product b on a.product_id = b.product_id          
                where 1=1
                and a.plan_status=0
                and a.plan_id in (' . implode(',', $planIds) . ')
                ;                
                ';
        $rows = $db->fetchAll($sql);
        if(empty($rows)){
            throw new Exception('采购计划已经处理过了，不可重复处理'); 
        }
        $warehouseArr = array();
        $poProduct = array();
        $po_code = Common_GetNumbers::getCode('create_po', '1', 'PO');
        $sumPayableAmount = 0;
        $currencyArr = array();
        foreach($rows as $k => $v){
            if($v['plan_status']!=0){
                throw new Exception('采购计划已经处理过了，不可重复处理');                
            }
            $warehouseArr[$v['warehouse_id']] = $v['warehouse_id'];
            
            $con = array(
                'supplier_id' => $supplier['supplier_id'],
                'pd_id' => $v['pd_id']
            );
            $sp = Service_SupplierProduct::getByCondition($con);
            if(! $sp){
//                 throw new Exception('该供应商' . $supplier_code . ' 没有SKU' . $v['product_sku'] . '的报价');
                $sp = array('sp_last_price'=>0,'currency_code'=>'RMB');
            }else{
                $sp = $sp[0];                
            }
            $currencyArr[$sp['currency_code']] = $sp['currency_code'];
            // print_r($v);exit;
            $sumPayableAmount += $v['plan_qty'] * $sp['sp_last_price'];
            // echo $sumPayableAmount;exit;
            if(isset($poProduct[$v['product_id']])){
                $poProduct[$v['product_id']]['qty_expected'] += $v['plan_qty'];
                $poProduct[$v['product_id']]['payable_amount'] += $v['plan_qty'] * $sp['sp_last_price'];
            }else{

                /**
                 * 临时方案，如果创建PO的时候存在计划类型为”缺货“计划，则PO单详细的补货类型是”缺货“
                 */
                $tempPlanCondition=array(
                    'product_id'=>$v['product_id'],
                    'plan_type'=>'3',
                    'plan_status'=>'0',
                );
               $tempExist= Service_PurchaseProductPlan::getByCondition($tempPlanCondition);
               $plan_type='1';
               if(!empty($tempExist)){
                   $plan_type='3';
               }
               $poProduct[$v['product_id']] = array(
                    'po_code' => $po_code,
                    'po_status' => '1',
                    
                    'product_id' => $v['product_id'],
                    'qty_expected' => $v['plan_qty'],
                    'qty_receving' => '0',
                    'payable_amount' => $v['plan_qty'] * $sp['sp_last_price'],
                    'actually_amount' => '0',
                    'currency_code' => $sp['currency_code'],
                    'unit_price' => $sp['sp_last_price'],
                    'shipping_method_id' => '0',
                    'plan_type' =>$plan_type
                );
            }
            $updateRow = array(
                'plan_status' => 1
            ); // 更新采购计划状态
            
            if(! Service_PurchaseProductPlan::update($updateRow, $v['plan_id'], 'plan_id')){
                throw new Exception('create Po Fail..');
            }
        }
        if(count($currencyArr) != 1){
            throw new Exception('产品的采购价格币种不一致');
        }
        if(empty($rows)){
            throw new Exception('没有需要生成采购单的采购计划');
        }
        if(count($warehouseArr) > 1){
            throw new Exception('不同仓库的采购计划不能合并采购');
        }
        sort($warehouseArr);
        $purchaseOrdersRow = array(
            'po_code' => $po_code,
            'create_type' => '1',
            'warehouse_id' => $warehouseArr[0],
            'shipping_method_id_head' => '',
            'tracking_no' => '',
            'ref_no' => '',
            'supplier_id' => $supplier['supplier_id'],
            'payable_amount' => $sumPayableAmount,
            'currency_code' => $sp['currency_code'],
            'actually_amount' => '',
            'pay_status' => '0',
            'po_status' => '1',
            'po_type' => '0',
            'date_release' => '',
            'date_create' => date("Y-m-d H:i:s"),
            'operator_create' => Service_User::getUserId(),
            'operator_release' => '',
            'operator_purchase' => $supplier['buyer_id'],
            'date_eta' => '',
            'to_warehouse_id'=>empty($to_warehouse_id)?'0':$to_warehouse_id,
        );
        if(! $po_id = Service_PurchaseOrders::add($purchaseOrdersRow)){
            throw new Exception('create Po Fail...');
        }
        // print_r($poProduct);exit;
        // exit;
        foreach($poProduct as $v){
            $v['po_id'] = $po_id;
            if(! Service_PurchaseOrderProduct::add($v)){
                throw new Exception('create Po Fail......');
            }
            
            $con = array(
                'warehouse_id' => $warehouseArr[0],
                'pi_product_id' => $v['product_id']
            );
            $exist = Service_PurchaseInventory::getByCondition($con);
            
            if($exist){
                $exist = $exist[0];
                $updateRow = array(
                    'qty_plan' => $exist['qty_plan'] - $v['qty_expected'],
                    'qty_create' => $exist['qty_create'] + $v['qty_expected']
                );
                if(! Service_PurchaseInventory::update($updateRow, $exist['pi_id'], 'pi_id')){ // 更新采购库存数量
                    throw new Exception('create Po Fail.........');
                }
            }else{
                throw new Exception('采购库存中无该产品数据');
            }
        }
        return array(
            'ask' => '1',
            'message' => '创建采购单成功',
            'po_code' => $po_code
        );
    }
    /**
     * 废弃
     * @param unknown_type $planIds
     * @param unknown_type $supplier_code
     * @return multitype:string
     */
    public static function ____createPoByPlanIdsTransaction($planIds,$supplier_code){
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $return = array('ask'=>0,'message'=>'');
        try{
            $return = self::createPoByPlanIds($planIds, $supplier_code);
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    

    public static function createPoByPlanIdsBatchTransaction($planIds,$to_warehouse_id=0){
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $return = array('ask'=>0,'message'=>'','result'=>array());
        try{
            $sql = 'SELECT 
                	a.*,
                	b.product_sku,
                    b.pd_id,
                    c.default_supplier_code
                FROM `purchase_product_plan` a 
                INNER JOIN product b on a.product_id = b.product_id 
                left JOIN product_develop c on c.pd_id=b.pd_id                
                where 1=1
                and a.plan_status=0
                and a.plan_id in (' . implode(',', $planIds) . ')
                ;                
                ';
            $virtualSupplier = Service_Supplier::getByField(4,'supplier_type');
            if(!$virtualSupplier){
                throw new Exception('未设置通用虚拟供应商');
            }
            $rows = $db->fetchAll($sql);

            if(empty($rows)){
                throw new Exception('采购计划已经处理过了，不可重复处理');
            }
            
            $supplierPlanArr = array();
            foreach($rows as $row){
                if(!isset($row['default_supplier_code']) || empty($row['default_supplier_code'])){
                    $row['default_supplier_code'] = $virtualSupplier['supplier_code'];
                }
                $supplierPlanArr[$row['default_supplier_code']][] = $row['plan_id'];
            }
            foreach($supplierPlanArr as $supplier_code => $subPlanIds){
                $result = self::createPoByPlanIds($subPlanIds, $supplier_code,$to_warehouse_id);
                $return['result'][] = $result;
            }
            $return['ask'] = 1;
            $return['message'] = '所有选择采购计划已经生成采购单';
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    /**************************保存采购信息 开始*************************************/
    /**
     * 获取虚拟供应商
     * @param unknown_type $company_code
     * @return multitype:string mixed Ambigous <string, mixed>
     */
    public function getVirtualSupplier($company_code=null){
        if(empty($company_code)){
            $company_code = Common_Company::getCompanyCode();
        }
        $con = array('company_code'=>$company_code,'supplier_type'=>'4');
        $exist = Service_Supplier::getByCondition($con);
        if(empty($exist)){               
            $row = array(
                'company_code' => $company_code,
                'supplier_code' => 'XNGYS',
                'supplier_name' => '虚拟供应商',
                'supplier_type' => '4',
                'supplier_desc' => '虚拟供应商'
            );
            $row['supplier_id']  = Service_Supplier::add($row);            
        }else{
            $row = $exist[0];
        }
        return $row;
    }
    /**
     * 采购更新供应商信息
     * 1.如果建立采购单，没有选择供应商，判断该公司代码是否存在虚拟供应商，如果没有虚拟供应商，建立一个虚拟供应商
     * 2.判断供应商与产品关系是否存在，不存在，建立供应商产品关系，存在，更新供应商产品关系，并记录日志
     * 3.保存时，判断产品是否只有一个供应商产品关系，如果只有一个，更新产品表的采购价、默认供应商， 并记录日志
     * @param unknown_type $po
     * @param unknown_type $poProductArr
     */
    public static function savePurchaseOrderSupplierRelation($po,$poProductArr){
        //1.
        if(empty($po['supplier_id'])){
            $supplier = $this->getVirtualSupplier($po['company_code']);
            $po['supplier_id'] = $supplier['suppliler_id'];
        }else{
            $supplier = Service_Supplier::getByField($po['suppliler_id'],'suppliler_id');
        }
        //2.
        foreach($poProductArr as $pop){
            $con = array(
                'supplier_id' => $po['supplier_id'],
                'product_id' => $pop['product_id']
            );
            $exist = Service_SupplierProduct::getByCondition($con);
            if($exist){
                $exist = $exist[0];
                $row = array(
                    'company_code' => Common_Company::getCompanyCode(),
                    'sp_supplier_sku' => $pop['sp_supplier_sku'],
                    'product_id' => $pop['product_id'],
                    'product_sku' => $pop['product_sku'],
                    'suppliler_id' => $pop['suppliler_id'],
                    'sp_unit_price' => $pop['sp_unit_price'],
                    'creater_id' => Service_User::getUserId(),
                    'sp_update_time' => date('Y-m-d H:i:s')
                );
                Service_SupplierProduct::update($row, $exist['sp_id'], 'sp_id');
                // 日志
            }else{
                $row = array(
                    'company_code' => Common_Company::getCompanyCode(),
                    'sp_supplier_sku' => $pop['sp_supplier_sku'],
                    'product_id' => $pop['product_id'],
                    'product_sku' => $pop['product_sku'],
                    'suppliler_id' => $pop['suppliler_id'],
                    'sp_unit_price' => $pop['sp_unit_price'],
                    'creater_id' => Service_User::getUserId(),
                    'sp_add_time' => date('Y-m-d H:i:s'),
                    'sp_update_time' => date('Y-m-d H:i:s')
                );
                Service_SupplierProduct::add($row);
                // 日志
            }
            
            //3.
            $product = Service_Product::getByField($pop['product_id']);
            $con = array(
                'supplier_id' => $po['supplier_id'],
                'product_id' => $pop['product_id'],
            );
            $exist = Service_SupplierProduct::getByCondition($con);
            if(empty($product['product_purchase_value']) && count($exist) == 1){
                $exist = $exist[0];
                $updateRow = array(
                    'supplier_id'=>$exist['supplier_id'],
                    'product_purchase_value' => $exist['sp_unit_price'],
                )
                ;
                Service_Product::update($updateRow, $product['product_id']);
            }
        }
    }
    
    /**
     * 采购单数据验证
     * 采购仓库
     * 采购员
     * @param unknown_type $po
     * @param unknown_type $poProductArr
     */
    public static function validatePurchaseOrder($po,$poProductArr){
        $err = array();
        if(!$po['warehouse_id']){
            $err[] = '没有选择仓库';
        }
        if(!$po['operator_purchase']){
            $err[] = '没有选择采购员';
        }
        foreach($poProductArr as $pop){           
            if(!$pop['product_id']){
                $err[] = '没有选择产品';
            }
            if(!$pop['product_sku']){
                $err[] = '没有选择产品';
            }
            if(!$pop['qty_expected']){
                $err[] = $pop['product_sku'].'没有输入数量';
            }
            if(!$pop['unit_price']){
                $err[] = $pop['product_sku'].'没有填写单价';
            }
        }
        
        return $err;
    }
    /**
     * 采购单数据验证
     * 采购仓库
     * 采购员
     * @param unknown_type $po
     * @param unknown_type $poProductArr
     */
    public static function formatPurchaseOrder($params){
        $po = array();
        $poProductArr = array( 
                'company_code'=>Common_Company::getCompanyCode(),
                'create_type'=>$params['create_type'],
                'warehouse_id'=>$params['warehouse_id'],
                'shipping_method_id_head'=>$params['shipping_method_id_head'],
                'tracking_no'=>$params['tracking_no'],
                'ref_no'=>$params['ref_no'],
                'receiving_code'=>$params['receiving_code'],
                'supplier_id'=>$params['supplier_id'],
                'payable_amount'=>$params['payable_amount'],
                'actually_amount'=>$params['actually_amount'],
                'currency_code'=>$params['currency_code'],
                'pay_status'=>$params['pay_status'],
                'po_status'=>$params['po_status'],
                'po_type'=>$params['po_type'],
                'date_create'=>now(),
//                 'date_release'=>$params['date_release'],
//                 'operator_create'=>$params['operator_create'],
//                 'operator_release'=>$params['operator_release'],
//                 'operator_purchase'=>$params['operator_purchase'],
//                 'date_eta'=>$params['date_eta'],
                'po_update_time'=>now(),
                'to_warehouse_id'=>$params['to_warehouse_id'],
                'pay_ship_amount'=>$params['pay_ship_amount'],
//                 'receiving_exception'=>$params['receiving_exception'],
//                 'receiving_exception_handle'=>$params['receiving_exception_handle'],
//                 'currency_rate'=>$params['currency_rate'],
        ); 
          
        foreach($params['product'] as $k=>$v){
            $poProductArr[] = array(
                    'product_id' => $v['product_id'],
                    'product_sku' => $v['product_sku'],
                    'supplier_id' => $v['supplier_id'],
                    'sp_supplier_sku' => $v['sp_supplier_sku'],
                    'qty_expected' => $v['qty_expected'],
                    'qty_eta' => $v['qty_eta'],
                    'qty_receving' => $v['qty_receving'],
                    'payable_amount' => $v['payable_amount'],
                    'actually_amount' => $v['actually_amount'],
                    'currency_code' => $v['currency_code'],
                    'unit_price' => $v['unit_price'],
                    'shipping_method_id' => $v['shipping_method_id'],
                    'plan_type' => $v['plan_type'],
                    'pop_update_time' => now(),
                    'note' => $v['note'],
                    'qty_pay' => $v['qty_pay'],
                    'receiving_exception' => $v['receiving_exception'],
                    'receiving_exception_handle' => $v['receiving_exception_handle']
            );
        }
        
        $result = array('po'=>$po,'poProductArr'=>$poProductArr); 
        return $result;
    }
    /**
     * 新增、修改 采购单
     * 新增采购单--数据保存
     * 修改采购单--删除采购明细，数据保存
     * @param unknown_type $po
     * @param unknown_type $poProductArr
     */
    public static function savePurchaseOrder($po,$poProductArr){
        if($po['po_id']){
            $po_id = $po['po_id'];
            unset($po['po_id']);
            unset($po['po_code']);
            unset($po['date_create']);
            foreach($po as $k=>$v){
                if(!isset($v)){
                    unset($po[$k]);
                }
            }
            Service_PurchaseOrders::update($po,$po_id,'po_id');
        }else{
            $po['po_code'] = Common_GetNumbers::getCode('create_po',Common_Company::getCompanyCode(),'PO');//生成采购单号
            $po_id = Service_PurchaseOrders::add($po);
        }
        //删除明细
        Service_PurchaseOrderProduct::delete($po_id,'po_id');
        //2.
        foreach($poProductArr as $pop){
            $row = array(
                'po_id' => $po_id,
                'po_code' => $po['po_code'],
                'product_id' => $pop['product_id'],
                'product_sku' => $pop['product_sku'],
                'supplier_id' => $po['supplier_id'],
                'sp_supplier_sku' => $pop['sp_supplier_sku'],
                'qty_expected' => $pop['qty_expected'],
                'qty_eta' => $pop['qty_eta'],
                'qty_receving' => $pop['qty_receving'],
                'payable_amount' => $pop['payable_amount'],
                'actually_amount' => $pop['actually_amount'],
                'currency_code' => $pop['currency_code'],
                'unit_price' => $pop['unit_price'],
                'shipping_method_id' => $pop['shipping_method_id'],
                'plan_type' => $pop['plan_type'],
                'pop_update_time' => now(),
                'note' => $pop['note'],
                'qty_pay' => $pop['qty_pay'],
                'receiving_exception' => $pop['receiving_exception'],
                'receiving_exception_handle' => $pop['receiving_exception_handle']
            );
            foreach($row as $k=>$v){//去除未设置项
                if(!isset($v)){
                    unset($row[$k]);
                }
            }
            Service_PurchaseOrderProduct::add($row);
        }
        //日志
    }
    /**
     * 保存采购单
     * 1.格式化请求数据
     * 2.验证数据有效性
     * 3.数据保存
     * @param unknown_type $params
     */
    public static function savePurchaseOrderTransaction($params){
        $return = array('ask'=>0,'message'=>'');
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
//             1.
            $params = self::formatPurchaseOrder($params);
            $po = $params['po'];
            $poProductArr = $params['poProductArr'];           
//             2.
            $err = self::validatePurchaseOrder($po, $poProductArr);
            if($err){
                $return['err'] = $err;
                throw new Exception('数据不合法');
            }
//             3.
            self::savePurchaseOrder($po, $poProductArr);
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = '操作成功';
        }catch(Exception $e){
            $db->rollBack();
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }
    /**************************保存采购信息 结束*************************************/
}