<?php

class Customer_BalanceLogController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "customer/views/";
        $this->serviceClass = new Service_CustomerBalanceLog();
    }

    public function listAction()
    {
        $companyCode=Common_Company::getCompanyCode();
        if ($this->_request->isPost()) {
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);

            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;

            $return = array(
                "state" => 0,
                "message" => "No Data"
            );

            $params = $this->_request->getParams();
            $condition = $this->serviceClass->getMatchFields($params);

            $condition['addDateFrom'] = $this->_request->getParam('addDateFrom', '');
            $condition['addDateEnd'] = $this->_request->getParam('addDateEnd', '');
//             print_r($condition);
//             exit;
            $condition['customer_code']=$companyCode;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields = array(

                    'customer_code',
                    'cbl_type',
                    'cbl_transaction_value',
                    'cbl_value',
                    'currency_rate',
                    'currency_code',
                    'cbl_note',
                    'user_id',
                    'fee_id',
                    'cbl_current_value',
                    'cbl_current_hold_value',
                    'cbl_refer_code',
                    'cbl_add_time',
                    'arrive_time',
                    'cbl_id',
                    'ft_code',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array('cbl_id desc'));

                //操作类型
                $customerBlTypeArr = Common_Type::customerBalanceLogType();
                //操作人
                $resultUser = Service_User::getAll();
                $userArr = array();
                foreach ($resultUser as $keyUser => $valueUser) {
                    $userArr[$valueUser['user_id']] = $valueUser;
                }
                $userArr['-1'] = array('user_name' => "客户");
                $userArr['0'] = array('user_name' => "系统");
                //费用类型
                $resultFeeType = Service_FeeType::getAll();
                $FeeTypeArr = array();
                foreach ($resultFeeType as $keyFt => $valueFt) {
                    $FeeTypeArr[$valueFt['ft_code']] = $valueFt;
                }
                foreach ($rows as $key => $value) {
                    //操作类型
                    if (isset($customerBlTypeArr[$value['E3']])) {
                        $rows[$key]['E3_title'] = $customerBlTypeArr[$value['E3']];
                    }
                    //操作人
                    if (isset($userArr[$value['E9']])) {
                        $rows[$key]['E9_title'] = $userArr[$value['E9']]['user_name'];
                    }
                    //费用类型
                    $rows[$key]['ft_name_cn'] = isset($FeeTypeArr[$value['E18']]) ? $FeeTypeArr[$value['E18']]['ft_name_cn'] : '';
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $this->view->companyCode = $companyCode;
        echo Ec::renderTpl($this->tplDirectory . "customer_balance_log_index.tpl", 'layout');
    }
    /**
     * 入款
     */
    public function inAction(){
        $this->view->companyCode = Common_Company::getCompanyCode();
        echo Ec::renderTpl($this->tplDirectory . "customer_balance_log_index_in.tpl", 'layout');
    }
    /**
     * 扣款
     */
    public function outAction(){
        $this->view->companyCode = Common_Company::getCompanyCode();
        echo Ec::renderTpl($this->tplDirectory . "customer_balance_log_index_out.tpl", 'layout');
    }
    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array('Fail.')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $row = array(

                'cbl_id' => '',
                'customer_code' => '',
                'cbl_type' => '',
                'cbl_transaction_value' => '',
                'cbl_value' => '',
                'currency_rate' => '',
                'currency_code' => '',
                'cbl_note' => '',
                'user_id' => '',
                'fee_id' => '',
                'cbl_current_value' => '',
                'cbl_refer_code' => '',
                'cbl_add_time' => '',
                'arrive_time' => '',
            );
            $row = $this->serviceClass->getMatchEditFields($params, $row);
            $paramId = $row['cbl_id'];
            if (!empty($row['cbl_id'])) {
                unset($row['cbl_id']);
            }
            $errorArr = $this->serviceClass->validator($row);

            if (!empty($errorArr)) {
                $return = array(
                    'state' => 0,
                    'message' => '',
                    'errorMessage' => $errorArr
                );
                die(Zend_Json::encode($return));
            }

            if (!empty($paramId)) {
                $result = $this->serviceClass->update($row, $paramId);
            } else {
                $result = $this->serviceClass->add($row);
            }
            if ($result) {
                $return['state'] = 1;
                $return['message'] = array('操作成功');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'cbl_id')) {
            $rows = $this->serviceClass->getVirtualFields($rows);
            //操作类型
            $customerBlTypeArr = Common_Type::customerBalanceLogType();
            if (isset($customerBlTypeArr[$rows['E3']])) {
                $rows['E3'] = $customerBlTypeArr[$rows['E3']];
            }

            //操作人
            $resultUser = Service_User::getAll();
            $userArr = array();
            foreach ($resultUser as $keyUser => $valueUser) {
                $userArr[$valueUser['user_id']] = $valueUser;
            }
            if (isset($userArr[$rows['E9']])) {
                $rows['E9'] = $userArr[$rows['E9']]['user_name'];
            }

            //费用类型
            $resultFeeType = Service_FeeType::getAll();
            $FeeTypeArr = array();
            foreach ($resultFeeType as $keyFt => $valueFt) {
                $FeeTypeArr[$valueFt['ft_id']] = $valueFt;
            }
            if (isset($userArr[$rows['E10']])) {
                $rows['E10'] = $FeeTypeArr[$rows['E10']]['ft_name_cn'];
            } else {
                $rows['E10'] = "";
            }
            $result = array('state' => 1, 'message' => '', 'data' => $rows);
        }
        die(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $result = array(
            "state" => 0,
            "message" => "Fail."
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            if (!empty($paramId)) {
                if ($this->serviceClass->delete($paramId)) {
                    $result['state'] = 1;
                    $result['message'] = '操作成功';
                }
            }
        }
        die(Zend_Json::encode($result));
    }
    
    /**
     * 同步流水
     */
    public function syncLogAction(){
        $return = array(
            'ask' => 1,
            'message' => 'Success'
        );
        set_time_limit(0);
        $timestamp = APPLICATION_PATH . '/../data/log/balance_log_init_' . Common_Company::getCompanyCode();
//         if(! file_exists($timestamp) || filemtime($timestamp) + 300 < time()){ // 文件不存在或者创建时间大于24小时
            @unlink($timestamp); // 删除时间戳文件
            file_put_contents($timestamp, now());
            $process = new Common_ThirdPartWmsAPIProcess();
            $companyCode = Common_Company::getCompanyCode();
            $return = $process->syncCustomerBalanceLog($companyCode);
//         }
        echo Zend_Json::encode($return);
        exit();
    }
}