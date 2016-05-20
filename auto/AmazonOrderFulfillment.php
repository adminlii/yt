<?php
require_once ('config.php');
// 运行记录
autoLog(basename(__FILE__));
// 任务开始输出
sapiStart(basename(__FILE__));
try{
    ob_start();
    // 逻辑处理
    $listOrders = new Amazon_AmazonOrderFulfillmentService();
    $listOrders->run('callOrderFulfillment', $account);
    $out1 = ob_get_contents();
    Ec::showError(str_replace('<br/>', "\n", $out1), '__amazonOrderFulfillment');
    ob_end_clean();
    echo iconv('UTF-8', 'GBK', str_replace('<br/>', "\n", $out1));
}catch(Exception $e){
    echo '[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n";
}

// 任务结束输出
sapiEnd(basename(__FILE__));