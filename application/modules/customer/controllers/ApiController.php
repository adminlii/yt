<?php
class Customer_ApiController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "customer/views/default/";
        $this->serviceClass = new Service_CustomerApi();
    }

    public function listAction()
    {
        $user = Service_User::getUser();
        if ($this->_request->isPost()) {
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
            $condition['customer_code']= Service_User::getCustomerCode();
            if(!$user['is_admin']){
            	$condition['user_id'] = $user['user_id'];
            }
//             print_r($condition);exit;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields=array(
                'user_id',
                'customer_code',
                'ca_status',
                'ca_token',
                'ca_key',
                'ca_add_time',
                'ca_update_time',
                'ca_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition,$showFields, $pageSize, $page, array('ca_id desc'));
                foreach($rows as $k=>$v){
                	$user = Service_User::getByField($v['E8'],'user_id');
                	$v['user'] = $user;
                	$rows[$k]=$v;
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
		$con = array (
				'customer_id' => Service_User::getCustomerId () 
		);
		if(!$user['is_admin']){
			$con['user_id'] = $user['user_id'];
		}
        $users = Service_User::getByCondition($con);
        $this->view->users = $users;
        echo Ec::renderTpl($this->tplDirectory . "customer_api_index.tpl", 'layout');
    }

    public function editAction()
    {
		$return = array (
				'state' => 0,
				'message' => '',
				'errorMessage' => array (
						'Fail.' 
				) 
		);
		
		if ($this->_request->isPost ()) {
			try {
				
				$user_id = $this->getParam ( 'user_id', '' );
				
				if (! $user_id) {
					throw new Exception ( '没有选择对应人员' );
				}
				$customer_code = Service_User::getCustomerCode ();
				$row = array (
						'user_id' => $user_id,
						'customer_code' => $customer_code,
						'ca_token' => md5 ( $user_id . $customer_code . time () ),
						'ca_key' => md5 ( $user_id . $customer_code . time () ) . md5 ( strrev ( ($user_id . $customer_code . time ()) ) ) 
				);
				
				$con = array (
						'ca_token' => $row ['ca_token'],
						'ca_key' => $row ['ca_key'] 
				);
				$exist = Service_CustomerApi::getByField ( $user_id, 'user_id' );
				if (! empty ( $exist )) {					
					$row ['ca_update_time'] = now ();
					$result = $this->serviceClass->update ( $row, $user_id,'user_id' );
				} else {
					
					$row ['ca_add_time'] = now ();
					
					$row ['ca_update_time'] = now ();
					$result = $this->serviceClass->add ( $row );
				}
				
				if ($result) {
					$return ['state'] = 1;
					$return ['message'] = array (
							'Success.' 
					);
				}
			} catch ( Exception $e ) {
				$return ['message'] = $e->getMessage ();
			}
		}
		die ( Zend_Json::encode ( $return ) );
	}

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'ca_id')) {
            $rows=$this->serviceClass->getVirtualFields($rows);
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
}