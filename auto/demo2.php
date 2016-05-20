<?php
require_once ('config.php');
echo PHP_SAPI;
// 运行记录112 
autoLog(basename(__FILE__));
// 任务开始输出
sapiStart(basename(__FILE__));

try{ // 逻辑处理
    $formalCode = "CNDHL";
    $objCommon = new API_Common_ServiceCommonClass();
    $channel = $objCommon->getServiceChannelByFormalCode($formalCode);

    if (empty($channel)) {
        throw new Exception("无法获取到 [{$formalCode}] 对应的API服务");
    }
    $class = $objCommon->getForApiServiceClass($channel['as_code']);

    if (empty($class)) {
        throw new Exception("无法获取到[{$formalCode}]对应的数据映射类");
    }
    if (class_exists($class)) {
        $obj = new $class();
    } else {
        throw new Exception("无法获取到[{$class}]对应的数据映射文件类");
    }

    $val['shipper_hawbcode'] = "YT1655100008200001";//客户单号
    $init = true;
    $loop = isset($argv[4]) ? $argv[4] : 0;
    $order_config = $loop;

    $obj->setParam($channel['as_code'], $val['shipper_hawbcode'], $channel['server_channelid'], $channel['server_product_code'],$order_config,$init);

    $result = $obj->createAndPreAlertOrderServiceByCode();

    var_dump(111);
    return;
    $url = "http://test.hwcservice.com/ChinaPost/Api/Order/PacketOrder";
    $params = "";
    $method = "POST";

    $result = array("ack"=>0,"message"=>"","data"=>"");
    try {
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $url);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);

        if($method == 'POST') {
            curl_setopt($tuCurl, CURLOPT_POST, 1);
            curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $params);
        }

        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8", "Content-length: ".strlen($params)));

        // print_r($tuCurl);die;
        $data = curl_exec($tuCurl);



        $data = Common_Common::objectToArray(json_decode($data));
        $result["ack"] = 1;
        $result["data"] = $data;
    } catch (Exception  $e) {
        $result["message"] = $e->getMessage();
    }

    Ec::showError("**************start*************\r\n"
        . print_r($params, true)
        . "\r\n"
        . print_r($data, true)
        . "**************end*************\r\n",
        'YunExpress_API/Create_response_info'.date("Ymd"));

    return $result;


}catch(Exception $e){
    echo '[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n";
}
// 任务结束输出 
sapiEnd(basename(__FILE__));

echo $start = date('Y-m-d\TH:i:s.000\Z', strtotime('-9hour'));;