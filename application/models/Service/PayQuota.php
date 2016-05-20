<?php
class Service_PayQuota extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_PayQuota|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_PayQuota();
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
    public static function update($row, $value, $field = "pq_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "pq_id")
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
    public static function getByField($value, $field = 'pq_id', $colums = "*")
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
        
        $validateArr[] = array("name" =>EC::Lang('职位ID'), "value" =>$val["up_id"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('用户ID'), "value" =>$val["user_id"], "regex" => array("positive"));
        $validateArr[] = array("name" =>EC::Lang('付款限额'), "value" =>$val["amount"], "regex" => array("require","positive3"));
        $validateArr[] = array("name" =>EC::Lang('退款限额'), "value" =>$val["amount_refund"], "regex" => array("require","positive3"));
        if($val["amount_refund_not_audit"]){
        	$validateArr[] = array("name" =>EC::Lang('退款免审金额'), "value" =>$val["amount_refund"], "regex" => array("require","positive3"));
        }
        $validateArr[] = array("name" =>EC::Lang('币种'), "value" =>$val["currency_code"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }

    /**
     * 获得用户支付/退款限额信息
     * @param unknown_type $user_id		用户ID
     * @param unknown_type $user_position_id	职位ID
     * @param unknown_type $currency_code	币种
     */
    public static function getUserPayQuota($user_id,$user_position_id,$currency_code){
    	$return = array(
    			'ask'=>1,
    			'is_quota'=>0,
    			'amount'=>0
    	);
    	/*
    	 * 1.检查用户是否单独设置限额
    	*/
    	$con_user = array(
    			'user_id'=>$user_id,
    			'currency_code'=>$currency_code
    	);
    	$result_user_quota = Service_PayQuota::getByCondition($con_user);
    	if(!empty($result_user_quota)){
    		$return['is_quota'] = 1;
    		$return['amount'] = $result_user_quota[0]['amount'];
    		$return['amount_refund'] = $result_user_quota[0]['amount_refund'];
    		$return['amount_refund_not_audit'] = $result_user_quota[0]['amount_refund_not_audit'];
    		$return['is_not_audit'] = $result_user_quota[0]['is_not_audit'];
    		return $return;
    	}
    	 
    	/*
    	 * 2.客户未单独设置限额，取用户职位信息的限额数据
    	*/
    	$con_position = array(
    			'up_id'=>$user_position_id,
    			'currency_code'=>$currency_code
    	);
    	$result_position_quota = Service_PayQuota::getByCondition($con_position);
    	if(!empty($result_position_quota)){
    		$return['is_quota'] = 1;
    		$return['amount'] = $result_position_quota[0]['amount'];
    		$return['amount_refund'] = $result_position_quota[0]['amount_refund'];
    		$return['amount_refund_not_audit'] = $result_position_quota[0]['amount_refund_not_audit'];
    		$return['is_not_audit'] = $result_position_quota[0]['is_not_audit'];
    		return $return;
    	}
    	 
    	/*
    	 * 3.用户和职位都未设置限额信息
    	*/
    	return $return;
    }

    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'pq_id',
              'E1'=>'up_id',
              'E2'=>'user_id',
              'E3'=>'amount',
              'E4'=>'currency_code',
              'E5'=>'create_id',
              'E6'=>'modify_id',
              'E7'=>'create_date',
              'E8'=>'last_update',
        	  'E9'=>'amount_refund',
        	  'E10'=>'amount_refund_not_audit',
        	  'E11'=>'is_not_audit',
        );
        return $row;
    }

}