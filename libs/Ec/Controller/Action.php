<?php

class Ec_Controller_Action extends Zend_Controller_Action
{
    public $_userAuth = '';

    public function init()
    {
        $this->view = Zend_Registry::get('EcView');
        $this->_userAuth = new Zend_Session_Namespace('userAuthorization');
       // $this->_userAuth->warehouseId;
        if (!$this->_userAuth->userId || !$this->_userAuth->isLogin) {
            $this->_redirect('/default/index/login');
            exit();
        }
        $this->view->quickId = $this->getRequest()->getParam('quick','0');

        $this->view->session_id = Zend_Session::getId();
        $this->view->system_title = Zend_Registry::get('system_title') ;

        $tms_id = Service_User::getTmsId();
        $sql = "select * from web_newsconfig where   news_type='LOGO';";
        $row = Common_Common::fetchRow($sql);
        //                 print_r($row);exit;
        $this->view->logo = '/images/head/zjs_logo.png';
        if($row){
        	$this->view->logo = $row['news_note'];
        }
        
 
    }

    public function indexAction()
    {
        $this->listAction();
    }

    public function listAction()
    {
    }

    //当不存在的方法时调用
    public function __call($methodName, $args)
    {
        $this->_forward('error', 'error', 'default');
    }

}