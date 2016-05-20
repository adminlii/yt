<?php
class Customer_StatementController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "customer/views/";
        $this->serviceClass = new Service_CustomerStatement();
    }

    public function listAction()
    {
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
            
            //客户代码
            $params['E1'] = $this->_request->getParam('customer_code', '');
            $params['E5'] = $this->_request->getParam('E5', '');
            $params['E6'] = $this->_request->getParam('E6', '');
            $params['E9'] = $this->_request->getParam('E9', '');
            $condition = $this->serviceClass->getMatchFields($params);
            
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields=array(
                    
                'customer_code',
                'cs_code',
                'reference_no',
                'cs_type',
                'cs_status',
                'cs_start_time',
                'cs_end_time',
                'cs_writeoff_sign',
                'cs_operator_id',
                'cs_finish_time',
                'cs_add_time',
                'cs_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition,$showFields, $pageSize, $page, array('cs_id desc'));
                
                $customerStatementType = Common_Type::customerStatementType();
                $customerStatementStatus = Common_Type::customerStatementStatus();
//                 print_r($customerStatementType);
//                 print_r($customerStatementStatus);
//                 exit;
                
                $resultUser = Service_User::getAll();
                $userArr = array();
                foreach ($resultUser as $keyUser => $valueUser) {
                	$userArr[$valueUser['user_id']] = $valueUser;
                }
                foreach ($rows as $key => $value) {
                	//账单类型
                	if(isset($customerStatementType[$value['E5']])){
                		$rows[$key]['cs_type_title'] = $customerStatementType[$value['E5']];
                	}
                	
                	//账单状态
                	if(isset($customerStatementStatus[$value['E6']])){
                		$rows[$key]['cs_status_title'] = $customerStatementStatus[$value['E6']];
                	}
                	
                	//操作人
                	if(isset($userArr[$value['E10']])){
                		$rows[$key]['cs_operator_title'] = $userArr[$value['E10']]['user_name'];
                	}
                }
                
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        echo Ec::renderTpl($this->tplDirectory . "customer_statement_index.tpl", 'layout');
    }
    
    public function getExportCsIdAction(){
    	//客户代码
        $params['E1'] = $this->_request->getParam('customer_code', '');
        $params['E5'] = $this->_request->getParam('E5', '');
        $params['E6'] = $this->_request->getParam('E6', '');
        $params['E9'] = $this->_request->getParam('E9', '');
        $condition = $this->serviceClass->getMatchFields($params);
          
        $result = $this->serviceClass->getByCondition($condition);
        die(Zend_Json::encode($result));
    }
    
    public function exportAction(){
    	set_time_limit(0);
    	ini_set('memory_limit', '500M');
    	$csIds = $this->_request->getParam('cs_id', array());
    	Service_CustomerStatementProcess::export($csIds);
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage'=>array('Fail.')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $row = array(
                
              'cs_id'=>'',
              'customer_code'=>'',
              'cs_code'=>'',
              'reference_no'=>'',
              'cs_type'=>'',
              'cs_status'=>'',
              'cs_writeoff_sign'=>'',
              'cs_operator_id'=>'',
              'cs_finish_time'=>'',
              'cs_add_time'=>'',
            );
            $row=$this->serviceClass->getMatchEditFields($params,$row);
            $paramId = $row['cs_id'];
            if (!empty($row['cs_id'])) {
                unset($row['cs_id']);
            }
            $errorArr = $this->serviceClass->validator($row);

            if (!empty($errorArr)) {
                $return = array(
                    'state' => 0,
                    'message'=>'',
                    'errorMessage' => $errorArr
                );
                die(Zend_Json::encode($return));
            }

            if (!empty($paramId)) {
                $result = $this->serviceClass->update($row, $paramId);
            } else {
                $result = $this->serviceClass->add($row);
            }
            if ($result) {
                $return['state'] = 1;
                $return['message'] = array('操作成功');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'cs_id')) {
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
                    $result['message'] = '操作成功';
                }
            }
        }
        die(Zend_Json::encode($result));
    }
}