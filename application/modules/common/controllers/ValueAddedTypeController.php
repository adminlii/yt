<?php
class Common_ValueAddedTypeController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "common/views/value-added/";
        $this->serviceClass = new Service_ValueAddedType();
    }

    public function listAction()
    {
    	$effectiveStatus = Common_Status::effectiveStatus();
    	$businessType = Common_Type::businessType();
    	
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

            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields=array(
                    
                'vat_code',
                'vat_business_type',
                'vat_status',
                'vat_name_en',
                'vat_name_cn',
                'vat_note',
                'vat_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition,$showFields, $pageSize, $page, array('vat_id desc'));
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
                $return['effectiveStatus'] = $effectiveStatus;
                $return['businessType'] = $businessType;
            }
            die(Zend_Json::encode($return));
        }
        
        $this->view->effectiveStatus = $effectiveStatus;
        $this->view->businessType = $businessType;
        echo Ec::renderTpl($this->tplDirectory . "value_added_type_index.tpl", 'layout');
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
                
              'vat_id'=>'',
              'vat_code'=>'',
              'vat_business_type'=>'',
              'vat_status'=>'',
              'vat_name_en'=>'',
              'vat_name_cn'=>'',
              'vat_note'=>'',
            );
            $row=$this->serviceClass->getMatchEditFields($params,$row);
            $paramId = $row['vat_id'];
            if (!empty($row['vat_id'])) {
                unset($row['vat_id']);
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
            
            // 验证代码重复
            $errorArr = $this->serviceClass->validatorRepeat($row, $paramId);
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
                $return['message'] = array('Success.');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'vat_id')) {
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