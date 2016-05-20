<?php
class Ec_Controller_FrontAction extends Zend_Controller_Action
{
	
	public function init()
	{
		parent::init();		
		$this->view = Zend_Registry::get('EcView');
		
		$this->module = strtolower(str_replace('-', '', $this->_request->getModuleName()));
		$this->controller = strtolower(str_replace('-', '', $this->_request->getControllerName()));
		$this->action = strtolower(str_replace('-', '', $this->_request->getActionName()));
        $this->customerAuth = new Zend_Session_Namespace("customerAuth");
        $this->view->quickId = $this->getRequest()->getParam('quick','0');
// 		var_dump(in_array($this->module,array("merchant")) && !in_array($this->controller,array("index")));exit;
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
	
   
    //当不存在的方法时调用
    public function __call($methodName, $args)
    {
        $this->_forward('error','error','default');
    }
}
