<?php
class Ec_Controller_DefaultAction extends Zend_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view = Zend_Registry::get('EcView');
        $this->view->quickId = $this->getRequest()->getParam('quick','0');
        $this->view->system_title = Zend_Registry::get('system_title') ;
        
        $tms_id = Service_User::getTmsId();
        $sql = "select * from web_newsconfig where   news_type='LOGO';";
        $row = Common_Common::fetchRow($sql);
        //                 print_r($row);exit;
        $this->view->logo = '/images/head/zjs_logo.png';
        if($row){
        	//$this->view->logo = $row['news_note'];
            $this->view->logo = '/images/head/yuntuexpress_logo.png';
        }
    }

    //当不存在的方法时调用
    public function __call($methodName, $args)
    {
        $this->_forward('error', 'error', 'default');
    }
}
