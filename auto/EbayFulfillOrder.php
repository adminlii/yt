<?php
require_once ('config.php');
// 运行记录
autoLog(basename(__FILE__));
// 任务开始输出
sapiStart(basename(__FILE__));
try{ // 逻辑处理
    Ebay_EbayServiceCommon::CompleteSale($account, true);
}catch(Exception $e){
    echo '[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage() . "\r\n";
}

// 任务结束输出
sapiEnd(basename(__FILE__));