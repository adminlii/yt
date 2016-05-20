<?php
class Service_ReceivingProcess
{

    protected $_asnCode = null;

    protected $_receiving = array();

    protected $_receivingDetail = array();

    protected $_userWarehouseIds = array(
        0
    );

    protected $_errorArr = array();

    protected $_params = array();

    protected $_userId = 0;

    protected $_date = '';

    protected $_appCode = '';

    protected $_qcCodes = array();
    
    public $_err = array();
    
    // 收货类型
    public static function getAsnType()
    {
        return Common_Type::receivingType('auto');
    }
    
    // asn状态
    public static function getStatus()
    {
        return Common_Status::receivingStatus('auto');
    }
    
    // asnDetail状态
    public static function getDetailStatus()
    {
        return Common_Status::receivingDetailStatus('auto');
    }

    public function __construct()
    {
        $this->_userId = Service_User::getUserId();
        $this->_date = date('Y-m-d H:i:s');
    }
    
    /**
     * 允许揽收的区域
     */
    public static function getSupportArea(){
        //允许的区域 start
        $con = array('ram_type'=>'1');
        $area = Service_ReceivingAreaMap::getByCondition($con);
        $allowArea = array();
        foreach($area as $v){
            $allowArea[] = $v['province_id'];
            $allowArea[] = $v['city_id'];
            $allowArea[] = $v['district_id'];
        }
        $allowArea = array_unique($allowArea);
        return $allowArea;
    }
    /*
     * 入库单内容审核
     */
    protected function _asnValidate($asnData)
    {
        $asnRow = $asnData['asn'];
        $asnProduct = $asnData['products'];
//         if(empty($asnRow['receiving_code'])){
//             $this->_err[] = Ec::Lang('param_error','receiving_code');
//             //throw new Exception(Ec::Lang('param_error','receiving_code'), '30000');            
//         }
        // 自送
        if($asnRow['income_type'] == '0'){
            // // 派送方式
            // if(empty($asnRow['shipping_method'])){
            // throw new Exception(Ec::Lang('shipping_method_can_not_empty'),
        // '30000');
            // }
            // // 跟踪号
            // if(empty($asnRow['tracking_number'])){
            // throw new Exception(Ec::Lang('tracking_no_can_not_empty'),
        // '30000');
            // }
        }else{ // 揽收
               // 省市区
               // 跟踪号
            if(empty($asnRow['region_0'])){
                $this->_err[] = Ec::Lang('region_0_can_not_empty');
                //throw new Exception(Ec::Lang('region_0_can_not_empty'), '30000');
            }
            // 地址
            if(empty($asnRow['street'])){
                $this->_err[] = Ec::Lang('street_can_not_empty');
                //throw new Exception(Ec::Lang('street_can_not_empty'), '30000');
            }
            // 联系人
            if(empty($asnRow['contacter'])){
                $this->_err[] = Ec::Lang('contacter_can_not_empty');
                //throw new Exception(Ec::Lang('contacter_can_not_empty'), '30000');
            }
            // 联系方式
            if(empty($asnRow['contact_phone'])){
                $this->_err[] = Ec::Lang('contact_phone_can_not_empty');
                //throw new Exception(Ec::Lang('contact_phone_can_not_empty'), '30000');
            }
            $allowArea = $this->getSupportArea();
            //允许的区域 end
            //区域不允许
            if(!in_array($asnRow['region_0'],$allowArea)||!in_array($asnRow['region_1'],$allowArea)||!in_array($asnRow['region_2'],$allowArea)){
                $this->_err[] = Ec::Lang('region_not_support');
                //throw new Exception(Ec::Lang('region_not_support','30000'));
            }
        }
        $box_no_Arr = array();
        $sku_total = 0;
        $skuUnique = array();
        // 判断是否有产品
        if(empty($asnProduct)){
            // '产品为必填'
            $this->_err[] = Ec::Lang('pls_select_sku');
            //throw new Exception(Ec::Lang('pls_select_sku'), '30000');
        }else{
            foreach($asnProduct as $k => $p){
                $sku_total += $p['quantity'];
                $productId = $p['product_id'];
                $product = Service_Product::getByField($productId, 'product_id');
                // 产品不存在
                if(empty($product)){
                    $this->_err[] = Ec::Lang('sku_not_exist');
                    continue;
                    //throw new Exception(Ec::Lang('sku_not_exist'), '30000');
                }
                // 产品未审核
                if($product['product_status'] != '1'){
                    $this->_err[] = Ec::Lang('sku_not_verify');
                    continue;
                    //throw new Exception(Ec::Lang('sku_not_verify', $product['product_sku']), '30000');
                }
                // 数量必须为数字且大于0'
                if(! preg_match('/^[0-9]+$/', $p['quantity']) || intval($p['quantity']) < 1){
                    $this->_err[] = Ec::Lang('sku_quantity_must_int_and_gt_0');
                    continue;
                    //throw new Exception(Ec::Lang('sku_quantity_must_int_and_gt_0'), '30000');
                }
                // 数量必须为数字且大于0'
                if(! preg_match('/^[0-9]+$/', $p['box_no']) || intval($p['box_no']) < 1){
                    $this->_err[] = Ec::Lang('box_no_must_int_and_gt_0');
                    continue;
                    //throw new Exception(Ec::Lang('box_no_must_int_and_gt_0'), '30000');
                }
                $box_no_Arr[$p['box_no']] = $p['box_no'];
                // 传递参数
                $p['product_barcode'] = $product['product_barcode'];
                $p['product_sku'] = $product['product_sku'];
                $p['product_title'] = $product['product_title'];
                $p['line_weight'] = $product['product_weight'] * $p['quantity'];
                $skuUnique[$p['product_barcode']] = $p['product_barcode'];
                $asnProduct[$k] = $p;
            }
        }
        // 总箱数
        $asnRow['box_total'] = count($box_no_Arr);
        $asnRow['sku_species'] = count($skuUnique);
        $asnRow['sku_total'] = $sku_total;
        // 验证箱号是否跳跃 start
        $box_no_list = array();
        for($i = 1;$i <= max($box_no_Arr);$i ++){
            $box_no_list[] = $i;
        }
        $diff = array_diff($box_no_list, $box_no_Arr);
        if(! empty($diff)){
            $this->_err[] = Ec::Lang('missing_box_no', implode(',', $diff));
            //throw new Exception(Ec::Lang('missing_box_no', implode(',', $diff)));
        }
        // 验证箱号是否跳跃 end
        
        // 判断仓库
        if(empty($asnRow['warehouse_code'])){
            // '仓库必填'
            $this->_err[] = Ec::Lang('warehouse_can_not_empty');
            //throw new Exception(Ec::Lang('warehouse_can_not_empty'), '30000');
        }else{
            $warehouse = Service_Warehouse::getByField($asnRow['warehouse_code'], 'warehouse_code');
            if(empty($warehouse)){
                $this->_err[] = Ec::Lang('warehouse_illagel');
                //throw new Exception(Ec::Lang('warehouse_illagel', $asnRow['warehouse_code']), '30000');
            }
            $asnRow['warehouse_id'] = $warehouse['warehouse_id'];
        }
        if($asnRow['receiving_type'] == '3'){
            // 判断仓库
            if(empty($asnRow['transit_warehouse_code'])){
                $this->_err[] = Ec::Lang('transit_warehouse_can_not_empty', $asnRow['transit_warehouse_code']);
                //throw new Exception(Ec::Lang('transit_warehouse_can_not_empty', $asnRow['transit_warehouse_code']), '30000');
            }else{
                $warehouse = Service_Warehouse::getByField($asnRow['transit_warehouse_code'], 'warehouse_code');
                if(empty($warehouse)){
                    $this->_err[] = Ec::Lang('warehouse_illagel', $asnRow['transit_warehouse_code']);
                    //throw new Exception(Ec::Lang('warehouse_illagel', $asnRow['transit_warehouse_code']), '30000');
                }
                $asnRow['transit_warehouse_id'] = $warehouse['transit_warehouse_id'];
            }
        }else{
            // unset($asnRow['transit_warehouse_id']);
            // unset($asnRow['transit_warehouse_code']);
        }
        // 公司代码
        if(empty($asnRow['company_code'])){
            // '公司代码'
            $this->_err[] = Ec::Lang('company_code_can_not_empty');
            //throw new Exception(Ec::Lang('company_code_can_not_empty'), '30000');
        }
        
        $row = array(
            'asn' => $asnRow,
            'products' => $asnProduct
        );
        
        return $row;
    }

    /**
     * 验证参考单号是否存在
     *
     * @param string $refrenceNo
     *            客户参考号
     * @param string $receiving_code
     *            平台入库单号
     * @return boolean
     */
    public function validateRefrenceNo($refrenceNo, $receiving_code = '')
    {
        if($refrenceNo){
            $con = array(
                'reference_no' => $refrenceNo
            );
            $rows = Service_Receiving::getByCondition($con);
            foreach($rows as $k => $v){
                if($receiving_code && $receiving_code == $v['receiving_code']){
                    unset($rows[$k]);
                }
                if($v['receiving_status'] == '0'){
                    unset($rows[$k]);
                }
            }
            if($rows){
                $this->_err[] = Ec::Lang('reference_no_exist');
                //throw new Exception(Ec::Lang('reference_no_exist', $refrenceNo), '30000');
            }
        }
        
        return true;
    }

    /**
     * 创建入库单
     *
     * @param unknown_type $row            
     * @return Ambigous <multitype:number NULL , multitype:number string mixed >
     */
    public function createAsnTransaction($row)
    {
        $result = array(
            "ask" => 0,
            "message" => Ec::Lang('order_create_fail')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $asn_code = Common_GetNumbers::getCode('CURRENT_ASN_COUNT', $row['asn']['company_code'], 'RV'); // 入库单号
            $row['asn']['receiving_code'] = $asn_code;
            $this->createAsn($row);
            $db->commit();
            
            $result = array(
                "ask" => 1,
                "message" => Ec::Lang('create_asn_success', $asn_code),
                'receiving_code' => $asn_code
            );
        }catch(Exception $e){
            $db->rollback();
            $result = array(
                "ask" => 0,
                "message" => Ec::Lang('create_asn_fail') . ',Reason:' . $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
        $result['err'] =  $this->_err;
        return $result;
    }

    /**
     * 创建入库单
     *
     * @param array $row            
     * @throws Exception
     * @return multitype:number string mixed
     */
    public function createAsn($row)
    {
        $time = date("Y-m-d H:i:s");
        // 验证输入的数据是否正确
        $row = $this->_asnValidate($row);
        $asnRow = $row['asn'];
        // 验证参考单号
        $this->validateRefrenceNo($asnRow['reference_no'], '');
        
        //===================================校验失败
        if(!empty($this->_err)){
            throw new Exception(Ec::Lang('validate_err'));
        }
        
        $asnRow['receiving_status'] = '1';
        $asn_code = $asnRow['receiving_code'];
        $receivingRow = array(
            'receiving_code' => $asn_code,
            'reference_no' => $asnRow['reference_no'],
            
            'tracking_number' => $asnRow['tracking_number'],
            'warehouse_id' => $asnRow['warehouse_id'],
            'warehouse_code' => $asnRow['warehouse_code'],
            'transit_warehouse_id' => $asnRow['transit_warehouse_id'],
            'transit_warehouse_code' => $asnRow['transit_warehouse_code'],
            'customer_code' => $asnRow['company_code'],
            'receiving_status' => '1',
            'receiving_type' => $asnRow['receiving_type'],
            'receiving_description' => $asnRow['receiving_description'],
            'expected_date' => $asnRow['expected_date'],
            
            'income_type' => $asnRow['income_type'],
            'shipping_method' => $asnRow['shipping_method'],
            'contacter' => $asnRow['contacter'],
            'contact_phone' => $asnRow['contact_phone'],
            'region_0' => $asnRow['region_0'],
            'region_1' => $asnRow['region_1'],
            'region_2' => $asnRow['region_2'],
            'street' => $asnRow['street'],
            
            'box_total' => $asnRow['box_total'],
            'sku_species' => $asnRow['sku_species'],
            'sku_total' => $asnRow['sku_total'],
            'receiving_add_time' => $time,
            'receiving_update_time' => $time
        );
        // print_r($receivingRow);exit;
        // 格式化
        foreach($receivingRow as $k => $v){
            $receivingRow[$k] = isset($v) ? $v : '';
        }
        if(! $asn_id = Service_Receiving::add($receivingRow)){
            throw new Exception(Ec::lang('inner_db_error'), '50000');
        }
        
        $asnProduct = $row['products'];
        foreach($asnProduct as $v){
            $productId = $v['product_id'];
            $productSku = $v['product_sku'];
            $productBarcode = $v['product_barcode'];
            $productTitle = empty($v['product_title']) ? '' : $v['product_title'];
            $qty = $v['quantity'];
            $box_no = $v['box_no'];
            $package_type = $v['package_type'];
            $line_weight = $v['line_weight'];
            $order_item_id = empty($v['order_item_id']) ? '' : $v['order_item_id']; //阿里订单商品ID RUSTON0719
            $now = date("Y-m-d H:i:s");
            $asnProductRow = array(
                'receiving_id' => $asn_id,
                'receiving_code' => $asn_code,
                'product_id' => $productId,
                'product_sku' => $productSku,
                'product_barcode' => $productBarcode,
                'rd_receiving_qty' => $qty,
                'box_no' => $box_no,
                'package_type' => $package_type,
                'line_weight' => $line_weight,
				'order_item_id' => $order_item_id, //阿里订单商品ID RUSTON0719
                
                'rd_add_time' => $now,
                'rd_update_time' => $now,
            		
            	'value_added_type' => $v['value_added_type'],
            );
            if(! Service_ReceivingDetail::add($asnProductRow)){
                throw new Exception(Ec::lang('inner_db_error'), '50000');
            }
        }
        if($asnRow['income_type'] == '1'){
            $addressRow = array(
                'company_code' => $asnRow['company_code'],
                'region_0' => $asnRow['region_0'],
                'region_1' => $asnRow['region_1'],
                'region_2' => $asnRow['region_2'],
                'street' => $asnRow['street'],
                'contacter' => $asnRow['contacter'],
                'contact_phone' => $asnRow['contact_phone']
            );
            foreach($addressRow as $k => $v){
                $addressRow[$k] = trim($v);
            }
            $con = $addressRow;
            $exist = Service_ReceivingAddress::getByCondition($con);
            $addressRow['is_default'] = empty($asnRow['is_default']) ? '0' : 1;
            if(empty($exist)){
                $rd_id = Service_ReceivingAddress::add($addressRow);
            }else{
                $rd_id = $exist[0]['rd_id'];
            }
            if(isset($asnRow['is_default'])){
                $updateRow = array(
                    'is_default' => '1'
                );
                Service_ReceivingAddress::update($addressRow, $rd_id, 'rd_id');
            }
        }
        $logRow = array(
            'receiving_id' => $asn_id,
            'receiving_code' => $asn_code,
            'user_id' => Service_User::getUserId(),
            'customer_code' => $asnRow['company_code'],
            'rl_note' => '创建入库单',
            'rl_ip' => Common_Common::getIP(),
            'rl_add_time' => date('Y-m-d H:i:s')
        );
        Service_ReceivingLog::add($logRow);
    }

    /**
     * 更新入库单
     *
     * @param array $row            
     * @param string $receiving_code            
     * @return Ambigous <multitype:number NULL , multitype:number string unknown
     *         >
     */
    public function updateAsnTransaction($row, $receiving_code)
    {
        $result = array(
            "ask" => 0,
            "message" => Ec::Lang('asn_update_fail')
        );
        
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $this->updateAsn($row, $receiving_code);
            $db->commit();
            
            $result = array(
                "ask" => 1,
                "message" => Ec::Lang('update_asn_success', $receiving_code),
                'receiving_code' => $receiving_code
            );
        }catch(Exception $e){
            $db->rollback();
            $result = array(
                "ask" => 0,
                "message" => Ec::Lang('update_asn_fail', $receiving_code) . $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }

        $result['err'] =  $this->_err;
        return $result;
    }

    /**
     * 更新入库单
     *
     * @param aray $row            
     * @param string $orderId            
     * @throws exception
     * @throws Exception
     * @return multitype:number string unknown
     */
    public function updateAsn($row, $receiving_code)
    {
        $asn = Service_Receiving::getByField($receiving_code, 'receiving_code');
        if(empty($asn)){
            throw new exception(Ec::Lang('asn_not_exist',$receiving_code));
        }
        $asn_code = $receiving_code;
        $asn_id = $asn['receiving_id'];
        $receiving_id = $asn['receiving_id'];
        // 草稿状态能更新，其他状态不可更新
        $allowStatus = array(
            1
        );
        if(! in_array($asn['receiving_status'], $allowStatus)){
            // "Asn Can't Edit,AsnStatus Is Not Draft-->{$orderId}"
            throw new exception(Ec::Lang('asn_edit_deny', $receiving_code));
        }
        // 验证输入的数据是否正确
        $row = $this->_asnValidate($row);
        // 验证参考单号
        $this->validateRefrenceNo($row['reference_no'], $asn['receiving_code']);
        //===================================校验失败
        if(!empty($this->_err)){
            throw new Exception(Ec::Lang('validate_err'));
        }
        $time = date("Y-m-d H:i:s");
        
        $asnRow = $row['asn'];
        $receivingRow = array(
            'reference_no' => $asnRow['reference_no'],
            'tracking_number' => $asnRow['tracking_number'],
            'warehouse_id' => $asnRow['warehouse_id'],
            'warehouse_code' => $asnRow['warehouse_code'],
            'transit_warehouse_id' => $asnRow['transit_warehouse_id'],
            'transit_warehouse_code' => $asnRow['transit_warehouse_code'],
            'customer_code' => $asnRow['company_code'],
            'receiving_type' => $asnRow['receiving_type'],
            'receiving_description' => $asnRow['receiving_description'],
            'expected_date' => $asnRow['expected_date'],
            // 'receiving_add_time' => $time,
            
            'income_type' => $asnRow['income_type'],
            'shipping_method' => $asnRow['shipping_method'],
            
            'region_0' => $asnRow['region_0'],
            'region_1' => $asnRow['region_1'],
            'region_2' => $asnRow['region_2'],
            'street' => $asnRow['street'],
            'contacter' => $asnRow['contacter'],
            'contact_phone' => $asnRow['contact_phone'],
            
            'box_total' => $asnRow['box_total'],
            'sku_species' => $asnRow['sku_species'],
            'sku_total' => $asnRow['sku_total'],
            'receiving_update_time' => $time
        );
        // 格式化
        foreach($receivingRow as $k => $v){
            $receivingRow[$k] = isset($v) ? $v : '';
        }
        if(! Service_Receiving::update($receivingRow, $receiving_code, 'receiving_code')){
            throw new Exception(Ec::lang('inner_db_error'), '50000');
        }
        
        Service_ReceivingDetail::delete($receiving_code, 'receiving_code');
        
        $asnProduct = $row['products'];
        foreach($asnProduct as $v){
            $productId = $v['product_id'];
            $productSku = $v['product_sku'];
            $productBarcode = $v['product_barcode'];
            $productTitle = empty($v['product_title']) ? '' : $v['product_title'];
            $qty = $v['quantity'];
            $box_no = $v['box_no'];
            $package_type = $v['package_type'];
            $line_weight = $v['line_weight'];
            $now = date("Y-m-d H:i:s");
            $asnProductRow = array(
                'receiving_id' => $asn_id,
                'receiving_code' => $asn_code,
                'product_id' => $productId,
                'product_sku' => $productSku,
                'product_barcode' => $productBarcode,
                'rd_receiving_qty' => $qty,
                'box_no' => $box_no,
                'package_type' => $package_type,
                'line_weight' => $line_weight,
            		
                'rd_add_time' => $now,
                'rd_update_time' => $now,
            		
            	'value_added_type' => $v['value_added_type'],
            );
//             print_r($asnProductRow);die;
            if(! Service_ReceivingDetail::add($asnProductRow)){
                throw new Exception(Ec::lang('inner_db_error'), '50000');
            }
        }
        if($asnRow['income_type'] == '1'){
            $addressRow = array(
                'company_code' => $asnRow['company_code'],
                'region_0' => $asnRow['region_0'],
                'region_1' => $asnRow['region_1'],
                'region_2' => $asnRow['region_2'],
                'street' => $asnRow['street'],
                'contacter' => $asnRow['contacter'],
                'contact_phone' => $asnRow['contact_phone']
            );
            foreach($addressRow as $k => $v){
                $addressRow[$k] = trim($v);
            }
            $con = $addressRow;
            $exist = Service_ReceivingAddress::getByCondition($con);
            $addressRow['is_default'] = empty($asnRow['is_default']) ? '0' : 1;
            if(empty($exist)){
                $rd_id = Service_ReceivingAddress::add($addressRow);
            }else{
                $rd_id = $exist[0]['rd_id'];
            }
            if(isset($asnRow['is_default'])){
                $updateRow = array(
                    'is_default' => '1'
                );
                Service_ReceivingAddress::update($addressRow, $rd_id, 'rd_id');
            }
        }
        
        $logRow = array(
            'receiving_id' => $asn_id,
            'receiving_code' => $asn_code,
            'user_id' => Service_User::getUserId(),
            'customer_code' => $asnRow['company_code'],
            'rl_note' => '更新入库单',
            'rl_ip' => Common_Common::getIP(),
            'rl_add_time' => date('Y-m-d H:i:s')
        );
        Service_ReceivingLog::add($logRow);
    }

    public static function getAsnDetail($asnCode = '')
    {
        $result = array(
            'state' => 0,
            'data' => array(),
            'message' => ''
        );
        $asnRow = Service_Receiving::getByField($asnCode, 'receiving_code');
        if(! empty($asnRow)){
            $result['state'] = 1;
            $lang = Ec::getLang(1);
            $asnItem = Service_ReceivingDetail::getByCondition(array(
                'receiving_id' => $asnRow['receiving_id']
            ), '*');
            foreach($asnItem as $key => $val){
                $asnItem[$key]['product'] = Service_Product::getByProduct($val['product_id'], 'product_id');
            }
            $result['data'] = array(
                'order' => $asnRow,
                'item' => $asnItem
            );
        }
        return $result;
    }

    public static function getAsnDetailPrint($asnCode = '')
    {
        $result = array(
            'state' => 0,
            'data' => array(),
            'message' => ''
        );
        $asnRow = Service_Receiving::getByField($asnCode, 'receiving_code');
        if(! empty($asnRow)){
            $result['state'] = 1;
            $lang = Ec::getLang(1);
            $asnItem = Service_ReceivingDetail::getByCondition(array(
                'receiving_id' => $asnRow['receiving_id']
            ), '*');
            $asnDetail = array();
            foreach($asnItem as $key => $p){
                $product = Service_Product::getByField($p['product_id'], 'product_id');
                $key = $product['product_barcode'];
                if(isset($asnDetail[$key])){
                    $asnDetail[$key]['rd_receiving_qty'] += $p['rd_receiving_qty'];
                }else{
                    $asnDetail[$key] = array(
                        'product_id' => $product['product_id'],
                        'product_barcode' => $p['product_barcode'],
                        'product_sku' => $product['product_sku'],
                        'sku' => $product['product_sku'],
                        'rd_receiving_qty' => $p['rd_receiving_qty'],
                        'product_weight' => $product['product_weight'],
                        'product_title' => $product['product_title']
                    );
                }
            }
            $result['data'] = array(
                'order' => $asnRow,
                'item' => $asnDetail
            );
        }
        return $result;
    }

    /**
     * 审核
     * 
     * @param unknown_type $receiving_code            
     * @param unknown_type $eta_date            
     */
    public function verifyTransaction($receiving_code, $eta_date)
    {
        $return = array(
            'ask' => 0,
            'message' => '',
            'receiving_code' => $receiving_code
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        try{
            $this->verify($receiving_code, $eta_date);
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }

    /**
     * 审核
     * 
     * @param unknown_type $receiving_code            
     * @param unknown_type $eta_date            
     * @throws Exception
     */
    public function verify($receiving_code, $eta_date)
    {
        $receiving_code = ! empty($receiving_code) ? $receiving_code : '';
        $asnRow = Service_Receiving::getByField($receiving_code, 'receiving_code');
        if(! $asnRow){
            throw new Exception('asn_not_exist');
        }
        if($asnRow['receiving_status'] != '1'){
            throw new Exception('asn_verify_deny');
        }
        
        $updateRow = array(
            'expected_date' => $eta_date
        );
        Service_Receiving::update($updateRow, $receiving_code, 'receiving_code');
        
        // 发送数据到WMS
        $apiService = new Common_ThirdPartWmsAPI();
        $apiProcess = new Common_ThirdPartWmsAPIProcess();
        $rs = $apiService->createAsn($receiving_code);
        // print_r($rs);exit;
        if($rs['ask'] != 'Failure'){
            // 更新状态
            $updateRow = array(
                'receiving_status' => '5'
            );
            if(isset($rs['transferWarehouseCode'])){
                $updateRow['transit_warehouse_code'] = $rs['transferWarehouseCode'];
                $warehouse = Service_Warehouse::getByField($rs['transferWarehouseCode'], 'warehouse_code');
                if($warehouse){
                    $updateRow['transit_warehouse_id'] = $warehouse['warehouse_id'];
                }
            }
            Service_Receiving::update($updateRow, $receiving_code, 'receiving_code');
            Ec::showError(print_r($updateRow,true),'create_asn_to_wms_update_');
            // 日志
            $logRow = array(
                'receiving_id' => $asnRow['receiving_id'],
                'receiving_code' => $receiving_code,
                'user_id' => Service_User::getUserId(),
                'customer_code' => $asnRow['customer_code'],
                'rl_note' => '入库单发送到WMS成功',
                'rl_ip' => Common_Common::getIP(),
                'rl_add_time' => date('Y-m-d H:i:s')
            );
            Service_ReceivingLog::add($logRow);
            // 同步库存
            $apiProcess->syncReceiving($receiving_code);
        }else{
            throw new Exception(Ec::Lang('wms_error', $rs['message']));
        }
    }

    /**
     * asn强制完成
     * @param unknown_type $code
     * @return Ambigous <Ambigous, multitype:number unknown string NULL >
     */
    public function asnForceFinishTransaction($code){
        return $this->asnDiscardTransaction($code);
    }
    /**
     * Asn废弃
     *
     * @param unknown_type $row            
     * @return Ambigous <multitype:number NULL , multitype:number string mixed >
     */
    public function asnDiscardTransaction($code)
    {
        $result = array(
            "ask" => 0,
            'receiving_code' => $code,
            "message" => Ec::Lang('operation_deny')
        );
        $db = Common_Common::getAdapter();
        $db->beginTransaction();
        $r_note = '';
        try{
            $asnRow = Service_Receiving::getByField($code, 'receiving_code');
            $allowStatus = array(
                '1',
                '5',
                '6',
            );
            if(! in_array($asnRow['receiving_status'], $allowStatus)){
                throw new Exception('[' . $code . ']' . Ec::Lang('operation_deny'));
            }
            $updateRow = array(
                    'receiving_status' => '0',
                    'receiving_update_time' => date('Y-m-d H:i:s')
            );
            if($asnRow['receiving_status'] == '1'){
                $r_note = '入库单作废成功';                
            }
            if($asnRow['receiving_status'] == '5'){ // 已经发送到WMS,但是未收货
                $service = new Common_ThirdPartWmsAPI();
                $rs = $service->cancelAsn($code);
                // print_r($rs);exit;
                if(strtolower($rs['ask']) != 'success'){
                    throw new Exception($rs['message']);
                }                
                foreach($rs['data']['receiving_detail'] as $p){
                    $con = array('receiving_code'=>$p['receiving_code'],'product_barcode'=>$p['product_barcode']);
                    $receivingDetailRow = Service_ReceivingDetail::getByCondition($con);
                    if(empty($receivingDetailRow)){
                        throw new Exception(Ec::Lang('inner_error'));
                    }
                    $receivingDetailRow = $receivingDetailRow[0];
                    $uRow = array('rd_status'=>$p['rd_status']);
                    Service_ReceivingDetail::update($uRow, $receivingDetailRow['rd_id'],'rd_id');
                }
                $updateRow['receiving_status'] = '0';
                $r_note = '入库单作废成功';
            }
            if($asnRow['receiving_status'] == '6'){ //已经发送到WMS,已收货,强制完成
                $service = new Common_ThirdPartWmsAPI();
                $rs = $service->finishAsn($code);
                // print_r($rs);exit;
                if(strtolower($rs['ask']) != 'success'){
                    throw new Exception($rs['message']);
                }
                foreach($rs['data']['receiving_detail'] as $p){
                    $con = array('receiving_code'=>$p['receiving_code'],'product_barcode'=>$p['product_barcode']);
                    $receivingDetailRow = Service_ReceivingDetail::getByCondition($con);
                    if(empty($receivingDetailRow)){
                        throw new Exception(Ec::Lang('inner_error'));
                    }
                    $receivingDetailRow = $receivingDetailRow[0];
                    $uRow = array('rd_status'=>$p['rd_status']);
                    Service_ReceivingDetail::update($uRow, $receivingDetailRow['rd_id'],'rd_id');
                }
                $updateRow['receiving_status'] = '7';
                $r_note = '入库单强制完成成功';
            }            
            
            Service_Receiving::update($updateRow, $asnRow['receiving_id'], 'receiving_id');
            // 日志
            $logRow = array(
                'receiving_id' => $asnRow['receiving_id'],
                'receiving_code' => $asnRow['receiving_code'],
                'user_id' => Service_User::getUserId(),
                'customer_code' => $asnRow['customer_code'],
                'rl_note' => $r_note,
                'rl_ip' => Common_Common::getIP(),
                'rl_add_time' => date('Y-m-d H:i:s')
            );
            Service_ReceivingLog::add($logRow);
            $db->commit();
            $result['ask'] = 1;
            $result['message'] = Ec::Lang('update_asn_success', $code);
        }catch(Exception $e){
            $db->rollback();
            $result['message'] = Ec::Lang('update_asn_fail', $code) . ',Reason:' . $e->getMessage();
        }
        return $result;
    }
}