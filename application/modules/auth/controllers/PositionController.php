<?php
class Auth_PositionController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/user/";
        $this->serviceClass = new Service_UserPosition();
    }

    public function listAction()
    {
        $departmentArray = Common_DataCache::getDepartment();
        $positionLevelArray = Common_DataCache::getUserPositionLevel();
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
            //公司代码
            $condition['company_code'] = Common_Company::getCompanyCode(); 

            $condition['company_code'] = Common_Company::getCompanyCode();
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields=array(
                'up_name',
                'up_name_en',
                'ud_id',
                'upl_id',
                'up_id',
                'company_code'
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition,$showFields, $pageSize, $page, array('ud_id asc','upl_id asc'));
                $language = Ec::getLang(1);
                foreach ($rows as $key => $val) {
                    $rows[$key]['E3'] = isset($departmentArray[$val['E3']]['ud_name' . $language]) ? $departmentArray[$val['E3']]['ud_name' . $language] : '';
                    $rows[$key]['E4'] = isset($positionLevelArray[$val['E4']]['upl_name' . $language]) ? $positionLevelArray[$val['E4']]['upl_name' . $language] : '';
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $this->view->department = $departmentArray;
        $this->view->positionLevel = $positionLevelArray;
        echo Ec::renderTpl($this->tplDirectory . "position_index.tpl", 'layout');
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
                
              'up_id'=>'',
              'up_name'=>'',
              'up_name_en'=>'',
              'ud_id'=>'',
              'upl_id'=>'',
            );
            $row=$this->serviceClass->getMatchEditFields($params,$row);
            $paramId = $row['up_id'];
            if (!empty($row['up_id'])) {
                unset($row['up_id']);
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
            $row['company_code'] = Common_Company::getCompanyCode();
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
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'up_id')) {
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

    public function getRightAction()
    {
        $result = array(
            "state" => 0,
            "data" => array(),
            "ids" => array(),
            "message" => Ec::Lang('operationFail')
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            $language = Ec::getLang(1);
            $showFields = array(
                'ur_name' . $language . ' as title',
                'ur_module as module',
                'um_id as menu_id',
                'ur_id as id',
            );
            $menuArr=Common_DataCache::getUserMenu();
            $result['data'] = Service_UserRight::getByCondition(array('ur_type'=>1), $showFields, 0, 0, array('um_id','ur_module','ur_sort'));
            foreach($result['data'] as $key =>$val){
                $result['data'][$key]['menu']=isset($menuArr[$val['menu_id']]['um_title'.$language])?$menuArr[$val['menu_id']]['um_title'.$language]:'';
            }
            $rightUraIdArr = array();
            if (!empty($paramId)) {
                $rightAction = Service_UserPositionRightMap::getByCondition(array('up_id' => $paramId), 'ur_id');
                if (!empty($rightAction)) {
                    foreach ($rightAction as $v) {
                        $rightUraIdArr[$v['ur_id']] = $v['ur_id'];
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
            if (!empty($permissionId) && Service_UserPosition::getByField($permissionId)) {
                $resultAction = Service_UserPositionRightMap::getByCondition(array('up_id' => $permissionId), '*');
                $delRow = $oldIdArr = $insertRow = array();
                if (!empty($resultAction)) {
                    foreach ($resultAction as $key => $val) {
                        $oldIdArr[] = $val['ur_id'];
                    }
                    $delIdArr = array_diff($oldIdArr, $actions);
                    foreach ($delIdArr as $val) {
                        $delRow[] =Table_UserPositionRightMap::getInstance()->deleteByUpIdAndUrId($permissionId, $val);
                    }
                }

                $insertIdArr = array_diff($actions, $oldIdArr);

                if (!empty($insertIdArr)) {
                    foreach ($insertIdArr as $val) {
                        $row = array('up_id' => $permissionId, 'ur_id' => $val);
                        $insertRow[] = Service_UserPositionRightMap::add($row);
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