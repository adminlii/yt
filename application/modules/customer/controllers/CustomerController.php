<?php
class Customer_CustomerController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "customer/views/default/";
    }

    public function listAction()
    {}

    public function editAction()
    {}

    public function getByJsonAction()
    {}

    public function getCustomerDataAction()
    {
        $result = array(
            'state' => '0',
            'message' => '数据异常',
            'data' => array()
        );
        if($this->_request->isPost()){
            $customerCode = Common_Company::getCompanyCode();
            $customerRow = Service_Company::getByField($customerCode,'company_code');
            $customerRow['trade_name'] = $customerRow['company_name'];
            $userId = Service_User::getUserId();
            $user = Service_User::getByField($userId,'user_id');
            $customerRow['customer_firstname'] = $user['user_name'];
            $customerRow['customer_lastname'] = '';
            $customerRow['customer_currency'] = 'RMB';
            
            
            $balance = Service_CustomerBalance::getByField($customerCode, 'customer_code');
            if(! empty($balance)){
                $customerRow['cb_value'] = $balance['cb_value'];
                $customerRow['cb_hold_value'] = $balance['cb_hold_value'];
            }else{
                $customerRow['cb_value'] = 0;
            }
            $result['state'] = 1;
            $result['message'] = '';
            $result['data'] = $customerRow;
            die(Zend_Json::encode($result));
        }
        die(Zend_Json::encode($result));
    }
}