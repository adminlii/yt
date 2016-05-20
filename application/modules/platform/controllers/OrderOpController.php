<?php
/**
 * 订单操作
 * @author Administrator
 *
 */
class Platform_OrderOpController extends Ec_Controller_Action {
	public function preDispatch() {
		$this->tplDirectory = "platform/views/order-op/";
		$this->serviceClass = new Service_Orders();

	}
	
	/**
	 * 暂存
	 */
	public function holdAction(){
	    $ref_ids = $this->getParam('ref_id',array());
	    $process = new Platform_OrderProcess();
	    $return = $process->order_hold($ref_ids);
	    echo Zend_Json::encode($return);
	}

	/**
	 * 转待发货审核
	 */
	public function draftAction(){
	    $ref_ids = $this->getParam('ref_id',array());
	    $process = new Platform_OrderProcess();
	    $return = $process->order_draft($ref_ids);
	    echo Zend_Json::encode($return);
	}
	
	/**
	 * 审核
	 */
	public function verifyAction(){
	    $ref_ids = $this->getParam('ref_id',array());
	    $shipper_account = $this->getParam('shipper_account','');
	    $product_code = $this->getParam('product_code','');
	    $status = $this->getParam('type','');
	    $warehouse_code = $this->getParam('warehouse_code','');
	    
	    $process = new Platform_OrderProcess();
	    $return = $process->order_verify($ref_ids, $shipper_account, $product_code,$warehouse_code,$status);
// 	    print_r($return);exit;
	    echo Zend_Json::encode($return);
	}
	

	/**
	 * 标记发货
	 */
	public function shipMarkAction(){
	    $order_arr = $this->getParam('order',array());
// 	     print_r($order_arr);exit;
	    $process = new Platform_OrderProcess();
	    $return = $process->order_ship_mark($order_arr);
	    // 	    print_r($return);exit;
	    echo Zend_Json::encode($return);
	}

	/**
	 * ebay标记发货
	 */
	public function completeSaleAction(){
	    $order_arr = $this->getParam('order',array());
	    // 	     print_r($order_arr);exit;
	    $process = new Platform_OrderProcess();
	    $return = $process->order_complete_sale($order_arr);
	    // 	    print_r($return);exit;
	    echo Zend_Json::encode($return);
	}
	
	/**
	 * 手工拉单
	 */
	public function loadPlatformOrderAction(){
	    $company_code = Service_User::getCustomerCode();
	    $user_account = $this->getParam('user_account','');	  
	    $start = $this->getParam('start_time','');	  
	    $end = $this->getParam('end_time','');	  
	    $process = new Platform_OrderProcess();
// 	    exit;
	    $return = $process->load_platform_order($user_account, $company_code, $start, $end,7);
//     	    print_r($return);exit;
	    echo Zend_Json::encode($return);exit;
// 	    $this->view->return = $return;
		echo $this->view->render($this->tplDirectory.'load_platform_order_aliexpress.js');exit;
	}

	/**
	 * 分配运输方式
	 */
	public function allotAction(){
	    $order_arr = $this->getParam('order',array());
// 	     print_r($order_arr);exit;
	    $process = new Platform_OrderProcess();
	    $return = $process->order_allot($order_arr);
	    // 	    print_r($return);exit;
	    echo Zend_Json::encode($return);
	}
	
	public function tAction(){
	    $con = array();
	    $data = Service_AliexpressOrderOriginal::getByCondition($con,'*',1,1);
// 	    var_dump($data);exit;
	    foreach($data as $d){
	        unset($d['aoo_id']);
	        $d['order_id'] = 0+Common_Common::random(15,1);
// 	        var_dump($d);exit;
	        Service_AliexpressOrderOriginal::add($d);
	    }

	    $data = Service_AliexpressOrderOriginal::getByCondition($con);
	    print_r($data);
	}

	/**
	 * 手动触发更新订单关键字服务
	 */
	public function cronUpdateOrderKeywordAction(){
		Zend_Registry::set('SAPI_DEBUG',true);
		$return = Service_OrderKeywordProcess::cronUpdateOrderKeyword();
	}
}