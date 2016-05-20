<?php
class Auth_RunControlController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/run_control/";
        $this->serviceClass = new Service_RunControl();
    }

    public function listAction()
    {
        if ($this->_request->isPost()) {
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);

            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;

            $return = array(
                "state" => 0,
                "message" => "No Data"
            );

            $condition = $this->_request->getParams();
            foreach($condition as $k=>$v){
                if(!is_array($v)){
                    $v = trim($v);
                }
                $condition[$k] = $v;
            }
            $condition['company_code'] = Common_Company::getCompanyCode();
//             print_r($condition);exit;
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                
                $rows = $this->serviceClass->getByCondition($condition,"*", $pageSize, $page, array('platform asc','user_account desc'));
                foreach($rows as $k=>$v){
                    $v['status'] = $v['status']=='1'?'启用':'禁用';
                    $rows[$k] = $v;
                }
//                 print_r($rows);exit;
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $con = array('company_code'=>Common_Company::getCompanyCode());
        $fields = array('user_account','platform_user_name','short_name','platform');
        $platformUser = Service_PlatformUser::getByCondition($con,$fields,0,0,'platform desc');        
        $this->view->platformUser = $platformUser;
        $platform = Service_Platform::getAll();
        foreach($platform as $k=>$v){
            $v['platform'] = strtolower($v['platform']);
            $v['platform_name'] = strtolower($v['platform_name']);
            $platform[$k] = $v;
        }
//         print_r($platform);exit;
        $this->view->platform = $platform;
        echo Ec::renderTpl($this->tplDirectory . "run_control_index.tpl", 'layout');
    }


    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'run_id')) {
//             $rows=$this->serviceClass->getVirtualFields($rows);
            $result = array('state' => 1, 'message' => '', 'data' => $rows);
        }
        die(Zend_Json::encode($result));
    }
    public function editAction()
    {
        $return = array(
                'state' => 0,
                'message' => '',
                'errorMessage' => array('Fail.')
        );
        if ($this->_request->isPost()) {
            try{
                $params = $this->_request->getParams();
                $row = array(
                        'start_time' => $this->getRequest()->getParam('start_time', '00:00:00'),
                        'end_time' => $this->getRequest()->getParam('end_time', '24:00:00'),
                        'run_interval_minute' => $this->getRequest()->getParam('run_interval_minute', '30'),
                        'last_run_time' => $this->getRequest()->getParam('last_run_time', date('Y-m-d H:i:s',strtotime('-3 months'))),
                        'status' => $this->getRequest()->getParam('status', '1'),
                );
                
                if(empty($row['start_time'])){
                    throw new Exception('start_time不能为空');
                }
    
                if(empty($row['end_time'])){
                    throw new Exception('end_time不能为空');
                }
    
                if(empty($row['run_interval_minute'])){
                    throw new Exception('run_interval_minute不能为空');
                }

                if(empty($row['last_run_time'])){
                    throw new Exception('last_run_time不能为空');
                }
                $paramId = $params['run_id'];
                foreach($row as $k=>$v){
                    $row[$k] = trim($v);
                }
                //             echo $paramId;exit;
                if (!empty($paramId)) {
                    $result = $this->serviceClass->update($row, $paramId);
                } else {
                    $result = $this->serviceClass->add($row);
                }
                if ($result) {
                    $return['state'] = 2;
                    $return['message'] = array('Success.');
                }
            }catch(Exception $e){
                $return['message'] = array($e->getMessage());
            }
    
        }
        die(Zend_Json::encode($return));
    }
    public function deleteAction()
    {
        $result = array(
                "state" => 0,
                "message" => "该功能禁用"
        );

        die(Zend_Json::encode($result));
        
        $result = array(
            "state" => 0,
            "message" => "Fail."
        );
        
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            if (!empty($paramId)) {
                if ($this->serviceClass->delete($paramId)) {
                    $result['state'] = 1;
                    $result['message'] = 'Success.';
                }
            }
        }
        die(Zend_Json::encode($result));
    }
    /**
     * 定时任务初始化
     * @throws Exception
     */
    public function initAccAction(){
        $platform = $this->getParam('platform', '');
        $acc = $this->getParam('acc', '');
        
        $return = Service_User::initAcc(Common_Company::getCompanyCode(), $platform, $acc);
        echo $return['message'];
    }
}