<?php
class Auth_ShippingMethodPlatformController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/shipping_method_platform/";
        $this->serviceClass = new Service_ShippingMethodPlatform();
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

            $params = $this->_request->getParams();
 
            $condition = array('platform' => $this->getRequest()->getParam('platform', ''),
                'name_cn' => $this->getRequest()->getParam('name_cn', ''),
                'name_en' => $this->getRequest()->getParam('name_en', ''),
                'shipping_method_code' => $this->getRequest()->getParam('shipping_method_code', ''),
                'short_code' => $this->getRequest()->getParam('short_code', ''),
                'site' => $this->getRequest()->getParam('site', ''),
                'user_account' => $this->getRequest()->getParam('user_account', ''),
                'platform_shipping_mark' => $this->getRequest()->getParam('platform_shipping_mark', ''),
            );
            foreach($condition as $k=>$v){
                $condition[$k] = trim($v);
            }
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {                  
                $rows = $this->serviceClass->getByCondition($condition, '*', $pageSize, $page, array());
                foreach($rows as $k=>$v){
                    $v['can_edit'] = empty($v['company_code'])?0:1;
                    if(Service_User::getUserId()==1&&empty($v['company_code'])){//管理员，可编辑
                        $v['can_edit'] = 1;
                    }
                    $v['company_code'] = empty($v['company_code'])?'系统':'本账号';
                    $rows[$k] = $v;
                }
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        echo Ec::renderTpl($this->tplDirectory . "shipping_method_platform.tpl", 'layout');
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
                        'platform' => $this->getRequest()->getParam('platform', ''),
                        'name_cn' => $this->getRequest()->getParam('name_cn', ''),
                        'name_en' => $this->getRequest()->getParam('name_en', ''),
                        'shipping_method_code' => $this->getRequest()->getParam('shipping_method_code', ''),
                        'short_code' => $this->getRequest()->getParam('short_code', ''),
                        'site' => $this->getRequest()->getParam('site', ''),
                        'user_account' => $this->getRequest()->getParam('user_account', ''),
                        'carrier' => $this->getRequest()->getParam('carrier', ''),
                        'platform_shipping_mark' => $this->getRequest()->getParam('platform_shipping_mark', ''),
                        'level' => $this->getRequest()->getParam('level', '0'),
                );
                if(empty($row['platform'])){
                    throw new Exception('平台代码不能为空');
                }

                if(empty($row['name_cn'])){
                    throw new Exception('中文名不能为空');
                }

                if(empty($row['name_en'])){
                    throw new Exception('英文名不能为空');
                }

                if(empty($row['shipping_method_code'])){
                    throw new Exception('运输代码不能为空');
                }

                if(empty($row['short_code'])){
                    throw new Exception('运输代码简称不能为空');
                }


                if($row['platform']=='amazon'&&empty($row['carrier'])){
                    throw new Exception('承运商代码不能为空');
                }
                
                $paramId = $params['shipping_method_id'];
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

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'shipping_method_id')) {
//             $rows = $this->serviceClass->getVirtualFields($rows);
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
                    $result['message'] = 'Success.';
                }
            }
            die(Zend_Json::encode($result));
        }
    }
}