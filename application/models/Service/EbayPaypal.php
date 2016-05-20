<?php
class Service_EbayPaypal extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_EbayPaypal|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_EbayPaypal();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
    	$result = array (
    			"ask" => 0,
    			"message" => "操作失败"
    	);
    	    	
        $model = self::getModelInstance();
        $db = $model->getAdapter();
        try {
        	$db->beginTransaction();
        	$resultEp_id = $model->add($row);
        	$lastRunTime = date('Y-m-d') . ' 00:00:00';	
        	//查询paypal交易数据，取当天时间的0点，为最后运行时间
        	$obj = new Service_EbayPaypal();
			$callPaypalSearchRow = $obj->getRunControlRow("callTransactionSearch" ,
														$row['paypal_account'], 
														$row['company_code'], 
														$lastRunTime);
			//查询paypal交易数据明细,因为需要$callPaypalSearchRow作为前置条件，所以最后运行时间段要提前1.5小时
			$tmpRunTime = date('Y-m-d H:i:s',strtotime("-1 hour",strtotime($lastRunTime)));
			$tmpRunTime = date('Y-m-d H:i:s',strtotime("-30 minutes",strtotime($tmpRunTime)));
			$callPaypalDetailRow = $obj->getRunControlRow("callTransactionDetail" ,
														$row['paypal_account'], 
														$row['company_code'], 
														$tmpRunTime);
			//paypal账户需要处理的退款任务，取当天时间的0点，为最后运行时间
			$callPaypalRefundRow = $obj->getRunControlRow("callRefundTransaction" ,
														$row['paypal_account'], 
														$row['company_code'], 
														$lastRunTime);
			Service_RunControl::add($callPaypalSearchRow);
			Service_RunControl::add($callPaypalDetailRow);
			Service_RunControl::add($callPaypalRefundRow);
        	$db->commit();
        	$result['ask'] = 1;
        	$result["message"] = "Success";
        } catch (Exception $e) {
        	$db->rollBack();
        }
        
        return $result;
    }
    /**
     * 获得自动运行控制表组装参数
     * @param unknown_type $runApp
     * @param unknown_type $paypalAccount
     * @param unknown_type $companyCode
     * @param unknown_type $lastRunTime
     * @param unknown_type $intervalMinute  默认间隔时间为60分钟
     */
    public function getRunControlRow($runApp,$paypalAccount,$companyCode,$lastRunTime,$intervalMinute = '60'){
    	$row = array(
    			'platform'=>'paypal',
    			'user_account'=>$paypalAccount,
    			'run_app'=>$runApp,
    			'company_code'=> $companyCode,
    			'run_interval_minute'=>$intervalMinute,
    			'start_time'=>'00:00:00',
    			'end_time'=>'24:00:00',
    			'last_run_time'=>$lastRunTime,
    			'status'=>1
    	);
    	return $row;
    }

    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "ep_id")
    {
    	$result = array (
    			"ask" => 0,
    			"message" => "操作失败"
    	);
    	
    	$model = self::getModelInstance();
    	$db = $model->getAdapter();
    	try {
    		$db->beginTransaction();
    		
    		//更改了paypal账户，同时更新runControl中的的计划任务
    		$resultEbayPaypal = Service_EbayPaypal::getByField($value,$field);
    		if(!empty($resultEbayPaypal) && $resultEbayPaypal['paypal_account'] != $row['paypal_account']){

    			$con = array(
    					'user_account'=>$resultEbayPaypal['paypal_account'],
    					'company_code'=>Common_Company::getCompanyCode()
    				);
    			$resultRunControl = Service_RunControl::getByCondition($con);

    			if(!empty($resultRunControl)){
    				foreach ($resultRunControl as $key => $v) {
    					$tmpRow = array(
    							'user_account'=>$row['paypal_account']
    							);
    					Service_RunControl::update($tmpRow, $v['run_id']);
    				}
    			}
    		}

    		$model->update($row, $value, $field);
    		$db->commit();
        	$result['ask'] = 1;
        	$result["message"] = "Success";
    	} catch (Exception $e) {
        	$db->rollBack();
        }
        
        return $result;
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ep_id")
    {
    	$result = array (
    			"ask" => 0,
    			"message" => "操作失败"
    	);
    	
    	$model = self::getModelInstance();
    	$db = $model->getAdapter();
    	try {
    		$db->beginTransaction();
    		
    		//删除了paypal账户，同时删除runControl中的的计划任务
    		$resultEbayPaypal = Service_EbayPaypal::getByField($value,$field);
    		if(!empty($resultEbayPaypal) && $resultEbayPaypal['paypal_account'] != $row['paypal_account']){

    			$con = array(
    					'user_account'=>$resultEbayPaypal['paypal_account'],
    					'company_code'=>Common_Company::getCompanyCode()
    				);
    			$resultRunControl = Service_RunControl::getByCondition($con);

    			if(!empty($resultRunControl)){
//     				foreach ($resultRunControl as $key => $v) {
//     					$tmpRow = array(
//     							'user_account'=>$row['paypal_account']
//     							);
    					Service_RunControl::delete($resultRunControl[0]['run_id']);
//     				}
    			}
    		}
    		
	        $model = self::getModelInstance();
	        $model->delete($value, $field);
	        
	        $db->commit();
	        $result['ask'] = 1;
	        $result["message"] = "Success";
        } catch (Exception $e) {
        	$db->rollBack();
        }
        
        return $result;
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'ep_id', $colums = "*")
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
        $validateArr[] = array("name" =>EC::Lang('paypal账户'), "value" =>$val["paypal_account"], "regex" => array("require","email"));
        $validateArr[] = array("name" =>EC::Lang('API账户'), "value" =>$val["name"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('API密码'), "value" =>$val["pass"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('API前面'), "value" =>$val["signature"], "regex" => array("require",));
        $validateArr[] = array("name" =>EC::Lang('ebay账户'), "value" =>$val["ebay_account"], "regex" => array("require",));
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'ep_id',
              'E1'=>'paypal_account',
              'E2'=>'name',
              'E3'=>'pass',
              'E4'=>'signature',
              'E5'=>'ebay_account',
              'E6'=>'fees',
              'E7'=>'user_id',
        	  'E8'=>'company_code'
        );
        return $row;
    }

}