<?php
class Ebay_Order_GenOrder
{

	private $_refrence_no_platform='';
	private $_refrence_no='';
	
    private $_order_sn = '';

    private $_db = null;

    private $_user_account = '';

    private $_company_code = '';
    
    // ebay 原始信息 start
    private $_ebay_order = array();
    
    private $_ebay_order_status_info = '';

    private $_ebay_order_detail = array();

    private $_ebay_order_payments = array();

    private $_ebay_order_external_transaction = array();

    private $_ebay_order_ship_detail = array();
    // ebay 原始信息 end
    private $_ebay_order_status = 2; // 平台订单状态
    private $_order_status = 2; // 销售端订单状态
    private $_platform_ship_status = 0; // 平台发货状态
    private $_platform_ship_time = ''; // 平台发货时间
    private $_shipping_method_no = '';

    private $_carrier_used = '';

    private $_trail_flag = false; // 试运行标记
    private $_trail_type = ''; // 试运行类型
    private $_trail_order_status = 4; // 试运行的默认状态
    private $_ot_id = null; // 分类ID
    private $_abmormal_reason = '';
                            
    // 操作订单信息 start
    private $_order = null;

    private $_addressRow = array();

    private $_orderRow = array();

    private $_updateRow = array();

    private $_order_product_arr = array();
    // 操作订单信息 end
    public function __construct($order_sn)
    {
        $this->_order_sn = $order_sn;
        $this->_db = Common_Common::getAdapter();
    }
    public function setOrderSn($order_sn) {
    	$this->_order_sn = $order_sn;
    }
    public function setUserAccount($user_account) {
    	$this->_user_account = $user_account;
    }
    public function setCompanyCode($company_code) {
    	$this->_company_code = $company_code;
    }
    private function _getEbayOrder()
    {
        $this->_ebay_order = Service_EbayOrder::getByField($this->_order_sn, 'order_sn');
        if(! $this->_ebay_order){
            throw new Exception('订单不存在');
        }
        $this->_refrence_no = $this->_order_sn;
        $this->_refrence_no_platform = 'EB' . sprintf ( '%010s',  $this->_ebay_order ['eo_id'] );
        
        $this->_ebay_order_status_info = '订单状态:OrderStatus=>' . $this->_ebay_order['order_status'];
        $this->_ebay_order_status_info .= '付款状态:CheckoutStatus=>' . $this->_ebay_order['checkout_status'];
        $this->_ebay_order_status_info .= '付款处理结果:CheckoutPaymentStatus=>' . $this->_ebay_order['checkout_payment_status'];
        $this->_ebay_order_status_info .= '付款时间:PaidTime=>' . $this->_ebay_order['paid_time'];
        
        $this->_user_account = $this->_ebay_order['user_account'];
        $this->_company_code = $this->_ebay_order['company_code'];
    }

    private function _getAddress()
    {}

    private function _getPayments()
    {
        // 付款信息
        $con = array(
            'payment_status' => 'Succeeded',
            'order_sn' => $this->_order_sn
        );
        $this->_ebay_order_payments = Service_EbayOrderPayments::getByCondition($con);
    }

    private function _getOrderDetail()
    {
        $con = array(
            'order_sn' => $this->_order_sn
        );
        $this->_ebay_order_detail = Service_EbayOrderDetail::getByCondition($con);
        if(empty($this->_ebay_order_detail)){
            throw new Exception('订单明细不存在');
        }
    }

    private function _getExternalTransaction()
    {
        $con = array(
            'order_sn' => $this->_order_sn
        );
        $this->_ebay_order_external_transaction = Service_EbayOrderExternalTransaction::getByCondition($con);
    }

    private function _getShipDetail()
    {
        $con = array(
            'order_sn' => $this->_order_sn
        );
        $this->_ebay_order_ship_detail = Service_EbayOrderShipDetail::getByCondition($con);
    }

    private function _validate()
    {}

    private function _getOrder()
    {
		if(empty($this->_refrence_no_platform)||empty($this->_refrence_no)){
			throw new Exception('refrence_no_platform/refrence_no未设置');
		}
		$con = array (
				'refrence_no_platform' => $this->_refrence_no_platform,
				'user_account' => $this->_user_account,
				'company_code' => $this->_company_code
		);
		$order = Service_Orders::getByCondition ( $con );
		if ($order) {
			if (count ( $order ) > 1) {
				throw new Exception ( '订单数据异常' );
			}
			$order = array_pop ( $order );
			if($order['order_status']==1){
				Service_Orders::delete($order['order_id'],'order_id');
				Service_OrderProduct::delete($order['order_id'],'order_id');
				Service_ShippingAddress::delete($order['refrence_no_platform'],'OrderID');
			}else{
				$this->_order = $order;
			}
			
		} else {
			$con = array (
					'refrence_no' => $this->_refrence_no,
					'user_account' => $this->_user_account,
					'company_code' => $this->_company_code
			);
			$order = Service_Orders::getByCondition ( $con );
			if ($order) {
				if (count ( $order ) > 1) {
					throw new Exception ( '订单数据异常' );
				}
				$order = array_pop ( $order );
				if ($order ['order_status'] == 1) {
					Service_Orders::delete ( $order ['order_id'], 'order_id' );
					Service_OrderProduct::delete ( $order ['order_id'], 'order_id' );
					Service_ShippingAddress::delete ( $order ['refrence_no_platform'], 'OrderID' );
				} else {
					$this->_order = $order;
				}
			}
		}
	}
    /**
     * 检测即将生成订单的状态
     * @throws Exception
     */
    private function _getEbayOrderStatus()
    {
		// 未完成的订单，不进行操作
		if ($this->_ebay_order ['order_status'] != 'Completed') {
			$this->_order_status = 2;
			$this->_abmormal_reason = "当前订单状态OrderStatus:" . $this->_ebay_order ['order_status'] . ",付款状态CheckoutStatus:" . $this->_ebay_order ['checkout_status'] . ",到账状态CheckoutPaymentStatus:" . $this->_ebay_order ['checkout_payment_status'] . ",到账时间PaidTime：" . $this->_ebay_order ['paid_time'] . ",未完成的订单";
		}
		
		if ($this->_ebay_order ['checkout_status'] != 'Complete') {
			$this->_order_status = 1; // 付款未完成(未付款)
			$this->_abmormal_reason = "当前订单状态OrderStatus:" . $this->_ebay_order ['order_status'] . ",付款状态CheckoutStatus:" . $this->_ebay_order ['checkout_status'] . ",到账状态CheckoutPaymentStatus:" . $this->_ebay_order ['checkout_payment_status'] . ",到账时间PaidTime：" . $this->_ebay_order ['paid_time'] . ",未完成的订单";
			
			// throw new Exception('checkout_status wrong', 1);
		}
		if ($this->_ebay_order ['checkout_payment_status'] != 'NoPaymentFailure') {
			$this->_order_status = 1; // 付款未完成(未付款)
			$this->_abmormal_reason = "当前订单状态OrderStatus:" . $this->_ebay_order ['order_status'] . ",付款状态CheckoutStatus:" . $this->_ebay_order ['checkout_status'] . ",到账状态CheckoutPaymentStatus:" . $this->_ebay_order ['checkout_payment_status'] . ",到账时间PaidTime：" . $this->_ebay_order ['paid_time'] . ",未完成的订单";
			
			// throw new Exception('checkout_payment_status wrong', 1);
		}
		
		if ($this->_ebay_order ['paid_time'] == '') {
			$this->_order_status = 1; // 付款未完成(未付款)
			$this->_abmormal_reason = "当前订单状态OrderStatus:" . $this->_ebay_order ['order_status'] . ",付款状态CheckoutStatus:" . $this->_ebay_order ['checkout_status'] . ",到账状态CheckoutPaymentStatus:" . $this->_ebay_order ['checkout_payment_status'] . ",到账时间PaidTime：" . $this->_ebay_order ['paid_time'] . ",未完成的订单";
			
			// throw new Exception('checkout_payment_status wrong', 1);
		}
	}
    /**
     * 检测订单是否出库
     */
    private function _getPlatformShipStatus(){
        if(! empty($this->_ebay_order['shipped_time'])){ // 已经出库了
            $this->_platform_ship_status = '1';
            $this->_platform_ship_time = $this->_ebay_order['shipped_time'];
        }
        $tracking_number_arr = array();
        $carrier_used = array();
        foreach($this->_ebay_order_ship_detail as $ship){// 已经出库了
            $this->_platform_ship_status = '1';
            $tracking_number_arr[] = $ship['tracking_number'];
            $carrier_used[] = $ship['carrier_used'];
        }
        $tracking_number_arr = array_unique($tracking_number_arr);
        $carrier_used = array_unique($carrier_used);
        // 发货单号
        $this->_shipping_method_no = implode(',', $tracking_number_arr);
        $this->_carrier_used = implode(',', $carrier_used);
    }

    /**
     * 数据整理
     */
    private function _formatData()
    {
        $platform_fee = 0; // 平台费
        $finalvaluefee = 0; // 成交费
        $product_count = 0; // 产品数量
        $site = 'ebay';
        $has_buyer_note = empty($this->_ebay_order['buyer_checkout_message']) ? 0 : 1;
        
        $ot_id = null;
        foreach($this->_ebay_order_detail as $k => $v){
            $site = $v['site']; // 站点
            $v['sku'] = empty($v['sku']) ? '--NoSku--' : $v['sku'];
            $finalvaluefee += $v['final_value_fee'];
            $product_count += $v['quantity_purchased'];
            $this->_ebay_order_detail[$k] = $v;
        }
        
        foreach($this->_ebay_order_payments as $k => $v){
            $platform_fee += $v['fee_or_credit_amount'];
        }
        
        foreach($this->_ebay_order_detail as $v){
            $this->_order_product_arr[] = array(
                'product_id' => '', // ebay_product表ID
                'product_sku' => $v['sku'],
                'warehouse_sku' => '',
                'op_quantity' => $v["quantity_purchased"],
                'product_title' => $v['title'],
                // 'pic' =>$image['src'],//=======================
                'pic' => '',
                'url' => '',
                'op_ref_item_id' => $v['item_id'],
                'op_ref_tnx' => $v['transaction_id'],
                'OrderID' => $this->_refrence_no_platform,
                'OrderIDEbay' => $this->_refrence_no,
                'op_recv_account' => '',
                'op_ref_buyer_id' => $this->_ebay_order['buyer_user_id'],
                'op_ref_paydate' => '',
                'op_site' => $v['site'],
                'op_record_id' => $v['selling_manager_sales_record_number'],
                'op_add_time' => now(),
                'op_update_time' => now(),
                'unit_price' => $v['transaction_price'], // 单价
                'unit_finalvaluefee' => $v['final_value_fee'] / $v["quantity_purchased"], // 单个成交费
                'transaction_price' => $v['transaction_price'] * $v["quantity_purchased"], // 总成交费
                'currency_code' => $this->_ebay_order['amoun_paid_currency'],
                'create_type' => 'api'
            );
        }
        // 地址
        $this->_addressRow = array(
            'Name' => $this->_ebay_order['consignee_name'],
            'Street1' => $this->_ebay_order['consignee_street1'],
            'Street2' => $this->_ebay_order['consignee_street2'],
            'Street3' => '',
            'CityName' => $this->_ebay_order['city_name'],
            'StateOrProvince' => $this->_ebay_order['consignee_state'],
            'Country' => $this->_ebay_order['country'],
            'CountryName' => $this->_ebay_order['country_name'],
            'Phone' => $this->_ebay_order['consignee_phone'],
            'PostalCode' => $this->_ebay_order['consignee_zip'],
            'AddressID' => '',
            'AddressOwner' => '',
            'ExternalAddressID' => '',
            'OrderID' => $this->_refrence_no_platform,
            'Plat_code' => 'ebay',
            'company_code' => $this->_ebay_order['company_code'],
            'user_account' => $this->_ebay_order['user_account'],
            'create_date_sys' => now(),
            'modify_date_sys' => now()
        );
        
        $this->_orderRow = array(
            "platform" => 'ebay',
            'data_source' => 'ebay', // 数据来源（主要区分B2C订单来源）
            'order_type' => 'sale', // 订单类型，sale正常销售订单,resend重发订单,refound退款,line线下订单
            'create_type' => 'api', // 创建类型:api下载的订单，upload批量上传订单，hand手工创建订单
            
            "order_status" => $this->_order_status,
            "create_method" => '2',
            "refrence_no" => $this->_refrence_no,
            "refrence_no_platform" => $this->_refrence_no_platform,
            "shipping_address_id" => 0,
            
            "company_code" => $this->_ebay_order['company_code'],
            "user_account" => $this->_ebay_order['user_account'],
            
            'date_create' => now(),
            'date_last_modify' => now(),
            
            'shipping_method_platform' => empty($this->_ebay_order['shipping_service']) ? '' : $this->_ebay_order['shipping_service'], // 平潭运输方式
//             'shipping_method_no' => $this->_shipping_method_no, // 运单
//             'carrier_name' => $this->_carrier_used, // 承运商
            
            'date_create' => date('Y-m-d H:i:s', strtotime($this->_ebay_order['created_time'])), // 创建时间
            'date_create_platform' => Ec_AutoRun::getLocalTime($this->_ebay_order['created_time']), // 创建时间
            'date_paid_platform' => Ec_AutoRun::getLocalTime($this->_ebay_order['paid_time']), // 付款时间
            
            'platform_ship_status' => $this->_platform_ship_status, // 平台发货状态
            'platform_ship_time' => $this->_platform_ship_time, // 平台发货时间
            
            'currency' => $this->_ebay_order['amoun_paid_currency'],
            'amountpaid' => $this->_ebay_order['amoun_paid'],
            'subtotal' => $this->_ebay_order['subtotal'],
            'ship_fee' => $this->_ebay_order['shipping_service_cost'],
            'platform_fee' => $platform_fee,
            'finalvaluefee' => $finalvaluefee,
            
            'buyer_id' => $this->_ebay_order['buyer_user_id'],
            'buyer_name' => $this->_ebay_order['consignee_name'],
            'buyer_mail' => $this->_ebay_order['buyer_email'],
            'site' => $site,
            'consignee_country' => $this->_ebay_order['country'],
            'product_count' => $product_count,
            'is_one_piece' => $product_count == 1 ? '1' : '0', // 一票一件？
            'order_weight' => 0, // 单位KG
            "order_desc" => $this->_ebay_order['buyer_checkout_message'],
            'has_buyer_note' => $has_buyer_note
        );
        if($this->_platform_ship_status){ // 平台已发货
//             $this->_orderRow['sync_status'] = 1;
        }
    }

    /**
     * 试运行验证
     */
    private function _checkTrail()
    {}

    /**
     * 订单所有原始信息
     */
    private function _getEbayOrderInfo()
    {
        $this->_getEbayOrder();
        $this->_getAddress();
        $this->_getPayments();
        $this->_getOrderDetail();
        $this->_getExternalTransaction();
        $this->_getShipDetail();
    }

    /**
     * 生成订单
     */
    public function genOrder()
    {
        try{
            if(empty($this->_order_sn)){
                throw new Exception('订单号不可为空');
            }
            // 订单原始数据
            $this->_getEbayOrderInfo();
            $this->_getPlatformShipStatus();
            // 平台订单状态
            $this->_getEbayOrderStatus();
            // 格式化数据
            $this->_formatData();
            
            // 操作订单
            $this->_getOrder();

            // 试运行检测
            $this->_checkTrail();
            
            if(! $this->_order){ // 订单未创建或未付款,生成操作订单
                $log = "生成订单，当前订单状态：" . 'OrderStatus=>' . $this->_ebay_order['order_status'];
                $log .= '付款状态:CheckoutStatus=>' . $this->_ebay_order['checkout_status'];
                $log .= '付款处理结果:CheckoutPaymentStatus=>' . $this->_ebay_order['checkout_payment_status'];
                $log .= '付款时间:PaidTime=>' . $this->_ebay_order['paid_time'];
                if($this->_carrier_used){
                    $log .= '承运商=>' . $this->_carrier_used;
                }
                if($this->_shipping_method_no){
                    $log .= '运单号=>' . $this->_shipping_method_no;
                }
                if($this->_ebay_order['shipped_time']){
                    $log .= '发货时间=>' . $this->_ebay_order['shipped_time'];
                }
                
                if($this->_platform_ship_status){ // 平台已发货
                	//$this->_orderRow['order_status'] = 3;
                }
                //问题
                $this->_orderRow['abnormal_reason'] = $this->_abmormal_reason;
                // 日志 start
                $logRow = array(
                    'ref_id' => $this->_order_sn,
                    'log_content' => $log,
                    'create_time' => now(),
                    'op_id' => Service_User::getUserId(),
                    'op_user_id' => Service_User::getUserId()
                );
                Service_OrderLog::add($logRow);
                // 日志 end
                Common_ApiProcess::log("生成操作订单:" . $this->_order_sn);
                
                $refrence_no_sys = Common_ApiProcess::getRefrenceSysCode(); // 系统单号
                $this->_orderRow['refrence_no_sys'] = $refrence_no_sys;
                
                // 地址
                Service_ShippingAddress::delete($this->_order_sn, 'OrderID');
                $addressRow = Ec_AutoRun::arrayNullToEmptyString($this->_addressRow);
                $address_id = Service_ShippingAddress::add($addressRow);
                // 订单头
                $orderRow = Ec_AutoRun::arrayNullToEmptyString($this->_orderRow);
                $orderRow['shipping_address_id'] = $address_id;
                $order_id = Service_Orders::add($orderRow);
                // 订单明细
                Service_OrderProduct::delete($this->_order_sn, 'OrderID');
                foreach($this->_order_product_arr as $p){
                    $p['order_id'] = $order_id;
                    $p = Ec_AutoRun::arrayNullToEmptyString($p);
                    Service_OrderProduct::add($p);
                }
                // 更新产品对应关系
                Service_OrderForWarehouseProcessNew::updateOrderProductWarehouseSku($this->_order_sn);
                // 均价
                Service_OrderProductProcess::updateOrderProductUnitPriceFinalValueFee($this->_order_sn);
                
                 
            }else{ // 订单已生成,更新操作订单
                Common_ApiProcess::log("更新操作订单:" . $this->_order_sn);
                $updateRow = array(
                    'date_last_modify' => now(),
                    'platform_ship_status' => $this->_orderRow['platform_ship_status'],
                    'platform_ship_time' => $this->_orderRow['platform_ship_time']
                );
                if($this->_order['platform_ship_time']){
                    $updateRow['platform_ship_time'] = $this->_order['platform_ship_time'];
                } 
                $this->_updateRow = $updateRow; 
                Service_Orders::update($updateRow, $this->_refrence_no_platform, 'refrence_no_platform');
            }
            
        }catch(Exception $e){
            Common_ApiProcess::log($e->getMessage());
            Ec::showError("订单号=>".$this->_order_sn."创建/更新异常=>".$e->getMessage()."\n状态说明=>\n".$this->_ebay_order_status_info, 'gen_order_');
            if($this->_platform_ship_status){ // 平台已发货，更新发货标记
                $updateRow = array(
                    'date_last_modify' => now(),
                    'platform_ship_status' => $this->_platform_ship_status,
                    'platform_ship_time' => $this->_platform_ship_time
                );
                Service_Orders::update($updateRow, $this->_refrence_no_platform, 'refrence_no_platform');
            }
			$err_msg = $e->getMessage();
        }
        if ($err_msg && preg_match ( '/SQLSTATE/', $err_msg )) {
        
        }else{
        	// 更新生成状态
        	$createUpdate = array (
        			'created' => '1'
        	);
        Service_EbayOrder::update($createUpdate, $this->_order_sn, 'order_sn');
        } 
    }
    
    public function getOrderRow(){
        return $this->_orderRow;
    }
    
    public function getUpdateRow(){
        return $this->_updateRow;
    }
    
}