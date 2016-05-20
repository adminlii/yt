<?php
/**
 * 马帮-订单服务
 * @author Max
 */
class Mabang_Order_OrderServiceProcess extends Ec_AutoRun{
	private $_msg = array();

	private $_sup_task = false;
	
	private $_user_account = '';
	
	private $_company_code = '';
	
	private $_order_data = array();
	
	private $_orderArr = array();
	
	public function setUserAccount($user_account)
	{
		$this->_user_account = $user_account;
	}
	
	public function setCompanyCode($company_code)
	{
		$this->_company_code = $company_code;
	}
	
	public function orderListQuery($codes) {
				//throw new Exception('账号/客户代码未设置');
		$return = array (
				'ask' => 0,
				'message' => Ec::Lang('订单下载失败') ,
				'msgArr'=>array()
		);
		$msgArr = array();
		
		try {

			$con = array (
					'platform' => 'mabang',
					'status' => 1,
					'user_account' => $user_account,
					'company_code'=>$company_code,
			);
			
				
			$svc = new Mabang_Order_OrderService ();
			//下载订单列表
			$orderListRs = $svc->orderListQuery ($codes);
			if($orderListRs['ask']){
				$orderArr = $orderListRs ['orderArr'];
				$this->_msg[] = "下载订单列表成功,在时间段{$start}~{$end}之内，共".count($orderArr)."个订单";
				foreach ( $orderArr as $k=> $order ) {
					$msgStr = '';
					$code = $order ['code'];
					$moo_id = $order ['moo_id'];
					$genProcess = new Mabang_Order_GenOrder();
					$genProcess->setMooId($moo_id);
					$genProcess->setCustomerCode($order['myexpresschannelcustomerCode']);
									//print_r($orderArr['myexpresschannelcustomerCode']);exit;

					$genOrderRs = $genProcess->genOrder();
					if($genOrderRs['ask']){
						$this->_msg[] = "[{$code}]生成平台订单并预报成功";					
					}else{
						$this->_msg[] = "[{$code}]生成平台订单/预报失败，失败原因:".$genOrderRs['message'];								
					}
					$orderArr[$k]['genOrderRs'] = $genOrderRs;
					
// 					Ec::showError ( print_r ( $genOrderRs, true ), '____aliexpress_order_detail_' );
				}
				$orderListRs['orderArr'] = $orderArr;
			}else{
				if($break){
					throw new Exception("下载订单列表失败，失败原因:".$orderListRs['message']);
				}
				$this->_msg[] = "下载订单列表失败，失败原因:".$orderListRs['message'];
			}
			$return['orderList'] = $orderListRs;
			$return['ask'] = 1;
			$return['message'] = 'Finish';
		} catch (Exception $e) {
						//print_r($e);exit;
			Ec::showError($e->getMessage(), '_mabang_receiveerr_' . date('Y-m-d') . "_");
			$return['message'] = $e->getMessage();
		}
		$return['msgArr'] = $this->_msg;
		return $return;		
	}	
	public function orderListQueryByTime($user_account,$company_code, $start, $end) {
				//throw new Exception('账号/客户代码未设置');
		
		$return = array (
				'ask' => 0,
				'message' => Ec::Lang('订单下载失败') ,
				'msgArr'=>array()
		);
		
		$msgArr = array();
		
		try {

			$con = array (
					'platform' => 'mabang',
					'status' => 1,
					'user_account' => $user_account,
					'company_code'=>$company_code,
			);
			
			
			$svc = new Mabang_Order_OrderService ();
			//下载订单列表
			$orderListRs = $svc->orderListQueryByTime ($start, $end);
// 			$return['message']=$orderListRs;
// 			return $return;
//			var_dump($orderListRs);exit;
			if($orderListRs['ask']){
				$orderArr = $orderListRs ['orderArr'];
				$this->_msg[] = "下载订单列表成功,在时间段{$start}~{$end}之内，共".count($orderArr)."个订单";
				
				foreach ( $orderArr as $k=> $order ) {
					$msgStr = '';
					$code = $order ['code'];
					$moo_id = $order ['moo_id'];
					$genProcess = new Mabang_Order_GenOrder();
					$genProcess->setMooId($moo_id);
					$genProcess->setCustomerCode($order['myexpresschannelcustomerCode']);
									//print_r($orderArr['myexpresschannelcustomerCode']);exit;

					$genOrderRs = $genProcess->genOrder();
					$return['msg']=$genOrderRs;
					if($genOrderRs['ask']){
						$this->_msg[] = "[{$code}]生成平台订单成功";					
					}else{
						$this->_msg[] = "[{$code}]生成平台订单失败，失败原因:".$genOrderRs['message'];								
					}
					$orderArr[$k]['genOrderRs'] = $genOrderRs;
					
// 					Ec::showError ( print_r ( $genOrderRs, true ), '____aliexpress_order_detail_' );
				}
				$orderListRs['orderArr'] = $orderArr;
			}else{
				if($break){
					throw new Exception("下载订单列表失败，失败原因:".$orderListRs['message']);
				}
				$this->_msg[] = "下载订单列表失败，失败原因:".$orderListRs['message'];
			}
			$return['orderList'] = $orderListRs;
			$return['ask'] = 1;
			$return['message'] = 'Finish';
		} catch (Exception $e) {
						//print_r($e);exit;
			Ec::showError($e->getMessage(), '_mabang_receiveerr_' . date('Y-m-d') . "_");
			$return['message'] = $e->getMessage();
		}
		$return['msgArr'] = $this->_msg;
		return $return;		
	}	
}