<?php
class Order_LabelPrintController extends Ec_Controller_Action {
	public function preDispatch() {
		$this->tplDirectory = "order/views/label-print/";
	}
	public function indexAction() {
		echo Ec::renderTpl ( $this->tplDirectory . "label-print.tpl", 'layout' );
	}
	public function printLabelAction() {
		set_time_limit ( 0 );
		ini_set ( 'memory_limit', '500M' );
		
		$params = $this->getRequest ()->getParams ();
		$order_id = $this->getParam ( 'order_code','');
		if (empty ( $order_id )) {
			header ( "Content-type: text/html; charset=utf-8" );
			echo Zend_Json::encode ( array (
					"ack" => 0,
					"message" => "没有需要打印的订单" 
			) );
			exit ();
		}
		
		//直接返回到标签打印的
		
		
		$errArr = array ();
		// PDF 打印信息
		$pdfPrintInfo = array ();
		
		$orderInfoArr = array ();
		$db = Common_Common::getAdapterForDb2 ();
		try {
			$order = Service_CsdOrder::getByField ( $order_id, 'shipper_hawbcode' );
			if (! $order) {
				$order = Service_CsdOrder::getByField ( $order_id, 'server_hawbcode' );
			}
			if (! $order) {
				throw new Exception ( Ec::Lang ( '订单不存在' ) );
			}
			if ($order ['customer_id'] != Service_User::getCustomerId ()) {
				throw new Exception ( Ec::Lang ( '非法操作' ) );
			}
			// 获取打印标签
			if (! empty ( $order ['small_hawbcode'] )) {
				$order ['server_hawbcode'] = $order ['small_hawbcode'];
			}
			$order_id = $order['order_id'];
			$printParam ["Data"] [] = $order ['server_hawbcode'];
		} catch ( Exception $e ) {
			$errArr [] = $e->getMessage ();
		}
		if (! empty ( $errArr )) {
			header ( "Content-type: text/html; charset=utf-8" );
			$errinfo = "";
			foreach ( $errArr as $err ) {
				$errinfo .= $err . '<br/>';
			}
			echo Zend_Json::encode ( array (
					"ack" => 0,
					"message" => $errinfo 
			) );
			exit ();
		}
		
		$printParam ["Version"] = "0.0.0.3";
		$printParam ["RequestTime"] = date ( "Y-m-d H:i:s" );
		$printParam ["RequestId"] = "a2b23daa-a519-48cc-b5c6-e0ebbfeada2b";
		$pdfPrintParamJson = Zend_Json::encode ( $printParam );
			$process = new Common_FastReport ( 'TMS' );
			$return = $process->PrintLabel ( $pdfPrintParamJson, "POST" );
			if ($return ['ack'] == 1) {
				$pdfData = $return ["data"] ["Data"];
				$trackingCodes = $printParam ["Data"];
				if ($pdfData) {
					//这里返回一个下载内页
					$PdfReturn = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]."/order/report/label-download?order_id[]=".$order_id;
					$return ['pdf'] = $PdfReturn;
					$updateRow ['print_date'] = date ( 'Y-m-d H:i:s' );
					Service_CsdOrder::update ( $updateRow, $order_id, 'order_id' );
				}
				if ($return ["data"] ["ResponseError"]) {
					$return ['ack'] = 0;
					$return ["message"] = $return ["data"] ["ResponseError"] ["ShortMessage"];
				}
				echo Zend_Json::encode ( $return );
				die ();
			} else {
				header ( "Content-type: text/html; charset=utf-8" );
				echo Zend_Json::encode ( $return );
				die ();
			}
		}
}