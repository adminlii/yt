<?php
require_once (dirname(__FILE__).'/config.php');
// 运行记录
autoLog ( basename ( __FILE__ ) );
// 任务开始输出
sapiStart ( basename ( __FILE__ ) ,false);
// D 草稿
// S 换号中
// A 可用订单
// P 已预报
// V 已收货
// C 已出仓
// E 已废弃
function myfunAction() {
	//锁文件
	$file= APPLICATION_PATH.'/../data/cache/mabang_callback';
// 	if (file_exists ( $file ) && filemtime ( $file )+60*10 > time ()) {
// 		return;
// 	}
// 	file_put_contents($file, date('Y-m-d H:i:s'));
	$local_url = Service_Config::getByField ( "LOCAL_URL", "config_attribute", "config_value" );
	if (empty ( $local_url )) {
		return;
	}
	$local_url = trim ( $local_url['config_value'], '/' );
	
	$sql = "select * from mabang_order_notify order by mon_id asc limit 100;";
	$rows = Common_Common::fetchAll ( $sql );
	
	foreach ( $rows as $v ) {
		$o = Service_CsdOrder::getByField ( $v ['code'], 'shipper_hawbcode' );
		if (! $o) {
			$sql = "delete  from mabang_order_notify where mon_id='{$v['mon_id']}';";
			Common_Common::query ( $sql );
			continue;
		}
		$priceReal = 0;
		$sql = "
				SELECT
				bs.checkin_grossweight as w,
				fun_get_income (bs.bs_id, 'A') as fee
				FROM
				bsn_business bs
				INNER JOIN bsn_expressexport ee ON bs.bs_id = ee.bs_id
				WHERE
				ee.shipper_hawbcode = '{$v['code']}';
		";
		
		$db2=Common_Common::getAdapterForDb2();
		$feeRow = $db2->fetchRow($sql);
		if($feeRow&&$feeRow['w']){
		$o ['order_weight'] = $feeRow['w'];
		}
		if($feeRow&&$feeRow['fee']){
			$priceReal = $feeRow['fee'];
		}
		$params = null;
		switch ($v ['order_status']) {
			case 'P' : //预报
				$params = array (
						'code' => $v ['code'],
						"changeStatus" => 'accept',
						"supplierInnerCode" => $o ['server_hawbcode'],
						"labelIMGUrl" => array (
								'b10_10' => array (
										'a' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=1&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
										'c' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=1&LableContentType=2&printerNo=0&order_code=' . $v ['code'],
										'p' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=1&LableContentType=3&printerNo=0&order_code=' . $v ['code'] 
								),
								'a4' => array (
										'a' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=2&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
										'c' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=2&LableContentType=2&printerNo=0&order_code=' . $v ['code'],
										'p' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=2&LableContentType=3&printerNo=0&order_code=' . $v ['code'] 
								) 
						),
						"labelPDFUrl" => array (
								'b10_10' => array (
										'a' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=1&LableContentType=1&printerNo=0&view=1&order_code=' . $v ['code'],
										'c' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=1&LableContentType=2&printerNo=0&view=1&order_code=' . $v ['code'],
										'p' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=1&LableContentType=3&printerNo=0&view=1&order_code=' . $v ['code'] 
								),
								'a4' => array (
										'a' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=2&LableContentType=1&printerNo=0&view=1&order_code=' . $v ['code'],
										'c' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=2&LableContentType=2&printerNo=0&view=1&order_code=' . $v ['code'],
										'p' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=2&LableContentType=3&printerNo=0&view=1&order_code=' . $v ['code'] 
								) 
						) 
				);
				
				
				break;
			case 'V' ://收货			
				$params = array (
						'code' => $v ['code'],
						"changeStatus" => 'received',
						'processMessage' => '',
						"supplierInnerCode" => $o ['server_hawbcode'],
						"weightReal" => $o ['order_weight'],
						"priceReal" => $priceReal,
// 						"labelIMGUrl" => array (
// 								'b10_10' => array (
// 										'a' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=1&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'c' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=2&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'p' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=4&LableContentType=1&printerNo=0&order_code=' . $v ['code'] 
// 								),
// 								'a4' => array (
// 										'a' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=1&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'c' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=2&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'p' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=4&LableContentType=1&printerNo=0&order_code=' . $v ['code'] 
// 								) 
// 						) 
				);
				
				break;
			case 'C' ://出库
				$params = array (
						'code' => $v ['code'],
						"changeStatus" => 'sent',
						'processMessage' => '',
						"supplierInnerCode" => $o ['server_hawbcode'],
						"expressChannelCode" => $o ['server_hawbcode'],
						"weightReal" => $o ['order_weight'],
						"priceReal" => $priceReal,
// 						"labelIMGUrl" => array (
// 								'b10_10' => array (
// 										'a' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=1&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'c' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=2&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'p' => $local_url . '/default/mabang/print-label?LableFileType=1&LablePaperType=4&LableContentType=1&printerNo=0&order_code=' . $v ['code'] 
// 								),
// 								'a4' => array (
// 										'a' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=1&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'c' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=2&LableContentType=1&printerNo=0&order_code=' . $v ['code'],
// 										'p' => $local_url . '/default/mabang/print-label?LableFileType=2&LablePaperType=4&LableContentType=1&printerNo=0&order_code=' . $v ['code'] 
// 								) 
// 						) 
				);
				
				break;
			case 'E' ://截单
				$params = array (
						'code' => $v ['code'],
						"changeStatus" => 'cancel',
						'processMessage' => '订单取消' 
				);
			case 'U' ://订单异常
				$params = array (
						'code' => $v ['code'],
						"changeStatus" => 'exception',
						'processMessage' => '订单异常,请登录：'.$local_url.'进行查看' 
				);
				
				break;
			Default :
				
				break;
		}
		if ($params) {
			Mabang_MabangLib::updateOrderStatus ( $params );
		}
		
		$sql = "delete  from mabang_order_notify where mon_id='{$v['mon_id']}';";
		Common_Common::query ( $sql );
	}
	//删除锁文件
	@unlink($file);
}

try {
	myfunAction ();
} catch ( Exception $e ) {
	echo '[' . date ( 'Y-m-d H:is' ) . ']Fail Exception:' . $e->getMessage () . "\r\n";
}

// 任务结束输出
sapiEnd ( basename ( __FILE__ ) );