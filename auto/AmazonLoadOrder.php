<?php
require_once ('config.php');
// 运行记录
autoLog ( basename ( __FILE__ ) );
// 任务开始输出
sapiStart ( basename ( __FILE__ ) );

try { // 逻辑处理
	$svc = new Amazon_Order_OrderServiceProcess ();
	$svc->run ( 'loadAmazonOrder', $account, $company_code );
} catch ( Exception $e ) {
	echo '[' . date ( 'Y-m-d H:is' ) . ']Fail Exception:' . $e->getMessage () . "\r\n";
}
 
try { // 逻辑处理
	Amazon_Order_GenOrder::genOrderBatch ( $account, $company_code );
} catch ( Exception $e ) {
	echo '[' . date ( 'Y-m-d H:is' ) . ']Fail Exception:' . $e->getMessage () . "\r\n";
}

// // $getOrderList = $svc->getOrderList('TEST_AMAZON','10000005','214-12-20
// 00:00:00','214-12-21 11:00:00');
// $getOrderList = $svc->getOrderList('TEST_AMAZON','10000005',date('Y-m-d
// H:i:s',strtotime('-10days')),date('Y-m-d H:i:s',strtotime('-9days')));
// Common_ApiProcess::log(print_r($getOrderList,true));
// 任务结束输出
sapiEnd ( basename ( __FILE__ ) );