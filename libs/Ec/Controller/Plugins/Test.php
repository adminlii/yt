<?php
class Ec_Controller_Plugins_Test extends Zend_Controller_Plugin_Abstract {

//     public function routeStartup (Zend_Controller_Request_Abstract $request) {
//         $this->getResponse()->appendBody("<p>routeStartup() called</p>\n");
//     }

//     public function routeShutdown (Zend_Controller_Request_Abstract $request) {
//         $this->getResponse()->appendBody("<p>routeShutdown() called</p>\n");
//     }

//     public function dispatchLoopStartup (Zend_Controller_Request_Abstract $request) {
//         $this->getResponse()->appendBody("<p>dispatchLoopStartup() called</p>\n");
//     }

//     public function preDispatch (Zend_Controller_Request_Abstract $request) {
//         $this->getResponse()->appendBody("<p>preDispatch() called</p>\n");
//     }

//     public function postDispatch (Zend_Controller_Request_Abstract $request) {
//         $this->getResponse()->appendBody("<p>postDispatch() called</p>\n");
//     }

    public function dispatchLoopShutdown () {
        global $start_t;
        
        if (Zend_Registry::get('debug')) {
            $msg = "";
            $selectCount = Zend_Registry::get('selectCount');
            $sql_select = Zend_Registry::get('sql_select');
            $end_t = microtime_float();
            if (! empty($sql_select)) {
                $msg .= "<div style='display:none;' id='sql_select' title='[Esc] &nbsp;进行了" . $selectCount . "个数据库查询，耗时：" . ($end_t - $start_t) . " s &nbsp;".sprintf('内存占用: %01.2f MB', memory_get_usage()/1024/1024)."'>" . preg_replace('/\\n/', '<br/>', $sql_select) .
                         "</div>";
                // $this->getResponse()->appendBody('<script type="text/javascript">$(function(){$("body").append("'.$msg.'")})</script>');
                $msg .= '<script type="text/javascript">$(function(){$("#sql_select").dialog({autoOpen:true,width:800,height:300,modal:true,show:"slide",buttons:{"Close":function(){$(this).dialog("close");}}});})</script>';
            }
            $this->getResponse()->appendBody($msg);
        }
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
