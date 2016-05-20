<?php
class Auth_MenuController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/user/";
        $this->serviceClass = new Service_UserMenu();
    }

    public function listAction()
    {
        $systemArray=Common_DataCache::getSystem();
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
            $condition['parent_id']='0';
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields = array(
                    'um_title',
                    'um_title_en',
                    'us_id',
                    'um_css',
                    'um_sort',
                    'um_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array('um_sort asc'));

                $language=Ec::getLang(1);
                foreach ($rows as $k => $v) {
                    $rows[$k]['E7'] = isset($systemArray[$v['E7']]['us_title' . $language]) ? $systemArray[$v['E7']]['us_title' . $language] : '';
                    $rows[$k]['submenu']=$this->serviceClass->getByCondition(array('parent_id'=>$v['E0']),'*',0,0,array('um_sort'));
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $this->view->systemArray=$systemArray;
        echo Ec::renderTpl($this->tplDirectory . "menu_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array('操作失败')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $row = array(

                'um_id' => '',
                'um_title' => '',
                'um_title_en' => '',
                'um_url' => '',
                'um_css' => '',
                'um_sort' => '',
                'us_id' => '',
            );
            $row = $this->serviceClass->getMatchEditFields($params, $row);
            $paramId = $row['um_id'];
            if (!empty($row['um_id'])) {
                unset($row['um_id']);
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
                $return['message'] = array('操作成功');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'um_id')) {
            $rows = $this->serviceClass->getVirtualFields($rows);
            $result = array('state' => 1, 'message' => '', 'data' => $rows);
        }
        die(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $result = array(
            "state" => 0,
            "message" => "操作失败"
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            if (!empty($paramId)) {
                if ($this->serviceClass->delete($paramId)) {
                    $this->serviceClass->delete($paramId,'parent_id');
                    $result['state'] = 1;
                    $result['message'] = '操作成功';
                }
            }
            die(Zend_Json::encode($result));
        }
    }

    /**
     * @desc 添加子菜单
     */
    public function editSubmenuAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array('操作失败')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $row = array(
                'um_id' => '',
                'parent_id' => '',
                'um_title' => '',
                'um_title_en' => '',
                'um_url' => '',
                'um_css' => '',
                'um_sort' => '',
                'us_id' => '',
            );
            $row = $this->serviceClass->getMatchEditFields($params, $row);
            $paramId = $row['um_id'];
            if (!empty($row['um_id'])) {
                unset($row['um_id']);
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
            if (!empty($row['parent_id']) && $rows = $this->serviceClass->getByField($row['parent_id'], 'um_id')) {
                $row['us_id'] = $rows['us_id'];
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


}