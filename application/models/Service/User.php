<?php
class Service_User extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_User|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_User();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "user_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "user_id")
    {
        $model = self::getModelInstance();
        return $model->delete($value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'user_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }

    public static function getLeftJoinByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getLeftJoinByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();

        $validateArr[] = array("name" => EC::Lang('userCode'), "value" => $val["user_code"], "regex" => array("require",));
        $validateArr[] = array("name" => EC::Lang('user_password'), "value" => $val["user_password"], "regex" => array("require",));
        $validateArr[] = array("name" => EC::Lang('userName'), "value" => $val["user_name"], "regex" => array("require",));
        $validateArr[] = array("name" => EC::Lang('userNameEn'), "value" => $val["user_name_en"], "regex" => array("require",));
        
//      $validateArr[] = array("name" => EC::Lang('departmentName'), "value" => $val["ud_id"], "regex" => array("require",));
//      $validateArr[] = array("name" => EC::Lang('positionName'), "value" => $val["up_id"], "regex" => array("require",));
        return Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public function getFields()
    {
        $row = array(

            'E0' => 'user_id',
            'E1' => 'user_code',
            'E2' => 'user_password',
            'E3' => 'user_name',
            'E4' => 'user_name_en',
            'E5' => 'user_status',
            'E6' => 'user_email',
            'E7' => 'ud_id',
            'E8' => 'up_id',
            'E9' => 'user_password_update_time',
            'E10' => 'user_phone',
            'E11' => 'user_mobile_phone',
            'E12' => 'user_note',
            'E13' => 'user_supervisor_id',
            'E14' => 'user_add_time',
            'E15' => 'user_last_login',
            'E16' => 'user_update_time',
            'E17' => 'is_admin',
        	'E18' => 'platform_token',
        	'E19' => 'user_sources',
        	'E20'=>'parent_user_id'
        );
        return $row;
    }

    
    /**
     * @后台登录
     * @param array $params
     * @return array
     */
    public static function login($params = array(),$apiLogin=false)
    {
        $result = array('state' => 0, 'message' => '');
        $userName = isset($params['userName']) ? $params['userName'] : '';
        $userPass = isset($params['userPass']) ? $params['userPass'] : '';
        $authCode = isset($params['authCode']) ? $params['authCode'] : '';
        $valid = isset($params['valid']) ? $params['valid'] : '0'; //是否需要要判断验证码
        if ($valid == '1') {
            $verifyCodeObj = new Common_Verifycode();
            $verifyCodeObj->set_sess_name('AdminVerifyCode'); //重置验证码
            if (empty($authCode) || !$verifyCodeObj->is_true($authCode)) {
                $result['message'] = Ec::Lang('verifyCodeMessage');
                return $result;
            }
        }

        if (empty($userName) || empty($userPass) || strlen($userName) > 64) {
            $result['message'] = Ec::Lang('loginMessage');
            return $result;
        }

        $model = self::getModelInstance();
        $userArr = $model->getByField($userName, 'user_code');
        if (empty($userArr)) {
            $result['message'] = Ec::Lang('loginMessage');
            return $result;
        }
        if($apiLogin){//api登陆 
            if ($userPass!==$userArr['user_password']) {
                $result['message'] = Ec::Lang('loginMessage');
                return $result;
            }
        }else{
            if (!Ec_Password::comparePassword($userPass, $userArr['user_password'])) {
                $result['message'] = Ec::Lang('loginMessage');
                return $result;
            } 
        }
        
        // TODO DB2
        $db = Common_Common::getAdapterForDb2();
        
        $session = new Zend_Session_Namespace('userAuthorization');
        $session->unsetAll();
        $date = date('Y-m-d');
        $userLastDate = $userArr['user_password_update_time'];
        $days = round((strtotime($date) - strtotime($userLastDate)) / 3600 / 24);
        
        if ($userArr['user_status'] != '1') {
            $result['message'] = Ec::Lang('userActivate');
            $result['state'] = 2;
            Service_UserLoginLog::add(array('user_id' => $userArr['user_id'], 'ull_status' => '0', 'ull_note' => 'Account is not activated'));
            return $result;
        } else {
            $sql = "select * from csi_customer where customer_id='{$userArr['customer_id']}';";
            $csi_customer = $db->fetchRow($sql);
            if(!$csi_customer){
                $result['message'] = Ec::Lang('userActivate');
                $result['state'] = 2;
                Service_UserLoginLog::add(array('user_id' => $userArr['user_id'], 'ull_status' => '0', 'ull_note' => 'Account Customer Info is not activated'));
                return $result;
            }
            $userArr['csi_customer'] = $csi_customer;
            
            
            unset($userArr['user_password']);
            unset($userArr['user_note']);
            $upRow = Service_UserPosition::getByField($userArr['up_id'], 'up_id');
            $userArr['upl_id'] = $upRow['upl_id'];
            $session->user = $userArr;
            $session->csi_customer = $csi_customer;
            $session->userId = $userArr['user_id'];
            $session->customer_id = $userArr['customer_id'];
            $session->customer_code = $csi_customer['customer_code'];
            $session->userCode = $userArr['user_code'];
            $session->isLogin = true;
            $session->message = '';
            $model->update(array('user_last_login' => date('Y-m-d H:i:s')), $userArr['user_id']);
            Service_UserLoginLog::add(array('user_id' => $userArr['user_id']));
            if (($days >= 60)) {
                $session->message = Ec::Lang('updateLastPass');
            }
            $result = array('state' => 1, 'message' => 'Success');

        }
        return $result;
    }

    // 用户
    private static $_user = array();
    private static $_customer = array();
    
    public static function setUser($user, $customer) {
    	self::$_user = $user;
    	self::$_customer = $customer;
    }
    
    public static function destroyUser($user, $customer) {
    	self::$_user = array();
    	self::$_customer = array();
    }
    
    // 获取用户属性
    public static function getUserAttr($attrName) {
    	if(isset(self::$_user[$attrName])) {
    		return self::$_user[$attrName];
    	}
    	return '';
    }
    
    // 获取客户属性
    public static function getCustomerAttr($attrName) {
    	if(isset(self::$_customer[$attrName])) {
    		return self::$_customer[$attrName];
    	}
    	return '';
    }
    
    public static function getUser()
    {
    	$userAuth = new Zend_Session_Namespace('userAuthorization');
    	return isset($userAuth->user) ? $userAuth->user : self::$_user;
    }
    public static function getUserId()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->userId) ? $userAuth->userId : self::getUserAttr('user_id');
    }

    public static function getUserName()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->user) ? $userAuth->user['user_name'] : self::getUserAttr('user_name');
    }
    
    public static function getUserCompanyCode()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->company_code) ? $userAuth->company_code : self::getCustomerAttr('customer_code');
    }

    public static function getCustomerId()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->user) ? $userAuth->user['customer_id'] : self::getCustomerAttr('customer_id');
    }    

    public static function getChannelid()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->user) ? $userAuth->user['customer_channelid'] : self::getCustomerAttr('customer_channelid');
    }
    
    public static function getCustomerCode()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->csi_customer) ? $userAuth->csi_customer['customer_code'] : self::getCustomerAttr('customer_code');
    }

    public static function getCustomer()
    {
    	$userAuth = new Zend_Session_Namespace('userAuthorization');
    	return isset($userAuth->csi_customer) ? $userAuth->csi_customer  : self::$_customer;
    }
    public static function getLoginUser()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->user) ? $userAuth->user : self::$_user;
    }

    public static function getTmsId()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->user) ? $userAuth->user['tms_id'] : self::getUserAttr('tms_id');
    }
    
    public static function getOgId()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        return isset($userAuth->csi_customer) ? $userAuth->csi_customer['og_id'] : 0;
    }
    
    public static function getUserWarehouseId()
    {
        return 1;
    }

    public static function getUserWarehouseIds()
    {
        $userAuth = new Zend_Session_Namespace('userAuthorization');
        $condition = array(
        		'company_code'=> Common_Company::getCompanyCode(),
        		'warehouse_status'=>1
        		);
        $result = Service_Warehouse::getByCondition($condition);
        $warehouseIdArr = array();
        foreach ($result as $key => $value) {
        	$warehouseIdArr[] = $value['warehouse_id'];
        }
        return $warehouseIdArr;
    }
    
    public static function getUserWarehouse(){
    	$userAuth = new Zend_Session_Namespace('userAuthorization');
    	$condition = array(
    			'company_code'=> Common_Company::getCompanyCode(),
    			'warehouse_status'=>1
    	);
    	$result = Service_Warehouse::getByCondition($condition);
    	return $result;
    }

    /**
     * @获取用户当前权限
     * @添加快捷导航页面、用户权限页面
     * @param string $userId
     * @return array
     */
    public static function getUserRightByUserId($userId=''){
        $language = Ec::getLang(1);
        $result = array(
            "state" => 0,
            "data" => array(),
            "ids" => array(),
        );
        $showFields = array(
            'ur_name' . $language . ' as title',
            'ur_module as module',
            'um_id as menu_id',
            'ur_id as id',
        );
        $menuArr = Common_DataCache::getUserMenu();
        $result['data'] = Service_UserRight::getByCondition(array(), $showFields, 0, 0, array('um_id', 'ur_module', 'ur_sort'));
        foreach ($result['data'] as $key => $val) {
            $result['data'][$key]['menu'] = isset($menuArr[$val['menu_id']]['um_title' . $language]) ? $menuArr[$val['menu_id']]['um_title' . $language] : '';
        }
        $rightUraIdArr = array();
        if (!empty($userId)) {
            $userRow= Service_User::getByField($userId,'user_id');
            $rightAction = Service_UserRightMap::getByCondition(array('user_id' => $userId), 'ur_id');
            if (!empty($rightAction)) {
                foreach ($rightAction as $v) {
                    $rightUraIdArr[$v['ur_id']] = $v['ur_id'];
                }
            }
            if (!empty($userRow) && !empty($userRow['up_id'])) {
                $rightAction = Service_UserPositionRightMap::getByCondition(array('up_id' => $userRow['up_id']), 'ur_id');
                if (!empty($rightAction)) {
                    foreach ($rightAction as $v) {
                        $rightUraIdArr[$v['ur_id']] = $v['ur_id'];
                    }
                }
            }
        }
        $result['ids'] = $rightUraIdArr;
        $result['state'] = 1;
        return $result;
    }

    /**
     * @desc 只获取用户已有权限
     * @用于绑定快捷导航
     * @param string $userId
     * @return array
     */
    public static function getUserRightCustomByUserId($userId=''){
        $result=self::getUserRightByUserId($userId);
        $data=array();
        $quickArr=array();
        if(isset($result['ids']) && !empty($result['ids']) ){
            $quickData=Service_UserRightHeaderMap::getSkyeQuiKey();
            //已选择的
            if(!empty($quickData)){
                foreach($quickData as $k =>$v){
                    $quickArr[$v['urlId']]=$v['urlId'];
                }
            }
            foreach($result['data'] as $key =>$val){
                $val['selected']=isset($quickArr[$val['id']])?'1':'0';
                if(isset($result['ids'][$val['id']])){
                    $data[$key]=$val;
                }
            }
        }
        unset($result);
        return $data;
    }

    public static function getPlatformUser($app_type='do',$platform=''){
        $userId = self::getUserId();
        $con = array(
            'user_id' => $userId,
            'app_type' => $app_type,
            'platform'=>$platform,
            'filter_type' => 'ua'
        );
        $result = Service_FilterSet::getByCondition($con);
        $temp = array();
        foreach($result as $v){
            $temp[] = $v['user_account'];
        }
        return $temp;
    }

    public static function getPlatformUserNew($app_type='do',$platform=''){
        $userId = self::getUserId();
        $con = array(
            'user_id' => $userId,
            'platform'=>$platform,
            'app_type' => $app_type,
            'filter_type' => 'ua'
        );
        $result = Service_FilterSet::getByCondition($con);        
        $temp = array();
        foreach($result as $v){
            $temp[$v['user_account']] = $v;
        }
        return $temp;
    }

    public static function getPlatformUserAll($platform=''){
        $con = array(
            'company_code' => Common_Company::getCompanyCode(),
            'platform' => $platform,
            'status' => '1'
        );
        
        $user_account_arr = Service_PlatformUser::getByCondition($con, array(
            'platform',
            'user_account',
            'platform_user_name'
        ));
        $temp = array();
        foreach($user_account_arr as $v){
            $temp[$v['user_account']] = $v;
        }
        return $temp;
    }
    
    /**
     * 初始化定时任务
     * @param unknown_type $companyCode
     * @param unknown_type $platform
     * @param unknown_type $acc
     * @throws Exception
     */
    public static function initAcc($companyCode,$platform,$acc){
        $return = array('ask'=>0,'message'=>'Fail');
        try{
            if(empty($companyCode)){
                throw new Exception('公司代码不能为空');
            }
            
            if(empty($platform)){
                throw new Exception('平台不能为空');
            }
            if(empty($acc)){
                throw new Exception('账号名称不能为空');
            }
            $con = array(
                'platform' => $platform,
                'user_account' => $acc,
                'company_code' => $companyCode
            );
            $exist = Service_PlatformUser::getByCondition($con);
            if(empty($exist)){
                throw new Exception('该账号不存在');
            }
            $result = Service_RunControl::getByCondition($con);
            foreach($result as $v){
                Service_RunControl::delete($v['run_id'], 'run_id');
            }
            switch($platform){
                case 'ebay':
                    $sqls = "insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('ebay','loadEbayFeedback','公司代码','账号名称','00:00:00','24:00:00','1440',now()
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('ebay','loadEbayItem','公司代码','账号名称','00:00:00','24:00:00','30',now()
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('ebay','loadEbayMessage','公司代码','账号名称','00:00:00','24:00:00','30',now()
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('ebay','loadEbayOrder','公司代码','账号名称','00:00:00','24:00:00','30',now()
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('ebay','loadEbayUserCases','公司代码','账号名称','00:00:00','24:00:00','1440',now()
                        );
                        ";
                    break;
                case 'paypal':
                    $sqls = "insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('paypal','callRefundTransaction','公司代码','paypal账号','00:00:00','24:00:00','60',DATE_add(now(),INTERVAL -1 DAY )
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('paypal','callTransactionDetail','公司代码','paypal账号','00:00:00','24:00:00','60',DATE_add(now(),INTERVAL -1 DAY )
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('paypal','callTransactionSearch','公司代码','paypal账号','00:00:00','24:00:00','60',DATE_add(now(),INTERVAL -1 DAY )
                        );
                        ";
                    break;
                case 'amazon':
                    $sqls = "insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('amazon','callListOrders','公司代码','amazon账号','00:00:00','24:00:00','60',now()
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('amazon','callListOrderItems','公司代码','amazon账号','00:00:00','24:00:00','60',now()
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('amazon','callOrderFulfillment','公司代码','amazon账号','00:00:00','24:00:00','60',now()
                        );
                        insert into run_control(platform,run_app,company_code,user_account,start_time,end_time,run_interval_minute,last_run_time) values('amazon','callOrderFulfillmentResult','公司代码','amazon账号','00:00:00','24:00:00','60',now()
                        );
                        ";
                    break;
            }
            $sqls = preg_replace('/[;\s]+$/', '', $sqls);
            $sqls = preg_replace('/公司代码/', $companyCode, $sqls);
            $sqls = preg_replace('/账号名称/', $acc, $sqls);
            $sqls = preg_replace('/paypal账号/', $acc, $sqls);
            $sqls = preg_replace('/amazon账号/', $acc, $sqls);
            $sqls = explode(';', $sqls);
            
            $db = Common_Common::getAdapter();
            foreach($sqls as $k => $v){
                if(empty($v)){
                    continue;
                }
                $db->query($v);
            }
            $return['ask'] = 1;
            $return['message'] = '初始化定时任务成功';
        }catch (Exception $e){
            $return['message'] = $e->getMessage();
        }
        return $return;        
        
    }
    
    public static function getConditionByIn($value, $field = 'user_id', $colums = "*"){
    	$model = self::getModelInstance();
    	return $model->getConditionByIn($value, $field, $colums);
    }
}