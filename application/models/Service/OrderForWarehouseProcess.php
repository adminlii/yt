<?php
class Service_OrderForWarehouseProcess
{
    private $_client = null;
    private $_domain = '';
    private $_abnormal = 1;
    private $_customerCode = 'EC001';
    private $_orderStatus = '4';

    /**
     * @desc 设置Client
     */
    private function setClient()
    {
        $wms_db = Zend_Registry::get('wms_db');
        $db = Common_Common::getAdapter();
        $sql = "select * from {$wms_db}.user_system where us_code='WMS';";
        $rs = $db->fetchRow($sql);
        if(!$rs){
            throw new Exception("{$wms_db}.user_system error");
        }
//         $config = Zend_Registry::get('config')->toArray();
        $this->_domain = trim($rs['us_url'],'/'). "/default/rest/service"; 
//         echo $this->_domain;exit;
        $this->_client = new Zend_Rest_Client ($this->_domain );
        $this->_client->getHttpClient()->setConfig(array('keepalive'=>true,'timeout'=>6000));//设定超时 
    }

    /**
     * @发送订单到仓库
     * @param string $orderCode
     * $param boolean $force 是否异常同步订单可重复尝试发送
     * @return array state
     */
    public function submit($orderCode = '',$force=true)
    {
        $date = date('Y-m-d H:i:s');
        $result = array('state' => 0, 'message' => array(), 'refrence_no_warehouse' => '', 'orderCode' => $orderCode, 'orderStatus' => '');
        try {
            $orderRow = Service_Orders::getByField($orderCode, 'refrence_no_platform');
            if (empty($orderRow)) {
                throw new Exception('订单号:' . $orderCode . '不存在.');
            }
            if (in_array($orderRow['order_status'] ,array('3','4'))) {
                throw new Exception('订单已经到达仓库');
            }
            if($force){
                if ($orderRow['sync_status'] != '0'&& $orderRow['sync_status'] != '2') {
                    throw new Exception('订单同步状态不是“未同步”.');
                }
            }else{
                if ($orderRow['sync_status'] != '0') {
                    throw new Exception('订单同步状态不是“未同步”.');
                }
            }
           
            $return = $this->sendWarehouse($orderRow);
            if (isset($return['state']) && $return['state'] == '1') {
                $result['state'] = 1;
                $result['refrence_no_warehouse'] = $return['orderCode'];
                $result['orderStatus'] = $return['orderStatus'];
                $update = array(
                    'sync_time' => $date,
                    'date_last_modify' => $date,
                    'order_status' => '3',
                    'sync_status' => 1,
                    'refrence_no_warehouse' => isset($return['orderCode']) ? $return['orderCode'] : ''
                );
                $log = array(
                    'ref_id' => $orderCode,
                    'create_time' => $date,
                    'log_content' => '已同步到仓库,仓库单号:' . (isset($return['orderCode']) ? $return['orderCode'] : ''),
                );
            } else { 
                
                $update = array(
                    'date_last_modify' => $date,
                    'sync_status' => 2
                );
                $log = array(
                    'ref_id' => $orderCode,
                    'create_time' => $date,
                    'log_content' => isset($return['message']) ? join(',', $return['message']) : '调用API失败',
                );
                $result['message'] = isset($return['message']) ? $return['message'] : array();
//                 print_r($return);exit;
                if($return['err_code']=='50000'){
                    throw new Exception('调用API失败','50000');
                }
            }
            Service_Orders::update($update, $orderCode, 'refrence_no_platform');
            Service_OrderLog::add($log);
        } catch (Exception $e) {
            $result['message'][] = $e->getMessage();
            $result['err_code'] = $e->getCode();
        }

        return $result;
    }

    /**
     * @desc 通过Rest同步订单到仓库
     * @param array $orderRow
     * @return array|mixed
     * @throws Exception
     */
    public function sendWarehouse($orderRow = array())
    {
        $orderCode = $orderRow['refrence_no_platform'];
        $result = array('state' => 0, 'message' => array(), 'referenceNo' => $orderCode, 'orderCode' => '', 'orderStatus' => '');
        try {
            $this->setClient();
            $whRow = Service_Warehouse::getByField($orderRow['warehouse_id']);
            if (empty($whRow)) {
                throw new Exception('仓库ID:' . $orderRow['warehouse_id'] . '不存在.');
            }
            $addressRow = Service_ShippingAddress::getByField($orderCode, 'OrderID');
            if (empty($addressRow)) {
                throw new Exception('地址信息不存在.');
            }
            $customerCode = $this->_customerCode;
            if ($orderRow['customer_id'] != '') {
                //调用WMS客户表
                $customerObj = new Table_Customer();
                $customerRow = $customerObj->getByField($orderRow['customer_id'], 'customer_id', array('customer_id', 'customer_code'));
                if (empty($customerRow)) {
                    throw new Exception('客户不存在.');
                }
                $customerCode = $customerRow['customer_code'];
            }
            $params = array(
                'CustomerCode' => $customerCode,
                'OrderStatus' => $this->_orderStatus,
                'ReferenceNo' => $orderCode,
                'ShippingMethod' => $orderRow['shipping_method'],
                'parcelContents' => '',
                'parcelDeclaredValue' => '',
                'Country' => $addressRow['Country'],
                'FirstName' => $addressRow['Name'],
                // 'LastName' =>'',
                'WarehouseCode' => $whRow['warehouse_code'],
                'Address1' => $addressRow['Street1'],
                'Address2' => $addressRow['Street2'],
                'City' => $addressRow['CityName'],
                'State/Provice' => $addressRow['StateOrProvince'],
                'Postalcode' => $addressRow['PostalCode'],
                'Email' => '',
                'Company' => '',
                'PhoneNo' => $addressRow['Phone'],
                'remark' => $orderRow['order_desc']
            );

            $orderProductRows = Service_OrderProduct::getByCondition(array('order_id' => $orderRow['order_id']), '*');
            if (empty($orderProductRows)) {
                throw new Exception('订单产品不能为空.');
            }
            foreach ($orderProductRows as $key => $product) {
                $conn = array('product_sku'=>$product['product_sku']);
                $combRows = Service_ProductCombineRelationProcess::getRelation($product['product_sku'],$orderRow['user_account']);
                
                if($combRows){
                    foreach($combRows as $row){//组合产品
                        if(isset($params['orderProduct'][$row['pcr_product_sku']])){
                            $params['orderProduct'][$row['pcr_product_sku']]['quantity'] += $product['op_quantity']*$row['pcr_quantity'];
                        }else{
                            $params['orderProduct'][$row['pcr_product_sku']] = array(
                                    'sku' => $row['pcr_product_sku'],
                                    'quantity' => $product['op_quantity']*$row['pcr_quantity'],
                                    'refTnx' => $product['op_ref_tnx'],
                                    'refItemId' => $product['op_ref_item_id'],
                                    'refBuyerId' => $product['op_ref_buyer_id'],
                                    'refPayDate' => $product['op_ref_paydate'],
                            );
                        }
                        
                    }
                    
                }else{
                    if(isset($params['orderProduct'][$product['product_sku']])){
                        $params['orderProduct'][$product['product_sku']] +=  $product['op_quantity'];
                    }else{
                        $params['orderProduct'][$product['product_sku']] = array(
                                'sku' => $product['product_sku'],
                                'quantity' => $product['op_quantity'],
                                'refTnx' => $product['op_ref_tnx'],
                                'refItemId' => $product['op_ref_item_id'],
                                'refBuyerId' => $product['op_ref_buyer_id'],
                                'refPayDate' => $product['op_ref_paydate'],
                        );
                    }
                    
                }
                
            }
//             print_r($params);exit;
            $params = serialize($params);
            $params = Common_Common::authcode($params, 'CODE');

//             $params = Common_Common::authcode($params, 'DECODE');
//             $params = unserialize($params);
//                         print_r($params);exit;
            try{
                $return = $this->_client->createOrder($params, $this->_abnormal)->post();                
            }catch(Exception $eee){
                throw new Exception($eee->getMessage(),'50000');
            }
//                         print_r('dfdfd');exit;
            if ($return->status == 'success') {
                $return = $return->getIterator()->createOrder;
                $result = Common_Common::objectToArray($return);
            } else {
                throw new Exception('API Internal error.','50000');
            }
        }catch (Exception $e) {
            $result['message'][] = $e->getMessage();
            $result['err_code'] = $e->getCode();
        }
        return $result;
    }

    /**
     * @截单操作
     * @param string $orderCode
     * @return array
     * @throws Exception
     */
    public function stopOrder($orderCode = '')
    {
        $date = date('Y-m-d H:i:s');
        $result = array('state' => 0, 'message' => '', 'orderCode' => $orderCode);
        try {
            $orderRow = Service_Orders::getByField($orderCode, 'refrence_no_platform');
            if (empty($orderRow)) {
                throw new Exception('订单号:' . $orderCode . '不存在.');
            }
            $warehouseOrderCode = $orderRow['refrence_no_warehouse'];
            if (!empty($warehouseOrderCode)) {
                $customerCode = $this->_customerCode;
                if ($orderRow['customer_id'] != '') {
                    //调用WMS客户表
                    $customerObj = new Table_Customer();
                    $customerRow = $customerObj->getByField($orderRow['customer_id'], 'customer_id', array('customer_id', 'customer_code'));
                    if (empty($customerRow)) {
                        throw new Exception('客户不存在.');
                    }
                    $customerCode = $customerRow['customer_code'];
                }
                $return = $this->stopWarehouseOrder($warehouseOrderCode,$customerCode);
                if (!isset($return['state']) || $return['state'] != '1') {
                    $log = array(
                        'ref_id' => $orderCode,
                        'create_time' => $date,
                        'log_content' => Ec::Lang('cancel_order_failure_reasons','auto') . (isset($return['message']) ? $return['message'] : Ec::Lang('interface_calls_fails','auto')),//'截单失败:' XXX OR '调用API失败'  
                    );
                    Service_OrderLog::add($log);
                    throw new Exception(Ec::Lang('oo_warehouse_intercept_failure','auto') . '：' . $return['message']);//仓库截单失败:
                }
            }
            $update = array(
                'order_status' => '0',
                'date_last_modify' => $date,
            );
            if (Service_Orders::update($update, $orderCode, 'refrence_no_platform')) {
                $log = array(
                    'ref_id' => $orderCode,
                    'create_time' => $date,
                    'log_content' => Ec::Lang('oo_intercept_success','auto'),//'截单成功.'
                );
                Service_OrderLog::add($log);
            }
            //'截单成功'
            $result = array('state' => 1, 'message' => Ec::Lang('oo_intercept_success','auto'), 'orderCode' => $orderCode);
            
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @desc 仓库截单
     * @param $warehouseOrderCode,$customerCode
     * @return array state message=string
     */
    public function stopWarehouseOrder($warehouseOrderCode = '', $customerCode = '',$reason='')
    {
    	$reason = (empty($reason))?Ec::Lang('oo_intercept','auto'):$reason;
        $result = array('state' => 0, 'message' => '');
        try {
            $this->setClient();
            $params = array(
                'CustomerCode' => $customerCode,
                'OrderCode' => $warehouseOrderCode,
                'Reason'=>$reason,
            );
            $params = serialize($params);
            $params = Common_Common::authcode($params, 'CODE');
            try{
                $return = $this->_client->stopOrder($params)->post();
            }catch(Exception $eee){
                throw new Exception($eee->getMessage(),'50000');
            }
            
            if ($return->status == 'success') {
                $return = $return->getIterator()->stopOrder;               
                $return = Common_Common::objectToArray($return);
                if (isset($return['state']) && $return['state'] == '1') {
                    $result['state'] = 1;
                    $result['message'] = Ec::Lang('oo_intercept_success','auto');//'截单成功';
                } else {
                    $result['message'] = isset($return['message']) ? $return['message'] : 'API Internal error.1';
                }
            } else {
                throw new Exception('API Internal error.');
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $result['err_code'] = $e->getCode();
        }
        return $result;
    }

}