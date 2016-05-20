<?php

/**
 * @desc WMS对外API
 * @Tips 仅对API系统开放,获取待预报物流系统订单
 */
class Common_ApiSvc
{
    protected $_active = 1; // 是否开启与API对接
    protected $_token = '';
    protected $_systemCode = '';
    protected $_requestLog = 1; // 是否开启记录请求信息
    protected $_responseLog = 1; // 是否开启记录响应信息
    protected $_language = 'zh_CN';

    /**
     *
     * @param $req
     * @throws Exception
     */
    private function authentication($req)
    {
        $paramKey = array(
            'token' => '',
            'service' => '',
            'systemCode' => '',
            'params' => ''
        );
        foreach ($paramKey as $key => $val) {
            $paramKey [$key] = isset ($req [$key]) ? $req [$key] : '';
        }
        unset ($req);

        if (empty ($paramKey ['token'])) {
            throw new Exception ('token 不能为空');
        }

        if (empty ($paramKey ['systemCode'])) {
            throw new Exception ('systemCode 不能为空');
        }

        if (isset ($req ['language']) && !empty ($req ['language']) && in_array(strtoupper($req ['language']), array(
                'ZH_CN',
                'EN_US'
            ))
        ) {
            $this->_language = strtoupper($req ['language']) == 'EN_US' ? 'en_US' : 'zh_CN';
        }
        if (empty ($paramKey ['service'])) {
            throw new Exception ('service 不能为空');
        }
        /**
         * 判断系统是否支持方法
         */
        $service = $paramKey ['service'];
        if (!method_exists($this, $paramKey ['service'])) {
            throw new Exception ('The system does not support method ' . $service);
        }

        // 验证token
        $apiArray = array();
        $api = new Zend_Config_Ini(APPLICATION_PATH . '/configs/api.ini');
        $api = $api->toArray();
        $oapi = $api['production']['api']['oapi'];
        if ($oapi) {
             $apiArray['oapi'] = $oapi;
        }
        
        $this->_token = isset($apiArray['oapi']['toKen']) ? $apiArray['oapi']['toKen'] : '';
        $this->_active = isset($apiArray['oapi']['active']) ? $apiArray['oapi']['active'] : '0';
        $this->_systemCode = isset($apiArray['oapi']['systemCode']) ? $apiArray['oapi']['systemCode'] : '';

        if (strtolower($paramKey ['token']) != strtolower($this->_token) || empty($this->_token) || empty($this->_systemCode) || strtolower($paramKey ['systemCode']) != strtolower($this->_systemCode)) {
            throw new Exception("authentication failed");
        }

        if (!$this->_active) {
            throw new Exception ("connection closed by server,API closed");
        }
    }

    /**
     * 接口入口
     *
     * @param $req
     * @return array
     */
    public function callService($req)
    {
        $service = '';
        try {
            // 对象转数组
            $req = Common_Common::objectToArray($req);
            // 记录请求数据
            $this->_requestLog($req);
            // 数据验证
            $this->authentication($req);
            $service = $req ['service'];
            $params = $req ['params'];
            $return = $this->$service ($params);
        } catch (Exception $e) {
            $return = array(
                'ask' => 'Failure',
                'message' => $e->getMessage()
            );
        }
        // 记录响应数据
        $this->_responseLog($service, $return);
        return $return;
    }

    /**
     * @desc 获取需要预报物流系统的订单
     * @param array $params
     * @return array
     */
    private function loadOrder($params = array())
    {
        $return = array(
            'ask' => 'Failure',
            'message' => '',
            'data' => array(),
            'count' => 0,
        );
        return Common_ApiSvcService::loadOrder($params);
    }

    /**
     * @desc 回写跟踪号&标签
     * @param array $params
     * @return array
     */
    private function backTrackingNo($params = array())
    {
        $return = array(
            'ask' => 'Failure',
            'message' => '',
        );
        return Common_ApiSvcService::backTrackingNo($params);
    }


    /**
     * 测试
     * @param $params
     * @return array
     */
    private function demo($params)
    {
        $return = array(
            'ask' => 'Failure',
            'message' => '',
            'data' => ''
        );
        try {
            $return ['ask'] = 'Success';
            $return ['data'] = $params;
        } catch (Exception $e) {
            $return ['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 记录请求信息
     * @param $req
     */
    private function _requestLog($req)
    {
        if (!$this->_requestLog) {
            return;
        }
        try {
            $service = isset ($req ['service']) ? $req ['service'] : 'null';
            $logger = new Zend_Log ();
            $uploadDir = APPLICATION_PATH . "/../data/log/";
            $writer = new Zend_Log_Writer_Stream ($uploadDir . 'apiSvc_request_' . $service . '_data.log');
            $logger->addWriter($writer);
            $logger->info("\n" . date('Y-m-d H:i:s') . ":\n" . (print_r($req, true)));
        } catch (Exception $e) {
        }
    }

    /**
     * 记录请求信息
     *
     * @param $service
     * @param $req
     */
    private function _responseLog($service, $req)
    {
        if (!$this->_responseLog) {
            return;
        }
        try {
            $logger = new Zend_Log ();
            $uploadDir = APPLICATION_PATH . "/../data/log/";
            $writer = new Zend_Log_Writer_Stream ($uploadDir . 'apiSvc_response_' . $service . '_data.log');
            $logger->addWriter($writer);
            $logger->info("\n" . date('Y-m-d H:i:s') . ":\n" . (print_r($req, true)));
        } catch (Exception $e) {
            //
        }
    }

    /**
     * 错误日志
     *
     * @param $error
     */
    private function _log($error)
    {
        try {
            $logger = new Zend_Log ();
            $uploadDir = APPLICATION_PATH . "/../data/log/";
            $writer = new Zend_Log_Writer_Stream ($uploadDir . 'apiSvc.log');
            $logger->addWriter($writer);
            $logger->info(date('Y-m-d H:i:s') . ': ' . $error . " \n");
        } catch (Exception $e) {
            //
        }
    }
}