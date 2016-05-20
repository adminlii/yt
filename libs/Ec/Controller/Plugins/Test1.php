<?php
class Ec_Controller_Plugins_Test1 extends Zend_Controller_Plugin_Abstract {

//     
public function routeStartup (Zend_Controller_Request_Abstract $request) {
        echo ("<p>routeStartup() called</p>");
    }

    public function routeShutdown (Zend_Controller_Request_Abstract $request) {
        echo ("<p>routeShutdown() called</p>");
    }

    public function dispatchLoopStartup (Zend_Controller_Request_Abstract $request) {
        echo ("<p>dispatchLoopStartup() called</p>");
    }

    public function preDispatch (Zend_Controller_Request_Abstract $request) {
        echo ("<p>preDispatch() called</p>");
    }

    public function postDispatch (Zend_Controller_Request_Abstract $request) {
        echo ("<p>postDispatch() called</p>");
    }

    public function dispatchLoopShutdown () {
        echo ('<p>dispatchLoopShutdown() called---</p>');
    }
}

// routeStartup() called

// routeShutdown() called

// dispatchLoopStartup() called

// preDispatch() called

// init() called

// postDispatch() called

// dispatchLoopShutdown

// preDispatch()---Controller called

// indexAction() called



?>
