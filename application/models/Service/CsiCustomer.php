<?php
class Service_CsiCustomer extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_CsiCustomer|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_CsiCustomer();
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
    public static function update($row, $value, $field = "customer_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "customer_id")
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
    public static function getByField($value, $field = 'customer_id', $colums = "*")
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

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'customer_id',
              'E1'=>'customer_code',
              'E2'=>'customer_shortname',
              'E3'=>'customer_allname',
              'E4'=>'customerstatus_code',
              'E5'=>'customerlevel_code',
              'E6'=>'customertype_code',
              'E7'=>'customersource_code',
              'E8'=>'settlementtypes_code',
              'E9'=>'customer_createdate',
              'E10'=>'customer_createrid',
              'E11'=>'server_status',
              'E12'=>'og_id',
              'E13'=>'tms_id',
              'E14'=>'start_time',
              'E15'=>'sameday_time',
        );
        return $row;
    }

    /**
     * 添加客户
     * @param unknown_type $customerInfo
     * @param unknown_type $user
     * @return multitype:number string
     */
    public function addCustomer($customerInfo = array(), $userInfo = array()) {
    	
    	$result = array('state' => 0, 'message' => '', 'api_key' => '' ,'api_token' => '');
    	
    	// 判断客户代码、名称不能为空
    	if(empty($customerInfo)) {
    		$result['message'] = "客户信息不能为空";
    		return $result;
    	}
    	
    	if(empty($userInfo)) {
    		$result['message'] = "登录信息不能为空";
    		return $result;
    	}
    	
    	if(empty($customerInfo['customer_code']) || trim($customerInfo['customer_code']) == "") {
    		$result['message'] = "客户代码不能为空";
    		return $result;
    	}
    	
    	if(empty($customerInfo['customer_shortname']) || trim($customerInfo['customer_shortname']) == "") {
    		$result['message'] = "客户名称不能为空";
    		return $result;
    	}
    	
    	// 去空格转大写
    	$customerInfo['customer_code'] = strtoupper(trim($customerInfo['customer_code']));
    	$customerInfo['customer_shortname'] = trim($customerInfo['customer_shortname']);
    	$customer_code = $customerInfo['customer_code'];
    	
    	// 判断客户代码不能重复
    	$customer_load = Service_CsiCustomer::getByField($customer_code, 'customer_code');
    	if(!empty($customer_load)) {
    		$result['message'] = "客户代码已经存在：" . $customer_code;
    		return $result;
    	}
    	
    	$user = Service_User::getByField($userInfo['user_code'], 'user_code');
    	if(!empty($user)) {
    		$result['message'] = "登录账号已经存在：" . $userInfo['user_code'];
    		return $result;
    	}
    	
    	$db = Common_Common::getAdapter();
    	$db->beginTransaction();
    	
    	try {
	    	// 新增客户
	    	$customer_id = $this->add($customerInfo);
	    	
	    	// 新增客户支持
	    	$support = array(
	    			'customer_id' => $customer_id,
	    			'express_servicerid' => 0,
	    			'express_sallerid' => 0,
	    			'express_dunnerid' => 0,
	    			'pickuper_id' => 0,
	    			);
	    	Service_CsiShippersupporter::add($support);
	    	
	    	// 新增用户
	    	$userInfo['customer_id'] = $customer_id;
	    	$user_id = Service_User::add($userInfo);

	    	$ca_token = md5 ( $user_id . $customer_code . time () );
	    	$ca_key = md5 ( $user_id . $customer_code . time () ) . md5 ( strrev ( ($user_id . $customer_code . time ()) ) );
	    	
	    	// 新增用户API
	    	$customer_api = array (
				'user_id' => $user_id,
				'customer_code' => $customer_code,
				'ca_token' => $ca_token,
				'ca_key' => $ca_key 
			);
	    	Service_CustomerApi::add($customer_api);
	    	
	    	$result['api_key'] = $ca_key;
	    	$result['api_token'] = $ca_token;
	    	$result['customer_code'] = $customer_code;
	    	
	    	$db->commit();
	    	$result['message'] = "Success";
	    	$result['state'] = "1";
    	} catch(Exception $e) {
    		$db->rollback();
    		$result['message'] = $e->getMessage();
    	}
    	
    	return $result;
    }
}