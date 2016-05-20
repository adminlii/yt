<?php
class Auth_ServiceController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/user/";
        $this->serviceClass = new Service_User();
//         Service_User::getPlatformUser('do');
    }

    public function listAction()
    {

        $kf_pos_row = Service_Config::getByField('KEFU_POSITION_ID','config_attribute');
        $op_pos_row = Service_Config::getByField('OPERATOR_POSITION_ID','config_attribute');
        if(empty($kf_pos_row)||empty($op_pos_row)){
            header("Content-type: text/html; charset=utf-8"); 
            die('请设置好客服和操作员职位，KEFU_POSITION_ID & OPERATOR_POSITION_ID');
        }
        $kf_pos_id = $kf_pos_row['config_value'];
        $op_pos_id = $op_pos_row['config_value'];
        
        $statusArray = Common_Type::status('auto');
        $departmentArray = Common_DataCache::getDepartment();
        $positionArray = Common_DataCache::getUserPosition();
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
//             print_r($condition);exit;

            $condition['company_code'] = Common_Company::getCompanyCode();
            
            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields = array(
                    'user_code',
                    'user_name',
                    'user_name_en',
                    'user_status',
                    'ud_id',
                    'up_id',
                    'user_mobile_phone',
                    'user_last_login',
                    'user_id',
                );
//                 $showFields = $this->serviceClass->getFieldsAlias($showFields);
//                 print_r($showFields);exit;
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array('user_id asc'));
                $language = Ec::getLang(1);
                foreach ($rows as $key => $val) {
                    $rows[$key]['ud_name'] = isset($departmentArray[$val['ud_id']]['ud_name' . $language]) ? $departmentArray[$val['ud_id']]['ud_name' . $language] : '';
                    $rows[$key]['up_name'] = isset($positionArray[$val['up_id']]['up_name' . $language]) ? $positionArray[$val['up_id']]['up_name' . $language] : '';
                    $rows[$key]['user_status'] = $statusArray[$val['user_status']];
                    $con = array(
                            'filter_type' => 'ua',
                            'user_id' => $val['user_id']
                    );
                    $filters = Service_FilterSet::getByCondition($con);
                    $bind = array();
                    foreach($filters as $v){
                        $bind[$v['app_type']][] = $v;
                    }
                    $rows[$key]['bind'] = $bind;
                }
//                 print_r($rows);exit;
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        
        $language = Ec::getLang();
        $this->view->status = $statusArray;
        $this->view->department = $departmentArray;
        $this->view->position = $positionArray;
        $this->view->statusArr = Common_Type::status($language);
        $this->view->userArr = Service_User::getByCondition(array(),array('user_id','user_name','user_name_en'));
        echo Ec::renderTpl($this->tplDirectory . "user_bind_account.tpl", 'layout');
    }
    
    public function getUserAccountAction(){
        $con = array(
            'company_code' => Common_Company::getCompanyCode(),
//             'platform' => 'ebay',
            'status' => '1',
        );
        $result = Service_PlatformUser::getByCondition($con, array('user_account','company_code','platform_user_name','platform'),0,0,'platform desc');
        die(Zend_Json::encode($result));
    }

    public function bindPlatformUserAction(){
        $userId = $this->getRequest()->getParam('user_id', '0');
        $type = $this->getRequest()->getParam('type', 'do');
        $platform_user = $this->getRequest()->getParam('account', array());
        $platform_user = array_unique($platform_user);
        try{           
            $con = array(
                'app_type' => $type,
                'filter_type' => 'ua',
                'user_id' => $userId
            );
            $row = Service_FilterSet::getByCondition($con);
            foreach($row as $v){
                Service_FilterSet::delete($v['filter_id'],'filter_id');
            }
            foreach($platform_user as $v){
                if(empty($v)){
                    continue;
                }
                $exp = explode('*##*', $v);
                $platform = $exp[0];
                $acc = $exp[1];
                $platacc = $exp[2];
                $row = array(
                        'user_id'=>$userId,
                        'filter_value' => $acc,
                        'user_account'=>$acc,
                        'platform_user_name'=>$platacc,
                        'platform'=>$platform,
                        'app_type' => $type,
                        'filter_type' => 'ua'
                );
                Service_FilterSet::add($row);
            }
            $result = array(
                'ask' => 1,
                'message' => '操作成功'
            );
        }catch(Exception $e){
            $result = array(
                'ask' => 0,
                'message' => '操作失败'
            );
        }
        
        die(Zend_Json::encode($result));
    }
    
    public function mailAction(){
    	$params = array(
    			'bodyType' => 'text',
    			'email' => array('280859158@qq.com'),
    			'subject' => 'eBay Error' . date('Y-m-d H:i:s'),
    			'body' => print_r('GetMyMessagesResponse',true),
    	);
    	$rr = Common_Email::send($params);
    	print_r($rr);exit;
    }

    public function changeAction(){
        $db = Common_Common::getAdapter();
        $sql = 'select * from filter_set_copy';
        $data = $db->query($sql);
        foreach($data as $v){
            
            $con = array(
                    'app_type' => $v['app_type'],
                    'filter_type' => 'ua',
                    'user_id' => $v['user_id'],
            );
            $rows = Service_FilterSet::getByCondition($con);
            foreach($rows as $r){
                Service_FilterSet::delete($r['filter_id'],'filter_id');
            }
            if(empty($v['filter_value'])){
                continue;
            }
            $str = $v['filter_value'];
            $arr = explode(',', $str);
            foreach($arr as $vv){
                $platform = preg_match('/amazon/', $vv)?'amazon':'ebay';
                $pu = Service_PlatformUser::getByField($vv,'user_account');
                $platform_user_name = $pu['platform_user_name'];
                $row = array(
                        'user_id'=>$v['user_id'],
                        'filter_value' => $vv,
                        'user_account'=>$vv,
                        'platform_user_name'=>$platform_user_name,
                        'platform'=>$platform,
                        'app_type' => $v['app_type'],
                        'filter_type' => 'ua'
                );
                Service_FilterSet::add($row);
            }
            
        }
        echo '===============================';
    }
}