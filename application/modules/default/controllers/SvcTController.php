<?php
class Default_SvcTController extends Zend_Controller_Action
{

    public function init()
    {
        $action = $this->_request->getActionName();
        $this->tplDirectory = "default/views/default/";
        $this->_svc = new Common_SvcCall();
    }

    // ==============================================================================================================
    // 测试 start
    public function getCountryAction()
    {
        $page = 1;
        $params = array(
            'pageSize' => '2',
            'page' => $page
        );
        $rs = $this->_svc->getCountry($params);
        print_r($rs);
    }

    public function getCountryPaginationAction()
    {
        // $pageSize = '10';
        // $page = '17';
        // $field = array(
        // 'country_id',
        // 'country_code',
        // 'country_name',
        // 'country_name_en'
        // );
        // $con = array();
        // $country = Service_Country::getByCondition($con, $field, $pageSize,
        // $page);
        // print_r($country);exit;
        $page = 170;
        while(true){
            $params = array(
                'pageSize' => '2',
                'page' => $page
            );
            
            $rs = $this->_svc->getCountryPagination($params);
            $rs['param'] = $params;
            print_r($rs);
            if($rs['nextPage'] != 'true'){
                break;
            }
            $page ++;
        }
    }

    public function getRegionAction()
    {
        $page = 1;
        $params = array(
            'pageSize' => '2',
            'page' => $page
        );
        $rs = $this->_svc->getRegion($params);
        print_r($rs);
    }

    public function getRegionForReceivingAction()
    {
//         $this->_svc = new Common_Svc();
        $rs = $this->_svc->getRegionForReceiving();
        print_r($rs);
    }
    
    public function getWarehouseAction()
    {
        $page = 1;
        $params = array(
            'pageSize' => '2',
            'page' => $page
        );
        $rs = $this->_svc->getWarehouse($params);
        print_r($rs);
    }

    public function getShippingMethodAction()
    {
        $page = 1;
        $params = array(
            'pageSize' => '2',
            'page' => $page
        );
        $rs = $this->_svc->getShippingMethod($params);
        print_r($rs);
    }

    public function getCategoryAction()
    {
        $page = 1;
        $params = array(
            'pageSize' => '2',
            'page' => $page
        );
        $rs = $this->_svc->getCategory($params);
        print_r($rs);
    }

    public function getAccountAction()
    {
        $rs = $this->_svc->getAccount();
        print_r($rs);
    }

    public function createProductAction()
    {
        $images = array();
        $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
        $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
        $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
        $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
//         $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
//         $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
//         $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
//         $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
//         $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
//         $images[] = 'http://www.ruston-oms.com/default/system/view-img?pa_id=3472';
        
        $sku = 'EA' . date('ymdHis');
        $productInfo = array(
            'product_sku' => $sku,
            'reference_no' => $sku,
            'product_title' => $sku,
            
            'product_weight' => '0.35', // 单位KG
            'product_length' => '29.70', // 单位cm
            'product_width' => '21.00', // 单位cm
            'product_height' => '4', // 单位cm
            
            'contain_battery' => '0', // 0不含电池，1：含电池
            
            'product_declared_value' => '10', // currency:USD
            'product_declared_name' => $sku,
            
            'cat_lang' => 'en', // zh,en
            'cat_id_level0' => '400001', // 1级品类
            'cat_id_level1' => '500013', // 2级品类
            'cat_id_level2' => '600109', // 3级品类
            
            'verify' => '1',
            'images'=>$images,
        );
        $rs = $this->_svc->createProduct($productInfo);
        print_r($rs);
    }

    public function modifyProductAction()
    {
        $sku = 'EA' . date('ymdHis');
        $productInfo = array(
            'product_sku' => $sku,
            'reference_no' => $sku,
            'product_title' => $sku,
            
            'product_weight' => '0.35', // 单位KG
            'product_length' => '29.70', // 单位cm
            'product_width' => '21.00', // 单位cm
            'product_height' => '4', // 单位cm
            
            'contain_battery' => '0', // 0不含电池，1：含电池
            
            'product_declared_value' => '10', // currency:USD
            'product_declared_name' => $sku,
            
            'cat_lang' => 'en', // zh,en
            'cat_id_level0' => '400001', // 1级品类
            'cat_id_level1' => '500013', // 2级品类
            'cat_id_level2' => '600109', // 3级品类
            'verify' => '1'
        );
        $rs = $this->_svc->modifyProduct($productInfo);
        print_r($rs);
    }

    public function getProductListAction()
    {
        $page = 1;
        while(true){
            $params = array(
                'pageSize' => '2',
                'page' => $page,
                'product_sku' => '',
                'product_sku_arr' => array()
            );
            $rs = $this->_svc->getProductList($params);
            print_r($rs);
            if($rs['nextPage'] != 'true'){
                break;
            }
            $page ++;
            break;
        }
    }

    public function createAsnAction()
    {
        $receivingInfo = array(
            // 'receiving_code'=>'RV100002-140508-0002',
            'reference_no' => 'dfdfd' . time(), // 入库单参考号
            'income_type' => '0', // 交货方式，0：自送，1：揽收
            'warehouse_code' => 'HRBW', // 目的仓
            
            'transit_warehouse_code' => 'SZW', // income_type为自送时，必填
            'shipping_method' => '顺丰', // 配送方式
            'tracking_number' => '12313213', // 跟踪号
            
            'receiving_desc' => 'dfdfdf', // 入库单描述
            'eta_date' => '2013-04-15', // 预计到达时间
            
            'contacter' => $receivingInfo['contacter'], // income_type为揽收时，联系人
            'contact_phone' => $receivingInfo['contact_phone'], // income_type为揽收时，
                                                                // 联系方式
            'region_id_level0' => '1', // income_type为揽收时， 省份ID,从region表获得
            'region_id_level1' => '1', // income_type为揽收时， 市ID,从region表获得
            'region_id_level2' => '1', // income_type为揽收时， 区ID,从region表获得
            'street' => 'address',
            'verify' => '1'
        );
        
        $items = array();
        $items[] = array(
            'product_sku' => 'EA140509201610',
            'quantity' => '10',
            'box_no' => '1'
        );
        $items[] = array(
            'product_sku' => 'EA140509201610',
            'quantity' => '10',
            'box_no' => '2'
        );
        $items[] = array(
            'product_sku' => 'EA140509201610',
            'quantity' => '10',
            'box_no' => '3'
        );
        $receivingInfo['items'] = $items;
        $rs = $this->_svc->createAsn($receivingInfo);
        print_r($rs);
    }

    public function modifyAsnAction()
    {
        $receivingInfo = array(
            'receiving_code' => 'RV100002-140509-0008',
            'reference_no' => 'dfdfd' . time(), // 入库单参考号
            'income_type' => '0', // 交货方式，0：自送，1：揽收
            'warehouse_code' => 'HRBW', // 目的仓
            
            'transit_warehouse_code' => 'SZW', // income_type为自送时，必填
            'shipping_method' => '顺丰', // 配送方式
            'tracking_number' => '12313213', // 跟踪号
            
            'receiving_desc' => 'dfdfdf', // 入库单描述
            'eta_date' => '2013-04-15', // 预计到达时间
            
            'contacter' => $receivingInfo['contacter'], // income_type为揽收时，联系人
            'contact_phone' => $receivingInfo['contact_phone'], // income_type为揽收时，
                                                                // 联系方式
            'region_id_level0' => '1', // income_type为揽收时， 省份ID,从region表获得
            'region_id_level1' => '1', // income_type为揽收时， 市ID,从region表获得
            'region_id_level2' => '1', // income_type为揽收时， 区ID,从region表获得
            'street' => 'address',
            'verify' => '1'
        );
        
        $items = array();
        $items[] = array(
            'product_sku' => 'EA140509201610',
            'quantity' => '10',
            'box_no' => '1'
        );
        $items[] = array(
            'product_sku' => 'EA140509201610',
            'quantity' => '10',
            'box_no' => '2'
        );
        $items[] = array(
            'product_sku' => 'EA140509201610',
            'quantity' => '10',
            'box_no' => '3'
        );
        $receivingInfo['items'] = $items;
        $rs = $this->_svc->modifyAsn($receivingInfo);
        print_r($rs);
    }

    public function getAsnListAction()
    {
        $page = 1;
        while(true){
            $params = array(
                'pageSize' => '2',
                'page' => $page,
                'receiving_code' => '',
                'receiving_code_arr' => array()
            );
            $rs = $this->_svc->getAsnList($params);
            print_r($rs);
            if($rs['nextPage'] != 'true'){
                break;
            }
            $page ++;
            break;
        }
    }

    public function getProductInventoryAction()
    {
        $page = 1;
        while(true){
            $params = array(
                'pageSize' => '2',
                'page' => $page,
                'product_sku' => '',
                'product_sku_arr' => array(),
                'warehouse_code' => 'HRBW',
                'warehouse_code_arr' => array()
            );
            $rs = $this->_svc->getProductInventory($params);
            print_r($rs);
            if($rs['nextPage'] != 'true'){
                break;
            }
            $page ++;
            break;
        }
    }

    public function createOrderAction()
    {
        $orderInfo = array(
            'platform' => 'OTHER',
            'warehouse_code' => 'HRBW',
            'shipping_method' => 'F4',
            'reference_no' => 'ref_' . time(),
            'order_desc' => '订单描述',
            'country_code' => 'RU',
            'province' => 'province',
            'city' => 'city',
            'address1' => 'address1',
            'address2' => 'address2',
            'address3' => 'address3',
            'zipcode' => '142970',
            'doorplate' => 'doorplate',
            'name' => 'name',
            'phone' => 'phone',
            
            // 'verify' => 1,
            'email' => 'email',
            'parcelDeclaredValue'=>'10.5'
        );
        $items = array();
        $items[] = array(
            'product_sku' => 'DD',
            'quantity' => '1'
        );
        $orderInfo['items'] = $items;
        $rs = $this->_svc->createOrder($orderInfo);
        print_r($rs);
    }

    public function modifyOrderAction()
    {
        $orderInfo = array(
            'platform' => 'OTHER',
            'warehouse_code' => 'HRBW',
            'shipping_method' => 'F4',
            'reference_no' => 'ref_' . time(),
            'order_desc' => '订单描述',
            'country_code' => 'RU',
            'province' => 'province',
            'city' => 'city',
            'address1' => 'address1',
            'address2' => 'address2',
            'address3' => 'address3',
            'zipcode' => 'zipcode',
            'doorplate' => 'doorplate',
            'name' => 'name',
            'phone' => 'phone',
            'email' => 'email',
            'parcelDeclaredValue'=>'10.5',
            
            'verify' => 1
        );
        $items = array();
        $items[] = array(
            'product_sku' => 'EA140509201610',
            'quantity' => '1'
        );
        $orderInfo['items'] = $items;
        
        $rs = $this->_svc->modifyOrder($orderInfo);
        print_r($rs);
    }

    public function cancelOrderAction()
    {
        $orderInfo = array(
            'order_code' => '10001-1000-11',
            'reason' => '客户买错了'
        );
        $rs = $this->_svc->cancelOrder($orderInfo);
        print_r($rs);
    }

    public function getOrderListAction()
    {
        $params = array(
            'pageSize' => '2',
            'page' => '1',
            'order_code' => '',
            'order_code_arr' => array(),
            'create_date_from' => '',
            'create_date_to' => '',
            'modify_date_from' => '',
            'modify_date_to' => ''
        );
        $rs = $this->_svc->getOrderList($params);
        print_r($rs);
    }

    public function orderTrailAction()
    {
        $params = array(
            'warehouse_code' => 'HRBW',
            'country_code' => 'RU',
            'shipping_method' => 'F4', // 运输方式
            'order_weight' => '0.2', // 重量 单位KG
            'length'=>'',
            'width'=>'',
            'height'=>'',
            '1' => '1'
        );
        $rs = $this->_svc->orderTrail($params);
        print_r($rs);
    }

    public function testAction()
    {
        $host = $this->getRequest()->getHttpHost();
        
        $svc = new Common_Svc();
        $m = get_class_methods('Common_Svc');
        foreach($m as $k => $v){
            if($v == 'callService'){
                unset($m[$k]);
                continue;
            }
            $m[$k] = 'public function ' . $v . 'Action(){}';
            // exit;
            $link = "http://" . $host . '/default/svc/' . preg_replace('/([A-Z])/e', 'strtolower("-$1")', $v);
            echo "<p><a href='{$link}' target='_blank'>{$v}</a></p>";
        }
        // print_r(implode(' ', $m));
        exit();
    }

    public function curlAction()
    {
        $this->curl();
    }

    public function curl()
    {
        $req = file_get_contents(APPLICATION_PATH . '/../data/log/request_xml_createAsn.xml');
        set_time_limit(0);
        $url = "http://www.oms-heb.com/default/svc/web-service";
        
        // 代理服务器
        $proxy = '';
        // 请求
        $str = $this->curlRequest($url, $req, $proxy);
        // 输出结果
        header("Content-type: text/xml; Charset=utf-8");
        prInt_r($str);
        // echo 'finish-->see createOrderOutput.xml';
    }
    // -------------------------------------------------------------------------------------------------------------
    private function curlRequest($url, $postData = '', $proxy = "")
    {
        $proxy = trim($proxy);
        $user_agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
        $ch = curl_init(); // 初始化CURL 句柄
        if(! empty($proxy)){
            curl_setopt($ch, CURLOPT_PROXY, $proxy); // 设置代理服务器
        }
        curl_setopt($ch, CURLOPT_URL, $url); // 设置请求的URL
                                             // curl_setopt($ch,
                                             // CURLOPT_FAILONERROR, 1); //
                                             // 启用时显示HTTP 状态码，默认行为是忽略编号小于等于400
                                             // 的HTTP 信息
                                             // curl_setopt($ch,
                                             // CURLOPT_FOLLOWLOCATION,
                                             // 1);//启用时会将服务器服务器返回的“Location:”放在header
                                             // 中递归的返回给服务器
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设为TRUE
                                                     // 把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_POST, 1); // 启用POST 提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // 设置POST 提交的字符串
                                                         // curl_setopt($ch,
                                                         // CURLOPT_PORT, 80);
                                                         // //设置端口
        curl_setopt($ch, CURLOPT_TIMEOUT, 25); // 超时时间
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent); // HTTP 请求User-Agent:头
                                                          // curl_setopt($ch,CURLOPT_HEADER,1);//设为TRUE
                                                          // 在输出中包含头信息
                                                          // $fp =
                                                          // fopen("example_homepage.txt",
                                                          // "w");//输出文件
                                                          // curl_setopt($ch,
                                                          // CURLOPT_FILE,
                                                          // $fp);//设置输出文件的位置，值是一个资源类型，默认为STDOUT
                                                          // (浏览器)。
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-Language: zh-cn',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Content-type: text/xml'
        )); // 设置HTTP 头信息
        
        $document = curl_exec($ch); // 执行预定义的CURL
        $info = curl_getinfo($ch); // 得到返回信息的特性
                                   // prInt_r($info);
        curl_close($ch);
        if($info['http_code'] == "405"){
            $result['message'] = "bad proxy {$proxy}\n"; // 代理出错
            return $result;
        }
        return $document;
    }
    // ==============================================================================================================
    // 测试 end
    
}