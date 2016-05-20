<?php
class Test_EubTestController extends Ec_Controller_Action
{
	public function preDispatch(){
	}

	/**
	 * 截单
	 */
    public function cargoHoldServiceAction() {
    	
    	$code = $this->_request->getParam("code", "");
    	
       	$obj = new API_Epacket_ForApiService();
       	$obj->setParam('EUBOFFLINE', $code,"","PK0043");
       
       	$return = $obj->cargoHoldService($code);
       	print_r($return);
    }

	/**
	 * 获取标签
	 */
    public function getLabelAction() {
    	
    	$return = array('state' => 0, 'message' => "fail.");
    	
    	if($this->_request->isPost()) { 
	    	$code = $this->_request->getParam("code", "");
	    	$api = $this->_request->getParam("api", "");
	    	
	    	$order = Service_CsdOrder::getByField($code, 'shipper_hawbcode');
	    	if(empty($order)) {
	    		$return['message'] ="单号不存在";
	    		die(Zend_Json::encode($return));
	    	}
	    	
	       	$obj = new API_Epacket_ForApiService();
	       	$obj->setParam($api, $code,"",$order['product_code']);
	       	$return = $obj->getLabel($order['server_hawbcode'],$code);
	       	die(Zend_Json::encode($return));
    	}
    	
    	echo Ec::renderTpl("test/views/eub-test.tpl", 'layout');
    }
}