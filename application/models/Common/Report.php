<?php
/**
 * 标签
 * @author Administrator
 */
class Common_Report
{

    protected $_authToken = ''; // key
    private $_client = null; // Common_ReportSoap
    public $_error = '';

    private function getClient()
    {
        if(empty($this->_client)){
            $this->setClient();
        }
        
        return $this->_client;
    }

    public function __construct($authToken)
    {
        $this->_authToken = $authToken;
    }

    public function setAuthToken($authToken)
    {
        $this->_authToken = $authToken;
    }

    private function setClient()
    {
        $wsdl = APPLICATION_PATH . '/../data/wsdl/ReportService.wsdl';
        
        // $this->_authToken = 'ukwh6lNEFNfBDGt6xue0uG7CkgNHIyyL';
        
        // 超时
        $timeout = 1000;
        
        $streamContext = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true
            ),
            'socket' => array()
        ));
        
        $options = array(
            "trace" => true,
            "connection_timeout" => $timeout,
            // "exceptions" => true,
            // "soap_version" => SOAP_1_1,
            // "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
            // "stream_context" => $streamContext,
            "encoding" => "utf-8"
        );
        $client = new Common_ReportSoap($wsdl, $options);
        $this->_client = $client;
    }

    public function GetVersion()
    {
        $req = array(
            'authToken' => $this->_authToken
        );
        $client = $this->getClient();
        $rs = $client->GetVersion($req);
        
        // echo $client->__getLastRequest();exit;
        
        $rs = Common_Common::objectToArray($rs);
        $rs = array_pop($rs);
        $rs = Zend_Json::decode($rs);       
        return $rs;
    }

    public function MakeLableFileToBase64($configInfoJson, $orderInfoJson)
    {
        $return = array('ask'=>0,"message"=>Ec::Lang('失败'));
        try{

            $req = array();
            $client = $this->getClient();
            $authToken = array(
                    'authToken' => $this->_authToken
            );
            $req['authToken'] = $this->_authToken;
            $req['configInfoJson'] = $configInfoJson;
            $req['orderInfoJson'] = $orderInfoJson;
            
            $rs = $client->MakeLableFileToBase64($req);
//             header("Content-type: text/xml; charset=utf-8");
//             echo $client->__getLastRequest();exit;
//             header("Content-type: text/html; charset=utf-8");
//             print_r($rs);exit;
            
            $rs = Common_Common::objectToArray($rs);
            $rs = array_pop($rs);
            $rs = Zend_Json::decode($rs);
            $return['rs'] = $rs;
            $return['ask'] = 1;
            $return['message'] = Ec::Lang('Success');
        }catch (Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
}