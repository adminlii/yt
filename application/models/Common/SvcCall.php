<?php
class Common_SvcCall
{

    protected $_appToken = ''; // token
    protected $_appKey = ''; // key
    public $_active = true; // 是否启用发送到oms
    private $_client = null; // SoapClient
    public $_error = '';

    private function getClient()
    {
        if(empty($this->_client)||!$this->_client instanceof SoapClient){
            $this->setClient();
        }
        
        return $this->_client;
    }

    private function setClient()
    {
        $omsConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oms.ini', APPLICATION_ENV);
        
        $omsConfig = $omsConfig->toArray();
        $omsConfig = $omsConfig['oms'];
        // print_r($omsConfig);exit;
        $wsdl = $omsConfig['wsdl'];
        $this->_appToken = $omsConfig['appToken'];
        $this->_appKey = $omsConfig['appKey'];
        // 超时
        $timeout = isset($omsConfig['timeout']) && is_numeric($omsConfig['timeout']) ? $omsConfig['timeout'] : 1000;
        
        $streamContext = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true
            ),
            // 'bindto' => $omsConfig['BindTo'],
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
        
        $client = new SoapClient($wsdl, $options);
        
        $this->_client = $client;
    }

    /**
     * 调用webservice
     * ====================================================================================
     *
     * @param unknown_type $req            
     * @return Ambigous <mixed, NULL, multitype:, multitype:Ambigous <mixed,
     *         NULL> , StdClass, multitype:Ambigous <mixed, multitype:,
     *         multitype:Ambigous <mixed, NULL> , NULL> , boolean, number,
     *         string, unknown>
     */
    private function callService($req)
    {
        $client = $this->getClient();
        
        $req['appToken'] = $this->_appToken;
        $req['appKey'] = $this->_appKey;
        $result = $client->callService($req);
        $result = Common_Common::objectToArray($result);
        // 日志
        $return = Zend_Json::decode($result['response']);
        $return['req'] = $req;
//         Ec::showError(print_r($return, true), '_oms_return');
        $request = $client->__getLastRequest();
        $response = $client->__getLastResponse();
//         file_put_contents(APPLICATION_PATH.'/../data/log/request_xml_'.$req['service'].'_all.txt', $request."\n",FILE_APPEND);
//         file_put_contents(APPLICATION_PATH.'/../data/log/response_xml'.$req['service'].'_all.txt', $response."\n",FILE_APPEND);

        file_put_contents(APPLICATION_PATH.'/../data/log/'.$req['service'].'_request_xml.xml', $request);
        file_put_contents(APPLICATION_PATH.'/../data/log/'.$req['service'].'_response_xml.xml', $response);
        
        if(!empty($req['paramsJson'])){
//             file_put_contents(APPLICATION_PATH.'/../data/log/__request_'.$req['service'].'_json.js', "var req={$req['paramsJson']};"."\n",FILE_APPEND);
        }
        file_put_contents(APPLICATION_PATH.'/../data/log/'.$req['service'].'_response__json.js', "var response={$result['response']};");
        
        return $return;
    }

    /**
     * 禁止数组中有null
     *
     * @param unknown_type $arr            
     * @return unknown string
     */
    private function arrFormat($arr)
    {
        if(! is_array($arr)){
            return $arr;
        }
        foreach($arr as $k => $v){
            if(! isset($v)){
                $arr[$k] = '';
            }
        }
        return $arr;
    }

    public function getCountry($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getCountry',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function getCountryPagination($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getCountryPagination',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    public function getRegion($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getRegion',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function getRegionForReceiving()
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                    'service' => 'getRegionForReceiving'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    public function getWarehouse($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getWarehouse',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function getShippingMethod($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getShippingMethod',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function getCategory($params)
    {
        $return = array(
                'ask' => 'Failure',
                'message' => ''
        );
        try{
            $req = array(
                    'service' => 'getCategory',
                    'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    
    public function getAccount()
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getAccount'
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function createProduct($productInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'createProduct',
                'paramsJson' => Zend_Json::encode($productInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function modifyProduct($productInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'modifyProduct',
                'paramsJson' => Zend_Json::encode($productInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function createAsn($receivingInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'createAsn',
                'paramsJson' => Zend_Json::encode($receivingInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function modifyAsn($receivingInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'modifyAsn',
                'paramsJson' => Zend_Json::encode($receivingInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function getAsnList($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getAsnList',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    private function getProduct($productInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getProduct',
                'paramsJson' => Zend_Json::encode($productInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
    public function getProductList($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getProductList',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function getProductInventory($productInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getProductInventory',
                'paramsJson' => Zend_Json::encode($productInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function createOrder($orderInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'createOrder',
                'paramsJson' => Zend_Json::encode($orderInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function modifyOrder($orderInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'modifyOrder',
                'paramsJson' => Zend_Json::encode($orderInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function cancelOrder($orderInfo)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'cancelOrder',
                'paramsJson' => Zend_Json::encode($orderInfo)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function getOrderList($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'getOrderList',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    public function orderTrail($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => ''
        );
        try{
            $req = array(
                'service' => 'orderTrail',
                'paramsJson' => Zend_Json::encode($params)
            );
            $return = $this->callService($req);
        }catch(Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;
    }
}