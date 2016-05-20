<?php
class Default_SvcForWmsController extends Zend_Controller_Action {

    public function indexAction () {
        $this->_forward('wsdl');
    }

    public function webServiceAction () {
        $input = file_get_contents('php://input');              
        if(!empty($input)){
            $server = new SoapServer(APPLICATION_PATH . "/../data/wsdl/EcForWms.wsdl");
            $server->setClass('Common_ServiceForWms3');
            $server->handle();          
        }else{
            echo 'Invalid SOAP request';
        }
        exit;
    }
    public function wsdlAction () {
        $host = $this->_request->getHttpHost();
        header("Content-type: text/xml; Charset=utf-8");
        $content = file_get_contents(APPLICATION_PATH . "/../data/wsdl/EcForWms.wsdl");
        $content = preg_replace('/www\.oms\.com/', $host, $content);
        echo $content;
        exit();
    }

    public function wsdlFileAction () {
        $host = $this->_request->getHttpHost();
        $content =  file_get_contents(APPLICATION_PATH . "/../data/wsdl/EcForWms.wsdl");
        $content = preg_replace('/www\.oms\.com/',$host,$content);
        $fileName = preg_replace('/([a-zA-Z_0-9]+)\.([a-zA-Z_0-9]+)\.([a-zA-Z_0-9]+)/e', 'strtolower(\\1)', $host);
        $fileName = APPLICATION_PATH.'/../data/cache/'.$fileName.'-EcForWms.wsdl';
        if(!file_exists($fileName)){
            file_put_contents($fileName, $content);
        }
        Common_Common::downloadFile($fileName);
        exit;
    }  

    public function oAction () {
        $wmsService = new Common_ThirdPartWmsAPI();
        $wmsProcess = new Common_ThirdPartWmsAPIProcess();
        $rs = $wmsProcess->syncCountry();
        
//         $param = array('customer_code'=>'E004');
//         $process = new Common_ThirdPartWmsAPI();
//         $result = $process->getInventory($param);
//         print_r($result);
        exit();
    }  
}