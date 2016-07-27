<?php
class Order_InvoicePrintController extends Ec_Controller_Action {
	public function preDispatch() {
		$this->tplDirectory = "order/views/invoice-label/";
	} 
	public function invoiceLabelAction() {
		$order_id_arr = $this->getParam('orderId',array());
		try {
			if(empty($order_id_arr)){
				throw new Exception('请传入订单号');
			}
			$countrys = Common_DataCache::getCountry();
// 			print_r($countrys);exit;
			$orderArr = array();
			foreach($order_id_arr as $order_id){
				$order = Service_CsdOrder::getByField($order_id, 'order_id');
				if(!$order){
					throw new Exception(Ec::Lang('订单不存在或已删除'));
				}
				$order['server_hawbcode'] = empty($order['server_hawbcode'])?$order['shipper_hawbcode']:$order['server_hawbcode'];
				if($order['customer_id']!=Service_User::getCustomerId()){
					throw new Exception(Ec::Lang('非法操作'));
				}
				// 历史数据 start
				$con = array(
						'order_id' => $order_id
				);
				$invoice = Service_CsdInvoice::getByCondition($con,'*',0,0,'invoice_id asc');
				$label = array();
				$label = Service_CsdInvoiced::getByCondition($con,'*',0,0,'invoice_id asc');
				
				$total = 0;
				//总件数
				$totalpice = 0;
				//总重
				$totalweight = 0;
				foreach($invoice as $k=>$v){
					$v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
					$invoice[$k] = $v;
					$total+=$v['invoice_totalcharge'];
					$totalweight+=$v['invoice_totalWeight'];
					$totalpice+=$v['invoice_quantity'];
				}
				$extservice = Service_CsdExtraservice::getByCondition($con);
				$shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
				$shipperConsignee['shipper_country_name'] = $countrys[$shipperConsignee['shipper_countrycode']]?$countrys[$shipperConsignee['shipper_countrycode']]['country_enname']:$shipperConsignee['shipper_countrycode'];
				$shipperConsignee['consignee_country_name'] = $countrys[$shipperConsignee['consignee_countrycode']]?$countrys[$shipperConsignee['consignee_countrycode']]['country_enname']:$shipperConsignee['consignee_countrycode'];
				$order['invoice_total'] = $total;
				$orderData = array('order'=>$order,'invoice'=>$invoice,'shipper_consignee'=>$shipperConsignee,"label"=>$label);
				if($orderData['order']['product_code']=='TNT')
					$orderData['order']['product_code']='G_DHL';
				if($orderData['order']['product_code']=='G_DHL'){
					//总价值直接读取
					$total = empty($orderData['invoice'][0]['invoice_totalcharge_all'])?'':$orderData['invoice'][0]['invoice_totalcharge_all'];
					
					
				}
				$orderArr[] = $orderData;
			}
			$this->view->total_Value=$total;
			$this->view->total_pice=$totalpice;
			$this->view->total_weight=$totalweight;
			$this->view->orderArr = $orderArr;
			
		} catch (Exception $e) {
			header("Content-type: text/html; charset=utf-8");
			echo $e->getMessage();exit;
		}
		//读取客户发票模板配置
		$config=Service_Config::getByField('INVOICE_LABEL_TEMPLATE','config_attribute');
		if ($config && !empty($config['config_value'])){
			$config_temp=$config['config_value'];
			$template=$config_temp."_invoice_label.tpl";
			echo $this->view->render($this->tplDirectory.$template);
		}else{
			echo $this->view->render($this->tplDirectory . "invoice-label.tpl");
		}
		
		
		
		
	}
	
	public function invoiceLabel1Action() {
		$order_id = $this->getParam('orderId','');
		$invoice_type = $this->getParam('invoice_type',1);
		try {
			if(empty($order_id)){
				throw new Exception('请传入订单号');
			}
			$countrys = Common_DataCache::getCountry();
			// 			print_r($countrys);exit;
			$orderArr = array();
			$order = Service_CsdOrder::getByField($order_id, 'order_id');
			if(!$order){
				throw new Exception(Ec::Lang('订单不存在或已删除'));
			}
			$order['server_hawbcode'] = empty($order['server_hawbcode'])?$order['shipper_hawbcode']:$order['server_hawbcode'];
			if($order['customer_id']!=Service_User::getCustomerId()){
				throw new Exception(Ec::Lang('非法操作'));
			}
			// 历史数据 start
			$con = array(
					'order_id' => $order_id
			);
			$invoice = Service_CsdInvoice::getByCondition($con,'*',0,0,'invoice_id asc');
			$label = array();
			$label = Service_CsdInvoiced::getByCondition($con,'*',0,0,'invoice_id asc');
			$total = 0;
			//总件数
			$totalpice = 0;
			//总重
			$totalweight = 0;
			foreach($invoice as $k=>$v){
				$v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
				$invoice[$k] = $v;
				//$total+=$v['invoice_totalcharge'];
				$totalweight+=$v['invoice_totalWeight'];
				$totalpice+=$v['invoice_quantity'];
			}
			$extservice = Service_CsdExtraservice::getByCondition($con);
			$shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
			$shipperConsignee['shipper_country_name'] = $countrys[$shipperConsignee['shipper_countrycode']]?$countrys[$shipperConsignee['shipper_countrycode']]['country_enname']:$shipperConsignee['shipper_countrycode'];
			$shipperConsignee['consignee_country_name'] = $countrys[$shipperConsignee['consignee_countrycode']]?$countrys[$shipperConsignee['consignee_countrycode']]['country_enname']:$shipperConsignee['consignee_countrycode'];
			$order['invoice_total'] = $total;
			$orderData = array('order'=>$order,'invoice'=>$invoice,'shipper_consignee'=>$shipperConsignee,"label"=>$label);

			//为了方便 吧TNT渠道也改成DHL 公用一个打印
			$orderData['order']['product_code']='G_DHL';
			$total = empty($orderData['invoice'][0]['invoice_totalcharge_all'])?'':$orderData['invoice'][0]['invoice_totalcharge_all'];
			$this->view->total_Value=$total;
			$this->view->total_pice=$totalpice;
			$this->view->total_weight=$totalweight;
			$this->view->o = $orderData;
			$this->view->invoicetype = $invoice_type;
			//print_r($orderData);	
		} catch (Exception $e) {
			header("Content-type: text/html; charset=utf-8");
			echo $e->getMessage();exit;
		}
$css ='
<style>
*{margin:0; border:0; padding:0; font-family:"微软雅黑","宋体"; font-size:14px;}
.bt{border:2px solid black;}
.bt1{border-top:2px solid black;border-right:2px solid black;}
.bt2{border-left:2px solid black;}
.bt3{border-bottom:2px solid black;}
.bt4{border-right:2px solid black;}
.div1{width:500px;height:200px;float:left;}
.div2{width:500px;height:200px;float:left;}
.div3{width:500px;height:260px;float:left;}
.div4{height:352px;width:1002px}
.div5{height:50px;}
.clear{clear:both;width:0px;height:0px;}
#warp{margin:auto;width:1006px;page-break-inside:avoid;}
p{height:20px;line-height:20px;}
.p1{height:30px;line-height:30px;}
.comment{height:48px;}
.invoice{height:30px;text-align:center;}
.total{margin-left:675px;width:325px;height:40px;}
.span1{line-height: 30px;height: 30px;display: inline-block;width:497px}
</style>
';		
		if($invoice_type==1)	
			$html = $this->view->render($this->tplDirectory . "dhl-label-pdf.tpl");
		else
			$html= $this->view->render($this->tplDirectory . "dhl-label-pdf1.tpl");
		$html=$css.$html;
		//创建文件
		$FileName = md5($order['shipper_hawbcode'].'type'.$invoice_type);
		$htmlFileName = APPLICATION_PATH . '/../public/invoice/'.$FileName.'.html';
		$pdfFileName  = APPLICATION_PATH . '/../public/invoice/'.$FileName.'.pdf';
		if(!file_exists($htmlFileName))
			file_put_contents($htmlFileName, $html);
		//shell调用xml
		if(!file_exists($pdfFileName)){
			shell_exec("wkhtmltopdf {$htmlFileName} {$pdfFileName}");
			//exec('/usr/local/wkhtmltox/bin/./wkhtmltopdf  {$htmlFileName} {$pdfFileName}');
		}
		//创建失败
		if(!file_exists($pdfFileName)){
			exit("创建pdf失败");
		}else{
			$this->redirect("/invoice/{$FileName}.pdf");
		}
	}
}