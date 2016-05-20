<?php

class API_DEDHL_ForApiService extends Common_APIChannelDataSet
{
    public function __construct()
    {

    }

    public function setParam($serviceCode = '', $orderCode = '', $channelId = '', $serverProductCode = '')
    {
        parent::__construct($serviceCode, $orderCode, $channelId, $serverProductCode);
    }

    public function getData()
    {

        //账号信息
        $this->accountKey = array(
            'token' => '',
            'requesterID' => $this->accountData["as_user"],
            'accountID' => "",
            'passPhrase' => $this->accountData["as_pwd"],
            'partnerCustomerID' => "",
            'partnerTransactionID' => "",
            'url' => $this->accountData["as_address"],
            'endpointurl' => '',
            'cigUser' => $this->accountData["cig_user"],
            'cigPwd' => $this->accountData["cig_pwd"],
            'aenvironment' => $this->accountData["as_environment"],
            'ekp' => $this->accountData["as_ekp"],
            'partner' => $this->accountData["as_partner"],
        );


        $this->orderKey["imageFormat"] = "PNG";
        $this->orderKey["InsuredMail"] = $this->accountData["as_application"];

        return $this->_paramsSet();
    }
}