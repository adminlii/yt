<?php
class Order_FeeTrailController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "order/views/fee-trail/";
        $this->serviceClass = new Service_CsiShipperTrailerAddress();
    }

    public function listAction()
    {
    	set_time_limit(0);
    	if($this->_request->isPost()){
			$return = array (
					'ask' => 0,
					'state' => 0,
					'message' => '试算失败' 
			);
    		$timeout = 1000;
			$wsdl = Service_Config::getByField('TMS_FEE_TRAIL_WSDL', 'config_attribute');
			if(!$wsdl){
    			$wsdl = 'http://120.24.63.108:9001/APIServicesDelegate?wsdl';
    		}else{
    			$wsdl = $wsdl['config_value'];
    		}
    		$options = array (
    				"trace" => true,
    				"connection_timeout" => $timeout,
//     				"location" => $wsdl,
    				// "exceptions" => true,
    				// "soap_version" => SOAP_1_1,
    				// "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
    				// "stream_context" => $streamContext,
    				"encoding" => "utf-8"
    		);

    		$client = new SoapClient($wsdl, $options);
    		$tms_id = '1';
    		$customer_id = '1';
    		$weight = $this->getParam('weight',0.2);
    		$country_code = $this->getParam('country_code','');
    		$org_area = '';
    		$length = $this->getParam('length','');
    		$width =  $this->getParam('width','');
    		$height =  $this->getParam('height','');
    		$cargo_type = "";
    		if(empty($country_code)){
    			throw new Exception(Ec::Lang('国家必选'));
    		}	
    		if(empty($weight)){
    			throw new Exception(Ec::Lang('重量必填'));
    		}	
    		$req = array (
					'strTms_id' => Service_User::getTmsId (),
					'strCustomer_id' => Service_User::getCustomerId (),
					'strWeight' => $weight,
					'strCountry_code' => $country_code,
					'strOg_id_pickup' => $org_area,
					'strLength' => $length,
					'strWidth' => $width,
					'strHeight' => $height,
					'strCargo_type' => $cargo_type 
			);
    		try {
    			// 			$rs = $client->AttemptCalculate($tms_id,$customer_id,$weight,$country_code,$org_area,$length,$width,$height,$cargo_type);
    			$rs = $client->AttemptCalculate($req);
    			$json = $rs->AttemptCalculateResult; 
    			$data = json_decode ( $json, true );    			
    			if($data){
    				$return['total'] = count($data);
    				$return ['ask'] = 1;
    				$return['state'] = 1;
    				$return ['message'] = 'Success';
    				$return ['data'] = $data;
    				$return['country_code'] = $country_code;
    			}else{
    				throw new Exception('试算失败，系统暂未支持该国家或重量段');
    			}
    		} catch (Exception $e) {
    			$return['total'] = count($data);
    			$return ['ask'] = 0;
    			$return['state'] = 1;
    			$return['message'] = $e->getMessage();
//     			echo $e->getMessage().'__'.__LINE__;
    		}
			echo Zend_Json::encode ( $return );
			exit ();
    	}
    	$country = Common_DataCache::getCountry();
//     	print_r($country);exit;
    	$this->view->country = $country;
        echo  Ec::renderTpl($this->tplDirectory . "fee-trail.tpl", 'layout');
    }
 
    
}