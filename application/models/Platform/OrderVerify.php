<?php
/**
 * 订单审核
 * @author Administrator
 *
 */
class Platform_OrderVerify
{
    private $_ref_id = '';    

    private $_order = array();

    private $_consignee = null;

    private $_shipper = null;

    private $_order_product = null;

    private $_warehouse_code = '';
    
    private $_product_code = '';
    
    private $_system = 'tms';

    private $_err = array();
    
    private $_status = 'D';
    
    private $_invoiceArr = array();
    
    public function __construct(){
        //系统是tms还是oms,
        //tms需要判断运输方式
        //oms需要判断运输方式与仓库
       	$type = Service_Config::getByField('TMS_OMS_TYPE', 'config_attribute');
        if($type){
            $this->_system = $type['config_value'];
        }
    }

    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    public function setRefId($ref_id)
    {
        $this->_ref_id = $ref_id;
    }

    public function setShipper($shipper)
    {
        $this->_shipper = $shipper;
    }

    public function setProductCode($product_code)
    {
        $this->_product_code = $product_code;
    }
    public function setWarehouseCode($warehouse_code)
    {
        $this->_warehouse_code = $warehouse_code;
    }
    
    public function _validate()
    {
        if(empty($this->_ref_id)){
            throw new Exception(Ec::Lang('订单号不可为空'));
        }else{
            $order = Service_Orders::getByField($this->_ref_id, 'refrence_no_platform');
            $this->_order = $order;
            if(empty($order)){
                throw new Exception(Ec::Lang('订单不存在'));
            }
            $allowStatus = array(
                '2',
                '5',
                '7'
            );
            if(! in_array($order['order_status'], $allowStatus)){
                throw new Exception(Ec::Lang('订单不允许审核'));
            }
            $add = Service_ShippingAddress::getByField($this->_ref_id, 'OrderID');
            if(! $add){
                throw new Exception(Ec::Lang('收件人信息不可为空'));
            }
            $consignee = array(
                'consignee_countrycode' => strtoupper($add['Country']),
                'consignee_company' => '',
                'consignee_province' => $add['StateOrProvince'],
                'consignee_name' => $add['Name'],
                'consignee_city' => $add['CityName'],
                'consignee_telephone' => $add['Phone'],
                'consignee_mobile' => '',
                'consignee_postcode' => $add['PostalCode'],
                'consignee_email' => $order['consignee_email'],
                'consignee_street' => $add['Street1'] . ' ' . $add['Street2'] . ' ' . $add['Street3'],
                'consignee_certificatetype' => '',
                'consignee_certificatecode' => '',
                'consignee_credentials_period' => ''
            );
            $this->_consignee = $consignee;
            
            $con = array(
                "OrderID" => $this->_ref_id
            );
            $order_product = Service_OrderProduct::getByCondition($con);
           
            if(empty($order_product)){
                throw new Exception(Ec::Lang('订单明细不存在'));
            }
            $this->_order_product = $order_product;
            $invoiceArr = array();
            foreach($order_product as $p){
                $rArr = Service_ProductCombineRelationProcess::getRelation($p['product_sku'], $this->_order['user_account'], $this->_order['company_code']);
                if($rArr){
                    foreach($rArr as $v){
                        $con = array(
                            'company_code' => $this->_order['company_code'],
                            'invoice_code' => $v['pcr_product_sku']
                        );
                        $invoiceInfo = Service_CsdInvoiceInfo::getByCondition($con);
                        if(empty($invoiceInfo)){
                            throw new Exception(Ec::Lang('申报信息异常'));
                        }
                        $invoiceInfo = array_pop($invoiceInfo);
                        $invoiceArr[] = array(
                            'invoice_cnname' => $invoiceInfo['invoice_cnname'],
                            'invoice_enname' => $invoiceInfo['invoice_enname'],
                            'unit_code' => 'PCE',
                        	//TODO	
                            'invoice_quantity' => $p['op_quantity'] * $v['pcr_quantity'],
                            'invoice_unitcharge' => $invoiceInfo['invoice_unitcharge'],
                            'invoice_totalcharge' => $invoiceInfo['invoice_unitcharge'] * $p['op_quantity'] * $v['pcr_quantity'],
                            'invoice_currencycode' => $this->_order['currency'],
                            'hs_code' => $invoiceInfo['hs_code'],
                            'invoice_note' => $invoiceInfo['invoice_note'],
                            'invoice_url' => $invoiceInfo['invoice_url']
                        );
                    }
                }else{
//                     $this->_err[] = Ec::Lang('平台SKU未设置申报品名映射').'-->'.$p['product_sku'];
                    $invoiceArr[] = array(
                        'invoice_cnname' => $p['product_title'],
                        'invoice_enname' => $p['product_title'],
                        'unit_code' => 'PCE',
                    	'invoice_weight'=>	$p['op_weight'],
                        'invoice_quantity' => $p['op_quantity'],
                        'invoice_unitcharge' => $p['unit_price'],
                        'invoice_totalcharge' => $p['unit_price'] * $p['op_quantity'],
                        'invoice_currencycode' => $this->_order['currency'],
                        'hs_code' => $p['product_sku'],
                        'invoice_note' =>'',
                        'invoice_url' => ''
                    );
                }
            }
            $this->_invoiceArr = $invoiceArr;
        }
        
        if($this->_order['shipping_method']){//如果订单有运输方式,取订单的运输方式
            $this->_product_code = $this->_order['shipping_method'];            
        }
        if(empty($this->_product_code)){
            $this->_err[] = Ec::Lang('运输方式不可为空');
        }
        if($this->_system=='oms'){
            if(empty($this->_warehouse_code)){
                $this->_err[] = Ec::Lang('发货仓库不可为空');
            } 
        }
        
    }

    /**
     * 审核
     */
    public function process()
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail'
        );
        $return['ref_id'] = $this->_ref_id;
        try{
            $this->_validate();
            if($this->_err){
                throw new Exception(Ec::Lang('数据不合法'));
            }
            $order = $this->_order;
       		$return['ref_id'] = $this->_order['refrence_no'];
            $orderArr = array(
                'product_code' => strtoupper($this->_product_code),
                'country_code' => strtoupper($order['consignee_country']),
//                 'shipper_hawbcode' => strtoupper($this->_ref_id),
                'shipper_hawbcode' =>'', //strtoupper($this->_order['refrence_no']), 
                'refer_hawbcode'=>strtoupper($this->_order['refrence_no']),
                'order_weight' => $order['order_weight'],
                'order_pieces' => '1',
                'buyer_id' => $order['buyer_id'],
                'order_id' => '',
                'order_create_code' => 'p',
                'customer_id' => Service_User::getCustomerId(),
                'creater_id' => Service_User::getUserId(),
                'modify_date' => date('Y-m-d H:i:s'),
                'mail_cargo_type' => '',
                'tms_id' => Service_User::getTmsId(),
            	'platform_id'=>Service_Platform::getPlatformId($this->_order['platform']),//7291
            );
            // 收件人
            $consigneeArr = $this->_consignee;
            // 发件人
            $shipperArr = $this->_shipper;
            // 申报信息(产品信息)
            $invoiceArr = array();
            $products = $this->_order_product;
//             // 可能会多个订单行,是同一个SKU====================================,找映射关系
//             foreach($products as $p){
//                 $rArr = Service_ProductCombineRelationProcess::getRelation($p['product_sku'],$this->_order['user_account'],$this->_order['company_code']);
//                 if($rArr){
//                     foreach($rArr as $v){
//                         $con = array(
//                             'company_code' => $this->_order['company_code'],
//                             'invoice_code' => $v['pcr_product_sku']
//                         );
//                         $invoiceInfo = Service_CsdInvoiceInfo::getByCondition($con);  
//                         if(empty($invoiceInfo)){
//                             throw new Exception(Ec::Lang('申报信息异常'));
//                         }  
//                         $invoiceInfo = array_pop($invoiceInfo);
//                         $invoiceArr[] = array(
//                                 'invoice_cnname' => $invoiceInfo['invoice_cnname'],
//                                 'invoice_enname' => $invoiceInfo['invoice_enname'],
//                                 'unit_code' => 'PCE',
//                                 'invoice_quantity' => $p['op_quantity']*$v['pcr_quantity'],
//                                 'invoice_unitcharge' => $invoiceInfo['invoice_unitcharge'],
//                                 'invoice_totalcharge' => $invoiceInfo['invoice_unitcharge']*$p['op_quantity']*$v['pcr_quantity'],
//                                 'invoice_currencycode' => $this->_order['currency'],
//                                 'hs_code' => $invoiceInfo['hs_code'],
//                                 'invoice_note' => $invoiceInfo['invoice_note'],
//                                 'invoice_url' => $invoiceInfo['invoice_url']
//                         );
//                     }
//                 }else{                   
//                     $invoiceArr[] = array(
//                             'invoice_cnname' => $p['product_title'],
//                             'invoice_enname' => $p['product_sku'],
//                             'unit_code' => 'PCE',
//                             'invoice_quantity' => $p['op_quantity'],
//                             'invoice_unitcharge' => $p['unit_price'],
//                             'invoice_totalcharge' => $p['unit_price'] * $p['op_quantity'],
//                             'invoice_currencycode' => $this->_order['currency'],
//                             'hs_code' => '',
//                             'invoice_note' => '',
//                             'invoice_url' => ''
//                     );
//                 }               
//             }
            $invoiceArr = $this->_invoiceArr;
            // print_r($invoiceArr);exit;
            // 额外服务,默认无
            $extraservice = array();
            
            // 创建运单
            $process = new Process_Order();
            $process->setOrder($orderArr);
            $process->setInvoice($invoiceArr);
            $process->setExtraservice($extraservice);
            $process->setShipper($shipperArr);
            $process->setConsignee($consigneeArr);
            
            // $process,草稿运单
            $this->_status = strtoupper($this->_status);
//             echo $this->_status.'ddd';exit;
            $rs = $process->createOrderTransaction($this->_status);
            
//             print_r($rs['err']);exit;
            if($rs['ask'] == 1){
                $updateRow = array(
                    'shipping_method'=>$this->_product_code,
                    'order_status' => '3',
                    'abnormal_reason'=>'',
                    'date_release'=>date('Y-m-d H:i:s'),
                );
            }else{
                $updateRow = array(
                    'shipping_method'=>$this->_product_code,
                    'order_status' => '7',
                    'abnormal_reason'=>$rs['message'].'。'.implode(',', $rs['err']),
                );
                
            }
            //print_r($updateRow);exit;
            Service_Orders::update($updateRow, $this->_ref_id, 'refrence_no_platform');
            
            // 日志
            $logRow = array(
                'ref_id' => $this->_ref_id,
                'log_content' => '订单审核,操作人' . Service_User::getUserName() . ',审核结果:' . $rs['message']
            );
            Service_OrderLog::add($logRow);
            $return['ask'] = $rs['ask'];
            $return['message'] = $rs['message'];
                        
            $return['rs'] = $rs;
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_err;
//         print_r($return);exit;
        return $return;
    }
}