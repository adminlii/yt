<?php
require_once ('config.php');
echo PHP_SAPI;
// 运行记录112 
autoLog(basename(__FILE__));
// 任务开始输出
sapiStart(basename(__FILE__));

try{ //

    /*$pdfDir = APPLICATION_PATH . "/../data/PDF";
    if(!is_dir($pdfDir)){
        mkdir($pdfDir);
    }
    $filename="test.txt";
    if(!file_exists($pdfDir."/".$filename)){
        $fp=fopen($pdfDir."/".$filename, "w+");
        var_dump($fp);
    }else{
        $fp=fopen($pdfDir."/".$filename, "r");
        var_dump($fp);
    }
    return;*/

    $order_id = "76083";
    $order = Service_CsdOrder::getByField($order_id, 'order_id');

    //$printParam = array();
    $printParam = array(
        "Data" => array("74899992140422057461"),
        "Version" => "0.0.0.3",
        "RequestTime" => date('Y-m-d H:i:s'),
        "RequestId" => "a2b23daa-a519-48cc-b5c6-e0ebbfeada2b"
    );
    $printParamJson = Zend_Json::encode($printParam);
    /*$url = "http://test.hwcservice.com/ChinaPost/api/LabelPrintService/MergeLabelByTrackingNumbers?type=json";
    $username = 'tmsUser';
    $password = '123456';
    $apiToken = base64_encode("{$username}:{$password}");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type:application/json;charset=utf-8",
        "Authorization: Basic {$apiToken}",
    ));
    //curl_setopt($curl, CURLOPT_USERPWD, $username.':'.$password);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $printParamJson); // Post提交的数据包
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);

    var_dump(curl_error($ch));
    var_dump($data);
    return;*/



    $process = new Common_FastReport ();
    $return = $process->PrintLabel($printParamJson, "POST");

    if($return['ack'] == 1) {
        //$pdfData = $return["data"]["Data"];
        $pdfData = "kjsgfhkdjsghfdkjghdfjgk";
        var_dump($pdfData);
        $pdfDir = APPLICATION_PATH . "/../public/PDF";
        if(!is_dir($pdfDir)){
            mkdir($pdfDir);
        }
        $filename = md5($printParam["data"][0]);
        $filename = $filename.".pdf";
        $aimDir = $pdfDir."/".$filename;
        if(!file_exists($aimDir)){
            $fp=fopen($aimDir, "w+");
            if($fp){
                file_put_contents($aimDir, $pdfData);
            }
        }else{
            $fp=fopen($pdfDir."/".$filename, "r");
        }
        header("Location: http://yt.net:9096/PDF/".$filename);
        exit();
    }


}catch(Exception $e){
    echo '[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n";
}
// 任务结束输出 
sapiEnd(basename(__FILE__));

echo $start = date('Y-m-d\TH:i:s.000\Z', strtotime('-9hour'));;