<?php

class Default_ErrorController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view = Zend_Registry::get('EcView');
    }

    public function preDispatch()
    {
        $this->tplDirectory = "default/views/error/";
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $errorType = isset($errors->type) ? $errors->type : '';
        switch ($errorType) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'The page you requested was not found..';
                break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'The page you requested was not found.';
                break;
        }
        echo Ec::renderTpl($this->tplDirectory . "error.tpl", "layout");
    }

    public function denyAction()
    {
        echo Ec::renderTpl($this->tplDirectory . "deny.tpl", "layout");
    }

}