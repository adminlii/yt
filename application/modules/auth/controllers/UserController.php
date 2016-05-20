<?php
class Auth_UserController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "auth/views/user/";
        $this->serviceClass = new Service_User();
    }

    public function listAction()
    {
        $statusArray = Common_Type::status('auto');
        $userType =  Common_type::userType('auto');

        $user = Service_User::getUser();
        
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
            $condition['customer_id'] = Service_User::getCustomerId();
    		$condition['customer_channelid'] = Service_User::getChannelid();
            

    		if(!$user['is_admin']){
    			$condition['auth_user_id'] = $user['user_id'];
    		}
//     		print_r($user);exit;
//     		print_r($condition);exit;
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
                    'is_admin',
                    'parent_user_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array('user_id asc'));
                $language = Ec::getLang(1);
                foreach ($rows as $key => $val) {
                    $rows[$key]['E7'] = isset($departmentArray[$val['E7']]['ud_name' . $language]) ? $departmentArray[$val['E7']]['ud_name' . $language] : '';
                    $rows[$key]['E8'] = isset($positionArray[$val['E8']]['up_name' . $language]) ? $positionArray[$val['E8']]['up_name' . $language] : '';
                    $rows[$key]['E5'] = $statusArray[$val['E5']];
                    $rows[$key]['E17'] = $userType[$val['E17']];
                    if($val['E20']){
                    	$parentUser = Service_User::getByField($val['E20'],'user_id'); 
                    	if($parentUser){
                    		$rows[$key]['parent_user'] = $parentUser;                    		
                    	}
                    }
                    
                }
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
        echo Ec::renderTpl($this->tplDirectory . "user_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array(
                'Fail.'
            )
        );
        
        if($this->_request->isPost()){
            try{
                
                $params = $this->_request->getParams();
                $row = array(
                    'user_id' => '',
                    'user_code' => '',
                    'user_password' => '',
                    'user_name' => '',
                    'user_name_en' => '',
                    'user_status' => '',
                    'user_email' => '',
                    // 'ud_id' => '',
                    // 'up_id' => '',
                    // 'user_supervisor_id' => '',
                    'user_phone' => '',
                    'user_mobile_phone' => '',
                    'user_note' => ''
                );
                $row = $this->serviceClass->getMatchEditFields($params, $row);
                $row['tms_id'] = Service_User::getTmsId();
                $paramId = $row['user_id'];
                if(! empty($row['user_id'])){
                    unset($row['user_id']);
                }
                $errorArr = $this->serviceClass->validator($row);
                
                if(! empty($errorArr)){
                    $return = array(
                        'state' => 0,
                        'message' => '',
                        'errorMessage' => $errorArr
                    );
                    die(Zend_Json::encode($return));
                }
                $row['user_update_time'] = date('Y-m-d H:i:s');
                if(! empty($row['user_password'])){
                    $row['user_password'] = Ec_Password::getHash($row['user_password']);
                }else{
                    unset($row['user_password']);
                }
                if(! empty($paramId)){
                    $row['customer_id'] = Service_User::getCustomerId();
                    $row['customer_channelid'] = Service_User::getChannelid();
                    unset($row['user_code']);
                    $emailExist = Service_User::getByField($row['user_email'], 'user_email');
                    if($emailExist&&$emailExist['user_id']!=$paramId){
                        throw new Exception('邮箱已被使用');
                    }
                    $result = $this->serviceClass->update($row, $paramId);
                }else{
                    $codeExist = Service_User::getByField($row['user_code'], 'user_code');
                    if($codeExist){
                        throw new Exception('用户名已被使用');
                    }
                    if(! preg_match('/^([a-zA-Z0-9_\-]+)$/', $row['user_code'])){
                        throw new Exception('用户名只能由字母，数字，下划线，中划线组成');
                    }
                    
                    if(!empty($row['user_email'])){
	                    $emailExist = Service_User::getByField($row['user_email'], 'user_email');
	                    if($emailExist){
	                        throw new Exception('邮箱已被使用');
	                    }
                    }
                    $row['customer_id']=Service_User::getCustomerId();
                    $row['customer_channelid'] = Service_User::getChannelid();
                    $row['parent_user_id'] = Service_User::getUserId();
                    $result = $this->serviceClass->add($row);
//                     $updateRow = array(
//                         'customer_id' => Service_User::getCustomerId()
//                     );
//                     $this->serviceClass->update($updateRow, $result);
                }
                if($result){
                    $return['state'] = 1;
                    $return['message'] = array(
                        'Success.'
                    );
                }
            }catch(Exception $e){
                $return['message'] = array(
                     $e->getMessage()
                );
                $return['errorMessage'] = array(
                    $e->getMessage()
                );
            }
        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'user_id')) {
            $rows = $this->serviceClass->getVirtualFields($rows);
            unset($rows['E2']);
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
        }
        die(Zend_Json::encode($result));
    }

    public function getRightAction()
    {
        $result = array(
            "state" => 0,
            "data" => array(),
            "ids" => array(),
            "message" => Ec::Lang('operationFail')
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            $language = Ec::getLang(1);
            $showFields = array(
                'ur_name' . $language . ' as title',
                'ur_module as module',
                'um_id as menu_id',
                'ur_id as id',
            );
            $menuArr = Common_DataCache::getUserMenu();
            $user = Service_User::getUser();            
            $con = array('ur_type'=>1);
            if(!$user['is_admin']){
            	//
            }
            $result['data'] = Service_UserRight::getByCondition($con, $showFields, 0, 0, array('um_id', 'ur_module', 'ur_sort'));
            foreach ($result['data'] as $key => $val) {
                $result['data'][$key]['menu'] = isset($menuArr[$val['menu_id']]['um_title' . $language]) ? $menuArr[$val['menu_id']]['um_title' . $language] : '';
            }
            $rightUraIdArr = array();
            if (!empty($paramId)) {
               $userRow= Service_User::getByField($paramId,'user_id');
                $rightAction = Service_UserRightMap::getByCondition(array('user_id' => $paramId), 'ur_id');
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
        }
//         print_r($result);exit;
        die(Zend_Json::encode($result));
    }

    //用户权限
    private function getRight($user_id){
    	$sql = "
    	select distinct c.*,d.*,b.visiable from user a INNER JOIN user_right_map b on a.user_id=b.user_id INNER JOIN user_right c on b.ur_id=c.ur_id
    	INNER JOIN user_menu d on c.um_id=d.um_id
    	where
    	1=1
    	and c.ur_type=1
    	and a.user_id='{$user_id}'
    	order by d.um_sort asc,c.ur_sort asc
    	";

    	$data = Common_Common::fetchAll($sql);
    	return $data;
    }
    public function getRightTmsAction()
    {
    	$result = array(
    			"state" => 0,
    			"data" => array(),
    			"ids" => array(),
    			"message" => Ec::Lang('operationFail')
    	);
    	if ($this->_request->isPost()) {
    		$user_id = Service_User::getUserId();
    		$paramId = $this->_request->getPost('paramId');
    		$language = Ec::getLang(1);
    		
    		$user = Service_User::getUser();
    		//当前用户所拥有的权限
    		$data = $this->getRight($user['user_id']);
			
    		//选择用户所拥有的权限
    		$ur_arr = $this->getRight($paramId);
    		$urIdArr = array();
    		
    		foreach($ur_arr as $ur){
    			$urIdArr[] = $ur['ur_id'];
    		}
//     		print_r($data);//exit;
    		$dataArr = array();
    		foreach($data as $row){
    			if($user_id!=$paramId){
    				if(in_array($row['ur_id'],$urIdArr)){
    					$row['visiable'] = 1;
    				}else{
    					$row['visiable'] = 0;
    				}
    			}
//     			if($row['um_id']==12){
//     				continue;
//     			}
				$menu = array (
						'um_id' => $row ['um_id'],
						'parent_id' => $row ['parent_id'],
						'us_id' => $row ['us_id'],
						'um_title' => $row ['um_title'],
						'um_title_en' => $row ['um_title_en'],
						'um_url' => $row ['um_url'],
						'um_css' => $row ['um_css'],
						'um_color' => $row ['um_color'],
						'um_sort' => $row ['um_sort'] 
				);
				$right = array (
						'ur_id' => $row ['ur_id'],
						'um_id' => $row ['um_id'],
						'ur_name' => $row ['ur_name'],
						'ur_name_en' => $row ['ur_name_en'],
						'ur_description' => $row ['ur_description'],
						'ur_url' => $row ['ur_url'],
						'ur_sort' => $row ['ur_sort'],
						'ur_common' => $row ['ur_common'],
						'ur_type' => $row ['ur_type'],
						'ur_module' => $row ['ur_module'],
						'ur_icon' => $row ['ur_icon'],
						'visiable' => $row ['visiable'], 
				);
// 				print_r($right);
				$dataArr[$row ['um_sort'].'_'.$row ['um_id']]['menu'] = $menu;
				$dataArr[$row ['um_sort'].'_'.$row ['um_id']]['right'][$row ['ur_id']] = $right;
			}
			ksort($dataArr);
// 			print_r($data);
// 			print_r($dataArr);exit;
    		$result['data'] = $dataArr;
    		$result['state'] = 1;
    	}
// 			print_r($result);exit;
    	die(Zend_Json::encode($result));
    }
    public function editRightAction()
    {
        $result = array(
            "state" => 0,
            "message" => Ec::Lang('operationFail')
        ); 
        if ($this->_request->isPost()) {
        	try {
        		$user_id = Service_User::getUserId();
        		$customer_id = Service_User::getCustomerId();
        		 
        		$permissionId = $this->_request->getPost('permissionId');
        		$actions = $this->_request->getParam('actionId', array());
        		sort($actions);
        		if(empty($permissionId)){
        			throw new Exception('用户参数错误');
        		}
        		$u = Service_User::getByField($permissionId);
        		if(!$u){
        			throw new Exception('用户异常');        			
        		}
        		if($u['customer_id']!=$customer_id){
        			throw new Exception('用户异常');           			
        		}

        		$urs = $this->getRight($user_id);
        		$urIdArr = array();
        		foreach($urs as $ur){
        			$urIdArr[] = $ur['ur_id'];
        		}
        		if(!array_intersect($urIdArr,$actions)){
        			throw new Exception('非法操作');
        		}
				//删除已有的权限
        		Service_UserRightMap::delete($permissionId,'user_id');
//         		echo $permissionId;exit;
        		if($user_id==$permissionId){//编辑自己
//         			print_r($urIdArr);//exit;   
//         			print_r($actions);exit;   			 
        			foreach($urs as $ur){
        				$ur_id = $ur['ur_id'];
        				if($ur_id==2){
        					continue;
        				}
        				$visiable = in_array($ur['ur_id'],$actions)?'1':'0';
        				$row = array('user_id'=>$permissionId,'ur_id'=>$ur['ur_id'],'visiable'=>$visiable);
        				Service_UserRightMap::add($row);
        			}
        		}else{//授权其他账号        			 
        			foreach($actions as $ur_id){
        				if($ur_id==2){
        					continue;
        				}
        				$row = array('user_id'=>$permissionId,'ur_id'=>$ur_id,'visiable'=>'1');
        				Service_UserRightMap::add($row);
        			}
        		}
				//用户中心
        		$row = array('user_id'=>$permissionId,'ur_id'=>'2','visiable'=>'1');
        		Service_UserRightMap::add($row);
        		
        		$result['state'] = 1;
        		$result['message'] = 'Success';
        	} catch (Exception $e) {
        		$result['message'] = $e->getMessage();
        	}
        	
        }
        die(Zend_Json::encode($result));
    }
    
    /**
     * 进入个人设置界面
     */
    public function userSetAction(){
    	/*
    	 * 1.查询用户的基本信息
    	 */
//     	$syncConfig = Common_LoginProcess::getSystemSync();
//     	$ez_user_db = $syncConfig['wms']['dbname'];
//     	$sql = "select * from $ez_user_db.ez_user where user_id = '" . Service_User::getUserId() . "'";
    	
//     	$table = new Table_User();
//     	$db = $table->getAdapter();
//     	$data = $db->fetchAll($sql);
//     	if(count($data) == 1){
//     		$this->view->ez_user = $data['0'];
//     	}
    	
    	echo Ec::renderTpl($this->tplDirectory . "user_set.tpl", 'layout');
    }
    
    /**
     * 修改用户资料
     */
    public function modifyUserProfileAction(){
    	$return = array(
    			'state' => 0,
    			'message' => '',
    			'errorMessage' => array('Fail.')
    	);
    	
    	if ($this->_request->isPost()) {
    		/*
    		 * 1. 检查请求类型
    		 */
    		$type = $this->_request->getParam('type','');
    		if(empty($type)){
    			$return['message'] = '非法的请求，请勿修改请求连接.';
    			die(Zend_Json::encode($return));
    		}
    		$user_id = Service_User::getUserId();
    		$sql = "select * from user where user_id = '" . $user_id . "'";
    		$db = Common_Common::getAdapter();
    		$data = $db->fetchAll($sql);
			
    		/*
    		 * 2. 检查用户信息
    		 */
    		if(count($data) == 0){
    			$return['message'] = '未找到用户信息，请重新登录.';
    			die(Zend_Json::encode($return));
    		}
    		/*
    		 * 3. 调用对应的修改方法
    		 */
    		$result_ez_user = $this->view->ez_user = $data['0'];    		
    		if($type == 'password'){
    			$return = $this->modPassword($result_ez_user);
    		}else if($type == 'priority_login'){
    			$return = $this->modPriorityLogin($result_ez_user);
    		}
    		
    	}
    	die(Zend_Json::encode($return));
    }
    
    private function modPassword($result_ez_user){
    	$return = array(
    			'state' => 0,
    			'message' => '',
    			'errorMessage' => array('Fail.')
    	);
    	
    	$old_password = $this->_request->getParam('old_password','');
    	$new_password = $this->_request->getParam('new_password','');
    	$new_password_again = $this->_request->getParam('new_password_again','');
    	
    	$checkBol = true;
    	if(empty($old_password)){
    		$return['message'] = '原始密码不能为空.';
    		$checkBol = false;
    		
    	}else if(!Ec_Password::comparePassword($old_password, $result_ez_user['user_password'])){
    		$return['message'] = '原始密码输入错误，请检查.';
    		$checkBol = false;
    	}else if(empty($new_password)){
    		$return['message'] = '新密码不能为空.';
    		$checkBol = false;
    	}else if(empty($new_password_again)){
    		$return['message'] = '确认新密码不能为空.';
    		$checkBol = false;
    	}else if($new_password != $new_password_again){
    		$return['message'] = '两次密码输入不一致，请检查.';
    		$checkBol = false;
    	}
    	
    	if(!$checkBol){
    		return $return;
    	}
    	
    	$user_id = $result_ez_user['user_id'];
    	$update_password = Ec_Password::getHash($new_password_again);
    	
    	$db = Common_Common::getAdapter();
        $db->beginTransaction();
        $date = date('Y-m-d H:i:s');
        try{	    	
	    	//更新EB user 表
	    	$sql3 = "update user set user_password= '$update_password',user_password_update_time = '$date' where user_id = $user_id";
	    	$result3 = $db->query($sql3);
	    	
	    	if($result3){
		    	$db->commit();
		    	$return['state'] = 1;
		    	$return['message'] = '修改密码成功.';
		    	
		    	//同步数据至EC系统(官网)
		    	$params = array(
		    			'user_code'=>$result_ez_user['user_code'],
		    			'user_password'=>$new_password_again
		    			);
		    	
		    	$obj = new Common_Sync();
		    	$ss = $obj->updateUser($params);
	    	}else{
	    		$db->rollback();
	    		$return['message'] = '修改密码失败，请稍后尝试.';
	    	}
        }catch(Exception $e){
        	$db->rollback();
            $return['message'] = $e->getMessage();
        }
        
        return $return;
    }
    
    public function updateUserAction(){
    	$result_ez_user = array(
    			'user_code'=>'2258801729@qq.com',
    			);
    	$new_password_again = '123456789';
    	//同步数据至EC系统(官网)
    	$params = array(
    			'user_code'=>$result_ez_user['user_code'],
    			'user_password'=>$new_password_again
    	);
    	 
    	$obj = new Common_Sync();
    	$ss = $obj->updateUser($params);
    	print_r($ss);
    }
    
    /**
     * 设置优先等来系统
     * @param unknown_type $result_ez_user
     */
    private function modPriorityLogin($result_ez_user){
    	//
    	$return = array(
    			'state' => 0,
    			'message' => '',
    			'errorMessage' => array('Fail.')
    	);
    	 
    	$priority_login = $this->_request->getParam('priority_login','');
    	if(empty($priority_login)){
    		$return['message'] = '请选择优先登陆的系统.';
    		return $return;
    	}
    	
    	$user_id = $result_ez_user['user_id'];
    	$syncConfig = Common_LoginProcess::getSystemSync();
    	$ez_user_db = $syncConfig['wms']['dbname'];
    	
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	$date = date('Y-m-d H:i:s');
    	try{
    		//更新EZ ez_user 表
    		$sql = "update $ez_user_db.ez_user set priority_login = '$priority_login',user_password_update_time = '$date'  where user_id = $user_id";
    		$result = $db->query($sql);
    	    	
    		if($result){
    			$db->commit();
    			$return['state'] = 1;
    			$return['message'] = '设置优先登陆系统成功.';
    		}else{
    			$db->rollback();
    			$return['message'] = '设置失败，请稍后尝试.';
    		}
    	}catch(Exception $e){
    		$db->rollback();
    		$return['message'] = $e->getMessage();
    	}
    	
    	return $return;
    }
    
    public function initWmsAction(){
        $process = new Common_ThirdPartWmsAPIProcess();
        //仓库
        $process->syncWarehouse();
        //国家
        $process->syncCountry();
        
        //运输方式
        
        
    }
}