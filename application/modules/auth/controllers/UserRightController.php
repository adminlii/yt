<?php
class Auth_UserRightController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/user/";
        $this->serviceClass = new Service_UserRight();
    }

    public function listAction()
    {
        $menuArray = Common_DataCache::getUserMenu();
        $displayArray = Common_Type::display('auto');
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
            $condition['ur_name_like']=isset($condition['ur_name'])?$condition['ur_name']:'';
            unset($condition['ur_name']);
//             print_r($condition);exit;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields = array(

                    'um_id',
                    'ur_name',
                    'ur_name_en',
                    'ur_sort',
                    'ur_url',
                    'ur_type',
                    'ur_module',
                    'ur_id',
                    'ur_common',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array('ur_id desc'));
                $language = Ec::getLang(1);
                foreach ($rows as $k => $v) {
                    $rows[$k]['E1'] = isset($menuArray[$v['E1']]['um_title' . $language]) ? $menuArray[$v['E1']]['um_title' . $language] : '';
                    $rows[$k]['E6'] = isset($displayArray[$v['E6']]) ? $displayArray[$v['E6']] : '';
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $menuArray = Service_UserMenu::getByCondition(array('parent_id'=>'0'),'*',0,0,array('um_sort'));
        foreach($menuArray as $key=> $val){
            $menuArray[$key]['submenu']=Service_UserMenu::getByCondition(array('parent_id'=>$val['um_id']),'*',0,0,array('um_sort'));
        }
        $this->view->menuArray = $menuArray;
        $this->view->displayArray = $displayArray;
        $this->view->module = Service_UserRightAction::getModule();
        echo Ec::renderTpl($this->tplDirectory . "right_index.tpl", 'layout');
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
                'ur_id' => '',
                'um_id' => '',
                'ur_name' => '',
                'ur_name_en' => '',
                'ur_description' => '',
                'ur_url' => '',
                'ur_type' => '',
                'ur_sort' => '',
            );
            $row = $this->serviceClass->getMatchEditFields($params, $row);
            $paramId = $row['ur_id'];
            if (!empty($row['ur_id'])) {
                unset($row['ur_id']);
            }
            $errorArr = $this->serviceClass->validator($row);
            $urlArr=explode('/',$row['ur_url']);
            $row['ur_module'] = isset($urlArr[1]) ? $urlArr[1] : 'common';
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
                $return['state'] = 2;
                $return['message'] = array('Success.');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'ur_id')) {
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
                    $result['state'] = 2;
                    $result['message'] = 'Success.';
                }
            }
        }
        die(Zend_Json::encode($result));
    }

    public function getActionAction()
    {
        $result = array(
            "state" => 0,
            "data" => array(),
            "ids" => array(),
            "message" => "Fail."
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            $language = Ec::getLang(1);
            $showFields = array(
                'ura_title' . $language . ' as title',
                'ura_module as module',
                'ura_controller as controller',
                'ura_action as action',
                'ura_id as id',
            );
            $actionAll = Service_UserRightAction::getByCondition(array('ura_module_gt' => 'default', 'ura_status' => 1), $showFields, 0, 0, array('ura_module'));
            $result['data'] = $actionAll;
            $rightUraIdArr = array();
            if (!empty($paramId)) {
                $rightAction = Service_UserRightActionMap::getByCondition(array('ur_id' => $paramId), 'ura_id');
                if (!empty($rightAction)) {
                    foreach ($rightAction as $v) {
                        $rightUraIdArr[$v['ura_id']] = $v['ura_id'];
                    }
                }
            }
            $result['ids'] = $rightUraIdArr;
            $result['state'] = 1;
        }
        die(Zend_Json::encode($result));
    }

    public function editRightAction()
    {
        $result = array(
            "state" => 0,
            "message" => Ec::Lang('operationFail')
        );
        if ($this->_request->isPost()) {
            $permissionId = $this->_request->getPost('permissionId');
            $actions = $this->_request->getParam('actionId', array());
            if (!empty($permissionId) && Service_UserRight::getByField($permissionId) && !empty($actions)) {
                $resultAction = Service_UserRightActionMap::getByCondition(array('ur_id' => $permissionId), '*');
                $delRow = $oldIdArr = $insertRow = array();
                if (!empty($resultAction)) {
                    foreach ($resultAction as $key => $val) {
                        $oldIdArr[] = $val['ura_id'];
                    }
                    $delIdArr = array_diff($oldIdArr, $actions);
                    foreach ($delIdArr as $val) {
                        $delRow[] = Service_UserRightActionMap::deleteByUrIdAnduraId($permissionId, $val);
                    }
                }

                $insertIdArr = array_diff($actions, $oldIdArr);

                if (!empty($insertIdArr)) {
                    foreach ($insertIdArr as $val) {
                        $row = array('ur_id' => $permissionId, 'ura_id' => $val);
                        $insertRow[] = Service_UserRightActionMap::add($row);
                    }
                }
                if (count($delRow) || count($insertRow)) {
                    $result = array('state' => 1, 'message' =>Ec::Lang('operationSuccess'));
                }
            }
        }
        die(Zend_Json::encode($result));
    }
}