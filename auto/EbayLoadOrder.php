<?php
require_once ('config.php');
// 运行记录
autoLog(basename(__FILE__));
// 任务开始输出
sapiStart(basename(__FILE__));


try{ // 逻辑处理
    $orderEbay = new Ebay_LoadEbayOrderService();
    autoLog('loadEbayOrder');
    $orderEbay->run('loadEbayOrder', $account,$company_code);
}catch(Exception $e){
    Common_ApiProcess::log('[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage());
    Ec::showError('ebay订单下载异常：' . $e->getMessage(), 'loadEbayOrder.php_');
}

try{ // 逻辑处理
    $orderEbay = new Ebay_OrderEbayCheckService();
    autoLog('checkEbayOrder');
    $orderEbay->run('checkEbayOrder', $account,$company_code);
}catch(Exception $e){
    Common_ApiProcess::log('[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage());
    Ec::showError('ebay订单下载异常：' . $e->getMessage(), 'loadEbayOrder.php_');
}

try{
	//无地址订单
	Ebay_EbayServiceCommon::cronLoadOrderNoAddressByItemTransactions();

}catch(Exception $e){
	Common_ApiProcess::log ( '[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage());
	Ec::showError('无地址订单异常：' . $e->getMessage(), 'loadEbayOrder.php_');
}
// exit;
// $sql = "update ebay_order set created=0";
// Common_Common::query($sql);
try{ // 逻辑处理
    $orderEbay = new Ebay_GenEbayOrderService();
    autoLog('generateOrderForEbay');
    $orderEbay->generateOrder();
}catch(Exception $e){
    Common_ApiProcess::log('[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage());
    Ec::showError('ebay订单生成异常：' . $e->getMessage(), 'loadEbayOrder.php_');
}

try{
	Ebay_EbayServiceCommon::cronLoadOrderBatch();
}catch(Exception $e){
	Common_ApiProcess::log('[' . date('Y-m-d H:is') . ']Fail Exception:' . $e->getMessage());
	Ec::showError('cronLoadOrder异常：' . $e->getMessage(), 'loadEbayOrder.php_');
}

// 任务结束输出
sapiEnd(basename(__FILE__));