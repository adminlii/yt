<?php
class Pickup_UserAddressController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "pickup/views/user-address/";
        $this->serviceClass = new Service_UserAddress();
    }

    public function listAction()
    {
        if($this->_request->isPost()){
           $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);

            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;

            $return = array(
                "state" => 0,
                "message" => "No Data"
            );

            $params = $this->_request->getParams();

            $condition = $this->serviceClass->getMatchFields($params);
            $condition['customer_id'] = Service_User::getCustomerId();
    		$condition['customer_channelid'] = Service_User::getChannelid();
           
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
//             print_r($count);exit;
            $return['total'] = $count;

            if ($count) {
                $showFields=array(
                	'address_id',
                	'shipper_name',
                	'shipper_company',
                	'shipper_countrycode',
                	'shipper_province',
                	'shipper_city',
                	'shipper_street',
                	'shipper_postcode',
                	'shipper_telephone',
                	'shipper_mobile',
                	'shipper_email',
                	'shipper_certificatetype',
                	'shipper_certificatecode',
                	'shipper_fax',
                	'shipper_mallaccount',
                	'is_default',
                );
                
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition,$showFields, $pageSize, $page, array('address_id asc'));
                $lang = Ec::getLang(1);
                foreach ($rows as $key => $value) {
                	//国家
                    $value['E4'] = empty($value['E4'])?'':$value['E4'];
                	$result_country = Service_Country::getByField($value['E4'],'country_code');
                	if(!empty($result_country)){
                		$rows[$key]['E4_title'] = $result_country['country_name' . $lang];
                	}
                	
                	$value['E13'] = empty($value['E13'])?'':$value['E13'];
//                 	//证件类型
                	$result_CertificateType = Service_AtdCertificateType::getByField($value['E13'],'certificate_type');
                	if(!empty($result_CertificateType)){
                		$lang_tmp = ($lang == '')?'_cn':'_en';
                		$rows[$key]['E13_title'] = $result_CertificateType['certificate_type'.$lang_tmp.'name'];
                	}
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            $this->view->return = Zend_Json::encode($return);
            die(Zend_Json::encode($return));
        }
        echo Ec::renderTpl($this->tplDirectory . "submiter_list.tpl", 'layout');
    }

    /**
     * 设置默认发件人
     */
    public function setDefaultAction(){
    	$return = array(
    			"state" => 0,
    			"message" => "No Data"
    	);
    	
    	if($this->_request->isPost()){
    		$id = $this->_request->getParam('paramid','');
    		
    		Service_CsiShipperTrailerAddress::update(array('is_default'=>'0'), Service_User::getCustomerId(), 'customer_id');
    		Service_CsiShipperTrailerAddress::update(array('is_default'=>'1'), $id, 'address_id');
    		
    		$return['state'] = 1;
    		$return['message'] = 'Success';
    		die(Zend_Json::encode($return));
    	}
    }
    
    /**
     * 编辑
     */
    public function editAction()
    {
    	if ($this->_request->isPost()) {
    	    $return = array(
    	            'state' => 0,
    	            'message' => '',
    	            'errorMessage'=>array('Fail.')
    	    );
    		$params = $this->_request->getParams();
    		$row = array (
					'address_id' => $params ['address_id'],
					'customer_id' => Service_User::getCustomerId(),
					'address_name' => $params ['address_name'],
					'state' => $params ['state'],
					'city' => $params ['city'],
					'district' => $params ['district'],
					'postal_code' => $params ['postal_code'],
					'street' => $params ['street'].'*#*'.$params ['street1'],
					'contact' => $params ['contact'],
					'phone' => $params ['phone'],
					'is_default' => $params ['is_default'],
					'pickup_og_id' => $params ['pickup_og_id'],
					'pickup_range_id' => $params ['pickup_range_id'],
					'is_modify' => ''
			);
    		 
    		$paramId = $row['address_id'];
    		if (!empty($row['address_id'])) {
    			unset($row['address_id']);
    		} 
    		$row = Common_Common::arrayNullToEmptyString($row);
    		$format = 'Y-m-d H:i:s';
    		$row['modify_date_sys'] = date($format);
    		$row['customer_id'] = Service_User::getCustomerId(); 
    		if (!empty($paramId)) {
	    		$row['is_modify'] = '1';
    			$result = $this->serviceClass->update($row, $paramId);
    			$address_id = $paramId;
    		} else {
    			$row['create_date_sys'] = date($format);
    			$result = $this->serviceClass->add($row);
    			$address_id = $result;
    		}

    		if($row['is_default']){
    		    $this->serviceClass->update(array('is_default'=>'0'), Service_User::getCustomerId(), 'customer_id');
    		    $this->serviceClass->update(array('is_default'=>'1'), $address_id, 'address_id');
    		}
    		
    		if ($result) {
    			$return['state'] = 1;
    			$return['message'] = array('Success.');
    		}
    	    die(Zend_Json::encode($return));
    
    	}
        echo Ec::renderTpl($this->tplDirectory . "user_address_create.tpl", 'layout');
    }
    
    public function getByJsonAction()
    {
    	$result = array('state' => 0, 'message' => 'Fail', 'data' => array());
    	$paramId = $this->_request->getParam('paramId', '');
    	if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'address_id')) {
    		//$rows=$this->serviceClass->getVirtualFields($rows);
    		if($rows){
    			$street = explode('*#*', $rows['street']);
    			$rows['street'] = $street[0];
    			$rows['street1'] = $street[1];
    		}
    		$result = array('state' => 1, 'message' => '', 'data' => $rows);
    	}
    	die(Zend_Json::encode($result));
    }
    
    public function deleteAction()
    {
    	$result = array(
    			"state" => 0,
    			"message" => "Fail."
    	);
    	if ($this->_request->isPost()) {
    		$paramId = $this->_request->getPost('paramId');
    		if (!empty($paramId)) {
    			if ($this->serviceClass->delete($paramId)) {
    				$result['state'] = 1;
    				$result['message'] = 'Success.';
    			}
    		}
    	}
    	die(Zend_Json::encode($result));
    } 
    public function getPickupRangAction(){
//     	$sql = "select distinct state from pickup_range;";
//     	$row = Common_Common::fetchAll($sql);

    	// TODO DB2
    	$db2 = Common_Common::getAdapterForDb2();

//     	$sql = "select * from pickup_range order by pickup_range_id desc;";
    	$sql = "select * from pickup_range;";
    	$rows = $db2->fetchAll($sql);
    	$data = array();
    	foreach($rows as $v){
    		$data[$v['state']][$v['city']][$v['district']][] = $v;
    	}
//     	print_r($data);exit;
    	echo Zend_Json::encode($data);
    }
    
    public function forPickupAction(){
//     	$this->getPickupRangAction();exit;
        echo Ec::renderTpl($this->tplDirectory . "user_address_create_for_pickup.tpl", 'layout');
    }
    
}