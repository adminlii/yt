<?php
/**
 * 速卖通-订单服务
 * @author Max
 */
class Aliexpress_Order_OrderServiceProcess  extends Ec_AutoRun{
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
	
	
	public function loadAliexpressOrder($loadId)
	{
		$return = array(
				'ask' => 0,
				'message' => ''
		);
		// 得到当前同步订单的关键参数
		$param = $this->getLoadParam($loadId);
		$userAccount = $param["user_account"];
		$companyCode = $param["company_code"];
	
		$this->_user_account = $userAccount;
		$this->_company_code = $companyCode;
	
		$start = $param["load_start_time"];
		$end = $param["load_end_time"];
		$count = $param["currt_run_count"];
		$this->_user_account = $userAccount;
		$this->_company_code = $companyCode;
	
		try{
			//下载失败，中断
			$rs = $this->orderListQueryCli($start,$end);
			//文件日志
			Ec::showError(print_r($rs,true),'aliexpress_load_order_'.date('Y-m-d_'));
			 
			$orderCount = count($this->_orderArr);
			$this->countLoad($loadId, 2, $orderCount); // 运行结束
	
			$return['ask'] = 1;
			$return['message'] = "aliexpress Time : " . $start . " ~ " . $end . ',' . $userAccount . ' Order Count ' . $orderCount;
		}catch(Exception $e){
			$this->countLoad($loadId, 3, 0);
			Ec::showError("账号：" . $userAccount . '发生错误，eBay时间：' . $start . ' To ' . $end . ',错误原因：' . $e->getMessage(), 'runOrder_Fail_');
			$return['message'] = $e->getMessage();
		}
		return $return;
	}

	public function orderListQueryCli($start='',$end='') {
		$con = array (
				'platform' => 'aliexpress',
				'status' => 1,
				'user_account' => $this->_user_account,
				'company_code' => $this->_company_code 
		);
		
		if (empty ( $this->_user_account ) || empty ( $this->_company_code )) {
			throw new Exception ( '账号/客户代码未设置' );
		}
		
		if (empty ( $start ) || empty ( $end )) {
			throw new Exception ( '必须选定起始/结束时间' );
		}
		$resultPlatformUser = Service_PlatformUser::getByCondition ( $con );
		if (empty ( $resultPlatformUser )) {
			throw new Exception ( '用户不存在/未激活' );
		}
		// print_r($resultPlatformUser);exit;
		$resultPlatformUser = array_pop ( $resultPlatformUser );
		
		$token_id = $resultPlatformUser ["user_token_id"];
		$token = $resultPlatformUser ["user_token"];
		$seller_id = $resultPlatformUser ["seller_id"];
		$site = $resultPlatformUser ["site"];
		
		$company_code = $resultPlatformUser ['company_code'];
		$account = $resultPlatformUser ['user_account'];
		
		$svc = new Aliexpress_Order_OrderService ();
		
		$svc->setCompanyCode ( $company_code );
		$svc->setUserAccount ( $account );
		// 下载订单列表
		$orderListRs = $svc->orderListQuery ( $start, $end );
		if ($orderListRs ['ask']) {
			$orderArr = $orderListRs ['orderArr'];
			$this->_orderArr = $orderArr;
			Common_ApiProcess::log("下载订单列表成功,在时间段{$start}~{$end}之内，共" . count ( $orderArr ) . "个订单");
			foreach ( $orderArr as $k => $order ) { 
				$order_id = $order ['order_id'];
				$aoo_id = $order ['aoo_id'];
				// 下载明细
				$svcOrder = new Aliexpress_Order_OrderService ();
				$svc->setCompanyCode ( $order ['company_code'] );
				$svc->setUserAccount ( $order ['user_account'] );
				$orderDetailRs = $svc->orderDetailQuery ( $order_id );
				$orderArr [$k] ['orderDetailRs'] = $orderDetailRs;
				// $orderReceipt = $svc->getOrderReceiptInfoById ( $order_id );
				if ($orderDetailRs ['ask']) { // 下载明细成功
					Common_ApiProcess::log( "[{$order_id}]下载订单明细成功");
				} else { 
					throw new Exception ( "[{$order_id}]下载订单明细失败，失败原因:" . $orderDetailRs ['message'] );
					 
				}				 
			}
			$orderListRs ['orderArr'] = $orderArr;
		} else {
			throw new Exception ( "下载订单列表失败，失败原因:" . $orderListRs ['message'] );
		} 
	}
	public function getOrderData(){
		return $this->_order_data;
	}
	
	
	public function orderListQuery($user_account='',$company_code='',$start='',$end='',$break=false) {
		$return = array (
				'ask' => 0,
				'message' => Ec::Lang('订单下载失败') ,
				'msgArr'=>array()
		);
		$msgArr = array();
		try {
// 			//test start
// 			$user_account = 'gizga_aliexpress1';
// 			$company_code='10000005';
// 			$start = '2014-10-04 08:53:27';
// 			$end = '2014-10-05 08:29:42';
// 			//test end
			
			$con = array (
					'platform' => 'aliexpress',
					'status' => 1,
					'user_account' => $user_account,
					'company_code'=>$company_code,
			);
			
			if(empty($user_account)||empty($company_code)){
				throw new Exception('账号/客户代码未设置');
			}

			if(empty($start)||empty($end)){
				throw new Exception('必须选定起始/结束时间');
			}
			$resultPlatformUser = Service_PlatformUser::getByCondition ( $con );
			if(empty($resultPlatformUser)){
				throw new Exception('用户不存在');
			}
			// print_r($resultPlatformUser);exit;
			$resultPlatformUser = array_pop ( $resultPlatformUser );
			
			$token_id = $resultPlatformUser ["user_token_id"];
			$token = $resultPlatformUser ["user_token"];
			$seller_id = $resultPlatformUser ["seller_id"];
			$site = $resultPlatformUser ["site"];
			
			$company_code = $resultPlatformUser ['company_code'];
			$account = $resultPlatformUser ['user_account'];
			
			$svc = new Aliexpress_Order_OrderService ();
			
			$svc->setCompanyCode ( $company_code );
			$svc->setUserAccount ( $account );
			//下载订单列表
			$orderListRs = $svc->orderListQuery ( $start, $end );
			if($orderListRs['ask']){
				$orderArr = $orderListRs ['orderArr'];
				$this->_msg[] = "下载订单列表成功,在时间段{$start}~{$end}之内，共".count($orderArr)."个订单";
				foreach ( $orderArr as $k=> $order ) {
					$msgStr = '';
					$order_id = $order ['order_id'];
					$aoo_id = $order ['aoo_id'];
					//下载明细
					$svcOrder = new Aliexpress_Order_OrderService ();			
					$svc->setCompanyCode ( $order ['company_code'] );
					$svc->setUserAccount ( $order ['user_account'] );
					$orderDetailRs = $svc->orderDetailQuery ( $order_id );
					$orderArr[$k]['orderDetailRs'] = $orderDetailRs;
					// 				$orderReceipt = $svc->getOrderReceiptInfoById ( $order_id );
					if($orderDetailRs['ask']){//下载明细成功
						$this->_msg[] = "[{$order_id}]下载订单明细成功";
						//生成订单
						$genProcess = new Aliexpress_Order_GenOrder();
						$genProcess->setAooId($aoo_id);
						$genOrderRs = $genProcess->genOrder();
						if($genOrderRs['ask']){
							$this->_msg[] = "[{$order_id}]生成平台订单成功";					
						}else{
							$this->_msg[] = "[{$order_id}]生成平台订单失败，失败原因:".$genOrderRs['message'];								
						}
						$orderArr[$k]['genOrderRs'] = $genOrderRs;
					}else{
						if($break){
							throw new Exception("[{$order_id}]下载订单明细失败，失败原因:".$orderDetailRs['message']);
						}
						$this->_msg[] = "[{$order_id}]下载订单明细失败，失败原因:".$orderDetailRs['message'];
					}
					
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
			$return['message'] = $e->getMessage();
		}
		$return['msgArr'] = $this->_msg;
		return $return;		
	}
	public function orderDetailQuery() {
		$con = array (
				'platform' => 'aliexpress',
				'status' => 1,
				'user_account' => 'gizga_aliexpress1' 
		);
		
		$resultPlatformUser = Service_PlatformUser::getByCondition ( $con );
		// print_r($resultPlatformUser);exit;
		$resultPlatformUser = array_pop ( $resultPlatformUser );
		
		$token_id = $resultPlatformUser ["user_token_id"];
		$token = $resultPlatformUser ["user_token"];
		$seller_id = $resultPlatformUser ["seller_id"];
		$site = $resultPlatformUser ["site"];
		
		$company_code = $resultPlatformUser ['company_code'];
		$account = $resultPlatformUser ['user_account'];
		
		$svc = new Aliexpress_Order_OrderService ();
		
		$svc->setCompanyCode ( $company_code );
		$svc->setUserAccount ( $account );
// 		$order_id = '65143668092700';
		$order_id = '64015486580783';
		$rs = $svc->orderDetailQuery ( $order_id ); 
	}
	public function getOrderReceiptInfo() {
		$con = array (
				'platform' => 'aliexpress',
				'status' => 1,
				'user_account' => 'gizga_aliexpress1' 
		);
		
		$resultPlatformUser = Service_PlatformUser::getByCondition ( $con );
		// print_r($resultPlatformUser);exit;
		$resultPlatformUser = array_pop ( $resultPlatformUser );
		
		$token_id = $resultPlatformUser ["user_token_id"];
		$token = $resultPlatformUser ["user_token"];
		$seller_id = $resultPlatformUser ["seller_id"];
		$site = $resultPlatformUser ["site"];
		
		$company_code = $resultPlatformUser ['company_code'];
		$account = $resultPlatformUser ['user_account'];
		
		$svc = new Aliexpress_Order_OrderService ();
		
		$svc->setCompanyCode ( $company_code );
		$svc->setUserAccount ( $account );
// 		$order_id = '64074175532222';
		$order_id = '651449417493000';
// 		$order_id = '64224935651652';
// 		$order_id = '65187779990308';
		$rs = $svc->getOrderReceiptInfoById ( $order_id );
		Ec::showError ( print_r ( $rs, true ), '____aliexpress_o0101' );
	}
}