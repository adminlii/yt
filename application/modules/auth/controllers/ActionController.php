<?php
class Auth_ActionController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/user/";
        $this->serviceClass = new Service_UserRightAction();
    }

    public function listAction()
    {
        $language = Ec::getLang();
        $displayArr = Common_Type::display($language);
        $statusArr = Common_Type::status($language);
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
                $showFields = array(

                    'ura_title',
                    'ura_title_en',
                    'ura_title_alias',
                    'ura_status',
                    'ura_display',
                    'ura_module',
                    'ura_controller',
                    'ura_action',
                    'ura_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array('ura_id desc'));
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['display'] = $displayArr;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }

        $this->view->displayArr = $displayArr;
        $this->view->statusArr = $statusArr;
        $this->view->module = Service_UserRightAction::getModule();
        echo Ec::renderTpl($this->tplDirectory . "action_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array('Fail.')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $row = array(

                'ura_id' => '',
                'ura_title' => '',
                'ura_title_en' => '',
                'ura_title_alias' => '',
                'ura_status' => '',
                'ura_display' => '',
                'ura_module' => '',
                'ura_controller' => '',
                'ura_action' => '',
            );
            $row = $this->serviceClass->getMatchEditFields($params, $row);
            $paramId = $row['ura_id'];
            if (!empty($row['ura_id'])) {
                unset($row['ura_id']);
            }
            $errorArr = $this->serviceClass->validator($row);

            if (!empty($errorArr)) {
                $return = array(
                    'state' => 0,
                    'message' => '',
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
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'ura_id')) {
            $rows = $this->serviceClass->getVirtualFields($rows);
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