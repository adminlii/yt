<?php
/**
 * 与第三方仓库对接
 * @author Administrator
 */
class Common_ThirdPartWmsAPI
{

    protected $_appToken = ''; // token
    protected $_appKey = ''; // key
    public $_active = true; // 是否启用发送到3part
    private $_client = null; // SoapClient
    public $_error = '';

    private function getClient()
    {
        if(empty($this->_client)){
            $this->setClient();
        }
        
        return $this->_client;
    }

    private function setClient()
    {
        $wmsConfig = Zend_Registry::get('wms');
        Ec::showError(print_r($wmsConfig,true),'_wms_config');
        $wsdl = $wmsConfig['3part']['wsdl'];
        $this->_appToken = $wmsConfig['3part']['appToken'];
        
        $this->_appKey = $wmsConfig['3part']['appKey'];
        
        $this->_active = $wmsConfig['3part']['active'];
        
        //超时
        $timeout = isset($wmsConfig['3part']['timeout'])&&is_numeric($wmsConfig['3part']['timeout'])?$wmsConfig['3part']['timeout']:1000;
        
        $streamContext = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true
            ),
            // 'bindto' => $wmsConfig['3part']['BindTo'],
            'socket' => array()
        ))

        ;
        $options = array(
            "trace" => true,
            "connection_timeout" => $timeout,
            // "exceptions" => true,
            // "soap_version" => SOAP_1_1,
            // "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
            // "stream_context" => $streamContext,
            "encoding" => "utf-8"
        );
        // print_r($wmsConfig);exit;
        if(!$this->_active){
            throw new Exception('Interface configuration is not enabled.');
        }
        
        $this->_client = new SoapClient($wsdl, $options);
    }

    /**
     * 调用webservice
     * ====================================================================================
     *
     * @param unknown_type $req            
     * @return Ambigous <mixed, NULL, multitype:, multitype:Ambigous <mixed,
     *         NULL> , StdClass, multitype:Ambigous <mixed, multitype:,
     *         multitype:Ambigous <mixed, NULL> , NULL> , boolean, number,
     *         string, unknown>
     */
    private function callService($req)
    {
        $client = $this->getClient();
        
        $req['appToken'] = $this->_appToken;
        $req['appKey'] = $this->_appKey;
        $result = $client->callService($req);
        
        $result = Common_Common::objectToArray($result);
        //日志
        $return = Zend_Json::decode($result['response']);
        $return['req'] = $req;
        $return['response'] = $result;
        Ec::showError(print_r($return,true),'_wms_return');
        return $return;
    }

    /**
     * 禁止数组中有null
     *
     * @param unknown_type $arr            
     * @return unknown string
     */
    private function arrFormat($arr)
    {
        if(! is_array($arr)){
            return $arr;
        }
        foreach($arr as $k => $v){
            if(! isset($v)){
                $arr[$k] = '';
            }
        }
        return $arr;
    }

    /**
     * 获取客户ID
     *
     * @param unknown_type $companyCode            
     * @throws Exception
     * @return mixed
     */
    public static function getCompanyCode($cId)
    {
        if(empty($cId)){
            throw new Exception('c_id param error');
        }
        $company = Service_Company::getByField($cId, 'c_id');
        if(! $company){
            throw new Exception('c_id not exists');
        }
        return $company['company_id'];
    }

    /**
     * 获取产品ID
     *
     * @param unknown_type $companyCode            
     * @throws Exception
     * @return mixed
     */
    public static function getProductBarcode($productId)
    {
        if(empty($productId)){
            throw new Exception('product_id param error');
        }
        $product = Service_Product::getByField($productId, 'product_id');
        if(! $product){
            throw new Exception('product_id not exists');
        }
        return $product['product_barcode'];
    }

    /**
     * 获取国家ID
     *
     * @param unknown_type $countryCode            
     * @throws Exception
     * @return mixed
     */
    public static function getCountryCode($countryId)
    {
        if(empty($countryId)){
            throw new Exception('country_id param error');
        }
        $country = Service_Country::getByField($countryId, 'country_id');
        if(! $country){
            throw new Exception('country_id not exists');
        }
        return $country['country_code'];
    }

    /**
     * 仓库看ID
     *
     * @param unknown_type $countryCode            
     * @throws Exception
     * @return mixed
     */
    public static function getWarehouseCode($warehouseId)
    {
        if(empty($warehouseId)){
            throw new Exception('warehouse_id param error');
        }
        $warehouse = Service_Warehouse::getByField($warehouseId, 'warehouse_id');
        if(! $warehouse){
            throw new Exception('warehouse_id not exists');
        }
        return $warehouse['warehouse_code'];
    }

    /**
     * 品类ID
     *
     * @param unknown_type $countryCode            
     * @throws Exception
     * @return mixed
     */
    public static function getCategoryCode($pceId)
    {
        if(empty($pceId)){
            throw new Exception('pce_id param error');
        }
        $category = Service_ProductCategory::getByField($pceId, 'pce_id');
        if(! $category){
            throw new Exception('pce_id not exists');
        }
        return $category['pce_value_shortname'];
    }

    /**
     * 获取客户ID
     *
     * @param unknown_type $companyCode            
     * @throws Exception
     * @return mixed
     */
    public static function getCid($companyCode)
    {
        if(empty($companyCode)){
            throw new Exception('companyCode param error');
        }
        $company = Service_Company::getByField($companyCode, 'company_id');
        if(! $company){
            throw new Exception('company not exists');
        }
        return $company['c_id'];
    }

    /**
     * 获取产品ID
     *
     * @param unknown_type $companyCode            
     * @throws Exception
     * @return mixed
     */
    public static function getProductId($productBarcode)
    {
        if(empty($productBarcode)){
            throw new Exception('product_barcode param error');
        }
        $product = Service_Product::getByField($productBarcode, 'product_barcode');
        if(! $product){
            throw new Exception('product_barcode not exists');
        }
        return $product['product_id'];
    }

    /**
     * 获取产品sku
     *
     * @param unknown_type $companyCode            
     * @throws Exception
     * @return mixed
     */
    public static function getProductSku($productBarcode)
    {
        if(empty($productBarcode)){
            throw new Exception('product_barcode param error');
        }
        if(is_int($productBarcode)){
            $product = Service_Product::getByField($productBarcode, 'product_id');
        }else{
            $product = Service_Product::getByField($productBarcode, 'product_barcode');
        }
        
        return $product ? $product['product_sku'] : '';
    }

    /**
     * 获取国家ID
     *
     * @param unknown_type $countryCode            
     * @throws Exception
     * @return mixed
     */
    public static function getCountryId($countryCode)
    {
        if(empty($countryCode)){
            throw new Exception('country_code param error');
        }
        $country = Service_Country::getByField($countryCode, 'country_code');
        if(! $country){
            throw new Exception('country_code not exists');
        }
        return $country['country_id'];
    }

    /**
     * 仓库看ID
     *
     * @param unknown_type $countryCode            
     * @throws Exception
     * @return mixed
     */
    public static function getWarehouseId($warehouseCode)
    {
        if(empty($warehouseCode)){
            throw new Exception('warehouse_code param error');
        }
        $warehouse = Service_Warehouse::getByField($warehouseCode, 'warehouse_code');
        if(! $warehouse){
            throw new Exception('warehouse not exists');
        }
        return $warehouse['warehouse_id'];
    }

    /**
     * 品类ID
     *
     * @param unknown_type $countryCode            
     * @throws Exception
     * @return mixed
     */
    public static function getCategoryId($shortName)
    {
        if(empty($shortName)){
            throw new Exception('short_name param error');
        }
        $category = Service_ProductCategory::getByField($shortName, 'pce_value_shortname');
        if(! $category){
            throw new Exception('pce_id not exists');
        }
        return $category['pce_id'];
    }
    
    // ============================================================================================================================
    /**
     * 创建/编辑客户
     */
    public function createCompany($companyCode)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
//         	print_r($companyCode);
            $companyInfo = Service_Company::getByField($companyCode, 'company_code');
//             print_r($companyInfo);
            
            if(! $companyInfo){
                throw new Exception('客户不存在');
            }
            $con_user = array(
            		'company_code'=>$companyCode,
            		'is_admin'=>1,
            		);
            $companyUser = Service_User::getByCondition($con_user);
            if(empty($companyUser)){
            	throw new Exception('未能找到客户注册信息');
            }
            $companyUser = $companyUser[0];
//             print_r($companyUser);
            $telephone = "";
            if(!empty($companyUser['user_phone']) && !empty($companyUser['user_mobile_phone'])){
            	$telephone = $companyUser['user_mobile_phone'];
            }else if(!empty($companyUser['user_phone'])){
            	$telephone = $companyUser['user_phone'];
            }else{
            	$telephone = $companyUser['user_mobile_phone'];
            }
            $companyRow = array(
                'customer_code' => $companyInfo['company_code'],
                'customer_firstname' => $companyUser['user_name'],
                'customer_lastname' => '',
                'customer_currency'=>'',
                'trade_name'=>'',
                'customer_company_name'=> $companyInfo['company_name'],
                'customer_email' => $companyUser['user_email'],
            	'customer_email_verify' => $companyUser['email_verify'],
                'customer_telephone' => $telephone,
                'customer_fax' =>'',
                'customer_logo' => '',
                'customer_signature' => ''
            );
            
            $companyRow = $this->arrFormat($companyRow);
            // print_r($companyRow);exit;
            $req = array(
                'service' => 'updateCustomer',
                'paramsJson' => Zend_Json::encode($companyRow)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 编辑
     */
    public function updateCompany($companyCode)
    {
        return $this->createCompany($companyCode);
    }

    /**
     * 编辑
     */
    public function getCompany($companyCode)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $companyInfo = Service_Company::getByField($companyCode, 'company_code');
            
            if(! $companyInfo){
                throw new Exception('客户不存在');
            }
            $companyRow = array(
                'customer_code' => $companyInfo['company_code']
            );
            $req = array(
                'service' => 'getCustomer',
                'paramsJson' => Zend_Json::encode($companyRow)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 创建/编辑产品
     */
    public function createProduct($productId)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{            
            $productId = empty($productId)?'':$productId;
            $productInfo = Service_Product::getByField($productId, 'product_id');
            if(! $productInfo){
                throw new Exception(Ec::Lang('sku_not_exist'));
            }
            //测试
//             $productInfo['company_code'] = 'EC001';
            $pc_id = '0';
            $productRow = array(
                'customer_code' => $productInfo['company_code'],
                'product_sku' => $productInfo['product_sku'],
                'product_barcode' => $productInfo['product_barcode'],
                
                'product_title_en' => $productInfo['product_title_en'],
                'product_title' => $productInfo['product_title'],
                
                'product_length' => $productInfo['product_length'],
                'product_width' => $productInfo['product_width'],
                'product_height' => $productInfo['product_height'],
                'product_weight' => $productInfo['product_weight'],

                'product_declared_name' => $productInfo['product_declared_name'], // 申报名称
                'product_declared_value' => $productInfo['product_declared_value'], // 申报价值
                'warning_qty'=>$productInfo['warning_qty'],//警报库存
                'currency_code' => $productInfo['currency_code'], // 币种
                
                'product_barcode_type' => $productInfo['product_barcode_type'], // 条码类型，系统或者自定义
                'product_type' => $productInfo['product_type'], // 产品类型，普通产品，组合产品
                'contain_battery' => $productInfo['contain_battery'], // 含电池
                'pc_id' => $productInfo['pc_id'],
                'cat_id0' => $productInfo['cat_id0'],
                'cat_id1' => $productInfo['cat_id1'],
                'cat_id2' => $productInfo['cat_id2'],
                'pu_code' => 'EA',
            );
            $productRow = $this->arrFormat($productRow);
            $con = array('product_id'=>$productId);
            $attachs = Service_ProductAttached::getByCondition($con);
            $images = array();
            foreach($attachs as $v){
                $images[] = array(
                    'pa_file_type'=>'link',
                    'pa_path' => "http://" . $_SERVER['SERVER_NAME'] . "/default/index/view-img?pa_id={$v['id']}"
                );
            }
            $productRow['attachedArr'] = $images;
//             print_r($productRow);exit;
            $req = array(
                'service' => 'createProduct',
                'paramsJson' => Zend_Json::encode($productRow)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 编辑产品
     *
     * @param unknown_type $productInfo            
     */
    public function updateProduct($productId)
    {
        return $this->createProduct($productId);
    }
    /**
     * 获取产品单位
     *
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getProduct($product_barcode)
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $productRow = array(
                'product_barcode' => $product_barcode
            );
            $req = array(
                'service' => 'getProduct',
                'paramsJson' => Zend_Json::encode($productRow)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    /**
     * 创建/编辑ASN
     */
    public function createAsn($receivingCode)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $receivingCode = empty($receivingCode)?"":$receivingCode;
            $asnInfo = Service_Receiving::getByField($receivingCode, 'receiving_code');
            if(! $asnInfo){
                throw new Exception(Ec::Lang('asn_not_exist'));
            }
            $state_name = '';
            $city_name = '';
            $zone_name = '';
            if(!empty($asnInfo['region_0'])){
            	$region = Service_Region::getByField($asnInfo['region_0'],'region_id');
            	if($region){
            		$state_name = $region['region_name'];
            	}
            }
            if(!empty($asnInfo['region_1'])){
            	$region = Service_Region::getByField($asnInfo['region_1'],'region_id');
            	if($region){
            		$city_name = $region['region_name'];
            	}
            }
            if(!empty($asnInfo['region_2'])){
            	$region = Service_Region::getByField($asnInfo['region_2'],'region_id');
            	if($region){
            		$zone_name = $region['region_name'];
            	}
            }
            $asnRow = array(
                'receiving_code' => $asnInfo['receiving_code'], // 参考号
                'reference_no' => $asnInfo['reference_no'], // 参考号
                'tracking_number' => $asnInfo['tracking_number'], // 跟踪号
                'warehouse_code' => $asnInfo['warehouse_code'],//目的仓库
                'transit_warehouse_code' => $asnInfo['transit_warehouse_code'],//中转仓
                'customer_code' => $asnInfo['customer_code'],
                'receiving_type' => empty($asnInfo['receiving_type']) ? '0' : $asnInfo['receiving_type'], // 类型
                'income_type'=>$asnInfo['income_type'],//交货方式,0自送，1：揽收
                'shipping_method'=>$asnInfo['shipping_method'],//派送方式
                'box_total'=>$asnInfo['box_total'],//总箱数
                'receiving_source_type' => empty($asnInfo['receiving_source_type']) ? '0' : $asnInfo['receiving_source_type'], // ？？
                'receiving_description' => empty($asnInfo['receiving_description']) ? '' : $asnInfo['receiving_description'],
            	'state_id'=>$asnInfo['region_0'],//省份
            	'city_id'=>$asnInfo['region_1'],//市
            	'zone_id'=>$asnInfo['region_2'],//区
            	
            	'state_name'=>$state_name,//省份
            	'city_name'=>$city_name,//市
            	'zone_name'=>$zone_name,//区
            	
            	'address'=>$asnInfo['street'],//地址
                'contacter' => empty($asnInfo['contacter']) ? '' : $asnInfo['contacter'], // 联系人
                'contact_phone' => empty($asnInfo['contact_phone']) ? '' : $asnInfo['contact_phone'], // 联系方式
                'expected_date' => empty($asnInfo['expected_date']) ? '' : $asnInfo['expected_date'],//预计到货时间
                'eda_date' => empty($asnInfo['eda_date']) ? '' : $asnInfo['eda_date'],
            );
            $con = array(
                'receiving_code' => $receivingCode
            );
           
            $items = Service_ReceivingDetail::getByCondition($con);
            $asnDetail = array();
            $asnDetailSplit = array();
            foreach($items as $p){
                $product = Service_Product::getByField($p['product_barcode'], 'product_barcode');
                $key = $p['product_barcode'];
                if(isset($asnDetail[$key])){
                    $asnDetail[$key]['quantity'] += $p['rd_receiving_qty'];
                }else{
                    $asnDetail[$key] = array(
                        'product_barcode' => $p['product_barcode'],
                        'sku' => $product['product_sku'],
                        'quantity' => $p['rd_receiving_qty'],
                        // 'box_no'=>$p['box_no'],//箱号
                        // 'package_type'=>$p['package_type'],//包装类型
                        'order_item_id'	=>$p['order_item_id'],//阿里订单商品ID RUSTON0719
                        'product_barcode' => $p['product_barcode'],
                    	'value_added_type' => $p['value_added_type'],
                    );
                }
                $asnDetailSplit[] = array(
                    'product_barcode' => $p['product_barcode'],
                    'sku' => $product['product_sku'],
                    'quantity' => $p['rd_receiving_qty'],
                    'box_no' => $p['box_no'], // 箱号
                    'package_type' => $p['package_type'], // 包装类型
                    'product_barcode' => $p['product_barcode'],
                	'value_added_type' => $p['value_added_type'],
                );
            }
            
            $asnRow['detail'] = $asnDetail;            
            $asnRow['detail_split'] = $asnDetailSplit;
            $asnRow = $this->arrFormat($asnRow);
            // throw new Exception(print_r($asnRow,true));
            $req = array(
                'service' => 'createAsn',
                'paramsJson' => Zend_Json::encode($asnRow)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 创建/编辑ASN
     */
    public function updateAsn($receivingCode)
    {
        return $this->createAsn($receivingCode);
    }

    /**
     * 取消ASN
     */
    public function cancelAsn($receivingCode)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $asnInfo = array(
                'receiving_code' => $receivingCode
            );
            $req = array(
                'service' => 'cancelAsn',
                'paramsJson' => Zend_Json::encode($asnInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 强制完成
     */
    public function finishAsn($receivingCode)
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $asnInfo = array(
                    'receiving_code' => $receivingCode
            );
            $req = array(
                    'service' => 'finishAsn',
                    'paramsJson' => Zend_Json::encode($asnInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    
    /**
     * 获取ASN
     */
    public function getAsn($receivingCode)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $asnInfo = array(
                'receiving_code' => $receivingCode
            );
            $req = array(
                'service' => 'getAsn',
                'paramsJson' => Zend_Json::encode($asnInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 创建/编辑订单
     *
     * @param unknown_type $orderInfo            
     * @param unknown_type $returnType            
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function createOrder($refId,$force=1)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => '',
            'ref_id' => $refId
        );
        
        try{
            $refId = empty($refId)?'':$refId;
            $orderInfo = Service_Orders::getByField($refId, 'refrence_no_platform');
            if(! $orderInfo){
                throw new Exception(Ec::Lang('order_not_exist'));
            }
            //判断是否退仓订单 start
            $service = 'createOrder';
            if($orderInfo['order_type']=='return'){
                $service = 'createRefundOrder';
                $force = 0;
            }
            //判断是否退仓订单 end
            
            $orderRow = array(
                'reference_no'=>$orderInfo['refrence_no'],
                'order_code' => $orderInfo['refrence_no_platform'],
                'customer_code' => $orderInfo['company_code'],
                'warehouse_code' => $orderInfo['warehouse_code'],
                'sm_code' => $orderInfo['shipping_method'], // 运输方式
                'order_type' => '0', // 0:Normal,2:Transfer,3:SelfPickup 
                'platform' => $orderInfo['platform'],
                'order_platform_type' => $orderInfo['order_type'],//平台类型:sale正常销售订单,resend重发订单,refound退款,line线下订单,return退仓
                'create_type' => $orderInfo['create_type'],
                'force-verify'=>$force,//强制审核
				'mail_no' => $orderInfo['mail_no'],//阿里传来的国际订单号 RUSTON0719
				'parcel_declared_value'=>$orderInfo['parcel_declared_value'],//申报价值
                'remark' => $orderInfo['operator_note']
            );
            if(! empty($orderInfo['to_warehouse_code'])){ // 转仓
                $orderRow['to_warehouse_code'] = $orderInfo['to_warehouse_code'];
            }
            $addressRow = array(
                'firstname' => $orderInfo['consignee_name'],
                'lastname' => '',
                'company' => empty($orderInfo['consignee_company']) ? '' : $orderInfo['consignee_company'],
                'country_code' => $orderInfo['consignee_country_code'],
                'postcode' => empty($orderInfo['consignee_postal_code']) ? '' : $orderInfo['consignee_postal_code'],
                'state' => empty($orderInfo['consignee_state']) ? '' : $orderInfo['consignee_state'],
                'city' => empty($orderInfo['consignee_city']) ? '' : $orderInfo['consignee_city'],
                'suburb' => empty($orderInfo['suburb']) ? '' : $orderInfo['suburb'],
                'street1' => $orderInfo['consignee_street1'],
                'street2' => $orderInfo['consignee_street2'] . $orderInfo['consignee_street3'],
                'phone' => empty($orderInfo['consignee_phone']) ? '' : $orderInfo['consignee_phone'],
                'email' => empty($orderInfo['consignee_email']) ? '' : $orderInfo['consignee_email']
            );
            $orderRow['address'] = $addressRow;
            $con = array(
                'order_id' => $orderInfo['order_id']
            );
            $detail = Service_OrderProduct::getByCondition($con);
            
            $orderProduct = array();
            foreach($detail as $p){
                $product = Service_Product::getByField($p['product_barcode'],'product_barcode');
                $orderProduct[] = array(
                    'product_barcode' => $p['product_barcode'],
                    'sku' => $product['product_sku'],
                	'company_code' => $product['company_code'],
                    'op_quantity' => $p['op_quantity'],
                    'pc_id'=>'',
                    'parcel_declared_value' => $p['parcel_declared_value'],//申报价值 RUSTON0724
                    'parcel_declared_name' => $p['parcel_declared_name'],//申报名称 RUSTON0724
					'order_item_id'=>$p['order_item_id'] //阿里订单商品ID  RUSTON0719
                );
            }
            $orderRow['orderProduct'] = $orderProduct;
            
//             print_r($orderRow);exit;
            $req = array(
                'service' => $service,
                'paramsJson' => Zend_Json::encode($orderRow)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 截单
     *
     * @param unknown_type $refIds            
     * @param unknown_type $returnType            
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function cancelOrder($refId, $reason = '截单')
    {
        $return = array( 
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $refId = empty($refId)?'':$refId;
            $order = Service_Orders::getByField($refId, 'refrence_no_platform');
            if(! $order){
                throw new Exception(Ec::Lang('order_not_exist'));
            }
            $orderInfo = array(
                'order_code' => $order['refrence_no_platform'],
                'reason' => $reason
            );
            $req = array(
                'service' => 'cancelOrder',
                'paramsJson' => Zend_Json::encode($orderInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 查看订单信息
     *
     * @param unknown_type $refId            
     * @throws Exception
     * @return multitype:number string unknown NULL multitype:unknown mixed
     *         multitype:unknown mixed multitype:multitype:unknown
     */
    public function getOrder($refId)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $orderInfo = array(
                'order_code' => $refId
            );
            $req = array(
                'service' => 'getOrder',
                'paramsJson' => Zend_Json::encode($orderInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    /**
     * 查看订单轨迹
     *
     * @param unknown_type $refId
     * @throws Exception
     * @return multitype:number string unknown NULL multitype:unknown mixed
     *         multitype:unknown mixed multitype:multitype:unknown
     */
    public function getOrderTrack($refId)
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $orderInfo = array(
                    'order_code' => $refId
            );
            $req = array(
                    'service' => 'getOrderTrack',
                    'paramsJson' => Zend_Json::encode($orderInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    /**
     * 获取国家
     *
     * @param unknown_type $refIds            
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getCountry()
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getCountry'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 获取品类
     *
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getCategory()
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                    'service' => 'getCategory'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    
    /**
     * 获取国家
     *
     */
    public function getWarehouse()
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getWarehouseList'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    /**
     * 揽收地址
     *
     */
    public function getReceivingArea()
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                    'service' => 'getReceivingArea'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    
    /**
     * 获取产品单位
     *
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getProductUom()
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                    'service' => 'getProductUom'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    

    /**
     * 获取产品质检项
     *
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getQcOption()
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                    'service' => 'getQcOption'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    
    /**
     * 获取费用类型
     *
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getFeeType()
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                    'service' => 'getFeeType'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    
    /**
     * 获取订单操作节点
     *
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getOrderOperationType()
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                'service' => 'getOrderOperationType'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    /**
     * 获取仓库运输方式
     *
     * @param unknown_type $refIds            
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getWareouseShipment()
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getWarehouseShipment'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 获取库存
     * $param =
     * array('warehouse_code'=>'','company_code'=>'','product_barcode'=>'')
     *
     * @param unknown_type $refIds            
     * @param unknown_type $returnType            
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function getInventory($param)
    {
        $return = array(
            'ask' => 'Success',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getInventory',
                'paramsJson' => Zend_Json::encode($param)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 获取订单费用
     */
    public function getOrderFee($refId)
    {
        $return = array(
            'ask' => 'Success',
            'message' => ''
        );
        try{
            $order = Service_Orders::getByField($refId, 'refrence_no_platform');
            if(! $order){
                throw new Exception(Ec::Lang('order_not_exist'));
            }
            $orderInfo = array(
                'OrderCode' => $order['ref_id_wms']
            );
            
            $req = array(
                'service' => 'getOrderFee',
                'paramsJson' => Zend_Json::encode($orderInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * ======================================================================================================
     */
    /**
     * 批量创建订单
     *
     * @param unknown_type $orderInfoArr            
     * @param unknown_type $returnType            
     * @return Ambigous <multitype:Ambigous <string, mixed> , multitype:Ambigous
     *         <multitype:Ambigous, multitype:Ambigous <string, mixed> ,
     *         multitype:string unknown multitype:NULL unknown_type Ambigous
     *         <number, string, mixed> > >
     */
    public function createOrderBatch($orderInfoArr)
    {
        $return = array(
            'ask' => 'Success',
            'message' => ''
        );
        echo '123';
        exit;
        foreach($orderInfoArr as $orderInfo){
            $result = $this->createOrder($orderInfo);
            $return['result'][] = $result;
        }
        return $return;
    }

    /**
     * 批量获取订单信息
     *
     * @param unknown_type $refIds            
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getOrderList($refIds)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            if(empty($refIds) || ! is_array($refIds)){
                throw new Exception(Ec::Lang('param_is_not_array'));
            }
            $results = array();
            foreach($refIds as $refId){
                $results[] = $this->getOrderByRefId($refId);
            }
            $return['result'] = $results;
            $return['ask'] = 'Success';
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 批量截单
     *
     * @param unknown_type $refIds            
     * @param unknown_type $returnType            
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function cancelOrders($refIds)
    {
        $return = array(
            'ask' => 'Success',
            'message' => ''
        );
        foreach($refIds as $refId){
            $result = $this->cancelOrder($refId);
            $return['result'][] = $result;
        }
        return $return;
    }
    
    
    /**
     * 获取某个运输费用
     *
     * @param unknown_type $refIds
     * @param unknown_type $returnType
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function getRatesByType($params)
    {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => ''
    	);
    	try{
//     		$orderInfo = array(
//     				'order_code' => $order['refrence_no_platform'],
//     				'reason' => $reason
//     		);
    		$req = array(
    				'service' => 'getRatesByType',
    				'paramsJson' => Zend_Json::encode($params)
    		);
    		$return = $this->callService($req);
    	}catch(Exception $e){
    		$return['message'] = $e->getMessage();
    	}
    	return $return;
    }
        
    /**
     * @desc 客户充值
     * @param $customerInfo
     * @return array
     */
    public function balance($balanceRow)
    {
    	/*
			$balanceRow = array(
    							'customer_code' => 'EC001',
    							'currency_code' => 'USD'
    							'cbl_value' => 10.00,
    							'currency_rate' => 1,
    							'pm_code' => 'PAYPAL',
    							'transaction_number' => 'Ds48321sjdnwjs3',
    							'cbl_type' => '3',
    							'cbl_note' => '客户：' . $company_code . ' 通过paypal充值 ' . $sys_cpd_amount . ' ' . $sys_currency_code,
    							'arrive_time' => date('Y-m-d H:i:s'),
    					);
    	 */
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    			'data' => array()
    	);
    	try {
    		$req = array(
    				'service' => 'balance',
    				'paramsJson' => Zend_Json::encode($balanceRow)
    		);
    		$return = $this->callService($req);
    	} catch (Exception $e) {
    		$return['message'] = $e->getMessage();
    	}
    	return $return;
    }
    
    /**
     * 创建退件单
     * @param unknown_type $roCode
     * @throws Exception
     * @return Ambigous <Ambigous, string>
     */
    public function createReturnOrder($roCode){
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );

        try{
            $roCode = empty($roCode) ? "" : $roCode;
            $ro = Service_ReturnOrders::getByField($roCode, 'ro_code');
            $asnRow = array(
                'customer_code' => $ro['company_code'],//wms
                'refrence_no_platform' => $ro['refrence_no_platform'],
                'orderCode' => $ro['refrence_no_platform'],//wms
                'refrence_no_warehouse' => $ro['refrence_no_warehouse'],
                'refrence_no' => $ro['refrence_no'],
                'tracking_number'=>$ro['tracking_number'],
                'receiving_code' => $ro['receiving_code'],
                'ro_code' => $ro['ro_code'],
                'warehouse_id' => $ro['warehouse_id'],
                'expected_date' => $ro['expected_date'],
                'etaDate' => $ro['expected_date'],//wms
                'receiving_exception' => $ro['receiving_exception'],
                'ro_is_all' => $ro['ro_is_all'],
                'ro_type' => $ro['ro_type'],
                'type'=> $ro['ro_type'],//wms
                'ro_process_type' => $ro['ro_process_type'],
                'ro_create_type' => $ro['ro_create_type'],
                'ro_desc' => $ro['ro_desc'],
                'description' => $ro['ro_desc'],//wms
                'ro_note' => $ro['ro_note']
            );
            $con = array(
                'ro_id' => $ro['ro_id']
            );
            
            $items = Service_ReturnOrderProduct::getByCondition($con);
            $detail = array();
            foreach($items as $p){
                $product = Service_Product::getByField($p['product_barcode'], 'product_barcode');
                $row = array(
                    'product_barcode' => $p['product_barcode'],//wms
                    'sku' => $product['product_sku'],//wms
                    'qty'=>$p['rop_quantity'],//wms
                    'rop_quantity' => $p['rop_quantity'],
                    'is_qc' => $p['is_qc'],
                    'isQc' => $p['is_qc'],//wms
                    'exception_process_instruction' => $p['exception_process_instruction'],
                    'processInstruction' => $p['exception_process_instruction'],//wms
                	'value_added_type' => $p['value_added_type'],//wms 增值服务
                    'rop_note' => $p['rop_note'],
                    'note' => $p['rop_note'],//wms
                    'rop_desc' => $p['rop_desc'],
                );
                $row = $this->arrFormat($row);
                $detail[] = $row;
            }
            $param = array();
            $asnRow = $this->arrFormat($asnRow);
            $param['items'] = $detail;
            $param['order'] = $asnRow;
            $req = array(
                'service' => 'createReturnOrder',
                'paramsJson' => Zend_Json::encode($param)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    /**
     * 获取退货单信息
     * @param unknown_type $roCode
     * @return Ambigous <Ambigous, string>
     */
    public function getReturnOrders($roCode){
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );        
        try{
            $param = array();
            $param['ro_code'] = $roCode;
            $req = array(
                    'service' => 'getReturnOrders',
                    'paramsJson' => Zend_Json::encode($param)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
        
        
    }
    
    /**
     * 废弃退货单
     * @param unknown_type $roCode
     * @return Ambigous <Ambigous, string>
     */
    public function cancelReturnOrders($roCode,$note=''){
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        
        try{
            $param = array();
            $param['ro_code'] = $roCode;
            $param['note'] = $note;
            $req = array(
                    'service' => 'cancelReturnOrders',
                    'paramsJson' => Zend_Json::encode($param)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
        
    }

    /**
     * 费用流水
     * @param unknown_type $roCode
     * @return Ambigous <Ambigous, string>
     */
    public function getCustomerBalanceLog($param){
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );        
        try{
            $req = array(
                'service' => 'getCustomerBalanceLog',
                'paramsJson' => Zend_Json::encode($param)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    /**
     * 创建异常收货
     * @param unknown_type $raCode
     */
    public function createReceivingAbnormal($raCode){
        $return = array(
                'ask' => 'Failure',
                'message' => '',
                'ra_code' => $raCode
        );
        
        try{
            $raCode = empty($raCode)?'':$raCode;
            $ra = Service_ReceivingAbnormal::getByField($raCode, 'ra_code');
            if(! $ra){
                throw new Exception(Ec::Lang('ra_not_exist'));
            }
           
            $ra = array(
                    'ref_no'=>$ra['ref_no'],
                    'ra_code' => $ra['ra_code'],
                    'ra_id' => $ra['ra_id'],
                    'customer_code' => $ra['company_code'],
                    'warehouse_id' => $ra['warehouse_id'],
                    'ra_type' => $ra['ra_type'], //处理类型：1:销毁 2:特采上架
                    'ra_desc' => $ra['ra_desc'],
            );
            $con = array(
                    'ra_id' => $ra['ra_id']
            );
            $detail = Service_ReceivingAbnormalDetail::getByCondition($con);
        
            $orderProduct = array();
            foreach($detail as $p){
                $product = Service_Product::getByField($p['product_id'],'product_id');
                $orderProduct[] = array(
                        'product_barcode' => $p['product_barcode'],
                        'sku' => $product['product_sku'],
                        'rad_quantity' => $p['rad_quantity'],
                        'rad_note'=>$p['rad_note'],
                );
            }
            $detail = $orderProduct;
            $param['ra'] = $ra;
            $param['detail'] = $detail;
            //             print_r($orderRow);exit;
            $req = array(
                    'service' => 'createReceivingAbnormal',
                    'paramsJson' => Zend_Json::encode($param)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
        
    }

    /**
     * 创建异常收货
     * @param unknown_type $raCode
     */
    public function getReceivingAbnormal($raCode){
        $return = array(
                'ask' => 'Failure',
                'message' => '',
                'ra_code' => $raCode
        );
    
        try{
            $param = array();
            $param['ra_code'] = $raCode;
            $req = array(
                    'service' => 'getReceivingAbnormal',
                    'paramsJson' => Zend_Json::encode($param)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    
    }


    /**
     * @desc 付款通知
     * @param $payInfo
     * @return array
     */
    public function payNote($payInfo)
    {
        /*
        $payInfo=array(
            'customer_code'=>'',
            'reference_no'=>'',//流水号
            'transaction_no'=>'',
            'payment_customer'=>'',
            'transaction_date'=>'',
            'amount'=>'',
            'ba_code'=>'',//我方收款帐号
            'currency_code'=>'',
            'payer_name'=>'',
            'payer_account_name'=>'',
            'payer_account'=>'',
            'note'=>'',
        );
         */
        $return = array(
            'ask' => 'Failure',
            'message' => '',
            'data' => array()
        );
        try {
            $req = array(
                'service' => 'payNote',
                'paramsJson' => Zend_Json::encode($payInfo)
            );
            $return = $this->callService($req);
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }
        return $return;
    }


    /**
     * @desc 确认到帐
     * @param $payInfo
     * @return array
     */
    public function confirmPay($payInfo)
    {
        /*
        $payInfo=array(
            'customer_code'=>'',
            'reference_no'=>'',流水号
            'transaction_no'=>'',
            'transaction_date'=>'',
            'amount'=>'',
            'currency_code'=>'',
            'payer_name'=>'',
            'payer_account_name'=>'',
            'payer_account'=>'',
            'note'=>'',
        );
         */
        $return = array(
            'ask' => 'Failure',
            'message' => '',
            'data' => array()
        );
        try {
            $req = array(
                'service' => 'confirmPay',
                'paramsJson' => Zend_Json::encode($payInfo)
            );
            $return = $this->callService($req);
        } catch (Exception $e) {
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    /**
     * 分享库存
     * @param unknown_type $shareInfo
     */
    public function shareInventory($shareInfo) {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    	);
    	try {
    		$req = array(
    				'service' => 'shareInventory',
    				'paramsJson' => Zend_Json::encode($shareInfo)
    		);
    		$return = $this->callService($req);
    	} catch (Exception $e) {
    		$return['errorMsg'] = $e->getMessage();
    	}
    	return $return;
    }

    /**
     * 取消分享库存
     * @param unknown_type $shareInfo
     */
    public function cancelShareInventory($shareInfo) {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    	);
    	try {
    		$req = array(
    				'service' => 'cancelShareInventory',
    				'paramsJson' => Zend_Json::encode($shareInfo)
    		);
    		$return = $this->callService($req);
    	} catch (Exception $e) {
    		$return['errorMsg'] = $e->getMessage();
    	}
    	return $return;
    }

    /**
     * 借用库存
     * @param unknown_type $shareInfo
     */
    public function borrowShareInventory($borrowInfo) {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    	);
    	try {
    		$req = array(
    				'service' => 'borrowShareInventory',
    				'paramsJson' => Zend_Json::encode($borrowInfo)
    		);
    		$return = $this->callService($req);
    	} catch (Exception $e) {
    		$return['errorMsg'] = $e->getMessage();
    	}
    	return $return;
    }

    /**
     * 借用库存
     * @param unknown_type $shareInfo
     */
    public function sendBackShareInventory($borrowInfo) {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    	);
    	try {
    		$req = array(
    				'service' => 'sendBackShareInventory',
    				'paramsJson' => Zend_Json::encode($borrowInfo)
    		);
    		$return = $this->callService($req);
    	} catch (Exception $e) {
    		$return['errorMsg'] = $e->getMessage();
    	}
    	return $return;
    }

    /**
     * 创建/编辑转仓订单
     * @param unknown_type $orderInfo
     * @param unknown_type $returnType
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function createTransferOrder($refId,$force=0)
    {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => '',
    			'ref_id' => $refId
    	);
    
    	try{
    		$refId = empty($refId)?'':$refId;
    		$orderInfo = Service_TransferOrders::getByField($refId, 'two_code');
    		if(! $orderInfo){
    			throw new Exception(Ec::Lang('order_not_exist'));
    		}
    		//判断是否退仓订单 start
    		$service = 'createOrder';
    
    		$orderRow = array(
    				'reference_no'=>$orderInfo['two_code'],
    				'order_code' => $orderInfo['two_code'],
    				'customer_code' => $orderInfo['company_code'],
    				'warehouse_code' => $orderInfo['warehouse_code'],
    				'to_warehouse_code' => $orderInfo['to_warehouse_code'],
    				'sm_code' => $orderInfo['shipping_method'], // 运输方式
    				'order_type' => '4', // 0:Normal,2:Transfer,3:SelfPickup, 4:转仓订单
//     				'platform' => $orderInfo['platform'],
//     				'order_platform_type' => $orderInfo['order_type'],//平台类型:sale正常销售订单,resend重发订单,refound退款,line线下订单,return退仓
    				'create_type' => $orderInfo['create_type'],
    				'force-verify'=>$force,//强制审核
//     				'mail_no' => $orderInfo['mail_no'],//阿里传来的国际订单号 RUSTON0719
    				'parcel_declared_value'=> 0,//申报价值
    				'remark' => $orderInfo['order_desc']
    		);
    		
    		// 加载目的仓库地址
    		$warehouseRow = Service_Warehouse::getByField($orderInfo['to_warehouse_id']);
    		
    		// 加载仓库对应的国家
    		$countryRow = Service_Country::getByField($warehouseRow['country_id']);
    		
    		$addressRow = array(
    				'firstname' => $warehouseRow['contacter'],
    				'lastname' => '',
    				'company' => empty($warehouseRow['company']) ? '' : $warehouseRow['company'],
    				'country_code' => $countryRow['country_code'],
    				'postcode' => empty($warehouseRow['postcode']) ? '' : $warehouseRow['postcode'],
    				'state' => empty($warehouseRow['state']) ? '' : $warehouseRow['state'],
    				'city' => empty($warehouseRow['city']) ? '' : $warehouseRow['city'],
    				'suburb' => '',
    				'street1' => $warehouseRow['street_address1'] ? '' : $warehouseRow['street_address1'],
    				'street2' => $warehouseRow['street_address2'] . $warehouseRow['street_address2'],
    				'phone' => empty($warehouseRow['phone_no']) ? '' : $warehouseRow['phone_no'],
    		);
    		$orderRow['address'] = $addressRow;
    		$con = array(
    				'to_id' => $orderInfo['to_id']
    		);
    		$detail = Service_TransferOrderProduct::getByCondition($con);
    
    		$orderProduct = array();
    		foreach($detail as $p){
    			$product = Service_Product::getByField($p['product_barcode'],'product_barcode');
    			$orderProduct[] = array(
    					'product_barcode' => $p['product_barcode'],
    					'sku' => $product['product_sku'],
    					'company_code' => $product['company_code'],
    					'op_quantity' => $p['quantity'],
    					'pc_id'=>'',
    					'parcel_declared_value' => 0,//申报价值 RUSTON0724
    					'parcel_declared_name' => '',//申报名称 RUSTON0724
    					'order_item_id'=> '' //阿里订单商品ID  RUSTON0719
    			);
    		}
    		$orderRow['orderProduct'] = $orderProduct;
    
//     		            print_r($addressRow);exit;
    		$req = array(
    				'service' => $service,
    				'paramsJson' => Zend_Json::encode($orderRow)
    		);
    		$return = $this->callService($req);
//     		print_r($return);exit;
    	}catch(Exception $e){
    		$return['message'] = $e->getMessage();
    	}
    	return $return;
    }
    

    /**
     * 截单
     *
     * @param unknown_type $refIds
     * @param unknown_type $returnType
     * @throws Exception
     * @return multitype:Ambigous <string, mixed>
     */
    public function cancelTransferOrder($refId, $reason = '截单')
    {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => ''
    	);
    	try{
    		$refId = empty($refId)?'':$refId;
    		$order = Service_TransferOrders::getByField($refId, 'two_code');
    		if(! $order){
    			throw new Exception(Ec::Lang('order_not_exist'));
    		}
    		$orderInfo = array(
    				'order_code' => $order['two_code'],
    				'reason' => $reason
    		);
    		$req = array(
    				'service' => 'cancelOrder',
    				'paramsJson' => Zend_Json::encode($orderInfo)
    		);
    		$return = $this->callService($req);
//     		print_r($return); die;
    	}catch(Exception $e){
    		$return['message'] = $e->getMessage();
    	}
    	return $return;
    }
    
    /**
     * 获取增值服务类型
     *
     * @param unknown_type $refIds
     * @return multitype:Ambigous <multitype:number, multitype:number string
     *         unknown_type NULL multitype:unknown mixed multitype:unknown mixed
     *         multitype:multitype:unknown >
     */
    public function getValueAddedType()
    {
    	$return = array(
    			'ask' => 'Failure',
    			'message' => ''
    	);
    	try{
    		$req = array(
    				'service' => 'getValueAddedType'
    		);
    		$return = $this->callService($req);
    	}catch(Exception $e){
    		$return['message'] = $e->getMessage();
    	}
    	return $return;
    }
}