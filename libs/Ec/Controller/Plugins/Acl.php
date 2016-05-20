<?php

class Ec_Controller_Plugins_Acl extends Zend_Controller_Plugin_Abstract
{

    private $_roleName = 'guest';

    private $_menuArr = array();

    private $_resources = array();

    private $_actions = array();

    private $_authActions = array();

    private $_rights = array();

    private $_allRights = array();

    private $_userAuth = null;

    private $_adminArr = array(1); //拥有最高权限UserId

    public function getUserAuthInfo()
    {
        $user = Service_User::getLoginUser();
        $position = Service_UserPosition::getByField($user['up_id'], 'up_id');

        $this->_roleName = $position['up_name'];

        $positionMap = Service_UserPositionRightMap::getByCondition(array(
            'up_id' => $user['up_id'] . ''
        ));
        $userRightIds = array();
        foreach ($positionMap as $p) {
            $userRightIds[$p['ur_id']] = $p['ur_id'];
        }
//         print_r($userRightIds);exit;

        $rightMap = Service_UserRightMap::getByCondition(array(
            'user_id' => $user['user_id'],
        	'visiable'=>'1'
        ));
        foreach ($rightMap as $p) {
            $userRightIds[$p['ur_id']] = $p['ur_id'];
        }

        $allRights = Common_DataCache::getUserRight();
        if (!in_array($user['user_id'], $this->_adminArr)) {
            foreach ($allRights as $k => $v) {
                if (isset($userRightIds[$v['ur_id']])) {
                    $rights[] = $v;
                }
            }
        } else {
            $rights = $allRights;
        }

        $this->_rights = $rights;
        $this->_allRights = $allRights;

        //set menu
        $this->getLeftMenu();

        $rightActionMap = Service_UserRightActionMap::getByCondition(array(
            'ur_id_arr' => $userRightIds
        ));
        //  print_r($rightActionMap);exit;
        $actionIds = array();
        foreach ($rightActionMap as $act) {
            $actionIds[$act['ura_id']] = $act['ura_id'];
        }

        $this->_actions = Common_DataCache::getUserRightAction();

        $this->_authActions = array();

        foreach ($this->_actions as $k => $v) {
            if (isset($actionIds[$v['ura_id']])) {
                $this->_authActions[$v['ura_id']] = $v;
            }
        }
        
    }
    
    public function getMenuArr(){
        return $this->_menuArr;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        $isAcl = true;
        $returnModuleName = array(
        		'default',
//         		'fee'
        		);
        if(in_array($moduleName, $returnModuleName)){
//         if ($moduleName == 'default') {
            $isAcl = false;
        } 

        if($request->getParam('session_id')){//for swfupload
            Zend_Session::setId($request->getParam('session_id'));
        }
        
        $this->_userAuth = new Zend_Session_Namespace('userAuthorization');
        if ($isAcl) {
            if(!$this->_userAuth->user){
                $this->login();
            } else {
                $this->_resources = Common_DataCache::getUserResource();
                if (isset($this->_userAuth->Acl['authActions']) && isset($this->_userAuth->Acl['menuArr']) && isset($this->_userAuth->Acl['rights'])) {
                    $this->_authActions = $this->_userAuth->Acl['authActions'];
                    $this->_menuArr = $this->_userAuth->Acl['menuArr'];
                    $this->_rights = $this->_userAuth->Acl['rights'];
                } else {
                    $this->getUserAuthInfo();
                    $this->_userAuth->Acl['menuArr'] = $this->_menuArr;
                    $this->_userAuth->Acl['rights'] = $this->_rights;
                    $this->_userAuth->Acl['authActions'] = $this->_authActions;
                }
                $openAcl = Zend_Registry::get('config')->openAcl;
                if (!in_array($this->_userAuth->userId, $this->_adminArr) && $openAcl && $moduleName != 'default') {
                    $this->_acl = new Zend_Acl();
                    $this->_acl->addRole(new Zend_Acl_Role($this->_roleName));
                    foreach ($this->_resources as $val) {
                        $this->_acl->add(new Zend_Acl_Resource($val));
                    }
                
                    foreach ($this->_authActions as $v) {
                        $this->_acl->allow($this->_roleName, $v['ura_module'] . ':' . $v['ura_controller'], $v['ura_action']);
                    }
                
                    $currentResourceName = $request->getModuleName() . ':' . $request->getControllerName();
                
                    if (!$this->_acl->has($currentResourceName) || !$this->_acl->isAllowed($this->_roleName, $currentResourceName, $actionName)) {
                        if ($request->isXmlHttpRequest()) {
                            $result = array('state' => 0, 'message' => 'You Have No Authority To Access');
                            die(Zend_Json::encode($result));
                        } else {
                            $this->denyAccess();
                        }
                    }
                }
            }
        }
    }

    /*
     * @获取用户菜单
    */
    public function getLeftMenu()
    {
        $userRightArray = $this->_rights;
        $userMenuArray = Common_DataCache::getUserMenu();
        $lang = Ec::getLang(1);
        $userMenu = array();
        $uk = '';
        if (!empty($userRightArray)) {
            foreach ($userRightArray as $key => $val) {
                if ($val['ur_type'] == '0') {
                    continue;
                }
                $uk = $userMenuArray[$val['um_id']]['um_sort'] . '-' . $val['um_id'];
                if (!isset($userMenu[$uk]['menu'])) {
                    $userMenu[$uk]['menu'] = $userMenuArray[$val['um_id']];
                    $userMenu[$uk]['menu']['value'] = $userMenuArray[$val['um_id']]['um_title' . $lang];
                }
                $userMenu[$uk]['item'][$key] = $val;
                $userMenu[$uk]['item'][$key]['value'] = $val['ur_name' . $lang];
            }
            ksort($userMenu, true);
        }
//         exit;
        $head = array();
        if (!empty($userMenu)) {
            foreach ($userMenu as $val) {
                if ($val['menu']['parent_id'] == '0') {
                    continue;
                }
                $uk = $userMenuArray[$val['menu']['parent_id']]['um_sort'] . '-' . $val['menu']['parent_id'];
                if (!isset($head[$uk])) {
                    $head[$uk]['parent'] = $userMenuArray[$val['menu']['parent_id']];
                    $head[$uk]['parent']['value'] =$userMenuArray[$val['menu']['parent_id']]['um_title'.$lang];
                }
                $head[$uk]['item'][] = $val;
            }
            ksort($head, true);
        }
//         print_r($head);exit;
        $this->_menuArr = $head;
    }

    /**
     * Deny Access Function
     * Redirects to errorPage, this can be called from an action using the action helper
     * @return void
     *
     */
    public function denyAccess()
    {
        $this->_request->setModuleName('default');
        $this->_request->setControllerName('error');
        $this->_request->setActionName('deny');
    }

    public function login()
    {
        $this->_request->setModuleName('default');
        $this->_request->setControllerName('index');
        $this->_request->setActionName('login');
    }

}