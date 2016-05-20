<?php
class Amazon_Order_OrderServiceProcess extends Ec_AutoRun
{

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
	

	public function loadAmazonOrder($loadId)
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
			$rs = $this->getOrderListCli($start,$end);
			//文件日志
			Ec::showError(print_r($rs,true),'amazon_load_order_'.date('Y-m-d_'));
			 
			$orderCount = count($this->_orderArr);
			$this->countLoad($loadId, 2, $orderCount); // 运行结束
	
			$return['ask'] = 1;
			$return['message'] = "amazon Time : " . $start . " ~ " . $end . ',' . $userAccount . ' Order Count ' . $orderCount;
		}catch(Exception $e){
			$this->countLoad($loadId, 3, 0);
			Ec::showError("账号：" . $userAccount . '发生错误，eBay时间：' . $start . ' To ' . $end . ',错误原因：' . $e->getMessage(), 'runOrder_Fail_');
			$return['message'] = $e->getMessage();
		}
		return $return;
	}

	public function getOrderListCli($start='',$end=''){
		$con = array (
				'platform' => 'amazon',
				'status' => 1,
				'user_account' => $this->_user_account,
				'company_code' => $this->_company_code 
		);
		$resultPlatformUser = Service_PlatformUser::getByCondition ( $con );
		if (empty ( $resultPlatformUser )) {
			throw new Exception ( '用户不存在/未激活' );
		}
		// print_r($resultPlatformUser);exit;
		$resultPlatformUser = array_pop ( $resultPlatformUser );
		
		$this->token_id = $resultPlatformUser ["user_token_id"];
		$this->token = $resultPlatformUser ["user_token"];
		$this->seller_id = $resultPlatformUser ["seller_id"];
		$this->site = $resultPlatformUser ["site"];
		
		$this->_company_code = $resultPlatformUser ['company_code'];
		$this->_user_account = $resultPlatformUser ['user_account'];
		
		$svc = new Amazon_Order_OrderService ( $this->token_id, $this->token, $this->seller_id, $this->site );
		
		$svc->setCompanyCode ( $this->_company_code );
		$svc->setUserAccount ( $this->_user_account );
		
		$orderList = $svc->getOrderList ( $start, $end );
		if ($orderList ['ask']) {
			$orderArr = $orderList ['orderArr'];
			//订单数据
			$this->_orderArr = $orderArr;
			Common_ApiProcess::log ( "下载订单列表成功，在时间段{$start}~{$end}之内，共有" . count ( $orderArr ) . "个订单" );
			foreach ( $orderArr as $k => $order ) {
				$amazon_order_id = $order ['amazon_order_id'];
				// 明细
				$svcItem = new Amazon_Order_OrderItemService ( $this->token_id, $this->token, $this->seller_id, $this->site );
				$svcItem->setCompanyCode ( $this->_company_code );
				$svcItem->setUserAccount ( $this->_user_account );
				$rsItem = $svcItem->getOrderItemList ( $amazon_order_id );
				
				if ($rsItem ['ask']) {
					Common_ApiProcess::log ( "[{$amazon_order_id}]订单明细下载成功" );
				} else {
					throw new Exception ( "[{$amazon_order_id}]订单明细下载失败，失败原因：" . $rsItem ['message'] );
				}
				$order ['rsItem'] = $rsItem;
				$orderArr [$k] = $order;
			}
			$orderList ['orderArr'] = $orderArr;
		} else {
			throw new Exception ( "下载订单失败，失败原因：" . $orderList ['message'] );
		}		 
	}
	
	public function getOrderData(){
		return $this->_order_data;
	}
	
	public function getOrderList($user_account='',$company_code='',$start='',$end='',$break=false){
// // 		test start
// 		$user_account = 'DEZhigao_s@yahoo.com';
// 		$company_code = '';
// 		$start = '2014-12-20';
// 		$end = '2014-12-21';
// // 		test end
// 		echo urldecode('2014-12-30T02%3A54%3A27%2B0000');exit;
// 		echo __LINE__;exit;
// 		Amazon_Order_GenOrder::genOrderBatch();exit;
		$return = array (
				'ask' => 0,
				'message' => 'Fail.',
				'msgArr' => array (),
				'orderList'=>array()
		);
		$msgArr = array ();
		try {
				
			$con = array (
					'platform' => 'amazon',
					'status' => 1,
					'user_account' => $user_account,
					'company_code' => $company_code
			); 
			$resultPlatformUser = Service_PlatformUser::getByCondition ( $con );
			if(empty($resultPlatformUser)){
				throw new Exception('用户不存在/未激活');
			}
			// print_r($resultPlatformUser);exit;
			$resultPlatformUser = array_pop ( $resultPlatformUser );
				
			$this->token_id = $resultPlatformUser ["user_token_id"];
			$this->token = $resultPlatformUser ["user_token"];
			$this->seller_id = $resultPlatformUser ["seller_id"];
			$this->site = $resultPlatformUser ["site"];
				
			$this->_company_code = $resultPlatformUser ['company_code'];
			$this->_user_account = $resultPlatformUser ['user_account'];
				
			$svc = new Amazon_Order_OrderService ( $this->token_id, $this->token, $this->seller_id, $this->site );
				
			$svc->setCompanyCode ( $this->_company_code );
			$svc->setUserAccount ( $this->_user_account );
				
			$orderList = $svc->getOrderList ( $start, $end );
			if ($orderList ['ask']) {
				$orderArr = $orderList ['orderArr'];
				$msgArr [] = "下载订单成功，在时间段{$start}~{$end}之内，共有" . count ( $orderArr ) . "个订单";
				foreach ( $orderArr as $k=> $order ) {
					$amazon_order_id = $order ['amazon_order_id'];
					// 明细
					$svcItem = new Amazon_Order_OrderItemService ( $this->token_id, $this->token, $this->seller_id, $this->site );
					$svcItem->setCompanyCode($this->_company_code);
					$svcItem->setUserAccount($this->_user_account);
					$rsItem = $svcItem->getOrderItemList ( $amazon_order_id );
					
					if ($rsItem ['ask']) {
						$msgArr [] = "[{$amazon_order_id}]订单明细下载成功";
						$aoo_id = $order['aoo_id'];
						$genOrderSvc = new Amazon_Order_GenOrder();
						$genOrderSvc->setAooId($aoo_id);
						// 生成订单
						$rsGenOrder = $genOrderSvc->genOrder();
						$rsItem['rsGenOrder'] = $rsGenOrder;
						if ($rsGenOrder ['ask']) {
							$msgArr [] = "[{$amazon_order_id}]订单生成成功";
						} else {								
							$msgArr [] = "[{$amazon_order_id}]订单生成失败，失败原因：" . $rsGenOrder ['message'];
						}
					} else {
						if($break){
							throw new Exception("[{$amazon_order_id}]订单明细下载失败，失败原因：" . $rsItem ['message']);
						}
						// 明细下载失败
						$msgArr [] = "[{$amazon_order_id}]订单明细下载失败，失败原因：" . $rsItem ['message'];
					}
					$order['rsItem'] = $rsItem;
					$orderArr[$k] = $order;
				}
				$orderList['orderArr'] = $orderArr;
			} else {
				if($break){
					throw new Exception("下载订单失败，失败原因：" . $orderList ['message']);
				}
				$msgArr [] = "下载订单失败，失败原因：" . $orderList ['message'];
			}
			$return ['ask'] = 1;
			$return ['message'] = 'Finish';
			$return ['orderList'] = $orderList;
		} catch ( Exception $e ) {
			$msgArr [] = "下载订单失败，失败原因：" . $e->getMessage ();
			$return ['message'] = $e->getMessage ();
		}
		$return ['msgArr'] = $msgArr;
		return $return;
	}
	
	public function getOrderByOrderIdArr($user_account='',$company_code='',$idArr=array('1231')){ 
		// // 		test start
		// 		$user_account = 'DEZhigao_s@yahoo.com';
		// 		$company_code = '';
		// 		$start = '2014-12-20';
		// 		$end = '2014-12-21';
// 		$idArr = array(
// '028-2742057-1673943',	
// 		);
		// // 		test end
		$return = array (
				'ask' => 0,
				'message' => 'Fail.',
				'msgArr' => array (),
				'orderList'=>array()
		);
		$msgArr = array ();
		try {

			$con = array (
					'platform' => 'amazon',
					'status' => 1,
					'user_account' => $user_account,
					'company_code' => $company_code
			); 
			
			$resultPlatformUser = Service_PlatformUser::getByCondition($con);
			// 		print_r($resultPlatformUser);exit;
			if(empty($resultPlatformUser)){
				throw new Exception('用户不存在/未激活');
			}
			if(empty($idArr)){
				throw new Exception('idArr没有传入参数');
			}
			if(count($idArr)>50){
				throw new Exception('idArr最多支持50个订单ID');
			}
			$resultPlatformUser = array_pop($resultPlatformUser);
			
			$this->token_id = $resultPlatformUser["user_token_id"];
			$this->token = $resultPlatformUser["user_token"];
			$this->seller_id = $resultPlatformUser["seller_id"];
			$this->site = $resultPlatformUser["site"];
			
			$this->_company_code = $resultPlatformUser['company_code'];
			$this->_user_account = $resultPlatformUser['user_account'];
			
			$svc = new Amazon_Order_OrderService($this->token_id, $this->token, $this->seller_id, $this->site);
			
			$svc->setCompanyCode($this->_company_code);
			$svc->setUserAccount($this->_user_account);
			$orderList = $svc->getOrderByOrderIdArr($idArr);
			if ($orderList ['ask']) {
				$orderArr = $orderList ['orderArr'];
				$msgArr [] = "下载订单成功，共有" . count ( $orderArr ) . "个订单";
				foreach ( $orderArr as $k=> $order ) {
					$amazon_order_id = $order ['amazon_order_id'];
					// 明细
					$svcItem = new Amazon_Order_OrderItemService ( $this->token_id, $this->token, $this->seller_id, $this->site );
					$svcItem->setCompanyCode($this->_company_code);
					$svcItem->setUserAccount($this->_user_account);
					$rsItem = $svcItem->getOrderItemList ( $amazon_order_id );
						
					if ($rsItem ['ask']) {
						$msgArr [] = "[{$amazon_order_id}]订单明细下载成功";
						$aoo_id = $order['aoo_id'];
						$genOrderSvc = new Amazon_Order_GenOrder();
						$genOrderSvc->setAooId($aoo_id);
						// 生成订单
						$rsGenOrder = $genOrderSvc->genOrder();
						$rsItem['rsGenOrder'] = $rsGenOrder;
						if ($rsGenOrder ['ask']) {
							$msgArr [] = "[{$amazon_order_id}]订单生成成功";
						} else {
							$msgArr [] = "[{$amazon_order_id}]订单生成失败，失败原因：" . $rsGenOrder ['message'];
						}
					} else {
						// 明细下载失败
						$msgArr [] = "[{$amazon_order_id}]订单明细下载失败，失败原因：" . $rsItem ['message'];
					}
					$order['rsItem'] = $rsItem;
					$orderArr[$k] = $order;
				}
				$orderList['orderArr'] = $orderArr;
			} else {
				$msgArr [] = "下载订单失败，失败原因：" . $orderList ['message'];
			}

			$return ['ask'] = 1;
			$return ['message'] = 'Finish';
			$return ['orderList'] = $orderList;
		} catch ( Exception $e ) {
			$msgArr [] = "下载订单失败，失败原因：" . $e->getMessage ();
			$return ['message'] = $e->getMessage ();
		}
		$return ['msgArr'] = $msgArr;
		return $return;
		
	}
	/**
	 * test
	 */
	public function getOrderItemsByOrderId(){
		$token_id = '';

		$con = array(
				'platform' => 'amazon',
				'status' => 1,
				'user_account'=>'DEZhigao_s@yahoo.com'
		);
		
		$resultPlatformUser = Service_PlatformUser::getByCondition($con); 
// 		print_r($resultPlatformUser);exit;
		$resultPlatformUser = array_pop($resultPlatformUser);
		
		$this->token_id = $resultPlatformUser["user_token_id"];
		$this->token = $resultPlatformUser["user_token"];
		$this->seller_id = $resultPlatformUser["seller_id"];
		$this->site = $resultPlatformUser["site"];
		
		$this->_company_code = $resultPlatformUser['company_code'];
		$this->_user_account = $resultPlatformUser['user_account'];
		
		$svc = new Amazon_Order_OrderItemService($this->token_id, $this->token, $this->seller_id, $this->site);

		$svc->setCompanyCode($this->_company_code);
		$svc->setUserAccount($this->_user_account);
		$idArr = array(
				'304-1622825-8159543',
				'304-5806595-7560353',
				'304-4607423-7698769',
				'305-1217830-7531534',
				'305-2144615-8509112',
				'303-2953210-7617104',
				'028-4749906-8577958',
				'302-0198960-3742704',
				'303-9840126-1685941',
				'302-3580989-2677113',
				 
		);
		$amazon_order_id = '304-1622825-8159543';
		$return = $svc->getOrderItemList($amazon_order_id);
		Ec::showError(print_r($return,true),'____amazon1');
	}
}