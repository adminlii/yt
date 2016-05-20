<?php
class Common_ServiceForWms3
{

    protected $_token = null;

    protected $_key = null;

    /**
     * 日志
     *
     * @param unknown_type $error            
     */
    private function log($error)
    {
        $logger = new Zend_Log();
        $uploadDir = APPLICATION_PATH . "/../data/log/";
        $writer = new Zend_Log_Writer_Stream($uploadDir . 'api.log');
        $logger->addWriter($writer);
        $logger->info(date('Y-m-d H:i:s') . ': ' . $error . " \n");
    }

    /**
     * 数据初始化验证
     *
     * @param unknown_type $req            
     * @throws Exception
     */
    private function init($req)
    {
        if(empty($req['appToken'])){
            throw new Exception('appToken 不能为空');
        }
        if(empty($req['appKey'])){
            throw new Exception('appKey 不能为空');
        }
        if(empty($req['paramsJson'])){
            // throw new Exception('paramsJson 不能为空');
        }
        if(empty($req['service'])){
            throw new Exception('service 不能为空');
        }
        /**
         * 判断系统是否支持方法
         */
        if(! method_exists($this, $req['service'])){
            throw new Exception('系统不支持方法' . $req['service']);
        }
        
        $this->_token = $req['appToken'];
        $this->_key = $req['appKey'];
    }

    /**
     * 接口入口===========================================================================================
     *
     * @param string $req            
     * @return string
     */
    public function callService($req)
    {
        Ec::showError(print_r($req,true),'__notify');
        try{
            // 对象转数组
            $req = Common_Common::objectToArray($req);
            $this->init($req);
            $service = $req['service'];
            if(isset($req['paramsJson'])){
                $params = Zend_Json::decode($req['paramsJson']);
                Ec::showError(print_r($params,true),'__notify_param');
                $return = $this->$service($params);
            }else{
                $return = $this->$service();
            }
        }catch(Exception $e){
            $return = array(
                'ask' => 'Failure',
                'message' => $e->getMessage()
            );
        }
        $return['date'] = date('Y-m-d H:i:s');
        $return = array(
            'response' => Zend_Json::encode($return)
        );
        return $return;
    }


    /**
     * WMS通知OMS接口
     * 如更新订单
     * @param unknown_type $param
     */
    public function notify($param)
    {
        Ec::showError(print_r($param,true),'__notify');
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            if(empty($param['app_code'])){ // 调用方法
                throw new Exception(Ec::Lang('param_error', 'method'));
            }
            $wmsService = new Common_ThirdPartWmsAPI();
            $wmsProcess = new Common_ThirdPartWmsAPIProcess();
            // 转为小写
            $app_code = strtolower($param['app_code']);
            $refer_no = $param['refer_no'];
            $action = $param['action'];
           
            switch($app_code){
                case 'orders':
                    // 操作，下架pickup，出库ship，计费cal_fee
                    $action = $param['action'];
                    //
                    $rs = $wmsProcess->syncOrder($refer_no);
                    break;
                case 'order_track':                    
                    $rs = $wmsProcess->syncOrderTrack($refer_no);
                    break;
                
                case 'receiving':
                    // 操作,收货receipt，质检qc，上架putaway,完成
                    $action = $param['action'];
                    $wmsProcess->syncReceiving($refer_no);
                    break;

                case 'inventory':  
                    foreach($param['product_sku_arr'] as $product_sku){
                        $invParam = array(
                                'customer_code' => $param['customer_code'],
                                'warehouse_code' => $param['warehouse_code'],
                                'product_sku' => $product_sku,
                        );
                        $wmsProcess->syncInventory($invParam);
                    }
                    break;
                case 'all-inventory':
                    $customers = Service_Company::getByCondition();
                    foreach($customers as $p){
                        $customer_code = $p['company_code'];
                        $invParam = array(
                                'customer_code' => $customer_code,
                                'warehouse_code' => '',
                                'product_sku' => '',
                        );
                        $subrs = $wmsProcess->syncInventory($invParam);
                    }
                    
                    break;
                case 'country':
                    $rs = $wmsProcess->syncCountry();
                    break;
                
                case 'warehouse':
                    $rs = $wmsProcess->syncWarehouse();
                    
                    break;
                case 'product_category':
                    $rs = $wmsProcess->syncCategory();
                    break;
                case 'shipping_method':
                    $rs = $wmsProcess->syncWarehouseShipment();
                    break;
                case 'product_qc_options':
                    $rs = $wmsProcess->syncQcOption();
                    break;
                case 'product_uom':
                    $rs = $wmsProcess->syncProductUom();
                    break;
                case 'fee_type':
                    $rs = $wmsProcess->syncFeeType();
                    break;
                case 'order_operation_type':
                    $rs = $wmsProcess->syncOrderOperationType();
                    break;
                case 'receiving_area_map':
                    $rs = $wmsProcess->syncReceivingArea();
                    break;
                case 'return_orders':
                    $rs = $wmsProcess->syncReturnOrder($refer_no);
                    break;

                case 'receiving_abnormal':
                    $rs = $wmsProcess->syncReceivingAbnormal($refer_no);
                    break;
                    
                case 'cancel_order':
                    //截单
                    if(strtolower($action)=='success'){
                        $row = array(
                                'order_status'=>'0',
                                'cancel_status' => '2',
                                'date_last_modify' => date('Y-m-d H:i:s')
                        );
                        Service_Orders::update($row, $refer_no,'refrence_no_platform');
                        $logRow = array(
                                'ref_id' => $refer_no,
                                'log_content' => '截单成功',
                                'create_time' => date('Y-m-d H:i:s'),
                                'system' => 'oms',
                                'op_id'=>'0',
                                'op_user_id'=>'0'
                        );
                        Service_OrderLog::add($logRow);
                        
                    }else{
                        $row = array(
                                'cancel_status' => '3',
                                'date_last_modify' => date('Y-m-d H:i:s')
                        );
                        $shipStatusArr = array(
                                '8',
                                '9',
                                '10',
                                '11',
                                '12',
                                '13'
                        );
                        //订单已经出库
                        if(in_array($param['order_status'], $shipStatusArr)){
                            $row['order_status'] = '4';
                        }
                        Service_Orders::update($row, $refer_no, 'refrence_no_platform');
                        $logRow = array(
                                'ref_id' => $refer_no,
                                'log_content' => '截单失败,'.$param['message'],
                                'create_time' => date('Y-m-d H:i:s'),
                                'system' => 'oms',
                                'op_id' => '0',
                                'op_user_id' => '0'
                        );
                        Service_OrderLog::add($logRow);
                        
                    }
                    break;
                case 'company':
                    $return = $wmsProcess->syncCompany($refer_no);
                    break;

                case 'product':
                    $rs = $wmsProcess->syncProduct($refer_no);
                    break;
				case 'product_weight_update':
                //ruston 0919 质检重量输入错误临时修改程序
                    $return = $wmsProcess->productWeightUpdate($refer_no,$param['weight']);
                    break;
                case 'add_post_code':
                //ruston 0919 临时添加邮编
                    $return = $wmsProcess->addPostCode($refer_no,$param['zone_code']);
                    break;  
                case 'transfer-orders':
                    $rs = $wmsProcess->syncTransferOrder($refer_no, $action);
                    break;  
                case 'value_added_type': // 增值服务
                    $rs = $wmsProcess->syncValueAddedType();
                    break;
                default:
                    throw new Exception(Ec::Lang('param_error', 'method'));
            }
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
    
        return $return;
    }
    
    /**
     * API创建退货入库单
     * @param unknown_type $param
     */
    public function createReturnAsn($param){
        
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        $asnParam = $param['asn'];
        $products = $param['items'];
        $orderR = array(
                'receiving_code'=>Common_GetNumbers::getCode('CURRENT_ASN_COUNT', $asnParam['customer_code'], 'RV'), // 入库单号
                'company_code' => $asnParam['customer_code'],
                'reference_no' => $asnParam ['refrence_no'],//订单号or退货单号
                // 'warehouse_id' => $params['warehouse_id'],
                'warehouse_code' => $asnParam ['warehouse_code'],//订单对应的仓库
                // 'transit_warehouse_id' =>
                // $params['transit_warehouse_id'],
                'receiving_status' => $asnParam ['receiving_status'],//状态:0删除,1草稿,2确认,3待审核,4审核,5在途,6收货中,7收货完成
                'transit_warehouse_code' => isset($asnParam ['transit_warehouse_code'])?$asnParam ['transit_warehouse_code']:$asnParam ['warehouse_code'],//订单对应的仓库
                
                'receiving_type' => isset($asnParam ['receiving_type'])?$asnParam ['receiving_type']:'1',//收货类型：0:标准;1:订单退货;2:指定产品退件,3:中转,4:采购
                'income_type' => isset($asnParam ['income_type'])?$asnParam ['income_type']:'0',//交货方式，0：自送，1：揽收，揽收需要提供揽收地址，地址参考淘宝，默认为自送
                
                'shipping_method' => $asnParam ['shipping_method'],//无效，无需传递
                'tracking_number' => $asnParam ['tracking_no'],//无效，无需传递
                'contacter' => $asnParam ['contacter'],//无效，无需传递
                'contact_phone' => $asnParam ['contact_phone'],//无效，无需传递
                'region_0' => $asnParam ['region_0'],//无效，无需传递
                'region_1' => $asnParam ['region_1'],//无效，无需传递
                'region_2' => $asnParam ['region_2'],//无效，无需传递
                'street' => $asnParam ['street'],//无效，无需传递
                'is_default'=>$asnParam['is_default'],//无效，无需传递
                'expected_date' => isset($asnParam ['eta_date'])?$asnParam ['eta_date']:date('Y-m-d'),//到达时间，可不传
                'receiving_description' => $asnParam ['receiving_desc'],
        )
        ;
        $productArr = array();
        foreach($products as  $product){
            $p = Service_Product::getByField($product['product_barcode'],'product_barcode');
            $productArr[] = array(
                    'product_id' => $p['product_id'],
                    'quantity' => $product['quantity'],
                    'box_no' => '1',
                    'package_type' => 'ow',
            );        
        }
        $row = array(
                'asn' => $orderR,
                'products' => $productArr
        );
        
        $process = new Service_ReceivingProcess();   
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $result = $process->createAsn($row);
            if(isset($asnParam['receiving_status'])){//设置了状态，则更新
                $updateRow = array('receiving_status'=>$asnParam['receiving_status']);
                Service_Receiving::update($updateRow, $orderR['receiving_code'],'receiving_code');
            }
            $db->commit();
            $return['ask'] = 'Success';
            $return['receiving_code'] = $orderR['receiving_code'];
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
       
       return $return;
    }
    
    /**
     * API创建退货入库单
     * @param unknown_type $param
     */
    public function createReturnOrder($param){
    
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        $orderParam = $param['order'];
        $products = $param['items'];
        $orderR = array(
            'refrence_no_platform' => $orderParam['order_code'],
            'refrence_no_warehouse' => $orderParam['order_code'],
            'company_code' => $orderParam['customer_code'],
            'receiving_code' => $orderParam['receiving_code'],
            'ro_code' => $orderParam['ro_code'], // ----------------------
            'warehouse_id' => $orderParam['warehouse_id'],
            'creater' => '0',
            'verifier' => '0',
            'expected_date' => date('Y-m-d'),
            'receiving_exception' => empty($orderParam['receiving_exception'])?0:$orderParam['receiving_exception'],
            'ro_is_all' => $orderParam['ro_is_all'],
    	    'ro_status'=>$orderParam['ro_status'],//状态 0:作废;1:待确认;2:待处理;3:处理完成
            'ro_type' => $orderParam['ro_type'], // 退件类型 1:物流 2:订单信息 3:其它
            'ro_status' => $orderParam['ro_status'], // 状态
                                                     // 0:作废;1:待确认;2:待处理;3:处理完成
            'ro_sync_status' => '1', // $orderParam['ro_sync_status'],
            'ro_process_type' => $orderParam['ro_process_type'], // 0，未指定 1，退件入库
                                                                 // 2，退件重发,3:销毁
            'ro_create_type' => '1', // 类型 0:主动 1:被动(只可以指定处理方式)
            'ro_desc' => $orderParam['ro_desc'],
            'ro_note' => $orderParam['ro_note'],
            'ro_add_time' => date('Y-m-d H:i:s'),
            'ro_confirm_time' => date('Y-m-d H:i:s'),
            'ro_update_time' => date('Y-m-d H:i:s'),                
        )
        ;
        $productArr = array();
        foreach($products as  $product){
            $p = Service_Product::getByField($product['product_barcode'], 'product_barcode');
            if(!$p){
                throw new Exception('product_barcode not exist-->'.$product['product_barcode']);
            }
            $productArr[] = array(
                'product_id' => $p['product_id'],
                'rop_quantity' => $product['quantity']
            );
        }
        $row = array(
            'return_orders' => $orderR,
            'return_order_product' => $productArr
        );

        $process = new Process_Return();
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            if(empty($orderParam['ro_code'])){
                throw new Exception('param ro_code missing');
            }
            $this->_createSingle($row,$orderParam['ro_code']);            
            $db->commit();
            $return['ask'] = 'Success';
            $return['ro_code'] = $orderParam['ro_code'];
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
         
        return $return;
    }

    private function _createSingle($row, $roCode){
        $process = new Process_Return();
        $row = $process->validate($row);
        $return_orders = $row['return_orders'];
        $return_order_product = $row['return_order_product'];
        //验证订单号
        $process->validateOrderCode($return_orders['refrence_no_platform'],$roCode) ;
        // 验证参考单号
        $process->validateRefrenceNo($return_orders['reference_no'], $roCode);
       
        $ro_id = Service_ReturnOrders::add($return_orders);
        foreach($return_order_product as $p){
            $p['ro_id'] = $ro_id;
            $p['exception_process_instruction'] = $return_orders['ro_process_type'];//异常处理指令 0:无(存放不良品区);1:重新上架;2:退回;3:销毁;
            Service_ReturnOrderProduct::add($p);
        }
    }
}