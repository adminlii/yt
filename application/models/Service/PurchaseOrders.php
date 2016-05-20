<?php
class Service_PurchaseOrders extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PurchaseOrders|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PurchaseOrders();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "po_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "po_id")
    {
        $model = self::getModelInstance();
        return $model->delete($value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'po_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        return  Common_Validator::formValidator($validateArr);
    }



    /**
     * 交货确认
     */
    public static function etaYes($paramsPo = array(),$paramsProduct=array(),$poCode,$supplierProduct=array(),$rowLog = array()){
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $date = date('Y-m-d H:i:s');
        try {
            //当前操作用户
            $userId = Service_User::getUserId();
            //获取主键，销毁array元素，为了防止更新主键
            //     		$paramPoId = $paramsPo['po_id'];
            if (!empty($paramsPo['po_id'])) {
                unset($paramsPo['po_id']);
            }
            /*
             * 单价变如果发生变化则需要改supplier_product 添加：supplier_product_log
            */
            if(!empty($supplierProduct)){
                self::eidtSupplierProduct($supplierProduct,$userId);
            }
    
            /*
             * 1、更新po单主信息记录
            */
            $paramsPo['po_update_time']=$date;
            if(!self::update($paramsPo, $poCode,'po_code')){
                throw new Exception('更新PO单主信息失败！');
            }
            /*
             * 2、更新明细
            */
            $objProduct = new Service_PurchaseOrderProduct();

            //循环更新明细信息
            foreach ($paramsProduct as $key=>$val){
                //获取明细主键
                $paramProId = $val['pop_id'];
                if (!empty($val['pop_id'])) {
                    unset($val['pop_id']);
                }
                 
                //更新明细
                if(!$objProduct->update($val, $paramProId)){
                    throw new Exception('更新PO单明细信息失败！');
                }
                 
               
            }
    
            /*
             * 4、记录日志
            */
            $rowLog[] = array(
                    "pol_ref_no"=>$poCode,
                    "pol_aciton_content"=>"交货确认",
                    "pol_action_operator"=>$userId,
                    "pol_action_date"=>$date,
            );
            foreach ($rowLog as $logKey=>$logVal){
                Service_PurchaseOrdersLog::add($logVal);
            }
            $db->commit();
            $return = array(
                    'state' => 1,
                    'message'=>array('Success.'),
                    'errorMessage' => ''
            );
             
            return $return;
        } catch (Exception $e) {
            $db->rollBack();
            $return = array(
                    'state' => 0,
                    'poCode' => $poCode,
                    'message'=>array('Fail.'),
                    'errorMessage' => $e->getMessage()
            );
            return $return;
        }
    }
    
    /**
     * 修改采购单信息业务method
     * @param unknown_type $paramsPo
     * @param unknown_type $paramsProduct
     * @throws Exception
     * @return multitype:number string Ambigous <multitype:, multitype:string > |multitype:number string multitype:string  |multitype:number multitype:string  NULL
     */
    public static function editPoAndDetail($paramsPo = array(),$paramsProduct=array(),$poCode,$supplierProduct=array(),$purchaseInventory=array(),$rowLog= array(),$warehouseInventory = array()){
         
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try {
    
            //当前操作用户
            $userId = Service_User::getUserId();
            //获取主键，销毁array元素，为了防止更新主键
            $paramPoId = $paramsPo['po_id'];
            if (!empty($paramsPo['po_id'])) {
                unset($paramsPo['po_id']);
            }
    
            //update
            $paramsPo['po_update_time']=date('Y-m-d H:i:s');
            if(!self::update($paramsPo, $paramPoId)){
                throw new Exception('更新PO单主信息失败！');
            }
            $objProduct = new Service_PurchaseOrderProduct();
            //循环更新明细信息
            foreach ($paramsProduct as $key=>$val){
                //获取明细主键
                $paramProId = $val['pop_id'];
                if (!empty($val['pop_id'])) {
                    unset($val['pop_id']);
                }
    
                //更新明细
                //update
                $objProduct->update($val, $paramProId);
            }
    
            /*
             * 单价如果发生变化则需要改supplier_product 添加：supplier_product_log
            */
            if(!empty($supplierProduct)){
                self::eidtSupplierProduct($supplierProduct,$userId);
            }
    
    
            /*
             *如果修改仓库,需要先移动计划库存
            */
            if(!empty($warehouseInventory)){
                foreach ($warehouseInventory["product"] as $key_temp=>$val_temp){
                    //减去原仓库计划库存
                    $inven_temp = Service_PurchaseInventory::getByCondition(
                            array(
                                    "warehouse_id"=>$warehouseInventory["original"],
                                    "pi_product_id"=>$val_temp["product_id"])
                    );
                    $createCount = $inven_temp[0]["qty_create"]-$val_temp["qty_expected"];
    
                    Service_PurchaseInventory::update(array('qty_create'=>$createCount), $inven_temp[0]["pi_id"]);
    
                    //累加现仓库计划库存
                    $inven_temp_ = Service_PurchaseInventory::getByCondition(
                            array(
                                    "warehouse_id"=>$warehouseInventory["now"],
                                    "pi_product_id"=>$val_temp["product_id"])
                    );
                    if(empty($inven_temp_)){
                        $purchaseInventoryInsert = array(
                        //     								"qty_plan"=>,
                                "qty_create"=>$val_temp["qty_expected"],
                                //     								"qty_release"=>,
                                "pi_sku"=>$val_temp["product_barcode"],
                                "pi_product_id"=>$val_temp["product_id"],
                                "warehouse_id"=>$warehouseInventory["now"],
                        );
                        	
                        Service_PurchaseInventory::add($purchaseInventoryInsert);
                    }else {
                        $createCount_ = $inven_temp_[0]["qty_create"]+$val_temp["qty_expected"];
                        Service_PurchaseInventory::update(array('qty_create'=>$createCount_), $inven_temp_[0]["pi_id"]);
                    }
    
                }
            }
    
    
            /*
             * 预期数量如果发生变化，则需要修改计划库存数量
            */
            if(!empty($purchaseInventory)){
                foreach($purchaseInventory as $inKey=>$inVal){
                    $pro_temp = Service_PurchaseOrderProduct::getByField($paramProId,"pop_id",array("product_id","qty_expected","qty_eta"));
    
                    $updateWhere = array("warehouse_id"=>$inVal["warehouse_id"],"pi_product_id"=>$inVal["product_id"]);
                    $rows = Service_PurchaseInventory::getByCondition($updateWhere);
                    $qty_create = $rows[0]["qty_create"] + $inVal["count"];
                    $updateFiled = array("qty_create"=>$qty_create);
                    	
                    Service_PurchaseInventory::updateQty($updateFiled, $updateWhere);
                }
            }
    
            $date = date('Y-m-d H:i:s');
            //日志记录
            $rowLog[] = array(
                    "pol_ref_no"=>$poCode,
                    "pol_aciton_content"=>"修改采购单",
                    "pol_action_operator"=>$userId,
                    "pol_action_date"=>$date,
            );
            foreach ($rowLog as $logKey=>$logVal){
                Service_PurchaseOrdersLog::add($logVal);
            }
    
            $db->commit();
            $return = array(
                    'state' => 1,
                    'message'=>array('Success.'),
                    'errorMessage' => ''
            );
    
            return $return;
        } catch (Exception $e) {
            $db->rollBack();
            $return = array(
                    'state' => 0,
                    'message'=>array('Fail.'),
                    'errorMessage' => $e->getMessage()
            );
            return $return;
        }
         
         
    }
    
    /**
     * 审批PO单业务核心方法
     * @param unknown_type $paramsPo
     * @throws Exception
     * @return multitype:number string multitype:string  |multitype:number multitype:string  NULL
     */
    public static function eximainStatus($paramsPo = array(),$customerId = 1,$customerCode = "EC001"){
         
        $return = array();
        $userId = Service_User::getUserId();
        $date = date('Y-m-d H:i:s');
        
        //循环更新状态
        foreach ($paramsPo as $key=>$val){
            $db = Common_Common::getAdapter();
            $db->beginTransaction();
            try {
                //获取明细主键
                $paramPoId = $val['po_id'];
                if (!empty($val['po_id'])) {
                    unset($val['po_id']);
                }
                	
                //查询PO头信息
                $poHead = Service_PurchaseOrders::getByField($paramPoId);
    
                if($poHead["po_status"] != 2){
                    throw new Exception('采购单'.$val["po_code"].'状态不是“交货确认”');
                }
    
                if(empty($poHead)){
                    throw new Exception('采购单'.$val["po_code"].'不存在');
                }
                //收货单
                $receiving = array(
                        'reference_no' => $poHead['ref_no'],
                        'warehouse_id' => "",
                        'supplier_id' => $poHead['suppiler_id'],
                        'customer_id' => $customerId,
                        'customer_code' => $customerCode,
                        'to_warehouse_id'=>"",
                        'receiving_type' => "",
                        'receiving_status' => 5,
                        'expected_date' => $poHead['date_eta'],
                        'po_code'=>$poHead['po_code'],
                );
    
                if($poHead['to_warehouse_id'] == "0" || $poHead['to_warehouse_id'] == "null" || empty($poHead['to_warehouse_id'])){
                    $receiving["warehouse_id"] = $poHead['warehouse_id'];
                    $receiving["to_warehouse_id"] = "0";
                    $receiving["receiving_type"] = "4";
                }else{
                    $receiving["warehouse_id"] = $poHead['to_warehouse_id'];
                    $receiving["to_warehouse_id"] = $poHead['warehouse_id'];
                    $receiving["receiving_type"] = "3";
                }
                //收货单明细
                $itemArr = $receivingDetailCostArr = array();
    
                //查询审批的PO单下所有商品的数量
                $con = array('po_id'=>$paramPoId);
                $poProduct = Service_PurchaseOrderProduct::getByCondition($con);
                
                /*
                 * update单头信息
                */
                if(!self::update($val, $paramPoId)){
                    throw new Exception('采购单'.$val["po_code"].'更新状态为“已审批”失败！');
                }
    
                if(empty($poProduct)){
                    throw new Exception('采购单'.$val["po_code"].'产品信息异常，无法获取到该单产品信息！');
                }
                foreach($poProduct as $ky=>$vl){
    
                   
                    /*
                     * 构建收货item
                    */
                    $pro = new Service_Product();
                    $productTemp_ = $pro->getByField($vl['product_id'],"product_id","product_barcode");
                    //入库数量要大于0
                    if($vl['qty_eta']>0){
                        //判断是否需要拍照
                        $conPh = 0;
                        
                        	
                        //判断是否需要填写描述属性
                        $conDes = 0;
                        
                        	
                        $itemArr[$vl['product_id']]= array(
                                'product_id' => $vl['product_id'],
                                'product_barcode' => $productTemp_["product_barcode"],
                                'rd_receiving_qty' => $vl['qty_eta'],
                                "is_need_dev_photo"=>$conPh,
                                "is_need_dev_desc"=>$conDes,
                        );
                        //成本
                        $currencyRow = Service_Currency::getByField($vl['currency_code'], 'currency_code', array('currency_rate'));
                        $currencyRate = isset($currencyRow['currency_rate']) ? $currencyRow['currency_rate'] : 0;
                        $receivingDetailCostArr[$vl['product_id']] = array(
                                'product_id' => $vl['product_id'],
                                'product_barcode' => $productTemp_["product_barcode"],
                                'quantity' => $vl['qty_eta'],
                                'po_code' => $poHead['po_code'],
                                'supplier_id' => $poHead['suppiler_id'],//注意字段
                                "unit_price" => $vl['unit_price'],
                                "currency_code" => $vl['currency_code'],
                                "currency_rate" => $currencyRate,
                        );
                    }
    
                    /*
                     * 更新目的仓库库存表,计划库存
                    *
                    */
                    if($poHead["to_warehouse_id"] != 0 && $poHead["to_warehouse_id"] != $poHead["warehouse_id"]){
                        $row = array(
                                'product_id' => $vl['product_id'],
                                'quantity' => $vl["qty_eta"],
                                'customQty' => 0, //用于其它
                                'operationType' => 16,
                                'unsellable' => 0,
                                'warehouse_id' => $poHead["warehouse_id"],
                                'reference_code' => $poHead["po_code"], //操作单号
                                'application_code' => 'Purchase', //操作类型
                                'note' => ''
                        );
                        $obj = new Service_ProductInventoryProcess();
                        $result = $obj->update($row);
                        if (!isset($result['state']) || $result['state'] != '1') {
                            throw new Exception('Inventory Internal error');
                        }
                    }
                     
                }
    
                /*
                 * 创建ASN
                */
                $objAsn = new Service_ReceivingProcess();
                $returnAsn = $objAsn->createAsn($receiving, $itemArr, $receivingDetailCostArr);
                if (!is_array($returnAsn) || !isset($returnAsn['receiving_code'])) {
                    throw new Exception('采购单'.$val["po_code"].'创建入库单失败');
                }
                /*
                 * 更新ASN号
                 */
                $updateRow = array('receiving_code'=>$returnAsn['receiving_code']);
                Service_PurchaseOrders::update($updateRow, $paramPoId);                
                	
                //日志记录
                $rowLog = array(
                        "pol_ref_no"=>$val["po_code"],
                        "pol_aciton_content"=>"审批采购单",
                        "pol_action_operator"=>$userId,
                        "pol_action_date"=>$date,
                );
                $log = new Service_PurchaseOrdersLog();
                $log->add($rowLog);
                $db->commit();
                $return[] = array(
                        'state' => 1,
                        'message'=>array('Success.'),
                        'errorMessage' => ''
                );
            } catch (Exception $e) {
                $db->rollBack();
                $return[] = array(
                        'state' => 0,
                        'message'=>array('Fail.'),
                        'errorMessage' => $e->getMessage()
                );
            }
             
        }
         
        return $return;
    }
    
    /**
     * 修改服务商商品单价
     * @param unknown_type $supplierProduct
     * @param unknown_type $userId
     */
    public static function eidtSupplierProduct($supplierProduct = array(),$userId = "1"){
        $date = date('Y-m-d H:i:s');
        //循环单价有变化的产品信息，进行修改和添加操作
        foreach($supplierProduct as $supKey=>$supVal){
             
            //获取服务商产品信息
            $supplierProduct_temp = Service_SupplierProduct::getByCondition(
                    array("sp_supplier_product_code"=>$supVal["sp_supplier_product_code"],
                            "supplier_id"=>$supVal["supplier_id"])
            );
    
            if(empty($supplierProduct_temp)){
                throw new Exception("保存服务商产品单价时，未在采购单对应的供应商下 找到该产品：'".$supVal["sp_supplier_product_code"]."'的记录");
            }
             
            //更新产品信息supplier_product
            Service_SupplierProduct::update(
            array("sp_unit_price"=>$supVal["sp_last_price"],
            "sp_last_price"=>$supVal["sp_last_price"],
            "updater_id"=>$userId,"sp_update_time"=>$date),
            $supplierProduct_temp[0]["sp_id"]
            );
             
            //添加产品日志信息supplier_product_log
            $supplierProductLog = $supplierProduct_temp[0];
            $supplierProductLog["sp_unit_price"] = $supVal["sp_last_price"];
            $supplierProductLog["sp_last_price"] = $supVal["sp_last_price"];
            $supplierProductLog["creater_id"] = $userId;
            $supplierProductLog["sp_add_time"] = $date;
            $supplierProductLog["sp_update_time"] = $date;
             
            unset($supplierProductLog['sp_id']);
             
            Service_SupplierProductLog::add($supplierProductLog);
        }
    }
    /**
     * 组装采购单据数据
     * @param unknown_type $key po_id
     * @param unknown_type $val po_code
     */
    public static function getPurchaseOrdersInfo_Param($key,$val){
        //采购单据数据
        $purchaseOrders = array();
    
        /*
         * 1、组装采购单据头部信息
        * 采购单号、供应商代码、供应商地址，时间
        */
        $date = date('Y-m-d H:i:s');
    
        //获取服务商数据 服务商代码、服务商地址
        $showHead = Service_PurchaseOrders::getByFieldShowHead($key,"po_id",array("po_id","warehouse_id"));
        //采购单号
        $purchaseOrders["po_code"] = $val;
    
        //服务商代码
        $purchaseOrders["supplier_code"] = $showHead["supplier_code"];
    
        //服务商地址
        $purchaseOrders["contact_address"] = $showHead["contact_address"];
        //打印日期
        $purchaseOrders["print_date"] = $date;
        /*
         * 2、组装明细信息
        * SKU、商品名称、商品数量、单价
        */
        $showDetail = Service_PurchaseOrders::getByFieldShowDetail($key,"purchase_orders.po_id","purchase_orders.po_id");
        //产品数量
        $purchaseOrders["total"] = count($showDetail);
    
        foreach($showDetail as $key=>$val){
            if($val["plan_type"] == 3){
                //$wearhouse,$sku
                $outDate = Service_PurchaseOrders::getOutStock($showHead["warehouse_id"],$val["product_sku"]);
                $showDetail[$key]["outDate"]= $outDate["outDate"];
            }else{
                $showDetail[$key]["outDate"]='';
            }
             
        }
        $purchaseOrders["detail"] = $showDetail;
        return $purchaseOrders;
    }
     
    
    /**
     * @param array $params
     * @return array
     */
    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'po_id',
              'E1'=>'po_code',
              'E2'=>'create_type',
              'E3'=>'warehouse_id',
              'E4'=>'shipping_method_id_head',
              'E5'=>'tracking_no',
              'E6'=>'ref_no',
              'E7'=>'supplier_id',
              'E8'=>'payable_amount',
              'E9'=>'actually_amount',
              'E10'=>'currency_code',
              'E11'=>'pay_status',
              'E12'=>'po_status',
              'E13'=>'po_type',
              'E14'=>'date_release',
              'E15'=>'date_create',
              'E16'=>'operator_create',
              'E17'=>'operator_release',
              'E18'=>'operator_purchase',
              'E19'=>'date_eta',
              'E20'=>'po_update_time',
              'E21'=>'to_warehouse_id',
              'E22'=>'pay_ship_amount',
              'E23'=>'receiving_exception',
              'E24'=>'receiving_exception_handle',
              'E25'=>'currency_rate',
        );
        return $row;
    }

}