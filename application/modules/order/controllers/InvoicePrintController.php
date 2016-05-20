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
				
				$total = 0;
				foreach($invoice as $k=>$v){
					$v['invoice_unitcharge'] = $v['invoice_quantity']?($v['invoice_totalcharge']/$v['invoice_quantity']):0;
					$invoice[$k] = $v;
					$total+=$v['invoice_totalcharge'];
				}
				$extservice = Service_CsdExtraservice::getByCondition($con);
				$shipperConsignee = Service_CsdShipperconsignee::getByField($order_id,'order_id');
				$shipperConsignee['shipper_country_name'] = $countrys[$shipperConsignee['shipper_countrycode']]?$countrys[$shipperConsignee['shipper_countrycode']]['country_enname']:$shipperConsignee['shipper_countrycode'];
				$shipperConsignee['consignee_country_name'] = $countrys[$shipperConsignee['consignee_countrycode']]?$countrys[$shipperConsignee['consignee_countrycode']]['country_enname']:$shipperConsignee['consignee_countrycode'];
				$order['invoice_total'] = $total;
				$orderData = array('order'=>$order,'invoice'=>$invoice,'shipper_consignee'=>$shipperConsignee);
				$orderArr[] = $orderData;
			}
			$this->view->total_Value=$total;
			$this->view->orderArr = $orderArr;
			
		} catch (Exception $e) {
			header("Content-type: text/html; charset=utf-8");
			echo $e->getMessage();exit;
		}
// 		print_r($orderArr);exit;
		
		
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
}