<?php
require_once ('config.php');
// 运行记录
autoLog ( basename ( __FILE__ ) );
// 任务开始输出
sapiStart ( basename ( __FILE__ ) );
try {
	// 逻辑处理
	$listOrders = new Aliexpress_Order_OrderServiceProcess ();
	$listOrders->run ( 'loadAliexpressOrder', $account, $company_code );
} catch ( Exception $e ) {
	echo '[' . date ( 'Y-m-d H:is' ) . ']Fail Exception:' . $e->getMessage () . "\r\n";
}
try {
	// 逻辑处理
	Aliexpress_Order_GenOrder::genOrderBatch ( $account, $company_code );
} catch ( Exception $e ) {
	echo '[' . date ( 'Y-m-d H:is' ) . ']Fail Exception:' . $e->getMessage () . "\r\n";
}

// 任务结束输出
sapiEnd ( basename ( __FILE__ ) );
 