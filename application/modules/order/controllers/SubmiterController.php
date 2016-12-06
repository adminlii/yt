<?php
class Order_SubmiterController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "order/views/submiter/";
        $this->serviceClass = new Service_CsiShipperTrailerAddress();
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
                	'shipper_account',
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
                $rows = $this->serviceClass->getByCondition($condition,$showFields, $pageSize, $page, array('shipper_account asc'));
                $lang = Ec::getLang(1);
                foreach ($rows as $key => $value) {
                	//国家
                    $value['E4'] = empty($value['E4'])?'':$value['E4'];
                	$result_country = Service_IddCountry::getByField($value['E4'],'country_code');
                	if(!empty($result_country)){
                		$rows[$key]['E4_title'] = $result_country['country_cnname'];
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
    		Service_CsiShipperTrailerAddress::update(array('is_default'=>'1'), $id, 'shipper_account');
    		
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
    		$row = array(
    				'shipper_account'=>'',
    				'shipper_name'=>'',
    				'shipper_company'=>'',
    				'shipper_countrycode'=>'',
    				'shipper_province'=>'',
    				'shipper_city'=>'',
    				'shipper_street'=>'',
    				'shipper_postcode'=>'',
    				'shipper_telephone'=>'',
    				'is_default'=>'0',
    		);
    		
    		$row = $this->serviceClass->getMatchEditFields($params,$row);
    		
    		$paramId = $row['shipper_account'];
    		if (!empty($row['shipper_account'])) {
    			unset($row['shipper_account']);
    		}
    		//编辑
    		if (!empty($paramId)) {
    			$shipperInfo = $this->serviceClass->getByField($paramId, 'shipper_account');
    			if($shipperInfo['customer_id'] != Service_User::getCustomerId()){
    				$return = array(
    						'state' => 0,
    						'message'=>'非法操作',
    						'errorMessage'=>array('非法操作')
    				);
    				die(Zend_Json::encode($return));
    			}
    		}
    		
    		
    		foreach ($row as $key => $value) {
    			$row[$key] = ($value != '')?trim($value):$value;
    		}
    		$errorArr = $this->serviceClass->validator($row);
    		//加上过滤条件
    		if(!empty($row['shipper_name'])){
    			if(!preg_match('/^[a-zA-Z\s\.%&\(\)\{\},\$-;#@\*\[\]【】]+$/', $row['shipper_name'])) {
    				$errorArr[]= "发件人姓名不能为非英文";
    			}
    		}
    		if(!empty($row['shipper_company'])){
    			if(!preg_match('/^[a-zA-Z\s\.%&\(\)\{\},\$-;#@\*\[\]【】]+$/', $row['shipper_company'])) {
    				$errorArr[]= "发件人公司不能为非英文";
    			}
    		}
    			
    		if(!empty($row['shipper_province'])){
    			if(!preg_match('/^[a-zA-Z\s]+$/', $row['shipper_province'])) {
    				$errorArr[]= "发件人州省不能为非英文";
    			}
    		}
    		if(!empty($row['shipper_city'])){
    			if(!preg_match('/^[a-zA-Z\s]+$/', $row['shipper_city'])) {
    				$errorArr[]= "发件人城市不能为非英文";
    			}
    		}
    		
    		if(!empty($row['shipper_telephone'])){
    			if(!preg_match('/^(\d){4,25}$/', $row['shipper_telephone'])) {
    				$errorArr[]= "电话格式应为4-25位纯数字";
    			}
    		}
    		if(!empty($row['shipper_postcode'])){
    			if(!preg_match('/^[0-9]{6,12}$/', $row['shipper_postcode'])) {
    				$errorArr[]= "发件人邮编应为6-12位数字";
    			}
    		}
    		
    		if (!empty($errorArr)) {
    			$return = array(
    					'state' => 0,
    					'message'=>'',
    					'errorMessage' => $errorArr
    			);
    			die(Zend_Json::encode($return));
    		}
    		$row = Common_Common::arrayNullToEmptyString($row);
    		$format = 'Y-m-d H:i:s';
    		$row['modify_date_sys'] = date($format);
    		$row['customer_id'] = Service_User::getCustomerId();
    		$row['customer_channelid'] = Service_User::getChannelid();
    		if (!empty($paramId)) {
	    		$row['is_modify'] = '1';
    			$result = $this->serviceClass->update($row, $paramId);
    			$shipper_account = $paramId;
    		} else {
    			$row['create_date_sys'] = date($format);
    			$result = $this->serviceClass->add($row);
    			$shipper_account = $result;
    		}

    		if($row['is_default']){
    		    Service_CsiShipperTrailerAddress::update(array('is_default'=>'0'), Service_User::getCustomerId(), 'customer_id');
    		    Service_CsiShipperTrailerAddress::update(array('is_default'=>'1'), $shipper_account, 'shipper_account');
    		}
    		
    		if ($result) {
    			$return['state'] = 1;
    			$return['message'] = array('Success.');
    		}
    	    die(Zend_Json::encode($return));
    
    	}
        echo Ec::renderTpl($this->tplDirectory . "submiter_create.tpl", 'layout');
    }
    
    public function getByJsonAction()
    {
    	$result = array('state' => 0, 'message' => 'Fail', 'data' => array());
    	$paramId = $this->_request->getParam('paramId', '');
    	if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'shipper_account')) {
    		if($rows['customer_id'] != Service_User::getCustomerId()){
    			$result['message']='非法操作';
    		}else{
    			$rows=$this->serviceClass->getVirtualFields($rows);
    			$result = array('state' => 1, 'message' => '', 'data' => $rows);
    		}
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
    			$shipperInfo = $this->serviceClass->getByField($paramId, 'shipper_account');
    			if($shipperInfo['customer_id'] != Service_User::getCustomerId()){
    				$return = array(
    						'state' => 0,
    						'message'=>'非法操作',
    						'errorMessage'=>array('非法操作')
    				);
    				die(Zend_Json::encode($return));
    			}
    			
    			if ($this->serviceClass->delete($paramId)) {
    				$result['state'] = 1;
    				$result['message'] = 'Success.';
    			}
    		}
    	}
    	die(Zend_Json::encode($result));
    }
    
    public function forOrderAction(){
        echo Ec::renderTpl($this->tplDirectory . "submiter_create_for_order.tpl", 'layout');
    }
    
}